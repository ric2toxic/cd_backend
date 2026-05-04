<?php
class ControllerProductSearch extends Controller {
	public function index() {
		$this->load->language('product/search');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');
        $data = array(); // Initializing the data array to be passed on to template files

		$data['detail_view'] = $this->language->get('detail_view');
		$data['store_id'] = $this->config->get('config_store_id');
		$data['store_class'] = '';

		switch($data['store_id']){

			case 0:
				$data['store_class'] = 'default_store';
				break;
			case 2:
				$data['store_class'] = 'singles_store_desktop';
				break;

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


		if (isset($this->request->get['hide_price']) && !empty($this->request->get['hide_price'])) {
		      $data['hide_price'] = 1;
		    }
		else
		   {
		     $data['hide_price'] = 0;
		   } 

		if (isset($this->request->get['search'])) {

			$search = trim($this->request->get['search']);
			//$search = trim(str_replace("0"," ",$this->request->get['search']));
		} else {
			$search = '';
		}

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} elseif (isset($this->request->get['search'])) {
			$tag = $this->request->get['search'];
		} else {
			$tag = '';
		}

		if (isset($this->request->get['description'])) {
			$description = $this->request->get['description'];
		} else {
			$description = '';
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
            $this->load->model('catalog/category');
            $category_info = $this->model_catalog_category->getCategory($category_id);
		} else {
			$category_id = 0;
		}

		if (isset($this->request->get['sub_category'])) {
			$sub_category = $this->request->get['sub_category'];

		} else {
			$sub_category = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		
		// for store 30 products
		if (!empty($store_product)) {
			$sort = 'date_added';
			$order = 'DESC';
		}

		if (isset($this->request->get['page']) && $this->request->get['page'] != '' && $this->request->get['page'] != 'undefined') {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_product_limit');
		}

		if (isset($this->request->get['rating_filter'])) {
			$rating_filter = $this->request->get['rating_filter'];
		} else {
			$rating_filter = null;
		}

		if (isset($this->request->get['search'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->request->get['search']);
		} elseif (isset($this->request->get['tag'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->language->get('heading_tag') . $this->request->get['tag']);
		} else {
			$this->document->setTitle($this->language->get('heading_title'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', 'SSL')
		);

		$url = '';

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['tag'])) {
			$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['description'])) {
			$url .= '&description=' . $this->request->get['description'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		if (isset($this->request->get['sub_category'])) {
			$url .= '&sub_category=' . $this->request->get['sub_category'];
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

		if (isset($this->request->get['rating_filter'])) {
			$url .= '&rating_filter=' . $this->request->get['rating_filter'];
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('product/search', $url, 'SSL')
		);

		if (isset($this->request->get['search'])) {
			$data['heading_title'] = $this->language->get('heading_title') .  ' - ' . trim($this->request->get['search']);
		} else {
			$data['heading_title'] = $this->language->get('heading_title');
		}

        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);



		$data['compare'] = $this->url->link('product/compare', 'SSL');

		$this->load->model('catalog/category');

		// 3 Level Category Search
		$data['categories'] = array();

		$categories_1 = $this->model_catalog_category->getCategories(0);
        $show_category = $this->language->get('category_text');
		foreach ($categories_1 as $category_1) {
			$level_2_data = array();

			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

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
            if (!empty($this->request->get['category_id'])) {
                if ($this->request->get['category_id'] == $category_1['category_id']) {
                    $show_category = $category_1['name'];
                }
            }
			$data['categories'][] = array(
				'category_id' => $category_1['category_id'],
				'name'        => $category_1['name'],
				'children'    => $level_2_data
			);
		}


		//Post data which receive from filter search
        // Check if we are in the browse more mode OR the first page
        if (isset($this->request->get['ajax'])) {
            $data['ajax'] = $this->request->get['ajax'];
        } else {
            $data['ajax'] = false;
        }
        $data['arr_selected_filter'] = array();
        // Check if any filters are set in the page
        if (isset($this->request->get['filter'])) {
            $filter = $this->request->get['filter'];
            $data['arr_selected_filter']['filter'] = $filter;
        } else {
            $filter = '';
        }

        // Check if any price range has been specified by the user
        if (isset($this->request->get['price_filter'])) {
            $price_filter = $this->request->get['price_filter'];
            $data['arr_selected_filter']['price_filter'] = $price_filter;
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
            $data['arr_selected_filter']['rating_filter'] = $rating_filter;
        } else {
            $rating_filter = null;
        }

        //IN stock/ out of stock filter
        if (isset($this->request->get['stock_filter'])) {
            $show_out_of_stock = $this->request->get['stock_filter'];

        } else {
            $show_out_of_stock = 0;
        }
        $data['arr_selected_filter']['stock_filter'] = $show_out_of_stock;
        // If any sort specified by the user
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];

        } else {
            // default behaviour is showing Handpicked designs.
            $sort = 'sort_order';
        }
        // Handpicked ids, if we are in sort_order mode
        if (isset($this->request->get['handpicked_ids'])) {
            $handpicked_ids = $this->request->get['handpicked_ids'];
        } else {
            $handpicked_ids = '';
        }
        // Random string to have pagination on same data set
        if (isset($this->request->get['random_string'])) {
            $random_string = trim($this->request->get['random_string']);
        } else {
            $random_string = '';
        }

        // product total for page 2 to 5
        if (isset($this->request->get['product_total'])) {
            $filter_product_total = $this->request->get['product_total'];
        } else {
            $filter_product_total = 0;
        }

		if (isset($this->request->get['clearance_sale'])) {
			$filter_sale = $this->request->get['clearance_sale'];
		} else {
			$filter_sale = '';
		}

		if (isset($this->request->get['store_code'])) {
			$data['store_code'] = $this->request->get['store_code'];
		} else {
			$data['store_code'] = '';
		}

		$show_exclusive_only = 0;
		if (isset($_COOKIE['exclusive_voucher_code']) && isset($this->request->get['is_exclusive']) && isset($this->request->get['is_exclusive']) == 1 ){
			$store_code  = SalesStaff::checkStoreVoucher( $this->db, $_COOKIE['exclusive_voucher_code'] );
			if($store_code) {
				$show_exclusive_only 	= 1;
			}
			$data['is_exclusive'] = $this->request->get['is_exclusive'];
		}else{
			$data['is_exclusive'] = 0;
		}
                
        $data['description'] = html_entity_decode($description, ENT_QUOTES, 'UTF-8');        
                
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
            //$path = $custom_url_filter['path'];
            $search = $custom_url_filter['search'];
            //$stock_filter = $custom_url_filter['stock_filter'];
            //$search_sale = $custom_url_filter['search_sale'];
            
            //set heading_title, discription for custom url
            $data['heading_title'] = $custom_url_filter['title'];
            $data['description'] = html_entity_decode($custom_url_filter['description'], ENT_QUOTES, 'UTF-8');   
        }
        
        $filter_data = array(
            'filter_name'         => $search,
            'filter_tag'          => $tag,
            'filter_description'  => $description,
            'filter_category_id'  => $category_id,
            'filter_sub_category' => $sub_category,
            'sort'                => $sort,
            'show_out_of_stock'   => $show_out_of_stock,
            'order'               => $order,
            'start'               => ($page - 1) * $limit,
            'limit'               => $limit,
            'facets'              => true,
            'filter_filter'       => $filter,
            'price_filter'        => $price_filter,
            'option'              => $option,
            'page'                => $page,
            'handpicked_ids'      => $handpicked_ids,
            'product_total'       => $filter_product_total,
            'random_string'       => $random_string,
            'rating_filter'       => $rating_filter,
            'filter_special'      => $filter_sale,
            'custom_store'		  => isset($this->session->data['custom_store']) ? $this->session->data['custom_store'] : 'set',
            'store_code'          => $data['store_code'],
            'is_search'           => 1,
            'is_facet'			  => 1,
            'show_exclusive_only'     => $show_exclusive_only,
						'date_added_less_than' => $date_added_less_than,
						'store_product' => $store_product
        );

			if(SOLR_ENABLED && SOLR_WSBOX_ENABLED){
				//$this->load->model('solr/product');
				$solr = new SolrProduct($this);
				$results_solr = $solr->getProductFromSolr($filter_data);
				$results = $results_solr['products'];
				$product_total = $results_solr['product_total'];
                if(isset($results_solr['filter_facets'])) {
                    $data['filter_facets'] = $results_solr['filter_facets'];
                }else{
                    $data['filter_facets'] = '';
                }
			
			}else {
				//Get Product Total count and product details with pagination limits
				$results_data = $this->model_catalog_product->getProducts($filter_data);

			    $results       = $results_data['products'];
			    $product_total = $results_data['total_count'];

                $data['filter_facets'] = '';
			}
			// Setting handpicked params in GET
            $data['handpicked_ids'] = (!empty($results_solr['handpicked_ids'])) ? $results_solr['handpicked_ids'] : '';

            $data['product_total'] = $product_total;
            $data['random_string'] = (!empty($results_solr['random_string'])) ? $results_solr['random_string'] : '';

            if($tag == "")
                $data['search_result_heading'] = sprintf($data['search_result_heading_without_tags'], $show_category, number_format($product_total));
            else
                $data['search_result_heading'] = sprintf($data['search_result_heading'], $show_category, $tag, number_format($product_total));

        	foreach ($results as $result) {
				$actuallysold = 0;
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
					$img_width = $this->config->get('config_image_product_width');
					$img_height = $this->config->get('config_image_product_height');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
					$img_width = '';
					$img_height = '';
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
					$unit_special_price = $result['special']; //ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
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

				if($result['vacation_mode'] == 1){
					$result['quantity'] = 0;
				}
                $is_single = $this->model_catalog_product->checkIfProductIsSingle($result['product_id']);

                if ( $this->customer->isLogged() )
                    $previously_ordered = $this->model_catalog_product->checkPreviouslyOrdered($this->customer->getId(), $result['product_id']);
                else
                    $previously_ordered = false;

                /*------ start fill heart after user login ----*/
                $data['fill_heart'] = $this->customer->getWishlistIcon($result['product_id']);
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
					'text_sold_out'=>sprintf($this->language->get('text_sold_out'), $result['sold_out']+$actuallysold),
					'text_out_of_stock'=>$this->language->get('text_out_of_stock'),
					'quantity'=>(int)$result['quantity'],
					'stock_status'=>$result['stock_status'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url, 'SSL'),
                    'is_single' => $is_single,
					'previously_ordered'=> $previously_ordered,
					'product_options' => $this->model_catalog_product->getProductOptions($result['product_id']),
					'cod_available'	 => $result['cod_available'],
					'img_width' => $img_width,
					'img_height' => $img_height,
					'mrp' => $result['mrp'],
					'format_mrp' => $result['format_mrp'],
					'saving_money' => $result['saving_money'],
					'selling_price' => $this->currency->format($result['selling_price']),
					'exp_dispatch_date' => $result['exp_dispatch_days'],
					'margin_percentage' => $result['margin_percentage '],
                    'row_data' => $result,
                    'cart_tracking_id_for_ga' => 'wsb-addtocart-from-list-'.strtolower($result['model']),
                    'detail_popup_tracking_id_for_ga' => 'wsb-detailpopup-from-list-'.strtolower($result['model']),
				);
			}

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			if (isset($this->request->get['rating_filter'])) {
				$url .= '&rating_filter=' . $this->request->get['rating_filter'];
			}

			$data['sorts'] = array();

            $data['sorts'][] = array(
                'text'  => $this->language->get('text_default'),
                'value' => 'sort_order',
                'href'  => $this->url->link('product/search', 'sort=sort_order' . $url, 'SSL'),
                'query_string'  => ' ',
                'selected'      => ($sort == 'sort_order') ? 'active':''

            );

            /*
             $data['sorts'][] = array(
                        'text'  => $this->language->get('text_most_in_stock'),
                        'value' => 'p.quantity-DESC',
                        'href'  => $this->url->link('product/search', 'sort=p.quantity&order=DESC' . $url),
                        'query_string'  => 'p.quantity&order=DESC'
                );
            */

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_most_recent'),
					'value' => 'p.date_added-DESC',
					'href'  => $this->url->link('product/search', 'sort=p.date_added&order=DESC' . $url, 'SSL'),
                    'query_string'  => 'p.date_added&order=DESC',
                    'selected'      => ($sort == 'p.date_added') ? 'active':''
            );

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.selling_price-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.selling_price&order=ASC' . $url, 'SSL'),
                'query_string'  => 'p.selling_price&order=ASC',
                'selected'      => ($sort == 'p.selling_price' && $order == 'ASC') ? 'active':''
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.selling_price-DESC',
				'href'  => $this->url->link('product/search', 'sort=p.selling_price&order=DESC' . $url, 'SSL'),
                'query_string'  => 'p.selling_price&order=DESC',
                'selected'      => ($sort == 'p.selling_price' && $order == 'DESC') ? 'active':''
			);

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
					'href'  => $this->url->link('product/search', $url . '&limit=' . $value, 'SSL')
				);
			}

			$data['ratings'] = array();
			$ratings = array(''=>'All',
							 '5'=>'Excellent Quality',
							 '4'=>'Good Quality',
							 '3'=>'Average Quality'
							);
			foreach($ratings as $key=>$value){
				$data['ratings'][] = array(
					'text' => $value,
					'value' => $key,
					'href' => $this->url->link('product/search', $url . '&rating_filter=' . $key, 'SSL'),
				);
			}

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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

			if (isset($this->request->get['rating_filter'])) {
				$url .= '&rating_filter=' . $this->request->get['rating_filter'];
			}

			/*$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/search', $url . '&page={page}');

			$data['pagination'] = $pagination->render(); */

			$data['total_pages'] = ceil($product_total / $limit);
			$data['current_page'] = $page;

			if(!empty($this->request->get['category_id'])) {
				$data['current_page_path'] = $this->request->get['category_id'];
			}else{
				$data['current_page_path'] = '';
			}

			/* $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit)); */


		$data['search'] = trim(urldecode($search));
		
		$data['category_id'] = $category_id;
		$data['sub_category'] = $sub_category;



		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;
		$data['rating_filter'] = $rating_filter;

        $post_type = '';

        if (isset($this->request->get['post_type'])) {
            $post_type = $this->request->get['post_type'];
        }

        $data['text_discount_on_order'] = $this->language->get('text_discount_on_order');
        $data['text_discount_above_order'] = $this->language->get('text_discount_above_order');

        $data['filter_data'] = $filter_data; //we use filter_ids and rating to set selected in left column

        if (is_array($data['filter_facets']) && count($data['filter_facets']) > 0 ) {
            $data['column_left'] = $this->load->controller('product/filter_facets', $data);
        } else {
            $data['column_left'] = $this->load->controller('common/column_left');
        }

        $data['selected_filters'] = $this->load->controller('product/selected_filters', $data);

        $data['sorts_list'] = $this->load->controller('product/sorts_list', $data);

		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		// customer id set in hidden value in comment box when i click on "i want this design" by vikas(02-06-2015)
		$data['customer_id'] = $this->customer->getId();
		$data['logged'] = $this->customer->isLogged();

		// for comment popup
		$data['comment_popup_heading'] = $this->language->get('comment_popup_heading');
		$data['comment_popup_send'] = $this->language->get('comment_popup_send');
		$data['i_want_this_design'] = $this->language->get('i_want_this_design');

        $data['ajax']   = false;
        if ($post_type == 'ajax') {
            //$data['column_left'] = '';
            //$data['column_right'] = '';
            $data['footer'] = '';
            $data['header'] = '';
            $data['ajax']   = true;
        }

        if($post_type == 'ajax_pagination'){
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product_list_search.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/product_list_search.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/product/product_list.tpl', $data));
			}
		} else if ($post_type == 'ajax') {
		    $data['search'] = $tag;
            $data['json']['product_list'] = $this->load->view($this->config->get('config_template') . '/template/product/product_list_search.tpl', $data);
            $data['json']['coulmn_left'] = $data['column_left'];
            $data['json']['search_result_heading'] = $data['search_result_heading'];
            $data['json']['total_search_result'] = $product_total;
            $data['json']['selected_filters'] = $data['selected_filters'];
            $data['json']['handpicked_ids'] = $data['handpicked_ids'];
            $data['json']['random_string'] = $data['random_string'];
            $data['json']['product_total'] = $data['product_total'];
            $data['json']['total_pages']    = $data['total_pages'];
            $data['json']['stock_filter'] = $data['arr_selected_filter']['stock_filter'];
            echo json_encode($data['json']);
            /*if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/search.tpl')) {

                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/search.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/product/search.tpl', $data));
            }
            */
        } else {
			$data['product_list'] = $this->load->view($this->config->get('config_template') . '/template/product/product_list_search.tpl', $data);
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/search.tpl')) {

			    $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/search.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/product/search.tpl', $data));
			}
		}

	}

	// this function is used for save comment by user when click on "i want this design" instead of
	// out of stock by vikas(02-06-2016)
	public function user_comment(){
		$this->load->model('catalog/product');
		$this->load->language('product/search');

		$customer_id = $this->customer->getId();
		$product_id = $this->request->post['product_id'];
		$product_status = $this->request->post['product_status'];
		$popup_comment = $this->request->post['popup_comment'];

		$request_source = 'WEB';
        if(CONFIG_IS_MOBILE)
        {
          $request_source = 'MOBILE';  
        }

		$data_user_comment = array(
			'customer_id' => $customer_id,
			'product_id' => $product_id,
			'product_status' => $product_status,
			'popup_comment' => $popup_comment,
			'request_source' => $request_source
		);


		$getInformation = $this->model_catalog_product->user_comment($data_user_comment);

		$message  = sprintf($this->language->get('mail_message'),
								$getInformation['customer_name'],
								$getInformation['mobile_no'],
								$getInformation['product_name'],
								$getInformation['product_model'],
								$getInformation['product_status'],
								$getInformation['user_comment']
							) . "\n\n";

		$subject = $this->language->get('mail_subject');
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->SMTPSecure = 'ssl';
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');
		$mail->setFrom(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
		$mail->addReplyTo(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
        $mail->addAddress(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
		$mail->Subject = $subject;
		$mail->msgHTML($message);
		$mail->send();

		$json = array(
			'product_id' => $product_id,
			'message'=> $this->language->get('want_design_message')
		);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * function to calculate percent discount from special price
	 * */

	public function calculateSpecialPriceValueInPercent($special_price, $selling_price, $tax_class_id ) {
		$discount = $selling_price - $special_price;
		$percent_discount = ($discount*100)/$selling_price;
		return ceil($percent_discount);
	}
}
