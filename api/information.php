<?php
     require_once(__DIR__.'/system.php');

    class InformationController extends SystemController
    {
        private $error = "";
        public function __construct($params) {

            parent::__construct($params);
        }
        /**
         * customer login
         */
        public function information() 
        {
          $this->load->model('catalog/information');

          if(isset($this->request['information_id'])){
             $keyword = $this->request['information_id'];
             $information_id =  $this->url->rewrite_value($this->request['information_id']);
          }
          else
          {
           $keyword = ''; 
           $information_id = 0; 
          }

         $information_info = $this->model_catalog_information->getInformation($information_id);

          if ($information_info) {
            $data['heading_title']   = html_entity_decode($information_info['title']);
            $data['keyword']  = $keyword;
            $data['button_continue'] = $this->language->get('button_continue');
            $data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
            $data['continue'] = $this->url->link('common/home');
          } else {
           $data['heading_title'] = $this->language->get('text_error');
           $data['keyword']       = '';
           $data['text_error'] = $this->language->get('text_error');
           $data['button_continue'] = $this->language->get('button_continue');
           $data['continue'] = $this->url->link('common/home');
          }

          $this->data_packet->data       = $data;
          $this->data_packet->message    = 'data fatch successfully';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;
        }

        public function about_us() 
        {
          $this->load->model('tool/image');
          $this->load->model('catalog/information');

          $team_cofounder = array();

          $data['images']['wholesalebox_about'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about.jpg');
          $data['images']['wholesalebox_about_2'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_2.jpg');
          $data['images']['wholesalebox_about_graph'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_graph.jpg');
          $data['images']['wholesalebox_about_how_we_work'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_how_we_work.jpg');
          $data['images']['wholesalebox_rohit_dangayach'] =  $this->model_tool_image->getOriginalImage('wholesalebox_rohit_dangayach.jpg');
          $data['images']['wholesalebox_chandan_agarwal'] =  $this->model_tool_image->getOriginalImage('wholesalebox_chandan_agarwal.jpg');
          $data['images']['wholesalebox_rakesh_shekhawat'] =  $this->model_tool_image->getOriginalImage('wholesalebox_rakesh_shekhawat.jpg');
          $data['images']['wholesalebox_madhur_maheshwari'] =  $this->model_tool_image->getOriginalImage('wholesalebox_madhur_maheshwari.jpg');
          $data['images']['wsb_topimg'] =  $this->model_tool_image->getOriginalImage('team/wsb-topimg.jpg');
          $data['images']['infographics_1'] =  $this->model_tool_image->getOriginalImage('infographics_1.jpg');
          $data['images']['infographics_2'] =  $this->model_tool_image->getOriginalImage('infographics_2.jpg');
          $data['images']['infographics_3'] =  $this->model_tool_image->getOriginalImage('infographics_3.jpg');

          $this->load->model('account/customer');
        $this->load->model('catalog/product');
        
        $data['total_customers'] = $this->model_account_customer->getTotalCustomers();  
        $data['total_products'] = $this->model_catalog_product->getHomePageProductsTotal();

          $information_info = $this->model_catalog_information->getInformation(4);

          if ($information_info) 
          {
            $data['title'] = $information_info['title'];
            $data['meta_description'] = $information_info['meta_description'];
            $data['meta_keyword'] = $information_info['meta_keyword'];
          }    
 
          $this->data_packet->data       = $data;
          $this->data_packet->message    = 'data fatch successfully';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;
        }


        public function storelocator() 
        {
       
         $this->load->model('localisation/location');
         $show_on = 'storelocator';
         $data['store_locators'] = $this->model_localisation_location->getStoreLocator($show_on);
      
          $this->data_packet->data       = $data;
          $this->data_packet->message    = 'data fatch successfully';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;
        }


        public function contactus() 
        {
          $this->load->model('tool/image');
          $this->load->model('localisation/location');
           $data['store'] = $this->config->get('config_name');
           $data['address'] = nl2br($this->config->get('config_address'));
           $data['geocode'] = $this->config->get('config_geocode');
           $data['telephone'] = $this->config->get('config_telephone');
           $data['fax'] = $this->config->get('config_fax');
           $data['open'] = nl2br($this->config->get('config_open'));
           $data['comment'] = $this->config->get('config_comment');
        
          foreach((array)$this->config->get('config_location') as $location_id)
          {
            $location_info = $this->model_localisation_location->getLocation($location_id);

            if ($location_info) {
              if ($location_info['image']) {
               $image = $this->model_tool_image->resize($location_info['image'], $this->config->get('config_image_location_width'), $this->config->get('config_image_location_height'));
                } else {
               $image = false;
                }
           $data['locations'][] = array(
             'location_id' => $location_info['location_id'],
             'name'        => $location_info['name'],
             'address'     => nl2br($location_info['address']),
             'geocode'     => $location_info['geocode'],
             'telephone'   => $location_info['telephone'],
             'fax'         => $location_info['fax'],
             'image'       => $image,
             'open'        => nl2br($location_info['open']),
             'comment'     => $location_info['comment']
             );
            }
          }

          $this->data_packet->data       = $data;
          $this->data_packet->message    = 'data fatch successfully';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;
        }
  
        public function contactus_form() 
        {
          $this->load->language('information/contact');
          if($this->validate()) {
           $mail = new PHPMailer();
           $mail->isSMTP();
           $mail->Host = $this->config->get('config_mail_smtp_hostname');
           $mail->Port = $this->config->get('config_mail_smtp_port');  
           $mail->SMTPSecure = 'ssl';
           $mail->SMTPAuth = true;
           $mail->Username = $this->config->get('config_mail_smtp_username');
           $mail->Password = $this->config->get('config_mail_smtp_password');
           $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
           $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');
           $mail->Subject = html_entity_decode(sprintf($this->language->get('email_subject'), 
                                                  $this->request['name']), 
                                                  ENT_QUOTES, 
                                                  'UTF-8');
           $mail->msgHTML(sprintf($this->language->get('email_message'), 
                             $this->request['name'], 
                             $this->request['telephone'], 
                             $this->request['email'],
                             $this->request['company'], 
                             $this->request['enquiry']), 
                             ENT_QUOTES, 
                             'UTF-8');
            $mail->send();
            $data['error'] = 0;
            $data['msg']   = 'Thanku for your query. We will contact you soon.';
        }
        else
        {
            $data['error'] = 1;
            $data['msg']   = $this->error;
        }

          $this->data_packet->data       = $data;
          $this->data_packet->message    = 'data fatch successfully';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;
        }

    public function terms_data()
    {
      $this->load->model('catalog/information');
      $information_info = $this->model_catalog_information->getInformation(11);
      
      if($information_info)
      {
       $information_info['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
      }

      $this->data_packet->data           = $information_info;
      $this->data_packet->statusCode     = 200;
      return $this->data_packet; 
    }

    public function policies()
    {
      $this->load->model('catalog/information');

       $information_info = array();
       $informations = $this->model_catalog_information->getInformations();

           $i=0;
           foreach ($informations as $result) 
           {
            if ($result['bottom']) {
               if($i==0) { $class = 'active'; } else { $class = ''; }
              $information_info[] = array(
                     'information_id' => $result['information_id'],
                     'title' => $result['title'],
                     'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id']),
                     'mobile_href'  => $this->url->rewrite_keyword('information_id', $result['information_id']),
                     'short_description'  => html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8'),
                     'meta_title'  => $result['meta_title'],
                     'meta_description'  => $result['meta_description'],
                     'meta_keyword'  => $result['meta_keyword'],
                     'class'  => $class);
                $i++;
             } 
           }

      $this->data_packet->data           = $information_info;
      $this->data_packet->statusCode     = 200;
      return $this->data_packet; 
    }    

     protected function validate() {

        $this->load->language('information/contact');

        if ((utf8_strlen($this->request['enquiry']) < 5) || (utf8_strlen($this->request['enquiry']) > 3000)) {
          $this->error = $this->language->get('error_enquiry');
         }

        if ((int)(is_numeric($this->request['telephone'])) == 0 || utf8_strlen($this->request['telephone']) !=  10) {
           $this->error = $this->language->get('error_telephone');
         }  

        if (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request['email'])) {
         $this->error = $this->language->get('error_email');
         } 

        if ((utf8_strlen($this->request['name']) < 3) || (utf8_strlen($this->request['name']) > 32)) 
        {
          $this->error = $this->language->get('error_name');
        }                        

         return !$this->error;
      }

    }