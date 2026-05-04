ALTER TABLE `oc_tax_rate` ADD `start_range` DECIMAL(10,2) NOT NULL , ADD `end_range` FLOAT NOT NULL AFTER `start_range`, ADD `rate_logic` ENUM('price_range','global') NOT NULL AFTER `end_range` 

`ALTER TABLE oc_tax_rate DROP PRIMARY KEY`
