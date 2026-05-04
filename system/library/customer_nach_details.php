<?php
class CustomerNachDetails {
	private $customer_id;
	private $account_name;
	private $account_no;
	private $ifsc_code;

	public function __construct($registry) {

		if (method_exists($registry, 'get')) {
			$this->config  = $registry->get('config');
			$this->db      = $registry->get('db');
			$this->user    = $registry->get('user') ?? array();
		}else{
	        $this->config  = $registry->config;
	        $this->db      = $registry->db;
	        $this->user    = $registry->user ?? array();
		}
		
        if (method_exists($registry, 'get') && $registry->get('customer_id') > 0)  {
            $this->customer_id = $registry->get('customer_id');
        } else if ( !empty($registry->customer_id) ) {
            $this->customer_id = $registry->customer_id;
        }else{
        	$this->customer_id = 0;
        }
	}

	public function getCustomerId() {
		return $this->customer_id;
	}
	
	public function getAccountName() {
		return $this->account_name;
	}

	public function getAccountNo() {
		return $this->account_no;
	}

	public function getIfscCode() {
		return $this->ifsc_code;
	}

	//Setters
	public function setCustomerId($customer_id) {
		$this->customer_id = $customer_id;
	}
	
	public function setAccountName($account_name) {
		$this->account_name = $account_name;
	}

	public function setAccountNo($account_no) {
		$this->account_no = $account_no;
	}

	public function setIfscCode($ifsc_code) {
		$this->ifsc_code = $ifsc_code;
	}

	/**
     * @ info : Public method to get customer related NACH details if exist
     * @param : $customer_id 
     * @return: array
     * @author: Nishu, Jan 2019
	*/
	public function getCustomerNachDetails($customer_id){
		$result = array();
		if(!empty($customer_id)){

			$sql = "
                    SELECT
                    	id,
                    	customer_id,
                    	account_name,
                    	account_no,
                    	ifsc_code,
                    	umrn_no,
                    	bank_type,
                    	lan_no,
                    	active_status,
                    	status
                    FROM
                    	".DB_PREFIX."customer_nach_details
                    WHERE 
                    	customer_id = ".(int)$customer_id."
                    ORDER BY
                    	active_status DESC, status DESC
			       ";
			$qry = $this->db->query($sql);
			if($qry->num_rows > 0 ){
				$result = $qry->rows;
			}
		}
		return $result;

	}

	/**
	 * @info: Private method to check duplicate dat entry in DB to verify before every insertion or deletion
	 * @param: array
	 * @author: Nishu, April 2019
	*/
	public function checkForDuplicateNachDetails($data){
		$duplicate_data = array();
		if(!empty($data)){
			//Check for Duplicate entry, if exist throw error
			$select_sql = "
							SELECT
								id,
								customer_id,
								account_name,
								account_no,
								ifsc_code,
								umrn_no,
								bank_type,
								lan_no
							FROM
								".DB_PREFIX."customer_nach_details
							WHERE
							 	account_no   = '".$this->db->escape($data['account_no'])."' AND
							 	ifsc_code    = '".$this->db->escape($data['ifsc_code'])."' AND 
							 	umrn_no      = '".$this->db->escape($data['umrn_no'])."' 
						    LIMIT 1 
			              ";
			$qry = $this->db->query($select_sql);
			if($qry->num_rows > 0){
				$duplicate_data = $qry->row;		
			}
		}
		return $duplicate_data;
	}

	/**
     * @info: Private method to get Total count of exist NACH account(s) for given customer_id
     * @param : integer $customer_id
     * @author: Nishu, April 2019
	*/
	private function getTotalCountOfNachAccountsForCustomerId(int $customer_id){
		$count = 0;
		if(!empty($customer_id)){
			$sql = "
					SELECT
						COUNT(id) AS total_count
					FROM
						".DB_PREFIX."customer_nach_details
					WHERE
						customer_id = ".(int)$customer_id."
						AND status = 1
						AND active_status = 1
			       ";
			$qry = $this->db->query($sql);
			if($qry->num_rows > 0){
				$count = $qry->row['total_count'];
			}
		}
		return $count;
	}

