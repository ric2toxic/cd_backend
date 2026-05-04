<?php
class ControllerCheckoutLogin extends Controller {
	private $error = array();
	public function index() {

		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			die;
		}

		$this->load->language('checkout/checkout');
		$this->load->model('account/customer');

		if(isset($this->request->get['is_dropshipper'])){
			$data['is_dropshipper'] = $this->request->get['is_dropshipper'];
			$this->is_dropshipper = 2;
		}else {
			$data['is_dropshipper'] = 0;
		}

		$data['text_checkout_account'] = $this->language->get('text_checkout_account');
		$data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
		$data['text_new_customer'] = $this->language->get('text_new_customer');
		$data['text_returning_customer'] = $this->language->get('text_returning_customer');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_guest'] = $this->language->get('text_guest');
		$data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
		$data['text_register_account'] = $this->language->get('text_register_account');
		$data['text_forgotten'] = $this->language->get('text_forgotten');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_mobile_or'] = $this->language->get('entry_mobile_or');
		$data['entry_password'] = $this->language->get('entry_password');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_login'] = $this->language->get('button_login');

		$data['checkout_guest'] = ($this->config->get('config_checkout_guest') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = 'register';
		}

		$data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');



		/*********************************Checkout Register******************************************************/
		$data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
		$data['text_your_details'] = $this->language->get('text_your_details');
		$data['text_your_address'] = $this->language->get('text_your_address');
		$data['text_your_password'] = $this->language->get('text_your_password');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_email_address'] = $this->language->get('entry_email_address');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_confirm'] = $this->language->get('entry_confirm');
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['show_pass'] = $this->language->get('show_pass');
		$data['entry_referred'] = $this->language->get('entry_referred');
		$this->load->language('common/footer');
		$data['text_mob_pop'] = $this->language->get('text_pop_mob_for_whatsapp');
		$data['entry_whatsapp_telephone'] = $this->language->get('entry_whatsapp_telephone');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_upload'] = $this->language->get('button_upload');

		/* *
                 * Check For Country Drop down list(START)
                 * 05-03-2016(Ravindra Singh)
                 * */
		if(isset($_COOKIE['country']) && $_COOKIE['country'] == 'IN'){
			$show_country = 0;
		}else{
			$show_country = 1;
		}
		$data['show_country'] = $show_country;

		/* *
		 * Check For Country Drop down list(END)
		 * 05-03-2016(Ravindra Singh)
		 * */

		if (isset($this->session->data['shipping_address']['postcode'])) {
			$data['postcode'] = $this->session->data['shipping_address']['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		$data['shipping_required'] = $this->cart->hasShipping();
		/********************************************************************************************************/

		/***************************Checkout Forgot***************************************************************/


		if(isset($this->request->post['form_button']) && ($this->request->post['form_button']=='forget_button')){
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->forgotValidation()) {
				$this->load->language('mail/forgotten');
				$this->load->language('account/sms_templates');

				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
				//$password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);
				$password = substr(mt_rand(), 0, 6);

				$this->model_account_customer->editPassword($customer_info['customer_id'], $password);

				$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

				$username = $this->model_account_customer->getUserNameByMobileNumber($this->request->post['email']);
				$message = $this->language->get('forgot_pass_msg');
				$msg = sprintf($message,$username,$password );
				$this->send_mail_or_sms($this->request->post['email'],$msg,$subject,$password);
			}
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['heading_text_forget_password'] = $this->language->get('heading_text_forget_password');

		$data['text_your_email'] = $this->language->get('text_your_email');
		$data['text_email'] = $this->language->get('text_email');

		$data['entry_email'] = $this->language->get('entry_email');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');

		/********************************************************************************************************/

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['order_summary_list'] = $this->load->controller('checkout/order_summary');
		$data['order_summary'] = $this->language->get('order_summary');
		$data['price_details'] = $this->language->get('price_details');
		//$data['forgot_action'] = $this->url->link('checkout/login#forgot_password_show', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/login.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/login.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/checkout/login.tpl', $data));
		}
	}

	public function save() {
		$this->load->language('checkout/checkout');

		$json = array();

		if ($this->customer->isLogged()) { 
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {  
			$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
		}
		
		if (!$json) {
			$this->load->model('account/customer');
			
			// Check how many login attempts have been made.
			$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);
			
			if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
				$json['error']['warning'] = $this->language->get('error_attempts');
			}			

			
			if (!isset($json['error'])) {  
				if (!$this->customer->login($this->request->post['email'], $this->request->post['password'], false, true)) {  
					$json['error']['warning'] = $this->language->get('error_login');
					$this->model_account_customer->addLoginAttempt($this->request->post['email']);
				} else { 
					$this->model_account_customer->deleteLoginAttempts($this->request->post['email']); 
				}			
			}
		}

		if (!$json) { 
			unset($this->session->data['guest']);

			$this->load->model('account/address');

			if ($this->config->get('config_tax_customer') == 'payment') {
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}

			if ($this->config->get('config_tax_customer') == 'shipping') {
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}

			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function forgotValidation(){
		if (!isset($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('forgot_error_email');
		} else if(!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('forgot_error_email');
		}else if(empty($this->request->post['email'])){
			$this->error['warning'] = $this->language->get('forgot_error_email');
		}
		return !$this->error;
	}

	/*
	 * send mail or sms by email or mobile number by vikas
	 *
	 */
	public function send_mail_or_sms($email,$message,$subject,$password){
		$this->load->language('account/sms_templates');
		$this->load->language('account/forgotten');
		if(is_numeric($email)){
			$send_sms = new SMS($message,$email);
			$send_sms->sendMessage(1);
			$this->session->data['success'] = sprintf($this->language->get('text_mobile_success'),$email);
		}else{
			$message  = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
			$message .= $this->language->get('text_password') . "\n\n";
			$message .= $password;
			$mail = new PHPMailer();
		    $mail->isSMTP();
		    // $mail->SMTPDebug = 2;
		    // $mail->Debugoutput = 'html';
		    $mail->Host = $this->config->get('config_mail_smtp_hostname');
		    $mail->SMTPSecure = 'ssl';
		    $mail->Port = $this->config->get('config_mail_smtp_port');
		    $mail->SMTPAuth = true;
		    $mail->Username = $this->config->get('config_mail_smtp_username');
		    $mail->Password = $this->config->get('config_mail_smtp_password');
		    $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
		    $mail->addReplyTo($this->config->get('config_email'), 'WholesaleBox');

		    // $mail->addAddress($this->config->get('config_email'), 'WholesaleBox');
		    $mail->addAddress($email);
		    $mail->addBCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
		    $mail->Subject = $subject;
		    $mail->Body = $message;
		    $mail->send(1);
			$this->session->data['success'] = sprintf($this->language->get('text_success'),$email);
		}

	}

}
