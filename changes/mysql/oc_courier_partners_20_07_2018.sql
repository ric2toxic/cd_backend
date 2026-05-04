ALTER TABLE `oc_courier_partners` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `is_forward_shipment`;

UPDATE `oc_courier_partners` SET `status` = '0' WHERE `oc_courier_partners`.`id` IN (7,8,9,10,12);




