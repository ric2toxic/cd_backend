<?php
require_once 'system.php';
require_once DIR_SYSTEM . 'library/solr/product.php';
require_once DIR_SYSTEM . 'library/cart.php';

class HomeController extends SystemController {
	private static $displayed = false;
	public function __construct($params) {

		parent::__construct($params);
		//$this->registry->set('customer', new Customer($this->registry));
		//$this->registry->set('currency', new Currency($this->registry));
		//$this->registry->set('tax', new Tax($this->registry));
		$this->registry->set('cart', new Cart($this->registry));
	}
	/**
	 * customer login
	 */
	public function latest_state() {
		$this->load->model('catalog/product');
		$this->load->model('account/customer');
		$this->load->language('common/language');
		$this->load->model('tool/image');

		$data['logged'] = $this->customer->isLogged();
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['list'] = $this->url->link('product/category', 'clearance_sale=1', 'SSL');
		$data['total_customers'] = $this->model_account_customer->getTotalCustomers();
		$data['total_products'] = $this->model_catalog_product->getHomePageProductsTotal();
		$data['text_users'] = $this->language->get('text_users');
		$data['text_designs'] = $this->language->get('text_designs');
		$data['text_latest_stats'] = $this->language->get('text_latest_stats');
		if ($this->customer->isLogged()) {
			$data['is_dropshipper'] = $this->model_account_customer->getisdropshipper($this->customer->isLogged());
		} else {
			$data['is_dropshipper'] = false;
		}

		$data['home_images']['app_icon'] = $this->model_tool_image->getOriginalImage('app_icon.png');
		$data['home_images']['ios_home'] = $this->model_tool_image->getOriginalImage('ios_home.png');
		$data['home_images']['Manufacturer'] = $this->model_tool_image->getOriginalImage('icons/Manufacturer.svg');
		$data['home_images']['Returns'] = $this->model_tool_image->getOriginalImage('icons/Returns.svg');
		$data['home_images']['Delivery'] = $this->model_tool_image->getOriginalImage('icons/Delivery.svg');
		$data['home_images']['Payment'] = $this->model_tool_image->getOriginalImage('icons/Payment.svg');
		$data['home_images']['camparision'] = $this->model_tool_image->getOriginalImage('camparision.jpg');
		$data['home_images']['gnrha'] = $this->model_tool_image->getOriginalImage('058gnrha.jpg');
		$data['home_images']['infographics_3'] = $this->model_tool_image->getOriginalImage('infographics_3.jpg');
		$data['home_images']['stats_img1'] = $this->model_tool_image->getOriginalImage('stats-img1.png');
		$data['home_images']['stats_img2'] = $this->model_tool_image->getOriginalImage('stats-img2.png');
		$data['home_images']['WSB_business_v1'] = $this->model_tool_image->getOriginalImage('WSB_business_v1.jpg');

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function banner() {
		$this->load->model('design/banner');
		$this->load->model('tool/image');
		$this->load->model('extension/module');

		if (isset($this->request['preferences'])) {
			$preferences = $this->request['preferences'];
		} else {
			$preferences = '';
		}

		$setting = $this->model_extension_module->getModule(27);
		$data['banners'] = array();
		if ($setting && $setting['status']) {
			if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
				$results = $this->model_design_banner->getBanner($setting['international_desktop_banner_id'], $preferences);
			} else {
				$results = $this->model_design_banner->getBanner($setting['banner_id'], $preferences);

			}

			foreach ($results as $result) {
				//if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'banner_id' => $result['banner_id'],
					'title' => $result['title'],
					'target_blank' => $result['target_blank'],
					'link' => html_entity_decode($result['link']),
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
					'img_width' => $setting['width'],
					'img_height' => $setting['height'],
				);
				//}
			}
		}
		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function latest_product() {
		$data['logged'] = $this->customer->isLogged();
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');
		$this->load->language('module/latest');

		if ($_SERVER['HTTPS']) {
			$static_content_url = STATIC_CONTENT_URL_SSL;
		} else {
			$static_content_url = STATIC_CONTENT_URL;
		}

		if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
			$menu_type = 'Desktop-International';
		} else {
			$menu_type = 'Desktop';
		}

