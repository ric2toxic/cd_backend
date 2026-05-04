<?php
class ModelSellerManageInventory extends Model {

	public function GetProductIsSingle($model){
		$sql ="SELECT product_id, model FROM ".DB_PREFIX."product WHERE model = '".$this->db->escape($model.'-SNGL')."'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	public function GetProductSize($product_id){
		$sql1="SELECT ovd.name, pov.product_id, pov.product_option_value_id, pov.quantity
			FROM
			oc_option_value_description ovd
			INNER JOIN
			oc_product_option_value pov
			ON (ovd.option_value_id = pov.option_value_id)
			WHERE pov.product_id ='". (int)$product_id."' AND ovd.language_id = 1";
		$new_res = $this->db->query($sql1);
		return $new_res->rows;
	}

	public function copyProductToSingle($product_id){

		$price_markup = 0;
		$commission = 0;

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p 
		INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
		WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");


		if ($query->num_rows) {
			$data = $query->row;

			$existing_single = $this->checkIfSingleExist($data['mode']);

			if (!$data['is_single'] && !$existing_single) {
				$data['single'] 		= 1;
				$data['sku'] 			= $data['sku'];
				$data['model'] 			= $data['model'].'-SNGL';
				$data['piece_in_set'] 	= 1;
				$data['minimum'] 		= 1;
				$data['commission'] 	= isset($commission)? $commission : $data['commission'];
				$data['price'] 			= ceil($data['price'] * (1 + $price_markup/100));
				$data['viewed'] 		= '0';
				$data['keyword'] 		= $this->setSeoUrl($data['name'], $data['model']);
				$data['status']			= '1';
				$data['sort_order']     = 999;

				$data['product_attribute'] 		= $this->getProductAttributes($product_id);
				$data['product_description'] 	= $this->getProductDescriptions($product_id);
				$data['product_description'][1]['set_description'] = '';
				if (!empty($data['product_option'])) {
					$data['product_option'] = $this->getProductOptions($product_id);
				} else {
					$description_var = $this->getProductDescriptions($product_id);
					if ($description_var[1]['set_description']) {
						preg_match_all('!\d\d!', $description_var[1]['set_description'], $matches);
					}

					$arr_option = array();
					if (isset($matches) && !empty($matches)) {

						$sizes_values = $this->getOptionValues('11');
						$size_chart = array_column($sizes_values, 'option_value_id' , 'name');
						foreach ($matches[0] as $key => $value) {
							if (array_key_exists($value, $size_chart)) {
								$arr_option['product_option_value'][] = array(
									'option_value_id' 		  => $size_chart[$value],
									'quantity'                => $data['quantity'],
									'subtract'                => '1',
									'price'                   => '0',
									'price_prefix'            => '',
									'points'                  => '',
									'points_prefix'           => '',
									'weight'                  => '0',
									'weight_prefix'           => ''
								);
							}
						}
						$arr_option['type'] = 'select';
						$arr_option['option_id'] = '11';
						$arr_option['required'] = '1';
						$arr_option['name'] = 'Size';
					}
					$data['product_option'] 		= array($arr_option);
					$temp_filters					= $this->getProductFilters($product_id);

					if(($key = array_search(62, $temp_filters)) !== false) {
						unset($temp_filters[$key]);
					} elseif (($key = array_search(63, $temp_filters)) !== false) {
						unset($temp_filters[$key]);
					}
					$data['product_discount']	 	= '';
					$data['product_filter'] 		= $temp_filters;
					$data['product_image'] 			= $this->getProductImages($product_id);
					$data['product_related'] 		= $this->getProductRelated($product_id);
					$data['product_special'] 		= $this->getProductSpecials($product_id);
					$data['product_category'] 		= $this->getProductCategories($product_id);
					$data['product_download'] 		= $this->getProductDownloads($product_id);
					$data['product_layout'] 		= $this->getProductLayouts($product_id);
					$data['product_store'] 			= $this->getProductStores($product_id);
					$data['product_recurrings'] 	= $this->getRecurrings($product_id);
					if ($new_product_id = $this->addProduct($data)) {
						$new_seller_id = $this->MsLoader->MsProduct->getSellerId($product_id);
						$sql_seller = "INSERT INTO ".DB_PREFIX."ms_product 
		                                SET
							               product_id = '".(int)$new_product_id."',
		                                   seller_id  = '".(int)$new_seller_id."' 
		                                ";
						$this->db->query($sql_seller);
					};
				}
			}
		}
	}

