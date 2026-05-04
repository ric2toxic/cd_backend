/////////////////////////////////// VERSION 1.5 //////////////////////////////


ALTER TABLE `oc_return_shipment_tracking` ADD `order_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `date_added`;


ALTER TABLE `oc_return_shipment_backto_customer` ADD `order_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `date_added`;


ALTER TABLE `oc_return_shipment_tracking` ADD `master_return_ids` VARCHAR(120) NULL DEFAULT NULL AFTER `shipping_id`;

ALTER TABLE `oc_return_shipment_backto_customer` ADD `return_ids` VARCHAR(120) NULL DEFAULT NULL AFTER `shipping_id`;



INSERT INTO `oc_return_action` (`return_action_id`, `language_id`, `name`, `class_name`, `post_actions`, `status`) VALUES (NULL, '1', 'Manually Generated Reverse Shipment', 'ActionManuallyGeneratedReverseShipment', NULL, '1');



ALTER TABLE `oc_return` ADD `relisted_product_id` INT(10) NOT NULL DEFAULT '0' AFTER `credit_note_id`;



UPDATE `oc_return_action` SET `status` = '1' WHERE `oc_return_action`.`return_action_id` = 135 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '102,104,103,107,105,135' WHERE `oc_return_action`.`return_action_id` = 101 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '104,105,109,110,111,139,135' WHERE `oc_return_action`.`return_action_id` = 102 AND `oc_return_action`.`language_id` = 1;

UPDATE `oc_return_action` SET `post_actions` = '105,109,110,111,139,135' WHERE `oc_return_action`.`return_action_id` = 103 AND `oc_return_action`.`language_id` = 1;



ALTER TABLE `oc_admin_change_log` CHANGE `source_field` `source_field` ENUM('product_edit','break_suborder','product_list','not_applicable','category_edit','cron/disableWrongPricedGarmentProductPerGST','edit_customer','update_seller_invoice','update_debit_note','pickup_edit','pickup_add','seller_assign','pickup_delete','seller_assign_delete','seller_invoice_verification','return_product') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'not_applicable';


ALTER TABLE `oc_admin_change_log` CHANGE `field_name` `field_name` ENUM('price','quantity','seller_tax','commission','tax_class_id','weight','title','description','model','sku','piece_in_set','set_description','store_sales','custom_rating','seller_change','only_for_search','hsn_code','options','sort_order','stock_status_id','status','shipping','date_available','subtract','minimum','expected_dispatch_date','is_single','mrp','cod_available','is_archived','shipping_collected','cod_failed_penalty','email','telephone','non_returnable','gst_number','customer_group_id','firstname','lastname','fax','password','confirm','newsletter','approved','safe','is_dropshipper','show_full_sku','should_download','country_code','exclusive','expire_exclusive_date','credit_status','franchise_status','wsb_credit_card_payment','franchise_coupon','franchise_discount','franchise_prefix','bank_ac_holder_name','bank_ac_number','ifsc_code','ws_access_token','invoice_physically_received_and_comment','debit_note_received_and_comment','sync_to_solr','seller_id','is_franchise','product_rating','seller_invoice_no','wsb_books_relisted') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

