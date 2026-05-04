<?php
/**
  * SrmLead model
  * This model use in SRM  
  * @author:Manish
 */
class ModelSrmLead extends Model {

    private $lead_log = 1;
    public $db_srm;

    function __construct() {
        //SRM Database
        $this->db_srm = new Database\DB( DBSRM_SERVERS );
    }

    public function checkOpenAccounts($mobile) {
        if ($this->lead_log == 1) {
            $srm_query = "SELECT id FROM accounts WHERE mobile= " . "'" . $mobile . "'";

            return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row['id'] : 0;
        } else {
            return 0;
        }
    }

    public function checkExistingContacts($mobile) {
        if ($this->lead_log == 1) {
            $srm_query = "SELECT id FROM contacts WHERE mobile= " . "'" . $mobile . "'";

            return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row['id'] : 0;
        } else {
            return 0;
        }
    }

    public function checkExistingLead($mobile) {
        if ($mobile != 'skipping') {
            if ($this->lead_log == 1) {
                $mobile = substr(trim($mobile), -10);
                $srm_query = "SELECT id FROM `leads` "
                        . "WHERE "
                        . "mobile LIKE '%" . $mobile . "' "
                        . "OR alternate_numbers LIKE '%" . $mobile . "%' "
                        . "LIMIT 1 ";

                return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row['id'] : 0;
            } else {
                return 0;
            }
        }
    }

    public function getLeadDetailsInCrm($mobile) {
        if ($mobile != 'skipping') {
            if ($this->lead_log == 1) {
                $mobile = substr(trim($mobile), -10);
                $srm_query = "SELECT id, user_id FROM `leads` "
                        . "WHERE "
                        . "mobile LIKE '%" . $mobile . "%' "
                        . "OR alternate_numbers LIKE '%" . $mobile . "%' "
                        . "LIMIT 1 ";

                return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row : 0;
            } else {
                return 0;
            }
        }
    }

