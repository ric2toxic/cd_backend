ALTER TABLE `oc_customer_preferences` 
ADD COLUMN `pref_product_review` TEXT NULL DEFAULT NULL AFTER `pref_shortlist`,
ADD COLUMN `product_like_count` INT(5) NULL DEFAULT NULL AFTER `total_amount`,
ADD COLUMN `product_dislike_count` INT(5) NULL DEFAULT NULL AFTER `product_like_count`;

ALTER TABLE `oc_customer_preferences` 
ADD COLUMN `brand_filter_id` INT(11) NULL DEFAULT NULL AFTER `seller_id`;
