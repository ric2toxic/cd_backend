 <?php


require_once(DIR_SYSTEM.'library/operations/orders/order_payment.php');

/**
 * 	Base class for the PaymentGateway
 * 	@author @Garvit Joshi
 */
class PaymentGatewayBase
{	
	public $registry;
	public $load;
	public $db;
    public $config;
    public $user;
    
    // Payment gateway identifier (good practice to provide a meaningful name in child classes)
	protected $paymentgateway = '';

	public function __construct($registry) {
        
        if (method_exists($registry, 'get')) {
            $this->registry = $registry;
            $this->db = $registry->get('db');
            $this->load = $registry->get('load');
            $this->config = $registry->get('config');
            $this->user = $registry->get('user');
        } else {
            $this->registry = $registry;
            $this->db = $registry->db;
            $this->load = $registry->load;
            $this->config = $registry->config;
            $this->user = $registry->user;
        }
	}

	public function doAction($data){

	}

	/**
	 *	checkPaymentStatus
	 *	@info: this function check the payment status of an order
	 *	@param  array $data Array->{$order_id, sub order_id}
	 *	@return Payment Status of an order like pending, successfull
	 */
	protected function checkPaymentStatus($data) {
		
	}

	/**	
	 *	generatePaymentLink
	 *	@info: this function Generate payment link of an order
	 *	@param 	array $data Array->{$order_id, $amount}
	 *	@return Payment successfull or not
	 */
	protected function generatePaymentLink($data){}

	/**
	 *	paymentRefund
	 *	@info: this function refund of an order
	 *	@param 	$refund_id or order_id
	 *	@return Process Refund of an order like pending, successfull
	 */
	public function paymentRefund($data){}

	protected function getPostAction($data){

	}

	protected function notifyBySms($data) {
		
	}

	protected function notifyByEmail($data) {
		
	}

	protected function getCurl($data) {

	}

	protected function postCurl($data) {

	}

	protected function getOrderDetail($data) {
		$selector = array('order' => array());
		$order_detail = OrderInfo::getOrderInfo($this->db, $data['order_id'], '', $selector)['order'];
        $order_detail['new_order_no'] = $this->checkOrderNoExistInOrderPaymant($order_detail);
        $order_detail['amount'] = $data['amount'];
        return $order_detail;
	}

	protected function checkOrderNoExistInOrderPaymant($order_info){
    	$sql = "SELECT max(order_no) 
    				FROM `".DB_PREFIX."order_payment` 
    				WHERE order_no like '". $this->db->escape($order_info['order_no']) ."%'";
    	$results = $this->db->query($sql)->row;

    	if(!empty($results['max(order_no)'])){
    		$character = substr($results['max(order_no)'], -1);
    		if(is_numeric($character)){
    			$results['max(order_no)'] = $results['max(order_no)'].'a';
    		}else{
    			$results['max(order_no)']++;
    		}
    	}else{
    		$results['max(order_no)'] = $order_info['order_no'];
    	}
    	return $results['max(order_no)'];
    }
    /**
	*	setGeneratePaymentLinkToDb
	*	@param 	array $order_info
	*	@param 	json $citrus_response
	*/
    protected function setGeneratePaymentLinkToDb($response){

    	$order_no = $this->checkOrderNoExistInOrderPaymant($response);

    	$payable_amt 	= 0;
        $paid_amt 		= (float)$response['amount'];
        
    	//Define data array 
    	$data = array(); 
    	$data['order_id'] 			= (int)$response['order_id'];
    	$data['merchant_txn_id'] 	= $response['merchant_txn_id'];
    	$data['order_no'] 			= $order_no;
    	$data['txn_status'] 		= 'pending';
    	$data['payment_mode'] 		= '';	
    	$data['amount'] 			= (float)$paid_amt;
    	$data['txn_date_time'] 		= '';
      if(!empty($response['txn_date_time'])) {
        $data['txn_date_time'] = $response['txn_date_time'];
      }
    	$data['date_added'] 		= 'NOW()';
    	$data['payment_gateway'] 	= $response['payment_gateway'];
    	$data['successfull'] 		= '0';
    	$data['reference'] 			= '';
      if(!empty($response['reference'])) {
        $data['reference'] = $response['reference'];
      }
    	$data['payment_link'] 		= $response['payment_link'];
    	$data['json_format'] 		= $response['serialize_response'];
    	$data['user_id'] 			= (int)$response['user_id'];
		
		OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table
    }

