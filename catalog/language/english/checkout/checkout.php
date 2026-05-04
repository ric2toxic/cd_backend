<?php
// Heading
$_['heading_title']                  = 'Checkout';

// Text
$_['text_cart']                      = 'Shopping Cart';
$_['text_checkout_option']           = 'Checkout Options';
$_['text_checkout_account']          = 'Account &amp; Billing Details';

$_['text_modify']                    = 'Modify &raquo;';
$_['text_new_customer']              = 'New Customer';
$_['text_returning_customer']        = 'Already registered ? ';
$_['text_checkout']                  = 'Checkout Options:';
$_['text_i_am_returning_customer']   = 'I am a returning customer';
$_['text_click_here_to_login']       = 'Click here to login';
$_['text_register']                  = 'Register Account';
$_['text_guest']                     = 'Guest Checkout';
$_['text_register_account']          = 'By creating an account you will be able to shop faster, be up to date on an order\'s status, and keep track of the orders you have previously made.';
$_['text_forgotten']                 = 'Forgot your password ?';
$_['text_your_details']              = 'Your Personal Details';
$_['text_your_address']              = 'Your Firm\'s Billing Address';
$_['text_your_password']             = 'Your Password';
$_['text_agree']                     = 'I have read and agree to the <a href="%s" class="agree"><b>%s</b></a>';
$_['text_address_new']               = 'Add New Address';
$_['text_address_existing']          = 'Use Saved Address';
$_['text_shipping_method']           = 'Please select the preferred shipping method to use on this order.';
$_['text_payment_method']            = 'Please select the preferred payment method to use on this order.';
$_['text_comments']                  = 'Add Comments About Your Order';
$_['text_recurring_item']            = 'Recurring Item';
$_['text_payment_recurring']         = 'Payment Profile';
$_['text_trial_description']         = '%s every %d %s(s) for %d payment(s) then';
$_['text_payment_description']       = '%s every %d %s(s) for %d payment(s)';
$_['text_payment_cancel']            = '%s every %d %s(s) until canceled';
$_['text_day']                       = 'day';
$_['text_week']                      = 'week';
$_['text_semi_month']                = 'half-month';
$_['text_month']                     = 'month';
$_['text_year']                      = 'year';
$_['text_cst']                       = 'CST (2%)';
$_['text_tax_refund']                = 'Refundable amount on Form C submission';
$_['text_will_deliver']              = ' (We will deliver your order here)';
$_['text_product_code']              = 'Product Code: ';
$_['text_subtotal']                  = 'Sub-Total';
$_['text_tax']                       = 'Total Tax';
$_['text_total_amount']              = 'Total Amount Payable';
$_['text_discount']                  = 'Get aditional 2% discount on prepaid orders.';
$_['text_total_qty']                 = 'Total Qty.';

$_['text_ship_same_address']         = 'Ship to same address';

// Column
$_['column_image']                   = 'Image';
$_['column_name']                    = 'Product';
$_['column_model']                   = 'Model';
$_['column_quantity']                = 'Sets';
$_['piece_per_set']                  = 'Piece Per Set';
$_['column_price']                   = 'Price';
$_['column_total']                   = 'Amount (Ex. Tax)';
$_['column_tax']                     = 'Tax';
$_['column_inc_tax']                 = ' (Inc. Taxes)';
$_['column_per_piece']               = ' &nbsp;/ Piece';
$_['column_per_set']                 = ' &nbsp;/ Set';
$_['pieces']                         = 'Pieces';

// Entry
$_['entry_email_address']            = 'E-Mail Address';
$_['entry_email']                    = 'E-Mail';
$_['entry_mobile_or']                = '&nbsp;OR Mobile';
$_['entry_password']                 = 'Password';
$_['entry_confirm']                  = 'Confirm Password';
$_['entry_firstname']                = 'Full Name Of Owner';
$_['entry_lastname']                 = 'Last Name';
$_['entry_telephone']                = 'Mobile';
$_['entry_fax']                      = 'Fax';
$_['entry_address']                  = 'Choose Address';
$_['entry_company']                  = 'Business Name';
$_['entry_customer_group']           = 'Customer Group';
$_['entry_address_1']                = 'Address Line 1';
$_['entry_address_2']                = 'Address Line 2';
$_['entry_postcode']                 = 'Post Code';
$_['entry_city']                     = 'City';
$_['entry_country']                  = 'Country';
$_['entry_zone']                     = 'Region / State';
$_['entry_newsletter']               = 'I wish to subscribe to the %s newsletter.';
$_['entry_shipping'] 	             = 'My delivery and billing addresses are the same.';
$_['show_pass'] 	                 = 'Show Password';
$_['entry_referred']                 = 'Referred By';
$_['firstname_entry']                = 'First Name';


