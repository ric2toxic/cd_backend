ALTER TABLE `oc_customer_preferences` 
ADD COLUMN `preference_source` ENUM('CRM', 'WEBSITE', 'CUSTOMER_SELECTED') NULL DEFAULT NULL AFTER `pref_id`;
