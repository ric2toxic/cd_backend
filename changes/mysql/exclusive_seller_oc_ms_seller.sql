ALTER TABLE `oc_ms_seller` ADD `exclusive` ENUM('normal','exclusive','both') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'normal' AFTER `app_version_code`;


/*
* Also add seller_list in oc_admin_change_log in source_field enum field
*/