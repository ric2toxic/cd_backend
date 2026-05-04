ALTER TABLE `oc_sales_staff` ADD `store_delivery` VARCHAR(8) NULL DEFAULT NULL;

ALTER TABLE `oc_sales_staff` ADD `store_delivery_last_updated` DATETIME NULL DEFAULT NULL ;

DELETE FROM oc_setting WHERE oc_setting.key = 'coupon_store_trial';
