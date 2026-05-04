DELIMITER //
DROP TRIGGER IF EXISTS oc_product_filter_delete //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_filter_delete 
    AFTER DELETE ON oc_product_filter 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    DECLARE filter_old_name VARCHAR(20);
    
    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", OLD.product_id, "\"");
    
    /* filter id */
    SET new_data := CONCAT(new_data, ",", "\"filter_id\"", ":{");
    SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.filter_id, "\"");
    SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"",  "\"");
    SET new_data := CONCAT(new_data, "}");

    /* Filter Name */
    SET filter_old_name = (SELECT name FROM oc_filter_description WHERE filter_id = OLD.filter_id AND language_id = 1);
    SET new_data := CONCAT(new_data, ",", "\"filters\"", ":{");
    SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", filter_old_name, "\"");
    SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", "\"");
    SET new_data := CONCAT(new_data, "}");

    SET new_data := CONCAT(new_data, "}");

    INSERT INTO oc_product_change_log 
    SET product_id = OLD.product_id, 
        table_name = 'oc_product_filter', 
        change_details = new_data, 
        date_added = NOW(), 
        solr_synced = 0;
END//
DELIMITER ;