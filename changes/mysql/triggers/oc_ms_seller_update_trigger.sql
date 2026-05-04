DELIMITER //

DROP TRIGGER IF EXISTS oc_ms_seller_update //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_ms_seller_update 
    AFTER UPDATE ON oc_ms_seller 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    DECLARE changed_val BOOL;
    SET @rowcount = 0;	
    
    SET new_data = CONCAT("{", "\"seller_id\"", ":", "\"", NEW.seller_id, "\"");
    SET changed_val = FALSE;
    
    /* Nickname */
    IF (OLD.nickname!=NEW.nickname) THEN
        SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"nickname\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.nickname, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.nickname, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* City */
    IF (OLD.city!=NEW.city) THEN
        SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"city\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.city, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.city, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Vacation Mode */
    IF (OLD.vacation_mode!=NEW.vacation_mode) THEN
        SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"vacation_mode\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.vacation_mode, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.vacation_mode, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Seller Status */
    IF (OLD.seller_status!=NEW.seller_status) THEN
        SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"seller_status\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.seller_status, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.seller_status, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

	/* App only status */
	IF (OLD.app_only !=NEW.app_only) THEN
        SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"app_only\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.app_only, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.app_only, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

	/* Non servicable areas */
	IF (OLD.non_serviceable_areas !=NEW.non_serviceable_areas) THEN
        SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"non_serviceable_areas\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.non_serviceable_areas, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.non_serviceable_areas, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    SET new_data := CONCAT(new_data, "}");

    IF (changed_val = TRUE) then
		INSERT INTO oc_product_change_log 
		SET product_id = NEW.seller_id, 
			table_name = 'oc_ms_seller', 
			change_details = new_data, 
			date_added = NOW(), 
			solr_synced = 0;
	END IF;
	IF (NEW.gst_provisional_id!=OLD.gst_provisional_id) 
	THEN
		(SELECT count(oc.customer_id) INTO @rowcount FROM oc_customer oc WHERE oc.gst_number=NEW.gst_provisional_id AND oc.customer_id !=NEW.seller_id GROUP BY oc.customer_id);
		
		IF @rowcount < 1
		THEN		
			UPDATE
			  oc_customer oc
			SET
			  oc.gst_number = NEW.gst_provisional_id
			WHERE
			  oc.customer_id = NEW.seller_id ;	
		END IF;
	END IF;
END//
DELIMITER ;
