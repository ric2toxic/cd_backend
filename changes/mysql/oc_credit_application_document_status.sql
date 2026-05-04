ALTER TABLE `oc_credit_application` ADD `document_status` SET('document_awaited','under_process','approved','rejected') NULL AFTER `version`;
