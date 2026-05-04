<?php
class ControllerAccountWishList extends Controller {
	public function index() {

		$this->load->model('tool/image'); 
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/wishlist', '', 'SSL');

			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}


		$data = array();

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

        $this->load->language('account/wishlist');
        
        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));



        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

		
		$this->load->autoLoadLanguage('product/product', $data);

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['remove'])) {
            $product_id = $this->request->get['remove'];
            $this->customer->deleteProductFromWishlist($product_id);
            $product_hotness = new ProductHotness($this->db);
            $product_hotness->updateHotness("remove-from-wishlist",$product_id);

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->response->redirect($this->url->link('account/wishlist'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_my_orders'),
			'href' => $this->url->link('account/order', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/wishlist')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_empty'] = $this->language->get('text_empty');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_stock'] = $this->language->get('column_stock');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_action'] = $this->language->get('column_action');
		$data['text_cod_available'] = $this->language->get('text_cod_available');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}


        $customer_wishlist = array_column($this->customer->getWishlistItems()->rows,'product_id');

        $data['products'] = array();
		foreach ($customer_wishlist as $key => $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
					$img_width  = $this->config->get('config_image_product_width');
					$img_height = $this->config->get('config_image_product_height');
					//$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
					/*--- commented on (04-01-2016) And image get same as well as product_list --*/
				} else {
					$image = false;
					$img_width = '';
					$img_height= '';
				}

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}

                $seller_tax_factor = 1.0 + ( (float)$product_info['seller_tax'] / 100.0 );
                $commission_factor = 1.0 + ( (float)$product_info['commission'] / 100.0 );

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $unit_price =  ceil($commission_factor * (float)($product_info['price']) / $seller_tax_factor);
					$price = $this->currency->format($this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
                    $unit_special_price = ceil($commission_factor * (float)($product_info['special']) / $seller_tax_factor);
					$special = $this->currency->format($this->tax->calculate($unit_special_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
				} else {
					$special = false;
				}
				$url_related = '';
				if (isset($this->request->get['popup'])) {
                    $url_related .= '&popup=true';
                }

                $product_options =  $this->model_catalog_product->getProductOptions($product_info['product_id']);

                                if($product_info['base_unit'] != ''){
                                    $price_with_unit =  $price . ' / ' . $product_info['base_unit'];
                                    
                                    $set_description = '1 ' . $product_info['super_unit'] . " = " . $product_info['piece_in_set'] . ' ' .$product_info['base_unit'] ;
                                    $set_description .=  ', ' . $product_info['set_description'] ;
                                }else{
                                    $price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
                                    $set_description = $product_info['set_description'];
                                }
                                    
                
				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'is_sor_enabled' => $product_info['is_sor_enabled'],
                    'sor_enabled_text' => $product_info['sor_enabled_text'],
                    'sor_enabled_detail_text' => $product_info['sor_enabled_detail_text'],
					'stock'      => $stock,
					'price'      => $price_with_unit,
					'set_description' => $set_description,
					'piece_in_set'      => $product_info['piece_in_set'],
					'special'    => $special,
                    'href' => $this->url->link('product/product', 'product_id=' . $product_info['product_id'] . $url_related, 'SSL'),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id']),
					'rating'	 => $product_info['rating'],
					'cod_available'=> $product_info['cod_available'],
					'img_width' => $img_width,
					'img_height' =>  $img_height,
					'row_data' => $product_info,
					'minimum' => $product_info['minimum'],
					'quantity' => $product_info['quantity'],
					'options' => $product_options,
					'margin_percentage' => !empty($product_info['margin_percentage']) ? $product_info['margin_percentage'] : ''
				);
                                
                                //echo "<pre>"; print_r($data['products']); die;
			}
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

        $data['continue'] = $this->url->link('account/account', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		if(CONFIG_IS_MOBILE == 1)
        {	
	    	$data['footer'] = $this->load->controller('common/footer');
		    $data['header'] = $this->load->controller('common/header');
		 }    

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/wishlist.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/wishlist.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/wishlist.tpl', $data));
		}
	}

	public function add() {

		$this->load->language('account/wishlist');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {

			if ( !$this->customer->checkExistingWishlistItem($product_id) ) {

                $product_hotness = new ProductHotness($this->db);
                $product_hotness->updateHotness("add-to-wishList",$product_id);

                $this->customer->processWishlist($product_id);

				if ($this->customer->isLogged()) {
					$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
				} else {
					$json['info'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
				}
			} else {
				$json['info'] = sprintf($this->language->get('text_exists'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
			}

            $total = $this->customer->getTotalWishlists();
			$json['total'] = sprintf($this->language->get('text_wishlist'), $total);
			$json['count'] = $total;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
