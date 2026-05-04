ALTER TABLE `oc_payment_sub_incomescr` ADD `ref_id` INT(11) NOT NULL AFTER `delete_status`, ADD `ref_tablename` ENUM('not_applicable','oc_seller_invoice','oc_seller_debit_note','oc_wsb_purchase','oc_trxn_details','oc_wsb_sor_payment') NOT NULL DEFAULT 'not_applicable' AFTER `ref_id`;


ALTER TABLE `oc_payment_sub_csv` ADD `ref_id` INT(11) NOT NULL AFTER `delete_status`, ADD `ref_tablename` ENUM('not_applicable','oc_seller_invoice','oc_seller_debit_note','oc_wsb_purchase','oc_trxn_details','oc_wsb_sor_payment') NOT NULL DEFAULT 'not_applicable' AFTER `ref_id`

