	ALTER TABLE `oc_return` ADD `return_shipment_tracking_id` INT(11) NOT NULL AFTER `customer_id`, ADD `return_pickup_address_id` INT(11) NOT NULL AFTER `return_shipment_tracking_id`;
	return_shipment_tracking_id
