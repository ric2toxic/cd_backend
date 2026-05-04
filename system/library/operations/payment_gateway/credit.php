<?php
require_once(DIR_SYSTEM.'library/operations/payment_gateway/payment_gateway_base.php');

class CreditPayment extends PaymentGatewayBase {

	private $_partner_id;
	private $_partner_key;

	public function __construct($registry) {
		parent::__construct($registry);
        $this->paymentgateway = 'credit';
		
	    $this->_partner_id  = $this->config->get('credit_neo_partner_id');
	    $this->_partner_key = $this->config->get('credit_neo_partner_key');
	}

    public function sendRequest($gateway_url, $post_data = ''){

        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_URL, $gateway_url);
        if(!empty($post_data)){
            @curl_setopt($ch, CURLOPT_POST, true);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_HEADER, false);
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
	 * @method: getTransactionStatus- get neogrowth status of an order
	 * @params: order_no
	 * @return: response 
	 * @author: Devendra, July 2018
	 */
	public function getTransactionStatus(string $order_no): array {
		// first we check in database, 
		// if order status for this order is already their then we will return the same, 
		// otherwise we will call neo-growth api
		$result = $this->getNeoGrowthTransactionLogUsingOrderNo($order_no, 'GetOrderStatus');
		if (!empty($result)) {
            $order_status_result = json_decode( ($result[0]['response'] ?? ''), true );
            
            if ( empty($order_status_result) || !is_array($order_status_result) ) {
                return array();
            }
            
            return $order_status_result;
		}
				
        $neo_url = NEOPAYLATER_TRANSACTION_STATUS_API . '?partner_id='.$this->_partner_id.'&order_id='.$order_no;
        // create log
        $log_data = array();
        $log_data['url'] = $neo_url;
        $log_data['request_type'] = 'GetOrderStatus';
        $log_data['order_no'] = $order_no;
        $log_data['request'] = 'partner_id=' . $this->_partner_id . '&order_id=' . $order_no;
        $log_id = $this->createNeoGrowthTransactionLog($log_data);
				
        // send request
        $result = $this->sendRequest($neo_url);
        $response = json_decode($result,true);
        
        // update the logs
        $log_data = array(
            'response' => $result,
            'status' => strtoupper($response['status']),
            'message' => $response['message']
        );
        $this->updateNeoGrowthTransactionLog($log_id, $log_data);
				
        // check neogrowth purchase transaction sttus, if status is not equal to success, 
        // then update the status according to the above response 
        $result = $this->getNeoGrowthTransactionLogUsingOrderNo($order_no, 'Purchased');
        if (!empty($result)) {
            $order_result = $result[0];
            if ($order_result['status'] !== 'SUCCESS') {
                $status = ($response['code'] == "ss-202") ? "SUCCESS" : "FAILED";
                $this->updateNeoGrowthTransactionLog($order_result['log_id'], array('status' => $status));
            }
        }
        
        if ( empty($response) || !is_array($response) ) {
            return array();
        }

        return $response;

	}

	/*
	 * @method: getCustomerNeoGrowthAccountNumber
	 * @params: customer id
	 * @return: account number 
	 * @author: Devendra, July 2018
	 */
	public function getCustomerNeoGrowthAccountNumber(int $customer_id): string {
		$sql = "SELECT neogrowth_account_number 
						FROM " . DB_PREFIX . "customer_credit
						WHERE 
							customer_id = '" . (int)$customer_id  ."' 
							AND
							type = 'Neogrowth'
						LIMIT 1";
		$query = $this->db->query($sql);

		if( $query->num_rows ){
				return (string)$query->row['neogrowth_account_number'];
		} else {
				return '';
		}
	}
	
	/*
	 * @method: getCustomerRegistrationNumber
	 * @params: customer id
	 * @return: buyer registration number
	 * @author: Devendra, July 2018
	 */
	public function getCustomerRegistrationNumber(int $customer_id): string {
	    $sql = "SELECT neogrowth_registration_number 
	            FROM " . DB_PREFIX . "customer_credit
	            WHERE customer_id = '" . (int)$customer_id  ."' AND type  = 'Neogrowth'  LIMIT 1";
	    $query = $this->db->query($sql);

	    if( $query->num_rows ){
	        return (string)$query->row['neogrowth_registration_number'];
	    } else {
	        return '';
	    }
	}

