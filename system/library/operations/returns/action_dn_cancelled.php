 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionDnCancelled extends ReturnActionBase 
{	
	protected $action_name 	  = 'Debit note cancelled';

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
		if(!empty($data)) {
			$mailer = new ActionEmailer($this);
			$subject = 'WholesaleBox :: Debit Note '.$data['debit_note_prefix'].$data['debit_note_no'].' Cancelled for Order No '.$data['order_no'];
			$data['additional_emails'][] = 'returns';
			
			$mailer->emailSetting(
								array(
									'subject' 	=> $subject,
									'to'		=> $data['email'],
									'cc'        => $data['additional_emails'],
									'template'	=> '',
								    'html'		 => $this->seller_email_template($mailer, $data),
								    'attachments'=> $data['attachments']
									)
								);
			//Send Mail
			$mailer->send();
			
		}
	}

	/**
	 * @info: Public Method to get template for seller
	 * @param: Object ActionMailerClass object
	 * @param: Array $data
	 * @return: String email template
	 * @author: Nishu, March 2018
	*/
	private function seller_email_template($mailer, $data)
	{
		$_html  = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
		$_html .= $mailer->getHeaderText($data['company']);
		$_html .= '<br>';
		$_html .= '<p>We have cancelled Debit Note <b>'.$data['debit_note_prefix'].$data['debit_note_no'].'</b>, dated <b>'.date('d-M-Y', strtotime($data['debit_note_date'])).'</b>, amounting to <b>&#x20b9;'.$data['debit_note_amount'].'</b>, for the Order No <b>'.$data['order_no'].'</b>. For your reference, we have attached the canceled debit note in pdf format here.</p> <br>';
	
		$_html .= '<p>Payment, if debited already, will be refunded back, in the next few working days.</p> ';
		
		$_html .= $mailer->getSellerFooterText();
		$_html .= '</div>';
		
		return $_html;
	}

	/**
	 * @info: Public Method to send push notification
	 * @param: Array $data
	 * @return: Void
	 * @author: Nishu, June 2018
	*/
	public function sendPushNotification($data = array())
	{
		if(empty($data)) { return false; }

		$this->seller_notification($data);
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

		$master_return_id 	= !empty($data['master_return_id'])?$data['master_return_id']:'';
		$return_id 			= !empty($data['return_id'])?$data['return_id']:'';
		$order_no 			= !empty($data['order_no'])?$data['order_no']:'';
		$order_id 			= !empty($data['order_id'])?$data['order_id']:'';
		
		if(!empty($order_no))
		{
			$message = array(
		           'custom_notification' => array
								           (
								               'sound' 					=> 'default',
								               'title'     				=> 'Return goods has been rejected for Order No ' . $order_no,
								               'body'    				=> 'Return goods has been rejected for Order No ' . $order_no,
								               'large_icon' 			=> 'https://cdnimages.net/img/wsbseller.png',
								               'order_id' 				=> $order_id,
								               'order_no' 				=> $order_no,
								               'return_id' 				=> $return_id,
								               'master_return_id' 		=> $master_return_id,
								               'priority'      			=> 'high',
								               'show_in_foreground' 	=> true,
								               'type' 					=> 'PickUpOrder' // user will be redirected to order detail page
								           ),
		           'order_id' 			=> $order_id,
		           'order_no' 			=> $order_no,	
		           'return_id' 			=> $return_id,
		           'master_return_id' 	=> $master_return_id,
		           'priority'      		=> 'high',
		           'show_in_foreground' => true,
		           'type' 				=> 'Return' // user will be redirected to order detail page
		       ); 

			// send notification
	        //$notification = new Notification($value['ws_gcm_registration_id'], $message);
	        //$notification->sendPushNotificationToSellers();
		}
		
	} // end of seller push notification
	


}//End of Class
