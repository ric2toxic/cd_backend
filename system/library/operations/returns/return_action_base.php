 <?php
/**
 * 	ReturnActionBase
 *  @info: ReturnInfo class to fetch return related all information
 * 	@author @Nishu Rani, Jan 2018
 */
require_once(DIR_SYSTEM.'library/wsbregisterybase.php');
require_once(DIR_SYSTEM.'library/prefixes.php');

class ReturnActionBase extends WSBRegisteryBase
{
	
	public  $return_url 					= HTTPS_CATALOG.'index.php';
	
	private $_limit_in_days_for_quality     = 15;
	private $_limit_in_days_for_replacement = 30;
	
	public $customer_ws_access_token;

	public function __construct($registry = null){
		parent::__construct($registry);
	}

	public function __get($property) {
	    if (property_exists($this, $property)) {
	      return $this->$property;
	    }
	}

	public function __set($property, $value) {
	    if (property_exists($this, $property)) {
	      $this->$property = $value;
	    }

	    return $this;
	}

	/**
     * Public Function to PostChecks for Return Actions
     * @param: $data Array
     * @return: 
     * @author: Nishu, Jan 2018
	*/
	public function doPostCheck($data){
		if(empty($data['return_action_id'])){
			return false;
		}
		if(empty($data['last_return_action_id'])){
			return false;
		}
		$sql = "SELECT * 
		         FROM ".DB_PREFIX."return_action 
		         WHERE 
		           return_action_id = ". (int)$data['last_return_action_id'] ."
		           AND FIND_IN_SET(". (int)$data['return_action_id'] .", post_actions)
		       ";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			return true;
		}else{
			return false;
		}
	}

	/**
     * Public Function to do acton on Return Actions
     * @param: $data Array
     * @return: 
     * @author: Nishu, Jan 2018
	*/
	public function doAction($data){}

	/**
     * Public Function to PostActivities for Return Actions
     * @param: $data Array
     * @return: 
     * @author: Nishu, Jan 2018
	*/
	public function doPostActivity($data){}

	/**
	 *	getAvailablePostAction
	 *	@info: this function to get available return action for current action
	 *	@param 	$refund_id or order_id
	 *	@return Process Refund of an order like pending, successfull
	 */
	public function getAvailablePostAction($return_action_id, $return_data = array()){
		
		$return_actions = 0;

		//Is Empty check
		if(empty($return_action_id)){
			return $return_actions;
		}

		$sql = "SELECT * 
			     FROM ".DB_PREFIX."return_action 
			     WHERE 
			       return_action_id = '". (int)$return_action_id ."'";
		$query = $this->db->query($sql);

		if($query->num_rows > 0 && !empty($query->row['post_actions'])){
			$return_actions = $query->row['post_actions'];
		}

		return $return_actions;
	}

	/**
	 *	getAvailableAllPostAction
	 *	@info: this function to get available return action for current action
	 *	@param 	$refund_id or order_id
	 *	@return Process Refund of an order like pending, successfull
	 */
	public function getAvailableAllPostAction(){
		
		$sql = "
				SELECT return_action_id
			     FROM ".DB_PREFIX."return_action 
			     WHERE 
			       status = 1 
			     ORDER BY name
			    ";
		$query = $this->db->query($sql);

		if($query->num_rows > 0 ){
			$return_action_ids = array_column($query->rows, 'return_action_id');

			$return_actions = implode(',', $return_action_ids);
		}

		return $return_actions;
	}	

	/**
	 * Public function to insert bulk returns data in oc_return table
	 * @param: $data Array [Array or Array]
	 * @return: void
	 * @author: MSA, Oct 2019
	*/
	public function insertReturnMultiple(array $data)
	{
		if(!empty($data)) {

			$sql = "INSERT INTO  `" . DB_PREFIX . "return` ";
			$columns = $this->getColumnsForMultipleInsert();
			$values  = $this->getValuesForMultipleInsert($data); 
			if(!empty($values)) {
				$query = $sql. '('. implode(',',$columns). ')'
						  	 . ' Values '
						  	 . implode(',',$values);
				
				$this->db->query($query);
			}
		}
	}

	/**
	 * Method to set columns in returns multi-insert query
	 * @param:  void
	 * @return: array
	 * @author: MSA, Oct 2019
	*/
	public function getColumnsForMultipleInsert(): array
	{
		return [
				'`master_return_id`',
				'`order_product_id`',
				'`quantity`',
				'`return_reason_id`',
				'`comment`',
				'`return_action_id`',
				'`return_action_reason_id`',
				'`internal_note`',
				'`shipping_method`',
				'`user`',
				'`user_id`',
				'`return_shipment_tracking_id`',
				'`return_shipment_backto_customer_id`',
				'`debit_note_id`',
				'`replacement_note_id`',
				'`credit_note_id`',
				'`relisted_product_id`',
				'`active_row`'
			];
	}

	/**
	 * Method to set values data in returns multi-insert query
	 * @param:  array $data
	 * @return: array
	 * @author: MSA, Oct 2019,
	 * 			Updated  by Nishu, 10th Oct 2019
	*/
	public function getValuesForMultipleInsert(array $data)
	{
		$values = array();
		if(!empty($data))
		{
			foreach ($data as $key => $value) {
				if((int)$value['master_return_id'] <= 0 ){
					throw new \Exception('Invalid Master Return Id: ' . (int)$value['master_return_id'] . '. Insert Return Faild.');
				}
				if((int)$value['order_product_id'] <= 0 ){
					throw new \Exception('Invalid Order Product Id: ' . (int)$value['order_product_id'] . '. Insert Return Faild.');
				}
				if((int)$value['quantity'] <= 0 ){
					throw new \Exception('Invalid Return Quantity: ' . (int)$value['quantity'] . '. Insert Return Faild.');
				}
				if((int)$value['return_reason_id'] <= 0 ){
					throw new \Exception('Invalid Return Reason: ' . (int)$value['return_reason_id'] . '. Insert Return Faild.');
				}
				if((int)$value['return_action_id'] <= 0 ){
					throw new \Exception('Invalid Return Action(Status): ' . (int)$value['return_action_id'] . '. Insert Return Faild.');
				}
				$column_values = array();
				$column_values['master_return_id'] 	= (int)($value['master_return_id']);
				$column_values['order_product_id'] 	= (int)($value['order_product_id']);
				$column_values['quantity'] 			= (int)($value['quantity']);
				$column_values['return_reason_id'] 	= (int)($value['return_reason_id']);
				$column_values['comment'] 			= "'".$this->db->escape(($value['comment'] ?? NULL)). "'";
				$column_values['return_action_id'] 	= (int)($value['return_action_id']);
				$column_values['return_action_reason_id'] = $this->db->escape(($value['return_action_reason_id'] ?? "NULL"));
				$column_values['internal_note'] 	=  "'".$this->db->escape(($value['internal_note'] ?? NULL)). "'";
				$column_values['shipping_method'] 	=  "'".$this->db->escape(($value['shipping_method'] ?? 'not_decided')). "'";
				
				if(!empty($this->user)){ 
					$column_values['user'] 		=  "'".$this->db->escape($this->user->getUserName()['name']). "'";
					$column_values['user_id'] 	= (int)$this->user->getId();
				}else{
					$column_values['user'] 		=  "'".$this->db->escape(($value['user'] ?? NULL)). "'";
					$column_values['user_id'] 	= (int)($value['user_id'] ?? 0);
				}

				$column_values['return_shipment_tracking_id'] 		= $this->db->escape(($value['return_shipment_tracking_id'] ?? "NULL"));
				$column_values['return_shipment_backto_customer_id']= $this->db->escape(($value['return_shipment_backto_customer_id'] ?? "NULL"));

				if(
					!empty($value['debit_note_id']) 
						&& 
					in_array($value['return_action_id'], array(
											RETURN_ACTION_IDS['DN_Generated_For_Seller'],
											RETURN_ACTION_IDS['DN_Generate_for_Logistic_Company']
											) 
							)
				){ 
					$column_values['debit_note_id'] 		= $this->db->escape($value['debit_note_id']);
				} else { 
					$column_values['debit_note_id'] 		= "NULL";
				}

				if(
					!empty($value['replacement_note_id']) 
						&& 
					in_array($value['return_action_id'], array(
											RETURN_ACTION_IDS['Replacement_Note']
											) 
							)
				){ 
					$column_values['replacement_note_id'] 	= $this->db->escape($value['replacement_note_id']);
				} else { 
					$column_values['replacement_note_id'] 	= "NULL";
				}

				//check credit_note_id is given or not
				if( 
					!empty($value['credit_note_id'])
						&&
					$value['return_action_id'] == RETURN_ACTION_IDS['CN_For_Client']
				){ 
					$column_values['credit_note_id'] 		= $this->db->escape($value['credit_note_id']);
				} else { 
					$column_values['credit_note_id'] 		= "NULL";
				}

				if(!empty($value['relisted_product_id'])){
					$column_values['relisted_product_id'] 	= $this->db->escape($value['relisted_product_id']);
				}else{
					$column_values['relisted_product_id']   = "NULL";
				}
				$column_values['active_row'] 			    = 1;
				
				$values[] = '('.implode(',',$column_values) . ')';
			}
		}
		return $values;
	}


	/**
	 * Public function to add return in oc_return table
	 * @param: $data Array
	 * @return: void
	 * @author: Nishu, Jan 2018
	*/
	public function insertReturn($data) {
		if(empty($data)){
			return 0;
		}

		//Insert Query to oc_return table
		$sql  = "INSERT INTO `" . DB_PREFIX . "return` 
				  SET 
				    order_product_id = '" . (int)$data['order_product_id'] . "',
					quantity         = '" . (int)$data['quantity'] . "',
		            date_added       = NOW(),
		            
		              ";
		//check user id is given or not
		if(!empty($this->user)){ 
			$sql .= "
					user             = '". $this->db->escape($this->user->getUserName()['name']) ."',
		            user_id          = '". (int)$this->user->getId() ."',
		            ";			
		}
		        
		//check master return id is given or not
		if(!empty($data['master_return_id'])){ 
			$sql .= "master_return_id = '" . (int)$data['master_return_id'] . "',";			
		}
		
		//check return reason id is given or not
		if(!empty($data['return_reason_id'])){ 
			$sql .= "return_reason_id = '" . (int)$data['return_reason_id'] . "',";			
		}

		//check return action id is given or not
		if(!empty($data['return_action_id'])){ 
			$sql .= "return_action_id = '" . (int)$data['return_action_id'] . "',";
		}

		//check shipping_method is given or not
		if(!empty($data['shipping_method'])){ 
			$sql .= "shipping_method = '" . $this->db->escape($data['shipping_method']) . "',";			
		}

		//check comment is given or not
		if(!empty($data['comment'])){ 
			$sql .= "comment = '" . $this->db->escape(trim($data['comment'])) . "',";			
		}

		//check return action reason id is given or not
		if(!empty($data['return_action_reason_id'])){ 
			$sql .= "return_action_reason_id = '" . (int)$data['return_action_reason_id'] . "',";			
		}

		//check internal_note is given or not
		if(!empty($data['internal_note'])){ 
			$data['internal_note'] = trim($data['internal_note'], '\n');
			$sql .= "internal_note = '" . $this->db->escape(trim($data['internal_note'])) . "',";			
		}

		//check customer_id is given or not
		if(!empty($data['customer_id'])){ 
			$sql .= "customer_id = '" . (int)$data['customer_id'] . "',";			
		}

		//check crm_user_id is given or not
		if(!empty($data['crm_user_id'])){ 
			$sql .= "crm_user_id = '" . (int)$data['crm_user_id'] . "',";			
		}

		//check debit_note_id is given or not
		if(
			!empty($data['debit_note_id']) 
				&& 
			in_array($data['return_action_id'], array(
									RETURN_ACTION_IDS['DN_Generated_For_Seller'],
									RETURN_ACTION_IDS['DN_Generate_for_Logistic_Company']
									) 
					)
		){ 
			$sql .= "debit_note_id = '" . (int)$data['debit_note_id'] . "', ";			
		} else { 
			$sql .= "debit_note_id = NULL, ";
		}

		//check replacement_note_id is given or not
		if(
			!empty($data['replacement_note_id']) 
				&& 
			in_array($data['return_action_id'], array(
									RETURN_ACTION_IDS['Replacement_Note']
									) 
					)
		){ 
			$sql .= "replacement_note_id = '" . (int)$data['replacement_note_id'] . "', ";			
		} else { 
			$sql .= "replacement_note_id = NULL, ";
		}

		//check credit_note_id is given or not
		if( 
			!empty($data['credit_note_id'])
				&&
			$data['return_action_id'] == RETURN_ACTION_IDS['CN_For_Client']
		){ 
			$sql .= "credit_note_id = '" . (int)$data['credit_note_id'] . "',";
		} else { 
			$sql .= "credit_note_id = NULL, ";
		}

		//check return_shipment_tracking_id is given or not
		if(!empty($data['return_shipment_tracking_id'])){ 
			$sql .= "return_shipment_tracking_id = '" . (int)$data['return_shipment_tracking_id'] . "',";
		}

		//check return_shipment_backto_customer_id is given or not
		if(!empty($data['return_shipment_backto_customer_id'])){ 
			$sql .= "return_shipment_backto_customer_id = '" . (int)$data['return_shipment_backto_customer_id'] . "',";
		}

		//check relisted_product_id is given or not
		if(!empty($data['relisted_product_id'])){ 
			$sql .= "relisted_product_id = '" . (int)$data['relisted_product_id'] . "',";		
		}

		//Default value for active_row field
		$sql .= " active_row = '1'";


		//Initializing return Id with 0 value
		$return_id = 0;

		//Execute Query
		if($this->db->query($sql)){
            $return_id  = $this->db->getLastId();
		}
		
		return $return_id;
	}

	/**
	 * Public function to add master return id in master_return table
	 * @param: $data Array
	 * @return: Master Return Id
	 * @author: Nishu, Jan 2018
	*/
	public function addMasterReturn($data){
		$master_return_id = 0;
		//If $data is empty, return master_return_id as 0
		if(empty($data)){
			return $master_return_id;
		}

		//If $data['order_id'] is empty, return master_return_id as 0
		if(empty($data['order_id'])){
			return $master_return_id;
		}

		//get return prefix dynamically from DB
	    $return_prefix = $this->getReturnPrefix();
	    //Create return no Dynamically
	    $return_no = $this->createReturnNo();
	    $return_shipment_tracking_id = 0;
	    if(!empty($data['return_shipment_tracking_id'])){
	    	$return_shipment_tracking_id = $data['return_shipment_tracking_id'];
	    }

	    //Combine return prefix and return_no
	    $return_no = $return_prefix.$return_no;
	    $user_id = 0;
	    if(!empty($this->user)){
	    	$user_id = $this->user->getId();
	    }
	    $sql = "INSERT INTO ".DB_PREFIX."master_return SET
	    		order_id = '".(int)$data['order_id']."',
	            return_no = '".$this->db->escape($return_no)."',
	            return_shipment_tracking_id = ".(int)$return_shipment_tracking_id.",
	            customer_id= '". (int)$data['customer_id'] ."',
	            user_id = '".(int)$user_id."', 
	            shipping_method = '" . $this->db->escape($data['shipping_method']) . "',
	            date_added = NOW()";   
	    if($this->db->query($sql)){
	        $master_return_id =  $this->db->getLastId();
	    }
	    return $master_return_id;
    }

    public function getRejectedMasterReturnId($order_id){
    	$master_return_id = 0;
    	if(empty($order_id)){
    		return $master_return_id;
    	}

    	$sql = "
    	        SELECT 
    	          mr.master_return_id,
    	          COUNT(
    	             IF(ocr.return_action_id != ".RETURN_ACTION_IDS['Return_Request_Rejected'].",
    	             1, 
    	             NULL)
    	           ) as non_rejected_count
    	           FROM ".DB_PREFIX."return ocr
    	           INNER JOIN
    	        ".DB_PREFIX."master_return as mr ON ocr.master_return_id = mr.master_return_id
    	        WHERE
    	        	mr.order_id = ".(int)$order_id."
    	        GROUP BY ocr.master_return_id
    	        HAVING 
    	            non_rejected_count = 0
    	        LIMIT 0, 1
    	       ";
    	$result = $this->db->query($sql);
    	if($result->num_rows > 0){
    		$master_return_id = $result->row['master_return_id'];
    	}

    	return $master_return_id;
    }


    /**
	 * Public function to get Return prefix dynamically from DB
	 * @return: Return Prefix
	 * @author: Nishu, Jan 2018
	*/
	public function getReturnPrefix(){
		$prefix = '';
		$prefix_obj     = new Prefixes();
		$prefix_details = $prefix_obj->getPrefix($this->db, '', 'RETURN_NO');
		$prefix         = $prefix_details['prefix'];
		$financial_year = '';
		if ( (int)(date('m')) <= 3 ) {
			$financial_year = date('y', strtotime('-1 years'));
		} else {
			$financial_year = date('y');
		}
		$prefix = $prefix . $financial_year . '-';
		return $prefix;
	}
    
	/**
	 * Public function to get Return prefix dynamically from DB
	 * @return: Return Prefix
	 * @author: Nishu, Jan 2018
	*/
    public function createReturnNo(){
        $sql  = "SELECT max(master_return_id) as return_no ";
        $sql .= "FROM ".DB_PREFIX."master_return ";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $return_no = $query->row['return_no'] + 1;
        }else{
            $return_no = 1;
        }
        return $return_no;
   	}

	/**
	 * Public function to add custom(CN/DN) return in product orders
	 * @param : $data- array
	 * @return : Integer
	 * @author : Nishu, Feb 2018
	 */
	public function splitOrderProduct($data) {

		/*//Inilize $new_pid with 0 value
		$new_pid = 0;
		if(empty($data)){
			return $new_pid;
		}else if(empty($data['op_id'])){
			return $new_pid;
		}

		$new_pid = $data['op_id'];
		$return_history = array(
								'user_id'			=> $this->user->getId(),
								'date_time'			=> date("Y-m-d H:i:s"),
								'comment'			=> $data['comment'],
							);
		$sql  = "SELECT * FROM " . DB_PREFIX . "order_product 
				 WHERE 
					order_product_id = ". (int)$data['op_id'];
		
		$result = $this->db->query($sql);

		if(!empty($result)){
			$opData = $result->row;
			$total_quantity 		= $opData['quantity'] * $opData['piece_in_set'];
			$rejected_quantity 		= $data['quantity'];
			//Check if rejected_quantity is less then total_quantity and new row will inserted to order+product or not
			if($rejected_quantity < $total_quantity){
				
				$opData['order_product_id'] = null;

				$sql = "
						INSERT INTO " . DB_PREFIX . "order_product 
						VALUES
						     ('" . implode("','",$opData) . "')";
				$this->db->query($sql);	

				//Get New Inserted Order_product Id
				$new_pid = $this->db->getLastId();
				
				$fulfilled_quantity		= $total_quantity - $rejected_quantity;
				$main_history			= array();
				$main_history[] 		= unserialize($opData['edit_history']); 
				$main_history[]			= $return_history;
				//Update old row with fullfilled product against order
				$sql  = "UPDATE " . DB_PREFIX . "order_product 
						 SET
							quantity 			= ". (int)$fulfilled_quantity .",
							piece_in_set 		= 1
						 WHERE
							order_product_id = ". (int)$data['op_id'];
				$this->db->query($sql);
			}
			//Update new row with rejected product by custom return
			$sql  = "UPDATE " . DB_PREFIX . "order_product 
					 SET
						quantity 			= ". (int)$rejected_quantity .",
						piece_in_set 		= 1,
						edit_type			= '".$data['return_reason']."',
						edit_history = '". serialize($return_history) ."'
					 WHERE
						order_product_id = ". (int)$new_pid;
			$this->db->query($sql);
		}*/
		//return $new_pid;
	}// End of splitOrderForCustomReturn()
	
	/**
	 * public function to get pickup-status Order Product
	 * @param: order product ID
	 * @return: String
	 * @author: Nishu, Feb 2017
	*/
	public function getPickupStatusByOPId($opid){
		$sql = "
				SELECT pickup_status
				FROM ".DB_PREFIX."order_product
				WHERE 
					order_product_id = " . (int)$opid . "
			  ";
		$result = $this->db->query($sql);
		if($result->num_rows > 0){
			return $result->row['pickup_status'];			
		}else{
			return '';
		}
	}

	/**
	 * Public function to get all return reasons for specific suborder status
	 * @param : $data- Array
	 * @return : $result Array
	 * @author : Nishu, Feb 2018
	 */
	public function returnReasonsForOrderProduct($data) {

		$_failed_return_reason_id 		= array( RETURN_REASON_IDS['COD_Failed'] );
	
		$_processed_return_reason_id    = array(
													RETURN_REASON_IDS['Rejected_Wrong_Product'], 
													RETURN_REASON_IDS['Rejected_Seller_Damage'], 
													RETURN_REASON_IDS['Cancelled_By_Customer'], 
													RETURN_REASON_IDS['Loss_By_WSB']
												 );
		$_parcel_lost_return_reason_id 	= array(RETURN_REASON_IDS['Damaged_By_Courier_Company']);
		
		$_quality_return_reason_id 		= array( 
												    RETURN_REASON_IDS['Quality_Issue'], 
												    RETURN_REASON_IDS['Pricing_Issue'], 
												    RETURN_REASON_IDS['Wrong_Item_Received'], 
												    RETURN_REASON_IDS['Order_Error_By_Customer'], 
												    RETURN_REASON_IDS['Other_Please_Supply_Details']
												   );
		$_sor_return                    = array( RETURN_REASON_IDS['sor_return_under_buyback'] );

		$_replacement_return_reason_id 	= array(
												RETURN_REASON_IDS['Manufacturing_Defect'],
												RETURN_REASON_IDS['Wrong_Item_Received_Replacement']
											  );

		//Inilize $result array as empty
		$result = array();
		
		if(empty($data)){
			return $result;
		}else if(
			empty($data['order_id']) 
			    || 
			empty($data['suborder_id']) 
			    || 
			empty($data['suborder_status'])
			    || 
			empty($data['seller_invoice_id'])
	    ){
			return $result;
		}

		//Handling Case, When buyer invoice is still not generated
		if( empty($data['buyer_invoice_id']) ){
			$result['processed']        = array(
											'title'   => 'Processed Reasons',
											'reasons' => $_processed_return_reason_id
									   	);
		}else{
			if($data['suborder_status'] == ORDER_STATUS['Failed']){
				$result['failed']       = array(
											'title'   => 'Failed Reasons',
											'reasons' => $_failed_return_reason_id
									    );
			}else if($data['suborder_status'] == ORDER_STATUS['Parcel Lost']){
				$result['parcel_lost']  = array(
											'title'   => 'Parcel Lost Reasons',
											'reasons' => $_parcel_lost_return_reason_id
										);
			}else if(
				$data['suborder_status'] == ORDER_STATUS['Delivered'] 
					                     ||
				$data['suborder_status'] == ORDER_STATUS['Complete']
		    ){

		    	//If admin Mode is ON- Show All Return Reasons available for Delivered status
		    	if( isset($data['admin_mode']) && $data['admin_mode'] == 'on'){
		    		//Return Reasons
		    		$result['quality'] = array(
								'title'   => 'Quality Reasons',
								'reasons' => $_quality_return_reason_id
							);
		    		//Replacement Reasons
		    		$result['replacement'] = array(
							'title'   => 'Replacement Reasons',
							'reasons' => $_replacement_return_reason_id
						);
		    		//Customer SOR Buy Back
		    		$result['sor_return'] = array(
		    									'title'   => 'SOR Returns',
												'reasons' => $_sor_return
		    			                    );

		    	}else{

			    	$date_today	 = date('d-m-Y');
			    	
			    	// Getting Max Date For return request with quality issue used for showing quality reason options
					$max_date_for_quality = date(
												'd-m-Y',
												strtotime(
													"+".$this->_limit_in_days_for_quality." days",
												  strtotime($data['status_date'])
												)
											);
			    	$quality     = strtotime($date_today) <= strtotime($max_date_for_quality);
			    	
			    	//Check for quality return reasons available or not
			    	if($quality){
						$result['quality'] = array(
							'title'   => 'Quality Reasons',
							'reasons' => $_quality_return_reason_id
						);
			    	}
			    	
					// Getting Max Date For return request with replacement issue used for showing replacement reason options
					$max_date_for_replacement = date(
													'd-m-Y',
													strtotime(
														 "+". $this->_limit_in_days_for_replacement. " days",
														 strtotime($data['status_date'])
													)
			    								);
			    	
			    	$replacement = strtotime($date_today) <= strtotime($max_date_for_replacement);

			    	//Check for replacement return reasons available or not
			    	if($replacement){
						$result['replacement'] = array(
							'title'   => 'Replacement Reasons',
							'reasons' => $_replacement_return_reason_id
						);
			    	}


			    	// Getting Max Date For return request with SOR Return Buy Back issue
			    	$order_product_id = (int)($data['order_product_id'] ?? 0);

			    	$sor_order_product           = new SorOrderProduct($this->registry);
			    	$sor_order_product_day_limit = $sor_order_product->getSorPeriodByOrderProductIds($order_product_id);
			    	$sor_order_product_day_limit = $sor_order_product_day_limit[$order_product_id] ?? 0;
					if($sor_order_product_day_limit > 0){

						$max_date_for_sor = date(
													'd-m-Y',
													strtotime(
														"+".$sor_order_product_day_limit." days",
													  strtotime($data['status_date'])
													)
												);
				    	$is_sor_returnable = strtotime($date_today) <= strtotime($max_date_for_sor);
				    	
				    	//Check for quality return reasons available or not
				    	if($is_sor_returnable){
							//Customer SOR Buy Back
		    				$result['sor_return'] = array(
				    									'title'   => 'SOR Returns',
														'reasons' => $_sor_return
				    			                    );
				    	}
			    	
					}
		    	}

			}
		}

		return $result;
	}

	/**
	 * @info: Public Method to check given return data is for generating CN
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function isCnGeneratable($return = array()){
		return false;
	}

	/**
	 * @info: Public Method to check given return data is for generating DN
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function isDnGeneratable($return = array()){
		return false;
	}

	/**
	 * @info: Public Method to check given return data is for generating CN
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function isQtyEditable($retrun_action_id){
		return 0;
	}

	/**
     * Public method to update old return as ative row = 0
     * @param: $return_id
     * @return: Boolen
     * @author: Nishu, March 2018
    */
    public function updateExistingAsInactive($return_id = 0){
    	if(empty($return_id) ) {
    		return;
    	}

    	$update_sql = "
                      UPDATE ".DB_PREFIX."return
                        SET 
                          active_row = 0
                        WHERE 
                          return_id = ". (int)$return_id ;
                          
        return $this->db->query($update_sql);
    }

    public function checkForMasterReturnId($order_id = 0, $op_id = 0){

    	$master_return_id = 0;
    	$sql = "
                SELECT 
                    ocr.master_return_id,
    			 	GROUP_CONCAT(DISTINCT ocr.order_product_id) AS order_product_ids
                FROM 
                	".DB_PREFIX."return as ocr
                INNER JOIN 
                	".DB_PREFIX."master_return as mr ON mr.master_return_id = ocr.master_return_id
                INNER JOIN 
                	".DB_PREFIX."order_product as oop ON ocr.order_product_id = oop.order_product_id
                WHERE
                     oop.order_id = ". (int)$order_id ."
                     AND ( mr.return_shipment_tracking_id IS NULL OR mr.return_shipment_tracking_id = 0)
                     AND ( ocr.credit_note_id IS NULL OR ocr.credit_note_id = 0 )
                     AND ( ocr.debit_note_id IS NULL OR ocr.debit_note_id = 0 )
                     AND ocr.active_row = 1
                GROUP BY 
                     ocr.master_return_id
    	       ";
    	if(!empty($op_id)){
    		$sql .= " HAVING ! FIND_IN_SET('".(int)$op_id."', order_product_ids)";
    	}

    	$sql .= " ORDER BY ocr.master_return_id DESC ";

    	$result = $this->db->query($sql);

    	if($result->num_rows > 0){
    		$master_return_id = $result->row['master_return_id'];
    	}
    	return $master_return_id;
    }

    public function resetReturnActiveStatusForReturnIds($active_status, $return_ids)
    {
    	if(!empty($return_ids)) {
    		$update_sql = "UPDATE " . DB_PREFIX . "return 
                              SET
                                active_row = '".(int)$active_status."'
                              WHERE
                                return_id  IN (". $this->db->escape($return_ids) .")
                              ";
          	$this->db->query($update_sql);
    	}
    }

    public function isReturnEditable(){
    	return true;
    }

    public function sendEmail($data = array()) {
    	return true;
    }

    public function sendSMS($data = array()) {
	   	return true;
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
    		//$return_action_id     = $return['return_action_id'];
    		$return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;

			if( $return_action_status == 1 ){
				$is_visible = true;
			}
    	}
    	return $is_visible;
    }

    public function getMailData($data = array(), $type='customer') {
    	$mail_data = array();
    	if(!empty($data)) {
    		$sno=0;
    		$order_product_ids = array();
			foreach($data as $key => $value) {
				$order_product_ids[] 	= isset($value['order_product_id'])?$value['order_product_id']:'';
				$mail_data['order_no'] 	= isset($value['form_data']['order_no'])?$value['form_data']['order_no']:'';
				if($type == 'customer') {
					$mail_data['name'] 	= isset($value['customer']['firstname'])?$value['customer']['firstname'] . ' ' . $value['customer']['lastname']:'';
					$mail_data['email']	= isset($value['customer']['email'])?$value['customer']['email']:'';
				}else{
					$mail_data['seller'] = $value['seller'][$value['seller_id']];
					$mail_data['name'] 	 = isset($mail_data['seller']['nickname'])?$mail_data['seller']['nickname']:'Seller';
					$mail_data['email']	 = isset($mail_data['seller']['email'])?$mail_data['seller']['email']:'';
				}
				
				$mail_data['telephone']	= isset($value['customer']['telephone'])?$value['customer']['telephone']:'';
				$mail_data['master_return_id']	= isset($value['master_return_id'])?$value['master_return_id']:'';
				$mail_data['order_id'] 		    = isset($value['order_id']) ? $value['order_id'] : ''; 
				$mail_data['customer_id'] 	    = isset($value['customer_id']) ? $value['customer_id'] : ''; 
				$mail_data['returns'][$sno++]= array(
											'return_id'				=> $value['return_id'],
											'image'					=> $value['image'],
											'model'					=> $value['model'],
											'return_reason'			=> $value['return_reason']['name'],
											'return_quantity' 		=> $value['form_data']['return_quantity'],
											'return_type'			=> $value['return_reason']['reason_type'],
											'shipping_method_id'	=> $value['form_data']['shipping_method_id'],
											'comment'				=> $value['comment']
										);
			}
    	}

    	$master_return_id 	= array_unique(array_column($data, 'master_return_id'));
		$mail_data['return_url'] = $this->getReturnUrl(array(
															'order_id'			=> isset($mail_data['order_id'])?$mail_data['order_id']:0,
															'master_return_id' 	=> (count($master_return_id)==1)?$master_return_id[0]:'',
															'customer_id' 		=> isset($mail_data['customer_id'])?$mail_data['customer_id']:0
															));
    	$mail_data['order_product_ids'] = $order_product_ids;

    	return $mail_data;
    }


    public function getSellerEmailData($data)
    {	
    	$seller_data = array();
    	if(!empty($data))
		{
			$order_id 		= $data['order_id'];
			$order_no 		= $data['order_no'];
			$customer_id 	= $data['customer_id'];
			$customer  		= $data['customer'];

			if(!empty($data['seller']))
			{
				foreach ($data['seller'] as $key => $value) {
					$seller_data[$key]['order_id'] = $order_id;
					$seller_data[$key]['order_no'] = $order_no; 
					$seller_data[$key]['customer_id'] = $customer_id; 
					$seller_id = $key;
					if(!empty($data['returns'])) {
						foreach($data['returns'] as $returns_key => $returns_value)
						{
							if($returns_value['seller_id'] == $seller_id)
							{
								$seller_data[$key]['returns'][$returns_value['return_id']] = $returns_value;
							}
						}
					}
					$seller_data[$key]['customer'] 	= $customer;
					$seller_data[$key]['seller'] 	= $data['seller'][$key];
				}
			}
		}
		return $seller_data;
    }

    public function getCustomerAccessToken(int $customer_id) {
	 	$sql = "SELECT `ws_access_token`
	 			FROM  " . DB_PREFIX . "customer 
                WHERE
                    customer_id  = '". (int)$customer_id ."'
                ";
      	$result = $this->db->query($sql);
      	if($result->num_rows) {
      		return $result->row['ws_access_token'];
      	}
      	return null;
	}

    public function getReturnUrl($data = array()){
		$return_url = $this->return_url;
		if( !empty($data['order_id']) 
			&&
			!empty($data['customer_id']) 
		) {

			$order_id 			= $data['order_id'];
			$master_return_id	= $data['master_return_id'];
			$customer_id		= $data['customer_id'];

			if(!empty($this->customer_ws_access_token)) {
				$return_url = $this->return_url . '?route=account/return&ctoken=' 
									   . $this->customer_ws_access_token 
									   . '&order_id=' . $order_id 
									   . '&master_return_id=' . $master_return_id ;
			}
		}

		//Short url default value as LongURL (Before converting)
		$short_url = $return_url;

		//Invoke method to convert Long URL into short URL
		$res = $this->convertUrlToShortUrl($return_url);

		//If APIs successfully converted Long URL into Short Url
		if($res['status'] == 1){
			$short_url = $res['message'];
		}

		return $short_url;
	}

	/**
    * Get TinyUrl/ShortUrl from Long URL
    * @param string $url
    * @return string
    * @author: Nishu, May 2018
    */
	public function convertUrlToShortUrl($longUrl = ''){
		if(empty($longUrl)){ return ''; }

		$ch = curl_init();
        $rt  = array();
        
        $url = 'https://api-ssl.bitly.com/v3/shorten?access_token='. BITLY_URL_SHORTNER_KEY .'&longUrl=' . urlencode($longUrl) . '&format=json';

       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       
       $data = curl_exec($ch);
       curl_close($ch);

       $data = json_decode($data);
       if(!empty($data)) {
       		if (isset($data->status_code) && ($data->status_code == '200')) {
	           $rt['status'] = 1;
	           $rt['message'] = $data->data->url;
	        } else {
	           $rt['status'] = 0;
	           $rt['message'] = $data->status_txt;
	        }
       }else{
       		$rt['status'] = 0;
	        $rt['message'] = 'Short URL generator API not responding.';
       }
       
       return $rt;
	}



    public function sendPushNotification($data = array())
    {
    	return true;
    }

    public function queuePushNotification($data = array()) {

    	$api_data = array();
    	if(!empty($data))
    	{
    		$api_data['notification_sending_time'] = date('Y-m-d h:i:s');
	    	
	    	$api_data['type'] = 'instant';
    		
    		foreach ($data as $key => $value) {

    			$api_data['data'][] = array(

		    				'type'	=> $value['type'],
		    				'id'	=> $value['id'], 
		    				'is_pn_to_send' => 1,
		    				'pn'	=> array(
		    								'msg_type'         => 1,
											'message'          => $value['message'],
											'master_return_id' => $value['master_return_id'],
											'order_no'         => $value['order_no'],
											'show_instantly'   => 1, 
											'title'		       => $value['title'],
											'vibrate'	       => 1,
											'mobile'	       => RETURN_HELPLINE,
											'sound'		       => 1
		    							),
		    				'is_sms_to_send'	=> !empty($value['sms'])? 1 : 0,
		    				'sms'				=> !empty($value['sms'])? $value['sms'] : array(),
		    				'is_email_to_send'	=> 0,
		    				'email'				=> array(),
		    				'email_to_head'		=> 0,
		    				'pn_to_head'		=> 0,
		    				'sms_to_head'		=> 0
		    			);
		    		}

    		//pr($api_data); die;
		    $sms_url = SMS_SENDING_API_URL;
    		$this->curl_post($api_data, $sms_url);
    	}
    }

    public function curl_post($data, $url='', $headers = array())
    {

    	if(empty($data) || empty($url)) {
    		return false;
    	}

    	if(empty($headers)) {
    		$headers = array('Content-Type: application/json');
    	}

        $curl      = curl_init();
        // Set SSL if required
        if (substr($url, 0, 5) == 'https') {
           curl_setopt($curl, CURLOPT_PORT, 443);
        }

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));            

        $result = curl_exec($curl);
        curl_close($curl); 
      
        return $result;
        //var_dump($result); die;
    }

    /**
	 * @info: Public method to send request[create/reply ticket] 
	 *			 			for each master return id to help desk system
	 * @param: $data  Array
	 * @author: MSA, August 2018
	*/
	public function sendToHelpDesk($data = array()) {

		if(!empty($data['ticket_data'])) {
			
			if(!empty($data['ticket_data']['master_ids'])) {

				foreach ($data['ticket_data']['master_ids'] as $master_return_id => $helpdesk_ticket_id) {
						
					if(!empty($helpdesk_ticket_id)) {

						$this->updateHelpDeskTicket($helpdesk_ticket_id, $data);

					}else{
						if(!empty($data['ticket_data']['action']) && $data['ticket_data']['action'] == 'pending' && empty($helpdesk_ticket_id))
						{
							$this->createHelpDeskTicket($master_return_id, $data);
						}else{
							$this->updateHelpDeskTicket($helpdesk_ticket_id, $data);
						}
					}
				}
			}
		}
	}
	
	/**
	 * @info: Public method to send request create new ticket on help desk
	 * @param: $master_return_id  master_return_id
	 * @param: $data  Array
	 * @author: MSA, August 2018
	*/
	public function createHelpDeskTicket($master_return_id, $data = array())
	{
		if(!empty($data['customer_id'])) {

			$obj            = new Customer($this->registry);
			$customer       = $obj->getCustomerById($data['customer_id']);

			$ticket_subject = $data['ticket_data']['title'];
			$description 	= $data['ticket_data']['description'];
			$order_no       = $data['order_no'];

         	$customer_access_token   	= $customer['ws_access_token'];
            $customer_id             	= $customer['customer_id'];
            $customer_name 			    = $customer['firstname'].' '.$customer['lastname'];
            $customer_email      		= $customer['email'];
            $customer_mobile         	= $customer['telephone'];
            $customer_type              = "BUYER";
            $customer_business_name 	= (!empty($data['customer']['customer_business_name']))
                                      		? $data['customer']['customer_business_name']
                                      		:'';
              
            $ticket_data = array(
                        "ticket_subject"    =>  $ticket_subject,
                        "description"       =>  $description,
                        "group_id"          =>  "3", // For Return id - 3
                        "priority"          =>  "2", // normal
                        "status"            =>  "1", // active status
                        "agent_id"          =>  "1", // fixed agent id
                        "source_id"         =>  "12", // fixed sourice id 12 for return penal
                        "notify_customer"   =>  "0", // notify to cusstomer 0/1
                        "customer_access_token" => $customer_access_token, //, 
                        "user_id"           => $customer_id, 
                        "wsb_id"            => $customer_id,
                        "customer_email"    => $customer_email, 
                        "customer_mobile"   => $customer_mobile,
                        "customer_name"     => $customer_name,
                        "customer_business_name" => $customer_business_name,
                        "customer_type"     => $customer_type,
                        "order_no"          => $order_no
                    );

            $ticket_url = HELP_TICKET_GENERATION_API_URL;
            $headers = array(
                                "api_key: ".HELP_TICKET_API_KEY,
                                "Content-Type: application/json"
                            );
            $result = $this->curl_post($ticket_data, $ticket_url, $headers);
            
            //echo 'Create ';pr($result);

            if(!empty($result)) {

                $result = json_decode($result,true);

                if(!empty($result['data']['ticket_data']['ticket_id'])) {

                	$ticket_id = $result['data']['ticket_data']['ticket_id'];

                	$this->updateHelpDeskTicketInMasterReturn($master_return_id, $ticket_id);

                }
            }
		}
	}

	public function updateHelpDeskTicketInMasterReturn($master_return_id, $helpdesk_ticket_id) {
		 if(!empty($master_return_id) && !empty($helpdesk_ticket_id)) {
		 	$update_sql = "UPDATE " . DB_PREFIX . "master_return 
                              SET
                                helpdesk_ticket_id = '".(int)$helpdesk_ticket_id."'
                              WHERE
                                master_return_id  = '". $this->db->escape($master_return_id) ."'
                              ";
          	$this->db->query($update_sql);
		 }
	}

	/**
	 * @info: Public method to send request for exiting help desk conversation with ticket id
	 * @param: $helpdesk_ticket_id  ticket id
	 * @param: $data  Array
	 * @author: MSA, August 2018
	*/
	public function updateHelpDeskTicket($helpdesk_ticket_id, $data)
	{
		if(!empty($helpdesk_ticket_id) && !empty($data['customer_id'])) {

			$obj            = new Customer($this->registry);
			$customer       = $obj->getCustomerById($data['customer_id']);

			$description 	= $data['ticket_data']['description'];
			
			$customer_access_token   	= $customer['ws_access_token'];
			$customer_id             	= $customer['customer_id'];

			$ticket_data = array(
                        'user_id'				=> $customer_id,
                        'customer_access_token'	=> $customer_access_token,
                        'source_id'				=> "12", // fixed sourice id 12 for return penal
                        'wsb_id'				=> $customer_id,
                        'ticket_id'				=> $helpdesk_ticket_id,
                        'notify_customer'		=> '1',
                        'customer_id'			=> $customer_id,
                        'body'					=> $description
                    );

			$ticket_url = HELP_TICKET_REPLY_API_URL;
            $headers = array(
                                "api_key: ".HELP_TICKET_API_KEY,
                                "Content-Type: application/json"
                            );
            $result = $this->curl_post($ticket_data, $ticket_url, $headers);
		}
	}

    public function getReturnLastStatus($returns)
    {
    	$return_ids = array();

    	$all_return_ids = array_keys($returns);

    	if(!empty($returns))
    	{
    		foreach($returns as $key => $value)
    		{
    			$return_ids[$key][] = array(
    				'master_return_id' => $value['master_return_id'],
    				'order_product_id' => $value['order_product_id'],
    			); 
    		}
    	}

    }

    /**
	 * @info: Public method to get active active return ids for given return ids
	 * @param: $return_ids Array
	 * @return: $active_return_ids Array
	 * @author: Nishu June 2018
    */
    public function UpdateInactiveReturnIdsForNewReturnIds($new_return_ids){
    	
    	if(empty($new_return_ids)){
    		return true;
    	}

    	$sql = "
    			UPDATE 
				    ".DB_PREFIX."return
				SET
					active_row = 0
				WHERE 
					
    	       ";
    	$whr = '';

    	foreach ($new_return_ids as $key => $value) {
    		$whr .=  "OR (
    					order_product_id = ". (int)$value['order_product_id'] ."
    					AND master_return_id = ". (int)$value['master_return_id'] ."
    					AND return_id < ". (int)$value['return_id'] ."
    					) ";
    	}
    	$sql .= trim($whr, "OR");
    	
    	//Execute Query
    	$this->db->query($sql);

    	return;
    }

    /**
	 * @info: Public Method to Check and mark as inactive active_row = 0 
	 *         If multiple active_row = 1 for single return 
	 *		    (i.e. GROUP on order_produc_id and   master_return_is) 
	 * @param: $db Object
	 * @author: Nishu, July 2018
    */
    public static function checkAndUpdateMultipleActiveRowForSingleReturn($db){

    	return true;

    	/*//Select Query for multiple rows active rows = 1
    	$sql = "
                SELECT
				  COUNT(*) AS Rows,
				  return_id
				FROM
				  oc_return
				WHERE
				  active_row = 1
				GROUP BY
				  order_product_id,
				  master_return_id
				HAVING 
					ROWS > 1
    		   ";
    	//Query Execute
    	$result = $db->query($sql);
    	if($result->num_rows > 0){
    		$return_ids = array_column($result->rows, 'return_id');
    		$update_sql = "
		                    UPDATE 
		                    	".DB_PREFIX."return
	                        SET 
	                          active_row = 0
	                        WHERE 
	                          return_id IN (". implode(',', $return_ids) . ") 
	                          AND active_row = 1 " ;
                          
        	return $db->query($update_sql);
    	}*/

    }
	
    /**
	 * @info: Public Method to update return reverse shipment status for cancel remark
	 * @param: integer $shipping_id
	 * @param: integer $cancel_status
	 * @param: string $cancel_label
	 * @author: MSA, July 2018
    */
    public function setReverseShipmentForCancelStatus($shipping_id, $cancel_status, $cancel_label='Cancelled')
    {
    	if(!empty($shipping_id)) {
    		$update_sql = "UPDATE " . DB_PREFIX . "return_shipment_tracking 
                              SET
                                is_cancel = '".(int)$cancel_status."',
                                status    = '".$this->db->escape($cancel_label)."',
                                equivalent_status = 'Problem',
                                date_cancelled = NOW()
                              WHERE
                                shipping_id =  '".(int)$shipping_id."'
                              ";
          	$this->db->query($update_sql);
    	}
    }

    /**
	 * @info: Public Method to update return forword shipment status for cancel remark
	 * @param: integer $shipping_id
	 * @param: integer $cancel_status
	 * @param: string $cancel_label
	 * @author: MSA, July 2018
    */
    public function setForwordShipmentForCancelStatus($shipping_id, $cancel_status, $cancel_label='Cancelled')
    {
    	if(!empty($shipping_id)) {
    		$update_sql = "UPDATE " . DB_PREFIX . "return_shipment_backto_customer 
                              SET
                                is_cancel = '".(int)$cancel_status."',
                                status    = '".$this->db->escape($cancel_label)."',
                                equivalent_status = 'Problem',
                                date_cancelled = NOW()
                              WHERE
                                shipping_id =  '".(int)$shipping_id."'
                              ";
          	$this->db->query($update_sql);
    	}
    }

    /**
	 * @info: Public Method to update shipment status for cancel [remove shipment tracking status with null] in master return table
	 * @param: integer $master_return_id
	 * @param: integer $tracking_no
	 * @author: MSA, July 2018
    */
    public function setShipmentCancelStatusInMasterReturn($master_return_ids){
    	if(!empty($master_return_ids)) {
    		$update_sql = "
    					UPDATE 
    						" . DB_PREFIX . "master_return 
                        SET
                            return_shipment_tracking_id = 0
                        WHERE
                            master_return_id IN (". $this->db->escape($master_return_ids).")
                        ";
          	$this->db->query($update_sql);
    	}
    }

    /**
	* Public method to merge missing combo's associated product return into $data
	* @param  Array $data
	* @return Array $data
	* @author MSA July 18, Updated by Nishu July 2018
	*/
	public function addMissingComboOrderProductsForReturn($data) {

		//Set $data with array keys as order_product_ids
		$data = array_combine(array_column($data, 'order_product_id'), $data);

		//All order_product_ids for all returns to be added
		$op_ids = array_column($data, 'order_product_id');

		//Create Object for ReturnInfo Class
		$return_info = new ReturnInfo($this);

		//Get all order_product_ids with group over combo_product_id as array key
		$combo_products = $return_info->getComboProductForOrderProductIds($op_ids);

		//Initialize as empty array
		$combo_data = array();

		//Check if there are exist order_products as combo products
		if(!empty($combo_products) && !empty($data)) {

			$data_group_by_combo_product_id = array();

			//Group by combo product id from $data
			foreach ($data as $opid => $d) {
				$seller_invoice_id = $d['seller_invoice_id'];
				$combo_id = $d['combo_id'];
				if(!isset($data_group_by_combo_product_id[$combo_id][$seller_invoice_id])){
					$data_group_by_combo_product_id[$combo_id][$seller_invoice_id] = array($opid);
				}else{
					$data_group_by_combo_product_id[$combo_id][$seller_invoice_id][] = $opid;
				}
			}

			//Loop for all combo products
			foreach ($combo_products as $combo_product_id => $si_wise_associate_op_ids) {
				foreach ($si_wise_associate_op_ids as $seller_invoice_id => $associate_op_ids) {
					$missing_combo_product_from_data = array_diff(
														  explode(',', $associate_op_ids), 
														  $data_group_by_combo_product_id[$combo_product_id][$seller_invoice_id]
														  );
					
					//If any combo order product is missing from $data
					if(!empty($missing_combo_product_from_data)){
						foreach ($missing_combo_product_from_data as $key => $missing_op_id) {
							//Already exist order_product_id wise data in $data
							$op_id_exist_in_data =  $data_group_by_combo_product_id[$combo_product_id][$seller_invoice_id][0];

							//Copy data for missing associated order_product to $data array
							$data[$missing_op_id] = $data[$op_id_exist_in_data];
							$data[$missing_op_id]['order_product_id'] = $missing_op_id;
						}
					}
				}
			}
		}

		//Refactor $data for combo products on basis of qty and reason
		$data = $this->refactorReturnDataForComboProducts($data, $combo_products);

		//Update $data's values 
		return $data;
	}

	/**
	 * @info: Public method to process combo products to add Return(s)
	 *         for Quantity and Return/Replacement Reason
	 * @author: Nishu, July 2018
	*/
	public function refactorReturnDataForComboProducts($data, $combo_products){
		
		if(!empty($data) && !empty($combo_products)){
			//////////////---------Start data manipulation---------//////////////

			//Return will added for minimum qty in order_products of same combo
			foreach ($combo_products as $combo_id => $si_wise_op_ids) { 
				//si_wise_op_ids means to seller invoice id wise data
				foreach ($si_wise_op_ids as $combo_id => $op_ids) {
					$op_ids = explode(',', $op_ids);

					$min_qty = 0;
					$shipping_method = '';
					$return_reason = 0;
					//Loop over order_product_ids combo_wise
					foreach ($op_ids as $op_id) {
						if(empty($min_qty) || $min_qty > $data[$op_id]['return_quantity']){
							$min_qty         = $data[$op_id]['return_quantity'];
							$shipping_method = $data[$op_id]['shipping_method'];
							$return_reason   = $data[$op_id]['return_reason'];
						}
					}
					
					//OverWrite return's qty of all products from same combo
					foreach ($op_ids as $op_id) {
						$data[$op_id]['return_quantity'] = $min_qty;
						$data[$op_id]['shipping_method'] = $shipping_method;
						$data[$op_id]['return_reason']   = $return_reason;
					}
				}
			}

			//////////////---------End data manipulation---------//////////////
		}

		return $data;
	}

	/**
	 * @info: Public method to get master id and help desk ticket id 
	 *				for return ids
	 * @param: string $return_ids
	 * @author: Nishu, July 2018
	*/
	public function getHelpDeskTicketsForReturnIds($return_ids)
	{
		$helpdesk_ticket_ids = array();
		if(!empty($return_ids)) {
		$sql = "SELECT 
						omr.master_return_id,
						omr.helpdesk_ticket_id
				FROM 
					" . DB_PREFIX . "return AS ocr
					INNER JOIN
					" . DB_PREFIX . "master_return AS omr ON omr.master_return_id = ocr.master_return_id
				WHERE
					ocr.return_id IN (".$this->db->escape($return_ids).")
				GROUP BY helpdesk_ticket_id		
				";
			$result = $this->db->query($sql);
		    if($result->num_rows) {
		      $helpdesk_ticket_ids =  $result->rows;
		    }
		    return $helpdesk_ticket_ids;
		}		
	}	

	public function getHelpDeskTicketsForCreditNoteIds($credit_note_id)
	{
		$helpdesk_ticket_ids = array();
		if(!empty($credit_note_id)) {
			$sql = "SELECT 
							omr.master_return_id,
							omr.helpdesk_ticket_id
					FROM 
						" . DB_PREFIX . "return AS ocr
						INNER JOIN
						" . DB_PREFIX . "master_return AS omr ON omr.master_return_id = ocr.master_return_id
					WHERE
						ocr.credit_note_id = ".(int)$credit_note_id."
					GROUP BY helpdesk_ticket_id		
					";
			$result = $this->db->query($sql);
		    if($result->num_rows) {
		      $helpdesk_ticket_ids =  $result->rows;
		    }
		    return $helpdesk_ticket_ids;		
		}

	}

}//End of Class