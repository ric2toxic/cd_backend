<?php

class ControllerSellerWebsiteSettings extends ControllerSellerAccount {

	public function index() {
		$this->document->addStyle('catalog/view/theme/default/javascript/summernote/summernote.css');
        $this->document->addScript('catalog/view/theme/default/javascript/summernote/summernote.js');
		$this->data['store_type_values'] = array( 'both'=>'Both [Will sell sets and singles both]',
										 'set_only'=>'Will sell sets only',
										 'singles_only'=>'Will sell singles only');

		$this->load->language('seller/account-profile');
		if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}else{
			$this->data['strs'] = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
			$store_id = $this->data['strs'][0]['store_id'];
			$this->session->data['seller_store_id'] = $store_id;
		}

		$logo_width = $this->customer->getStoreConfigForSeller($store_id, 'config_logo_width');
		$logo_height = $this->customer->getStoreConfigForSeller($store_id, 'config_logo_height');
		$mobile_logo_width = $this->customer->getStoreConfigForSeller($store_id, 'config_mobile_logo_width');
		$mobile_logo_height = $this->customer->getStoreConfigForSeller($store_id, 'config_mobile_logo_height');
		$favicon_width = 16;
		$favicon_height = 16;
		$payment_cards_width = 453;
		$payment_cards_height = 28;
		$shipping_banner_height = 180;
		$shipping_banner_width = 250;

		$this->data['select_seller_store_id'] = $store_id;
		$this->data['text_title'] = $this->language->get('text_title');
		$this->data['text_slug'] = $this->language->get('text_slug');
		$this->data['text_status'] = $this->language->get('text_status');
		$this->data['text_enable'] = $this->language->get('text_enable');
		$this->data['text_disable'] = $this->language->get('text_disable');
		$this->data['text_add_new_page'] = $this->language->get('text_add_new_page');
		$this->data['text_email'] = $this->language->get('text_email');
		$this->data['text_landline_no'] = $this->language->get('text_landline_no');
		$this->data['text_mobile_no'] = $this->language->get('text_mobile_no');
		$this->data['text_whatsapp_no'] = $this->language->get('text_whatsapp_no');
		$this->data['text_not_whats_app_no'] = $this->language->get('text_not_whats_app_no');
		$this->data['text_address'] = $this->language->get('text_address');
		$this->data['text_themes_color'] = $this->language->get('text_themes_color');
		$this->data['text_desktop_logo'] = $this->language->get('text_desktop_logo');
		$this->data['text_mobile_logo'] = $this->language->get('text_mobile_logo');
		$this->data['text_favicon'] = $this->language->get('text_favicon');
		$this->data['text_payment_cards_banner'] = $this->language->get('text_payment_cards_banner');
		$this->data['text_shipping_banner'] = $this->language->get('text_shipping_banner');
		$this->data['text_select_theme_color'] = $this->language->get('text_select_theme_color');
		$this->data['text_facebook'] = $this->language->get('text_facebook');
		$this->data['text_twitter'] = $this->language->get('text_twitter');
		$this->data['text_instagram'] = $this->language->get('text_instagram');
		$this->data['text_google'] = $this->language->get('text_google');
		$this->data['text_logo_detail'] = sprintf($this->language->get('text_logo_detail'), $logo_width , $logo_height);
		$this->data['text_mobile_logo_detail'] = sprintf($this->language->get('text_mobile_logo_detail'), $mobile_logo_width , $mobile_logo_height);
		$this->data['text_favicon_detail'] = sprintf($this->language->get('text_favicon_detail'), $favicon_width , $favicon_height);
		$this->data['text_shipping_banner_detail'] = sprintf($this->language->get('text_shipping_banner_detail'), $shipping_banner_width, $shipping_banner_height);
		$this->data['text_payment_cards_banner_detail'] = sprintf($this->language->get('text_payment_cards_banner_detail'), $payment_cards_width, $payment_cards_height);

