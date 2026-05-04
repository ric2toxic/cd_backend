<?php
     require_once('system.php');
     require_once( DIR_CATALOG . 'form/register_form.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );

    class HeaderController extends SystemController
    {
        private $error = array();

        public function __construct($params) {

            parent::__construct($params);
        }
        /**
         * customer login
         */

       public function userlogin() 
        {
          $data['logout']    = $this->url->link('account/logout', '', 'SSL');
           if($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId()))
            {
               $data['logged'] = $this->customer->isLogged();
               $this->load->model('seller_panel/profile');
               $nickname = $this->model_seller_panel_profile->getSellersInformation($this->customer->getId(),'company');
               $data['cust_name'] = $nickname['company'];
               //$data['profile_status'] = ($seller_status == 1) ? 1 : 0 ;
               $data['SELLER_AGREEMENT_POPUP'] = SELLER_AGREEMENT_POPUP;

               $data['AGREEMENT_CONSENT_POPUP'] = $this->model_seller_panel_profile->getSellersAgreementConsent($this->customer->getId());
            }
            else
            {
               $this->customer->logout();
            }


         $this->data_packet->data        = $data;
         $this->data_packet->statusCode  = 200;
         return $this->data_packet;

        }

  public function saveAgreementConsent()
  {
     $this->load->model('seller_panel/profile');
     $declaration = $this->request['declaration'];
     $data = $this->model_seller_panel_profile->saveSellersAgreementConsent($this->customer->getId(), $declaration);
     $this->data_packet->data        = $data;
     $this->data_packet->statusCode  = 200;
     return $this->data_packet;

  }      

  public function login()
  {     
        $result = array('error'=>1);
        $this->load->language('account/login');
        $this->load->model('account/customer');     

        $this->request['customer_access_token'] =  $this->generate_access_token();

        $redirect_url = '';
         if ($customer_id = $this->loginValidation())
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
            $result['customer_access_token'] =  $this->request['customer_access_token'];
            $result['customer_id']     =  $customer_id;
         }


         $this->data_packet->data        = $result;
         $this->data_packet->statusCode  = 200;
         return $this->data_packet;

    } 

  public function register() {
    $this->language->load('multiseller/multiseller');
    $this->load->model('account/customer');
    $data = $this->request;

    $json = array();

    $data['reg_telephone'] = $data['seller_telephone'];
    $data['reg_email']     = $data['seller_email'];
    $data['name']          = $data['seller_name'];

    $arr_name = explode(" ", $data['name']);
    $data['firstname'] = $arr_name[0];
    if(isset($arr_name[1])) {
      $data['lastname'] = $arr_name[1];
    }else{
      $data['lastname'] = '';
    }

    if ((utf8_strlen($data['name']) < 1) || (utf8_strlen($data['name']) > 32)) {
          $json['errors'] = $this->language->get('error_name');
      }
   
    if (!preg_match('/^[0-9]*$/', $data['reg_telephone']) || (utf8_strlen($data['reg_telephone']) < 10) || (utf8_strlen($data['reg_telephone']) > 10)) {
      $json['errors'] = $this->language->get('error_telephone'); 
    }
    
    if (!empty($data['reg_telephone']) && $this->MsLoader->MsSeller->getTotalSellersByTelephone($data['reg_telephone'])) {
      $json['errors'] = $this->language->get('ms_error_sellerinfo_exists_phone');
    }
    
    if ((utf8_strlen($data['reg_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['reg_email'])) {
          $json['errors'] = $this->language->get('error_email');
      } else {
  
      if ($this->MsLoader->MsSeller->getTotalSellersByEmail($data['reg_email'])) {
         $json['errors'] = $this->language->get('ms_error_sellerinfo_error_email');
        }
    }

    if ((utf8_strlen($data['password']) < 4) || (utf8_strlen($data['password']) > 20)) {
      $json['errors'] = $this->language->get('error_password');
    }

    if ($data['password_confirm'] != $data['password']) {
      $json['errors'] = $this->language->get('error_confirm');
    }

    if ($this->config->get('msconf_seller_terms_page')) {
      $this->load->model('catalog/information');
      $information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

      if ($information_info && !isset($data['terms'])) {
        $json['errors'] = htmlspecialchars_decode(sprintf($this->language->get('ms_error_sellerinfo_terms'), $information_info['title']));
      }
    }

    if (empty($json['errors'])) {
      $data['customer_access_token'] =  $this->generate_access_token();
      // Check if seller has buyer account
      $checkCustmrExsist = $this->model_account_customer->getCustomerByEmail($data['reg_email']);
      if(is_array($checkCustmrExsist) && !empty($checkCustmrExsist)){
        // update group id/telephone number/password  for seller
        $this->model_account_customer->updateCustomerToSeller($checkCustmrExsist['customer_id'], $data);
      }
      else {
      
        // Create buyer account
        //$data['seller']['telephone'] = '';
        $data['is_dropshipper'] = 0;
        $data['nickname'] = '';
        $data['company'] = '';
        $data['address_1'] = '';
        $data['address_2'] = '';
        $data['city'] = '';
        $data['postcode'] = '';
        $data['country_id'] = 0;
        $data['zone_id'] = 0;

        // according to mahaveer  (adding by vikas, 2017)
        $data['mobile_country_code'] = '';
        $data['company'] = '';
        $data['customer_type_id'] = '';

        $data['is_seller'] = true;
        $customer_id = $this->model_account_customer->addCustomer($data);
      }
      
 
      $this->model_account_customer->deleteLoginAttempts($data['reg_email']);

      $this->customer->login($data['reg_email'], $data['password']);

      unset($this->session->data['guest']);

      //Add access token
      $access_token = $this->model_account_customer->addCustomerToken($this->customer->getId());

      // Register seller
      //$data['seller']['status'] = MsSeller::STATUS_INCOMPLETE; // commented by vikas, 2017
      $data['status'] = MsSeller::STATUS_INACTIVE;
      $data['approved'] = 0;

      $data['seller_id'] = $this->customer->getId();
      $data['company'] = $data['name'];
      $this->MsLoader->MsSeller->createSeller($data);

      $this->SellerAccountEmail($data['company'], $data['reg_email']);
      //$json['redirect'] = $this->url->link('seller_panel/profile');
      $json['customer_access_token'] = $data['customer_access_token'];
      $json['customer_id']     = $this->customer->getId();
    }


      $this->data_packet->data        = $json;
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;

  }


  public function send_otp()
  {   
      $result = array('error'=>1);
      $this->load->language('account/sms_templates');
      $this->load->language('account/login');
      $this->load->model('account/customer');

     if(isset($this->request['reg_telephone']))
     {
            $result['reg_telephone']      =  preg_replace('/\s+/', '', $this->request['reg_telephone']);
             $result['user_verify']       = 0;

            $user_data = $this->model_account_customer->getCustomerByEmailOrMobile($result['reg_telephone']);
           
            if(count($user_data) > 0)
            {
                $result['session_id']    = session_id();
                $result['ip']            = $this->getIpAddress;
                $result['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                 $result['otp']          = (SITE_ENVIRONMENT =='Production')?rand(1000, 9999):1010;
                $result['otp_page']      = 'forgot_password';
                $result['customer_id']   = $user_data['customer_id'];
                $user_otp = $this->model_account_customer->addOTP($result);
                if($user_otp > 0) { $result['otp'] = $user_otp; }
                $message   = $this->language->get('on_app_signup_email');
                $verify_link = $this->url->link('common/login_popup/email_otp_verify', '', 'SSL').'&otp='.base64_encode($result['otp']);
                $message   = sprintf($message,$result['otp'], $verify_link);
                
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
                $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
                $mail->addAddress($result['reg_telephone'], "WholesaleBox");
                $mail->Subject =  html_entity_decode("OTP", ENT_QUOTES, 'UTF-8');
                $mail->msgHTML($message);
                $mail->send();

                $result['otp']   = ''; 
                $result['error'] = 0;
                $result['msg']   = sprintf($this->language->get('otp_success_email'), $result['reg_telephone']);
            }
            else
            {
               $result['msg']   = 'Email address not found in database!';
               $result['error'] = 1;      
            }
     }


      $this->data_packet->data        = $result;
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
    }

  public function verify_otp()
  {   
      $result = array('error'=>1);
      $this->load->language('account/login');
      $this->load->model('account/customer');
       
      if(isset($this->request['otp']))
      {
            $result['reg_telephone']   = $this->request['reg_telephone'];
            $result['otp']             = $this->request['otp'];
            $result['otp_page']        = "forgot_password";
             $check_data = $this->model_account_customer->checkOTP($result);
            
             if(count($check_data) == 0)
             {
              $check_data = $this->model_account_customer->checkMasterOTP($result);
             }

            if(count($check_data) > 0)
            {
               $this->model_account_customer->verifyOTP($check_data);
               $access_token  = $this->generate_access_token();
               if($customer_id = $this->customer->login($result['reg_telephone'], $result['otp'], true, false, $access_token))
               {  

                 if($this->MsLoader->MsSeller->isCustomerSeller($customer_id) && $this->MsLoader->MsSeller->getStatus() != MsSeller::STATUS_DISABLED)
                     { 
                       $redirect_url              =  $this->login_process();
                       $result['url']             =  $redirect_url;
                       $result['customer_access_token'] =  $access_token;
                       $result['customer_id']     =  $customer_id;
                       $result['msg']             =  $this->language->get('text_login_success' );
                        $result['error']           =  0;
              
                      }
                  else
                      {
                         $result['msg']   = 'Seller Account Disabled';
                         $result['error'] = 1;
                      }
                }
                else
                {
                   $result['msg']   = 'Something went wrong in login';
                   $result['error'] = 1;
                }
                      
              }         
              else
              {
                $result['msg']   = 'Please enter a valid OTP';
                $result['error'] = 1;
              }
         }

       $this->data_packet->data        = $result;
       $this->data_packet->statusCode  = 200;
       return $this->data_packet;     
    } 



  public function loginValidation()
  {  
     $event = new Event($this->registry);
     $this->registry->set('event', $event);

     $this->event->trigger('pre.customer.login');
    if($customer_id = $this->customer->login($this->request['email'], $this->request['password'], false, false, $this->request['customer_access_token']))
     { 
   
        if($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId()) && $this->MsLoader->MsSeller->getStatus() != MsSeller::STATUS_DISABLED)
        {  
           if(is_numeric($this->request['email']))
            {
            $this->model_account_customer->deleteMobileLoginAttempts($this->request['email']);
            }
           else
           {
             $this->model_account_customer->deleteLoginAttempts($this->request['email']);
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
       else
        {
            $customer_id = 0;
            $this->customer->logout();
            $this->error['warning'] =  'Seller account disabled';
       }
    }
    else
    {
           $this->error['warning'] =  $this->language->get('error_login');
            if(is_numeric($this->request['email']))
            {
             $this->model_account_customer->addMobileLoginAttempt($this->request['email']);
            }
            else
            {
              $this->model_account_customer->addLoginAttempt($this->request['email']);
             }
    }      

    return $customer_id;

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

    if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
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
                        if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_ACTIVE)
                           {
                              $redirect_url =  $this->url->link('seller_panel/account-order', '', 'SSL');
                            }
                    else if($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE)
                        {
                          $redirect_url =  $this->url->link('seller_panel/profile', '', 'SSL');
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

  public function SellerAccountEmail($company, $email) {

    $this->load->language('mail/seller');

    $data['email'] = $email;
    $data['name'] = $company;

    $html = '';
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/seller_account_email.tpl')) {
      $html = $this->load->view($this->config->get('config_template') . '/template/mail/seller_account_email.tpl', $data);
    }

    $mail = new  PHPMailer();

    $mail->isSMTP();
    $mail->Host = $this->config->get('config_mail_smtp_hostname');
    $mail->Port = $this->config->get('config_mail_smtp_port');
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Username = $this->config->get('config_mail_smtp_username');
    $mail->Password = $this->config->get('config_mail_smtp_password');
    $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
    $mail->addReplyTo(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

    $mail->addAddress($email, $company);

    $mail->addCC(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
    $mail->Subject = $this->language->get('text_seller_signup_subject');
    //$mail->Subject = 'Welcome to WholesaleBox : Seller Panel Access';

    $mail->msgHTML($html);
    $mail->isHTML(true);
    $mail->send();
    }

}
