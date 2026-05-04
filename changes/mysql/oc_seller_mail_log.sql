CREATE TABLE `oc_seller_mail_log` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `order_id` int(11) NOT NULL,
 `suborder_id` varchar(20) NOT NULL,
 `seller_id` int(11) NOT NULL,
 `action` enum('AUTO','BTN_TRIGGER','ADD_UPDATE_PRODUCT') DEFAULT NULL,
 `date_added` datetime DEFAULT NULL,
 `user_id` int(11) DEFAULT NULL,
 `comment` text,
 PRIMARY KEY (`id`),
 KEY `order_id` (`order_id`),
 KEY `suborder_id` (`suborder_id`),
 KEY `seller_id` (`seller_id`),
 KEY `order_id_2` (`order_id`,`suborder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
