ALTER TABLE `oc_wsb_preorder` ADD `option_name` VARCHAR(50) NOT NULL AFTER `product_id`, ADD `option_value` VARCHAR(50) NOT NULL AFTER `option_name`;
