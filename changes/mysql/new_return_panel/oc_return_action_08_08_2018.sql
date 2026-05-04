INSERT INTO `oc_return_action` (`return_action_id`, `language_id`, `name`, `class_name`, `post_actions`, `status`) VALUES (NULL, '1', 'Replacement Note', 'ActionReplacementNote', '121,122', '1');


UPDATE `oc_return_action` SET `post_actions` = '108,121,117,140,143' WHERE `oc_return_action`.`return_action_id` = 109 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '108,121,117,140,143' WHERE `oc_return_action`.`return_action_id` = 110 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '108,121,117,140,143' WHERE `oc_return_action`.`return_action_id` = 111 AND `oc_return_action`.`language_id` = 1;

ALTER TABLE `oc_return` ADD `replacement_note_id` INT NULL DEFAULT NULL AFTER `debit_note_id`;

ALTER TABLE `oc_wsb_prefixes` CHANGE `prefix_type` `prefix_type` ENUM('DEBIT_NOTE','CREDIT_NOTE','SELLER_INVOICE','BUYER_INVOICE','FRANCHISE_INVOICE','RETURN_NO','REPLACEMENT_NOTE') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


INSERT INTO `oc_wsb_prefixes` ( `gstin`, `prefix`, `prefix_type`, `financial_year`, `available_no`, `status`, `date_added`) VALUES
('08AABCW7022Q1ZU', 'WSBRJ-RN-', 'REPLACEMENT_NOTE', NULL, 0, 0, '2018-08-09 06:15:32'),
('07AABCW7022Q1ZW', 'WSBDL-RN-', 'REPLACEMENT_NOTE', NULL, 0, 0, '2018-08-09 06:15:32'),
('24AABCW7022Q1Z0', 'WSBGJ-RN-', 'REPLACEMENT_NOTE', NULL, 0, 0, '2018-08-09 06:15:32'),
('29AABCW7022Q1ZQ', 'WSBKA-RN-', 'REPLACEMENT_NOTE', NULL, 0, 0, '2018-08-09 06:15:32'),
('DEFAULT', 'WSB-RN-', 'REPLACEMENT_NOTE', NULL, 0, 0, '2018-08-09 06:15:32'),
('08AABCW7022Q1ZU', 'RJRN', 'REPLACEMENT_NOTE', '18-', 0, 1, '2018-08-09 18:30:00'),
('24AABCW7022Q1Z0', 'GJRN', 'REPLACEMENT_NOTE', '18-', 0, 1, '2017-10-31 18:30:00'),
('07AABCW7022Q1ZW', 'DLRN', 'REPLACEMENT_NOTE', '18-', 0, 1, '2017-10-31 18:30:00'),
('27AABCW7022Q1ZU', 'MHRN', 'REPLACEMENT_NOTE', '18-', 0, 1, '2017-10-31 18:30:00');


UPDATE `oc_return_action` SET `post_actions` = '129,143' WHERE `oc_return_action`.`return_action_id` = 120 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '117,118,119,141,104,143' WHERE `oc_return_action`.`return_action_id` = 123 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `name` = 'Generate Replacement Note' WHERE `oc_return_action`.`return_action_id` = 143 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '108,121,117,140,143,134' WHERE `oc_return_action`.`return_action_id` = 109 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '108,121,117,140,143,134' WHERE `oc_return_action`.`return_action_id` = 110 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '108,121,117,140,143,134' WHERE `oc_return_action`.`return_action_id` = 111 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '124,122,134' WHERE `oc_return_action`.`return_action_id` = 121 AND `oc_return_action`.`language_id` = 1;



