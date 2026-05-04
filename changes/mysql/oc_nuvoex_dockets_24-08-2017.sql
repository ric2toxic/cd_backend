CREATE TABLE `oc_nuvoex_dockets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `docket_no` varchar(100) NOT NULL,
  `date_added` datetime DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `comments` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

