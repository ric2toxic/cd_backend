CREATE TABLE `oc_otp_failure` ( `otp_id` INT NOT NULL AUTO_INCREMENT , `telephone` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `status` TINYINT(1) NOT NULL , `message` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,PRIMARY KEY (`otp_id`)) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'table for otp log';

ALTER TABLE `oc_otp_failure` CHANGE `status`  `status` VARCHAR(20) NOT NULL;

ALTER TABLE `oc_otp_failure` ADD `email_sent` TINYINT(1) NOT NULL DEFAULT '0' AFTER `message`;

ALTER TABLE `oc_otp_failure` ADD `date_added` DATETIME NOT NULL;