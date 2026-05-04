 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionReverseShipmentGenerated extends ReturnActionBase 
{	
	protected $action_name 	= 'Reverse Shipment Generated';
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
	 * @info: Public Method to send email, Mail method will called from logistics classes instead change action
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function sendEmailReverseShipment($data = array()) 
	{
		if(!empty($data))
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
	

	private function customer_email_template($mailer, $data)
	{
		$return_quantity = 0;
		if(!empty($data['returns'])) {
			foreach ($data['returns'] as $key => $value)
			{
				$return_quantity += $value['return_quantity'];
			}
		}	
		
		$_html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
	      $_html .= $mailer->getHeaderText($data['customer']['name']);
	        $_html .= '<p>';
	          $_html .= 'There is an update against your Return/Replacement request for the Order No '.$data['order_no'].'. ';
	          $_html .= 'Our courier partner will collect following products from below address:';
	          if(!empty($data['pickup_address'])) {
	            $_html .= $data['pickup_address'];
	          }
	        $_html .= '</p>';
	      $_html .= '<p>Refund/Replacement will be processed after we verify the condition of products.</p>';  
	      $_html .= '<p>Products:</p>';
	      $_html .= $mailer->getProductList($data);
	      
	      $_html .= '<p>Kindly pack the returned products and keep ready. Soon our pick up executive will collect the goods.</p>';
	    $_html .= $mailer->getFooterText($data);
	    $_html .= '</div>';
	    return $_html;
	}

	private function customer_sms_template($data)
	{
		$_message = '';

		if(empty($data)) { return $_message;}

		$customer_name 		= isset($data['customer']['name'])?$data['customer']['name']:'';
		$order_no 			= isset($data['order_no'])?$data['order_no']:'';
		$number_of_pieces 	= isset($data['return_quantity'])?$data['return_quantity']:'';
		$courier_partner    = isset($data['courier_partner'])?$data['courier_partner']:'';;
		$url 		   		= isset($data['url'])?$data['url']:'';

		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Reverse_Shipment_Generated']],
							$order_no,
							$courier_partner,
							$number_of_pieces,
							$url,
							RETURN_HELPLINE
						);
		return $_message;

	}

	public function sendPNForReverseShipment($data = array())
	{
		if(empty($data) || empty($data['customer_id'])){ return true; }

		$return_quantity = 0;

		$customer_id 		= $data['customer_id'];

		$master_return_id 	= array_unique(array_column($data['returns'], 'master_return_id'));

		$order_no 		= $data['order_no'];
		$telephone		= $data['customer']['phone'];
		$customer_name 	= $data['customer']['name'];
		
		foreach ($data['returns'] as $key => $value)
		{
			$return_quantity += $value['return_quantity'];
		}

		$data['url'] = $this->getReturnUrl(
											array(
												'order_id'         => $data['order_id'],
												'master_return_id' => (count($master_return_id)==1)?$master_return_id[0]:'',
												'customer_id'      => $customer_id
											)
										);

		$data['return_quantity'] = $return_quantity;
		$sms_template = $this->customer_sms_template($data);

		$title   = $order_no. ' - Return Update'; 
		$message = 'Pick up for your return/replacement request for Order No '.$order_no.'. ';

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
		
		if(!empty($master_ids)) {
		/*handle case if same ticket id agenst multiple master return id*/
			$master_ids = array_filter($master_ids);
			// set ticket id as array key to get unique master return ids
			$master_ids = array_flip($master_ids);
			// revert master return id as array key again
			$master_ids = array_flip($master_ids);
		}

		$data['ticket_data'] 	= array(
			'customer_id'	=> $customer_id,
			'master_ids' 	=> $master_ids,
			'title'			=> $title,
			'description'	=> $sms_template
		);
		$this->sendToHelpDesk($data);
	}




}//End of Class
