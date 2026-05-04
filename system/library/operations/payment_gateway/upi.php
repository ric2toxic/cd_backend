<?php
require_once(DIR_SYSTEM.'library/operations/payment_gateway/payment_gateway_base.php');

class Upi extends PaymentGatewayBase {

	private $private_key;
	private $public_key;
	private $scb_public_key;

	public function __construct($registry) {
	
		parent::__construct($registry);
        
        // Payment gateway name
        $this->paymentgateway = 'upi';
	}

	/**
	 *	doAction
	 *	@info doAction always call because this method know that which method to call  
	 *	@param array $data
	 *	@return data
	 */
	public function doAction($data) {
		if (!empty($data['method'])) { 
			$method_name = ucfirst($data['method']).'('.$data.')';
			if(function_exists($method_name)) { // Here we can check that method is exit or not
            	$post_action = $this->$method_name;
            	$this->getPostAction($post_action);
            } else {
            	throw new Exception("Invalid Method Name in OperationsFactory->getInstance.");
            }
        } else {
            throw new Exception("Empity Method Name in OperationsFactory->getInstance.");
        }
	}

	/**
	 *	getPostAction
	 *	@info getPostAction always call in last and this will decide that which class to call 
	 *	@param 	array $data
	 *	@return data
	 */
	public function getPostAction($data) {
		return new $data();
	}

