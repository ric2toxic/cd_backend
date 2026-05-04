<?php 
class ControllerAccountRegisterSeller extends Controller {
	public  $data = array();
	public function __construct($registry) {
		parent::__construct($registry);

		$this->language->load('account/register');
		$this->load->model('account/customer');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->document->addScript('catalog/view/javascript/multimerch/account-register-seller.js');
	}

	public function index() {
		if ($this->customer->isLogged()) {
	  		$this->response->redirect($this->url->link('seller/account-profile', '', 'SSL'));
    	}
        $this->data['seller_mobile'] = '';
        $this->data['seller_email'] = '';
		if(isset($_POST['name'])){
			 $this->data['seller_name'] = $_POST['name'];

		}
        
        /*if(isset($_POST['mobile'])) {
            if (strpos($_POST['mobile'], '@') === false) {
                $this->data['seller_mobile'] = $_POST['mobile'];
            } else {
                $this->data['seller_email'] = $_POST['mobile'];

            }
        }*/
        
        if(isset($_POST['email'])) {          
          
            $this->data['seller_email'] = $_POST['email'];
        }


		if ($this->config->get('msconf_seller_terms_page')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

			if ($information_info) {
				$this->data['seller_terms'] = sprintf($this->language->get('ms_account_sellerinfo_terms_note'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('msconf_seller_terms_page'), 'SSL'), $information_info['title'], $information_info['title']);
			}
		}
		$this->data['header_seller'] = $this->load->controller('common/seller_header');
		$this->data['footer_seller'] = $this->load->controller('common/seller_footer');
		$this->document->setTitle($this->language->get('ms_account_register_seller'));
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
			),
			array(
				'text' => $this->language->get('ms_account_register_seller'),
				'href' => $this->url->link('account/register-seller', '', 'SSL')
			)
		));
		$this->data['text_account_already'] = sprintf($this->language->get('text_account_already'),  $this->url->link('account/login', '', 'SSL'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account/register-seller');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxsavesellerinfo() {

		$data = $this->request->post;
		$json = array();

		$arr_name = explode(" ", $data['seller']['name']);
		$data['seller']['firstname'] = $arr_name[0];
		if(isset($arr_name[1])) {
			$data['seller']['lastname'] = $arr_name[1];
		}else{
			$data['seller']['lastname'] = '';
		}

		if ((utf8_strlen($data['seller']['name']) < 1) || (utf8_strlen($data['seller']['name']) > 32)) {
      		$json['errors']['seller[name]'] = $this->language->get('error_name');
    	}
    	
		/*
    	if ((utf8_strlen($data['seller']['lastname']) < 1) || (utf8_strlen($data['seller']['lastname']) > 32)) {
      		$json['errors']['seller[lastname]'] = $this->language->get('error_lastname');
    	}
		*/
		//if ((utf8_strlen($data['seller']['telephone']) < 10) || (utf8_strlen($data['seller']['telephone']) > 10)) {
		if (!preg_match('/^[0-9]*$/', $data['seller']['reg_telephone']) || (utf8_strlen($data['seller']['reg_telephone']) < 10) || (utf8_strlen($data['seller']['reg_telephone']) > 10)) {
			$json['errors']['seller[reg_telephone]'] = $this->language->get('error_telephone'); 
		}

		//if (!empty($data['seller']['telephone']) && $this->model_account_customer->getTotalCustomersByTelephone($data['seller']['telephone'])) {
		/*if (!empty($data['seller']['reg_telephone']) && $this->model_account_customer->getTotalCustomersByTelephone($data['seller']['reg_telephone'])) {
			$json['errors']['seller[reg_telephone]'] = $this->language->get('error_exists_phone');
		}*/
		
		if (!empty($data['seller']['reg_telephone']) && $this->MsLoader->MsSeller->getTotalSellersByTelephone($data['seller']['reg_telephone'])) {
			$json['errors']['seller[reg_telephone]'] = $this->language->get('ms_error_sellerinfo_exists_phone');
		}
		
		if ((utf8_strlen($data['seller']['reg_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['seller']['reg_email'])) {
      		$json['errors']['seller[reg_email]'] = $this->language->get('error_email');
    	} else {
			/*if ($this->model_account_customer->getTotalCustomersByEmail($data['seller']['reg_email'])) {
				$json['errors']['seller[reg_email]'] = $this->language->get('error_exists');
			}*/
			
			if ($this->MsLoader->MsSeller->getTotalSellersByEmail($data['seller']['reg_email'])) {
				$json['errors']['seller[reg_email]'] = $this->language->get('ms_error_sellerinfo_error_email');
			}
		}

		if ((utf8_strlen($data['seller']['password']) < 4) || (utf8_strlen($data['seller']['password']) > 20)) {
			$json['errors']['seller[password]'] = $this->language->get('error_password');
		}

		if ($data['seller']['password_confirm'] != $data['seller']['password']) {
			$json['errors']['seller[password_confirm]'] = $this->language->get('error_confirm');
		}
		/*
		if (empty($data['seller']['nickname'])) {
			$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_empty');
		} else if (mb_strlen($data['seller']['nickname']) < 4 || mb_strlen($data['seller']['nickname']) > 128 ) {
			$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_length');
		} else if (($this->MsLoader->MsSeller->nicknameTaken($data['seller']['nickname'])) ) {
			$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_taken');
		} else {
			switch($this->config->get('msconf_nickname_rules')) {
				case 1:
					// extended latin
					if(!preg_match("/^[a-zA-Z0-9_\-\s\x{00C0}-\x{017F}]+$/u", $data['seller']['nickname'])) {
						$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_latin');
					}
					break;

				case 2:
					// utf8
					if(!preg_match("/((?:[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})./x", $data['seller']['nickname'])) {
						$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_utf8');
					}
					break;

				case 0:
				default:
					// alnum
					if(!preg_match("/^[a-zA-Z0-9_\-\s]+$/", $data['seller']['nickname'])) {
						$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_alphanumeric');
					}
					break;
			}
		}
		*/

		if ($this->config->get('msconf_seller_terms_page')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

			if ($information_info && !isset($data['seller']['terms'])) {
				$json['errors']['seller[terms]'] = htmlspecialchars_decode(sprintf($this->language->get('ms_error_sellerinfo_terms'), $information_info['title']));
			}
		}

		if (empty($json['errors'])) {
			
			// Check if seller has buyer account
			$checkCustmrExsist = $this->model_account_customer->getCustomerByEmail($data['seller']['reg_email']);
			if(is_array($checkCustmrExsist) && !empty($checkCustmrExsist)){
				// update group id/telephone number/password  for seller
				$this->model_account_customer->updateCustomerToSeller($checkCustmrExsist['customer_id'], $data['seller']);
			}
			else {
			
				// Create buyer account
				//$data['seller']['telephone'] = '';
				$data['seller']['is_dropshipper'] = 0;
				$data['seller']['nickname'] = '';
				$data['seller']['company'] = '';
				$data['seller']['address_1'] = '';
				$data['seller']['address_2'] = '';
				$data['seller']['city'] = '';
				$data['seller']['postcode'] = '';
				$data['seller']['country_id'] = 0;
				$data['seller']['zone_id'] = 0;

				// according to mahaveer  (adding by vikas, 2017)
				$data['seller']['mobile_country_code'] = '';
                $data['seller']['company'] = '';
                $data['seller']['customer_type_id'] = '';
                                
				$this->model_account_customer->addCustomer($data['seller']);
			}
			
			// Clear any previous login attempts for unregistered accounts.
			//$this->model_account_customer->deleteLoginAttempts($data['seller']['email']);
			//$this->customer->login($data['seller']['email'], $data['seller']['password']);
			$this->model_account_customer->deleteLoginAttempts($data['seller']['reg_email']);
			$this->customer->login($data['seller']['reg_email'], $data['seller']['password']);
			unset($this->session->data['guest']);

             //Add access token
            $access_token = $this->model_account_customer->addCustomerToken($this->customer->getId());

			// Register seller
			//$data['seller']['status'] = MsSeller::STATUS_INCOMPLETE; // commented by vikas, 2017
			$data['seller']['status'] = MsSeller::STATUS_INACTIVE;
			$data['seller']['approved'] = 0;

			$data['seller']['seller_id'] = $this->customer->getId();
			$data['seller']['company'] = $data['seller']['name'];
			$this->MsLoader->MsSeller->createSeller($data['seller']);

			$this->SellerAccountEmail($data['seller']['company'], $data['seller']['reg_email']);
			
			//$json['redirect'] = $this->url->link('seller/account-profile');
			$json['redirect'] = $this->url->link('seller_panel/profile');
		}
		//echo "<pre>"; print_r($json); exit;
		header('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function SellerAccountEmail($company, $email) {

		$this->load->language('mail/seller');

		$data['email'] = $email;
		$data['name'] = $company;

		$html = '';
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/seller_account_email.tpl')) {
			$html = $this->load->view($this->config->get('config_template') . '/template/mail/seller_account_email.tpl', $data);
		}

		$mail = new  PHPMailer();

		$mail->isSMTP();
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');
		$mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
		$mail->addReplyTo(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

		$mail->addAddress($email, $company);
		$mail->addCC(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);

		$mail->Subject = $this->language->get('text_seller_signup_subject');

		$mail->msgHTML($html);
		$mail->isHTML(true);
		$mail->send();
    }
}
?>
