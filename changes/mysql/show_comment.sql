ALTER TABLE `oc_credit_application_status_remarks` ADD `show_comment` TINYINT(2) NOT NULL AFTER `reason`;

ALTER TABLE `oc_credit_application_status_remarks` ADD `bank_account_last_digit` VARCHAR(20) NOT NULL AFTER `followup_date`, ADD `ifsc_code` VARCHAR(20) NOT NULL AFTER `bank_account_last_digit`;
