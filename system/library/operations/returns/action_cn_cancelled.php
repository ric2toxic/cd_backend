 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionCnCancelled extends ReturnActionBase 
{	
	
	protected $action_name 	  = 'CREDIT NOTE CANCELLED';

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
	 * @info: Public Method to send email
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function sendEmail($data = array()) {
		if(!empty($data)) {
			$mailer  = new ActionEmailer($this);
			$subject = "Return request for Order No ".$data['order_no']." - Status Updated: ".$this->action_name." ";
			$mailer->emailSetting(
								array(
									'subject' 	  => $subject,
									'to'		  => $data['customer']['email'],
									'bcc'         => '', //array('vikas', 'accounts', 'returns'),
									'template'	  => '',
								    'html'		  => $this->customer_email_template($mailer, $data),
								    'attachments' => $data['attachments']
									)
								);
			//Send Mail
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
		$_html .= '<p>We have cancelled Credit Note against your Return/Replacement request for Order No '.$data['order_no'].'. </p>';
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

		$order_no 	   = isset($data['order_no'])?$data['order_no']:'';
		$cn_no  	   = isset($data['cn_no'])?$data['cn_no']:'';
		$cn_amount     = isset($data['cn_amount'])?$data['cn_amount']:'0';
		$url 		   = isset($data['url'])?$data['url']:'';

		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Cancel_CN']],
							$cn_no,
							$cn_amount,
							$order_no,
							$url,
							RETURN_HELPLINE
						);

		return $_message;
	}

	public function sendPushNotification($data = array()) {

		if(empty($data['customer_id'])){ return false; }

		$customer_id = isset($data['customer_id'])?$data['customer_id'] : 0;
		
		$order_no    = isset($data['order_no'])?$data['order_no'] : 0;

		$telephone	 = isset($data['customer']['phone'])?$data['customer']['phone']:'';

		$data['url'] = $this->getReturnUrl(
									array(
										'order_id'         => $data['order_id'],
										'master_return_id' => '',
										'customer_id'      => $customer_id
									)
								);

		$title       = $order_no. ' - Return Update'; 
		$message     = 'Credit Note for Order No '.$order_no.' has been Cancelled';

		$push_data = array();

		$push_data[] = array(
							'type'		       => 'customer',
							'id'		       => $customer_id,
							'title'		       => $title,
							'message'	       => $message,
							'master_return_id' => '',
							'order_no'         => $order_no,
							'sms'			   => array(
												   'mobile'   => $telephone,
												   'message' => $this->customer_sms_template($data),
												)
						);
		$this->queuePushNotification($push_data);

		/*After sending push notification, generate reply help desk ticket*/
		if(!empty($data['cn_id'])) {
			$master_ids = $this->getHelpDeskTicketsForCreditNoteIds($data['cn_id']);
			/*After sending push notification, generate reply help desk ticket*/
			$master_ids = array_combine(array_column($master_ids, 'master_return_id'), 
									array_column($master_ids, 'helpdesk_ticket_id')
									);
			if(!empty($master_ids)) {
			/*handle case if same ticket id agenst multiple master return id*/
				$data['ticket_data'] 	= array(
					'customer_id'	=> $customer_id,
					'title'			=> $title,
					'master_ids' 	=> $master_ids,
					'description'	=> $this->customer_sms_template($data)
				);
				$this->sendToHelpDesk($data);
			}
		}	
	}

	
}//End of Class
