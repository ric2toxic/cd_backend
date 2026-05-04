
DROP TABLE IF EXISTS `oc_custom_parties`;

CREATE TABLE `oc_custom_parties` (
  `custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `firm_name` varchar(128) CHARACTER SET latin1 NOT NULL,
  `address1` text CHARACTER SET latin1 NOT NULL,
  `address2` text CHARACTER SET latin1,
  `country` text CHARACTER SET latin1 NOT NULL,
  `country_id` int(11) NOT NULL,
  `city` varchar(128) CHARACTER SET latin1 NOT NULL,
  `state` varchar(128) CHARACTER SET latin1 NOT NULL,
  `pincode` text CHARACTER SET latin1 NOT NULL,
  `zone_id` int(11) NOT NULL,
  `gst_number` varchar(100) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`custom_id`),
  FULLTEXT KEY `Custom Party` (`firm_name`,`city`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

