ALTER TABLE `oc_order_status` ADD `post_actions` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `priority`;

UPDATE `oc_order_status` SET `post_actions` = '2,6,9,16' WHERE `oc_order_status`.`order_status_id` = 1 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '2,6,9,16' WHERE `oc_order_status`.`order_status_id` = 1 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '2,6,13,14' WHERE `oc_order_status`.`order_status_id` = 9 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '2,6,13,14' WHERE `oc_order_status`.`order_status_id` = 9 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '2,6,13,14' WHERE `oc_order_status`.`order_status_id` = 16 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '2,6,13,14' WHERE `oc_order_status`.`order_status_id` = 16 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '1,2,4,6,8,9,11,12,13,14,15,16,17' WHERE `oc_order_status`.`order_status_id` = 6 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '1,2,4,6,8,9,11,12,13,14,15,16,17' WHERE `oc_order_status`.`order_status_id` = 6 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '4,8,15,17' WHERE `oc_order_status`.`order_status_id` = 13 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '4,8,15,17' WHERE `oc_order_status`.`order_status_id` = 13 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '4,8,15,17' WHERE `oc_order_status`.`order_status_id` = 14 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '4,8,15,17' WHERE `oc_order_status`.`order_status_id` = 14 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '8,15,17' WHERE `oc_order_status`.`order_status_id` = 4 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '8,15,17' WHERE `oc_order_status`.`order_status_id` = 4 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '4,8,15' WHERE `oc_order_status`.`order_status_id` = 17 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '4,8,15' WHERE `oc_order_status`.`order_status_id` = 17 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '5' WHERE `oc_order_status`.`order_status_id` = 15 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '5' WHERE `oc_order_status`.`order_status_id` = 15 AND `oc_order_status`.`language_id` = 2;

UPDATE `oc_order_status` SET `post_actions` = '1,9,16' WHERE `oc_order_status`.`order_status_id` = 2 AND `oc_order_status`.`language_id` = 1;

UPDATE `oc_order_status` SET `post_actions` = '1,9,16' WHERE `oc_order_status`.`order_status_id` = 2 AND `oc_order_status`.`language_id` = 2;







