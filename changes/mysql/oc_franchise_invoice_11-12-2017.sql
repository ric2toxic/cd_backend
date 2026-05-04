CREATE TABLE `oc_franchise_invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Franchise Invoice ID',
  `order_id` int(11) NOT NULL,
  `suborder_id` varchar(20) NOT NULL,
  `franchise_id` int(11) NOT NULL,
  `invoice_prefix` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `invoice_no` varchar(16) CHARACTER SET utf8 NOT NULL,
  `invoice_amount` float(10,2) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `invoice_meta` text CHARACTER SET utf8,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB;