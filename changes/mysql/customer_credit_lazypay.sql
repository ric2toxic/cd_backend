ALTER TABLE oc_customer_credit DROP PRIMARY KEY;

ALTER TABLE `oc_customer_credit` ADD `credit_id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`credit_id`);

ALTER TABLE `oc_customer_credit` ADD `type` ENUM('Neogrowth','Lazypay') NOT NULL DEFAULT 'Neogrowth' AFTER `customer_id`;

ALTER TABLE `oc_customer_credit` ADD `lazypay_email` VARCHAR(128) NOT NULL AFTER `neogrowth_account_number`, ADD `lazypay_mobile` VARCHAR(32) NOT NULL AFTER `lazypay_email`;


/* Add record in `oc_wsb_extension_to_store` table for newly added Lazypay extension */


