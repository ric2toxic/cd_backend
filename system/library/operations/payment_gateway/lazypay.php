<?php
require_once(DIR_SYSTEM.'library/operations/payment_gateway/payment_gateway_base.php');

class LazypayPayment extends PaymentGatewayBase {

	private $_partner_id;
	private $_partner_key;
	private $_access_key;
	private $_secret_key;
	private $_signature;
	private $_pre_auth_token;
	private $_user_eligibility_check_duration = 5; // minutes

	public function __construct($registry) {
        
		parent::__construct($registry);
        $this->paymentgateway = 'lazypay';
        
	    $this->_access_key  = $this->config->get('lazypay_access_key');
	    $this->_secret_key  = $this->config->get('lazypay_secret_key');
	    $this->_signature   = '';
	    $this->_pre_auth_token = ''; 
	}

    public function sendRequest($gateway_url, $post_data = '', $isReleaseAPI = 0){

        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_URL, $gateway_url);
        if(!empty($post_data)){ 
            @curl_setopt($ch, CURLOPT_POST, true);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        //In LazyPay Release API - POST method with blank post data array
        if($isReleaseAPI) {
        	@curl_setopt($ch, CURLOPT_POST, true);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_HEADER, false);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders());
        @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = @curl_exec($ch);
        if (!$result) {
            $result = json_encode(array(
                'status' => 'Failed',
                'message' => 'Error while getting data - ' . curl_error($ch)
            ));
        }

        @curl_close($ch);

