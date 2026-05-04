ALTER TABLE `oc_seller_invoice` ADD `invoice_image` VARCHAR(255) NOT NULL AFTER `seller_invoice_no`;
ALTER TABLE `oc_seller_invoice` ADD `invoice_image_approved` TINYINT(1) NULL DEFAULT '0' AFTER `invoice_received_comment`, ADD `invoice_image_comment` TEXT CHARACTER SET utf16 COLLATE utf16_bin NOT NULL AFTER `invoice_image_approved`;
