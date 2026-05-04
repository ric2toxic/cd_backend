<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i> <?php echo $button_cancel; ?></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?> </h3>
      </div>
      <div class="panel-body">
        <form class="form-horizontal" id="cart_form">
          <ul id="order" class="nav nav-tabs nav-justified">
            <li class="disabled active"><a href="#tab-customer" data-toggle="tab">1. <?php echo $tab_customer; ?></a></li>
            <li class="disabled"><a href="#tab-cart" data-toggle="tab">2. <?php echo $tab_product; ?></a></li>
            <li class="disabled"><a href="#tab-payment" data-toggle="tab">3. <?php echo $tab_payment; ?></a></li>
            <li class="disabled"><a href="#tab-shipping" data-toggle="tab">4. <?php echo $tab_shipping; ?></a></li>
            <li class="disabled"><a href="#tab-total" data-toggle="tab">5. <?php echo $tab_total; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-customer">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="customer" value="<?php echo $customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control" />
                  <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
                </div>
              </div>
              <!--<div class="form-group">
                <label class="col-sm-2 control-label" for="input-store"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <select name="store_id" id="input-store" class="form-control">
                    <option value="0"><?php echo $text_default; ?></option>
                    <?php foreach ($stores as $store) { ?>
                    <?php if ($store['store_id'] == $store_id) { ?>
                    <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-currency"><?php echo $entry_currency; ?></label>
                <div class="col-sm-10">
                  <select name="currency" id="input-currency" class="form-control">
                    <?php foreach ($currencies as $currency) { ?>
                    <?php if ($currency['code'] == $currency_code) { ?>
                    <option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="customer" value="<?php echo $customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control" />
                  <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
                </div>
              </div>
              -->
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="firstname" value="<?php echo $firstname; ?>" id="input-firstname" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="lastname" value="<?php echo $lastname; ?>" id="input-lastname" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="telephone" value="<?php echo $telephone; ?>" id="input-telephone" class="form-control" />
                </div>
              </div>
              <div class="text-right">
                <button type="button" id="button-customer" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?></button>
              </div>
            </div>
            <div class="tab-pane" id="tab-cart">
              <div class="table-responsive">


                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <td class="text-left">Image</td>
                      <td class="text-left"><?php echo $column_product; ?></td>
                      <td class="text-left"><?php echo $column_model; ?></td>
                      <td class="text-right"><?php echo $column_quantity; ?></td>
                      <td class="text-right">Piece/Set</td>
                      <td class="text-right"><?php echo $column_price; ?></td>
                      <td class="text-right"><?php echo $column_total; ?></td>
                      <td class="text-right">Set Broken</td>

                      <td></td>
                    </tr>
                  </thead>
                  <tbody id="cart">
                    <?php if ($order_products || $order_vouchers) : ?>
                    <?php $product_row = 0; ?>
                    <?php foreach ($order_products as $order_product) : ?>

                    <input type="hidden" name="product[<?php echo $product_row; ?>][product_id]" value="<?php echo $order_product['product_id']; ?>" />
                    <?php foreach ($order_product['option'] as $option) { ?>

                    <?php if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'image') { ?>
                    <input type="hidden" name="product[<?php echo $product_row; ?>][option][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['product_option_value_id']; ?>" />
                    <?php } ?>
                    <?php if ($option['type'] == 'checkbox') { ?>
                    <input type="hidden" name="product[<?php echo $product_row; ?>][option][<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option['product_option_value_id']; ?>" />
                    <?php } ?>
                    <?php if ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') { ?>
                    <input type="hidden" name="product[<?php echo $product_row; ?>][option][<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" />
                    <?php } ?>
                    <?php } ?>

                    <input type="hidden" name="product[<?php echo $product_row; ?>][quantity]" value="<?php echo $order_product['quantity']; ?>" />
                    <input type="hidden" name="product[<?php echo $product_row; ?>][comment]" value="<?php echo $order_product['comment']; ?>" />
                    <input type="hidden" name="product[<?php echo $product_row; ?>][piece_in_set]" value="<?php echo $order_product['piece_in_set']; ?>" />
                    <input type="hidden" name="product[<?php echo $product_row; ?>][tax]" value="<?php echo $order_product['tax']; ?>" />
                    <?php $product_row++; ?>
                    <?php endforeach; ?>
                    <?php $voucher_row = 0; ?>
                    <?php foreach ($order_vouchers as $order_voucher) : ?>

                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][voucher_id]" value="<?php echo $order_voucher['voucher_id']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][description]" value="<?php echo $order_voucher['description']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][code]" value="<?php echo $order_voucher['code']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][from_name]" value="<?php echo $order_voucher['from_name']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][from_email]" value="<?php echo $order_voucher['from_email']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][to_name]" value="<?php echo $order_voucher['to_name']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][to_email]" value="<?php echo $order_voucher['to_email']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][voucher_theme_id]" value="<?php echo $order_voucher['voucher_theme_id']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][message]" value="<?php echo $order_voucher['message']; ?>" />
                    <input type="hidden" name="voucher[<?php echo $voucher_row; ?>][amount]" value="<?php echo $order_voucher['amount']; ?>" />
                    <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <ul class="nav nav-tabs nav-justified">
                <li class="active"><a href="#tab-product" data-toggle="tab"><?php echo $tab_product; ?></a></li>
                <!--<li><a href="#tab-voucher" data-toggle="tab"><?php // echo $tab_voucher; ?></a></li>-->
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="tab-product">
                  <fieldset>
                    <legend><?php echo $text_product; ?></legend>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-model"><?php echo $entry_model; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="model" value="" id="input-model" class="form-control" />
                        <input type="hidden" name="product_id" value="" />
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="remove-quantity">
                      <label class="col-sm-2 control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="quantity" value="1" id="input-quantity" class="form-control" />
                      </div>
                        </div>
                      <div class="show_set form-group"></div>
                      <div class="show_details form-group"></div>

                      <div class="show_store form-group"></div>

                      <hr>
                    </div>
                     <?php $i = 0; ?>
                    <div class="showpq">
                      <div class="show_previous_break"></div>
                    <div class="form-group required col-sm-12" id="size_box">
                      <label class="col-sm-2 control-label" for="input-size-set">Size/Color</label>
                      <div class="col-sm-4" style="margin-bottom: 20px;">
                       <input type="text" name="set-size[]" value="" id="input-size-set" class="form-control input-size-set size_of_quantity" required="required" />
                      </div>
                      <label class="col-sm-2 control-label" for="input-quantity-piece">Quantity</label>
                      <div class="col-sm-3" style="margin-bottom: 20px;">
                        <input type="text" name="break-quantity[]" value="0" id="input-quantity-piece" class="form-control" required="required" />
                      </div>
                      <div class="error-size"></div>

                      <div class="hidden_set"></div>
                      <div class="hidden_sell"></div>
                      <div class="hidden_check"></div>
                    </div>
                      <div class="add-btn add-minus-btn col-sm-1"><span style="color: white; font-size: 16px; font-weight: 600;"><i class="fa fa-plus"></i></span></div>
                    </div>
                    <div id="option"></div>
                  </fieldset>
                  <div class="addtocart_container col-sm-2">
                    <button type="button" id="button-product-add" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_product_add_to_cart; ?></button>
                  </div>
                  <div class="break-btn col-sm-4"><span><i class="fa fa-gavel"></i>&nbsp;Break Set</span></div>
                  <div class="hide_parent col-sm-4"><span style="color: black; font-size: 16px; font-weight: 400;"><i class="fa fa-times"></i>&nbsp;Cancel</span></div>
                </div>
               <!-- <div class="tab-pane" id="tab-voucher">
                  <fieldset>
                    <legend><?php echo $text_voucher; ?></legend>
                    <div class="form-group required">
                      <label class="col-sm-2 control-label" for="input-to-name"><?php echo $entry_to_name; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="to_name" value="" id="input-to-name" class="form-control" />
                      </div>
                    </div>
                    <div class="form-group required">
                      <label class="col-sm-2 control-label" for="input-to-email"><?php echo $entry_to_email; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="to_email" value="" id="input-to-email" class="form-control" />
                      </div>
                    </div>
                    <div class="form-group required">
                      <label class="col-sm-2 control-label" for="input-from-name"><?php echo $entry_from_name; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="from_name" value="" id="input-from-name" class="form-control" />
                      </div>
                    </div>
                    <div class="form-group required">
                      <label class="col-sm-2 control-label" for="input-from-email"><?php echo $entry_from_email; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="from_email" value="" id="input-from-email" class="form-control" />
                      </div>
                    </div>
                    <div class="form-group required">
                      <label class="col-sm-2 control-label" for="input-theme"><?php echo $entry_theme; ?></label>
                      <div class="col-sm-10">
                        <select name="voucher_theme_id" id="input-theme" class="form-control">
                          <?php foreach ($voucher_themes as $voucher_theme) { ?>
                          <option value="<?php echo $voucher_theme['voucher_theme_id']; ?>"><?php echo $voucher_theme['name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-message"><?php echo $entry_message; ?></label>
                      <div class="col-sm-10">
                        <textarea name="message" rows="5" id="input-message" class="form-control"></textarea>
                      </div>
                    </div>
                    <div class="form-group required">
                      <label class="col-sm-2 control-label" for="input-amount"><?php echo $entry_amount; ?></label>
                      <div class="col-sm-10">
                        <input type="text" name="amount" value="<?php echo $voucher_min; ?>" id="input-amount" class="form-control" />
                      </div>
                    </div>
                  </fieldset>
                  <div class="text-right">
                    <button type="button" id="button-voucher-add" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_voucher_add; ?></button>
                  </div>
                </div>-->
              </div>
              <br />
              <div class="row">
                <div class="col-sm-6 text-left">
                  <button type="button" onclick="$('a[href=\'#tab-customer\']').tab('show');" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?php echo $button_back; ?></button>
                </div>
                <div class="col-sm-6 text-right">
                  <button type="button" id="button-cart" class="btn btn-primary"><i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?></button>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-payment">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-payment-address"><?php echo $entry_address; ?></label>
                <div class="col-sm-10">
                  <select name="payment_address" id="input-payment-address" class="form-control">
                    <option value="0" selected="selected"><?php echo $text_none; ?></option>
                    <?php foreach ($addresses as $address) { ?>
                    <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname'] . ' ' . $address['lastname'] . ', ' . $address['address_1'] . ', ' . $address['city'] . ', ' . $address['country']; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-payment-firstname"><?php echo $entry_firstname; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="firstname" value="<?php echo $payment_firstname; ?>" id="input-payment-firstname" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-payment-lastname"><?php echo $entry_lastname; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="lastname" value="<?php echo $payment_lastname; ?>" id="input-payment-lastname" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-payment-company"><?php echo $entry_company; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="company" value="<?php echo $payment_company; ?>" id="input-payment-company" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-payment-address-1"><?php echo $entry_address_1; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="address_1" value="<?php echo $payment_address_1; ?>" id="input-payment-address-1" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-payment-address-2"><?php echo $entry_address_2; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="address_2" value="<?php echo $payment_address_2; ?>" id="input-payment-address-2" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-payment-city"><?php echo $entry_city; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="city" value="<?php echo $payment_city; ?>" id="input-payment-city" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-payment-postcode"><?php echo $entry_postcode; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="postcode" value="<?php echo $payment_postcode; ?>" id="input-payment-postcode" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-payment-country"><?php echo $entry_country; ?></label>
                <div class="col-sm-10">
                  <select name="country_id" id="input-payment-country" class="form-control">
                    <option value=""><?php echo $text_select; ?></option>
                    <?php foreach ($countries as $country) { ?>
                    <?php if ($country['country_id'] == $payment_country_id) { ?>
                    <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-payment-zone"><?php echo $entry_zone; ?></label>
                <div class="col-sm-10">
                  <select name="zone_id" id="input-payment-zone" class="form-control">
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-payment-postcode"><?php echo "Save this Address to Customer Address"; ?></label>
                <div class="col-sm-10">
                  <input type="checkbox" name="savetoaddress" value="yes" id="input-payment-savetoaddress" class="form-control" />
                  <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-payment-postcode"><?php echo "Ship to the same Address"; ?></label>
                <div class="col-sm-10">
                  <input type="checkbox" name="shipping_address" value="yes" id="input-payment-shipping_address" class="form-control" />
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6 text-left">
                  <button type="button" onclick="$('a[href=\'#tab-cart\']').tab('show');" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?php echo $button_back; ?></button>
                </div>
                <div class="col-sm-6 text-right">
                  <button type="button" id="button-payment-address" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?></button>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-shipping">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-shipping-address"><?php echo $entry_address; ?></label>
                <div class="col-sm-10">
                  <select name="shipping_address" id="input-shipping-address" class="form-control">
                    <option value="0" selected="selected"><?php echo $text_none; ?></option>
                    <?php foreach ($addresses as $address) { ?>
                    <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname'] . ' ' . $address['lastname'] . ', ' . $address['address_1'] . ', ' . $address['city'] . ', ' . $address['country']; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-shipping-firstname"><?php echo $entry_firstname; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="firstname" value="<?php echo $shipping_firstname; ?>" id="input-shipping-firstname" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-shipping-lastname"><?php echo $entry_lastname; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="lastname" value="<?php echo $shipping_lastname; ?>" id="input-shipping-lastname" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-shipping-company"><?php echo $entry_company; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="company" value="<?php echo $shipping_company; ?>" id="input-shipping-company" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-shipping-address-1"><?php echo $entry_address_1; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="address_1" value="<?php echo $shipping_address_1; ?>" id="input-shipping-address-1" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-shipping-address-2"><?php echo $entry_address_2; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="address_2" value="<?php echo $shipping_address_2; ?>" id="input-shipping-address-2" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-shipping-city"><?php echo $entry_city; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="city" value="<?php echo $shipping_city; ?>" id="input-shipping-city" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-shipping-postcode"><?php echo $entry_postcode; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="postcode" value="<?php echo $shipping_postcode; ?>" id="input-shipping-postcode" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-shipping-country"><?php echo $entry_country; ?></label>
                <div class="col-sm-10">
                  <select name="country_id" id="input-shipping-country" class="form-control">
                    <option value=""><?php echo $text_select; ?></option>
                    <?php foreach ($countries as $country) { ?>
                    <?php if ($country['country_id'] == $shipping_country_id) { ?>
                    <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-shipping-zone"><?php echo $entry_zone; ?></label>
                <div class="col-sm-10">
                  <select name="zone_id" id="input-shipping-zone" class="form-control">
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-6 text-left">
                  <button type="button" onclick="$('a[href=\'#tab-payment\']').tab('show');" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?php echo $button_back; ?></button>
                </div>
                <div class="col-sm-6 text-right">
                  <button type="button" id="button-shipping-address" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?></button>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-total">
              <div class="table-responsive">
                <table class="table table-bordered table_without_cst">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo "Image"; ?></td>
                      <td class="text-left"><?php echo $column_product; ?></td>
                      <td class="text-left"><?php echo $column_model; ?></td>
                      <td class="text-right"><?php echo $column_quantity; ?></td>
                      <td class="text-right"><?php echo $column_price; ?></td>
                      <td class="text-right"><?php echo $column_total; ?></td>
                      <td class="text-right"><?php echo "Tax"; ?></td>
                    </tr>
                  </thead>
                  <tbody id="total">
                    <tr>
                      <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
                    </tr>
                  </tbody>
                </table>

                <table class="table table-bordered table_without_cst pull-right" id="total_without_cst" style="width: 40%;">
                  <tbody>
                  <tr>
                    <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
                  </tr>
                  </tbody>
                </table>

                <table class="table table-bordered table_cst pull-right" id="total_cst" style="display:none; width: 40%;">
                  <tbody>
                  <tr>
                    <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
                  </tr>
                  </tbody>
                </table>

              </div>
              <fieldset>
                <legend><?php echo $text_order; ?></legend>
                <div class="form-group row">
                  <form method="post" class="col-sm-10">
                    <h4 class="cart-c-form cart-c-form col-sm-10 pull-right"><input type="checkbox" id="c-form-check" name="c-form-check"> <?php echo $text_cform_submit; ?> <span><?php echo $text_cform_submit_1; ?></span></h4>
                    <input type="hidden" name="cform_submit" value="" />
                  </form>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-shipping-method"><?php echo $entry_shipping_method; ?></label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <select name="shipping_method" id="input-shipping-method" class="form-control">
                        <option value=""><?php echo $text_select; ?></option>
                        <?php if ($shipping_code) { ?>
                        <option value="<?php echo $shipping_code; ?>" selected="selected"><?php echo $shipping_method; ?></option>
                        <?php } ?>
                      </select>
                      <span class="input-group-btn">
                      <button type="button" id="button-shipping-method" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_apply; ?></button>
                      </span></div>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-payment-method"><?php echo $entry_payment_method; ?></label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <select name="payment_method" id="input-payment-method" class="form-control">
                        <option value=""><?php echo $text_select; ?></option>
                        <?php if ($payment_code) { ?>
                        <option value="<?php echo $payment_code; ?>" selected="selected"><?php echo $payment_method; ?></option>
                        <?php } ?>
                      </select>
                      <span class="input-group-btn">
                      <button type="button" id="button-payment-method" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_apply; ?></button>
                      </span></div>
                  </div>
                </div>
                <div class="form-group">
                  <div id="free-ship" data-html = 'KVFREESHIP'>Free-Shipping&nbsp&nbsp&nbsp&nbsp<span class="shining_text">(click here to use free shipping coupon)</span></div><div id="free-two" data-html = 'KV2OFF'>2% Off&nbsp&nbsp&nbsp&nbsp<span class="shining_text">(click here to use 2% discount coupon)</span></div>
                  <label class="col-sm-2 control-label" for="input-coupon"><?php echo $entry_coupon; ?></label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <input type="text" name="coupon" value="<?php echo $coupon; ?>" id="input-coupon" class="form-control" />
                      <span class="input-group-btn">
                      <button type="button" id="button-coupon" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_apply; ?></button>
                      </span></div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-voucher"><?php echo $entry_voucher; ?></label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <input type="text" name="voucher" value="<?php echo $voucher; ?>" id="input-voucher" data-loading-text="<?php echo $text_loading; ?>" class="form-control" />
                      <span class="input-group-btn">
                      <button type="button" id="button-voucher" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_apply; ?></button>
                      </span></div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                  <div class="col-sm-10">
                    <select name="order_status_id" id="input-order-status" class="form-control">
                      <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if ($order_status['order_status_id'] == $order_status_id) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-comment"><?php echo $entry_comment; ?></label>
                  <div class="col-sm-10">
                    <textarea name="comment" rows="5" id="input-comment" class="form-control"><?php echo $comment; ?></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-affiliate"><?php echo $entry_affiliate; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="affiliate" value="<?php echo $affiliate; ?>" id="input-affiliate" class="form-control" />
                    <input type="hidden" name="affiliate_id" value="<?php echo $affiliate_id; ?>" />
                  </div>
                </div>
              </fieldset>
              <div class="row">
                <div class="col-sm-6 text-left">
                  <button type="button" onclick="$('select[name=\'shipping_method\']').prop('disabled') ? $('a[href=\'#tab-payment\']').tab('show') : $('a[href=\'#tab-shipping\']').tab('show');" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?php echo $button_back; ?></button>
                </div>
                <div class="col-sm-6 text-right">
                  <button type="button" id="button-refresh" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-warning"><i class="fa fa-refresh"></i></button>
                  <button type="button" id="button-save" class="btn btn-primary"><i class="fa fa-check-circle"></i> <?php echo $button_save; ?></button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div id="add-size-quantity" style="display: none;">
  <div class="size_quantity_append_0 remove_size">
    <label class="col-sm-2 control-label" for="input-size-set">Size/Color</label>
    <div class="col-sm-4" style="margin-bottom: 20px;">
      <input type="text" name="set-size[]" value="" id="input-size-set" class="form-control size_of_quantity input-size-set" required="required" />
    </div>
    <label class="col-sm-2 control-label" for="input-quantity-piece">Quantity</label>
    <div class="col-sm-3" style="margin-bottom: 20px;">
      <input type="text" name="break-quantity[]" value="0" id="input-quantity-piece" class="form-control input-quantity-piece" required="required" />
    </div>
    <div class="col-sm-1 remove_btn add-minus-btn"  style="margin-bottom: 20px;">
      <span style="color: white; font-size: 16px; font-weight: 600;"><i class="fa fa-minus"></i></span>
    </div>
    <div class="error-size"></div>
    <div class="hidden_set"></div>
    <div class="hidden_sell"></div>
    <div class="hidden_check"></div>
  </div>
  </div>
  <script type="text/javascript"><!--
    var i = 0;
    var comment_product = '';
    // Disable the tabs
$('#order a[data-toggle=\'tab\']').on('click', function(e) {
	return false;
});
			
// Add all products to the cart using the api
    var a = 0;
$(document).delegate('#button-refresh' , 'click', function () {

	$.ajax({
    url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/cart/products&backend=1&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		dataType: 'json',
		success: function(json) {
			$('.alert-danger, .text-danger').remove();

			// Check for errors
			if (json['error']) {
				if (json['error']['warning']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				if (json['error']['stock']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['stock'] + '</div>');
				}

				if (json['error']['minimum']) {
					for (i in json['error']['minimum']) {
						$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['minimum'][i] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					}
				}
			}

			var shipping = false;

			html = '';
           // alert(json['products'].length);
          // console.log(json['products']);
			if (json['products']) {
				for (i = 0; i < json['products'].length; i++) {
					product = json['products'][i];

                  //console.log(product);

					html += '<tr">';
                  html += '  <td class="text-left"><img src="' + product['image'] + '" width="75px" height="111px" /></td>';

                  html += '  <td class="text-left"><a href="' + product['href'] + '" target="_blank">' + product['name'] + ' ' + (!product['stock'] ? '<div style="background-color: lightcoral; color: white; font-size: 16px; font-weight: 600; text-align: center; width: 100%; height: 100%;">Out of Stock!!!</div>' : '') + '<br />';
                  html += product['comment'];
					html += '  <input type="hidden" name="product[' + i + '][product_id]" value="' + product['product_id'] + '" />';

					if (product['option']) {
						for (j = 0; j < product['option'].length; j++) {
							option = product['option'][j];

							html += '  - <small>' + option['name'] + ': ' + option['value'] + '</small><br />';

							if (option['type'] == 'select' || option['type'] == 'radio' || option['type'] == 'image') {
								html += '<input type="hidden" name="product[' + i + '][option][' + option['product_option_id'] + ']" value="' + option['product_option_value_id'] + '" />';
							}

							if (option['type'] == 'checkbox') {
								html += '<input type="hidden" name="product[' + i + '][option][' + option['product_option_id'] + '][]" value="' + option['product_option_value_id'] + '" />';
							}

							if (option['type'] == 'text' || option['type'] == 'textarea' || option['type'] == 'file' || option['type'] == 'date' || option['type'] == 'datetime' || option['type'] == 'time') {
								html += '<input type="hidden" name="product[' + i + '][option][' + option['product_option_id'] + ']" value="' + option['value'] + '" />';
							}
						}
					}

					html += '</a></td>';
					html += '  <td class="text-left" id="'+ i +'" data-html = "' + product['model'] + '">' + product['model'] + '</td>';
                    html += '  <td class="text-right">' + product['quantity'] + '<input type="hidden" name="product[' + i + '][quantity]" value="' + product['quantity'] + '" /></td>';
                    html += '  <td class="text-right">' + product['piece_in_set'] + '</td>';
                    html += '  <td class="text-right">' + product['price_per_piece'] + '</td>';
                    html += '  <td class="text-right">' + product['total'] + '</td>';
                  if(product['comment'] != '') {
                    html += '  <td class="text-right">Yes</td>';
                  }else{
                    html += '  <td class="text-right">No</td>';
                  }

					html += '  <td class="text-center" style="width: 3px;"><button type="button" value="' + product['key'] + '" data-toggle="tooltip" title="<?php echo $button_remove; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger" data-remove = "'+  product['model']  +'" id="remove_'+ i +'"><i class="fa fa-minus-circle"></i></button></td>';
                    html += '  <td class="text-center" style="width: 3px;"><button type="button" data-html="' + product['model'] + '" data-toggle="tooltip" title="Edit" id="edit_product_button"  class="btn edit_product_button" value = "' + product['model'] + '" data-comment="' + product['comment'] + '"><i class="fa fa-pencil"></i></button></td>';
                    html += '</tr>';


                  if (product['shipping'] != 0) {
						shipping = true;
					}

                  if (!product['stock']) {
                     a = 1;
                  }
				}


              $(document).delegate('.edit_product_button' , 'click', function () {
                  var edit = $(this).attr('data-html');
                  var comment = $(this).attr('data-comment');
                  document.getElementById("input-model").value = edit;
                  $('#input-model').trigger("focus");
                  comment_product = comment;
                 // alert(comment);
              });

			}

			if (!shipping) {
				$('select[name=\'shipping_method\'] option').removeAttr('selected');
				$('select[name=\'shipping_method\']').prop('disabled', true);
				$('#button-shipping-method').prop('disabled', true);
			} else {
				$('select[name=\'shipping_method\']').prop('disabled', false);
				$('#button-shipping-method').prop('disabled', false);
			}

			if (json['vouchers']) {
				for (i in json['vouchers']) {
					voucher = json['vouchers'][i];

					html += '<tr>';
					html += '  <td class="text-left">' + voucher['description'];
                    html += '    <input type="hidden" name="voucher[' + i + '][code]" value="' + voucher['code'] + '" />';
					html += '    <input type="hidden" name="voucher[' + i + '][description]" value="' + voucher['description'] + '" />';
                    html += '    <input type="hidden" name="voucher[' + i + '][from_name]" value="' + voucher['from_name'] + '" />';
                    html += '    <input type="hidden" name="voucher[' + i + '][from_email]" value="' + voucher['from_email'] + '" />';
                    html += '    <input type="hidden" name="voucher[' + i + '][to_name]" value="' + voucher['to_name'] + '" />';
                    html += '    <input type="hidden" name="voucher[' + i + '][to_email]" value="' + voucher['to_email'] + '" />';
                    html += '    <input type="hidden" name="voucher[' + i + '][voucher_theme_id]" value="' + voucher['voucher_theme_id'] + '" />';
                    html += '    <input type="hidden" name="voucher[' + i + '][message]" value="' + voucher['message'] + '" />';
                    html += '    <input type="hidden" name="voucher[' + i + '][amount]" value="' + voucher['amount'] + '" />';
					html += '  </td>';
					html += '  <td class="text-left"></td>';
					html += '  <td class="text-right">1</td>';
					html += '  <td class="text-right">' + voucher['amount'] + '</td>';
					html += '  <td class="text-right">' + voucher['amount'] + '</td>';
					html += '  <td class="text-center" style="width: 3px;"><button type="button" value="' + voucher['code'] + '" data-toggle="tooltip" title="<?php echo $button_remove; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
					html += '</tr>';
				}
			}

			if (json['products'] == '' && json['vouchers'] == '') {
				html += '<tr>';
				html += '  <td colspan="6" class="text-center"><?php echo $text_no_results; ?></td>';
				html += '</tr>';
			}

			$('#cart').html(html);

			// Totals
			html = '';

			if (json['products']) {
				for (i = 0; i < json['products'].length; i++) {
					product = json['products'][i];

					html += '<tr>';
                  html += '  <td class="text-left"><img src="' + product['image'] + '" width="75px" height="111px" /></td>';

                  html += '  <td class="text-left">' + product['name'] + ' ' + (!product['stock'] ? '<span class="text-danger">***</span>' : '') + '<br />';

					if (product['option']) {
						for (j = 0; j < product['option'].length; j++) {
							option = product['option'][j];

							html += '  - <small>' + option['name'] + ': ' + option['value'] + '</small><br />';
						}
					}
					html += '  </td>';
					html += '  <td class="text-left">' + product['model'] + '</td>';
					html += '  <td class="text-right">' + product['quantity'] + '</td>';
					html += '  <td class="text-right">' + product['price'] + '</td>';
					html += '  <td class="text-right">' + product['total'] + '</td>';
                    html += '  <td class="text-right">' + product['tax'] + '</td>';
					html += '</tr>';
				}
			}

			if (json['vouchers']) {
				for (i in json['vouchers']) {
					voucher = json['vouchers'][i];

					html += '<tr>';
					html += '  <td class="text-left">' + voucher['description'] + '</td>';
					html += '  <td class="text-left"></td>';
					html += '  <td class="text-right">1</td>';
					html += '  <td class="text-right">' + voucher['amount'] + '</td>';
					html += '  <td class="text-right">' + voucher['amount'] + '</td>';
					html += '</tr>';
				}
			}
            $('#total').html(html);
            html = '';
			if (json['totals']) {
            //alert(JSON.stringify(json['totals']));

              for (i in json['totals']) {
                total = json['totals'][i];

                html += '<tr>';
                html += '  <td class="text-right" colspan="6">' + total['title'] + ':</td>';
                html += '  <td class="text-right" >' + total['text'] + '</td>';
                html += '</tr>';
                $('#total_without_cst').html(html);
              }

              html = '';


              for (i in json['totals']) {
                total = json['totals'][i];


                if (total['code'] == 'tax') {
                  total['title'] = total['cst_refund'];
                  total['text'] = total['text_refund']
                  html += '<tr>';
                  html += '  <td class="text-right" colspan="6">' + total['cst'] + ':</td>';
                  html += '  <td class="text-right">' + total['cst_value'] + '</td>';
                  html += '</tr>';
                }
                html += '<tr>';
                html += '  <td class="text-right" colspan="6">' + total['title'] + ':</td>';
                html += '  <td class="text-right">' + total['text'] + '</td>';
                html += '</tr>';

                $('#total_cst').html(html);

              }

				}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

    $('#c-form-check').change(function (){
      if (this.checked) {
        $('#total_cst').css("display", "table");
        $('#total_without_cst').css("display", "none");
      } else {
        $('#total_cst').css("display", "none");
        $('#total_without_cst').css("display", "table");
      }
    });

    $('#button-cart').on('click', function() {
      if(a == 1) {
      if(confirm('There are out of stock products in cart. Are you sure to continue?')) {
        $('a[href=\'#tab-payment\']').tab('show');
        a = 0;
      }else{
        a = 0;
        return false;
      }
      }else {
        $('a[href=\'#tab-payment\']').tab('show');
      }
    });
// Currency
$('select[name=\'currency\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/currency&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: 'currency=' + $('select[name=\'currency\'] option:selected').val(),
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'currency\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},	
		complete: function() {
			$('.fa-spin').remove();
		},		
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			
			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			
				// Highlight any found errors
				$('select[name=\'currency\']').parent().parent().parent().addClass('has-error');			
			}
		},	
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'currency\']').trigger('change');

// Customer
$('input[name=\'customer\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_all=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				json.unshift({
					customer_id: '0',					
					name: '<?php echo $text_none; ?>',
					firstname: '',
					lastname: '',
					email: '',
					telephone: '',
					address: []			
				});				
				
				response($.map(json, function(item) {
                  var tel = item['telephone'];
                  var email = item['email'];
                    var label_name = item['name'].concat(" ") + tel.concat(" ") + email;
					return {
						label: label_name,
						value: item['customer_id'],					
						firstname: item['firstname'],
						lastname: item['lastname'],
						email: item['email'],
						telephone: item['telephone'],
						address: item['address']
					}
				}));
			}
		});
	},
	'select': function(item) {
		// Reset all custom fields
		$('#tab-customer input[type=\'text\'], #tab-customer input[type=\'text\'], #tab-customer textarea').not('#tab-customer input[name=\'customer\'], #tab-customer input[name=\'customer_id\']').val('');
		$('#tab-customer select option').removeAttr('selected');
		$('#tab-customer input[type=\'checkbox\'], #tab-customer input[type=\'radio\']').removeAttr('checked');
		$('#tab-customer input[name=\'customer\']').val(item['label']);
		$('#tab-customer input[name=\'customer_id\']').val(item['value']);
		$('#tab-customer input[name=\'firstname\']').val(item['firstname']);
		$('#tab-customer input[name=\'lastname\']').val(item['lastname']);
		$('#tab-customer input[name=\'email\']').val(item['email']);
		$('#tab-customer input[name=\'telephone\']').val(item['telephone']);		
		
		html = '<option value="0"><?php echo $text_none; ?></option>'; 
			
		for (i in  item['address']) {
			html += '<option value="' + item['address'][i]['address_id'] + '">' + item['address'][i]['firstname'] + ' ' + item['address'][i]['lastname'] + ', ' + item['address'][i]['address_1'] + ', ' + item['address'][i]['city'] + ', ' + item['address'][i]['country'] + '</option>';
		}
		
		$('select[name=\'payment_address\']').html(html);
		$('select[name=\'shipping_address\']').html(html);
		
		$('select[name=\'payment_address\']').trigger('change');
		$('select[name=\'shipping_address\']').trigger('change');
	}
});

$('#button-customer').on('click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/customer&order_id=<?php echo $order_edit; ?>&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: $('#tab-customer input[type=\'text\'], #tab-customer input[type=\'hidden\'], #tab-customer input[type=\'radio\']:checked, #tab-customer input[type=\'checkbox\']:checked, #tab-customer select, #tab-customer textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-customer').button('loading');
		},
		complete: function() {
			 $('#button-customer').button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			
			if (json['error']) {
				if (json['error']['warning']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}				
				
				for (i in json['error']) {
					var element = $('#input-' + i.replace('_', '-'));
					
					if (element.parent().hasClass('input-group')) {
                   		$(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
					} else {
						$(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
					}
				}

				// Highlight any found errors
				$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
			} else {

				$.ajax({
					url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/cart/add&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
                    data: $('#cart input[name^=\'product\'][type=\'text\'], #cart input[name^=\'product\'][type=\'hidden\'], #cart input[name^=\'product\'][type=\'radio\']:checked, #cart input[name^=\'product\'][type=\'checkbox\']:checked, #cart select[name^=\'product\'], #cart textarea[name^=\'product\']'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-product-add').button('loading');
					},
					complete: function() {
						$('#button-product-add').button('reset');

					},
					success: function(json) {

						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');
					
						if (json['error'] && json['error']['warning']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {

						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});		
					
				$.ajax({
					url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/voucher/add&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					type: 'post',
					data: $('#cart input[name^=\'voucher\'][type=\'text\'], #cart input[name^=\'voucher\'][type=\'hidden\'], #cart input[name^=\'voucher\'][type=\'radio\']:checked, #cart input[name^=\'voucher\'][type=\'checkbox\']:checked, #cart select[name^=\'voucher\'], #cart textarea[name^=\'voucher\']'),
					dataType: 'json',
					beforeSend: function() {
						$('#button-voucher-add').button('loading');
					},
					complete: function() {
						$('#button-voucher-add').button('reset');
					},
					success: function(json) {
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');
					
						if (json['error'] && json['error']['warning']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});	

				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
								
				$('a[href=\'#tab-cart\']').tab('show');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});


    var flag = '0';
    var replaceI = '0';
    $('.add-btn').click(function(){
      if (flag == 0) {
        replaceI = '1';
      }
      //replaceI = '<?php echo $i;?>';
      var replaceWith = "size_quantity_append_" + replaceI;
      var tobereplaced = "size_quantity_append_0";

      var size = $("#add-size-quantity").html();

      var new_text = size.replace(tobereplaced, replaceWith);
      if ($('#size_box').append(new_text)) {
        replaceI++;
        flag = '1';
        // clickflag = 1;
      }
    });
    $('.add-btn').click(function(){

    });

$('#button-product-add').on('click', function() {
  if($('.showpq').is(':visible')) {
    var input_set_size = $('#input-size-set').val();
    var input_quantity_piece = $('#input-quantity-piece').val();
    if (input_set_size == '' || input_quantity_piece == 0){
      alert('please fill out size and quantity in order break set or cancel and then add to cart to add as set');
      return false;
    }
  }
  for(jak = 0; jak <= 200; jak++){
    var value_model = $('#'+jak).attr("data-html");

    var entered_model = $('#input-model').val();

    var model_only = entered_model.substring(0, entered_model.indexOf(' '));

    if(model_only.search("SNGL") == -1){
    if(value_model == model_only){
      var r = confirm("This product in already in cart. Are you sure to change it?");
      if (r == true) {
         var remove = $("#remove_" + jak).val();

        $.ajax({

        url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/cart/remove&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
        type: 'post',
          data: 'key=' + encodeURIComponent(remove),
          dataType: 'json',
          success: function(json) {
            $('.alert, .text-danger').remove();

            // Check for errors
            if (json['error']) {
              $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            } else {
              // Refresh products, vouchers and totals
              $('#button-refresh').trigger('click');
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
      } else {
        return false;
      }
    }
    }
  }

	$.ajax({
        url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/cart/add&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: $('#tab-product input[name=\'product_id\'], #tab-product input[name=\'selling-price\'], #tab-product input[name=\'break-quantity[]\'], #tab-product input[name=\'check_hide\'], #tab-product input[name=\'set-type\'], #tab-product input[name=\'set-size[]\'], #tab-product input[name=\'quantity\'], #tab-product input[name^=\'option\'][type=\'text\'], #tab-product input[name^=\'option\'][type=\'hidden\'], #tab-product input[name^=\'option\'][type=\'radio\']:checked, #tab-product input[name^=\'option\'][type=\'checkbox\']:checked, #tab-product select[name^=\'option\'], #tab-product textarea[name^=\'option\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-product-add').button('loading');
		},
		complete: function() {
			$('#button-product-add').button('reset');
		},
		success: function(json) {
          $('.alert, .text-danger').remove();
			if (json['error']) {
				if (json['error']['warning']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							$(element).parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							$(element).after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}

				if (json['error']['store']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['store'] + '</div>');
				}

				// Highlight any found errors
				$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
			} else {
				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
              $('.alert, .text-danger').remove();
              $('.form-group').removeClass('has-error');
              $('.showpq').hide();
              $('.set_break').hide();
              $('.details_table').hide();
              $('.store_check').hide();
              $('.store_price').hide();
              $('#option').hide();
              $('.addtocart_container').hide();
              $('.break-btn').hide();
              $('.hide_parent').hide();
              $('.show_previous_break_up').remove();
              document.getElementById("cart_form").reset();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#tab-cart').delegate('.btn-danger', 'click', function() {
	var node = this;
 // alert(encodeURIComponent(this.value));
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/cart/remove&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: 'key=' + encodeURIComponent(this.value),
		dataType: 'json',
		beforeSend: function() {
			$(node).button('loading');
		},
		complete: function() {
			$(node).button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();

			// Check for errors
			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			} else {
				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

});


// Payment Address
$('select[name=\'payment_address\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=sale/customer/address&token=<?php echo $token; ?>&address_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'payment_address\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('#tab-payment .fa-spin').remove();
		},
		success: function(json) {
			// Reset all fields
			$('#tab-payment input[type=\'text\'], #tab-payment input[type=\'text\'], #tab-payment textarea').val('');
			$('#tab-payment select option').not('#tab-payment select[name=\'payment_address\']').removeAttr('selected');
			$('#tab-payment input[type=\'checkbox\'], #tab-payment input[type=\'radio\']').removeAttr('checked');

			$('#tab-payment input[name=\'firstname\']').val(json['firstname']);
			$('#tab-payment input[name=\'lastname\']').val(json['lastname']);
			$('#tab-payment input[name=\'company\']').val(json['company']);
			$('#tab-payment input[name=\'address_1\']').val(json['address_1']);
			$('#tab-payment input[name=\'address_2\']').val(json['address_2']);
			$('#tab-payment input[name=\'city\']').val(json['city']);
			$('#tab-payment input[name=\'postcode\']').val(json['postcode']);
			$('#tab-payment select[name=\'country_id\']').val(json['country_id']);

			payment_zone_id = json['zone_id'];

			$('#tab-payment select[name=\'country_id\']').trigger('change');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});


    // Voucher
    $('#button-voucher-add').on('click', function() {
      $.ajax({
        url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/voucher/add&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
        type: 'post',
        data: $('#tab-voucher input[type=\'text\'], #tab-voucher input[type=\'hidden\'], #tab-voucher input[type=\'radio\']:checked, #tab-voucher input[type=\'checkbox\']:checked, #tab-voucher select, #tab-voucher textarea'),
        dataType: 'json',
        beforeSend: function() {
          $('#button-voucher-add').button('loading');
        },
        complete: function() {
          $('#button-voucher-add').button('reset');
        },
        success: function(json) {
          $('.alert, .text-danger').remove();
          $('.form-group').removeClass('has-error');

          if (json['error']) {
            if (json['error']['warning']) {
              $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            }

            for (i in json['error']) {
              var element = $('#input-' + i.replace('_', '-'));

              if (element.parent().hasClass('input-group')) {
                $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
              } else {
                $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
              }
            }

            // Highlight any found errors
            $('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');
          } else {
            $('input[name=\'from_name\']').attr('value', '');
            $('input[name=\'from_email\']').attr('value', '');
            $('input[name=\'to_name\']').attr('value', '');
            $('input[name=\'to_email\']').attr('value', '');
            $('textarea[name=\'message\']').attr('value', '');
            $('input[name=\'amount\']').attr('value', '<?php echo addslashes($voucher_min); ?>');

            // Refresh products, vouchers and totals
            $('#button-refresh').trigger('click');
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    });



var payment_zone_id = '<?php echo $payment_zone_id; ?>';

$('#tab-payment select[name=\'country_id\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=sale/order/country&token=<?php echo $token; ?>&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('#tab-payment select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('#tab-payment .fa-spin').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#tab-payment input[name=\'postcode\']').parent().parent().addClass('required');
			} else {
				$('#tab-payment input[name=\'postcode\']').parent().parent().removeClass('required');
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == payment_zone_id) {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('#tab-payment select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#tab-payment select[name=\'country_id\']').trigger('change');

$('#button-payment-address').on('click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/payment/address&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: $('#tab-payment input[type=\'text\'], #tab-payment input[type=\'hidden\'], #tab-payment input[type=\'radio\']:checked, #tab-payment input[type=\'checkbox\']:checked, #tab-payment select, #tab-payment textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-payment-address').button('loading');
		},
		complete: function() {
			$('#button-payment-address').button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			// Check for errors
			if (json['error']) {
				if (json['error']['warning']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				for (i in json['error']) {
					var element = $('#input-payment-' + i.replace('_', '-'));
					
					if ($(element).parent().hasClass('input-group')) {
						$(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
					} else {
						$(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
					}
				}	
								
				// Highlight any found errors
				$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');				
			} else {
				// Payment Methods
				$.ajax({
					url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/payment/methods&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					dataType: 'json',
					beforeSend: function() {
						$('#button-payment-address i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
						$('#button-payment-address').prop('disabled', true);
					},
					complete: function() {
						$('#button-payment-address i').replaceWith('<i class="fa fa-arrow-right"></i>');
						$('#button-payment-address').prop('disabled', false);
					},
					success: function(json) {
						if (json['error']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						} else {
							html = '<option value=""><?php echo $text_select; ?></option>';
							
							if (json['payment_methods']) {
								for (i in json['payment_methods']) {
									if (json['payment_methods'][i]['code'] == $('select[name=\'payment_method\'] option:selected').val()) {
										html += '<option value="' + json['payment_methods'][i]['code'] + '" selected="selected">' + json['payment_methods'][i]['title'] + '</option>';
									} else {
										html += '<option value="' + json['payment_methods'][i]['code'] + '">' + json['payment_methods'][i]['title'] + '</option>';
									}
								}
							}	
							
							$('select[name=\'payment_method\']').html(html);					
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});	
				
				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
								
				// If shipping required got to shipping tab else total tabs
				if ($('select[name=\'shipping_method\']').prop('disabled')) {
					$('a[href=\'#tab-total\']').tab('show');		
				} else {
                    if($('#input-payment-shipping_address').prop('checked')){
                      $('a[href=\'#tab-total\']').tab('show');
                    }else{
                      $('a[href=\'#tab-shipping\']').tab('show');
                    }

				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

// Shipping Address
$('select[name=\'shipping_address\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=sale/customer/address&token=<?php echo $token; ?>&address_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'shipping_address\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('#tab-shipping .fa-spin').remove();
		},		
		success: function(json) {
			// Reset all fields
			$('#tab-shipping input[type=\'text\'], #tab-shipping input[type=\'text\'], #tab-shipping textarea').val('');
			$('#tab-shipping select option').not('#tab-shipping select[name=\'shipping_address\']').removeAttr('selected');
			$('#tab-shipping input[type=\'checkbox\'], #tab-shipping input[type=\'radio\']').removeAttr('checked');
					
			$('#tab-shipping input[name=\'firstname\']').val(json['firstname']);
			$('#tab-shipping input[name=\'lastname\']').val(json['lastname']);
			$('#tab-shipping input[name=\'company\']').val(json['company']);
			$('#tab-shipping input[name=\'address_1\']').val(json['address_1']);
			$('#tab-shipping input[name=\'address_2\']').val(json['address_2']);
			$('#tab-shipping input[name=\'city\']').val(json['city']);
			$('#tab-shipping input[name=\'postcode\']').val(json['postcode']);
			$('#tab-shipping select[name=\'country_id\']').val(json['country_id']);
			
			shipping_zone_id = json['zone_id'];	
			
			$('#tab-shipping select[name=\'country_id\']').trigger('change');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});

var shipping_zone_id = '<?php echo $shipping_zone_id; ?>';

$('#tab-shipping select[name=\'country_id\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=sale/order/country&token=<?php echo $token; ?>&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('#tab-shipping select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('#tab-shipping .fa-spin').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#tab-shipping input[name=\'postcode\']').parent().parent().addClass('required');
			} else {
				$('#tab-shipping input[name=\'postcode\']').parent().parent().removeClass('required');
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == shipping_zone_id) {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('#tab-shipping select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#tab-shipping select[name=\'country_id\']').trigger('change');

$('#button-shipping-address').on('click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/shipping/address&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: $('#tab-shipping input[type=\'text\'], #tab-shipping input[type=\'hidden\'], #tab-shipping input[type=\'radio\']:checked, #tab-shipping input[type=\'checkbox\']:checked, #tab-shipping select, #tab-shipping textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-address').button('loading');
		},
		complete: function() {
			$('#button-shipping-address').button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			// Check for errors
			if (json['error']) {
				if (json['error']['warning']) {
					$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				for (i in json['error']) {
					var element = $('#input-shipping-' + i.replace('_', '-'));
					
					if ($(element).parent().hasClass('input-group')) {
						$(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
					} else {
						$(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
					}
				}
				
				// Highlight any found errors
				$('.text-danger').parentsUntil('.form-group').parent().addClass('has-error');					
			} else {
				// Shipping Methods
				$.ajax({
					url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/shipping/methods&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
					dataType: 'json',
					beforeSend: function() {
						$('#button-shipping-address i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
						$('#button-shipping-address').prop('disabled', true);
					},
					complete: function() {
						$('#button-shipping-address i').replaceWith('<i class="fa fa-arrow-right"></i>');
						$('#button-shipping-address').prop('disabled', false);
					},
					success: function(json) {
						if (json['error']) {
							$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						} else {
							// Shipping Methods
							html = '<option value=""><?php echo $text_select; ?></option>';
							
							if (json['shipping_methods']) {
								for (i in json['shipping_methods']) {
									html += '<optgroup label="' + json['shipping_methods'][i]['title'] + '">';
								
									if (!json['shipping_methods'][i]['error']) {
										for (j in json['shipping_methods'][i]['quote']) {
											if (json['shipping_methods'][i]['quote'][j]['code'] == $('select[name=\'shipping_method\'] option:selected').val()) {
												html += '<option value="' + json['shipping_methods'][i]['quote'][j]['code'] + '" selected="selected">' + json['shipping_methods'][i]['quote'][j]['title'] + ' - ' + json['shipping_methods'][i]['quote'][j]['text'] + '</option>';
											} else {
												html += '<option value="' + json['shipping_methods'][i]['quote'][j]['code'] + '">' + json['shipping_methods'][i]['quote'][j]['title'] + ' - ' + json['shipping_methods'][i]['quote'][j]['text'] + '</option>';
											}
										}		
									} else {
										html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
									}
									
									html += '</optgroup>';
								}
							}
							
							$('select[name=\'shipping_method\']').html(html);	
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});	
				
				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
								
				$('a[href=\'#tab-total\']').tab('show');							
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});				
});

// Shipping Method
$('#button-shipping-method').on('click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/shipping/method&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: 'shipping_method=' + $('select[name=\'shipping_method\'] option:selected').val(),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-method').button('loading');	
		},	
		complete: function() {
			$('#button-shipping-method').button('reset');
		},		
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			
			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			
				// Highlight any found errors
				$('select[name=\'shipping_method\']').parent().parent().parent().addClass('has-error');			
			}
			
			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
			}
		},	
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

// Payment Method
$('#button-payment-method').on('click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/payment/method&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: 'payment_method=' + $('select[name=\'payment_method\'] option:selected').val(),
		dataType: 'json',
		beforeSend: function() {
			$('#button-payment-method').button('loading');
		},	
		complete: function() {
			$('#button-payment-method').button('reset');
		},		
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			
			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			
				// Highlight any found errors
				$('select[name=\'payment_method\']').parent().parent().parent().addClass('has-error');				
			}
			
			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});		
});

// Coupon
$('#button-coupon').on('click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/coupon&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: $('input[name=\'coupon\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-coupon').button('loading');
		},	
		complete: function() {
			$('#button-coupon').button('reset');
		},		
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			
			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			
				// Highlight any found errors
				$('input[name=\'coupon\']').parent().parent().parent().addClass('has-error');				
			}
			
			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});		
});

// Voucher
$('#button-voucher').on('click', function() {
	$.ajax({
		url: 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/voucher&store_id=' + $('select[name=\'store_id\'] option:selected').val(),
		type: 'post',
		data: $('input[name=\'voucher\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-voucher').button('loading');
		},	
		complete: function() {
			$('#button-voucher').button('reset');
		},		
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			
			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				// Highlight any found errors
				$('input[name=\'voucher\']').parent().parent().parent().addClass('has-error');			
			}
			
			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				
				// Refresh products, vouchers and totals
				$('#button-refresh').trigger('click');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});		
});

// Affiliate
$('input[name=\'affiliate\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=marketing/affiliate/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				json.unshift({
					affiliate_id: 0,
					name: '<?php echo $text_none; ?>'
				});
								
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['affiliate_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'affiliate\']').val(item['label']);
		$('input[name=\'affiliate_id\']').val(item['value']);		
	}	
});


// Checkout
$('#button-save').on('click', function() {
	var order_id = $('input[name=\'order_id\']').val();
	if (order_id == 0) {
		var url = 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/order/add&data=backend&store_id=' + $('select[name=\'store_id\'] option:selected').val();
	} else {
		var url = 'index.php?route=sale/order/api&token=<?php echo $token; ?>&api=api/order/edit&data=backend&store_id=' + $('select[name=\'store_id\'] option:selected').val() + '&order_id=' + order_id;
	}
    var value = 0;
    if ($('#c-form-check').prop("checked")) {
      $('#tab-total input[name=\'cform_submit\']').val(1);
    } else {
      $('#tab-total input[name=\'cform_submit\']').val(0);
    }

	$.ajax({
		url: url,
		type: 'post',
		data: $('#tab-total select[name=\'order_status_id\'], #tab-total select, #tab-total textarea[name=\'comment\'], #tab-total input[name=\'affiliate_id\'], #tab-total input[name=\'cform_submit\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-save').button('loading');	
		},	
		complete: function() {
			$('#button-save').button('reset');
		},		
		success: function(json) {
          alert(json);
			$('.alert, .text-danger').remove();
			
			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			
			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '  <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			
			if (json['order_id']) {
				$('input[name=\'order_id\']').val(json['order_id']);
			}			
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});		
});

$('#content').delegate('button[id^=\'button-upload\'], button[id^=\'button-custom-field\'], button[id^=\'button-payment-custom-field\'], button[id^=\'button-shipping-custom-field\']', 'click', function() {
	var node = this;
	
	$('#form-upload').remove();
	
	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');
	
	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}
	
	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			
			$.ajax({
				url: 'index.php?route=tool/upload/upload&token=<?php echo $token; ?>',
				type: 'post',		
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,		
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},		
				success: function(json) {
					$(node).parent().find('.text-danger').remove();
					
					if (json['error']) {
						$(node).parent().find('input[type=\'hidden\']').after('<div class="text-danger">' + json['error'] + '</div>');
					}
								
					if (json['success']) {
						alert(json['success']);
					}
					
					if (json['code']) {
						$(node).parent().find('input[type=\'hidden\']').attr('value', json['code']);
					}
				},			
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});	
//--></script> 
  <script type="text/javascript">
// Sort the custom fields
$('#tab-customer .form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-customer .form-group').length) {
		$('#tab-customer .form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('#tab-customer .form-group').length) {
		$('#tab-customer .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('#tab-customer .form-group').length) {
		$('#tab-customer .form-group:first').before(this);
	}
});

// Sort the custom fields
$('#tab-payment .form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-payment .form-group').length) {
		$('#tab-payment .form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('#tab-payment .form-group').length) {
		$('#tab-payment .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('#tab-payment .form-group').length) {
		$('#tab-payment .form-group:first').before(this);
	}
});

$('#tab-shipping .form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-shipping .form-group').length) {
		$('#tab-shipping .form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('#tab-shipping .form-group').length) {
		$('#tab-shipping .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('#tab-shipping .form-group').length) {
		$('#tab-shipping .form-group:first').before(this);
	}
});

$(document).delegate('.remove_btn' , 'click', function () {
  $(this).parents('.remove_size').remove();
});

  </script>
<script>
  $('#free-ship').click(function(){
    //alert('hello');
     var free_ship = $('#free-ship').attr('data-html');
    //alert(free_ship);
     document.getElementById("input-coupon").value = free_ship;

  });

  $('#free-two').click(function(){
    var free_two = $('#free-two').attr('data-html');
    document.getElementById("input-coupon").value = free_two;
  });

  $('#c-form-check').change(function (){
    if (this.checked) {
      $('#cform-submit').css("display", "table");
      $('#cform-no-submit').css("display", "none");
    } else {
      $('#cform-submit').css("display", "none");
      $('#cform-no-submit').css("display", "table");
    }
  });

</script>
</div>
<?php echo $footer; ?>
