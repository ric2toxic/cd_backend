 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionSelfShipment extends ReturnActionBase 
{	
	protected $action_name 	  = 'Self Shipment';
	
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
	 * @author: MSA, March 2018
	*/
	public function sendEmail($data = array()) {
		if(!empty($data) && !empty($data['invoice_status'])) {
			$order_product_ids = array_unique(array_column($data['returns'], 'order_product_id'));
			$pickup_address = OrderInfo::getPickupCityCodes($this->db,$order_product_ids);
			if( count($pickup_address) > 1) {
				$data['pickup_address'] =  OrderInfo::getWarehouseDetailsByCityCode($this->db,'JP');
			}else{
				$pickup_city_code = $pickup_address[0]['pickup_city_code'];
				$data['pickup_address'] =  OrderInfo::getWarehouseDetailsByCityCode($this->db,$pickup_city_code);
			}
			$mailer = new ActionEmailer($this);
			$mailer->emailSetting(
								array(
									'subject' 	=> 'Return/Replacement request for Order No '.$data['order_no'].' - Status Updated: ' . $this->action_name,
									'to'		=> $data['customer']['email'],
									'cc'		=> '', 
									'template'	=> '',
								    'html'		 => $this->customer_email_template($mailer, $data),
								    'attachments'=> ''
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
			$_html .= $mailer->getHeaderText( ucfirst($data['customer']['name']) );
				$_html .= '<p>';
					$_html .= 'You have chosen self shipment for the return/replacement for Order No '.$data['order_no'].'. ';
				$_html .= '</p>';
			if(!empty($data['pickup_address'])) {
				$_html .= '<p>';
					$_html .= 'Please send products to below address:<br><br>';
					$_html .= '<b>'.$data['pickup_address']['warehouse_name'].'<br>';
					$_html .= $data['pickup_address']['address_1'].'<br>';
					$_html .= $data['pickup_address']['address_2'].'<br>';
					$_html .= $data['pickup_address']['city'].'</b>';
				$_html .= '</p>';
			}	
			$_html .= '<p>If you will not ship within 3 days then your return/replacement request will be canceled. So please send following products by '.date('d-m-Y',strtotime('+3 Days')).'.</p>';
			$_html .= '<p>You shall ship following products:</p>';
			$_html .= $mailer->getProductList($data);
			$_html .= $mailer->getFooterText();
		$_html .= '</div>';
		return $_html;
	}

	/**
	 * @info: Public Method to send customer sms template
	 * @param: $data Array
	 * @return: String SMS template
	 * @author: MSA, March 2018
	*/
	public function customer_sms_template($data)
	{
		$_message = '';

		if(empty($data)) { return $_message;}

		$order_no 			= $data['order_no'];
		$number_of_pieces 	= isset($data['return_quantity'])?$data['return_quantity']:'';
		$url 				= isset($data['url'])?$data['url']:'';
		
		$pickup_address1     =  'WholeSaleBox ' ; 
		$pickup_address2     =  $data['pickup_address']['city']; 

		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Self_Shipment']],
							$order_no,
							$number_of_pieces,
							$pickup_address1,
							$pickup_address2,
							$url,
							RETURN_HELPLINE
						);
		return $_message;
	}

	/**
	 * @info: Public Method to send customer SMS & Push notification
	 * @param: $data Array
	 * @return: Void
	 * @author: MSA, March 2018
	*/
	public function sendPushNotification($data = array())
	{
		if(empty($data) || empty($data['customer_id']) || empty($data['invoice_status'])){ return true; }

		$customer_id 		= $data['customer_id'];

		$master_return_id 	= array_unique(array_column($data['returns'], 'master_return_id'));

		$order_product_ids = array_unique(array_column($data['returns'], 'order_product_id'));

		$pickup_address = OrderInfo::getPickupCityCodes($this->db,$order_product_ids);
		
		if( count($pickup_address) > 1) {
			$pickup_address =  OrderInfo::getWarehouseDetailsByCityCode($this->db, 'JP');
		}else{
			$pickup_city_code = $pickup_address[0]['pickup_city_code'];
			$pickup_address =  OrderInfo::getWarehouseDetailsByCityCode($this->db, $pickup_city_code);
		}

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


	    $data['pickup_address']  = $pickup_address;
		$sms_template = $this->customer_sms_template($data);

		$title = $order_no. ' - Return Update'; 		
		$message = 'If you will not ship within 3 days then your return request will be cancelled. So please send products by ' . date('d-m-Y',strtotime("+3 days"));

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
