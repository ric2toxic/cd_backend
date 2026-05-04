ALTER TABLE `oc_banner_image` CHANGE `type` `type` ENUM('link','search','category','sale') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'link';
