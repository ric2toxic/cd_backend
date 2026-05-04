<?php
     require_once('system.php');
     
    class FooterController extends SystemController
    {
        public function __construct($params) {

            parent::__construct($params);

        }
        /**
         * customer login
         */
        public function information() 
        {
            $this->load->model('catalog/information');
            $this->load->model('account/customer');
            $this->load->model('tool/image');

            $data['informations'] = array();
            foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['bottom']) {
                $data['informations'][] = array(
                     'information_id' => $result['information_id'],
                     'title' => $result['title'],
                     'short_description' => $result['short_description'],
                     'meta_title' => $result['meta_title'],
                     'meta_description' => $result['meta_description'],
                     'meta_keyword' => $result['meta_keyword'],
                     'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id']),
                     'mobile_href'  => $this->url->rewrite_keyword('information_id', $result['information_id'])
                    );
                  }
           }
          $data['logged'] = $this->customer->isLogged(); 
          $data['contact'] = $this->url->link('information/contact', '', 'SSL');
          $data['storelocator'] = $this->url->link('information/storelocator', '', 'SSL');
          $data['logout'] = $this->url->link('account/logout', '', 'SSL');
          $data['careers'] = $this->url->link('careers/careers', 'static=careers', 'SSL');
          $data['dropshipper'] = $this->url->link('account/dropshipper', 'static=dropshipper', 'SSL');
          if ($data['logged']) { 
              $customer_id = $this->customer->getId();
              $data['is_dropshipper'] = $this->model_account_customer->getisdropshipper($customer_id);
              $dropshipper = $data['is_dropshipper'];
            }


          $data['footer_images']['fd']               =  $this->model_tool_image->getOriginalImage('fd.jpg');
          $data['footer_images']['gk']               =  $this->model_tool_image->getOriginalImage('gk.jpg');
          $data['footer_images']['dtdc']           =  $this->model_tool_image->getOriginalImage('dtdc.jpg');
          $data['footer_images']['blue_dart']                =  $this->model_tool_image->getOriginalImage('blue_dart.jpg');
          $data['footer_images']['qr_code_new']               =  $this->model_tool_image->getOriginalImage('qr_code_new.png');
          $data['footer_images']['google_play_android_app']                =  STATIC_CONTENT_URL_SSL.'google-play-android-app.svg';
          $data['footer_images']['ios_download']            =  STATIC_CONTENT_URL_SSL.'ios_download.svg';
          $data['footer_images']['PAYMENT']               =  $this->model_tool_image->getOriginalImage('PAYMENT.jpg');

          $this->data_packet->data       = $data;
          $this->data_packet->message    = 'data fatch successfully';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;
        }

        public function popular_tags() 
        {
           $this->load->model('module/popular_search');
           $this->load->language('common/footer');
           $getPopularTag = $this->model_module_popular_search->getPopularTags();
           $data['popular_tags'] = array();
           foreach($getPopularTag as $values){
            $data['popular_tags'][] = array(
             'text' => $values['popular_search'],
             'link' => $values['link'],
             'href' => html_entity_decode($this->url->link('product/search','&search='.html_entity_decode(trim($values['popular_search'])),'SSL'))
            );
          }
          $data['text_popular_tag_lable'] = $this->language->get('text_popular_tag_lable');

          $this->data_packet->data       = $data;
          $this->data_packet->message    = 'data fatch successfully';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;
        }


     public function getAppLink(){
         $this->load->language('mail/forgotten');
         $this->load->language('account/sms_templates');
         $json = array();
         $mobile_no = $this->request['mobile_no'];
         if(is_numeric($mobile_no)){
           if(strlen($mobile_no)==10){
              $message = sprintf($this->language->get('download_app_link'));
              $send_sms = new SMS($message,$mobile_no);
              sleep(5);
              $send_sms->sendMessage();
              $json['success'] = true;
              $json['success_msg'] = $this->language->get('success_msg');
             }else{
                $json['error'] = true;
                $json['error_msg'] = $this->language->get('error_valid_mobile_number');
                }
           }else{
            $json['error'] = true;
              $json['error_msg'] = $this->language->get('error_mobile_number');
           }

          $this->data_packet->data       = $json;
          $this->data_packet->message    = 'data fatch successfully';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;

        } 

    public function sendMailForCallBackRequest(){
        
        $mail = new PHPMailer();
        $mobile = $this->request['mobile'];
        $subject = "Callback Request From: " . $mobile;
        $body    = ""; 
        $body .= "Dear Wholesalebox Team,<br/><br/>";
        $body .= "Following customer has placed a request for callback.<br/>";
        $body .= "Customer Mobile Number: " . $mobile . "<br/><br/>";
        $body .= "Please Assist <br/>";
        $mail->isSMTP(); 
        //$mail->SMTPDebug = 1; 
        $mail->SMTPAuth = true; 
        $mail->SMTPSecure = 'ssl'; 
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port'); 
        $mail->Username = $this->config->get('config_mail_smtp_username');        
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->SetFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');         
        $mail->addAddress(EMAIL_IDS['info']['email_id'],EMAIL_IDS['info']['name']); 
        $mail->Subject = $subject;                  
        $mail->Body    = $body;  
        $mail->isHTML(true);
        $mail = $mail->Send(true);

        $this->data_packet->data       = array();
        $this->data_packet->message    = 'Thank you! We shall call you shortly.';
        $this->data_packet->statusCode = 200;
        return $this->data_packet;
    }


  }