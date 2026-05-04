<?php
class ModelRestapiReturn extends Model {

    public function getRefundParameters( int $order_product_id ) {

        if(isset($order_product_id) && !empty($order_product_id)){
            $sql = "
                    SELECT 
                      oop.price_per_piece,
                      oop.discount_per_piece,
                      oop.weight_per_piece,
                      oop.output_tax_rates,
                      oop.model,
                      oop.quantity,
                      oop.piece_in_set,
                      so.shipping_code
                    FROM 
                      ".DB_PREFIX."order_product AS oop
                    INNER JOIN 
                      ".DB_PREFIX."suborder AS so ON so.order_id = oop.order_id
                    WHERE 
                      oop.order_product_id = '" . (int)$order_product_id . "'
                      AND oop.suborder_id = so.suborder_id 
                    ";
            $result = $this->db->query($sql);

            if($result->num_rows){
                return $result;
            }
         }
     }

     public function getCustomerReturns($master_return_ids) {

       if(isset($master_return_ids) && !empty($master_return_ids)){
      
           $master_return_ids = implode(',',$master_return_ids);
      
           $sql = "SELECT ri.return_id,
                          ri.order_product_id,
                          ri.quantity,
                          ri.return_action_id,
                          ri.master_return_id,
                          op.order_id,
                          o.order_no
                          FROM ".DB_PREFIX."return ri
                          INNER JOIN ".DB_PREFIX."order_product op ON ri.order_product_id = op.order_product_id
                          INNER JOIN ".DB_PREFIX."order o ON (op.order_id = o.order_id)
                          WHERE ri.master_return_id IN(".$this->db->escape($master_return_ids).") AND ri.active_row = 1
                          GROUP BY ri.order_product_id
                          ORDER BY ri.date_added DESC
                    ";         
           $result = $this->db->query($sql);

           if($result->num_rows){
               return $result;
           }
       }
   }

  public function getShippingAddress( int $order_product_id ) {

      if(isset($order_product_id) && !empty($order_product_id)){
    
          $sql = "SELECT o.shipping_company,
                         o.shipping_address_1,
                         o.shipping_city,
                         o.shipping_postcode,
                         o.shipping_zone_id,
                         o.telephone
                         FROM ".DB_PREFIX."order_product oop
                         INNER JOIN ".DB_PREFIX."order o ON (oop.order_id = o.order_id)
                         WHERE oop.order_product_id = '" . (int)$order_product_id . "' 
                  ";
        $result = $this->db->query($sql);

        if($result->num_rows){
            return $result;
        }

      }
  }

   public function createReturnNo(){
    
    $sql  = "SELECT 
                max(master_return_id) as return_no 
             FROM 
                ".DB_PREFIX."master_return 
            ";
		
    $query = $this->db->query($sql);
		
    if($query->row['return_no']){
		
    	$return_no = $query->row['return_no'] + 1;
		
    }else{
		
    	$return_no = 1;
		}

		return $return_no;
   }

  public function addReturn($data) {

    if(!isset($data['return_action_id']) &&  empty($data['return_action_id'])){
        $data['return_action_id'] = RETURN_ACTION_IDS['Pending'];
    }

    if(!isset($data['internal_note']) &&  empty($data['internal_note'])){
        $data['internal_note'] = '';
    }

    if(!isset($data['shipping_method']) || empty($data['shipping_method'])){
        $data['shipping_method'] = 'not_decided';
    }

    if(!isset($data['crm_user_id']) &&  empty($data['crm_user_id'])){
        $data['crm_user_id'] = 0;
    }

		$sql  = "INSERT INTO `" . DB_PREFIX . "return` SET ";
		$sql .= "order_product_id = '" . (int)$data['order_product_id'] . "',";
		$sql .= "quantity = '" . (int)$data['quantity'] . "',";
		$sql .= "return_reason_id = '" . (int)$data['return_reason_id'] . "',";
		$sql .= "return_action_id = '" . (int)$data['return_action_id'] . "',";
		$sql .= "comment = '" . $this->db->escape($data['comment']) . "',";
		$sql .= "internal_note = '" . $this->db->escape($data['internal_note']) . "',";
    $sql .= "shipping_method = '" . $this->db->escape($data['shipping_method']) . "',";
		$sql .= " date_added = NOW(),";
		$sql .= " user = '', ";
    $sql .= " crm_user_id = ". (int)$data['crm_user_id'] .", ";
		$sql .=	" customer_id= '". $this->db->escape($this->customer->getId()) ."',";
		$sql .= " master_return_id = '" . (int)$data['master_return_id'] . "',";
    $sql .= " active_row = 1 ";
    
      if($this->db->query($sql)){
        if($data['return_reason_id'] == RETURN_REASON_IDS['Manufacturing_Defect']){ 
            $image['return_id'] = $this->db->getLastId();
            $image['order_product_id'] = $data['order_product_id'];
            $image['app_version_code'] = $data['app_version_code'];
            $upload_images = $this->uploadImages($image); 
            return $upload_images;
        }
      }
    }
    
