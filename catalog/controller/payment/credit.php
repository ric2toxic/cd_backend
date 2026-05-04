<?php
class ControllerPaymentCredit extends Controller {
    private $_neoGrowthResponseCodes = null;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->_neoGrowthResponseCodes = array(
            'ft-100' => 'Invalid values for input params.',
            'ft-101' => 'Mandatory params are missing.',
            'ft-209' => 'Invalid transaction.',
            'ft-401' => 'Invalid value for Request type.',
            'ft-402' => 'Partner #'.$this->config->get('credit_neo_partner_id').' does not exists or is not active. Please contact support.',
            'ft-403' => 'Checksum hash not matching.',
            'ft-201' => 'An order with same details is already processed, please check the status.',
            'ft-202' => 'This order ID is already completed. Check status.',
            'ft-301' => 'There was an error in saving api order transaction - issue 1.',
            'ft-302' => 'There was an error in saving api order transaction - issue 2.',
            'ft-303' => 'There was an error in saving api order transaction - issue 3.',
            'ft-304' => 'There was an error in saving api order transaction - issue 4.',
            'ft-400' => 'Missing Mandatory Parameters.',
            'ft-501' => 'User transaction cancelled.',
            'ft-502' => 'Transaction Session Timeout.',
            'ft-503' => 'Maximum Retries exhausted.',
            'o-805'  => 'Paylater Account is invalid or inactive. Please contact support.',
            'o-806'  => 'Available balance is not sufficient for this transaction.',
            'st-200' => 'Congrats !!! Your NEOPayLater transaction is successful.',
            'fs-100' => 'Invalid values for input params',
            'fs-101' => 'Partner #'.$this->config->get('credit_neo_partner_id').' does not exists or is not active. Please contact support.',
            'fs-102' => 'Order ID with Partner is not available with NeoPaylater',
            'fs-400' => 'Missing Mandatory params',
            'ss-202' => 'Purchase transaction status is OPEN',
            'ss-203' => 'Purchase transaction status is PENDING',
            'ss-204' => 'Purchase transaction status is FAILED'
        );
    }

    public function index() {
        global $_SERVER;

        $data = array(); // Initializing the data array to be passed on to template files
        $log_data = array(); //store log data
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('payment/credit', $data);

        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
        }
        if (!empty($this->session->data['neo_credit_user_credit_limit'])) {
            $order_info['neo_credit_user_credit_limit'] = $this->session->data['neo_credit_user_credit_limit'];
        }

        $log_data['partner_id'] = $data['partner_id'] = $this->config->get('credit_neo_partner_id');
        $log_data['order_id'] = $data['order_id'] = $order_info['order_no'];
        $log_data['buyer_registration_number'] = $data['buyer_registration_number'] = $this->customer->getCustomerNeoGrowthRegistrationNumber();

        $data['currency'] = $order_info['currency_code'];
        $this->session->data['order_total_val']=$order_info['total'];
       
        if($order_info['total'] <=  $order_info['neo_credit_user_credit_limit']){
             $total = $this->currency->format($order_info['total'],
                    $order_info['currency_code'],
                    $order_info['currency_value'],
                    false);
        }else{
                $neoPayAmount=$order_info['neo_credit_user_credit_limit'];
                $total = $this->currency->format($neoPayAmount,
                        $order_info['currency_code'],
                        $order_info['currency_value'],
                        false);
        }
        $log_data['transaction_amount'] = $data['transaction_amount'] = sprintf("%.2f", $total);
        $this->session->data['neo_credit_pay_amount'] = $data['transaction_amount'];

        $log_data['session_id'] = $data['session_id'] = session_id();

        foreach($this->cart->getProducts() AS $product) {
            $cart_details[]= $product['model'];
        }

        $log_data['cart_details'] = $data['cart_details'] = implode(',' , $cart_details);
        $log_data['request_type'] = $data['request_type'] = 'purchased';
        $log_data['transaction_datetime'] = $data['transaction_datetime'] = time();

        $data['partner_key'] = $this->config->get('credit_neo_partner_key');

        $request_str = $data['partner_id'] . ',' . $data['order_id'] . ',' . $data['buyer_registration_number'] . ',' .
                        $data['transaction_amount'] . ',' . $data['partner_key'];

        $credit_payment_gateway = new CreditPayment($this);
        $log_data['session_id'] = $data['checksum_hash'] = $credit_payment_gateway->generateCheckSumHash($request_str, $data['partner_key']);

        $data['action'] = NEOPAYLATER_BUYER_TRANSACTION_API;

        $domain = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        $callback_url = $domain . 'payment/credit/callback';

        $log_data['callback_url'] = $data['callback_url'] = $callback_url;
        $log_data['success_url'] = $data['success_url'] = $callback_url;
        $log_data['failure_url'] = $data['failure_url'] = $callback_url;
        $request_data_string = http_build_query($log_data);
		$log_data['url'] = $data['action'];
		$log_data['request'] = $request_data_string;
		$log_data['order_no'] = $data['order_id'];
        $log_data['transaction_id'] = $data['order_id'];
  		$log_id = $credit_payment_gateway->createNeoGrowthTransactionLog($log_data);
        
        $this->session->data['log_id'] = $log_id;
        
        if(isset($this->request->post['one_page_checkout_payment_method'])){
            $data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/credit.tpl'))
        {
            return $this->load->view($this->config->get('config_template') . '/template/payment/credit.tpl',$data);
        }
        else
        {
            return $this->load->view('default/template/payment/credit.tpl',$data);
        }
    }

    /*
     @method: callback
     @ this callback is passed to neogrowth, when customer placing order on neogrowth.
     @ this will be called by neogrowth on order success or failed 
     */
    public function callback()
    {
      $log_data = array(
        'response' => json_encode($_GET),
        'status' => strtoupper($_GET['status']),
        'message' => isset($_GET['message']) ? $_GET['message'] : ''
      );
      $credit_payment_gateway = new CreditPayment($this);
      $credit_payment_gateway->updateNeoGrowthTransactionLog((int)$this->session->data['log_id'], $log_data);
      unset($this->session->data['log_id']);
        //echo '<pre>'; print_r($_GET); print_r($this->session->data);exit;
        if (strtoupper($_GET['status']) == 'SUCCESS' || $_GET['code'] == 'st-200')
        {
            $this->load->model('checkout/order');
            $input = array();
            $input['order_id'] = $this->session->data['order_id'];
            $input['order_status_id'] = $this->config->get('credit_order_status_id');
            $order_total_val=$this->session->data['order_total_val'];
            $payamount=$this->session->data['neo_credit_pay_amount'];
            unset($this->session->data['neo_credit_pay_amount']);
            if($order_total_val > $this->session->data['neo_credit_user_credit_limit']){
                $remain_amount=$order_total_val-$payamount;
                $comment="Net Payable Amount: ".$order_total_val."<br>Amount to be paid by NeoGrowth: ".$payamount."<br>Remaining Amount(to be paid by customer): ".$remain_amount;
            }else{
                $comment="Net Payable Amount: ".$order_total_val."<br>Amount to be paid by NeoGrowth: ".$payamount;
            }
            $input['comment'] = $comment;
            //$input['comment'] = $_GET['code'];
            $input['notes'] = 'Order Successfully placed on NeoGrowth credit.';
            $this->model_checkout_order->addOrderHistory($input);
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
        else
        {
            $this->session->data['error'] = isset($this->_neoGrowthResponseCodes[$_GET['code']])?$this->_neoGrowthResponseCodes[$_GET['code']]:$_GET['code'];
            $this->session->data['error'] = "NeoGrowth Response - " . $this->session->data['error'];
            $this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL','payment'));
        }
    }
    
    /*
     @method:  otherTransactionCallback
     this callback will be passed for neogrowth transactions happening on order cancel, delivery, and return.
     NeoGrowth is not using this call back, but since callback is mandatory parameter, we made this dummy function.
    */
    public function neoTransactionCallback() {
      // dummy method
    }
}
