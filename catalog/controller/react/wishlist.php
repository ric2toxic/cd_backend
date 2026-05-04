<?php
class ControllerReactWishlist extends Controller {

	public function index()
	{

		if(!$this->customer->isLogged()){
			$this->response->redirect($this->url->link('react/home'));
		}

		$this->load->model('tool/image');

		$this->load->language('account/wishlist');

        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));


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

    	if (isset($this->session->data['custom_store']) && $this->session->data['custom_store'] != '') {
            $data['custom_store_val'] = $this->session->data['custom_store'];
        } else {
            $data['custom_store_val'] = 'set';
        }

		$data['title'] = $this->document->getTitle();
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
	    $data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();

		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

       	$data['home_url'] = $this->url->link('common/home', '', 'SSL'); 

       	$myaccount_language = array();
		$header_language = array();
		$footer_language = array();
		$this->load->autoLoadLanguage('account/account', $myaccount_language);
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);

		$language = $data;
 		
 		$logout_url = $this->url->link('account/logout','','SSL');
 		// get a menus and set on not record found components
 		$data['myaccount_language'] = json_encode($myaccount_language);
		$data['header_language'] 	= json_encode($header_language);
		$data['footer_language'] 	= json_encode($footer_language);
		$data['logout_url']		 	= $logout_url;
		$data['lang']      		 	= $header_language['code'];
		$data['direction'] 			= $header_language['direction'];

		if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
       		$data['international_store'] = 1;
      	} else {
            $data['international_store'] = 0;
        }

		if( file_exists( DIR_TEMPLATE . $this->config->get('config_template') . 'template/react/wishlist.tpl' ) ) {
			$this->response->setOutput( $this->load->view( $this->config->get('config_template') . 'template/react/wishlist.tpl' , $data) );
		} else {
			$this->response->setOutput( $this->load->view( 'default/template/react/wishlist.tpl' , $data) );
		}
	}

}