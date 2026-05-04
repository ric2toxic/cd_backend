<?php
class ModelDesignBanner extends Model {
	public function addBanner($data) {
            
                //echo "<pre>"; print_r($data); die;
            
                $this->event->trigger('pre.admin.banner.add', $data);
                //echo "INSERT INTO " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', store_id = '" . (int)$data['store_id'] . "', is_category = '" . (int)$data['is_category'] . "'"; die;
		$this->db->query("INSERT INTO " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', store_id = '" . (int)$data['store_id'] . "', is_category = '" . (int)$data['is_category'] . "'");

		$banner_id = $this->db->getLastId();

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {

                            if(isset($banner_image['target_blank'])){
                                $target_blank = 1;
                            }else{
                                $target_blank = 0;
                            }
                            foreach ($banner_image['banner_image_description'] as $language_id => $image){
                                //$sql = "INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', item_id = '" . (int)$banner_image['banner_item'] . "', language_id = '" . (int)$language_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($image['image']) . "', target_blank = '" . (int)$target_blank . "', sort_order = '" . (int)$banner_image['sort_order'] . "'";
                                //echo $sql.'<br/>';
                                $this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', item_id = '" . (int)$banner_image['banner_item'] . "', language_id = '" . (int)$language_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($image['image']) . "', target_blank = '" . (int)$target_blank . "', sort_order = '" . (int)$banner_image['sort_order'] . "'', status = '" . (int)$banner_image['status'] . ""); 
                                $banner_image_id = $this->db->getLastId();
                                    if(!empty($banner_image['category_image'])){ 
                                            $this->addImageCategories($banner_id,$banner_image_id,$banner_image['category_image']);
                                    }
                                // echo "INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', item_id = '" . (int)$banner_image['banner_item'] . "', language_id = '" . (int)$language_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'";
                                $sql = "INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($image['title']) . "'";
                                //echo $sql.'<br/>';
                                $this->db->query($sql);

                            }
			}//die;

		}

		$this->event->trigger('post.admin.banner.add', $banner_id);

		return $banner_id;
	}

	public function editBanner($banner_id,$data) { 
                //$banner_image['category_image'][] = $category_id;
               // echo "<pre>"; print_r($data); //die;
                
		$this->event->trigger('pre.admin.banner.edit', $data);
                
		$this->db->query("UPDATE " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "' WHERE banner_id = '" . (int)$banner_id . "'");
                
                if( isset($data['select_category']) ){
                    // For delete  banner prefrences category wise 
                    $sqlBanner = "SELECT banner_image_id FROM " . DB_PREFIX . "wsb_banners_image_category AS BIC "
                       . "WHERE BIC.banner_id = '" . (int)$banner_id . "' "
                       . "AND BIC.category_id = '" . (int)$data['select_category'] . "' ";

                    $queryB = $this->db->query($sqlBanner);
                    if($queryB->num_rows > 0){

                        foreach($queryB->rows as $qb){
                            
                            if(isset($qb['banner_image_id']) && $qb['banner_image_id'] != ''){     
                                $sql = "DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_image_id = '" . (int)$qb['banner_image_id'] . "'";
                                $this->db->query($sql);

                                $sql = "DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_image_id = '" . (int)$qb['banner_image_id'] . "'";
                                $this->db->query($sql);


                                $sql = "DELETE FROM " . DB_PREFIX . "wsb_banners_image_category WHERE banner_image_id = '" . (int)$qb['banner_image_id'] . "'";
                                $this->db->query($sql);

                                $sql = "DELETE FROM " . DB_PREFIX . "wsb_banners_image_cart_category WHERE banner_image_id = '" . (int)$qb['banner_image_id'] . "'";
                                $this->db->query($sql);  
                            }

                        }
                    }
                // for single banner case    
                }else{
                    $sql = "DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int) $banner_id . "'";
                    $this->db->query($sql);

                    $sql = "DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int) $banner_id . "'";
                    $this->db->query($sql);

                    $sql = "DELETE FROM " . DB_PREFIX . "wsb_banners_image_category WHERE banner_id = '" . (int) $banner_id . "'";
                    $this->db->query($sql);

                    $sql = "DELETE FROM " . DB_PREFIX . "wsb_banners_image_cart_category WHERE banner_id = '" . (int) $banner_id . "'";
                    $this->db->query($sql);
                }
                
                if(count($data['banner_image']) > 0){ 
                    foreach($data['banner_image'] as $bi){                       
                        
                        if(!isset($bi['positive_category']))
                        {
                            $bi['positive_category'] = array();
                        } 
                        
                        if(!isset($bi['negative_category']))
                        {
                            $bi['negative_category'] = array();
                        }
                        // For Insert banner prefrences category wise 
                        if(isset($bi['target_blank'])){
                            $target_blank = 1;
                        }else{
                            $target_blank = 0;
                        }

                        if ( !empty($bi['type']) && ( $bi['type'] == 'category' || $bi['type'] == 'sale' ) ) 
                        {
                            $link = (int)$bi['category_id'];
                        } else {
                            $link = $bi['link'];
                        }

                        foreach ($bi['banner_image_description'] as $language_id => $image)
                        {
                            $sql = "INSERT INTO 
                                        " . DB_PREFIX . "banner_image 
                                        SET "
                                        . "banner_id    = '" . (int)$banner_id . "', "
                                        . "item_id      = '" . (int)$bi['banner_item'] . "', "
                                        . "language_id  = '" . (int)$language_id . "', "
                                        . "link         = '" .  $this->db->escape($link) . "', "
                                        . "image        = '" .  $this->db->escape($image['image']) . "', "
                                        . "target_blank = '" . $target_blank . "', "
                                        . "sort_order   = '" . (int)$bi['sort_order'] . "',"
                                        . "status       = '" . (int)$bi['status'] . "',"
                                        . "type         = '" . $bi['type'] . "'";
                            $this->db->query($sql);

                            $banner_image_id = $this->db->getLastId();
                            
                            $sql = "INSERT INTO 
                                        " . DB_PREFIX . "banner_image_description 
                                        SET 
                                            banner_image_id = '" . (int)$banner_image_id . "', 
                                            language_id     = '" . (int)$language_id . "', 
                                            banner_id       = '" . (int)$banner_id . "', 
                                            title           = '" .  $this->db->escape($image['title']) . "'";
                            $this->db->query($sql);

                             $sql = "INSERT INTO 
                                        " . DB_PREFIX . "wsb_banners_image_cart_category 
                                        SET 
                                            banner_image_id = '" . (int)$banner_image_id . "', 
                                            banner_id       = '" . (int)$banner_id . "', 
                                            positive_category           = '" .  $this->db->escape( serialize($bi['positive_category'])) . "',
                                            negative_category           = '" .  $this->db->escape(serialize($bi['negative_category'])) . "'";
                            $this->db->query($sql);
                            
                            if(!empty($bi['category_image'])){
                                $this->addImageCategories($banner_id,$banner_image_id,$bi['category_image']);
                            }                            
                        } 
                    }
                }
                
                $this->event->trigger('post.admin.banner.edit', $banner_id);
	}

