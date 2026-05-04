<?php
class ControllerInformationContact extends Controller {
	private $error = array();

	public function index() {

        $this->load->model('tool/image');

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		
		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}

		$header_language = array();
		$footer_language = array();
		$login_language = array();
       
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
		$data['direction'] = $header_language['direction'];
		
         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

        $this->load->language('information/contact');
 
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		$data['title'] = $this->document->getTitle();
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		

		 $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

		$data['base'] = $server;
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$mail = new PHPMailer();
			$mail->isSMTP();
			$mail->Host = $this->config->get('config_mail_smtp_hostname');
			$mail->Port = $this->config->get('config_mail_smtp_port');	
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->Username = $this->config->get('config_mail_smtp_username');
			$mail->Password = $this->config->get('config_mail_smtp_password');
			$mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
			$mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesalebox');
			$mail->Subject = html_entity_decode(sprintf($this->language->get('email_subject'), 
			                                            $this->request->post['name']), 
			                                            ENT_QUOTES, 
			                                            'UTF-8');
			$mail->msgHTML(sprintf($this->language->get('email_message'), 
			                       $this->request->post['name'], 
			                       $this->request->post['telephone'], 
			                       $this->request->post['email'],
			                       $this->request->post['company'], 
			                       $this->request->post['enquiry']), 
			                       ENT_QUOTES, 
			                       'UTF-8');
			$mail->send();
			$this->response->redirect($this->url->link('information/contact/success'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);

		$data['heading_title'] = $this->language->get('heading_title');
		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_location'] = $this->language->get('text_location');
		$data['text_store'] = $this->language->get('text_store');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_address'] = $this->language->get('text_address');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_jaipur_telephone'] = $this->language->get('text_jaipur_telephone');
		$data['text_Delhi_telephone'] = $this->language->get('text_Delhi_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_open'] = $this->language->get('text_open');
		$data['text_comment'] = $this->language->get('text_comment');

		$data['entry_name'] = $this->language->get('entry_name');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_enquiry'] = $this->language->get('entry_enquiry');

		$data['button_map'] = $this->language->get('button_map');

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
        
        if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['company'])) {
			$data['error_company'] = $this->error['company'];
		} else {
			$data['error_company'] = '';
		}

		if (isset($this->error['enquiry'])) {
			$data['error_enquiry'] = $this->error['enquiry'];
		} else {
			$data['error_enquiry'] = '';
		}

		if (isset($this->error['captcha'])) {
			$data['error_captcha'] = $this->error['captcha'];
		} else {
			$data['error_captcha'] = '';
		}

		$data['button_submit'] = $this->language->get('button_submit');

		$data['action'] = $this->url->link('information/contact');

		$this->load->model('tool/image');

		if ($this->config->get('config_image')) {
			$data['image'] = $this->model_tool_image->resize($this->config->get('config_image'), $this->config->get('config_image_location_width'), $this->config->get('config_image_location_height'));
		} else {
			$data['image'] = false;
		}

		$data['store'] = $this->config->get('config_name');
		$data['address'] = nl2br($this->config->get('config_address'));
		$data['geocode'] = $this->config->get('config_geocode');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['fax'] = $this->config->get('config_fax');
		$data['open'] = nl2br($this->config->get('config_open'));
		$data['comment'] = $this->config->get('config_comment');

		$data['locations'] = array();

		$this->load->model('localisation/location');
                
                $show_on = 'contact';
                $data['store_locators'] = $this->model_localisation_location->getStoreLocator($show_on);
                
                foreach($data['store_locators'] as $location_info) {
			if ($location_info) {
				
				$data['locations'][] = array(
					'location_id' => $location_info['location_id'],
					'name'        => $location_info['name'],
					'location_type'        => $location_info['location_type'],
					'address'     => nl2br($location_info['address']),
					'city'     => $location_info['city'],
					'postcode'     => $location_info['postcode'],
					'state'     => $location_info['state'],
					'country'     => $location_info['country'], 
                                        'telephone'   => $location_info['telephone'],
                                        'geocode'     => $location_info['geocode'],
					'direction_url'         => $location_info['direction_url'],
					'image'       => '',
					'timing'        => nl2br($location_info['timing']),
                                        'sort_order'         => $location_info['sort_order'],
					'comment'     => $location_info['comment']
				);
			}
		}
                if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else {
			$data['name'] = $this->customer->getFirstName();
		}
        
        if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else {
			$data['telephone'] = $this->customer->getTelephone();
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = $this->customer->getEmail();
		}

		if (isset($this->request->post['company'])) {
			$data['company'] = $this->request->post['company'];
		} else {
			$data['company'] = '';
		}		

		if (isset($this->request->post['enquiry'])) {
			$data['enquiry'] = $this->request->post['enquiry'];
		} else {
			$data['enquiry'] = '';
		}

		if ($this->config->get('config_google_captcha_status')) {
			$this->document->addScript('https://www.google.com/recaptcha/api.js');

			$data['site_key'] = $this->config->get('config_google_captcha_public');
		} else {
			$data['site_key'] = '';
		}

      
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
	 if(CONFIG_IS_MOBILE == 1)
      {	
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
	  }	

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/contact.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/contact.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/information/contact.tpl', $data));
		}
	}

	public function success() {

         $this->load->model('tool/image');
         
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$header_language = array();
		$footer_language = array();
		$login_language = array();
       
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['lang']      = $header_language['code'];
		$data['direction'] = $header_language['direction'];
		
         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;

        $this->load->language('information/contact');
 
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		$data['title'] = $this->document->getTitle();
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		
		 $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));
				
		$this->load->language('information/contact');
		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_message'] = $this->language->get('text_success');

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/success.tpl', $data));
		}
	}

	protected function validate() {
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}
        
        if ((int)(is_numeric($this->request->post['telephone'])) == 0 || utf8_strlen($this->request->post['telephone']) !=  10) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['enquiry']) < 5) || (utf8_strlen($this->request->post['enquiry']) > 3000)) {
			$this->error['enquiry'] = $this->language->get('error_enquiry');
		}

		if ($this->config->get('config_google_captcha_status')) {
			$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->config->get('config_google_captcha_secret')) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->getIpAddress);

			$recaptcha = json_decode($recaptcha, true);

			if (!$recaptcha['success']) {
				$this->error['captcha'] = $this->language->get('error_captcha');
			}
		}

		return !$this->error;
	}
}