        return $result;
    }

    /*
     * @method: getRequestHeaders - Lazypay request headers
     * @params: void
     * @return: array 
     * @author: MSA, Jan 2019
     */
    public function getRequestHeaders()
    {
       $headers = array();
       $headers[] = 'signature: '.$this->_signature;
       $headers[] = 'accessKey: '.$this->_access_key;
       if(!empty($this->_pre_auth_token)) {
               $headers[] = 'Authorization: '.$this->_pre_auth_token;  
       }
       $headers[] = 'content-type: application/json';
      // pr($headers);
       return $headers;
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

    public function generatePaymentLink($data){
	    // For now this method will do nothing
    }

    public function paymentRefund($data){
        // For now this method will do nothing
    }

	/*
	 * @method: getTransactionStatus- get LazyPay status of an order
	 * @params: order_no
	 * @return: response 
	 * @author: MSA, July 2018
	 */
	public function getTransactionStatus(string $order_no): array {
				// first we check in database, 
				// if order status for this order is already their then we will return the same, 
				// otherwise we will call neo-growth api
				$result = $this->getLazypayTransactionLogUsingOrderNo($order_no, 'GetOrderStatus');
				if (!empty($result)) {
					$order_status_result = $result[0]['response'];
					return json_decode($order_status_result, true);
				}
				
        $neo_url = NEOPAYLATER_TRANSACTION_STATUS_API . '?partner_id='.$this->_partner_id.'&order_id='.$order_no;
				// create log
				$log_data = array();
				$log_data['url'] = $neo_url;
				$log_data['request_type'] = 'GetOrderStatus';
				$log_data['order_no'] = $order_no;
				$log_data['request'] = 'partner_id=' . $this->_partner_id . '&order_id=' . $order_no;
				$log_id = $this->createLazypayTransactionLog($log_data);
				
				// send request
        $result = $this->sendRequest($neo_url);
				$response = json_decode($result,true);
				
				// update the logs
				$log_data = array(
					'response' => $result,
					'status' => strtoupper($response['status']),
					'message' => $response['message']
				);
				$this->updateLazypayTransactionLog($log_id, $log_data);
				
				// check LazyPay purchase transaction sttus, if status is not equal to success, 
				// then update the status according to the above response 
				$result = $this->getLazypayTransactionLogUsingOrderNo($order_no, 'Purchased');
				if (!empty($result)) {
					$order_result = $result[0];
					if ($order_result['status'] !== 'SUCCESS') {
						$status = ($response['code'] == "ss-202") ? "SUCCESS" : "FAILED";
						$this->updateLazypayTransactionLog($order_result['log_id'], array('status' => $status));
					}
				}

        return $response;

	}

	/*
	 * @method: getCustomerLazypayData
	 * @params: customer id
	 * @return: mobile, email 
	 * @author: Mahaveer, Jan 2019
	 */
	public function getCustomerLazypayData(int $customer_id): array {
		$sql = "SELECT 
						cc.lazypay_mobile, 
						cc.lazypay_email,
						ca.firstname,
						ca.lastname
				FROM 
					" . DB_PREFIX . "customer_credit AS cc
				INNER JOIN 
					" . DB_PREFIX . "customer AS ca ON ca.customer_id = cc.customer_id
				WHERE 
					cc.customer_id = '" . (int)$customer_id  ."' 
					AND 
					cc.type = 'Lazypay' LIMIT 1
				";
		$query = $this->db->query($sql);
		$data = array();
		if( $query->num_rows ){
			$data = array(
				'lazypay_email'  => $query->row['lazypay_email'],
				'lazypay_mobile' => $query->row['lazypay_mobile'],
				'firstname'		 => $query->row['firstname'],	
				'lastname'		 => $query->row['lastname'],	
			);
		} 
		return $data;
	}
	
	public function userLazypayActivityStatusChecked( string $buyer_registration_number, 
											 		  string $check_action = 'EligibilityCheck'
													)
	{
		$sql = "
				SELECT 
					TIMESTAMPDIFF( MINUTE, date_added, NOW() ) as minutes
				FROM 
					" . DB_PREFIX . "transaction_logs 
				WHERE 
					type = 'LAZYPAY'
					AND
					buyer_registration_number = '" . $this->db->escape( $buyer_registration_number ) . "'
					AND
					request_type = '" . $this->db->escape( $check_action ) . "'
					AND
					status ='SUCCESS'
				HAVING 
					minutes < '" . $this->db->escape( $this->_user_eligibility_check_duration ) . "'

			";
		$query = $this->db->query( $sql );
		if( $query->num_rows ) {
			return true;
		}
		return false;
	}

	public function setLazypaySignature(string $type, array $params)
	{
		$signature_string = '';

		switch( $type ) {

            case 'eligibility' : 
                   $signature_string .= $params['mobile'];
                   $signature_string .= $params['email'];
                   $signature_string .= $params['amount'];
                   $signature_string .= $params['currency'];
                   break;
            case 'initiate_preauth' : 
                   $signature_string .= "merchantAccessKey=" . $params['accessKey'];
                   $signature_string .= "&transactionId=" . $params['transactionId'];
                   $signature_string .= "&amount=" . $params['txnAmount'];
                   break;
            case 'authorized_preauth' : 
                   $signature_string .= "merchantAccessKey=" . $params['accessKey'];
                   $signature_string .= "&transactionId=" . $params['transactionId'];
                   $signature_string .= "&otp=" . $params['otp'];
                   break;
            case 'capture_payment' : 
                   $signature_string .= "merchantAccessKey=" . $params['accessKey'];
                   $signature_string .= "&txnRefNo=" . $params['txnRefNo']; 
             	   break;
            case 'release_payment' : 
                   $signature_string .= "merchantAccessKey=" . $params['accessKey'];
                   $signature_string .= "&txnRefNo=" . $params['txnRefNo']; 
             	   break;
            case 'refund' : 
	    			$signature_string .= "merchantAccessKey=" . $params['accessKey'];
	                $signature_string .= "&merchantTxnId=" . $params['merchantTxnId']; 
	                $signature_string .= "&amount=" . $params['amount']; 
	             	break;
	        case 'enquiry' : 
	    			$signature_string .= "merchantAccessKey=" . $params['accessKey'];
	                $signature_string .= "&merchantTransactionId=" . $params['merchantTransactionId']; 
	             	break;     
           default: 
                   break;

			}
		//echo '<br>' . $signature_string . '<br>';
		$this->_signature =  hash_hmac('SHA1', $signature_string,  $this->_secret_key);
	}

	/*
	 * @method: lazypay_payments
	 * @params: lazypay api end point url
	 * @return: api data list
	 * @author: MSA, Jan 2019
	 */
	public function lazypay_payments(string $api_endpoint, array $api_data = array(), int $isReleaseAPI = 0): array {

		if(!empty($api_data)) {

			$result = $this->sendRequest( $api_endpoint, json_encode($api_data) );

		}else{

			if( $isReleaseAPI ) {
				$result = $this->sendRequest( $api_endpoint, array(), $isReleaseAPI );
			}else{
				$result = $this->sendRequest( $api_endpoint );
			}
		}

		$response = json_decode($result,true);
		
		return $response;
	}

    public function updateCreditBalance(int $customer_id, float $balance): bool {
        $sql = "UPDATE " . DB_PREFIX . "customer_credit 
                SET credit_balance = '" . $this->db->escape($balance) ."' 
                WHERE customer_id = '" . (int)$customer_id  ."' and type = 'Lazypay'";
        if ($this->db->query($sql)) {
            return true;
        }

        return false;
    }
		
		public function generateCheckSumHash(string $string_to_sign, string $shared_secret): string {
        //return hash_hmac("sha1", '', $string_to_sign, false);
        return base64_encode(hash_hmac("sha1", $string_to_sign, $shared_secret, true));
    }
	
	    /*
		 * @method: getCustomerRegistrationNumber
		 * @params: customer id
		 * @return: buyer registration number
		 * @author: MSA, Jan 2019
		 */
		public function getCustomerRegistrationNumber(int $customer_id): string {
		    $sql = "SELECT lazypay_mobile 
                FROM " . DB_PREFIX . "customer_credit
                WHERE customer_id = '" . (int)$customer_id  ."' and type = 'Lazypay'";
	        $query = $this->db->query($sql);

	        if( $query->num_rows ){
	            return $query->row['lazypay_mobile'];
	        } else {
	            return '';
	        }
		}


		/*
		 * @method: refundOrder- refund action over lazypay orders
		 * @params: order_data = array(order_no, detail, customer_id, total)
		 * @return: lazypay response 
		 * @author: MSA, Jan 2019
		 */
		public function refundOrder(array $order_data, $model_checkout_order) {

			$data = array();
			
			$customer_id = $order_data['customer_id'];
			$order_id    = $order_data['order_id'];
			$order_no    = $order_data['order_no'];
			$request_type= $order_data['action'];
			$total       = sprintf("%.2f", $order_data['total']);
			$detail      = $order_data['detail'];
			$buyer_registration_number = $this->getCustomerRegistrationNumber($customer_id);
			$lazypay_records = $this->getLazypayTransactionLogUsingOrderNo($order_data['order_no'], 'Purchased');
			$response  =array();
			if(!empty($lazypay_records)) {
				
				$lazypay_transaction = $lazypay_records[0];
				
				$api_data       = array(
		                                'merchantTxnId' => $lazypay_transaction['transaction_id'],
		                                'amount'    => array(
		                                    'value'     => $total, 
		                                    'currency'  => 'INR'
		                                ),
		                            );
				$this->setLazypaySignature('refund',
	                                    array(
	                                            'accessKey'  	=> $this->_access_key,
	                                            'merchantTxnId' => $lazypay_transaction['transaction_id'], //lpTxnId, transaction_id
	                                            'amount'	 	=> $total, 
	                                        )
	                                    );

				$api_endpoint   = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_REFUND_URL;
				
				$response = $this->lazypay_payments( $api_endpoint, $api_data );

				// Lazypay log entry
				$log_data = array();
				$log_data['url']        = $api_endpoint;
	            $log_data['request']    = json_encode($api_data);
	            $log_data['order_no']   = $order_no;
	            $log_data['order_id']   = $order_id;
	            $log_data['transaction_id'] = $lazypay_transaction['transaction_id'];
	            $log_data['request_type'] = 'Refund';
	            $log_data['transaction_amount']  = $total; 
	            $log_data['buyer_registration_number'] = $buyer_registration_number;
	            $log_id = $this->createLazypayTransactionLog($log_data);

				if(!empty($response['status']) && $response['status'] == 'REFUND_SUCCESS')
				{
					$log_data = array();
					$log_data['response']   = json_encode($response);
	                $log_data['status']     = 'SUCCESS';
	                $log_data['message']    = $response['respMessage'] ?? 'Refund is successful over Lazypay' ;
	                $log_data['lpTxnId']    = $response['lpTxnId'];
	                $this->updateLazypayTransactionLog($log_id, $log_data);

	                $response['success'] = 1;
					$response['message'] = $log_data['message'];

					/*Inert Order payment data for Refund action */
					$is_success_data = array();
					$is_success_data['successfull'] = 1;
					$is_success_data['order_id']          = $order_id;
                    $is_success_data['order_no']          = $order_no;
                    $is_success_data['payment_mode']      = 'lazypay_refund';
                    $is_success_data['merchant_txn_id']   = $lazypay_transaction['transaction_id'];
                    $is_success_data['refund_amount']     = -abs(round($total, 2));
                    $is_success_data['payment_link']      = 'Lazypay Direct Payment';
                    $is_success_data['payment_gateway']   = 'lazypay';
                    $is_success_data['reference']         = $response['lpTxnId'];
                    $is_success_data['user_id']           = 0;
                    $is_success_data['json_format']       = serialize($response);
                    parent::insertRefundPaymentIntoDb($is_success_data); 

					/*Add refund order history*/
						$input = array();
			            $input['order_id']          = $order_data['order_id'];
			            $input['suborder_id']       = '';
			            $comment="Refunded Amount: ".$this->registry->currency->format($order_data['total']) . " Over LazyPay";
			            $input['comment'] = $comment;
			            $input['notes'] = 'Refund successfully placed on Lazypay.';
						$input['user']	= $this->user->getUserName();
						$this->setOrderHistory($input);
				
				} else { 

					/* Call Enquery API to confirm transaction status over lazypay*/
					$api_endpoint   = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_ENQUIRY;
					$this->setLazypaySignature('enquiry',
                                        array(
                                                'accessKey'             => $this->_access_key,
                                                'merchantTransactionId' => $lazypay_transaction['transaction_id']
                                            )
                                        );
                     $api_endpoint = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_ENQUIRY . '?merchantTxnId=' . $lazypay_transaction['transaction_id'];
                     $enquery_result = $this->lazypay_payments( $api_endpoint );
                     if(!empty($enquery_result)) {
                     	
                     	/* Get last transaction history data index */ 
                       	$lastTransactionIndex  = ( count( $enquery_result )-1 );
                       
                       	if(!empty($enquery_result[$lastTransactionIndex]['status']) 
                            && 
                            $enquery_result[$lastTransactionIndex]['status'] == 'SUCCESS'
                            &&
                            $enquery_result[$lastTransactionIndex]['txnType'] == 'REFUND'
                        ) {
                       	
                       		/*Amount refunded successfully over lazypay*/
                       		$response = $enquery_result[$lastTransactionIndex];
                       		$log_data = array();
							$log_data['response']   = json_encode($response);
			                $log_data['status']     = 'SUCCESS';
			                $log_data['message']    = $response['respMessage'] ?? 'Refund is successful over Lazypay' ;
			                $log_data['lpTxnId']    = $response['lpTxnId'];
			                $this->updateLazypayTransactionLog($log_id, $log_data);

			                $response['success'] = 1;
							$response['message'] = $log_data['message'];

							/*Inert Order payment data for Refund action */
							$is_success_data = array();
							$is_success_data['successfull'] = 1;
							$is_success_data['order_id']          = $order_id;
		                    $is_success_data['order_no']          = $order_no;
		                    $is_success_data['payment_mode']      = 'lazypay_refund';
		                    $is_success_data['merchant_txn_id']   = $lazypay_transaction['transaction_id'];
		                    $is_success_data['refund_amount']     = -abs(round($total, 2));
		                    $is_success_data['payment_link']      = 'Lazypay Direct Payment';
		                    $is_success_data['payment_gateway']   = 'lazypay';
		                    $is_success_data['reference']         = $response['lpTxnId'];
		                    $is_success_data['user_id']           = 0;
		                    $is_success_data['json_format']       = serialize($response);
		                    parent::insertRefundPaymentIntoDb($is_success_data); 

							/*Add refund order history*/
								$input = array();
					            $input['order_id']          = $order_data['order_id'];
					            $input['suborder_id']       = '';
					            $comment="Refunded Amount: ".$this->registry->currency->format($order_data['total']) . " Over LazyPay";
					            $input['comment'] = $comment;
					            $input['notes'] = 'Refund successfully placed on Lazypay.';
								$input['user']	= $this->user->getUserName();
								$this->setOrderHistory($input);
                        	
                        	$response['success'] = 1;
							$response['message'] = $log_data['message'];
                        }else{
                        	$log_data = array();
							$log_data['response']   = json_encode($response);
			                $log_data['status']     = 'FAILED';
			                $log_data['message']    = $response['message'] ?? 'Refund not completed over Lazypay' ;
			                $this->updateLazypayTransactionLog($log_id, $log_data);
							
							$response['error_warning'] = 1;
							$response['message'] = $log_data['message'];
                        }

                     } else { 

                     	$log_data = array();
						$log_data['response']   = json_encode($response);
		                $log_data['status']     = 'FAILED';
		                $log_data['message']    = $response['message'] ?? 'Refund not completed over Lazypay' ;
		                $this->updateLazypayTransactionLog($log_id, $log_data);
						
						$response['error_warning'] = 1;
						$response['message'] = $log_data['message'];

                     }
				}

			} else {

				$response['error_warning'] = 1;
				$response['message'] = 'Lazypay transaction log data not found!!';
			}

			return $response;
		}
		
		/*
		 * @method: captureOrder- capture payment action over lazypay orders
		 * @params: order_data = array(order_no, detail, customer_id, total)
		 * @return: lazypay response 
		 * @author: MSA, Jan 2019
		 */
		public function captureOrder(array $order_data, $model_checkout_order) {

			$data = array();
			
			$customer_id = $order_data['customer_id'];
			$order_id    = $order_data['order_id'];
			$order_no    = $order_data['order_no'];
			$request_type= $order_data['action'];
			$total       = sprintf("%.2f", $order_data['total']);
			$detail      = $order_data['detail'];
			$buyer_registration_number = $this->getCustomerRegistrationNumber($customer_id);
			$lazypay_records = $this->getLazypayTransactionLogUsingOrderNo($order_data['order_no'], 'AuthorizedPreAuth');
			
			$response  =array();

			if(!empty($lazypay_records[0])) 
			{
				$lazypay_transaction = $lazypay_records[0];
				$lazypay_tokens = json_decode($lazypay_transaction['response'],true);
				$lpTxnId        = $lazypay_tokens['lpTxnId'] ?? '';
		        $preAuthToken   = $lazypay_tokens['preAuthToken'] ?? '';
		        $api_data       = array(
		                                'amount'    => array(
		                                    'value'     => $total, 
		                                    'currency'  => 'INR'
		                                ),
		                            );
		        $api_endpoint   = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_AUTHORISED_CAPTURE;
		        $this->setLazypaySignature('capture_payment',
		                                    array(
		                                            'accessKey'     => $this->_access_key,
		                                            'txnRefNo'      => $preAuthToken
		                                        )
		                                    );

		        $this->_pre_auth_token = $preAuthToken;

        		$final_result = $this->lazypay_payments( $api_endpoint, $api_data );
        		// Call Lazypay API to capture payment - confirm payment transaction
	            $log_data = array();
	            $log_data['url']        = $api_endpoint;
	            $log_data['request']    = json_encode($api_data);
	            $log_data['order_no']   = $order_no;
	            $log_data['order_id']   = $order_id;
	            $log_data['transaction_id'] = $order_no;
	            $log_data['request_type'] = 'Purchased';
	            $log_data['transaction_amount']  = $total; 
	            $log_data['buyer_registration_number'] =  $this->getCustomerRegistrationNumber($customer_id);
	            $log_id = $this->createLazypayTransactionLog($log_data);

	            if(empty($final_result['status']) && $final_result['status'] == 'SUCCESS')
           		{
           			$log_data = array();
	                $log_data['response']   = json_encode($final_result);
	                $log_data['status']     = 'SUCCESS';
	                $log_data['message']    = "Lazypay payment transaction completed" ;
	                $log_data['lpTxnId']    = $final_result['lpTxnId'];
	                $this->updateLazypayTransactionLog($log_id, $log_data);

	                /*Add Order History data for new order*/
	                $input = array();
		            $input['order_id']          = $order_id;
		            $input['suborder_id']   	= '';
		            $comment="Net Captured Amount: ".$this->registry->currency->format($total)." over LazyPay";
		            $input['comment'] = $comment;
		            $input['notes'] = 'Amount successfully captured on Lazypay.';
					$input['user']	= $this->user->getUserName();
					$this->setOrderHistory($input);

                    /*Inert Order payment data for Capture action */
					$payment_data = array();
                    $payment_data['amount']         = $total;
                    $payment_data['order_id']       = $order_id;
                    $payment_data['merchant_txn_id']= $order_no; 
                    $payment_data['order_no']       = $order_no;
                    $payment_data['paymentMode']    = 'lazypay_credit';
                    $payment_data['txn_status']     = 'SUCCESS';
                    $payment_data['payment_gateway']= 'lazypay';
                    $payment_data['reference']      = $final_result['lpTxnId'];
                    $payment_data['json_response']  = json_encode($final_result);
                    $this->insertDirectCustomerPaymentDetailsIntoDb($payment_data);

                    $response['success'] = 1;
					$response['message'] = $log_data['message'];
					$this->session->data['success'] = $response['message'];

           		} else { 

           			/* Call Enquery API to confirm transaction status over lazypay*/
					$api_endpoint   = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_ENQUIRY;
					$this->setLazypaySignature('enquiry',
                                        array(
                                                'accessKey'             => $this->_access_key,
                                                'merchantTransactionId' => $order_no
                                            )
                                        );
                     $api_endpoint = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_ENQUIRY . '?merchantTxnId=' . $lazypay_transaction['transaction_id'];
                     $enquery_result = $this->lazypay_payments( $api_endpoint );
                    
                    if(!empty($enquery_result)) 
                    {
                     	/* Get last transaction history data index */ 
                       	$lastTransactionIndex  = ( count( $enquery_result )-1 );

                       	if(!empty($enquery_result[$lastTransactionIndex]['status']) 
                            && 
                            $enquery_result[$lastTransactionIndex]['status'] == 'SUCCESS'
                            &&
                            $enquery_result[$lastTransactionIndex]['txnType'] == 'SALE'
                        ) {
                       		
                       		/*Amount refunded successfully over lazypay*/
                       		$response = $enquery_result[$lastTransactionIndex];
                       		$log_data = array();
							$log_data['response']   = json_encode($response);
			                $log_data['status']     = 'SUCCESS';
			                $log_data['message']    = $response['respMessage'] ?? 'Payment is successful over Lazypay' ;
			                $log_data['lpTxnId']    = $response['lpTxnId'];
			               
			                $this->updateLazypayTransactionLog($log_id, $log_data);

			                $response['success'] = 1;
							$response['message'] = $log_data['message'];

							/*Inert Order payment data for Capture action */
							$payment_data = array();
		                    $payment_data['amount']         = $total;
		                    $payment_data['order_id']       = $order_id;
		                    $payment_data['merchant_txn_id']= $order_no; 
		                    $payment_data['order_no']       = $order_no;
		                    $payment_data['paymentMode']    = 'lazypay_credit';
		                    $payment_data['txn_status']     = 'SUCCESS';
		                    $payment_data['payment_gateway']= 'lazypay';
		                    $payment_data['reference']      = $response['lpTxnId'] ?? '';
		                    $payment_data['json_response']  = json_encode($response);
		                  	
		                   	$this->insertDirectCustomerPaymentDetailsIntoDb($payment_data);

							/*Add refund order history*/
							$input = array();
				            $input['order_id']          = $order_id;
				            $input['suborder_id']   	= '';
				            //$input['order_status_id']   = 1;
				            $comment="Net Captured Amount: ".$this->registry->currency->format($total)." over LazyPay";
				            $input['comment'] = $comment;
				            $input['notes'] = 'Amount successfully captured on Lazypay.';
				            $input['user']	= $this->user->getUserName();
							$this->setOrderHistory($input);
						
							$response['success'] = 1;
							$response['message'] = $log_data['message'];  
							$this->session->data['success'] = $response['message']; 

                       }else{
                       	
                       		$log_data = array();
							$log_data['response']   = json_encode($enquery_result);
			                $log_data['status']     = 'FAILED';
			                $log_data['message']    = $enquery_result['message'] ?? $enquery_result['error'] ?? 'Payment not completed over Lazypay' ;
			                $this->updateLazypayTransactionLog($log_id, $log_data);
							
							$response['error_warning'] = 1;
							$response['message'] = $log_data['message'];
							$this->session->data['success'] = $response['message'];
                       }
                    
                    }else{

                    	$log_data = array();
						$log_data['response']   = json_encode($final_result);
		                $log_data['status']     = 'FAILED';
		                $log_data['message']    = $final_result['message'] ?? $final_result['error'] ?? 'Payment not completed over Lazypay' ;
		                $this->updateLazypayTransactionLog($log_id, $log_data);
						
						$response['error_warning'] = 1;
						$response['message'] = $log_data['message'];
						$this->session->data['success'] = $final_result['message'];
                    }	
           		}

			} else { 

				$response['error_warning'] = 1;
				$response['message'] = 'Lazypay transaction log data not found!!';
				$this->session->data['success'] = $response['message'];
			}
			return $response;
		}

		/*
		 * @method: releaseOrder- release payment action over lazypay
		 * @params: order_data = array(order_no, detail, customer_id, total)
		 * @return: lazypay response 
		 * @author: MSA, Jan 2019
		 */
		public function releaseOrder(array $order_data, $model_checkout_order) {

			$data = array();
			
			$customer_id = $order_data['customer_id'];
			$order_id    = $order_data['order_id'];
			$order_no    = $order_data['order_no'];
			$request_type= $order_data['action'];
			$total       = sprintf("%.2f", $order_data['total']);
			$detail      = $order_data['detail'];
			$buyer_registration_number = $this->getCustomerRegistrationNumber($customer_id);
			$lazypay_records = $this->getLazypayTransactionLogUsingOrderNo($order_data['order_no'], 'AuthorizedPreAuth');
			
			$response  =array();

			if(!empty($lazypay_records[0])) 
			{
				$lazypay_transaction = $lazypay_records[0];
				$lazypay_tokens = json_decode($lazypay_transaction['response'],true);
				$lpTxnId        = $lazypay_tokens['lpTxnId'] ?? '';
		        $preAuthToken   = $lazypay_tokens['preAuthToken'] ?? '';
		        $api_data       = array();
		        $api_endpoint   = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_PREAUTH_RELEASE;
		        $this->setLazypaySignature('release_payment',
		                                    array(
		                                            'accessKey'     => $this->_access_key,
		                                            'txnRefNo'      => $preAuthToken
		                                        )
		                                    );

		        $this->_pre_auth_token = $preAuthToken;
	        		
        		$release_result = $this->lazypay_payments( $api_endpoint, $api_data, 1 );
		        
        		// Call Lazypay API to capture payment - confirm payment transaction
	            $log_data = array();
	            $log_data['url']        = $api_endpoint;
	            $log_data['request']    = json_encode($api_data);
	            $log_data['order_no']   = $order_no;
	            $log_data['order_id']   = $order_id;
	            $log_data['transaction_id'] = $order_no;
	            $log_data['request_type'] = 'ReleasedPreAuth';
	            $log_data['transaction_amount']  = $total; 
	            $log_data['buyer_registration_number'] =  $this->getCustomerRegistrationNumber($customer_id);
	            $log_id = $this->createLazypayTransactionLog($log_data);
	            if(!empty($release_result['status']) && $release_result['status'] == 'SUCCESS')
	            {
	            	$log_data = array();
	                $log_data['response']   = json_encode($final_result);
	                $log_data['status']     = 'SUCCESS';
	                $log_data['message']    = "Order Amount ".$this->registry->currency->format($total)." Released Over LazyPay which is freezed for the order" ;
	                $log_data['lpTxnId']    = $final_result['lpTxnId'];
	                $this->updateLazypayTransactionLog($log_id, $log_data);

	                /*Add refund order history*/
					$input = array();
		            $input['order_id']          = $order_id;
		            $input['suborder_id']   	= '';
		            $input['order_status_id']   = '';
		            $comment="Order Amount: ".$this->registry->currency->format($total)." Released Over LazyPay";
		            $input['comment'] = $comment;
		            $input['notes'] = 'Freeze amount released successfully over Lazypay.';
					$input['user']	= $this->user->getUserName();
					$this->setOrderHistory($input);

	                $response['success'] = 1;
					$response['message'] = 'Order amount released over LazyPay successfully.';

	            } else { 
	            	$response['error_warning'] = 1;
					$response['message'] = 'Order amount not released over LazyPay.';
	            }

			}else {
				$response['error_warning'] = 1;
				$response['message'] = 'Lazypay transaction log data not found!!';
			}
			return $response;
		}

		/**
	     * Function to call front side API to udpate order history
	     * @author : MSA, Dec. 2018
	     */
	    public function setOrderHistory(array $post_params)
	    {
	    	$order_id = $post_params['order_id'] ?? 0;

	    	$selector = array(
				'suborder' => array( 'select' => array('suborder_id', 'order_status_id')
			));
			
			$result = OrderInfo::getOrderInfo($this->db, $post_params['order_id'], '', $selector);

			$response = array();

			if(!empty($result['suborder'])) 
			{
				foreach ($result['suborder'] as $key => $value) 
				{
					$url = HTTPS_CATALOG .'index.php?route=api/order/history&'. http_build_query($value);
					
					$post_params['suborder_id'] = $value['suborder_id'];
					$post_params['order_status_id'] = $value['order_status_id'];

					/*Curl request to update Order History */
						$ch = curl_init($url);
				        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_params));
				        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
				        $response = curl_exec($ch);
				        if(curl_error($ch)){
				            $response['error'] = curl_error($ch); 
				        }
				        curl_close($ch);
				}
			}
	       return $response;
	    }

		/*
		 * @method: createLazypayTransactionLog- create LazyPay transaction log entry
		 * @params: data (key => value) transaction data
		 * @return: log id
		 * @author: MSA, July 2018
		 */
		public function createLazypayTransactionLog(array $data): int {
			$fields = array('url', 'request', 'order_no', 'order_id', 'request_type', 'credit_note_id' ,'transaction_id', 'transaction_amount', 'buyer_registration_number');
			$sql = "INSERT INTO " . DB_PREFIX . "transaction_logs SET 
							date_added=NOW(), date_modified=NOW(), type='LAZYPAY' ";
			foreach ($fields as $key) {
				if (isset($data[$key])) {
					$sql .= ", " . $key . "='" . $this->db->escape($data[$key]) . "' ";
				}
			}
			$sql .= ", status='NEW'";
			//echo $sql; die;
			$this->db->query($sql);
			$log_id = $this->db->getLastId();
			
			return (int)$log_id;
		}
		
		/*
		 * @method: createLazypayTransactionLog- update LazyPay transaction log entry
		 * @params: data (key => value) transaction data, log id
		 * @author: MSA, July 2018
		 */
		public function updateLazypayTransactionLog(int $log_id, array $data): bool {
			$fields = array('response', 'status', 'message', 'lpTxnId');
			$sql = "UPDATE " . DB_PREFIX . "transaction_logs 
						SET date_modified=NOW() ";
			foreach ($fields as $key) {
				if (isset($data[$key])) {
					$sql .= ", " . $key . "='" . $this->db->escape($data[$key]) . "' ";
				}
			}
			
			$sql .= " WHERE log_id='" . (int)$log_id . "'";
			if ($this->db->query($sql)) {
				return true;
			}
		
			return false;
		}
		/*
		 * @method: updateLazypayTransactionLogStatusByOrderNo- update lazy transaction log entry
		 * @params: order_id
		 * @params: request_type
		 * @author: MSA, 1 Feb 2019
		 */
		public function updateLazypayTransactionLogStatusByOrderId(string $order_id, string $request_type='', string $section='') {
			$fields = array('url', 'request', 'response', 
									'order_id', 'order_no', 'request_type', 
									'credit_note_id' ,'transaction_amount', 
									'transaction_id', 'lpTxnId', 
									'buyer_registration_number','message'
								);

			if($section == 'order_history')
			{
				$sql = "SELECT 
						* 
						FROM " . DB_PREFIX . "transaction_logs 
						WHERE
							request_type = 'Purchased'
							AND
							order_id='" . $this->db->escape($order_id) . "' 
							AND 
							type='LAZYPAY' 
						Order by log_id 
						";
				$result = $this->db->query($sql);			
				if($result->num_rows) {
					$log_data = $result->row;
					$log_data['request_type'] 	= $request_type;
					$sql = "INSERT INTO " . DB_PREFIX . "transaction_logs SET
							date_added = NOW(), date_modified = NOW(), type = 'LAZYPAY'	
					";
					foreach ($fields as $key) {
						if (isset($log_data[$key])) {
							$sql .= ", " . $key . "='" . $this->db->escape($log_data[$key]) . "' ";
						}
					}
					$sql .= ", status='NEW'";
					$this->db->query($sql);
				}		
			}

			if($section == 'generate_cn' || $section == 'cancel_cn')
			{
				$sql = "SELECT 
						* 
						FROM " . DB_PREFIX . "transaction_logs 
						WHERE
							request_type = 'Purchased'
							AND
							order_id='" . $this->db->escape($order_id) . "' 
							AND 
							type='LAZYPAY' 
						Order by log_id 
						";
				$result = $this->db->query($sql);			
				if($result->num_rows) {
					$log_data = $result->row;
					$log_data['request_type'] 	= $request_type;
					$sql = "INSERT INTO " . DB_PREFIX . "transaction_logs SET
							date_added = NOW(), date_modified = NOW(), type = 'LAZYPAY'	
					";
					foreach ($fields as $key) {
						if (isset($log_data[$key])) {
							$sql .= ", " . $key . "='" . $this->db->escape($log_data[$key]) . "' ";
						}
					}
					$sql .= ", status='NEW'";
					$this->db->query($sql);
				}		
			}
		
		}

		
		/*
		 * @method: getLazyPayTransactionLogUsingOrderNo- get LazyPay transactions happened for an order
		 * @params: order number, 
		 * @params: request_type (optional) possible values: ‘Purchased’, ‘Delivered’, ‘Cancelled’, ‘Return’ , 'GetOrderStatus', 'GetOTBL'
		 * @author: MSA, July 2018
		 */
		public function getLazypayTransactionLogUsingOrderNo(string $order_no, string $request_type=''): array {
			$sql = "SELECT  
							log_id, 
							request_type, 
							transaction_amount, 
							date_added, 
							date_modified, 
							transaction_id,
							credit_note_id,
							lpTxnId, 
							buyer_registration_number, 
							status, 
							message, 
							response, 
							order_no
					FROM " . DB_PREFIX . "transaction_logs 
					WHERE 
						type = 'LAZYPAY'
						AND
						order_no='" . $this->db->escape($order_no) ."'
					";
			if (!empty($request_type)) {
				$sql .= " AND request_type='" . $this->db->escape($request_type) . "'";
				$sql .= " group by '" . $this->db->escape($request_type) . "'";
			}
			$result = $this->db->query($sql);
			if ($result->num_rows) {
				return $result->rows;
			}
			
			return array();
		}
		

		public function getLazypayTransactionLogUsingLogId(string $log_id): array {
			$sql = "SELECT  log_id, 
							type,
							url,
							request,
							response,
							order_id,
							order_no,
							request_type,
							credit_note_id,
							transaction_amount,
							transaction_id,
							lpTxnId,
							buyer_registration_number,
							status,
							message	
					FROM " . DB_PREFIX . "transaction_logs 
					WHERE 
						log_id='" . $this->db->escape($order_no) ."'
					";
			$result = $this->db->query($sql);
			if ($result->num_rows) {
				return $result->rows;
			}
			
			return array();
		}


		/*
		 * @method: getLazyPayStatsOfOrder- get LazyPay transactions stats for an order
		 * @params: order number, 
		 * @return: purchased_amount, delivered_amount, cancelled_amount, and return_amount
		 * @author: MSA, July 2018
		 */
		public function getLazypayStatsOfOrder(string $order_no): array {
			$sql = "SELECT 
    						SUM(IF(amount > 0, amount, 0)) as purchased_amount,
    						SUM(IF(amount > 0, amount, 0)) as delivered_amount,
    						SUM(IF(amount < 0, amount, 0)) as refund_amount
  						FROM " . DB_PREFIX . "order_payment 
 							WHERE 
 								order_no='" . $this->db->escape($order_no) . "' 
 					";
			//echo $sql; die;
			$result = $this->db->query($sql);
			if ($result->num_rows) {
				return $result->row;
			}
			
			return array(
				'purchased_amount' 	=> 0,
				'delivered_amount' => 0,
				'refund_amount' => 0
			);		
		}

		/*
		 * @method: insertDirectCustomerPaymentDetailsIntoDb- insert lazypay payment direct in order payment db table
		 * @params: response  
		 * @author: MSA, Jan 2019
		 */
	    public function insertDirectCustomerPaymentDetailsIntoDb($response) 
	    {
	        $payable_amt    = 0;
	        $paid_amt       = (float)$response['amount'];
	       
	        //Define data array 
	        $data = array(); 
	        $data['order_id']           = (int)$response['order_id'];
	        $data['merchant_txn_id']    = $response['merchant_txn_id'];
	        $data['order_no']           = $response['order_no'];
	        $data['txn_status']         = $response['txn_status'];
	        $data['payment_mode']       = $response['paymentMode'];   
	        $data['amount']             = (float)$paid_amt; 
	        $data['txn_date_time']      = date('Y-m-d: h:i:s');
	        $data['date_added']         = 'NOW()';
	        $data['payment_gateway']    = $response['payment_gateway'];
	        $data['successfull']        = '1';
	        $data['reference']          = $response['reference'];
	        $data['payment_link']       = 'Customer Direct Payment';
	        $data['json_format']        = $response['json_response'];
	        $data['user_id']            = '0';
					$valid_insertion = 1;
					// check if row is already there with same information
					if($data['amount'] > 0) {
						$sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
								WHERE 
								merchant_txn_id ='". $this->db->escape($data['merchant_txn_id']) ."' AND
								successfull = '1' AND
								amount = '".$this->db->escape($data['amount'])."' AND
								payment_gateway = 'lazypay'";
						$query_result = $this->db->query($sql);
						if($query_result->num_rows) {
							$valid_insertion = 0;
						}
					}
					if($valid_insertion && $data['order_id'] != 0) {
	        			OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table
					}
	    }

		
		/*
		 * @method: getSubOrderWiseAmount- get LazyPay amount distributed among suborders 
		 * @params: order id, order payment data
		 * @return: suborder wise amount
		 * @author: MSA, July 2018
		 */
		public function getSubOrderWiseAmount(int $order_id, array $payment_data): array {
			$order_no = $this->__getOrderNoUsingOrderId($order_id);
			$purchase_records = $this->getLazypayTransactionLogUsingOrderNo($order_no, 'Purchased');
			if (empty($purchase_records)) return array();
			
			$purchase_data = $purchase_records[0];
			
			$advance_vouchers = AdvanceVoucherLib::getActiveAdvanceVouchersWithPayment($this->db, $order_id, $payment_data['payment_id']);
			
			$suborder_wise_amount = array();
			
			foreach ($advance_vouchers as $advance_voucher) {
				$suborder_wise_amount[$advance_voucher['suborder_id']] = $advance_voucher['value'];
			}
			
			return $suborder_wise_amount;
		}
		
		/* private method to get payment data for LazyPay payment */
		private function __getLazypayPaymentData(string $order_no): array {
			$sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
					WHERE 
					order_no ='". $this->db->escape($order_no) ."' AND
					successfull = '1' AND
					payment_gateway = 'Lazypay'";
			$query_result = $this->db->query($sql);
			if($query_result->num_rows) {
				return $query_result->row;
			}
			
			return array();
		}
		
		/* private method to get payment amount against an order except LazyPay payment amount */
		private function __getExceptLazypayPaymentAmount(string $order_no): array {
			$sql = "SELECT 
			          SUM(IF(amount >0, amount, 0)) as amount,
			          SUM(IF(amount < 0, amount, 0)) as refund
					FROM ".DB_PREFIX."order_payment				   
					WHERE 
					 successfull = 1
					   AND bank_transfer_mode NOT IN ('cheque_deposited', 'cheque_failed')
					   AND order_no ='". $this->db->escape($order_no) ."'
						 AND payment_gateway != 'lazypay'
				   ";

			$result = $this->db->query($sql);
			$data = array();
			$data['amount'] = (float)$result->row['amount'];
			$data['refund'] = (float)$result->row['refund'];
			return $data;
		}
		
		/* 
			private method to update order payment amount according to lazypay cancel transactions
		 */
		private function __updateOrderPaymentForCancelLazypayOrder(array $data)
		{
			/* -- Order payment table entries for lazypay cancel order -- */
		}

		/* private method to update order payment amount according to LazyPay transactions
			we calculate the amount which will be recieved from LazyPay on the basis of purchased amount 
			and cancelled amount 
		 */
		private function __updateLazypayPaymentAmountAccordingLazypayTransactions(string $order_no): bool {

			$payment_data = $this->__getLazypayPaymentData((string)$order_no);
			
			if(empty($payment_data)) {
				return false;
			}

			$lazypay_stats = $this->getLazypayStatsOfOrder($order_no);

			if (empty($lazypay_stats['purchased_amount'])) {
				return false;
			}
			
			$current_lazypay_order_amount = $lazypay_stats['purchased_amount'] - $lazypay_stats['refund_amount'];
			//$current_lazypay_order_amount =  sprintf("%.2f", $current_lazypay_order_amount);
			
			if ($payment_data['amount'] == $current_lazypay_order_amount) {
				return true;
			}
			
			$sql = "UPDATE ". DB_PREFIX . "order_payment 
						SET 
							amount = '" . (float)$current_lazypay_order_amount . "'
						WHERE 
							order_no ='". $this->db->escape($order_no) ."' AND
							payment_id = '" . (int)$payment_data['payment_id'] . "' AND
							payment_gateway = 'lazypay'";
					
			$query_result = $this->db->query($sql);

			if($query_result) {
				return true;
			}
			
			return false;
		}
		
		/*
		 * @method: __canShowLazyPayActions- method to check whether should show order action for lazypay or not ( in lazypay order panel)
		 * Current Logic(July 2018): if all the suborder of an order are completed(i.e. either cancelled or delivered), 
		 		then only we will show lazypay actions like 'Mark Deliver to Lazypay' or 
				'Mark Cancel to Lazypay' in Lazypay panel 
		 * @params: order id 
		 * @return: true or false
		 * @author: MSA, July 2018
		 */
		private function __canShowLazypayActions(int $order_id): bool {
			$sql = "SELECT order_status_id FROM " . DB_PREFIX . "suborder WHERE order_id = '" . (int)$order_id . "'";
			$query_result = $this->db->query($sql);
			
			if (!$query_result->num_rows) {
				return false;
			}
			
			$suborders = $query_result->rows;
			
			/* Currenly(July 2018) assuming that suborder is completed if order_status_id is any of 2, 5, 8, or 15.
				This assumption can become wrong in future if meaning of these order_status_ids get changed. 
				So if any changes happen in order status need to review this code.
			*/
			foreach($suborders as $suborder) {
				if (
					!in_array($suborder['order_status_id'], array(
						2 /*Cancelled*/, 
						8 /*Failed*/, 
					))
				) {
					return false;
				}
			}
			
			return true;
		}
		
		/* private method to get order no using order id */
		private function __getOrderNoUsingOrderId(int $order_id): string {
			$sql = "SELECT order_no FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "' LIMIT 1";
			$result = $this->db->query($sql);
			if ($result->num_rows) {
				return (string)$result->row['order_no'];
			}
			
			return '0';			
		}
		
		/* private method to get order total using order id */
		private function __getOrderTotal(int $order_id): float {
			$total = 0.0;
			$selector = array(
				'suborder' => array( 'select' => array('suborder_id', 'order_status_id')
			));
			$result = OrderInfo::getOrderInfo($this->db, $order_id, '', $selector);

			foreach ($result['suborder'] as $suborder_id => $value) {
				//Check for suborder must not be cancelled
				if($value['order_status_id'] != 2){
					$this->db->query("CALL calculateTotalInvoiceAmount('" . $this->db->escape($suborder_id) . "', '" . (int)1 . "', @total_invoice_amount)");
					$suborder_invoice_total = (float) $this->db->query("SELECT @total_invoice_amount")->row['@total_invoice_amount'];
					$total += $suborder_invoice_total;
				}
			}

			return $total;			
		}
		
		/*
		 * @method: getLazyPayActions- get LazyPay actions for an order 
		 * Possible LazyPay actions: 1. Mark deliver to LazyPay, 2. Mark Cancel to LazyPay, and 3. Mark Return to LazyPay
		 * complete logic to calculate the amount for above actions is written in this method.
		 * @params: order id
		 * @return: LazyPay actions array
		 * @author: MSA, Jan 2018
		 */
		public function getLazypayActions(int $order_id): array {
			// MSA, 02/11/2018: For now disabling the actions buttons
			
			$lazypay_actions = array();

			$order_no = $this->__getOrderNoUsingOrderId($order_id);
			$purchase_records = $this->getLazypayTransactionLogUsingOrderNo($order_no, 'Purchased');

			if (!empty($purchase_records)) { 
				// order is not placed on lazypay, so return empty array
				$purchase_data = $purchase_records[0];
			
				$can_show_lazypay_action = $this->__canShowLazypayActions($order_id);
				if (!$can_show_lazypay_action) {
					// can't show LazyPay actions for this order, so return empty array
					return array();
				}

				$order_delivered_amount = 0.00;
				$order_cancelled_amount = 0.00;
				
				$lazypay_payment_data = $this->__getLazypayPaymentData((string)$order_no);

				if (empty($lazypay_payment_data)) {
					return array();
				}

				$lazypay_stats_of_order = $this->getLazypayStatsOfOrder($order_no);
				
				$amount_available_to_take_action = $lazypay_stats_of_order['purchased_amount'] - ( - ($lazypay_stats_of_order['refund_amount']) );

				$amount_to_be_cancelled_to_lazypay =  sprintf("%.2f", $amount_available_to_take_action);
				
				$refunded_amount_over_lazypay = $lazypay_stats_of_order['refund_amount'];

				if ($amount_to_be_cancelled_to_lazypay > 0) {
					$lazypay_actions['cancel'][] = array(
						'order_no' => $order_no,
						'total' => $amount_to_be_cancelled_to_lazypay,
						'detail' => 'Order no - ' . $order_no,
						'text' => 'Mark Refund On Lazypay',
						'is_link' => 1
					);
				}else if($refunded_amount_over_lazypay){
					$lazypay_actions['cancel'][] = array(
						'order_no' => $order_no,
						'total' => $amount_to_be_cancelled_to_lazypay,
						'detail' => 'Order no - ' . $order_no,
						'text' => 'Refunded Over Lazypay',
						'is_link' => 0
					);
				}

			}


			// 04-09-2018 commenting return action for now
			/*
			$return_records = $this->getLazypayTransactionLogUsingOrderNo($order_no, 'Return');
			$return_refund_records = $this->getLazypayTransactionLogUsingOrderNo($order_no, 'Refund');
			$refunded_credit_notes = array();
			if(!empty($return_refund_records)) {
				$refunded_credit_notes = array_column($return_refund_records, 'credit_note_id');
			}
			
			if (!empty($return_records)) { 

				$lazypay_stats_of_order 	= $this->getLazypayStatsOfOrder($order_no);
				$credit_notes 				= CreditNote::getCreditNotesOfAnOrder($this->db, $order_id);
				$net_refundable 			= $credit_notes[0]['net_refundable'] ?? $lazypay_stats_of_order['refund_amount'];
				$delivered_amount_till_now 	= $lazypay_stats_of_order['refund_amount'];

				$credit_note_names = array();
				foreach ($return_records as $return_record) {
					if ($return_record['status'] == 'SUCCESS') {
						$credit_note_names[$return_record['transaction_id']] = $return_record['transaction_amount'];
					}
				}

				if (!empty($credit_notes)) {
					foreach ($credit_notes as $credit_note) {
						$credit_note_name = $credit_note['credit_note_prefix'] . $credit_note['credit_note_no'];
						
						if (isset($credit_note_names[$credit_note_name])) {
							continue;
						}
						
						$returnable_amount = $credit_note['net_refundable'];
						if ($returnable_amount > $delivered_amount_till_now) {
							$returnable_amount = $delivered_amount_till_now;
						}
						$returnable_amount = sprintf("%.2f", $returnable_amount);
						
						$text = 'Mark Return Refund <br/>(' . $credit_note_name . ')';
						$is_link = 1;
						if(in_array($credit_note['credit_note_id'], $refunded_credit_notes)) {
							$text = 'Refunded On Lazypay <br/>(' . $credit_note_name . ')';
							$is_link = 0;
						}
						
						$lazypay_actions['return'][] = array(
							'order_no' 	=> $order_no,
							'total' 	=> $returnable_amount,
							'detail' 	=> $credit_note_name,
							'credit_note_id' => $credit_note['credit_note_id'],
							'text' 		=> $text,
							'is_link' 	=>  $is_link,
						);
					}
				}
			} 
			*/
			return $lazypay_actions;
		}

		public function lazypay_validate(string $value, string $type) {

		/*Remove special chars from value */	
			$value = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($value));

			switch ($type) {
				case 'phone':
					/*Phone No. field to be only numeric and length 10 digits */
						$value = preg_replace('/[^0-9]/', '', $value);
						if(strlen($value)>10) {
							$start = strlen($value) - 10;
							$value = substr($value, $start);
						}
					break;
				case 'email':
					/*  Email id field should contain A-Z, a-z, 0-9 and _ (underscore) . (dot) AND @ 
                        remove all other special chars from email id
					*/
						$value = preg_replace('/[^A-Za-z0-9._@]/', '', $value); 
					break;
				case 'name':
					/*
						Name field to be only alphabets
					*/
						$value = preg_replace('/[^A-Za-z]/', '', $value); 
					break;
				default:
					# code...
					break;
			}

			return $value;

		}

	/**
     * Public method to update wsb_credit details for given customer_id from customer_credits tab
     * @param : $data Array
     * @author:Nishu, 2019
	*/
	public function updateCreditDetails($data){
		if(!empty($data['customer_id'])){
			//Set Data to add into admin_change_log
            $admin_change_data = array();
            $admin_change_data['table_id'] = (int)$data['customer_id'];
            
			$select_sql = "
                            SELECT
                            	credit_status,
                            	lazypay_email,
                            	lazypay_mobile
                            FROM
                            	".DB_PREFIX."customer_credit
                            WHERE 
                            	customer_id = ". (int)$data['customer_id'] ."
                            	AND type    = 'Lazypay' 
			              ";
			$result = $this->db->query($select_sql);

			if($result->num_rows > 0){
					$is_updatable = 0;
					$old_credit_status   = $result->row['credit_status'] ?? '';
					$credit_status       = $data['credit_status'] ?? '';

					$old_lazypay_mobile  = $result->row['lazypay_mobile'] ?? '';
					$lazypay_mobile      = $data['lazypay_mobile'] ?? '';

					$old_lazypay_email   = $result->row['lazypay_email'] ?? '';
					$lazypay_email       = $data['lazypay_email'] ?? '';

					if($credit_status <> $old_credit_status){
						$is_updatable = 1;
						$admin_change_data['old_value']  = (int)$old_credit_status;
	                	$admin_change_data['new_value']  = (int)$credit_status;
	                	$admin_change_data['field_name'] = 'credit_status';
	                	$this->logAdminChangeLog($admin_change_data);
					}
					if($lazypay_mobile <> $old_lazypay_mobile){
						$is_updatable = 1;
						$admin_change_data['old_value']  = $old_lazypay_mobile;
	                	$admin_change_data['new_value']  = $lazypay_mobile;
	                	$admin_change_data['field_name'] = 'lazypay_mobile';
	                	$this->logAdminChangeLog($admin_change_data);
					}

					if($lazypay_email <> $old_lazypay_email){
						$is_updatable = 1;
						$admin_change_data['old_value']  = $old_lazypay_email;
	                	$admin_change_data['new_value']  = $lazypay_email;
	                	$admin_change_data['field_name'] = 'lazypay_email';
	                	$this->logAdminChangeLog($admin_change_data);
					}

					if($is_updatable == 1){
						$update_sql = "
	                                UPDATE
	                                	".DB_PREFIX."customer_credit
	                                SET
	                                	credit_status = ".(int)$data['credit_status'].",
	                                	lazypay_mobile = '".$this->db->escape($data['lazypay_mobile'])."',
	                                	lazypay_email = '".$this->db->escape($data['lazypay_email'])."'
	                                WHERE
	                                	customer_id = ". (int)$data['customer_id'] ."
					              ";
						$this->db->query($update_sql);
					}
			}else{

				$credit_status       = $data['credit_status'] ?? '';
				$lazypay_mobile      = $data['lazypay_mobile'] ?? '';
				$lazypay_email       = $data['lazypay_email'] ?? '';

				$admin_change_data['new_value']  = (int)$credit_status;
            	$admin_change_data['field_name'] = 'credit_status';
            	$this->logAdminChangeLog($admin_change_data);

            	$admin_change_data['new_value']  = $lazypay_mobile;
            	$admin_change_data['field_name'] = 'lazypay_mobile';
            	$this->logAdminChangeLog($admin_change_data);

            	$admin_change_data['new_value']  = $lazypay_email;
            	$admin_change_data['field_name'] = 'lazypay_email';
            	$this->logAdminChangeLog($admin_change_data);

				$insert_sql = "
                                INSERT INTO
                                	".DB_PREFIX."customer_credit
                                SET
                                	customer_id    = ".(int)$data['customer_id'].",
                                	type           = 'Lazypay',
                                	credit_status  = ".(int)$data['credit_status'].",
                                	lazypay_mobile = '".$this->db->escape($data['lazypay_mobile'])."',
                                	lazypay_email  = '".$this->db->escape($data['lazypay_email'])."'
				              ";
				             
				$this->db->query($insert_sql);
			}
		}
	}

	/**
	 * Public method to log admin change log
	 * @author : MSA Feb 2019
	*/
	public function logAdminChangeLog($admin_change_data){
		/////////////// Insert a row in customer change log/////////////////////
          
        $admin_change_data['user_id']       = $this->user->getId() ?? 0;
        $admin_change_data['name']          = 'Customer Credits';
        $admin_change_data['username']      = $this->user->getUserName()["name"] ?? '';
        $admin_change_data['table_name']    = 'oc_customer_credit';
        $admin_change_data['source_field']  = 'customer_credits';
        $admin_change_data['ref_url']       = 'sale/customer_credits';
        $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
        $admin_change_data['ip_address']    = $_SERVER['REMOTE_ADDR'];
        $admin_change_data['file_location'] = 'sale/customer_credits';
        $admin_change_data['user_type']     = 'Administrator';

        //Call dynamic static function for entry into admin change log
        CommonLib::addAdminChangeLog($this->db, $admin_change_data);
	}

	/**
	 * Public method to check order PreAuth Release action
	 * @author : MSA Feb 2019
	*/
	public function isLazyPayPreAuthReleased(int $order_id): int
	{
		$sql = "
				SELECT 
					log_id
				FROM 
					".DB_PREFIX."transaction_logs
				WHERE
					order_id = '".(int) $order_id."'
					AND
					request_type = 'ReleasedPreAuth' AND status = 'SUCCESS'
			";	
		$result = $this->db->query($sql);
		if($result->num_rows) {
			return 1;
		}else{
			return 0;
		}
	}

	/**
	 * Public method to check order PreAuth Purchased action
	 * @author : MSA Feb 2019
	*/
	public function isLazyPayPreAuthPurchased(int $order_id)
	{
		$sql = "
				SELECT
					log_id
				FROM 
					".DB_PREFIX."transaction_logs
				WHERE
					order_id = '".(int) $order_id."'
					AND
					request_type = 'Purchased' AND ( status = 'SUCCESS' OR status = 'COMPLETE')
			";
			$result = $this->db->query($sql);
			if($result->num_rows) {
				return 1;
			}else{
				return 0;
			}
	}

	public function lazyPayFreezedPreAuthAmount(int $order_id): float
	{
		$sql = "
				SELECT 
					log_id,
					transaction_amount
				FROM 
					".DB_PREFIX."transaction_logs
				WHERE
					order_id = '".(int) $order_id."'
					AND
					request_type = 'AuthorizedPreAuth'
					AND
					status = 'SUCCESS'
				LIMIT 1
			";	
		$result = $this->db->query($sql);
		if($result->num_rows) {
			return sprintf("%.2f", $result->row['transaction_amount']);
		}else{
			return 0;
		}
	}

	


}
