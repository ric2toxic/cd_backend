<?php
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/payment_gateway_base.php');
/**
 * 	Citrus
 * https://developers.citruspay.com/doc/blazepay/-------(Possible response code for APIs)
    Response Code (pgRespCode)  Transaction Status (TxStatus)
    Normal Transaction Response
    0   SUCCESS
    1   FAIL
    2   Transaction initiated
    3   Transaction canceled by consumer
    4   Pending with bank
    7   Session expired
    11  Refund successful
    12  Refund failure
    14  Success on verification
    15  Rejected by payment gateway. Transaction already in progress. 30 Invalid status
    Error Codes due to incorrect integration
    100 Invalid Request – Mandatory Param Missing
    101 Record not available
    102 Merchant not found
    104 Payment related mandatory parameters are missing
    105 Missing or Incorrect Payment Mode
    106 Payment mode not enabled
    107 Invalid Request – Custom Param Missing
    110 Invalid request parameter
    111 Invalid update request
    112 Invalid currency
    113 Transaction already in progress
    114 Invalid signature key
    114 Invalid amount
    116 Refund amount more than allowed limit
    117 Invalid or tempared offerToken value.
    120 Velocity rule failed
    121 Successful sale transaction doesn’t exists
    Error Codes returned by Card Transactions.
    800 Transaction refused by gateway
    801 Transaction denied by Risk
    802 Transaction is in invalid state to process further 803 Gateway Unavailable
    804 Gateway operation failed
    805 Gateway validation failed
    806 gateway denied
    807 Request Timed Out
    808 Unexpected response from Bank Gateway”,null), 810 data invalid
    811 data invalid
    812 data invalid
    813 Data Tampered
    814 Internal Application error
    815 International Card found
    816 3D verification failed or Declined by user
    817 Blocked at ReD screening
    820 Operation Declined
 * 	@author @Garvit Joshi
 */
class Citrus extends PaymentGatewayBase 
{
	private $access_key;
	private $secret_key;
    //0->Success, 2->Transaction initiated, 11->Refund successful, 14->Success on verification
    private $_citrus_res_status = array(0, 2, 11, 14);

