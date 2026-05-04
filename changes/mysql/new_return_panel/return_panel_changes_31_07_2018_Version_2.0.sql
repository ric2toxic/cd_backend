/////////////////////////////////// VERSION 2.0 //////////////////////////////

ALTER TABLE `oc_return` CHANGE `credit_note_id` `credit_note_id` INT(11) NULL DEFAULT NULL;

ALTER TABLE `oc_return` CHANGE `debit_note_id` `debit_note_id` INT(11) NULL DEFAULT NULL;

UPDATE `oc_return` SET `credit_note_id` = NULL WHERE `credit_note_id` = 0

UPDATE `oc_return` SET `debit_note_id` = NULL WHERE `debit_note_id` = 0



