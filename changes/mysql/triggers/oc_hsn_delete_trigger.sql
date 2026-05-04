DELIMITER //
DROP TRIGGER IF EXISTS oc_hsn_delete_trigger //

CREATE DEFINER=`root`@`localhost` TRIGGER oc_hsn_delete_trigger 
    AFTER DELETE ON oc_hsn 
    FOR EACH ROW 
BEGIN
    
      UPDATE oc_product SET hsn_code = "" , tax_class_id = 0 WHERE hsn_code = OLD.hsn_code;

END //
DELIMITER ;
