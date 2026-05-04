 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionGoodsPickedup extends ReturnActionBase 
{	
	protected $action_name 	  = 'Goods picked up';
	
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
	 * @author: MSA, March 2018
	*/
	public function customer_email_template($mailer, $data)
	{
		$_html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
			$_html .= $mailer->getHeaderText( ucfirst($data['customer']['name']) );
				
				$_html .= '<p>';
					$_html .= 'There is an update against your Return/Replacement request for the Order No '.$data['order_no'].'. We have picked goods as per your request.';
				$_html .= '</p>';

			if(!empty($data['tracking'])) {
				if(count($data['tracking']) == 1) {
					$_html .= '<p>You can track your reverse shipment from <a href="'.$data['tracking'][0]['tracking_url'].'">here</a>.</p>';
				}else{
					$counter=1;
					$_html .= '<p>';
					foreach ($data['tracking'] as $tracking) {
						$_html .= 'You can track your reverse shipment '.$counter.' from ';
						$_html .= '<a href="'.$tracking['tracking_url'].'">here</a><br>';
						$counter++;
					}
					$_html .= '</p>';
				}
			}	
			$_html .= '<p>Picked Products :</p>';
			$_html .= $mailer->getProductList($data);
		$_html .= $mailer->getFooterText();
		$_html .= '</div>';
		return $_html;
	}

	/**
	 * @info: Public Method create customer SMS template
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

		$tracking_no = '';

		if(!empty($data['tracking'])) {
	 		foreach ($data['tracking'] as $key => $value) {
	 			$tracking_no .= $value['tracking_no'].' ';
	 		}
	 	}

		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Goods_Picked_Up']],
							$order_no,
							$tracking_no,
							$url,
							RETURN_HELPLINE
						);
		return $_message;
	}

	/**
	 * @info: Public Method send push notifications
	 * @param: $data Array
	 * @return: Void
	 * @author: MSA, March 2018
	*/
	public function sendPushNotification($data = array())
	{
		if(empty($data) || empty($data['invoice_status'])) { return false; }

		$this->customer_notification($data);
	}

	/**
	 * @info: Public Method send customer push notifications
	 * @param: $data Array
	 * @return: Void
	 * @author: MSA, March 2018
	*/
	public function customer_notification($data)
	{
		if(empty($data) || empty($data['customer_id'])){ return true; }

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

		$title   = $order_no. ' - Return Update'; 
		$message = 'Goods picked up for your return request for Order No  '.$order_no;

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
