DELIMITER //

DROP TRIGGER IF EXISTS oc_product_special_update //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_special_update 
    AFTER UPDATE ON oc_product_special 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    DECLARE changed_val BOOL;
    
    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", NEW.product_id, "\"");
    SET changed_val = FALSE;
    
    /* price */
    IF (OLD.price!=NEW.price) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"price\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.price, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.price, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

     /* Date Start */
    IF (OLD.date_start!=NEW.date_start) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"date_start\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.date_start, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.date_start, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Date End */
    IF (OLD.date_end!=NEW.date_end) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"date_end\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.date_end, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.date_end, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    SET new_data := CONCAT(new_data, "}");

    IF (changed_val = TRUE) then
		INSERT INTO oc_product_change_log
		SET product_id = NEW.product_id,
			table_name = 'oc_product_special',
			change_details = new_data,
			date_added = NOW(),
			solr_synced = 0;
	END IF;
END//
DELIMITER ;