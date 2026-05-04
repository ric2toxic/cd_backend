DELIMITER $$
DROP TRIGGER IF EXISTS afterUpdateOcTentativeAdvance $$

CREATE DEFINER=`root`@`localhost` TRIGGER `afterUpdateOcTentativeAdvance` AFTER UPDATE ON `oc_tentative_advance`
 FOR EACH ROW BEGIN 
   CALL updateOperationsStatusOfOrder(NEW.order_id); 
   
END$$
DELIMITER ;
