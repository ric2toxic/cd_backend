<?php
class ModelCatalogMenu extends Model {
	public function getPages($data = array()){
		$sql = "SELECT i.information_id, id.title, id.description FROM " . DB_PREFIX . "information as i INNER JOIN " . DB_PREFIX ."information_description as id ON i.information_id = id.information_id";

		if (!empty($data['filter_name'])) {
			$sql .= " AND id.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
				'title'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY title";
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
	public function getPage($data = array(),$store_id){
		$query = $this->db->query("
									SELECT id.title FROM " . DB_PREFIX . "menu_item mi
									LEFT JOIN " . DB_PREFIX . "information_description id
									ON (mi.value = id.information_id)
									LEFT JOIN " . DB_PREFIX . "information i ON (i.information_id = id.information_id)
									WHERE mi.value = '" . (int)$data . "'
									AND mi.store_id = '" . (int)$store_id . "' AND i.status = '1'"
								);
		return $query->row;
	}
	public function menu_store_status($menu_status, $menu_lang){
			$sql = "UPDATE ". DB_PREFIX ."menu_item SET status = '" . $menu_status . "' WHERE store_id IN (0,2,9) AND language_id = '". $menu_lang ."'";
			$query = $this->db->query($sql);
	}
	public function getLanguage(){
		$sql = "SELECT language_id, name FROM " . DB_PREFIX . "language 
					WHERE status = '1'
					ORDER BY `sort_order` ASC"; 
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getMenuLanguage($menu_id, $store_id, $language_id){
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "menu_item
				WHERE menu_id = '" . (int)$menu_id . "'
				AND store_id = '". $store_id ."' 
				AND language_id = '". $language_id ."' 
				ORDER BY position ASC";
	}

	public function getMenu($menu_id, $store_id, $language_id) {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "menu_item
				WHERE menu_id = '" . (int)$menu_id . "'
				AND store_id = '". $store_id ."'
				AND language_id = '". (int)$language_id ."'
				ORDER BY position ASC";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	

/*public function saveMenu($data = array()) {
		$language = $data['menu_store_language'];
		foreach ($data['info'] as $key=>$value) {
			if($value['category'] == "0"){
				$value['category'] = $data['name_info'][$key]['category'];
			}
			$menu_db_id = $value['menu_db_id'];
			$store_id 	= $value['store_id'];
			$menu_id	= $value['menu_id'];
			$link_title = $value['link_title'];
			$link_type 	= $value['link_type'];
			$val 		= $value['category'];
			$type 		= $value['type'];
			$position 	= $value['position'];
			$status 	= $value['status'];
			if($menu_db_id > 0 ){
				$sql = "UPDATE ". DB_PREFIX ."menu_item
						SET menu_id ='". $menu_id ."',
						link_title = '". $link_title ."',
						link_type  = '". $link_type ."',
						value = '" . $val . "',
						type = '". $type ."',
						parent_id = 0,
						position = '". (int)$position ."',
						status = '". (int)$status ."',
						store_id = '". (int)$store_id ."',
						language_id = '" . $language . "'
						WHERE id = '". $menu_db_id ."' AND language_id = '" . $language . "'";
				$this->db->query($sql);
				$parent_id = $menu_db_id;
			}else{
				$sql = "INSERT INTO ". DB_PREFIX ."menu_item
						SET menu_id = '". $menu_id ."',
						link_title = '" . $link_title . "',
						link_type = '" . $link_type . "',
						value = '" . $val . "',
						type = '" . $type . "',
						parent_id = 0,
						position = '" . (int)$position . "',
						status = '" . (int)$status . "',
						store_id = '" . (int)$store_id . "',
						language_id = '" . $language . "'";
				$this->db->query($sql);
				$parent_id = $this->db->getLastId();
			}
			if(!empty($value['child'])){
				$p_id = $parent_id;
				foreach ($value['child'] as $ar => $arr) {
					if($arr['megamenu_type'] == 1){
						$menu_db_id = $arr['menu_db_id'];
						$type 		= $arr['type'];
						$store_id 	= $arr['store_id'];
						$menu_id	= $arr['menu_id'];
						$megamenu	= $arr['megamenu'];
						$status 	= $arr['status'];
						if($menu_db_id > 0 ){
							$sql = "UPDATE ". DB_PREFIX ."menu_item
									SET menu_id ='". $menu_id ."',
									link_type  = 'megamenu',
									megamenu = '" . $megamenu . "',
									type = '". $type ."',
									parent_id = '". $p_id ."',
									position = 0,
									status = '". (int)$status ."',
									store_id = '". (int)$store_id ."',
									language_id = '" . $language . "'
									WHERE id = '". $menu_db_id ."' AND language_id = '" . $language . "'";
							$this->db->query($sql);
						}else{
							$sql = "INSERT INTO ". DB_PREFIX ."menu_item
									SET menu_id = '". $menu_id ."',
									link_type = 'megamenu',
									megamenu = '" . $megamenu . "',
									type = '" . $type . "',
									parent_id = '". $p_id ."',
									position = 0,
									status = '" . (int)$status . "',
									store_id = '" . (int)$store_id . "',
									language_id = '" . $language . "'";
							$this->db->query($sql);
						}
					}else{
						if($arr['category'] == "0"){
							$arr['category'] = $data['name_info'][$key]['child'][$ar]['category'];
						}
						$menu_db_id = $arr['menu_db_id'];
						$store_id 	= $arr['store_id'];
						$menu_id	= $arr['menu_id'];
						$link_title = $arr['link_title'];
						$link_type 	= $arr['link_type'];
						$val 		= $arr['category'];
						$type 		= $arr['type'];
						$position 	= $arr['position'];
						$status 	= $arr['status'];
						if($menu_db_id > 0 ){
							$sql = "UPDATE ". DB_PREFIX ."menu_item
									SET menu_id ='". $menu_id ."',
									link_title = '". $link_title ."',
									link_type  = '". $link_type ."',
									value = '" . $val . "',
									type = '". $type ."',
									parent_id = '". $p_id ."',
									position = '". (int)$position ."',
									status = '". (int)$status ."',
									store_id = '". (int)$store_id ."',
									language_id = '" . $language . "'
									WHERE id = '". $menu_db_id ."' AND language_id = '" . $language . "'";
							$this->db->query($sql);
						}else{
							$sql = "INSERT INTO ". DB_PREFIX ."menu_item
									SET menu_id = '". $menu_id ."',
									link_title = '" . $link_title . "',
									link_type = '" . $link_type . "',
									value = '" . $val . "',
									type = '" . $type . "',
									parent_id = '". $p_id ."',
									position = '" . (int)$position . "',
									status = '" . (int)$status . "',
									store_id = '" . (int)$store_id . "',
									language_id = '" . $language . "'";
							$this->db->query($sql);
						}
					}
				}
			}
		}
	}*/


	public function saveMenu($data = array()) {

		$language = $data['menu_store_language'];
		foreach ($data['info'] as $key=>$value) {
			if($value['category'] == "0"){
				$value['category'] = $data['name_info'][$key]['category'];
			}
			
			$menu_db_id = $value['menu_db_id'];
			$store_id 	= $value['store_id'];
			$menu_id	= $value['menu_id'];
			$link_title =  $this->db->escape($value['link_title']);
			$link_type 	= $value['link_type'];
			$val 		= $value['category'];
			$type 		= $value['type'];
			$position 	= $value['position'];
			$status 	= $value['status'];
			if($menu_db_id > 0 ){
				$sql = "UPDATE ". DB_PREFIX ."menu_item
						SET menu_id ='". $menu_id ."',
						link_title = '". $link_title ."',
						link_type  = '". $link_type ."',
						value = '" . $val . "',
						type = '". $type ."',
						parent_id = 0,
						position = '". (int)$position ."',
						status = '". (int)$status ."',
						store_id = '". (int)$store_id ."',
						language_id = '" . (int)$language . "'
						WHERE id = '". (int)$menu_db_id ."' AND language_id = '" . (int)$language . "'";
				$this->db->query($sql);
				$parent_id = $menu_db_id;
			}else{
				 $sql = "INSERT INTO ". DB_PREFIX ."menu_item
						SET menu_id = '". $menu_id ."',
						link_title = '" . $link_title . "',
						link_type = '" . $link_type . "',
						value = '" . $val . "',
						type = '" . $type . "',
						parent_id = 0,
						position = '" . (int)$position . "',
						status = '" . (int)$status . "',
						store_id = '" . (int)$store_id . "',
						language_id = '" . (int)$language . "'";
				$this->db->query($sql);
				$parent_id = $this->db->getLastId();
			}
			if(!empty($value['child'])){
				
				foreach ($value['child'] as $ar => $arr) {
					$promotion='';
					if(!empty($arr['promotion'])){
						$promotion = serialize($arr['promotion']);
					}

					if(isset($arr['category']) && $arr['category'] == "0"){
						$arr['category'] = $data['name_info'][$key]['child'][$ar]['category'];
					}

					$child_parent_id = $this->add_child_menu($arr, $parent_id, $language,$promotion);

					  if(!empty($arr['child']))
					  {
					  	
			        	foreach ($arr['child'] as $ar2 => $arr2) {
			        		$promotion='';
							if(!empty($arr2['promotion'])){
								$promotion = serialize($arr2['promotion']);
							}

			        		if(isset($arr2['category']) && $arr2['category'] == "0"){
								$arr2['category'] = $data['name_info'][$key]['child'][$ar]['child'][$ar2]['category'];
						    }
					    
					     $sub_child_parent_id = $this->add_child_menu($arr2, $child_parent_id, $language,$promotion);
                          if(!empty($arr2['child']))
					      {
			        	    foreach ($arr2['child'] as $ar3 => $arr3) {

                             if(isset($arr3['category']) && $arr3['category'] == "0"){
							  $arr3['category'] = $data['name_info'][$key]['child'][$ar]['child'][$ar2]['child'][$ar3]['category'];
						      }

					        $this->add_child_menu($arr3, $sub_child_parent_id, $language,$promotion);
		             	  }
				      }

		             	}
				      }
		        	
		        	}
 
				}
			}
		}


	public function add_child_menu($arr, $parent_id, $language, $promotion)
	{  
		$p_id = $parent_id;  
		if($arr['megamenu_type'] == 1){
						$menu_db_id = $arr['menu_db_id'];
						$type 		= $arr['type'];
						$store_id 	= $arr['store_id'];
						$menu_id	= $arr['menu_id'];
						$megamenu	= $arr['megamenu'];
						$status 	= $arr['status'];
						if($menu_db_id > 0 ){
							$sql = "UPDATE ". DB_PREFIX ."menu_item
									SET menu_id ='". $menu_id ."',
									link_type  = 'megamenu',
									megamenu = '" . $megamenu . "',
									type = '". $type ."',
									parent_id = '". $p_id ."',
									position = 0,
									status = '". (int)$status ."',
									store_id = '". (int)$store_id ."',
									language_id = '" . (int)$language . "',
									promotion = '" . $this->db->escape($promotion) . "'
									WHERE id = '". (int)$menu_db_id ."' AND language_id = '" . (int)$language . "'";
							$this->db->query($sql);
							$parent_id = $menu_db_id;
						}else{
							$sql = "INSERT INTO ". DB_PREFIX ."menu_item
									SET menu_id = '". $menu_id ."',
									link_type = 'megamenu',
									megamenu = '" . $megamenu . "',
									type = '" . $type . "',
									parent_id = '". $p_id ."',
									position = 0,
									status = '" . (int)$status . "',
									store_id = '" . (int)$store_id . "',
									language_id = '" . (int)$language . "',
									promotion = '" . $this->db->escape($promotion) . "'";
							$this->db->query($sql);
							$parent_id = $this->db->getLastId();
						}
					}else{
						
						$menu_db_id = $arr['menu_db_id'];
						$store_id 	= $arr['store_id'];
						$menu_id	= $arr['menu_id'];
						$link_title =  $this->db->escape($arr['link_title']);
						$link_type 	= $arr['link_type'];
						$val 		= $arr['category'];
						$type 		= $arr['type'];
						$position 	= $arr['position'];
						$status 	= $arr['status'];
						if($menu_db_id > 0 ){
							$sql = "UPDATE ". DB_PREFIX ."menu_item
									SET menu_id ='". $menu_id ."',
									link_title = '". $link_title ."',
									link_type  = '". $link_type ."',
									value = '" . $val . "',
									type = '". $type ."',
									parent_id = '". $p_id ."',
									position = '". (int)$position ."',
									status = '". (int)$status ."',
									store_id = '". (int)$store_id ."',
									language_id = '" . (int)$language . "',
									promotion = '" . $this->db->escape($promotion) . "'
									WHERE id = '". $menu_db_id ."' AND language_id = '" . $language . "'";
							$this->db->query($sql);
							$parent_id = $menu_db_id;
						}else{
							$sql = "INSERT INTO ". DB_PREFIX ."menu_item
									SET menu_id = '". $menu_id ."',
									link_title = '" . $link_title . "',
									link_type = '" . $link_type . "',
									value = '" . $val . "',
									type = '" . $type . "',
									parent_id = '". $p_id ."',
									position = '" . (int)$position . "',
									status = '" . (int)$status . "',
									store_id = '" . (int)$store_id . "',
									language_id = '" . (int)$language . "',
									promotion = '" . $this->db->escape($promotion) . "'";
							$this->db->query($sql);
							$parent_id = $this->db->getLastId();
						}
					}

					return $parent_id;
	           }
	
}