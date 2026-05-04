<?php
class ModelLocalisationLocation extends Model {

	public function getLocation($location_id) {
		$query = $this->db->query("SELECT location_id, name, address, geocode, telephone, image, timing, comment FROM " . DB_PREFIX . "location WHERE location_id = '" . (int)$location_id . "'");
		
                
                return $query->row;
	}
        
        public function getStoreLocator($page) {
        
        $data = array();
        $address = '';
        
        $where = " WHERE status = 1 AND (show_on = 'all'";
        if($page != ''){
            $where .= " OR show_on = '" . $page . "'";
        }
        $where .= ")";
        
        $sql = "SELECT * FROM " . DB_PREFIX . "location " . $where . "  order by sort_order";
        $res = $this->db->query($sql);
        if($res->num_rows > 0){
            foreach ($res->rows as $key => $val){
                foreach ($val as $k => $row){
                        
                        if($k == 'address'){
                            $address =  preg_replace("/\r\n|\r|\n/",'<br/>',$row);
                            
                        }
                        if($k == 'geocode'){
                            
                            $data[$key]['lat'] = '';
                            $data[$key]['long'] = '';
                            
                            if( isset($row) ){ 
                                $geocode = explode(",", $row);
                                $data[$key]['lat'] =  $geocode[0];
                                $data[$key]['long'] =  $geocode[1];
                            }
                        }
                        //create complete address
                        if($k == 'city'){
                            $address .= ",<br> ".$row;
                        }   
                        if($k == 'postcode' && $row != ''){ 
                            $address .= "-".$row;
                        } 
                        if($k == 'state'){
                            $address .= ", ".$row;
                        } 
                        if($k == 'country'){
                            $address .= ", ".$row;
                        } 
                       
                        $data[$key]['address'] = $address;
                        
                        $data[$key][$k] =  $row;
                    
                }
            }
        }
        return $data;    
        
    }
}
