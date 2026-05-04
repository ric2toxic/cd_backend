DROP TABLE IF EXISTS `oc_return_shipment_tracking`;
CREATE TABLE `oc_return_shipment_tracking` (
  `shipping_id` int(11) NOT NULL AUTO_INCREMENT,
  `courier_company` varchar(255) NOT NULL,
  `tracking_no` varchar(255) NOT NULL COMMENT 'NuvoEx Docket/AWB',
  `shipping_slip` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `order_no` varchar(50) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `value` float(10,2) DEFAULT NULL,
  `package_description` varchar(255) DEFAULT NULL,
  `qty` int(5) DEFAULT NULL,
  `return_reason` varchar(255) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` varchar(10) DEFAULT NULL,
  `vendor_code` varchar(50) DEFAULT NULL,
  `request_param` text,
  `remarks` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`shipping_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

