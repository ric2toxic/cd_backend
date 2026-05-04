ALTER TABLE `oc_customer` 
ADD COLUMN `text_msg_permit` TINYINT(1) NOT NULL DEFAULT '1' AFTER `wsb_credit_card_payment`;


