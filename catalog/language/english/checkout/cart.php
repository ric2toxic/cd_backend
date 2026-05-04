<?php
// Heading
$_['heading_title']            = 'Shopping Cart';

// Text
$_['text_success']             = 'Success: You have added <a href="%s">%s</a> to your <a href="%s">shopping cart</a>!';
$_['text_success_cart_added_popup'] = '<div class="alert alert-success addedincart_popup"><p>%s has been added to your cart</p>
                                        <div class="action_btn">
                                            <button onclick="return continueShopping();" class="btn btn-primary" id="continue">Continue Shopping</button>
                                            <button onclick="return viewCart();" class="btn btn-secondary" id="view_cart">View Cart</button>
                                           <button data-dismiss="alert" class="close addedincart_popup_close" type="button"></button>
                                        </div>
                                        </div>';
$_['text_remove']              = 'Success: You have modified your shopping cart!';
$_['text_login']               = 'Attention: You must <a href="%s">login</a> or <a href="%s">create an account</a> to view prices!';
$_['text_items']               = ' %s item(s) - %s';
//$_['text_items']               = '%s';
$_['text_points']              = 'Reward Points: %s';
$_['text_next']                = 'What would you like to do next?';
$_['text_next_choice']         = 'Choose if you have a coupon code or credit note you want to use.';
$_['text_empty']               = 'There are no items in your cart.';
$_['text_day']                 = 'day';
$_['text_week']                = 'week';
$_['text_semi_month']          = 'half-month';
$_['text_month']               = 'month';
$_['text_year']                = 'year';
$_['text_trial']               = '%s every %s %s for %s payments then ';
$_['text_recurring']           = '%s every %s %s';
$_['text_length']              = ' for %s payments';
$_['text_until_cancelled']     = 'until cancelled';
$_['text_recurring_item']      = 'Recurring Item';
$_['text_payment_recurring']   = 'Payment Profile';
$_['text_trial_description']   = '%s every %d %s(s) for %d payment(s) then';
$_['text_payment_description'] = '%s every %d %s(s) for %d payment(s)';
$_['text_payment_cancel']      = '%s every %d %s(s) until canceled';
$_['text_cform_pre']           = 'On submission of Form C, you will get a refund of ';
$_['text_cform_post']          = ' in your preferred mode.';
$_['text_cform_submit']        = 'Check if you will be submitting Form C.';
$_['text_cform_submit_1']      = '(You should have valid CST Number)';
$_['text_cform']			   = 'Check if you will be submitting Form C, then you will get refund of ';
$_['text_cst']                 = 'CST (2%)';
$_['text_tax_refund']          = 'Refundable amount on Form C submission';
$_['text_insufficient_quantity']          = 'Only %s sets left';
$_['text_out_of_stock']          = 'Out of stock';
$_['text_product_code'] = 'Product Code: ';
$_['text_order_summary']            = 'ORDER SUMMARY';
$_['text_subtotal']            = 'Sub-Total';
$_['text_tax']                     = 'Total Tax';
$_['text_total_amount']    = 'Total Amount';
$_['text_total_qty']       = 'Total Qty.';
$_['text_out_of_stock_popup']    = 'The Requested quantity of this Product is not available. Please change the quantity';
$_['text_store_pickup']       = 'Immediate pickup from Store';

// Column
$_['column_serial_no']              = 'S. No.';
$_['column_image']                  = 'Image';
$_['column_name']                   = 'Product';
$_['column_model']                  = 'Model';
$_['column_quantity']               = 'Sets';
$_['column_price']                  = 'Price';
$_['column_total']                  = 'Amount (Ex. Tax)';
$_['column_total_order_summary']    = 'Amount';
$_['column_tax']                    = 'Tax';
$_['column_inc_tax']                = ' (Inc. Taxes)';
$_['column_per_piece']              = ' &nbsp;/ Piece';
$_['column_per_set']                = ' &nbsp;/ Set';
$_['pieces']                        = 'Pieces';
$_['descriptionText']               = 'Set Description';
$_['pcs']                           = 'Pcs.';



// Error
$_['error_stock']              = 'Products marked with red background are not available in the desired quantity or not in stock!';
$_['error_minimum']            = 'Minimum Order Quantity for <span style="color: #FF0000"><b>%s</b></span> is <span style="color: #FF0000;"><b>%s</b></span>.';
$_['error_minimum_mobile']     = '<span style="color: #FFFFFF">Minimum Order Quantity for <span style="color: #FF0000"><b>%s</b></span> is <span style="color: #FF0000;"><b>%s</b></span>.</span>';
$_['error_cart_minimum']       = 'For buying at factory prices, Minimum Order amount (Subtotal) required at WholesaleBox is %s !';
$_['error_required']           = 'please select a %s !';
$_['error_options_quantity_required']           = 'Quantity not provided for any option!!';
$_['error_product']            = 'Warning: There are no products in your cart!';
$_['error_recurring_required'] = 'Please select a payment recurring!';
$_['error_wrong_store'] = 'Please place separate Orders for your Store Inventory and Online Inventory. ';


