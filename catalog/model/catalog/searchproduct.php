<?php
class ModelCatalogSearchproduct extends Model {
	public function getProducts($data = array('filter_name' => 'cot')) {
        $query = $this->db->query("SELECT DISTINCT name FROM " . DB_PREFIX . "product_description WHERE name LIKE '%" .$data['filter_name'] . "%' LIMIT 0,10 ");
        //$query = $this->db->query("SELECT DISTINCT name FROM " . DB_PREFIX . "product_description WHERE name LIKE %" .$data['filter_name'] . "%");
        if($query->rows) {
            foreach ($query->rows as $data) {
                $rs[] = $data['name'];
            }
        }else{
            $rs = array();
        }
        return $rs;
	}

    public function maintainProductSearchLog($data,$aws_mysqli,$keyword,$aws){
        $user_agent  =  $_SERVER['HTTP_USER_AGENT'];
        $browser = "Unknown Browser";

            $browser_array = array(
                '/msie/i' => 'Internet Explorer',
                '/firefox/i' => 'Firefox',
                '/safari/i' => 'Safari',
                '/chrome/i' => 'Chrome',
                '/opera/i' => 'Opera',
                '/netscape/i' => 'Netscape',
                '/maxthon/i' => 'Maxthon',
                '/konqueror/i' => 'Konqueror',
                '/mobile/i' => 'Handheld Browser'
            );

            foreach ($browser_array as $regex => $value) {

                if (preg_match($regex, $user_agent)) {
                    $browser = $value;
                }
            }
        $user_agent=$browser;
        $os_platform    =   "Unknown OS Platform";
         if( isset($this->restapi->getRequestHeader()['REQUEST_BY'])) {
                if(strtoupper($this->restapi->getRequestHeader()['REQUEST_BY']) == 'IOS_APP') {
                    $os_platform='IOS_APP';
                } else {
                    $os_platform='ANDROID_APP';
                }
            }else{
                 if(CONFIG_IS_MOBILE == 1){
                        $os_platform = 'MOBILE_WEB';
                    } else {
                        $os_platform = 'WEB';
                    }
            }
        $ip=getClientIpAddress();
        $previousSearch=$this->getSearchExistInLogOrNot($aws_mysqli,$os_platform,$data,$ip,$user_agent,$aws,$keyword);
        
        if(!empty($data['page']) && $data['page']=='1'){
                if($previousSearch > 0){
                        $final_sql = "UPDATE search_term_logs
                        SET count = count + 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'
                        WHERE id = '".(int)$previousSearch."'";
                         $aws_mysqli->query($final_sql);
                        return true;
                }else{
                $sql='';
                if(isset($data['user_id']) && !empty($data['user_id'])){
                     $sql .="customer_id = '" . $this->db->escape(trim($data['user_id'])) . "', ";

                }
                 $sql='';
                if(isset($data['user_id']) && !empty($data['user_id'])){
                     $sql .="customer_id = '" . (int) $data['user_id'] . "', ";
                }
                if(isset($keyword) && !empty($keyword)){
                     $sql .="keyword = '" . $this->db->escape(trim($keyword)) . "', ";
                }
                if(isset($data['device_id']) && !empty($data['device_id'])){
                     $sql .="device_id = '" . $this->db->escape(trim($data['device_id'])) . "', ";
                }
                if(isset($data['gcm_id']) && !empty($data['gcm_id'])){
                     $sql .="gcm_id = '" . $this->db->escape(trim($data['gcm_id'])) . "', ";
                }
                     $sql .="ip_address = '" . $this->db->escape(trim($ip)) . "', ";
                     $sql .="platform = '" . $this->db->escape(trim($os_platform)) . "', ";
                     $sql .="search_date = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', ";
                     $sql .="user_agent = '" . $this->db->escape(trim($user_agent)) . "', ";
                     $sql .="count = '1', ";
                     $sql .="date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'";


                     if($os_platform=='IOS_APP' || $os_platform=='ANDROID_APP'){
                         if(!empty($data['user_id'])){
                                $final_sql = "INSERT INTO search_term_logs SET ". " $sql ";
                                $aws_mysqli->query($final_sql);
                        }
                     }else{

                                $final_sql = "INSERT INTO search_term_logs SET ". " $sql ";
                                $aws_mysqli->query($final_sql);
                     }
                     return true;
                     //aws_mysqli
               }
           }
    }

    public function getSearchExistInLogOrNot($aws_mysqli,$os_platform,$data,$ip,$user_agent,$aws,$keyword) {
        $condition='';
            if(isset($data['user_id']) && !empty($data['user_id'])){
               $condition .=" customer_id = '".(int)$data['user_id']."' ";
            }
            else{
                $condition .=" customer_id IS NULL ";
            }
            if(isset($os_platform) && !empty($os_platform)){
               $condition .=" AND platform = '".$this->db->escape(trim($os_platform))."' ";
            }
            if(isset($ip) && !empty($ip)){
               $condition .=" AND ip_address = '".$this->db->escape(trim($ip))."' ";
            }
            if(isset($data['device_id']) && !empty($data['device_id'])){
               $condition .=" AND device_id = '".$this->db->escape(trim($data['device_id']))."' ";
            }
            if(isset($keyword) && !empty($keyword)){
               $condition .=" AND keyword = '".$this->db->escape(trim($keyword))."' ";
            }
            if(isset($user_agent) && !empty($user_agent)){
               $condition .=" AND user_agent = '".$this->db->escape(trim($user_agent))."' ";
            }
               $condition .=" AND DATE_FORMAT(`date_added`,'%Y-%m-%d') = '".$this->db->escape(date('Y-m-d'))."' ";

            
        $sql = "SELECT id FROM search_term_logs WHERE  $condition";


        
        $data = $aws_mysqli->query( $sql );
        if ($data->num_rows) {
            if($aws > 0){
                $row = $data->fetch_row();
                   if ( !empty( $row[0] )) {
                     return $row[0];
                   }else{
                     return 0;
                   } 
               }else{
                return $data->row['id'];
               }
        }else{
            return 0;
        }

       // return $result;
    }
}
