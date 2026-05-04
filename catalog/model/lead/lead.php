<?php

/**
 * 
 */
class ModelLeadLead extends Model {

    private $lead_log = 1;
    public $db_crm;

    function __construct() {
        //CRM Database
        $this->db_crm = new Database\DB( DBCRM_SERVERS );
        // $registry->set('db_crm', $db_crm);
    }

    public function checkOpenAccounts($mobile) {
        if ($this->lead_log == 1) {
            $crm_query = "SELECT id FROM accounts WHERE mobile= " . "'" . $this->db_crm->escape($mobile) . "'";
            
            $query = $this->db_crm->query($crm_query);
            return $query->num_rows ? $query->row['id'] : 0;
        } else {
            return 0;
        }
    }

    public function checkExistingContacts($mobile) {
        if ($this->lead_log == 1) {
            $crm_query = "SELECT id FROM contacts WHERE mobile= " . "'" . $this->db_crm->escape($mobile) . "'";
            
            $query = $this->db_crm->query($crm_query);
            return $query->num_rows ? $query->row['id'] : 0;
        } else {
            return 0;
        }
    }

    public function checkExistingLead($mobile) {

        if (empty($mobile)) {
            return 0;
        }

        if ($mobile != 'skipping') {
            if ($this->lead_log == 1) {
                $mobile = substr(trim($mobile), -10);

                if (strlen($mobile) == 10) {

                    $crm_query = ""
                            . "SELECT Leads.id FROM leads Leads WHERE (Leads.status = 'OPEN' AND mobile = '" . $this->db_crm->escape($mobile) . "')"
                            . "UNION "
                            . "SELECT LeadContacts.lead_id AS `LeadContacts__lead_id` FROM lead_contacts LeadContacts WHERE (mobile = '" . $this->db_crm->escape($mobile) . "' AND status = 1)";
                } else {
                    $crm_query = "SELECT Leads.id FROM leads Leads WHERE (Leads.status = 'OPEN' AND mobile LIKE '%" . $this->db_crm->escape($mobile) . "%')"
                            . "UNION "
                            . "SELECT LeadContacts.lead_id AS `LeadContacts__lead_id` FROM lead_contacts LeadContacts WHERE (mobile LIKE '%" . $this->db_crm->escape($mobile) . "%' AND status = 1)";
                }

                $query = $this->db_crm->query($crm_query);
                return $query->num_rows ? $query->row['id'] : 0;
            } else {
                return 0;
            }
        }
        die("Resut");
    }

    public function getLeadDetailsInCrm($mobile) {

       if ($mobile != 'skipping' && $this->lead_log == 1) {

           $mobile = substr(trim($mobile), -10);

           if (strlen($mobile) == 10) {
               $comp_sql = " = '" . $this->db_crm->escape($mobile) . "' ";
           } else {
               $comp_sql = " LIKE '%" . $this->db_crm->escape($mobile) . "%' ";
           }

           $sql = "(
                    SELECT l.id, l.user_id
                    FROM leads AS l
                    WHERE l.mobile " . $comp_sql . "
                      AND l.status <> 'MERGE_DEAD'
                    LIMIT 1
                   )
                   UNION
                   (
                    SELECT l.id, l.user_id
                    FROM leads AS l
                    INNER JOIN lead_contacts AS lc ON lc.lead_id = l.id
                                                      AND lc.status = 1
                                                      AND lc.mobile " . $comp_sql . "
                    WHERE l.status <> 'MERGE_DEAD'
                    LIMIT 1
                   )";
           $query = $this->db_crm->query($sql);
           return $query->num_rows ? $query->row : 0;
       }

