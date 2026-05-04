ALTER TABLE `oc_customer` ADD `is_franchise` TINYINT(1) NULL DEFAULT NULL , ADD `franchise_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_franchise`;
ALTER TABLE `oc_product`  ADD `franchise_id` INT(11) NULL DEFAULT NULL ;
ALTER TABLE `oc_order` ADD `franchise_id` INT(11) NULL DEFAULT NULL ;
ALTER TABLE `oc_order_product` ADD `franchise_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `oc_admin_product_change_log` CHANGE `field_name` `field_name` ENUM('price','quantity','seller_tax','commission','tax_class_id','weight','title','description','model','sku','piece_in_set','set_description','store_sales','exclusive','custom_rating','seller_change','only_for_search','hsn_code','options','sort_order','stock_status_id','status','shipping','date_available','subtract','minimum','expected_dispatch_date','is_single','mrp','cod_available','is_archived','franchise_id') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
