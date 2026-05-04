DELIMITER //
DROP TRIGGER IF EXISTS oc_product_filter_insert //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_filter_insert 
    AFTER INSERT ON oc_product_filter 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    DECLARE filter_new_name VARCHAR(20);
    
    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", NEW.product_id, "\"");
    
    /* filter id */
    SET new_data := CONCAT(new_data, ",", "\"filter_id\"", ":{");
    SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
    SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.filter_id, "\"");
    SET new_data := CONCAT(new_data, "}");

    /* Filter Name */
    SET filter_new_name = (SELECT name FROM oc_filter_description WHERE filter_id = NEW.filter_id AND language_id = 1);
    SET new_data := CONCAT(new_data, ",", "\"filters\"", ":{");
    SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "\"");
    SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", filter_new_name, "\"");
    SET new_data := CONCAT(new_data, "}");

    SET new_data := CONCAT(new_data, "}");

    INSERT INTO oc_product_change_log 
    SET product_id = NEW.product_id, 
        table_name = 'oc_product_filter', 
        change_details = new_data, 
        date_added = NOW(), 
        solr_synced = 0;

END//
DELIMITER ;