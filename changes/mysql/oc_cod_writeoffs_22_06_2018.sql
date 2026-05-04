ALTER TABLE `oc_cod_writeoffs` CHANGE `suborder_id` `suborder_id` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `oc_cod_writeoffs` ADD `order_no` VARCHAR(20) NOT NULL AFTER `order_id`, ADD `customer_name` VARCHAR(500) NOT NULL AFTER `suborder_id`, ADD `courier_partner` VARCHAR(255) NOT NULL AFTER `customer_name`, ADD `tracking_no` VARCHAR(150) DEFAULT NULL AFTER `courier_partner`, ADD `amount` DECIMAL(15,2) NOT NULL AFTER `tracking_no`;

=========== If You don't have oc_cod_writeoffs table on local ================

CREATE TABLE `oc_cod_writeoffs` (
  `order_id` int(11) NOT NULL,
  `order_no` varchar(20) NOT NULL,
  `suborder_id` varchar(150) NOT NULL,
  `customer_name` varchar(500) NOT NULL,
  `courier_partner` varchar(255) NOT NULL,
  `tracking_no` varchar(150) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  UNIQUE KEY `order_id` (`order_id`,`suborder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8


