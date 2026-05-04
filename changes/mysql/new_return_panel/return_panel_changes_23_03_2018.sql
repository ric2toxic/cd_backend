///////////////////////////////////CRON TO BE ADDED///////////////////////////
sendAlertForPendingSelfShipment(), sendAlertForPendingGoodsReceived()
////////////////////////////////////////////////////////////////////////////





**********************************Queries To be Executed****************************

ALTER TABLE `oc_courier_partners` ADD `is_reverse_shipment` TINYINT(1) NOT NULL DEFAULT '0' AFTER `tracking_url`;


ALTER TABLE `oc_return_shipment_tracking` ADD `warehouse_id` INT(11) NULL AFTER `vendor_code`;


UPDATE oc_return_shipment_tracking orst INNER JOIN oc_warehouse_address as wa ON wa.nuvoex_vendor_code = orst.vendor_code SET orst.warehouse_id = wa.warehouse_id;



ALTER TABLE `oc_wsb_prefixes` CHANGE `prefix_type` `prefix_type` ENUM('DEBIT_NOTE','CREDIT_NOTE','SELLER_INVOICE','BUYER_INVOICE','FRANCHISE_INVOICE','RETURN_NO') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

INSERT INTO `oc_wsb_prefixes` (`prefix_id`, `gstin`, `prefix`, `prefix_type`, `financial_year`, `available_no`, `status`, `date_added`) VALUES (NULL, 'DEFAULT', 'WSB-RTN-', 'RETURN_NO', '18', '0', '1', CURRENT_TIMESTAMP);	

//////////////////////////////////////////////////////////////////////////////////////
//Prefix be added manually to wsb_prefix: For generating return_no////////////////////
//////////////////////////////////////////////////////////////////////////////////////

ALTER TABLE `oc_return` CHANGE `return_shipment_tracking_id` `return_shipment_tracking_id` INT(11) NULL DEFAULT '0';

INSERT INTO `oc_courier_partners` (`id`, `courier_name`, `courier_telephone`, `tracking_url`, `is_reverse_shipment`,`is_forward_shipment`) VALUES (NULL, 'ShadowFax', NULL, NULL, 1,0)

UPDATE `oc_courier_partners` SET `is_reverse_shipment` = '1' WHERE `oc_courier_partners`.`id` = 2;
UPDATE `oc_courier_partners` SET `is_reverse_shipment` = '1' WHERE `oc_courier_partners`.`id` = 11;


UPDATE `oc_return` SET `return_shipment_tracking_id` = 0
WHERE `return_shipment_tracking_id` IS NULL;


ALTER TABLE `oc_credit_note` ADD `reversal_shipping` FLOAT(10,2) NOT NULL DEFAULT '0.00' AFTER `shipping_collected`, ADD `other_charges` FLOAT(10,2) NOT NULL DEFAULT '0.00' AFTER `reversal_shipping`;

//////////////////UPDATE prefixes table for credit note-- financial_year, available_no


ALTER TABLE `oc_admin_change_log` CHANGE `field_name` `field_name` ENUM('price','quantity','seller_tax','commission','tax_class_id','weight','title','description','model','sku','piece_in_set','set_description','store_sales','custom_rating','seller_change','only_for_search','hsn_code','options','sort_order','stock_status_id','status','shipping','date_available','subtract','minimum','expected_dispatch_date','is_single','mrp','cod_available','is_archived','shipping_collected','cod_failed_penalty','email','telephone','non_returnable','gst_number','customer_group_id','firstname','lastname','fax','password','confirm','newsletter','approved','safe','is_dropshipper','show_full_sku','should_download','country_code','exclusive','expire_exclusive_date','credit_status','franchise_status','wsb_credit_card_payment','franchise_coupon','franchise_discount','franchise_prefix','bank_ac_holder_name','bank_ac_number','ifsc_code','ws_access_token','reversal_shipping','other_charges') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


ALTER TABLE `oc_return_reason` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `reason_type`;
UPDATE `oc_return_reason` SET `status` = '0' WHERE `return_reason_id` IN (8, 14);

INSERT INTO `oc_return_reason` (`return_reason_id`, `language_id`, `name`, `reason_type`, `status`) VALUES ('16', '1', 'Other, please supply details', 'REPLACEMENT', '1');