    public function uploadImages($image_data){
        if(!empty($image_data)){
            $app_version_code = $image_data['app_version_code'];
            $order_product_id = $image_data['order_product_id'];
            $return_id = $image_data['return_id'];
            $i = 0;
            if(isset($app_version_code) && $app_version_code >= 68){
                if(!empty($_FILES['manufacturing']['name'][$order_product_id])){
                    foreach($_FILES['manufacturing']['name'][$order_product_id] as $key => $values){
                        $directory = 'return/'.$order_product_id;
                        $dt = new DateTime();
                        $ext = pathinfo($values, PATHINFO_EXTENSION);
                        // Sanitize the filename
                        $values   = $order_product_id."_".$i."_".$dt->format('Y-m-d').".".$ext;
                        
                        $filename = basename(html_entity_decode($values, ENT_QUOTES, 'UTF-8')); 
                        // Check to see if any PHP files are trying to be uploaded
                        $file_name_with_full_path = $_FILES['manufacturing']['tmp_name'][$order_product_id][$key];
            
                        // Return any upload error
                        if ($_FILES['manufacturing']['error'][$order_product_id][$key] != UPLOAD_ERR_OK) {
                            continue;
                        }
                        
                        if(is_uploaded_file($_FILES['manufacturing']['tmp_name'][$order_product_id][$key])){
                            if (function_exists('curl_file_create')) { // php 5.5+
                                $cFile = curl_file_create($file_name_with_full_path);
                            } else { // 
                                $cFile = '@' . realpath($file_name_with_full_path);
                            }
                        }

                        $post = array('file'=> $cFile);
                        $ch = curl_init();
                        $target_url = 'https://cdnimages.net/fileupload.php?directory='.$directory.'&filename='.$filename;
                        curl_setopt($ch, CURLOPT_URL,$target_url);
                        curl_setopt($ch, CURLOPT_POST,1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $result = curl_exec($ch);              
                        curl_close($ch);
                        $images_path[$order_product_id][$key] = "https://cdnimages.net/".$directory."/".$filename.'?'.time();
                        $this->saveImages($values,$return_id);
                        $i++;
                    }
                    return $images_path;
                }
            }
        }
    }

    /**
    * Public method to upload courier slipping slip on cdn server 
    * @param: string courier shipping slip
    * @return: void
    * @author: MSA Nov 2018
    */
    public function uploadReturnCourierSlippingSlip(string $tracking_no, string $shipping_slip)
    {
       if(!empty($_FILES['shipment_slip']['name']))
       {
          $directory = 'return/courier_slip/'.$tracking_no.'/';
          // Sanitize the filename
          $filename = basename(html_entity_decode($shipping_slip, ENT_QUOTES, 'UTF-8')); 
          // Check to see if any PHP files are trying to be uploaded
          $file_name_with_full_path = $_FILES['shipment_slip']['tmp_name'];
          // Return any upload error
          if ($_FILES['shipment_slip']['error'] != UPLOAD_ERR_OK) {
              return false;
          }
          if(is_uploaded_file($_FILES['shipment_slip']['tmp_name'])){
            $cFile = '';
            if (function_exists('curl_file_create')) { // php 5.5+
                $cFile = curl_file_create($file_name_with_full_path);
            } else { // 
                $cFile = '@' . realpath($file_name_with_full_path);
            }
            $post = array('file'=> $cFile);
            $ch = curl_init();
            $target_url = STATIC_CONTENT_URL_SSL . 'fileupload.php?directory='.$directory.'&filename='.$filename;
            curl_setopt($ch, CURLOPT_URL,$target_url);
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);              
            curl_close($ch);
          }

          return true;
      }
    }


    public function saveImages($values, $return_id) {

        if(!empty($values) && !empty($return_id)){

            $sql = "INSERT INTO ".DB_PREFIX."return_defect_images 
                    SET
                      image       = '" . $this->db->escape($values) . "',
                      return_id   = '" . (int)$return_id . "',
                      date_addded =  NOW()
                  ";
            $this->db->query($sql);
        }
    }

    public function findReturns( int $user_id ) {

        if(isset($user_id) && !empty($user_id)){
      
            $sql = "SELECT 
                      COUNT(return_id) As count 
                    FROM 
                      ".DB_PREFIX."return
                    WHERE 
                      customer_id = '" . (int)$user_id . "'
                      AND 
                      active_row = 1 ";

            $result = $this->db->query($sql);

            return $result;
        }
    }