    /**
	*Public method to update oder payment details in to DB based on handling multiple responses against single transation 
	* @param $data (array) : key-value pair
	*Case1: merchant_txn_id is empty in DB, Update that row 
	*Case2: merchant_txn_id is not empty in DB and  
	*  merchant_txn_id = $data['TxId'] and 
    *  $data['TxStatus'] = success 
    *  then Update that row 
	*Case3: merchant_txn_id is not empty in DB and  
	*  merchant_txn_id = $data['TxId'] and 
    *  $data['TxStatus'] != success 
    *  then Copy that row and update status for new row
	*Case4: merchant_txn_id is not empty in DB and  
    *  merchant_txn_id != $data['TxId'] 
    *  then Copy that row and update status for new row
	*and also Sends Email to Operations and Accounts
    *and Sends SMS to Customer
    */
    public function updatePaymentDetailsIntoDb($controller, $data){

    	if(empty($data['successfull'])) {
        	$data['successfull']	=	0;
    	}
        
    	if($data['TxStatus'] == 'SUCCESS') {
    		$data['successfull']	=	1;
    	}
        
        $data['reference']		=	$data['TxMsg'];
        
    	try{
    		// Start transaction
            $this->db->query( " START TRANSACTION " );
            
            // Upi payment method
            if(isset($data['payment_gateway']) 
              && (strtolower($data['payment_gateway']) == 'upi')) {
            	// update single row
			    $sql = "UPDATE
                        ".DB_PREFIX."order_payment 
                        SET merchant_txn_id = '".$this->db->escape($data['TxId'])."',
                        txn_status = '".$this->db->escape(strtolower($data['TxStatus']))."',
                        json_format = '".$this->db->escape($data['json_format'])."',
                        successfull = '".$this->db->escape($data['successfull'])."'";
          if(!empty($data['payment_mode']) && !empty($data['txn_date_time'])) {
            $sql .= ", payment_mode = '".$this->db->escape($data['payment_mode'])."',
                      txn_date_time = '".$this->db->escape($data['txn_date_time'])."'";
          }
          $sql .= " WHERE payment_id = '".$data['payment_id']."'";              
            	$update_result = $this->db->query($sql);

            	if($data['successfull']) {
                    $data_advance = array('order_id'=>$data['order_id'], 'payment_id'=>$data['payment_id'], 'user_id'=>0);
                    OrderPayment::insertAdvanceVoucher($this->db, $data_advance);
                }

                $this->db->query( " COMMIT " );

            } else if(isset($data['payment_gateway']) && strtolower($data['payment_gateway']) == 'razorpay') {
                $check_txn_id = $data['TxId'];
                
                /** offline payment links have invoice_id as merchant_txn_id (when in pending status) **/
                if(!empty($data['invoice_id'])) {
                    $check_txn_id = $data['invoice_id'];
                }
                
                /** check if same txn status row is already inserted **/
                $sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
		    			WHERE 
		    			(merchant_txn_id ='". $this->db->escape($check_txn_id) ."'
                         OR reference LIKE '%". $this->db->escape($check_txn_id) ."%')
		    			AND (txn_status = '". $this->db->escape($data['TxStatus']) ."' OR (txn_status IN ('captured') AND successfull = '1'))
		    			AND payment_gateway = 'razorpay'";
                $query_result = $this->db->query($sql);
                
                if($query_result->num_rows == 0) {
                    
                    // Fetch order payment details for txn_id (can have multiple rows)
                    $sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
    		    			WHERE 
    		    			(merchant_txn_id ='". $this->db->escape($check_txn_id) ."'
                             OR reference LIKE '%". $this->db->escape($check_txn_id) ."%')
    		    			AND payment_gateway = 'razorpay'";
                    $query_result = $this->db->query($sql);
                    
                    $is_copy_row = 1; // to differentiate if row is to updated(in case of pending status) or inserted(using copyRow)
                    
                    $order_id = 0;
                    $payment_id = 0;
                    
                    if($query_result->num_rows) { // if op detail exists
                        $order_payments = $query_result->rows;
                        
                        /** Check for all rows
                        ** if 'pending' status found then update that row
                        ** otherwise choose a row -> copy it in db with new updated data **/ 
                        foreach ($order_payments as $row_id => $order_payment_detail) {
                            if($order_payment_detail['txn_status'] == 'pending') {
                                $sql = "UPDATE ".DB_PREFIX."order_payment 
                                            SET merchant_txn_id = '".$this->db->escape($data['TxId'])."',
                                            txn_status = '".$this->db->escape(strtolower($data['TxStatus']))."',
                                            json_format = '".$this->db->escape($data['json_format'])."',
                                            successfull = '".$this->db->escape($data['successfull'])."', 
                                            payment_mode = '".$this->db->escape($data['payment_mode'])."',
                                            txn_date_time = '".$this->db->escape($data['txn_date_time'])."'
                                        WHERE payment_id = '".$order_payment_detail['payment_id']."'";
                            	$update_result = $this->db->query($sql);
                                $is_copy_row = 0;
                                $order_id = $order_payment_detail['order_id'];
                                $payment_id = $order_payment_detail['payment_id'];
                                break;
                            }
                            $key_in = array('order_no'			=> $order_payment_detail['order_no'],
    			                            'merchant_txn_id'	=> $order_payment_detail['merchant_txn_id'], 
    	                                    'txn_status'        => $order_payment_detail['txn_status']
    			                           );
    			    		$key_out = array();
    			            $key_out[] = array('order_no'			=> $order_payment_detail['order_no'],
                                               'merchant_txn_id'	=> $this->db->escape($data['TxId']), 
    	                                       'txn_status'         => $this->db->escape($data['TxStatus']),
                                               'json_format'        => $this->db->escape($data['json_format']),
                                               'successfull'        => $this->db->escape($data['successfull']),
                                               'payment_mode'       => $this->db->escape($data['payment_mode']),
                                               'txn_date_time'      => $this->db->escape($data['txn_date_time'])
    			                              );
    			            $skip_fields = array('payment_id');
                            $order_id = $order_payment_detail['order_id'];
                        }
                        
                        if($is_copy_row) { // if no row with pending status found
                            $payment_id = $this->db->copyRow( DB_PREFIX . 'order_payment', $key_in, $key_out, false, $skip_fields);
                        }
                    }
                    
                    if($data['successfull']) { // insert Advance Vouchers
                        $data_advance = array('order_id'=>$order_id,
                                              'payment_id'=>$payment_id,
                                              'user_id'=>0);
                        OrderPayment::insertAdvanceVoucher($this->db, $data_advance);
                    }
                    $this->db->query( " COMMIT " );
                }
            } else {
		    	//Fetch order payment details against order_id for citrus (payment gateway)
	            //Check same trxn entry already exist or not
		    	$sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
		    			WHERE 
		    			order_no ='". $this->db->escape($data['OrderNO']) ."' AND 
		    			merchant_txn_id ='". $this->db->escape($data['TxId']) ."' AND
		    			txn_status = '". $this->db->escape($data['TxStatus']) ."' AND
		    			payment_gateway = 'citrus'";
		    	$query_result = $this->db->query($sql);
		    	
		    	if($query_result->num_rows == 0){
		    		$sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
		    			WHERE 
		    			order_no ='". $this->db->escape($data['OrderNO']) ."' AND 
		    			payment_gateway = 'citrus'";
		    		$op_details = $this->db->query($sql)->row;
		    		if(!empty($op_details)){
			    		$key_in = array('order_no'			=> $op_details['order_no'],
			                            'merchant_txn_id'	=> $op_details['merchant_txn_id'], 
	                                    'txn_status'        => $op_details['txn_status']
			                           );
			    		$key_out = array();
			            $key_out[] = array('order_no'			=> $data['OrderNO'],
			                               'merchant_txn_id'	=> $data['TxId'], 
	                                       'txn_status'         => $data['TxStatus']
			                              );
			            $skip_fields = array('payment_id');

			            //Checks trxn_id(response) is not match with trxn_id(DB)
			    		if($op_details['merchant_txn_id'] != $data['TxId']){
			    			//Copy existing row, and update value in new row
		            		$this->db->copyRow( DB_PREFIX . 'order_payment', $key_in, $key_out, false, $skip_fields);
			    		}else{
			    			//Checks trxn_status(response) is not match with trxn_status(DB)
			    			if($op_details['txn_status'] != $data['TxStatus']){
			    				//Copy existing row, and update value in new row
		            			$this->db->copyRow( DB_PREFIX . 'order_payment', $key_in, $key_out, false, $skip_fields);
			    			}
			    		}
			    		//Update row in DB
				    	$this->updateOrderPayment($data);

				    	$this->db->query( " COMMIT " );
			    	}//End of Inner IF block

		    	}
	    	}
	    	
    	}catch(Exception $e){
            $this->db->query( " ROLLBACK " );
            echo $e->getMessage();
        }

        if ( $data['TxStatus'] == 'SUCCESS' 
              || strtolower($data['TxStatus']) == 'completed' 
              || strtolower($data['TxStatus']) == 'captured') {
            // Op_detail = Order Payment Detail
            $op_detail = $this->getDataByOrderNO($data['OrderNO']);

            $data['payment_id']         = $op_detail['payment_id'];
            $data['order_id']           = $op_detail['order_id'];
            $data['date_added']         = $op_detail['date_added'];
            $data['payment_gateway']    = $op_detail['payment_gateway'];
            $data['successfull']        = $op_detail['successfull'];
            $data['reference']          = $op_detail['reference'];
            $data['payment_link']       = $op_detail['payment_link'];

            $this->sendSuccessEmailToOperationAndAccounts($data);
            $order_no = OrderInfo::getOrderNo($controller->db, $op_detail['order_id']);
            //To send SMS
            OrderPayment::sendAdvanceSMS($controller, $op_detail['order_id'], $order_no);
        }
    }