	/**
	 *	generatePaymentLink
	 *	@param 	array $data
	 *	@return response
	 */
    public function generatePaymentLink($data) {

    	if(!empty($data['order_id'])) {
				if(!empty($data['order_no'])) {
        	$order_info = parent::getOrderDetail($data);
        	$cust_vpa = "";

        	if (!empty($this->session->data['net_order_totals'])) {
                $order_info['total'] = $data['amount'] = $this->session->data['net_order_totals'];
            }

            $vpa_result = $this->getCustomerVPA((int)$order_info['customer_id']);
        	$cust_vpa   = $upi_vpa    = trim($vpa_result['upi_vpa'] ?? '');

        	if(isset($data['payer_vpa']) && !empty(trim($data['payer_vpa'])) ) {
        		$cust_vpa = trim($data['payer_vpa']);

        		if( $upi_vpa != $cust_vpa ) {
                    Customer::updateCustomerVPA($this->db, $order_info['customer_id'], $cust_vpa);
            	}
        	}

			$upi_response = array();

    		if(!empty($cust_vpa)){

    			$payload_data = array();
		
				$result_rows = array();

				//$result_rows = $this->getOrderPaymentDetailsfromOrderID($data['order_id']);

	        	$payload_data['order_id'] = $data['order_id'];
	        	$payload_data['firstname'] = $order_info['payment_firstname'];
	        	$payload_data['payer_vpa'] = $cust_vpa;
	        	$payload_data['amount'] = $payload_data['total'] = isset($data['amount'])?$data['amount']:$order_info['total'];
	        	$payload_data['telephone'] = $order_info['telephone'];
	        	$payload_data['upi_txn_id'] = $this->generateTransactionID($order_info['new_order_no']);
	        	$payload_data['expreAfterRule'] = '1440';
	        	$payload_data['currency_code'] = 'INR';
	        	$payload_data['order_no'] = $data['order_no'];

				// send payment request
				$response_payload = $this->initiateTransaction($payload_data);

				// code for query transaction
				$query_data['txnID'] = $payload_data['upi_txn_id'];
				$query_response_payload = $this->transactionsEnquiry($query_data);

				// initialize data for insertDirectCustomerPaymentDetailsIntoDb
				$value['order_no'] 		= $order_info['new_order_no'];
				$value['order_id'] 		= $data['order_id'];
				
				if(!($response_payload->status == "00")) {
					$value['upi_payment_status'] = "failed";
				} else {
					$value['upi_payment_status'] = strtolower($query_response_payload->responseStatus);
				}

				$responses = array();
				$responses['initiate'] = $response_payload;
				$responses['query'] = $query_response_payload;

				$value['json_response'] = serialize($responses);
				$value['payment_mode'] = 'UPI';

				$value['payment_link'] = 'Customer Offline Payment';
				$value['customer_id'] = (isset($data['user_id']) && !empty($data['user_id']))?$data['user_id']:$order_info['customer_id'];
				$value['successfull'] = '0';
				$payload_data['amount'] = $payload_data['total'];

				if(isset($query_response_payload->requestTime)) {
					$var = $query_response_payload->requestTime;
					$date = str_replace('/', '-', $var);
					$value['txnDateTime'] = date('Y-m-d H:i:s', strtotime($date));
				} else if(isset($response_payload->trnDateTime)) {
					$var = $response_payload->trnDateTime;
					$date = str_replace('/', '-', $var);
					$value['txnDateTime'] = date('Y-m-d H:i:s', strtotime($date));
				} else {
					$value['txnDateTime'] = date('Y-m-d H:i:s');
				}
				// Update amount from response
				if(!empty($query_response_payload->txnAmount)) {
					$payload_data['amount'] = $payload_data['total'] = $query_response_payload->txnAmount;
				}
			    $this->insertDirectCustomerPaymentDetailsIntoDb($payload_data, $value);

				// if initiation success 
				if($response_payload->status == "00") {
					
					// Insert payer vpa in DB
					$this->updateCustomerVPA($payload_data['payer_vpa'], $order_info['customer_id']);

			        $upi_response['responseMsg'] = strtoupper('SUCCESS');
			        //$upi_response['specialMsg'] = $response_payload->responseParameterMap;
			        $upi_response['payment_gateway'] = 'UPI';
			        $upi_response['upi_txn_id'] = $order_info['new_order_no'];

			        $body = '<h4>Dear '.ucfirst(strtolower($payload_data['firstname'])).',</h4><h3>Thank you for your order!</h3>';
			        $body .= "<p>Your order number is #".$order_info['order_no'].".</p>";
			        $body .= '<p>Before we process your order, please make your payment using request sent to your upi app within 24 hrs. </p>';
			        $body .= '<p>We shall share shipping details after payment confirmation.<br>
								Thank you for shopping with us! And hope to see you soon!<br>
								For any assistance, call/whatsapp: +91-8696491521</p><br>';
					$body .= 'Thanks<br>WholesaleBox Internet Pvt. Ltd.';
			        $mail = new PHPMailer();
			        $mail->isSMTP();	
			        $mail->SMTPSecure = 'ssl';
			        $mail->Host = $this->registry->config->get('config_mail_smtp_hostname');
			        $mail->Port = $this->registry->config->get('config_mail_smtp_port');
			        $mail->SMTPAuth = true;
			        $mail->Username = $this->registry->config->get('config_mail_smtp_username');
			        $mail->Password = $this->registry->config->get('config_mail_smtp_password');
			        $mail->setFrom($this->registry->config->get('config_email'), 'WholesaleBox');
			        $mail->addAddress($order_info['email']);
			        $mail->Subject = 'WholesaleBox - Pending Payment';
			        $mail->msgHTML($body);
			        $mail->send();

				} else {
					
					// fail response
			        $upi_response['responseMsg'] = "FAIL";
			        $upi_response['specialMsg'] = $response_payload->responseMsg;
			        $upi_response['payment_gateway'] = 'UPI';
					parent::errorResponse($response_payload, $data, $upi_response);
				}

				return $upi_response;
    		}
	        $upi_response['responseMsg'] = "FAIL";
	        $upi_response['specialMsg'] = "UPI VPA of customer not found.";
	        $upi_response['payment_gateway'] = 'UPI';
        	return $upi_response;
				} else {
					$upi_response['responseMsg'] = "FAIL";
	        $upi_response['specialMsg'] = "Order Number not found.";
	        $upi_response['payment_gateway'] = 'UPI';
        	return $upi_response;
				}
    	} else {
            throw new Exception("Data is empty in system/library/operations/payment_gateway/upi/generatePaymentLink.".print_r($data));
        }
    }

