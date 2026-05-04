<?php
require_once __DIR__ . '/system.php';
require_once DIR_SYSTEM . 'library/solr/product.php';
require_once DIR_SYSTEM . 'library/solr/model_solr_product.php';
require_once DIR_SYSTEM . 'library/cart.php';
require_once DIR_SYSTEM . 'library/wsb.php';

class CategoryController extends SystemController {
	private static $displayed = false;
	public function __construct($params) {

		parent::__construct($params);
		// Currency
		//$this->registry->set('currency', new Currency($this->registry));
		// Customer
		//$this->registry->set('customer', new Customer($this->registry));
		// Tax
		//$this->registry->set('tax', new Tax($this->registry));
		// Cart
		$this->registry->set('cart', new Cart($this->registry));
		// Wsb
		$this->registry->set('wsb', new Wsb($this->registry));
	}
	/**
	 * customer login
	 */
	public function product_list() {
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');
		$this->load->model('catalog/category');
		$this->load->language('product/category');
		$this->load->language('product/product');

		$data['meta_title'] = $this->config->get('config_meta_title');
		$data['meta_description'] = $this->config->get('config_meta_description');
		$data['meta_keyword'] = $this->config->get('config_meta_keyword');

		$sellers = '';
		$data['detail_view'] = $this->language->get('detail_view');

		// Check which store we want to show the data for mobile
		if (isset($this->request['custom_store']) && !empty($this->request['custom_store'])) {
			$custom_store = $this->request['custom_store'];
			$data['custom_store'] = $this->request['custom_store'];
		} else if (isset($this->session->data['custom_store']) && $this->session->data['custom_store'] != '') {
			$custom_store = $this->session->data['custom_store'];
			$data['custom_store'] = $this->session->data['custom_store'];
		} else {
			$custom_store = 'set';
			$data['custom_store'] = 'set';
		}

		if (isset($this->request['client_preferences'])) {
			$client_preferences = $this->request['client_preferences'];
		} else {
			$client_preferences = '';
		}

		// Check whether we want to show the alert for being on singles store
		if ($custom_store == 'single') {
			$data['single_store_alert'] = $this->language->get('alert_single_store');
		} else {
			$data['single_store_alert'] = '';
		}

		if (isset($this->request['search'])) {
			$search = str_replace('`', '&', $this->request['search']);
			$search = trim(html_entity_decode($search));
			$data['search'] = $search;
		} else {
			$search = '';
			$data['search'] = $search;
		}

		// to get the store30 products(i.e. store prodcts added before 30 days)
		if (isset($this->request['store_product'])) {
			$store_product = (int) $this->request['store_product'];
		} else {
			$store_product = 0;
		}

		if (!empty($this->request['purchase_days'])) {
			$purchase_days = (int) $this->request['purchase_days'];
			$date = date_create();
			date_sub($date, date_interval_create_from_date_string($purchase_days . " days"));
			$date_added_less_than = date_format($date, "Y-m-d\TH:i:s\Z");
		} else {
			$purchase_days = 0;
			$date_added_less_than = '';
		}

		// Check if any filters are set in the page
		if (isset($this->request['filter'])) {
			$filter = $this->request['filter'];
		} else {
			$filter = '';
		}

		// Check if any price range has been specified by the user
		if (!empty($this->request['price_filter']) && $this->request['price_filter'] != 'all') {
			if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID && $this->session->data['currency'] != 'USD') {
				$price_filter_arr = explode("-", $this->request['price_filter']);

				$price_filter_from = $this->currency->convert((int) $price_filter_arr[0], 'INR', 'USD');

				$price_filter_to = $this->currency->convert((int) $price_filter_arr[1], 'INR', 'USD');

				$price_filter = $price_filter_from . '-' . $price_filter_to;
			} else {
				$price_filter = $this->request['price_filter'];
			}

		} else {
			$price_filter = '';
		}

		// Check if any option has been selected by the user
		if (isset($this->request['option'])) {
			$option = $this->request['option'];
		} else {
			$option = '';
		}

		// Check if any rating has been selected by the user
		if (!empty($this->request['rating_filter']) && ($this->request['rating_filter'] != "undefined")) {
			$rating_filter = $this->request['rating_filter'];
		} else {
			$rating_filter = null;
		}

		// If any sort specified by the user
		if (!empty($this->request['sort'])) {
			$sort = $this->request['sort'];
		} else if (strtolower(trim($this->request['search'])) == "rxt") {
			/*** RXT Code search ***/
			// if sort is not present in request and search keyword is rxt, then set sort to date added
			$sort = "p.date_added";
		} else {
			$sort = 'sort_order';
		}

		// Handpicked ids, if we are in sort_order mode
		if (!empty($this->request['handpicked_ids'])) {
			$handpicked_ids = $this->request['handpicked_ids'];
		} else {
			$handpicked_ids = '';
		}
		// product total for page 2 to 5
		if (!empty($this->request['product_total'])) {
			$filter_product_total = $this->request['product_total'];
		} else {
			$filter_product_total = 0;
		}

		// product total for page 2 to 5
		if (!empty($this->request['random_string'])) {
			$random_string = trim($this->request['random_string']);
		} else {
			$random_string = '';
		}

		if (!empty($this->request['order'])) {
			$order = $this->request['order'];
		} else if (strtolower(trim($this->request['search'])) == "rxt") {
			/*** RXT Code search ***/
			$order = "DESC";
		} else {
			$order = 'ASC';
		}

		// for store 30 products
		if (!empty($store_product)) {
			$sort = 'date_added';
			$order = 'DESC';
		}

		if (!empty($this->request['page'])) {
			$page = $this->request['page'];
		} else {
			$page = 1;
		}

		if (!empty($this->request['limit'])) {
			$limit = $this->request['limit'];
		} else {
			$limit = $this->config->get('config_product_limit');
		}

		if (!empty($this->request['stock_filter']) && $this->request['stock_filter'] == 1) {
			$show_out_of_stock = $this->request['stock_filter'];
			$data['stock_filter'] = $show_out_of_stock;
		} else {
			$show_out_of_stock = 0;
			$data['stock_filter'] = $show_out_of_stock;
		}

