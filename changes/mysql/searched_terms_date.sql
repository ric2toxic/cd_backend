ALTER TABLE `oc_searched_terms` ADD `created` DATETIME NOT NULL AFTER `search_history`;

ALTER TABLE `oc_searched_terms` ADD `modified` DATETIME NOT NULL AFTER `created`;
