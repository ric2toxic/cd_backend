<?php 
class ModelPickersPickers extends Model
{

       public function login($username, $password, $device_id)
       {
            $q = "SELECT id, first_name, last_name, phone_no, email, ws_access_token, device_id, pickup_city, pickup_city_code, password FROM " . DB_PREFIX . "pickup_operations WHERE status = '1' AND (LOWER(email) = '" . $this->db->escape(utf8_strtolower($username)) . "' OR phone_no = '".$this->db->escape(utf8_strtolower($username))."')";
              $pickup_query = $this->db->query($q);

            $password_valid = false;
            if ($pickup_query->num_rows) 
            {
              $password_valid = password_verify($password, $pickup_query->row['password']);
            }

            if ($password_valid) 
            {
               if($pickup_query->row['device_id'] == '')
               {
                  $sql = "UPDATE ". DB_PREFIX ."pickup_operations  
                            SET device_id = '" . trim($this->db->escape($device_id)) . "',
                                modified = NOW() WHERE id = " . (int)$pickup_query->row['id'];
                        $this->db->query($sql);
                 $pickup_query->row['password'] = '';  
                 return $pickup_query->row;
               }
               else
               {
                 if($pickup_query->row['device_id'] == $device_id)
                 {
                    $pickup_query->row['password'] = '';
                    return $pickup_query->row;
                 }
                 else
                 {
                   return 2;
                 }
               }
               
            }
            else
            {
              return 0;  
            }
       }

    public function VerifyToken($pickup_id,$access_token,$device_id, $role='pickup') {

      if($role == 'pickup')
      {
        if($device_id == 0){
            if($this->db->query("UPDATE " . DB_PREFIX . "pickup_operations SET ws_access_token = '" . $this->db->escape($access_token) . "' WHERE id = '" . $this->db->escape($pickup_id) . "'")){
                return 1;
            }else{
                return 0;
            }
        }else{
            if($this->db->query("UPDATE " . DB_PREFIX . "pickup_operations SET ws_access_token = '" . $this->db->escape($access_token) . "', device_id = '" . $this->db->escape($device_id) . "' WHERE id = '" . $this->db->escape($pickup_id) . "'")){
                return 1;
            }else{
                return 0;
            }
        }
      }
      else if($role == 'admin')
      {
        if($device_id == 0){
            if($this->db->query("UPDATE " . DB_PREFIX . "user SET access_token = '" . $this->db->escape($access_token) . "' WHERE user_id = '" . $this->db->escape($pickup_id) . "'")){
                return 1;
            }else{
                return 0;
            }
        }else{
            if($this->db->query("UPDATE " . DB_PREFIX . "user SET access_token = '" . $this->db->escape($access_token) . "', device_id = '" . $this->db->escape($device_id) . "' WHERE user_id = '" . $this->db->escape($pickup_id) . "'")){
                return 1;
            }else{
                return 0;
            }
        }        
      }  

    }


