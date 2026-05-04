DELIMITER //
DROP TRIGGER IF EXISTS oc_hsn_update_trigger //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_hsn_update_trigger 
    AFTER UPDATE ON oc_hsn 
    FOR EACH ROW 
BEGIN

    DECLARE hsn_code_val varchar(16);

    /* tax_class_id */
    IF (OLD.tax_class_id!=NEW.tax_class_id) THEN
		UPDATE oc_product SET price = price+0.001 WHERE hsn_code = OLD.hsn_code;
        UPDATE oc_product SET price = price-0.001 WHERE hsn_code = OLD.hsn_code;
		UPDATE oc_product SET tax_class_id = NEW.tax_class_id WHERE hsn_code = OLD.hsn_code;
    END IF;

END //
DELIMITER ;
