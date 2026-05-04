<?php
class ModelSaleReturn extends Model {

	private $_order_info = array();
	private $_cn_prefix  = '';
	private $_cn_no      = 0;
	private $_cn_id      = NULL;
	
   	public function updateMasterReturnWithShippingMethod($data){
   		$sql = "SELECT * 
   		         from ".DB_PREFIX."master_return 
   		         WHERE master_return_id = '". (int)$data['master_return_id'] . "'";
   		$result = $this->db->query($sql);
   		if($result->num_rows > 0){
   			if($result->row['shipping_method'] == $data['shipping_method']){
   				return;
   			}else{
   				$sql = "SELECT * from ".DB_PREFIX."return
   						 WHERE 
   						    master_return_id ='". (int)$data['master_return_id'] ."'
   						    AND active_row = 1
   					   ";
   				$returns = $this->db->query($sql);
   				if($returns->num_rows > 0){
   					foreach ($returns->rows as $return) {
   						if($return['shipping_method'] != $data['shipping_method']){
   							return;
   						}
   					}
   					$update_sql = "UPDATE ".DB_PREFIX."master_return 
   					                SET 
   					                 shipping_method = '". $this->db->escape($data['shipping_method']) ."'
   					                WHERE
   					                 master_return_id = '". (int)$data['master_return_id']."' ";
   					$this->db->query($update_sql);
   				}
   			}
   		}
   		return;
   	}

	public function getReturn($return_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id ) AS customer FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'");

