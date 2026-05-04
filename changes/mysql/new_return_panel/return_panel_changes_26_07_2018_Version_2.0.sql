/////////////////////////////////// VERSION 2.0 //////////////////////////////

INSERT INTO `oc_return_action` (`return_action_id`, `language_id`, `name`, `class_name`, `post_actions`, `status`) VALUES (142, '1', 'Cancel Reverse Shipment', 'ActionCancelReverseShipment', '105,107,108', '0');
 
ALTER TABLE `oc_return_shipment_tracking` ADD `is_cancel` TINYINT(1) NOT NULL DEFAULT '0' AFTER `remarks`;

ALTER TABLE `oc_return_shipment_tracking` ADD `date_cancelled` DATETIME NULL DEFAULT NULL AFTER `date_added`;

ALTER TABLE `oc_return_shipment_backto_customer` ADD `date_cancelled` DATETIME NULL DEFAULT NULL AFTER `date_added`;

ALTER TABLE `oc_return_shipment_backto_customer` ADD `is_cancel` TINYINT(1) NOT NULL DEFAULT '0' AFTER `remarks`;

ALTER TABLE `oc_return_shipment_tracking` ADD `token_no` VARCHAR(25) NULL DEFAULT NULL AFTER `shipping_details`;

ALTER TABLE `oc_return` CHANGE `return_shipment_tracking_id` `return_shipment_tracking_id` INT(11) NULL DEFAULT NULL;

ALTER TABLE `oc_return` CHANGE `return_shipment_backto_customer_id` `return_shipment_backto_customer_id` INT(11) NULL DEFAULT NULL;

UPDATE `oc_return` SET	`return_shipment_tracking_id` = NULL WHERE `return_shipment_tracking_id` = 0

UPDATE `oc_return` SET	`return_shipment_backto_customer_id` = NULL WHERE `return_shipment_backto_customer_id` = 0

ALTER TABLE `oc_seller_debit_note` CHANGE `custom_dn_ref` `custom_debit_note_ref` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
