<?php

class ControllerCheckoutOnePageCheckout extends Controller {

    private $_free_shipping_coupon_applied = false;

    public function deleteCustomer($customer_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int) $customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int) $customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int) $customer_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int) $customer_id . "'");
    }

    public function index() {

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
        }

        if (isset($this->session->data['shipping_address_id'])) {
            unset($this->session->data['shipping_address_id']);
        }

        if (isset($this->session->data['shipping_postcode'])) {
            unset($this->session->data['shipping_postcode']);
        }

        if (isset($this->session->data['header_block'])) {
            unset($this->session->data['header_block']);
        }

        $data = array();

        /* Remove minimum Purchase price Limit for dropshipper */
        if (isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1) {
            $this->config->set('config_customer_cart_limit', $this->config->get('config_cart_limit'));
            $this->config->set('config_cart_limit', $this->config->get('config_dropshipper_cart_limit'));
        }
        // If store voucher applied then setting cart limit to 0
        $coupon_data = $this->cart->getCoupon();
        if (!isset($this->session->data['ORDER_FROM']) || $this->session->data['ORDER_FROM'] == 'IOSAPP') {
          if (!empty($coupon_data['coupon_franchise_cash']) || !empty($coupon_data['coupon_franchise_credit'])) {
              // franchise cash or credit coupon applied but customer is not logged in via franchise
              // so need to remove the coupon
              $this->cart->setCoupon( '' );
              $this->cart->setFranchiseId(0);
              $this->cart->setFranchiseMargin(0);
              $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
          }
        }
        
        if (
            (!empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code'])) ||
            !empty($coupon_data['coupon_store_trial']) ||
            !empty($coupon_data['coupon_franchise']) ||
            !empty($coupon_data['coupon_franchise_cash']) ||
            !empty($coupon_data['coupon_franchise_credit']) ||
            !empty($coupon_data['coupon_remove_cart_limit'])
        ) {
            $this->config->set('config_customer_cart_limit', 0);
            $this->config->set('config_cart_limit', 0);
        }

         //vernacular language for app
        if (!empty($this->request->get['language'])) 
         {
           $data['app_language'] = $this->request->get['language'];
           $this->session->data['app_language'] = $data['app_language'];
           $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
         }
         else if (!empty($this->session->data['app_language'])) 
          {
             $data['app_language'] = $this->session->data['app_language'];
             $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
           }
         else
         {
           $data['app_language'] = $this->config->get('config_language_id'); 
         }
         //----end code---

        $this->load->autoLoadLanguage('checkout/checkout', $data);
        $language = $data;

        if ((int) $this->config->get('config_cart_limit') == 0) {
            $language['text_cart_minimum'] = "";
        } else {
            $language['text_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'), $this->currency->format((float) ($this->config->get('config_cart_limit')), $this->currency->getCode(), 1));
        }

        $data['language'] = json_encode($language);

        if(isset($this->session->data['error'])){
            $data['prev_error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }

        if ($this->customer->isLogged()) {
            $this->load->model('account/customer');
            $data['customer_id'] = $this->customer->getId();
            if (isset($this->session->data['checkout_customer_id']) && $this->session->data['checkout_customer_id'] === true) {
                //cleanup previous incomplete checkout attempts
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['shipping_address']);
                unset($this->session->data['shipping_address_id']);
                unset($this->session->data['payment_address']);
                unset($this->session->data['payment_address_id']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);

                unset($this->session->data['guest']);
                unset($this->session->data['account']);
                unset($this->session->data['shipping_country_id']);
                unset($this->session->data['shipping_zone_id']);
                unset($this->session->data['payment_country_id']);
                unset($this->session->data['payment_zone_id']);
                unset($this->session->data['shipping_postcode']);
            }
        } else {
            $this->session->data['otp_verify_success'] = 3;
            $this->response->redirect($this->url->link('common/home', '', 'SSL'));
        }

        $store_id = $this->config->get('config_store_id');
        $data['international_store'] = 0;
        if ($store_id == INTERNATIONAL_STORE_ID)
            $data['international_store'] = 1;

        // Validate cart has products and has stock.
//    if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers']))
//        || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))
//        || (isset($this->session->data['error_cart_minimum']) && $this->session->data['error_cart_minimum']) ) {
//      $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
//    }

         //if (!isset($this->session->data['payment_zone_id '])) $this->session->data['payment_zone_id '] = '';

        if (isset($_REQUEST['product_id'])) {
            $this->cart->add($_REQUEST['product_id'], 1, null, null);
        }

        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) /* || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) */) {
            $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
        }

        //var_dump($this->session->data);

        $this->validate($data);
        $this->login(false, $data);
        //$this->guest(false, $data); //Remove guest validation
        $this->checkout(false, $data);
        $this->shipping_address(false, $data);
        $this->shipping_method(false, $data);
        $this->payment_method(false, $data);
        $this->payment_address(false, $data);

        //$this->confirm(false, $data);
        //var_dump($data);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('checkout/header');


        if (isset($this->request->get['quickconfirm'])) {
            $data['quickconfirm'] = $this->request->get['quickconfirm'];
        }

        $data['neo_credit_user_credit_limit']=0;
        $this->session->data['neo_credit_user_credit_limit'] = 0;

        if ($this->customer->isLogged()) {
            $data['firstname']  = $this->customer->getFirstName();
            $data['lastname']   = $this->customer->getLastName();
            $data['email']      = $this->customer->getEmail();
            $data['telephone']  = $this->customer->getTelephone();
            $data['payment_address_id'] = $this->customer->getAddressId();
            $data['address']    = $this->model_account_address->getAddress($this->customer->getAddressId());

            if ($this->customer->isCustomerCreditStatus('Neogrowth')) {
              $credit_payment_gateway = new CreditPayment($this);
              $neo_credit_user_credit_limit = $credit_payment_gateway->getCustomerNeoGrowthLimit($this->customer->getId());
              $data['neo_credit_user_credit_limit']=$neo_credit_user_credit_limit;
              $this->session->data['neo_credit_user_credit_limit'] = $neo_credit_user_credit_limit;
            }
        }
       
        $data['lazypay_user_credit_limit']  = $this->session->data['cart_total_value'] ?? 0;
        $this->session->data['lazypay_user_credit_limit'] = $this->session->data['cart_total_value'] ?? 0;
        /*if ($this->customer->isLogged()) {
            $data['firstname'] = $this->customer->getFirstName();
            $data['lastname'] = $this->customer->getLastName();
            $data['email'] = $this->customer->getEmail();
            $data['telephone'] = $this->customer->getTelephone();
            $data['payment_address_id'] = $this->customer->getAddressId();
            $data['address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            if ($this->customer->isCustomerLazypayStatus()) {
              $lazypay_payment_gateway = new LazypayPayment($this);
              $lazypay_user_credit_limit = $lazypay_payment_gateway->getCustomerLazypayLimit($this->customer->getId());
              if(!$lazypay_user_credit_limit) {
                $data['lazypay_user_credit_limit']= 0;
                $this->session->data['lazypay_user_credit_limit'] = 0;
              }
            }
        }*/


      //RBL Credit user limit, Set default values
        $data['rbl_credit_user_credit_limit']=0;
        $data['text_rbl_limit_error'] = '';
        $this->session->data['rbl_credit_user_credit_limit'] = 0;
        $data['text_rbl_limit_error'] =  sprintf($this->language->get('text_rbl_limit_error'),$this->currency->format((float) RBL_ORDER_LIMIT ));
        
        $this->cart(false, $data);
        if (isset($this->session->data['error_cart_minimum']) && $this->session->data['error_cart_minimum']) {
            $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
        }
        $data['countries'] = json_encode($data['countries']);

        $data['is_app'] = 0;
        if (isset($this->session->data['ORDER_FROM']) && $this->session->data['ORDER_FROM'] == 'ANDROIDAPP') {
            $data['is_app'] = 1;
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/one_page_checkout.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/one_page_checkout.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/checkout/one_page_checkout.tpl', $data));
        }
        /* if(CHECKOUT_LINK == 1){
          //version 2.0
          if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/one_page_checkout.tpl')) {
          $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/one_page_checkout.tpl', $data));
          } else {
          $this->response->setOutput($this->load->view('default/template/checkout/one_page_checkout.tpl', $data));
          }
          }else{
          // version 1.0
          $data['order_summary'] = $this->language->get('order_summary');
          $data['price_details'] = $this->language->get('price_details');
          //for mobile theme
          $data['steps_one'] = $this->language->get('steps_one');
          $data['steps_two'] = $this->language->get('steps_two');
          $data['steps_three'] = $this->language->get('steps_three');
          $data['button_back'] = $this->language->get('button_back');
          if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/new_checkout.tpl')) {
          $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/new_checkout.tpl', $data));
          } else {
          $this->response->setOutput($this->load->view('default/template/checkout/new_checkout.tpl', $data));
          }
          } */

        //$this->response->setOutput($this->load->view('checkout/one_page_checkout', $data));
