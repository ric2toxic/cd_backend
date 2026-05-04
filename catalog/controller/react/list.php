<?php
class ControllerReactList extends Controller {

	public function index() {

    $this->load->model('catalog/category');
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
    $this->load->model('tool/image');

    $data['request_uri'] = $_SERVER['REQUEST_URI'];
    if ($this->request->server['HTTPS']) {
      $data['in_store'] = 'https://'.INDIA_STORE_HOST;
      $data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
      $data['list_url'] = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    } else {
      $data['in_store'] = 'http://'.INDIA_STORE_HOST;
      $data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
      $data['list_url'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

   if (isset($this->request->get['hide_price']) && !empty($this->request->get['hide_price'])) {
      $data['hide_price'] = 1;
    }
   else
   {
     $data['hide_price'] = 0;
   } 

		if (isset($this->request->get['path']) && !empty($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);
			$category_id = (int)array_pop($parts);

			/*foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

    
				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url, 'SSL')
					);
          $category_info['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
				}
			}*/
		 } else {
			$category_id = 0;
		 }
  


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
                setcookie('preferences', $parent_menu['value'], time() + (86400 * 7), "/");
              }
              else
              {
                setcookie('preferences', $menu_info['value'], time() + (86400 * 7), "/");
              }
            }

         }
      }  


