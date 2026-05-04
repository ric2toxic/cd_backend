<?php
class ModelSellerUpdateInventory extends Model {
	
	public function addProduct($data) {
		//echo "<pre>"; print_r($data); exit;
		$this->event->trigger('pre.admin.product.add', $data);
		$seller_tax = '5.5';//$data['seller_tax'];
		$commission = '8.5';//$data['commission'];
		$tax_class_id = 9;//$data['tax_class_id']
		$data['model'] = $this->MsLoader->MsSeller->getNickname().'_'.$data['sku'];
        
        $seller_tax_factor = 1.0 + ( (float)$seller_tax / 100.0 );
        $commission_factor = 1.0 + ( (float)$commission / 100.0 );
        $selling_price = ceil($commission_factor * (float)($data['price']) / $seller_tax_factor);

		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape(trim($data['model'])) . "', sku = '" . $this->db->escape(trim($data['sku'])) . "', quantity = '" . (int)$data['quantity'] . "', price = '" . (float)$data['price'] . "', selling_price = '" . (float)$selling_price . "', price_per_set = '" . (float)((float)$data['price']*(int)$data['piece_in_set']) . "', piece_in_set = '" . (int)$data['piece_in_set'] . "', seller_tax = '" . (float)$seller_tax . "', commission = '" . (float)$commission . "',tax_class_id = '" . (int)$tax_class_id . "', date_added = NOW()");

		$product_id = $this->db->getLastId();
		
		
		//Save in product to approve table
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_approve SET product_id = '" . (int)$product_id . "', approved = '0'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		//foreach ($data['product_description'] as $language_id => $value) {
			$language_id = 1;
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($data['title']) . "', set_description = '" . $this->db->escape($data['set_description']) . "', description = '" . $this->db->escape($data['description']) . "', tag = '" . $this->db->escape($data['tag']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'");
			//$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($data['title']) . "', set_description = '" . $this->db->escape($data['set_description']) . "', description = '" . $this->db->escape($data['description']) . "'");
		//}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				//$store_id = 0;
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");

				if (isset($data['store_price'])) {

					foreach ($data['store_price'] as $key => $value) {
						if ($key == $store_id) {
							$this->db->query("UPDATE ".DB_PREFIX."product_to_store 
									SET store_price="."'" .$this->db->escape($value[0])."'  
									WHERE store_id="."'".$this->db->escape($key)."' 
									AND product_id="."'" . (int)$product_id . "'");						
						}
						 
					} 
				} 

			} 
		}
		// insert data in ms_product table 
		$seller_id = $this->session->data['customer_id'];
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_product SET product_id = '" . (int)$product_id . "', seller_id = '" . (int)$seller_id . "' ");
		
		$store_id = 0;
		$store_price = "0.0";
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");

		$this->db->query("UPDATE ".DB_PREFIX."product_to_store 
				SET store_price="."'" .$this->db->escape($store_price)."'  
				WHERE store_id="."'".$this->db->escape($store_id)."' 
				AND product_id="."'" . (int)$product_id . "'");						
		

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', store_id = '" . (int)$product_discount['store_id'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image) . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if (isset($data['keyword']) && !empty($data['keyword'])) {
			$seo_keyword = $data['keyword'];
		}
		else {
			$seo_keyword = $this->setSeoUrl( $data['description'], $data['model']);
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $seo_keyword . "'");



        if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
        }
        
		if (isset($data['product_store_info'])) {

			foreach ($data['product_store_info'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_storeinfo 
					SET product_id = " . (int)$product_id . ", 
					store_id = " . (int)$value['store_id'] . ",
					language = " . (int)$value['language'] . ", 
					meta_title = " . "'" .$this->db->escape($value['meta_title'])."'" . ",
					meta_keywords = " ."'" .$this->db->escape($value['meta_title'])."'" . ",
					meta_description = " . "'". $this->db->escape($value['meta_description'])."'". ",
					created = NOW()");
			}
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.add', $product_id);

		return $product_id;
	}

}
