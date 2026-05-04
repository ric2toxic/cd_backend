ALTER TABLE `oc_ms_seller` ADD `app_info` TEXT NULL DEFAULT NULL AFTER `purchase_firm_id`, ADD `app_version_code` INT NOT NULL DEFAULT '0' AFTER `app_info`;
