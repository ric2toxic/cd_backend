<?php
require_once(DIR_SYSTEM . 'library/commonlib.php');

class ProductChangeLog{

	/**
     * Constructor
     */
    public function __construct($registry) {
    	if(method_exists($registry,'get')){
			$this->_registry = $registry;
	    	$this->_session = $registry->get('session');
	    	$this->_url = $registry->get('url');
	    	$this->_db = $registry->get('db');
	    	$this->_request = $registry->get('request');

	    	if($registry->get('customer')){
	    		$this->_user = $registry->get('customer');
	    	} else {
	    		$this->_user = $registry->get('user');	
	    	}	    	
		}
		else{
			$this->_registry = $registry;
			$this->_db = $registry->db;
	    	$this->_session = $registry->session;
	    	$this->_url = $registry->url;
	    	$this->_db = $registry->db;
	    	$this->_request = $registry->request;
	    	if($registry->customer){
	    		$this->_user = $registry->customer;
	    	} else {
	    		$this->_user = $registry->user;
	    	} 
		}
    }

	/**
	* Method For Record log about any information change of product
	* @param $product_id : Integer of product id
	* @param $changes_data : array of records
	* @return NULL
	* @author vikas, 2017
	*/
	public function recordLogs( $product_id, $changes_data, $source_field, $route = null, $table_name='oc_product', $comment='' ){

		if (empty($changes_data)) {
			// No data to log
			return false;
		}

		$additional_data = array();

		// User Details
		$user_id = !empty((int)$this->_user->getId()) ? (int)$this->_user->getId() : 0 ;
        if (method_exists($this->_user, 'getUserName') ) {
        	$user_name = $this->_user->getUserName($this->_user->getId())['username'];
			$name      = $this->_user->getUserName($this->_user->getId())['name'];
			$user_type = $this->_user->getGroupName();
		} else {
			$user_name = 'Automatic Cron';
			$name      = 'Automatic Cron';
			$user_type = 'System';
		}

		// Source Field and Reference URL / Route / Calling source code
		$sourcefield = !empty($this->_db->escape($source_field)) ? $this->_db->escape($source_field) : $source_field;

		$dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
		$class = isset($dbt[1]['class']) ? $dbt[1]['class'] : '';
		$function = isset($dbt[1]['function']) ? $dbt[1]['function'] : '';
		$ref_url = $class .'/'. $function;

		$url = !empty($ref_url) ? $ref_url : '';
		if (!empty($route)) {
			$url = $route;
		}

		// IP and server details
		$ip = $this->_request->getIpAddress;
        $server = $_SERVER['HTTP_USER_AGENT'];

        // Populating $additional_data array
        $additional_data['user_id'] = $user_id;
        $additional_data['username'] = $user_name;
        $additional_data['name'] = $name;
        $additional_data['user_type'] = $user_type;
        $additional_data['ip'] = $ip;
        $additional_data['server'] = $server;
        $additional_data['source_field'] = $source_field;
        $additional_data['url'] = $url;
        $additional_data['table_name'] = $table_name;

        // Logging the data
        ProductChangeLog::recordLogsStatic($this->_db, $product_id, $changes_data, $additional_data, $comment);

        return true;
	}


