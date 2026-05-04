<?php
/*
 * Class CartController: provide api for cart operations (edit,update,remove from cart etc)
 * @Author: Devendra Dhayal, Date: 15-05-2017
*/


require_once(__DIR__.'/system.php');

require_once(DIR_SYSTEM . 'library/cart.php');
require_once(DIR_SYSTEM . 'library/weight.php');
require_once(DIR_SYSTEM . 'library/wsb.php');
require_once(DIR_SYSTEM . 'library/total/totalfactory.php');
require_once(DIR_SYSTEM . 'library/seller/seller_info.php');
require_once(DIR_SYSTEM . 'library/sales_staff.php');
require_once(DIR_SYSTEM . 'library/restapi.php');
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/upi.php');

    class CartController extends SystemController
    {
        public function __construct($params) {

            parent::__construct($params);

            // Currency
            //$this->registry->set('currency', new Currency($this->registry));

            // Tax
            //$this->registry->set('tax', new Tax($this->registry));

            // Weight
            $this->registry->set('weight', new Weight($this->registry));

            // WSB common library
            $this->registry->set('wsb', new Wsb($this->registry));
            
            // Customer
            //$this->registry->set('customer', new Customer($this->registry));

            // Cart
            $this->registry->set('cart',new Cart($this->registry));
        }
        /*
        * Private method : cartResponse
        * @Return: cart data(products and order summary)
        * @Author: Devendra Dhayal, Date: 15-05-2017
        */
        private function __cartResponse( $auto_apply_referral_coupon = false ){
            $json = array();
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $this->load->model('tool/upload');
            $this->load->language('checkout/cart');

            unset($this->session->data['shipping_method']);
            unset($this->session->data['payment_method']);

            $json['status'] = 'success';
            $json['status_text'] = $this->language->get('text_remove');
            $json['cart_error_show'] = 0;

            if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])){
                $json['empty'] = '0';
                $json['products'] = array();


                $products = $this->cart->getProducts('',true);
                
                /*Remove minimum Purchase price Limit for dropshipper (Ravindra Singh 02-02-2016) */
                if(isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1){
                    $this->config->set('config_customer_cart_limit',$this->config->get('config_cart_limit'));
                    $this->config->set('config_cart_limit',$this->config->get('config_dropshipper_cart_limit'));
                }
                $json['coupon'] = '';
                /// If store voucher applied then setting cart limit to 0
                $coupon_data = $this->cart->getCoupon();
                if( !empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code']) ){
                    $this->config->set('config_customer_cart_limit',0);
                    $this->config->set('config_cart_limit',0);
                    $json['coupon'] = $coupon_data['wsb_store_voucher'];
                }
                else if( !empty($coupon_data['coupon_store_trial']) ){ // store trial coupon
                    $this->config->set('config_customer_cart_limit',0);
                    $this->config->set('config_cart_limit',0);
                    $json['coupon'] = $coupon_data['coupon_store_trial'];
                }
                else if( !empty($coupon_data['coupon_franchise']) || !empty($coupon_data['coupon_franchise_cash']) || !empty($coupon_data['coupon_franchise_credit']) ){ // for order by franchise
                    $this->config->set('config_customer_cart_limit',0);
                    $this->config->set('config_cart_limit',0);
                    $json['coupon'] = array_values($coupon_data)[0];
                }
                else if (!empty($coupon_data['coupon_remove_cart_limit'])) {
                    $this->config->set('config_customer_cart_limit',0);
                    $this->config->set('config_cart_limit',0);
                    $json['coupon'] = $coupon_data['coupon_remove_cart_limit'];
                }
                else if(!empty($coupon_data)){
                    $json['coupon'] = array_values($coupon_data)[0];
                }

                if ( $auto_apply_referral_coupon ) {

                    // auto apply referral coupon
                    $this->load->model('checkout/coupon');
                    $applied_coupon_code = $this->model_checkout_coupon->autoApplyReferralCoupon( (int) $this->customer->getId() );
                    if ( !empty( $applied_coupon_code )) {
                        $json['coupon'] = $applied_coupon_code;
                    }
                }

                if ($this->config->get('config_cart_weight')) {
                    $json['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
                } else {
                    $json['weight'] = '';
                }


                $this->load->model('catalog/product');

                $total_sets = 0;
                $total_pieces = 0;

                $total_out_of_stock_products = 0;
                $total_quantity_reduced_products = 0;
                $total_moq_error_products = 0;

                // set defaults
                $json['error_cart_minimum'] = '';
                $this->session->data['error_cart_minimum'] = false;
                $json['disable_place_order'] = 0;
                $json['text_cart_minimum'] = '';

                $product_moq_error = array();

                foreach ($products as $product) {
                    $product_total = 0;

                    foreach ($products as $product_2) {
                        if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                            $product_total += $product_2['quantity'];
                        }
                    }

                    if ($product['minimum'] > $product_total) {
                        $json['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
                        $json['cart_error_show'] = 1;
                    }
                    if($product['status'] == 0){
                        $json['error_warning'] = $this->language->get('error_stock');
                        $json['cart_error_show'] = 1;
                    }

                    if ($product['image']) {
                        $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
                        $img_width = $this->config->get('config_image_cart_width');
                        $img_height = $this->config->get('config_image_cart_height');
                    } else {
                        $image = '';
                        $img_width = '';
                        $img_height = '';
                    }

                    $option_data = array();

                    foreach ($product['option'] as $option) {
                        if ($option['type'] != 'file') {
                            $value = $option['value'];
                        } else {
                            $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                            if ($upload_info) {
                                $value = $upload_info['name'];
                            } else {
                                $value = '';
                            }
                        }

                        if ($option['image']) {
                            $image = $this->model_tool_image->resize($option['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
                        }

                        $option_data[] = array(
                            'name'  => $option['name'],
                            'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                        );
                    }

                    $piece_in_set = $product['piece_in_set'];

                    // Display per piece prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $price_per_piece = $this->currency->format($this->tax->calculate($product['price']/$piece_in_set, $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
                    } else {
                        $price_per_piece = false;
                    }

                    $moq_error = false;
                    if($product['stock'] == true && $product['minimum'] > $product_total ){
                        $moq_error = true;
                        $total_moq_error_products += 1;
                        if( !isset($product_moq_error[$product['product_id']]) ) {
                            $json['text_cart_minimum'] = $json['text_cart_minimum'] . sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']) . '<br/><br/>';
                            $json['cart_error_show'] = 1;
                            $json['disable_place_order'] = 1;
                            $this->session->data['error_cart_minimum'] = true;
                            $product_moq_error[$product['product_id']] = true;
                        }
                    }

                    $quantity_reduced = false;
                    if( 
                      $product['donot_have_enough_qty'] == true &&
                                        $product['stock_quantity'] &&  // quantity should be greater than zero
                                        $product['product_id'] == $product['combo_product_id'] // should not be combo product
                    ){
                        $product['tax'] = $product['tax']*$product['stock_quantity']/$product['quantity'];
                        $product['quantity'] = $product['stock_quantity'];
                        $product['total'] = $product['price'] * $product['quantity'];
                        $this->cart->update($product['key'], $product['stock_quantity']);
                        $total_quantity_reduced_products += 1;
                        $product['stock']= true;
                        $quantity_reduced = true;
                    }


                    // Display per set prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
                    } else {
                        $price = false;
                    }


                    // Display total prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $unformat_total = $product['total'];
                        $total = $this->currency->format($product['total']);
                    } else {
                        $total = false;
                        $unformat_total = false;
                    }

                    $recurring = '';

                    if ($product['recurring']) {
                        $frequencies = array(
                            'day'        => $this->language->get('text_day'),
                            'week'       => $this->language->get('text_week'),
                            'semi_month' => $this->language->get('text_semi_month'),
                            'month'      => $this->language->get('text_month'),
                            'year'       => $this->language->get('text_year'),
                        );

                        if ($product['recurring']['trial']) {
                            $recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
                        }

                        if ($product['recurring']['duration']) {
                            $recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                        } else {
                            $recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                        }
                    }

                    if ($this->customer->isLogged())
                        $previously_ordered = $this->model_catalog_product->checkPreviouslyOrdered($this->customer->getId(), $product['product_id']);
                    else
                        $previously_ordered = false;

                    if($product['stock'] == false)
                        $total_out_of_stock_products += 1;
                    else{
                        $total_sets += (int)($product['quantity']);
                        $total_pieces += (int)($product['piece_in_set'] * $product['quantity']);
                    }

                    $non_returnable = $product['non_returnable'] ?? false;

                    $json['products'][$product['pickup_city']][$product['key']][] = array(
                        'key'       => $product['key'],
                        'product_id'=> $product['product_id'],
                        'rating'    => $product['rating'],
                        'thumb'     => $image,
                        'name'      => $product['name'],
                        'is_sor_enabled'          => $product['is_sor_enabled'],
                        'sor_enabled_text'        => $product['sor_enabled_text'],
                        'sor_enabled_detail_text' => $product['sor_enabled_detail_text'],
                        'model'     => $product['model'],
                        'status'     => $product['status'],
                        'set_description'     => $product['set_description'],
                        'option'    => $option_data,
                        'recurring' => $recurring,
                        'quantity'  => $product['quantity'],
                        'minimum'  => $product['minimum'],
                        'stock_quantity'=>$product['stock_quantity'],
                        'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
                        'price_per_piece' => $price_per_piece,
                        'piece_in_set'=> $product['piece_in_set'] * $product['quantity'],
                        'price'     => $price,
                        'total'     => $total,
                        'unformat_total' => $unformat_total,
                        'tax'       => $this->currency->format($product['tax']),
                        'weight'    => $product['weight'],
                        //'href'      => str_replace('&amp;', '&',$this->url->link('product/product', 'product_id=' . $product['product_id'])),
                        'href' => $this->url->rewrite_keyword('product_id', $product['product_id'], 'SSL'),
                        'sellers'  => array($product['seller_id'] => array('price' => $product['selling_price'],'total' => $total)),
                        'previously_ordered' => $previously_ordered,
                        'is_single' => $product['is_single'],
                        'cod_available' => $product['cod_available'],
                        'wrong_store' => $product['wrong_store'],
                        'store_pickup' => $product['store_pickup'],
                        'img_width' => $img_width,
                        'img_height' => $img_height,
                        'comment'  => $product['comment'],
                        'non_returnable' => $non_returnable,
                        'exp_dispatch_date' => $product['exp_dispatch_date'],
                        'quantity_reduced' => $quantity_reduced,
                        'moq_error' => $moq_error,
                        'wishlist' => $this->customer->checkExistingWishlistItem($product['combo_product_id']),
                        'error_store_limit' => $product['error_store_limit'],
                        'error_store_limit_msg' => $product['error_store_limit_msg'],
                        'base_unit' => $product['base_unit'],
                        'super_unit' => $product['super_unit'],
                        'tax_rate' => 'GST('.$product['output_tax_rates'].'%)',
                        'pickup_city' =>$product['seller_city'],
                        'combo_product_id' => $product['combo_product_id']
                    );
                    $json['sellers'][][$product['seller_id']] = array(
                        'price' => $product['selling_price'],
                        'total'   => $total
                    );
                    $json['clear_cart'][$product['pickup_city']][] = $product['key'];


                }
                $json['total_sets'] = $total_sets;
                $json['total_pieces'] = $total_pieces;

                $json['total_out_of_stock_products'] = $total_out_of_stock_products;
                $json['total_quantity_reduced_products'] = $total_quantity_reduced_products;
                $json['total_moq_error_products'] = $total_moq_error_products;

                $total_data = array();
                $total = 0;

                // Display prices
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    // Shift total code in library By Sudhanshu
                    $total_factory = new TotalFactory( $this, true);
                    $total_data = $total_factory->getTotal( true, $this->session->data['free_shipping_coupon_applied'] ?? false );
                    $total = $total_factory->total;
                    $taxes = $total_factory->taxes;
                    $cst = $total_factory->cst;

                }

                $json['totals'] = array();
                $tax_in_totals = false;

                $cartTotal = 0.0;
                $cartSubTotal = 0.0;
                $store_credit = 0.0;

                /**Discount on Taxes with coupon code (Ravindra Singh 16-02-2016) End**/
                foreach ($total_data as $total) {

                    if ($total['code'] == 'sub_total') {
                        $total['title'] = $this->language->get('text_subtotal');
                        $cartSubTotal = $total['value'];
                    } elseif ($total['code'] == 'total') {
                        $total['title'] = $this->language->get('text_total_amount');
                        $cartTotal = $total['value'];
                    } elseif ($total['code'] == 'credit') {
                        $store_credit = $total['value'];
                    } elseif ($total['code'] == 'tax') {
                        $tax_in_totals = true;
                        $this->session->data['cst'] = $cst;
                        $this->session->data['tax_refund'] = $total['value'] - $cst; // Refund will be total tax - cst value
                        $json['cst'] = $this->currency->format($cst);
                        $json['tax_refund'] = $this->currency->format($total['value'] - $cst);  // Refund will be total tax - cst value
                    }

                    /**Validate discount on free shipping (Ravindra Singh 05-02-2016) Start**/
                    if(($total['code'] == "shipping" && $total['code'] == "0") || $total['title'] == "Free Shipping"){
                        $freeshipflag = 1;
                    }
                    /**Validate discount on free shipping (Ravindra Singh 05-02-2016) End**/

                    $json['totals'][$total['code']] = array(
                        'title' => $total['title'],
                        'value' => $total['value'],
                        'show_value' => $total['value'],
                        'text'  => $this->currency->format($total['value'])
                    );

                }
                
                // cart limit 
                $t_cart_limit = $this->config->get('config_cart_limit');
                if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
                  $t_cart_limit = $this->currency->convertLiveRates($t_cart_limit, 'USD', $this->currency->getCode());
                }

                $sum_cart_credit = $cartSubTotal; //$cartTotal + abs($store_credit);
                $exception_customer_ids = array(52);
                if ( $this->customer->isLogged()
                    and in_array($this->customer->getId(), $exception_customer_ids) ) {}
                else {
                    if ( $sum_cart_credit*$this->currency->getValue() == 0 || $sum_cart_credit*$this->currency->getValue() < (float)($t_cart_limit) ) {
                        $this->session->data['error_cart_minimum'] = true;
                        $json['cart_error_show'] = 1;
                        $json['disable_place_order'] = 1;
                        $json['text_cart_minimum'] = $json['text_cart_minimum'] . sprintf($this->language->get('error_cart_minimum'),
                            $this->currency->format( round($t_cart_limit),
                                $this->currency->getCode(), 1, true, 0 ));
                    }
                }

                //customer data

                // get customer upi vpa 
                $upi = new Upi($this);
                $customer_vpa = "";

                $customer_vpa_result = $upi->getCustomerVPA($this->customer->getId());
                if(!empty($customer_vpa_result['upi_vpa'])) {
                        $customer_vpa = $customer_vpa_result['upi_vpa'];
                }
                $telephone = $this->customer->getTelephone();
                $json['customer_data'] = array(
                    "customer_id" => $this->customer->getId(),
                    "customer_vpa" => $customer_vpa,
                    "is_dropshipper" => $this->customer->is_dropshipper,
                    "gst_number" => $this->customer->getGSTNumber(),
                    "address_id" => $this->customer->getAddressId(),
                    "gst_option" => 0,
                    "telephone" => !empty($telephone)?$telephone:""
                );

                $json['cartlimitcross'] = 0;
                if ( $this->config->get('config_customer_cart_limit') < $cartTotal)
                    $json['cartlimitcross'] = 1;

            }
            else{
                $json['empty'] = '1';
            }
            
            if($json['empty'] != '1')
            {
                $surface_shipping =  SURFACE_FREE_SHIPPING-$json['totals']['sub_total']['value'];
                $json['surface_shipping'] = ($surface_shipping > 0) ? $this->currency->format($surface_shipping) : 0;
            } else {
                $json['surface_shipping'] = SURFACE_FREE_SHIPPING;
            }
            //print_r($json);die;
            return $json;
        }

        /*
        * Method getCart
        * @Return: cart Data
        * @Author: Devendra Dhayal, Date: 15-05-2017
        */
        public function getCart(){
            
            $json = $this->__cartResponse( true );

            $language = array();
            $this->load->autoLoadLanguage('checkout/cart', $language);
            if((int)$this->config->get('config_cart_limit') == 0){
                $language['text_cart_minimum']="";
            }
            else{
                $language['text_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'),
                    $this->currency->format( (float)($this->config->get('config_cart_limit')),
                        $this->currency->getCode(), 1 ));
            }
            $json['language'] = $language;

            $this->load->autoLoadLanguage('checkout/cart', $json);

            if(!$this->customer->isLogged())
                $json['checkout'] = $this->url->link('account/login', 'referrers=cart', 'SSL');
            else
                $json['checkout'] = $this->url->link('checkout/one_page_checkout', '', 'SSL','delivery');

            $json['show_cform_option'] = false;

            if($json['empty'] == '1'){
                $cart_data = array();
                $cart_summary = array();
            }
            else{
                $cart_summary = array(
                    "totals"                            => $json['totals'],
                    "disable_place_order"               => $json['disable_place_order'],
                    "text_cart_minimum"                 => $json['text_cart_minimum'],
                    "tax_refund"                        => (isset($json['tax_refund'])?$json['tax_refund']:0),
                    "checkout"                          => $json['checkout'],
                    "show_cform_option"                 => $json['show_cform_option'],
                    "total_out_of_stock_products"       => $json['total_out_of_stock_products'],
                    "total_quantity_reduced_products"   => $json['total_quantity_reduced_products'],
                    "price_in_rupees"                   => ''
                );
                $cart_data = array(
                    "products"      => $json['products'],
                    "total_sets"    => $json['total_sets'],
                    "total_pieces"  => $json['total_pieces'],
                    "clear_cart"    => $json['clear_cart'],
                    "cartlimitcross"    => $json['cartlimitcross'],
                    "weight"        => $json['weight']
                );
            }

            $data = array(
                "customer" => isset($json['customer_data']) ? $json['customer_data'] : array(),
                "coupon"   => isset($json['customer_data']) ? $json['coupon'] : '',
                "cart_data" => $cart_data,
                "surface_shipping" => $json['surface_shipping'],
                "cart_summary" => $cart_summary,
                "language" => $language,
                "is_empty" => $json['empty'],
                "checkout" => $this->url->link('checkout/one_page_checkout', '', 'SSL','delivery')
            );
           
            return $data;

        }

        /*
        * Method remove: remove product from cart
        * @Request type: POST
        * @Params: product_key
        * @Return: cart data
        * @Author: Devendra Dhayal, Date: 15-05-2017
        */
        public function remove(){
            $json = array();
            if (($this->method == 'POST')) {

                if ( isset($this->request['key']) ) {
                    $this->cart->remove($this->request['key']);
                    $this->_updateLead(0);
                    return $this->__cartResponse();
                }
                else{
                    $json['status']='error';
                    $json['error_msg'] = 'Product Key is not present in post data.';
                }
            }
            else{
                $json['status']='error';
                $json['error_msg'] = 'Request type should be post.';
            }
            return $json;
        }

        /*
        * Method update: update the quantity of product in cart
        * @Request type: POST
        * @Params: product_key,quantity
        * @Return: cart data
        * @Author: Devendra Dhayal, Date: 15-05-2017
        */
        public function update(){
            $json = array();
            if (($this->method == 'POST')) {
   
                if ( isset($this->request['key']) && isset($this->request['quantity']) ) {
                    $this->cart->update($this->request['key'],$this->request['quantity']);
                    $this->_updateLead();
                    return $this->__cartResponse();
                } 
                else{
                    $json['status']='error';
                    $json['error_msg'] = 'Product Key is not present in post data.';
                }
            }
            else{
                $json['status']='error';
                $json['error_msg'] = 'Request type should be post.';
            }
            return $json;
        }

        public function addToWishlist(){
            $json = array();
            if (($this->method == 'POST')) {

                $this->load->language('account/wishlist');
                
                if ( isset($this->request['product_id']) ) {
                   
                   $this->load->model('catalog/product');
                   $product_info = $this->model_catalog_product->getProduct($this->request['product_id']);  
                   $json = array();
                   //$json = $this->__cartResponse();

                  if ( !$this->customer->checkExistingWishlistItem($this->request['product_id']) ) 
                  {
                      $this->customer->processWishlist($this->request['product_id']);

                      if ($this->customer->isLogged()) {
                                    $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
                            } else {
                                    $json['info'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'), $this->url->link('product/product', 'product_id=' . (int)$this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
                            }
                  }
                  else
                  {
                    $json['info'] = sprintf($this->language->get('text_exists'), $this->url->link('product/product', 'product_id=' . (int)$this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
                  }   
                   
                    $json['total_wishlist_items'] = $this->customer->getTotalWishlists();
                    return $json;
                }
                else{
                    $json['status']='error';
                    $json['error_msg'] = 'Product id is not present in post data.';
                }
            }
            else{
                $json['status']='error';
                $json['error_msg'] = 'Request type should be post.';
            }
            return $json;
        }

        /*
        * Method clearCart: clear the cart
        * @Request type: POST
        * @Author: Devendra Dhayal, Date: 15-05-2017
        */
        public function clearCart(){
            $this->cart->clear();
            $this->_updateLead(0);
            $json = array();
            $json['empty']='1';
            return $json;
        }

        /*
        * Method clearPickupCityCart: remove the products belong to a particular city from cart
        * @Request type: POST
        * @Params: product_keys
        * @Return: cart data
        * @Author: Devendra Dhayal, Date: 15-05-2017
        */
        public function clearPickupCityCart(){
            $json = array();
            if (($this->method == 'POST')) {

                if ( isset($this->request['keys']) ) {
                    if(isset($this->request['add_to_wishlist']) && (int)$this->request['add_to_wishlist'] == 1){
                        $keys = explode(',',$this->request['keys']);
                        foreach($keys as $key){
                            $product = unserialize(base64_decode($key));
                            $this->customer->processWishlist($product['product_id']);
                        }
                    }
                    $this->cart->removeProducts($this->request['keys']);
                    $this->_updateLead();
                    return $this->__cartResponse();
                }
                else{
                    $json['status']='error';
                    $json['error_msg'] = 'Product Keys is not present in post data.';
                }
            }
            else{
                $json['status']='error';
                $json['error_msg'] = 'Request type should be post.';
            }
            return $json;
        }

        /*
        * Method addComment: add comment to product in cart
        * @Request type: POST
        * @Params: product_key,comment
        * @Return: cart data
        * @Author: Devendra Dhayal, Date: 15-05-2017
        */
        public function addComment(){
            $json = array();
            if (($this->method == 'POST')) {

                if ( isset($this->request['key']) && isset($this->request['comment']) ) {
                    $this->cart->addComment($this->request['key'],$this->request['comment']);
                    return $this->__cartResponse();
                }
                else{
                    $json['status']='error';
                    $json['error_msg'] = 'Product Key or comment is not present in post data.';
                }
            }
            else{
                $json['status']='error';
                $json['error_msg'] = 'Request type should be post.';
            }
            return $json;
        }

        /*
        * Method add: add product to cart
        * @Request type: POST
        * @Params: product_id,quantity
        * @Author: Devendra Dhayal, Date: 15-05-2017
        */
    public function add(){
            $json = array();
            if (($this->method == 'POST')) {
                if ( isset($this->request['product_id']) && isset($this->request['quantity']) ) {

                    $this->load->model('catalog/product');
                    $product_id = $this->request['product_id'];
                    
                    $product_option_info = $this->model_catalog_product->getProductOptionInfo($product_id);
                    if(!empty($product_option_info)){
                        if((int)$product_option_info['required'] == 1){
                            $json['status']='error';
                            $json['error_msg'] = 'Product option is required.';
                            return $json;
                        }
                    }
                    $combo_id = $this->model_catalog_product->getComboProductIdOfAssociate($product_id);
                    if (!empty($combo_id)) {
                      // means this product is associate, so we will replace product id with combo product id 
                      $product_id = $combo_id;
                    }
                    $this->cart->add($product_id,$this->request['quantity']);
                    $this->_updateLead();
                    return $this->__cartResponse();
                } 
                else{
                    $json['status']='error';
                    $json['error_msg'] = 'Product id or qunatity is not present in post data.';
                }
            }
            else{
                $json['status']='error';
                $json['error_msg'] = 'Request type should be post.';
            }
            return $json;
        }
        
        
        /*
        * Method addCartWith Option: add c form 
        * @Request type: POST
        * @Params: cform_submit
        * @Author: Amarat, Date: 03-07-2017
        */
        
        public function addWithOptions() {
                
        $json = array();
                //echo "<pre>"; print_r($this->request); die;     
        if (isset($this->request['product_id'])) {
            $product_id = (int)$this->request['product_id'];
        } else {
            $product_id = 0;
        }
                
                
        if (isset($this->request['option_quantities'])) {
            $option_quantities = $this->request['option_quantities'];
        } else {
            $option_quantities = 0;
        }
        if (isset($this->request['option_max_quantities'])) {
            $option_max_quantities = $this->request['option_max_quantities'];
        } else {
            $option_max_quantities = 0;
        }
                

        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if ($option_quantities) {


                $product_options = $this->model_catalog_product->getProductOptions($this->request['product_id']);
                                   // echo "<pre>"; print_r($product_options); die;
                                foreach ($product_options as $product_option) {

                    $option_arr = $option_quantities[$product_option['product_option_id']];
                    $option_max_arr =  $option_max_quantities[$product_option['product_option_id']];

                    $res = array_map(array($this, 'compare_max_option_qty'), $option_arr, $option_max_arr);
                    $keys = array_keys($option_arr);

                    $net_arr = array_combine($keys, $res);
                    if (array_filter($net_arr)) {
                        $json['error']['option_value'][$product_option['product_option_id']] = array_filter($net_arr);
                    }

                    if ($product_option['required'] && !array_filter($option_quantities[$product_option['product_option_id']]) ) {
                        $json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_options_quantity_required'), $product_option['name']);
                    }
                }


            }

            $recurring_id = 0;
            if (!$json) {
                foreach ($option_quantities as $option_value => $value_arr) {
                    foreach ($value_arr as $option__value_id => $quantity) {
                        if ($quantity) {
                            $this->cart->add($this->request['product_id'], $quantity, array($option_value=>$option__value_id ), $recurring_id);
                        }
                    }
                }
                                /*
                if ($data['popup'] == true) {
                    $json['success'] = sprintf($this->language->get('text_success_cart_added_popup'), $product_info['name']);
                }else{
                    $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request['product_id'], 'SSL'), $product_info['name'], $this->url->link('checkout/cart', '', 'SSL'));
                }
                                 * 
                                 */

                unset($this->session->data['shipping_method']); /* Shipping method should not reset everytime */
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);

                // Totals
                $this->load->model('extension/extension');

                $total_data = array();
                $total = 0;
                $taxes = 0;

                // Display prices
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $total_factory = new TotalFactory( $this, true );
                    $total_data = $total_factory->getTotal();
                    $total = $total_factory->total;
                    $taxes = $total_factory->taxes;
                    $cst = $total_factory->cst;
                }
                $json['total_in_cart'] = $this->cart->countProducts();
                $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
                $json['cart_popup'] = 'index.php?route=checkout/cart&popup=true';
            } else {
                $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request['product_id'], 'SSL'));
            }
        }
               
                return $json; 
    }

        
        public function compare_max_option_qty($option_quantity, $option_max_quantity)
    {
        if ($option_quantity > $option_max_quantity) {
            return 1;
        } else {
            return 0;;
        }
    }
        
        
        /*
        * Method add: add product to cart
        * @Request type: POST
        * @Params: product_id,quantity
        * @Author: Mahaveer, Date: 15-05-2017
        */
        public function add_item(){
            $json = array();
            if (($this->method == 'POST')) {
                
                if ( isset($this->request['product_id']) && isset($this->request['quantity']) ) {

                    $this->load->model('catalog/product');
                    $product_id = $this->request['product_id'];

                    $product_option_info = $this->model_catalog_product->getProductOptionInfo($product_id);
                    if(!empty($product_option_info)){
                        if((int)$product_option_info['required'] == 1){
                            $json['status']='error';
                            $json['error_msg'] = 'Product option is required.';
                            $total_sets = $this->cart->countProducts();
                            $json['total_in_cart'] = $total_sets;
                            return $json;
                        }
                    }
                    
                    $combo_id = $this->model_catalog_product->getComboProductIdOfAssociate($product_id);
                    if (!empty($combo_id)) {
                      // means this product is associate, so we will replace product id with combo product id 
                      $product_id = $combo_id;
                    }

                    $this->cart->add($product_id,$this->request['quantity']);
                    $this->_updateLead();
                    $total_sets = $this->cart->countProducts();
                    $json['total_in_cart'] = $total_sets;
                } 
                else{
                    $json['status']='error';
                    $json['error_msg'] = 'Product id or qunatity is not present in post data.';
                }
            }
            else{
                $json['status']='error';
                $json['error_msg'] = 'Request type should be post.';
            }
            return $json;
        }

        public function downloadCartAsCSV($customer_id){

        }

        /*
         * Method to apply coupon on cart.
        */
        public function applyCoupon() {
            $this->load->language('checkout/coupon');

            $json = array();

            $this->load->model('checkout/coupon');
            $this->load->model('setting/setting');

            if (isset($this->request['coupon'])) {
                $coupon = $this->request['coupon'];
            } else {
                $coupon = '';
            }

            if(!empty($coupon)){
                $coupon_data = array();

                $coupon_info = $this->model_checkout_coupon->getCoupon( $coupon );

                if( $store_code = SalesStaff::checkStoreVoucher( $this->db , $coupon ) ) {
                    $coupon_data['wsb_store_voucher'] = $coupon;
                    $coupon_data['wsb_store_code'] = $store_code;
                }
                else if( $store_code = SalesStaff::checkStoreDelivery( $this->db , $coupon ) ) {
                    $coupon_data['wsb_store_delivery'] = $coupon;
                    $coupon_data['wsb_store_code'] = $store_code;
                }
                else if ( $coupon_info['coupon_message']['status'] ) {
                    $coupon_data['coupon'] = $coupon;
                    $coupon_data['coupon_type'] = $coupon_info['type'];
                }
                else if($franchise_discount = $this->model_checkout_coupon->getFranchiseCoupon($coupon,$this->customer->getId())) {
                    if ($this->customer->isFranchise()) {
                        $coupon_data['coupon_franchise'] = $coupon;
                        $coupon_data['franchise_discount'] = $franchise_discount;
                    } else {
                        $json['error'] = $this->language->get('error_coupon');
                    }
                }else {
                    $coupon_key = $this->model_checkout_coupon->getCouponFromSetting($coupon);
                    if (!empty($coupon_key) && !in_array($coupon_key, array('coupon_franchise_cash', 'coupon_franchise_credit'))) {
                        $coupon_data[$coupon_key] = $coupon;
                    } else {
                        $json['error'] = $coupon_info['coupon_message']['message'] ?? $this->language->get('error_coupon');
                    }
                }
                if(!isset($json['error'])){
                    $this->cart->setCoupon( $coupon_data );
                    $this->session->data['success'] = $this->language->get('text_success');
                    $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
                }
            }
            else{
                $json['error'] = $this->language->get('error_empty');
            }

            if(!isset($json['error']))
                return $this->__cartResponse();
            else
                return $json;
        }

        /*
        * Method to remove coupon on cart.
       */
        public function removeCoupon() {

            $this->cart->setCoupon( '' );

            /**
             * remove free shipping coupon from session if applied
             */
            if ( isset($this->session->data['free_shipping_coupon_applied'])) {
                unset( $this->session->data['free_shipping_coupon_applied'] );
            }

            return $this->__cartResponse();

        }

    
        public function user_comment() {

        $this->load->model('catalog/product');
        $this->load->language('product/search');

        //$customer_id = $this->request->post['user_id'];
        $customer_id = $this->customer->getId();
        $product_id = $this->request['product_id'];
        $product_status = $this->request['product_status'];
        $popup_comment = $this->request['popup_comment'];

        $data_user_comment = array(
            'customer_id' => $customer_id,
            'product_id' => $product_id,
            'product_status' => $product_status,
            'popup_comment' => $popup_comment
        );

        $getInformation = $this->model_catalog_product->user_comment($data_user_comment);

        $message  = sprintf($this->language->get('mail_message'),
                                $getInformation['customer_name'],
                                $getInformation['mobile_no'],
                                $getInformation['product_name'],
                                $getInformation['product_model'],
                                $getInformation['product_status'],
                                $getInformation['user_comment']
                            ) . "\n\n";

        $subject = $this->language->get('mail_subject');
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->SMTPSecure = 'ssl';
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
        $mail->addReplyTo(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
        $mail->addAddress(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
        $mail->Subject = $subject;
        $mail->msgHTML($message);
        $mail->send();

        $json = array(
            'product_id' => $product_id,
            'message'=> $this->language->get('want_design_message')
        );
          
         return $json; 

        }
        /*
         * Method to update CRM Lead on update of cart.
        */
        private function _updateLead($is_cart = 1){
            if(!$this->customer->isLogged())
                return;
            $cart_data = '';
            $cart = $this->cart->get_customer_cart('customer_id = '.$this->customer->getId());
            
            if(!empty($cart)){
                $cart_data = $cart[0]['cart_data'];
            } else {
                $is_cart = 0;
            }

            $this->load->model('lead/lead');
            $telephone = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id =".$this->customer->getId() )->row['telephone'];
            if (!empty($telephone)) {                   
                $lead_data = [
                    'cart_modified_date'    => date('Y-m-d H:i:s'),
                    'cart'                  => $cart_data,
                    'is_cart'               => $is_cart
                ];
                
                $this->model_lead_lead->updateLead($lead_data, $telephone, 'cart updated', $this->customer->getId() );
            }
        }

        public function getWebengageCartData()
        {
             $json = $this->__cartResponse(); 
             return $json;
        }

    }
?>