CREATE TABLE `oc_master_return` (
  `master_return_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `return_no` varchar(50) NOT NULL,
  `return_shipment_tracking_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL,
  `shipping_method` enum('self_courier','wsb_pickup','not_decided','') NOT NULL,
  `date_added` datetime NOT NULL,
  `cancel_return` tinyint(4) NOT NULL,
  PRIMARY KEY (`master_return_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1442 DEFAULT CHARSET=utf8;

