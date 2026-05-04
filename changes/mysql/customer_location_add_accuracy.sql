ALTER TABLE `oc_customer_location` ADD `accuracy` DOUBLE NULL AFTER `location_timestamp`, ADD `actual_location_timestamp` INT(11) NULL AFTER `accuracy`;
