CREATE TABLE `oc_daily_once_change_log` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` int(11) DEFAULT NULL,
  `table_name` varchar(32) CHARACTER SET utf8 NOT NULL,
  `solr_synced` tinyint(1) NOT NULL,
  `solr_synced_date` datetime DEFAULT NULL,
  `change_details` text CHARACTER SET utf8 NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
