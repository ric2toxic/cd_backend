DELIMITER //
DROP TRIGGER IF EXISTS oc_product_to_store_insert //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_to_store_insert 
    AFTER INSERT ON oc_product_to_store 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    
    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", NEW.product_id, "\"");
    
    /* store id */
    SET new_data := CONCAT(new_data, ",", "\"store_id\"", ":{");
    SET new_data := CONCAT(new_data, "\"old\"", ":", "\"",  "\"");
    SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.store_id, "\"");
    SET new_data := CONCAT(new_data, "}");

    SET new_data := CONCAT(new_data, "}");

    INSERT INTO oc_product_change_log 
    SET product_id = NEW.product_id, 
        table_name = 'oc_product_to_store', 
        change_details = new_data, 
        date_added = NOW(), 
        solr_synced = 0;

END//
DELIMITER ;