//    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/one_page_checkout.tpl')) {
//      $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/one_page_checkout.tpl', $data));
//    } else {
//      $this->response->setOutput($this->load->view('default/template/checkout/one_page_checkout.tpl', $data));
//    }
        $this->session->data['_checkout'] = $data;
    }

    public function validate($data = array()) {
        
        $json = array();

        if (!$this->customer->isLogged()) {
            if (!empty($this->request->post['register']) && $this->request->post['register'] == "on") {
                $json = $this->register_validate($data);
            }
        }

        if (empty($json['error']))
            $json = $this->payment_address_validate();
        if (empty($json['error']))
            $json = $this->shipping_address_validate();
        if (empty($json['error']))
            $json = $this->shipping_method_validate();
        if (empty($json['error']))
            $json = $this->payment_method_validate();

        if(isset($this->request->post['upi_vpa']) && (isset($this->request->post['payment_method']) && $this->request->post['payment_method'] == 'upi')) {
          $upi_vpa = $this->request->post['upi_vpa'];

        //$vpa_check_expression = "[aA-zZ0-9\.\-]+@[aA-zZ0-9\.\-]+$";

      // $success = preg_match($vpa_check_expression, $upi_vpa, $match);
      // if ($success) {
      //  echo "Match: ".$match[0]."<br />"; 
      //  echo "Group 1: ".$match[1]."<br />"; 
      //  echo "Group 2: ".$match[2]."<br />"; 
      // }
      // die;
          (int) $length = strlen($upi_vpa);
          if((int) $length == 0){
            $json['error']['warning'] = "Please enter your UPI VPA";
          } else {
            $pos = strpos($upi_vpa, '@');
            $pos_next = strpos($upi_vpa, '@', $pos+1);
            if($pos == 0 || $pos == ($length-1) || $pos === FALSE || $pos_next > 0){
              $json['error']['warning'] = "Invalid UPI VPA";
            }
          }
        }

        // original code
        /*
          $json = array();
          if (isset($_REQUEST['register']) && !empty($_REQUEST['register']))
          {
          $json = $this->register_validate($data);
          }
          else
          {
          if (!isset($this->session->data['customer_id'])) $json = $this->guest_validate($data);
          }

          //if (!isset($json['error'])) $json = array_merge($json, $this->payment_address_validate());
          if (!isset($json['error'])) $json = array_merge($json, $this->shipping_address_validate());

          if (!isset($json['error'])) $json = array_merge($json, $this->shipping_method_validate());

          if (!isset($json['error'])) $json = array_merge($json, $this->payment_method_validate());

          $this->response->setOutput(json_encode($json));
         */

        // check wether current total of cart satisfied the minimum cart amount condition or not.
        /* Remove minimum Purchase price Limit for dropshipper */
        if (isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1) {
            $this->config->set('config_customer_cart_limit', $this->config->get('config_cart_limit'));
            $this->config->set('config_cart_limit', $this->config->get('config_dropshipper_cart_limit'));
        }
        // If store voucher applied then setting cart limit to 0
        $coupon_data = $this->cart->getCoupon();
        
        if ( !empty( $coupon_data['coupon'] )) {
            $this->validateFreeShippingCoupon( $coupon_data['coupon'] );
        } else {
            $this->_free_shipping_coupon_applied = false;
            if ( isset($this->session->data['free_shipping_coupon_applied'] )) {
                unset($this->session->data['free_shipping_coupon_applied']);
            }
        }

        if (
            (!empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code'])) ||
            !empty($coupon_data['coupon_store_trial']) ||
            !empty($coupon_data['coupon_franchise']) ||
            !empty($coupon_data['coupon_franchise_cash']) ||
            !empty($coupon_data['coupon_franchise_credit']) ||
            !empty($coupon_data['coupon_remove_cart_limit'])
        ) {
            $this->config->set('config_customer_cart_limit', 0);
            $this->config->set('config_cart_limit', 0);
        }

        $cartSubTotal = 0.0;
        $total_factory = new TotalFactory($this, true);
        $total_data = $total_factory->getTotal(true, $this->_free_shipping_coupon_applied);
        /*         * Discount on Taxes with coupon code (Ravindra Singh 16-02-2016) End* */
        foreach ($total_data as $total) {
            if ($total['code'] == 'sub_total') {
                $cartSubTotal = $total['value'];
            }
        }
        $exception_customer_ids = array(52);
        if ($this->customer->isLogged()
                and in_array($this->customer->getId(), $exception_customer_ids)) {
            
        } else {
            if ($cartSubTotal * $this->currency->getValue() == 0 || $cartSubTotal * $this->currency->getValue() < (float) ($this->config->get('config_cart_limit'))) {
                $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function country($data = array()) {
        $json = array();

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id' => $country_info['country_id'],
                'name' => $country_info['name'],
                'iso_code_2' => $country_info['iso_code_2'],
                'iso_code_3' => $country_info['iso_code_3'],
                'address_format' => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone' => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status' => $country_info['status']
            );
        }

        $this->response->setOutput(json_encode($json));
    }

    //validate

    public function login_validate($data = array()) {

        $this->load->language('checkout/checkout');

        $json = array();

        if ($this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
        }

        if (!$json) {
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
                $json['error']['warning'] = $this->language->get('error_login');
            }
        }

        if (!$json) {
            unset($this->session->data['guest']);

            // Default Addresses
            $this->load->model('account/address');

            $address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

            if ($address_info) {

                if ($this->config->get('config_tax_customer') == 'payment') {
                    $this->session->data['payment_addess'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                }

                if ($this->config->get('config_tax_customer') == 'shipping') {
                    $this->session->data['shipping_addess'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                }
            } else {
                unset($this->session->data['shipping_country_id']);
                unset($this->session->data['shipping_zone_id']);
                unset($this->session->data['shipping_postcode']);
                unset($this->session->data['payment_country_id']);
                unset($this->session->data['payment_zone_id']);
            }

            $json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        $this->response->setOutput(json_encode($json));
    }

    public function guest_validate() {

        $this->load->language('checkout/checkout');


        $json = array();

        // Validate if customer is logged in.
        if ($this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
        }

        // Check if guest checkout is available.
        if (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price') || $this->cart->hasDownload()) {
            $json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        if (!$json) {
            if (isset($this->request->post['firstname']) && ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32))) {
                $json['error']['firstname'] = $this->language->get('error_firstname');
            }

            if (isset($this->request->post['lastname']) && ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32))) {
                $json['error']['lastname'] = $this->language->get('error_lastname');
            }

            if (isset($this->request->post['email']) && ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email']))) {
                $json['error']['email'] = $this->language->get('error_email');
            }

            if (isset($this->request->post['telephone']) && ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32))) {
                $json['error']['telephone'] = $this->language->get('error_telephone');
            }

            if (isset($this->request->post['address_1']) && ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128))) {
                $json['error']['address_1'] = $this->language->get('error_address_1');
            }

            if (isset($this->request->post['city']) && ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128))) {
                $json['error']['city'] = $this->language->get('error_city');
            }

            $this->load->model('localisation/country');
            $country_info = array();
            if (isset($this->request->post['country_id']))
                $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

            if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
                $json['error']['postcode'] = $this->language->get('error_postcode');
            }

            if (isset($this->request->post['country_id']) && $this->request->post['country_id'] == '') {
                $json['error']['country'] = $this->language->get('error_country');
                $json['error']['country_id'] = $this->language->get('error_country');
            }

            if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
                $json['error']['zone'] = $this->language->get('error_zone');
                $json['error']['zone_id'] = $this->language->get('error_zone');
            }

        }

        if (!$json) {
            $this->session->data['account'] = 'guest';

            $this->session->data['guest']['firstname'] = $this->request->post['firstname'];
            $this->session->data['guest']['lastname'] = $this->request->post['lastname'];
            $this->session->data['guest']['email'] = $this->request->post['email'];
            $this->session->data['guest']['telephone'] = $this->request->post['telephone'];

            $this->session->data['payment_address']['firstname'] = $this->request->post['firstname'];
            $this->session->data['payment_address']['lastname'] = $this->request->post['lastname'];
            $this->session->data['payment_address']['company'] = $this->request->post['company'];
            $this->session->data['payment_address']['address_1'] = $this->request->post['address_1'];
            $this->session->data['payment_address']['address_2'] = $this->request->post['address_2'];
            $this->session->data['payment_address']['postcode'] = $this->request->post['postcode'];
            $this->session->data['payment_address']['city'] = $this->request->post['city'];
            $this->session->data['payment_address']['country_id'] = $this->request->post['country_id'];
            $this->session->data['payment_address']['zone_id'] = $this->request->post['zone_id'];

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

            if ($country_info) {
                $this->session->data['payment_address']['country'] = $country_info['name'];
                $this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['payment_address']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['payment_address']['country'] = '';
                $this->session->data['payment_address']['iso_code_2'] = '';
                $this->session->data['payment_address']['iso_code_3'] = '';
                $this->session->data['payment_address']['address_format'] = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

            if ($zone_info) {
                $this->session->data['payment_address']['zone'] = $zone_info['name'];
                $this->session->data['payment_address']['zone_code'] = $zone_info['code'];
            } else {
                $this->session->data['payment_address']['zone'] = '';
                $this->session->data['payment_address']['zone_code'] = '';
            }

            if (!empty($this->request->post['shipping_address'])) {
                $this->session->data['guest']['shipping_address'] = $this->request->post['shipping_address'];
            } else {
                $this->session->data['guest']['shipping_address'] = false;
            }

            // Default Payment Address
            if ($this->session->data['guest']['shipping_address'] || $this->session->data['shipping_address']) {
                $this->session->data['shipping_address']['firstname'] = $this->request->post['firstname'];
                $this->session->data['shipping_address']['lastname'] = $this->request->post['lastname'];
                $this->session->data['shipping_address']['company'] = $this->request->post['company'];
                $this->session->data['shipping_address']['address_1'] = $this->request->post['address_1'];
                $this->session->data['shipping_address']['address_2'] = $this->request->post['address_2'];
                $this->session->data['shipping_address']['postcode'] = $this->request->post['postcode'];
                $this->session->data['shipping_address']['city'] = $this->request->post['city'];
                $this->session->data['shipping_address']['country_id'] = $this->request->post['country_id'];
                $this->session->data['shipping_address']['zone_id'] = $this->request->post['zone_id'];

                if ($country_info) {
                    $this->session->data['shipping_address']['country'] = $country_info['name'];
                    $this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
                    $this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
                    $this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
                } else {
                    $this->session->data['shipping_address']['country'] = '';
                    $this->session->data['shipping_address']['iso_code_2'] = '';
                    $this->session->data['shipping_address']['iso_code_3'] = '';
                    $this->session->data['shipping_address']['address_format'] = '';
                }

                if ($zone_info) {
                    $this->session->data['shipping_address']['zone'] = $zone_info['name'];
                    $this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
                } else {
                    $this->session->data['shipping_address']['zone'] = '';
                    $this->session->data['shipping_address']['zone_code'] = '';
                }
            }

            //      unset($this->session->data['shipping_method']);
            //      unset($this->session->data['shipping_methods']);
            //      unset($this->session->data['payment_method']);
            //      unset($this->session->data['payment_methods']);
        }

        return $json;
    }

    public function register_validate(&$data = array()) {

        $this->load->language('checkout/checkout');

        $this->load->model('account/customer');

        $json = array();

        // Validate if customer is already logged out.
        if ($this->customer->isLogged()) {
            //$json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');

                break;
            }
        }

        if (!$json) {

            $this->load->model('account/customer');

            if (isset($this->request->post['firstname']) && ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32))) {
                $json['error']['firstname'] = $this->language->get('error_firstname');
            }

            if (isset($this->request->post['lastname']) && ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32))) {
                $json['error']['lastname'] = $this->language->get('error_lastname');
            }

            /* if (isset($this->request->post['reg_email']) && ((utf8_strlen($this->request->post['reg_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['reg_email']))) {
              $json['error']['reg_email'] = $this->language->get('error_email');
              } */

            if (!empty($this->request->post['reg_email']) && ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['reg_email']))) {
                $json['error']['warning'] = $this->language->get('error_exists');
            }

            /* if (!empty($this->request->post['reg_email']) && ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['reg_email']))) {
              $json['error']['warning'] = $this->language->get('error_exists');
              } */

            if (!preg_match('/^[0-9]*$/', $this->request->post['reg_telephone']) || (utf8_strlen($this->request->post['reg_telephone']) < 10) || (utf8_strlen($this->request->post['reg_telephone']) > 10)) {
                $json['error']['reg_telephone'] = $this->language->get('error_telephone');
            }

            if (!empty($this->request->post['reg_telephone']) && $this->model_account_customer->getTotalCustomersByTelephone($this->request->post['reg_telephone'])) {
                $json['error']['warning'] = $this->language->get('error_exists_telephone');
            }


// comment by manoj
            /*
              if (isset($this->request->post['address_1']) && ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128))) {
              $json['error']['address_1'] = $this->language->get('error_address_1');
              }

              if (isset($this->request->post['city']) && ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128))) {
              $json['error']['city'] = $this->language->get('error_city');
              }

              $this->load->model('localisation/country');

              $country_info = $this->model_localisation_country->getCountry(isset($this->request->post['country_id'])?$this->request->post['country_id']:0);

              if ($country_info) {
              if (isset($country_info['postcode_required']) && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
              $json['error']['postcode'] = $this->language->get('error_postcode');
              }


              // VAT Validation
              $this->load->helper('vat');

              if ($this->config->get('config_vat') && $this->request->post['tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
              $json['error']['tax_id'] = $this->language->get('error_vat');
              }
              }


              if (!isset($this->request->post['country_id']) || $this->request->post['country_id'] == '') {
              $json['error']['country_id'] = $this->language->get('error_country');
              }

              if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
              $json['error']['zone_id'] = $this->language->get('error_zone');
              }
             */
            if (isset($this->request->post['register']) && ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20))) {
                $json['error']['password'] = $this->language->get('error_password');
            }

            if (isset($this->request->post['confirm']) && ($this->request->post['confirm'] != $this->request->post['password'])) {
                $json['error']['confirm'] = $this->language->get('error_confirm');
            }
