ALTER TABLE `oc_gati_dockets` ADD `type` ENUM('gati_kwe', 'gati_ltd') NULL DEFAULT NULL AFTER `weight`;
