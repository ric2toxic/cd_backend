ALTER TABLE `oc_banner_image` CHANGE `target_blank` `target_blank` TINYINT(4) NOT NULL DEFAULT '0';

ALTER TABLE `oc_banner` ADD `is_category` TINYINT(1) NOT NULL AFTER `name`;

ALTER TABLE `oc_banner` CHANGE `is_category` `is_category` TINYINT(1) NOT NULL DEFAULT '0'; 