    /**
	* public function to update in oc_order_payment
	* @param $data array
	* @return void
	* @author Nishu
    */
    public function updateOrderPayment($data){
        
    	$sql = "SELECT payment_id, order_id FROM " . DB_PREFIX . "order_payment 
    			WHERE
    				order_no = '". $this->db->escape($data['OrderNO']) ."' AND
    				payment_gateway = 'citrus'	
    			ORDER BY 
    				payment_id DESC
    			LIMIT 0,1";
    	$row = $this->db->query($sql)->row;
        
    	if(!empty($row)){
	    	$sql = 	"UPDATE `" . DB_PREFIX . "order_payment`
	    				SET
		    				merchant_txn_id = '". $this->db->escape($data['TxId']) . "',
		    				txn_status 		= '". $this->db->escape($data['TxStatus']) ."',
		    				payment_mode 	= '". $this->db->escape($data['paymentMode']) ."',
		    				txn_date_time 	= '". $this->db->escape($data['txnDateTime']) ."',
		    				successfull 	= '". (int)$data['successfull']."',
		    				reference 		= '". $this->db->escape($data['reference']) ."',
		    				json_format 	= '". $this->db->escape($data['json_response']) ."'
						WHERE 
							payment_id 		= '". $row['payment_id'] ."'";
			$this->db->query($sql);
		}
                if($data['successfull']) {
                    $data_advance = array('order_id'=>$row['order_id'], 'payment_id'=>$row['payment_id'], 'user_id'=>0);
                    OrderPayment::insertAdvanceVoucher($this->db, $data_advance);
                    
                }
                
		
    }

