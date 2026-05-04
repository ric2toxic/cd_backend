DELIMITER //

DROP TRIGGER IF EXISTS oc_product_description_update //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_description_update 
    AFTER UPDATE ON oc_product_description 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    DECLARE changed_val BOOL;
    
    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", NEW.product_id, "\"");
    SET changed_val = FALSE;
    
    /* Name */
    IF (OLD.name!=NEW.name) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"name\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.name, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.name, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

     /* Set Description */
    IF (OLD.set_description!=NEW.set_description) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"set_description\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.set_description, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.set_description, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Description */
    IF (OLD.description!=NEW.description) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"description\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.description, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.description, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    
    /* Tag */
    IF (OLD.tag!=NEW.tag) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"tag\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.tag, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.tag, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    SET new_data := CONCAT(new_data, "}");

    IF (changed_val = TRUE) then
		INSERT INTO oc_product_change_log
		SET product_id = NEW.product_id,
			table_name = 'oc_product_description',
			change_details = new_data,
			date_added = NOW(),
			solr_synced = 0;
	END IF;
END//
DELIMITER ;