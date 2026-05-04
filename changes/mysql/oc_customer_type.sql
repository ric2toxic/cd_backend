CREATE TABLE `oc_customer_type` (
  `customer_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL DEFAULT '',
  `date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`customer_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;