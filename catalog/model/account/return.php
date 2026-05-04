<?php
class ModelAccountReturn extends Model {
	/*public function addReturn($data) {
		$this->event->trigger('pre.return.add', $data);

		$this->db->query("INSERT INTO `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', customer_id = '" . (int)$this->customer->getId() . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_status_id = '" . (int)$this->config->get('config_return_status_id') . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()");

		$return_id = $this->db->getLastId();

		$this->event->trigger('post.return.add', $return_id);

		return $return_id;
	}*/


	private $_limit_in_days_for_replacement = 14;

	public function getReturn($return_id) {
		return ;
	}

	/*
	* Did not found table - return_status [ LEFT JOIN table ] in Database, thus commented this queries
	* Author: MSA June 2018 
	*/
	public function getReturns($start = 0, $limit = 20) {
		/*if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 20;
		}
		$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, rs.name as status, r.date_added FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.customer_id = '" . $this->customer->getId() . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.return_id DESC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
		*/
	}

	public function getTotalReturns() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`WHERE customer_id = '" . (int)($this->customer->getId()) . "'");

		return $query->row['total'];
	}

	/*
	* Did not found table - return_history & return_status in Database, thus commented this queries
	* Author: MSA June 2018 
	*/
	public function getReturnHistories($return_id) {
		/*
		$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rh.notify = '1' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added ASC");
		return $query->rows;
		*/
	}

	public function getOrder($order_id, $suborder_id='', $selector=array()) {

		$order_data = array();

		if ( !empty($selector) )
			$this->_order_tables = $selector;

		// We get all the data as requested from all the order tables
		foreach ($this->_order_tables as $table) {

			$sql = "SELECT * FROM " . DB_PREFIX . $table[0] .
				" WHERE order_id = '" . (int)$order_id . "'";

			if ( $table[1] and $suborder_id ) { // table has suborder_id and suborder_id is provided
				$sql .= " AND suborder_id = '" . $this->db->escape($suborder_id) . "'";
			}

			// adding sort rules
			if ( !empty($this->_sort_rules[$table[0]]) ) {

				$sql .= " ORDER BY ";
				$sql .= implode(", ",
					array_map(
						function($key, $value) {
							return $key . " " . $value;
						},
						array_keys($this->_sort_rules[$table[0]]),
						$this->_sort_rules[$table[0]]
					)
				);
			}

			$query = $this->db->query($sql);

			if ( $query->num_rows > 0 ) {

				$order_data[$table[0]] = $query->rows;
			}  elseif ( $table[2] ) { // table is mandatory to get order_data

				return false;
			}
		}

		// Now we get some more information from different tables as needed
		// Also, some custom values are created as well required by various controllers
		if ( empty($selector) ) {

			// Creating customer_name
			$customer_name = '';
			$customer_name .= !empty($order_data['order'][0]['firstname']) ? $order_data['order'][0]['firstname'] : '';
			$customer_name .= ' ';
			$customer_name .= !empty($order_data['order'][0]['lastname']) ? $order_data['order'][0]['lastname'] : '';
			$order_data['order'][0]['customer_name'] = $customer_name;


			// Getting all tracking URL(s) for the suborders
			foreach ( $order_data['suborder'] as $key => $suborder ) {
				if(!empty($suborder['courier_partner'])){
					$sql = "SELECT tracking_url FROM " . DB_PREFIX . "courier_partners
    	        	        WHERE courier_name LIKE '" . $this->db->escape($suborder['courier_partner'])."'";
					$query = $this->db->query($sql);
					$order_data['suborder'][$key]['tracking_url'] = $query->num_rows ? $query->row['tracking_url'] : '';
				}else{
					$order_data['suborder'][$key]['tracking_url'] = '';
				}
			}

			// Unserializing custom fields
			$order_data['order'][0]['custom_field'] = unserialize($order_data['order'][0]['custom_field']);
			$order_data['order'][0]['payment_custom_field'] = unserialize($order_data['order'][0]['payment_custom_field']);
			$order_data['order'][0]['shipping_custom_field'] = unserialize($order_data['order'][0]['shipping_custom_field']);


			// Getting sales staff
			$order_data['order'][0]['sales_staff'] = $this->getSalesPersonName($order_data['order'][0]['sales_staff_id']);
			//get seller list
			$order_data['order'][0]['sales_staff_list'] = $this->getSalesStaffList();


			// Extracting specific information out of order totals
			foreach ($order_data['order_total'] as $key => $total) {

				$total_code = strtolower(trim($total['code']));

				// extracting advance collected value.
				$order_data['order'][0]['advance_collected'] = '';
				$order_data['order'][0]['credit_collected'] = '';
				if ($total_code == 'advance') {
					$order_data['order'][0]['advance_collected'] = abs($total['value']);

				} elseif ($total_code == 'credit') {
					// extracting credit amount in the order.
					$order_data['order'][0]['credit_collected'] = -$total['value'];

				} elseif ($total_code == 'shipping') {
					// gettting shipping charges on the order
					$order_data['order'][0]['shipping_charge_collected'] = $total['value'];
				}
			}

			// count repeat Order
			$count = $this->repeatOrderTag($order_data['order'][0]['customer_id']);
			if($count > 0){
				$order_data['order'][0]['count_order'] = $count;
			}
		}

		return $order_data;
	}

	public function getHistoryTime($order_id, $status_id) {
        $sql  = "SELECT oh.date_added ";
        $sql .= "FROM " . DB_PREFIX . "order_history oh ";
        $sql .= "WHERE oh.order_id = '" . (int)$order_id . "' AND  ";
        $sql .=      "oh.order_status_id = '" . (int)$status_id . "' ";
        $sql .= "ORDER BY oh.date_added DESC ";
        $sql .= "LIMIT 0,1";
		$query = $this->db->query($sql);

		if ($query->row)
			return $query->row['date_added'];
		else
			return false;
	}

    public function getSuborderHistoryTime($order_id, $suborder_id , $status_id) {
    	    $sql = "SELECT oh.date_added FROM " . DB_PREFIX . "order_history oh
    						WHERE oh.order_id = '" . (int)$order_id . "' AND
    									oh.suborder_id = '" . $this->db->escape($suborder_id) . "' AND
    									oh.order_status_id = '" . (int)$status_id . "'
    						ORDER BY oh.date_added DESC LIMIT 0,1";
     		$query = $this->db->query($sql);

             if ($query->row)
     		    return $query->row['date_added'];
             else
                 return false;
    }

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT oop.*, op.image FROM " . DB_PREFIX . "order_product oop
		                            LEFT JOIN " . DB_PREFIX . "product op ON op.product_id = oop.product_id
		                           WHERE oop.order_id = '" . (int)$order_id . "' ORDER BY oop.order_product_id ASC");

		return $query->rows;
	}

	public function getReturnDetailsByProduct_ids($product_ids){
		if(is_array($product_ids)){
			$sql = "
					Select 
						ocr.*,
						omr.master_return_id,
						omr.cancel_return,
						omr.return_shipment_tracking_id 
					from 
						".DB_PREFIX."return ocr
					INNER JOIN 
						".DB_PREFIX."master_return omr ON( ocr.master_return_id = omr.master_return_id )
					where 
						order_product_id in (".implode(',',$product_ids).") 
						AND omr.cancel_return = 0 
					order by 
						order_product_id DESC, return_id DESC ";
			$result = $this->db->query($sql);
			if($result){
				return $result->rows;
			}
		}
	}

	//get return reason
	public function getReturnReason(){
		$query = $this->db->query("SELECT return_reason_id, name
				FROM ". DB_PREFIX ."return_reason
				WHERE language_id = ".$this->config->get('config_language_id')."
				ORDER BY return_reason_id ASC
				");

		return $query->rows;
	}

	//get return action
	public function getReturnAction(){
		$query = $this->db->query("SELECT return_action_id, name
				FROM ". DB_PREFIX ."return_action
				WHERE language_id = ".$this->config->get('config_language_id')."
				ORDER BY return_action_id ASC
				");

		return $query->rows;
	}

	public function addReturn($data) {
        if(!isset($data['return_action_id']) &&  empty($data['return_action_id'])){
        	return 0;
            $data['return_action_id'] = 0;
        }
        if(!isset($data['internal_note']) &&  empty($data['internal_note'])){
            $data['internal_note'] = '';
        }
        
		$sql  = "INSERT INTO " . DB_PREFIX . "return SET ";
		$sql .= 			 "order_product_id = '" . (int)$data['order_product_id'] . "',";
		$sql .= 			 "quantity = '" . (int)$data['quantity'] . "',";
		$sql .= 			 "return_reason_id = '" . (int)$data['return_reason_id'] . "',";
		$sql .= 			 "return_action_id = '" . (int)$data['return_action_id'] . "',";
		$sql .= 			 "shipping_method = '" . $this->db->escape($data['shipping_method']) . "',";
		$sql .= 			 "comment = '" . $this->db->escape($data['comment']) . "',";
		$sql .= 			 "internal_note = '" . $this->db->escape($data['internal_note']) . "',";
		$sql .= 			 "date_added = NOW(),";
		$sql .= 			 "user = '', ";
		$sql .=				"customer_id= '". $this->db->escape($this->customer->getId()) ."',";
		$sql .= 			 "master_return_id = '" . (int)$data['master_return_id'] . "'";
        $this->db->query($sql);
	}

	public function getDiscountAdjustmentFactor( $order_id , $totals = array() ){
		// if $otal is empty get the order total value from db
		if(empty($totals)){
			$totals = $this->db->query("Select code,value from oc_order_total where order_id = $order_id")->rows;

		}
		// Getting Subtotal of and order
		if(!empty($totals)){
			$subtotal = 0;
			foreach($totals as $key => $total){
				if( $total['code'] == 'sub_total' ){
					$subtotal = (float)$total['value'];
					break;
				}
			}

			$discount_total = 0;

			// Getting total discount of an order
			foreach($totals as $key => $total){
				if(in_array($total['code'],$this->_discount_types)){
					$discount_total += (float)$total['value'];
				}
			}
			$discount_factor = 0;
			// Getting Total percent factor
			if(!empty($subtotal)){
				$discount_factor = (float)$discount_total / (float)$subtotal;
				return $discount_factor;
			}
		}
	}

	public function notificationReturnMailandSMS($product_data = array()){

        $product_data["image_width"]     = $this->config->get('config_image_additional_width');
		$product_data["image_height"]    = $this->config->get('config_image_additional_height');
		$product_data["comment_message"] = "We have received your return/replacement request for the following items in your order : #";
		$template = MailTemplate::getReturnMailTemplate($product_data);
        if(!empty($product_data['old']) || !empty($product_data['updated'])){
            $subject = 'Return Request Updated';
        }
        else{
            $subject = 'Return Request';
        }
		$this->sendEmail($product_data['buyer_email'], $subject, $template);
        $this->load->language('account/sms_templates');
        $template = '';
        if(!empty($product_data['new']) && empty($product_data['updated']) && empty($product_data['old'])){
            $on_fresh_return_request = $this->language->get('on_fresh_return_request');
            $template = sprintf($on_fresh_return_request,$product_data['buyer_name'],$product_data['order_no'],$product_data['buyer_email']);
        }
        else if(!empty($product_data['updated'])){
            $on_update_return_request = $this->language->get('on_update_return_request');
            $template = sprintf($on_update_return_request,$product_data['buyer_name'],$product_data['order_no'],$product_data['buyer_email']);
        } elseif (!empty($product_data['new'])) {
            $on_update_return_request = $this->language->get('on_update_return_request');
            $template = sprintf($on_update_return_request,$product_data['buyer_name'],$product_data['order_no'],$product_data['buyer_email']);
        }
        if(!empty($template) && !empty($product_data['buyer_mobile'])){
            $this->sendSms($product_data['buyer_mobile'],$template);
        }
	}

    private function sendSms( $telephone , $message ){
        $sms = new SMS();
        $sms->setMessage($message);
        $sms->setNumber($telephone);
        if($sms->sendMessage()){
            return true;
        }
    }

    private function sendEmail($buyer,$subject,$message){

        // Preparing mail object
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->AddReplyTo($buyer['buyer_email'], $buyer['buyer_name']);
        $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
        $mail->addAddress($buyer['buyer_email'], $buyer['buyer_name']);
		$mail->Subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');

		if(isset($buyer['post_your_req']) && $buyer['post_your_req'] == 1){
           $mail->addCC(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['rohit']['sales']);
        } else{
           $mail->addCC(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        }

        $mail->msgHtml($message);
        if($mail->send(1, false)){
            return true;
        }
    }

    public function checkReturnsForOrder( $order_id ){
			$sql  = "SELECT ocr.return_id ";
			$sql .=    "FROM ".DB_PREFIX."order_product oop ";
			$sql .=    "INNER JOIN ".DB_PREFIX."return ocr on oop.order_product_id = ocr.order_product_id ";
			$sql .=    "WHERE oop.order_id = '".(int)$order_id."' AND ocr.active_row = 1 ";
			$sql .=    "GROUP BY ocr.order_product_id, ocr.master_return_id ";
			$results = $this->db->query($sql);
			if($results->num_rows  > 0 ){
					return $results->num_rows;
			}
			else{
					return false;
			}
	}

    public function getSellerInvoicesByOrderId( $order_id ){
			$sql  = "SELECT seller_id ";
			$sql .=    "FROM ".DB_PREFIX."seller_invoice ";
			$sql .=    "WHERE order_id = '".(int)$order_id."' ";
			$results = $this->db->query($sql);
			if( $results->num_rows > 0){
					return array_column($results->rows,'seller_id');
			}
			return false;
	}

    public function getInvoicesOrderProductIdBySellerId( $seller_id , $order_id ){
			if(!empty($seller_id)){
					$sql  = "SELECT oop.order_product_id ";
					$sql .=    "FROM ".DB_PREFIX."order_product oop ";
					$sql .=    "WHERE  oop.seller_id IN ('".implode("','",$seller_id)."') AND ";
					$sql .=          " oop.order_id = '".(int)$order_id."'";
					$results = $this->db->query($sql);
					if( $results->num_rows > 0 ){
							return array_column( $results->rows , 'order_product_id' );
					}
			}
			return false;

	}

    public function courierDetails($data){
        if(isset($data) && !empty($data)){
			$master_return_id = $this->checkshipment($data);
			$return = array();
			if(!empty($master_return_id)){
				
				//check if already uploaded tracking details for order no and master return id
				$check_sql = "SELECT shipping_id 
							  FROM `" . DB_PREFIX . "return_shipment_tracking` 
							  WHERE 
							  	master_return_ids = '" . $this->db->escape($master_return_id) . "'
							  	AND
							  	order_no = '" . $this->db->escape($data['order_no']) . "'
							  	AND
							  	is_cancel = 0
							  ORDER BY shipping_id DESC	
						  	";
				$check_result = $this->db->query($check_sql);
				if($check_result->num_rows) {
				//Update exiting tracking record	
					$shipping_id = $check_result->row['shipping_id'];
					$return["shipping_id"] = $shipping_id;
					$sql  = "UPDATE `" . DB_PREFIX . "return_shipment_tracking` SET ";
					$sql .= "courier_company	= '".$this->db->escape($data['courier_company'])."',";
					$sql .= "tracking_no		= '".$this->db->escape($data['tracking_no'])."',";
					$sql .= "shipping_slip		= '".$this->db->escape($data['shipping_slip'])."',";
					$sql .= "order_no			= '".$this->db->escape($data['order_no'])."' ";
					$sql .= " WHERE shipping_id = '".$this->db->escape($shipping_id)."' ";	
					$this->db->query($sql);
				}else{
				// Insert new tracking record	
					$sql  = "INSERT INTO `" . DB_PREFIX . "return_shipment_tracking` SET ";
					$sql .= "master_return_ids	= '".$this->db->escape($master_return_id)."',";
					$sql .= "courier_company	= '".$this->db->escape($data['courier_company'])."',";
					$sql .= "tracking_no		= '".$this->db->escape($data['tracking_no'])."',";
					$sql .= "shipping_slip		= '".$this->db->escape($data['shipping_slip'])."',";
					$sql .= "order_no			= '".$this->db->escape($data['order_no'])."',";
					$sql .= "warehouse_id		= '".$this->db->escape($data['warehouse_id'])."',";
					$sql .= "date_added			= Now()";

					if($this->db->query($sql)){
						$last_insert_id = $this->db->getLastId();
						$return["shipping_id"] = $last_insert_id;
						/*update oc_master_return table for courier tracking id*/
						$sql   =  "
								   UPDATE 
									" . DB_PREFIX . "master_return
								   SET 
								      return_shipment_tracking_id = ".(int)$last_insert_id ." 
								   WHERE 
								   	  master_return_id = ".(int)$master_return_id."
								";
						$this->db->query($sql);
						/*update oc_return table for courier tracking id*/
							$sql   =  "
										UPDATE 
											" . DB_PREFIX . "return
										SET 
											return_shipment_tracking_id = ". (int)$last_insert_id. " 
										WHERE 
											master_return_id = ". (int)$master_return_id."
											AND active_row = 1
										";
							$this->db->query($sql);
					}
				}
				$return["success"] = "success";
				return $return;
			}
        }
    }

	public function checkshipment($data){
		if(isset($data) && !empty($data)){
			$sql = "
					SELECT 
						master_return_id,return_shipment_tracking_id AS tracking_id 
					FROM 
						".DB_PREFIX."master_return
					WHERE 
						master_return_id IN (".$this->db->escape( $data['master_return_id']) . ")
					";		
			$result = $this->db->query($sql);
			if($result->num_rows){
				$master_return_id = $result->row['master_return_id'];
			}
			return $master_return_id;
		}
	}

    public function cancelReturn($master_return_id){
        if(!empty($master_return_id)){
        	$select_sql = "
        					SELECT * 
        					FROM ".DB_PREFIX."return
        					WHERE 
        					  master_return_id = ".(int)$master_return_id."
                      		  AND active_row = 1
        		          ";
            $results = $this->db->query($select_sql);
            if($results->num_rows > 0){
            	//Create object of ReturnActionBase class
    		    $return_action_base = new ReturnActionBase($this);
    		    
    		    $return_ids = array_column($results->rows, 'return_id');

            	foreach ($results->rows as $return) {
            		unset($return['return_id']);
            		$return['return_action_id'] = RETURN_ACTION_IDS['Cancelled_By_Customer'];
            		$return_action_base->insertReturn($return);	

            	}
            	$return_ids    = implode(',', $return_ids);
            	$active_status = 0; 
            	$return_action_base->resetReturnActiveStatusForReturnIds($active_status, $return_ids);
            }

            $sql = "UPDATE ".DB_PREFIX."master_return
						SET cancel_return = 1
						WHERE master_return_id = ".(int)$master_return_id."";
            $this->db->query($sql);
			return true;
        }
    }

	public function sendMailFromApi($buyer,$subject,$template){
		$this->sendEmail($buyer,$subject,$template);
	}

	public function addMasterReturn($master_return_data,$source){
		if(!empty($master_return_data)){
			$user_id         = $master_return_data['customer_id'];
			$shipping_method = $master_return_data['shipping_method'];
			$order_id        = $master_return_data['order_id'];
			$financial_year = '';
				if ( (int)(date('m')) <= 3 ) {
					$financial_year = date('Y', strtotime('-1 years')) . '-' . date('y');
				} else {
					$financial_year = date('Y') . '-' . date('y',strtotime('+1 years'));
				}

				$return_no_prefix = 'WSB-RTN-' . $financial_year . '_';

				$return_no = $this->createReturnNo();
				$return_no = $return_no_prefix.$return_no;
				$sql = "INSERT INTO ".DB_PREFIX."master_return SET
						return_no = '".$this->db->escape($return_no)."',
						customer_id= '".(int)$user_id."',
						shipping_method = '" . $this->db->escape($shipping_method) . "',
						order_id = ".(int)$order_id.",
						source = '".$this->db->escape($source)."',
						date_added = NOW()";
				if($this->db->query($sql)){
					$data['last_master_return_id'] =  $this->db->getLastId();
					$data['return_no'] = $return_no;
					return $data;
				}
		}
   }

   public function createReturnNo(){
        $sql  = "SELECT max(master_return_id) as return_no ";
		$sql .=        "FROM ".DB_PREFIX."master_return ";
		$query = $this->db->query($sql);
		if($query->row['return_no']){
			$return_no = $query->row['return_no'] + 1;
		}else{
			$return_no = 1;
		}

		return $return_no;
   }


	public function getLastMasterReturnId($master_return_id){
		$data = array();

		if(!empty($master_return_id)){

			$sql = "SELECT
						omr.master_return_id,
						omr.return_no,
						omr.return_shipment_tracking_id,
						omr.shipping_method
						FROM
						".DB_PREFIX."master_return omr ";

			$sql .= " WHERE omr.master_return_id =".(int)$master_return_id."";
			$sql .= " ORDER BY omr.master_return_id DESC ";
			$result = $this->db->query($sql);
			if($result->num_rows){
				$data = $result->row;
			}
		}

		return $data;
	}

	public function checkLastReturn($order_id, $op_id = 0){
		if(isset($order_id)){

			$restricted_action = array(
				                   RETURN_ACTION_IDS['Return_Request_Rejected']
				                 );
			$whr = '';
			if(!empty($op_id)){
				$whr = " AND ri.order_product_id != ".(int)$op_id. " ";
			}

			$sql = "SELECT
				omr.master_return_id,
				omr.return_no,
				omr.helpdesk_ticket_id,
				ri.return_action_id,
				omr.return_shipment_tracking_id,
				omr.shipping_method
				FROM ".DB_PREFIX."master_return omr
				INNER JOIN ".DB_PREFIX."return ri ON(ri.master_return_id = omr.master_return_id)
				WHERE 
                    omr.order_id = ".(int)$order_id." 
                    AND omr.cancel_return = 0 
                    AND ( 
                         omr.return_shipment_tracking_id IS NULL 
                          OR omr.return_shipment_tracking_id = 0 
                        )
                    AND ri.return_action_id NOT IN (". implode(',', $restricted_action) .") "
                    .$whr;

			$result = $this->db->query($sql);
			if($result->num_rows){
                return  $result->row;
			}
		}
	}

	public function getReturnsByOrderId($master_return_ids, $order_id, $flag = ''){ 
       
        if(is_array($master_return_ids) && !empty($master_return_ids)){
            $master_return_ids = implode(',',$master_return_ids);
            $sql = "SELECT ri.return_id,
                          ri.order_product_id,
                          ri.quantity,
                          ri.return_action_id,
                          ri.master_return_id,
                          ri.return_reason_id,
                          ri.debit_note_id ";

            if(empty($order_id)){
                $sql .= ",op.order_id,o.order_no ";
            }

            if ($flag == 'crmapi') {
                $sql .= ",ri.comment,
                          rpa.address, rpa.postcode, rpa.city, 
                          z.name as zone_name,
                          ra.name as product_return_status ";
            }

            $sql .= " FROM ".DB_PREFIX."return ri ";

            if(empty($order_id)){
                $sql .= " INNER JOIN ".DB_PREFIX."order_product op ON ri.order_product_id = op.order_product_id ";
                $sql .= " INNER JOIN ".DB_PREFIX."order o ON (op.order_id = o.order_id) ";
            }

            if ($flag=='crmapi') {
                
                $sql .= " LEFT JOIN ".DB_PREFIX."return_action ra ON (ri.return_action_id = ra.return_action_id)";

                $sql .= " LEFT JOIN ".DB_PREFIX."zone z ON (rpa.zone_id = z.zone_id)";
            }

            $sql .= " WHERE ri.master_return_id IN (".$master_return_ids.") AND ri.active_row = 1 ";
            
            $sql .= " ORDER BY ri.master_return_id DESC ";

           $result = $this->db->query($sql);
           if($result->num_rows){
               return $result;
           }
        }
    }

	public function transformReturn($result,$master_return_id = ''){
		if(!empty($result)){
			foreach($result as $key => $row){
				if($row['return_reason_id'] == RETURN_REASON_IDS['Quality_Issue']){		
					$row['return_reason_name'] = 'Quality Issue';
				}
				if($row['return_reason_id'] == RETURN_REASON_IDS['Pricing_Issue']){
					$row['return_reason_name'] = 'Pricing Issue';
				}
				if($row['return_reason_id'] == RETURN_REASON_IDS['Wrong_Item_Received']){
					$row['return_reason_name'] = 'Wrong Item Received';
				}
				if($row['return_reason_id'] == RETURN_REASON_IDS['Manufacturing_Defect']){
					$row['return_reason_name'] = 'Manufacturing Defect / Damaged Goods';
					if(
						$row['return_action_id'] == RETURN_ACTION_IDS['Old_Pending']
						        || 
						$row['return_action_id'] == RETURN_ACTION_IDS['Pending']
				    ){
						if(empty($master_return_id)){
							if(empty($check_quantity[$row['order_product_id']]['quantity'])){
								$check_quantity[$row['order_product_id']]['quantity'] = $row['quantity'];
							}
							$total_return_quantity = $this->totalQuantity($row['order_product_id']);
							if(!empty($total_return_quantity) && $total_return_quantity['remaining_quantity'] != $total_return_quantity['process_quantity']){
									$row['quantity'] = $total_return_quantity['remaining_quantity'];
									$row['total_quantity'] = $total_return_quantity['remaining_quantity'];
									$row['pervious_quantity'] = $total_return_quantity['process_quantity'];
									$returns['total_quantity'] = 'true';
							}else{
								if(isset($returns['total_quantity'])){
									unset($returns['total_quantity']);
								}
							}
						}
					}
				}
				if(!empty($master_return_id)){
					if($row['master_return_id'] == $master_return_id){
						if($row['return_reason_id'] == RETURN_REASON_IDS['Manufacturing_Defect']){
							if(
								$row['return_action_id'] == RETURN_ACTION_IDS['Old_Pending']
						            || 
								$row['return_action_id'] == RETURN_ACTION_IDS['Pending']
							){
								if(!isset($check_quantity[$row['order_product_id']])){
									$check_quantity[$row['order_product_id']]['quantity'] = "true";
								}
								if($check_quantity[$row['order_product_id']]['quantity'] == "true"){
									$returns[$row['order_product_id']] = $row;
									$check_quantity[$row['order_product_id']]['quantity'] = "false";
								}							
							}
						}else{
							$returns[$row['order_product_id']] = $row;
						}
						$result_tentative = $this->getQuantityMasterReturn($row['master_return_id'],$row['order_product_id'],'true');
						$return_tentative_data['product'][$row['order_product_id']]['quantity'] = $result_tentative['quantity'];
						$return_tentative_data['product'][$row['order_product_id']]['return_reason_id'] = $row['return_reason_id'];
						$return_tentative_data['product'][$row['order_product_id']]['order_product_id'] = $row['order_product_id'];
						$returns['return_tentative_data'] = $return_tentative_data;
					}
				}else{
					$returns[$row['order_product_id']] = $row;
				}
		   }
		   	return $returns;
		}
	}

	public function totalQuantity($order_product_id){
		if(!empty($order_product_id)){
			$sql = "SELECT quantity,piece_in_set FROM ".DB_PREFIX."order_product WHERE order_product_id =  ".(int)$order_product_id." ";
			$result = $this->db->query($sql);
			if($result->num_rows){
				$total_return_quantity = $this->findTotalReturnQuantity($order_product_id);
				$total_quantity = $result->row['quantity'] * $result->row['piece_in_set'];
				$remaining_quantity['remaining_quantity'] = $total_quantity - $total_return_quantity['completed'];
				if(isset($total_return_quantity['process'])){
					$remaining_quantity['process_quantity']   = $total_return_quantity['process'];
				}else{
					$remaining_quantity['process_quantity'] = '';
				}
			}else{
				$remaining_quantity = '';
			}
			return $remaining_quantity;
		}
	}
	public function findTotalReturnQuantity($order_product_id){
		if(!empty($order_product_id)){
			$sql = "SELECT master_return_id FROM ". DB_PREFIX ."return
					WHERE order_product_id = ".(int)$order_product_id." AND active_row = 1 
					GROUP BY master_return_id";
			$result = $this->db->query($sql);
			if($result->num_rows){
				if(!isset($total_quantity['completed'])){
					$total_quantity['completed'] = 0;
				}
				foreach($result->rows as $value){
					$return_quantity = $this->getQuantityMasterReturn($value['master_return_id'],$order_product_id,'');
					if(isset($return_quantity[$value['master_return_id']]['completed'])){
						$total_quantity['completed'] += $return_quantity[$value['master_return_id']]['completed'];
					}
					if(isset($return_quantity[$value['master_return_id']]['process'])){
						$total_quantity['process'] = $return_quantity[$value['master_return_id']]['process']; 
					}
				}
			}else{
				$total_quantity = 0;
			}
			return $total_quantity;		
		}
	}

	public function getQuantityMasterReturn($master_return_id,$order_product_id,$call){
		$estimated_return_actions = array(
									RETURN_ACTION_IDS['Old_Returned_Goods_Received'],
									RETURN_ACTION_IDS['Old_Replacement_Sent'],
									RETURN_ACTION_IDS['Return_Complete'],
									RETURN_ACTION_IDS['Shipment_Back_To_Customer'],
									RETURN_ACTION_IDS['Customer_Picked_Items'],
									RETURN_ACTION_IDS['Loss_Booked_By_WSB'],
									RETURN_ACTION_IDS['Goods_Taken_On_WSB_Books_And_Relisted'],
									RETURN_ACTION_IDS['ActionTransferToWSBBooksWithoutRelisting']
		                        	);

		if(!empty($master_return_id)){
			$sql = "SELECT quantity,return_action_id,order_product_id 
					FROM ".DB_PREFIX."return
				    WHERE master_return_id = ".(int)$master_return_id." 
					   AND order_product_id = ".(int)$order_product_id."
					   AND active_row = 1
				    ORDER BY date_added DESC LIMIT 1"; 
			$result = $this->db->query($sql);	   
			if($result->num_rows){
				if(empty($call)){
					if(
						in_array($result->row['return_action_id'], $estimated_return_actions)
					){
						$quantity_array[$master_return_id]['completed']   = $result->row['quantity'];
					}else{
						$quantity_array[$master_return_id]['process'] = $result->row['quantity'];
					}
				}else{
					return $result->row;	
				}
			}else{
				$quantity_array = 0;
			}
			return $quantity_array;
		}
	}

	public function checkReturnTimeLimit($delivered_date){
		if(!empty($delivered_date)){
			$date_today	     = time();
			$max_date_for_upload_courier = strtotime(
														"+$this->_limit_in_days_for_replacement days",
														strtotime($delivered_date)
													);
			$valid = $date_today <= $max_date_for_upload_courier;
			return $valid;
		}
	}

	//This function is not used (called), checked by Global search 
	//So Function commenting done by Nishu 16 June 2018
	public function getCerditNote($debit_note){
		// if(!empty($debit_note)){
		// 	$sql = "SELECT credit_note_id 
		// 	        FROM ".DB_PREFIX."return
		// 			WHERE 
		// 				debit_note_id = ".$debit_note."
		// 				AND active_row = 1 ";
		// 	$result = $this->db->query($sql);
		// 	if($result->num_rows){
		// 		return $result->num_rows;
		// 	}
		// }
	}

	public function findCreditNote($order_id,$customer_id){
			$sql = "
					SELECT cn.credit_note_id,
						cn.credit_note_prefix,
						cn.credit_note_no,
						cn.order_id,
						cn.date_added
					FROM ".DB_PREFIX."credit_note cn ";
			if(!empty($customer_id)){
				$sql .= " INNER JOIN ".DB_PREFIX."order o ON (o.order_id = cn.order_id) ";
			}

			if(!empty($customer_id)){
				$sql .= " WHERE o.customer_id = ".(int)$customer_id." AND credit_note_status = 1 ";
			}else{
				$sql .= " WHERE order_id = ".(int)$order_id." AND credit_note_status = 1 ";
			}
			$sql .= "ORDER BY cn.date_added DESC"; 
			$result = $this->db->query($sql);
			if($result->num_rows){
				$return = $result->rows;
			}else{
				$return = '';
			}
			return $return;
		}

		public function crediteNote($result){
			$encode_file = array();
			if(!empty($result)){
				$i = 0;
				foreach($result as $value){
					$encode_file['order_id'] 	   = $value['order_id'];
					$encode_file['credit_note_id'] = $value ['credit_note_id'];
					$serialize = base64_encode(serialize($encode_file));
					$credit_note[$i]['credit_note_link'] = $this->securefiledownload->getDownloadLink('buyer_credit_note',$serialize, false);
					$credit_note[$i]['credit_note_no']  = $value['credit_note_prefix'].$value['credit_note_no'];
					$credit_note[$i]['date_added'] = $value['date_added'];
					$i++;
				}	
			}else{
				$credit_note = '';
			}
			return $credit_note;
	}

	public function checkProductIsReturnable($seller_id){
		if(!empty($seller_id)){
			$sql ="SELECT non_returnable FROM ".DB_PREFIX."ms_seller WHERE seller_id = ".(int)$seller_id."";
			$result = $this->db->query($sql);
			if($result->num_rows){
				if($result->row['non_returnable']){
					$return = "non_returnable";
				}else{
					$return = "";
				}
			}
			return $return;
		}
	}

	public function getProductDetails($order_product_ids){
		if(!empty($order_product_ids)){
			$order_product_ids = implode(",",$order_product_ids);
			$sql = "SELECT product_id,model,order_product_id FROM ".DB_PREFIX."order_product
					WHERE order_product_id IN(".$order_product_ids.")";
			$result = $this->db->query($sql);
			return $result->rows;
		}
	}

	public function getAllSuborderDeliveryDate($suborders,$order_id){
		if(!empty($suborders)){
			$suborders =  implode( "','",$suborders);
			$sql = "SELECT date_added,suborder_id FROM ".DB_PREFIX."order_history
					WHERE order_id = ".(int)$order_id." AND suborder_id IN('".$suborders."') 
					AND order_status_id = ".(int)ORDER_STATUS['Delivered']."
					GROUP BY suborder_id ORDER BY date_added ASC";
		   $result = $this->db->query($sql);
		   if($result->num_rows){
			   return $result->rows;
		   }else{
			   return '';
		   }		
		}
	}

	/**
	 * @info : Public method to get total Qty for given OrderProductId
	 * @param: $op_id
	 * @return: $total_quantity
	 * @author: Nishu, July 2018
	*/
	public function getTotalQtyForOrderProduct($order_product_id){
		$total_quantity = 0;

		if(!empty($order_product_id)){
			$sql = "
					SELECT 
						(quantity * piece_in_set) AS total_quantity
					FROM
						". DB_PREFIX ."order_product
					WHERE
						order_product_id = ". (int)$order_product_id ."
				   ";
			$result = $this->db->query($sql);
			if($result->num_rows > 0){
				$total_quantity = $result->row['total_quantity'];
			}
		}
		return $total_quantity;
	}
}
