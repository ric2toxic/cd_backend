INSERT INTO `oc_share_templates` (`template_id`, `title`, `template`, `status`, `customer_id`) VALUES (NULL, 'Reseller Template', '[product_name] [set_showonlysize] ', '1', '0');

ALTER TABLE `oc_customer` CHANGE `default_share_template` `default_share_template` INT(11) NOT NULL DEFAULT '0' COMMENT 'default share template for android';

UPDATE `oc_customer` SET default_share_template = 0 WHERE is_dropshipper = 1 and default_share_template = 1

