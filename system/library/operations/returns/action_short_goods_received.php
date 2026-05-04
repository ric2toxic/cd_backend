 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionShortGoodsReceived extends ReturnActionBase 
{	
	protected $action_name 	  = 'Sort goods received';
	
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
		if(strtoupper($return['return_type']) != 'REPLACEMENT'){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @info: Public Method to check given return data quantity is editable 
	 * @param: Int $return_action_id
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function isQtyEditable($retrun_action_id){
		return 1;
	}

	/**
	 * @info: Public Method to send email
	 * @param: $return Array
	 * @return: Boolen
	 * @author: MSA, March 2018
	*/
	public function sendEmail($data = array()) {
		if(!empty($data) && !empty($data['invoice_status']))
		{
			$mailer = new ActionEmailer($this);
			$mailer->emailSetting(
								array(
									'subject' 	  => 'Return/Replacement request for Order No '.$data['order_no'].' - Status Updated: '.$this->action_name.' ',
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
	 * @info: Public Method to send customer email
	 * @param: $mailer Object
	 * @param: $data Array
	 * @return: String email template
	 * @author: MSA, March 2018
	*/
	public function customer_email_template($mailer, $data)
	{
		$_html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
			$_html .= $mailer->getHeaderText($data['customer']['name']);
				$_html .= '<p>';
					$_html .= 'There is an update against your Return/Replacement request for the Order No '.$data['order_no'].'. ';
					$_html .= 'We have received lesser products as per your request. ';
				$_html .= '</p>';
				$_html .= '<p>After analyzing products, we shall take appropriate action.</p>';
				
				$_html .= '<p>If you haven\'t added bank details then please add. Go to Wholsalebox Web/App – Login – My Account – Bank Details. Also find attached Credit Note with this email.</p>';
				
			$_html .= '<p>Received Products :</p>';
			$_html .= $mailer->getProductList($data);
				
		$_html .= $mailer->getFooterText();
		$_html .= '</div>';
		return $_html;
	}

	/**
	 * @info: Public Method to send customer sms template
	 * @param: $mailer Object
	 * @param: $data Array
	 * @return: String SMS template
	 * @author: MSA, March 2018
	*/
	public function customer_sms_template($data)
	{
		$_message = '';

		if(empty($data)) { return $_message;}

		$order_no 			= $data['order_no'];
		$url 				= isset($data['url'])?$data['url']:'';
		
		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Short_Goods_Received']],
							$order_no,
							$url,
							RETURN_HELPLINE
						);
		return $_message;
	}
	
	/**
	 * @info: Public Method to send customer push notification
	 * @param: $data Array
	 * @return: Void
	 * @author: MSA, March 2018
	*/
	public function sendPushNotification($data = array())
	{
		if(empty($data) || empty($data['customer_id']) || empty($data['invoice_status'])){ return true; }

		$customer_id 		= $data['customer_id'];
		$master_return_id 	= array_unique(array_column($data['returns'], 'master_return_id'));

		$order_no 		= $data['order_no'];
		$telephone		= $data['customer']['phone'];
		$customer_name 	= $data['customer']['name'];

		$data['url']    = $this->getReturnUrl(
											array(
											 'order_id'         => $data['order_id'],
											 'master_return_id' => (count($master_return_id)==1)?$master_return_id[0]:'',
											 'customer_id'      => $customer_id
											)
										);

		$sms_template = $this->customer_sms_template($data);
		
		$title = $order_no. ' - Return Update'; 
		$message = 'We have less goods received products as per your return request for Order No '.$order_no.'. ';

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
