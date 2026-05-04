<?php

require_once __DIR__ . '/db/db.php';

class Ordertag {

    private $data;
    private $db_crm;

    function __construct($data) {
        $this->data = $data;
        $this->db_crm = new Database\DB( DBCRM_SERVERS );
        $this->db = new Database\DB( DB_SERVERS );
    }
    
    /**
     * Kusum Joshi
     * @return string|int
     * 
     * updates salesstaff 
     */
    public function updateSalesStaff() {
        
        $order_id = $this->data['order_id'];
        $order_no = $this->data['order_no'];
        $sales_staff_id = $this->data['sales_staff_id'];
        $customer_id = $this->data['customer_id'];
        $amount = $this->data['amount'];
        $type = $this->data['type'];
        $tag_type = $this->data['tag_type'];
        $user_id = $this->data['user_id'];
        
        if (!empty($order_id) && isset($sales_staff_id) && $sales_staff_id > 0) {
            // Get CRM user_id from Sales Staff id
            
            $crm_user_id = $this->db->query("SELECT crm_user_id FROM " . DB_PREFIX . "sales_staff WHERE staff_id = " . (int) ($sales_staff_id))->row['crm_user_id'];

            if ($crm_user_id == 0) {
                return "This User is not registered in CRM";
            }

            // Get Total no of Orders Placed by Customer Where Order status_id is not zero.
            $this->db->query("set @row_number=0");
            $sql = "SELECT (@row_number := @row_number +1) as num, o.order_id, o.total, o.date_added, o.self_order
                            FROM " . DB_PREFIX . "order o
                            INNER JOIN " . DB_PREFIX . "suborder so ON (o.order_id = so.order_id)
                            WHERE o.customer_id = " . (int) $customer_id . " 
                            AND so.order_status_id != 0
							GROUP BY o.order_id
							ORDER BY o.order_id ASC";
            $orders = $this->db->query($sql)->rows; // status !=0
            $this->db->query("set @row_number:=NULL");

            $repeate_order_count = NULL;
            foreach ($orders as $key => $value) {
                if ($value['order_id'] == $order_id) {
                    $sql = "SELECT product_id, ((oop.price_per_piece + oop.discount_per_piece) * oop.piece_in_set * oop.quantity ) AS subTotal
							FROM " . DB_PREFIX . "order o
							INNER JOIN " . DB_PREFIX . "suborder so ON (o.order_id = so.order_id)
							INNER JOIN oc_order_product oop ON (so.order_id = oop.order_id)
							WHERE o.order_id = " . $order_id . " 
							GROUP BY oop.product_id";
                    $sub_order = $this->db->query($sql)->rows;
                    $sub_total = 0;
                    foreach ($sub_order as $sub) {
                        $sub_total = $sub_total + $sub['subTotal'];
                    }
                    $repeate_order_count = $value['num'];
                    $amount = $sub_total;
                    $date_added = $value['date_added'];
                    $self_order = $value['self_order'];
                }
            }
            if ($repeate_order_count == NULL) {
                return "This Order Can't Be Tag because Order is in Missing State";
            }
            if($self_order == 0){
                $check_result = $this->checkOrderforSelf($customer_id,$order_id,$sales_staff_id,$date_added);
            }
            $diff = abs(strtotime(date("Y-m-d")) - strtotime($date_added));
            $order_date_diff = floor(($diff) / (60 * 60 * 24));

            if ($order_date_diff < 35) {

                $result = $this->updateAgentOrder($order_id, $order_no, $customer_id, $crm_user_id, $repeate_order_count, $amount, $type);

                /*
                 * Update lead in crm
                 */
                /*
                  $lead_data = array(
                  'user_id'       => $crm_user_id,
                  'customer_id'   => $customer_id,
                  'order_no'      => $order_no,
                  'is_ordered'    => 1,
                  'lead_show'     => 1,
                  'is_registered' => 1
                  );
                  $tag_by = $this->user->getUserName($this->user->getId())['name'];
                  $update_lead = $this->frontend_model_lead_lead->updateLeadByTagOrder($lead_data, $tag_by);
                 */

                if (is_array($result) && $result['force_tag'] == 1) {
                    $return_msg = $result['return_msg'];
                    $result = array();
                    $result['return_msg'] = $return_msg;
                    $result['force_tag'] = 1;
                } elseif ($result == "done" && ($type == 'add' || $type == 'force_tag')) {
                    /*
                     * check tagged sales staff
                     */
                    $check_tagged_sales_staff = $this->db->query("SELECT id FROM " . DB_PREFIX . "order_sales_staff WHERE order_id = " . (int) ($order_id) . " AND sales_staff_id = " . (int) ($sales_staff_id) . "")->rows;
                    if (empty($check_tagged_sales_staff)) {
                        $result = $this->db->query("INSERT INTO " . DB_PREFIX . "order_sales_staff SET order_id = " . (int) ($order_id) . ", sales_staff_id = " . (int) ($sales_staff_id) . ", user_id = " . $user_id .", tag_type = '" . $tag_type . "' , date_added = now()");
                    }
                    $result = "done";
                } elseif ($result == "done" && $type == 'remove') {
                    $result = $this->db->query("DELETE FROM " . DB_PREFIX . "order_sales_staff WHERE order_id = " . (int) $order_id . " AND sales_staff_id = '" . (int) ($sales_staff_id) . "'");
                    $result = "done";
                }
            } else {
                return "Order can't be tag because this order Ordered before 30 days.";
            }
        }
        return $result;
    }
    
