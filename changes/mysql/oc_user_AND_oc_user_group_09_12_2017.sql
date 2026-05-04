ALTER TABLE `oc_user_group` CHANGE `permission` `permission` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_user` ADD `permission` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `branch_code`;

