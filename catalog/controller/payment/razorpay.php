<?php

class ControllerPaymentRazorpay extends Controller {

    public function index() {
        $data['button_back'] = $this->language->get('button_back');
        $data['button_confirm'] = $this->language->get('button_confirm');

        $selector = array('order' => array());
        //Check if payment made via fixed amount coupon or cashback  
        $order_info = OrderPayment::getOrderInfoIfNetPayableAmountApplicable($this->db, $this->session->data['order_id'], $selector)['order'];
        if (!empty($this->session->data['net_order_totals'])) {
            $order_info['total'] = $this->session->data['net_order_totals'];
        }

        $data['key_id'] = $this->config->get('razorpay_key_id');
        
        if(!(strtolower(SITE_ENVIRONMENT) == 'production')) {
					$data['key_id']       = "rzp_test_kPLVqzulyibTrP";
			    $data['key_secret']   = "nqAHhJaG0zoR1F7p5ChU4vBd";
				}
        $data['currency_code'] = 'INR'; //Harcoding to INR as payment gateway accepts only INR payments
        $total = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $ntotal = $this->currency->convertLiveRates($total, $order_info['currency_code'], $data['currency_code']);
        $ntotal = sprintf("%.2f", $ntotal);
        
        $data['total'] = $ntotal * 100;
        $data['display_amount'] = $total;
        $data['display_currency'] = $order_info['currency_code'];
        $data['merchant_order_id'] = $this->session->data['order_id'];
        $data['card_holder_name'] = trim($order_info['firstname'] . ' ' . $order_info['lastname']);
        $data['email'] = $order_info['email'];
        $data['phone'] = $order_info['telephone'];
        $data['name'] = $this->config->get('config_name');
        $data['lang'] = $this->session->data['language'];
        $data['return_url'] = $this->url->link('payment/razorpay/callback', '', 'SSL');
        $data['store_id'] = $this->config->get('config_store_id');

        if (isset($this->request->post['one_page_checkout_payment_method'])) {
            $data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/razorpay.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/razorpay.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/razorpay.tpl', $data);
        }
    }

    private function get_curl_handle($payment_id, $amount) {
        $url = 'https://api.razorpay.com/v1/payments/' . $payment_id . '/capture';
        $key_id = $this->config->get('razorpay_key_id');
        $key_secret = $this->config->get('razorpay_key_secret');
        if(!(strtolower(SITE_ENVIRONMENT) == 'production')) {
					$key_id       = "rzp_test_kPLVqzulyibTrP";
			    $key_secret   = "nqAHhJaG0zoR1F7p5ChU4vBd";
				}
        $fields_string = "amount=$amount";

        //cURL Request
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $key_id . ':' . $key_secret);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/ca-bundle.crt');

