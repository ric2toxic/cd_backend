 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionReplacementNote extends ReturnActionBase 
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

    /**
    * @info: Public Method to Check return action is visible or not as post return action
    *         Default return value is : true
    * @param: $return_id Integer, $data Array
    * @return: Boolen
    * @author: MSA, August 2018
    */
    public function checkPostActionVisibility($return_id, $data, $return_action_id, $check_for){
            
        $is_visible = false;
        if(!empty($return_id)){
            $return  = $data['returns'][$return_id];
            $op_id   = $return['order_product_id'];
            $product = array();

            if(isset($data['products'][$op_id])){
                $product     = $data['products'][$op_id];
                $suborder_id = $product['suborder_id'];
                $suborder    = $data['suborder'][$suborder_id];
                $return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;
                if(
                    $return_action_status == 1
                        &&
                    !empty($product['seller_invoice_id']) 
                        &&
                    !empty($return['return_reason']['reason_type'])
                        &&     
                    $return['return_reason']['reason_type'] == 'REPLACEMENT'
                        &&
                    empty($return['debit_note_id'])    
                        &&
                    empty($return['credit_note_id'])        
                       
                ){
                    $is_visible = true;
                }
            }
        }
        return $is_visible;
    }

		
}//End of Class
