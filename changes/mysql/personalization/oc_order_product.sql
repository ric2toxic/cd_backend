ALTER TABLE `oc_order_product` ADD COLUMN `product_review` ENUM('like', 'dislike') NULL DEFAULT NULL AFTER `is_returnable`;
ALTER TABLE `oc_order_product` ADD COLUMN `product_review_date` DATETIME NULL AFTER `product_review`;
ALTER TABLE `oc_order_product` ADD COLUMN `previous_product_review` ENUM('like', 'dislike') NULL AFTER `product_review`;
ALTER TABLE `oc_order_product` ADD COLUMN `review_sync` TINYINT(1) NULL AFTER `previous_product_review`;
