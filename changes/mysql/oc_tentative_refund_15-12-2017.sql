CREATE TABLE `oc_tentative_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refund_type` enum('CREDIT_NOTE','EXCESS_PAYMENT_BY_CUSTOMER') NOT NULL,
  `ref_id` int(11) NOT NULL,
  `refund_ref` varchar(255) DEFAULT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `order_no` varchar(32) NOT NULL,
  `suborder_id` varchar(255) DEFAULT NULL,
  `txn_status` varchar(255) NOT NULL,
  `total_refund` float(10,2) NOT NULL,
  `date_added` datetime NOT NULL,
  `reference` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `updated_by_user` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `is_cleared` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)