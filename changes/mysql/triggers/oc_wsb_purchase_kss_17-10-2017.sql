DELIMITER $$
DROP TRIGGER IF EXISTS
  delete_wsb_purchase_breakup $$
CREATE DEFINER=`root`@`localhost` TRIGGER `delete_wsb_purchase_breakup` AFTER
DELETE ON
  `oc_wsb_purchase` FOR EACH ROW
BEGIN
DELETE
FROM
  oc_wsb_purchase_breakup
WHERE
  purchase_id = old.purchase_id;
END $$
DELIMITER ;
