<?php
class ControllerProductCategory extends Controller {
	public function index() { 

		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$this->load->model('setting/setting');
		$this->document->addScript('catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/touchspin/jquery.bootstrap-touchspin.css');

		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));
		$sellers = '';
		$data['detail_view'] = $this->language->get('detail_view');

        // Check which store we want to show the data for
		if (isset($this->session->data['custom_store']) && $this->session->data['custom_store'] != '') {
			$custom_store = $this->session->data['custom_store'];
			$data['custom_store'] = $this->session->data['custom_store'];
		} else {
			$data['custom_store'] = 'set';
			$custom_store ='set';
		}
		
		// to get the store30 products(i.e. store prodcts added before 30 days)
	 if (isset($this->request->get['store_product'])) {
		 $store_product = (int)$this->request->get['store_product'];
	 } else {
		 $store_product = 0;
	 }

	 if (!empty($this->request->get['purchase_days'])) {
		 $purchase_days = (int)$this->request->get['purchase_days'];
		 $date=date_create();
		 date_sub($date,date_interval_create_from_date_string($purchase_days . " days"));
		 $date_added_less_than = date_format($date,"Y-m-d\TH:i:s\Z");
	 } else {
		 $purchase_days = 0;
		 $date_added_less_than = '';
	 }

        // Check whether we want to show the alert for being on singles store
		if ($custom_store == 'single') {
			$data['single_store_alert'] = $this->language->get('alert_single_store');
		} else {
			$data['single_store_alert'] = '';
		}

        // Check if we are in the browse more mode OR the first page
		if (isset($this->request->get['ajax'])) {
			$data['ajax'] = $this->request->get['ajax'];
		} else {
			$data['ajax'] = false;
		}

        // Check if any filters are set in the page
		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

        // Check if any price range has been specified by the user
		if (isset($this->request->get['price_filter'])) {
			$price_filter = $this->request->get['price_filter'];
		} else {
			$price_filter = '';
		}

        // Check if any option has been selected by the user
		if (isset($this->request->get['option'])) {
			$option = $this->request->get['option'];
		} else {
			$option = '';
		}

        // Check if any rating has been selected by the user
		if (isset($this->request->get['rating_filter'])) {
			$rating_filter = $this->request->get['rating_filter'];
		} else {
			$rating_filter = null;
		}

        // If any sort specified by the user
		if (isset($this->request->get['sort']) && (!empty($this->request->get['sort'])) ) {
			$sort = $this->request->get['sort'];
		} else {
            // default behaviour is showing Handpicked designs.
			$sort = 'sort_order';
		}
		// echo $sort; die;

        // Handpicked ids, if we are in sort_order mode
		if (isset($this->request->get['handpicked_ids'])) {
			$handpicked_ids = $this->request->get['handpicked_ids'];
		} else {
			$handpicked_ids = '';
		}

        // product total for page 2 to 5
		if (isset($this->request->get['product_total'])) {
			$filter_product_total = $this->request->get['product_total'];
		} else {
			$filter_product_total = 0;
		}

        // product total for page 2 to 5
		if (isset($this->request->get['random_string'])) {
			$random_string = trim($this->request->get['random_string']);
		} else {
			$random_string = '';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'asc';
		}
		
		// for store 30 products
		if (!empty($store_product)) {
			$sort = 'date_added';
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', 'SSL')
		);

		if (isset($this->request->get['path'])) {
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

			foreach ($parts as $path_id) {
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
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);
        $data['category_id'] = (int)$category_id;

        // Hard code to check against Jaipuri Bandhani
        if ( $data['category_id'] == 79 ) {
            $data['bandhani_alert'] = $this->language->get('alert_bandhani_dispatch');
        } else {
            $data['bandhani_alert'] = false;
        }
        if ( $data['category_id'] == 87 ) {
            $data['alert_thaan_dispatch'] = $this->language->get('alert_thaan_dispatch');
        } else {
            $data['alert_thaan_dispatch'] = false;
        }
        $store_id = $this->config->get('config_store_id');
				
		if ($category_info || isset($this->request->get['location'])) {

            $can_url=$this->url->link("product/category","path=".$this->request->get['path'], 'SSL');
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

			$this->document->addLink($this->url->link('product/category', 'path=' . $this->request->get['path']), 'canonical', 'SSL');

			$data['heading_title'] = $category_info['name'];

			$data['text_refine'] = $this->language->get('text_refine');

			if (isset($store_info['config_seller_id'])) {
				$data['text_empty'] = "There are no products in this category";
			} else {
				$data['text_empty'] = $this->language->get('text_empty');
			}
			$data['text_previously_ordered'] = $this->language->get('text_previously_ordered');
			$data['text_empty_store'] = $this->language->get('text_empty_store');
			$data['text_quantity'] = $this->language->get('text_quantity');
			$data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$data['text_model'] = $this->language->get('text_model');
			$data['text_price'] = $this->language->get('text_price');
			$data['text_tax'] = $this->language->get('text_tax');
            $data['text_inc_tax'] = $this->language->get('text_inc_tax');
			$data['text_points'] = $this->language->get('text_points');
			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
			$data['text_sort'] = $this->language->get('text_sort');
			$data['text_limit'] = $this->language->get('text_limit');
            $data['text_moq_default'] = $this->language->get('text_moq_default');
            $data['text_moq_pre'] = $this->language->get('text_moq_pre');
            $data['text_moq_post'] = $this->language->get('text_moq_post');
            $data['text_per_piece'] = $this->language->get('text_per_piece');
            $data['text_per_set'] = $this->language->get('text_per_set');
            $data['text_plus_cst'] = $this->language->get('text_plus_cst');
			$data['text_inside_tooltip'] = $this->language->get('text_inside_tooltip');
			$data['text_checkbox_tooltip'] = $this->language->get('text_checkbox_tooltip');
			$data['text_inside_tooltip_mobile'] = $this->language->get('text_inside_tooltip_mobile');
			$data['text_cod_available'] = $this->language->get('text_cod_available');
			$data['text_available_after'] = $this->language->get('text_available_after');
			$data['text_days'] = $this->language->get('text_days');

			$data['column_mrp'] = $this->language->get('column_mrp');
			$data['column_our_price'] = $this->language->get('column_our_price');
			$data['column_save_money'] = $this->language->get('column_save_money');

			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['button_continue'] = $this->language->get('button_continue');
			$data['button_list'] = $this->language->get('button_list');
			$data['button_grid'] = $this->language->get('button_grid');

			$data['set_items'] = $this->language->get('text_set_items');
			$data['single_items'] = $this->language->get('text_single_items');
			$data['single_item'] = $this->language->get('text_single_item');

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'], 'SSL')
			);

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare', 'SSL');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['price_filter'])) {
				$url .= '&price_filter=' . $this->request->get['price_filter'];
			}
			if (isset($this->request->get['option'])) {
				$url .= '&option=' . $this->request->get['option'];
			}

			if (isset($this->request->get['rating_filter'])) {
				$url .= '&rating_filter=' . $this->request->get['rating_filter'];
			}

			$store_id = $this->config->get('config_store_id') ;
			if($store_id == 2){
				$single_store_order_limit = 2500;

				if((int)$this->config->get('config_limit') > 0){

					$single_store_order_limit = $this->config->get('config_limit');
				}

				//$data['single_store_alert'] = sprintf($this->language->get('alert_single_item_limit'), $this->currency->format($single_store_order_limit));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();
			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);

				$data['categories'][] = array(
					'name'  => $result['name'],
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url, 'SSL')
				);
			}

			$request_uri =  substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
			$data['qry_string'] = parse_url($request_uri, PHP_URL_QUERY);

			$path_only = parse_url($request_uri, PHP_URL_PATH);
			//dev url hook, remove dev/ as it is already in config_url
			if(strpos($path_only, 'staging/') !== false){
				$path_only = substr($path_only, 8, strlen($path_only));
			}
			
			if (isset($this->request->get['location'])) {
					$location = $this->request->get['location'];
			} else {
					$location = '';
			}
			//$data['url_path'] = $this->config->get('config_url'). $path_only;
            $data['url_path'] = $path_only;

			$data['products'] = array();
			
            
            //change filter data for custom_url
            if(isset($this->session->data['is_custom']) && $this->session->data['is_custom'] == 1){
                $custom_url_filter = $this->getCustomUrlFilters();
                
                $filter = $custom_url_filter['filters'];
                $price_filter = $custom_url_filter['price_filter'];
                $option = $custom_url_filter['options'];
                $rating_filter = $custom_url_filter['rating_filter'];
                $sort = $custom_url_filter['sort'];
                $order = $custom_url_filter['order'];
                $path = $custom_url_filter['path'];
                $search = $custom_url_filter['search'];
                //$stock_filter = $custom_url_filter['stock_filter'];
                //$search_sale = $custom_url_filter['search_sale'];
                
                //set heading_title, discription for custom url
                $data['heading_title'] = $custom_url_filter['title'];
                $data['description'] = html_entity_decode($custom_url_filter['description'], ENT_QUOTES, 'UTF-8');
            }            
                        
                        
            // Setting filters to get the products for
            $filter_data = array(
                'filter_category_id' => $category_id,
                'filter_filter'      => $filter,
                'option'      		 => $option,
                'price_filter'	 	 => $price_filter,
                'sort'               => $sort,
                'order'              => $order,
                'page'               => $page,
                'start'              => ($page - 1) * $limit,
                'limit'              => $limit,
                'seller'             => $sellers,
                'custom_store' 		 => $custom_store,
                'handpicked_ids'     => $handpicked_ids,
                'product_total'      => $filter_product_total,
                'random_string'      => $random_string,
				'is_facet'			 => 1,
				'location' => $location,
				'date_added_less_than' => $date_added_less_than,
				'store_product' => $store_product
			);

            // If a valid rating_filter is provided
            if ( !empty($rating_filter) and $rating_filter != 'all' ) {
                $filter_data['rating_filter'] = $rating_filter;
            }

            // Getting only instock products
			if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) { 
				$solr = new SolrProduct($this);
				$results_solr = $solr->getProductFromSolr($filter_data);
				$results = $results_solr['products'];
				$product_total = $results_solr['product_total'];
			} else {  
				$product_total = $this->model_catalog_product->getTotalInStockProducts($filter_data);

				$results = $this->model_catalog_product->getInStockProducts($filter_data);
			}

            // Setting handpicked params in GET
            $data['handpicked_ids'] = (!empty($results_solr['handpicked_ids'])) ? $results_solr['handpicked_ids'] : '';

            $data['product_total'] = $product_total;

            $data['random_string'] = (!empty($results_solr['random_string'])) ? $results_solr['random_string'] : '';


			foreach ($results as $result) {
				
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
					$image_width = $this->config->get('config_image_product_width');
					$image_height = $this->config->get('config_image_product_height');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
					$image_width = $this->config->get('config_image_product_width');
					$image_height = $this->config->get('config_image_product_height');
				}

                $seller_tax_factor = 1.0 + ( (float)$result['seller_tax'] / 100.0 );
                $commission_factor = 1.0 + ( (float)$result['commission'] / 100.0 );

                $piece_in_set = (int)$result['piece_in_set'] > 1 ? (int)$result['piece_in_set'] : 1;
                $unit_price =  ceil($commission_factor * (float)($result['price']) / $seller_tax_factor);

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
					$unformatted_price = $this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
				} else {
					$price = false;
					$unformatted_price = false;
				}

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price_per_set = $this->currency->format($this->tax->calculate($unit_price*$piece_in_set, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
                } else {
					$price_per_set = false;
				}

				if ((float)$result['special']) {
                    $unit_special_price = (float)$result['special']; // ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
					$special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
					$percent_discount = $this->model_catalog_product->calculateSpecialPriceValueInPercent($unit_special_price, $result['selling_price'], $result['tax_class_id']);
					$special_per_set = $this->currency->format($this->tax->calculate($unit_special_price*$piece_in_set, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
				} else {
					$percent_discount = false;
					$special = false;
					$special_per_set = false;
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

                if ( (int)$this->config->get('config_store_id') == 2 )
                    $is_single = $this->model_catalog_product->checkIfProductIsSingle($result['product_id']);
                else
                    $is_single = false;

				if ( $this->customer->isLogged() )
                    $previously_ordered = $this->model_catalog_product->checkPreviouslyOrdered($this->customer->getId(), $result['product_id']);
                else
                    $previously_ordered = false;

                //$url .= '&popup=true';

                /*------ start fill heart after user login ----*/
                $data['fill_heart'] = $this->customer->getWishlistIcon($result['product_id']);//echo $data['fill_heart']; die;
                /*------ END fill heart after user login ----*/
                
                //## Getting category link (in case of multiple categories, it picks only the first one in the list)
				$getCatLink = $this->model_catalog_product->getCategory($result['product_id']);
				
				//##
				$filters = $this->model_catalog_product->getProductFiltersData($result['product_id']);

                $data['products'][] = array(
					'product_id'  => $result['product_id'],
                    'thumb'       => $image,
					'name'        => $result['name'],
                    'set_description' => $result['set_description'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'unformatted_price' => $unformatted_price,
					'selling_price'  => $this->currency->format($result['selling_price']),
                    'price_per_set' => $price_per_set,
                    'piece_in_set'=> $piece_in_set,
					'special'     => $special,
					'percent_discount' => $percent_discount,
                    'special_per_set'=> $special_per_set,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'text_in_stock'=>sprintf($this->language->get('text_in_stock'), $result['quantity']),
                    'items_in_stock'=>sprintf($this->language->get('items_in_stock'), $result['quantity']),
					'text_out_of_stock'=>$this->language->get('text_out_of_stock'),
					'quantity'=>(int)$result['quantity'],
					'stock_status'=>$result['stock_status'],
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url, 'SSL'),
                    'is_single' => $result['is_single'],
					'product_options' => $this->model_catalog_product->getProductOptions($result['product_id']),
					'previously_ordered'=> $previously_ordered,
					'cod_available'	=> $result['cod_available'],
                    'fill_heart' => $data['fill_heart'],
					'image_width' => $image_width,
					'image_height' => $image_height,
					'mrp' => $result['mrp'],
					'format_mrp' => $result['format_mrp'],
					'saving_money' => $result['saving_money'] ,
					'exp_dispatch_date' => $result['exp_dispatch_days'],
					'margin_percentage' => !empty($result['margin_percentage']) ? $result['margin_percentage'] : '',
					'row_data' => $result,
                    'cart_tracking_id_for_ga' => 'wsb-addtocart-from-list-'.strtolower($result['model']),
                    'detail_popup_tracking_id_for_ga' => 'wsb-detailpopup-from-list-'.strtolower($result['model']),
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}
			if (isset($this->request->get['price_filter'])) {
				$url .= '&price_filter=' . $this->request->get['price_filter'];
			}
			if (isset($this->request->get['option'])) {
				$url .= '&option=' . $this->request->get['option'];
			}

			if (isset($this->request->get['rating_filter'])) {
				$url .= '&rating_filter=' . $this->request->get['rating_filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.rand',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url, 'SSL'),
				'query_string'  => ' ',
				'selected'      => ($sort == 'sort_order') ? 'active':''
			);

            $data['sorts'][] = array(
				'text'  => $this->language->get('text_most_recent'),
				'value' => 'p.date_added-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.date_added&order=DESC' . $url, 'SSL'),
				'query_string'  => 'p.date_added&order=DESC', // set all query string are in sorting product and replace to href in filter.tpl and category_ajax.tpl
				'selected'      => ($sort == 'p.date_added') ? 'active':''
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.selling_price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.selling_price&order=ASC' . $url, 'SSL'),
				'query_string'  => 'p.selling_price&order=ASC',
				'selected'      => ($sort == 'p.selling_price' && $order == 'ASC') ? 'active':''
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.selling_price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.selling_price&order=DESC' . $url, 'SSL'),
				'query_string'  => 'p.selling_price&order=DESC',
				'selected'      => ($sort == 'p.selling_price' && $order == 'DESC') ? 'active':''
			);
			/*
                        if ($this->config->get('config_review_status')) {
                            $data['sorts'][] = array(
                                'text'  => $this->language->get('text_rating_desc'),
                                'value' => 'rating-DESC',
                                'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url),
                                'query_string'  => 'rating&order=DESC'
                            );

                            $data['sorts'][] = array(
                                'text'  => $this->language->t('text_rating_asc'),
                                'value' => 'rating-ASC',
                                'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url),
                                'query_string'  => 'rating&order=ASC'
                            );
                        }

            */

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}
			if (isset($this->request->get['price_filter'])) {
				$url .= '&price_filter=' . $this->request->get['price_filter'];
			}
			if (isset($this->request->get['option'])) {
				$url .= '&option=' . $this->request->get['option'];
			}

			if (isset($this->request->get['rating_filter'])) {
				$url .= '&rating_filter=' . $this->request->get['rating_filter'];
			}


			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();
			$limits = array_unique(array($this->config->get('config_product_limit'), 25, 50, 75, 100));
			sort($limits);
			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value, 'SSL')
				);
			}

			$url = '';
			$data['filter'] = '';
			$data['price_filter'] = '';
			$data['option'] = '';
			$data['rating_filter'] = '';


			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
				$filters = explode(",", $this->request->get['filter']);
				$data['filters'] =  $this->model_catalog_category->getSelectFilters($filters);
				$text = array();
				if(!empty($data['filters'])){
					foreach($data['filters'] as $filters_to_comment){
						if(is_numeric($filters_to_comment['name'])){
							$text[] = $filters_to_comment['name']." size";
						}else{
							$text[] = $filters_to_comment['name'];
						}
					}
				}
				$implode_text = implode(', ', $text);

				if(empty($results) && $product_total< 1){
					$data['no_products_for_applied_filters'] = sprintf($this->language->get('no_products_for_applied_filters'), $implode_text);
				}
			}
			if (isset($this->request->get['price_filter'])) {
				$url .= '&price_filter=' . $this->request->get['price_filter'];
				$price_filter =  $this->request->get['price_filter'];
				$data['price_filter'] = $price_filter;
			}
			if (isset($this->request->get['option'])) {
				$url .= '&option=' . $this->request->get['option'];
				$options = explode(",", $this->request->get['option']);
				$data['options'] =  $this->model_catalog_category->getSelectOptions($options);
			}
			if (isset($this->request->get['rating_filter'])) {
				$url .= '&rating_filter=' . $this->request->get['rating_filter'];
				$rating_filter =  $this->request->get['rating_filter'];
				$data['rating_filter'] = $rating_filter;
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}


			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}', 'SSL');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			$data['total_pages'] = ceil($product_total / $limit);
			$data['current_page'] = $page;
			$data['current_page_path'] = $category_id;
			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;
			$data['custom_store'] = $custom_store;

			$data['continue'] = $this->url->link('common/home', '', 'SSL');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$data['module_filters'] = $this->load->controller('module/filter');
			$data['path'] = $this->request->get['path'];

			$post_type = '';

			if (isset($this->request->get['post_type'])) {
				$post_type = $this->request->get['post_type'];
			}

			if($post_type == 'ajax'){ 
				$data['product_list'] = $this->load->view($this->config->get('config_template') . '/template/product/product_list.tpl', $data);
			    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category_ajax.tpl')) {

					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/category_ajax.tpl', $data));
				} else {
					$this->response->setOutput($this->load->view('default/template/product/category_ajax.tpl', $data));
				}
			}elseif($post_type == 'ajax_pagination'){ 
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product_list.tpl')) {
						$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/product_list.tpl', $data));
					} else {
						$this->response->setOutput($this->load->view('default/template/product/product_list.tpl', $data));
					}
			}else { 
				$data['product_list'] = $this->load->view($this->config->get('config_template') . '/template/product/product_list.tpl', $data);

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category.tpl')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/category.tpl', $data));
				} else {
					$this->response->setOutput($this->load->view('default/template/product/category.tpl', $data));
				}
			}
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}
			if (isset($this->request->get['price_filter'])) {
				$url .= '&price_filter=' . $this->request->get['price_filter'];
			}
			if (isset($this->request->get['option'])) {
				$url .= '&option=' . $this->request->get['option'];
			}

			if (isset($this->request->get['rating_filter'])) {
				$url .= '&rating_filter=' . $this->request->get['rating_filter'];
			}


			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/category', $url, 'SSL')
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');
			$store_id = $this->config->get('config_store_id') ;
			if($store_id == 2){
				$link = $this->config->get('config_url');
				$data['error_cat_not_found'] = sprintf($this->language->get('error_cat_not_found_single'),$link);
			}else{
				$link = $this->config->get('config_url').'?session_id='.$this->session->getId();
				$data['error_cat_not_found'] = sprintf($this->language->get('error_cat_not_found_default'),$link);
			}
			/*$categories = $this->model_catalog_category->getCategories(0);

			foreach($categories as $cat){
				$data['categories'][$cat['name']] = $this->url->link('product/category', 'path=' . $cat['category_id'], 'SSL');
			}*/

			$data['continue'] = $this->url->link('common/home', '', 'SSL');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
			}

		}
	}

	/**
	 * function for setting cookie in tooltip popup on category page
	 */

	public function informativetooltip(){

		$cookie_name = "checkbox";
		$cookie_value = "10";
		setcookie("$cookie_name", $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

	}

	public function informativeTooltipClose(){

		$cookie_name = "closetooltip";
		$cookie_value = "11";
		setcookie("$cookie_name", $cookie_value, time() + (86400 * 7), "/"); // 86400 = 1 day

	}

	/**
	 * function for Deal Of the day
	 */

	public function dealOfTheDay()
	{
		$this->load->language('product/category');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$data['text_empty'] = $this->language->get('text_deal_empty');
		$data['text_per_piece'] = $this->language->get('text_per_piece');
		$data['text_moq_default'] = $this->language->get('text_moq_default');
		$data['text_moq_pre'] = $this->language->get('text_moq_pre');
		$data['heading_title'] = $this->language->get('deal_day_heading_title');
		$data['no_records'] = $this->language->get('no_records');
		$data['detail_view'] = $this->language->get('detail_view');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['store_id'] = $this->config->get('config_store_id');
		$this->document->setTitle($data['heading_title']);
		$data['text_moq_post'] = $this->language->get('text_moq_post');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_list'] = $this->language->get('button_list');
		$data['button_grid'] = $this->language->get('button_grid');
		$data['text_category'] = $this->language->get('text_category');
		$data['categories'] = '';
		$data['store_class'] = '';
		switch($data['store_id']){
			case 0:
				$data['store_class'] = 'default_store';
				break;
			case 2:
				$data['store_class'] = 'singles_store_desktop';
				break;
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = 0;
		}

		$categories_1 = $this->model_catalog_category->getCategories(0);

		foreach ($categories_1 as $category_1) {
			$level_2_data = array();

			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);
			//echo "<pre>"; print_r($categories_2); echo "</pre>";
			foreach ($categories_2 as $category_2) {
				$level_3_data = array();

				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
							'category_id' => $category_3['category_id'],
							'name'        => $category_3['name'],
					);
				}

				$level_2_data[] = array(
						'category_id' => $category_2['category_id'],
						'name'        => $category_2['name'],
						'children'    => $level_3_data
				);
			}

			$data['categories'][] = array(
					'category_id' => $category_1['category_id'],
					'name'        => $category_1['name'],
					'children'    => $level_2_data
			);
		}

		$data['category_id'] = $category_id;


		if (isset($this->request->get['ajax'])) {
			$data['ajax'] = $this->request->get['ajax'];
		} else {
			$data['ajax'] = false;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'RAND()';
		}


		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home', '', 'SSL')
		);

		// Hard code to check against Jaipuri Bandhani
		if (isset($data['category_id']) && $data['category_id'] == 79 ) {
			$data['bandhani_alert'] = $this->language->get('alert_bandhani_dispatch');
		} else {
			$data['bandhani_alert'] = false;
		}
        if ( $data['category_id'] == 87 ) {
            $data['alert_thaan_dispatch'] = $this->language->get('alert_thaan_dispatch');
        } else {
            $data['alert_thaan_dispatch'] = false;
        }
			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_deal_of_day'),
					'href' => 'deal-of-the-day'
			);

			$url = '';
			// $data['single_store_alert'] = '';


			$store_id = $this->config->get('config_store_id') ;
			if($store_id == 2){
				$single_store_order_limit = 2500;

				if((int)$this->config->get('config_limit') > 0){

					$single_store_order_limit = $this->config->get('config_limit');
				}

				$data['single_store_alert'] = sprintf($this->language->get('alert_single_item_limit'), $this->currency->format($single_store_order_limit));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$request_uri =  substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
			$data['qry_string'] = parse_url($request_uri, PHP_URL_QUERY);
			$path_only = parse_url($request_uri, PHP_URL_PATH);

			//dev url hook, remove dev/ as it is already in config_url
			if(strpos($path_only, 'dev/') !== false){
				$path_only = substr($path_only, 4, strlen($path_only));
			}

			//$data['url_path'] = $this->config->get('config_url'). $path_only;
            $data['url_path'] =  $path_only;
			//echo $data['url_path']; die;
			$sellers = "";
			$sellerids = array();
			$data_deal_day = array();
			if($this->config->get('deal_of_day') && !empty($this->config->get('deal_of_day'))){
				$sellers = $this->config->get('deal_of_day');

				//echo "<pre>"; print_r($sellers); exit;
					foreach($sellers as $seller){
					if(!empty($seller['start_date'])){
						$start = explode("/",substr($seller['start_date'],0,10));
						$seller['start_date'] = $start[2].'-'.$start[0].'-'.$start[1];
					}
					if(!empty($seller['end_date'])){
						$end = explode("/",substr($seller['end_date'],0,10));
						$seller['end_date'] = $end[2].'-'.$end[0].'-'.$end[1];
					}
					$today = date('Y-m-d');
					$today=date('Y-m-d', strtotime($today));
					$start_date = date('Y-m-d', strtotime($seller['start_date']));
					$end_date = date('Y-m-d', strtotime($seller['end_date']));

					if (($today >= $start_date) && ($today <= $end_date))
					{
						$sellerids[] = $seller['seller_id'];
						$data_deal_day[] = $seller;
						//break;
					}
				}
					$sellers = implode(",",$sellerids);

					//for mobile theme
					$data['start_date'] = $start_date;
					$data['end_date'] = $end_date;
			}
			$data['total_deal_day'] = count($data_deal_day);
			$data['data_deal_day']  = $data_deal_day;

			$data['products'] = array();

			if(empty($sellers)){
				$product_total = 0;
				$results = array();
			}else{
				$filter_data = array(
						'sort'               => $sort,
						'order'              => $order,
						'start'              => ($page - 1) * $limit,
						'limit'              => $limit,
						'seller'            => $sellers,
						'filter_category_id'=> $category_id,
						'custom_store'		=> isset($this->session->data['custom_store'])?$this->session->data['custom_store'] : 'set'
				);

				$product_total = $this->model_catalog_product->getTotalInStockProducts($filter_data);
				$results = $this->model_catalog_product->getInStockProducts($filter_data);
			}
			//echo "<pre>"; print_r($results); echo "</pre>";


			foreach ($results as $result) {
				$actuallysold = 0;
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				}

				$seller_tax_factor = 1.0 + ( (float)$result['seller_tax'] / 100.0 );
				$commission_factor = 1.0 + ( (float)$result['commission'] / 100.0 );

				$piece_in_set = (int)$result['piece_in_set'] > 1 ? (int)$result['piece_in_set'] : 1;
				$unit_price =  ceil($commission_factor * (float)($result['price']) / $seller_tax_factor);

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
				} else {
					$price = false;
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price_per_set = $this->currency->format($this->tax->calculate($unit_price*$piece_in_set, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
				} else {
					$price_per_set = false;
				}

				if ((float)$result['special']) {
					$unit_special_price = ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
					$special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
				} else {
					$special = false;
				}

				if ((float)$result['special']) {
					$unit_special_price = ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
					$special_per_set = $this->currency->format($this->tax->calculate($unit_special_price*$piece_in_set, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
				} else {
					$special_per_set = false;
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

				if ( (int)$this->config->get('config_store_id') == 2 )
					$is_single = $this->model_catalog_product->checkIfProductIsSingle($result['product_id']);
				else
					$is_single = false;

				//$url .= '&popup=true';

				$data['products'][] = array(
						'product_id'  => $result['product_id'],
						'thumb'       => $image,
						'name'        => $result['name'],
						'set_description' => $result['set_description'],
						'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
						'price'       => $price,
						'selling_price'  => $result['selling_price'],
						'price_per_set' => $price_per_set,
						'piece_in_set'=> $piece_in_set,
						'special'     => $special,
						'special_per_set'=> $special_per_set,
						'tax'         => $tax,
						'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
						'rating'      => $result['rating'],
						'text_in_stock'=>sprintf($this->language->get('text_in_stock'), $result['quantity']),
						'items_in_stock'=>sprintf($this->language->get('items_in_stock'), $result['quantity']),
						'text_out_of_stock'=>$this->language->get('text_out_of_stock'),
						'quantity'=>(int)$result['quantity'],
						'stock_status'=>$result['stock_status'],
						'href'        => $this->url->link('product/product', 'path=' . '' . '&product_id=' . $result['product_id'] . $url, 'SSL'),
						'is_single' => $is_single,
						'product_options' => $this->model_catalog_product->getProductOptions($result['product_id'])
				);
			}
			// Randomizing product sort if no sort order given (default)
			if (!isset($this->request->get['sort'])) {
				shuffle($data['products']);
			}


			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_default'),
					'value' => 'p.rand',
					'href'  => $this->url->link('product/category/dealoftheday', 'path=' . '' . $url, 'SSL'),
					'query_string'  => ' '

			);

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_most_recent'),
					'value' => 'p.date_added-DESC',
					'href'  => $this->url->link('product/category/dealoftheday', 'path=' . '' . '&sort=p.date_added&order=DESC' . $url, 'SSL'),
					'query_string'  => 'p.date_added&order=DESC' // set all query string are in sorting product and replace to href in filter.tpl and category_ajax.tpl
			);

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_price_asc'),
					'value' => 'p.selling_price-ASC',
					'href'  => $this->url->link('product/category/dealoftheday', 'path=' . '' . '&sort=p.selling_price&order=ASC' . $url, 'SSL'),
					'query_string'  => 'p.selling_price&order=ASC'
			);

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_price_desc'),
					'value' => 'p.selling_price-DESC',
					'href'  => $this->url->link('product/category/dealoftheday', 'path=' . '' . '&sort=p.selling_price&order=DESC' . $url, 'SSL'),
					'query_string'  => 'p.selling_price&order=DESC'
			);

			//$getTotalDealDay = $this->model_catalog_category->getTotalDealDay();
			//$data_deal_day = isset($getTotalDealDay['value']) ? unserialize($getTotalDealDay['value']):'';
			//$data['data_deal_day'] = $data_deal_day;

			/*if(!empty($data_deal_day)){
				foreach($data_deal_day as $data){
					if(!empty($data['start_date'])){
						$start = explode("/",substr($data['start_date'],0,10));
						$data['start_date'] = $start[2].'-'.$start[0].'-'.$start[1];
					}
					if(!empty($data['end_date'])){
						$end = explode("/",substr($data['end_date'],0,10));
						$data['end_date'] = $end[2].'-'.$end[0].'-'.$end[1];
					}
					$today = date('Y-m-d');
					$today=date('Y-m-d', strtotime($today));
					$start_date = date('Y-m-d', strtotime($data['start_date']));
					$end_date = date('Y-m-d', strtotime($data['end_date']));

					if (($today >= $start_date) && ($today <= $end_date))
					{
						echo "vikas";
						break;
					}
				}
			}*/
			//echo "<pre>"; print_r(unserialize($getTotalDealDay['value'])); echo "</pre>";
			//echo "<pre>"; print_r($data['total_deal_day']); echo "</pre>"; die;
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('config_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
						'text'  => $value,
						'value' => $value,
						'href'  => $this->url->link('product/category/dealoftheday', $url . '&limit=' . $value, 'SSL')
				);
			}

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

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category/dealoftheday', $url . '&page={page}', 'SSL');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			$data['total_pages'] = ceil($product_total / $limit);
			$data['current_page'] = $page;
			//$data['current_page_path'] = $this->request->get['path'];  /*** commented on (05-01-2016) by vikas****/
			//$data['current_page_path'] = $category_id;
			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home', '', 'SSL');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$data['module_filters'] = $this->load->controller('module/filter');
			$data['path'] = '';//$this->request->get['path'];

			$post_type = '';

			if (isset($this->request->get['post_type'])) {
				$post_type = $this->request->get['post_type'];
			}
			
		if($post_type == 'ajax'){
			$data['product_list'] = $this->load->view($this->config->get('config_template') . '/template/product/deal_day_product_list.tpl', $data);
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/deal_day_category_ajax.tpl')) {

				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/deal_day_category_ajax.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/product/deal_day_category_ajax.tpl', $data));
			}
		}elseif($post_type == 'ajax_pagination'){
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/deal_day_product_list.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/deal_day_product_list.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/product/deal_day_product_list.tpl', $data));
			}
		}else {
			$data['product_list'] = $this->load->view($this->config->get('config_template') . '/template/product/deal_day_product_list.tpl', $data);

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/deal_day_category.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/deal_day_category.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/product/deal_day_category.tpl', $data));
			}
		}
	}

}