/*  if($category_id > 0)
  {
       if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'], 1) == true)
       {
           $menu_info = $this->model_catalog_category->getMenuCategory($category_id, 1);
          if(isset($menu_info['parent_id']) && $menu_info['parent_id'] == 0)
              {
                if(isset($_COOKIE['preferences']) && $_COOKIE['preferences'] != $menu_info['value'])
                {
                  setcookie('preferences', $menu_info['value'], time() + (86400 * 7), "/");
                  $this->response->redirect($this->url->link('common/home','','SSL'));
                }
              }
       }
       else
       {

           $menu_info = $this->model_catalog_category->getMenuCategory($category_id);
           if(isset($menu_info['parent_id']))
           {
               if($menu_info['parent_id'] > 0)
              {
                $parent_menu = $this->model_catalog_category->getParentMenu($menu_info['parent_id']);
                setcookie('preferences', $parent_menu['value'], time() + (86400 * 7), "/");
              }
              else
              {
                setcookie('preferences', $menu_info['value'], time() + (86400 * 7), "/");
              }
          }
       }
   }*/


        // Check if any price range has been specified by the user
        if (isset($this->request->get['price_filter']) && $this->request->get['price_filter'] != 'all') {
        $price_filter = $this->request->get['price_filter'];
        } else {
        $price_filter = '';
        }
        
        $category_info = $this->model_catalog_category->getCategory($category_id);

        if ($category_id == 79 ) {
            $data['bandhani_alert'] = $this->language->get('alert_bandhani_dispatch');
        } else {
            $data['bandhani_alert'] = false;
        }
        if ( $category_id == 87 ) {
            $data['alert_thaan_dispatch'] = $this->language->get('alert_thaan_dispatch');
        } else {
            $data['alert_thaan_dispatch'] = false;
        }

        $store_id = $this->config->get('config_store_id'); 
        

        if ($category_info) {
        
        $category_info['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8'); 

        $category_info['short_description'] = html_entity_decode($category_info['short_description'], ENT_QUOTES, 'UTF-8'); 

        if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID)
        {
          $store_data = $this->model_catalog_category->getStoreMetaData($category_id, $this->config->get('config_store_id'));
          if(isset($store_data[0]['description']))
          {
           $category_info['description'] = html_entity_decode($store_data[0]['description'], ENT_QUOTES, 'UTF-8');
          }
          if(isset($store_data[0]['short_description']))
          {
           $category_info['short_description'] = html_entity_decode($store_data[0]['short_description'], ENT_QUOTES, 'UTF-8');
          }
        }


         $can_url=$this->url->link("product/category","path=".$this->request->get['path'], 'SSL');

        if(isset($this->session->data['is_custom']) && $this->session->data['is_custom'] == 1)
         {
            $can_url=$this->url->custom_link($this->request->get['_route_'],'SSL');
          } 
          else {
            $can_url=$this->url->link("product/category","path=".$this->request->get['path'], 'SSL');
          }
  
         $this->document->addLink($can_url,"canonical", 'SSL');
          if($store_id != 0){
            	$StoreMetaData = $this->model_catalog_category->getStoreMetaData($category_id,$store_id);

            	if(!empty($StoreMetaData)){
            		$this->document->setTitle($StoreMetaData[0]['meta_title']);
					$this->document->setDescription($StoreMetaData[0]['meta_description']);
					$this->document->setKeywords($StoreMetaData[0]['meta_keywords']);
            	}
            }else{
            	$this->document->setTitle($category_info['meta_title']);
				$this->document->setDescription($category_info['meta_description']);
				$this->document->setKeywords($category_info['meta_keyword']);
            }
        }

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		 $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));
                
    $data['social_meta_tags'] = $this->document->getSocialMetaTags();
    $data['base'] = $server;
		
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		


		$header_language = array();
		$footer_language = array();
		$login_language = array();
		$category_language = array();
		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$this->load->autoLoadLanguage('product/category', $category_language);
    $this->load->autoLoadLanguage('product/product',  $category_language);

    $data['lang']      = $header_language['code'];
    $data['direction'] = $header_language['direction'];
        
        
        //set default
        $data['custom_title'] = '';
    
        //@author : Amarat
        //Description : Change meta data for custom url 
        
        //Add condition for meta_data
        if(isset($this->session->data['is_custom']))
        {
           $data['is_custom'] = $this->session->data['is_custom'];
        }
        else
        {
          $data['is_custom'] = '';
        }
        
        
        if(isset($this->session->data['is_custom']) && $this->session->data['is_custom'] == 1){ 
            $this->load->model('catalog/custom_url'); 
            $url_alias_id = $this->session->data['url_alias_id']; 
            $custom_url_data = $this->model_catalog_custom_url->getCustomUrlInfo($url_alias_id);
            //set custom_url meta data
            $data['title'] = $custom_url_data['meta_title'];
            $data['description'] = $custom_url_data['meta_description'];
            $data['keywords'] = $custom_url_data['keyword'];
            
            //set name, description for custom URL
            $data['custom_title'] = $custom_url_data['title'];
            $category_info['description'] = html_entity_decode($custom_url_data['description'], ENT_QUOTES, 'UTF-8');
            $category_info['short_description'] = html_entity_decode($custom_url_data['short_description'], ENT_QUOTES, 'UTF-8');
        }else{
            //default meta data
            $data['title'] = $this->document->getTitle();
            $data['description'] = $this->document->getDescription();
            $data['keywords'] = $this->document->getKeywords();
            
        }
        $data['category_info'] = json_encode($category_info);
    
        $data['filters'] = array();
        $data['price_filter'] = '';
        $data['options'] = array();
        $data['rating_filter'] = '';

        
        //echo $this->session->data['is_filter']; die;
        
        //@author : Amarat
        //Description : Change filter data for custom filter url 
        
        //Add condition for filter
        if(isset($this->session->data['is_custom'])){ 

            $this->load->model('catalog/custom_url'); 
            $url_alias_id = $this->session->data['url_alias_id']; 
            $custom_url_data = $this->model_catalog_custom_url->getCustomUrlInfo($url_alias_id);
            $custom_url = $custom_url_data['query'];
            
            //set default filter data
            //$data['price_filter'] = '';
            //$data['rating_filter'] = '';
            //$data['options'] = '';
            //$data['filters'] = '';
            $sort = 'sort_order';
            $order = 'ASC';
            $data['path'] = '';
            $search = '';
            $data['stock_filter'] = 0;
            $data['search_sale'] = 0;
            
            if (isset($this->request->get['path'])) {
                $data['path'] = $this->request->get['path'];
            } else {
                $data['path'] = '';
            }
						
						if (isset($this->request->get['location'])) {
						 $location = $this->request->get['location'];
						} else {
							$location = '';
						} 

            
             if (isset($this->request->get['csv_req'])) {
              $csv_req = $this->request->get['csv_req'];
             } else {
               $csv_req = 0;
             }   
            
            if( isset($this->session->data['is_filter'])) {  
                
                $filters_data = explode('#!', $custom_url);
                //echo "<pre>"; print_r($filters_data); //die;
                $filters = explode("&", str_replace('amp;', '', $filters_data[1]));
                //echo "<pre>"; print_r($filters); //die;

                $data1 = array();
                $filtr = array();
                for($i = 0; $i < count($filters); $i++){
                    $filtr = explode('=', $filters[$i]);

                    //echo $i . "<pre>";  print_r($filtr);
                    //echo "<br/>";

                    if($filtr['0'] == 'filter'){ 
                        $data['filters'] = explode(",", $filtr['1']);
                    }
                    elseif ($filtr['0'] == 'price_filter') {
                        $data['price_filter'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'order'){ 
                        $order = $filtr['1'];
                    }
                    if($filtr['0'] == 'option') {
                        $data['options'] = explode(",", $filtr['1']);
                    }
                    if( $filtr['0'] == 'rating_filter') {
                        $data['rating_filter'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'sort'){ 
                        $sort = $filtr['1']; 
                    }
                    if($filtr['0'] == 'search'){ 
                        $search = $filtr['1'];
                    }
                    if($filtr['0'] == 'stock_filter'){
                        if($filtr['1'] != 'undefined' && $filtr['1'] != ''){ 
                            $data['stock_filter'] = $filtr['1'];
                        }
                    }
                    if($filtr['0'] == 'clearance_sale'){ 
                        if($filtr['1'] != 'undefined' && $filtr['1'] != ''){ 
                            $data['search_sale'] = $filtr['1'];
                        }
                    }

                } 

            }else if( isset($this->session->data['is_search'])) {  
                $search_data = explode("&", str_replace('amp;', '', $custom_url));
                for($i = 0; $i < count($search_data); $i++){
                    $search = explode('=', $search_data[$i]);
                    for($j = 0; $j < count($search['1']); $j++){
                        if($search['0'] == 'search'){ 
                            $search = $search['1'];  
                        }
                    }
                }
            }else if( isset($this->session->data['is_search_with_filter'])) {
                
                //filter data for search with filters case
                $filters_data = explode('#!', $custom_url);
                //echo "<pre>"; print_r($filters_data); //die;
                $filters = explode("&", str_replace('amp;', '', $filters_data[1]));
                //echo "<pre>"; print_r($filters); //die;

                $data1 = array();
                $filtr = array();
                for($i = 0; $i < count($filters); $i++){
                    $filtr = explode('=', $filters[$i]);

                    //echo $i . "<pre>";  print_r($filtr);
                    //echo "<br/>";

                    if($filtr['0'] == 'filter'){ 
                        $data['filters'] = explode(",", $filtr['1']);
                    }
                    elseif ($filtr['0'] == 'price_filter') {
                        $data['price_filter'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'order'){ 
                        $order = $filtr['1'];
                    }
                    if($filtr['0'] == 'option') {
                        $data['options'] = explode(",", $filtr['1']);
                    }
                    if( $filtr['0'] == 'rating_filter') {
                        $data['rating_filter'] = $filtr['1'];
                    }
                    if($filtr['0'] == 'sort'){ 
                        $sort = $filtr['1']; 
                    }
                    if($filtr['0'] == 'search'){ 
                        $search = $filtr['1'];
                    }
                    if($filtr['0'] == 'stock_filter'){
                        if($filtr['1'] != 'undefined' && $filtr['1'] != ''){ 
                            $data['stock_filter'] = $filtr['1'];
                        }
                    }
                    if($filtr['0'] == 'clearance_sale'){ 
                        if($filtr['1'] != 'undefined' && $filtr['1'] != ''){ 
                            $data['search_sale'] = $filtr['1'];
                        }
                    }

                }
                
                // search data for search_with_filter case
                if (strpos($custom_url, '#!') !== false) {
                    $pos = strpos($custom_url, '#!');
                    $custom_url = substr($custom_url, 0, $pos);
                }
                $search_data = explode("&", str_replace('amp;', '', $custom_url));
                for($i = 0; $i < count($search_data); $i++){
                    $search = explode('=', $search_data[$i]);
                    for($j = 0; $j < count($search['1']); $j++){
                        if($search['0'] == 'search'){ 
                            $search = $search['1'];  
                        }
                    }
                }
                
                
            }
            
            
        }else{ 
            
            //default filter 
            if (isset($this->request->get['filter'])) {
               $filters = explode(",", $this->request->get['filter']);
               $data['filters'] = $filters;
            }

            if (isset($this->request->get['price_filter'])) {
             $price_filter =  $this->request->get['price_filter'];
             $data['price_filter'] = $price_filter;
            }

            if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
             } else {
             $order = 'ASC';
            }

            if (isset($this->request->get['option'])) {
              $options = explode(",", $this->request->get['option']);
              $data['options'] =  $options;
            }
            if (isset($this->request->get['rating_filter'])) {
             $rating_filter =  $this->request->get['rating_filter'];
             $data['rating_filter'] = $rating_filter;
            }

            if (isset($this->request->get['sort']) && (!empty($this->request->get['sort'])) ) {
              $sort = $this->request->get['sort'];
             } else {
              $sort = 'sort_order';
             }

             if (isset($this->request->get['search'])) {
             $search = str_replace('`', '&', $this->request->get['search']);
             } else {
               $search = '';
             }

             if (isset($this->request->get['csv_req'])) {
              $csv_req = $this->request->get['csv_req'];
             } else {
               $csv_req = 0;
             }
						 if (isset($this->request->get['location'])) {
              $location = $this->request->get['location'];
             } else {
               $location = '';
             }
						 
            //IN stock/ out of stock filter
            if (isset($this->request->get['stock_filter'])) {
                $show_out_of_stock = $this->request->get['stock_filter'];
            } else {
                $show_out_of_stock = 0;
            }
            $data['stock_filter'] = $show_out_of_stock;

            if (isset($this->request->get['clearance_sale'])) {
              $data['search_sale'] = $this->request->get['clearance_sale'];
            } else {
              $data['search_sale'] = 0;
            }
            
            if (isset($this->request->get['last_filter_action'])) {
              $data['last_filter_action'] = $this->request->get['last_filter_action'];
            } else {
              $data['last_filter_action'] = ''; 
            }
            
            //echo "<pre>"; print_r($this->request->get);  die;
            //echo $this->request->get['path']; die;
            if (isset($this->request->get['path'])) {
              $data['path'] = $this->request->get['path'];
            } else {
              $data['path'] = '';
            }
        } 
        
        
        
        
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
        $data['customer_data'] = $customer_data;  
    
			if (isset($this->request->get['store_product'])) {
	 			 $store_product = (int)$this->request->get['store_product'];
	 		} else {
	 			 $store_product = 0;
	 		}
			if (isset($this->request->get['purchase_days'])) {
				 $purchase_days = (int)$this->request->get['purchase_days'];
			} else {
				 $purchase_days = 0;
			}
			if (isset($this->request->get['store_code'])) {
	 			 $store_code = $this->request->get['store_code'];
	 		} else {
	 			 $store_code = '';
	 		}
        
		$data['header_language']   = json_encode($header_language);
		$data['footer_language']   = json_encode($footer_language);
		$data['login_language']    = json_encode($login_language);
		$data['category_language'] = json_encode($category_language);
		$data['price_filter']      = json_encode($data['price_filter']);
		$data['rating_filter']     = json_encode($data['rating_filter']);
		$data['options']           = json_encode($data['options']);
		$data['filters']           = json_encode($data['filters']);
		$data['sorts']             = json_encode($sort);
		$data['order']             = json_encode($order);
		$data['path']              = $data['path'];
		$data['search']            = trim(urldecode($search));
    $data['csv_req']           = $csv_req;
    $data['location']           = $location;
		$data['store_product']      = $store_product;
    $data['purchase_days']           = $purchase_days;
		$data['store_code']       = $store_code;

		
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
    
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/react/list.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/react/list.tpl', $data));
		} else {
				$this->response->setOutput($this->load->view('default/template/react/list.tpl', $data));
		}

	}

}
