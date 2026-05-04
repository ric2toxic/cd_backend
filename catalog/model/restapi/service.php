<?php
class ModelRestapiService extends Model {

	private $returns_helpline_number = "9587896265";
	private $courier_delivery_number = "01414049163";
	private $courier_delivery_number_2 = "7220090698";
	private $credit_helpline_number = "8239778680";
	private $fashcart_helpline_number = "9116138955";
	private $wholesalebox_notification_number = "7230007486";
	private $_top_page_promotions = array(
										"image_url" => "https://d36qiqd7gl7e25.cloudfront.net/img/app_banner/footwear_thumbnail.png",
										"video_id" => "IaYG_jZUUc4",
										"aspect_ratio" => 16/9
									);

    public function checkCustomerByMobile( string $mobile ) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape($mobile) . "'");
        return $query->num_rows;
    }

    public function checkCustomerByEmail( string $email ) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "' ");
        return $query->num_rows;
    }

    public function checkDuplicateMobileCustomer( string $mobile, int $customer_id ) {
		$query = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape($mobile) . "' and customer_id != ".(int)$customer_id." limit 1");
        return $query->num_rows;
    }

    public function checkDuplicateEmailCustomer( string $email, int $customer_id ) {
		$query = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE email = '".$this->db->escape($email)."' and customer_id != ".(int)$customer_id." limit 1");
        return $query->num_rows;
    }


    public function Updatepassword( array $data ) {

		if(isset($data['password'])){
			$password = password_hash($data['password'], PASSWORD_DEFAULT);
		}else{
			$password = "";
		}

		$sql = "UPDATE " . DB_PREFIX . "customer SET password = '" . $this->db->escape( $password ) . "', password_mode = 'new' WHERE ";

		if (isset($data['email']) && !empty($data['email'])) {
			$sql.= " email = '" . $this->db->escape($data['email']) . "' ";
		}

		if (isset($data['mobile']) && !empty($data['mobile'])) {
			$sql.= " telephone = '" . $this->db->escape($data['mobile']) . "' ";
		}

		if (isset($data['customer_id']) && !empty($data['customer_id'])) {
			$sql.= " customer_id = '" . (int)$data['customer_id'] . "' ";
		}

		if($this->db->query($sql)){
			return 1;
		}else{
			return 0;
		}
	}

	public function checkCustomerByMobileAndPassword( string $mobile, string $password ) {

		if(defined('WSB_STORES_ID')) {
			$store_ids =  explode(",", WSB_STORES_ID);
		}else{
			$store_ids = explode(",", $this->config->get('config_store_id'));
		}

		if(is_numeric($mobile))
        {
         	$check_email_or_mobile = "telephone = '".$this->db->escape(trim($mobile))."'";
        } else {
       		$check_email_or_mobile = "email = '".$this->db->escape(trim($mobile))."'";
        }


		$q = "SELECT * FROM " . DB_PREFIX . "customer WHERE ".$check_email_or_mobile;

		$customer_query = $this->db->query($q);

        $login_valid = false;

		if ($customer_query->num_rows)
		{
			if($customer_query->row['password_mode'] != 'new')
            {
              if($customer_query->row['password'] == SHA1($customer_query->row['salt'].SHA1($customer_query->row['salt'].SHA1($password))) || $customer_query->row['password'] == md5($password))
	            {
	            	$login_valid = true;
	            }
            }
            else
            {
               if(password_verify($password, $customer_query->row['password']))
	           {
	         	 $login_valid = true;
	           }
            }
		}

         if($login_valid)
         {
         	if($customer_query->row['password_mode'] != 'new')
            {
              $password_secret = password_hash($password, PASSWORD_DEFAULT);

              $this->db->query("UPDATE " . DB_PREFIX . "customer SET password_mode = 'new', password = '" . $this->db->escape( $password_secret ) . "' WHERE customer_id = '" . (int)$customer_query->row['customer_id'] . "'");
            }

            return $customer_query->row;

         }
		else {
		$registered_user = $this->db->query("SELECT customer_id FROM ".DB_PREFIX."customer WHERE telephone ='".$this->db->escape(utf8_strtolower($mobile))."'")->num_rows;
			if (!$registered_user) {
				return 'not_registered';
			} else {
				return 0;
			}
		}
    }

    public function checkCustomerByEmailAndPassword( string $email, string $password ) {

		$q = "SELECT customer_id, 
                     password, 
                     password_mode, 
                     salt 
              FROM " . DB_PREFIX . "customer 
              WHERE (email = '" . $this->db->escape($email) . "' 
                     OR telephone = '". $this->db->escape($email)."'
                    )";
		$customer_query = $this->db->query($q);

        // if customer exists
		if ($customer_query->num_rows) {
            
			if($customer_query->row['password_mode'] != 'new') {
              if($customer_query->row['password'] == SHA1($customer_query->row['salt'].SHA1($customer_query->row['salt'].SHA1($password))) 
                 || $customer_query->row['password'] == md5($password)) {
                     
                  //Update password_mode as new
                  $password_secret = password_hash($password, PASSWORD_DEFAULT);
                  $this->db->query("UPDATE " . DB_PREFIX . "customer 
                                    SET password_mode = 'new', 
                                        password = '" . $this->db->escape( $password_secret) . "' 
                                    WHERE customer_id = '" . (int)$customer_query->row['customer_id'] . "'");
                
                  return 1;
              } else {
                  return 0;
              }
            } else {
                return (int)password_verify($password, $customer_query->row['password']);
            }
        } else {
            return 'not_registered';
        }
    }
    
	public function getCustomerByMobile( string $mobile ){

        $this->load->model('localisation/zone');

		$query = $this->db->query("SELECT c.*,a.company, a.city, a.postcode, a.zone_id, a.country_id, a.address_1, a.address_2 FROM " . DB_PREFIX . "customer c LEFT JOIN oc_address a ON c.customer_id = a.customer_id WHERE c.telephone = '" . $this->db->escape($mobile) . "' ");
        if($query->num_rows)
		    return $query->row;
        else
            return array();
    }

    public function getCustomerByEmail( string $email ) {
		$query = $this->db->query("SELECT c.*,a.company, a.city, a.postcode, a.zone_id, a.country_id, a.address_1, a.address_2 FROM " . DB_PREFIX . "customer c LEFT JOIN oc_address a ON c.customer_id = a.customer_id WHERE c.email = '" . $this->db->escape($email) . "' ");
        return $query->row;
    }
	public function checkCustomerByID( int $id ){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$id . "'");
        return $query->row;
    }

    public function isCustomerFranchise( int $cid ){
		$sql = "SELECT franchise_status FROM " . DB_PREFIX . "franchise_data WHERE franchise_id = '". (int)$cid . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            return (int)$query->row['franchise_status'];
        }
        
        return 0;
    }

	public function checkCustomerByMobileAndOtp( string $mobile, string $otp ) {
		$query = $this->db->query("SELECT c.*,a.company FROM " . DB_PREFIX . "customer c LEFT JOIN oc_address a ON c.customer_id = a.customer_id WHERE c.telephone = '" . $this->db->escape($mobile) . "' AND c.otp = '" . $this->db->escape($otp) . "'");
        return $query->row;
    }
    public function checkCustomerByEmailAndOtp( string $email, string $otp ){
		$query = $this->db->query("SELECT c.*,a.company FROM " . DB_PREFIX . "customer c LEFT JOIN oc_address a ON c.customer_id = a.customer_id WHERE c.email = '" . $this->db->escape($email) . "' AND c.otp = '" . $this->db->escape($otp) . "'");
        return $query->row;
    }
    public function checkCustomerByIdwithAdd( int $id ) {
		$query = $this->db->query("SELECT c.firstname, c.lastname, c.telephone, a.company FROM " . DB_PREFIX . "customer c LEFT JOIN oc_address a ON c.customer_id = a.customer_id WHERE c.customer_id = '" . (int)$id . "' ");
        if(!empty($query->row))
            return $query->row;
        else
            return false;
    }
    public function checkUserByAccessToken($access_token, $user_id){
        
        $access_token = trim($access_token);
        // If empty access token (meaning, invalid token), return 0 (false)
        if ( empty($access_token) ) 
            return 0;

        $sql = "SELECT ws_access_token 
                FROM " . DB_PREFIX . "customer 
                WHERE customer_id = '" . (int)$user_id . "'";
        $query = $this->db->query($sql);
        return (int)(($query->row['ws_access_token'] ?? '') === $access_token);
    }

    public function addCustomer( array $data ){
		$this->event->trigger('pre.customer.add', $data);

        if(!empty($data['mobile'])){

            if(is_numeric($data['mobile']))
            {
                $mobile_verified = $this->checkEmailMobileVerified($data['mobile']);
                $mobile = $data['mobile'];
                $email = "";
                $email_verified = 0;
            }
            else
            {
                $email_verified = $this->checkEmailMobileVerified($data['mobile']);
                $email = $data['mobile'];
                $mobile = "";
                $mobile_verified = 0;
            }
        }

        if(isset($data['email']) && !empty($data['email']))
        {
           $email_verified = $this->checkEmailMobileVerified($data['email']);
           $email = $data['email'];
        }

		if(isset($data['access_token'])){
			$access_token = $data['access_token'];
		}else{
			$access_token = "";
		}

		if(isset($data['password'])){
			$password = $this->db->escape(password_hash($data['password'], PASSWORD_DEFAULT));
		}else{
			$password = "";
		}
		if(isset($data['otp'])){
			$otp = $data['otp'];
		}else{
			$otp = "";
		}

        if (isset($data['first_name'])){
            $first_name = $data['first_name'];
        } else {
            $first_name = "";
        }

        if (isset($data['last_name'])){
            $last_name = $data['last_name'];
        } else {
            $last_name = "";
        }

		if(isset($data['gcm_id']) && !empty($data['gcm_id']) && !empty($data['device_id'])){
			$gcm_id = $data['gcm_id'];
			$device_id = $data['device_id'];
			$sql = "UPDATE " . DB_PREFIX . "customer
					SET
						ws_gcm_registration_id = ''
					WHERE
						device_id = '".$this->db->escape($device_id)."'
						OR
						ws_gcm_registration_id = '".$this->db->escape($data['gcm_id'])."'
					";
			$this->db->query($sql);
		}

		if(!empty($data['gcm_id'])){
			$ws_gcm_registration_id = " ws_gcm_registration_id = '" . $this->db->escape($data['gcm_id']) . "', ";
		}else{
			$ws_gcm_registration_id = "";
		}

        if( isset($this->restapi->getRequestHeader()['REQUEST_BY']) && strtoupper($this->restapi->getRequestHeader()['REQUEST_BY']) == 'IOS_APP') {
		    $signup_from = 'IOS_APP';
        } else if(isset($data['call_from']) && $data['call_from'] == 'crm'){
            $signup_from = 'CRM';
        } else {
            $signup_from = 'ANDROID_APP';
        }

        if(isset($data['ip'])){
            $ip = $data['ip'];
        } else {
            $ip = '';
        }

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer
							SET
								mobile_country_code = '". $this->db->escape($data['country_code']) ."',
								telephone = '" . $this->db->escape($mobile) . "',
								firstname = '" . $this->db->escape($first_name) . "',
								lastname = '" . $this->db->escape($last_name) . "',
								email = '" . $this->db->escape($email) . "',
								password= '".$this->db->escape($password)."',
								password_mode = 'new',
								device_id = '" . $this->db->escape($data['device_id']) . "',
								otp = '" . $this->db->escape($otp) . "',
								". $ws_gcm_registration_id ."
								ws_access_token = '".$this->db->escape($access_token)."',
								mobile_verified = '" . $this->db->escape(isset($mobile_verified) ? $mobile_verified : 0) . "',
								email_verified = '" . $this->db->escape(isset($email_verified) ? $email_verified : 0) . "',
								date_added = NOW() ,
								signup_from = '".$this->db->escape($signup_from)."',
								ip = '".$this->db->escape($ip)."'"
						);

		if(isset($data['master_id']) && $data['master_id'] > 0){
            $customer_id = $this->db->getLastId();

            $master_customer_id = $data['master_id'];
        }else{
            $customer_id = $this->db->getLastId();
            $master_customer_id = $customer_id;
        }

        // update master id when new customer created account
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET master_id = '" .(int)$master_customer_id . "' WHERE customer_id = '" .(int)$customer_id . "'");

		if(isset($data['call_from']) && $data['call_from'] == 'crm'){
			$lead_data = [
				'signup_date'	=> date('Y-m-d H:i:s')
			];
		}else{
			$lead_data = [
				'email' => $this->db->escape($email),
				'app_installed' => 1,
				'priority'		=> 1 ,
			];
		}

		$this->load->model('lead/lead');

		$this->model_lead_lead->updateLead($lead_data, $this->db->escape($data['mobile']), 'app registration', $customer_id);

		$address_id = $this->db->getLastId();

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");

		return $customer_id;
    }
    public function updateCsvPath( int $user_id, string $csv_path ){
		$sql = "UPDATE " . DB_PREFIX . "customer SET contact_csv_url = '".$this->db->escape($csv_path)."' WHERE customer_id = '".(int)$user_id."'";
		$this->db->query($sql);
	}
	public function updateImgPath( int $user_id, int $order_id, string $img_path ){
		$sql = "UPDATE " . DB_PREFIX . "order SET bank_slip_image = '".$this->db->escape($img_path)."' WHERE customer_id = '".(int)$user_id."' AND order_id=  '".(int)$order_id."'";
		$this->db->query($sql);
	}

	public function createCustomerProfile( array $data ) {

		$headers = getallheaders();
		$request_by = $headers['REQUEST_BY'] ?? "";

		$call_by_api = '';

		if(!empty($data['call_by_api'])) {
			$call_by_api = $data['call_by_api'];
		}

		$app_version_code = $data['app_version_code'];
		$this->event->trigger('pre.customer.add', $data);

		$customer_id = $data['user_id'];
		$customer = $this->checkCustomerByID($customer_id);

		$update_customer_data = array();

		$company   = $data['business_name'];

		$gst_number = "";
		$gst_valid 	= 0;

        //$update_customer_data['updated_gst_number'] = $updated_gst_number = "";
		/*** GST Number check starts here ***/
		if(isset($data['gst_number']) && !empty($data['gst_number'])) {

			$gst_number = strtoupper($data['gst_number']);
			$gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
			$gst_number = trim($gst_number);

			$this->load->language('account/edit');
			$this->load->model('account/customer');
			$customer_details = $this->model_account_customer->getCustomer($customer_id);

			if( empty($customer_details['gst_number']) || ($customer_details['gst_number'] != $gst_number) ) {

				$customerObj = new Customer($this->registry);
				$valid_gst_result = $customerObj->gstObject->validateGSTNumber($gst_number, $customer_id);

				if ( !empty($gst_number) && !($valid_gst_result['result'] === true) ) {
					$error = "";

					if($valid_gst_result['message'] == "error_regex") {
						$error = $this->language->get('error_gst_number');

					} elseif($valid_gst_result['message'] == "error_checksum") {
						$error = sprintf($this->language->get('error_gst_checksum'),
							$valid_gst_result['gst_number_details']['gst_number_without_checksum'].$valid_gst_result['gst_number_details']['gst_number_checksum']);
					} else if($valid_gst_result['message'] == "error_duplicate") {

                        $gst_valid = 1;
                    }

                    if( !$gst_valid
                    	&& ( $call_by_api == 'update_profile'
                    		 || (( strtoupper($request_by) == "ANDROID_APP" || strtoupper($request_by) == "ANDROID APP") && $app_version_code > 72 )
                    		 || ( strtoupper($request_by) == "IOS_APP" && $app_version_code > 2 ))) {

                    	return array('error' => $error);
                    }
                } else if($valid_gst_result['message'] == 'valid') {
                	$gst_valid = 1;
                }

                if((!empty($gst_number) && $gst_valid == 1)) {
                	$customerObj->setGSTNumber($gst_number);
                }
            }
        } /*** GST Number validation Ends here ***/

        $updated_helpdesk_id 	= "";
        if (isset($data['helpdesk_id']) && !empty($data['helpdesk_id'])) {
        	$update_customer_data['updated_helpdesk_id'] = $updated_helpdesk_id   = " helpdesk_id = '".$this->db->escape($data['helpdesk_id'])."' ";
        }

        if(isset($data['password']) && !empty($data['password']))
        {
        	$salt      = substr(md5(uniqid(rand(), true)), 0, 9);
        	$password = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        else
        {
        	$salt = '';
        	$password = '';
        }

        /*****Email and Mobile no verification; don't store if not valid****/
		// Never update mobile for .in and email for .co
        if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
        	if(!empty($data['email'])) {
        		unset($data['email']);
        	}
        }

		//***** check valid mobile
        if(!empty($data['mobile'])) {
        	if(!preg_match('/^[+]?[0-9]+$/', $data['mobile'])) {
        		unset($data['mobile']);
        		if( $call_by_api == 'update_profile'
	        		 || ( ( strtoupper($request_by) == "ANDROID_APP" || strtoupper($request_by) == "ANDROID APP") && $app_version_code > 72 )
	        		 || ( strtoupper($request_by) == "IOS_APP" && $app_version_code > 2 )) {

        			return array('error'=>'Invalid Mobile Number !!!');
        		}
        	}

        	// check whether this mobile no is already present for some other customer
        	$customer_detail = $this->getCustomerByMobile($data['mobile']);

        	if(!empty($customer_detail) && (int)$customer_detail['customer_id'] != (int)$customer_id) {
        		// means this mobile no is registered with another customer account.
        		unset($data['mobile']);
        		if( $call_by_api == 'update_profile'
	        		 || ( ( strtoupper($request_by) == "ANDROID_APP" || strtoupper($request_by) == "ANDROID APP") && $app_version_code > 72 )
	        		 || ( strtoupper($request_by) == "IOS_APP" && $app_version_code > 2 )) {

        			return array('error'=>'Mobile Number is tagged to other customer !!!');
        		}
        	}
        }

		// check valid email
        $emailval = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
        if(!empty($data['email']) && !preg_match($emailval, strtolower( $data['email'] ))) {
        	unset($data['email']);
        	if( $call_by_api == 'update_profile'
        		 || ( ( strtoupper($request_by) == "ANDROID_APP" || strtoupper($request_by) == "ANDROID APP") && $app_version_code > 72 )
        		 || ( strtoupper($request_by) == "IOS_APP" && $app_version_code > 2 )) {

        		return array('error'=>'Invalid Email !!!');
        	}
        }

        if(isset($data['email'])){
        	$email = trim($data['email']);
        }

        if(!empty($data['mobile'])) {
        	$mobile = $data['mobile'];
        }

        $device_id_update = "";
        if(isset($data['device_id']) && !empty($data['device_id'])){
        	$device_id = $data['device_id'];
        	$update_customer_data['device_id_update'] = $device_id_update = " device_id = '".$this->db->escape($device_id)."' ";
        }else{
        	$device_id = '';
        }

        if(isset($data['gcm_id']) && !empty($data['gcm_id'])){
        	$gcm_id = $data['gcm_id'];
        	if(!empty($device_id)){
        		$updated_gcm_by_divice_id = " OR device_id = '".$device_id."' ";
        	}else{
        		$updated_gcm_by_divice_id = " ";
        	}
			$update_customer_data['updated_gcm'] = $updated_gcm = " ws_gcm_registration_id = '".$this->db->escape($gcm_id)."' ";

		} else {
			$updated_gcm = " ";
		}

		if(isset($data['customer_type_id']) && !empty($data['customer_type_id'])){
			$customer_type_id = trim($data['customer_type_id']);
		}
		$name = explode(' ',$data['name']);
		if(count($name) > 1){
			$firstname = $name['0'];
			$update_customer_data['firstname'] = " firstname = '".$this->db->escape($firstname)."' ";
			unset($name['0']);
			$lastname = implode(' ',$name);
			$update_customer_data['lastname'] = " lastname = '".$this->db->escape($lastname)."' ";
		}else{
			$firstname = $data['name'];
			$lastname = '';
			if(!($firstname == '')){
				$update_customer_data['firstname'] = " firstname = '".$this->db->escape($firstname)."' ";
				$update_customer_data['lastname'] = " lastname = '".$this->db->escape($lastname)."' ";
			}
		}

		$first_app_install = $this->checkFirstTimeAppInstallation($customer_id);

		if ($first_app_install) {
			$app_install_date = date('Y-m-d');
		} else {
			$app_install_date = '';
		}

		$this->load->model('lead/lead');
		$lead_data = [
			'name' => $firstname,
			'app_installed' => 1,
			'business_name' => $company
		];

		if(empty($company)){
			unset($lead_data['business_name']);
		}
		if(empty($firstname)){
			unset($lead_data['name']);
		}

        if(!empty($app_install_date)){
            $lead_data['app_installed'] = '1';
            $lead_data['app_install_date'] = $app_install_date;
        }


		if(isset($data['gcm_id']) && !empty($data['gcm_id'])){
			$lead_data['ws_gcm_registration_id'] = $data['gcm_id'];
		}

		$lead = $this->model_lead_lead->updateLeadFromCustomerId($lead_data, $customer_id);

		$updated_telephone = '';
		$updated_email = '';

		if(isset($email) && $email != ''){
			$update_customer_data['updated_email'] = $updated_email = " email = '".$this->db->escape($email)."' ";
			$lead_data['email'] = $email;
		}

		if(isset($mobile) && $mobile != ''){
			$update_customer_data['updated_telephone'] = $updated_telephone = " telephone = '".$this->db->escape($mobile)."' ";
			$lead_data['mobile'] = $mobile;
		}

		if(isset($password) && $password != ''){
			$update_customer_data['updated_password'] = $updated_password = " salt = '".$this->db->escape($salt)."', password = '".$this->db->escape($password)."', password_mode = 'new' ";
		}
		else{ $updated_password = ""; }

		if(isset($data['mobile_verified'])){
			$update_customer_data['updated_mobile_verified'] = $updated_mobile_verified = " mobile_verified = '".$this->db->escape($data['mobile_verified'])."' ";
		}
		else{ $updated_mobile_verified = ""; }

		if(isset($data['email_verified'])){
			$update_customer_data['updated_email_verified'] = $updated_email_verified = " email_verified = '".$this->db->escape($data['email_verified'])."' ";
		}
		else{ $updated_email_verified = ""; }

		if(isset($customer_type_id) && !empty($customer_type_id)){
			$update_customer_data['updated_cust_type'] = $updated_cust_type = " customer_type_id = '".$this->db->escape($customer_type_id)."' ";

			/*** If customer is online reseller; we will make dropshipper column as 1 in db ***/
			if( strpos($customer_type_id, '2') !== false
				&& !$this->customerHasAtleastOneDeliveredOrder( (int)$customer_id ) ) {
				// replace $this->customerHasAtleastOneDeliveredOrder with CustomerOrderInfo->getAllOrdersByCustomerId()
				// once CustomerOrderInfo's constructor parameter is fixed
				$update_customer_data['updated_dropshipper'] = $updated_dropshipper = " is_dropshipper = '1' ";
			}

		}else{
			$updated_cust_type = "";
		}

		if((isset($data['customer_vpa']))) {
			$update_customer_data['upi_vpa'] = $updated_upi_vpa 	= " upi_vpa = '".$this->db->escape($data['customer_vpa'])."' ";
		}

		if(!empty($update_customer_data)){
			$update_customer_data = implode(',', $update_customer_data);
			$sql = "UPDATE " . DB_PREFIX . "customer
			SET ".$update_customer_data." WHERE customer_id = '" . $this->db->escape($customer_id) . "'";
			$this->db->query($sql);
		}

		// add referral code if valid; 25 June 2019 (Anurag)
		if ( !empty( $data['referral_code'] )) {
			
			$this->load->model('checkout/coupon');

			if ( !empty( $data['utm_info']['utm_campaign'] ) && strtoupper( $data['utm_info']['utm_campaign'] ) == "EKOAFF" ) {

				$utm_data = array();
				$utm_data['utm_campaign'] = $data['utm_info']['utm_campaign'] ?? "";
				$utm_data['utm_medium']   = $data['utm_info']['utm_medium'] ?? "";
				$utm_data['utm_source']   = $data['utm_info']['utm_source'] ?? "";
				$utm_data['utm_channel']  = $data['utm_info']['utm_channel'] ?? "";
				$utm_data['utm_term']     = $data['utm_info']['utm_term'] ?? "";

				$add_status = $this->model_checkout_coupon->addReferralCode( (int) $customer_id, $data['referral_code'], $utm_data );

			} else {
				$add_status = $this->model_checkout_coupon->addReferralCode( (int) $customer_id, $data['referral_code'] );
			}

			if ( ! $add_status['status'] ) {
				return array( 'error_referral_code' => $add_status['message'] );
			}
		}

		// stamp lead mobile no. if signup is from sms or any url sent to a perticular mobile (lead)
		if ( !empty( $data['lead_mobile'] )) {
			
			$lead_mobile = base64_decode( trim( $data['lead_mobile'] ));
				
			if ( preg_match( '/^[+]?[0-9]+$/', $lead_mobile )) {
				$this->addLeadSignupDetail( (int) $customer_id, $lead_mobile );
			}
		}

		$pin_code = '';
		$arr_update_field['firstname'] = $firstname;
		$arr_update_field['lastname'] = $lastname;
		$arr_update_field['address_1'] = '';
		$arr_update_field['address_2'] = '';
		$arr_update_field['postcode'] = '';
		$arr_update_field['city'] = '';
		$arr_update_field['zone_id'] = '';
		$arr_update_field['telephone'] = isset($mobile) ? $mobile : '';
		$arr_update_field['country_id'] = '';
		$arr_update_field['custom_field'] = '';
		$arr_update_field['company'] = $company;

		$all_empty = 1;

		if(!empty($mobile)){
			$all_empty = 0;
		}
		if (!empty($firstname)) {
			$arr_update_field['firstname'] = $this->db->escape($firstname);
			$all_empty = 0;
		}
		if (!empty($lastname)) {
			$arr_update_field['lastname'] = $this->db->escape($lastname);
			$all_empty = 0;
		}
		if (!empty($company)) {
			$arr_update_field['company'] = $this->db->escape($company);
			$all_empty = 0;
		}

		if (!empty($data['pin_code'])) {
			$all_empty = 0;
			$arr_update_field['postcode'] = $this->db->escape($data['pin_code']);
			$lead_data['zip'] = $arr_update_field['postcode'];
		}
		if (!empty($data['city'])) {
			$all_empty = 0;
			$arr_update_field['city'] = $this->db->escape($data['city']);
			$city_id = $this->model_lead_lead->getCrmCityId($data['city']);
			$lead_data['city_id'] = $city_id;
		}
		if (!empty($data['address'])) {
			$all_empty = 0;
			$arr_update_field['address_1'] = $this->db->escape($data['address']);
		}
		if (!empty($data['address_2'])) {
			$all_empty = 0;
			$arr_update_field['address_2'] = $this->db->escape($data['address_2']);
		}
		if (!empty($data['zone_id'])) {
			$all_empty = 0;
			$arr_update_field['zone_id'] = $this->db->escape($data['zone_id']);
			$lead_data['state_id'] = $arr_update_field['zone_id'];

		}
		if (!empty($data['country_id'])) {
			$all_empty = 0;
			$arr_update_field['country_id'] = $this->db->escape($data['country_id']);
			$lead_data['country_id'] = $arr_update_field['country_id'];

		}

        $this->model_lead_lead->updateLead($lead_data,$customer['telephone'], 'Country updation from APP', $customer_id);
		$default_address = array();
		$this->load->model('account/address');
		$default_address = $this->model_account_address->getDefaultAddress($customer_id);

		if(!empty($arr_update_field) && $all_empty == 0) {

			$update_fields = implode(', ', array_map(
				function ($v, $k) { return sprintf("%s='%s'", $k, $v); },
				$arr_update_field,
				array_keys($arr_update_field)
			));

			$arr_update_field['default'] = 1;
			if ($default_address !== false) {
				$this->model_account_address->editAddress($default_address['address_id'], $arr_update_field, $customer_id);
			}else{
				$default_address['address_id'] = $this->model_account_address->addAddress($arr_update_field, $customer_id);
			}
		}

      	$customer_sql = "SELECT
							c.*,
							a.company,
							a.zone_id,
							a.city,
							a.country_id,
							a.address_1,
							a.address_2,
							a.postcode
						FROM
							" . DB_PREFIX . "customer c
							LEFT JOIN oc_address a ON c.customer_id = a.customer_id
						WHERE
							c.customer_id = '" . $this->db->escape($customer_id) . "'";

		if ($default_address !== false) {
			$customer_sql .= " AND a.address_id = '".$default_address['address_id']."'";
		}

		$query_result = $this->db->query($customer_sql);

		return $query_result->row;
	}

	public function UpdateOtpByEmail( string $email, string $device_id, string $otp = "1111", string $gcm_id ) {
		if(!empty($email)) {
			if(!empty($gcm_id)){
				$ws_gcm_registration_id = " ws_gcm_registration_id = '" . $this->db->escape($gcm_id) . "', ";
			}else{
				$ws_gcm_registration_id = "";
			}
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET otp = '".$this->db->escape($otp)."', ".$ws_gcm_registration_id." WHERE email = '" . $this->db->escape($email) . "'");
			return 1;
		}else{
			return 0;
		}

	}
	public function UpdateOtp( string $mobile, string $device_id, string $otp = "1111", string $gcm_id) {
		if(!empty($mobile)){
			if(!empty($gcm_id)){
				$ws_gcm_registration_id = " ws_gcm_registration_id = '" . $this->db->escape($gcm_id) . "', ";
			}else{
				$ws_gcm_registration_id = "";
			}
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET otp = '".$this->db->escape($otp)."', ".$ws_gcm_registration_id." WHERE telephone = '" . $this->db->escape($mobile) . "'");
			return 1;
		}else{
			return 0;
		}

	}
	/**
	 * Only For Update Gcm id for all previous users(29-01-2016 Ravindra Singh)
	 * Check And Update gcm id according to user
	 **/
	public function updategcmid( int $user_id, string $gcm_id) {
		if(!empty($gcm_id) && !empty($user_id)){
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET ws_gcm_registration_id = '" . $this->db->escape($gcm_id) . "' WHERE customer_id = '" . (int)$user_id . "' AND ws_gcm_registration_id = ''");
			return 1;
		}else{
			return 0;
		}
	}

	public function UpdateCartOtp( int $customer_id, string $otp ) {
		if($this->db->query("UPDATE " . DB_PREFIX . "customer SET otp = '".$this->db->escape($otp)."' WHERE customer_id = '" . (int)$customer_id . "'")){
			return 1;
		}else{
			return 0;
		}
	}

    public function UpdateCartShortlistOtp( int $customer_id, string $otp ) {
		if($this->db->query("UPDATE " . DB_PREFIX . "customer SET otp = '".$this->db->escape($otp)."' WHERE customer_id = '" . (int)$customer_id . "'")){
			return 1;
		}else{
			return 0;
		}
	}

	public function VeriFyMobile( int $customer_id, string $access_token, string $device_id ) {
		if($device_id == 0){
			if($this->db->query("UPDATE " . DB_PREFIX . "customer SET ws_access_token = '" . $this->db->escape($access_token) . "', otp = '' WHERE customer_id = '" . (int)$customer_id . "'")){
				return 1;
			}else{
				return 0;
			}
		}else{
			if($this->db->query("UPDATE " . DB_PREFIX . "customer SET ws_access_token = '" . $this->db->escape($access_token) . "', otp = '', device_id = '" . $this->db->escape($device_id) . "' WHERE customer_id = '" . (int)$customer_id . "'")){
				return 1;
			}else{
				return 0;
			}
		}
	}
	public function getSubCategories( int $parent_id = 0 ) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
		$categories = array();
        $this->load->model('tool/image');
		$i = 0;
		foreach ($query->rows as $result) {
		    if(NGINX_ENABLED == 1){
                $img = $this->model_tool_image->getOriginalImage($result['image']);
            }
            else{
                if(!empty($result['image'])){
                    $img = $this->config->get('config_url').'image/'.$result['image'];
                }else{
                    $img = $this->config->get('config_url').'image/no_image.png';
                }
            }

            $view_type_grid = true;
            $request_data = $this->restapi->getRequestData();

            $single_coulmn_parent_category = explode(",", SINGLE_COLUMN_LAYOUT_PARENT_CATEGORIES);
            if(in_array($parent_id, $single_coulmn_parent_category)) {
                $view_type_grid = false;
            }
			if($i == 0){
				$categories[] = array(
					'sub_category_id' => $parent_id,
					'sub_category_name'      => "All",
                    'view_type_grid' => $view_type_grid

				);
			}
			$categories[] = array(
				'sub_category_id' => $result['category_id'],
				'sub_category_name'      => html_entity_decode($result['name']),
				'image'      => $img,
                'view_type_grid' => $view_type_grid
			);
			$i++;
		}
		return $categories;
	}

    public function getSubMenu( int $parent_id = 0 ) {
	    $sql = "SELECT mi.link_title as name, mi.value as category_id, c.image FROM " . DB_PREFIX . "menu_item mi LEFT JOIN " . DB_PREFIX . "category c ON (mi.value = c.category_id)  WHERE mi.parent_id = '" . (int)$parent_id . "' AND mi.language_id = '" . (int)$this->config->get('config_language_id') . "' AND mi.link_type='category' AND mi.status = '1' ORDER BY mi.position, LCASE(mi.link_title)";
	    $query = $this->db->query($sql);
        $categories = array();
        $this->load->model('tool/image');
        $i = 0;
        foreach ($query->rows as $result) {
            if(NGINX_ENABLED == 1){
                $img = $this->model_tool_image->getOriginalImage($result['image']);
            }
            else{
                if(!empty($result['image'])){
                    $img = $this->config->get('config_url').'image/'.$result['image'];
                }else{
                    $img = $this->config->get('config_url').'image/no_image.png';
                }
            }

            $view_type_grid = true;
            $request_data = $this->restapi->getRequestData();

            $single_coulmn_parent_category = explode(",", SINGLE_COLUMN_LAYOUT_PARENT_CATEGORIES);
            if(in_array($parent_id, $single_coulmn_parent_category)) {
                $view_type_grid = false;
            }

            $sub_cat_arr = array();
            $sub_cat_arr['sub_category_id'] = $result['category_id'];
            $sub_cat_arr['sub_category_name'] = html_entity_decode($result['name']);
            $sub_cat_arr['image'] = $img;
            $sub_cat_arr['view_type_grid'] = $view_type_grid;

            if ( $result['category_id'] == '291' ) {
            	$sub_cat_arr['top_page_promotions'] = $this->_top_page_promotions;
            }

            $categories[] = $sub_cat_arr;
            $i++;
        }
        return $categories;
    }
	public function getCategories( int $parent_id = 0 ){
	    $sql = "SELECT c.category_id, cd.name, c.image, c.price_range FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)";
	    $query = $this->db->query($sql);
		$categories = array();
        $this->load->model('tool/image');
		if ($query->num_rows > 0) {
			// Loop through the returned rows for processing
			foreach ($query->rows as $result) {
                if(NGINX_ENABLED == 1){
                    $img = $this->model_tool_image->getOriginalImage($result['image']);
                }
                else{
                    if(!empty($result['image'])){
                        $img = $this->config->get('config_url').'image/'.$result['image'];
                    }else{
                        $img = $this->config->get('config_url').'image/no_image.png';
                    }
                }

				$subCategories = $this->getSubCategories($result['category_id']);
				$category_images = $this->getCategoryImages($result['category_id']);

				$categories[] = array(
					'category_id' 		=> $result['category_id'],
          			'price_range' 		=> $result['price_range'],
					'name' 				=> html_entity_decode($result['name']),
					'image' 			=> $img,
					'images' 			=> $category_images,
					'sub_categories' 	=> $subCategories
				);
			}
		}

		return $categories;
	}

  public function getMenus( string $menu_name ) {
	    $sql = "SELECT DISTINCT mi.* FROM " . DB_PREFIX . "menu_item mi INNER JOIN ".DB_PREFIX."menu m on m.id=mi.menu_id and m.store_id=mi.store_id
				WHERE m.name = '" . $this->db->escape($menu_name) . "'
				AND mi.status = '1'
				AND mi.language_id = '". (int)$this->config->get('config_language_id') ."'
				ORDER BY mi.position ASC, mi.parent_id ASC";
		$query = $this->db->query($sql);
		return $query->rows;
	}

    public function getMenu(){
        $sql = "SELECT mi.id, mi.value as category_id, mi.link_title as name, c.image, c.price_range FROM " . DB_PREFIX . "menu m LEFT JOIN " . DB_PREFIX . "menu_item as mi ON m.id = mi.menu_id LEFT JOIN " . DB_PREFIX . "category c ON mi.value = c.category_id WHERE m.name = 'mobile' AND link_type = 'CATEGORY' AND type='PARENT' AND mi.language_id = '" . (int)$this->config->get('config_language_id') . "' AND mi.status = '1' ORDER BY mi.position, LCASE(name)";
        $query = $this->db->query($sql);
        $categories = array();
        $this->load->model('tool/image');
        if ($query->num_rows > 0) {
            // Loop through the returned rows for processing
            foreach ($query->rows as $result) {
				if(NGINX_ENABLED == 1){
                    $img = $this->model_tool_image->getOriginalImage($result['image']);
                }
                else{
					if(!empty($result['image'])){
						$img = $this->config->get('config_url').'image/'.$result['image'];
					}else{
						$img = $this->config->get('config_url').'image/no_image.png';
					}
				}
				$subCategories = $this->getSubMenu($result['id']);
				$category_images = $this->getCategoryImages($result['category_id']);

				$category_arr = array();
				$category_arr['category_id'] = $result['category_id'];
				$category_arr['name'] = html_entity_decode($result['name']);
				$category_arr['image'] = $img;
				$category_arr['images'] = $category_images;
				$category_arr['price_range'] = $result['price_range'];
				$category_arr['sub_categories'] = $subCategories;

				if ( $result['category_id'] == '126' ) {
					$category_arr['top_page_promotions'] = $this->_top_page_promotions;
				}

				$categories[] = $category_arr;
            }
        }
        return $categories;
    }

	public function getCategoryImages( int $category_id ){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_images WHERE category_id = '" . (int)$category_id . "'")->rows;
		$category_images = array();
        $this->load->model('tool/image');
		foreach($query as $category_image) {
			$category_images[] = array(
                'image' 		=> $this->model_tool_image->getOriginalImage($category_image['image']),
				'image_height' 	=> $category_image['image_height'],
				'image_width' 	=> $category_image['image_width'],
				'sort_order' 	=> $category_image['sort_order'],
        		'db_image' 		=> $category_image['image']
			);
		}
		return $category_images;
	}

	public function getProducts( array $data ){
		if($this->currency->getCode() == 'INR'){
			$price_values = $this->currency->currencies['INR']['value'];
		}else{
			$price_values = $this->currency->currencies['USD']['value'];
		}
		$sql = "SELECT p.product_id,
                       (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating,
					   (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
			/***
			 * Get Products According to seller (Quality Expectations)
			 ***/
			if (isset($data['seller']) && !empty($data['seller'])) {
				$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id) INNER JOIN ".DB_PREFIX."ms_seller mss ON (mp.seller_id = mss.seller_id)";
			}

		}else {
			$sql .= " FROM " . DB_PREFIX . "product p";
			if (isset($data['seller']) && !empty($data['seller'])) {
				$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id) INNER JOIN ".DB_PREFIX."ms_seller mss ON (mp.seller_id = mss.seller_id)";
			}
		}

        //echo $sql; exit;
        $out_of_stock_id = 0;
        $out_of_stock_query = $this->db->query("SELECT ss.stock_status_id FROM " . DB_PREFIX . "stock_status ss WHERE UPPER(ss.name) = 'OUT OF STOCK' AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "'");
        if ($out_of_stock_query->num_rows)
            $out_of_stock_id = (int)($out_of_stock_query->row['stock_status_id']);

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.quantity > 0 AND p.stock_status_id != '" . $out_of_stock_id . "' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}
				$sql_check = "SELECT DISTINCT (filter_group_id) FROM  " . DB_PREFIX . "filter WHERE filter_id IN (" . implode(',', $implode) . ") ";
				$query_check = $this->db->query($sql_check);
				$filter_groups = array();
				foreach ($query_check->rows as $result) {
				   $filter_groups[$result['filter_group_id']] = array();
				}
				if(count($filter_groups) > 1){
				   $validimi = true;
				}else{
				   $validimi = false;
				}
				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
			if(!empty($data['price_filter'])){
				$price_min = '';
				$price_max = '';

				if(isset($data['price_filter']) && ($data['price_filter'] !='')){
					$prices = explode('-', $data['price_filter']);
					$price_min = $prices[0];
					$price_max = $prices[1];
				}

				/****--- start updated on (05-01-2016) by vikas ----****/
				if($price_max == 0){
					$sql .= " AND p.selling_price >= ".($price_min/$price_values);
				}else{
					$sql .= " AND p.selling_price >= ".($price_min/$price_values)."  AND p.selling_price<= ".($price_max/$price_values);
				}
				/*****--- end ----*******/
			}
		}
		if (isset($data['custom_store']) && !empty($data['custom_store']) && $data['custom_store'] == 'single') {

				$sql .= " AND (p.is_single = 1 OR p.piece_in_set = 1)";

		}else{
				$sql .= " AND p.is_single = 0";

		}
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

                /* Enabling default search into description */
				$sql .= " OR ";
				foreach ($words as $word) {
					$description[] = "pd.description LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($description) {
					$sql .= " " . implode(" AND ", $description) . "";
				}

			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		/***
		 * Get Products According to seller (Quality Expectations)
		 ***/
		if (isset($data['seller']) && !empty($data['seller'])) {
			$sql .= " AND mp.seller_id IN(".$data['seller'].") AND mss.vacation_mode = 0";
		}
		If(isset($validimi)) {
		   $sql .= " GROUP BY p.product_id HAVING COUNT( DISTINCT pf.filter_id)=".count($filter_groups);
		}else{
		   $sql .= " GROUP BY p.product_id";
		}
		if(!empty($data['rating_filter']) && $data['rating_filter'] != 0){
		   if(isset($validimi)){
			$sql .= " AND rating >=". $data['rating_filter'];
		   }else{
			$sql .= " HAVING rating >=". $data['rating_filter'];
		   }

	   }
		$sort_data = array(
			'p.product_id',
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'p.selling_price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort'])) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY " . $data['sort'] .' '. $data['order'];
			} elseif ($data['sort'] == 'p.selling_price') {
				$sql .= " ORDER BY " . $data['sort'] .' '. $data['order'];
			} else {
				$sql .= " ORDER BY " . $data['sort'] .' '. $data['order'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 200000;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();
		$query = $this->db->query($sql);
		if($data['user_id']){
			$user_id = $data['user_id'];
		}else{
			$user_id = '';
		}

        $product_data['total'] = $query->num_rows;

		foreach ($query->rows as $result) {
		    $product = $this->getProduct($result['product_id'],$user_id);
		    if(!empty($product) ){
                // 04-07-2017: Check product's stock status, if product is out of stock, then we will not send it
                // and also decrement the product count.
                // stock_status is coming from model_catalog_product->getProduct method
                if( isset($product['stock_status']) && $product['stock_status'] != 'out of stock' ){
                    $product_data['data'][] = $product;
                }
                else{
                    $product_data['total'] = $product_data['total'] - 1;
                }
            }
		}

		return $product_data;
	}


	public function getTotalProducts( array $data ){
		if($this->currency->getCode() == 'INR'){
			$price_values = $this->currency->currencies['INR']['value'];
		}else{
			$price_values = $this->currency->currencies['USD']['value'];
		}

		$sql = "SELECT p.product_id,
                       (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating,
					   (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
			/***
			 * Get Products According to seller (Quality Expectations)
			 ***/
			if (isset($data['seller']) && !empty($data['seller'])) {
				$sql .= "INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id) INNER JOIN ".DB_PREFIX."ms_seller mss ON (mp.seller_id = mss.seller_id)";
			}

			if(!empty($data['price_filter'])){
				$price_min = '';
				$price_max = '';

				if(isset($data['price_filter']) && ($data['price_filter'] !='')){
					$prices = explode('-', $data['price_filter']);
					$price_min = $prices[0];
					$price_max = $prices[1];
				}

				/****--- start updated on (05-01-2016) by vikas ----****/
				if($price_max == 0){
					$sql .= " AND p.selling_price >= ".($price_min/$price_values);
				}else{
					$sql .= " AND p.selling_price >= ".($price_min/$price_values)."  AND p.selling_price<= ".($price_max/$price_values);
				}
				/*****--- end ----*******/
			}
		} else {

			/***
			 * Get Products According to seller (Quality Expectations)
			 ***/
			$sql .= " FROM " . DB_PREFIX . "product p";
			if (isset($data['seller']) && !empty($data['seller'])) {
				$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id) INNER JOIN ".DB_PREFIX."ms_seller mss ON (mp.seller_id = mss.seller_id)";
			}
		}
        //echo $sql; exit;
        $out_of_stock_id = 0;
        $out_of_stock_query = $this->db->query("SELECT ss.stock_status_id FROM " . DB_PREFIX . "stock_status ss WHERE UPPER(ss.name) = 'OUT OF STOCK' AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "'");
        if ($out_of_stock_query->num_rows)
            $out_of_stock_id = (int)($out_of_stock_query->row['stock_status_id']);

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.quantity > 0 AND p.stock_status_id != '" . $out_of_stock_id . "' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}
		if (isset($data['custom_store']) && !empty($data['custom_store']) && $data['custom_store'] == 'single') {

				$sql .= " AND (p.is_single = 1 OR p.piece_in_set = 1)";

		}else{
				$sql .= " AND p.is_single = 0";

		}
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

                /* Enabling default search into description */
				$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";

			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		/***
		 * Get Products According to seller (Quality Expectations)
		 ***/
		if (isset($data['seller']) && !empty($data['seller'])) {
			$sql .= " AND mp.seller_id IN(".$data['seller'].") AND mss.vacation_mode = 0";
		}
		$sql .= " GROUP BY p.product_id";

		if(!empty($data['rating_filter']) && $data['rating_filter'] != 0){
		   if(isset($validimi)){
			$sql .= " AND rating >=". $data['rating_filter'];
		   }else{
			$sql .= " HAVING rating >=". $data['rating_filter'];
		   }

		}
		$sort_data = array(
			'p.product_id',
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'p.selling_price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY " . $data['sort'];
			} elseif ($data['sort'] == 'p.selling_price') {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 200000;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		return $query->num_rows;
	}

	public function getProduct( int $product_id, int $user_id = 0){

        $this->load->model('catalog/product');

        $request_data = $this->restapi->getRequestData();

        $product_info = $this->model_catalog_product->getProduct($product_id);
        $store_id = $this->config->get('config_store_id');
        //Get Images

        $this->load->model('tool/image');
        $imgDir = DIR_IMAGE;
        $imgPath = HTTP_SERVER; // Need To replace with static variable
        if(NGINX_ENABLED == 1){
            $original_image = $this->model_tool_image->getOriginalImage($product_info['image']);
            $img = $this->model_tool_image->resizeBasedOnLargeDimension($product_info['image'], '400');
            $thumb_image = $this->model_tool_image->resizeBasedOnLargeDimension($product_info['image'], $this->config->get('config_image_thumb_width'));
            $small_thumb = $this->model_tool_image->resizeBasedOnLargeDimension($product_info['image'], '36');
        }
        else {
            if (S3_ENABLED == 0) {

                if (!empty($product_info['image'])) {
                    $original_image = $this->model_tool_image->getOriginalImage($product_info['image']);
                    $img = $this->model_tool_image->resizeBasedOnLargeDimension($product_info['image'], '400');
                    $thumb_image = $this->model_tool_image->resizeBasedOnLargeDimension($product_info['image'], $this->config->get('config_image_thumb_width'));
                    $small_thumb = $this->model_tool_image->resizeBasedOnLargeDimension($product_info['image'], '36');
                } else {
                    $no_image = $this->config->get('config_url') . 'image/no_image.png';
                    $original_image = $no_image;
                    $img = $no_image;
                    $thumb_image = $this->model_tool_image->resize($no_image, $this->config->get('config_image_thumb_width'));
                    $small_thumb = $thumb_image;
                }
            } else {

                if (isset($_SERVER["HTTPS"]) && ((strtolower($_SERVER["HTTPS"]) == "on") || ($_SERVER['HTTPS'] == '1'))) {
                    $url = HTTPS_SERVER;
                } else {
                    $url = HTTP_SERVER;
                }

                $original_image = $product_info['image'];

                $img = $url . 'index.php?route=restapi/image/load&original_image=' . $original_image . '&display_image=' . $this->getDisplayImage($original_image, '400') . '&width=400&height=';
                $thumb_image = $url . 'index.php?route=restapi/image/load&original_image=' . $original_image . '&display_image=' . $this->getDisplayImage($original_image, $this->config->get('config_image_thumb_width')) . '&width=' . $this->config->get('config_image_thumb_width') . '&height=';
                $small_thumb = $url . 'index.php?route=restapi/image/load&original_image=' . $original_image . '&display_image=' . $this->getDisplayImage($original_image, '36') . '&width=36&height=';
            }
        }

        $fimg = $img;

        $other_img = $this->getOtherImages($product_id, $fimg);
        $price_starting_from_arr = array();
        $options = $this->model_catalog_product->getProductOptions($product_id);
        $update_price_by = $this->getCustomerSettingByKey('update_price',$user_id);
        if (empty($update_price_by)) {
            $update_price_by = DEFAULT_SHARE_MARGIN;
        } else {
            $update_price_by = $update_price_by['value'];
        }
        if(!empty($options)) {
          $to_unset_option_keys = array();
          foreach ($options as $key => $value) {
            $to_unset_option_value_keys = array();
            if(!empty($value['product_option_value'])) {
              foreach ($value['product_option_value'] as $_key => $option_value) {
                $option_price = 0;
                if ($option_value['price_prefix'] == "+") {
                    $option_price += $option_value['price'];
                } else {
                    $option_price -= $option_value['price'];
                }
                $product_info_temp = $product_info;
                $product_info_temp['option_price'] = $option_price;
                $price_details = $this->cart->getPrice($product_info_temp, $this->registry);
                $unfor_option_price = $price_details['selling_price'];
                $price_starting_from_arr[] = $unfor_option_price;

                $testString = $unfor_option_price;
                $pr = ltrim(preg_replace("/[^0-9.]/", "", $testString),'.');
                $updated_price = ($update_price_by * $pr)/100;
                $updated_price = ceil($updated_price + $pr);
                $updated_price = $this->roundUpToAny($updated_price,5);

                if($store_id == INTERNATIONAL_STORE_ID){
                  $options[$key]['product_option_value'][$_key]['option_price'] = $this->currency->format($unfor_option_price,'','',true,0,'');
                  $options[$key]['product_option_value'][$_key]['option_updated_price'] = $this->currency->format($updated_price, $product_info['tax_class_id'], $this->config->get('config_tax'),true,0,'');
                } else {
                  $options[$key]['product_option_value'][$_key]['option_price'] = $this->currency->format($unfor_option_price,'','',true,0,'frontend');
                  $options[$key]['product_option_value'][$_key]['option_updated_price'] = $this->currency->format($updated_price, $product_info['tax_class_id'], $this->config->get('config_tax'),true,0,'frontend');
                }
                if((int)$value['option_id'] == 5) {
                  if(!empty($option_value["name"])) {
                    $hex_code = $this->getColorHexCode($option_value["name"]);
                    $options[$key]['product_option_value'][$_key]['hex_code'] = !empty($hex_code)?$hex_code:"";
                  }
                }
                if(empty($option_value['option_code'])) {
                  $option_value['option_code'] = "";
                }
                $options[$key]['product_option_value'][$_key]['option_code'] = $option_value['option_code'];
                if(isset($option_value['quantity']) && empty($option_value['quantity'])) {
                  $to_unset_option_value_keys[] = $_key;
                }
              }
            }
            if(!empty($to_unset_option_value_keys)){
              foreach ($to_unset_option_value_keys as $unset_key => $unset_value) {
                unset($options[$key]['product_option_value'][$unset_value]);
              }
              $options[$key]['product_option_value'] = array_values($options[$key]['product_option_value']);
              if(empty($options[$key]['product_option_value'])) {
                $to_unset_option_keys[] = $key;
              }
            }
          }
          if(!empty($to_unset_option_keys)) {
            foreach ($to_unset_option_keys as $unset_option_key => $unset_option) {
              unset($options[$unset_option]);
            }
            $options = array_values($options);
          }
        }

        $filters = $this->getProductFiltersData($product_id);
        $customer_info = $this->checkCustomerByIdwithAdd($user_id);

        $data_to_track = array('Brand Name' => '');
        $spec = array();
        $i = 1;
        $spec[0]['key'] = 'Product Code';
        if(isset($product_info['model'])){
            $spec[0]['value'] = $product_info['model'];
        }else{
            $spec[0]['value'] = '';
        }

        foreach ($filters as $filter) {
            $spec[$i]['key'] = $filter['group_name'];
            $spec[$i]['value'] = html_entity_decode($filter['filter_name']);

            if($filter['group_name'] == 'Brand Name')
             {
               $data_to_track['Brand Name'] = html_entity_decode($filter['filter_name']);
             }

            $i++;
        }

        if (is_array($product_info) && count($product_info) > 0) {
            $arr_price = $this->model_catalog_product->getPrice($product_info);

            $raw_price = $this->tax->calculate(ceil($arr_price['unformatted_price']), $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']);

            $price_starting_from_arr[] = $raw_price;

      			if($store_id == INTERNATIONAL_STORE_ID){
      				$price = $this->currency->formatWebservices($raw_price,'','',true,'');
              $product_info['price_starting_from'] = $this->currency->formatWebservices(min($price_starting_from_arr),'','',true,'');
      			} else {
      				$price = $this->currency->formatWebservices($raw_price,'','',true,'frontend');
              $product_info['price_starting_from'] = $this->currency->formatWebservices(min($price_starting_from_arr),'','',true,'frontend');
      			}

            // 31st October 2018: special price is already calculated in product info
            // so we use directly it here
            $special_price = $product_info['special'];

            $update_price_by = '0';
			if (!empty($request_data['sharing_margin'])) {
                $update_price_by = $request_data['sharing_margin'];
            } else {
                $update_price_by = $this->getCustomerSettingByKey('update_price',$user_id);

                if (empty($update_price_by)) {
                    $update_price_by = DEFAULT_SHARE_MARGIN;
                } else {
                    $update_price_by = $update_price_by['value'];
                }
            }

            if ($arr_price['special'] !== false) {
                // Need to be send in different field named as special price(Modified On 17-12-2015 Ravindra Singh)
                $testString = $special_price;
                $pr = ltrim(preg_replace("/[^0-9,.]/", "", $testString),'.');
                $updated_price = ($update_price_by * $pr)/100;
                $updated_price = ceil($updated_price + $pr);
                // will round to 5 only if currency is inr
                if ($this->currency->getCode() == 'INR') {
                    $updated_price = $this->roundUpToAny($updated_price,5);
                }
                $uprice = $this->currency->formatWebservices($updated_price, $product_info['tax_class_id'], $this->config->get('config_tax'));
            } else {
                $testString = $product_info['selling_price'];
                $pr = ltrim(preg_replace("/[^0-9.]/", "", $testString),'.');
                $updated_price = ($update_price_by * $pr)/100;
                $updated_price = ceil($updated_price + $pr);
                // will round to 5 only if currency is inr
                if ($this->currency->getCode() == 'INR') {
                    $updated_price = $this->roundUpToAny($updated_price,5);
                }
                $uprice = $this->currency->formatWebservices($updated_price, $product_info['tax_class_id'], $this->config->get('config_tax'));
            }


            // If customer is logged in via franchise,
            // then send the credit price for franchise products, otherwise will send empty credit price.
            $product_info['credit_price'] = '';
            $headers = getallheaders();
            if(isset($headers['crm_user_id']) && isset($headers['crm_role_id']) && (int)$headers['crm_role_id'] == (int)CRM_FRANCHISE_ROLE_ID) {
                if(isset($headers['franchise_margin']) && !empty($product_info['franchise_id']) ){
                    $update_price_by = 1.0 + (float)$headers['franchise_margin']/100.00;
                    $testString = $product_info['selling_price'];
                    $pr = ltrim(preg_replace("/[^0-9,.]/", "", $testString),'.');
                    $credit_price = round($pr*$update_price_by);
                    $product_info['credit_price'] = $this->currency->formatWebservices($credit_price, $product_info['tax_class_id'], $this->config->get('config_tax'));
                }
            }

            $is_wishlist = 0;
            // for versions greater than 74, will not send wishlist data.
            if ($request_data['app_version_code'] < 75) {
                $is_wishlist = $this->checkWishlist($user_id, $product_info['product_id']);
            }

            if ($is_wishlist > 0) {
                $wishlist = true;
            } else {
                $wishlist = false;
            }

            $default_template_text = '';
            if (!empty($request_data['default_sharing_template'])) {
                $default_template_text = $request_data['default_sharing_template'];
            } else {
                $template = $this->getDefaultTemplate($user_id);
                $template_sql = "SELECT st.template FROM ".DB_PREFIX."share_templates st WHERE st.template_id=".$template;
                $default_template_text_query = $this->db->query($template_sql);
                if(!empty($default_template_text_query->row['template'])) {
                    $default_template_text = $default_template_text_query->row['template'];
                }
            }
            if (stripos($product_info['set_description'], 'Size')) {
                $product_info['set_description_onlysize'] = substr($product_info['set_description'], stripos($product_info['set_description'], 'Size'));
            } else {
                $product_info['set_description_onlysize'] = $product_info['set_description'];
            }
            $find = array('[product_name]','[set]',
                '[model]', '[markup_price]',
                '[shop_name]','[my_name]','[mobile]','[set_showonlysize]');
            $replace = array(trim(html_entity_decode($product_info['name'])),
                html_entity_decode($product_info['set_description']),
                $product_info['model'],$uprice,$customer_info['company'],
                $customer_info['firstname'].' '.$customer_info['lastname'],
                $customer_info['telephone'],$product_info['set_description_onlysize']);
            if (!empty($default_template_text)) {
                $default_template_text = str_replace($find, $replace, $default_template_text);
            } else {
                $default_global_text = $this->getGlobalDefaultTemplate();
                $default_template_text = str_replace($find, $replace, isset($default_global_text['template'])?$default_global_text['template']:'');
            }

            $sku = $product_info['model'];

            $product_info['sku'] = $sku;
            $product_info['original_image'] = $original_image;
            $product_info['image'] = (isset($product_info['show_large_image_in_zoom'])
                                            && $product_info['show_large_image_in_zoom'] == 1
                                     ) ? $original_image :  $fimg;

            $product_info['thumb_image'] = $thumb_image;
            $product_info['small_image'] = $small_thumb;
            $product_info['sub_images'] = $other_img;

            $product_info['sku'] = $sku;
            $product_info['price'] = $price;
            $product_info['stock'] = $product_info['quantity'];
            if($product_info['rating'] > 0) {
                $product_info['product_rating'] = (float)$product_info['rating'];
				if($product_info['rating'] > 4){
					$product_info['quality_tag'] = 'Excellent Quality';
				}elseif($product_info['rating'] > 3){
					$product_info['quality_tag'] = 'Good Quality';
				}elseif($product_info['rating'] > 2){
					$product_info['quality_tag'] = 'Average Quality';
				}else{
					$product_info['quality_tag'] = "";
				}
            }else{
                $product_info['product_rating'] = null;
				$product_info['quality_tag'] = "";
            }
            $product_info['updated_price'] = $uprice;
            $product_info['price_unit'] = "/piece + GST (" . $product_info['tax_rate'] . "%)";
            $product_info['specifications'] = $spec;
            $product_info['share_message'] = !empty($default_template_text)?$default_template_text :'';
            $product_info['is_wishlist'] = $wishlist;
            $product_info['template_text'] = !empty($default_template_text)?$default_template_text :'';
            $product_info['options']       = $options;

            $product_info['data_to_track'] = $data_to_track;

            $product_info['product_previously_order'] = $this->model_catalog_product->getProductPreviouslyOrder((int)$product_id,(int)$user_id);

            $product_info['non_returnable'] = (int)( $product_info['non_returnable'] ?? 0 );

            if($product_info['non_returnable'])
            {
              $product_info['non_returnable_info'] = "This item is non-returnable";
            } else {
              $product_info['non_returnable_info'] = "";
            }

			if(!empty($product_info['mrp']) && !empty($product_info['discount_percentage']) && !empty($product_info['special_text'])) {
				$product_info['price'] = $product_info['special_text'];
			}
            return $product_info;
        }

       return false;

    }

	public function getProduct_bkp( int $product_id, int $user_id = 0) {

		$sql = "SELECT DISTINCT *, pd.description as product_description, pd.name AS name, p.sold_out, p.image, m.name AS manufacturer,
			(SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id
				AND pd2.quantity = '1' AND pd2.store_id ='0' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW())
				AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW()))
				ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount,
			(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id
				AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW())
				AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
				ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special,
			(SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id) AS reward,
			(SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id
				AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status,
			(SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id
				AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class,
			(SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id
				AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class,
			(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id
				AND r1.status = '1' GROUP BY r1.product_id) AS rating,
			(SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id
				AND r2.status = '1' GROUP BY r2.product_id) AS reviews,
				ms.vacation_mode, p.sort_order FROM " . DB_PREFIX . "product p
			INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id)
			INNER JOIN " . DB_PREFIX . "ms_seller ms ON (ms.seller_id = mp.seller_id)
			WHERE p.product_id = '" . (int)$product_id . "'
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND p.date_available <= NOW()
			AND p2s.store_id = ".(int)$this->config->getStoreIdForSql();
		$query = $this->db->query($sql);
		$this->load->model('tool/image');
        $this->load->model('catalog/product');
		$imgDir = "/var/www/html/image/"; // Need To replace with static variable
		$imgPath = "http://wholesalebox.in/"; // Need To replace with static variable
		$options = $this->model_catalog_product->getProductOptions($product_id);
		$filters = $this->getProductFiltersData($product_id);
		$customer_info = $this->checkCustomerByIdwithAdd($user_id);

        $spec = array();
		$i = 1;
		$spec[0]['key'] = 'Product Code';
		if(isset($query->row['model'])){
			$spec[0]['value'] = $query->row['model'];
		}else{
			$spec[0]['value'] = '';
		}

		foreach($filters as $filter){
			$spec[$i]['key'] = $filter['group_name'];
			$spec[$i]['value'] = html_entity_decode($filter['filter_name']);
			$i++;
			//$spec[$filter['group_name']] = $filter['filter_name'];
		}
		if(!empty($query->row['image'])){
			$img = $this->model_tool_image->resizeBasedOnLargeDimension($query->row['image'], '400');

			$thumb_image = $this->model_tool_image->resize($query->row['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
		}else{
			$img = $this->config->get('config_url').'image/no_image.png';
			$thumb_image = $this->model_tool_image->resize($this->config->get('config_url').'image/no_image.png',  $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
		}
		$fimg = $img;
		$other_img = $this->getOtherImages($product_id, $fimg);

		if ($query->num_rows) {
			$price = $this->getCalculatedProductPrice($query->row['product_id']);

			$special_price = $this->getCalculatedProductSpecialPrice($query->row['product_id']);
			$update_price_by = $this->getCustomerSettingByKey('update_price',$user_id);
			if(empty($update_price_by)){
				$update_price_by = DEFAULT_SHARE_MARGIN;
			}else{
				$update_price_by = $update_price_by['value'];
			}

			if($special_price != '0'){
				$price = $special_price;
				// Need to be send in different field named as special price(Modified On 17-12-2015 Ravindra Singh)
				$testString = $special_price;
				$testString = $query->row['selling_price'];
				$pr = ltrim(preg_replace("/[^0-9,.]/", "", $testString),'.');
				$updated_price = ($update_price_by * $pr)/100;
				$updated_price = ceil($updated_price + $pr);
				$uprice = $this->getCalculatedProductPriceCurrencyFormat($query->row['product_id'],$this->roundUpToAny($updated_price,5));
			}else{
				$testString = $query->row['selling_price'];
				$pr = ltrim(preg_replace("/[^0-9.]/", "", $testString),'.');
				$updated_price = ($update_price_by * $pr)/100;
				$updated_price = ceil($updated_price + $pr);
				$uprice = $this->getCalculatedProductPriceCurrencyFormat($query->row['product_id'],$this->roundUpToAny($updated_price,5));
			}

			$share_message = $this->getShareMessage(html_entity_decode(trim($query->row['name'])),$uprice,$user_id,$query->row['model'],html_entity_decode($query->row['set_description']));
			$is_wishlist = $this->checkWishlist($user_id,$query->row['product_id']);
			if($is_wishlist > 0){
				$wishlist = true;
			}else{
				$wishlist = false;
			}
			$template = $this->getDefaultTemplate($user_id);
			$default_template_text = $this->db->query("SELECT st.template FROM ".DB_PREFIX."share_templates st WHERE st.template_id=".$template)->row['template'];
			$default_global_text = $this->getGlobalDefaultTemplate();
                        if (stripos($query->row['set_description'], 'Size')) {
                           $set_description_onlysize = substr($query->row['set_description'], stripos($query->row['set_description'], 'Size'));
                        } else {
                            $set_description_onlysize = $query->row['set_description'];
                        }
			$find = array('[product_name]','[set]',
				'[model]', '[markup_price]',
				'[shop_name]','[my_name]','[mobile]','[set_showonlysize]');
			$replace = array(trim(html_entity_decode($query->row['name'])),
				html_entity_decode($query->row['set_description']),
				$query->row['model'],$uprice,$customer_info['company'],
				$customer_info['firstname'].' '.$customer_info['lastname'],
				$customer_info['telephone'],$set_description_onlysize);
			if (!empty($default_template_text)) {
				$default_template_text = str_replace($find, $replace, $default_template_text);
			} else {
				$default_global_text_up = str_replace($find, $replace, $default_global_text['template']);
			}
			
            $sku = $query->row['model'];

			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => trim(html_entity_decode($query->row['name'])),
                'set_description'  => html_entity_decode($query->row['set_description']),
				'description'      => html_entity_decode($query->row['description']),
				'model'            => $query->row['model'],
				'sku'              => $sku,
				'image'            => $fimg,
				'thumb_image'      => $thumb_image,
               	'sub_images'       => $other_img,
				'price'            => $price,
				'updated_price'    => $uprice,
				'selling_price'    => $query->row['selling_price'],
                'piece_in_set'     => $query->row['piece_in_set'],
                'price_unit' 		=> "/piece",
                'minimum'          => $query->row['minimum'],
                'tax_class_id'     => $query->row['tax_class_id'],
                'stock'     => $query->row['quantity'],
				'specifications'   => $spec,
				'options'          => $options,
				'share_message' => !empty($default_template_text)?$default_template_text : $default_global_text_up,
				'is_wishlist'   => $wishlist,
				'is_single'   => $query->row['is_single'],
				'template_text' => !empty($default_template_text)?$default_template_text : $default_global_text_up,
				'product_rating' => $query->row['rating'],
				'product_reviews' => $query->row['reviews'],
				'cod_available' => $query->row['cod_available'],
				'mrp'			   => $query->row['mrp'],
				'format_mrp'	   => $this->currency->format($query->row['mrp'])

			);
		} else {
			return false;
		}
	}


    public function getProductOptionsUsingSubOrderId( int $order_id, string $suborder_id, int $order_product_id ) {
        $product_option_data = array();
        $sql = "SELECT * FROM " . DB_PREFIX . "product_option_value pov
                INNER JOIN " . DB_PREFIX . "order_option op ON (pov.product_option_value_id = op.product_option_value_id)
                WHERE op.order_product_id = '" . (int)$order_product_id . "'
                  AND op.order_id = '".(int)$order_id."'
                  AND op.suborder_id = '".$this->db->escape($suborder_id)."'";
        $product_option_value_query = $this->db->query($sql);

        foreach ($product_option_value_query->rows as $product_option_value) {

            $product_option_data[] = array(
                'product_option_id'    => $product_option_value['product_option_id'],
                'product_option_value_id' => $product_option_value['product_option_value_id'],
                'name'                 => $product_option_value['name'],
                'type'                 => $product_option_value['type'],
                'value'                => $product_option_value['value'],
                'image'                   => $product_option_value['image'],
                'quantity'                => $product_option_value['quantity'],
                'subtract'                => $product_option_value['subtract'],
                'price'                   => $product_option_value['price'],
                'price_prefix'            => $product_option_value['price_prefix'],
                'points'                  => $product_option_value['points'],
                'points_prefix'           => $product_option_value['points_prefix'],
                'weight'                  => $product_option_value['weight'],
                'weight_prefix'           => $product_option_value['weight_prefix']
            );
        }

        return $product_option_data;
    }

	public  function getProductFiltersData( int $product_id ){

		$q = "SELECT
				GROUP_CONCAT( f.filter_id ORDER BY f.filter_id ASC ) as filter_id,
				GROUP_CONCAT( fd.name ORDER BY f.filter_id ASC ) as filter_name,
				fgd.name as group_name
			  FROM ".DB_PREFIX."product_filter f
			  INNER JOIN ".DB_PREFIX."filter_description fd
			  	ON f.filter_id = fd.filter_id AND fd.language_id = 1
			  INNER JOIN ".DB_PREFIX."filter fl ON ( f.filter_id = fl.filter_id )
			  INNER JOIN ".DB_PREFIX."filter_group_description fgd
			  	ON fl.filter_group_id = fgd.filter_group_id AND fgd.language_id = 1
			  WHERE
			  	f.product_id = '".(int)$product_id."'
			  GROUP BY
			  	group_name
			  ORDER BY
			  	group_name ASC";
		$query = $this->db->query($q);
		return $query->rows;

	}
    public function getProductImages( int $product_id ) {

        $sql = "SELECT * FROM " . DB_PREFIX . "product_image
                WHERE product_id = " . (int)$product_id;
        $query = $this->db->query($sql);
        $data = $query->rows;
        array_multisort(array_column($data, 'sort_order'), SORT_ASC, SORT_NUMERIC, $data);

        return $data;
    }
	public function checkWishlist( int $customer_id, int $product_id ){
		$query = $this->db->query("SELECT id FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$customer_id . "' AND product_id = '" . (int)$product_id . "'");
		return $query->num_rows;
	}

	public function addProductToWishlist( int $customer_id, int $product_id, string $message ){
		$dateadded = date("Y-m-d H:i:s");
		$store_id = (int)$this->config->getStoreIdForSql();
		if($this->db->query("INSERT INTO " . DB_PREFIX . "customer_wishlist SET customer_id = '" . (int)$customer_id . "',product_id = '" . (int)$product_id . "', share_message = '" . $this->db->escape($message) . "', date_added = '" . $this->db->escape($dateadded) . "', store_id = '" . $this->db->escape($store_id) . "'")){
			return 1;
		}else{
			return 0;
		}
	}
	public function deleteProductToWishlist( int $customer_id, string $product_id ){
	    $product_id = array_unique(array_filter(array_map('intval', explode(',', $product_id))));
	    if(empty($product_id)) return 1;

		$q = "DELETE FROM " . DB_PREFIX . "customer_wishlist 
		      WHERE customer_id = '" . (int)$customer_id . "' 
		        AND product_id IN(" . $this->db->escape(implode(',',$product_id)) . ")";
		$this->db->query($q);
		return 1;
	}
	public function clearProductToWishlist( int $customer_id ){
		$q = "DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$customer_id . "'";
		$query = $this->db->query($q);
		if($query){
			return 1;
		}else{
			return 0;
		}
	}
	public function deleteUserTemplate( string $template_id ){
		$sql = "DELETE FROM ".DB_PREFIX."share_templates WHERE template_id IN (" . $this->db->escape($template_id) . ")";
		$query = $this->db->query($sql);
		if($query){
			return 1;
		}else{
			return 0;
		}
	}
	public function getWishlist( array $data ){
		$customer_id = $data['customer_id'];
		$sql = "SELECT cw.*,pd.name FROM " . DB_PREFIX . "customer_wishlist cw LEFT JOIN  ".DB_PREFIX."product_description pd ON cw.product_id = pd.product_id LEFT JOIN " . DB_PREFIX . "product p ON p.product_id = cw.product_id  WHERE customer_id = '" . $this->db->escape($customer_id) . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cw.id DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 200000;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql, true);
		$product_data = array();
		$ar = array();
		$pdata = array();
		$rt = array();
		foreach ($query->rows as $result) {
			$pdata = $this->getProduct($result['product_id'],$customer_id);
			$ar['share_message'] = $this->db->escape($result['share_message']);

			if($pdata == 'null' || $pdata == ""){
				$rt[] = $result['product_id'];

			}else{
				$prd = array_merge($pdata,$ar);
				$product_data['products'][] = $prd;
			}
		}

		$product_data['total'] = $query->num_rows;
		$product_data['nulled'] = $rt;
        return $product_data;
	}
	public function getTotalWishlist( array $data ){
		$customer_id = $data['customer_id'];
		$sql = "SELECT cw.*,pd.name,p.image FROM " . DB_PREFIX . "customer_wishlist cw LEFT JOIN  ".DB_PREFIX."product_description pd ON cw.product_id = pd.product_id LEFT JOIN " . DB_PREFIX . "product p ON p.product_id = cw.product_id WHERE customer_id = '" . $this->db->escape($customer_id) . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 200000;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql, true);
        return $query->num_rows;
	}

	public function getCustomerSettingByKey( string $key, int $customer_id ){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_settings WHERE key_field = '" . $this->db->escape($key) . "' AND customer_id = '" . (int)$customer_id . "'");
		return $query->row;
	}

	public function checkCustomerSettingByKey( string $key, int $customer_id ){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_settings WHERE key_field = '" . $this->db->escape($key) . "' AND customer_id = '" . (int)$customer_id . "'");
		return $query->num_rows;
	}
	public function UpdateCustomerSetting( int $customer_id, string $key, string $value ){
		$date = Date("Y-m-d H:i:s");
		if(!empty($this->checkCustomerSettingByKey($key,$customer_id))){
			if($this->db->query("UPDATE " . DB_PREFIX . "customer_settings SET value = '" . $this->db->escape($value) . "' WHERE customer_id = '" . (int)$customer_id . "' AND key_field = '" . $this->db->escape($key)."'")){
				return 1;
			}else{
				return 0;
			}
		}else{
			if($this->db->query("INSERT INTO " . DB_PREFIX . "customer_settings SET customer_id = '" . (int)$customer_id . "',key_field = '" . $this->db->escape($key) . "', value = '" . $this->db->escape($value) . "' , date_added = '". $this->db->escape($date) ."'")){
				return 1;
			}else{
				return 0;
			}
		}
	}

	public function getCategoryFilters( int $category_id ) {
		$implode = array();
		//Original - get filter of a category, commented by Rakesh
		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();
		$implode = array_unique(array_filter($implode));
		if (!empty($implode)) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fgd.description, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f INNER JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						 'description'    => $filter_group['description'],
						'filter'          => $filter_data
					);
				}
			}
		}

		return $filter_group_data;
	}
	/**
     * get calculated price of a product
     * @param  product_id
     * @return calculated price
     * @author Ravindra Singh
     */
    public function getCalculatedProductPrice( int $product_id ){
    	$sql = "SELECT p.* FROM ".DB_PREFIX."product p WHERE p.product_id='".(int)$product_id."' ";
    	$query= $this->db->query($sql);
    	if(isset($query->rows)){

    		$seller_tax_factor = 1.0 + ( (float)$query->row['seller_tax'] / 100.0 );
            $commission_factor = 1.0 + ( (float)$query->row['commission'] / 100.0 );
            $unit_price =  $commission_factor * (float)($query->row['price']) / $seller_tax_factor;
			$price = $this->currency->formatWebservices($this->tax->calculate(ceil($unit_price), $query->row['tax_class_id'], $this->config->get('config_tax'), $query->row['mrp']));
			return $price;
    	}else{
			return 0;
		}
    }
    /**
     * get calculated special price of a product
     * @param  product_id
     * @return calculated price
     * @author Ravindra Singh
     */
    public function getCalculatedProductSpecialPrice( int $product_id ){
    	$sql = "SELECT p.*,ps.price as special_price,ps.date_start,ps.date_end FROM ".DB_PREFIX."product p INNER JOIN ".DB_PREFIX."product_special ps ON ps.product_id = p.product_id WHERE p.product_id='".(int)$product_id."'";
    	$query= $this->db->query($sql);

    	if(isset($query->rows) && $query->num_rows > 0){
    		$seller_tax_factor = 1.0 + ( (float)$query->row['seller_tax'] / 100.0 );
            $commission_factor = 1.0 + ( (float)$query->row['commission'] / 100.0 );
            $unit_price =  $commission_factor * (float)($query->row['special_price']) / $seller_tax_factor;
			$price = $this->currency->formatWebservices($this->tax->calculate($unit_price, $query->row['tax_class_id'], $this->config->get('config_tax'), $query->row['mrp']));

			return $price;
    	}else{
			return 0;
		}
    }
	/**
     * get getCalculatedProductPriceCurrencyFormat
     * @param product_id
     * @return calculated price
     * @author Ravindra Singh
     */
    public function getCalculatedProductPriceCurrencyFormat( int $product_id, string $price ){
    	$sql = "SELECT p.* FROM ".DB_PREFIX."product p WHERE p.product_id='".(int)$product_id."'";
    	$query = $this->db->query($sql);
    	if(isset($query->rows)){
			$price = $this->currency->formatWebservices($price, $query->row['tax_class_id'], $this->config->get('config_tax'));

			return $price;
    	}else{
			return $price;
		}
    }

	/**
     * get share message of a product
     * @param  product_id
     * @return calculated price
     * @author Ravindra Singh
     */
    public function getShareMessage( string $name, string $price, int $user_id, string $model, string $set_description ){
		$this->load->language('account/wishlist');
		$message = $this->language->get('share_mesage');
		$udata = $this->getUserDataById($user_id);

    	if(!empty($udata['company'])){
			$show_name = $udata['company'];
		}else{
			if(isset($udata['firstname']) && isset($udata['lastname'])){
				$show_name = $udata['firstname'].' '.$udata['lastname'];
			}else{
				$show_name = "";
			}
		}
		if(isset($udata['telephone'])){
			$mobile = $udata['telephone'];
		}else{
			$mobile = "";
		}
    	$name = $name;
    	$msg = sprintf($message,$name,$price,$show_name,$mobile);
    	return $msg;
    }

    public function getUserDataById( int $user_id ){
		$query = $this->db->query("SELECT c.*,a.company FROM " . DB_PREFIX . "customer c LEFT JOIN oc_address a ON c.customer_id = a.customer_id WHERE c.customer_id = '" . (int)$user_id . "'");
		return $query->row;
    }

     /**
     * get getFiltersOfProducts
     * @param  category_id
     * @return Filters
     * @author Ravindra Singh
     */
    public function getFiltersOfProducts( int $category_id, int $is_single, $filters = '') {

		//Query to get filters of product of a category

		$cats = $this->getCategories((int)$category_id) ;
		$implodecats = array();
		$implodecats[] = (int)$category_id;
		foreach ($cats as $catt)
		{
			$implodecats[] = (int)$catt['category_id'];
		}

		$q = "SELECT pf.filter_id
			   FROM " . DB_PREFIX . "product p
			   LEFT JOIN " . DB_PREFIX . "product_to_category p2c
			   ON (p.product_id = p2c.product_id)
			   LEFT JOIN " . DB_PREFIX . "product_filter pf
			   ON (p.product_id = pf.product_id)
			   LEFT JOIN " . DB_PREFIX . "product_to_store p2s
			   ON (p.product_id = p2s.product_id)
			   WHERE p2c.category_id IN (" . implode(',', $implodecats) . ")
			   AND p.status = '1'
			   AND p.quantity > 0
			   AND p.stock_status_id != '5'
			   AND p.date_available <= NOW()
			   AND p2s.store_id = ". $this->config->get('config_store_id');


	    if ($is_single==1) {
	    	$q .= " AND (p.is_single=1 OR p.piece_in_set=1) ";
	    } else {
	    	$q .= " AND p.is_single=0 ";
	    }

		$query = $this->db->query($q);
		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();
		$implode = array_unique(array_filter($implode));
		if (!empty($implode)) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fgd.description, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();
				$sql = "SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f INNER JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)";
				$filter_query = $this->db->query($sql);

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
							'filter_id' => $filter['filter_id'],
							'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
							'filter_group_id' => $filter_group['filter_group_id'],
							'name'            => $filter_group['name'],
							'description'    => $filter_group['description'],
							'filter'          => $filter_data
					);
				}
			}
		}

		return $filter_group_data;
	}

	/**
     * get getFiltersOfProducts
     * @param  category_id 	INT
	 * @param  search 		STRING
	 * @param  is_single	TINY INT
     * @return final 		ARRAY
     * @author Garvit
     */
	public function getFiltersOfProductsBySolr( int $category_id, string $search, int $is_single ) {
		$solr_obj = new SolrProduct($this);

		$filter = array();
		$filter['is_single'] 		= $is_single;
		$filter['is_facet'] 		= 1;
		$filter['both_facet'] 		= 1;
		$filter['facet_filters'] 	= 1;
		if(empty($category_id)){
			$filter['facet_field'] 		= 'category_id';
		}else{
			$filter['facet_field'] 		= 'price';
			$filter['filter_category_id'] = $category_id;
		}
		if(!empty($search)) $filter['filter_name'] = $search;
		$result = $solr_obj->getProductFromSolrOnly($filter);
		// SEND category if category_ids not select.
		$final 	= array();
		if(empty($category_id)){
			$category_ids = array();
			foreach( $result['result_facet_field'] as $key => $value ){
				if($value > 0) {
					$category_ids[] = $key;
				}
			}
			$final['filter_category'] = $this->_getParentChildCategories($category_ids);
		}

		if(isset($result['filter_facets'])){
			$final['filter_facets'] = $result['filter_facets'];
		}
		$price = array();
		foreach( $result['result_facet_field'] as $key => $value ){
			if($value > 0) {
				$price[] = $key;
			}
		}
		if(!empty($category_id)){
			$final['min_price'] = !empty($price)?min($price):0;
			$final['max_price'] = !empty($price)?max($price):10;
		}
		return $final;
	}

	/**
     * _getParentChildCategories
     * @param  category_ids 	INT
     * @return return 			ARRAY
     * @author Garvit
     */
	private function _getParentChildCategories( array $category_ids ){
		$final_arr = array();
		if(!empty($category_ids)){
			$sql = "SELECT c.category_id, cd.name FROM ".DB_PREFIX."category c
				INNER JOIN ".DB_PREFIX."category_description cd ON (c.category_id = cd.category_id)
				WHERE cd.category_id IN (".implode(",",$category_ids).") AND c.parent_id = 0 AND c.status = 1 AND cd.language_id = 1";
			$parent_category_id = $this->db->query($sql)->rows;
			if(!empty($parent_category_id)){
				foreach($parent_category_id as $cat_id){
					$sql = "SELECT c.category_id, cd.name FROM ".DB_PREFIX."category c
					INNER JOIN ".DB_PREFIX."category_description cd ON (c.category_id = cd.category_id)
					WHERE c.parent_id = ".(int)$cat_id['category_id']." AND c.status = 1 AND cd.language_id = 1";
					$child_category_id = $this->db->query($sql)->rows;
					$final_arr[$cat_id['category_id']]['parent_cat'] 	= $cat_id;
					$final_arr[$cat_id['category_id']]['child_cat'] 	= $child_category_id;
				}
			}
		}
		return array_values($final_arr);
	}

	/**
	 * Validate the API call
	 * @param $headers
	 * @return boolean
	 */

	public function validateApiCall( array $headers ){

		if(!isset($headers['PUBLIC_KEY']) || $headers['PUBLIC_KEY'] == ""){

			return false;

		}

		if(!isset($headers['REQUEST_DATE']) || $headers['REQUEST_DATE'] == ""){

			return false;

		}

		if(!isset($headers['SIGNATURE']) || $headers['SIGNATURE'] == ""){

			return false;

		}

		$public_key = $headers['PUBLIC_KEY'];
		$request_date = $headers['REQUEST_DATE'];
		$signature = $headers['SIGNATURE'];

		date_default_timezone_set('Asia/Calcutta');
		$curDate =  date("Y-m-d H:i:s");

		if($this->dateTimeDiff($request_date, $curDate) > 300){
			return false;
		}

		//Get private key
		$private_key = $this->getApiKey($public_key);
		if($private_key == false){
			return false;
		}
		$data = "Private Key:".$private_key."\n".
				"Current Time:".$request_date;
		//Create signature
		$hmac = $this->createHMACKey($data, $private_key);

		if($hmac == $signature){
			return true;
		}else{
			return false;
		}

	}
	/**
	 *
	 */
	private function createHMACKey($data, $private_key){

		$secret64 = $private_key;
		$secret = base64_decode($secret64);
		$hmac  = hash_hmac('sha1', $data, $secret, true);
		$hmac64 = base64_encode($hmac);
		return $hmac64;
	}
	/**
	 * Date difference in Seconds
	 */
	public function dateTimeDiff( string $date1, string $date2 )
	{
		$datetime1 = strtotime($date1);
		$datetime2 = strtotime($date2);
		$interval = abs($datetime2 - $datetime1);
		$seconds = round($interval / 60*60);

		return $seconds;
	}
	/**
	 * Get API key
	 * @params String public_key
	 * @return String private_key
	 */
	public function getApiKey( string $public_key ){
		$keys = $this->apiKeys();
		if(isset($keys[$public_key]['private_key'])){
			return $keys[$public_key]['private_key'];
		}else{
			return false;
		}

	}
	/**
	 * Store API keys, later store it in to DB or in a separate file
	 */
	public function apiKeys(){

		return $api_access_keys = array(
				'AKIAI6EUJTSZUG7SXE2A'=>array('private_key'=>'7Rx8R7DtzMBr96rkftCKRyrwVG/YIebq9/O7rYF8', 'assigned_to'=>'Android App')
				);
	}

	public function getUserTemplates( int $customer_id ){
		$sql = "SELECT * FROM ".DB_PREFIX."share_templates st WHERE st.customer_id = '".(int)$customer_id."' ";
		$query = $this->db->query($sql);

		if ($query->num_rows > 0) {
			return $query->rows;
		} else {
			return 0;
		}

	}
	public function setUserTemplates($template_id = '', int $customer_id, $template, $status=0, $title=''){

		$sql = "INSERT INTO ".DB_PREFIX."share_templates
				SET
					customer_id = '".(int)$customer_id."',
					template 	= '".$this->db->escape($template)."',
					status 		= '".$this->db->escape($status)."',
					title 		= '".$this->db->escape($title)."'
			  " ;

		if (isset($template_id) && !empty($template_id)) {
			$sql_update = "UPDATE ".DB_PREFIX."share_templates
							SET
								template= '".$this->db->escape($template)."',
								status 	= '".$this->db->escape($status)."',
								title 	= '".$this->db->escape($title)."'
							WHERE
								template_id= '".(int)$template_id ."'
								AND
								customer_id = '".(int)$customer_id."'
						";

			return $this->db->query($sql_update);
		}

		$this->db->query($sql);

		if ($template_id = $this->db->getLastId()) {
			if ($this->setDefaultTemplate($customer_id, $template_id)) {
				return 1;
			}
		} else {
			return 0;
		}

	}
	public function getDefaultTemplate( int $customer_id ){

		$sql = "SELECT c.default_share_template,c.is_dropshipper FROM ".DB_PREFIX."customer c WHERE c.customer_id= '".(int)$customer_id."' ";

		$query = $this->db->query($sql);

		if (isset($query->num_rows) && $query->num_rows > 0) {
                        if($query->row['default_share_template'] > 0){
                            return $query->row['default_share_template'];
                        }else{
                           if($query->row['is_dropshipper'] == '1'){
                                return RESELLER_TEMPLATE;
                           }else{
                               return 1;
                           }
                        }

		} else {
			return 0;
		}


	}
	public function setDefaultTemplate( int $customer_id, int $template_id ){

		$sql = "UPDATE ".DB_PREFIX."customer SET default_share_template='".$this->db->escape($template_id) ."' WHERE customer_id = '".(int)$customer_id."' ";

		if ($query = $this->db->query($sql)) {
			return 1;
		} else {
			return 0;
		}
	}

        /**
         * updateAppVersionInCrm
         * @author Vishnu Shekhawat
         * @description Update App version if current app version in not equal to previous app version
         * @param string $app_version
         * @param string $type IOS|ANDROID
         * @param integer $customer_id
         * @return TRUE
         */
        public function updateAppVersionInCrm( string $app_version, string $type, int $customer_id ){
               $fields = '';
            if($type == 'IOS'){
              $fields = 'ios_app_version';
                $appVersionSql = "SELECT c.ios_app_version as app_version  FROM ".DB_PREFIX."customer c WHERE c.customer_id=".(int)$customer_id;
            }else{
              $fields = 'app_version';
                $appVersionSql = "SELECT c.app_version FROM ".DB_PREFIX."customer c WHERE c.customer_id=".(int)$customer_id;
            }

             $lead_data = [
				$fields => $app_version,
		];
            $query = $this->db->query($appVersionSql);

		if (isset($query->num_rows) && $query->num_rows > 0) {
			$pre_app_version = $query->row['app_version'];
            if($pre_app_version != $app_version){
                $this->load->model('lead/lead');
                $lead = $this->model_lead_lead->updateLeadFromCustomerId($lead_data, $customer_id);
            }
		} else {
			return TRUE;
		}
            return TRUE;

        }

	public function updateAppVersion( int $customer_id, string $app_version ){

            $type = '';
		if( isset($this->restapi->getRequestHeader()['REQUEST_BY']) && strtoupper($this->restapi->getRequestHeader()['REQUEST_BY']) == 'IOS_APP') {
            $type = 'IOS';
			$version = " ios_app_version = '".$this->db->escape($app_version)."' ";
		} else {
            $type = 'ANDROID';
			$version = " app_version = '".$this->db->escape($app_version)."' ";
		}

		$sql = "UPDATE ".DB_PREFIX."customer SET ".$version." WHERE customer_id=".(int)$customer_id;

		if ($query = $this->db->query($sql)) {

            $this->updateAppVersionInCrm($app_version , $type ,$customer_id);

			return 1;

		} else {

			return 0;
		}
	}

	public function replaceShortTags($text){
		$pattern = '/#{2}[a-zA-Z0-9_]+#{2}/' ;

		preg_match_all($pattern, 'falana ##NAME## of the product is ##set_description##', $matches);

		if (!empty($matches[0])) {
			return $matches;
		} else {
			return 0;
		}

		// echo "<pre>";print_r($matches);
	}

	public function getGlobalDefaultTemplate(){
        return array('1', 'Template1', '[product_name] in just [markup_price] from -[shop_name], [mobile]', '1', '0');
	}
	public function updatePreviousWishlist($value='')
	{

	}
	public function roundUpToAny($n,$x=5) {
	    // If number is already rounded, then will not add +5
	    if(ceil($n)%$x == 0){
	        return $n;
        }
	    return round(($n+$x/2)/$x)*$x;
	}

	public function getTotalCart() {

	}
	/**
	 * [getOptionsOfProducts description]
	 * @author Parth Gupta
	 * @dateTime 2016-03-15T17:13:44+0530
	 * @param    int                   $category_id
	 * @param    string                   $options
	 * @return   array                 array of products
	 */
	public function getOptionsOfProducts( int $category_id, $is_single, $options = ''){
		$cats = $this->getCategories((int)$category_id) ;
		$implodecats = array();
		$implodecats[] = (int)$category_id;
		foreach ($cats as $catt)
		{
			// echo $implodecats[] = (int)$catt['category_id'];
		}

		$q_n = "SELECT pov.option_value_id, p.product_id FROM
				".DB_PREFIX."product p
				LEFT JOIN " . DB_PREFIX . "product_to_category p2c
				ON (p.product_id = p2c.product_id)
				LEFT JOIN ".DB_PREFIX."product_option po
				ON (po.product_id = p.product_id)
				LEFT JOIN ".DB_PREFIX."product_option_value pov
				ON (pov.product_option_id = po.product_option_id)
				WHERE p2c.category_id IN (" . implode(',', $implodecats) . ")
				AND pov.quantity > 0
				AND p.status = '1'
				AND p.quantity > 0
				AND p.stock_status_id != '5'
				AND p.date_available <= NOW()";

				$query = $this->db->query($q_n);

		foreach ($query->rows as $optionvalue) {
			$implode[] = $optionvalue['option_value_id'];
		}

		$option_description_data = array();
		if (!empty($implode)) {

			$option_description_query = "SELECT DISTINCT od.option_id, od.name
										FROM ".DB_PREFIX."option op
										LEFT JOIN ".DB_PREFIX."option_description od
										ON (op.option_id = od.option_id)
										LEFT JOIN ".DB_PREFIX."option_value ov
										ON (od.option_id = ov.option_id)
										LEFT JOIN ".DB_PREFIX."option_value_description ovd
										ON (ov.option_value_id = ovd.option_value_id)
										WHERE ovd.option_value_id IN  (" . implode(',', $implode) . ") AND
										od.language_id = " . (int)$this->config->get('config_language_id')
										;
			$option_description = $this->db->query($option_description_query);

			foreach ($option_description->rows as $option) {


				$option_data = array();

				$option_data_query = $this->db->query("SELECT DISTINCT ovd.option_value_id, ovd.name as option_value
											FROM ".DB_PREFIX."option op
											LEFT JOIN ".DB_PREFIX."option_description od
											ON (op.option_id = od.option_id)
											LEFT JOIN ".DB_PREFIX."option_value ov
											ON (od.option_id = ov.option_id)
											LEFT JOIN ".DB_PREFIX."option_value_description ovd
											ON (ov.option_value_id = ovd.option_value_id)
											WHERE ovd.option_value_id IN  (" . implode(',', $implode) . ") AND
											od.language_id = " . (int)$this->config->get('config_language_id')."
											AND ovd.option_id = '".$this->db->escape($option['option_id'])."' ");

				foreach ($option_data_query->rows as $opt) {
					$option_data[] = array(
						'option_value_id' => $opt['option_value_id'],
						'option_value' => $opt['option_value']
					);
				}
				if ($option_data) {
						$option_array[]= array(
							'option_id' => $option['option_id'],
							'option_name' => $option['name'],
							'option' => $option_data
						);
					}


			}
		}
		return isset($option_array)? $option_array : 0;
	}
	public function getPopularSearch()
	{
		return $this->db->query("SELECT popular_search FROM ".DB_PREFIX."popular_search WHERE link IS NULL OR link = '' LIMIT 0,5")->rows;
	}
	public function getOrders( int $customer_id, int $start, int $limit )
	{
		if ($start < 0) {
			$start = 0;
		}

        $query = $this->db->query(
        	"SELECT o.order_id, o.order_no, subo.invoice_no, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value
        	FROM `" . DB_PREFIX . "order` o
            LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id)
        	LEFT JOIN " . DB_PREFIX . "suborder subo ON (subo.order_id = o.order_id)
        	WHERE o.customer_id = '" . (int)$customer_id . "'
        	AND o.order_status_id > '0' AND o.franchise_id = 0
        	AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	public function getTotalOrders( int $customer_id ) {
        $query = $this->db->query("
        	SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o
        	WHERE customer_id = '" . (int)$customer_id . "'
        	AND o.order_status_id > '0' AND o.franchise_id = 0 ");

		return $query->row['total'];
	}
	public function getFrontImage( string $image )
	{
		$this->load->model('tool/image');
		if(!empty($image)){
	      $img = $this->model_tool_image->resize($image, '400', '600');
	    }else{
	      $img = $this->config->get('config_url').'image/no_image.png';
	    }
	    return $img;
	}
	public function getOtherImages( int $product_id, $fimg)
	{
		$imgs = $this->getProductImages($product_id);
		$this->load->model('tool/image');

		$other_img = array();
		$i = 0;
		foreach($imgs as $img){
			if($i == 0){
			  $other_img[] = $fimg;
			  $i++;
			}

			if(NGINX_ENABLED == 1){
                $other_img[] = $this->model_tool_image->resizeBasedOnLargeDimension($img['image'], '400');
            }
            else {
                if (S3_ENABLED == 0) {
                    if (!empty($img['image'])) {
                        $other_img[] = $this->model_tool_image->resizeBasedOnLargeDimension($img['image'], '400');
                    }
                } else {
                    if (isset($_SERVER["HTTPS"]) && ((strtolower($_SERVER["HTTPS"]) == "on") || ($_SERVER['HTTPS'] == '1'))) {
                        $url = HTTPS_SERVER;
                    } else {
                        $url = HTTP_SERVER;
                    }
                    if (!empty($img['image'])) {

                        $original_image_array = explode("/", $img['image']);
                        $original_image = $original_image_array[count($original_image_array) - 1];
                        $other_img[] = $url . 'index.php?route=restapi/image/load&original_image=' . $original_image . '&display_image=' . $this->getDisplayImage($img['image'], '400') . '&width=400&height=';
                    }
                }
            }

		}
		return $other_img;
	}
	public function checkUserOrder( int $user_id, int $order_id )
	{
		return $this->db->query("SELECT order_no FROM ".DB_PREFIX."order WHERE customer_id='".(int)$user_id."' AND order_id='".(int)$order_id."' ")->num_rows;
	}
	public function checkFailedAttempts( int $user_id, string $reset_counter='')
	{
		$this->load->model('account/customer');
		$user = $this->checkCustomerByID($user_id);
		if ($reset_counter) {
			$this->model_account_customer->deleteMobileLoginAttempts($user['telephone']);
		}
		if($attempts = $this->model_account_customer->getMobileLoginAttempts($user['telephone'])){
			if ($attempts['total_mobile'] > 4) {
				$rt['error_code'] = '1003';
				$rt['status'] = '0';
				$rt['status_text'] = 'Request failure';
				$rt['message'] = 'Security Breach attempt Please contact us';

				echo json_encode($rt);
				exit();
			} else {
				return true;;
			}
		} else {
			return true;;
		}
	}
	public function addInvalidAttempt( int $user_id )
	{
		$user = $this->checkCustomerByID($user_id);
		$this->load->model('account/customer');
		$this->model_account_customer->addMobileLoginAttempt($user['telephone']);
		return true;
	}


	public function invoice( int $order_id, string $type, int $customer_id, string $app_version_code = '') {

		$this->load->language('account/invoice');
		$this->load->model('restapi/service');
		$this->load->model('tool/image');
		$data['title'] = $this->language->get('text_invoice');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$data['text_invoice'] = $this->language->get('text_invoice');
		$data['text_retail']    = $this->language->get('text_retail');
		$data['text_tin_no']    = $this->language->get('text_tin_no');
		$data['text_seller_tin_no']    = $this->language->get('text_seller_tin_no');
		$data['text_buyer_tin_no']    = $this->language->get('text_buyer_tin_no');
		$data['text_order_detail'] = $this->language->get('text_order_detail');
		$data['text_order_no'] = $this->language->get('text_order_no');
		$data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$data['text_invoice_date'] = $this->language->get('text_invoice_date');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_helpline'] = $this->language->get('text_helpline');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_whatsapp'] = $this->language->get('text_whatsapp');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_website'] = $this->language->get('text_website');
		$data['text_ship_to'] = $this->language->get('text_ship_to');
		$data['text_payer'] = $this->language->get('text_payer');
		$data['text_payment_method'] = $this->language->get('text_payment_method');
		$data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$data['text_dupatta_taxfree'] = $this->language->get('text_dupatta_taxfree');
		$data['text_additional_octroi'] = $this->language->get('text_additional_octroi');
		$data['text_customer_declaration'] = $this->language->get('text_customer_declaration');
		$data['text_pre_declaration'] = $this->language->get('text_pre_declaration');
		$data['text_post_declaration'] = $this->language->get('text_post_declaration');
		$data['text_signature'] = $this->language->get('text_signature');
		$data['text_total_pieces'] = $this->language->get('text_total_pieces');
		$data['text_cod'] = $this->language->get('text_cod');
		$data['text_prepaid'] = $this->language->get('text_prepaid');
		$data['text_cst'] = $this->language->get('text_cst');
		$data['text_tax_refund'] = $this->language->get('text_tax_refund');

		$data['column_product'] = $this->language->get('column_product');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_sets'] = $this->language->get('column_sets');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_pieces'] = $this->language->get('column_pieces');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_tax']   = $this->language->get('column_tax');
		$data['column_comment'] = $this->language->get('column_comment');

		$this->load->model('account/invoice');

		$data['orders'] = array();

		$order_info = $this->model_account_invoice->getOrder($order_id, $customer_id);

		if ($order_info) {

			// Getting Tin Number of Payment and Shipping address
			$payment_tin_no = '';
			$shipping_tin_no = '';
			$data['account_custom_fields'] = array();
			$custom_fields = $this->model_account_invoice->getCustomFields();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['type'] == 'text' && $custom_field['location'] == 'address' && $custom_field['name'] == 'TIN Number') {

					if (isset($order_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
						if (!($type == 'b2c'))
							$shipping_tin_no = $order_info['shipping_custom_field'][$custom_field['custom_field_id']];
					}

					if (isset($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
						if (!($type == 'b2c'))
							$payment_tin_no = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
					}
				}
			}
			if (($order_info['order_status_id'] == 9)  && ($order_info['payment_code'] == 'bank_transfer' ||  $order_info['payment_code'] == 'cod'))  {
				$should_upload_bank_slip = true;
			} else {
				$should_upload_bank_slip = false;
			}


			$store_info = $this->model_account_invoice->getSetting('config', $order_info['store_id']);

			if ($store_info) {
				$store_address = $store_info['config_address'];
				$store_email = $store_info['config_email'];
				$store_telephone = $store_info['config_telephone'];
				$store_fax = $store_info['config_fax'];
			} else {
				$store_address = $this->config->get('config_address');
				$store_email = $this->config->get('config_email');
				$store_telephone = $this->config->get('config_telephone');
				$store_fax = $this->config->get('config_fax');
			}

            $invoice_no = '';

			if ($order_info['invoice_no']) {
                $this->load->model('account/order');
                $order_history = $this->model_account_order->getOrderHistoryByStatus($order_id, 15);
                if (count($order_history) > 0) {
                    $invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                }
			}


			if (!$payment_tin_no) {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}';
			} else {
				$format = '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}';
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
					'firstname' => trim($order_info['payment_firstname']),
					'lastname'  => trim($order_info['payment_lastname']),
					'company'   => ( (!$payment_tin_no and $order_info['payment_company']) ? ('c/o ' . $order_info['payment_company']) : ($order_info['payment_company']) ),
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city_postcode'  => trim($order_info['payment_city'])."".( $order_info['payment_postcode'] ? ('- ' . $order_info['payment_postcode']) : '' ),
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
			);

			$payment_address = $replace;
			if (!$shipping_tin_no) {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}';
			} else {
				$format = '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}';
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
					'firstname' => trim($order_info['shipping_firstname']),
					'lastname'  => trim($order_info['shipping_lastname']),
					'company'   => ( (!$shipping_tin_no and $order_info['shipping_company']) ? ('c/o ' . $order_info['shipping_company']) : ($order_info['shipping_company']) ),
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city_postcode'  => trim($order_info['shipping_city']) ."". ( $order_info['shipping_postcode'] ? ('- ' . $order_info['shipping_postcode']) : '' ),
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
			);

			$shipping_name = $order_info['shipping_firstname'];
			if ($order_info['shipping_lastname'] ) {
				$shipping_name = $shipping_name . " " . $order_info['shipping_lastname'];
			}

			$shipping_address = $replace;
			$this->load->model('tool/upload');

			$product_data = array();

			$products = $this->model_account_invoice->getOrderProducts($order_id);
			$this->load->model('catalog/product');

			$total_pieces_order = 0;

			foreach ($products as $product) {

				$this->language->load('multiseller/multiseller');
				$data['column_seller'] = $this->language->get('ms_seller');
				// todo check
				$seller = $this->MsLoader->MsSeller->getSeller(
						$this->MsLoader->MsProduct->getSellerId($product['product_id']),
						array(
								'product_id' => $product['product_id']
						)
				);

				$option_data = array();

				$options = $this->model_account_invoice->getOrderOptions($order_id, $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
					);
				}

				$piece_in_set = (int)$product['piece_in_set'] > 1 ? (int)$product['piece_in_set'] : 1;
				$total_pieces_order += (int)($product['total_pieces']);

				$product_data[] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'image'    => $this->model_restapi_service->getFrontImage($product['image']),
						'other_images'    => $this->model_restapi_service->getOtherImages($product['product_id'], $this->model_restapi_service->getFrontImage($product['image'])),
						'id'   	=> $product['product_id'],
						'set_description'   => $product['set_description'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price_per_piece' => $this->currency->format($product['price_per_piece'], $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']),
						'tax'      => $this->currency->format($product['tax']),
						'total_pieces' => $product['total_pieces']
				);
			}

			$voucher_data = array();

			$vouchers = $this->model_account_invoice->getOrderVouchers($order_id);

			foreach ($vouchers as $voucher) {
				$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			$total_data = array();

			$totals = $this->model_account_invoice->getOrderTotals($order_id);
			$amount_payable = 0;

			foreach ($totals as $total) {

				if ($total['code'] == 'shipping') {
					$total['title'] = $this->language->get('text_shipping');
				} elseif ($total['code'] == 'tax') {
					$total['title'] = $this->language->get('text_tax');
				} elseif ($total['code'] == 'total') {
					$total['title'] = $this->language->get('text_total_amount');
					$amount_payable = (float)($total['value']);
				}

				$total_data[] = array(
						'title' => $total['title'],
						'code' => $total['code'],
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			$data['orders'] = array(
					'order_id'	         => $order_id,
					'order_no'           => $order_info['order_no'],
					'invoice_no'         => $invoice_no,
					'invoice_date'       => date($this->language->get('date_format_short'), strtotime($order_info['invoice_date'])),
					'date_added'         => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'         => $order_info['store_name']. "\n\n",
					'store_url'          => rtrim($order_info['store_url'], '/'),
					'store_address'      => $store_address,
					'store_email'        => $store_email,
					'store_telephone'    => $store_telephone,
					'store_fax'          => $store_fax,
					'email'              => $order_info['email'],
					'telephone'          => $order_info['telephone'],
					'shipping_name'      => $shipping_name,
					'shipping_address'   => $shipping_address,
					'shipping_method'    => $order_info['shipping_method'],
					'payment_address'    => $payment_address,
					'payment_method'     => $order_info['payment_method'],
					'payment_code'       => trim(strtolower($order_info['payment_code'])),
					'product'            => $product_data,
					'voucher'            => $voucher_data,
					'total'              => $total_data,
					'order_total'        => $this->currency->format($amount_payable, $order_info['currency_code'], $order_info['currency_value']),
					'comment'            => nl2br($order_info['comment']),
					'shipping_tin_no'    => $shipping_tin_no,
					'payment_tin_no'     => $payment_tin_no,
					'total_pieces_order' => $total_pieces_order,
					'cform_submit'       => $order_info['cform_submit'],
					'courier_partner'    => $order_info['courier_partner'],
					'tracking_no'        => $order_info['tracking_no'],
					'tracking_url'       => $order_info['tracking_url'],
					'bank_slip_image'    => $this->model_tool_image->resize($order_info['bank_slip_image'],375, 250),
					'should_upload_slip' => $should_upload_bank_slip,
					'cst_with_cform'     => $this->currency->format($order_info['cst_with_cform'], $order_info['currency_code'], $order_info['currency_value']),
					'refundable_cform'   => $this->currency->format($order_info['refundable_cform'], $order_info['currency_code'], $order_info['currency_value']),
					'wayBillReqd'        => $this->model_account_invoice->isWayBillReqd($order_id),
					'cst_lable'          =>  "CST(2%)",
					'cst_refundable_lable' => "Refundable Amount on Form C Submission"
			);
		}
		return $data;
	}
	public function trackNotification( int $notification_id, int $user_id, string $read_status )
	{
		$this->db->query("INSERT INTO ".DB_PREFIX."wsb_notification_tracking
						 SET
						 	`notification_id`= '".(int)$notification_id."',
							`customer_id` 	= '".(int)$user_id."',
							`read` 			= '".$this->db->escape($read_status)."',
							`date_added` 	= NOW()"
						);
	}
	public function getPriceFilterByCategoryForPrice_filter($category_id = '', $is_single=0)
	{
		$price_filter_option = array(
				'minimum_price' => (1),
				'maximum_price' => (20000)
		);

		return $price_filter_option;
	}

	public function getParentCategoryOfProduct(int $product_id){
		$sql = "SELECT c.category_id
				FROM oc_product p
				INNER JOIN oc_product_to_category p2c ON (p.product_id = p2c.product_id)
				INNER JOIN oc_category c ON (p2c.category_id = c.category_id)
				WHERE p.product_id = '".(int)$product_id."'
				AND c.parent_id = 0";
		$result = $this->db->query($sql);
		return $result->row;
	}

	// Get filter of product from solr
	public function CheckProductHaveStyleFilter($product_id){
		if(SOLR_ENABLED && SOLR_WSBOX_ENABLED){
			$solr = new SolrProduct($this);
			$results = $solr->getProductDataFromSolr($product_id);
			$filter_group_id = array();
			$filter['filter_name'] = '';
			if(isset($results['filter_group_df_3'])){
				$filter_group_id = explode(",",$results['filter_group_df_3'][0]);
				if(!empty($filter_group_id)){
					$filter_ids = implode(',', $filter_group_id);
					$filter = $this->getFilterName($filter_ids);
					if(!empty($filter)){
						$filter_name = '';
						foreach ($filter as $value) {
							$filter_name .= $filter_name.' '. $value['name'];
						}
						$filter['filter_name'] = $filter_name;
					}
				}
				$filter['seller_id'] = $results['seller_id'];
			}
		}else{
			// Get filter of product from MY-SQL
			$filters = $this->getStyleFilterProduct($product_id);
			if(!empty($filters)){
				$filter_group = array();
				foreach ($filters as $value) {
					$filter_group[] = $value['name'];
					$filter['seller_id'] = $value['seller_id'];
				}
				$filter['filter_name'] = implode(' ', $filter_group);
			}else{
				$filter['filter_name'] = '';
			}
		}
		return $filter;
	}

	public function getStyleFilterProduct( int $product_id ){
		$sql = "SELECT f.filter_id, fd.name, mp.seller_id
				FROM ".DB_PREFIX."product p
				LEFT JOIN ".DB_PREFIX."product_filter pf ON (p.product_id = pf.product_id)
				INNER JOIN ".DB_PREFIX."filter f ON pf.filter_id = f.filter_id
				INNER JOIN ".DB_PREFIX."filter_description fd ON (pf.filter_id = fd.filter_id)
				LEFT JOIN ".DB_PREFIX."ms_product mp ON (p.product_id = mp.product_id)
				WHERE p.product_id = '".(int)$product_id."'
				AND f.filter_group_id = 3
				AND fd.language_id = 1";
		$result = $this->db->query($sql);
		return $result->rows;
	}
	public function getFilterName( string $filter_ids ){
		$sql = "SELECT name
				FROM ".DB_PREFIX."filter_description
				WHERE filter_id IN (". $this->db->escape($filter_ids) .") AND language_id = 1";
		$result = $this->db->query($sql);
		return $result->rows;
	}
	public function getCategoryName( string $category_ids ){
		$sql = "SELECT name
				FROM ".DB_PREFIX."category_description
				WHERE category_id IN (". $this->db->escape($category_ids) .") AND language_id = 1";
		$result = $this->db->query($sql);
		return $result->row;
	}
	public function getProductSeller( int $product_id ){
		$sql = "SELECT seller_id FROM ".DB_PREFIX."ms_product WHERE product_id = '".(int)$product_id."'";
		$result = $this->db->query($sql);
		return $result->row;
	}

	public function getPriceOfProduct( int $product_id ){
		$sql = "SELECT p.price
				FROM ".DB_PREFIX."product p
				WHERE p.product_id = '".(int)$product_id."'";

		$result = $this->db->query($sql);
		return $result->row;
	}
	public function getReletedProduct($data){
		foreach ($data as $key => $value) {
			$filter_data[$key] 	= $value;
		}
		if(SOLR_ENABLED && SOLR_WSBOX_ENABLED){
			$filter_data['start'] 			= 0;
			$filter_data['limit'] 			= 20;
			$filter_data['sort'] 			= '';
			$filter_data['order'] 			= 'ASC';
			$filter_data['call_from'] 		= 'app';
			$filter_data['sort_data_by'] 	= 'hotness_value';

			$solr = new SolrProduct($this);
			$results = $solr->getProductFromSolr($filter_data);
		}else{
			$results = $this->model_restapi_service->getProducts($filter_data);
		}
		return $results;
	}

		/* *******
	 * Function : updateLocationFinderText
	 * Request Parameters :
	 * Type :
	 * Output : Boolean(true or false)
	 *
	 ******* */
	public function updateLocationFinderText($customer_id = '' , $mobile = '',$locationfinder){
		if(!empty(trim($locationfinder))){
			if($mobile != '0' && !empty($mobile)){
				if($this->db->query("UPDATE " . DB_PREFIX . "customer SET location_finder_description = '" . $this->db->escape($locationfinder) . "' WHERE telephone = '" . $this->db->escape($mobile) . "'")){
					return true;
				}else{
					return false;
				}
			}else{
				if($this->db->query("UPDATE " . DB_PREFIX . "customer SET location_finder_description = '" . $this->db->escape($locationfinder) . "' WHERE customer_id = '" . (int)$customer_id . "'")){
					return true;
				}else{
					return false;
				}
			}
		}else{
			return true;
		}
	}


	public function getMasterPreferences(){
		$sql = "SELECT * FROM ". DB_PREFIX ."master_preference";
		$result = $this->db->query($sql);
		$preferences = $result->rows;
		$i = 0;
		foreach ($preferences as $value) {
			// Get Category Name with category id
			$results = $this->getCategoryName($value['category_id']);
			$data[$i]['category_id'] 	= $value['category_id'];
			$data[$i]['category_name'] 	= $results['name'];
			$data[$i]['category_image'] = $value['category_image'];

			// Get Filter Name with category id
			$filters = explode(',', $value['filter_id']);
			foreach ($filters as $filter_id) {
				$filter_info = $this->getFilterWithGroup($filter_id);
				if ($filter_info) {
					$data[$i]['filters'][] = array(
						'filter_group_id' 	=> $filter_info['filter_group_id'],
						'filter_group'  	=> $filter_info['group_name'],
						'filter_id' 		=> $filter_info['filter_id'],
						'filter_name'   	=> $filter_info['filter_name']
					);
				}
			}
		$i++;
		}
		return $data;
	}
	public function getFilterWithGroup( int $filter_id ) {

		$sql = "SELECT *, fgd.name AS group_name, fd.name AS filter_name
				FROM " . DB_PREFIX . "filter f
				INNER JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id)
				INNER JOIN " . DB_PREFIX . "filter_group_description fgd ON (f.filter_group_id = fgd.filter_group_id)
				WHERE f.filter_id = '" . (int)$filter_id . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getCustomerSelectedPreferences( int $customer_id ): array
	{
		$this->load->model("preferences");
		return $this->model_preferences->getCustomerPreferencesByPreferenceType( $customer_id, '', 'cust' );
	}

	public function getCustomerPreferences( int $customer_id ){
		$sql = "SELECT DISTINCT(category_id), max(customer_selected) AS customer_selected FROM " . DB_PREFIX . "customer_preferences WHERE customer_id= '".(int)$customer_id."' GROUP BY category_id";
		$query = $this->db->query($sql);
    if($query->num_rows) {
      foreach ($query->rows as $key => $row) {
        $price_sql = "SELECT min(min_price) AS min_price, max(max_price) AS max_price FROM " . DB_PREFIX . "customer_preferences WHERE customer_id = '".(int)$customer_id."' AND category_id = '".(int)$row['category_id']."'";
        if($row['customer_selected'] == '1') {
          $price_sql .= " AND customer_selected = 1 ";
        }
        $price_result = $this->db->query($price_sql);
        $query->rows[$key]['min_price'] = $price_result->row['min_price'];
        $query->rows[$key]['max_price'] = $price_result->row['max_price'];
      }
    }
		return $query->rows;
	}

	public function customerPreferencesSavedOrNot( int $customer_id ): bool
	{
		$this->load->model("preferences");
		return $this->model_preferences->checkPreferenceExistence( $customer_id, 'cust' );
	}

	public function getFilterPreferences($filterfilters, $i, $data, $cust_filters=''){
		$j = 0;
		foreach ($filterfilters as $value) {
			$data[$i]['filters'][$j]['filter_group_id'] 	= $value['filter_group_id'];
			$data[$i]['filters'][$j]['filter_group_name'] 	= $value['filter_group'];
			$k = 0;
			foreach ($value['filter'] as $master_filter) {
				if(!empty($cust_filters)){
					$filter_id = explode(',', $cust_filters);
					foreach ($filter_id as $filters) {
						if($filters == $master_filter['filter_id']){
							$data[$i]['filters'][$j]['filter'][$k]['is_selected'] 	= 1;
							$data[$i]['filters'][$j]['filter'][$k]['filter_id'] 	= $master_filter['filter_id'];
							$data[$i]['filters'][$j]['filter'][$k]['filter_name'] 	= $master_filter['filter_name'];
							break;
						}else{
							$data[$i]['filters'][$j]['filter'][$k]['is_selected'] 	= 0;
							$data[$i]['filters'][$j]['filter'][$k]['filter_id'] 	= $master_filter['filter_id'];
							$data[$i]['filters'][$j]['filter'][$k]['filter_name'] 	= $master_filter['filter_name'];
						}
					}
				}else{
					$data[$i]['filters'][$j]['filter'][$k]['is_selected'] 	= 0;
					$data[$i]['filters'][$j]['filter'][$k]['filter_id'] 	= $master_filter['filter_id'];
					$data[$i]['filters'][$j]['filter'][$k]['filter_name'] 	= $master_filter['filter_name'];
				}
			$k++;
			}
		$j++;
		}
		return $data;
	}
	public function getPriceByCategory( int $category_id ){

		$sql = "SELECT MAX(p.selling_price) as max_price, MIN(p.selling_price) as min_price
				FROM oc_product p
				INNER JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id)
				INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				WHERE pc.category_id = '".(int)$category_id."'
				AND p.status = '1'
				AND p.quantity > 0
				AND p.selling_price > 0
				AND p.stock_status_id != 5
				AND p.date_available <= NOW()
				AND p2s.store_id IN (".WSB_STORES_ID.")";
		$price_result = $this->db->query($sql);

		$minimum_price = $price_result->row['min_price'];
		$maximum_price = $price_result->row['max_price'];

		if($this->currency->getCode() == 'INR'){
			$price_values = $this->currency->currencies['INR']['value'];
			$currency 	  = 'INR';
		}else{
			$price_values = $this->currency->currencies['USD']['value'];
			$currency 	  = 'USD';
		}

		$price = array(
				'minimum_price' => (int)($minimum_price*$price_values),
				'maximum_price' => ceil($maximum_price*$price_values),
				'currency'		=> $currency

		);

		return $price;
	}

	public function updatePreferencesFromApp( int $user_id, array $preferences ): bool
	{
	    if( !empty( $preferences )) {

	    	$price_values = $this->currency->getCode() == 'INR' ? $this->currency->currencies['INR']['value'] : $this->currency->currencies['USD']['value'];

	    	$final_prefs = array();

	    	foreach ( $preferences as $value ) {
				
				$category_id = (int) $value['category_id'];
				$price_min   = 0;
				$price_max   = 0;

				if( !empty( $value['min_price'] )) {
					$price_min = (int) $value['min_price']/$price_values;
				}

				if( !empty( $value['max_price'] )) {
					$price_max = $value['max_price']/$price_values;
				}

				$final_prefs[ $user_id ][ $category_id ] = array(
																"min_price" => $price_min,
																"max_price" => $price_max
															);
			}

			if ( !empty( $final_prefs )) {
				$this->load->model('preferences');
				return $this->model_preferences->setCustomerPreferencesFromApp( $final_prefs );
			}
	    }

	    return true;
	}

	public function getCustomerTypes()
	{
		$sql = "SELECT customer_type_id, type
				FROM ".DB_PREFIX."customer_type
				ORDER BY `oc_customer_type`.`order` ASC ";
		return $this->db->query($sql)->rows;
	}

	public function checkAccessTokenAndUserId($request) {
		$this->load->language('error/error_code');

		if (isset($request['access_token'])) {

			if (isset($request['user_id'])) {
				$check_access_token = $this->checkUserByAccessToken($request['access_token'],$request['user_id']);

				if ($check_access_token > 0) {
					return false;
				} else {
					$message = $this->language->get('error_1002');
				}

			} else {
				$message = sprintf($this->language->get('error_1001'), 'user id');
			}

		} else {
			$message = sprintf($this->language->get('error_1001'), 'access token');
		}

		return $data = array(
						'message' => $message,
						'status'  => '0',
						'status_message' => 'Failed'
						);
	}

	public function getDefaultAddress( int $customer_id ) {

	    $sql = 'SELECT c.address_id, a.* FROM '.DB_PREFIX.'customer c '.
               ' INNER JOIN '. DB_PREFIX .'address a'.
                ' ON c.address_id = a.address_id '.
               ' WHERE c.customer_id = '. (int)$customer_id;

        $query = $this->db->query($sql);

        if ($query->num_rows > 0 ) {
            $address_data['postcode'] = $query->row['postcode'];
            $address_data['city'] = $query->row['city'];
            $address_data['zone_id'] = $query->row['zone_id'];
            $address_data['address_1'] = $query->row['address_1'];
            $address_data['address_2'] = $query->row['address_2'];
            $address_data['country_id'] = $query->row['country_id'];

        }else{
            $address_data['postcode'] = '';
            $address_data['city'] = '';
            $address_data['zone_id'] = 0;
            $address_data['address_1'] = '';
            $address_data['address_2'] = '';
            $address_data['country_id'] = 0;
        }

        return $address_data;
    }

	/**
     *
     */
	public function getSupportPersonDetail($registry, int $user_id, $has_website = 0) {

        $response = '';

	    if ($user_id > 0) {
            $registry->set('customer_id', $user_id);
            $telephone = $this->customer->getTelephone();
            $this->load->model('lead/lead');
            $staff_info = $this->model_lead_lead->getStaffDataAssignedToLead($telephone);

            /*** removing staff details if staff is Admin/TL/ZH ***/
            $skipped_support_role_ids = array(CRM_ROLE_ADMIN, CRM_ROLE_TEAM_LEAD);
            if(!empty($staff_info)) {
                if(isset($staff_info['role_id']) && in_array($staff_info['role_id'], $skipped_support_role_ids)) {
                    $staff_info = 0;
                }
            }

			$store_id = $this->config->get('config_store_id');
			if($store_id == INTERNATIONAL_STORE_ID){
				$phone = '+919116134795';
			}else{
				$phone = '8696491521';
			}

			$main_whatsapp_info = array(
				'name' => 'WholesaleBox SupportDesk',
				'phone' => $phone
			);

			$return_help_info = array(
                'name' => 'Wholesalebox Returns Helpline',
                'phone' => $this->returns_helpline_number
            );

            $return_courier_info = array(
                'name' => 'Wholesalebox Courier Delivery',
                'phone' => $this->courier_delivery_number
            );

			$return_courier_info_2 = array(
                'name' => 'Wholesalebox Courier Delivery2',
                'phone' => $this->courier_delivery_number_2
            );

            $credit_help_info = array(
                'name' => 'WholesaleBox Credit Helpline',
                'phone' => $this->credit_helpline_number
            );

            if ($staff_info == 0) {
                $response = array($main_whatsapp_info,$return_help_info,$return_courier_info,$credit_help_info,$return_courier_info_2);
            } else {
                $staff_info['time'] = "10.00 to 19.00";
                $staff_info['name'] =  ucfirst($staff_info['name']).' WholesaleBox Support';
                $response = array($staff_info, $main_whatsapp_info,$return_help_info,$return_courier_info,$return_courier_info_2);
            }

            if(!empty( $has_website )) {
				$fashcart_help_info = array(
	                'name' => 'Wholesalebox FREE Website',
	                'phone' => $this->fashcart_helpline_number
	            );
	            $response[] = $fashcart_help_info;
            }

			$wholesalebox_notification_number = array(
                'name' => 'WholesaleBox notifications',
                'phone' => $this->wholesalebox_notification_number
            );

			$response[] = $wholesalebox_notification_number;

            return $response;
        }

    }
    public function checkFirstTimeAppInstallation( int $customer_id )
    {
    	$sql = "SELECT ws_access_token FROM oc_customer WHERE customer_id = ".(int)$customer_id;

    	$token = $this->db->query($sql)->row['ws_access_token'];

    	if ($token) {
    		return false;
    	}else {
    		return true;
    	}
    }

    /**
     * Force Update
     */
    public function forceUpdate($customer_id, $data=array()) {
        if( isset($this->restapi->getRequestHeader()['REQUEST_BY']) && strtoupper($this->restapi->getRequestHeader()['REQUEST_BY']) == 'IOS_APP') {
			$app_version 				= IOS_APP_VERSION;
			$unstable_apk_array 		= IOS_UNSTABLE_APK_ARRAY;
			$minimum_app_version_code 	= IOS_MINIMUM_APP_VERSION_CODE_REQUIRED;
		}else {
			$app_version 				= ANDROID_APP_VERSION; //current Android app version is 60 .update/change ANDROID_APP_VERSION to APP_VERSION in app version >= 65.
			$unstable_apk_array 		= ANDROID_UNSTABLE_APK_ARRAY;
			$minimum_app_version_code 	= ANDROID_MINIMUM_APP_VERSION_CODE_REQUIRED;
		}

		$unstable_apk_array = explode(",", $unstable_apk_array);

        $request_data = $this->restapi->getRequestData();
        $current_version_code = $request_data['app_version_code'];

        if ($current_version_code < $minimum_app_version_code) {
            $compulsory_update_flag = "must";
        } else if (in_array($current_version_code, $unstable_apk_array)){
            $compulsory_update_flag = "must";
        } else if($current_version_code < $app_version) {
            $compulsory_update_flag = "may";
        } else {
            $compulsory_update_flag = "not";
        }

        return $compulsory_update_flag;
    }

	public function setCustomerDislikeProduct( int $customer_id, $product_ids ){

        // Sanitizing product_id(s) input string (comma separated)
		$arr_product_ids = array_unique(array_filter(array_map('intval', explode(',', $product_ids))));

		// Preparing Batch Insert SQL
        $values = array();
        foreach ($arr_product_ids as $pid) {
            $values[] = "(" . (int)$customer_id . "," . (int)$pid . ")";
        }

		if ( !empty($values) ) {
		    $sql = "INSERT IGNORE INTO wsb_customer_dislike ";
		    $sql .= "(customer_id, product_id) VALUES ";
		    $sql .= implode(',', $values);

		    $this->db->query($sql);
        }
	}

	public function getCustomerDislikedProduct( int $customer_id ){
		$sql = "SELECT GROUP_CONCAT(product_id) AS product_ids 
                FROM wsb_customer_dislike 
				WHERE customer_id = ".(int)$customer_id;
		return $this->db->query($sql)->row;
	}


	public function addOtpForMobile($data)
	 {
	 	$rows = $this->db->query("SELECT otp from `" . DB_PREFIX . "customer_signups` where  telephone = '" . $this->db->escape($data['mobile']) . "' and otp_verified = 0")->row;

        if(count($rows) == 0)
        {
		  $query = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_signups` set country_code = '" . $this->db->escape($data['country_code']) . "', telephone = '" . $this->db->escape($data['mobile']) . "', session_id = '" . $this->db->escape($data['session_id']) . "', ip = '" . $this->db->escape($data['ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', otp = '" . $this->db->escape($data['otp']) . "', date_added = '".date("Y-m-d h:i:s")."', otp_page = '" . $this->db->escape($data['otp_page']) . "', customer_id = '" . (int)$data['customer_id'] . "', otp_verified=0");
		  $otp = 0;
		}
		else
		{
		  $otp = $rows['otp'];
		}

		return $otp;
	 }

	public function addOtpForEmail($data)
	 {
	 	$rows = $this->db->query("SELECT otp from `" . DB_PREFIX . "customer_signups` where  email = '" . $this->db->escape($data['mobile']) . "' and otp_verified = 0")->row;

        if(count($rows) == 0)
        {
		  $query = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_signups` set email = '" . $this->db->escape($data['mobile']) . "', session_id = '" . $this->db->escape($data['session_id']) . "', ip = '" . $this->db->escape($data['ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', otp = '" . $this->db->escape($data['otp']) . "', date_added = '".date("Y-m-d h:i:s")."', otp_page = '" . $this->db->escape($data['otp_page']) . "', customer_id = '" . (int)$data['customer_id'] . "', otp_verified=0");

		  $otp = 0;

		}
		else
		{
		  $otp = $rows['otp'];
		}
		return $otp;
	 }


	public function checkMasterOTP($data)
	 {
	 	$rows = $this->db->query("SELECT staff_id from `" . DB_PREFIX . "sales_staff` where  active_status = '1' and master_otp = '" . (int)$data['otp'] . "'")->row;

       if(count($rows) > 0)
        {
           $staff_id = $rows['staff_id'];
           $rows = $this->db->query("SELECT signup_id, otp_page from `" . DB_PREFIX . "customer_signups` where  (telephone = '" . $this->db->escape($data['mobile']) . "' or email = '" . $this->db->escape($data['mobile']) . "') and otp_page = '" . $this->db->escape($data['otp_page']) . "' and otp_verified = 0")->row;
           if(count($rows) > 0) { $rows['staff_id'] = $staff_id; }
        }

	 	return $rows;
	 }


	public function checkOTP($data)
	 {
        $rows = $this->db->query("SELECT signup_id, otp_page from `" . DB_PREFIX . "customer_signups` where  (telephone = '" . $this->db->escape($data['mobile']) . "' or email = '" . $this->db->escape($data['mobile']) . "') and otp = '" .(int)$data['otp']. "' and otp_page = '" . $this->db->escape($data['otp_page']) . "' and otp_verified = 0")->row;

	 	return $rows;
	 }

	public function verifyOTP($data)
	 {
	 	 $this->db->query("UPDATE `" . DB_PREFIX . "customer_signups` set otp_verified = 1, staff_id = '" . $this->db->escape(isset($data['staff_id']) ? $data['staff_id'] : 0) . "' where signup_id = '" . $this->db->escape($data['signup_id']) . "'");
	 }

	public function checkEmailMobileVerified( string $mobile )
	 {
	 	 $query = $this->db->query("SELECT signup_id from " . DB_PREFIX . "customer_signups
                                    where ( email = '" . $this->db->escape($mobile) . "'
                                            OR telephone = '".$this->db->escape($mobile)."')
                                          and otp_verified = 1
                                          and staff_id = 0");
	 	 return $query->num_rows;
	 }

	 public function setCustomerFeedback( int $customer_id, $feedback_text ) {
	 	 $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_feedback` SET customer_id = ".(int)$customer_id.", customer_feedback = '" . $this->db->escape($feedback_text)."', date_added = now()");
	 }

    public function getblacklist(){

        $query = $this->db->query("SELECT GROUP_CONCAT(mobile)AS mobile, GROUP_CONCAT(app)AS app, GROUP_CONCAT(name)AS name FROM `oc_wsb_blacklist`");
        return $query->row;
    }

	public function requestExclusiveCollection( int $customer_id, $image_name_1, $image_name_2) {
		$result = $this->db->query("SELECT CONCAT(c.firstname, ' ', c.lastname) as name, c.email, c.telephone, ct.type, GROUP_CONCAT(DISTINCT(cd.name)) as category_preference
									from `" . DB_PREFIX . "customer` c
									LEFT JOIN oc_customer_type ct ON (c.customer_type_id = ct.customer_type_id)
									LEFT JOIN oc_customer_preference cp On ( c.customer_id = cp.customer_id )
									LEFT JOIN oc_category_description cd ON (cp.category_id = cd.category_id)
									where cd.language_id = 1 AND c.customer_id = " . $this->db->escape($customer_id))->row;

		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');
		$mail->setFrom($this->config->get('config_email'), 'WholesaleBox');
		$mail->addAddress(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
		$mail->Subject = 'Request to see Exclusive inventory';
		$html = '<div> <span> Name: '. $result['name'] .' </span> <br> <span> Email: '. $result['email'] .'</span> <br> <span> Telephone: '. $result['telephone'] .'</span> <br> <span> Customer Type: '. $result['type'] .'</span> <br> <span> Category Preference: '. $result['category_preference'] .'</span> </div>';

		if(!empty($image_name_1)){
			$image_dest = DIR_IMAGE.'request_for_exclusive/'.$image_name_1;
			if(file_exists($image_dest)){
				$mail->addAttachment($image_dest);
			}
		}
		if(!empty($image_name_2)){
			$image_dest = DIR_IMAGE.'request_for_exclusive/'.$image_name_2;
			if(file_exists($image_dest)){
				$mail->addAttachment($image_dest);
			}
		}

		$mail->msgHTML($html);
		$mail->send(1);

	}

    public function logForDebug($method, $request, $response) {

        // Uncomment the below lines to enable logging.
        // To be used only when required to debug.
        // Once debugging is done, dont forget to comment out this portion again
        /*
        $txt = "\n*********************************************************\n";
        $myfile = fopen(DIR_LOGS."log_rest_api_tmp.txt", "a");
        $txt .= date("Y-m-d H:i:s").' '.$method;
        $txt .= "\n";
        $txt .= json_encode($request);

        $txt .= "\n-----------Headers--------------:\n";
        $txt .= json_encode($this->restapi->getRequestHeader());

        $txt .= "\n-----------Response--------------:\n";
        $txt .= json_encode($response);
        $txt .= "\n*********************************************************\n";
        $txt .= "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        */

        return true;
    }

    public function getLatestProducts($category_id, $user_id, $view_more, $page, $group_limit = 2, $filter_data = null, $flag = null, $request_data = null) {
    	$solr = new SolrProduct($this);
    	if($view_more == 0){
    		$category_ids = array();
    		$category_ids = explode(',', $category_id);
    		$new_arr = array();
    		if(!empty($category_ids)) {
    			foreach($category_ids as $cat_ids){
    				$filter = array();
    				if(!empty($category_id)){
    					$filter['filter_category_id'] = $cat_ids;
    				}

    				if(!empty($filter_data) && isset($filter_data['filter_filter'])){
    					$filter['filter_filter']   = $filter_data['filter_filter'];
    				}
    				if(!empty($filter_data) && isset($filter_data['franchise_id'])){
    					$filter['franchise_id'] = $filter_data['franchise_id'];
    				}
					$filter['is_group']           = 1;
					$filter['group_field']        = 'seller_id';
					$filter['group_sort_data_by'] = 'date_added';
					$filter['group_get_p_id']     = 1;
					$filter['group_limit']        = $group_limit;
					$filter['is_latest']          = 1;
					$filter['custom_store']       = $filter_data['custom_store'];

    				$return = $solr->getProductFromSolrOnly($filter)['result_group_field'];
    				$filter['is_latest'] 		= 0;

    				foreach($return as $val){
    					$new_arr[] = $val;
    				}
    			}
    		}
    		if(!isset($new_arr[12])) {
    			$group_limit = $group_limit*2;
    			if ( (int)$group_limit <= 8 ) {
    				$this->getLatestProducts($category_id, $user_id, $view_more, $page, $group_limit, $filter_data, $flag, $request_data);
    			}
    		}
    		arsort($new_arr);
    		$new_arr_unique = array_unique($new_arr);
    		$sliced_array = array_slice($new_arr_unique, 0, 12);

    		$filter = array();
    		$filter['product_ids'] 	= implode(',', $sliced_array);
    		if ($flag != 'blog') {
    			$filter['call_from']    = 'app';
    		}

    		$filter['user_id'] 		= $user_id;
    		$filter['filter_limit'] = 12;
			$filter['custom_store'] = $filter_data['custom_store'];

			if ( !empty($filter['product_ids']) ) {
				$final_result = $solr->getProductFromSolr($filter);
			} else {
				$final_result = array();
				$final_result['products'] = array();
				$final_result['product_total'] = 0;
				$final_result['total'] = 0;
				$final_result['data'] = array();
			}

    		return $final_result;
    	}else{
    		$last 	= $page*10;
    		$curent = $last-9;

    		$filter = $request_data;

    		if(!empty($request_data) && isset($request_data['filter'])){
    			$filter['filter_filter']   = $request_data['filter'];
    		}
    		if(!empty($filter_data) && isset($filter_data['franchise_id'])){
    			$filter['franchise_id'] = $filter_data['franchise_id'];
    		}
    		if (!isset($request_data['price_filter'])) {
    			$filter['price_filter'] = "";
    		}
    		if (!isset($request_data['rating_filter'])) {
    			$filter['rating_filter'] = "";
    		}

    		if ($flag != 'blog') {
    			$filter['call_from']    = 'app';
    		}

    		$filter['user_id'] 			= $user_id;
    		$filter['sort'] 	= 'date_added';
    		$filter['order'] 	= 'desc';
			// $filter['sort_data_by'] 	= 'date_added';
			// $filter['order_data_by'] 	= 'desc';
    		$filter['filter_category_id'] = $category_id;
    		$filter['start'] 	= $curent-1;
    		$filter['limit'] 	= 10;
    		$filter['filter_limit'] 	= $curent."-10";
    		if(!empty($filter['filter_only'])) {
    			$filter['facets'] = true;
    		}
    		$filter['is_latest'] 		= 1;
    		isset($request_data['filter'])?$filter['clicked_filter'] = $request_data['filter']:"";

			$filter['custom_store']       = $filter_data['custom_store'];

    		$final_result = $solr->getProductFromSolr($filter);
    		return $final_result;
    	}

    }

    public function getHomeBanners(){
        $store_id = $this->config->get('config_store_id');

        if(INTERNATIONAL_STORE_ID == $store_id){
            $banner_id =  WHOLESALEBOX_APP_BANNERS;
        }else{
            $banner_id = WHOLESALEBOX_APP_BANNERS;
        }
          $sql = "SELECT * FROM " . DB_PREFIX . "banner b
                  INNER JOIN ". DB_PREFIX . "banner_image bi ON(b.banner_id  = bi.banner_id)
                  LEFT JOIN " . DB_PREFIX . "banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id)
                  WHERE bi.banner_id = '" . (int)$banner_id . "' AND bid.language_id = '" . (int)$this->config->get('config_language_id') . "'AND b.store_id = '" . (int)$store_id . "' AND bi.status = 1 ORDER BY bi.sort_order ASC";
                  $result = $this->db->query($sql);
                  if($result->num_rows){
                        return $result->rows;
                   }
    }
	public function getDynamicLayout(){
		$cache_data 		= array();
		$cache_file_name 	= 'dynamic_app_layouts';
		if(!empty($this->cache->get($cache_file_name))){
			$cache_data 	=  $this->cache->get($cache_file_name);
		}
		return $cache_data;
	}

	public function customerDataToTrack( int $customer_id )
	{
		$result = array();

		$sql = "SELECT 
					customer_type_id, 
					self_order, 
					master_id
				FROM " . DB_PREFIX . "customer
				WHERE 
					customer_id = ". (int) $customer_id;
		
		$customer_data = $this->db->query( $sql )->row;

		$result['customer_type'] = $customer_data['customer_type_id'];
		$result['Self Order']    = ( $customer_data['self_order'] > 0 ) ? "1" : "0";

        $membership_sql = "SELECT 
			        			id,
			        			membership_id,
			        			expiry_date
							FROM " . DB_PREFIX . "master_customer_membership
							WHERE 
								master_id = '". (int) $customer_data['master_id']. "'
								AND status = '1'
								AND expiry_date >= CURDATE()";

		$membership_data = $this->db->query( $membership_sql );

		$result['Membership'] = "0";
		
		if ( $membership_data->num_rows ) {
			$result['Membership'] = "1";
			$result['membership_plan_id'] = $membership_data->row['membership_id'] ?? null;
			$result['membership_expiry_date'] = $membership_data->row['expiry_date'] ?? null;
		}
		
		return $result;
	}

	public function getDisplayImage($filename, $size){

		if (file_exists(DIR_IMAGE . $filename)) {

            list($original_width, $original_height, $type, $attr) = getimagesize(DIR_IMAGE . $filename);

            $ratio = $original_width / $original_height;

            if ($ratio > 1) {

                $height = $size;

                $width = floor($size * $ratio);

            } else {

                $width = $size;

                $height = floor($size/$ratio);

            }

           $extension = pathinfo($filename, PATHINFO_EXTENSION);
           $new_image = 'cache/'.utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

           return $new_image;

        }
        return $filename;
	}

    public function updateBankDetailOtp( int $customer_id, string $otp ) {
        if($this->db->query("UPDATE " . DB_PREFIX . "customer SET  otp = '".$this->db->escape($otp)."' WHERE customer_id = '" . (int)$customer_id . "'")){
            return 1;
        }else{
            return 0;
        }
    }

    public function verifyBankDetailOtp( int $customer_id, string $otp ) {
        $sql = "SELECT customer_id FROM " . DB_PREFIX . "customer WHERE  otp = '".$this->db->escape($otp)."' AND customer_id = '" . (int)$customer_id . "'";
        $result = $this->db->query($sql);
        if($result->num_rows){
            return 1;
        }else{
            return 0;
        }
    }

	public function getStoreData(){
	    $stores = array();
	    $sql = "SELECT * FROM " . DB_PREFIX . "stores WHERE status = 1";
        $result = $this->db->query($sql);
        if($result->num_rows){
            $stores = $result->rows;
        }
        return $stores;
    }

    public function getCustomerIdUsingCRMUserId($crm_user_id){
	    $sql = "SELECT customer_id FROM ". DB_PREFIX ."sales_staff WHERE crm_user_id = ".(int)$crm_user_id;
	    $result = $this->db->query($sql);
	    if($result->num_rows){
	        return $result->row['customer_id'];
        }
        else{
	        return 0;
        }
    }

    /*
     * @Authour : Amaarat
     * Description : get customer detail, address detail, password for changelog
     */
    public function getCustomerDetail( int $customer_id ){
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "' ");
        $result = array();
        $result = $query->row;
        $credit_sql = "SELECT customer_id, credit_status
                                   FROM " . DB_PREFIX . "customer_credit
                                   WHERE customer_id = '" . (int)$customer_id . "' ";
        $credit_query = $this->db->query($credit_sql);
        if( $credit_query->num_rows ){
                $result['credit_status'] = $credit_query->row['credit_status'];
        } else {
                $result['credit_status'] = 0 ;
        }

        return $result;
    }

    public function getAddressDetail( int $address_id ){
        $address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

            if ($address_query->num_rows) {
                    $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

                    if ($country_query->num_rows) {
                            $country = $country_query->row['name'];
                            $iso_code_2 = $country_query->row['iso_code_2'];
                            $iso_code_3 = $country_query->row['iso_code_3'];
                            $address_format = $country_query->row['address_format'];
                    } else {
                            $country = '';
                            $iso_code_2 = '';
                            $iso_code_3 = '';
                            $address_format = '';
                    }

                    $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

                    if ($zone_query->num_rows) {
                            $zone = $zone_query->row['name'];
                            $zone_code = $zone_query->row['code'];
                    } else {
                            $zone = '';
                            $zone_code = '';
                    }

                    return array(
                            'address_id'     => $address_query->row['address_id'],
                            'customer_id'    => $address_query->row['customer_id'],
                            'firstname'      => $address_query->row['firstname'],
                            'lastname'       => $address_query->row['lastname'],
                            'company'        => $address_query->row['company'],
                            'address_1'      => $address_query->row['address_1'],
                            'address_2'      => $address_query->row['address_2'],
                            'postcode'       => $address_query->row['postcode'],
                            'city'           => $address_query->row['city'],
                            'zone_id'        => $address_query->row['zone_id'],
                            'zone'           => $zone,
                            'zone_code'      => $zone_code,
                            'country_id'     => $address_query->row['country_id'],
                            'country'        => $country,
                            'iso_code_2'     => $iso_code_2,
                            'iso_code_3'     => $iso_code_3,
                            'address_format' => $address_format
                    );
            }
    }

    public function getCustomerPassword( int $customer_id ) {
        $sql = "SELECT password FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "' ";
        $query = $this->db->query($sql);
        $result = array();
        $result = $query->row;
        return $result;
    }

    /*** End By Amarat ***/

    public function setOrderProductReviews($order_product_id, $review) {
    	$order_product_review_data = array();
    	$order_product_review_data['order_product_id'] = $order_product_id;
    	$order_product_review_data['product_review']   = $review;
    	$order_product_review_data['ip']               = getClientIpAddress();
    	$order_product_review_data['user_agent']       = $_SERVER['HTTP_USER_AGENT'];

        //---------Add/Update OrderProductReview in oc_order_product_review
    	OrderProductReview::addOrderProductReview($this->db, $order_product_review_data);

    	return true;
    }

  public function getColorHexCode($color_name) {
    $sql = "SELECT hex_code FROM ".DB_PREFIX."color WHERE name = '".$this->db->escape($color_name)."' LIMIT 1";
    $result = $this->db->query($sql);
    if($result->num_rows) {
      return $result->row['hex_code'];
    }
    return false;
  }

    public function getSalesStaffWithDeviceId( string $device_id ) {
      $sql = "SELECT * FROM ".DB_PREFIX."sales_staff WHERE device_id = '".$this->db->escape($device_id)."' AND active_status = '1' AND role = 'field' LIMIT 1";
      $result = $this->db->query($sql);
      if($result->num_rows) {
        return $result->row;
      }
      return false;
    }
    public function getFranchiseCategory() {

      $franchise_category = array();
      $franchise_sub_category = array();
      $franchise_category['category_id'] = '700000';
      $franchise_category['name'] = 'Franchise';
      $franchise_category['image'] = '';
      $franchise_category['images'][] = array(
                                          "image" => '',
                                          "image_height" => '130',
                                          "image_width" => '130',
                                          "sort_order" => '1'
                                        );
      $franchise_category['sub_categories'][] = array(
                                                  "sub_category_id" => '700000',
                                                  "sub_category_name" => 'All',
                                                  "image" => '',
                                                  "view_type_grid" => true
                                                );
      return $franchise_category;
    }

    public function getWishlistMessages($user_id) {
        // get default template
        $template = $this->getDefaultTemplate($user_id);
        $template_sql = "SELECT st.template FROM ".DB_PREFIX."share_templates st WHERE st.template_id=".(int)$template;
        $default_template_text_query = $this->db->query($template_sql);
        if(!empty($default_template_text_query->row['template'])) {
            $default_template_text = $default_template_text_query->row['template'];
        } else {
            $default_template_text = '[product_name] in just [markup_price] from -[shop_name], [mobile]';
        }

        // get margin
        $update_price_by = $this->getCustomerSettingByKey('update_price',$user_id);
        if (empty($update_price_by)) {
            $update_price_by = DEFAULT_SHARE_MARGIN;
        } else {
            $update_price_by = $update_price_by['value'];
        }

        // get wishlist data
        $wishlist_data = array();

        $sql = "SELECT product_id, share_message FROM " . DB_PREFIX . "customer_wishlist  WHERE customer_id = '" . (int)$user_id . "' ORDER BY id DESC";
        $query = $this->db->query($sql);
        foreach ($query->rows as $result) {
            $wishlist_data[] = array(
                'product_id' => $result['product_id'],
                'share_message' => $this->db->escape($result['share_message'])
            );
        }

        $result = array(
            'default_sharing_template' => $default_template_text,
            'sharing_margin' => $update_price_by,
            'wishlist_data' => $wishlist_data
        );

        return $result;
    }

    /*
     * method to update gcm_id(for android user) or apns_token(for ios user) of a customer
     *  */
    public function updateNotificationToken($data) {
        if (!isset($data['user_id'])) {
            return false;
        }

        if(!isset($data['gcm_id']) && !isset($data['apns_token'])) {
            return false;
        }

        if (isset($data['gcm_id'])) {
            $temp = " ws_gcm_registration_id='" . $this->db->escape($data['gcm_id']) . "'";
        } else {
            $temp = " apns_token='" . $this->db->escape($data['apns_token']) . "'";
        }

        $sql = "UPDATE ".DB_PREFIX."customer SET " . $temp . " WHERE customer_id=" . (int)$data['user_id'];

        return $this->db->query($sql);
    }

    public function trueCallerApi($signature, $package) {

        require_once(DIR_SYSTEM . '/library/truecallersdk/vendor/phpseclib/phpseclib/phpseclib/Crypt/RSA.php');

        // Public Key Fetched from 'https://api4.truecaller.com/v1/key'
        $key = TRUECALLER_API_KEY;

        $rsa = new Crypt_RSA();
        $rsa->setHash("sha512");
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $rsa->loadKey( $key );

        if ($rsa->verify( $package, base64_decode($signature) ) ) {
            return True;
        } else {
            return False;
        }
    }

    /*
    * @method: updateCustomerDeviceInfo
    * @purpose: update device info into customer table
    * @params: customer_id, device_info,
    * @return: true or false
    * @author: Devendra, June 2018
    */
    public function updateCustomerDeviceInfo($customer_id, $device_info) {
      $sql = "UPDATE " . DB_PREFIX . "customer SET device_info='" . $this->db->escape($device_info) . "'
              WHERE customer_id='" . (int)$customer_id . "'";
      return $this->db->query($sql);
    }

    public function getProductsByCustomerPreferences($data) {

      $solr = new SolrProduct($this);
      $product_data = array();
      $customers = $data['customers'];

      if(!empty($data['page'])) {
        $page = $data['page'];
      }else{
        $page = 1;
      }

      if(!empty($data['limit'])) {
        $limit = $data['limit'];
      } else {
        $limit = 10;
      }
      if(!empty($data['handpicked_ids'])) {
        $handpicked_ids = $data['handpicked_ids'];
      } else {
        $handpicked_ids = '';
      }

      if( !empty($data['filter_tag']) ) {
        $filter_tag = $data['filter_tag'];
      } else {
        $filter_tag = '';
      }

      if( !empty($data['filter_special']) ) {
        $filter_special = $data['filter_special'];
      } else {
        $filter_special = '';
      }

      $price_filter = $data['price_filter'] ?? '';

      $request_for = $data['request_for'] ?? '';
      $total_products = 0;
      foreach ($customers as $key => $value) {

        $category_ids = '';

        if(is_array($value)) {
          $user_id = $key;
          $category_ids = implode(',', $value);
        } else {
          $user_id = $customers[$key];
        }

        $filter_data = array(
			'user_id'            => $user_id,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit,
			'page'               => $page,
			'call_from'          => 'customer_preference',
			'filter_category_id' => $category_ids,
			'sort'               => 'sort_order',
			'request_for'        => $request_for,
			'price_filter'       => $price_filter,
			'handpicked_ids'     => $handpicked_ids,
			'filter_tag'         => $filter_tag,
			'filter_special'     => $filter_special
        );

        /** this block is for fashcart upcoming designs
        	showing singles to dropshipper **/
        if( empty( $request_for ) && $this->getCustomerDropshipperStatus( (int) $user_id ) ) {
        	$filter_data['custom_store'] = 'single';
        	$filter_data['is_dropshipper'] = 1;
        }

        $products = $solr->getProductFromSolr($filter_data);
        $product_count = isset($products['products']) ? count($products['products']) : 0;
        $total_products += $product_count;
        $product_data[] = $products;
      }
      $product_data['total_products'] = $total_products;
      return $product_data;
    }

    /**************************************************
    @function: getCustomerLastOrderIdWithNonCancelledStatus
    @description: Function to get last order id of a customer with order status greater than cancelled
    @params:
    	   $customer_id: integer
    @return: order_id (integer)
    @author: Anurag Jain (6 July 2018)
    **************************************************/
    public function getCustomerLastOrderIdWithNonCancelledStatus(int $customer_id) {
        $sql = "SELECT
                    IF(
                        (oos.priority >= 9 AND (o.date_added BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()))
                            OR (oos.priority < 9),
                        o.order_id,
                        NULL
                    ) as order_id
                FROM " . DB_PREFIX . "order o
                    LEFT JOIN " . DB_PREFIX . "suborder osub
                        ON (osub.order_id = o.order_id)
                    INNER JOIN " . DB_PREFIX . "order_status oos
                        ON (oos.order_status_id = osub.order_status_id AND oos.language_id = '1')
                WHERE customer_id = '".(int)$customer_id."'
                    AND oos.priority > 1
                ORDER BY o.order_id DESC LIMIT 1";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            return $result->row['order_id'];
        }
        return null;
    }


    /**************************************************
    @function: getCustomerLastOrderIdWithAtleastOneDeliveredStatus
    @description: Function to get last order id of a customer with least one order status is deliverd
    @params:
    	   $customer_id: integer
    @return: order_id (integer)
    @author: Rahul Singh (23 Sep 2019)
    **************************************************/
    public function getCustomerLastOrderIdWithAtleastOneDeliveredStatus(int $customer_id) {
        $sql = "SELECT o.order_id
                FROM " . DB_PREFIX . "order o
                    LEFT JOIN " . DB_PREFIX . "suborder osub
                        ON (osub.order_id = o.order_id)
                    INNER JOIN " . DB_PREFIX . "order_status oos
                        ON (oos.order_status_id = osub.order_status_id AND oos.language_id = '1')
                WHERE customer_id = '".(int)$customer_id."'
                    AND NOT EXISTS (SELECT 1
                                          FROM oc_suborder s
                                          WHERE s.order_id = o.order_id
                                          AND s.order_status_id > 0
                                          AND s.order_status_id <> 2
                                          AND s.delivered_date IS NULL)
                    AND oos.priority >= 9
                ORDER BY o.order_id DESC LIMIT 1";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            return $result->row['order_id'];
        }
        return null;
    }

    /**
     * get customer data with address
     * @param int $customer_id [customer id]
     * @return array [customer data with address]
     * @author: Anurag Jain (17 Sept 2018)
     */
    public function getCustomerProfile( int $customer_id ): array {

	    $this->load->model('account/address');

    	$default_address = array();
	    $default_address = $this->model_account_address->getDefaultAddress($customer_id);

		$sql = "SELECT
					c.*,
					a.company,
					a.zone_id,
					a.city,
					a.country_id,
					a.address_1,
					a.address_2,
					a.postcode
				FROM
					" . DB_PREFIX . "customer c
					LEFT JOIN oc_address a ON c.customer_id = a.customer_id
				WHERE
					c.customer_id = '" . (int)$customer_id . "'";

        if( $default_address !== false ) {
			$sql .= " AND a.address_id = '". (int)$default_address['address_id'] ."'";
		}

		$result = $this->db->query($sql);

		if( $result->num_rows ) {
			return $result->row;
		}

		return array();
    }

    /**
     * @param int $customer_id [customer id]
     * @return boolean
     * @author: Anurag Jain (3 Oct 2018)
     * @comment: this method will be replaced with CustomerOrderInfo->getAllOrdersByCustomerId()
     */
    public function customerHasAtleastOneDeliveredOrder( int $customer_id ): bool
    {
    	$select_sql = " SELECT
	                        o.order_id
	                    FROM
	                       " . DB_PREFIX . "order AS o
	                    INNER JOIN
	                       " . DB_PREFIX . "suborder AS so ON so.order_id = o.order_id AND so.order_status_id > 0
	                    WHERE
	                       o.customer_id = '". (int)$customer_id ."'
	                       AND so.order_status_id IN (
														".(int)ORDER_STATUS['Complete'].",
														".(int)ORDER_STATUS['Delivered']."
													 )
	                    LIMIT 1 ";

	    $result = $this->db->query($select_sql);

		if( $result->num_rows ) {
			return true;
		}
		return false;
    }

    public function getSingleStoreAppBanners() {

        $store_id = $this->config->get('config_store_id');
        $banner_id = SINGLE_STORE_APP_BANNERS;
        $sql = "SELECT * FROM " . DB_PREFIX . "banner b
                  INNER JOIN " . DB_PREFIX . "banner_image bi ON(b.banner_id  = bi.banner_id)
                  LEFT JOIN " . DB_PREFIX . "banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id)
                  WHERE bi.banner_id = '" . (int) $banner_id . "' AND bid.language_id = '" . (int) $this->config->get('config_language_id') . "'AND b.store_id = '" . (int) $store_id . "' AND bi.status = 1 ORDER BY bi.sort_order ASC";
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            return $result->rows;
        } else {
            return array();
        }
    }



    /**
	 * Function : saveCustomerLocation
	 * Request Parameters : user_id,device_id,location_lat,location_lng,timestamp
	 * Return : location save or not
	 * Author Rahul Singh, 22 Nov 2018
	 * */

    public function saveCustomerLocation($request){
    	$device_id=$this->db->escape($request['device_id']);
    	$customer_id=$this->db->escape($request['user_id']);
    	$sql = "INSERT INTO ". DB_PREFIX ."customer_location (`customer_id`,
																    `device_id`,
																    `location_lat`,
																    `location_lng`,
																    `location_timestamp`,
																    `day`,
																    `accuracy`,
																    `actual_location_timestamp`,
																    `created`,
																    `modified`
																  ) VALUES ";

		$location_count=count($request['location']);
		$k=1;
		if(!empty($request['location'])){
			foreach($request['location'] as $key => $value) {
						$time_in_second = round($value['location_timestamp']/1000);
						$actual_location_timestamp_second = round($value['actual_location_timestamp']/1000);
					    $day = date('l', $time_in_second);
					    $location_timestamp = $this->db->escape($time_in_second);
					    $location_lat = $this->db->escape($value['location_lat']);
					    $location_lng = $this->db->escape($value['location_lng']);
					    $accuracy = $this->db->escape($value['accuracy']);
					    $create = $modified = date('Y-m-d H:i:s');

					    $sql .= "('".(int)$customer_id."',
								    '".$device_id."',
								    '".$location_lat."',
								    '".$location_lng."',
								    '".$location_timestamp."',
								    '".$day."',
								    '".$accuracy."',
								    '".$actual_location_timestamp_second."',
								    '".$create."',
								    '".$modified."')";
					    if ($k != $location_count) {
					        $sql .= ", ";
					    }
					    $k++;
					}
		if($this->db->query($sql)){
			return 1;
		}else{
			return 0;
		}

		}else{
			return 0;
		}
    }



     /**
	 * Function : getCustomerLocationCount
	 * Request Parameters : customer_id
	 * Return : customer location save count
	 * Author Rahul Singh, 22 Nov 2018
	 * */

    public function getCustomerLocationCount($customer_id){

    	$sql = "SELECT COUNT(id) AS location_count FROM " . DB_PREFIX . "customer_location WHERE customer_id = '".(int)$customer_id."'";

		$count_location=$this->db->query($sql);

		if ($count_location->row['location_count'] > 0 ) {
    				return $count_location->row['location_count'];
    			}
    			else{
    				return 0;
    			}
    	}
    /**
     * all support number array
     * @return array
     * @author Anurag Jain, 19 Nov 2018
     */
    public function getAllSupportNumberDetails(): array
    {
    	$all_support_number_details[] = array(
    										"name" => "Return Helpline",
    										"description" => "Return Related Issues",
    										"number" => $this->returns_helpline_number,
    										"is_on_whatsapp" => "1",
    										"is_on_call" => "1"
    									);

    	$all_support_number_details[] = array(
    										"name" => "Credit Helpline",
    										"description" => "Credit Related Issues",
    										"number" => $this->credit_helpline_number,
    										"is_on_whatsapp" => "1",
    										"is_on_call" => "1"
    									);

    	$all_support_number_details[] = array(
    										"name" => "Delivery Helpline",
    										"description" => "Delivery Related Issues",
    										"number" => $this->courier_delivery_number_2,
    										"is_on_whatsapp" => "0",
    										"is_on_call" => "1"
    									);
$store_id = $this->config->get('config_store_id');
			if($store_id == INTERNATIONAL_STORE_ID){
				$phone = '+919116134795';
			}else{
				$phone = '8696491521';
			}

    	$all_support_number_details[] = array(
    										"name" => "General Support",
    										"description" => "Other Issues",
    										"number" => $phone,
    										"is_on_whatsapp" => "1",
    										"is_on_call" => "1"
    									);
    	return $all_support_number_details;
    }

    /**
     * returns dropshipper status of a customer
     * @param  int    $customer_id
     * @return boolean
     */
    public function getCustomerDropshipperStatus( int $customer_id ): bool
    {
    	$dropshipper_status = false;

    	$query = "SELECT is_dropshipper
    			FROM ". DB_PREFIX ."customer
    			WHERE customer_id = '" . (int) $customer_id . "'";

    	$dropshipper_result = $this->db->query( $query );

    	if( !empty( $dropshipper_result->num_rows )
    		&& !empty( $dropshipper_result->row['is_dropshipper'] )) {
    		$dropshipper_status = true;
    	}

    	return $dropshipper_status;
    }

    /**
     * function checks if already applied for credit with new form to show credit popup in app
     *
     * @param  int     $customer_id
     * @return boolean
     * @author Anurag Jain, 20 Dec 2018
     */
    public function getCreditApplicationSubmissionStatus( int $customer_id ): bool
    {
    	$credit_status = false;
    	$credit_form_version = 2;

    	$credit_sql = "SELECT
    					customer_id
    				   FROM ". DB_PREFIX ."credit_application
    				   WHERE customer_id = '". (int)$customer_id ."'
    				   	AND version = '". $this->db->escape($credit_form_version) ."'";

    	$credit_result = $this->db->query( $credit_sql );

    	if( $credit_result->num_rows ) {
    		$credit_status = true;
    	}

    	return $credit_status;
    }

    /**
     * gets popup model details
     * @param  array  $data
     * @return array
     * @author Anurag Jain, 20 Dec 2018
     */
    public function getPopupModelData( array $data )
    {
		$credit_application_submission_status = $this->getCreditApplicationSubmissionStatus( (int) $data['customer_id'] );

		$credit_banner_image = "https://cdnimages.net/img/dw=500,dh=300,q=60/app_banner/credit-promotion-popup_new.png";
		$fashcart_banner_image = "https://cdnimages.net/img/dw=720,dh=416,q=90/catalog/mobile_banner.jpg";

		$final_popup_model_data = NULL;

		// details for local app
		$basic_local_data = array();
		$basic_local_data["action_button"]["button_name"] = "Apply Now";
		$basic_local_data["action_button"]["open_view"] = "open_webview";
		$basic_local_data["action_button"]["web_view_url"] = $data['credit_application_url'];
		$basic_local_data["action_button"]["headline"] = "Apply For Credit";
	    $basic_local_data["banner_aspect_ratio"] = "1.73";
	    $basic_local_data["banner_image"] = $credit_banner_image;
	    $basic_local_data["is_only_once"] = "1";
	    $basic_local_data["popup_id"] = "17";
	    $basic_local_data["popup_title"] = "Special Offer!!";
	    $basic_local_data["should_show_whatsapp"] = "1";
	    $basic_local_data["should_show_call"] = "1";
	    $basic_local_data["calling_number"] = $this->credit_helpline_number;
	    $basic_local_data["whatsapp_number"] = $this->credit_helpline_number;
	    $basic_local_data["is_once_in_a_week"] = "1";

	    // details for international app
	    $basic_international_data = array();
		$basic_international_data["action_button"]["button_name"] = "Create Now";
		$basic_international_data["action_button"]["open_view"] = "fashcart_website";
	    $basic_international_data["banner_aspect_ratio"] = "1.73";
	    $basic_international_data["banner_image"] = $fashcart_banner_image;
	    $basic_international_data["is_only_once"] = "1";
	    $basic_international_data["popup_id"] = "18";
	    $basic_international_data["popup_title"] = "Free Website!!";
	    $basic_international_data["should_show_whatsapp"] = "1";
	    $basic_international_data["is_once_in_a_week"] = "1";

		// decide popup model to show
		if( !empty($data['has_website']) && $data['has_website'] == '1') {

			if( $this->config->get('config_store_id') != INTERNATIONAL_STORE_ID
				&& !$credit_application_submission_status ) {

				//$final_popup_model_data = json_decode(json_encode($basic_local_data));
			}

		} else {

			if( $this->config->get('config_store_id') == INTERNATIONAL_STORE_ID ) {
				$final_popup_model_data = json_decode(json_encode($basic_international_data));

			} else {

				if( !$credit_application_submission_status ) {
					//$final_popup_model_data = json_decode(json_encode($basic_local_data));
				}
			}
		}

		return $final_popup_model_data;
    }


    /**
     * Function to get app left navigation drawer details according to national or international store
     * @param void
     * @return array
     * @author Anurag Jain, 15 Nov 2018
     */
    public function getLeftNavigationDrawerList( array $data , int $app_version_code): array
    {
    	$international_store = false;
    	if ( $this->config->get('config_store_id') == INTERNATIONAL_STORE_ID ) {
    		$international_store = true;
    	}

    	$left_navigation_drawer_list = array();

    	$switch_store_object = new stdClass();
    	$switch_store_object->name = "";
    	$switch_store_object->action = new stdClass();
    	$switch_store_object->action->open_view = "switch_store";
    	$left_navigation_drawer_list[] = $switch_store_object;

    	$how_to_use_app_object = new stdClass();
    	$how_to_use_app_object->name = "How To Use App";
    	$how_to_use_app_object->action = new stdClass();
    	$how_to_use_app_object->action->open_view = "how_to_use_app";
    	$how_to_use_app_object->action->hide_on_store_app = "1";
    	$left_navigation_drawer_list[] = $how_to_use_app_object;

    	$fashcart_website_object = new stdClass();
    	$fashcart_website_object->name = "";
    	$fashcart_website_object->action = new stdClass();
    	$fashcart_website_object->action->open_view = "fashcart_website";
    	$left_navigation_drawer_list[] = $fashcart_website_object;

    	if ( !$international_store  && $data['is_dropshipper'] == 0) {

	    	$credit_object = new stdClass();
	    	$credit_object->name = "Apply For Credit";
	    	$credit_object->is_text_bold = true;
	    	$credit_object->action = new stdClass();
	    	if($app_version_code >='111'){
	    		$credit_object->action->open_view = "open_credit_form";
	    		$credit_object->action->web_view_url = '';
	    	}else{
	    		$credit_object->action->open_view = "open_webview";
	    		$credit_object->action->web_view_url = $data['credit_application_url'];
	    	}
	    	$credit_object->action->hide_on_store_app = "1";
	    	$credit_object->is_new = "1";
	    	$credit_object->headline = "Apply For Credit";
    		$left_navigation_drawer_list[] = $credit_object;

            $sale_list_object = new stdClass();
            $sale_list_object->name = "SALE";
            $sale_list_object->is_new = "1";
            $sale_list_object->is_text_bold = true;
            $sale_list_object->should_show_divider = true;
            $sale_list_object->action = new stdClass();
            $sale_list_object->action->offer_key = "@@clearance_sale__search";
            $sale_list_object->action->open_view = "open_search_page";
            $sale_list_object->action->headline = "Sale";
            $left_navigation_drawer_list[] = $sale_list_object;

        }

    	$category_list_object = new stdClass();
    	$category_list_object->name = "Shop By Category";
    	$category_list_object->action = new stdClass();
    	$category_list_object->action->open_view = "category_list";
    	$left_navigation_drawer_list[] = $category_list_object;

    	$rate_us_object = new stdClass();
    	$rate_us_object->name = "Rate Us";
    	$rate_us_object->action = new stdClass();
    	$rate_us_object->action->open_view = "rate_us";
    	$rate_us_object->action->hide_on_store_app = "1";
    	$left_navigation_drawer_list[] = $rate_us_object;

    	$call_our_customer_support_object = new stdClass();
    	$call_our_customer_support_object->name = "Call Our Customer Support";
    	$call_our_customer_support_object->action = new stdClass();
    	$call_our_customer_support_object->action->open_view = "call_our_customer_support";
    	$call_our_customer_support_object->action->hide_on_store_app = "1";
    	$left_navigation_drawer_list[] = $call_our_customer_support_object;

    	$chat_with_us_object = new stdClass();
    	$chat_with_us_object->name = "Chat With Us";
    	$chat_with_us_object->action = new stdClass();
    	$chat_with_us_object->action->open_view = "chat_with_us";
    	$chat_with_us_object->action->hide_on_store_app = "1";
    	$left_navigation_drawer_list[] = $chat_with_us_object;

        $store_locator_object = new stdClass();
        $store_locator_object->name = "Policies";
        $store_locator_object->action = new stdClass();
        $store_locator_object->action->open_view = "open_webview";
        $store_locator_object->action->web_view_url = HTTPS_SERVER . "i/policies?popup=true";
        $store_locator_object->action->hide_on_store_app = "1";
        $store_locator_object->action->headline = "Policies";
        $left_navigation_drawer_list[] = $store_locator_object;

        $privacy_policy_object = new stdClass();
        $privacy_policy_object->name = "Privacy Policy";
        $privacy_policy_object->action = new stdClass();
        $privacy_policy_object->action->open_view = "open_webview";
        $privacy_policy_object->action->web_view_url = HTTPS_SERVER . "i/privacy?popup=true";
        $privacy_policy_object->action->hide_on_store_app = "1";
        $privacy_policy_object->action->headline = "Privacy Policy";
        $left_navigation_drawer_list[] = $privacy_policy_object;

    	$store_locator_object = new stdClass();
    	$store_locator_object->name = "Store Locator";
    	$store_locator_object->action = new stdClass();
    	$store_locator_object->action->open_view = "open_webview";
    	$store_locator_object->action->web_view_url = HTTPS_SERVER . "storelocator?popup=true";
    	$store_locator_object->action->hide_on_store_app = "1";
    	$store_locator_object->action->headline = "Store Locator";
    	$left_navigation_drawer_list[] = $store_locator_object;

	    return $left_navigation_drawer_list;
    }

    /**
     * @param  string $language
     * @param  int    $credit_activation_status
     * @param  int    $customer_dropshipper
     * @return array banners
     * @author Anurag Jain, 13 March 2019
     */
    public function appListPageAllBanners( string $language, int $credit_activation_status, int $customer_dropshipper ): array
    {
    	$list_page_banners = array();

        //Free surface shipping
        $list_page_banners[] = array(
            "banner_id" => "100",
            "background_image_url" => "http://d36qiqd7gl7e25.cloudfront.net/img/free_shipping_product_list.png"
        );

    	// credit form banner
    	if ( $credit_activation_status != 1 ) {
    		if ( $language == "hi" ) {
    			$list_page_banners[] = array(
    										"banner_id" => "101",
	    									"action" => array( "open_view" => "open_credit_form" ),
	    									"background_image_url" => "http://d36qiqd7gl7e25.cloudfront.net/img/banners/credit_app_hindi.png"
	    								);
    		} else {
    			$list_page_banners[] = array(
    										"banner_id" => "102",
	    									"action" => array( "open_view" => "open_credit_form" ),
	    									"background_image_url" => "http://d36qiqd7gl7e25.cloudfront.net/img/banners/list_page_credit_image.png"
	    								);
    		}
    	}

    	// free delivery banner
    	/*$list_page_banners[] = array(
    								"banner_id" => "103",
									"action" => array(
													"open_view" => "open_search_page",
													"search_term" => "aakara",
													"sort_options" => "latest_designs"
												),
									"background_image_url" => "http://d36qiqd7gl7e25.cloudfront.net/img/banners/free_delivery.png"
								);*/

    	// return banner
    	if ( $customer_dropshipper != 1 ) {
    		if ( $language == "hi" ) {
    			$list_page_banners[] = array(
    										"banner_id" => "104",
	    									"action" => array(
													"open_view" => "open_webview",
													"web_view_url" => HTTPS_SERVER . "i/returns-policy"
												),
	    									"background_image_url" => "http://d36qiqd7gl7e25.cloudfront.net/banners/return_app_banner_hindi.png"
	    								);
    		} else {
    			$list_page_banners[] = array(
    										"banner_id" => "105",
	    									"action" => array(
													"open_view" => "open_webview",
													"web_view_url" => HTTPS_SERVER . "i/returns-policy"
												),
	    									"background_image_url" => "http://d36qiqd7gl7e25.cloudfront.net/img/banners/return_app_banner.png"
	    								);
    		}
    	}

    	// discount banner
    	$list_page_banners[] = array(
										"banner_id" => "106",
										"background_image_url" => "http://d36qiqd7gl7e25.cloudfront.net/img/banners/app_order_discount.png"
    								);

    	return $list_page_banners;
    }

    public function checkReturnable($product_id){
        $sql = "SELECT ms.non_returnable,p.non_returnable as product_returnable
                FROM ".DB_PREFIX."ms_seller ms
                  INNER JOIN ".DB_PREFIX."ms_product mp ON (mp.seller_id = ms.seller_id)
                  INNER JOIN ".DB_PREFIX."product p ON (mp.product_id = p.product_id)
                WHERE mp.product_id = '". (int)$product_id . "'";

        $query = $this->db->query($sql);

        if($query->row['non_returnable'] || $query->row['product_returnable'])
        {
        	return 1;
        }
        else
        {
        	return 0;
        }

    }

    /**
     * @param  int  $order_id
     * @return array
     * @author MSA, 03 August 2019
     */
    public function getAllSubOrderStatus(int $order_id){

    	$sql = "SELECT
    				order_id,
    				suborder_id,
    				order_status_id
    			FROM
    				".DB_PREFIX."suborder
    			WHERE
    				order_id = '".(int)$order_id."'

    		";
    	$result = $this->db->query($sql);
    	if($result->num_rows) {
    		return $result->rows;
    	}
    	return array();
    }

    /**
     * [getReferralDetailsOfCustomer
     * referral details includes: referral_code, order_count, commission amount
     * @param  int    $customer_id
     * @return array
     * @author Anurag Jain, 17th July 2019
     */
    public function getReferralDetailsOfCustomer( int $customer_id ): array
    {
    	$this->load->model("affiliate/affiliate");
    	return $this->model_affiliate_affiliate->getReferralDetailsOfCustomer( $customer_id );
    }

    /**
     * @param  int    $customer_id [description]
     * @return int              [description]
	 * @author Anurag Jain, 17 July 2019
     */
    public function createCustomerAffiliate( int $customer_id )
    {
    	$this->load->model("affiliate/affiliate");
	    return $this->model_affiliate_affiliate->createCustomerAffiliate( $customer_id );
    }

    /**
     * @param  int    $customer_id
     * @return int
     * @author Anurag Jain, 27th July 2019
     */
    public function getCustomerAppVersion( int $customer_id ): int
    {
    	$app_version = 0;

    	$sql = "
    			SELECT
    				app_version
    			FROM " . DB_PREFIX ."customer
 	   			WHERE customer_id = '". $customer_id ."'";

 	   	$result = $this->db->query( $sql );

 	   	if ( $result->num_rows ) {
 	   		$app_version = (int) $result->row['app_version'];
 	   	}

 	   	return $app_version;
    }

    /** 
     * @param int    $customer_id [description]
     * @param int    $order_id [description]
     * @param string $img_path [description]
     * @param float  $amount   [description]
     * @author Anurag Jain, 18th July 2019
     */
    public function addTentativeAdvanceFromBankSlip( int $customer_id, int $order_id, string $img_path, float $amount )
    {
    	$add_sql = "
    				INSERT INTO ". DB_PREFIX ."tentative_advance
    				SET
    					order_id = '". $order_id ."',
    					customer_id = '". $customer_id ."',
    					payment_mode = 'cash',
    					amount = '". $amount ."',
    					bank_deposited = '1',
    					bank_deposited_image = '". $img_path ."',
    					date_created = NOW(),
    					transaction_status = 'deposited'
    				";

    	return $add_result = $this->db->query( $add_sql );
    }

    /**
     * @param  int    $customer_id [description]
     * @param  int    $order_id    [description]
     * @return [type]              [description]
     * @author Anurag Jain, 1st Aug 2019
     */
    public function getLatestTentativeAdvanceAmountUploadedByCustomer( int $customer_id, int $order_id )
    {
    	$amount = 0.00;
    	$sql = "
				SELECT
					amount
				FROM ". DB_PREFIX ."tentative_advance
				WHERE customer_id = '" . (int)$customer_id . "'
					AND order_id = '" . (int)$order_id . "'
				ORDER BY date_created DESC
				LIMIT 1 ";
		$result = $this->db->query( $sql );

		if ( $result->num_rows ) {
			$amount = $result->row['amount'];
		}

    	return $amount;
    }

    /**
     * Method to check RBL Pre-Approved user exists or not
     * @param  int  $user_id 		
     * @return bool true/false
     * @author MSA, 10 Sept 2019
     */
    public function checkRBLPreApprovedUserExist( int $user_id ): bool {
        
        $sql = "SELECT 
        			1 
                FROM 
                	" . DB_PREFIX . "customer_credit_preapproved 
                WHERE 
                	retailer_id = '" . (int)$user_id . "'
                ";
        $result = $this->db->query($sql);
        
        if($result->num_rows) {

        	return true;
        }

        return false;
    }

    /**
     * Method to update journey status for a RBL Pre-Approved user
     * @param  int  $user_id 		
     * @return void
     * @author MSA, 10 Sept 2019
     */
    public function updateRBLPreApprovedUserForJourneyStatus( int $user_id ): void {

    	$sql = "
    			UPDATE 
    				" . DB_PREFIX . "customer_credit_preapproved 
    			SET
    				cif_creation_status = 'JourneyCompleted'
    			WHERE
    				retailer_id = '" . (int)$user_id . "'
    		";
    	$this->db->query( $sql );
    }

    public function updateMswipeReferralCodeForCustomer( int $customer_id )
    {
    	$this->load->model("checkout/coupon");
    	$referral_code = "MSWIPE";
    	return $this->model_checkout_coupon->insertReferralCode( $customer_id, $referral_code );
    }

    /**
     * adds lead mobile number if signup from sms or url sent to lead mobile no
     * @param int    $customer_id [description]
     * @param string $lead_mobile [description]
     * @return bool
     * @author Anurag Jain, 15th Oct 2019
     */
    public function addLeadSignupDetail( int $customer_id, string $lead_mobile ): bool
    {
    	if ( empty( $customer_id ) || empty( $lead_mobile )) {
    		return false;
    	}

    	$sql = "INSERT IGNORE INTO lead_signup_details (customer_id, lead_mobile) VALUES ('". $customer_id ."', '". $this->db->escape( $lead_mobile ) ."')";
    	return $this->db->query( $sql );
    }
}