UPDATE `oc_return` SET `return_action_id` = 101 WHERE `return_action_id` = 0;




UPDATE `oc_return` SET `return_action_id` = 103 WHERE `return_action_id` =1 AND `return_reason_id` IN (1,13,16);
UPDATE `oc_return` SET `return_action_id` = 102 WHERE `return_action_id` =1 AND `return_reason_id` NOT IN (1,13,16);

UPDATE `oc_return` SET `return_action_id` = 109 WHERE `return_action_id` =2;

UPDATE `oc_return` SET `return_action_id` = 116 WHERE `return_action_id` =3;

UPDATE `oc_return` SET `return_action_id` = 116 WHERE `return_action_id` =4;

UPDATE `oc_return` SET `return_action_id` = 107 WHERE `return_action_id` =7 AND `return_reason_id` IN (1,13,16);
UPDATE `oc_return` SET `return_action_id` = 104 WHERE `return_action_id` =7 AND `return_reason_id` NOT IN (1,13,16);

UPDATE `oc_return` SET `return_action_id` = 108 WHERE `return_action_id` =8;

UPDATE `oc_return` SET `return_action_id` = 130 WHERE `return_action_id` =9;

ALTER TABLE `oc_credit_note` ADD `return_ids` TEXT NULL DEFAULT NULL AFTER `credit_note_id`;


ALTER TABLE `oc_warehouse_address` ADD `city_code` ENUM('NO', 'DL', 'JP', 'ST', 'KL', 'MU', 'BL') NOT NULL DEFAULT 'NO' AFTER `city`;
ALTER TABLE `oc_warehouse_address` ADD `sort_order` INT(3) NOT NULL DEFAULT '0' AFTER `city_code`;
UPDATE `oc_warehouse_address` SET `city_code` = 'JP' WHERE `oc_warehouse_address`.`warehouse_id` = 1;
UPDATE `oc_warehouse_address` SET `sort_order` = '1' WHERE `oc_warehouse_address`.`warehouse_id` = 11;
UPDATE `oc_warehouse_address` SET `city_code` = 'ST' WHERE `oc_warehouse_address`.`warehouse_id` = 6;
UPDATE `oc_warehouse_address` SET `city_code` = 'DL' WHERE `oc_warehouse_address`.`warehouse_id` = 7;
UPDATE `oc_warehouse_address` SET `city_code` = 'MU' WHERE `oc_warehouse_address`.`warehouse_id` = 8;
UPDATE `oc_warehouse_address` SET `city_code` = 'KL' WHERE `oc_warehouse_address`.`warehouse_id` = 9;
UPDATE `oc_warehouse_address` SET `city_code` = 'BL' WHERE `oc_warehouse_address`.`warehouse_id` = 10;

UPDATE `oc_warehouse_address` SET `city_code` = 'DL' WHERE `oc_warehouse_address`.`warehouse_id` = 11;


UPDATE `oc_courier_partners` SET `tracking_url` = 'https://www.fedex.com/apps/fedextrack/index.html?action=track&locale=en_US&cntry_code=en&tracknumbers=' WHERE `oc_courier_partners`.`id` = 1;

UPDATE `oc_courier_partners` SET `tracking_url` = 'https://track.shadowfax.in/track?order=return&trackingId=' WHERE `oc_courier_partners`.`id` = 13;


ALTER TABLE `oc_courier_partners` ADD `is_forward_shipment` TINYINT(1) NOT NULL DEFAULT '1' AFTER `is_reverse_shipment`;
UPDATE `oc_courier_partners` SET `is_forward_shipment`= 0 WHERE id IN (6,7,8,9,10,13)



ALTER TABLE `oc_return` ADD `return_shipment_backto_customer_id` INT(11) NULL DEFAULT '0' AFTER `return_shipment_tracking_id`;


ALTER TABLE `oc_order` CHANGE `excess_payment_cleared` `payment_cleared` ENUM('YES','NO','PENDING_APPROVAL','NOT_APPLICABLE') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'NO';

ALTER TABLE `oc_credit_note` CHANGE `net_refunable` `net_refundable` DECIMAL(10,2) NULL DEFAULT NULL;



"Remove Hard coded Tracking ID from BlueDart tracking API"

