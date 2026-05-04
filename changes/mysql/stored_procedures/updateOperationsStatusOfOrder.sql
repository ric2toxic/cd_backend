DELIMITER $$
DROP PROCEDURE IF EXISTS `updateOperationsStatusOfOrder`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateOperationsStatusOfOrder`(IN `in_order_id` INT)
BEGIN   
  
  /* oc_order table fields: payment_code, unique suborder statues, order total value, current operations status, currency_code */
  DECLARE order_payment_code VARCHAR(64) DEFAULT 'credit';
  DECLARE order_statuses VARCHAR(128) DEFAULT '';
  DECLARE order_total DECIMAL(11,2) DEFAULT 0;
  DECLARE current_operations_status VARCHAR(64) DEFAULT 'none';
  DECLARE order_currency_code VARCHAR(4) DEFAULT 'INR';

  /* confirmed order payments (all without cash entry/cashback/coupon; cashback/coupon; and cash/cheque deposited entry) */
  DECLARE realized_amount DECIMAL(11,2) DEFAULT 0;
  DECLARE cash_discount_amount DECIMAL(11,2) DEFAULT 0;
  DECLARE order_payment_cash_amount DECIMAL(11,2) DEFAULT 0;
  DECLARE order_payment_cheque_deposited_amount DECIMAL(11,2) DEFAULT 0;
  /* tentative_advance table, not reflecting in order_payment_table */
  DECLARE tentative_advance_cash_amount DECIMAL(11,2) DEFAULT 0;
  DECLARE tentative_advance_cheque_deposited_amount DECIMAL(11,2) DEFAULT 0;
  
  DECLARE check_amount DECIMAL(11,2) DEFAULT 0; /* Amount to be used for checking against order_total. It will vary as per order conditions */
  
  /*Customer id -  to get cheque failed history from order payment table for customer id */
  DECLARE order_customer_id INT DEFAULT 0;

  /* Customer cheque failed status*/
  DECLARE is_cheque_failed_history INT DEFAULT 0;

  /* Result: Operations status */
  DECLARE new_operations_status VARCHAR(64) DEFAULT 'none';
  
  /*customer COD security balance value  */
  DECLARE cod_security_amount DECIMAL(10,2) DEFAULT 0.00;


 /* Get order payment method, suborder statuses, and order total for the given order*/
  SELECT IFNULL(LOWER(TRIM(o.payment_code)), 'credit'), 
         IFNULL(GROUP_CONCAT(DISTINCT osub.order_status_id), ''), 
         IFNULL(o.total, 0), 
         IFNULL(o.operations_status, 'none'), 
         IFNULL(UPPER(TRIM(o.currency_code)), 'INR'),
         o.customer_id 
    INTO order_payment_code, order_statuses, order_total, current_operations_status, order_currency_code,order_customer_id
  FROM oc_order o
  INNER JOIN oc_suborder osub
   ON osub.order_id = o.order_id
  WHERE o.order_id = in_order_id 
    AND osub.order_status_id > 0 
  GROUP BY o.order_id 
  LIMIT 1;

/* is_cheque_failed_history : call - checkForChequeBounceHistory() stored procedure to get customer cheque failed history count */
CALL checkForChequeBounceHistory(order_customer_id, is_cheque_failed_history);   


/* get Customer remaining COD Security amount */
CALL calculateAvailableCodSecurityAmountForOrder(order_customer_id, in_order_id, cod_security_amount);   

  /* Based on various conditions, we determine the next set of actions */
  CASE 

    /* Credit order OR non-INR order OR (Not in pending order AND Non in processed order) - 'none' */
    /* Order statuses: 1 (Pending), 9 (Processed), 16 (Tentative Processed) */
    WHEN order_payment_code = 'credit' OR 
         order_currency_code != 'INR' OR 
         (FIND_IN_SET('1', order_statuses) = 0 AND FIND_IN_SET('9', order_statuses) = 0 AND FIND_IN_SET('16', order_statuses) = 0) THEN 
        SET new_operations_status = 'none';
    
    /* In rest of the cases, we determine the new_operations_status */
    ELSE 
        
        /* Get successfully realized amount and cash discount (cashback/coupon) from oc_order_payment table */
        SELECT 
            IFNULL(SUM(IF(bank_transfer_mode NOT IN ('cheque_deposited','cheque_failed') AND payment_gateway NOT IN ('cash','cashback','coupon'),amount,0)),0), 
            IFNULL(SUM(IF(payment_gateway IN ('cashback','coupon'),amount,0)),0), 
            IFNULL(SUM(IF(payment_gateway = 'cash',amount,0)),0), 
            IFNULL(SUM(IF(payment_gateway = 'bank_transfer' AND bank_transfer_mode = 'cheque_deposited',amount,0)),0)   
        INTO realized_amount, cash_discount_amount, order_payment_cash_amount, order_payment_cheque_deposited_amount
        FROM oc_order_payment 
        WHERE order_id = in_order_id 
          AND successfull = 1 
        GROUP BY order_id 
        LIMIT 1;
        
        /* If the order is COD, then we may have COD security balance amount of this customer 
           with us. Ideally, that is also a part of realized amount */
        IF order_payment_code = 'cod' THEN 
          SET realized_amount = realized_amount + cod_security_amount;
        END IF;
        
        SET check_amount = realized_amount + cash_discount_amount;
        
        /* ORDER IS IN PENDING STATE */
        IF FIND_IN_SET('1', order_statuses) > 0 THEN /* atleast one suborder is in pending state */
            
            /* tentative amounts will also be considered */
            SELECT IFNULL(SUM(IF(confirm = 0 AND 
                                 (transaction_status IN ('will_deposit', 'deposited') AND payment_mode = 'cash') AND 
                                 (order_payment_id IS NULL OR order_payment_id = 0), amount, 0)), 
                          0), 
                   IFNULL(SUM(IF(confirm = 0 AND 
                                 (transaction_status = 'deposited' AND payment_mode = 'cheque') AND 
                                 (order_payment_id IS NULL OR order_payment_id = 0), amount, 0)), 
                          0) 
            INTO tentative_advance_cash_amount, tentative_advance_cheque_deposited_amount
            FROM oc_tentative_advance
            WHERE order_id = in_order_id 
            GROUP BY order_id 
            LIMIT 1;

            /* update check_amount if order_status is pending. Tentative order_payment and advance entries need to be considered also */
            SET check_amount = check_amount + order_payment_cash_amount + tentative_advance_cash_amount;

            /* Consider cheque deposited amounts also, if there is NO cheque_failed history */
            IF is_cheque_failed_history = 0 THEN 
                SET check_amount = check_amount + order_payment_cheque_deposited_amount + tentative_advance_cheque_deposited_amount;
            END IF;

            /* in order to consider the order good_to_process, there must be atleast some payment done by customer other than cashback/coupon */
            IF realized_amount = 0 AND order_payment_cash_amount = 0 AND tentative_advance_cash_amount = 0 THEN 
                SET new_operations_status = 'none';
                
            ELSEIF (order_payment_code = 'cod' AND check_amount >= 0.1*order_total) /* COD order; check_amount must be >= 10% of order_total */
                    OR
                   (order_payment_code != 'cod' AND check_amount - order_total >= -10) THEN /* Prepaid order; check_amount must be within 10 Rs of order_total */
                SET new_operations_status = 'good_to_process';
            
            ELSE 
                SET new_operations_status = 'none';
            
            END IF;
            
        /* ORDER IS IN PROCESSED STATE */
        ELSEIF FIND_IN_SET('9', order_statuses) > 0 OR FIND_IN_SET('16', order_statuses) > 0 THEN /* atleast one suborder is in processed/tentative processed state */
        
            /* in order to consider the order good_to_dispatch, there must be atleast some payment done by customer other than cashback/coupon */
            IF realized_amount = 0 THEN 
                SET new_operations_status = 'dont_dispatch';
        
            ELSEIF (order_payment_code = 'cod' AND check_amount >= 0.1*order_total) /* COD order; check_amount must be >= 10% of order_total */
                OR
               (order_payment_code != 'cod' AND check_amount - order_total >= -10) THEN /* Prepaid order; check_amount must be within 10 Rs of order_total */
              SET new_operations_status = 'good_to_dispatch';
            
            ELSE 
                SET new_operations_status = 'dont_dispatch';
            
            END IF;
        
        ELSE /* Undeterminable state */
        
            SET new_operations_status = 'none';
            
        END IF;
        
  END CASE;

  /* UPDATE operations_status, if new and current are different */
  IF new_operations_status != current_operations_status THEN 
    UPDATE oc_order SET operations_status = new_operations_status WHERE order_id = in_order_id;
  END IF;

END$$
DELIMITER ;