    public function getLeadOrder($data) {        
        if ($this->lead_log == 1) {
            foreach ($data as $datas) {
                //Finding the appropriate sales staff
                if (!empty($datas['sales_telephone'])) {
                    $user_id = $this->db_srm->query(
                            "SELECT id FROM users 
								WHERE mobile LIKE " . $datas['sales_telephone']
                    );
                    if (!empty($user_id->num_rows)) {
                        $user_id = $user_id->row['id'];
                    }
                }

                //Finding whether ther are any open leads
                if ($lead_id = $this->checkExistingLead($datas['telephone'])) {
                    $sql = "UPDATE leads SET
					status = 'OPEN', 
					name     					=" . "'" . $datas['buyer_name'] . "', 
					business_name     			=" . "'" . $datas['shipping_company'] . "', 
					email    					=" . "'" . $datas['email'] . "', 
					address  					=" . "'" . $datas['shipping_address_1'] . "', 
					country_id 					=" . "'" . $datas['shipping_country_id'] . "', 
					state_id   					=" . "'" . $datas['shipping_zone_id'] . "', 
					is_registered 				= '1', 
					priority   					= '1', 
					order_no					=" . "'" . $datas['order_no'] . "', 
					zip      					=" . "'" . $datas['shipping_postcode'] . "', 
					status_update_date 			= NOW()";

                    if (isset($user_id)) {
                        $sql .= ", user_id=" . "'" . $user_id . "'";
                    }

                    $sql .= " WHERE id =" . $lead_id;

                    //Seletecting any open tasks in case of open lead
                    //and updating for followup date
                    //$this->createLog($lead_id, 'Updating 7 days followup lead');
                    if ($this->db_srm->query($sql)) {
                        $data = array('status' => 'OPEN');
                        $find_task_sql = $this->findTask($lead_id, $data);
                        /*
                          "SELECT * FROM tasks t
                          WHERE t.lead_id= ".$lead_id."
                          AND t.status = 'OPEN'";
                          $find_task_sql = $this->db_srm->query($find_task_sql); */

                        //Finding any open tasks for the given lead
                        if ($find_task_sql->num_rows > 1) {
                            
                        } elseif ($find_task_sql->num_rows == 1) {
                            $task = $find_task_sql->row;
                            $task_sql1 = "UPDATE tasks SET 
							followup_date 		=" . "'" . date('Y-m-d') . "', 
							user_id 			=" . "'" . $user_id . "', 
							status 				= 'CLOSED', 
							modified 			= NOW(),  
							status_update_date 	= NOW() 
							WHERE id =" . $task['id'];

                            $this->db_srm->query($task_sql1);

                            $task_sql = " INSERT INTO tasks SET 
							user_id 			=" . "'" . $user_id . "', 
							status_update_date 	= NOW(), 
							name				= 'Delivered 7 days before',   
							status 				= 'OPEN', 
							created 			= NOW(),  
							modified 			= NOW(),  
							lead_id 			=" . "'" . $lead_id . "', 
							followup_date 		=" . "'" . date('Y-m-d') . "'";
                            // $this->db_srm->query($task_sql); 
                        } else {
                            $task_sql = "INSERT INTO tasks SET 
							user_id 			=" . "'" . $user_id . "', 
							status_update_date 	= NOW(), 
							name				= 'Delivered 7 days before',   
							status 				= 'OPEN', 
							created 			= NOW(),  
							lead_id 			=" . "'" . $lead_id . "', 
							followup_date 		=" . "'" . date('Y-m-d') . "'";
                        }
                        $this->db_srm->query($task_sql);
                    }
                } else {
                    // Inserting in case of no open leads found
                    $sql = "INSERT INTO leads SET 
						name     					=" . "'" . $datas['buyer_name'] . "' , 
						business_name     			=" . "'" . $datas['shipping_company'] . "' , 
						email    					=" . "'" . $datas['email'] . "', 
						user_id    					=" . "'" . $user_id . "', 
						mobile    					=" . "'" . $datas['telephone'] . "', 
						address  					=" . "'" . $datas['shipping_address_1'] . "' , 
						city     					=" . "'" . $datas['shipping_city'] . "', 
						country_id 					=" . "'" . $datas['shipping_country_id'] . "' , 
						state_id   					=" . "'" . $datas['shipping_zone_id'] . "', 
						is_registered 				= '1', 
						status   					= 'OPEN', 
						priority   					= '1' , 
						source_id					= '7', 
						order_no					=" . "'" . $datas['order_no'] . "', 
						zip      					=" . "'" . $datas['shipping_postcode'] . "', 
						status_update_date 			= NOW()
						";

                    if ($this->db_srm->query($sql)) {
                        // code for opening a new task

                        $new_lead_id = $this->db_srm->getLastId();

                        $new_task_sql = "INSERT INTO tasks SET 
						user_id 			=" . "'" . $user_id . "', 
						status_update_date 	= NOW(), 
						name				= 'Delivered 7 days before',   
						status 				= 'OPEN', 
						created 			= NOW(),  
						lead_id 			=" . "'" . $new_lead_id . "', 
						followup_date 		=" . "'" . date('Y-m-d') . "'";
                        $this->db_srm->query($new_task_sql);
                    }
                    $task_comm = $this->db_srm->getLastId();
                    $this->addTaskComment($task_comm, "Delivered Before 7 days order date - " . isset($datas['order_date']) ? $datas['order_date'] : '');


                    // echo $lead_id = $this->db_srm->getLastId(); die;
                }
                //////////////////
                //End crm entry //
                //////////////////
            }
            // return true;
        } else {
            return 0;
        }
    }

    /*public function createLog($lead_id, $message) {
        $l_d = serialize($this->getLeadData($lead_id));
        $this->db_srm->query(
                "INSERT INTO activity_changelog SET 
			lead_id =" . $lead_id . " , 
			log ='" . $l_d . "' , 
			name ='" . $message . "' , 
			created = NOW() ,
			modified = NOW()"
        );
    }*/

