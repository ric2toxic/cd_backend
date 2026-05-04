ALTER TABLE `oc_credit_application` ADD `crif_score` INT(11) NULL AFTER `draft`;
ALTER TABLE `oc_credit_application` ADD `crif_score_date` DATETIME NULL AFTER `crif_score`;

