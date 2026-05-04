<?php

declare(strict_types=1);

/*
 * 	NachBankBase - Abstract Base Class for NACH bank type specification
 * 	@author @Nishu Rani, Jan 2018
 */
abstract class NachBankBase
{
    // Registry and common use objects - for ease of access
    protected $registry;
	protected $load;
	protected $db;
    protected $config;
    protected $user;
    
    /*
     * Constructor
     * @param: $registry - Registry class object
     */
    public function __construct(Registry $registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->load = $registry->get('load');
        $this->config = $registry->get('config');
        $this->user = $registry->get('user');
	}

	/*
     * Abstract function - to be redefined mandatorily in child classes. 
     * It is used to generate NACH Sheet and return the filepath string.
     * @param $data - array 
     * @return string - filepath
	 */
	abstract public function generateNachSheet(array $data) : string;
    
}//End of Class
