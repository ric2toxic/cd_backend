CREATE DEFINER=`root`@`localhost` TRIGGER `update_order_backup_table` AFTER UPDATE ON `oc_suborder`
 FOR EACH ROW IF OLD.order_status_id = 0 and NEW.order_status_id=1 THEN BEGIN
INSERT INTO oc_order_backup
SELECT *,NOW() FROM `oc_order` o
WHERE o.order_id=OLD.order_id;
INSERT INTO oc_suborder_backup
SELECT *,NOW() FROM `oc_suborder` sub
WHERE sub.suborder_id=OLD.suborder_id;
INSERT INTO oc_order_product_backup
SELECT *,NOW() FROM `oc_order_product` op
WHERE op.suborder_id=OLD.suborder_id;
INSERT INTO oc_order_option_backup
SELECT *,NOW() FROM `oc_order_option` oo
WHERE oo.suborder_id=OLD.suborder_id;
END;
END IF
