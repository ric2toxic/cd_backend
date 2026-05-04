<?php
require_once __DIR__ . '/system.php';
require_once DIR_SYSTEM . 'library/cart.php';
require_once DIR_SYSTEM . 'library/seller/seller_info.php';
require_once DIR_SYSTEM . 'library/solr/product.php';
require_once DIR_SYSTEM . 'library/solr/model_solr_product.php';

class HeaderController extends SystemController {
	public function __construct($params) {

		parent::__construct($params);
		// Tax
		//$this->registry->set('tax', new Tax($this->registry));
		//$this->registry->set('customer', new Customer($this->registry));
		$this->registry->set('cart', new Cart($this->registry));
	}
	/**
	 * customer login
	 */
	public function menu() {
		$this->load->model('catalog/category');

		if (isset($this->request['preferences'])) {
			$preferences = $this->request['preferences'];
		} else {
			$preferences = '';
		}

		if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
			$menus = $this->model_catalog_category->getMenu('Desktop-International');
		} else {
			$menus = $this->model_catalog_category->getMenu('Desktop');
		}

		$menus_data = array();
		$rs = array();
		if (isset($menus) && !empty($menus)) {
			$i = 1;
			foreach ($menus as $menu_categories) {
				if ($menu_categories['link_type'] == 'category') {
					$menu_categories['href'] = $this->url->link('product/category', 'path=' . $menu_categories['value'], 'SSL');
				} else if ($menu_categories['link_type'] == 'page') {
					$menu_categories['href'] = $this->url->link('information/information', 'information_id=' . $menu_categories['value'], 'SSL');
				} else {
					$menu_categories['href'] = html_entity_decode($menu_categories['value'], ENT_QUOTES, 'UTF-8');
				}

				if ($menu_categories['link_type'] == 'megamenu') {
					$menu_categories['name'] = html_entity_decode($menu_categories['megamenu']);
					$menu_categories['megamenu'] = 1;

					if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
						$menu_categories['name'] = str_replace("in_price", "in_price hide", $menu_categories['name']);
					} else {
						$menu_categories['name'] = str_replace("co_price", "co_price hide", $menu_categories['name']);
					}
				} else {
					$menu_categories['name_for_id'] = preg_replace('/\s+/', '-', strtolower($menu_categories['link_title']));
				}

				if ($menu_categories['parent_id'] == 0) {
					$menus_data['parent'][] = $menu_categories;
				} else {
					$menus_data['children'][$menu_categories['parent_id']][] = $menu_categories;
				}

			}
		}

		if (isset($menus_data['parent'])) {
			$i = 0;
			$main_menus = 0;
			foreach ($menus_data['parent'] as $rs) {
				if (isset($menus_data['children'][$rs['id']])) {
					$child = $menus_data['children'][$rs['id']];
					$rs['children'] = $child;
				}

				if (isset($rs['children'])) {
					foreach ($rs['children'] as $key => $sub_child) {
						if (isset($menus_data['children'][$sub_child['id']])) {
							$child2 = $menus_data['children'][$sub_child['id']];
							$rs['children'][$key]['children'] = $child2;
						}

						if (isset($rs['children'][$key]['children'])) {
							foreach ($rs['children'][$key]['children'] as $key3 => $sub_child3) {
								if (isset($menus_data['children'][$sub_child3['id']])) {
									$child3 = $menus_data['children'][$sub_child3['id']];
									$rs['children'][$key]['children'][$key3]['children'] = $child3;
									if ($child3[0]['link_type'] == 'megamenu') {
										$rs['children'][$key]['children'][$key3]['child_link_type'] = 'megamenu';
									} else {
										$rs['children'][$key]['children'][$key3]['child_link_type'] = 'category';
									}
								}}
						}

					}
				}

				if ($rs['value'] == $preferences && $rs['parent_id'] == 0) {$main_menus = $i;}

				$data['menus'][$i] = $rs;
				$i++;
			}
		}

		$data['preference_menu'][] = $data['menus'][$main_menus];
		unset($data['menus'][$main_menus]);
		$data['menus'] = array_values($data['menus']);
		$data['home_link'] = $this->url->link('common/home', '', 'SSL');

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;

		return $this->data_packet;

	}

	public function mobile_menu() {
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
			$menus = $this->model_catalog_category->getMenu('Desktop-International');
		} else {
			$menus = $this->model_catalog_category->getMenu('Desktop');
		}

		$menus_data = array();
		$rs = array();
		if (isset($menus) && !empty($menus)) {
			$i = 1;
			foreach ($menus as $menu_categories) {

				$menu_categories['image'] = $this->model_tool_image->resize($menu_categories['image'], $this->config->get('config_image_category_height'), $this->config->get('config_image_category_width'));

				if ($menu_categories['link_type'] == 'category') {
					$menu_categories['href'] = $this->url->rewrite_keyword('category_id', $menu_categories['value']);
				} else if ($menu_categories['link_type'] == 'page') {
					$menu_categories['href'] = $this->url->rewrite_keyword('information_id', $menu_categories['value']);
				} else {
					$menu_categories['href'] = html_entity_decode($menu_categories['value'], ENT_QUOTES, 'UTF-8');
				}

				if ($menu_categories['link_type'] == 'megamenu') {
					$menu_categories['name'] = html_entity_decode($menu_categories['megamenu']);
					$menu_categories['megamenu'] = 1;

					if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
						$menu_categories['name'] = str_replace("in_price", "in_price hide", $menu_categories['name']);
					} else {
						$menu_categories['name'] = str_replace("co_price", "co_price hide", $menu_categories['name']);
					}
				} else {
					$menu_categories['name_for_id'] = preg_replace('/\s+/', '-', strtolower($menu_categories['link_title']));
				}

				if ($menu_categories['parent_id'] == 0) {
					$menus_data['parent'][] = $menu_categories;
				} else {
					$menus_data['children'][$menu_categories['parent_id']][] = $menu_categories;
				}

			}
		}

		if (isset($menus_data['parent'])) {

			$i = 0;
			$main_menus = 0;
			foreach ($menus_data['parent'] as $rs) {
				if (isset($menus_data['children'][$rs['id']])) {
					$child = $menus_data['children'][$rs['id']];
					$rs['children'] = $child;
				}

				if (isset($rs['children'])) {
					foreach ($rs['children'] as $key => $sub_child) {
						if (isset($menus_data['children'][$sub_child['id']])) {
							$child2 = $menus_data['children'][$sub_child['id']];
							$rs['children'][$key]['children'] = $child2;
						}

						if (isset($rs['children'][$key]['children'])) {
							foreach ($rs['children'][$key]['children'] as $key3 => $sub_child3) {
								if (isset($menus_data['children'][$sub_child3['id']])) {
									$child3 = $menus_data['children'][$sub_child3['id']];
									$rs['children'][$key]['children'][$key3]['children'] = $child3;
								}}
						}
					}
				}
				$data['menus'][$i] = $rs;
				$i++;
			}

		}

		$data['home_link'] = $this->url->link('common/home', '', 'SSL');

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;

	}

	public function custom_store() {
		$change_store = 0;
		if (isset($this->request['change_store'])) {
			$change_store = $this->request['change_store'];
		}
		if ($change_store == 1) {
			$this->load->model('setting/store');
			if (isset($this->session->data['custom_store']) && !empty($this->session->data['custom_store'])) {
				if ($this->session->data['custom_store'] == 'single') {

					$this->session->data['custom_store'] = 'set';
					$data['store_switch'] = array(
						'label' => $this->language->get('text_single_store'),
					);
				} elseif ($this->session->data['custom_store'] == 'set') {
					$this->session->data['custom_store'] = 'single';
					$data['store_switch'] = array(
						// 'url'      => $result['url'].$url_path.$build_query_string,
						'label' => $this->language->get('text_wholesale_set_store'),
					);
				}
			} else {
				$this->session->data['custom_store'] = 'single';
				$data['store_switch'] = array(
					// 'url'      => $result['url'].$url_path.$build_query_string,
					'label' => $this->language->get('text_single_store'));
			}
		}
		if (isset($this->session->data['custom_store'])) {
			if ($this->session->data['custom_store'] == 'single') {
				$custom_store = "Go On Wholesale Set Store";} else {
				$custom_store = "Go On Singles Store";
			}
		} else { $custom_store = "Go On Singles Store";}

		$this->data_packet->data = $custom_store;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function custom_store_mobile() {
		$this->load->model('setting/store');
		if (isset($this->request['custom_store']) && !empty($this->request['custom_store'])) {
			if ($this->request['custom_store'] == 'single') {
				$data['custom_store'] = 'set';
				$data['store_switch'] = 'single';
				$data['store_switch_label'] = 'Go To Singles Store';
			} elseif ($this->request['custom_store'] == 'set') {
				$data['custom_store'] = 'single';
				$data['store_switch'] = 'set';
				$data['store_switch_label'] = "Go To Wholesale Set Store";
			}
		} else {
			$data['custom_store'] = 'set';
			$data['store_switch'] = 'single';
			$data['store_switch_label'] = 'Go To Singles Store';
		}

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function logo() {
		$this->load->model('tool/image');

		$data['meta_title'] = $this->config->get('config_meta_title');
		$data['meta_description'] = $this->config->get('config_meta_description');
		$data['meta_keywords'] = $this->config->get('config_meta_keyword');

		$data['name'] = $this->config->get('config_name');
		$data['logo'] = $this->model_tool_image->getOriginalImage($this->config->get('config_logo'));
		$data['mobile_logo'] = $this->model_tool_image->getOriginalImage('mobile_logo.png');
		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));
		$data['home'] = $this->url->link('common/home', '', 'SSL');
		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function wishlist() {

		$data['logged'] = $this->customer->isLogged();
		$total = $this->customer->getTotalWishlists();
		$data['wishlist_total'] = $total;
		$data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function cart() {
		$total_sets = $this->cart->totalCartProductsCount();
		$data['total_in_cart'] = $total_sets;
		$data['cart_url'] = $this->url->link('checkout/cart', '', 'SSL');
		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function userlogin() {
		$this->load->language('common/header');

		$customer_id = $this->customer->getId();
		$data['logged'] = $this->customer->isLogged();
		$data['cust_name'] = $this->customer->getFirstName();
		$data['text_my_orders'] = $this->language->get('text_my_orders');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$data['store_locator'] = $this->url->link('information/storelocator', '', 'SSL');
		$data['credit_application'] = $this->url->link('account/credit_application', '', 'SSL');

		//Check is customer a seller or not
		$data['is_seller'] = SellerInfo::isCustomerSeller($this->db, $customer_id);
		$data['manufacturer_link'] = './seller_panel/#/login';
		$data['manufacturer_dashboard_link'] = './seller_panel/#/profile';

		//$data['manufacturer_link'] = $this->url->link('common/seller_home', '', 'SSL');
		//$data['manufacturer_dashboard_link'] = $this->url->link('seller/account-dashboard', '', 'SSL');
		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function userloginMobile() {

		$this->load->language('common/header');
		$this->load->language('common/footer');
		$this->load->model('account/customer');

		if ($this->__userAuthentication()) {
			$customer_id = $this->customer->getId();
			$data['logged'] = $this->customer->isLogged();
			$data['cust_name'] = $this->customer->getFirstName();
			$data['is_seller'] = SellerInfo::isCustomerSeller($this->db, $customer_id);
			$data['is_dropshipper'] = $this->model_account_customer->getisdropshipper($customer_id);
			$data['credit_application'] = $this->url->link('account/credit_application', '', 'SSL');
			$data['account_statement'] = true;
			if (in_array($customer_id, AC_SMT_BLOCK_CUSTOMERS)) {
				$data['account_statement'] = false;
			}

		} else {
			$data['logged'] = false;
			$data['cust_name'] = '';
			$data['is_seller'] = false;
			$data['is_dropshipper'] = false;
			$data['credit_application'] = false;
		}

		$this->load->model('setting/store');
		$result = $this->model_setting_store->getInternationalSwitch();
		$data['is_redirect'] = $result['is_redirect'];
		$data['redirect_url'] = $result['redirect_url'];
		$data['user_country'] = $result['user_country'];

		$data['text_my_orders'] = $this->language->get('text_my_orders');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$data['store_locator'] = $this->url->link('information/storelocator', '', 'SSL');

		//Check is customer a seller or not
		$data['manufacturer_link'] = './seller_panel/#/login';
		$data['manufacturer_dashboard_link'] = './seller_panel/#/profile';

		$data['manufacturer_dashboard_link'] = './seller_panel/#/profile';
		$data['text_whatsappw_no'] = $this->language->get('text_whatsappw_no');
		$data['text_whatsappw_no_msg_text'] = $this->language->get('text_whatsappw_no_msg_text');

		if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
			$data['international_store'] = 1;
		} else {
			$data['international_store'] = 0;
		}

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function autoComplete() {
		$get_response = array();
		$solr = new SolrProduct($this);
		$keyword = $this->request['term'];
		// $get_response = $solr->getAutoSuggestions($keyword);
		// return $get_response;
		$this->data_packet->message = 'set session successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
		exit();
	}

	public function currencyList() {
		$this->load->model('localisation/currency');

		$currency_list = $this->model_localisation_currency->getCurrencies();
		// unset default INR currency
		unset($currency_list['INR']);

		$country_code_to_currency_title = array();
		$country_code_to_currency_code = array();
		foreach ($currency_list as $currency) {
			$country_code_to_currency_title[$currency['country_code']] = $currency['text'];
			$country_code_to_currency_code[$currency['country_code']] = $currency['code'];
		}

		$data['currency_list'] = $currency_list;
		$data['country_code_to_currency_title'] = $country_code_to_currency_title;
		$data['country_code_to_currency_code'] = $country_code_to_currency_code;

		if (isset($this->session->data['currency'])) {
			$data['currency'] = $this->session->data['currency'];
		} else {
			$data['currency'] = $this->config->get('config_currency');
		}

		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function setCurrencySession() {

		if (isset($this->request['currency'])) {
			$currency = $this->request['currency'];
			//set session
			if (!isset($this->session->data['currency']) || ($this->session->data['currency'] != $currency)) {
				$this->session->data['currency'] = $currency;
			}
			//set cookies
			if (!isset($this->request->cookie['currency']) || ($this->request->cookie['currency'] != $currency)) {
				setcookie('currency', $currency, time() + 60 * 60 * 24 * 30, '/');
			}
		}
		$data['currency'] = $currency;
		$this->data_packet->data = $data;
		$this->data_packet->message = 'set session successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}

	public function search() {
		$this->load->model('catalog/category');
		$get_response = array();
		$response = array();
		$cats = $this->model_catalog_category->getHeaderCategory('Desktop');
		$data['cats'] = $cats;
		$this->data_packet->data = $data;
		$this->data_packet->message = 'data fatch successfully';
		$this->data_packet->statusCode = 200;
		return $this->data_packet;
	}
}
