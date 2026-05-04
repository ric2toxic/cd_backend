ALTER TABLE `oc_gati_dockets` ADD `is_success` TINYINT(1) NOT NULL DEFAULT '0' AFTER `used`;



ALTER TABLE `oc_gati_dockets` ADD `post_data` TEXT NULL DEFAULT NULL AFTER `comments`, ADD `response_data` TEXT NULL DEFAULT NULL AFTER `post_data`;
