 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionPending extends ReturnActionBase 
{	
	protected $action_name 	  = 'PENDING';
	
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
	 * @info: Public Method to send email
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function sendEmail($data = array()) {
		if(!empty($data))
		{
			$mailer = new ActionEmailer($this);
			$mailer->emailSetting(
								array(
									'subject' 	  => 'Return/Replacement request for Order No '.$data['order_no'].' - Status Updated: ' . $this->action_name,
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
	 * @info: Public Method create customer email template
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
					$_html .= 'There is an update against your Return/Replacement request for the Order No '.$data['order_no'].'. ';
					$_html .= 'Your return request has been received for following products. ';
				$_html .= '</p>';
			$_html .= $mailer->getProductList($data);
			
			$_html .= '<p>Please wait for 48 hours to get next status about your return/replacement. Kindly pack the returned products and keep ready, if you have opted for Wholesalebox pick up. </p>';	
		$_html .= $mailer->getFooterText();
		$_html .= '</div>';
		return $_html;
	}

	/** 
	 * @info: Public Method create customer SMS template
	 * @param: $data Array
	 * @return: String SMS template
	 * @author: Nishu, March 2018
	*/
	public function customer_sms_template($data)
	{
		$url 				= !empty($data['url'])?$data['url']:'';
		$order_no 			= $data['order_no'];

		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Pending']],
							$order_no,
							$url,
							RETURN_HELPLINE
						);
		return $_message;
	}

	/**
	 * @info: Public Method send push notifications
	 * @param: $data Array
	 * @return: Void
	 * @author: Nishu, March 2018
	*/
	public function sendPushNotification($data = array())
	{
		if(empty($data) || empty($data['customer_id'])){ return true; }

		$return_quantity = 0;

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
		$message = 'Your return request has been received for Order No '.$order_no.'. ';

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


		/*After sending push notification, generate help desk ticket*/
		
		$master_ids = array_combine(array_column($data['returns'], 'master_return_id'), 
									array_column($data['returns'], 'helpdesk_ticket_id')
									);	

		$data['ticket_data'] 	= array(
			'action'		=> 'pending',
			'customer_id'	=> $customer_id,
			'master_ids' 	=> $master_ids,
			'title'			=> $title,
			'description'	=> $sms_template
		);

		$this->sendToHelpDesk($data);

	}


}//End of Class
