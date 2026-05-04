 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionWrongItemReceivedFromCourier extends ReturnActionBase 
{	
	protected $action_name 	  = '';

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry 	= $registry;
		
		if (method_exists($registry, 'get')) {
            $this->db 		= $registry->get('db');
            $this->load 	= $registry->get('load');
            $this->user   	= $registry->get('user');
        } else {
            $this->db 		= $registry->db;
            $this->load 	= $registry->load;
            $this->user   	= $registry->user;
        }
	}

	/**
	 * @info: Public Method to check given return data is for generating DN
	 * @param: $return Array
	 * @return: Boolen
	 * @author: Nishu, March 2018
	*/
	public function isDnGeneratable($return = array()){
		return true;
	}

}//End of Class
