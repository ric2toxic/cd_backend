<?php 
//set_include_path('./citrus/lib/'.PATH_SEPARATOR.'./lib/'.PATH_SEPARATOR.'./citrus/'.PATH_SEPARATOR.get_include_path());
//require_once ('CitrusPay.php');
//require_once ('Zend/Crypt/Hmac.php');
	class ControllerPaymentSafecheckout extends Controller 
	{
		/*function generateHmacKey($data, $apiKey=null){
			$hmackey = Zend_Crypt_Hmac::compute($apiKey, "sha1", $data);
			return $hmackey;
		}	*/
		
		public function index() 
		{
			//global $_SERVER;
			//echo "<pre>"; print_r($_REQUEST); exit;
			
			if(isset($this->session->data['site_url'])){
				if(!isset($_REQUEST['order_id']) && empty($_REQUEST['order_id'])){
					$this->response->redirect($this->session->data['site_url']);
				}
			}else{
				if(!isset($_REQUEST['order_id']) && empty($_REQUEST['order_id'])){
					$this->response->redirect('/');
				}
			}
			
			
			$rs = $_REQUEST;
			$this->session->data['site_url'] = $rs['site_url'];
			$this->session->data['order_id'] = $rs['order_id'];
			$data['first_name'] = $rs['firstName'];
			$data['last_name'] = $rs['lastName'];
			$data['email'] = $rs['email'];
			if(isset($rs['addressStreet1'])){
			$data['address1'] = $rs['addressStreet1'];
			}else{
				$data['address1'] = '';
			}
			if(isset($rs['addressStreet2'])){
			$data['address2'] = $rs['addressStreet2'];
			}else{
				$data['address2'] = '';
			}
			$data['city'] = $rs['addressCity'];
			$data['state'] = $rs['addressState'];
			$data['country'] = $rs['addressCountry'];
			$data['pin_code'] = $rs['addressZip'];
			$data['mobile'] = $rs['phoneNumber'];
			$data['order_amount'] = $rs['orderAmount'];
			$data['currency'] = $rs['currency'];
			$txnID = uniqid();
			$amount = $rs['orderAmount'];

			$secret_key = "0b38019b08756a879d18c1819d112b036ddd0e74"; 
    
			
			$access_key = "B5XEE12BI2G85CIXBX3Q"; 
			$data1 = "merchantAccessKey=" . $access_key
                . "&transactionId="  . $txnID 
                . "&amount="         . $amount;
			$signature = hash_hmac('sha1', $data1, $secret_key);
			//echo $signature; exit;
			
			$data['txnID'] = $txnID;//$rs['orderAmount'];
			$data['securitySignature'] = $signature;
			$data['returnURL'] = HTTPS_SERVER.'index.php?route=payment/safecheckout/return_url';//$rs['returnURL'];
			$data['notifyUrl'] = HTTPS_SERVER.'index.php?route=payment/safecheckout/notify_url';//$rs['notifyUrl'];
			
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/safecheckout.tpl')) { 
				return $this->load->view($this->config->get('config_template') . '/template/payment/safecheckout.tpl', $data);
			} else {
				return $this->load->view('default/template/payment/safecheckout.tpl', $data);
			}
			
			
			//return $this->load->view('default/template/payment/safecheckout.tpl',$data);
			//return $this->load->view($this->config->get('config_template') . '/template/payment/safecheckout.tpl',$data);	
			
		}
		
		public function return_url() 
		{
			//echo "<pre>"; print_r($_REQUEST); exit;
			$order_id = $_REQUEST['order_id'];
			$comment = $_POST['TxMsg'].',TxId='.$_REQUEST['TxId'].',paymentMode='.$_REQUEST['paymentMode'].',TxGateway='.$_REQUEST['TxGateway'];
			$payment_info = serialize($_REQUEST);	
			$this->load->model('checkout/order');
			$data['citrus_module'] = $this->config->get('citrus_module');
			$data['citrus_secret_key'] = $this->config->get('citrus_secret_key');
			//CitrusPay::setApiKey($data['citrus_secret_key'],$data['citrus_module']);
			
			if (strtoupper($_POST['TxStatus']) == 'SUCCESS')
			{
				//resp signature validation
				$str=$_POST['TxId'].$_POST['TxStatus'].$_POST['amount'].$_POST['pgTxnNo'].$_POST['issuerRefNo'].$_POST['authIdCode'].$_POST['firstName'].$_POST['lastName'].$_POST['pgRespCode'].$_POST['addressZip'];
		  		$respSig=$_POST['signature'];
		  		//if($this->generateHmacKey($str,CitrusPay::getApiKey()) == $respSig)
				//{ 
					//$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('citrus_order_status_id'),$_POST['TxMsg'],false);
					$this->model_checkout_order->addOrderHistoryWithPaymentinfo($order_id, $this->config->get('citrus_order_status_id'),$comment,$payment_info,false);
					//$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
					//$this->response->redirect($this->session->data['site_url']);
				/*}
				else
				{
					$this->session->data['error'] = "Invalid or forged transactiond..";		//forged 
					$this->response->redirect($this->session->data['site_url']);
					//$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
				}*/
			}
			else
			{
				$this->session->data['error'] = "Citrus Response - ".$_POST['TxMsg'];
				//$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			}
		}
		
		public function notify_url() 
		{
			
			//echo "<pre>"; print_r($_REQUEST); exit;
			//$this->session->data['order_id'] = "";
			$order_id = $this->session->data['order_id'];
			$comment = $_POST['TxMsg'].',TxId='.$_REQUEST['TxId'].',paymentMode='.$_REQUEST['paymentMode'].',TxGateway='.$_REQUEST['TxGateway'];
			$payment_info = serialize($_REQUEST);	
			$this->load->model('checkout/order');
			$data['citrus_module'] = $this->config->get('citrus_module');
			$data['citrus_secret_key'] = $this->config->get('citrus_secret_key');
			//CitrusPay::setApiKey($data['citrus_secret_key'],$data['citrus_module']);
			
			if (strtoupper($_POST['TxStatus']) == 'SUCCESS')
			{
				//resp signature validation
				$str=$_POST['TxId'].$_POST['TxStatus'].$_POST['amount'].$_POST['pgTxnNo'].$_POST['issuerRefNo'].$_POST['authIdCode'].$_POST['firstName'].$_POST['lastName'].$_POST['pgRespCode'].$_POST['addressZip'];
		  		$respSig=$_POST['signature'];
				/*if($this->generateHmacKey($str,CitrusPay::getApiKey()) == $respSig)
				{ */
					$this->model_checkout_order->addOrderHistoryWithPaymentinfo($this->session->data['order_id'], $this->config->get('citrus_order_status_id'),$comment,$payment_info,false);
					//$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
					$this->response->redirect($this->session->data['site_url']);
				/*}
				else
				{
					$this->session->data['error'] = "Invalid or forged transactiond..";		//forged 
					$this->response->redirect($this->session->data['site_url']);
					//$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
				}*/
			}
			else
			{
				$this->session->data['error'] = "Citrus Response - ".$_POST['TxMsg'];
				$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			}
		}
		
		public function callback() 
		{
			$this->load->model('checkout/order');
			$data['citrus_module'] = $this->config->get('citrus_module');
			$data['citrus_secret_key'] = $this->config->get('citrus_secret_key');
			CitrusPay::setApiKey($data['citrus_secret_key'],$data['citrus_module']);
			
			if (strtoupper($_POST['TxStatus']) == 'SUCCESS')
			{
				//resp signature validation
				$str=$_POST['TxId'].$_POST['TxStatus'].$_POST['amount'].$_POST['pgTxnNo'].$_POST['issuerRefNo'].$_POST['authIdCode'].$_POST['firstName'].$_POST['lastName'].$_POST['pgRespCode'].$_POST['addressZip'];
		  		$respSig=$_POST['signature'];
				if($this->generateHmacKey($str,CitrusPay::getApiKey()) == $respSig)
				{ 
					$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('citrus_order_status_id'),$_POST['TxMsg'],false);
					$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
				}
				else
				{
					$this->session->data['error'] = "Invalid or forged transactiond..";		//forged 
					$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
				}
			}
			else
			{
				$this->session->data['error'] = "Citrus Response - ".$_POST['TxMsg'];
				$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			}
		}
		
	}
?>
