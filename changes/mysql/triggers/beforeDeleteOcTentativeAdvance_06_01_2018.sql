DELIMITER $$
DROP TRIGGER IF EXISTS beforeDeleteOcTentativeAdvance $$

CREATE DEFINER=`root`@`localhost` TRIGGER `beforeDeleteOcTentativeAdvance` BEFORE DELETE ON `oc_tentative_advance`
 FOR EACH ROW BEGIN 
   CALL updateOperationsStatusOfOrder(OLD.order_id); 
END$$
DELIMITER ;