ALTER TABLE `oc_customer` ADD `gst_number` VARCHAR(100) NOT NULL AFTER `customer_type_id`, ADD `uin_number` VARCHAR(100) NOT NULL AFTER `gst_number`;
