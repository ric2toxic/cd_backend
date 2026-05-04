<?php
	//Author Divya Porwal
	// February 2017
class ModelFilters extends Model{
	
	//public method to get filter groups
	
	public function getFilterGroups($category_id) {
		
		$filter_group_array = array();
		$filter_group_name = array();
		$new_filter_group = array();
		
		//get from cache if present
		$filter_groups = $this->cache->get((int)$category_id);
		
		if (!$filter_groups) {
			$query = $this->db->query("SELECT filter_groups FROM " . DB_PREFIX . "category
						WHERE category_id = '" . (int)$category_id . "' AND status = '1'");
						
			$filter_groups = $query->row['filter_groups'];
		}
		
		$group_arr = explode(',',$filter_groups);
		$i=0;
		foreach($group_arr as $var) {
			$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "filter_group_description
					WHERE filter_group_id = '" . (int)$var . "'") ;
			$name = $query->row;
			$new_filter_group[$i]['filter_group'] = array_values($name);
			$i++;
		}
		
		$filter_group_array['filter_group_id'] = $group_arr;
		$filter_group_array['filter_group_name'] = $new_filter_group;
		
		
		return $filter_group_array;
	}
	
	//public method to get filters

	public function getFilters($filter_groups) {
		
		$filter_id = array();
		$filter_name = array();
		$filters = array();
		foreach($filter_groups['filter_group_id'] as $var) {
			$sql = "
					SELECT 
						f.filter_id, fd.name 
					FROM 
						". DB_PREFIX ."filter AS f
					INNER JOIN ". DB_PREFIX ."filter_description AS fd 
						ON f.filter_id = fd.filter_id
							AND fd.language_id = 1
					WHERE 
						f.filter_group_id = '" . (int)$var . "' 
					";
			$query = $this->db->query();
			$filters[$var] = $query->rows;
		}
	
		return $filters;
		
		
	}
	
	public function getNonMandatoryFilters($category_id) {
		
		$filter_group_array = array();
		$filter_group_name = array();
		$new_filter_group = array();
		
		//get from cache if present
		$filter_groups = $this->cache->get((int)$category_id);
		
		if (!$filter_groups) {
			$query = $this->db->query("SELECT non_mandatory_filter_groups  FROM " . DB_PREFIX . "category
						WHERE category_id = '" . (int)$category_id . "' AND status = '1'");
			$filter_groups = $query->row['non_mandatory_filter_groups'];
		}
		
		$group_arr = explode(',',$filter_groups);
		$i = 0;
		foreach($group_arr as $var) {
			$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "filter_group_description
					WHERE filter_group_id = '" . (int)$var . "'") ;
			$name = $query->row;
			$new_filter_group[$i]['non_mandatory_filter_groups'] = array_values($name);
			$i++;
		}
		
		$filter_group_array['filter_group_id'] = $group_arr;
		$filter_group_array['filter_group_name'] = $new_filter_group;
		
	
		return $filter_group_array;
		
		
	}
	
}


?>
