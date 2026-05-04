<?php 

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModelDigitalMarketingCustomUrl extends Model
{
    
        public function getCustomUrlList($data = array()) { 
            
                
		$sql = "select * from ". DB_PREFIX ."url_alias as UA LEFT JOIN " . DB_PREFIX . "custom_url_description as CD ON CD.url_alias_id = UA.url_alias_id where UA.is_custom = 1"; 
		//add filter in query
                if (!empty($data['filter_query'])) {
                    $sql .= " AND UA.query LIKE '%" . $this->db->escape(trim($data['filter_query'])) . "%'";
		}
                if (!empty($data['filter_keyword'])) {
			$sql .= " AND UA.keyword LIKE '%" . $this->db->escape(trim($data['filter_keyword'])) . "%'";
		}
                if (!empty($data['filter_meta_title'])) {
			$sql .= " AND CD.meta_title LIKE '%" . $this->db->escape(trim($data['filter_meta_title'])) . "%'";
		}
                if (isset($data['start']) || isset($data['limit'])) {
                    if ($data['start'] < 0) {
                            $data['start'] = 0;
                    }
                    if ($data['limit'] < 1) {
                            $data['limit'] = 30;                    
                    }
                    $sql .= " ORDER BY UA.url_alias_id DESC LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                //echo $sql; die; 
		$query = $this->db->query($sql);
                //echo count($query->rows); die;
                return $query->rows;
	}

        public function getTotalCustomUrls($data = array()) { 
            
		$sql = "select COUNT(DISTINCT UA.url_alias_id) AS total from ". DB_PREFIX ."url_alias as UA LEFT JOIN " . DB_PREFIX . "custom_url_description as CD ON CD.url_alias_id = UA.url_alias_id where UA.is_custom = 1";
                //add filter in query
                if (!empty($data['filter_query'])) {
                    $sql .= " AND UA.query LIKE '%" . $this->db->escape(trim($data['filter_query'])) . "%'";
		}
                if (!empty($data['filter_keyword'])) {
			$sql .= " AND UA.keyword LIKE '%" . $this->db->escape(trim($data['filter_keyword'])) . "%'";
		}
                if (!empty($data['filter_meta_title'])) {
			$sql .= " AND CD.meta_title LIKE '%" . $this->db->escape(trim($data['filter_meta_title'])) . "%'";
		}
                //echo $sql; die;
                $query = $this->db->query($sql);
                //echo $query->row['total']; die;
                return $query->row['total'];
	}
        
        public function addCustomUrls($data = array()) { 
            
            $sql = "INSERT INTO ". DB_PREFIX ."url_alias  SET
                                query = '" . trim($this->db->escape($data['query'])) . "',
                                keyword = '" . trim($this->db->escape($data['keyword'])) . "',
                                is_redirect_301 = " . (int)$data['is_redirect_301'] . ",
                                url_type = '" . trim($this->db->escape($data['url_type'])) . "',
                                search_id = '" . trim($this->db->escape($data['search_id'])) . "',
                                is_custom = '1'";  
                                
            $query = $this->db->query($sql);
            $coutom_url_id = $this->db->getLastId();
            
            //add record in oc_custom_url_description
            if($coutom_url_id > 0){
                $sql = "INSERT INTO ". DB_PREFIX ."custom_url_description  SET 
                            title =  '" . trim($this->db->escape($data['title'])) . "', 
                            description =  '" . trim($this->db->escape($data['description'])) . "', 
                            short_description =  '" . trim($this->db->escape($data['short_description'])) . "', 
                            meta_title =  '" . trim($this->db->escape($data['meta_title'])) . "', 
                            meta_description =  '" . trim($this->db->escape($data['meta_description'])) . "', 
                            url_alias_id =  ". $coutom_url_id;  
                
                $this->db->query($sql);
            }
            
            
        }
    
        
        public function editCustomUrls($coutom_url_id, $data) {  
            $sql = "UPDATE ". DB_PREFIX ."url_alias SET 
                                query = '" . trim($this->db->escape($data['query'])) . "',
                                keyword = '" . trim($this->db->escape($data['keyword'])) . "',
                                is_redirect_301 = " . (int)$data['is_redirect_301'] . ",
                                url_type = '" . trim($this->db->escape($data['url_type'])) . "',
                                search_id = '" . trim($this->db->escape($data['search_id'])) . "',    
                                is_custom = '1'
                                WHERE url_alias_id =  ". $coutom_url_id;  
            //echo $sql; die;
            $query = $this->db->query($sql);
            
            //edit record in oc_custom_url_description
            if($query){
                $sql = "UPDATE ". DB_PREFIX ."custom_url_description  SET 
                            title =  '" . trim($this->db->escape($data['title'])) . "',  
                            description =  '" . trim($this->db->escape($data['description'])) . "',  
                            short_description =  '" . trim($this->db->escape($data['short_description'])) . "',  
                            meta_title =  '" . trim($this->db->escape($data['meta_title'])) . "', 
                            meta_description =  '" . trim($this->db->escape($data['meta_description'])) . "' 
                            WHERE url_alias_id =  ". $coutom_url_id; 
                //echo $sql; die;
                $this->db->query($sql);
            }
            
            
        }
        //for get custom_url_info for to show in edit case
        public function getCustomUrlById($custom_url_id) {
                $data = array();
                $sql = "SELECT * FROM " . DB_PREFIX . "custom_url_description as CD LEFT JOIN " . DB_PREFIX . "url_alias as UA ON CD.url_alias_id = UA.url_alias_id where UA.url_alias_id = " . (int)$custom_url_id;
                //echo $sql; die;
                $query = $this->db->query($sql);
		$data = $query->row;
                return $data;
	}
        
        
        //for delete
        public function deleteCustomUrl($custom_url_id) {   
            $sql = "DELETE from ". DB_PREFIX ."custom_url_description WHERE url_alias_id = " . $custom_url_id; 
            $this->db->query($sql);
            
            
            $sql = "DELETE FROM " . DB_PREFIX . "url_alias WHERE url_alias_id = " . $custom_url_id; 
            $query = $this->db->query($sql);
            
            
        }
        
        
        //for check slug exits 
        public function isSlugExits($keyword, $id){
            
            $where = " WHERE keyword = '" . $keyword . "'"; 
            if($id != ''){
                $where .= ' AND url_alias_id != ' .$id;
            }
            
            $sql = "SELECT url_alias_id FROM ". DB_PREFIX ."url_alias" . $where; 
            $query = $this->db->query($sql);
            if($query->num_rows == 1){
                return TRUE;
            }else{
                return FALSE;
            }
        } 
        
        //for get custom_url_info for to show in edit case
        public function getCategoryByName($name) {
            $data = array();
            $sql = "SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword = '" . $name ."' AND is_custom = 0";
            //echo $sql; die;
            $query = $this->db->query($sql);
            //$data = (array) $result; 
            if($query->num_rows > 0){
                $result = $query->row;
                $cat_ids = explode("=", $result['query']);
                $data['cat_id'] = end($cat_ids);
            }
            return $data;   
	}
        
        
        
        
    
}

