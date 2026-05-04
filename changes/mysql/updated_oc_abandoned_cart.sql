ALTER TABLE `oc_abandoned_cart` ADD `user_ip` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `cart` ,
ADD `user_os` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `user_ip` ,
ADD `user_browser` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `user_os` ;
ALTER TABLE `oc_abandoned_cart` ADD `mail_sent` TINYINT( 1 ) NOT NULL AFTER `user_browser` ;
