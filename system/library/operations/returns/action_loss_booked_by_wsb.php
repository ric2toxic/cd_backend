 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionLossBookedByWSB extends ReturnActionBase 
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

	public function isReturnEditable(){
    	return false;
    }

    /**
	* @info: Public Method to Check return action is visible or not as post return action
	*         Default return value is : true
	* @param: $return_id Integer, $data Array
	* @return: Boolen
	* @author: Nishu, May 2018
	*/
	public function checkPostActionVisibility($return_id, $data, $return_action_id, $check_for){
		if($check_for == 'admin_mode'){
			return true;
		}
		
    	$is_visible = false;
    	if(!empty($return_id)){
    		$return  = $data['returns'][$return_id];
    		$op_id   = $return['order_product_id'];
    		$dn_id   = $return['debit_note_id'];

    		//$return_action_id     = $return['return_action_id'];
    		$return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;

    		if(
    			$return_action_status == 0
    				||
    			(
	    			isset($data['all_debit_notes'][$dn_id])
	    				&&
	    			!empty($data['all_debit_notes'][$dn_id]['custom_id'])
	    				&&
	    			$data['all_debit_notes'][$dn_id]['debit_note_status'] == 1
	    		)
    		){
    			return $is_visible;
    		}

    		$product = array();
    		if(isset($data['products'][$op_id])){
    			$product     = $data['products'][$op_id];
    			$suborder_id = $product['suborder_id'];
    			$suborder    = $data['suborder'][$suborder_id];
    			if(
    				$return_action_status == 1
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
	   					empty($product['buyer_invoice_id'])
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
			$mailer = new ActionEmailer($this);
			$mailer->emailSetting(
								array(
									'subject' 	=> 'Return request for Order No '.$data['order_no'].' - Status Updated: Loss Booked By WSB',
									'to'		=> array('returns', 'accounts', 'vikas'),
									'template'	=> '',
								    'html'		 => $this->email_template($mailer, $data),
								    'attachments'=> ''
									)
								);
			$mailer->send();
		}
	}

	/**
	 * @info: Public Method to get template for customer
	 * @param: Object ActionMailerClass object
	 * @param: Array $mail_data
	 * @return: String email template
	 * @author: Nishu, March 2018
	*/
	public function email_template($mailer, $data)
	{
		$_html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
		$_html .= $mailer->getHeaderText('All');
		$_html .= '<p>';
		$_html .= 'Loss booked by WSB for Order No '.$data['order_no'].'., ';
		$_html .= 'Please find below details:';
		$_html .= '</p>';
		$_html .= '<p>Products:</p>';
		$_html .= $mailer->getProductList($data);
		$_html .= '<br><br><p>By Returns</p>';
		$_html .= '</div>';
		
		return $_html;
	}
	
}//End of Class
