 <?php
/**
 * 	ReturnActionBase
 *  @info: ReturnInfo class to fetch return related all information
 * 	@author @Nishu Rani, Jan 2018
 */
require_once(DIR_SYSTEM.'library/wsbregisterybase.php');
require_once(DIR_SYSTEM.'library/prefixes.php');

class ReturnBase extends WSBRegisteryBase
{
	private $_return_reason_enum = array(
								RETURN_REASON_IDS['Rejected_Wrong_Product'] => 'REJECTED_WRONG_PRODUCT',
								RETURN_REASON_IDS['Rejected_Seller_Damage'] => 'REJECTED_SELLER_DAMAGE',
								RETURN_REASON_IDS['Loss_By_WSB']         	=> 'REJECTED_WSB_DAMAGE',
								RETURN_REASON_IDS['Transfer_To_WSB_Books'] 	=> 'TRANSFER_TO_WSB_BOOKS',
								RETURN_REASON_IDS['Cancelled_By_Customer'] 	=> 'CANCELLED_BY_CUSTOMER',
								RETURN_REASON_IDS['Other_Please_Supply_Details_Replacement'] => 'DAMAGE_BY_COURIER_DURING_PICKUP',
							);
	
	public function __construct($registry = null){
		parent::__construct($registry);
	}

