 <?php
     require_once('system.php');
     require_once( DIR_CATALOG . 'form/register_form.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );

    class Login_mobileController extends SystemController
    {
        private $error = array();

        public function __construct($params) {

            parent::__construct($params);

            $mobile_country_code = array('CO'=>array('AE'=>'+971','AU'=>'+61','BD'=>'+880','CA'=>'+1','IN'=>'+91','MY'=>'+60','OM'=>'+968','SA'=>'+966','GB'=>'+44'), 'IN'=>array('IN'=>'+91'));

            $this->registry->set('mobile_country_code', $mobile_country_code);
        }
        /**
         * customer login
         */

  public function send_otp()
  {
      $result = array('error'=>1);
      $this->load->language('account/sms_templates');
      $this->load->language('account/login');
      $this->load->model('account/customer');

      $config = array();
      $config['config_store_id'] = $this->config->get('config_store_id');
      $RegisterForm = new RegisterForm($config);

     if(isset($this->request['reg_telephone']))
     {
        $mobileValidation = $RegisterForm->mobileValidation($this->request);
        if ($mobileValidation['valid'])
         {
            $result['reg_telephone']      =  preg_replace('/\s+/', '', $this->request['reg_telephone']);
            $result['country_code']       =  preg_replace('/\s+/', '', $this->request['country_code']);
            $result['country_iso_code']   =  preg_replace('/\s+/', '',  $this->request['country_iso_code']);
            $result['user_verify']        = 0;
            $user_data = $this->model_account_customer->getCustomerByEmailOrMobile($result['reg_telephone']);
           if(count($user_data) == 0)
            {
              $result['new_user'] = 1;
              
              //remove otp verify for .co
              if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
               {
                 $this->request['user_verify'] = 1;
               }
              //end 

              if($this->request['user_verify'] == 0)
               {
                  $result['session_id']    = session_id();
                  $result['ip']            = $this->getIpAddress;
                  $result['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                  $result['otp']           = (SITE_ENVIRONMENT =='Production')?rand(1000, 9999):1010;
                  $result['otp_page']      = 'sign_up';
                  $result['customer_id']   = 0;
                  $user_otp = $this->model_account_customer->addOTP($result);
                  if($user_otp > 0) { $result['otp'] = $user_otp; }
                    if(is_numeric($result['reg_telephone']))
                      {
                        if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
                         {
                             $message = $this->language->get('on_app_signup_co');
                         }
                        else
                         {
                             $message = $this->language->get('on_app_signup');
                         }   

                         $message   = sprintf($message,$result['otp']);
                         $this->send_mail_or_sms($result['country_code'].$result['reg_telephone'], $message, '', '');
                       }
                    else
                       {

                          if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
                          {
                             $message=$this->language->get('on_app_signup_email_co');
                          }
                         else
                          {
                             $message =$this->language->get('on_app_signup_email');
                          } 
                          
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
             $result['cart_session_id'] =  $this->generate_access_token();
             
           if(is_numeric($result['reg_telephone']))
           {
             if($user_data['mobile_country_code'] != '')
             {
               $result['country_code']     = $user_data['mobile_country_code'];
               $result['country_iso_code'] = array_search('+'.$result['country_code'], $this->mobile_country_code["CO"]);
             }
           }
           
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
          $result['error'] = 0;
          $result['msg']   = sprintf($this->language->get('otp_success'), $result['reg_telephone']);
        }
                  
      }
      else
      {
         $result['msg']   = $mobileValidation['errors']['message'];
         $result['error'] = 1;
      }

     }

      $this->data_packet->data       = $result;
      $this->data_packet->message    = 'data fatch successfully';
      $this->data_packet->statusCode = 200;
      return $this->data_packet;

    }



  public function verify_otp()
  {
      $result = array('error'=>1);

      $this->load->language('account/login');
      $this->load->model('account/customer');
      $this->load->model('account/address');
      $customer = new Customer($this->registry);
       $this->registry->set('customer', $customer);
       
      if(isset($this->request['otp']))
      {
            $result['otp_page']        = $this->request['otp_page'];
            $result['reg_telephone']   = $this->request['reg_telephone'];
            $result['redirect_cart']   = $this->request['redirect_cart'];
            if(is_numeric($result['reg_telephone']))
             {
               $result['country_code']     = $this->request['country_code'];
               $result['country_iso_code'] = $this->request['country_iso_code'];
             }

             $result['otp']  = $this->request['otp'];
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
                  $customer_id = $this->customer->login($result['reg_telephone'], $result['otp'], 1, false, $access_token);
                  if($customer_id > 0)
                  {
                    $redirect_url              =  $this->login_process();
                    $result['customer_access_token'] =  $access_token;

                    $address_data = $this->model_account_address->getAddress($this->customer->getAddressId(), $this->customer->getId());
                    $result['msg']   = $this->language->get('text_login_success');
                    $result['error'] = 0;
                    $result['url']   = $redirect_url;
                    $result['customer_id']   = $this->customer->getId();
                    $result['first_name']    = $this->customer->getFirstName();
                    $result['last_name']     = $this->customer->getLastName();
                    $result['email']         = $this->customer->getEmail();
                    $result['telephone']     = $this->customer->getTelephone();
                    $result['is_dropshipper'] = $this->customer->getIsDropshipper();
                    $result['gst_number']     = $this->customer->getGSTNumber();
                    $result['self_order']     = $this->customer->getSelfOrder();
                    $result['has_website']    = $this->customer->getHasWebsite();
                    $result['user_city']      = $address_data['city'];
                    $result['pincode']        = $address_data['postcode'];
                    $result['customer_type'] = $this->model_account_customer->getCustomerType($this->customer->getId());
                    $result['membership'] = $this->model_account_customer->checkCustomerHasMembership($this->customer->getMasterId());
                    $result['credit_application'] = $this->url->link( 'account/credit_application','','SSL' );
                  }
               }
               else
               {
                 $result['cart_session_id'] =  $this->generate_access_token();
                 if(is_numeric($result['reg_telephone']))
                 {
                   $result['msg']   = sprintf($this->language->get('otp_verify'), $result['reg_telephone']);
                   $result['error'] = 0;
                 }
                else
                 {
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

      $this->data_packet->data       = $result;
      $this->data_packet->message    = 'data fatch successfully';
      $this->data_packet->statusCode = 200;
      return $this->data_packet;
    }


  public function send_password_otp()
  {
      $result = array('error'=>1);

      $this->load->language('account/sms_templates');
      $this->load->language('account/login');
      $this->load->model('account/customer');

    if(isset($this->request['customer_mobile']) && !empty($this->request['customer_mobile']))
      {
            $result['reg_telephone'] = $this->request['customer_mobile'];
            if(!empty($this->request['country_code'])) { $result['country_code']  = $this->request['country_code']; }
            else { $result['country_code']  = ''; }
            
            $user_data = $this->model_account_customer->getCustomerByEmailOrMobile($result['reg_telephone']);

            if(count($user_data) > 0)
            {
                $result['session_id']    = session_id();
                $result['ip']            = $this->getIpAddress;
                $result['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                $result['otp']           = (SITE_ENVIRONMENT =='Production')?rand(1000, 9999):1010;
                $result['otp_page']      = 'forgot_password';
                $result['customer_id']   = $user_data['customer_id'];
                $user_otp = $this->model_account_customer->addOTP($result);
                if($user_otp > 0) { $result['otp'] = $user_otp; }

                     if(is_numeric($result['reg_telephone']))
                      {

                        if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
                         {
                             $message = $this->language->get('on_app_signup_co');
                         }
                        else
                         {
                             $message = $this->language->get('on_app_signup');
                         } 

                         $message   = sprintf($message,$result['otp']);
                         $this->send_mail_or_sms($result['country_code'].$result['reg_telephone'], $message, '', '');
                      }
                     else
                       {

                          if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
                          {
                             $message = $this->language->get('on_app_signup_email_co');
                          }
                         else
                          {
                             $message = $this->language->get('on_app_signup_email');
                          }

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
      else
      {
        $result['msg']   = 'Cookie not created';
      }

     $this->data_packet->data       = $result;
     $this->data_packet->message    = 'data fatch successfully';
     $this->data_packet->statusCode = 200;
      return $this->data_packet;
    }


  public function login()
  {
        $result = array('error'=>1);
        $this->load->language('account/login');
        $this->load->model('account/customer');
        $this->load->model('account/address');
        $customer = new Customer($this->registry);
        $this->registry->set('customer', $customer);
            //echo "<pre>"; print_r($this->request); die;         
     
        $this->request['reg_telephone']    = $this->request['reg_telephone'];
        $this->request['customer_access_token'] =  $this->generate_access_token();
        $result['redirect_cart']           = $this->request['redirect_cart'];
        $result['pre_order']               = $this->request['pre_order'];

        $redirect_url = '';
         if (($this->method == 'POST') && $this->loginValidation())
         {
               $redirect_url =  $this->login_process();
               $result['msg']   = $this->language->get('text_login_success');
               $result['error'] = 0;
               $result['url']   = $redirect_url;
               $result['customer_access_token'] =  $this->request['customer_access_token'];

              $address_data = $this->model_account_address->getAddress($this->customer->getAddressId(), $this->customer->getId());
                    $result['customer_id']   = $this->customer->getId();
                    $result['first_name']    = $this->customer->getFirstName();
                    $result['last_name']     = $this->customer->getLastName();
                    $result['email']         = $this->customer->getEmail();
                    $result['telephone']     = $this->customer->getTelephone();
                    $result['is_dropshipper'] = $this->customer->getIsDropshipper();
                    $result['gst_number']     = $this->customer->getGSTNumber();
                    $result['self_order']     = $this->customer->getSelfOrder();
                    $result['has_website']    = $this->customer->getHasWebsite();
                    $result['user_city']      = $address_data['city'];
                    $result['pincode']        = $address_data['postcode'];
                    $result['customer_type'] = $this->model_account_customer->getCustomerType($this->customer->getId());
                    $result['membership'] = $this->model_account_customer->checkCustomerHasMembership($this->customer->getMasterId());
                    $result['credit_application'] = $this->url->link( 'account/credit_application','','SSL' );
         }
         else 
         {
            $result['msg']   = $this->error['warning'];
            $result['url']   = $redirect_url;
         }

      $this->data_packet->data       = $result;
      $this->data_packet->message    = 'data fatch successfully';
      $this->data_packet->statusCode = 200;
      return $this->data_packet;

    }


  public function register()
  {
     $result = array('error'=>1);
     $this->load->language('account/login');
     $this->load->model('account/customer');
      $this->load->model('account/address');
     
     $customer = new Customer($this->registry);
     $this->registry->set('customer', $customer);
     $event = new Event($this->registry);
     $this->registry->set('event', $event);
     
     $config = array();
     $config['config_store_id'] = $this->config->get('config_store_id');
     $RegisterForm = new RegisterForm($config);
   
    if($this->method == 'POST')
    {

      if(!empty($this->request['reg_telephone']) && is_numeric($this->request['reg_telephone']))
        { 
          $this->request['mobile_country_code']     = $this->request['country_code'];
        }
       else
       {
        $this->request['mobile_country_code'] = '';
       } 

      $this->request['address_telephone']       = $this->request['reg_telephone']; 
      $redirect_url                             = '';
      $arr_name                                 = explode(" ", $this->request['name']);
      $this->request['firstname']               = $arr_name[0];
      $result['redirect_cart']                  =  $this->request['redirect_cart'];

      if(isset($arr_name[1])) {
          $this->request['lastname'] = $arr_name[1];
        }else{
          $this->request['lastname'] = '';
       }

      if(isset($this->request['customer_type_id']))
        {
           $this->request['customer_type_id'] = implode(",", $this->request['customer_type_id']);
        }
      else
        {
          $this->request['customer_type_id'] = '';
        }


      if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
      {
         $registerValidation = $RegisterForm->registerValidationCo($this->request); 
      }
      else
      {
        $registerValidation = $RegisterForm->registerValidation($this->request); 
      }


      if ($registerValidation['valid'] && $this->registerCustomValidation())
      { 
          
        $customer_id = $this->model_account_customer->addCustomer($this->request);
      
      if($customer_id > 0)
       {  
         $this->request['customer_access_token'] = $this->generate_access_token();
         $this->load->language('account/sms_templates');
          $message = $this->language->get('on_normal_signup');
          $msg = sprintf($message,$this->request['reg_telephone'],$this->request['password']);
          $send_sms = new SMS($msg, $this->request['reg_telephone']);
          $send_sms->sendMessage();

        $this->load->model('account/address');
        $this->request['address_2'] = '';
        $address_id = $this->model_account_address->addAddress($this->request, $customer_id);

        if(isset($_COOKIE['session_id']) && isset($_COOKIE['utm_source'])){
          $this->load->model('tracking/tracking');
          $this->model_tracking_tracking->update_customer_id($customer_id,$mobile);
        }
        // Clear any previous login attempts for unregistered accounts.
        $this->model_account_customer->deleteLoginAttempts($this->request['reg_email']);

         if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
        {
          $this->customer->login($this->request['reg_email'], '', 1, false, $this->request['customer_access_token']);
        }
        else
        {
          $this->customer->login($this->request['reg_telephone'], '', 1, false, $this->request['customer_access_token']);
        }
        

        unset($this->session->data['guest']);

        /*$this->load->model('account/activity');
        $activity_data = array(
            'customer_id' => $customer_id,
            'name'        => $this->request['firstname'] . ' ' . $this->request['lastname']
        );
        $this->model_account_activity->addActivity('register', $activity_data);*/

        if($this->request['referrers']=='cart'){
          $redirect_url = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }else{
          $redirect_url = $this->url->link('account/success');
        }
      }
       else
       {
        $this->error['warning'] = 'Registration failed. Internal server error, please try again';
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
        $result['customer_access_token'] =  $this->request['customer_access_token'];

        $address_data = $this->model_account_address->getAddress($this->customer->getAddressId(), $this->customer->getId());

          $result['customer_id']   = $this->customer->getId();
          $result['first_name']    = $this->customer->getFirstName();
          $result['last_name']     = $this->customer->getLastName();
          $result['email']         = $this->customer->getEmail();
          $result['telephone']     = $this->customer->getTelephone();
          $result['is_dropshipper'] = $this->customer->getIsDropshipper();
          $result['gst_number']     = $this->customer->getGSTNumber();
          $result['self_order']     = $this->customer->getSelfOrder();
          $result['has_website']    = $this->customer->getHasWebsite();
          $result['user_city']      = $address_data['city'];
          $result['pincode']        = $address_data['postcode'];
          $result['customer_type'] = $this->model_account_customer->getCustomerType($this->customer->getId());
          $result['membership'] = $this->model_account_customer->checkCustomerHasMembership($this->customer->getMasterId());
          $result['credit_application'] = $this->url->link( 'account/credit_application','','SSL' );

        }
    }
    else
    {
      $result['msg'] ='Invalid value';
    }

      $this->data_packet->data       = $result;
      $this->data_packet->message    = 'data fatch successfully';
      $this->data_packet->statusCode = 200;
      return $this->data_packet;

    }


  public function loginValidation()
  {
     $event = new Event($this->registry);
     $this->registry->set('event', $event);

     $this->event->trigger('pre.customer.login');
     if (!$this->customer->login($this->request['reg_telephone'], $this->request['password'], false, false, $this->request['customer_access_token']))
      {
        $this->error['warning'] = $this->language->get('error_login');
        if(is_numeric($this->request['reg_telephone']))
        {
         $this->model_account_customer->addMobileLoginAttempt($this->request['reg_telephone']);
         }
         else
         {
          $this->model_account_customer->addLoginAttempt($this->request['reg_telephone']);
         }
      }
      else
      {
         if(is_numeric($this->request['reg_telephone']))
         {
         $this->model_account_customer->deleteMobileLoginAttempts($this->request['reg_telephone']);
         }
         else
         {
          $this->model_account_customer->deleteLoginAttempts($this->request['reg_telephone']);
         }

        $this->event->trigger('post.customer.login');
        $this->load->model('lead/lead');
        $telephone = $this->customer->getTelephone();
        if (!empty($telephone)) {
          $lead_data = [
            'last_login_date' => date('Y-m-d H:i:s')
          ];
          $this->model_lead_lead->updateLead($lead_data, $telephone, 'Last Login', $this->customer->getId() );
        }
      }
    return !$this->error;
  }


  public function registerCustomValidation()
  {   

    if (!empty($this->request['reg_email']) && $this->model_account_customer->getTotalCustomersByEmailToValidate($this->request['reg_email']))
     {
       $this->error['warning'] = $this->language->get('error_exists');
     }

    if($this->config->get('config_store_id') != INTERNATIONAL_STORE_ID || !empty($this->request['reg_telephone']))
    {
      if ($this->model_account_customer->getTotalCustomersByTelephone($this->request['reg_telephone']))
       {
         $this->error['warning'] = $this->language->get('error_exists_phone');
       }
    }   
     
    if(isset($this->request['gst_uin_number']) && $this->request['gst_uin_number'] == 1) 
    { 
      if ($this->model_account_customer->getTotalCustomersByGst($this->request['gst_number']))
      {
       $this->error['warning'] = $this->language->get('error_exists_gst');
      }
    }       

    // Agree to terms
    if ($this->config->get('config_account_id'))
    {
      $this->load->model('catalog/information');
      $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
      if ($information_info && !isset($this->request['agree'])) {
        $this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
      }
    }

    return !$this->error;
  }



  private function login_process()
   {
    unset($this->session->data['guest']);
    $this->load->model('account/address');
    if ($this->config->get('config_tax_customer') == 'payment') {
        $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
    }
    if ($this->config->get('config_tax_customer') == 'shipping') {
            $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
    }
   }
}
