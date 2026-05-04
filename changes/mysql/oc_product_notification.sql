CREATE TABLE `oc_product_notification` (
  `product_notification_id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `operation_type` enum('NO','NEW_PRODUCT','STOCK_TRANSFER') NOT NULL DEFAULT 'NO'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `oc_product_notification`
  ADD PRIMARY KEY (`product_notification_id`);
ALTER TABLE `oc_product_notification`
  MODIFY `product_notification_id` int(11) NOT NULL AUTO_INCREMENT;
