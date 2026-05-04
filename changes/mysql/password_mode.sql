ALTER TABLE `oc_customer` ADD `password_mode` ENUM('old','new') NOT NULL DEFAULT 'old' AFTER `password`;

