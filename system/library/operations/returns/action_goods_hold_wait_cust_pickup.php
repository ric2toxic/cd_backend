 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionGoodsHoldWaitCustPickup extends ReturnActionBase 
{	
	protected $action_name 	  = 'GOODS TO PICKED UP';
	
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
    		$op_id   = $return['order_product_id'];
    		$dn_id   = $return['debit_note_id'];
    		$cn_id   = $return['credit_note_id'];
    		$credit_notes = array_combine(
    			                     array_column($data['credit_notes'], 'credit_note_id'), 
    			                     $data['credit_notes']
    			               );
    		//$return_action_id     = $return['return_action_id'];
    		$return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;
    		
    		if(
    			$return_action_status == 1
    				&&
    			(
	    			(
	    			isset($data['all_debit_notes'][$dn_id])
	    				&&
	    			$data['all_debit_notes'][$dn_id]['debit_note_status'] == 1
	    			)
	    				||
	    			(
	    			isset($credit_notes[$cn_id])
	    				&&
	    			$credit_notes[$cn_id]['credit_note_status'] == 1
	    			)
	    		)
    		){
    			return $is_visible;
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

		if(!empty($data) && !empty($data['invoice_status']))
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

			$_html .= '<p>There is an update against your Return/Replacement request for the Order No '.$data['order_no'].'. We are waiting for you to picked up following items. </p>';
			
			$_html .= '<p>Products :</p>';
			$_html .= $mailer->getProductList($data);

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
		$_message = '';

		if(empty($data)) { return $_message;}

		$order_no 			= isset($data['order_no'])?$data['order_no']:'';
		$pickup_address     = $data['pickup_address'];
		$url 		   		= isset($data['url'])?$data['url']:'';
		
		$address1          = 'WholeSaleBox ' ;
		$address2          = $pickup_address['city'];

		$_message =  sprintf(
							RETURN_CUSTOMER_SMS_TEMPLATE[RETURN_ACTION_IDS['Goods_Hold_WSB_Wait_Customer_Pickup']],
							$order_no,
							$address1,
							$address2,
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
	public function sendPushNotification($data = array()){

		if(empty($data) || empty($data['customer_id']) || empty($data['invoice_status'])){ return true; }

		$customer_id 		= $data['customer_id'];

		$master_return_id 	= array_unique(array_column($data['returns'], 'master_return_id'));

		$order_product_ids 	= array_column($data['returns'], 'order_product_id');
		$pickup_address 	= OrderInfo::getPickupCityCodes($this->db,$order_product_ids);

		//Check if Products form multile sub-orders
		if( count($pickup_address) > 1) {
			$pickup_address =  OrderInfo::getWarehouseDetailsByCityCode($this->db, 'JP');
		}else{
			$pickup_city_code = $pickup_address[0]['pickup_city_code'];
			$pickup_address =  OrderInfo::getWarehouseDetailsByCityCode($this->db, $pickup_city_code);
		}

		$order_no 		= $data['order_no'];
		$telephone		= $data['customer']['phone'];
		$data['url'] = $this->getReturnUrl(
											array(
												'order_id'         => $data['order_id'],
												'master_return_id' => (count($master_return_id)==1)?$master_return_id[0]:'',
												'customer_id'      => $customer_id
											)
										);
		$data['pickup_address'] = $pickup_address;

		$sms_template = $this->customer_sms_template($data);

		$title   = $order_no. ' - Return Update'; 
		$message = 'Please pick goods for Order No '.$order_no.'.';

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