// comment by manoj
            /*
              if (!isset($this->request->post['payment_method'])) {
              $json['error']['warning'] = $this->language->get('error_payment');
              } elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
              //        error_log(print_r($this->session->data['payment_methods'],1));
              $json['error']['warning'] = $this->language->get('error_payment');
              }
             */

            if ($this->config->get('config_account_id')) {
                $this->load->model('catalog/information');

                $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

                if ($information_info && !isset($this->request->post['agree'])) {
                    $json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
                }
            }
        }

        if (!$json) {
            //uncomment this
            $this->session->data['account'] = 'register';
            if (!$this->customer->isLogged()) {
                if (empty($this->request->post['reg_email'])) {
                    $this->request->post['reg_email'] = 'noemailwsb@gmail.com';
                }

                $this->session->data['checkout_customer_id'] = $customer_id = $this->model_account_customer->addCustomer($this->request->post);

                // Clear any previous login attempts for unregistered accounts.
                $this->model_account_customer->deleteLoginAttempts($this->request->post['reg_telephone']);

                //$this->session->data['checkout_customer_id'] = true;
                $this->load->model('account/customer');
            }

            $this->customer->login($this->request->post['reg_telephone'], $this->request->post['password']);

            // Default Payment Address
            $this->load->model('account/address');

            $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());

            if (!empty($this->request->post['shipping_address'])) {
                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }
            
            unset($this->session->data['guest']);

        }
        if (!empty($json))
            return $json;
    }

    public function payment_address_validate(&$data = array()) {

        $this->load->language('checkout/checkout');

        $json = array();

        // Validate if customer is logged in.
        if (!$this->customer->isLogged()) {
            //$json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
//      $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');

                break;
            }
        }


        if (!$json) {
            if (isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'existing') {
                $this->load->model('account/address');

                $support_number = 8696491521;
                /* $request['customer_id'] = $this->customer->getId();
                  $request['token']       = $this->customer->getAccessToken();

                  $data_json = json_encode($request);
                  $api_url = CRM_URL."cron/getAgentofCustomer";
                  $ch = curl_init($api_url);
                  curl_setopt($ch, CURLOPT_HEADER, 0);
                  curl_setopt($ch, CURLOPT_HTTPHEADER,
                  array('Content-Type: application/json',
                  'Content-Length: ' . strlen($data_json))
                  );
                  curl_setopt($ch, CURLOPT_VERBOSE, 1);
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $result = curl_exec($ch);
                  $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                  $result = json_decode($result, true );

                  if($result['status'] == 1)
                  {
                  if(!empty($result['agent'])) { $support_number = $result['agent'];  }
                  else if(!empty($result['team_lead'])) { $support_number = $result['team_lead']; }
                  else { $support_number = $result['support']; }
                  } */

                if (empty($this->request->post['payment_address_id'])) {
                    $json['error']['warning'] = sprintf($this->language->get('error_address_payment'), $support_number);
                } else {
                    $arrayAddresses = array();
                    $getAllAddresses = $this->model_account_address->getAddresses();
                    if (is_array($getAllAddresses) && !empty($getAllAddresses)) {
                        foreach ($getAllAddresses as $valAddress) {
                            $arrayAddresses[] = $valAddress['address_id'];
                        }
                    }

                    if (!in_array($this->request->post['payment_address_id'], $arrayAddresses))
                        $json['error']['warning'] = sprintf($this->language->get('error_address_payment'), $support_number);
                }

                if (!$json) {
                    // Default Payment Address
                    $this->load->model('account/address');

                    $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->post['payment_address_id']);

                    //unset($this->session->data['payment_method']);
                    //unset($this->session->data['payment_methods']);
                }
            } else if (isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'new') {
                if (!isset($this->request->post['address_1']) || ( (utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128))) {
                    $json['error']['address_1'] = $this->language->get('error_address_1');
                }

                if (!isset($this->request->post['city']) || ( (utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 32))) {
                    $json['error']['city'] = $this->language->get('error_city');
                }

                $this->load->model('localisation/country');

                if (isset($this->request->post['country_id']))
                    $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

                if (isset($country_info) && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
                    $json['error']['postcode'] = $this->language->get('error_postcode');
                }

                if (!isset($this->request->post['country_id']) || ($this->request->post['country_id'] == '')) {
                    $json['error']['country_id'] = $this->language->get('error_country');
                }

                if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
                    $json['error']['zone_id'] = $this->language->get('error_zone');
                }

                if (!$json) {

                    // Default Shipping Address
                    $this->load->model('account/address');
                    if (isset($this->request->post['payment_method']) && isset($this->request->post['shipping_method'])) {
                        $address_id = $this->model_account_address->addAddress($this->request->post);
                        $this->session->data['payment_address'] = $this->model_account_address->getAddress($address_id);
                        //      $this->session->data['payment_country_id'] = $this->request->post['payment_country_id'];
                        //      $this->session->data['payment_zone_id'] = $this->request->post['payment_zone_id'];
                        //      $this->session->data['payment_postcode'] = $this->request->post['payment_postcode'];
                    }
                }
            } else {
                /* if (!isset($this->request->post['firstname']) || ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32))) {
                  $json['error']['firstname'] = $this->language->get('error_firstname');
                  }else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->request->post['firstname'])){
                  $json['error']['firstname'] = $this->language->get('error_special_character');
                  }else if(is_numeric($this->request->post['firstname'])){
                  $json['error']['firstname'] = $this->language->get('error_numeric');
                  }

                  if (!isset($this->request->post['lastname']) || ( (utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32))) {
                  $json['error']['lastname'] = $this->language->get('error_lastname');
                  }else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->request->post['lastname'])){
                  $json['error']['lastname'] = $this->language->get('error_special_character');
                  }else if(is_numeric($this->request->post['lastname'])){
                  $json['error']['lastname'] = $this->language->get('error_numeric');
                  } */

                if (!isset($this->request->post['address_1']) || ( (utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128))) {
                    $json['error']['address_1'] = $this->language->get('error_address_1');
                }

                if (!isset($this->request->post['city']) || ( (utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 32))) {
                    $json['error']['city'] = $this->language->get('error_city');
                }

                $this->load->model('localisation/country');

                if (isset($this->request->post['country_id']))
                    $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

                if (isset($country_info) && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
                    $json['error']['postcode'] = $this->language->get('error_postcode');
                }

                if (!isset($this->request->post['country_id']) || ($this->request->post['country_id'] == '')) {
                    $json['error']['country_id'] = $this->language->get('error_country');
                }

                if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
                    $json['error']['zone_id'] = $this->language->get('error_zone');
                }

                if (!$json) {
                    // Default Payment Address
                    $this->load->model('account/address');

                    $_shipping_address = array();
                    foreach ($this->request->post as $key => $value) {
                        if (strpos($key, 'shipping_') !== false)
                            $_shipping_address[str_replace('shipping_', '', $key)] = $value;
                    }
                    if (isset($this->request->post['payment_method']) && isset($this->request->post['shipping_method'])) {
                        $address_id = $this->model_account_address->addAddress($this->request->post);
                        $this->model_account_address->addAddress($_shipping_address);

                        $this->session->data['payment_address'] = $this->model_account_address->getAddress($address_id);
                    }
                }
            }
        }


        return $json;
    }

    public function shipping_address_validate(&$data = array()) {

        $this->load->language('checkout/checkout');

        $json = array();

        // Validate if customer is logged in.
        if (!$this->customer->isLogged()) {
            //$json['redirect'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        // Validate if shipping is required. If not the customer should not have reached this page.
        if (!$this->cart->hasShipping()) {
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
                if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');

                break;
            }
        }

        if (!$json) {

            if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
                $this->load->model('account/address');

                if (empty($this->request->post['shipping_address_id'])) {
                    $json['error']['warning'] = $this->language->get('error_address');
                } elseif (!in_array($this->request->post['shipping_address_id'], array_column($this->model_account_address->getAddresses(), 'address_id'))) {
                    $json['error']['warning'] = $this->language->get('error_address');
                }

                if (!$json) {
                    $this->session->data['shipping_address_id'] = $this->request->post['shipping_address_id'];

                    // Default Shipping Address
                    $this->load->model('account/address');

                    $address_info = $this->model_account_address->getAddress($this->request->post['shipping_address_id']);

                    if ($address_info) {
                        $this->session->data['shipping_country_id'] = $address_info['country_id'];
                        $this->session->data['shipping_zone_id'] = $address_info['zone_id'];
                        $this->session->data['shipping_postcode'] = $address_info['postcode'];
                    } else {
                        unset($this->session->data['shipping_country_id']);
                        unset($this->session->data['shipping_zone_id']);
                        unset($this->session->data['shipping_postcode']);
                    }
                }
            }


            if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'new') {
                if ((utf8_strlen($this->request->post['shipping_firstname']) < 1) || (utf8_strlen($this->request->post['shipping_firstname']) > 32)) {
                    $json['error']['shipping_firstname'] = $this->language->get('error_firstname');
                } else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->request->post['shipping_firstname'])) {
                    $json['error']['shipping_firstname'] = $this->language->get('error_special_character');
                } else if (is_numeric($this->request->post['shipping_firstname'])) {
                    $json['error']['shipping_firstname'] = $this->language->get('error_numeric');
                }

                /* if ((utf8_strlen($this->request->post['shipping_lastname']) < 1) || (utf8_strlen($this->request->post['shipping_lastname']) > 32)) {
                  $json['error']['shipping_lastname'] = $this->language->get('error_lastname');
                  }else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->request->post['shipping_lastname'])){
                  $json['error']['shipping_lastname'] = $this->language->get('error_special_character');
                  }else if(is_numeric($this->request->post['shipping_lastname'])){
                  $json['error']['shipping_lastname'] = $this->language->get('error_numeric');
                  } */

                if ((utf8_strlen($this->request->post['shipping_address_1']) < 3) || (utf8_strlen($this->request->post['shipping_address_1']) > 128)) {
                    $json['error']['shipping_address_1'] = $this->language->get('error_address_1');
                }

                if ((utf8_strlen($this->request->post['shipping_city']) < 2) || (utf8_strlen($this->request->post['shipping_city']) > 128)) {
                    $json['error']['shipping_city'] = $this->language->get('error_city');
                }

                $this->load->model('localisation/country');

                $country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);

                if (isset($country_info) && $country_info['postcode_required'] && (utf8_strlen($this->request->post['shipping_postcode']) < 2) || (utf8_strlen($this->request->post['shipping_postcode']) > 10)) {
                    $json['error']['shipping_postcode'] = $this->language->get('error_postcode');
                }

                if ($this->request->post['shipping_country_id'] == '') {
                    $json['error']['shipping_country'] = $this->language->get('error_country');
                }

                if (!isset($this->request->post['shipping_zone_id']) || $this->request->post['shipping_zone_id'] == '') {
                    $json['error']['shipping_zone_id'] = $this->language->get('error_zone');
                }

                if (!$json) {

                    // Default Shipping Address
                    $this->load->model('account/address');
                    $_shipping_address = array();
                    foreach ($this->request->post as $key => $value) {
                        if (strpos($key, 'shipping_') !== false)
                            $_shipping_address[str_replace('shipping_', '', $key)] = $value;
                    }

                    if (isset($this->request->post['payment_method']) && isset($this->request->post['shipping_method'])) {
                        $this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($_shipping_address);
                        $this->session->data['shipping_country_id'] = $this->request->post['shipping_country_id'];
                        $this->session->data['shipping_zone_id'] = $this->request->post['shipping_zone_id'];
                        $this->session->data['shipping_postcode'] = $this->request->post['shipping_postcode'];
                    }
                }
            }
        }
        //echo $json;
        //exit;
        return $json;
    }

    public function shipping_method_validate(&$data = array()) {

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
            $shipping_address = isset($this->session->data['guest']['shipping']) ? $this->session->data['guest']['shipping'] : '';
        }

        if (empty($shipping_address)) {
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
                if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');

                break;
            }
        }
        $json_show = 0;
        if (!$json) {
            if (!isset($this->request->post['shipping_method'])) {
                if (isset($this->session->data['shipping_method']['code']) && $this->session->data['shipping_method']['code'] == 'free.free') {
                    $json_show = 1;
                } else if (isset($this->session->data['shipping_method'])) {
                    $json_show = 1;
                } else {
                    $json['error']['warning'] = $this->language->get('error_shipping');
                }
            } else {
                $shipping = explode('.', $this->request->post['shipping_method']);
                if (!isset($shipping[0]) || !isset($shipping[1])/* || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]) */) {
                    $json['error']['warning'] = $this->language->get('error_shipping');
                }
            }

            if (!$json && $json_show == 0) {
                $shipping = explode('.', $this->request->post['shipping_method']);
                if (isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]))
                    $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

                $this->session->data['comment'] = (isset($this->request->post['comment'])) ? strip_tags($this->request->post['comment']) : '';
            }
        }

        return $json;
    }

    public function payment_method_validate(&$data = array()) {

        $this->load->language('checkout/checkout');

        $json = array();

        // Validate if payment address has been set.
        $this->load->model('account/address');

        if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } elseif (isset($this->session->data['guest']['payment'])) {
            $payment_address = $this->session->data['guest']['payment'];
        } else {
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
                if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');

                break;
            }
        }

        if (!$json) {
            if (!isset($this->request->post['payment_method'])) {
                $json['error']['warning'] = $this->language->get('error_payment');
            } elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
//        error_log(print_r($this->session->data['payment_methods'],1));
                $json['error']['warning'] = $this->language->get('error_payment');
            }

            if ($this->config->get('config_checkout_id')) {
                $this->load->model('catalog/information');

                $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

                if ($information_info && !isset($this->request->post['agree'])) {
                    $json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
                }
            }

            if (isset($this->request->post['payment_method']) && isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
                $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
            } else {
                $this->session->data['payment_method'] = '';
            }
            $this->session->data['discount_type'] = (isset($this->request->post['getOffer']) ? $this->request->post['getOffer'] : '' );
            $this->session->data['comment'] = (isset($this->request->post['comment'])) ? strip_tags($this->request->post['comment']) : '';
        }
        return $json;
    }

    public function shipping_address($render = true, &$data = array()) {

        $this->load->language('checkout/checkout');

        $data['text_address_existing'] = $this->language->get('text_address_existing');
        $data['text_address_new'] = $this->language->get('text_address_new');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');

        //$data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['firstname_entry'] = $this->language->get('firstname_entry');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['entry_company'] = $this->language->get('entry_company');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_zone'] = $this->language->get('entry_zone');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_place_order'] = $this->language->get('button_place_order');

        if (isset($this->session->data['shipping_address_id'])) {
            $data['shipping_address_id'] = $this->session->data['shipping_address_id'];
        } else {
            $data['shipping_address_id'] = $this->customer->getAddressId();
        }

        $this->load->model('account/address');

        $data['addresses'] = $this->model_account_address->getAddresses();
        $data['shipping_addresses'] = json_encode($data['addresses']);
        //$this->session->data['addresses'] = $data['addresses'];

        if (isset($this->session->data['shipping_postcode'])) {
            $data['postcode'] = $this->session->data['shipping_postcode'];
        } else {
            $data['postcode'] = '';
        }

        if (isset($this->session->data['shipping_country_id'])) {
            $data['country_id'] = $this->session->data['shipping_country_id'];
        } else {
            $data['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($this->session->data['shipping_zone_id'])) {
            $data['zone_id'] = $this->session->data['shipping_zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        if ($render !== false) {

            $this->response->setOutput($this->load->view('checkout/shipping_address', $data));
        }
    }

    public function shipping_method($render = true, &$data = array()) {

        $this->load->language('checkout/checkout');

        $this->load->model('account/address');

        $shipping_address = array('country_id' => 0, 'zone_id' => 0, 'firstname' => '', 'lastname' => '', 'company' => '', 'address_1' => '');

        if (isset($this->session->data['shipping_address'])) {
            if (isset($this->request->post['same_address_shipping']) && $this->request->post['same_address_shipping'] == 1) {
                $country_id = $this->request->post['country_id'];
                $zone_id = $this->request->post['zone_id'];
            } else if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'new') {
                $country_id = $this->request->post['shipping_country_id'];
                $zone_id = $this->request->post['shipping_zone_id'];
            } else {
                $shipping_address = $this->session->data['shipping_address'];
            }
        } else if ($this->customer->isLogged()) {
            if (isset($this->request->post['same_address_shipping']) && $this->request->post['same_address_shipping'] == 1) {
                $country_id = $this->request->post['country_id'];
                $zone_id = $this->request->post['zone_id'];
            } else if (isset($this->request->post['shipping_address'])) {
                if ($this->request->post['shipping_address'] == 'new') {
                    $country_id = $this->request->post['shipping_country_id'];
                    $zone_id = $this->request->post['shipping_zone_id'];
                }
            } else if (isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'new') {
                $country_id = $this->request->post['country_id'];
                $zone_id = $this->request->post['zone_id'];
            }
        } else if (isset($this->request->post['country_id'])) {
            $country_id = $this->request->post['country_id'];
            $zone_id = $this->request->post['zone_id'];
        }

        if (isset($country_id)) {
            $this->session->data['guest']['shipping'] = array_merge($shipping_address, isset($this->session->data['guest']['shipping']) ? $this->session->data['guest']['shipping'] : array(), array('country_id' => $country_id,
                'zone_id' => $zone_id,
                'city' => $this->request->post['city'],
                'postcode' => $this->request->post['postcode']));

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($country_id);

            if ($country_info) {
                $this->session->data['guest']['shipping']['country'] = $country_info['name'];
                $this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['guest']['shipping']['country'] = '';
                $this->session->data['guest']['shipping']['iso_code_2'] = '';
                $this->session->data['guest']['shipping']['iso_code_3'] = '';
                $this->session->data['guest']['shipping']['address_format'] = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($zone_id);

            if ($zone_info) {
                $this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
                $this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
            } else {
                $this->session->data['guest']['shipping']['zone'] = '';
                $this->session->data['guest']['shipping']['zone_code'] = '';
            }

            $this->session->data['shipping_country_id'] = $country_id;
            $this->session->data['shipping_zone_id'] = $zone_id;
            $this->session->data['shipping_postcode'] = $this->request->post['postcode'];

            $shipping_address = $this->session->data['guest']['shipping'];
        } elseif ($this->customer->isLogged()) {
            if (isset($this->request->post['same_address_shipping']) && $this->request->post['same_address_shipping'] == 1) {
                $shipping_address['country_id'] = $this->request->post['country_id'];
                $shipping_address['zone_id'] = $this->request->post['zone_id'];
            } else if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'new') {
                $shipping_address['country_id'] = $this->request->post['shipping_country_id'];
                $shipping_address['zone_id'] = $this->request->post['shipping_zone_id'];
            } else {
                $shipping_address_id = (isset($this->request->post['shipping_address_id']) ? $this->request->post['shipping_address_id'] :
                        (isset($this->session->data['shipping_address_id']) ? $this->session->data['shipping_address_id'] : null));

                $payment_address_id = (isset($this->request->post['payment_address_id']) ? $this->request->post['payment_address_id'] :
                        (isset($this->session->data['payment_address_id']) ? $this->session->data['payment_address_id'] : null));

                if ($shipping_address_id) {
                    $shipping_address = $this->model_account_address->getAddress($shipping_address_id);
                    $data['shipping_address_id'] = $this->session->data['shipping_address_id'] = $shipping_address_id;
                } else if ($payment_address_id) {
                    $shipping_address = $this->model_account_address->getAddress($payment_address_id);
                }
            }

            /* $shipping_address_id = (isset($this->request->post['shipping_address_id'])?$this->request->post['shipping_address_id']:
              (isset($this->session->data['shipping_address_id'])?$this->session->data['shipping_address_id']:null));

              $payment_address_id = (isset($this->request->post['payment_address_id'])?$this->request->post['payment_address_id']:
              (isset($this->session->data['payment_address_id'])?$this->session->data['payment_address_id']:null));

              if ($shipping_address_id){
              $shipping_address = $this->model_account_address->getAddress($shipping_address_id);
              $data['shipping_address_id'] = $this->session->data['shipping_address_id'] = $shipping_address_id;
              } else if ($payment_address_id) {
              $shipping_address = $this->model_account_address->getAddress($payment_address_id);
              } */
        } elseif (isset($this->session->data['guest']['shipping'])) {
            $shipping_address = array_merge($shipping_address, $this->session->data['guest']['shipping']);
        }


        if (isset($shipping_address)) {

            $this->session->data['shipping_address'] = $shipping_address;
            // Shipping Methods
            $method_data = array();

            $this->load->model('extension/extension');

            $results = $this->model_extension_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {

                    if ($result['code'] != "free" || $this->_free_shipping_coupon_applied) {

                        $this->load->model('shipping/' . $result['code']);

                        $quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address);

                        if ($quote) {
                            $method_data[$result['code']] = array(
                                'title' => $quote['title'],
                                'quote' => $quote['quote'],
                                'sort_order' => $quote['sort_order'],
                                'error' => $quote['error']
                            );
                        }
                    }
                }
            }

            // both values are set in cart page.
            if (isset($this->session->data['cart_total_value']) && !empty($this->session->data['cart_total_value'])) {
                $cart_total_value = $this->session->data['cart_total_value'];
            } else {
                $cart_total_value = '';
            }
            if (isset($this->session->data['config_customer_cart_limit_value']) && !empty($this->session->data['config_customer_cart_limit_value'])) {
                $config_customer_cart_limit_value = $this->session->data['config_customer_cart_limit_value'];
            } else {
                $config_customer_cart_limit_value = '';
            }
            $store_id = $this->config->get('config_store_id');
            if ($store_id != INTERNATIONAL_STORE_ID) {
                if ($this->customer->isLogged()) {
                    if ($config_customer_cart_limit_value > $cart_total_value) {
                        if ($this->customer->is_dropshipper == 1) {
                            if (isset($method_data['weight'])) {
                                foreach ((array) $method_data['weight']['quote'] as $key => $value) {
                                    if ($key != 'weight_8') {
                                        unset($method_data['weight']['quote'][$key]);
                                    }
                                }
                            }
                        } else {
                            if (isset($method_data['weight']['quote']['weight_8'])) {
                                unset($method_data['weight']['quote']['weight_8']);
                            }
                        }
                    } else {
                        if ($this->customer->is_dropshipper == 1) {
                            // All show shipping charges....
                        } else {
                            if (isset($method_data['weight']['quote']['weight_8'])) {
                                unset($method_data['weight']['quote']['weight_8']);
                            }
                        }
                    }
                }
            } else {
                if (isset($method_data['weight']['quote']['weight_8'])) {
                    unset($method_data['weight']['quote']['weight_8']);
                }
            }
            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $this->session->data['shipping_methods'] = $method_data;
        }

        // free shipping method don't show during page redirect to checkout form cart page
        //unset($this->session->data['shipping_methods']['free']);
        //----------------------------------------------------------
        //echo "<pre>"; print_r($method_data);
        $data['text_checkout_shipping_method'] = sprintf($this->language->get('text_checkout_shipping_method'), 4);
        $data['text_shipping_method'] = $this->language->get('text_shipping_method');
        $data['text_comments'] = $this->language->get('text_comments');
        $data['text_loading'] = $this->language->get('text_loading');

        $data['button_continue'] = $this->language->get('button_continue');

        if (empty($this->session->data['shipping_methods'])) {
            $data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['shipping_methods'])) {
            $data['shipping_methods'] = $this->session->data['shipping_methods'];
        } else {
            $data['shipping_methods'] = array();
        }

        if (isset($this->session->data['shipping_method']['code'])) {
            $data['code'] = $this->session->data['shipping_method']['code'];
        } else {
            $data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }



        if ($render !== false) {

            //$this->response->setOutput($this->load->view('checkout/easy_shipping_method', $data));
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/easy_shipping_method.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/easy_shipping_method.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/checkout/easy_shipping_method.tpl', $data));
            }
        }
    }

    public function payment_address($render = true, &$data = array()) {

        $this->load->language('checkout/checkout');

        if (isset($this->request->get['is_dropshipper'])) {
            $data['is_dropshipper'] = $this->request->get['is_dropshipper'];
            $this->is_dropshipper = 2;
        } else {
            $data['is_dropshipper'] = 0;
        }

        $data['text_address_existing'] = $this->language->get('text_address_existing');
        $data['text_address_new'] = $this->language->get('text_address_new');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');

        $this->load->language('common/footer');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['text_mob_pop'] = $this->language->get('text_pop_mob_for_whatsapp');
        $data['entry_whatsapp_telephone'] = $this->language->get('entry_whatsapp_telephone');

        $data['entry_referred'] = $this->language->get('entry_referred');
        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_company'] = $this->language->get('entry_company');
        if ($this->language->get('entry_company_id') != 'entry_company_id')
            $data['entry_company_id'] = $this->language->get('entry_company_id');
        if ($this->language->get('entry_tax_id') != 'entry_tax_id')
            $data['entry_tax_id'] = $this->language->get('entry_tax_id');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_zone'] = $this->language->get('entry_zone');

        $data['button_continue'] = $this->language->get('button_continue');
        //updated by vikas
        if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
            $show_country = 1;
        } else {
            $show_country = 0;
        }
        $data['show_country'] = $show_country;
        if (isset($this->session->data['payment_address']['address_id'])) {
            $data['payment_address_id'] = $this->session->data['payment_address']['address_id'];
        } else {
            $data['payment_address_id'] = $this->customer->getAddressId();
        }


        $data['addresses'] = array();

        $this->load->model('account/address');

        $data['addresses'] = $this->model_account_address->getAddresses();
        //$this->session->data['addresses'] = $data['addresses'];


        if (isset($this->session->data['payment_address']['country_id'])) {
            $data['country_id'] = $this->session->data['payment_address']['country_id'];
        } else {
            $data['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($this->session->data['payment_address']['zone_id'])) {
            $data['zone_id'] = $this->session->data['payment_address']['zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();


        if ($render !== false) {

            $this->response->setOutput($this->load->view('checkout/payment_address', $data));
        }
    }

    public function payment_method($render = true, &$data = array()) {
        $this->load->language('checkout/checkout');

        $this->load->model('account/address');

        //##$payment_address = $this->model_account_address->getAddress((isset($this->request->post['payment_address_id']))?$this->request->post['payment_address_id']:0);
        $payment_address = $this->model_account_address->getAddress((isset($this->request->post['shipping_address_id'])) ? $this->request->post['shipping_address_id'] : 0);

        /* ## if (isset($this->request->post['country_id']) && !empty($this->request->post['zone_id']))
          {
          $this->session->data['guest']['payment']['country_id'] = $payment_address['country_id'] = $this->request->post['country_id'];
          $this->session->data['shipping_country_id'] = $this->session->data['payment_country_id'] = $this->session->data['guest']['payment']['payment_country_id'] = $payment_address['payment_country_id'] = $this->request->post['country_id'];
          $this->session->data['shipping_zone_id'] = $this->session->data['payment_zone_id'] = $this->session->data['guest']['payment']['zone_id'] = $payment_address['zone_id'] = $this->request->post['zone_id'];
          $payment_address['postcode'] = (!empty($this->request->post['shipping_postcode']))?$this->request->post['shipping_postcode']:"";
          }
          elseif ($this->customer->isLogged() && isset($this->session->data['payment_address_id']))
          {
          $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
          } */



        if (isset($this->session->data['shipping_address_id'])) {
            $payment_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        }

        if (isset($this->request->post['shipping_address_id']) && $this->request->post['shipping_address'] == "existing") {
            $payment_address = $this->model_account_address->getAddress($this->request->post['shipping_address_id']);
        } else {

            if (isset($this->request->post['shipping_country_id']))
                $this->session->data['shipping_country_id'] = $this->session->data['payment_country_id'] = $this->session->data['guest']['payment']['country_id'] = $payment_address['country_id'] = $this->request->post['shipping_country_id'];
            if (isset($this->request->post['shipping_zone_id']))
                $this->session->data['shipping_zone_id'] = $this->session->data['payment_zone_id'] = $this->session->data['guest']['payment']['zone_id'] = $payment_address['zone_id'] = $this->request->post['shipping_zone_id'];
            if (isset($this->request->post['shipping_postcode']))
                $this->session->data['shipping_postcode'] = $this->session->data['payment_postcode'] = $this->session->data['guest']['payment']['postcode'] = $payment_address['postcode'] = $this->request->post['shipping_postcode'];
        }

        /* elseif (isset($this->session->data['guest']['payment']))
          {
          $payment_address = $this->session->data['guest']['payment'];
          } */

        /* ## $this->session->data['payment_address'] = $payment_address;

          if (!isset($this->session->data['payment_zone_id '])) $this->session->data['payment_zone_id '] = $payment_address['zone_id'];
          $this->tax->setPaymentAddress($payment_address['country_id'], $payment_address['zone_id']);
         */

        //##$this->session->data['payment_address'] = $payment_address;
        $this->session->data['shipping_address'] = $payment_address;

        if (!isset($this->session->data['shipping_zone_id ']))
            $this->session->data['shipping_zone_id '] = $payment_address['zone_id'];

        // Totals
        $totals = array();
        $taxes = 0;
        $total = 0;


        // Because __call can not keep var references so we put them into an array.
        $order_data['totals'] = array(
            'totals' => &$totals,
            'taxes' => &$taxes,
            'total' => &$total
        );

        // Get Total From system/lib/TotalFactory
        // By Sudhanshu Jain
        $total_factory = new TotalFactory($this, true);
        $order_data['totals'] = $total_factory->getTotal(true);
        $order_data['total'] = $total_factory->total;
        $order_data['taxes'] = $total_factory->taxes;
//                echo $order_data['total'];die;
        $cst = $total_factory->cst;

        // Payment Methods
        $method_data = array();


        $this->load->model('extension/extension');


        // for checking COD serviceability for Gati and Fedex
        $checkCod = false;
        if ($payment_address['zone_id'] == "1479" || $payment_address['zone_id'] == "1505" || $payment_address['zone_id'] == "1490") {
            // For Bihar[1479], UP[1505], Kerala[1490] state, COD serviceability is not available (restricted for now).
            $checkCod = false;
        } else {
            if (!empty($payment_address['postcode'])) {
                $checkCod = $this->model_extension_extension->checkCODServiceability($payment_address['postcode']);
            }
        }

        $data['CODServiceability'] = $checkCod;

        $results = $this->model_extension_extension->getExtensions('payment', $this->config->get('config_store_id'), $checkCod);

        $cart_has_recurring = (method_exists($this->cart, 'hasRecurringProducts') && $this->cart->hasRecurringProducts());

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('payment/' . $result['code']);
                if ($this->customer->isLogged()) {

                    if ((isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1 && $result['code'] == 'cod')) {
                        continue;
                    }

                    $method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['shipping_address'], $total);
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
                } else {
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
        $data['text_instruction'] = $this->language->get('text_instruction');
        $data['text_description'] = $this->language->get('text_description');
        $data['text_payment'] = $this->language->get('text_payment');
        $data['bank_transfer'] = nl2br($this->config->get('bank_transfer_bank' . $this->config->get('config_language_id')));

        $bank_transfer = array();
        $bank_transfer['text_instruction'] = $data['text_instruction'];
        $bank_transfer['text_description'] = $data['text_description'];
        $bank_transfer['text_payment'] = $data['text_payment'];
        $bank_transfer['bank_transfer'] = $data['bank_transfer'];

        $payment_data = array(
            "payment_methods" => $data['payment_methods'],
            "bank_transfer_info" => $bank_transfer,
            "upi_id" => $this->config->get('upi_id')
        );
        $data['payment_data'] = json_encode($payment_data);

        $data['text_payment_method'] = $this->language->get('text_payment_method');
        $data['text_comments'] = $this->language->get('text_comments');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['text_discount'] = $this->language->get('text_discount');


        if ($this->config->get('free_total')) {
            $this->load->language('checkout/shipping');
            $data['free_shipping'] = sprintf($this->language->get('free_shipping'), $this->config->get('free_total'));
        }

        $data['store_id'] = $this->config->get('config_store_id');

        if (isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1) {
            $data['show_offers'] = 0;
        } else {
            if ($this->config->get('config_store_id') != INTERNATIONAL_STORE_ID) {
                $data['show_offers'] = 1;
            }
        }

        if ($render !== false) {
            //$this->response->setOutput($this->load->view('checkout/easy_payment_method', $data));
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/easy_payment_method.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/easy_payment_method.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/checkout/easy_payment_method.tpl', $data));
            }
        }
    }

    public function checkout($render = true, &$data = array()) {
        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            //if ($data['opencart2']) $this->response->redirect($this->url->link('checkout/cart')); else $this->redirect($this->url->link('checkout/cart'));
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
            }
        }

        $this->load->language('checkout/checkout');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', '', 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_cart'),
            'href' => $this->url->link('checkout/cart', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('checkout/one_page_checkout', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_checkout_account'] = sprintf($this->language->get('text_checkout_account'), 1);
        $data['text_checkout_payment_address'] = sprintf($this->language->get('text_checkout_payment_address'), 2);
        $data['text_checkout_shipping_address'] = sprintf($this->language->get('text_checkout_shipping_address'), 3);
        $data['text_checkout_shipping_method'] = sprintf($this->language->get('text_checkout_shipping_method'), 4);

        if ($this->cart->hasShipping()) {
            $data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
            $data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
        } else {
            $data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
            $data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);
        }
        $data['text_modify'] = $this->language->get('text_modify');

        $data['logged'] = $this->customer->isLogged();
        $data['shipping_required'] = $this->cart->hasShipping();
        $data['back_button'] = $this->url->link('checkout/cart', '', 'SSL');
        $data['button_back'] = $this->language->get('button_back');

        //$this->response->setOutput($this->load->view('checkout/one_page_checkout', $data));
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/one_page_checkout.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/one_page_checkout.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/checkout/one_page_checkout.tpl', $data));
        }

        if ($render !== false) {

            if (isset($this->request->get['quickconfirm'])) {
                $data['quickconfirm'] = $this->request->get['quickconfirm'];
            }
        }
    }

    public function guest($render = false, &$data = array()) {
        $this->load->language('checkout/checkout');

        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_your_details'] = $this->language->get('text_your_details');
        $data['text_your_account'] = $this->language->get('text_your_account');
        $data['text_your_address'] = $this->language->get('text_your_address');

        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        //$data['entry_email'] = $this->language->get('entry_email');
        $data['entry_email_address'] = $this->language->get('entry_email_address');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['entry_company'] = $this->language->get('entry_company');
        if ($this->language->get('entry_company_id') != 'entry_company_id')
            $data['entry_company_id'] = $this->language->get('entry_company_id');
        if ($this->language->get('entry_tax_id') != 'entry_tax_id')
            $data['entry_tax_id'] = $this->language->get('entry_tax_id');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_zone'] = $this->language->get('entry_zone');
        $data['entry_shipping'] = $this->language->get('entry_shipping');

        $data['button_continue'] = $this->language->get('button_continue');

        if (isset($this->session->data['guest']['firstname'])) {
            $data['firstname'] = $this->session->data['guest']['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->session->data['guest']['lastname'])) {
            $data['lastname'] = $this->session->data['guest']['lastname'];
        } else {
            $data['lastname'] = '';
        }

        if (isset($this->session->data['guest']['email'])) {
            $data['email'] = $this->session->data['guest']['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->session->data['guest']['telephone'])) {
            $data['telephone'] = $this->session->data['guest']['telephone'];
        } else {
            $data['telephone'] = '';
        }

        if (isset($this->session->data['guest']['payment']['company'])) {
            $data['company'] = $this->session->data['guest']['payment']['company'];
        } else {
            $data['company'] = '';
        }

        // Company ID
        if (isset($this->session->data['guest']['payment']['company_id'])) {
            $data['company_id'] = $this->session->data['guest']['payment']['company_id'];
        } else {
            $data['company_id'] = '';
        }

        // Tax ID
        if (isset($this->session->data['guest']['payment']['tax_id'])) {
            $data['tax_id'] = $this->session->data['guest']['payment']['tax_id'];
        } else {
            $data['tax_id'] = '';
        }

        if (isset($this->session->data['guest']['payment']['address_1'])) {
            $data['address_1'] = $this->session->data['guest']['payment']['address_1'];
        } else {
            $data['address_1'] = '';
        }

        if (isset($this->session->data['guest']['payment']['address_2'])) {
            $data['address_2'] = $this->session->data['guest']['payment']['address_2'];
        } else {
            $data['address_2'] = '';
        }

        if (isset($this->session->data['guest']['payment']['postcode'])) {
            $data['postcode'] = $this->session->data['guest']['payment']['postcode'];
        } elseif (isset($this->session->data['shipping_postcode'])) {
            $data['postcode'] = $this->session->data['shipping_postcode'];
        } else {
            $data['postcode'] = '';
        }

        if (isset($this->session->data['guest']['payment']['city'])) {
            $data['city'] = $this->session->data['guest']['payment']['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->session->data['guest']['payment']['country_id']) && $this->session->data['guest']['payment']['country_id']) {
            $data['country_id'] = $this->session->data['guest']['payment']['country_id'];
        } elseif (isset($this->session->data['shipping_country_id']) && $this->session->data['shipping_country_id']) {
            $data['country_id'] = $this->session->data['shipping_country_id'];
        } else {
            $data['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($this->session->data['guest']['payment']['zone_id'])) {
            $data['zone_id'] = $this->session->data['guest']['payment']['zone_id'];
        } elseif (isset($this->session->data['shipping_zone_id'])) {
            $data['zone_id'] = $this->session->data['shipping_zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        $data['shipping_required'] = $this->cart->hasShipping();

        if (isset($this->session->data['guest']['shipping_address'])) {
            $data['shipping_address'] = $this->session->data['guest']['shipping_address'];
        } else {
            $data['shipping_address'] = true;
        }

        if ($render !== false) {

            $this->response->setOutput($this->load->view('checkout/guest', $data));
        }
    }

    public function login($render = false, &$data = array()) {
        $this->load->language('checkout/checkout');

        $data['text_new_customer'] = $this->language->get('text_new_customer');
        $data['text_returning_customer'] = $this->language->get('text_returning_customer');
        $data['text_checkout'] = $this->language->get('text_checkout');
        $data['text_register'] = $this->language->get('text_register');
        $data['text_guest'] = $this->language->get('text_guest');
        //$data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
        $data['text_click_here_to_login'] = $this->language->get('text_click_here_to_login');
        $data['text_register_account'] = $this->language->get('text_register_account');
        $data['text_forgotten'] = $this->language->get('text_forgotten');

        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_confirm'] = $this->language->get('entry_confirm');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_login'] = $this->language->get('button_login');

        $data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());

        if (isset($this->session->data['account'])) {
            $data['account'] = $this->session->data['account'];
        } else {
            $data['account'] = 'register';
        }

        $data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
        $data['heading_text_forget_password'] = $this->language->get('heading_text_forget_password');
        if ($render !== false) {

            $this->response->setOutput($this->load->view('checkout/login', $data));
        }
    }

    public function cart($render = true, &$data = array()) {
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



        $data['column_image'] = $this->language->get('column_image');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');


        $data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);



        $data['text_cart'] = $this->language->get('text_cart');
        $data['text_total_qty'] = $this->language->get('text_total_qty');
        $data['pcs'] = $this->language->get('pcs');
        $data['text_product_code'] = $this->language->get('text_product_code');
        $data['text_previously_ordered'] = $this->language->get('text_previously_ordered');
        $data['pieces'] = $this->language->get('pieces');
        $data['column_per_piece'] = $this->language->get('column_per_piece');
        $data['column_per_set'] = $this->language->get('column_per_set');
        $data['column_tax'] = $this->language->get('column_tax');


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } elseif (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
            $data['error_warning'] = $this->language->get('error_stock');
        } else {
            $data['error_warning'] = '';
        }

        if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
            $data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '' . 'SSL'));
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
        if (isset($this->session->data['shipping_methods']['weight']['quote']['weight_5'])) {
            if (isset($this->session->data['shipping_method']) && $this->session->data['shipping_method']['code'] == 'free.free') {
                $this->session->data['shipping_method'] = $this->session->data['shipping_methods']['weight']['quote']['weight_5'];
            }
        }


        $products = $this->cart->getProducts();
        $products_info = array();
        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
            }