    public function getMasterReturns( int $customer_id, int $order_id ) {       
      
      $sql = "SELECT omr.master_return_id, omr.return_shipment_tracking_id, omr.return_no,omr.date_added, omr.shipping_method ";

      if (!empty($order_id)) {
         $sql .= " ,o.order_no "; 
      }

      $sql .= " ,rst.courier_company, rst.tracking_no ";
      
      $sql .= " FROM ".DB_PREFIX."master_return omr ";

      if(!empty($order_id)){
          $sql .= "INNER JOIN ".DB_PREFIX."order o ON (omr.order_id = o.order_id)";
       }

        $sql .= "LEFT JOIN ".DB_PREFIX."return_shipment_tracking rst ON (omr.return_shipment_tracking_id = rst.shipping_id)";
      
      $sql .= " WHERE 1=1";

      if(!empty($customer_id)){
          
          $sql .= " AND omr.customer_id = '".(int)$customer_id."' ";
      }

      $sql .= " AND cancel_return = 0";

      if(!empty($order_id)){
          $sql  .= " AND o.order_id = '".(int)$order_id."' ";        
      }

      $sql  .= " ORDER BY master_return_id DESC ";

      $result = $this->db->query($sql);

      if($result) {
          return $result;
      }            
    }

    public function checkShipmentId($master_return_ids){

      if(isset($master_return_ids) && !empty($master_return_ids)){
        $master_return_ids = array_unique(array_filter(array_map('intval', explode(',', $master_return_ids)), function($v) {return $v > 0;}));

        // Now, run this query only when we have non-empty $master_return_ids array
        if ( empty($master_return_ids) ) return array();

          $sql = "SELECT 
                    master_return_id,
                    return_shipment_tracking_id As tracking_id 
                  FROM 
                    ".DB_PREFIX."master_return 
                  WHERE 
                    master_return_id IN (". implode(',', $master_return_ids) .") 
                    AND return_shipment_tracking_id IS NULL
                ";
      
          $result = $this->db->query($sql);
      
          if($result->num_rows){
              return $result->rows;
          }
      }
    }

    public function uploadCourierDetails($data) {
      $status = false;
        if(isset($data) && !empty($data)){

            if(is_array($data['master_return_ids'])) {
          
              $master_return_ids = implode(',',$data['master_return_ids']);  
          
            }else{
          
              $master_return_ids = $data['master_return_ids'];  
            }

            // Need to sanitize the input string, which is supposed to be a comma separated string of "int" product_id(s)
            // So, we explode back to array. array_map to convert them to integers (to prevent SQL injection)
            // Afterwards, we array_filter it out to remove invalid values. Then array_unique to remove duplicates
            $master_return_ids = array_unique(array_filter(array_map('intval', explode(',', $master_return_ids)), function($v) {return $v > 0;}));

            // Now, run this query only when we have non-empty $master_return_ids array
            if ( empty($master_return_ids) ) {return false;}
          
            $sql  = "INSERT INTO `" . DB_PREFIX . "return_shipment_tracking` SET ";
            $sql .= "master_return_ids = '".$this->db->escape(implode(',',$master_return_ids))."',";
            $sql .= "courier_company   = '".$this->db->escape($data['shipment_company'])."',";
            $sql .= "tracking_no       = '".$this->db->escape($data['tracking_no'])."',";
            $sql .= "order_id          = '".(int)$data['order_id']."',";
            $sql .= "warehouse_id      = '1',";
            $sql .= "date_added        = Now()";
            
            if($this->db->query($sql)){
                $last_insert_id = $this->db->getLastId();
                $sql   =  "UPDATE " . DB_PREFIX . "return
                          SET 
                            return_shipment_tracking_id = ". (int)$last_insert_id ."
                          WHERE 
                            master_return_id IN(". implode(',',$master_return_ids) .") 
                            AND active_row = 1 ";
                $this->db->query($sql);

                $sql   =  "UPDATE " . DB_PREFIX . "master_return
                                    SET return_shipment_tracking_id = ". (int)$last_insert_id ."
                                    WHERE master_return_id IN(". implode(',',$master_return_ids) .")";
                $this->db->query($sql);

                /*upload courier slipping slip on CDN Server*/
                if(!empty($data['shipping_slip'])) {
                  $isSlippingSlipUploaded = $this->uploadReturnCourierSlippingSlip( $data['tracking_no'], $data['shipping_slip'] );
                  if($isSlippingSlipUploaded) {
                    $sql   =  "UPDATE 
                                " . DB_PREFIX . "return_shipment_tracking
                              SET 
                                shipping_slip = '".$this->db->escape($data['shipping_slip'])."'
                              WHERE 
                                shipping_id = '".(int)$last_insert_id."'
                              ";
                    $this->db->query($sql);
                  }
                }

              $status = true;
            }
        }
        return $status;
    }

