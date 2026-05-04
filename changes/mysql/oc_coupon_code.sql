ALTER TABLE `oc_coupon_history` 
ADD COLUMN `coupon_code` VARCHAR(10) NULL AFTER `date_added`;

ALTER TABLE `oc_coupon_history` 
ADD COLUMN `coupon_name` VARCHAR(45) NULL AFTER `coupon_code`;
