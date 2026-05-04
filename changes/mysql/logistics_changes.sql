ALTER TABLE `oc_bluedart_pincodes` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `mode`;
ALTER TABLE `oc_gati_pincodes` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `prepaid`;
ALTER TABLE `oc_delhivery_pincodes` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `value_capping`;
ALTER TABLE `oc_truxcargo_pincodes` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `oda`;
ALTER TABLE `oc_fedex_pincodes` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `cod_serviceable`;


