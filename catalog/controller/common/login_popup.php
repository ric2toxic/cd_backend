<?php
class ControllerCommonLoginPopup extends Controller {

	private $error = array();

	public function index()
	{

       $this->load->form('register_form');
       $this->load->model('account/customer');
       $this->load->model('localisation/country');
       $this->load->model('localisation/zone');
       $this->load->language('account/login');

       $config['CustomersType'] = $this->model_account_customer->getCustomersType();
       $config['country_data']  = $this->model_localisation_country->getCountries();
       $data['entry_zone']      = $this->language->get('entry_zone');

       if(isset($_COOKIE['user_country']) && !empty($_COOKIE['user_country']))
       {
         $config['country_id']   = $this->model_localisation_country->getCountryByCode($_COOKIE['user_country']);
       }

        /********** Popup Form**********/
        $RegisterForm                    = new RegisterForm($config);
        $data['send_otp_form']           = $RegisterForm->send_otp_form();
        $data['login_form']              = $RegisterForm->login_form();
        $data['register_form']           = $RegisterForm->register_form();
        $data['verify_otp_form']         = $RegisterForm->verify_otp_form();
        $data['verify_otp_forgot_form']  = $RegisterForm->verify_otp_form('', 'verify_otp_forgot_form');


        $data['send_otp_url']               = $this->url->link('common/login_popup/send_otp', '', 'SSL');
        $data['login_form_url']             = $this->url->link('common/login_popup/login', '', 'SSL');
        $data['register_form_url']          = $this->url->link('common/login_popup/register', '', 'SSL');
        $data['verify_otp_form_url']        = $this->url->link('common/login_popup/verify_otp', '', 'SSL');
        $data['verify_otp_forgot_form_url'] = $this->url->link('common/login_popup/verify_otp', '', 'SSL');
        $data['manufacturer_link'] = $this->url->link('common/seller_home', '', 'SSL');

		if(isset($this->request->get['is_dropshipper']))
		{
			$data['is_dropshipper'] = $this->request->get['is_dropshipper'];
			$this->is_dropshipper = 2;
		}
		else
		{
			$data['is_dropshipper'] = 0;
		}

		if(isset($_COOKIE['customer_mobile']) && !empty($_COOKIE['customer_mobile']))
        {
          $data['customer_mobile'] = $_COOKIE['customer_mobile'];
        }
        if(isset($_COOKIE['country_code']) && !empty($_COOKIE['country_code']))
        {
          $data['country_code'] = $_COOKIE['country_code'];
        }
        if(isset($_COOKIE['country_iso_code']) && !empty($_COOKIE['country_iso_code']))
        {
          $data['country_iso_code'] = $_COOKIE['country_iso_code'];
        }

       if(isset($this->session->data['otp_verify_success']))
       {
       	 $data['otp_verify_success'] = $this->session->data['otp_verify_success'];
       	 unset($this->session->data['otp_verify_success']);
       }
       

       if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
       {
       	 $data['international_store'] = 1;
       }
       else
       {
       	$data['international_store'] = 0;
       }

	  if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/login_popup.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/common/login_popup.tpl', $data);
			} else {
				return $this->load->view('default/template/common/login_popup.tpl', $data);
			}

	}


	public function login()
	{
	    $ajax = new ajax;
	    $ajax->HTTP_ORIGIN();
        $result = array('error'=>1);
        $this->load->language('account/login');
        $this->load->model('account/customer');

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
		{
            $this->request->post['reg_telephone'] = $_COOKIE['customer_mobile'];
            $result['redirect_cart'] = $this->request->post['redirect_cart'];

            $redirect_url = '';
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->loginValidation())
			{
               $redirect_url =  $this->login_process();
			}

			if (isset($this->error['warning']))
			{
			  $result['msg'] = $this->error['warning'];
			  $result['url']   = $redirect_url;
		    }
		    else
		    {
			  $result['msg']   = $this->language->get('text_login_success');
			  $result['error'] = 0;
			  $result['url']   = $redirect_url;
		    }

           header('Content-Type: application/json');
           $this->response->setOutput(json_encode($result));
        }
        else if (!empty($this->request->get['token']))
        {
           $this->customer->logout();
		   $this->cart->clear();
		   $customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);
		   $access_token  = $this->generate_access_token();

		   if(!empty($customer_info['telephone']))
                $customer_to_login = $customer_info['telephone'];
            else if(!empty($customer_info['email']))
                $customer_to_login = $customer_info['email'];
            else
                $customer_to_login = $customer_info['customer_id'];
            
		   if ($this->customer->login($customer_to_login, '', true, false, $access_token))
		   {
		   	  $redirect_url =  $this->login_process();
              $this->response->redirect($redirect_url);
		   }
		   else
		   {
		   	   $this->response->redirect($this->url->link('common/home', '', 'SSL'));
		   }

        }
        else
        {
            $this->request->post['reg_telephone'] =  $this->request->post['email'];
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->loginValidation())
			{
               $redirect_url =  $this->login_process();
               $this->response->redirect($redirect_url);
			}
			else
			{
			   $this->session->data['otp_verify_success'] = 2;
    	       $this->response->redirect($this->url->link('common/home', '', 'SSL'));
    	       exit;
			}
        }

    }


	public function seller_login()
	{
        $result = array('error'=>1);
        $this->load->model('account/customer');

        if(!empty($this->request->get['token']))
        {
           $this->customer->logout();
		   //$this->cart->clear();
		   $customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);
		   $access_token  = $this->generate_access_token();
		   
		   $customer_mobile = !empty($customer_info['telephone']) ? $customer_info['telephone'] : $customer_info['email']; 
		   
		   $this->model_account_customer->setSellerToken($customer_info['customer_id'], $access_token, $customer_mobile); 

		  if ($this->customer->login($customer_info['telephone'], '', true, false, $access_token))
		  {

		   $this->response->redirect(SELLER_PANEL_LANDING_PAGE_URL);
    	       exit;
    	  } 
    	  else
    	  {
    	  	 $this->response->redirect($this->url->link('common/home', '', 'SSL'));
    	  	 exit;
    	  }    

        }

    }

	public function register()
	{
	    $ajax = new ajax;
	    $ajax->HTTP_ORIGIN();
        $result = array('error'=>1);

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        {
        	  $this->load->form('register_form');
		      $this->load->language('account/login');
		      $this->load->model('account/customer');
		      $config = array();
		      $config['config_store_id'] = $this->config->get('config_store_id');
              $RegisterForm = new RegisterForm($config);

		if(($this->request->server['REQUEST_METHOD'] == 'POST'))
		{

            if(is_numeric($_COOKIE['customer_mobile']))
            { 
			  $this->request->post['reg_telephone']          = $_COOKIE['customer_mobile'];
			  $this->request->post['mobile_country_code']     = $_COOKIE['country_code'];
			  $this->request->post['address_telephone']       = $_COOKIE['customer_mobile'];
		    }
		    else
		    {
		       $this->request->post['reg_email'] = $_COOKIE['customer_mobile'];
		       $this->request->post['mobile_country_code']  = '';
			   $this->request->post['address_telephone']    = $this->request->post['reg_telephone'];
		    }


			$redirect_url                                   = '';
			$arr_name                                       = explode(" ", $this->request->post['name']);
			$this->request->post['firstname']               = $arr_name[0];

			if(isset($arr_name[1])) {
					$this->request->post['lastname'] = $arr_name[1];
				}else{
					$this->request->post['lastname'] = '';
			}

            if(isset($this->request->post['customer_type_id']))
            {
               $this->request->post['customer_type_id'] = implode(",", $this->request->post['customer_type_id']);
            }
            else
            {
               $this->request->post['customer_type_id'] = '';
            }

           if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
           {
             $registerValidation = $RegisterForm->registerValidationCo($this->request->post);
           }
           else
           {
           	$registerValidation = $RegisterForm->registerValidation($this->request->post);
           }

			if ($registerValidation['valid'] && $this->registerCustomValidation())
			{

				$customer_id = $this->model_account_customer->addCustomer($this->request->post);

				$this->load->language('account/sms_templates');
			    $message = $this->language->get('on_normal_signup');
			    $msg = sprintf($message,$this->request->post['reg_telephone'],$this->request->post['password']);
				$send_sms = new SMS($msg, $this->request->post['reg_telephone']);
			    $send_sms->sendMessage();

				$this->load->model('account/address');
				$this->request->post['address_2'] = '';
				$address_id = $this->model_account_address->addAddress($this->request->post, $customer_id);

				if(isset($_COOKIE['session_id']) && isset($_COOKIE['utm_source'])){
					$this->load->model('tracking/tracking');
					$this->model_tracking_tracking->update_customer_id($customer_id,$mobile);
				}
				// Clear any previous login attempts for unregistered accounts.
				$this->model_account_customer->deleteLoginAttempts($this->request->post['reg_email']);

				$access_token  = $this->generate_access_token();
               if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
               { 
				 $this->customer->login($this->request->post['reg_email'], $this->request->post['password'], false, false, $access_token);
               }
               else
               {
               	 $this->customer->login($this->request->post['reg_telephone'], $this->request->post['password'], false, false, $access_token);
               } 
				unset($this->session->data['guest']);

                //Add access token
                $access_token = $this->model_account_customer->addCustomerToken($customer_id);

				if($this->request->post['referrers']=='cart'){
					$redirect_url = $this->url->link('checkout/one_page_checkout', '', 'SSL');
				}else{

					$redirect_url = $this->url->link('account/success');
				}
			}
		}


			if (!$registerValidation['valid'])
			{
			  $result['msg'] = $registerValidation['errors']['message'];
			  $result['url']   = $redirect_url;
		    }
		    else if (isset($this->error['warning']))
			{
			  $result['msg'] = $this->error['warning'];
			  $result['url']   = $redirect_url;
		    }
		    else
		    {
			  $result['msg']   = '';
			  $result['error'] = 0;
			  $result['url']   = $redirect_url;
		    }
	 }

        header('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));

    }



  private function login_process()
   {
		unset($this->session->data['guest']);
		$this->load->model('account/address');
		$this->load->model('account/customer');

		if ($this->config->get('config_tax_customer') == 'payment')
		    {
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}
		if ($this->config->get('config_tax_customer') == 'shipping')
		    {
			$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}

         //Add access token
		  $access_token = $this->model_account_customer->addCustomerToken($this->customer->getId());

		if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
				$redirect_url = str_replace('&amp;', '&', $this->request->post['redirect']);
			}
	    else {
			if (!empty($this->request->post['referrers']) && $this->request->post['referrers']=='cart')
			   {
					$redirect_url = $this->url->link('checkout/one_page_checkout', '', 'SSL');
				}
		    else {
                    if (!CONFIG_IS_MOBILE && $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId()))
                    {
                        if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_ACTIVE)
                           {
                              //$redirect_url =  $this->url->link('seller_panel/account-order', '', 'SSL');
                              $redirect_url =  "./seller_panel/#/orders/getPickpupOrderRequested";
                            }
		                else if($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE)
		                    {
		                    	//$redirect_url =  $this->url->link('seller_panel/profile', '', 'SSL');
		                    	$redirect_url =  "./seller_panel/#/orders/getPickpupOrderRequested";
		                    }
		                else{
		                    	$redirect_url =  $this->url->link('account/logout', '', 'SSL');
		                    }
				    }
				    else
				       {
				        	$redirect_url =  $this->url->link('account/order', '', 'SSL');
				        }
				}
			}

		return $redirect_url;
   }


	public function loginValidation()
	{
		$this->event->trigger('pre.customer.login');

		/*$login_info = $this->model_account_customer->getMobileLoginAttempts($this->request->post['reg_telephone']);
		if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
			$this->error['warning'] = $this->language->get('error_attempts');
		}*/
		//if (!$this->error)
		//{
		$access_token  = $this->generate_access_token();
		if (!$this->customer->login($this->request->post['reg_telephone'], $this->request->post['password'], false, false, $access_token))
			{
			    $this->error['warning'] = $this->language->get('error_login');
               if(is_numeric($this->request->post['reg_telephone']))
               {
				 $this->model_account_customer->addMobileLoginAttempt($this->request->post['reg_telephone']);
			   }
			   else
			   {
			   	$this->model_account_customer->addLoginAttempt($this->request->post['reg_telephone']);
			   }
			}
			else
			{
               if(is_numeric($this->request->post['reg_telephone']))
               {
				 $this->model_account_customer->deleteMobileLoginAttempts($this->request->post['reg_telephone']);
			   }
			   else
			   {
			   	$this->model_account_customer->deleteLoginAttempts($this->request->post['reg_telephone']);
			   }

				$this->event->trigger('post.customer.login');
				$this->load->model('lead/lead');
				$telephone = $this->customer->getTelephone();
				if (!empty($telephone)) {
					$lead_data = [
						'last_login_date'	=> date('Y-m-d H:i:s')
					];
					$this->model_lead_lead->updateLead($lead_data, $telephone, 'Last Login', $this->customer->getId() );
				}
			}
		//}
		return !$this->error;
	}


	public function registerCustomValidation()
	{

		if (!empty($this->request->post['reg_email']) && $this->model_account_customer->getTotalCustomersByEmailToValidate($this->request->post['reg_email']))
		 {
			 $this->error['warning'] = $this->language->get('error_exists');
		 }

		
		if($this->config->get('config_store_id') != INTERNATIONAL_STORE_ID || !empty($this->request->post['reg_telephone']))
        { 

		    if ($this->model_account_customer->getTotalCustomersByTelephone($this->request->post['reg_telephone']))
		   {
			 $this->error['warning'] = $this->language->get('error_exists_phone');
		   }

		}   

		/*** Validate GST Number from GST library ***/
	   if(isset($this->request->post['gst_uin_number']) && $this->request->post['gst_uin_number'] == 1)	
	   {	
        $gstObject = new GST($this->registry);
        $customer_id = 0;
        $seller_duplicacy_check = true;
        $current_customer = array(
                                'customer_id' => 0,
                                'firstname' => $this->request->post['firstname'],
                                'telephone' => $this->request->post['reg_telephone'],
                                'email' => $this->request->post['reg_email'],
                                'master_id' => 0
                            );
        $valid_gst_result = $gstObject->validateGSTNumber($this->request->post['gst_number'], $customer_id, $seller_duplicacy_check, $current_customer);

        if($valid_gst_result['message'] == "error_regex") {
            $this->error['warning'] = $this->language->get('error_gst_number');
        } elseif($valid_gst_result['message'] == "error_checksum") {
            $this->error['warning'] = sprintf($this->language->get('error_gst_checksum'),
                                                 $valid_gst_result['gst_number_details']['gst_number_without_checksum']."<b>".$valid_gst_result['gst_number_details']['gst_number_checksum']."</b>");
        } else if($valid_gst_result['message'] == "error_duplicate") {
            /**
              *  - Commenting below code to allow customer to add duplicate GST Number (Duplicacy mail is already being sent)
              *  - By Anurag Jain(Sept 2018)
              **/
            // $user_str = '';
            // if(!empty($valid_gst_result['duplicate_gst_number_details'])) {
            //     $duplicate_gst_number_customer = $valid_gst_result['duplicate_gst_number_details'];
            //     if(!empty($duplicate_gst_number_customer['telephone'])) {
            //         $user_str = 'mobile number <strong>' . substr($duplicate_gst_number_customer['telephone'],0, 2) . 'xxxxx' . substr($duplicate_gst_number_customer['telephone'],7) . '</strong>';
            //     } else if(!empty($duplicate_gst_number_customer['email'])){
            //         $len = strlen(explode('@',$duplicate_gst_number_customer['email'])[0]);
            //         $user_str = 'email <strong>' . 'xxxxx' . substr($duplicate_gst_number_customer['email'],$len/2).'</strong>';
            //     }
            //     $this->error['warning'] = sprintf($this->language->get('error_exists_gst'), $this->request->post['gst_number'], $user_str, $this->url->link('information/contact') );
            // }
         }
        }
        /*** End: GST Number Validation ***/ 

		// Customer Group

		// Agree to terms
		if ($this->config->get('config_account_id'))
		{
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			if ($information_info && !isset($this->request->post['agree'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

		return !$this->error;
	}




	public function send_otp()
	{ 
	  $ajax = new ajax;
	  $ajax->HTTP_ORIGIN();
      $result = array('error'=>1);

   if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
     {
      $this->load->form('register_form');
      $this->load->language('account/sms_templates');
      $this->load->language('account/login');
      $this->load->model('account/customer');

	   $config = array();
	   $config['config_store_id'] = $this->config->get('config_store_id');
       $RegisterForm = new RegisterForm($config);
       
	  if(isset($this->request->post['reg_telephone']))
		{

		  	$mobileValidation = $RegisterForm->mobileValidation($this->request->post);

		if ($mobileValidation['valid'])
		  {
            $result['reg_telephone']      =  preg_replace('/\s+/', '', $this->request->post['reg_telephone']);
	  	    $result['country_code']       =  preg_replace('/\s+/', '', $this->request->post['country_code']);
	  	    $result['country_iso_code']   = preg_replace('/\s+/', '',  $this->request->post['country_iso_code']);
			$result['user_verify']        = 0;

		  	$user_data = $this->model_account_customer->getCustomerByEmailOrMobile($result['reg_telephone']);
		  if(count($user_data) == 0)
		  	 {
				$result['new_user'] = 1;
				$user_verify = array();
			    if(isset($_COOKIE['customer_mobile']) && $_COOKIE['customer_mobile']==$result['reg_telephone'])
	            {
				  $user_verify = $this->model_account_customer->checkUserIsVerify($_COOKIE['customer_mobile']);
			     }

			    if(count($user_verify) == 0)
			     {
	  	            $result['session_id']    = session_id();
	  	            $result['ip']            = $this->request->getIpAddress;
	  	            $result['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
	  	            $result['otp']           = (SITE_ENVIRONMENT =='Production')?rand(1000, 9999):1010;
	  	            $result['otp_page']      = 'sign_up';
	  	            $result['customer_id']   = 0;
	  	            $user_otp = $this->model_account_customer->addOTP($result);
	  	            if($user_otp > 0) { $result['otp'] = $user_otp; }

                    if(is_numeric($result['reg_telephone']))
                      {
                         $message   = $this->language->get('on_app_signup');
		  	             $message   = sprintf($message,$result['otp']);
                    	 $this->send_mail_or_sms($result['country_code'].$result['reg_telephone'], $message, '', '');
                       }
                     else
                       {
                          $message   = $this->language->get('on_app_signup_email');
                          $otp_email = base64_encode($result['reg_telephone'].' '.$result['otp_page'].' '.$result['otp']);
                       	  $verify_link = $this->url->link('common/login_popup/email_otp_verify', '', 'SSL').'&otp='.$otp_email;
                       	  $message   = sprintf($message,$result['otp'], $verify_link);
		                  $this->send_mail_or_sms($result['reg_telephone'], $message, 'OTP', $result['otp']);
                       }

			       }
			       else { $result['user_verify'] = 1; }
		  	  }
		  	  else
		  	  {
		  	    $result['new_user'] = 0;
				$result['user_verify'] = 1;
				setcookie('customer_mobile', $result['reg_telephone'], time() + (86400 * 7), "/");
			    setcookie('register_user', 1, time() + (86400 * 7), "/");

			    if(is_numeric($result['reg_telephone']))
				{
				  if($user_data['mobile_country_code'] != '')
				  {
				   $result['country_code']     = $user_data['mobile_country_code'];
				   $result['country_iso_code'] = array_search('+'.$result['country_code'], $this->mobile_country_code["CO"]);
				  }

				     setcookie('country_code', $result['country_code'], time() + (86400 * 7), "/");
			         setcookie('country_iso_code', $result['country_iso_code'], time() + (86400 * 7), "/");

				   }
				   else
				   {
				   	setcookie('country_code', '', time() - 86400, "/");
			        setcookie('country_iso_code', '', time() - 86400, "/");
				   }
		       }


                  if(!is_numeric($result['reg_telephone']))
                  {
                    $result['country_iso_code'] = '';
                    $result['country_code'] = '';
                    $result['otp']   = '';
                     $result['error'] = 0;
                    $result['msg']   = sprintf($this->language->get('otp_success_email'), $result['reg_telephone']);
                  }
                  else
                  {
                  	 $result['otp']   = '';
		             $result['msg']   = sprintf($this->language->get('otp_success'), $result['reg_telephone']);
		             $result['error'] = 0;
                  }

		  }
		  else
		  {
		 	$result['msg']   = $mobileValidation['errors']['message'];
		    $result['error'] = 1;
		  }

	   }}

        header('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));

    }


	public function send_password_otp()
	{
	  $ajax = new ajax;
	  $ajax->HTTP_ORIGIN();
      $result = array('error'=>1);

	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    {
      $this->load->language('account/sms_templates');
      $this->load->language('account/login');
      $this->load->model('account/customer');

	  if(isset($_COOKIE['customer_mobile']) && !empty($_COOKIE['customer_mobile']))
      {
            $result['reg_telephone'] = $_COOKIE['customer_mobile'];
            if(!empty($_COOKIE['country_code'])) { $result['country_code']  = $_COOKIE['country_code']; }
            else { $result['country_code']  = ''; }

		  	$user_data = $this->model_account_customer->getCustomerByEmailOrMobile($result['reg_telephone']);

		  	if(count($user_data) > 0)
		  	{
	  	        $result['session_id']    = session_id();
	  	        $result['ip']            = $this->request->getIpAddress;
	  	        $result['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
	  	        $result['otp']           = (SITE_ENVIRONMENT =='Production')?rand(1000, 9999):1010;
	  	        $result['otp_page']      = 'forgot_password';
	  	        $result['customer_id']   = $user_data['customer_id'];
	  	       	$user_otp = $this->model_account_customer->addOTP($result);
	  	        if($user_otp > 0) { $result['otp'] = $user_otp; }

                     if(is_numeric($result['reg_telephone']))
                      {
                         $message   = $this->language->get('on_app_signup');
		  	             $message   = sprintf($message,$result['otp']);
                    	 $this->send_mail_or_sms($result['country_code'].$result['reg_telephone'], $message, '', '');
                       }
                     else
                       {
                          $message   = $this->language->get('on_app_signup_email');
                          $otp_email = base64_encode($result['reg_telephone'].' '.$result['otp_page'].' '.$result['otp']);
                       	  $verify_link = $this->url->link('common/login_popup/email_otp_verify', '', 'SSL').'&otp='.$otp_email;
                       	  $message   = sprintf($message,$result['otp'], $verify_link);
		                  $this->send_mail_or_sms($result['reg_telephone'], $message, 'OTP', $result['otp']);
                       }


                  if(!is_numeric($result['reg_telephone']))
                  {
                     $result['otp']   = '';
                     $result['error'] = 0;
                     $result['msg']   = sprintf($this->language->get('otp_success_email'), $result['reg_telephone']);
                  }
                  else
                  {
                  	 $result['otp']   = '';
		             $result['msg']   = sprintf($this->language->get('otp_success'), $result['reg_telephone']);
		             $result['error'] = 0;
                  }


		  	}
	    }
	  }

        header('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));
    }



	public function verify_otp()
	{
	  $ajax = new ajax;
	  $ajax->HTTP_ORIGIN();
      $result = array('error'=>1);

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    {
      $this->load->language('account/login');
      $this->load->model('account/customer');
	  if(isset($this->request->post['otp']))
		{

            $result['otp_page']    = $this->request->post['otp_page'];
            if($result['otp_page'] == 'sign_up')
            {
            	$result['reg_telephone']    = $this->request->post['reg_telephone'];
          	    $result['redirect_cart']    = $this->request->post['redirect_cart'];
          	    if(is_numeric($result['reg_telephone']))
                {
          	      $result['country_code']     = $this->request->post['country_code'];
          	      $result['country_iso_code'] = $this->request->post['country_iso_code'];
          	    }
          	}
          	else
          	{
          		$result['reg_telephone']     = $_COOKIE['customer_mobile'];
                $result['redirect_cart']     = $this->request->post['redirect_cart'];
                if(is_numeric($result['reg_telephone']))
                {
          	      $result['country_code']      = $_COOKIE['country_code'];
                  $result['country_iso_code']  = $_COOKIE['country_iso_code'];
          	    }
          	}

          	$result['otp']  = $this->request->post['otp'];

          	$check_data = $this->model_account_customer->checkOTP($result);
          	
          	if(count($check_data) == 0)
            {
              $check_data = $this->model_account_customer->checkMasterOTP($result);
            }

            if(count($check_data) > 0)
            {
               $this->model_account_customer->verifyOTP($check_data);
               if($check_data['otp_page'] == 'forgot_password')
               {  
               	  $access_token  = $this->generate_access_token();
               	  if($this->customer->login($result['reg_telephone'], $result['otp'], 1, false, $access_token))
               	  {
                    $redirect_url    =  $this->login_process();
                    $result['url']   =  $redirect_url;
                    $result['msg']   =  $this->language->get('text_login_success');
                    $result['error'] = 0;
               	  }
               }
               else
               {
                  setcookie('customer_mobile', $result['reg_telephone'], time() + (86400 * 7), "/");
                  setcookie('register_user', 0, time() + (86400 * 7), "/");

                 if(is_numeric($result['reg_telephone']))
                 {
			      setcookie('country_code', $result['country_code'], time() + (86400 * 7), "/");
			      setcookie('country_iso_code', $result['country_iso_code'], time() + (86400 * 7), "/");
			      $result['msg']   = sprintf($this->language->get('otp_verify'), $result['reg_telephone']);
                  $result['error'] = 0;
			     }
			     else
			     {
			      setcookie('country_code', '', time() - 86400, "/");
			      setcookie('country_iso_code', '', time()-86400, "/");
			      $result['msg']   = sprintf($this->language->get('otp_verify_email'), $result['reg_telephone']);
                  $result['error'] = 0;
			     }
                   
               }

            }
            else
            {
            	$result['msg']   = 'Please enter a valid OTP';
            	$result['error'] = 1;
            }

	    }
	 }
        header('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));

    }




	public function email_otp_verify()
	{

	  $ajax = new ajax;
	  $ajax->HTTP_ORIGIN();
      $result = array('error'=>1);
      $this->load->language('account/login');
      $this->load->model('account/customer');
	  if(isset($this->request->request['otp']) && !empty($this->request->request['otp']))
		{
			$otp_data = base64_decode($this->request->request['otp']);
			$otp_data = explode(" ", $otp_data);
            $request_data['reg_telephone'] = $otp_data[0];
            $request_data['otp_page']      = $otp_data[1];
            $request_data['otp']           = $otp_data[2];
          	$check_data     = $this->model_account_customer->checkOTP($request_data);
            if(count($check_data) > 0)
            { 
               $this->model_account_customer->verifyOTP($check_data);
               if($check_data['otp_page'] == 'forgot_password')
               {
               	  $access_token  = $this->generate_access_token();
               	  if($this->customer->login($check_data['email'], $otp, 1, false, $access_token))
               	  {
                    $redirect_url    =  $this->login_process();
                     $this->response->redirect($redirect_url);
               	  }
               }
               else
               {
                  setcookie('customer_mobile', $check_data['email'], time() + (86400 * 7), "/");
                  setcookie('country_code', '', time() - 86400, "/");
			      setcookie('country_iso_code', '', time()-86400, "/");
			      $this->session->data['otp_verify_success'] = 1;
               }

            }
            else
            {
               $this->session->data['otp_verify_success'] = 0;
            }

	    }

	     $this->response->redirect($this->url->link('common/home', '', 'SSL'));

    }


    public function pincodeAddress()
    {
       $ajax = new ajax;
	   $ajax->HTTP_ORIGIN();

        $response_data = array();
        $this->load->form('register_form');
        $RegisterForm = new RegisterForm();
       	$postcodeValidation = $RegisterForm->postcodeValidation($this->request->post);
		if ($postcodeValidation['valid'])
		{

            $request['pincode'] = $this->request->post['postcode'];
            $data_json = json_encode($request);

            $api_url = "https://www.wholesalebox.in/index.php?route=restapi/lookup/pincode";
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
            $data = json_decode($result, true );


           $city = isset($data[0]['city']) ? $data[0]['city']:'';
           $state = isset($data[0]['zone']) ? $data[0]['zone']:'--- Please Select ---';
           $country = isset($data[0]['country']) ? $data[0]['country']:'';
           $country_id = isset($data[0]['country_id']) ? $data[0]['country_id']:99;;
           $zone_id = isset($data[0]['zone_id']) ? $data[0]['zone_id']:'';

           $response_data['error']      = 0;
           $response_data['country_id'] = $country_id;
           $response_data['zone_id']    = $zone_id;
           $response_data['city']       = $city;
           $response_data['state']      = $state;
           $response_data['country']    = $country;

       }
       else
       {
       		$response_data['msg']   = $postcodeValidation['errors']['message'];
		    $response_data['error'] = 1;
       }

        header('Content-Type: application/json');
        $this->response->setOutput(json_encode($response_data));
   }


    public function state_list()
    {
    	$ajax = new ajax;
	    $ajax->HTTP_ORIGIN();

        $response_data = array();
        $this->load->model('localisation/zone');
        $this->load->language('account/login');
        if(isset($this->request->post['country_id']))
        {
          $request['country_id']    = $this->request->post['country_id'];
          $zones   = $this->model_localisation_zone->getZonesByCountryId($request['country_id']);
          $response_data['zone_title'] =  $this->language->get('entry_zone');
          $response_data['zone_data']  = $zones;
        }

        header('Content-Type: application/json');
        $this->response->setOutput(json_encode($response_data));

   }

    private function send_mail_or_sms($email, $message, $subject, $password)
    {
    	//echo $message;
        $this->load->language('account/sms_templates');
        $this->load->language('account/forgotten');
        if (is_numeric($email) && SITE_ENVIRONMENT =='Production')
        {
            $send_sms = new SMS($message, $email);
            $send_sms->sendOTPMessage();
        } else {

		    $mail = new PHPMailer();
			$mail->isSMTP();
			$mail->Host = $this->config->get('config_mail_smtp_hostname');
			$mail->Port = $this->config->get('config_mail_smtp_port');
			$mail->SMTPSecure = 'ssl';
		    $mail->SMTPDebug = 0;
			$mail->Debugoutput = 'html';
			$mail->SMTPAuth = true;
			$mail->Username = $this->config->get('config_mail_smtp_username');
			$mail->Password = $this->config->get('config_mail_smtp_password');
			$mail->setFrom($this->config->get('config_email'), 'WholesaleBox');
			$mail->addAddress($email, "WholesaleBox");
			$mail->Subject =  html_entity_decode("OTP", ENT_QUOTES, 'UTF-8');
			$mail->msgHTML($message);
			$mail->send();

        }
    }

   public function generate_access_token(){
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