//                if ($product['image']) {
//                  $image = $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_cart_width'), $this->config->get($this->config->get('config_theme') . '_image_cart_height'));
//                } else {
//                  $image = '';
//                }
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
                    if (isset($option['option_value'])) {
                        $value = $option['option_value'];
                    } else if (isset($option['value'])) {
                        $value = $option['value'];
                    } else {
                        $value = '';
                    }
                } else {
                    $filename = $this->encryption->decrypt(isset($option['option_value']) ? $option['option_value'] : isset($option['value']) ? $option['value'] : '');

                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                }

                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                );
            }

//              // Display prices
//        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
//          $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
//        } else {
//          $price = false;
//        }
//
//        // Display prices
//        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
//          $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']);
//        } else {
//          $total = false;
//        }

            $piece_in_set = $product['piece_in_set'];

            // Display per piece prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price_per_piece = $this->currency->format($this->tax->calculate($product['price'] / $piece_in_set, $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
            } else {
                $price_per_piece = false;
            }

            // Display per set prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
            } else {
                $price = false;
            }


            // Display total prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $total = $this->currency->format($product['total']);
            } else {
                $total = false;
            }

            $profile_description = '';

            if (isset($product['recurring']) && $product['recurring']) {
                $frequencies = array(
                    'day' => $this->language->get('text_day'),
                    'week' => $this->language->get('text_week'),
                    'semi_month' => $this->language->get('text_semi_month'),
                    'month' => $this->language->get('text_month'),
                    'year' => $this->language->get('text_year'),
                );

                if (isset($product['recurring_trial']) && $product['recurring_trial']) {
                    $recurring_price = $this->currency->format($this->tax->calculate($product['recurring_trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')));
                    $profile_description = sprintf($this->language->get('text_trial_description'), $recurring_price, $product['recurring_trial_cycle'], $frequencies[$product['recurring_trial_frequency']], $product['recurring_trial_duration']) . ' ';
                }

                $recurring_price = $this->currency->format($this->tax->calculate($product['recurring_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')));

                if ($product['recurring_duration']) {
                    $profile_description .= sprintf($this->language->get('text_payment_description'), $recurring_price, $product['recurring_cycle'], $frequencies[$product['recurring_frequency']], $product['recurring_duration']);
                } else {
                    $profile_description .= sprintf($this->language->get('text_payment_until_canceled_description'), $recurring_price, $product['recurring_cycle'], $frequencies[$product['recurring_frequency']], $product['recurring_duration']);
                }
            }

            $total_sets += (int) ($product['quantity']);
            $total_pieces += (int) ($product['piece_in_set'] * $product['quantity']);

            if ($this->customer->isLogged())
                $previously_ordered = $this->model_catalog_product->checkPreviouslyOrdered($this->customer->getId(), $product['product_id']);
            else
                $previously_ordered = false;
            $temp_product = array(
                'key' => $product['key'],
                'product_id' => $product['product_id'],
                'thumb' => $image,
                'name' => $product['name'],
                'model' => $product['model'],
                'set_description' => $product['set_description'],
                'option' => $option_data,
                'recurring' => $profile_description,
                'quantity' => $product['quantity'],
                'stock_quantity' => $product['stock_quantity'],
                'stock' => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
                'price_per_piece' => $price_per_piece,
                'piece_in_set' => $product['piece_in_set'] * $product['quantity'],
                'price' => $price,
                'total' => $total,
                'tax' => $this->currency->format($this->tax->getTax($product['total'], $product['hsn_code'], '', '', $product['mrp'])),
                'weight' => $product['weight'],
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL'),
                'sellers' => array($product['seller_id'] => array('price' => $product['selling_price'], 'total' => $total)),
                'previously_ordered' => $previously_ordered,
                'is_single' => $product['is_single'],
                'img_width' => $img_width,
                'img_height' => $img_height,
                'comment' => $product['comment']
            );
            $data['products'][] = $temp_product;
            $products_info[$product['pickup_city']][] = $temp_product;
            $data['clear_cart'][$product['pickup_city']][] = $product['key'];
        }

        $data['total_pieces'] = $total_pieces;
        $data['total_sets'] = $total_sets;

        $data['clear_cart'] = json_encode($data['clear_cart']);
        $data["product_json"] = json_encode($products_info);

        $data['products_recurring'] = array();

        // Gift Voucher
        $data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
                $data['vouchers'][] = array(
                    'key' => $key,
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount']),
                    'remove' => $this->url->link('checkout/cart', 'remove=' . $key, 'SSL')
                );
            }
        }

        if (isset($this->request->post['next'])) {
            $data['next'] = $this->request->post['next'];
        } else {
            $data['next'] = '';
        }

        $data['coupon_status'] = $this->config->get('coupon_status');

        if (isset($this->request->post['coupon'])) {
            $data['coupon'] = $this->request->post['coupon'];
        } elseif (isset($this->session->data['coupon'])) {
            $data['coupon'] = $this->session->data['coupon'];
        } else {
            $data['coupon'] = '';
        }

        $data['voucher_status'] = $this->config->get('voucher_status');

        if (isset($this->request->post['voucher'])) {
            $data['voucher'] = $this->request->post['voucher'];
        } elseif (isset($this->session->data['voucher'])) {
            $data['voucher'] = $this->session->data['voucher'];
        } else {
            $data['voucher'] = '';
        }

        $data['reward_status'] = ($points && $points_total && $this->config->get('reward_status'));

        if (isset($this->request->post['reward'])) {
            $data['reward'] = $this->request->post['reward'];
        } elseif (isset($this->session->data['reward'])) {
            $data['reward'] = $this->session->data['reward'];
        } else {
            $data['reward'] = '';
        }

        $data['shipping_status'] = $this->config->get('shipping_status') && $this->config->get('shipping_estimator') && $this->cart->hasShipping();

        if (isset($this->request->post['country_id']) && $this->request->post['country_id']) {
            $data['country_id'] = $this->request->post['country_id'];
        } elseif (isset($this->session->data['shipping_country_id']) && $this->session->data['shipping_country_id']) {
            $data['country_id'] = $this->session->data['shipping_country_id'];
        } else {
            $data['country_id'] = $this->config->get('config_country_id');
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        if (isset($this->request->post['zone_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
        } elseif (isset($this->session->data['shipping_zone_id'])) {
            $data['zone_id'] = $this->session->data['shipping_zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        if (isset($this->request->post['postcode'])) {
            $data['postcode'] = $this->request->post['postcode'];
        } elseif (isset($this->session->data['shipping_postcode'])) {
            $data['postcode'] = $this->session->data['shipping_postcode'];
        } else {
            $data['postcode'] = '';
        }

        //if (isset($this->request->post['shipping_method'])) {
//        $data['shipping_method'] = $this->request->post['shipping_method'];
        //} else


        if (isset($this->session->data['shipping_method'])) {
            $data['shipping_method'] = $this->session->data['shipping_method']['code'];
        } else {
            $data['shipping_method'] = '';
        }

        // Totals

        $order_data['totals'] = array();
        $total = 0;
        $taxes = 0;
        $total_factory = new TotalFactory($this, true);
        $order_data['totals'] = $total_factory->getTotal(true);
        $total = $total_factory->total;
        $taxes = $total_factory->taxes;
        $cst = $total_factory->cst;

        $data['cform_submit'] = 0;

        $data['text_cst'] = $this->language->get('text_cst');
        $data['text_tax_refund'] = $this->language->get('text_tax_refund');

        $data['totals'] = array();
        $tax_in_totals = false;
        $cartTotal = 0.0;
        $cartSubTotal = 0.0;
        $store_credit = 0.0;
        $data['totalJson'] = array();
        foreach ($order_data['totals'] as $total) {
            //echo "<pre>"; print_r($total);die;
            if ($total['code'] == 'sub_total') {
                $total['title'] = $this->language->get('text_subtotal');
                $cartSubTotal = $total['value'];
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
            }

            if (($total['code'] == "shipping" && $total['code'] == "0") || $total['title'] == "Free Shipping") {
                foreach ($order_data['totals'] as $k => $ext) {
                    if ($ext['code'] == "paycharge") {
                        unset($order_data['totals'][$k]);
                        continue;
                    }
                    if ($ext['code'] == "tax") {
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
            $data['totalJson'][$total['code']] = array(
                'code' => $total['code'],
                'title' => $total['title'],
                'value' => $total['value'],
                'show_value' => $total['value'],
                'text' => $this->currency->format($total['value'])
            );
            $data['totals'][] = array(
                'code' => $total['code'],
                'title' => $total['title'],
                'value' => $total['value'],
                'show_value' => $total['value'],
                'text' => $this->currency->format($total['value'])
            );
        }

        $data['totalJson'] = json_encode($data['totalJson']);
        /* foreach ($order_data['totals'] as $total) {
          $data['totals'][] = array(
          'title' => $total['title'],
          'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
          );
          } */

        //$sum_cart_credit = $cartTotal + abs($store_credit);
        $sum_cart_credit = $cartSubTotal;

        $data['error_cart_minimum'] = '';
        $this->session->data['error_cart_minimum'] = false;

        $store_id = $this->config->get('config_store_id');

        $exception_customer_ids = array(52);
        if ($this->customer->isLogged() && in_array($this->customer->getId(), $exception_customer_ids)) {
            //do nothing
        } else {
            // Now store id 2 is international store's id, not singles store's
            // so commenting below singles store cart limit check
            /*if ($store_id == 2) { // Singles store
                $single_store_order_limit = (float) $this->config->get('config_limit');
                if ($cartTotal < $single_store_order_limit) {
                    $data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'), $this->currency->format(ceil($single_store_order_limit)));
                    $this->session->data['error_cart_minimum'] = true;
                }
            } else */
            if ($sum_cart_credit == 0 || $sum_cart_credit < (float) ($this->config->get('config_cart_limit'))) {
                $data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'), $this->currency->format((float) ($this->config->get('config_cart_limit')), $this->currency->getCode(), 1));
                $this->session->data['error_cart_minimum'] = true;
            }
        }


        $this->load->model('extension/extension');

//      $data['modules'] = array();
//
//      $files = glob(DIR_APPLICATION . 'controller/total/*.php');
//
//      if ($files) {
//        foreach ($files as $file) {
//          $result = $this->load->controller('total/' . basename($file, '.php'));
//
//          if ($result) {
//            $data['modules'][] = $result;
//          }
//        }
//      }

        $data['continue'] = $this->url->link('common/home', '', 'SSL');

        $data['checkout'] = $this->url->link('checkout/one_page_checkout', '', 'SSL');


        $this->load->model('extension/extension');


        $data['checkout_buttons'] = array();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/easy_cart.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/easy_cart.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/checkout/easy_cart.tpl', $data));
        }
    }

    public function confirm($render = true, &$data = array()) {
        if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
          $this->currency->setCurrencyTemporary('INR');
          // apply the internation price factor on products price
          $this->cart->setApplyInternationPriceFactor(true);
        }
      
        //Setting as true for using hidden selling price if exists
        $this->cart->useHiddenSellingPrice(true);
        $redirect = '';

        $data['payment'] = '';
        $data['products'] = '';

        $redirect = '';

        $this->load->language('checkout/checkout');
        $data['text_cart'] = $this->language->get('text_cart');


        if ($this->cart->hasShipping()) {
            // Validate if shipping address has been set.
            if (!isset($this->session->data['shipping_address'])) {
                $redirect = $this->url->link('checkout/one_page_checkout', '', 'SSL');
            }

            // Validate if shipping method has been set.
            if (!isset($this->session->data['shipping_method'])) {
                $redirect = $this->url->link('checkout/one_page_checkout', '', 'SSL');
            }
        } else {
            unset($this->session->data['shipping_address']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
        }

        // Validate if payment address has been set.
        if (!isset($this->session->data['payment_address'])) {
            $redirect = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        // Validate if payment method has been set.
        if (!isset($this->session->data['payment_method'])) {
            $redirect = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $redirect = $this->url->link('checkout/cart', '', 'SSL');
        }
        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $redirect = $this->url->link('checkout/cart', '', 'SSL');

                break;
            }
        }
        $order_data = array();

        $order_data['totals'] = array();
        $taxes = 0;
        $total = 0;

        // this code written in confirm.php by madhur sir (06-06-2016) start
        // CST Calculation
        // $CST_CLASS_ID = 12;
        // $cst = $this->cart->getCST($CST_CLASS_ID); // CST tax class id is 12
        // end

        $total_factory = new TotalFactory($this, true);
        $order_data['totals'] = $total_factory->getTotal();
        $total = $total_factory->total;
        $taxes = $total_factory->taxes;
        $cst = $total_factory->cst;
        //Get subtotal value to compare with config cart limit
        //Then get paycharge key from order total so we can unset it from total if needed
        $sub_total_value = 0;
        $paycharge_key = '';
        foreach ($order_data['totals'] as $total_key => $total_value) {

            if (isset($total_value['code']) && $total_value['code'] == 'sub_total') {
                $sub_total_value = $total_value['value'];
            } else if (isset($total_value['code']) && $total_value['code'] == 'paycharge') {
                $paycharge_key = $total_key;
            }
        }

        //if customer is logged in and dropshipper and does not meet the cart limit then he would not be applicable for prepaid discount
        if ($this->customer->isLogged() && $this->customer->is_dropshipper == 1 && $sub_total_value <= $this->config->get('config_dropshipper_cart_limit')) {
            unset($order_data['totals'][$paycharge_key]);
        }

    $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
    $order_data['store_id'] = $this->config->get('config_store_id');
    $order_data['store_name'] = $this->config->get('config_name');

    if ($order_data['store_id']) {
      $order_data['store_url'] = $this->config->get('config_url');
    } else {
      $order_data['store_url'] = HTTP_SERVER;
    }

    if (isset($_POST) && !empty($_POST))
    {
      if ($this->customer->isLogged()) {
        $this->load->model('account/customer');

        $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

        $order_data['customer_id'] = $this->customer->getId();
        $order_data['firstname'] = $customer_info['firstname'];
        $order_data['lastname'] = $customer_info['lastname'];
        $order_data['email'] = $customer_info['email'];
        $order_data['telephone'] = $customer_info['telephone'];
      } elseif (isset($this->session->data['guest'])) {
        $order_data['customer_id'] = 0;
        $order_data['firstname'] = isset($this->session->data['guest']['firstname'])?$this->session->data['guest']['firstname']:'';
        $order_data['lastname'] = isset($this->session->data['guest']['lastname'])?$this->session->data['guest']['lastname']:'';
        $order_data['email'] = isset($this->session->data['guest']['email'])?$this->session->data['guest']['email']:'';
        $order_data['telephone'] = isset($this->session->data['guest']['telephone'])?$this->session->data['guest']['telephone']:'';
      }


      if ((isset($payment_address) && is_array($payment_address)) || isset($this->session->data['payment_address']))
      {

        if (isset($this->session->data['payment_address'])) $payment_address = $this->session->data['payment_address'];
        $order_data['payment_firstname'] = isset($payment_address['firstname']) ? $payment_address['firstname'] : '';
        $order_data['payment_lastname'] = isset($payment_address['lastname']) ? $payment_address['lastname'] : '';
        $order_data['payment_company'] = isset($payment_address['company']) ? $payment_address['company'] : '';
        $order_data['payment_company_id'] = isset($payment_address['company_id'])? $payment_address['company_id']:'';
        $order_data['payment_tax_id'] = isset($payment_address['tax_id'])? $payment_address['tax_id']:'';
        $order_data['payment_address_1'] = isset($payment_address['address_1']) ? $payment_address['address_1'] : '';
        $order_data['payment_address_2'] = isset($payment_address['address_2']) ? $payment_address['address_2'] : '';
        $order_data['payment_city'] = isset($payment_address['city']) ? $payment_address['city'] : '';
        $order_data['payment_postcode'] = isset($payment_address['postcode']) ? $payment_address['postcode'] : '';
        $order_data['payment_zone'] = isset($payment_address['zone']) ? $payment_address['zone'] : '';
        $order_data['payment_zone_id'] = isset($payment_address['zone_id']) ? $payment_address['zone_id'] : '';
        $order_data['payment_country'] = isset($payment_address['country']) ? $payment_address['country'] : '';
        $order_data['payment_country_id'] = isset($payment_address['country_id']) ? $payment_address['country_id'] : '';
        $order_data['payment_address_format'] = isset($payment_address['address_format']) ? $payment_address['address_format'] : '';
      }


      if (isset($this->session->data['payment_method']['title'])) {
        $order_data['payment_method'] = $this->session->data['payment_method']['title'];
      } else {
        $order_data['payment_method'] = '';
      }

      if (isset($this->session->data['payment_method']['code'])) {
        $order_data['payment_code'] = $this->session->data['payment_method']['code'];
      } else {
        $order_data['payment_code'] = '';
      }

            if ($this->cart->hasShipping()) {

                if (!$this->customer->isLogged()) {
                    if (!isset($this->request->post['shipping_address'])) {

                        $this->session->data['shipping_address']['firstname'] = $this->request->post['firstname'];
                        $this->session->data['shipping_address']['lastname'] = $this->request->post['lastname'];
                        $this->session->data['shipping_address']['company'] = $this->request->post['company'];
                        $this->session->data['shipping_address']['address_1'] = $this->request->post['address_1'];
                        $this->session->data['shipping_address']['address_2'] = $this->request->post['address_2'];
                        $this->session->data['shipping_address']['address_telephone'] = $this->request->post['address_telephone'];
                        $this->session->data['shipping_address']['postcode'] = $this->request->post['postcode'];
                        $this->session->data['shipping_address']['city'] = $this->request->post['city'];
                        $this->session->data['shipping_address']['country_id'] = $this->request->post['country_id'];
                        $this->session->data['shipping_address']['zone_id'] = $this->request->post['zone_id'];
                        $this->load->model('localisation/country');
                        $this->load->model('localisation/zone');
                        $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
                        $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);
                        if ($country_info) {
                            $this->session->data['shipping_address']['country'] = $country_info['name'];
                            $this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
                            $this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
                            $this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
                        } else {
                            $this->session->data['shipping_address']['country'] = '';
                            $this->session->data['shipping_address']['iso_code_2'] = '';
                            $this->session->data['shipping_address']['iso_code_3'] = '';
                            $this->session->data['shipping_address']['address_format'] = '';
                        }

                        if ($zone_info) {
                            $this->session->data['shipping_address']['zone'] = $zone_info['name'];
                            $this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
                        } else {
                            $this->session->data['shipping_address']['zone'] = '';
                            $this->session->data['shipping_address']['zone_code'] = '';
                        }



                        if (isset($this->session->data['shipping_address'])) {
                            $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
                            $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
                            $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                            $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];

                            $order_data['shipping_address_2'] = (isset($this->session->data['shipping_address']['address_2'])) ? $this->session->data['shipping_address']['address_2'] : '';

                            $order_data['address_telephone'] = (isset($this->session->data['shipping_address']['address_telephone'])) ? $this->session->data['shipping_address']['address_telephone'] : '';

                            $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
                            $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
                            $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
                            $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
                            $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                            $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                            $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
                            $order_data['shipping_method'] = $this->session->data['payment_method']['title'];
                        }
                    } else {
                        $this->load->model('localisation/country');
                        $this->load->model('localisation/zone');
                        $country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);
                        $zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping_zone_id']);
                        if ($country_info) {
                            $this->session->data['shipping_address']['country'] = $country_info['name'];
                            $this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
                            $this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
                            $this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
                        } else {
                            $this->session->data['shipping_address']['country'] = '';
                            $this->session->data['shipping_address']['iso_code_2'] = '';
                            $this->session->data['shipping_address']['iso_code_3'] = '';
                            $this->session->data['shipping_address']['address_format'] = '';
                        }

                        if ($zone_info) {
                            $this->session->data['shipping_address']['zone'] = $zone_info['name'];
                            $this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
                        } else {
                            $this->session->data['shipping_address']['zone'] = '';
                            $this->session->data['shipping_address']['zone_code'] = '';
                        }
                        $order_data['shipping_firstname'] = $this->request->post['shipping_firstname'];
                        $order_data['shipping_lastname'] = $this->request->post['shipping_lastname'];
                        $order_data['shipping_company'] = $this->request->post['shipping_company'];
                        $order_data['shipping_address_1'] = $this->request->post['shipping_address_1'];
                        $order_data['shipping_address_2'] = $this->request->post['shipping_address_2'];
                        $order_data['shipping_city'] = $this->request->post['shipping_city'];
                        $order_data['address_telephone'] = $this->request->post['address_telephone'];
                        $order_data['shipping_postcode'] = $this->request->post['shipping_postcode'];
                        $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone']; //$this->request->post['shipping_zone'];
                        $order_data['shipping_zone_id'] = $this->request->post['shipping_zone_id'];
                        $order_data['shipping_country'] = $this->session->data['shipping_address']['country']; //$this->request->post['shipping_country'];
                        $order_data['shipping_country_id'] = $this->request->post['shipping_country_id'];
                        $order_data['shipping_address_format'] = ''; //$this->request->post['shipping_address_format'];
                        $order_data['shipping_method'] = $this->request->post['_shipping_method'];
                        $order_data['shipping_code'] = $this->request->post['shipping_method'];
                    }
                } else {

                    if (isset($this->request->post['shipping_firstname'])) {
                        if (!$this->customer->isLogged()) {
                            $this->load->model('localisation/country');
                            $this->load->model('localisation/zone');
                            $country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);
                            $zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping_zone_id']);
                            if ($country_info) {
                                $this->session->data['shipping_address']['country'] = $country_info['name'];
                                $this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
                                $this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
                                $this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
                            } else {
                                $this->session->data['shipping_address']['country'] = '';
                                $this->session->data['shipping_address']['iso_code_2'] = '';
                                $this->session->data['shipping_address']['iso_code_3'] = '';
                                $this->session->data['shipping_address']['address_format'] = '';
                            }

                            if ($zone_info) {
                                $this->session->data['shipping_address']['zone'] = $zone_info['name'];
                                $this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
                            } else {
                                $this->session->data['shipping_address']['zone'] = '';
                                $this->session->data['shipping_address']['zone_code'] = '';
                            }
                            $order_data['shipping_firstname'] = $this->request->post['shipping_firstname'];
                            $order_data['shipping_lastname'] = $this->request->post['shipping_lastname'];
                            $order_data['shipping_company'] = $this->request->post['shipping_company'];
                            $order_data['shipping_address_1'] = $this->request->post['shipping_address_1'];
                            $order_data['shipping_address_2'] = $this->request->post['shipping_address_2'];
                            $order_data['shipping_city'] = $this->request->post['shipping_city'];
                            $order_data['address_telephone'] = $this->request->post['address_telephone'];
                            $order_data['shipping_postcode'] = $this->request->post['shipping_postcode'];
                            $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone']; //$this->request->post['shipping_zone'];
                            $order_data['shipping_zone_id'] = $this->request->post['shipping_zone_id'];
                            $order_data['shipping_country'] = $this->session->data['shipping_address']['country']; //$this->request->post['shipping_country'];
                            $order_data['shipping_country_id'] = $this->request->post['shipping_country_id'];
                            $order_data['shipping_address_format'] = ''; //$this->request->post['shipping_address_format'];
                            $order_data['shipping_method'] = $this->request->post['_shipping_method'];
                            $order_data['shipping_code'] = $this->request->post['shipping_method'];
                        } else {


                            if (isset($this->session->data['shipping_address_id'])) {

                                $this->load->model('account/address');

                                $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);

                                $order_data['shipping_firstname'] = $shipping_address['firstname'];
                                $order_data['shipping_lastname'] = $shipping_address['lastname'];
                                $order_data['shipping_company'] = $shipping_address['company'];
                                $order_data['shipping_address_1'] = $shipping_address['address_1'];
                                $order_data['shipping_address_2'] = $shipping_address['address_2'];
                                $order_data['shipping_city'] = $shipping_address['city'];
                                $order_data['shipping_address_telephone'] = $shipping_address['address_telephone'];
                                $order_data['shipping_postcode'] = $shipping_address['postcode'];
                                $order_data['shipping_zone'] = $shipping_address['zone'];
                                $order_data['shipping_zone_id'] = $shipping_address['zone_id'];
                                $order_data['shipping_country'] = $shipping_address['country'];
                                $order_data['shipping_country_id'] = $shipping_address['country_id'];
                                $order_data['shipping_address_format'] = $shipping_address['address_format'];

                                if (isset($this->session->data['shipping_method']['title'])) {
                                    $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                                } else {
                                    $order_data['shipping_method'] = '';
                                }
                            } else {

                                $this->session->data['shipping_address']['firstname'] = $this->request->post['shipping_firstname'];
                                $this->session->data['shipping_address']['lastname'] = $this->request->post['shipping_lastname'];
                                $this->session->data['shipping_address']['company'] = $this->request->post['shipping_company'];
                                $this->session->data['shipping_address']['address_1'] = $this->request->post['shipping_address_1'];
                                $this->session->data['shipping_address']['address_2'] = $this->request->post['shipping_address_2'];
                                $this->session->data['shipping_address']['address_telephone'] = $this->request->post['address_telephone'];
                                $this->session->data['shipping_address']['postcode'] = $this->request->post['shipping_postcode'];
                                $this->session->data['shipping_address']['city'] = $this->request->post['shipping_city'];
                                $this->session->data['shipping_address']['country_id'] = $this->request->post['shipping_country_id'];
                                $this->session->data['shipping_address']['zone_id'] = $this->request->post['shipping_zone_id'];
                                $this->load->model('localisation/country');
                                $this->load->model('localisation/zone');

                                $country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);
                                $zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping_zone_id']);
                                if ($country_info) {
                                    $this->session->data['shipping_address']['country'] = $country_info['name'];
                                    $this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
                                    $this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
                                    $this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
                                } else {
                                    $this->session->data['shipping_address']['country'] = '';
                                    $this->session->data['shipping_address']['iso_code_2'] = '';
                                    $this->session->data['shipping_address']['iso_code_3'] = '';
                                    $this->session->data['shipping_address']['address_format'] = '';
                                }

                                if ($zone_info) {
                                    $this->session->data['shipping_address']['zone'] = $zone_info['name'];
                                    $this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
                                } else {
                                    $this->session->data['shipping_address']['zone'] = '';
                                    $this->session->data['shipping_address']['zone_code'] = '';
                                }


                                if (isset($this->session->data['shipping_address'])) {
                                    $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
                                    $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
                                    $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                                    $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
                                    $order_data['shipping_address_2'] = (isset($this->session->data['shipping_address']['address_2'])) ? $this->session->data['shipping_address']['address_2'] : '';
                                    $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
                                    $order_data['address_telephone'] = $this->session->data['shipping_address']['address_telephone'];
                                    $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
                                    $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
                                    $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
                                    $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                                    $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                                    $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
                                    $order_data['shipping_method'] = $this->session->data['payment_method']['title'];
                                }
                            }
                        }
                    } else if (isset($this->session->data['shipping_address_id'])) {

                        $this->load->model('account/address');

                        $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);

                        $order_data['shipping_firstname'] = $shipping_address['firstname'];
                        $order_data['shipping_lastname'] = $shipping_address['lastname'];
                        $order_data['shipping_company'] = $shipping_address['company'];
                        $order_data['shipping_address_1'] = $shipping_address['address_1'];
                        $order_data['shipping_address_2'] = $shipping_address['address_2'];
                        $order_data['shipping_city'] = $shipping_address['city'];
                        $order_data['shipping_postcode'] = $shipping_address['postcode'];
                        $order_data['address_telephone'] = $shipping_address['address_telephone'];
                        $order_data['shipping_zone'] = $shipping_address['zone'];
                        $order_data['shipping_zone_id'] = $shipping_address['zone_id'];
                        $order_data['shipping_country'] = $shipping_address['country'];
                        $order_data['shipping_country_id'] = $shipping_address['country_id'];
                        $order_data['shipping_address_format'] = $shipping_address['address_format'];

                        if (isset($this->session->data['shipping_method']['title'])) {
                            $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                        } else {
                            $order_data['shipping_method'] = '';
                        }
                    } else {
                        $order_data['shipping_firstname'] = '';
                        $order_data['shipping_lastname'] = '';
                        $order_data['shipping_company'] = '';
                        $order_data['shipping_address_1'] = '';
                        $order_data['shipping_address_2'] = '';
                        $order_data['shipping_city'] = '';
                        $order_data['address_telephone'] = '';
                        $order_data['shipping_postcode'] = '';
                        $order_data['shipping_zone'] = '';
                        $order_data['shipping_zone_id'] = '';
                        $order_data['shipping_country'] = '';
                        $order_data['shipping_country_id'] = '';
                        $order_data['shipping_address_format'] = '';
                        $order_data['shipping_method'] = '';
                        $order_data['shipping_code'] = '';
                    }

                    if (isset($this->session->data['shipping_method']['title'])) {
                        $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                    } else {
                        $order_data['shipping_method'] = '';
                    }

                    if (isset($this->session->data['shipping_method']['code'])) {
                        $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
                    } else {
                        $order_data['shipping_code'] = '';
                    }
                }
            } else {
                $order_data['shipping_firstname'] = '';
                $order_data['shipping_lastname'] = '';
                $order_data['shipping_company'] = '';
                $order_data['shipping_address_1'] = '';
                $order_data['shipping_address_2'] = '';
                $order_data['shipping_city'] = '';
                $order_data['address_telephone'] = '';
                $order_data['shipping_postcode'] = '';
                $order_data['shipping_zone'] = '';
                $order_data['shipping_zone_id'] = '';
                $order_data['shipping_country'] = '';
                $order_data['shipping_country_id'] = '';
                $order_data['shipping_address_format'] = '';
                $order_data['shipping_method'] = '';
                $order_data['shipping_code'] = '';
            }

            $total_pieces_order = 0;
            $total_sets = 0;

            $order_data['products'] = array();

            $order_data['total_weight'] = 0;
            $order_data['weight_class_id'] = $this->config->get('config_weight_class_id');

            $this->load->model('catalog/product');
            $apply_store_code = false;
            $coupon_data = $this->cart->getCoupon();
            if (!empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code'])) {
                $order_data['coupon'] = $coupon_data['wsb_store_voucher'];
            } else if (!empty($coupon_data)) {
                $order_data['coupon'] = array_values($coupon_data)[0];
            } else {
                $order_data['coupon'] = '';
            }

            foreach ($this->cart->getProducts() as $key => $product) {
                $option_data = array();
                foreach ($product['option'] as $option) {
                    $option_data[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'product_option_value_id' => $option['product_option_value_id'],
                        'option_id' => $option['option_id'],
                        'option_value_id' => $option['option_value_id'],
                        'name' => $option['name'],
                        'value' => $option['value'],
                        'type' => $option['type']
                    );
                }
                $total_pieces_order += (int) ($product['total_pieces']);
                $total_sets += (int) ($product['quantity']);

                if ($product['shipping']) {
                    $order_data['total_weight'] += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
                }

                $order_data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'sku' => $product['sku'],
                    'option' => $option_data,
                    'download' => $product['download'],
                    'quantity' => $product['quantity'],
                    'piece_in_set' => $product['piece_in_set'],
                    'total_pieces' => $product['total_pieces'],
                    'weight_per_piece' => !empty($product['weight_per_piece']) ? $product['weight_per_piece'] : '',
                    'subtract' => $product['subtract'],
                    'price' => $product['price'],
                    'price_per_piece' => $product['price_per_piece'],
                    'total' => $product['total'],
                    'tax' => $product['tax'],
                    'discount_breakup' => $product['discount_breakup'],
                    'discount_per_piece' => $product['discount_per_piece'],
                    'comment' => $product['set_description'],
                    'seller_id' => $product['seller_id'],
                    'seller_nickname' => $product['seller_nickname'],
                    'seller_tax' => $product['seller_tax'],
                    'commission' => $product['commission'],
                    'store_sales' => $product['store_sales'],
                    'store_pickup' => $product['store_pickup'],
                    'output_tax_rates' => $product['output_tax_rates'],
                    'customer_comment' => $product['comment'],
                    'hsn_code' => $product['hsn_code'],
                    'transfer_price_per_piece' => $product['transfer_price_per_piece'],
                    'unit_id' => $product['unit_id'],
                    'sor_product' => $product['sor_product'],
                    'wsb_purchase_id' => $product['wsb_purchase_id'],
                    'franchise_id' => $product['franchise_id'],
                    'notes' => $product['notes'],
                    'non_returnable' => $product['non_returnable'],
                    'combo_product_id' => $product['combo_product_id']
        );


                if (!empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code'])) {
                    if ($product['store_sales'] == $coupon_data['wsb_store_code']) {
                        $apply_store_code = true;
                    }
                }
            }

            $order_data['franchise_id'] = $this->cart->getFranchiseId();
            $order_data['franchise_margin'] = $this->cart->getFranchiseMargin();

            // If franchise id is not empty, then it means this order is by franchise.
            if (!empty($order_data['franchise_id'])) {
                $order_data['order_by_franchise'] = 1;
            } else {
                $order_data['order_by_franchise'] = 0;
            }

            // If cart does not contain any product of the current franchise, then we will treat this order as normal order
            // i.e. will set the franchise id to zero.
            if (!$this->cart->checkFranchiseProductsInCart($order_data['franchise_id'])) {
                $order_data['franchise_id'] = 0;
                $order_data['franchise_margin'] = 0;
            } else if (!empty($coupon_data['coupon_franchise_cash'])) {
                // If franchise cash coupon is applied, then set franchise margin to zero in order.
                $order_data['franchise_margin'] = 0;
            }


            // Gift Voucher
            $order_data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $order_data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'code' => substr(md5(mt_rand()), 0, 10),
                        'to_name' => $voucher['to_name'],
                        'to_email' => $voucher['to_email'],
                        'from_name' => $voucher['from_name'],
                        'from_email' => $voucher['from_email'],
                        'voucher_theme_id' => $voucher['voucher_theme_id'],
                        'message' => $voucher['message'],
                        'amount' => $voucher['amount']
                    );
                }
            }

            // If store voucher applied then saving store voucher in oc_order and set shipping method
            // to store pickup.
            // if all products from online, then regular shipping charges will be applied, irrespective of store voucher
            if ($apply_store_code) {
                $order_data['wsb_store_voucher'] = $coupon_data['wsb_store_voucher'];
                $order_data['shipping_method'] = 'Store Pickup';
                $order_data['shipping_code'] = 'weight.weight_0';
            }

            if (!empty($coupon_data['wsb_topay_coupon'])) {
                $order_data['shipping_method'] = 'To Pay Courier';
                $order_data['shipping_code'] = 'topay';
            }

            if (!empty($coupon_data['wsb_topay_with_shipping'])) {
                $order_data['shipping_method'] = 'To Pay Courier (Rs 200)';
                $order_data['shipping_code'] = 'topay_with_shipping';
            }

            if (!empty($coupon_data['self_pickup_coupon'])) {
                $order_data['shipping_method'] = 'Self Pickup from Warehouse';
                $order_data['shipping_code'] = 'warehouse_pickup';
            }


            $order_data['comment'] = $this->session->data['comment'];
            $order_data['total'] = $total;

            if (isset($this->request->cookie['tracking'])) {
                $order_data['tracking'] = $this->request->cookie['tracking'];

                $subtotal = $this->cart->getSubTotal();

                // Affiliate
                $this->load->model('affiliate/affiliate');

                $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);

                if ($affiliate_info) {
                    $order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
                    $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                } else {
                    $order_data['affiliate_id'] = 0;
                    $order_data['commission'] = 0;
                }

                // Marketing
                $this->load->model('checkout/marketing');

                $marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

                if ($marketing_info) {
                    $order_data['marketing_id'] = $marketing_info['marketing_id'];
                } else {
                    $order_data['marketing_id'] = 0;
                }
            } else {
                $order_data['affiliate_id'] = 0;
                $order_data['commission'] = 0;
                $order_data['marketing_id'] = 0;
                $order_data['tracking'] = '';
            }
            
            if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
                $currency_id = $this->currency->getId(DUMMY_INR_CURRENCY);
            } else {
              $currency_id = $this->currency->getId('INR');
            }

            $order_data['language_id'] = $this->config->get('config_language_id');
            $order_data['currency_id'] = $currency_id;
            $order_data['currency_code'] = 'INR';
            $order_data['currency_value'] = 1;
            $order_data['currency_live_conversion_rate'] = 1;
            $order_data['ip'] = $this->request->getIpAddress; //$this->request->server['REMOTE_ADDR'];


            if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $order_data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
            } else {
                $order_data['user_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $order_data['accept_language'] = '';
            }

            /*             * Discount on Taxes with coupon code (Ravindra Singh 16-02-2016) End* */

            $data['totals'] = array();
            $data['cform_submit'] = 0;
            $data['text_cst'] = $this->language->get('text_cst');
            $data['text_tax_refund'] = $this->language->get('text_tax_refund');

            $order_data['cform_submit'] = 'no_submit';
            $order_data['cst_with_cform'] = 0.0;
            $order_data['refundable_cform'] = 0.0;
            $order_data['refund_status'] = 'not_applicable';

            foreach ($order_data['totals'] as $total) {

                if ($total['code'] == 'subtotal') {
                    $total['title'] = $this->language->get('text_subtotal');
                } elseif ($total['code'] == 'total') {
                    $total['title'] = $this->language->get('text_total_amount');
                }

                $data['totals'][] = array(
                    'code' => $total['code'],
                    'title' => $total['title'],
                    'text' => $this->currency->format($total['value']),
                );
            }

            $order_data['gst_number'] = '';
            $order_data['gst_unregister_declared'] = '1';
            if (isset($this->request->post['gst_number'])) {
                $order_data['gst_number'] = $this->request->post['gst_number'];
                if (!empty($this->request->post['gst_number'])) {
                    $this->customer->setGSTNumber($this->request->post['gst_number']);
                }
            }
            if (isset($this->request->post['gst_unregister_declared'])) {
                $order_data['gst_unregister_declared'] = $this->request->post['gst_unregister_declared'];
            }

            $order_data['no_wsb_tape'] = '0';
            $order_data['no_invoice_with_shipment'] = '0';
            if (isset($this->request->post['no_wsb_tape'])) {
                $order_data['no_wsb_tape'] = $this->request->post['no_wsb_tape'];
            }
            if (isset($this->request->post['no_invoice_with_shipment'])) {
                $order_data['no_invoice_with_shipment'] = $this->request->post['no_invoice_with_shipment'];
            }

            $this->load->model('checkout/order');

            $order_total_array = array();
            if (!empty($order_data['totals'])) {
                $order_total_array = array_combine(array_column($order_data['totals'], 'code'), $order_data['totals']);
            }

            if(isset($this->session->data['net_order_totals'])) unset($this->session->data['net_order_totals']);
            if (array_key_exists('net_payable_amount', $order_total_array)) {
                $this->session->data['net_order_totals'] = $order_total_array['net_payable_amount']['value'];
            }

            if(isset($this->session->data['rbl_order_totals'])) unset($this->session->data['rbl_order_totals']);
            if (array_key_exists('rbl_payable_amount', $order_total_array)) {
                $this->session->data['rbl_order_totals'] = $order_total_array['rbl_payable_amount']['value'];
            }

            if(isset($this->session->data['tax'])) unset($this->session->data['tax']);
            if (array_key_exists('tax', $order_total_array)) {
                $this->session->data['tax'] = $order_total_array['tax']['value'];
            }


            // Their are orders, which placed without shipping method, so added a check just before adding order
            if(empty($order_data['shipping_code'])){
                $this->session->data['error'] = 'Unable to get shipping method. Please try again.';
                $this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL', 'delivery'));
            }
            
            $this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

            $product_hotness = new ProductHotness($this->db);
            foreach ($products as $product) {
                $product_hotness->updateHotness("place-order", $product['product_id']);
            }

            $data['text_recurring_item'] = $this->language->get('text_recurring_item');
            $data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
        }
        $data['column_image'] = $this->language->get('column_image');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');

        $this->load->model('tool/upload');
        $this->load->model('tool/image');

        $data['products'] = array();


        foreach ($this->cart->getProducts() as $product) {

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

                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                );
            }


            $piece_in_set = $product['piece_in_set'];

            // Display per piece prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price_per_piece = $this->currency->format($this->tax->calculate($product['price'] / $piece_in_set, $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
            } else {
                $price_per_piece = false;
            }

            $recurring = '';

            if ($product['recurring']) {
                $frequencies = array(
                    'day' => $this->language->get('text_day'),
                    'week' => $this->language->get('text_week'),
                    'semi_month' => $this->language->get('text_semi_month'),
                    'month' => $this->language->get('text_month'),
                    'year' => $this->language->get('text_year'),
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

            if ($product['image']) {
                $image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $image = '';
            }
            $data['products'][] = array(
                'key' => $product['key'],
                'product_id' => $product['product_id'],
                'thumb' => $image,
                'name' => $product['name'],
                'model' => $product['model'],
                'set_description' => $product['set_description'],
                'option' => $option_data,
                'recurring' => $recurring,
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price_per_piece' => $price_per_piece,
                'piece_in_set' => $product['piece_in_set'] * $product['quantity'],
                'price' => $this->currency->format($product['price']),
                'total' => $this->currency->format($product['price'] * $product['quantity']),
                'tax' => $this->currency->format($this->tax->getTax($product['price'] * $product['quantity'], $product['hsn_code'], '', '', $product['mrp'])),
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL'),
                'comment' => $product['comment'],
                'unit_id' => $product['unit_id']
            );
        }

        // Gift Voucher
        $data['voucher$cartdatas'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount'])
                );
            }
        }

        $data['totals'] = array();

        foreach ($order_data['totals'] as $total) {
            $data['totals'][] = array(
                'title' => $total['title'],
                'text' => $this->currency->format($total['value'], $this->session->data['currency'])
            );
        }

        if ($render !== false) {
            echo $data['payment'] = $this->load->controller('payment/' . $this->session->data['payment_method']['code']);
            $this->model_account_customer->setPreferences($this->customer->getId(), $data['products']);
        }
    }

    public function cmp($a, $b) {
        return $a["seller_id"] - $b["seller_id"];
    }


    /*
     @method: lazypay_eligibility_check
     @ This lazypay method used to check customer Txn eligibility status using customer mobile and email
     @ Author MSA Feb 2019
    */
    public function lazypay_eligibility_check()
    {
         //vernacular language for app
         if (!empty($this->request->get['language'])) 
         {
           $this->session->data['app_language'] = $this->request->get['language'];
           $this->language->switchLanguage(VERNACULAR_LANGUAGE[$this->request->get['language']]);
         }
         else if (!empty($this->session->data['app_language'])) 
         {             
             $this->language->switchLanguage(VERNACULAR_LANGUAGE[$this->session->data['app_language']]);
           }
         //----end code---

          $this->load->language('checkout/checkout');
          $customer_id = $this->request->post['customer_id'] ?? '';
          $order_total = $this->request->post['total'] ?? '';

          $response = array();
          
          $lazypay = new LazypayPayment($this);
          
            // check if user eligibility check in last 15 minutes
            $buyer_registration_number = $this->customer->getCustomerLazypayRegistrationNumber();
            // Eligibility API end point
            $api_endpoint = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_CHECK_ELIGIBILITY;
            //Set eligibility check payment data
            $api_data = $this->getLazypayEligibilityAPIData($customer_id, $order_total);
            //Set eligibility API signature string
            $lazypay->setLazypaySignature('eligibility',
                                        array(
                                                'mobile'    => $api_data['userDetails']['mobile'] ?? '',
                                                'email'     => $api_data['userDetails']['email'] ?? '',
                                                'amount'    => $order_total, 
                                                'currency'  => LAZYPAY_SIGNATURE_CURRENCY
                                            )
                                        );
            $eligibility_check_response = $lazypay->lazypay_payments( $api_endpoint, $api_data );
            /*create lazypay log for Eligibility check */
             $log_data = array(); //store log data
                 $log_data['url']       = $api_endpoint;
                 $log_data['request']   = json_encode($api_data);
                 $log_data['order_no']  = '';
                 $log_data['request_type']  = 'EligibilityCheck';
                 $log_data['transaction_amount']  = $order_total; //$api_data['amount']['value'];
                 $log_data['buyer_registration_number'] = $this->customer->getCustomerLazypayRegistrationNumber();
            $log_id = $lazypay->createLazypayTransactionLog($log_data);
            /*create lazypay log */

            if( !empty($eligibility_check_response['txnEligibility']) ) {

                $log_data = array();
                $log_data['response'] = json_encode($eligibility_check_response);
                $log_data['status']   = 'SUCCESS';  
                $log_data['message']  = isset($this->_lazypayResponseCodes[$eligibility_check_response['errorCode']])
                                        ? $this->_lazypayResponseCodes[$eligibility_check_response['errorCode']]
                                        : $eligibility_check_response['reason'];
                $log_data['lpTxnId']    = '';
                $lazypay->updateLazypayTransactionLog($log_id, $log_data);

                $this->session->data['lazypay_due_date'] = $eligibility_check_response['dueDate'] ?? '';

                $response = array(
                            'success'           => 1,
                            'msg'               => $this->language->get('text_lazypay_eligibility_msg'),
                            'txnEligibility'    => $eligibility_check_response['txnEligibility'] ?? '',
                            'code'              => $eligibility_check_response['code'] ?? '',
                            'userEligibility'   => $eligibility_check_response['txnEligibility'] ?? '',
                            'dueDate'           => $eligibility_check_response['dueDate'] ?? '',
                        );

            }else{
                
                $log_data = array();
                $log_data['response'] = json_encode($eligibility_check_response);
                $log_data['status']   = 'FAILED';  
                $log_data['message']  = isset($this->_lazypayResponseCodes[$eligibility_check_response['errorCode']])
                                        ? $this->_lazypayResponseCodes[$eligibility_check_response['errorCode']]
                                        : $eligibility_check_response['reason'];
                $log_data['lpTxnId']    = '';
                $lazypay->updateLazypayTransactionLog($log_id, $log_data);
                
                $response = array(
                      'success'           => 0,
                      'msg'               => $log_data['message'] ?? 'Oops!! lazypay payment gatewway not responding!',
                      'txnEligibility'    => $eligibility_check_response['txnEligibility'] ?? '',
                      'code'              => $eligibility_check_response['code'] ?? '',
                      'userEligibility'   => $eligibility_check_response['txnEligibility'] ?? '',
                      'dueDate'           => $eligibility_check_response['dueDate'] ?? '',
                  );
            }

          echo json_encode($response);
          exit;
      }

      /*
       @method: getLazypayEligibilityAPIData
       @ This is LazyPay class method to generate request data list
       @ Param: integer Customer id
       @ Param: string Order total amount
       @ Author MSA Feb 2019
      */
      public function getLazypayEligibilityAPIData(int $customer_id, string $order_total)
      {  
          $this->load->model('account/address');
          $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);

          $lazypay_payment = new LazypayPayment($this);
          $customer_data = $lazypay_payment->getCustomerLazypayData($customer_id);
          
          $lazypay_mobile   = $customer_data['lazypay_mobile'] ?? '';
          $lazypay_email    = $customer_data['lazypay_email'] ?? '';
          $lazypay_firstname= $customer_data['firstname'] ?? '';
          $lazypay_lastname = $customer_data['lastname'] ?? '';

          $data = array();    
          $data['userDetails'] = array(
                  'mobile'    => $lazypay_payment->lazypay_validate($lazypay_mobile, 'phone'), 
                  'email'     => $lazypay_payment->lazypay_validate($lazypay_email, 'email'),
                  'firstName' => $lazypay_payment->lazypay_validate($lazypay_firstname, 'name'),
                  'lastName'  => $lazypay_payment->lazypay_validate($lazypay_lastname, 'name'),
              );
          $data['amount'] = array(
                  'value'     => $order_total,
                  'currency'  => 'INR'
              );
          $data['source'] = 'WholeSaleBox';
          $data['address'] = array( //customer billing address
                  'street1'   => $shipping_address['address_1'] ?? '',
                  'street2'   => $shipping_address['address_2'] ?? '',
                  'city'      => $shipping_address['city'] ?? '',
                  'state'     => $shipping_address['zone_code'] ?? '',
                  'country'   => $shipping_address['country'] ?? '',
                  'zip'       => $shipping_address['postcode'] ?? ''
          );

          $products = $this->cart->getProducts();
          foreach ($products as $key => $value) {
             $productData = array(
                  'productId'     => $is_pre_auth ? 'preauth' : $value['product_id'],
                  'description'   => $is_pre_auth ? 'preauth' : $value['name'],
                  'attributes'    => array(
                                      'size'=> '',
                                      'color'=> '',
                                  ),
                  'image'         => $value['image'],
                  'shippable'     => true,
                  'skus'          => array(
                                      array(
                                              'skuId'      => $value['sku'],
                                              'price'      => $order_total,
                                              'attributes' => array(
                                                      'size'          => '',
                                                      'color'         => '',
                                                      'itemsselected' => 1,
                                                      'amount'        => $order_total, 
                                                  )    
                                          )
                                  )
                  );
          }

          $data['productSkuDetails'] = array( $productData );
          return $data;
      }


    public function validateFreeShippingCoupon( $coupon_code )
    {
        $current_coupons_sql = "SELECT 
                                    c.code,
                                    cc.customer_id 
                                FROM ". DB_PREFIX ."coupon c
                                INNER JOIN ". DB_PREFIX ."coupon_customer cc
                                    ON cc.coupon_id = c.coupon_id
                                WHERE c.name = 'Free Shipping Coupon'
                                AND ((c.date_start = '0000-00-00' OR c.date_start <= CURRENT_DATE()) 
                                     AND (c.date_end = '0000-00-00' OR c.date_end >= CURRENT_DATE()))
                                AND c.code = '". $coupon_code ."'
                                AND cc.customer_id = '". $this->customer->getId() ."'";

        $current_coupons_result = $this->db->query( $current_coupons_sql );
        
        if( $current_coupons_result->num_rows ) {
            $this->_free_shipping_coupon_applied = true;
            $this->session->data['free_shipping_coupon_applied'] = true;

        } else {

            $this->_free_shipping_coupon_applied = false;
            if ( isset($this->session->data['free_shipping_coupon_applied'] )) {
                unset($this->session->data['free_shipping_coupon_applied']);
            }
        }
    }

}