// Error
$_['error_warning']                  = 'There was a problem while trying to process your order! If the problem persists please try selecting a different payment method or you can contact the store owner by <a href="%s">clicking here</a>.';
$_['error_login']                    = 'Warning: No match for E-Mail Address and/or Password.';
$_['error_attempts']                 = 'Warning: Your account has exceeded allowed number of login attempts. Please try again in 1 hour.';
$_['error_approved']                 = 'Warning: Your account requires approval before you can login.';
//$_['error_exists']                   = 'Warning: E-Mail Address is already registered!'.' <a href="index.php?route=checkout/checkout#forgot_password_show" class="forgot-redirect">Forget Password</a>.';
//$_['error_exists_telephone']         = 'Warning: Mobile Number is already registered!'.' <a href="index.php?route=checkout/checkout#forgot_password_show" class="forgot-redirect">Forget Password</a>.';
$_['error_exists']                   = 'Warning: E-Mail Address is already registered!'.' <a href="index.php?route=account/forgotten&popup=true" class="forgot-redirect fancybox fancybox.iframe ellipsis">Forget Password</a>.';
$_['error_exists_telephone']         = 'Warning: Mobile Number is already registered!'.' <a href="index.php?route=account/forgotten&popup=true" class="forgot-redirect fancybox fancybox.iframe ellipsis">Forget Password</a>.';
$_['error_firstname']                = 'Please provide your first name.';
$_['error_lastname']                 = 'Last Name must be between 1 and 32 characters!';
$_['error_email']                    = 'E-Mail address does not appear to be valid!';
$_['error_password']                 = 'Password must be between 3 and 20 characters!';
$_['error_confirm']                  = 'Password confirmation does not match password!';
$_['error_address_1']                = 'Address 1 must be between 3 and 128 characters!';
$_['error_city']                     = 'City must be between 2 and 128 characters!';
$_['error_country']                  = 'Please select a country!';
$_['error_zone']                     = 'Please select a region / state!';
$_['error_agree']                    = 'Warning: You must agree to the %s!';
$_['error_address']                  = 'Warning: You must select address!';
$_['error_address_payment']          = 'Warning: You must select payment address!<br/>If you are a dropshipper, then add a default address in your address book.<p>For any assistance, call/whatsapp: +91-%s</p>';
$_['error_shipping']                 = 'Warning: Shipping method required!';

$_['error_payment']                  = 'Warning: Payment method required!';
$_['error_no_payment']               = 'Warning: No Payment options are available. Please <a href="%s">contact us</a> for assistance!';
$_['error_custom_field']             = '%s required!';
$_['error_minimum_order_amount_validation'] = 'Minimum cart value %s For singles store. No returns are allowed for single store items.';



//---------------------------------------------------------------------------------------------------------------------------

//For Forget Password
// Text
$_['heading_text_forget_password']  = 'Forgot Your Password?';
$_['text_account']                  = 'Account';
$_['text_forgotten']                = 'Forgot your password ?';
$_['text_your_email']               = 'Your E-Mail Address';
$_['text_email']                    = 'Enter the e-mail address associated with your account. Click submit to have your password e-mailed to you.';
$_['text_success']                  = 'Success: A new password has been sent to your e-mail address.';
$_['text_mobile_success']           = 'Success: A new password has been sent on your mobile.';

// Entry
$_['entry_email']                   = 'E-Mail Address or Mobile Number';

// Error
$_['forgot_error_email']            = 'Warning: The E-Mail Address OR Mobile Number was not found in our records, please try again!';



//---------------------------------------------------------------------------------------------------------------------------


//buttons

$_['button_back']                   = 'Back';

$_['order_summary']                 = 'Order Summary';

