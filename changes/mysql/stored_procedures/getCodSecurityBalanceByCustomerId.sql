DELIMITER $$
DROP PROCEDURE IF EXISTS `getCodSecurityBalanceByCustomerId`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCodSecurityBalanceByCustomerId`(IN `in_customer_id` INT,
				                                                                 OUT `out_cod_security_balance` DECIMAL(10,2)
				                                                                )

BEGIN   


SELECT
      cod_security_balance
	  INTO out_cod_security_balance
FROM
  `oc_customer` AS cust
INNER JOIN `oc_cod_security` AS ocs ON ocs.master_id = cust.master_id
WHERE
  customer_id = in_customer_id 
LIMIT 1; 

IF out_cod_security_balance IS NULL THEN 
  SET out_cod_security_balance = 0.00;
END IF;

END$$

DELIMITER ;