	/**
	 * @method: Method to get NeoGrowth's details for given customer_id
	 * @param : $customer id
	 * @return: Array
	 * @author: Nishu, Feb 2019
	 */
	public function getNeoGrowthDetailsByCustomerId(int $customer_id): array {
		$data = array();
		if(!empty($customer_id)){
		    $sql = "SELECT 
		    			customer_id,
		    			credit_status,
		    			neogrowth_registration_number ,
		    			neogrowth_account_number
		            FROM 
		            	" . DB_PREFIX . "customer_credit
		            WHERE 
		            	customer_id = '" . (int)$customer_id  ."'
		            		AND 
		            	type    = 'Neogrowth' 
		            	LIMIT 1";
		    $query = $this->db->query($sql);

		    if( $query->num_rows > 0 ){
		        $data = $query->row;
		    } 
		}
		return $data;
	}
	
	/**
	 * @method: Method to get all credit details for given customer_id(i.e. Neogrowth or Lazypay)
	 * @param : $customer id
	 * @return: Array
	 * @author: Nishu, Feb 2019
	 */
	public function getCreditDetailsByCustomerId(int $customer_id): array {
		$data = array();
		$data['neogrowth'] = array();
		$data['lazypay']   = array();
		if(!empty($customer_id)){
		    $sql = "SELECT 
		    			customer_id,
		    			type,
		    			credit_status,
		    			neogrowth_registration_number ,
		    			neogrowth_account_number,
		    			lazypay_email,
		    			lazypay_mobile
		            FROM 
		            	" . DB_PREFIX . "customer_credit
		            WHERE 
		            	customer_id = '" . (int)$customer_id  ."'";
		    $query = $this->db->query($sql);

		    if( $query->num_rows > 0 ){
		        foreach ($query->rows as $key => $value) {
		        	if($value['type'] == 'Neogrowth'){
		        		$data['neogrowth']['credit_status']                 = $value['credit_status'];
		        		$data['neogrowth']['neogrowth_registration_number'] = $value['neogrowth_registration_number'];
		        		$data['neogrowth']['neogrowth_account_number']      = $value['neogrowth_account_number'];
		        	}else if($value['type'] == 'Lazypay'){
		        		$data['lazypay']['credit_status']  = $value['credit_status'];
		        		$data['lazypay']['lazypay_email']  = $value['lazypay_email'];
		        		$data['lazypay']['lazypay_mobile'] = $value['lazypay_mobile'];
		        	}else if($value['type'] == 'RBL'){
		        		$data['Rbl']['credit_status']  = $value['credit_status'];
		        	}

		        }
		    } 
		}
		return $data;
	}
	
	/*
	 * @method: getCustomerNeoGrowthAccountNumber
	 * @params: customer id
	 * @return: account number
	 * @author: Devendra, July 2018
	 */
	public function getCustomerNeoGrowthLimit(int $customer_id): float {
	    // get account number
	    $account_number = $this->getCustomerNeoGrowthAccountNumber($customer_id);
	    // if empty return zero
	    if (empty($account_number)) return 0;

	    $url = NEOPAYLATER_GET_OTBL . '?partner_id=' . $this->_partner_id . '&account_number=' . $account_number;
			// create log
			$log_data = array();
			$log_data['url'] = $url;
			$log_data['request_type'] = 'GetOTBL';
			$log_data['buyer_registration_number'] = $this->getCustomerRegistrationNumber($customer_id);
			$log_data['request'] = 'partner_id=' . $this->_partner_id . '&account_number=' . $account_number;
			$log_id = $this->createNeoGrowthTransactionLog($log_data);
			
	    $result = $this->sendRequest($url);
			$response = json_decode($result,true);
			
			$log_data = array(
        'response' => $result,
        'status' => strtoupper($response['status']),
        'message' => ($response['status'] != 'success') ? $response['message'] : 
									('Account Status: '. $response['message']['account_status'] . '| OTBL: '. $response['message']['otbl'])
      );
      $this->updateNeoGrowthTransactionLog($log_id, $log_data);

	    // if failed, return zero
	    if ($response['status'] != 'success') {
	        return 0;
        }

        // if neo-growth account is in-active, then return zero
        if ($response['message']['account_status']!== 'ACTIVE') {
	        return 0;
        }

        $otbl = (float)$response['message']['otbl'];
	    // update the otbl in database
	    $this->updateCreditBalance($customer_id, $otbl);

	    return (float)$otbl;
    }

