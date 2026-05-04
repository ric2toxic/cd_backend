ALTER TABLE `oc_banner_image` ADD `type` ENUM('link','search','category') NOT NULL DEFAULT 'link' AFTER `item_id`;
