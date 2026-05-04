DELIMITER //
DROP TRIGGER IF EXISTS oc_product_to_category_delete //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_to_category_delete 
    AFTER DELETE ON oc_product_to_category 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    DECLARE category_old_name VARCHAR(20);

    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", OLD.product_id, "\"");
    
    /* store id */
    SET new_data := CONCAT(new_data, ",", "\"category_id\"", ":{");
    SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.category_id, "\"");
    SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"",  "\"");
    SET new_data := CONCAT(new_data, "}");

    /* Category Name */
    SET category_old_name = (SELECT name FROM oc_category_description WHERE category_id = OLD.category_id AND language_id = 1);
    SET new_data := CONCAT(new_data, ",", "\"categories\"", ":{");
    SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", category_old_name, "\"");
    SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", "\"");
    SET new_data := CONCAT(new_data, "}");

    SET new_data := CONCAT(new_data, "}");

    INSERT INTO oc_product_change_log 
    SET product_id = OLD.product_id, 
        table_name = 'oc_product_to_category', 
        change_details = new_data, 
        date_added = NOW(), 
        solr_synced = 0;
END//
DELIMITER ;