ALTER TABLE `oc_customer_signups` CHANGE `otp_page` `otp_page` ENUM('sign_up','forgot_password','update_profile') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'sign_up';
