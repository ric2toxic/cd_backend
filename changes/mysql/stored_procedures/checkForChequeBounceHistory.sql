DELIMITER $$
DROP PROCEDURE IF EXISTS `checkForChequeBounceHistory`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkForChequeBounceHistory`(IN `in_customer_id` INT,
                                                                          OUT `out_cheque_failed_history` INT
                                                                          )

/*IN - customer id from oc_customer table */
/*OUT - count value for customers payment transactions with status - `cheque_failed`  */

BEGIN   

/* oc_customer table field: customer id */
  DECLARE in_customer_ids VARCHAR(255);

/* Call procedure to get all customer ids*/
CALL getUmbrellaCustomerIds(in_customer_id, in_customer_ids);

/* get list of comma separated customer ids, */
/* to use in next query to get count of customer bank transfer records with status - `cheque_failed` */


/*Query to get if customer has any payment transaction record with status - `cheque_failed` */
    SELECT
          COUNT(op.payment_id)
      INTO out_cheque_failed_history
    FROM
      `oc_order` as oor
      INNER JOIN `oc_order_payment` op ON op.order_id = oor.order_id
    WHERE
     oor.customer_id IN (in_customer_ids)
     AND 
     op.bank_transfer_mode = 'cheque_failed'; 
 

END$$
DELIMITER ;

