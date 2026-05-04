<?php
class ControllerReactProduct extends Controller {

	public function index() {
        
    $this->load->model('catalog/product');
    $this->load->model('catalog/category');        
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
        $this->load->model('catalog/product');
         $this->load->model('tool/image');

        $data['request_uri'] = $_SERVER['REQUEST_URI'];
        if ($this->request->server['HTTPS']) {
            $data['in_store'] = 'https://'.INDIA_STORE_HOST;
            $data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
        } else {
            $data['in_store'] = 'http://'.INDIA_STORE_HOST;
            $data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
        }

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['title'] = $this->document->getTitle();
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
                
                
         $product_language = array();
		$header_language = array();
		$footer_language = array();
		$login_language = array();
		//$this->load->autoLoadLanguage('product/product', $product_language);
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$language = $data;
 
		$data['product_language'] = json_encode($product_language);
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
        
        //print_r($_SESSION); die;
        //print_r($_SESSION['customer_id']); die;
        $customer_data = array();
        if(isset($_SESSION['customer_id']) && intval($_SESSION['customer_id']) > 0){
            $customer_id = $_SESSION['customer_id']; 
            $sql = "SELECT firstname,telephone,email from oc_customer where customer_id = ".$customer_id; 
            $row = $this->db->query($sql)->rows[0];
            
            $customer_data['customer_id'] = $customer_id;
            $customer_data['customer_name'] = $row['firstname'];
            $customer_data['telephone'] = $row['telephone'];
            $customer_data['email'] = $row['email'];
            
        }
        $product_id = $this->request->get['product_id'];
        $data['product_id'] = $product_id;
        $data['customer_data'] = $customer_data; 


         
     $category_id = $this->model_catalog_product->getCategory($product_id);
     if($category_id > 0)
      {
         if(!isset($_COOKIE['preferences']) && empty($_COOKIE['preferences']))
         {
            $category_info = $this->model_catalog_category->getParentCategory($category_id);
            $menu_info = $this->model_catalog_category->getMenuCategory($category_info['category_id']);
            if(isset($menu_info['parent_id']))
             {
               if($menu_info['parent_id'] > 0)
              {
                $parent_menu = $this->model_catalog_category->getParentMenu($menu_info['parent_id']);
                if(isset($parent_menu['value'])) { setcookie('preferences', $parent_menu['value'], time() + (86400 * 7), "/"); }
              }
              else
              {
                setcookie('preferences', $menu_info['value'], time() + (86400 * 7), "/");
              }
            }

         }
      }  
 



         $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

        if ($_SERVER['HTTPS']) {
           $static_content_url =  STATIC_CONTENT_URL_SSL;
         } else {
            $static_content_url =  STATIC_CONTENT_URL ;
         }

        $this->load->model('tool/image');

        $original_image_result = $this->model_catalog_product->getProductOriginalImages($product_id);
        $original_image = $original_image_result['image'];
        $extension  = pathinfo($original_image, PATHINFO_EXTENSION);

        //if (file_exists(DIR_IMAGE . $original_image)) {
        //    list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $original_image);
        //}
        $dimensions = unserialize($original_image_result['image_dimensions']);
        if(!empty($dimensions['width']))
            $width_orig = $dimensions['width'];
        if(!empty($dimensions['height']))
            $height_orig = $dimensions['height'];

        if (!empty($width_orig) && !empty($height_orig) && ($width_orig / $height_orig) > 1)
        {
            //$data['product_image'] = $static_content_url.'cache/'.utf8_substr($original_image, 0, utf8_strrpos($original_image, '.')) . '-' . $this->config->get('config_image_popup_height') . 'x' . $this->config->get('config_image_popup_width') . '.' . $extension;
            $data['product_image'] = $this->model_tool_image->resize($original_image,$this->config->get('config_image_popup_height'),$this->config->get('config_image_popup_width'));
        }
        else
        {
            //$data['product_image'] = $static_content_url.'cache/'.utf8_substr($original_image, 0, utf8_strrpos($original_image, '.')) . '-' . $this->config->get('config_image_popup_width') . 'x' . $this->config->get('config_image_popup_height') . '.' . $extension;
            $data['product_image'] = $this->model_tool_image->resize($original_image,$this->config->get('config_image_popup_width'),$this->config->get('config_image_popup_height'));
        }

        $this->load->model('setting/store');
        $this->model_setting_store->getInternationalSwitch();
        $alertSwitchStore = $this->model_setting_store->alertOnRedirection;
        if($alertSwitchStore){

            $data_popup['logo'] = STATIC_CONTENT_URL_SSL."mobile_logo.png";
            $data_popup['name'] = $this->config->get('config_name');

            $data['redirect_popup'] = $this->load->view($this->config->get('config_template') . '/template/common/domain_redirect_popup.tpl', $data_popup);
            $data['show_redirect_popup'] = $alertSwitchStore;
        }

        $data['detail_url'] = $this->url->link('product/product', 'product_id=' . $product_id, 'SSL');

        if (isset($this->session->data['custom_store']) && $this->session->data['custom_store'] != '') {
             $data['custom_store_val'] = $this->session->data['custom_store'];
           } else {
             $data['custom_store_val'] = 'set';
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
           
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/react/product_detail.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/react/product_detail.tpl', $data));
		} else {
				$this->response->setOutput($this->load->view('default/template/react/product_detail.tpl', $data));
		}

	}

}
