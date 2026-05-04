ALTER TABLE `oc_bluedart_dockets` ADD `shipping_method` VARCHAR(64) NULL DEFAULT NULL
AFTER  `unit`, ADD `shipping_code` VARCHAR(64) NULL DEFAULT NULL
AFTER `shipping_method`, ADD `customer_code` INT(11) NULL DEFAULT NULL
AFTER `shipping_code`, ADD `vendor_code` VARCHAR(64) NULL DEFAULT NULL AFTER `customer_code`;
