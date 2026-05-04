<?php

class ControllerPaymentpaytm extends Controller {

    public function index() {
        require_once(DIR_SYSTEM . 'encdec_paytm.php');

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('payment/paytm', $data);
        $this->load->model('payment/paytm');

        $selector = array('order' => array());
        //Check if payment made via fixed amount coupon or cashback  
        $order_info = OrderPayment::getOrderInfoIfNetPayableAmountApplicable($this->db, $this->session->data['order_id'], $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
        }

        $data['merchant'] = $this->config->get('paytm_merchant');

        $data['trans_id'] = $order_info['order_no'];
        $data['currency'] = $this->config->get('config_currency');
        //$data['amount'] = $this->currency->format($order_info['total'],$order_info['currency_code'],$order_info['currency_value'], false);
        $ntotal = $this->currency->convert($order_info['total'], $this->currency->getCode(), $data['currency']);
        $data['amount'] = sprintf("%.2f", $ntotal);
        $data['channel_id'] = "WEB";
        $data['industry_type_id'] = $this->config->get('paytm_industry');
        ;
        $data['website'] = $this->config->get('paytm_website');

        if (!empty($order_info['customer_id'])) {
            $data['customer_id'] = $order_info['customer_id'];
        } else {
            $data['customer_id'] = $order_info['email'];
        }

        $data['email'] = !empty(trim($order_info['email'])) ? trim($order_info['email']) : 'noemailwsb@gmail.com';
        $data['mobile_no'] = preg_replace('#[^0-9]{0,13}#is', '', $order_info['telephone']);

        if ($this->config->get('paytm_environment') == "P") {
            $data['action_url'] = PAYTM_PAYMENT_URL_PROD;
        } else {
            $data['action_url'] = PAYTM_PAYMENT_URL_TEST;
        }

        if ($_SERVER['HTTPS']) {
            $data['callback_url'] = HTTPS_SERVER . PAYTM_CALLBACK_URL_TAIL_PART;
        } else {
            $data['callback_url'] = HTTP_SERVER . PAYTM_CALLBACK_URL_TAIL_PART;
        }
        $parameters = array(
            "MID" => $data['merchant'],
            "ORDER_ID" => $data['trans_id'],
            "CUST_ID" => $data['customer_id'],
            "TXN_AMOUNT" => $data['amount'],
            "CHANNEL_ID" => $data['channel_id'],
            "INDUSTRY_TYPE_ID" => $data['industry_type_id'],
            "WEBSITE" => $data['website'],
            "MOBILE_NO" => $data['mobile_no'],
            "EMAIL" => $data['email'],
        );
        if ($this->config->get('paytm_callbackurl') == '1') {
            $parameters["CALLBACK_URL"] = $data['callback_url'];
        }

        $mer = htmlspecialchars_decode(decrypt_e($this->config->get('paytm_key'), PAYTM_SECRET_KEY), ENT_NOQUOTES);
        $mer = rtrim($mer);
        $data['checkSum'] = getChecksumFromArray($parameters, $mer);
        $data['paytm_callbackurl'] = $this->config->get('paytm_callbackurl');

        if (isset($this->request->post['one_page_checkout_payment_method'])) {
            $data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paytm.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/paytm.tpl', $data);
        } else {
            return $this->load->view('payment/paytm.tpl', $data);
        }
    }

    public function callback() {

        require_once(DIR_SYSTEM . 'encdec_paytm.php');

        $param = array();
        foreach ($_POST as $key => $value) {
            if ($key != "route") {
                $param[$key] = $_POST[$key];
            }
        }
        $isValidChecksum = false;
        $txnstatus = false;
        $authStatus = false;
        $mer = htmlspecialchars_decode(decrypt_e($this->config->get('paytm_key'), PAYTM_SECRET_KEY), ENT_NOQUOTES);
        $mer = rtrim($mer);
        if (isset($_POST['CHECKSUMHASH'])) {
            $checksum = htmlspecialchars_decode($_POST['CHECKSUMHASH']);
            $return = verifychecksum_e($param, $mer, $checksum);
            if ($return == "TRUE")
                $isValidChecksum = true;
        }

        if ($param['STATUS'] == "TXN_SUCCESS") {
            $txnstatus = true;
        }

        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $this->session->data['order_id'], '', $selector)['order'];

        if ($order_info) {
            $data = array(); // Initializing the data array to be passed on to template files
            // Autoloading the lanugage
            $this->load->autoLoadLanguage('payment/paytm', $data);

            $data['title'] = sprintf($data['heading_title'], $this->config->get('config_name'));
            $data['language'] = $this->language->get('code');
            $data['heading_title'] = sprintf($data['heading_title'], $this->config->get('config_name'));
            $data['text_success_wait'] = sprintf($data['text_success_wait'], $this->url->link('checkout/success'));
            $data['text_failure_wait'] = sprintf($data['text_failure_wait'], $this->url->link('checkout/onepagecheckout'));


            if ($txnstatus && $isValidChecksum) {
                $authStatus = true;

                $value = $_POST;
                $value['order_no'] = $_POST['ORDERID'];
                $value['order_id'] = $this->session->data['order_id'];
                $value['json_response'] = serialize($_POST);

                $payment_gateway = new Paytm($this);

                $this->load->model('checkout/order');
                $input = array();
                $input['order_id'] = $this->session->data['order_id'];
                $input['order_status_id'] = $this->config->get('paytm_order_status_id');
                $input['comment'] = $_POST['RESPMSG'];
                $notes = "";
                $notes .= !empty($_POST['TXNID']) ? (" Merchant Txn ID: " . $_POST['TXNID'] . ".") : "";
                $notes .= !empty($_POST['TXNAMOUNT']) ? (" Amount: " . $_POST['TXNAMOUNT'] . ".") : "";
                $notes .= !empty($_POST['STATUS']) ? (" Transaction Status: " . $_POST['STATUS'] . ".") : "";
                $notes .= !empty($_POST['GATEWAYNAME']) ? (" Paytm Gateway: " . $_POST['GATEWAYNAME'] . ".") : "";
                $notes .= !empty($_POST['BANKNAME']) ? (" Bank Name: " . $_POST['BANKNAME'] . ".") : "";
                $input['notes'] = $notes;
                $this->model_checkout_order->addOrderHistory($input);

                /*
                 * Insert into payment table after splitting order so that advance can bifurcate according to suborders
                 */
                $payment_gateway->insertDirectCustomerPaymentDetailsIntoDb($_POST, $value);

                $data['continue'] = $this->url->link('checkout/success');
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paytm_success.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/payment/paytm_success.tpl';
                } else {
                    $this->template = 'payment/paytm_success.tpl';
                }

                $this->children = array(
                    'common/column_left',
                    'common/column_right',
                    'common/content_top',
                    'common/content_bottom',
                    'common/footer',
                    'common/header'
                );

                $this->response->setOutput($this->load->view($this->template, $data));
            } else {
                $this->load->model('checkout/order');

                $data['continue'] = $this->url->link('checkout/one_page_checkout', '', 'SSL', 'payment');
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paytm_failure.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/payment/paytm_failure.tpl';
                } else {
                    $this->template = 'payment/paytm_failure.tpl';
                }

                $this->children = array(
                    'common/column_left',
                    'common/column_right',
                    'common/content_top',
                    'common/content_bottom',
                    'common/footer',
                    'common/header'
                );

                $this->response->setOutput($this->load->view($this->template, $data));
            }
        }
    }

}

?>
