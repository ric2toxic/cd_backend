 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionCnForClient extends ReturnActionBase 
{	
	protected $action_name 	  = 'CREDIT NOTE';

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
			$mailer   = new ActionEmailer($this);
			$subject  = 'Return/Replacement request for an Order No '.$data['order_no'].' - Status Updated: Credit Note '; 
			$mailer->emailSetting(
								array(
									'subject' 	=> $subject,
									'to'		=> $data['customer']['email'],
									'bcc'		=> '', // array('returns', 'account', 'vikas'),
									'template'	=> '',
								    'html'		 => $this->customer_email_template($mailer, $data),
								    'attachments'=> !empty($data['attachments'])? $data['attachments']:''
									)
								);
			//Send Mail
			$mailer->send();
		}
	}

	private function customer_email_template($mailer, $data)
	{

		$_html  = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';

			$_html .= $mailer->getHeaderText( ucfirst($data['customer']['name']) );
			$_html .= 'You return request for Order No '.$data['order_no'].' has been completed, and Credit Note ';
			$_html .= $data['cn_no'] ?? '';

			$_html .= ' has been generated. Please find the attached Credit Note for your perusal.<br><br> ';
			$_html .= 'Kindly add/update your bank account details (if any).';
			if(!empty($data['bank_detail_url'])){

				$short_url = $data['bank_detail_url'];
				$url = parent::convertUrlToShortUrl($data['bank_detail_url']);

				if($url['status']){
					$short_url = $url['message'];
				}
				$_html .= ' To add/update bank details, please <a href="'.$short_url.'">click here</a><br>';
			}
			$_html .= 'Otherwise, you can also login to WholesaleBox website/app and check My Account -> Bank Details.<br><br>';
			$_html .= 'A refund shall be initiated in a few working days. Please ensure your Bank Account details are correct.<br><br>';
			$_html .= 'Please call 9587896265 for any help or clarification.<br><br>';
			$_html .= 'Hoping to get a repeat order from you soon :)<br><br>';
			$_html .= 'Happy Selling !';
		
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
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['CN_For_Client']],
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

		$customer_id = $data['customer_id'];
		
		$order_no    = $data['order_no'];

		$data['url'] = $this->getReturnUrl(
											array(
												'order_id'         => $data['order_id'],
												'master_return_id' => '',
												'customer_id'      => $customer_id
											)
										);

		$telephone	 = isset($data['customer']['phone'])?$data['customer']['phone']:'';

		$title   = $order_no. ' - Return Update'; 
		$message = "Credit Note for Order No ".$order_no.", is generated.";

		$sms_template = $this->customer_sms_template($data);
		
		$push_data = array();
		$push_data[] = array(
							'type'		       => 'customer',
							'id'		       => $customer_id,
							'order_no'         => $order_no,
							'title'		       => $title,
							'message'	       => $message,
							'master_return_id' => '',
							'sms'			   => array(
												   'mobile'	  => $telephone,
												   'message' => $sms_template,
												)
						);
		$this->queuePushNotification($push_data);

		/*After sending push notification, generate reply help desk ticket*/
		if(!empty($data['return_ids'])) {
			$master_ids = $this->getHelpDeskTicketsForReturnIds($data['return_ids']);
			/*After sending push notification, generate reply help desk ticket*/
			$master_ids = array_combine(array_column($master_ids, 'master_return_id'), 
									array_column($master_ids, 'helpdesk_ticket_id')
									);
			if(!empty($master_ids)) {
			/*handle case if same ticket id agenst multiple master return id*/
				$master_ids = array_filter($master_ids);
				// set ticket id as array key to get unique master return ids
				$master_ids = array_flip($master_ids);
				// revert master return id as array key again
				$master_ids = array_flip($master_ids);

				$data['ticket_data'] 	= array(
					'customer_id'	=> $customer_id,
					'master_ids' 	=> $master_ids,
					'title'			=> $title,
					'description'	=> $sms_template
				);
				$this->sendToHelpDesk($data);
			}
		}
	}
	
	/**
	 * @info: Public Method to send CN updated email alert to accounts and return 
	 * @param:  $data Array
	 * @return: Boolen
	 * @author: MSA, July 2018
	*/
	public function sendCnUpdatedEmail($data = array()) {

		if(!empty($data)) {
			$mailer   = new ActionEmailer($this);
			$mailer->emailSetting(
								array(
									'subject' 	=> 'Order No '.$data['credit_note_info']['order_no'].' - Status: Credit Note Updated',
									'to'		=> array('returns', 'account'),
									'bcc'		=> array('vikas'), 
									'template'	=> '',
								    'html'		 => $this->getCnUpdateEmailTemplate($mailer, $data),
								    'attachments'=> !empty($data['attachments'])? $data['attachments']:''
									)
								);
			//Send Mail
			$mailer->send();
		}
	}

	/**
	 * @info: Public Method to generate HTML content for CN update email
	 * @param:  $mailer Object
	 * @param:  $data Array
	 * @return: Boolen
	 * @author: MSA, July 2018
	*/
	public function getCnUpdateEmailTemplate($mailer, $data)
	{
		$credit_note_no = $data['credit_note_info']['credit_note_prefix'].$data['credit_note_info']['credit_note_no'];

		$_html  = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
			$_html  = '<p>Dear All,</p>';
			$_html .= '<p>Credit Note <b>'.$credit_note_no.'</b> details has been updated.</p>';
			$_html .= '<p>Find updated credit note pdf file in attachment.</p>';
		$_html .= '</div>';
		return $_html;
	}

}//End of Class
