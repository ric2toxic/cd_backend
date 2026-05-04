<?php
include_once DIR_SYSTEM . '../rabbitmq/task_directive_constants.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ControllerPaymentRblCredit extends Controller {

    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    public function index() {
        global $_SERVER;

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('payment/rbl', $data);

        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
        }
        if (!empty($this->session->data['rbl_credit_user_credit_limit'])) {
            $order_info['rbl_credit_user_credit_limit'] = $this->session->data['rbl_credit_user_credit_limit'];
        }

        $data['partner_id'] = $this->config->get('credit_rbl_partner_id');
        $data['order_id'] = $order_info['order_id'];
        $data['order_no'] = $order_info['order_no'];

        $data['currency'] = $order_info['currency_code'];
        $this->session->data['order_total_val']=$order_info['total'];
       
        if($order_info['total'] <=  $order_info['rbl_credit_user_credit_limit']){
             $total = $this->currency->format($order_info['total'],
                    $order_info['currency_code'],
                    $order_info['currency_value'],
                    false);
        }else{
                $rblPayAmount=$order_info['rbl_credit_user_credit_limit'];
                $total = $this->currency->format($rblPayAmount,
                        $order_info['currency_code'],
                        $order_info['currency_value'],
                        false);
        }
        
        $data['transaction_amount'] = sprintf("%.2f", $total);
        
        $this->session->data['rbl_credit_pay_amount'] = $data['transaction_amount'];

        $data['session_id'] = session_id();
        $data['request_type'] = 'purchased';
        $data['transaction_datetime'] = time();

        $data['partner_key'] = $this->config->get('credit_neo_partner_key');
        $data['buyer_registration_number'] = '';

        $request_str = $data['order_id'] . ',' . $data['order_no'] . ',' . $data['transaction_amount'] ;

        $credit_payment_gateway = new RblPayment($this);
        $log_data['session_id'] = $data['checksum_hash'] = $credit_payment_gateway->generateCheckSumHash($request_str, $data['partner_key']);

        $data['action'] = $this->url->link('payment/rbl_credit/callback', '', 'SSL');

        $domain = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        
        $callback_url = $domain . 'payment/rbl_credit/callback';

        $data['callback_url'] = $callback_url;
        $data['success_url']  = $callback_url;
        $data['failure_url']  = $callback_url;
        
        if(isset($this->request->post['one_page_checkout_payment_method'])){
            $data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/rbl.tpl'))
        {
            return $this->load->view($this->config->get('config_template') . '/template/payment/rbl.tpl',$data);
        }
        else
        {
            return $this->load->view('default/template/payment/rbl.tpl',$data);
        }
    }

    /*
     @method: callback
     @ this callback is passed to RBL, when customer placing order on RBL.
     @ this will be called by RBL on order success or failed 
     */
    public function callback()
    {
        $order_total    = $this->request->post['transaction_amount'];
        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'] ?? $order_total;
        }

        try{

            $customer_id = $order_info['customer_id'];
            $order_amount= $order_info['total'];
            $order_id    = $order_info['order_id'];

            $credit_payment_gateway = new RblPayment($this);

            $post_data = array();
            $post_data['RDFAnchorJourney']['data'] = array(
                                                          'URNumber' => $credit_payment_gateway->getCustomerUrnNumber((int)$customer_id),
                                                          'request'  => array(
                                                                  'orderPunch' => array(
                                                                      'retailerId'    => $customer_id,
                                                                      'anchorId'      => RBL_API_ANCHORID,
                                                                      'orderDate'     => date("d/m/Y"),
                                                                      'orderAmount'   => number_format($order_amount, 2, '.', ''),
                                                                      'deliveryDate'  => date('d/m/Y', strtotime("+".RBL_DEFAULT_ORDER_DELIVERED_DAYS." days")),
                                                                      'orderId'       => $order_id
                                                                  )
                                                              )
                                                          );

            $api_endpoint   = RBL_API_ENDPOINTS.'?client_id='.RBL_API_CLIENT_ID.'&client_secret='.RBL_API_SECRET;
            $result         = Curl::callRblAPI( $api_endpoint, $post_data );
            $rbl_response   = json_decode($result,true);
            
            $log_data       = array(
                                    'api_type'      => 'orderPunch',
                                    'customer_id'   => $customer_id,
                                    'order_id'      => $order_id,
                                    'suborder_id'   => '',
                                    'order_amount'  => $order_amount,
                                    'api_endpoint'  => $api_endpoint,
                                    'api_post_data' => json_encode($post_data)
                                );
            $log_id = $credit_payment_gateway->createRblApiLog($log_data);

            if(isset($rbl_response['RDFAnchorJourney']['status'])) 
            {
                $api_status      = $rbl_response['RDFAnchorJourney']['status'];
                $cif_id          = $rbl_response['RDFAnchorJourney']['data']['response']['orderPunch']['cifId'] ?? '';
                $dpd_count       = $rbl_response['RDFAnchorJourney']['data']['response']['orderPunch']['DPDCount'] ?? '';
                $message         = $rbl_response['RDFAnchorJourney']['data']['response']['orderPunch']['message'] ?? '';

                $update_punch_log= array(
                                'cif_id'            => $cif_id,
                                'dpd_count'         => $dpd_count,
                                'api_status'        => $api_status,
                                'api_response_data' => json_encode($rbl_response),
                                'status'            => 'SUCCESS'
                            );

                $credit_payment_gateway->updateRblApiLog($log_id, $update_punch_log);

                if( $api_status > 0 || $dpd_count > 0 ) {

                    /*
                        If ApiStatus > 0 || DPDCount > 0 received from RBL API,
                            ApiStatus = 1 [failer response] 
                            DPDCount  = 1 [order disbusal issue]
                        order will be consider as cancelled/failed order on RBL
                    */
                    $this->session->data['error'] = "RBL Response - " . $message;
                    $this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL','payment'));
                    exit;

                } else {
                    /*
                        If RBL orderPunch API successfully called, then we will process the order and will save required order data
                         for transaction log and, we will save 2 transaction log entries, one for Purchase status and second 
                         for OrderPunch status
                    */
                    $log_data = array(); 
                    $log_data['partner_id']     = $this->config->get('credit_rbl_partner_id');
                    $log_data['order_id']       = $order_info['order_id'];
                    $log_data['order_no']       = $order_info['order_no'];
                    $log_data['request_type']   = 'Purchased';
                    $log_data['transaction_datetime'] = time();
                    $callback_url = $domain . 'payment/rbl_credit/callback';
                    $log_data['callback_url']   = $callback_url;
                    $log_data['success_url']    = $callback_url;
                    $log_data['failure_url']    = $callback_url;
                    $request_data_string        = http_build_query($log_data);
                    $log_data['url']            = $this->url->link('payment/rbl_credit/callback', '', 'SSL');
                    $log_data['request']        = $request_data_string;
                    $log_data['suborder_id']    = '';
                    $log_data['transaction_id'] = $order_info['order_id'];
                    $log_data['transaction_amount'] = $order_amount;
                    $log_data['buyer_registration_number'] = '';
                    $log_data['message']        = 'Order Successfully placed over RBL';
                    
                    //RBL transaction log for Purchase status
                    $credit_payment_gateway->createOrderWiseRBLTransactionLog($log_data); 

                    //For orderPunch status
                    $log_data['url']            = $api_endpoint;
                    $log_data['request']        = json_encode($post_data);
                    $log_data['response']       = json_encode($rbl_response);
                    $log_data['request_type']   = 'OrderPunch';   
                    $log_data['message']        = 'OrderPunch request sent successfully to RBL';
                    $credit_payment_gateway->createOrderWiseRBLTransactionLog($log_data); 

                    /*Add Order History data for new order*/
                    $this->load->model('checkout/order');
                    $input = array();
                    $input['order_id']          = $order_info['order_id'];
                    $input['order_status_id']   = $this->config->get('rbl_order_status_id');
                    unset($this->session->data['rbl_credit_pay_amount']);
                    $comment="Net Payable Amount: ".$order_amount."<br>Amount to be paid by RBL: ".$order_amount;
                    $input['comment'] = $comment;
                    $input['notes'] = 'Order Successfully placed on RBL credit.';
                    $this->model_checkout_order->addOrderHistory($input);

                    //RESET CUSTOMER BALANCE ON ORDERPUNCH API CALL
                    $this->updateCustomerCreditBalance($customer_id, $order_amount);

                    $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
                    exit;
                }

            } else { 

                $this->session->data['error'] = "RBL Server not responding";
                $this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL','payment'));
                exit;
            }


        } catch( Exception $e ) {

            $this->session->data['error'] = $e->getMessage();
            $this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL','payment'));
            exit;

        }

    }
    
    /*
     @method:  otherTransactionCallback
     this callback will be passed for RBL transactions happening on order cancel, delivery, and return.
     RBL is not using this call back, but since callback is mandatory parameter, we made this dummy function.
    */
    public function neoTransactionCallback() {
      // dummy method
    }

    /*
     * @method: updateCustomerCreditBalance
     * This method use to reset customer credit balance on OrderPunch API call
     * 
    */
    public function updateCustomerCreditBalance( int $customer_id, $order_total )
    {
        $sql = "SELECT 
                    credit_balance  
                FROM " . DB_PREFIX . "customer_credit 
                WHERE
                    customer_id = '" . (int)$customer_id  ."'
                    AND
                    type = 'RBL'
                    AND
                    credit_status = '1'
            ";
        $result = $this->db->query($sql);    
        if($result->num_rows) {
            $updated_balance = (float)$result->row['credit_balance'] - (float)$order_total;
            if( $updated_balance > 0 ) {
                $sql = "UPDATE " . DB_PREFIX . "customer_credit 
                        SET 
                            credit_balance = '" . (float)$updated_balance ."' 
                        WHERE 
                            customer_id = '" . (int)$customer_id  ."'
                            AND
                            type = 'RBL'
                            AND
                            credit_status = '1' 
                        ";
                $this->db->query($sql);
            }
        }
    }



}
