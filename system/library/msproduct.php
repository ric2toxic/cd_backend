<?php
class MsProduct extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_DELETED = 4;
	const STATUS_UNPAID = 5;
	
	const MS_PRODUCT_VALIDATION_NONE = 1;
	const MS_PRODUCT_VALIDATION_APPROVAL = 2;
	
	private $errors;
	
	
	private function _getDepth($a, $eid) {
		foreach ($a as $key => $val) {
			if ($val['category_id'] == $eid) {
				if ($val['parent_id'] == 0) {
					return 0;
				} else {
					return 1 + $this->_getDepth($a, $val['parent_id']);
				}
			}
		}
	}
	
	private function _getPath($category_id) {
		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $query->row['name'];
		} else {
			return $query->row['name'];
		}
	}
	
	public function getCategories($parent_id = 0) {
		//$category_data = $this->cache->get('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id);
		$category_data = FALSE;
		
		if (!$category_data) {
			$category_data = array();
		
			$sql = "SELECT
					c.category_id,
					c.parent_id,
					cd.name,
					(SELECT COUNT(*) FROM `" . DB_PREFIX . "category` cc WHERE cc.parent_id = c.category_id) as children
			FROM `" . DB_PREFIX . "category` c
			LEFT JOIN `" . DB_PREFIX . "category_description` cd
				ON (c.category_id = cd.category_id)
			WHERE c.parent_id = " . (int)$parent_id . "
			AND c.status = 1
			AND cd.language_id = " . (int)$this->config->get('config_language_id') . "
			ORDER BY c.sort_order, cd.name ASC";
			
			$query = $this->db->query($sql);
		
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'category_id' => $result['category_id'],
					'parent_id' => $result['parent_id'],
					'name'        => $result['name'],
					'children'        => $result['children'],
					'disabled' => ((in_array($result['category_id'], $this->config->get('msconf_restrict_categories')) || ($this->config->get('msconf_additional_category_restrictions') == 1 && $result['parent_id'] == 0) || ($this->config->get('msconf_additional_category_restrictions') == 2 && $result['children'] > 0)) ? TRUE : FALSE)
				);
			
				//Recursive call of the function and merge of all the categories together
				$category_data = array_merge($category_data, $this->getCategories($result['category_id']));
			}
	
			//$this->cache->set('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $category_data);
		}
		
		// The first calls of the function (for the root categories), where indentation takes place
		if ($parent_id == 0) {
			$category_data_indented = array();
			foreach ($category_data as $category) {
				$category_data_indented[] = array(
					'category_id' => $category['category_id'],
					'name'        => str_repeat('&nbsp;&nbsp;&nbsp;',$this->_getDepth($category_data, $category['category_id'])) . $category['name'],
					'parent_id' => $category['parent_id'],
					'children' => $category['children'],
					'disabled' => $category['disabled'],
				);
			}
			return $category_data_indented;
		}
		
		return $category_data;
	}
	
	public function getSellerId($product_id) {
		$sql = "SELECT seller_id FROM " . DB_PREFIX . "ms_product
				WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);
		
		if (isset($res->row['seller_id']))
			return $res->row['seller_id'];
		else
			return 0;
	}
	
	public function isEnabled($product_id) {
		$sql = "SELECT	p.status as enabled,
				FROM `" . DB_PREFIX . "product` p
				WHERE p.product_id = " . (int)$product_id;

		$res = $this->db->query($sql);
		
		if (!$res->row['enabled'])
			return false;
		else
			return true;
	}	
	
	public function getProductImages($product_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC";
		$res = $this->db->query($sql);
		
		$images = array();
		foreach ($res->rows as $row) {
			$images[$row['product_image_id']] = $row;
		}
		
		return $images;
	}

	public function getProductCategories($product_id) {
		$sql = "SELECT group_concat(ptc.category_id separator ',') as category_id FROM `" . DB_PREFIX . "product_to_category` ptc WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		return $res->row['category_id'];
	}

	public function getProductDownloads($product_id) {
		$sql = "SELECT 	*
				FROM `" . DB_PREFIX . "download` d
				LEFT JOIN `" . DB_PREFIX . "product_to_download` pd
					USING(download_id)
				WHERE pd.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		
		$downloads = array();
		foreach ($res->rows as $row) {
			$downloads[$row['download_id']] = $row;
		}
		
		return $downloads;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
		
		return $query->rows;
	}
	
	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
		
		return $query->rows;
	}

	public function getProductThumbnail($product_id) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->row;
	}		
		
	public function saveProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$store_id = $this->config->get('config_store_id');

		if (isset($data['product_thumbnail'])) {
			$thumbnail = $this->MsLoader->MsFile->moveImage($data['product_thumbnail']);
		} else {
			$thumbnail = '';
		}

        $model = isset($data['product_model']) ? $data['product_model'] : $this->db->escape($data['languages'][$first]['product_name']);
        $sku = isset($data['product_sku']) ? $data['product_sku'] : '';
        $manufacturer_id = isset($data['product_manufacturer_id']) ? $data['product_manufacturer_id'] : 0;
        $tax_class_id = isset($data['product_tax_class_id']) ? $data['product_tax_class_id'] : 0;
        $stock_status_id = isset($data['product_stock_status_id']) ? $data['product_stock_status_id'] : $this->config->get('config_stock_status_id');
        $date_available = isset($data['product_date_available']) ? $data['product_date_available'] : date('Y-m-d', time() - 86400);

		$sql = "INSERT INTO " . DB_PREFIX . "product
				SET model = '" . $this->db->escape($model) . "',
				    sku = '" . $this->db->escape($sku) . "',
				    manufacturer_id = '" . (int)$manufacturer_id . "',
				    price = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($data['product_price']) . ",
					image = '" .  $this->db->escape($thumbnail)  . "',
					subtract = " . (int)$data['product_subtract'] . ",
                    tax_class_id = '" . $this->db->escape($tax_class_id) . "',
					stock_status_id = '" . (int)$stock_status_id . "',
					date_available = '" . $this->db->escape($date_available) . "',
					quantity = " . (int)$data['product_quantity'] . ",
					shipping = " . (int)$data['product_enable_shipping'] . ",
					status = " . (int)$data['enabled'] . ",
					date_added = NOW(),
					date_modified = NOW()";
		
		$this->db->query($sql);
		$product_id = $this->db->getLastId();
		
		if (isset($data['keyword'])) {
			//$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($data['keyword']) . "%' AND query LIKE 'product_id=%'");
			$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($data['keyword']) . "%'");
			$number = $similarity_query->num_rows;
			
			if ($number > 0) {
				$data['keyword'] = $data['keyword'] . "-" . $number;
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
		
		foreach ($data['languages'] as $language_id => $language) {
            $meta_description = isset($language['product_meta_description']) ? htmlspecialchars(nl2br($language['product_meta_description']), ENT_COMPAT) : '';
            $meta_keyword = isset($language['product_meta_keyword']) ? htmlspecialchars(nl2br($language['product_meta_keyword']), ENT_COMPAT) : '';

			$sql = "INSERT INTO " . DB_PREFIX . "product_description
					SET product_id = " . (int)$product_id . ",
						name = '". $this->db->escape($language['product_name']) ."',
						description = '". $this->db->escape($language['product_description']) ."',
						meta_description = '". $this->db->escape($meta_description) ."',
						meta_keyword = '". $this->db->escape($meta_keyword) ."',
						tag = '" . $this->db->escape($language['product_tags']) . "',
						language_id = " . (int)$language_id;
			$this->db->query($sql);
			
		}
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_product
				SET product_id = " . (int)$product_id . ",
					seller_id  = " . (int)$this->registry->get('customer')->getId();

		$this->db->query($sql);
		
		foreach ($data['product_category'] as $id => $category_id) {
			$sql = "INSERT INTO " . DB_PREFIX . "product_to_category
					SET product_id = " . (int)$product_id . ",
						category_id = " . (int)$category_id;
			$this->db->query($sql);
		}

		$sql = "INSERT INTO " . DB_PREFIX . "product_to_store
				SET product_id = " . (int)$product_id . ",
					store_id = " . (int)$store_id;
		$this->db->query($sql);


		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $key => $img) {
				$newImagePath = $this->MsLoader->MsFile->moveImage($img);
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}
		
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
				$fileMask = substr($newFile,0,strrpos($newFile,'.'));
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "download SET filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
				$download_id = $this->db->getLastId();
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				
				foreach ($data['languages'] as $language_id => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
				}
			}
		}

		// options
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				// unset sample
				if (isset($product_option['product_option_value'][0])) unset($product_option['product_option_value'][0]);
				
				// get type 
				$o = $this->MsLoader->MsOption->getOptions(array('option_id' => $product_option['option_id'], 'single' => 1));
				if (!$o) continue; else { $product_option['type'] = $o['type']; }
				
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
		
					$product_option_id = $this->db->getLastId();
		
					if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0 ) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$product_option_value['price_prefix'] = ($product_option_value['price_prefix'] == '-' ? '-' : '+'); 
							//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_option_value['price']) . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "'");
						}
					}else{
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_option_id = '".$product_option_id."'");
					}
				} else {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '', required = '" . (int)0 . "'");
				}
			}
		}
		
		// specials
		if (isset($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_special['price']) . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_discount['price']) . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->registry->get('cache')->delete('product');
		
		return $product_id;
	}	

	public function editProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$product_id = $data['product_id'];

		/*
		 * thumbnails
		 */
		$old_thumbnail = $this->getProductThumbnail($product_id);
		$old_images = $this->getProductImages($product_id);
		
		if (isset($data['product_thumbnail'])) {
			$keep_thumbnail = false;
			foreach ($old_images as $old_image) {
				if ($old_image['image'] == $data['product_thumbnail']) {
					$keep_thumbnail = true;
					$thumbnail = $old_image['image']; 
					break;
				}
			}
			
			if (!$keep_thumbnail) {
				if ($old_thumbnail['image'] == $data['product_thumbnail']) {
					$thumbnail = $old_thumbnail['image'];
				} else {
					$this->MsLoader->MsFile->deleteImage($old_thumbnail['image']);
					$thumbnail = $this->MsLoader->MsFile->moveImage($data['product_thumbnail']);				
				}
			}
		} else {
			$this->MsLoader->MsFile->deleteImage($old_thumbnail['image']);
			$thumbnail = '';
		}

        $included_field_sql = '';
        isset($data['product_model']) ? $included_field_sql .= " model = '" . $this->db->escape($data['product_model']) . "',"  : '';
        isset($data['product_sku']) ? $included_field_sql .= " sku = '" . $this->db->escape($data['product_sku']) . "',"  : '';
        isset($data['product_manufacturer_id']) ? $included_field_sql .= " manufacturer_id = '" . (int)$data['product_manufacturer_id'] . "',"  : '';
        isset($data['product_tax_class_id']) ? $included_field_sql .= " tax_class_id = '" . $this->db->escape($data['product_tax_class_id']) . "',"  : '';
        isset($data['product_stock_status_id']) ? $included_field_sql .= " stock_status_id = '" . (int)$data['product_stock_status_id'] . "',"  : '';
        isset($data['product_date_available']) ? $included_field_sql .= " date_available = '" . $this->db->escape($data['product_date_available']) . "',"  : '';

		$sql = "UPDATE " . DB_PREFIX . "product
				SET" . $included_field_sql . " price = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($data['product_price']) . ",
					status = " . (int)$data['enabled'] . ",
					image = '" . $this->db->escape($thumbnail) . "',
					subtract = " . (int)$data['product_subtract'] . ",
					quantity = " . (int)$data['product_quantity'] . ",
					shipping = " . (int)$data['product_enable_shipping'] . ",
					date_modified = NOW()
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);

		// this needs to be here
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = " . (int)$product_id);
		
		/*
		 * languages
		 */
		foreach ($data['languages'] as $language_id => $language) {
            $included_field_sql = '';
            isset($language['product_meta_description']) ? $included_field_sql .= " meta_description = '". $this->db->escape(htmlspecialchars(nl2br($language['product_meta_description']), ENT_COMPAT)) ."',"  : '';
            isset($language['product_meta_keyword']) ? $included_field_sql .= " meta_keyword = '". $this->db->escape(htmlspecialchars(nl2br($language['product_meta_keyword']), ENT_COMPAT)) ."',"  : '';

			$sql = "UPDATE " . DB_PREFIX . "product_description
					SET" . $included_field_sql . " name = '". $this->db->escape($language['product_name']) ."',
						description = '". $this->db->escape($language['product_description']) ."',
						tag = '". $this->db->escape($language['product_tags']) ."'
					WHERE product_id = " . (int)$product_id . "
					AND language_id = " . (int)$language_id;
					
			$this->db->query($sql);
			
		}
		
		$sql = "DELETE FROM " . DB_PREFIX . "product_to_category
				WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);

		foreach ($data['product_category'] as $id => $category_id) {
			$sql = "INSERT INTO " . DB_PREFIX . "product_to_category
					SET product_id = " . (int)$product_id . ",
						category_id = " . (int)$category_id;
			$this->db->query($sql);	
		}

		/*
		 * images
		 */
		if (isset($data['product_images'])) {
			
			$new_images = $data['product_images'];
			
			foreach($old_images as $k => $old_image) {
				$key = array_search($old_image['image'], $data['product_images']);
				if ($key !== FALSE) {
					unset($old_images[$k]);
					unset($data['product_images'][$key]);
				}
			}
			
			foreach ($data['product_images'] as $key => $product_image) {
				$newImagePath = $this->MsLoader->MsFile->moveImage($product_image);
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)array_search($product_image, $new_images) . "'");
			}
			
			$i = 0;
			foreach ($new_images as $key => $image) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_image SET sort_order = " . $i++ . " WHERE product_id = '" . (int)$product_id . "' AND image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "'");
			}
		}

		foreach($old_images as $old_image) {
			if ($old_image['image'] != $thumbnail) {
				$this->MsLoader->MsFile->deleteImage($old_image['image']);
			}
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' AND product_image_id = '" . (int)$old_image['product_image_id'] . "'");
		}

		/*
		 * downloads
		 */
		$old_downloads = $this->getProductDownloads($product_id);
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				if (!empty($dl['download_id'])) {
					if (!empty($dl['filename'])) {
						// update download #download_id:
						$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
						$fileMask = substr($newFile,0,strrpos($newFile,'.'));
						
						$this->db->query("UPDATE " . DB_PREFIX . "download SET filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "'");
						
						foreach ($data['languages'] as $language_id => $language) {
							$this->db->query("UPDATE " . DB_PREFIX . "download_description SET name = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "' AND language_id = '" . (int)$language_id . "'");
						}
						
						$this->MsLoader->MsFile->deleteDownload($old_downloads[$dl['download_id']]['filename']);
					} else {
						// do nothing
					}
					
					// don't remove the download
					unset($old_downloads[$dl['download_id']]);
				} else if (!empty($dl['filename'])) {
					// add new download
					$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
					$fileMask = substr($newFile,0,strrpos($newFile,'.'));					
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "download SET filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
					$download_id = $this->db->getLastId();
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
					
					foreach ($data['languages'] as $language_id => $language) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
					}
				}
			}
		}

		if (!empty($old_downloads)) {
			foreach($old_downloads as $old_download) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->MsLoader->MsFile->deleteDownload($old_download['filename']);
			}
		}

		// options
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");		

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				// unset sample
				if (isset($product_option['product_option_value'][0])) unset($product_option['product_option_value'][0]);
		
				// get type
				$o = $this->MsLoader->MsOption->getOptions(array('option_id' => $product_option['option_id'], 'single' => 1));
				if (!$o) continue; else { $product_option['type'] = $o['type'];
				}
		
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
		
					$product_option_id = $this->db->getLastId();
		
					if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0 ) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$product_option_value['price_prefix'] = ($product_option_value['price_prefix'] == '-' ? '-' : '+');
							//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "'");
						}
					}else{
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_option_id = '".$product_option_id."'");
					}
				} else {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '', required = '" . (int)0 . "'");
				}
			}
		}		
		
		// specials
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		if (isset($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_special['price']) . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}		
		
		// discounts
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		if (isset($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_discount['price']) . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}		
		
		$this->registry->get('cache')->delete('product');
		
		return $product_id;
	}
	
	public function hasDownload($product_id, $download_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "product_to_download`
				WHERE product_id = " . (int)$product_id . " 
				AND download_id = " . (int)$download_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];			
	}
	
	public function getDownload($download_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");
		
		return $query->row;
	}
	
	public function productOwnedBySeller($product_id, $seller_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_product`
				WHERE seller_id = " . (int)$seller_id . " 
				AND product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];			
	}
	
	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product WHERE product_id = '" . (int)$product_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
		
		$this->registry->get('cache')->delete('product');	

		//Delete SOLR
        if(SOLR_ENABLED && SOLR_WSBOX_ENABLED){
			//$this->load->model('solr/product');
			$solr = new SolrProduct($this);
			$solr->deleteProductFromSolr($product_id);
		}	
	}
	
	/*****************************************/
	
	public function getTotalProducts($data) { 
		$sql = "
			SELECT COUNT(*) as total
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "ms_product mp
				USING (product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store ps
				USING (product_id)
			LEFT JOIN " . DB_PREFIX . "ms_seller ms
				USING (seller_id)
			WHERE 1 = 1 "
			. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['store_id']) ? " AND ps.store_id =  " .  (int)$data['store_id'] : '')
			. (isset($data['is_single']) ? " AND p.is_single =  " .  (int)$data['is_single'] : '')
			. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
			. (isset($data['enabled']) ? " AND status =  " .  (int)$data['enabled'] : '');
			//echo $sql; exit;
		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	//todo
	public function getProduct($product_id) {
		$sql = "SELECT 	p.price,
		                p.model, p.sku,
		                p.manufacturer_id, p.tax_class_id, p.subtract, p.stock_status_id, p.date_available,
						p.product_id as 'product_id',
						p.status as enabled,
						p.image as thumbnail,
						p.shipping as shipping,
						p.quantity as quantity
				FROM `" . DB_PREFIX . "product` p
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON p.product_id = mp.product_id
				WHERE p.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);

		if (!$res->num_rows) return FALSE;

		$sql = "SELECT pd.*,
					   pd.description as 'pd.description'
				FROM " . DB_PREFIX . "product_description pd
				WHERE pd.product_id = " . (int)$product_id . "
				GROUP BY language_id";

		$descriptions = $this->db->query($sql);
		$product_description_data = array();
		foreach ($descriptions->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'tags'      => $result['tag'],
				'meta_keyword'     => $result['meta_keyword'],
				'meta_description' => $result['meta_description']
			);
		}

		$res->row['languages'] = $product_description_data;
		return $res->row;
	}	
	
	public function getProducts($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';

		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_created`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}
		
		// todo validate order parameters
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS "
					// additional columns
					."p.product_id as 'product_id',
					p.minimum as 'p.minimum',
					p.quantity as 'p.quantity',
					p.image as 'p.image',
					p.price as 'p.price',
					p.sku as 'p.sku',
					pd.name as 'pd.name',
					ms.seller_id as 'seller_id',
					ms.nickname as 'ms.nickname',
					p.date_added as 'p.date_created',
					p.date_modified  as 'p.date_modified',
					pd.set_description as 'pd.set_description',
					pd.description as 'pd.description',
				   (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS 'p.stock_status'
				FROM " . DB_PREFIX . "product p
				INNER JOIN " . DB_PREFIX . "product_description pd
					USING(product_id)
				LEFT JOIN " . DB_PREFIX . "ms_product mp
					USING(product_id)
				LEFT JOIN " . DB_PREFIX . "ms_seller ms
					USING (seller_id)
				WHERE 1 = 1"

				. (isset($data['seller_id']) ? " AND ms.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')				
				. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
				
				. $wFilters
				
				. " GROUP BY p.product_id HAVING 1 = 1 "
				
				. $hFilters
				
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');
		//echo $sql; exit;
		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return $res->rows;
	}

    public function getSellerProducts($data = array(), $sort = array(), $cols = array()) {

		 $sql = "SELECT p.product_id,
                        p.seller_tax, 
                        p.status, 
                        p.model, 
                        p.price as 'p.price', 
                        p.quantity as 'p.quantity', 
                        p.image as 'p.image', 
                        p.sku as 'p.sku', 
                        p.piece_in_set as 'p.piece_in_set', 
                        ptc.category_id as 'category_id', 
                        pd.name as 'pd.name', 
                        pd.set_description as 'pd.set_description', 
                        pd.description as 'pd.description', 
                        ps.store_id as 'ps.store_id', 
                        mp.seller_id as 'seller_id', 
                        p.is_single as 'single_product', 
                        p.stock_status_id 
                 FROM " . DB_PREFIX . "ms_product mp
 				 INNER JOIN " . DB_PREFIX . "product p USING(product_id)
 				 INNER JOIN " . DB_PREFIX . "product_description pd USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_to_store ps USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_to_category ptc USING(product_id)
 				 WHERE (ps.store_id != " . SOR_STORE_ID . " OR ps.store_id IS NULL)"

			. (isset($data['seller_id']) ? " AND mp.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')
			. (isset($data['store_id']) ? " AND ps.store_id =  " .  (int)$data['store_id'] : '')
			. (isset($data['is_single']) ? " AND p.is_single =  " .  (int)$data['is_single'] : '')
			. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
			. (isset($sort['filters']['p.category_id']) ? " AND ptc.category_id =  " .  $sort['filters']['p.category_id'] : '')
			. (isset($sort['filters']['p.low_price']) ? " AND p.price >=  " .  $sort['filters']['p.low_price'] : '')
			. (isset($sort['filters']['p.price']) ? " AND p.price <=  " .  $sort['filters']['p.price'] : '')
			. (isset($sort['filters']['p.sku']) ? " AND p.sku LIKE '%" . $sort['filters']['p.sku'] . "%'" : '') 
            
            . " GROUP BY p.product_id "

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');
		
        $res = $this->db->query($sql);
        
        $wholesale_products = array();
        foreach ($res->rows as $product) {
            
            // Getting SOR product ID (if any)
            $sor_query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store ps USING(product_id) 
                                           WHERE p.model LIKE '" . ($product['model'].'-SOR') . "' 
                                             AND p.sku LIKE '" . $product['p.sku'] . "' 
                                             AND ps.store_id = " . SOR_STORE_ID);
            if ($sor_query->rows) {
                $product = array_merge($product, array('sor_product_id' => $sor_query->row['product_id']));
            } else {
                $product = array_merge($product, array('sor_product_id' => '')); 
            }
            
            // Getting product Status
            $status_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "stock_status 
                                              WHERE stock_status_id = '" . (int)$product['stock_status_id'] . "' AND language_id = '1'");
            if ($status_query->rows) {
                $product = array_merge($product, array('p.stock_status' => $status_query->row['name']));
            } else {
                $product = array_merge($product, array('p.stock_status' => ''));
            }
            
        	$store_info = array();
        	if ($stores = $this->getSellerStores((int)$data['seller_id'])) {
        		foreach ($stores as $store) {
        			$store_info[] = array_merge($this->getStore($store['store_id']), 
        				array('store_price'=> $this->getPriceForStore($store['store_id'], $product['product_id']) ));
        		}
        	}
        	$wholesale_products[] = array_merge($product, array('store_info' => $store_info));
        }

        return $wholesale_products;
    }
	public function getTotalSellerProducts($data = array(), $sort = array(), $cols = array()) {

        $sql = "SELECT count(DISTINCT p.product_id) as total 
                 FROM " . DB_PREFIX . "ms_product mp
 				 INNER JOIN " . DB_PREFIX . "product p USING(product_id)
 				 INNER JOIN " . DB_PREFIX . "product_description pd USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_to_store ps USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_to_category ptc USING(product_id)
 				 WHERE (ps.store_id != " . SOR_STORE_ID . " OR ps.store_id IS NULL)"

			. (isset($data['seller_id']) ? " AND mp.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')
			. (isset($data['store_id']) ? " AND ps.store_id =  " .  (int)$data['store_id'] : '')
			. (isset($data['is_single']) ? " AND p.is_single =  " .  (int)$data['is_single'] : '')
			. (isset($sort['filters']['p.category_id']) ? " AND ptc.category_id =  " .  $sort['filters']['p.category_id'] : '')
			. (isset($sort['filters']['p.low_price']) ? " AND p.price >=  " .  $sort['filters']['p.low_price'] : '')
			. (isset($sort['filters']['p.price']) ? " AND p.price <=  " .  $sort['filters']['p.price'] : '')
			. (isset($sort['filters']['p.sku']) ? " AND p.sku LIKE '%" . $sort['filters']['p.sku'] . "%'" : '');
		
        $res = $this->db->query($sql);

		return $res->row['total'];
	}

	public function changeStatus($product_id, $product_status) {
		if ($product_status == MsProduct::STATUS_ACTIVE)
			$enabled = 1;
		else
			$enabled = 0;
		
		$sql = "UPDATE " . DB_PREFIX . "product
				SET status = " . (int)$enabled . " WHERE product_id = " . (int)$product_id;

		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');

 		$sql = "SELECT name FROM " . DB_PREFIX . "product_status WHERE product_status_id = '" . (int)$product_status . "'";
 		$ps = $this->db->query($sql);
 		$p_status = $ps->row['name'];
		
	}
	
	public function createRecord($product_id, $data = array()) {
		
		$sql = "INSERT IGNORE INTO " . DB_PREFIX . "ms_product
				SET
					product_id =  " . (int)$product_id . "	
				";
		$sql .= (isset($data['seller_id']) ? ", seller_id =  " .  (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);
	}
	
	public function changeSeller($product_id, $seller_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	seller_id =  " . (int)$seller_id . "
				WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');
	}

    public function getManufacturers($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
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

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
    /**
     * This functions gets the products with options and retuns the results
     * used here for single products for sellers
     * @author Parth Gupta
     * @dateTime 2016-01-06T13:13:20+0530
     * @param    array $data [description]
     * @param    array $sort [description]
     * @param    array $cols [description]
     * @return   array array of products with options
     */
    public function getSellerProductsWithOptions($data = array(), $sort = array(), $cols = array()){

        // todo validate order parameters

		 $sql = " SELECT  p.status, p.price as 'p.price', p.product_id as 'product_id', 
				 p.quantity as 'p.quantity', 
				 p.image as 'p.image', 
				 p.sku as 'p.sku', 
				 p.piece_in_set as 'p.piece_in_set', 
				 pd.name as 'pd.name', 
				 pd.set_description as 'pd.set_description', 
				 ps.store_id as 'ps.store_id',
				 mp.seller_id as 'seller_id' ,
				 p.is_single as 'single_product',
 				 (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '1') AS 'p.stock_status'
 				 FROM " . DB_PREFIX . "ms_product mp
 				 INNER JOIN " . DB_PREFIX . "product p USING(product_id)
 				 INNER JOIN " . DB_PREFIX . "product_description pd USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_to_store ps USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_option_value pov USING(product_id)
 				 INNER JOIN " . DB_PREFIX . "product_to_category ptc USING(product_id) 
 				 WHERE (ps.store_id != " . SOR_STORE_ID . " OR ps.store_id IS NULL)"

			. (isset($data['seller_id']) ? " AND mp.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')
			. (isset($data['store_id']) ? " AND ps.store_id =  " .  (int)$data['store_id'] : '')
			. (isset($data['is_single']) ? " AND p.is_single =  " .  (int)$data['is_single'] : '')
			. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')

			. (isset($sort['filters']['p.sku']) ? " AND p.sku LIKE '%" . $sort['filters']['p.sku'] . "%'" : '')
			. (isset($sort['filters']['p.category_id']) ? " AND ptc.category_id =  " .  $sort['filters']['p.category_id'] : '')
			. (isset($sort['filters']['p.low_price']) ? " AND p.price >=  " .  $sort['filters']['p.low_price'] : '')
			. (isset($sort['filters']['p.price']) ? " AND p.price <=  " .  $sort['filters']['p.price'] : '')
			
			. " GROUP BY p.product_id"

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');
		;

		//echo $sql; exit;
		//
       $res = $this->db->query($sql);

		//echo "<pre>"; print_r($res->rows); exit;
       foreach ($res->rows as $key => $value) {			
       	$sql1="
			SELECT ovd.name, ovd.option_id,pov.product_id,
			(SELECT od.name FROM oc_option_description od WHERE od.option_id = ovd.option_id) as 'od.name', pov.product_option_value_id, pov.quantity, pov.price
			FROM
			oc_option_value_description ovd
			INNER JOIN
			oc_product_option_value pov
			ON (ovd.option_value_id = pov.option_value_id)
			INNER JOIN
			oc_option_description od
			on od.option_id = ovd.option_id
			WHERE pov.product_id ="."'". $value['product_id']."'".
			"AND ovd.option_id IN (SELECT po.option_id FROM ".DB_PREFIX.
			"product_option po WHERE po.product_id="."'" . $value['product_id'] . "'".")";
       	
       	$new_res = $this->db->query($sql1);
       	//echo "<br>";echo $sql1;die;
       	
       		$size_arr = array();
       		$color_arr = array();
       		$store_info =array();
       		if(!empty($new_res->rows)){
				foreach ($new_res->rows as $key1 => $value1) {
					if ($value1['od.name'] = 'Size' && $value1['product_id'] == $value['product_id'] ) {
						$size_arr[] = array('size' =>$value1['name'], 'value_id'=> $value1['product_option_value_id'],'quantity'=> $value1['quantity'],'price'=> $value1['price'],);
					}
					else 
						if ($value1['od.name'] = 'Color' && $value1['product_id'] == $value['product_id'] ) {
							$color_arr[] = $value1['name'];
						}
				
				$res->rows[$key]['sizes'] = $size_arr;//(!empty($size_arr)) ? $size_arr : '' ;
				$res->rows[$key]['colors'] = $color_arr;//(!empty($color_arr)) ? $color_arr : '' ;
				
				}
			}else{
				$res->rows[$key]['sizes'] = $size_arr;
				$res->rows[$key]['colors'] = $color_arr;
			}


			
			if ($stores = $this->getSellerStores((int)$data['seller_id'])) {
				foreach ($stores as $store) {
					$store_info[] = array_merge($this->getStore($store['store_id']), 
						array('store_price'=> 
							$this->getPriceForStore($store['store_id'], $value['product_id']) 
							)
						);
					// $store_info['info'] = $this->getStore($store['store_id'])
					// $store_info['price'] = $this->getPriceForStore($store['store_id'], $value['product_id'])

				}
			}
			$res->rows[$key]['store_info']	= 	$store_info;


       }
        $total = $this->db->query("SELECT FOUND_ROWS() as total");
        // if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

        return $res->rows;
    }
    /**
     * get stores of seller
     * @author Parth Gupta
     * @dateTime 2016-04-11T14:49:03+0530
     * @param    seller_id  
     * @return   array stores
     */
    public function getSellerStores($seller_id){
    	$this->load->model('setting/store');
    	$stores = $this->db->query("SELECT os.store_id FROM ".DB_PREFIX."setting os
    	    WHERE os.code = 'config' 
    	    AND os.key = 'config_seller_id'     
    	    AND os.value = ".$seller_id);

        if ($stores->num_rows) {
            return $stores->rows;
        }
        else{
            return 0;
        }
    }

    public function getPriceForStore($store_id, $product_id){
    	$store_price = $this->db->query("SELECT p2s.store_price AS store_price 
    		FROM ".DB_PREFIX."product_to_store p2s
			WHERE p2s.product_id=".$product_id." 
			AND p2s.store_id=".$store_id);

	    if (!empty($store_price->row)) {
	    	return $store_price->row['store_price'];
	    }else {
	    	return 0;
	    }

    }
    /**
     * get deatails of a store based on store id
     * @author Parth Gupta
     * @dateTime 2016-04-18T14:50:09+0530
     * @param                    $store_id 
     * @return   array store 
     */
    public function getStore($store_id) {

    	$stores_data = $this->cache->get('store');

    	$store_data = array();
    	if (!$stores_data) {
    		$q = "SELECT * FROM " . DB_PREFIX . "store WHERE store_id = " . $store_id;
    		$query = $this->db->query($q);

    		$store_data = $query->row;
    	}else{

    		foreach($stores_data as $store){

    			if($store['store_id'] == $store_id) {
    				$store_data['store_id'] = $store['store_id'];
    				$store_data['name'] = $store['name'];
    				$store_data['url'] = $store['url'];
    				$store_data['ssl'] = $store['ssl'];
    			}
    		}
    	}

    	return $store_data;
    }

    /**
     * Public function to set Images against product Ids
     * @param: Product Ids, array
     * @return: void
     * @author: Nishu, Sept 2017
    */
    public static function setProductImages($this_ob, $pids, &$data, $is_op_id = 1){
        $img_width  = $this_ob->config->get('config_image_additional_width');
        $img_height = $this_ob->config->get('config_image_additional_height');
        
        $images  = array();
        if(!empty($pids)){
           $this_ob->load->model('catalog/product');
           // $images = $this_ob->model_catalog_product->getProductImagesByProductsIds($pids);  

            $images = Product::getOrderProductOptionImages($this_ob->db, $pids, $is_op_id);    
        }
        $data['pid_to_imgs'] = array();
        if(!empty($images)){
         $this_ob->load->model('tool/image');
         foreach ( $images as $op_id => $image) {
            $data['pid_to_imgs'][$op_id] = $this_ob->model_tool_image->resize(
                                                                         $image,
                                                                         $img_width,
                                                                         $img_height
                                                                        );
         }
        }
        $data['image_width']  = $img_width;
        $data['image_height'] = $img_height;
        return $data;
    }

    /**
     * Public function to set Images against product Ids
     * @param: Product Ids, array
     * @return: void
     * @author: Nishu, Sept 2017
    */
    public static function getProductSellerDetails($this_ob, $pids){
    	$data = array();
        
        if(!empty($pids)){
            $sql = "
            		SELECT ms.*, sp.product_id
            		  FROM oc_ms_seller AS ms
            		INNER JOIN oc_ms_product AS sp ON sp.seller_id = ms.seller_id
         			WHERE
         			  product_id IN ('". implode("','",$pids)."')
            	   ";
            //echo $sql;die;
           	$result = $this_ob->db->query($sql);
           	if($result->num_rows > 0){
           		foreach ($result->rows as $key => $value) {
           			if(!isset($data[$value['seller_id']])){
           				$data[$value['seller_id']]           = $value;
           				$data[$value['seller_id']]['pids'][] = $value['product_id'];
           			}else{
           				array_push($data[$value['seller_id']]['pids'], $value['product_id']);
           			}
           		}
           	}
        }
        return $data;
    }
}
?>