<?php
class ControllerSellerPanelProfile extends Controller {

	public function index(){
		$data = array();
		$this->load->autoLoadLanguage('seller_panel/seller_profile',$data);
		
		$this->document->setTitle($data['heading_title']);

	  	if (!$this->customer->isLogged()) { 
	  		$this->response->redirect($this->url->link('account/login', '', 'SSL'));
    	}

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
		$data["zones"] = $get_zones;
		$seller_nickname = $result["nickname"];
		$request['pincode'] = $result['pincode'];
		// $pan_image = $result["pan_image"];
		// $tin_image = $result["tin_image"];
		// $cancel_cheque_image = $result["cancel_cheque_image"];
		// $result['pan_image_url'] = "/system/upload/seller_upload/".$seller_nickname.'/'.$pan_image;
		// $result['tin_image_url'] = DIR_SELLER_UPLOADS.$seller_nickname.'/'.$tin_image;
		// $result["cancel_cheque_image_url"] = DIR_SELLER_UPLOADS.$seller_nickname.'/'.$cancel_cheque_image;
		$additional_emails = $this->model_seller_panel_profile->getAdditionalEmails($seller_id);
		$result["additional_emails"] = $additional_emails;
		//$data["token"] = $this->request->get['ctoken'];
		$data["seller_data"] = $result;
		$data["header"] = 	$this->load->controller('seller_panel/seller_header',$result);
		$data["footer"] = 	$this->load->controller('seller_panel/seller_footer');
		$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/seller_panel/profile.tpl', $data));
	}

	public function getState(){
		if($this->request->post){
			$request['pincode'] = $this->request->post['pincode'];
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

	public function updateSellerProfile(){
		if(isset($this->request->post["seller_id"])){
			$seller_profile = new SellerProfile($this);
			$data = array();
			if(isset($this->request->post["same_as_primary"])){
				$data['same_as_primary']  = 1;
			}
			if(isset($this->request->post["tax_type"])){
				$data["tin_tax_type"] = $this->request->post["tax_type"];
				$data["old_tin_tax_type"] = $this->request->post["old_tin_tax_type"];
			}
			$data['seller_id'] = $this->request->post["seller_id"];
			$data['field']     = $this->request->post["field"];
			$data["old_value"] = $this->request->post["old_value"];
			$data['update_value'] = $this->request->post["update_value"];
			if(isset($this->request->post["seller_nickname"]) && !empty($this->request->post["seller_nickname"])){
					$data['seller_nickname'] = $this->request->post["seller_nickname"];
			}
			$result = $this->validateForm($data);
			if($result['valid'] == "not vaild"){
				echo json_encode($result);
				exit;
			}
			if(isset($_FILES["upload_file"]) && $_FILES["upload_file"]["error"] == 0){
					$data["upload_file"]  = $_FILES["upload_file"];
					$data["img"] = $this->request->post["img"];
					$valid["type"] = $_FILES["upload_file"]["type"];
					$valid["field"]  = "image";
					$valid['size'] = $_FILES["upload_file"]["size"];
					$result =	$this->validateForm($valid);
					if($result['valid'] == "not vaild"){
						echo json_encode($result);
						exit;
					}
					$result = $seller_profile->updateBusniessProfile($data);
			} else {
				 $result = $seller_profile->updateSellerProfile($data);
				//  exit;
			}
			echo json_encode($result);
			exit;
		}
	}
	public function sameAsPrimary(){
		$seller_profile = new SellerProfile($this);
        if(isset($this->request->post['primary_contact_no']) && !empty($this->request->post['primary_contact_no'])){
            $data["update_value"] = $this->request->post['primary_contact_no'];
            $data["field"]        = "primary_contact_no";
            $result = $this->validateForm($data);
            if($result['valid'] == "not vaild"){
                echo json_encode($result);
                exit;
            }
        }
		$result = 	$seller_profile->updateSellerProfile($this->request->post);
		echo json_encode($result);;
		exit;
	}

	protected function validateForm($data) {

				if( !empty($data) && $data['field'] == 'gst_certificate' ){
					$img_ext = 	substr($data["type"], strpos($data["type"], "/") + 1);
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
								$valid["valid"] = "not vaild";
								$valid["message"] = "Email is not valid";
    						return $valid ;
					} else{
							$valid["valid"] = "true";
					}
				}
				if($data["field"] == "image"){
				  $img_ext = 	substr($data["type"], strpos($data["type"], "/") + 1);
				  $img_size = $data['size']/1024;
					if(strtolower(trim($img_ext)) == "jpeg" || strtolower(trim($img_ext)) == "jpg" ){
						if($img_size < 512){
							$valid["valid"] = "true";
							return $valid;
						}else{
							$valid["valid"] = "not vaild";
							$valid["message"] = "Image size should be less then 512 KB.";
							return $valid;	
						}
						
					} else{
						$valid["valid"] = "not vaild";
						$valid["message"] = "upload only image type jpg or jpeg";
						return $valid;
					}
				}
				if($data['field'] == "primary_contact_no" || $data['field'] == "pickup_contact_no" || $data['field'] == "account_contact_no" || $data['field'] == "inventory_contact_no" || $data['field'] == "mobile_no" ){
					$firstChar = $data['update_value'][0];
					if($firstChar == 0 || $firstChar == 1 || $firstChar == 2 || $firstChar == 3 || $firstChar == 4 || $firstChar == 5 ||$firstChar == 6){
						$valid["valid"] = "not vaild";
						$valid["message"] = "Mobile Number is not valid";
						return $valid;
					}
						if(!preg_match('/^\d{10}$/',$data['update_value'])){
							$valid["valid"] = "not vaild";
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
							  $valid["valid"]   = "not vaild";
							  $valid["message"] = "Email is not valid";
							} else{
									$valid["valid"] = "true";
							}
						}
						return $valid;
				}
	}
	public function bankDetails(){

        // system/helper/utilities.php
        $json = validateBankIFSC($this->request->get['ifsc_code']);
        echo $json;
        exit;
	}

