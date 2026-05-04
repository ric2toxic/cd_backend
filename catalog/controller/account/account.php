
<?php
class ControllerAccountAccount extends Controller {
	public function index() {
		
        $this->load->model('tool/image'); 
        $data['ms_seller_created'] = $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId());
		$data = array_merge($this->load->language('multiseller/multiseller'), $data);

		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
		if($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())){
			$this->response->redirect(HTTPS_SERVER.'seller_panel/#/orders/getPickpupOrderRequested');
		}


		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}


		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}		

		$header_language = array();
		$footer_language = array();
		$login_language = array();
       
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
		$data['direction'] = $header_language['direction'];
		
         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));


		$this->load->language('account/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
	    $data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_order'),
			'href' => $this->url->link('account/order', '', 'SSL')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('account/transaction');
		$amount = $this->model_account_transaction->getTotalAmount();

		$data['amount'] = $amount;
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_my_account'] = $this->language->get('text_my_account');
		$data['text_my_orders'] = $this->language->get('text_my_orders');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_password'] = $this->language->get('text_password');
		$data['text_address'] = $this->language->get('text_address');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_reward'] = $this->language->get('text_reward');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_credit'] = $this->language->get('text_credit');
		$data['text_credit_balance'] = $this->language->get('text_credit_balance');
		$data['text_recurring'] = $this->language->get('text_recurring');

		$data['text_referral'] = $this->language->get('text_referral');
		$data['text_refer_url'] = $this->language->get('text_refer_url');
		$data['text_active'] = $this->language->get('text_active');
		$data['text_pending'] = $this->language->get('text_pending');
		$data['text_blocked'] = $this->language->get('text_blocked');
		$this->load->model('account/customer');
		if ($this->customer->isLogged()) {
			$customer_id = $this->customer->getId();
			$data['is_dropshipper'] = $this->model_account_customer->getisdropshipper($customer_id);
		}
		$data['welcome'] = $this->language->get('welcome');
		$data['name'] = $this->customer->getFirstName();
		$data['email'] = $this->customer->getemail();

		$data['referral_code'] = $this->customer->getreferral_code();

		$data['edit'] = $this->url->link('account/edit', '', 'SSL');
                $data['password'] = $this->url->link('account/password', '', 'SSL');
		$data['address'] = $this->url->link('account/address', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist');
                $data['credit_application'] = $this->url->link('account/credit_application','token='.$this->customer->getAccessToken() . '&customer_id=' . $customer_id,'SSL');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['return'] = $this->url->link('account/return', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['recurring'] = $this->url->link('account/recurring', '', 'SSL');

 		if ($this->config->get('reward_status')) {
			$data['reward'] = $this->url->link('account/reward', '', 'SSL');
		} else {
			$data['reward'] = '';
		}
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		
		if(CONFIG_IS_MOBILE == 1)
        {
		  $data['footer'] = $this->load->controller('common/footer');
		  $data['header'] = $this->load->controller('common/header');
		}  
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/account.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/account.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/account.tpl', $data));
		}
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
  	public function autoLoginSms(){
        $this->load->model('account/customer');
		//$otp = "1234";
		//echo parse_url($_SERVER['REMOTE_ADDR']);
		//echo '<pre>'; print_r($_GET); //exit;
		$url = explode('/',$_GET['route']);
		//echo '<pre>'; print_r($url[count($url)-1]); exit;

		$otp = $url[count($url)-1];//$this->request->get['otp'];
		//echo $otp; exit;
		$otp = base64_decode($otp);
        $customer_info = $this->model_account_customer->getCustomerByOtp($otp);
        //echo '<pre>'; print_r($customer_info); exit;
        if ($this->customer->login($customer_info['telephone'], '', true)) {

            $this->load->model('account/address');

            $this->event->trigger('post.customer.login');

            $this->response->redirect($this->url->link('account/account', '', 'SSL'));

            echo "success"; exit;
        }else{
            echo "Failed"; exit;
        }
    }
    public function autoLoginEmail(){
        $this->load->model('account/customer');
        //$customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['otp']);
        //$mobile = base64_decode($this->request->get['mobile']);
        //$mobile = '9828544446';
        //echo "<pre>"; print_r($this->session->data); //exit;
        $cdays = 30;
        if(setcookie('mobile', 'skipping...', time() + (86400 * $cdays), "/",".".HTTP_DOMAIN )){
			if(setcookie('skip', 'skipped', time() + (86400 * $cdays), "/",".".HTTP_DOMAIN )){
			//$this->response->redirect($this->url->link('common/home'));
			//echo "dddd"; exit;
			}
		}
        $url = explode('/',$_GET['route']);
		$email = $url[count($url)-1];//$this->request->get['otp'];
		$rt = unserialize(base64_decode($email));
		$customerId = key($rt);
		$otp = $rt[$customerId];
        if ($this->customer->isLogged()) {
			$this->event->trigger('pre.customer.logout');

			$this->customer->logout();
			$this->cart->clear();

			unset($this->session->data['wishlist']);
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_address']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);

			$this->event->trigger('post.customer.logout');

			//$this->response->redirect($this->url->link('account/logout', '', 'SSL'));
		}
        $customer_info = $this->model_account_customer->getCustomerByIdOtp($customerId,$otp);

        if ($this->customer->login($customer_info['telephone'], '', true)) {
		//echo "<pre>"; print_r($this->session->data); //exit;
            $this->load->model('account/address');

            $this->event->trigger('post.customer.login');

            $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));

            echo "success"; exit;
        }else{
            echo "Failed"; exit;
        }
    }

    public function checkoutlogin(){
        $this->load->model('account/customer');
        //$customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['otp']);
        //$mobile = base64_decode($this->request->get['mobile']);
        //$mobile = '9828544446';
        //echo "<pre>"; print_r($this->session->data); //exit;
        $cdays = 30;
        //setcookie('ORDER_FROM', 'ANDROIDAPP', time() + (86400 * $cdays), "/",".".HTTP_DOMAIN );
        $this->session->data['ORDER_FROM'] = 'ANDROIDAPP';
        $headers = getallheaders();
        // Check headers, if request is coming from ios_app, than change the value of order_from to IOSAPP;
        if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP'){
            $this->session->data['ORDER_FROM'] = 'IOSAPP';
        }
        if(!empty($_GET['campaign_event_no'])){
            $campaign_event_no = $_GET['campaign_event_no'];
            setcookie('campaign_event_no',$campaign_event_no,time() + (86400 * $cdays),"/",".".HTTP_DOMAIN);
        }

        if(setcookie('mobile', 'skipping...', time() + (86400 * $cdays), "/",".".HTTP_DOMAIN )){
			if(setcookie('skip', 'skipped', time() + (86400 * $cdays), "/",".".HTTP_DOMAIN )){
			//$this->response->redirect($this->url->link('common/home'));
			//echo "dddd"; exit;
			}
		}
        $url = explode('/',$_GET['route']);
		$email = $url[count($url)-1];//$this->request->get['otp'];
		$rt = unserialize(base64_decode($email));
		
		// sales_staff_id to be stored in session and carried forward so that no cashback is given if FSE is ordering for customer via CRM
		if(!empty($rt['sales_staff_id'])) {
			$this->session->data['SALES_STAFF_ID'] = $rt['sales_staff_id'];
		}
		
		$customerId = key($rt);
		$otp = $rt[$customerId];
     
		if (!empty($rt['language'])) 
         {
           $this->session->data['app_language'] = $rt['language'];
         }
         else
         {
           $this->session->data['app_language'] = $this->config->get('config_language_id'); 
         }

        if ($this->customer->isLogged()) {
		//if (isset($this->session->data['customer_id'])) {
			$this->event->trigger('pre.customer.logout');

			$this->customer->logout();
			$this->cart->clear();

			unset($this->session->data['wishlist']);
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_address']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);

			if ( isset($this->session->data['free_shipping_coupon_applied'])) {
                unset( $this->session->data['free_shipping_coupon_applied'] );
            }

			$this->event->trigger('post.customer.logout');

			//$this->response->redirect($this->url->link('account/logout', '', 'SSL'));
		}
		//echo $customerId; echo $otp;
		$customer_access_token =  $this->generate_access_token();

        $customer_info = $this->model_account_customer->getCustomerByIdOtp($customerId,$otp);
       // echo "<pre>"; print_r($customer_info); exit;
        // 11-07-2017: From now logging the customer using customer id and otp instead of mobile no
        // because their are possibilities that multiple customers can have same mobile number.
        if ($this->customer->loginUsingIdOtp($customerId,$otp,$customer_access_token)) 
        {
        	//trigger webengage event
            $this->load->model('webengage/webengage');
            $event_data = array();
            $event_data['eventName']  = 'Checkout Login';
             $event_data['userId']    = $customerId;
            $event_data['eventData']  = array('Customer Id' => (string)$customerId); 
            $this->model_webengage_webengage->pushEventToQueue($event_data);

			$this->session->data["popup"] = true;
            $this->load->model('account/address');

            $this->event->trigger('post.customer.login');

            // Reset user_country cookie when opening checkout in app
            setcookie('user_country', '', time() + 60 * 60 * 24 * 30, '/', INDIA_STORE_HOST);
            setcookie('user_country', '', time() + 60 * 60 * 24 * 30, '/', INTERNATIONAL_STORE_HOST);

			//One page checkout
			$this->response->redirect($this->url->link('checkout/one_page_checkout&popup=true', '', 'SSL'));

            echo "success"; exit;
        }else{
            echo "Failed"; exit;
        }
    }

    public function naya_function(){
     exit("yaya aao");

 }
	/**
	 * change_customer_password
	 * @author Ravindra Singh
	 * @Date 04-04-2016
	 ***/
	public function change_customer_password(){
		$this->load->model('account/customer');
		$this->load->model('tool/image');

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}


		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}		

		$header_language = array();
		$footer_language = array();
		$login_language = array();
       
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
		$data['direction'] = $header_language['direction'];
		
         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

        $this->load->language('account/password');

        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
 
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));


		if(isset($this->request->post['email']) && !empty($this->request->post['email'])){
			$email = $this->request->post['email'];
			if(is_numeric($email)){
				$customerInfo = $this->model_account_customer->getCustomerByMobile($email);
			}else{
				$customerInfo = $this->model_account_customer->getCustomerByEmail($email);
			}
			if(!empty($customerInfo)){
				$password =  mt_rand(100000, 999999);

				if($this->model_account_customer->editPassword($customerInfo['customer_id'], $password)){
					$data['password'] = $password;
					$data['column_left'] = $this->load->controller('common/column_left');
					$data['column_right'] = $this->load->controller('common/column_right');
					$data['content_top'] = $this->load->controller('common/content_top');
					$data['content_bottom'] = $this->load->controller('common/content_bottom');
                  
                   if(CONFIG_IS_MOBILE == 1)
                   {	
					$data['footer'] = $this->load->controller('common/footer');
					$data['header'] = $this->load->controller('common/header');
				   }	

					$data['action'] = $this->url->link('account/account/change_customer_password', '', 'SSL');
					$data['staff_id'] = $this->request->post['staff_id'];
					$data['success'] = "Customer Password Has Been Updated Successfully.And New Password is ".$password;

					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/change_customer_password.tpl')) {
						$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/change_customer_password.tpl', $data));
					} else {
						$this->response->setOutput($this->load->view('default/template/account/change_customer_password.tpl', $data));
					}
				}else{
					$this->response->redirect('/');
				}
			}else{
				$this->response->redirect('/');
			}

		}else{
			if(isset($_GET['token']) && !empty($_GET['token'])){
				$token = $_GET['token'];
				//$token = base64_decode($token);
				$staff_member_info = $this->model_account_customer->getStaffMemberInfo($token);
				///echo "<pre>"; print_r($staff_member_info); exit;
				if(isset($staff_member_info) && !empty($staff_member_info)){
					//echo "<pre>"; print_r($staff_member_info); exit;
					$data['column_left'] = $this->load->controller('common/column_left');
					$data['column_right'] = $this->load->controller('common/column_right');
					$data['content_top'] = $this->load->controller('common/content_top');
					$data['content_bottom'] = $this->load->controller('common/content_bottom');
					$data['footer'] = $this->load->controller('common/footer');
					$data['header'] = $this->load->controller('common/header');
					$data['action'] = $this->url->link('account/account/change_customer_password', '', 'SSL');
					$data['staff_id'] = $staff_member_info['staff_id'];
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/change_customer_password.tpl')) {
						$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/change_customer_password.tpl', $data));
					} else {
						$this->response->setOutput($this->load->view('default/template/account/change_customer_password.tpl', $data));
					}
				}
			}else{
				$this->response->redirect('/');
			}
		}
	}
	/**
	 * update_customer_password
	 * @author Ravindra Singh
	 * @Date 04-04-2016
	 ***/
	public function update_customer_password(){
		$this->load->model('account/customer');
		//echo "<pre>"; print_r($this->request->post['email']); exit;
		if(isset($this->request->post['email']) && !empty($this->request->post['email'])){
			$email = $this->request->post['email'];
			if(is_numeric($email)){
				$customerInfo = $this->model_account_customer->getCustomerByMobile($email);
			}else{
				$customerInfo = $this->model_account_customer->getCustomerByEmail($email);
			}
			$password =  mt_rand(100000, 999999);

			if($this->model_account_customer->editPassword($customerInfo['customer_id'], $password)){
				$data['password'] = $password;
			}
		}
	}

    /* 
     * @method: updateShippingPreferences
     * @purpose: update the customer's shipping preferences i.e. packaging preference(wsb tape and invoice) and courier partner preferences
     * @params: no_wsb_tape, no_invoice_with_shipment, courier_partner_preference, order_id, suborder_id
     * @author: Mahaveer, April 2019
     */
    public function updateShippingPreferences() {
        $response = array();
        
        if (!isset($this->request->post['order_id'])) {
            $response['error'] = 'Order Error: Please try again!';
            echo json_encode($response); exit;
        }
        
        $get_customer_id = OrderInfo::getCustomerIdFromOrder($this->db, $this->request->post['order_id']);

        $customer_id = $this->customer->getId();
        if( $get_customer_id != $customer_id){
            $response['error'] = 'Security Error: You are not authorized!';
            echo json_encode($response); exit;
        }
        
        $update_fields = array();
        
        $order_id = $this->request->post['order_id'];
        $suborder_id = '';
        if (isset($this->request->post['suborder_id'])) {
            $suborder_id = $this->request->post['suborder_id'];
        }
        
        if (isset($this->request->post['no_wsb_tape'])) {
            $update_fields[] = ' no_wsb_tape=' . (int)$this->request->post['no_wsb_tape'] . ' ';
        }
        
        if (isset($this->request->post['no_invoice_with_shipment'])) {
            $update_fields[] = ' no_invoice_with_shipment=' . (int)$this->request->post['no_invoice_with_shipment'] . ' ';
        }
        
        if (isset($this->request->post['courier_partner_preference'])) {
            $update_fields[] = ' courier_partner_preference=\'' . $this->request->post['courier_partner_preference'] . '\' ';
        }
        
        if (empty($update_fields)) {
            $response['error'] = 'Can\'t Update: Please try again!';
            echo json_encode($response); exit;
        }
        
        $this->load->model('account/order');
        
        $result = $this->model_account_order->updateSuborder($update_fields, $order_id, $suborder_id);
        
        if ($result) {
            $response['success'] = 'Your preferences successfully saved. You can view or edit them in my order section.';
        } else {
            $response['error'] = 'FAILURE: Failed to update.';
        }
        
        echo json_encode($response); exit;
    }



 public function generate_access_token()
 {
    $length = 16; 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
        
        
}
