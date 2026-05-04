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
                     'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                    );
                  }
           }
          $data['logged'] = $this->customer->isLogged();
          $data['dropshipper'] = $this->url->link('account/dropshipper', 'static=dropshipper', 'SSL');
          if ($data['logged']) { 
              $customer_id = $this->customer->getId();
              $data['is_dropshipper'] = $this->model_account_customer->getisdropshipper($customer_id);
              $dropshipper = $data['is_dropshipper'];
            }


          $data['year'] = date("Y");

         $this->data_packet->data        = $data;
         $this->data_packet->statusCode  = 200;
         return $this->data_packet;

        }     

    }