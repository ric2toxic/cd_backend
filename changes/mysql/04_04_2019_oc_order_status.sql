ALTER TABLE `oc_order_status` ADD `is_invoice_required` TINYINT(1) NOT NULL DEFAULT '0' AFTER `post_actions`;

UPDATE `oc_order_status` SET is_invoice_required = 1 WHERE `order_status_id` IN (4,5,8,11,12,13,14,15)


