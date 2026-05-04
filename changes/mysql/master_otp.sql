ALTER TABLE `oc_sales_staff` ADD `master_otp` INT(6) NOT NULL AFTER `imei_number`, ADD `master_otp_last_updated` DATETIME NOT NULL AFTER `master_otp`;
ALTER TABLE `oc_customer_signups` ADD `staff_id` INT(11) NOT NULL DEFAULT '0' AFTER `customer_id`;