	public function setSeoUrl($form_name, $form_model){

		$name_after_apostrophe = str_replace("'","",trim($form_name));
		$name_after_ampersand = str_replace("amp","",trim($name_after_apostrophe));
		$name_after_regex = preg_replace('/[^a-zA-Z0-9- \n]/', "", trim($name_after_ampersand));
		$name = strtolower(implode("-",preg_split('/[\s]+/', trim($name_after_regex))));

		$model_after_apostrophe = str_replace("'","",trim($form_model));
		$model_after_ampersand = str_replace("amp","",trim($model_after_apostrophe));
		$model_after_regex = preg_replace('/[^a-zA-Z0-9- \n]/', "", trim($model_after_ampersand));
		$model = strtolower(implode("-",preg_split('/[\s]+/', trim($model_after_regex))));

		$seo_line = $name."-".$model ;
		return $seo_line;
	}
	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}
	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'set_description' => $result['set_description'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}
	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po INNER JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) INNER JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}
	public function getOptionValues($option_id) {
		$option_value_data = array();

		$option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int)$option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order, ovd.name");

		foreach ($option_value_query->rows as $option_value) {
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'name'            => $option_value['name'],
				'image'           => $option_value['image'],
				'sort_order'      => $option_value['sort_order']
			);
		}

		return $option_value_data;
	}
	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}
	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}
	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}
	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}
	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}
	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}
	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}
	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}
	public function addProduct($data) {

        $this->event->trigger('pre.admin.product.add', $data);
        
        $seller_tax_factor = 1.0 + ( (float)$data['seller_tax'] / 100.0 );
        $commission_factor = 1;
        $selling_price = ceil($commission_factor * (float)($data['price']) / $seller_tax_factor);

        $this->db->query("INSERT INTO " . DB_PREFIX . "product 
                          SET model = '" . $this->db->escape(trim($data['model'])) . "', 
                              sku = '" . $this->db->escape(trim($data['sku'])) . "', 
                              location = '" . $this->db->escape($data['location']) . "', 
                              quantity = '" . (int)$data['quantity'] . "', 
                              minimum = '" . (int)$data['minimum'] . "', 
                              subtract = '" . (int)$data['subtract'] . "', 
                              stock_status_id = '" . (int)$data['stock_status_id'] . "', 
                              date_available = '" . $this->db->escape($data['date_available']) . "', 
                              manufacturer_id = '" . (int)$data['manufacturer_id'] . "', 
                              shipping = '" . (int)$data['shipping'] . "', 
                              price = '" . (float)$data['price'] . "', 
                              selling_price = '" . (float)$selling_price . "', 
                              price_per_set = '" . (float)((float)$data['price']*(int)$data['piece_in_set']) . "', 
                              piece_in_set = '" . (int)$data['piece_in_set'] . "', 
                              seller_tax = '" . (float)$data['seller_tax'] . "', 
                              commission = '" . (float)$data['commission'] . "', 
                              points = '" . (int)$data['points'] . "', 
                              weight = '" . (float)$data['weight'] . "', 
                              weight_class_id = '" . (int)$data['weight_class_id'] . "', 
                              length = '" . (float)$data['length'] . "', 
                              width = '" . (float)$data['width'] . "', 
                              height = '" . (float)$data['height'] . "', 
                              length_class_id = '" . (int)$data['length_class_id'] . "', 
                              status = '" . (int)$data['status'] . "', 
                              tax_class_id = '" . (int)$data['tax_class_id'] . "', 
                              sort_order = '" . (int)$data['sort_order'] . "', 
                              is_single = '" . (int)$data['single'] . "', 
                              date_added = NOW()");

        $product_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
        }

        foreach ($data['product_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', set_description = '" . $this->db->escape($value['set_description']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        if (isset($data['product_store'])) {
            foreach ($data['product_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

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

        if (isset($data['product_discount']) && !empty($data['product_discount']) ) {
            foreach ($data['product_discount'] as $product_discount) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', store_id = '" . (int)$product_discount['store_id'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
            }
        }

        if (isset($data['product_special']) && count($data['product_special']) > 0 ) {
            foreach ($data['product_special'] as $product_special) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
            }
        }

        if (isset($data['product_image']) && count($data['product_image']) > 0) {
            foreach ($data['product_image'] as $product_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
            }
        }

        if (isset($data['product_download']) && count($data['product_download']) > 0) {
            foreach ($data['product_download'] as $download_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
            }
        }

        if (isset($data['product_category']) && count($data['product_category']) > 0) {
            foreach ($data['product_category'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
            }
        }

        if (isset($data['product_filter']) && count($data['product_filter']) > 0) {
            foreach ($data['product_filter'] as $filter_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
            }
        }

        if (isset($data['product_related']) && count($data['product_related']) > 0) {
            foreach ($data['product_related'] as $related_id) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
            }
        }


        if (isset($data['product_layout']) && count($data['product_layout']) > 0) {
            foreach ($data['product_layout'] as $store_id => $layout_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
            }
        }

        if (isset($data['keyword']) && !empty($data['keyword'])) {
            $seo_keyword = $data['keyword'];
        }
        else {
            $seo_keyword = $this->setSeoUrl( $data['product_description'][1]['name'], $data['model']);
        }
        $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $seo_keyword . "'");



        if (isset($data['product_recurrings']) && count($data['product_recurrings']) > 0) {
            foreach ($data['product_recurrings'] as $recurring) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", `recurring_id` = " . (int)$recurring['recurring_id']);
            }
        }

        $this->cache->delete('product');

        $this->event->trigger('post.admin.product.add', $product_id);

        return $product_id;
    }
    public function showProductInStore($product_id, $store_id, $ischeck) {
    	if($ischeck == 1){
    		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$store_id . "'");
    		$sql = "INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'";
    		if($store_id == 0){
    			$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '". 2 ."' WHERE product_id = '" . (int)$product_id . "'");	
    		}
    		$this->db->query($sql);
    	}else{
    		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$store_id . "'");
    		if($store_id == 0){
    			$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '". 0 ."' WHERE product_id = '" . (int)$product_id . "'");
    		}
    	}

	}
	public function GetStoreProduct($product_id, $store_id) {
		$sql ="SELECT product_id FROM ".DB_PREFIX."product_to_store WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$store_id . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function checkIfSingleExist($model)
	{
		$exsisting_query = $this->db->query("SELECT DISTINCT model FROM " . DB_PREFIX . "product p WHERE model LIKE " . "'" . $this->db->escape($model . '-SNGL') . "'");
		if ($exsisting_query->num_rows) {
			$existing_single = true;
		} else {
			$existing_single = false;
		}

		return $existing_single;
	}
	public function changeStockStatusProducts($product_id, $stock_status){

		$get_model = $this->db->query("SELECT model FROM " . DB_PREFIX . "product WHERE product_id = '". (int)$product_id ."'");
		$model = $get_model->row['model'];
		$sor_model = $model.'-SOR';
		$sor_products = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE model LIKE " . "'" . $this->db->escape($sor_model) . "'");
		$sor_product = $sor_products->row;

		//Update SOLR
		$solr_data = array();
		$solr_data[] = $product_id;
		if(!empty($sor_product) && isset($sor_product)){
			$sor_product_id = $sor_product['product_id'];
			if($stock_status == 'out_stock'){
				$sql ="UPDATE " . DB_PREFIX . "product SET stock_status_id = 5 WHERE product_id = '" . (int)$sor_product_id . "'";
			}
			if($stock_status == 'in_stock'){
				$sql ="UPDATE " . DB_PREFIX . "product SET stock_status_id = 7 WHERE product_id = '" . (int)$sor_product_id . "'";
			}
			$this->db->query($sql);
			$solr_data[] = $sor_product_id;
		}
		if($stock_status == 'out_stock'){
			$sql ="UPDATE " . DB_PREFIX . "product SET stock_status_id = 5 WHERE product_id = '" . (int)$product_id . "'";
		}
		if($stock_status == 'in_stock'){
			$sql ="UPDATE " . DB_PREFIX . "product SET stock_status_id = 7 WHERE product_id = '" . (int)$product_id . "'";
		}
		$this->db->query($sql);

	}


	public function updateVacation($vacation, $seller_id){
		$sql = "UPDATE " . DB_PREFIX . "ms_seller SET vacation_mode = '". (int)$vacation ."' WHERE seller_id = '" . (int)$seller_id . "'";
		$this->db->query($sql);


		//Update SOLR
		$data = array();
		$data['seller_id'] = $seller_id;
		$data['vacation_mode'] = $vacation;
		$type = "vacation_mode";

		//$this->load->model('solr/product');
		// $solr = new SolrProduct($this);
		// $solr->atomicBulkUpdateToSolr($data, $type);
        
        
        // Sending mail to seller
        $seller = $this->getSellerDetail($seller_id);  
        $html = MailTemplate::sellerVacationModeTemplate( $seller, $vacation );
        
        if( $vacation ){
        	$subject = 'Vacation Mode Turn ON';	
        }else{
        	$subject = 'Vacation Mode Turn OFF';	
        }
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
        $mail->addReplyTo($this->config->get('config_email'), 'Wholesale Box');
        $mail->addAddress($seller['email'],$seller['company']);
        // Get list of additional emails
        $add_emails = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_additional_email WHERE customer_id = '" . (int)$seller_id . "'");
        if ($add_emails->rows) {
            foreach ($add_emails->rows as $row) {
                $mail->addAddress($row['email'], $seller['company']);
            }
        }

        $mail->addBCC(EMAIL_IDS['prabhav']['email_id'], EMAIL_IDS['prabhav']['name']);
        $mail->addBCC(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->send();
	}


	public function getVacation($seller_id){
		$query = $this->db->query("SELECT vacation_mode FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
		return $query->row['vacation_mode'];
	}


	public function checkSize($set_color_size){
		$sql = "SELECT filter_id FROM " . DB_PREFIX . "filter_description WHERE name = '" . (int)$set_color_size . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}


	public function CreateFilterSize($set_color_size){
		$sql = "INSERT INTO " . DB_PREFIX . "filter SET filter_group_id = 9, sort_order = 0";
		$query = $this->db->query($sql);
		$filter_id = $this->db->getLastId();

		$sql = "INSERT INTO " . DB_PREFIX . "filter_description 
		        SET filter_id = '" . (int)$filter_id . "', 
		            language_id = 1, 
		            name = '" . (int)$set_color_size . "'";
		$query = $this->db->query($sql);

		$sql = "SELECT filter_id FROM " . DB_PREFIX . "filter_description WHERE name = '" . (int)$set_color_size . "'";
		$query = $this->db->query($sql);
		return $query->row['filter_id'];
	}


	public function InsertProductSize($product_id, $filter_id){
		$sql = "INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'";
		$query = $this->db->query($sql);
	}


	public function UpdateProductSize($product_id, $filter_id, $check_filter_id){
		$sql = "UPDATE " . DB_PREFIX . "product_filter SET filter_id = '" . (int)$filter_id . "', product_id = '" . (int)$product_id . "' WHERE product_id = '" . (int)$product_id . "' AND filter_id = '" . (int)$check_filter_id . "'";
		$query = $this->db->query($sql);
	}


	public function getProductFilterSize($product_id){
		$sql = "
				SELECT 
					f.filter_id, 
					pf.product_id, 
					f.filter_group_id 
					FROM 
						" . DB_PREFIX . "filter f
					INNER JOIN 
						".DB_PREFIX."product_filter pf ON pf.filter_id = f.filter_id 
					WHERE 
						f.filter_group_id = 9 
						AND pf.product_id = '" . (int)$product_id . "'
					";
		$query = $this->db->query($sql);
		return $query->row;
	}


	public function DeleteProductFilterSize($product_id, $filter_id){
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "' AND filter_id = '" . (int)$filter_id . "'");
	}


	public function getSellerDetail($seller_id){
		$query = $this->db->query("SELECT company,email,mobile_no FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
		return $query->row;
	}


	public function GetSorProductDetail($product_id){
		$query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		return $query->row;
	}


	public function showProductSorInStore($product_id, $ischeck){
		if($ischeck == 1){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '". 2 ."' WHERE product_id = '" . (int)$product_id . "'");
		}else{
			$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '". 0 ."' WHERE product_id = '" . (int)$product_id . "'");
		}


	}
	public function getSinglesPrice($product_id){
		$sql = "SELECT price FROM ". DB_PREFIX ."product WHERE product_id = '". (int)$product_id ."'";
		$query = $this->db->query($sql);
		return $query->row['price'];
	}

	public function GetProductCategoriesSlug($product_id){
		$sql = $this->db->query("SELECT c.category_id FROM " . DB_PREFIX . "product_to_category as ptc INNER JOIN oc_category as c ON c.category_id = ptc.category_id WHERE product_id = '" . (int)$product_id . "' ORDER BY c.parent_id DESC");
		$category =  $sql->rows;
		foreach ($category as $value) {
			$slug = $value['category_id'];
			if ($slug == 87){
				$data['category_url'] = 'index.php?route=seller/category_set_description&product_type=yardage&popup=true';
				$data['category_id'] = $slug;
				$data['transferprice'] = 'Price/set';
				return $data;
			}else{
				$data['category_url'] = 'index.php?route=seller/category_set_description&product_type=general&popup=true';
				$data['category_id'] = $slug;
				$data['transferprice'] = 'Price/piece';
				return $data;
			}
		}
	}

	public function InformSellerChangeLog($type='', $new='', $product_id=''){

		$query_1 = "SELECT price, piece_in_set, model FROM ".DB_PREFIX."product WHERE product_id = " . (int)$product_id;
		$peiceandprice = $this->db->query($query_1);
		$price = $peiceandprice->row['price'];
		// print_r($price); die;
		$model = $peiceandprice->row['model'];
		$piece = $peiceandprice->row['piece_in_set'];
		$query = "SELECT set_description, description FROM ".DB_PREFIX."product_description WHERE product_id = " . (int)$product_id;
		$set_des = $this->db->query($query);
		$set_description = $set_des->row['set_description'];
		$description = $set_des->row['description'];
		$model_query = "SELECT seller_id FROM ".DB_PREFIX."ms_product WHERE product_id = ".(int)$product_id;
		$model_1 = $this->db->query($model_query);
		$seller_id = $model_1->row['seller_id'];
		$nick_query = "SELECT nickname FROM ".DB_PREFIX."ms_seller WHERE seller_id = ".(int)$seller_id;
		$query = $this->db->query($nick_query);
		$nickname = $query->row['nickname'];

		if($type == "set_quantity"){

			$oldqualtity = $this->db->query("SELECT quantity FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
			$old = $oldqualtity->row['quantity'];

			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$old','$new',now())";
			$query = $this->db->query($insert1);
		}

		if($type == "piece_per_set"){
			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$piece','$new',now())";
			$query = $this->db->query($insert1);
		}

		if($type == "price"){
			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$price','$new',now())";
			$query = $this->db->query($insert1);
		}
		if($type == "store_price"){
			$data = explode("_", $new);
			$query_1 = "SELECT name FROM ".DB_PREFIX."store WHERE store_id = '$data[0]'";
			$store = $this->db->query($query_1);
			$store_name = $store->row['name'];
			$store_price = $this->db->query("SELECT store_price FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . $data[0] . "'");
			$old_store_price = $store_name . " = " . $store_price->row['store_price'];
			$new_price = $store_name . " = " . $data[1];

			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$old_store_price','$new_price',now())";
			$query = $this->db->query($insert1);
		}

		if($type == "show_on_wholesale"){
			if($new == 0){
				$old = 1;
			}else{
				$old = 0;
			}
			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$old','$new',now())";
			$query = $this->db->query($insert1);
		}
		if($type == "show_on_store"){

			$data = explode("_", $new);
			$query_1 = "SELECT name FROM ".DB_PREFIX."store WHERE store_id = '$data[0]'";
			$store = $this->db->query($query_1);
			$store_name = $store->row['name'];
			if($data[1] == 1){
				$old_store = $store_name . " = " . 0;
				$new_store = $store_name . " = " . 1;
			}else{
				$old_store = $store_name . " = " . 1;
				$new_store = $store_name . " = " . 0;
			}

			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$old_store','$new_store',now())";
			$query = $this->db->query($insert1);
		}
		if($type == "single_quantity"){

			$data = explode("_", $new);
			$query = $this->db->query("SELECT option_value_id FROM `oc_product_option_value` WHERE product_option_value_id= '".$data[0]."'");

			$option_value_id = $query->row['option_value_id'];
			$option_value = $this->db->query("SELECT name FROM `oc_option_value_description` WHERE option_value_id= '". (int)$option_value_id ."'");
			$option_size = $option_value->row['name'];
			$new = "size " . $option_size . " = " . $data[1] . " qty.";

			$sql1="SELECT pov.quantity
            FROM
            oc_option_value_description ovd
            INNER JOIN
            oc_product_option_value pov
            ON (ovd.option_value_id = pov.option_value_id)
            INNER JOIN
            oc_option_description od
            on od.option_id = ovd.option_id
            WHERE pov.product_id ='". $product_id."' AND ovd.name = '". $option_size ."' AND ovd.option_id IN (SELECT po.option_id FROM ".DB_PREFIX.
				"product_option po WHERE po.product_id='" . $product_id . "')";
			$new_res = $this->db->query($sql1);
			$quantity = $new_res->row['quantity'];

			$old = "size " . $option_size . " = " . $quantity . " qty.";
			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$old','$new',now())";
			$query = $this->db->query($insert1);
		}


		if($type == "set_description"){
			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$set_description','$new',now())";
			$query = $this->db->query($insert1);
		}

		if($type == "description"){
			// echo $new; die;
			$insert1 = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$description','$new',now())";
			$query = $this->db->query($insert1);
		}
		if($type == "Stock_status"){
			$query = $this->db->query("SELECT ss.name as name FROM `oc_stock_status` as ss INNER JOIN `oc_product` as p ON p.stock_status_id = ss.stock_status_id WHERE product_id= '".$product_id."'");
			$old_status = $query->row['name'];
			$sql = "INSERT INTO ".DB_PREFIX."seller_change_log(`seller_id`, `product_id`, `product`, `nickname`, `updated_type`, `old`, `new`, `modified`) VALUES ('$seller_id','$product_id','$model','$nickname','$type','$old_status','$new',now())";
			$query = $this->db->query($sql);

		}

		/*
         * to update product date_modified when the product is modified
         * */
		$sql_product = "UPDATE " . DB_PREFIX . "product SET `date_modified`= now() WHERE product_id = '" . (int)$product_id . "'";
		$this->db->query($sql_product);
	}

	public function updateProductDescription($des, $type, $product_id, $sor_product_id){
		$des = $des;
		$product_id = $product_id;
		$type = $type;
		$new = $des;
		$this->InformSellerChangeLog($type, $new, $product_id);
		$sql = "UPDATE `" . DB_PREFIX . "product_description` SET `description`= '$des' WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);
		$sql = "UPDATE `" . DB_PREFIX . "product` SET status= '2' WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);

		//Update SOLR
		$solr_data = array();
		$solr_data[] = $product_id;

		if(!empty($sor_product_id) && isset($sor_product_id)){
			$sor_product_id = $sor_product_id;
			$sql = "UPDATE `" . DB_PREFIX . "product_description` SET `description`= '$des' WHERE product_id = ".(int)$sor_product_id;
			$this->db->query($sql);
			$sql = "UPDATE `" . DB_PREFIX . "product` SET status= '2' WHERE product_id = ".(int)$sor_product_id;
			$this->db->query($sql);

			$solr_data[] = (int)$sor_product_id;
		}

	}

	public function UpdateQuantity($qn, $product_model, $product_id, $sor_product_id){
		$qn = $qn;
		$product_model = $product_model;
		$product_id = $product_id;
		$product_sngl_model = $product_model.'-SNGL';

		$product_sngl_ids = $this->db->query("SELECT product_id 
                                              FROM " . DB_PREFIX . "product 
                                              WHERE model = '" . $this->db->escape($product_sngl_model) . "'");
		$product_sngl_row_id = $product_sngl_ids->row;

		//Update SOLR
		$solr_data = array();
		$solr_data[] = $product_id;

		if(!empty($product_sngl_row_id)){
			$product_sngl_id = $product_sngl_row_id['product_id'];
			$this->db->query("UPDATE " . DB_PREFIX . "product_option_value 
			                  Set quantity = '" . (int)$qn . "' 
			                  WHERE product_id = '" . (int)$product_sngl_id . "'");

			$quantity = $this->db->query("SELECT quantity FROM ".DB_PREFIX. "product_option_value 
			                              WHERE product_id =" ."'". (int)$product_sngl_id."'");
			$qty = $quantity->rows;
			$temp = 0;
			foreach($qty as $qt){
				$temp = $temp + $qt['quantity'];
			}
			$sql = "UPDATE `" . DB_PREFIX . "product` SET `quantity`= '".(int)$temp."' WHERE product_id = '".(int)$product_sngl_id."'";
			$this->db->query($sql);

			$solr_data[] = (int)$product_sngl_id;
		}
		if(!empty($sor_product_id) && isset($sor_product_id)){
			$product_sor_id = $sor_product_id;
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `quantity`= '".(int)$qn."' WHERE product_id = '".(int)$product_sor_id."'");

			$solr_data[] = (int)$product_sor_id;
		}

		$this->InformSellerChangeLog("set_quantity", $qn, $product_id);

		$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = ".(int)$qn." WHERE product_id = '" . (int)$product_id . "'");

		
    }

    public function UpdatePriceField($field, $fv, $product_id){
    	$field = $field;
    	$fv = $fv;
    	$product_id = $product_id;
    	$commission_limit = 10;
    	$record = $this->db->query("SELECT price, seller_tax, commission, piece_in_set 
                                    FROM ".DB_PREFIX."product WHERE product_id = '" . (int)$product_id . "'");
            
        $seller_tax_factor = 1.0 + ( (float)$record->row['seller_tax'] / 100.0 );
        
        /* Evaluating new commission based on the price change */
        $commission = (float)($record->row['commission']);
        if ( $commission < $commission_limit ) {
            $old_commission_factor = 1.0 + ($commission / 100.0);
            $old_selling_price = ceil( $old_commission_factor * (float)$record->row['price'] / $seller_tax_factor );
            $new_selling_price = ceil( $old_commission_factor * $fv / $seller_tax_factor);
            
            if ( $new_selling_price < $old_selling_price ) {
                $expected_selling_price = $old_selling_price - (0.8 * ($old_selling_price - $new_selling_price)); // Taking 20% benefit
                $new_commission = (($expected_selling_price*$seller_tax_factor/$fv) - 1)*100;
                
                if ($new_commission > $commission and $new_commission < $commission_limit)
                    $commission = $new_commission;
            }
        } /* evaluation new commission */
                    
        $piece_in_set = $record->row['piece_in_set'];
        $price_per_set = $fv * $piece_in_set;
        
        $commission_factor = 1.0 + ($commission / 100.0);
        $selling_price = ceil($commission_factor * $fv / $seller_tax_factor);
        $product_id = $product_id;
        $new = $fv;
        $type = $field;
        $this->InformSellerChangeLog($type, $new, $product_id);
        if (is_numeric($fv)) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET ".$field." = '".$this->db->escape($fv)."', price_per_set = ".(float)$price_per_set.", selling_price = " . (float)$selling_price . ", commission = " . (float)$commission . " WHERE product_id = '" . (int)$product_id . "'");
        }
	}

	public function UpdatePieceInSet($pis, $product_id, $sor_product_id){

		$pis = $pis;
		$product_id = $product_id;
		$sor_product_id = $sor_product_id;

		//Update SOLR
		$solr_data = array();
		$solr_data[] = $product_id;

		$record = $this->db->query("SELECT price FROM ".DB_PREFIX."product WHERE product_id = '" . (int)$product_id . "'");
		$price = $record->row['price'];
		$price_per_set = $price * $pis;
		// Sor Product Price
		if(!empty($sor_product_id) && isset($sor_product_id)){
			$query = $this->db->query("SELECT price FROM ".DB_PREFIX."product WHERE product_id = '" . (int)$sor_product_id . "'");
			$sor_price = $query->row['price'];
			$sor_price_per_set = $sor_price * $pis;
			$this->db->query("UPDATE " . DB_PREFIX . "product SET piece_in_set = ".(int)$pis.",price_per_set = ".(float)$sor_price_per_set." WHERE product_id = '" . (int)$sor_product_id . "'");

			$solr_data[] = $sor_product_id;
		}
		// echo"<pre>";print_r($record);exit();
		$type = "piece_per_set";
		$this->InformSellerChangeLog($type, $pis, $product_id);
		$this->db->query("UPDATE " . DB_PREFIX . "product SET piece_in_set = ".(int)$pis.", 
		                  price_per_set = ".(float)$price_per_set." WHERE product_id = '" . (int)$product_id . "'");

	}

	public function UpdatePDField($field, $fv, $product_id, $sor_product_id){
		$new = $fv;
		$type = $field;
		$this->InformSellerChangeLog($type, $new, $product_id);
		$this->db->query("UPDATE " . DB_PREFIX . "product_description SET ".$field." = '".$this->db->escape($fv)."'  
		                  WHERE product_id = '" . (int)$product_id . "'");

		//Update SOLR
		$solr_data = array();
		$solr_data[] = $product_id;

		if(!empty($sor_product_id) && isset($sor_product_id)){
			$sor_product_id = $sor_product_id;
			$this->db->query("UPDATE " . DB_PREFIX . "product_description SET ".$field." = '".$this->db->escape($fv)."' 
			                  WHERE product_id = '" . (int)$sor_product_id . "'");
			$solr_data[] = $sor_product_id;
		}

	}

	public function getTotalSellerProducts($data = array(), $sort = array(), $cols = array(), $flag = '') {

		$sql = "SELECT count(DISTINCT p.product_id) as total
                 FROM " . DB_PREFIX . "ms_product mp
 				 INNER JOIN " . DB_PREFIX . "product p USING(product_id)
 				 INNER JOIN " . DB_PREFIX . "product_description pd USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_to_store ps USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_to_category ptc USING(product_id)
 				 WHERE (ps.store_id != " . SOR_STORE_ID . " OR ps.store_id IS NULL)"
			. (isset($flag) && $flag == 1 ? " AND p.is_archived = ". '1' : " AND p.is_archived = ". '0')
			. (isset($data['seller_id']) ? " AND mp.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')
			. (isset($data['store_id']) ? " AND ps.store_id =  " .  (int)$data['store_id'] : '')
			. (isset($data['is_single']) ? " AND p.is_single =  " .  (int)$data['is_single'] : '')
			. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
			. (isset($sort['filters']['p.category_id']) ? " AND ptc.category_id =  " .  (int)$sort['filters']['p.category_id'] : '')
			. (isset($sort['filters']['p.low_price']) ? " AND p.price >=  " .  (float)$sort['filters']['p.low_price'] : '')
			. (isset($sort['filters']['p.price']) ? " AND p.price <=  " .  (float)$sort['filters']['p.price'] : '')
			. (isset($sort['filters']['p.sku']) ? " AND p.sku LIKE '%" . $this->db->escape($sort['filters']['p.sku']) . "%'" : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	

	public function getSellerProducts($data = array(), $sort = array(), $cols = array(), $flag = ''){

		$sql = "SELECT p.product_id,
                        p.seller_tax,
                        p.status,
                        p.model,
                        p.price as 'p.price',
                        p.quantity as 'p.quantity',
                        IF (p.quantity = 0, NULL, p.quantity) as quantity_sort,
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
                        pps.price as 'special',
                        pps.discount_type as 'discount_type',
                        pps.discount_value as 'discount_value',
                        pps.date_start as 'offer_date_start',
                        pps.date_end as 'offer_date_end',
                        p.stock_status_id,
                        p.store_sales
                FROM " . DB_PREFIX . "ms_product mp
 				INNER JOIN " . DB_PREFIX . "product p USING(product_id)
 				INNER JOIN " . DB_PREFIX . "product_description pd USING(product_id)
 				LEFT JOIN " . DB_PREFIX . "product_to_store ps USING(product_id)
 				LEFT JOIN " . DB_PREFIX . "product_to_category ptc USING(product_id)
 				LEFT JOIN " . DB_PREFIX . "product_special pps USING(product_id)
 				WHERE (ps.store_id != " . SOR_STORE_ID . " OR ps.store_id IS NULL)"
 				
			. (isset($flag) && $flag == 1 ? " AND p.is_archived = ". '1' : " AND p.is_archived = ". '0')
			. (isset($data['seller_id']) ? " AND mp.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')
			. (isset($data['store_id']) ? " AND ps.store_id =  " .  (int)$data['store_id'] : '')
			. (isset($data['is_single']) ? " AND p.is_single =  " .  (int)$data['is_single'] : '')
			. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
			. (isset($sort['filters']['p.category_id']) ? " AND ptc.category_id =  " .  (int)$sort['filters']['p.category_id'] : '')
			. (isset($sort['filters']['p.low_price']) ? " AND p.price >=  " .  (float)$sort['filters']['p.low_price'] : '')
			. (isset($sort['filters']['p.price']) ? " AND p.price <=  " .  (float)$sort['filters']['p.price'] : '')
			. (isset($sort['filters']['p.sku']) ? " AND p.sku LIKE '%" . $this->db->escape($sort['filters']['p.sku']) . "%'" : '')

			. " GROUP BY p.product_id "

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');
         
		$res = $this->db->query($sql);

		$wholesale_products = array();
		foreach ($res->rows as $product) {

			// Getting SOR product ID (if any)
			$sor_query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p 
			                                LEFT JOIN " . DB_PREFIX . "product_to_store ps USING(product_id)
                                           WHERE p.model LIKE '" . $this->db->escape($product['model'].'-SOR') . "'
                                             AND p.sku LIKE '" . $this->db->escape($product['p.sku']) . "'
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
				 pps.price as 'special',
	  			 pps.discount_type as 'discount_type',
				 pps.discount_value as 'discount_value',
			 	 pps.date_start as 'offer_date_start',
				 pps.date_end as 'offer_date_end',
 				 p.stock_status_id
 				 FROM " . DB_PREFIX . "ms_product mp
 				 INNER JOIN " . DB_PREFIX . "product p USING(product_id)
 				 INNER JOIN " . DB_PREFIX . "product_description pd USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_to_store ps USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_option_value pov USING(product_id)
 				 INNER JOIN " . DB_PREFIX . "product_to_category ptc USING(product_id)
 				 LEFT JOIN " . DB_PREFIX . "product_special pps USING(product_id)
 				 WHERE (ps.store_id != " . SOR_STORE_ID . " OR ps.store_id IS NULL)"

			. (isset($data['seller_id']) ? " AND mp.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')
			. (isset($data['store_id']) ? " AND ps.store_id =  " .  (int)$data['store_id'] : '')
			. (isset($data['is_single']) ? " AND p.is_single =  " .  (int)$data['is_single'] : '')
			. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')

			. (isset($sort['filters']['p.sku']) ? " AND p.sku LIKE '%" . $this->db->escape($sort['filters']['p.sku']) . "%'" : '')
			. (isset($sort['filters']['p.category_id']) ? " AND ptc.category_id =  " .  (int)$sort['filters']['p.category_id'] : '')
			. (isset($sort['filters']['p.low_price']) ? " AND p.price >=  " .  (float)$sort['filters']['p.low_price'] : '')
			. (isset($sort['filters']['p.price']) ? " AND p.price <=  " .  (float)$sort['filters']['p.price'] : '')

			. " GROUP BY p.product_id"

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');
		;

		//echo $sql; exit;
		//
		$res = $this->db->query($sql);

		foreach ($res->rows as $key => $value) {

			// Getting product Status
			$status_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "stock_status
                                          WHERE stock_status_id = '" . (int)$value['stock_status_id'] . "' AND language_id = '1'");
			if ($status_query->rows) {
				$res->rows[$key]['p.stock_status'] = $status_query->row['name'];
			} else {
				$res->rows[$key]['p.stock_status'] = '';
			}
			$sql1="
			SELECT ovd.name as option_name, ovd.option_id, pov.product_id, od.name, pov.product_option_value_id, pov.quantity, pov.price
			FROM
			oc_option_value_description ovd
			INNER JOIN
			oc_product_option_value pov
			ON (ovd.option_value_id = pov.option_value_id)
			INNER JOIN
			oc_option_description od
			ON (od.option_id = ovd.option_id)
			WHERE pov.product_id ="."'". (int)$value['product_id']."'".
				"AND ovd.option_id IN (SELECT po.option_id FROM ".DB_PREFIX.
				"product_option po WHERE po.product_id="."'" . (int)$value['product_id'] . "'".")";

			$new_res = $this->db->query($sql1);
			//echo "<br>";echo $sql1;die;

			$size_arr = array();
			$color_arr = array();
			$store_info =array();
			if(!empty($new_res->rows)){
				foreach ($new_res->rows as $key1 => $value1) {
					if ($value1['od.name'] = 'Size' && $value1['product_id'] == $value['product_id'] ) {
						$size_arr[] = array('size' =>$value1['option_name'], 'value_id'=> $value1['product_option_value_id'],'quantity'=> $value1['quantity'],'price'=> $value1['price'],);
					}
					else
						if ($value1['od.name'] = 'Color' && $value1['product_id'] == $value['product_id'] ) {
							$color_arr[] = $value1['name'];
						}

					$res->rows[$key]['sizes']  = $size_arr;
					$res->rows[$key]['colors'] = $color_arr;

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
					
				}
			}
			$res->rows[$key]['store_info']	= 	$store_info;
		}
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		
		return $res->rows;
	}

	public function getSellerStores($seller_id){
		$this->load->model('setting/store');
		$stores = $this->db->query("SELECT os.store_id FROM ".DB_PREFIX."setting os
    	    WHERE os.code = 'config'
    	    AND os.key = 'config_seller_id'
    	    AND os.value = ".(int)$seller_id);

		if ($stores->num_rows) {
			return $stores->rows;
		}
		else{
			return 0;
		}
	}

	public function getStore($store_id) {

		$stores_data = $this->cache->get('store');

		$store_data = array();
		if (!$stores_data) {
			$q = "SELECT * FROM " . DB_PREFIX . "store WHERE store_id = " . (int)$store_id;
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

	public function getPriceForStore($store_id, $product_id){
		$store_price = $this->db->query("SELECT p2s.store_price AS store_price
    		FROM ".DB_PREFIX."product_to_store p2s
			WHERE p2s.product_id=".(int)$product_id."
			AND p2s.store_id=".(int)$store_id);

		if (!empty($store_price->row)) {
			return $store_price->row['store_price'];
		}else {
			return 0;
		}

	}

	public function checkSellerSOR($seller_id){
		$seller_sor = $this->db->query("SELECT sor_enabled
    		FROM ".DB_PREFIX."ms_seller
			WHERE seller_id=".(int)$seller_id."");
		return $seller_sor->row;
	}
	public function getSellerMarkupPrice($seller_id){
		$seller_markup = $this->db->query("SELECT seller_markup_over_tp
    		FROM ".DB_PREFIX."wsb_seller_to_store
			WHERE seller_id=".(int)$seller_id."");
		return $seller_markup->row;
	}
	public function updateMarkupPrice($seller_id, $markup_price){
		$seller_markup = $this->db->query("SELECT seller_markup_over_tp
    		FROM ".DB_PREFIX."wsb_seller_to_store
			WHERE seller_id=".(int)$seller_id."");
		if(!empty($seller_markup->row)){
			$sql_product = "UPDATE " . DB_PREFIX . "wsb_seller_to_store 
			                SET `seller_markup_over_tp`= '" . (float)$markup_price . "', 
			                date_modified = now() WHERE seller_id = '" . (int)$seller_id . "'";
			$this->db->query($sql_product);
		}else{
			$insert = "INSERT INTO `" . DB_PREFIX . "wsb_seller_to_store`
                   	SET
                   	seller_id = '".(int)$seller_id ."',
                    store_id = ". SOR_STORE_ID .",
                   	seller_markup_over_tp = '" . (float)$markup_price . "',
                   	wsb_commission_over_tp = 12.5,
                   	date_added = now()";

			$query = $this->db->query($insert);
		}
	}



}
