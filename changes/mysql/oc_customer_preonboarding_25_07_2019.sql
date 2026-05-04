ALTER TABLE `oc_customer_preonboarding` ADD `anchor_name` VARCHAR(65) NULL AFTER `retailer_id`, ADD `anchor_id` VARCHAR(65) NULL AFTER `anchor_name`;


ALTER TABLE `oc_customer_credit_preapproved` ADD `utr` VARCHAR(25) NULL AFTER `retailer_id`;


