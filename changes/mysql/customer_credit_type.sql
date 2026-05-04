ALTER TABLE `oc_customer_credit` ADD `type` ENUM('Neogrowth','Lazypay') NOT NULL DEFAULT 'Neogrowth' AFTER `customer_id`;