	/**
	* Static Method For Record log about any information change of product 
	* @param $db : Database object
	* @param $product_id : Integer of product id
	* @param $changes_data : array of records
	* @param $additional_data: array consisting of user_id, username, name, user_type, $ip, $server, $source_field, $url, $table_name
	* @return NULL
	* @author vikas, 2017
	*/
	public static function recordLogsStatic($db, $product_id, $changes_data, $additional_data, $comment = ''){

		if (empty($changes_data)) {
			// No data to log
			return false;
		}

		// Extracting Additional Data
		$user_id      = !empty($additional_data['user_id'])      ? $additional_data['user_id']      : 0 ;
        $username     = !empty($additional_data['username'])     ? $additional_data['username']     : '' ;
        $name         = !empty($additional_data['name'])         ? $additional_data['name']         : '' ;
        $user_type    = !empty($additional_data['user_type'])    ? $additional_data['user_type']    : '' ;
        $ip           = !empty($additional_data['ip'])           ? $additional_data['ip']           : '' ;
        $server       = !empty($additional_data['server'])       ? $additional_data['server']       : '' ;
        $source_field = !empty($additional_data['source_field']) ? $additional_data['source_field'] : '' ;
        $url          = !empty($additional_data['url'])          ? $additional_data['url']          : '' ;
        $table_name   = !empty($additional_data['table_name'])   ? $additional_data['table_name']   : '' ;

		if( !empty($changes_data)){

			foreach ($changes_data as $key => $value) {
                
                if( substr($key,0,14) == 'product_option'){
                    $key = 'options';
                }

                //Set Data to add into admin_change_log
                $admin_change_data                  = array();
                $admin_change_data['table_id']      = (int)$product_id;
                $admin_change_data['source_field']  = $source_field;
                $admin_change_data['field_name']    = $key;
                $admin_change_data['old_value']     = $value['old_value'];
                $admin_change_data['new_value']     = $value['new_value'];
                $admin_change_data['table_name']    = $table_name;
                $admin_change_data['user_id']       = (int)$user_id;
                $admin_change_data['username']      = $username;
                $admin_change_data['name']          = $name;
                $admin_change_data['user_type']     = $user_type;
                $admin_change_data['ref_url']       = $url;
                $admin_change_data['ip_address']    = $ip;
                $admin_change_data['user_agent']    = $server;
                $admin_change_data['file_location'] = $url;
                $admin_change_data['comment']       = '';
                
                if (!empty($comment)) {
                    $admin_change_data['comment'] = $comment;
                }

                //Call dynamic static function for entry into admin change log
                CommonLib::addAdminChangeLog($db, $admin_change_data);

			    /*$sql = "INSERT INTO " . DB_PREFIX . "admin_change_log 
			            SET table_id = '" . (int)$product_id . "',
						source_field = '" . $db->escape($source_field) . "',
						field_name   = '" . $db->escape($key) . "',
						old_value    = '" . $db->escape($value['old_value']) . "',
						new_value    = '" . $db->escape($value['new_value']) . "',
						date_added   = NOW(),
						table_name   = '" . $db->escape($table_name) . "',
						user_id      = '" .(int)$user_id. "',
						username     = '" . $db->escape($username) . "',
						name         = '" . $db->escape($name) . "',
						user_type    = '" . $db->escape($user_type) . "',
						ref_url      = '" . $db->escape($url) . "',
						ip_address   = '" . $db->escape($ip) . "',
						user_agent   = '" . $db->escape($server) . "',
						file_location = '". $db->escape($url) ."' ";

                if (!empty($comment)) {
                    $sql .= " , comment = '" . $db->escape($comment) ."'";
                }
				$db->query($sql);*/
			}
		}

		return true;
	}


	/**
	* Method For Record log about any information change of product by seller
	* @param $product_id : Integer of product id
	* @param $changes_data : array of records
	* @return NULL
	* @author vikas, 2017
	*/
	public function sellerProductChangeLogs( $product_id, $changes_data ){
		$nickname = SellerInfo::getSellerFirmDetails($this->_db,$this->_user->getId());
		if(!empty($nickname)){
			$nickname = $nickname['nickname'];
		} else {
			$nickname = 'admin';
		}

		if( !empty($changes_data)){

			foreach ($changes_data as $key => $value) {
                
                if( substr($key,0,14) == 'product_option'){
                    $key = 'options';
                } 

				$sql = "INSERT INTO " . DB_PREFIX . "seller_change_log
						SET seller_id 	= '" . (int)$this->_user->getId() . "',
							product_id 	= '" . (int)$product_id . "',
							product     = '" . $this->_db->escape($value['sku']) . "',
							nickname    = '" . $this->_db->escape($nickname) . "',
							updated_type= '" . $this->_db->escape($key) . "',
							old 		= '" . $this->_db->escape($value['old_value']) . "',
							new 		= '" . $this->_db->escape($value['new_value']) . "',
							modified 	= NOW()";
				$this->_db->query($sql);
			}
		}
	}

	/**
	* Method For Record Log when products sync to solr
	* @return NULL
	* @author vikas, 2018
	*/
	public function recordLogsForProductSyncToSolr( $product_id, $changes_data ){
		if( !empty($changes_data)){
               
			$sql = "INSERT INTO " . DB_PREFIX . "product_change_log
					SET product_id 	= '" . (int)$product_id . "',
						table_name  = '" . $this->_db->escape($changes_data['table_name']) . "',
						solr_synced = '0',
						change_details = '" . $this->_db->escape($changes_data['change_details']) . "',
						date_added 	= NOW()";
			$this->_db->query($sql);
		}
	}
}