    public function getLeadData($lead_id) {
      return $this->db_srm->query("SELECT id,cart FROM leads WHERE id = " . $lead_id)->row;
    }
    
    public function updateLead($data, $mobile, $message, $customer_id = '') {     
        if ($lead_id = $this->checkExistingLead($mobile)) {
            $q = "UPDATE leads SET ";
            $i = 0;
            //echo "<pre>"; print_r($data);
            $data['user_id'] = $this->getLeadUser($lead_id);
            foreach ($data as $key => $value) {

                $value = addslashes($value);                               

                if ($key == 'status' && $value != 'OPEN') {
                        $value = 'OPEN';
                }
                

                if ($key == 'country_id') {

                    $get_country_crm = $this->db_srm->query("SELECT id FROM countries WHERE name = '" . $value . "' ")->row;

                    if (!empty($get_country_crm)) {

                        $country_id = $get_country_crm['id'];
                    } else {

                        $add_country_crm = "INSERT INTO countries SET 
                            name      =   '" . $value . "',               
                            status      =   '1' ";

                        $this->db_srm->query($add_country_crm);
                        $country_id = $this->db_srm->getLastId();
                  }
                    $value = $country_id;
                }
                

                if ($key == 'state_id') {

                    $get_state_crm = $this->db_srm->query("SELECT id FROM states WHERE name = '" . $value . "' ")->row;
 
                    if (!empty($get_state_crm)) {
                        
                        $state_id = $get_state_crm['id'];
                        $stateId = $get_state_crm['id'];

                    } else {

                        $add_state_crm  = "INSERT INTO states SET 
                            name        =   '" . $value . "',                 
                            country_id  =   '" . $country_id . "', 
                            status      =   '1'";

                        $this->db_srm->query($add_state_crm);
                        $state_id = $this->db_srm->getLastId();
                    }

                    $value = $state_id;
                }
                    

                if ($key == 'city') {
                    $get_city_crm = $this->db_srm->query("SELECT id FROM cities WHERE name = '" . $value . "' ")->row;

                    if (!empty($get_city_crm)) {

                        $city_id = $get_city_crm['id'];
                    } else {

                        $add_city_crm = "INSERT INTO cities SET 
                          name        =   '" . $value . "', 
                          state_id    =   '" . $state_id . "',   
                          country_id  =   '" . $country_id . "', 
                          status      =   '1', 
                          created     =   NOW(),  
                          modified    =   NOW()";

                        $this->db_srm->query($add_city_crm);
                        $city_id = $this->db_srm->getLastId();
                    }

                    $key = 'city_id';
                    $value = $city_id;
                }

                if ($i == count($data) - 1) {
                    $q .= $key . " = " . "'" . $value . "'";
                } else {
                    $q .= $key . " = " . "'" . $value . "', ";
                }

                $i++;
            }
            $q .= ", modified = NOW() ";
             $q .= isset($cust) ? ", customer_id =" . $cust . ", is_registered = '1'" : ", is_registered = '1'";
            $q .= " WHERE id=" . $lead_id;

            $this->addSellerId($lead_id, $customer_id);
            $this->db_srm->query($q);
            return true;
        }
    }

