CREATE TABLE `oc_customer_preferences_all` (
 `customer_id` int(10) unsigned NOT NULL DEFAULT '0',
 `category_id` smallint(5) unsigned NOT NULL DEFAULT '0',
 `source` enum('cust','algo','crm','admin') NOT NULL DEFAULT 'algo',
 `criteria_type` enum('seller','brand_filter','rating','NA') NOT NULL DEFAULT 'NA',
 `criteria_type_value` int(10) unsigned NOT NULL DEFAULT '0',
 `pref_type` enum('order','shortlist','product_review','return','NA') NOT NULL DEFAULT 'NA',
 `min_price` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `max_price` mediumint(8) unsigned NOT NULL DEFAULT '0',
 `pieces_count` smallint(5) unsigned DEFAULT '0',
 `total_amount` int(10) unsigned DEFAULT '0',
 `product_like_count` smallint(5) unsigned DEFAULT '0',
 `product_dislike_count` smallint(5) unsigned DEFAULT '0',
 `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`customer_id`,`source`,`criteria_type`,`pref_type`,`category_id`,`criteria_type_value`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;