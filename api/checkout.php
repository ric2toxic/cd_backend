<?php
require_once('system.php');
require_once(DIR_SYSTEM . 'library/cart.php');
require_once(DIR_SYSTEM . 'library/weight.php');
require_once(DIR_SYSTEM . 'library/wsb.php');
require_once(DIR_SYSTEM . 'library/total/totalfactory.php');
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/rbl.php');

    class CheckoutController extends SystemController{

        private $_free_shipping_coupon_applied = false;

        public function __construct($params)
        {
            parent::__construct($params);

            // Customer
            //$this->registry->set('customer', new Customer($this->registry));

            // Currency
            //$this->registry->set('currency', new Currency($this->registry));

            // Tax
            //$this->registry->set('tax', new Tax($this->registry));

            // Weight
            $this->registry->set('weight', new Weight($this->registry));

            // WSB common library
            $this->registry->set('wsb', new Wsb($this->registry));

            // Cart
            $this->registry->set('cart',new Cart($this->registry));

            if ( !empty( $this->session->data['free_shipping_coupon_applied'] )) {
                $this->_free_shipping_coupon_applied = true;
            }
        }

        public function shipping_method_validate(&$data  = array()) {

            $this->load->language('checkout/checkout');

            $json = array();

            // Validate if shipping is required. If not the customer should not have reached this page.
            if (!$this->cart->hasShipping()) {
                //$json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
            }

            // Validate if shipping address has been set.
            $this->load->model('account/address');

            if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
                $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
            } elseif (isset($this->session->data['guest'])) {
                $shipping_address = isset($this->session->data['guest']['shipping'])?$this->session->data['guest']['shipping']:'';
            }

            if (empty($shipping_address)) {
                //$json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
            }

            // Validate cart has products and has stock.
            if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
                //$json['redirect'] = $this->url->link('checkout/cart');
            }

            $json_show = 0;
            if (!$json) {
                if (!isset($this->request['shipping_method'])) {
                    if(isset($this->session->data['shipping_method']['code']) && $this->session->data['shipping_method']['code'] == 'free.free'){
                        $json_show = 1;
                    }else if(isset($this->session->data['shipping_method'])){
                        $json_show = 1;
                    }else{
                        $json['error']['warning'] = $this->language->get('error_shipping');
                    }
                } else {
                    $shipping = explode('.', $this->request['shipping_method']);
                    if (!isset($shipping[0]) || !isset($shipping[1])/* || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])*/) {
                        $json['error']['warning'] = $this->language->get('error_shipping');
                    }
                }

                if (!$json && $json_show == 0) {
                    $shipping = explode('.', $this->request['shipping_method']);
                    if (isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]))
                        $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

                    $this->session->data['comment'] = (isset($this->request['comment']))?strip_tags($this->request['comment']):'';
                }
            }

            return $json;
        }


        public function payment_method_validate(&$data  = array()) {

            $this->load->language('checkout/checkout');

            $json = array();

            // Validate if payment address has been set.
            $this->load->model('account/address');

            if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
                $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
            } elseif (isset($this->session->data['guest']['payment'])) {
                $payment_address = $this->session->data['guest']['payment'];
            } else
            {
                $payment_address = $this->model_account_address->getAddress(0);
            }

            if (empty($payment_address)) {
                //$json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
            }

            // Validate cart has products and has stock.
            if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
                //$json['redirect'] = $this->url->link('checkout/cart');
            }

            // Validate minimum quantity requirments.
            $products = $this->cart->getProducts();

            foreach ($products as $product) {
                $product_total = 0;

                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }

                if ($product['minimum'] > $product_total) {
                    $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');

                    break;
                }
            }

            if (!$json) {
                if (!isset($this->request['payment_method'])) {
                    $json['error']['warning'] = $this->language->get('error_payment');
                } elseif (!isset($this->session->data['payment_methods'][$this->request['payment_method']])) {
//              error_log(print_r($this->session->data['payment_methods'],1));
                    $json['error']['warning'] = $this->language->get('error_payment');
                }

                if ($this->config->get('config_checkout_id')) {
                    $this->load->model('catalog/information');

                    $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

                    if ($information_info && !isset($this->request['agree'])) {
                        $json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
                    }
                }

                if(isset($this->request['payment_method']) && isset($this->session->data['payment_methods'][$this->request['payment_method']])){
                    $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request['payment_method']];
                }else{
                    $this->session->data['payment_method'] = '';
                }
                $this->session->data['discount_type'] = (isset($this->request['getOffer']) ? $this->request['getOffer'] : '' );
                $this->session->data['comment'] = (isset($this->request['comment']))?strip_tags($this->request['comment']):'';

            }
            return $json;
        }

        public function cartSummary()
        {
            $data = array();
            $this->shipping_method_validate();
            $this->payment_method_validate();

            $this->load->language('checkout/cart');

            if (!isset($this->session->data['vouchers'])) {
                $this->session->data['vouchers'] = array();
            }



            $points = $this->customer->getRewardPoints();

            $points_total = 0;

            foreach ($this->cart->getProducts() as $product) {
                if ($product['points']) {
                    $points_total += $product['points'];
                }
            }

            $data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);

            if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
            } elseif (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
                $data['error_warning'] = $this->language->get('error_stock');
            } else {
                $data['error_warning'] = '';
            }

            if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
                $data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', ''. 'SSL'));
            } else {
                $data['attention'] = '';
            }

            if (isset($this->session->data['success'])) {
                $data['success'] = $this->session->data['success'];

                unset($this->session->data['success']);
            } else {
                $data['success'] = '';
            }

            $data['action'] = $this->url->link('checkout/cart', '', 'SSL');

            if ($this->config->get('config_cart_weight')) {
                $data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
            } else {
                $data['weight'] = '';
            }


            $this->load->model('tool/image');
            $this->load->model('catalog/product');

            $data['products'] = array();
            $total_sets = 0;
            $total_pieces = 0;

            //default shipping method apply, later it will change in to free ship if free ship condition passed
            if( isset($this->session->data['shipping_methods']['weight']['quote']['weight_5']) 
                && !$this->_free_shipping_coupon_applied 
                && $this->cart->getTotal() < CART_SUB_TOTAL_FOR_FREE_SHIPPING ) {
                
                if(isset($this->session->data['shipping_method']) && $this->session->data['shipping_method']['code'] == 'free.free'){
                    $this->session->data['shipping_method'] = $this->session->data['shipping_methods']['weight']['quote']['weight_5'];
                }
            }


            // Gift Voucher
            $data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $key => $voucher) {
                    $data['vouchers'][] = array(
                        'key'         => $key,
                        'description' => $voucher['description'],
                        'amount'      => $this->currency->format($voucher['amount']),
                        'remove'      => $this->url->link('checkout/cart', 'remove=' . $key, 'SSL')
                    );
                }
            }


            $data['coupon_status'] = $this->config->get('coupon_status');

            if (isset($this->request['coupon'])) {
                $data['coupon'] = $this->request['coupon'];
            } elseif (isset($this->session->data['coupon'])) {
                $data['coupon'] = $this->session->data['coupon'];
            } else {
                $data['coupon'] = '';
            }

            $data['voucher_status'] = $this->config->get('voucher_status');

            if (isset($this->request['voucher'])) {
                $data['voucher'] = $this->request['voucher'];
            } elseif (isset($this->session->data['voucher'])) {
                $data['voucher'] = $this->session->data['voucher'];
            } else {
                $data['voucher'] = '';
            }

            $data['reward_status'] = ($points && $points_total && $this->config->get('reward_status'));

            if (isset($this->request['reward'])) {
                $data['reward'] = $this->request['reward'];
            } elseif (isset($this->session->data['reward'])) {
                $data['reward'] = $this->session->data['reward'];
            } else {
                $data['reward'] = '';
            }

            $data['shipping_status'] = $this->config->get('shipping_status') && $this->config->get('shipping_estimator') && $this->cart->hasShipping();

            // Totals
            $order_data['totals'] = array();
            $total = 0;
            $taxes = 0;
            $total_factory = new TotalFactory( $this, true );
            $order_data['totals'] = $total_factory->getTotal(true, $this->_free_shipping_coupon_applied);
            $total = $total_factory->total;
            $taxes = $total_factory->taxes;
            $cst = $total_factory->cst;

            $data['cform_submit'] = 0;

            $data['text_cst'] = $this->language->get('text_cst');
            $data['text_tax_refund'] = $this->language->get('text_tax_refund');

            $data['totals'] = array();
            $tax_in_totals = false;
            $cartTotal = 0.0;
            $store_credit = 0.0;

            foreach ($order_data['totals'] as $key => $total) {
                
                if ($total['code'] == 'subtotal') {
                    $total['title'] = $this->language->get('text_subtotal');
                } elseif ($total['code'] == 'total') {
                    $total['title'] = $this->language->get('text_total_amount');
                    $cartTotal = $total['value'];
                } elseif ($total['code'] == 'credit') {
                    $store_credit = $total['value'];
                } elseif ($total['code'] == 'tax') {  // Getting tax from Order total table and calculating 60% refund for Form C submission case
                    //$total['title'] = $this->language->get('text_tax');
                    $tax_in_totals = true;
                    $this->session->data['cst'] = $cst;
                    $this->session->data['tax_refund'] = $total['value'] - $cst; // Refund will be total tax - cst value
                    $data['cst'] = $this->currency->format($cst);
                    $data['tax_refund'] = $this->currency->format($total['value'] - $cst);  // Refund will be total tax - cst value
                }else if($total['code'] == 'paycharge' && abs($total['value']) == 0) {
                    unset($order_data['totals'][$key]);
                    continue;
                }

                if(($total['code'] == "shipping" && $total['code'] == "0") || $total['title'] == "Free Shipping"){
                    foreach($order_data['totals'] as $k=>$ext){
                        if($ext['code'] == "paycharge"){
                            unset($order_data['totals'][$k]);
                            continue;
                        }
                        if($ext['code'] == "tax"){
                            $CST_CLASS_ID = 12;
                            $cst = $this->cart->getCST($CST_CLASS_ID);
                            $this->session->data['cst'] = $cst;
                            $this->session->data['tax_refund'] = $ext['value'] - $cst; // Refund will be total tax - cst value
                            $data['cst'] = $this->currency->format($cst);
                            $data['tax_refund'] = $this->currency->format($ext['value'] - $cst);
                        }
                    }
                    //$data['totals'] = array_values($order_data['totals']);
                }

                $data['totals'][$total['code']] = array(
                    'code'  => $total['code'],
                    'title' => $total['title'],
                    'value' => $total['value'],
                    'show_value' => $total['value'],
                    'text'  => $this->currency->format($total['value'])
                );
            }
            $data['price_in_rupees'] = '';
            if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID && $this->currency->getCode() != DUMMY_INR_CURRENCY ) {
              if (isset($data['totals']['net_payable_amount'])) {
                $amount_in_inr = $this->currency->convert($data['totals']['net_payable_amount']['value'], 'INR', DUMMY_INR_CURRENCY);
                $data['price_in_rupees'] = $this->currency->format($amount_in_inr, DUMMY_INR_CURRENCY, 1);
              } else {
                $amount_in_inr = $this->currency->convert($data['totals']['total']['value'], 'INR', DUMMY_INR_CURRENCY);
                $data['price_in_rupees'] = $this->currency->format($amount_in_inr, DUMMY_INR_CURRENCY, 1);
              }
            }

            /*foreach ($order_data['totals'] as $total) {
                $data['totals'][] = array(
                    'title' => $total['title'],
                    'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
                );
            }*/

            $sum_cart_credit = $cartTotal + abs($store_credit);

            $data['error_cart_minimum'] = '';
            $this->session->data['error_cart_minimum'] = false;

            $store_id = $this->config->get('config_store_id') ;

            if ($store_id == 2) { // Singles store
                $single_store_order_limit = (float)$this->config->get('config_limit');
                if ($cartTotal < $single_store_order_limit) {
                    $data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'), $this->currency->format(ceil($single_store_order_limit)) );
                    $this->session->data['error_cart_minimum'] = true;
                }
            } elseif ( $sum_cart_credit < (float)($this->config->get('config_cart_limit')) ) {
                $data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'),$this->currency->format( (float)($this->config->get('config_cart_limit')), $this->currency->getCode(), 1 ));
                $this->session->data['error_cart_minimum'] = true;
            }


            $customer_cif_id = '';
            if(!empty($this->customer->getId())) {

                $credit_payment_gateway = new RblPayment($this);
                $customer_cif_id = $credit_payment_gateway->getCustomerCIFId($this->customer->getId());

            }
            
            $data['text_rbl_consent'] =  sprintf($this->language->get('text_rbl_consent'),$customer_cif_id,$data['totals']['total']['text']);


            $data['continue'] = $this->url->link('common/home', '', 'SSL');

            $data['checkout'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');

            $data['checkout_buttons'] = array();
            unset($this->session->data['shipping_method']);

            return $data;

        }

        public function getShippingAddress(){
            $json = array();
            $json['shipping_address_id'] = 0;
            $pincode = $this->request['pincode'];
            $zone_id = $this->request['zone_id'];

            // Default Addresses
            $this->load->model('account/address');
            $addresses = $this->model_account_address->getAddresses();
            foreach ($addresses as $address_id => $address){
                if($pincode != ""){
                    if($pincode == $address['postcode']){
                        $json['shipping_address_id'] = $address_id;
                        break;
                    }
                }
                else{
                    if($zone_id == $address['zone_id']){
                        $json['shipping_address_id'] = $address_id;
                        break;
                    }
                }
            }

            return $json;
        }

        public function shippingMethod() {

            $this->load->language('checkout/shipping');
            $json = array();
            if (!isset($this->request['weight_cart'])) {
                if (!$this->cart->hasProducts()) {
                    $json['error']['warning'] = $this->language->get('error_product');
                }

                if (!$this->cart->hasShipping()) {
                    $json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
                }
            }
            if ($this->request['country_id'] == '') {
                $json['error']['country'] = $this->language->get('error_country');
            }

            if (!isset($this->request['zone_id']) || $this->request['zone_id'] == '') {
                $json['error']['zone'] = $this->language->get('error_zone');
            }

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($this->request['country_id']);
            if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request['postcode'])) < 2 || utf8_strlen(trim($this->request['postcode'])) > 10)) {
                //$json['error']['postcode'] = $this->language->get('error_postcode');
            }
            

            if (!$json) {
                //  $this->tax->setShippingAddress($this->request['country_id'], $this->request['zone_id']);
                if ($country_info) {
                    $country = $country_info['name'];
                    $iso_code_2 = $country_info['iso_code_2'];
                    $iso_code_3 = $country_info['iso_code_3'];
                    $address_format = $country_info['address_format'];
                } else {
                    $country = '';
                    $iso_code_2 = '';
                    $iso_code_3 = '';
                    $address_format = '';
                }

                $this->load->model('localisation/zone');
                $zone_info = $this->model_localisation_zone->getZone($this->request['zone_id']);

                if ($zone_info) {
                    $zone = $zone_info['name'];
                    $zone_code = $zone_info['code'];
                } else {
                    $zone = '';
                    $zone_code = '';
                }

                $shipping_address = array(
                    'address_id' => '0',
                    'firstname' => '',
                    'lastname' => '',
                    'company' => '',
                    'address_1' => '',
                    'address_2' => '',
                    'postcode' => $this->request['postcode'],
                    'city' => '',
                    'zone_id' => $this->request['zone_id'],
                    'zone' => $zone,
                    'zone_code' => $zone_code,
                    'country_id' => $this->request['country_id'],
                    'country' => $country,
                    'iso_code_2' => $iso_code_2,
                    'iso_code_3' => $iso_code_3,
                    'address_format' => $address_format
                );

                // Default Addresses
                $this->load->model('account/address');
                $addresses = $this->model_account_address->getAddresses();
                $json['addresses'] = $addresses;
                foreach ($addresses as $address) {
                    if ($this->request['postcode'] != "") {
                        if ($this->request['postcode'] == $address['postcode']) {
                            $shipping_address = $address;
                            break;
                        }
                    } else {
                        if ($this->request['zone_id'] == $address['zone_id']) {
                            $shipping_address = $address;
                            break;
                        }
                    }
                }

                $json['shipping_address'] = $shipping_address;
                 //echo "<pre>"; print_r($results); exit;
                $quote_data = array();
                $this->load->model('extension/extension');
                $results = $this->model_extension_extension->getExtensions('shipping');
                //echo "<pre>"; print_r($results); exit;
                
                if( $this->cart->getTotal() >= CART_SUB_TOTAL_FOR_FREE_SHIPPING ) {
                    $this->_free_shipping_coupon_applied = true;
                }

                foreach ($results as $result) {
                    if ($this->config->get($result['code'] . '_status')) {
                        if ($result['code'] != "free" || $this->_free_shipping_coupon_applied) { //remove free shipping option
                            $this->load->model('shipping/' . $result['code']);
                            if (isset($this->request['weight_cart'])) {
                                $shipping_address['from_backend'] =true;
                                $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address, $this->request['weight_cart'], $this->_free_shipping_coupon_applied );
                            } else {
                                $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address, 0, $this->_free_shipping_coupon_applied );
                            }
                        // print_r($quote);
                            if ($quote) {
                                $quote_data[$result['code']] = array(
                                    'title' => $quote['title'],
                                    'quote' => $quote['quote'],
                                    'sort_order' => $quote['sort_order'],
                                    'error' => $quote['error']
                                );
                            }
                        }
                    }
                }

                 
                if ($this->customer->isLogged()) {
                    if ($this->customer->is_dropshipper == 1 && isset($this->request['cartlimitcross']) && $this->request['cartlimitcross'] == 0) {
                        if (isset($quote_data['weight'])) {
                            foreach ((array) $quote_data['weight']['quote'] as $key => $value) {
                                if ($key != 'weight_8') {
                                    unset($quote_data['weight']['quote'][$key]);
                                }
                            }
                        }
                    } else if ($this->customer->is_dropshipper == 1 && isset($this->request['cartlimitcross']) && $this->request['cartlimitcross'] == 1) {
                        if (isset($quote_data['weight']['quote']['weight_7'])) {
                            unset($quote_data['weight']['quote']['weight_7']);
                        }
                    } else {
                        if (isset($quote_data['weight']['quote']['weight_8'])) {
                            unset($quote_data['weight']['quote']['weight_8']);
                        }
                    }
                } else if (isset($this->request['is_dropshipper'])) {
                    if ($this->request['is_dropshipper'] == 1 && isset($this->request['cartlimitcross']) && $this->request['cartlimitcross'] == 0) {
                        if (isset($quote_data['weight'])) {
                            foreach ((array) $quote_data['weight']['quote'] as $key => $value) {
                                if ($key != 'weight_8') {
                                    unset($quote_data['weight']['quote'][$key]);
                                }
                            }
                        }
                    } else if ($this->request['is_dropshipper'] && isset($this->request['cartlimitcross']) && $this->request['cartlimitcross'] == 1) {
                        if (isset($quote_data['weight']['quote']['weight_7'])) {
                            unset($quote_data['weight']['quote']['weight_7']);
                        }
                    } else {
                        if (isset($quote_data['weight']['quote']['weight_8'])) {
                            unset($quote_data['weight']['quote']['weight_8']);
                        }
                    }
                }

                // for orders by franchise, we will show only one shipping method(surface courier), which will be selected by default.
                if(is_object($this->cart) && !empty($this->cart->getFranchiseId()) && $this->cart->checkFranchiseProductsInCart()){
                    $quote_data['weight']['quote']['weight_0'] = array(
                        'code' => 'weight.weight_0',
                        'cost' => 0,
                        'description' => 'Store Pickup',
                        'title' => 'Store Pickup',
                        'tax_class_id' => 0,
                        'text' => 'Rs. 0.00'
                    );
                    $quote_data['weight']['quote'] = array("weight_0" => $quote_data['weight']['quote']['weight_0']);

                }


                // In case of store pickup, will show only Store Pickup Shipping method with zero charges
                $coupon_data = $this->cart->getCoupon();
                if( !empty($coupon_data['wsb_store_voucher'] ) &&
                    !empty($coupon_data['wsb_store_code']) ){
                    $products = $this->cart->getProducts();
                    $store_pickup = true;
                    foreach ( $products as $product) {
                        if(!$product['store_pickup']){
                            $store_pickup = $product['store_pickup'];
                            break;
                        }
                    }
                    if($store_pickup){
                        $quote_data['weight']['quote']['weight_0'] = array(
                            'code' => 'weight.weight_0',
                            'cost' => 0,
                            'description' => 'Store Pickup',
                            'title' => 'Store Pickup',
                            'tax_class_id' => 0,
                            'text' => 'Rs. 0.00'
                        );
                        $quote_data['weight']['quote'] = array("weight_0" => $quote_data['weight']['quote']['weight_0']);
                    }
                }

                //In case of surface courier, if shipping cost is greater than 10% of the cart total, then need to send internal mail
                // Also, request should not be from backend
                if(!isset($this->request['weight_cart'])) {
                    $check_send_mail = false;
                    if (isset($quote_data['weight']['quote']['weight_5'])) {
                        $shipping_cost_tmp = $quote_data['weight']['quote']['weight_5']['cost'];
                        $shipping_method_tmp = $quote_data['weight']['quote']['weight_5']['title'];
                        $check_send_mail = true;
                    } else if (isset($quote_data['weight']['quote']['weight_48'])) {
                        $shipping_cost_tmp = $quote_data['weight']['quote']['weight_48']['cost'];
                        $shipping_method_tmp = $quote_data['weight']['quote']['weight_48']['title'];
                        $check_send_mail = true;
                    } else if (isset($quote_data['weight']['quote']['weight_49'])) {
                        $shipping_cost_tmp = $quote_data['weight']['quote']['weight_49']['cost'];
                        $shipping_method_tmp = $quote_data['weight']['quote']['weight_49']['title'];
                        $check_send_mail = true;
                    }
                    if ($check_send_mail && $this->currency->getCode() == 'INR') {
                        $cart_sub_total = $this->cart->getSubTotal();
                        $cart_weight = $this->cart->getWeight();
                        $cart_products = $this->cart->getProducts();
                        $cart_contain_high_weight_product = false;
                        foreach ($cart_products as $product) {
                          if ($product['weight_per_piece'] > 2) {
                            $cart_contain_high_weight_product = true;
                            break;
                          }
                        }
                        $cart_limit = $this->config->get('config_cart_limit');
                        if($this->customer->is_dropshipper == 1) $cart_limit = $this->config->get('config_customer_cart_limit');
                        if ( $cart_contain_high_weight_product && ( (float)$cart_sub_total > (float)$cart_limit ) && ( (float)$shipping_cost_tmp > (float)(($cart_sub_total * 10) / 100) ) ) {
                            // shipping cost is greater than 10% of the cart total, so need to send mail
                            $data = array();
                            $data['shipping_cost_ratio'] = number_format((float)($shipping_cost_tmp / $cart_sub_total), 2);
                            $data['shipping_cost'] = $this->currency->format($shipping_cost_tmp);
                            $data['shipping_method'] = $shipping_method_tmp;
                            $data['cart_sub_total'] = $this->currency->format($cart_sub_total);
                            $data['shipping_address'] = $json['shipping_address'];
                            $data['customer_id'] = $this->customer->getId();
                            $data['customer_mobile'] = $this->customer->getTelephone();
                            $data['customer_email'] = $this->customer->getEmail();
                            $data['cart'] = $cart_products;
                            $data['cart_weight'] = $this->weight->format($cart_weight, $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
                            $this->_sendMailOnHighShippingCharge($data);
                        }
                    }
                }

                //echo "<pre>"; print_r($quote_data); exit;
                $sort_order = array();

                foreach ($quote_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }


                array_multisort($sort_order, SORT_ASC, $quote_data);

                $shipping_methods = $quote_data;


                if ($shipping_methods && !empty($shipping_methods['weight']['quote'])) {
                    $json['shipping_method'] = $shipping_methods;
                } else {
                    $json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
                }

                if (!isset($this->request['weight_cart'])) {
                    $this->session->data['shipping_address'] = $shipping_address;
                    $this->session->data['shipping_methods'] = $shipping_methods;
                    $this->session->data['shipping_address_id'] = $shipping_address['address_id'];
                    $this->session->data['shipping_country_id'] = $shipping_address['country_id'];
                    $this->session->data['shipping_zone_id'] = $shipping_address['zone_id'];
                    $this->session->data['shipping_postcode'] = $shipping_address['postcode'];
                }
            }

            $this->load->model('extension/extension');

            $checkESS = $this->model_extension_extension->checkESSServiceability($this->request['postcode']);

            $json['ess_service'] = $checkESS;
            return $json;
        }

        public function paymentMethod() {
            $this->load->language('checkout/checkout');

            $this->load->model('account/address');


            $payment_address = $this->model_account_address->getAddress($this->request['address_id']);

            $this->session->data['shipping_address_id'] = $this->request['address_id'];
            $this->session->data['shipping_address'] = $payment_address;
            $this->session->data['payment_address'] = $payment_address;

            // Totals
            $totals = array();
            $taxes = 0;
            $total = 0;


            // Because __call can not keep var references so we put them into an array.
            $order_data['totals'] = array(
                'totals' => &$totals,
                'taxes'  => &$taxes,
                'total'  => &$total
            );

            // Get Total From system/lib/TotalFactory
            // By Sudhanshu Jain
            $total_factory = new TotalFactory( $this, true );
            $order_data['totals'] = $total_factory->getTotal(true);
            $order_data['total'] = $total_factory->total;
            $order_data['taxes'] = $total_factory->taxes;
            $cst = $total_factory->cst;

            // Payment Methods
            $method_data = array();


            $this->load->model('extension/extension');


            // for checking COD serviceability for Gati and Fedex
            // For Bihar[1479], UP[1505] state.
            $checkCod = false;
            if($payment_address['zone_id'] == "1490"){
                // Kerala[1490] state, COD serviceability is not available (restricted for now).
                $checkCod = false;
            } else if(in_array($this->customer->getId(), COD_BLOCKED_CUSTOMERS)) {
              // blocking cod payment method for above customers
              $checkCod = false;
            }else {
                if(!empty($payment_address['postcode'])){
                    $checkCod = $this->model_extension_extension->checkCODServiceability($payment_address['postcode']);
                }
            }
            
            $is_customer_franchise = $this->customer->isFranchise();

            // For orders by franchise we will show only COD (if not available than bank transfer).
            if(is_object($this->cart) && !empty($this->cart->getFranchiseId()) && $this->cart->checkFranchiseProductsInCart() ){
                $method_data['franchise'] = array(
                    'code' => 'franchise',
                    'title' => 'Order on Franchise Store',
                    'sort_order' => 1,
                    'terms' => ''
                );
            }
            else{
                //If shipping method is ToPay Courier OR Drop ship by air, then disable cod option
                if( !empty($this->request['shipping_method']) && ($this->request['shipping_method'] == 'weight.weight_7' || $this->request['shipping_method'] == 'weight.weight_8')){
                    $checkCod = false;
                }


                $data['CODServiceability'] = $checkCod;

                $results = $this->model_extension_extension->getExtensions('payment', $this->config->get('config_store_id'), $checkCod);

                $cart_has_recurring = (method_exists($this->cart, 'hasRecurringProducts') && $this->cart->hasRecurringProducts());

                foreach ($results as $result) {

                    if($result['code'] == 'upi' && $order_data['total'] > 100000) {
                        continue;
                    }

                    if ($this->config->get($result['code'] . '_status')) {
                        $this->load->model('payment/' . $result['code']);
                        if($this->customer->isLogged()){
                            if($result['code'] == 'cod' && isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1 && ($order_data['total'] < $this->config->get('config_cart_limit'))){
                                continue;
                            }

                            if( $result['code'] == 'credit' && !$this->customer->isCustomerCreditStatus('Neogrowth') ){
                                continue;
                            }

                            if( $result['code'] == 'lazypay' && !$this->customer->isCustomerCreditStatus('Lazypay') ){
                                continue;
                            }

                            if( $result['code'] == 'rbl' && !$this->customer->isCustomerCreditStatus('RBL') ){
                                continue;
                            }

                            if($is_customer_franchise && !in_array($result['code'], array('bank_transfer', 'credit', 'wsb_credit', 'epay_later')) ){
                                // for franchise customers, only bank transfer and credit methods will be enabled
                                continue;
                            }

                            $method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['shipping_address'], $order_data['total']);
                            // ##$method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

                            if ($method) {
                                if ($cart_has_recurring) {
                                    if (method_exists($this->{'model_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_payment_' . $result['code']}->recurringPayments()) {
                                        $method_data[$result['code']] = $method;
                                    }
                                } else {
                                    $method_data[$result['code']] = $method;
                                }
                            }

                        }else{
                            // ##$method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);
                            $method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['shipping_address'], $total);

                            if ($method) {
                                if ($cart_has_recurring) {
                                    if (method_exists($this->{'model_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_payment_' . $result['code']}->recurringPayments()) {
                                        $method_data[$result['code']] = $method;
                                    }
                                } else {
                                    $method_data[$result['code']] = $method;
                                }
                            }
                        }

                    }
                }

                $sort_order = array();

                foreach ($method_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $method_data);
            }

            $this->session->data['payment_methods'] = $method_data;

            //}
            $data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
            $data['text_payment_method'] = $this->language->get('text_payment_method');
            $data['text_comments'] = $this->language->get('text_comments');

            $data['button_continue'] = $this->language->get('button_continue');

            if (empty($this->session->data['payment_methods'])) {
                $data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact', '', 'SSL'));
            } else {
                $data['error_warning'] = '';
            }

            if (isset($this->session->data['payment_methods'])) {
                $data['payment_methods'] = $this->session->data['payment_methods'];
            } else {
                $data['payment_methods'] = array();
            }
            if (isset($this->session->data['payment_method']['code'])) {
                $data['code'] = $this->session->data['payment_method']['code'];
            } else {
                $data['code'] = '';
            }

            if (isset($this->session->data['comment'])) {
                $data['comment'] = $this->session->data['comment'];
            } else {
                $data['comment'] = '';
            }

            if ($this->config->get('config_checkout_id')) {
                $this->load->model('catalog/information');

                $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

                if ($information_info) {
                    $data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
                } else {
                    $data['text_agree'] = '';
                }
            } else {
                $data['text_agree'] = '';
            }

            if (isset($this->session->data['agree'])) {
                $data['agree'] = $this->session->data['agree'];
            } else {
                $data['agree'] = true;
            }

            $this->load->language('payment/bank_transfer');
            $bank_transfer = array();
            $bank_transfer['text_instruction'] = $this->language->get('text_instruction');
            $bank_transfer['text_description'] = $this->language->get('text_description');
            $bank_transfer['text_payment'] = $this->language->get('text_payment');
            $bank_transfer['bank_transfer'] = nl2br($this->config->get('bank_transfer_bank' . $this->config->get('config_language_id')));

            $cod_available = 1;
            $cod_not_available_text = "the selected shipping method";
            if( !empty($this->request['shipping_method']) && ($this->request['shipping_method'] == 'weight.weight_6') ){
                $cod_available = 0;
                $shipping = explode('.', $this->request['shipping_method']);
                if(isset($this->session->data['shipping_methods']) && !empty( $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]] ) ){
                    $cod_not_available_text = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]['title'];
                }
            }

            $coupon_data = $this->cart->getCoupon();
            $data['show_offers'] = 1;
            if(isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1 && ($this->cart->getSubTotal() < $this->config->get('config_cart_limit')) ){
                $data['show_offers'] = 0;
            }else if( !empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code']) ) {
                $data['show_offers'] = 0;
            }
            else if( !empty($coupon_data['coupon_franchise']) ) {
                $data['show_offers'] = 0;
            }
            else if (!empty($this->cart->getFranchiseId()) && $this->cart->checkFranchiseProductsInCart()){
                $data['show_offers'] = 0;
            }
            else if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID){
                $data['show_offers'] = 0;
            }

            $data['payment_data'] = array(
                "payment_methods" => $data['payment_methods'],
                "bank_transfer_info" => $bank_transfer,
                "upi_id" => $this->config->get('upi_id'),
                "cod_available" => $cod_available,
                "change_shipping_text" => 'Change Shipping',
                "cod_not_available_text" => "COD is not available for ".$cod_not_available_text,
                "show_offers" => $data['show_offers'],
                "netbanking_help_url" => STATIC_CONTENT_URL_SSL . 'Net_Banking_Web.jpg',
                "netbanking_help_mobile_url" => STATIC_CONTENT_URL_SSL . 'Net_Banking_Mobile.jpg'
            );

            $data['text_payment_method'] = $this->language->get('text_payment_method');
            $data['text_comments'] = $this->language->get('text_comments');
            $data['text_loading'] = $this->language->get('text_loading');
            $data['text_discount'] = $this->language->get('text_discount');


            if($this->config->get('free_total')){
                $this->load->language('checkout/shipping');
                $data['free_shipping'] = sprintf($this->language->get('free_shipping'),$this->config->get('free_total'));
            }

            $data['store_id'] = $this->config->get('config_store_id') ;


            return $data;
        }

        public function country($get) {
            $json = array();

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($get[0]);

            if ($country_info) {
                $this->load->model('localisation/zone');

                $json = array(
                    'country_id'        => $country_info['country_id'],
                    'name'              => $country_info['name'],
                    'iso_code_2'        => $country_info['iso_code_2'],
                    'iso_code_3'        => $country_info['iso_code_3'],
                    'address_format'    => $country_info['address_format'],
                    'postcode_required' => $country_info['postcode_required'],
                    'zone'              => $this->model_localisation_zone->getZonesByCountryId($get[0]),
                    'status'            => $country_info['status']
                );
            }

            return $json;
        }

        public function validateGSTNumber() { 
            
            $json = array();
            $json['success'] = 0;
            
            $this->load->language('checkout/checkout');
            
            $customer_id = $this->customer->getId();

            $gst_number  = $this->request['gst_number'] ?? '';
            $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
            $gst_number  = trim($gst_number);

    		$valid_gst_result = $this->customer->gstObject->validateGSTNumber($gst_number, $customer_id);
            
    		if ( !empty($gst_number) && !($valid_gst_result['result'] === true) ) {
    			if($valid_gst_result['message'] == "error_regex") {
    				$json['error']['warning'] = $this->language->get('error_gst');
    			} elseif($valid_gst_result['message'] == "error_checksum") {
    				$json['error']['warning'] = sprintf($this->language->get('error_gst_checksum'),
    				 									 $valid_gst_result['gst_number_details']['gst_number_without_checksum']."<b>".$valid_gst_result['gst_number_details']['gst_number_checksum']."</b>");
    			} else if($valid_gst_result['message'] == "error_duplicate") {

                    /**
                     *  - Commenting below code to allow customer to add duplicate GST Number (Duplicacy mail is already being sent)
                     *  - By Anurag Jain(Sept 2018)
                     **/
                    // $user_str = '';
                    // if(!empty($valid_gst_result['duplicate_gst_number_details'])) {
                    //  $duplicate_gst_number_customer = $valid_gst_result['duplicate_gst_number_details'];
                    //     if(!empty($duplicate_gst_number_customer['telephone'])) {
                    //         $user_str = 'mobile number <strong>' . substr($duplicate_gst_number_customer['telephone'],0, 2) . 'xxxxx' . substr($duplicate_gst_number_customer['telephone'],7) . '</strong>';
                    //     } else if(!empty($duplicate_gst_number_customer['email'])){
                    //         $len = strlen(explode('@',$duplicate_gst_number_customer['email'])[0]);
                    //         $user_str = 'email <strong>' . 'xxxxx' . substr($duplicate_gst_number_customer['email'],$len/2).'</strong>';
                    //     }
                    //     $json['error']['warning'] = sprintf($this->language->get('error_exists_gst'), $this->request['gst_number'], $user_str, $this->url->link('information/contact') );
                    // }
                }
            }

            return $json;
        }
        
        public function getLanguage(){
            $language = array();
            $this->load->autoLoadLanguage('checkout/checkout',$language);
            $data['language'] = $language;
            return $data;
        }

        public function callMeBackRequest(){
            $json = array();
            if(!isset($this->request['phone_number'])){
                $json['error']['warning'] = "Please provide phone number.";
                return $json;
            }
            $data_json = array(
                'phone_number'  => $this->request['phone_number'],
                'ip'            => $this->getIpAddress,
                'browser'       => isset($_POST['browser']) ? $_POST['browser']: '',
                'referral'      => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '',
                'page'          => "checkout"
            );

            //curl url
            $curl_url = WSBOX_CRM_URL."/api/callbackToMeBusinessLanding";

            $data_json = json_encode($data_json);
            $ch =  curl_init();
            curl_setopt($ch,CURLOPT_URL,$curl_url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            $result=curl_exec($ch);
            curl_close($ch);
            if(!empty($result)){
                $json['success'] = "Success";
            }
            else{
                $json['error']['warning'] = "Oops! Something went wrong.";
            }
            return $json;
        }

        private function _sendMailOnHighShippingCharge($data){

            $carthtml = "<table cellpadding=\"5px\"><tr><td><strong>Product Id</strong></td><td><strong>Product Code</strong></td>
                        <td><strong>Quantity</strong></td><td><strong>Price/Piece</strong></td><td><strong>Weight/Piece</strong></td></tr>";
            foreach ($data['cart'] as $key => $product){
                $tr = "<tr><td>".$product['product_id']."</td><td>".$product['model']."</td><td>".$product['quantity']."</td>
                        <td>".$this->currency->format($product['price_per_piece'])."</td><td>".$this->weight->format($product['weight_per_piece'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'))."</td></tr>";
                $carthtml .= $tr;
            }
            $carthtml .= "</table>";
            $html = "<html>
                    <body>
                        <h3>Shipping Cost vs Cart Sub Total Ratio: ".$data['shipping_cost_ratio']."</h3>
                    <h3>Customer</h3>
                    <table cellpadding=\"2px\">
                        <tr><td><strong>Id:</strong></td><td>".$data['customer_id']."</td></tr>
                        <tr><td><strong>Mobile:</strong></td><td>".$data['customer_mobile']."</td></tr>
                        <tr><td><strong>Email:</strong></td><td>".$data['customer_email']."</td></tr>
                    </table>
                    <br/>
                    <h3>Cart</h3>".$carthtml."
                    <br/>
                    <table cellpadding=\"2px\">
                        <tr><td><strong>Cart Weight:</strong></td><td>".$data['cart_weight']."</td></tr>
                        <tr><td><strong>Cart Sub Total:</strong></td><td>".$data['cart_sub_total']."</td></tr>
                    </table>
                    <br/>
                    <h3>Shipping</h3>
                    <table cellpadding=\"2px\">
                        <tr><td><strong>Shipping Zone:</strong></td><td>".$data['shipping_address']['zone']."</td></tr>
                        <tr><td><strong>Shipping Country:</strong></td><td>".$data['shipping_address']['country']."</td></tr>
                        <tr><td><strong>Shipping Post Code:</strong></td><td>".$data['shipping_address']['postcode']."</td></tr>
                        <tr><td><strong>Shipping Method:</strong></td><td>".$data['shipping_method']."</td></tr>
                        <tr><td><strong>Shipping Cost:</strong></td><td>".$data['shipping_cost']."</td></tr>
                    </table>
                    </body>
                    </html>";
            $mail = new PHPMailer();
            $mail->isSMTP();

            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            ;
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');
            $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
            $mail->Subject = 'Alert! Shipping cost is greater than 10% of the cart total.';
            $mail->msgHTML($html);
            //send to admin;
            $mail->send();
        }

        // Method: update customer's mobile number
        // POST Params: mobile_code, telephone
        // author: Devendra Dhayal, 26-12-2017
        public function updateCustomerTelephone(){
            $json = array();
            if(!isset($this->request['telephone'])){
                $json['error']['warning'] = "Please provide Telephone number.";
                return $json;
            }
            $this->load->model('account/customer');
            $customer = $this->model_account_customer->getCustomerByMobile($this->request['telephone']);
            if(!empty($customer)){
                $json['error']['warning'] = "Mobile no already exists for some other account.";
                return $json;
            }

            $data = array("mobile_country_code" => $this->request['mobile_code'], "telephone" => $this->request['telephone']);
            $this->model_account_customer->updateCustomerDetails($data,$this->customer->getId());
            $json['success'] = "Telephone number successfully updated.";
            return $json;
        }


        public function getCifStatus()
        {
            $data = array(
                'rbl_status' => 0,
                'rbl_balance' => 0
            );
            $customer_id = $this->customer->getId();
            if (empty($customer_id)) return $data;

            $obj = new RblPayment($this);
            $obj->registry = $this->registry;

            $data = $obj->getCifStatus($customer_id);

            $this->session->data['rbl_credit_user_credit_limit'] = $data['rbl_balance'];

            return $data;
        }


    }
?>