//cart empty
$_['cart_empty']               = '<p>Your WholesaleBox cart is empty<br/>but it does not have to be</p>' ;





//---------------------------------------------------------------------------------------------------------------------------


//cart text column table  by vikas (28-03-2016)

$_['text_number_of_pieces']  =   'No. of Pieces';
$_['text_price_per_piece']   =   'Price per piece';
$_['text_price_per_set']     =   'Price per set';
$_['text_total']             =   'Total';
$_['text_tax']               =   'Tax';
$_['text_set_description']   =   'Set Description';

// (06-04-2016)
$_['text_previously_ordered'] = 'Previously Ordered';


//19-04-2016
$_['text_custom_msg']         = 'You have selected Your Courier shipping method. Please note that, for this shipping method, pickup will be either done by you at our Jaipur office, OR, goods will be shipped to you in To Pay basis. Order needs to be Prepaid only for this shipping mode.';

//clear cart button added in cart page by vikas (05-05-2016)
$_['button_clear_cart'] = 'Clear Cart';


$_['text_cod_available'] = 'Not available on COD';

//promo code
$_['text_promo']  = 'Have a Promo Code? ';
$_['text_place_promo']  = 'Promo Code';
$_['text_promo_success'] = '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Promo Code successfully applied.</p></div>';
$_['text_promo_remove'] = '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Promo Code successfully removed.</p></div>';

$_['text_shipping'] = "Shipping Charge";

$_['text_remove_button'] = 'REMOVE';
$_['text_move_to_wishlist'] = 'ADD TO SHORTLIST';
$_['text_comment'] = 'COMMENT';
$_['text_out_of_stock_alert'] = 'This item won’t be included in your order!';

$_['want_design_message'] = 'Thank you ! We have received your message';
$_['comment_popup_heading'] = "<p>We shall try our best to get this design from manufacturer, if it is available.</p>
                                <p>Please specify how many pieces and what sizes/colors you like to have:</p>
                                ";
$_['comment_popup_send'] = 'Send';
$_['i_want_this_design'] = 'I want this design';

$_['text_pickup_city'] = 'Shipment From ';
$_['text_estimate_shipping'] = 'ESTIMATE SHIPPING';

$_['text_non_returnable'] = 'This item is non-returnable';

$_['error_postcode']       = 'Postcode must be between 2 and 10 characters!';

$_['text_out_of_stock_review'] = ' item(s), marked in red background are OUT OF STOCK.';
$_['text_quantity_reduced_review'] = ' item(s), marked with blue background has updated quantity.Those products did not have number of sets you have requested';
$_['text_moq_error_review'] = ' item(s), marked with yellow background has quantity less than MOQ.';
$_['text_review'] = 'Please review!';
$_['text_success_alert'] = ' <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span> <strong>Success</strong>
            <hr class="message-inner-separator">
            <p>You have modified your shopping cart!</p>
        </div>';
$_['text_want_design_sucess'] = '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p><strong>Thank you!</strong> We have received your message.</p></div>';
$_['text_add_wishlist'] = '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>Product added to your shortlist.</p></div>';
$_['text_start_shopping'] = 'START SHOPPING';

$_['text_custom_duty_charge'] = '<strong>Note: </strong>If there will be any custom duty on this order then it will be customer\'s responsibility to clear goods from custom. <b>For any help, please WhatsApp on +91 9116134795</b>';

$_['text_app_cashback_title'] = 'Extra 2% Cash Back on orders placed on the App';
$_['text_app_cashback_body'] = 'You can use this cashback on your next order within 15 days from delivery of this order';
$_['text_app_download'] = 'Download App from';


$_['text_free_surface_shipping_1'] = 'YAY! You get FREE Surface Shipping when ordering above INR 15000';

$_['text_free_surface_shipping_2'] = 'Add more products to qualify for FREE SURFACE SHIPPING (INR 15000 or more)';

$_['text_free_surface_shipping_3'] = '*Free Shipping not available on Stocklots';

$_['text_wsb_offer'] = 'Wholesalebox Offers';
$_['text_wsb_offer_line1'] = 'FREE website for your shop, get more clients';
$_['text_wsb_offer_line2'] = 'Join Membership to avail up to 6% discounts, call us for details';
$_['text_wsb_offer_line3'] = 'After completion of the order';

$_['text_rbl_consent']              = "<span style='color:#17319f'>I hereby authorise RBL Bank to debit my CIF account no.<b> %s </b>with an amount of <b>%s</b> and credit the amount to WholesaleBox Internet Pvt. Ltd. account.<span>";