    public function addLead($data, $mobile, $customer_id = '', $message = '') {
       
      $lead_id = $this->checkExistingLead($mobile);
      $seller = '';
      $i = 0;

      if (!empty($customer_id)) {
          $seller = $customer_id;
      }

      if ($lead_id > 0) {
          $this->updateLead($data, $mobile, 'Updating already existing lead on signup', $customer_id);
          return true;
      }
      
      $date = date('Y-m-d H:i:s');
      $keys = array();
      $values = array();
      
      foreach ($data as $key => $value) {
  			$keys[] = $key;
  			$values[] = "'".$value."'";           
        $i++;
      }
        
      if (isset($seller)) {
  			$other_keys = array('created','status_update_date','mobile','customer_id');		
  			$other_values = array('NOW()',"'".$date."'","'".$mobile."'","'".$seller."'");

		  } else {
  			$other_keys = array('created','status_update_date','mobile');		
  			$other_values = array('NOW()',"'".$date."'","'".$mobile."'");
		  }
      
      $keys_arr = array_merge($keys,$other_keys);
      $values_arr = array_merge($values,$other_values);   
     
  		$final_keys_arr = implode(',',$keys_arr);
  		$final_values_arr = implode(',',$values_arr);
		  
      $q = "INSERT INTO 
				leads ($final_keys_arr) 
				SELECT $final_values_arr FROM DUAL WHERE NOT EXISTS (SELECT id FROM leads WHERE mobile='$mobile')";
		    
        //echo "<pre>"; print_r($q); exit;		
		
        $this->db_srm->query($q);
        $lead_id = $this->db_srm->getLastId();

        if (isset($seller) && !empty($seller)) {
            $this->addSellerId($lead_id, $seller);
        }

        if (empty($data['user_id'])) {
            $user_id = $this->getLeadUser($lead_id);

        } else {
            $user_id = $data['user_id'];
        }

        $task_sql = "INSERT INTO tasks SET 
    			status_update_date 	= NOW(), 
    			name				= 'New Signup',   
    			status 				= 'OPEN', 
    			priority 			= '1', 
    			user_id 			= " . "'" . $user_id . "', 
    			created 			= NOW(),  
    			modified 			= NOW(),  
    			lead_id 			=" . "'" . $lead_id . "', 
    			followup_date 		=" . "'" . date('Y-m-d') . "'";

        $this->db_srm->query($task_sql);

        $last_tasks_id = $this->db_srm->getLastId();

        $this->addTaskComment($last_tasks_id, $message . " User registered on date..- " . date('Y-m-d'));
        return true;
    }


   public function addSellerId($lead_id, $seller_id) {
      
        $lead_id = trim($lead_id);
        $seller_id = trim($seller_id);
        $lead_seller_id = $this->checkExistingSeller($lead_id, $seller_id);
        
        if ($lead_seller_id === FALSE) {            
            $q = "INSERT INTO  leads_website_seller_ids SET 

                  lead_id   = $lead_id, 
                  seller_id = $seller_id,   
                  created   = NOW(),
                  modified  = NOW(),
                  status    = 1";

            try {
                  $this->db_srm->query($q);
                } 

            catch(Exception $e) {
                return true;
            }
        }
    }

    public function checkExistingSeller($lead_id, $seller_id) {

        $srm_query = "SELECT * FROM `leads_website_seller_ids` "
                . "WHERE "
                . "lead_id = $lead_id AND seller_id = $seller_id LIMIT 1";

        return $this->db_srm->query($srm_query)->num_rows ? TRUE : FALSE;
    }


    public function closeOrderedTasks($task_id) {
        $this->db_srm->query(
                "UPDATE tasks SET 
			status = 'CLOSED' , 
			modified = " . "'" . date('Y-m-d H:i:s') . "' 
			WHERE id =" . $task_id
        );
    }

    public function findTask($lead_id, $data) {
        $sql = "SELECT * FROM tasks WHERE lead_id=" . $lead_id;
        $sql .= isset($data['status']) ? " AND status =" . "'" . $data['status'] . "'" : '';

        return $this->db_srm->query($sql);
    }

    public function updateLeadFromSellerId($lead_data, $customer_id) {
        if (!empty($lead = $this->db_srm->query("SELECT id, mobile FROM leads WHERE customer_id = " . $customer_id)->row)) {
            $this->updateLead($lead_data, $lead['mobile'], 'profile updated from app', $customer_id);
        }
    }

