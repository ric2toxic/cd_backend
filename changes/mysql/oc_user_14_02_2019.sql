ALTER TABLE `oc_user` CHANGE `password` `password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `oc_user` ADD `old_password` VARCHAR(255) NULL DEFAULT NULL AFTER `permission`, ADD `old_password_active` TINYINT(0) NOT NULL AFTER `old_password`;


