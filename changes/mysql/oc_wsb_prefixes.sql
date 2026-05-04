CREATE TABLE `oc_wsb_prefixes` (
  `prefix_id` int(11) NOT NULL AUTO_INCREMENT,
  `gstin` varchar(25) NOT NULL,
  `prefix` varchar(100) NOT NULL,
  `prefix_type` enum('DEBIT_NOTE','CREDIT_NOTE','SELLER_INVOICE','BUYER_INVOICE') DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prefix_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
