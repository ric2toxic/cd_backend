<?php
class ControllerAccountAddress extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
		$this->load->language('account/address');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('account/address');

		$this->getList();
	}

	public function add() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->load->language('account/address');
		$this->load->model('account/address');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$arr_name = explode(" ", $this->request->post['name']);
			$this->request->post['firstname'] = $arr_name[0];
			if(isset($arr_name[1])) {
				$this->request->post['lastname'] = $arr_name[1];
			}else{
				$this->request->post['lastname'] = '';
			}

			if(!empty($this->request->post['address_telephone'])) 
                {
                   $this->request->post['address_telephone'] = explode(",", $this->request->post['address_telephone']);
                }

			$address_id = $this->model_account_address->addAddress($this->request->post);

            $this->session->data['success'] = $this->language->get('text_add');
                        
            $this->response->redirect($this->url->link('account/address', '', 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->load->language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$this->load->model('account/address');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$arr_name = explode(" ", $this->request->post['name']);
			$this->request->post['firstname'] = $arr_name[0];
			if(isset($arr_name[1])) {
				$this->request->post['lastname'] = $arr_name[1];
			}else{
				$this->request->post['lastname'] = '';
			}


            if(!empty($this->request->post['address_telephone'])) 
                {
                   $this->request->post['address_telephone'] = explode(",", $this->request->post['address_telephone']);
                }

			$this->model_account_address->editAddress($this->request->get['address_id'], $this->request->post);

            
			// Default Shipping Address
			if (isset($this->session->data['shipping_address']['address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address']['address_id'])) {
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->get['address_id']);

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
			}

			// Default Payment Address
			if (isset($this->session->data['payment_address']['address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address']['address_id'])) {
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->get['address_id']);

				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
			}

			$this->session->data['success'] = $this->language->get('text_edit');

			$data['name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();

			if(isset($this->request->post['popup']) && $this->request->post['popup'] == true){
				$this->response->redirect($this->url->link('checkout/one_page_checkout', '', 'SSL'));
			}else{
				$this->response->redirect($this->url->link('account/address', '', 'SSL'));
			}
		}

		$this->getForm();
	}

	public function delete() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->load->language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/address');

		if (isset($this->request->get['address_id']) && $this->validateDelete()) {
			$this->model_account_address->deleteAddress($this->request->get['address_id']);
			// when customer delete our default address then update new address id in customer table
			if ($this->customer->getAddressId() == $this->request->get['address_id']) {
				$this->model_account_address->updateAddressId($this->customer->getId());
			}

			// Default Shipping Address
			if (isset($this->session->data['shipping_address']['address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address']['address_id'])) {
				unset($this->session->data['shipping_address']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
			}

			// Default Payment Address
			if (isset($this->session->data['payment_address']['address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address']['address_id'])) {
				unset($this->session->data['payment_address']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
			}

			$this->session->data['success'] = $this->language->get('text_delete');

			if(isset($this->request->get['checkout_delete']) && $this->request->get['checkout_delete']==true){
				$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
				exit;
			}elseif(isset($this->request->get['one_page_checkout_delete']) && $this->request->get['one_page_checkout_delete']==true){
				//$url = '&error='.$this->language->get('error_default');
				$this->response->redirect($this->url->link('checkout/one_page_checkout', $url, 'SSL'));
				exit;
			}else {
				$this->response->redirect($this->url->link('account/address', '', 'SSL'));
				exit;
			}
		}
		if(isset($this->request->get['checkout_delete']) && $this->request->get['checkout_delete']==true){
			$url = '&error='.$this->language->get('error_default');
			$this->response->redirect($this->url->link('checkout/checkout', $url, 'SSL'));
			exit;
		}elseif(isset($this->request->get['one_page_checkout_delete']) && $this->request->get['one_page_checkout_delete']==true){
			$url = '&error='.$this->language->get('error_default');
			$this->response->redirect($this->url->link('checkout/one_page_checkout', $url, 'SSL'));
			exit;
		}
		$this->getList();
	}

	protected function getList() {
        
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

        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));      
 
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_my_orders'),
			'href' => $this->url->link('account/order', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/address', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_address_book'] = $this->language->get('text_address_book');
		$data['text_empty'] = $this->language->get('text_empty');

		$data['button_new_address'] = $this->language->get('button_new_address');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_back'] = $this->language->get('button_back');

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

		$data['addresses'] = array();

		$results = $this->model_account_address->getAddresses();

		foreach ($results as $result) {
			if ($result['address_format']) {
				$format = $result['address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $result['firstname'],
				'lastname'  => $result['lastname'],
				'company'   => $result['company'],
				'address_1' => $result['address_1'],
				'address_2' => $result['address_2'],
				'city'      => $result['city'],
				'postcode'  => $result['postcode'],
				'zone'      => $result['zone'],
				'zone_code' => $result['zone_code'],
				'country'   => $result['country']
			);

			$data['addresses'][] = array(
				'address_id' => $result['address_id'],
				'address'    => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
				'update'     => $this->url->link('account/address/edit', 'address_id=' . $result['address_id'], 'SSL'),
				'delete'     => $this->url->link('account/address/delete', 'address_id=' . $result['address_id'], 'SSL')
			);
		}
                
                //get chatbox language
                $data['text_chatbox_message'] = $this->language->get('text_chatbox_message');
                $data['text_whatsApp'] = $this->language->get('text_whatsApp');
                $data['text_chat_on_whatsappw_web'] = $this->language->get('text_chat_on_whatsappw_web');
                $data['text_whatsappw_no'] = $this->language->get('text_whatsappw_no');
                $data['text_whatsappw_no_msg_text'] = $this->language->get('text_whatsappw_no_msg_text');
                $data['text_callus'] = $this->language->get('text_callus');
                $data['text_callus_no'] = $this->language->get('text_callus_no');
                $data['text_call_back_request'] = $this->language->get('text_call_back_request');
                $data['text_call_back_request_messgae'] = $this->language->get('text_call_back_request_messgae');
                $data['text_chat_here'] = $this->language->get('text_chat_here');
                $data['text_error_mobile_no'] = $this->language->get('text_error_mobile_no');
                $data['text_error_mobile_not_start_zero'] = $this->language->get('text_error_mobile_not_start_zero');
                
                
		$data['add'] = $this->url->link('account/address/add', '', 'SSL');
		$data['back'] = $this->url->link('account/account', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/address_list.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/address_list.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/address_list.tpl', $data));
		}
	}

	protected function getForm() {

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

        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
 
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();

		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));


		$data['breadcrumbs'] = array();
		$data['name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();


		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/address', '', 'SSL')
		);

		if (!isset($this->request->get['address_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_edit_address'),
				'href' => $this->url->link('account/address/add', '', 'SSL')
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_edit_address'),
				'href' => $this->url->link('account/address/edit', 'address_id=' . $this->request->get['address_id'], 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit_address'] = $this->language->get('text_edit_address');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['entry_name'] = $this->language->get('entry_name');

		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_default'] = $this->language->get('entry_default');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');
		$data['button_upload'] = $this->language->get('button_upload');

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['address_1'])) {
			$data['error_address_1'] = $this->error['address_1'];
		} else {
			$data['error_address_1'] = '';
		}

		if (isset($this->error['city'])) {
			$data['error_city'] = $this->error['city'];
		} else {
			$data['error_city'] = '';
		}

		if (isset($this->error['postcode'])) {
			$data['error_postcode'] = $this->error['postcode'];
		} else {
			$data['error_postcode'] = '';
		}

		if (isset($this->error['country'])) {
			$data['error_country'] = $this->error['country'];
		} else {
			$data['error_country'] = '';
		}

		if (isset($this->error['zone'])) {
			$data['error_zone'] = $this->error['zone'];
		} else {
			$data['error_zone'] = '';
		}
		
		if (!isset($this->request->get['address_id'])) {
			$data['action'] = $this->url->link('account/address/add', '', 'SSL');
		} else {
			$data['action'] = $this->url->link('account/address/edit', 'address_id=' . $this->request->get['address_id'], 'SSL');
		}

		if (isset($this->request->get['address_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$address_info = $this->model_account_address->getAddress($this->request->get['address_id']);
		}

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
            $arr_name = explode(" ", $data['name']);
            $this->request->post['firstname'] = $arr_name[0];
            if(isset($arr_name[1])) {
                $this->request->post['lastname'] = $arr_name[1];
            }else{
                $this->request->post['lastname'] = '';
            }

        }


		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($address_info)) {
			$data['firstname'] = $address_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($address_info)) {
			$data['lastname'] = $address_info['lastname'];
		} else {
			$data['lastname'] = '';
		}





		if (isset($this->request->post['company'])) {
			$data['company'] = $this->request->post['company'];
		} elseif (!empty($address_info)) {
			$data['company'] = $address_info['company'];
		} else {
			$data['company'] = '';
		}

		if (isset($this->request->post['address_1'])) {
			$data['address_1'] = $this->request->post['address_1'];
		} elseif (!empty($address_info)) {
			$data['address_1'] = $address_info['address_1'];
		} else {
			$data['address_1'] = '';
		}

		if (isset($this->request->post['address_2'])) {
			$data['address_2'] = $this->request->post['address_2'];
		} elseif (!empty($address_info)) {
			$data['address_2'] = $address_info['address_2'];
		} else {
			$data['address_2'] = '';
		}

		if (isset($this->request->post['postcode'])) {
			$data['postcode'] = $this->request->post['postcode'];
		} elseif (!empty($address_info)) {
			$data['postcode'] = $address_info['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} elseif (!empty($address_info)) {
			$data['city'] = $address_info['city'];
		} else {
			$data['city'] = '';
		}

		if (isset($this->request->post['address_telephone'])) {
			$data['address_telephone'] = $this->request->post['address_telephone'];
		} elseif (!empty($address_info)) {
			$data['address_telephone'] = implode(",", $address_info['address_telephone']);
		} else {
			$data['address_telephone'] = '';
		}

		if (isset($this->request->post['address_alternate_telephone'])) {
			$data['address_alternate_telephone'] = $this->request->post['address_alternate_telephone'];
		} elseif (!empty($address_info)) {
			$data['address_alternate_telephone'] = $address_info['address_alternate_telephone'];
		} else {
			$data['address_alternate_telephone'] = '';
		}



		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		}  elseif (!empty($address_info)) {
			$data['country_id'] = $address_info['country_id'];
		} else {
			$data['country_id'] = '';//$this->config->get('config_country_id');
		}

		if (isset($this->request->post['zone_id'])) {
			$data['zone_id'] = $this->request->post['zone_id'];
		}  elseif (!empty($address_info)) {
			$data['zone_id'] = $address_info['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['default'])) {
			$data['default'] = $this->request->post['default'];
		} elseif (isset($this->request->get['address_id'])) {
			$data['default'] = $this->customer->getAddressId() == $this->request->get['address_id'];
		} else {
			$data['default'] = false;
		}

		$data['back'] = $this->url->link('account/address', '', 'SSL');


		if (isset($this->request->get['popup']) && $this->request->get['popup'] == true) {
			$data['popup'] = true;
		}else{
			$data['popup'] = false;
		}
		if(isset($this->request->post['popup']) && $this->request->post['popup'] == true){
			$data['popup'] = $this->request->post['popup'];
		}

		if(isset($data['popup']) && $data['popup'] == true){
			$this->request->get['popup'] = true;
			$data['column_left'] = '';
			$data['column_right'] = '';
			$data['content_top'] = '';
			$data['content_bottom'] = '';
			$data['breadcrumbs'] = array();
		}else{
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
		}
       
       if(CONFIG_IS_MOBILE == 1)
       {
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
	    }	
		/* *
		 * Check For Country Drop down list(START)
		 * 05-03-2016(Ravindra Singh)
		 * */
		 /*if(isset($_COOKIE['country']) && $_COOKIE['country'] == 'IN'){
			 $show_country = 0;
		 }else{
			 $show_country = 1;
		 }*/
		//updated by vikas
		/*if($this->config->get('config_store_id') == 2){
			$show_country = 1;
		}else{
			$show_country = 0;
		}*/
		 $data['show_country'] = 1;

		/* *
		 * Check For Country Drop down list(END)
		 * 05-03-2016(Ravindra Singh)
		 * */
                 
                //get chatbox language
                $data['text_chatbox_message'] = $this->language->get('text_chatbox_message');
                $data['text_whatsApp'] = $this->language->get('text_whatsApp');
                $data['text_chat_on_whatsappw_web'] = $this->language->get('text_chat_on_whatsappw_web');
                $data['text_whatsappw_no'] = $this->language->get('text_whatsappw_no');
                $data['text_whatsappw_no_msg_text'] = $this->language->get('text_whatsappw_no_msg_text');
                $data['text_callus'] = $this->language->get('text_callus');
                $data['text_callus_no'] = $this->language->get('text_callus_no');
                $data['text_call_back_request'] = $this->language->get('text_call_back_request');
                $data['text_call_back_request_messgae'] = $this->language->get('text_call_back_request_messgae');
                $data['text_chat_here'] = $this->language->get('text_chat_here'); 
                 

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/address_form.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/address_form.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/address_form.tpl', $data));
		}
	}

	protected function validateForm() {

		$store_id = (int)($this->config->get('config_store_id'));

		if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}