	/**
     * @info: Public method to update or insert data into oc_customer_nach_details
     * @param: Array
     * @author: Nishu, Feb 2019
	*/
	public function insertNachDetails(array $data){
		
		if(!empty($data['customer_id'])){

			//Check for Duplicate entry, if exist throw error
			$duplicate_data = $this->checkForDuplicateNachDetails($data);

			if(!empty($duplicate_data)){
				return 'Given NACH A/c details already exist for Customer Id: '.$duplicate_data['customer_id'];
			}

			//Check, is it first NACH account details to add against specific customer_id,
			//Then mark it as active NACH account
			$total_count = $this->getTotalCountOfNachAccountsForCustomerId((int)$data['customer_id']);
			$active_status = 0;

			if($total_count == 0){
				$active_status = 1;
			}

			//Insert new NACH Account details
			$insert_sql = "
							INSERT INTO 
								".DB_PREFIX."customer_nach_details
							SET
								customer_id  = '".(int)$data['customer_id']."',
								account_name = '".$this->db->escape(trim($data['account_name']))."',
							 	account_no   = '".$this->db->escape(trim($data['account_no']))."',
							 	ifsc_code    = '".$this->db->escape(trim($data['ifsc_code']))."',
							 	umrn_no      = '".$this->db->escape(trim($data['umrn_no']))."',
							 	bank_type    = '".$this->db->escape(trim($data['bank_type']))."',
							 	active_status= ".(int)$active_status."
						  ";
			$data['lan_no'] = trim($data['lan_no']);
			if(!empty($data['lan_no'])){
				$insert_sql .= " , lan_no       = '".$this->db->escape($data['lan_no'])."'";
			}
			$this->db->query($insert_sql);
			$id = (int)$this->db->getLastId();

			$admin_change_data['table_id'] = (int)$id;

	    	$admin_change_data['new_value']  = trim($data['account_name']);
	    	$admin_change_data['field_name'] = 'account_name';
	    	$this->logAdminChangeLog($admin_change_data);

	    	$admin_change_data['new_value']  = trim($data['account_no']);
	    	$admin_change_data['field_name'] = 'account_no';
	    	$this->logAdminChangeLog($admin_change_data);

	    	$admin_change_data['new_value']  = trim($data['ifsc_code']);
	    	$admin_change_data['field_name'] = 'ifsc_code';
	    	$this->logAdminChangeLog($admin_change_data);

	    	$admin_change_data['new_value']  = trim($data['umrn_no']);
	    	$admin_change_data['field_name'] = 'umrn_no';
	    	$this->logAdminChangeLog($admin_change_data);

	    	$admin_change_data['new_value']  = trim($data['bank_type']);
	    	$admin_change_data['field_name'] = 'bank_type';
	    	$this->logAdminChangeLog($admin_change_data);

	    	$admin_change_data['new_value']  = trim($data['lan_no']);
	    	$admin_change_data['field_name'] = 'lan_no';
	    	$this->logAdminChangeLog($admin_change_data);
		}
		return 'success';
	}

