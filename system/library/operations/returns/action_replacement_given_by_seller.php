 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionReplacementGivenBySeller extends ReturnActionBase 
{	
	protected $action_name 	  = 'DELIVER TO SELLER WAITING REPLACEMENT';

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
    		$return      = $data['returns'][$return_id];
    		$return_type = $return['return_type'];
    		$seller_id   = $return['product']['seller_id'];
            
            //$return_action_id     = $return['return_action_id'];
            $return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;

    		$seller_invoice_generate = 1; //Assign default value

    		//Get $seller_invoice_generate value from seller details to check seller is from our Store or this is franchise seller
    		if(isset($data['sellers'][$seller_id])){
    			$seller_invoice_generate = $data['sellers'][$seller_id]['seller_invoice_generate'];
    		}
    		
			if( 
                $return_action_status == 1
                        &&
				strtoupper($return_type) == 'REPLACEMENT'
						&&
				empty($return['credit_note_id']) 
						&& 
				empty($return['debit_note_id'])
				        &&
                (
                    !in_array($return['return_action_id'] ,  array(
                                                            RETURN_ACTION_IDS['Goods_Received'],
                                                            RETURN_ACTION_IDS['Extra_Goods_Received'],
                                                            RETURN_ACTION_IDS['Short_Goods_Received']
                                                            )
                    )
                        ||
                    $seller_invoice_generate == 0
                )

			){
				$is_visible = true;
			}
    	}
    	return $is_visible;
    }

		
}//End of Class