		return $query->row;
	}

	public function getTotalReturnHistories($return_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_id = '" . (int)$return_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnHistoriesByReturnStatusId($return_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_status_id = '" . (int)$return_status_id . "' GROUP BY return_id");

		return $query->row['total'];
	}

	// product return by order id
	public function getOrderReturn($order_id){
		$sql = "SELECT op.order_id,
						op.order_no,
						op.product_id,
						op.order_product_id,
						op.name,
						op.model,
						op.sku,
						op.quantity,
						op.piece_in_set,
						op.total_pieces,
						op.price_per_piece,
						op.total,
						op.comment,
						p.image,
						r.quantity as return_quantity,
						r.return_reason_id,
						r.comment as return_comment,
						r.return_action_id,
						p.weight
				FROM ". DB_PREFIX ."order o
				LEFT JOIN ". DB_PREFIX ."order_product op
				  ON(op.order_id = o.order_id)
				LEFT JOIN ". DB_PREFIX ."product p
				  ON(p.product_id = op.product_id)
				LEFT JOIN ". DB_PREFIX ."return r
				  ON(r.order_product_id = op.order_product_id)
				WHERE o.order_id = ".(int)$order_id ."
				  AND o.language_id = '".$this->config->get('config_language_id')."' 
				  AND o.franchise_id = 0 
				";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	//get return reason
	public function getReturnReason($reason_id = ''){
		$condition = '';
		if($reason_id != ''){
			$condition = "return_reason_id = ".$reason_id. " AND ";
		}
		$query = $this->db->query("SELECT return_reason_id, name
				FROM ". DB_PREFIX ."return_reason
				WHERE ".$condition." language_id = ".$this->config->get('config_language_id')."
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

	//get order no
	public function getOrderNo($order_id){
		$query = $this->db->query("SELECT order_no FROM ".DB_PREFIX."order WHERE order_id = ".$order_id." AND franchise_id = 0");
		return $query->row;
	}

	public function getReturnDetailsByProduct_ids($product_ids){
		if(is_array($product_ids)){
			$sql = "SELECT 
						ocr.*,oop.suborder_id 
					FROM ".DB_PREFIX."return as ocr
					INNER JOIN ".DB_PREFIX."order_product oop ON ocr.order_product_id = oop.order_product_id
					WHERE ocr.order_product_id in (".implode(',',$product_ids).") order by ocr.order_product_id DESC, ocr.return_id DESC
					";
			$result = $this->db->query($sql);
			if($result){
				return $result->rows;
			}
		}
	}

	private function checkInvoiceIsMandatory($seller_id){
        $sql = "SELECT seller_invoice_generate
                FROM ".DB_PREFIX."ms_seller
                WHERE seller_id = '".(int)$seller_id."' ";
        $result = $this->db->query($sql);
        if( $result->num_rows > 0 ){
            return $result->row['seller_invoice_generate'];
        }
    }

	/**
     * public function to generate DebitNote
     * @param: $data Array
     * @return: $debit_note_id Integer
     * @author: Nishu, Nov 2017
	*/
	public function addDebitNote($data = array()){
		//To check DebitNote will be generated for Seller or WSB's Dummy Seller
		$isDnNumberGeneratable = $this->checkInvoiceIsMandatory($data['seller_id']);

		$prefix_obj = new Prefixes();

		$gstin = $data['gstin'] ?? '';
		$gstin = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gstin);
		$gstin = trim($gstin);
		$prefix_detail = $prefix_obj->getPrefix($this->db, $gstin, 'DEBIT_NOTE');
		if(empty($prefix_detail)){
			$prefix_detail['prefix']         = '';
			$prefix_detail['available_no']   = 0;
			$prefix_detail['financial_year'] = date('y-');
		}
		$prefix        = $prefix_detail['prefix'];
        $trxn_done     = 'NOT_DONE';
		$financial_year = '';
		if ( (int)(date('m')) <= 3 ) {
			$financial_year = date('y-', strtotime('-1 years'));
		} else {
			$financial_year = date('y-');
		}
		$seller_invoice_prefix = $prefix . $financial_year;


        if($isDnNumberGeneratable){
        	if($financial_year == $prefix_detail['financial_year']){
        		$available_no = (int)$prefix_detail['available_no'];
        		if($available_no == 0){
        			$debit_note_no = 1;
        		}else{
        			$debit_note_no = (int)$available_no;
        		}
        	}else{
        		$debit_note_no = 1;
        	}
        	//check for DebitNote no is already existed in DB or not
    		if($this->CheckDnNoExistance($seller_invoice_prefix, $debit_note_no)){
    			return 0; //DebitNote Id 0 returned for for already existed debit_note_no in db
    		}
        	$prefix_data = array();
        	$prefix_data['available_no'] = $debit_note_no + 1;
        	$prefix_data['prefix']       = $prefix;
        	$prefix_data['financial_year']= $financial_year;
        	Prefixes::updatePrefixAvailableNo($this->db, $prefix_data);
        }else{
            $debit_note_no = 0;
            $trxn_done = 'NOT_APPLICABLE';
        }
		$debit_note_date  = date('d/m/Y');
		
		//Check if DebitNote is already generated for given return ids
		if($this->checkForDebitNote($data)){
			$sql = "INSERT INTO " . DB_PREFIX . "seller_debit_note
						SET 
					order_id 		= '".(int)$data['order_id']."',
					suborder_id 	=  '".$this->db->escape($data['suborder_id'])."',
					seller_id 		='".(int)$data['seller_id']."',
					debit_note_prefix = '".$this->db->escape($seller_invoice_prefix)."',
					debit_note_no 	= '".$debit_note_no."',
					date_added 		= NOW(),
                    trxn_done 		= '" . $this->db->escape($trxn_done) . "',
					user 			= '". $this->db->escape($this->user->getUserName()["name"]) . "'";

			if($this->db->query($sql)){
				$debit_note_id =  $this->db->getLastId();

				//Create object of return action base class
				$return_action_base = new ReturnActionBase($this);

				//Create object of ReturnInfo class
				$return_info    = new ReturnInfo($this);

				//Invoke method to replicate oc_return table enteries and update old rows with active 0
				$returns = $return_info->getReturnById(implode(',', $data['return_ids']));

				//ReturnIds, newly inserted for debit_note creation
				$new_return_ids = array();
				if(!empty($returns)){
					foreach ($returns as $r) {
						$r['return_action_id'] = RETURN_ACTION_IDS['DN_Generated_For_Seller'];
						$r['debit_note_id']    = $debit_note_id;
						$r['internal_note']    = 'DN generated for Seller returnId:#'.$r['return_id'];
						$new_return_ids[]      = $return_action_base->insertReturn($r);
					}
					$active_row = 0;
					$return_action_base->resetReturnActiveStatusForReturnIds($active_row, implode(',', $data['return_ids']));
				}

				//Check if debit note for SOR products
				if($debit_note_no == 0){
					$dn_mail_data = array();
					$dn_mail_data['order_no']  = $data['order_no'];
					$dn_mail_data['seller_id'] = (int)$data['seller_id'];
					$dn_mail_data['dn_id']     = (int)$debit_note_id;
					$dn_mail_data['return_ids'] = $new_return_ids;

					//Send Mail internally to inform
					$this->sendDNMail($dn_mail_data);
				}
				return $debit_note_id;
			}
	 	}
	}

	/**
     * public function to generate ReplacementNote
     * @param: $data Array
     * @return: $replacement_note_id Integer
     * @author: MSA, August 2018
	*/
	public function addReplacementNote($data = array()){
		//To check DebitNote will be generated for Seller or WSB's Dummy Seller
		$isDnNumberGeneratable = $this->checkInvoiceIsMandatory($data['seller_id']);

		$prefix_obj = new Prefixes();

		$gstin = $data['gstin'] ?? '';
		$gstin = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gstin);
		$gstin = trim($gstin);

		$prefix_detail = $prefix_obj->getPrefix($this->db, $gstin, 'REPLACEMENT_NOTE');
		if(empty($prefix_detail)){
			$prefix_detail['prefix']         = '';
			$prefix_detail['available_no']   = 0;
			$prefix_detail['financial_year'] = date('y-');
		}
		$prefix        = $prefix_detail['prefix'];
        $trxn_done     = 'NOT_DONE';
		$financial_year = '';
		if ( (int)(date('m')) <= 3 ) {
			$financial_year = date('y-', strtotime('-1 years'));
		} else {
			$financial_year = date('y-');
		}
		$seller_invoice_prefix = $prefix . $financial_year;


        if($isDnNumberGeneratable){
        	if($financial_year == $prefix_detail['financial_year']){
        		$available_no = (int)$prefix_detail['available_no'];
        		if($available_no == 0){
        			$replacement_note_no = 1;
        		}else{
        			$replacement_note_no = (int)$available_no;
        		}
        	}else{
        		$replacement_note_no = 1;
        	}
        	//check for DebitNote no is already existed in DB or not
    		if($this->CheckDnNoExistance($seller_invoice_prefix, $replacement_note_no)){
    			return 0; //DebitNote Id 0 returned for for already existed debit_note_no in db
    		}
        	$prefix_data = array();
        	$prefix_data['available_no'] = $replacement_note_no + 1;
        	$prefix_data['prefix']       = $prefix;
        	$prefix_data['financial_year']= $financial_year;
        	Prefixes::updatePrefixAvailableNo($this->db, $prefix_data);
        }else{
            $replacement_note_no = 0;
            $trxn_done = 'NOT_APPLICABLE';
        }
		$debit_note_date  = date('d/m/Y');
		//Check if DebitNote is already generated for given return ids
		if($this->checkForReplacementNote($data)){
			$sql = "INSERT INTO " . DB_PREFIX . "replacement_note
						SET 
							replacement_note_prefix = '".$this->db->escape($seller_invoice_prefix)."',
							replacement_note_no 	= '".$replacement_note_no."',
							date_added 				= NOW(),
							user 					= '". $this->db->escape($this->user->getUserName()["name"]) . "'";

			if($this->db->query($sql)){
				$replacement_note_id =  $this->db->getLastId();

				//Create object of return action base class
				$return_action_base = new ReturnActionBase($this);

				//Create object of ReturnInfo class
				$return_info    = new ReturnInfo($this);

				//Invoke method to replicate oc_return table enteries and update old rows with active 0
				$returns = $return_info->getReturnById(implode(',', $data['return_ids']));

				//ReturnIds, newly inserted for debit_note creation
				$new_return_ids = array();
				if(!empty($returns)){
					foreach ($returns as $r) {
						$r['return_action_id'] 	= RETURN_ACTION_IDS['Replacement_Note'];
						$r['replacement_note_id']= $replacement_note_id;
						$r['internal_note']    = 'RN generated for Seller returnId:#'.$r['return_id'];
						$new_return_ids[]      = $return_action_base->insertReturn($r);
					}
					$active_row = 0;
					$return_action_base->resetReturnActiveStatusForReturnIds($active_row, implode(',', $data['return_ids']));
				}
				return $replacement_note_id;
			}
	 	}
	}

	/**
	 * @info: Public Method to Check already exitance of debit_note_no in debit_note table
	 * @param: $prefix Sring, $debit_note_no Integer
	 * @return: Boolean
	 * @author: Nishu, May 2018
	*/
	public function CheckDnNoExistance($prefix, $debit_note_no){
		$sql = "
				SELECT *
				FROM " . DB_PREFIX . "seller_debit_note
				WHERE
					debit_note_prefix = '".$this->db->escape($prefix)."'
					AND debit_note_no 	= '".$debit_note_no."'
			   ";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Public function to check Debit Note is already genareated or not for given returns
	 * @param: $data Array
	 * @return: Boolen
	 * @author: Nishu, Nov 2017
	*/
	public function checkForDebitNote($data){
		$sql = "
				SELECT 
				    ocr3.input_return_id, ocr1.return_id as return_id_containing_dn 
				FROM
				    " . DB_PREFIX . "return AS ocr1 
				INNER JOIN
				    (SELECT ocr2.return_id as input_return_id, ocr2.order_product_id, ocr2.master_return_id 
				     FROM " . DB_PREFIX . "return ocr2
				     WHERE ocr2.return_id IN (". implode(',', $data['return_ids']) .")
				     GROUP BY ocr2.order_product_id, ocr2.master_return_id) AS ocr3 
				  ON ocr1.order_product_id = ocr3.order_product_id AND ocr1.master_return_id = ocr3.master_return_id
				INNER JOIN " . DB_PREFIX . "seller_debit_note AS dn ON dn.debit_note_id = ocr1.debit_note_id
				WHERE 
				  ocr1.debit_note_id > 0 
				  AND dn.debit_note_status = 1 
				GROUP BY ocr3.input_return_id
				";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Public function to check Replacement Note is already genareated or not for given returns
	 * @param: $data Array
	 * @return: Boolen
	 * @author: Nishu, Nov 2017
	*/
	public function checkForReplacementNote($data){
		$sql = "
				SELECT 
				    ocr3.input_return_id, ocr1.return_id as return_id_containing_dn 
				FROM
				    " . DB_PREFIX . "return AS ocr1 
				INNER JOIN
				    (SELECT ocr2.return_id as input_return_id, ocr2.order_product_id, ocr2.master_return_id 
				     FROM " . DB_PREFIX . "return ocr2
				     WHERE ocr2.return_id IN (". implode(',', $data['return_ids']) .")
				     GROUP BY ocr2.order_product_id, ocr2.master_return_id) AS ocr3 
				  ON ocr1.order_product_id = ocr3.order_product_id AND ocr1.master_return_id = ocr3.master_return_id
				INNER JOIN " . DB_PREFIX . "replacement_note AS rn ON rn.replacement_note_id = ocr1.replacement_note_id
				WHERE 
				  ocr1.replacement_note_id IS NOT NULL
				GROUP BY ocr3.input_return_id
				";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			return false;
		}else{
			return true;
		}
	}

	/**
	* Public function to get product details by given return ids
	* @param: $data Array
	* @return: $data Array
    * @author: Nishu, Nov 2017
	*/
	public function getProductsByReturns($data){
		$return_data = array();
		$sql = "SELECT 
				(op.piece_in_set * op.quantity) as ttl_qty,
				op.sku,
				op.model,
				op.product_id,
				op.store_sales,
				oop.suborder_id,
				ocr.return_id,
				ocr.master_return_id,
				ocr.order_product_id,
				ocr.debit_note_id,
				ocr.quantity as dn_qty
				 FROM ".DB_PREFIX."product as op
				 INNER JOIN ".DB_PREFIX."order_product as oop ON op.product_id = oop.product_id
				 INNER JOIN ".DB_PREFIX."return as ocr ON ocr.order_product_id = oop.order_product_id
				 WHERE 
				 	ocr.return_id IN (". implode(',', $data['return_ids']) .")";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			$data['products']  = array();
			foreach ($result->rows as $row) {
				$product_details = array();
				$product_details['ttl_qty']          = $row['ttl_qty'];
				$product_details['model']            = $row['model'];
				$product_details['store_sales']      = $row['store_sales'];
				$product_details['suborder_id']      = $row['suborder_id'];
				$product_details['order_product_id'] = $row['order_product_id'];
				$product_details['debit_note_id']    = $row['debit_note_id'];
				$product_details['dn_qty']           = $row['dn_qty'];

				$data['products'][]                  = $product_details;
			}
		}
		return $data;
	}

	/**
	* Public function to send internal mail for debit note
	* @param: $data Array
	* @return: void
    * @author: Nishu, Nov 2017
	*/
	public function sendDNMail($data){
		$data = $this->getProductsByReturns($data);
		$html = MailTemplate::mailForStoreDNsHTML($data);
        $today_date = date("d/F/Y");
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        ;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->addReplyTo(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
        $mail->Subject = 'Debit Note(s) for WSB Store (' . $today_date . ')';
        $mail->msgHTML($html);
        $mail->send();
	}

	/**
	*public function to add Debit note for Custom Party
	*@param: $data array
	*@return: Debit note id
	*@author: Nishu, 2017
	*/
	public function addCustomDebitNote($data = array()){
		$prefix_obj = new Prefixes();

		$gstin = $data['gstin'] ?? '';
		$gstin = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gstin);
		$gstin = trim($gstin);

		$prefix_detail = $prefix_obj->getPrefix($this->db, $gstin, 'DEBIT_NOTE');
		if(empty($prefix_detail)){
			$prefix_detail['prefix']         = '';
			$prefix_detail['available_no']   = 0;
			$prefix_detail['financial_year'] = date('y-');
		}
		$prefix        = $prefix_detail['prefix'];

		$financial_year = '';
		if ( (int)(date('m')) <= 3 ) {
			$financial_year = date('y-', strtotime('-1 years'));
		} else {
			$financial_year = date('y-');
		}
		$dn_prefix = $prefix . $financial_year;
        
    	if($financial_year == $prefix_detail['financial_year']){
    		$available_no = (int)$prefix_detail['available_no'];
    		if($available_no == 0){
    			$debit_note_no = 1;
    		}else{
    			$debit_note_no = (int)$available_no;
    		}
    	}else{
    		$debit_note_no = 1;
    	}

    	//check for DebitNote no is already existed in DB or not
		if($this->CheckDnNoExistance($dn_prefix, $debit_note_no)){
			return 0; //DebitNote Id 0 returned for for already existed debit_note_no in db
		}

    	$prefix_data = array();
    	$prefix_data['available_no'] = $debit_note_no + 1;
    	$prefix_data['prefix']       = $prefix;
    	$prefix_data['financial_year']= $financial_year;
    	Prefixes::updatePrefixAvailableNo($this->db, $prefix_data);
		//Trxn are not applicable for custom debit note
        $trxn_done     = 'NOT_APPLICABLE';

		//Check if DebitNote is already generated for given return ids
		if($this->checkForDebitNote($data)){
			$custom_party = $this->getCustomPartyDetails( $data['custom_party_id'] );
			$custom_gst   = $custom_party['gst_number'] ?? '';
			$custom_gst   = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $custom_gst);
			$custom_gst   = trim($custom_gst);

			$buyer_gst   = $data['buyer_data']['tin'] ?? '';
			$buyer_gst   = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $buyer_gst);
			$buyer_gst   = trim($buyer_gst);

			$custom_dn_meta = array();
			$custom_dn_meta['custom_party']['id']         = $custom_party['custom_id'];
			$custom_dn_meta['custom_party']['firm_name']  = $custom_party['firm_name'];
			$custom_dn_meta['custom_party']['address1']   = $custom_party['address1'];
			$custom_dn_meta['custom_party']['address2']   = $custom_party['address2'];
			$custom_dn_meta['custom_party']['country']    = $custom_party['country'];
			$custom_dn_meta['custom_party']['city']       = $custom_party['city'];
			$custom_dn_meta['custom_party']['state']      = $custom_party['state'];
			$custom_dn_meta['custom_party']['zone_id']    = $custom_party['zone_id'];
			$custom_dn_meta['custom_party']['pincode']    = $custom_party['pincode'];
			$custom_dn_meta['custom_party']['gst_number'] = $custom_gst;
			$custom_dn_meta['custom_party']['invoice_ref']= $data['custom_debit_note_ref'];

			$custom_dn_meta['buyer_data']['company']    = $data['buyer_data']['company'];
			$custom_dn_meta['buyer_data']['address1']   = $data['buyer_data']['address1'];
			$custom_dn_meta['buyer_data']['address2']   = $data['buyer_data']['address2'];
			$custom_dn_meta['buyer_data']['city']       = $data['buyer_data']['city'];
			$custom_dn_meta['buyer_data']['pincode']    = $data['buyer_data']['pincode'];
			$custom_dn_meta['buyer_data']['gst_number'] = $buyer_gst;
			$custom_dn_meta['buyer_data']['state']      = $data['buyer_data']['state'];
			$custom_dn_meta['buyer_data']['country']    = $data['buyer_data']['country'];
			$custom_dn_meta['buyer_data']['zone_id']    = $data['buyer_data']['zone_id'];

			$total_dn_val = (float)$data['custom_dn_value'];
			$data['custom_dn_value'] = round($total_dn_val * (1 + $data['tax_rate'] / 100), 2);
            $sql = "INSERT INTO " . DB_PREFIX . "seller_debit_note
						SET 
					order_id 		= '".(int)$data['order_id']."',
					suborder_id 	=  '".$this->db->escape($data['suborder_id'])."',
					seller_id 		='".(int)$data['seller_id']."',
					custom_id 		='".(int)$data['custom_party_id']."',
					custom_debit_note_meta = '".$this->db->escape(serialize($custom_dn_meta))."',
					sac_code        = '".$this->db->escape(LOGISTIC_HSN_CODE)."',
					sac_tax_rate    = ".(float)$data['tax_rate'].",
					debit_note_amount= ". (float)$data['custom_dn_value'] .",
					debit_note_prefix = '".$this->db->escape($dn_prefix)."',
					debit_note_no 	= '".$debit_note_no."',
					date_added 		= NOW(),
                    trxn_done 		= '" . $this->db->escape($trxn_done) . "',
					user 			= '". $this->db->escape($this->user->getUserName()["name"]) . "',
					custom_debit_note_ref   = '".$this->db->escape($data['custom_debit_note_ref'])."'
				  ";
			if($this->db->query($sql)){
				$debit_note_id =  $this->db->getLastId();

				//Create object of return action base class
				$return_action_base = new ReturnActionBase($this);

				//Create object of ReturnInfo class
				$return_info    = new ReturnInfo($this);

				//Invoke method to replicate oc_return table enteries and update old rows with active 0
				$returns = $return_info->getReturnById(implode(',', $data['return_ids']));

				if(!empty($returns)){
					foreach ($returns as $r) {
						$r['return_action_id'] = RETURN_ACTION_IDS['DN_Generate_for_Logistic_Company'];
						$r['debit_note_id']    = $debit_note_id;
						$r['internal_note']    = 'Custom DN generated for returnId:#'.$r['return_id'];
						$return_action_base->insertReturn($r);
					}
					$active_row = 0;
					$return_action_base->resetReturnActiveStatusForReturnIds($active_row, implode(',', $data['return_ids']));
				}
				return $debit_note_id;
			}
	 	}
	}

    public function updateDebitNoteAmonut( $debit_note_id , $debit_note_amount ){
        $sql  = "UPDATE " . DB_PREFIX . "seller_debit_note ";
        $sql .= "SET debit_note_amount = '" . (float)$debit_note_amount . "' ";
        $sql .= "WHERE debit_note_id = '" . (int)$debit_note_id . "'";
        $result = $this->db->query($sql);
        if( $result ){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * @info: Public method update replacement note amount
     * @author: MSA, August 2018
    */
    public function updateReplacementNoteAmonut( $replacement_note_id , $replacement_note_amount ){
        $sql  = "UPDATE " . DB_PREFIX . "replacement_note ";
        $sql .= "SET replacement_note_amount = '" . (float)$replacement_note_amount . "' ";
        $sql .= "WHERE replacement_note_id = '" . (int)$replacement_note_id . "'";
        $result = $this->db->query($sql);
        if( $result ){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * @info: Public method to set CN prefix and CN Number to member variables
     * @author: Nishu, July 2018
    */
    public function setCnPrefixAndCnNo($data){
    	//Creating Prefix object
		$prefix_obj = new Prefixes();

		$gstin = $data['gstin'] ?? '';
		$gstin = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gstin);
		$gstin = trim($gstin);

		$prefix_detail = $prefix_obj->getPrefix($this->db, $gstin, 'CREDIT_NOTE');
		if(empty($prefix_detail)){
			$prefix_detail['prefix']         = '';
			$prefix_detail['available_no']   = 0;
			$prefix_detail['financial_year'] = date('y-');
		}
	    $prefix        = $prefix_detail['prefix'];

	    $financial_year = '';
		if ( (int)(date('m')) <= 3 ) {
			$financial_year = date('y-', strtotime('-1 years'));
		} else {
			$financial_year = date('y-');
		}
		
		//Set Credit Note Prefix
		$this->_cn_prefix = $prefix . $financial_year;

		//Set Credit Note Number
	    if($financial_year == $prefix_detail['financial_year']){
    		$available_no = (int)$prefix_detail['available_no'];
    		if($available_no == 0){
    			$this->_cn_no = 1;
    		}else{
    			$this->_cn_no = (int)$available_no;
    		}
    	}else{
    		$this->_cn_no = 1;
    	}

    	//Check for CreditNote no is already existed in DB or not
		if($this->CheckCnNoExistance($this->_cn_prefix, $this->_cn_no)){
			return false; //returned null for already existed credit_note_no in db
		}

		//Update Prefix table - with available_no and financial_year
    	$prefix_data = array();
    	$prefix_data['available_no']  = $this->_cn_no + 1;
    	$prefix_data['prefix']        = $prefix;
    	$prefix_data['financial_year']= $financial_year;
    	Prefixes::updatePrefixAvailableNo($this->db, $prefix_data);

    	return true;
    }

    /**
	 * @info: payment_cleared field for oc_credit_note
	 * @author: Nishu, July 2018
    */
    public function setPaymentClearedForCn($data){
    	//Get Order Info
		$selector = array(
                'order'=> array('select' => array('payment_code','stock_transfer'))
               );
		//Get OrderInfo details
		$this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'],'',$selector);
		
		//payment_cleared field for oc_credit_note will be NOT_APPLICABLE,
		//If order is as credit order Or is stock-transfer order
		$payment_code = $this->_order_info['order']['payment_code'];
		if(
			!empty($this->_order_info)
				&&
			(
				in_array($payment_code, CREDIT_PAYMENT_CODES)
					||
				$this->_order_info['order']['stock_transfer'] == 1
			)
		){
			$data['payment_cleared'] = 'NOT_APPLICABLE';
		}

		return $data;
    }

    /**
     * @info: Public function to get shipping charges applied in suborder's buyer invoice
     *          only that shipping charges are not applied in any other CN for this suborder
     * @param: $order_id int, $suborder_id string
     * @return Float
     * @author: Nishu, Dec 2018
    */
    public function getInvoiceShippingForCn(int $order_id, string $suborder_id){
    	$invoice_shipping = 0.00;
    	$sql = "
                SELECT 
                 	osub.shipping_charge,
                 	COALESCE( SUM(cn.invoice_shipping), 0) AS invoice_shipping
                FROM
                    ".DB_PREFIX."suborder AS osub
                LEFT JOIN ".DB_PREFIX."credit_note AS cn 
                	ON osub.order_id = cn.order_id AND cn.credit_note_status = 1 AND cn.is_cod_failed = 1
                WHERE
                	(osub.suborder_id = cn.suborder_id OR cn.suborder_id IS NULL)
                	AND osub.suborder_id = '".$this->db->escape($suborder_id)."'
                	AND osub.order_id = ".(int)$order_id."
    	       ";
    	$result = $this->db->query($sql);
    	if($result->num_rows > 0){
    		$invoice_shipping = max((float)$result->row['shipping_charge'] - (float)$result->row['invoice_shipping'], 0);
    	}
    	return $invoice_shipping;
    }

    /**
     * @info: Method to add an entery into DB 'oc_credit_note'
    */
    public function insertCnToDb($data){

    	$getuserName 	  = $this->user->getUserName();
		$userName         = $getuserName["name"];
		if($data['is_cod_failed'] == 1 ){
			$invoice_shipping = $this->getInvoiceShippingForCn((int)$data['order_id'], $data['suborder_id']);
		}else{
			$invoice_shipping = 0.00;
		}


    	//Set Insert Query for oc_credit_note
		$sql = "INSERT INTO " . DB_PREFIX . "credit_note
				  SET 
				    order_id           = '".(int)$data['order_id']."',
                    suborder_id        = '".$this->db->escape($data['suborder_id'])."',
					credit_note_prefix = '".$this->db->escape($this->_cn_prefix)."',
					credit_note_no     = '".(int)$this->_cn_no."',
					shipping_collected = '".(float)$data['shipping_collected']."',
                    cod_failed_penalty = '".(float)$data['cod_failed_penalty']."',
                    invoice_shipping   = '".(float)$invoice_shipping."',
                    advance_collected  = '".(float)$data['advance_collected']."',
                    cash_discount      = '".(float)$data['cash_discount']."',
                    less_cash_discount = '".(float)$data['less_cash_discount']."',
                    reversal_shipping  = '".(float)$data['reversal_shipping']."',
                    other_charges      = '".(float)$data['other_charges']."',
					date_added         = NOW(),
					is_cod_failed      = '".(int)$data['is_cod_failed']."',
					payment_cleared    = '".$this->db->escape($data['payment_cleared']) ."',
					user               = '". $this->db->escape($userName) . "'";

		$cn_id = 0;
		if($this->db->query($sql)){
			$cn_id =  $this->db->getLastId();
		}

		return $cn_id;
    }

    /**
     * @info: Public method to Add/Update oc_return for newly generated CN
     * @author: Nishu, July 2018
    */
    public function updateReturnTableForAddingCn($data){
    	//Create object of return action base class
		$return_action_base = new ReturnActionBase($this);

		//Create object of ReturnInfo class
		$return_info    = new ReturnInfo($this);

		//Invoke method to replicate oc_return table enteries and update old rows with active 0
		$returns = $return_info->getReturnById($data['return_ids']);

		if(!empty($returns)){
			foreach ($returns as $r) {
				$r['return_action_id'] = RETURN_ACTION_IDS['CN_For_Client'];
				$r['credit_note_id']   = $this->_cn_id;
				$r['internal_note']    = 'Created CN for returnId:#'.$r['return_id'];
				$return_action_base->insertReturn($r);
			}
			$active_row = 0;
			$return_action_base->resetReturnActiveStatusForReturnIds($active_row, $data['return_ids']);
		}
		return true;
    }

    public function isCODFailed(string $return_ids) : bool
    {
    	//Get Return info from return table
		$sql = "SELECT 
					return_reason_id, return_action_id 
		          FROM ".DB_PREFIX."return 
		 			WHERE return_id IN (".$return_ids.")
		 			ORDER BY return_id DESC
					LIMIT 0,1";
		$return_data = $this->db->query($sql);	
		$return_reason_id = (int)$return_data->row['return_reason_id'];
		$return_action_id = (int)$return_data->row['return_action_id'];

		$is_cod_failed = false;
		if($return_reason_id == RETURN_REASON_IDS['COD_Failed']){ 
			$is_cod_failed = true;
		}
		return $is_cod_failed;
    }

    /**
	 * @info: Public function generate CreditNote and update oc_return
	 * @param: $data Array
	 * @author: Nishu, July 2018
    */
	public function addCreditNote($data = array()){
		
		//Set CN Prefix and CN No. to class members 
		if(!$this->setCnPrefixAndCnNo($data)){
			return;
		}
    	
		if($this->checkForCreditNote($data)){

			//payment_cleared field for oc_credit_note will be NOT_APPLICABLE,
			//If order is as credit order Or is stock-transfer order
			$data = $this->setPaymentClearedForCn($data);
			
			//Set Insert CN to oc_credit_note
			$this->_cn_id = $this->insertCnToDb($data);
			
			if(!empty($this->_cn_id)){
				
				//Add/Update oc_return table for adding new generated CreditNote
				$this->updateReturnTableForAddingCn($data);

				return array('credit_note_id' => $this->_cn_id);
			}
		}
	}

	/**
	 * @info: Public Method to Check already exitance of credit_note_no in debit_note table
	 * @param: $prefix Sring, $credit_note_no Integer
	 * @return: Boolean
	 * @author: Nishu, May 2018
	*/
	public function CheckCnNoExistance($prefix, $credit_note_no){
		$sql = "
				SELECT *
				FROM " . DB_PREFIX . "credit_note
				WHERE
					credit_note_prefix = '".$this->db->escape($prefix)."'
					AND credit_note_no 	= '".$credit_note_no."'
			   ";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	* Public function to check Credit Note is already genareated or not
	* @param: $data Array
	* @return: Boolen
	* @author: Nishu, Nov 2017
	*/
	public function checkForCreditNote($data){
		$sql = "
				SELECT 
				    ocr3.input_return_id, ocr1.return_id as return_id_containing_cn 
				FROM
				    ".DB_PREFIX."return AS ocr1 
				INNER JOIN
				    (SELECT ocr2.return_id as input_return_id, ocr2.order_product_id, ocr2.master_return_id 
				     FROM ".DB_PREFIX."return ocr2
				     WHERE ocr2.return_id IN (". $data['return_ids'] .")
				     GROUP BY ocr2.order_product_id, ocr2.master_return_id) AS ocr3 
				  ON ocr1.order_product_id = ocr3.order_product_id AND ocr1.master_return_id = ocr3.master_return_id
				INNER JOIN ".DB_PREFIX."credit_note AS cn ON cn.credit_note_id = ocr1.credit_note_id
				WHERE 
				  ocr1.credit_note_id > 0 
				  AND cn.credit_note_status = 1 
				GROUP BY ocr3.input_return_id
				";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			return false;
		}else{
			return true;
		}
	}

	// get debit note no using for showing in pdf
	public function getDebitNoteNo($order_id,$seller_id,$return_ids){
		$sql = "SELECT debit_note_no, date_added as debit_note_date
				FROM " . DB_PREFIX . "seller_debit_note
				WHERE order_id = ".$order_id."
					AND seller_id = ". $seller_id . "
					AND return_ids IN (".implode(',', $return_ids).")";
		$query = $this->db->query($sql);
		return $query->row;
	}

	//get order_product_id, quantity, reason_id, comment
	public function getReturnInfo($return_ids) {
		$sql = "SELECT  ocr.return_id,
						ocr.order_product_id,
						ocr.quantity as product_quantity,
						ocr.return_reason_id,
						ocr.comment,
						ocrr.name
				FROM ". DB_PREFIX. "return ocr
				LEFT JOIN ". DB_PREFIX. "return_reason ocrr
					ON (ocr.return_reason_id = ocrr.return_reason_id)
				WHERE return_id IN (".implode(',', $return_ids).")";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	/* Method for Cancel debit note pdf download
	* @request: $debit_note_id: Integer of Debit note ID
	* @output: String
	* @author: Nishu, Dec 2017
	*/
	public function cancelDebitNoteDownloadPdf($debit_note_id, $debit_note_type='seller'){
		
		if(empty($debit_note_id)){
			return 0;
		}

		if($debit_note_type == 'custom') {
			$return_action_id =	RETURN_ACTION_IDS['Custom_DN_Cancelled'];
		}else{
			$return_action_id =	RETURN_ACTION_IDS['DN_Cancelled'];
		}
		
		//Get ReturnIds for specific DebitNote Id
		$sql = "SELECT return_id 
				  FROM " . DB_PREFIX . "return 
				  WHERE 
				      debit_note_id =". (int)$debit_note_id. " 
               ";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			//Return Ids and debit_note_status updated in Debit Note table
			$return_ids = array_column($result->rows, 'return_id');
			$update_sql = "UPDATE " . DB_PREFIX . "seller_debit_note
			                SET 
			                	return_ids = '". implode(',',$return_ids). "',
			                	debit_note_status = 0
			                WHERE debit_note_id=".(int)$debit_note_id ;
			$status = $this->db->query($update_sql);

			if($status){
				
				//Create object of return action base class
				$return_action_base = new ReturnActionBase($this);

				//Create object of ReturnInfo class
				$return_info    = new ReturnInfo($this);

				//Invoke method to replicate oc_return table enteries and update old rows with active 0
				$returns = $return_info->getReturnById(implode(',', $return_ids));
				$new_return_ids = array();
				if(!empty($returns)){
					foreach ($returns as $r) {
						$r['return_action_id'] = $return_action_id;
						$r['debit_note_id']    = NULL;
						$r['internal_note']    = 'DN Cancelled for returnId:#'.$r['return_id'];

						$new_r_id = $return_action_base->insertReturn($r);

						//Set Data to mark old returns as inactive
						$new_return_ids[$new_r_id]['return_id'] = $new_r_id;
						$new_return_ids[$new_r_id]['order_product_id'] = $r['order_product_id'];
						$new_return_ids[$new_r_id]['master_return_id'] = $r['master_return_id'];
					}
					//Get Active ReturnIds to mark as Inactive
					$return_action_base->UpdateInactiveReturnIdsForNewReturnIds($new_return_ids);

					// //Mark Old Returns as inactive
					// $active_row = 0;
					// $return_action_base->resetReturnActiveStatusForReturnIds($active_row, implode(',', $active_return_ids));
				}

				return 1;
			}else{
				return 0;
			}
		}
	}

	/**
	 * Method for Cancel debit note pdf download
	 * @param: $debit_note_id: Integer of Debit note ID
	 * @return: String
	 * @author: Nishu, April 2018
	*/
	public function cancelCreditNote($credit_note_id){
		if(empty($credit_note_id)){
			return "CreditNote Id can not be empty.";
		}
		//Get ReturnIds for specific DebitNote Id
		$sql = "SELECT return_id 
				  FROM " . DB_PREFIX . "return 
				  WHERE 
				      credit_note_id =". (int)$credit_note_id;

		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			//Return Ids and debit_note_status updated in Credit Note table
			$return_ids = array_column($result->rows, 'return_id');
			$update_sql = "UPDATE " . DB_PREFIX . "credit_note
			                SET 
			                	return_ids = '". implode(',',$return_ids). "',
			                	credit_note_status = 0,
			                	payment_cleared = 'NOT_APPLICABLE'
			                WHERE credit_note_id=".(int)$credit_note_id ;
			$status = $this->db->query($update_sql);

			if($status){
				
				//Create object of return action base class
				$return_action_base = new ReturnActionBase($this);

				//Create object of ReturnInfo class
				$return_info    = new ReturnInfo($this);

				//Invoke method to replicate oc_return table enteries and update old rows with active 0
				$returns = $return_info->getReturnById(implode(',', $return_ids));

				$new_return_ids = array();

				if(!empty($returns)){
					foreach ($returns as $r) {
						$r['return_action_id'] = RETURN_ACTION_IDS['Cancel_CN'];
						$r['credit_note_id']   = NULL;
						$r['internal_note']    = 'CN Cancelled for returnId:#'.$r['return_id'];

						$new_r_id = $return_action_base->insertReturn($r);

						//Set Data to mark old returns as inactive
						$new_return_ids[$new_r_id]['return_id'] = $new_r_id;
						$new_return_ids[$new_r_id]['order_product_id'] = $r['order_product_id'];
						$new_return_ids[$new_r_id]['master_return_id'] = $r['master_return_id'];
					}

					//Get Active ReturnIds to mark as Inactive
					$return_action_base->UpdateInactiveReturnIdsForNewReturnIds($new_return_ids);
				}
				return 1;
			}else{
 				return 0;
			}
		}
	}

	public function getCustomer($customer_id, $fields = '*') {
		$query = $this->db->query("SELECT $fields FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getSellersFromOrderProductId($order_product_id){
		$sql  = "SELECT op.order_product_id,
		                op.order_id,
                        op.seller_sku,
                        oms.nickname,
                        oc.email,
                        oms.seller_id,
                        oms.company ";
		$sql .= "FROM ".DB_PREFIX."order_product op ";
		$sql .= "INNER JOIN ".DB_PREFIX."ms_seller oms ";
		$sql .=       "ON ( op.seller_id = oms.seller_id ) ";
		$sql .= "INNER JOIN ".DB_PREFIX."customer oc ";
		$sql .=       "ON ( oc.customer_id = oms.seller_id ) ";
		$sql .= "WHERE op.order_product_id IN ( ".implode(',',$order_product_id)." )";
		$result = $this->db->query($sql);

		return $result->rows;
	}

	public function calculateShippingCharges($order_product_id){
		$order_product_id = implode(",",$order_product_id);
		$sql = "SELECT order_product_id,weight_per_piece 
		            FROM ".DB_PREFIX."order_product
					WHERE order_product_id IN($order_product_id)";
		$result = $this->db->query($sql);
		return $result->rows;
	}

	public function checkReturnsForOrder( $order_id ){
			$sql  = "SELECT ocr.return_id ";
			$sql .=    "FROM ".DB_PREFIX."order_product oop ";
			$sql .=    "INNER JOIN ".DB_PREFIX."return ocr on ( oop.order_product_id = ocr.order_product_id) ";
			$sql .=    "WHERE oop.order_id = '".(int)$order_id."' ";
			$results = $this->db->query($sql);
			if($results->num_rows  > 0 ){
					return $results->num_rows;
			}
			else{
					return false;
			}
	}

	public function getSellerInvoicesByOrderId( $order_id ){
			$sql  = "SELECT seller_id, date_added, gst ";
			$sql .=    "FROM ".DB_PREFIX."seller_invoice ";
			$sql .=    "WHERE order_id = '".(int)$order_id."' ";
			$results = $this->db->query($sql);
			if( $results->num_rows > 0){
					return $results->rows;
			}
			return false;
	}

	public function getInvoicesOrderProductIdByOrderId( $order_id ){
			if(!empty($order_id)){
					$sql  = "SELECT order_product_id ";
					$sql .=    "FROM ".DB_PREFIX."order_product oop ";
					$sql .=    "WHERE 
									oop.seller_invoice_id > 0 
								AND oop.seller_invoice_id IS NOT NULL
								AND oop.order_id = '".(int)$order_id."'";
					$results = $this->db->query($sql);
					if( $results->num_rows > 0 ){
							return array_column( $results->rows , 'order_product_id' );
					}
			}
			return array();
	}

	/**
	 * Public function to add custom(CN/DN) return in product orders
	 * @param : $order_id
	 * @return : Array 
	 * @author : Nishu
	*/
	public function getBuyerInvoicesByOrderId( $order_id ){
			$sql  = "SELECT suborder_id ";
			$sql .=    "FROM ".DB_PREFIX."suborder ";
			$sql .=    "WHERE order_id = '".(int)$order_id."' AND 
							  invoice_no != 0 AND invoice_no IS NOT NULL";
			$results = $this->db->query($sql);
			if( $results->num_rows > 0){
					return $results->rows;
			}
			return array();
	}
	
	/**
	Public function to get Seller by orderID
	@param: return_reason_id
	@return: array
	@author: Nishu, 2017
	*/
	public function getSellerByOrderId($order_id = ''){
		$sql  = "SELECT DISTINCT ms.seller_id, ms.nickname, ms.company, ms.email ";
		$sql .=    "FROM ".DB_PREFIX."order_product as op ";
		$sql .=    "INNER JOIN ".DB_PREFIX."ms_seller as ms ON op.seller_id = ms.seller_id ";
		
		if($order_id != ''){
			$sql .= "WHERE op.order_id	= '".$order_id."'";
		}
		$results = $this->db->query($sql);
		return $results->rows;
		
	}

	/**
	Public function to copy order option
	@param: $opid, $new_opid
	@return: Void
	@author: Nishu, 2017
	*/
	public function copyOrderOption($opid, $new_opid){
		$key_in = array('order_product_id'	=> $opid);
		$key_out = array();
        $key_out[] = array('order_product_id'	=> $new_opid);
        $skip_fields = array('order_option_id');

        $this->db->copyRow( DB_PREFIX . 'order_option', $key_in, $key_out, false, $skip_fields);
	}

	/**
	 * Method to get Custom Parties details
	 * @param searching data
	 * Output: get searching address data
	 * Author: Nishu, 2017
	 */
	public function getCustomParties($data = array()){
		$sql = "SELECT *
 				  FROM ". DB_PREFIX ."custom_parties ";
		if((isset($data['search_firm_name']) && !empty($data['search_firm_name'])) || (isset($data['search_city']) && !empty($data['search_city']))){
			$sql .= "WHERE MATCH(firm_name,city)
				  		AGAINST ('".$data['search_firm_name']." ".$data['search_city']."' IN BOOLEAN MODE)
				  	AND status = 1 ";
		}else{
			$sql .= "WHERE status = 1 ORDER BY custom_id ASC";
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	/**
	*Public function to save Custom party details 
	*@author: Nishu, 2017
    */
    public function save_custom_party($post){
    	$gstin = $post['gst_num'] ?? '';
    	$gstin = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gstin);
		$gstin = trim($gstin);

    	$sql  = "INSERT INTO " . DB_PREFIX . "custom_parties SET ";
		$sql .= 	"firm_name 		= '" . $this->db->escape($post['firm_name']) . "',";
		$sql .= 	"address1		= '" . $this->db->escape($post['address1']) . "',";
		$sql .= 	"address2 		= '" . $this->db->escape($post['address2']) . "',";
		$sql .= 	"country 		= '" . $this->db->escape($post['country']) . "',";
		$sql .= 	"country_id 	= '" . (int)$post['country_id'] . "',";
		$sql .= 	"city 			= '" . $this->db->escape($post['city']) . "',";
		$sql .= 	"state 			= '" . $this->db->escape($post['state']) . "',";
		$sql .= 	"zone_id 		= '" . (int)$post['zone_id'] . "',";
		$sql .= 	"pincode 		= '" . $this->db->escape($post['post_code']) . "',";
		$sql .= 	"gst_number 	= '" . $this->db->escape($gstin) . "',";
		$sql .= 	"date_added 	= NOW(),";
		$sql .= 	"user_id 		= '". $this->user->getId() ."'";

		$this->db->query($sql);
    }  

    /**
	*Public function to get debite note meta data to generate debit note for custom party 
	*@param: $data array
	*@return: Data Meta array
	*@author: Nishu, 2017
    */
    public function getCustomDebitMeta($data){
    	$invoice_data = array();
    	$seller_invoice = new SellerInvoice($this->registry);
    	$custom_party = $this->getCustomPartyDetails($data['custom_party_id']);
    	$invoice_data = $seller_invoice->getInvoiceInfo($data['order_id'],$data['suborder_id'],$data['seller_id']);

    	//Get GST StateCode for Custom Party
	    $model_localisation_zone = null;
	    $this->load->model('localisation/zone', 'frontend');
	    if(method_exists( $this->registry, 'get')){
	      $model_localisation_zone = $this->registry->get('frontend_model_localisation_zone');
	    }else{
	      $model_localisation_zone = $this->registry->frontend_model_localisation_zone;
	    }

	    $gst_state_code = $model_localisation_zone->getZoneGSTStateCode($custom_party['zone_id']);

    	$invoice_meta = unserialize($invoice_data['seller_invoice_meta']);
    	$gst_number  = $custom_party['gst_number'] ?? '';
        $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number  = trim($gst_number);


		$invoice_meta['seller_data'] = array();
		$invoice_meta['seller_data']['company'] 	      = $custom_party['firm_name'];
		$invoice_meta['seller_data']['address1'] 		  = $custom_party['address1'];
		$invoice_meta['seller_data']['address2'] 		  = $custom_party['address2'];
		$invoice_meta['seller_data']['pincode'] 		  = $custom_party['pincode'];
		$invoice_meta['seller_data']['city'] 			  = $custom_party['city'];
		$invoice_meta['seller_data']['tin'] 			  = $gst_number;
		$invoice_meta['seller_data']['zone_id'] 		  = $custom_party['zone_id'];
		$invoice_meta['seller_data']['country_id'] 		  = $custom_party['country_id'];
		$invoice_meta['seller_data']['state'] 			  = $custom_party['state'];
		$invoice_meta['seller_data']['country']			  = $custom_party['country'];
		$invoice_meta['seller_data']['state_code'] 		  = array();
		$invoice_meta['seller_data']['state_code'][0]['zone_id'] 		= $custom_party['zone_id'];
		$invoice_meta['seller_data']['state_code'][0]['gst_state_code'] = (isset($gst_state_code[0]['gst_state_code']) ? $gst_state_code[0]['gst_state_code'] : '');
		$invoice_meta['seller_data']['vat_input_rule_id'] = 0;
		return serialize($invoice_meta);
    }

    /**
    *public functon to get Custom party details
    *@param: $custom_id
    *@return: array
    *@author: Nishu, 2017
    */
    public function getCustomPartyDetails($custom_id = ''){
    	$sql = "SELECT *
 				  FROM ". DB_PREFIX ."custom_parties 
 				  WHERE custom_id = ".$custom_id;
		$query = $this->db->query($sql);
		return $query->row;
    }

	/**
	* Public function to get all Master return Ids
	*@param: $order_id
	*@return: Master_ids as array
	*/
	public function getMasterReturnIdsByOrderId($order_id){
		$data = array();

		$action_ids = array(
						(int)RETURN_ACTION_IDS['Return_Request_Accepted'],
						(int)RETURN_ACTION_IDS['Replacement_Request_Accepted'],
						(int)RETURN_ACTION_IDS['Self_Shipment'],
						(int)RETURN_ACTION_IDS['Action_Cancel_Reverse_Shipment']
					);
		
		/////////////Commented Old query------ Have to remove this section after verification/////

		/*$sql  = "SELECT mr.master_return_id, GROUP_CONCAT(oop.model) AS oop_model 
					FROM ".DB_PREFIX."master_return as mr
				 INNER JOIN ".DB_PREFIX."return as r ON r.master_return_id = mr.master_return_id 
				 INNER JOIN ".DB_PREFIX."order_product as oop ON oop.order_product_id = r.order_product_id ";
		if($order_id != ''){
			$sql .= "WHERE 
			            mr.order_id	= '".$order_id."' 
			            AND oop.buyer_invoice_id IS NOT NULL
			            AND oop.buyer_invoice_id > 0
			            AND ( r.credit_note_id IS NULL OR r.credit_note_id = 0 )
			            AND (	mr.return_shipment_tracking_id IS NULL 
				            	OR 
				            	mr.return_shipment_tracking_id = 0 
				            	OR 
				            	mr.return_shipment_tracking_id = ''
			            	)
			            AND r.return_action_id IN (".implode(',',$action_ids).")
			        ";
		}
		$sql .= " GROUP BY r.master_return_id "; */

		//New Query according to new DB structure
		$sql = "SELECT 
				    omr.master_return_id,
				    GROUP_CONCAT(oop.model) AS oop_model
				FROM ".DB_PREFIX."master_return as  omr
				INNER JOIN 
				(
				    SELECT 
				            MAX(return_id) as return_id,
				            MAX(credit_note_id) as credit_note_id,
				            master_return_id
				    FROM ".DB_PREFIX."return 
				    GROUP BY master_return_id,order_product_id
				) as ocr ON ocr.master_return_id = omr.master_return_id
				LEFT JOIN ".DB_PREFIX."return as ocr1 ON ocr1.return_id = ocr.return_id 
				LEFT JOIN ".DB_PREFIX."order_product oop ON oop.order_product_id = ocr1.order_product_id
				LEFT JOIN ".DB_PREFIX."credit_note ocn ON ocn.credit_note_id = ocr.credit_note_id
				WHERE
				    omr.order_id	= '".(int)$order_id."' 
				    AND 
				    ocr1.return_action_id IN (".implode(',',$action_ids).") 
				    AND
				    ocr1.active_row = 1 
				    AND
				    oop.buyer_invoice_id IS NOT NULL 
				    AND
				     oop.buyer_invoice_id > 0 
				    AND 
				    ( omr.return_shipment_tracking_id Is NULL OR  omr.return_shipment_tracking_id = 0 )
				    AND
				    
				    ( ocn.credit_note_id IS NULL 
				      OR 
				      ocn.credit_note_status = 0 
				    ) 
			    GROUP BY 
					omr.master_return_id
			 ";
			 
		//Query Execution
		$results = $this->db->query($sql);

		if($results->num_rows > 0){ //If ResultSet is not empty
			$data = $results->rows;
		}
		return $data;
	}

	/**
	* Public function to get all Master return Ids
	*@param: $order_id
	*@return: Master_ids as array
	*/
	public function getShipmentBackToCustomerReturnIdsByOrderId($order_id){
		$data = array();

		$action_ids = array(
					  (int)RETURN_ACTION_IDS['Return_Goods_Rejected'],
					  (int)RETURN_ACTION_IDS['Seller_Rejected_Accepting_Returns'],
					  (int)RETURN_ACTION_IDS['Replacement_Given_By_Seller'],
					  (int)RETURN_ACTION_IDS['Cancel_CN']
					);
		
		/////////////Commented Old query------ Have to remove this section after verification////////
		/*	
		$sql  = "SELECT mr.master_return_id, r.return_id, oop.order_product_id, oop.model AS oop_model ";
		$sql .= "FROM ".DB_PREFIX."master_return as mr ";
		$sql .= "INNER JOIN ".DB_PREFIX."return as r ON r.master_return_id = mr.master_return_id ";
		$sql .= "INNER JOIN ".DB_PREFIX."order_product as oop ON oop.order_product_id = r.order_product_id ";
		if($order_id != ''){
			$sql .= "WHERE 
			            mr.order_id	= '".$order_id."' 
			            AND r.active_row = 1
			            AND oop.buyer_invoice_id IS NOT NULL
			            AND oop.buyer_invoice_id > 0
			            AND ( r.credit_note_id IS NULL OR r.credit_note_id = 0 )
			            AND (	r.return_shipment_backto_customer_id IS NULL 
			            		OR 
			            		r.return_shipment_backto_customer_id = 0 
			            		OR 
			            		r.return_shipment_backto_customer_id = ''
			            	)
			            AND r.return_action_id IN (".implode(',',$action_ids).")
			        ";
		}*/

		$sql = "
				SELECT 
				    omr.master_return_id, 
				    ocr1.return_id, 
				    oop.order_product_id, 
				    oop.model AS oop_model
				FROM ".DB_PREFIX."master_return as  omr
				INNER JOIN 
				(
				    SELECT 
				            MAX(return_id) as return_id,
				            MAX(credit_note_id) as credit_note_id,
				            master_return_id
				    FROM ".DB_PREFIX."return
				    GROUP BY master_return_id,order_product_id

				) as ocr ON ocr.master_return_id = omr.master_return_id

				LEFT JOIN ".DB_PREFIX."return as ocr1 ON ocr1.return_id = ocr.return_id 
				LEFT JOIN ".DB_PREFIX."order_product oop ON oop.order_product_id = ocr1.order_product_id
				LEFT JOIN ".DB_PREFIX."credit_note ocn ON ocn.credit_note_id = ocr.credit_note_id
				WHERE
				 	omr.order_id	= '".$order_id."'
				 	AND
				    ocr1.return_action_id IN (".implode(',',$action_ids).") 
				    AND
				    oop.buyer_invoice_id IS NOT NULL 
				    AND
				     oop.buyer_invoice_id > 0 
				    AND 
				    ( ocr1.return_shipment_backto_customer_id Is NULL OR  ocr1.return_shipment_backto_customer_id = 0 )
				    AND
				    ( ocn.credit_note_id IS NULL 
				      OR 
				      ocn.credit_note_status = 0 
				    )
		      ";
		
		//Query Execution
		$results = $this->db->query($sql);

		if($results->num_rows > 0){ //If ResultSet is not empty
			$data = $results->rows;
		}
		return $data;
	}


	/**
	* Public function to mark NuvoEx Docket as used or not
	*@param: Docket No, Used
	*@author: Nishu, August 2017
	*/
	public function addReturnDocket($data){
		$sql = "INSERT INTO ".DB_PREFIX."return_dockets 
					SET docket_no   = '". $this->db->escape($data['aws_docket']). "', 
						date_added  = Now(), 
						used 		= 1, 
						master_return_id = '" .$this->db->escape($data['master_return_id']). "', 
						req_res = '" .$this->db->escape($data['req_res']). "'
					";
		$results = $this->db->query($sql);
	}

	/**
	* Public function to Add Reverse Shipment
	*@param: array
	*@return: Shipment Id
	*@author: Nishu, August 2017
	*/
	public function addReverseShipment($data){
		$sql  = "INSERT INTO ".DB_PREFIX."return_shipment_tracking 
				   SET 
					courier_company 	= '". $this->db->escape($data['Courier Company'])."',
					tracking_no 		= '". $this->db->escape($data['aws_docket'])."',
					date_added 			= Now(),
					order_no 			= '". $this->db->escape($data['order_no'])."',
					weight 				= '". $this->db->escape($data['weight'])."',
					value 				= '". (float)$data['total_amount']."',
					status 				= '". $this->db->escape($data['response_text']->status)."',
					equivalent_status	= '". $this->db->escape($data['response_text']->status)."',
					package_description = '". $this->db->escape($data['package_desc'])."',
					qty 				= '". (int)$data['qty']."',
					vendor_code 		= '". $this->db->escape($data['vendor_code'])."',
					request_param 		= '". $this->db->escape(serialize($data['req']))."',
					remarks 			= '". $this->db->escape($data['remarks'])."'
				";
		$this->db->query($sql);
		return $this->db->getLastId();
	}
	
	/**
	* Public function to update master return with reverse
	*@param: master_return_id, return_shipment_id
	*@author: Nishu, August 2017
	*/
	public function updateMasterReturnWithShipment($master_return_id, $return_shipment_id){
		$sql  = "UPDATE ".DB_PREFIX."master_return  
					SET return_shipment_tracking_id = '". (int)$return_shipment_id."'
				WHERE master_return_id IN (". $master_return_id . ")
				";
		$this->db->query($sql);
	}

	/**
	* Public function to get reverse shipment for nuvoex 
	*@param: Order No
	*@return: array
	*@author: Nishu, August 2017
	*/
	public function getReverseShipments($order_no){
		$sql  = "SELECT 
					rst.*, 
					wa.warehouse_name,
					wa.city,
					rst.status as shipment_status, 
					GROUP_CONCAT(mr.master_return_id) as master_return_id
					FROM ".DB_PREFIX."return_shipment_tracking rst
				LEFT JOIN ".DB_PREFIX."master_return as mr ON mr.return_shipment_tracking_id = rst.shipping_id
				INNER JOIN ".DB_PREFIX."warehouse_address as wa ON wa.warehouse_id = rst.warehouse_id
				WHERE 
					rst.order_no = '". $this->db->escape($order_no) ."'
					AND
					rst.tracking_no !=''
				GROUP BY rst.tracking_no
				";
		$results = $this->db->query($sql);
		if($results->num_rows > 0){
			return $results->rows;
		}
	}

	/**
	* Public function to update oc_return with reverse shipment
	*@param: Master Return Id, Return shipment Id
	*@author: Nishu, August 2017
	*/
	public function updateReturnWithShipment($master_return_id, $return_shipment_id){
		$sql  = "UPDATE ".DB_PREFIX."return  
					SET return_shipment_tracking_id = '". (int)$return_shipment_id."'
				 WHERE master_return_id IN (". $master_return_id . ")
				";
		$this->db->query($sql);
	}

	/**
	* Public function to get total weight for given return Ids
	*@param: Return Ids, array
	*@return: Array
	*@author: Nishu, Sept 2017
	*/
	public function getTotalWeightForReturns($return_ids){
		$sql  = "SELECT 
					sum(case
				        WHEN oop.weight_per_piece >= 0.01 THEN oop.weight_per_piece * ocr.quantity
				        WHEN op.weight >= 0.01 THEN op.weight * ocr.quantity
				        ELSE 0.25 * ocr.quantity
				    END) AS total_weight, oop.suborder_id
				FROM ".DB_PREFIX."order_product oop
					INNER JOIN ".DB_PREFIX."product as op ON op.product_id = oop.product_id
					INNER JOIN ".DB_PREFIX."return as ocr ON ocr.order_product_id = oop.order_product_id
				WHERE 
				    ocr.return_id IN (". implode(',',$return_ids) .")
				    AND ocr.active_row = 1
				GROUP BY oop.suborder_id
				";
		$result = $this->db->query($sql);
		
		$data = array();
		if($result->num_rows > 0){
			foreach ($result->rows as $row) {
				$data[$row['suborder_id']] = $row['total_weight'];
			}
		}
		return $data;
	}

	/**
	* @info: Public function to get total weight for given return Ids, 
	* to calculate reverse shipping suborder wise
	*@param: Return Ids, array
	*@return: Array
	*@author: Nishu, Sept 2017
	*/
	public function getTotalWeightForReverseShipping($return_ids){
		$sql  = "SELECT 
					sum(case
				        WHEN oop.weight_per_piece >= 0.01 THEN oop.weight_per_piece * ocr.quantity
				        WHEN op.weight >= 0.01 THEN op.weight * ocr.quantity
				        ELSE 0.25 * ocr.quantity
				    END) AS total_weight, oop.suborder_id
				FROM ".DB_PREFIX."order_product oop
					INNER JOIN ".DB_PREFIX."product as op ON op.product_id = oop.product_id
					INNER JOIN ".DB_PREFIX."return as ocr ON ocr.order_product_id = oop.order_product_id
				WHERE 
				    ocr.return_id IN (". implode(',',$return_ids) .")
				    AND
				    (
					  ocr.shipping_method != 'self_courier' 
					   	OR
					  ocr.return_shipment_tracking_id > 0
					)
				    AND ocr.active_row = 1
				GROUP BY oop.suborder_id
				";
		$result = $this->db->query($sql);
		
		$data = array();
		if($result->num_rows > 0){
			foreach ($result->rows as $row) {
				$data[$row['suborder_id']] = $row['total_weight'];
			}
		}
		return $data;
	}

	/**
	* Public function to get total weight Suborder wise
	*@param: Debit Note Ids, array
	*@return: Number
	*@author: Nishu, Sept 2017
	*/
	public function getTotalWeightForSuborder($order_id){
		$sql  = "SELECT 
					sum(case
				        WHEN oop.weight_per_piece >= 0.01 THEN oop.weight_per_piece * oop.quantity * oop.piece_in_set
				        WHEN op.weight >= 0.01 THEN op.weight * oop.quantity * oop.piece_in_set
				        ELSE 0.25 * oop.quantity * oop.piece_in_set
				    END) AS total_weight, oop.suborder_id
				FROM ".DB_PREFIX."order_product oop
					INNER JOIN ".DB_PREFIX."product as op ON op.product_id = oop.product_id
				WHERE 
				    oop.order_id = ". (int)$order_id ."

				GROUP BY oop.suborder_id
				";
		$result = $this->db->query($sql);
		
		$data = array();
		if($result->num_rows > 0){
			foreach ($result->rows as $row) {
				$data[$row['suborder_id']] = round($row['total_weight'], 2);
			}
		}
		return $data;
	}

	/**
	* Public function to get DN Nos by Return Ids
	*@param: Return Ids, array
	*@return: Number
	*@author: Commented by -Nishu, Sept 2017
	*/
	public function getDNsByReturn_ids($return_ids){
		// $data = array();
		// if(!empty($return_ids)){
		// 	$sql  = "
		// 			SELECT 
		// 				GROUP_CONCAT(DISTINCT dn.debit_note_no) AS debit_note_nos, ocr.return_id
		// 			FROM ".DB_PREFIX."return as ocr
		// 					LEFT JOIN 
		// 			".DB_PREFIX."seller_debit_note as dn ON dn.debit_note_id = ocr.debit_note_id 
		// 			WHERE 
		// 				ocr.return_id IN (". implode(',',$return_ids) .")
		// 			GROUP BY ocr.return_id
		// 			";
		// 	$result = $this->db->query($sql);			
		// 	if($result->num_rows > 0){
		// 		foreach ($result->rows as $row) {
		// 			$data[$row['return_id']] = $row['debit_note_nos'];
		// 		}
		// 	}		
		// }
		// return $data;
	}

	/**
	* public function to get return product details by master_return_id
	* @param: Return Ids , Comma seperated string
	* @return: array
	* @author: Nishu, Nov 2017
	*/
	public function getReturnProducts($return_ids){
		$resp = array();
		if(!empty($return_ids)){
			$sql = "SELECT 
					    ocr.return_id,
					    oop.order_product_id,
					    oop.product_id,
					    oop.seller_sku,
					    ((oop.price_per_piece + oop.discount_per_piece) * oop.output_tax_rates) / 100 as p_tax,
					    ocr.quantity,
					    oop.name as p_name,
					    oop.hsn_code,
					    oop.price_per_piece,
					    oop.discount_per_piece,
					    (oop.price_per_piece + oop.discount_per_piece) as p_price,
					    ocd.category_id,
					    GROUP_CONCAT(DISTINCT ocd.name) as cat_name,
					    orr.name as reason_name,
					    so.invoice_prefix,
						so.invoice_no
					FROM
					    ".DB_PREFIX."return as ocr
					        INNER JOIN
					    ".DB_PREFIX."return_reason as orr ON orr.return_reason_id = ocr.return_reason_id
					        INNER JOIN
					    ".DB_PREFIX."order_product as oop ON oop.order_product_id = ocr.order_product_id
						    INNER JOIN
	    				".DB_PREFIX."suborder as so ON so.order_id = oop.order_id
					        LEFT JOIN
					    ".DB_PREFIX."product_to_category as ptc ON ptc.product_id = oop.product_id
					        LEFT JOIN
					    ".DB_PREFIX."category_description as ocd ON ocd.category_id = ptc.category_id
					        AND ocd.language_id = 1
					WHERE
					    ocr.return_id IN (".$return_ids.") AND so.suborder_id = oop.suborder_id
					GROUP BY oop.order_product_id
				";
			$result = $this->db->query($sql);
			if($result->num_rows > 0){
				$resp = $result->rows;			
			}
		}
		return $resp;
	}

	/**
	* public function to get pickup-status Order Product
	* @param: order product ID
	* @return: String
	* @author: Nishu, Nov 2017
	*/
	public function getPickupStatusByOPId($opid){
		$sql = "
				SELECT pickup_status
				FROM ".DB_PREFIX."order_product
				WHERE 
					order_product_id = " . (int)$opid . "
			  ";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			return $result->row['pickup_status'];			
		}else{
			return '';
		}
	}
   
    /**
	* Public function to update credit note table
	* @param: $data array
	* @return: status
	* @author Nishu, Nov 2017
	*/
	public function updateCNByFieldList($data){
		//Mark Tentative Refund as Rejected
		$tr_data                = array();
		$tr_data['ref_id']      = $data['cn_id'];
		$tr_data['refund_type'] = "CREDIT_NOTE";

		//Get TentativeRefundId from Database
		TentativePaymentRefund::rejectTentativeRefundByRefIdAndType($this->db, $tr_data);

		//Update oc-credit_note with new values
		$sql = "UPDATE 
					".DB_PREFIX."credit_note 
				SET 
				    payment_cleared = '". $this->db->escape($data['payment_cleared']) ."'
			   ";

		foreach ($data['fields'] as $field_key => $value) {
			$sql .= ", " .$field_key." = '". $this->db->escape($value)."' ";
		}	

		$sql .= " WHERE credit_note_id = ". (int)$data['cn_id'];
		
		return $this->db->query($sql);
	}

	/**
	* Public function to get all return action reasons or specific action reason name
	* @param: $action_reason_id Optional
	* @return: String OR Array
	* @author: Nishu, Nov 2017
	*/
	public function getActionReason($action_reason_id = 0){
		if($action_reason_id != 0){
			$fields = "action_reason_name";
			$where = " AND id = ". (int)$action_reason_id;
		}else{
			$fields = "*";
			$where = "";
		}
		$sql = "
				SELECT ".$fields." 
				FROM ".DB_PREFIX."return_action_reasons
				WHERE 
					status = 1 AND language_id = 1 ". $where ."
			  ";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			if($action_reason_id != 0){
				return $result->row['action_reason_name'];
			}else{
				return $result->rows;
			}
		}else{
			return '';
		}
	}

	/**
	* Public function to get all return action reasons by parent action id
	* @param: $action_reason_id Optional
	* @return: String OR Array
	* @author: Nishu, Nov 2017
	*/
	public function getActionReasonByActionId($action_id = 0){
		$where = " AND FIND_IN_SET(". (int)$action_id .", parent_action_ids) ";
		$sql = "
				SELECT *
				FROM ".DB_PREFIX."return_action_reasons
				WHERE 
					status = 1 AND language_id = 1 ". $where ."
			  ";
		$result = $this->db->query($sql);	
		return $result->rows;
	}

	/**
     * Public function to get total number of rows or Data dynamically 
     * @info: This model to fetch data for those return based on return action changed
     * 1.: i.e. Return Action Date Filter will be work on :
     	   -retunr action changed first time for that particuler return action, 
     	   -not for return action date when return comment or qty is changed
	   2.: Debit Note Generated or not Filer is also  
     * @param: $data Array, type_flag String
     * @return: Number Or String
     * @author: Nishu, Nov 2017
	*/
	public function getAllReturns($data = array(), $type_flag = "count") {
		$select = " dn.debit_note_id, 
					dn.debit_note_prefix, 
					dn.debit_note_no,
					dn.debit_note_amount,
					dn.date_added,
					ocr.master_return_id,
					ocr.order_product_id,
					ocr1.return_added_date as return_requested_date, 
					oo.firstname,
					oo.lastname,
					oo.order_id,
					oo.order_no,
					oo.email,
					oo.telephone,
					oo.shipping_company,
					oo.shipping_city,

					rst.courier_company,
					rst.tracking_no,
					rst.date_added as shipping_date

					";

		$dn_condition = "";
		if(!empty( $data['filter_debit_note_no'] )){
			$dn_condition .= " AND dn.debit_note_no = '".$this->db->escape($data['filter_debit_note_no'])."'";
		}
		if(!empty( $data['filter_debit_note_date_from'] )){
			$dn_condition .= " AND dn.date_added >= '".$data['filter_debit_note_date_from']."'";
		}
		if(!empty( $data['filter_debit_note_date_to'] )){
			$dn_condition .= " AND dn.date_added <= '".$data['filter_debit_note_date_to']."'";
		}

		$whr = " WHERE ocr.return_id IS NOT NULL ";
		if(!empty( $data['filter_return_request_date_added_from'] )){
			$whr .= " AND ocr1.return_added_date >= '".$data['filter_return_request_date_added_from']."' ";
		}
		if(!empty( $data['filter_return_request_date_added_to'] )){
			$whr .= " AND ocr1.return_added_date <= '".$data['filter_return_request_date_added_to']." 23:59' ";
		}
		if(!empty( $data['filter_order_no'] )){
			$whr .= " AND oo.order_no LIKE '%".$this->db->escape($data['filter_order_no'])."%'";
		}
		if (!empty($data['filter_customer'])) {
			$whr .= " AND (CONCAT(oo.firstname, ' ', oo.lastname) LIKE '%" . $this->db->escape(trim($data['filter_customer'])) . "%'
							OR oo.email LIKE '%".$this->db->escape(trim($data['filter_customer']))."%'
							OR oo.telephone LIKE '%".$this->db->escape(trim($data['filter_customer']))."%'
						 ) ";
		}

		if (!empty($data['filter_return_replacement'])) {
			$whr .= " AND orr.reason_type LIKE '%" . $this->db->escape(trim($data['filter_return_replacement'])) . "%'";
		}


		if (!empty($data['filter_company'])) {
			$whr .= " AND oo.shipping_company LIKE '%" . $this->db->escape(trim($data['filter_company'])) . "%'";
		}

		if (!empty($data['filter_city'])) {
			$whr .= " AND oo.shipping_city LIKE '%" . $this->db->escape(trim($data['filter_city'])) . "%'";
		}

		if (!empty($data['filter_return_action'])) {
			$whr .= " AND ocr.return_action_id = '" . (int)$data['filter_return_action'] . "'";
		}

		$having_cnd = "";
		//Check for debit note is generated or not for that return
		if (isset($data['filter_dn_generated']) && $data['filter_dn_generated'] == 'YES'){
			//$having_cnd = ' HAVING dn_detail IS NOT NULL ';
			$having_cnd = ' HAVING debit_note_id IS NOT NULL ';
		}else if (isset($data['filter_dn_generated']) && $data['filter_dn_generated'] == 'NO'){
			//$having_cnd = ' HAVING dn_detail IS NULL ';
			$having_cnd = ' HAVING debit_note_id IS NULL ';
		} 

		$sql = "SELECT 
				   ".$select." 
				FROM
				    ".DB_PREFIX."return AS ocr
				INNER JOIN
				    ".DB_PREFIX."return_reason AS orr ON orr.return_reason_id = ocr.return_reason_id
				INNER JOIN
				    ".DB_PREFIX."order_product AS oop ON oop.order_product_id = ocr.order_product_id
				INNER JOIN
				    ".DB_PREFIX."order AS oo ON oo.order_id = oop.order_id
				INNER JOIN (
				    SELECT 
						MAX(return_id) AS return_id,
						MAX(debit_note_id) AS debit_note_id,
						MIN(date_added) AS return_added_date
					FROM 
						".DB_PREFIX."return 
					GROUP BY 
						order_product_id, master_return_id
				    ) AS ocr1 ON ocr1.return_id = ocr.return_id AND ocr.active_row = 1
				LEFT JOIN
				    ".DB_PREFIX."seller_debit_note AS dn ON dn.debit_note_id = ocr1.debit_note_id
				        AND dn.debit_note_status = 1

				LEFT JOIN
				    ".DB_PREFIX."return_shipment_tracking AS rst ON rst.shipping_id = ocr.return_shipment_tracking_id

				 ". $whr. $dn_condition ."
				GROUP BY ocr.master_return_id, ocr.order_product_id
				". $having_cnd ."
				ORDER BY ocr1.return_added_date DESC
				";
		if($type_flag != "count" && $type_flag != "download"){
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}
				if ($data['limit'] < 1) {
					$data['limit'] = 30;
				}
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}
		}
		$query = $this->db->query($sql);
		if($type_flag == "count"){
			return $query->num_rows;
		}else{
			return $query->rows;
		}
	}

	/**
	 * @info: Public method to get 
	*/
	public function getRetrunProductsAmount($returns){
		$data = array();
		if(empty($returns)){
			return $data;
		}
		foreach ($returns as $suborder_id => $value) {

			// (($value['price_per_piece'] + $value['discount_per_piece']) * $data[$value['order_product_id']]) * (1 + ($value['output_tax_rates'] / 100));



			$sql = "SELECT 
						sum(((oop.price_per_piece + oop.discount_per_piece)*ocr.quantity)* (1 + (oop.output_tax_rates / 100) ) )  as amt
					 FROM ".DB_PREFIX."order_product oop
					 	 INNER JOIN
					 ".DB_PREFIX."return as ocr ON ocr.order_product_id = oop.order_product_id
					 WHERE
					 	ocr.return_id IN (". implode(',', $value) .")
					 	AND oop.suborder_id = '". $this->db->escape($suborder_id) ."'
				  ";
			$result = $this->db->query($sql);
			if($result->num_rows > 0){
				$data[$suborder_id] = round($result->row['amt'], 2);
			}
		}
		return $data;
	}

	/**
	*@info: Public function to get all Return / Replacement Reasons for return_type
	*@param: $data
	*@return: Return Reason Array
	*@author: Nishu May 2018
	*/
	public function getReturnReasonsForConversion($post){
		$data = array();

		if(empty($post)){ return $data; }

		$sql  = "
		        SELECT *
		           FROM ".DB_PREFIX."return_reason
		        WHERE
		           status = 1
		        ";
		if(strtoupper($post['rtype']) == "RETURN"){
			$sql .= " AND reason_type = 'REPLACEMENT' ";
		}else{
			$sql .= " AND reason_type = 'RETURN' ";
		}
		
		$results = $this->db->query($sql);
		if($results->num_rows > 0){
			$data = $results->rows;	
		}

		return $data;
	}


	/**
	* Public function to get shipment(s) back to customer
	*@param: Order No
	*@return: array
	*@author: Nishu, August 2017
	*/
	public function getShipmentsBackToCustomer($order_id){
		
		$sql  = "SELECT 
					return_ids,
					shipping_id,
					courier_company,
					tracking_no,
					request_param,
					date_added,
					qty,
					value,
					equivalent_status,
					file_name,
					is_cancel,
					status as shipment_status
				FROM 
					".DB_PREFIX."return_shipment_backto_customer
				WHERE 
					order_id = '". (int)$order_id ."'
				";
		$results = $this->db->query($sql);
		if($results->num_rows > 0){
			return $results->rows;
		}
	}

	/**
	* Public function to get getTrackingCourierClass() to get courier class name
	*@param: String $courier_name
	*@return: String courier class name
	*@author: MSA, June 2018
	*/
	public function getTrackingCourierClass($courier_name)
	{
		$courier_name = str_replace(array(' ','-','_'), array('|','|','|'), $courier_name);
		$courier_name = explode('|',$courier_name);
		$sql  = "SELECT courier_name as courier_class ";  
		$sql .= "FROM ".DB_PREFIX."courier_partners ";
		$sql .= "WHERE ( is_reverse_shipment = 1 OR is_forward_shipment = 1 ) ";
		if(!empty($courier_name)) {
			$sql .= "AND ( ";
			$counter=1;
			foreach ($courier_name as $string) {
				$sql .= "courier_name LIKE '%".$this->db->escape($string)."%'  ";
				if($counter < count($courier_name)) {
					$sql .= "OR ";
				}
				$counter++;
			}
			$sql .= ") ";
		}
		$sql .= " LIMIT 1 ";
		$results = $this->db->query($sql);
		if($results->num_rows > 0){
			return trim($results->rows[0]['courier_class']);
		}		
	}

	/**
	* Public function to get getCourierCompanyTrackingUrls() to get shipment tracking url
	*@param: Integer $return_shipment_ids
	*@return: Array
	*@author: MSA, June 2018
	*/
	public function getCourierCompanyTrackingUrls($return_shipment_ids)
	{
		if(!empty($return_shipment_ids)) {

			$sql = "SELECT orst.tracking_no, 
						   ocp.tracking_url
					FROM ".DB_PREFIX."return_shipment_tracking as orst
					INNER JOIN ".DB_PREFIX."courier_partners as ocp ON ocp.courier_name = orst.courier_company 
					WHERE 
						orst.shipping_id IN (".implode(',', $return_shipment_ids).")
					";
			$results = $this->db->query($sql);
			if($results->num_rows > 0){
				return $results->rows;
			}		
		}
	}

	/**
	* Public function to get getShipmentTrackingDetails() to get shipment tracking details
	*@param: Integer $shipment_id
	*@return: Array
	*@author: MSA, June 2018
	*/
	public function getShipmentTrackingDetails($shipment_id, $shipment_type = 'reverse' )
	{
		if(!empty($shipment_id) && !empty($shipment_type)) {
			
			$table = ($shipment_type == 'reverse')?'return_shipment_tracking':'return_shipment_backto_customer';
			$sql = "SELECT courier_company,
						   tracking_no,
						   order_id,
						   order_no
					FROM " . DB_PREFIX . $table . "
					WHERE 
						shipping_id = '". (int)$shipment_id ."'
					";
			$results = $this->db->query($sql);
			if($results->num_rows > 0){
				return $results->row;
			}else{
				return array();
			}		
		}
	}

	/**
	 * @info: public Method to update existing return 
	 *          by its reason, quantity and shiping_method
	 * @author: Nishu, 2018
    */
    public function updateExistingReturn($data){
    	$sql = "
    			Update
    				".DB_PREFIX."return
    			SET 
    				internal_note = '". $this->db->escape('Edit Existing Return(Like return_reason OR Quantity).') ."',
    			    return_reason_id = ". (int)$data['return_reason'];

    	//If return_quantity is passed
    	if(!empty($data['return_quantity'])){
    		$sql .= ", quantity        = ". (int)$data['return_quantity'];
    	}

    	//If shipping_method is passed
    	if(!empty($data['shipping_method'])){
    		$sql .= ", shipping_method = '". $this->db->escape($data['shipping_method']). "' ";
    	}

    	//If comment is passed
    	if(!empty($data['comment'])){
    		$sql .= ", comment = '". $this->db->escape($data['comment']). "' ";
    	}

    	//Where clause
    	$sql .= "    
    			WHERE
    				return_id = ". (int)$data['last_return_id'] ."
    				AND active_row = 1
    		   ";
    		   
    	//Excute Query
    	$this->db->query($sql);

    }

    /**
	 * @info: public Method to get backto customer shipment details 
	 				 to generate shipping label pdf file
	 * @author: MSA, 2018
    */
    public function getBackToCustomerShipmentDetails($shipping_id)
    {

    	$sql = "
    			SELECT 
    				rsbc.return_ids,
    				rsbc.courier_company,
    				rsbc.tracking_no,
    				rsbc.date_added,
    				rsbc.order_id,
    				rsbc.order_no,
    				rsbc.weight,
    				rsbc.value,
    				rsbc.package_description,
    				rsbc.qty,
    				rsbc.request_param,
    				rsbc.shipping_details,
    				wa.warehouse_name,
    				wa.gstin,
    				wa.address_1,
    				wa.address_2,
    				wa.city,
    				wa.postcode,
    				wa.telephone,
    				wa.email,
    				o.alternate_contact_number
    			FROM 
    				".DB_PREFIX."return_shipment_backto_customer AS rsbc
    			INNER JOIN 
    				".DB_PREFIX."order as o ON o.order_id = rsbc.order_id
    			INNER JOIN 
    				".DB_PREFIX."warehouse_address AS wa ON wa.warehouse_id = rsbc.warehouse_id
    			WHERE
					rsbc.shipping_id = '".(int)$shipping_id."'
					AND
					is_cancel = 0	    					
    			";
    	$results = $this->db->query($sql);
		if($results->num_rows > 0){
			return $results->row;
		}else{
			return array();
		}
    }
   
   /**
	 * @info: public Method update custom DN Ref data
	 * @param: integer dn_id
	 * @param: string custom_debit_note_ref
	 * @author: MSA, 2018
    */
	public function updateCustomDNRef($dn_id, $custom_debit_note_ref)
	{ 
		if(!empty($dn_id)) {

			$custom_debit_note_meta = array();

			/* get DN data details*/ 
			$select_sql = "
						SELECT 
							`custom_debit_note_meta`
						FROM
							".DB_PREFIX."seller_debit_note
						WHERE
							debit_note_id = '".(int)$dn_id."'			
						";	
			$result = $this->db->query($select_sql);			
			if($result->num_rows) {
				$custom_debit_note_meta = unserialize($result->row['custom_debit_note_meta']);
			}

			$custom_debit_note_meta['custom_party']['invoice_ref']  = $custom_debit_note_ref;

			/*Update DN details for Custom DN Ref and DN Meta details*/
			$sql = "
					UPDATE 
						".DB_PREFIX."seller_debit_note
					SET 
						custom_debit_note_ref	= '".$this->db->escape($custom_debit_note_ref)."',
						custom_debit_note_meta 	= '".$this->db->escape(serialize($custom_debit_note_meta))."'
					WHERE
						debit_note_id = '".(int)$dn_id."'		
					";
			$this->db->query($sql);
		}
	} 				 

	/**
	 * @info: public Method update CN data for 
	 *	 		advance collected, cash discount and less cash discount
	 * @param: integer dn_id
	 * @param: string custom_debit_note_ref
	 * @author: MSA, 2018
    */
	public function updateCreditNoteFields(int $cn_id, array $fields): void
	{
		if( !empty($cn_id) && !empty($fields) ) {

			$fields_list = array();

				foreach ($fields as $key => $value) {
					$fields_list[] = "`". $key . "`" . " = " . $this->db->escape( $value ) ;
				}
			
			$fields_string = implode(', ', $fields_list);
			$sql = "
					UPDATE 
						".DB_PREFIX."credit_note
					SET
						".$fields_string."
					WHERE 	
						credit_note_id = ".(int) $cn_id ."
					" ;
			$this->db->query($sql);
		}
	}


}
