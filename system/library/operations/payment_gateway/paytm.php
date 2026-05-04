<?php
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/payment_gateway_base.php');
/**
 * 	Paytm
 * 	@author @Manoj Singh
 */
class Paytm extends PaymentGatewayBase 
{
	private $access_key;
	private $secret_key;

	public function __construct($registry) {
		parent::__construct($registry);
        $this->paymentgateway = 'paytm';
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
    }

    /**
	 *	_createJsonForGeneratePaymentLink
	 *  @INFO   PayTM document for making invoice JSON for PayTM
	 *	@param 	array $order_detail
	 *	@return json order_detail
	 */
    private function _createJsonForGeneratePaymentLink($order_detail) {
    }

    /**
	 *	_callCitrusApiForGenerateInvoiceLink
	 *	@param 	json $json_request
	 *	@return json PayTM Response  
	 */
    private function _callPaytmApiForGenerateInvoiceLink($json_request) {        
    }

	/**
     *  getTransactionsByDate
     *  @param  array $info => {sync_solr, txnStartDate, txnEndDate}
     *  @return json Paytm Response
     */
    public function getTransactionsByDate($info) {
    }

    /**
	 *	_callCitrusApiForSearchingTransactionsByDate
	 *	@param 	json $json_request
	 *	@return json Paytm Response
	 */
    private function _callPaytmApiForSearchingTransactionsByDate($json_request){
    }

    /**
	 *	paymentRefund
	 *	@param 	array $data Array->{merchant_txn_id, refund_total}
	 *	@return json of the refund Payment
	 */
	public function paymentRefund($data) {
    }

    /**
	 *	transactionsEnquiry
	 *	@param 	string $date = merchant_txn_id
	 *	@return json Citrus Response
	 */
    public function transactionsEnquiry($data){
    }

    /**
     *  _callCitrusApiForRefundPayment
     *  @param  json  $json_request
     *  @param  array $data
     *  @return json  Paytm Response
     */
	private function _callPaytmApiForRefundPayment($json_request, $data) {
    }

    /**
     *  insertDirectCustomerPaymentDetailsIntoDb
     *  @param  array $response
     *  @param  array $value
     **/     
    public function insertDirectCustomerPaymentDetailsIntoDb($response, $value) {

    	$paiable_amt 	= 0;
        $paid_amt 		= (float)$response['TXNAMOUNT'];
        
    	//Define data array 
        $data = array(); 
        $data['order_id']           = (int)$value['order_id'];
        $data['merchant_txn_id']    = $response['TXNID'];
        $data['order_no']           = $value['order_no'];
        $data['txn_status']         = $response['STATUS'];
        $data['payment_mode']       = $response['PAYMENTMODE'];   
        $data['amount']             = (float)$response['TXNAMOUNT']; 
        $data['txn_date_time']      = $response['TXNDATE'];
        $data['date_added']         = 'NOW()';
        $data['payment_gateway']    = 'paytm';
        $data['successfull']        = '1';
        $data['reference']          = '';
        $data['payment_link']       = 'Customer Direct Payment';
        $data['json_format']        = $value['json_response'];
        $data['user_id']            = '0';
        
        OrderPayment::insertOrderPayment($this->db,$data); //To insert data in order payment table

    }
}
