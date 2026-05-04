 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionGoodsGivenPickupBoy extends ReturnActionBase 
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
	* @author: Nishu, May 2018
	*/
	public function checkPostActionVisibility($return_id, $data, $return_action_id, $check_for){
        if($check_for == 'admin_mode'){
            return true;
        }
        
    	$is_visible = false;
    	if(!empty($return_id)){
    		$return               = $data['returns'][$return_id];
    		//$return_action_id     = $return['return_action_id'];
            $return_action_status = !empty($data['return_actions'][$return_action_id])?$data['return_actions'][$return_action_id]['status'] : 0;

    		//Check for replacement or CN and DN must be generated
    		if(
                $return_action_status == 1
                    &&
                (
        			strtoupper($return['return_type']) == "REPLACEMENT"
        				OR 
        			(
        				!empty($return['credit_note_id'])
        					AND 
        				!empty($return['debit_note_id'])
        			)
                )
    		){
    			$is_visible = true;
    		}
    	}
    	return $is_visible;
    }
	
}//End of Class