	public function getZone($country_id) {
		if (isset($country_id) && !empty($country_id)){
			$this->load->model('localisation/zone');
			$result = $this->model_localisation_zone->getZonesByCountryId($country_id);
			return $result;
		}
	}

	public function updateSellerBankDetailsAndAddress(){
		if($this->request->post){
			$seller_profile = new SellerProfile($this);
			if($this->request->post['type'] == "bank_details"){
				$valid["type"] = $_FILES["upload_file"]["type"];
				$valid["field"]  = "image";
				$valid["size"]  = $_FILES["upload_file"]["size"];
				$result =	$this->validateForm($valid);
				if($result['valid'] == "not vaild"){
					echo json_encode($result);
					exit;
				}
			}
			if($this->request->post['type'] == 'gst_details'){
				if( !empty($this->request->post['gst_provision_id'] ) ){
					$validateGstProvisionalIDValue = validateGSTNo($this->request->post['gst_provision_id'], true);
					if( $validateGstProvisionalIDValue ){
						$data['pan_number'] = $validateGstProvisionalIDValue;
						$data['seller_id'] = $this->request->post['seller_id'];
						$seller_profile->UpdatePanNumberViaGstin($data);
					} else{
						$result['valid'] = "not vaild";
						$result['type'] = "get_certificate";
						$result['text_box'] = 'error_gst_provisional_id';
						$result['message'] = "Please enter correct your GST Provisional ID!";
						echo json_encode($result);
						exit;
					}

				} 
			
				$valid["type"] = $_FILES["upload_file"]["type"];
				$valid["field"]  = "gst_certificate";
				$valid["size"]  = $_FILES["upload_file"]["size"];
				$result =	$this->validateForm($valid);
				if($result['valid'] == "not vaild"  &&  $result['type'] == "get_certificate"){
					echo json_encode($result);
					exit;
				}	
				
			}
			$result = $seller_profile->updateSellerBankDetailsAndAddress($this->request->post);


			/*
			 *	Start code for update profile details in SRM.
			*/
			if (isset($this->request->post["seller_id"])) {				
				if (isset($this->request->post['type']) && $this->request->post['type'] == 'address_details') {
						$this->updateLeadSrm($this->request->post);
				}
            }
            

			if($result["msg"] == "update seller address details"){
					echo json_encode($result);
					exit;
			}  elseif ($result["msg"] == "pickup details updated") {
				  echo json_encode($result);
					exit;
			}  else{
					echo json_encode($result);
					exit;
			}
		}
	}

	/*
	 *	Update Lead in SRM.
	*/
	private function updateLeadSrm($data) {

		$telephone = $this->db->query("SELECT telephone FROM oc_customer WHERE customer_id =".$data["seller_id"] )->row['telephone'];
	            
        if (!empty($telephone)) {

        	$address = isset($data['address']) ? $data['address'] : '';    	
        	$seller_city = isset($data['seller_city']) ? $data['seller_city'] : '';
        	$pincode = isset($data['pincode']) ? $data['pincode'] : '';
        	$seller_country = 'India';
        	$seller_zone_name = isset($data['seller_zone_name']) ? $data['seller_zone_name'] : '';            	
            
            $lead_data = [                                     
            	'address'    	=> $address,                
                'zip'    		=> $pincode,
                'country_id'    => $seller_country,
                'state_id'    	=> $seller_zone_name,
                'city'    		=> $seller_city,
            ];

        	$this->load->model('srm/lead');
        	$this->model_srm_lead->updateLead($lead_data, $telephone, 'profile updated', $data["seller_id"]);
    	}
	}

	public function updateTinType(){
		if($this->request->post){
			$seller_profile = new SellerProfile($this);
			$result = $seller_profile->updateTinType($this->request->post);
		}
	}

	// via gstin --  insert value of pan number in database without request and when we are remove tin block and others then pan and tin code optimize
	// vikas, 2017 
	public function UpdatePanNumberViaGstin(){
		if(!empty($this->request->post)){
			$seller_profile = new SellerProfile($this);
			$seller_profile->UpdatePanNumberViaGstin($this->request->post);	
		} else{
			return false;
		}
	}
}