		if (isset($this->request['preferences'])) {
			$preferences = $this->request['preferences'];
			$preferences = $this->model_catalog_category->getAllMenuIds($preferences, $menu_type);
			$preferences = implode(",", $preferences);
		} else {
			$preferences = '';
		}

		if (isset($this->request['custom_store'])) {
			$custom_store = $this->request['custom_store'];
		} else {
			$custom_store = 'set';
		}

		$setting = array(
			'width' => 190,
			'height' => 283,
			'limit' => 11,
		);

		$limit = $this->request['limit'] ?? $setting['limit'];

		$data['heading_title'] = $this->language->get('heading_title');

		if (!empty($this->request['custom_store'])) {
			$custom_store = $this->request['custom_store'];
			$data['custom_store'] = $this->request['custom_store'];
		} else if (!empty($_COOKIE['custom_store'])) {
			$custom_store = $_COOKIE['custom_store'];
			$data['custom_store'] = $_COOKIE['custom_store'];
		} else {
			$data['custom_store'] = 'set';
			$custom_store = 'set';
		}

		// categories
		$data['products'] = array();
		$filter_data = array(
			'sort' => 'p.date_added',
			'filter_category_id' => $preferences,
			'order' => 'DESC',
			'is_latest' => 1,
			'start' => 0,
			'limit' => $limit,
			'custom_store' => $custom_store,
			'is_latest' => 1,
			'is_group' => 1,
			'group_field' => 'seller_id',
			'group_limit' => 1,
			'group_sort_data_by' => 'date_added',
		);

		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
			$solr = new SolrProduct($this);
			$results_solr = $solr->getProductFromSolr($filter_data);
			$results = $results_solr['products'];
		} else {
			$results = $this->model_catalog_product->getInStockProducts($filter_data);
		}

		if ($results) {
			foreach ($results as $result) {
				$result_images = array();
				$result_images[] = $this->get_images_data($result['image'], unserialize($result['image_dimensions']));

				$result['images'] = $this->model_catalog_product->getProductImages($result['product_id']);

				foreach ($result['images'] as $images) {
					$image_data = $this->get_images_data($images['image'], unserialize($images['image_dimensions']));
					$result_images[] = $image_data;
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price_value = $this->tax->calculate($result['selling_price'], $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
					$price = $this->currency->format($price_value);
				} else {
					$price = false;
					$price_value = '0';
				}

				if ((float) $result['special']) {
					$unit_special_price = (float) $result['special']; // ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
					$special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']), '', '', true, 0, 'frontend');
					$percent_discount = $this->model_catalog_product->calculateSpecialPriceValueInPercent($unit_special_price, $result['selling_price'], $result['tax_class_id']);
				} else {
					$percent_discount = false;
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}
				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				/*------ start fill heart after user login ----*/
				$data['fill_heart'] = $this->customer->getWishlistIcon($result['product_id']);

				$product_options = array();
				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

				//manage text units
				if ($result['base_unit'] != '') {
					if ($special) {
						$special = $special . ' / ' . $result['base_unit'];
						$price_with_unit = $price;
					} else {
						$price_with_unit = $price . ' / ' . $result['base_unit'];
					}
				} else {
					$price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
				}
				/* End by Amarat (01-sept-2017) */
				// echo '<pre>'; print_r($result); die();

				$stock_array = Cart::getProductStockStatus($result);
				if (isset($stock_array['stock']) && $stock_array['stock'] === true) {
					$stock = true;
					$quantity = $result['quantity'];
				} else {
					$stock = false;
					$quantity = 0;
				}

				$pickup_city = preg_replace('/[0-9]+/', '', $result['pickup_city']);
				$pickup_city = explode(",", $pickup_city);
				$index_count = count($pickup_city) - 1;
				if (isset($pickup_city[$index_count])) {
					$result['pickup_city'] = trim($pickup_city[$index_count]);
				} else {
					$result['pickup_city'] = trim($pickup_city[$index_count - 1]);
				}

				//## Remove wishlist functionality and icon from home page
				if ($stock == true) {
					$data['products'][] = array(
						'product_id' => $result['product_id'],
						'name' => mb_strimwidth(html_entity_decode($result['name']), 0, 22, "..."),
						'full_name' => html_entity_decode($result['name']),
						'is_sor_enabled' => $result['is_sor_enabled'],
						'sor_enabled_text' => $result['sor_enabled_text'],
						'sor_enabled_detail_text' => $result['sor_enabled_detail_text'],
						'set_description' => $result['set_description'],
						'exp_dispatch_days' => $result['exp_dispatch_days'],
						'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
						'price' => $price_with_unit,
						'price_value' => $price_value,
						'piece_in_set' => $result['piece_in_set'],
						'seller_id' => $result['seller_id'],
						'category_id' => $result['category_id'],
						'special' => $special,
						'pickup_city' => ucwords(strtolower($result['pickup_city'])),
						'percent_discount' => $percent_discount,
						'tax' => $tax,
						'rating' => $rating,
						'quantity' => $result['quantity'],
						'minimum' => $result['minimum'],
						'fill_heart' => $data['fill_heart'],
						'stock_status' => $result['stock_status'],
						'options' => $product_options,
						'href' => $this->url->rewrite_keyword('product_id', $result['product_id'], 'SSL'),
						'original' => $result_images[0]['original'],
						'thumb' => $result_images[0]['thumb'],
						'image' => $result_images[0]['image'],
						'img_vertical' => $result_images[0]['img_vertical'],
						'additional_width' => $result_images[0]['additional_width'],
						'additional_height' => $result_images[0]['additional_height'],
						'width' => $result_images[0]['width'],
						'height' => $result_images[0]['height'],
						'images' => $result_images,
						'real_pic' => 0,
					);
				}
			}
		}

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function trending_product() {
		$data['logged'] = $this->customer->isLogged();
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');
		$this->load->language('module/trending');

		if ($_SERVER['HTTPS']) {
			$static_content_url = STATIC_CONTENT_URL_SSL;
		} else {
			$static_content_url = STATIC_CONTENT_URL;
		}

		if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
			$menu_type = 'Desktop-International';
		} else {
			$menu_type = 'Desktop';
		}

		if (isset($this->request['preferences'])) {
			$preferences = $this->request['preferences'];
			$preferences = $this->model_catalog_category->getAllMenuIds($preferences, $menu_type);
			$preferences = implode(",", $preferences);
		} else {
			$preferences = '';
		}

		$setting = array(
			'width' => 190,
			'height' => 283,
			'limit' => 9,
		);

		$limit = !empty($this->request['limit']) && $this->request['limit'] > 0 ? $this->request['limit'] : $setting['limit'];
		$start = !empty($this->request['page']) && $this->request['page'] > 1 ? ($this->request['page'] - 1) * $limit : 0;

		$data['heading_title'] = $this->language->get('heading_title');

		if (!empty($this->request['custom_store'])) {
			$custom_store = $this->request['custom_store'];
			$data['custom_store'] = $this->request['custom_store'];
		} else if (!empty($_COOKIE['custom_store'])) {
			$custom_store = $_COOKIE['custom_store'];
			$data['custom_store'] = $_COOKIE['custom_store'];
		} else {
			$data['custom_store'] = 'set';
			$custom_store = 'set';
		}

		// categories
		$data['products'] = array();
		$filter_data = array(
			'sort' => 'hotness_value',
			'order' => 'DESC',
			'start' => $start,
			'limit' => $limit,
			'filter_category_id' => $preferences,
			'is_trending' => 1,
			'custom_store' => $custom_store,
			'days' => 7,
			'is_group' => 1,
			'group_field' => 'seller_id',
			'group_limit' => 1,
			'group_sort_data_by' => 'hotness_value',
		);

		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
			$solr = new SolrProduct($this);
			$results_solr = $solr->getProductFromSolr($filter_data);
			$results = $results_solr['products'];
		} else {
			$results = $this->model_catalog_product->getInStockProducts($filter_data);
		}

		if ($results) {
			foreach ($results as $result) {
				$result_images = array();
				$result_images[] = $this->get_images_data($result['image'], unserialize($result['image_dimensions']));

				$result['images'] = $this->model_catalog_product->getProductImages($result['product_id']);

				foreach ($result['images'] as $images) {
					$image_data = $this->get_images_data($images['image'], unserialize($images['image_dimensions']));
					$result_images[] = $image_data;
				}

				$seller_tax_factor = 1.0 + ((float) $result['seller_tax'] / 100.0);
				$commission_factor = 1.0 + ((float) $result['commission'] / 100.0);

				$unit_price = ceil($commission_factor * (float) ($result['price']) / $seller_tax_factor);
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price_value = $this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
					$price = $this->currency->format($price_value); // Add Selling price (05-01-2015) Ravindra Singh
				} else {
					$price = false;
					$price_value = '0';
				}

				if ((float) $result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']), '', '', true, 0, 'frontend');
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}
				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				$product_options = array();
				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

				/*------ start fill heart after user login ----*/
				$data['fill_heart'] = $this->customer->getWishlistIcon($result['product_id']);

				//manage text units
				if ($result['base_unit'] != '') {
					if ($special) {
						$special = $special . ' / ' . $result['base_unit'];
						$price_with_unit = $price;
					} else {
						$price_with_unit = $price . ' / ' . $result['base_unit'];
					}
				} else {
					$price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
				}
				/* End by Amarat (01-sept-2017) */

				$stock_array = Cart::getProductStockStatus($result);
				if (isset($stock_array['stock']) && $stock_array['stock'] === true) {
					$stock = true;
					$quantity = $result['quantity'];
				} else {
					$stock = false;
					$quantity = 0;
				}

				$pickup_city = preg_replace('/[0-9]+/', '', $result['pickup_city']);
				$pickup_city = explode(",", $pickup_city);
				$index_count = count($pickup_city) - 1;
				if (isset($pickup_city[$index_count])) {
					$result['pickup_city'] = trim($pickup_city[$index_count]);
				} else {
					$result['pickup_city'] = trim($pickup_city[$index_count - 1]);
				}

				//## Remove wishlist functionality and icon from home page
				if ($stock == true) {
					$data['products'][] = array(
						'product_id' => $result['product_id'],
						'name' => mb_strimwidth(html_entity_decode($result['name']), 0, 22, "..."),
						'full_name' => html_entity_decode($result['name']),
						'is_sor_enabled' => $result['is_sor_enabled'],
						'sor_enabled_text' => $result['sor_enabled_text'],
						'sor_enabled_detail_text' => $result['sor_enabled_detail_text'],
						'set_description' => $result['set_description'],
						'exp_dispatch_days' => $result['exp_dispatch_days'],
						'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
						'price' => $price_with_unit,
						'price_value' => $price_value,
						'piece_in_set' => $result['piece_in_set'],
						'seller_id' => $result['seller_id'],
						'category_id' => $result['category_id'],
						'special' => $special,
						'pickup_city' => ucwords(strtolower($result['pickup_city'])),
						'tax' => $tax,
						'rating' => $rating,
						'quantity' => $result['quantity'],
						'minimum' => $result['minimum'],
						'stock_status' => $result['stock_status'],
						'fill_heart' => $data['fill_heart'],
						'options' => $product_options,
						'href' => $this->url->rewrite_keyword('product_id', $result['product_id'], 'SSL'),
						'original' => $result_images[0]['original'],
						'thumb' => $result_images[0]['thumb'],
						'image' => $result_images[0]['image'],
						'img_vertical' => $result_images[0]['img_vertical'],
						'additional_width' => $result_images[0]['additional_width'],
						'additional_height' => $result_images[0]['additional_height'],
						'width' => $result_images[0]['width'],
						'height' => $result_images[0]['height'],
						'images' => $result_images,
						'real_pic' => 0,
					);
				}
			}
		}

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function category_list() {
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		$categories = $this->model_catalog_category->getCategories(0);
		$category_data = array();

		$i = 0;
		foreach ($categories as $category) {
			$category_data[$i]['category_id'] = $category['category_id'];
			$category_data[$i]['name'] = $category['name'];
			$category_data[$i]['image'] = $this->model_tool_image->getOriginalImage($category['image']);
			$category_data[$i]['images'] = $this->model_catalog_category->getCategoryImages($category['category_id']);
			$category_data[$i]['href'] = $this->url->rewrite_keyword('category_id', $category['category_id']);
			$i++;
		}

		$data['category_data'] = $category_data;
		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

}