//mobile
$_['price_details']                 = 'Price Details';
$_['steps_one']                     = 'Step 1/4';
$_['steps_two']                     = 'Step 2/4';
$_['steps_three']                   = 'Step 3/4';


//---------------------------------------------------------------------------------------------------------------------------


//confirm text column table  by vikas (28-03-2016)

$_['text_number_of_pieces']  =   'No. of Pieces';
$_['text_price_per_piece']   =   'Price per piece';
$_['text_price_per_set']     =   'Price per set';
$_['text_total']             =   'Total';
$_['text_tax']               =   'Tax';
$_['text_set_description']   =   'Set Description';


//---------------------------------------------------------------------------------------------------------------------------

$_['error_special_character']   =   'special character not allowed !';
$_['error_numeric']   =   'Numeric not allowed !';


//promo code
$_['text_promo']  = 'Have a Promo Code? ';
$_['text_place_promo']  = 'Promo Code';

$_['text_shipping'] = "Shipping Charge";

$_['text_remove_button'] = 'REMOVE';
$_['text_move_to_wishlist'] = 'ADD TO SHORTLIST';
$_['text_comment'] = 'COMMENT';
$_['text_out_of_stock_alert'] = "This item won't be included in your order!";

$_['want_design_message'] = 'Thank you ! We have received your message';
$_['comment_popup_heading'] = "<p>We shall try our best to get this design from manufacturer, if it is available.</p>
                                <p>Please specify how many pieces and what sizes/colors you like to have:</p>
                                ";
$_['comment_popup_send'] = 'Send';
$_['i_want_this_design'] = 'I want this design';

$_['text_pickup_city'] = 'Shipment From ';
$_['text_estimate_shipping'] = 'ESTIMATE SHIPPING';
$_['button_clear_cart'] = 'Clear Cart';
$_['text_non_returnable'] = 'This item is non-returnable';

$_['text_cform_submit_1']      = '(You should have valid CST Number)';
$_['text_cform']			   = 'Check if you will be submitting Form C, then you will get refund of ';
$_['text_remove']              = 'Success: You have modified your shopping cart!';
$_['text_previously_ordered'] = 'Previously Ordered';
$_['text_out_of_stock']          = 'Out of stock';
$_['error_cart_minimum']       = 'For buying at factory prices, Minimum Order amount (Subtotal) required at WholesaleBox is %s !';

$_['text_store_pickup']  = 'Immediate pickup from Store';
$_['error_wrong_store'] = 'Wrong Store Inventory. Please remove this and add Correct one.';

$_['text_out_of_stock_review'] = ' item(s), marked in red background are OUT OF STOCK.';
$_['text_quantity_reduced_review'] = ' item(s), marked with blue background has updated quantity.';
$_['text_review'] = 'Please review!';
$_['text_success_alert'] = ' <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span> <strong>Success</strong>
            <hr class="message-inner-separator">
            <p>You have modified your shopping cart!</p>
        </div>';
$_['text_want_design_sucess'] = '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p><strong>Thank you!</strong> We have received your message.</p></div>';
$_['text_add_wishlist'] = '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Product added to your shortlist.</p></div>';
$_['text_address_update'] = '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Addresses updated successfully.</p></div>';
$_['text_select_address'] = 'Please select the address where you want products to be delivered.';

$_['text_select_billing_address'] = 'Please select the billing address, It will be used in invoice.';







//------------------------------mahaveer---------------------

