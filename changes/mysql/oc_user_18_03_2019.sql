ALTER TABLE `oc_user` ADD `dont_show_dashboard` TINYINT(1) NOT NULL DEFAULT '0' AFTER `old_password_active`, ADD `default_landing_page_url` VARCHAR(255) NULL DEFAULT NULL AFTER `dont_show_dashboard`;