	/**
     * @info: Public method to update or insert data into oc_customer_nach_details
     * @param: Array
     * @author: Nishu, Feb 2019
	*/
	public function updateCustomerNachDetails(array $data){
		
		if(!empty($data['id'])){

			//Check for Duplicate entry, if exist throw error
			$duplicate_data = $this->checkForDuplicateNachDetails($data);

			if(!empty($duplicate_data)){
				if(
					$duplicate_data['id'] == $data['id'] && 
					trim($duplicate_data['account_name']) == trim($data['account_name']) &&
					trim($duplicate_data['account_no'])   == trim($data['account_no']) &&
					trim($duplicate_data['ifsc_code'])    == trim($data['ifsc_code']) &&
					trim($duplicate_data['umrn_no'])      == trim($data['umrn_no']) &&
					trim($duplicate_data['lan_no'])       == trim($data['lan_no']) &&
					trim($duplicate_data['bank_type'])    == trim($data['bank_type']) 
				){
				// No need to chek for rest of the fields, as they are constrained using UNIQUE key
					return "There is no change observed in the submitted new NACH A/c details.";
				}elseif(
					$duplicate_data['id'] != $data['id'] &&
					trim($duplicate_data['account_name']) == trim($data['account_name']) &&
					trim($duplicate_data['account_no'])   == trim($data['account_no']) &&
					trim($duplicate_data['ifsc_code'])    == trim($data['ifsc_code']) &&
					trim($duplicate_data['umrn_no'])      == trim($data['umrn_no']) &&
					trim($duplicate_data['lan_no'])       == trim($data['lan_no']) &&
					trim($duplicate_data['bank_type'])    == trim($data['bank_type']) 
				){
					return "Given NACH A/c details already exist for Customer Id: ".$duplicate_data['customer_id'];
				}
			}

			$sql = "
                    SELECT 
                    	* 
                    FROM
                    	".DB_PREFIX."customer_nach_details
                    WHERE
                    	id = ".(int)$data['id']."
			       ";
			$result = $this->db->query($sql);
			
			if($result->num_rows > 0){
				$is_updatable = 0;
				$update_sql = "
								UPDATE 
									".DB_PREFIX."customer_nach_details
								SET
									";
				
				//Set Data to add into admin_change_log
            	$admin_change_data = array();
				$admin_change_data['table_id'] = (int)$data['id'];
				
				if(trim($result->row['account_name']) <> trim($data['account_name'])){
					$is_updatable = 1;
					$update_sql .= " account_name = '".$this->db->escape(trim($data['account_name']))."',";

					$admin_change_data['old_value']  = trim($result->row['account_name']);
                	$admin_change_data['new_value']  = trim($data['account_name']);
                	$admin_change_data['field_name'] = 'account_name';
                	$this->logAdminChangeLog($admin_change_data);
				}
				if(trim($result->row['account_no']) <> trim($data['account_no'])){
					$is_updatable = 1;
					$update_sql .= " account_no   = '".$this->db->escape(trim($data['account_no']))."',";

					$admin_change_data['old_value']  = trim($result->row['account_no']);
                	$admin_change_data['new_value']  = trim($data['account_no']);
                	$admin_change_data['field_name'] = 'account_no';
                	$this->logAdminChangeLog($admin_change_data);
				}
				if(trim($result->row['ifsc_code']) <> trim($data['ifsc_code'])){
					$is_updatable = 1;
					$update_sql .= " ifsc_code    = '".$this->db->escape(trim($data['ifsc_code']))."',";
					$admin_change_data['old_value']  = trim($result->row['ifsc_code']);
                	$admin_change_data['new_value']  = trim($data['ifsc_code']);
                	$admin_change_data['field_name'] = 'ifsc_code';
                	$this->logAdminChangeLog($admin_change_data);
				}
				if(trim($result->row['umrn_no']) <> trim($data['umrn_no'])){
					$is_updatable = 1;
					$update_sql .= " umrn_no      = '".$this->db->escape(trim($data['umrn_no']))."',";
					$admin_change_data['old_value']  = trim($result->row['umrn_no']);
                	$admin_change_data['new_value']  = trim($data['umrn_no']);
                	$admin_change_data['field_name'] = 'umrn_no';
                	$this->logAdminChangeLog($admin_change_data);
				}
				if(trim($result->row['bank_type']) <> trim($data['bank_type'])){
					$is_updatable = 1;
					$update_sql .= " bank_type      = '".$this->db->escape(trim($data['bank_type']))."',";
					$admin_change_data['old_value']  = trim($result->row['bank_type']);
                	$admin_change_data['new_value']  = trim($data['bank_type']);
                	$admin_change_data['field_name'] = 'bank_type';
                	$this->logAdminChangeLog($admin_change_data);
				}
				if(trim($result->row['lan_no']) <> trim($data['lan_no'])){
					$is_updatable = 1;
					$update_sql .= " lan_no      = '".$this->db->escape(trim($data['lan_no']))."',";
					$admin_change_data['old_value']  = trim($result->row['lan_no']);
                	$admin_change_data['new_value']  = trim($data['lan_no']);
                	$admin_change_data['field_name'] = 'lan_no';
                	$this->logAdminChangeLog($admin_change_data);
				}
				//To check condition at least one value is changed 
				if($is_updatable == 1){
					$update_sql  = trim($update_sql, ',');
					$update_sql .= " WHERE id = ".(int)$data['id'];
					
					$qry = $this->db->query($update_sql);
				}
			}
		}
		return 'success';
	}

