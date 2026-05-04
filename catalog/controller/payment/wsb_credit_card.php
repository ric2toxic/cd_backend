<?php

set_include_path('./citrus/lib/' . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . './citrus/' . PATH_SEPARATOR . get_include_path());
require_once ('CitrusPay.php');
require_once ('Zend/Crypt/Hmac.php');

class ControllerPaymentWsbCreditCard extends Controller {

    function generateHmacKey($data, $apiKey = null) {
        $hmackey = Zend_Crypt_Hmac::compute($apiKey, "sha1", $data);
        return $hmackey;
    }

    public function index() {
        global $_SERVER;

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('payment/wsb_credit_card', $data);

        $selector = array('order' => array());

        //Check if payment made via fixed amount coupon or cashback  
        $order_info = OrderPayment::getOrderInfoIfNetPayableAmountApplicable($this->db, $this->session->data['order_id'], $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
        }

        $data['sid'] = $this->config->get('citrus_account');
        $data['citrus_module'] = $this->config->get('wsb_credit_card_module');

        $store_id = $this->config->get('config_store_id');
        if ($store_id == INTERNATIONAL_STORE_ID) {
            $data['citrus_vanityurl'] = international_citrus_vanityurl;
            $data['citrus_access_key'] = international_citrus_access_key;
            $data['citrus_secret_key'] = international_citrus_secret_key;
        } else {
            $data['citrus_vanityurl'] = $this->config->get('wsb_credit_card_vanityurl');
            $data['citrus_access_key'] = $this->config->get('wsb_credit_card_access_key');
            $data['citrus_secret_key'] = $this->config->get('wsb_credit_card_secret_key');
        }

        $data['citrus_merchant_trans_id'] = $order_info['order_no'];
        $data['currency'] = $order_info['currency_code'];
        $total = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $data['total'] = sprintf("%.2f", $total);

        $data['firstname'] = $order_info['payment_firstname'];
        $data['lastname'] = $order_info['payment_lastname'];
        $data['addr1'] = $order_info['payment_address_1'];
        $data['city'] = $order_info['payment_city'];
        $data['state'] = $order_info['payment_zone'];
        $data['zip'] = $order_info['payment_postcode'];
        $data['country'] = $order_info['payment_country'];
        $data['email'] = $order_info['email'];
        $data['phone'] = $order_info['telephone'];

        CitrusPay::setApiKey($data['citrus_secret_key'], $data['citrus_module']);
        $vanityUrl = $data['citrus_vanityurl'];
        $currency = $data['currency'];
        $merchantTxnId = $data['citrus_merchant_trans_id'];
        $orderAmount = $data['total'];
        $tmpdata = "$vanityUrl$orderAmount$merchantTxnId$currency";

        $secSignature = $this->generateHmacKey($tmpdata, CitrusPay::getApiKey());
        $action = CitrusPay::getCPBase() . "$vanityUrl";

        $data['action'] = $action;
        $data['secSignature'] = $secSignature;
        $data['baseurl'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . dirname($_SERVER['PHP_SELF']) . '/';
        $data['lang'] = $this->session->data['language'];
        $data['notify_url'] = $this->url->link('payment/wsb_credit_card/callback', '', 'SSL');
        $data['redir_url'] = $this->url->link('payment/wsb_credit_card/callback', '', 'SSL');
        if (isset($this->request->post['one_page_checkout_payment_method'])) {
            $data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/wsb_credit_card.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/wsb_credit_card.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/wsb_credit_card.tpl', $data);
        }
    }

    public function callback() {
        $data['citrus_module'] = $this->config->get('wsb_credit_card_module');
        $data['citrus_secret_key'] = $this->config->get('wsb_credit_card_secret_key');
        CitrusPay::setApiKey($data['citrus_secret_key'], $data['citrus_module']);

        if (strtoupper($_POST['TxStatus']) == 'SUCCESS') {
            //resp signature validation
            $str = $_POST['TxId'] . $_POST['TxStatus'] . $_POST['amount'] . $_POST['pgTxnNo'] . $_POST['issuerRefNo'] . $_POST['authIdCode'] . $_POST['firstName'] . $_POST['lastName'] . $_POST['pgRespCode'] . $_POST['addressZip'];
            $respSig = $_POST['signature'];
            if ($this->generateHmacKey($str, CitrusPay::getApiKey()) == $respSig) {

                $value = $_POST;
                $value['order_no'] = $_POST['TxId'];
                $value['order_id'] = $this->session->data['order_id'];
                $value['json_response'] = serialize($_POST);

                $payment_gateway = new Citrus($this);


                $notes = "";
                $notes .= !empty($_POST['TxId']) ? (" Merchant Txn ID: " . $_POST['TxId'] . ".") : "";
                $notes .= !empty($_POST['amount']) ? (" Amount: " . $_POST['amount'] . ".") : "";
                $notes .= !empty($_POST['TxStatus']) ? (" Transaction Status: " . $_POST['TxStatus'] . ".") : "";
                $notes .= !empty($_POST['pgTxnNo']) ? (" PG Transaction ID: " . $_POST['pgTxnNo'] . ".") : "";

                $this->load->model('checkout/order');
                $input = array();
                $input['order_id'] = $this->session->data['order_id'];
                $input['order_status_id'] = $this->config->get('wsb_credit_card_order_status_id');
                $input['comment'] = $_POST['TxMsg'];
                $input['notes'] = $notes;
                $this->model_checkout_order->addOrderHistory($input);
                /*
                 * Insert into payment table after splitting order so that advance can bifurcate according to suborders
                 */
                $payment_gateway->insertDirectCustomerPaymentDetailsIntoDb($_POST, $value);

                $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
            } else {
                $this->session->data['error'] = "Invalid or forged transaction attempt..";  //forged
                $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
            }
        } else {
            $this->session->data['error'] = "Citrus Response - " . $_POST['TxMsg'];
            $this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL', 'payment'));
        }
    }

    public function notifyInvoice() {
        $data = $_REQUEST;
        $data['json_response'] = serialize($data);
        //echo "<pre>"; print_r($data); echo "</pre>";

        $secret_key = $this->config->get('wsb_credit_card_secret_key');

        $value = "";
        $flag = "true";
        if (isset($data['TxId'])) {
            $value .= $data['TxId'];
        }
        if (isset($data['TxStatus'])) {
            $value .= $data['TxStatus'];
        }
        if (isset($data['amount'])) {
            $value .= $data['amount'];
        }
        if (isset($data['pgTxnNo'])) {
            $value .= $data['pgTxnNo'];
        }
        if (isset($data['issuerRefNo'])) {
            $value .= $data['issuerRefNo'];
        }
        if (isset($data['authIdCode'])) {
            $value .= $data['authIdCode'];
        }
        if (isset($data['firstName'])) {
            $value .= $data['firstName'];
        }
        if (isset($data['lastName'])) {
            $value .= $data['lastName'];
        }
        if (isset($data['pgRespCode'])) {
            $value .= $data['pgRespCode'];
        }
        if (isset($data['addressZip'])) {
            $value .= $data['addressZip'];
        }
        if (isset($data['signature'])) {
            $signature = $data['signature'];
        }

        $respSignature = hash_hmac('sha1', $value, $secret_key);
        if ($signature != "" && strcmp($signature, $respSignature) != 0) {
            $flag = "false";
        }

        $payment_gateway = new Citrus($this);

        if ($flag == "true") {
            $payment_gateway->updatePaymentDetailsIntoDb($this, $data);
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        } else {

            $payment_gateway->sendInvalidAttemptEmailInPaymentGateway($data, 'citrus');
        }
    }

}

?>
