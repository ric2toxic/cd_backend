<?php
class ModelCatalogCategory extends Model {
	public function getCategory($category_id, $with_all_language = 0) {
		$sql = "SELECT DISTINCT *
				FROM " . DB_PREFIX . "category c
				LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
				LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
				WHERE c.category_id = '" . (int)$category_id . "'
				AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = 1";
		$query = $this->db->query($sql);
		return $query->row;
	}
        
        /**
         * getCategoriesNameFromCategoryIds
         * @author Vishnu Shekhawat
         * @description return category name with category ids
         * @param array $category_ids
         * @return array array(category_id=>category_name) 
         */
	public function getCategoriesNameFromCategoryIds(array $category_ids) {
            
            $category = array();
            if(empty($category_ids)){
                return $category;
            }
            $category_ids = implode(',', $category_ids);
            $sql = "SELECT  cd.category_id, cd.name 
                                    FROM " . DB_PREFIX . "category_description cd
                                    WHERE cd.category_id IN (" . $category_ids . ")
                                    AND cd.language_id = " . (int) $this->config->get('config_language_id') . "";

            $query = $this->db->query($sql);

            
            foreach ($query->rows as $result) {
                $category[$result['category_id']] = $result['name'];
            }
            
            return $category;
        }

	public function getCategories($parent_id) {
		$data = array();

		// Need to sanitize the input string, which is supposed to be a comma separated string of "int" product_id(s)
        // So, we explode back to array. array_map to convert them to integers (to prevent SQL injection)
        // Afterwards, we array_filter it out to remove invalid values. Then array_unique to remove duplicates

        $parent_id = array_unique(array_filter(array_map('intval', explode(',', $parent_id)), function($v) {return $v > 0;}));

		if(!empty($parent_id)){

			$sql = "SELECT * FROM " . DB_PREFIX . "category c
	                  LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
	                  LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
	                WHERE c.parent_id IN (" . implode(',', $parent_id) . ")
	                  AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
	                  AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
	                  AND c.status = '1'
	                ORDER BY c.sort_order, LCASE(cd.name)";
			$query = $this->db->query($sql);
			if($query->num_rows > 0){
				$data = $query->rows;
			}
		}

		return $data;
	}

	public function getProductModifiedDate($data) {

        $sql = "SELECT date(p.date_modified) as p_modified, MAX(p2c.product_id) as pid FROM " . DB_PREFIX . "product_to_category p2c ";
        $sql .= " INNER JOIN (select date_modified, product_id from " . DB_PREFIX . "product WHERE status = '1' AND date_available <= NOW() AND is_single = 0  ORDER BY sort_order DESC";
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        $sql .= ") as p ON (p2c.product_id = p.product_id) INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ";

        $sql .= " WHERE p2c.category_id = ".$data['filter_category_id']. "  AND p2s.store_id = ".(int)$this->config->getStoreIdForSql()." ;";

		$query = $this->db->query($sql);
		if($query->num_rows)
		    return $query->row['p_modified'];
        else
		    return null;
	}

