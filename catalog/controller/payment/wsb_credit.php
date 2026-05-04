<?php
class ControllerPaymentWsbCredit extends Controller {
	public function index() {

		$this->load->language('checkout/checkout');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$data['text_loading'] = $this->language->get('text_loading');

		$data['continue'] = $this->url->link('checkout/success');
        
        if ( isset($this->request->post['one_page_checkout_payment_method']) ) { 
            $data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
        }
		
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/wsb_credit.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/wsb_credit.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/wsb_credit.tpl', $data);
		}
	}

	public function confirm() {

		if (
			!empty($this->session->data['payment_method']) &&
			$this->session->data['payment_method']['code'] != 'wsb_credit'
		) {
			return;
		}
		
		$order_id = $this->session->data['order_id'] ?? NULL;
		$selector = array(
                    'order'=> array('select' => array('total','customer_id','order_no')) 
                   );
        //Get OrderInfo details
        $order_info  = OrderInfo::getOrderInfo($this->db, $order_id,'',$selector);

        $this->load->model('checkout/order');

        $input = array();
        $input['order_id']        = $order_id ;
        $input['order_status_id'] = $this->config->get('wsb_credit_order_status_id');
        $this->model_checkout_order->addOrderHistory($input);

        //WSB credit's cash dummy entry into order payment
        $wsb_credit_data['order_id']    = $order_id;
        $wsb_credit_data['order_no']    = $order_info['order']['order_no'] ?? 0;
        $wsb_credit_data['customer_id'] = $order_info['order']['customer_id'] ?? 0;
        $wsb_credit_data['amount']      = $order_info['order']['total'] ?? 0;

        $this->wsbCreditPaymentIntoDb($wsb_credit_data, (int)$order_id);
	}

	/**
     * Public method to wsb_credit entry into order_payment
    */
    public function wsbCreditPaymentIntoDb(array $wsb_credit_data, int $order_id) {
        $error_arr = array();
        $wsb_credit_payment = new WsbCreditPayment($this);
              
        $wsb_credit_bal = $wsb_credit_payment->getAvaiableCreditBalance($wsb_credit_data['customer_id'], $order_id);

        $order_amount = $wsb_credit_data['amount'];
        $txn_amount   = min($order_amount, $wsb_credit_bal);
        
        $wsb_credit_data['txn_status ']     = 'SUCCESS';
        $wsb_credit_data['payment_mode']    = 'WSB_CREDIT_DUMMY_CASH';
        $wsb_credit_data['payment_gateway'] = 'wsb_credit';
        $wsb_credit_data['amount']          = $txn_amount;
        
        //This payment method for wsb credit and entry into order payment
        $wsb_credit_payment->insertWsbCreditPaymentDetailsIntoDb($wsb_credit_data);

        //Entry into oc_transaction_logs table
        $log_data = array();
        $log_data['order_no']           = $wsb_credit_data['order_no'] ?? 0;
        $log_data['order_id']           = $wsb_credit_data['order_id'] ?? 0;
        $log_data['transaction_amount'] = $txn_amount ?? 0;
        $log_data['url']                = 'route=payment/wsb_credit';
        $log_data['request_type']       = 'purchased';

        $wsb_credit_bal     = $wsb_credit_payment->createTransactionLog($log_data);

        return;
    }
}