    public function checkUserByAccessToken($access_token,$pickup_id,$device_id=''){
        // If access token is empty, return false;
        if(empty($access_token)){
            return 0;
        }

        if($device_id != '')
        {
         $query = $this->db->query("SELECT id, first_name, last_name, pickup_city_code, phone_no FROM " . DB_PREFIX . "pickup_operations
                                   WHERE ws_access_token = '" . $this->db->escape($access_token) . "'
                                   AND device_id = '" . $this->db->escape($device_id) . "' AND id = '" . $this->db->escape($pickup_id) . "' and status = '1'");
        }
        else
        {
        $query = $this->db->query("SELECT id, first_name, last_name, pickup_city_code, phone_no FROM " . DB_PREFIX . "pickup_operations
                                   WHERE ws_access_token = '" . $this->db->escape($access_token) . "'
                                   AND id = '" . $this->db->escape($pickup_id) . "' and status = '1'");
        }
        
        if ($query->num_rows) 
            {
              return $query->row;
            }
            else
            {
              return 0;  
            }
    }    


    public function checkAdminUserByAccessToken($access_token,$pickup_id,$device_id=''){
        // If access token is empty, return false;
        if(empty($access_token)){
            return 0;
        }

        if($device_id != '')
        {
         $query = $this->db->query("SELECT user_id as id, firstname as first_name, lastname as last_name, branch_code as pickup_city_code FROM " . DB_PREFIX . "user WHERE access_token = '" . $this->db->escape($access_token) . "'
                                   AND device_id = '" . $this->db->escape($device_id) . "' AND user_id = '" . $this->db->escape($pickup_id) . "' and status = '1'");
        }
        else
        {
        $query = $this->db->query("SELECT user_id as id, firstname as first_name, lastname as last_name, branch_code as pickup_city_code FROM " . DB_PREFIX . "user WHERE access_token = '" . $this->db->escape($access_token) . "' AND user_id = '" . $this->db->escape($pickup_id) . "' and status = '1'");
        }
        
        if ($query->num_rows) 
            {
              return $query->row;
            }
            else
            {
              return 0;  
            }
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
                        . " WHERE SP.picker_id = " . $picker_id  
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
                return $data;
  }
        
        
        // get all seller List which are assigned to picker 
  public function getSellerList($sellers=array()){
                //$sellers = array('8355','9230','15041','15632','15910');
                
                //add condition for get seller list post data
                $condition = '';
                if(count($sellers) > 0){
                    $condition = "AND seller_id IN (" . implode(",",$sellers) . ")";
                }
            
    $sql = "SELECT ms.seller_id, ms.nickname, ms.pickup_city_code FROM " . DB_PREFIX ."ms_seller as ms left join " . DB_PREFIX . "pickup_operations as p on p.pickup_city_code = ms.pickup_city_code WHERE ms.pickup_city_code != '' AND ms.seller_status = 1 group by ms.`seller_id`"
        . $condition . " ORDER BY ms.nickname ASC  ";
        $query = $this->db->query($sql);
        return $query->rows;
  }


    public function getPickupList($pickup_id, $pickup_status='')
    {

       $sql = "SELECT MS.nickname, 
               MS.company,
               MS.seller_id, 
               MS.primary_contact_name, 
               MS.primary_contact_no, 
               MS.pickup_holder_name, 
               MS.pickup_contact_no, 
               MS.pickup_address, 
               MS.pickup_city, 
               MS.pickup_pincode, 
               MS.pickup_zone_id, 
               MS.pickup_country_id, 
               O.order_id, 
               O.order_no,
               O.date_added, 
               SO.suborder_id,
               OP.seller_invoice_id as product_invoice_id, 
               OP.order_product_id, 
               OP.product_id, 
               OP.name AS product_name, 
               OP.seller_sku AS product_model,
               OP.pickup_status AS pickup_status,
               OP.comment AS product_comment,
               OP.transfer_price_per_piece AS transfer_price_per_piece, 
               OP.quantity as total_set,
               (OP.quantity * OP.piece_in_set) as total_pieces, 
               (OP.quantity * OP.piece_in_set * transfer_price_per_piece) as total_price,
               P.image AS product_image, 
               P.image_dimensions AS product_image_dimensions
               FROM " . DB_PREFIX . "ms_seller AS MS"
               . " INNER JOIN " . DB_PREFIX. "seller_pickup AS SP ON SP.seller_id = MS.seller_id "
               . " INNER JOIN " . DB_PREFIX. "order_product AS OP ON MS.seller_id = OP.seller_id "
               . " INNER JOIN " . DB_PREFIX. "order AS O ON OP.order_id = O.order_id "
               . " INNER JOIN " . DB_PREFIX. "suborder AS SO ON SO.order_id = O.order_id "
               . " INNER JOIN " . DB_PREFIX. "product AS P ON P.product_id = OP.product_id "
               . " WHERE SP.picker_id = " . $pickup_id
               . " AND (SO.order_status_id = '9' OR SO.order_status_id = '16') "
               . " AND SO.suborder_id = OP.suborder_id "
               . " AND O.store_id IN (".WSB_STORES_ID.")" 
               . " AND MS.seller_id not in (select `seller_id` from ". DB_PREFIX ."seller_pickup_assign where pickup_id = ".$pickup_id." AND DATE(start_date) <= DATE(NOW()) AND DATE(end_date) >= DATE(NOW()) ) ";
             
             if($pickup_status == '')
             {
                $sql = $sql. " AND OP.pickup_status IN ('Not_Given', 'Issue', 'Picked_Up')";
             }
             else
             {
                if($pickup_status == 1)
                {
                   $sql = $sql. " AND OP.pickup_status = 'Picked_Up'";
                }
                else
                {
                  $sql = $sql. " AND OP.pickup_status IN ('Not_Given', 'Issue')";
                }
              }

                  $sql = $sql. " AND OP.edit_type IN ('YES', 'SELLER_PARTIAL', 'SELLER_APPROVED', 'SELLER_LATER_DISPATCH')";

               $sql = $sql. " AND OP.store_pickup = 0 ". " ORDER BY O.date_added DESC";


        $query = $this->db->query($sql);
        return $query->rows;

    }
        
   
    public function getPickupAssignList($pickup_id, $pickup_status='')
    {

       $sql = "SELECT MS.nickname, 
               MS.company,
               MS.seller_id, 
               MS.primary_contact_name, 
               MS.primary_contact_no, 
               MS.pickup_holder_name, 
               MS.pickup_contact_no, 
               MS.pickup_address, 
               MS.pickup_city, 
               MS.pickup_pincode, 
               MS.pickup_zone_id, 
               MS.pickup_country_id, 
               O.order_id, 
               O.order_no,
               O.date_added, 
               SO.suborder_id,
               OP.seller_invoice_id as product_invoice_id, 
               OP.order_product_id, 
               OP.product_id, 
               OP.name AS product_name, 
               OP.seller_sku AS product_model,
               OP.comment AS product_comment,
                OP.pickup_status AS pickup_status,
               OP.transfer_price_per_piece AS transfer_price_per_piece, 
               OP.quantity as total_set,
               (OP.quantity * OP.piece_in_set) as total_pieces, 
               (OP.quantity * OP.piece_in_set * transfer_price_per_piece) as total_price,
               P.image AS product_image, 
               P.image_dimensions AS product_image_dimensions
               FROM " . DB_PREFIX . "ms_seller AS MS"
               . " INNER JOIN " . DB_PREFIX. "seller_pickup_assign AS SPA ON SPA.seller_id = MS.seller_id "
               . " INNER JOIN " . DB_PREFIX. "order_product AS OP ON MS.seller_id = OP.seller_id "
               . " INNER JOIN " . DB_PREFIX. "order AS O ON OP.order_id = O.order_id "
               . " INNER JOIN " . DB_PREFIX. "suborder AS SO ON SO.order_id = O.order_id "
               . " INNER JOIN " . DB_PREFIX. "product AS P ON P.product_id = OP.product_id "
               . " WHERE SPA.assign_id = " . $pickup_id
               . " AND DATE(SPA.start_date) <=  DATE(NOW()) "
               . " AND DATE(SPA.end_date)   >=  DATE(NOW()) "
               . " AND (SO.order_status_id = '9' OR SO.order_status_id = '16') "
               . " AND SO.suborder_id = OP.suborder_id " 
               . " AND O.store_id IN (".WSB_STORES_ID.")" 
               . " AND OP.store_pickup = 0 ";

             if($pickup_status == '')
             {
                $sql = $sql. " AND OP.pickup_status IN ('Not_Given', 'Issue', 'Picked_Up')";
             }
             else
             {
                if($pickup_status == 1)
                {
                   $sql = $sql. " AND OP.pickup_status = 'Picked_Up'";
                }
                else
                {
                  $sql = $sql. " AND OP.pickup_status IN ('Not_Given', 'Issue')";
                }
              }
               
               $sql = $sql. " AND OP.edit_type IN ('YES', 'SELLER_PARTIAL', 'SELLER_APPROVED', 'SELLER_LATER_DISPATCH')";

               $sql = $sql. " ORDER BY O.date_added DESC ";


        $query = $this->db->query($sql);
        return $query->rows;

    }


    public function getPickupOrder($pickup_id, $order_product_id)
    {

       $sql = "SELECT MS.nickname, 
               MS.company,
               MS.seller_id, 
               MS.primary_contact_name, 
               MS.primary_contact_no, 
               MS.pickup_holder_name, 
               MS.pickup_contact_no, 
               MS.pickup_address, 
               MS.pickup_city, 
               MS.pickup_pincode, 
               MS.pickup_zone_id, 
               MS.pickup_country_id, 
               O.order_id, 
               O.order_no,
               O.date_added, 
               SO.suborder_id,
               OP.seller_invoice_id as product_invoice_id, 
               OP.order_product_id, 
               OP.product_id, 
               OP.name AS product_name, 
               OP.seller_sku AS product_model,
               OP.pickup_status AS pickup_status,
               OP.seller_invoice_id AS seller_invoice_id,
               OP.comment AS product_comment,
               OP.transfer_price_per_piece AS transfer_price_per_piece, 
               OP.quantity as total_set,
               (OP.quantity * OP.piece_in_set) as total_pieces, 
               (OP.quantity * OP.piece_in_set * transfer_price_per_piece) as total_price,
               P.image AS product_image, 
               P.image_dimensions AS product_image_dimensions
               FROM " . DB_PREFIX . "ms_seller AS MS"
               . " INNER JOIN " . DB_PREFIX. "order_product AS OP ON MS.seller_id = OP.seller_id "
               . " INNER JOIN " . DB_PREFIX. "order AS O ON OP.order_id = O.order_id "
               . " INNER JOIN " . DB_PREFIX. "suborder AS SO ON SO.order_id = O.order_id "
               . " INNER JOIN " . DB_PREFIX. "product AS P ON P.product_id = OP.product_id "
               . " WHERE OP.order_product_id = ".$order_product_id
               . " AND SO.suborder_id = OP.suborder_id "
               . " AND O.store_id IN (".WSB_STORES_ID.")"; 

              $sql = $sql. " AND OP.edit_type IN ('YES', 'SELLER_PARTIAL', 'SELLER_APPROVED', 'SELLER_LATER_DISPATCH')";

        $query = $this->db->query($sql);
        return $query->row;

    }

        //for get Pickerinfo
        public function getAllPickerInfo($city_code) {
        $pickup_result = array();
            
        $pickup_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pickup_operations where pickup_city_code = '".$city_code."'" );

        foreach($pickup_query->rows as $row)
        {      
          $data = array(); 
          $result = array();
          $orders_arr = array();
          $rec_orders_arr = array();
          $rec_seller_arr = array();
          $data = $row;

          $pickup_list   = $this->getPickupList($row['id']);
          $assign_pickup_list = $this->getPickupAssignList($row['id']);
          if(count($assign_pickup_list) > 0) { $pickup_list = array_merge($pickup_list, $assign_pickup_list); }
          $data['total_pieces'] = 0;
          $data['total_orders'] = 0;
          $data['total_received_pieces'] = 0;
          $data['total_received_orders'] = 0;
          $data['seller_count'] = 0;
          $data['seller_received_count'] = 0;
          
          $i=1;
          foreach($pickup_list as $pickup)
           {
             $data['total_pieces'] += $pickup['total_pieces'];

             if (!in_array($pickup['order_id'], $orders_arr)) 
              { 
                $data['total_orders']++;
                $orders_arr[] = $pickup['order_id'];
              }
             
             if($pickup['pickup_status'] == 'Picked_Up') 
              { $data['total_received_pieces'] += $pickup['total_pieces']; }


            if (!in_array($pickup['order_id'], $rec_orders_arr)) 
            { 
              if($pickup['pickup_status'] == 'Picked_Up' || $pickup['pickup_status'] == 'Issue') 
                {
                  $data['total_received_orders']++;
                  $rec_orders_arr[] = $pickup['order_id'];
                }
            }
            
            if(!isset($result['seller'][$pickup['seller_id']]['seller_id']))
            {
              $data['seller_count']++;
              $data['seller_received_count']++;
            }

            if (!in_array($pickup['seller_id'], $rec_seller_arr)) 
            { 
              if($pickup['pickup_status'] == 'Not_Given') 
                {
                  $data['seller_received_count']--;
                  $rec_seller_arr[] = $pickup['seller_id'];
                }
            }
             
             $result['seller'][$pickup['seller_id']]['seller_id']           = $pickup['seller_id'];
             $result['seller'][$pickup['seller_id']]['company']             = $pickup['company'];
             $result['seller'][$pickup['seller_id']]['nickname']            = $pickup['nickname'];
             $result['seller'][$pickup['seller_id']]['primary_contact_name']= $pickup['primary_contact_name'];
             $result['seller'][$pickup['seller_id']]['primary_contact_no']  = $pickup['primary_contact_no'];
             $result['seller'][$pickup['seller_id']]['pickup_holder_name']  = $pickup['pickup_holder_name'];
             $result['seller'][$pickup['seller_id']]['pickup_contact_no']   = $pickup['pickup_contact_no'];
             $result['seller'][$pickup['seller_id']]['pickup_address']      = $pickup['pickup_address'];
             $result['seller'][$pickup['seller_id']]['pickup_city']         = $pickup['pickup_city'];
             $result['seller'][$pickup['seller_id']]['pickup_pincode']      = $pickup['pickup_pincode'];
             $result['seller'][$pickup['seller_id']]['pickup_zone_id']      = $pickup['pickup_zone_id'];
             $result['seller'][$pickup['seller_id']]['pickup_country_id']   = $pickup['pickup_country_id'];


             if(isset($result['seller'][$pickup['seller_id']]['seller_total']))
             {
               $result['seller'][$pickup['seller_id']]['seller_total'] += $pickup['total_price'];
             }
             else
             {
               $result['seller'][$pickup['seller_id']]['seller_total'] = $pickup['total_price'];
             } 


             if(isset($result['seller'][$pickup['seller_id']]['seller_total_pieces']))
             {
               $result['seller'][$pickup['seller_id']]['seller_total_pieces'] += $pickup['total_pieces'];
             }
             else
             {
               $result['seller'][$pickup['seller_id']]['seller_total_pieces'] = $pickup['total_pieces'];
            }                         
 
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_id']  = $pickup['order_id'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_no']  = $pickup['order_no'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['date_added']= $pickup['date_added'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['suborder_id'] = $pickup['suborder_id'];


              $seller_invoice = $this->getSellerInvoice($pickup['product_invoice_id']);

             if(isset($seller_invoice['seller_invoice_id']) && !empty($seller_invoice['seller_invoice_id']))
             {
              $pickup['seller_invoice_id']           = $seller_invoice['seller_invoice_id'];
              $pickup['seller_invoice_no']           = $seller_invoice['seller_invoice_no'];
              $pickup['invoice_physically_received'] = $seller_invoice['invoice_physically_received'];
              $pickup['invoice_image']               = ($seller_invoice['invoice_image'] != '') ? $seller_invoice['invoice_image'] : '';
             }
             else
             {
              $pickup['seller_invoice_id']           = null;
              $pickup['seller_invoice_no']           = null;
              $pickup['invoice_physically_received'] = null;
              $pickup['invoice_image']               = null;
             }

             if (!isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice']['invoice_id']) 
                ||
                !in_array($pickup['seller_invoice_id'], $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice']['invoice_id']))
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['invoice_id'] = $pickup['seller_invoice_id'];
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['invoice_no'] = $pickup['seller_invoice_no'];
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['invoice_received'] = $pickup['invoice_physically_received'];
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['invoice_image'] = $pickup['invoice_image'];
              
              if(isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['total_pieces']))
              {
                $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['total_pieces'] += $pickup['total_pieces'];
              }
              else
              {
                 $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['total_pieces'] = $pickup['total_pieces'];
              } 

              if(isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['order_total']))
              {
                $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['order_total'] += $pickup['total_price'];
              }
              else
              {
                 $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['order_total'] = $pickup['total_price'];
              }  


             }

            
             if(isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total']))
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total'] += $pickup['total_price'];
             }
             else
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total'] = $pickup['total_price'];
             }
         
             if(isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total_pieces']))
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total_pieces'] += $pickup['total_pieces'];
             }
             else
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total_pieces'] = $pickup['total_pieces'];
             }            
             
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['product_id']  = $pickup['product_id'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['order_product_id']  = $pickup['order_product_id'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['name']  = $pickup['product_name'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['model']  = $pickup['product_model'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['pieces']  = $pickup['total_pieces'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['total']  = $pickup['total_price'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['total_set']  = $pickup['total_set'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['transfer_price_per_piece']  = $pickup['transfer_price_per_piece'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['product_comment']  = $pickup['product_comment'];
            $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['pickup_status']  = $pickup['pickup_status'];

              $dimensions = unserialize($pickup['product_image_dimensions']);
              if(!empty($dimensions['width'])) { $width_orig = $dimensions['width']; }

              if(!empty($dimensions['height'])) { $height_orig = $dimensions['height']; }


              if (!empty($width_orig) && !empty($height_orig) && ($width_orig / $height_orig) > 1)
                  {
                   $image =  $this->model_tool_image->resize($pickup['product_image'], $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );
                 }
               else
                 {
                  $image = $this->model_tool_image->resize($pickup['product_image'], $this->config->get('config_image_product_width') , $this->config->get('config_image_product_height') );
                 }   
          
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['image']  = $image;

            $i++;
           }

          $data['pickup_list']  = $result;        
          $pickup_result[] = $data;
        }
           return $pickup_result;
        }

   public function getUnassignSeller()
    {

        $sql = "SELECT MS.nickname, 
               MS.company,
               MS.seller_id, 
               MS.primary_contact_name, 
               MS.primary_contact_no, 
               MS.pickup_holder_name, 
               MS.pickup_contact_no, 
               MS.pickup_address, 
               MS.pickup_city, 
               MS.pickup_pincode, 
               MS.pickup_zone_id, 
               MS.pickup_country_id 
               FROM " . DB_PREFIX . "ms_seller AS MS"
               . " LEFT OUTER JOIN " . DB_PREFIX. "seller_pickup AS SP ON MS.seller_id = SP.seller_id"
               . " where SP.seller_id is null"
               . " group by seller_id ";
              
        $query = $this->db->query($sql);
        return $query->rows;
    }


    public function getSellerInvoice($invoice_id)
    {

       $sql = "SELECT
               SI.invoice_image, 
               SI.seller_invoice_id,
               SI.seller_invoice_no,
               SI.invoice_physically_received
               FROM " . DB_PREFIX . "seller_invoice AS SI"
               . " WHERE SI.seller_invoice_id = " . (int)$invoice_id;
             
        $query = $this->db->query($sql);
        return $query->row;

    }


        public function isEmailExits($email, $id=null){
            
            $where = "WHERE 1";     
            $where .= " AND p.email = '" . $email . "'"; 
            if($id > 0){
                $where .= " AND p.id != " . $id;     
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
            $where .= " AND p.phone_no = '" . $phone . "'";  
            if($id > 0){
                $where .= " AND p.id != " . $id;     
            }

            $sql = "SELECT p.id FROM " . DB_PREFIX . "pickup_operations as p " . $where;
            $query = $this->db->query($sql);
            if($query->num_rows > 0){
                return true;
            }else{
                return false;
            }
            
	}

  public function uploadImgUsingCurl($file, $invoice_id,$suborder_id){

        if(NGINX_ENABLED=='1'){
        $ext                      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name                     =  $invoice_id.'.'.$ext;
        $directory                = 'seller_invoice/'.$suborder_id;
        $file_name_with_full_path = $file['tmp_name'];

        if (function_exists('curl_file_create')) { // php 5.5+
        $cFile = curl_file_create($file_name_with_full_path);
        } else { //
        $cFile = '@' . realpath($file_name_with_full_path);
        }

        $post = array('file'=> $cFile);
        $ch = curl_init();
        $target_url = STATIC_CONTENT_URL_SSL.'fileupload.php?directory='.$directory.'&filename='.$name;
        curl_setopt($ch, CURLOPT_URL,$target_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $upload_result=curl_exec ($ch);
        curl_close ($ch);
        if (FALSE === $upload_result){
        $rt['message'] = 'File not uploaded successfully.';
        $image_name='';
        }
        else{
        $image_name=$directory.'/'.$name; 
        }
      }
      else
      {
        $directory_path           = 'system/upload/seller_invoice/'.$suborder_id;
        $directory_complete_path  = DIR_SYSTEM . 'upload/seller_invoice/'.$suborder_id;
        $ext                      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name                     =  $invoice_id.'.'.$ext;
        $file_name_with_full_path = $file['tmp_name'];
        // Check its a directory

        if (!is_dir($directory_complete_path)) {
          @mkdir($directory_complete_path, 0777);
         }
            if(move_uploaded_file($file_name_with_full_path, $directory_complete_path . '/' . $name)){
              $image_name=$directory_path.'/'.$name; 
            }else{
              $image_name=''; 
            }
      }

        $sql = "UPDATE  " . DB_PREFIX . "seller_invoice SET invoice_image = '".$image_name."' where  suborder_id = '".$suborder_id."' and seller_invoice_id = '".$invoice_id."'";
              
        $query = $this->db->query($sql);
        return $image_name;

  }



}