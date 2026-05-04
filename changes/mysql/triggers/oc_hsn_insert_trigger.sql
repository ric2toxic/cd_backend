DELIMITER //
DROP TRIGGER IF EXISTS oc_hsn_insert_trigger //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_hsn_insert_trigger 
    AFTER INSERT ON oc_hsn 
    FOR EACH ROW 
BEGIN
    DECLARE tax_class INT(11);
    UPDATE oc_product SET price = price+0.001 WHERE hsn_code = NEW.hsn_code;    
    UPDATE oc_product SET price = price-0.001 WHERE hsn_code = NEW.hsn_code;
    SELECT oh.tax_class_id INTO tax_class FROM oc_hsn oh WHERE oh.hsn_code = NEW.hsn_code;
    UPDATE oc_product SET tax_class_id = tax_class WHERE hsn_code = NEW.hsn_code;
END //
DELIMITER ; 
