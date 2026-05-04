<?php class ModelAccountsSellerInvoice extends Model 
{
	/**
    * To get the invoices based on filter values.
    * 
    * @param: $data array data
    * @author: Original Ashish 
    * @author: Another MSA Oct 2019  
    */
	public function getInvoices($data = array()) 
    {
        $sql = "SELECT 
                    ANY_VALUE(CONCAT(oms.company,' ( ',oms.nickname,' )')) AS seller_name,
                    ANY_VALUE(oms.city) AS location,

                    osi.seller_invoice_id,
                    osi.seller_invoice_no,
                    osi.suborder_id,
                    osi.invoice_image,
				    date(osi.date_added) AS purchase_invoice_date,
                    ANY_VALUE(CONCAT(osi.seller_invoice_prefix,'',osi.seller_invoice_no)) AS purchase_invoice_no,
                    osi.invoice_physically_received AS status,
                    osi.invoice_received_comment AS comment,

				  	ANY_VALUE(oo.order_no) AS order_no,
                    ANY_VALUE(date(oo.date_added)) AS order_date,
				  	
				    SUM(round(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece, 2)
				    ) AS total_value, 
				    SUM(round((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) / (1 + oop.seller_input_tax/100), 2)) AS total_product_value,
                    SUM(round((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) - ((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) / (1 + oop.seller_input_tax/100)), 2)) as total_tax
				    
                FROM ".DB_PREFIX."seller_invoice osi
                
                INNER JOIN 
                  ".DB_PREFIX."order_product oop ON oop.seller_invoice_id = osi.seller_invoice_id
                INNER JOIN 
                  ".DB_PREFIX."ms_seller oms ON oms.seller_id = oop.seller_id 
				INNER JOIN 
				  ".DB_PREFIX."order oo ON oo.order_id = osi.order_id 
				 
                WHERE
				  1=1 ";

        $filter_seller_name = trim($data['filter_seller_name'] ?? '');
        if (!empty($filter_seller_name)) {
            $sql .= " AND ( oms.company LIKE '%" . $this->db->escape($filter_seller_name) . "%' 
                            OR oms.nickname LIKE '%" . $this->db->escape($filter_seller_name) . "%' )";
        }

        $filter_location = trim($data['filter_location'] ?? '');
        if (!empty($filter_location)) {
            $sql .= " AND oms.city LIKE '%" . $this->db->escape($filter_location) . "%'";
        }

        $filter_order_no = trim($data['filter_order_no'] ?? '');
        if (!empty($filter_order_no)) {
            $filter_order_no = trim(substr($filter_order_no, 0, 11));
            $order_no_len = strlen($filter_order_no);
            switch($order_no_len) {
                case 11: $sql .= " AND oo.order_no LIKE '" . $this->db->escape($filter_order_no) . "'"; break;
                case  5: $sql .= " AND oo.order_no_last_five_digits LIKE '" . $this->db->escape($filter_order_no) . "'"; break;
                default: $sql .= " AND oo.order_no LIKE '" . $this->db->escape($filter_order_no) . "%'"; break;
            }
        }

        if (!empty($data['filter_purchase_invoice_date_from']) && validateDate($data['filter_purchase_invoice_date_from'], 'Y-m-d')) {
            $sql .= " AND  osi.date_added >= '" . $this->db->escape($data['filter_purchase_invoice_date_from']) . " 00:00:00' ";
        }

        if (!empty($data['filter_purchase_invoice_date_to']) && validateDate($data['filter_purchase_invoice_date_to'], 'Y-m-d')) {
            $sql .= " AND osi.date_added <= '" . $this->db->escape($data['filter_purchase_invoice_date_to']) . " 23:59:59' ";
        }

        if (isset($data['filter_status']) && $data['filter_status'] != -1) 
        {
           if($data['filter_status'] == 0)
           {
             $sql .= " AND osi.invoice_physically_received = '0' AND  osi.invoice_image IS NULL ";
           }
           else if($data['filter_status'] == 1)
           {
             $sql .= " AND osi.invoice_physically_received = '0' AND  osi.invoice_image IS NOT NULL ";
           }
           else if($data['filter_status'] == 2)
           {
             $sql .= " AND osi.invoice_physically_received = '2' AND  osi.invoice_image IS NOT NULL ";
           }
           else
           {
             $sql .= " AND osi.invoice_physically_received = '1' AND  osi.invoice_image IS NOT NULL ";
           }
            
        }

        // Pagination related filter condition
        $p = $data['page'] ?? '';
        if ( $p !== 'FIRST' && (int)$p > 0 ) {
            $sql .= " AND osi.seller_invoice_id < " . (int)$p;
        }

        $sql .= " GROUP BY osi.seller_invoice_id ";

        $sql .= " ORDER BY osi.seller_invoice_id DESC";

        if (!$data['download_seller_invoice_report']) {
            if (isset($data['limit'])) {
                if ( (int)$data['limit'] < 1) {
                    $data['limit'] = 15;
                }

                $sql .= " LIMIT " . (int) $data['limit'];
                }
            }

        $query = $this->db->query($sql);
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return false;
        }
    }

    /**
    * Function to Update the Invoice status of invoice_physically_received and invoice_physically_received columns in oc_seller_invoice and TO log the details which user is updating the records.
    * 
    * @param $comment       string  popup will be shown and user can enter the comment as its not optional
    * @param $seller_inv_id integer seller_invoice_id
    * @param $status        integer status for the seller invoice id 
    * @author Ashish , Updated BY Nishu (For admin_change_log)
    */
    public function updateInvoiceStatus($comment, $seller_inv_id, $status) 
    {
    	// Start to update oc_ms_seller table for seller invoice received and comment in table
    	$sql = "UPDATE ".DB_PREFIX."seller_invoice 
    	           SET invoice_physically_received = " . (int)$status . ", 
    	               invoice_received_comment = '" . $this->db->escape($comment) . "' 
    	         WHERE seller_invoice_id = " . (int)$seller_inv_id;

    	$result = $this->db->query($sql);
        //End of the query to update seller invoice received and comment for the each record

    	// Start To log the details who is updating the Invoice records 
        $user_id = !empty((int)$this->user->getId()) ? (int)$this->user->getId() : 0;

    	if (method_exists($this->user, 'getUserName') ) {
        	$user_name = $this->user->getUserName($this->user->getId())['username'];
			$name      = $this->user->getUserName($this->user->getId())['name'];
			$user_type = $this->user->getGroupName();
		}

		$dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
		$class = isset($dbt[1]['class']) ? $dbt[1]['class'] : '';
		$function = isset($dbt[1]['function']) ? $dbt[1]['function'] : '';
		$ref_url = $class .'/'. $function;
		$url = !empty($ref_url) ? $ref_url : '';

        $table_name  = 'oc_seller_invoice';
        $sourcefield = 'update_seller_invoice';
        $fieldname   = 'invoice_physically_received_and_comment';
        $ip = $this->request->getIpAddress;
        $server = $_SERVER['HTTP_USER_AGENT'];

        //Set Data to add into admin_change_log
        $admin_change_data                  = array();
        $admin_change_data['table_id']      = (int)$seller_inv_id;
        $admin_change_data['user_id']       = (int)$user_id;
        $admin_change_data['name']          = $name;
        $admin_change_data['username']      = $user_name;
        $admin_change_data['table_name']    = $table_name;
        $admin_change_data['source_field']  = $sourcefield;
        $admin_change_data['field_name']    = $fieldname;
        $admin_change_data['ref_url']       = $url;
        $admin_change_data['old_value']     = 0;
        $admin_change_data['new_value']     = 1;
        $admin_change_data['comment']       = $comment;
        $admin_change_data['user_agent']    = $server;
        $admin_change_data['ip_address']    = $ip;
        $admin_change_data['file_location'] = $url;
        $admin_change_data['user_type']     = $user_type;

        //Call dynamic static function for entry into admin change log
        CommonLib::addAdminChangeLog($this->db, $admin_change_data);

    	// Return the result of updation query of oc_ms_seller table
        if ($result) {
    		return true;
    	}
        // End of return
    }

    /**
    * Method for update Seller Invoice No.
    * @param: $data : array of data with in seller invoice id and seller invoice no
    * @return : NULL
    * @author : Vikas, Apr 2018
    */
    public function updateSellerInvoiceNo($data) {
        $product_change_log = new ProductChangeLog($this->registry);
        
        if(empty($data)) {
            return false;
        }

        // get old value of seller invoice no
        $get_sql = "SELECT seller_invoice_id, 
                           seller_invoice_prefix, 
                           seller_invoice_no
                    FROM " . DB_PREFIX . "seller_invoice
                    WHERE seller_invoice_id = " . (int)$data['seller_invoice_id'];
        $get_query = $this->db->query($get_sql);            

        $sql = "UPDATE oc_seller_invoice 
                SET seller_invoice_no = '" . $this->db->escape($data['seller_invoice_no']) . "',
                    seller_invoice_prefix = ''
                WHERE seller_invoice_id = " . (int)$data['seller_invoice_id'];
        $query = $this->db->query($sql);

        $table_name         = 'oc_seller_invoice';
        $source_field       = 'seller_invoice_verification';
        $seller_invoice_id  = $data['seller_invoice_id'];
        $changes_data = array(
                                'seller_invoice_no' => array(
                                                                'old_value' => $get_query->row['seller_invoice_prefix'].''.$get_query->row['seller_invoice_no'],
                                                                'new_value' => $data['seller_invoice_no']
                                                            )
                            );

        $product_change_log->recordLogs($seller_invoice_id, $changes_data, $source_field, '', $table_name);

        if( $query ) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Method for update Seller Invoice No.
    * @param: $data : array of data with in seller invoice id and seller invoice no
    * @return : NULL
    * @author : Vikas, Apr 2018
    */
    public function approveSellerInvoice($data) {

        $product_change_log = new ProductChangeLog($this->registry);

        if(empty($data)) {
            return false;
        }

        // get old value of seller invoice no
        $get_sql = "SELECT seller_invoice_id, 
                           invoice_received_comment,
                           invoice_image,
                           invoice_physically_received
                    FROM " . DB_PREFIX . "seller_invoice
                    WHERE seller_invoice_id = " . (int)$data['seller_invoice_id'];
        $get_query = $this->db->query($get_sql);  

        if($get_query->row['invoice_image'] == '') { return false; }          

        $sql = "UPDATE oc_seller_invoice 
                SET invoice_physically_received = '" . 1 . "',
                    invoice_received_comment = '". $this->db->escape(trim($data['comment'] ?? '')) ."'
                WHERE seller_invoice_id = " . (int)$data['seller_invoice_id'] . " 
                  AND invoice_image != ''";

        $query = $this->db->query($sql);

        $table_name         = 'oc_seller_invoice';
        $source_field       = 'seller_invoice_verification';
        $seller_invoice_id  = $data['seller_invoice_id'];
        $changes_data = array(
                                'invoice_received_comment' => array(
                                                                'old_value' => $get_query->row['invoice_received_comment'],
                                                                'new_value' => $data['comment']
                                                            ),
                                'invoice_physically_received' => array(
                                                                'old_value' => $get_query->row['invoice_physically_received'],
                                                                'new_value' => 1
                                                            )
                            );

        $product_change_log->recordLogs($seller_invoice_id, $changes_data, $source_field, '', $table_name);

        if( $query ) {
            return true;
        } else {
            return false;
        }
    }

    public function rejectSellerInvoice($data) {

        $product_change_log = new ProductChangeLog($this->registry);

        if(empty($data)) {
            return false;
        }
        // get old value of seller invoice no
        $get_sql = "SELECT seller_invoice_id, 
                           invoice_received_comment,
                           invoice_image,
                           invoice_physically_received
                    FROM " . DB_PREFIX . "seller_invoice
                    WHERE seller_invoice_id = " . (int)$data['seller_invoice_id'];
        $get_query = $this->db->query($get_sql);  
        

        $sql = "UPDATE oc_seller_invoice 
                SET invoice_physically_received = '" . 2 . "'
                WHERE seller_invoice_id = " . (int)$data['seller_invoice_id'] . "";
        $query = $this->db->query($sql);

        $table_name         = 'oc_seller_invoice';
        $source_field       = 'seller_invoice_verification';
        $seller_invoice_id  = $data['seller_invoice_id'];
        $changes_data = array(
                                'invoice_physically_received' => array(
                                                                'old_value' => $get_query->row['invoice_physically_received'],
                                                                'new_value' => 2
                                                            )
                            );

        $product_change_log->recordLogs($seller_invoice_id, $changes_data, $source_field, '', $table_name);

        if( $query ) {
            return true;
        } else {
            return false;
        }
    }

   public function SellerInvoiceComment($data) {

        $product_change_log = new ProductChangeLog($this->registry);

        if(empty($data)) {
            return false;
        }
        // get old value of seller invoice no
        $get_sql = "SELECT seller_invoice_id, 
                           invoice_received_comment
                    FROM " . DB_PREFIX . "seller_invoice
                    WHERE seller_invoice_id = " . (int)$data['seller_invoice_id'];
        $get_query = $this->db->query($get_sql);  
        

        $sql = "UPDATE oc_seller_invoice 
                SET invoice_received_comment = '" . $this->db->escape(trim($data['comment'] ?? '')) . "'
                WHERE seller_invoice_id = " . (int)$data['seller_invoice_id'] . "";
        $query = $this->db->query($sql);

        $table_name         = 'oc_seller_invoice';
        $source_field       = 'seller_invoice_verification';
        $seller_invoice_id  = $data['seller_invoice_id'];
        $changes_data = array(
                                'invoice_received_comment' => array(
                                                                'old_value' => $get_query->row['invoice_received_comment'],
                                                                'new_value' => $data['comment']
                                                            )
                            );

        $product_change_log->recordLogs($seller_invoice_id, $changes_data, $source_field, '', $table_name);

        if( $query ) {
            return true;
        } else {
            return false;
        }
    }

     public function uploadImgUsingCurl($file, $invoice_id, $suborder_id){
        
        $product_change_log = new ProductChangeLog($this->registry);

        $get_sql = "SELECT seller_invoice_id, 
                           invoice_image
                    FROM " . DB_PREFIX . "seller_invoice
                    WHERE seller_invoice_id = " . (int)$invoice_id;
        $get_query = $this->db->query($get_sql);

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

        $sql = "UPDATE  " . DB_PREFIX . "seller_invoice 
                SET invoice_image = '" . $this->db->escape($image_name) . "' 
                WHERE seller_invoice_id = " . (int)$invoice_id;
              
        $query = $this->db->query($sql);


        $table_name         = 'oc_seller_invoice';
        $source_field       = 'seller_invoice_verification';
        $seller_invoice_id  = $invoice_id;
        $changes_data = array(
                                'invoice_image' => array(
                                                                'old_value' => $get_query->row['invoice_image'],
                                                                'new_value' => $image_name
                                                            )
                            );

        $product_change_log->recordLogs($seller_invoice_id, $changes_data, $source_field, '', $table_name);

        return $image_name;

  }

}