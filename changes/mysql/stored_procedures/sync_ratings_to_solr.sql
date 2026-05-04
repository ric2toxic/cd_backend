DELIMITER $$
DROP PROCEDURE IF EXISTS `syncRatingsToSolr`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `syncRatingsToSolr`(IN `pid` INT)
BEGIN
	DECLARE WSB_STORES_ID TEXT;
    DECLARE rat double(11,2);
    DECLARE sid INT;
    DECLARE s_status INT;
    DECLARE parrent_cat_id VARCHAR(255);
    DECLARE cid VARCHAR(255);
    DECLARE all_cat_ids VARCHAR(255);
    
    SET WSB_STORES_ID = "0,2,9";
	
    SET rat = (SELECT AVG(rating) AS rating 
				 FROM oc_review 
				 WHERE product_id = pid 
				 AND status = '1' 
				 AND store_id IN (WSB_STORES_ID) 
				 GROUP BY product_id);
    IF(rat IS NULL) THEN 
		SET sid = (SELECT seller_id 
					 FROM oc_ms_product 
					 WHERE product_id = pid);
                     
		IF(sid IS NOT NULL) THEN
			SET s_status = (SELECT seller_status 
									FROM oc_ms_seller 
									WHERE seller_id = sid AND seller_status = 1);
            IF(s_status IS NOT NULL) THEN
            
				SET parrent_cat_id = (SELECT GROUP_CONCAT(category_id) AS category_id 
										FROM oc_product_to_category 
										WHERE product_id = pid);
				
				SET cid = (SELECT GROUP_CONCAT(category_id) AS category_id 
							 FROM oc_category 
							 WHERE parent_id IN (parrent_cat_id));
				
				IF (cid IS NULL) THEN
					SET all_cat_ids = parrent_cat_id;
				ELSE 
					SET all_cat_ids = CONCAT(parrent_cat_id,',',cid);
				END IF;
				
				SET rat = (SELECT MIN(rating) AS rating FROM oc_review_rules 
							 WHERE seller_id = sid 
							 AND rule_type = 'category' 
							 AND category_id IN (all_cat_ids));
				IF(rat IS NULL) THEN
					SET rat = (SELECT rating FROM oc_review_rules 
								 WHERE seller_id = sid 
								 AND rule_type = 'global');
				END IF;
			END IF;
        END IF;
    END IF;
    SELECT rat;
END$$
DELIMITER ;
