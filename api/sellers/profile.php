<?php
     require_once('system.php');
     require_once( DIR_CATALOG . 'form/register_form.php' );
     require_once( DIR_SYSTEM . 'library/seller/seller_profile.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );

    class ProfileController extends SystemController
    {
        private $error = array();

        public function __construct($params) {

            parent::__construct($params);
        }
        /**
         * customer login
         */

       public function userdata() 
        {
         $result = array();

      $this->load->model('seller_panel/profile');
      $fields = "seller_id,mobile_no,email,city,address1,address2,country_id,zone_id,pickup_holder_name,pickup_contact_no,account_holder_name,account_contact_no,inventory_holder_name,inventory_contact_no,primary_contact_name,primary_contact_no,bank_ac_holder_name,bank_ac_number,pan,tin,ifsc_code,nickname,company,same_as_primary_pickup,same_as_primary_account,same_as_primary_inventory,pan_image,tin_image,cancel_cheque_image,pickup_city,pickup_address,pickup_zone_id,pickup_country_id,same_as_primary_address,tin_tax_type, pincode, pickup_pincode, gst_arn,gst_provisional_id, seller_status";
      $seller_id = $this->customer->getId();
        // $seller_id = 15711;
      $result = $this->model_seller_panel_profile->getSellersInformation($seller_id,$fields);


        $pickup_zone_id = $result["pickup_zone_id"];
        $zone_id       = $result["zone_id"];
        if($result["country_id"] == 0){
           $country_id = 99;
          } else{
           $country_id     = $result["country_id"];
         }
        $get_zones    = $this->getZone($country_id);
        $result["zones"] = $get_zones;
        $seller_nickname = $result["nickname"];
        $request['pincode'] = $result['pincode'];
        $additional_emails = $this->model_seller_panel_profile->getAdditionalEmails($seller_id);
        $result["additional_emails"] = $additional_emails;

        $this->data_packet->data           = $result;
        $this->data_packet->statusCode     = 200;
        return $this->data_packet; 
     }

  public function getZone($country_id) {
    if (isset($country_id) && !empty($country_id)){
      $this->load->model('localisation/zone');
      $result = $this->model_localisation_zone->getZonesByCountryId($country_id);
      return $result;
    }
  }


  public function updateSellerProfile(){
    $result = array();
    $seller_id = $this->customer->getId();
    $this->request['seller_id'] = $seller_id;

    if($seller_id){

      $seller_profile = new SellerProfile($this);
      $data = array();

      if(isset($this->request["same_as_primary"])){
        $data['same_as_primary']  = 1;
      }
      if(isset($this->request["tax_type"])){
        $data["tin_tax_type"] = $this->request["tax_type"];
        $data["old_tin_tax_type"] = $this->request["old_tin_tax_type"];
      }
      $data['seller_id'] = $seller_id;
      $data['field']     = $this->request["field"];
      $data["old_value"] = $this->request["old_value"];
      $data['update_value'] = $this->request["update_value"];

      if($data['field'] === 'additional_emails' && !empty($data["update_value"]))
      {
        $update_value =  explode(",", $data["update_value"]);
        foreach($update_value as $key => $email)
        {
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
          {
            $this->data_packet->message = "Invalids additional emails";
            $this->data_packet->statusCode = 999;
            return $this->data_packet;  
          }
        }
      }

      if(isset($this->request["seller_nickname"]) && !empty($this->request["seller_nickname"])){
          $data['seller_nickname'] = $this->request["seller_nickname"];
      }

      $result = $this->validateForm($data);
      if($result['valid'] == "not vaild"){
        $this->data_packet->data       = $result; 
        $this->data_packet->message    = $result['message'];
        $this->data_packet->statusCode = 999;
        return $this->data_packet;
      }
      if(isset($_FILES["upload_file"]) && $_FILES["upload_file"]["error"] == 0){
          $data["upload_file"]  = $_FILES["upload_file"];
          $data["img"] = $this->request->post["img"];
          $valid["type"] = $_FILES["upload_file"]["type"];
          $valid["field"]  = "image";
          $valid['size'] = $_FILES["upload_file"]["size"];
          $result = $this->validateForm($valid);
          if($result['valid'] == "not vaild")
          {
             $this->data_packet->data      = $result; 
             $this->data_packet->message   = $result['message'];
             $this->data_packet->statusCode = 999;
             return $this->data_packet;
          }
          $result = $seller_profile->updateBusniessProfile($data);
      } else {
         $result = $seller_profile->updateSellerProfile($data);
        //  exit;
      }
      $this->data_packet->data = $result;
      $this->data_packet->statusCode = 200;
      return $this->data_packet;
    }
  } 


  public function updateSellerBankDetailsAndAddress(){

      $seller_id = $this->customer->getId();
      $this->request['seller_id'] = $seller_id;

      $this->load->model('seller_panel/profile');
      $fields = "nickname";
      $seller_data = $this->model_seller_panel_profile->getSellersInformation($seller_id,$fields);
      $this->request['seller_nickname'] = $seller_data['nickname'];

      $seller_profile = new SellerProfile($this);

      if($this->request['type'] == "bank_details"){
        $valid["type"] = $_FILES["upload_file"]["type"];
        $valid["field"]  = "image";
        $valid["size"]  = $_FILES["upload_file"]["size"];
        $result = $this->validateForm($valid);
        if($result['valid'] == "not vaild"){
         $this->data_packet->data       = $result; 
         $this->data_packet->message    = $result['message'];
         $this->data_packet->statusCode = 999;
         return $this->data_packet;
        }
      }
      if($this->request['type'] == 'gst_details'){
        if( !empty($this->request['gst_provision_id'] ) ){
            $gst_provision_id = $this->request['gst_provision_id'];
            $sellerGSTObject = new SellerGST($this->registry);
            $customer_duplicacy_check = false;
    		$valid_gst_result = $sellerGSTObject->validateGSTNumber($gst_provision_id, $seller_id, $customer_duplicacy_check);
    		$error = "";
    		if ( !empty(trim($gst_provision_id)) && !($valid_gst_result['result'] === true) ) {
                $this->load->language('seller_panel/seller_profile');
    			if($valid_gst_result['message'] == "error_regex") {
    				$error = $this->language->get('error_gst_number');
    			} elseif($valid_gst_result['message'] == "error_checksum") {
    				$error = sprintf($this->language->get('error_gst_checksum'),
    				 									 $valid_gst_result['gst_number_details']['gst_number_without_checksum'].$valid_gst_result['gst_number_details']['gst_number_checksum']);
    			} else if($valid_gst_result['message'] == "error_duplicate") {
                    $user_str = '';
                    if(!empty($valid_gst_result['duplicate_gst_number_details'])) {
    					$duplicate_gst_number_customer = $valid_gst_result['duplicate_gst_number_details'];
                        if(!empty($duplicate_gst_number_customer['telephone'])) {
                            $user_str = 'mobile number ' . substr($duplicate_gst_number_customer['telephone'],0, 2) . 'xxxxx' . substr($duplicate_gst_number_customer['telephone'],7);
                        } else if(!empty($duplicate_gst_number_customer['email'])){
                            $len = strlen(explode('@',$duplicate_gst_number_customer['email'])[0]);
                            $user_str = 'email ' . 'xxxxx' . substr($duplicate_gst_number_customer['email'],$len/2);
                        }
                        $error = sprintf($this->language->get('error_exists_gst'), $gst_provision_id, $valid_gst_result['duplicate_gst_number_entity'], $user_str, $this->url->link('information/contact') );
                    }
    			}
                if(!empty($error)) {
                    $this->data_packet->message    = $error;
                    $this->data_packet->statusCode = 999;
                    return $this->data_packet;
                }
    		}
            
          //$validateGstProvisionalIDValue = validateGSTNo($this->request['gst_provision_id'], true);
          if( !empty($valid_gst_result['pan']) ) {
            $data['pan_number'] = $valid_gst_result['pan'];
            $data['seller_id']  = $seller_id;
            $seller_profile->UpdatePanNumberViaGstin($data);
          } else{
            $this->data_packet->message         = "Please enter your correct GST Provisional ID!";
            $this->data_packet->statusCode = 999;
            return $this->data_packet;
          }

        } 
      
        $valid["type"] = $_FILES["upload_file"]["type"];
        $valid["field"]  = "gst_certificate";
        $valid["size"]  = $_FILES["upload_file"]["size"];
        $result = $this->validateForm($valid);
        if($result['valid'] == "not vaild"  &&  $result['type'] == "get_certificate"){
          $this->data_packet->data    = $result; 
          $this->data_packet->message = $result['message'];
          $this->data_packet->statusCode = 999;
          return $this->data_packet;
        } 
      }
          
          $result = $seller_profile->updateSellerBankDetailsAndAddress($this->request);
          /*
           *  Start code for update profile details in SRM.
          */
          if (isset($this->request['seller_id'])) {       
            if (isset($this->request['type']) && $this->request['type'] == 'address_details') {
                $this->updateLeadSrm($this->request);
              }
          }
                
          $this->data_packet->data = $result;
          $this->data_packet->statusCode = 200;
          return $this->data_packet;  
    }


  private function updateLeadSrm($data) {

    $telephone = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id =".$data["seller_id"] )->row['telephone'];
              
        if (!empty($telephone)) {

          $address = isset($data['address']) ? $data['address'] : '';     
          $seller_city = isset($data['seller_city']) ? $data['seller_city'] : '';
          $pincode = isset($data['pincode']) ? $data['pincode'] : '';
          $seller_country = 'India';
          $seller_zone_name = isset($data['seller_zone_name']) ? $data['seller_zone_name'] : '';              
            
            $lead_data = [                                     
              'address'     => $address,                
                'zip'       => $pincode,
                'country_id'    => $seller_country,
                'state_id'      => $seller_zone_name,
                'city'        => $seller_city,
            ];

          $this->load->model('srm/lead');
          $this->model_srm_lead->updateLead($lead_data, $telephone, 'profile updated', $data["seller_id"]);
      }
  }

  public function sameAsPrimary(){

      $this->request['seller_id'] = $this->customer->getId();

      $this->load->model('seller_panel/profile');
      $fields = "account_holder_name,account_contact_no,primary_contact_name,
                 primary_contact_no, pincode, city, zone_id, address1, address2, pickup_pincode, pickup_city, pickup_zone_id, pickup_address";
      $profile_data = $this->model_seller_panel_profile->getSellersInformation($this->request['seller_id'],$fields);

     if($this->request['type'] == 'same_as_primary_address' && !empty($profile_data['pincode']))
     {
       $this->request['primary_address'] = $profile_data['address1'].' '.$profile_data['address2'];
       $this->request['primary_zone']    = $profile_data['zone_id'];
       $this->request['primary_city']    = $profile_data['city'];
       $this->request['primary_pincode'] = $profile_data['pincode'];

       $this->request['old_pickup_address'] = $profile_data['pickup_address'];
       $this->request['old_pickup_city']    = $profile_data['pickup_zone_id'];
       $this->request['old_pickup_pincode'] = $profile_data['pickup_city'];
       $this->request['old_pickup_zone_id'] = $profile_data['pickup_pincode'];

       $seller_profile = new SellerProfile($this);
       $result =   $seller_profile->updateSellerProfile($this->request);
       $result['result'] = $this->request; 
     }
     else if(!empty($profile_data['primary_contact_name']) || !empty($profile_data['primary_contact_no']))
     {
        $this->request['primary_contact_name'] = $profile_data['primary_contact_name'];
        $this->request['primary_contact_no']   = $profile_data['primary_contact_no'];
        $this->request['old_contact_name']     = $profile_data['account_holder_name'];
        $this->request['old_contact_no']       = $profile_data['account_contact_no'];

        $seller_profile = new SellerProfile($this);
        $result =   $seller_profile->updateSellerProfile($this->request);
        $result['result'] = $this->request;
     }
     else
     {
       $result['result'] = array('msg'=>'error');
       $this->data_packet->data = $result;
       $this->data_packet->statusCode = 999;
       return $this->data_packet;
     }

      $this->data_packet->data = $result;
      $this->data_packet->statusCode = 200;
      return $this->data_packet;  
    
  }

  public function bankDetails(){
        // system/helper/utilities.php
        $json = validateBankIFSC($this->request['ifsc_code']);
        echo $json;
        exit;
  }

  public function changePassword()
  {
      $this->load->model('account/customer');
      $this->model_account_customer->editPassword($this->customer->getEmail(), $this->request['password']);
      $this->data_packet->message = 'password changed successfully';
      $this->data_packet->statusCode = 200;
      return $this->data_packet; 

  }

  public function getState(){
    if(isset($this->request['pincode'])){
      $request['pincode'] = $this->request['pincode'];
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

    }

    $info_to_be_populated = array();
    $city = isset($data[0]['city']) ? $data[0]['city']:'';
    $state = isset($data[0]['zone']) ? $data[0]['zone']:'empty';
    $zone_id = isset($data[0]['zone_id']) ? $data[0]['zone_id']:'';


    $info_to_be_populated['city'] = $city;
    $info_to_be_populated['state'] = $state;
    $info_to_be_populated['zone_id'] = $zone_id;
    echo json_encode($info_to_be_populated);
    exit;
  }

  protected function validateForm($data) {

        if( !empty($data) && $data['field'] == 'gst_certificate' ){
          $img_ext =  substr($data["type"], strpos($data["type"], "/") + 1);
            $img_size = $data['size']/1024; 

            if( !in_array($img_ext, array('png','PNG','jpg','jpeg','pdf','PDF'))){
              $valid['type'] = 'get_certificate';
              $valid["valid"] = "not vaild";
            $valid["message"] = "GST Certificate upload in png, jpg, jpeg, pdf format.";
            return $valid;
            } else if($img_size > 512 ){
              $valid['type'] = 'get_certificate';
              $valid["valid"] = "not vaild";
            $valid["message"] = "GST Certificate should be less then 512 KB.";
            return $valid;  
            } 
        }

        if($data['field'] == "email" && $data['update_value'] != ''){
          if (filter_var($data['update_value'], FILTER_VALIDATE_EMAIL) === false) {
                $valid['type'] = 'email';
                $valid["valid"] = "not vaild";
                $valid["message"] = "Email is not valid";
                return $valid ;
          } else{
              $valid["valid"] = "true";
          }
        }
        if($data["field"] == "image"){
          $img_ext =  substr($data["type"], strpos($data["type"], "/") + 1);
          $img_size = $data['size']/1024;
          if(strtolower(trim($img_ext)) == "jpeg" || strtolower(trim($img_ext)) == "jpg" ){
            if($img_size < 512){
              $valid["valid"] = "true";
              return $valid;
            }else{
              $valid['type']    = 'image';
              $valid["valid"]   = "not vaild";
              $valid["message"] = "Image size should be less then 512 KB.";
              return $valid;  
            }
            
          } else{
            $valid['type']    = 'image';
            $valid["valid"]   = "not vaild";
            $valid["message"] = "upload only image type jpg or jpeg";
            return $valid;
          }
        }
        if($data['field'] == "primary_contact_no" || $data['field'] == "pickup_contact_no" || $data['field'] == "account_contact_no" || $data['field'] == "inventory_contact_no" || $data['field'] == "mobile_no" ){
          $firstChar = $data['update_value'][0];
          if($firstChar == 0 || $firstChar == 1 || $firstChar == 2 || $firstChar == 3 || $firstChar == 4 || $firstChar == 5 ||$firstChar == 6){
            $valid['type']    = 'mobile_number';
            $valid["valid"]   = "not vaild";
            $valid["message"] = "Mobile Number is not valid";
            return $valid;
          }
            if(!preg_match('/^\d{10}$/',$data['update_value'])){
              $valid['type']    = 'mobile_number';
              $valid["valid"]  = "not vaild";
              $valid["message"] = "Mobile Number is not valid";
              return $valid;
            }  else{
              $valid["valid"] = "true";
            }
        }
        if($data["field"] == 'additional_emails' && $data['update_value'] != ''){
            $valid =  array();
            $array_additional_email = explode(',',$data['update_value']);
            foreach($array_additional_email as $emails){
              if (!filter_var($emails, FILTER_VALIDATE_EMAIL)) {
                $valid['type']    = 'email';
                $valid["valid"]   = "not vaild";
                $valid["message"] = "Email is not valid";
              } else{
                  $valid["valid"] = "true";
              }
            }
            return $valid;
        }
  }

  public function terms_data()
  {
    $this->load->model('catalog/information');
    $this->load->model('seller_panel/profile');
    
    $data = array();
    $fields = "company, zone_id,gst_provisional_id, primary_contact_name, city, email, address1, address2";
    $seller_id = $this->customer->getId();
    $result = $this->model_seller_panel_profile->getSellersInformation($seller_id,$fields);

    $information_info = $this->model_catalog_information->getInformation(11);

    if(!empty($result['zone_id']))
    {
      $information_info = str_replace("[COMPANY_GST]", WSB_STATE_GST[$result['zone_id']], $information_info);
    }
    else
    {
      $information_info = str_replace("[COMPANY_GST]", '.............', $information_info);
    }


    if(!empty($result['gst_provisional_id']))
    {
      $information_info = str_replace("[SELLER_GST]", $result['gst_provisional_id'], $information_info);
    }
    else
    {
     $information_info = str_replace("[SELLER_GST]", '.............', $information_info);
    }

    if(!empty($result['primary_contact_name']))
    {
      $information_info = str_replace("[SELLER_NAME]", $result['primary_contact_name'], $information_info);
    }
    else
    {
     $information_info = str_replace("[SELLER_NAME]", '..................', $information_info);
    }

     if(!empty($result['company']))
    {
      $information_info = str_replace("[SELLER_COMPANY]", $result['company'], $information_info);
    }
    else
    {
     $information_info = str_replace("[SELLER_COMPANY]", '..................', $information_info);
    }


    if(!empty($result['address1']))
    {
      $information_info = str_replace("[SELLER_ADDRESS]", $result['address1'].' '.$result['address2'].' '.$result['city'], $information_info);
    }
    else
    {
     $information_info = str_replace("[SELLER_ADDRESS]", '........................', $information_info);
    }

    if(!empty($result['email']))
    {
      $information_info = str_replace("[SELLER_EMAIL]", $result['email'], $information_info);
    }
    else
    {
     $information_info = str_replace("[SELLER_ADDRESS]", '.....................', $information_info);
    }

    
     if ($information_info) {
      $data['heading_title'] = $information_info['title'];
      $data['button_continue'] = $this->language->get('button_continue');
      $data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
      $data['continue'] = $this->url->link('common/home');
      } else {
      $data['heading_title'] = $this->language->get('text_error');
      $data['text_error'] = $this->language->get('text_error');
      $data['button_continue'] = $this->language->get('button_continue');
      $data['continue'] = $this->url->link('common/home');
      }

      $this->data_packet->data           = $data;
      $this->data_packet->statusCode     = 200;
      return $this->data_packet; 

  }


}