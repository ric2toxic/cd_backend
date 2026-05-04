CREATE TABLE `oc_payment_gateway_error_logs` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `error_data` TEXT NOT NULL,
  `order_id` INT(11) NOT NULL,
  `payment_gateway` ENUM('citrus', 'razorpay', 'bank_transfer', 'cash', 'paytm', 'fedex', 'gati_kwe', 'gati_ltd', 'coupon', 'cashback', 'neogrowth', 'upi', 'paytabs', 'dotzot', 'connect-india', 'bluedart') NOT NULL,
  `date_added` DATETIME NOT NULL,
  PRIMARY KEY (`log_id`));
