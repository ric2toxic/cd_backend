<?php
class ModelAccountCustomer extends Model {
	public function addCustomer($data) {
		$referral_code = $this->generatereferralcode();

		$data['mobile_verified'] = $this->checkEmailMobileVerified($data['reg_telephone'] ?? '');
         
        $reg_email = "";
        $gst_number = NULL;

		if(!empty(trim($data['reg_email'] ?? '')))
		{ 
		  $data['email_verified']  = $this->checkEmailMobileVerified(trim($data['reg_email'] ?? ''));
		  $reg_email ="email =  '".$this->db->escape(trim($data['reg_email'] ?? ''))."', ";
	    }

	    if(!empty($data['gst_number']))
		{ 
		  $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $data['gst_number']);
          $gst_number  = trim($gst_number);
	    }

	    if(CONFIG_IS_MOBILE == 1){
		    $signup_from = 'MOBILE_WEB';
        } else {
            $signup_from = 'DESKTOP_WEB';
        }

        if ( empty($data['is_dropshipper']) ) {
        	if( strpos($data['customer_type_id'], '2') !== false ) {
        		$data['is_dropshipper'] = '1';
        	}
        }

        $password_secret = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $insertQuery = "INSERT INTO " . DB_PREFIX . "customer SET
            firstname = '" . $this->db->escape($data['firstname']) . "',
            lastname = '" . $this->db->escape($data['lastname']) . "',
            mobile_country_code = '" . $this->db->escape($data['mobile_country_code']) . "', 
            telephone = '" . $this->db->escape(trim($data['reg_telephone'])) . "', 
            ".$reg_email."
            mobile_verified = '" . $this->db->escape(isset($data['mobile_verified']) ? $data['mobile_verified'] : 0) . "',
            email_verified = '" . $this->db->escape(isset($data['email_verified']) ? $data['email_verified'] : 0) . "',
            password = '" . $this->db->escape($password_secret) . "', 
            password_mode = 'new', 
            ip = '" . $this->db->escape($this->request->getIpAddress) . "', 
            referral_code = '" . $this->db->escape($referral_code) ."', 
            is_dropshipper = '" . $this->db->escape($data['is_dropshipper']) . "', 
            customer_type_id = '" . $this->db->escape($data['customer_type_id']) . "', 
            ws_access_token = '" . (isset($data['ws_access_token']) ? $this->db->escape($data['ws_access_token']) : '') . "',
            customer_access_token = '" . (isset($data['customer_access_token']) ? $this->db->escape($data['customer_access_token']) : '') . "',
            date_added = NOW() , signup_from = '".$this->db->escape($signup_from)."'";
            

		 $this->db->query($insertQuery);


		$customer_id = $this->db->getLastId();
		// update master id when new customer created account
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET master_id = '" .(int)$customer_id . "' WHERE customer_id = '" .(int)$customer_id . "'");
		
		/*** Update GST Number ***/
		if(!empty($gst_number)) {
			$gstObject = new GST($this->registry);
			$gstObject->updateGstNumber( (int) $customer_id, (int) $customer_id, "", $gst_number );
		}
		
		$this->load->language('mail/customer');
		$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

		$message = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";

		$message .= $this->language->get('text_login') . "\n";
        
        if (empty($data['is_seller'])) {
            $message .= $this->url->link('account/login', '', 'SSL') . "\n\n";
            $message .= $this->language->get('text_services') . "\n\n";
            $message .= $this->language->get('text_thanks') . "\n";
            $message .= html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';

            //$mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_email'), 'WholesaleBox');
            $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');

            $mail->addAddress($data['reg_email'], $data['firstname']);
            $mail->Subject =  $subject;
            //$mail->Body = $html;
            $mail->msgHTML($message);

            $mail->send();

        }//do not send email when seller sign up
        
