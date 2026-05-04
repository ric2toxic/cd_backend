<?php
class Url {
	private $domain;
	private $ssl;
	private $rewrite = array();

	public function __construct($domain, $ssl = '', $registry = '') {
		$this->domain = $domain;
		$this->ssl = $ssl;
		if (is_object($registry)) {
			$this->session = $registry->get('session');
			$this->db = $registry->get('db');
			$this->config = $registry->get('config');
		}
	}

	public function addRewrite($rewrite) {
		$this->rewrite[] = $rewrite;
	}

	public function link($route, $args = '', $secure = true, $hash = '') {
		if (!$secure) {
			$url = $this->domain;
		} else {
			$url = $this->ssl;
		}

		$url .= 'index.php?route=' . $route;

		if (isset($this->session->data['ctoken']) && $this->session->data['ctoken'] != '') {
			$arr_route = explode('/', $route);

			if ($arr_route[0] == 'account' && $arr_route[1] != 'login') {
				$url .= '&ctoken=' . $this->session->data['ctoken'];
			}
		}

		if ($args) {
			$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
		}

		//if ($args) {
		//  $url .= '&' . ltrim($args, '&');
		//}

		if (!empty($this->rewrite)) {
			foreach ($this->rewrite as $rewrite) {
				$url = $rewrite->rewrite($url);
			}
		} else {
			$url = $this->api_rewrite($url);
		}

		if ($hash != '') {
			$url = $url . "#" . $hash;
		}

		return $url;
	}

	public function custom_link($custom_path, $secure = true) {
		if (!$secure) {
			$url = $this->domain;
		} else {
			$url = $this->ssl;
		}

		$url .= $custom_path;

		return $url;
	}

	public function api_rewrite($link) {

		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		foreach ($data as $key => $value) {

			if ($data['route'] == 'seller/catalog-seller') {
				$url .= '/' . $this->config->get('msconf_sellers_slug') . '/';
			}

			if (isset($data['route'])) {
				if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id') || ($data['route'] == 'seller/catalog-seller/profile' && $key == 'seller_id') || ($data['route'] == 'seller/catalog-seller/products' && $key == 'seller_id')) {
					$query = $this->db->query("SELECT keyword, query FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int) $value) . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$query_url = explode('=', $query->row['query']);

						if ($data['route'] == 'seller/catalog-seller/profile') {
							$url .= '/' . $this->config->get('msconf_sellers_slug') . '/' . $query->row['keyword'];
						} else if ($data['route'] == 'seller/catalog-seller/products') {
							$url .= '/' . $this->config->get('msconf_sellers_slug') . '/' . $query->row['keyword'] . '/products/';
						} else {

							if ($query_url[0] == 'product_id') {
								$url .= '/p/' . $query->row['keyword'];
							} else if ($query_url[0] == 'information_id') {
								$url .= '/i/' . $query->row['keyword'];
							} else {
								$url .= '/' . $query->row['keyword'];
							}

						}

						unset($data[$key]);
					}
				} elseif ($data['route'] == 'checkout/checkout') {
					$url .= '/' . 'checkout';
				} elseif ($data['route'] == 'checkout/one_page_checkout') {
					$url .= '/' . 'onepagecheckout';
				} elseif ($data['route'] == 'checkout/cart') {
					$url .= '/' . 'cart';
				} elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT keyword, query FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int) $category . "'");
						if ($query->num_rows && $query->row['keyword']) {

							$custom_query = $this->db->query("SELECT keyword, query FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($query->row['keyword']) . "' and `is_custom` = 1");

							if ($custom_query->num_rows && $custom_query->row['keyword']) {
								$url .= '/' . $custom_query->row['keyword'];
							} else {
								$url .= '/' . $query->row['keyword'];
							}

						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				} elseif ($key == 'static') {

					$query = $this->db->query("SELECT keyword, query FROM " . DB_PREFIX . "url_alias WHERE `query` = 'static=" . $this->db->escape($value) . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];
					} else {
						$url = '';

						break;
					}
					unset($data[$key]);
				}
			}
		}

		if ($url) {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			$link = str_replace('index.php?route=common/home', '', $link);
			return $link;

		}
	}

	public function rewrite_keyword($key, $value) {

		$url = '';
		if ($key == 'product_id') {$url = 'p/';}
		if ($key == 'information_id') {$url = 'i/';}
		if ($key == 'category_id') {$url = 'category/';}

		$categories = explode('_', $value);
		foreach ($categories as $category) {
			$query = $this->db->query("SELECT keyword, query FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int) $value) . "'");
			if ($query->num_rows && $query->row['keyword']) {
				$custom_query = $this->db->query("SELECT keyword, query FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($query->row['keyword']) . "' and `is_custom` = 1");

				if ($custom_query->num_rows && $custom_query->row['keyword']) {
					$url .= $custom_query->row['keyword'] . '/';
				} else {
					$url .= $query->row['keyword'] . '/';
				}

			} else {
				$url = '';
				break;
			}
		}

		if ($url == '') {
			return $value . '/';
		} else {
			return $url;
		}

	}

	public function rewrite_value($keyword) {
		$value = $keyword;
		$custom_query = $this->db->query("SELECT keyword, query, search_id FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $this->db->escape($keyword) . "' AND is_custom = 1");
		if ($custom_query->num_rows == 0) {
			$query = $this->db->query("SELECT keyword, query FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $this->db->escape($keyword) . "'");
			if ($query->num_rows) {
				$value = $query->row['query'];
				$value = explode("=", $value);
				return $value[1] ?? 0;
			}
		} else {
			$value = $custom_query->row['search_id'];
		}
		return $value;
	}

	public function getQueryFromKeyword($keyword) {
		$result = array();
		$custom_query = $this->db->query("SELECT keyword, query, url_type, search_id FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $this->db->escape($keyword) . "' AND is_custom = 1");
		if ($custom_query->num_rows == 0) {
			$query = $this->db->query("SELECT keyword, query FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $this->db->escape($keyword) . "'");
			if ($query->num_rows) {
				$value = $query->row['query'];
				$value = explode("=", $value);
				$result['url_type'] = $value[0] ?? '';
				$result['id'] = $value[1] ?? 0;
			}
		} else {
			$result['url_type'] = $custom_query->row['url_type'];
			$result['id'] = $custom_query->row['search_id'];
		}

		return $result;

	}

	public function staticRedirect($route) {
		$return_data = array();

		if ($route == 'home-furnishing/tapestry') {
			$return_data['redirect_type'] = '301';
			$return_data['redirect_url'] = 'tapestry';
		}

		if ($route == 'accessories/bracelet') {
			$return_data['redirect_type'] = '301';
			$return_data['redirect_url'] = 'bangle-bracelet';
		}

		if ($route == 'watche') {
			$return_data['redirect_type'] = '404';
			$return_data['redirect_url'] = 'error/not_found';
		}

		if ($route == 'silver-anklets-toe-rings') {
			$return_data['redirect_type'] = '404';
			$return_data['redirect_url'] = 'error/not_found';
		}

		if ($route == 'Bajuband') {
			$return_data['redirect_type'] = '404';
			$return_data['redirect_url'] = 'error/not_found';
		}

		/*if ($route == 'bulk-lots')
			            {
			              $return_data['redirect_type'] = '404';
			              $return_data['redirect_url'] =  'error/not_found';
		*/

		return $return_data;
	}

}