	/**
     * Public method to add new return
     * @param: 
     * @return: void
     * @author: Nishu, Sept 2018
	*/
	Public function addReturnWithPendingStatus(array $post) : void{
		
		$data = $post['data'];
    	//$data = $this->request->get['data'];
    	$new_master_return_id = 0;
    	$return_ids  = '';
    	
    	//Is empty Check for $data
    	if(!empty($data)){

    		$this->load->model('sale/return', 'admin');
    		$order_id = 0;

    		//Get customer id from data 
    		$customer_id              = $data[0]['customer_id'] ?? 0;
    		$customer_payment_company = $data[0]['customer_payment_company'] ?? '';
			
			$data = array_combine(
						array_column($data, 'order_product_id'),
						$data);	
    		
    		//Create object of ReturnActionBase class
    		$return_action_base = new ReturnActionBase($this);

    		//Create object of ReturnInfo class
    		$return_info = new ReturnInfo($this);

    		//Merge orderProducts to $data, 
    		//If front-end validation is failed and any product is missing from combo to add return 
    		$data = $return_action_base->addMissingComboOrderProductsForReturn($data);

    		//Order_product_ids for which we have to add returns
    		$op_ids = array_column($data, 'order_product_id');

    		//Get All active return(s) for given order_product_ids
    		$all_returns = $return_info->getReturnsByOpIds($op_ids);

    		$all_returns = array_combine(
    			                      array_column($all_returns, 'return_id'), 
    			                      $all_returns
    			                    );

    		//Set All order productids for existing(open) reurns
    		$all_returned_op_ids = array_column($all_returns, 'order_product_id');

    		//Fetch unique orderProductIds for existing returns
    		$all_returned_op_ids = array_unique($all_returned_op_ids);

    		//Loop for adding each return
    		foreach ($data as $return_data) {

    			if(
    				empty($return_data['return_reason'])
    					|| 
    				empty($return_data['return_quantity'])
    					||
    				empty($return_data['shipping_method'])
    			){
    				continue;
    			}

    			$order_id             = (int)$return_data['order_id'];
    			$op_id                = (int)$return_data['order_product_id'];
    			$old_op_id            = $op_id;
    			
    			$already_reurned_qty  = 0;
    			
    			//Get Already returned product quantity
				$already_reurned_qty  = $return_info->getReturnedQtyByOpId($op_id, $return_data['last_return_id'] );

				$field_list = array('quantity', 'piece_in_set');
				$op_data = OrderInfo::getOrderProductDetailsByOpId($this->db, $op_id, $field_list);
				$total_op_qty = (int)$op_data['quantity'] * (int)$op_data['piece_in_set'];

				//Set Remaining return qty to add or update return
				$remaining_qty = $total_op_qty - $already_reurned_qty;

				//If not any qty available to add return 
				if($remaining_qty <= 0){
					continue;
				}

				//Return adding request is greater then remaining qty for that OrderProduct
				if($return_data['return_quantity'] > $remaining_qty ){
					$return_data['return_quantity'] = $remaining_qty;
				}

				//Check for if this request to add new return or Update existing
				if(empty($return_data['last_return_id'])){
				
					//If Buyer Invoice is not generated
	                if( empty($return_data['buyer_invoice_id']) ){
	                	$user_id = 0;
	                	$user_name = "";
	                	if(!empty($this->user)){
	                		$user_id = $this->user->getId();
	                		$user_name = $this->user->getUserName()["name"];
	                	}

	                	$opData                     = array();
						$opData['edit_type'] 	    = $this->_return_reason_enum[$return_data['return_reason']];
						$opData['op_id'] 			= (int)$op_id;
						$opData['seller_id'] 		= (int)$return_data['seller_id'];
						$opData['comment'] 			= trim($return_data['comment']);
						$opData['quantity'] 		= $return_data['total_quantity'] - $return_data['return_quantity'];
						$opData['total_quantity']	= $return_data['total_quantity'];
						$opData['return_reason_id'] = $return_data['return_reason'];
						$opData['edit_history']     = array(
														'user_id'    => (int)$user_id,
														'user_name'  => $user_name,
														'user_type'  => 'Return User',
														'user_ip'	 => $this->request->getIpAddress,
														'user_agent' => $_SERVER['HTTP_USER_AGENT'],
														'date_added' => date('d-m-Y H:i:s'),
														'comment'    => $opData['edit_type']
	 												  );
						
						//Spilt OrderProduct qty with return qty only if buyerInvoice is not generated
						$new_pid = OrderEdit::splitOrderProduct($this->db, $op_id, $opData);

						//Update with newly generated OrderProductId
						$op_id 	= $new_pid;
					}

					//Setting data to insert into oc_return table
	    			$return_id = $this->setAddReturnData((int)$op_id, (int)$new_master_return_id, $return_data, $all_returns);

	    			$return_ids .= $return_id.',';
    			}else{

    				//update existing return 
    				$this->load->admin_model_sale_return->updateExistingReturn($return_data);
    			}
    		}

    		//Code added by Nilesh because of oc_order_product triggers removal
    		//To update order total
            OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id);

    		$return_data['return_ids'] = trim($return_ids, ',');
    		$return_data['customer_payment_company'] = $customer_payment_company;		
			/*End Block Sending email, SMS and Pushnotification*/
    		$this->setSendMailData($return_data);

    	}
    
	}

	/**
	 * @info: Set data to add and update return request
	 * @param: $data
	 * @author: Nishu, Sept 2017
	*/
    public function setAddReturnData(int $op_id, int $new_master_return_id, array $return_data, array $all_returns) : int{
    	//Create object of ReturnActionBase class
    	$return_action_base = new ReturnActionBase($this);

    	$insert_return = array();

    	$order_id = $return_data['order_id'];

    	//Update array with old data, If Old return Id is exist(Specialy For OldMasterReturnId)
    	if(!empty($return_data['last_return_id']) && isset($all_returns[$return_data['last_return_id']]) ){
			$r_id = $return_data['last_return_id'];
			$insert_return  = $all_returns[$r_id];
		}

		//Set Return info coming from Return Form
    	$insert_return['order_product_id'] = $op_id;
		$insert_return['return_reason_id'] = $return_data['return_reason'];
		$insert_return['comment']          = trim($return_data['comment']);
		$insert_return['return_action_id'] = RETURN_ACTION_IDS['Pending'];
		$insert_return['shipping_method']  = $return_data['shipping_method'];
		$insert_return['customer_id']      = $return_data['customer_id'];
		$insert_return['order_id']         = $order_id;
		$insert_return['quantity']         = $return_data['return_quantity'];

		//If return reason is to Marked LOSS BY WSB
		if($insert_return['return_reason_id'] == RETURN_REASON_IDS['Loss_By_WSB']){
			$insert_return['return_action_id'] = RETURN_ACTION_IDS['Loss_Booked_By_WSB'];
		}

		if(empty($insert_return['master_return_id'])){
			if($new_master_return_id == 0){
				//Get Avaiable Master Return Id which is not assigned to that specific OrderProductId
				$available_master_return_id = $return_action_base->checkForMasterReturnId($order_id, $op_id );
				if($available_master_return_id == 0){
					$new_master_return_id = $return_action_base->addMasterReturn($insert_return);
					$insert_return['master_return_id'] = $new_master_return_id;
				}else{
					$insert_return['master_return_id'] = $available_master_return_id;
				}
			}else{
				$insert_return['master_return_id'] = $new_master_return_id;
			}
		}

		//Update Old return as inactive
		if(!empty($return_data['last_return_id'])){
			$return_action_base->updateExistingAsInactive($return_data['last_return_id']);
		}

		//Add Return to oc_return table
		$return_id = $return_action_base->insertReturn($insert_return);

		//Return newly added return_id in oc_return table
		return (int)$return_id;
    }


    /**
	 * @info: Set data in proper format to send mail 
	 * @param: $data
	 * @author: Nishu, Sept 2017
	*/
    public function setSendMailData($return_data){
    	
    	$email_data  = array();

    	$email_data['order_id']     = $return_data['order_id'];
		$email_data['order_no']     = $return_data['order_no'];
		$email_data['customer_id']  = $return_data['customer_id'];
		$email_data['seller']       = array();
		$email_data['customer']     = array();
		$email_data['returns']      = array();

		$email_data['customer']['id']     = $return_data['customer_id'];
		$email_data['customer']['name']   = trim($return_data['first_name'].' '.$return_data['last_name']);
		$email_data['customer']['email']  = $return_data['email'];
		$email_data['customer']['phone']  = $return_data['telephone'];
		$email_data['customer']['customer_business_name']  = !empty($return_data['customer_payment_company'])
																? $return_data['customer_payment_company']
																: '';

		$product_images = array();
  		
		$selector = array(
                    'oc_return'=> array('select' => array()) ,
                    'oc_master_return'=> array('select' => array('helpdesk_ticket_id')) ,
                    'oc_return_reason' => array( 'select' => array()),
                    'oc_return_action' => array( 'select' => array()),
                    'oc_order_product' => array( 'select' => array()),
                    'oc_suborder'      => array( 'select' => array('invoice_no as buyer_invoice_no', 'order_status_id'))
                   );
		$return_info_data = array('return_ids' => $return_data['return_ids'] );

		//Create object of ReturnInfo class
    	$return_info = new ReturnInfo($this);
		
		//Returns all order product returns for all return ids
        $returns_details = $return_info->getReturnInfo($this->db, $return_info_data, $selector);

        $opids = array_column($returns_details['oc_return'], 'order_product_id');

		//Get product images list
		if(!empty($opids)) {
	    	MsProduct::setProductImages($this->registry, $opids, $product_images);
		}

		$mail_return_data = array();

		/*Start Block Sending email, SMS and Pushnotification*/
		if(!empty($returns_details['oc_return'])) {

			foreach ($returns_details['oc_return'] as $return) {
				
				$action_classes = array();

				if(
			    	!empty($return['buyer_invoice_id'])
			    	        &&
			    	!empty($return['buyer_invoice_no'])
			    	        &&
			    	$return['order_status_id'] != ORDER_STATUS['Canceled']
			    ){
					$pid    = $return['product_id'];
					$opid   = $return['order_product_id'];
					$return_id = $return['return_id'];
					$return_action_id = $return['return_action_id'];
						
					/*Set image for return product with details*/
					if(!empty($product_images['pid_to_imgs'][$opid])) {
						$mail_return_data[$return_action_id][$return_id]['image'] = $product_images['pid_to_imgs'][$opid];
					}

					$mail_return_data[$return_action_id][$return_id]['return_id']       = $return_id;
					$mail_return_data[$return_action_id][$return_id]['master_return_id']= $return['master_return_id'];
					$mail_return_data[$return_action_id][$return_id]['helpdesk_ticket_id']= $return['helpdesk_ticket_id'];
					$mail_return_data[$return_action_id][$return_id]['order_product_id']= $return['order_product_id'];
					$mail_return_data[$return_action_id][$return_id]['seller_id']       = $return['seller_id'];
					$mail_return_data[$return_action_id][$return_id]['model']           = $return['model'];
					$mail_return_data[$return_action_id][$return_id]['return_reason']   = $return['return_reason']['name'];
					$mail_return_data[$return_action_id][$return_id]['return_type']     = $return['return_reason']['reason_type'];
					$mail_return_data[$return_action_id][$return_id]['return_quantity'] = $return['return_quantity'];
					$mail_return_data[$return_action_id][$return_id]['comment']         = $return['return_comment'];
					$mail_return_data[$return_action_id][$return_id]['comment']         = $return['return_comment'];
					$action_classes[$return_action_id]    = $return['return_action']['class_name'];
				}
			}

			//Send Mail Return action-wise bunching
			foreach ($mail_return_data as $return_action_id => $value) {
				$actionClass = 	$action_classes[$return_action_id];

				$email_data['returns'] = $value;

				if(class_exists($actionClass) && !empty($email_data)) {
					$returnClassObj = new $actionClass($this);

					$returnClassObj->sendEmail($email_data);

					$returnClassObj->sendPushNotification($email_data);
				}
			}
		}
    }

	
}//End of Class