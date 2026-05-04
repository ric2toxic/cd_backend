CREATE TABLE `oc_admin_change_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) DEFAULT NULL,
  `field_name` varchar(50) DEFAULT NULL,
  `ref_url` varchar(150) DEFAULT NULL,
  `old_value` varchar(100) DEFAULT NULL,
  `new_value` varchar(100) DEFAULT NULL,
  `comment` varchar(100) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `user_agent` varchar(100) DEFAULT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;