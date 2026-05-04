<?php
class ControllerCommonHome extends Controller {
	
	public function index() {

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['base'] = $this->config->get('config_ssl');
			$data['static_content_url'] = STATIC_CONTENT_URL_SSL;
		} else {
			$data['base'] = $this->config->get('config_url');
			$data['static_content_url'] = STATIC_CONTENT_URL;
		}
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		//For Canonical URLs
		//Parth Gupta
		$can_url=$this->url->link('common/home');
		$can_url=str_replace('index.php?route=common/home', '', $can_url); 
		$this->document->addLink($can_url,'canonical');

		if (isset($this->request->get['route'])) {
			$this->document->addLink(HTTP_SERVER, 'canonical');
		}
		$this->load->model('catalog/product');
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));

		$this->load->language('common/language');
        $data['header'] = $this->load->controller('common/header');
		//$data['home_advertise_article'] = $this->language->get('home_advertise_article');
		$data['dealoftheday_link'] = $this->language->get('dealoftheday_link'); // add line for deal of the day.
		//$data['column_left'] = $this->load->controller('common/column_left');
		//$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['mobile_slide_b'] = $this->load->controller('common/mobile_slide_banner');

		 // this page run in worker js now...Mahaveer choudhary
		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		$data['home_side_banner'] = $this->load->controller('common/home_side_banner');
		$data['footer_top'] = $this->load->controller('common/footer_top');


        $data['footer'] = $this->load->controller('common/footer');
		//echo "<pre>"; print_r($data['header']); exit;
		$data['kurti_category_href'] = $this->url->link('product/category',  'path=61');
		$data['plazo_category_href'] = $this->url->link('product/category',  'path=65');
		$data['patiyala_category_href'] = $this->url->link('product/category',  'path=66');
		$data['dupatta_category_href'] = $this->url->link('product/category',  'path=64');
		$data['tops_category_href'] = $this->url->link('product/category',  'path=72');
		$data['catalouge_category_href'] = $this->url->link('product/category',  'path=70');
        $data['text_tabs_bottom_1'] = $this->language->get('text_tabs_bottom_1');
        $data['text_tabs_bottom_2'] = $this->language->get('text_tabs_bottom_2');
        $data['text_tabs_bottom_3'] = $this->language->get('text_tabs_bottom_3');

        $data['text_tab_info_1'] = $this->language->get('text_tab_info_1');
        $data['text_tab_info_2'] = $this->language->get('text_tab_info_2');
        $data['text_tab_info_3'] = $this->language->get('text_tab_info_3');
        $data['text_two_cashback'] = $this->language->get('text_two_cashback');

        $data['text_users']         = $this->language->get('text_users');
		$data['text_designs']       = $this->language->get('text_designs');
		$data['text_latest_stats']  = $this->language->get('text_latest_stats');

		$data['show_promotional_tabs'] = 0;

		if ($this->config->get('config_store_id')  != INTERNATIONAL_STORE_ID) {
			$data['show_promotional_tabs'] = 1;
		}
		/*
		*  Showing number of designs, users
		*/
	  $this->load->model('account/customer');
	  $data['total_customers'] = $this->model_account_customer->getTotalCustomers();  
	  $this->load->model('catalog/product');
	  $data['total_products'] = $this->model_catalog_product->getHomePageProductsTotal();

        $data['categories_menu'] = array();

		$categories = $this->model_catalog_category->getCategories(0);
        //echo "<pre>"; print_r($categories); die;
        $category_ids = array();
        foreach ($categories as $category) {
            $category_ids[] = $category['category_id'];
            $category_id = array(
                'category_id' => $category['category_id'],
                'name' => $category['name'],
                'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
            );
            //$data['content_category'][] = $this->load->controller('module/latest_as_category', $category_id);
			if ($category['top']) {
				// Level 1
				$data['categories_menu'][] = array(
					'name'     => $category['name'],
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}
		$data['category_ids'] = $category_ids;
		/*
		$results = $this->model_catalog_product->getRelatedByCategoryId(61);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
					$image_medium = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
					$image_medium = '';
				}

				$seller_tax_factor = 1.0 + ( (float)$result['seller_tax'] / 100.0 );
				$commission_factor = 1.0 + ( (float)$result['commission'] / 100.0 );

				$piece_in_set = (int)$result['piece_in_set'] > 1 ? (int)$result['piece_in_set'] : 1;
				$unit_price =  ceil($commission_factor * (float)($result['price']) / $seller_tax_factor);

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$unit_special_price = ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
					$special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? ceil($commission_factor * (float)($result['special']) / $seller_tax_factor) : ceil($commission_factor * (float)($result['price']) / $seller_tax_factor));
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'image_medium'=> $image_medium,
					'name'        => $result['name'],
					'set_description' => $result['set_description'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'piece_in_set' => $piece_in_set,
					'tax'         => $tax,
					'tax_class_id' => $result['tax_class_id'],
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		*/

		/* *
		 * For Mobile Theme (Send All Categories)
		 * @Date 14-12-2015
		 * @added By Ravindra Singh
		 * */
		if(parent::isDeviceMobile()) { //added by Rakesh
			$this->load->model('tool/image');
			$this->load->model('catalog/category');
			$categories = $this->model_catalog_category->getCategories(0);
			$data['categories'] = array();
			//echo "<pre>"; print_r($categories); exit;
			foreach ($categories as $category) {
                if(NGINX_ENABLED == 1){
                    $img = $this->model_tool_image->getOriginalImage($category['image']);
                }
                else{
                	$this->model_tool_image->getOriginalImage($category['image']);
                }

				//$image = empty($category['image']) ? 'no_image.png' : $category['image'];
				//$thumb = $this->model_tool_image->resize($image, '720', '198');
				//$thumb = HTTP_SERVER.'image/'.$image;

				$data['categories'][] = array(
						'name' => $category['name'],
						'column' => $category['column'] ? $category['column'] : 1,
						'thumb' => $img,
						'href' => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		if(isset($this->request->get['amp']) && $this->request->get['amp'] == 1) {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home-amp.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/home-amp.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/common/home-amp.tpl', $data));
			}
		}else{
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/home.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/common/home.tpl', $data));
			}
		}

	}


	/***********download redirect app on play store by vikas (08-02-2016)*********************/
		public function track(){
			$this->load->language('common/language');

			//$this->document->setMeta('no-index no follow');
			$data['app_download_link'] = $this->language->get('text_app_download_link');
			$data['text_redirect_msg'] = $this->language->get('text_redirect_msg');
			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/track.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/track.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/common/track.tpl', $data));
			}
		}
	/*************************************************/

		public function postYourRequirement(){
			$data = array();
			$email = array();	
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
         	if($store_id == INTERNATIONAL_STORE_ID){
				   $data['international_store'] = 1;
			}	   

			$this->document->setDescription($this->config->get('config_meta_description'));
			$this->document->setKeywords($this->config->get('config_meta_keyword'));
			$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));
			$this->load->language('account/get_customer_preferences');
			$this->document->setTitle($this->language->get('heading_title'));
			$data['social_meta_tags'] = $this->document->getSocialMetaTags();
			$data['base'] = $server;
			$data['title'] = $this->document->getTitle();
			$data['description'] = $this->document->getDescription();
			$data['keywords'] = $this->document->getKeywords();
			$data['links'] = $this->document->getLinks();
			$data['styles'] = $this->document->getStyles();
			
			$this->load->model('catalog/category');
			$this->load->model('account/return');
			
			$data['preference_text']  = $this->language->get('preference_text');
			$data['description_text'] = "Please describe your buying requirement. i.e Fabric,material and many other preferences.";
			if($this->request->post){
				if(empty($this->request->post['category'])){
					$data['error'] = "Please select category";
				}else{
					$post['category_name'] = $this->request->post['category'];
				}
				if(empty($this->request->post['quantity'])){
					$post['quantity'] = 0;
				}else{
					$post['quantity'] = $this->request->post['quantity'];
				}
				if(empty($this->request->post['require_days'])){
					$post['required_days'] = "";
				}else{
					$post['required_days'] = $this->request->post['require_days'];
				}
				if(empty($this->request->post['price_from'])){
					$post['price_from'] = 0;
				}else{
					$post['price_from'] = $this->request->post['price_from'];
				}
				if(empty($this->request->post['price_to'])){
					$post['price_to'] = 0;
				}else{
					$post['price_to'] = $this->request->post['price_to'];
				}
				if(empty($this->request->post['mobile_no'])){
					$data['error'] = "Please provide name";
				}else{
					$post['name'] = $this->request->post['name'];
				}
				if(empty($this->request->post['email'])){
					$data['error'] = "Please provide email";
				}else{
					$post['email'] = $this->request->post['email'];
				}
				if(empty($this->request->post['mobile_no'])){
					$data['error'] = "Please provide mobile";
				}else{
					$post['mobile_no'] = $this->request->post['mobile_no'];
				}
				if(!empty($this->request->post['refrenece_link'])){
					$post['refrenece_link'] = $this->request->post['refrenece_link'];
				}else{
					$post['refrenece_link'] = '';
				}
				if(!empty($this->request->post['description'])){
					$post['description'] = $this->request->post['description'];
				}else{
					$post['description'] = '';
				}
			}
			$menus = $this->model_catalog_category->menus('Desktop', 0, 'desktop-category');
			if(!empty($menus)){
				$menu_title = array_column($menus,'link_title');
				$data['menu'] = $menu_title;
			}
			if(!isset($data['error']) && !empty($post) && $this->request->post){
				$this->load->model('restapi/wsbcartservice');
				$result = $this->model_restapi_wsbcartservice->insertRequirement($post);
			
				if(isset($result['success'])){
					$mail_data['firstname'] = $post['name'];
					$mail_data['telephone'] = $post['mobile_no'];
					$mail_data['email'] = $post['email'];
					$mail_data['category_name'] = $post['category_name'];
					$mail_data['quantity'] = $post['quantity'];
					$mail_data['required_days'] = $post['required_days'];
					$mail_data['price_from'] = $post['price_from'];
					$mail_data['price_to'] = $post['price_to'];
					$mail_data['refrenece_link'] = $post['refrenece_link'];
					$mail_data['description'] = $post['description'];
					$mail_data['image_name'] = $result['images_data'];
					
					$body = $this->model_restapi_wsbcartservice->getmailBody($mail_data);
					$subject = 'Product Requirement query from '.$post['category_name'];
					
					$email['buyer_email'] = EMAIL_IDS['prabhav']['email_id']; 
					$email['buyer_name']  = EMAIL_IDS['prabhav']['name']; 
					$email['post_your_req'] = 1;
					$this->model_account_return->sendMailFromApi($email,$subject,$body);
                    $data['success'] = "Your requirement is submitted successfully!";
				}
			}		
			$this->response->setOutput($this->load->view('default/template/common/post_your_requirement.tpl', $data));
		}
                
                
    public function sendMailForCallBackRequest(){
        
        

        $mobile = $this->request->get['mobile'];
        
        
        if(!empty($mobile)){
        $request['mobile'] = $mobile;
        $data_json = json_encode($request);
        
        $api_url = CRM_URL . "cron/saveCallbyRequestLead";
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
            'Content-Length: ' . strlen($data_json))
        );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $result = json_decode($result, true);
        if(isset($result['status']) && $result['status'] != 1){
            $mail = new PHPMailer();
             $subject = "Lead Add/Update failed for Callback Request From: " . $mobile;

            $body    = ""; 
            $body .= "Dear Rakesh,<br/><br/>";
            $body .= "Lead add/update failed for .<br/>";
            $body .= "Customer Mobile Number: " . $mobile . "<br/><br/>";

        $mail->isSMTP(); 
        //$mail->SMTPDebug = 1; 
        $mail->SMTPAuth = true; 
        $mail->SMTPSecure = 'ssl'; 
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port'); 
        $mail->Username = $this->config->get('config_mail_smtp_username');        
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->SetFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');         

        $mail->addAddress(EMAIL_IDS['rakesh']['email_id'],EMAIL_IDS['rakesh']['name']); 

        $mail->Subject = $subject;                  
        $mail->Body    = $body;  
        $mail->isHTML(true);

        $mail = $mail->Send(true);
        }
        }
        $mail = new PHPMailer();
        $subject = "Callback Request From: " . $mobile;

        $body    = ""; 
        $body .= "Dear Wholesalebox Team,<br/><br/>";
        $body .= "Following customer has placed a request for callback.<br/>";
        $body .= "Customer Mobile Number: " . $mobile . "<br/><br/>";
		$body .= "Please Assist <br/>";

        $mail->isSMTP(); 
        //$mail->SMTPDebug = 1; 
        $mail->SMTPAuth = true; 
        $mail->SMTPSecure = 'ssl'; 
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port'); 
        $mail->Username = $this->config->get('config_mail_smtp_username');        
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->SetFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');         

        $mail->addAddress(EMAIL_IDS['info']['email_id'],EMAIL_IDS['info']['name']); 

        $mail->Subject = $subject;                  
        $mail->Body    = $body;  
        $mail->isHTML(true);

        $mail = $mail->Send(true);

    }
            
                
}
