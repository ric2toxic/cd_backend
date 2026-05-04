<?php
class ModelCatalogCategory extends Model {

    private $_category_change_log;
    public function __construct($registry) {
        $this->registry = $registry;
        $this->_category_change_log = new ProductChangeLog($registry);
    }

	public function addCategory($data) {
        $this->event->trigger('pre.admin.category.add', $data);

		// data to update in categpry table	
		$category_data = array(); 
		$category_data['unit_id'] 		= $data['unit_id'] ?? '';
		$category_data['parent_id'] 	= (int) ( $data['parent_id'] ?? 0 );
		$category_data['top'] 			= (int) ( $data['top'] ?? 0 );
		$category_data['non_returnable']= (int) ( $data['non_returnable'] ?? 0 );
		$category_data['column'] 		= (int) ( $data['column'] ?? 0 );
		$category_data['sort_order'] 	= (int) ( $data['sort_order'] ?? 0 );
		$category_data['status']		= (int) ( $data['status'] ?? 1 ); // default enabled status
		if(!empty($data['category-filter-inventory'])){
			$category_data['filter_groups'] = implode("," , $data['category-filter-inventory']);	
		}
		$category_data['min_weight']	 = (float) ( $data['Min_Weight'] ?? 0 );
		$category_data['max_weight']	 = (float) ( $data['Max_Weight'] ?? 0 );
		$category_data['taxable']	 	 = (int) ( $data['taxable'] ?? 0 );
		$category_data['show_for_import']= (int) ( $data['show_for_import'] ?? 0 );
		$category_data['image'] 		 = $data['image'] ?? '';

		if(!empty($data['category-nonmandatoryfilter-inventory'])) {
			$category_data['non_mandatory_filter_groups'] = implode("," , $data['category-nonmandatoryfilter-inventory']);
		}
		if(!empty($data['category-naming-inventory'])) {
			$category_data['naming_filters'] = implode("," , $data['category-naming-inventory']);
		}
		
		if(isset($data['price_range_start']) && isset($data['price_range_end'])) 
	    {
	      $price_range = "";
	      if(!empty($data['price_range_start']) && !empty($data['price_range_end'])) {
	        $price_range = $data['price_range_start']."-".$data['price_range_end'];
	      }
	      $category_data['price_range'] = $this->db->escape($price_range);
	    }

    	//INSERT category table row
	    if(!empty($category_data)) {
	    	$sql = "INSERT INTO " . DB_PREFIX . "category SET ";
	    	foreach ($category_data as $key => $value) {
	    		$sql .=  "`".$key."` = '" . $this->db->escape($value) . "', ";
	    	}
	    	$sql .= " date_modified = NOW() ";
	    	$this->db->query($sql);
	    }

	    $category_id = $this->db->getLastId();

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_images WHERE category_id = '" . (int)$category_id . "'");
			
		if(isset($data['category_image']) && count($data['category_image']) > 0)
	     {	
			foreach ($data['category_image'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_images SET category_id = " . (int)$category_id . ", image_height = " . (int)$value['image_height'] . ", image_width = " . (int)$value['image_width'] . ", image = '" . $this->db->escape($value['image']) . "', sort_order = " . (int)$value['sort_order']);
			}
	     }	

		foreach ($data['category_description'] as $language_id => $value) {
                
            $this->db->query("INSERT INTO " . DB_PREFIX . "category_description 
            					SET 
            						category_id 		= '" . (int)$category_id . "', 
            						language_id 		= '" . (int)$language_id . "', 
            						name 				= '" . $this->db->escape($value['name']) . "', 
            						description 		= '" . $this->db->escape($value['description']) . "', 
            						short_description 	= '" . $this->db->escape($value['short_description']) . "', 
            						meta_title 			= '" . $this->db->escape($value['meta_title']) . "', 
            						meta_description 	= '" . $this->db->escape($value['meta_description']) . "', 
            						meta_keyword 		= '" . $this->db->escape($value['meta_keyword']) . "'"
            				);
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . (int)$level . "'");

		if (isset($data['category_filter'])) {
			foreach ($data['category_filter'] as $key=>$filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_filter_group SET category_id = '" . (int)$category_id . "', filter_group_id = '" . (int)$filter_id . "', sort_order = '" . (int)$data['category_filter_short_order'][$key] . "'");
			}
		}

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// Set which layout to use with this category
		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
		
	//set admin change data for new category
		$change_log['category_id'] 	= $category_id;
		$change_log['action'] 		= 'add';
		$change_log['unit_id'] 		= $category_data['unit_id'];
		$change_log['parent_id']	= $category_data['parent_id'];
		$change_log['non_returnable']= $category_data['non_returnable'];
		$change_log['sort_order']	= $category_data['sort_order'];
		$change_log['status']		= $category_data['status'];
		$change_log['filter_groups']= $category_data['filter_groups'];
		$change_log['min_weight']	= $category_data['min_weight'];
		$change_log['max_weight']	= $category_data['max_weight'];
		$change_log['taxable']		= $category_data['taxable'];
		$change_log['non_mandatory_filter_groups']= $category_data['non_mandatory_filter_groups'];
		$change_log['naming_filters']= $category_data['naming_filters'];
		$change_log['image']		= $category_data['image'];
		$change_log['price_range']	= $category_data['price_range'];
		$change_log['category_images']	= $data['category_image'] ?? array();
		$change_log['category_description'] = $data['category_description'];
		$change_log['category_to_store'] = $data['category_store'];
		$this->admin_change_log_action($change_log);

		$this->cache->delete('category');

		$this->event->trigger('post.admin.category.add', $category_id);
       
        return $category_id;
	}

	public function editCategory($category_id, $data) {
		
		//admin change log
		$change_log = array();

		$this->event->trigger('pre.admin.category.edit', $data);

        if( !empty($data['changes_data'])){
            $source_field = 'category_edit';
            $changes_data = json_decode($data['changes_data'],true);
            $this->_category_change_log->recordLogs($category_id, $changes_data, $source_field, 'oc_category');
        }

	// data to update in categpry table	
		$category_data = array(); 
		$category_data['unit_id'] 		= $data['unit_id'] ?? '';
		$category_data['parent_id'] 	= (int) ( $data['parent_id'] ?? 0 );
		$category_data['top'] 			= (int) ( $data['top'] ?? 0 );
		$category_data['non_returnable']= (int) ( $data['non_returnable'] ?? 0 );
		$category_data['column'] 		= (int) ( $data['column'] ?? 0 );
		$category_data['sort_order'] 	= (int) ( $data['sort_order'] ?? 0 );
		$category_data['status']		= (int) ( $data['status'] ?? 1 ); // default enabled status
		if(!empty($data['category-filter-inventory'])){
			$category_data['filter_groups'] = implode("," , $data['category-filter-inventory']);	
		}
		$category_data['min_weight']	 = (float) ( $data['Min_Weight'] ?? 0 );
		$category_data['max_weight']	 = (float) ( $data['Max_Weight'] ?? 0 );
		$category_data['taxable']	 	 = (int) ( $data['taxable'] ?? 0 );
		$category_data['show_for_import']= (int) ( $data['show_for_import'] ?? 0 );
		$category_data['image'] 		 = $data['image'] ?? '';

		if(!empty($data['category-nonmandatoryfilter-inventory'])) {
			$category_data['non_mandatory_filter_groups'] = implode("," , $data['category-nonmandatoryfilter-inventory']);
		}
		if(!empty($data['category-naming-inventory'])) {
			$category_data['naming_filters'] = implode("," , $data['category-naming-inventory']);
		}
		
		if(isset($data['price_range_start']) && isset($data['price_range_end'])) 
	    {
	      $price_range = "";
	      if(!empty($data['price_range_start']) && !empty($data['price_range_end'])) {
	        $price_range = $data['price_range_start']."-".$data['price_range_end'];
	      }
	      $category_data['price_range'] = $this->db->escape($price_range);
	    }

	    // for admin change log
	    $old_category_data = $this->getCategory($category_id);

    	//update category table fields
	    if(!empty($category_data)) {
	    	$sql = "UPDATE " . DB_PREFIX . "category SET ";
	    	foreach ($category_data as $key => $value) {
	    		$sql .=  "`".$key."` = '" . $this->db->escape($value) . "', ";
	    	}
	    	$sql .= " date_modified = NOW() ";
	    	$sql .= " WHERE category_id = '" . (int)$category_id . "' ";
	    	$this->db->query($sql);
	    }

		$change_log['category_id'] 	= $category_id;
		$change_log['action'] 		= 'edit';
		$change_log['unit_id'] 		= $category_data['unit_id'];
		$change_log['parent_id']	= $category_data['parent_id'];
		$change_log['non_returnable']= $category_data['non_returnable'];
		$change_log['sort_order']	= $category_data['sort_order'];
		$change_log['status']		= $category_data['status'];
		$change_log['filter_groups']= $category_data['filter_groups'];
		$change_log['min_weight']	= $category_data['min_weight'];
		$change_log['max_weight']	= $category_data['max_weight'];
		$change_log['taxable']		= $category_data['taxable'];
		$change_log['non_mandatory_filter_groups']= $category_data['non_mandatory_filter_groups'];
		$change_log['naming_filters']= $category_data['naming_filters'];
		$change_log['image']		= $category_data['image'];
		$change_log['price_range']	= $category_data['price_range'];
		$change_log['category_images']	= $data['category_image'] ?? array();
		$change_log['category_description'] = $data['category_description'];
		$change_log['category_to_store'] = $data['category_store'];
		$this->admin_change_log_action($change_log, $old_category_data);
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_images WHERE category_id = '" . (int)$category_id . "'");
			
		if(isset($data['category_image']) && count($data['category_image']) > 0)
	     {	
			foreach ($data['category_image'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_images SET category_id = " . (int)$category_id . ", image_height = " . (int)$value['image_height'] . ", image_width = " . (int)$value['image_width'] . ", image = '" . $this->db->escape($value['image']) . "', sort_order = " . (int)$value['sort_order']);
			}
	     }		

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");
		foreach ($data['category_description'] as $language_id => $value) {
                        
			$this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', short_description = '" . $this->db->escape($value['short_description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}
		
		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE path_id = '" . (int)$category_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $category_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' AND level < '" . (int)$category_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {

					$this->db->query("UPDATE `" . DB_PREFIX . "category_path` SET level = '" . (int)$level . "' WHERE category_id = '" . (int)$category_path['category_id'] . "' AND `path_id` = '" . (int)$path_id . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			//$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', level = '" . (int)$level . "'");
			$this->db->query("UPDATE `" . DB_PREFIX . "category_path` SET level = '" . (int)$level . "' WHERE category_id = '" . (int)$category_id . "' AND `path_id` = '" . (int)$category_id . "' ");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_filter_group WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_filter'])) {
			foreach ($data['category_filter'] as $key=>$filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_filter_group SET category_id = '" . (int)$category_id . "', filter_group_id = '" . (int)$filter_id . "', sort_order = '" . (int)$data['category_filter_short_order'][$key] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
	   
		$this->cache->delete('category');
        //Save translation from reverie
        
		$this->event->trigger('post.admin.category.edit', $category_id);
	}

	public function deleteCategory($category_id) {
		$this->event->trigger('pre.admin.category.delete', $category_id);

		// for admin change log entry
			$change_log['category_id'] 	= $category_id;
			$change_log['action'] 		= 'delete';
		    $old_category_data = $this->getCategory($category_id);
			$this->admin_change_log_action($change_log, $old_category_data);

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path WHERE path_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteCategory($result['category_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'");

		$this->cache->delete('category');

		$this->event->trigger('post.admin.category.delete', $category_id);
	}

	public function repairCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $category) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category['category_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			//$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$category['category_id'] . "', level = '" . (int)$level . "'");
			$this->db->query("UPDATE `" . DB_PREFIX . "category_path` SET level = '" . (int)$level . "' WHERE category_id = '" . (int)$category['category_id'] . "' AND `path_id` = '" . (int)$category['category_id'] . "'");

			$this->repairCategories($category['category_id']);
		}
	}

	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path, (SELECT DISTINCT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "') AS keyword FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCategoriesByParent($parent_id) {

		$sql = "SELECT c.category_id, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id IN (" . $parent_id . ") AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)";
		$query = $this->db->query($sql);
		return $query->rows;
	}
    
	public function getTotalCategories($parent_id = NULL) {
                
                $where = '';
                if(isset($parent_id)){ 
                   $where = " WHERE parent_id = " . $parent_id; 
                } 
            
                $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category " . $where;
                $query = $this->db->query($sql);
                return $query->row['total'];
	}
        
        public function getCategories($data = array(), $store_id = 0) {
                
                $sql = "SELECT cp.category_id AS category_id, "
                        . "GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, "
                        . "c1.parent_id, c1.sort_order "
                        . "FROM " . DB_PREFIX . "category_path cp "
                        . "LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) "
                        . "LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) "
                        . "LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) "
                        . "LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) "
                        . "LEFT JOIN " . DB_PREFIX . "category_to_store cs ON (cp.category_id = cs.category_id) "
                        . "WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' "
                        . "AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' "
                        . "AND cs.store_id = '".(int)$store_id."' ";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_category_id']) && !empty($data['filter_category_id'])) {
			$sql .= " AND cd2.category_id = '" . $this->db->escape($data['filter_category_id']) . "'";
		}

		$sql .= " GROUP BY cp.category_id";

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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
        
        public function getChildCategories($parent_id = 0,$data = array()) {
                
                $sql = "SELECT C.category_id AS category_id, CD.name, C.sort_order FROM " . DB_PREFIX . "category AS C "
                        . "LEFT JOIN " . DB_PREFIX . "category_description AS CD"
                        . " ON C.category_id = CD.category_id "
                        . " WHERE CD.language_id = 1 AND C.parent_id = '" . (int)$parent_id."'";
                
                $sort_data = array(
			'name',
			'sort_order'
		);
                
                
                if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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
        
        public function getParentCategories($category_id){
            
            $result = array();
            
            $sql = "SELECT * FROM " . DB_PREFIX ."category_path WHERE category_id = " . $category_id . " AND path_id > 0 ORDER BY level";
            $query = $this->db->query($sql);
            
            if($query->num_rows > 0){
                foreach( $query->rows as $row ){
                    
                    $where = ' WHERE 1'; 
                    $where .= ' AND CD.language_id = 1'; 
                    $where .= ' AND C.category_id = ' . $row['path_id'];
                    
                    $sql = "SELECT C.category_id AS category_id, CD.name FROM " . DB_PREFIX . "category AS C "
                        . "LEFT JOIN " . DB_PREFIX . "category_description AS CD"
                        . " ON C.category_id = CD.category_id"
                        . $where ; 
                    $query = $this->db->query($sql);
                    
                    $parent_data['id']   = $query->row['category_id'];
                    $parent_data['name'] = $query->row['name'];
                    
                    $result[] = $parent_data;
                }
                 
            }
            return $result;
        }
        
        public function getParentCategory($category_id){
            
            $result = array();
            
            $sql = "SELECT * FROM " . DB_PREFIX ."category AS C "
                    . " LEFT JOIN " . DB_PREFIX . "category_description AS CD "
                    . " ON C.parent_id = CD.category_id"
                    . " WHERE C.category_id = " . $category_id . " "
                    . " AND language_id = 1";
            $query = $this->db->query($sql);
            if($query->num_rows > 0){ 
                $result['parent_id'] = $query->row['parent_id'];
                $result['parent_name'] = $query->row['name'];
            }
            return $result;
            
        }
        
        public function getCategoryDescriptions($category_id) {
		$category_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description'],
				'short_description'=> $result['short_description']
			);
		}

		return $category_description_data;
	}

	public function getCategoryFilters($category_id) {
		$category_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "' ORDER BY sort_order ASC");

		foreach ($query->rows as $result) {
			$category_filter_data[] = $result['filter_id'];
		}

		return $category_filter_data;
	}
	public function getCategoryFilterGroups($category_id) {
		$category_filter_group_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_filter_group WHERE category_id = '" . (int)$category_id . "' ORDER BY sort_order ASC");

		foreach ($query->rows as $result) {
			$category_filter_group_data[] = $result['filter_group_id'];
		}

		return $category_filter_group_data;
	}

	public function getCategoryStores($category_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "' AND store_id IN (".WSB_STORES_ID .")");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}

	public function getCategoryLayouts($category_id) {
		$category_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id IN (".WSB_STORES_ID .")");

		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $category_layout_data;
	}
        
        public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category_to_layout WHERE layout_id = '" . (int)$layout_id . "' AND store_id IN (".WSB_STORES_ID .")");

		return $query->row['total'];
	}

	public function getcategoryfiltersdata($category_id){
		$q = "
			SELECT 
				f.filter_id, 
				GROUP_CONCAT(fd.name SEPARATOR ', ') as filter_name,
				(
				SELECT name 
				FROM ".DB_PREFIX."filter_group_description fgd
				WHERE fl.filter_group_id = fgd.filter_group_id
					AND fgd.language_id = 1
				) as group_name
			FROM 
				".DB_PREFIX."category_filter f 
			INNER JOIN 
				".DB_PREFIX."filter_description fd ON f.filter_id = fd.filter_id AND fd.language_id = 1 
			INNER JOIN 
				".DB_PREFIX."filter fl ON f.filter_id = fl.filter_id
			WHERE 
				f.category_id =".(int)$category_id." 
			GROUP BY 
				group_name";

		$query = $this->db->query($q);
		return $query->rows;
	}

	public function dynamicmetatags(){

		$this->load->model('catalog/filter');

		if($this->request->post['category_description'][1]['meta_description'] == '') {

			$filter_ids = $this->request->post['category_filter'];
			$filters = array();

			foreach ($filter_ids as $id) {
				$data = $this->model_catalog_filter->getFilter($id);
				$filters[] = $data['name'];
			}

			$filter = implode(',', $filters);
			$this->request->post['category_description'][1]['meta_description'] = sprintf($this->language->get('meta_description'), $this->request->post['category_description'][1]['name'], $this->request->post['category_description'][1]['name'], $filter);
		}

		if($this->request->post['category_description'][1]['meta_title'] == ''){

			$this->request->post['category_description'][1]['meta_title'] = sprintf($this->language->get('meta_title'), $this->request->post['category_description'][1]['name'], $this->request->post['category_description'][1]['name']);
		}

		if($this->request->post['category_description'][1]['meta_keyword'] == ''){

			$this->request->post['category_description'][1]['meta_keyword'] = sprintf($this->language->get('meta_keyword'), $this->request->post['category_description'][1]['name']);
		}
	}
	public function get_store_data(){
		$sql = "SELECT * from " . DB_PREFIX . "store";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function store_data(){

		if($this->request->post['store_id']){
			$store_id = $this->request->post['store_id'];
		}else{
			$store_id = '';
		}
		if($this->request->get['category_id']){
			$category_id = $this->request->get['category_id'];
		}else{
			$category_id = '';
		}
		if($this->request->post['Language']){
			$language = (int)$this->request->post['Language'];
		}else{
			$language = '';
		}
		if($this->request->post['Meta_Tag_Title']){
			$meta_title = $this->request->post['Meta_Tag_Title'];
		}else{
			$meta_title = '';
		}

		if($this->request->post['Description']){
			$description = $this->request->post['Description'];
		}else{
			$description = '';
		}

		if($this->request->post['Meta_Keyword']){
			$meta_keyword = $this->request->post['Meta_Keyword'];
		}else{
			$meta_keywords = '';
		}
		if($this->request->post['Meta_Description']){
			$meta_description = $this->request->post['Meta_Description'];
		}else{
			$meta_description = '';
		}
		$c_id = "SELECT category_id,store_id from " . DB_PREFIX . "category_storeinfo WHERE category_id= '". (int)$category_id ."' AND store_id = '". (int)$store_id ."'";

		$c_id    =  $this->db->query($c_id);
		$cate_id =	(int)$c_id->row['category_id'];
		$s_id    =	(int)$c_id->row['store_id'];

		if(($cate_id == $category_id)&&($s_id == $store_id)){
			$sql = "UPDATE " . DB_PREFIX . "category_storeinfo SET store_id = '". (int)$store_id ."', category_id = '". (int)$category_id ."', language = '". $language ."', meta_title = '". $this->db->escape($meta_title) ."', description = '". $this->db->escape($description) ."', meta_keywords = '".  $this->db->escape($meta_keyword) ."', meta_description = '". $this->db->escape($meta_description) ."', modify = NOW() WHERE category_id= '". (int)$category_id ."' AND store_id = '". (int)$store_id ."'";
		}else{
			$sql = "INSERT INTO " . DB_PREFIX . "category_storeinfo SET store_id = '". (int)$store_id ."', category_id = '". (int)$category_id ."', language = '". $language ."', meta_title = '". $this->db->escape($meta_title) ."', description = '". $this->db->escape($description) ."', meta_keywords = '".  $meta_keyword ."', meta_description = '". $this->db->escape($meta_description) ."', created = NOW(), modify = NOW()";	
		}
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function get_category_store_data(){
		
		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
            $sql = "SELECT * from " . DB_PREFIX . "category_storeinfo WHERE category_id= '". (int)$category_id ."' AND store_id IN (".WSB_STORES_ID .")";
            $query = $this->db->query($sql);
            return $query->rows;
        } else
            return false;
	}
	public function get_ajax_category_store_data($store_id,$category_id){
		$sql = "SELECT * from " . DB_PREFIX . "category_storeinfo WHERE category_id= '". (int)$category_id ."' AND store_id= '".(int)$store_id."'";

		$query = $this->db->query($sql);
		return $query->rows;	

	}
    
    
    /**
     * Method to return an array of all the unique children category ids
     * given parent category ids. It goes upto infinite level deep.
     * Input(s): 
     * @param array  parent_ids
     * @param boolean (optional defaulted to false) include_parent (Return array to include parent_id or not)
     * Output: Array of category_ids
     * Author: Madhur
     */
    public function getChildCategoryIds($parent_ids, $include_parent = false) {
        
    	if (empty($parent_ids)){
    		return array();
    	}

        $breakLoop = false;
        $return_ids = array();
        
        // Remove duplicate from parent_ids
        $parent_ids = array_unique($parent_ids, SORT_NUMERIC);
        
        if ($include_parent) {
            $return_ids = $parent_ids;
        }

        // This array is needed to be filled to prevent rechecking a parent id. To avoid circular loop.
        $already_checked_ids = array();        
        
        while (!$breakLoop) {
            
            $sql = "SELECT category_id FROM " . DB_PREFIX . "category 
                    WHERE parent_id IN (" . implode(', ', $parent_ids) . ")";
            $query = $this->db->query($sql);
            
            // Insert Present Level parent ids into already checked list
            $already_checked_ids = array_merge($already_checked_ids, $parent_ids); 
            
            if ($query->num_rows) {
                
                foreach ($query->rows as $row) {
                    
                    $child_cat_id = (int)$row['category_id'];

                    if ( !in_array( $child_cat_id, $return_ids ) ) { // getting the childern if they are not already stored previously
                        $return_ids[] = $child_cat_id;
                    }
                    
                    // Initialize next level parent ids
                    $parent_ids = array();
                    
                    // Every child is a potential next level parent id unless it has already been checked for
                    if ( !in_array( $child_cat_id, $already_checked_ids ) ) {
                        $parent_ids[] = $child_cat_id;
                    }
                }
                
                // If no more parent_ids at next level to check
                if ( empty($parent_ids) ) {
                    $breakLoop = true;
                }
            } else { // no more children found
                $breakLoop = true;
            }                
        }
        
        return $return_ids;
    }

	public function getCategoryImages($category_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_images WHERE category_id = '" . (int)$category_id . "'")->rows;
		return $query;
	}
	
	/*
	 * getting all the unit ids and unit names for display in dropdown list in category form page 
	 * and product form page
	 * */
	public function getUnitIdsAndNames(){
		$sql = "SELECT CONCAT(UCASE(super_unit),'-->', UCASE(base_unit)) as unit_name,
					   (unit_id) as unit_id
					FROM ". DB_PREFIX ."units 
				  WHERE status = 1";
		$query = $this->db->query($sql);
		if($query->num_rows) {
			return $this->db->query($sql)->rows;
		}
	}

	/**
    * Public function to add/edit category changes data into oc_admin_change_log for various option actions(add/edit/delete)
    * @param: array  $change_log_action_data 
    * @return void
    * @author MSA May 2019
    */
	public function admin_change_log_action(array $change_log_action_data, array $old_category_data = array())
	{
		$change_log = array();
		$change_log['user_id'] 	= $this->user->getId();
		$change_log['name'] 	= $this->user->getGroupName();
		$change_log['username'] = $this->user->getUserName()['username'];
		$change_log['user_type']= $this->user->getGroupName();

		$action = $change_log_action_data['action'] ?? '';

		switch ($action) {
			
			case 'add':
				$change_log['file_location']= 'ModelCatalogCategory/addCategory';
				$change_log['ref_url']		= 'ModelCatalogCategory/addCategory';
				$change_log['source_field'] = 'category_edit';
				$change_log['table_name']	= 'oc_category';
				$change_log['table_id']	 	= $change_log_action_data['category_id'];
				
				if(!empty($change_log_action_data['unit_id'])){
					$change_log['field_name']  = 'unit_id';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['unit_id'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['parent_id'])){
					$change_log['field_name']  = 'parent';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['parent_id'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['non_returnable'])){
					$change_log['field_name']  = 'non_returnable';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['non_returnable'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['sort_order'])){
					$change_log['field_name']  = 'sort_order';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['sort_order'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['status'])){
					$change_log['field_name']  = 'status';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['status'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['filter_groups'])){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['filter_groups'];
					$change_log['comment']     = 'Category Filter Groups';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['min_weight'])){
					$change_log['field_name']  = 'weight';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['min_weight'];
					$change_log['comment']     = 'Min Weight';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['max_weight'])){
					$change_log['field_name']  = 'weight';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['max_weight'];
					$change_log['comment']     = 'Max Weight';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['non_mandatory_filter_groups'])){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['non_mandatory_filter_groups'];
					$change_log['comment']     = 'Non Mandatory Filter Groups';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['naming_filters'])){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['naming_filters'];
					$change_log['comment']     = 'Naming filters';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['image'])){
					$change_log['field_name']  = 'image';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['image'];
					$change_log['comment']     = 'Category image';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['price_range'])){
					$change_log['field_name']  = 'price';
					$change_log['old_value']   = '';
					$change_log['new_value']   = $change_log_action_data['price_range'];
					$change_log['comment']     = 'Price range';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['category_store'])) {
					$change_log['field_name']  = 'stores';
					$change_log['old_value']   = '';
					$change_log['new_value']   = serialize($change_log_action_data['category_store']);
					$change_log['comment']     = 'Category Stores';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['category_images'])) {
					$change_log['field_name']  = 'image';
					$change_log['old_value']   = '';
					$change_log['new_value']   = serialize($change_log_action_data['category_images']);
					$change_log['comment']     = 'Category Images';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['category_description'])) {
					foreach ($change_log_action_data['category_description'] as $key => $value) {
						if(!empty($value['name'])) {
							$change_log['field_name']  = 'description';
							$change_log['old_value']  = '';
							$change_log['new_value']  = $value['name'];
							$change_log['comment']  = 'Category Description';
							$this->save_admin_change_log($change_log);
						}
					}
				}
				break;

			case 'edit':

				$change_log['file_location']= 'ModelCatalogCategory/editCategory';
				$change_log['ref_url']		= 'ModelCatalogCategory/editCategory';
				$change_log['source_field'] = 'category_edit';
				$change_log['table_name']	= 'oc_category';
				$change_log['table_id']	 	= $change_log_action_data['category_id'];
				
				if($change_log_action_data['unit_id']!= $old_category_data['unit_id']){
					$change_log['field_name']  = 'unit_id';
					$change_log['old_value']   = $old_category_data['unit_id'];
					$change_log['new_value']   = $change_log_action_data['unit_id'];
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['parent_id']!= $old_category_data['parent_id']){
					$change_log['field_name']  = 'parent';
					$change_log['old_value']   = $old_category_data['parent_id'];
					$change_log['new_value']   = $change_log_action_data['parent_id'];
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['non_returnable']!= $old_category_data['non_returnable']){
					$change_log['field_name']  = 'non_returnable';
					$change_log['old_value']   = $old_category_data['non_returnable'];
					$change_log['new_value']   = $change_log_action_data['non_returnable'];
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['sort_order']!= $old_category_data['sort_order']){
					$change_log['field_name']  = 'sort_order';
					$change_log['old_value']   = $old_category_data['sort_order'];
					$change_log['new_value']   = $change_log_action_data['sort_order'];
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['status']!= $old_category_data['status']){
					$change_log['field_name']  = 'status';
					$change_log['old_value']   = $old_category_data['status'];
					$change_log['new_value']   = $change_log_action_data['status'];
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['filter_groups']!= $old_category_data['filter_groups']){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = $old_category_data['filter_groups'];
					$change_log['new_value']   = $change_log_action_data['filter_groups'];
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['min_weight']!= $old_category_data['min_weight']){
					$change_log['field_name']  = 'weight';
					$change_log['old_value']   = $old_category_data['min_weight'];
					$change_log['new_value']   = $change_log_action_data['min_weight'];
					$change_log['comment']     = 'Min Weight';
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['max_weight']!= $old_category_data['max_weight']){
					$change_log['field_name']  = 'weight';
					$change_log['old_value']   = $old_category_data['max_weight'];
					$change_log['new_value']   = $change_log_action_data['max_weight'];
					$change_log['comment']     = 'Max Weight';
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['non_mandatory_filter_groups']!= $old_category_data['non_mandatory_filter_groups']){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = $old_category_data['non_mandatory_filter_groups'];
					$change_log['new_value']   = $change_log_action_data['non_mandatory_filter_groups'];
					$change_log['comment']     = 'Non Mandatory Filter Groups';
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['naming_filters']!= $old_category_data['naming_filters']){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = $old_category_data['naming_filters'];
					$change_log['new_value']   = $change_log_action_data['naming_filters'];
					$change_log['comment']     = 'Naming filters';
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['image']!= $old_category_data['image']){
					$change_log['field_name']  = 'image';
					$change_log['old_value']   = $old_category_data['image'];
					$change_log['new_value']   = $change_log_action_data['image'];
					$change_log['comment']     = 'Category image';
					$this->save_admin_change_log($change_log);
				}
				if($change_log_action_data['price_range']!= $old_category_data['price_range']){
					$change_log['field_name']  = 'price';
					$change_log['old_value']   = $old_category_data['price_range'];
					$change_log['new_value']   = $change_log_action_data['price_range'];
					$change_log['comment']     = 'Price range';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['category_to_store'])) {
					$old_category_stores = $this->getCategoryStores($change_log_action_data['category_id']);
					$change_log['field_name']  = 'stores';
					$change_log['old_value']   = serialize($old_category_stores);
					$change_log['new_value']   = serialize($change_log_action_data['category_to_store']);
					$change_log['comment']     = 'Category Stores';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['category_images'])) {
					$old_category_images = $this->getCategoryImages($change_log_action_data['category_id']);			
					$change_log['field_name']  = 'image';
					$change_log['old_value']   = serialize($old_category_images);
					$change_log['new_value']   = serialize($change_log_action_data['category_images']);
					$change_log['comment']     = 'Category Images';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($change_log_action_data['category_description'])) {
					$old_category_descriptions = $this->getCategoryDescriptions($change_log_action_data['category_id']);
					$new_category_descriptions = $change_log_action_data['category_description'];
					
					foreach ($change_log_action_data['category_description'] as $key => $value) {
						if(
							isset($old_category_descriptions[$key]['name'])
							&&
							$old_category_descriptions[$key]['name'] != $value['name']
						) {
							$change_log['field_name']  = 'description';
							$change_log['old_value']  = $old_category_descriptions[$key]['name'];
							$change_log['new_value']  = $value['name'];
							$change_log['comment']  = 'Category Description';
							$this->save_admin_change_log($change_log);
						}else if(!empty($value['name']) && empty($old_category_descriptions[$key]['name'])){
							$change_log['field_name']  = 'description';
							$change_log['old_value']  = '';
							$change_log['new_value']  = $value['name'];
							$change_log['comment']  = 'Category Description';
							$this->save_admin_change_log($change_log);
						}
					}
				}

				break;

			case 'delete':
				
				$change_log['file_location']= 'ModelCatalogCategory/deleteCategory';
				$change_log['ref_url']		= 'ModelCatalogCategory/deleteCategory';
				$change_log['source_field'] = 'category_edit';
				$change_log['table_name']	= 'oc_category';
				$change_log['table_id']	 	= $change_log_action_data['category_id'];
				
				if(!empty($old_category_data['unit_id'])){
					$change_log['field_name']  = 'unit_id';
					$change_log['old_value']   = $old_category_data['unit_id'];
					$change_log['new_value']   = $old_category_data['unit_id'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['parent_id'])){
					$change_log['field_name']  = 'parent';
					$change_log['old_value']   = $old_category_data['parent_id'];
					$change_log['new_value']   = $old_category_data['parent_id'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['non_returnable'])){
					$change_log['field_name']  = 'non_returnable';
					$change_log['old_value']   = $old_category_data['non_returnable'];
					$change_log['new_value']   = $old_category_data['non_returnable'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['sort_order'])){
					$change_log['field_name']  = 'sort_order';
					$change_log['old_value']   = $old_category_data['sort_order'];
					$change_log['new_value']   = $old_category_data['sort_order'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['status'])){
					$change_log['field_name']  = 'status';
					$change_log['old_value']   = $old_category_data['status'];
					$change_log['new_value']   = $old_category_data['status'];
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['filter_groups'])){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = $old_category_data['filter_groups'];
					$change_log['new_value']   = $old_category_data['filter_groups'];
					$change_log['comment']     = 'Category Filter Groups';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['min_weight'])){
					$change_log['field_name']  = 'weight';
					$change_log['old_value']   = $old_category_data['min_weight'];
					$change_log['new_value']   = $old_category_data['min_weight'];
					$change_log['comment']     = 'Min Weight';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['max_weight'])){
					$change_log['field_name']  = 'weight';
					$change_log['old_value']   = $old_category_data['max_weight'];
					$change_log['new_value']   = $old_category_data['max_weight'];
					$change_log['comment']     = 'Max Weight';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['non_mandatory_filter_groups'])){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = $old_category_data['non_mandatory_filter_groups'];
					$change_log['new_value']   = $old_category_data['non_mandatory_filter_groups'];
					$change_log['comment']     = 'Non Mandatory Filter Groups';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['naming_filters'])){
					$change_log['field_name']  = 'filter_groups';
					$change_log['old_value']   = $old_category_data['naming_filters'];
					$change_log['new_value']   = $old_category_data['naming_filters'];
					$change_log['comment']     = 'Naming filters';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['image'])){
					$change_log['field_name']  = 'image';
					$change_log['old_value']   = $old_category_data['image'];
					$change_log['new_value']   = $old_category_data['image'];
					$change_log['comment']     = 'Category image';
					$this->save_admin_change_log($change_log);
				}
				if(!empty($old_category_data['price_range'])){
					$change_log['field_name']  = 'price';
					$change_log['old_value']   = $old_category_data['price_range'];
					$change_log['new_value']   = $old_category_data['price_range'];
					$change_log['comment']     = 'Price range';
					$this->save_admin_change_log($change_log);
				}
				$old_category_stores = $this->getCategoryStores($change_log_action_data['category_id']);
				if(!empty($old_category_stores)) {
					$change_log['field_name']  = 'stores';
					$change_log['old_value']   = serialize($old_category_stores);
					$change_log['new_value']   = serialize($old_category_stores);
					$change_log['comment']     = 'Category Stores';
					$this->save_admin_change_log($change_log);
				}
				$old_category_images = $this->getCategoryImages($change_log_action_data['category_id']);
				if(!empty($change_log_action_data['category_images'])) {
					$change_log['field_name']  = 'image';
					$change_log['old_value']   = serialize($old_category_images);
					$change_log['new_value']   = serialize($old_category_images);
					$change_log['comment']     = 'Category Images';
					$this->save_admin_change_log($change_log);
				}
				$old_category_descriptions = $this->getCategoryDescriptions($change_log_action_data['category_id']);
				if(!empty($old_category_descriptions)) {
					foreach ($old_category_descriptions as $key => $value) {
						if(!empty($value['name'])) {
							$change_log['field_name']  = 'description';
							$change_log['old_value']  = $value['name'];
							$change_log['new_value']  = $value['name'];
							$change_log['comment']  = 'Category Description';
							$this->save_admin_change_log($change_log);
						}
					}
				}
				break;

			default:
				break;
		}
	}
    
	/**
    * Public function to add category change data into oc_admin_change_log DB table 
	*  	dynamically
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

	/**
    * Public function to get all categories name to generate tree using parent id
    * @param: array  $elements 
    * @param: int    $parentId 
    * @return array  [category tree]
    * @author MSA Oct 2019
    */
	public function getCategoriesNameForTree() {

		$sql = "SELECT 
						c.category_id, 
						c.parent_id, 
						cd.name 
				FROM " . DB_PREFIX . "category c 
				INNER JOIN " . DB_PREFIX . "category_description cd 
						ON (c.category_id = cd.category_id) 
				INNER JOIN " . DB_PREFIX . "category_to_store c2s 
						ON (c.category_id = c2s.category_id) 
				WHERE 
					cd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
					AND 
					c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  
					AND 
					c.status = '1' 
				ORDER BY c.sort_order
				";
		$query = $this->db->query($sql);
		if($query->num_rows){
			return $query->rows;
		}
		return array();
	}

	/**
    * Public function to create category tree using parent id
    * @param: array  $elements 
    * @param: int    $parentId 
    * @return array  [category tree]
    * @author MSA Oct 2019
    */
	public function buildCategoryTree(array $elements, $parentId = 0) {
	    $branch = array();

	    foreach ($elements as $element) {
	        if ($element['parent_id'] == $parentId) {
	            $children = $this->buildCategoryTree($elements, $element['category_id']);
	            if ($children) {
	                $element['children'] = $children;
	            }
	            $branch[] = $element;
	        }
	    }
	    return $branch;
	}


}
