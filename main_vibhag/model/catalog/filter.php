<?php
class ModelCatalogFilter extends Model {

	private $_maxNoOfFiltersCanBeDeleted = 3;

	public function addFilter($data) {
		$this->event->trigger('pre.admin.filter.add', $data);

		$this->db->query("INSERT INTO `" . DB_PREFIX . "filter_group` SET sort_order = '" . (int)$data['sort_order'] . "'");

		$filter_group_id = $this->db->getLastId();

		/*Admin change log for filter group*/
			$change_log_action_data = array(
					'action' 			=> 'addFilterGroup',
					'table_id'			=> $filter_group_id,
					'sort_order'		=> $data['sort_order']
				);
			$this->admin_change_log_action($change_log_action_data);


		foreach ($data['filter_group_description'] as $language_id => $value) {
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "filter_group_description SET filter_group_id = '" . (int)$filter_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			
			/*Admin change log for filter group description*/
				$change_log_action_data = array(
						'action' 			=> 'addFilterGroupDescrption',
						'table_id'			=> $filter_group_id,
						'group_name'		=> $value['name'],

					);
				$this->admin_change_log_action($change_log_action_data);
		}


		if (isset($data['filter'])) {
			foreach ($data['filter'] as $key=>$filter) {
				$upload_image='';
				
				if(!empty($filter['image'])){
					$upload_image = $filter['image'];
				} else {
					$upload_image = $filter['image_old'];
				}	
				$this->db->query("INSERT INTO " . DB_PREFIX . "filter SET filter_group_id = '" . (int)$filter_group_id . "', sort_order = '" . (int)$filter['sort_order'] . "', image = '" . $this->db->escape($upload_image) . "'"); 

				$filter_id = $this->db->getLastId();

				/*Admin change log for filter*/
					$change_log_action_data = array(
							'action' 			=> 'addFilter',
							'filter_id'			=> $filter_id,
							'filter_group_id'	=> $filter_group_id,
							'sort_order'		=> $filter['sort_order'],
							'image'				=> $upload_image,
						);
					$this->admin_change_log_action($change_log_action_data);
				//change log	

				foreach ($filter['filter_description'] as $language_id => $filter_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "filter_description SET filter_id = '" . (int)$filter_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($filter_description['name']) . "' ");
                    // this array is passed to reverie model
                    $language_array['filter_id']= $filter_id;
                    $language_array['filter_description'][$language_id] = array(
                        'name' => $filter_description['name'],
                    );

                   /*Admin change log for filter*/
					$change_log_action_data = array(
							'action' 			=> 'addFilterDescription',
							'filter_id'			=> $filter_id,
							'name'				=> $filter_description['name'],
						);
					$this->admin_change_log_action($change_log_action_data);
					//change log 	

				}
                $filter_ids[] = $language_array;
			}
		}
		$this->event->trigger('post.admin.filter.add', $filter_group_id);

