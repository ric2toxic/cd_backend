INSERT INTO `oc_url_alias` (`url_alias_id`, `query`, `keyword`, `url_type`, `store_id`, `search_id`, `is_custom`, `is_redirect_301`) VALUES (NULL, 'information/contact', 'contact-us', 'information', NULL, '0', '0', '0');

INSERT INTO `oc_url_alias` (`url_alias_id`, `query`, `keyword`, `url_type`, `store_id`, `search_id`, `is_custom`, `is_redirect_301`) VALUES (NULL, 'information/storelocator', 'storelocator', 'information', NULL, '0', '0', '0');

UPDATE `oc_url_alias` SET `query` = 'information/aboutus' WHERE `oc_url_alias`.`keyword` = 'about-us';

UPDATE `oc_url_alias` SET `query` = 'account/dropshipper' WHERE `oc_url_alias`.`keyword` = 'dropshipper';
