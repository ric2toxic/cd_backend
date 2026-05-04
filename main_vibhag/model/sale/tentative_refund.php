<?php
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/bank_transfer.php');
class ModelSaleTentativeRefund extends Model {

	/**
     * Public function to get total number of rows or Data dynamically, for tentative refunds 
     * @param: $data Array, type_flag String
     * @return: Number Or String
     * @author: Nishu, Nov 2017
	*/
	public function getAllTentativeRefunds($data = array(), $type_flag = "count") {
		if($type_flag == "count"){
			$select = " oo.order_id ";
		}else{
			$select = " tr . *,
					    oo.email,
					    oo.telephone,
					    oo.shipping_company,
					    oo.shipping_city,
					    CONCAT(oo.firstname,' ', oo.lastname) as customer,
					    c.bank_ac_holder_name,
					    c.bank_ac_number,
					    c.ifsc_code,
					    cn.date_added as cn_date";
		}
		
		$inner_join = '';
		
		$whr = " WHERE 1 = 1 ";
		
		if(!empty( $data['filter_order_no'] )){
		  $whr .= " AND oo.order_no LIKE '%".$this->db->escape($data['filter_order_no'])."%'";
		}
		
		if (isset($data['filter_is_approved']) && $data['filter_is_approved'] != '-1') {
		  $whr .= " AND tr.is_approved = '" . (int)$data['filter_is_approved'] . "'";
		}

		if (isset($data['filter_refund_type']) && $data['filter_refund_type'] != 'all') {
		 $whr .= " AND tr.refund_type = '".$this->db->escape($data['filter_refund_type'])."'";
		}
		
		if (isset($data['filter_cn_date_from'])) {
		 $whr .= " AND cn.date_added >= '".$this->db->escape($data['filter_cn_date_from'])."'";
		}
		if (isset($data['filter_cn_date_to'])) {
		 $whr .= " AND cn.date_added <= '".$this->db->escape($data['filter_cn_date_to'])." 23:59'";
		}

		if (isset($data['filter_refund_date_from'])) {
		 $whr .= " AND tr.date_added >= '".$this->db->escape($data['filter_refund_date_from'])."'";
		}
		if (isset($data['filter_refund_date_to'])) {
		 $whr .= " AND tr.date_added <= '".$this->db->escape($data['filter_refund_date_to'])." 23:59'";
		}
		
		$sql = "SELECT 
				   ".$select." 
				FROM
				    ".DB_PREFIX."tentative_refund as tr
				        INNER JOIN
				    ".DB_PREFIX."order as oo ON oo.order_id = tr.order_id
				    	INNER JOIN
				    ".DB_PREFIX."customer as c ON c.customer_id = oo.customer_id
				    	LEFT JOIN ".
				    DB_PREFIX."credit_note as cn
		 				ON cn.credit_note_id = tr.ref_id AND tr.refund_type = 'CREDIT_NOTE'
			    ". $whr ;
		$sort_data = array(
						'oo.order_no',
						'oo.firstname',
						'oo.email',
						'oo.shipping_company',
						'oo.shipping_city',
					   );
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY oo.order_id ";
		}
		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
		}
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
		}else if($query->num_rows > 0){
			$return_data = array();
			$return_data['refunds'] = $query->rows;
			$order_ids = array_unique(array_column($query->rows, 'order_id'));
			$total_invoice = array();
			$cn_amount     = array();
			
			foreach ($order_ids as $order_id) {
				$selector = array(
                 'suborder' => array( 'select' => array('suborder_id', 'order_status_id')
	            ));
	            $result = OrderInfo::getOrderInfo($this->db, $order_id, '', $selector);

	            //Initialize $total_invoice with $order_id as key
	            $total_invoice[$order_id] = $total_invoice[$order_id] ?? 0;

	            foreach ($result['suborder'] as $suborder_id => $value) {

	            	//Check for suborder must not be cancelled
	            	if($value['order_status_id'] != 2){
						$this->db->query("CALL calculateTotalInvoiceAmount('" . $this->db->escape($suborder_id) . "', '" . (int)1 . "', @total_invoice_amount)");
    					$suborder_invoice_total = (float) $this->db->query("SELECT @total_invoice_amount")->row['@total_invoice_amount'];
    					
    					$total_invoice[$order_id] += $suborder_invoice_total;
	        			
					}else{
						$total_invoice[$order_id] += 0;
					}
	            }

	            $return_data['cn_amount'][$order_id] = $this->getCNAmtByOrderId($order_id);
	            $return_data['payment'][$order_id]   = $this->getPaymentAgainstOrderId($order_id);
			}
			$return_data['total_invoice'] = $total_invoice;
			
			return $return_data;
		}else{
			return array();
		}
	}

	/**
     * Public function to get CreditNote value order wise
     * @param: $order_id Integer
     * @return: $amount Float
	 * @author: Nishu, Dec 2017
	*/
	public function getCNAmtByOrderId($order_id='')
	{
		$sql = "SELECT SUM(cn.credit_note_amount) as cn_amount 
				FROM ".DB_PREFIX."credit_note as cn
				   INNER JOIN
				".DB_PREFIX."suborder as so ON so.order_id = cn.order_id
				WHERE 
				 so.suborder_id = cn.suborder_id
				   AND cn.credit_note_status = 1 
				   AND so.order_status_id > 0 
				   AND so.order_status_id != 2 
				   AND so.invoice_no > 0 
				   AND so.buyer_invoice_id > 0
				   AND cn.order_id = '". (int)$order_id ."'
			   ";

		$result = $this->db->query($sql);
		$amt = 0;
		if($result->num_rows > 0){
			$amt = (float)$result->row['cn_amount'];
		}
		return $amt;
	}
	/**
     * Public function to get totalpaid amt and refund amt against order_id
     * @param: $order_id Integer
     * @return: $data Array
	 * @author: Nishu, Dec 2017
	*/
	public function getPaymentAgainstOrderId($order_id='')
	{
		$sql = "SELECT 
		          SUM(IF(amount >0, amount, 0)) as amount,
		          SUM(IF(amount < 0, amount, 0)) as refund
				FROM ".DB_PREFIX."order_payment				   
				WHERE 
				 successfull = 1
				   AND bank_transfer_mode NOT IN ('cheque_deposited', 'cheque_failed')
				   AND order_id = '". (int)$order_id ."'
			   ";

		$result = $this->db->query($sql);
		$data = array();
		$data['amount'] = (float)$result->row['amount'];
		$data['refund'] = (float)$result->row['refund'];
		return $data;
	}


	public function updateIsApprovedTentativeRefund($data){
		if(empty($data['id'])){
			return false;
		}
		$rejected_comment = '';
		if(!empty($data['comment'])){
		 $rejected_comment = " rejected_comment = '".$this->db->escape($data['comment'])."', ";
		}
		//Update Tentative Refund status
		$sql = "UPDATE ".DB_PREFIX."tentative_refund 
			     SET 
			        is_approved     = '". (int)$data['is_approved'] ."',
			        ". $rejected_comment ." 
			        user_id         = '". (int)$this->user->getId() ."',
			        updated_by_user = '". $this->db->escape($this->user->getUserName()['name']) ."'
			     WHERE 
			     	id = '". (int)$data['id'] ."'
			   ";
		//Execute Refund
		$this->db->query($sql);

		//If Tentative refund is marked as Rejected
		if($data['is_approved'] == 2){
			$select_sql = "SELECT * FROM ".DB_PREFIX."tentative_refund 
			        WHERE id = '". (int)$data['id'] ."'
			       ";
			$result = $this->db->query($select_sql);
			$result_data = $result->row;

			//Check If Tentative refund is for CN or Excess Payments
			if($result_data['refund_type'] == 'CREDIT_NOTE'){
				$this->markCnRefundNotApplicable($result_data);
			}else{
				$mark_not_applicable_sql = "
                                             UPDATE ".DB_PREFIX."order
                                               SET
                                                  payment_cleared = 'NOT_APPLICABLE'
                                               WHERE
                                                  order_id = '". (int)$result_data['order_id'] ."'
				                           ";
				//Execute query to mark not_applicable
				$this->db->query($mark_not_applicable_sql);
			}

			//Send Mail for Rejected tentative refunds
			$this->sendrefundRejectedMail($result_data);
		}
	}

	public function markCnRefundNotApplicable($data){
		$mark_not_applicable_sql = "
                                    UPDATE 
                                    	".DB_PREFIX."credit_note
                                    SET
                                        payment_cleared = 'NOT_APPLICABLE'
                                    WHERE
                                        credit_note_id = '". (int)$data['ref_id'] ."'
		                           ";
		//Execute query to mark not_applicable
		$this->db->query($mark_not_applicable_sql);
	}

	/**
	* Public function to send internal mail for rejecting tentative refunds
	* @param: $data Array
	* @return: void
    * @author: Nishu, Dec 2017
	*/
	public function sendrefundRejectedMail($data){
		$html = MailTemplate::mailRejectedRefundsHTML($data);
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');

        $mail->setFrom(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addReplyTo(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->addCC(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->Subject = 'Tentative Refund Rejected (' . date("d/F/Y H:i:s") . ')';
        $mail->msgHTML($html);

        $mail->send();
	}

}