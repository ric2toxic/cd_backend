ALTER TABLE `oc_credit_application` ADD `form_action` ENUM('add','edit') NOT NULL DEFAULT 'add' AFTER `draft`;


