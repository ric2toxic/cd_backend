ALTER TABLE `oc_product` ADD `hidden_selling_price` DECIMAL(15,4) NULL DEFAULT NULL AFTER `is_associate`;

/*
* Also add hidden_selling_price in oc_admin_change_log in field_name column
*/