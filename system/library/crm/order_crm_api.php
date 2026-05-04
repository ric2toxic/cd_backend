<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class OrderCrmApi {

    public $order_id = '';
    public $order_no = '';
    public $sales_staff_id = array();
    public $operation_staff = array();
    public $customer_id = '';
    public $type = '';
    public $url = '';
    public $db_crm;
    public $sales_staff_ids_previous = array();

    function __construct($param) {
        $this->order_id = $param['order_id'];
        $this->order_no = $param['order_no'];
        $this->sales_staff_id = !empty($param['sales_staff']) ? $param['sales_staff'] : array();
        $this->sales_staff_ids_previous = !empty($param['sales_staff_ids_previous']) ? $param['sales_staff_ids_previous'] : array();
        $this->type = $param['type'];
        $this->operation_staff = isset($param['operation_staff']) ? $param['operation_staff'] : array();
        $this->customer_id = isset($param['customer_id']) ? $param['customer_id'] : '';
        $this->url = (isset($param['crm_url'])) ? $param['crm_url'] : self::getCrmUrl();
        $this->db_crm = '';
    }

    /**
     * updateLeadData
     * @author Vishnu Shekhawat
     * @description Push to rabbit mq Or Update user tentative Incentive
     * @param boolean $send_to_queue
     * @return true
     */
    public function updateLeadData($send_to_queue = false) {

        // Sending to RabbitMQ Queue if send_to_queue is set to True and SMS_QUEUE is set to 1 in config.php
        if ($send_to_queue) {

            try {
                $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                $channel = $connection->channel();

                // third parameter is for queue durability. we set it to true
                // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
                // passive - false ; exclusive - false; auto-delete - false
                $channel->queue_declare('update_user_tentative_incentive', false, true, false, false);
                // echo "<pre>";print_r($this); die;
                $data = base64_encode(serialize($this)); // Sending the current SMS object as message for reconstruction
                // delivery_mode = 2 makes messag   e persistent (durable)
                $msg = new AMQPMessage($data, array('delivery_mode' => 2));
                $channel->basic_publish($msg, '', 'update_user_tentative_incentive'); // send to sms_queue

                $channel->close();
                $connection->close();

                return true;
            } catch (\Exception $e) {

                // Now we ensure that the actual SMS is still sent
                $this->UpdateTentativeIncentiveInCrm();
                return true;
            }
        } else {
            $this->UpdateTentativeIncentiveInCrm();
            return true;
        }

        return true;
    }

    /**
     * loadDb
     */
    public function loadDb() {

        $this->db_crm = '';
    }

    /**
     * UpdateTentativeIncentiveInCrm
     * @author Vishnu Shekhawat
     * @description Update tentative incentive in crm 
     * @param array $param
     * @return true
     */
    public function UpdateTentativeIncentiveInCrm() {

        $order_id = $this->order_id;
        $order_no = $this->order_no;
        $sales_staff_id = $this->sales_staff_id;
        $type = $this->type;
        $operation_staff = $this->operation_staff;
        $customer_id = $this->customer_id;
        $sales_staff_ids_previous = $this->sales_staff_ids_previous;
        // create a new cURL resource
        $ch = curl_init();

        // Get Url
        $crm_site_url = $this->url . 'Cron/userTentativeIncentive';

        $operation_staff = $data = array('order_id' => $order_id, 'order_no' => $order_no, 'sales_staff_id' => $sales_staff_id, 'type' => $type, 'operation_staff' => $operation_staff, 'customer_id'=>$customer_id, 'sales_staff_ids_previous' => $sales_staff_ids_previous);
        //set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $crm_site_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // grab URL and pass it to the browser
        $result = curl_exec($ch); 

//        echo "<pre>"; print_r($result); exit;
     
        // close cURL resource, and free up system resources
        curl_close($ch);
        return TRUE;
    }

    /**
     * getCrmUrl
     * @author Vishnu Shekhawat
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

    /**
     * getWhomOrderTagged
     * @author 
     * @description  return users id and their roles 
     * @return array
     */
    public static function getWhomOrderTagged($db, $order_id) {

        $q = $db->query("SELECT ss.crm_user_id, ss.role 
              FROM " . DB_PREFIX . "sales_staff ss 
              RIGHT JOIN " . DB_PREFIX . "order_sales_staff oss
              ON ss.staff_id = oss.sales_staff_id
              WHERE oss.order_id = " . $order_id . "
                AND ss.role = 'field'");
        if ($q->num_rows) {
            return $q->rows;
        } else {
            return array();
        }
    }

}