$_['text_checkout_payment_address'] = 'Billing Address';
$_['text_checkout_shipping_address']= 'Delivery Address';
$_['text_checkout_shipping_method'] = 'Shipping Method';
$_['text_checkout_payment_method']  = 'Payment Method';
$_['text_order_summary']            = 'ORDER SUMMARY';
$_['text_shipping_charge']          = 'Shipping Charge';
$_['text_checkout_gst']             = 'GST';
$_['text_gst_already']              = 'GST Number: ';
$_['text_preferred_shipping']       = 'Please select preferred Shipping method';
$_['text_shipping_estimate']        = 'Shipping Estimate for Pincode';
$_['text_shipping_mode']            = 'SHIPPING MODE';
$_['text_amount']                   = 'AMOUNT';
$_['text_cancel']                   = 'Cancel';
$_['text_discount_2']               = 'Get 2% Discount ! for Online Payment';
$_['payment_debit_card']            = 'Pay using Debit Card/Credit Card/Net Banking';
$_['text_amount_pay']               = 'Amount Payable';
$_['text_pay_now']                  = 'PAY NOW';
$_['text_upi_option']               = 'Option 2: Pay using UPI id';
$_['text_upi_id']                   = 'Wholesalebox UPI Id';
$_['text_upi_using']                = 'to know "How to pay using UPI id?"';
$_['text_click_here']               = 'Click here';
$_['entry_upi_id']                  = 'Enter your UPI Id';
$_['text_note']                     = 'Please note';
$_['text_checkout_confirm']         = 'Confirm Order';
$_['text_app_cashback_title']       = 'Extra 2% Cash Back on App';
$_['text_app_download']             = 'Download App from';
$_['text_wsb_offer']                = 'Wholesalebox Offers';
$_['text_wsb_offer_line1']          = 'FREE website for your shop, get more clients';
$_['text_wsb_offer_line2']          = 'Join Membership to avail up to 6% discounts, call us for details';
$_['text_wsb_offer_line3']          = 'After completion of the order';
$_['text_checkout_chat']            = 'Chat';
$_['text_checkout_call_us']         = 'Call Us';
$_['text_edit_address']             = 'Edit Your Address';
$_['text_new_address']              = 'Add New Address';
$_['text_add']                      = 'Add';
$_['text_select_country']           = 'Select Your Country';
$_['text_select_state']             = 'Select Your State';
$_['text_call_me']                  = 'Call Me';
$_['text_call_request']             = 'Call Back Request';
$_['text_whatsapp']                 = 'Whatsapp';
$_['text_contact']                  = 'Contact';
$_['text_for_help']                 = 'For Help';
$_['text_details_title']            = 'Please provide following details';
$_['text_apply']                    = 'Apply';
$_['text_applied']                  = 'Applied';
$_['text_promo']                    = 'Promo';
$_['text_gst_option_yes']           = 'Yes';
$_['text_gst_option_no']            = 'I have registered GST Number.';
$_['text_please_wait']              = 'please wait...';
$_['text_lazypay_eligibility_msg']  = 'We shall ship your order after payment confirmation. Click on confirm order button to complete the transaction!';
$_['text_lazypay_features']         = 'Top features of LazyPay - Pay Later';
$_['text_lazypay_features_1']       = 'Zero Cost Credit(0% interest)';
$_['text_lazypay_features_2']       = 'No registration needed';
$_['text_lazypay_features_3']       = 'Place your order with just an OTP';
$_['text_lazypay_features_4']       = 'Pay us back within the due date – we will remind you';

$_['text_credit_limit']              = 'Your Credit limit is <span  class="Payable_Amount">Rs. %s</span>';
$_['text_rbl_limit']              	 = 'Your Rbl Credit limit is <span  class="Payable_Amount">&#8377; %s</span>';
$_['text_cod_note']                  = '<b>Please note:</b> We do not do 100% Cash On Delivery. 10% of the amount(or Rs. 1000 whichever is greater) has to be paid in advance and rest can be paid at the time of delivery.';
$_['text_cod_10_per']                ='10% of Total Amount: <span  class="Payable_Amount">Rs. %s</span> Pay using RTGS/NEFT/IMPS or Pay Online after Confirm-Order';
$_['text_cod_90_per']                ='90% of Total Amount : <span  class="Payable_Amount">Rs. %s</span> Cash On Delivery';
$_['text_cash_deposit']              ='Pay using Cash Deposit/Cheque/NEFT/RTGS';
$_['text_cash_deposit_option']       ='Option 1: Pay using Cash Deposit/Cheque/NEFT/RTGS';
$_['text_ship_order']               = 'We shall ship your order after payment confirmation';
$_['text_remain_amount']            = '<b>Please note:</b> Your order will not ship until we receive payment <span class="Payable_Amount">Rs. %s</span>'; 
$_['error_gst_unselect']            = '<span style="color:red;display:block;text-align: center;">Please select an option for GST.</span>';
$_['text_gst_help']                 = 'GST number is not mandatory. You can continue without providing GST number(or can remove current GST number and continue).';
$_['error_exists_gst']              = '<div style="display: block; font-size: 18px;"><p>GST No <strong>%s</strong> is already linked against a different buyer account using %s. If this another account is yours, please login using that account to enable GST billing.</p><p><a href="%s">Contact us</a> if the other account is not yours !!</p></div>';
$_['text_gst_declaration']          = 'I declare that I am not registered under GST  as non applicability of the law.';
$_['error_gst_checksum']            = '<span style="color:red;display:block;text-align: center;">Invalid GST Number!</span><div style="font-size:14px;display:block;text-align: center;"> Do you mean %s instead? Please check and enter correct GST number again!</div>';
$_['text_app_cashback_body']         = 'You can use this 2% cashback on your next order from Wholesalebox within 15 days after the delivery of an order.';
$_['text_custom_duty_charge']        = '<strong>Note: </strong>If there will be any custom duty on this order then it will be customer\'s responsibility to clear goods from custom. <b>For any help, please WhatsApp on +91 9116134795</b>';