		$this->data['entry_enter_title'] = $this->language->get('entry_enter_title');
		$this->data['entry_enter_slug'] = $this->language->get('entry_enter_slug');
		$this->data['entry_message_contant'] = $this->language->get('entry_message_contant');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_landline_no'] = $this->language->get('entry_landline_no');
		$this->data['entry_mobile_no'] = $this->language->get('entry_mobile_no');
		$this->data['entry_whatsapp_no'] = $this->language->get('entry_whatsapp_no');
		$this->data['entry_address'] = $this->language->get('entry_address');

		$this->data['website_pages'] = $this->language->get('website_pages');
		$this->data['website_themes'] = $this->language->get('website_themes');
		$this->data['categories'] = $this->language->get('text_categories');
		$this->data['social_profile'] = $this->language->get('text_social_profile');
		$this->data['contact_details'] = $this->language->get('text_contact_details');
		$this->data['no_pages'] = $this->language->get('no_pages');
		

		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		$seller_id = $seller['seller_id'];

		$this->load->model('catalog/information');
		$this->data['seller_page_info'] = $this->model_catalog_information->getpages($store_id);
		$this->data['page_themes'] = $this->model_catalog_information->getpage_themes();
		$this->data['user_themes'] = $this->customer->getStoreConfigForSeller($store_id, 'theme_seller');
		$this->data['facebook'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_social_facebook');
		$this->data['twitter'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_social_twitter');
		$this->data['instagram'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_social_instagram');
		$this->data['google'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_social_google');
		$this->data['copyright'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_copyright');
		$this->data['config_cart_limit'] = $this->customer->getStoreConfigForSeller($store_id, 'config_cart_limit');

		$this->data['store_type'] = $this->customer->getStoreConfigForSeller($store_id, 'store_type');
		/* Contact info start -gt*/
		$this->data['store_email'] = $this->customer->getStoreConfigForSeller($store_id, 'config_email');
		$this->data['store_landline_no'] = $this->customer->getStoreConfigForSeller($store_id, 'config_telephone');
		$this->data['store_mobile_no'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_mobile');
		$this->data['store_whatsapp_no'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_whatsapp_no');
		$this->data['store_address'] = $this->customer->getStoreConfigForSeller($store_id, 'config_address');
		$this->data['seller_default_store'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_default_store');
		$this->data['seller_promo_box'] = $this->customer->getStoreConfigForSeller($store_id, 'seller_promo_box');
		$this->load->model('tool/image');
		for($i=1; $i<5; $i++){
			$promo_box_image = $this->customer->getStoreConfigForSeller($store_id, 'seller_promo_box_'. $i. '_image');
			//if (is_file(DIR_IMAGE . $promo_box_image)) {
				$image = $promo_box_image;
				$thumb = $promo_box_image;
			//} else {
				//$image = '';
				//$thumb = 'no_image.png';
			//}
			$this->data['seller_promo_image_'.$i] = $this->model_tool_image->resize($thumb, 100, 100);
			$this->data['seller_promo_name_'.$i] = $this->customer->getStoreConfigForSeller($store_id, 'seller_promo_box_'. $i. '_name');
			$this->data['seller_promo_link_'.$i] = $this->customer->getStoreConfigForSeller($store_id, 'seller_promo_box_'. $i. '_link');
			$this->data['seller_promo_show_'.$i] = $this->customer->getStoreConfigForSeller($store_id, 'seller_promo_box_'. $i. '_show');
		}
		/*contact info end -gt*/
		
		$this->data['category'] = $this->model_catalog_information->getcategory();
		$this->data['seller_category'] = $this->model_catalog_information->getSellerCategory($store_id);
		$this->data['image'] = '';
		$config_logo	= $this->customer->getStoreConfigForSeller($store_id, 'config_logo');
		$mobile_logo 	= $this->customer->getStoreConfigForSeller($store_id, 'logo_seller_mobile');
		$config_icon	= $this->customer->getStoreConfigForSeller($store_id, 'config_icon');
		$payment_banner	= $this->customer->getStoreConfigForSeller($store_id, 'payment_banner');
		$shipping_banner= $this->customer->getStoreConfigForSeller($store_id, 'shipping_banner');

		if($this->config->get('msconf_enable_rte')) {
			$this->document->addScript('catalog/view/javascript/multimerch/summernote/summernote.js');
			$this->document->addStyle('catalog/view/javascript/multimerch/summernote/summernote.css');
		}
		$this->data['strs'] = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
        $this->data['stores'] = array();
        $this->data['count']	=	0;
        foreach ($this->data['strs'] as $st) {
            $this->data['stores'][] = $this->MsLoader->MsProduct->getStore($st['store_id']);
            $this->data['count']++;
        }

		//if (is_file(DIR_IMAGE . $config_logo)) {
			$image = $config_logo;
			$thumb = $config_logo;
		//} else {
			//$image = '';
			//$thumb = 'no_image.png';
		//}

		$this->data['seller_logo'] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100)
		);

		//if (is_file(DIR_IMAGE . $mobile_logo)) {
			$image = $mobile_logo;
			$thumb = $mobile_logo;
		//} else {
			//$image = '';
			//$thumb = 'no_image.png';
		//}

		$this->data['seller_mobile_logo'] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100)
		);

		//if (is_file(DIR_IMAGE . $config_icon)) {
			$image = $config_icon;
			$thumb = $config_icon;
		//} else {
			//$image = '';
			//$thumb = 'no_image.png';
		//}

		$this->data['seller_favicon'] = array(
			'image'      => $image,
			'thumb'      => $this->model_tool_image->resize($thumb, 100, 100)
		);

		//if (is_file(DIR_IMAGE . $payment_banner)) {
			$image = $payment_banner;
			$thumb = $payment_banner;
		//} else {
			//$image = '';
			//$thumb = 'no_image.png';
		//}

		$this->data['payment_cards_banner'] = array(
			'image'      => $image,
			'thumb'      => $this->model_tool_image->resize($thumb, 453, 28)
		);
//		if (is_file(DIR_IMAGE . $shipping_banner)) {
			$image = $shipping_banner;
			$thumb = $shipping_banner;
		//} else {
			//$image = '';
			//$thumb = 'no_image.png';
		//}

		$this->data['shipping_banners'] = array(
			'image'      => $image,
			'thumb'      => $this->model_tool_image->resize($thumb, 250, 180)
		);


		if ($seller) {
			switch ($seller['ms.seller_status']) {
				case MsSeller::STATUS_UNPAID:
				case MsSeller::STATUS_INCOMPLETE:
					$this->data['statusclass'] = 'warning';
					break;
				case MsSeller::STATUS_ACTIVE:
					$this->data['statusclass'] = 'success';
					break;
				case MsSeller::STATUS_DISABLED:
				case MsSeller::STATUS_DELETED:
					$this->data['statusclass'] = 'danger';
					break;
			}

			$this->data['header_seller'] = $this->load->controller('common/seller_header');
			$this->data['footer_seller'] = $this->load->controller('common/seller_footer');
			$this->data['seller'] = $seller; unset($this->data['seller']['banner']);
			$this->data['country_id'] = $seller['ms.country_id'];

			if (!empty($seller['ms.avatar'])) {
				$this->data['seller']['avatar']['name'] = $seller['ms.avatar'];
				$this->data['seller']['avatar']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
				$this->session->data['multiseller']['files'][] = $seller['ms.avatar'];
			}

			if ($this->config->get('msconf_enable_seller_banner')) {
				if (!empty($seller['banner'])) {
					$this->data['seller']['banner']['name'] = $seller['banner'];
					$this->data['seller']['banner']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['banner'], $this->config->get('msconf_product_seller_banner_width'), $this->config->get('msconf_product_seller_banner_height'));
					$this->session->data['multiseller']['files'][] = $seller['banner'];
				}
			}

			$this->data['statustext'] = '';

			if ($seller['ms.seller_status'] != MsSeller::STATUS_INCOMPLETE) {
				$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_seller_status_' . $seller['ms.seller_status']) . ' ';
			}

			if ($seller['ms.seller_status'] == MsSeller::STATUS_INACTIVE && !$seller['ms.seller_approved']) {
				$this->data['statustext'] .= $this->language->get('ms_account_status_tobeapproved');
			}

			if ($seller['ms.seller_status'] == MsSeller::STATUS_INCOMPLETE) {
				$this->data['statustext'] .= $this->language->get('ms_account_status_please_fill_in');
			}

			$this->data['ms_account_sellerinfo_terms_note'] = '';
		} else {
			$this->data['seller'] = FALSE;
			$this->data['country_id'] = $this->config->get('config_country_id');
			$this->data['statustext'] = $this->language->get('ms_account_status_please_fill_in');

			if ($this->config->get('msconf_seller_terms_page')) {
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

				if ($information_info) {
					$this->data['ms_account_sellerinfo_terms_note'] = sprintf($this->language->get('ms_account_sellerinfo_terms_note'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('msconf_seller_terms_page'), 'SSL'), $information_info['title'], $information_info['title']);
				} else {
					$this->data['ms_account_sellerinfo_terms_note'] = '';
				}
			} else {
				$this->data['ms_account_sellerinfo_terms_note'] = '';
			}
		}
		$this->data['seller_validation'] = $this->config->get('msconf_seller_validation');
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_sellerinfo_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller_panel/account-order', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_sellerinfo_breadcrumbs'),
				'href' => $this->url->link('seller/website-settings', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('seller-faq');
		$this->data['seller_faq'] =  $this->load->view($template, $this->data);

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('website-settings');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function addpages(){
		if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}
    	if(isset($this->request->post['page_title'])){
    		$content 	=	$this->request->post['content'];
    		$page_title	=	$this->request->post['page_title'];
    		$status		=	$this->request->post['status'];
    		if (!empty($page_title)) {
    			$this->load->model('catalog/information');
				$this->model_catalog_information->addpages($store_id, $content, $page_title, $status);
    		}
    	}
    }
    public function editpages(){
    	if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}
    	if(isset($this->request->post['page_title'])){
    		$data_id 	=	$this->request->post['data_id'];
    		$content 	=	$this->request->post['content'];
    		$page_title	=	$this->request->post['page_title'];
    		$status		=	$this->request->post['status'];
    		if (!empty($page_title)) {
    			$this->load->model('catalog/information');
				$this->model_catalog_information->editpages($data_id,$store_id, $content, $page_title, $status);
    		}
    	}
    }

	public function deletePages(){
		if(isset($this->session->data['seller_store_id'])) {
			$store_id = $this->session->data['seller_store_id'];
		}
		if(isset($this->request->post['data_id'])){
			$data_id = $this->request->post['data_id'];
			//echo $data_id; die;
			$this->load->model('catalog/information');
			$this->model_catalog_information->deletePages($data_id, $store_id);
		}
	}

    public function updatesellerdetails(){
    	if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}
		$seller_detail = array();
		
		$this->load->model('catalog/information');

		//echo $this->request->post['config_logo']; die;
    	if(isset($this->request->post['config_logo'])){
    		if(!empty($this->request->post['config_logo'])){
    			$seller_detail[] = array(
    				//'code' 		=> 	'logo',
					'code' 		=> 	'config',
    				'key' 		=>	'config_logo',
    				'value' 	=>	$this->request->post['config_logo']
    			);
    		}
      	}
    	if(isset($this->request->post['mobile_logo'])){
    		if(!empty($this->request->post['mobile_logo'])){
				$seller_detail[] = array(
					//'code' 		=> 	'logo',
					'code' 		=> 	'config',
					'key'		=>	'logo_seller_mobile',
					'value' 	=> 	$this->request->post['mobile_logo']
    			);
			}	
      	}

		if(isset($this->request->post['config_icon'])){
			if(!empty($this->request->post['config_icon'])){
				$seller_detail[] = array(
					//'code' 		=> 	'logo',
					'code' 		=> 	'config',
					'key' 		=>	'config_icon',
					'value' 	=>	$this->request->post['config_icon']
				);
			}
		}

    	if(isset($this->request->post['seller_theme'])){
    		if(!empty($this->request->post['seller_theme'])){
				$seller_detail[] = array(
					'code' 		=> 	'theme',
					'key' 		=>	'theme_seller',
					'value' 	=> 	$this->request->post['seller_theme']
    			);
			}	
      	}

		if(isset($this->request->post['facebook'])){

    			$seller_detail[] = array(  					
    				'code' 		=> 	'seller',
    				'key' 		=>	'seller_social_facebook',
    				'value' 	=>	$this->request->post['facebook']
    			);

      	}
    	if(isset($this->request->post['twitter'])){

				$seller_detail[] = array(  					
					'code' 		=> 	'seller',
					'key' 		=>	'seller_social_twitter',
					'value' 	=> 	$this->request->post['twitter']
    			);

      	}

    	if(isset($this->request->post['instagram'])){

				$seller_detail[] = array(  					
					'code' 		=> 	'seller',
					'key' 		=>	'seller_social_instagram',
					'value' 	=> 	$this->request->post['instagram'],
    			);

      	}
			
		if(isset($this->request->post['google'])){

				$seller_detail[] = array(  					
	  				'code' 		=> 	'seller',
					'key' 		=>	'seller_social_google',
					'value' 	=>  $this->request->post['google']
    			);

		}

		if(isset($this->request->post['store_type'])){

				$seller_detail[] = array(
					'code' 		=> 	'seller',
					'key' 		=>	'store_type',
					'value' 	=>  $this->request->post['store_type']
				);

		}
		if(isset($this->request->post['config_cart_limit'])){

			$seller_detail[] = array(
				'code' 		=> 	'config',
				'key' 		=>	'config_cart_limit',
				'value' 	=>  $this->request->post['config_cart_limit']
			);

		}

		if(isset($this->request->post['default_store'])){
			$seller_detail[] = array(
				'code' 		=> 	'seller',
				'key' 		=>	'seller_default_store',
				'value' 	=>  $this->request->post['default_store']
			);
		}

		$this->model_catalog_information->updateSellerWebsitesDetail($store_id, $seller_detail);      	
    }
    public function change_seller_store(){
    	if(!empty($this->request->post['store_id'])){
			$store_id 	= 	$this->request->post['store_id'];
			$this->session->data['seller_store_id'] = $store_id;
		}
    }
    public function updatesellercategory(){
    	if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}
		$this->load->model('catalog/information');
		if(isset($this->request->post['category'])){
			if(!empty($this->request->post['category'])){
			 	$category 	=	$this->request->post['category'];
    			$this->model_catalog_information->updateSellerCategory($store_id, $category); 
			}
		}
    }
    public function contact_info(){

    	if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}
		$seller_contact_info = array();
		
		$this->load->model('catalog/information');

		if(isset($this->request->post['seller']['config_email'])){
			$seller_contact_info[] = array(
				'code' 		=> 	'config',
				'key' 		=>	'config_email',
				'value' 	=>	$this->request->post['seller']['config_email']
    		);
		}		
		if(isset($this->request->post['seller']['config_telephone'])){
			$seller_contact_info[] = array(
				'code' 		=> 	'config',
				'key' 		=>	'config_telephone',
				'value' 	=>	$this->request->post['seller']['config_telephone']
    		);
		}
		if(isset($this->request->post['seller']['seller_mobile'])){
			$seller_contact_info[] = array(
				'code' 		=> 	'seller',
				'key' 		=>	'seller_mobile',
				'value' 	=>	$this->request->post['seller']['seller_mobile']
    		);
		}
		if(isset($this->request->post['seller']['seller_whatsapp_no']) && !empty($this->request->post['seller']['seller_whatsapp_no'])){
			$seller_contact_info[] = array(
				'code' 		=> 	'seller',
				'key' 		=>	'seller_whatsapp_no',
				'value' 	=>	$this->request->post['seller']['seller_whatsapp_no']
    		);
		}else if(isset($this->request->post['seller']['seller_mobile'])){
			$seller_contact_info[] = array(
				'code' 		=> 	'seller',
				'key' 		=>	'seller_whatsapp_no',
				'value' 	=>	$this->request->post['seller']['seller_mobile']
    		);
		}
		if(isset($this->request->post['seller']['config_address'])){
			$seller_contact_info[] = array(
				'code' 		=> 	'config',
				'key' 		=>	'config_address',
				'value' 	=>	$this->request->post['seller']['config_address']
    		);
		}
		
		$this->model_catalog_information->updateSellerWebsitesDetail($store_id, $seller_contact_info);  

		$this->response->redirect($this->url->link('seller/website-settings', '#contact-details', 'SSL'));
    }
    
 	public function promotional_boxes(){

 		if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}
		$seller_detail = array();
		$this->load->model('catalog/information');
    	if(isset($this->request->post['promo_box'])){
    		if(isset($this->request->post['show_hide_promo_select']) && !empty($this->request->post['show_hide_promo_select'])){
				$seller_detail[] = array(
					'code' 		=> 	'seller',
					'key' 		=>	'seller_promo_box',
					'value' 	=>  $this->request->post['show_hide_promo_select']
				);
			}
			for($i= 1; $i<5; $i++) {
				if(isset($this->request->post['promo_box'][$i]['image'])){
					$seller_detail[] = array(
						'code' 		=> 	'seller',
						'key' 		=>	'seller_promo_box_'. $i. '_image',
						'value' 	=>  $this->request->post['promo_box'][$i]['image']
					);
				}
				if(isset($this->request->post['promo_box'][$i]['name'])){
					$seller_detail[] = array(
						'code' 		=> 	'seller',
						'key' 		=>	'seller_promo_box_'. $i. '_name',
						'value' 	=>  $this->request->post['promo_box'][$i]['name']
					);
				}
				if(isset($this->request->post['promo_box'][$i]['link'])){
					$seller_detail[] = array(
						'code' 		=> 	'seller',
						'key' 		=>	'seller_promo_box_'. $i. '_link',
						'value' 	=>  $this->request->post['promo_box'][$i]['link']
					);
				}
				if(isset($this->request->post['promo_box'][$i]['show'])){
					$seller_detail[] = array(
						'code' 		=> 	'seller',
						'key' 		=>	'seller_promo_box_'. $i. '_show',
						'value' 	=>  $this->request->post['promo_box'][$i]['show']
					);
				}else{
					$seller_detail[] = array(
						'code' 		=> 	'seller',
						'key' 		=>	'seller_promo_box_'. $i. '_show',
						'value' 	=>  0
					);
				}
			}
		}
		$this->model_catalog_information->updateSellerWebsitesDetail($store_id, $seller_detail);
		$this->response->redirect($this->url->link('seller/website-settings', '#promotional_boxes', 'SSL'));
	}
	public function hide_promo(){
		if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}
		$seller_detail = array();

		$this->load->model('catalog/information');
		if(isset($this->request->post['show_hide_promo_select'])){
			$seller_detail[] = array(
				'code' 		=> 	'seller',
				'key' 		=>	'seller_promo_box',
				'value' 	=>  $this->request->post['show_hide_promo_select']
			);
		}
		$this->model_catalog_information->updateSellerWebsitesDetail($store_id, $seller_detail);
	}
	public function updateSellerFooter(){
		if(isset($this->session->data['seller_store_id'])) { 
    		$store_id = $this->session->data['seller_store_id'];
		}
		$seller_detail = array();
		
		$this->load->model('catalog/information');
    	if(isset($this->request->post['payment_banner']) && !empty($this->request->post['payment_banner'])){
    		if(!empty($this->request->post['payment_banner'])){
    			$seller_detail[] = array(
					'code' 		=> 	'seller',
    				'key' 		=>	'payment_banner',
    				'value' 	=>	$this->request->post['payment_banner']
    			);
    		}
      	}
    	if(isset($this->request->post['shipping_banner']) && !empty($this->request->post['shipping_banner'])){
    		if(!empty($this->request->post['shipping_banner'])){
				$seller_detail[] = array(
					'code' 		=> 	'seller',
					'key'		=>	'shipping_banner',
					'value' 	=> 	$this->request->post['shipping_banner']
    			);
			}	
      	}
      	if(isset($this->request->post['copyright']) && !empty($this->request->post['copyright'])){

				$seller_detail[] = array(  					
	  				'code' 		=> 	'seller',
					'key' 		=>	'seller_copyright',
					'value' 	=>  $this->request->post['copyright']
    			);

		}
      	$this->model_catalog_information->updateSellerWebsitesDetail($store_id, $seller_detail);   
	}
	
}