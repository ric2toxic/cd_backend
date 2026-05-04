<?php
class ModelSellerMenu extends Model {

	public function saveMenu($data = array()) {
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
						store_id = '". (int)$store_id ."'
						WHERE id = '". $menu_db_id ."'";
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
						store_id = '" . (int)$store_id . "'";
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
									store_id = '". (int)$store_id ."'
									WHERE id = '". $menu_db_id ."'";
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
									store_id = '" . (int)$store_id . "'";
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
									store_id = '". (int)$store_id ."'
									WHERE id = '". $menu_db_id ."'";
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
									store_id = '" . (int)$store_id . "'";
							$this->db->query($sql);
						}
					}
				}
			}
		}
	}

	public function deleteMenu($menu_id) {
		$this->event->trigger('pre.admin.banner.delete', $banner_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int)$banner_id . "'");

		$this->event->trigger('post.admin.banner.delete', $banner_id);
	}

	public function getMenu($menu_id, $store_id) {
		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "menu_item
				WHERE menu_id = '" . (int)$menu_id . "'
				AND store_id = '". $store_id ."'
				ORDER BY position ASC";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getMenus($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "menu";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalMenus() {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "menu"; 
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getCategoryInfo($category_id)
	{
		return $this->registry;
	}



	// delete menus with sub menu and without sub menu by vikas
	public function fetchMenuByParentMenuID($parent_menu_item_id,$store_id){
		$sql = "SELECT * FROM oc_menu_item WHERE parent_id = '".$parent_menu_item_id."' AND store_id = '".$store_id."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function deleteAllMenus($parent_menu_item_id){
		$sql = "DELETE FROM oc_menu_item WHERE id = '".$parent_menu_item_id."' OR parent_id = '".$parent_menu_item_id."'";
		$this->db->query($sql);
	}
	public function deleteChildMenus($parent_menu_item_id){
		$sql = "DELETE FROM oc_menu_item WHERE id = '".$parent_menu_item_id."'";
		$this->db->query($sql);
	}


	// get page information
	public function getPage($data = array(),$store_id){
		$query = $this->db->query("
									SELECT ssp.title FROM " . DB_PREFIX . "menu_item mi
									LEFT JOIN " . DB_PREFIX . "seller_store_pages ssp
									ON (mi.value = ssp.id)
									WHERE mi.value = '" . (int)$data . "'
									AND mi.store_id = '" . (int)$store_id . "' AND mi.status = '1'"
								);
		return $query->row;
	}


	// ger menu name for breadcrumbs by vikas (02-05-2016)
	public function getMenu_name($menu_id){
		$query = $this->db->query("SELECT name FROM oc_menu WHERE id = '".$menu_id."'");
		return $query->row['name'];
	}

	public function getMenu_detail($menu_id){
		$query = $this->db->query("SELECT * FROM oc_menu WHERE id = '".$menu_id."'");
		return $query->row;
	}


}