    public function insertRefundPaymentIntoDb($order_info){ 
    	$payable_amt 	= 0;
        $paid_amt 		= - abs($order_info['refund_amount']);
        
        //Define data array 
    	$data = array(); 
    	$data['order_id'] 			= (int)$order_info['order_id'];
    	$data['merchant_txn_id'] 	= $order_info['merchant_txn_id'];
    	$data['order_no'] 			= $order_info['order_no'];
    	$data['txn_status'] 		= 'REFUND SUCCESS';
    	$data['payment_mode'] 		= '';	
    	$data['amount'] 			= (float)$paid_amt;	
    	$data['txn_date_time'] 		= '';
    	$data['date_added'] 		= 'NOW()';
    	
    	if(isset($order_info['txn_status'])){
    		$data['txn_status'] 	= $order_info['txn_status'];    		
    	}

    	if(isset($order_info['trxn_id'])){
    		$data['trxn_id'] 		= (int)$order_info['trxn_id'];    		
    	}
    	if(isset($order_info['txn_date_time'])){
    		$data['txn_date_time'] 	= $order_info['txn_date_time'];    		
    	}
    	if(isset($order_info['payment_gateway'])){
    		$data['payment_gateway'] 	= $order_info['payment_gateway'];    		
    	}
    	if(isset($order_info['bank_transfer_mode'])){
    		$data['bank_transfer_mode'] 	= $order_info['bank_transfer_mode'];    		
    	}
    	if(isset($order_info['payment_mode'])){
    		$data['payment_mode'] 	= $order_info['payment_mode'];    		
    	}
    	if(isset($order_info['successfull'])){
    		$data['successfull'] 	= $order_info['successfull'];
    	}
    	if(isset($order_info['payment_link'])){
    		$data['payment_link'] 	= $order_info['payment_link'];
    	}
    	if(isset($order_info['reference'])){
    		$data['reference'] 		= $order_info['reference'];
    	}else{
    		$data['reference'] 		= '';
    	}
    	$data['json_format'] 		= $order_info['json_format'];
    	$data['user_id'] 			= (int)$order_info['user_id'];
    	
		$order_payment_id = OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table
		
		return $order_payment_id;
    }
    
