<?php

require_once DIR_SYSTEM . 'library/db/db.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Updatelead {

    private $lead_log = 1;
    private $data;
    private $mobile;
    private $message;
    private $customer_id;
    private $db_crm;
    public $is_update_alternate_contact_no;

    function __construct($data, $mobile, $message, $customer_id = '') {
        $this->data = $data;
        $this->mobile = $mobile;
        $this->message = $message;
        $this->customer_id = $customer_id;
        $this->is_update_alternate_contact_no = 0;
        $this->db_crm = new Database\DB( DBCRM_SERVERS );
        $this->db = new Database\DB( DB_SERVERS );
        // echo "23sds"; die;
    }

    public function loadDb() { 
        $this->db_crm = new Database\DB( DBCRM_SERVERS );
        $this->db = new Database\DB( DB_SERVERS );
    }

    /**
     * @author Vishnu Shekhawat
     * @description add request data in rabbit mq for further process
     * @return boolean true
     */
    public function addToRabbitMqQueue() {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        // third parameter is for queue durability. we set it to true
        // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
        // passive - false ; exclusive - false; auto-delete - false
        $channel->queue_declare('lead_updation_queue', false, true, false, false);

        $data = base64_encode(serialize($this)); // Sending the current SMS object as message for reconstruction
        // delivery_mode = 2 makes messag   e persistent (durable)
        $msg = new AMQPMessage($data, array('delivery_mode' => 2));
        $channel->basic_publish($msg, '', 'lead_updation_queue'); // send to sms_queue

        $channel->close();
        $connection->close();

        return true;
    }

    /**
     * get update Lead data
     * function sendDataToUpdateLead
     * @date 16-01-2016
     * @return true
     * */
    public function updateLeadData($send_to_queue = true) {
//print_r($this);die;
        // Sending to RabbitMQ Queue if send_to_queue is set to True and SMS_QUEUE is set to 1 in config.php
        if ($send_to_queue && CRM_UPDATE_LEAD_QUEUE) {

            try {
               $this->addToRabbitMqQueue();
               return true;
            } catch (\Exception $e) {
                // Now we ensure that the actual SMS is still sent
                if($this->is_update_alternate_contact_no == 1){
                    $this->addAlternateContactInLead();
                     return "success";
                }else{
                    goto UPDATE_LEAD;
                }
            }
        } else {
            if ($this->is_update_alternate_contact_no == 1) {
                $this->addAlternateContactInLead();
                return "success";
            } else {
                goto UPDATE_LEAD;
            }
        }


        UPDATE_LEAD:
           
        $customer_id = $this->customer_id;
        $mobile = $this->mobile;
        $message = $this->message;
        $data = $this->data;
        $solr_log = array();
        $is_customer_new_registered = TRUE; 
        if (!empty($customer_id)) {
            $cust = $customer_id;
            $lead_id = $this->getLeadIdfromCustomerId($customer_id);
            if(!$lead_id > 0){
                $is_customer_new_registered = TRUE;
                $lead_id = $this->checkExistingLead($mobile);
            }
            
        }else{

        $lead_id = $this->checkExistingLead($mobile);
        }
        if (!$lead_id) {

            /*
             * New Lead Can be add by cron only
             */
            // $this->addLead($data, $mobile, $customer_id, $message = '');

            return "success";
        }
        
        $solr_core = 'LEAD';
        
        // echo "<pre>"; print_r($data); exit;

        if (isset($data['status']) && ($data['status'] == 'ORDERED')) {
            $solr_core = 'BOTH';
            $this->closeAllleadTasks($lead_id);

            $campaign_event_no = 0;
            if (isset($data['campaign_event_no']) && !empty($data['campaign_event_no'])) {
                $campaign_event_no = $data['campaign_event_no'];
            }

            $campaing_id = 0;
            if (!empty($campaign_event_no)) {
                $campaing_data = $this->db_crm->query("SELECT campaign_id FROM `campaign_events`  WHERE event_no = " . $campaign_event_no);
                if ($campaing_data->num_rows) {
                    $campaing_id = $campaing_data->row['campaign_id'];
                }
            }


            $user_id = $this->getLeadUser($lead_id);
            $task_sql = "INSERT INTO tasks SET 
                        status_update_date  = NOW(), 
                        name                = 'ORDERED',   
                        status              = 'CLOSED', 
                        priority            = '1', 
                        user_id             =  $user_id, 
                        campaign_event_no   = " . "'" . $campaign_event_no . "',
                        campaign_id       = " . "'" . $campaing_id . "',
                        created             = NOW(),  
                        modified            = NOW(),  
                        lead_id             =" . "'" . $lead_id . "', 
                        followup_date       =" . "'" . date('Y-m-d') . "'";


            $this->db_crm->query($task_sql);

            $last_tasks_id = $this->db_crm->getLastId();
            // echo $last_tasks_id; die;
            $comment = $message . " New Order recieved on date -" . date('Y-m-d');
            $this->addTaskComment($last_tasks_id, $comment);
            
           // $this->addSolrTaskChangelog($last_tasks_id,$comment);
            
            $data['status_update_date'] = date('Y-m-d H:i:s');
            
             if (isset($data['order_id']) && !empty($data['order_id'])) {
               
                $category_list = $this->getProductsCategoryfromOrderID($data['order_id']);
                
                if(!empty($category_list)){
                    $this->updateLeadPreferences($category_list,$lead_id);
                }
            }
        }

        if ($lead_id) {
            $q = "UPDATE leads SET ";
            $i = 0;
            //echo "<pre>"; print_r($data);
            $user_id = $this->getLeadUser($lead_id);
            $data['user_id'] = (!empty($user_id)) ? $user_id : 0;
            foreach ($data as $key => $value) {

                if ($key == 'campaign_event_no') {
                    continue;
                }
                if ($key == 'is_registered') {
                    continue;
                }
                if ($key == 'refer_by_customer_id') {
                    continue;
                }
                if ($key == 'order_id') {
                    continue;
                }
                $value = addslashes($value);

                if ($key == 'status' && $value != 'OPEN') {
                    $value = 'OPEN';
                }
                if ($key == 'name' && empty(trim($value))) {
                    continue;
                }
                if ($key == 'business_name' && empty(trim($value))) {
                    continue;
                }
                  /*if ($key == 'country_id' && !empty($value)) {

                  $get_country_crm = $this->db_crm->query("SELECT id FROM countries WHERE name = '" . $value . "' ")->row;

                  if (!empty($get_country_crm)) {

                  $country_id = $get_country_crm['id'];
                  } else {

                  $add_country_crm = "INSERT INTO countries SET
                  name      =   '" . $value . "',
                  status      =   '1' ";

                  $this->db_crm->query($add_country_crm);
                  $country_id = $this->db_crm->getLastId();
                  }

                  $value = $country_id;
                  }*/

                  /*if ($key == 'state_id' && !empty($value)) {

                  $get_state_crm = $this->db_crm->query("SELECT id FROM states WHERE id = '" . $value . "' ")->row;

                  if (!empty($get_state_crm)) {

                  $state_id = $get_state_crm['id'];
                  } else {

                  $add_state_crm = "INSERT INTO states SET
                  name      =   '" . $value . "',
                  country_id    =   '" . $country_id . "',
                  status      =   '1'";

                  $this->db_crm->query($add_state_crm);
                  $state_id = $this->db_crm->getLastId();
                  }

                  $value = $state_id;
                  }*/

                  /*if ($key == 'city' && !empty($value)) {

                    $get_city_crm = $this->db_crm->query("SELECT id FROM cities WHERE id = '" . $value . "' ")->row;

                  if (!empty($get_city_crm)) {

                    $city_id = $get_city_crm['id'];
                  } else {

                      $add_city_crm = "INSERT INTO cities SET
                      name      =   '" . $value . "',
                      state_id    =   '" . $state_id . "',
                      country_id    =   '" . $country_id . "',
                      status      =   '1',
                      created     =   NOW(),
                      modified    =   NOW()";

                      $this->db_crm->query($add_city_crm);
                      $city_id = $this->db_crm->getLastId();
                  }

                  $key = 'city_id';
                  $value = $city_id;
                  }*/

                if ($key == 'city') {
                    if (!empty($value)) {
                        $city_id = $this->getCrmCityId($value);
                        $key = 'city_id';
                        $value = $city_id;
                    } else {
                        $key = 'city_id';
                        continue;
                    }
                }

                if ($key == 'state_id') {
                   continue;
                }

                if ($key == 'country_id') {
                    continue;
                }
                 

                if ($i == count($data) - 1) {
                    $q .= $key . " = " . "'" . $value . "', ";
                } else {
                    $q .= $key . " = " . "'" . $value . "', ";
                }

                $i++;
            }
            $q .= " modified = NOW() ";
//            $q .= ", is_registered = '1'";
            // $q .= ", is_registered = 1";
            $q .= " WHERE id=" . $lead_id;
            
            // echo print_r($q); die;
            //$this->createLog($lead_id, $message);
            $lead_entry = $this->db_crm->query($q); 
            //echo '<Pre>'; print_r($this->db_crm); exit;
            if (!empty($customer_id)) {
                $success = $this->addCustomerId($lead_id, $customer_id);
            } 
            
            if(isset($data['refer_by_customer_id']) && !empty($data['refer_by_customer_id'])){
                $this->addReferByLeadId($lead_id, $data['refer_by_customer_id']);
            }
            
            if(isset($data['app_install_date']) && !empty($data['app_install_date'])){
                $this->addAppInstalledLeadInDesktopDialer($lead_id);
            }

            if ($is_customer_new_registered) {
                $type = '';
                if (isset($data['cart']) && !empty($data['cart'])) {
                    $type = 'CART';
                }
                $this->leadMovementForGirnarDesktopDialer($lead_id, $type);
            }

            if($lead_entry){
                
                if (SOLR_ENABLED_CRM_WEB == 1) {
                    $this->insertSolrChangeLog($lead_id, $solr_core);
                }

                return "success";
            }else{
                 return "failure";
            }
            
           
            return true;
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

    public function checkExistingLeadRefer($lead_id) {

        if (empty($lead_id)) {
            return TRUE;
        }
        $crm_query = "SELECT * FROM `leads_website_refer_ids` "
                . "WHERE "
                . "lead_id = " . (int)$lead_id . " LIMIT 1";

        return $this->db_crm->query($crm_query)->num_rows ? TRUE : FALSE;
    }

    
    /**
     * @author Vishnu Shekhawat
     * @description merge two leads if customer update his mobile number 
     *  if alternate number is not available  in crm than add alternate contact to lead
     *  if both mobile number belongs to same lead return true
     *  if both leads are different and assigned to same person or unassigned lead then merge
     *  if both leads are different and assigned to different person send an email request to merge
     * @return boolean
     */
    public function addAlternateContactInLead(){
           
        $customer_id = $this->customer_id;
        $mobile = $this->mobile;
        $message = $this->message;
        $data = $this->data;
      
        if (!empty($customer_id)) {
            $cust = $customer_id;
           // $lead_id = $this->getLeadIdfromCustomerId($customer_id);
            
           // if(!$lead_id > 0){    
                $lead_id = $this->checkExistingLead($mobile);
           // }
            
        }else{
            $lead_id = $this->checkExistingLead($mobile);
        }
    
        if ($lead_id) {
            
            $alternate_mobile = $data['alternate_number'];
            $alternate_lead_id = $this->checkExistingLead($alternate_mobile);
            
            if (empty($alternate_lead_id)) {
                $sql = "INSERT INTO lead_contacts SET 
                            lead_id =" . "'" . $lead_id . "',
                            status = '1',  
                            mobile =" . "'" . $alternate_mobile . "'";
                
                 $this->db_crm->query($sql);
                 
                 $success = TRUE;
            }else if(!empty($alternate_lead_id) && $alternate_lead_id === $lead_id){
         
                $success = TRUE;
            }else if($alternate_lead_id != $lead_id){
               
                $main_number_assigned_user_id = $this->getLeadUser($lead_id);
               $alternate_number_assigned_user_id =  $this->getLeadUser($alternate_lead_id);
               
               if($main_number_assigned_user_id == $alternate_number_assigned_user_id || empty($alternate_number_assigned_user_id) || empty($main_number_assigned_user_id) ){
                   $success = TRUE;
                   $lead_ids = array($lead_id , $alternate_lead_id);
                   $comment = "These leads are same as $mobile and $alternate_mobile are used by same customer. ";
               
                   $response = $this->sendRequestToMergeLead($lead_ids , $comment);
                   
                 
                   /**
                    * Merge lead
                    */
                   
               }else{
                   /**
                    * Send mail
                    */
                   $subject = "Request for merge two leads as $mobile and $alternate_mobile leads are same.";
                   $message = "Please merge these leads as both leads are same.Leads mobile no are $mobile and $alternate_mobile";
                   
                   $this->sendmailToCrmTeam($subject , $message);
                  $success = FALSE;
               }
               
            }
   
            if (!empty($success)) {
                if (SOLR_ENABLED_CRM_WEB == 1) {
                    $this->insertSolrChangeLog($lead_id , 'LEAD');
                }
                $this->addCustomerId($lead_id, $customer_id);
            } 
            
            return true;
        }
    }
    
    /**
     * @author Vishnu Shekhawat
     * @description send request to crm to merge leads
     * @param array $lead_ids
     * @param string $comment
     * @return boolean
     */
    public function sendRequestToMergeLead($lead_ids , $comment) {

        $apiData['lead_ids'] = $lead_ids;
        $apiData['comment'] = $comment;

        $jsonData = json_encode($apiData);

        $url = 'https://www.wholesalebox.biz/staging/cron/mergeDuplicateLeads';

        if (SITE_ENVIRONMENT == 'Production') {
            $url = 'https://www.wholesalebox.biz/cron/mergeDuplicateLeads';
        }

       //  $url = 'http://localhost/wholesalebox/cron/mergeDuplicateLeads';
        $curl = curl_init();

        // Set SSL if required
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
        }
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($jsonData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // grab URL and pass it to the browser
        $result = curl_exec($curl); 


        $json = curl_exec($curl);
        $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);
        curl_close($curl);
        
        return true;
    }
    
    
    /**
     * @author Vishnu Shekhawat
     * @description send request to crm to merge leads
     * @param array $lead_ids
     * @param string $comment
     * @return boolean
     */
    public function addAppInstalledLeadInDesktopDialer($lead_id) {

        $apiData['lead_id'] = $lead_id;

        $jsonData = json_encode($apiData);

        $url = 'https://www.wholesalebox.biz/staging/webapi/DesktopDialer/addAppInstalledLeadInDesktopDialer';

        if (SITE_ENVIRONMENT == 'Production') {
            $url = 'https://www.wholesalebox.biz/webapi/DesktopDialer/addAppInstalledLeadInDesktopDialer';
        }

       //  $url = 'http://localhost/wholesalebox/cron/mergeDuplicateLeads';
        $curl = curl_init();

        // Set SSL if required
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
        }
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // grab URL and pass it to the browser
        $result = curl_exec($curl); 


        $json = curl_exec($curl);
        $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);
        curl_close($curl);
        
        return true;
    }
    
    
    /**
     * @author Vishnu Shekhawat
     * @description send request to crm to merge leads
     * @param array $lead_ids
     * @param string $comment
     * @return boolean
     */
    public function leadMovementForGirnarDesktopDialer($lead_id, $type = '') {

        $apiData = array();
        $apiData["lead_id"] = $lead_id;

        $jsonData = json_encode($apiData);

        $url = 'https://www.wholesalebox.biz/webapi/DesktopDialer/leadMovementForGirnarDesktopDialer/' . $lead_id;
        if ($type == 'CART') {
           // $url = 'https://www.wholesalebox.biz/webapi/DesktopDialer/cartleadsMovementForGirnarDesktopDialer/' . $lead_id;
        }


        //  $url = 'http://localhost/wholesalebox/cron/mergeDuplicateLeads';
        $curl = curl_init();

        // Set SSL if required
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // grab URL and pass it to the browser
        $result = curl_exec($curl);

        /**
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => array(
          
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Type: application/json",
                "Host: www.wholesalebox.biz",
                "accept-encoding: gzip, deflate",
                "cache-control: no-cache"
            ),
        ));
        */

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return true;
    }

    /**
     * @author Vishnu Shekhawat
     * @description send email to ravindra shekhawat
     * @param array $lead_ids
     * @param string $comment
     * @return boolean
     */
    public function sendMailToCrmTeam($subject , $message) {

       
        $apiData['subject'] = $subject;
        $apiData['message'] = $message;

        $jsonData = json_encode($apiData);

        $url = 'https://www.wholesalebox.biz/staging/cron/sendMailToCrmTeam';

        if (SITE_ENVIRONMENT == 'Production') {
            $url = 'https://www.wholesalebox.biz/cron/sendMailToCrmTeam';
        }

        // $url = 'http://localhost/wholesalebox/cron/sendMailToCrmTeam';
        $curl = curl_init();

        // Set SSL if required
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
        }

       $curl = curl_init();

        // Set SSL if required
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
        }
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($jsonData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // grab URL and pass it to the browser
        $result = curl_exec($curl); 


        $json = curl_exec($curl);
        $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);
        curl_close($curl);
        
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
                        lead_id = $lead_id, 
                        customer_id = $customer_id,   
                        status = 1";
            try {
                $this->db_crm->query($q); return true;
            } catch (\Exception $e) {
                return true;
            }
        }
        return TRUE;
    }

    public function checkExistingCustomer($lead_id, $customer_id) {

        if (empty($lead_id) || empty($customer_id)) {
            return TRUE;
        }
        $crm_query = "SELECT * FROM `leads_website_customer_ids` "
                . "WHERE "
                . "lead_id = $lead_id AND customer_id = $customer_id LIMIT 1";

        return $this->db_crm->query($crm_query)->num_rows ? TRUE : FALSE;
    }

    public function createLog($lead_id, $message) {
        $l_d = serialize($this->getLeadData($lead_id));
        $this->db_crm->query(
                "INSERT INTO activity_changelog SET 
      lead_id =" . $lead_id . " , 
      log ='" . $l_d . "' , 
      name ='" . $message . "' , 
      created = NOW() ,
      modified = NOW()"
        );
    }

    public function getLeadData($lead_id) {
        return $this->db_crm->query("SELECT id,cart FROM leads WHERE id = " . $lead_id)->row;
    }

    public function addLead($data, $mobile, $customer_id = '', $message = '') {

        $q = "INSERT INTO leads SET ";
        $date = date('Y-m-d H:i:s');
        $i = 0;
        foreach ($data as $key => $value) {
            if ($i == count($data) - 1) {
                $q .= $key . " = " . "'" . $value . "'";
            } else {
                $q .= $key . " = " . "'" . $value . "',";
            }
            $i++;
        }

        $q .= ", created = NOW(), status_update_date = '" . $date . "'";
        $q .= ", mobile =" . "'" . $mobile . "'";
        $q .= isset($cust) ? ", customer_id =" . $cust . ", is_registered = 2" : ', is_registered = 2';
        $this->db_crm->query($q);

        $lead_id = $this->db_crm->getLastId();
        if (empty($data['user_id'])) {
            $user_id = $this->getLeadUser($lead_id);
        } else {
            $user_id = $data['user_id'];
        }


        $task_sql = "INSERT INTO tasks SET 
      status_update_date  = NOW(), 
      name        = 'New Signup',   
      status        = 'OPEN', 
      priority      = '1', 
      user_id       = " . "'" . $user_id . "', 
      created       = NOW(),  
      modified      = NOW(),  
      lead_id       =" . "'" . $lead_id . "', 
      followup_date     =" . "'" . date('Y-m-d') . "'";
        $this->db_crm->query($task_sql);

        $last_tasks_id = $this->db_crm->getLastId();
        $this->addTaskComment($last_tasks_id, $message . " User registered on date.- " . date('Y-m-d'));
        return true;
    }

    public function closeOrderedTasks($task_id) {
        $this->db_crm->query(
                "UPDATE tasks SET 
            status = 'CLOSED' , 
            modified = " . "'" . date('Y-m-d H:i:s') . "' 
            WHERE id =" . $task_id
        );
    }

    /**
     * @description Close all task for lead
     * @author Vishnu Shekhawat
     * @param type $lead_id
     * @return boolean TRUE
     */
    public function closeAllleadTasks($lead_id) {

        $this->db_crm->query(
                "UPDATE tasks SET 
            status = 'CLOSED' , 


      modified = " . "'" . date('Y-m-d H:i:s') . "' 
      WHERE lead_id =" . $lead_id
        );



        return TRUE;
    }

    public function findTask($lead_id, $data) {
        $sql = "SELECT * FROM tasks WHERE lead_id=" . $lead_id;
        $sql .= isset($data['status']) ? " AND status =" . "'" . $data['status'] . "'" : '';

        return $this->db_crm->query($sql);
    }

    public function updateLeadFromCustomerId($lead_data, $customer_id) {
        if (!empty($lead = $this->db_crm->query("SELECT id, mobile FROM leads WHERE customer_id = " . $customer_id)->row)) {
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
        $user_q = $this->db_crm->query("SELECT user_id FROM task_comments WHERE task_id=" . $task_id);



        if ($user_q->num_rows) {
            $user_id = $user_q->row['user_id'];
        } else {
            $user_id = '';
        }
        $sql = "INSERT INTO task_comments SET 
        task_id =" . "'" . $task_id . "',
        comment =" . "'" . $comm . "', ";

        if (!empty($user_id)) {
            $sql .= "user_id ='" . $user_id . "'";
        }
        $sql .= "created = NOW(),
        modified= NOW() 
        ";
        $this->db_crm->query($sql);
    }

    public function updateLeadStatus($data) {
        //echo "<pre>"; print_r($data); exit;

        $q = "UPDATE leads SET ";
        $q .= " lead_status_id = " . $data['order_status_id'];
        if ($data['order_status_id'] == '5' || $data['order_status_id'] == '15') {
            $q .= ", is_ordered = 1 ";
        }
        $q .= ", modified = NOW() ";
        $q .= " WHERE mobile=" . $data['mobile'];

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
        $crm_query = "SELECT id FROM cities c WHERE name = '" . $city_name . "'";
        return $this->db_crm->query($crm_query)->num_rows ? $this->db_crm->query($crm_query)->row['id'] : 0;
    }

    public function getCrmStateId($state_name) {
        $crm_query = "SELECT id FROM states c WHERE name = '" . $state_name . "'";
        return $this->db_crm->query($crm_query)->num_rows ? $this->db_crm->query($crm_query)->row['id'] : 0;
    }

    public function getCrmCountryId($country_name) {
        $crm_query = "SELECT id FROM countries c WHERE name = '" . $country_name . "'";
        return $this->db_crm->query($crm_query)->num_rows ? $this->db_crm->query($crm_query)->row['id'] : 0;
    }

    public function getUserFromCity($city_id) {
        $crm_query = "SELECT user_id FROM city_user_data cd INNER JOIN users u on (u.id = cd.user_id) WHERE u.status = 1 AND city_id = '" . $city_id . "'";
        return $this->db_crm->query($crm_query)->num_rows ? $this->db_crm->query($crm_query)->row['user_id'] : CRM_TEAM_LEAD_USER_ID;
    }

    public function getLeadUser($lead_id) {
        $crm_query = "SELECT user_id FROM leads WHERE id = " . $lead_id;
        return $this->db_crm->query($crm_query)->num_rows ? $this->db_crm->query($crm_query)->row['user_id'] : CRM_TEAM_LEAD_USER_ID;
    }

    public function checkOpenAccounts($mobile) {
        if ($this->lead_log == 1) {
            $crm_query = "SELECT id FROM accounts WHERE mobile= " . "'" . $mobile . "'";

            return $this->db_crm->query($crm_query)->num_rows ? $this->db_crm->query($crm_query)->row['id'] : 0;
        } else {
            return 0;
        }
    }

    public function checkExistingContacts($mobile) {
        if ($this->lead_log == 1) {
            $crm_query = "SELECT id FROM contacts WHERE mobile= " . "'" . $mobile . "'";

            return $this->db_crm->query($crm_query)->num_rows ? $this->db_crm->query($crm_query)->row['id'] : 0;
        } else {
            return 0;
        }
    }

    public function checkExistingLead($mobile) {
        
        if (empty($mobile)) {
            return 0;
        }
        $mobile = trim($mobile);
        if ($mobile != 'skipping') {
            if ($this->lead_log == 1) {
                if (strlen($mobile) == 10) {
                    $crm_query = ""
                            . "SELECT Leads.id FROM leads Leads WHERE (status != 'MERGE_DEAD' AND mobile = '" . $mobile . "')"
                            . "UNION "
                            . "SELECT LeadContacts.lead_id AS `LeadContacts__lead_id` FROM lead_contacts LeadContacts WHERE (mobile = '" . $mobile . "')";
                } else {
                    $crm_query = "SELECT Leads.id FROM leads Leads WHERE (status != 'MERGE_DEAD' AND mobile LIKE '" . $mobile . "%')"
                            . "UNION "
                            . "SELECT LeadContacts.lead_id AS `LeadContacts__lead_id` FROM lead_contacts LeadContacts WHERE (mobile LIKE '" . $mobile . "%')";
                }
                
                $data = $this->db_crm->query($crm_query);
                
                return $data->num_rows ? $data->row['id'] : 0;
            } else {
                return 0;
            }
        }
    }
    
    /**
     * Kusum Joshi
     * @param type $order_no
     * @return type
     *  gets category_ids for given order_no
     */
    public function getProductsCategoryfromOrderID($order_id){
        
        $category_list = '';
         $sql = "SELECT GROUP_CONCAT(DISTINCT(opc.category_id)) as category_id
                                FROM " . DB_PREFIX . "order o
                                INNER JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id)
                                INNER JOIN " . DB_PREFIX . "product_to_category opc ON (op.product_id = opc.product_id)
                                WHERE o.order_id = " .  (int) $order_id;
                        $query = $this->db->query($sql);
                        if ($query->num_rows) {
                            $categories = $query->row;
                            $category_list = $categories['category_id'];
                        }
         return $category_list;
    }
    
    /**
     * Kusum Joshi
     * @param type $category_list
     * @param type $lead_id
     * @return boolean
     * updates lead prefernces for given category_list
     */
    public function updateLeadPreferences($category_list,$lead_id){
        
//        $category = explode(',',$category_list);
        
        $crm_query = "SELECT GROUP_CONCAT(DISTINCT(crm_category_id)) as category_id "
                . "FROM category_comparison "
                . "WHERE website_category_id IN (" . $category_list . ")";
        $query = $this->db_crm->query($crm_query);
         if ($query->num_rows) {
              $category_compare = $query->row;
              if(!empty($category_compare['category_id'])){
                  $category = explode(',',$category_compare['category_id']);
                  $preference_list = $this->getLeadPreferencesforLeadId($lead_id);
                  if(empty($preference_list)){
                      $preference_diff = $category;
                  }else{
                      $preference_list = explode(',',$preference_list);
                    $preference_diff = array_diff($category,$preference_list);
                  }
                  foreach($preference_diff as $preference_category_id){
                       $sql = "INSERT INTO lead_preferences SET 
                                lead_id =" . "'" . $lead_id . "',
                                category_id =" . "'" . $preference_category_id . "',"
                               ."status = 1";
                        $this->db_crm->query($sql);
                  }
              }
         }
        return true;
    }
    
    /**
     * Kusum Joshi
     * @param type $lead_id
     * @return type
     * collects all prefernces for given lead
     */
    public function getLeadPreferencesforLeadId($lead_id){
        
         $crm_query = "SELECT GROUP_CONCAT(DISTINCT(category_id)) as category_id "
                . "FROM lead_preferences "
                . "WHERE lead_id = '" . $lead_id . "'";
        $query = $this->db_crm->query($crm_query);
         if ($query->num_rows) {
             $prefernces = $query->row;
         }
         
        return $query->num_rows ?$query->row['category_id'] : array();

    }
    
    /**
     * Kusum Joshi
     * @param type $customer_id
     * @return int
     * get lead_id based on customer_id
     */
    public function getLeadIdfromCustomerId($customer_id) {

        if (empty($customer_id)) {
            return 0;
        }
        $crm_query = "SELECT lead_id FROM `leads_website_customer_ids` "
                . "WHERE "
                . "customer_id = $customer_id LIMIT 1";
        
        $lead = $this->db_crm->query($crm_query);
        
        return $lead->num_rows ? $lead->row['lead_id'] : 0;
    }
    
    public function insertLeadUpdation(){
        
       // Update in Database
        $update_lead['customer_id'] = $this->customer_id;
        $update_lead['data'] = $this->data;
        $update_lead['mobile'] = $this->mobile;
        $update_lead['message'] = $this->message;
        
        $failure_log=serialize($update_lead);
        $queue_name = 'lead_updation_queue';
        $failure_entry = "INSERT INTO ".DB_PREFIX."crm_failure_queue_log SET 
                        failure_log  = '".$failure_log."',
                        queue_name       = '".$queue_name."',
                        created  = NOW() ";
//        print_r($failure_entry);die;
        $this->db->query($failure_entry);
        return true;
    }
    public function addSolrTaskChangelog($task_id,$comment){
        
        $query="SELECT * FROM tasks
                where id='".$task_id."'";
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
                where l.id='".$lead_id."'";
        
         $lead = $this->db_crm->query($query);
        if ($lead->num_rows) {
             $lead_data = $lead->row;
        }  
        return $lead_data;

    }
    /**
     * 
     * @param type $data
     * @return type
     * 
     * insert leads/tasks add/updates in ChangeLogToSolr
     */
    
    public function insertSolrChangeLog($lead_id , $solr_core){
        
     
        $sql = "INSERT INTO solr_change_logs SET 
                                lead_id =" . "'" . $lead_id . "',
                                core =" . "'" . $solr_core . "',
                                solr_flag = '0'";
        
        try{
            $this->db_crm->query($sql);
        }catch(\Exception $e){
            return true;
        }
        
                        
        return TRUE;        
        
    }

}
