ALTER TABLE `oc_customer_signups` CHANGE `otp_page` `otp_page` ENUM('sign_up','forgot_password','update_profile','bank_update') NOT NULL DEFAULT 'sign_up';