	/**
	 * This Email call when user or customer pay some amount via paymentgateway.
	 **/
    public function sendSuccessEmailToOperationAndAccounts($data){
    	$mail = new PHPMailer();
		
		$mail->isSMTP();
		$mail->Host = $this->registry->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->registry->config->get('config_mail_smtp_port');;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = $this->registry->config->get('config_mail_smtp_username');
		$mail->Password = $this->registry->config->get('config_mail_smtp_password');

		$mail->addAddress(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
        $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
		$mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
		$mail->Subject = $data['amount'].' received for order no: '. $data['OrderNO'].' in '. $data['payment_gateway'];
		$html = '<div> '. $data['currency'] .' '. $data['amount'] .' Payment received in '. $data['payment_gateway'] .' via '. $data['paymentMode'] .' with TxId : '. $data['TxId'] .' on '. $data['txnDateTime'] .' </div>';
		$mail->msgHTML($html);
		$mail->send();
    }

	public function sendInvalidAttemptEmailInPaymentGateway($data, $payment_gateway){
    	$mail = new PHPMailer();
		
		$mail->isSMTP();
		$mail->Host = $this->registry->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->registry->config->get('config_mail_smtp_port');;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = $this->registry->config->get('config_mail_smtp_username');
		$mail->Password = $this->registry->config->get('config_mail_smtp_password');

		$mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
		$mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);

		$mail->Subject = 'Invalid Payment attempt in'.$payment_gateway.' Payment Gateway';

		$html = '<div>  Server: '. $data['server'] .'<br> Request: '. $data['request'] .' <br> Post: '. $data['post'] .' </div>';
		$mail->msgHTML($html);
		$mail->send();
    }
    
	public function errorResponse($response, $customer_info, $data){
    	$mail = new PHPMailer();
		
		$mail->isSMTP();
		$mail->Host = $this->registry->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->registry->config->get('config_mail_smtp_port');;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = $this->registry->config->get('config_mail_smtp_username');
		$mail->Password = $this->registry->config->get('config_mail_smtp_password');
		
		$mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
		$mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);

		$mail->Subject = $data['payment_gateway'] .' Payment ='. $data['responseMsg'] .' Due to '. $data['specialMsg'] .' Order no = '. $customer_info['order_no'] ;
		
		$html = '<div> '. $data['payment_gateway']  .' Payment ='. $data['responseMsg'] .' Due to '. $data['specialMsg'] .' Order no = '. $customer_info['order_no'] .' </div>';
		$html .= '<div> Customer Order ID ='. $customer_info['order_id'] .'<br>';
		$html .= 'Customer Order No ='. $customer_info['order_no'] .'<br>';
		$html .= 'Customer Order Amount ='. $customer_info['amount'] .'<br>';
		$html .= 'ERROR: '. json_encode($response) .' </div>';
		
		$mail->msgHTML($html);
		$mail->send();
    
