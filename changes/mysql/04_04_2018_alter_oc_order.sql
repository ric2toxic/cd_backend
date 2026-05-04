ALTER TABLE `oc_suborder` ADD `no_wsb_tape` TINYINT(1) NOT NULL DEFAULT '0' AFTER `custom_totals`, ADD `no_invoice_with_shipment` TINYINT(1) NOT NULL DEFAULT '0' AFTER `no_wsb_tape`;
