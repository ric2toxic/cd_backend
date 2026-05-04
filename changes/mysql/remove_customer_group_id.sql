ALTER TABLE `oc_customer` DROP `customer_group_id`;
ALTER TABLE `oc_product_special` DROP `customer_group_id`;
ALTER TABLE `oc_product_discount` DROP `customer_group_id`;
ALTER TABLE `oc_product_recurring` DROP `customer_group_id`;

DROP TABLE `oc_customer_group`;
DROP TABLE `oc_customer_group_description`;
DROP TABLE `oc_custom_field_customer_group`;
DROP TABLE `oc_tax_rate_to_customer_group`;
DROP TABLE `oc_product_reward`;

DELETE FROM `oc_setting` WHERE `oc_setting`.`key` = 'config_customer_group_id';
DELETE FROM `oc_setting` WHERE `oc_setting`.`key` = 'config_customer_group_display';
DELETE FROM `oc_setting` WHERE `oc_setting`.`key` = 'pp_login_customer_group_id';
DELETE FROM `oc_setting` WHERE `oc_setting`.`key` = 'openbay_amazon_order_customer_group';
DELETE FROM `oc_setting` WHERE `oc_setting`.`key` = 'openbay_amazonus_order_customer_group';

ALTER TABLE `oc_customer` DROP `customer_parent_id`;

ALTER TABLE `oc_order` CHANGE `customer_group_id` `customer_group_id` INT(4) NOT NULL DEFAULT '0';
ALTER TABLE `oc_order_product` CHANGE `reward` `reward` INT(8) NOT NULL DEFAULT '0';

