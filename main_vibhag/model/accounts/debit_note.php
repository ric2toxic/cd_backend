<?php class ModelAccountsDebitNote extends Model 
{
	/**
    * To get total debit notes where debit_note_status is 1.
    * 
    * @param $data array data
    * @author Ashish  
    */
    public function getTotalDebitNotes($data=array()) 
    {	
		$sql = "SELECT count(DISTINCT osdn.debit_note_id) as total 
				FROM ".DB_PREFIX."seller_debit_note osdn 
				INNER JOIN 
				  ".DB_PREFIX."order oo ON oo.order_id = osdn.order_id 
				INNER JOIN 
				  ".DB_PREFIX."ms_seller oms ON oms.seller_id = osdn.seller_id 
                INNER JOIN 
                  ".DB_PREFIX."return ort on ort.debit_note_id = osdn.debit_note_id
                INNER JOIN 
                  ".DB_PREFIX."order_product oop on oop.order_product_id = ort.order_product_id 
				WHERE 
				  debit_note_status = 1 
                  AND ort.debit_note_id != 0  ";

        if (!empty($data['filter_seller_name'])) {
            $sql .= " AND ( oms.company LIKE '%" . $data['filter_seller_name'] . "%' OR oms.nickname LIKE '%" . $data['filter_seller_name'] . "%' ) ";
        }

        if (!empty($data['filter_location'])) {
            $sql .= " AND oms.city LIKE '%" . $this->db->escape($data['filter_location']) . "%'";
        }

        if (!empty($data['filter_order_no'])) {
            $sql .= " AND oo.order_no LIKE '%" . $this->db->escape($data['filter_order_no']) . "%'";
        }

        if (!empty($data['filter_debit_note_date_from']) && !empty($data['filter_debit_note_date_to'])) {
            $sql .= " AND DATE(osdn.date_added) BETWEEN DATE('" . $this->db->escape($data['filter_debit_note_date_from']) . "') and DATE('" . $this->db->escape($data['filter_debit_note_date_to']) . "')";
        } else {
            if (!empty($data['filter_debit_note_date_from'])) {
                $sql .= " AND DATE(osdn.date_added) = DATE('" . $this->db->escape($data['filter_debit_note_date_from']) . "')";
            }

            if (!empty($data['filter_debit_note_date_to'])) {
                $sql .= " AND DATE(osdn.date_added) = DATE('" . $this->db->escape($data['filter_debit_note_date_to']) . "')";
            }
        }

        if (isset($data['filter_status']) && $data['filter_status'] != -1) {
            $sql .= " AND osdn.debit_note_physically_received = '" . $this->db->escape($data['filter_status']) . "' ";
        }

		$result = $this->db->query($sql);

		return $result->row['total'];

	}


	/**
    * To get the invoices based on filter values.
    * 
    * @param $data array data
    * @author Ashish 
    */
	public function getDebitNotes($data = array()) 
    {
        $sql = "SELECT 
                    CONCAT(oms.company,' ( ',oms.nickname,' )') AS seller_name, 
				    osdn.debit_note_id, 
				    oms.city AS location, 
				  	oo.order_no AS order_no, 
				  	date(oo.date_added) AS order_date, 
				  	date(osdn.date_added) AS debit_note_date, 
				  	CONCAT(osdn.debit_note_prefix,'',osdn.debit_note_no) AS debit_note_no,
				    osdn.debit_note_physically_received AS status, 
                    SUM(ort.quantity * oop.transfer_price_per_piece) AS total_value, 
                    SUM(ort.quantity * ROUND(oop.transfer_price_per_piece/(1 + oop.seller_input_tax/100),2)) as product_value, 
                    SUM(ort.quantity * 
                        ROUND(oop.transfer_price_per_piece - ROUND(oop.transfer_price_per_piece/
                                                              (1 + oop.seller_input_tax/100),2),2)
                       ) AS tax,
                    debit_note_received_comment AS comment

                FROM ".DB_PREFIX."seller_debit_note osdn 
				INNER JOIN 
				  ".DB_PREFIX."order oo ON oo.order_id = osdn.order_id 
				INNER JOIN 
				  ".DB_PREFIX."ms_seller oms ON oms.seller_id = osdn.seller_id 
                INNER JOIN 
                  ".DB_PREFIX."return ort on ort.debit_note_id = osdn.debit_note_id 
                INNER JOIN 
                  ".DB_PREFIX."order_product oop on oop.order_product_id = ort.order_product_id 
				WHERE 
				  debit_note_status = 1 
                  AND ort.debit_note_id != 0 ";

        if (!empty($data['filter_seller_name'])) {
            $sql .= " AND ( oms.company LIKE '%" . $data['filter_seller_name'] . "%' OR oms.nickname LIKE '%" . $data['filter_seller_name'] . "%' ) ";
        }

        if (!empty($data['filter_location'])) {
            $sql .= " AND oms.city LIKE '%" . $this->db->escape($data['filter_location']) . "%'";
        }

        if (!empty($data['filter_order_no'])) {
            $sql .= " AND oo.order_no LIKE '%" . $this->db->escape($data['filter_order_no']) . "%'";
        }

        if (!empty($data['filter_debit_note_date_from']) && !empty($data['filter_debit_note_date_to'])) {
            $sql .= " AND DATE(osdn.date_added) BETWEEN DATE('" . $this->db->escape($data['filter_debit_note_date_from']) . "') and DATE('" . $this->db->escape($data['filter_debit_note_date_to']) . "')";
        } else {
            if (!empty($data['filter_debit_note_date_from'])) {
                $sql .= " AND DATE(osdn.date_added) = DATE('" . $this->db->escape($data['filter_debit_note_date_from']) . "')";
            }

            if (!empty($data['filter_debit_note_date_to'])) {
                $sql .= " AND DATE(osdn.date_added) = DATE('" . $this->db->escape($data['filter_debit_note_date_to']) . "')";
            }
        }

        if (isset($data['filter_status']) && $data['filter_status'] != -1) {
            $sql .= " AND osdn.debit_note_physically_received = '" . $this->db->escape($data['filter_status']) . "' ";
        }

        $sql .= " GROUP BY osdn.debit_note_id ";

        if(!$data['download_debit_note_report']) {
            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
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
    * Function to Update the Invoice status of debit_note_physically_received and debit_note_physically_received columns in oc_seller_invoice and TO log the details which user is updating the records.
    * 
    * @param $comment       string  popup will be shown and user can enter the comment as its not optional
    * @param $debit_note_id integer debit_note_id
    * @param $status        integer status for the seller invoice id 
    * @author Ashish, Updated BY Nishu (For admin_change_log)
    */
    public function updateDebitNoteStatusAndComment($comment, $debit_note_id, $status) 
    {
    	// Start to update oc_seller_debit_note table for seller invoice received and comment in table
    	$sql = "UPDATE ".DB_PREFIX."seller_debit_note 
    	       SET debit_note_physically_received = '" . (int)$status . "', 
    	       debit_note_received_comment = '" . $this->db->escape($comment) . "' 
    	       WHERE debit_note_id = '" . (int)$debit_note_id . "' 
    	       LIMIT 1 ";

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

        $table_name  = 'oc_seller_debit_note';
        $sourcefield = 'update_debit_note';
        $fieldname   = 'debit_note_received_and_comment';
        $ip = $this->request->getIpAddress;
        $server = $_SERVER['HTTP_USER_AGENT'];

        //Set Data to add into admin_change_log
        $admin_change_data                  = array();
        $admin_change_data['table_id']      = $debit_note_id;
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

    	/*$log_sql = "INSERT INTO ".DB_PREFIX."admin_change_log 
    	           SET 
    	           table_id = '" . (int)$debit_note_id . "', 
    	           user_id  = '" . (int)$user_id . "', 
    	           name     = '" . $this->db->escape($name) . "', 
    	           username = '" . $this->db->escape($user_name) . "', 
    	           table_name = '" . $this->db->escape($table_name) . "', 
    	           source_field = '" . $this->db->escape($sourcefield) . "', 
    	           field_name   = '" . $this->db->escape($fieldname) . "', 
    	           ref_url      = '" . $this->db->escape($url) . "', 
    	           old_value    = '" . 0 . "', 
    	           new_value    = '" . 1 . "', 
    	           comment      = '" . $this->db->escape($comment) . "', 
    	           date_added   = NOW(), 
    	           user_agent   = '" . $this->db->escape($server) . "', 
    	           ip_address   = '" . $this->db->escape($ip) . "', 
    	           file_location = '" . $this->db->escape($url) . "', 
    	           user_type     = '" . $this->db->escape($user_type) . "' ";

    	$this->db->query($log_sql);
        //End of the log query*/

    	// Return the result of updation query of oc_ms_seller table
        if ($result) {
    		return true;
    	}
        // End of return
    }
}