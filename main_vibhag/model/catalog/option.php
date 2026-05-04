<?php
class ModelCatalogOption extends Model {
	public function addOption($data) {
		$this->event->trigger('pre.admin.option.add', $data);

		$this->db->query("INSERT INTO `" . DB_PREFIX . "option` SET type = '" . $this->db->escape($data['type']) . "', sort_order = '" . (int)$data['sort_order'] . "'");
		$option_id = $this->db->getLastId();

		//log data
		$change_log_action_data = array(
								'action' 			=> 'addOption',
								'table_id'			=> $option_id,
								'type'				=> $data['type'],
								'sort_order'		=> $data['sort_order'],
							);
		$this->admin_change_log_action($change_log_action_data);

		foreach ($data['option_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			
			//log data
			$change_log_action_data = array(
									'action' 			=> 'addOptionDescription',
									'table_id'			=> $option_id,
									'option_id'			=> $option_id,
									'name'				=> $value['name'],
								);
			$this->admin_change_log_action($change_log_action_data);
		}

		if (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$option_id . "', image = '" . $this->db->escape(html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$option_value['sort_order'] . "'");

				$option_value_id = $this->db->getLastId();

				//log data
				$change_log_action_data = array(
										'action' 			=> 'addOptionValue',
										'table_id'			=> $option_value_id,
										'option_id'			=> $option_id,
										'option_value_id'	=> $option_value_id,
										'image'				=> $option_value['image'],
										'sort_order'		=> $option_value['sort_order'],
									);
				$this->admin_change_log_action($change_log_action_data);

				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', language_id = '" . (int)$language_id . "', option_id = '" . (int)$option_id . "', name = '" . $this->db->escape($option_value_description['name']) . "'");
				
					//log data
					$change_log_action_data = array(
											'action' 			=> 'addOptionValueDescription',
											'table_id'			=> $option_value_id,
											'option_id'			=> $option_id,
											'option_value_id'	=> $option_value_id,
											'image'				=> $option_value['image'],
											'sort_order'		=> $option_value['sort_order'],
										);
					$this->admin_change_log_action($change_log_action_data);
				}
			}
		}

		$this->event->trigger('post.admin.option.add', $option_id);

