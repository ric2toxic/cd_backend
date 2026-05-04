DELIMITER $$
DROP PROCEDURE IF EXISTS `calculateSP`$$

CREATE DEFINER=`root`@`localhost` 
	PROCEDURE `calculateSP`(
						IN  tp        DECIMAL(15,4),
						IN commission  DECIMAL(15,4),
						IN  h_code    VARCHAR(8),
						INOUT selling_price DECIMAL(15,4)
						 
						)
BEGIN
    DECLARE tax_class_id INT(11);
    DECLARE tax_rate_id INT(11);
    DECLARE seller_tax DECIMAL(15,4);

    	
   SET tax_class_id = (SELECT oh.tax_class_id 
						 FROM oc_hsn oh
						 WHERE oh.hsn_code = h_code 
					  );
                      
					  
	SET tax_rate_id = (SELECT DISTINCT 
						tr.tax_rate_id 
						FROM oc_tax_rule tr 
						WHERE 
						tr.tax_class_id = tax_class_id 
						ORDER BY tr.priority ASC LIMIT 1
                       );
                       
    IF(tax_rate_id IS NOT NULL) THEN 
    
		SET seller_tax = (SELECT tr.rate
						 FROM oc_tax_rate AS tr
						 WHERE tp >= (tr.start_range*(1+(tr.rate/100)))
						 AND tp <= (tr.end_range*(1+(tr.rate/100)))
						 AND rate_logic = 'price_range' 
						 AND type = 'P' 
						 AND tr.tax_rate_id = tax_rate_id
						 ORDER BY rate ASC LIMIT 1
						);
						
		IF (seller_tax IS NULL) THEN
			SET seller_tax = (SELECT tr.rate from oc_tax_rate AS tr
						 WHERE
						 rate_logic = 'global' 
						 AND type = 'P' 
						 AND tax_rate_id = tax_rate_id
						 ORDER BY rate ASC LIMIT 1
						);
		END IF;
		 			
	END IF;
	
	IF (seller_tax IS NOT NULL) THEN
		SET selling_price = CEIL ( tp * (1 + (commission/100)) / (1 + (seller_tax/100)) );
	END IF;
	
END;