$_['text_free_surface_shipping_1'] = 'YAY! You get FREE Surface Shipping when ordering above INR 15000';

$_['text_free_surface_shipping_2'] = 'Add more products to qualify for FREE SURFACE SHIPPING (INR 15000 or more)';
$_['text_free_surface_shipping_3'] = '*Free Shipping not available on Stocklots';

$_['text_rbl_limit_error']			 = '<span style="color:red;">For RBL Credit, order amount must be greater than %s </span>';

$_['button_back']            = 'Back';
$_['button_continue']        = 'Continue';
$_['button_delivery']        = 'DELIVER TO THIS ADDRESS';
$_['button_billing']         = 'BILLING TO THIS ADDRESS';
$_['button_prev']            = 'Previous';
$_['button_next']            = 'Next';
$_['button_ok']              = 'OK';
$_['button_close']           = 'Close';
$_['button_submit']          = 'Submit';


$_['entry_name']             = "Please Enter Your Name";
$_['entry_business']         = "Please Enter Business Name";
$_['entry_address_1']        = "Enter your address line 1";
$_['entry_address_2']        = "Enter your address line 2";
$_['entry_contact']          = "Enter your contact no";
$_['entry_pincode']          = "Enter your PINCODE";
$_['entry_city']             = "Enter your City";


$_['error_mobile']            = 'Please provide your mobile number';
$_['error_name']              = 'Please provide your name';
$_['error_company']           = 'Business Name must be between 2 and 64 characters!';
$_['error_address_1']         = 'Address must be between 3 and 128 characters!';
$_['error_address_2']         = 'Address must be less than 128 characters!';
$_['error_city']              = 'City must be between 2 and 32 characters!';
$_['error_country']           = 'Please select a country!';
$_['error_zone']              = 'Please select a region / state!';
$_['error_address_telephone'] = 'Contact no should contain only digits and should be less than 15 digits!';
$_['error_postcode']          = '*Please enter a valid 6 digit pincode';
$_['error_postcode_co']       = '*Pincode must be between 2 and 10 characters';
$_['error_telephone']           = '*Mobile number should be numeric!';
$_['error_telephone_max_limit'] = '*Mobile number should be less than 15 digits!';
$_['error_telephone_min_limit'] = 'Mobile number should contain at least 6 digits.';
$_['error_credit_limit']        =   'Dear Customer, Your Credit limit is zero! Please choose another payment method to place your order.';
$_['error_rbl_credit_limit']        =   'Dear Customer, Your Rbl Credit limit is zero! Please choose another payment method to place your order.';
$_['error_payment']             =   "Oops, Something went wrong.<br/>Please try again.";
$_['error_delivery_address']    =   "Please select a delivery address";
$_['error_billing_address']     =   "Please select a billing address";
$_['error_shipping_method']     =   "Please select a shipping method";
$_['error_gst'] = '<span style="color:red;display:block;text-align: center;">GST number is invalid.</span><div style="font-size:14px;display:block;text-align: center;">Either continue without GST number or enter a valid GST number.</div>';
$_['error_no_shipping']         = '<h4>We apologize, No Shipping options are available.</h4><br/> Please <a href="%s">contact us</a> for assistance!';