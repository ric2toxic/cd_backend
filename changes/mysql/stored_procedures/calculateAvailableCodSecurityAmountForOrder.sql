DELIMITER $$
DROP PROCEDURE IF EXISTS `calculateAvailableCodSecurityAmountForOrder`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `calculateAvailableCodSecurityAmountForOrder`(IN `in_customer_id` INT, 
	                                                                                      IN `in_order_id` INT,
                                                                                          OUT `out_cod_security_amount` DECIMAL(10,2)
															     						 )

BEGIN   

/*customer COD security balance value  */
DECLARE cod_security_amount DECIMAL(10,2) DEFAULT 0;

/*customer master id  */
DECLARE customer_master_id INT(11) DEFAULT 0;

/* COD security amount used for previous order */
DECLARE used_cod_security_amount DECIMAL(10,2) DEFAULT 0; 


/* Initializing output */
SET out_cod_security_amount = 0;

/* get customer master id -------------------*/
SELECT
      master_id
	  INTO customer_master_id
FROM
  `oc_customer`
WHERE
  customer_id = in_customer_id 
LIMIT 1; 

IF customer_master_id > 0 THEN 

	/*get customer COD security balance ----------------*/
	SELECT
		  cod_security_balance
		  INTO cod_security_amount 
	FROM
	  `oc_cod_security`
	WHERE
	  master_id = customer_master_id  
	LIMIT 1; 

	/* we dont need to do further calculations if there is no COD security amount */
	IF cod_security_amount > 0 THEN 

		/* Compute COD Security only when atleast one of the suborder_id of in_order_id needs to block amount */
		IF EXISTS(SELECT 1 
				  FROM oc_suborder AS osub
				  WHERE osub.order_id = in_order_id AND 
						(osub.order_status_id NOT IN (2, 5, 8, 11, 15) OR 
						 osub.order_status_id = 8 AND NOT EXISTS ( SELECT 1   
																   FROM oc_credit_note AS ocn 
																   WHERE ocn.credit_note_status = 1 
																	 AND ocn.suborder_id = osub.suborder_id 
																 ) 
						) 
				  LIMIT 1) THEN 
		      
			/* This order requires COD security amount blocking */ 
			SELECT 

				SUM(IF(sub.order_status_id = 8, GREATEST(1000, 0.1*sub.total), 0.1*sub.total)) 
				INTO used_cod_security_amount 
			FROM 
			`oc_customer` as c  
			INNER JOIN `oc_order` AS o ON c.customer_id = o.customer_id 
			INNER JOIN `oc_suborder` as sub ON sub.order_id = o.order_id
			WHERE
				c.master_id = customer_master_id
				AND
				sub.order_status_id > 0
				AND (
					 sub.order_status_id NOT IN (2, 5, 8, 11, 15) 
					 OR 
					 ( sub.order_status_id = 8
					   AND
					   NOT EXISTS ( SELECT 1  
									FROM `oc_credit_note` AS ocn 
									WHERE credit_note_status = 1 
									  AND ocn.suborder_id = sub.suborder_id 
								  )
					 )
					)	
				AND o.order_id < in_order_id 
				AND TRIM(LOWER(o.payment_code)) = 'cod' 

			GROUP BY c.master_id 
			LIMIT 1; 

			IF used_cod_security_amount IS NULL THEN 
			  SET used_cod_security_amount = 0.00;
			END IF;

			/*Reset COD security amount by used COD security amount*/
			SET out_cod_security_amount = ( cod_security_amount - used_cod_security_amount );
		END IF;
	END IF;
END IF;

IF out_cod_security_amount < 0 THEN 
    SET out_cod_security_amount = 0;
END IF;


END$$

DELIMITER ;