    public function getOrderId( int $master_return_id ) {
        
        if(!empty($master_return_id)){
        
            $sql = "SELECT order_id FROM ".DB_PREFIX."master_return WHERE master_return_id = '".(int)$master_return_id."' ";
        
            $result = $this->db->query($sql);
        
            if($result->num_rows){
        
                $return = $result->row;
        
            }else{
        
                $return = '';
            }
            return $return;
        }   
    }

    public function getShipmentMethod( int $master_return_id ) {

        if($master_return_id){
            
            $sql = "SELECT shipping_method,order_id FROM " .DB_PREFIX."master_return WHERE master_return_id = '" . (int)$master_return_id . "' ";
            
            $result = $this->db->query($sql);
            
            if($result->num_rows){
            
                return $result->row;
            
            }else{
            
                return 0;
            }
        }
    }

    public function getReturns( int $master_return_id ){

        if(!empty($master_return_id)){
      
            $sql = "SELECT 
                      return_id,
                      order_product_id,
                      quantity AS return_quantity,
                      return_reason_id,
                      comment,
                      return_action_id 
                    FROM 
                      ".DB_PREFIX."return 
                    WHERE 
                       master_return_id = '" . (int)$master_return_id . "'
                       AND 
                       active_row = 1
                    GROUP BY 
                      order_product_id
                  ";
            $result = $this->db->query($sql);

            if($result->num_rows){
            
                return $result->rows;
            
            }else{
            
                return 0;
            }
        }
    }

    

    public function findProductsDetails($order_product_ids) {

        if(!empty($order_product_ids)){
      
            $order_product_ids = implode(",", $order_product_ids);
      
            $sql = "SELECT
                      oop.order_product_id,
                      oop.name,
                      oop.product_id,
                      oop.model
                    FROM 
                      ".DB_PREFIX."order_product oop
                    WHERE 
                      oop.order_product_id IN(".$this->db->escape($order_product_ids).")
                    ";
      
             $result = $this->db->query($sql);      
      
             if($result->num_rows){
      
                 return $result->rows;
      
             }else{
      
                 return 0;
             }
        }
    }

  public function writeLog($data) {

		$fp = fopen(DIR_LOGS.'log_return.txt', 'a');
		
    fwrite($fp, json_encode($data));
		
    fclose($fp);
	}
	
	/** getShipTrackingURL
	 *  get shipping tracking url 
	 *  @param : $courier_partner
	 *  @return: tracking url
	 *  @author: Manish 
	 */ 	 
	public function getShipTrackingURL( string $courier_partner ) {
		
    $url = "";
		
    $sql = "SELECT 
                tracking_url 
            FROM 
                ".DB_PREFIX."courier_partners 
            WHERE 
                courier_name = '" . $this->db->escape($courier_partner) . "'
          ";
		
    $query = $this->db->query($sql);
		
    if ($query->num_rows) {
		
      foreach ($query->row as $field => $value) {
		    	$url = $value;
			}

		}

		return $url;
  }

 /** resetReturnActionStatus
  *  reset return action status 
  *  @param : int $return_id
  *  @return: void
  *  @author: MSA 
  */ 
  public function resetReturnActiveStatus( int $return_id, int $active_row )
  {

    if($return_id) {

      $sql = "UPDATE 
                ".DB_PREFIX."return 
              SET 
                active_row = '" . (int)$active_row . "' 
              WHERE 
                return_id = '" . (int)$return_id . "' 
              ";
      $this->db->query($sql);

    }

  }

  /**
   * @info: Public Function to get Latest Return Id
   * @param: $data Array
   * @return $return_id
   * @Author:  Nishu, May 2018
  */
  public function getAddedReturnId($data) {
    
    $return_id = 0;
    
    $sql = "
            SELECT 
               return_id
            FROM
               ".DB_PREFIX."return 
            WHERE 
              order_product_id = '".(int)$data['order_product_id']."'
              AND 
              master_return_id = '". (int)$data['master_return_id'] ."'
              AND 
              active_row = 1
            ORDER BY 
              return_id DESC
            LIMIT 
              0, 1
           ";
    $result = $this->db->query($sql);
   
    if($result->num_rows > 0){
   
      $return_id = $result->row['return_id'];
   
    }
   
    return $return_id;
  }

}