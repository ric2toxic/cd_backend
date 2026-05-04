<?php
class ControllerCommonFooter extends Controller {
	public function index() {
        //$this->wsb->getMData();

        $data = array_merge($this->load->language('multiseller/multiseller'), isset($data) ? $data : array());

		$this->load->language('common/footer');
		$this->load->model('account/customer');
		$this->load->model('setting/setting');
		 $this->load->model('tool/image');

		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));
		$data['store'] = '';
		if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID){
			$data['store'] = 'co';
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['base'] = $server;

		$data['route'] = '';
		if(isset($this->request->get['route']) && $this->request->get['route'] != '') {
			$data['route'] = $this->request->get['route'];
		}

		$data['is_home'] = 0;
		if (!isset($this->request->get['route']) ) {
			$data['is_home'] = 1;
		}

		$data['popup'] = false;
		if (isset($this->request->get['popup'])) {
			$data['popup'] = $this->request->get['popup'];
		}

        /**
         * All Categories
         * */
             $data['category_id'] = array();
            $this->load->model('catalog/category');
            $categories = $this->model_catalog_category->getCategories(0);
            $i = 0;
            foreach ($categories as $category) {
                $data['category_id'][$i] = $category['category_id'];
                $i++;
            }
            //echo "<pre>"; print_r($data['category_id']); die;
            $data['category_ids'] = json_encode($data['category_id']);

		/** MObile number popup ** */
		if (isset($_COOKIE['mobile']) && $_COOKIE['mobile'] != '' ) {
			$this->request->get['mobile'] = $_COOKIE['mobile'];
			$data['pop'] = '0';
		}else if (isset($_COOKIE['user_country']) && $_COOKIE['user_country'] != 'IN' ) {
			$data['pop'] = '0';	
		}else{
			$data['pop'] = '1';
		}
		if(isset($_COOKIE['skip'])){
			$data['skip'] = 1;
		}else{
			$data['skip'] = 0;
		}
		
		//Hide/show mobile popup for a store
		$data['show_mobile_number_popup'] = 0;
		if($this->config->get('config_store_id') == 0){
			$data['show_mobile_number_popup'] = 1;
		}
		//$data['referral'] = $this->url->link('common/header/referralUrl', '', 'SSL');

		$data['text_mob_pop'] = $this->language->get('text_pop_mob_for_whatsapp');
		$data['text_information'] = $this->language->get('text_information');
		$data['text_service'] = $this->language->get('text_service');
		$data['text_extra'] = $this->language->get('text_extra');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_sitemap'] = $this->language->get('text_sitemap');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_voucher'] = $this->language->get('text_voucher');
		$data['text_affiliate'] = $this->language->get('text_affiliate');
		$data['text_special'] = $this->language->get('text_special');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_dropshipper'] = $this->language->get('text_dropshipper');
		$data['register'] = $this->url->link('account/register', 'static=register', 'SSL');
		$data['login'] = $this->url->link('account/login', 'static=login', 'SSL');
		$data['dropshipper'] = $this->url->link('account/dropshipper', 'static=dropshipper', 'SSL');
		$data['careers'] = $this->url->link('careers/careers', 'static=careers', 'SSL');
		$data['text_career'] = $this->language->get('text_career');
		$data['text_app_label'] = $this->language->get('text_app_label');
		$data['entry_app_mobile'] = $this->language->get('entry_app_mobile');
		$data['label_button_app'] = $this->language->get('label_button_app');
		$data['text_popular_tag_lable'] = $this->language->get('text_popular_tag_lable');
		$data['text_three_percent_discount'] = $this->language->get('text_three_percent_discount');
		$data['text_two_percent_discount'] = $this->language->get('text_two_percent_discount');

		$data['text_ad_heading'] = $this->language->get('text_ad_heading');
		$data['text_ad_list'] = $this->language->get('text_ad_list');
		$data['text_shop_now'] = $this->language->get('text_shop_now');
		$data['text_Affiliate'] = $this->language->get('text_Affiliate');
        $data['text_promobox_1'] = $this->language->get('text_promobox_1');
        $data['text_promobox_2'] = $this->language->get('text_promobox_2');
        $data['text_promobox_3'] = $this->language->get('text_promobox_3');
        $data['text_promobox_4'] = $this->language->get('text_promobox_4');
        $data['text_cashback'] = $this->language->get('text_two_cashback');
        $data['text_about_us'] = $this->language->get('text_about_us');
        $data['text_contact_us'] = $this->language->get('text_contact_us');
        $data['text_storelocator'] = $this->language->get('text_storelocator');
		$data['text_exclusive'] = $this->language->get('text_exclusive');
		$data['text_invalid_exclusive_voucher_code'] = $this->language->get('text_invalid_exclusive_voucher_code');



		$data['scripts'] = $this->document->getScripts();
		$data['additional_scripts'] = $this->document->getAdditionalScript();
		
		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact', '', 'SSL');
		$data['storelocator'] = $this->url->link('information/storelocator', '', 'SSL');

		$data['return'] = $this->url->link('account/return/add', '', 'SSL');
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		//$data['voucher'] = $this->url->link('account/voucher', '', 'SSL');
		$data['affiliate'] = $this->url->link('affiliate/account', '', 'SSL');
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$data['affiliate'] = $this->url->link('affiliate/login', '', 'SSL');
        $data['language'] = $this->load->controller('common/language');
		$this->load->language('common/language');
		$data['home_advertise_article'] = $this->language->get('home_advertise_article');

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		$data['logged'] = $this->customer->isLogged();

		if ($data['logged']) { 
			$customer_id = $this->customer->getId();
			$data['is_dropshipper'] = $this->model_account_customer->getisdropshipper($customer_id);
		//	print_r($data['is_dropshipper']); die;	
			$dropshipper = $data['is_dropshipper'];
		}
		
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			/*if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}*/

			$ip = $this->request->getIpAddress;

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->whosonline($ip, 
                                                 $this->customer->getId(), 
                                                 $this->config->get('config_store_id'), 
                                                 $url, 
                                                 $referer);
		}


		// get popular tag set in footer by vikas(09-07-2016)
		$this->load->model('module/popular_search');
		$getPopularTag = $this->model_module_popular_search->getPopularTags();
		$data['popular_tags'] = array();
		foreach($getPopularTag as $values){
			$data['popular_tags'][] = array(
				'text' => $values['popular_search'],
				'link' => $values['link'],
				'href' => $this->url->link('product/search','&search='.html_entity_decode(trim($values['popular_search'])),'SSL')
			);
		}

		/**
		 *Track traffic sources 
		 **/
		if(!empty($this->request->get['utm_source'])){
			if(isset($_COOKIE['session_id'])){
				
			}else{
				$this->load->model('tracking/tracking');
				$return = $this->model_tracking_tracking->add($this->request->get);
			}
			
		}
		
		$data['discount_shipping_popup'] = 1;
		if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID 
			|| $this->config->get('config_store_id') == SOR_STORE_ID 
			|| $data['route'] == 'product/product' 
			|| $data['route'] == 'product/category'
			|| $data['route'] == 'product/search'){
			$data['discount_shipping_popup'] = 0;
		}

        //redirect popup for india store in other country
        $this->load->model('setting/store');
        $this->model_setting_store->getInternationalSwitch();
        $alertSwitchStore = $this->model_setting_store->alertOnRedirection;
        if($alertSwitchStore){

            $data_popup['logo'] = $server . 'image/mobile_logo.png';
            $data_popup['name'] = $this->config->get('config_name');

            $data['redirect_popup'] = $this->load->view($this->config->get('config_template') . '/template/common/domain_redirect_popup.tpl', $data_popup);
            $data['show_redirect_popup'] = $alertSwitchStore;
        }

         
        if(!$this->customer->isLogged()) 
        {
          $data['login_popup'] = $this->load->controller('common/login_popup');
        }
        else
        {
          $data['login_popup'] = '';
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
        
        $data['footer_images']['fd']            =  $this->model_tool_image->getOriginalImage('fd.jpg');
        $data['footer_images']['gk']            =  $this->model_tool_image->getOriginalImage('gk.jpg');
        $data['footer_images']['dtdc']          =  $this->model_tool_image->getOriginalImage('dtdc.jpg');
        $data['footer_images']['blue_dart']     =  $this->model_tool_image->getOriginalImage('blue_dart.jpg');


		if($data['popup'] == true){
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer-popup.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/common/footer-popup.tpl', $data);
			} else {
				return $this->load->view('default/template/common/footer-popup.tpl', $data);
			}
		}else {
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				return $this->load->view($this->config->get('config_template') . '/template/common/footer_storefront.tpl', $data);
			} else {
				if(isset($this->request->get['amp']) && $this->request->get['amp'] == 1) {
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer-amp.tpl')) {
						return $this->load->view($this->config->get('config_template') . '/template/common/footer-amp.tpl', $data);
					} else {
						return $this->load->view('default/template/common/footer-amp.tpl', $data);
					}
				}else{

					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer.tpl')) {
						return $this->load->view($this->config->get('config_template') . '/template/common/footer.tpl', $data);
					} else {
						return $this->load->view('default/template/common/footer.tpl', $data);
					}
				}
			}
		}
	}

	/**
	 * SETTING cookie for discount popups
	 */

	public function setCookieForDiscount(){

		$cookie_name1 = "discount_ten";
		$cookie_value1 = "11";
		setcookie("$cookie_name1", $cookie_value1, time() + (86400 * 1), "/"); // 86400 = 1 day
	}

	public function setCookieForDiscount2(){

		$cookie_name2 = "discount_two";
		$cookie_value2 = "12";
		setcookie("$cookie_name2", $cookie_value2, time() + (86400 * 1), "/"); // 86400 = 1 day
	}

	public function setCookieForExclusive($exclusive_voucher_code){
		$cookie_name 	= "exclusive_voucher_code";
		$cookie_value 	= $exclusive_voucher_code;
		setcookie("$cookie_name", $cookie_value, time() + (86400 * 1), "/"); // 86400 = 1 day
	}
	public function checkExclusiveVoucherCode(){
		if(isset($this->request->post['exclusive_voucher_code'])) {
			$exclusive_voucher_code = $this->request->post['exclusive_voucher_code'];
			$store_code  = SalesStaff::checkStoreVoucher( $this->db, $exclusive_voucher_code );
			if($store_code) {
				if( !file_exists(DIR_LOGS . 'voucher_code_useages.csv') ) {
					$fp = fopen(DIR_LOGS . 'voucher_code_useages.csv', 'a');
					$data = array('Customer Id', 'Voucher Code', 'Store Code', 'Date Added', 'Device Type');
					fputcsv($fp, $data);
				}else{
					$fp = fopen(DIR_LOGS . 'voucher_code_useages.csv', 'a');
				}
				// Get customer is loged in or not 
				$logged = $this->customer->isLogged();
				if($logged){
					$customer_id = $this->customer->getId();
				}else{
					$customer_id = 0;
				}
				$data = array($customer_id, $exclusive_voucher_code, $store_code, Date('Y-m-d H:i:s'), 'web');
				fputcsv($fp, $data);
				$this->setCookieForExclusive($exclusive_voucher_code);
				$json['success'] = 1;
				$json['location'] = HTTPS_SERVER."exclusive";
			}else{
				$json['success'] = 0;
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	/**
	 * Get app link over sms by vikas(20-06-2016)
	 */
	public function getAppLink(){
		$this->load->language('mail/forgotten');
		$this->load->language('account/sms_templates');
		$json = array();
		$mobile_no = $this->request->post['mobile_no'];
		if(is_numeric($mobile_no)){
			if(strlen($mobile_no)==10){
			$message = sprintf($this->language->get('download_app_link'));
			$send_sms = new SMS($message,$mobile_no);
			sleep(5);
			$send_sms->sendMessage();
			$json['success'] = true;
			$json['success_msg'] = $this->language->get('success_msg');
			}else{
				$json['error'] = true;
				$json['error_msg'] = $this->language->get('error_valid_mobile_number');
			}
		}else{
			$json['error'] = true;
			$json['error_msg'] = $this->language->get('error_mobile_number');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
