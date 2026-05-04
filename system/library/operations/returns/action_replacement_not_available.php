 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionReplacementNotAvailable extends ReturnActionBase 
{	
	protected $action_name 	  = 'REPLACEMENT NOT AVAILABLE';

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
        if($check_for == 'admin_mode'){
            return true;
        }
		
    	$is_visible = false;
    	if(!empty($return_id)){
    		$return  = $data['returns'][$return_id];
    		//$return_action_id     = $return['return_action_id'];
    		$return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;
    		
			if( 
				$return_action_status == 1
    				&&
    			empty($return['credit_note_id']) 
    				&& 
    			empty($return['debit_note_id'])
    		){
				$is_visible = true;
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

	/*Check invoice status and buyer invoice id to send email to customer,  */	
		 if( !empty($data['invoice_status']) )	{ 
		 	$this->emailToCustomer($data);
		 }
		
		//$this->emailToSeller($data);
	}

	/**
	 * @info: Public Method to send email
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function emailToCustomer($data)
	{
		if(!empty($data))
		{
			$mailer = new ActionEmailer($this);
			$mailer->emailSetting(
								array(
									'subject' 	  => 'Replacement request for Order No '.$data['order_no'].' - Status Updated: ' . $this->action_name,
									'to'		  => $data['customer']['email'],
									'cc'		  => '',
									'template'	  => '',
								    'html'		  => $this->customer_email_template($mailer, $data),
								    'attachments' => ''
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
	private function customer_email_template($mailer, $data)
	{
		$_html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
			$_html .= $mailer->getHeaderText($data['customer']['name']);
			
				$_html .= '<p>';
					$_html .= 'There is an update against your Return/Replacement request for the Order No '.$data['order_no'].'. ';
					$_html .= 'Status of some product(s) has been updated to '.$this->action_name.'. ';
					$_html .= 'Please find below details:';
				$_html .= '</p>';
			$_html .= '<p>Products:</p>';
			$_html .= $mailer->getProductList($data);

			$_html .= '<p>A refund shall be initiated in a few working days.</p>';
			$_html .= "<p>If you haven't added bank details then please add. Go to Wholsalebox Web/App - Login - My Account - Bank Details.</p>";

		$_html .= $mailer->getFooterText();
		$_html .= '</div>';
		
		return $_html;
	}

	/**
	 * @info: Public Method to get SMS template for customer
	 * @param: Array $data
	 * @return: String SMS template
	 * @author: Nishu, March 2018
	*/
	private function customer_sms_template($data)
	{
		$_message = '';

		if(empty($data)) { return $_message;}

		$customer_name 		= isset($data['customer']['name'])?$data['customer']['name']:'';
		$order_no 			= isset($data['order_no'])?$data['order_no']:'';
		$number_of_pieces 	= isset($data['return_quantity'])?$data['return_quantity']:'';
		$url 		   		= isset($data['url'])?$data['url']:'';

		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Replacement_Not_Available']],
							$number_of_pieces,
							$order_no,
							$url,
							RETURN_HELPLINE
						);
		return $_message;
	}

	/**
	 * @info: Public Method to send push notification
	 * @param: Array $data
	 * @return: Void
	 * @author: Nishu, March 2018
	*/
	public function sendPushNotification($data = array())
	{
		if(empty($data) ||  empty($data['invoice_status'])) { return false; }

		$this->customer_notification($data);
	}

	/**
	 * @info: Public Method to send customer push notification
	 * @param: Array $data
	 * @return: Void
	 * @author: Nishu, March 2018
	*/
	public function customer_notification($data)
	{
		if(empty($data) || empty($data['customer_id'])){ return true; }

		$customer_id 		= $data['customer_id'];

		$master_return_id 	= array_unique(array_column($data['returns'], 'master_return_id'));

		$order_no 		= $data['order_no'];
		$telephone		= $data['customer']['phone'];
		$customer_name 	= $data['customer']['name'];
		
		$data['url'] = $this->getReturnUrl(
											array(
												'order_id'         => $data['order_id'],
												'master_return_id' => (count($master_return_id)==1)?$master_return_id[0]:'',
												'customer_id'      => $customer_id
											)
										);
		
		$sms_template = $this->customer_sms_template($data);

		$title    = $order_no. ' - Return Update'; 
		$message  = 'Replacement is not available for your Order No '.$order_no.'.';

		$push_data = array();
		$push_data[] = array(
			'type'				=> 'customer',
			'id'				=> $customer_id,
			'order_no'			=> $order_no,
			'master_return_id'	=> (count($master_return_id)==1)?$master_return_id[0]:'',
			'title'				=> $title,
			'message'			=> $message,
			'sms'				=> array(
											'mobile' 	=> $telephone,
											'message' 	=> $sms_template,
										)
		);

		$this->queuePushNotification($push_data);

		/*After sending push notification, generate reply help desk ticket*/
		$master_ids = array_combine(array_column($data['returns'], 'master_return_id'), 
									array_column($data['returns'], 'helpdesk_ticket_id')
									);
		$data['ticket_data'] 	= array(
			'customer_id'	=> $customer_id,
			'master_ids' 	=> $master_ids,
			'title'			=> $title,
			'description'	=> $sms_template
		);
		$this->sendToHelpDesk($data);
	}


}//End of Class
