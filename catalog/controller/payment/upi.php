<?php

class ControllerPaymentUPI extends Controller {

	public function index(){

		global $_SERVER;
		$data = array();

		$data['order_id'] = '';
	    $data['payer_vpa'] = $this->request->post['upi_vpa']; // set payer_vpa

		if(isset($this->session->data['order_id'])){

    		$data['order_id'] = $this->session->data['order_id'];

    		if(isset($this->request->post['one_page_checkout_payment_method'])){
				$data['one_page_checkout_payment_method'] = $this->request->post['one_page_checkout_payment_method'];
			} else {
				$data['one_page_checkout_payment_method'] = 'one_page_checkout_payment_method';
			}

			// initiate UPI transaction process
	        $this->upiInitiateTransaction($data);
    	}
	}

	private function upiInitiateTransaction($data){

    	if(!empty($data['order_id'])){

    		// get order info from order_id
    		$selector = array('order' => array());
	        // $order_info = OrderInfo::getOrderInfo($this->db, 
	        //                                       $data['order_id'], 
	        //                                       '',
	        //                                       $selector)['order'];
	        $order_info = OrderPayment::getOrderInfoIfNetPayableAmountApplicable($this->db, $this->session->data['order_id'], $selector)['order'];

            if (!empty($this->session->data['net_order_totals'])) {
                $order_info['total'] = $this->session->data['net_order_totals'];
            }
						$total = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
            $order_info['total'] = number_format(((float)$total), 2, '.', '');
			$data['amount']   = sprintf("%.2f", $order_info['total']);
			$data['total']    = sprintf("%.2f", $order_info['total']);
			$data['order_no'] = $order_info['order_no'];
			$data['currency_code'] = $order_info['currency_code'];
			$data['currency_value'] = $order_info['currency_value'];
			$data['firstname'] = $order_info['firstname'];
			$data['telephone'] = $order_info['telephone'];
			$data['expreAfterRule'] = '3';
	        $data['upi_txn_id'] = $this->generateTransactionID($data['order_no']);

			$upi = new Upi($this);

			//create request
			$response_payload = $upi->initiateTransaction($data);

			$data['initiate_response'] = str_replace('"', '\'', json_encode($response_payload));

	        $data['action'] = '';
	        
	        $this->session->data['payer_vpa'] = $data['payer_vpa'];
					// Update amount from response
					if(!empty($response_payload->amount)) {
						$data['amount'] = $data['total'] = $response_payload->amount;
					}
	        if($response_payload->status == "00"){
	        	// Insert payer vpa in DB
	        	$upi->updateCustomerVPA($data['payer_vpa'], $order_info['customer_id']);
				$data['action'] = $this->url->link('payment/upi/upiPaymentProcess', '', 'SSL');

			} else {
				
				$json_response = $this->upiQueryTransaction($data['upi_txn_id']);

				// add in Order History (Payment failed from UPI but mode changed to bank transfer)
				$data['upi_payment_status'] = 'failed';
		    	$notes = "";
				$notes .= !empty($data['upi_txn_id']) ? (" Merchant Txn ID: " . $data['upi_txn_id'] . ".") : ""; 
				$notes .= !empty($data['amount']) ? (" Amount: " . $data['amount'] . ".") : "";
				$notes .= !empty($data['upi_payment_status']) ? ("UPI Transaction Status: " . strtoupper($data['upi_payment_status']) . ".") : "";
				$notes .= "Payment mode changed to Bank Transfer.";
		        
		        $this->load->model('checkout/order');
		        $input = array();
		        $input['order_id'] = $data['order_id'];	
		        $input['order_status_id'] = '1';
		        $input['comment'] = "UPI Payment.";
		        $input['notes'] = $notes;
		        $input['notify_email'] = 1;
				$this->model_checkout_order->addOrderHistory($input);

				//insert failure into order payment
				$value = array();

				$value['upi_payment_status'] = $data['upi_payment_status'];
				$value['order_no'] 		= $order_info['order_no'];
				$value['order_id'] 		= $data['order_id'];

				$responses = array();
				$responses['initiate'] = $data['initiate_response'];
				$responses['query'] = $json_response;

				$value['json_response'] = serialize($responses);
				$value['payment_mode'] = 'UPI';
				$value['customer_id'] = $order_info['customer_id'];
				$value['successfull'] = 0;

				if(isset($response_payload->trnDateTime)) {
					$var = $response_payload->trnDateTime;
					$date = str_replace('/', '-', $var);
					$value['txnDateTime'] = date('Y-m-d H:i:s', strtotime($date));
				} else {
					$value['txnDateTime'] = date('Y-m-d H:i:s');
				}

				$value['payment_link'] = 'Customer Direct Payment';
				
				$payment_gateway = new Upi($this);
		        $payment_gateway->insertDirectCustomerPaymentDetailsIntoDb($data, $value);

		        $this->session->data['payment_method']['code'] = 'bank_transfer';
		        $this->session->data['upi_payment_status'] = $value['upi_payment_status'];
		        $data['action'] = $this->url->link('checkout/success', '', 'SSL');
       		}
       		
			if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/upi_form.tpl')) {
			    return $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/upi_form.tpl', $data));
			} else {
			    return $this->response->setOutput($this->load->view('default/template/payment/upi_form.tpl', $data));
			}
    	}
    }

	public function upiPaymentProcess(){

 		$data = array();

 		$data['upi_txn_id'] = $this->request->post['upi_txn_id'];
 		$data['order_id'] = $this->request->post['order_id'];
 		$data['amount']   = $this->request->post['amount'];
		$data['total']    = $this->request->post['total'];
		$data['order_no'] = $this->request->post['order_no'];
		$data['currency_code'] = $this->request->post['currency_code'];
		$data['currency_value'] = $this->request->post['currency_value'];
	    $data['payer_vpa'] = $this->request->post['payer_vpa'];
	    $data['initiate_response'] = $this->request->post['initiate_response'];

	    $upiLanguage = array();
		$this->load->autoLoadLanguage('payment/upi', $upiLanguage);
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_message'] = sprintf($this->language->get('text_pre_pay'),
											$this->currency->format($data['total'], $data['currency_code'], $data['currency_value'], true)
											);
		$this->session->data['header_block'] = 1;
		$data['header'] = $this->load->controller('checkout/header');

		$action = $this->url->link('payment/upi/upiPostPaymentProcess', '', 'SSL');

		$data['action'] = $action;

		$action_query = $this->url->link('payment/upi/upiQueryTransaction', '', 'SSL');

		$data['action_query'] = $action_query;

		if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/upi_payment_process.tpl')) {
		    return $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/upi_payment_process.tpl', $data));
		} else {
		    return $this->response->setOutput($this->load->view('default/template/payment/upi_payment_process.tpl', $data));
		}
    }

    public function upiPostPaymentProcess() {

    	/**
    	 * Condition needed to halt the upi flow if already processed
    	 * This scenario mmay occur if querying status of ongoing upi payment is slow
    	 */
    	if ( !empty( $this->session->data['upi_flow_already_processed'] )) {

    		exit();

    	} else {

    		$this->session->data['upi_flow_already_processed'] = "1";
    	}

    	if(isset($this->request->post['upi_txn_id'])){

    		$data = array();
    		$data['upi_txn_id'] = $this->request->post['upi_txn_id'];
    		$data['amount'] = $this->request->post['amount'];
    		$data['payer_vpa'] = $this->request->post['payer_vpa'];
    		$data['initiate_response'] = json_decode(str_replace('\'', '"', $this->request->post['initiate_response']));

    		$json_response = $this->upiQueryTransaction($data['upi_txn_id']);
    		if(isset($json_response->responseStatus)) {
				$data['upi_payment_status'] = strtolower($json_response->responseStatus);
			} else {
				// redirect to failure
				if(isset($json_response->status) && $json_response->status == '01'){

					$this->response->redirect($this->url->link('checkout/failure', '', 'SSL','payment'));
					exit();
				}
			}

    		// Getting Order Details								                    
			
			$selector = array('order' => array());

            // $order_info = OrderInfo::getOrderInfo($this->db, 
            //                                       $this->session->data['order_id'], 
            //                                       '',
            //                                       $selector)['order'];
			$order_info = OrderPayment::getOrderInfoIfNetPayableAmountApplicable($this->db, $this->session->data['order_id'], $selector)['order'];
	        if (!empty($this->session->data['net_order_totals'])) {
	            $order_info['total'] = $this->session->data['net_order_totals'];
	        }
	        $order_info['total'] = number_format(((float)$order_info['total']), 2, '.', '');
            // add in Order History
	        if(!empty($data['upi_txn_id']) && strtolower($data['upi_payment_status']) == 'completed') {
	        	$data['upi_txn_id'] = "UPI/".strtoupper($data['upi_txn_id']);
	        	$this->request->post['upi_txn_id'] = $data['upi_txn_id'];
	        }

	    	$notes = "";
			$notes .= !empty($data['upi_txn_id']) ? (" Merchant Txn ID: " . $data['upi_txn_id'] . ".") : ""; 
			$notes .= !empty($data['amount']) ? (" Amount: " . $data['amount'] . ".") : "";
			$notes .= !empty($data['upi_payment_status']) ? (" Transaction Status: " . $data['upi_payment_status'] . ".") : "";
	        
	        $this->load->model('checkout/order');
	        $input = array();
	        $input['order_id'] = $this->session->data['order_id'];	
	        $input['order_status_id'] = '1';
	        $input['comment'] = "UPI Payment";
	        $input['notes'] = $notes;
	        $input['notify_email'] = 1;
			$this->model_checkout_order->addOrderHistory($input);

	     	// insert into payment table after splitting order so that advance can bifurcate according to suborders

     	    $value = $this->request->post;	
			$value['order_no'] 		= $order_info['order_no'];
			$value['order_id'] 		= $this->session->data['order_id'];
			$value['upi_payment_status'] = $data['upi_payment_status'];

			$responses = array();
			$responses['initiate'] = $data['initiate_response'];
			$responses['query'] = $json_response;

			$value['json_response'] = serialize($responses);
			$value['payment_mode'] = 'UPI';
			$value['customer_id'] = $order_info['customer_id'];
			$value['successfull'] = 0;

			if($data['upi_payment_status'] == 'completed'){
				$value['successfull'] = 1;

				//mail to operation and account team for successful payment
			    $upi = new Upi($this);
				$settings = $upi->getUPISettings();

                foreach ($settings as $upi_settings) {
                    if($upi_settings['key'] == 'upi_module'){
                        if(strtolower($upi_settings['value']) == 'staging'){
                            $upi_module = 'scb';
                        } else if (strtolower($upi_settings['value']) == 'production'){
                            $upi_module = 'scbl';
                        } else {
                            $upi_module = '';
                        }
                    }
                }

			} else {
				$this->session->data['payment_method']['code'] = 'bank_transfer';
			}

			$var = $json_response->requestTime;
			$date = str_replace('/', '-', $var);
			$value['txnDateTime'] = date('Y-m-d H:i:s', strtotime($date));
			
			if(!empty($json_response->txnAmount)) {
				$this->request->post['amount'] = $this->request->post['total'] = $json_response->txnAmount;
			}
			$value['payment_link'] = 'Customer Direct Payment';
			$payment_gateway = new Upi($this);
	        $payment_id = $payment_gateway->insertDirectCustomerPaymentDetailsIntoDb($this->request->post, $value);

            if($value['successfull'] && !empty($payment_id)) {

                $op_detail = $upi->getDataByOrderNO($order_info['order_no']);

	            $data['payment_id']         = $op_detail['payment_id'];
	            $data['order_id']           = $op_detail['order_id'];
	            $data['date_added']         = $op_detail['date_added'];
	            $data['payment_gateway']    = $op_detail['payment_gateway'];
	            $data['successfull']        = $op_detail['successfull'];
	            $data['reference']          = $op_detail['reference'];
	            $data['payment_link']       = $op_detail['payment_link'];

	            $upi->sendSuccessEmailToOperationAndAccounts($data);

                //To send SMS
                $order_no = OrderInfo::getOrderNo($this->db, $this->session->data['order_id']);
                OrderPayment::sendAdvanceSMS($this, $this->session->data['order_id'], $order_no);
            }

        	$this->session->data['upi_payment_status'] = $data['upi_payment_status'];

    		$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));

    	} else{

				$this->session->data['error'] = "UPI Response - ".$this->request->post['TxMsg'];
				$this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL','payment'));
    	}
    }	

    public function upiQueryTransaction($upi_txn_id = ''){

    	if(isset($this->request->post['upi_txn_id']) || $upi_txn_id != ''){

    		$data = array();
    		if(isset($this->request->post['upi_txn_id'])){
    			$data = [
					   'txnID' => $this->request->post['upi_txn_id']
					  ];
			} else {
				$data = [
					   'txnID' => $upi_txn_id
					  ];
			}
			
			$upi = new Upi($this);
			$response_payload = $upi->transactionsEnquiry($data);
			
			if(isset($this->request->post['ajax']) && $this->request->post['ajax'] == '1'){
				if(isset($response_payload->responseStatus)) {
					$responseStatus = $response_payload->responseStatus;
				} else {
					$responseStatus = '';
				}
	            $data = [
	                 'responseStatus' => strtolower($responseStatus),
	                ];
				echo json_encode($data);
				return 0;
			}
			return $response_payload;
    	}
		return 0;
    }

	private function generateTransactionID($order_no){
		// generate unique txn id for txn
		return $order_no;
	}

	public function NotifyTxnStatus() {
		/***
		From Request:
			merchant_txn_id
			txn_status
		***/
		/*****Sample Request****/
		// $sample_req['merchant_txn_id'] = '20171202177d';
		// $sample_req['txn_status'] = 'rejected';
		// $this->request->post['notify_request'] = json_encode($sample_req);
		/*************/

		$data = $_REQUEST;
		$final_response = array();
		$final_response['status'] = '0';
		$final_response['notify_date_time'] = date('Y-m-d H:i:s');
		$upi = new UPI($this);

		if(!empty($this->request->post['notify_request'])) {
			$notify_request = $this->request->post['notify_request'];
			$notify = json_decode($notify_request);
			//$notify = $upi->NotifyTxnStatus($notify_request);
			$final_response['merchant_txn_id'] = $notify->merchant_txn_id;
		}

		if(empty($this->session->data['error']) && !empty($notify)) {
			$this->load->model('payment/upi');
	        $result = $this->model_payment_upi->getOrderPaymentDetailsfromMerchTxnID($notify->merchant_txn_id);

	        if($result->num_rows){
	        	$order_payment = $result->row;
	        	if(!empty($order_payment['txn_status']) && strtolower($order_payment['txn_status']) == 'pending') {
	        		$data = array();
		        	$data = [
		        				'txnID' => $notify->merchant_txn_id
		        			];
		        	$upi = new Upi($this);
					$response_payload = $upi->transactionsEnquiry($data);
		        	
		        	$responses = array();
		        	$raw_json_format = unserialize($order_payment['json_format']);
			        $responses['initiate'] = $raw_json_format['initiate'];
			        $responses['query'] = $response_payload;
			        $json_format = serialize($responses);
			        
			        $successfull = 0;
					if(strtolower($notify->txn_status) == 'completed') {		
						$successfull = 1;
                        $success_txnID = "UPI/".strtoupper($notify->merchant_txn_id);
			        }

			        if(isset($response_payload->requestTime)) {
						$var = $response_payload->requestTime;
						$date = str_replace('/', '-', $var);
						$txnDateTime = date('Y-m-d H:i:s', strtotime($date));
					} else if(isset($responses['initiate']->trnDateTime)) {
						$var = $responses['initiate']->trnDateTime;
						$date = str_replace('/', '-', $var);
						$txnDateTime = date('Y-m-d H:i:s', strtotime($date));
					} else {
						$txnDateTime = date('Y-m-d H:i:s');
					}

		            $update_data['TxStatus'] = $response_payload->responseStatus;
	                $update_data['TxMsg'] = "UPI Payment";
	                $update_data['json_format'] = $json_format;
	                $update_data['successfull'] = $successfull;
	                $update_data['OrderNO'] = $notify->merchant_txn_id;
	                $update_data['TxId'] = !empty($success_txnID)?$success_txnID:$notify->merchant_txn_id;
	                $update_data['payment_gateway'] = "UPI";
	                $update_data['payment_id'] = $order_payment['payment_id'];
	                $update_data['order_id'] = $order_payment['order_id'];
	                $update_data['amount'] = $responses['initiate']->amount;
	                $update_data['currency'] = "INR";
	                $update_data['paymentMode'] = 'UPI';
	                $update_data['txnDateTime'] = $txnDateTime;

	                $upi->updatePaymentDetailsIntoDb($this, $update_data);

	                // if(strtolower($response_payload->responseStatus) == 'completed') {
	                //     $comment = "UPI Payment";
	                //     $sql = "UPDATE
	                //                 ".DB_PREFIX."order_history 
	                //                 SET comment = '".$comment."'
	                //                 WHERE order_id = '".$order_payment['order_id']."'";              
	                //     $update_result = $this->db->query($sql);
	                // }
	        	}
	        	$final_response['status'] = '1';
	        	
	        }else {
	        	$final_response['status'] = '0';
	        }
	        
		} 
		//else {
		// 	$data['server'] = $_SERVER;
		// 	$data['request'] = $_REQUEST;
		// 	$data['post'] = $_POST;
		// 	$upi->sendInvalidAttemptEmailInPaymentGateway($data, 'upi');
		// }
		echo json_encode($final_response);
		exit();
	}
} 


