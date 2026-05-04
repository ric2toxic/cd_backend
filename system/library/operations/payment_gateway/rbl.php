<?php
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

require_once(DIR_SYSTEM.'library/operations/payment_gateway/payment_gateway_base.php');

class RblPayment extends PaymentGatewayBase {

	public 	$registry;
	private $_load;
	private $_db;
	private $_partner_id;
	private $_partner_key;

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry 	= $registry;
		$this->user 	    = $registry->user ?? array();
		$this->_db 			= $registry->db;
		$this->_load 		= $registry->load;
		$this->_config 		= $registry->config;
	}

    public function sendRequest($gateway_url, $post_data = ''){

        // Define body for RBL action 
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

    public function getCustomerCreditData(int $customer_id) {
    	$data = array();
    	$sql = "
	    		SELECT 
	    			credit_id,
	    			credit_balance,
	    			DATE(last_updated) as last_updated
	    		FROM 
	    			" . DB_PREFIX . "customer_credit 
	    		WHERE
	    			customer_id = '" . (int)$customer_id  ."'
	    			AND
	    			type = 'RBL'
	    	  ";
	    $result = $this->_db->query($sql);
		if ($result->num_rows) {
			$data = $result->row;
		}
		return $data;
    }

	public function getCustomerPreviousLimitAPI(int $customer_id)
	{
		$response = array();
		$post_data = array();
		$post_data['RDFAnchorJourney']['data'] = array(
														'URNumber' => $this->getCustomerUrnNumber((int)$customer_id),
														'request'  => array(
																'cifStatus'	=> array(
																	'retailerId' => $customer_id,
																	'anchorId'	 => RBL_API_ANCHORID
																)
															)
														);
		$api_endpoint 	= RBL_API_ENDPOINTS.'?client_id='.RBL_API_CLIENT_ID.'&client_secret='.RBL_API_SECRET;
		$result 		= Curl::callRblAPI( $api_endpoint, $post_data );
        $response 		= json_decode($result,true);
        $add_log = array(
	                        'api_type'      => 'cifStatus',
	                        'customer_id'   => $customer_id,
	                        'api_endpoint'  => $api_endpoint,
	                        'api_post_data' => json_encode($post_data),
                        );

        $rbl_log_id = $this->createRblApiLog($add_log);

        if(isset($response['RDFAnchorJourney']['status'])) {
            
            $cif_status     = $response['RDFAnchorJourney']['status'];
            $cif_id          = $response['RDFAnchorJourney']['data']['response']['cifStatus']['cifId'] ?? '';
            $limitAvailable = $response['RDFAnchorJourney']['data']['response']['cifStatus']['limitAvailable'] ?? ''; 
        	
        	/*update log status*/
        	$update_log = array(
	                                'cif_id'                => $cif_id,
	                                'api_response_data'     => json_encode($response),
	                                'api_status'			=> $cif_status,
	                                'status'                => 'SUCCESS'
                            	);
            $this->updateRblApiLog($rbl_log_id, $update_log);
            
            if($cif_status) { 
            	// cif_status = [0-Active], [1-InActive/Hold]
                $sql = "UPDATE " . DB_PREFIX . "customer_credit 
                            SET 
                                credit_status   = '0',
                                last_updated   = NOW()
                            WHERE 
                                customer_id = '" . (int)$customer_id  ."'
                                AND
                                type = 'RBL'
                            ";
                //$this->_db->query($sql);
            }
        }
	}

    public function getCustomerCreditLimit(int $customer_id, $is_check_api = true)
    {
   		$this->getCustomerPreviousLimitAPI($customer_id);	
    	
    	$credit_balance = 0;
	    $sql = "
	    		SELECT 
	    			credit_balance
	    		FROM 
	    			" . DB_PREFIX . "customer_credit 
	    		WHERE
	    			customer_id = '" . (int)$customer_id  ."'
	    			AND
	    			type = 'RBL'
	    			AND
	    			credit_status = '1'	
	    	  ";
	    $result = $this->_db->query($sql);
		if ($result->num_rows) {
			$credit_balance = $result->row['credit_balance'];
		}
		return (float)$credit_balance;
    }
	
	/*
	 * @method: getCustomerRBLLimit
	 * @params: customer id
	 * @return: account number
	 * @author: MSA, March 2019
	 */
	public function getCustomerRBLLimit(int $customer_id): float {

	    $credit_balance = $this->getCustomerCreditLimit($customer_id);

		$pending_order_total = $this->getAllRBLSubOrdersBalance($customer_id);
		
		if( $pending_order_total > 0 ) {
		
			$credit_balance =  (float) ( $credit_balance - $pending_order_total );
			if($credit_balance < 0) {
				$credit_balance = 0;
			}
		} 

		return (float)$credit_balance;
    }

    public function updateCreditBalance(int $customer_id, float $balance): bool {
        $sql = "UPDATE " . DB_PREFIX . "customer_credit 
                SET 
                	credit_balance = '" . $this->_db->escape($balance) ."' 
                WHERE 
                	customer_id = '" . (int)$customer_id  ."'
                	AND
                	type = 'RBL'
                ";
        if ($this->_db->query($sql)) {
            return true;
        }

        return false;
    }
		
	/* private method to get order no using order id */
	private function __getOrderNoUsingOrderId(int $order_id): string {
		$sql = "SELECT order_no FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "' LIMIT 1";
		$result = $this->_db->query($sql);
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
		$result = OrderInfo::getOrderInfo($this->_db, $order_id, '', $selector);

		foreach ($result['suborder'] as $suborder_id => $value) {
			//Check for suborder must not be cancelled
			if($value['order_status_id'] != 2){
				$this->_db->query("CALL calculateTotalInvoiceAmount('" . $this->_db->escape($suborder_id) . "', '" . (int)1 . "', @total_invoice_amount)");
				$suborder_invoice_total = (float) $this->_db->query("SELECT @total_invoice_amount")->row['@total_invoice_amount'];
				$total += $suborder_invoice_total;
			}
		}

		return $total;			
	}
		
	/**
     * Public method to update RBL details for given customer_id from customer_credits tab
     * @param : $data Array
     * @author:MSA, 2019
	*/
	public function updateCreditDetails($data){
		
		if(!empty($data['customer_id'])){
			//Set Data to add into admin_change_log
            $admin_change_data = array();
            $admin_change_data['table_id'] = (int)$data['customer_id'];
			
			$select_sql = "
                            SELECT
                            	credit_status
                            FROM
                            	".DB_PREFIX."customer_credit
                            WHERE 
                            	customer_id = ". (int)$data['customer_id'] ."
                            	AND type    = 'RBL' 
			              ";

			$result = $this->_db->query($select_sql);
			if($result->num_rows > 0){
				$is_updatable = 0;

				$old_credit_status        = $result->row['credit_status'] ?? '';
				$credit_status            = $data['credit_status'] ?? '';

				if($credit_status <> $old_credit_status){
					$is_updatable = 1;
					$admin_change_data['old_value']  = (int)$old_credit_status;
                	$admin_change_data['new_value']  = (int)$credit_status;
                	$admin_change_data['field_name'] = 'credit_status';
                	$this->logAdminChangeLog($admin_change_data);
				}
				if($is_updatable == 1){
					$update_sql = "
	                            UPDATE
	                            	".DB_PREFIX."customer_credit
	                            SET
	                            	credit_status = '".$this->_db->escape($data['credit_status'])."'
	                            WHERE
	                            	customer_id = ". (int)$data['customer_id'] ."
				              ";
					$this->_db->query($update_sql);

					/*Updated credit comment table for RBL account status*/
					$this->updateCreditApplicationStatusRemark((int)$data['customer_id'], (int)$credit_status);

				}
			}else{
				$credit_status            = $data['credit_status'] ?? '';

				$admin_change_data['new_value']  = (int)$credit_status;
            	$admin_change_data['field_name'] = 'credit_status';
            	$this->logAdminChangeLog($admin_change_data);

				$insert_sql = "
                                INSERT INTO
                                	".DB_PREFIX."customer_credit
                                SET
                                	customer_id  = ".(int)$data['customer_id'].",
                                	type 		 = 'RBL',	
                                	credit_status = '".$this->_db->escape($data['credit_status'])."'
				              ";
				$this->_db->query($insert_sql);
			}
		}
	}

	/*
	 * @method: getSubOrdersData- get all suborder by order id
	 * @params: int $order_id
	 * @return: array
	 * @author: MSA, March 2019
	 */
	public function getSubOrdersData(int $order_id)
	{
		$sql = "
				SELECT 
					suborder_id,
					total
				FROM 
					" . DB_PREFIX . "suborder 
				WHERE
					order_id = '".(int)$order_id."'
			    ";
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			return $result->rows;
		}
		return array();
	}

	/*
	 * @method: createRBLTransactionLog- create RBL transaction log entry
	 * @params: data (key => value) transaction data
	 * @return: log id
	 * @author: MSA, March 2019
	 */
	public function createOrderWiseRBLTransactionLog(array $data) {
		$fields = array('url', 'request', 'response', 'order_id', 'suborder_id', 'order_no', 'request_type', 'transaction_id', 'transaction_amount', 'buyer_registration_number','message');
		$data['response'] = json_encode($data);
		$sql = "INSERT INTO 
					" . DB_PREFIX . "transaction_logs 
				SET 
					type 		  = 'RBL', 
					date_added	  = NOW(),
					date_modified = NOW() ";
		foreach ($fields as $key) {
			if (isset($data[$key])) {
				$sql .= ", " . $key . "='" . $this->_db->escape($data[$key]) . "' ";
			}
		}
		$sql .= ", status='SUCCESS'";
		$this->_db->query($sql);

	}

	/*
	 * @method: createRBLTransactionLog- create RBL transaction log entry
	 * @params: data (key => value) transaction data
	 * @return: log id
	 * @author: MSA, March 2019
	 */
	public function createRBLTransactionLog(array $data) {
		
		$suborders = $this->getSubOrdersData($data['order_id']);
		$fields = array('url', 'request', 'response', 'order_id', 'suborder_id', 'order_no', 'request_type', 'transaction_id', 'transaction_amount', 'buyer_registration_number');
		
		if(!empty($suborders) && !empty($data)) {

			foreach ($suborders as $key => $value) {
				
				$data['suborder_id'] = $value['suborder_id'];
				$data['transaction_amount'] = $value['total'];
				$data['response'] = json_encode(array('order_id'=>$order_id,'suborder_id'=>$value['suborder_id'],'order_total'=>$value['total']));
				$sql = "INSERT INTO 
							" . DB_PREFIX . "transaction_logs 
						SET 
							type 		  = 'RBL', 
							message       = 'Order Successfully placed over RBL',
							date_added	  = NOW(),
							date_modified = NOW() ";
				foreach ($fields as $key) {
					if (isset($data[$key])) {
						$sql .= ", " . $key . "='" . $this->_db->escape($data[$key]) . "' ";
					}
				}
				$sql .= ", status='SUCCESS'";
				$this->_db->query($sql);
			}
		}
	}

	/*
	 * @method: updateRBLTransactionLog- update RBL transaction log entry
	 * @params: data (key => value) transaction data, log id
	 * @author: MSA, March 2019
	 */
	public function updateRBLTransactionLog(int $order_id) {
		$suborders = $this->getSubOrdersData($order_id);
		if(!empty($suborders)) {
			foreach ($suborders as $key => $value) {
				$response = json_encode(array('order_id'=>$order_id,'suborder_id'=>$value['suborder_id'],'order_total'=>$value['total']));
				$sql = "UPDATE 
							" . DB_PREFIX . "transaction_logs 
						SET 
							response = '".$this->_db->escape($response)."',
							status 	 = 'SUCCESS',
							message  = 'Order Successfully placed over RBL',
							date_modified=NOW()
						WHERE 
							type = 'RBL'
							AND
							order_id='" . (int)$order_id . "' 
						";
				$this->_db->query($sql);		
			}
		}
	}

	/*
	 * @method: createRBLTransactionLogForDisbursalRequest- create RBL transaction log entry for disbursal request
	 * @params: data (key => value) transaction data
	 * @return: log id
	 * @author: MSA, March 2019
	 */
	public function createRBLTransactionLogForDisbursalRequest(array $data): int {
		$fields = array('url', 'request', 'order_id', 'suborder_id', 'order_no', 'request_type', 'transaction_id', 'transaction_amount', 'buyer_registration_number');
		$sql = "INSERT INTO 
					" . DB_PREFIX . "transaction_logs 
				SET 
					type 		= 'RBL', 
					date_added	= NOW(),
					date_modified = NOW() ";
		foreach ($fields as $key) {
			if (isset($data[$key])) {
				$sql .= ", " . $key . "='" . $this->_db->escape($data[$key]) . "' ";
			}
		}
		$sql .= ", status='NEW'";
		$this->_db->query($sql);
		$log_id = $this->_db->getLastId();
		
		return (int)$log_id;
	}

	/*
	 * @method: updateRBLTransactionLogForDisbursalRequest- update RBL transaction log entry for disbursal request
	 * @params: data (key => value) transaction data, log id
	 * @author: MSA, March 2019
	 */
	public function updateRBLTransactionLogForDisbursalRequest(int $log_id, array $data): bool {
		$fields = array('response', 'status', 'message');
		$sql = "UPDATE " . DB_PREFIX . "transaction_logs 
					SET date_modified=NOW() ";
		foreach ($fields as $key) {
			if (isset($data[$key])) {
				$sql .= ", " . $key . "='" . $this->_db->escape($data[$key]) . "' ";
			}
		}
		
		$sql .= " WHERE log_id='" . (int)$log_id . "'";
		
		if ($this->_db->query($sql)) {
			return true;
		}
	
		return false;
	}

	/*
	 * @method: getRBLTransactionLogUsingOrderNo- get RBL transactions happened for an order
	 * @params: order number, 
	 * @params: request_type (optional) possible values: ‘Purchased’, ‘Delivered’, ‘Cancelled’, ‘Return’ , 'GetOrderStatus', 'GetOTBL'
	 * @author: MSA, March 2019
	 */
	public function getRBLTransactionLogUsingOrderNo(string $order_no, string $request_type=''): array {

		$sql = "SELECT  log_id, type, request_type, transaction_amount, 					date_added, date_modified, transaction_id, 
						buyer_registration_number, status, message, response, order_no FROM " . DB_PREFIX . "transaction_logs 
					 WHERE order_no='" . $this->_db->escape($order_no) ."'";
		$sql .= " AND type = 'RBL' ";
		
		if (!empty($request_type)) {
			$sql .= " AND request_type='" . $this->_db->escape($request_type) . "'";
		}
	
		$result = $this->_db->query($sql);
		if ($result->num_rows) {
			return $result->rows;
		}
		
		return array();
	}

	/*
	 * @method: insertRBLPaymentDetailsIntoDb- insert RBL payment into db
	 * @params: order id
	 * @author: MSA, March 2019
	 */
	public function insertRBLPaymentDetailsIntoDb(int $order_id): bool {
		
		return true; // stop inserting data in order payment table

		$order_no = $this->__getOrderNoUsingOrderId($order_id);
		$purchase_records = $this->getRBLTransactionLogUsingOrderNo($order_no, 'Purchased');
		if (empty($purchase_records)) {
			return false;
		}
		
		$purchase_data = $purchase_records[0];
		
		$paid_amount = $purchase_data['transaction_amount'];
		$payment_type = 'credit_agency';
	
		//Define data array 
		$data = array(); 
		$data['payment_type']       = $payment_type;
		$data['order_id']           = (int)$order_id;
		$data['order_no']           = $order_no;
		$data['txn_status']         = $purchase_data['status'];
		$data['payment_mode']       = 'credit_agency';   
		$data['amount']             = (float)$paid_amount; 
		$data['txn_date_time']      = $purchase_data['date_added'];
		$data['date_added']         = 'NOW()';
		$data['payment_gateway']    = 'rbl';
		$data['successfull']        = '1';
		$data['reference']          = '';
		$data['payment_link']       = 'Payment by RBL';
		$data['json_format']        = $purchase_data['response'];
		$data['user_id']            = '0';
    
		$valid_insertion = 1;
		// check if row is already there with same information
		if($data['amount'] > 0) {
			$payment_data = $this->__getRBLPaymentData((string)$order_no);
			if(!empty($payment_data)) {
				$valid_insertion = 0;
			}
		}

		if($valid_insertion && $data['order_id'] != 0) {
			OrderPayment::insertOrderPayment($this->_db,$data); //To insert data in order payment table
		}
		
		return true;
	}

	/*
	 * @method: getSubOrderWiseAmount- get RBL amount distributed among suborders 
	 * @params: order id, order payment data
	 * @return: suborder wise amount
	 * @author: MSA, March 2019
	 */
	public function getSubOrderWiseAmount(int $order_id, array $payment_data): array {
		$order_no = $this->__getOrderNoUsingOrderId($order_id);
		$purchase_records = $this->getRBLTransactionLogUsingOrderNo($order_no, 'Purchased');
		if (empty($purchase_records)) return array();
		
		$purchase_data = $purchase_records[0];
		
		$advance_vouchers = AdvanceVoucherLib::getActiveAdvanceVouchersWithPayment($this->_db, $order_id, $payment_data['payment_id']);
		
		$suborder_wise_amount = array();
		
		foreach ($advance_vouchers as $advance_voucher) {
			$suborder_wise_amount[$advance_voucher['suborder_id']] = $advance_voucher['value'];
		}
		
		return $suborder_wise_amount;
	}

	/* private method to get payment data for RBL payment */
	private function __getRBLPaymentData(string $order_no): array {
		$sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
				WHERE 
				order_no ='". $this->_db->escape($order_no) ."' AND
				successfull = '1' AND
				payment_gateway = 'rbl'";
		$query_result = $this->_db->query($sql);
		if($query_result->num_rows) {
			return $query_result->row;
		}
		
		return array();
	}

	/* private method to get payment amount against an order except RBL payment amount */
	private function __getExceptRBLPaymentAmount(string $order_no): array {
		$sql = "SELECT 
		          SUM(IF(amount >0, amount, 0)) as amount,
		          SUM(IF(amount < 0, amount, 0)) as refund
				FROM ".DB_PREFIX."order_payment				   
				WHERE 
				 successfull = 1
				   AND bank_transfer_mode NOT IN ('cheque_deposited', 'cheque_failed')
				   AND order_no ='". $this->_db->escape($order_no) ."'
					 AND payment_gateway != 'rbl'
			   ";

		$result = $this->_db->query($sql);
		$data = array();
		$data['amount'] = (float)$result->row['amount'];
		$data['refund'] = (float)$result->row['refund'];
		return $data;
	}

	/*
	 * @method: getRBLStatsOfOrder- get RBL transactions stats for an order
	 * @params: order number, 
	 * @return: purchased_amount, delivered_amount, cancelled_amount, and return_amount
	 * @author: MSA, March 2019
	 */
	public function getRBLStatsOfOrder(string $order_no): array {
		$sql = "SELECT 
						SUM(IF(request_type='Purchased', transaction_amount, 0)) as purchased_amount,
						SUM(IF(request_type='Delivered', transaction_amount, 0)) as delivered_amount,
						SUM(IF(request_type='Cancelled', transaction_amount, 0)) as cancelled_amount,
						SUM(IF(request_type='Return', transaction_amount, 0)) as return_amount
						FROM 
							".DB_PREFIX."transaction_logs
						WHERE 
							order_no='" . $this->_db->escape($order_no) . "' 
							AND status='SUCCESS'
							AND type = 'RBL' ";

		$result = $this->_db->query($sql);
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

	/* private method to update order payment amount according to RBL transactions
		we calculate the amount which will be recieved from RBL on the basis of purchased amount 
		and cancelled amount 
	 */
	private function __updateRBLPaymentAmountAccordingRBLTransactions(string $order_no): bool {
		$payment_data = $this->__getRBLPaymentData((string)$order_no);
		if(empty($payment_data)) {
			return false;
		}
		
		$rbl_stats = $this->getRBLStatsOfOrder($order_no);
		if (empty($rbl_stats['purchased_amount'])) {
			return false;
		}
		
		$current_rbl_order_amount = $rbl_stats['purchased_amount'] - $rbl_stats['cancelled_amount'];
		$current_rbl_order_amount =  sprintf("%.2f", $current_rbl_order_amount);
		
		if ($payment_data['amount'] == $current_rbl_order_amount) {
			return true;
		}
		
		$sql = "UPDATE ". DB_PREFIX . "order_payment 
						SET amount = '" . (float)$current_rbl_order_amount . "'
				WHERE 
				order_no ='". $this->_db->escape($order_no) ."' AND
				payment_id = '" . (int)$payment_data['payment_id'] . "' AND
				payment_gateway = 'rbl'";
				
		$query_result = $this->_db->query($sql);
		if($query_result) {
			return true;
		}
		
		return false;
	}

	/*
	 * @method: __canShowRBLActions- method to check whether should show order action for RBL or not ( in RBL order panel)
	 * Current Logic(March 2019): if all the suborder of an order are completed(i.e. either cancelled or delivered), 
	 		then only we will show RBL actions like 'Mark Deliver to RBL' or 
			'Mark Cancel to RBL' in RBL panel 
	 * @params: order id 
	 * @return: true or false
	 * @author: MSA, March 2018
	 */
	private function __canShowRBLActions(int $order_id): bool {
		$sql = "SELECT order_status_id FROM " . DB_PREFIX . "suborder WHERE order_id = '" . (int)$order_id . "'";
		$query_result = $this->_db->query($sql);
		
		if (!$query_result->num_rows) {
			return false;
		}
		
		$suborders = $query_result->rows;
		
		/* Currenly(March 2019) assuming that suborder is completed if order_status_id is any of 2, 5, 8, or 15.
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


	/*
	 * @method: getRBLActions- get RBL actions for an order 
	 * Possible RBL actions: 1. Mark deliver to RBL, 2. Mark Cancel to RBL, and 3. Mark Return to RBL
	 * complete logic to calculate the amount for above actions is written in this method.
	 * @params: order id
	 * @return: RBL actions array
	 * @author: MSA, March 2019
	 */
	public function getRBLActions(int $order_id): array {
		
		return array();

		$order_no = $this->__getOrderNoUsingOrderId($order_id);
		$purchase_records = $this->getRBLTransactionLogUsingOrderNo($order_no, 'Purchased');
		if (empty($purchase_records)) { 
			// order is not placed on RBL, so return empty array
			return array();
		}
		
		$purchase_data = $purchase_records[0];
		
		$can_show_rbl_action = $this->__canShowRBLActions($order_id);
		if (!$can_show_rbl_action) {
			// can't show RBL actions for this order, so return empty array
			return array();
		}
		
		$order_delivered_amount = 0.00;
		$order_cancelled_amount = 0.00;
		
		$neo_payment_data = $this->__getRBLPaymentData((string)$order_no);
		if (empty($neo_payment_data)) {
			return array();
		}
		
		$order_total = $this->__getOrderTotal($order_id);
		$other_payment_data = $this->__getExceptRBLPaymentAmount((string)$order_no);
		
		$total_payment_amount = $neo_payment_data['amount'] + $other_payment_data['amount'] - $other_payment_data['refund'];
		
		if ($total_payment_amount > $order_total) {
			$order_cancelled_amount = $total_payment_amount - $order_total;
			$order_delivered_amount = $neo_payment_data['amount'] - $order_cancelled_amount;
		} else {
			$order_delivered_amount = $neo_payment_data['amount'];
		}		
		
		$rbl_stats_of_order = $this->getRBLStatsOfOrder($order_no);
		
		$amount_available_to_take_action = $rbl_stats_of_order['purchased_amount'] - $rbl_stats_of_order['delivered_amount'] - $rbl_stats_of_order['cancelled_amount'];
		
		// amount to be delivered by RBL is total delivered amount of order minus amount already delivered by RBL.
		$amount_to_be_delivered_by_rbl = $order_delivered_amount - $rbl_stats_of_order['delivered_amount'];
		if ($amount_to_be_delivered_by_rbl > $amount_available_to_take_action) {
			$amount_to_be_delivered_by_rbl = $amount_available_to_take_action;
		}
		$amount_to_be_delivered_by_rbl =  sprintf("%.2f", $amount_to_be_delivered_by_rbl);
		
		// amount to be cancelled to RBL is total cancelled amount of order minus amount already cancelled to RBL.
		$amount_to_be_cancelled_to_rbl = $order_cancelled_amount - $rbl_stats_of_order['cancelled_amount'];
		if ($amount_to_be_cancelled_to_rbl > $amount_available_to_take_action) {
			$amount_to_be_cancelled_to_rbl = $amount_available_to_take_action;
		}
		
		$amount_to_be_cancelled_to_rbl =  sprintf("%.2f", $amount_to_be_cancelled_to_rbl);
		
		$rbl_actions = array();
		if ($amount_to_be_delivered_by_rbl > 0) {
			$rbl_actions['deliver'][] = array(
				'order_no' => $order_no,
				'total' => $amount_to_be_delivered_by_rbl,
				'detail' => 'Order no - ' . $order_no,
				'text' => 'Mark Deliver'
			);
		}
		
		if ($amount_to_be_cancelled_to_rbl > 0) {
			$rbl_actions['cancel'][] = array(
				'order_no' => $order_no,
				'total' => $amount_to_be_cancelled_to_rbl,
				'detail' => 'Order no - ' . $order_no,
				'text' => 'Mark Cancel'
			);
		}
		
		return $rbl_actions;
	}

	/**
	 * Public method to generate checksumhash string
	 * @author : MSA 2019
	*/
	public function generateCheckSumHash(string $string_to_sign, string $shared_secret): string {
        return base64_encode(hash_hmac("sha1", $string_to_sign, $shared_secret, true));
    }

	/**
	 * Public method to log admin change log
	 * @author : MSA 2019
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
        CommonLib::addAdminChangeLog($this->_db, $admin_change_data);
	}

	/**
	 * Public method to check disbursal request status for a suborder
	 * @param: int $order_id
	 * @param: string $order_no
	 * @param: string $request_type
	 * @author : MSA 2019
	*/
	public function isDisbursalRequestSentForSuborder(int $order_id, string $order_no, string $request_type = 'DisbursalRequest'): int
	{
		$disbursalStatus = 0;
		//suborder_id = '".$this->_db->escape($order_no)."'
		$sql = "
				SELECT 
					log_id 
				FROM " . DB_PREFIX . "transaction_logs
				WHERE
					order_id = '".(int)$order_id."'
						AND
					order_no = '".$this->_db->escape($order_no)."'
						AND
					type  = 'RBL'
						AND
					request_type = '".$this->_db->escape($request_type)."'
						AND
					status NOT IN ('NEW','FAILED') 
				ORDER BY log_id DESC 
				LIMIT 1
				";
		$result = $this->_db->query($sql);	
		
		if($result->num_rows) {
		
			$disbursalStatus = 1;
		}	
		
		return $disbursalStatus;

	}

	/**
	 * Public method to check disbursal request status for a suborder
	 * @param: int $order_id
	 * @param: string $order_no
	 * @param: string $request_type
	 * @author : MSA 2019
	*/
	public function isDisbursalRequestSentForOrder(int $order_id): int
	{
		$disbursalStatus = 0;
		$sql = "
				SELECT 
					log_id 
				FROM " . DB_PREFIX . "transaction_logs
				WHERE
					order_id = '".(int)$order_id."'
						AND
					type  = 'RBL'
						AND
					request_type = 'DisbursalRequest'
						AND
					status NOT IN ('NEW','FAILED') 
				ORDER BY log_id DESC 
				LIMIT 1
				";
		$result = $this->_db->query($sql);	
		
		if($result->num_rows) {
		
			$disbursalStatus = 1;
		}	
		
		return $disbursalStatus;

	}


	/**
	 * Public method to generate disbursal request for a suborder
	 * @param: array $data
	 * @author : MSA 2019
	*/
	public function generateDisbursalRequest(array $data)
	{
		$response = array();
		if(!empty($data))
		{
			$order_id      		= $data['order_id'];
			$order_no   		= $data['order_no'];
			$suborder_id   		= $data['suborder_id'];
			$customer_id 		= $data['customer_id'];
			//$invoice_amount   = (float)$data['invoice_total'];
			$invoice_amount   	= (float)$data['total'];
			$order_punch_amount = (float)$data['order_punch_amount'];


			$previous_credit_limit = $this->getCustomerCreditLimit($customer_id, false);

			$total_pending_disbursal_amount = $this->getCustomerPendingDisbursalAmount($customer_id);

			$previous_credit_limit = $previous_credit_limit - $total_pending_disbursal_amount; 

			//SETTING UP CUSTOMER CREDIT LIMIT USING ORDER PUNCH AMOUNT
				if($invoice_amount < $order_punch_amount) {
					$previous_credit_limit = (float)$previous_credit_limit + (float)$order_punch_amount;
				}

			$updated_credit_limit = (float) ( $previous_credit_limit - $invoice_amount );
			
			$disbursal_request_data = array(
					'date'			=> date('d/m/Y'),
					'anchor_name'	=> 'WHOLESALEBOX',
					'anchor_id'		=> RBL_API_ANCHORID,
					'URNumber'		=> $this->getCustomerUrnNumber($customer_id),
					'retailer_id' 	=> $customer_id,
					'cif_id'		=> $this->getCifIdByOrderId((int)$order_id),
					'retailer_name' => ucwords($data['customer_name']),
					'firm_name'		=> $data['customer_company'],
					'invoice_number'=> $data['invoice_id'],
					'previous_credit_limit' => (float)$previous_credit_limit,
					'invoice_amount'=> $invoice_amount,
					'order_id' 		=> $order_id,
					'order_amount'	=> $order_punch_amount,
					'disbursed_amount' => $invoice_amount,
					'updated_credit_limit' => $updated_credit_limit,
					'disbursal_date'=> date('d/m/Y'),
					'due_date'		=> date('d/m/Y'),
					'update_date'	=> date('d/m/Y'),
				);
			
			//pr($disbursal_request_data);die; 

			try {

				$log_data = array(
						'url'	   => 'payment/rbl/generateDisbursalRequest',
						'request'  => 'order_id='.$order_id . '&orderno=' . $order_no . '&amount=' . $invoice_amount,
						'order_id' => $order_id,
						'order_no' => $order_no,
						'suborder_id' => $suborder_id,
						'request_type' => 'DisbursalRequest',
						'transaction_id' => $order_no,
						'transaction_amount' => $invoice_amount,
						'buyer_registration_number' => ''
					);
				
				$log_id = $this->createRBLTransactionLogForDisbursalRequest($log_data);

				//send disbursal request on API
				$api_status = $this->sendDisbursalRequestOnAPI($disbursal_request_data);

				// //write disbursal request data on CSV file
				// $obj = new MYSFTP();
				
				// //change date format for csv files to write
				// $disbursal_request_data['date'] = date('m/d/Y');
				// $disbursal_request_data['disbursal_date'] = date('m/d/Y');
				// $disbursal_request_data['due_date'] = date('m/d/Y');
				// $disbursal_request_data['update_date'] = date('m/d/Y');
				
				// $csv_write_status = $obj->put( RBL_CSV_FILES['DISBURSAL_REQUEST'], $disbursal_request_data );
				
				//if($csv_write_status || $api_status) {

				if( $api_status ) {

					$update_log_data = array(
						'status'   => 'SUCCESS',
						'response' => 'RBL disbursal request sent successfully',
						'message'  => 'RBL disbursal request sent successfully'
					);		
					$log_id = $this->updateRBLTransactionLogForDisbursalRequest($log_id,$update_log_data);
					$response['success'] = 1;
					$response['message'] = 'RBL disbursal request sent successfully';
					$this->session->data['error_warning'] = $response['message'];

					$sms_data = array(
						'action' 		=> 'disbursal_request',
						'customer_id' 	=> $customer_id,
						'order_no'		=> $order_no,
						'order_amount'  => $invoice_amount,
					);
					$this->sendRBLAlerts( $sms_data );

				}else{

					$response['error_warning'] = 1;
					$response['message'] = 'Error in RBL disbursal request generation process';
					$this->session->data['error_warning'] = $response['message'];
				}

			}catch(Exception $e) {

				$response['error_warning'] = 1;
				$response['message'] = $e->getMessage();
				$this->session->data['error_warning'] = $response['message'];
			}

		} else {
			$response['error_warning'] = 1;
			$response['message'] = 'Invalid RBL transaction data!!';
			$this->session->data['error_warning'] = $response['message'];
		}

		return $response;
	}


	/**
	 * Public method to generate disbursal request using RBL API 
	 * @param: array $data
	 * @author : MSA 2019
	*/
	public function sendDisbursalRequestOnAPI(array $data)
	{	
		try{
			$post_data = array();
			$customer_id = $data['retailer_id'];
			$cif_id = $this->getCifIdByOrderId((int)$data['order_id']);
            $post_data['RDFAnchorJourney']['data'] = array(
                                                            'URNumber' => $this->getCustomerUrnNumber((int)$customer_id),
                                                            'request'  => array(
                                                                    'lanDisbursement' => array(
                                                                        "cifId"					=> $cif_id,
																		"orderId"				=> $data['order_id'],
																		"deliveryDate"			=> date('d/m/Y'),
																		"disbursalRequestDate"	=> date('d/m/Y'),
																		"disbursalAmount"		=> number_format($data['invoice_amount'], 2, '.', ''),
																		"orderAmount"			=> number_format($data['order_amount'], 2, '.', ''),
																		"retailerId"			=> $data['retailer_id'],
																		"anchorId"				=> RBL_API_ANCHORID
                                                                    )
                                                                )
                                                            );
            $api_endpoint   = RBL_API_ENDPOINTS.'?client_id='.RBL_API_CLIENT_ID.'&client_secret='.RBL_API_SECRET;
            $result         = Curl::callRblAPI( $api_endpoint, $post_data );
            $rbl_response   = json_decode($result,true);
            $rbl_api_log = array(
                                    'cif_id'		=> $cif_id,
                                    'api_type'      => 'lanDisbursement',
                                    'customer_id'   => $data['retailer_id'],
                                    'order_id'      => $data['order_id'],
                                    'suborder_id'   => $data['suborder_id'] ?? '',
                                    'order_amount'  => $data['invoice_amount'],
                                    'api_endpoint'  => $api_endpoint,
                                    'api_post_data' => json_encode($post_data),
                                    'disbursal_amount' => $data['invoice_amount'],
                                    'disbursal_request_date' => date('Y-m-d')
                                );
            $punch_log_id = $this->createRblApiLog($rbl_api_log);
            
            if(isset($rbl_response['RDFAnchorJourney']['status'])) {
            	//API called successfully, update log data
            	$lanDisbursementStatus 	= $rbl_response['RDFAnchorJourney']['status'];
            	$DPDStatus 				= $rbl_response['RDFAnchorJourney']['data']['response']['lanDisbursement']['DPDStatus'] ?? '';	
            	$limitStatus 			= $rbl_response['RDFAnchorJourney']['data']['response']['lanDisbursement']['limitStatus'] ?? '';
            	$lan_status 			= $rbl_response['RDFAnchorJourney']['data']['response']['lanDisbursement']['lanStatus'] ?? '';
            	$disbursement_status 	= $rbl_response['RDFAnchorJourney']['data']['response']['lanDisbursement']['disbursementStatus'] ?? '';
            	$updated_limit_available= $rbl_response['RDFAnchorJourney']['data']['response']['lanDisbursement']['updatedLimiAvailable'] ?? '';
            	$transaction_id 		= $rbl_response['RDFAnchorJourney']['data']['response']['lanDisbursement']['transactionID'] ?? '';
            	$message 				= $rbl_response['RDFAnchorJourney']['data']['response']['lanDisbursement']['message'] ?? '';

            	$update_punch_log = array(
                                            'api_status'			 => $lanDisbursementStatus,
                                            'DPDStatus'              => $DPDStatus,
                                            'limitStatus'			 => $limitStatus,
                                            'lan_status'			 => $lan_status,
                                            'disbursement_status'	 => $disbursement_status,
                                            'updated_limit_available'=> $updated_limit_available,
                                            'transaction_id'		 => $transaction_id,
                                            'message'				 => $message,
                                            'api_response_data'      => json_encode($rbl_response),
                                            'status'                 => 'SUCCESS'
                                        );
                $this->updateRblApiLog($punch_log_id, $update_punch_log);

                if( !$lanDisbursementStatus 
                	&&
                	!empty($transaction_id) 
                	&&
                	strtolower($disbursement_status) == 'success'
                 ){

                // TESTING - UPDATED CREDIT BALANCE BY ORDER PUNCH AMOUNT DIFFERENCE   
                	/*if($updated_limit_available > 0) {

	                	$sql = "UPDATE " . DB_PREFIX . "customer_credit 
				                SET 
				                    credit_balance = credit_balance + '" . (float)$updated_limit_available ."' 
				                WHERE 
				                    customer_id = '" . (int)$customer_id  ."'
				                    AND
				                    type = 'RBL'
				                    AND
				                    credit_status = '1'	
				                ";
			         }*/

			    // PRODUCTION - UPDATED CREDIT BALANCE BY API RESPONSE DATA    
                	$sql = "UPDATE " . DB_PREFIX . "customer_credit 
			                SET 
			                    credit_balance = '" . (float)$updated_limit_available ."' 
			                WHERE 
			                    customer_id = '" . (int)$customer_id  ."'
			                    AND
			                    type = 'RBL'
			                    AND
			                    credit_status = '1'	
			                ";
			                
			        $this->_db->query($sql);
                }

                return !$lanDisbursementStatus;
            }

		}catch(Exception $e) {

			return false;
		}
	}

	public function sendOrderCancelRequestToRBL(array $data)
	{
		try{

			$post_data = array();
			$customer_id = $data['customer_id'];
            $order_id    = $data['order_id'];
            $order_total = $data['total'];
            $date_added = date("d/m/Y",strtotime($data['date_added']));
            $post_data['RDFAnchorJourney']['data'] = array(
                                                            "URNumber" => $this->getCustomerUrnNumber((int)$customer_id),
                                                            "request"  => array(
                                                                    "orderCancellation" => array(
																		"anchorId"		=> RBL_API_ANCHORID,
																		"retailerId"	=> $customer_id,
																		"orderDate"		=> $date_added,
																		"orderAmount"	=> number_format((float)$order_total, 2, '.', ''),
																		"orderCancellationdate"	=> date("d/m/Y"),
																		"orderId"		=> $order_id,
                                                                    )
                                                                )
                                                            );
            $api_endpoint   = RBL_API_ENDPOINTS.'?client_id='.RBL_API_CLIENT_ID.'&client_secret='.RBL_API_SECRET;
            $result         = Curl::callRblAPI( $api_endpoint, $post_data );
            $rbl_response   = json_decode($result,true);
            $rbl_api_log = array(
                                'user'			=>$this->user->getUserName()['username'],
                                'api_type'      => 'orderCancellation',
                                'customer_id'   => $customer_id,
                                'order_id'      => $order_id,
                                'order_amount'  => $order_total,
                                'api_endpoint'  => $api_endpoint,
                                'api_post_data' => json_encode($post_data),
                                );
            $punch_log_id = $this->createRblApiLog($rbl_api_log);

            if(isset($rbl_response['RDFAnchorJourney']['status'])) {

            	//API called successfully, update log data
            	$orderCancellationStatus 	= (int)$rbl_response['RDFAnchorJourney']['status'];
            	$orderStatus 				= $rbl_response['RDFAnchorJourney']['data']['response']['orderCancellation']['orderStatus'] ?? '';
            	$updatedLimitAvailable      = $rbl_response['RDFAnchorJourney']['data']['response']['orderCancellation']['updatedLimitAvailable'] ?? '';
            	$message 					= $rbl_response['RDFAnchorJourney']['data']['response']['orderCancellation']['message'] ?? '';

            	$update_punch_log = array(
                                            'api_status'			 => $orderCancellationStatus,
                                            'message'				 => $message,
                                            'api_response_data'      => json_encode($rbl_response),
                                            'status'                 => 'CANCELLED'
                                        );
                $this->updateRblApiLog($punch_log_id, $update_punch_log);

                if(!$orderCancellationStatus && strtolower($orderStatus) == 'cancelled') {

                	// TESTING - UPDATED CREDIT BALANCE BY ORDER PUNCH AMOUNT 
			        /*$sql = "UPDATE " . DB_PREFIX . "customer_credit 
			                SET 
			                    credit_balance = credit_balance + '" . (float)$order_total ."' 
			                WHERE 
			                    customer_id = '" . (int)$customer_id  ."'
			                    AND
			                    type = 'RBL'
			                    AND
			                    credit_status = '1'	
			                ";       
			        $this->_db->query($sql);
			        */

			    // PRODUCTION - UPDATED CREDIT BALANCE BY API RESPONSE DATA  
			        
			        if( !empty( $updatedLimitAvailable ) ) {

            			$sql = "UPDATE " . DB_PREFIX . "customer_credit 
			                SET 
			                    credit_balance = '" . (float)$updatedLimitAvailable ."' 
			                WHERE 
			                    customer_id = '" . (int)$customer_id  ."'
			                    AND
			                    type = 'RBL'
			                    AND
			                    credit_status = '1'	
			                ";
			            $this->_db->query($sql);    
			        } 

			        //add transaction log entry for order cancelled on RBL

			        $sql = "INSERT INTO " . DB_PREFIX . "transaction_logs
			        		SET
			        			type 				= 'RBL',
			        			url  				= '".$this->_db->escape($api_endpoint)."',
			        			request 			= '".$this->_db->escape(json_encode($post_data))."',
			        			response    		= '".$this->_db->escape(json_encode($rbl_response))."',
			        			order_id    		= '".(int)$order_id."',
			        			request_type		= 'Cancelled',
			        			transaction_amount 	=  '".$this->_db->escape($order_total)."',
			        			transaction_id 		= '".(int)$order_id."',
			        			message   			= '".$this->_db->escape($message)."',
			        			status 				= 'SUCCESS',
			        			date_added 			= NOW(),
			        			date_modified 		= NOW()
			        		";
			        $this->_db->query($sql);	

            	}

    			$response['error_warning'] = 1;
				if(!$orderCancellationStatus && strtolower($orderStatus) == 'cancelled') {
					$response['message'] = 'Order cancellation request has sent to RBL successfully';	
					$response['success'] = 1;	
				}else{
					$response['message'] = 'Error occure to cancel order on RBL';
					$this->session->data['error_warning'] = $response['message'];	
				}
				
            }else{

            	$response['error_warning'] = 1;
				$response['message'] = 'RBL Server not responding!!';	
				$this->session->data['error_warning'] = $response['message'];
            }

		}catch(Exception $e) {

			$response['error_warning'] = 1;
			$response['message'] = $e->getMessage();	
			$this->session->data['error_warning'] = $response['message'];
		}

		return $response;
	}

	public function sendOrderPunchRequestToRBL(int $customer_id, int $order_id, string $suborder_id, float $suborder_total)
	{
		$response = array();

		if(empty($customer_id) || empty($suborder_id) || empty($suborder_total))
		{
			$response['error']['rbl'] = 'API data not found!';

		} else {
			//call RBL OrderPunchAPI
			$post_data = array();
			$suborder_id = $suborder_id . '_' . $this->getUniqueCode();
		    $post_data['RDFAnchorJourney']['data'] = array(
	                                                      'URNumber' => RBL_API_URNUMBER,
	                                                      'request'  => array(
	                                                              'orderPunch' => array(
	                                                                  'retailerId'    => $customer_id,
	                                                                  'anchorId'      => RBL_API_ANCHORID,
	                                                                  'orderDate'     => date("d/m/Y"),
	                                                                  'orderAmount'   => number_format((float)$suborder_total, 2, '.', ''),
	                                                                  'deliveryDate'  => date('d/m/Y', strtotime(date("Y-m-d"). ' + 10 day')),
	                                                                  'orderId'       => $suborder_id
	                                                              )
	                                                          )
	                                                      );
		    $api_endpoint   = RBL_API_ENDPOINTS.'?client_id='.RBL_API_CLIENT_ID.'&client_secret='.RBL_API_SECRET;
		    $result         = Curl::callRblAPI( $api_endpoint, $post_data );
		    $rbl_response   = json_decode($result,true);
		    $log_data 		= array(
		    						'api_type' 		=> 'orderPunch',
		    						'customer_id' 	=> $customer_id,
		    						'order_id'		=> $order_id,
		    						'suborder_id' 	=> $suborder_id,
		    						'order_amount' 	=> $suborder_total,
		    						'api_endpoint' 	=> $api_endpoint,
		    						'api_post_data' => json_encode($post_data)
		    					);
		    $log_id = $this->createRblApiLog($log_data);
 	        if(isset($rbl_response['RDFAnchorJourney']['status'])) 
		    {
	          	$api_status      = $rbl_response['RDFAnchorJourney']['status'];
	          	$cif_id          = $rbl_response['RDFAnchorJourney']['data']['response']['orderPunch']['cifId'] ?? '';
	          	$dpd_count       = $rbl_response['RDFAnchorJourney']['data']['response']['orderPunch']['DPDCount'] ?? '';
	          	$message         = $rbl_response['RDFAnchorJourney']['data']['response']['orderPunch']['message'] ?? '';

	          	$update_punch_log= array(
	          					'cif_id' 			=> $cif_id,
	          					'dpd_count'			=> $dpd_count,
	          					'api_status'		=> $api_status,
	          					'api_response_data'	=> json_encode($rbl_response),
	          					'status'			=> 'SUCCESS'
	          				);

	           	$this->updateRblApiLog($log_id, $update_punch_log);
	          	
	           	if( $api_status > 0 ) {
	           		
	           		$response['error']['rbl'] = 'RBL API Response :: '.$message;

	           	} else if( $dpd_count > 0 ) {

	           		$response['error']['rbl'] = "Don't dispatch this suborder (RBL DPDCount > 0)";
	           	
	           	} else {

	           		$response['success']['rbl'] = 'RBL allow to process this suborder';
	           	}
	          	
  	        } else {

  	        	$response['error']['rbl'] = "RBL Server not responding";
  	        } 
		}

		return $response;
	}

	public function getUniqueCode()
	{
		return substr(uniqid(),7,13);
	}

	/**
	 * Public method to get CIF_ID for a order id from RBL log data
	 * @param: int $order_id
	 * @author : MSA 2019
	*/
	public function getCifIdByOrderId(int $order_id)
	{
		$cif_id = 0;
		$sql = "
				SELECT 
					MAX(id),
					cif_id
				FROM 
					" . DB_PREFIX . "rbl_api_log
				WHERE
					order_id = '".(int)$order_id."'
					AND
					api_type = 'orderPunch'
					AND
					api_status = 0
			";
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			$cif_id = $result->row['cif_id'];
		}
		return $cif_id;
	}

	/**
	 * Public method to get customer pending disbursal amount
	 * @param: int $order_id
	 * @author : MSA 2019
	*/
	public function getCustomerPendingDisbursalAmount(int $customer_id, string $order_no = '')
	{
		$status = array_merge(
							  ORDER_STATUS_CLUSTERS['failed'],
							  ORDER_STATUS_CLUSTERS['cancelled']
							);
		$sql = "
			SELECT
				SUM(tsan_log.transaction_amount) as total_pending_disbursal_amount
			FROM " . DB_PREFIX . "transaction_logs as tsan_log
			INNER JOIN " . DB_PREFIX . "suborder as sub ON sub.suborder_id = tsan_log.suborder_id
			INNER JOIN " . DB_PREFIX . "order as o ON o.order_id = sub.order_id
			WHERE
				tsan_log.type = 'RBL'
					AND
				tsan_log.request_type = 'DisbursalRequest'
					AND
				tsan_log.status NOT IN ('NEW','FAILED') 
					AND
				o.customer_id = '".(int)$customer_id."'
					AND
				sub.invoice_no > 0
					AND
				sub.order_status_id > 0 
					AND
				sub.order_status_id <> 2
				 	AND
				sub.order_status_id NOT IN (".implode(',', $status).")
			";
		$where = "";
		if(!empty($order_no)) {
			$where = " order_no = '".$this->_db->escape($order_no)."' ";
		}
		$sql = $sql . $where;
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			return $result->row['total_pending_disbursal_amount'] ?? 0;
		}
	}

	/**
	 * Public method to get total of customer's RBL suborders
	 * @param: int $customer_id
	 * @author : MSA 2019
	*/
	public function getAllRBLSubOrdersBalance(int $customer_id)
	{
		/*
			SubOrder Wise
				oc_transaction_logs AS log ON log.suborder_id = sub.suborder_id
			Order Wise
				oc_transaction_logs AS log ON log.order_id = sub.order_id
		*/	

		$sql = "
			SELECT
				 sub.order_id,
				 sub.suborder_id
			FROM
				  oc_order AS o
				INNER JOIN
				  oc_suborder AS sub ON sub.order_id = o.order_id
				INNER JOIN
				  oc_transaction_logs AS log ON log.order_id = sub.order_id
				WHERE
				  o.payment_code = 'rbl_credit' 
				  AND 
				  o.customer_id = '".(int)$customer_id."' 
				  AND 
				  sub.order_status_id > 0
				  AND 
				  sub.order_status_id <> 2
				  AND
				  o.store_id IN (".WSB_STORES_ID.") 
				  AND
     			   ( o.franchise_id IS NULL 
     			   		OR 
     			   	 o.franchise_id = 0 
     			   )
     			  AND
     				o.stock_transfer = 0 
				  AND
				  log.type = 'RBL'
				  AND
				  log.request_type NOT IN ('Disbursed')
				  AND 
				  log.status != 'FAILED'
				GROUP BY sub.suborder_id  
			";
		$result = $this->_db->query($sql);
		$balance_amount = 0;
		if($result->num_rows) {
			foreach ($result->rows as $key => $value) {
				//$balance_amount += orderInfo::getOrderBalanceAmount($this->_db, $value['order_id']);
				$balance_amount += Suborder::getSubOrderBalanceAmount($this->registry, $value['order_id'], $value['suborder_id']);
			}
		}

		return -(float)$balance_amount;
	}

	/**
	 * Public method to get RBL order total for a customer id
	 * @param: int $customer_id
	 * @author : MSA 2019
	*/
	public function getCustomerRBLOrdersTotal(int $customer_id)
	{	
		$status = array_merge(ORDER_STATUS_CLUSTERS['delivered'], 
							  ORDER_STATUS_CLUSTERS['failed'],
							  ORDER_STATUS_CLUSTERS['cancelled']
							);
		$sql = "
			SELECT
			    log.log_id,
			    log.request_type,
			    log.transaction_amount
			FROM " . DB_PREFIX . "order as o
			INNER JOIN " . DB_PREFIX . "suborder as sub ON sub.order_id = o.order_id
			INNER JOIN " . DB_PREFIX . "transaction_logs as log ON log.order_id = o.order_id
			WHERE
				o.payment_code = 'rbl_credit'
					AND
				o.customer_id = '".(int)$customer_id."'
					AND
				sub.order_status_id <> 2 
					AND
				sub.order_status_id NOT IN (".implode(',', $status).")
					AND
				log.status != 'FAILED'
			";
		//echo '<br>'.$sql.'<br>'; die;
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			$data = array();
			foreach ($result->rows as $key => $value) {
				$data[$value['log_id']] = $value;
			}
			$total_purchased = 0;
			$total_disbursed = 0;
			if(!empty($data)) {
				foreach ($data as $key => $value) {
					if(in_array($value['request_type'], array('Purchased'))) {
						$total_purchased += $value['transaction_amount'];
					}
					if(in_array($value['request_type'], array('DisbursalRequest','Disbursed'))) {
						$total_disbursed += $value['transaction_amount'];
					}
				}
			}
			
			return (float)($total_purchased - $total_disbursed);

		}	
	}

	/**
     * @info: public method to send SMS, Notification & Email through CRM API's
     * @param: array $data
     * @author: MSA, July 2019 
    */
	public function sendRBLAlerts(array $data)
    {
    	if(!empty($data) && 
    	   !empty($data['action']) && 
    	   !empty($data['customer_id']) 
    	)
    	{
		   $this->sendRBLAlertsThroughCrmAPI($data);
    	}
    }

    /**
     * @info: protected method to send SMS, Notification & Email through CRM API's
     * @param: array $data
     * @author: MSA, July 2019 
    */
    protected function sendRBLAlertsThroughCrmAPI(array $data)
    {
	    $customer_id 		= $data['customer_id'] ?? ''; 
	    $customer_obj   	= new Customer($this);
	    $fields         	= 'customer_id, master_id, firstname, lastname, email, telephone';
        $customer			= $customer_obj->getCutomerInfo(array($customer_id), $fields)[$customer_id];
	    $data 				= array_merge($data,$customer);
	    $this->setRBLEmailData($data);
	    $this->setRBLSMSData($data);
		$this->queuePushNotification($data);	    
    }

    /**
     * @info: Cron protected method to set customer email data for RBL action
     * @param: array $data
     * @author: MSA, July 2019 
    */
    protected function setRBLEmailData(array &$data)
    {
    	$action 	= $data['action'] ?? '';
		$data['email_data'] = array(
			'to' 		=> $data['email'],
			'subject'	=> $this->getEmailSubject($action),
			'is_html'	=> 1,
			'body'		=> serialize($this->load->view('mail/rbl/'.$action.'.tpl', $data)),
		);
    }

    /**
     * @info: Cron protected method to set email subject line
     * @param: string $action
     * @author: MSA, July 2019 
    */
    protected function getEmailSubject(string $action)
    {
        switch ($action) {
            
            case 'pre_onboarding':
                return 'RBL loan pre-onboarding status ';
                break;
            
            case 'disbursal_request':
                return 'RBL disbursal request status';
                break;
            
            default:
                return 'RBL Credit';
                break;
        }
    }

    /**
     * @info: Cron protected method to set customer SMS data for RBL action
     * @param: array $data
     * @author: MSA, July 2019 
    */
    protected function setRBLSMSData(array &$data)
    {
    	$action 	= $data['action'] ?? '';
    	$language   = $this->load->language('sale/customer_credit_application');
    	$data['sms']=  sprintf($language['sms_'.$action],$data['order_amount'],$data['order_no']);
    	$data['notification'] = sprintf($language['notification_'.$action],$data['order_amount'],$data['order_no']);
    }

    /**
     * @info: Cron protected method to call CRM API for SMS, Notification & Email
     * @param: array $data
     * @author: MSA, July 2019 
    */
    protected function queuePushNotification(array $data) {
    	
    	if(empty($data)) { return true; }

    	$api_data = array();
    	$api_data['notification_sending_time'] = date('Y-m-d h:i:s');
	    $api_data['type'] = 'instant';
	    $api_data['data'][] = array(
				    				'type'	=> 'customer',
				    				'id'	=> $data['customer_id'], 
				    				'is_pn_to_send' => 1,
				    				'pn'	=> array(
				    								'msg_type'         => 1,
													'message'          => $data['notification'],
													'show_instantly'   => 1, 
													'title'		       => 'RBL Credit Alert',
													'vibrate'	       => 1,
													'mobile'	       => $data['telephone'], 
													'sound'		       => 1
				    							),
				    				'is_sms_to_send'	=> 1,
				    				'sms'				=> array(
																'mobile' 	=> $data['telephone'],
																'message' 	=> $data['sms'],
																),
				    				'is_email_to_send'	=> 1,
				    				'email'				=> $data['email_data'],
				    				'email_to_head'		=> 0,
				    				'pn_to_head'		=> 0,
				    				'sms_to_head'		=> 0
				    			);
	    Curl::post( SMS_SENDING_API_URL, $api_data );
    }

    /*
	 * @method: createRblApiLog- create RBL order punch log entry
	 * @params: data (key => value) order punch data
	 * @return: log id
	 * @author: MSA, July 2019
	 */
	public function createRblApiLog(array $data) {

		$sql = "INSERT INTO " . DB_PREFIX . "rbl_api_log SET ";
		$fields = array();
		if(!empty($data['user'])) {
			$fields[] = " user = '".$this->_db->escape($data['user'])."' ";
		}
		if(!empty($data['api_type'])) {
			$fields[] = " api_type = '".$this->_db->escape($data['api_type'])."' ";
		}
		if(!empty($data['customer_id'])) {
			$fields[] = " customer_id = '".(int)$data['customer_id']."' ";
		}
		if(!empty($data['order_id'])) {
			$fields[] = " order_id = '".(int)$data['order_id']."' ";
		}
		if(!empty($data['suborder_id'])) {
			$fields[] = " suborder_id = '".$this->_db->escape($data['suborder_id'])."' ";
		}
		if(!empty($data['order_amount'])) {
			$fields[] = " order_amount = '".$this->_db->escape($data['order_amount'])."' ";
		}
		if(!empty($data['api_endpoint'])) {
			$fields[] = " api_endpoint = '".$this->_db->escape($data['api_endpoint'])."' ";
		}
		if(!empty($data['api_post_data'])) {
			$fields[] = " api_post_data = '".$this->_db->escape($data['api_post_data'])."' ";
		}
		if(!empty($data['api_response_data'])) {
			$fields[] = " api_response_data = '".$this->_db->escape($data['api_response_data'])."' ";
		}
		if(!empty($data['delivery_date'])) {
			$fields[] = " delivery_date = '".$this->_db->escape($data['delivery_date'])."' ";
		}
		if(!empty($data['disbursal_request_date'])) {
			$fields[] = " disbursal_request_date = '".$this->_db->escape($data['disbursal_request_date'])."' ";
		}
		if(!empty($data['disbursal_amount'])) {
			$fields[] = " disbursal_amount = '".$this->_db->escape($data['disbursal_amount'])."' ";
		}
		if(!empty($data['api_status'])) {
			$fields[] = " api_status = '".$this->_db->escape($data['api_status'])."' ";
		}
		if(!empty($data['status'])) {
			$fields[] = " status = '".$this->_db->escape($data['status'])."' ";
		}else{
			$fields[] = " status = 'NEW' ";
		}
		$fields[] = " date_added = NOW() ";
		$sql .= implode(', ', $fields);
		$this->_db->query($sql);
		return $this->_db->getLastId();
	}

	/*
	 * @method: updateRblApiLog- update RBL order punch log entry
	 * @params: int $log_id
	 * @params: array $data
	 * @author: MSA, July 2019
	 */
	public function updateRblApiLog(int $log_id, array $data) {

		$sql = "UPDATE " . DB_PREFIX . "rbl_api_log SET ";
		$fields = array();
		if(!empty($data['cif_id'])) {
			$fields[] = " cif_id = '".$this->_db->escape($data['cif_id'])."' ";
		}
		if(isset($data['dpd_count'])) {
			$fields[] = " dpd_count = '".(int)$data['dpd_count']."' ";
		}
		if(!empty($data['api_response_data'])) {
			$fields[] = " api_response_data = '".$this->_db->escape($data['api_response_data'])."' ";
		}
		if(!empty($data['disbursal_amount'])) {
			$fields[] = " disbursal_amount = '".$this->_db->escape($data['disbursal_amount'])."' ";
		}
		if(!empty($data['disbursal_request_date'])) {
			$fields[] = " disbursal_request_date = '".$this->_db->escape($data['disbursal_request_date'])."' ";
		}
		if(!empty($data['dpd_status'])) {
			$fields[] = " dpd_status = '".$this->_db->escape($data['dpd_status'])."' ";
		}
		if(isset($data['api_status'])) {
			$fields[] = " api_status = '".$this->_db->escape($data['api_status'])."' ";
		}
		if(!empty($data['limit_status'])) {
			$fields[] = " limit_status = '".$this->_db->escape($data['limit_status'])."' ";
		}
		if(!empty($data['lan_status'])) {
			$fields[] = " lan_status = '".$this->_db->escape($data['lan_status'])."' ";
		}
		if(!empty($data['disbursement_status'])) {
			$fields[] = " disbursement_status = '".$this->_db->escape($data['disbursement_status'])."' ";
		}
		if(!empty($data['updated_limit_available'])) {
			$fields[] = " updated_limit_available = '".$this->_db->escape($data['updated_limit_available'])."' ";
		}
		if(!empty($data['transaction_id'])) {
			$fields[] = " transaction_id = '".$this->_db->escape($data['transaction_id'])."' ";
		}
		if(!empty($data['message'])) {
			$fields[] = " message = '".$this->_db->escape($data['message'])."' ";
		}
		if(!empty($data['status'])) {
			$fields[] = " status = '".$this->_db->escape($data['status'])."' ";
		}
		$sql .= implode(', ', $fields);
		$sql .= " WHERE id = '".(int)$log_id."' ";
		$this->_db->query($sql);
	}

	/*
	 * @method: updateRblApiLog- update RBL order punch log entry
	 * @params: int $log_id
	 * @params: array $data
	 * @author: MSA, July 2019
	 */
	public function setOrderHistory($url, $post_params)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        if(curl_error($ch)){
            echo curl_error($ch); exit;
        }
        curl_close($ch);
	}

	/*
	 * @method: isOrderAlreadyCancelledOnRBL- check order cancelled status on RBL
	 * @params: int $order_id
	 * @author: MSA, July 2019
	 */
	public function isOrderAlreadyCancelledOnRBL(int $order_id)
	{
		$sql = "
			SELECT 
				id
			FROM 
				" . DB_PREFIX . "rbl_api_log
			WHERE
				api_type = 'orderCancellation'
				AND
				order_id = '".(int)$order_id."'
				AND
				( api_status = '0' OR  api_status IS NULL )
				AND
				status = 'CANCELLED'
		";
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			return 1;
		}
		
		return 0;
	}

	/*
	 * @method: getCustomerUrnNumber- get pre-approved customer URN Number
	 * @params: int $customer_id
	 * @author: MSA, July 2019
	*/
	public function getCustomerUrnNumber(int $customer_id)
	{
		$sql = "
			SELECT 
				utr 
			FROM 
				" . DB_PREFIX . "customer_credit_preapproved
			WHERE
				retailer_id = '".(int)$customer_id."'
			";
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			return $result->row['utr'];
		}
		
		return 0;
	}

	/*
	 * @method: getRBLOrderPunchAmount- get order punch amount
	 * @params: int $customer_id
	 * @author: MSA, July 2019
	*/
	public function getRBLOrderPunchAmount(int $order_id)
	{
		$sql = "
			SELECT 
				transaction_amount 
			FROM 
				" . DB_PREFIX . "transaction_logs
			WHERE
				type = 'RBL'
				AND
				request_type = 'OrderPunch'
				AND
				order_id = '".(int)$order_id."'
				AND
				status = 'SUCCESS'
			";
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			return (float)$result->row['transaction_amount'];
		}
		
		return 0;
	}

	/*
	 * @method: getCustomerCIFId- get cif approved customer CIF ID
	 * @params: int $customer_id
	 * @author: MSA, July 2019
	*/
	public function getCustomerCIFId(int $customer_id)
	{
		$sql = "
			SELECT 
				cif_id 
			FROM 
				" . DB_PREFIX . "customer_credit_cif_created
			WHERE
				retailer_id = '".(int)$customer_id."'
			";
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			return $result->row['cif_id'];
		}
		
		return 0;
	}


	/*
	 * @method: getCustomerCreditBalance
	 * @params: customer id
	 * @return: account number
	 * @author: MSA, March 2019
	 */
	public function getCustomerCreditBalance(int $customer_id): float {

	    $credit_balance = 0;
	    $sql = "
	    		SELECT 
	    			credit_balance
	    		FROM 
	    			" . DB_PREFIX . "customer_credit 
	    		WHERE
	    			customer_id = '" . (int)$customer_id  ."'
	    			AND
	    			type = 'RBL'
	    			AND
	    			credit_status = '1'	
	    	  ";
	    $result = $this->_db->query($sql);
		if ($result->num_rows) {
			$credit_balance = (float)$result->row['credit_balance'];
		}
		return (float)$credit_balance;
    }

    /*
	 * @method: checkCustomerCIFStatus use to check customer account CIF status over RBL
	 * @params: customer id
	 * @return: void
	 * @author: MSA, March 2019
	 */
    public function checkCustomerCIFStatus(int $customer_id)
	{
		$response = array();
		$post_data = array();
		$post_data['RDFAnchorJourney']['data'] = array(
														'URNumber' => $this->getCustomerUrnNumber((int)$customer_id),
														'request'  => array(
																'cifStatus'	=> array(
																	'retailerId' => $customer_id,
																	'anchorId'	 => RBL_API_ANCHORID
																)
															)
														);
		$api_endpoint 	= RBL_API_ENDPOINTS.'?client_id='.RBL_API_CLIENT_ID.'&client_secret='.RBL_API_SECRET;
		$result 		= Curl::callRblAPI( $api_endpoint, $post_data );
        $response 		= json_decode($result,true);
        $add_log = array(
	                        'api_type'      => 'cifStatus',
	                        'customer_id'   => $customer_id,
	                        'api_endpoint'  => $api_endpoint,
	                        'api_post_data' => json_encode($post_data),
	                        'api_response_data' => json_encode($response),
                        );

        $rbl_log_id = $this->createRblApiLog($add_log);

        if(isset($response['RDFAnchorJourney']['status'])) {
            
            $cif_status     = $response['RDFAnchorJourney']['status'];
            $cif_id          = $response['RDFAnchorJourney']['data']['response']['cifStatus']['cifId'] ?? '';
            $limitAvailable = $response['RDFAnchorJourney']['data']['response']['cifStatus']['limitAvailable'] ?? ''; 
        	
        	/*update log status*/
        	$update_log = array(
	                                'cif_id'                => $cif_id,
	                                'api_response_data'     => json_encode($response),
	                                'api_status'			=> $cif_status,
	                                'status'                => 'SUCCESS'
                            	);
            $this->updateRblApiLog($rbl_log_id, $update_log);
            
            if($cif_status) { 
            	/*
				 * cif_status = [0-Active], [1-InActive/Hold]
				 * If cif status = 1, de-activate user credit balance account
            	*/
                $sql = "UPDATE " . DB_PREFIX . "customer_credit 
                            SET 
                                credit_status   = '0',
                                last_updated   = NOW()
                            WHERE 
                                customer_id = '" . (int)$customer_id  ."'
                                AND
                                type = 'RBL'
                            ";
                //uncomment this line on PRODUCTION 
                $this->_db->query($sql);

            }else if($cif_status == 0) {
            	/*
            	 * Update customer credit limit as per available limit in CIF Status API data
            	*/
            	 $sql ="UPDATE " . DB_PREFIX . "customer_credit 
                        SET 
                            credit_balance   = '".(float)$limitAvailable."',
                            last_updated   	 = NOW()
                        WHERE 
                            customer_id = '" . (int)$customer_id  ."'
                            AND
                            type = 'RBL'
                        ";
                //uncomment this line on PRODUCTION 
                $this->_db->query($sql); 
            }
        }
	}

	/*
	 * @method: updateCreditApplicationStatusRemark 	
	 *			@info - updated credit status remark data for credit status activity
	 * @params: customer id
	 * @return: account number
	 * @author: MSA, March 2019
	 */
	public function updateCreditApplicationStatusRemark( int $customer_id, int $status )
	{
		$sql = "SELECT 
					id 
				FROM " . DB_PREFIX . "credit_application 
				WHERE 
					customer_id = '".(int)$customer_id."' 
				";
		$result = $this->_db->query($sql);
		if($result->num_rows) {
			$credit_application_id = $result->row['id'];
			if( $status  == 1) {
				$status_text = 'Customer RBL Credit Status Enabled';
				$type = 'Comment';
			}else{
				$status_text = 'Customer RBL Credit Status Disabled';
				$type = 'Note';
			}
			$remark_sql = "
						INSERT INTO " . DB_PREFIX . "credit_application_status_remarks
						SET
							credit_application_id 	= '".$this->_db->escape($credit_application_id)."',
							user_id 				= '".(int)$this->user->getId()."',
							type    				= '".$this->_db->escape($type)."', 
							reason 					= '".$this->_db->escape($status_text)."', 
							remark 					= '".$this->_db->escape($status_text)."', 
							show_comment 			= 0,
							date_added 				= NOW()
						";
			$this->_db->query($remark_sql);	
		}
    }
    
    public function getCifStatus($customer_id)
    {
        $data = array(
            'rbl_status' => 0,
            'rbl_balance' => 0
        );
        // check customer credit's last udpated time
        $sql = "SELECT 
                    credit_status,
                    credit_balance,
                    TIMESTAMPDIFF(HOUR, last_updated, NOW()) as hour_diff
                FROM 
                    " . DB_PREFIX . "customer_credit 
                WHERE
                    customer_id = '" . (int)$customer_id ."'
                    AND
                    type = 'RBL'
                ";
        
        $result = $this->_db->query($sql);
        if(!empty($result->num_rows))
        {
            $hour_diff = (int)$result->row['hour_diff'];
            //if balance udpated in more than 2 hour, call api
            if( $hour_diff > 2 )
            {
                //call cifStatus API and update customer credit data
                $this->checkCustomerCIFStatus($customer_id);
                //get updated credit status and balance data
                $sql_cb = "SELECT 
                            credit_status,
                            credit_balance
                        FROM 
                            " . DB_PREFIX . "customer_credit 
                        WHERE
                            customer_id = '" . (int)$customer_id ."'
                            AND
                            type = 'RBL'
                        ";
        
                $credit_query = $this->_db->query($sql_cb);
                if ($credit_query->num_rows) {
                    $data['rbl_status'] = (int)$credit_query->row['credit_status'];
                    $data['rbl_balance']= (float)$credit_query->row['credit_balance'];
                }
            }else{
                $data['rbl_status'] = (int)$result->row['credit_status'];
                $data['rbl_balance'] = (float)$result->row['credit_balance'];
            }
        }

        return $data;
    }


}