    public function updateCreditBalance(int $customer_id, float $balance): bool {
        $sql = "UPDATE " . DB_PREFIX . "customer_credit 
                SET credit_balance = '" . $this->db->escape($balance) ."' 
                WHERE customer_id = '" . (int)$customer_id  ."' AND type = 'Neogrowth'  ";
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
		 * @method: cancelOrder- whenever a suborder of a order( which is placed on credit) got cancelled, we need to notify neogrowth 
		 * @params: order_data = array(order_no, detail, customer_id, total)
		 * @return: neogrowth response 
		 * @author: Devendra, July 2018
		 */
		public function cancelOrder(array $order_data): array {
			$data = array();
			$data['partner_id'] = $this->_partner_id;
			$data['order_id'] = $order_data['order_no'];
			$data['buyer_registration_number'] = $this->getCustomerRegistrationNumber($order_data['customer_id']);
			$data['transaction_amount'] = sprintf("%.2f", $order_data['total']);
			
			$request_str = $data['partner_id'] . ',' . $data['order_id'] . ',' . $data['buyer_registration_number'] . ',' .
											$data['transaction_amount'] . ',' . $this->_partner_key;
			$data['checksum_hash'] = $this->generateCheckSumHash($request_str, $this->_partner_key);
			
			$data['session_id'] = session_id();
			
			$data['cart_details'] = $order_data['detail'];
			$data['request_type'] = 'Cancelled';
			
			$neo_records = $this->getNeoGrowthTransactionLogUsingOrderNo($order_data['order_no'], 'Cancelled');
			$transaction_no = count($neo_records) + 1;
			$data['transaction_id'] = 'C' . $order_data['order_no'] . '-' . $transaction_no;
			$data['transaction_datetime'] = time();
			
			$domain = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');
			$callback_url = $domain . 'payment/credit/neoTransactionCallback';

			$data['success_url'] = $callback_url;
			$data['failure_url'] = $callback_url;
			
			$url = NEOPAYLATER_BUYER_TRANSACTION_API;
			$request_data_string = http_build_query($data);
			// create log
			$log_data = $data;
			$log_data['url'] = $url;
			$log_data['request'] = $request_data_string;
			$log_data['order_no'] = $data['order_id'];
			$log_id = $this->createNeoGrowthTransactionLog($log_data);
		
	    $result = $this->sendRequest($url, $request_data_string);
			
			// update the neogrowth response in logs
			$log_data = array(
				'response' => $result,
				'status' => 'FAILED' // default status, later it will be updated according to response
			);
			$this->updateNeoGrowthTransactionLog($log_id, $log_data);
			
	    $response = json_decode($result,true);
			
			if (isset($response['status'])) {
				$log_data = array(
					'status' => strtoupper($response['status']),
					'message' => isset($response['message']) ? $response['message'] : ''
				);
				$this->updateNeoGrowthTransactionLog($log_id, $log_data);
			}
			
			// now update the order payment amount according to the purchsed amount minus cancelled amount
			$this->__updateNeogrowthPaymentAmountAccordingNeogrowthTransactions($order_data['order_no']);
			
			return $response;
		}
		
		/*
		 * @method: deliverOrder- whenever a suborder of a order( which is placed on credit) got delivered, we need to notify neogrowth 
		 * @params: order_data = array(order_no, detail, customer_id, total)
		 * @return: neogrowth response 
		 * @author: Devendra, July 2018
		 */
		public function deliverOrder(array $order_data): array {
			$data = array();
			$data['partner_id'] = $this->_partner_id;
			$data['order_id'] = $order_data['order_no'];
			$data['buyer_registration_number'] = $this->getCustomerRegistrationNumber($order_data['customer_id']);
			$data['transaction_amount'] = sprintf("%.2f", $order_data['total']);
			
			$request_str = $data['partner_id'] . ',' . $data['order_id'] . ',' . $data['buyer_registration_number'] . ',' .
											$data['transaction_amount'] . ',' . $this->_partner_key;
			$data['checksum_hash'] = $this->generateCheckSumHash($request_str, $this->_partner_key);
			
			$data['session_id'] = session_id();
			
			$data['cart_details'] = $order_data['detail'];
			$data['request_type'] = 'Delivered';
			
			$neo_records = $this->getNeoGrowthTransactionLogUsingOrderNo($order_data['order_no'], 'Delivered');
			$transaction_no = count($neo_records) + 1;
			$data['transaction_id'] = 'D' . $order_data['order_no'] . '-' . $transaction_no;
			$data['transaction_datetime'] = time();
			
			$domain = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');
			$callback_url = $domain . 'payment/credit/neoTransactionCallback';

			$data['success_url'] = $callback_url;
			$data['failure_url'] = $callback_url;
			
			$url = NEOPAYLATER_BUYER_TRANSACTION_API;
			$request_data_string = http_build_query($data);
			// create log
			$log_data = $data;
			$log_data['url'] = $url;
			$log_data['request'] = $request_data_string;
			$log_data['order_no'] = $data['order_id'];
			$log_id = $this->createNeoGrowthTransactionLog($log_data);
		
			$result = $this->sendRequest($url, $request_data_string);
			
			// update the neogrowth response in logs
			$log_data = array(
				'response' => $result,
				'status' => 'FAILED' // default status, later it will be updated according to response
			);
			$this->updateNeoGrowthTransactionLog($log_id, $log_data);
			
			$response = json_decode($result,true);
			
			if (isset($response['status'])) {
				$log_data = array(
					'status' => strtoupper($response['status']),
					'message' => isset($response['message']) ? $response['message'] : ''
				);
				$this->updateNeoGrowthTransactionLog($log_id, $log_data);
			}
			
			return $response;
		}
		
	/*
	 * @method: returnOrder- whenever a return request approved for products of a order which is placed on credit, we need to notify neogrowth 
	 * @params: order_data = array(order_no, customer_id, total, detail)
	 * @return: neogrowth response 
	 * @author: Devendra, July 2018
	 */
	public function returnOrder(array $order_data): array {
			$data = array();
			$data['partner_id'] = $this->_partner_id;
			$data['order_id'] = $order_data['order_no'];
			$data['buyer_registration_number'] = $this->getCustomerRegistrationNumber($order_data['customer_id']);
			$data['transaction_amount'] = sprintf("%.2f", $order_data['total']);
			
			$request_str = $data['partner_id'] . ',' . $data['order_id'] . ',' . $data['buyer_registration_number'] . ',' .
											$data['transaction_amount'] . ',' . $this->_partner_key;
			$data['checksum_hash'] = $this->generateCheckSumHash($request_str, $this->_partner_key);
			
			$data['session_id'] = session_id();
			
			$order_returns = $this->getNeoGrowthTransactionLogUsingOrderNo($order_data['order_no'], 'Return');
			$next_return_no = count($order_returns) + 1;
			
			$data['cart_details'] = 'Order no - ' . $order_data['order_no'];
			$data['request_type'] = 'Return';
			$data['transaction_id'] = $order_data['detail']; //'R' . $order_data['order_no'] . '-' . $next_return_no;
			$data['transaction_datetime'] = time();
			
			$domain = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');
			$callback_url = $domain . 'payment/credit/neoTransactionCallback';

			$data['success_url'] = $callback_url;
			$data['failure_url'] = $callback_url;
			
			$url = NEOPAYLATER_BUYER_TRANSACTION_API;
			$request_data_string = http_build_query($data);
			// create log
			$log_data = $data;
			$log_data['url'] = $url;
			$log_data['request'] = $request_data_string;
			$log_data['order_no'] = $data['order_id'];
			$log_id = $this->createNeoGrowthTransactionLog($log_data);
		
	   		$result = $this->sendRequest($url, $request_data_string);
			
			// update the neogrowth response in logs
			$log_data = array(
				'response' => $result,
				'status' => 'FAILED' // default status, later it will be updated according to response
			);
			$this->updateNeoGrowthTransactionLog($log_id, $log_data);
			
	    	$response = json_decode($result,true);
			
			if (isset($response['status'])) {
				$log_data = array(
					'status' => strtoupper($response['status']),
					'message' => isset($response['message']) ? $response['message'] : ''
				);
				$this->updateNeoGrowthTransactionLog($log_id, $log_data);
			}
			
			return $response;
	}
		
	/*
	 * @method: createNeoGrowthTransactionLog- create neogrowth transaction log entry
	 * @params: data (key => value) transaction data
	 * @return: log id
	 * @author: Devendra, July 2018
	 */
	public function createNeoGrowthTransactionLog(array $data): int {
		$fields = array('url', 'request', 'order_no', 'request_type', 'transaction_id', 'transaction_amount', 'buyer_registration_number');
		$sql = "INSERT INTO 
					" . DB_PREFIX . "transaction_logs 
				SET 
					type = 'NEOGROWTH', 
					date_added=NOW(),
					date_modified=NOW() ";
		foreach ($fields as $key) {
			if (isset($data[$key])) {
				$sql .= ", " . $key . "='" . $this->db->escape($data[$key]) . "' ";
			}
		}
		$sql .= ", status='NEW'";
		$this->db->query($sql);
		$log_id = $this->db->getLastId();
		
		return (int)$log_id;
	}
		
	/*
	 * @method: createNeoGrowthTransactionLog- update neogrowth transaction log entry
	 * @params: data (key => value) transaction data, log id
	 * @author: Devendra, July 2018
	 */
	public function updateNeoGrowthTransactionLog(int $log_id, array $data): bool {
		$fields = array('response', 'status', 'message');
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
	 * @method: getNeoGrowthTransactionLogUsingOrderNo- get neogrowth transactions happened for an order
	 * @params: order number, 
	 * @params: request_type (optional) possible values: ‘Purchased’, ‘Delivered’, ‘Cancelled’, ‘Return’ , 'GetOrderStatus', 'GetOTBL'
	 * @author: Devendra, July 2018
	 */
	public function getNeoGrowthTransactionLogUsingOrderNo(string $order_no, string $request_type=''): array {

		$sql = "SELECT  log_id, type, request_type, transaction_amount, 					date_added, date_modified, transaction_id, 
						buyer_registration_number, status, message, response, order_no FROM " . DB_PREFIX . "transaction_logs 
					 WHERE order_no='" . $this->db->escape($order_no) ."'";
		$sql .= " AND type = 'NEOGROWTH' ";
		
		if (!empty($request_type)) {
			$sql .= " AND request_type='" . $this->db->escape($request_type) . "'";
		}
	
		$result = $this->db->query($sql);
		if ($result->num_rows) {
			return $result->rows;
		}
		
		return array();
	}
		
	/*
	 * @method: getNeoGrowthStatsOfOrder- get neogrowth transactions stats for an order
	 * @params: order number, 
	 * @return: purchased_amount, delivered_amount, cancelled_amount, and return_amount
	 * @author: Devendra, July 2018
	 */
	public function getNeoGrowthStatsOfOrder(string $order_no): array {
		$sql = "SELECT 
						SUM(IF(request_type='Purchased', transaction_amount, 0)) as purchased_amount,
						SUM(IF(request_type='Delivered', transaction_amount, 0)) as delivered_amount,
						SUM(IF(request_type='Cancelled', transaction_amount, 0)) as cancelled_amount,
						SUM(IF(request_type='Return', transaction_amount, 0)) as return_amount
						FROM 
							".DB_PREFIX."transaction_logs
						WHERE 
							order_no='" . $this->db->escape($order_no) . "' 
							AND status='SUCCESS'
							AND type = 'NEOGROWTH' ";

		$result = $this->db->query($sql);
		if ($result->num_rows) {
			return $result->row;
		}
		
		return array(
			'purchased_amount' => 0,
			'delivered_amount' => 0,
			'cancelled_amount' => 0,
			'return_amount' => 0
		);		
	}
		
	/*
	 * @method: insertNeoGrowthPaymentDetailsIntoDb- insert neogrowth payment into db
	 * @params: order id
	 * @author: Devendra, July 2018
	 */
	public function insertNeoGrowthPaymentDetailsIntoDb(int $order_id): bool {
		$order_no = $this->__getOrderNoUsingOrderId($order_id);
		$purchase_records = $this->getNeoGrowthTransactionLogUsingOrderNo($order_no, 'Purchased');
		if (empty($purchase_records)) {
			return false;
		}
		
		$purchase_data = $purchase_records[0];
		
		$paid_amount = $purchase_data['transaction_amount'];
		
		//Define data array 
		$data = array(); 
		$data['order_id']           = (int)$order_id;
		$data['order_no']           = $order_no;
		$data['txn_status']         = $purchase_data['status'];
		$data['payment_mode']       = 'credit_agency';   
		$data['amount']             = (float)$paid_amount; 
		$data['txn_date_time']      = $purchase_data['date_added'];
		$data['date_added']         = 'NOW()';
		$data['payment_gateway']    = 'neogrowth';
		$data['successfull']        = '1';
		$data['reference']          = '';
		$data['payment_link']       = 'Payment by NeoGrowth';
		$data['json_format']        = $purchase_data['response'];
		$data['user_id']            = '0';
    
		$valid_insertion = 1;
		// check if row is already there with same information
		if($data['amount'] > 0) {
			$payment_data = $this->__getNeogrowthPaymentData((string)$order_no);
			if(!empty($payment_data)) {
				$valid_insertion = 0;
			}
		}

		if($valid_insertion && $data['order_id'] != 0) {
			OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table
		}
		
		return true;
	}
		
	/*
	 * @method: getSubOrderWiseAmount- get neogrowth amount distributed among suborders 
	 * @params: order id, order payment data
	 * @return: suborder wise amount
	 * @author: Devendra, July 2018
	 */
	public function getSubOrderWiseAmount(int $order_id, array $payment_data): array {
		$order_no = $this->__getOrderNoUsingOrderId($order_id);
		$purchase_records = $this->getNeoGrowthTransactionLogUsingOrderNo($order_no, 'Purchased');
		if (empty($purchase_records)) return array();
		
		$purchase_data = $purchase_records[0];
		
		$advance_vouchers = AdvanceVoucherLib::getActiveAdvanceVouchersWithPayment($this->db, $order_id, $payment_data['payment_id']);
		
		$suborder_wise_amount = array();
		
		foreach ($advance_vouchers as $advance_voucher) {
			$suborder_wise_amount[$advance_voucher['suborder_id']] = $advance_voucher['value'];
		}
		
		return $suborder_wise_amount;
	}
		
	/* private method to get payment data for neogrowth payment */
	private function __getNeogrowthPaymentData(string $order_no): array {
		$sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
				WHERE 
				order_no ='". $this->db->escape($order_no) ."' AND
				successfull = '1' AND
				payment_gateway = 'neogrowth'";
		$query_result = $this->db->query($sql);
		if($query_result->num_rows) {
			return $query_result->row;
		}
		
		return array();
	}
		
	/* private method to get payment amount against an order except neogrowth payment amount */
	private function __getExceptNeogrowthPaymentAmount(string $order_no): array {
		$sql = "SELECT 
		          SUM(IF(amount >0, amount, 0)) as amount,
		          SUM(IF(amount < 0, amount, 0)) as refund
				FROM ".DB_PREFIX."order_payment				   
				WHERE 
				 successfull = 1
				   AND bank_transfer_mode NOT IN ('cheque_deposited', 'cheque_failed')
				   AND order_no ='". $this->db->escape($order_no) ."'
					 AND payment_gateway != 'neogrowth'
			   ";

		$result = $this->db->query($sql);
		$data = array();
		$data['amount'] = (float)$result->row['amount'];
		$data['refund'] = (float)$result->row['refund'];
		return $data;
	}
		
	/* private method to update order payment amount according to neogrowth transactions
		we calculate the amount which will be recieved from neogrowth on the basis of purchased amount 
		and cancelled amount 
	 */
	private function __updateNeogrowthPaymentAmountAccordingNeogrowthTransactions(string $order_no): bool {
		$payment_data = $this->__getNeogrowthPaymentData((string)$order_no);
		if(empty($payment_data)) {
			return false;
		}
		
		$neogrowth_stats = $this->getNeoGrowthStatsOfOrder($order_no);
		if (empty($neogrowth_stats['purchased_amount'])) {
			return false;
		}
		
		$current_neogrowth_order_amount = $neogrowth_stats['purchased_amount'] - $neogrowth_stats['cancelled_amount'];
		$current_neogrowth_order_amount =  sprintf("%.2f", $current_neogrowth_order_amount);
		
		if ($payment_data['amount'] == $current_neogrowth_order_amount) {
			return true;
		}
		
		$sql = "UPDATE ". DB_PREFIX . "order_payment 
						SET amount = '" . (float)$current_neogrowth_order_amount . "'
				WHERE 
				order_no ='". $this->db->escape($order_no) ."' AND
				payment_id = '" . (int)$payment_data['payment_id'] . "' AND
				payment_gateway = 'neogrowth'";
				
		$query_result = $this->db->query($sql);
		if($query_result) {
			return true;
		}
		
		return false;
	}
		
	/*
	 * @method: __canShowNeogrowthActions- method to check whether should show order action for neogrowth or not ( in neogrowth order panel)
	 * Current Logic(July 2018): if all the suborder of an order are completed(i.e. either cancelled or delivered), 
	 		then only we will show neogrowth actions like 'Mark Deliver to Neogrowth' or 
			'Mark Cancel to neogrowth' in Neogrowth panel 
	 * @params: order id 
	 * @return: true or false
	 * @author: Devendra, July 2018
	 */
	private function __canShowNeogrowthActions(int $order_id): bool {
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
					5 /*Complete*/, 
					8 /*Failed*/, 
					15 /*Delivered */
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
	 * @method: getNeogrowthActions- get neogrowth actions for an order 
	 * Possible Neogrowth actions: 1. Mark deliver to Neogrowth, 2. Mark Cancel to Neogrowth, and 3. Mark Return to Neogrowth
	 * complete logic to calculate the amount for above actions is written in this method.
	 * @params: order id
	 * @return: neogrowth actions array
	 * @author: Devendra, July 2018
	 */
	public function getNeogrowthActions(int $order_id): array {
		// Devendra, 02/11/2018: For now disabling the actions buttons
		return array();
		
		$order_no = $this->__getOrderNoUsingOrderId($order_id);
		$purchase_records = $this->getNeoGrowthTransactionLogUsingOrderNo($order_no, 'Purchased');
		if (empty($purchase_records)) { 
			// order is not placed on neogrowth, so return empty array
			return array();
		}
		
		$purchase_data = $purchase_records[0];
		
		$can_show_neogrowth_action = $this->__canShowNeogrowthActions($order_id);
		if (!$can_show_neogrowth_action) {
			// can't show neogrowth actions for this order, so return empty array
			return array();
		}
		
		$order_delivered_amount = 0.00;
		$order_cancelled_amount = 0.00;
		
		$neo_payment_data = $this->__getNeogrowthPaymentData((string)$order_no);
		if (empty($neo_payment_data)) {
			return array();
		}
		
		$order_total = $this->__getOrderTotal($order_id);
		$other_payment_data = $this->__getExceptNeogrowthPaymentAmount((string)$order_no);
		
		$total_payment_amount = $neo_payment_data['amount'] + $other_payment_data['amount'] - $other_payment_data['refund'];
		
		if ($total_payment_amount > $order_total) {
			$order_cancelled_amount = $total_payment_amount - $order_total;
			$order_delivered_amount = $neo_payment_data['amount'] - $order_cancelled_amount;
		} else {
			$order_delivered_amount = $neo_payment_data['amount'];
		}		
		
		$neogrowth_stats_of_order = $this->getNeoGrowthStatsOfOrder($order_no);
		
		$amount_available_to_take_action = $neogrowth_stats_of_order['purchased_amount'] - $neogrowth_stats_of_order['delivered_amount'] - $neogrowth_stats_of_order['cancelled_amount'];
		
		// amount to be delivered by neogrowth is total delivered amount of order minus amount already delivered by neogrowth.
		$amount_to_be_delivered_by_neogrowth = $order_delivered_amount - $neogrowth_stats_of_order['delivered_amount'];
		if ($amount_to_be_delivered_by_neogrowth > $amount_available_to_take_action) {
			$amount_to_be_delivered_by_neogrowth = $amount_available_to_take_action;
		}
		$amount_to_be_delivered_by_neogrowth =  sprintf("%.2f", $amount_to_be_delivered_by_neogrowth);
		
		// amount to be cancelled to neogrowth is total cancelled amount of order minus amount already cancelled to neogrowth.
		$amount_to_be_cancelled_to_neogrowth = $order_cancelled_amount - $neogrowth_stats_of_order['cancelled_amount'];
		if ($amount_to_be_cancelled_to_neogrowth > $amount_available_to_take_action) {
			$amount_to_be_cancelled_to_neogrowth = $amount_available_to_take_action;
		}
		
		$amount_to_be_cancelled_to_neogrowth =  sprintf("%.2f", $amount_to_be_cancelled_to_neogrowth);
		
		$neogrowth_actions = array();
		if ($amount_to_be_delivered_by_neogrowth > 0) {
			$neogrowth_actions['deliver'][] = array(
				'order_no' => $order_no,
				'total' => $amount_to_be_delivered_by_neogrowth,
				'detail' => 'Order no - ' . $order_no,
				'text' => 'Mark Deliver'
			);
		}
		
		if ($amount_to_be_cancelled_to_neogrowth > 0) {
			$neogrowth_actions['cancel'][] = array(
				'order_no' => $order_no,
				'total' => $amount_to_be_cancelled_to_neogrowth,
				'detail' => 'Order no - ' . $order_no,
				'text' => 'Mark Cancel'
			);
		}
		
		// 04-09-2018 commenting return action for now
		/*
		$delivered_amount_till_now = $neogrowth_stats_of_order['delivered_amount'] - $neogrowth_stats_of_order['return_amount'];
		
		$credit_notes = CreditNote::getCreditNotesOfAnOrder($this->db, $order_id);
		
		$return_records = $this->getNeoGrowthTransactionLogUsingOrderNo($order_no, 'Return');
		$credit_note_names = array();
		foreach ($return_records as $return_record) {
			if ($return_record['status'] == 'SUCCESS') {
				$credit_note_names[$return_record['transaction_id']] = $return_record['transaction_amount'];
			}
		}
		
		if (!empty($credit_notes) && $delivered_amount_till_now > 0) {
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
				
				$neogrowth_actions['return'][] = array(
					'order_no' => $order_no,
					'total' => $returnable_amount,
					'detail' => $credit_note_name,
					'text' => 'Mark Return <br/>(' . $credit_note_name . ')'
				);
			}
		}*/
		
		return $neogrowth_actions;
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
                            	neogrowth_registration_number,
                            	neogrowth_account_number
                            FROM
                            	".DB_PREFIX."customer_credit
                            WHERE 
                            	customer_id = ". (int)$data['customer_id'] ."
                            	AND type    = 'Neogrowth' 
			              ";

			$result = $this->db->query($select_sql);
			if($result->num_rows > 0){
				$is_updatable = 0;

				$old_credit_status        = $result->row['credit_status'] ?? '';
				$credit_status            = $data['credit_status'] ?? '';

				$old_registration_number  = $result->row['neogrowth_registration_number'] ?? '';
				$registration_number      = $data['neogrowth_registration_number'] ?? '';

				$old_account_number       = $result->row['neogrowth_account_number'] ?? '';
				$account_number           = $data['neogrowth_account_number'] ?? '';

				if($credit_status <> $old_credit_status){
					$is_updatable = 1;
					$admin_change_data['old_value']  = (int)$old_credit_status;
                	$admin_change_data['new_value']  = (int)$credit_status;
                	$admin_change_data['field_name'] = 'credit_status';
                	$this->logAdminChangeLog($admin_change_data);
				}
				if($registration_number <> $old_registration_number){
					$is_updatable = 1;
					$admin_change_data['old_value']  = $old_registration_number;
                	$admin_change_data['new_value']  = $registration_number;
                	$admin_change_data['field_name'] = 'neogrowth_registration_number';
                	$this->logAdminChangeLog($admin_change_data);
				}

				if($account_number <> $old_account_number){
					$is_updatable = 1;
					$admin_change_data['old_value']  = $old_account_number;
                	$admin_change_data['new_value']  = $account_number;
                	$admin_change_data['field_name'] = 'neogrowth_account_number';
                	$this->logAdminChangeLog($admin_change_data);
				}

				if($is_updatable == 1){
					$update_sql = "
	                            UPDATE
	                            	".DB_PREFIX."customer_credit
	                            SET
	                            	credit_status = '".$this->db->escape($data['credit_status'])."',
	                            	neogrowth_registration_number = '".$this->db->escape($data['neogrowth_registration_number'])."',
	                            	neogrowth_account_number = '".$this->db->escape($data['neogrowth_account_number'])."'
	                            WHERE
	                            	customer_id = ". (int)$data['customer_id'] ."
				              ";
					$this->db->query($update_sql);
				}
			}else{
				$credit_status            = $data['credit_status'] ?? '';
				$registration_number      = $data['neogrowth_registration_number'] ?? '';
				$account_number           = $data['neogrowth_account_number'] ?? '';

				$admin_change_data['new_value']  = (int)$credit_status;
            	$admin_change_data['field_name'] = 'credit_status';
            	$this->logAdminChangeLog($admin_change_data);

            	$admin_change_data['new_value']  = $registration_number;
            	$admin_change_data['field_name'] = 'neogrowth_registration_number';
            	$this->logAdminChangeLog($admin_change_data);

            	$admin_change_data['new_value']  = $account_number;
            	$admin_change_data['field_name'] = 'neogrowth_account_number';
            	$this->logAdminChangeLog($admin_change_data);

				$insert_sql = "
                                INSERT INTO
                                	".DB_PREFIX."customer_credit
                                SET
                                	customer_id  = ".(int)$data['customer_id'].",
                                	credit_status = '".$this->db->escape($data['credit_status'])."',
	                            	neogrowth_registration_number = '".$this->db->escape($data['neogrowth_registration_number'])."',
	                            	neogrowth_account_number = '".$this->db->escape($data['neogrowth_account_number'])."'
				              ";
				$this->db->query($insert_sql);
			}
		}
	}

	/**
	 * Public method to log admin change log
	 * @author : Nishu
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
}
