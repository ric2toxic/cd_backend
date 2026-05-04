<?php
class ControllerModuleFilter extends Controller {
	public function index() {

		$this->load->model('setting/store');
		$data['store_switch'] = $this->model_setting_store->getStoreSwitch();

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		$price_filter_show = 1;
		$data['price_filter_show'] = $price_filter_show;

		/////////////////////////////////////////////////
		// Disabling Quality Expection for Store Front //
		/////////////////////////////////////////////////
		
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));

		//echo '<pre>';print_r($store_info);echo '</pre>';
		// echo $this->config->get('config_store_id'). "<br>";
		if (isset($store_info['config_seller_id'])) {
			$data['dont_show_quality'] = 1;				
		}

		$qualityname = 0;
		$data['result'] = null;
		
		$data['quality'] = $qualityname;

		$min_price = 1;
		$max_price = 20000;

		$data['price_with_currency'] = array(
											'symbol'=> $this->currency->getSymbolLeft(),
											'minimum_price' => $min_price,
											'maximum_price' => $max_price
											);

		//echo "<pre>";
		//print_r($data['price_with_currency']);
		//echo "</pre>"; die;
		// stop slide when get price from url. if it's not get from url then set minimum and maximum price.
		$data['get_url_price_filter'] = '';
			if (isset($this->request->get['price_filter'])) {
				$data['get_url_price_filter'] = explode('-', $this->request->get['price_filter']);
			}else{
				$data['get_url_price_filter'] ='';
			}

//echo "<pre>"; print_r($this->request->get['price_filter']); echo "</pre>";
	/*end price range with slider*/
		$category_id = end($parts);

		$this->load->model('catalog/category');

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {
			$this->load->language('module/filter');

			$data['heading_title'] = $this->language->get('heading_title');
            $data['text_discount_on_order'] = $this->language->get('text_discount_on_order');
            $data['text_discount_above_order'] = $this->language->get('text_discount_above_order');

			$data['button_filter'] = $this->language->get('button_filter');


			$url = '';
			$data['sort'] = '';
			/*if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
				$data['sort'] = $this->request->get['sort'];
			}*/
			$data['order'] = 'ASC';
			$url_sort = '';
			/*if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
				$url_sort .= '&order=' . $this->request->get['order'];
				$data['order'] = $this->request->get['order'];
			}*/
			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
				$url_sort .= '&filter=' . $this->request->get['filter'];
			}
			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
				$url_sort .= '&limit=' . $this->request->get['limit'];
			}
			if (isset($this->request->get['price_filter'])) {
				$url .= '&price_filter=' . $this->request->get['price_filter'];
				$url_sort .= '&price_filter=' . $this->request->get['price_filter'];
			}
			if (isset($this->request->get['rating_filter'])) {
				$url .= '&rating_filter=' . $this->request->get['rating_filter'];
				$url_sort .= '&rating_filter=' . $this->request->get['rating_filter'];
			}

			//sorts is copied from category.php to add sortby with filter module on mobile
