 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionReturnGoodsRejected extends ReturnActionBase 
{	
	
	protected $action_name = "Return Goods Rejected";

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
	 * @info: Public Method to send email
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function sendEmail($data = array()) {
		
		/*Check invoice status and buyer invoice id to send email to customer,  */	
		 if( !empty($data['invoice_status']) )	
		 { 
		 	$this->emailToCustomer($data);
		 }

		$this->emailToSeller($data);
	}

	/**
	 * @info: Public Method to send customer email
	 * @param: $data Array
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
									'subject' 	  => 'Return request for Order No '.$data['order_no'].' - Status Updated: '.$this->action_name.' ',
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
	 * @info: Public Method to send seller email
	 * @param: $data Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function emailToSeller($data)
	{	
		if(empty($data)){ return true; }

		$seller_data = $this->getSellerEmailData($data);
		
		if(!empty($seller_data))
		{
			foreach ($seller_data as $key => $value) 
			{
				$seller_additional_emails = array();

				if(!empty($value['seller']['additional_emails'])) {
					$seller_additional_emails = array_values($value['seller']['additional_emails']);
				}
				$mailer = new ActionEmailer($this);
				$mailer->emailSetting(
									array(
											'subject' 	  => 'WholesaleBox :: Return request for Order No '.$value['order_no'].' - Status Updated: Rejected',
											'to'		  => $value['seller']['email'],
											'cc'		  => $seller_additional_emails,
											'template'	  => '',
										    'html'		  => $this->seller_email_template($mailer, $value),
										    'attachments' => ''
										)
									);
				$mailer->send();
			} 
		} 	
	}

	/**
	 * @info: Public Method to send customer email
	 * @param: $mailer Object
	 * @param: $data Array
	 * @return: String email template
	 * @author: Nishu, March 2018
	*/
	public function customer_email_template($mailer, $data)
	{
		$_html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
			$_html .= $mailer->getHeaderText($data['customer']['name']);
				$_html .= '<p>';
					$_html .= 'There is an update against your Return/Replacement request for the Order No '.$data['order_no'].', ';
					$_html .=  $this->action_name.'. ';
					$_html .= 'Please find below details:';
				$_html .= '</p>';
			$_html .= '<p>Products:</p>';
			$_html .= $mailer->getProductList($data);
		$_html .= $mailer->getFooterText();
		$_html .= '</div>';
		return $_html;
	}

	/**
	 * @info: Public Method to send seller email
	 * @param: $mailer Object
	 * @param: $data Array
	 * @return: String email template
	 * @author: Nishu, March 2018
	*/
	public function seller_email_template($mailer, $data)
	{
		$_html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
			$_html .= $mailer->getHeaderText($data['seller']['name']);
				$_html .= '<p>';
					$_html .= 'We have rejected the return request from the customer, for the Order No '.$data['order_no'].'. ';
					$_html .= 'In previous emails, tentative details of the return request received from the customer, were already informed to you. 
								Please find below details of the rejected return request items.';
				$_html .= '</p>';
			$_html .= $mailer->getSellerProductList($data);
		$_html .= $mailer->getSellerFooterText();
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

		$url 				= isset($data['url'])?$data['url']:'';
		$order_no 			= $data['order_no'];
		$number_of_pieces 	= isset($data['return_quantity'])?$data['return_quantity']:'0';

		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Return_Goods_Rejected']],
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
		if(empty($data)) { return false; }

		$this->customer_notification($data);

		$this->seller_notification($data);
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
		$return_quantity= 0;

		foreach ($data['returns'] as $key => $value)
		{
			$return_quantity += $value['return_quantity'];
		}
		
	    $data['return_quantity'] = $return_quantity;
	    $data['url']			 = $this->getReturnUrl(
	    											array(
														'order_id'         => $data['order_id'],
														'master_return_id' => (count($master_return_id)==1)?$master_return_id[0]:'',
														'customer_id'      => $customer_id
													)
												);

		$sms_template = $this->customer_sms_template($data);

		$title   = $order_no. ' - Return Update'; 
		$message = 'Your return request has been rejected for Order No '.$order_no.'. ';

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

	/**
	 * @info: Public Method to send seller push notification
	 * @param: Array $data
	 * @return: Void
	 * @author: Nishu, June 2018
	*/
	public function seller_notification($data)
	{
		if(empty($data)){ return true; }

		// get Seller wise returns data
		$returns_data = $this->getSellerEmailData($data); 

		$message = array();
		
		if(!empty($returns_data)) {
			
			foreach ($returns_data as $seller_id => $seller_returns) {
				
				if(!empty($seller_returns['seller']['ws_gcm_registration_id']) && !empty($seller_returns['returns']))
				{
					$order_id = $seller_returns['order_id'];
					$order_no = $seller_returns['order_no'];
					$return_ids = array_keys($seller_returns['returns']);
					$master_return_id = array_unique(array_column($seller_returns['returns'], 'master_return_id'));
						$message = array(
				           'custom_notification' => array
										           (
										               'sound' 					=> 'default',
										               'title'     				=> 'Return goods has been rejected for Order No ' . $order_no,
										               'body'    				=> 'Return goods has been rejected for Order No ' . $order_no,
										               'large_icon' 			=> 'https://cdnimages.net/img/wsbseller.png',
										               'order_id' 				=> $order_id,
										               'order_no' 				=> $order_no,
										               'return_id' 				=> implode(',', $return_ids),
										               'master_return_id' 		=> implode(',', $master_return_id),
										               'priority'      			=> 'high',
										               'show_in_foreground' 	=> true,
										               'type' 					=> 'PickUpOrder' // user will be redirected to order detail page
										           ),
				           'order_id' 			=> $order_id,
				           'order_no' 			=> $order_no,
				           'return_id' 			=> implode(',', $return_ids),
				           'master_return_id' 	=> implode(',', $master_return_id),
				           'priority'      		=> 'high',
				           'show_in_foreground' => true,
				           'type' 				=> 'Return' // user will be redirected to order detail page
				       ); 
					// send notification
			        //$notification = new Notification($value['seller']['ws_gcm_registration_id'], $message);
			        //$notification->sendPushNotificationToSellers();

				} // end of second if condition checking 

			} // end of foreach

		} // end of first if condition checking

	} // end of seller push notification


}//End of Class
