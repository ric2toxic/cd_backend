<?php
     require_once('system.php');
     require_once( DIR_CATALOG . 'form/register_form.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );
     require_once(DIR_SYSTEM . 'library/ajax.php');

    class LoginController extends SystemController
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
              $user_verify = array();

              if(isset($_COOKIE['customer_mobile']) && $_COOKIE['customer_mobile']==$result['reg_telephone'])
                {
                 $user_verify = $this->model_account_customer->checkUserIsVerify($_COOKIE['customer_mobile']);
                }
               
               //remove otp verify for .co
               if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
               {
                 $user_verify = array('success'=>1);
               }  
                //end

              if(count($user_verify) == 0)
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
                             $message = $this->language->get('on_app_signup_email_co');
                          }
                         else
                          {
                             $message   = $this->language->get('on_app_signup_email');
                          } 
                          
                          $otp_email = base64_encode($result['reg_telephone'].' '.$result['otp_page'].' '.$result['otp']);
                          $verify_link = $this->url->link('common/login_popup/email_otp_verify', '', 'SSL').'&otp='.$otp_email;
                          $message   = sprintf($message,$result['otp'], $verify_link);
                           $this->send_mail_or_sms($result['reg_telephone'], $message, 'OTP', $result['otp']);
                       }
               }
               else 
                {
                  $result['user_verify'] = 1; 
                  setcookie('customer_mobile', $result['reg_telephone'], time() + (86400 * 7), "/"); 
                }
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

    if(isset($_COOKIE['customer_mobile']) && !empty($_COOKIE['customer_mobile']))
      {
            $result['reg_telephone'] = $_COOKIE['customer_mobile'];
            if(!empty($_COOKIE['country_code'])) { $result['country_code']  = $_COOKIE['country_code']; }
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
            $result['otp_page']    = $this->request['otp_page'];
            if($result['otp_page'] == 'sign_up')
            {
                $result['reg_telephone']      = $this->request['reg_telephone'];
                $result['redirect_cart']    = $this->request['redirect_cart'];
                if(is_numeric($result['reg_telephone']))
                {
                  $result['country_code']     = $this->request['country_code'];
                  $result['country_iso_code'] = $this->request['country_iso_code'];
                }
            }
            else
            {
                $result['reg_telephone']     = $_COOKIE['customer_mobile'];
                $result['redirect_cart']     = $this->request['redirect_cart'];
                if(is_numeric($result['reg_telephone']))
                {
                   if(isset($_COOKIE['country_code'])) { $result['country_code']= $_COOKIE['country_code']; } 
                   else { $result['country_code']=''; }
                   if(isset($_COOKIE['country_iso_code'])) { $result['country_iso_code']= $_COOKIE['country_iso_code']; }
                   else { $result['country_code']=''; }
                }
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
                  $this->request['customer_access_token'] =  $this->generate_access_token();
                  if($this->customer->login($result['reg_telephone'], $result['otp'], 1, false, $this->request['customer_access_token']))
                  {
                    $redirect_url            =  $this->login_process();
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
      else
      {
        $result['msg']   = 'Please enter OTP';
        $result['error'] = 1;
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
     
        $this->request['reg_telephone'] = $_COOKIE['customer_mobile'];
        $result['redirect_cart'] = $this->request['redirect_cart'];
        $result['pre_order'] = $this->request['pre_order'];

        $this->request['customer_access_token'] =  $this->generate_access_token();

        $redirect_url = '';
         if (($this->method == 'POST') && $this->loginValidation())
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
   
    if(($this->method == 'POST') && isset($_COOKIE['customer_mobile']))
    {
       if(is_numeric($_COOKIE['customer_mobile']))
        { 
          $this->request['reg_telephone']           = $_COOKIE['customer_mobile'];
          $this->request['mobile_country_code']     = $_COOKIE['country_code'];
          $this->request['address_telephone']       = $_COOKIE['customer_mobile'];
        }
        else
        {
           $this->request['reg_email'] = $_COOKIE['customer_mobile'];
           $this->request['mobile_country_code']  = '';
           $this->request['address_telephone']    = $this->request['reg_telephone'];
        }

     
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
        //$this->model_account_customer->deleteLoginAttempts($this->request['reg_email']);

        if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
        {
          $this->customer->login($this->request['reg_email'], '', 1, false, $this->request['customer_access_token']);
        }
        else
        {
          $this->customer->login($this->request['reg_telephone'], '', 1, false, $this->request['customer_access_token']);
        }  

        unset($this->session->data['guest']);

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
        /*if(is_numeric($this->request['reg_telephone']))
        {
         $this->model_account_customer->addMobileLoginAttempt($this->request['reg_telephone']);
         }
         else
         {
          $this->model_account_customer->addLoginAttempt($this->request['reg_telephone']);
         }*/
      }
      else
      {
         /*if(is_numeric($this->request['reg_telephone']))
         {
         $this->model_account_customer->deleteMobileLoginAttempts($this->request['reg_telephone']);
         }
         else
         {
          $this->model_account_customer->deleteLoginAttempts($this->request['reg_telephone']);
         }*/

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
    
      /*** Validate GST Number from GST library ***/
      if(!empty($this->request['gst_uin_number'])) {
          $gstObject = new GST($this->registry);
          $customer_id = 0;
          $seller_duplicacy_check = true;
          $current_customer = array(
                                  'customer_id' => 0,
                                  'firstname' => $this->request['firstname'],
                                  'telephone' => $this->request['reg_telephone'],
                                  'email' => $this->request['reg_email'],
                                  'master_id' => 0
                              );
          $gst_number  = $this->request['gst_number'] ?? '';
          $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
          $gst_number  = trim($gst_number);
          
          $valid_gst_result = $gstObject->validateGSTNumber($gst_number, $customer_id, $seller_duplicacy_check, $current_customer);

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
              //     $this->error['warning'] = sprintf($this->language->get('error_exists_gst'), $this->request['gst_number'], $user_str, $this->url->link('information/contact') );
              // }
          }
      }
      /*** End: GST Number Validation ***/     
      

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
    if ($this->config->get('config_tax_customer') == 'payment')
     {
        $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
      }
    if ($this->config->get('config_tax_customer') == 'shipping')
      {
      $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
      }

    if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false))
      {
        $redirect_url = str_replace('&amp;', '&', $this->request->post['redirect']);
      }
      else {
      if (!empty($this->request['referrers']) && $this->request['referrers']=='cart')
         {
          $redirect_url = $this->url->link('checkout/one_page_checkout', '', 'SSL');
        }
        else {
                    if (!CONFIG_IS_MOBILE && $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId()))
                    {
                      $customer_id   = $this->customer->getId();
                      $access_token  = $this->customer->getAccessToken();
                      $seller_status = $this->MsLoader->MsSeller->getStatus();
                      $customer_mobile = ($this->customer->getTelephone()) ? $this->customer->getTelephone() : $this->customer->getEmail();
                      
                      $this->customer->logout();

                      $this->model_account_customer->setSellerToken($customer_id, $access_token, $customer_mobile);

                      if ($seller_status == MsSeller::STATUS_ACTIVE)
                           {
                              $redirect_url =  HTTPS_SERVER.'seller_panel/#/orders/getPickpupOrderRequested/';
                            }
                      else if($seller_status == MsSeller::STATUS_INACTIVE)
                        {
                          $redirect_url =  HTTPS_SERVER.'seller_panel/#/profile/';
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
    public function pincodeAddress()
    {
        $response_data = array();
        $RegisterForm = new RegisterForm();
        $postcodeValidation = $RegisterForm->postcodeValidation($this->request);
    if ($postcodeValidation['valid'])
    {
            $request['pincode'] = $this->request['postcode'];
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

      $this->data_packet->data       = $response_data;
      $this->data_packet->message    = 'data fatch successfully';
      $this->data_packet->statusCode = 200;
      return $this->data_packet;

   }


    public function customer_type()
    {
        $response_data = array();
        $this->load->model('account/customer');
        $response_data['CustomersType'] = $this->model_account_customer->getCustomersType();
        $response_data['CustomersDropshipperType'] = $this->model_account_customer->getCustomersDropshipperType();
        $this->data_packet->data       = $response_data;
        $this->data_packet->message    = 'data fatch successfully';
        $this->data_packet->statusCode = 200;
        return $this->data_packet;
   }

    public function state_list()
    {
        $response_data = array();
        $this->load->model('localisation/zone');
        $this->load->language('account/login');
        $cache = new Cache('file');
        $this->registry->set('cache', $cache);

        if(isset($this->request['country_id']))
        {
          $zones   = $this->model_localisation_zone->getZonesByCountryId($this->request['country_id']);
          $response_data['zone_title'] =  $this->language->get('entry_zone');
          $response_data['zone_data']  = $zones;
        }

       $this->data_packet->data       = $response_data;
       $this->data_packet->message    = 'data fatch successfully';
       $this->data_packet->statusCode = 200;
       return $this->data_packet;
   }


    public function country_list()
    {
        $response_data = array();
        $this->load->model('localisation/country');
        $this->load->language('account/login');

        $country   = $this->model_localisation_country->getCountries();
        $response_data['country_title'] =  $this->language->get('entry_country');
        $response_data['country_data']  = $country; 

       $this->data_packet->data       = $response_data;
       $this->data_packet->message    = 'data fatch successfully';
       $this->data_packet->statusCode = 200;
       return $this->data_packet;
   }


}
