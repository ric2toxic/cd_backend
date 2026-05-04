ALTER TABLE `oc_url_alias` ADD `is_custom` INT(1) NOT NULL DEFAULT '0' AFTER `keyword`;

ALTER TABLE `oc_url_alias` ADD `url_type` ENUM('category','product','manufacturer','seller','information') NOT NULL AFTER `keyword`, ADD `search_id` INT(11) NOT NULL AFTER `url_type`;


ALTER TABLE `oc_url_alias` CHANGE `url_type` `url_type` ENUM('category','product','manufacturer','seller','information','filter','category_search') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


ALTER TABLE `oc_url_alias` CHANGE `query` `query` VARCHAR(333) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
