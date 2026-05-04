<?php

require_once __DIR__ . '/db/db.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Updateorder {

    private $data;
    private $db_crm;

    function __construct($data) {
        $this->data = $data;
        $this->url = self::getCrmUrl();
    }

    public function loadDb() {
        $this->db_crm = new Database\DB( DBCRM_SERVERS );
        $this->db = new Database\DB( DB_SERVERS );
    }

    /**
     * Kusum Joshi
     * @param type $send_to_queue
     * @return boolean
     * updateOrderData
     */
    public function updateOrderData($send_to_queue = true) {

        // Sending to RabbitMQ Queue if send_to_queue is set to True and SMS_QUEUE is set to 1 in config.php
        if ($send_to_queue) {

            try {
                $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                $channel = $connection->channel();

                // third parameter is for queue durability. we set it to true
                // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
                // passive - false ; exclusive - false; auto-delete - false
                $channel->queue_declare('order_updation_queue', false, true, false, false);

                $data = base64_encode(serialize($this)); // Sending the current SMS object as message for reconstruction
                // delivery_mode = 2 makes messag   e persistent (durable)
                $msg = new AMQPMessage($data, array('delivery_mode' => 2));
                $channel->basic_publish($msg, '', 'order_updation_queue'); // send to sms_queue

                $channel->close();
                $connection->close();

                return true;
            } catch (\Exception $e) {
                // Now we ensure that the actual SMS is still sent
                goto UPDATE_ORDER;
            }
        } else {
            goto UPDATE_ORDER;
        }


        UPDATE_ORDER:

        $data = $this->data;
        $user_id = '';
        if(isset($data['customer_id']) && !empty($data['customer_id'])){
        $user_id = $this->getLeadUserId($data['customer_id']);
        }
        if(($data['action'] == 'AUTO_TAG') && !empty($user_id)){
            //get last 3 orders and related sales staff members
            $orders = $this->getCustomerOrders($data['order_id'],$data['customer_id']);
            if(!empty($orders)){
                $sales_staff_info = $this->getOrderSalesStaffInfo($orders);
                if(!empty($sales_staff_info) && isset($sales_staff_info[$user_id])){
                    // Tag the order to this sales staff
                    $sales_staff_id = $sales_staff_info[$user_id];
                    $order_tag_data = [
                    'order_id' => $data['order_id'],
                    'order_no' => $data['order_no'],
                    'sales_staff_id' => $sales_staff_id,
                    'customer_id'=> $data['customer_id'],
                    'amount' => '',
                    'type' => 'add',
                    'tag_type' => 'Automatic',
                    'user_id' => 0
                    ];
                    
                    $order_tag = new ordertag($order_tag_data);
                    $result = $order_tag->updateSalesStaff();
                    
                    
            
                     if ((is_array($result) && $result['force_tag'] == 1)){ 
                         return TRUE;
                     }else if($result == "done") {
                         $sales_staff[] = $sales_staff_id;
                        /** Update sales_staff with merge sales_staff * */
                        $order_crm_update['order_id'] = $data['order_id'];
                        $order_crm_update['order_no'] = $data['order_no'];
                        $order_crm_update['type'] = 'add';
                        $order_crm_update['customer_id'] = $data['customer_id'];
                        $order_crm_update['sales_staff'] = $sales_staff;
                        $order_crm_update['sales_staff_ids_previous'] = array();
                        $order_crm_update['operation_staff'] = array('username' => 'System','name' => 'System');
                        $order_crm_update['crm_url'] = $this->url;
                        $order_crm_leads = new OrderCrmApi($order_crm_update);
                        $order_crm_leads->updateLeadData();
                        
                        return TRUE;
                     }else{
                         return FALSE;
                     }
                }else{
                    return TRUE;
                }
            }else{
                return TRUE;
            }
                        
        }
        return TRUE;
    }
    
    /**
     * Kusum Joshi
     * @param type $order_id
     * @param type $customer_id
     * @return type
     * get last 3 orders of customer
     */
    public function getCustomerOrders($order_id,$customer_id){
        
        //select order_id,date_added from oc_order where customer_id = 16 ORDER BY date_added DESC LIMIT 1,3
        $orders = array();
        $sql = "SELECT order_id
                                FROM " . DB_PREFIX . "order o
                                WHERE o.customer_id = " .  (int) $customer_id . " AND o.order_id <" .  (int) $order_id 
                ." ORDER BY o.date_added DESC LIMIT 3 ";
                        $query = $this->db->query($sql);
                        if ($query->num_rows && $query->num_rows == 3) {
                            foreach ($query->rows as $result) {
                                $orders[] = $result['order_id'];
                            }
                        }
         return $orders;
    }
    /**
     * Kusum Joshi
     * @param type $orders
     * @return type
     * get sales staff 
     */
    public function getOrderSalesStaffInfo($orders){
        $sales_staff_info = array();
        if(!empty($orders)){
         $sql = "SELECT ooss.sales_staff_id, COUNT(DISTINCT(ooss.order_id)) as order_count , oss.telephone, oss.crm_user_id
                                FROM " . DB_PREFIX . "order_sales_staff ooss
                                INNER JOIN " . DB_PREFIX . "sales_staff oss ON (ooss.sales_staff_id = oss.staff_id)
                                WHERE order_id IN (" .implode(',',$orders) . ") GROUP BY sales_staff_id";
                        $query = $this->db->query($sql);
                        if ($query->num_rows) {
                            foreach ($query->rows as $result) {
                                if($result['order_count'] == 3){
                                $sales_staff_info[$result['crm_user_id']] = $result['sales_staff_id'];
                                }
                            }
                        }
         
        }
        return $sales_staff_info;
    }
    
    /**
     * Kusum Joshi
     * @param type $customer_id
     * @return type
     * get leads user_id
     */
    public function getLeadUserId($customer_id){
         $sql = "SELECT l.user_id
                FROM leads_website_customer_ids lwc INNER JOIN leads l ON (lwc.lead_id = l.id)
                INNER JOIN users u ON (l.user_id = u.id)
                WHERE lwc.customer_id = " . $customer_id ." AND u.status = 1";
        $query = $this->db_crm->query($sql);
        
        return $query->num_rows ?$query->row['user_id'] : 0;
    }
    
    /**
     * getCrmUrl
     * @author kusum Joshi
     * @description return crm url 
     * @return string
     */
    public static function getCrmUrl() {

        $url = HTTP_SERVER;
        $crm_site_url = 'https://www.wholesalebox.biz/';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] == '127.0.0.1' || $_SERVER['HTTP_X_FORWARDED_FOR'] == '::1')) {
            $crm_site_url = "http://localhost/wsbox-crm/";

        } elseif (strpos($url, 'staging') !== false) {
            $crm_site_url = "https://www.wholesalebox.biz/staging/";
        } elseif (strpos($url, 'wsb.in') !== false || strpos($url, 'localhost') !== false) {
            $crm_site_url = "http://localhost/wsbox-crm/";
        }
        return $crm_site_url;
    }

}