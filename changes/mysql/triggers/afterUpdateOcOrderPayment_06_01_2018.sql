DELIMITER $$
DROP TRIGGER IF EXISTS afterUpdateOcOrderPayment $$

CREATE DEFINER=`root`@`localhost` TRIGGER `afterUpdateOcOrderPayment` AFTER UPDATE ON `oc_order_payment`
 FOR EACH ROW BEGIN
   IF NEW.successfull = 1 OR
   	  OLD.successfull = 1 THEN 
   	  CALL updateOperationsStatusOfOrder(NEW.order_id); 
   END IF;   
END$$
DELIMITER ;
