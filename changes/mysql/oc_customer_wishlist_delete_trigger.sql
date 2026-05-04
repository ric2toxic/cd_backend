DELIMITER //
DROP TRIGGER IF EXISTS oc_customer_wishlist_delete //

CREATE TRIGGER oc_customer_wishlist_delete 
    BEFORE DELETE ON oc_customer_wishlist 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    
    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", OLD.product_id, "\"");
    
        /* store id */
        SET new_data := CONCAT(new_data, ",", "\"wishlist\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"",OLD.customer_id , "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", "\"");
        SET new_data := CONCAT(new_data, "}");

    SET new_data := CONCAT(new_data, "}");

    INSERT INTO oc_daily_once_change_log 
    SET product_id = OLD.product_id, 
        table_name = 'oc_customer_wishlist', 
        change_details = new_data, 
        date_added = NOW(), 
        solr_synced = 0;

END//
DELIMITER ;
