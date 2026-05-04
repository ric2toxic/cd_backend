 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionDnForSeller extends ReturnActionBase 
{	
	
	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry 	= $registry;
		
		if (method_exists($registry, 'get')) {
            $this->db 		= $registry->get('db');
            $this->load 	= $registry->get('load');
            $this->user   = $registry->get('user');
        } else {
            $this->db 		= $registry->db;
            $this->load 	= $registry->load;
            $this->user   = $registry->user;
        }
	}

	/**
	 * @info: Public Method to check given return data is for generating CN
	 *         Over-writing parent method
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function isCnGeneratable($return = array()){
		return true;
	}

	/**
	* @info: Public Method to Check return action is visible or not as post return action
	*         Default return value is : true
	* @param: $return_id Integer, $data Array
	* @return: Boolen
	* @author: Nishu, May 2018
	*/
	public function checkPostActionVisibility($return_id, $data, $return_action_id, $check_for){
		
		$is_visible = false;
    	if(!empty($return_id)){
    		$return  = $data['returns'][$return_id];
    		$op_id   = $return['order_product_id'];
    		$product = array();
    		if(isset($data['products'][$op_id])){
    			$product     = $data['products'][$op_id];
    			$suborder_id = $product['suborder_id'];
    			$suborder    = $data['suborder'][$suborder_id];
    			//$return_action_id     = $return['return_action_id'];
    			$return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;
    			if(
    				$return_action_status == 1
    					&&
    				isset($product['seller_invoice_id']) 
    					&&
    				$product['seller_invoice_id'] > 0
    					&&
    				empty($return['debit_note_id'])
    					&&
    				(
    					(
    					!empty($product['buyer_invoice_id'])
    						&&
    					$suborder['order_status_id'] != ORDER_STATUS['Canceled']
    						&&
    					!empty($return['credit_note_id'])
    					)
    						||
    					in_array($suborder['order_status_id'], array(
    															ORDER_STATUS['Processed'],
    															ORDER_STATUS['Tentative Processed']
    															)
    							)
    				)
    			){
    				$is_visible = true;
    			}
    		}
    	}
    	return $is_visible;
    }

	/**
	 * @info: Public Method to send email
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function sendEmail($data = array()) {

		if(!empty($data)) {
			$mailer   = new ActionEmailer($this);
			$subject = 'WholesaleBox :: Debit Note '.$data['debit_note_prefix'].$data['debit_note_no'].' Generated due to Returns in Order No '.$data['order_no'];
			$data['additional_emails'][] = 'returns';
			
			$mailer->emailSetting(
								array(
									'subject' 	=> $subject,
									'to'		=> $data['email'],
									'cc'        => $data['additional_emails'],
								    'template'	=> '',
								    'html'		 => $this->seller_email_template($mailer, $data),
								    'attachments'=> !empty($data['attachments'])? $data['attachments']:''
									)
								);
			//Send Mail
			$mailer->send();
		}
	}

	private function seller_email_template($mailer, $data) {

		$_html  = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
		$_html .= $mailer->getHeaderText($data['company']);
		$_html .= '<br>';
		$_html .= '<p>Debit Note <b>'.$data['debit_note_prefix'].$data['debit_note_no'].'</b>, dated <b>'.date('d-M-Y', strtotime($data['debit_note_date'])).'</b>, amounting to <b>&#x20b9;'.$data['debit_note_amount'].'</b>, for the Order No <b>'.$data['order_no'].'</b>, has been generated against the returns. For your reference, we have attached the debit note in pdf format here.</p>';
	
		$_html .= '<p>Debit note has been generated post our internal review of the returns received from the customer. In previous email, details of the items in-process of being returned, has already been updated to you. Goods with physical debit note copy, shall be handed over to you, by our pickup person, in few working days. Payment against this debit note, will be debited accordingly, based on the payment status of invoice against this debit note.</p> ';
		
		$_html .= $mailer->getSellerFooterText();
		$_html .= '</div>';

		return $_html;
	}

}//End of Class