		if (!empty($this->request['clearance_sale']) && $this->request['clearance_sale'] == 1) {
			$filter_sale = $this->request['clearance_sale'];
		} else {
			$filter_sale = '';
		}

		if (!empty($this->request['store_code']) && $this->request['store_code'] != 'undefined') {
			$data['store_code'] = $this->request['store_code'];
		} else {
			$data['store_code'] = '';
		}

		if (!empty($this->request['last_filter_action'])) {
			$last_filter_action = $this->request['last_filter_action'];
		} else {
			$last_filter_action = '';
		}

		if (!empty($this->request['csv_req'])) {
			$csv_req = $this->request['csv_req'];
		} else {
			$csv_req = 0;
		}

		if (!empty($this->request['location'])) {
			$location = $this->request['location'];
		} else {
			$location = '';
		}

		$show_exclusive_only = 0;
		if (isset($_COOKIE['exclusive_voucher_code']) && isset($this->request['is_exclusive']) && isset($this->request['is_exclusive']) == 1) {
			$store_code = SalesStaff::checkStoreVoucher($this->db, $_COOKIE['exclusive_voucher_code']);
			if ($store_code) {
				$show_exclusive_only = 1;
			}
			$data['is_exclusive'] = $this->request['is_exclusive'];
		} else {
			$data['is_exclusive'] = 0;
		}

		$url = '';
		$search_all_products = 0;
		if (!empty($this->request['search_all_products'])) {
			$url .= '&search_all_products=' . $this->request['search_all_products'];
			$search_all_products = $this->request['search_all_products'];
			if ($sort == 'sort_order') {
				$sort = '';
			}
		}

		if (!empty($this->request['path'])) {
			$data['static_redirect'] = $this->url->staticRedirect($this->request['path']);

			$data['path_type'] = 'category_id';
			$data['path_keyword'] = $this->request['path'];
			$query_path = $this->url->getQueryFromKeyword($this->request['path']);
			if (count($query_path) > 0) {
				$this->request['path'] = $query_path['id'];
				$data['path_type'] = $query_path['url_type'];
			}

			if (isset($this->request['sort'])) {
				$url .= '&sort=' . $this->request['sort'];
			}

			if (isset($this->request['order'])) {
				$url .= '&order=' . $this->request['order'];
			}

			if (isset($this->request['limit'])) {
				$url .= '&limit=' . $this->request['limit'];
			}

			$path = '';
			$parts = explode('_', (string) $this->request['path']);
			$category_id = (int) array_pop($parts);

		} else {
			$category_id = 0;
			$data['path_keyword'] = '';
		}
		$data['category_id'] = (int) $category_id;
		$data['category_info'] = $this->model_catalog_category->getCategory($category_id);

		if (!empty($data['category_info']['parent_id'])) {
			$data['parent_category_info'] = $this->model_catalog_category->getCategory($data['category_info']['parent_id']);
		}

		if ($data['category_id'] == 79) {
			$data['bandhani_alert'] = $this->language->get('alert_bandhani_dispatch');
		} else {
			$data['bandhani_alert'] = false;
		}
		if ($data['category_id'] == 87) {
			$data['alert_thaan_dispatch'] = $this->language->get('alert_thaan_dispatch');
		} else {
			$data['alert_thaan_dispatch'] = false;
		}

		$store_id = $this->config->get('config_store_id');
		//get similar menu
		$data['selected_tab'] = 0;
		$data['categories'] = array();
		$mbileobj = new Mobile_Detect_Class();
		$is_mobile_site = $mbileobj->isMobile();
		if ($is_mobile_site && $category_id > 0) {
			$similar_menu = $this->model_catalog_category->getSimilarMenu($category_id);
			$selected_tab = 0;
			if (count($similar_menu) > 0) {
				foreach ($similar_menu as $similar_menu_arr) {
					$data['categories'][] = array(
						'name' => $similar_menu_arr['link_title'],
						'keyword' => $this->url->rewrite_keyword('category_id', $similar_menu_arr['value']),
						'href' => $this->url->link('product/category', 'path=' . $this->request['path'] . '_' . $similar_menu_arr['value'] . $url, 'SSL'),
					);
					if ($category_id == $similar_menu_arr['value']) {
						$data['selected_tab'] = $selected_tab;
					}
					$selected_tab++;
				}
			}
		}

		$request_uri = substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
		$data['qry_string'] = parse_url($request_uri, PHP_URL_QUERY);

		$path_only = parse_url($request_uri, PHP_URL_PATH);
		//dev url hook, remove dev/ as it is already in config_url
		if (strpos($path_only, 'staging/') !== false) {
			$path_only = substr($path_only, 8, strlen($path_only));
		}

		//$data['url_path'] = $this->config->get('config_url'). $path_only;
		$data['url_path'] = $path_only;
		$data['show_limit'] = $limit;
		$data['products'] = array();

		// Setting filters to get the products for
		$filter_data = array(
			'filter_name' => $search,
			'client_preferences' => $client_preferences,
			'filter_category_id' => (isset($category_id)) ? $category_id : "",
			'filter_filter' => $filter,
			'option' => $option,
			'price_filter' => $price_filter,
			'show_out_of_stock' => $show_out_of_stock,
			'sort' => $sort,
			'order' => $order,
			'last_filter_action' => $last_filter_action,
			'csv_req' => $csv_req,
			'page' => $page,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
			'seller' => $sellers,
			'custom_store' => $custom_store,
			'handpicked_ids' => $handpicked_ids,
			'product_total' => $filter_product_total,
			'random_string' => $random_string,
			'filter_special' => $filter_sale,
			'store_code' => $data['store_code'],
			'show_exclusive_only' => $show_exclusive_only,
			'facets' => true,
			'is_search' => 1,
			'is_facet' => 1,
			'search_all_products' => $search_all_products,
			'user_id' => isset($this->session->data['customer_id']) && (!empty($this->session->data['customer_id'])) ? $this->session->data['customer_id'] : $this->customer->getId(),
			'date_added_less_than' => $date_added_less_than,
			'store_product' => $store_product,
		);