    /**
     * Kusum Joshi
     * @param type $order_id
     * @param type $order_no
     * @param type $customer_id
     * @param type $crm_user_id
     * @param type $repeate_order_count
     * @param type $amount
     * @param type $type
     * @return string|int
     * 
     */
    public function updateAgentOrder($order_id, $order_no, $customer_id, $crm_user_id, $repeate_order_count, $amount, $type) {
        $remarks = array();
       
        // Get User Role and it's defined Commission From user table it may be user on an agent.
        $user = $this->db_crm->query("SELECT role_id, name, first_order_commission_rate, next_order_commission_rate FROM users WHERE id = " . $crm_user_id)->row;
        if (isset($user['role_id'])) {
            $user_role_id = $user['role_id'];
        } else {
            return "Role is not defined in CRM";
        }

        // Get agent_id and user_id from lead table
        $lead = $this->db_crm->query("SELECT lead_id from leads_website_customer_ids WHERE customer_id = " . $customer_id)->row;

        if (empty($lead)) {
            $lead = $this->db_crm->query("SELECT id from leads WHERE customer_id = " . $customer_id)->row;
        }


        //print_r($lead); die; or crm_user_id 217 is case of via.
        if (empty($lead) || $crm_user_id == 217) { // lead is not present in Crm 
            //return "Customer's Lead is not present in CRM.";
            return "done";
        } else {
            $is_expired = $this->db_crm->query("SELECT is_expired from leads INNER JOIN leads_website_customer_ids lwci ON lwci.lead_id = leads.id WHERE lwci.customer_id = " . $customer_id . " AND is_expired = 1 AND agent_id = user_id")->row;
            if (!empty($is_expired)) {
                return "This Lead is Expired. so, Order can't be tag.";
            }
        }

        $commission_rates = $this->getOrderCommissionRates(); // Get All commission Rates which are in setting table
        $order_by = $crm_user_id;
        if ($user_role_id == 7) { // Ordered by AGENT
            // Here we check this customer is assigned to Ordered tag user or not
            $get_Agent_lead = "SELECT l.agent_id, u.name from leads l INNER JOIN users u ON (l.agent_id = u.id) INNER JOIN leads_website_customer_ids lwci ON (lwci.lead_id = l.id) WHERE lwci.customer_id =" . $customer_id . " AND is_expired = 0";
            $lead_user = $this->db_crm->query($get_Agent_lead)->row;
            if (empty($lead_user) && $type == 'add') { // If lead is not Related to Agent
                $u_id = $this->db_crm->query("SELECT l.user_id, u.name from leads l INNER JOIN users u ON (l.user_id = u.id) INNER JOIN leads_website_customer_ids lwci ON (lwci.lead_id = l.id) WHERE lwci.customer_id =" . $customer_id)->row;
                $return['return_msg'] = "This Customer is assigned to User " . $u_id['name'];
                $return['force_tag'] = 1;
                return $return;
            }
            if (isset($lead_user['agent_id']) && $type == 'add' && $lead_user['agent_id'] != $crm_user_id) {
                $return['return_msg'] = "This Customer is assigned to Agent " . $lead_user['agent_id'];
                $return['force_tag'] = 1;
                return $return;
            }

            $agent_id = $crm_user_id;
            $agent_first_order_commission_rate = ($user['first_order_commission_rate'] == 0) ? $commission_rates['agent']['first_order_commission_rate'] : $user['first_order_commission_rate'];
            $agent_next_order_commission_rate = ($user['next_order_commission_rate'] == 0) ? $commission_rates['agent']['next_order_commission_rate'] : $user['next_order_commission_rate'];
            $sales_next_order_commission_rate = 0;
        } else {
            // get customer is related to agent or not
            $sql = "SELECT l.agent_id, l.user_id, u.name, u.first_order_commission_rate, u.next_order_commission_rate from leads l INNER JOIN users u ON (l.agent_id = u.id) INNER JOIN leads_website_customer_ids lwci ON (lwci.lead_id = l.id) WHERE lwci.customer_id = " . $customer_id . " AND l.user_id = " . $crm_user_id . " AND u.role_id = 7 AND l.is_expired = 0";
            $agent = $this->db_crm->query($sql)->row;
            $agent_id = isset($agent['agent_id']) ? $agent['agent_id'] : '';

            if ($agent_id == '') { // if customer is not related to agent then return true
                $u_id = $this->db_crm->query("SELECT l.user_id, u.name from leads l INNER JOIN users u ON (l.user_id = u.id) INNER JOIN leads_website_customer_ids lwci ON (lwci.lead_id = l.id) WHERE lwci.customer_id =" . $customer_id)->row;
                if (isset($u_id['user_id']) && $type == 'add' && $u_id['user_id'] != $crm_user_id) {
                    $return['return_msg'] = "This Customer is assigned to User " . $u_id['name'];
                    $return['force_tag'] = 1;
                    return $return;
                }
                return "done";
            } elseif ($repeate_order_count == 1) {
                return "This is an Agent First Order So, it will be tag agent : " . $agent['name'];
            } else {
                $agent_first_order_commission_rate = ($agent['first_order_commission_rate'] == 0) ? $commission_rates['agent']['first_order_commission_rate'] : $agent['first_order_commission_rate'];
                $agent_next_order_commission_rate = ($agent['next_order_commission_rate'] == 0) ? $commission_rates['agent']['next_order_commission_rate'] : $agent['next_order_commission_rate'];
                $sales_next_order_commission_rate = ($user['next_order_commission_rate'] == 0) ? $commission_rates['telesales']['next_order_commission_rate'] : $user['next_order_commission_rate'];
            }
        }

        $this->db_crm->query("DELETE FROM agent_commission where order_id = " . $order_id . " AND order_no = " . $order_no . "");

        $order_commission_rate = ($repeate_order_count == 1) ? $agent_first_order_commission_rate : $agent_next_order_commission_rate;
        $tentative_amount = ($amount * $order_commission_rate) / 100;

        if ($type == 'add') {
            $sql = "INSERT INTO agent_commission SET 
                    agent_id					= " . $agent_id . ", 
                    order_by 					= " . $order_by . ", 
                    order_id 					= " . $order_id . ", 
                    order_no 					= '" . $order_no . "', 
                    customer_id 				= " . $customer_id . ", 
                    tentative_amount            = " . $tentative_amount . ", 
                    status 						= 'tentative', 
                    agent_first_order_commission_rate 		= " . $agent_first_order_commission_rate . ", 
                    agent_next_order_commission_rate 		= " . $agent_next_order_commission_rate . ", 
                    sales_next_order_commission_rate        = " . $sales_next_order_commission_rate . ", 
                    order_commission_rate 		= 0, 
                    repeate_order_count 		= " . $repeate_order_count . ", 
                    date_added 					= now()";
            $this->db_crm->query($sql);

            if (isset($lead['lead_id'])) {
                $lead_id = $lead['lead_id'];
            } else {
                $lead_id = $lead['id'];
            }
            $customer_info = $this->db_crm->query("SELECT name, business_name FROM leads WHERE id =" . $lead_id)->row;
            $customer_info['business_name'] = !empty($customer_info['business_name']) ? "(" . $customer_info['business_name'] . ")" : '';

            $agent_info = $this->db_crm->query("SELECT name, mobile FROM users WHERE id = " . $agent_id)->row;
            $cur_amt = "Rs. " . $amount;
            $cur_tentative_amt = "Rs. " . $tentative_amount . "/-";
            $message = "Dear " . $agent_info['name'] . ", " . $customer_info['name'] . $customer_info['business_name'] . " has placed an order of amount " . $cur_amt . ", Your tentative commission would be " . $cur_tentative_amt . " for this order.";
            $send_sms = new SMS($message, $agent_info['mobile']);
            $send_sms->sendMessage();
        } elseif ($type == 'remove') {
            $sql = "DELETE FROM agent_commission WHERE order_id = " . $order_id;
            $this->db_crm->query($sql);
        }
        return "done";
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
    
    /**
     * Kusum Joshi
     * @param type $customer_id
     * @param type $order_id
     * @param type $sales_staff_id
     * 
     */
    public function checkOrderforSelf($customer_id, $order_id, $sales_staff_id,$order_date) {
        
        if(!empty($order_date)){
            $order_time = date('H:i a',strtotime($order_date));

            if(date("l",strtotime($order_date)) == 'Sunday'){
                // update self_order flag
               $update_result = $this->updateOrdertoSelf($order_id, $customer_id);
               return $update_result;
                
            }else{
                $sql = "SELECT staff_id, telephone, role
                    FROM " . DB_PREFIX . "sales_staff 
                        WHERE staff_id = " . (int) $sales_staff_id ;
                $query = $this->db->query($sql);
                if ($query->num_rows) {
                    $sales_staff = $query->row;
                    if($sales_staff['role'] == 'field' && !($order_time > date('H:i a',strtotime(FIELD_OPENING_TIME)) && $order_time < date('H:i a',strtotime(FIELD_CLOSING_TIME)))  ){
                         $update_result = $this->updateOrdertoSelf($order_id, $customer_id);
                         return $update_result;
                    }
                    else if($sales_staff['role'] == 'tele' && !($order_time > date('H:i a',strtotime(TELE_OPENING_TIME)) && $order_time < date('H:i a',strtotime(TELE_CLOSING_TIME))) ){
                         $update_result = $this->updateOrdertoSelf($order_id, $customer_id);
                         return $update_result;
                
                    }else if($sales_staff['role'] == 'store' && !($order_time > date('H:i a',strtotime(STORE_OPENING_TIME)) && $order_time < date('H:i a',strtotime(STORE_CLOSING_TIME))) ){
                         $update_result = $this->updateOrdertoSelf($order_id, $customer_id);
                         return $update_result;
                    }else{
                        return FALSE;
                    }
                }
            }
        }
        return FALSE;
    }
    
    /**
     * Kusum Joshi
     * @param type $order_id
     * @param type $customer_id
     * @return boolean
     * 
     */
    public function updateOrdertoSelf($order_id,$customer_id){
        
        if(!empty($order_id) && !empty($customer_id)){
                // update self_order flag
                $upd_order = "UPDATE " . DB_PREFIX . "order SET self_order = 1,self_order_reason = 'Non working hours' WHERE order_id=" . $order_id;
                
                $query = $this->db->query($upd_order);
                
                $upd_cus = "UPDATE " . DB_PREFIX . "customer SET self_order = 1 WHERE customer_id=" . $customer_id;
                
                $query = $this->db->query($upd_cus);
                
                $lead_id = $this->getLeadIdfromCustomerId($customer_id);
                
                if($lead_id != 0){
                    $this->updateLeadtoSelfOrder($lead_id);
                }
                
                return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * Kusum Joshi
     * @param type $customer_id
     * @return type
     * getLead Id from leads_website_customer_ids table
     */
    public function getLeadIdfromCustomerId($customer_id){
         $sql = "SELECT lead_id
                FROM leads_website_customer_ids
                WHERE customer_id = " . $customer_id;
        $query = $this->db_crm->query($sql);
        
        return $query->num_rows ?$query->row['lead_id'] : 0;
    }
    
    /**
     * Kusum Joshi
     * @param type $lead_id
     * @return boolean
     * update lead to selfOrder
     */
    public function updateLeadtoSelfOrder($lead_id){
        
        if(!empty($lead_id)){
        $upd_lead = "UPDATE leads SET self_order = 1 WHERE id=" . $lead_id;
                
        $query = $this->db_crm->query($upd_lead);
        }
        return TRUE;
    }
    
    
}