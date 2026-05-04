<?php
// Strict mode
declare( strict_types = 1 );

use Cache\Mem;

class GST {
    
    protected static $gst_regex = '/^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([Z]){1}([a-zA-Z0-9]){1}?$/';
    protected static $checksum_weight_characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected static $checksum_factor_even_place = 1;
    protected static $checksum_factor_odd_place = 2;
    protected $gst_update_crm_api_url = WSBOX_CRM_URL."crmapi/leads/getCustomerGstFromWeb";
    public $log_changes;
    private $_duplicate_mail_delay_time = 60*60; // 1 hr
    
    public function __construct($registry) {
        if(method_exists($registry,'get')) {
            $this->registry = $registry;
            $this->session = $registry->get('session');
            $this->url = $registry->get('url');
            $this->db = $registry->get('db');
            $this->request = $registry->get('request');
    		$this->config = $registry->get('config');
            if($registry->get('customer')) {
                $this->user = $registry->get('customer');
            } else {
                $this->user = $registry->get('user');
            }
        } else {
            $this->registry = $registry;
            $this->db = $registry->db;
            $this->session = $registry->session;
            $this->url = $registry->url;
            $this->db = $registry->db;
            $this->request = $registry->request;
    		$this->config = $registry->config;
            if($registry->customer) {
                $this->user = $registry->customer;
            } else {
                $this->user = $registry->user;
            }
        }
        $this->log_changes = false;
    }
    
    /**
     * validates gst number including regex check, checksum check and duplicacy check
     * @param: $gst_number: (string)
     *         $customer_id: (int)
     *         $seller_duplicacy_check: (bool)
     *         $current_customer: (array) - current customer info; used when new customer tries to used duplicate GST (for mailing purpose)
     * @return: $result: success or error specifications
     * @author: Anurag Jain (Aug 2018)
     **/
    public function validateGSTNumber(string $gst_number,
                                      int $customer_id = 0,
                                      bool $seller_duplicacy_check = true,
                                      array $current_customer = array()): array {
        $result = array();

        $gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);;
        $gst_number = trim($gst_number);
        
        /*** GST format check ***/
        $result = self::validateGSTNumberFormat($gst_number);
        