		if (isset($this->request['filter_retain']) && $this->request['filter_retain'] == 1) {
			$filter_data['filter_retain'] = $this->request['filter_retain'];
		}
		// If a valid rating_filter is provided
		if (!empty($rating_filter) and $rating_filter != 'all') {
			$filter_data['rating_filter'] = $rating_filter;
		}

		// location wise product_list
		if (!empty($location)) {
			$filter_data['location'] = $location;
		}

		// Getting only instock products
		$newFilter = array();

		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
			if ((int) $this->request['page'] == 1) {
				$filter_data['clicked_filter'] = $this->request['filter'];
			}
			$solr = new SolrProduct($this);
			$results_solr = $solr->getProductFromSolr($filter_data);

			if (isset($category_id) && $category_id != '0') {
				if (isset($results_solr['filter_facets']['filters'])) {
					$unique_category_filter_group_id = $this->model_catalog_category->getUniqueCategoryFilterGroupId($category_id);
					foreach ($unique_category_filter_group_id as $key => $value) {
						if (isset($results_solr['filter_facets']['filters'][$value['filter_group_id']]) && count($results_solr['filter_facets']['filters'][$value['filter_group_id']]) > 0) {
							$newFilter[$value['filter_group_id']] = $results_solr['filter_facets']['filters'][$value['filter_group_id']];
						}
					}
				}
				if (count($newFilter) > 0) {
					$remain_array = array_diff_key($results_solr['filter_facets']['filters'], $newFilter);
					$results_solr['filter_facets']['filters'] = array_values($newFilter);
					if (count($remain_array) > 0) {
						$combine_array = $newFilter + $remain_array;
						$results_solr['filter_facets']['filters'] = array_values($combine_array);
					}
				}
			}
			$results = $results_solr['products'];
			$product_total = $results_solr['product_total'];

			$data['filter_facets'] = '';
			$data['price_with_currency'] = array(
				'symbol' => $this->currency->getSymbolLeft(),
				'minimum_price' => 0,
				'maximum_price' => 0,
			);
			$data['filter_rating'] = array(
				'5.0' => 0,
				'4.0' => 0,
				'3.0' => 0,
			);

