ALTER TABLE `oc_credit_application` ADD `source` ENUM('website','mobile','android','import','admin') NOT NULL AFTER `document_status`;