        if(!empty($result['message']) && $result['message'] == 'valid') {
            
            /*** GST duplicacy check ***/
            if(empty($current_customer)) {
                $current_customer = $this->getCustomerByCustomerId($customer_id);
            }
            $master_id = !empty($current_customer['master_id']) ? (int) $current_customer['master_id'] : 0;
            $duplicate_gst_number_customer = $this->getCustomerByDuplicateGSTNumber($gst_number, $master_id);
            
            if($duplicate_gst_number_customer['total'] > 0) {
                $duplicate_gst_number_entity = 'customer';
                $result = array('result' => false,
                                'message' => 'error_duplicate',
                                'duplicate_gst_number_details' => $duplicate_gst_number_customer,
                                'duplicate_gst_number_entity' => $duplicate_gst_number_entity);
                
                /*** Mail to authorities for GST duplicacy encounter ***/
                if( $this->shouldSendGSTDuplicacyMail( array('entity_id' => $customer_id, 'gst_number' => $gst_number ))) {

                    $mail_data = array('current_customer' => $current_customer,
                                       'duplicate_customer' => $duplicate_gst_number_customer, 
                                       'duplicate_gst_number_entity' => $duplicate_gst_number_entity);
                    $this->sendGSTNumberDuplicacyMail($mail_data);
                }
			}
        }
        return $result;
    }
    
    /**
     * validates gst number format (includes regex and checksum check)
     * @param: $gst_number: (string)
     * @return: $result: (array) success or error specifications
     * @author: Anurag Jain (Aug 2018)
     **/
    public static function validateGSTNumberFormat(string $gst_number): array {
        
        $result = array('result' => true, 'message' => 'valid');
        
        if(!preg_match(self::$gst_regex, $gst_number)) {
            /*** validate GST Number with regex ***/
            $result = array('result' => false, 'message' => 'error_regex');
        } else {
            /*** validate GST Checksum ***/
            $gst_number_array = str_split($gst_number);
            $gst_number_last_letter = array_pop($gst_number_array);
            $calculated_checksum_letter = self::calculateGSTNumberChecksum($gst_number);
            
            if($gst_number_last_letter != $calculated_checksum_letter) {
                $result = array('result' => false,
                                'message' => 'error_checksum',
                                'gst_number_details' => array(
                                                            'gst_number_without_checksum' => implode("", $gst_number_array),
                                                            'gst_number_checksum' => $calculated_checksum_letter,
                                                            'calculated_gst_number' => implode("", $gst_number_array).$calculated_checksum_letter
                                                        ));
            }
        }
        /*** PAN No extraction ***/
        if(!empty($result['result'])) {
            $result['pan'] = self::getPANByGSTNumber($gst_number);
        }
        return $result;
    }
    
    /**
     * calculate gst_number's checksum digit
     * @param: $gst_number: (string)
     * @return: string: calculated checksum letter
     * @author: Anurag Jain (Aug 2018)
     **/
    public static function calculateGSTNumberChecksum(string $gst_number): string {
        
        $factor = self::$checksum_factor_even_place;
        $sum = 0;
        $gst_number_array = str_split($gst_number);
        $checksum_weight_array = str_split(self::$checksum_weight_characters);
        $checksum_mod = count($checksum_weight_array);
        
        if(count($gst_number_array) == 15) {
            array_pop($gst_number_array);
        }
        foreach ($gst_number_array as $gst_number_index => $gst_number_letter) {
            
            $current_letter_weight = array_search($gst_number_letter, $checksum_weight_array, TRUE);
            $current_checksum_digit = 0;
            if(!($current_letter_weight === FALSE)) {
                $current_checksum_digit = $current_letter_weight * $factor;
                $current_checksum_digit = (int)($current_checksum_digit / $checksum_mod) + ($current_checksum_digit % $checksum_mod);
                $sum += $current_checksum_digit;
            }
            $factor = ($factor == self::$checksum_factor_even_place) ? self::$checksum_factor_odd_place : self::$checksum_factor_even_place;
        }
        
        $calculated_checksum_weight = ($checksum_mod - ($sum % $checksum_mod)) % $checksum_mod;
        $calculated_checksum_letter = (isset($checksum_weight_array[$calculated_checksum_weight])) 
                                        ? $checksum_weight_array[$calculated_checksum_weight] 
                                        : "" ;
        return $calculated_checksum_letter;
    }
    
    /**
     * check duplicacy for gst_number (excluding same master_id)
     * @param: $gst_number: (string)
     *         $master_id: (int) current customer's master id
     * @return: array: duplicate customer details
     * @author: Anurag Jain (Aug 2018)
     **/
    public function getCustomerByDuplicateGSTNumber(string $gst_number, int $master_id = 0): array {
        $sql = "SELECT count(customer_id) AS total, customer_id, master_id, firstname, telephone, email, gst_number
                FROM ".DB_PREFIX."customer 
		        WHERE gst_number = '" . $this->db->escape($gst_number) . "' 
                    AND master_id <> '" . (int)$master_id . "'";
        
		$result = $this->db->query($sql);
		return $result->row;
	}
    
    /**
     * get required details of a given customer
     * @param: $customer_id: (int)
     * @return: array: customer details
     * @author: Anurag Jain (Aug 2018)
     **/
    public function getCustomerByCustomerId(int $customer_id): array {
        $sql = "SELECT 
                    customer_id, 
                    master_id, 
                    firstname, 
                    telephone, 
                    email, 
                    gst_number 
                FROM " . DB_PREFIX . "customer 
                WHERE customer_id = '" . $this->db->escape($customer_id) . "' 
                LIMIT 1";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            return $result->row;
        }
        return array();
    }
    
    /**
     * send mail if duplicacy of gst number is encountered
     * @param: $mail_data: (array) current and duplicate old customer details
     * @return: bool
     * @author: Anurag Jain (Aug 2018)
     **/  
    public function sendGSTNumberDuplicacyMail(array $mail_data): bool {
        $mail = new PHPMailer();
        
        $current_customer = $mail_data['current_customer'];
        $duplicate_customer = $mail_data['duplicate_customer'];
        $subject = "Duplicate GST Number Encountered - ". date('d M Y h:i:s A', time()) ." !!!";
        
        $body    = ""; 
        $body .= "Dear Team,<br><br>";
        $body .= "A new customer is trying to use GST Number: <b>". $duplicate_customer['gst_number'] ."</b>";
        $body .= " which is already being used against an old customer.<br><br>";
        $body .= "New Customer Details:<br>";
        $body .= "Customer Id: " . $current_customer['customer_id'] . "<br>";
        $body .= "Name: " . $current_customer['firstname'] . "<br>";
        $body .= "Mobile Number: " . $current_customer['telephone'] . "<br>";
        $body .= "Email: " . $current_customer['email'] . "<br>";
        $body .= "<br><br>";
        $body .= "Old Customer Details:<br>";
        $body .= "Customer Id: " . $duplicate_customer['customer_id'] . "<br>";
        $body .= "Name: " . $duplicate_customer['firstname'] . "<br>";
        $body .= "Mobile Number: " . $duplicate_customer['telephone'] . "<br>";
        $body .= "Email: " . $duplicate_customer['email'] . "<br>";
        $body .= "<br><br>"; 
        $body .= "Thanks & Regards<br>WholesaleBox"; 
        
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl'; 
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port'); 
        $mail->Username = $this->config->get('config_mail_smtp_username');        
        $mail->Password = $this->config->get('config_mail_smtp_password');
        
        if( strtolower(SITE_ENVIRONMENT) == "production" ) {
            $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
            $mail->addReplyTo(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        } else {
            $mail->addAddress('anurag.jain@wholesalebox.co', 'Staging Anurag'); 
            $mail->addAddress('dheeraj.sharma@wholesalebox.co', 'Staging Dheeraj'); 
        }
        
        $mail->Subject = $subject;                  
        $mail->Body    = $body;  
        $mail->isHTML(true);
        $mail = $mail->send(true);
        
        return $mail;
    }
	
	/**
	 * Public method to update gst number of customer
	 * 	- updates all customers with same master_ids (having same or empty gst_number)
	 *	- updates eligible customers' all orders ids with all suborders as uninvoiced OR cancelled
     *  - sync customer -> gst_number to CRM
     *  - logs changes for gst number update
	 * @param: $customer_id: (int)
     *         $master_id: (int)
     *         $old_gst_number: (string)
     *         $new_gst_number: (string)
     *         $log_changes: (bool)
	 * @return: (boolean)
	 * @author: Anurag Jain, Aug 2018
	*/
	public function updateGstNumber(int $customer_id, 
                                    int $master_id, 
                                    string $old_gst_number, 
                                    string $new_gst_number, 
                                    bool $log_changes = false): bool {
        if($log_changes) {
            $this->log_changes = $log_changes;
        }
		/*** calculate all eligible customer ids to update on basis of master_id ***/
		$update_eligible_customer_ids = $this->getGstUpdateEligibleCustomerIds($old_gst_number, $master_id);
		
		if(!empty($update_eligible_customer_ids)) {
			/*** update customers' gst_number ***/
	        $this->updateCustomersGstNumber($customer_id, $update_eligible_customer_ids, $old_gst_number, $new_gst_number);
            
			/*** calculate all eligible order ids to update on basis of customer_ids ***/
			$update_order_ids_array = OrderInfo::getOrdersWithAllUninvoicedOrCancelledSuborders($this->db, $update_eligible_customer_ids);
			if(!empty($update_order_ids_array)) {
                /*** update orders' gst_number ***/
				$this->updateOrdersGstNumber($update_order_ids_array, $old_gst_number, $new_gst_number);
			}
		}
		return true;
	}
	
	/**
	 * Public method to get all eligible customers with same master_id
     * Eligibility: gst_number empty or null or same as current customer's gst_number
	 * @param: $gst_number: (string)
     *         $master_id: (int)
	 * @return: $data: (array) customers' data
	 * @author: Anurag Jain, 25 July 2018
    */
    public function getGstUpdateEligibleCustomerIds(string $gst_number, int $master_id): array {
    	$data = array();
    	if(empty($master_id)) {
    		return $data;
    	}
        $sql = "SELECT customer_id 
                FROM " . DB_PREFIX . "customer 
                WHERE master_id = '" . (int)$master_id . "' 
                    AND (gst_number IS NULL OR gst_number = '' OR gst_number = '". $this->db->escape($gst_number) ."')";
		$query = $this->db->query($sql);
		if($query->num_rows > 0) {
			$data = array_column($query->rows, 'customer_id');
		}
		return $data;
	}
    
    /**
	 * Public method to update customers' gst_number in Website and CRM
	 * @param: $customer_ids: (array)
     *         $gst_number: (string)
	 * @return: (boolean) update query result  
	 * @author: Anurag Jain, Aug 2018
	*/
	public function updateCustomersGstNumber(int $current_customer_id, 
                                             array $customer_ids,
                                             string $old_gst_number, 
                                             string $new_gst_number): bool {
		$customer_ids_string = implode(',', $customer_ids);

        $new_gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $new_gst_number);;
        $new_gst_number = trim($new_gst_number);

        $old_gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $old_gst_number);;
        $old_gst_number = trim($old_gst_number);
        
        /*** update customers' gst_number in Website ***/
		$update_sql = "UPDATE " . DB_PREFIX . "customer 
                        SET gst_number = '" . $this->db->escape($new_gst_number). "' 
                       WHERE customer_id IN (" . $customer_ids_string . ")";
	    $update_result = $this->db->query($update_sql);
        
        /*** log customer GST no change in admin_change_log ***/
        $change_data = array();
        if($this->log_changes && $old_gst_number != $new_gst_number) {
            /* add admin_change_log */
            $change_data['table_id'] = $current_customer_id;
            $change_data['old_value'] = $old_gst_number;
            $change_data['new_value'] = $new_gst_number;
            $this->logCustomerGSTChange($change_data);
        }
        
        /*** update customers' gst_number in CRM ***/
        $request_data = array();
        foreach($customer_ids as $key => $id) {
            /* prepare crm update data */
            $request_data['data'][$id] = $new_gst_number;
        }
        $json = json_encode($request_data);
        $this->updateCustomersGstNumberInCRM($json);
        
        return true;
    }
    
	/**
	 * Public method to update orders' gst_number
	 * @param: $order_ids: (array)
     *         $old_gst_number: (string)
     *         $new_gst_number: (string)
	 * @return: (boolean) update query result
	 * @author: Anurag Jain, Aug 2018
	*/
	public function updateOrdersGstNumber(array $order_ids, string $old_gst_number, string $new_gst_number): bool {
		$order_ids = implode(',', $order_ids);

        $new_gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $new_gst_number);;
        $new_gst_number = trim($new_gst_number);
        
        $old_gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $old_gst_number);;
        $old_gst_number = trim($old_gst_number);

		$update_sql = "UPDATE ". DB_PREFIX ."order 
						SET gst_number = '".$this->db->escape($new_gst_number)."'
					   WHERE order_id IN (". $order_ids .")
					   	AND (gst_number IS NULL OR gst_number = '' OR gst_number = '".$old_gst_number."')";
        $update_result = $this->db->query($update_sql);
        return true;
	}
    
	/**
	 * Public method to update customer gst_number in CRM
	 * @param: $json: (json) => json of array ["customer_id"=>"gst_number"]
	 * @return: curl result 
	 * @author: Anurag Jain, Aug 2018
	*/
    public function updateCustomersGstNumberInCRM($json) {
        $url = $this->gst_update_crm_api_url;
        
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';

        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST		   => 1,
            CURLOPT_POSTFIELDS	   => $json,
            CURLOPT_HTTPHEADER     => $headers
        );
        curl_setopt_array($ch, $curlConfig);
        
		//execute post
		$result = curl_exec($ch);
        
		//close connection
		curl_close($ch);
        
		return $result;
    }
    
    /**
     * Public method to log customer gst change
     * @param: array $change_data
     * @return: bool
     * @author: Anurag Jain, Aug 2018
    */
    public function logCustomerGSTChange(array $change_data): bool {
        if(empty($change_data)) {
            return false;
        }
        
        /** User Details **/
		$user_id = (!empty($this->user) && !empty((int)$this->user->getId())) ? (int)$this->user->getId() : 0 ;
        if (method_exists($this->user, 'getUserName')) {
        	$user_name = $this->user->getUserName($this->user->getId())['username'];
			$name      = $this->user->getUserName($this->user->getId())['name'];
			$user_type = $this->user->getGroupName();
		} else {
			$user_name = 'Customer';
			$name      = 'Customer';
			$user_type = 'Customer';
		}
        
        $dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		$class = isset($dbt[1]['class']) ? $dbt[1]['class'] : '';
		$function = isset($dbt[1]['function']) ? $dbt[1]['function'] : '';
		$ref_url = $class .'/'. $function;
        
        /** IP and server details **/
		$ip = $this->request->getIpAddress;
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        $change_data['table_name'] = 'oc_customer';
        $change_data['source_field'] = 'edit_customer';
        $change_data['ref_url'] = $ref_url;
        $change_data['field_name'] = 'gst_number';
        $change_data['user_id'] = $user_id;
        $change_data['name'] = $name;
        $change_data['username'] = $user_name;
        $change_data['user_type'] = $user_type;
        $change_data['comment'] = '';
        $change_data['user_agent'] = $user_agent;
        $change_data['ip_address'] = $ip;
        $change_data['file_location'] = $ref_url;
        CommonLib::addAdminChangeLog($this->db, $change_data);
        return true;
    }
    
    /**
     * Public static method to get pan from gst number
     * @param: string $gst_number
     * @return: string
     * @author: Anurag Jain, Aug 2018
    */
    public static function getPANByGSTNumber(string $gst_number): string {
        return substr($gst_number, 2, 10);
    }
    
    /**
     * Public static method to get gst_state_code from gst number
     * @param: string $gst_number
     * @return: string
     * @author: Nilesh, 2018
    */
    public static function getStateCodeFromGSTNumber(string $gst_number): int {
        return (int)substr($gst_number, 0, 2);
    }
    
    /**
     * Public static method to get gst_state_code from zone table
     * @param: string $gst_number
     * @return: string
     * @author: Nilesh, 2018
    */
    public function getGstStateCodeAndNameFromZone(int $payment_zone_id): array {
        $result = array();
        $sql = "SELECT gst_state_code,
                       name as state_name
                FROM ".DB_PREFIX."zone 
                WHERE zone_id = '".(int)$payment_zone_id."'";
        $query = $this->db->query($sql);
        $result = $query->row;
        return $result;
    }
    
    /**
     * Public method to gst_number on order if gst_state_code is different
     * @param: string $gst_number
     * @return: array result of validation
     * @author: Nilesh,2018
    */
    public function validateOrderGstNoWithStateCode(string $gst_number, int $payment_zone_id): array {
        $gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);;
        $gst_number = trim($gst_number);

        $result = self::validateGSTNumberFormat($gst_number);
        
        if (!empty($gst_number) && !($result['result'] === true)) {
            return $result;
        } else {
            $actual_gst_state_code = (int) $this->getGstStateCodeAndNameFromZone($payment_zone_id)['gst_state_code'];
        
            $gst_state_code_on_order = self::getStateCodeFromGSTNumber($gst_number);
            if($gst_state_code_on_order !== $actual_gst_state_code) {
                $result = array('result' => false,
                                'message' => 'error_gst_no_mismatch');
            } else {
                $result = array('result' => true, 'message' => 'valid');
            }
        }
        return $result;
    }

    /**
     * function will decide whether to send gst duplicacy mail and updates mem cache for sent mail
     * @param  array  $data includes entity id and gst number
     * @return bool
     * @author Anurag Jain, 15 Jan 2018
     */
    protected function shouldSendGSTDuplicacyMail( array $data ): bool
    {

        $should_send_gst_duplicacy_mail = true;

        if ( class_exists('Cache\Mem') ) {
            
            $mem_cache = new Mem( $this->_duplicate_mail_delay_time );
            $cache_gst_key = $data['entity_id'] . '_' . $data['gst_number' ];

            $is_mail_sent = $mem_cache->get( $cache_gst_key );

            if ( $is_mail_sent ) {

                $should_send_gst_duplicacy_mail = false;
            } else {

                $mail_flag = 1;
                $mem_cache->set( $cache_gst_key, $mail_flag );
            }
        }
        
        return $should_send_gst_duplicacy_mail;
    }
}