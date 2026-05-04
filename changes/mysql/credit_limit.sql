ALTER TABLE `oc_credit_application_status_remarks` ADD `credit_limit` INT(11) NOT NULL AFTER `remark`, ADD `credit_expire_date` DATETIME NOT NULL AFTER `credit_limit`;