		return $customer_id;
	}

	public function editCustomer($data) {

		if(isset($data['reg_email'])){
			$email = $data['reg_email'];
		}else{
			$email = $data['email'];
		}

		if(isset($data['reg_telephone'])){
			$telephone = $data['reg_telephone'];
		}else{
			$telephone = $data['telephone'];
		}

        if($this->customer->getTelephone() != $telephone)
        {
        	$mobile_verified = 0;
        }
        else
        {
        	$mobile_verified = $this->customer->getMobileVerified();
        }

        if($this->customer->getEmail() != $email)
        {
        	$email_verified = 0;
        }
        else
        {
        	$email_verified = $this->customer->getEmailVerified();
        }

		$customer_id = $this->customer->getId();
       
         $updateGst = "";
         if(isset($data['gst_number']) && !empty(trim($data['gst_number'])))
         {
         	//$updateGst = ", gst_number = '" . $this->db->escape($data['gst_number']) . "'";
			/*** using customer library function as it handles same masterids gst updation too; Anurag Jain ***/
			$log_changes = true;
			$this->customer->setGSTNumber($data['gst_number'], $log_changes);
         }

		$this->db->query("UPDATE " . DB_PREFIX . "customer 
		SET firstname = '" . $this->db->escape($data['firstname']) . "', 
		lastname = '" . $this->db->escape($data['lastname']) . "', 
		email = '" . $this->db->escape($email) . "', 
		telephone = '" . $this->db->escape($telephone) . "', 
		mobile_verified = '" . (int)$mobile_verified . "', 
        email_verified = '" . (int)$email_verified . "' 
        ".$updateGst." 
		WHERE customer_id = '" . (int)$customer_id . "'");
	}


	public function updateCustomerToSeller($customer_id, $data = array()) {
	    if(!empty($data))
	  {
		$password_secret = password_hash($data['password'], PASSWORD_DEFAULT);
		$this->db->query("UPDATE " . DB_PREFIX . "customer 
		                  SET telephone = '" . $this->db->escape($data['reg_telephone']) . "', 
		                      password = '" . $this->db->escape($password_secret) . "', 
		                      password_mode = 'new' WHERE customer_id = '" . (int)$customer_id . "'");
	  }
	}

	// update editPassword() by vikas
	public function editPassword($customer_id, $password) {

        $result = false;
        $password_secret = password_hash($password, PASSWORD_DEFAULT);
		
	    $q = "UPDATE " . DB_PREFIX . "customer SET password = '" . $this->db->escape($password_secret) . "', 
	          password_mode = 'new' WHERE customer_id =" .(int) $customer_id ;

		$result = $this->db->query($q);
        return $result;
	}
	/**************************************/

    public function update_account_mobile_email($data)
    {
       if($this->customer->getId() == $data['customer_id'])
       {
    	
          $lead_data = array();
           
    	 if(isset($data['telephone']) && !empty($data['telephone']))
         {
            $previous_telephone = $this->customer->getTelephone();
            
    	   $this->db->query("UPDATE " . DB_PREFIX . "customer 
    	                     SET mobile_country_code = '" . (int)$data['country_code'] . "', 
    	                          telephone = '" . (int)$data['telephone'] . "', mobile_verified = '1' 
    	                     WHERE customer_id = '" . (int)$this->customer->getId() . "'");

               $lead_data['alternate_number'] = $data['telephone'];
            $this->load->model('lead/lead');
            $this->model_lead_lead->addAlternateContactInLead($lead_data,$previous_telephone ,'Mobile no changed in website', $this->customer->getId());
            
    	 }
    	 
    	 if(isset($data['email']) && !empty($data['email']))
    	 {
    	 	$this->db->query("UPDATE " . DB_PREFIX . "customer 
    	 	                  SET email = '" .$this->db->escape(trim($data['email'])) . "', 
    	 	                  email_verified = '1' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
    	 }  
         
       }	

    }

	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {

        if ($email != 'noemailwsb@gmail.com') {

            $query = $this->db->query("SELECT customer_id, email, 	telephone, mobile_country_code, firstname, lastname 
                                       FROM " . DB_PREFIX . "customer 
                                       WHERE email = '" . $this->db->escape($email) . "' ");

            return $query->row;

        } else {
			return 0;
		}
	}

	public function getCustomerByEmailOrMobile($email) {
        if ($email != 'noemailwsb@gmail.com') {

           if(is_numeric($email))
	       {
             $check_email_or_mobile = "telephone = '".$this->db->escape(trim($email))."'";
	       }
	       else
	       {
	       	$check_email_or_mobile = "email = '".$this->db->escape(trim($email))."'";
	       }

            $query = $this->db->query("SELECT customer_id, email, 	telephone, mobile_country_code 
                                       FROM " . DB_PREFIX . "customer WHERE ".$check_email_or_mobile." ");

            return $query->row;

        } else {
			return 0;
		}
	}

	public function getCustomerByToken($token) {
        if (empty($token)) return array();
		$query = $this->db->query("SELECT customer_id, email, telephone, firstname, lastname FROM " . DB_PREFIX . "customer 
                                   WHERE customer_access_token = '" . $this->db->escape($token) . "'");
		return $query->row;
	}


	public function getCustomerByTokenAndId($token, $customer_id) {
        $result = array();
		if($token != '')
		{
          $query = $this->db->query("SELECT oc.customer_id, oc.customer_access_token, ms.seller_id, ms.nickname 
                                     FROM " . DB_PREFIX . "customer oc 
                                     LEFT JOIN " . DB_PREFIX . "ms_seller ms on oc.customer_id = ms.seller_id  
                                     WHERE oc.customer_id = '".(int)$customer_id."'");

           if($query->row['customer_access_token'] == $token)
           {
             $result = $query->row;
           }
           
		}
		
		return $result;
	}	

	public function getCustomerByTokenAndIdFromAPP($token, $customer_id) {
       $result = array();
        if($token != '')
        {
         $query = $this->db->query("SELECT oc.customer_id, oc.ws_access_token, ms.seller_id, ms.nickname 
                                    FROM " . DB_PREFIX . "customer oc 
                                    LEFT JOIN " . DB_PREFIX . "ms_seller ms on oc.customer_id = ms.seller_id  
                                    WHERE oc.customer_id = '".(int)$customer_id."'");

          if($query->row['ws_access_token'] == $token)
           {
             $result = $query->row;
           }
        }
        
        return $result;
    }
    
    public function getCustomerByOtp($otp) {
        $query = $this->db->query("SELECT customer_id, email, telephone FROM " . DB_PREFIX . "customer WHERE otp = '" . $this->db->escape($otp) . "'");

        return $query->row;
    }
    public function getCustomerByIdOtp($customerId,$otp) {
        $query = $this->db->query("SELECT customer_id, email, telephone 
                                   FROM " . DB_PREFIX . "customer 
                                   WHERE customer_id = '".$this->db->escape($customerId)."' AND otp = '" . $this->db->escape($otp) . "'");
        return $query->row;
    }

    public function getCustomerByMobile($mobile) {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer 
        WHERE telephone = '" . $this->db->escape($mobile) . "'");

        return $query->row;
    }

	/******************
	* getTotalCustomersByEmail() is updated by vikas
	***/
	public function getTotalCustomersByEmail($email) {
		if(is_numeric($email)){
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape($email) . "'");
		}else{
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
		}

		return $query->row['total'];
	}
	/****************************************************/
    /**
     * total customers (generally required for home page)
     * @author Madhur (updated on 7 Aug 2019)
     * Uses information schema tables to get an approx count, instead of unnecesary exact count and avoid full index/table scan
     */
	public function getTotalCustomers(){
        $sql = "SELECT table_rows FROM information_schema.tables 
                WHERE table_schema = 'wholesalebox' 
                  AND table_name = '" . DB_PREFIX . "customer'";
        $query = $this->db->query($sql);
        return (int)($query->row['table_rows'] ?? 0);
    }

	public function addLoginAttempt($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_login 
		                           WHERE email = '" . $this->db->escape((string)$email) . "' 
		                             AND ip = '" . $this->db->escape($this->request->getIpAddress) . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_login 
			                  SET email = '" . $this->db->escape((string)$email) . "', 
			                         ip = '" . $this->db->escape($this->request->getIpAddress) . "', 
			                         total = 1");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "customer_login 
			                  SET total = (total + 1) 
			                  WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'");
		}
	}
	public function addMobileLoginAttempt($mobile) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_login 
		                           WHERE mobile = '" . $this->db->escape($mobile) . "' 
		                             AND ip = '" . $this->db->escape($this->request->getIpAddress) . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_login 
			                          SET mobile = '" . $this->db->escape($mobile) . "', 
			                                  ip = '" . $this->db->escape($this->request->getIpAddress) . "', 
			                                  total_mobile = 1");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "customer_login 
			                  SET total_mobile = (total_mobile + 1) 
			                  WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'");
		}
	}

	public function getLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape($email) . "'");
		return $query->row;
	}
	public function getMobileLoginAttempts($mobile) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE mobile = '" . $this->db->escape($mobile) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape($email) . "'");
	}
	public function deleteMobileLoginAttempts($mobile) {
		if (isset($mobile) && !is_null($mobile)) {
			$user = $this->db->query("SELECT customer_login_id 
                                      FROM ".DB_PREFIX."customer_login 
                                      WHERE mobile = '". $this->db->escape($mobile) . "'");
			if ($user->num_rows) {
				$customer_login_id = $user->row['customer_login_id'];
				$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` 
				                  WHERE customer_login_id = '" . $this->db->escape($customer_login_id) . "'");
			}
		} else {
			return true;
		}
	}

	public function getTotalCustomersByTelephone($telephone) {
		
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE LOWER(telephone) = '" . $this->db->escape(utf8_strtolower($telephone)) . "'");
		$q = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer 
		      WHERE telephone = '" . $this->db->escape($telephone) . "'";
		$query = $this->db->query($q);

		return $query->row['total'];
	}

	public function getTotalCustomersByGst($gst_number) {
		
		$q = "SELECT COUNT(*) AS total 
              FROM " . DB_PREFIX . "customer 
              WHERE gst_number = '" . $this->db->escape($gst_number) . "' ";
		$query = $this->db->query($q);

		return $query->row['total'];
	}

    public function getCustomerUsingGst($gst_number) {
        $q = "SELECT customer_id, telephone, email, master_id 
              FROM " . DB_PREFIX . "customer 
              WHERE gst_number = '" . $this->db->escape($gst_number) . "' LIMIT 1";
        $query = $this->db->query($q);
        if($query->num_rows){
            return $query->row;
        }
        else{
            return array();
        }
    }


	public function getTotalCustomersByEmailToValidate($email)
	{
		if ($email != 'noemailwsb@gmail.com'){

			$q = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer 
			       WHERE email = '" . $this->db->escape($email) . "' ";
			$query = $this->db->query($q);
			return (int)($query->row['total'] ?? 0);
		}else{
			return 0;
		}

	}

	/**
	 * This function runs query for sign up step1
	 * Please start the mail script (Only for Ravindra Singh)
	 */

	public function addstep1($data){


		$password_secret = password_hash($data['password'], PASSWORD_DEFAULT);

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET 
		firstname = '" . $this->db->escape($data['firstname']) . "', 
		lastname = '" . $this->db->escape($data['lastname']) . "', 
		email = '" . $this->db->escape($data['reg_email']) . "', 
		telephone = '" . $this->db->escape($data['reg_telephone']) ."', 
		password = '" . $this->db->escape($password_secret) . "', 
		password_mode = 'new', 
		ip = '" . $this->db->escape($this->request->getIpAddress) . "',
		date_added = NOW()");

		$customer_id = $this->db->getLastId();
		// update master id when new customer created account
		$this->db->query("UPDATE " . DB_PREFIX . "customer 
		SET master_id = '" .(int)$customer_id . "' 
		WHERE customer_id = '" .(int)$customer_id . "'");

		return $customer_id;

	}

	/*
	 * get user name by mobile by vikas
	 *
	 */
	function getUserNameByMobileNumber($email){
		if(is_numeric($email)){
			$query = $this->db->query("SELECT firstname  FROM " . DB_PREFIX . "customer 
			WHERE telephone = '" . $this->db->escape($email) . "'");


			return $query->row['firstname'];
		}
	}

	public function generatereferralcode()
	{
        $length = 8;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $referral_code = '';
        for ($i = 0; $i < $length; $i++) {
            $referral_code .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $referral_code;
	}

	public function getisdropshipper($customer_id) {
	   	$query = $this->db->query("SELECT is_dropshipper FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");
		return (int) $query->row['is_dropshipper'];
	}

	public function updateDropshipper($customer_id) {
	   	$query = $this->db->query("UPDATE " . DB_PREFIX . "customer SET is_dropshipper = 2 WHERE customer_id =" . (int)$customer_id);
	}

	public function getStaffMemberInfo($token) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sales_staff 
		                           WHERE token = '" . $this->db->escape(trim($token)) . "' AND active_status = 1");

        if ($query->num_rows > 0)
            return $query->row;
        else
            return false;
	}

    public function getSalesPersonName($staff_id) {

        $staff_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sales_staff WHERE staff_id = '". (int)$staff_id . "'");

        if ($staff_query->rows) {
            return $staff_query->row['name'];
        } else {
            return false;
        }
    }

	public function setPreferences($customer_id, $product_ids){

		$product_ids 	= array_unique(array_map('intval', array_column($product_ids, 'product_id')));

        $category_id = array();
		if ( !empty($product_ids) ) {
            $product_id = implode(',', $product_ids);
            $sql = "SELECT distinct category_id 
                    FROM " . DB_PREFIX . "product_to_category 
                    WHERE product_id IN (" . $product_id . ")";
            $category_ids = $this->db->query($sql)->rows;
            $category_id = array_column($category_ids, 'category_id');
        }

		$sql = "SELECT category_id 
                FROM ". DB_PREFIX ."customer_preference 
                WHERE customer_id = " . (int)$customer_id;
        $query  = $this->db->query($sql)->rows;
        $preferences_category_id = array_column($query, 'category_id');

        foreach ($category_id as $value) {
            if(in_array($value, $preferences_category_id)){
            }else{
                $sql  = "INSERT IGNORE INTO ". DB_PREFIX ."customer_preference 
                         SET customer_id = " . (int)$customer_id . "
                           , category_id=  " . (int)$value . "
                           , filter_id = ''
                           , min_price = 0
                           , max_price = 0";
                $query = $this->db->query($sql);
            }
        }
	}
	public function getCustomersRegisteredBeforeOneHour(){

		$sql1 = "SELECT c.customer_id,
                        concat(c.firstname, ' ', c.lastname ) as name,
                        c.is_dropshipper,
                        c.telephone, 
                        c.ws_access_token, 
                        c.date_added, 
                        c.referral_code,
                        c.ip, 
                        a.city as address_city,
                        c.gst_number as gst_number,
                        a.address_1 as address_address, 
                        a.country_id as address_country_id, 
                        a.zone_id as address_zone_id,
                        a.postcode as address_zip 
                 FROM oc_customer c 
                 LEFT JOIN oc_address a ON (c.customer_id = a.customer_id) 
                 LEFT JOIN oc_ms_seller ms ON (c.customer_id = ms.seller_id)
                 WHERE c.date_added >= '2017-04-01 00:00:00' 
                   AND c.telephone != '' 
                   AND c.lead_inserted = 0 
                   AND ms.seller_id IS NULL 
                 LIMIT 30 ";

		return $this->db->query($sql1)->rows;
	}

	public function leadInserted($customer_id)
	{
		$sql = "UPDATE oc_customer SET lead_inserted = 1 WHERE customer_id = " . (int)$customer_id;
		$this->db->query($sql);
		return true;
	}


	public function addOTP($data)
	 {
	 	if(is_numeric($data['reg_telephone']))
	       {
             $check_email_or_mobile = "telephone = '".$this->db->escape(trim($data['reg_telephone']))."'";
	       }
	       else
	       {
	       	$check_email_or_mobile = "email = '".$this->db->escape(trim($data['reg_telephone']))."'";
	       }

	 	$rows = $this->db->query("SELECT otp, signup_id from `" . DB_PREFIX . "customer_signups` where ".$check_email_or_mobile." and otp_verified = 0 and otp_page = '" . $this->db->escape($data['otp_page']) . "'")->row;
        if(count($rows) == 0)
        {
          if(is_numeric($data['reg_telephone'])){
		  $query = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_signups` set country_code = '" . $this->db->escape($data['country_code']) . "', telephone = '" . $this->db->escape(trim($data['reg_telephone'])) . "', session_id = '" . $this->db->escape($data['session_id']) . "', ip = '" . $this->db->escape($data['ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', otp = '" . $this->db->escape($data['otp']) . "', date_added = '".date("Y-m-d h:i:s")."', date_modified = '".date("Y-m-d h:i:s")."', otp_page = '" . $this->db->escape($data['otp_page']) . "', customer_id = '" . $this->db->escape($data['customer_id']) . "', otp_verified=0");
		  }
		  else
		  {
		  	$query = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_signups` set email = '" . $this->db->escape(trim($data['reg_telephone'])) . "', session_id = '" . $this->db->escape($data['session_id']) . "', ip = '" . $this->db->escape($data['ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', otp = '" . $this->db->escape($data['otp']) . "', date_added = '".date("Y-m-d h:i:s")."', date_modified = '".date("Y-m-d h:i:s")."', otp_page = '" . $this->db->escape($data['otp_page']) . "', customer_id = '" . $this->db->escape($data['customer_id']) . "', otp_verified=0");
		  }
		   $otp = 0;
		}
		else
		{
            $query = $this->db->query("Update `" . DB_PREFIX . "customer_signups` set date_modified = '".date("Y-m-d h:i:s")."' where signup_id = '". $rows['signup_id'] ."'");
		    $otp = $rows['otp'];
		}
		return $otp;
	 }

	public function checkMasterOTP($data)
	 {
	 	$rows = $this->db->query("SELECT staff_id from `" . DB_PREFIX . "sales_staff` 
	 	where  active_status = '1' and master_otp = '" . (int)$data['otp'] . "'")->row;

       if(count($rows) > 0)
        {
        	if(!empty($data['customer_id']))
	       {
             $check_email_or_mobile = "customer_id = '". (int)$data['customer_id']."'";
	       }
           else if(is_numeric($data['reg_telephone']))
	       {
             $check_email_or_mobile = "telephone = '".$this->db->escape(trim($data['reg_telephone']))."'";
	       }
	       else
	       {
	       	$check_email_or_mobile = "email = '".$this->db->escape(trim($data['reg_telephone']))."'";
	       }

           $staff_id = $rows['staff_id'];
           $rows = $this->db->query("SELECT signup_id, otp_page, customer_id, country_code, email, telephone 
                                     from `" . DB_PREFIX . "customer_signups` 
                                     where ".$check_email_or_mobile." 
                                       and otp_page = '" . $this->db->escape($data['otp_page']) . "' 
                                       and otp_verified = 0")->row;
           if(count($rows) > 0) { $rows['staff_id'] = $staff_id; }
        }
	 	return $rows;
	 }

	public function checkOTP($data, $override=false)
	 {
	 	if($override)
	 	{
          $rows = $this->db->query("SELECT signup_id, otp_page, email, telephone, customer_id, country_code  
                                      from `" . DB_PREFIX . "customer_signups` 
                                      where  otp = '" .(int)$data. "' and otp_verified = 0 
                                      order by date_modified desc")->row;
	 	}
	 	else
	 	{
           if(!empty($data['customer_id']))
	       {
             $check_email_or_mobile = "customer_id = '".$this->db->escape(trim($data['customer_id']))."'";
	       }
	       else if(is_numeric($data['reg_telephone']))
	       {
             $check_email_or_mobile = "telephone = '".$this->db->escape(trim($data['reg_telephone']))."'";
	       }
	       else
	       {
	       	$check_email_or_mobile = "email = '".$this->db->escape(trim($data['reg_telephone']))."'";
	       }	

          $rows = $this->db->query("SELECT signup_id, otp_page, email, telephone, customer_id, country_code 
                                    from `" . DB_PREFIX . "customer_signups` 
                                    where  ".$check_email_or_mobile." 
                                      and otp = '" .(int)$data['otp']. "' 
                                      and otp_page = '" . $this->db->escape($data['otp_page']) . "' 
                                      and otp_verified = 0 order by date_modified desc")->row;

	 	}
	 	
	 	return $rows;
	 }

	public function checkUserIsVerify($telephone)
	 {
	  if(is_numeric($telephone)){
	 	$rows = $this->db->query("SELECT * from `" . DB_PREFIX . "customer_signups` 
	 	where  telephone = '" . $this->db->escape(trim($telephone)) . "' and otp_verified = 1")->row;
	   }
	   else
	   {
	   	$rows = $this->db->query("SELECT * from `" . DB_PREFIX . "customer_signups` 
	   	where  email = '" . $this->db->escape(trim($telephone)) . "' and otp_verified = 1")->row;
	   }
	 	return $rows;
	 }

	public function verifyOTP($data)
	 {
	 	 $this->db->query("UPDATE `" . DB_PREFIX . "customer_signups` 
	 	 set otp_verified = 1, 
	 	 staff_id = '" . $this->db->escape(isset($data['staff_id']) ? $data['staff_id'] : 0) . "' 
	 	 where signup_id = '" . $this->db->escape($data['signup_id']) . "'");
	 }

	public function checkEmailMobileVerified($mobile)
	 {

	 	if(is_numeric($mobile)){
	 	  $query = $this->db->query("SELECT signup_id from `" . DB_PREFIX . "customer_signups` 
	 	  where telephone = '".$this->db->escape(trim($mobile))."' and otp_verified = '1'");
	    }
	    else
	    {
	      $query = $this->db->query("SELECT signup_id from `" . DB_PREFIX . "customer_signups` 
	      where email = '" . $this->db->escape(trim($mobile)) . "'  and otp_verified = '1'");
	    }
	 	 

	 	 if($query->num_rows == 0)
	 	 {
	 	 	return $query->num_rows;
	 	 }
	 	 else
	 	 {
            return 1;
	 	 }
	 }

	public function getCustomersType()
	{
        $sql1 = "SELECT customer_type_id, type FROM ".DB_PREFIX."customer_type where type NOT LIKE '%online reseller%'";
		return $this->db->query($sql1)->rows;
	}


	public function getCustomersDropshipperType()
	{
		$result = array();
		if($this->config->get('config_store_id') != INTERNATIONAL_STORE_ID)
         {
          
           $sql1 = "SELECT * FROM ".DB_PREFIX."customer_type where type LIKE '%online reseller%'";
          $result = $this->db->query($sql1)->rows;
         }	

		return $result;
	}

    public function getCustomerDetails($customer_id, $fields = array() ){
        $sql  = "SELECT ". (empty($fields) ? " * " : ( is_array($fields) ? implode(",",$fields) : $fields  )) . " ";
        $sql .= "FROM ".DB_PREFIX."customer ";
        $sql .= "WHERE customer_id = " . (int)$customer_id;
        $result = $this->db->query($sql);
        return $result->row;
    }
    
    public function checkCustomerBankDetails($data, $customer_id)
    {
       $sql = "SELECT  customer_id from ".DB_PREFIX."customer 
               WHERE customer_id != '". (int)$customer_id ."' 
                 AND bank_ac_number = '".(int)$data['account_number']."' 
                 and ifsc_code= '".$this->db->escape($data['ifsc_code'])."'";
      
        $result = $this->db->query($sql);
        return $result->num_rows; 
        	
    }

    public function updateCustomerDetails($data,$customer_id){
        $sql = "UPDATE ".DB_PREFIX."customer SET ";
        $fields = array();
        foreach ($data as $field => $value) {
            $fields[] = $this->db->escape($field) . " = '" . $this->db->escape($value) .  "'";
        }
        $sql .= implode( ",", $fields);
        $sql .= " WHERE customer_id = ". (int)$customer_id;
        return $this->db->query($sql);
    }
    public function getCustomerBank_details($customer_id){
        $sql = "SELECT bank_ac_holder_name, bank_ac_number, ifsc_code 
                FROM " .DB_PREFIX ."customer 
                WHERE customer_id = " . (int)$customer_id;
        $result = $this->db->query($sql);
        return $result->row;
    }

    public function editCustomerBankDetails($data)
    {
        if(!empty($data)){
            $sql = "UPDATE ".DB_PREFIX."customer SET
                    bank_ac_holder_name = '".$this->db->escape($data['bank_ac_holder_name'])."',
                    bank_ac_number = '".$this->db->escape($data['bank_ac_number'])."',
                    ifsc_code = '".$this->db->escape($data['ifsc_code'])."',
                    upi_vpa = '".$this->db->escape($data['customer_vpa'])."'
                    WHERE customer_id = ". (int)$data['customer_id'];
            $this->db->query($sql);
        }
    }


	public function generate_access_token(){
		$length = 16;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[random_int(0, $charactersLength - 1)];
		}
		return $randomString;
	}


	public function addCustomerToken($customer_id)
	 {
        $data = array();
		$data['customer_id']  =  $customer_id;
		$data['token']	      =  $this->generate_access_token();
		$data['access_type']  = (CONFIG_IS_MOBILE) ? 'web-mobile' : 'web';
		$data['device_info']['session_id']   = session_id();
	  	$data['device_info']['ip']           = $this->request->getIpAddress;
	    $data['device_info']['user_agent']   = $_SERVER['HTTP_USER_AGENT'];

	 	$query = $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_tokens` 
	 	                           set customer_id = '" . (int) $data['customer_id'] . "', 
	 	                               wsb_access_token = '" . $this->db->escape($data['token']) . "', 
	 	                               access_type = '" . $this->db->escape($data['access_type']) . "', 
	 	                               login_date = NOW(), 
	 	                               device_info = '" . $this->db->escape( serialize($data['device_info'])) . "'");


        setcookie('wat', $data['token'], time() + (86400 * 30), "/");
	 	return $data['token'];

	 }


	public function getCustomerToken($customer_id)
	 {
	 	$query = $this->db->query("SELECT  * from `" . DB_PREFIX . "customer_tokens` where customer_id = '" . (int) $customer_id . "'");
	 	return $query->row['wsb_access_token'];

	 }

	public function removeCustomerToken($customer_id)
	 {
	 	$token = $_COOKIE['wat'];
	 	$query = $this->db->query("DELETE from `" . DB_PREFIX . "customer_tokens` 
	 	                           where customer_id = '" . (int) $customer_id . "' 
	 	                             and wsb_access_token = '" . $this->db->escape($token) . "'");
	 }
	/**
	* Check this customer has a master_id
	* @param: customer id
	* @author: kalyan 20th Nov 2017
	*/
	public function checkCustomerHasMasterId($customer_id){

		$sql = "SELECT master_id 
                FROM " . DB_PREFIX . "customer 
                WHERE customer_id = " . (int)$customer_id;
		$query = $this->db->query($sql);
		return (int)($query->row['master_id'] ?? 0);
	}

    /***
	 * Get customer VPA
    **/
	public function getCustomerVPA($customer_id) {
		$sql = "SELECT upi_vpa
				FROM ".DB_PREFIX."customer
				WHERE customer_id = " . (int)$customer_id;
		$query = $this->db->query($sql);
		return $query->row['upi_vpa'] ?? false;
	}

	public function getCustomerType($customer_id)
	{
		 $get_sql = "SELECT customer_type_id
				     FROM ".DB_PREFIX."customer
				     WHERE customer_id = ". (int)$customer_id;

		 $query = $this->db->query($get_sql);	
		 
		 return (int)($query->row['customer_type_id'] ?? 0);
	}

	public function setSellerToken($customer_id, $token, $customer_mobile)
	{
        setcookie('customer_id', $customer_id, time() + (86400 * 30), "/");
		setcookie('customer_access_token', $token, time() + (86400 * 30), "/");
		setcookie('customer_mobile', $customer_mobile, time() + (86400 * 30), "/");
		setcookie('register_user', 1, time() + (86400 * 30), "/");

		$this->db->query("UPDATE " . DB_PREFIX . "customer 
		                  SET customer_access_token = '" . $this->db->escape($token) . "' 
		                  WHERE customer_id = " . (int)$customer_id);
	}

	/**
	* Check this customer has a membership
	* @param: master id
	* @author: mahaveer 20th Nov 2019
	*/
	public function checkCustomerHasMembership($master_id) : array {
         
        $return_result = array();
		
		$sql = "SELECT membership_id, expiry_date 
                FROM " . DB_PREFIX . "master_customer_membership 
                WHERE master_id = " . (int)$master_id . " 
                  AND status = 1
                  AND expiry_date > '". date("Y-m-d 00:00:00") ."'
                LIMIT 1";
		
		$query = $this->db->query($sql);
		if ($query->num_rows > 0)
		{
          $return_result['membership'] = 1;
          $return_result['membership_id'] = $query->row['membership_id'];
          $return_result['expiry_date'] = $query->row['expiry_date'];
		}
		else
		{
		  $return_result['membership'] = 0;
          $return_result['membership_id'] = 0;
          $return_result['expiry_date'] = '';	
		}
		
		return $return_result; 
	}

	public function checkIfBankAccountExists($data) : int {
		$sql = "SELECT customer_id 
                FROM " . DB_PREFIX . "customer 
                WHERE bank_ac_number= '" . $this->db->escape(trim($data['bank_ac_number'] ?? '')) . "' 
                  AND ifsc_code = '" . $this->db->escape(trim($data['ifsc_code'] ?? '')) . "' 
                  AND customer_id != " . (int)($data['customer_id'] ?? 0) . " 
                LIMIT 1";

		$query = $this->db->query($sql);
        if ($query->num_rows > 0){
            return 0;
        }
        return 1;
	}

	public function checkIfBankAccountIsBlock(array $data) : int {
		$sql = "SELECT bank_ac_number 
                FROM " . DB_PREFIX . "wsb_blocked_bank_account 
                WHERE bank_ac_number= '" . $this->db->escape(trim($data['bank_ac_number'] ?? '')) . "' 
                  AND ifsc_code = '" . $this->db->escape(trim($data['ifsc_code'] ?? '')) . "' 
                LIMIT 1";
		$query = $this->db->query($sql);
		return (int)($query->num_rows);
	}
        
    public function getReferByCustomerId( string $refferal_code ): int
    {
        $sql = "SELECT
                	oa.customer_id  
                FROM ". DB_PREFIX ."affiliate oa
                WHERE
                	oa.code = '". $this->db->escape( $refferal_code ) ."'";
       
        $query = $this->db->query( $sql );
        return (int)($query->row['customer_id'] ?? 0);
    }

}
