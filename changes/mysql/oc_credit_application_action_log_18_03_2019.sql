ALTER TABLE `oc_credit_application_action_log` CHANGE `type` `type` ENUM('CUSTOMER','KHUFIYA_USER','CRM_USER','CRON') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'CUSTOMER';
