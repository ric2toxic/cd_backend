/////////////////////////////////// VERSION 2.0 //////////////////////////////


INSERT INTO `oc_return_action` (`return_action_id`, `language_id`, `name`, `class_name`, `post_actions`, `status`) VALUES (142, '1', 'Cancel Reverse Shipment', 'ActionCancelReverseShipment', '105,107,108', '0');

 
ALTER TABLE `oc_return_shipment_tracking` ADD `is_cancel` TINYINT(1) NOT NULL DEFAULT '0' AFTER `remarks`;


ALTER TABLE `oc_return_shipment_tracking` ADD `date_cancelled` DATETIME NULL DEFAULT NULL AFTER `date_added`;

ALTER TABLE `oc_return_shipment_backto_customer` ADD `date_cancelled` DATETIME NULL DEFAULT NULL AFTER `date_added`;

ALTER TABLE `oc_return_shipment_backto_customer` ADD `is_cancel` TINYINT(1) NOT NULL DEFAULT '0' AFTER `remarks`;

ALTER TABLE `oc_return_shipment_backto_customer` ADD `shipping_details` TEXT NULL DEFAULT NULL AFTER `request_param`, ADD `file_name` VARCHAR(255) NULL DEFAULT NULL AFTER `shipping_details`;

ALTER TABLE `oc_seller_debit_note` ADD `custom_dn_ref` VARCHAR(100) NULL DEFAULT NULL AFTER `sor_payment_id`;

ALTER TABLE `oc_return_shipment_tracking` ADD `shipping_details` TEXT NULL DEFAULT NULL AFTER `request_param`;
