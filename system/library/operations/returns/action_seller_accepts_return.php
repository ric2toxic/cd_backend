 <?php
require_once(DIR_SYSTEM . 'library/operations/returns/return_action_base.php');

class ActionSellerAcceptsReturn extends ReturnActionBase 
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

}//End of Class
