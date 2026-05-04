ALTER TABLE `oc_rbl_api_log` CHANGE `api_type` `api_type` ENUM('cifStatus','orderPunch','lanDisbursement','orderCancellation') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `oc_rbl_api_log` CHANGE `status` `status` ENUM('NEW','SUCCESS','FAILED','CANCELLED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'NEW';

ALTER TABLE `oc_rbl_api_log` ADD `user` VARCHAR(100) NULL DEFAULT NULL AFTER `id`;
