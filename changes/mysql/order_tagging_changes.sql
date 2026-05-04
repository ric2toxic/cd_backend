ALTER TABLE `oc_order_sales_staff` ADD `tag_type` ENUM('Manual','Automatic') NOT NULL DEFAULT 'Manual' AFTER `user_id`;

ALTER TABLE `oc_customer` ADD `self_order` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `oc_order` ADD `self_order` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `oc_order` ADD `self_order_reason` ENUM('NULL','Non working hours') NOT NULL DEFAULT 'NULL' AFTER `self_order`;
