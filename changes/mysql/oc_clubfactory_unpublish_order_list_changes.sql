ALTER TABLE `oc_clubfactory_unpublish_order_list` CHANGE `order_id` `club_factory_order_id` INT(11) NOT NULL;

ALTER TABLE `oc_clubfactory_unpublish_order_list` ADD `wsb_order_id` INT(11) NOT NULL AFTER `id`;
