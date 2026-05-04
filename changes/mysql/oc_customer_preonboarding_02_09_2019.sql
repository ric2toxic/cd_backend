ALTER TABLE `oc_customer_preonboarding` CHANGE `office_address` `office_address_line1` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


ALTER TABLE `oc_customer_preonboarding` ADD `office_address_line2` VARCHAR(255) NULL DEFAULT NULL AFTER `office_address_line1`, ADD `office_address_landmark` VARCHAR(255) NULL DEFAULT NULL AFTER `office_address_line2`, ADD `office_city` VARCHAR(100) NULL DEFAULT NULL AFTER `office_address_landmark`, ADD `office_state` VARCHAR(100) NULL DEFAULT NULL AFTER `office_city`;
