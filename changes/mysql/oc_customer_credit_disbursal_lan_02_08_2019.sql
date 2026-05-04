ALTER TABLE `oc_customer_credit_disbursal_lan` ADD `order_id` INT(11) NULL AFTER `firm_name`, ADD `suborder_id` VARCHAR(50) NULL DEFAULT NULL AFTER `order_id`;