	public function getCategoryFilters($category_id) {
		$implode = array();
		//Original - get filter of a category, commented by Rakesh
		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if ($implode) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fgd.description, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f INNER JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'description'    => $filter_group['description'],
						'filter'          => $filter_data
					);
				}
			}
		}

		return $filter_group_data;
	}

	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}

	/**
	 * Added by Rakesh
	 */

	public function getSelectFilters($filters){
		$filter_data = array();

		if(is_array($filters) && count($filters) > 0 && $filters[0] != '') {
			$sql = "
					SELECT 
						f.filter_id, 
						fd.name, 
						fgd.name as group_name 
					FROM 
						" . DB_PREFIX . "filter f 
					INNER JOIN
						".DB_PREFIX."filter_description AS fd
						ON f.filter_id = fd.filter_id
						AND fd.language_id = ".(int)$this->config->get('config_language_id')."
					INNER JOIN  
						" . DB_PREFIX . "filter_group_description fgd 
						ON f.filter_group_id = fgd.filter_group_id 
						AND fgd.language_id = ".(int)$this->config->get('config_language_id')."
					WHERE 
						f.filter_id IN (" . implode(',', $filters) . ") 
				    "; 

			$filter_query = $this->db->query($sql);

			foreach ($filter_query->rows as $filter) {
				$filter_data[] = array(
									'filter_id'  => $filter['filter_id'],
									'name'       => $filter['name'],
									'group_name' => $filter['group_name']
								);
			}
		}

		return $filter_data;
	}

    /**
     * Get the select options data
     * @param array $options
     * @return array
     */
	public function getSelectOptions(array $options) : array {
		$option_data = array();
		// Sanitizing $options input array
        $opt_san = array_unique(array_filter(array_map('intval', $options), function($v) {return $v > 0;}));
        
        if ( !empty($opt) ) {
            $sql = "SELECT ovd.name, ovd.option_value_id, od.name as group_name 
                    FROM " . DB_PREFIX . "option_value_description ovd 
                    INNER JOIN " . DB_PREFIX . "option_description od ON od.option_id = ovd.option_id  
                    WHERE ovd.option_value_id IN (" . implode(',', $opt_san) . ")   
                      AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                      AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'";
            $query = $this->db->query($sql);

            foreach ($query->rows as $result) {
                $option_data[] = array(
                    'name' => $result['name'],
                    'group_name' => $result['group_name'],
                    'option_value_id' => $result['option_value_id']
                );
            }
        }

        return $option_data;
	}

	/**
	 * Gets filters of displayed products
	 * @author Rakesh Shekhawat
	 * @modified
	 * @dateTime 2016-01-28T16:53:39+0530
	 * @param    integer $category_id categoryid
	 * @param    string  $filters     [description]
	 * @return   array filters
	 */
	public function getFiltersOfProducts($category_id, $filters = '', $autocomplete = '') {
		$filter_group_data = array();
		
		if(!empty($category_id)){

			$this->load->model('setting/setting');
			$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));
			//Query to get filters of product of a category
			$cats = $this->getCategories($category_id);
			$implodecats = array();
			$implodecats[] = (int)$category_id;

			if(!empty($cats)){
				foreach ($cats as $catt)
				{
					$implodecats[] = (int)$catt['category_id'];
				}
			}
			if (isset($this->session->data['custom_store'])) {
				if ($this->session->data['custom_store'] == 'single') {
					$is_single = 1;
				}else {
					$is_single = 0;
				}
			} else {
				$is_single = 0;
			}

			$q = "SELECT pf.filter_id
				   FROM " . DB_PREFIX . "product p
				   INNER JOIN " . DB_PREFIX . "product_to_category p2c
				   ON (p.product_id = p2c.product_id)
				   INNER JOIN " . DB_PREFIX . "product_filter pf
				   ON (p.product_id = pf.product_id)
				   INNER JOIN " . DB_PREFIX . "product_to_store p2s
				   ON (p.product_id = p2s.product_id) ";
			if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
				$q .= " INNER JOIN " . DB_PREFIX . "ms_product mp ON (mp.product_id = p.product_id) ";
			}

			$q .= " WHERE p2c.category_id IN (" . implode(',', $implodecats) . ")
				   AND p.status = '1'
				   AND p.quantity > 0
				   AND p.stock_status_id != '5'
				   AND p.date_available <= NOW()
				   AND p2s.store_id = 0";

		    if (isset($store_info['config_seller_id']) && $store_info['config_seller_id'] > 0) {
		    	$q .= " AND mp.seller_id IN(".$store_info['config_seller_id'].")";
		    }
			if ($is_single==1) {
				$q .= " AND (p.is_single=1 OR p.piece_in_set=1) ";
			} else {
				$q .= " AND p.is_single=0 ";
			}
			$query = $this->db->query($q);
			//Skip filters for Size and Color for singles store and add
			$skip_filters = array(62, 63, 103, 104, 105, 106, 107, 108, 109, 110, 145,  246, 247, 248, 249, 250);

			foreach ($query->rows as $result) {
				if ($is_single) {
					if (in_array($result['filter_id'], $skip_filters)) {
						continue;
					}
				}
				if (in_array($result['filter_id'], array(145))) {
					continue;
				}
				$implode[] = (int)$result['filter_id'];
			}

			$filter_group_data = array();
			$implode = array_unique(array_filter($implode));
			if (!empty($implode)) {
				$fgd = "SELECT DISTINCT f.filter_group_id, fgd.name, fgd.description, fg.sort_order
					FROM " . DB_PREFIX . "filter f
					LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id)
					LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id)
					WHERE f.filter_id IN (" . implode(',', $implode) . ")
					AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

				$fgd .= "GROUP BY f.filter_group_id
					ORDER BY fg.sort_order, LCASE(fgd.name)";
				$filter_group_query = $this->db->query($fgd);

				foreach ($filter_group_query->rows as $filter_group) {
					$filter_data = array();

					$sql = "SELECT DISTINCT 
								f.filter_id, fd.name
							FROM 
								" . DB_PREFIX . "filter f
							INNER JOIN 
								" . DB_PREFIX . "filter_description fd 
								ON f.filter_id = fd.filter_id
								AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'
							WHERE 
								f.filter_id IN (" . implode(',', $implode) . ")
								AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "'
							";
						if(!empty($autocomplete)){
							$sql .=	" AND fd.name Like '%". $autocomplete ."%'";
						}
					$sql .=	"ORDER BY f.sort_order, LCASE(fd.name)";

					if(!empty($autocomplete)){
						$sql .= "LIMIT 0,10";
					}

					$filter_query = $this->db->query($sql);
					foreach ($filter_query->rows as $filter) {
						$filter_data[] = array(
								'filter_id' => $filter['filter_id'],
								'name'      => $filter['name']
						);
					}
					if ($filter_data) {
						$filter_group_data[] = array(
								'filter_group_id' => $filter_group['filter_group_id'],
								'name'            => $filter_group['name'],
								'description'     => $filter_group['description'],
								'filter'          => $filter_data
						);
					}
				}
			}

		} //End of !empty check

		return $filter_group_data;
	}

	/*
	*
	*get total of deal of the day by vikas (28-01-2016)
	* */
	public function getTotalDealDay(){
		$sql = "SELECT s.value FROM ".DB_PREFIX."setting as s WHERE `key`='deal_of_day'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	/**
	 * [getOptionsOfProducts description]
	 * @author Parth Gupta
	 * @dateTime 2016-03-15T17:13:44+0530
	 * @param    int                   $category_id
	 * @param    string                   $options
	 * @return   array                 array of products
	 */
	public function getOptionsOfProducts($category_id, $options = ''){
		$cats = $this->getCategories((int)$category_id) ;
		$implodecats = array();
		$implodecats[] = (int)$category_id;
		
		if (isset($this->session->data['custom_store'])) {
			if ($this->session->data['custom_store'] == 'single') {
				$is_single = 1;
			}else {
				$is_single = 0;
			}
		} else {
			$is_single = 0;
		}
		$q_n = "SELECT pov.option_value_id, p.product_id FROM
				".DB_PREFIX."product p
				LEFT JOIN " . DB_PREFIX . "product_to_category p2c
				ON (p.product_id = p2c.product_id)
				LEFT JOIN ".DB_PREFIX."product_option po
				ON (po.product_id = p.product_id)
				LEFT JOIN ".DB_PREFIX."product_option_value pov
				ON (pov.product_option_id = po.product_option_id)
				WHERE p2c.category_id IN (" . implode(',', $implodecats) . ")
				AND pov.quantity > 0
				AND p.status = '1'
				AND p.quantity > 0
				AND p.stock_status_id != '5'
				AND p.date_available <= NOW()";

				// echo $q_n;
				$query = $this->db->query($q_n);

				// echo("<pre>");print_r($query);

		foreach ($query->rows as $optionvalue) {
			$implode[] = $optionvalue['option_value_id'];
		}

		$option_description_data = array();
		if (!empty($implode)) {

			$option_description_query = "SELECT DISTINCT od.option_id, od.name
										FROM ".DB_PREFIX."option op
										LEFT JOIN ".DB_PREFIX."option_description od
										ON (op.option_id = od.option_id)
										LEFT JOIN ".DB_PREFIX."option_value ov
										ON (od.option_id = ov.option_id)
										LEFT JOIN ".DB_PREFIX."option_value_description ovd
										ON (ov.option_value_id = ovd.option_value_id)
										WHERE ovd.option_value_id IN  (" . implode(',', $implode) . ") AND
										od.language_id = " . (int)$this->config->get('config_language_id')
										;
			$option_description = $this->db->query($option_description_query);

			foreach ($option_description->rows as $option) {


				$option_data = array();

				$option_data_query = $this->db->query("SELECT DISTINCT ovd.option_value_id, ovd.name as option_value
											FROM ".DB_PREFIX."option op
											LEFT JOIN ".DB_PREFIX."option_description od
											ON (op.option_id = od.option_id)
											LEFT JOIN ".DB_PREFIX."option_value ov
											ON (od.option_id = ov.option_id)
											LEFT JOIN ".DB_PREFIX."option_value_description ovd
											ON (ov.option_value_id = ovd.option_value_id)
											WHERE ovd.option_value_id IN  (" . implode(',', $implode) . ") AND
											od.language_id = " . (int)$this->config->get('config_language_id').
											" AND ovd.option_id = ".$option['option_id'])
											;


				foreach ($option_data_query->rows as $opt) {
					$option_data[] = array(
						'option_value_id' => $opt['option_value_id'],
						'option_value' => $opt['option_value']
					);
				}
				if ($option_data) {
						$option_array[]= array(
							'option_id' => $option['option_id'],
							'option_name' => $option['name'],
							'option' => $option_data
						);
					}


			}
		}
		return isset($option_array)? $option_array : 0;
	}
	public function getStoreMetaData($category_id,$store_id){
		$sql = "SELECT * from " . DB_PREFIX . "category_storeinfo WHERE category_id= '". $category_id ."' AND store_id='".$store_id."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function menus($menu_name,$parent_id = 0,$type='desktop-category'){
		$store_id =  (int)$this->config->get('config_store_id');
		if($store_id == 0 || $store_id == 2 || $store_id == 9){
			$sql ="SELECT mi.id, mi.menu_id, mi.link_title, mi.link_type, mi.value, mi.type, mi.parent_id, mi.position, mi.status, mi.store_id, ci.image, ci.image_width, ci.image_height FROM " . DB_PREFIX . "menu_item mi LEFT JOIN  ".DB_PREFIX."category_images ci  ON mi.value=ci.category_id INNER JOIN " . DB_PREFIX . "menu m ON m.id=mi.menu_id
				WHERE mi.parent_id = '" . (int)$parent_id . "'
				AND m.name = '" . $menu_name. "'
				AND mi.store_id = '0'
				AND mi.status = '1'
				AND mi.link_type = 'category'
				AND mi.language_id = '". (int)$this->config->get('config_language_id') ."'
				AND ci.image LIKE '%". $type ."%'
				ORDER BY mi.position ASC";
			$query = $this->db->query($sql);
			return $query->rows;
		}else{
			$query = $this->db->query("SELECT mi.id, mi.menu_id, mi.link_title, mi.link_type, mi.value, mi.type, mi.parent_id, mi.position, mi.status, mi.store_id, ci.image, ci.image_width, ci.image_height FROM " . DB_PREFIX . "menu_item mi LEFT JOIN  ".DB_PREFIX."category_images ci ON mi.value=ci.category_id
				WHERE mi.parent_id = '" . (int)$parent_id . "'
				AND mi.menu_id = '" . (int)$menu_id . "'
				AND mi.store_id = '" . (int)$this->config->get('config_store_id') . "'
				AND mi.status = '1'
				AND mi.link_type = 'category'
				AND ci.image LIKE '%". $type ."%'
				ORDER BY mi.position ASC");
			return $query->rows;
		}

	}

	public function getMenu($menu_name) {
		//AND mi.store_id = '0'
	    $sql = "SELECT mi.id,
	           mi.menu_id,
	           mi.link_title,
	           mi.link_type,
	           mi.value,
	           mi.type,
	           mi.parent_id,
	           mi.position,
	           mi.status,
	           mi.store_id,
	           mi.megamenu,
	           mi.language_id,
	           mi.promotion,
	           mi.image
	           FROM " . DB_PREFIX . "menu_item mi 
	           INNER JOIN ".DB_PREFIX."menu m 
	           on m.id=mi.menu_id and m.store_id=mi.store_id 
				WHERE m.name = '" . $menu_name . "' 				
				AND mi.status = '1'
				AND mi.language_id = '". (int)$this->config->get('config_language_id') ."'
				ORDER BY mi.position ASC, mi.parent_id ASC";
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

    /**
     * Get Category Name
     */

    public function getName(){

    }

    
    public function getMenuCategory($category_id)
    {
    	$sql = "SELECT mi.* FROM " . DB_PREFIX . "menu_item mi INNER JOIN ".DB_PREFIX."menu m on m.id=mi.menu_id 
				WHERE mi.value = '" . (int)$category_id . "'
				AND mi.link_type = 'category'
				AND m.name = 'Desktop'
				AND mi.parent_id = '0'
				AND mi.language_id = '". (int)$this->config->get('config_language_id') ."'
				ORDER BY mi.position ASC";
		$query = $this->db->query($sql);
	   if($query->num_rows == 0)
	   {
	   	   $sql = "SELECT mi.* FROM " . DB_PREFIX . "menu_item mi INNER JOIN ".DB_PREFIX."menu m on m.id=mi.menu_id 
				WHERE mi.value = '" . (int)$category_id . "'
				AND mi.link_type = 'category'
				AND m.name = 'Desktop'
				AND mi.language_id = '". (int)$this->config->get('config_language_id') ."'
				ORDER BY mi.position ASC";
		  $query = $this->db->query($sql);
	    }	
		return $query->row;

    }


   	public function getChildMenu($menu_id) {
		 $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "menu_item
				WHERE id = '" . (int)$menu_id . "'
				AND language_id = '". (int)$this->config->get('config_language_id') ."'
				ORDER BY position ASC";
		$query = $this->db->query($sql);
		return $query->row;
	}

    public function getParentMenu($menu_id) 
    {   

        $menu_info = $this->getChildMenu($menu_id);
        if(isset($menu_info['parent_id']) && $menu_info['parent_id'] > 0)
        {
          $menu_info = $this->getParentMenu($menu_info['parent_id']);
        }
        return $menu_info;
    }

    public function getParentCategory($category_id) 
    {   
        $category_info = $this->getCategory($category_id);
        if(!empty($category_info['parent_id']) && $category_info['parent_id'] > 0)
        {
          $category_info = $this->getParentCategory($category_info['parent_id']);
        }
        return $category_info;
    }

    public function getAllMenuIds($preferences, $menu_type) {
         $output = array();
         $output[] = $preferences;
         if (!empty($preferences)) {
         	 $sql = "SELECT mi.value, mi.id FROM " . DB_PREFIX . "menu_item mi
                     INNER JOIN " . DB_PREFIX . "menu m on m.id=mi.menu_id
                     WHERE mi.value = '" . (int)$preferences . "'
				     AND mi.link_type = 'category'
				     AND m.name = '".$this->db->escape($menu_type)."'
				     AND mi.language_id = '". (int)$this->config->get('config_language_id') ."'
				     ORDER BY mi.parent_id ASC limit 1";

	        $query = $this->db->query($sql);
	        $output =  $this->getAllChildMenu($query->row['id'], $output);
         }
         
        return $output;
    }

   public function getAllChildMenu($menu_id, $output) 
    {   
        $menu_info = $this->getSubMenu($menu_id);

       foreach($menu_info as $menu_data)
       { 
          array_push($output,$menu_data['value']);
          $output = $menu_info = $this->getAllChildMenu($menu_data['id'], $output);
       } 
        return array_filter($output);
    }

   	public function getSubMenu($menu_id) {
		   $sql = "SELECT id, menu_id, link_title, link_type, value, type, parent_id, position, status, store_id, megamenu, language_id, promotion, image
		        FROM " . DB_PREFIX . "menu_item
				WHERE parent_id = '" . (int)$menu_id . "'
				AND (link_type = 'category' OR link_type = 'others')
				AND language_id = '". (int)$this->config->get('config_language_id') ."'
				ORDER BY position ASC";
		$query = $this->db->query($sql);
		return $query->rows;
	}

   	public function getSimilarMenu($category_id) {
		$sql = "SELECT mi.* FROM " . DB_PREFIX . "menu_item mi INNER  JOIN " . DB_PREFIX . "menu m
		        ON m.id = mi.menu_id  
				WHERE mi.value = '" . (int)$category_id . "'
				AND mi.link_type = 'category'
				AND mi.language_id = '". (int)$this->config->get('config_language_id') ."'
				AND mi.parent_id != 0
				AND mi.status = 1
				AND m.name = 'Desktop'";

		$query = $this->db->query($sql);
		if($query->num_rows > 0)
		{
           
           $sql2 = "SELECT * FROM " . DB_PREFIX . "menu_item 
				WHERE 
				parent_id = '" . (int)$query->row['parent_id'] . "'
				AND 
				status = 1
				AND 
				language_id = '". (int)$this->config->get('config_language_id') ."' ORDER BY position ASC";
			$query2 = $this->db->query($sql2);
			return $query2->rows;
		}
		else
		{
			return false;
		}
	}

    public function getAllCategoryIds() {

         $sql = "SELECT c.category_id, cd.name, c.date_modified FROM " . DB_PREFIX . "category c
                     LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
                     LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
                     WHERE  c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
                     AND c.status = '1'
                     AND cd.language_id = 1";

        $query = $this->db->query($sql);
        return $query->rows;
    }
/**
     * Method to return an array of all the unique filter ids
     * given parent category id.
     * Input(s):
     * @param array  categoryId
     * Output: Array of filter_group_id
     * Author: Rahul Singh 
     */
    
    public function getUniqueCategoryFilterGroupId($categoryId) {
    	$parentCategoryId=$this->getParentCategory($categoryId);

    	if(!empty($parentCategoryId['category_id']) && $parentCategoryId['category_id'] == $categoryId){
    	$sql = "SELECT cf.filter_group_id, cf.sort_order FROM " . DB_PREFIX . "category_filter_group cf 
		    		WHERE cf.category_id = '" . (int)$categoryId . "' 
		    		ORDER BY sort_order ASC ";
        		$query = $this->db->query($sql);
        		return $query->rows;
        	}else if(isset($parentCategoryId['category_id']) && $parentCategoryId['category_id'] != $categoryId){

        		$parentSql = "SELECT cf.filter_group_id, cf.sort_order FROM " . DB_PREFIX . "category_filter_group cf 
		    		WHERE cf.category_id = '" . (int)$parentCategoryId['category_id'] . "' 
		    		ORDER BY sort_order ASC ";
        		$parentQuery = $this->db->query($parentSql);
        		$parrentArray=$parentQuery->rows;
        		$removeCadArray=array();
	        		if(count($parrentArray)>0){
	        			foreach($parrentArray as $key=>$values){
	        				$removeCadArray[$key]=$values['filter_group_id'];
	        			}

	        		}
	        		$removeCadStr='';
	        		if(count($removeCadArray)> 0){
	        			$removeCadStr=implode(',',$removeCadArray);
	        		}
	        		if($removeCadStr!==''){
			        	$childSql = "SELECT cf.filter_group_id, cf.sort_order FROM " . DB_PREFIX . "category_filter_group cf 
				    		WHERE cf.category_id = '" . (int)$parentCategoryId['category_id'] . "' 
				    		AND cf.filter_group_id NOT IN (".$removeCadStr.")
				    		ORDER BY sort_order ASC ";
		    		}else{
		    			$childSql = "SELECT cf.filter_group_id, cf.sort_order FROM " . DB_PREFIX . "category_filter_group cf 
				    		WHERE cf.category_id = '" . (int)$parentCategoryId['category_id'] . "' 
				    		ORDER BY sort_order ASC ";

		    		}
		    		$childQuery = $this->db->query($childSql);
        			$childArray=$childQuery->rows;
        			$final_array=array_merge($parrentArray,$childArray);
        			return $final_array;

        	}else{
        		$sql = "SELECT cf.filter_group_id, cf.sort_order FROM " . DB_PREFIX . "category_filter_group cf 
		    		WHERE cf.category_id = '" . (int)$categoryId . "' 
		    		ORDER BY sort_order ASC ";
        		$query = $this->db->query($sql);
        		return $query->rows;

        	}
    }

    public function promotion($categoryId,$link_type,$store_id){
    	$sql = "SELECT promotion FROM " . DB_PREFIX . "menu_item
                     WHERE  value = '" . (int)$categoryId . "'
                     AND link_type = '". $this->db->escape($link_type) ."'
                     AND promotion != '' 
                     AND promotion IS NOT NULL
                     AND store_id = '".(int)$store_id."'
                     ";
        $query = $this->db->query($sql);
        return $query->row;

	}

	public function getCategoryImages($category_id){

		$sql = "SELECT image_height, image_width, image, sort_order FROM " . DB_PREFIX . "category_images WHERE category_id = '" . (int)$category_id . "'";
		
		$query = $this->db->query($sql)->rows;
		$category_images = array();
        $this->load->model('tool/image');
		foreach($query as $category_image) {
			$category_images[] = array(
				//'image' 		=> $this->config->get('config_url').'image/'.$category_image['image'],
                'image' 		=> $this->model_tool_image->getOriginalImage($category_image['image']),
				'image_height' 	=> $category_image['image_height'],
				'image_width' 	=> $category_image['image_width'],
				'sort_order' 	=> $category_image['sort_order'],
        'db_image' => $category_image['image']
			);
		}
		return $category_images;
	}	

}