		return $option_id;
	}

	
	public function editOption($option_id, $data) {

		$this->event->trigger('pre.admin.option.edit', $data);

		$old_option_data = $this->db->query("SELECT `type`,`sort_order` FROM " . DB_PREFIX . "option WHERE option_id = '" . (int)$option_id . "' ");
		$old_type 		= $old_option_data->row['type'] ?? '';
		$old_sort_order = $old_option_data->row['sort_order'] ?? '';

		$new_type = $data['type'] ?? '';
		$new_sort_order = $data['sort_order'] ?? '';
		if( ($old_type != $new_type) || ($old_sort_order !=$new_sort_order) )
		{
			$fields = array();
			if($old_type != $new_type){
				$fields[] = " type = '".$this->db->escape($data['type'])."' ";
			}
			if($old_sort_order !=$new_sort_order){
				$fields[] = " sort_order = '".(int)$data['sort_order']."' ";
			}
			if(!empty($fields)) {
				$sql  = "UPDATE `" . DB_PREFIX . "option` SET ";
				$sql .= implode(",", $fields);
				$sql .= " WHERE option_id = '" . (int)$option_id . "' ";
				$this->db->query($sql);
			}	
			//log data
			$change_log_action_data = array(
				'action' 			=> 'editOption',
				'table_id'			=> $option_id,
				'old_type'			=> $old_type,
				'type'				=> $new_type,
				'old_sort_order'	=> $old_sort_order,
				'sort_order'		=> $new_sort_order,
			);
			$this->admin_change_log_action($change_log_action_data);
		}

		foreach ($data['option_description'] as $language_id => $value) {
			$old_option_description =  $this->db->query("SELECT `name` FROM " . DB_PREFIX . "option_description 
                                                         WHERE option_id = '" . (int)$option_id . "' AND 
                                                               language_id = '" . (int)$language_id . "' "
                                                        );
            $old_option_name = '';																	 
			if($old_option_description->num_rows) {
				$old_option_name  = trim($old_option_description->row['name']);
			}
			$new_option_name = trim($value['name']) ?? '';
			// Update option name, if it changed
			if($old_option_name != $new_option_name) {
				$this->db->query("UPDATE 
								" . DB_PREFIX . "option_description 
							    SET 
									name 		= '" . $this->db->escape($new_option_name) . "'
								WHERE
									option_id 	= '" . (int)$option_id . "'
								AND
									language_id = '" . (int)$language_id . "'	
							");	
				/*Admin change log for filter group description*/
				$change_log_action_data = array(
						'action' 			=> 'editOptionDescription',
						'table_id'			=> $option_id,
						'old_option_name'	=> $old_option_name,
						'option_name'		=> $new_option_name,

					);
				$this->admin_change_log_action($change_log_action_data);
			}
		}

		// get all old filter ids
		$oldOptionValueQuery = $this->db->query("SELECT `option_value_id` FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "'");
		$oldOptionValues=array();
		if($oldOptionValueQuery->num_rows) {
		    $oldOptionValues = array_column($oldOptionValueQuery->rows, 'option_value_id');
		}
		$newOptionValues=array();
		foreach($data['option_value'] as $key=>$optionValueDetail){
			if(!empty($optionValueDetail['option_value_id'])){
				$newOptionValues[$key] = $optionValueDetail['option_value_id'];
			}
		}
		// get list of Option value ids to remove
		$optionsValuesToRemove=array();
		$optionsValuesToRemove = array_diff($oldOptionValues, $newOptionValues);
		$error_warning = array();
		if(count($optionsValuesToRemove)>0){
			foreach ($optionsValuesToRemove as $key => $value) {
				$deleted_option_value_id = $value;
				/* 
					check option value id count in oc_product_option_value
					if count > 0 then not allowed to delete option
				*/
				$product_option_value_query = $this->db->query("SELECT 1 FROM " . DB_PREFIX . "product_option_value WHERE option_value_id = '".(int)$deleted_option_value_id."' LIMIT 1 ");
				if($product_option_value_query->num_rows == 0){

					//admin change log entries for deleted filter
					$deleted_option_value_data =  $this->db->query("SELECT `option_value_id`,`option_id`,`sort_order`,`image` FROM " . DB_PREFIX . "option_value WHERE option_value_id = '" . (int)$deleted_option_value_id . "' ");
					if($deleted_option_value_data->num_rows) {
						$change_log_action_data = array(
								'action' 			=> 'deleteOptionValue',
								'table_id'			=> $deleted_option_value_id,
								'option_id'			=> $deleted_option_value_data->row['option_id'],
								'option_value_id'	=> $deleted_option_value_data->row['option_value_id'],
								'image'				=> $deleted_option_value_data->row['image'],
								'sort_order'		=> $deleted_option_value_data->row['sort_order'],
							);
						$this->admin_change_log_action($change_log_action_data);
					}

					//admin change log entries for deleted filter descriptions
					$deleted_option_value_description_data =  $this->db->query("SELECT `option_value_id`,`option_id`,`name`,`extra_info` FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int)$deleted_option_value_id . "' ");
					if($deleted_option_value_description_data->num_rows) {
						foreach($deleted_option_value_description_data->rows as $des_key => $des_value) {
							$change_log_action_data = array(
									'action' 			=> 'deleteOptionValueDescription',
									'table_id'			=> $deleted_option_value_id,
									'option_id'			=> $des_value['option_id'],
									'option_value_id'	=> $des_value['option_value_id'],
									'name'				=> $des_value['name'],
									'extra_info'		=> $des_value['extra_info'],
								);
							$this->admin_change_log_action($change_log_action_data);
						}
					}

					/*Remove filter from product_filter table */
					//$this->db->query("DELETE FROM " . DB_PREFIX . "option_value WHERE option_value_id = '" . (int)$deleted_option_value_id . "'");

					//$this->db->query("DELETE FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int)$deleted_option_value_id . "'");
				
				}else{
					$option_value_description_data =  $this->db->query("SELECT `name` FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int)$deleted_option_value_id . "' AND language_id = 1 ");
					if($option_value_description_data->num_rows){
						//$error_warning[] = "Option value(".$option_value_description_data->row['name'].") can not be deleted as value assign in products as option";
					}
				}
			}
		}
		
		if(!empty($data['option_value'])) {

			foreach ($data['option_value'] as $key => $option) {
				
				if(empty($option['option_value_id'])) {

					//insert option value data
					$this->db->query("INSERT 
										INTO " . DB_PREFIX . "option_value 
									 SET 
										option_id 	= '".(int)$option_id."',
										image 		= '".$this->db->escape($option['image'])."',
										sort_order  = '".(int)$option['sort_order']."'
									");
					$option_value_id = $this->db->getLastId();
					
					/*Admin change log for filter*/
					$change_log_action_data = array(
							'action' 			=> 'addOptionValue',
							'table_id'			=> $option_value_id,
							'option_id'			=> $option_id,
							'option_value_id'	=> $option_value_id,
							'sort_order'		=> $option['sort_order'],
							'image'				=> $option['image'],
						);
					$this->admin_change_log_action($change_log_action_data);
					
					// save option description
					if(!empty($option['option_value_description']))
					{
						foreach($option['option_value_description'] as $language_id => $value_description){
							
							$extra_info = '';
							if($option_id == '5') {
								$color_name_arr = explode('(',$value_description['name']);
								if(!empty($color_name_arr[0])) {
									$value_description['name'] = trim($color_name_arr[0]);
								}

								if(!empty($color_name_arr[1])) {
									$color_code_arr = explode(')',$color_name_arr[1]);
									$extra_info = $color_code_arr[0];
								}
							}

							$this->db->query("INSERT 
												INTO " . DB_PREFIX . "option_value_description 
											SET 
												option_value_id 		= '" . (int)$option_value_id . "', 
												language_id 			= '" . (int)$language_id . "', 
												option_id 				= '" . (int)$option_id . "', 
												name 					= '" . $this->db->escape($value_description['name']) . "',
												extra_info 				= '" . $this->db->escape($extra_info)."'
											");
							$option_value_description_id = $this->db->getLastId();
							/*Admin change log for filter*/
							$change_log_action_data = array(
									'action' 			=> 'addOptionValueDescription',
									'table_id'			=> $option_value_description_id,
									'option_id'			=> $option_id,
									'option_value_id'	=> $option_value_id,
									'name'				=> $option['name'],
									'extra_info'		=> $extra_info
								);
							$this->admin_change_log_action($change_log_action_data);
						}
					}

				}else{
					
					//update option value
					$old_option_value_data = array();
					$old_option_value_data_query = $this->db->query("SELECT `option_value_id`, `option_id`, `sort_order`,`image` 
																	FROM " . DB_PREFIX . "option_value 
																	WHERE option_value_id = '" . (int)$option['option_value_id'] . "' "
																	);
					if($old_option_value_data_query->num_rows) {
						$old_option_value_data = $old_option_value_data_query->row;
					}
					if( !empty($old_option_value_data) 
						&&
						(
							$old_option_value_data['sort_order'] != $option['sort_order'] 
							|| 
							$old_option_value_data['image'] != $option['image']
						)
					  ) {
						$sql = "
								UPDATE 
									" . DB_PREFIX . "option_value
								SET
									sort_order 		= '" . (int)$option['sort_order'] . "',
									image 			= '" . $this->db->escape($option['image']) . "'
								WHERE
									option_value_id = '" . (int)$option['option_value_id'] . "' 
								";
						$this->db->query($sql);
						//Admin change log for filter 
						$change_log_action_data = array(
								'action' 			=> 'editOptionValue',
								'table_id'			=> $option['option_value_id'],
								'option_id'			=> $option['option_id'],
								'old_sort_order'	=> $old_option_value_data['sort_order'],
								'sort_order'		=> $option['sort_order'],
								'old_image'			=> $old_option_value_data['image'],
								'image'				=> $option['image'],
							);
						$this->admin_change_log_action($change_log_action_data);

					}


					//update option value descriptions
					if(!empty($option['option_value_description'])) {

						foreach($option['option_value_description'] as $language_id => $value_description)
						{

							$extra_info = '';
							if($option_id == '5' && !empty($value_description['name'])) {
								$color_name_arr = explode('(',$value_description['name']);
								if(!empty(trim($color_name_arr[0]))) {
									$value_description['name'] = trim($color_name_arr[0]);
								}

								if(!empty(trim($color_name_arr[1]))) {
									$color_code_arr = explode(')',$color_name_arr[1]);
									$extra_info = $color_code_arr[0];
								}
							}
							$old_option_value_description_data = array();
							$old_option_value_description_data_query = $this->db->query("SELECT `name`,`extra_info`
																						FROM " . DB_PREFIX . "option_value_description 
																						WHERE option_value_id = '" . (int)$option['option_value_id'] . "' 
																								AND 
																							  language_id = '" . (int)$language_id . "' "
																					  );
							if($old_option_value_description_data_query->num_rows) {
								$old_option_value_description_data = $old_option_value_description_data_query->row;
							}
							if(trim($old_option_value_description_data['name']) != trim($value_description['name']))
							{
								$sql = "
									UPDATE 
										" . DB_PREFIX . "option_value_description
									SET
										name 		= '" . $this->db->escape($value_description['name']) . "'
									WHERE
										option_value_id = '" . (int)$option['option_value_id'] . "' 
										AND
										language_id = '" . (int)$language_id . "'
									";

								$this->db->query($sql);
								//Admin change log for filter 
								$change_log_action_data = array(
										'action' 			=> 'editOptionValueDescription',
										'table_id'			=> $option['option_value_id'],
										'option_id'			=> $option_id,
										'option_value_id'	=> $option['option_value_id'],
										'old_name'			=> $old_option_value_description_data['name'],
										'name'				=> $value_description['name'],
										'old_extra_info'	=> '',
										'extra_info'		=> '',
									);
								$this->admin_change_log_action($change_log_action_data);
								//change log
							}
							if($option_id == '5') {
								if(trim($old_option_value_description_data['extra_info']) != trim($extra_info))
								{
									$sql = "
											UPDATE 
												" . DB_PREFIX . "option_value_description
											SET
												extra_info 		= '" . $this->db->escape($extra_info) . "'
											WHERE
												option_value_id = '" . (int)$option['option_value_id'] . "' 
												AND
												language_id = '" . (int)$language_id . "'
											";
										$this->db->query($sql);
									$change_log_action_data['old_extra_info'] = $old_option_value_description_data['extra_info'] ?? '';
									$change_log_action_data['extra_info'] = $extra_info;
									$this->admin_change_log_action($change_log_action_data);
								}
							}
							
						}
					}
					
				}
			}

		}

		return $error_warning;

		$this->event->trigger('post.admin.option.edit', $option_id);
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function deleteOption($options) {

		$error_warning = array();
		if(empty($options)){
			return false;
		}
		foreach ($options as $option_id) 
		{
			$product_total = $this->getTotalProductsByOptionId($option_id);
			
			if ($product_total) {

				$error_warning[] = 'Warning: This option cannot be deleted as it is currently assigned to '.$product_total.' products!';
			
			}else{

				//admin change log - oc_option table action
				$optionData = $this->db->query("SELECT `type`,`sort_order` FROM `" . DB_PREFIX . "option` WHERE option_id = '".(int)$option_id."' ");
				if($optionData->num_rows) {
					$change_log_action_data = array(
							'action' 			=> 'deleteOption',
							'table_id'			=> $option_id,
							'option_id'			=> $option_id,
							'type'				=> $optionData->row['type'],
							'sort_order'		=> $optionData->row['sort_order'],
						);
					$this->admin_change_log_action($change_log_action_data);
				}
				
				//admin change log - oc_option_description table action
				$optionDescriptionData = $this->db->query("SELECT `name` FROM `" . DB_PREFIX . "option_description` WHERE option_id = '".(int)$option_id."' ");
				if($optionDescriptionData->num_rows) {
					foreach ($optionDescriptionData->rows as $key => $value) {
							$change_log_action_data = array(
									'action' 			=> 'deleteOptionDescription',
									'table_id'			=> $option_id,
									'option_id'			=> $option_id,
									'name'				=> $value['name'],
								);
							$this->admin_change_log_action($change_log_action_data);
						}	
				}

				//admin change log - option_value_description table action
				$optionValueData =  $this->db->query("SELECT `option_value_id`,`image`,`sort_order` FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "' ");
				if($optionValueData->num_rows) {
					foreach($optionValueData->rows as $des_key => $des_value) {
						$change_log_action_data = array(
								'action' 			=> 'deleteOptionValue',
								'table_id'			=> $option_id,
								'option_id'			=> $option_id,
								'option_value_id'	=> $des_value['option_value_id'],
								'image'				=> $des_value['image'],
								'sort_order'		=> $des_value['sort_order'],
							);
						$this->admin_change_log_action($change_log_action_data);
					}
				}

				//admin change log - option_value_description table action
				$optionValueDescriptionData =  $this->db->query("SELECT `option_value_id`,`name`,`extra_info` FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int)$option_id . "' ");
				if($optionValueDescriptionData->num_rows) {
					foreach($optionValueDescriptionData->rows as $des_key => $des_value) {
						$change_log_action_data = array(
								'action' 			=> 'deleteOptionValueDescription',
								'table_id'			=> $option_id,
								'option_id'			=> $option_id,
								'option_value_id'	=> $des_value['option_value_id'],
								'name'				=> $des_value['name'],
								'extra_info'		=> $des_value['extra_info'],
							);
						$this->admin_change_log_action($change_log_action_data);
					}
				}

				// after admin change log entries, delete data from various option tables
				$this->db->query("DELETE FROM `" . DB_PREFIX . "option` WHERE option_id = '" . (int)$option_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$option_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int)$option_id . "'");
			}

		}
		return $error_warning;
	}

	public function getOption($option_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE o.option_id = '" . (int)$option_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getOptions($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND od.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'od.name',
			'o.type',
			'o.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY od.name";
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

	public function getOptionDescriptions($option_id) {
		$option_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$option_id . "'");

		foreach ($query->rows as $result) {
			$option_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $option_data;
	}

	public function getOptionValue($option_value_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_value_id = '" . (int)$option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
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

	public function getOptionValueDescriptions($option_id) {
		$option_value_data = array();

		$option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "' ORDER BY sort_order");

		foreach ($option_value_query->rows as $option_value) {
			$option_value_description_data = array();

			$option_value_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int)$option_value['option_value_id'] . "'");

			foreach ($option_value_description_query->rows as $option_value_description) {
				$option_value_description_data[$option_value_description['language_id']] = array('name' => $option_value_description['name']);
			}

			$option_value_data[] = array(
				'option_value_id'          => $option_value['option_value_id'],
				'option_value_description' => $option_value_description_data,
				'image'                    => $option_value['image'],
				'sort_order'               => $option_value['sort_order']
			);
		}

		return $option_value_data;
	}

	public function getTotalOptions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "option`");

		return $query->row['total'];
	}
	
	public function getAllColors($color_like = null, $language_id = 0 ) {
		$sql = "SELECT * FROM ".DB_PREFIX."color";
		//$sql = "SELECT language_id, GROUP_CONCAT(name,hex_code) as colors FROM ".DB_PREFIX."color";
		if(!empty($language_id)) {
			$sql .= " WHERE language_id = '".$this->db->escape($language_id)."'";
		}
		if(!empty($color_like)) {
			$sql .= " AND name LIKE '%".$color_like."%'";
		}
		//$sql .= " GROUP BY language_id";
		$result = $this->db->query($sql);
		if($result->num_rows) {
			return $result->rows;
		}
		return false;
	}
	
	public function getColorHexCode($color_name, $language_id = null) {
		$sql = "SELECT hex_code FROM ".DB_PREFIX."color WHERE name = '".$this->db->escape($color_name)."'";
		if(!empty($language_id)) {
			$sql .= " AND language_id = ".$this->db->escape($language_id);
		}
		$sql .= " LIMIT 1";
		$result = $this->db->query($sql);
		if($result->num_rows) {
			return $result->row['hex_code'];
		}
		return false;
	}
	
	public function addNewMasterColor($color_data) {
		if(empty($color_data['synonyms'])) {
			$color_data['synonyms'] = "";
		}
		$sql = "INSERT INTO ".DB_PREFIX."color SET 
						language_id = '".$this->db->escape($color_data['language_id'])."',
						name = '".$this->db->escape($color_data['color_name'])."',
						hex_code = '".$this->db->escape($color_data['hex_code'])."',
						synonyms = '".$this->db->escape($color_data['synonyms'])."'";
		$result = $this->db->query($sql);
		return true;
	}

	/**
    * Public function to add option changes data into oc_admin_change_log for various option actions(add/edit/delete)
    * @param: array  $change_log_action_data 
    * @return void
    * @author MSA May 2019
    */
	public function admin_change_log_action(array $change_log_action_data)
	{
		$action 	= $change_log_action_data['action'] ?? '';

		$change_log = array();
		$change_log['user_id'] 	= $this->user->getId();
		$change_log['name'] 	= $this->user->getGroupName();
		$change_log['username'] = $this->user->getUserName()['username'];
		$change_log['user_type']= $this->user->getGroupName();

		switch ($action) {

			case 'addOption':
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'type';
				$change_log['ref_url'] 		= 'ModelCatalogOption/addOption';
				$change_log['old_value'] 	= $change_log_action_data['type'];
				$change_log['new_value'] 	= $change_log_action_data['type'];
				$change_log['file_location'] = 'ModelCatalogOption/addOption';
				$change_log['comment'] 		= 'add new option';
				$this->save_admin_change_log($change_log);	
				$change_log['old_value'] 	= $change_log_action_data['sort_order'];
				$change_log['new_value'] 	= $change_log_action_data['sort_order'];
				$this->save_admin_change_log($change_log);	
				break;

			case 'editOption':
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'type';
				$change_log['ref_url'] 		= 'ModelCatalogOption/editOption';
				$change_log['old_value'] 	= $change_log_action_data['old_type'];
				$change_log['new_value'] 	= $change_log_action_data['type'];
				$change_log['file_location'] = 'ModelCatalogOption/editOption';
				$change_log['comment'] 		= 'edit option value';
				if($change_log['old_value'] != $change_log['new_value']) {
					$this->save_admin_change_log($change_log);	
				}
				$change_log['field_name'] 	= 'sort_order';
				$change_log['ref_url'] 		= 'ModelCatalogFilter/editFilter';
				$change_log['old_value'] 	= $change_log_action_data['old_sort_order'];
				$change_log['new_value'] 	= $change_log_action_data['sort_order'];
				if($change_log['old_value'] != $change_log['new_value']) {
					$this->save_admin_change_log($change_log);
				}
				break;

			case 'addOptionDescription' : 
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'name';
				$change_log['ref_url'] 		= 'ModelCatalogOption/addOption';
				$change_log['old_value'] 	= $change_log_action_data['option_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_id'];
				$change_log['file_location'] = 'ModelCatalogOption/addOption';
				$change_log['comment'] 		= 'add new option description';
				$this->save_admin_change_log($change_log);
				$change_log['old_value'] 	= $change_log_action_data['name'];
				$change_log['new_value'] 	= $change_log_action_data['name'];
				$this->save_admin_change_log($change_log);			
				break;

			case 'editOptionDescription' : 
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option_description';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'name';
				$change_log['ref_url'] 		= 'ModelCatalogOption/editOption';
				$change_log['old_value'] 	= $change_log_action_data['old_option_name'];
				$change_log['new_value'] 	= $change_log_action_data['option_name'];
				$change_log['file_location'] = 'ModelCatalogOption/editOption';
				$change_log['comment'] 		= 'edit option description';
				if($change_log['old_value'] != $change_log['new_value']) {
					$this->save_admin_change_log($change_log);	
				}	
				break;
			case 'deleteOption' :
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'type';
				$change_log['ref_url'] 		= 'ModelCatalogOption/deleteOption';
				$change_log['old_value'] 	= $change_log_action_data['type'];
				$change_log['new_value'] 	= $change_log_action_data['type'];
				$change_log['file_location'] = 'ModelCatalogOption/deleteOption';
				$change_log['comment'] 		= 'delete option';
				$this->save_admin_change_log($change_log);	
				$change_log['field_name'] 	= 'sort_order';
				$change_log['old_value'] 	= $change_log_action_data['sort_order'];
				$change_log['new_value'] 	= $change_log_action_data['sort_order'];
				$this->save_admin_change_log($change_log);	
				break;
			case 'deleteOptionDescription' :
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option_description';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'name';
				$change_log['ref_url'] 		= 'ModelCatalogOption/deleteOption';
				$change_log['old_value'] 	= $change_log_action_data['name'];
				$change_log['new_value'] 	= $change_log_action_data['name'];
				$change_log['file_location'] = 'ModelCatalogOption/deleteOption';
				$change_log['comment'] 		= 'delete option description';
				$this->save_admin_change_log($change_log);
				break;		
			case 'deleteOptionValue' : 
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option_value';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'option_id';
				$change_log['ref_url'] 		= 'ModelCatalogOption/editOption';
				$change_log['old_value'] 	= $change_log_action_data['option_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_id'];
				$change_log['file_location'] = 'ModelCatalogOption/editOption';
				$change_log['comment'] 		= 'delete option value';
				$this->save_admin_change_log($change_log);	
				$change_log['old_value'] 	= $change_log_action_data['option_value_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_value_id'];
				$this->save_admin_change_log($change_log);	
				$change_log['old_value'] 	= $change_log_action_data['image'];
				$change_log['new_value'] 	= $change_log_action_data['image'];
				$this->save_admin_change_log($change_log);	
				$change_log['old_value'] 	= $change_log_action_data['sort_order'];
				$change_log['new_value'] 	= $change_log_action_data['sort_order'];
				$this->save_admin_change_log($change_log);	
				break;

			case 'deleteOptionValueDescription' : 
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option_value';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'option_id';
				$change_log['ref_url'] 		= 'ModelCatalogOption/editOption';
				$change_log['old_value'] 	= $change_log_action_data['option_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_id'];
				$change_log['file_location'] = 'ModelCatalogOption/editOption';
				$change_log['comment'] 		= 'delete option value description';
				$this->save_admin_change_log($change_log);	
				$change_log['field_name'] 	= 'option_value_id';
				$change_log['old_value'] 	= $change_log_action_data['option_value_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_value_id'];
				$this->save_admin_change_log($change_log);
				$change_log['field_name'] 	= 'name';
				$change_log['old_value'] 	= $change_log_action_data['name'];
				$change_log['new_value'] 	= $change_log_action_data['name'];
				$this->save_admin_change_log($change_log);
				$change_log['field_name'] 	= 'extra_info';	
				$change_log['old_value'] 	= $change_log_action_data['extra_info'];
				$change_log['new_value'] 	= $change_log_action_data['extra_info'];
				$this->save_admin_change_log($change_log);	
				break;

			case 'addOptionValue' : 
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option_value';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'option_id';
				$change_log['ref_url'] 		= 'ModelCatalogOption/editOption';
				$change_log['old_value'] 	= $change_log_action_data['option_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_id'];
				$change_log['file_location'] = 'ModelCatalogOption/editOption';
				$change_log['comment'] 		= 'add option value';
				$this->save_admin_change_log($change_log);	
				$change_log['old_value'] 	= $change_log_action_data['option_value_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_value_id'];
				$this->save_admin_change_log($change_log);	
				$change_log['old_value'] 	= $change_log_action_data['sort_order'];
				$change_log['new_value'] 	= $change_log_action_data['sort_order'];
				$this->save_admin_change_log($change_log);
				$change_log['old_value'] 	= $change_log_action_data['image'];
				$change_log['new_value'] 	= $change_log_action_data['image'];
				$this->save_admin_change_log($change_log);	
				break;

			case 'addOptionValueDescription' :
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option_value_description';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'option_id';
				$change_log['ref_url'] 		= 'ModelCatalogOption/editOption';
				$change_log['old_value'] 	= $change_log_action_data['option_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_id'];
				$change_log['file_location'] = 'ModelCatalogOption/editOption';
				$change_log['comment'] 		= 'add option value description';
				$this->save_admin_change_log($change_log);
				$change_log['field_name'] 	= 'option_value_id';	
				$change_log['old_value'] 	= $change_log_action_data['option_value_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_value_id'];
				$this->save_admin_change_log($change_log);	
				$change_log['old_value'] 	= $change_log_action_data['name'];
				$change_log['new_value'] 	= $change_log_action_data['name'];
				$this->save_admin_change_log($change_log);
				$change_log['old_value'] 	= $change_log_action_data['extra_info'];
				$change_log['new_value'] 	= $change_log_action_data['extra_info'];
				$this->save_admin_change_log($change_log);
				break;
					
			case 'editOptionValue' :
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option_value_description';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'option_value_id';
				$change_log['ref_url'] 		= 'ModelCatalogOption/editOption';
				$change_log['old_value'] 	= $change_log_action_data['option_value_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_value_id'];
				$change_log['comment'] 		= 'edit option value';
				if($change_log['old_value'] != $change_log['new_value']){
					$this->save_admin_change_log($change_log);	
				}
				$change_log['old_value'] 	= $change_log_action_data['old_sort_order'];
				$change_log['new_value'] 	= $change_log_action_data['sort_order'];
				if($change_log['old_value'] != $change_log['new_value']){
					$this->save_admin_change_log($change_log);	
				}
				$change_log['old_value'] 	= $change_log_action_data['old_image'];
				$change_log['new_value'] 	= $change_log_action_data['image'];
				if($change_log['old_value'] != $change_log['new_value']){
					$this->save_admin_change_log($change_log);	
				}
				break;
			case 'editOptionValueDescription' :
				$change_log['table_id'] 	= $change_log_action_data['table_id'];
				$change_log['table_name'] 	= 'oc_option_value_description';
				$change_log['source_field'] = 'option';
				$change_log['field_name'] 	= 'option_id';
				$change_log['ref_url'] 		= 'ModelCatalogOption/editOption';
				$change_log['old_value'] 	= $change_log_action_data['option_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_id'];
				$change_log['file_location'] = 'ModelCatalogOption/editOption';
				$change_log['comment'] 		= 'edit option value description';
				if($change_log['old_value'] != $change_log['new_value']){
					$this->save_admin_change_log($change_log);	
				}
				$change_log['field_name'] 	= 'option_value_id';	
				$change_log['old_value'] 	= $change_log_action_data['option_value_id'];
				$change_log['new_value'] 	= $change_log_action_data['option_value_id'];
				if($change_log['old_value'] != $change_log['new_value']){
					$this->save_admin_change_log($change_log);	
				}
				$change_log['field_name'] 	= 'name';
				$change_log['old_value'] 	= $change_log_action_data['old_name'];
				$change_log['new_value'] 	= $change_log_action_data['name'];
				if($change_log['old_value'] != $change_log['new_value']){
					$this->save_admin_change_log($change_log);	
				}
				$change_log['field_name'] 	= 'extra_info';
				$change_log['old_value'] 	= $change_log_action_data['old_extra_info'];
				$change_log['new_value'] 	= $change_log_action_data['extra_info'];
				if($change_log['old_value'] != $change_log['new_value']){
					$this->save_admin_change_log($change_log);	
				}
				break;
		}

	} 
	/**
    * Public function to add option change data into oc_admin_change_log DB table dynamically
    * @param: array  $data 
    * @return void
    * @author MSA May 2019
    */
	public function save_admin_change_log(array $data): void
	{
        $admin_change_data                  = array();
        $admin_change_data['table_id']      = $data['table_id'];
        $admin_change_data['user_id']       = $data['user_id'];
        $admin_change_data['name']          = $data['name'];
        $admin_change_data['username']      = $data['username'];
        $admin_change_data['table_name']    = $data['table_name'];
        $admin_change_data['source_field']  = $data['source_field'];
        $admin_change_data['field_name']    = $data['field_name'];
        $admin_change_data['ref_url']       = $data['ref_url'];
        $admin_change_data['old_value']     = $data['old_value'];
        $admin_change_data['new_value']     = $data['new_value'];
        $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
        $admin_change_data['ip_address']    = $this->request->getIpAddress;
        $admin_change_data['file_location'] = $data['file_location'];
        $admin_change_data['user_type']     = $data['user_type'];
        $admin_change_data['comment']     	= $data['comment'] ?? '';
        CommonLib::addAdminChangeLog($this->db, $admin_change_data);
        
	}

}