		return $filter_group_id;
	}

	public function editFilter($filter_group_id, $data) {
		
		$this->event->trigger('pre.admin.filter.edit', $data);
		
		$old_sort_order = 0;
		$old_filter_group =  $this->db->query("SELECT `sort_order` FROM " . DB_PREFIX . "filter_group WHERE filter_group_id = '" . (int)$filter_group_id . "' ");

		if($old_filter_group->num_rows) {
			$old_sort_order  = (int)$old_filter_group->row['sort_order'];
		}	
		
		$new_sort_order = (int)$data['sort_order'] ?? 0;
		// Update filter group, if it changed
		if($old_sort_order != $new_sort_order) {

			$this->db->query("UPDATE 
							`" . DB_PREFIX . "filter_group` 
							SET 
								sort_order = '" . $new_sort_order . "' 
							WHERE 
								filter_group_id = '" . (int)$filter_group_id . "'"
						);

			/*Admin change log for filter group*/
			$change_log_action_data = array(
					'action' 			=> 'editFilterGroup',
					'table_id'			=> $filter_group_id,
					'old_sort_order'	=> $old_sort_order,
					'sort_order'		=> $new_sort_order,
				);
			$this->admin_change_log_action($change_log_action_data);
		}
		
		
		foreach ($data['filter_group_description'] as $language_id => $value) 
		{
			$old_filter_group_description =  $this->db->query("SELECT `name` FROM " . DB_PREFIX . "filter_group_description 
                                                               WHERE filter_group_id = '" . (int)$filter_group_id . "' AND 
                                                                     language_id = '" . (int)$language_id . "' ");
            $old_group_name = '';																	 
			if($old_filter_group_description->num_rows) {
				$old_group_name  = trim($old_filter_group_description->row['name']);
			}

			$new_group_name = trim($value['name']) ?? '';
			// Update group name, if it changed
			if($old_group_name != $new_group_name) {
				$this->db->query("UPDATE 
								" . DB_PREFIX . "filter_group_description 
							    SET 
									name 			= '" . $this->db->escape($new_group_name) . "'
								WHERE
									filter_group_id = '" . (int)$filter_group_id . "'
								AND
									language_id = '" . (int)$language_id . "'	
							");	

			
				/*Admin change log for filter group description*/
				$change_log_action_data = array(
						'action' 			=> 'editFilterGroupDescrption',
						'table_id'			=> $filter_group_id,
						'old_group_name'	=> $old_group_name,
						'group_name'		=> $new_group_name,

					);
				$this->admin_change_log_action($change_log_action_data);
			}
		}

		// get all old filter ids
		$oldFilterQuery = $this->db->query("SELECT `filter_id` FROM " . DB_PREFIX . "filter WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		$oldFilter=array();
		if($oldFilterQuery->num_rows) {
		    $oldFilter = array_column($oldFilterQuery->rows, 'filter_id');
		}

		$newFilter=array();
		$filter_to_remove=array();
		foreach($data['filter'] as $ky=>$filterDetail){
			if(!empty($filterDetail['filter_id'])){
				$newFilter[$ky] = $filterDetail['filter_id'];
			}
		}
		
		// get list of filter ids to remove
		$filter_to_remove = array_diff($oldFilter, $newFilter);
		
		$noOfFilterToRemove = count($filter_to_remove);

		if($noOfFilterToRemove > 0 && $noOfFilterToRemove <= $this->_maxNoOfFiltersCanBeDeleted ){

			foreach ($filter_to_remove as $key => $value) {
				
				$deleted_filter_id = $value;

				//admin change log entries for deleted filter
				$deleted_filter_data =  $this->db->query("SELECT `filter_id`,`sort_order`,`image` FROM " . DB_PREFIX . "filter WHERE filter_id = '" . (int)$deleted_filter_id . "' ");
				if($deleted_filter_data->num_rows) {
					$change_log_action_data = array(
							'action' 			=> 'deleteFilter',
							'filter_id'			=> $deleted_filter_data->row['filter_id'],
							'filter_group_id'	=> $filter_group_id,
							'sort_order'		=> $deleted_filter_data->row['sort_order'],
							'image'				=> $deleted_filter_data->row['image'],
						);
					$this->admin_change_log_action($change_log_action_data);
				}
				//admin change log entries for deleted filter descriptions
				$deleted_filter_description_data =  $this->db->query("SELECT `filter_id`,`name` FROM " . DB_PREFIX . "filter_description WHERE filter_id = '" . (int)$deleted_filter_id . "' ");
				if($deleted_filter_description_data->num_rows) {
					foreach($deleted_filter_description_data->rows as $des_key => $des_value) {
						$change_log_action_data = array(
								'action' 			=> 'deleteFilterDescription',
								'filter_id'			=> $des_value['filter_id'],
								'filter_group_id'	=> $filter_group_id,
								'name'				=> $des_value['name'],
							);
						$this->admin_change_log_action($change_log_action_data);
					}
				}

				/*Remove filter from product_filter table */
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE filter_id = '" . (int)$deleted_filter_id . "'");
				
				$this->db->query("DELETE FROM " . DB_PREFIX . "filter WHERE filter_id = '" . (int)$deleted_filter_id . "'");
				/* Foreign key constraint added on oc_filter_description table 
					with CASCADE action to remove oc_filter referenced records. 
				*/
			}
		}
		$filter_id = 0;
		if (isset($data['filter'])) {
			foreach ($data['filter'] as $key=>$filter) {

				$upload_image='';
				
				if(!empty($filter['image'])){
					$upload_image = $filter['image'];
				} else {
					$upload_image = $filter['image_old'];
				}	

				if(empty($filter['filter_id'])) {

					$this->db->query("INSERT 
										INTO " . DB_PREFIX . "filter 
									SET 
										filter_group_id = '" . (int)$filter_group_id . "', 
										sort_order 		= '" . (int)$filter['sort_order'] . "',
										image 			= '" . $this->db->escape($upload_image) . "'
									");
					$filter_id = $this->db->getLastId();
					
					/*Admin change log for filter*/
					$change_log_action_data = array(
							'action' 			=> 'addFilter',
							'filter_id'			=> $filter_id,
							'filter_group_id'	=> $filter_group_id,
							'sort_order'		=> $filter['sort_order'],
							'image'				=> $upload_image,
						);
					$this->admin_change_log_action($change_log_action_data);
					//change log

				} else { 

					$old_filter_data = array();
					$old_filter_data_query = $this->db->query("SELECT `sort_order`,`image` FROM " . DB_PREFIX . "filter WHERE filter_id = '" . (int)$filter['filter_id'] . "' ");
					if($old_filter_data_query->num_rows) {
						$old_filter_data = $old_filter_data_query->row;
					}

					if( $old_filter_data['sort_order'] != $filter['sort_order'] 
						|| 
						$old_filter_data['image'] != $upload_image
					  ) {
						$this->db->query("UPDATE 
										 		" . DB_PREFIX . "filter 
										SET 
											filter_group_id = '" . (int)$filter_group_id . "', 
											sort_order 		= '" . (int)$filter['sort_order'] . "',
											image 			= '" . $this->db->escape($upload_image) . "'
										WHERE 
											filter_id = '" . (int)$filter['filter_id'] . "' "
										);
						/*Admin change log for filter*/
						
						$change_log_action_data = array(
								'action' 			=> 'editFilter',
								'filter_id'			=> $filter['filter_id'],
								'filter_group_id'	=> $filter_group_id,
								'old_sort_order'	=> $old_filter_data['sort_order'],
								'sort_order'		=> $filter['sort_order'],
								'old_image'			=> $old_filter_data['image'],
								'image'				=> $upload_image,
							);
						$this->admin_change_log_action($change_log_action_data);
						//change log
					}
				}
				
				foreach ($filter['filter_description'] as $language_id => $filter_description) 
				{
					if(!empty($filter['filter_id'])) {

						$old_filter_description_data = array();
						$old_filter_description_data_query = $this->db->query("SELECT `name` FROM " . DB_PREFIX . "filter_description WHERE filter_id = '" . (int)$filter['filter_id'] . "' AND language_id = '" . (int)$language_id . "' ");
						if($old_filter_description_data_query->num_rows) {
							$old_filter_description_data = $old_filter_description_data_query->row;
						}

						if(trim($old_filter_description_data['name']) != trim($filter_description['name']))
						{
							$sql = "
									UPDATE 
										" . DB_PREFIX . "filter_description
									SET
										name 		= '" . $this->db->escape($filter_description['name']) . "'
									WHERE
										filter_id = '" . (int)$filter['filter_id'] . "' 
										AND
										language_id = '" . (int)$language_id . "'
									";

							$this->db->query($sql);

							/*Admin change log for filter*/
							$change_log_action_data = array(
									'action' 			=> 'editFilterDescription',
									'filter_id'			=> $filter['filter_id'],
									'filter_group_id'	=> $filter_group_id,
									'old_name'			=> $old_filter_description_data['name'],
									'name'				=> $filter_description['name'],
								);
							$this->admin_change_log_action($change_log_action_data);
							//change log
						}


					} else if( $filter_id ) {

							$this->db->query("INSERT 
												INTO " . DB_PREFIX . "filter_description 
											SET 
												filter_id 		= '" . (int)$filter_id . "', 
												language_id 	= '" . (int)$language_id . "', 
												name 			= '" . $this->db->escape($filter_description['name']) . "'
											");	

							/*Admin change log for new filter description*/
							$change_log_action_data = array(
									'action' 			=> 'addFilterDescription',
									'filter_id'			=> $filter_id,
									'filter_group_id'	=> $filter_group_id,
									'name'				=> $filter_description['name'],
								);
							$this->admin_change_log_action($change_log_action_data);
							//change log 
					}	
				}
			}
		}

        $this->event->trigger('post.admin.filter.edit', $filter_group_id);
	}

	public function deleteFilter($filter_group_id) {
		
		$this->event->trigger('pre.admin.filter.delete', $filter_group_id);

		//admin change log entries for group delete
			$filter_group =  $this->db->query("SELECT `sort_order` FROM " . DB_PREFIX . "filter_group WHERE filter_group_id = '" . (int)$filter_group_id . "' ");
			if($filter_group->num_rows) {
				$change_log_action_data = array(
					'action' 			=> 'deleteFilterGroup',
					'table_id'			=> $filter_group_id,
					'sort_order'		=> $filter_group->row['sort_order'],
				);
				$this->admin_change_log_action($change_log_action_data);
			}
			$filter_group_description =  $this->db->query("SELECT `name` FROM " . DB_PREFIX . "filter_group_description WHERE filter_group_id = '" . (int)$filter_group_id . "' ");
			if($filter_group_description->num_rows) {
				foreach ($filter_group_description->rows as $key => $value) {
					$change_log_action_data = array(
						'action' 			=> 'deleteFilterGroupDescription',
						'table_id'			=> $filter_group_id,
						'group_name'		=> $value['name'],
					);
					$this->admin_change_log_action($change_log_action_data);
				}
			}

		
		//admin change log entries for filter delete
		$filter_data =  $this->db->query("SELECT `filter_id`,`sort_order`,`image` FROM " . DB_PREFIX . "filter WHERE filter_group_id = '" . (int)$filter_group_id . "' ");
		$product_filter_ids = array();
		$filter_id = 0;
		if($filter_data->num_rows) {
			foreach($filter_data->rows as $key => $value) {
				$filter_id = $value['filter_id'];
				$product_filter_ids[] = $value['filter_id'];
				$change_log_action_data = array(
						'action' 			=> 'deleteFilter',
						'filter_id'			=> $value['filter_id'],
						'filter_group_id'	=> $filter_group_id,
						'sort_order'		=> $value['sort_order'],
						'image'				=> $value['image'],
					);
				$this->admin_change_log_action($change_log_action_data);
			}
		}

		//admin change log entries for filter description delete
		$filter_data =  $this->db->query("SELECT `filter_id`,`name` FROM " . DB_PREFIX . "filter_description WHERE filter_id = '" . (int)$filter_id . "' ");
		if($filter_data->num_rows) {
			foreach($filter_data->rows as $key => $value) {
				$change_log_action_data = array(
						'action' 			=> 'deleteFilterDescription',
						'filter_id'			=> $value['filter_id'],
						'filter_group_id'	=> $filter_group_id,
						'name'				=> $value['name'],
					);
				$this->admin_change_log_action($change_log_action_data);
			}
		}
		// change log

		/*Remove filter from product_filter table */
		if(!empty($product_filter_ids)) {
			$product_filter_ids = implode(',', $product_filter_ids);
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE filter_id IN (".$product_filter_ids.") ");	
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "filter_group` WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		/* Foreign key constraint added on filter_group with CASCADE action 
			to remove filter_group_description table referenced records. 
		*/

		$this->db->query("DELETE FROM `" . DB_PREFIX . "filter` WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		/* Foreign key constraint added on oc_filter_description & oc_product_filter tables 
			with CASCADE action to remove oc_filter referenced records. 
		*/
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_filter_group` WHERE filter_group_id = '" . (int)$filter_group_id . "'");

		$this->event->trigger('post.admin.filter.delete', $filter_group_id);
	}

	public function getFilterGroup($filter_group_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "filter_group` fg LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE fg.filter_group_id = '" . (int)$filter_group_id . "' AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getFilterGroups($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "filter_group` fg LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'fgd.name',
			'fg.sort_order'
		);

		//filter_name
		if (!empty($data['filter_name'])) {
			$sql .= " AND fgd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY fgd.name";
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

	public function getFilterGroupDescriptions($filter_group_id) {
		$filter_group_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter_group_description WHERE filter_group_id = '" . (int)$filter_group_id . "'");

		foreach ($query->rows as $result) {
			$filter_group_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $filter_group_data;
	}

	public function getFilter($filter_id) {
		$query = $this->db->query("SELECT *, (SELECT name FROM " . DB_PREFIX . "filter_group_description fgd WHERE f.filter_group_id = fgd.filter_group_id AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS `group` FROM " . DB_PREFIX . "filter f INNER JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id = '" . (int)$filter_id . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getFilters($data='') {
		$sql = "SELECT *, (SELECT name FROM " . DB_PREFIX . "filter_group_description fgd WHERE f.filter_group_id = fgd.filter_group_id AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS `group` FROM " . DB_PREFIX . "filter f INNER JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND fd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " ORDER BY f.sort_order ASC";

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

	public function getFilterDescriptions($filter_group_id) {
		$filter_data = array();

		$filter_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter WHERE filter_group_id = '" . (int)$filter_group_id . "' ORDER BY sort_order ASC");

		foreach ($filter_query->rows as $filter) {
			$filter_description_data = array();

			$filter_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter_description WHERE filter_id = '" . (int)$filter['filter_id'] . "'");

			foreach ($filter_description_query->rows as $filter_description) {
				$filter_description_data[$filter_description['language_id']] = array('name' => $filter_description['name']);
			}

			$this->load->model('tool/image');
			if(!empty($filter['image'])){
				$filter['image'] = $filter['image'];
			} else {
				$filter['image'] = 'placeholder.png';
			}
			$image_url=$this->model_tool_image->resize($filter['image'],
													   $this->config->get('config_image_additional_width'),
													   $this->config->get('config_image_additional_height'));
			$filter_data[] = array(
				'filter_id'          => $filter['filter_id'],
				'filter_description' => $filter_description_data,
				'sort_order'         => $filter['sort_order'],
				'image'              => $image_url,
				'image_old'          => $filter['image'],
				'width'				 => $this->config->get('config_image_additional_width'),
				'height'			 => $this->config->get('config_image_additional_height')
			);
		}

		return $filter_data;
	}

	public function getTotalFilterGroups() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "filter_group`");

		return $query->row['total'];
	}
	/**
	 * upload image
	 * @author Rahul Singh
	 * @param    file key 
	 * @return   text upload image name
	 */
	public function uploadImgUsingCurl($key){
				if(NGINX_ENABLED=='1'){
				$name =  time()."_".$this->request->files['filter']['name'][$key]['image'];
				$directory = 'fillter_image';
				$file_name_with_full_path = $this->request->files['filter']['tmp_name'][$key]['image'];
				if (function_exists('curl_file_create')) { // php 5.5+
				$cFile = curl_file_create($file_name_with_full_path);
				} else { //
				$cFile = '@' . realpath($file_name_with_full_path);
				}
				$post = array('file'=> $cFile);
				$ch = curl_init();
				$target_url = STATIC_CONTENT_URL_SSL.'fileupload.php?directory='.$directory.'&filename='.$name;
				curl_setopt($ch, CURLOPT_URL,$target_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST,1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$upload_result=curl_exec ($ch);
				curl_close ($ch);
				if (FALSE === $upload_result){
				$rt['message'] = 'File not uploaded successfully.';
				$image_name='';
				}
				else{
				$image_name=$directory.'/'.$name;	
				}
				return $image_name;
			}else{
				$directory = 'fillter_image';
				$directory_complete_path = DIR_IMAGE . 'fillter_image';
				$name =  time()."_".$this->request->files['filter']['name'][$key]['image'];
				$file_name_with_full_path = $this->request->files['filter']['tmp_name'][$key]['image'];
				// Check its a directory
				if (!is_dir($directory_complete_path)) {
					@mkdir($directory_complete_path, 0777);
				 }
						if(move_uploaded_file($file_name_with_full_path, $directory_complete_path . '/' . $name)){
							$image_name=$directory.'/'.$name;	
						}else{
							$image_name='';	
						}
					return $image_name;
			}

	}
	/**
	 * gets the filter ids for comparing from CSV
	 * @author Parth Gupta
	 * @dateTime 2016-01-06T12:44:35+0530
	 * @param    text $filter_group  heading of the filter
	 * @param    text $filter_name   field value of the flter-name
	 * @return   int filter_id
	 */
	public function getFilterId($filter_group,$filter_name){
		
		$sql = "SELECT 
					f.filter_id as filter_id 
                FROM 
                	". DB_PREFIX."filter f 
                INNER JOIN
                	". DB_PREFIX."filter_description fd ON f.filter_id = fd.filter_id
				INNER JOIN ".DB_PREFIX. "filter_group_description fgd
				ON (f.filter_group_id = fgd.filter_group_id)
				WHERE fgd.name = '" . $this->db->escape(trim($filter_group)) . "' 
                  AND fd.name = '" . $this->db->escape(trim($filter_name)) . "'";

		$query = $this->db->query($sql);

		return $query->row;
	}

	/**
	 * method for get all brand name of particular filter group id
	 * @param    $filter_group_id : integer of filter_group_id
	 * @return   array of brand name of particular filter_group_id
	 * @author 	 vikas, oct 2018
	 * @message: this function used checking for duplicate brand name when user enter duplicate brand name. 
	 */
	public function getBrandNameOfParticularFilterId($filter_group_id){
		
		$sql = "SELECT 
					LOWER(fd.name) as name
                FROM 
                	". DB_PREFIX."filter f
                INNER JOIN
                	".DB_PREFIX."filter_description fd ON f.filter_id = fd.filter_id
				WHERE 
					f.filter_group_id = " .(int)$filter_group_id."
				  	AND fd.language_id = 1";

		$query = $this->db->query($sql);

		$result = array();

		if($query->num_rows){
			$result =  array_column($query->rows, 'name');
		} 

		return $result;		
	}

	/**
    * Public function to add filters change data into oc_admin_change_log DB table dynamically
    * @param: array  $data 
    * @return void
    * @author MSA Feb 2019
    */
	public function save_admin_change_log(array $data): void
	{
		//Set Data to add into admin_change_log
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
        //echo 'admin_change_data::';pr($admin_change_data); die;
        //Call dynamic static function for entry into admin change log
        CommonLib::addAdminChangeLog($this->db, $admin_change_data);
        
	}

	/**
    * Public function to add filters change data into oc_admin_change_log for various filter actions(add/edit/delete)
    * @param: array  $data 
    * @return void
    * @author MSA Feb 2019
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

			case 'addFilterGroup':
					$change_log['table_id'] 	= $change_log_action_data['table_id'];
					$change_log['table_name'] 	= 'oc_filter_group';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/addFilter';
					$change_log['old_value'] 	= 0;
					$change_log['new_value'] 	= $change_log_action_data['table_id'];
					$change_log['file_location'] = 'ModelCatalogFilter/addFilter';
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'sort_order';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/addFilter';
					$change_log['old_value'] 	= 0;
					$change_log['new_value'] 	= $change_log_action_data['sort_order'];
					$this->save_admin_change_log($change_log);
				break;
			
			case 'editFilterGroup':
					$change_log['table_id'] 	= $change_log_action_data['table_id'];
					$change_log['table_name'] 	= 'oc_filter_group';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/editFilter';
					$change_log['old_value'] 	= $change_log_action_data['table_id'];
					$change_log['new_value'] 	= $change_log_action_data['table_id'];
					$change_log['file_location'] = 'ModelCatalogFilter/editFilter';
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

			case 'addFilterGroupDescrption':
					$change_log['table_id'] 	= $change_log_action_data['table_id'];
					$change_log['table_name'] 	= 'oc_filter_group_description';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/addFilter';
					$change_log['old_value'] 	= 0;
					$change_log['new_value'] 	= $change_log_action_data['group_name'];
					$change_log['file_location'] = 'ModelCatalogFilter/addFilter';
					$this->save_admin_change_log($change_log);
				break;

			case 'editFilterGroupDescrption':
					$change_log['table_id'] 	= $change_log_action_data['table_id'];
					$change_log['table_name'] 	= 'oc_filter_group_description';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/editFilter';
					$change_log['old_value'] 	= $change_log_action_data['old_group_name'];
					$change_log['new_value'] 	= $change_log_action_data['group_name'];
					$change_log['file_location'] = 'ModelCatalogFilter/editFilter';
					if($change_log['old_value'] != $change_log['file_location']) {
						$this->save_admin_change_log($change_log);	
					}
					
				break;

			case 'deleteFilterGroup':
					$change_log['table_id'] 	= $change_log_action_data['table_id'];
					$change_log['table_name'] 	= 'oc_filter_group_description';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/deleteFilter';
					$change_log['old_value'] 	= $change_log_action_data['table_id'];
					$change_log['new_value'] 	= $change_log_action_data['table_id'];
					$change_log['file_location'] = 'ModelCatalogFilter/deleteFilter';
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'sort_order';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/deleteFilter';
					$change_log['old_value'] 	= $change_log_action_data['sort_order'];
					$change_log['new_value'] 	= $change_log_action_data['sort_order'];
					$this->save_admin_change_log($change_log);

				break;

			case 'deleteFilterGroupDescription':
					$change_log['table_id'] 	= $change_log_action_data['table_id'];
					$change_log['table_name'] 	= 'oc_filter_group_description';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/deleteFilter';
					$change_log['old_value'] 	= $change_log_action_data['group_name'];
					$change_log['new_value'] 	= $change_log_action_data['group_name'];
					$change_log['file_location'] = 'ModelCatalogFilter/deleteFilter';
					$this->save_admin_change_log($change_log);
				break;

			case 'addFilter':
					$change_log['table_id'] 	= $change_log_action_data['filter_id'];
					$change_log['table_name'] 	= 'oc_filter';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/addFilter';
					$change_log['old_value'] 	= 0;
					$change_log['new_value'] 	= $change_log_action_data['filter_id'];
					$change_log['file_location'] = 'ModelCatalogFilter/addFilter';
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['new_value'] 	= $change_log_action_data['filter_group_id'];
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'sort_order';
					$change_log['new_value'] 	= $change_log_action_data['sort_order'];
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'image';
					$change_log['new_value'] 	= $change_log_action_data['image'];
					$this->save_admin_change_log($change_log);
				break;	

			case 'addFilterDescription':	
					$change_log['table_id'] 	= $change_log_action_data['filter_id'];
					$change_log['table_name'] 	= 'oc_filter_description';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/addFilter';
					$change_log['old_value'] 	= 0;
					$change_log['new_value'] 	= $change_log_action_data['filter_id'];
					$change_log['file_location']= 'ModelCatalogFilter/addFilter';
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'name';
					$change_log['new_value'] 	= $change_log_action_data['name'];
					$this->save_admin_change_log($change_log);
				break;	

			case 'editFilter':
					$change_log['table_id'] 	= $change_log_action_data['filter_id'];
					$change_log['table_name'] 	= 'oc_filter';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/editFilter';
					$change_log['old_value'] 	= $change_log_action_data['filter_id'];
					$change_log['new_value'] 	= $change_log_action_data['filter_id'];
					$change_log['file_location'] = 'ModelCatalogFilter/editFilter';
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['old_value'] 	= $change_log_action_data['filter_group_id'];
					$change_log['new_value'] 	= $change_log_action_data['filter_group_id'];
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'sort_order';
					$change_log['old_value'] 	= $change_log_action_data['old_sort_order'];
					$change_log['new_value'] 	= $change_log_action_data['sort_order'];
					if($change_log['old_value'] != $change_log['new_value']) {
						$this->save_admin_change_log($change_log);	
					}
					

					$change_log['field_name'] 	= 'image';
					$change_log['old_value'] 	= $change_log_action_data['old_image'];
					$change_log['new_value'] 	= $change_log_action_data['image'];
					if($change_log['old_value'] != $change_log['new_value']) {
						$this->save_admin_change_log($change_log);	
					}
				break;	

			case 'editFilterDescription':	
					$change_log['table_id'] 	= $change_log_action_data['filter_id'];
					$change_log['table_name'] 	= 'oc_filter_description';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/editFilter';
					$change_log['old_value'] 	= $change_log_action_data['filter_id'];
					$change_log['new_value'] 	= $change_log_action_data['filter_id'];
					$change_log['file_location']= 'ModelCatalogFilter/editFilter';
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'name';
					$change_log['old_value'] 	= $change_log_action_data['old_name'];
					$change_log['new_value'] 	= $change_log_action_data['name'];
					if($change_log['old_value'] != $change_log['new_value']) {
						$this->save_admin_change_log($change_log);	
					}
					break;

			case 'deleteFilter':
					$change_log['table_id'] 	= $change_log_action_data['filter_id'];
					$change_log['table_name'] 	= 'oc_filter';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/deleteFilter';
					$change_log['old_value'] 	= $change_log_action_data['filter_id'];
					$change_log['new_value'] 	= $change_log_action_data['filter_id'];
					$change_log['file_location']= 'ModelCatalogFilter/deleteFilter';
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'filter_group_id';
					$change_log['old_value'] 	= $change_log_action_data['filter_group_id'];
					$change_log['new_value'] 	= $change_log_action_data['filter_group_id'];
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'name';
					$change_log['old_value'] 	= $change_log_action_data['image'];
					$change_log['new_value'] 	= $change_log_action_data['image'];
					$this->save_admin_change_log($change_log);
					break;	

			case 'deleteFilterDescription':		
					$change_log['table_id'] 	= $change_log_action_data['filter_id'];
					$change_log['table_name'] 	= 'oc_filter_description';
					$change_log['source_field'] = 'filter';
					$change_log['field_name'] 	= 'filter_id';
					$change_log['ref_url'] 		= 'ModelCatalogFilter/deleteFilter';
					$change_log['old_value'] 	= $change_log_action_data['filter_id'];
					$change_log['new_value'] 	= $change_log_action_data['filter_id'];
					$change_log['file_location']= 'ModelCatalogFilter/deleteFilter';
					$this->save_admin_change_log($change_log);

					$change_log['field_name'] 	= 'name';
					$change_log['old_value'] 	= $change_log_action_data['name'];
					$change_log['new_value'] 	= $change_log_action_data['name'];
					$change_log['file_location']= 'ModelCatalogFilter/deleteFilter';
					$this->save_admin_change_log($change_log);

					break;	
			default:
				# code...
				break;
		}

	}

}
