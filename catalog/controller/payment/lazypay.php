<?php
class ControllerPaymentLazypay extends Controller {
    private $_lazypayResponseCodes = null;
    public $transaction_id = '';
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->_lazypayResponseCodes = array(
            'LP_ELIGIBLE'                       => 'User is eligible to use Lazypay for this transaction',
            'LP_USER_BLOCKED'                   => 'Your Lazypay account id blocked',
            'LP_USER_OPTED_OUT'                 => 'You have opted out of Lazypay service',
            'LP_USER_INELIGIBLE'                => 'Sorry! You are currently not registered for LazyPay',
            'LP_INSUFFICIENT_BALANCE'           => 'Sorry, you do not have sufficient balance for this transaction',
            'LP_MERCHANT_DISABLED'              => 'Merchant not activated for Lazypay',
            'LP_EXCEEDS_MER_MAX_TXN_LIMIT'      => 'Merchant transaction limit exceeded',
            'LP_EXCEEDS_USER_MAX_TXN_LIMIT'     => 'You have crossed the maximum transaction limit',
            'LP_PRODUCT_SKU_DETAILS_REQUIRED'   => 'Mandatory product sku details required',
            'LP_ADDRESS_DETAILS_REQUIRED'       => 'Mandatory user address parameters  required',
            'LP_MOBILE_ALREADY_LINKED'          => 'Sorry! Your mobile is already linked with email <maskedEmail>',
            'LP_USER_DETAILS_REQUIRED'          => 'User email/mobile required',
            'LP_SIGNATURE_REQUIRED'             => 'Signature missing in request',
            'LP_SIGNATURE_MISMATCH'             => 'Request checksum(signature) not matching',
            'LP_INVALID_ACCESS_KEY'             => 'Invalid merchant access key',
            'LP_INVALID_EMAIL'                  => 'Transaction cannot be processed. Invalid email address provided',
            'LP_INVALID_MOBILE'                 => 'Transaction cannot be processed. Invalid mobile provides',
            'LP_INVALID_FIRSTNAME'              => 'Transaction cannot be processed. Invalid first name provided',
            'LP_INVALID_LASTNAME'               => 'Transaction cannot be processed. Invalid last name provided',
            'LP_BILL_OVER_DUES'                 => 'Transaction cannot be processed. Previous bill is over due',
            'CUSTOM_PARAMS_EXCEEDS_LIMIT'       => 'Custom parameters exceeds the specific limit',
            'LP_PRE_AUTH_DISABLED'              => 'Pre Auth is disabled for the merchant ',
            'LP_DUPLICATE_TRANSACTION_REQUEST'  => 'Transaction cannot be processed. Duplicate transaction request',
            'LP_RISK_RULE_VIOLATION'            => 'Transaction declined due to risk rules',
            'LP_OTP_REQUIRED'                   => 'Otp is required for this transaction',
            'LP_INCORRECT_OTP'                  => 'Sorry! The OTP entered is incorrect. Last <remainingAttempts> chances to enter correct OTP before account is locked',
            'LP_ACCOUNT_LOCKED'                 => 'Account is locked',
            'LP_TXN_TIMED_OUT'                  => 'Transaction  declined. Session timed out',
            'LP_INVALID_PAY_REQUEST'            => 'Payment declined. Invalid payment mode or txnRefNo',
            'LP_INVALID_PAYMENT_MODE'           => 'Please provide valid payment mode as <Payment_Mode>',
            'LP_ACCESS_DENIED'                  => 'Request not authenticated',
            'LP_INVALID_TOKEN'                  => 'Invalid transaction auth token'
        );
    }

    /*
     @method: index
     @ This is LazyPay class index method to Initiate PreAuth Payment.
     @ After successful calling this method, OTP sends on customer mobile, and OTP screen will show to customer
     @ Customer need to use OTP to complete the transaction
     @ Author MSA Feb 2019
     */
    public function index() {
        global $_SERVER;

        $data       = array(); // Initializing the data array to be passed on to template files

        //vernacular language for app
         if (!empty($this->request->get['language'])) 
         {
           $this->session->data['app_language'] = $this->request->get['language'];
           $this->language->switchLanguage(VERNACULAR_LANGUAGE[$this->request->get['language']]);
         }
         else if (!empty($this->session->data['app_language'])) 
         {             
             $this->language->switchLanguage(VERNACULAR_LANGUAGE[$this->session->data['app_language']]);
         }
         //----end code---
       
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('payment/lazypay', $data);

        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
        }
        
        $customer_id = $data['customer_id'] = $order_info['customer_id'];
        $order_no    = $data['order_no'] = $order_info['order_no'];
        $order_id    =  $order_info['order_id'];

        $log_data['access_token'] = $data['access_token'] = $this->config->get('lazypay_access_key');
        $log_data['order_id'] = $data['order_id'] = $order_info['order_id'];
        $log_data['order_no'] = $data['order_no'] = $order_info['order_no'];
       
        $log_data['buyer_registration_number'] = $data['buyer_registration_number'] = $this->customer->getCustomerLazypayRegistrationNumber();

        $data['currency'] = $order_info['currency_code'];
        $this->session->data['order_total_val']=$order_info['total'];
       
        $total = $this->currency->format($order_info['total'],
                                $order_info['currency_code'],
                                $order_info['currency_value'],
                                false);

        $log_data['transaction_amount'] = $data['transaction_amount'] = sprintf("%.2f", $total);
        
        $this->session->data['lazypay_pay_amount'] = $data['transaction_amount'];

        $log_data['session_id'] = $data['session_id'] = session_id();

        foreach($this->cart->getProducts() AS $product) {
            $cart_details[]= $product['model'];
        }

        $log_data['cart_details'] = $data['cart_details'] = implode(',' , $cart_details);
        $log_data['request_type'] = $data['request_type'] = 'AuthorizedPreAuth';
        $log_data['transaction_datetime'] = $data['transaction_datetime'] = time();

        $domain = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        
        $data['action'] = $this->url->link('payment/lazypay/callback', '', 'SSL');

        /*Initiate Pre-Auth payment */    
        $order_total  = $order_info['total']; 
        $api_endpoint = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_PREAUTH;
        $api_data = $this->getLazypayEligibilityAPIData($customer_id, $order_total);
        $api_data['merchantTxnId'] = $order_info['order_no']; //time();
        $lazypay = new LazypayPayment($this);
        $lazypay->setLazypaySignature('initiate_preauth',
                                                array(
                                                    'accessKey'     => $this->config->get('lazypay_access_key'),
                                                    'transactionId' => $api_data['merchantTxnId'],
                                                    'txnAmount'     => $order_total 
                                                )
                                            );
        $result = $lazypay->lazypay_payments( $api_endpoint, $api_data );

        $log_data   = array();
        $log_data['url']            = $api_endpoint;
        $log_data['request']        = json_encode($api_data);
        $log_data['order_no']       = $order_no;
        $log_data['order_id']       = $order_id;
        $log_data['request_type']   = 'InitiatePreAuth';
        $log_data['transaction_id'] = $api_data['merchantTxnId']; 
        $log_data['transaction_amount']  = $order_total; 
        $log_data['buyer_registration_number'] = $this->customer->getCustomerLazypayRegistrationNumber();
        $log_id = $lazypay->createLazypayTransactionLog($log_data);
        $data['dueDate']  = '';
        if(!empty($result['status']) && $result['status'] == 'IN_PROGRESS') 
        {
          $log_data = array();
          $log_data['response'] = json_encode($result);
          $log_data['status']   = 'SUCCESS'; 
          $log_data['message']  = 'Order status - ' . $result['status'];
          $log_data['lpTxnId']    = $result['lpTxnId'];
          $lazypay->updateLazypayTransactionLog($log_id, $log_data);
            
          $data['success']  = 1;
          $data['msg']      = 'Transaction initiated, confirm order with OTP.';
          $data['transaction_id'] = $api_data['merchantTxnId'];
          $data['dueDate']  = $this->session->data['lazypay_due_date'];

      }else{

          $log_data = array();
          $log_data['response'] = json_encode($result);
          $log_data['status']   = 'FAILED';  
          $error_message = 'Lazypay API error';
          if(!empty($result['message'])) {
            $error_message = $result['message'];
          }else{
            $error_message  = isset($this->_lazypayResponseCodes[$result['errorCode']])
                                      ? $this->_lazypayResponseCodes[$result['errorCode']]
                                      : $result['error'];
          }
          $log_data['message'] = $error_message;
          $log_data['lpTxnId']    = '';
          $lazypay->updateLazypayTransactionLog($log_id, $log_data);

          $data['success']  = 0;
          $data['msg']      = $error_message;
          $data['reason']   = $error_message;
          exit(0);
      }

        $this->session->data['log_id'] = $log_id;
        
        if(isset($this->request->post['one_page_checkout_payment_method'])){
            $data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/lazypay.tpl'))
        {
            return $this->load->view($this->config->get('config_template') . '/template/payment/lazypay.tpl',$data);
        }
        else
        {
            return $this->load->view('default/template/payment/lazypay.tpl',$data);
        }
       
    }

    /*
     @method: callback
     @ this callback is passed to lazypay, when customer placing order on lazypay.
     @ this will be called by lazypay on order success or failed 
     @ Author MSA Feb 2019
     */
    public function callback()
    {
        $order_total    = $this->request->post['transaction_amount'];
        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'] ?? $order_total;
        }

        $order_total = $order_info['total'];
        $order_no    = $order_info['order_no'];
        $order_id    = $order_info['order_id'];

        /*Add Order History data for new order*/
        $this->load->model('checkout/order');
        $input = array();
        $input['order_id']          = $this->session->data['order_id'];
        $input['order_status_id']   = $this->config->get('lazypay_order_status_id');
        unset($this->session->data['lazypay_pay_amount']);
        unset($this->session->data['lazypay_authorized_preauth_api']);
        $comment="Net Payable Amount: ".$this->currency->format($order_total)."<br>Amount to be paid by Lazypay: ".$order_total;
        $input['comment'] = $comment;
        $input['notes'] = 'Order Successfully placed on Lazypay.';
        $this->model_checkout_order->addOrderHistory($input);

        $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        exit;
    }

    /*
     @method: getLazypayEligibilityAPIData
     @ This is LazyPay class method to generate request data list
     @ Param: integer Customer id
     @ Param: string Order total amount
     @ Author MSA Feb 2019
     */
    public function getLazypayEligibilityAPIData(int $customer_id, string $order_total)
    {  
        $this->load->model('account/address');
        $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
        $lazypay_payment = new LazypayPayment($this);
        $customer_data = $lazypay_payment->getCustomerLazypayData($customer_id);
        
        $data = array();

        $data['userDetails'] = array(
                'mobile'    => $lazypay_payment->lazypay_validate($customer_data['lazypay_mobile'], 'phone'), 
                'email'     => $lazypay_payment->lazypay_validate($customer_data['lazypay_email'],'email'),
                'firstName' => $lazypay_payment->lazypay_validate($customer_data['firstname'],'name'),
                'lastName'  => $lazypay_payment->lazypay_validate($customer_data['lastname'],'name')
            );
        $data['amount'] = array(
                'value'     => $order_total,
                'currency'  => 'INR'
            );
        $data['source'] = 'WholeSaleBox';
        $data['address'] = array( //customer billing address
                'street1'   => $shipping_address['address_1'] ?? '',
                'street2'   => $shipping_address['address_2'] ?? '',
                'city'      => $shipping_address['city'] ?? '',
                'state'     => $shipping_address['zone_code'] ?? '',
                'country'   => $shipping_address['country'] ?? '',
                'zip'       => $shipping_address['postcode'] ?? ''
        );

        $products = $this->cart->getProducts();
        $index = 0;
        foreach ($products as $key => $value) {
           $productData = array(
                'productId'     => $is_pre_auth ? 'preauth' : $value['product_id'],
                'description'   => $is_pre_auth ? 'preauth' : $value['name'],
                'attributes'    => array(
                                    'size'=> '',
                                    'color'=> '',
                                ),
                'image'         => $value['image'],
                'shippable'     => true,
                'skus'          => array(
                                        array(
                                            'skuId' => $value['sku'],
                                            'price' => $order_total, 
                                            'attributes' => array(
                                                'size'=> '',
                                                'color'=> '',
                                                'itemsselected'=> '1',
                                                'amount'=> $order_total, 
                                            )
                                        )
                                    )
                );
        }

        $data['productSkuDetails'] = array( $productData );
        return $data;
    }    

    /*
     @method: verify_otp
     @ This is LazyPay class method to verify OTP sends on customer mobile
     @ After OTP verification, transaction get completed by Capture API 
     @ Author MSA Feb 2019
     */
    public function verify_otp()
    {
        $customer_id    = $this->customer->getId();
        $order_id       = $this->request->post['order_id'];
        $order_no       = $this->request->post['order_no'];
        $transaction_id = $this->request->post['transaction_id'];
        $otp            = $this->request->post['otp'];
        $order_total    = $this->request->post['transaction_amount'];
        $session_id     = $this->request->post['session_id']; 
        $cart_details   = $this->request->post['cart_details']; 
        $request_type   = $this->request->post['request_type']; 
        $transaction_datetime = $this->request->post['transaction_datetime']; 

        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
            $order_total = $order_info['total'];
        }
        
        $api_endpoint   = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_AUTHORISED_PREAUTH;
        $api_data       = $this->getLazypayEligibilityAPIData($customer_id, $order_total);
        $api_data['merchantTxnId'] = $transaction_id;
        $api_data['otp'] = $otp;

        $lazypay        = new LazypayPayment($this);
        $lazypay->setLazypaySignature('authorized_preauth',
                                        array(
                                                'accessKey'     => $this->config->get('lazypay_access_key'),
                                                'transactionId' => $transaction_id,
                                                'otp'           => $otp,
                                            )
                                    );
         /*Authorized Pre-Auth API calling*/   
        $result = $lazypay->lazypay_payments( $api_endpoint, $api_data );

        // create log for authorized_preauth process
        $log_data = array();
        $log_data['url']            = $api_endpoint;
        $log_data['request']        = json_encode($api_data);
        $log_data['request_type']   = 'AuthorizedPreAuth';
        $log_data['order_no']       = $order_no;
        $log_data['order_id']       = $order_id;
        $log_data['transaction_id'] = $transaction_id;
        $log_data['transaction_amount']  = $order_total; 
        $log_data['buyer_registration_number'] = $this->customer->getCustomerLazypayRegistrationNumber();
        $log_id = $lazypay->createLazypayTransactionLog($log_data);
        
        $response = array();

         if(!empty($result['status']) && $result['status'] == 'INITIATED') 
         {
            $log_data = array();
            $log_data['response']   = json_encode($result);
            $log_data['status']     = 'SUCCESS';
            $log_data['message']    = 'Order Transaction Status - ' .$result['status'];
            $log_data['lpTxnId']    = $result['lpTxnId'];

            $lazypay->updateLazypayTransactionLog($log_id, $log_data);
            
            $response['success'] = 1;
            $response['message'] = 'Order Transaction Status - ' .$result['status'];
         
            $this->session->data['lazypay_authorized_preauth_api'] = $result;

         } else {
            
            // Lazypay transaction failed at pre-auth stag
            $log_data = array();
            $log_data['response']   = json_encode($result);
            $log_data['status']  = 'FAILED';
            
            if(!empty($result['message'])) {
                $error = $result['message'];
            }else{
                 $error = isset($this->_lazypayResponseCodes[$result['errorCode']])
                            ? $this->_lazypayResponseCodes[$result['errorCode']]
                            : $result['error'];
            }
                       
            $log_data['message'] = "Lazypay response - " . $error;
            $log_data['lpTxnId']    = '';
            $lazypay->updateLazypayTransactionLog($log_id, $log_data);
            
            $response['success'] = 0;
            $response['message'] = $error;

            $this->session->data['lazypay_authorized_preauth_api'] = $result;

         } 

       echo json_encode($response);

    }

    /*
     @method: resend_otp
     @ This is LazyPay class method to resend OTP on customer mobile
     @ After OTP verification, transaction get completed by Capture API 
     @ Author MSA Feb 2019
     */
    public function resend_otp()
    {
        global $_SERVER;

        $data       = array(); // Initializing the data array to be passed on to template files
       
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('payment/lazypay', $data);

        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
        }

        $customer_id = $data['customer_id'] = $order_info['customer_id'];
        $order_no    = $data['order_no'] = $order_info['order_no'];
        $order_id    = $data['order_id'] = $order_info['order_id'];

        $log_data['access_token'] = $data['access_token'] = $this->config->get('lazypay_access_key');
        $log_data['order_id'] = $data['order_id'] = $order_info['order_id'];
        $log_data['order_no'] = $data['order_id'] = $order_info['order_no'];
       
        $log_data['buyer_registration_number'] = $data['buyer_registration_number'] = $this->customer->getCustomerLazypayRegistrationNumber();

        $data['currency'] = $order_info['currency_code'];
        $this->session->data['order_total_val']=$order_info['total'];
       
        $total = $this->currency->format($order_info['total'],
                                $order_info['currency_code'],
                                $order_info['currency_value'],
                                false);

        $log_data['transaction_amount'] = $data['transaction_amount'] = sprintf("%.2f", $total);
        
        $this->session->data['lazypay_pay_amount'] = $data['transaction_amount'];

        $log_data['session_id'] = $data['session_id'] = session_id();

        foreach($this->cart->getProducts() AS $product) {
            $cart_details[]= $product['model'];
        }

        $log_data['cart_details'] = $data['cart_details'] = implode(',' , $cart_details);
        $log_data['request_type'] = $data['request_type'] = 'AuthorizedPreAuth';
        $log_data['transaction_datetime'] = $data['transaction_datetime'] = time();

        $domain = $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        
        $data['action'] = $this->url->link('payment/lazypay/callback', '', 'SSL');

        /*Initiate Pre-Auth payment */    
        $order_total  = $order_info['total']; 
        $api_endpoint = LAZYPAY_PAYMENT_BASE_URL . LAZYPAY_PAYMENT_PREAUTH;
        $api_data = $this->getLazypayEligibilityAPIData($customer_id, $order_total);
        $api_data['merchantTxnId'] = $order_info['order_no']; //time();
        $lazypay = new LazypayPayment($this);
        $lazypay->setLazypaySignature('initiate_preauth',
                                                array(
                                                    'accessKey'     => $this->config->get('lazypay_access_key'),
                                                    'transactionId' => $api_data['merchantTxnId'],
                                                    'txnAmount'     => $order_total 
                                                )
                                            );
        $result = $lazypay->lazypay_payments( $api_endpoint, $api_data );
        $log_data   = array();
        $log_data['url']            = $api_endpoint;
        $log_data['request']        = json_encode($api_data);
        $log_data['order_no']       = $order_no;
        $log_data['order_id']       = $order_id;
        $log_data['request_type']   = 'InitiatePreAuth';
        $log_data['transaction_id'] = $api_data['merchantTxnId']; 
        $log_data['transaction_amount']  = $order_total; 
        $log_data['buyer_registration_number'] = $this->customer->getCustomerLazypayRegistrationNumber();
        $log_id = $lazypay->createLazypayTransactionLog($log_data);
        $api_response = array();
        if(!empty($result['status']) && $result['status'] == 'IN_PROGRESS') 
        {
          $log_data = array();
          $log_data['response'] = json_encode($result);
          $log_data['status']   = 'SUCCESS'; 
          $log_data['message']  = 'Order status - ' . $result['status'];
          $log_data['lpTxnId']    = $result['lpTxnId'];
          $lazypay->updateLazypayTransactionLog($log_id, $log_data);
            
          $api_response['success']  = 1;
          $api_response['msg']      = 'Transaction initiated, confirm order with OTP.';
          $api_response['transaction_id'] = $api_data['merchantTxnId'];
          $api_response['lpTxnId']  = $result['lpTxnId'];
      }else{

          $log_data = array();
          $log_data['response'] = json_encode($result);
          $log_data['status']   = 'FAILED';  
          $error_message = 'Lazypay API error';
          if(!empty($result['message'])) {
            $error_message = $result['message'];
          }else{
            $error_message  = isset($this->_lazypayResponseCodes[$result['errorCode']])
                                      ? $this->_lazypayResponseCodes[$result['errorCode']]
                                      : $result['error'];
          }
          $log_data['message'] = $error_message;
          $log_data['lpTxnId']    = '';
          $lazypay->updateLazypayTransactionLog($log_id, $log_data);

          $api_response['success']  = 0;
          $api_response['msg']      = $error_message;
          $api_response['reason']   = $error_message;
      }

        $this->session->data['log_id'] = $log_id;

        echo json_encode($api_response); 

    }


}