	public function __construct($registry) {
		
		parent::__construct($registry);
        $this->paymentgateway = 'citrus';

		$this->access_key  	= $registry->config->get('citrus_access_key');
        $this->secret_key  	= $registry->config->get('citrus_secret_key');
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
	 *	@return status of that Payment
	 */
    public function generatePaymentLink($data) {
        if(!empty($data['order_id'])){
        	/** 
        	 *Get the Order Detail and check order_no is exist or not in oc_order_payment 
			 *if exist then add some alphabatic charector in order_no
			 **/
        	$order_info = parent::getOrderDetail($data);
            /**
             *This method make Json of order information
             **/
            $json_request = $this->_createJsonForGeneratePaymentLink($order_info); 
            
            /**
             *call Citrus Api For Generate Invoice Link and send to customer for payment
             **/
            $json_response = $this->_callCitrusApiForGenerateInvoiceLink($json_request);
            $response = json_decode($json_response);
            //echo "Citrus Response: "; echo "<pre>"; print_r($json_response); echo "</pre>";
			$citrus_response = array();

            if($response->responseMsg == 'SUCCESS'){
                /**
                 *Insert data into oc_order_payment with success = 0
                 **/
                if(!isset($data['user_id'])){
                    $data['user_id'] = 0;
                }

                $value = array();
                $value['order_id']				= $data['order_id'];
                $value['merchant_txn_id'] 		= '';
                $value['order_no'] 				= $data['order_no'];
                $value['amount'] 				= $data['amount'];
                $value['payment_gateway'] 		= 'citrus';
                $value['payment_link'] 			= $response->specialMsg;
                $value['serialize_response'] 	= serialize($response); 
                $value['user_id']               = $data['user_id'];

            	parent::setGeneratePaymentLinkToDb($value);
                
                $citrus_response['responseMsg']     = $response->responseMsg;
                $citrus_response['specialMsg']      = $response->specialMsg;
                $citrus_response['payment_gateway'] = 'Citrus';
            } else {
                /**
                 * Here we send an email to wholesalebox for Invalid Merchant in citrus
                 **/
                $citrus_response['responseMsg']     = $response->responseMsg;
                $citrus_response['specialMsg']      = $response->specialMsg;
                $citrus_response['payment_gateway'] = 'Citrus';
                parent::errorResponse($response, $data, $citrus_response);
                //throw new Exception("Invalid Merchant/customer information in system/library/operations/payment_gateway/citrus/generatePaymentLink.");
            }
            return $citrus_response;
        } else {
            throw new Exception("data is empty in system/library/operations/payment_gateway/citrus/generatePaymentLink.");
        }
    }

    /**
	 *	_createJsonForGeneratePaymentLink
	 *  @INFO   https://developers.citruspay.com/doc/integrations/ for make a invoice citrus json 
	 *	@param 	array $order_detail
	 *	@return json order_detail
	 */
    private function _createJsonForGeneratePaymentLink($order_detail) {

        if(!empty($order_detail)){
            $json = array();
            
            //data sanitization
            $order_detail['firstname']      = preg_replace('/[^A-Za-z\-]/', '', $order_detail['firstname']);
            $order_detail['lastname']       = preg_replace('/[^A-Za-z\-]/', '', $order_detail['lastname']);
            $order_detail['shipping_city']  = preg_replace('/[^A-Za-z\-]/', '', $order_detail['shipping_city']);

            //Consumer Detail
            $json['consumerDetail'] = array();
            $json['consumerDetail']['firstName']    = empty($order_detail['firstname'])?'na':trim($order_detail['firstname']);;
            $json['consumerDetail']['lastName']     = empty($order_detail['lastname'])?'na':trim($order_detail['lastname']);
            $json['consumerDetail']['email']        = empty($order_detail['email'])?'noemailwsb@gmail.com':trim($order_detail['email']);
            $json['consumerDetail']['phoneNumber'] = array();
                //Phone no.
                $json['consumerDetail']['phoneNumber']['phoneNumber']   = trim($order_detail['telephone']);
                $json['consumerDetail']['phoneNumber']['type']          = 'Mobile';
            $json['consumerDetail']['alternateNumber']  = trim($order_detail['telephone']);
            $json['consumerDetail']['contactAddress'] = array();
                //Contact Address
                $json['consumerDetail']['contactAddress']['addressStreet1'] = empty($order_detail['shipping_address_1'])?'na':trim($order_detail['shipping_address_1']);
                $json['consumerDetail']['contactAddress']['addressStreet2'] = empty($order_detail['shipping_address_2'])?'na':trim($order_detail['shipping_address_2']);
                $json['consumerDetail']['contactAddress']['addressState']   = empty($order_detail['shipping_zone']?'na':str_replace(' ','',trim($order_detail['shipping_zone'])));
                $json['consumerDetail']['contactAddress']['addressCity']    = empty($order_detail['shipping_city']?'na':str_replace(' ','',trim($order_detail['shipping_city'])));
                $json['consumerDetail']['contactAddress']['addressCountry'] = trim($order_detail['shipping_country']);
                $json['consumerDetail']['contactAddress']['addressZip']     = empty($order_detail['shipping_postcode'])?'111111':trim($order_detail['shipping_postcode']);
            $json['orderAmount'] = array();
            	//Order Amount
                $json['orderAmount']['currency']    = trim($order_detail['currency_code']);
            	$json['orderAmount']['amount']      = sprintf("%.2f", $order_detail['amount']);
            $json['customParamsMap'] = array();
	        	//custom Params Map
	            $json['customParamsMap']['Email']   = trim($order_detail['email']);
	            $json['customParamsMap']['Mobile']  = trim($order_detail['telephone']);
	            $json['customParamsMap']['OrderNO'] = trim($order_detail['new_order_no']);
            $json['maxPayAllowed']      = '1';
            $json['validityDays']       = '7';
            $json['createSmsInvoice']   = 'true';
            $json['createEmailInvoice'] = 'true';
            $json['notifyThirdParties'] = 'false';
                
            $store_id = $this->registry->config->get('config_store_id') ;
            if($store_id == INTERNATIONAL_STORE_ID){
                $citrus_vanityurl   = international_citrus_vanityurl;
                $citrus_access_key  = international_citrus_access_key;
                $citrus_secret_key  = international_citrus_secret_key;
            }else{
                $citrus_vanityurl   = $this->registry->config->get('citrus_vanityurl');
                $citrus_access_key  = $this->access_key;
                $citrus_secret_key  = $this->secret_key;
            }
            $vanityUrl              = $citrus_vanityurl;
            $orderAmount            = $json['orderAmount']['amount'];
            $currency               = $json['orderAmount']['currency'];

            $signature              = "$vanityUrl$orderAmount$currency";
            $secureSignature        = hash_hmac('sha1', $signature, $citrus_secret_key);

            $json['vanityUrl'] = $vanityUrl;
            $json['signature'] = $secureSignature;

            return $json;
        }else{
            throw new Exception("order_detail is empty in system/library/operations/payment_gateway/citrus/generatePaymentLink.");
        }
    }

    /**
	 *	_callCitrusApiForGenerateInvoiceLink
	 *	@param 	json $json_request
	 *	@return json Citrus Response  
	 */
    private function _callCitrusApiForGenerateInvoiceLink($json_array) {

        $input = $json_array;

        $success = false;
        while( !$success ){
            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'Content-Type: application/json';

            $ch = curl_init();
            $curlConfig = array(
                CURLOPT_URL            => CITRUS_GENERATE_INVOICE_LINK_URL,
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_POSTFIELDS     => json_encode($input)
            );

            curl_setopt_array($ch, $curlConfig);
            $result = curl_exec($ch);
            if(curl_error($ch)) {
                echo 'error:' . curl_error($ch);
                exit();
            }

            $result_array = json_decode($result,true);
            
            if($result_array['responseMsg'] === 'FAILURE'){
                if($result_array['specialMsg'] === 'Invalid Email'){
                    $input['consumerDetail']['email'] = 'noemailwsb@gmail.com';
                } elseif($result_array['specialMsg'] === 'Invalid phone number'){
                    $input['consumerDetail']['phoneNumber']['phoneNumber']   = '9999999999';
                    $input['consumerDetail']['alternateNumber']  = '9999999999';
                } elseif($result_array['specialMsg'] === 'Invalid Zip Code'){
                    $input['consumerDetail']['contactAddress']['addressZip'] = '302016';
                } else { // Untraceable error - alert internally
                    $success =  true;
                    return $result;
                }
            } else {
                $success = true;
                return $result;
            }
        }
    }

	/**
     *  getTransactionsByDate
     *  @param  array $info => {sync_solr, txnStartDate, txnEndDate}
     *  @return json Citrus Response  
     */
    public function getTransactionsByDate($info) {
		
			$data = array();

			if(isset($info['txnStartDate'])) {
	        $data['txnStartDate']   = date("Ymd", strtotime($info['txnStartDate']));
	    } else {
	        $data['txnStartDate']   = "20150901";
	    }
	    if (isset($info['txnEndDate'])) {
	        $data['txnEndDate']     = date("Ymd", strtotime($info['txnEndDate']));
	    } else {
	        $data['txnEndDate']     = date("Ymd");
	    }
			
			if (isset($info['fromPosition'])) {
					$data['fromPosition'] = $info['fromPosition'];
			} else {
					$data['fromPosition'] = 0;
			}
			
			$value = json_encode($data);

	    $result = $this->_callCitrusApiForSearchingTransactionsByDate($value);
	    $json_decode = json_decode($result,true);

	    // if(isset($info['sync_solr']) && $info['sync_solr'] == true) {
	    // 	$PaymentTransactions = new PaymentTransactions($this->registry);
      //   	$PaymentTransactions->addTranscationsToSolr($json_decode->transactions);
      //   }
      return $json_decode;
    }

    /**
	 *	_callCitrusApiForSearchingTransactionsByDate
	 *	@param 	json $json_request
	 *	@return json Citrus Response
	 */
    private function _callCitrusApiForSearchingTransactionsByDate($json_request){
			$citrus_keys = $this->getCitrusSecretKeys();
			$response = array(
				'transactions' => array(),
				'errors' => array()
			);
			foreach ($citrus_keys as $citrus_key) {
				$headers = array();
	    	$headers[] = 'access_key:'.$citrus_key['access_key'];
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Content-Type: application/json';

	        $ch = curl_init();
	        $curlConfig = array(
	            CURLOPT_URL            => CITRUS_SEARCHING_TRANSACTIONS_BY_DATE_URL,
	            CURLOPT_POST           => true,
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_HTTPHEADER     => $headers,
	            CURLOPT_POSTFIELDS     => $json_request
	        );
	        curl_setopt_array($ch, $curlConfig);
	        $result = curl_exec($ch);
	        if(curl_error($ch)) {
	            echo 'error:' . curl_error($ch);
							// if error, then continue
							continue;
	        }
					$json_decode = json_decode($result,true);
					if (isset($json_decode['transactions'])) {
						$response['transactions'] = array_merge($response['transactions'], $json_decode['transactions']);
					} else {
						$json_decode['access_key'] = substr_replace($citrus_key['access_key'], '*********', 0, strlen($citrus_key['access_key'])*3/4);
						$response['errors'][] = $json_decode;
					}
			}
			
			$result = json_encode($response);
    	
      return $result;
    }

    /**
	 *	paymentRefund
	 *	@param 	array $data Array->{merchant_txn_id, refund_total}
	 *	@return json of the refund Payment
	 */
	public function paymentRefund($data) {
        if(!empty($data['merchant_txn_id'])) {
            
            // Initializing
            $info = array();
            $info['merchantTxnId']  = '';
            $info['pgTxnId']        = '';
            $info['rrn']            = '';
            $info['authIdCode']     = '';
            $info['currencyCode']   = '';
            
            // Array to store various refund PG Transaction ID(s)
            $refund_pg_trxn_ids = array();
            
            $order_info = $this->transactionsEnquiry($data['merchant_txn_id']);
            $order = json_decode($order_info);
            
            if(!empty($order->enquiryResponse)){
                $balance_amt = 0;
                
                foreach ($order->enquiryResponse as $value) {
                    
                    if(in_array($value->respCode, $this->_citrus_res_status)) {
                        
                        if( strtolower($value->txnType) == "refund" 
                           && !in_array($value->pgTxnId, $refund_pg_trxn_ids) ) {
                               
                            $balance_amt -= abs($value->amount);
                            $refund_pg_trxn_ids[] = $value->pgTxnId;
                            
                        }else if(strtolower($value->txnType) == "sale" 
                                 && empty($info['pgTxnId']) ){
                            
                            $balance_amt += abs($value->amount);
                            
                            // populate other data to initiate refund against this sale
                            $info['merchantTxnId']  = $value->merchantTxnId;
                            $info['pgTxnId']        = $value->pgTxnId;
                            $info['rrn']            = $value->rrn;
                            $info['authIdCode']     = $value->authIdCode;
                            $info['currencyCode']   = $value->currency;
                        }
                    }
                }
                
                // Amount that we need to refund
                $info['amount']         = (string)$data['refund_amount'];
                $info['txnType']        = "Refund";
                $total_amount           = (float)$balance_amt; // Actual available amount
                
                $json_encode = json_encode($info);
                
                // Get order_payment table entries for this Merchant Txn ID
                $merchant_detail = parent::getOrderDetailByMerchant_txn_id($data['merchant_txn_id']);

                if($data['refund_amount'] <= $total_amount ){
                    
                    $result = $this->_callCitrusApiForRefundPayment($json_encode, $info);
                    $response = json_decode($result);
                    
                    $refund_response = array();
                    $is_success_data = array();
                    
                    if(!empty($response)){
                        
                        if(in_array($response->respCode, $this->_citrus_res_status)) {
                            $is_success_data['successfull'] = 1;
                            $refund_response['status']      = "success";
                            $refund_response['text']        = "Refund Done";
                            $refund_response['response']    = $result;
                            
                            if(!empty($merchant_detail)){
                                $enquiry = $this->transactionsEnquiry($response->merchantTxnId);
                                $citrus_enquiry = json_decode($enquiry);
                                if(!empty($citrus_enquiry->enquiryResponse)){
                                    foreach ($citrus_enquiry->enquiryResponse as $enquiry_data) {
                                        if($enquiry_data->merchantRefundTxId == $response->merchantRefundTxId){
                                           $is_success_data['txn_date_time'] = $enquiry_data->txnDateTime;
                                        }
                                    }
                                }
                                
                                $trxn_data = array();
                                $trxn_data['tr_id']       = $data['tr_id'];
                                $trxn_data['trxn_for']    = $data['trxn_for'];
                                $trxn_data['trxn_for_id'] = $data['trxn_for_id'];
                                $trxn_data['trxn_done']   = 'BANK_SUCCESS';
                                $trxn_data['trxn_amount'] = round($response->amount, 2);
                                $trxn_data['trxn_utr_internal'] = $response->pgTxnId;
                                $trxn_data['trxn_utr']    = $response->merchantTxnId;
                                $trxn_data['trxn_bank']   = 'citrus';
                                $trxn_data['trxn_response']   = serialize($response);
                                $trxn_data['trxn_date_added'] = 'NOW()';
                                $trxn_id = parent::addTrxnDetails($trxn_data);

                                $is_success_data['order_id']          = $merchant_detail['order_id'];
                                $is_success_data['order_no']          = $merchant_detail['order_no'];
                                $is_success_data['payment_mode']      = $merchant_detail['payment_mode'];
                                $is_success_data['merchant_txn_id']   = $response->merchantTxnId;
                                $is_success_data['trxn_id']           = $trxn_id;
                                $is_success_data['refund_amount']     = -abs(round($response->amount, 2));
                                $is_success_data['payment_link']      = $response->merchantRefundTxId;
                                $is_success_data['payment_gateway']   = 'citrus';
                                $is_success_data['payment_mode']      = $merchant_detail['payment_mode'];
                                $is_success_data['reference']         = $data['payment_ref'];
                                $is_success_data['user_id']           = $data['user_id'];
                                $is_success_data['json_format']       = serialize($response);
                                parent::insertRefundPaymentIntoDb($is_success_data);    
                            }
                        }else{
                            $refund_response['status']     = "error";
                            $refund_response['text']       = $response->respMsg;
                        }  
                                          
                    } else {
                        $refund_response['status']   = "error";
                        $refund_response['text']     = "No Response received from Citrus, on calling Refund API";
                    }
                } else {
                    $refund_response['status']   = "error";
                    $refund_response['text']     = "Refund Amount is more than Available Balance to Refund against this Merchant Txn ID";
                }
            }else{
                $refund_response['status'] = "error";
                $refund_response['text']   = "Citrus enquiry response is empty. No payment received against this Merchant Txn ID.";
            }
        } else {
            $refund_response['status'] = "error";
            $refund_response['text']   = "Merchant Trxn Id is empty";            
        }
        return $refund_response;
    }

    /**
	 *	transactionsEnquiry
	 *	@param 	string $date = merchant_txn_id
	 *	@return json Citrus Response
	 */
    public function transactionsEnquiry($data){
    	$citrus_keys = $this->getCitrusSecretKeys();
			
			// check the transaction for all mechant account of citrus, when found stop checking for further mechants 
			foreach ($citrus_keys as $citrus_key) {
				$headers = array();
	    	$headers[] = 'access_key:'.$citrus_key['access_key'];
	        $headers[] = 'Accept: application/json';
	        $headers[] = 'Content-Type: application/json';

	        $ch = curl_init();
	        $curlConfig = array(
	            CURLOPT_URL            => CITRUS_TRANSACTIONS_ENQUIRY_URL.$data,
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_HTTPHEADER     => $headers
	        );
	        curl_setopt_array($ch, $curlConfig);
	        $result = curl_exec($ch);
	        if(curl_error($ch)) {
	            echo 'error:' . curl_error($ch);
							// if error in current request, then continue
							continue;
	        }
					$response = json_decode($result);
					
					// if got the enquiry response, then return;
					if(!empty($response->enquiryResponse)) {
						return $result;
					}
			}
			
			// return the empty response 
			$response = array();
			return json_encode($response);
    }

    /**
     *  _callCitrusApiForRefundPayment
     *  @param  json  $json_request
     *  @param  array $data
     *  @return json  Citrus Response
     */
	private function _callCitrusApiForRefundPayment($json_request, $data) {
			$citrus_keys = $this->getCitrusSecretKeys();
			foreach ($citrus_keys as $citrus_key) {
				$citrus_access_key  = $citrus_key['access_key'];
        $citrus_secret_key  = $citrus_key['secret_key'];
        $signature = "merchantAccessKey=". $citrus_access_key ."&transactionId=". $data['merchantTxnId'] ."&amount=". $data['amount'];

        $secureSignature    = hash_hmac('sha1', $signature, $citrus_secret_key);
	    	$headers = array();
	    	$headers[] = 'Content-Type: application/json';
	    	$headers[] = 'access_key: '.$citrus_access_key;
	    	$headers[] = 'signature: '.$secureSignature;
        $headers[] = 'Accept: application/json';

        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL            => CITRUS_REFUND_PAYMENT_URL,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $json_request
        );
        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);
        if(curl_error($ch)) {
            echo 'error:' . curl_error($ch);
						// if error in current request, then continue
						continue;
        }
				$response = json_decode($result);
				if (!empty($response)) {
					return $result;
				}
			}
			
