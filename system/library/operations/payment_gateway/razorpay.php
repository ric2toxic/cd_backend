<?php
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/payment_gateway_base.php');
/**
 * 	Razorpay
 * 	@author @Garvit Joshi
 **/
class Razorpay extends PaymentGatewayBase 
{
	private $key_id;
	private $key_secret;

	public function __construct($registry) {
        
		parent::__construct($registry);
        $this->paymentgateway = 'razorpay';

		$this->key_id       = $registry->config->get('razorpay_key_id');
        $this->key_secret   = $registry->config->get('razorpay_key_secret');
        
        if(!(strtolower(SITE_ENVIRONMENT) == 'production')) { // Sandbox/staging account
            $this->key_id       = "rzp_test_kPLVqzulyibTrP";
            $this->key_secret   = "nqAHhJaG0zoR1F7p5ChU4vBd";
        }
    }

	/**
     *  doAction
     *  @info doAction always call because this method know that which method to call  
     *  @param array $data
     *  @return 
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
     *  getPostAction
     *  @info getPostAction always call in last and this will decide that which class to call 
     *  @param  array $data
     *  @return   
     */
    public function getPostAction($data) {
        return new $data();
    }

	/**
	 *	generatePaymentLink
	 *	@param 	array $data
	 *	@return status of that Payment
	 */
    public function generatePaymentLink($data) {
        if(!empty($data['order_id'])){
        	/** 
        	 *Get the Order Detail and check order_no is exist or not in oc_order_payment 
			 *if exist then add some alphabatic charector in order_no
			 **/

        	$data['amount']        = $data['amount']*100; // In Razorpay Amount Always in paise.
            $data['order_total']   = $data['order_total']*100;
        	$order_info = parent::getOrderDetail($data);
						 if(!empty($data['call_from_backend'])) {
							 $order_info['call_from_backend'] = $data['call_from_backend'];
						 }
						 //$order_info['payment_link_secret_key'] = base64_encode(rand()."_".$order_info['new_order_no']."_".time());
             /**
              *This method make Json of order information
              **/
            $json_request = $this->_createJsonForGeneratePaymentLink($order_info); 
            $jresponse = json_decode($json_request);
            //echo "Json: "; echo "<pre>"; print_r($jresponse); echo "</pre>";
            /**
             *call Razorpay Api For Generate Invoice Link and send to customer for payment
             **/
            $json_response = $this->_callRazorpayApiForGenerateInvoiceLink($json_request);
            $response = json_decode($json_response);
            //echo "Razorpay Response: "; echo "<pre>"; print_r($json_response); echo "</pre>"; 
			
            if(!empty($response->id)) {
                /**
                 *Insert data into oc_order_payment with success = 0
                 **/
                if(!isset($data['user_id'])){
                    $data['user_id'] = 0;
                }

                $value = array();
                $value['order_id']				= $response->notes->order_id;
                $value['merchant_txn_id'] 		= $response->id;
                $value['order_no'] 				= $response->notes->new_order_no;
                $value['amount'] 				= $response->amount/100;
                $value['payment_gateway'] 		= 'razorpay';
                $value['payment_mode'] 		= 'Razorpay';
                $value['payment_link'] 			= $response->short_url;
                $value['reference'] 		= 'Razorpay Payment Link - '.$response->id;
                $value['serialize_response'] 	= serialize($response);
                $value['user_id']               = $data['user_id'];
								$value['txn_date_time'] = gmdate("Y-m-d H:i:s", ($response->issued_at)+(330 * 60));

            	parent::setGeneratePaymentLinkToDb($value);

                $razorpay_response['responseMsg']       = 'SUCCESS';
                $razorpay_response['specialMsg']        = $response->short_url;
                $razorpay_response['payment_gateway']   = 'Razorpay';
								$razorpay_response['razorpay_txn_id'] = $response->id;
            } else {
                /**
                 * Here we send an email to wholesalebox for Failure Response in Razorpay
                 **/
                $razorpay_response['responseMsg']       = 'FALIURE';
                $razorpay_response['specialMsg']        = 'FALIURE';
                $razorpay_response['payment_gateway']   = 'Razorpay';
                parent::errorResponse($response, $data, $razorpay_response);
                // throw new Exception("BAD_REQUEST_ERROR in system/library/operations/payment_gateway/razorpay/generatePaymentLink.");
            }
            return $razorpay_response;
        } else {
            throw new Exception("order_id is empty in system/library/operations/payment_gateway/razorpay/generatePaymentLink.");
        }
    }