    /**
	 *	initiateTransaction
	 *	@param 	array $data
	 *	@return response payload
	 */
    public function initiateTransaction($data) {

    	if(!empty($data['order_id'])){

			$upi_merchant_code = '';
			$upi_wsb_vpa = '';
			$upi_web_app_id = '';

    		$settings = $this->getUPISettings();
    		$remarks = 'WholesaleBox';
    		foreach ($settings as $upi_settings) {
                if($upi_settings['key'] == 'upi_merchant_code'){$upi_merchant_code = $upi_settings['value'];}
                if($upi_settings['key'] == 'upi_merchant_name'){$upi_merchant_name = $upi_settings['value'];}
                if($upi_settings['key'] == 'upi_wsb_vpa'){$upi_wsb_vpa = $upi_settings['value'];}
                if($upi_settings['key'] == 'upi_web_app_id'){$upi_web_app_id = $upi_settings['value'];}
                if($upi_settings['key'] == 'upi_vpa_mobile'){$upi_vpa_mobile = $upi_settings['value'];}
                if($upi_settings['key'] == 'upi_module'){
                	if(strtolower($upi_settings['value']) == 'staging'){
                		$upi_module = 'scb';
                		$initiate_url = UPI_INITIATE_COLLECT_URL_STAGING;
                		$remarks = 'Test';
                	} else if (strtolower($upi_settings['value']) == 'production'){
                		$upi_module = 'scbl';
                		$initiate_url = UPI_INITIATE_COLLECT_URL;
                		if(!empty(SITE_ENVIRONMENT) && strtolower(SITE_ENVIRONMENT) == 'test') {
                			$remarks = 'Test';
			            }
                	} else {
                		$upi_module = '';
                		$initiate_url = '';
                	}
                }
            }
            
            $payload = [
			           'payerName' => $data['firstname'],
					   'payerAddr' => $data['payer_vpa'],
					   'remarks' => $remarks.' UPI Payment for Order No. '.$data['order_no'],//,
					   'txnAmount' => number_format(ceil((float)$data['total']), 2, '.', ''),
					   'payerMobileNo' => $data['telephone'],
					   'payeeMobileNo' => $upi_vpa_mobile,
					   'payeeVPA' => $upi_wsb_vpa,
					   'entityID' => $upi_module,
					   'transactionID' => $data['upi_txn_id'],
					   'appId' => $upi_web_app_id,
					   'expreAfterRule' => $data['expreAfterRule'],
					   'refUrl' => 'https://www.wholesalebox.in/',
					   'currencyCode' => $data['currency_code']
					  ];
			$payload['references'][] = ["key" => "Payment Method"];
			$payload['references'][] = ["value" => "UPI"];

			if($upi_module == 'scbl') {
	        	$payload['merchantCode'] = $upi_merchant_code;
	        	
	        	/*** Skipping this code since SCB unable to generate keys for now
	        	// create JWT token
	        	$signedJson = $this->createJsonSignature($payload);

				// send payment request
				$signedResponse = $this->invokeInitiateCollect($signedJson, $initiate_url);

				//check response verification
				$verifiedPayload = $this->verifyJsonSignature($signedResponse);
				***/

				$verifiedPayload = $this->invokeInitiateCollect(json_encode($payload), $initiate_url);
				return json_decode($verifiedPayload); 

			} else if ($upi_module = 'scb') {

				$verifiedPayload = $this->invokeInitiateCollect(json_encode($payload), $initiate_url);
				return json_decode($verifiedPayload); 
			}


    	} else {
            throw new Exception("Data is empty in system/library/operations/payment_gateway/upi/generatePaymentLink.".print_r($data));
        }
    }

    /**
	 *	transactionsEnquiry
	 *	@param 	array $data
	 *	@return response payload
	 */
    public function transactionsEnquiry($data) {
    	$settings = $this->getUPISettings();

		foreach ($settings as $upi_settings) {
            if($upi_settings['key'] == 'upi_module'){
            	if(strtolower($upi_settings['value']) == 'staging'){
            		$upi_module = 'scb';
                	$initiate_url = UPI_QUERY_TRANSACTION_URL_STAGING;
            	} else if (strtolower($upi_settings['value']) == 'production'){
            		$upi_module = 'scbl';
                	$initiate_url = UPI_QUERY_TRANSACTION_URL;
            	} else {
            		$upi_module = '';
            		$initiate_url = '';
            	}
            }
        }
    	if(!empty($data['txnID'])){

    		$payload = [
    						'txnID' => $data['txnID'],
    						'entityID' => $upi_module
    				   ];

    		if($upi_module == 'scbl') {

    			/*** Skipping this code since SCB unable to generate keys for now
    			// create JWT token
	        	$signedJson = $this->createJsonSignature($payload);

				// send payment request
				$signedResponse = $this->invokeQueryTransaction($signedJson, $initiate_url);

				//check response verification
				$verifiedPayload = $this->verifyJsonSignature($signedResponse);
				***/

				$verifiedPayload = $this->invokeQueryTransaction(json_encode($payload), $initiate_url);
				return json_decode($verifiedPayload);

    		} else if($upi_module = 'scb') {
				$verifiedPayload = $this->invokeQueryTransaction(json_encode($payload), $initiate_url);
				return json_decode($verifiedPayload);
    		}



    	} else {
            throw new Exception("Data is empty in system/library/operations/payment_gateway/upi/transactionsEnquiry.");
        }
    }

