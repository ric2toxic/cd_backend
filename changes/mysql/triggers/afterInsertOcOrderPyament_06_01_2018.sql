DELIMITER $$
DROP TRIGGER IF EXISTS afterInsertOcOrderPayment $$

CREATE DEFINER=`root`@`localhost` TRIGGER `afterInsertOcOrderPayment` AFTER INSERT ON `oc_order_payment`
 FOR EACH ROW BEGIN
   IF NEW.successfull = 1 THEN 
   	  CALL updateOperationsStatusOfOrder(NEW.order_id); 
   END IF;   
END$$
DELIMITER ;
