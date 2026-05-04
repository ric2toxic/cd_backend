DELIMITER //

DROP TRIGGER IF EXISTS oc_ms_product_update //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_ms_product_update 
    AFTER UPDATE ON oc_ms_product 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
	DECLARE sellers_nickname VARCHAR(64);
    DECLARE sellers_city VARCHAR(64);
    DECLARE sellers_status INT;
    DECLARE sellers_vacation_mode INT;
    DECLARE changed_val BOOL;
	DECLARE sellers_app_only BOOL;
	DECLARE sellers_non_serviceable_areas TEXT;
    
    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", NEW.product_id, "\"");
    SET changed_val = FALSE;
    
    /* filter id */ 
    IF (OLD.seller_id!=NEW.seller_id) THEN	
    	SELECT vacation_mode, seller_status, nickname, city, app_only, non_serviceable_areas
    	INTO sellers_vacation_mode, sellers_status, sellers_nickname, sellers_city, sellers_app_only, sellers_non_serviceable_areas 
		FROM oc_ms_seller 
		WHERE seller_id = NEW.seller_id;
        
        SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"seller_id\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.seller_id, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.seller_id, "\"");
        SET new_data := CONCAT(new_data, "}");

		/* nickname */ 
		SET new_data := CONCAT(new_data, ",", "\"nickname\"", ":{");
		IF( sellers_nickname IS NULL) THEN
            SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
			SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", "\"");
        ELSE
			SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
			SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", sellers_nickname, "\"");
        END IF;
		SET new_data := CONCAT(new_data, "}");
		
		/* city */ 
		SET new_data := CONCAT(new_data, ",", "\"city\"", ":{");
        IF( sellers_city IS NULL) THEN
            SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
			SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", "\"");
        ELSE
			SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
			SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", sellers_city, "\"");
        END IF;
        SET new_data := CONCAT(new_data, "}");
		
        /* sellers_status */ 
		SET new_data := CONCAT(new_data, ",", "\"seller_status\"", ":{");
		SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
		SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", sellers_status, "\"");
		SET new_data := CONCAT(new_data, "}");
			
		/* vacation mode */ 
		SET new_data := CONCAT(new_data, ",", "\"vacation_mode\"", ":{");
		SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
		SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", sellers_vacation_mode, "\"");
		SET new_data := CONCAT(new_data, "}");

		/* App only status */
		SET new_data := CONCAT(new_data, ",", "\"app_only\"", ":{");
		SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
		SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", sellers_app_only, "\"");
		SET new_data := CONCAT(new_data, "}");

		/* Non servicable areas */
		SET new_data := CONCAT(new_data, ",", "\"non_serviceable_areas\"", ":{");
        IF( sellers_non_serviceable_areas IS NULL) THEN
            SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
			SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", "\"");
        ELSE
			SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
			SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", sellers_non_serviceable_areas, "\"");
        END IF;

    END IF;

    SET new_data := CONCAT(new_data, "}");

    IF (changed_val = TRUE) then
		INSERT INTO oc_product_change_log 
		SET product_id = NEW.product_id, 
			table_name = 'oc_ms_product', 
			change_details = new_data, 
			date_added = NOW(), 
			solr_synced = 0;
	END IF;
END//
DELIMITER ;