// query_string is used for sorting and using in filter.tpl href replace by query_string
			$data['sorts'] = array();

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_default'),
					'value' => 'p.rand',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url_sort),
					'query_string'  => ' '

			);

			/*$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url_sort)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url_sort)
			);*/

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_most_recent'),
					'value' => 'p.date_added-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.date_added&order=DESC' . $url_sort),
					'query_string'  => 'p.date_added&order=DESC'
			);

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_price_asc'),
					'value' => 'p.selling_price-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.selling_price&order=ASC' . $url_sort),
					'query_string'  => 'p.selling_price&order=ASC'
			);

			$data['sorts'][] = array(
					'text'  => $this->language->get('text_price_desc'),
					'value' => 'p.selling_price-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.selling_price&order=DESC' . $url_sort),
					'query_string'  => 'p.selling_price&order=DESC'
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
						'text'  => $this->language->get('text_rating_desc'),
						'value' => 'rating-DESC',
						'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url),
						'query_string'  => 'rating&order=DESC'
				);

				$data['sorts'][] = array(
						'text'  => $this->language->get('text_rating_asc'),
						'value' => 'rating-ASC',
						'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url),
						'query_string'  => 'rating&order=ASC'
				);
			}

	        $data['sorts'][] = array(
	            'text'  => $this->language->get('text_discount_desc'),
	            'value' => 'p.special_price-DESC',
	            'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.special_price&order=DESC' . $url_sort),
	            'query_string'  => 'p.special_price&order=DESC'
	        );

			$data['path'] = $this->request->get['path'];
			$data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url_sort));

			if (isset($this->request->get['filter'])) {
				$data['filter_category'] = explode(',', $this->request->get['filter']);
			} else {
				$data['filter_category'] = array();
			}			
			if (isset($this->request->get['option'])) {
				$data['option_category'] = explode(',', $this->request->get['option']);
			} else {
				$data['option_category'] = array();
			}

			$this->load->model('catalog/product');

			$data['filter_groups'] = array();

			//$filter_groups = $this->model_catalog_category->getCategoryFilters($category_id);
			$filter_groups = $this->model_catalog_category->getFiltersOfProducts($category_id);

			if (isset($this->session->data['custom_store'])) {
				if ($this->session->data['custom_store'] == 'single') {
					$option_groups = $this->model_catalog_category->getOptionsOfProducts($category_id);
				}
			}

			if ($filter_groups) {
				foreach ($filter_groups as $filter_group) {
					$childen_data = array();

					foreach ($filter_group['filter'] as $filter) {
						$filter_data = array(
							'filter_category_id' => $category_id,
							'filter_sub_category' => true,
							'filter_filter'      => $filter['filter_id']
						);

						$childen_data[] = array(
							'filter_id' => $filter['filter_id'],
							'name'      => $filter['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						);
					}

					$data['filter_groups'][] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'description'     => $filter_group['description'],
						'filter'          => $childen_data
					);
				}
				if (isset($option_groups)&& !empty($option_groups)) {
					foreach ($option_groups as $option_group) {
						$option_children = array();
						
						foreach ($option_group['option'] as $option) {
							$option_data = array(
								'option_category_id' => $category_id,
								'option_sub_category_id' => true,
								'option_option' => $option['option_value_id'],
							);
							$option_children[] = array(
								'option_value_id' => $option['option_value_id'],
								'option_value' => $option['option_value']
								);

						}
						$data['option_groups'][] = array(
							'option_group_id' => $option_group['option_id'],
							'name' 			  => $option_group['option_name'],
							'option' 		  => $option_children
							);
					}

				}
                                
                                //@author : Amarat
                                //Description : Change filter data for custom filter url
                                
                                //default data
                                $data['is_custom_url'] = '0';
                                $hash_value = '';
                                $data['hash_value'] = '';
                                
                                if(isset($this->session->data['is_custom'])){  //echo 'fdf'; die;
                                    $this->load->model('catalog/custom_url'); 
                                    $url_alias_id = $this->session->data['url_alias_id']; 
                                    $custom_url_data = $this->model_catalog_custom_url->getCustomUrlInfo($url_alias_id);
                                    $origanl_url = $custom_url_data['query'];
                                    if (strpos($origanl_url, '#!') !== false) { //echo '11';
                                        $pos = strpos($origanl_url, '#!');
                                        $hash_value = substr(str_replace('amp;', '',$origanl_url), $pos);
                                        //echo $hash_value;
                                    }
                                    $data['hash_value'] = "'".$hash_value."'";
                                    $data['is_custom_url'] = '1';  
                                }
                                
				// echo "<pre>"; print_r($data['option_groups']);
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/filter.tpl')) {
					return $this->load->view($this->config->get('config_template') . '/template/module/filter.tpl', $data);
				} else {
					return $this->load->view('default/template/module/filter.tpl', $data);
				}
			}
		}
	}
}
