<?php
// Heading
$_['heading_title']         = 'Customer Credit Details';

// Text
$_['text_success']          = 'Success: You have modified customers!';
$_['text_list']             = 'Customer List';
$_['text_add']              = 'Add Customer';
$_['text_edit']             = 'Edit Customer';
$_['text_default']          = 'Default';
$_['text_balance']          = 'Balance';
$_['text_add_ban_ip']       = 'Add Ban IP';
$_['text_remove_ban_ip']    = 'Remove Ban IP';
$_['text_panding']		    = 'Pending';
$_['text_block']		    = 'Block';
$_['text_select']			= '---Select---';
$_['text_active']			= 'Active';
$_['text_cashback_total']   = 'Total Cashback Offered';
$_['text_cashback_utilized']= 'Cashback Used';
$_['text_cashback_expired'] = 'Cashback Expired';
$_['text_cashback_available'] = 'Cashback Available';


// Column
$_['column_name']           = 'Customer Name';
$_['column_email']          = 'E-Mail';
$_['column_telephone']      = 'Telephone';
$_['column_customer_group'] = 'Customer Group';
$_['column_status']         = 'Status';
$_['column_date_added']     = 'Date Added';
$_['column_comment']        = 'Comment';
$_['column_description']    = 'Description';
$_['column_amount']         = 'Amount';
$_['column_points']         = 'Points';
$_['column_ip']             = 'IP';
$_['column_total']          = 'Total Accounts';
$_['column_action']         = 'Action';
$_['column_last_login']     = 'Last Login';
$_['column_cart_items']     = 'Cart Items';
$_['column_last_cart_modified']  = 'Cart Modified';
$_['column_amount_utilized']     = 'Amount Utilized';
$_['column_expired']             = 'Validity';
$_['column_buildup_time'] 		 = 'Build Up Time';

// Entry
$_['entry_customer_group']  = 'Customer Group';
$_['entry_firstname']       = 'First Name';
$_['entry_lastname']        = 'Last Name';
$_['entry_email']           = 'E-Mail';
$_['country_code']           = 'Country Code';

$_['entry_telephone']       = 'Telephone';
$_['entry_gst_number']      = 'GST Number';
$_['entry_fax']             = 'Fax';
$_['entry_newsletter']      = 'Newsletter';
$_['entry_status']          = 'Status';
$_['entry_approved']        = 'Approved';
$_['entry_safe']            = 'Safe';
$_['entry_password']        = 'Password';
$_['entry_confirm']         = 'Confirm';
$_['entry_company']         = 'Company';
$_['entry_address_1']       = 'Address 1';
$_['entry_address_2']       = 'Address 2';
$_['entry_city']            = 'City';
$_['entry_postcode']        = 'Postcode';
$_['entry_country']         = 'Country';
$_['entry_zone']            = 'Region / State';
$_['entry_default']         = 'Default Address';
$_['entry_comment']         = 'Comment';
$_['entry_description']     = 'Description';
$_['entry_amount']          = 'Amount';
$_['entry_points']          = 'Points';
$_['entry_name']            = 'Customer Name';
$_['entry_customer_id']     = 'Customer Id';
$_['entry_master_id']       = 'Master Id';
$_['entry_ip']              = 'IP';
$_['entry_date_added']      = 'Date Added';
$_['entry_dropshipper']		= 'Dropshipper';
$_['entry_quality_expectation'] = 'Quality Expectation';
$_['entry_categories']      = 'Categories';
$_['entry_filters']         = 'Filters';
$_['entry_price_filter']	= 'Price Filter';
$_['entry_price_min']		= 'Min. Price';
$_['entry_price_max']		= 'Max. Price';
$_['entry_country_code']		= 'Country Code';
$_['entry_expire_exclusive_date'] = 'Expiry Exclusive Date';
$_['entry_customer_type']   = 'Customer Type';
$_['entry_franchise_coupon'] = 'Franchise Coupon';
$_['entry_franchise_discount'] = 'Franchise Discount';
$_['entry_franchise_prefix'] = 'Franchise Prefix';

$_['entry_bank_ac_holder_name'] = 'A/C Holder Name';
$_['entry_bank_ac_number']      = 'A/C Number';
$_['entry_ifsc_code']           = 'IFSC Code';
$_['entry_bank_details_verified'] = 'Bank Details Verified';
$_['entry_enable_credit_card_payment'] = 'Enable Wholesalebox + ICICI Credit Card Payment Method';
$_['entry_enable_wsb_credit_payment']  = 'WSB Credit Payment Status';
$_['entry_wsb_credit_payment_limit']   = 'WSB Credit Payment Limit';
$_['entry_nach_schedule_crontab']      = 'NACH Schedule Crontab';
$_['entry_days_before_nach_start']     = 'Days After NACH Starts';
$_['entry_schedule_days_for_nach']     = 'Total Days to complete NACH';
$_['entry_auto_nach_enabled']          = 'Auto NACH Enabled';
$_['entry_comment']                    = 'Comment';

// Help
$_['help_safe']             = 'Set to true to avoid this customer from being caught by the anti-fraud system';
$_['help_points']           = 'Use minus to remove points';

// Error
$_['error_warning']         = 'Warning: Please check the form carefully for errors!';
$_['error_permission']      = 'Warning: You do not have permission to modify customers!';
$_['error_exists']          = 'Warning: E-Mail Address is already registered!';
$_['error_exists_gst']      = 'Warning: <div style="display: block; font-size: 12px;"><p>GST No <strong>%s</strong> is already linked against a different buyer account using %s, cid: %s.</p></div>';//'Warning: GST number is already registered!';
$_['error_firstname']       = 'First Name must be between 1 and 32 characters!';
$_['error_lastname']        = 'Last Name must be between 1 and 32 characters!';
$_['error_email']           = 'E-Mail Address does not appear to be valid!';
$_['error_telephone']       = 'Telephone must be between 3 and 32 characters!';
$_['error_gst_number']      = 'Invalid gst number!';
$_['error_password']        = 'Password must be between 4 and 20 characters!';
$_['error_confirm']         = 'Password and password confirmation do not match!';
$_['error_address_1']       = 'Address 1 must be between 3 and 128 characters!';
$_['error_city']            = 'City must be between 2 and 128 characters!';
$_['error_postcode']        = 'Postcode must be between 2 and 10 characters for this country!';
$_['error_country']         = 'Please select a country!';
$_['error_zone']            = 'Please select a region / state!';
$_['error_custom_field']    = '%s required!';
$_['error_comment']         = 'You must enter a comment!';
$_['error_neogrowth_registration_number'] = 'Customer\'s neogrowth registration number should not be empty, if credit status is enabled';
$_['error_neogrowth_account_number'] = 'Customer\'s neogrowth account number should not be empty, if credit status is enabled';
$_['error_gst_checksum']       = 'Invalid GST Number! Do you mean %s instead? Please check and enter correct GST number again!';
$_['error_gst_no_mismatch']    = 'Invoice cannot be generated because GST number %s belongs to %s; while Payment address is of %s state. Please fix either one of them.';


/***************/
$_['text_store_list'] = 'Store Lists';
$_['text_select_store_list'] = '--Select All--';
$_['tab_preferences'] = 'Preferences';
$_['tab_franchise'] = 'Franchise Data';
$_['tab_bank_details'] = 'Bank Details';
$_['has_website'] = 'Has Website';