/*
		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}
*/
		if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
			$this->error['address_1'] = $this->language->get('error_address_1');
		}

		if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

        if($this->request->post['country_id'] != 99)
        {  
		  if (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10) 
		  {
			$this->error['postcode'] = $this->language->get('error_postcode');
		   }
		}
		else
		{
           if (!preg_match ('/^([0-9]+)$/', $this->request->post['postcode']) || (utf8_strlen(trim($this->request->post['postcode'])) < 6 && utf8_strlen(trim($this->request->post['postcode']) > 6))) {
			$this->error['postcode'] = $this->language->get('error_postcode2');
		   }
		} 

		if ($this->request->post['country_id'] == '') {
			$this->error['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id']) || empty($this->request->post['zone_id'])) {
			$this->error['zone'] = $this->language->get('error_zone');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if ($this->model_account_address->getTotalAddresses() == 1) {
			$this->error['warning'] = $this->language->get('error_delete');
		}

		/*if ($this->customer->getAddressId() == $this->request->get['address_id']) {
			$this->error['warning'] = $this->language->get('error_default');
		}*/

		return !$this->error;
	}

    public function autoPopulateAddress(){


        if(!empty($this->request->post['pincode'])) {
            $request['pincode'] = $this->request->post['pincode'];
            $data_json = json_encode($request);

            $api_url = "https://www.wholesalebox.in/index.php?route=restapi/lookup/pincode";
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array('Content-Type: application/json',
                    'Content-Length: ' . strlen($data_json))
            );
            curl_setopt($ch, CURLOPT_VERBOSE, 1);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
            $data = json_decode($result, true );

        }

        $info_to_be_populated = array();
        $city = isset($data[0]['city']) ? $data[0]['city']:'';
        $state = isset($data[0]['zone']) ? $data[0]['zone']:'--- Please Select ---';
        $country = isset($data[0]['country']) ? $data[0]['country']:'';
        $country_id = isset($data[0]['country_id']) ? $data[0]['country_id']:99;;
        $zone_id = isset($data[0]['zone_id']) ? $data[0]['zone_id']:'';


        $info_to_be_populated['city'] = $city;
        $info_to_be_populated['state'] = $state;
        $info_to_be_populated['country'] = $country;


        echo json_encode($info_to_be_populated); exit;


    }
}
