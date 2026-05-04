DELIMITER $$
DROP TRIGGER IF EXISTS beforeDeleteOcOrderPayment $$

CREATE DEFINER=`root`@`localhost` TRIGGER `beforeDeleteOcOrderPayment` BEFORE DELETE ON `oc_order_payment`
 FOR EACH ROW BEGIN
   IF OLD.successfull = 1 THEN 
   	  CALL updateOperationsStatusOfOrder(OLD.order_id); 
   END IF;   

END$$
DELIMITER ;