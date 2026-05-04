ALTER TABLE `oc_courier_partners` ADD `is_reverse_shipment` TINYINT(1) NOT NULL DEFAULT '0' AFTER `tracking_url`;
