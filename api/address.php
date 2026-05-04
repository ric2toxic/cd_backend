<?php
require_once(__DIR__.'/system.php');
require_once(DIR_SYSTEM.'library/solr/lookup.php');

    class AddressController extends SystemController{
        public function __construct($params)
        {
            parent::__construct($params);
        }

        public function autoPopulateAddress(){

            if(!empty($this->request['pincode'])) {
                $request['pincode'] = (int) str_replace( " ", "", trim( $this->request['pincode'] ));
                $data = '';
                $solr = new SolrLookup($this);
                $data = $solr->gePincodeData($request['pincode']);
            }

            $info_to_be_populated = array();
            $city = isset($data[0]['city']) ? $data[0]['city']:'';
            $state = isset($data[0]['zone']) ? $data[0]['zone']:'--- Please Select ---';
            $country = isset($data[0]['country']) ? $data[0]['country']:'';
            $country_id = isset($data[0]['country_id']) ? $data[0]['country_id']:99;
            $zone_id = isset($data[0]['zone_id']) ? $data[0]['zone_id']:'';
            $info_to_be_populated['country_id'] = $country_id;
            if($city === ''){
                $this->load->model('localisation/zone');
                $this->load->model('localisation/country');
                $info_to_be_populated['zones'] = $this->model_localisation_zone->getZonesByCountryId($country_id);
                $info_to_be_populated['countries'] = $this->model_localisation_country->getCountries();
                $ip_info = $this->__ipInfo("Visitor", "location");
                $country = $this->model_localisation_country->getCountryByCode($ip_info['country_code']);
                if(!empty($country))
                    $info_to_be_populated['country_id'] = $country['country_id'];
                else
                    $info_to_be_populated['country_id'] = 0;
            }


            $info_to_be_populated['city'] = $city;
            $info_to_be_populated['state'] = $state;
            $info_to_be_populated['country'] = $country;
            $info_to_be_populated['zone_id'] = $zone_id;


            return $info_to_be_populated;


        }

        public function addAddress() {
            $json = array();
            if (!$this->customer->isLogged()) {
                $this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
                $json['status']='error';
                $json['error_msg'] = 'Customer is not logged in.';
                return $json;
            }
            $this->load->language('account/address');

            $this->load->model('account/address');
            $json['status']="error";
            if ( $this->method == 'POST' ) {
                $arr_name = explode(" ", $this->request['name']);
                $this->request['firstname'] = $arr_name[0];
                if(isset($arr_name[1])) {
                    $last_name = implode(" ",array_slice($arr_name, 1));
                    $this->request['lastname'] = $last_name;
                }else{
                    $this->request['lastname'] = '';
                }
               

                $this->load->model('account/address'); 
                
                $customer_id = $this->request['customer_id'];
                
                if(!empty($this->request['address_telephone'])) 
                {
                   $this->request['address_telephone'] = explode(",", $this->request['address_telephone']);
                }
               
                $address_id = $this->model_account_address->addAddress($this->request);
                

                $json['message'] = $this->language->get('text_add');
                $json['address_id'] = $address_id;
                $json['status']="sucess";
            }

            $json['addresses'] = $this->model_account_address->getAddresses();
            $json['customer_address_id'] = $this->model_account_address->getCustomerAddressId($this->customer->getId());
            
            return $json;
        }

        public function editAddress($get) {
            $json = array();
            if (!$this->customer->isLogged()) {
                $this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
                $json['status']='error';
                $json['error_msg'] = 'Customer is not logged in.';
                return $json;
            }

            $this->load->language('account/address');

            $this->load->model('account/address');
            $json['status']="error";
            if ( $this->method == 'POST' ) {
                $arr_name = explode(" ", $this->request['name']);
                $this->request['firstname'] = $arr_name[0];
                if(isset($arr_name[1])) {
                    $last_name = implode(" ",array_slice($arr_name, 1));
                    $this->request['lastname'] = $last_name;
                }else{
                    $this->request['lastname'] = '';
                }

                $this->load->model('account/address'); 
                

                if(!empty($this->request['address_telephone'])) 
                {
                   $this->request['address_telephone'] = explode(",", $this->request['address_telephone']);
                }

                $this->model_account_address->editAddress($get[0], $this->request); 
                
 
                $this->session->data['success'] = $this->language->get('text_add');

                $json['status']="sucess";
            }

            $json['addresses'] = $this->model_account_address->getAddresses();
            return $json;
        }
        
        
        public function getAddresses(){
            $this->load->model('account/address');
            $json['addresses'] = $this->model_account_address->getAddresses();
            $json['customer_address_id'] = $this->customer->getAddressId();
         
           $this->data_packet->statusCode = 200;
           $this->data_packet->data       = $json;
           $this->data_packet->message    = "Data Successfully Found.";
           return $this->data_packet;
        }

        private function __ipInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
            $output = NULL;
            if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
                $ip = $this->getIpAddress; // $_SERVER["HTTP_X_FORWARD"];
                // No need of deep_detect now, This is already done in above line
                /*if ($deep_detect) {
                    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                }*/
            }
            $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
            $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
            $continents = array(
                "AF" => "Africa",
                "AN" => "Antarctica",
                "AS" => "Asia",
                "EU" => "Europe",
                "OC" => "Australia (Oceania)",
                "NA" => "North America",
                "SA" => "South America"
            );
            if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
                $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
                if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                    switch ($purpose) {
                        case "location":
                            $output = array(
                                "city"           => @$ipdat->geoplugin_city,
                                "state"          => @$ipdat->geoplugin_regionName,
                                "country"        => @$ipdat->geoplugin_countryName,
                                "country_code"   => @$ipdat->geoplugin_countryCode,
                                "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                                "continent_code" => @$ipdat->geoplugin_continentCode
                            );
                            break;
                        case "address":
                            $address = array($ipdat->geoplugin_countryName);
                            if (@strlen($ipdat->geoplugin_regionName) >= 1)
                                $address[] = $ipdat->geoplugin_regionName;
                            if (@strlen($ipdat->geoplugin_city) >= 1)
                                $address[] = $ipdat->geoplugin_city;
                            $output = implode(", ", array_reverse($address));
                            break;
                        case "city":
                            $output = @$ipdat->geoplugin_city;
                            break;
                        case "state":
                            $output = @$ipdat->geoplugin_regionName;
                            break;
                        case "region":
                            $output = @$ipdat->geoplugin_regionName;
                            break;
                        case "country":
                            $output = @$ipdat->geoplugin_countryName;
                            break;
                        case "countrycode":
                            $output = @$ipdat->geoplugin_countryCode;
                            break;
                    }
                }
            }
            return $output;
        }


   public function get_address_list() 
   {
        $this->load->model('account/address');  
        $data['addresses'] = array();
        $results = $this->model_account_address->getAddresses($this->customer->getId());
        foreach ($results as $result) {
          if ($result['address_format']) {
            $format = $result['address_format'];
          } else {
            $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
          }

          $find = array(
            '{firstname}',
            '{lastname}',
            '{company}',
            '{address_1}',
            '{address_2}',
            '{city}',
            '{postcode}',
            '{zone}',
            '{zone_code}',
            '{country}'
          );

          $replace = array(
            'firstname' => $result['firstname'],
            'lastname'  => $result['lastname'],
            'company'   => $result['company'],
            'address_1' => $result['address_1'],
            'address_2' => $result['address_2'],
            'city'      => $result['city'],
            'postcode'  => $result['postcode'],
            'zone'      => $result['zone'],
            'zone_code' => $result['zone_code'],
            'country'   => $result['country']
          );

          if (isset($result['address_id']) && $this->customer->getAddressId() == $result['address_id']) {
            $result['default'] = true;
          } else {
            $result['default'] = false;
          }

          $data['addresses'][] = array(
            'address_id' => $result['address_id'],
            'address_details'=>$result,
            'address'    => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
          );
        }

    $this->data_packet->data        = $data;
    $this->data_packet->message     = 'User list successfully fetch';
    $this->data_packet->statusCode  =  200;
    return $this->data_packet;
  }

  public function user_address_book_add() 
  {
     $this->load->language('account/address');
     $this->load->model('account/address');
      $arr_name = explode(" ", $this->request['name']);
      $this->request['firstname'] = $arr_name[0];
      if(isset($arr_name[1])) {
        $this->request['lastname'] = $arr_name[1];
      }else{
        $this->request['lastname'] = '';
      }

    if ($this->validateAddressBookForm()) 
    {
        
      if(!empty($this->request['address_telephone'])) 
        {
           $this->request['address_telephone'] = explode(",", $this->request['address_telephone']);
        }
          
      $this->model_account_address->addAddress($this->request,$this->customer->getId());

  
           $this->data_packet->data        = array();
           $this->data_packet->message     = 'Address Add successfully';
           $this->data_packet->statusCode  =  200;
           return $this->data_packet;
    }
    else{

           $this->data_packet->data        = $this->error;
           $this->data_packet->message     = $this->error;
           $this->data_packet->statusCode  =  400;
           return $this->data_packet;
       }

    exit;
  }

  public function user_address_book_update() 
  {
    $this->load->language('account/address');
    $this->load->model('account/address');
    
      $arr_name = explode(" ", $this->request['name']);
      $this->request['firstname'] = $arr_name[0];
      if(isset($arr_name[1])) {
        $this->request['lastname'] = $arr_name[1];
      }else{
        $this->request['lastname'] = '';
      }

    if ($this->validateAddressBookForm()) 
     {
        if(!empty($this->request['address_telephone'])) 
        {
           $this->request['address_telephone'] = explode(",", $this->request['address_telephone']);
        }

        $this->model_account_address->editAddress($this->request['address_id'], $this->request,$this->customer->getId());

         $data['name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();

           $this->data_packet->data        = $this->request['address_id'];
           $this->data_packet->message     = 'Address update successfully';
           $this->data_packet->statusCode  =  200;
           return $this->data_packet;

       }
       else{

           $this->data_packet->data        = $this->error;
           $this->data_packet->message     = $this->error;
           $this->data_packet->statusCode  =  400;
           return $this->data_packet;
       }
    exit;
    
  }

  public function user_address_delete() 
  {
    $this->load->language('account/address');
    $this->load->model('account/address');

    if (isset($this->request['address_id']) && $this->validateDelete()) {
      $deleteAddress=$this->model_account_address->deleteAddress($this->request['address_id']);
      // when customer delete our default address then update new address id in customer table
      if ($this->customer->getAddressId() == $this->request['address_id']) {
        $this->model_account_address->updateAddressId($this->customer->getId());
      }

      // Default Shipping Address
      if (isset($this->session->data['shipping_address']['address_id']) && ($this->request['address_id'] == $this->session->data['shipping_address']['address_id'])) {
        unset($this->session->data['shipping_address']);
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
      }

      // Default Payment Address
      if (isset($this->session->data['payment_address']['address_id']) && ($this->request['address_id'] == $this->session->data['payment_address']['address_id'])) {
        unset($this->session->data['payment_address']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
      }

      $this->session->data['success'] = $this->language->get('text_delete');

           $this->data_packet->data        = '';
           $this->data_packet->message     = $this->language->get('text_delete');
           $this->data_packet->statusCode  =  200;
           return $this->data_packet;
    }
    else{

           $this->data_packet->data        = '';
           $this->data_packet->message     = $this->language->get('error_delete');
           $this->data_packet->statusCode  =  400;
           return $this->data_packet;
       }
      exit;
  }


  protected function validateDelete() {
    if ($this->model_account_address->getTotalAddresses() == 1) {
      $this->error = $this->language->get('error_delete');
    }
    return !$this->error;
  }

  protected function validateAddressBookForm() {

    $store_id = (int)($this->config->get('config_store_id'));

    if ((utf8_strlen(trim($this->request['name'])) < 1) || (utf8_strlen(trim($this->request['name'])) > 32)) {
      $this->error = $this->language->get('error_name');
    }

    if ((utf8_strlen(trim($this->request['address_1'])) < 3) || (utf8_strlen(trim($this->request['address_1'])) > 128)) {
      $this->error = $this->language->get('error_address_1');
    }

    if ((utf8_strlen(trim($this->request['city'])) < 2) || (utf8_strlen(trim($this->request['city'])) > 128)) {
      $this->error = $this->language->get('error_city');
    }

    $this->load->model('localisation/country');

    $country_info = $this->model_localisation_country->getCountry($this->request['country_id']);

        if($this->request['country_id'] != 99)
        {  
      if (utf8_strlen(trim($this->request['postcode'])) < 2 || utf8_strlen(trim($this->request['postcode'])) > 10) 
      {
      $this->error = $this->language->get('error_postcode');
       }
    }
    else
    {
           if (!preg_match ('/^([0-9]+)$/', $this->request['postcode']) || (utf8_strlen(trim($this->request['postcode'])) < 6 && utf8_strlen(trim($this->request['postcode']) > 6))) {
      $this->error = $this->language->get('error_postcode2');
       }
    } 

    if ($this->request['country_id'] == '') {
      $this->error = $this->language->get('error_country');
    }

    if (!isset($this->request['zone_id']) || empty($this->request['zone_id'])) {
      $this->error = $this->language->get('error_zone');
    }

    return !$this->error;
  }


}

?>
