ALTER TABLE `oc_customer` ADD `customer_parent_id` INT(11) NOT NULL AFTER `customer_id`;
ALTER TABLE `oc_customer` ADD `company` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `fax`;
ALTER TABLE `oc_customer` ADD `mobile_country_code` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `email`;
