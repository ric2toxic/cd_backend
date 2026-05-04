ALTER TABLE `oc_wsb_questions` ADD `seller_visible` TINYINT(1) NOT NULL AFTER `date_added`, ADD `email_sent` TINYINT(1) NOT NULL DEFAULT '0' AFTER `seller_visible`;
ALTER TABLE `oc_wsb_questions` ADD `is_answered` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `oc_wsb_questions` ADD `seller_id` INT NOT NULL ;
ALTER TABLE `oc_wsb_questions` ADD `seller_email` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL

ALTER TABLE `oc_wsb_questions` ADD `product_sku` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `product_id`, 
ADD `product_model` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `product_sku`;ALTER TABLE `oc_wsb_questions` ADD `product_sku` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `product_id`, ADD `product_model` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `oc_wsb_questions` ADD `product_image` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `oc_wsb_questions` ADD `seller_code` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_wsb_questions` ADD `question_id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`question_id`);
ALTER TABLE `oc_wsb_questions` ADD `product_name` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_wsb_questions` CHANGE `seller_visible` `seller_visible` TINYINT(0) NOT NULL DEFAULT '1';