       return 0;
   }


    public function getLeadIdUsingCustomerInCrm($user_id) {
            if ($this->lead_log == 1) {
                    $crm_query = "SELECT lcwi.lead_id, l.user_id as user_id FROM  leads_website_customer_ids lcwi "
                            . "   INNER JOIN leads l ON l.id = lcwi.lead_id "
                            . "   WHERE lcwi.customer_id = '" . $this->db_crm->escape($user_id) . "' AND l.status != 'MERGE_DEAD' "
                            . "   LIMIT 1 ";
                
                $query = $this->db_crm->query($crm_query);
                return $query->num_rows ? $query->row : 0;
            } else {
                return 0;
            }
    }

    public function getLeadOrder($data) {
        /////////////////////////////////////////
        //Adding the entry to crm by Parth		//
        /////////////////////////////////////////
        if ($this->lead_log == 1) {
            foreach ($data as $datas) {
                //Finding the appropriate sales staff


                $sales_staff_id = 0;
                if (!empty($datas['sales_telephone'])) {
                    $user_id = $this->db_crm->query("SELECT id FROM users WHERE mobile LIKE '" . $this->db_crm->escape(trim($datas['sales_telephone'])) . "'");

                    if (!empty($user_id->num_rows)) {
                        $sales_staff_id = $user_id->row['id'];
                    }
                }

                //Finding whether ther are any open leads
                if ($lead_id = $this->checkExistingLead($datas['telephone'])) {
                    // echo $lead_id; die;


                    $sql = "UPDATE leads SET
					status = 'OPEN', 
					name     					=" . "'" . $this->db_crm->escape($datas['buyer_name']) . "', 
					business_name     			=" . "'" . $this->db_crm->escape($datas['shipping_company']) . "', 
					email    					=" . "'" . $this->db_crm->escape($datas['email']) . "', 
					address  					=" . "'" . $this->db_crm->escape($datas['shipping_address_1']) . "', 
					country_id 					=" . "'" . $this->db_crm->escape($datas['shipping_country_id']) . "', 
					state_id   					=" . "'" . $this->db_crm->escape($datas['shipping_zone_id']) . "', 
					is_registered 				= '1', 
					priority   					= '1', 
					order_no					=" . "'" . $this->db_crm->escape($datas['order_no']) . "', 
					zip      					=" . "'" . $this->db_crm->escape($datas['shipping_postcode']) . "', 
					status_update_date 			= NOW()";

                    /*
                     * No need to assign here as per discussed with Rakesh Sir(04-09-2017)
                     * So in case of self order it will not assign to tele sale user automatically
                     * 
                      if (isset($sales_staff_id)) {
                      $sql .= ", user_id=" . "'" . $sales_staff_id . "'";
                      }
                     * 
                     */
                    $sql .= " WHERE id = " . (int)$lead_id;

                    //Seletecting any open tasks in case of open lead
                    //and updating for followup date
                    //$this->createLog($lead_id, 'Updating 7 days followup lead');
                    if ($this->db_crm->query($sql)) {
                        $data = array('status' => 'OPEN');
                        $find_task_sql = $this->findTask($lead_id, $data);
                        /*
                          "SELECT * FROM tasks t
                          WHERE t.lead_id= ".$lead_id."
                          AND t.status = 'OPEN'";
                          $find_task_sql = $this->db_crm->query($find_task_sql); */

                        //Finding any open tasks for the given lead
                        if ($find_task_sql->num_rows > 1) {
                            
                        } elseif ($find_task_sql->num_rows == 1) {
                            $task = $find_task_sql->row;

                            $order_date = isset($datas['order_date']) ? $datas['order_date'] : '';
                            $comment = "Delivered Before 7 days order date - " . $order_date;

                            $this->addTaskComment($task['id'], $comment);
                               $task_sql1 = "UPDATE tasks SET 
                              followup_date =" . "'" . $this->db_crm->escape(date('Y-m-d')) . "',
                              user_id 		=" . "'" . (int)$user_id . "',
                              status 		= 'CLOSED',
                              modified 		= NOW(),
                              status_update_date 	= NOW()
                              WHERE id =" . (int)$task['id'];

                              $this->db_crm->query($task_sql1);


                              $task_sql = " INSERT INTO tasks SET
                              user_id 		=" . "'" . (int)$user_id . "',
                              status_update_date = NOW(),
                              name			= 'Interested - need followup',
                              status 		= 'OPEN',
                              log_action_id = 2,
                              created 		= NOW(),
                              modified 		= NOW(),
                              lead_id 		=" . "'" . (int)$lead_id . "',
                              followup_date =" . "'" . $this->db_crm->escape(date('Y-m-d')) . "'";
                             $this->db_crm->query($task_sql);
                             
                        } else {
                            $task_sql = "INSERT INTO tasks SET 
							user_id 			=" . "'" . (int)$sales_staff_id . "', 
							status_update_date 	= NOW(), 
							name				= 'Interested - need followup',   
							status 				= 'OPEN', 
							created 			= NOW(),  
                            log_action_id                   = 2,
							lead_id 			=" . "'" . (int)$lead_id . "', 
							followup_date 		=" . "'" . $this->db_crm->escape(date('Y-m-d')) . "'";

                            $this->db_crm->query($task_sql);
                            $task_comm = $this->db_crm->getLastId();
                            $order_Date = isset($datas['order_date']) ? $datas['order_date'] : '';
                            $comment = "Delivered Before 7 days order date - " . $order_Date;
                            $this->addTaskComment($task_comm, $comment);
                        }
                    }
                } else {
                    // Inserting in case of no open leads found
                    $sql = "INSERT INTO leads SET 
						name     					=" . "'" . $this->db_crm->escape($datas['buyer_name']) . "' , 
						business_name     			=" . "'" . $this->db_crm->escape($datas['shipping_company']) . "' , 
						email    					=" . "'" . $this->db_crm->escape($datas['email']) . "', 
						user_id    					=" . "'" . $this->db_crm->escape($sales_staff_id) . "', 
						mobile    					=" . "'" . $this->db_crm->escape($datas['telephone']) . "', 
						address  					=" . "'" . $this->db_crm->escape($datas['shipping_address_1']) . "' , 
						city     					=" . "'" . $this->db_crm->escape($datas['shipping_city']) . "', 
						country_id 					=" . "'" . $this->db_crm->escape($datas['shipping_country_id']) . "' , 
						state_id   					=" . "'" . $this->db_crm->escape($datas['shipping_zone_id']) . "', 
						is_registered 				= '1', 
						status   					= 'OPEN', 
						priority   					= '1' , 
						source_id					= '7', 
						order_no					=" . "'" . $this->db_crm->escape($datas['order_no']) . "', 
						zip      					=" . "'" . $this->db_crm->escape($datas['shipping_postcode']) . "', 
						status_update_date 			= NOW()
						";

                    if ($this->db_crm->query($sql)) {
                        // code for opening a new task

                        $new_lead_id = $this->db_crm->getLastId();

                        $new_task_sql = "INSERT INTO tasks SET 
						user_id 			=" . "'" . (int)$sales_staff_id . "', 
						status_update_date 	= NOW(), 
						name				= 'Interested - need followup',   
						status 				= 'OPEN', 
                        log_action_id       = 2,
						created 			= NOW(),  
						lead_id 			=" . "'" . (int)$new_lead_id . "', 

						followup_date 		=" . "'" . $this->db_crm->escape(date('Y-m-d')) . "'";
                        $this->db_crm->query($new_task_sql);

                        $task_comm = $this->db_crm->getLastId();
                        $order_Date = isset($datas['order_date']) ? $datas['order_date'] : '';
                        $comment = "Delivered Before 7 days order date - " . $order_Date;
                        $this->addTaskComment($task_comm, $comment);
                    }



                    // echo $lead_id = $this->db_crm->getLastId(); die;
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


    public function getLeadData($lead_id) {
        return $this->db_crm->query("SELECT id,cart FROM leads WHERE id = " . (int)$lead_id)->row;
    }

    public function updateLead($data, $mobile, $message, $customer_id = '') {

        if (isset($data['mobile'])) {
            unset($data['mobile']);
        }

        $lead = new updateLead($data, $mobile, $message, $customer_id);
        $lead->updateLeadData();
        return true;
    }
    
    public function addAlternateNumber($data, $mobile, $message, $customer_id = '') {

     
        $lead = new updateLead($data, $mobile, $message, $customer_id);
        $lead->addAlternateNumber();
        return true;
    }

    /**
     * @author Vishnu singh shekhawat
     * @description update alternate contact number in crm
     * @param type $data
     * @param type $mobile
     * @param type $message
     * @param type $customer_id
     * @return boolean
     */
    public function addAlternateContactInLead($data, $mobile, $message, $customer_id = '') {

        $lead = new updateLead($data, $mobile, $message, $customer_id);
        $lead->is_update_alternate_contact_no = 1;
        $lead->updateLeadData();
        return true;
    }


    public function addLead($data, $mobile, $customer_id = '', $message = '') {

        $lead_id = $this->checkExistingLead($mobile);
      
        $cust = '';
        if (!empty($customer_id)) {
            $cust = $customer_id;
        }

        if ($lead_id > 0) {

            $this->updateLead($data, $mobile, 'Updating already existing lead on signup', $cust);
            return true;
        }

        /*
         * INSERT INTO leads 
         * (name,business_name,user_id,is_dropshipper,
         *  city_id ,country_id ,state_id,zip,priority,
         *  lead_show,ip ,app_installed,app_install_date,
         *  signup_date, created, status_update_date, mobile,
         *  customer_id, is_registered) 
         * SELECT
         *       'sujon sk','sujon sk','49','0','184','99','1506','742135', '1','1','','1', 
         *       '2017-07-11 19:32:02','2017-07-11 12:36:57',NOW(),
         *       '2017-07-11 19:32:03','7098184965', 44215, 
         *        '2' 
         *  FROM DUAL 
         *  WHERE NOT EXISTS 
         *  (SELECT id FROM leads WHERE mobile='7098184965')
         */
        $date = date('Y-m-d H:i:s');
        $i = 0;
        $keys = array();
        $values = array();
        
        foreach ($data as $key => $value) {


            if ($key == 'campaign_event_no') {
                continue;
            }
            if ($key == 'refer_by_customer_id') {
                continue;
            }

            $keys[] = $key;

            if ($key == 'is_registered') {
                $values[] = "$value";
            } else {
                $values[] = "'" . $this->db_crm->escape($value) . "'";
            }

            $i++;
        }
       
        $other_keys = array('created', 'status_update_date', 'mobile');
        $other_values = array('NOW()', "'" . $date . "'", "'" . $mobile . "'");
        
        $keys_arr = array_merge($keys, $other_keys);
        $values_arr = array_merge($values, $other_values);



        $final_keys_arr = implode(',', $keys_arr);
        $final_values_arr = implode(',', $values_arr);
        $q = "INSERT INTO leads ($final_keys_arr) 
            SELECT $final_values_arr FROM DUAL
            WHERE NOT EXISTS (SELECT id FROM leads WHERE mobile = '" . $this->db_crm->escape($mobile) . "')";
        
        if ($this->db_crm->query($q)) {

            $lead_id = $this->db_crm->getLastId();
            if (!empty($lead_id)) {

                if (isset($cust) && !empty($cust)) {
                    $this->addCustomerId($lead_id, $cust);
                }
                if(isset($data['refer_by_customer_id']) && !empty($data['refer_by_customer_id'])){
                     $this->addReferByLeadId($lead_id, $data['refer_by_customer_id']);
                }

                if (empty($data['user_id'])) {
                    $user_id = $this->getLeadUser($lead_id);
                } else {
                    $user_id = $data['user_id'];
                }
                $campaign_event_no = 0;
                if (isset($data['campaign_event_no']) && !empty($data['campaign_event_no'])) {
                    $campaign_event_no = $data['campaign_event_no'];
                }

                $campaing_id = 0;
                if (!empty($campaign_event_no)) {
                    $campaing_data = $this->db_crm->query("SELECT campaign_id FROM `campaign_events`  WHERE event_no = '" . $this->db_crm->escape($campaign_event_no) . "'");
                    if ($campaing_data->num_rows) {
                        $campaing_id = $campaing_data->row['campaign_id'];
                    }
                }


                $task_sql = "INSERT INTO tasks SET
			status_update_date 	= NOW(),
			name				= 'New Signup',
			status 				= 'OPEN',
            log_action_id                   = '39',
			priority 			= '1',
			user_id 			= " . "'" . (int)$user_id . "',
			created 			= NOW(),
			modified 			= NOW(),
			lead_id 			=" . "'" . (int)$lead_id . "',
			campaign_event_no 	=" . "'" . $this->db_crm->escape($campaign_event_no) . "',
			campaign_id 	    =" . "'" . $this->db_crm->escape($campaing_id) . "',
			followup_date 		=" . "'" . $this->db_crm->escape(date('Y-m-d')) . "'";
                $this->db_crm->query($task_sql);

                $last_tasks_id = $this->db_crm->getLastId();

                $message .= " User registered in website On  " . $data['signup_date'] . " And User registered in Crm On  " . date('Y-m-d H:i:s');

                $this->addTaskComment($last_tasks_id, $message);
             
                return true;
            }
        } else {
            return false;
        }

        return true;
    }
    
    public function addCustomerId($lead_id, $customer_id) {


        if (empty(trim($lead_id)) || empty(trim($customer_id))) {
            return TRUE;
        }

        $lead_id = trim($lead_id);
        $customer_id = trim($customer_id);
        $lead_customer_id = $this->checkExistingCustomer($lead_id, $customer_id);
        if ($lead_customer_id === FALSE) {
            $q = "INSERT INTO  leads_website_customer_ids SET 
                        lead_id = " . (int)$lead_id . ", 
                        customer_id = " . (int)$customer_id . ",   
                        status              = 1";

            try {
                $this->db_crm->query($q);
            } catch (\Exception $e) {
                return true;
            }
        }
    }
    
    public function addReferByLeadId($lead_id, $refer_by_customer_id) {


        if (empty(trim($lead_id)) || empty(trim($refer_by_customer_id))) {
            return TRUE;
        }

        $lead_id = trim($lead_id);

        if ($this->checkExistingLeadRefer($lead_id) === FALSE) {
            $refer_by_customer_id = trim($refer_by_customer_id);
            $refer_by_lead_data = $this->getLeadIdUsingCustomerInCrm($refer_by_customer_id);
            if (!empty($refer_by_lead_data)) {
                $refer_by_lead_id = $refer_by_lead_data['lead_id'];
                $q = "INSERT INTO  leads_website_refer_ids SET 
                        lead_id = " . (int) $lead_id . ", 
                        refer_by_lead_id = " . (int) $refer_by_lead_id . ",   
                        refer_by_customer_id = " . (int) $refer_by_customer_id . " , 
                        status  = 1";

                try {
                    $this->db_crm->query($q);
                } catch (\Exception $e) {
                    return true;
                }
            }
        }
    }

    public function checkExistingCustomer($lead_id, $customer_id) {

        if (empty($lead_id) || empty($customer_id)) {
            return TRUE;
        }
        $crm_query = "SELECT * FROM `leads_website_customer_ids` "
                . "WHERE "
                . "lead_id = " . (int)$lead_id . " AND customer_id = " . (int)$customer_id . " LIMIT 1";

        return $this->db_crm->query($crm_query)->num_rows ? TRUE : FALSE;
    }
    

    public function checkExistingLeadRefer($lead_id) {

        if (empty($lead_id)) {
            return TRUE;
        }
        $crm_query = "SELECT * FROM `leads_website_refer_ids` "
                . "WHERE "
                . "lead_id = " . (int)$lead_id . " LIMIT 1";

        return $this->db_crm->query($crm_query)->num_rows ? TRUE : FALSE;
    }


    public function closeOrderedTasks($task_id) {
        $this->db_crm->query(
                "UPDATE tasks SET 
			status = 'CLOSED' , 
			modified = " . "'" . $this->db_crm->escape(date('Y-m-d H:i:s')) . "' 
			WHERE id =" . (int)$task_id
        );
    }

    

    public function findTask($lead_id, $data) {
        $sql = "SELECT * FROM tasks WHERE lead_id=" . (int)$lead_id;
        $sql .= isset($data['status']) ? " AND status =" . "'" . $this->db_crm->escape($data['status']) . "'" : '';

        return $this->db_crm->query($sql);
    }

    public function updateLeadFromCustomerId($lead_data, $customer_id) {

        $sql = "SELECT l.id,l.mobile 
                FROM leads_website_customer_ids lwci 
                INNER JOIN leads l ON l.id = lwci.lead_id 
                WHERE l.status = 'OPEN' AND  lwci.customer_id = " . (int)$customer_id;

        if (!empty($lead = $this->db_crm->query($sql)->row)) {
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
        $user_q = $this->db_crm->query("SELECT user_id FROM task_comments WHERE task_id=" . (int)$task_id);

        if ($user_q->num_rows) {
            $user_id = $user_q->row['user_id'];
        } else {
            $user_id = '';
        }
        $sql = "INSERT INTO task_comments SET 
		task_id =" . "'" . (int)$task_id . "',
		comment =" . "'" . $this->db_crm->escape($comm) . "', ";

        if (!empty($user_id)) {
            $sql .= "user_id ='" . (int)$user_id . "', ";
        }
        $sql .= "created = NOW(),
		         modified= NOW() 
                ";
        $this->db_crm->query($sql);
    }

    public function updateLeadStatus($data) {
        $q = "UPDATE leads SET ";
        $q .= " lead_status_id = " . $this->db_crm->escape($data['order_status_id']);
        if ($data['order_status_id'] == '5' || $data['order_status_id'] == '15') {
            $q .= ", is_ordered = 1 ";
        }
        $q .= ", modified = NOW() ";
        $q .= " WHERE order_no = '" . $this->db_crm->escape($data['order_no']) . "'";

        if ($this->db_crm->query($q)) {
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

        $lead_mobile_no = trim($lead_mobile_no);
        
        if (strlen($lead_mobile_no) == 10) {
            $crm_query = "(SELECT u.name, u.mobile, u.role_id FROM users u 
                                INNER JOIN leads l ON l.user_id = u.id AND l.mobile = '" . $this->db_crm->escape($lead_mobile_no) . "'   AND l.status = 'OPEN' WHERE u.status = 1 LIMIT 1)
                            UNION 
                            (SELECT u.name, u.mobile, u.role_id FROM users u INNER JOIN leads l ON l.user_id = u.id AND  l.status = 'OPEN' 
                            INNER JOIN lead_contacts lc ON lc.lead_id = l.id AND lc.mobile = '" . $this->db_crm->escape($lead_mobile_no) . "'  AND lc.status = 1 WHERE u.status = 1 LIMIT 1)";

        } else {
            
             $crm_query = "(SELECT u.name, u.mobile, u.role_id FROM users u 
                                INNER JOIN leads l ON l.user_id = u.id AND l.status = 'OPEN' WHERE u.status = 1 AND  l.mobile LIKE '" . $this->db_crm->escape($lead_mobile_no) . "%'  LIMIT 1)
                            UNION 
                            (SELECT u.name, u.mobile, u.role_id FROM users u INNER JOIN leads l ON l.user_id = u.id AND  l.status = 'OPEN' 
                            INNER JOIN lead_contacts lc ON lc.lead_id = l.id AND  lc.status = 1 WHERE u.status = 1 AND lc.mobile = '" . $this->db_crm->escape($lead_mobile_no) . "'  LIMIT 1)";
            
        }

        $data = $this->db_crm->query($crm_query);

        return $data->num_rows ? $data->row : 0;
    }

    public function getCrmCityId($city_name) {
        $crm_query = "SELECT id FROM cities c WHERE name = '" . $this->db_crm->escape($city_name) . "'";
        $data = $this->db_crm->query($crm_query);
        return $data->num_rows ? $data->row['id'] : 0;
    }

    public function getCrmStateId($state_name) {
        $crm_query = "SELECT id FROM states c WHERE name = '" . $this->db_crm->escape($state_name) . "'";
        $data = $this->db_crm->query($crm_query);
        return $data->num_rows ? $data->row['id'] : 0;
    }

    public function getCrmCountryId($country_name) {
        $crm_query = "SELECT id FROM countries c WHERE name = '" . $this->db_crm->escape($country_name) . "'";
        $data = $this->db_crm->query($crm_query);
        return $data->num_rows ? $data->row['id'] : 0;
    }

    public function getUserFromCity($city_id) {
        $crm_query = "SELECT user_id 
                      FROM city_user_data cd 
                      INNER JOIN users u on (u.id = cd.user_id) 
                      WHERE u.status = 1 AND city_id = '" . $this->db_crm->escape($city_id) . "'";
        $data = $this->db_crm->query($crm_query);
        return $data->num_rows ? $data->row['user_id'] : CRM_TEAM_LEAD_USER_ID;
    }

    public function getLeadUser($lead_id) {
        $crm_query = "SELECT l.user_id FROM leads l
                      INNER JOIN users u 
                      ON u.id = l.user_id 
                      WHERE u.status = 1 
                       AND l.id = " . (int)$lead_id;
        $data = $this->db_crm->query($crm_query);
        return $data->num_rows ? $data->row['user_id'] : CRM_TEAM_LEAD_USER_ID;
    }

    public function getCrmTeamLeadEmailId($user_id) {
        $crm_query = "SELECT u.name, u.email
                        FROM users AS u
                        INNER JOIN user_agents AS ua on (u.id = ua.user_id) 
                        WHERE u.status = 1 AND u.role_id = 5 AND ua.agent_id IN ( " . $user_id . " )";
        return $this->db_crm->query($crm_query);
    }

    //get all commission Rates
    private function getOrderCommissionRates() {
        $sql = "SELECT code,`key`,value
                FROM settings
                WHERE `key` like '%order_commission_rate%'";
        $results = $this->db_crm->query($sql)->rows;
        $result = array();
        if (!empty($results)) {
            foreach ($results as $value) {
                $result[$value['code']][$value['key']] = $value['value'];
            }
        }
        return $result;
    }


    public function syncSalesStaff($user_id) {
        $users_date = $this->db_crm->query("SELECT created, modified FROM users WHERE id = " . (int)$user_id)->row;
        return $users_date;
    }

    public function getTotalCrmDataCount($start_date, $end_date) {
        $sql = "SELECT count(id) as total_lead 
				FROM leads 
				WHERE status != 'MERGE_DEAD'
                AND created BETWEEN STR_TO_DATE('" . $this->db_crm->escape($start_date) . "', '%Y-%m-%d') 
                                    AND STR_TO_DATE('" . $this->db_crm->escape($end_date) . "', '%Y-%m-%d')";
        return $this->db_crm->query($sql)->row;
    }

    public function getNetAppInstalledByStaffSales($user_id) {
        $sql = "SELECT count(l.id) as app_installed
                FROM leads as l
                INNER JOIN users as u ON (u.id = l.user_id) 
                INNER JOIN tasks as t on (l.id = t.lead_id)
                WHERE u.id = " . (int)$user_id . "
                AND l.app_installed = 1
                AND t.open_by = " . (int)$user_id . " 
                AND STR_TO_DATE(t.status_update_date, '%Y-%m-%d') = STR_TO_DATE(l.signup_date, '%Y-%m-%d')
                GROUP BY l.id";
        $net_app_installed = $this->db_crm->query($sql)->num_rows;
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

        $customer_id = !empty($data['user_id']) ? $data['user_id'] : 0;
        $mobile = !empty($data['mobile']) ? $data['mobile'] : '';
        $app = !empty($data['app']) ? $data['app'] : '';
        $name = !empty($data['name']) ? $data['name'] : '';

        $other_app_name_installed = !empty($data['other_app_name_installed']) ? $data['other_app_name_installed'] : '';

        $other_app_package_installed = !empty($data['other_app_package_installed']) ? $data['other_app_package_installed'] : '';

        $competitor_data = '';

        if ($customer_id > 0) {

            $sql = "SELECT lwc.lead_id FROM leads_website_customer_ids lwc  WHERE lwc.customer_id = '" . (int)$customer_id . "' LIMIT 1 ";


            $leads = $this->db_crm->query($sql)->row;

            if (!empty($leads)) {

                if (isset($data['app_version_code']) && $data['app_version_code'] > 62) {

                    $competitor_data = array(
                        'mobile' => $mobile,
                        'name' => $name,
                        'app' => $app
                    );

                    $competitor_data = serialize($competitor_data);

                    $other_install_aaps = array(
                        'other_app_package_installed' => $other_app_package_installed,
                        'other_app_name_installed' => $other_app_name_installed
                    );

                    $other_install_aaps = serialize($other_install_aaps);

                    //Update competitor data
                    $update_sql = "UPDATE leads SET 
                          using_competitors   = '" . $this->db_crm->escape($competitor_data) . "',
                          other_install_apps  = '" . $this->db_crm->escape($other_install_aaps) . "'
                          WHERE id = '" . (int)$leads['id'] . "' ";

                    if ($save_data = $this->db_crm->query($update_sql)) {
                        
                    } else {
                        $rt['status'] = '0';
                        $rt['status_text'] = 'Error';
                        $rt['message'] = 'db error!';
                        return $rt;
                        exit;
                    }
                }

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

    public function getCustomerAgentName($customer_ids) {
        
        if (empty($customer_ids))
            return '';
        
        $is_single_cid = false;
        $single_cid = 0;
        
        // if not an array, but instead, a single customer is passed
        if (!is_array($customer_ids)) {
            $is_single_cid = true;
            $single_cid = $customer_ids;
            
            $customer_ids = array($customer_ids);
        }
        
        // SELECTING on unique id(s) only
        $customer_ids = array_unique($customer_ids);
        
        $sql = "SELECT lwci.customer_id, u.name, u.mobile 
                FROM leads_website_customer_ids lwci 
                INNER JOIN leads l ON (lwci.lead_id = l.id) 
                INNER JOIN users u ON (u.id = l.user_id)
                WHERE lwci.customer_id IN (" . implode(',', $customer_ids) . ") 
                GROUP BY lwci.customer_id";
        $result = $this->db_crm->query($sql);
        
        $agent = '';
        if ($result->num_rows) {
            
            if ($is_single_cid) {
                $agent = $result->row['name'] . "(" . $result->row['mobile'] . ")";
                return $agent;
            }
                
            // Array input
            $agent_data = array(); // restructuring data
            $agent_data = array_combine(array_column($result->rows, 'customer_id'), 
                                        $result->rows
                                       );
            
            $agent = array(); // reinitializing to array
            foreach ($customer_ids as $cid) {
                if (isset($agent_data[$cid])) {
                    $agent[$cid] = $agent_data[$cid]['name'] . "(" . $agent_data[$cid]['mobile'] . ")";
                } else {
                    $agent[$cid] = '';
                }
            }
        }
        
        return $agent;
    }


    public function getCustomerUserId($customer_ids) {
        
        if (empty($customer_ids))
            return '';
        
        $is_single_cid = false;
        $single_cid = 0;
        
        // if not an array, but instead, a single customer is passed
        if (!is_array($customer_ids)) {
            $is_single_cid = true;
            $single_cid = $customer_ids;
            
            $customer_ids = array($customer_ids);
        }
        
        // SELECTING on unique id(s) only
        $customer_ids = array_unique($customer_ids);
        
        $sql = "SELECT lwci.customer_id, u.id
                FROM leads_website_customer_ids lwci 
                INNER JOIN leads l ON (lwci.lead_id = l.id) 
                INNER JOIN users u ON (u.id = l.user_id)
                WHERE lwci.customer_id IN (" . implode(',', $customer_ids) . ") 
                GROUP BY lwci.customer_id";
        $result = $this->db_crm->query($sql);
        
        $agent = '';
        if ($result->num_rows) {
            
            if ($is_single_cid) {
                $agent = $result->row['user_id'] . "(" . $result->row['id'] . ")";
                return $agent;
            }
            // Array input
            $agent_data = array(); // restructuring data
            $agent_data = array_combine(array_column($result->rows, 'customer_id'), 
                                        $result->rows
                                       );
            
            $agent = array(); // reinitializing to array
            foreach ($customer_ids as $cid) {
                if (isset($agent_data[$cid])) {
                    $agent[$cid] =  $agent_data[$cid]['id'];
                } else {
                    $agent[$cid] = '';
                }
            }
        }
        
        return $agent;
    }

    public function get_random_TL() {
        $user_array = array(CRM_TEAM_LEAD_USER_ID, SALE_KHAN_USER_ID);
        $user_id = $user_array[array_rand($user_array, 1)];
        return $user_id;
    }

    public function getTlRandomUserId($tl_user_id):int{

        $agent_id = 0;
        if (empty(trim($tl_user_id))) {

            $tl_user_id = (int) $tl_user_id;

            $crm_query = "SELECT UserAgents.agent_id AS `agent_id`  
                          FROM user_agents UserAgents 
                          INNER JOIN users Users ON Users.id = (UserAgents.agent_id) 
                          WHERE (user_id = " . (int)$tl_user_id . " AND Users.status = 1) ORDER BY rand() LIMIT 1";
            $data = $this->db_crm->query($crm_query);

            $agent_id = $data->num_rows ? $data->row['agent_id'] : 0;
        }

        return $agent_id;
    }
    
    /**
     * @description return random desktop dialer user id
     * @return int
     */
    public function getDesktopDialerRandomUserId():int{

        $agent_id = 0;
      

        $crm_query = "SELECT u.id  FROM users u  
                      WHERE (u.status = 1 
                             AND u.is_test = 0 
                             AND u.is_desktop_dialer_active = 1 
                             AND u.desktop_dialer_list_id IS NOT NULL 
                             AND u.desktop_dialer_list_id > 0 
                             AND u.desktop_dialer_id IS NOT NULL 
                             AND u.desktop_dialer_id != '') 
                      ORDER BY rand() LIMIT 1";
                      
        $data = $this->db_crm->query($crm_query);

        $agent_id = $data->num_rows ? $data->row['id'] : 0;
        
        return $agent_id;
    }

    /**
     * getTseUserFromCity
     * Get tele sales user from multi cities
     * @param : city_id
     * @return: user_id ON success      
     * */
    public function getTseUserFromCity($city_id) {

       $crm_query = "SELECT user_id FROM city_user_data cd 
                     INNER JOIN users u on (u.id = cd.user_id) 
                     WHERE u.status = 1 
                           AND u.is_test != 1 
                           AND u.role_id = 3 
                           AND cd.city_id = '" . (int)$city_id . "' 
                     ORDER BY RAND() LIMIT 1";
        $data = $this->db_crm->query($crm_query);

       return $data->num_rows ? $data->row['user_id'] : 0;
   }
   
   /**
    * @author Vishnu Shekhawat
    * @param integer $customer_id
    * @param string $message
    * @return boolean
    */
   public function saveLogInCrm($customer_id, $message, $sender_name, $timestamp,  $aws_mysqli){
        
      
       $message = $aws_mysqli->real_escape_string($message);
       $customer_id = $aws_mysqli->real_escape_string($customer_id);
       $sender_name = $aws_mysqli->real_escape_string($sender_name);
       $timestamp = $aws_mysqli->real_escape_string($timestamp);
       $sql = "INSERT INTO short_message_logs SET 
                    customer_id					= " . $customer_id . ", 
                    message 					= '" . $message . "', 
                    sender_name 				= '" . $sender_name . "', 
                    timestamp 					= '" . $timestamp . "', 
                    created 					= NOW()";
        
       if($aws_mysqli->query($sql)){
           return true;
       }else{
           return false;
       }       
       
   }
   
    /**
    * @author Vishnu Shekhawat
    * @param integer $customer_id
    * @return boolean
    * @updated Anurag Jain, modified for AWS
    */
    public function getLastSyncTimeStamp( $customer_id, $aws_mysqli )
    {
        $customer_id = $aws_mysqli->real_escape_string( $customer_id );

        // find in new table
        $aws_query = "SELECT max(timestamp) as timestamp 
                      FROM short_message_logs 
                      WHERE customer_id =  '" . (int)$customer_id . "'";
        
        $data = $aws_mysqli->query( $aws_query );

        if ( $data->num_rows ) {

           $row = $data->fetch_row();
           
           if ( !empty( $row[0] )) {
             return $row[0];
           }
           
        }
        
        // find in old table
        $aws_query = "SELECT max(timestamp) as timestamp 
                      FROM short_message_logs_old 
                      WHERE customer_id =  '" . (int)$customer_id . "'";
        
        $data = $aws_mysqli->query( $aws_query );

        if ( $data->num_rows ) {
           $row = $data->fetch_row();
           return $row[0];
        }
        
       return '0';
    }
   
   /**
    * 
    * @param type $lead_id
    * @return boolean
    * add new Lead to Solr
    * 
    */
   public function addLeadtoSolr($lead_id){
       $query="SELECT  l.id,
		l.name,
		l.business_name,
		l.created_by,
		l.source_id,
		l.user_id,
		l.email,
		l.duplicate_leads_id,
		l.is_duplicate_count,
		l.country_code,
		l.mobile,
		l.city_id,
		l.zip,
		l.cart,
		l.state_id,
		l.country_id,
		l.status,
		DATE_FORMAT(l.created,'%Y-%m-%dT%H:%i:%sZ') as created,
                if(l.modified = '0000-00-00 00:00:00','',DATE_FORMAT(l.modified,'%Y-%m-%dT%H:%i:%sZ')) as modified,
		l.lead_expected_value,
		l.is_registered,
		l.is_dropshipper,
		l.app_installed,
		if(l.app_install_date = '0000-00-00 00:00:00','',DATE_FORMAT(l.app_install_date,'%Y-%m-%dT%H:%i:%sZ')) as app_install_date,
		l.other_install_apps,
		l.is_ordered,
		l.lead_show,
		l.is_cart,
		if(l.cart_modified_date = '0000-00-00 00:00:00','',DATE_FORMAT(l.cart_modified_date,'%Y-%m-%dT%H:%i:%sZ')) as cart_modified_date,
                if(l.signup_date = '0000-00-00 00:00:00','',DATE_FORMAT(l.signup_date,'%Y-%m-%dT%H:%i:%sZ')) as signup_date,
		l.using_competitors,
		l.pending_missed_call,
		l.utm_source,
		l.utm_campgain,
		l.tracking,
		l.lead_type ,
		if(l.last_login_date = '0000-00-00 00:00:00','',DATE_FORMAT(l.last_login_date,'%Y-%m-%dT%H:%i:%sZ')) as last_login_date,
		l.agent_id,
		l.ws_gcm_registration_id,
		l.ws_apn_registration_id,
		l.app_version,
		l.ios_app_version,
		l.is_on_whatsapp,
		l.is_contact_save,
		l.priority,
		l.address,
                c.name as city_name,
		group_concat(DISTINCT lc.customer_id separator ',') as customer_id,
		lld.log_action_id as priority_log_action_id,
		lld.latest_task_id,
		lld.latest_log_action_id,
		t.open_by as latest_task_open_by,
		DATE_FORMAT(t.followup_date,'%Y-%m-%dT%H:%i:%sZ') as latest_task_followup_date,
		t.name as latest_task_name,
		DATE_FORMAT(t.status_update_date,'%Y-%m-%dT%H:%i:%sZ') as latest_task_status_update_date,
		t.status as latest_task_status,
		group_concat(DISTINCT lp.category_id separator ',') as category_id,
		count(DISTINCT lp.category_id) as category_count,
		group_concat(DISTINCT lco.mobile separator ',') as alternate_numbers,
		u.name as user_name,
		u.mobile as user_mobile,
		u.email as user_email,
		u.role_id as user_role_id,
		st.name as state_name,
                ct.name as country_name
		FROM leads l 
		LEFT JOIN leads_website_customer_ids lc on (l.id=lc.lead_id) 
		LEFT JOIN lead_log_action_data lld on (l.id=lld.lead_id) 
		LEFT JOIN tasks t on (lld.latest_task_id=t.id) 
		LEFT JOIN lead_preferences lp on (l.id = lp.lead_id)
		LEFT JOIN lead_contacts lco on (l.id = lco.lead_id)
		LEFT JOIN cities c on (c.id=l.city_id)
		LEFT JOIN states st on (st.id=l.state_id)
                LEFT JOIN countries ct on (ct.id=l.country_id)
		LEFT JOIN users u on (u.id=l.user_id)
                where l.id= '". (int)$lead_id ."'
		GROUP BY l.id";
 
         $leads = $this->db_crm->query($query);
        if ($leads->num_rows) {
   
            $lead = $leads->row;
            
            $lead_data['core'] = 'leads';
            $lead_data['log'] = serialize($lead);
            $lead_data['lead_id'] = $lead['id'];
            $lead_data['add_update_flag'] = 0;
        
        $result = $this->insertSolrChangeLog($lead_data);
        
        }
        
       return TRUE;
   }
   
   /**
     * 
     * @param type $data
     * @return type
     * 
     * insert leads/tasks add/updates in ChangeLogToSolr
     */
    public function insertSolrChangeLog($data){
        
        $data['created'] = date('Y-m-d H:i:s');
        $add_update_flag = isset($data['add_update_flag']) ? $data['add_update_flag'] : 1;
        $sql = "INSERT INTO change_log_to_solr SET 
                                lead_id =" . "'" .(int)$data['lead_id'] . "',
                                core =" . "'" . $this->db_crm->escape($data['core']) . "',
                                log =" . "'" . $this->db_crm->escape($data['log']) . "',
                                add_update_flag = '" .$this->db_crm->escape($add_update_flag) ."',
                                created =" . "'" . $this->db_crm->escape($data['created']) . "'";
        $this->db_crm->query($sql);
                        
        return TRUE;        
        
    }
    
    public function insertChangeLogInSolr($lead_id , $solr_core){
        
       
        $sql = "INSERT INTO solr_change_logs SET 
                                lead_id =" . "'" . $this->db_crm->escape($lead_id) . "',
                                core =" . "'" . $this->db_crm->escape($solr_core) . "',
                                solr_flag = '0'";
        
        try{
            $this->db_crm->query($sql);
        }catch(\Exception $e){
            return true;
        }
        
                        
        return TRUE;        
        
    }

    
    public function addSolrTaskChangelog($task_id,$comment){
        
        $query="SELECT * FROM tasks
                where id='". (int)$task_id. "'";
        $tasks = $this->db_crm->query($query);
        if ($tasks->num_rows) {
             $task = $tasks->row;
        $new_task['id'] = $task['id'];
        $new_task['user_id'] = $task['user_id'];
        $new_task['lead_id'] = $task['lead_id'];
        $new_task['open_by'] = isset($task['open_by'])?$task['open_by']:'';
        $new_task['close_by'] = isset($task['close_by'])?$task['close_by']:'';
        $new_task['followup_date'] = date('Y-m-d'.'\T'.'H:i:s'.'\Z',strtotime($task['followup_date']));
        $new_task['name'] = $task['name'];
        $new_task['status_update_date'] = date('Y-m-d'.'\T'.'H:i:s'.'\Z',strtotime($task['status_update_date']));
        $new_task['status'] = $task['status'];
        $new_task['call_connected'] = isset($task['call_connected'])?$task['call_connected']:"";
        $new_task['call_duration'] = isset($task['call_duration'])?$task['call_duration']:"";
        $new_task['created'] = isset($task['created'])?date('Y-m-d'.'\T'.'H:i:s'.'\Z',strtotime($task['created'])):"";
        $new_task['modified'] = isset($task['modified'])?date('Y-m-d'.'\T'.'H:i:s'.'\Z',strtotime($task['modified'])):"";
        $new_task['lat'] = isset($task['lat'])?$task['lat']:"";
        $new_task['lng'] = isset($task['lng'])?$task['lng']:"";
        $new_task['dfs'] = isset($task['dfs'])?$task['dfs']:"";
        $new_task['dfpl'] = isset($task['dfpl'])?$task['dfpl']:"";
        $new_task['dfh'] = isset($task['dfh'])?$task['dfh']:"";
        $new_task['campaign_id'] = isset($task['campaign_id'])?$task['campaign_id']:"";
        $new_task['campaign_event_no'] = isset($task['campaign_event_no'])?$task['campaign_event_no']:"";
        $new_task['task_comment'] = $comment;
        
        $task_data['core'] = 'tasks';
        $task_data['log'] = serialize($new_task);
        $task_data['lead_id'] = $task['lead_id'];
        $task_data['add_update_flag'] = 0;
        
        $result = $this->insertSolrChangeLog($task_data);
        
        $lead = $this->getLeadLogActionData($task['lead_id']);
        $lead['latest_task_open_by'] = $new_task['open_by'];
        $lead['latest_task_followup_date'] = $new_task['followup_date'];
        $lead['latest_task_name'] = $new_task['name'];
        $lead['latest_task_status_update_date'] = $new_task['status_update_date'];
        $lead['latest_task_status'] = $new_task['status'];
        
        $lead_data['core'] = 'leads';
        $lead_data['log'] = serialize($lead);
        $lead_data['lead_id'] = $task['lead_id'];
                
        $result = $this->insertSolrChangeLog($lead_data);
        }
        return true;
                
    }
    
    /**
     * Kusum Joshi
     * @param type $lead_id
     * @return type
     * To get priority log sction, log sction info when new task is added
     * 
     */
    public function getLeadLogActionData($lead_id){
        
        $lead_data = array();
        $query="SELECT l.id,
		lld.log_action_id as priority_log_action_id,
		lld.latest_task_id,
		lld.latest_log_action_id
                FROM leads l 
		LEFT JOIN lead_log_action_data lld on (l.id=lld.lead_id) 
                where l.id='". (int)$lead_id."'";
        
         $lead = $this->db_crm->query($query);
        if ($lead->num_rows) {
             $lead_data = $lead->row;
        }  
        return $lead_data;

    }
    
    public function getAssignedToUserIdForLead(): int {
        $user_id = 0;


        $query = "SELECT value FROM settings WHERE settings.key = 'fresh_lead_agents'";

        $data = $this->db_crm->query($query);

        if ($data->num_rows) {
            if (!empty(trim($data->row['value']))) {
                $user_ids = explode(',', $data->row['value']);

                $user_id = $user_ids[array_rand(array_filter($user_ids))];
            }
        }
        return $user_id;
    }

    /**
     * 
     * @param int $telephone
     * @param int $crm_user_id
     * @param string $comment
     * @return bool
     */
    public function addCreditApplicationCommentInTask(int $telephone, int $crm_user_id, string $comment  , $customer_id= 0, $credit_status = '', $followup_date = ''): bool {

        $lead_id = $this->checkExistingLead($telephone);
        if (!empty($lead_id)) {
            
            $this->updateInTodayDialerLeads($lead_id , $crm_user_id);
            $last_task = $this->getLeadsLastTask($lead_id);

            if (!empty($last_task)) {
                
                $this->closeOrderedTasks($last_task['id']);
                
                $task_sql = "INSERT INTO tasks SET
			status_update_date 	= NOW(),
			name				= " . "'" . $this->db_crm->escape($last_task['name']) . "',
			status 				= 'OPEN',
            log_action_id       = " . "'" . $this->db_crm->escape($last_task['log_action_id']) . "',
			priority 			= '0',
			user_id 			= " . "'" . (int)$crm_user_id . "',
            open_by 			= " . "'" . (int)$crm_user_id . "',
			created 			= NOW(),
			modified 			= NOW(),
			lead_id 			=" . "'" . (int)$lead_id . "',
			followup_date 		=" . "'" . $this->db_crm->escape($last_task['followup_date']) . "'";
                $this->db_crm->query($task_sql);

                $last_tasks_id = $this->db_crm->getLastId();
                $this->addTaskComment($last_tasks_id, $comment);
                $this->addInCreditUsersLeads($lead_id, $crm_user_id, $customer_id, $credit_status, $followup_date);
                
            }
        }

        return true;
    }
    
    public function addInCreditUsersLeads($lead_id, $crm_user_id, $customer_id = 0, $credit_status, $followup_date) {
        #return true;
        if (empty($crm_user_id) || empty($lead_id)) {
            return true;
        }

        $on_duplicate_update = '';
        $followup_date_query = '';
        if (!empty($followup_date)) {
            $followup_date_query = " credit_status 	= " . "'" . $this->db_crm->escape($credit_status) . "',  next_followup_date =" . "'" . $this->db_crm->escape($followup_date) . "'  ";
            $on_duplicate_update = $followup_date_query;
        } else {
            $on_duplicate_update = "credit_status = " . "'" . $this->db_crm->escape($credit_status) . "'";
            $followup_date_query = " credit_status = " . "'" . $this->db_crm->escape($credit_status) . "'";
        }

        $task_sql = "INSERT INTO 
                            leads_credit_users 
                        SET
                            lead_id = " . "'" . (int) $lead_id . "',
                            user_id	= " . "'" . (int) $crm_user_id . "',
                            customer_id = " . "'" . (int) $customer_id . "',
                            $followup_date_query ON DUPLICATE KEY UPDATE $on_duplicate_update";
        
        $this->db_crm->query($task_sql);
        
        return true;
    }

    public function getExistingCreditLeadUsers(int $lead_id):array{
         $sql = "SELECT id,user_id,credit_status,next_followup_date FROM leads_credit_users WHERE status = 1 AND lead_id = " .(int) $lead_id;
         
        $query = $this->db_crm->query($sql);
        return $query->row;
    }
    /**
     * 
     * @param type $task_id
     */
    public function updateInTodayDialerLeads(int $lead_id ,int $user_id):bool {
        if(empty($lead_id) && !empty($user_id)){
            $this->db_crm->query(
                "UPDATE todays_dialer_list SET 
			call_status = '1' 
			WHERE lead_id = " . (int)$lead_id . " AND user_id = " . (int)$user_id);
            return true;
        }
        return FALSE;
        
    }
    
    public function getLeadsLastTask($lead_id):array{
         
        $task_data = array();
        $query="SELECT * 
                FROM tasks 
                where lead_id='".(int)$lead_id."' ORDER BY id DESC LIMIT 1";
        
         $lead = $this->db_crm->query($query);
        if ($lead->num_rows) {
             $task_data = $lead->row;
        }  
        return $task_data;
    }

    public function getTodayDialerList($filter_data = array())
    {
        $sql = "SELECT id,
                       user_id,
                       call_status,
                       lead_id,
                       sequence_number,
                       `date`,
                       content,
                       created,
                       lead_business_name ,
                       lead_followup_date,
                       mobile_number,
                       lead_name
                 FROM todays_dialer_list ";
          
           $sql = $sql."WHERE call_status = 0 ";
          


        $implode=array();


        $implode[] = "user_id = " . (int) $filter_data['filter_user_id'] . " ";

        if (!empty($filter_data['filter_lead_id']) ) {
            $implode[] = "lead_id = '" . (int) $filter_data['filter_lead_id'] . "' ";
        }

        if (!empty($filter_data['filter_mobile_number']) ) {
            $implode[] = "mobile_number = '" . (int) $filter_data['filter_mobile_number'] . "' ";
        }

        if (!empty($filter_data['filter_lead_name']) ) {
             $implode[] = "lead_name LIKE '%" . $this->db_crm->escape($filter_data['filter_lead_name']) . "%' ";
        }

        if (!empty($filter_data['filter_request_from']) ) {
            $implode[] = "request_from = '" . $this->db_crm->escape($filter_data['filter_request_from']) . "' ";
        }

         if (!empty($filter_data['filter_sequence_number']) ) {
            $implode[] = "sequence_number = '" . (int)$filter_data['filter_sequence_number'] . "' ";
        }

        if(!empty($implode)){
            $sql .= " AND " . implode(" AND ", $implode);
        }

        if (isset($filter_data['sort'])) {
            $sql .= " ORDER BY " . $filter_data['sort'];
        } else {
            $sql .= " ORDER BY sequence_number";
        }
        if (isset($filter_data['order']) && ($filter_data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }
         

       if(empty($filter_data['filter_sequence_number']))
       {  
        if (isset($filter_data['start']) || isset($filter_data['limit'])) {
            if ($filter_data['start'] < 0) {
                $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
                $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
          }
        }  
        return $this->db_crm->query($sql)->rows;

    }

    public function getTotalTodayDialerList($filter_data = array())
    {
       $sql = "SELECT id
                FROM todays_dialer_list ";

           $sql = $sql."WHERE call_status = 0 ";

        $implode=array();

       
        $implode[] = "user_id = " . (int) $filter_data['filter_user_id'] . " ";

        if (!empty($filter_data['filter_lead_id']) ) {
            $implode[] = "lead_id = " . (int) $filter_data['filter_lead_id'] . " ";
        }

        if (!empty($filter_data['filter_mobile_number']) ) {
            $implode[] = "mobile_number = '" . (int) $filter_data['filter_mobile_number'] . "' ";
        }


        if (!empty($filter_data['filter_lead_name']) ) {
             $implode[] = "lead_name LIKE '%" . $this->db_crm->escape($filter_data['filter_lead_name']) . "%' ";
        }


        if (!empty($filter_data['filter_request_from']) ) {
            $implode[] = "request_from = '" . $this->db_crm->escape($filter_data['filter_request_from']) . "' ";
        }

        if (!empty($filter_data['filter_sequence_number']) ) {
            $implode[] = "sequence_number = '" . (int)$filter_data['filter_sequence_number'] . "' ";
        }

        if(!empty($implode)){
            $sql .= " AND " . implode(" AND ", $implode);
        }
        $query = $this->db_crm->query($sql);
        return (int)$query->num_rows;
    }

   
}