			// return the empty response 
			$response = array();
			return json_encode($response);
    }

    /**
     *  insertDirectCustomerPaymentDetailsIntoDb
     *  @param  array $response
     *  @param  array $value
     **/
    public function insertDirectCustomerPaymentDetailsIntoDb($response, $value) 
    {

        $payable_amt    = 0;
        $paid_amt       = (float)$response['amount'];
        
        //Define data array 
        $data = array(); 
        $data['order_id']           = (int)$value['order_id'];
        $data['merchant_txn_id']    = $response['TxId'];
        $data['order_no']           = $value['order_no'];
        $data['txn_status']         = $response['TxStatus'];
        $data['payment_mode']       = $response['paymentMode'];   
        $data['amount']             = (float)$paid_amt; 
        $data['txn_date_time']      = $response['txnDateTime'];
        $data['date_added']         = 'NOW()';
        $data['payment_gateway']    = 'citrus';
        $data['successfull']        = '1';
        $data['reference']          = '';
        $data['payment_link']       = 'Customer Direct Payment';
        $data['json_format']        = $value['json_response'];
        $data['user_id']            = '0';
        
				$valid_insertion = 1;
				// check if row is already there with same information
				if($data['amount'] > 0) {
					$sql = "SELECT * FROM ". DB_PREFIX . "order_payment 
							WHERE 
							merchant_txn_id ='". $this->db->escape($data['merchant_txn_id']) ."' AND
							successfull = '1' AND
							amount = '".$this->db->escape($data['amount'])."' AND
							payment_gateway = 'citrus'";
					$query_result = $this->db->query($sql);
					if($query_result->num_rows) {
						$valid_insertion = 0;
					}
				}
				
				if($valid_insertion && $data['order_id'] != 0) {
        	OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table
				}
    }
    
		// method to get all citrus secret keys
    public function getCitrusSecretKeys(): array {
      $citrus_keys = array(); // store the all citrus keys
      $citrus_keys[] = array(
				'secret_key' => $this->secret_key,
				'access_key' => $this->access_key
			);  // default keys from config
      $citrus_keys[] = array(
				'secret_key' => international_citrus_secret_key,
				'access_key' => international_citrus_access_key
			);
      return $citrus_keys;
    }
}