	/**
	 *	createJsonSignature
	 *	@param 	array $payload
	 *	@return JWT token
	 */
    public function createJsonSignature($payload){
		$key = $this->getKeys()['private_key'];

		$header = [
		           'typ' => 'JWT',
				   'alg' => 'HS256'
				  ];

		$header = base64_encode(json_encode($header));

		$payload = base64_encode(json_encode($payload));

		// Generates a keyed hash value using the HMAC method
		$signature = hash_hmac('SHA256',$header.'.'.$payload, $key, true);

		//base64 encode the signature
		$signature = base64_encode($signature);

		//concatenating the header, the payload and the signature to obtain the JWT token
		$token = "$header.$payload.$signature";
		return $token;
	}

	/**
	 *	invokeInitiateCollect
	 *	@param  $signedJson
	 *	@return signed json response
	 */
	private function invokeInitiateCollect($signedJson, $initiate_url){
		$url = $initiate_url;

		$headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';

        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST		   => 1,
            CURLOPT_POSTFIELDS	   => $signedJson,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYHOST => false, 
            CURLOPT_SSL_VERIFYPEER => false
        );
        curl_setopt_array($ch, $curlConfig);
//echo "InitiateCollect Request: "; print_r($signedJson);//die;
		//execute post
		$result = curl_exec($ch);
		if(curl_error($ch)) {
            echo 'error:' . curl_error($ch);
        }
//echo "InitiateCollect Response: "; print_r($result);die;
		//close connection
		curl_close($ch);
		//return $result;
		return $result;
	}
	/**
	 *	invokeQueryTransaction
	 *	@param  signed json
	 *	@return signed response
	 */
	private function invokeQueryTransaction($signedJson, $initiate_url) {
		$url = $initiate_url;

		$headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';

        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST		   => 1,
            CURLOPT_POSTFIELDS	   => $signedJson,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYHOST => false, 
            CURLOPT_SSL_VERIFYPEER => false
        );
        curl_setopt_array($ch, $curlConfig);

		//execute post
		$result = curl_exec($ch);
		if(curl_error($ch)) {
            echo 'error:' . curl_error($ch);
        }

		//close connection
		curl_close($ch);

		return $result;
	}

	/**
	 *	verifyJsonSignature
	 *	@param  signed json response
	 *	@return payload
	 */
	private function verifyJsonSignature($signedResponse){
		$key = $this->getKeys()['scb_public_key'];

		//$token = json_decode($signedResponse);

		//$jwt_access_token = $token['access_token'];

		$separator = '.';

		if (2 !== substr_count($signedResponse, $separator)) {
		    //throw new Exception("Incorrect access token format");
		    $this->session->data['error'] = "Invalid Access";
		}

		list($header, $payload, $signature) = explode($separator, $signedResponse);

		$decoded_signature = base64_decode(str_replace(array('-', '_'), array('+', '/'), $signature));

		// The header and payload are signed together
		$payload_to_verify = utf8_decode($header.$separator.$payload);

		// however you want to load your public key
		//$public_key_scb = file_get_contents('/path/to/pubkey.pem');

		// default is SHA256
		$verified = openssl_verify($payload_to_verify, $decoded_signature, $key, OPENSSL_ALGO_SHA256);

		if ($verified !== 1) {
		    $this->session->data['error'] = "Invalid Access";
		}

		return $payload;
	}

	/**
     *  insertDirectCustomerPaymentDetailsIntoDb
     *  @param  array $response
     *  @param  array $value
     **/
    public function insertDirectCustomerPaymentDetailsIntoDb($response, $value) {

        $payable_amt    = 0;
        $paid_amt       = (float)$response['amount'];
        
        //Define data array 
        $data = array(); 
        $data['order_id']           = (int)$value['order_id'];
        $data['merchant_txn_id']    = $response['upi_txn_id'];
        $data['order_no']           = $value['order_no'];
        $data['txn_status']         = $value['upi_payment_status'];
        $data['payment_mode']       = $value['payment_mode'];   
        $data['amount']             = (float)$paid_amt; 
        $data['txn_date_time']      = $value['txnDateTime'];
        $data['date_added']         = 'NOW()';
        $data['payment_gateway']    = 'upi';
        $data['successfull']        = $value['successfull'];
        $data['reference']          = '';
        $data['payment_link']       = $value['payment_link'];
        $data['json_format']        = $value['json_response'];
        $data['user_id']            = $value['customer_id'];
        
        $payment_id = OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table
        return $payment_id;
    }

	private function base64url_encode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	private function base64url_decode($data) {
		return rtrim(strtr(base64_decode($data), '+/', '-_'), '=');
	}

	public function getUPISettings(){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE code = 'upi'");
        if ($query->num_rows) {
        	return $query->rows;    
        }
	}
	private function getKeys(){

		// Extract the private key from $res to $privKey
		// openssl_pkey_export($res, $privKey);

		// // Extract the public key from $res to $pubKey
		// $pubKey = openssl_pkey_get_details($res);
		// $pubKey = $pubKey["key"];

		$setting = $this->getUPISettings();

		$upi_private_key = '';
		$upi_public_key = '';
		$upi_scb_public_key = '';
		
		// Private key as PEM string
		$pem_private_key = file_get_contents("/etc/ssl/private/ssl-cert-snakeoil.key");
		// As PHP resource
		$upi_private_key = openssl_pkey_get_private($pem_private_key);

		// Public key as PEM string
		$pem_public_key = openssl_pkey_get_details($upi_private_key)['key'];
		// As PHP resource
		$upi_public_key = openssl_pkey_get_public($pem_public_key);

        if ($setting) {
            
            foreach ($setting as $upi_settings) {
                if($upi_settings['key'] == 'upi_scb_public_key'){$upi_scb_public_key = $upi_settings['value'];}
            }
        } else {
            //return $order_id;
            throw new Exception("Invalid request.");
        }

        $keys = [
        	'public_key'  => $pem_public_key,//file_get_contents('/path/to/pubkey.pem');
        	'private_key' => $pem_private_key, //file_get_contents('/path/to/pubkey.pem');
        	'scb_public_key' => $pem_public_key
        ];	

		return $keys;
	}

	public function paymentRefund($data) {}

	private function generateTransactionID($order_no){
		// generate unique txn id for txn
		return $order_no;
	}

	private function getOrderPaymentDetailsfromOrderID($order_id){

		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_payment` WHERE order_id = '" . $order_id . "' AND payment_mode = 'UPI' ORDER BY payment_id DESC LIMIT 1");
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_payment` 
		                           WHERE order_id = '" . (int)$order_id . "' ORDER BY payment_id DESC LIMIT 1");
        if ($query->rows) {
            return $query->rows;
        } else {
            //return $order_id;
            throw new Exception("Invalid link generation request.");
        }
	}

	public function NotifyTxnStatus($notify_request){
		//check rerquest verification
		$verifiedPayload = $this->verifyJsonSignature($notify_request);
		$notify = json_decode(base64_decode($verifiedPayload));
		return $notify;
	}

	public function updateCustomerVPA($vpa, $customer_id) {
		$sql = "UPDATE ".DB_PREFIX."customer
				SET upi_vpa = '".$this->db->escape($vpa)."'
				WHERE customer_id = '". (int)$customer_id."'";
		$update_result = $this->db->query($sql);
        return $update_result;
	}

	public function getCustomerVPA($customer_id) {
		$sql = "SELECT upi_vpa
				FROM ".DB_PREFIX."customer
				WHERE customer_id = '". (int)$customer_id."' LIMIT 1";
		$query = $this->db->query($sql);

		if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return array();
        }
	}
}
