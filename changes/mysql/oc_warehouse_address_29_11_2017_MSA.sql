ALTER TABLE `oc_warehouse_address` ADD `bluedart_vendor_code` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `nuvoex_vendor_code`, ADD `bluedart_customer_code` INT(11) NULL DEFAULT NULL AFTER `bluedart_vendor_code`, ADD `bluedart_origin_area` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `bluedart_customer_code`, ADD `email` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `bluedart_origin_area`;