        return $ch;
    }

    public function callback() {
        if (isset($this->request->request['razorpay_payment_id']) && isset($this->request->request['merchant_order_id'])) {

            $razorpay_payment_id = $this->request->request['razorpay_payment_id'];
            $merchant_order_id = $this->request->request['merchant_order_id'];

            $selector = array('order' => array());
            $order_info = OrderInfo::getOrderInfo($this->db, $merchant_order_id, '', $selector)['order'];
            if (!empty($this->session->data['net_order_totals'])) {
                $order_info['total'] = $this->session->data['net_order_totals'];
            }
            $total = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
            $ntotal = $this->currency->convertLiveRates($total, $order_info['currency_code'], 'INR');
            $ntotal = sprintf("%.2f", $ntotal);
            $amount = $ntotal * 100;

            $success = false;
            $error = '';

            try {
                $ch = $this->get_curl_handle($razorpay_payment_id, $amount);

                //execute post
                $result = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                $razorpay = new Razorpay($this);
                if ($result === false) {
                    $success = false;
                    $error = 'Curl error: ' . curl_error($ch);
                    // query payment
                    $queryResult = $razorpay->paymentsTransactionsEnquiry($razorpay_payment_id);
                    $queryResponse = json_decode($queryResult, true);
                } else {
                    $response_array = json_decode($result, true);
                    //Check success response
                    if ($http_status === 200 and isset($response_array['error']) === false) {
                        $success = true;
                    } else {
                        $success = false;
                        
                        // query payment
                        $queryResult = $razorpay->paymentsTransactionsEnquiry($razorpay_payment_id);
                        $queryResponse = json_decode($queryResult, true);
                        
                        if (!empty($response_array['error']['code'])) {
                            $error = $response_array['error']['code'] . ':' . $response_array['error']['description'];
                        } else {
                            $error = 'RAZORPAY_ERROR:Invalid Response <br/>' . $result;
                        }
                    }
                }

                //close connection
                curl_close($ch);
                
                /*** Send mail to capture payment if status is authorized ***/
                if(!empty($queryResponse)) {
                  if(!empty($queryResponse['status']) && strtolower($queryResponse['status']) == 'authorized') {
                    $mail_data['status'] = $queryResponse['status'];
                    $mail_data['razorpay_payment_id'] = $razorpay_payment_id;
                    $mail_data['merchant_order_id'] = $merchant_order_id;
                    $mail_data['order_no'] = $order_info['order_no'];
                    $this->sendMailToCapturePayment($mail_data);
                    $success = true;
                  }
                }
            } catch (Exception $e) {
                $success = false;
                $error = 'OPENCART_ERROR:Request to Razorpay Failed';
            }

            if ($success === true) {
                if(!empty($queryResponse)) {
                  $response_array = $queryResponse;
                }
                $this->load->model('checkout/order');
                $input = array();
                $input['order_id'] = $merchant_order_id;
                $input['order_status_id'] = $this->config->get('razorpay_order_status_id');
                $input['comment'] = 'Payment Successful. Razorpay Payment Id:' . $razorpay_payment_id;

                $this->model_checkout_order->addOrderHistory($input);

                $payable_amt = (float) $amount;
                $paid_amt = (float) $response_array['amount'];
                
                $successfull = '1';
                if($response_array['status'] == 'authorized') {
                    $successfull = '0';
                }
                //Define data array 
                $data = array();
                $data['order_id'] = (int) $order_info['order_id'];
                $data['merchant_txn_id'] = $response_array['id'];
                $data['order_no'] = $order_info['order_no'];
                $data['txn_status'] = $response_array['status'];
                $data['payment_mode'] = $response_array['method'];
                $data['amount'] = (float) ($paid_amt / 100);
                $data['txn_date_time'] = date('Y-m-d H:i:s', $response_array['created_at']);
                $data['date_added'] = 'NOW()';
                $data['payment_gateway'] = 'razorpay';
                $data['successfull'] = $successfull;
                $data['reference'] = '';
                $data['payment_link'] = 'Customer Direct Payment';
                $data['json_format'] = serialize($result);
                $data['user_id'] = '0';
                $data['sales_staff_id'] = '0';

                //Insert data to Order Payment table
                OrderPayment::insertOrderPayment($this->db, $data);
                
                echo '<html>' . "\n";
                echo '<head>' . "\n";
                echo '  <meta http-equiv="Refresh" content="0; url=' . $this->url->link('checkout/success') . '">' . "\n";
                echo '</head>' . "\n";
                echo '<body>' . "\n";
                echo '  <p>Please follow <a href="' . $this->url->link('checkout/success') . '">link</a>!</p>' . "\n";
                echo '</body>' . "\n";
                echo '</html>' . "\n";
                exit();
            } else {
                echo '<html>' . "\n";
                echo '<head>' . "\n";
                echo '  <meta http-equiv="Refresh" content="0; url=' . $this->url->link('checkout/failure') . '">' . "\n";
                echo '</head>' . "\n";
                echo '<body>' . "\n";
                echo '  <p>Please follow <a href="' . $this->url->link('checkout/failure') . '">link</a>!</p>' . "\n";
                echo '</body>' . "\n";
                echo '</html>' . "\n";
                exit();
            }
        } else {
            echo 'An error occured. Contact site administrator, please!';
        }
    }

    public function convertCurrencyByLiveApi($amount) {
        if ($this->config->get('config_store_id') == 2) {
            $converted_total = $this->currency->convertLiveRates($amount, 'USD', 'INR');
        } else {
            $converted_total = $this->currency->convertLiveRates($amount, 'INR', 'INR');
        }
        $amount = $this->currency->format($converted_total, '', '', false);
        return $amount;
    }
    
    public function callbackPaymentLink() {
        
      $json_request_data = file_get_contents('php://input');
      $request_data = json_decode($json_request_data, true);
      // $this->updateLogFileToTest($request_data);
      $rt = array();
      $rt['status'] = '1';
      $rt['message'] = 'Request handeled successfully !!!';
           
      // Verify Signature
      $headers = getallheaders();
      $received_signature = $headers['X-Razorpay-Signature'];
      $received_message = $json_request_data;
      $webhook_secret_key = RAZORPAY_WEBHOOK_SECRET;
      $expected_signature = hash_hmac('sha256', $received_message, $webhook_secret_key);
      if($expected_signature == $received_signature) {
        if((!empty($request_data['entity']) && $request_data['entity'] == 'event')
         && !empty($request_data['event'])) {
          
          $successfull = 0;
          $razorpay = new Razorpay($this);
          $event_name_arr = explode('.',$request_data['event']);
          
          $update_data = array();
          if(!empty($event_name_arr[0]) && $event_name_arr[0] == 'payment') {
            $payload_data = $request_data['payload']['payment'];
            if(!empty($payload_data['entity']['invoice_id'])) { // for offline paymentLinks
              $order_payment = $razorpay->getOrderDetailByMerchant_txn_id($payload_data['entity']['invoice_id'], $successfull);
              $update_data['invoice_id'] = $payload_data['entity']['invoice_id'];
            } else if(!empty($payload_data['entity']['id'])) { // for live payments manual capture
              $order_payment = $razorpay->getOrderDetailByMerchant_txn_id($payload_data['entity']['id'], $successfull);
            }
          }
          
          if(!empty($order_payment)) {
              if(strtolower($payload_data['entity']['status']) == 'captured') {
                  $successfull = 1;
              }
              
              $update_data['TxStatus'] = $payload_data['entity']['status'];
              $update_data['TxMsg'] = "Razorpay Payment Link";
              $update_data['json_format'] = serialize($json_request_data);
              $update_data['successfull'] = $successfull;
              //$update_data['OrderNO'] = $order_payment['order_no'];
              $update_data['TxId'] = $payload_data['entity']['id'];
              $update_data['payment_gateway'] = "razorpay";
              $update_data['payment_mode'] = $payload_data['entity']['method'];
              //$update_data['payment_id'] = $order_payment['payment_id'];
              //$update_data['order_id'] = $order_payment['order_id'];
              $update_data['amount'] = $payload_data['entity']['amount']/100;
              $update_data['currency'] = "INR";
              $update_data['paymentMode'] = 'Razorpay';
              $update_data['txn_date_time'] = gmdate("Y-m-d H:i:s", ($request_data['created_at'])+(330 * 60));
              $razorpay->updatePaymentDetailsIntoDb($this, $update_data);
          } else {
            $rt['status'] = '0';
            $rt['message'] = 'Invalid request or request is already handled !!!';
          }
        } else {
          $rt['status'] = '0';
          $rt['message'] = 'Invalid request data !!!';
        }
      } else {
        $rt['status'] = '0';
        $rt['message'] = 'Invalid request signature !!!';
      }
      echo json_encode($rt); exit;
    }
    
    public function sendMailToCapturePayment($mail_data) {
      $mail = new PHPMailer();
    
      $mail->isSMTP();
      $mail->Host = $this->config->get('config_mail_smtp_hostname');
      $mail->Port = $this->config->get('config_mail_smtp_port');
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth = true;
      $mail->Username = $this->config->get('config_mail_smtp_username');
      $mail->Password = $this->config->get('config_mail_smtp_password');
      
      if(!(strtolower(SITE_ENVIRONMENT) == 'production')) {
        $mail->addAddress("anurag.jain@wholesalebox.co",'Anurag');
      } else {
        $mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
        $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
      }
      $mail->Subject = "RazorPay: Please capture payment !!!";
      $html = '<div>Please capture razorpay payment with following details:</div><br>';
      $html .= 'Payment Id: '.$mail_data['razorpay_payment_id'].'<br>';
      $html .= 'Current Payment Status: '.ucfirst($mail_data['status']).'<br>';
      $html .= 'Order No.: '.$mail_data['order_no'].'<br><br><br>';
      $html .= '<div>Thanks & Regards<br>WholesaleBox</div>';
      $mail->msgHTML($html);
      $mail->send();
    }
    
    // public function updateLogFileToTest($data) {
    //   $folder_path = DIR_IMAGE.'/webhook_logs';
    //   if (!file_exists($folder_path)) {
    //       mkdir($folder_path, 7777, true);
    //   }
    //   $filename = 'webhook_logs_'.date("Y-m-d_H-i",time()).'.txt';
    //   $filepath = $folder_path.'/'.$filename;
    //   $update_data = $data;
    //   $file = fopen($filepath, 'w');
    //   fwrite($file, json_encode($update_data));
    //   fclose ($file);
    // }
}
