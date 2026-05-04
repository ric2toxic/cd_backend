ALTER TABLE `oc_tentative_advance` 
ADD COLUMN `customer_id` INT NULL DEFAULT NULL AFTER `order_id`;
