<?php
     require_once(__DIR__.'/system.php');
     require_once(DIR_SYSTEM.'library/solr/lookup.php');
     require_once(DIR_CATALOG.'form/register_form.php');

    class AccountController extends SystemController
    {
        private $error = "";

        public function __construct($params) {

            parent::__construct($params);

            $mobile_country_code = array('CO'=>array('AE'=>'+971','AU'=>'+61','BD'=>'+880','CA'=>'+1','IN'=>'+91','MY'=>'+60','OM'=>'+968','SA'=>'+966','GB'=>'+44'), 'IN'=>array('IN'=>'+91'));

            $this->registry->set('mobile_country_code', $mobile_country_code);
            //$this->registry->set('customer',new Customer($this->registry));

        }
        /**
         * customer login
         */

 public function user_detail() 
  {
    $this->load->model('account/customer');
    $json = array();
    $data['heading_title'] = $this->language->get('heading_title');
    $data['text_your_details'] = $this->language->get('text_your_details');
    $data['text_additional'] = $this->language->get('text_additional');
    $data['text_select'] = $this->language->get('text_select');
    $data['text_loading'] = $this->language->get('text_loading');
    $data['entry_firstname'] = $this->language->get('entry_firstname');
    $data['entry_lastname'] = $this->language->get('entry_lastname');
    $data['entry_email'] = $this->language->get('entry_email');
    $data['entry_telephone'] = $this->language->get('entry_telephone');
    $data['entry_gst_number'] = $this->language->get('entry_gst_number');
    $data['button_continue'] = $this->language->get('button_continue');
    $data['button_back'] = $this->language->get('button_back');
    $data['button_upload'] = $this->language->get('button_upload');

    $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

    if (!empty($customer_info)) {
      $data['firstname'] = $customer_info['firstname'];
    } else {
      $data['firstname'] = '';
    }
    if (!empty($customer_info)) {
      $data['lastname'] = $customer_info['lastname'];
    } else {
      $data['lastname'] = '';
    }

    if (!empty($customer_info) && $customer_info['email'] != '') {
      $data['email'] = $customer_info['email'];
      $data['email_exist'] = 1;
    }
    else 
    {
      $data['email'] = '';
    }   

    if (!empty($customer_info) && $customer_info['telephone'] != '') {
      $data['telephone'] = $customer_info['telephone'];
      $data['telephone_exist'] = 1;
    }
    else 
    {
      $data['telephone'] = '';
    }

  if (!empty($customer_info) && $customer_info['gst_number'] != '') {
      $data['gst_number'] = $customer_info['gst_number'];
      $data['gst_number_exist'] = 1;
    }
   else 
   {
      $data['gst_number'] = '';
    }

    $this->data_packet->data        = $data;
    $this->data_packet->message     = 'data fatch successfully';
    $this->data_packet->statusCode  =  200;
    return $this->data_packet;
  }

  public function update_number_otp()
  {
      $result = array('error'=>1);

      $this->load->language('account/sms_templates');
      $this->load->language('account/login');
      $this->load->model('account/customer');

   if(!empty($this->request['update_telephone']))
      {
        $result['reg_telephone'] = $this->request['update_telephone'];
        if(is_numeric($result['reg_telephone']) && !empty($this->request['country_code'])) 
         { 
            $result['country_code']  = $this->request['country_code'];
            $result['country_iso_code']  = $this->request['country_iso_code'];
         }
        else { $result['country_code']  = ''; $result['country_iso_code']  = ''; }

            $user_data = $this->model_account_customer->getCustomerByEmailOrMobile($result['reg_telephone']);

            if(count($user_data) == 0)
            {
                $result['session_id']    = session_id();
                $result['ip']            = $this->getIpAddress;
                $result['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                $result['otp']           = (SITE_ENVIRONMENT =='Production')?rand(1000, 9999):1010;
                $result['otp_page']      = 'update_profile';
                $result['customer_id']   = $this->customer->isLogged();
            
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
                             $message = $this->language->get('on_update_email_co');
                          }
                         else
                          {
                             $message = $this->language->get('on_update_email');
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
        else
        {
          $result['msg']   = 'Mobile number or Email address already exist.';
        }
      }
      else
      {
        $result['msg']   = 'Please enter mobile number or email for verify';
      }

      $this->data_packet->data           = $result;
      $this->data_packet->message        = 'Detail successfully updated';
      $this->data_packet->statusCode     =  200;
      return $this->data_packet;
    }


  public function verify_otp()
  {
      $result = array('error'=>1);
      $this->load->language('account/login');
      $this->load->model('account/customer');
       
      if(isset($this->request['otp']))
      {
            $result['otp_page']          = 'update_profile';
            $result['reg_telephone']     = $this->request['reg_telephone'];

                if(is_numeric($result['reg_telephone']))
                {
                   if(isset($this->request['country_code'])) 
                    { $result['country_code']= $this->request['country_code']; } 
                   else { $result['country_code']=''; }
                  
                   if(isset($this->request['country_iso_code'])) 
                    { $result['country_iso_code']= $this->request['country_iso_code']; }
                   else { $result['country_code']=''; }
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
               $this->model_account_customer->update_account_mobile_email($check_data);
               $result['msg']   =  $this->language->get('text_login_success');
               $result['error'] = 0;
                  
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

      $this->data_packet->data           = $result;
      $this->data_packet->message        = 'OTP successfully verified';
      $this->data_packet->statusCode     =  200;
      return $this->data_packet;
    }

    public function user_profile_update() 
    {

      $this->load->model('account/customer');
      $this->load->language('account/edit');

      if($this->customer->getEmail() != '')
     {
        $this->request['email'] = $this->customer->getEmail();
      }

      if($this->customer->getTelephone() != '')
            {
        $this->request['telephone'] = $this->customer->getTelephone();
      }

    if ($this->validate()) 
      {
          if($this->customer->getGSTNumber() != '')
          {
              $this->request['gst_number'] = $this->customer->getGSTNumber();
          }
          $this->model_account_customer->editCustomer($this->request);

          $this->data_packet->data           = '';
          $this->data_packet->message        = 'Detail successfully updated';
          $this->data_packet->statusCode     =  200;
          return $this->data_packet;

       }
       else{

          $this->data_packet->data           = $this->error;
          $this->data_packet->message        = $this->error;
          $this->data_packet->statusCode     =  400;
          return $this->data_packet;
       }
       exit;
     }

  public function user_password_update(){

        $this->load->language('account/password');
        if($this->password_validate()) 
         {
           $this->load->model('account/customer');
           
          $this->model_account_customer->editPassword($this->customer->getId(), $this->request['password']);
          
           $this->data_packet->data        = $this->language->get('text_success');
           $this->data_packet->message     = $this->language->get('text_success');
           $this->data_packet->statusCode  =  200;
           return $this->data_packet;
          }
          else
          {
            $this->data_packet->data        = $this->error;
            $this->data_packet->message     = $this->error;
            $this->data_packet->statusCode  =  400;
            return $this->data_packet;
          }
          exit;
 }          

    public function postYourRequirement(){
      $data = array();
      $email = array(); 
      $this->load->model('tool/image'); 

      $this->load->language('account/get_customer_preferences');

      $this->load->model('catalog/category');
      $this->load->model('account/return');
      
      $data['preference_text']  = $this->language->get('preference_text');
      $data['description_text'] = "Please describe your buying requirement. i.e Fabric,material and many other preferences.";

      if($this->request){
        if(empty($this->request['category'])){
          $data['error'] = "Please select category";
        }else{
          $post['category_name'] = $this->request['category'];
        }
        if(empty($this->request['quantity'])){
          $post['quantity'] = 0;
        }else{
          $post['quantity'] = $this->request['quantity'];
        }
        if(empty($this->request['require_days'])){
          $post['required_days'] = "";
        }else{
          $post['required_days'] = $this->request['require_days'];
        }
        if(empty($this->request['price_from'])){
          $post['price_from'] = 0;
        }else{
          $post['price_from'] = $this->request['price_from'];
        }
        if(empty($this->request['price_to'])){
          $post['price_to'] = 0;
        }else{
          $post['price_to'] = $this->request['price_to'];
        }

        
        if(empty($this->request['name'])){
          $post['name'] = $this->customer->getFirstName()+' '+$this->customer->getLastName();
         }else{
          $post['name'] = $this->request['name'];
        }

        if(empty($this->request['email'])){
          $post['email'] = $this->customer->getEmail();
         }else{
          $post['email'] = $this->request['email'];
        }

        if(empty($this->request['mobile_no'])){
          $post['mobile_no'] = $this->customer->getTelephone();
         }else{
          $post['mobile_no'] = $this->request['mobile_no'];
        }

        if(!empty($this->request['refrenece_link'])){
          $post['refrenece_link'] = $this->request['refrenece_link'];
        }else{
          $post['refrenece_link'] = '';
        }

        if(!empty($this->request['description'])){
          $post['description'] = $this->request['description'];
        }else{
          $post['description'] = '';
        }

      }
      $menus = $this->model_catalog_category->menus('Desktop', 0, 'desktop-category');
      if(!empty($menus)){
        $menu_title = array_column($menus,'link_title');
        $data['menu'] = $menu_title;
      }
      $data['success']='';
      if(!isset($data['error']) && !empty($post) && $this->request){
        $this->load->model('restapi/wsbcartservice');
        $result = $this->model_restapi_wsbcartservice->insertRequirement($post);
      
        if(isset($result['success'])){
          $mail_data['firstname'] = $post['name'];
          $mail_data['telephone'] = $post['mobile_no'];
          $mail_data['email'] = $post['email'];
          $mail_data['category_name'] = $post['category_name'];
          $mail_data['quantity'] = $post['quantity'];
          $mail_data['required_days'] = $post['required_days'];
          $mail_data['price_from'] = $post['price_from'];
          $mail_data['price_to'] = $post['price_to'];
          $mail_data['refrenece_link'] = $post['refrenece_link'];
          $mail_data['description'] = $post['description'];
          $mail_data['image_name'] = $result['images_data'];
          
          $body = $this->model_restapi_wsbcartservice->getmailBody($mail_data);
          $subject = 'Product Requirement query from '.$post['category_name'];
          
          $email['buyer_email'] = EMAIL_IDS['prabhav']['email_id']; 
          $email['buyer_name']  = EMAIL_IDS['prabhav']['name'];
          $email['post_your_req'] = 1;
          $this->model_account_return->sendMailFromApi($email,$subject,$body);
                    $data['success'] = "Your requirement is submitted successfully!";
        }
      } 
      if($data['success']!=''){
          $this->data_packet->data        = $data;
          $this->data_packet->message     = $data['success'];
          $this->data_packet->statusCode  =  200;
          return $this->data_packet;
      }  else{
          
          $this->data_packet->data        = $data['error'];
          $this->data_packet->message     = 'Your requirement is submitted fail!';
          $this->data_packet->statusCode  =  400;
          return $this->data_packet;
      }
    }


    public function postYourRequirementCategory(){
            $this->load->model('catalog/category');
            $this->load->model('account/return');
            $data=array();
            $menus = $this->model_catalog_category->menus('Desktop', 0, 'desktop-category');
            if(!empty($menus)){
              $menu_title = array_column($menus,'link_title');
              $data = $menu_title;
            }

          $this->data_packet->data        = $data;
          $this->data_packet->message     = 'data fatch successfully';
          $this->data_packet->statusCode  =  200;
          return $this->data_packet;
    }

   public function updateDropshipper(){
    $this->load->model('account/customer');
    $customer_id = $this->customer->getId();
    $this->model_account_customer->updateDropshipper($customer_id);
   
    $this->data_packet->data        = array();
    $this->data_packet->message     = 'We would review your request shortly and would get back to you.';
    $this->data_packet->statusCode  =  200;
    return $this->data_packet;
  }

  protected function validateGSTNo($gst_number) 
  {
      $regex_for_gst = '/^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([a-zA-Z0-9]){1}?$/';
  
      if (!preg_match($regex_for_gst, $gst_number)) 
        {
            return true;
        }
      else
        {
           return false;
        }
  
  }

  protected function validate() {
      $this->error = array();

    if ((utf8_strlen(trim($this->request['firstname'])) < 1) || (utf8_strlen(trim($this->request['firstname'])) > 32)) {
      $this->error = $this->language->get('error_firstname');
    }
    else if ((utf8_strlen($this->request['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request['email'])) {
      $this->error = $this->language->get('error_email');
    }
    else if (($this->customer->getEmail() != $this->request['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request['email'])) {
      $this->error = $this->language->get('error_exists');
    }
    else if (($this->customer->getTelephone() != $this->request['telephone']) && $this->model_account_customer->getTotalCustomersByTelephone($this->request['telephone'])) {
      $this->error = $this->language->get('error_telephone_exists');
    }
    else if ((utf8_strlen($this->request['telephone']) < 3) || (utf8_strlen($this->request['telephone']) > 32)) {
      $this->error = $this->language->get('error_telephone');
    }

    else if (!empty(trim($this->request['gst_number'])) && $this->validateGSTNo($this->request['gst_number'])) {
      $this->error = $this->language->get('error_gst_number');
    }
    else if (!empty($this->error['gst_number']) && !empty(trim($this->request['gst_number'])) && $this->model_account_customer->getTotalCustomersByGst($this->request['gst_number']))
    {
          $this->error = $this->language->get('error_exists_gst');
    }

    return !$this->error;
  }


  protected function password_validate() 
  {
    if ((utf8_strlen($this->request['password']) < 6) || (utf8_strlen($this->request['password']) > 20)) {
      $this->error = $this->language->get('error_password');
    }

    if ($this->request['password'] != $this->request['confirm_password']) {
      $this->error = $this->language->get('error_confirm');
    }

    return !$this->error;
  }   

}