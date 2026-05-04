DELIMITER //

DROP TRIGGER IF EXISTS oc_product_update //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_update 
    AFTER UPDATE ON oc_product 
    FOR EACH ROW 
BEGIN
    DECLARE new_data TEXT;
    DECLARE stock_status_old_name VARCHAR(20);
	DECLARE stock_status_new_name VARCHAR(20);
    DECLARE changed_val BOOL;
    DECLARE changed_price decimal(15,4);
    DECLARE update_selling_price decimal(15,4);
    DECLARE changed_seller_tax decimal(15,4);
    DECLARE changed_commission decimal(15,4);
    DECLARE new_price decimal(15,4);
    DECLARE new_commission decimal(15,4);
    DECLARE old_hsn_code VARCHAR(8);
    DECLARE new_hsn_code VARCHAR(8);
    DECLARE tax_class_id INT(11);
    DECLARE franchise_id INT(11);
    DECLARE rating TINYINT(1);
    
    SET new_data = CONCAT("{", "\"product_id\"", ":", "\"", NEW.product_id, "\"");
    SET changed_val = FALSE;
    SET old_hsn_code = "";
    SET new_hsn_code = "";
    
    /* Sku */
    IF (OLD.sku!=NEW.sku) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"sku\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.sku, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.sku, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Model */
    IF (OLD.model!=NEW.model) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"model\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.model, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.model, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Price */
    IF (OLD.price!=NEW.price) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"price\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.price, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.price, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    
    /* hsn_code */
    /* hsn_code can be NULL; Handling that case */
    IF (OLD.hsn_code IS NOT NULL) THEN
        SET old_hsn_code = OLD.hsn_code;
    END IF;
    IF (NEW.hsn_code IS NOT NULL) THEN
        SET new_hsn_code = NEW.hsn_code;
    END IF;
    IF (old_hsn_code!=new_hsn_code) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"hsn_code\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", old_hsn_code, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", new_hsn_code, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    
    /* Commission */
    IF (OLD.commission!=NEW.commission) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"commission\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.commission, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.commission, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    
    /* Tax Class Id */
    IF (OLD.tax_class_id!=NEW.tax_class_id) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"tax_class_id\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.tax_class_id, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.tax_class_id, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    
    /* Selling Price */
    IF (OLD.price!=NEW.price || old_hsn_code!=new_hsn_code || OLD.commission!=NEW.commission) THEN		
    
        SELECT price, commission, hsn_code 
			INTO new_price, new_commission, new_hsn_code 
			FROM oc_product 
			WHERE product_id = NEW.product_id;
		                
        set @selling_price = 0;
        CALL calculateSP(new_price, new_commission, new_hsn_code, @selling_price);
		SET changed_val = TRUE;
		
        SET new_data := CONCAT(new_data, ",", "\"selling_price\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"",  "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", @selling_price, "\"");
        SET new_data := CONCAT(new_data, "}");
                
    END IF;
    
    /* is_single */
    IF (OLD.is_single!=NEW.is_single) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"is_single\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.is_single, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.is_single, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Status */
    IF (OLD.status!=NEW.status) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"status\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.status, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.status, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Quantity */
    IF (OLD.quantity!=NEW.quantity) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"quantity\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.quantity, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.quantity, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Sort Order */
    IF (OLD.sort_order!=NEW.sort_order) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"sort_order\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.sort_order, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.sort_order, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Stock Status Id AND Stock Status Name */
    IF (OLD.stock_status_id!=NEW.stock_status_id) THEN
		/* Stock Status id */
        SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"stock_status_id\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.stock_status_id, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.stock_status_id, "\"");
        SET new_data := CONCAT(new_data, "}");

        /* Stock Status Name */
        SET stock_status_old_name = (SELECT name FROM oc_stock_status WHERE stock_status_id = OLD.stock_status_id AND language_id = 1);
        SET stock_status_new_name = (SELECT name FROM oc_stock_status WHERE stock_status_id = NEW.stock_status_id AND language_id = 1);
        SET new_data := CONCAT(new_data, ",", "\"stock_status\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", stock_status_old_name, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", stock_status_new_name, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Date Available */
    IF (OLD.date_available!=NEW.date_available) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"date_available\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.date_available, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.date_available, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Date Added */
    IF (OLD.date_added!=NEW.date_added) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"date_added\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.date_added, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.date_added, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Minimum */
    IF (OLD.minimum!=NEW.minimum) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"minimum\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.minimum, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.minimum, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Piece In Set */
    IF (OLD.piece_in_set!=NEW.piece_in_set) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"piece_in_set\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.piece_in_set, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.piece_in_set, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* Store sales */
    IF (OLD.store_sales!=NEW.store_sales) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"store_sales\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.store_sales, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.store_sales, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* exclusive */
    IF (OLD.exclusive!=NEW.exclusive) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"exclusive\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.exclusive, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.exclusive, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    
    /* is_archived field */
    IF (OLD.is_archived!=NEW.is_archived) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"is_archived\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.is_archived, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.is_archived, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;


    /* only_for_search */
    IF (OLD.only_for_search!=NEW.only_for_search) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"only_for_search\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.only_for_search, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.only_for_search, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    
    /* franchise_id field */
    IF (OLD.franchise_id!=NEW.franchise_id) THEN
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"franchise_id\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.franchise_id, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.franchise_id, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    IF (OLD.franchise_id IS NULL AND NEW.franchise_id IS NOT NULL) THEN 
		SET changed_val = TRUE;
		SET new_data := CONCAT(new_data, ",", "\"franchise_id\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "", "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.franchise_id, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* rating field */
    IF (OLD.rating!=NEW.rating) THEN
        SET changed_val = TRUE;
        SET new_data := CONCAT(new_data, ",", "\"rating\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.rating, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.rating, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    IF (OLD.rating IS NULL AND NEW.rating IS NOT NULL) THEN 
        SET changed_val = TRUE;
        SET new_data := CONCAT(new_data, ",", "\"rating\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", "", "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.rating, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;

    /* is_associate field */
    IF (OLD.is_associate!=NEW.is_associate) THEN
        SET changed_val = TRUE;
        SET new_data := CONCAT(new_data, ",", "\"is_associate\"", ":{");
        SET new_data := CONCAT(new_data, "\"old\"", ":", "\"", OLD.is_associate, "\"");
        SET new_data := CONCAT(new_data, ",\"new\"", ":", "\"", NEW.is_associate, "\"");
        SET new_data := CONCAT(new_data, "}");
    END IF;
    

    SET new_data := CONCAT(new_data, "}");

    IF (changed_val = TRUE) then
		INSERT INTO oc_product_change_log
		SET product_id = NEW.product_id,
			table_name = 'oc_product',
			change_details = new_data,
			date_added = NOW(),
			solr_synced = 0;
	END IF;
END//
DELIMITER ;
