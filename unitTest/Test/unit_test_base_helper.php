 <?php
require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . 'library/user.php');
require_once(DIR_SYSTEM . 'library/config.php');

use PHPUnit\Framework\TestCase;

class UnitTestBaseHelper extends TestCase
{	

	protected $load;
	protected $db;
	protected $user;
	protected $config;

	public function __construct() {
		parent::__construct();
		
		$this->initializeDb();
	}

	//Set Member atribute $db
	protected function initializeDb(){
		/*$db_name  = array('master' => 'wholesaleboxtest',
                          'slave'  => 'wholesaleboxtest');
		// Creating DB object
       $this->db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $db_name, DB_PORT);
       */
       
       $this->db = new Database\DB( DBTEST_SERVERS );

	}
	//Get Member atribute $db
	protected function getDb(){
		return $this->db;
	}

	//Set Member atribute $user
	protected function initializeUser($user_id = 0){
	   // Creating User object
       $user_obj = new User();
       $user_obj->setDb($this->db);
       $user_obj->setId($user_id);
       //Set User member variable
       $this->user = $user_obj;
	}
	//Get Member atribute $user
	protected function getUser(){
		return $this->user;
	}

	/**
	 * @info method to set config member nad setting values from setting table of DB
	 * This method can only be invoked after intializing DB
	*/
	protected function setConfig(){
		// Config
		$config = new Config();
		//Set config member variable
        $this->config = $config;

		// Settings from DB
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0'");
		foreach ($query->rows as $setting) {
			if (!$setting['serialized']) {
				$this->config->set($setting['key'], $setting['value']);
			} else {
				$this->config->set($setting['key'], unserialize($setting['value']));
			}
		}
	}
	//Get Member atribute $config
	protected function getConfig(){
		return $this->config;
	}

}//End of Class

?>