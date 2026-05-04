<?php
     require_once(__DIR__.'/system.php');

  class BankController extends SystemController
  {
        private $error = "";

        public function __construct($params) {

            parent::__construct($params);
        }

    public function update_bank_otp()
    {
      $result = array('error'=>1);
      $this->load->language('account/sms_templates');
      $this->load->language('account/login');
      $this->load->model('account/customer');

      $result['session_id']    = session_id();
      $result['ip']            = $this->getIpAddress;
      $result['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
      $result['otp']           = (SITE_ENVIRONMENT =='Production')?rand(1000, 9999):1010;
      $result['otp_page']      = 'bank_update';
      $result['customer_id']   = $this->customer->getId();
      $result['country_code']  = $this->customer->getCountryCode();
      $result['reg_telephone'] = (empty($this->customer->getTelephone())) ? $this->customer->getEmail() : $this->customer->getTelephone();
      
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

        $this->data_packet->data           = $result;
        $this->data_packet->message        = 'Detail successfully updated';
        $this->data_packet->statusCode     =  200;
        return $this->data_packet;
      }       

     public function user_bank_detail(){
          $this->load->model('account/customer');
          $data['bank_ac_holder_name']=$data['bank_ac_number']=$data['ifsc_code']='';
          /***** UPI *****/
          $data['entry_customer_vpa'] = $this->language->get('entry_customer_vpa');
          $customer_vpa = $this->model_account_customer->getCustomerVPA($this->customer->getId());
            if(!empty($customer_vpa)){
              $data['customer_vpa'] = $customer_vpa;
            }else{
              $data['customer_vpa'] = '';
            }
          /****/
          $bank_details = $this->model_account_customer->getCustomerBank_details($this->customer->getId());
            if(isset($bank_details)){
              if(!empty($bank_details['bank_ac_holder_name'])){
                $data['bank_ac_holder_name'] = $bank_details['bank_ac_holder_name'];
              }
              if(!empty($bank_details['bank_ac_number'])){
                $data['bank_ac_number']      = $bank_details['bank_ac_number'];
              }
              if(!empty($bank_details['ifsc_code'])){
                $data['ifsc_code']           = $bank_details['ifsc_code'];
              }
            }

            $this->data_packet->data        = $data;
            $this->data_packet->message     = 'bank detail successfully fetch';;
            $this->data_packet->statusCode  =  200;
            return $this->data_packet;

        }

        

    public function user_bank_detail_update(){

          $this->load->language('account/bank_details');

            $data['bank_ac_holder_name'] = $this->request['bank_ac_holder_name'];
            $data['bank_ac_number']      = $this->request['bank_ac_number'];
            $data['ifsc_code']           = $this->request['ifsc_code'];
            $data['customer_vpa']        = $this->request['customer_vpa'];
            $this->request['customer_id'] = $this->customer->getId();
            $this->request['otp_page']    = 'bank_update';
          
            if(!$this->bank_detail_validate())
            {
               $this->data_packet->data       = $this->error;
               $this->data_packet->message    = $this->error;
               $this->data_packet->statusCode = 400;
                return $this->data_packet;
            } 
            else{
                $this->load->model('account/customer');
                $this->request['customer_id'] = $this->customer->getId();
                $this->model_account_customer->editCustomerBankDetails($this->request);
                $data['success'] = $this->language->get('text_success');

                $this->data_packet->data       = $this->language->get('text_success');
                $this->data_packet->message    = $this->language->get('text_success');
                $this->data_packet->statusCode = 200;
                return $this->data_packet;
        }
          exit;
      }


  protected function bank_detail_validate() 
    {
          if ((utf8_strlen(trim($this->request['bank_ac_holder_name'])) < 1) || (utf8_strlen(trim($this->request['bank_ac_holder_name'])) > 32)) {
            $this->error = $this->language->get('error_account_holder_name');
          }

          if ((utf8_strlen(trim($this->request['bank_ac_number'])) < 1) || (utf8_strlen(trim($this->request['bank_ac_number'])) > 32)) {
            $this->error = $this->language->get('error_account_number');
            $account_number_error='1';
          }

          if ((utf8_strlen(trim($this->request['ifsc_code'])) < 1) || (utf8_strlen(trim($this->request['ifsc_code'])) > 32)) {
            $this->error = $this->language->get('error_ifsc_code');
            $ifsc_code_error='1';
          }

          if ((utf8_strlen(trim($this->request['otp'])) < 1) || (utf8_strlen(trim($this->request['otp'])) > 4)) {
            $this->error = $this->language->get('error_otp');
          }

          if ((utf8_strlen(trim($this->request['customer_vpa'])) > 0)) {
            $upi_vpa = trim($this->request['customer_vpa']);
            (int) $length = utf8_strlen($upi_vpa);
                if((int) $length > 0){
                  $pos = strpos($upi_vpa, '@');
                  $pos_next = strpos($upi_vpa, '@', $pos+1);
                  if($pos == 0 || $pos == ($length-1) || $pos === FALSE || $pos_next > 0){
                    $this->error = $this->language->get('error_customer_vpa');
                  }
                }
          }
          $this->load->model('account/customer');
        if(empty($account_number_error) && empty($ifsc_code_error)){
          $check_if_bank_account_exist=$this->model_account_customer->checkIfBankAccountExists($this->request);
          if(empty($check_if_bank_account_exist)){
            $this->error = $this->language->get('error_account_number_exist');
            $account_number_error='1';
          }
        }


       if(empty($account_number_error) && empty($ifsc_code_error)){
      $check_if_bank_account_block=$this->model_account_customer->checkIfBankAccountIsBlock($this->request);
      if(!empty($check_if_bank_account_block)){
        $this->error = $this->language->get('error_account_number_block');
         }
       }

      if(empty($this->error))
      {
        $check_data = $this->model_account_customer->checkOTP($this->request);
         if(count($check_data) == 0)
          {
            $check_data = $this->model_account_customer->checkMasterOTP($this->request);
          }
         if(count($check_data) == 0)
         {
           $this->error = $this->language->get('error_otp_exist');
         }
         else
         {
           $this->model_account_customer->verifyOTP($check_data);
         }
      }

        return !$this->error;
    } 

  }
