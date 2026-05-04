<?php
class ModelCatalogProduct extends Model {

	public function updateViewed($product_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int) $product_id . "'");
	}

	public function getProduct($product_id) {

		// Product must belong to the current store
		$sql = "SELECT * FROM " . DB_PREFIX . "product_to_store
                WHERE product_id = '" . (int) $product_id . "'
                  AND store_id = '" . (int) $this->config->getStoreIdForSql() . "'";
		$store_query = $this->db->query($sql);
		if ($store_query->num_rows == 0) {
			return false;
		}

		// Get seller_id
		$sql = "SELECT msp.seller_id, ms.nickname FROM " . DB_PREFIX . "ms_product msp
                INNER JOIN " . DB_PREFIX . "ms_seller ms ON msp.seller_id=ms.seller_id
                WHERE msp.product_id = '" . (int) $product_id . "'";
		$omp_query = $this->db->query($sql);
		if ($omp_query->num_rows == 0) // If no seller then we dont show the product details
		{
			return false;
		}

		// oc_product query
		$sql = "SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int) $product_id . "'";
		$op_query = $this->db->query($sql);
		if ($op_query->num_rows == 0) {
			return false;
		}

		// oc_product_description query
		$language_id = (int) $this->config->get('config_language_id'); // Current Language
		$sql = "SELECT * FROM " . DB_PREFIX . "product_description
                WHERE product_id = '" . (int) $product_id . "'
                  AND language_id = '" . $language_id . "'";
		$opdesc_query = $this->db->query($sql);

		if ($opdesc_query->num_rows == 0 and $language_id != 1) {
			$opdesc_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description
                                              WHERE product_id = '" . (int) $product_id . "' AND language_id = '1'");
			if ($opdesc_query->num_rows == 0) {
				return false;
			}

		}

		// oc_ms_seller query
		$sql = "SELECT seller_id, cod_available, vacation_mode, seller_status, non_returnable, city
                FROM " . DB_PREFIX . "ms_seller
                WHERE seller_id = '" . (int) $omp_query->row['seller_id'] . "'";
		$oms_query = $this->db->query($sql);
		if ($oms_query->num_rows == 0) {
			return false;
		}

		// oc_product_special query
		$sql = "SELECT price AS special, date_start, date_end FROM " . DB_PREFIX . "product_special
                WHERE product_id = '" . (int) $product_id . "'
                  AND ((date_start = '0000-00-00' OR date_start < NOW())
                  AND (date_end = '0000-00-00' OR date_end > NOW()))
                ORDER BY priority ASC, price ASC LIMIT 1";

		$opspl_query = $this->db->query($sql);

		// oc_stock_status query
		/*$sql = "SELECT name AS product_stock_status FROM " . DB_PREFIX . "stock_status
	                WHERE stock_status_id = '" . (int)$op_query->row['stock_status_id'] . "'
	                AND language_id = '" . $language_id . "'";
*/

		// category query
		$sql = "SELECT GROUP_CONCAT(category_id SEPARATOR ',') AS category_id FROM " . DB_PREFIX . "product_to_category
                WHERE product_id = '" . (int) $product_id . "' GROUP BY product_id";
		$cat_query = $this->db->query($sql);

		// sor product query
		$sql = "SELECT sor_days FROM " . DB_PREFIX . "product_sor_terms
                WHERE product_id = '" . (int) $product_id . "'";
		$sor_query = $this->db->query($sql);
		if (!empty($sor_query->row['sor_days'])) {
			$is_sor_enabled = 1;

			if ($op_query->row['piece_in_set'] > 1) {
				$sor_enabled_text = 'Buyback Guarantee within ' . $sor_query->row['sor_days'] . ' Days (Setwise)';
				$sor_enabled_detail_text = 'Buyback Guarantee within ' . $sor_query->row['sor_days'] . ' Days (full set only, not partial)';
			} else {
				$sor_enabled_text = 'Buyback Guarantee within ' . $sor_query->row['sor_days'] . ' Days';
				$sor_enabled_detail_text = 'Buyback Guarantee within ' . $sor_query->row['sor_days'] . ' Days';
			}

		} else {
			$is_sor_enabled = 0;
			$sor_enabled_text = '';
			$sor_enabled_detail_text = '';
		}

		// oc_review query - Currently disabled
		/* $sql =  "SELECT COUNT(*) AS reviews FROM " . DB_PREFIX . "review
	                 WHERE product_id = '" . (int)$product_id . "'
	                   AND status = '1'
	                   AND store_id IN (".WSB_STORES_ID.")
	                 GROUP BY product_id";
*/

		/* Added by Amarat (28-august-2017) */
		$p_base_unit = '';
		$p_super_unit = '';

		$units = $this->getProductUnit($product_id);
		if (count($units) > 0) {
			$p_base_unit = !empty($units['base_unit']) ? $units['base_unit'] : '';
			$p_super_unit = !empty($units['super_unit']) ? $units['super_unit'] : '';
			$unit_id = $units['unit_id'];
		}
		/* End by Amarat (28-august-2017) */

		// Checking For SOR / Other store price markups and commission changes
		$price = $op_query->row['price'];

		$commission = $op_query->row['commission'];
		$selling_price = $op_query->row['selling_price'];

		/*-- diff between two date for get exp_dispatch_date and show on particular product */

		$exp_final_date = 0;
		if ((bool) (strtotime($op_query->row['expected_dispatch_date']))) {
			if (isset($op_query->row['expected_dispatch_date']) && $op_query->row['expected_dispatch_date'] != '0000-00-00') {
				$exp_dis_date = strtotime($op_query->row['expected_dispatch_date']);
				$today_date = strtotime(date('Y-m-d'));
				$date_diff = $exp_dis_date - $today_date;
				$exp_final_date = (int) ($date_diff / (60 * 60 * 24));
			}
		} else {
			$exp_final_date = (int) $op_query->row['expected_dispatch_date'];
		}

		if ($exp_final_date > '1' && !empty($exp_final_date)) {
			$exp_dispatch_date = "Available after " . $exp_final_date . " Days";
		} else {
			$exp_dispatch_date = '';
		}

		/*-- saving price for customer*/
		$saving_money = 0;
		if (isset($op_query->row['mrp']) && $op_query->row['mrp'] > 0) {
			$saving_money = $this->currency->format(($op_query->row['mrp'] - $op_query->row['selling_price']));
		}

		/*---------------------------------*/
		/*-- margin_percentage  for customer*/
		$margin_percentage = 0;
		$unit_special_price = array();
		if (isset($opspl_query->row['special']) && $opspl_query->row['special'] > 0) {
			$special_price_result = array(
				'price' => $price,
				'special' => $opspl_query->row['special'],
				'piece_in_set' => $op_query->row['piece_in_set'],
				'seller_tax' => $op_query->row['seller_tax'],
				'commission' => $op_query->row['commission'],
				'tax_class_id' => $op_query->row['tax_class_id'],
				'image' => $op_query->row['image'],
				'mrp' => $op_query->row['mrp'],
			);
			$unit_special_price = $this->getPrice($special_price_result);
		}

		if (isset($op_query->row['mrp']) && $op_query->row['mrp'] > 0) {
			$margin_percentage = (ceil((($op_query->row['mrp'] - $op_query->row['selling_price']) * 100) / $op_query->row['mrp']));
			if (isset($opspl_query->row['special']) && $opspl_query->row['special'] > 0) {
				$margin_percentage = (ceil((($op_query->row['mrp'] - $unit_special_price['unformatted_special']) * 100) / $op_query->row['mrp']));
			}
		}

		//===product previously order Start=====//
		if (isset($this->customer) && $this->customer->isLogged()) {
			$product_previously_order = $this->getProductPreviouslyOrder((int) $op_query->row['product_id'], (int) $this->customer->getId());
		} else {
			$product_previously_order = 0;
		}

		//===product previously order End=======//

		$discount_percentage = 0;
		if (isset($opspl_query->row['special']) && $opspl_query->row['special'] > 0) {
			$discount_percentage = (ceil((($op_query->row['price'] - $opspl_query->row['special']) * 100) / $op_query->row['price']));
		}
		$data = array('product_id' => $op_query->row['product_id']);
		$price_details = Cart::getPrice($data, $this->registry);

		$margin_percentage = $price_details['margin_percentage'];
		$discount_percentage = $price_details['discount_percentage'];

		$op_query->row['minimum'] = ($op_query->row['quantity'] > $op_query->row['minimum']) ? $op_query->row['minimum'] : $op_query->row['quantity'];

		$stock_status = "In Stock"; //$oss_query->num_rows ? $oss_query->row['product_stock_status'] : '';

		$op_query->row['tax_class_id'] = $price_details['tax_class_id'];
		$op_query->row['seller_status'] = $oms_query->row['seller_status'];
		$op_query->row['vacation_mode'] = $oms_query->row['vacation_mode'];

		$sql = "SELECT count(p.product_id) as total_associates FROM " . DB_PREFIX . "product p JOIN " . DB_PREFIX . "product_to_associate ps ON p.product_id=ps.associate_product_id  WHERE ps.product_id = '" . (int) $op_query->row['product_id'] . "'";
		$associate_product_query = $this->db->query($sql);

		//Setting variable is_combo to false
		$is_combo = 0;
		// normal product
		if ($associate_product_query->row['total_associates'] == 0) {
			$stock_status_info = Cart::getProductStockStatus($op_query->row);
			if (isset($stock_status_info['stock'])
				&& $stock_status_info['stock'] === false) {
				$stock_status = 'out of stock';
			}
		}
		// combo product with only one associate product, so mark it as out of stock
		else if ($associate_product_query->row['total_associates'] == 1) {
			$stock_status = 'out of stock';
		}
		// combo product with multiple associate products
		else {
			$is_combo = 1;
			$t_stock = Cart::getComboProductStockStatus($op_query->row['product_id'], $this->db);
			if ($t_stock == false) {
				$stock_status = 'out of stock';
			}
		}

		$store_id = $this->config->get('config_store_id');
		if ($store_id == INTERNATIONAL_STORE_ID) {
			$format_mrp = $this->currency->format($op_query->row['mrp'], '', '', true, '', '');
		} else {
			$format_mrp = $this->currency->format($op_query->row['mrp'], '', '', true, '', 'frontend');
		}

		//Added by NILESH
		// check for non-returnable
		// if any of seller or category or product is marked as non-returnable, then will treat the product as non-returnable.
		$non_returnable = ($oms_query->row['non_returnable'] |
			Cart::getCategoryLevelNonReturnable($this->db, $product_id) |
			$op_query->row['non_returnable']);

		return array(
			'product_id' => $op_query->row['product_id'],
			'name' => html_entity_decode($opdesc_query->row['name']),
			'category_id' => (isset($cat_query->row['category_id'])) ? $cat_query->row['category_id'] : "", //can be multiple categories string comma separated
			'set_description' => html_entity_decode($opdesc_query->row['set_description']),
			'description' => html_entity_decode($opdesc_query->row['description']),
			'is_sor_enabled' => $is_sor_enabled,
			'sor_enabled_text' => $sor_enabled_text,
			'sor_enabled_detail_text' => $sor_enabled_detail_text,
			'meta_title' => $opdesc_query->row['meta_title'],
			'meta_description' => $opdesc_query->row['meta_description'],
			'meta_keyword' => $opdesc_query->row['meta_keyword'],
			'tag' => $opdesc_query->row['tag'],
			'model' => $op_query->row['model'],
			'hsn_code' => $op_query->row['hsn_code'],
			'sku' => $op_query->row['sku'],
			'location' => $op_query->row['location'],
			'quantity' => $op_query->row['quantity'],
			'stock_status' => $stock_status,
			'stock_status_id' => $op_query->row['stock_status_id'],
			'image' => $op_query->row['image'],
			'image_dimensions' => $op_query->row['image_dimensions'],
			'seller_id' => $omp_query->row['seller_id'],
			'seller_nickname' => $omp_query->row['nickname'],
			'pickup_city' => $oms_query->row['city'],
			'manufacturer_id' => 0, // Currently no such use of manufacturers
			'manufacturer' => '', // Currently no such use of manufacturers
			'price' => $price,
			'saving_money' => $this->currency->format($price_details['saving_money']),
			'margin_percentage ' => $margin_percentage, //It needs to be removed, a space added in key accidentely and used in older versin of APP
			'margin_percentage' => $margin_percentage,
			'discount_percentage' => $discount_percentage,
			'margin_percentage_text' => sprintf($this->language->get('text_margin'), $margin_percentage . '%'),
			'discount_percentage_text' => sprintf($this->language->get('text_discount'), $discount_percentage . '%'),
			'mrp' => (float) $op_query->row['mrp'],
			'format_mrp' => $format_mrp,
			'selling_price' => ($price_details['original_selling_price'] != $price_details['selling_price']) ? $price_details['original_selling_price'] : $price_details['selling_price'],
			'piece_in_set' => $op_query->row['piece_in_set'],
			'seller_tax' => $price_details['seller_tax'],
			'commission' => $commission,
			'special' => ($price_details['original_selling_price'] != $price_details['selling_price']) ? $price_details['selling_price'] : '',
			'special_text' => ($price_details['original_selling_price'] != $price_details['selling_price']) ? $this->currency->format($price_details['selling_price']) : '',
			'points' => $op_query->row['points'],
			'tax_class_id' => $price_details['tax_class_id'],
			'exp_dispatch_days' => $exp_final_date,
			'date_available' => $op_query->row['date_available'],
			'weight' => $op_query->row['weight'],
			'weight_class_id' => $op_query->row['weight_class_id'],
			'length' => $op_query->row['length'],
			'width' => $op_query->row['width'],
			'height' => $op_query->row['height'],
			'length_class_id' => $op_query->row['length_class_id'],
			'subtract' => $op_query->row['subtract'],
			//'rating'           => $this->getProductRating( $op_query->row['product_id'] ),
			'rating' => $op_query->row['rating'],
			'reviews' => 0, // Currently Disabled : $oprev_query->num_rows ? $oprev_query->row['reviews'] : 0,
			'vacation_mode' => $oms_query->row['vacation_mode'],
			'minimum' => $op_query->row['minimum'],
			'sort_order' => $op_query->row['sort_order'],
			'status' => $op_query->row['status'],
			'date_added' => $op_query->row['date_added'],
			'date_modified' => $op_query->row['date_modified'],
			'viewed' => $op_query->row['viewed'],
			'sold_out' => $op_query->row['sold_out'],
			'is_single' => $op_query->row['is_single'],
			'cod_available' => ((int) $op_query->row['cod_available'] & (int) $oms_query->row['cod_available']),
			'seller_id' => $oms_query->row['seller_id'],
			'shipping' => $op_query->row['shipping'],
			'exp_dispatch_date' => $exp_dispatch_date,
			'show_large_image_in_zoom' => $op_query->row['show_large_image_in_zoom'],
			'exclusive' => $op_query->row['exclusive'],
			'tax_per_piece' => $price_details['tax_per_piece'],
			'tax_rate' => $price_details['output_tax_rates'],
			'super_unit' => $p_super_unit,
			'base_unit' => $p_base_unit,
			'franchise_id' => $op_query->row['franchise_id'],
			'credit_price' => '',
			'store_sales' => $op_query->row['store_sales'],
			'unit_id' => $unit_id,
			'sor_product' => $op_query->row['sor_product'],
			'wsb_purchase_id' => $op_query->row['wsb_purchase_id'],
			'non_returnable' => $non_returnable,
			'url' => $this->url->link('product/product', '&product_id=' . $op_query->row['product_id'], 'SSL'),
			'product_previously_order' => $product_previously_order,
			'is_combo' => $is_combo,
			'hidden_selling_price' => $op_query->row['hidden_selling_price'],
		);
	}

	// Currently re-factored to suit Search Controller only - Madhur
	public function getProducts($data = array()) {
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));

		$sql = "SELECT
                       SQL_CALC_FOUND_ROWS
                       p.product_id,p.sor_product,
                       p.sold_out, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating,
                       (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";

			/***
				             * Get Products According to Seller Store
			*/
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id)";
			}

		} else {
			/***
				             * Get Products According to Seller Store
			*/
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$sql .= " FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product p";
			}
		}

		if (!empty($data['filter_special'])) {
			$sql .= " INNER JOIN " . DB_PREFIX . "product_special psp ON (p.product_id = psp.product_id)";
		}

		$sql .= " INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                  INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                  WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'
                        AND p.status = '1'
                        AND p.date_available <= NOW()
                        AND p2s.store_id = " . (int) $this->config->getStoreIdForSql();

		if (!empty($data['product_id_array'])) {
			$sql .= " AND p.product_id IN (" . $data['product_id_array'] . ") ";
		}

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int) $data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int) $data['filter_category_id'] . "'";
			}
		}
		if (!empty($data['custom_store']) && $data['custom_store'] == 'single') {
			$sql .= " AND (p.is_single = 1 OR (p.piece_in_set = 1 AND p.minimum = 1)) ";
		} else {
			$sql .= " AND p.is_single = 0";
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {

			$sql .= " AND ( "; //wrap search And conditions
			if (!empty($data['filter_name'])) {
				$implode = array();

				$keywords = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));
				$words = array();
				foreach ($keywords as $keyword) {
					$words = array_merge($words, $this->getDictionarySynonyms($keyword));
				}

				$i = 0;
				foreach ($words as $word) {
					if ($i > 0) {
						$sql .= " AND ";
					}
					$i++;
					$sql .= "  pd.name LIKE '%" . $this->db->escape(trim($word)) . "%'
                             OR pd.description LIKE '%" . $this->db->escape(trim($word)) . "%'";
				}
			}

			if (empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= "  pd.tag LIKE '" . $this->db->escape(trim($data['filter_tag'])) . "'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower(trim($data['filter_name']))) . "%'";
			}

			$sql .= " ) "; //wrap search And conditions
		}

		/***
			         * Get Products According to Seller Store
		*/
		if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
			$sql .= " AND mp.seller_id IN(" . $store_info['config_seller_id'] . ")";
		}

		$sql .= " GROUP BY p.product_id";

		if (!empty($data['rating_filter'])) {
			$sql .= ' HAVING rating >= ' . (float) $data['rating_filter'];
		}

		$sort_data = array(
			'p.quantity',
			'p.selling_price',
			'p.date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);

		//To get total number of rows for pagination
		$count_sql = "SELECT FOUND_ROWS() AS overall_count";
		$count_query = $this->db->query($count_sql);

		if (!empty($data['filter_name'])) {
			$has_results = $query->num_rows;
			if ($has_results > 0) {
				$has_results = 1;
			} else {
				$has_results = 0;
			}
			$this->load->model('catalog/product');
			$this->model_catalog_product->setSearchedKeyword($data['filter_name'], $has_results);
		}
		foreach ($query->rows as $result) {

			if (!empty($this->getProduct($result['product_id']))) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
		}

		$all_data = array();

		$all_data['products'] = $product_data;
		$all_data['total_count'] = (int) $count_query->row['overall_count'];

		return $product_data;
	}

	public function getTotalProducts($data = array()) {

		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));

		$sql = "SELECT p.product_id,
                       (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating ";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";

			/***
				             * Get Products According to Seller Store
			*/
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id)";
			}

		} else {
			/***
				             * Get Products According to Seller Store
			*/
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$sql .= " FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product p";
			}
		}
		if (!empty($data['filter_special'])) {
			$sql .= " INNER JOIN " . DB_PREFIX . "product_special psp ON (p.product_id = psp.product_id)";
		}

		$sql .= " INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                  INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                  WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'
                        AND p.status = '1'
                        AND p.date_available <= NOW()
                        AND p2s.store_id = " . (int) $this->config->getStoreIdForSql();

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int) $data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int) $data['filter_category_id'] . "'";
			}
		}
		if (!empty($data['custom_store']) && $data['custom_store'] == 'single') {
			$sql .= " AND (p.is_single = 1 OR (p.piece_in_set = 1 AND p.minimum = 1)) ";
		} else {
			$sql .= " AND p.is_single = 0";
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {

			$sql .= " AND ( "; //wrap search And conditions
			if (!empty($data['filter_name'])) {
				$implode = array();

				$keywords = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));
				$words = array();
				foreach ($keywords as $keyword) {
					$words = array_merge($words, $this->getDictionarySynonyms($keyword));
				}

				$i = 0;
				foreach ($words as $word) {
					if ($i > 0) {
						$sql .= " AND ";
					}
					$i++;
					$sql .= "  pd.name LIKE '%" . $this->db->escape(trim($word)) . "%'
                             OR pd.description LIKE '%" . $this->db->escape(trim($word)) . "%'";
				}
			}

			if (empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= "  pd.tag LIKE '" . $this->db->escape(trim($data['filter_tag'])) . "'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower(trim($data['filter_name']))) . "%'";
			}

			$sql .= " ) "; //wrap search And conditions
		}

		/***
			         * Get Products According to Seller Store
		*/
		if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
			$sql .= " AND mp.seller_id IN(" . $store_info['config_seller_id'] . ")";
		}

		$sql .= " GROUP BY p.product_id";

		$query = $this->db->query($sql);

		return (int) ($query->num_rows);
	}

	public function getInStockProducts($data = array()) {
		if ($this->currency->getCode() == 'INR') {
			$price_values = $this->currency->currencies['INR']['value'];
		} else {
			$price_values = $this->currency->currencies['USD']['value'];
		}
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));

		$sql = "SELECT p.product_id,
                       (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating,
                       (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
			if (!empty($data['option'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (p.product_id = pov.product_id) ";
			}

			/***
				             * Get Products According to seller (Quality Expectations)
			*/
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id)";
			} else {
				if (isset($data['seller']) && !empty($data['seller'])) {
					$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id)";
				}
			}

		} else {

			/***
				             * Get Products According to seller (Quality Expectations)
			*/
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$sql .= " FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id";
			} else {
				if (isset($data['seller']) && !empty($data['seller'])) {
					$sql .= " FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id";
				} else {
					$sql .= " FROM " . DB_PREFIX . "product p";
				}
			}
		}

		if (!empty($data['is_trending'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_hotness ph ON (p.product_id = ph.product_id) ";
		}

		$out_of_stock_id = 0;
		$out_of_stock_query = $this->db->query("SELECT ss.stock_status_id FROM " . DB_PREFIX . "stock_status ss WHERE UPPER(ss.name) = 'OUT OF STOCK' AND ss.language_id = '" . (int) $this->config->get('config_language_id') . "'");
		if ($out_of_stock_query->num_rows) {
			$out_of_stock_id = (int) ($out_of_stock_query->row['stock_status_id']);
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                  WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'
                  AND p.status = '1' AND p.quantity > 0 AND p.stock_status_id != '" . $out_of_stock_id . "'
                  AND p.date_available <= NOW()
                  AND p2s.store_id = " . (int) $this->config->getStoreIdForSql();

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int) $data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int) $data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = array_unique(explode(',', $data['filter_filter']));
				$filter_sql = '';
				foreach ($filters as $filter_id) {
					$implode[] = (int) $filter_id;
				}

				$sql_check = "SELECT DISTINCT (filter_group_id) FROM  " . DB_PREFIX . "filter WHERE filter_id IN (" . implode(',', $implode) . ") ";
				$query_check = $this->db->query($sql_check);
				$filter_groups = array();
				foreach ($query_check->rows as $result) {
					$filter_groups[$result['filter_group_id']] = array();
				}

				if (count($filter_groups) > 1) {
					$validimi = true;
				} else {
					$validimi = false;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
			if (!empty($data['option'])) {
				$implode = array();

				$options = array_unique(explode(',', $data['option']));
				$option_sql = '';

				foreach ($options as $option_value_id) {
					$implode[] = (int) $option_value_id;
				}
				$sql .= " AND pov.option_value_id IN (" . implode(',', $implode) . ") AND pov.quantity > 0";
			}

			// Add This for selling price on 24-12-2015 (Ravindra Singh)
			if (!empty($data['price_filter'])) {
				$price_min = '';
				$price_max = '';

				if (isset($data['price_filter']) && ($data['price_filter'] != '')) {
					$prices = explode('-', $data['price_filter']);
					$price_min_val = $prices[0];
					if ($prices[0] == '1') {
						$price_min_val = '0.1';
					}
					$price_min = $price_min_val;
					$price_max = $prices[1];
				}

				/****--- start updated on (05-01-2016) by vikas ----****/
				if ($price_max == 0) {
					$sql .= " AND p.selling_price >= " . ($price_min / $price_values);
				} else {
					$sql .= " AND p.selling_price >= " . ($price_min / $price_values) . "  AND p.selling_price<= " . ($price_max / $price_values);
				}
				/*****--- end ----*******/
			}
		}
		if (!empty($data['custom_store']) && $data['custom_store'] == 'single') {

			$sql .= " AND (p.is_single = 1 OR (p.piece_in_set = 1 AND p.minimum = 1))";
		} else {
			$sql .= " AND p.is_single = 0";

		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int) $data['filter_manufacturer_id'] . "'";
		}
		/***
			         * Get Products According to seller (Quality Expectations)
		*/
		if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
			$sql .= " AND mp.seller_id IN(" . $store_info['config_seller_id'] . ")";
		} else {
			if (isset($data['seller']) && !empty($data['seller'])) {
				$sql .= " AND mp.seller_id IN(" . $data['seller'] . ")";
			}
		}

		if (!empty($data['days'])) {
			$sql .= " AND DATEDIFF(NOW(),p.date_added) > 30";
		}

		If (isset($validimi)) {
			$sql .= " GROUP BY p.product_id HAVING COUNT( DISTINCT pf.filter_id)=" . count($filter_groups);
		} else {

			$sql .= " GROUP BY p.product_id";

		}

		if (!empty($data['rating_filter'])) {
			if (isset($validimi)) {
				$sql .= " AND rating >=" . (float) $data['rating_filter'];
			} else {
				$sql .= " HAVING rating >=" . (float) $data['rating_filter'];
			}

		}

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'p.selling_price',
			'rating',
			'p.sort_order',
			'p.date_added',
			'p.viewed',
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special ELSE p.price END)";
			} elseif ($data['sort'] == 'p.selling_price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special ELSE p.selling_price END)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} elseif (!empty($data['is_trending'])) {
			$sql .= " DESC, ph.hotness_points DESC";
		} else {
			//$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$product_data = array();
		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			if (!empty($this->getProduct($result['product_id']))) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
		}

		return $product_data;
	}

	public function getProductSpecials($data = array()) {
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->getStoreIdForSql() . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getLatestProducts($limit) {

		$product_data = $this->cache->get('product.latest.' . (int) $this->config->get('config_language_id') . '.' . (int) $this->config->getStoreIdForSql() . '.' . (int) $limit);

		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->getStoreIdForSql() . "' ORDER BY p.date_added DESC LIMIT " . (int) $limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.latest.' . (int) $this->config->get('config_language_id') . '.' . (int) $this->config->getStoreIdForSql() . '.' . (int) $limit, $product_data);
		}

		return $product_data;
	}

	public function getPopularProducts($limit) {
		$product_data = array();
		$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->getStoreIdForSql() . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int) $limit);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getBestSellerProducts($limit, $seller_id) {

		if (!empty($seller_id)) {
			$seller_id = implode(",", $seller_id);
		} else {
			$seller_id = '';
		}

		$product_data = $this->cache->get('product.bestseller.' . (int) $this->config->get('config_language_id') . '.' . (int) $this->config->getStoreIdForSql() . '.' . (int) $limit);

		if (!$product_data) {
			//echo "123456"; die;
			$product_data = array();
			if (!empty($seller_id)) {
				// echo $seller_id; die;
				$q = "SELECT op.product_id, SUM(op.quantity) AS total
                    FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)
                    LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id)
                    LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON (p2c.product_id = p.product_id)
                    LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                    WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW()
                    AND op.seller_id IN(" . $seller_id . ")
                    AND p2c.category_id NOT IN(66,67)
                    AND p2s.store_id = " . (int) $this->config->getStoreIdForSql() . "
                    GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int) $limit;
				// echo $q; die;

				$query = $this->db->query($q);
			} else {

				$q = "SELECT op.product_id, SUM(op.quantity) AS total
                    FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)
                    LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id)
                    LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON (p2c.product_id = p.product_id)
                    LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                    WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW()
                    AND p2c.category_id NOT IN(66,67)
                    AND p2s.store_id = " . (int) $this->config->getStoreIdForSql() . "
                    GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int) $limit;
				// echo $q; die;
				$query = $this->db->query($q);
			}

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.bestseller.' . (int) $this->config->get('config_language_id') . '.' . (int) $this->config->getStoreIdForSql() . '.' . $this->config->getStoreIdForSql() . '.' . (int) $limit, $product_data);
		}

		return $product_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_group_data = array();

		$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int) $product_id . "' AND agd.language_id = '" . (int) $this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();

			$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int) $product_id . "' AND a.attribute_group_id = '" . (int) $product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int) $this->config->get('config_language_id') . "' AND pa.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name' => $product_attribute['name'],
					'text' => $product_attribute['text'],
				);
			}

			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name' => $product_attribute_group['name'],
				'attribute' => $product_attribute_data,
			);
		}

		return $product_attribute_group_data;
	}

	public function getProductOptions($product_id) {
		// Initializing return variable
		$product_option_data = array();

		$po_sql = "SELECT po.product_option_id,
                          po.option_id,
                          po.value,
                          po.required,
                          o.type,
                          o.sort_order,
                          od.name
                   FROM " . DB_PREFIX . "product_option po
                   INNER JOIN " . DB_PREFIX . "option o ON po.option_id = o.option_id
                   INNER JOIN " . DB_PREFIX . "option_description od
                           ON o.option_id = od.option_id
                              AND od.language_id = " . (int) $this->config->get('config_language_id') . "
                   WHERE po.product_id = " . (int) $product_id;

		$po_query = $this->db->query($po_sql);
		$po_data = $po_query->rows;
		// Sort the data by sort_order in Ascending order
		array_multisort(array_column($po_data, 'sort_order'), SORT_ASC, SORT_NUMERIC, $po_data);

		foreach ($po_data as $po) {

			$pov_sql = "SELECT pov.product_option_value_id,
                               pov.option_value_id,
                               pov.quantity,
                               pov.subtract,
                               pov.price,
                               pov.price_prefix,
                               pov.weight,
                               pov.weight_prefix,
                               pov.option_code,
                               pov.image,
                               pov.option_image,
                               ov.sort_order,
                               ovd.name,
                               ovd.extra_info
                        FROM " . DB_PREFIX . "product_option_value pov
                        INNER JOIN " . DB_PREFIX . "option_value ov ON pov.option_value_id = ov.option_value_id
                        INNER JOIN " . DB_PREFIX . "option_value_description ovd
                                ON ov.option_value_id = ovd.option_value_id
                                   AND ovd.language_id = " . (int) $this->config->get('config_language_id') . "
                        WHERE pov.product_id = " . (int) $product_id . "
                              AND pov.product_option_id = " . (int) $po['product_option_id'];
			$pov_query = $this->db->query($pov_sql);
			$pov_data = $pov_query->rows;

			// Sort by sort_order in ascending order. If option name is Color, then first sort by quantity in descending order
			$sort_order = array_column($pov_data, 'sort_order');
			$quantity = array_column($pov_data, 'quantity');
			array_multisort($sort_order, SORT_ASC, SORT_NUMERIC, $quantity, SORT_DESC, SORT_NUMERIC, $pov_data);

			$po['product_option_value'] = $pov_data;
			$product_option_data[] = $po;
		}

		return $product_option_data;
	}

	// This function will return only data from product_option table, not the actual product options
	public function getProductOptionInfo($product_id) {
		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option  WHERE product_id = '" . (int) $product_id . "'");
		if ($product_option_query->num_rows > 0) {
			return $product_option_query->row;
		} else {
			return array();
		}

	}

	public function getProductDiscounts($product_id, $store_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' AND quantity > 1 AND store_id = '" . (int) $store_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $query->rows;
	}

	public function getProductImages($product_id) {

		$sql = "SELECT * FROM " . DB_PREFIX . "product_image
                WHERE product_id = '" . (int) $product_id . "'
                      AND default_image = '0'";
		$query = $this->db->query($sql);
		$data = $query->rows;
		array_multisort(array_column($data, 'sort_order'), SORT_ASC, SORT_NUMERIC, $data);

		return $data;
	}

	public function getProductOriginalImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int) $product_id . "'");
		return $query->row;
	}

	public function getProductRelated($product_id) {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int) $product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->getStoreIdForSql() . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->getProduct($result['related_id']);
		}

		return $product_data;
	}

	public function getProductLayoutId($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int) $product_id . "' AND store_id = '" . (int) $this->config->getStoreIdForSql() . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int) $product_id . "'");

		return $query->rows;
	}

	public function getCategory($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int) $product_id . "'");
		if (isset($query->row['category_id'])) {
			return $query->row['category_id'];
		} else {
			return 0;
		}
	}

	public function getTotalInStockProducts($data = array()) {

		if ($this->currency->getCode() == 'INR') {
			$price_values = $this->currency->currencies['INR']['value'];
		} else {
			$price_values = $this->currency->currencies['USD']['value'];
		}
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));

		$sql = "SELECT p.product_id,
                (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
			if (!empty($data['option'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (p.product_id = pov.product_id) ";
			}

			/***
				             * Get Products According to seller (Quality Expectations)
			*/
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id)";
			} else {
				if (isset($data['seller']) && !empty($data['seller'])) {
					$sql .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id)";
				}
			}

		} else {

			/***
				             * Get Products According to seller (Quality Expectations)
			*/
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$sql .= " FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id";
			} else {
				if (isset($data['seller']) && !empty($data['seller'])) {
					$sql .= " FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "ms_product mp ON mp.product_id = p.product_id";
				} else {
					$sql .= " FROM " . DB_PREFIX . "product p";
				}
			}
		}

		$out_of_stock_id = 0;
		$out_of_stock_query = $this->db->query("SELECT ss.stock_status_id FROM " . DB_PREFIX . "stock_status ss WHERE UPPER(ss.name) = 'OUT OF STOCK' AND ss.language_id = '" . (int) $this->config->get('config_language_id') . "'");
		if ($out_of_stock_query->num_rows) {
			$out_of_stock_id = (int) ($out_of_stock_query->row['stock_status_id']);
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                  WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'
                  AND p.status = '1' AND p.quantity > 0 AND p.stock_status_id != '" . $out_of_stock_id . "'
                  AND p.date_available <= NOW()
                  AND p2s.store_id = " . (int) $this->config->getStoreIdForSql();

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int) $data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int) $data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = array_unique(explode(',', $data['filter_filter']));
				$filter_sql = '';
				foreach ($filters as $filter_id) {
					$implode[] = (int) $filter_id;
				}

				$sql_check = "SELECT DISTINCT (filter_group_id) FROM  " . DB_PREFIX . "filter WHERE filter_id IN (" . implode(',', $implode) . ") ";
				$query_check = $this->db->query($sql_check);
				$filter_groups = array();
				foreach ($query_check->rows as $result) {
					$filter_groups[$result['filter_group_id']] = array();
				}

				if (count($filter_groups) > 1) {
					$validimi = true;
				} else {
					$validimi = false;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
			if (!empty($data['option'])) {
				$implode = array();

				$options = array_unique(explode(',', $data['option']));
				$option_sql = '';

				foreach ($options as $option_value_id) {
					$implode[] = (int) $option_value_id;
				}
				$sql .= " AND pov.option_value_id IN (" . implode(',', $implode) . ") AND pov.quantity > 0";
			}

			// Add This for selling price on 24-12-2015 (Ravindra Singh)
			if (!empty($data['price_filter'])) {
				$price_min = '';
				$price_max = '';

				if (isset($data['price_filter']) && ($data['price_filter'] != '')) {
					$prices = explode('-', $data['price_filter']);
					$price_min_val = $prices[0];
					if ($prices[0] == '1') {
						$price_min_val = '0.1';
					}
					$price_min = $price_min_val;
					$price_max = $prices[1];
				}

				/****--- start updated on (05-01-2016) by vikas ----****/
				if ($price_max == 0) {
					$sql .= " AND p.selling_price >= " . ($price_min / $price_values);
				} else {
					$sql .= " AND p.selling_price >= " . ($price_min / $price_values) . "  AND p.selling_price<= " . ($price_max / $price_values);
				}
				/*****--- end ----*******/
			}
		}
		if (!empty($data['custom_store']) && $data['custom_store'] == 'single') {

			$sql .= " AND (p.is_single = 1 OR (p.piece_in_set = 1 AND p.minimum = 1))";
		} else {
			$sql .= " AND p.is_single = 0";

		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int) $data['filter_manufacturer_id'] . "'";
		}
		/***
			         * Get Products According to seller (Quality Expectations)
		*/
		if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
			$sql .= " AND mp.seller_id IN(" . $store_info['config_seller_id'] . ")";
		} else {
			if (isset($data['seller']) && !empty($data['seller'])) {
				$sql .= " AND mp.seller_id IN(" . $data['seller'] . ")";
			}
		}

		if (isset($validimi)) {
			$sql .= " GROUP BY p.product_id HAVING COUNT( DISTINCT pf.filter_id)=" . count($filter_groups);
		} else {
			$sql .= " GROUP BY p.product_id";
		}

		if (!empty($data['rating_filter'])) {
			if (isset($validimi)) {
				$sql .= " AND rating >= " . (float) $data['rating_filter'];
			} else {
				$sql .= " HAVING rating >= " . (float) $data['rating_filter'];
			}

		}
		$query = $this->db->query($sql);
		$total = (int) ($query->num_rows);
		return $total;
	}

	public function getProfiles($product_id) {
		return $this->db->query("SELECT `pd`.* FROM `" . DB_PREFIX . "product_recurring` `pp` JOIN `" . DB_PREFIX . "recurring_description` `pd` ON `pd`.`language_id` = " . (int) $this->config->get('config_language_id') . " AND `pd`.`recurring_id` = `pp`.`recurring_id` JOIN `" . DB_PREFIX . "recurring` `p` ON `p`.`recurring_id` = `pd`.`recurring_id` WHERE `product_id` = " . (int) $product_id . " AND `status` = 1 ORDER BY `sort_order` ASC")->rows;
	}

	public function getProfile($product_id, $recurring_id) {
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring` `p` JOIN `" . DB_PREFIX . "product_recurring` `pp` ON `pp`.`recurring_id` = `p`.`recurring_id` AND `pp`.`product_id` = " . (int) $product_id . " WHERE `pp`.`recurring_id` = " . (int) $recurring_id . " AND `status` = 1")->row;
	}

	public function getTotalProductSpecials() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total
                                   FROM " . DB_PREFIX . "product_special ps
                                     LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id)
                                     LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                                   WHERE p.status = '1'
                                     AND p.date_available <= NOW()
                                     AND p2s.store_id = '" . (int) $this->config->getStoreIdForSql() . "'
                                     AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                                     AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function soldout() {

		$sql = "SELECT * FROM " . DB_PREFIX . "product";

		$products = $this->db->query($sql)->rows;

		foreach ($products as $product) {

			if ($product['sold_out'] > 0) {
				$number = (int) $product['sold_out'] + rand(5, 10);
				$query = "UPDATE " . DB_PREFIX . "product
                          SET sold_out = '" . (int) $number . "'
                          WHERE product_id = '" . (int) $product['product_id'] . "'";
				$this->db->query($query);
			} else {
				$number = rand(10, 30);
				$query = "UPDATE " . DB_PREFIX . "product
                          SET sold_out = '" . (int) $number . "'
                          WHERE product_id = '" . (int) $product['product_id'] . "'";
				$this->db->query($query);

			}

		}

		echo 'Successfully updated';

	}

	/**
	 * Added by Rakesh
	 */
	public function getRelatedByCategory($product_id) {
		$product_data = array();

		$category_list = '';
		if ($category_list == '') {
			$query = $this->db->query("SELECT DISTINCT(category_id) FROM `" . DB_PREFIX . "product_to_category`
                                       WHERE `product_id` = '" . (int) $product_id . "'");
			$categories = array();
			foreach ($query->rows as $category) {
				$categories[] = $category['category_id'];
			}
			$category_list = implode(',', $categories);
		}
		if ($category_list != '') {
			$query = $this->db->query("SELECT DISTINCT(p2c.product_id)
                                       FROM `" . DB_PREFIX . "product_to_category` p2c
                                         LEFT JOIN `" . DB_PREFIX . "product` p ON (p2c.product_id = p.product_id)
                                         LEFT JOIN `" . DB_PREFIX . "product_to_store` p2s ON (p.product_id = p2s.product_id)
                                       WHERE p2c.category_id IN ('" . $category_list . "')
                                         AND p2c.product_id != '" . (int) $product_id . "'
                                         AND p.status = '1'
                                         AND p.date_available <= NOW()
                                         AND p2s.store_id = '" . (int) $this->config->getStoreIdForSql() . "'
                                       ORDER BY RAND()");
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
		}
		return $product_data;
	}

	/**
	 * Added by Rakesh
	 */
	public function getRelatedByCategoryId($category_id) {
		$product_data = array();

		if ($category_id > 0) {
			$query = $this->db->query("SELECT DISTINCT(p2c.product_id)
                                       FROM `" . DB_PREFIX . "product_to_category` p2c
                                         LEFT JOIN `" . DB_PREFIX . "product` p ON (p2c.product_id = p.product_id)
                                         LEFT JOIN `" . DB_PREFIX . "product_to_store` p2s ON (p.product_id = p2s.product_id)
                                       WHERE p2c.category_id = '" . (int) $category_id . "'
                                         AND p.status = '1'
                                         AND p.date_available <= NOW()
                                         AND p2s.store_id = '" . (int) $this->config->getStoreIdForSql() . "'
                                       ORDER BY RAND()");
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
		}
		return $product_data;
	}

	/**
	 * Added by Madhur
	 */
	public function getRelatedBySeller($product_id) {
		$product_data = array();

		$query = $this->db->query("SELECT seller_id FROM `" . DB_PREFIX . "ms_product` WHERE `product_id` = '" . (int) $product_id . "' limit  10");
		$seller_id = 0;
		if (isset($query->row['seller_id'])) {
			$seller_id = $query->row['seller_id'];
		} else {
			return false;
		}

		if ($seller_id) {
			$query = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "ms_product`
                                       WHERE seller_id = '" . (int) $seller_id . "'
                                         AND product_id != '" . (int) $product_id . "'
                                       ORDER BY RAND() LIMIT 10");

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
		}
		return $product_data;
	}

	/**
	Added by veer
	 */
	public function getRelatedCategoryProduct($product_id) {

		// Get category list for this product
		$category_list = '';
		if ($category_list == '') {
			$query = $this->db->query("SELECT DISTINCT(category_id) FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int) $product_id . "'");
			$categories = array();
			foreach ($query->rows as $category) {
				$categories[] = $category['category_id'];
			}
			$category_list = implode(',', $categories);
		}
		return $category_list;
	}

	/**
	 * Added by Madhur
	 */
	public function getRelatedBySellerAndCategory($product_id) {
		$product_data = array();

		// Get seller id
		$query = $this->db->query("SELECT seller_id FROM `" . DB_PREFIX . "ms_product` WHERE `product_id` = '" . (int) $product_id . "'");
		$seller_id = 0;
		if (isset($query->row['seller_id'])) {
			$seller_id = (int) $query->row['seller_id'];
		}

		// No seller id ==> Return related to category
		if (!$seller_id) {
			return $this->getRelatedByCategory($product_id);
		}

		// Get category list for this product
		$category_list = '';
		if ($category_list == '') {
			$query = $this->db->query("SELECT DISTINCT(category_id) FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int) $product_id . "'");
			$categories = array();
			foreach ($query->rows as $category) {
				$categories[] = $category['category_id'];
			}
			$category_list = implode(',', $categories);
		}

		if ($category_list != '') {
			$sql_list = "SELECT DISTINCT(p2c.product_id)
                         FROM `" . DB_PREFIX . "product_to_category` p2c
                           LEFT JOIN `" . DB_PREFIX . "product` p ON (p2c.product_id = p.product_id)
                           LEFT JOIN `" . DB_PREFIX . "product_to_store` p2s ON (p.product_id = p2s.product_id)
                           LEFT JOIN `" . DB_PREFIX . "ms_product` msp ON (p2s.product_id = msp.product_id)
                         WHERE p2c.category_id IN ('" . $category_list . "')
                           AND p2c.product_id != '" . (int) $product_id . "'
                           AND p.quantity > 0
                           AND p.status = '1'
                           AND p.date_available <= NOW()
                           AND p2s.store_id = '" . (int) $this->config->getStoreIdForSql() . "'
                           AND msp.seller_id = '" . (int) $seller_id . "'";

			if (!empty($this->session->data['custom_store']) && $this->session->data['custom_store'] == 'single') {
				$sql_list .= " AND p.is_single = '1' ";
			} else {
				$sql_list .= " AND p.is_single = '0' ";

			}
			$sql_list .= " ORDER BY RAND() LIMIT 10";
			$query = $this->db->query($sql_list);
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
		} else {
			// no category list
			return $this->getRelatedBySeller($product_id);
		}

		return $product_data;
	}

	/**
	 * Added by Rakesh
	 */

	public function setQtyDiscount() {
		// Commented out to prevent accidental discount addition.
		// This needs to be fixed and made rule based
		// rather than a bulk discount update on all products - Madhur

		/*
			        $q = "SELECT product_id, price FROM `" . DB_PREFIX . "product`";

			        $query = $this->db->query($q);
			        $tq = "TRUNCATE `" . DB_PREFIX . "product_discount`";
			        $this->db->query($tq);

			        echo("Discount table truncated !");

			        $ignore_products = $this->getIgnoreDiscountChangeProducts();

			        foreach($query->rows as $product){

			            $product_id = (int)($product['product_id']);
			            if (in_array($product_id, $ignore_products))
			                continue;

			            $base_price = $product['price'];

			            $arr_qty_discount[$product_id][1] = ceil(0.99 * $base_price);
			            $arr_qty_discount[$product_id][2] = ceil(0.98 * $base_price);
			            $arr_qty_discount[$product_id][3] = ceil(0.96 * $base_price);

			            $insert1 = "INSERT INTO `" . DB_PREFIX . "product_discount`
			                       SET
			                       product_id = ".$product_id .",
			                       quantity = 3,
			                       priority = 1,
			                       store_id = 0,
			                       price = ".$arr_qty_discount[$product_id][1];

			            $query = $this->db->query($insert1);

			            $insert2 = "INSERT INTO `" . DB_PREFIX . "product_discount`
			                       SET
			                       product_id = ".$product_id .",
			                       quantity = 6,
			                       priority = 1,
			                       store_id = 0,
			                       price = ".$arr_qty_discount[$product_id][2];

			            $query = $this->db->query($insert2);

			            $insert3 = "INSERT INTO `" . DB_PREFIX . "product_discount`
			                       SET
			                       product_id = ".$product_id .",
			                       quantity = 11,
			                       priority = 1,
			                       store_id = 0,
			                       price = ".$arr_qty_discount[$product_id][3];

			            $query = $this->db->query($insert3);
			        }
			        echo "Discounts updated !";
		*/
	}

	/**
	 * This function returns product ids which do no have seo keywords
	 * @return array $resultset
	 */
	public function getProductsWithSeoUrl() {
		$query_products_wi_url = "SELECT `query` FROM `" . DB_PREFIX . "url_alias` WHERE `query` like 'product_id=%'";

		$resultset = $this->db->query($query_products_wi_url);
		return $resultset;
	}
	/**
	 * This function returns product ids and names which have seo keywords
	 * @param array $product_ids
	 * @return array $resultset
	 */
	public function getProductsWithoutSeoUrl($product_ids) {
		$query_products_wo_url = "SELECT " . DB_PREFIX . "product_description.product_id," . DB_PREFIX . "product_description.name," . DB_PREFIX . "product.model  FROM " . DB_PREFIX . "product_description INNER JOIN " . DB_PREFIX . "product ON " . DB_PREFIX . "product.product_id = " . DB_PREFIX . "product_description.product_id  WHERE " . DB_PREFIX . "product.product_id NOT IN (" . implode(",", $product_ids) . ")";
		$resultset = $this->db->query($query_products_wo_url);
		return $resultset;
	}
	/**
	 * This function inserts product ids with seo keywords in url_alias table
	 * @param array $url_array
	 * @return void
	 */
	public function insertIntoProductsUrl($url_array) {
		if (is_array($url_array) && count($url_array) > 0) {
			foreach ($url_array as $product_id => $url) {
				$str_product_id = 'product_id=' . (int) $product_id;
				$insert_query_products_url = "INSERT INTO " . DB_PREFIX . "url_alias (query, keyword) VALUES ('$str_product_id', '$url') ";
				$this->db->query($insert_query_products_url);
			}
		}
	}

	/**
	 * this function fetches product filter data for particular product_id
	 * @param $product_id
	 * @return result set
	 * @author Rakesh Shekhawat
	 */
	public function getProductFiltersData($product_id) {

		$q = "SELECT
                f.filter_id, GROUP_CONCAT(fd.name SEPARATOR ', ') as filter_name,
                (
                    SELECT
                        name
                    FROM
                        " . DB_PREFIX . "filter_group_description fgd
                    WHERE
                        fl.filter_group_id = fgd.filter_group_id
                        AND fgd.language_id = 1
                ) as group_name
              FROM " . DB_PREFIX . "product_filter f
              INNER JOIN " . DB_PREFIX . "filter_description fd
                ON f.filter_id = fd.filter_id
              INNER JOIN " . DB_PREFIX . "filter fl
              ON f.filter_id = fl.filter_id
              WHERE
                f.product_id = '" . (int) $product_id . "'
                AND fd.language_id = '1'
              GROUP BY
                group_name";

		$query = $this->db->query($q);

		return $query->rows;
	}

	/**
	 *
	 */
	public function checkIfProductIsSingle($product_id) {
		if ((int) $product_id > 0) {
			$q = "SELECT count(product_id) as total
              FROM " . DB_PREFIX . "product_to_store pts
              WHERE pts.product_id = '" . (int) $product_id . "'
              AND pts.store_id = '2'";

			$query = $this->db->query($q);

			return $query->row['total'];
		} else {
			return 0;
		}
	}
	/**
	 * getSingleProductIdBySku
	 * Get Id By sku code
	 */
	public function getSingleProductIdBySku($sku) {
		$q = "SELECT p.product_id
              FROM " . DB_PREFIX . "product p
              WHERE p.sku = '" . $this->db->escape($sku) . "'";
		$query = $this->db->query($q);
		if (isset($query->row['product_id'])) {
			return $query->row['product_id'];
		} else {
			return null;
		}
	}

	/**
	 *
	 *
	 */
	public function getTransferPrice($product_id) {
		$price = 0;
		$sql = "SELECT p.* FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int) $product_id . "'";
		$query = $this->db->query($sql);
		if (isset($query->rows)) {
			$price = $query->row['price'];
		}

		return $price;

	}
	/**
	 *
	 *
	 */
	public function getFinalPriceWithoutTax($product_id) {
		$sql = "SELECT p.* FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int) $product_id . "'";
		$query = $this->db->query($sql);
		$final_price = 0;
		if (isset($query->rows)) {
			$seller_tax_factor = 1.0 + ((float) $query->row['seller_tax'] / 100.0);
			$commission_factor = 1.0 + ((float) $query->row['commission'] / 100.0);
			$final_price = ceil($commission_factor * (float) ($query->row['price']) / $seller_tax_factor);

		}

		return $final_price;
	}

	public function getFinalPriceWithTax($product_id) {
		$sql = "SELECT p.* FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int) $product_id . "'";
		$query = $this->db->query($sql);
		$final_price = 0;
		if (isset($query->rows)) {
			$seller_tax_factor = 1.0 + ((float) $query->row['seller_tax'] / 100.0);
			$commission_factor = 1.0 + ((float) $query->row['commission'] / 100.0);
			$final_price = ceil($commission_factor * (float) ($query->row['price']) / $seller_tax_factor);

			$final_price = $this->tax->calculate($final_price, $query->row['tax_class_id'], '', $query->row['mrp']);
		}

		return $final_price;
	}
	/**
	 * get calculated price of a product
	 * @param  product_id
	 * @return calculated price
	 * @author Ravindra Singh
	 */
	public function getCalculatedProductPrice($product_id) {
		$price = $this->getFinalPrice($product_id);

		return $this->currency->format($price, $this->config->get('config_tax'));

	}

	/**
	 * Download Product Images
	 * @param  product_id
	 * @return Images Download
	 * @author Ravindra Singh
	 */
	public function downloadProductImages($product_id) {
		$this->load->model('tool/image');

		$sql = "SELECT image, model FROM " . DB_PREFIX . "product
                WHERE product_id = '" . (int) $product_id . "'";
		$query = $this->db->query($sql);

		$images = array();

		if ($query->num_rows) {
			if (NGINX_ENABLED == 1) {
				$images[] = $this->model_tool_image->getOriginalImage($query->row['image']);
			} else {
				$images[] = DIR_IMAGE . $query->row['image'];
			}
		}

		// Other Images
		foreach ($this->getProductImages($product_id) as $img) {
			if (NGINX_ENABLED == 1) {
				$images[] = $this->model_tool_image->getOriginalImage($img['image']);
			} else {
				if (!empty($img['image'])) {
					$images[] = DIR_IMAGE . $img['image'];
				}
			}
		}

		$zip_file_name = !empty($query->row['model']) ? $query->row['model'] : '';
		if (createAndDownloadZip($images, $zip_file_name, true, true, true) === false) {
			echo 'Sorry! No image(s) found!';
		}
	}

	/*
		     * check returnable flag for seller by vikas (22-01-2016)
		     *
	*/
	public function checkReturnable($product_id) {
		$sql = "SELECT ms.seller_id,ms.non_returnable,p.non_returnable as product_returnable
                FROM " . DB_PREFIX . "ms_seller ms
                  INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.seller_id = ms.seller_id)
                  INNER JOIN " . DB_PREFIX . "product p ON (mp.product_id = p.product_id)
                WHERE mp.product_id = '" . (int) $product_id . "'";

		$query = $this->db->query($sql);
		return $query->row;
	}

	/*
		     * get price for single store by session data['set'] or ['single'] by vikas (22-01-2016)
		     *
	*/
	public function getAlternateProductInfo($is_single, $product_name) {
		if (!$is_single) {
			$sql = "SELECT p.selling_price, p.product_id FROM " . DB_PREFIX . "product p WHERE model LIKE '" . $this->db->escape($product_name) . "-SNGL'
            AND p.quantity > 0
            AND p.status =1";
			$query = $this->db->query($sql);

		} else {
			$sql = "SELECT p.selling_price, p.product_id FROM " . DB_PREFIX . "product p WHERE model LIKE '" . chop($this->db->escape($product_name), '-SNGL') . "'
            AND p.quantity > 0
            AND p.status =1";
			$query = $this->db->query($sql);
		}
		if (isset($query->row['product_id'])) {
			$data = array('product_id' => $query->row['product_id']);
			$price_details = $this->cart->getPrice($data, $this->registry);
			$query->row['selling_price'] = $price_details['selling_price'];
			$query->row['tax_rate'] = $price_details['output_tax_rates'];
		}

		//echo "<pre>"; print_r($data); echo "<br>"; print_r($product_name);echo "</pre>";
		//echo "<pre>"; print_r($sql); echo "<br>"; print_r($query);echo "</pre>";
		return $query->row;

	}
	/**
	 * Get hierarchies of categories from one categgory
	 * @author Parth Gupta
	 * @dateTime 2016-02-09T12:23:37+0530
	 * @param    integer $category_id
	 * @return   array array of category hierarchy
	 */
	public function getCategoryHierarchy($category_id) {

		$cat_arr = array($category_id);
		while ($new_category_id = $this->getParentCategory($category_id)) {
			$cat_arr[] = $new_category_id;
			$category_id = $this->getParentCategory($new_category_id);
		}
		return array_reverse($cat_arr);
	}
	/**
	 * get the Parent category of the input category id
	 * @author Parth Gupta
	 * @dateTime 2016-02-09T12:25:15+0530
	 * @param    integer $category_id
	 * @return   integer parent_category_id
	 */
	public function getParentCategory($category_id) {
		$sql = "SELECT c.parent_id FROM " . DB_PREFIX . "category c WHERE c.category_id=" . $category_id;
		if (isset($this->db->query($sql)->row['parent_id'])) {
			return $this->db->query($sql)->row['parent_id'];
		} else {
			return 0;
		}
	}
	public function getStoreMetaData($product_id, $store_id) {
		$sql = "SELECT * from " . DB_PREFIX . "product_storeinfo WHERE product_id= '" . $product_id . "' AND store_id='" . $store_id . "'";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	/**
	 * Store searched keyword
	 *
	 */
	public function setSearchedKeyword($keyword, $has_results = 0) {

		$customer_id = '';

		if (preg_match('/_/', $keyword) OR preg_match('/-/', $keyword)) {
			$is_model = 1;
		} else {
			$is_model = 0;
		}

		$search_history = array(
			'ip' => $this->request->getIpAddress,
			'user_agent' => $_SERVER['HTTP_USER_AGENT'],
			'date' => date('d-m-Y H:i:s'),
		);

		$sql = "SELECT * FROM " . DB_PREFIX . "searched_terms WHERE keyword = '" . $keyword . "'";
		$serched_data = $this->db->query($sql)->row;
		$fetch_search_history = array();
		if (!empty($serched_data)) {
			if ($this->customer->isLogged()) {
				if (preg_match('/' . $this->customer->getId() . '/', $serched_data['customer_id'])) {
					$customer_id = $serched_data['customer_id'];
				} else {
					$customer_id = $serched_data['customer_id'] . ', ' . (int) $this->customer->getId();
				}
			} else {
				$customer_id = $serched_data['customer_id'];
			}

			$fetch_search_history = unserialize($serched_data['search_history']);
			$fetch_search_history[] = $search_history;

			$this->db->query("UPDATE " . DB_PREFIX . "searched_terms
                                SET customer_id = '" . $this->db->escape($customer_id) . "',
                                    count = (count + 1),
                                    has_results = '" . $has_results . "',
                                    search_history = '" . serialize($fetch_search_history) . "',
                                    modified = '" . date('Y-m-d H:i:s') . "'
                                    WHERE searched_term_id = '" . (int) $serched_data['searched_term_id'] . "'");
		} else {
			$index_search_history[0] = $search_history;
			if ($this->customer->isLogged()) {
				$customer_id = (int) $this->customer->getId();
			} else {

				$customer_id = 0;
			}
			$sql = "INSERT INTO " . DB_PREFIX . "searched_terms SET
                    keyword = '" . $this->db->escape($keyword) . "',
                    customer_id = '" . $customer_id . "',
                    count = 1,
                    has_results = '" . $has_results . "',
                    is_model = '" . $is_model . "',
                    search_history ='" . serialize($index_search_history) . "',
                    created = '" . date('Y-m-d H:i:s') . ",
                    modified = '" . date('Y-m-d H:i:s') . "'";
			$this->db->query($sql);
		}
	}

	/*
		     * Check if a particular product has already been ordered by the customer
		     * We can increase the horizon here. By ordered, it would mean that customer
		     * had shown interest in this product before. Added to cart and even checked out.
		     * SQL tip: If we just want to find out if an id exists. LIMIT 1 is faster than count
	*/
	public function checkPreviouslyOrdered($customer_id, $product_id) {
		$sql = "SELECT op.product_id FROM " . DB_PREFIX . "order_product op
                INNER JOIN " . DB_PREFIX . "order o ON (o.order_id = op.order_id)
                WHERE o.customer_id = '" . (int) $customer_id . "'
                  AND op.product_id = '" . (int) $product_id . "'
                LIMIT 1";
		$query = $this->db->query($sql);
		if ($query->num_rows) {
			return true;
		} else {
			return false;
		}

	}

	public function getPrice($result) {

		$seller_tax_factor = 1.0 + ((float) $result['seller_tax'] / 100.0);
		$commission_factor = 1.0 + ((float) $result['commission'] / 100.0);

		$piece_in_set = (isset($result['piece_in_set']) && (int) $result['piece_in_set'] > 1) ? (int) $result['piece_in_set'] : 1;
		$unit_price = isset($result['price']) ? ceil($commission_factor * (float) ($result['price']) / $seller_tax_factor) : '';

		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
			$unformatted_price = $this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
		} else {
			$price = false;
			$unformatted_price = false;
		}

		if ((float) $result['special']) {
			$unit_special_price = ceil($commission_factor * (float) ($result['special']) / $seller_tax_factor);
			$special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
			$unformatted_special = $this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
		} else {
			$special = false;
			$unformatted_special = false;
		}

		if ($this->config->get('config_tax')) {
			$tax = $this->currency->format((float) $result['special'] ? ceil($commission_factor * (float) ($result['special']) / $seller_tax_factor) : ceil($commission_factor * (float) ($result['price']) / $seller_tax_factor));
		} else {
			$tax = false;
		}

		$product_price = array();
		$product_price[] = array(
			'price' => $price,
			'unformatted_price' => $unformatted_price,
			'special' => $special,
			'unformatted_special' => $unformatted_special,
			'piece_in_set' => $piece_in_set,
			'tax' => $tax,
		);
		return $product_price[0];
	}

	// this function is used for save comment by user when click on "i want this design" instead of
	// out of stock by vikas(02-06-2016)
	public function user_comment($data = array()) {

		$customer_name = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();

		$customer_mobile_no = $this->customer->getTelephone();
		if (empty($customer_mobile_no) && isset($_COOKIE['customer_mobile'])) {$customer_mobile_no = $_COOKIE['customer_mobile'];} else if (empty($customer_mobile_no) && empty($_COOKIE['customer_mobile'])) {$customer_mobile_no = $data['customer_mobile'];}

		$product_name = $this->getProduct($data['product_id']);

		if (empty($data['request_source'])) {
			$data['request_source'] = '';
		}

		$sql = "INSERT INTO " . DB_PREFIX . "wsb_preorder SET " .
		"customer_id = '" . (int) $data['customer_id'] . "'," .
		"customer_name = '" . $this->db->escape($customer_name) . "'," .
		"mobile_no = '" . $customer_mobile_no . "'," .
		"product_id = '" . (int) $data['product_id'] . "'," .
		"product_status = '" . $this->db->escape($data['product_status']) . "'," .
		"comment = '" . $this->db->escape($data['popup_comment']) . "'," .
		"option_name = '" . $this->db->escape($data['option_name']) . "'," .
		"option_value = '" . $this->db->escape($data['option_value']) . "'," .
		"request_source = '" . $this->db->escape($data['request_source']) . "'," .
			"date_added = NOW()";
		$this->db->query($sql);

		$getInforamtion = array(
			'customer_name' => $customer_name,
			'mobile_no' => $customer_mobile_no,
			'product_name' => $product_name['name'],
			'product_model' => $product_name['model'],
			'option_name' => $data['option_name'],
			'option_value' => $data['option_value'],
			'product_status' => $data['product_status'],
			'user_comment' => $data['popup_comment'],
		);
		return $getInforamtion;
	}

	public function CreateFilterCondition($filters) {
		$sql = "SELECT
                filter_group_id, filter_id
              FROM
                " . DB_PREFIX . "filter
              WHERE
                filter_id IN(" . $filters . ")
            ";
		$query = $this->db->query($sql);
		$filter_condition = array();
		foreach ($query->rows as $id) {
			$filter_group_id = $id['filter_group_id'];
			$filter_condition[$filter_group_id][] = 'pf.filter_id=' . $id['filter_id'];
		}

		$implode = array();
		foreach ($filter_condition as $key => $value) {
			$implode[] = "(" . implode(' OR ', $value) . ")";
		}
		$filter_str = implode(' AND ', $implode);
		return $filter_str;
	}

	public function askAQuestion($data = array()) {

		$sql = "INSERT INTO " . DB_PREFIX . "wsb_questions
                SET product_id = " . (int) $data['product_id'] . ",
                    customer_id = " . ((int) ($data['customer_id'] ?? 0) > 0 ? (int) $data['customer_id'] : "NULL") . ",
                    customer_name = '" . $this->db->escape(trim($data['customer_name'])) . "',
                    telephone = '" . $this->db->escape(trim($data['telephone'])) . "',
                    email = '" . $this->db->escape(trim($data['email'])) . "',
                    question = '" . $this->db->escape(trim($data['popup_question'])) . "'";
		$this->db->query($sql);

		$lead_data = [
			'name' => ($data['customer_name']),
			'email' => ($data['email']),
			'status' => 'OPEN',
			'priority' => 1,
			//'is_registered'           => 1 ,
		];
		$this->load->model('lead/lead');
		$this->model_lead_lead->updateLead($lead_data, $data['telephone'], 'product question');
	}

	/**
	 * Function to Get Synonyms of Word from Dictionary (if Any)
	 */
	public function getDictionarySynonyms($word) {
		$sql = "SELECT word, synonyms FROM " . DB_PREFIX . "search_dictionary
                WHERE (FIND_IN_SET('" . $this->db->escape(trim($word)) . "', TRIM(REPLACE(synonyms,' ',''))) <> 0
                       AND FIND_IN_SET('" . $this->db->escape(trim($word)) . "', TRIM(REPLACE(synonyms,' ',''))) IS NOT NULL)
                      OR (word LIKE '" . $this->db->escape(trim($word)) . "')";

		$query = $this->db->query($sql);
		$synonyms = array();

		if ($query->num_rows) {
			foreach ($query->rows as $match) {
				array_push($synonyms, trim($match['word']));
				$synonyms = array_merge($synonyms, explode(',', trim($match['synonyms'])));
			}
		} else {
			array_push($synonyms, trim($word));
		}

		return $synonyms;
	}
	/**
	 * total products for home page
	 * @author Madhur (updated on 7 Aug 2019)
	 * Uses information schema tables to get an approx count, instead of unnecesary exact count and avoid full index/table scan
	 */
	public function getHomePageProductsTotal() {
		$sql = "SELECT table_rows FROM information_schema.tables
                WHERE table_schema = 'wholesalebox'
                  AND table_name = '" . DB_PREFIX . "product'";
		$query = $this->db->query($sql);
		return (int) ($query->row['table_rows'] ?? 0);
	}

	public function getMinimalProductInfo($product_id) {
		$sql = " SELECT p.image, p.commission, p.price, tax_class_id, piece_in_set, seller_tax
                 FROM " . DB_PREFIX . "product p
                 WHERE product_id = " . (int) $product_id;
		$query = $this->db->query($sql);

		if ($query->num_rows) {

			$price = $query->row['price'];
			$commission = $query->row['commission'];
			$tax_class_id = $query->row['tax_class_id'];
			$seller_tax = $query->row['seller_tax'];
			$piece_in_set = $query->row['piece_in_set'];

			// oc_product_special query
			$sql = "SELECT price AS special FROM " . DB_PREFIX . "product_special
                WHERE product_id = '" . (int) $product_id . "'
                  AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW()))
                ORDER BY priority ASC, price ASC LIMIT 1";
			$opspl_query = $this->db->query($sql);

			$special = $opspl_query->num_rows ? $opspl_query->row['special'] : false;

			if ($this->config->get('config_store_id') == SOR_STORE_ID) {
				$custom_price = $this->wsb->getStorePrice($query->row['product_id'], $price, $this->config->get('config_store_id'), $query->row['seller_id']);
				//print_r($custom_price);
				if ($custom_price['price'] > 0) {
					$price = $custom_price['price'];
				}
				if ($custom_price['commission'] > 0) {
					$commission = $custom_price['commission'];
				}

			}

			$result = array(
				'image' => $query->row['image'],
				'price' => $price,
				'commission' => $commission,
				'tax_class_id' => $tax_class_id,
				'seller_tax' => $seller_tax,
				'piece_in_set' => $piece_in_set,
				'special' => $special,
			);

			$price = $this->getPrice($result);

			return $result;
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
		return $this->db->sp_query("CALL syncRatingsToSolr(" . (int) $product_id . ")")->row['rat'];
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
	 *
	 */
	public function getAllFilterGroupsFromDB() {

		$sql = "SELECT fg.filter_group_id, fgd.`name`, fgd.description
                FROM " . DB_PREFIX . "filter_group_description fgd
                INNER JOIN " . DB_PREFIX . "filter_group fg
                ON fg.filter_group_id = fgd.filter_group_id
                WHERE language_id = 1
                ORDER BY sort_order ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * Get Filter Group Data
	 */
	public function getFilterGroupDetail($filter_group_id) {
		if ($filter_group_id > 0) {
			$sql = "SELECT fgd.filter_group_id, fgd.`name`, fgd.description FROM " . DB_PREFIX . "filter_group_description fgd
                    WHERE filter_id = " . $filter_group_id;

			$row = $this->db->query($sql);
			if (isset($row->row['filter_group_id']) && $row->row['filter_group_id'] > 0) {
				$filter_group_data = $row->row;
			} else {
				$filter_group_data = '';
			}
		}

		return $filter_group_data;
	}

	/**
	 * function to add offer to a particular product
	 * */

	public function addOfferToAParticularProduct($data = array()) {

		$delete = "DELETE FROM `" . DB_PREFIX . "product_special` WHERE product_id = '" . $data['product_id'] . "'";
		$this->db->query($delete);

		if (isset($data['offer_type']) && $data['offer_type'] == "percentage") {
			$price = $data['price'] - (($data['price'] * $data['offer']) / 100);
		} else {
			$price = $data['price'] - $data['offer'];
		}
		if ($price > 0 && $data['price'] > $price) {
			$insert = "INSERT INTO `" . DB_PREFIX . "product_special`
                       SET
                       product_id = " . $data['product_id'] . ",
                       priority = 1,
                       date_start = " . "'" . $data['date_from'] . "'" . ",
                       date_end = " . "'" . $data['date_to'] . "'" . ",
                       discount_type = " . "'" . $data['offer_type'] . "'" . ",
                       price = " . $price . ",
                       discount_value = " . $data['offer'];
			$this->db->query($insert);
			$product = array(
				'date_start' => $data['date_from'],
				'date_end' => $data['date_end'],
				'special_price' => $price,
			);

			return true;
		} else {
			return false;
		}
		// Add special price to solr
		//$this->load->model('solr/product');
		//$solr = new SolrProduct($this);
		//$solr->atomicUpdateToSolr($this->config->solrConfig() , $product);
	}

	/**
	 * function to delete offer from product
	 * */

	public function deleteOfferFromProduct($product_id = '') {

		$delete = "DELETE FROM `" . DB_PREFIX . "product_special` WHERE product_id = '" . $product_id . "'";
		$this->db->query($delete);

	}

	/**
	 * function to calculate percent discount from special price
	 * */

	public function calculateSpecialPriceValueInPercent($special_price, $selling_price, $tax_class_id) {
		$discount = $selling_price - $special_price;
		$percent_discount = ($discount * 100) / $selling_price;
		return ceil($percent_discount);
	}

	/**
	 * getProductsAccordingToCustomerPreferences
	 * @info   Here, We Get products Which are Related to customer Order, Wishlist, and customer's own Preference.
	 * @param  int    $customer_id
	 * @param  string $type
	 * @return json   $result
	 * @author Garvit
	 **/
	public function getProductsAccordingToCustomerPreferences($customer_id, $type) {

		$products_details = $this->_getSimilarProductsByCustomerOrderAndWishlist($customer_id);

		if ($type == 'you_may_like') {
			$result = $this->_youMayLike($customer_id, $products_details);
		} elseif ($type == 'what_others_like') {
			$result = $this->_whatOthersLike($customer_id, $products_details);
		}
		return $result;
	}

	/**
	 * _getSimilarProductsByCustomerOrderAndWishlist
	 * @info   Here, We Get Category id, Seller id, Min-Max Price of Products Which are Related to Customer Order, Wishlist.
	 * @param  int   customer_id
	 * @return Array cat_id, seller_id, min_price, max_price
	 * @author Garvit
	 **/
	private function _getSimilarProductsByCustomerOrderAndWishlist($customer_id) {
		$solr = new SolrProduct($this);

		$filter['customer_preference'] = $customer_id;
		//Here, We get Products By order And Wishlist from Solr
		$like_products = $solr->getProductFromSolrOnly($filter)['response']['docs'];

		$price_markup = 0.25;
		$data = array();
		$customer_see_products = array();
		foreach ($like_products as $key => $value) {
			foreach ($value['category_id'] as $cid) {
				if (empty($data[$cid])) {
					$data[$cid] = array();
					$data[$cid]['seller'][] = $value['seller_id'];
					$data[$cid]['price_range'] = $value['price'] * (1 - $price_markup) . "-" . $value['price'] * (1 + $price_markup);
				} else {
					$price_range = explode('-', $data[$cid]['price_range']);
					$min_price = $price_range[0];
					$max_price = $price_range[1];
					$min_price = min($value['price'] * (1 - $price_markup), $min_price);
					$max_price = max($value['price'] * (1 + $price_markup), $max_price);
					$data[$cid]['price_range'] = $min_price . "-" . $max_price;
					if (!in_array($value['seller_id'], $data[$cid]['seller'])) {
						$data[$cid]['seller'][] = $value['seller_id'];
					}
				}
			}
			$customer_see_products[] = $value['id'];
		}
		$result = array();
		$result['customer_see_products'] = $customer_see_products;
		$result['details'] = $data;
		return $result;
	}

	/**
	 * _youMayLike
	 * Here, We get Customer Preference Products from Solr.
	 * @param  int   customer_id
	 * @param  Array result
	 * @return Array Products
	 * @author Garvit
	 **/
	private function _youMayLike($customer_id, $result) {

		$total_like_products = 0;
		$solr = new SolrProduct($this);
		$like_products_arr = array();
		$preference_products = array();
		$this->load->model('restapi/service');
		$dislike_products = $this->model_restapi_service->getCustomerDislikedProduct($customer_id);

		if (isset($dislike_products['product_ids']) && !empty($dislike_products['product_ids'])) {
			$dislike_product = explode(',', $dislike_products['product_ids']);
		} else {
			$dislike_product = array();
		}

		$removed_product = array_unique(array_merge($dislike_product, $result['customer_see_products']));
		$rem_product = implode(',', $removed_product);

		$address = $this->model_restapi_service->getDefaultAddress($customer_id);
		$postcode = $address['postcode'];

		foreach ($result['details'] as $key => $value) {
			if ($key != 0) {
				$filter = array();
				$filter['filter_category_id'] = $key;
				$filter['filter_seller_id'] = implode(",", $value['seller']);
				$filter['price_filter'] = $value['price_range'];
				$filter['call_from'] = 'app';
				$filter['sort_data_by'] = 'hotness_value';
				$filter['order_data_by'] = 'Desc';
				$filter['user_id'] = $customer_id;
				$filter['not_product_ids'] = $rem_product;
				if (!empty($postcode)) {
					$filter['post_code'] = $postcode;
				}

				$like_products = $solr->getProductFromSolr($filter);
				$like_products_arr[] = $like_products;

				$total_like_products = $total_like_products + $like_products['total'];
			}
		}
		// We get Customer preference data only when if Order AND Whishlist Data is not More then 50.
		if ($total_like_products < 500) {
			$preference_data = $this->_getSimilarProductsByCustomerPreference($customer_id);
			$preference_products = array();
			if (!empty($preference_data)) {
				foreach ($preference_data as $value) {
					$filter = array();
					$filter['filter_category_id'] = $value['category_id'];
					if ((int) $value['max_price'] != 0) {
						$filter['price_filter'] = $value['min_price'] . '-' . $value['max_price'];
					}
					$filter['call_from'] = 'app';
					$filter['sort_data_by'] = 'hotness_value';
					$filter['order_data_by'] = 'Desc';
					$filter['user_id'] = $customer_id;
					$filter['not_product_ids'] = $rem_product;
					if (!empty($postcode)) {
						$filter['post_code'] = $postcode;
					}

					$preference_products[] = $solr->getProductFromSolr($filter);
				}
			}
			$like_products_arr = array_merge($like_products_arr, $preference_products);
		}

		$new_products_arr = array();
		foreach ($like_products_arr as $val) {
			foreach ($val['data'] as $product) {
				$new_products_arr[] = $product;
			}
		}
		shuffle($new_products_arr);
		return $new_products_arr;
	}

	/**
	 * _whatOthersLike
	 * info Customer's product_id and min_price,max_price from customer's preference
	 * @param  int   customer_id
	 * @param  Array result
	 * @return Array Products
	 * @author Garvit
	 **/
	private function _whatOthersLike($customer_id, $result) {

		$order_by = $this->_getProductsByCustomersPreference($customer_id, $result, 'order_by_count');

		$wishlist = $this->_getProductsByCustomersPreference($customer_id, $result, 'wishlist_count');

		$like_products_arr = array_merge($order_by['products_arr'], $wishlist['products_arr']);
		$total_like_products = $order_by['total_products'] + $wishlist['total_products'];

		// Get customer Preference.
		if ($total_like_products < 500) {
			$preference_data = $this->_getSimilarProductsByCustomerPreference($customer_id);
			$preference_products = array();
			if (!empty($preference_data)) {
				$solr = new SolrProduct($this);
				foreach ($preference_data as $value) {
					$filter = array();
					$filter['filter_category_id'] = $value['category_id'];
					if ((int) $value['max_price'] != 0) {
						$filter['price_filter'] = $value['min_price'] . '-' . $value['max_price'];
					}
					$filter['sort_data_by'] = 'order_by_count';
					$filter['order_data_by'] = 'Desc';
					$filter['call_from'] = 'app';
					$filter['user_id'] = $customer_id;
					$filter['not_product_ids'] = implode(',', $result['customer_see_products']);
					$preference_products[] = $solr->getProductFromSolr($filter);
				}
			}
			$like_products_arr = array_merge($like_products_arr, $preference_products);
		}

		$new_products_arr = array();
		foreach ($like_products_arr as $key => $val) {
			foreach ($val['data'] as $product) {
				$new_products_arr[] = $product;
			}
		}
		return $new_products_arr;
	}

	/**
	 * _getSimilarProductsByCustomerPreference
	 * info Customer's product_id and min_price,max_price from customer's preference
	 * @param  int   customer_id
	 * @return Array cat_id, min_price, max_price
	 * @author Yogesh, Garvit
	 **/
	private function _getSimilarProductsByCustomerPreference($customer_id) {
		$price_markup = 0.10;
		$sql = "SELECT  DISTINCT category_id, min_price*(1-$price_markup) as min_price,
				max_price*(1+$price_markup) as max_price
				from oc_customer_preference
				where customer_id = " . $customer_id;
		$preference_category_id = $this->db->query($sql)->rows;
		return $preference_category_id;
	}

	private function _getProductsByCustomersPreference($customer_id, $result, $type) {
		$total_like_products = 0;
		$solr = new SolrProduct($this);
		$like_products_arr = array();

		$this->load->model('restapi/service');
		$dislike_products = $this->model_restapi_service->getCustomerDislikedProduct($customer_id);

		if (isset($dislike_products['product_ids']) && !empty($dislike_products['product_ids'])) {
			$dislike_product = explode(',', $dislike_products['product_ids']);
		} else {
			$dislike_product = array();
		}

		$removed_product = array_unique(array_merge($dislike_product, $result['customer_see_products']));
		$rem_product = implode(',', $removed_product);

		$address = $this->model_restapi_service->getDefaultAddress($customer_id);
		$postcode = $address['postcode'];

		foreach ($result['details'] as $key => $value) {
			if ($key != 0) {
				$filter = array();
				$filter['filter_category_id'] = $key;
				$filter['filter_seller_id'] = implode(",", $value['seller']);
				$filter['price_filter'] = $value['price_range'];
				$filter['sort_data_by'] = $type;
				$filter['order_data_by'] = 'Desc';
				$filter['call_from'] = 'app';
				$filter['not_product_ids'] = $rem_product;
				$filter['user_id'] = $customer_id;
				if (!empty($postcode)) {
					$filter['post_code'] = $postcode;
				}

				$like_products = $solr->getProductFromSolr($filter);
				$like_products_arr[] = $like_products;
				$total_like_products = $total_like_products + $like_products['total'];
			}
		}
		$data['products_arr'] = $like_products_arr;
		$data['total_products'] = $total_like_products;
		return $data;
	}

	/**
	 * Method for get pieces sold
	 * @param  int   product_id
	 * @return total piece sold
	 * @author vikas,2017
	 **/
	public function getPiecesSold($product_id) {
		$sql = "SELECT SUM(oop.quantity * oop.piece_in_set) as pieces_sold
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub
                    ON (osub.order_id = o.order_id)
                INNER JOIN " . DB_PREFIX . "order_product oop
                    ON (oop.order_id = o.order_id)
                WHERE oop.product_id = '" . (int) $product_id . "'
                  AND osub.suborder_id = oop.suborder_id
                  AND  osub.order_status_id > 2 ";
		$query = $this->db->query($sql);

		if ($query->num_rows > 0) {
			return $query->row['pieces_sold'];
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

	public function getProductUnit($product_id) {

		$units = array();

		/*$sql = "SELECT unit_id FROM " . DB_PREFIX . "product
			                WHERE product_id = '" . $product_id . "'";
			        $op_query = $this->db->query($sql);

			        //first check unit_id of product
			        if($op_query->row['unit_id'] != 0 && $op_query->row['unit_id'] != '' && $op_query->row['unit_id'] != NULL) {
			            $unit_id = (int)$op_query->row['unit_id'];
			        }else{
			            //first get unit_id from product category_id have high MAX(tag_priority)
			            $sql = "SELECT PC.category_id, PC.product_id, C.unit_id FROM " . DB_PREFIX . "product_to_category AS PC "
			                    . "LEFT JOIN " . DB_PREFIX . "category AS C ON C.category_id = PC.category_id "
			                    . "WHERE PC.product_id = " . $product_id . " ORDER BY C.tag_priority DESC limit 1";
			            //echo $sql; die;
			            $query = $this->db->query($sql);
			            $unit_id = $query->row['unit_id'];
			        }
			        $sql = "SELECT super_unit, base_unit FROM " . DB_PREFIX . "units
			                WHERE unit_id = '" . $unit_id . "'
			                AND status = '1'";
			        $opu_query = $this->db->query($sql);
			        $units['unit_id'] = $unit_id;
			        if($opu_query->num_rows > 0){
			            $units['base_unit'] = $opu_query->row['base_unit'];
			            $units['super_unit'] = $opu_query->row['super_unit'];
		*/

		$units['unit_id'] = "1";
		$units['base_unit'] = "Piece";
		$units['super_unit'] = "Set";

		return $units;

	}

	public function getLikePreferenceProducts($data, $like_type) {
		$filter_data = array();
		$results = array();

		// get customer preferences
		$this->load->model('preferences');
		$category_ids = $this->model_preferences->getAllPositivePreferenceCategories((int) $data['user_id']);

		if (!empty($category_ids)) {
			$filter_data['filter_category_id'] = $category_ids;
		}

		if (isset($data['call_from']) && !empty($data['call_from'])) {
			$filter_data['call_from'] = $data['call_from'];
		}
		if (isset($data['user_id']) && !empty($data['user_id'])) {
			$filter_data['user_id'] = $data['user_id'];
		}

		$filter_data['start'] = 0;
		$filter_data['limit'] = 500;

		/*** show single ***/
		$show_single = $data['show_single'] ?? 0;
		if ((int) $show_single == 1) {
			$custom_store = "single";
		} else {
			$custom_store = "wholesale";
		}
		$filter_data['custom_store'] = $custom_store;

		if ($like_type == 'you_may_like') {

			// get disliked products
			$this->load->model('restapi/service');
			$dislike_products_ids = $this->model_restapi_service->getCustomerDislikedProduct($data['user_id']);
			if (!empty($dislike_products_ids) && !empty($dislike_products_ids['product_ids'])) {
				$filter_data['not_product_ids'] = $dislike_products_ids['product_ids'];
			}

			$filter_data['page'] = 1;
			$filter_data['sort'] = 'sort_order';
			$filter_data['shuffle'] = false;
		}

		if ($like_type == 'what_others_like') {

			$filter_data['sort_data_by'] = 'hotness_value';
			$filter_data['order_data_by'] = 'desc';

		}

		if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
			$solr = new SolrProduct($this);
			$results_solr = $solr->getProductFromSolr($filter_data);
			if (isset($data['call_from']) && $data['call_from'] == 'app') {
				$results = $results_solr['data'];
			} else {
				$results = $results_solr['products'];
			}

		}

		return $results;
	}

	public function getProductByModel($product_model) {

		$sql = "SELECT op.product_id, op.seller_tax, op.commission, op.sku, op.model, op.image, op.selling_price, op.price, op.price_per_set, op.piece_in_set, op.minimum, opd.name, opd.set_description,  op.seller_tax, op.commission, op.tax_class_id, op.tax_class_id, op.mrp from " . DB_PREFIX . "product op left join " . DB_PREFIX . "product_description opd on op.product_id=opd.product_id and opd.language_id=1 WHERE op.model='" . $product_model . "'";
		$op_query = $this->db->query($sql);
		if ($op_query->num_rows == 0) {
			return false;
		}

		$product_id = (int) $op_query->row['product_id'];
		$data = array('product_id' => $product_id);
		$price_details = $this->cart->getPrice($data, $this->registry);
		$p_base_unit = '';
		$p_super_unit = '';

		$units = $this->getProductUnit($product_id);
		$p_base_unit = $p_super_unit = '';
		if (count($units) > 0) {
			$p_base_unit = $units['base_unit'];
			$p_super_unit = $units['super_unit'];
		}
		return array(
			'product_detail' => $op_query->row,
			'p_base_unit' => $p_base_unit,
			'p_super_unit' => $p_super_unit,
			'tax_rate' => $price_details['output_tax_rates'],
			'seller_tax' => $price_details['seller_tax'],
		);

	}

	public function updateProductSortOrder($product_ids, $new_sort_order) {

		$sql = "UPDATE " . DB_PREFIX . "product
              SET sort_order = " . $new_sort_order . ",
                  date_modified = NOW() ";
		if (is_array($product_ids)) {
			$sql .= "WHERE product_id IN (" . implode(',', $product_ids) . ")";
		} else {
			$sql .= "WHERE product_id IN (" . $product_ids . ")";
		}

		$result = $this->db->query($sql);
		return $result;
	}

	public function deletePromotion($promotion_id) {
		$sql = "DELETE FROM " . DB_PREFIX . "seller_promotion WHERE id = " . (int) $promotion_id;
		$result = $this->db->query($sql);
		return $result;
	}

	public function getDemotionEligibleProductIds($product_ids) {
		// $product_ids comma separated
		$sql = "SELECT GROUP_CONCAT(product_id) as product_ids FROM " . DB_PREFIX . "product WHERE product_id IN (" . $product_ids . ")
              AND sort_order < 999 AND sort_order > 0";

		$result = $this->db->query($sql);
		if ($result->num_rows) {return $result->row['product_ids'];}
		return false;
	}

	public function getPromotionsToDisable() {
		$sql = "SELECT * FROM " . DB_PREFIX . "seller_promotion WHERE (DATEDIFF(NOW(), added_date) > 2 OR status = 0)";
		$result = $this->db->query($sql);
		if ($result->num_rows) {return $result->rows;}
		return false;
	}

	public function getCategoryName($category_id) {
		$sql = "SELECT name from " . DB_PREFIX . "category_description
          WHERE category_id = " . $category_id . "";
		$result = $this->db->query($sql);
		return $result->row;
	}

	// get seller information by seller id
	public function getSeller($seller_id) {

		$query = $this->db->query("SELECT ms.company as seller_company,ms.*,c.*,wss.*,
                                   (SELECT keyword FROM `oc_url_alias` WHERE query = 'seller_id=" . $seller_id . "' ) as seller_seokeyword
                                   FROM " . DB_PREFIX . "ms_seller ms
                                     INNER JOIN " . DB_PREFIX . "customer c
                                       ON (ms.seller_id = c.customer_id)
                                     LEFT JOIN " . DB_PREFIX . "wsb_seller_to_store wss
                                       ON (wss.seller_id = ms.seller_id)
                                     WHERE ms.seller_id = '" . (int) $seller_id . "'
                                     ");

		$additional_emails = $this->db->query("SELECT email FROM " . DB_PREFIX . "customer_additional_email WHERE customer_id = " . $seller_id);
		$query->row['additional_email'] = implode(',', array_column($additional_emails->rows, 'email'));

		return $query->row;
	}
	/**
	 * method for get product by id
	 * @author kalyan 28th Feb 2018
	 */
	public function getProductById($product_id) {

		$sql = "SELECT op.product_id, op.seller_tax, op.commission, op.sku, op.model, op.image, op.selling_price, op.price, op.price_per_set, op.piece_in_set, op.minimum, opd.name, opd.set_description,  op.seller_tax, op.commission, op.tax_class_id, op.tax_class_id, op.mrp, op.quantity from " . DB_PREFIX . "product op left join " . DB_PREFIX . "product_description opd on op.product_id=opd.product_id and opd.language_id=1 WHERE op.product_id='" . $product_id . "'";
		$op_query = $this->db->query($sql);
		if ($op_query->num_rows == 0) {
			return false;
		}

		$product_id = (int) $op_query->row['product_id'];
		$data = array('product_id' => $product_id);
		$price_details = $this->cart->getPrice($data, $this->registry);
		$p_base_unit = '';
		$p_super_unit = '';

		$units = $this->getProductUnit($product_id);
		$p_base_unit = $p_super_unit = '';
		if (count($units) > 0) {
			$p_base_unit = $units['base_unit'];
			$p_super_unit = $units['super_unit'];
		}

		// category query
		$sql = "SELECT GROUP_CONCAT(category_id SEPARATOR ',') AS category_id FROM " . DB_PREFIX . "product_to_category
                WHERE product_id = '" . (int) $product_id . "' GROUP BY product_id";
		$cat_query = $this->db->query($sql);

		//meta data
		$opdesc_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description
                                              WHERE product_id = '" . (int) $product_id . "' AND language_id = '" . (int) $this->config->get('config_language_id') . "'");

		// oc_product_special query
		$sql = "SELECT price AS special, date_start, date_end FROM " . DB_PREFIX . "product_special
                WHERE product_id = '" . (int) $product_id . "'
                  AND ((date_start = '0000-00-00' OR date_start < NOW())
                  AND (date_end = '0000-00-00' OR date_end > NOW()))
                ORDER BY priority ASC, price ASC LIMIT 1";
		$opspl_query = $this->db->query($sql);

		// seller query
		$sql = "SELECT msp.seller_id, ms.nickname FROM " . DB_PREFIX . "ms_product msp
                INNER JOIN " . DB_PREFIX . "ms_seller ms ON msp.seller_id=ms.seller_id
                WHERE msp.product_id = '" . (int) $product_id . "'";
		$msp_query = $this->db->query($sql);

		return array(
			'product_detail' => $op_query->row,
			'meta_data' => $opdesc_query->row,
			'p_base_unit' => $p_base_unit,
			'p_super_unit' => $p_super_unit,
			'tax_rate' => $price_details['output_tax_rates'],
			'seller_tax' => $price_details['seller_tax'],
			'category_id' => (!empty($cat_query->row['category_id'])) ? $cat_query->row['category_id'] : 0,
			'special' => !empty($opspl_query->row['special']) ? $opspl_query->row['special'] : 0,
			'seller_id' => $msp_query->row['seller_id'],
			'seller_nickname' => $msp_query->row['nickname'],
		);

	}
	/**
	 * method for get product Previously order or not using productId and userId
	 * @author rahul 28th May 2018
	 */
	public function getProductPreviouslyOrder(int $product_id, int $customer_id = 0): int {
		// if(!empty($product_id) && !empty($customer_id)){
		//     $sql = "SELECT 1
		//             FROM " . DB_PREFIX . "order AS oo
		//             INNER JOIN " . DB_PREFIX . "suborder AS osub
		//                     ON osub.order_id = oo.order_id
		//                        AND osub.order_status_id > 0
		//                        AND osub.order_status_id <> 2
		//             INNER JOIN " . DB_PREFIX . "order_product AS oop
		//                     ON oop.suborder_id = osub.suborder_id
		//             WHERE oop.product_id = '" . (int)$product_id . "'
		//               AND oo.customer_id = '" . (int)$customer_id . "'
		//             LIMIT 1";
		//     $productPreviouslyOrderQuery = $this->db->query($sql);
		//     return (int)$productPreviouslyOrderQuery->num_rows;
		// }

		return 0;
	}

	/*
		    * @method: get Combo Product Id Of an associate product.
		    * here we are assuming that an associate product will be part of a single combo product id
		    * @param: $associate_product id
		    * @return: combo product id, if product is not part of any combo then return 0
		    * @author: Devendra, July 2018
	*/
	public function getComboProductIdOfAssociate($associate_product_id) {
		$sql = "SELECT product_id FROM " . DB_PREFIX . "product_to_associate
              WHERE associate_product_id='" . (int) $associate_product_id . "' LIMIT 1";
		$result = $this->db->query($sql);

		if ($result->num_rows) {
			return $result->row['product_id'];
		}

		return 0;
	}

	/*
		    * @param: $product ids
		    * @return: product detail
		    * @author: Mahaveer 2019
	*/
	public function getWebengageProductDetail(string $product_ids): array{
		// Need to sanitize the input string, which is supposed to be a comma separated string of "int" product_id(s)
		// So, we explode back to array. array_map to convert them to integers (to prevent SQL injection)
		// Afterwards, we array_filter it out to remove invalid values. Then array_unique to remove duplicates
		$pid = array_unique(array_filter(array_map('intval', explode(',', $product_ids)), function ($v) {return $v > 0;}));

		// Now, run this query only when we have non-empty $pid array
		if (empty($pid)) {
			return array();
		}

		$sql = "SELECT op.product_id,
                       op.model,
                       op.image,
                       op.selling_price,
                       opd.name,
                       op.seller_tax,
                       op.hsn_code,
                       op.commission,
                       op.tax_class_id,
                       op.piece_in_set,
                       op.price,
                       op.mrp,
                       op.quantity,
                       opd.meta_title,
                       opd.meta_description,
                       opd.meta_keyword,
                       ops.price AS special,
                       msp.seller_id,
                       ms.nickname as seller_nickname,
                       GROUP_CONCAT(ptc.category_id SEPARATOR ',') AS category_id,
                       GROUP_CONCAT(cd.name SEPARATOR ',') AS category_name
                       from " . DB_PREFIX . "product op
                       inner join " . DB_PREFIX . "product_description opd
                       on op.product_id=opd.product_id and opd.language_id=1
                       left join " . DB_PREFIX . "product_special ops
                       on op.product_id=ops.product_id
                       AND ((ops.date_start = '0000-00-00' OR ops.date_start < NOW())
                       AND (ops.date_end = '0000-00-00' OR ops.date_end > NOW()))
                       inner join " . DB_PREFIX . "ms_product msp
                       on msp.product_id=opd.product_id
                       inner join " . DB_PREFIX . "ms_seller ms
                       on ms.seller_id=msp.seller_id
                       inner join " . DB_PREFIX . "product_to_category ptc
                       on ptc.product_id=opd.product_id
                       inner join " . DB_PREFIX . "category_description cd
                       on cd.category_id=ptc.category_id and cd.language_id=1
                       WHERE op.product_id IN (" . implode(',', $pid) . ")";

		$op_query = $this->db->query($sql);
		return $op_query->rows;
	}

	public function getProductBrandName($product_id) {
		$q = "SELECT fd.name as filter_name
              FROM " . DB_PREFIX . "product_filter f
              INNER JOIN " . DB_PREFIX . "filter_description fd
                ON f.filter_id = fd.filter_id
              INNER JOIN " . DB_PREFIX . "filter fl
              ON f.filter_id = fl.filter_id
              INNER JOIN " . DB_PREFIX . "filter_group_description fgd
              ON fl.filter_group_id = fgd.filter_group_id
              AND fgd.language_id = 1
              AND fgd.name = 'Brand Name'
              WHERE
                f.product_id = '" . (int) $product_id . "'
                AND fd.language_id = '1'";

		$query = $this->db->query($q);
		return !empty($query->row['filter_name']) ? $query->row['filter_name'] : '';
	}

	public function getProductByIdMinimalData($product_id) {

		$sql = "SELECT op.product_id,op.price, op.weight, opd.name, opd.set_description, op.mrp,op.commission,op.tax_class_id, op.seller_tax, op.piece_in_set, owc.title AS weight_title,  owc.unit AS weight_unit from " . DB_PREFIX . "product op left join " . DB_PREFIX . "product_description opd on op.product_id=opd.product_id and opd.language_id=1 INNER JOIN " . DB_PREFIX . "weight_class_description owc ON ( op.weight_class_id = owc.weight_class_id)  WHERE op.product_id='" . $product_id . "'";
		$op_query = $this->db->query($sql);
		if ($op_query->num_rows == 0) {
			return false;
		}

		// oc_product_special query
		$sql = "SELECT price AS special, date_start, date_end FROM " . DB_PREFIX . "product_special
                WHERE product_id = '" . (int) $product_id . "'
                  AND ((date_start = '0000-00-00' OR date_start < NOW())
                  AND (date_end = '0000-00-00' OR date_end > NOW()))
                ORDER BY priority ASC, price ASC LIMIT 1";
		$opspl_query = $this->db->query($sql);

		// seller query
		// $sql = "SELECT msp.seller_id, ms.nickname FROM " . DB_PREFIX . "ms_product msp
		//               INNER JOIN " . DB_PREFIX . "ms_seller ms ON msp.seller_id=ms.seller_id
		//               WHERE msp.product_id = '" . (int) $product_id . "'";
		// $msp_query = $this->db->query($sql);

		$price = $op_query->row['price'] ?? 0;
		$commission = $op_query->row['commission'] ?? 0;
		$tax_class_id = $op_query->row['tax_class_id'] ?? 0;
		$seller_tax = $op_query->row['seller_tax'] ?? 0;
		$piece_in_set = $op_query->row['piece_in_set'] ?? 0;
		$mrp = $op_query->row['mrp'] ?? 0;
		$weight = $this->formatWeightNew($op_query->row['weight']) ?? 0;
		$weight_title = $op_query->row['weight_title'] ?? '';
		$weight_unit = $op_query->row['weight_unit'] ?? '';

		$result = array(
			'price' => $price,
			'commission' => $commission,
			'tax_class_id' => $tax_class_id,
			'seller_tax' => $seller_tax,
			'piece_in_set' => $piece_in_set,
			'special' => !empty($opspl_query->row['special']) ? $opspl_query->row['special'] : 0,
			'mrp' => $mrp,
		);

		$price = $this->getPrice($result);

		$array = array(
			'special' => !empty($opspl_query->row['special']) ? $opspl_query->row['special'] : 0,
			'product_id' => $op_query->row['product_id'] ?? 0,
			'name' => $op_query->row['name'] ?? '',
			'set_description' => $op_query->row['set_description'] ?? '',
			// 'seller_nickname' => $msp_query->row['nickname'],
			'weight' => $weight,
			'weight_title' => $weight_title,
			'weight_unit' => $weight_unit,
		);
		// $array . array_($array, $op_query->row);
		$arrayqq = array_merge($array, $price);

		return $arrayqq;

	}

	public function formatWeightNew($value, $decimal_point = '.', $thousand_point = ',') {
		return number_format($value, 2, $decimal_point, $thousand_point);
	}

}
