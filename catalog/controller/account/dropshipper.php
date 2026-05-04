<?php
class ControllerAccountDropshipper extends Controller {

	public function index() {
		$this->load->language('account/account');
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
		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));
		
		$this->document->setTitle($this->language->get('title'));
		$this->document->setDescription($this->language->get('description'));
		$this->document->setKeywords($this->language->get('keywords'));


		$data['title'] = $this->document->getTitle();
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['description'] = $this->document->getDescription();
	    $data['keywords'] = $this->document->getKeywords();
	    $data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();

		if(CONFIG_IS_MOBILE == 1)
        {
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
	    }

		$data['logged'] = $this->customer->isLogged();

		$data['dropshipper'] =  $this->model_tool_image->getOriginalImage('dropshipper.jpg');
		// echo $meta_description; die;

		if ($data['logged']) {
			$this->load->model('account/customer'); 
			$customer_id = $this->customer->getId();
			$data['is_dropshipper'] = $this->model_account_customer->getisdropshipper($customer_id);
		//	print_r($data['is_dropshipper']); die;	
		}
	
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/dropshipper.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/dropshipper.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/dropshipper.tpl', $data));
		}
	}

	public function updateDropshipper(){
		$this->load->model('account/customer');
		$customer_id = $this->customer->getId();
	    $this->model_account_customer->updateDropshipper($customer_id);
	}

}