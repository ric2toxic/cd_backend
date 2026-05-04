<?php
class Controllerinformationaboutus extends Controller {
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
		

	   $data['mobile_logo'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

        $this->load->model('account/customer');
        $this->load->model('catalog/product');
        
		$data['total_customers'] = $this->model_account_customer->getTotalCustomers();  
        $data['total_products'] = $this->model_catalog_product->getHomePageProductsTotal();
      
		if (isset($this->request->get['information_id'])) {
		   $information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 4;
		}
        
		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
            $this->document->setTitle('About Us');
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);


		    $data['social_meta_tags'] = $this->document->getSocialMetaTags();
            $data['base'] = $server;
		    $data['links'] = $this->document->getLinks();
		    $data['styles'] = $this->document->getStyles();
		    $data['title'] = $this->document->getTitle();
		    $data['description'] = $this->document->getDescription();
		    $data['keywords'] = $this->document->getKeywords();	

			$data['breadcrumbs'][] = array(
				'text' => $information_info['title'],
				'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
			);

			$data['heading_title'] = $information_info['title'];
			$data['button_continue'] = $this->language->get('button_continue');
			$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
			$data['continue'] = $this->url->link('common/home');
           
           
           if(CONFIG_IS_MOBILE == 1)
           {
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		   }	

			$team_cofounder[] = array('name' => 'Rohit Dangayach',	'department'=>'Co-Founder', 'photo'=>$this->model_tool_image->getOriginalImage('team/cf-1.jpg'), 'linkedin'=>'https://in.linkedin.com/pub/rohit-dangayach/2/a2/876');
			$team_cofounder[] = array('name' => 'Chandan Agarwal',	'department'=>'Co-Founder', 'photo'=>$this->model_tool_image->getOriginalImage('team/cf-2.jpg'), 'linkedin'=>'https://in.linkedin.com/pub/chandan-agarwal/105/6a8/933');
			$team_cofounder[] = array('name' => 'Rakesh Shekhawat',	'department'=>'Co-Founder', 'photo'=>$this->model_tool_image->getOriginalImage('team/cf-3.jpg'), 'linkedin'=>'https://in.linkedin.com/in/rakeshshekhawat');
			$team_cofounder[] = array('name' => 'Madhur Maheshwari','department'=>'Co-Founder', 'photo'=>$this->model_tool_image->getOriginalImage('team/cf-4.jpg'), 'linkedin'=>'https://in.linkedin.com/pub/madhur-bhaiya/10/b90/614');

			$team_tech[] = array('name' => 'Ravindra Shekhawat',	'department'=>'Technology', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-1.jpg') );
			$team_tech[] = array('name' => 'Parth Gupta',		'department'=>'Technology', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-2.jpg') );
			$team_tech[] = array('name' => 'Neeraj Bagra',		'department'=>'Technology', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-6.jpg') );
			$team_tech[] = array('name' => 'Vikas Agrawal',		'department'=>'Technology', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-8.jpg') );
			$team_tech[] = array('name' => 'Garvit Joshi',		'department'=>'Technology', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-10.jpg') );
			$team_tech[] = array('name' => 'Tushar Taneja',		'department'=>'Designer', 	'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-13.jpg') );
			
			$team_operations[] = array('name' => 'Yash Naruka',		'department'=>'Sales', 		'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-3.jpg') );
			$team_operations[] = array('name' => 'Nitesh Bhutoria',	'department'=>'Operations', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-4.jpg') );
			$team_operations[] = array('name' => 'Ashu Vyas',			'department'=>'Operations', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-12.jpg') );			
			$team_operations[] = array('name' => 'Karishma Naruka',	'department'=>'Operations', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-15.jpg') );
			$team_operations[] = array('name' => 'Divya Kumawat',		'department'=>'Telecaller', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-17.jpg') );
			$team_operations[] = array('name' => 'Omprakash Sharma',	'department'=>'Telecaller', 'photo'=>$this->model_tool_image->getOriginalImage('team/wsb-18.jpg') );

			$data['team_cofounder'] = $team_cofounder;
			$data['team_tech'] = $team_tech;
			$data['team_operations'] = $team_operations;

		   $data['images']['wholesalebox_about'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about.jpg');
		   $data['images']['wholesalebox_about_2'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_2.jpg');
		   $data['images']['wholesalebox_about_graph'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_graph.jpg');
		   $data['images']['wholesalebox_about_how_we_work'] =  $this->model_tool_image->getOriginalImage('wholesalebox_about_how_we_work.jpg');
		   $data['images']['wholesalebox_rohit_dangayach'] =  $this->model_tool_image->getOriginalImage('wholesalebox_rohit_dangayach.jpg');
		   $data['images']['wholesalebox_chandan_agarwal'] =  $this->model_tool_image->getOriginalImage('wholesalebox_chandan_agarwal.jpg');
		   $data['images']['wholesalebox_rakesh_shekhawat'] =  $this->model_tool_image->getOriginalImage('wholesalebox_rakesh_shekhawat.jpg');
		   $data['images']['wholesalebox_madhur_maheshwari'] =  $this->model_tool_image->getOriginalImage('wholesalebox_madhur_maheshwari.jpg');
		   $data['images']['wsb_topimg'] =  $this->model_tool_image->getOriginalImage('team/wsb-topimg.jpg');
		   $data['images']['infographics_1'] =  $this->model_tool_image->getOriginalImage('infographics_1.jpg');
		   $data['images']['infographics_2'] =  $this->model_tool_image->getOriginalImage('infographics_2.jpg');
		   $data['images']['infographics_3'] =  $this->model_tool_image->getOriginalImage('infographics_3.jpg');
		   $data['images']['AU_img_2'] =  $this->model_tool_image->getOriginalImage('team/AU_img-2.jpg');
		   $data['images']['AU_img_3'] =  $this->model_tool_image->getOriginalImage('team/AU_img-3.jpg');
		   $data['images']['AU_img_4'] =  $this->model_tool_image->getOriginalImage('team/AU_img-4.jpg');
		   $data['images']['AU_img_5'] =  $this->model_tool_image->getOriginalImage('team/AU_img-5new.jpg');
		   $data['images']['blue_line'] =  $this->model_tool_image->getOriginalImage('team/blue-line.png');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/aboutus.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/aboutus.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/information/aboutus.tpl', $data));
			}
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		   if(CONFIG_IS_MOBILE == 1)
           {
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		   }	
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
			}
		}
	}

	public function agree() {
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}