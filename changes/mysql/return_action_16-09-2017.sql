DROP TABLE IF EXISTS `oc_return_action`;

CREATE TABLE `oc_return_action` (
  `return_action_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`return_action_id`,`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

INSERT INTO `oc_return_action` VALUES (1,1,'Return Approved - Awaiting Products'),(2,1,'Returned Goods Received'),(3,1,'Amount Refunded'),(4,1,'Credit Issued'),(5,1,'Replacement Sent'),(6,1,'Replacement Reserved'),(7,1,'Return Request Rejected'),(8,1,'Returned Goods Rejected'),(9,1,'Rejected Returns Reserved'),(10,1,'Rejected Returns Resent'),(11,1,'Cancelled'),(12,1,'Replacement not available'),(13,1,'Goods to Debit Note');
