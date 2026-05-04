 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionReturnComplete extends ReturnActionBase 
{	
	
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
    		$return           = $data['returns'][$return_id];
    		$op_id            = $return['order_product_id'];
    		$dn_id            = $return['debit_note_id'];
    		$cn_id            = $return['credit_note_id'];
            $seller_id        = $return['product']['seller_id'];
            $buyer_invoice_id = $return['product']['buyer_invoice_id'];

            //$return_action_id     = $return['return_action_id'];
            $return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;
            
            $seller_invoice_generate = 1; //Assign default value

            //Get $seller_invoice_generate value from seller details to check seller is from our Store or this is franchise seller
            if(isset($data['sellers'][$seller_id])){
                $seller_invoice_generate = $data['sellers'][$seller_id]['seller_invoice_generate'];
            }
            
            //Set CreditNote array credit_note_id wise
    		$credit_notes = array_combine(
    			                     array_column($data['credit_notes'], 'credit_note_id'), 
    			                     $data['credit_notes']
    			               );
    		//If CreditNote is generated DebitNote must be generated to mark return completed
    		if(
                $return_action_status == 1
                    &&
                (
                    empty($buyer_invoice_id)
                        ||
                    (
                        isset($credit_notes[$cn_id])
                            &&
                        $credit_notes[$cn_id]['credit_note_status'] == 1
                    )
                )
    				&& 
    			(
	    			(
	    			isset($data['all_debit_notes'][$dn_id])
	    				&&
	    			$data['all_debit_notes'][$dn_id]['debit_note_status'] == 1
	    			)
	    			    ||
	    			in_array($return['return_action_id'] ,  array(
	    													RETURN_ACTION_IDS['Loss_Booked_By_WSB'],
	    													RETURN_ACTION_IDS['Goods_Taken_On_WSB_Books_And_Relisted'],
                                                            RETURN_ACTION_IDS['ActionTransferToWSBBooksWithoutRelisting']
	    													)
	    			 )
	   			)
                    &&
                (
                    !in_array($return['return_action_id'] ,  array(
                                                             RETURN_ACTION_IDS['DN_Generated_For_Seller'],
                                                             RETURN_ACTION_IDS['DN_Generate_for_Logistic_Company'],
                                                             RETURN_ACTION_IDS['CN_For_Client'],
                                                             RETURN_ACTION_IDS['Refunded_Initiated'],

                                                             RETURN_ACTION_IDS['Goods_Received'],
                                                             RETURN_ACTION_IDS['Short_Goods_Received'],
                                                             RETURN_ACTION_IDS['Extra_Goods_Received'],
                                                             RETURN_ACTION_IDS['Goods_Given_To_Pickup_Boy'],
                                                             RETURN_ACTION_IDS['Goods_Handed_Over_To_Seller']
                                                            )
                    )
                        ||
                    $seller_invoice_generate == 0
                )
    		){
    		   $is_visible = true; //This Return action will be visible as post action
    		}
    	}
    	return $is_visible;
    }


		
}//End of Class
