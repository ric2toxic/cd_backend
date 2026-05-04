<?php 
class ModelPickersPickers extends Model
{
	public function getPickerLists($data = array()) { 
                
                //Get seller city code 
                $seller_city_code = '';
                if (!empty($data['filter_seller'])) {
                    $sql1 = "select MS.pickup_city_code as city_code from ". DB_PREFIX ."ms_seller as MS where MS.seller_id = " . $this->db->escape(trim($data['filter_seller'])) ; 
                    $query = $this->db->query($sql1);
                    $res = $query->row;
                    $seller_city_code = $res['city_code'];
                }
                //End get seller city code 
                
                if (!empty($data['filter_seller'])) {  
                    //query for filter_seller(filter by seller)
                    $sql = "select *, PO.id as picker_id from ". DB_PREFIX ."pickup_operations as PO LEFT JOIN " . DB_PREFIX ."seller_pickup as SP ON PO.id = SP.picker_id WHERE 1"; 
                }else{
                    //normal query to show all record from pickup_operations
                    $sql = "select *, PO.id as picker_id from ". DB_PREFIX ."pickup_operations as PO WHERE 1"; 
                }
                //add filter in query
                if (!empty($data['filter_name'])) {
			
                    //for full name match
                    if(preg_match('/\s/',trim($data['filter_name']))){
                        $name = explode(' ', trim($data['filter_name']));
                        $first_name = $name['0']; 
                        $last_name = $name['1'];
//                        $sql .= "  AND ( '" . $first_name . "' LIKE Concat(Concat('%',`first_name`),'%')" 
//                             ." AND '" . $last_name ."' LIKE  Concat(Concat('%',`last_name`),'%'))";
                          $sql .= " AND ( PO.first_name LIKE '%" . $this->db->escape($first_name) . "'";
			  $sql .= " AND PO.last_name LIKE '" . $this->db->escape($last_name) . "%' )";  
                        
                    }else{
                        $sql .= " AND ( PO.first_name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%'";
			$sql .= " OR PO.last_name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%' )";
                        
                    }
                    
		}
                //echo $sql; die;
                if (!empty($data['filter_pickup_city_code'])) {
			$sql .= " AND PO.pickup_city_code LIKE '%" . $this->db->escape(trim($data['filter_pickup_city_code'])) . "%'";
			
		}
//                if (!empty($seller_city_code)) {
//			$sql .= " AND PO.pickup_city_code = '" . $this->db->escape(trim($seller_city_code)) . "'";
//			
//		}
                if (!empty($data['filter_seller'])) {  
			$sql .= " AND SP.seller_id = '" . $this->db->escape(trim($data['filter_seller'])) . "'";
			
		}
                if (!empty($data['filter_pickup_city'])) {
			$sql .= " AND PO.pickup_city LIKE '%" . $this->db->escape(trim($data['filter_pickup_city'])) . "%'";
			
		}
                if ($data['filter_status'] != '') {
                    
			$sql .= " AND PO.status = " . $this->db->escape(trim($data['filter_status'])) ;
			
		}
                
                if (!empty($data['filter_seller'])) {  
                    $sql .= " GROUP BY SP.picker_id";
                }
                if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 30;                    
			}
			$sql .= " ORDER BY PO.id DESC LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                
                //echo $sql; die; 
		$query = $this->db->query($sql);
                //echo count($query->rows); die;
                return $query->rows;
	}

        public function getTotalPickers($data = array()) { 
            
		$sql = "select COUNT(DISTINCT PO.id) AS total from ". DB_PREFIX ."pickup_operations as PO where 1"; 
                //add filter in query
                if (!empty($data['filter_name'])) {
			
                    //for full name match
                    if(preg_match('/\s/',trim($data['filter_name']))){
                        $name = explode(' ', trim($data['filter_name']));
                        $first_name = $name['0']; 
                        $last_name = $name['1'];
//                        $sql .= "  AND ( '" . $first_name . "' LIKE Concat(Concat('%',`first_name`),'%')" 
//                             ." AND '" . $last_name ."' LIKE  Concat(Concat('%',`last_name`),'%'))";
                          $sql .= " AND ( PO.first_name LIKE '%" . $this->db->escape($first_name) . "'";
			  $sql .= " AND PO.last_name LIKE '" . $this->db->escape($last_name) . "%' )";  
                        
                    }else{
                        $sql .= " AND ( PO.first_name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%'";
			$sql .= " OR PO.last_name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%' )";
                        
                    }
                    
		}
                if (!empty($data['filter_pickup_city_code'])) {
			$sql .= " AND PO.pickup_city_code LIKE '%" . $this->db->escape(trim($data['filter_pickup_city_code'])) . "%'";
			
		}
                if (!empty($data['filter_pickup_city'])) {
			$sql .= " AND PO.pickup_city LIKE '%" . $this->db->escape(trim($data['filter_pickup_city'])) . "%'";
			
		}
                if ($data['filter_status'] != '') {
			$sql .= " AND PO.status = " . $this->db->escape(trim($data['filter_status'])) ;
			
		}
                $query = $this->db->query($sql);
                //echo $query->row['total']; die;
                return $query->row['total'];
	}
        
        public function addPicker($data = array()) { 
            
            $password_secret = password_hash($data['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO ". DB_PREFIX ."pickup_operations  
                            SET first_name = '" . trim($this->db->escape($data['first_name'])) . "',
                                last_name = '" . trim($this->db->escape($data['last_name'])) . "',
                                phone_no = '" . trim($this->db->escape($data['phone'])) . "',
                                email = '" . trim($this->db->escape($data['email'])) . "', 
                                password = '" . $this->db->escape($password_secret) . "',
                                device_id = '" . trim($this->db->escape($data['device_id'])) . "',
                                pickup_city = '" . trim($this->db->escape($data['pickup_city'])) . "',
                                pickup_city_code = '" . trim($data['pickup_city_code']) . "',
                                status = '" . (int)($data['status']) . "',
                                created =  NOW(), modified = NOW()"; 
            //echo $sql; die;
            $query = $this->db->query($sql);
            $picker_id = $this->db->getLastId();
            
            //add record in oc_seller_pickup
            if($picker_id > 0){

                foreach($data['seller'] as $seller_id){
                 
                 $check_sql = $this->db->query("select id from ". DB_PREFIX ."seller_pickup where  seller_id = '" . $seller_id . "'");
                 if($check_sql->num_rows == 0){
                    $sql = "INSERT INTO ". DB_PREFIX ."seller_pickup  
                                SET seller_id =  '" . $seller_id . "', 
                                 picker_id =  ". $picker_id;  
                    $this->db->query($sql);
                   } 

                }
            }
            
            $username = $this->user->getUserName();
            $logs_data['table_name'] = 'pickup_operations, seller_pickup';
            $logs_data['field_name'] = 'seller_id';
            $logs_data['ref_url']    = 'ModelPickersPickers/addPicker';
            $logs_data['old_value']  = '';
            $logs_data['new_value']  = $data;
            $logs_data['comment']    = 'new pickup boy entry and assign seller';
            $logs_data['file_location']    = 'ModelPickersPickers/addPicker';
            $logs_data['user_type']    = $this->user->getGroupName();
            $logs_data['source_field'] = 'pickup_add';
            $logs_data['user_id']      = $this->user->getId();
            $logs_data['name']         = $username['name'];
            $logs_data['username']     =  $username['username'];
            $this->seller_pickup_logs($logs_data);
            
            
        }
    
        
        public function editPicker($picker_id, $data) {  
           
           $old_data = array();
           $old_query = $this->db->query("SELECT * from ". DB_PREFIX ."pickup_operations where id = " . $picker_id);
           $old_data['pickup'] = $old_query->row;

           $old_query = $this->db->query("SELECT * from ". DB_PREFIX ."seller_pickup WHERE picker_id = " . $picker_id);
           $old_data['seller_pickup'] = $old_query->rows;

            $sql = "UPDATE ". DB_PREFIX ."pickup_operations  
                            SET first_name = '" . trim($this->db->escape($data['first_name'])) . "',
                                last_name = '" . trim($this->db->escape($data['last_name'])) . "',
                                phone_no = '" . trim($this->db->escape($data['phone'])) . "',
                                email = '" . trim($this->db->escape($data['email'])) . "',
                                pickup_city = '" . trim($this->db->escape($data['pickup_city'])) . "',
                                pickup_city_code = '" . trim($data['pickup_city_code']) . "',
                                device_id = '" . trim($this->db->escape($data['device_id'])) . "',
                                status = '" . (int)($data['status']) . "',
                                modified = NOW() WHERE id = " . $picker_id; 
            //echo $sql; die;
            $query = $this->db->query($sql);

           if ($data['password']) {

            $password_secret = password_hash($data['password'], PASSWORD_DEFAULT);
            $this->db->query("UPDATE " . DB_PREFIX . "pickup_operations SET password = '" . $this->db->escape($password_secret) . "' WHERE id = '" . (int)$picker_id . "'");
            }
             
            //update oc_seller_pickup
            if($query){
                
                //delete picker records
                $sql = "DELETE FROM " . DB_PREFIX . "seller_pickup WHERE picker_id = " . (int) $picker_id; 
                $query = $this->db->query($sql);
                
                foreach($data['seller'] as $seller_id){

                  $check_sql = $this->db->query("select id from ". DB_PREFIX ."seller_pickup where  seller_id = '" . (int) $seller_id . "'");
                 if($check_sql->num_rows == 0)
                  {
                    $sql = "INSERT INTO ". DB_PREFIX ."seller_pickup  
                                SET seller_id =  '" . (int) $seller_id . "', 
                                 picker_id =  ". (int) $picker_id;   
                    $this->db->query($sql);
                   }  
                    
                }
            }

            if($data['status'] == 0)
                 {
                    $assign_sql = "DELETE from ". DB_PREFIX ."seller_pickup_assign WHERE assign_id = " . (int) $picker_id; 
                    $this->db->query($assign_sql); 
                 } 
            
            $username = $this->user->getUserName();
            $logs_data['table_name'] = 'pickup_operations, seller_pickup';
            $logs_data['field_name'] = 'seller_id';
            $logs_data['ref_url']    = 'ModelPickersPickers/editPicker';
            $logs_data['old_value']  = $old_data;
            $logs_data['new_value']  = $data;
            $logs_data['comment']    = 'edit pickup boy entry and assign seller';
            $logs_data['file_location']    = 'ModelPickersPickers/editPicker';
            $logs_data['user_type']    = $this->user->getGroupName();
            $logs_data['source_field'] = 'pickup_edit';
            $logs_data['user_id']      = $this->user->getId();
            $logs_data['name']         = $username['name'];
            $logs_data['username']     =  $username['username'];
     
            $this->seller_pickup_logs($logs_data);
            
        }
        //for get Pickerinfo for to show in edit case
        public function getPickerById($picker_id) {
        $data = array();
            
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pickup_operations where id = " . (int)$picker_id );
		$data = $query->row;
                $pickup_city_code = $data['pickup_city_code'];
                
                //get picker seller
                $sql = "SELECT MS.nickname AS nickname, MS.company AS company, MS.seller_id AS seller_id FROM " . DB_PREFIX . "seller_pickup AS SP"
                        . " LEFT JOIN " . DB_PREFIX. "ms_seller AS MS ON SP.seller_id = MS.seller_id "
                        . " WHERE SP.picker_id = " . (int) $picker_id  
                        . " AND MS.seller_status = 1 ORDER BY MS.nickname ASC"; 
                //echo $sql; die;
                $query = $this->db->query($sql);
                $seller = array();  
                $seller_data = array();
                foreach($query->rows as $seller){
                    $seller['seller_id'] = $seller['seller_id'];
                    $seller['nickname'] = $seller['nickname'];
                    $seller['company'] = $seller['company'];
                    $seller['city_code'] = $pickup_city_code;
                    
                    $seller_data[] = $seller;
                } 
                $data['seller'] = $seller_data;


                //assign seller
                $assign_seller_data = array();
                $sql = "SELECT MS.nickname, MS.company, MS.seller_id, SPA.id, SPA.assign_id, SPA.start_date, SPA.end_date, PO.first_name, PO.last_name FROM " . DB_PREFIX . "seller_pickup_assign AS SPA"
                        . " INNER JOIN " . DB_PREFIX. "ms_seller AS MS ON SPA.seller_id = MS.seller_id "
                        . " INNER JOIN " . DB_PREFIX. "pickup_operations AS PO ON SPA.assign_id = PO.id "
                        . " WHERE SPA.pickup_id = " . (int) $picker_id 
                        . " AND SPA.end_date >=  '".$this->db->escape(date('Y-m-d 00:00:00'))."' " 
                        . " AND MS.seller_status = 1 ORDER BY MS.nickname ASC";
                $query = $this->db->query($sql);
                
                $i=0;
                foreach($query->rows as $assign_seller)
                  {
                    $assign_seller_data[$i]['id']         = $assign_seller['id'];
                    $assign_seller_data[$i]['assign_id']  = $assign_seller['assign_id'];
                    $assign_seller_data[$i]['first_name'] = $assign_seller['first_name'];
                    $assign_seller_data[$i]['last_name']  = $assign_seller['last_name'];
                    $assign_seller_data[$i]['start_date'] = $assign_seller['start_date'];
                    $assign_seller_data[$i]['end_date']   = $assign_seller['end_date'];
                    $assign_seller_data[$i]['seller_id']  = $assign_seller['seller_id'];
                    $assign_seller_data[$i]['nickname']   = $assign_seller['nickname'];
                    $assign_seller_data[$i]['company']    = $assign_seller['company'];
                    $i++;
                  }  

                  $data['assign_seller'] = $assign_seller_data; 

                //assign seller
                $self_assign_seller_data = array();
                $sql = "SELECT MS.nickname, MS.company, MS.seller_id, SPA.id, SPA.assign_id, SPA.start_date, SPA.end_date, PO.first_name, PO.last_name FROM " . DB_PREFIX . "seller_pickup_assign AS SPA"
                        . " INNER JOIN " . DB_PREFIX. "ms_seller AS MS ON SPA.seller_id = MS.seller_id "
                        . " INNER JOIN " . DB_PREFIX. "pickup_operations AS PO ON SPA.assign_id = PO.id "
                        . " WHERE SPA.assign_id = " . (int) $picker_id 
                        . " AND SPA.end_date >=  '".$this->db->escape(date('Y-m-d 00:00:00'))."' " 
                        . " AND MS.seller_status = 1 ORDER BY MS.nickname ASC";
                $query = $this->db->query($sql);
                
                $i=0;
                foreach($query->rows as $assign_seller)
                  {
                    $self_assign_seller_data[$i]['id']         = $assign_seller['id'];
                    $self_assign_seller_data[$i]['assign_id']  = $assign_seller['assign_id'];
                    $self_assign_seller_data[$i]['first_name'] = $assign_seller['first_name'];
                    $self_assign_seller_data[$i]['last_name']  = $assign_seller['last_name'];
                    $self_assign_seller_data[$i]['start_date'] = $assign_seller['start_date'];
                    $self_assign_seller_data[$i]['end_date']   = $assign_seller['end_date'];
                    $self_assign_seller_data[$i]['seller_id']  = $assign_seller['seller_id'];
                    $self_assign_seller_data[$i]['nickname']   = $assign_seller['nickname'];
                    $self_assign_seller_data[$i]['company']    = $assign_seller['company'];
                    $i++;
                  } 

                $data['self_assign_seller'] = $self_assign_seller_data;      

                return $data;
	    }
     
        //for get Pickerinfo
        public function getAllPickerInfo() {
        $result = array();
            
        $pickup_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pickup_operations" );

        foreach($pickup_query->rows as $row)
        {      
               $data = array(); 
               $data = $row;
                $pickup_city_code = $data['pickup_city_code'];
                $picker_id        = $data['id'];
                //get picker seller
                $sql = "SELECT MS.nickname AS nickname, MS.company AS company, MS.seller_id AS seller_id FROM " . DB_PREFIX . "seller_pickup AS SP"
                        . " LEFT JOIN " . DB_PREFIX. "ms_seller AS MS ON SP.seller_id = MS.seller_id "
                        . " WHERE SP.picker_id = " . (int) $picker_id  
                        . " AND MS.seller_status = 1 ORDER BY MS.nickname ASC"; 
                //echo $sql; die;
                $query = $this->db->query($sql);
                $seller = array();  
                $seller_data = array();
                foreach($query->rows as $seller){
                    $seller['seller_id'] = $seller['seller_id'];
                    $seller['nickname'] = $seller['nickname'];
                    $seller['company'] = $seller['company'];
                    $seller['city_code'] = $pickup_city_code;
                    
                    $seller_data[] = $seller;
                } 
                $data['seller'] = $seller_data;


                //assign seller
                $assign_seller_data = array();
                $sql = "SELECT MS.nickname, MS.company, MS.seller_id, SPA.id, SPA.assign_id, SPA.start_date, SPA.end_date, PO.first_name, PO.last_name FROM " . DB_PREFIX . "seller_pickup_assign AS SPA"
                        . " INNER JOIN " . DB_PREFIX. "ms_seller AS MS ON SPA.seller_id = MS.seller_id "
                        . " INNER JOIN " . DB_PREFIX. "pickup_operations AS PO ON SPA.assign_id = PO.id "
                        . " WHERE SPA.pickup_id = " . (int) $picker_id 
                        . " AND SPA.end_date >=  '".$this->db->escape(date('Y-m-d 00:00:00'))."' " 
                        . " AND MS.seller_status = 1 ORDER BY MS.nickname ASC";
                $query = $this->db->query($sql);
                
                $i=0;
                foreach($query->rows as $assign_seller)
                  {
                $assign_seller_data[$assign_seller['seller_id']]['id']         = $assign_seller['id'];
                $assign_seller_data[$assign_seller['seller_id']]['assign_id']  = $assign_seller['assign_id'];
                $assign_seller_data[$assign_seller['seller_id']]['first_name'] = $assign_seller['first_name'];
                $assign_seller_data[$assign_seller['seller_id']]['last_name']  = $assign_seller['last_name'];
                $assign_seller_data[$assign_seller['seller_id']]['start_date'] = $assign_seller['start_date'];
                $assign_seller_data[$assign_seller['seller_id']]['end_date']   = $assign_seller['end_date'];
                $assign_seller_data[$assign_seller['seller_id']]['seller_id']  = $assign_seller['seller_id'];
                $assign_seller_data[$assign_seller['seller_id']]['nickname']   = $assign_seller['nickname'];
                $assign_seller_data[$assign_seller['seller_id']]['company']    = $assign_seller['company'];
                    $i++;
                  }  

                  $data['assign_seller'] = $assign_seller_data; 

                //assign seller
                $self_assign_seller_data = array();
                $sql = "SELECT MS.nickname, MS.company, MS.seller_id, SPA.id, SPA.assign_id, SPA.start_date, SPA.end_date, PO.first_name, PO.last_name FROM " . DB_PREFIX . "seller_pickup_assign AS SPA"
                        . " INNER JOIN " . DB_PREFIX. "ms_seller AS MS ON SPA.seller_id = MS.seller_id "
                        . " INNER JOIN " . DB_PREFIX. "pickup_operations AS PO ON SPA.pickup_id = PO.id "
                        . " WHERE SPA.assign_id = " . (int) $picker_id 
                        . " AND SPA.end_date >=  '".$this->db->escape(date('Y-m-d 00:00:00'))."' " 
                        . " AND MS.seller_status = 1 ORDER BY MS.nickname ASC";
                $query = $this->db->query($sql);
                
                $i=0;
                foreach($query->rows as $assign_seller)
                  {
                    $self_assign_seller_data[$i]['id']         = $assign_seller['id'];
                    $self_assign_seller_data[$i]['assign_id']  = $assign_seller['assign_id'];
                    $self_assign_seller_data[$i]['first_name'] = $assign_seller['first_name'];
                    $self_assign_seller_data[$i]['last_name']  = $assign_seller['last_name'];
                    $self_assign_seller_data[$i]['start_date'] = $assign_seller['start_date'];
                    $self_assign_seller_data[$i]['end_date']   = $assign_seller['end_date'];
                    $self_assign_seller_data[$i]['seller_id']  = $assign_seller['seller_id'];
                    $self_assign_seller_data[$i]['nickname']   = $assign_seller['nickname'];
                    $self_assign_seller_data[$i]['company']   = $assign_seller['company'];
                    $i++;
                  } 
                $data['self_assign_seller'] = $self_assign_seller_data;      
           
            $result[] = $data;
            }
           return $result;
        }

        
        //for delete
        public function deletePicker($picker_id) {  
           $data = array();
           $query = $this->db->query("SELECT * from ". DB_PREFIX ."pickup_operations WHERE id = " . (int) $picker_id);
           $data['pickup'] = $query->rows;

           $query = $this->db->query("SELECT * from ". DB_PREFIX ."seller_pickup WHERE picker_id = " . (int) $picker_id);
           $data['seller_pickup'] = $query->rows;

           $query = $this->db->query("SELECT * from ". DB_PREFIX ."seller_pickup_assign WHERE pickup_id = " . (int) $picker_id." or assign_id = " . (int) $picker_id);
           $data['seller_pickup_assign'] = $query->rows;

            $username = $this->user->getUserName();
            $logs_data['table_name'] = 'pickup_operations, seller_pickup, seller_pickup_assign';
            $logs_data['field_name'] = 'seller_id';
            $logs_data['ref_url']    = 'ModelPickersPickers/deletePicker';
            $logs_data['old_value']  = $data;
            $logs_data['new_value']  = '';
            $logs_data['comment']    = 'delete pickup boy and assign seller';
            $logs_data['file_location']    = 'ModelPickersPickers/deletePicker';
            $logs_data['user_type']    = $this->user->getGroupName();
            $logs_data['source_field'] = 'pickup_delete';
            $logs_data['user_id']      = $this->user->getId();
            $logs_data['name']         = $username['name'];
            $logs_data['username']     =  $username['username'];
            $this->seller_pickup_logs($logs_data);
            
        }
        
        //for delete
        public function assign_seller_delete($id) {  
            
            $query = $this->db->query("SELECT * from ". DB_PREFIX ."seller_pickup_assign WHERE id = " . (int) $id);
            $data = $query->rows;

            $sql = "DELETE from ". DB_PREFIX ."seller_pickup_assign WHERE id = " . (int) $id; 
            $this->db->query($sql);  

            $username = $this->user->getUserName();
            $logs_data['table_name'] = 'seller_pickup_assign';
            $logs_data['field_name'] = 'seller_id';
            $logs_data['ref_url']    = 'ModelPickersPickers/assign_seller_delete';
            $logs_data['old_value']  = $data;
            $logs_data['new_value']  = '';
            $logs_data['comment']    = 'delete assign seller';
            $logs_data['file_location']    = 'ModelPickersPickers/assign_seller_delete';
            $logs_data['user_type']    = $this->user->getGroupName();
            $logs_data['source_field'] = 'seller_assign_delete';
            $logs_data['user_id']      = $this->user->getId();
            $logs_data['name']         = $username['name'];
            $logs_data['username']     =  $username['username'];
            $this->seller_pickup_logs($logs_data);        
        }
                
        //for ajax
        public function getSellerByPickupCityCode($pickup_city_code,$selected_seller=array()) {  
            
            $arr = array();
            foreach($selected_seller as $seller){
                $arr[] = $seller['seller_id'];
            }
            
            $condition = '';
            if(count($selected_seller) > 0){
                $condition = "AND seller_id NOT IN (" . implode(",",$arr) . ")";
            }
            
            $sql = "Select seller_id, nickname, company from ". DB_PREFIX ."ms_seller WHERE pickup_city_code = '" . $pickup_city_code . "' AND seller_status = 1 and (`seller_id`) not in (select `seller_id` from ". DB_PREFIX ."seller_pickup)  ORDER BY nickname"; 
            //echo $sql; die;
            $query = $this->db->query($sql);
            return $query->rows; 
        }
        
        
	public function getSellerList($sellers=array()){
                $condition = '';
                if(count($sellers) > 0){
                    $condition = "AND seller_id IN (" . implode(",",$sellers) . ")";
                }
            
		$sql = "SELECT ms.seller_id, ms.nickname, ms.pickup_city_code FROM " . DB_PREFIX ."ms_seller as ms left join " . DB_PREFIX . "pickup_operations as p on p.pickup_city_code = ms.pickup_city_code WHERE ms.pickup_city_code != '' AND ms.seller_status = 1 group by ms.`seller_id`"
                        . $condition . " ORDER BY ms.nickname ASC  ";
                $query = $this->db->query($sql);
                return $query->rows;
	}
        
    public function getSellerListByIds($sellers=array()){
            
        $sql = "SELECT ms.seller_id, ms.nickname, ms.company, ms.pickup_city_code FROM " . DB_PREFIX ."ms_seller as ms WHERE ms.seller_status = 1 AND ms.seller_id IN (" . implode(",",$sellers) . ") ORDER BY ms.nickname ASC  ";
                $query = $this->db->query($sql);
                return $query->rows;
    }


        public function isEmailExits($email, $id=null){
            
            $where = "WHERE 1";     
            $where .= " AND p.email = '" . $this->db->escape($email) . "'"; 
            if($id > 0){
                $where .= " AND p.id != " . (int) $id;     
            }

            $sql = "SELECT p.id FROM " . DB_PREFIX . "pickup_operations as p " . $where;
            $query = $this->db->query($sql);
            if($query->num_rows > 0){
                return true;
            }else{
                return false;
            }
            
	}
        
        public function isPhoneExits($phone, $id=null){
            
            $where = "WHERE 1";     
            $where .= " AND p.phone_no = '" . $this->db->escape($phone) . "'";  
            if($id > 0){
                $where .= " AND p.id != " . (int) $id;     
            }

            $sql = "SELECT p.id FROM " . DB_PREFIX . "pickup_operations as p " . $where;
            $query = $this->db->query($sql);
            if($query->num_rows > 0){
                return true;
            }else{
                return false;
            }
            
	}
        
        public function checkSameCitySeller($sellers,$pickup_city_code){
            
            $condition = '';
            if(count($sellers) > 0){
                $condition = " WHERE seller_id IN (" . implode(",",$sellers) . ")";
            }
            
            $sql = "SELECT pickup_city_code FROM " . DB_PREFIX . "ms_seller " . $condition;
            //echo $sql; die;
            $query = $this->db->query($sql);
            $result = $query->rows;
            
            //create single diementional array
            $res = array();
            $res['0'] = $pickup_city_code; 
            foreach ($result as $val) {
                if($val['pickup_city_code'] != $pickup_city_code){
                    return false;
                }
                
            }
            return true;
        }
        
        
        
        public function getPickupByCityCode($pickup_city_code, $pickup_id)
        {

          $sql = "SELECT * FROM " . DB_PREFIX . "pickup_operations where pickup_city_code = '".$pickup_city_code."' and id != '".(int) $pickup_id."' and status = 1";
          $query = $this->db->query($sql);
          return $query->rows; 
        }

        public function getPickerCities()
        {
          $sql = "SELECT pickup_city, pickup_city_code FROM " . DB_PREFIX . "pickup_operations group by pickup_city_code";
          $query = $this->db->query($sql);
          return $query->rows; 
        }

        public function assign_seller_save($data=array())
        { 
               $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seller_pickup_assign WHERE pickup_id = " . (int) $data['pickup_id']);
                $old_data = $query->rows;

                $sql = "DELETE FROM " . DB_PREFIX . "seller_pickup_assign WHERE pickup_id = " . (int) $data['pickup_id']." and end_date < '".$this->db->escape(date('Y-m-d 23:59:59'))."' "; 
                $query = $this->db->query($sql);

                foreach($data['sellers'] as $seller_id){
                    $sql = "INSERT INTO ". DB_PREFIX ."seller_pickup_assign  
                                SET pickup_id =  '" . $data['pickup_id'] . "', 
                                 assign_id =  '". $data['assign_id']."',
                                 seller_id =  '". $seller_id."',
                                 start_date =  '". $data['start_date']."',
                                 end_date =  '". $data['end_date']."',
                                 status =  1";  
                    $this->db->query($sql);
                }

            $username = $this->user->getUserName();
            $logs_data['table_name'] = 'seller_pickup_assign';
            $logs_data['field_name'] = 'seller_id';
            $logs_data['ref_url']    = 'ModelPickersPickers/assign_seller_save';
            $logs_data['old_value']  = $old_data;
            $logs_data['new_value']  = $data;
            $logs_data['comment']    = 'assign seller';
            $logs_data['file_location']    = 'ModelPickersPickers/assign_seller_save';
            $logs_data['user_type']    = $this->user->getGroupName();
            $logs_data['source_field'] = 'seller_assign';
            $logs_data['user_id']      = $this->user->getId();
            $logs_data['name']         = $username['name'];
            $logs_data['username']     =  $username['username'];
            $this->seller_pickup_logs($logs_data);  
          
        }
        
        public function seller_pickup_logs($data = array()) { 
            
            $admin_change_data                  = array();
            $admin_change_data['table_name']    = $data['table_name'];
            $admin_change_data['field_name']    = $data['field_name'];
            $admin_change_data['ref_url']       = $data['ref_url'];
            $admin_change_data['old_value']     = serialize($data['old_value']);
            $admin_change_data['new_value']     = serialize($data['new_value']);
            $admin_change_data['comment']       = $data['comment'];
            $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
            $admin_change_data['ip_address']    = $this->request->getIpAddress;
            $admin_change_data['file_location'] = $data['file_location'];
            $admin_change_data['user_type']     = $data['user_type'];
            $admin_change_data['source_field']  = $data['source_field'];
            $admin_change_data['user_id']       = $data['user_id'];
            $admin_change_data['name']          = $data['name'];
            $admin_change_data['username']      = $data['username'];
            

            //Call dynamic static function for entry into admin change log
            CommonLib::addAdminChangeLog($this->db, $admin_change_data);

                /*$sql = "
                    INSERT INTO ". DB_PREFIX ."admin_change_log
                    (table_name, 
                    field_name, 
                    ref_url, 
                    old_value, 
                    new_value, 
                    comment, 
                    date_added, 
                    user_agent, 
                    ip_address, 
                    file_location, 
                    user_type, 
                    source_field, 
                    user_id, 
                    name, 
                    username) 

                    VALUES ('". 
                    $this->db->escape($data['table_name']) ."','" . 
                    $this->db->escape($data['field_name']) . "','" . 
                    $this->db->escape($data['ref_url']) . "','" . 
                    serialize($data['old_value']) . "','" . 
                    serialize($data['new_value']) . "','" . 
                    $this->db->escape($data['comment']) . 
                    "',NOW(),'" . 
                    $_SERVER['HTTP_USER_AGENT'] . "','".
                    $this->request->getIpAddress."','" . 
                    $this->db->escape($data['file_location']) . "','" .
                    $this->db->escape($data['user_type']) . "','" . 
                    $this->db->escape($data['source_field']) . "','" . 
                    $this->db->escape($data['user_id']) . "','" .
                    $this->db->escape($data['name']) . "','" . 
                    $this->db->escape($data['username']) . "')"; 

            $query = $this->db->query($sql);
            $picker_id = $this->db->getLastId();*/
        }


}
