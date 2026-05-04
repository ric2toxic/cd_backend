<?php
class ControllerAccountForgotten extends Controller {
	private $error = array();

	public function index() {
		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->load->language('account/forgotten');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/customer');
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
		} else {
			$data['success'] = '';
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->language('mail/forgotten');
			$this->load->language('account/sms_templates');

			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
			//$password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);
			$password = substr(mt_rand(), 0, 6);

			$this->model_account_customer->editPassword($customer_info['customer_id'], $password);

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

//			$message  = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
//			$message .= $this->language->get('text_password') . "\n\n";
//			$message .= $password;


			$username = $this->model_account_customer->getUserNameByMobileNumber($this->request->post['email']);
			$message = $this->language->get('forgot_pass_msg');
			$msg = sprintf($message,$username,$password );
			$this->send_mail_or_sms($this->request->post['email'],$msg,$subject,$password);

			$this->response->redirect($this->url->link('account/forgotten&popup=true', '', 'SSL'));

		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_my_orders'),
			'href' => $this->url->link('account/order', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_forgotten'),
			'href' => $this->url->link('account/forgotten', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_your_email'] = $this->language->get('text_your_email');
		$data['text_email'] = $this->language->get('text_email');

		$data['entry_email'] = $this->language->get('entry_email');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action'] = $this->url->link('account/forgotten', '', 'SSL');

		$data['back'] = $this->url->link('account/login', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/forgotten.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/forgotten.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/forgotten.tpl', $data));
		}
	}




	protected function validate() {
		if (!isset($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_email');
		} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_email');
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
			$send_sms->sendMessage();
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

			$mail->addAddress($email);
			$mail->addBCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
			$mail->Subject = $subject;
			$mail->Body = $message;
			$mail->send();
			$this->session->data['success'] = sprintf($this->language->get('text_success'),$email);
		}

	}


		/**
	 * ajax for forgot password form
	 */

	public function ajaxforgot() {
		if ($this->customer->isLogged()) {
		$this->response->redirect($this->url->link('account/account', '', 'SSL'));
	}

		$this->load->language('account/forgotten');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->language('mail/forgotten');
			require_once DIR_SYSTEM.'library/swiftmailer/lib/swift_required.php';

			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			$password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);

			$this->model_account_customer->editPassword($customer_info['customer_id'], $password);

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$message  = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
			$message .= $this->language->get('text_password') . "\n\n";
			$message .= $password;

			$transport = Swift_SmtpTransport::newInstance($this->config->get('config_mail_smtp_hostname'), $this->config->get('config_mail_smtp_port'));
			$transport->setUsername($this->config->get('config_mail_smtp_username'));
			$transport->setPassword(html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'));

			// Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);

			$mail = Swift_Message::newInstance();
			$mail->setSubject($subject);

			  // Set the From address with an associative array
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setFrom($this->config->get('config_email'));

			  // Set the To addresses with an associative array
			$mail->setTo($this->request->post['email']);
			$mail->setBody($mail);

			$numsent = $mailer->send($mail);


			// $mail = new Mail();
			// $mail->protocol = $this->config->get('config_mail_protocol');
			// $mail->parameter = $this->config->get('config_mail_parameter');
			// $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			// $mail->smtp_username = $this->config->get('config_mail_smtp_username');
			// $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			// $mail->smtp_port = $this->config->get('config_mail_smtp_port');
			// $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			// $mail->setTo($this->request->post['email']);
			// $mail->setFrom($this->config->get('config_email'));
			// $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			// $mail->setSubject($subject);
			// $mail->setText($message);
			// $mail->send();

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}


		if (isset($this->error['warning'])) {
			$data['errors'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action'] = $this->url->link('account/forgotten', '', 'SSL');

		echo json_encode($data);



	}

// this forgot function used for one page checkout by vikas (11-06-2016)
	public function forgotpassword(){
		$this->load->model('account/customer');
		$this->load->language('account/forgotten');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->language('mail/forgotten');
			$this->load->language('account/sms_templates');
			$password = substr(mt_rand(), 0, 6);

			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			$this->model_account_customer->editPassword($customer_info['customer_id'], $password);

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			
			$message = $this->language->get('forgot_pass_msg');
			$msg = sprintf($message,$customer_info['username'],$password );
			$this->send_mail_or_sms($this->request->post['email'],$msg,$subject,$password);
			$this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL'));
		}else{
			echo "1";
		}
	}
}
