CREATE TABLE `oc_gati_dockets` ( `id` INT NOT NULL AUTO_INCREMENT COMMENT 'primary key' , `docket_no` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'docket number' ,`order_no` VARCHAR(63) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'order number with this docket' , `used` INT(1) NOT NULL COMMENT 'whether docket no used' , PRIMARY KEY (`id`)) ENGINE = MyISAM;

ALTER TABLE `oc_gati_dockets` ADD `comments` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'comments' AFTER `used`;

ALTER TABLE `oc_gati_dockets` CHANGE `used` `used` INT(1) NOT NULL DEFAULT '0' COMMENT 'whether docket no used';

ALTER TABLE `oc_gati_dockets` ADD `weight` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `order_no`;