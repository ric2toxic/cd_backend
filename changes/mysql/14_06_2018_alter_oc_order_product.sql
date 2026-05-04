ALTER TABLE `oc_order_product` ADD `combo_product_id` INT(11) NULL DEFAULT NULL;
UPDATE `oc_order_product` SET `combo_product_id`=`product_id` WHERE `combo_product_id` IS NULL;