	/**
	 *	_createJsonForGeneratePaymentLink
	 *  @INFO   https://docs.razorpay.com/v1/page/invoices#v1invoices for make a invoice Razorpay json
	 *	@param 	array $order_detail
	 *	@return json  order_detail
	 */
	private function _createJsonForGeneratePaymentLink($order_detail) {
		$json = array();

        $json['customer'] = array();
        	//customer
        	$json['customer']['name']    			= isset($order_detail['firstname'])?$order_detail['firstname']:'';
        	$json['customer']['email']      		= isset($order_detail['email'])?$order_detail['email']:'';
        	$json['customer']['contact']			= $order_detail['telephone'];
        $json['line_items'] = array();
        	//Line Items 
        	$json['line_items'][0]['name'] 			= $order_detail['new_order_no'];
        	$json['line_items'][0]['description']	= 'Order_id: '. isset($order_detail['order_id'])?$order_detail ['order_id']:'';
        	$json['line_items'][0]['amount']   		= $order_detail['amount'];
        $date = new DateTime();
        $json['date'] 								= $date->getTimestamp();
        $json['currency'] 							= 'INR';//$order_detail['currency_code'];
        $json['notes'] = array();
        	//Notes
        	$json['notes']['order_id'] 				= $order_detail['order_id'];
        	$json['notes']['order_no'] 				= $order_detail['order_no'];
        	$json['notes']['new_order_no'] 			= $order_detail['new_order_no'];
        	//$json['notes']['invoice_no']			= $order_detail['invoice_no'];
        $json['sms_notify'] 						= "1";
		$json['email_notify'] 						= "1";
		$json['receipt']							= $order_detail['new_order_no'];
		$json['type']	= "link";
		$path = '';
		if(!empty($order_detail['call_from_backend'])) {
			$path = HTTPS_CATALOG;
		} else {
			$path = HTTPS_SERVER;
		}
		// $json['callback_url']	= $path."razorpay/callbackPaymentLink/".$order_detail['payment_link_secret_key'];
		// $json['callback_method'] = "get";
		
		$json_encode = json_encode($json);
        return $json_encode;
	}

