ALTER TABLE `oc_banner_image` CHANGE `target_blank` `target_blank` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `oc_banner_image` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `target_blank`;

