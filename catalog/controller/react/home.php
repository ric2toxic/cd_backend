<?php
class ControllerReactHome extends Controller {

	public function index() 
	{
    $this->load->model('tool/image');
		$this->document->setTitle($this->config->get('config_meta_title'));
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

/*		$preferences = 0;
		if (isset($this->request->get['preferences'])) {
		  $preferences = $this->request->get['preferences'];
		}

		if($preferences > 0)
         {
            $this->load->model('catalog/category');	
           $preferences_info = $this->model_catalog_category->getMenuCategory($preferences);
           if(isset($preferences_info['parent_id']) && $preferences_info['parent_id'] == 0)
              {
                  setcookie('preferences', $preferences_info['value'], time() + (86400 * 7), "/");
              }
           }*/


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

		  $home_language = array();
		  $header_language = array();
		  $footer_language = array();
		  $login_language = array();
		  $this->load->autoLoadLanguage('common/language', $home_language);
		  $this->load->autoLoadLanguage('common/header', $header_language);
		  $this->load->autoLoadLanguage('common/footer', $footer_language);
		  $this->load->autoLoadLanguage('account/login', $login_language);
		  $language = $data;
 
       
		  $data['home_language'] = json_encode($home_language);
		  $data['header_language'] = json_encode($header_language);
		  $data['footer_language'] = json_encode($footer_language);
                  $data['login_language'] = json_encode($login_language);
		  $data['lang']      = $header_language['code'];
		  $data['direction'] = $header_language['direction'];

		  if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
          {
       	    $data['international_store'] = 1;
          }
          else
          {
            $data['international_store'] = 0;
          }

          $this->load->model('setting/store');
          $this->model_setting_store->getInternationalSwitch();
          $alertSwitchStore = $this->model_setting_store->alertOnRedirection;
          if($alertSwitchStore){

            $data_popup['logo'] = STATIC_CONTENT_URL_SSL . 'mobile_logo.png';
            $data_popup['name'] = $this->config->get('config_name');

            $data['redirect_popup'] = $this->load->view($this->config->get('config_template') . '/template/common/domain_redirect_popup.tpl', $data_popup);
            $data['show_redirect_popup'] = $alertSwitchStore;
           }


		   if(isset($this->session->data['otp_verify_success']))
           {
        	 $data['otp_verify_success'] = $this->session->data['otp_verify_success'];
         	 unset($this->session->data['otp_verify_success']);
           }
           else
           {
        	 $data['otp_verify_success'] = '';
           }


           $data['menus'] = array();
           if(!isset($_COOKIE['preferences']) && empty($_COOKIE['preferences']))
	       {
          	 $this->load->model('tool/image');
             $this->load->model('catalog/category');
          	 $menus = $this->model_catalog_category->menus('Desktop', 0, 'desktop-category');
             
              setcookie('preferences', $menus[0]['value'], time() + (86400 * 7), "/" );

             /*if($data['international_store'] == 1)
              {
                  setcookie('preferences', $menus[0]['value'], time() + (86400 * 7), "/" );
              }
             else
              {
                foreach ($menus as $menu) {
                 $category_image = $this->model_tool_image->resize($menu['image'], $menu['image_width'], $menu['image_height']);
                   $data['menus'][] = array(
                   'menu_id' => $menu['menu_id'],
                   'link_title' => $menu['link_title'],
                   'link_type' => $menu['link_type'],
                   'value' => $menu['value'],
                   'type' => $menu['type'],
                   'parent_id' => $menu['parent_id'],
                   'position' => $menu['position'],
                   'status' => $menu['status'],
                   'store_id' => $menu['store_id'],
                   'megamenu' => $menu['megamenu'],
                   'image' => $category_image
                   );
                  }   
              }*/
             
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
            
            
           
                //echo "<pre>"; var_dump($data['footer_language']); die;
            $data['preferences_menus'] = json_encode($data['menus']);
   
       
		     if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/react/home.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/react/home.tpl', $data));
		     } else {
				$this->response->setOutput($this->load->view('default/template/react/home.tpl', $data));
		     }
	    }

}
