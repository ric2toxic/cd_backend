INSERT INTO `oc_url_alias` (`url_alias_id`, `query`, `keyword`, `url_type`, `store_id`, `search_id`, `is_custom`, `is_redirect_301`) VALUES (NULL, 'information/policies', 'policies', 'information', NULL, '0', '0', '0');

ALTER TABLE `oc_information_description` ADD `short_description` LONGBLOB NOT NULL AFTER `title`;
