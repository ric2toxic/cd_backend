<?php

class ModelCatalogProduct extends Model {

	private $_product_change_log;
	public function __construct($registry) {
		$this->registry = $registry;
		$this->_product_change_log = new ProductChangeLog($registry);
	}

	public function addProduct($data) {

		$this->event->trigger('pre.admin.product.add', $data);

		// defaulting exclusive key - if not available
		$data['exclusive'] = (empty($data['exclusive']) ? 'normal' : $data['exclusive']);
		$data['cod_available'] = (!isset($data['cod_available']) ? '1' : $data['cod_available']);
		$data['non_returnable'] = (!isset($data['non_returnable']) ? '0' : $data['non_returnable']);
		$data['franchise_id'] = (!isset($data['franchise_id']) ? '0' : $data['franchise_id']);
		$data['is_associate'] = (!isset($data['is_associate']) ? '0' : $data['is_associate']);

		//for copy product case blank model
		if (isset($this->request->get['external_copy']) && $this->request->get['external_copy'] == 1) {
			$data['model'] = '';
		}

		$sql = "INSERT INTO " . DB_PREFIX . "product ";
		$sql .= "SET model = '" . $this->db->escape(trim($data['model'])) . "',";
		$sql .= "sku = '" . $this->db->escape(trim($data['sku'])) . "',";
		$sql .= "location = '" . $this->db->escape((isset($data['location']) ? $data['location'] : '')) . "',";
		$sql .= "quantity = '" . (int) $data['quantity'] . "',";
		$sql .= "date_modified = NOW(),";
		$sql .= "date_out_of_stock = NULL,";
		$sql .= "minimum = '" . (int) max(($data['minimum']), 1) . "',";
		$sql .= "subtract = '" . (int) $data['subtract'] . "',";
		$sql .= "stock_status_id = '" . (int) $data['stock_status_id'] . "',";
		$sql .= "expected_dispatch_date = '" . $this->db->escape($data['expected_dispatch_date']) . "',";

		if (!empty($data['date_available']) && $data['date_available'] !== '0000-00-00') {
			$sql .= "date_available = '" . $this->db->escape($data['date_available']) . "',";
		}

		$sql .= "manufacturer_id = '" . (int) $data['manufacturer_id'] . "',";
		$sql .= "shipping = '" . (int) $data['shipping'] . "',";
		$sql .= "price = '" . (float) $data['price'] . "',";
		$sql .= "mrp = '" . (float) $data['mrp'] . "',";
		$sql .= "price_per_set = '" . (float) ((float) $data['price'] * (int) $data['piece_in_set']) . "',";
		$sql .= "piece_in_set = '" . (int) $data['piece_in_set'] . "',";
		$sql .= "commission = '" . (float) $data['commission'] . "',";
		$sql .= "points = '" . (int) ($data['points'] ?? 0) . "',";
		$sql .= "weight = '" . (float) $data['weight'] . "',";
		$sql .= "weight_class_id = '" . (int) ($data['weight_class_id'] ?? 1) . "',";
		$sql .= "length = '" . (float) ($data['length'] ?? 0) . "',";
		$sql .= "width = '" . (float) ($data['width'] ?? 0) . "',";
		$sql .= "height = '" . (float) ($data['height'] ?? 0) . "',";
		$sql .= "length_class_id = '" . (int) ($data['length_class_id'] ?? 1) . "',";
		$sql .= "status = '" . (int) $data['status'] . "',";
		$sql .= "sort_order = '" . (int) $data['sort_order'] . "',";
		$sql .= "is_single = '" . (int) $data['is_single'] . "',";
		$sql .= "exclusive = '" . $this->db->escape($data['exclusive']) . "', ";
		$sql .= "is_associate = '" . (int) $data['is_associate'] . "', ";

		if (isset($data['rating'])) {
			$sql .= "rating = '" . (int) $data['rating'] . "', ";
		}

		if (isset($data['franchise_id'])) {

			$sql .= "franchise_id = '" . (int) $data['franchise_id'] . "', ";
		}
		if (isset($data['sor_product'])) {

			$sql .= "sor_product = '" . $this->db->escape($data['sor_product']) . "', ";
		}

		$sql .= "cod_available = '" . (int) $data['cod_available'] . "', ";
		$sql .= "non_returnable = '" . (int) $data['non_returnable'] . "', ";

		//getting tax_class_id
		$tax = new Tax($this->registry);
		if (isset($data['hsn_code'])) {
			$hsn_code = trim($data['hsn_code']);
			if (strlen($hsn_code) >= 4 && strlen($hsn_code) <= 8 && preg_match("/[0-9]{4,}/", $hsn_code)) {
				$sql .= "hsn_code = '" . $this->db->escape($data['hsn_code']) . "', ";
			}
			$tax_class_id = $tax->getTaxClassIdFromHSNCode($data['hsn_code']);
			$data['tax_class_id'] = !empty($tax_class_id) ? $tax_class_id : 0;
			$seller_tax = $tax->getTaxRateForTaxIncludedPrice($data['price'], $data['hsn_code'], $data['mrp']);
			$data['seller_tax'] = !empty($seller_tax) ? $seller_tax : 0;
			$seller_tax_factor = 1.0 + ((float) $data['seller_tax'] / 100.0);
			$commission_factor = 1.0 + ((float) $data['commission'] / 100.0);
			$data['selling_price'] = ceil($commission_factor * $data['price'] / $seller_tax_factor);
			$sql .= "tax_class_id = '" . (int) ($data['tax_class_id']) . "',";
			$sql .= "seller_tax = '" . (int) ($data['seller_tax']) . "',";
			$sql .= "selling_price = '" . (int) ($data['selling_price']) . "',";
		}

		if (!empty($data['store_sales'])) {
			$sql .= "store_sales = '" . $this->db->escape($data['store_sales']) . "',";
		}

		if (isset($data['unit_id'])) {
			$sql .= "unit_id = " . (int) ($data['unit_id']) . ", ";
		}

		$sql .= "date_added = NOW()";

		$this->db->query($sql);

		$product_id = $this->db->getLastId();

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description
			                  SET product_id       = '" . (int) $product_id . "',
			                      language_id      = '" . (int) $language_id . "',
			                      name             = '" . $this->db->escape(ucfirst($value['name'])) . "',
			                      set_description  = '" . $this->db->escape($value['set_description']) . "',
			                      description      = '" . $this->db->escape($value['description']) . "',
			                      tag              = '" . $this->db->escape($value['tag']) . "',
			                      meta_title       = '" . $this->db->escape($value['meta_title']) . "',
			                      meta_description = '" . $this->db->escape($value['meta_description']) . "',
			                      meta_keyword     = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store
				                  SET product_id = '" . (int) $product_id . "',
				                      store_id = '" . (int) $store_id . "'");
			}
		}

		// add sor terms product
		if (!empty($data['sor_product_terms']) && empty($data['non_returnable'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_sor_terms
                              SET product_id = " . (int) $product_id . ",
                                  sor_type = '" . $this->db->escape($data['sor_type']) . "',
                                  sor_days = '" . (int) $data['sor_days'] . "'");
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute
						                  SET product_id = '" . (int) $product_id . "',
						                      attribute_id = '" . (int) $product_attribute['attribute_id'] . "',
						                      language_id = '" . (int) $language_id . "',
						                      text = '" . $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (
			isset($data['product_option']) &&
			empty($data['associate_product_ids']) && // can't add options to combo product
			empty($data['is_associate']) // can't add options to associate product
		) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option
						                  SET product_id = '" . (int) $product_id . "',
						                      option_id = '" . (int) $product_option['option_id'] . "',
						                      required = '" . (int) $product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {

							$prdct_optn_val = '';
							if (!empty($product_option_value['option_image'])) {
								$prdct_optn_val = $product_option_value['option_image'];
							}

							// If we are copying franchise order product then we will receive product option value id here
							// we will set the quantity of this product according to the order product quantity, and for rest of the options
							// we will set quantity to zero.
							if (!empty($data['franchise_id']) && isset($data['product_option_value_id'])) {
								if ($data['product_option_value_id'] == $product_option_value['product_option_value_id']) {
									$quantity = $data['product_option_value_quantity'];
								} else {
									$quantity = 0;
								}
							} else {
								$quantity = $product_option_value['quantity'];
							}

							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value
							                  SET product_option_id = '" . (int) $product_option_id . "',
							                      product_id = '" . (int) $product_id . "',
							                      option_id = '" . (int) $product_option['option_id'] . "',
							                      option_value_id = '" . (int) $product_option_value['option_value_id'] . "',
							                      quantity = '" . (int) $quantity . "',
							                      subtract = '" . (int) $product_option_value['subtract'] . "',
							                      price = '" . (float) $product_option_value['price'] . "',
							                      price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "',
							                      points = '" . (int) $product_option_value['points'] . "',
							                      points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "',
							                      weight = '" . (float) $product_option_value['weight'] . "',
							                      weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "',
							                      option_image = '" . $this->db->escape($prdct_optn_val) . "'");

						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option
					                  SET product_id = '" . (int) $product_id . "',
					                      option_id = '" . (int) $product_option['option_id'] . "',
					                      value = '" . $this->db->escape($product_option['value']) . "',
					                      required = '" . (int) $product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount
				                  SET product_id = '" . (int) $product_id . "',
				                      quantity = '" . (int) $product_discount['quantity'] . "',
				                      priority = '" . (int) $product_discount['priority'] . "',
				                      price = '" . (float) $product_discount['price'] . "',
				                      store_id = '" . (int) $product_discount['store_id'] . "',
				                      date_start = '" . $this->db->escape($product_discount['date_start']) . "',
				                      date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special
				                  SET product_id = '" . (int) $product_id . "',
				                      priority = '" . (int) $product_special['priority'] . "',
				                      price = '" . (float) $product_special['price'] . "',
				                      date_start = '" . $this->db->escape($product_special['date_start']) . "',
				                      date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			$s = array_column($data['product_image'], 'sort_order');
			sort($s);
			foreach ($data['product_image'] as $key => $product_image) {
				if ($product_image['sort_order'] == $s[0]) {
					$this->db->query("UPDATE " . DB_PREFIX . "product
					                  SET image = '" . $this->db->escape($product_image['image']) . "'
					                  WHERE product_id = '" . (int) $product_id . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image
					                  SET product_id = '" . (int) $product_id . "',
					                      image = '" . $this->db->escape($product_image['image']) . "',
					                      sort_order = '" . (int) $product_image['sort_order'] . "'");
				}
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				if ((int) $download_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download
				                      SET product_id = '" . (int) $product_id . "',
				                          download_id = '" . (int) $download_id . "'");
				}

			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				if ((int) $category_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category
				                      SET product_id = '" . (int) $product_id . "',
				                          category_id = '" . (int) $category_id . "'");
				}

			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				if ((int) $filter_id) {
					$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_filter
				                      SET product_id = '" . (int) $product_id . "',
				                          filter_id = '" . (int) $filter_id . "'");
				}

			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				if ((int) $related_id) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_related
				                      WHERE (product_id = '" . (int) $product_id . "' AND related_id = '" . (int) $related_id . "')
				                        OR  (product_id = '" . (int) $related_id . "' AND related_id = '" . (int) $product_id . "')");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_related (product_id, related_id)
				                      VALUES ('" . (int) $product_id . "', '" . (int) $related_id . "'),
				                             ('" . (int) $related_id . "', '" . (int) $product_id . "')");
				}
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				if ((int) $layout_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout
				                      SET product_id = '" . (int) $product_id . "',
				                          store_id = '" . (int) $store_id . "',
				                          layout_id = '" . (int) $layout_id . "'");
				}

			}
		}

		$seo_keyword = $this->setSeoUrlNew($data['product_description'][1]['name'], $data['model'], $seo_keyword = '');

		//for copy product case
		if (isset($this->request->get['external_copy']) && $this->request->get['external_copy'] == 1) {
			$seo_keyword = '';
		}

		if ($seo_keyword != '') {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias
                          SET query = 'product_id=" . (int) $product_id . "',
                              keyword = '" . $this->db->escape(trim($seo_keyword)) . "'");
		}

		if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_recurring
                                          SET product_id = '" . (int) $product_id . "',
                                              recurring_id` = '" . (int) $recurring['recurring_id'] . "'");
			}
		}

		if (!empty($data['associate_product_ids'])) {
			$this->addAssociateProducts($product_id, $data['associate_product_ids']);
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.add', $product_id);

		if (!empty($data['seller_id'])) {
			$seller_id = (int) $data['seller_id'];
			$sql = "SELECT seller_status, vacation_mode,app_only,non_serviceable_areas
				    FROM " . DB_PREFIX . "ms_seller
					WHERE seller_id = '" . $seller_id . "'";
			$query = $this->db->query($sql);
			$seller_status = (int) $query->row['seller_status'];
			$vacation_mode = (int) $query->row['vacation_mode'];
			$app_only = (int) $query->row['app_only'];
			$non_serviceable_areas = $query->row['non_serviceable_areas'];
		} else {
			$seller_id = 0;
			$seller_status = 0;
			$vacation_mode = 0;
			$app_only = 0;
			$non_serviceable_areas = '';
		}

		$stock_status = $this->db->query("SELECT name FROM " . DB_PREFIX . "stock_status
		                                  WHERE stock_status_id = '" . (int) $data['stock_status_id'] . "'")->row['name'];

		//Add to SOLR
		$data['product_id'] = $product_id;
		$data['seller_id'] = $seller_id;
		$data['seller_status'] = $seller_status;
		$data['vacation_mode'] = $vacation_mode;
		$data['stock_status'] = $stock_status;
		$data['viewed'] = 0;
		$data['app_only'] = $app_only;
		$data['non_serviceable_areas'] = $non_serviceable_areas;

		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
			//$this->load->model('solr/product','frontend');
			$solr = new SolrProduct($this);
			$solr->addProductToSolr($data);
		}
		return $product_id;
	}

	public function editProduct($product_id, $data) {

		$this->event->trigger('pre.admin.product.edit', $data);
		// if any field change by any user then we have record in database
		$changes_data = json_decode($data['changes_data'], true);

		if (!empty($changes_data)) {
			$source_field = 'product_edit';
			$this->_product_change_log->recordLogs($product_id, $changes_data, $source_field);
		}
		$expected_dispatch_date = 'NULL';
		if (!empty($data['expected_dispatch_date'])) {
			$expected_dispatch_date = "'" . $this->db->escape($data['expected_dispatch_date']) . "'";
		}

		$sql = "UPDATE " . DB_PREFIX . "product ";
		$sql .= "SET model = '" . $this->db->escape(trim($data['model'])) . "', ";
		$sql .= "sku = '" . $this->db->escape(trim($data['sku'])) . "', ";
		$sql .= "quantity = '" . (int) $data['quantity'] . "', ";
		$sql .= "minimum = '" . (int) max(($data['minimum']), 1) . "', ";
		$sql .= "subtract = '" . (int) $data['subtract'] . "', ";
		$sql .= "stock_status_id = '" . (int) $data['stock_status_id'] . "', ";
		$sql .= "expected_dispatch_date = " . $expected_dispatch_date . ",";
		$sql .= "date_available = '" . $this->db->escape($data['date_available']) . "', ";
		$sql .= "manufacturer_id = '" . (int) $data['manufacturer_id'] . "', ";
		$sql .= "shipping = '" . (int) $data['shipping'] . "', ";
		$sql .= "price = '" . (float) $data['price'] . "', ";
		$sql .= "mrp = '" . (float) $data['mrp'] . "', ";
		$sql .= "price_per_set = '" . (float) ((float) $data['price'] * (int) $data['piece_in_set']) . "', ";
		$sql .= "piece_in_set = '" . (int) $data['piece_in_set'] . "', ";
		$sql .= "commission = '" . (float) $data['commission'] . "', ";
		$sql .= "points = '" . (int) ($data['points'] ?? 0) . "', ";
		$sql .= "weight = '" . (float) $data['weight'] . "', ";
		$sql .= "weight_class_id = '" . (int) ($data['weight_class_id'] ?? 1) . "',";
		$sql .= "length = '" . (float) ($data['length'] ?? 0) . "',";
		$sql .= "width = '" . (float) ($data['width'] ?? 0) . "',";
		$sql .= "height = '" . (float) ($data['height'] ?? 0) . "',";
		$sql .= "length_class_id = '" . (int) ($data['length_class_id'] ?? 1) . "',";
		$sql .= "status = '" . (int) $data['status'] . "', ";
		$sql .= "sort_order = '" . (int) $data['sort_order'] . "', ";
		$sql .= "is_single = '" . (int) $data['is_single'] . "', ";
		$sql .= "exclusive = '" . $this->db->escape($data['exclusive']) . "', ";
		$sql .= "sor_product = '" . $this->db->escape($data['sor_product']) . "', ";
		$sql .= "cod_available = '" . (int) $data['cod_available'] . "', ";
		$sql .= "non_returnable = '" . (int) $data['non_returnable'] . "', ";
		$sql .= "rating = '" . (int) $data['rating'] . "', ";

		if (isset($data['is_associate'])) {
			$sql .= "is_associate = '" . (int) $data['is_associate'] . "', ";
		}

		if (isset($data['hsn_code'])) {
			$hsn_code = trim($data['hsn_code']);
			if (strlen($hsn_code) >= 4 && strlen($hsn_code) <= 8 && preg_match("/[0-9]{4,}/", $hsn_code)) {
				$sql .= "hsn_code = '" . $this->db->escape($data['hsn_code']) . "', ";
			}
		}

		if (isset($data['unit_id'])) {
			$sql .= "unit_id = '" . (int) ($data['unit_id']) . "', ";
		}

		if (!empty($data['store_sales'])) {
			$sql .= "store_sales = '" . $this->db->escape($data['store_sales']) . "', ";
		}
		$sql .= "only_for_search = '" . $data['only_for_search'] . "', ";
		$sql .= "date_modified = NOW() ";
		$sql .= "WHERE product_id = '" . (int) $product_id . "'";

		$this->db->query($sql);

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("UPDATE " . DB_PREFIX . "product_description
				                 SET product_id = '" . (int) $product_id . "',
				                     language_id = '" . (int) $language_id . "',
				                     name = '" . $this->db->escape($value['name']) . "',
				                     set_description = '" . $this->db->escape($value['set_description']) . "',
				                     description = '" . $this->db->escape($value['description']) . "',
				                     tag = '" . $this->db->escape($value['tag']) . "',
				                     meta_title = '" . $this->db->escape($value['meta_title']) . "',
				                     meta_description = '" . $this->db->escape($value['meta_description']) . "',
				                     meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'
                              WHERE product_id = '" . (int) $product_id . "'
			                    AND language_id = '" . (int) $language_id . "'");
		}

		// Updating stores for the product
		// Sometimes key does not come, if there are no stores in old or new (removed all)
		$data['old_product_store'] = empty($data['old_product_store']) ? array() : $data['old_product_store'];
		$data['product_store'] = empty($data['product_store']) ? array() : $data['product_store'];

		// Finding stores to delete
		$stores_to_delete = array_diff($data['old_product_store'], $data['product_store']);
		foreach ($stores_to_delete as $store_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store
                              WHERE store_id = '" . (int) $store_id . "'
                                AND product_id = '" . (int) $product_id . "'");
		}

		// Finding stores to add
		$stores_to_add = array_diff($data['product_store'], $data['old_product_store']);
		foreach ($stores_to_add as $store_id) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store
                              SET store_id = '" . (int) $store_id . "',
                                  product_id = '" . (int) $product_id . "'");
		}

		// add sor terms product
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_sor_terms
                              WHERE product_id = " . (int) $product_id . "");

		if (!empty($data['sor_product_terms']) && empty($data['non_returnable'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_sor_terms
                              SET product_id = " . (int) $product_id . ",
                                  sor_type = '" . $this->db->escape($data['sor_type']) . "',
                                  sor_days = '" . (int) $data['sor_days'] . "'");

			$sor_type = $data['sor_type'];
			$sor_days = $data['sor_days'];

		} else {
			$sor_type = "";
			$sor_days = 0;
		}

		// Update change in solr
		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
			$solr_data = array();
			$solr_data['product_id'] = (int) $product_id;
			$solr_data['skip_filter_groups'] = true;

			$solr_data['fields']['sor_type'] = $sor_type;
			$solr_data['fields']['sor_days'] = (int) $sor_days;

			SolrProduct::atomicUpdateToSolr($solr_data);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int) $product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int) $product_id . "', attribute_id = '" . (int) $product_attribute['attribute_id'] . "', language_id = '" . (int) $language_id . "', text = '" . $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int) $product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int) $product_id . "'");

		if (
			isset($data['product_option']) &&
			empty($data['associate_product_ids']) && // can't add options to combo product
			empty($data['is_associate']) // can't add options to associate product
		) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int) $product_option['product_option_id'] . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $product_option['option_id'] . "', required = '" . (int) $product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int) $product_option_value['product_option_value_id'] . "', product_option_id = '" . (int) $product_option_id . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $product_option['option_id'] . "', option_value_id = '" . (int) $product_option_value['option_value_id'] . "', quantity = '" . (int) $product_option_value['quantity'] . "', subtract = '" . (int) $product_option_value['subtract'] . "', price = '" . (float) $product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int) $product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float) $product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "',  option_code = '" . $this->db->escape($product_option_value['option_code']) . "', option_image = '" . $this->db->escape($product_option_value['option_image']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int) $product_option['product_option_id'] . "', product_id = '" . (int) $product_id . "', option_id = '" . (int) $product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int) $product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND store_id = '0'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int) $product_id . "', quantity = '" . (int) $product_discount['quantity'] . "', priority = '" . (int) $product_discount['priority'] . "', price = '" . (float) $product_discount['price'] . "', store_id = '" . (int) $product_discount['store_id'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int) $product_id . "', priority = '" . (int) $product_special['priority'] . "', price = '" . (float) $product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int) $product_id . "'");

		if (isset($data['product_image'])) {
			$s = array_column($data['product_image'], 'sort_order');
			sort($s);
			foreach ($data['product_image'] as $key => $product_image) {
				$dimensions = serialize(array('width' => $product_image['width'], 'height' => $product_image['height']));
				if ($product_image['sort_order'] == $s[0]) {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($product_image['image']) . "', image_dimensions = '" . $this->db->escape($dimensions) . "' WHERE product_id = '" . (int) $product_id . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int) $product_id . "', image = '" . $this->db->escape($product_image['image']) . "', image_dimensions = '" . $this->db->escape($dimensions) . "', sort_order = '" . (int) $product_image['sort_order'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int) $product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				if ((int) $download_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download
				                      SET product_id = '" . (int) $product_id . "',
				                          download_id = '" . (int) $download_id . "'");
				}

			}
		}

		// Updating CATEGORIES for the product
		// Sometimes key does not come, if there are no CATEGORIES in old or new (removed all)
		$data['old_product_category'] = empty($data['old_product_category']) ? array() : $data['old_product_category'];
		$data['product_category'] = empty($data['product_category']) ? array() : $data['product_category'];

		// Finding CATEGORIES to delete
		$categories_to_delete = array_diff($data['old_product_category'], $data['product_category']);
		foreach ($categories_to_delete as $category_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category
                              WHERE category_id = '" . (int) $category_id . "'
                                AND product_id = '" . (int) $product_id . "'");
		}

		// Finding CATEGORIES to add
		$categories_to_add = array_diff($data['product_category'], $data['old_product_category']);
		foreach ($categories_to_add as $category_id) {
			$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_to_category
                              SET category_id = '" . (int) $category_id . "',
                                  product_id = '" . (int) $product_id . "'");
		}

		// Updating FILTERS for the product
		// Sometimes key does not come, if there are no FILTERS in old or new (removed all)
		$data['old_product_filter'] = empty($data['old_product_filter']) ? array() : $data['old_product_filter'];
		$data['product_filter'] = empty($data['product_filter']) ? array() : $data['product_filter'];

		// Finding FILTERS to delete
		$filters_to_delete = array_diff($data['old_product_filter'], $data['product_filter']);
		foreach ($filters_to_delete as $filter_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter
                             WHERE filter_id = '" . (int) $filter_id . "'
                               AND product_id = '" . (int) $product_id . "'");
		}

		// Finding FILTERS to add
		$filters_to_add = array_diff($data['product_filter'], $data['old_product_filter']);
		foreach ($filters_to_add as $filter_id) {

			$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_filter
                              SET filter_id = '" . (int) $filter_id . "',
                                  product_id = '" . (int) $product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related
		                  WHERE product_id = '" . (int) $product_id . "'
		                    OR  related_id = '" . (int) $product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				if ((int) $related_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_related (product_id, related_id)
				                      VALUES ('" . (int) $product_id . "', '" . (int) $related_id . "'),
				                             ('" . (int) $related_id . "', '" . (int) $product_id . "')");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int) $product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				if ((int) $layout_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout
				                      SET product_id = '" . (int) $product_id . "',
				                          store_id = '" . (int) $store_id . "',
				                          layout_id = '" . (int) $layout_id . "'");
				}

			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int) $product_id . "'");

		$seo_keyword = $this->setSeoUrlNew($data['product_description'][1]['name'], $data['model'], $data['keyword']);

		if ($seo_keyword) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias
			                  SET query = 'product_id=" . (int) $product_id . "',
			                      keyword = '" . $this->db->escape(trim($seo_keyword)) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int) $product_id);

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int) $product_id . ", `recurring_id` = " . (int) $product_recurring['recurring_id']);
			}
		}

		// change log associate products of a combo
		$old_associate_product_ids = $this->getAssociateProductIds($product_id);
		$old_associate_product_ids_str = implode(',', $old_associate_product_ids);
		if (!isset($data['associate_product_ids'])) {
			$data['associate_product_ids'] = array();
		}

		sort($data['associate_product_ids']);
		$new_associate_product_ids_str = implode(',', $data['associate_product_ids']);
		if ($old_associate_product_ids_str !== $new_associate_product_ids_str) {
			$changes_data = array(
				"associate_product_ids" => array(
					"old_value" => $old_associate_product_ids_str,
					"new_value" => $new_associate_product_ids_str,
				),
			);
			$source_field = 'product_edit';
			$this->_product_change_log->recordLogs($product_id, $changes_data, $source_field);
		}

		$this->deleteAllAssociateProducts($product_id);
		if (!empty($data['associate_product_ids'])) {
			$this->addAssociateProducts($product_id, $data['associate_product_ids']);
			Product::updateComboProductQuantity($this->db, $product_id);
			// update cod available and non-returnable fields of associate products
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET cod_available = '" . (int) $data['cod_available'] . "', non_returnable = '" . (int) $data['non_returnable'] . "' WHERE product_id IN(" . implode(',', $data['associate_product_ids']) . ")");
		}
		if (!empty($data['is_associate'])) {
			Product::updateComboProductQuantityUsingAssociate($this->db, $product_id);
			$this->syncComboProductWithAssociate($product_id, $data);
		}

		$this->cache->delete('product');
		$this->event->trigger('post.admin.product.edit', $product_id);
	}

	public function copyProduct($product_id, $franchise_product_data = array(), $quantity = 0, $product_option_value_id = 0, $return_product = array()) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '1'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';

			$data['product_attribute'] = $this->getProductAttributes($product_id);
			$data['product_description'] = $this->getProductDescriptions($product_id);
			$data['product_discount'] = $this->getProductDiscounts($product_id, $store_id = 0);
			$data['product_filter'] = $this->getProductFilters($product_id);
			$data['product_image'] = $this->getProductImages($product_id);

			if (!empty($return_product)) {
				$data['product_option'] = $this->getProductOptions($product_id, $product_option_value_id);
			} else {
				$data['product_option'] = $this->getProductOptions($product_id);
			}

			$data['product_related'] = $this->getProductRelated($product_id);
			$data['product_special'] = $this->getProductSpecials($product_id);
			$data['product_category'] = $this->getProductCategories($product_id);
			$data['product_download'] = $this->getProductDownloads($product_id);
			$data['product_layout'] = $this->getProductLayouts($product_id);
			$data['product_store'] = $this->getProductStores($product_id);
			$data['product_recurrings'] = $this->getRecurrings($product_id);
			$data['associate_product_ids'] = $this->getAssociateProductIds($product_id);

			if (!empty($franchise_product_data)) {
				$data['franchise_id'] = $franchise_product_data['franchise_id'];

				$franchise_data = $this->getFranchiseData($franchise_product_data['franchise_id']);

				$data['sku'] = 'FR_' . $data['sku'] . '_' . $franchise_data['name'];
				$data['model'] = $franchise_data['franchise_prefix'] . '_' . $data['model'];
				$data['minimum'] = '1';
				$data['piece_in_set'] = $franchise_product_data['piece_in_set'];
				$data['sor_product'] = 0;
				$data['status'] = '1';
				$data['store_sales'] = 'NO';
				$data['date_available'] = NULL;
				$data['exclusive'] = 'both';
				$data['is_associate'] = 0;

				if ($quantity != 0) {
					$data['quantity'] = $quantity;
				}

				if ($product_option_value_id != 0) {
					$data['product_option_value_id'] = $product_option_value_id;
					$data['product_option_value_quantity'] = $quantity;
				}
			}

			//Assign Default value to seller_id from config
			$seller_id = DUMMY_SELLER_ID;

			// If we are copy product from return action - WSB Books and Relisted
			if (!empty($return_product)) {

				$data['sku'] = $return_product['sku'];
				$data['model'] = $return_product['model'];
				$data['piece_in_set'] = 1;
				$data['sort_order'] = 1;

				if ($quantity != 0) {
					$data['quantity'] = $quantity;
				}

				if (!empty($data['product_option'])) {
					$data['product_option'][0]['product_option_value'][0]['quantity'] = $quantity;
				}
				$seller_id = $return_product['seller_id'];
			}

			$new_product_id = $this->addProduct($data);

			if (!empty($franchise_product_data) || !empty($return_product)) {
				$product_id_arr = array();
				$product_id_arr[] = $new_product_id;
				$this->ProductAssignToSeller($seller_id, $product_id_arr);
			}

			return $new_product_id;
		}
	}

	public function deleteProduct($product_id) {

		$this->event->trigger('pre.admin.product.delete', $product_id);

		// Checking first if Product has already been ordered before or not.
		$product_query = $this->db->query("SELECT order_product_id
                                           FROM " . DB_PREFIX . "order_product
                                           WHERE product_id = '" . (int) $product_id . "'
                                           ORDER BY order_product_id ASC LIMIT 1");

		if ($product_query->num_rows == 0) {

			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int) $product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id = " . (int) $product_id);
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int) $product_id . "'");

			//If this is franchise product then it will have to entry in mapping tables as well so need to delete the product mapping
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_franchise WHERE new_product_id = " . (int) $product_id);

			$this->cache->delete('product');

			//Delete SOLR
			if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
				//$this->load->model('solr/product','frontend');
				$solr = new SolrProduct($this);
				$solr->deleteProductFromSolr($product_id);
			}

		} else {
			// Product quantity is set to 0 and being disabled.
			$this->db->query("UPDATE " . DB_PREFIX . "product
                              SET quantity = 0,
                                  status = 0
                              WHERE product_id = '" . (int) $product_id . "'");

		}

		$this->event->trigger('post.admin.product.delete', $product_id);
	}

	public function getProduct($product_id, $fields = array()) {

		//@todo make it general to add all product tables (way getOrder is changed)
		if (!$fields) {
			$sql = "SELECT DISTINCT *,
			       (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int) $product_id . "') AS keyword
			       FROM " . DB_PREFIX . "product p
			         LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id )
			       WHERE p.product_id = '" . (int) $product_id . "'
			         AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";
		} else {
			$fields = implode(", ", $fields);
			$sql = "SELECT " . $fields . " FROM " . DB_PREFIX . "product
			        WHERE product_id = '" . (int) $product_id . "'";
		}

		$query = $this->db->query($sql);

		return ($query->num_rows ? $query->row : false);
	}

	public function getSorProduct($product_id) {

		$sql = "SELECT sor_type, sor_days
			       FROM " . DB_PREFIX . "product_sor_terms
			       WHERE product_id = '" . (int) $product_id . "'";

		$query = $this->db->query($sql);
		return ($query->num_rows ? $query->row : false);

	}

	public function getProductsUsingProductIds($product_ids) {
		$result = array();
		if (isset($product_ids) && !empty($product_ids)) {
			$sql = "SELECT p.image,
	                       p.product_id,
	                       p.model,
	                       p.sku,
	                       p.price,
	                       p.selling_price,
                           p.seller_tax,
	                       p.commission,
	                       p.quantity,
	                       p.status,
	                       p.is_single,
	                       p.piece_in_set,
	                       p.tax_class_id,
                           p.hsn_code,
                           pd.name,
	                       pd.set_description,
	                       p.weight,
                           p.mrp,
	                       p.weight_class_id,
	                       p.shipping,
                           p.store_sales,
                           p.minimum,
                           p.sort_order,
                           p.sor_product,
                           p.is_archived,
                           p.date_available,
                           p.stock_status_id,
                           p.cod_available,
                           p.non_returnable,
                           mp.seller_id,
                           ms.vacation_mode,
                           p.rating as product_rating,
                           p.is_associate,
                           p.hidden_selling_price,
                           p.only_for_search
					FROM " . DB_PREFIX . "product p
					LEFT JOIN " . DB_PREFIX . "product_description pd
					  ON (p.product_id = pd.product_id)
					LEFT JOIN " . DB_PREFIX . "ms_product mp
					  ON (mp.product_id = p.product_id)
					LEFT JOIN " . DB_PREFIX . "ms_seller ms
					  ON (mp.seller_id = ms.seller_id)
					WHERE pd.language_id = 1 AND  p.product_id  IN (" . $product_ids . ")";

			//echo $sql;

			$query = $this->db->query($sql);

			//echo "<pre>"; print_r($query);

			if ($query->num_rows) {

				foreach ($query->rows as $row) {

					// weight unit
					$sql = "SELECT unit FROM " . DB_PREFIX . "weight_class_description
	                        WHERE weight_class_id = '" . (int) $row['weight_class_id'] . "'
	                          AND language_id = '1'";
					$query = $this->db->query($sql);
					if ($query->num_rows) {
						$row['weight_unit'] = $query->row['unit'];
					} else {
						$row['weight_unit'] = '';
					}

					//  tax title
					$sql = "SELECT title FROM " . DB_PREFIX . "tax_class
	                        WHERE tax_class_id = '" . (int) $row['tax_class_id'] . "'";
					$query = $this->db->query($sql);
					if ($query->num_rows) {
						$row['tax_title'] = $query->row['title'];
					} else {
						$row['tax_title'] = '';
					}

					// product status
					$sql = "SELECT name FROM " . DB_PREFIX . "product_status
	                        WHERE product_status_id = '" . (int) $row['status'] . "'
	                          AND language_id = '1'";
					$query = $this->db->query($sql);
					if ($query->num_rows) {
						$row['product_status'] = $query->row['name'];
					} else {
						$row['product_status'] = '';
					}

					// Product rating //commented by vikas(03-04-2018) bcz now rating field added in oc_product so don't need avg rating.
					// $row['product_rating'] = floor( $this->getProductRating($row['product_id']) );
					$sql = "SELECT ood.name
						FROM oc_product_option opp
						INNER JOIN oc_option_description ood
						  ON ood.option_id = opp.option_id
						WHERE opp.product_id = '" . (int) $row['product_id'] . "'
						GROUP BY opp.product_id";
					$query = $this->db->query($sql);
					if ($query->num_rows) {
						$row['product_option_name'] = $query->row['name'];
					} else {
						$row['product_option_name'] = '';
					}

					//====product_store_id====//
					$query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int) $row['product_id'] . "'");
					if ($query->num_rows) {
						$row['store_id'] = $query->row['store_id'];
					} else {
						$row['store_id'] = '';
					}

					$row['product_parent_category_id'] = $this->getCategory($row['product_id']);

					$result[] = $row;
				}
			}
		}

		return $result;
	}

	public function getProducts($data = array()) {

		$sql = "SELECT
                       p.product_id,
                       p.image,
                       p.model,
                       p.sku,
                       p.price,
                       p.selling_price,
					   p.seller_tax,
                       p.commission,
                       p.quantity,
                       p.status,
                       p.is_single,
                       p.piece_in_set,
					   p.exclusive,
					   pd.name as name,
                       pd.set_description as set_description,
                       p.weight,
                       p.store_sales,
                       p.hsn_code,
                       p.minimum,
                       p.sor_product,
                       mp.seller_id as seller_id,
                       p.sort_order,
                       p.mrp,
                       p.date_available,
                       p.franchise_id,
                       p.is_archived,
                       p.stock_status_id,
                       p.cod_available,
                       p.non_returnable,
					   p.is_associate,
                       p.rating as product_rating,
                       p.hidden_selling_price,
                       p.only_for_search,
                       COALESCE(
                            ( SELECT od.name
                              FROM oc_product_option po
                              JOIN oc_option_description od
                                ON od.option_id = po.option_id AND od.language_id = 1
                              WHERE po.product_id = p.product_id LIMIT 1
                            ), '') as product_option_name
				FROM " . DB_PREFIX . "product p
				STRAIGHT_JOIN " . DB_PREFIX . "product_description pd
				  ON p.product_id = pd.product_id
				     AND pd.language_id = 1
				LEFT JOIN " . DB_PREFIX . "ms_product mp
				  ON mp.product_id = p.product_id
				  ";

		if (!empty($data['filter_category'])) {

			$sql .= " INNER JOIN " . DB_PREFIX . "product_to_category pc
		                      ON p.product_id = pc.product_id
			                     AND pc.category_id = " . (int) $data['filter_category'];
		}

		if (isset($data['request_page']) && $data['request_page'] == 'franchise_product') {

			if (!empty($data['franchise_id'])) {

				$sql .= " WHERE p.franchise_id = " . (int) $data['franchise_id'];

			} else {

				$sql .= " WHERE p.franchise_id > 0 ";
			}

		} else {

			$sql .= " WHERE p.franchise_id = 0 ";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$filter_operator = $data['filter_operator'] ?? '';
			$filter_type_string = $data['filter_type_string'] ?? '';
			$filter_val_from = $data['filter_val_from'] ?? '';
			$filter_val_to = $data['filter_val_to'] ?? '';

			$sql .= " AND ( p.model LIKE '%" . $this->db->escape(trim($data['filter_model'])) . "%'";

			if (!empty($filter_type_string)) {
				$sql .= queryString('p.model', $this->db, $filter_type_string, $filter_operator);
			}

			if (!empty($filter_val_from) && !empty($filter_val_to)) {
				$sql .= queryInteger('p.model', $this->db, $filter_val_from, $filter_val_to, $filter_operator);
			}
			$sql .= " ) ";
		}

		if (!empty($data['filter_price_from'])) {
			$sql .= " AND p.price >= '" . (float) $data['filter_price_from'] . "'";
		}

		if (!empty($data['filter_price_to'])) {
			$sql .= " AND p.price <= '" . (float) $data['filter_price_to'] . "'";
		}

		if (!empty($data['filter_commission_from'])) {
			$sql .= " AND p.commission >= '" . (float) $data['filter_commission_from'] . "'";
		}

		if (!empty($data['filter_commission_to'])) {
			$sql .= " AND p.commission <= '" . (float) $data['filter_commission_to'] . "'";
		}

		if (!empty($data['filter_seller_sku'])) {

			$sllr_sku_filter_operator = $data['sllr_sku_filter_operator'] ?? '';
			$sllr_sku_filter_type_string = $data['sllr_sku_filter_type_string'] ?? '';
			$sllr_sku_filter_val_from = $data['sllr_sku_filter_val_from'] ?? '';
			$sllr_sku_filter_val_to = $data['sllr_sku_filter_val_to'] ?? '';

			$sql .= " AND ( p.sku LIKE '%" . $this->db->escape(trim($data['filter_seller_sku'])) . "%'";

			if (!empty($sllr_sku_filter_type_string)) {
				$sql .= queryString('p.sku', $this->db, $sllr_sku_filter_type_string, $sllr_sku_filter_operator);
			}

			if (!empty($sllr_sku_filter_val_from) && !empty($sllr_sku_filter_val_to)) {
				$sql .= queryInteger('p.sku', $this->db, $sllr_sku_filter_val_from, $sllr_sku_filter_val_to, $sllr_sku_filter_operator);
			}
			$sql .= " ) ";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
		}

		if (!empty($data['filter_sort_order_from'])) {
			$sql .= " AND p.sort_order >= '" . (int) $data['filter_sort_order_from'] . "'";
		}

		if (!empty($data['filter_sort_order_to'])) {
			$sql .= " AND p.sort_order <= '" . (int) $data['filter_sort_order_to'] . "'";
		}

		if (!empty($data['filter_non_single'])) {
			$sql .= " AND p.is_single = '0'";
		}
		if (!empty($data['filter_non_sor'])) {
			$sql .= " AND p.model NOT LIKE '%-SOR'";
		}

		if (!empty($data['filter_seller_list'])) {
			if ((int) $data['filter_seller_list'] > 0) {
				$sql .= " AND mp.seller_id = '" . (int) $data['filter_seller_list'] . "'";
			} elseif ($data['filter_seller_list'] == 'null') {
				$sql .= " AND mp.seller_id is " . $data['filter_seller_list'] . " ";
			}
		}

		if (!empty($data['is_associate'])) {
			$sql .= " AND p.is_associate = '" . (int) $data['is_associate'] . "'";
		}

		if (!empty($data['filter_store_sales_code'])) {
			$sql .= " AND p.store_sales = '" . $this->db->escape(trim($data['filter_store_sales_code'])) . "'";
		}

		if (!empty($data['filter_hsn_code'])) {
			$sql .= " AND p.hsn_code = '" . $this->db->escape(trim($data['filter_hsn_code'])) . "'";
		}

		if (!empty($data['filter_images']) && $data['filter_images'] == 1) {
			$sql .= " AND ( p.image is NULL OR p.image = '' ) ";
		}

		$sort = $data['sort'] ?? '';
		$order = $data['order'] ?? '';

		// Pagination related filter condition
		$p = $data['page'] ?? '';
		if ($p !== 'FIRST' && (int) $p > 0) {

			switch ($sort) {

			case 'p.sku':

				if (strtolower($order) == 'asc') {
					$sql .= " AND p.sku > '" . $this->db->escape($data['filter_sorting_data']) . "'
	        					  OR ( p.sku = '" . $this->db->escape($data['filter_sorting_data']) . "'
	        					  		AND
	        					  	   p.product_id > " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				} else if (strtolower($order) == 'desc') {
					$sql .= " AND p.sku < '" . $this->db->escape($data['filter_sorting_data']) . "'
	        					  OR ( p.sku = '" . $this->db->escape($data['filter_sorting_data']) . "'
	        					  		AND
	        					  	   p.product_id < " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				}

				break;

			case 'p.price':

				if (strtolower($order) == 'asc') {
					$sql .= " AND p.price > " . (float) $data['filter_sorting_data'] . "
	        					  OR ( p.price = " . (float) $data['filter_sorting_data'] . "
	        					  		AND
	        					  	   p.product_id > " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				} else if (strtolower($order) == 'desc') {
					$sql .= " AND p.price < " . (float) $data['filter_sorting_data'] . "
	        					  OR ( p.price = " . (float) $data['filter_sorting_data'] . "
	        					  		AND
	        					  	   p.product_id < " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				}

				break;

			case 'p.commission':

				if (strtolower($order) == 'asc') {
					$sql .= " AND p.commission > " . (float) $data['filter_sorting_data'] . "
	        					  OR ( p.commission = " . (float) $data['filter_sorting_data'] . "
	        					  		AND
	        					  	   p.product_id > " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				} else if (strtolower($order) == 'desc') {
					$sql .= " AND p.commission < " . (float) $data['filter_sorting_data'] . "
	        					  OR ( p.commission = " . (float) $data['filter_sorting_data'] . "
	        					  		AND
	        					  	   p.product_id < " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				}

				break;

			case 'p.quantity':

				if (strtolower($order) == 'asc' && $data['filter_product_id'] > 0) {
					$sql .= " AND p.quantity > " . (int) $data['filter_sorting_data'] . "
	        					  OR ( p.quantity = " . (int) $data['filter_sorting_data'] . "
	        					  		AND
	        					  	   p.product_id > " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				} else if (strtolower($order) == 'desc' && $data['filter_product_id'] > 0) {
					$sql .= " AND p.quantity < " . (int) $data['filter_sorting_data'] . "
	        					  OR ( p.quantity = " . (int) $data['filter_sorting_data'] . "
	        					  		AND
	        					  	   p.product_id < " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				}

				break;

			case 'p.status':

				if (strtolower($order) == 'asc') {
					$sql .= " AND p.status > " . (int) $data['filter_sorting_data'] . "
	        					  OR ( p.status = " . (int) $data['filter_sorting_data'] . "
	        					  		AND
	        					  	   p.product_id > " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				} else if (strtolower($order) == 'desc') {
					$sql .= " AND p.status < " . (int) $data['filter_sorting_data'] . "
	        					  OR ( p.status = " . (int) $data['filter_sorting_data'] . "
	        					  		AND
	        					  	   p.product_id < " . (int) $data['filter_product_id'] . "
	        					  	 )
	        					";
				}

				break;

			default:

				$sql .= " AND p.product_id < " . (int) $p;

				break;
			}

		}

		$sort_data = array(
			'p.price',
			'p.sku',
			'p.commission',
			'p.quantity',
			'p.status',
		);

		if (!empty($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
			$sql .= " " . ($data['order'] ?? "ASC");
			$sql .= ", p.product_id " . ($data['order'] ?? "ASC");
		} else {
			$sql .= " ORDER BY p.product_id DESC ";
		}

		if (!empty($data['limit'])) {
			if ((int) $data['limit'] < 1) {
				$data['limit'] = 30;
			}

			$sql .= " LIMIT " . (int) $data['limit'];
		}

		$query = $this->db->query($sql);

		$result = array();

		if ($query->num_rows) {

			$result = $query->rows;
		}

		$all_data = array();
		$all_data['products'] = $result;

		return $all_data;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int) $category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int) $product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name' => $result['name'],
				'set_description' => $result['set_description'],
				'description' => $result['description'],
				'meta_title' => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword' => $result['meta_keyword'],
				'tag' => $result['tag'],
			);
		}

		return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int) $product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int) $product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int) $product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int) $product_id . "' AND attribute_id = '" . (int) $product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id' => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data,
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id, $product_option_value_id = 0) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po INNER JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) INNER JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int) $product_id . "' AND od.language_id = '" . (int) $this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$sql = "
					SELECT
						*
					FROM
						" . DB_PREFIX . "product_option_value
					WHERE
						product_option_id = '" . (int) $product_option['product_option_id'] . "'";

			if (!empty($product_option_value_id)) {
				$sql .= " AND product_option_value_id = " . (int) $product_option_value_id;

			}
			$product_option_value_query = $this->db->query($sql);

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id' => $product_option_value['option_value_id'],
					'quantity' => $product_option_value['quantity'],
					'subtract' => $product_option_value['subtract'],
					'price' => $product_option_value['price'],
					'option_image' => $product_option_value['option_image'],
					'price_prefix' => $product_option_value['price_prefix'],
					'points' => $product_option_value['points'],
					'points_prefix' => $product_option_value['points_prefix'],
					'weight' => $product_option_value['weight'],
					'weight_prefix' => $product_option_value['weight_prefix'],
					'option_code' => $product_option_value['option_code'],
				);
			}

			$product_option_data[] = array(
				'product_option_id' => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id' => $product_option['option_id'],
				'name' => $product_option['name'],
				'type' => $product_option['type'],
				'value' => $product_option['value'],
				'required' => $product_option['required'],
			);
		}

		return $product_option_data;
	}

	public function getProductImages($product_id) {
		$other_images_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int) $product_id . "' ORDER BY sort_order ASC");
		$main_image_query = $this->db->query("SELECT p.product_id, p.image, p.image_dimensions, '1' AS `sort_order` FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int) $product_id . "'");
		$result_query = array_merge($main_image_query->rows, $other_images_query->rows);
		return $result_query;
	}

	public function getProductDiscounts($product_id, $store_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND store_id = '" . (int) $store_id . "' ORDER BY quantity, priority, price");
		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int) $product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int) $product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int) $product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int) $product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int) $product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total
				FROM " . DB_PREFIX . "product p
				INNER JOIN " . DB_PREFIX . "product_description pd
				  ON (p.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "ms_product mp
				  ON (mp.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_to_category pc
		          ON (p.product_id = pc.product_id)
				";

		$sql .= " WHERE pd.language_id = 1";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$filter_operator = $data['filter_operator'];
			$filter_type_string = $data['filter_type_string'];
			$filter_val_from = $data['filter_val_from'];
			$filter_val_to = $data['filter_val_to'];

			$sql .= " AND ( p.model LIKE '%" . $this->db->escape(trim($data['filter_model'])) . "%'";

			if (!empty($filter_type_string)) {
				$sql .= queryString('p.model', $this->db, $filter_type_string, $filter_operator);
			}

			if (!empty($filter_val_from) && !empty($filter_val_to)) {
				$sql .= queryInteger('p.model', $this->db, $filter_val_from, $filter_val_to, $filter_operator);
			}
			$sql .= " ) ";
		}

		if (isset($data['filter_price_from']) && !is_null($data['filter_price_from'])) {
			$sql .= " AND p.price >= '" . (float) $data['filter_price_from'] . "'";
		}

		if (isset($data['filter_price_to']) && !is_null($data['filter_price_to'])) {
			$sql .= " AND p.price <= '" . (float) $data['filter_price_to'] . "'";
		}

		if (isset($data['filter_commission_from']) && !is_null($data['filter_commission_from'])) {
			$sql .= " AND p.commission >= '" . (float) $data['filter_commission_from'] . "'";
		}

		if (isset($data['filter_commission_to']) && !is_null($data['filter_commission_to'])) {
			$sql .= " AND p.commission <= '" . (float) $data['filter_commission_to'] . "'";
		}

		if (isset($data['filter_seller_sku']) && !is_null($data['filter_seller_sku'])) {
			$sllr_sku_filter_operator = $data['sllr_sku_filter_operator'];
			$sllr_sku_filter_type_string = $data['sllr_sku_filter_type_string'];
			$sllr_sku_filter_val_from = $data['sllr_sku_filter_val_from'];
			$sllr_sku_filter_val_to = $data['sllr_sku_filter_val_to'];

			$sql .= " AND ( p.sku LIKE '%" . $this->db->escape(trim($data['filter_seller_sku'])) . "%'";

			if (!empty($sllr_sku_filter_type_string)) {
				$sql .= queryString('p.sku', $this->db, $sllr_sku_filter_type_string, $sllr_sku_filter_operator);
			}

			if (!empty($sllr_sku_filter_val_from) && !empty($sllr_sku_filter_val_to)) {
				$sql .= queryInteger('p.sku', $this->db, $sllr_sku_filter_val_from, $sllr_sku_filter_val_to, $sllr_sku_filter_operator);
			}
			$sql .= " ) ";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
		}

		if (isset($data['filter_non_single']) && $data['filter_non_single'] == 1) {
			$sql .= " AND p.is_single = '0'";
		}

		if (isset($data['filter_non_sor']) && $data['filter_non_sor'] == 1) {
			$sql .= " AND p.model NOT LIKE '%-SOR'";
		}

		if (isset($data['filter_seller_list']) && !is_null($data['filter_seller_list'])) {
			$sql .= " AND mp.seller_id = '" . (int) $data['filter_seller_list'] . "'";
		}

		if (!empty($data['filter_category'])) {
			$sql .= " AND pc.category_id = '" . (int) $data['filter_category'] . "'";
		}

		if (!empty($data['is_associate'])) {
			$sql .= " AND p.is_associate = '" . (int) $data['is_associate'] . "'";
		}

		if (isset($data['filter_sort_order_from']) && !is_null($data['filter_sort_order_from'])) {
			$sql .= " AND p.sort_order >= '" . (int) $data['filter_sort_order_from'] . "'";
		}

		if (isset($data['filter_sort_order_to']) && !is_null($data['filter_sort_order_to'])) {
			$sql .= " AND p.sort_order <= '" . (int) $data['filter_sort_order_to'] . "'";
		}

		if (isset($data['filter_store_sales_code']) && !is_null($data['filter_store_sales_code'])) {
			$sql .= " AND p.store_sales = '" . $this->db->escape(trim($data['filter_store_sales_code'])) . "'";
		}

		if (isset($data['filter_hsn_code']) && !is_null($data['filter_hsn_code'])) {
			$sql .= " AND p.hsn_code LIKE '%" . $this->db->escape(trim($data['filter_hsn_code'])) . "%'";
		}

		if (isset($data['filter_images']) && $data['filter_images'] == 1) {
			$sql .= " AND ( p.image is NULL OR p.image = '' ) ";
		}

		if (isset($data['request_page']) && $data['request_page'] == 'franchise_product') {
			if (!empty($data['franchise_id'])) {

				$sql .= " AND p.franchise_id = '" . $this->db->escape(trim($data['franchise_id'])) . "'";

			} else {

				$sql .= " AND ( p.franchise_id IS NOT NULL AND p.franchise_id != 0 )";
			}

		} else {
			// Add check
			$sql .= " AND ( p.franchise_id IS NULL OR p.franchise_id = 0 )";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int) $tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int) $stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int) $weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int) $length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int) $download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int) $manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int) $attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int) $option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int) $recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int) $layout_id . "'");

		return $query->row['total'];
	}
	public function setSeoUrl($form_name, $form_model) {

		$name_after_apostrophe = str_replace("'", "", trim($form_name));
		$name_after_ampersand = str_replace("amp", "", trim($name_after_apostrophe));
		$name_after_regex = preg_replace('/[^a-zA-Z0-9- \n]/', "", trim($name_after_ampersand));
		$name = strtolower(implode("-", preg_split('/[\s]+/', trim($name_after_regex))));

		$model_after_apostrophe = str_replace("'", "", trim($form_model));
		$model_after_ampersand = str_replace("amp", "", trim($model_after_apostrophe));
		$model_after_regex = preg_replace('/[^a-zA-Z0-9- \n]/', "", trim($model_after_ampersand));
		$model = strtolower(implode("-", preg_split('/[\s]+/', trim($model_after_regex))));

		$seo_line = $name . "-" . $model;
		return $seo_line;
	}

	/**
	 * SEO URL is always empty
	 * generate the new URL,
	 * check for new URL in table,
	 * if exists in table, generate new URL
	 * else return the generated URL
	 * */
	public function setSeoUrlNew($form_name, $form_model, $seo_url) {

		if (empty($seo_url)) {

			//if empty SEO URL, frame basic structure of SEO URL and then
			$name_after_apostrophe = str_replace("'", "", trim($form_name));
			$name_after_ampersand = str_replace("amp", "", trim($name_after_apostrophe));
			$name_after_regex = preg_replace('/[^a-zA-Z0-9- \n]/', "", trim($name_after_ampersand));
			$name = strtolower(implode("-", preg_split('/[\s]+/', trim($name_after_regex))));

			$model_after_apostrophe = str_replace("'", "", trim($form_model));
			$model_after_ampersand = str_replace("amp", "", trim($model_after_apostrophe));
			$model_after_regex = preg_replace('/[^a-zA-Z0-9- \n]/', "", trim($model_after_ampersand));
			$model = strtolower(implode("-", preg_split('/[\s]+/', trim($model_after_regex))));

			$seo_line = $name . "-" . $model;
		} else {
			$seo_line = $seo_url;
		}
		//recursively checking for unique seo url
		return $this->checkExistingSeoUrl($seo_line);
	}

	/**
	 * recursive method to check for SEO url duplicacy
	 * */
	public function checkExistingSeoUrl($seo_line) {
		$sql = "SELECT *
					FROM oc_url_alias
					WHERE
					keyword LIKE '" . $seo_line . "'";

		$sql_query = $this->db->query($sql);

		//if same keyword exists, generate another one
		if ($sql_query->num_rows > 0) {
			$uniqid = $seo_line . uniqid();
			return $this->checkExistingSeoUrl($uniqid);
		} else {
			return $seo_line;
		}
	}

	public function getProductFiltersData($product_id) {

		$q = "SELECT f.filter_id, GROUP_CONCAT(fd.name SEPARATOR ', ') as filter_name,
				(SELECT name FROM oc_filter_group_description fgd WHERE fl.filter_group_id = fgd.filter_group_id AND fgd.language_id = 1) as group_name
			  FROM " . DB_PREFIX . "product_filter f
			  INNER JOIN " . DB_PREFIX . "filter_description fd
			  ON f.filter_id = fd.filter_id
			  INNER JOIN " . DB_PREFIX . "filter fl
			  ON f.filter_id = fl.filter_id
			  WHERE f.product_id = " . $product_id . "
			  AND fd.language_id =1
			  GROUP BY group_name";

		$query = $this->db->query($q);

		return $query->rows;
	}
	public function dynamicmetatags() {

		$this->load->model('catalog/filter');

		if ($this->request->post['product_description'][1]['meta_description'] == '') {

			$filter_ids = $this->request->post['product_filter'];
			$filters = array();
			foreach ($filter_ids as $id) {
				$data = $this->model_catalog_filter->getFilter($id);
				$filters[] = $data['name'];
			}
			$filters = implode(',', $filters);
			$this->request->post['product_description'][1]['meta_description'] = sprintf($this->language->get('meta_description'), $this->request->post['product_description'][1]['name'], $this->request->post['product_description'][1]['name'], $this->request->post['product_description'][1]['name'], $filters, $this->request->post['product_description'][1]['name']);

		}

		if ($this->request->post['product_description'][1]['meta_title'] == '') {

			$this->request->post['product_description'][1]['meta_title'] = sprintf($this->language->get('meta_title'), $this->request->post['product_description'][1]['name'], $this->request->post['product_description'][1]['name']);
		}

		if ($this->request->post['product_description'][1]['meta_keyword'] == '') {

			$this->request->post['product_description'][1]['meta_keyword'] = sprintf($this->language->get('meta_title'), $this->request->post['product_description'][1]['name'], $this->request->post['product_description'][1]['name']);
		}
	}
	/**
	 * Adds any product from wholesale store to singles store
	 * @author Parth Gupta
	 * @dateTime 2016-01-18T15:55:22+0530
	 * @param int  $product_id product id of the product to copy as single
	 */
	public function copyProductToSingle($product_id) {

		if (isset($this->session->data['copy_price_markup'])) {
			$price_markup = $this->session->data['copy_price_markup'];
		} else {
			$price_markup = 0;
		}
		if (isset($this->session->data['copy_commission'])) {
			$commission = $this->session->data['copy_commission'];
		}

		$this->load->model('catalog/option');
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$exsisting_query = $this->db->query("SELECT DISTINCT model FROM " . DB_PREFIX . "product p WHERE model LIKE " . "'" . $data['model'] . '-SNGL' . "'");
			if ($exsisting_query->num_rows) {
				$existing_single = true;
			} else {
				$existing_single = false;
			}
			if (!$data['is_single'] && !$existing_single) {
				$data['is_single'] = 1;
				$data['sku'] = $data['sku'];
				$data['model'] = $data['model'] . '-SNGL';
				$data['piece_in_set'] = 1;
				$data['commission'] = isset($commission) ? $commission : $data['commission'];
				$data['price'] = ceil($data['price'] * (1 + $price_markup / 100));
				$data['viewed'] = '0';
				$data['keyword'] = $this->setSeoUrl($data['name'], $data['model']);
				$data['status'] = '1';
				$data['sort_order'] = 999;

				$data['product_attribute'] = $this->getProductAttributes($product_id);
				$data['product_description'] = $this->getProductDescriptions($product_id);
				$data['product_description'][1]['set_description'] = '';

				//USING REGEX TO MATCH CONSECUTIVE DECIMALS

				if (!empty($data['product_option'])) {
					$data['product_option'] = $this->getProductOptions($product_id);
				} else {

					$options_found = 0;

					$description_var = $this->getProductDescriptions($product_id);

					if ($description_var[1]['set_description']) {
						preg_match_all('!\d\d!', $description_var[1]['set_description'], $matches);
					}

					$arr_option = array();

					if (isset($matches) && !empty($matches)) {

						$sizes_values = $this->model_catalog_option->getOptionValues('11');
						$size_chart = array_column($sizes_values, 'option_value_id', 'name');

						foreach ($matches[0] as $key => $value) {
							if (array_key_exists($value, $size_chart)) {
								$options_found += 1;

								$arr_option['product_option_value'][] = array(
									'option_value_id' => $size_chart[$value],
									'quantity' => $data['quantity'],
									'subtract' => '1',
									'price' => '0',
									'price_prefix' => '',
									'points' => '',
									'points_prefix' => '',
									'weight' => '0',
									'weight_prefix' => '',
								);
							}

						}
						$arr_option['type'] = 'select';
						$arr_option['option_id'] = '11';
						$arr_option['required'] = '1';
						$arr_option['name'] = 'Size';

					}

					// Re-adjusting the total qty for singles
					$data['quantity'] = $options_found ? $options_found * (int) ($data['quantity']) : (int) ($data['quantity']);

					$data['product_option'] = array($arr_option);
					$temp_filters = $this->getProductFilters($product_id);

					if (($key = array_search(62, $temp_filters)) !== false) {
						unset($temp_filters[$key]);
					} elseif (($key = array_search(63, $temp_filters)) !== false) {
						unset($temp_filters[$key]);
					}

					$data['product_filter'] = $temp_filters;
					$data['product_image'] = $this->getProductImages($product_id);
					$data['product_related'] = $this->getProductRelated($product_id);
					$data['product_special'] = $this->getProductSpecials($product_id);
					$data['product_category'] = $this->getProductCategories($product_id);
					$data['product_download'] = $this->getProductDownloads($product_id);
					$data['product_layout'] = $this->getProductLayouts($product_id);
					$data['product_store'] = $this->getProductStores($product_id);
					$data['product_recurrings'] = $this->getRecurrings($product_id);

					if ($new_product_id = $this->addProduct($data)) {
						$new_seller_id = $this->MsLoader->MsProduct->getSellerId($product_id);
						$sql_seller = "INSERT INTO " . DB_PREFIX . "ms_product
										SET
										   product_id = '" . (int) $new_product_id . "',
										   seller_id  = '" . (int) $new_seller_id . "'
									  ";
						$this->db->query($sql_seller);

					};

				}
			}
		}
	}

	/**
	 * get product availability in alternate store
	 * @param $is_single
	 * @param $product_name
	 * @return mixed
	 */
	public function getAlternateProductInfo($is_single, $product_name) {
		if (!$is_single) {
			$sql = "SELECT p.selling_price, p.product_id FROM " . DB_PREFIX . "product p WHERE model LIKE '" . $product_name . "-SNGL'";
			$query = $this->db->query($sql);

		} else {
			$sql = "SELECT p.selling_price, p.product_id FROM " . DB_PREFIX . "product p WHERE model LIKE '" . chop($product_name, '-SNGL') . "'";
			$query = $this->db->query($sql);
		}
		//echo "<pre>"; print_r($data); echo "<br>"; print_r($product_name);echo "</pre>";
		//echo "<pre>"; print_r($sql); echo "<br>"; print_r($query);echo "</pre>";
		return $query->row;

	}

	public function get_store_data() {
		$sql = "SELECT * from " . DB_PREFIX . "store";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function store_data() {
		if ($this->request->post['store_id']) {
			$store_id = $this->request->post['store_id'];
		} else {
			$store_id = '';
		}
		if ($this->request->get['product_id']) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = '';
		}
		if ($this->request->post['Language']) {
			$language = (int) $this->request->post['Language'];
		} else {
			$language = '';
		}
		if ($this->request->post['Meta_Tag_Title']) {
			$meta_title = $this->request->post['Meta_Tag_Title'];
		} else {
			$meta_title = '';
		}
		if ($this->request->post['Meta_Tag_Keywords']) {
			$meta_keyword = $this->request->post['Meta_Tag_Keywords'];
		} else {
			$meta_keywords = '';
		}
		if ($this->request->post['Meta_Tag_Description']) {
			$meta_description = $this->request->post['Meta_Tag_Description'];
		} else {
			$meta_description = '';
		}

		$p_id = "SELECT product_id,store_id from " . DB_PREFIX . "product_storeinfo WHERE product_id= '" . $product_id . "' AND store_id = '" . $store_id . "'";
		$p_id = $this->db->query($p_id);
		$pro_id = $p_id->row['product_id'];
		$s_id = trim($p_id->row['store_id']);
		$store_id = trim($store_id);
		if (($pro_id == $product_id) && ($s_id == $store_id)) {
			$sql = "UPDATE " . DB_PREFIX . "product_storeinfo SET store_id = '" . $store_id . "', product_id = '" . $product_id . "', language = '" . $language . "', meta_title = '" . $meta_title . "', meta_keywords = '" . $meta_keyword . "', meta_description = '" . $meta_description . "', modify = NOW() WHERE product_id= '" . $product_id . "' AND store_id = '" . $store_id . "'";

		} else {
			$sql = "INSERT INTO " . DB_PREFIX . "product_storeinfo SET store_id = '" . $store_id . "', product_id = '" . $product_id . "', language = '" . $language . "', meta_title = '" . $meta_title . "', meta_keywords = '" . $meta_keyword . "', meta_description = '" . $meta_description . "', created = NOW(), modify = NOW()";
		}
		$query = $this->db->query($sql);

		return $query->rows;

	}
	public function get_product_store_data() {

		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = '';
		}

		$sql = "SELECT * from " . DB_PREFIX . "product_storeinfo WHERE product_id= '" . $product_id . "'";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function get_ajax_product_store_data($store_id, $product_id) {
		$sql = "SELECT * from " . DB_PREFIX . "product_storeinfo WHERE product_id= '" . $product_id . "' AND store_id= '" . $store_id . "'";

		$query = $this->db->query($sql);
		return $query->rows;

	}

	/**
	 * @param $product_id
	 */
	public function copyProductToSOR($product_id) {

		if (isset($this->session->data['copy_price_markup'])) {
			$price_markup = $this->session->data['copy_price_markup'];
		} else {
			$price_markup = 0;
		}

		if (isset($this->session->data['copy_commission'])) {
			$commission = $this->session->data['copy_commission'];
		}

		$this->load->model('catalog/option');
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$exsisting_query = $this->db->query("SELECT DISTINCT model FROM " . DB_PREFIX . "product p WHERE model LIKE " . "'" . $data['model'] . '-SOR' . "'");
			if ($exsisting_query->num_rows) {
				$existing_sor = true;
			} else {
				$existing_sor = false;
			}

			if (!$existing_sor && !$data['is_single']) {
				$data['sku'] = $data['sku'];
				$data['model'] = $data['model'] . '-SOR';
				$data['commission'] = isset($commission) ? $commission : $data['commission'];
				$data['price'] = ceil($data['price'] * (1 + $price_markup / 100));
				$data['viewed'] = '0';
				$data['keyword'] = $this->setSeoUrl($data['name'], $data['model']);
				$data['status'] = '1';
				$data['is_single'] = '0';
				$data['sort_order'] = 999;

				$data['product_attribute'] = $this->getProductAttributes($product_id);
				$data['product_description'] = $this->getProductDescriptions($product_id);
				$data['product_option'] = $this->getProductOptions($product_id);
				$data['product_filter'] = $this->getProductFilters($product_id);
				$data['product_image'] = $this->getProductImages($product_id);
				$data['product_related'] = $this->getProductRelated($product_id);
				$data['product_special'] = $this->getProductSpecials($product_id);
				$data['product_category'] = $this->getProductCategories($product_id);
				$data['product_download'] = $this->getProductDownloads($product_id);
				$data['product_layout'] = $this->getProductLayouts($product_id);
				$data['product_store'] = array(SOR_STORE_ID);
				$data['product_recurrings'] = $this->getRecurrings($product_id);

				if ($new_product_id = $this->addProduct($data)) {
					$new_seller_id = $this->MsLoader->MsProduct->getSellerId($product_id);
					$sql_seller = "INSERT INTO " . DB_PREFIX . "ms_product
                                   SET product_id='" . (int) $new_product_id . "', seller_id='" . $new_seller_id . "' ";
					$this->db->query($sql_seller);

				}
			}
		}
	}

	/**
	 * Product to approve list
	 **/
	public function product_to_approve_list($filter_data) {

		$sql = "SELECT pta.*,p.*,pd.*,ms.nickname FROM " . DB_PREFIX . "product_to_approve pta
									LEFT JOIN " . DB_PREFIX . "product p ON (pta.product_id = p.product_id)
									LEFT JOIN " . DB_PREFIX . "product_description pd ON (pta.product_id = pd.product_id)
									LEFT JOIN " . DB_PREFIX . "ms_product mp ON (pta.product_id = mp.product_id)
									LEFT JOIN " . DB_PREFIX . "ms_seller ms ON (mp.seller_id = ms.seller_id) WHERE ";
		if (isset($filter_data['seller']) && !empty($filter_data['seller'])) {
			$seller_id = $filter_data['seller'];
			$sql .= " ms.seller_id = '" . $seller_id . "' AND ";
		}
		$sql .= "pta.approved = '0'";

		if (isset($filter_data['limit'])) {
			$limit = $filter_data['limit'];
			$start = $filter_data['start'];
			$sql .= " LIMIT  " . (int) $start . "," . (int) $limit;
		}
		///echo $sql; exit;
		$query = $this->db->query($sql);
		//echo "<pre>"; print_r($query->rows); exit;
		return $query->rows;
		//if ($query->num_rows) {}
	}
	/**
	 * Product to approve list total
	 **/
	public function product_to_approve_list_total($filter_data) {

		$query = $this->db->query("SELECT pta.*,p.*,pd.*,ms.nickname FROM " . DB_PREFIX . "product_to_approve pta
									LEFT JOIN " . DB_PREFIX . "product p ON (pta.product_id = p.product_id)
									LEFT JOIN " . DB_PREFIX . "product_description pd ON (pta.product_id = pd.product_id)
									LEFT JOIN " . DB_PREFIX . "ms_product mp ON (pta.product_id = mp.product_id)
									LEFT JOIN " . DB_PREFIX . "ms_seller ms ON (mp.seller_id = ms.seller_id)
									WHERE pta.approved = '0'");
		//echo "<pre>"; print_r($query->rows); exit;
		return $query->num_rows;
		//if ($query->num_rows) {}
	}

	// get seller List with assigned product by vikas (27-06-2016)
	public function getSellerList() {
		$sql = "SELECT ms.seller_id, ms.nickname,ms.company
				FROM " . DB_PREFIX . "ms_seller ms
                INNER JOIN oc_customer c
                  ON c.customer_id = ms.seller_id
                WHERE ms.seller_status = 1
                ORDER BY ms.nickname ASC  ";
		$query = $this->db->query($sql);
		//echo "<prE>"; print_r($query);
		return $query->rows;
	}

	// get list of product status and set in filter status by vikas (27-06-2016)
	public function getProductStatus() {
		$sql = "SELECT ps.product_status_id, ps.name FROM " . DB_PREFIX . "product_status ps";
		$query = $this->db->query($sql);
		//echo "<prE>"; print_r($query);
		return $query->rows;
	}

	// updateProductList with price , commission, and quantity by vikas (29-06-2016)
	public function updateProductList($product_id, $field_type, $field_value, $changes_data = array()) {

		// save change log data in admin product change log table
		if (!empty($changes_data)) {
			$source_field = 'product_list';
			$this->_product_change_log->recordLogs($product_id, $changes_data, $source_field);
		}

		if ($field_type == 'quantity') {
			$field_values = (int) $field_value;
		} else {
			$field_values = (float) $field_value;
		}
		$this->db->query("UPDATE oc_product
						  SET " . $field_type . "= '" . $this->db->escape($field_values) . "'
						  WHERE product_id = '" . (int) $product_id . "'");

		$this->db->query("UPDATE oc_product
				SET selling_price = ceil((price * (1 + (commission/100)))/(1 + (seller_tax/100)))
				WHERE product_id = '" . (int) $product_id . "'");

		$query = $this->db->query("SELECT price, commission, seller_tax FROM oc_product
							WHERE product_id = '" . (int) $product_id . "'");
		$query_data = $query->row;
		$selling_price = ceil(($query_data['price'] * (1 + ($query_data['commission'] / 100))) / (1 + ($query_data['seller_tax'] / 100)));
	}

	/**
	 * Method for set product rating by ajax and rating value store in oc_product table
	 * @param  :  array of product_rating , product_ids , changes_data with have value
	 * @return : NULL
	 * @author : vikas , Apr 2018
	 */
	public function setProductRatingByAjax($data) {

		$product_ids = $data['product_ids'];
		$rating = $data['product_rating'];
		$changes_data = $data['changes_data'];

		$query = $this->db->query("UPDATE oc_product  SET rating='" . $rating . "' WHERE product_id IN  (" . implode(',', $product_ids) . ")");

		if ($query) {
			if (!empty($changes_data)) {
				foreach ($changes_data as $key => $values) {
					foreach ($values as $key_pid => $value_old_pro_rating) {
						// key_pid means key of product id AND $value_old_pro_rating means old value of seller id
						$changes_data = array(
							'product_rating' => array(
								'old_value' => $value_old_pro_rating,
								'new_value' => $rating,
							),
						);
						$source_field = 'product_list';
						$this->_product_change_log->recordLogs($key_pid, $changes_data, $source_field);
					}
				}
			}
			return true;
		} else {
			return false;
		}
	}

	// get order of products by product_id
	public function productsOrderList($product_id) {

		$sql = "SELECT  'WSB Purchase' as transaction_type,
                                  owpb.sku as seller_sku,
                                  owp.invoice_date,
                                  SUM(owpb.pieces) as qty_in,
                                  NULL as qty_out,
                                  NULL as stock_movement,
                                  NULL as stock_movement_detail,
                                  owp.invoice_no as reference,
                                  IF(owp.sor_purchase = 1, 'SOR', 'Non-SOR') as reference_status,
								  '' as option_name,
								  '' as option_value

                        FROM " . DB_PREFIX . "wsb_purchase as owp
                        INNER JOIN " . DB_PREFIX . "wsb_purchase_breakup as owpb ON (owpb.purchase_id = owp.purchase_id)
                        WHERE
                            owpb.product_id = " . (int) $product_id . "
                        GROUP BY owp.purchase_id
                        ";

		$sql .= " UNION ";

		$sql .= "SELECT  'WSB Purchase Return' as transaction_type,
                                  owprb.sku as seller_sku,
                                  owpr.date_added as invoice_date,
                                  NULL as qty_in,
                                  SUM(owprb.quantity) as qty_out,
                                  NULL as stock_movement,
                                  NULL as stock_movement_detail,
                                  CONCAT(owpr.debit_note_prefix, owpr.debit_note_no) as reference,
                                  CONCAT('Inv: ', owp.invoice_no) as reference_status,
								  '' as option_name,
								  '' as option_value

                        FROM " . DB_PREFIX . "wsb_purchase_return as owpr
                        INNER JOIN " . DB_PREFIX . "wsb_purchase_return_breakup as owprb ON (owprb.debit_note_id = owpr.debit_note_id)
                        INNER JOIN " . DB_PREFIX . "wsb_purchase as owp ON (owp.purchase_id = owpr.purchase_id)

                        WHERE
                              owprb.product_id = " . (int) $product_id . "
                          AND owpr.debit_note_status = 1

                        GROUP BY owpr.debit_note_id
                        ";

		$sql .= " UNION ";

		$sql .= "SELECT
                                IF(o.stock_transfer = 0, 'Sales', 'Stock Transfer') as transaction_type,
                                oop.seller_sku,
                                IF(osub.order_status_id != 2
                                    AND osub.invoice_no > 0
                                    AND osub.buyer_invoice_id > 0,
                                   osub.invoice_date,
                                   osub.date_added
                                  ) as invoice_date,

                                NULL as qty_in,
                                IF(o.stock_transfer = 0, SUM(oop.piece_in_set*oop.quantity), NULL) as qty_out,

                                IF(o.stock_transfer = 1, SUM(oop.piece_in_set*oop.quantity), NULL) as stock_movement,
                                IF(o.stock_transfer = 1, CONCAT('TO ', o.payment_city), NULL)  as stock_movement_detail,

                                osub.suborder_id as reference,
                                oos.name as reference_status,
								oopt.name as option_name,
								GROUP_CONCAT(oopt.value,'[', oop.quantity, 'x', oop.piece_in_set , ']') as option_value
                        FROM " . DB_PREFIX . "order o
                        INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id
                        INNER JOIN " . DB_PREFIX . "order_status oos ON oos.order_status_id = osub.order_status_id
                        INNER JOIN " . DB_PREFIX . "order_product oop ON oop.order_id = o.order_id
                        LEFT JOIN " . DB_PREFIX . "order_option oopt ON oopt.order_product_id = oop.order_product_id
                        WHERE  oop.suborder_id = osub.suborder_id
                          AND  oop.product_id = " . (int) $product_id . "
                          AND  osub.order_status_id > 0 AND osub.order_status_id != 2
                          AND  oos.language_id = 1
                          AND  o.store_id IN (" . WSB_STORES_ID . ")
                          AND  o.franchise_id = 0
                          AND (CASE
                               WHEN (osub.order_status_id != 2 AND osub.invoice_no > 0 AND osub.buyer_invoice_id > 0)
                                 THEN oop.buyer_invoice_id = osub.buyer_invoice_id

                               WHEN osub.order_status_id != 2
                                  THEN oop.edit_type IN ('YES',
                                                         'SELLER_PARTIAL',
                                                         'SELLER_APPROVED',
                                                         'SELLER_LATER_DISPATCH',
                                                         'DAMAGE_BY_COURIER_COMPANY'
                                                        )
                              ELSE 1=1
                          END )
                        GROUP BY oop.suborder_id ";

		$sql .= " UNION ";

		$sql .= "SELECT  IF(o.stock_transfer = 0, 'Sales Return', 'Stock Transfer Return') as transaction_type,
                                  oop.seller_sku,
                                  ocn.date_added as invoice_date,

                                  IF(o.stock_transfer = 0, SUM(ort.quantity), NULL) as qty_in,
                                  NULL as qty_out,

                                  IF(o.stock_transfer = 1, SUM(ort.quantity), NULL) as stock_movement,
                                  IF(o.stock_transfer = 1, CONCAT('FROM ', o.payment_city), NULL) as stock_movement_detail,

                                  CONCAT(ocn.credit_note_prefix, ocn.credit_note_no) as reference,
                                  osub.suborder_id as reference_status,
								  oopt.name as option_name,
								  GROUP_CONCAT(oopt.value,'[', oop.quantity, 'x', oop.piece_in_set , ']') as option_value

                        FROM " . DB_PREFIX . "credit_note ocn
                        INNER JOIN " . DB_PREFIX . "return ort ON ort.credit_note_id = ocn.credit_note_id
                        INNER JOIN " . DB_PREFIX . "order_product oop ON oop.order_product_id = ort.order_product_id
                        INNER JOIN " . DB_PREFIX . "suborder osub ON osub.buyer_invoice_id = oop.buyer_invoice_id
                        INNER JOIN " . DB_PREFIX . "order o ON osub.order_id = o.order_id
                        LEFT JOIN " . DB_PREFIX . "order_option oopt ON oopt.order_product_id = oop.order_product_id

                        WHERE
                              oop.product_id = " . (int) $product_id . "
                          AND ocn.credit_note_status = 1
                          AND osub.order_status_id > 0 AND osub.order_status_id != 2
                          AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0
                          AND o.store_id IN (" . WSB_STORES_ID . ")
                          AND o.franchise_id = 0

                        GROUP BY ocn.credit_note_id
                        ";

		$sql .= " ORDER BY invoice_date ";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	// get Total order of products by product_id
	public function getTotalProductsOrderList($product_id) {
		$sql = "SELECT  COUNT(DISTINCT o.order_id) AS total
					FROM oc_order_product op
 					INNER JOIN oc_suborder osub
 					  ON(osub.suborder_id = op.suborder_id)
                    INNER JOIN oc_order o
                      ON(o.order_id = osub.order_id)
 					WHERE op.product_id = " . (int) $product_id . "
 					  AND (osub.order_status_id > 0 AND osub.order_status_id != 2)
 					  AND o.store_id IN ('" . WSB_STORES_ID . "')
 					  AND o.franchise_id = 0
 				";

		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	// update oc_ms_product and products assigned to seller
	public function ProductAssignToSeller($seller_id, $product_ids, $changes_data = '', $check_product_exclusive = 0) {

		if (!is_array($product_ids)) {
			$product_ids = explode(',', $product_ids);
		}

		$query = $this->db->query("SELECT product_id,seller_id FROM oc_ms_product WHERE product_id IN(" . implode(",", $product_ids) . ")");

		$product_id_array = array_column($query->rows, 'product_id');

		foreach ($product_id_array as $prod_id) {

			$this->db->query("UPDATE oc_ms_product
						SET	seller_id ='" . (int) $seller_id . "'
						WHERE product_id ='" . (int) $prod_id . "'
					");
		}

		$remaining_product_id = array_diff($product_ids, $product_id_array);

		if (count($remaining_product_id) > 0) {
			foreach ($remaining_product_id as $product_id) {
				$this->db->query("INSERT INTO oc_ms_product
						SET product_id ='" . (int) $product_id . "',
							seller_id ='" . (int) $seller_id . "'
					");
			}
		}

		if (!empty($changes_data)) {

			foreach ($changes_data as $key => $values) {
				foreach ($values as $key_pid => $value_old_sid) {
					$source_field = 'product_list';
					// key_pid means key of product id AND $value_old_sid means old value of seller id
					$changes_data = array(
						'seller_change' => array(
							'old_value' => $value_old_sid,
							'new_value' => $seller_id,
						),
					);
					$this->_product_change_log->recordLogs($key_pid, $changes_data, $source_field);
				}
			}
		} else {

			foreach ($query->rows as $pid_seller_id) {
				$source_field = 'product_list';
				$changes_data = array(
					'seller_change' => array(
						'old_value' => $pid_seller_id['seller_id'],
						'new_value' => $seller_id,
					),
				);
				$this->_product_change_log->recordLogs($pid_seller_id['product_id'], $changes_data, $source_field);
			}
		}

		$this->setProductRatingWhenProductAssignToSeller($seller_id, $product_ids);

		//code added by Nilesh as per new requirement of seller exclusive
		if (!empty($check_product_exclusive)) {
			$this->_updateProductExclusiveStatus($seller_id, $product_ids);
		}

		//update seller product sor terms
		$query = $this->db->query("SELECT sor_type,sor_days FROM " . DB_PREFIX . "seller_sor_terms WHERE seller_id = " . (int) $seller_id);
		if ($query->num_rows) {
			$this->bulkUpdateSor($query->row['sor_days'], $query->row['sor_type'], $product_ids, 'insert');
		} else {
			$this->bulkUpdateSor('', '', $product_ids, 'remove');
		}

	}

	public function _updateProductExclusiveStatus($seller_id, $product_ids) {
		$selector = array(
			'select' => array('exclusive'),
		);
		$seller_exclusive_status = SellerInfo::getSellerInfo($this->db, $seller_id, $selector);
		$seller_exclusive_status = $seller_exclusive_status[$seller_id]['exclusive'];

		$sql = "SELECT product_id,
                       exclusive
                FROM " . DB_PREFIX . "product
                WHERE product_id IN (" . implode(",", $product_ids) . ")";
		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach ($query->rows as $product_info) {
				if ($product_info['exclusive'] != $seller_exclusive_status) {
					$source_field = 'product_list';
					$changes_data = array(
						'exclusive' => array(
							'old_value' => $product_info['exclusive'],
							'new_value' => $seller_exclusive_status,
						),
					);
					$this->_product_change_log->recordLogs($product_info['product_id'], $changes_data, $source_field);
				}
			}
		}

		$sql = "UPDATE " . DB_PREFIX . "product op
                SET op.exclusive = '" . $this->db->escape($seller_exclusive_status) . "'
                WHERE op.product_id IN (" . implode(',', $product_ids) . ") AND
                      op.exclusive !='" . $this->db->escape($seller_exclusive_status) . "'";
		$this->db->query($sql);
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int) $product_id . "'");

		return $query->rows;
	}

	public function getCategory($product_id) {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int) $product_id . "' ORDER BY category_id DESC");
		if (isset($query->row['category_id'])) {
			return $query->row['category_id'];
		} else {
			return 0;
		}
	}

	/**
	 * Method to get rating for a product.
	 * It first checks if an individual rating has been defined for this product.
	 * If not, then it tries to find rating using the global rules defined.
	 * Input(s):
	 * @param int product_id
	 * Output: Float rating value or boolean false (if no rating found by any of the logics)
	 * Author: Madhur
	 */
	public function getProductRating($product_id) {

		// First check if we have an individual rating defined for this product
		$sql = "SELECT AVG(rating) AS avg_rating FROM " . DB_PREFIX . "review
                WHERE product_id = '" . (int) $product_id . "'
				  AND status = '1'
                  AND store_id IN (" . WSB_STORES_ID . ")
                GROUP BY product_id";
		$query = $this->db->query($sql);

		if ($query->num_rows) {
			return (float) ($query->row['avg_rating']);
		}

		// We are here, it implies that individual rating not found.
		$seller_id = $this->MsLoader->MsProduct->getSellerId($product_id);
		if (empty($seller_id)) {
			return false;
		}

		// Get all categories for this product
		$base_category_ids = array_column($this->getCategories($product_id), 'category_id');

		// Get all child categories including the parent categories
		$this->load->model('catalog/category');
		$all_category_ids = $this->model_catalog_category->getChildCategoryIds($base_category_ids, true);

		if (!empty($all_category_ids)) {
			// Check if there is a rule defined for this seller and category_id
			$sql = "SELECT MIN(rating) as rating FROM " . DB_PREFIX . "review_rules
                    WHERE seller_id = '" . (int) $seller_id . "'
                      AND rule_type = 'category'
                      AND category_id IN (" . implode(', ', $all_category_ids) . ")";
			$query = $this->db->query($sql);
			if ($query->num_rows and !empty($query->row['rating'])) {
				return (float) ($query->row['rating']);
			}
		}

		// Check for global rating now - because category wise ratings not found
		$sql = "SELECT rating FROM " . DB_PREFIX . "review_rules
                WHERE seller_id = '" . (int) $seller_id . "'
                  AND rule_type = 'global'";
		$query = $this->db->query($sql);

		if ($query->num_rows and !empty($query->row['rating'])) {
			return (float) ($query->row['rating']);
		} else {
			return false;
		}

	}

	/**
	 * Method to get current tax rates for a product
	 * Input  : @param int product_id
	 * Output : Semi-colon separated string containing various tax rates applicable on that product
	 */
	public function getTaxRates($product_id) {

		// First get the tax class id
		$query = $this->db->query("SELECT tax_class_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int) $product_id . "'");

		if ($query->num_rows and !empty($query->row['tax_class_id'])) {

			$sql = "SELECT trl.tax_class_id, trt.tax_rate_id, trt.rate, trt.type
                    FROM " . DB_PREFIX . "tax_rule trl INNER JOIN " . DB_PREFIX . "tax_rate trt ON trl.tax_rate_id = trt.tax_rate_id
                    WHERE trl.tax_class_id = '" . (int) $query->row['tax_class_id'] . "'
                    ORDER BY trl.priority ASC";
			$query = $this->db->query($sql);

			if ($query->num_rows) {
				$tax_rates = $query->row['rate'] . $query->row['type'];

				foreach (array_slice($query->rows, 1) as $row) {
					$tax_rates .= "; " . $query->row['rate'] . $query->row['type'];
				}

				return $tax_rates;

			} else {
				return 0;
			}

		} else {
			return 0;
		}
	}

	/**
	 * Method to get product images according to product ids
	 * Input  : @param int product_ids
	 */
	public function getProductImagesByProductsIds($product_ids) {

		$sql = "SELECT product_id,image FROM " . DB_PREFIX . "product ";

		$sql .= "WHERE product_id IN ('" . implode("','", $product_ids) . "')";

		$result = $this->db->query($sql);

		if ($result->num_rows > 0) {

			return $result->rows;

		}

	}

	/**
	 * get HSN Ids from oc_product table
	 * */
	public function getHSNIds() {

		//getting all non empty hsn ids from oc_product

		$sql = "SELECT GROUP_CONCAT(DISTINCT hsn_code) as hsn_code from " . DB_PREFIX . "product WHERE hsn_code !=''";
		$hsn_codes = $this->db->query($sql)->row;
		return $hsn_codes;

	}

	public function setUnitByCategory($product_id, $categories, $product_unit_id) {
		$data = array();
		$is_popup = 0;
		$category_unit_id = 0;
		$category_unit_text = '';
		$product_unit_id = 0;

		if (!empty($categories)) {
			$condition = rtrim(implode(',', $categories));
			$sql = "SELECT
	         				category_id,
	         				MAX(tag_priority),
	         				C.unit_id AS unit_id,
	         				U.super_unit,
	         				U.base_unit
	         		FROM
	         			" . DB_PREFIX . "category AS C
	         		LEFT JOIN
	         			" . DB_PREFIX . "units AS U ON U.unit_id = C.unit_id
	         		WHERE
	         			C.category_id in (" . $condition . ")
	                ";

			$query = $this->db->query($sql);
			if ($query->num_rows > 0) {
				$category_unit_id = $query->row['unit_id'];
				$category_unit_text = strtoupper($query->row['super_unit']) . '-->' . strtoupper($query->row['base_unit']);
			}

			if ($product_unit_id != 0 && $product_unit_id != '' && $product_unit_id != NULL) {
				$sql = "SELECT * FROM " . DB_PREFIX . "units WHERE unit_id = " . $product_unit_id;
				$query = $this->db->query($sql);
				$pop_product_unit_id = $query->row['unit_id'];
				$product_unit_text = strtoupper($query->row['super_unit']) . '-->' . strtoupper($query->row['base_unit']);
			} else {
				$pop_product_unit_id = '0';
				$product_unit_text = 'NO Units';
			}

			if ($product_unit_id != 0) {
				if ($category_unit_id != $product_unit_id) {
					$is_popup = 1;
					$data['category_unit_id'] = $category_unit_id;
					$data['category_unit_text'] = ' (' . $category_unit_text . ')';
					$data['product_unit_id'] = $pop_product_unit_id;
					$data['product_unit_text'] = ' (' . $product_unit_text . ')';
				}
				$data['is_popup'] = $is_popup;
			}
		}
		return $data;
	}

	/**
	 * Get product id based on product sku
	 * @param : product sku
	 * @author : kalyan 28th Oct. 2017
	 * @return : array of products
	 */
	public function getProductBySku($sku) {

		$sql = "SELECT p.product_id,p.store_sales,p.hsn_code,p.quantity,p.piece_in_set FROM " . DB_PREFIX . "product p WHERE p.sku= '" . $this->db->escape($sku) . "'";
		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * public method to change product status for selected product ids
	 * @request : product_ids: array of product id
	 * @request : product_status: integer of status id
	 * @author: vikas,2017
	 */
	public function bulkUpdateChangeProductStatus($product_status_id, $product_ids, $field = 'status', $route = null) {
		$sql = "SELECT product_id, status, weight
				FROM " . DB_PREFIX . "product
			    WHERE product_id IN (" . implode(",", $product_ids) . ") ";

		$query = $this->db->query($sql);

		foreach ($query->rows as $pid_status) {
			if ($this->request->get['route'] == 'cron/cron/disableWrongPricedGarmentProductPerGST') {
				$source_field = 'cron/disableWrongPricedGarmentProductPerGST';
			} else {
				$source_field = 'product_list';
			}

			$changes_data = array(
				$field => array(
					'old_value' => $pid_status[$field],
					'new_value' => $product_status_id,
				),
			);
			$this->_product_change_log->recordLogs($pid_status['product_id'], $changes_data, $source_field, $route);
		}

		$sql = "UPDATE " . DB_PREFIX . "product
                           set " . $field . " = " . $product_status_id . ",
                           date_modified = NOW()
                           WHERE product_id in (" . implode(',', $product_ids) . ") ";
		$query = $this->db->query($sql);

	}

	/**
	 * public method to change product status for selected product ids
	 * @request : product_ids: array of product id
	 * @request : product_status: integer of status id
	 * @author: vikas,2017
	 */
	public function bulkUpdateAssignStoreCode($store_code, $product_ids) {
		$sql = "SELECT product_id, store_sales
				FROM " . DB_PREFIX . "product
			    WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);

		foreach ($query->rows as $pid_store_sales) {
			$source_field = 'product_list';
			$changes_data = array(
				'store_sales' => array(
					'old_value' => $pid_store_sales['store_sales'],
					'new_value' => $store_code,
				),
			);
			$this->_product_change_log->recordLogs($pid_store_sales['product_id'], $changes_data, $source_field);
		}

		$sql = "UPDATE " . DB_PREFIX . "product
				SET	store_sales = '" . $this->db->escape($store_code) . "'
				WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);
	}

	/**
	 * public method to change product status for selected product ids
	 * @request : product_ids: array of product id
	 * @request : product_status: integer of status id
	 * @author: vikas,2017
	 */
	public function bulkUpdateChangeQuantity($quantity, $product_ids) {
		$sql = "SELECT product_id, quantity
				FROM " . DB_PREFIX . "product
			    WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);

		foreach ($query->rows as $pid_store_sales) {
			$source_field = 'product_list';
			$changes_data = array(
				'quantity' => array(
					'old_value' => $pid_store_sales['quantity'],
					'new_value' => $quantity,
				),
			);
			$this->_product_change_log->recordLogs($pid_store_sales['product_id'], $changes_data, $source_field);
		}

		$sql = "UPDATE " . DB_PREFIX . "product
				SET	quantity = '" . (int) ($quantity) . "'
				WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);
	}
	//To get franchise lists from the oc_products
	public function getFranchiseList() {
		$sql = "SELECT
                    customer_id ,
                    concat(customer_id, ' ', firstname, ' ', lastname) franchise_name
                FROM
                    " . DB_PREFIX . "customer c
                WHERE
                    EXISTS(
                        SELECT 1 FROM " . DB_PREFIX . "franchise_data WHERE franchise_id = c.customer_id
                    )
                ORDER BY
                    customer_id ASC ";
		$query = $this->db->query($sql);

		return $query->rows;
	}

	//Get Product Id from oc_order_product
	public function getProductId($order_id) {
		$sql = "select product_id from
		       " . DB_PREFIX . "order_product
		       WHERE order_id = '" . (int) $order_id . "'
		       ";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	/**
	 * public method To get product assigned to the franchise
	 * @request : product_id   : integer of product id
	 * @request : franchise_id : integer of franchise id
	 **/
	public function getProductToFranchise($product_id, $franchise_id) {
		$sql = "SELECT new_product_id from
		       " . DB_PREFIX . "product_to_franchise
		       WHERE product_id = '" . (int) $product_id . "'
		       AND franchise_id = '" . (int) $franchise_id . "' ";

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			return $query->row['new_product_id'];
		} else {
			return '';
		}

	}

	/**
	 * To insert the old product id with a franchise id assigned to new product id
	 * @request : $product_id      : old previous id
	 * @request : $franchise_id    : franchise id
	 * @request : $new_product_id  : new product id
	 **/
	public function insertProductToFranchise($product_id, $franchise_id, $new_product_id) {
		$sql = "INSERT INTO " . DB_PREFIX . "product_to_franchise
                              SET product_id     = '" . (int) $product_id . "' ,
                                  franchise_id   = '" . (int) $franchise_id . "' ,
                                  new_product_id = '" . (int) $new_product_id . "' ";
		$query = $this->db->query($sql);
	}

	/**
	 * public method To get product assigned to the franchise
	 * @request : product_id   : integer of product id
	 **/
	public function getProductQuantity($product_id) {
		$sql = "SELECT quantity from
		       " . DB_PREFIX . "product
		       WHERE product_id = '" . (int) $product_id . "' ";

		$query = $this->db->query($sql);
		if ($query->num_rows) {
			return $query->row['quantity'];
		} else {
			return 0;
		}

	}

	/**
	 * public method To get product assigned to the franchise
	 * @request : new_product_id   : integer of new product id
	 * @request : updated_quantity : integer of updated quantity
	 **/
	public function updateNewProductQuantity($new_product_id, $updated_quantity) {
		$sql = "UPDATE " . DB_PREFIX . "product SET
		       quantity = " . (int) $updated_quantity . "
		       WHERE product_id = '" . (int) $new_product_id . "' ";

		$query = $this->db->query($sql);

		return true;
	}

	public function getFranchiseData($franchise_id) {
		$sql = "SELECT franchise_prefix, concat(firstname,'_',lastname) AS name FROM " . DB_PREFIX . "franchise_data AS FD
		       INNER JOIN " . DB_PREFIX . "customer AS OC
		       ON OC.customer_id = FD.franchise_id
		       WHERE FD.franchise_id = '" . (int) $franchise_id . "' LIMIT 1 ";

		$query = $this->db->query($sql);
		if ($query->num_rows) {
			return $query->row;
		} else {
			return array();
		}
	}
	/**
	 * Method to product seller has seller invoice generate status 0
	 * @param $product_id
	 * @author Kalyan 27th Nov 2017
	 */
	public function getSellerInvoiceGenerateStatusOfProduct($product_id) {

		$sql = "SELECT mp.product_id
                FROM " . DB_PREFIX . "ms_product mp
                INNER JOIN " . DB_PREFIX . "ms_seller mss
                ON mp.seller_id=mss.seller_id
                WHERE mp.product_id = '" . (int) $product_id . "'
                AND mss.seller_invoice_generate=0";

		$query = $this->db->query($sql);

		if ($query->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method to product seller has seller invoice generate status 0
	 * @param $product_id               New product id to which value is to be assigned
	 * @param $product_option_value_id  ID of the new product option value id
	 * @param $quantity                 Quantity of the product and product option value id
	 * @author Ashish
	 */
	public function updateProductOptionQuantity($new_product_id, $product_option_value_id, $quantity) {

		$product_option_value = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int) $product_option_value_id . "'");
		if (!$product_option_value->num_rows) {
			return 0;
		}

		$option_value_data = $product_option_value->row;

		$sql = "UPDATE " . DB_PREFIX . "product_option_value SET quantity = quantity + " . (int) $quantity . "
                                    WHERE product_id = " . (int) $new_product_id . " AND
                                    option_id = " . (int) $option_value_data['option_id'] . " AND
                                    option_value_id = " . (int) $option_value_data['option_value_id'] . "";

		$this->db->query($sql);
	}

	/**
	 * public method to change product status for selected product ids
	 * @request : product_ids: array of product id
	 * @request : product_status: integer of status id
	 * @author: ashish,2017
	 */
	public function bulkUpdateAssignExclusiveStatus($exclusive_status, $product_ids) {
		$sql = "SELECT product_id, exclusive
				FROM " . DB_PREFIX . "product
			    WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);

		foreach ($query->rows as $pid_status) {
			$source_field = 'product_list';

			$changes_data = array(
				'status' => array(
					'old_value' => $pid_status['exclusive'],
					'new_value' => $exclusive_status,
				),
			);

			$this->_product_change_log->recordLogs($pid_status['product_id'], $changes_data, $source_field);
		}

		$sql = "UPDATE " . DB_PREFIX . "product
				SET	exclusive = '" . $exclusive_status . "',
				date_modified = NOW()
				WHERE product_id IN (" . implode(",", $product_ids) . ") ";

		$query = $this->db->query($sql);
	}

	/**
	 * public method to assign products sor for selected product ids
	 * @request : product_ids: array of product id
	 * @request : sor_days: integer of sor_days
	 * @request : sor_type: string of sor_type
	 * @return 	: string(mesage)
	 * @author  : mahaveer, 2019
	 */
	public function bulkUpdateSor($sor_days, $sor_type, $product_ids, $action = "remove") {
		$success = 0;
		$sql = "SELECT p.product_id, p.non_returnable, pst.sor_type, pst.sor_days
				FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_sor_terms pst
				ON p.product_id = pst.product_id
			    WHERE p.product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);

		foreach ($query->rows as $pid_sor) {
			if (!$pid_sor['non_returnable']) {
				$success = 1;
				if ($action == 'remove') {
					$this->db->query("DELETE from " . DB_PREFIX . "product_sor_terms
                  	where product_id = " . (int) $pid_sor['product_id']);

					$source_field = 'product_sor_terms_delete';
					$changes_data = array(
						'sor_type' => array(
							'old_value' => $pid_sor['sor_type'],
							'new_value' => '',
						),
						'sor_days' => array(
							'old_value' => $pid_sor['sor_days'],
							'new_value' => '',
						),
					);
					$this->_product_change_log->recordLogs($pid_sor['product_id'], $changes_data, $source_field);
				} else {

					if ($pid_sor['sor_days']) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_sor_terms
                              SET sor_type = '" . $this->db->escape($sor_type) . "',
                                  sor_days = '" . (int) $sor_days . "'
                                  where product_id = " . (int) $pid_sor['product_id']);
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_sor_terms
                              SET product_id = " . (int) $pid_sor['product_id'] . ",
                                  sor_type = '" . $this->db->escape($sor_type) . "',
                                  sor_days = '" . (int) $sor_days . "'");
					}

					$source_field = 'product_sor_terms_update';
					$changes_data = array(
						'sor_type' => array(
							'old_value' => $pid_sor['sor_type'],
							'new_value' => $sor_type,
						),
						'sor_days' => array(
							'old_value' => $pid_sor['sor_days'],
							'new_value' => $sor_days,
						),
					);
					$this->_product_change_log->recordLogs($pid_sor['product_id'], $changes_data, $source_field);

				}

				// Update change in solr
				$solr_data = array();
				$solr_data['product_id'] = $pid_sor['product_id'];
				$solr_data['skip_filter_groups'] = true;

				if ($action == 'remove') {
					$solr_data['fields']['sor_type'] = "";
					$solr_data['fields']['sor_days'] = 0;
				} else {
					$solr_data['fields']['sor_type'] = $sor_type;
					$solr_data['fields']['sor_days'] = $sor_days;
				}

				SolrProduct::atomicUpdateToSolr($solr_data);

			}
		}

		return $success;

	}

	/**
	 * public method to change product status for selected product ids
	 * @request : product_ids: array of product id
	 * @request : update_minimum_quantity: integer of update minimum quantity
	 * @author: vikas,2018
	 */
	public function applyBulkUpdateMinimumQuantity($update_minimum_quantity, $product_ids) {
		$sql = "SELECT product_id, minimum
				FROM " . DB_PREFIX . "product
			    WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);

		foreach ($query->rows as $pid_product) {
			$source_field = 'product_list';
			$changes_data = array(
				'minimum' => array(
					'old_value' => $pid_product['minimum'],
					'new_value' => $update_minimum_quantity,
				),
			);
			$this->_product_change_log->recordLogs($pid_product['product_id'], $changes_data, $source_field);
		}

		$sql = "UPDATE " . DB_PREFIX . "product
				SET	minimum = '" . (int) $update_minimum_quantity . "'
				WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);
	}

	/**
	 * Update perticular product convert to single
	 * @param  $product_id : Integer of product id of the product to copy as single
	 * @author vikas , Jan 2017
	 */
	public function applyBulkProductConvertToSingle($product_id) {

		$this->load->model('catalog/option');
		$query = $this->db->query("SELECT p.product_id,
										  p.model,
										  p.quantity,
										  p.piece_in_set,
										  pd.set_description
								   FROM " . DB_PREFIX . "product p
								   LEFT JOIN " . DB_PREFIX . "product_description pd
								     ON (p.product_id = pd.product_id)
								   WHERE p.product_id = '" . (int) $product_id . "'
								    AND  pd.language_id = '" . (int) $this->config->get('config_language_id') . "'");
		if ($query->num_rows) {
			$data = $query->row;

			$exsisting_query = $this->db->query("SELECT DISTINCT model
												 FROM " . DB_PREFIX . "product p
												 WHERE model LIKE " . "'" . $data['model'] . '-SNGL' . "'");
			if ($exsisting_query->num_rows) {
				$existing_single = true;
			} else {
				$existing_single = false;
			}
			if ($data['piece_in_set'] > 1 && !$existing_single) {
				$data['product_description'] = $this->getProductDescriptions($product_id);

				$changes_data = array(
					'piece_in_set' => array(
						'old_value' => $data['piece_in_set'],
						'new_value' => 1,
					),
					'set_description' => array(
						'old_value' => $data['product_description'][1]['set_description'],
						'new_value' => '',
					),
				);

				if (!empty($data['product_option'])) {
					$data['product_option'] = $this->getProductOptions($product_id);
				} else {

					$data['piece_in_set'] = 1;

					$data['product_description'][1]['set_description'] = '';

					$options_found = 0;

					$description_var = $this->getProductDescriptions($product_id);

					//USING REGEX TO MATCH CONSECUTIVE DECIMALS
					if ($description_var[1]['set_description']) {
						preg_match_all('!\d\d!', $description_var[1]['set_description'], $matches);
					}

					$arr_option = array();
					$product_option_value = false;
					if (isset($matches) && !empty($matches)) {

						$sizes_values = $this->model_catalog_option->getOptionValues('11');
						$size_chart = array_column($sizes_values, 'option_value_id', 'name');

						foreach ($matches[0] as $key => $value) {
							if (array_key_exists($value, $size_chart)) {
								$product_option_value = true;
								$options_found += 1;

								$arr_option['product_option_value'][] = array(
									'option_value_id' => $size_chart[$value],
									'quantity' => $data['quantity'],
									'subtract' => '1',
								);
							}

						}
						$arr_option['type'] = 'select';
						$arr_option['option_id'] = '11';
						$arr_option['required'] = '1';
						$arr_option['name'] = 'Size';

					}

					if ($product_option_value) {
						// Re-adjusting the total qty for singles
						$data['quantity'] = $options_found ? $options_found * (int) ($data['quantity']) : (int) ($data['quantity']);

						$data['product_option'] = array($arr_option);

						// update particular field after product convert to single
						$sql = $this->db->query("UPDATE " . DB_PREFIX . "product
												 SET quantity = '" . (int) $data['quantity'] . "',
													piece_in_set = '" . (int) $data['piece_in_set'] . "'
												 WHERE product_id = " . (int) $product_id . "");
						if (isset($data['product_description'])) {
							foreach ($data['product_description'] as $language_id => $value) {
								$this->db->query("UPDATE " . DB_PREFIX . "product_description
									              SET set_description = '" . $this->db->escape($value['set_description']) . "'
					                              WHERE product_id = '" . (int) $product_id . "'
								                    AND language_id = '" . (int) $language_id . "'");
							}
						}

						if (isset($data['product_option'])) {
							foreach ($data['product_option'] as $product_option) {
								if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
									if (isset($product_option['product_option_value'])) {

										$this->db->query("INSERT INTO " . DB_PREFIX . "product_option
														  SET product_id = '" . (int) $product_id . "',
														      option_id = '" . (int) $product_option['option_id'] . "',
														      required = '" . (int) $product_option['required'] . "'");

										$product_option_id = $this->db->getLastId();

										foreach ($product_option['product_option_value'] as $product_option_value) {
											$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value
															  SET product_option_id = '" . (int) $product_option_id . "',
															      product_id = '" . (int) $product_id . "',
															      option_id = '" . (int) $product_option['option_id'] . "',
															      option_value_id = '" . (int) $product_option_value['option_value_id'] . "',
															      quantity = '" . (int) $product_option_value['quantity'] . "',
															      subtract = '" . (int) $product_option_value['subtract'] . "'");
										}
									}
								} else {
									$this->db->query("INSERT INTO " . DB_PREFIX . "product_option
										 			  SET product_id = '" . (int) $product_id . "',
											 			  option_id = '" . (int) $product_option['option_id'] . "',
											 			  value = '" . $this->db->escape($product_option['value']) . "',
											 			  required = '" . (int) $product_option['required'] . "'");
								}
							}
						}
						$source_field = 'product_list';
						$this->_product_change_log->recordLogs($product_id, $changes_data, $source_field);
					}
				}
			}
		}
	}
	/**
	 * public method for product sync to solr
	 * @param : product_ids: array of product id
	 * @return : null
	 * @author: vikas,2017
	 */
	public function applyBulkProductSyncToSolr($product_ids) {
		$obj_solr_product = new SolrProduct($this->registry);
		$product_data = array();

		$sql_product = "SELECT p.product_id,
							   p.sku,
							   p.model,
							   p.price,
							   p.selling_price,
							   p.commission,
							   p.is_single,
							   p.status,
							   p.quantity,
							   p.sort_order,
							   p.stock_status_id,
							   DATE_FORMAT(CONVERT_TZ(p.date_available,'+00:00','+01:00'),'%Y-%m-%dT%TZ') AS date_available,
							   DATE_FORMAT(CONVERT_TZ(p.date_added,'+00:00','+01:00'),'%Y-%m-%dT%TZ') AS date_added,
							   p.minimum,
							   p.viewed,
							   p.piece_in_set,
							   p.store_sales,
							   p.exclusive,
							   p.hsn_code,
							   p.only_for_search,
							   p.tax_class_id,
							   p.is_archived,
							   IFNULL(p.franchise_id,0) as franchise_id,
							   p.rating,
							   p.is_associate
						FROM " . DB_PREFIX . "product p
						WHERE p.product_id IN (" . implode(',', $product_ids) . ")";
		$query_product = $this->db->query($sql_product);

		if ($query_product->num_rows) {

			foreach ($query_product->rows as $key => $value) {
				$product_data[$value['product_id']] = $value;
				// get data from product description
				$sql_prod_desc = "SELECT pd.name,
							   			 pd.set_description,
							   			 pd.description,
							   			 pd.tag
							   	  FROM " . DB_PREFIX . "product_description pd
								  WHERE pd.product_id = " . (int) $value['product_id'];
				$query_prod_desc = $this->db->query($sql_prod_desc);

				// set data of product description
				$product_data[$value['product_id']]['name'] = $query_prod_desc->row['name'];
				$product_data[$value['product_id']]['set_description'] = $query_prod_desc->row['set_description'];
				$product_data[$value['product_id']]['description'] = $query_prod_desc->row['description'];
				$product_data[$value['product_id']]['tag'] = $query_prod_desc->row['tag'];

				// get data from product hotness
				$sql_prod_hotness = "SELECT oph.hotness_points as hotness_value
							   	  	 FROM " . DB_PREFIX . "product_hotness oph
								  	 WHERE oph.product_id = " . (int) $value['product_id'];
				$query_prod_hotness = $this->db->query($sql_prod_hotness);

				// set data of product hotness
				if ($query_prod_hotness->num_rows) {
					$product_data[$value['product_id']]['hotness_value'] = $query_prod_hotness->row['hotness_value'];
				} else {
					$product_data[$value['product_id']]['hotness_value'] = 0;
				}

				// get data From product special
				$sql_prod_spcl = "SELECT ps.price,
							   			 DATE_FORMAT(CONVERT_TZ(ps.date_start,'+00:00','+01:00'),'%Y-%m-%dT%TZ') as date_start,
							   			 DATE_FORMAT(CONVERT_TZ(ps.date_end,'+00:00','+01:00'),'%Y-%m-%dT%TZ') as date_end
							   	  FROM " . DB_PREFIX . "product_special ps
		                    	  WHERE ps.product_id = " . (int) $value['product_id'];
				$query_prod_spcl = $this->db->query($sql_prod_spcl);

				// set data of product special
				if ($query_prod_spcl->num_rows) {
					$product_data[$value['product_id']]['special_price'] = $query_prod_spcl->row['price'];
					$product_data[$value['product_id']]['date_start'] = $query_prod_spcl->row['date_start'];
					$product_data[$value['product_id']]['date_end'] = $query_prod_spcl->row['date_end'];
				} else {
					$product_data[$value['product_id']]['special_price'] = '';
					$product_data[$value['product_id']]['date_start'] = '';
					$product_data[$value['product_id']]['date_end'] = '';
				}

				// get data From stock status
				$sql_stock_status = "SELECT ss.name as stock_status
								   	 FROM " . DB_PREFIX . "stock_status ss
			                    	 WHERE ss.stock_status_id = " . (int) $value['stock_status_id'];
				$query_stock_status = $this->db->query($sql_stock_status);

				// set data of product hotness
				$product_data[$value['product_id']]['stock_status'] = $query_stock_status->row['stock_status'];

				// get data from seller
				$sql_sllr_info = "SELECT ms.seller_id,
										 ms.nickname,
										 ms.city,
										 ms.seller_status,
										 ms.vacation_mode,
										 ms.app_only,
										 ms.non_serviceable_areas
							   	  FROM " . DB_PREFIX . "ms_seller ms
							   	  INNER JOIN " . DB_PREFIX . "ms_product mp
							   	    ON mp.seller_id = ms.seller_id
		                    	  WHERE mp.product_id = " . (int) $value['product_id'];
				$query_sllr_info = $this->db->query($sql_sllr_info);

				// set data of seller
				$product_data[$value['product_id']]['seller_id'] = $query_sllr_info->row['seller_id'];
				$product_data[$value['product_id']]['nickname'] = $query_sllr_info->row['nickname'];
				$product_data[$value['product_id']]['city'] = $query_sllr_info->row['city'];
				$product_data[$value['product_id']]['seller_status'] = $query_sllr_info->row['seller_status'];
				$product_data[$value['product_id']]['vacation_mode'] = $query_sllr_info->row['vacation_mode'];
				$product_data[$value['product_id']]['app_only'] = $query_sllr_info->row['app_only'];
				$product_data[$value['product_id']]['non_serviceable_areas'] = $query_sllr_info->row['non_serviceable_areas'];

				// get data from product to categeries and category description
				$sql_prod_cate = "SELECT pc.category_id as category_id,
	   									 cd.name as categories
							   	  FROM " . DB_PREFIX . "product_to_category pc
							   	  INNER JOIN " . DB_PREFIX . "category_description cd
							   	    ON cd.category_id = pc.category_id
		                    	  WHERE pc.product_id = " . (int) $value['product_id'] . "
		                    	  GROUP BY pc.category_id";
				$query_prod_cate = $this->db->query($sql_prod_cate);

				// set data of product category
				if (!empty($query_prod_cate->num_rows)) {
					$category_array = array();
					foreach ($query_prod_cate->rows as $category_values) {
						$category_array[$category_values['category_id']] = $category_values['categories'];
						$product_data[$value['product_id']]['category_id'][] = $category_values['category_id'];
					}
					$product_data[$value['product_id']]['categories'] = implode(',', $category_array);
				} else {
					$product_data[$value['product_id']]['category_id'] = NULL;
				}

				// get data from product filter and filter category
				$sql_prod_filter = "SELECT pf.filter_id as filter_id,
										   fd.name as filters
							   	  FROM " . DB_PREFIX . "product_filter pf
							   	  INNER JOIN  " . DB_PREFIX . "filter_description fd
							   	  	ON fd.filter_id = pf.filter_id
		                    	  WHERE pf.product_id = " . (int) $value['product_id'] . "
		                    	  GROUP BY pf.filter_id";
				$query_prod_filter = $this->db->query($sql_prod_filter);

				// set data of product filter and filter category
				if (!empty($query_prod_filter->num_rows)) {
					$filter_array = array();
					foreach ($query_prod_filter->rows as $filter_values) {
						$filter_array[$filter_values['filter_id']] = $filter_values['filters'];
						$product_data[$value['product_id']]['filter_id'][] = $filter_values['filter_id'];
					}
					$product_data[$value['product_id']]['filters'] = implode(',', $filter_array);

					$filter_by_group = array();
					if (!empty($filter_array)) {
						foreach ($filter_array as $filter_id_key => $filter_values_string) {
							$filter_group_id = $obj_solr_product->getFilterGroup($filter_id_key);
							$filter_by_group[$filter_group_id][] = $filter_id_key;
						}
					}

					if (count($filter_by_group) > 0) {
						foreach ($filter_by_group as $group_id => $arr_filter_id) {
							$dynamic_filed_name = 'filter_group_df_' . $group_id;
							$product_data[$value['product_id']][$dynamic_filed_name] = $arr_filter_id;
						}
					}
				} else {
					$product_data[$value['product_id']]['filter_id'] = NULL;
				}

				// get data from product filter and filter category
				$sql_prod_store = "SELECT p2s.store_id as store_id
							   	  FROM " . DB_PREFIX . "product_to_store p2s
		                    	  WHERE p2s.product_id = " . (int) $value['product_id'];
				$query_prod_store = $this->db->query($sql_prod_store);

				// set data of product filter and filter category
				if ($query_prod_store->num_rows) {
					$product_data[$value['product_id']]['store_id'] = array_column($query_prod_store->rows, 'store_id');
				} else {
					$product_data[$value['product_id']]['store_id'] = NULL;
				}

				// get data for sor term
				$sql_sor_terms = "
								SELECT
									sor_type,
									sor_days
								FROM " . DB_PREFIX . "product_sor_terms
								WHERE product_id = '" . (int) $value['product_id'] . "'";

				$result_sor_terms = $this->db->query($sql_sor_terms);

				// set data for sor terms
				if ($result_sor_terms->num_rows) {
					$product_data[$value['product_id']]['sor_type'] = $result_sor_terms->row['sor_type'] ?? NULL;
					$product_data[$value['product_id']]['sor_days'] = $result_sor_terms->row['sor_days'] ?? NULL;
				} else {
					$product_data[$value['product_id']]['sor_type'] = NULL;
					$product_data[$value['product_id']]['sor_days'] = NULL;
				}
			}

			if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
				foreach ($product_data as $product_id_key => $value) {

					// logs for category in product change log
					$sql_prod_cate_log = "SELECT pc.category_id as category_id,
	   									 	     cd.name as categories
					   	  		  		  FROM " . DB_PREFIX . "product_to_category pc
							   	  	  	  INNER JOIN " . DB_PREFIX . "category_description cd
							   	    		ON cd.category_id = pc.category_id
	                    	  	  		  WHERE pc.product_id = " . (int) $product_id_key . "
		                    	  	  	  GROUP BY cd.category_id";
					$query_prod_cate_log = $this->db->query($sql_prod_cate_log);
					if ($query_prod_cate_log->num_rows) {
						foreach ($query_prod_cate_log->rows as $cate_value) {
							$change_details = array(
								'product_id' => $product_id_key,
								'category_id' => array(
									'old' => '',
									'new' => $cate_value['category_id'],
								),
								'categories' => array(
									'old' => '',
									'new' => $cate_value['categories'],
								),
							);

							$changes_data = array(
								'product_id' => $product_id_key,
								'table_name' => 'oc_product_to_category',
								'change_details' => serialize($change_details),
							);
						}
					}

					// logs for filter in product change log
					$sql_prod_filter_log = "SELECT pf.filter_id as filter_id,
											   	   fd.name as filters
								   	  		FROM " . DB_PREFIX . "product_filter pf
							   	  			INNER JOIN  " . DB_PREFIX . "filter_description fd
								   	  			ON fd.filter_id = pf.filter_id
			                    	  		WHERE pf.product_id = " . (int) $product_id_key . "
		                    	  	  	  	GROUP BY fd.filter_id";
					$query_prod_filter_log = $this->db->query($sql_prod_filter_log);

					if ($query_prod_filter_log->num_rows) {
						foreach ($query_prod_filter_log->rows as $filter_value) {
							$change_details = array(
								'product_id' => $product_id_key,
								'filter_id' => array(
									'old' => '',
									'new' => $filter_value['filter_id'],
								),
								'filters' => array(
									'old' => '',
									'new' => $filter_value['filters'],
								),
							);

							$changes_data = array(
								'product_id' => $product_id_key,
								'table_name' => 'oc_product_filter',
								'change_details' => serialize($change_details),
							);
						}
					}

					// logs for product store in product change log
					$sql_prod_store_log = "SELECT p2s.store_id as store_id
								   	   FROM " . DB_PREFIX . "product_to_store p2s
			                    	   WHERE p2s.product_id = " . (int) $product_id_key;
					$query_prod_store_log = $this->db->query($sql_prod_store_log);

					if ($query_prod_store_log->num_rows) {
						foreach ($query_prod_store_log->rows as $store_value) {
							$change_details = array(
								'product_id' => $product_id_key,
								'store_id' => array(
									'old' => '',
									'new' => $store_value['store_id'],
								),
							);

							$changes_data = array(
								'product_id' => $product_id_key,
								'table_name' => 'oc_product_to_store',
								'change_details' => serialize($change_details),
							);
						}
					}

					$source_field = 'product_list';
					$table_name = 'product_change_log';
					$change_data = array(
						'sync_to_solr' => array(
							'old_value' => 'sync_to_solr',
							'new_value' => 'sync_to_solr',
						),
					);

					// delete product info in form oc_product change log
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_change_log WHERE product_id = " . (int) $product_id_key);

					$this->_product_change_log->recordLogs($product_id_key, $change_data, $source_field, '', $table_name);
					$obj_solr_product->editProductToSolr($value);
				}
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * public method to Commission update for selected product(s)
	 * @request : product_ids: array of product id
	 * @request : bulk_commission_update: integer of update minimum quantity
	 * @author: vikas,2018
	 */
	public function applyBulkUpdateCommission($bulk_commission_update, $product_ids) {
		$sql = "SELECT product_id, commission
				FROM " . DB_PREFIX . "product
			    WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);

		foreach ($query->rows as $pid_product) {
			$source_field = 'product_list';
			$changes_data = array(
				'commission' => array(
					'old_value' => $pid_product['commission'],
					'new_value' => $bulk_commission_update,
				),
			);
			$this->_product_change_log->recordLogs($pid_product['product_id'], $changes_data, $source_field);
		}

		$sql = "UPDATE " . DB_PREFIX . "product
				SET	commission = '" . (float) $bulk_commission_update . "'
				WHERE product_id IN (" . implode(",", $product_ids) . ") ";
		$query = $this->db->query($sql);
	}

	/**
	 * Method for set rating to product when product assign to seller
	 * @param : $product_ids : array of product_ids
	 * @return true
	 * @author : vikas/manoj S. R., Apr 2018
	 */
	public function setProductRatingWhenProductAssignToSeller($seller_id, $product_ids = array()) {

		/** set product rating  in oc_product table*/
		$product_rating = "0";
		$seller_rating_category = "0";

		$query_global_rating = $this->db->query("select seller_id, rule_type, GROUP_CONCAT(category_id) category_id, MIN(rating) as rating from oc_review_rules where seller_id = " . (int) $seller_id . "  group by rule_type");
		if ($query_global_rating->num_rows > 0) {
			foreach ($query_global_rating->rows as $val) {
				if ($val['rule_type'] == "global") {
					$product_rating = $val['rating'];
				}

				if ($val['rule_type'] == "category") {
					$seller_rating_category = $val['category_id'];
				}
			}
		}

		if (!empty($product_ids)) {
			$query_category = $this->db->query(" SELECT GROUP_CONCAT(rr.category_id) category_id
													    FROM oc_product_to_category p2c
													    INNER JOIN oc_review_rules rr
													      ON rr.category_id = p2c.category_id
													    WHERE rr.seller_id = " . (int) $seller_id . "
													      AND p2c.product_id IN (" . implode(',', $product_ids) . ")
													      AND rr.rule_type = 'category'
													    GROUP BY p2c.product_id ");

			if ($query_category->num_rows > 0) {
				$seller_rating_category = $query_category->row['category_id'];
			} else {
				$seller_rating_category = "0";
			}
		}

		if (!empty($seller_rating_category)) {

			$this->db->query('DROP TABLE IF EXISTS temp_rating_assign_product');

			$query_global_rating = $this->db->query("SET @seller_global_rating = " . $product_rating);

			$sql_product_rating = "CREATE TEMPORARY TABLE temp_rating_assign_product
									SELECT mp.product_id pid, mp.seller_id sid, group_concat(DISTINCT p2c.category_id) cid  , p2c.category_id cc,
								    (CASE
								        WHEN rr.rating IS NULL AND @seller_global_rating > 0 THEN CAST(@seller_global_rating AS UNSIGNED)
								       	WHEN p2c.category_id IS NOT NULL AND rr.rating IS NOT NULL THEN MIN(rr.rating)

								    END) as rating
								    FROM " . DB_PREFIX . "ms_product mp

								    INNER JOIN " . DB_PREFIX . "product_to_category p2c
								    ON mp.product_id = p2c.product_id

								    LEFT JOIN " . DB_PREFIX . "review_rules rr
								    ON p2c.category_id =rr.category_id

								    WHERE mp.seller_id = " . (int) $seller_id . "  ";

			if (!empty($product_ids)) {
				$sql_product_rating .= " AND mp.product_id IN (" . (implode(',', $product_ids)) . ")";
			}

			$sql_product_rating .= " GROUP BY mp.product_id ";

			$query_temp = $this->db->query($sql_product_rating);

			$this->db->query("UPDATE " . DB_PREFIX . "product p
								JOIN temp_rating_assign_product trap ON trap.pid = p.product_id
								SET p.rating = trap.rating");
		} else {

			// For global set rating only
			if ($product_rating > 0) {
				$sql_product_rating = "UPDATE " . DB_PREFIX . "product p
							JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id
							JOIN " . DB_PREFIX . "review_rules rr ON rr.seller_id = mp.seller_id
							SET p.rating = " . (int) $product_rating . "
							WHERE mp.seller_id = " . (int) $seller_id . " ";
			} else {
				$sql_product_rating = "UPDATE " . DB_PREFIX . "product p
							JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id
							SET p.rating = NULL
							WHERE mp.seller_id = " . (int) $seller_id . " ";
			}

			if (!empty($product_ids)) {
				$sql_product_rating .= " AND mp.product_id IN (" . (implode(',', $product_ids)) . ")";
			}

			$this->db->query($sql_product_rating);

		}

	}

	/**
	 * Method for check any product has contain product option
	 * @param : product_ids : Integer with in array of product ids
	 * @return : true or false
	 * @author: vikas, Apr 2018
	 */
	public function checkAnyProductHasProductOption($product_ids) {
		$result = array();

		//product_ids may be array or single id
		if (!empty($product_ids)) {

			if (is_array($product_ids)) {
				$product_ids = implode(',', $product_ids);
			}

			$sql = "SELECT product_id
            FROM " . DB_PREFIX . "product_option
            WHERE product_id IN (" . $product_ids . ") ";
			$query = $this->db->query($sql);

			if ($query->num_rows) {
				$result = array_column($query->rows, 'product_id');
			}
		}

		return $result;
	}

	/**
	 * Method for get Product Option Value id By Order Product Id
	 * @param  : Integer order_product_id
	 * @return : integer product_option_value_id
	 * @author : MSA, June 2018
	 */
	public function getOptionValueIdByOrderProductId($order_product_id) {
		if (!empty($order_product_id)) {
			$sql = "SELECT `product_option_value_id` FROM " . DB_PREFIX . "order_option
						WHERE order_product_id = '" . $this->db->escape($order_product_id) . "'
					LIMIT 1
			 		";
			$query = $this->db->query($sql);
			if ($query->num_rows) {
				return $query->rows[0]['product_option_value_id'];
			}
		}
		return 0;
	}

	/**
	 * Public Method to get seller(WSB Seller) Details by given seller_invoice_id,
	 * 	by using purchase firm id
	 * @author: Nishu, June 2018
	 */
	public function getWsbSellerBySellerInvoiceId($seller_invoice_id = 0) {
		$data = array();
		if (empty($seller_invoice_id)) {
			$sql = "
					SELECT
						ms.*
					FROM
						" . DB_PREFIX . "ms_seller AS ms
					WHERE
						ms.purchase_firm_id = 1
				 ";
		} else {
			$sql = "
					SELECT
						ms.*
					FROM
						" . DB_PREFIX . "ms_seller AS ms
						INNER JOIN
					" . DB_PREFIX . "vat_input_rules AS vat ON vat.purchase_firm_id = ms.purchase_firm_id
						INNER JOIN
					" . DB_PREFIX . "seller_invoice AS si ON si.vat_input_rule_id = vat.rule_id
					WHERE
						si.seller_invoice_id = " . (int) $seller_invoice_id . "
				 ";
		}

		$result = $this->db->query($sql);
		if ($result->num_rows > 0) {
			$data = $result->row;
		}

		return $data;
	}

	/**
	 * Method to get associate products of a product
	 * @param : product_id
	 * @return : associate product list
	 * @author: Devendra, June 2018
	 */
	public function getAssociateProducts($product_id) {

		$sql = "SELECT p.image,
                       p.product_id,
                       p.model,
                       p.sku,
                       p.price,
                       p.selling_price,
					   p.seller_tax,
                       p.commission,
                       p.quantity,
                       p.status,
                       p.is_single,
                       p.piece_in_set,
                       p.tax_class_id,
					   p.exclusive,
					   pd.name,
                       pd.set_description,
                       p.weight,
                       p.weight_class_id,
                       p.shipping,
                       p.store_sales,
                       p.hsn_code,
                       p.minimum,
                       p.sor_product,
                       mp.seller_id,
                       p.sort_order,
                       p.mrp,
                       p.date_available,
                       p.franchise_id,
                       p.is_archived,
                       p.stock_status_id,
                       p.cod_available,
                       p.non_returnable,
											 p.is_associate,
                       p.rating as product_rating
				FROM " . DB_PREFIX . "product_to_associate ps
				INNER JOIN " . DB_PREFIX . "product p
					ON (p.product_id = ps.associate_product_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd
				  ON (p.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "ms_product mp
				  ON (mp.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_to_category pc
		          ON (p.product_id = pc.product_id)
				WHERE pd.language_id = 1 AND ps.product_id = '" . (int) $product_id . "' GROUP BY p.product_id";

		$query = $this->db->query($sql);

		$result = array();

		if ($query->num_rows) {

			foreach ($query->rows as $row) {

				// weight unit
				$sql = "SELECT unit FROM " . DB_PREFIX . "weight_class_description
                        WHERE weight_class_id = '" . (int) $row['weight_class_id'] . "'
                          AND language_id = '1'";
				$query = $this->db->query($sql);
				if ($query->num_rows) {
					$row['weight_unit'] = $query->row['unit'];
				} else {
					$row['weight_unit'] = '';
				}

				//  tax title
				$sql = "SELECT title FROM " . DB_PREFIX . "tax_class
                        WHERE tax_class_id = '" . (int) $row['tax_class_id'] . "'";
				$query = $this->db->query($sql);
				if ($query->num_rows) {
					$row['tax_title'] = $query->row['title'];
				} else {
					$row['tax_title'] = '';
				}

				// product status
				$sql = "SELECT name FROM " . DB_PREFIX . "product_status
                        WHERE product_status_id = '" . (int) $row['status'] . "'
                          AND language_id = '1'";

				$query = $this->db->query($sql);
				if ($query->num_rows) {
					$row['product_status'] = $query->row['name'];
				} else {
					$row['product_status'] = '';
				}

				// Product rating //commented by vikas(03-04-2018) bcz now rating field added in oc_product so don't need avg rating.
				//$row['product_rating'] = floor( $this->getProductRating($row['product_id']) );

				//getting vacation mode and seller status from ms_seller

				$sql = "SELECT vacation_mode, seller_status FROM " . DB_PREFIX . "ms_seller
                        WHERE seller_id = '" . (int) $row['seller_id'] . "'";
				$query = $this->db->query($sql);
				if ($query->num_rows) {
					$row['vacation_mode'] = $query->row['vacation_mode'];
					$row['seller_status'] = $query->row['seller_status'];

				} else {
					$row['vacation_mode'] = '';
					$row['seller_status'] = '';
				}

				$sql = "SELECT ood.name
						FROM oc_product_option opp
						INNER JOIN oc_option_description ood
						  ON ood.option_id = opp.option_id
						WHERE opp.product_id = '" . (int) $row['product_id'] . "'
						GROUP BY opp.product_id";
				$query = $this->db->query($sql);
				if ($query->num_rows) {
					$row['product_option_name'] = $query->row['name'];
				} else {
					$row['product_option_name'] = '';
				}
				$result[] = $row;
			}
		}

		return $result;
	}

	/**
	 * Method to get associate products ids of a product
	 * @param : product_id
	 * @return : associate products ids array
	 * @author: Devendra, June 2018
	 */
	public function getAssociateProductIds($product_id) {
		$sql = "SELECT associate_product_id FROM " . DB_PREFIX . "product_to_associate
					WHERE product_id='" . (int) $product_id . "' ORDER BY associate_product_id";

		$query = $this->db->query($sql);
		if ($query->num_rows) {
			return array_column($query->rows, 'associate_product_id');
		}

		return array();
	}

	/**
	 * Method to add associate products to a product
	 * @param : product_id, associate_product_ids
	 * @return : true or false
	 * @author: Devendra, June 2018
	 */
	public function addAssociateProducts($product_id, $associate_product_ids) {
		foreach ($associate_product_ids as $associate_product_id) {
			$sql = "INSERT IGNORE INTO " . DB_PREFIX . "product_to_associate
							SET product_id='" . (int) $product_id . "', associate_product_id='" . (int) $associate_product_id . "'";
			$this->db->query($sql);
		}

		return true;
	}

	/**
	 * Method to delete all associate products of a product
	 * @param : product_id
	 * @return : true or false
	 * @author: Devendra, June 2018
	 */
	public function deleteAllAssociateProducts($product_id) {
		$sql = "DELETE FROM " . DB_PREFIX . "product_to_associate
						WHERE product_id='" . (int) $product_id . "'";
		return $this->db->query($sql);
	}

	/**
	 * Method to update or insert data in product_image
	 * @param : $product_detail,$update
	 * @return : true or false
	 * @author: Rahul, 2 July 2018
	 */
	public function mergeDifferentDesignProduct($product_detail, $update = 1) {
		if ($update == '1') {
			$sql = "UPDATE " . DB_PREFIX . "product_image
					                  SET design_group_title = '" . $this->db->escape($product_detail['design_group_title']) . "',
					                  front_image = '" . (int) $product_detail['front_image'] . "',
					                  group_sort_order = '" . (int) $product_detail['group_sort_order'] . "'
					                  WHERE product_image_id = '" . (int) $product_detail['product_image_id'] . "'";
		} else {
			$sort_order = '1';
			$sql = "INSERT INTO " . DB_PREFIX . "product_image
					                  SET product_id = '" . (int) $product_detail['product_id'] . "',
					                      image = '" . $this->db->escape($product_detail['image']) . "',
					                      sort_order = '" . (int) $sort_order . "',
					                      front_image = '" . (int) $product_detail['front_image'] . "',
					                  default_image = '" . (int) $product_detail['default_image'] . "',
					                  design_group_title = '" . $this->db->escape($product_detail['design_group_title']) . "',
					                  image_dimensions = '" . $this->db->escape($product_detail['image_dimensions']) . "',
					                  group_sort_order = '" . (int) $product_detail['group_sort_order'] . "'";
		}
		return $this->db->query($sql);
	}

	/**
	 * Method to reset value default iamge acording proguct_id
	 * @param : $product_id
	 * @return : true or false
	 * @author: Rahul, 3 July 2018
	 */
	public function resetDefaultDesignProduct($product_id) {
		$sql = "UPDATE " . DB_PREFIX . "product_image
					                  SET design_group_title = '',
					                  front_image = '0',
					                  group_sort_order = '0'
					                  WHERE product_id = '" . (int) $product_id . "'";
		return $this->db->query($sql);
	}

	/**
	 * Method to update the quantity of combo product and its associate on basis of a associate product
	 * @param : associate product_id, data
	 * @return : true or false
	 * @author: Devendra, July 2018
	 */
	public function syncComboProductWithAssociate($associate_product_id, $data) {
		$sql = "SELECT product_id FROM " . DB_PREFIX . "product_to_associate
						WHERE associate_product_id='" . (int) $associate_product_id . "'";
		$result = $this->db->query($sql);
		if ($result->num_rows < 1) {
			return true;
		}
		// update all combo product
		foreach ($result->rows as $product) {
			$combo_product_id = $product['product_id'];

			$associate_product_ids = $this->getAssociateProductIds($combo_product_id);

			$all_product_ids = array_merge($associate_product_ids, array($combo_product_id));

			$sql = "UPDATE `" . DB_PREFIX . "product` SET
								cod_available = '" . (int) $data['cod_available'] . "',
								non_returnable = '" . (int) $data['non_returnable'] . "'
							WHERE
								product_id IN(" . implode(',', $all_product_ids) . ")";

			$this->db->query($sql);
		}
	}

	public function getOrderProductMrp(int $product_id) {
		$sql = "SELECT mrp
                FROM " . DB_PREFIX . "product
                WHERE product_id = '" . (int) $product_id . "'";
		$mrp = (float) $this->db->query($sql)->row['mrp'];
		return $mrp;
	}
	public function updateOrderProductSellingPrice(int $order_product_id, float $selling_price) {
		$sql = "UPDATE
		 			" . DB_PREFIX . "order_product
                SET
                	price_per_piece = '" . (float) $selling_price . "'
                WHERE
                	order_product_id = '" . (int) $order_product_id . "'
                ";
		$this->db->query($sql);
	}
	public function updateOrderProductOutputTaxRate(int $order_product_id, float $output_tax_rates): void{
		$sql = "UPDATE
        			" . DB_PREFIX . "order_product
                SET
                	output_tax_rates = '" . (float) $output_tax_rates . "'
                WHERE
                	order_product_id = '" . (int) $order_product_id . "'
                ";
		$this->db->query($sql);
	}
	public function updateOrderProductTransferPrice(int $order_product_id, float $transfer_price) {
		$sql = "UPDATE
		 			" . DB_PREFIX . "order_product
                SET
                	transfer_price_per_piece = '" . (float) $transfer_price . "'
                WHERE
                	order_product_id = '" . (int) $order_product_id . "'
                ";
		$this->db->query($sql);
	}
	public function updateOrderProductSellerInputTaxRate(int $order_product_id, float $seller_input_tax): void{
		$sql = "UPDATE
        			" . DB_PREFIX . "order_product
                SET
                	seller_input_tax = '" . (float) $seller_input_tax . "'
                WHERE
                	order_product_id = '" . (int) $order_product_id . "'
                ";
		$this->db->query($sql);
	}

	public function getOrderProductQuantity($order_product_id) {
		$sql = "SELECT quantity from
		       " . DB_PREFIX . "order_product
		       WHERE order_product_id = '" . (int) $order_product_id . "' ";

		$query = $this->db->query($sql);
		if ($query->num_rows) {
			return $query->row['quantity'];
		} else {
			return 0;
		}

	}

	/**
	 * Get Products specials data
	 * @param: array $product_ids
	 * @return: array
	 * @author: MSA, Oct 2019
	 */
	public function getProductsSpecialsData(array $product_ids) {

		$product_ids = array_unique(array_filter(array_map('intval', $product_ids), function ($v) {return $v > 0;}));

		if (empty($product_ids)) {return array();}

		$product_ids = implode(',', $product_ids);

		$sql = "SELECT product_id, priority, price, discount_value,
					   discount_type, date_start, date_end
				FROM " . DB_PREFIX . "product_special
				WHERE
					product_id  IN(" . $this->db->escape($product_ids) . ")
				ORDER BY priority, price
				";
		$query = $this->db->query($sql);

		$product_special = array();

		if ($query->num_rows) {

			$product_special = array_combine(
				array_column($query->rows, 'product_id'),
				$query->rows
			);

		}

		return $product_special;
	}

	public function getProductsNameOnly($data = array()) {

		$sql = "SELECT
                       p.product_id,
					   					pd.name as name
				FROM " . DB_PREFIX . "product p
				STRAIGHT_JOIN " . DB_PREFIX . "product_description pd
				  ON p.product_id = pd.product_id
				     AND pd.language_id = 1";

		if (!empty($data['filter_name'])) {
			// $sql .= " AND pd.name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%'";
			$sql .= " AND (p.model LIKE '" . $this->db->escape(trim($data['filter_name'])) . "%' OR pd.name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%')";

		}

		$sort = $data['sort'] ?? '';
		$order = $data['order'] ?? '';

		$sql .= " ORDER BY pd.name ASC ";

		if (!empty($data['limit'])) {
			if ((int) $data['limit'] < 1) {
				$data['limit'] = 30;
			}

			$sql .= " LIMIT " . (int) $data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

}
