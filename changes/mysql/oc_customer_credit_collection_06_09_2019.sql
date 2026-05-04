ALTER TABLE `oc_customer_credit_collection` ADD `date_added` DATE NULL DEFAULT NULL AFTER `last_payment_date`;


ALTER TABLE `oc_customer_credit_collection` CHANGE `cif_id` `cif_id` VARCHAR(255) NULL DEFAULT NULL;



