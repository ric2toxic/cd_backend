<?php
class ControllerinformationPolicies extends Controller {

	public function index() {
		$this->load->language('information/information');
		$this->load->model('catalog/information');
		 $this->load->model('tool/image');

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
 
          $data['social_meta_tags'] = $this->document->getSocialMetaTags();
          $data['base'] = $server;
		  $data['links'] = $this->document->getLinks();
		  $data['styles'] = $this->document->getStyles();
		
		  $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

		  $data['breadcrumbs'] = array();
		  $data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
	      	);

		    
		   $informations = $this->model_catalog_information->getInformations();
           
           $i=0;
           foreach ($informations as $result) 
           {
           	if ($result['bottom']) {
               if($i==0) { $class = 'active'; } else { $class = ''; }
              $data['informations'][] = array(
                     'information_id' => $result['information_id'],
                     'title' => $result['title'],
                     'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id']),
                     'short_description'  => html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8'),
                     'meta_title'  => $result['meta_title'],
                     'meta_description'  => $result['meta_description'],
                     'meta_keyword'  => $result['meta_keyword'],
                     'class'  => $class);
                $i++;
             } 
           }


			$this->document->setTitle("Policies");
			$data['heading_title'] = "Policies";
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
		  if(CONFIG_IS_MOBILE == 1)
           {	
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		   }	

		    $data['title'] = $this->document->getTitle();
            $data['social_meta_tags'] = $this->document->getSocialMetaTags();
            $data['base'] = $server;
		    $data['description'] = $this->document->getDescription();
		    $data['keywords'] = $this->document->getKeywords();
	    	$data['links'] = $this->document->getLinks();
		    $data['styles'] = $this->document->getStyles();

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/policies.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/policies.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/information/policies.tpl', $data));
			}
		
	}

}