    public function getUserData() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $user_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $user_ip = $_SERVER['HTTP_X_FORWARD'];
        }

        //for gettting the OS platform
        $os_platform = "Unknown OS Platform";
        $os_array = array(
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        // for getting the browser of the user
        $browser = "Unknown Browser";

        $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        $user_os = $os_platform;
        $user_browser = $browser;

        $date_added = date('Y:m:d H:i:s');

        $utm_source = '';
        $utm_medium = '';
        $utm_campgain = '';
        $landing_page = '';
        if (isset($_COOKIE['utm_source'])) {
            $utm_source = $_COOKIE['utm_source'];
        }
        if (isset($_COOKIE['utm_medium'])) {
            $utm_medium = $_COOKIE['utm_medium'];
        }
        if (isset($_COOKIE['utm_campgain'])) {
            $utm_campgain = $_COOKIE['utm_campgain'];
        }
        if (isset($_COOKIE['firstlanding'])) {
            $landing_page = base64_decode($_COOKIE['firstlanding']);
        }

        return array('user_os' => $user_os,
            'user_browser' => $user_browser,
            'date_added' => $date_added,
            'user_ip' => $user_ip,
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campgain' => $utm_campgain,
            'firstlanding' => $landing_page
        );
    }

    public function addTaskComment($task_id, $comm) {
        $user_q = $this->db_srm->query("SELECT user_id FROM tasks WHERE id = " . $task_id);
                
        if ($user_q->num_rows) {
            $user_id = $user_q->row['user_id'];
        } else {
            $user_id = '';
        }

        $sql = "INSERT INTO task_comments SET 
              		task_id =" . "'" . $task_id . "',
                  user_id =" . "'" . $user_id . "',
              		comment =" . "'" . $comm . "', ";
        
        $sql .= "created = NOW(),
              		modified= NOW() 
              		";
        $this->db_srm->query($sql);
    }

    public function updateLeadStatus($data) {
        $q = "UPDATE leads SET ";
        $q .= " lead_status_id = " . $data['order_status_id'];
        if ($data['order_status_id'] == '5' || $data['order_status_id'] == '15') {
            $q .= ", is_ordered = 1 ";
        }
        $q .= ", modified = NOW() ";
        $q .= " WHERE order_no = '" . $this->db_srm->escape($data['order_no']) . "'";

        if ($this->db_srm->query($q)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get staff mobile number who is assigned to lead
     * @param string $lead_mobile_no
     * @return string mobile contact number of support person if assigned else will return 0
     */
    public function getStaffDataAssignedToLead($lead_mobile_no) {

        $srm_query = " SELECT u.`name`, u.mobile FROM users u
                       INNER JOIN leads l
                       ON l.user_id = u.id
                       WHERE (l.mobile like '%" . $lead_mobile_no . "' 
                       OR l.alternate_numbers like '%" . $lead_mobile_no . "%')
                       AND u.status = 1
                      ";
        return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row : 0;
    }

    public function getCrmCityId($city_name) {
        $srm_query = "SELECT id FROM cities c WHERE name = '" . $city_name . "'";
        return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row['id'] : 0;
    }

    public function getCrmStateId($state_name) {
        $srm_query = "SELECT id FROM states c WHERE name = '" . $state_name . "'";
        return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row['id'] : 0;
    }

    public function getCrmCountryId($country_name) {
        $srm_query = "SELECT id FROM countries c WHERE name = '" . $country_name . "'";
        return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row['id'] : 0;
    }

    public function getUserFromCity($city_id) {
        $srm_query = "SELECT user_id FROM city_user_data cd INNER JOIN users u on (u.id = cd.user_id) WHERE u.status = 1 AND city_id = '" . $city_id . "'";
        return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row['user_id'] : 0;
    }

    public function getLeadUser($lead_id) {
        $srm_query = "SELECT l.user_id FROM leads l
                      INNER JOIN users u 
                      ON u.id = l.user_id 
                      WHERE u.status = 1 
                       AND l.id = " . $lead_id;
        return $this->db_srm->query($srm_query)->num_rows ? $this->db_srm->query($srm_query)->row['user_id'] : 0;
    }

    public function getCrmTeamLeadEmailId($user_id) {
        $srm_query = "SELECT u.name, u.email
                        FROM users AS u
                        INNER JOIN user_agents AS ua on (u.id = ua.user_id) 
                        WHERE u.status = 1 AND u.role_id = 5 AND ua.agent_id IN ( " . $user_id ." )";
        return $this->db_srm->query($srm_query);
    }

    //get all commission Rates
    private function getOrderCommissionRates() {
        $sql = "SELECT code,`key`,value
                FROM settings
                WHERE `key` like '%order_commission_rate%'";
        $results = $this->db_srm->query($sql)->rows;
        $result = array();
        if(!empty($results)){
            foreach($results as $value){
                $result[$value['code']][$value['key']] = $value['value']; 
            }
        }
        return $result;
    }

    public function updateAgentOrder($order_id, $order_no, $customer_id, $crm_user_id, $repeate_order_count, $amount, $type) {
        $remarks = array();
        
        // Get User Role and it's defined Commission From user table it may be user on an agent.
        $user = $this->db_srm->query("SELECT role_id, name, first_order_commission_rate, next_order_commission_rate FROM users WHERE id = ".$crm_user_id)->row;
        if(isset($user['role_id'])){
            $user_role_id = $user['role_id'];
        } else{
            return "Role is not defined in CRM";
        }

        // Get agent_id and user_id from lead table
         $lead = $this->db_srm->query("SELECT lead_id from leads_website_customer_ids WHERE customer_id = " . $customer_id)->row;

            if(empty($lead)){
                $lead = $this->db_srm->query("SELECT id from leads WHERE customer_id = " . $customer_id)->row;
           }


        //print_r($lead); die; or crm_user_id 217 is case of via.
        if(empty($lead) || $crm_user_id == 217 ){ 
        // lead is not present in Crm 
            //return "Customer's Lead is not present in CRM.";
            return "done";
        } else {
            $is_expired = $this->db_srm->query("SELECT is_expired from leads WHERE customer_id = ". $customer_id." AND is_expired = 1 AND agent_id = user_id")->row;
            if(!empty($is_expired)){
                return "This Lead is Expired. so, Order can't be tag.";
            }
        }

        $commission_rates = $this->getOrderCommissionRates(); // Get All commission Rates which are in setting table
        $order_by = $crm_user_id;
        if($user_role_id == 7) { // Ordered by AGENT
            // Here we check this customer is assigned to Ordered tag user or not
            $lead_user = $this->db_srm->query("SELECT l.agent_id, u.name from leads l INNER JOIN users u ON (l.agent_id = u.id) WHERE l.customer_id =". $customer_id." AND is_expired = 0")->row;
            if(empty($lead_user) && $type == 'add'){ // If lead is not Related to Agent
                $u_id = $this->db_srm->query("SELECT l.user_id, u.name from leads l INNER JOIN users u ON (l.user_id = u.id) WHERE l.customer_id =". $customer_id)->row;
                $return['return_msg'] = "This Customer is assigned to User ".$u_id['name'];
                $return['force_tag'] = 1;
                return $return;
            }
            if(isset($lead_user['agent_id']) && $type == 'add' && $lead_user['agent_id'] != $crm_user_id){
                $return['return_msg'] = "This Customer is assigned to Agent ".$lead_user['agent_id'];
                $return['force_tag'] = 1;
                return $return;
            }

            $agent_id 								= $crm_user_id;
            $agent_first_order_commission_rate 		= ($user['first_order_commission_rate'] == 0)?$commission_rates['agent']['first_order_commission_rate']:$user['first_order_commission_rate'];
            $agent_next_order_commission_rate 		= ($user['next_order_commission_rate'] == 0)?$commission_rates['agent']['next_order_commission_rate']:$user['next_order_commission_rate'];
            $sales_next_order_commission_rate 		= 0;

        } else {
            // get customer is related to agent or not
            $sql ="SELECT l.agent_id, l.user_id, u.name, u.first_order_commission_rate, u.next_order_commission_rate from leads l INNER JOIN users u ON (l.agent_id = u.id) WHERE l.customer_id = ". $customer_id ." AND l.user_id = ".$crm_user_id." AND u.role_id = 7 AND l.is_expired = 0";
            $agent = $this->db_srm->query($sql)->row;
            $agent_id = isset($agent['agent_id'])?$agent['agent_id']:'';

            if($agent_id == '') { // if customer is not related to agent then return true
                $u_id = $this->db_srm->query("SELECT l.user_id, u.name from leads l INNER JOIN users u ON (l.user_id = u.id) WHERE l.customer_id =". $customer_id)->row;
                if(isset($u_id['user_id']) && $type == 'add' && $u_id['user_id'] != $crm_user_id){
                    $return['return_msg'] = "This Customer is assigned to User ".$u_id['name'];
                    $return['force_tag'] = 1;
                    return $return;
                }
                return "done";
            }elseif($repeate_order_count == 1){
                return "This is an Agent First Order So, it will be tag agent : ". $agent['name'];
            }else{
                $agent_first_order_commission_rate 		= ($agent['first_order_commission_rate'] == 0)?$commission_rates['agent']['first_order_commission_rate']:$agent['first_order_commission_rate'];
                $agent_next_order_commission_rate 		= ($agent['next_order_commission_rate'] == 0)?$commission_rates['agent']['next_order_commission_rate']:$agent['next_order_commission_rate'];
                $sales_next_order_commission_rate   	= ($user['next_order_commission_rate'] == 0)?$commission_rates['telesales']['next_order_commission_rate']:$user['next_order_commission_rate'];
            }
        }

        $this->db_srm->query("DELETE FROM agent_commission where order_id = ". $order_id ." AND order_no = ".$order_no."");

        $order_commission_rate = ($repeate_order_count == 1)?$agent_first_order_commission_rate:$agent_next_order_commission_rate;
        $tentative_amount = ($amount*$order_commission_rate)/100;

        if($type == 'add'){
            $sql = "INSERT INTO agent_commission SET 
                    agent_id					= ". $agent_id .", 
                    order_by 					= ". $order_by .", 
                    order_id 					= ". $order_id .", 
                    order_no 					= '". $order_no ."', 
                    customer_id 				= ".$customer_id.", 
                    tentative_amount            = ".$tentative_amount.", 
                    status 						= 'tentative', 
                    agent_first_order_commission_rate 		= ".$agent_first_order_commission_rate.", 
                    agent_next_order_commission_rate 		= ".$agent_next_order_commission_rate.", 
                    sales_next_order_commission_rate        = ".$sales_next_order_commission_rate.", 
                    order_commission_rate 		= 0, 
                    repeate_order_count 		= ".$repeate_order_count.", 
                    date_added 					= now()";
            $this->db_srm->query($sql);

            if(isset($lead['lead_id'])){
                $lead_id    = $lead['lead_id'];
            }else{
                $lead_id    = $lead['id'];
            }
            $customer_info  = $this->db_srm->query("SELECT name, business_name FROM leads WHERE id =".$lead_id)->row;
            $customer_info['business_name'] = !empty($customer_info['business_name'])?"(".$customer_info['business_name'].")":'';

            $agent_info         = $this->db_srm->query("SELECT name, mobile FROM users WHERE id = ".$agent_id)->row;
            $cur_amt            = "Rs. ".$amount;
            $cur_tentative_amt  = "Rs. ".$tentative_amount."/-";
            $message            = "Dear ".$agent_info['name'].", ".$customer_info['name'].$customer_info['business_name']." has placed an order of amount ".$cur_amt.", Your tentative commission would be ".$cur_tentative_amt." for this order.";
            $send_sms = new SMS($message, $agent_info['mobile']);
			$send_sms->sendMessage();
        } elseif($type == 'remove') {
            $sql = "DELETE FROM agent_commission WHERE order_id = ". $order_id;
            $this->db_srm->query($sql);
        }
        return "done";
    }

    public function syncSalesStaff($user_id){
        $users_date = $this->db_srm->query("SELECT created, modified FROM users WHERE id = ".$user_id)->row;
        return $users_date;
    }

    public function getTotalCrmDataCount($start_date, $end_date) {
		$sql = "SELECT count(id) as total_lead 
				FROM leads 
				WHERE status != 'MERGE_DEAD'
                AND created BETWEEN STR_TO_DATE('".$start_date."', '%Y-%m-%d') AND STR_TO_DATE('".$end_date."', '%Y-%m-%d')";
		return $this->db_srm->query( $sql )->row;
	}

    public function getNetAppInstalledByStaffSales($user_id) {
        $sql = "SELECT count(l.id) as app_installed
                FROM leads as l
                INNER JOIN users as u ON (u.id = l.user_id) 
                INNER JOIN tasks as t on (l.id = t.lead_id)
                WHERE u.id = ".$user_id."
                AND l.app_installed = 1
                AND t.open_by = ".$user_id." 
                AND STR_TO_DATE(t.status_update_date, '%Y-%m-%d') = STR_TO_DATE(l.signup_date, '%Y-%m-%d')
                GROUP BY l.id";
        $net_app_installed = $this->db_srm->query($sql)->num_rows;
        return $net_app_installed;
    }

    /*
     * lead using competitors
     * @param  : $data array type || Get from restapi-blacklist method
     * @return : result in array type || Send to restapi-blacklist method
     * @date   : 24-06-17
     * @author : manish
     * */
    public function leadUsingCompetitors($data = array()) {

      $customer_id      = !empty($data['user_id']) ? $data['user_id'] : 0;
      $mobile           = !empty($data['mobile']) ? $data['mobile'] : '';
      $app              = !empty($data['app']) ? $data['app'] : '';
      $name             = !empty($data['name']) ? $data['name'] : '';
      $competitor_data  = '';

      if ($customer_id > 0) {
         
          $sql = "SELECT leads.id FROM leads LEFT JOIN leads_website_customer_ids lwc ON leads.id = lwc.lead_id WHERE lwc.customer_id = '$customer_id' ";


          $leads = $this->db_srm->query($sql)->row;

          if (!empty($leads)) {

            $competitor_data = array(
              'mobile' => $mobile,             
              'name'   => $name               
            );         

            $competitor_data = serialize($competitor_data);

            $competitor_aaps = array( 
                    'app' => $app
                  );

            $competitor_aaps = serialize($competitor_aaps);
              
            //Update competitor data
            $update_sql = "UPDATE leads SET 
                      using_competitors   = '$competitor_data',
                      other_install_apps  = '$competitor_aaps'
                      WHERE id = '".$leads['id']."' ";

              $save_data = $this->db_srm->query($update_sql);

              $rt['status'] = '1';
              $rt['status_text'] = 'Success';
              $rt['message'] = 'competitor data has been saved.';             
             
          } else {

              $rt['status'] = '0';
              $rt['status_text'] = 'Error';
              $rt['message'] = 'Invalid customer id!';
          }
        
      } else {
            $rt['status'] = '0';
            $rt['status_text'] = 'Error';
            $rt['message'] = 'User id is empty';
      }   
                 
      return $rt; 
      exit;
    }

    public function getCustomerAgentName($customer_id){
        $sql = "SELECT u.name, u.mobile 
                FROM leads_website_customer_ids lwci 
                INNER JOIN leads l ON (lwci.lead_id = l.id) 
                INNER JOIN users u ON (u.id = l.user_id)
                WHERE lwci.customer_id = ".(int)$customer_id;
        $result = $this->db_srm->query($sql);
        if ($result->num_rows) {
            $agent_name = $result->row['name']."(".$result->row['mobile'].")";
        } else {
            $agent_name = '';
        }
        return $agent_name;
    }
}
