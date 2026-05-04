<?php
     require_once('system.php');

    class SettingController extends SystemController
    {
        public function __construct($params) {

            parent::__construct($params);
        }
        /**
         * customer login
         */
        public function common_setting() 
        {
          $this->load->model('tool/image');

          $data['title']             = $this->config->get('config_meta_title');
          $data['description']       = $this->config->get('config_meta_description');
          $data['keywords']          = $this->config->get('config_meta_keyword');

          if ($this->config->get('config_google_analytics_status')) {
           $data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
          } else {
           $data['google_analytics'] = '';
           }

          $data['name'] = $this->config->get('config_name');

           $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));
           $data['logo'] = $this->model_tool_image->getOriginalImage($this->config->get('config_logo'));
           
         if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
         {
           $data['international_store'] = 1;
         }
         else
         {
           $data['international_store'] = 0;
         }         


        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
          $data['base'] = $this->config->get('config_ssl');
          $data['static_content_url'] = STATIC_CONTENT_URL_SSL;
        } else {
          $data['base'] = $this->config->get('config_url');
          $data['static_content_url'] = STATIC_CONTENT_URL;
        }

        if($this->config->get('config_store_id') != SOR_STORE_ID) {
          $data['store_id'] = $this->config->get('config_store_id');
          $data['text_store_link'] = $this->language->get('text_sor');
          $data['sor_store_link'] = SOR_STORE_URL;
          $data['mobile_logo'] = $data['base'] . 'image/mobile_logo.png';
        }else{
          $data['store_id'] = $this->config->get('config_store_id');
          $data['text_store_link'] = $this->language->get('text_back_to_website');
          $data['sor_store_link'] = "http://".INDIA_STORE_HOST;
          $data['mobile_logo'] = $data['base'] . 'image/sor_mobile_logo.png';
        }      

          $this->data       = $data;
          $this->message    = 'data fatch successfully';
          $this->statusCode = 200;
          return $this->__response();
        }

        private function __response(){
           // response function
           $response = array();
           $response['statusCode'] = $this->statusCode;
           $response['message']    = $this->message;
           $response['data']       = $this->data;
           return $response; 
        }

    }