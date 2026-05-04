DELIMITER $$
DROP TRIGGER IF EXISTS afterInsertOcTentativeAdvance $$

CREATE DEFINER=`root`@`localhost` TRIGGER `afterInsertOcTentativeAdvance` AFTER INSERT ON `oc_tentative_advance`
 FOR EACH ROW BEGIN 
   CALL updateOperationsStatusOfOrder(NEW.order_id); 
   
END$$
DELIMITER ;