			if (isset($results_solr['filter_facets'])) {
				$data['filter_facets_solr'] = $results_solr['filter_facets'];

				if (isset($results_solr['filter_facets']['filters'])) {
					//ksort($results_solr['filter_facets']['filters']);
					$data['filter_facets'] = $results_solr['filter_facets'];
					$data['filter_facets']['filters'] = array_values($data['filter_facets']['filters']);
				}

				if (isset($results_solr['filter_facets']['price'])) {
					$data['price_with_currency'] = array(
						'symbol' => $this->currency->getSymbolLeft(),
						'minimum_price' => intval($results_solr['filter_facets']['price'][0]),
						'maximum_price' => ceil($results_solr['filter_facets']['price'][1]),
						'price_value' => array('min' => intval($results_solr['filter_facets']['price'][0]), 'max' => ceil($results_solr['filter_facets']['price'][1]),
						),
					);

					// set search filter
					if (isset($this->request['price_filter']) && $this->request['price_filter'] != 'all') {
						$res = explode("-", $this->request['price_filter']);
						$data['price_with_currency']['price_value']['min'] = isset($res[0]) ? $res[0] : 0;
						$data['price_with_currency']['price_value']['max'] = isset($res[1]) ? $res[1] : 0;
						if ($data['price_with_currency']['maximum_price'] == 0) {
							$data['price_with_currency']['minimum_price'] = isset($res[0]) ? $res[0] : 0;
							$data['price_with_currency']['maximum_price'] = isset($res[1]) ? $res[1] : 0;
						}
					}

				}

				if (isset($results_solr['filter_facets']['rating'])) {
					$data['filter_rating'] = $results_solr['filter_facets']['rating'];
				}

			}

		} else {
			$product_total = $this->model_catalog_product->getTotalInStockProducts($filter_data);
			$results = $this->model_catalog_product->getInStockProducts($filter_data);

			// searching data saved in searched_term
			$has_results = count($results);
			if ($has_results > 0) {
				$has_results = 1;
			} else {
				$has_results = 0;
			}
			$this->model_catalog_product->setSearchedKeyword($search, $has_results);
			$data['filter_facets'] = '';
		}

		$data['handpicked_ids'] = (!empty($results_solr['handpicked_ids'])) ? $results_solr['handpicked_ids'] : '';
		$data['product_total'] = $product_total;
		$data['random_string'] = (!empty($results_solr['random_string'])) ? $results_solr['random_string'] : '';

		foreach ($results as $result) {

			$preload_img_string = array();
			$result_images = array();

			if (!empty($result['image_dimensions'])) {
				$result_images[] = $this->get_images_data($result['image'], unserialize($result['image_dimensions']));
			} else {
				$result_images[] = $this->get_images_data($result['image']);
			}
			$preload_img_string[] = $result_images[0]['popup'];
			$preload_img_string[] = $result_images[0]['thumb'];

			$result['images'] = $this->model_catalog_product->getProductImages($result['product_id']);

			foreach ($result['images'] as $images) {
				if (!empty($images['image_dimensions'])) {
					$image_data = $this->get_images_data($images['image'], unserialize($images['image_dimensions']));
				} else {
					$image_data = $this->get_images_data($images['image']);
				}
				$result_images[] = $image_data;
				$preload_img_string[] = $image_data['popup'];
				$preload_img_string[] = $image_data['thumb'];
			}

			$seller_tax_factor = 1.0 + ((float) $result['seller_tax'] / 100.0);
			$commission_factor = 1.0 + ((float) $result['commission'] / 100.0);

			$piece_in_set = (int) $result['piece_in_set'] > 1 ? (int) $result['piece_in_set'] : 1;
			$unit_price = ceil($commission_factor * ((float) ($result['price']) / $seller_tax_factor));

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$unformatted_price = $this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
				$store_id = $this->config->get('config_store_id');
				if ($store_id == INTERNATIONAL_STORE_ID) {
					$price = $this->currency->format($unformatted_price, '', '', true, 2, '');
				} else {
					$price = $this->currency->format($unformatted_price, '', '', true, 0, 'frontend');
				}
			} else {
				$price = false;
				$unformatted_price = false;
			}
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price_per_set = $this->currency->format($this->tax->calculate($unit_price * $piece_in_set, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
			} else {
				$price_per_set = false;
			}

			if ((float) $result['special']) {
				$unit_special_price = (float) $result['special']; // ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
				$special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']), '', '', true, 0, 'frontend');
				$percent_discount = $this->model_catalog_product->calculateSpecialPriceValueInPercent($unit_special_price, $result['selling_price'], $result['tax_class_id']);
				$special_per_set = $this->currency->format($this->tax->calculate($unit_special_price * $piece_in_set, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
			} else {
				$percent_discount = false;
				$special = false;
				$special_per_set = false;
			}

			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format((float) $result['special'] ? ceil($commission_factor * (float) ($result['special']) / $seller_tax_factor) : ceil($commission_factor * (float) ($result['price']) / $seller_tax_factor));
			} else {
				$tax = false;
			}

			if ($this->config->get('config_review_status')) {
				$rating = (int) $result['rating'];
			} else {
				$rating = false;
			}

			if ((int) $this->config->get('config_store_id') == 2) {
				$is_single = $this->model_catalog_product->checkIfProductIsSingle($result['product_id']);
			} else {
				$is_single = false;
			}

			if ($this->customer->isLogged()) {
				$previously_ordered = $this->model_catalog_product->checkPreviouslyOrdered($this->customer->getId(), $result['product_id']);
			} else {
				$previously_ordered = false;
			}

			/*------ start fill heart after user login ----*/
			$data['fill_heart'] = $this->customer->getWishlistIcon($result['product_id']);
			/*------ END fill heart after user login ----*/
			//## Getting category link (in case of multiple categories, it picks only the first one in the list)
			$getCatLink = $this->model_catalog_product->getCategory($result['product_id']);

			//##
			$filters = $this->model_catalog_product->getProductFiltersData($result['product_id']);

			$tags_result = array();

			$stock_array = Cart::getProductStockStatus($result);
			if (isset($stock_array['stock']) && $stock_array['stock'] === true) {
				$stock = true;
				$quantity = $result['quantity'];
			} else {
				$stock = false;
				$quantity = 0;
			}

			$alternate_product = $this->model_catalog_product->getAlternateProductInfo($result['is_single'], $result['model']);
			$custom_store_selling_price = $this->currency->format(isset($alternate_product['selling_price']) ? $alternate_product['selling_price'] : '');
			$custom_store_product_id = isset($alternate_product['product_id']) ? $alternate_product['product_id'] : '';

			$custom_store_product_href = "";
			if (!empty($custom_store_product_id)) {
				$custom_store_product_href = $this->url->link('product/product', 'path=' . $this->request['path'] . '&product_id=' . $custom_store_product_id, 'SSL');
			}

			$discounts = array();
			$product_options = array();
			$tax_rate = $result['tax_rate'];
			$text_tax_rate = $tax_rate ? '(' . (float) $tax_rate . "%)" : false;

			$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

			/********Added by NILESH, As per new requirement option image will show in backend appProduct*********/

			/*if(!empty($product_options)) {
				                foreach ($product_options as &$product_option) {
				                    if (!empty($product_option['product_option_value'])) {
				                        foreach ($product_option['product_option_value'] as &$option_value) {
				                            if (!empty($option_value['image'])) {
				                                $image = $this->model_tool_image->resize($option_value['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				                                $option_value['image'] = $image;
				                            } else {
				                                $option_value['image'] = $result_images[0]['image'];
				                            }
				                        }
				                    }
				                }
			*/
			/*****END*****/

			//manage text units
			if ($result['base_unit'] != '') {
				if ($special) {
					// $special = $special . ' / ' . $result['base_unit'];
					$special = $special;
					$price_with_unit = $price;
				} else {
					$price_with_unit = $price . ' / ' . $result['base_unit'];
				}

			} else {
				$price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
			}
			/* End by Amarat (23-august-2017) */

			if ($show_out_of_stock == 1 || ($show_out_of_stock == 0 && $stock == true)) {

				$pickup_city = preg_replace('/[0-9]+/', '', $result['pickup_city']);
				$pickup_city = explode(",", $pickup_city);
				$index_count = count($pickup_city) - 1;
				if (isset($pickup_city[$index_count])) {
					$result['pickup_city'] = trim($pickup_city[$index_count]);
				} else {
					$result['pickup_city'] = trim($pickup_city[$index_count - 1]);
				}

				$data['products'][] = array(
					'product_id' => $result['product_id'],
					'name' => mb_strimwidth(html_entity_decode($result['name']), 0, 25, "..."),
					'full_name' => html_entity_decode($result['name']),
					'is_sor_enabled' => $result['is_sor_enabled'],
					'sor_enabled_text' => $result['sor_enabled_text'],
					'sor_enabled_detail_text' => $result['sor_enabled_detail_text'],
					'heading_title' => mb_strimwidth(html_entity_decode($result['name']), 0, 50, "..."),
					'set_description' => $result['set_description'],
					'short_set_description' => utf8_substr($result['set_description'], 0, 45),
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'exp_dispatch_days' => $result['exp_dispatch_days'],
					'price' => $price,
					'price_value' => $unformatted_price,
					'seller_id' => $result['seller_id'],
					'pickup_city' => ucwords(strtolower($result['pickup_city'])),
					'category_id' => $result['category_id'],
					'unformatted_price' => $unformatted_price,
					'selling_price' => $this->currency->format($result['selling_price']),
					'price_per_set' => $price_per_set,
					'piece_in_set' => $piece_in_set,
					'special' => $special,
					'percent_discount' => $percent_discount,
					'discounts' => $discounts,
					'special_per_set' => $special_per_set,
					'tax' => $tax,
					'text_tax_rate' => $text_tax_rate,
					'rating' => $rating,
					'quantity' => $quantity,
					'stock' => $stock,
					'minimum' => $result['minimum'],
					'stock_status' => $result['stock_status'],
					'is_single' => $result['is_single'],
					'previously_ordered' => $previously_ordered,
					'cod_available' => $result['cod_available'],
					'fill_heart' => $data['fill_heart'],
					'mrp' => $result['mrp'],
					'format_mrp' => $result['format_mrp'],
					'saving_money' => $result['saving_money'],
					'exp_dispatch_date' => $result['exp_dispatch_days'],
					'margin_percentage' => !empty($result['margin_percentage']) ? $result['margin_percentage'] : '',
					'cart_tracking_id_for_ga' => 'wsb-addtocart-from-list-' . strtolower($result['model']),
					'detail_popup_tracking_id_for_ga' => 'wsb-detailpopup-from-list-' . strtolower($result['model']),
					'href' => $this->url->rewrite_keyword('product_id', $result['product_id'], 'SSL'),
					'original' => $result_images[0]['original'],
					'popup' => $result_images[0]['popup'],
					'pan_detail' => $result_images[0]['pan_detail'],
					'thumb' => $result_images[0]['thumb'],
					'image' => $result_images[0]['image'],
					'popup_width' => $result_images[0]['popup_width'],
					'popup_height' => $result_images[0]['popup_height'],
					'pan_width' => $result_images[0]['pan_width'],
					'pan_height' => $result_images[0]['pan_height'],
					'additional_width' => $result_images[0]['additional_width'],
					'additional_height' => $result_images[0]['additional_height'],
					'width' => $result_images[0]['width'],
					'height' => $result_images[0]['height'],
					'img_vertical' => $result_images[0]['img_vertical'],
					'images' => $result_images,
					'tags' => $tags_result,
					'filters' => $filters,
					'model' => $result['model'],
					'points' => $result['points'],
					'sold_out' => $result['sold_out'],
					'cod_available' => $result['cod_available'],
					'selling_price' => $this->currency->format($result['selling_price']),
					'tax_per_piece' => $this->currency->format($result['tax_per_piece']),
					'custom_store_selling_price' => sprintf($this->language->get('text_per_piece'), $custom_store_selling_price),
					'custom_store_product_id' => $custom_store_product_id,
					'custom_store_product_id' => $custom_store_product_id,
					'custom_store_product_href' => $custom_store_product_href,
					'preload_img_string' => $preload_img_string,
					'real_pic' => 0,
					'options' => $product_options,
					'is_combo' => $result['is_combo'],
				);
			}
		}

		$data['menus'] = array();
		if (isset($this->request['search']) && count($data['products']) == 0) {
			$menus = $this->model_catalog_category->menus('Desktop', 0, 'desktop-category');
			foreach ($menus as $menu) {
				$category_image = $this->model_tool_image->resize($menu['image'], $menu['image_width'], $menu['image_height']);

				$child_menu_data = array();

				if ($menu['link_type'] == 'category') {

					$href = $this->url->link('product/category', 'path=' . $menu['value'], 'SSL');
					$mobile_href = $this->url->rewrite_keyword('category_id', $menu['value'], 'SSL');

				} else if ($menu['link_type'] == 'page') {
					$href = $this->url->link('information/information', 'information_id=' . $menu['value'], 'SSL');
					$mobile_href = $this->url->rewrite_keyword('information_id', $menu['value'], 'SSL');
				} else {
					$href = html_entity_decode($menu['value'], ENT_QUOTES, 'UTF-8');
					$mobile_href = html_entity_decode($menu['value'], ENT_QUOTES, 'UTF-8');
				}

				$child_menus = $this->model_catalog_category->getSubMenu($menu['id']);

				//child menu
				foreach ($child_menus as $child_menu) {

					if ($child_menu['link_type'] == 'category') {
						$child_href = $this->url->link('product/category', 'path=' . $child_menu['value'], 'SSL');
						$child_mobile_href = $this->url->rewrite_keyword('category_id', $child_menu['value'], 'SSL');

					} else if ($menu['link_type'] == 'page') {
						$child_href = $this->url->link('information/information', 'information_id=' . $child_menu['value'], 'SSL');
						$child_mobile_href = $this->url->rewrite_keyword('information_id', $child_menu['value'], 'SSL');
					} else {
						$child_href = html_entity_decode($child_menu['value'], ENT_QUOTES, 'UTF-8');
						$child_mobile_href = html_entity_decode($child_menu['value'], ENT_QUOTES, 'UTF-8');
					}

					$child_menu_data[] = array(
						'id' => $child_menu['id'],
						'menu_id' => $menu['menu_id'],
						'parent_id' => $menu['parent_id'],
						'link_title' => $child_menu['link_title'],
						'position' => $child_menu['position'],
						'status' => $child_menu['status'],
						'store_id' => $child_menu['store_id'],
						'href' => $child_href,
						'mobile_href' => $child_mobile_href,
					);
				}
				//end child menu

				$data['menus'][] = array(
					'id' => $menu['id'],
					'menu_id' => $menu['menu_id'],
					'link_title' => $menu['link_title'],
					'position' => $menu['position'],
					'status' => $menu['status'],
					'store_id' => $menu['store_id'],
					'image' => $category_image,
					'href' => $href,
					'mobile_href' => $mobile_href,
					'children' => $child_menu_data,
				);

			}
		}

		$data['sorts'] = array();
		$data['sorts'][] = array(
			'text' => $this->language->get('text_default'),
			'value' => 'p.rand',
			'href' => $this->url->link('product/category', 'path=' . $this->request['path'] . $url, 'SSL'),
			'query_string' => 'sort_order&order=ASC',
			'selected' => ($sort == 'sort_order') ? 'active' : '',
		);

		$data['sorts'][] = array(
			'text' => $this->language->get('text_most_recent'),
			'value' => 'p.date_added-DESC',
			'href' => $this->url->link('product/category', 'path=' . $this->request['path'] . '&sort=p.date_added&order=DESC' . $url, 'SSL'),
			'query_string' => 'p.date_added&order=DESC',
			'selected' => ($sort == 'p.date_added') ? 'active' : '',
		);

		$data['sorts'][] = array(
			'text' => $this->language->get('text_price_asc'),
			'value' => 'p.selling_price-ASC',
			'href' => $this->url->link('product/category', 'path=' . $this->request['path'] . '&sort=p.selling_price&order=ASC' . $url, 'SSL'),
			'query_string' => 'p.selling_price&order=ASC',
			'selected' => ($sort == 'p.selling_price' && $order == 'ASC') ? 'active' : '',
		);

		$data['sorts'][] = array(
			'text' => $this->language->get('text_price_desc'),
			'value' => 'p.selling_price-DESC',
			'href' => $this->url->link('product/category', 'path=' . $this->request['path'] . '&sort=p.selling_price&order=DESC' . $url, 'SSL'),
			'query_string' => 'p.selling_price&order=DESC',
			'selected' => ($sort == 'p.selling_price' && $order == 'DESC') ? 'active' : '',
		);

		$data['sorts'][] = array(
			'text' => $this->language->get('text_discount_desc'),
			'value' => 'p.special_price-DESC',
			'href' => $this->url->link('product/category', 'path=' . $this->request['path'] . '&sort=p.special_price&order=DESC' . $url, 'SSL'),
			'query_string' => 'p.special_price&order=DESC',
			'selected' => ($sort == 'p.special_price' && $order == 'DESC') ? 'active' : '',
		);

		$data['limit'] = '28';
		$data['filter'] = '';
		$data['price_filter'] = '';
		$data['option'] = '';
		$data['rating_filter'] = '';

		if (isset($this->request['filter'])) {
			$url .= '&filter=' . $this->request['filter'];
			$filters = explode(",", $this->request['filter']);
			$data['filters'] = $this->model_catalog_category->getSelectFilters($filters);
			$text = array();

			if (!empty($data['filters'])) {
				foreach ($data['filters'] as $filters_to_comment) {
					if (is_numeric($filters_to_comment['name'])) {
						$text[] = $filters_to_comment['name'] . " size";
					} else {
						$text[] = $filters_to_comment['name'];
					}
				}

				$implode_text = implode(', ', $text);
				if (empty($results) && $product_total < 1) {
					$data['no_products_for_applied_filters'] = sprintf($this->language->get('no_products_for_applied_filters'), $implode_text);
				}
			}
		}

		if (isset($this->request['price_filter']) && $this->request['price_filter'] != 'all') {
			$url .= '&price_filter=' . $this->request['price_filter'];
			$price_filter = $this->request['price_filter'];
			$data['price_filter'] = $price_filter;
		}
		if (isset($this->request['option'])) {
			$url .= '&option=' . $this->request['option'];
			$options = explode(",", $this->request['option']);
			$data['options'] = $this->model_catalog_category->getSelectOptions($options);
		}
		if (isset($this->request['rating_filter'])) {
			$url .= '&rating_filter=' . $this->request['rating_filter'];
			$rating_filter = $this->request['rating_filter'];
			$data['rating_filter'] = $rating_filter;
		}

		// set custom meta data
		$this->load->model('catalog/custom_url');
		$custom_url_data = $this->model_catalog_custom_url->getMobileCustomUrlInfo($data['path_keyword']);
		if (!empty($custom_url_data['meta_title'])) {
			$data['meta_title'] = $custom_url_data['meta_title'];
			$data['meta_description'] = $custom_url_data['meta_description'];
			$data['meta_keywords'] = $custom_url_data['keyword'];
		}

		if (isset($this->request['price_filter']) && $this->request['price_filter'] != 'all') {
			$url .= '&price_filter=' . $this->request['price_filter'];
			$price_filter = $this->request['price_filter'];
			$data['price_filter'] = $price_filter;
		}
		if (isset($this->request['option'])) {
			$url .= '&option=' . $this->request['option'];
			$options = explode(",", $this->request['option']);
			$data['options'] = $this->model_catalog_category->getSelectOptions($options);
		}
		if (isset($this->request['rating_filter'])) {
			$url .= '&rating_filter=' . $this->request['rating_filter'];
			$rating_filter = $this->request['rating_filter'];
			$data['rating_filter'] = $rating_filter;
		}
		$data['promotion'] = array();
		$parentCategory_id = '';
		$parts = explode('_', (string) $this->request['path']);
		if (!empty($parts[0])) {
			$parentCategory_id = $parts[0];
		}
		// =============menu Promotion work start================//
		if ($parentCategory_id != '0' && $parentCategory_id != '') {
			$promotion_vals = $this->model_catalog_category->promotion($parentCategory_id, 'category', $store_id);
			if (!empty($promotion_vals['promotion'])) {
				$data['promotion'] = unserialize($promotion_vals['promotion']);
			}
		}
		$data['store_id'] = $store_id;
		// =============menu Promotion work end================//

		$data['path'] = $this->request['path'];
		$data['page'] = $page;
		$data['product_total'] = $product_total;
		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function filter() {
		$this->load->model('setting/setting');
		$this->load->model('catalog/category');
		$this->load->language('product/category');

		$sellers = '';
		// Check which store we want to show the data for mobile
		if (isset($this->request['custom_store']) && !empty($this->request['custom_store'])) {
			$custom_store = $this->request['custom_store'];
		} else if (isset($this->session->data['custom_store']) && $this->session->data['custom_store'] != '') {
			$custom_store = $this->session->data['custom_store'];
		} else {
			$custom_store = 'set';
		}

		if (isset($this->request['client_preferences'])) {
			$client_preferences = $this->request['client_preferences'];
		} else {
			$client_preferences = '';
		}

		if (isset($this->request['search'])) {
			$search = trim(html_entity_decode($this->request['search']));
		} else {
			$search = '';
		}

		// to get the store30 products(i.e. store prodcts added before 30 days)
		if (isset($this->request['store_product'])) {
			$store_product = (int) $this->request['store_product'];
		} else {
			$store_product = 0;
		}

		if (!empty($this->request['purchase_days'])) {
			$purchase_days = (int) $this->request['purchase_days'];
			$date = date_create();
			date_sub($date, date_interval_create_from_date_string($purchase_days . " days"));
			$date_added_less_than = date_format($date, "Y-m-d\TH:i:s\Z");
		} else {
			$purchase_days = 0;
			$date_added_less_than = '';
		}

		// Check if any filters are set in the page
		if (isset($this->request['filter'])) {
			$filter = $this->request['filter'];
		} else {
			$filter = '';
		}
		// Check if any price range has been specified by the user
		if (isset($this->request['price_filter']) && $this->request['price_filter'] != 'all') {
			$price_filter = $this->request['price_filter'];
		} else {
			$price_filter = '';
		}

		// Check if any option has been selected by the user
		if (isset($this->request['option'])) {
			$option = $this->request['option'];
		} else {
			$option = '';
		}

		// Check if any rating has been selected by the user
		if (isset($this->request['rating_filter'])) {
			$rating_filter = $this->request['rating_filter'];
		} else {
			$rating_filter = null;
		}

		// If any sort specified by the user
		if (isset($this->request['sort']) && (!empty($this->request['sort']))) {
			$sort = $this->request['sort'];
		} else {
			$sort = 'sort_order';
		}

		// Handpicked ids, if we are in sort_order mode
		if (isset($this->request['handpicked_ids'])) {
			$handpicked_ids = $this->request['handpicked_ids'];
		} else {
			$handpicked_ids = '';
		}
		// product total for page 2 to 5
		if (isset($this->request['product_total'])) {
			$filter_product_total = $this->request['product_total'];
		} else {
			$filter_product_total = 0;
		}

		// product total for page 2 to 5
		if (isset($this->request['random_string'])) {
			$random_string = trim($this->request['random_string']);
		} else {
			$random_string = '';
		}

		if (isset($this->request['order'])) {
			$order = $this->request['order'];
		} else {
			$order = 'ASC';
		}

		// for store 30 products
		if (!empty($store_product)) {
			$sort = 'date_added';
			$order = 'DESC';
		}

		if (isset($this->request['page'])) {
			$page = $this->request['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request['limit'])) {
			$limit = $this->request['limit'];
		} else {
			$limit = $this->config->get('config_product_limit');
		}

		if (isset($this->request['stock_filter']) && $this->request['stock_filter'] == 1) {
			$show_out_of_stock = $this->request['stock_filter'];
		} else {
			$show_out_of_stock = 0;
		}

		$data['stock_filter'] = $show_out_of_stock;

		if (isset($this->request['clearance_sale'])) {
			$filter_sale = $this->request['clearance_sale'];
		} else {
			$filter_sale = '';
		}

		if (isset($this->request['store_code']) && $this->request['store_code'] != 'undefined') {
			$data['store_code'] = $this->request['store_code'];
		} else {
			$data['store_code'] = '';
		}

		if (isset($this->request['last_filter_action'])) {
			$last_filter_action = $this->request['last_filter_action'];
		} else {
			$last_filter_action = '';
		}

		if (isset($this->request['csv_req'])) {
			$csv_req = $this->request['csv_req'];
		} else {
			$csv_req = 0;
		}

		if (isset($this->request['location'])) {
			$location = $this->request['location'];
		} else {
			$location = '';
		}

		$show_exclusive_only = 0;
		if (isset($_COOKIE['exclusive_voucher_code']) && isset($this->request['is_exclusive']) && isset($this->request['is_exclusive']) == 1) {
			$store_code = SalesStaff::checkStoreVoucher($this->db, $_COOKIE['exclusive_voucher_code']);
			if ($store_code) {
				$show_exclusive_only = 1;
			}
			$data['is_exclusive'] = $this->request['is_exclusive'];
		} else {
			$data['is_exclusive'] = 0;
		}

		$url = '';
		$search_all_products = 0;
		if (isset($this->request['search_all_products']) && !empty($this->request['search_all_products'])) {
			$url .= '&search_all_products=' . $this->request['search_all_products'];
			$search_all_products = $this->request['search_all_products'];
			if ($sort == 'sort_order') {
				$sort = '';
			}
		}

		if (isset($this->request['path']) && !empty($this->request['path'])) {
			$data['path_keyword'] = $this->request['path'];
			$this->request['path'] = $this->url->rewrite_value($this->request['path']);

			if (isset($this->request['sort'])) {
				$url .= '&sort=' . $this->request['sort'];
			}

			if (isset($this->request['order'])) {
				$url .= '&order=' . $this->request['order'];
			}

			if (isset($this->request['limit'])) {
				$url .= '&limit=' . $this->request['limit'];
			}

			$path = '';
			$parts = explode('_', (string) $this->request['path']);
			$category_id = (int) array_pop($parts);
		} else {
			$category_id = 0;
			$data['path_keyword'] = '';
		}

		$store_id = $this->config->get('config_store_id');
		$mbileobj = new Mobile_Detect_Class();
		$is_mobile_site = $mbileobj->isMobile();

		if ($is_mobile_site && $category_id > 0) {
			$parent_category = $this->model_catalog_category->getParentCategory($category_id);
			$results = $this->model_catalog_category->getCategories($parent_category['category_id']);
		} else {
			$results = $this->model_catalog_category->getCategories($category_id);
		}

		foreach ($results as $result) {
			$filter_data = array(
				'filter_category_id' => $result['category_id'],
				'filter_sub_category' => true,
			);
		}

		$request_uri = substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
		$data['qry_string'] = parse_url($request_uri, PHP_URL_QUERY);

		$path_only = parse_url($request_uri, PHP_URL_PATH);
		//dev url hook, remove dev/ as it is already in config_url
		if (strpos($path_only, 'staging/') !== false) {
			$path_only = substr($path_only, 8, strlen($path_only));
		}

		//$data['url_path'] = $this->config->get('config_url'). $path_only;
		$data['url_path'] = $path_only;
		$data['show_limit'] = $limit;
		$data['products'] = array();

		// Setting filters to get the products for
		$filter_data = array(
			'filter_name' => $search,
			'client_preferences' => $client_preferences,
			'filter_category_id' => (isset($category_id)) ? $category_id : "",
			'filter_filter' => $filter,
			'option' => $option,
			'price_filter' => $price_filter,
			'show_out_of_stock' => $show_out_of_stock,
			'sort' => $sort,
			'order' => $order,
			'last_filter_action' => $last_filter_action,
			'csv_req' => $csv_req,
			'page' => $page,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
			'seller' => $sellers,
			'custom_store' => $custom_store,
			'handpicked_ids' => $handpicked_ids,
			'product_total' => $filter_product_total,
			'random_string' => $random_string,
			'filter_special' => $filter_sale,
			'store_code' => $data['store_code'],
			'custom_store' => isset($this->session->data['custom_store']) ? $this->session->data['custom_store'] : 'set',
			'show_exclusive_only' => $show_exclusive_only,
			'facets' => true,
			'is_search' => 1,
			'is_facet' => 1,
			'search_all_products' => $search_all_products,
			'user_id' => isset($this->session->data['customer_id']) && (!empty($this->session->data['customer_id'])) ? $this->session->data['customer_id'] : $this->customer->getId(),
			'date_added_less_than' => $date_added_less_than,
			'store_product' => $store_product,
			'filter_only' => 1,
			'call_from' => 'app',
		);
		// If a valid rating_filter is provided
		if (!empty($rating_filter) and $rating_filter != 'all') {
			$filter_data['rating_filter'] = $rating_filter;
		}
		// location wise product_list
		if (!empty($location)) {
			$filter_data['location'] = $location;
		}

		// Getting only instock products
		$newFilter = array();
		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
			if ((int) $this->request['page'] == 1) {
				$filter_data['clicked_filter'] = $this->request['filter'];
			}
			$solr = new SolrProduct($this);
			$results_solr = $solr->getProductFromSolr($filter_data);

			if (isset($category_id) && $category_id != '0') {
				if (isset($results_solr['filter_facets']['filters'])) {
					$unique_category_filter_group_id = $this->model_catalog_category->getUniqueCategoryFilterGroupId($category_id);
					foreach ($unique_category_filter_group_id as $key => $value) {
						if (isset($results_solr['filter_facets']['filters'][$value['filter_group_id']]) && count($results_solr['filter_facets']['filters'][$value['filter_group_id']]) > 0) {
							$newFilter[$value['filter_group_id']] = $results_solr['filter_facets']['filters'][$value['filter_group_id']];
						}
					}
				}
				if (count($newFilter) > 0) {
					$remain_array = array_diff_key($results_solr['filter_facets']['filters'], $newFilter);
					$results_solr['filter_facets']['filters'] = array_values($newFilter);
					if (count($remain_array) > 0) {
						$combine_array = $newFilter + $remain_array;
						$results_solr['filter_facets']['filters'] = array_values($combine_array);
					}
				}
			}

			$data['filter_facets'] = '';
			$data['price_with_currency'] = array(
				'symbol' => $this->currency->getSymbolLeft(),
				'minimum_price' => 0,
				'maximum_price' => 0,
			);
			$data['filter_rating'] = array(
				'5.0' => 0,
				'4.0' => 0,
				'3.0' => 0,
			);

			if (isset($results_solr['filter_facets'])) {
				$data['filter_facets_solr'] = $results_solr['filter_facets'];

				if (isset($results_solr['filter_facets']['filters'])) {
					//ksort($results_solr['filter_facets']['filters']);
					$data['filter_facets'] = $results_solr['filter_facets'];
					$data['filter_facets']['filters'] = array_values($data['filter_facets']['filters']);
				}

				if (isset($results_solr['filter_facets']['price'])) {
					$data['price_with_currency'] = array(
						'symbol' => $this->currency->getSymbolLeft(),
						'minimum_price' => intval($results_solr['filter_facets']['price'][0]),
						'maximum_price' => ceil($results_solr['filter_facets']['price'][1]),
						'price_value' => array('min' => intval($results_solr['filter_facets']['price'][0]), 'max' => ceil($results_solr['filter_facets']['price'][1]),
						),
					);

					// set search filter
					if (isset($this->request['price_filter']) && $this->request['price_filter'] != 'all') {
						$res = explode("-", $this->request['price_filter']);
						$data['price_with_currency']['price_value']['min'] = isset($res[0]) ? $res[0] : 0;
						$data['price_with_currency']['price_value']['max'] = isset($res[1]) ? $res[1] : 0;
						if ($data['price_with_currency']['maximum_price'] == 0) {
							$data['price_with_currency']['minimum_price'] = isset($res[0]) ? $res[0] : 0;
							$data['price_with_currency']['maximum_price'] = isset($res[1]) ? $res[1] : 0;
						}
					}

				}

				if (isset($results_solr['filter_facets']['rating'])) {
					$data['filter_rating'] = $results_solr['filter_facets']['rating'];
				}

			}

		} else {
			$product_total = $this->model_catalog_product->getTotalInStockProducts($filter_data);
			$results = $this->model_catalog_product->getInStockProducts($filter_data);

			// searching data saved in searched_term
			$has_results = count($results);
			if ($has_results > 0) {
				$has_results = 1;
			} else {
				$has_results = 0;
			}
			$this->model_catalog_product->setSearchedKeyword($search, $has_results);
			$data['filter_facets'] = '';
		}

		if (isset($this->request['rating_filter'])) {
			$data['rating_filter'] = $this->request['rating_filter'];
		}

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;

	}

}