	public function deleteBanner($banner_id) {
		$this->event->trigger('pre.admin.banner.delete', $banner_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int)$banner_id . "'");

		$this->event->trigger('post.admin.banner.delete', $banner_id);
	}

	public function getBanner($banner_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "' AND store_id IN (".WSB_STORES_ID .")");

		return $query->row;
	} 

	public function getBanners($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "banner";

		$sort_data = array(
			'name',
			'status'
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
        
        
        public function getBannerImages($banner_id,$category_id) {
            $banner = array();
            
            //check banner have category or not
            //$banner_id
            $sql = "SELECT * FROM " . DB_PREFIX . "banner AS B "
                    . "WHERE B.banner_id = '" . (int)$banner_id . "' ";
            $query = $this->db->query($sql);         
            //echo "<pre>"; print_r($query); die;
            if($query->num_rows > 0){ 
                $result = $query->row;
                $is_category = $result['is_category'];
                if($is_category == 0){ //die('0');
                    $banner = $this->getBannerImagesForSingle($banner_id);  
                    
                }elseif($is_category == 1){ //die('1');
                    $banner = $this->getBannerImagesForCategory($banner_id,$category_id); 
                }
            }
            
            //echo "<pre>123"; print_r($banner); die;
            return $banner;
            
        }
        
        public function getBannerImagesForCategory($banner_id,$category_id) {
            $banner_image_data = array();
            $this->load->model('tool/image');
	    $banner = array();

            //first get banner_image_id from wsb_banners_image_category
            $banner_image_id = '';
            $sql = "SELECT * FROM " . DB_PREFIX . "wsb_banners_image_category AS BIC "
                    . "WHERE BIC.banner_id = '" . (int)$banner_id . "' "
                    . "AND BIC.category_id = '" . (int)$category_id . "' ";
            //echo $sql; die;
            $query = $this->db->query($sql);
            //echo "<pre>"; print_r($query); die;
            if($query->num_rows > 0){ 
                foreach($query->rows as $key =>  $row){
                    $banner_image_id = $row['banner_image_id']; 
                    //echo $banner_image_id;
                    //echo "<br/>";
                    $sql = "SELECT * FROM " . DB_PREFIX . "banner_image AS BI  "
                            . "LEFT JOIN " . DB_PREFIX . "banner_image_description AS BID "
                            . "ON (BI.banner_image_id = BID.banner_image_id) "
                            . "WHERE BI.banner_image_id = '" . (int)$banner_image_id . "' "
                            . "AND BID.language_id = 1 "
                            . "ORDER BY BI.sort_order ASC";
                    //echo $sql; 
                    //echo "<br/>"; 
                    $banner_image_query = $this->db->query($sql);
                    //echo "<pre>"; print_r($banner_image_query); die;
                    if($banner_image_query->num_rows == 1){
                        foreach($banner_image_query->rows as $banner_image){
                            //if (is_file(DIR_IMAGE . $banner_image['image'])) {
                                $image = $banner_image['image'];
                                $thumb = $banner_image['image'];
                                                $directory = dirname($banner_image['image']);
//                            } else {
//                                $image = '';
//                                $thumb = 'no_image.png';
//                                                $directory = '';
//                            }
                                $banner_image_1[$banner_image['language_id']] = array(
                                    'title' => $banner_image['title'],
                                    'image' => $image,
                                    'thumb' => $this->model_tool_image->resize($thumb, 100, 100),
                                    'directory' => $directory
                                );
                                //$banner[$banner_image['item_id']] = array(
                                $banner[$key] = array( 
                                    'banner_image_description' => $banner_image_1,
                                    'link'                     => $banner_image['link'],
                                    'sort_order'               => $banner_image['sort_order'],
                                    'status'                   => $banner_image['status'],
                                    'target_blank'             => $banner_image['target_blank'],
                                    'banner_image_id'          => $banner_image['banner_image_id']
                                );  
                            
                        }
                    }
                }
            }
            //die;
            $banner = $this->aasort($banner,"sort_order");  
            //echo "<pre>"; print_r($banner); die;
            return $banner;
	}
        
        function aasort (&$array, $key) { 
            $sorter=array();
            $ret=array();
            reset($array);
            foreach ($array as $ii => $va) {
                $sorter[$ii]=$va[$key];
            }
            asort($sorter);
            foreach ($sorter as $ii => $va) {
                $ret[$ii]=$array[$ii];
            }
            $array=$ret;
            
            return $array;
        }
        
	public function getBannerImagesForSingle($banner_id) {
		$banner_image_data = array();

		$sql = "SELECT *, bid.title as title FROM " . DB_PREFIX . "banner_image bi LEFT JOIN oc_banner_image_description bid ON (bi.banner_image_id = bid.banner_image_id) WHERE bi.banner_id = '" . (int)$banner_id . "' ORDER BY sort_order ASC";
//		$sql = "SELECT * FROM " . DB_PREFIX . "banner_image AS BI "
//                        . "LEFT JOIN oc_banner_image_description AS BID "
//                        . "ON (BI.banner_image_id = BID.banner_image_id) "
//                        . "WHERE BI.banner_id = '" . (int)$banner_id . "' "
//                        . "AND BI.language_id = 1 "
//                        . "ORDER BY sort_order ASC";
                //echo $sql; die;
		$banner_image_query = $this->db->query($sql);
                
                //echo "<pre>"; print_r($banner_image_query); die('model');
                
		/*foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_description_data = array();

			$banner_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image_description WHERE banner_image_id = '" . (int)$banner_image['banner_image_id'] . "' AND banner_id = '" . (int)$banner_id . "'");

			foreach ($banner_image_description_query->rows as $banner_image_description) {
				$banner_image_description_data[$banner_image_description['language_id']] = array('title' => $banner_image_description['title']);
			}

			$banner_image_data[] = array(
				'banner_image_description' => $banner_image_description_data,
				'link'                     => $banner_image['link'],
				'image'                    => $banner_image['image'],
				'sort_order'               => $banner_image['sort_order']
			);
		}*/
        $this->load->model('tool/image');
	    $banner = array();
	    foreach($banner_image_query->rows as $banner_image){
            //if (is_file(DIR_IMAGE . $banner_image['image'])) {
                $image = $banner_image['image'];
                $thumb = $banner_image['image'];
				$directory = dirname($banner_image['image']);
            /*} else {
                $image = '';
                $thumb = 'no_image.png';
				$directory = '';
            }*/

                $banner_image_1[$banner_image['language_id']] = array(
                    'title' => $banner_image['title'],
                    'image' => $image,
                    'thumb' => $this->model_tool_image->resize($thumb, 100, 100),
                                    'directory' => $directory
                );

                $banner[$banner_image['item_id']] = array(
                    'banner_image_id'          =>   $banner_image['banner_image_id'],
                    'banner_image_description' => $banner_image_1,
                    'link'                     => $banner_image['link'],

                    'sort_order'               => $banner_image['sort_order'],
                    'status'               => $banner_image['status'],
                    'banner_image_id'               => $banner_image['banner_image_id'],

                    'target_blank'             => $banner_image['target_blank'] 

                ); 
            }
        //echo "<pre>"; print_r($banner_image_data); die;
        // echo "<pre>"; print_r($banner); die;
		return $banner;
	}

	public function getTotalBanners() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner WHERE store_id IN (".WSB_STORES_ID .")");

		return $query->row['total'];
	}

        
        public function findParentCategories($stroe_id){
//		$sql = "SELECT c.category_id,
//				cd.name
//				FROM ".DB_PREFIX."category c
//				INNER JOIN ".DB_PREFIX."category_description cd ON (c.category_id = cd.category_id)
//				WHERE c.parent_id = 0 AND c.status = 1 AND cd.language_id = 1";
                //echo $sql;// die('hi');
                
            
                $sql = "SELECT MI.link_title as name, MI.link_title as value, MI.value as category_id FROM " . DB_PREFIX . "menu_item AS MI"
                        . " LEFT JOIN ".DB_PREFIX."category C ON (C.category_id = MI.value) "
                        . " LEFT JOIN ".DB_PREFIX."category_to_store CS ON (C.category_id = CS.category_id) "
                        . " LEFT JOIN ".DB_PREFIX."menu M ON (M.id = MI.menu_id) "
                        . "WHERE MI.link_type = 'category' "
                        . "AND MI.parent_id = 0 "
                        . "AND MI.language_id = 1 "
                        . "AND CS.store_id = '" . $stroe_id ."' "
                        . "AND M.status = 1 "
                        . "AND MI.status = 1 "
                        . "AND LOWER(M.name) = 'desktop'";
                //echo $sql; die();

		$result = $this->db->query($sql);
		if($result->num_rows){
			$return = $result->rows;
		}else{
			$return = '';
		}
		return $return;
	}

        
        public function addImageCategories($banner_id,$banner_image_id,$categories_id){		
                    
            $cat_ids = explode(',', $categories_id); 
            foreach($cat_ids as $categoryId){
                if(!empty($categoryId)){
                    $sql = "INSERT INTO 
                           " .DB_PREFIX. "wsb_banners_image_category SET banner_image_id = ".$banner_image_id.",category_id = ".$categoryId.",banner_id = ".$banner_id."";	
                    $this->db->query($sql);
                }
            } 		
	}
        
        public function findSelectedCategories($banner_image_id){
		$return = array();
		$sql = "SELECT category_id FROM ".DB_PREFIX."wsb_banners_image_category WHERE banner_image_id = ".$banner_image_id;
		//echo $sql; die;

		$result = $this->db->query($sql);
		if($result->num_rows > 0){

			foreach($result->rows as $rows)
			{
			 $return[] = $rows['category_id'];	
			}

		}
		return $return;
	}
        
    
    public function getCartCategories($banner_image_id) 
    {
        $return = array(); 

        $sql = "SELECT positive_category, negative_category FROM ".DB_PREFIX."wsb_banners_image_cart_category WHERE banner_image_id = ".$banner_image_id;
        $result = $this->db->query($sql);

        if($result->num_rows > 0){

            foreach($result->rows as $rows)
            {
             $return['positive_category'] = unserialize($rows['positive_category']);
             $return['negative_category'] = unserialize($rows['negative_category']);  
            }

        }
        return $return;  
    } 

    /* Added by Amarat 
    */
        
    public function getStores() {
		$store_data = array();
                $sql = "SELECT * FROM " . DB_PREFIX . "store WHERE store_id IN (".WSB_STORES_ID .")";
                //echo $sql; die;
		$query = $this->db->query($sql);
                $store_data = $query->rows;
		//echo "<pre>"; print_r($store_data); die;
		return $store_data; 
	}

    /**
    * Function to get the banner type (i.e :: link, search, category)
    * 
    * @param int $banner_image_id banner_id
    * 
    * @author Ashish, Sept 2018
    * @return banner_type : link/search/category
    */
    public function getBannerType($banner_image_id)
    {
        $banner_type = '';
        $sql = "SELECT 
                    type 
                FROM 
                    ".DB_PREFIX."banner_image 
                WHERE 
                    banner_image_id = '" . $banner_image_id . "' " ;

        $result = $this->db->query($sql);

        if ($result->num_rows)
        {
            $banner_type = $result->row['type'];
        }

        return $banner_type;
    }
}