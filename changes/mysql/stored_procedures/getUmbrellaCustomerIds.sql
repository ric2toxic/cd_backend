DELIMITER $$
DROP PROCEDURE IF EXISTS `getUmbrellaCustomerIds`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUmbrellaCustomerIds`(IN `in_customer_id` INT,
                                                                    OUT `out_customer_ids` VARCHAR(255)
                                                                    )

/*IN - customer id from oc_customer table */
/*OUT - comma separeted list of customer ids for customer where master id are same*/

BEGIN   
 
SELECT
      GROUP_CONCAT(DISTINCT cust2.customer_id) 
INTO out_customer_ids
FROM
  `oc_customer` AS cust1
INNER JOIN
  `oc_customer` as cust2 ON cust2.master_id = cust1.master_id
WHERE
  cust1.customer_id = in_customer_id 
GROUP BY cust1.customer_id 
LIMIT 1; 

END$$

DELIMITER ;
