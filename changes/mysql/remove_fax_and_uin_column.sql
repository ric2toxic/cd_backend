ALTER TABLE `oc_customer` DROP `uin_number`;
ALTER TABLE `oc_customer` DROP `fax`;
ALTER TABLE `oc_order` CHANGE `fax` `fax` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `oc_customer` DROP `isd`;