	/**
	 *	_callRazorpayApiForGenerateInvoiceLink
	 *	@param 	json $json_request
	 *	@return json Razorpay Response  
	 */
	private function _callRazorpayApiForGenerateInvoiceLink($json_request) {
		$key_id        = $this->key_id;
        $key_secret    = $this->key_secret;

		$url = "https://api.razorpay.com/v1/invoices";

 		$headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
		
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL            	=> $url,
            CURLOPT_USERPWD			=> $key_id.':'.$key_secret,
            CURLOPT_POST           	=> true,
            CURLOPT_RETURNTRANSFER 	=> true,
            CURLOPT_SSL_VERIFYPEER	=> true,
            CURLOPT_HTTPHEADER     	=> $headers,
            CURLOPT_POSTFIELDS     	=> $json_request
        );
        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);

        if(curl_error($ch)) {
            echo 'error:' . curl_error($ch);
        }
        return $result;
	}

	/**
     *  getTransactionsByDate
     *  @param  array $info => {sync_solr, txnStartDate, txnEndDate}
     *  @return json Razorpay Response  
     */
	public function getTransactionsByDate($info) {
		
        $data = array();
        
        if(isset($info['txnStartDate'])) {
            $data['txnStartDate']   = strtotime($info['txnStartDate']);
        } else {
            $data['txnStartDate']   = strtotime('Oct 3, 2016');
        }
        if (isset($info['txnEndDate'])) {
            $data['txnEndDate']     = strtotime($info['txnEndDate']);
        } else {
            $data['txnEndDate']     = time();
        }

        $result = $this->_callRazorpayApiForSearchingTransactionsByDate($data);
        $json_decode = json_decode($result);

        if(isset($info['sync_solr']) && $info['sync_solr'] == true) {
            $PaymentTransactions = new PaymentTransactions($this->registry);
            $PaymentTransactions->addTranscationsToSolr($json_decode);
        }
        return $json_decode;
	}

    /**
     *  _callRazorpayApiForSearchingTransactionsByDate
     *  @param  array $data
     *  @return json  Razorpay Response
     */
    private function _callRazorpayApiForSearchingTransactionsByDate($data){
        $key_id        = $this->key_id;
        $key_secret    = $this->key_secret;

        $from_timestamp = $data['txnStartDate'];
        $to_timestamp   = $data['txnEndDate'];
        $url = "https://api.razorpay.com/v1/invoices?from=".$from_timestamp."&to=".$to_timestamp;
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL             => $url,
            CURLOPT_USERPWD         => $key_id.':'.$key_secret,
            CURLOPT_TIMEOUT         => 60,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYPEER  => true
        );
        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);

        if(curl_error($ch)) {
            echo 'error:' . curl_error($ch);
        }
        return $result;
    }

    /**
     *  paymentRefund
     *  @param  array $data Array->{merchant_txn_id, refund_total}
     *  @return json of the refund Payment
     */
    public function paymentRefund($data) {

        if(!empty($data['merchant_txn_id'])) {
            $info = array();
            $data['refund_amount'] =$data['refund_amount']*100;
            
            $order_info = $this->transactionsEnquiry($data['merchant_txn_id']);
            $order = json_decode($order_info);    
            $info['amount']                     = $data['refund_amount'];
            $info['notes']['order_id']          = $order->notes->order_id;
            $info['notes']['order_no']          = $order->notes->order_no;
            $info['notes']['new_order_no']      = $order->notes->new_order_no;
            $info['notes']['merchant_txn_id']   = $order->id;

            $json_encode = json_encode($info);
            $info['id'] = $order->payment_id;

            $data['total_refunded_amount'] = parent::getMerchantTotalRefundAmount($data['merchant_txn_id']);
            if($data['total_refunded_amount'] < $order->amount){
                $result = $this->_callRazorpayApiForRefundPayment($json_encode, $info);
                $response = json_decode($result);
                $value  = array();
                $value['order_id']          = $response->notes->order_id;
                $value['order_no']          = $response->notes->new_order_no;
                $value['merchant_txn_id']   = $response->notes->merchant_txn_id;
                $value['refund_amount']     = $response->amount/100;
                $value['payment_gateway']   = 'razorpay';
                $results = serialize($response); 
                $order_info = parent::insertRefundPayment($value, $results);
            } else {
                $result = "Refund Amount is greater then Order Amount";
            }
            return $result;
        } else {
            throw new Exception("merchant_txn_id is empty in system/library/operations/payment_gateway/razorpay/paymentRefund.");
        }
    }

    /**
     *  transactionsEnquiry
     *  @param  string $data = merchant_txn_id
     *  @return json Razorpay Response
     */
    public function transactionsEnquiry($data){
        $key_id        = $this->key_id;
        $key_secret    = $this->key_secret;
        
        $url = "https://api.razorpay.com/v1/invoices/".$data;
        
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL             => $url,
            CURLOPT_USERPWD         => $key_id.':'.$key_secret,
            CURLOPT_TIMEOUT         => 60,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYPEER  => true
        );
        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);

        if(curl_error($ch)) {
            echo 'error:' . curl_error($ch);
        }
        return $result;
    }

    /**
     *  _callRazorpayApiForRefundPayment
     *  @param  json  $json_request
     *  @param  array $data
     *  @return json  Razorpay Response
     */
    private function _callRazorpayApiForRefundPayment($json_request, $data) {
        $key_id        = $this->key_id;
        $key_secret    = $this->key_secret;

        $url = "https://api.razorpay.com/v1/payments/".$data['id']."/refund";

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';

        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL             => $url,
            CURLOPT_USERPWD         => $key_id.':'.$key_secret,
            CURLOPT_TIMEOUT         => 60,
            CURLOPT_POST            => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_POSTFIELDS      => $json_request
        );
        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);

        if(curl_error($ch)) {
            echo 'error:' . curl_error($ch);
        }
        return $result;
    }
		
		/**
		 *  paymentsTransactionsEnquiry
		 *  @param  string $data = razorpay_payment_id
		 *  @return json Razorpay Response
		 */
		public function paymentsTransactionsEnquiry($razorpay_payment_id){
				$key_id        = $this->key_id;
				$key_secret    = $this->key_secret;
				
				$url = "https://api.razorpay.com/v1/payments/".$razorpay_payment_id;
				
				$ch = curl_init();
				$curlConfig = array(
						CURLOPT_URL             => $url,
						CURLOPT_USERPWD         => $key_id.':'.$key_secret,
						CURLOPT_TIMEOUT         => 60,
						CURLOPT_RETURNTRANSFER  => true,
						CURLOPT_SSL_VERIFYPEER  => true
				);
				curl_setopt_array($ch, $curlConfig);
				$result = curl_exec($ch);

				if(curl_error($ch)) {
						echo 'error:' . curl_error($ch);
				}
				return $result;
		}
		
	/**
	 *  getOrderDetailByMerchant_txn_id
	 *  @param  string $merchant_txn_id = merchant txn id
	 *    		int $successfull = 1 or 0
	 *  @return sql_result order payment details
	 */	
	public function getOrderDetailByMerchant_txn_id($merchant_txn_id, $successfull = 1) {
    	$sql = "SELECT * FROM `".DB_PREFIX."order_payment` 
                WHERE (merchant_txn_id like '%". $this->db->escape($merchant_txn_id) ."%' OR reference LIKE '%". $this->db->escape($merchant_txn_id) ."%') 
                  AND successfull = ".(int)$successfull." 
                  AND payment_gateway = '" . $this->db->escape($this->paymentgateway) . "'";
        $results = $this->db->query($sql)->row;
        return $results;
    }

}