    // Create log entry for error
    $log_data = array();
    $log_data['error_data'] = serialize(json_encode($response));
    $log_data['order_id'] = $customer_info['order_id'];
    $log_data['payment_gateway'] = $data['payment_gateway'];
    $this->logPaymentGatewayError($log_data);
    }

    public function getMerchantTotalRefundAmount($merchantTxnId) {
		$sql = "SELECT SUM(amount) as amount 
    				FROM `".DB_PREFIX."order_payment` 
    				WHERE amount < 0
                      AND merchant_txn_id = '" .$this->db->escape($merchantTxnId). "' 
                      AND successfull = '1'";
    	$results = $this->db->query($sql)->row['amount'];
    	return $results;
    }

    public function getDataByOrderNO($order_no) {
    	$sql = "SELECT payment_id, 
                        order_id, 
                        order_no,
                        date_added, 
                        payment_gateway, 
                        successfull, 
                        reference, 
                        payment_link
                FROM `".DB_PREFIX."order_payment` 
                WHERE order_no = '". $this->db->escape($order_no) ."'";
        $results = $this->db->query($sql)->row;
        return $results;
    }

    public function getOrderDetailByMerchant_txn_id($merchant_txn_id, $successfull = 1) {
    	$sql = "SELECT * FROM `".DB_PREFIX."order_payment` 
                WHERE merchant_txn_id like '". $this->db->escape($merchant_txn_id) ."' 
                  AND successfull = ".(int)$successfull." 
                  AND payment_gateway = '" . $this->db->escape($this->paymentgateway) . "'";
        $results = $this->db->query($sql)->row;
        return $results;
    }
    
    public function getSuccessfullMerchantTxnIdWithBalance($order_id) {
        
        $sql = "SELECT merchant_txn_id, SUM(amount) as balance 
                FROM `".DB_PREFIX."order_payment` 
                WHERE order_id =  '". (int)$order_id . "' 
                  AND successfull = 1 
                  AND payment_gateway = '" . $this->db->escape($this->paymentgateway) . "' 
                GROUP BY merchant_txn_id 
                HAVING balance > 0 
                ORDER BY balance DESC ";
        $result = $this->db->query($sql);
        
        if ($result->num_rows) {
            return $result->rows;
        }
        
        // if we are here, that means no rows received
        return array();
    }

    /**
    * Method to calculate payment type
    * @param  $payable_amt $paid_amt 
    * @return string
    * @author Nishu
    */
    public static function getCalculatePaymentType($db,$order_id, $payable_amt=0, $paid_amt=0) {

    	if($payable_amt == 0){
    		$sql = "SELECT total FROM " . DB_PREFIX . "order WHERE order_id = ". (int)$order_id;
	        $result = $db->query($sql)->row;
	        $payable_amt = (float)$result['total'];
	    }

    	if($paid_amt < 0){						//check if paid amount is negative
    		return 'refund';
    	}else if($paid_amt >= $payable_amt){	//check if paid amount is greater then or equals to payable amount
    		return 'full';
    	}else if($paid_amt < $payable_amt){		//check if paid amount is less then payable amount
    		return 'advance';
    	}else{									//Others
    		return 'others';	
    	}
    }

    /**
    * Public Function to get Refundable balance in citrus
    * @param  $order_id
    * @return $data array
    * @author Nishu, Sept, 2017
    **/
    public function getPossibleRefundableAmtInPG($order_id){
        $balance_amount    = 0;
        $sql = "SELECT 
                    *
                FROM
                    oc_order_payment
                WHERE
                    order_id = ". (int)$order_id ." AND 
                    successfull = 1 AND 
                    payment_gateway = '". $this->db->escape($this->paymentgateway) ."' 
                ORDER BY amount DESC
                ";                  
        $result = $this->db->query($sql);
        $data = array();
        $data['refund'] 	= 0;
        $data['balance'] 	= 0;
        $data['row_data']   = array();
        if($result->num_rows){
        	$data['row_data'] = $result->rows;
            foreach ($result->rows as $key => $value) {
				
				$amount = (float)$value['amount'];
				
            	if($amount > 0 ){
            		$data['balance'] += $amount;
            	}else if($amount < 0 ){
            		$data['refund']  += $amount;
            	}
            }
        }
        $data['refund_balance']	= $data['balance'] + $data['refund'];
    	return $data;
    }

    /**
     * Public function to add trxn_details dynamically
     * @param: $data Array
     * @return: void
     * @author: Nishu, Nov 2017
    */
    public function addTrxnDetails($data){
        $set_sql = " 
                    trxn_for          = '". $this->db->escape($data['trxn_for']) ."',
                    trxn_for_id       = '". (int)$data['trxn_for_id'] ."'
                   ";

        if( isset($data['tr_id']) && !empty($data['tr_id']) ){
            $set_sql .= ", tr_id   = '". (int)$data['tr_id'] ."' ";
        }

        if(isset($data['trxn_done'])){
            $set_sql .= ", trxn_done   = '". $this->db->escape($data['trxn_done']) ."' ";
        }
        if(isset($data['trxn_amount'])){
            $set_sql .= ", trxn_amount = '". (float)$data['trxn_amount'] ."' ";
        }
        if(isset($data['trxn_utr_internal'])){
            $set_sql .= ", trxn_utr_internal = '". $this->db->escape($data['trxn_utr_internal']) ."' ";
        }
        if(isset($data['trxn_utr'])){
            $set_sql .= ", trxn_utr   = '". $this->db->escape($data['trxn_utr']) ."' ";
        }
        if(isset($data['trxn_utr_date'])){
            $set_sql .= ", trxn_utr_date  = '". $data['trxn_utr_date'] ."' ";
        }else{
        	$set_sql .= ", trxn_utr_date  = Now() ";
        }
        if(isset($data['trxn_bank'])){
            $set_sql .= ", trxn_bank  = '". $this->db->escape($data['trxn_bank']) ."' ";
        }
        if(isset($data['trxn_response'])){
            $set_sql .= ", trxn_response  = '". $this->db->escape($data['trxn_response']) ."' ";
        }
        if(isset($data['trxn_date_added'])){
            $set_sql .= ", trxn_date_added  = ". $data['trxn_date_added'] ." ";
        }
        $sql = "INSERT INTO oc_trxn_details SET ". $set_sql;
        $this->db->query($sql);
        return $this->db->getLastId();
    }

    /**
     * Public function to check existing trxn_details
     * @param: $data Array
     * @return: void
     * @author: Nishu, Nov 2017
    */
    public function checkExistingTrxnDetails($data){
    	$sql = "
    	        SELECT * FROM oc_trxn_details
    	        WHERE
    	           trxn_for = '".$this->db->escape($data['trxn_for'])."'
    	           AND trxn_for_id = '". (int)$data['trxn_for_id'] ."'
    	           AND trxn_done = '". $this->db->escape($data['trxn_done']) ."'
    	           AND trxn_amount = '". (float)$data['trxn_amount'] ."'
    	       ";
    	if(isset($data['tr_id']) && !empty($data['tr_id'])){
    		$sql .= " AND tr_id = '".(int)$data['tr_id']."' ";
    	}
    	
    	$result = $this->db->query($sql);
    	if($result->num_rows > 0){
    		return $result->row['id'];
    	}else{
    		return 0;
    	}
    }

    /**
     * Public function to get Sum of all successfull tentative refund done by tentative_refund_id
     * @param: $tr_id Integer
     * @return: Float
     * @author: Nishu, March 2018
    */
    public static function getTtlAmtOfSuccessTentativeRefundDone($db, $tr_id){
    	$sql = "
    	        SELECT SUM(trxn_amount) as amount FROM oc_trxn_details
    	        WHERE
    	           tr_id = '". (int)$tr_id ."'
    	           AND trxn_done IN ('BANK_REQUESTED', 'BANK_PROCESSED', 'BANK_SUCCESS', 'NOT_APPLICABLE')
    	       ";
    	//Execute Query
    	$result = $db->query($sql);
    	
    	if($result->num_rows > 0){
    		return $result->row['amount'];
    	}else{
    		return 0;
    	}
    }
    
    /**
     * Public function to log errors for payment gateways
     * @param: error Response, customer data
     * @author: Anurag Jain, 26 Apr 2018
    */
    public function logPaymentGatewayError($log_data) {
      $success = false;
      $error_data = $log_data['error_data'];
      $order_id = $log_data['order_id'];
      $payment_gateway = $log_data['payment_gateway'];
      
      $sql = "INSERT INTO ".DB_PREFIX."payment_gateway_error_logs
                SET error_data = '".$this->db->escape($error_data)."',
                    order_id = '".(int)$order_id."',
                    payment_gateway = '".$this->db->escape($payment_gateway)."',
                    date_added = NOW()";
      $result = $this->db->query($sql);
      if($this->db->getLastId()) {
        $success = true;
      }
      return $success;
    }

    /**
     * Public function to get positive payment(s) total by OrderId
     * @param: $order_id Int/Array
     * @author: Nishu, Aug 2018
    */
    public function getPositiveAmountReceivedByOId($order_id) {
      $data = array();
      
      if(!empty($order_id)){//Not empty Check
        
        if(is_array($order_id)){ //Check If passing multiple order_ids as array
            $oid = implode(',', $order_id);
        }else{
            $oid = $order_id;
        }

        $sql = "
                SELECT 
                    order_id,
                    SUM(amount) AS total_amount 
                FROM
                    " . DB_PREFIX . "order_payment
                WHERE
                    order_id IN (". $oid .")
                    AND amount > 0
                    AND successfull = 1
                GROUP BY 
                    order_id
               ";
        $query = $this->db->query($sql);

        //Check If any amount is received against given order list
        if($query->num_rows > 0){
            $data = $query->rows;
            $data = array_combine(array_column($data, 'order_id'), $data);
        }

      }//End of not empty Check

      return $data;
    }

    /**
     * private Method to order deatils 
     * @param: $oid, $selector
     * @return: Array
     * @author: Nishu, Aug 2018
    */
    private function setOrderInfo($oid, $order_info){
        $selector = array(
                    'order'=> array('select' => array('order_id', 'payment_code', 'total')) 
                   );
        //Get OrderInfo details
        $order_detail = OrderInfo::getOrderInfo($this->db, $oid,'',$selector);

        //Set values
        $order_info[$oid]['total']        = $order_detail['order']['total'];
        $order_info[$oid]['payment_code'] = $order_detail['order']['payment_code'];

        return $order_info;
    }

    /**
     * private Method to set order's total received amounts
     * @param: $oid, $order_info
     * @return: Array
     * @author: Nishu, Aug 2018
    */
    private function setOrderTotalReceivedAmount($order_ids, $order_info){
        if(!empty($order_ids)){
            $orders_rcv_amt = $this->getPositiveAmountReceivedByOId($order_ids);
            if(!empty($orders_rcv_amt)){
                foreach ($orders_rcv_amt as $oid => $value) {
                    $order_info[$oid]['total_received'] = $value['total_amount'];
                }
            }
        }
        return $order_info;
    }


    /**
     * Public function to get Balance amount on behalf of customer,
     *     Calculate ampunt to generate payment link
     * @param : $order_info Array, Multiple Order details
     *             Array keys are: order_id, payment_code, total, total_received, 
     * @return: $order_info Array, Order details
     * @author: Nishu, Aug 2018
    */
    public function calculateTotalReceivedAndPendingBalanceAmount($order_info) {

        if(!empty($order_info)){//Not empty Check
            $order_ids = array();
            //Loop over order_info, for order wise details
            foreach ($order_info as $data) {
                $oid = $data['order_id'];

                //if order details missing
                if(empty($data['payment_code']) || empty($data['total'])){
                    //set Order details
                    $order_info = $this->setOrderInfo($oid, $order_info);
                }

                //If order's total_received_amount is not given for that set order_id
                if(empty($data['total_received'])){
                    $order_ids[] = $oid;
                }
            }

            //Order_ids for which order's recevied total amount is not set
            $order_info = $this->setOrderTotalReceivedAmount($order_ids, $order_info);

            //Calculate Order's balance amount
            $order_info = $this->calculateOrderBalance($order_info);

        }

        return $order_info;
    }

    /**
     * Public function to get Balance amount for order(s)
     *     Calculate ampunt to generate payment link
     * @param : $order_info Array, Single/Multiple Order details
     * @return: $order_info Array, Order details
     * @author: Nishu, Aug 2018
    */
    public function calculateOrderBalance($order_info) {
        //Orderinfo Empty check
        if(!empty($order_info)){

            foreach ($order_info as $oid => $data) {
                if(empty($data['total_received'])){
                    $data['total_received'] = 0;
                    $order_info[$oid]['total_received'] = $data['total_received'];
                }

                //Check if order's payment code is COD 
                if(strtolower($data['payment_code']) == 'cod' ){
                    $order_info[$oid]['balance'] = MAX(MAX($data['total']*0.1, 1000) - $data['total_received'], 0);
                }else{//If order id Prepaid order
                    $order_info[$oid]['balance'] = MAX($data['total'] - $data['total_received'], 0);
                }
            }
        }

        return $order_info;
    }

    /**
     * Public methos to get payment links order wise
     * @author: Nishu, Aug 2018
    */
    public function getPaymentLinkOrderWise($order_ids){
        $order_data = array();
        if(!empty($order_ids)){
            
            if(is_array($order_ids)){
                $order_ids = implode(',', $order_ids);
            }
            $sql = "
                    SELECT 
                        oop1.order_id, TRIM(oop1.payment_link) AS payment_link
                    FROM
                        oc_order_payment AS oop1
                    INNER JOIN
                        (SELECT 
                            MAX(payment_id) AS payment_id
                        FROM
                            oc_order_payment
                        WHERE
                            order_id IN (". $order_ids.")
                                AND successfull = 0
                                AND payment_gateway IN ('". implode("','", OFFLINE_INVOICING_PAYMENT_GATEWAYS) ."')
                                AND TRIM(payment_link) != ''
                                AND TRIM(payment_link) IS NOT NULL
                        GROUP BY order_id
                        ) AS oop2 ON oop1.payment_id = oop2.payment_id
                   ";

            $result = $this->db->query($sql);
            
            if($result->num_rows > 0){
                $order_data = $result->rows;
                $order_data = array_combine(array_column($order_data, 'order_id'), $order_data);
            }
        }

        return $order_data;
    }


}//End of Class