	/**
	 * Public method to log admin change log
	 * @author : Nishu
	*/
	public function logAdminChangeLog($admin_change_data){
		/////////////// Insert a row in customer change log/////////////////////
          
        $admin_change_data['user_id']       = $this->user->getId() ?? 0;
        $admin_change_data['name']          = 'Customer NACH Details';
        $admin_change_data['username']      = $this->user->getUserName()["name"] ?? '';
        $admin_change_data['table_name']    = 'oc_customer_nach_details';
        $admin_change_data['source_field']  = 'customer_credits';
        $admin_change_data['ref_url']       = 'sale/customer_credits';
        $admin_change_data['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
        $admin_change_data['ip_address']    = $_SERVER['REMOTE_ADDR'];
        $admin_change_data['file_location'] = 'sale/customer_credits';
        $admin_change_data['user_type']     = 'Administrator';

        //Call dynamic static function for entry into admin change log
        CommonLib::addAdminChangeLog($this->db, $admin_change_data);
	}

	/**
     * @info: Public method to update or insert data into oc_customer_nach_details
     * @param: Array
     * @author: Nishu, Feb 2019
	*/
	public function markNachDetailAsDefault(array $data){
		
		if(!empty($data['id'])){

			//Update All other NACH A/c as active_status = 0 
			//Update the input NACH A/c as active_status = 1 and status = 1
			$update_sql = "
							UPDATE 
								".DB_PREFIX."customer_nach_details
							SET
							 	active_status = CASE id 
                                                  WHEN " . (int)$data['id'] . " THEN 1 
                                                  ELSE 0 
                                                END, 
								status = CASE id 
                                           WHEN " . (int)$data['id'] . " THEN 1 
                                           ELSE status 
                                         END 
							WHERE
								customer_id = ".(int)$data['customer_id']."
			              ";
			$qry = $this->db->query($update_sql);

			//Set Data to add into admin_change_log
	    	$admin_change_data = array();
			$admin_change_data['table_id'] = (int)$data['id'];
			
			$admin_change_data['old_value']  = 0;
        	$admin_change_data['new_value']  = 1;
        	$admin_change_data['field_name'] = 'active_status';
        	$this->logAdminChangeLog($admin_change_data);
			
		}
		return;
	}

	/**
     * @info: Public method to change availability 
     *			from available to unavailable 
     *			and unavailable to available customer related NACH A/c
     * @author: Nishu, April 2019
	*/
	public function changeAvailabilityNachDetail(array $data){
		
		if(!empty($data['id'])){
			$select_sql = "
							SELECT
								status,
								active_status
							FROM
								".DB_PREFIX."customer_nach_details
							WHERE
								id = ".(int)$data['id']."
			              ";
			$qry = $this->db->query($select_sql);

			if($qry->num_rows > 0 && $qry->row['active_status'] != 1){
				$old_status = 0;
				$new_status = 1;

				if($qry->row['status'] == 1){
					$old_status = 1;
					$new_status = 0;
				}
				$update_sql = "
								UPDATE 
									".DB_PREFIX."customer_nach_details
								SET
								 	status = ".(int)$new_status."
								WHERE
									id = ".(int)$data['id']."
				              ";
				$this->db->query($update_sql);

				//Set Data to add into admin_change_log
		    	$admin_change_data = array();
				$admin_change_data['table_id'] = (int)$data['id'];
				
				$admin_change_data['old_value']  = $old_status;
	        	$admin_change_data['new_value']  = $new_status;
	        	$admin_change_data['field_name'] = 'status';
	        	$this->logAdminChangeLog($admin_change_data);
			}

		}
		return;
	}

	/**
     * @info: Public method to Move NACH account details from One customer a/c to another customer a/c 
     *         Logic (4 cases to handle): 
     *             1. If current status value of NACH a/c is 0 i.e. not active, then A/c can't move
     *             2. If active but not default - then move and make that NACH a/c as default a/c for to_customer_id
     *             3. If active and also default (but sigle a/c details exist with to_customer_id) - then move and make that NACH a/c as default a/c for to_customer_id
     *             4. If active and also default (but having multiple a/c details exist with to_customer_id) - then can't  move until mark as undefault that NACH a/c for to_customer_id and after applicable case(2)
     * @author: Nishu, April 2019
	*/
	public function moveNachAcDetail(array $data){
		$resp = "";

		if(!empty($data['nach_id'])){
			$select_sql = "
							SELECT
								status,
								active_status
							FROM
								".DB_PREFIX."customer_nach_details
							WHERE
								id = ".(int)$data['nach_id']."
								AND status = 1
			              ";
			$qry = $this->db->query($select_sql);

			if($qry->num_rows > 0){

				//NACH a/c's  current statuses
				$row = $qry->row;
				$ready_to_move = false;

				if($row['active_status'] != 1){ //Handle case 2 
					$ready_to_move = true;
				}else{
					//To handle case 3 and 4
					$select_sql = "
									SELECT
										id
									FROM
										".DB_PREFIX."customer_nach_details
									WHERE
										id != ".(int)$data['nach_id']."
										AND customer_id = ". (int)$data['from_customer_id'] ."
										AND status = 1 
									LIMIT 1 
					              ";
					              
					$qry1 = $this->db->query($select_sql);
					if($qry1->num_rows <= 0){ // Means case 3
						$ready_to_move = true;
					}else{ //Means case 4
						$resp = "Please change this NACH to UNDEFAULT first, in order to move to other customer ID: '". $data['to_customer_id'] ."'!!!";
					}
				}


				//Means That NACH a/c is ready to move for other customer_id (i.e. case 2 and 3)
				if($ready_to_move){
					$update_sql = "
								UPDATE 
									".DB_PREFIX."customer_nach_details
								SET
								 	customer_id = ".(int)$data['to_customer_id']."
								WHERE
									id = ".(int)$data['nach_id']."
				              ";
					$this->db->query($update_sql);

					//Set Data to add into admin_change_log
			    	$admin_change_data = array();
					$admin_change_data['table_id'] = (int)$data['nach_id'];
					
					$admin_change_data['old_value']  = $data['from_customer_id'];
		        	$admin_change_data['new_value']  = $data['to_customer_id'];
		        	$admin_change_data['field_name'] = 'customer_id';
		        	$this->logAdminChangeLog($admin_change_data);

		        	//Mark that NACH a/c as default account for to_customer_id
		        	$to_customer_data = array();
		        	$to_customer_data['id']          = (int)$data['nach_id'];
		        	$to_customer_data['customer_id'] = (int)$data['to_customer_id'];
		        	$this->markNachDetailAsDefault($to_customer_data);
				}
				
			}else{
				$resp = "NACH details is in INACTIVE state right now. Please Activated it first!!!";
			}

		}
		return $resp;
	}

}
