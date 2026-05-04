<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../../config.php';
require_once(DIR_SYSTEM . '../vendor/autoload.php');
require_once(DIR_SYSTEM . 'library/solr/product.php');

class SyncProductToSolr {
    private $product_id;
    private $table_name;
    private $change_details;
    private $_queue_name;
    private $_daily_once_queue;

    function __construct($product_id, $table_name, $change_details){
        $this->product_id       = $product_id;
        $this->table_name       = $table_name;
        $this->change_details   = $change_details;

        $this->_queue_name = 'STAGING_PUSH_SYNC_PRODUCT_SOLR_QUEUE';
        $this->_daily_once_queue = 'STAGING_DAILY_ONCE_PUSH_SYNC_SOLR_QUEUE';

        if (SITE_ENVIRONMENT == 'Production') {
            $this->_queue_name = 'PUSH_SYNC_PRODUCT_SOLR_QUEUE';
            $this->_daily_once_queue = 'DAILY_ONCE_PUSH_SYNC_SOLR_QUEUE';
        }

    }

    public function updateProductToSolr($send_to_queue=true){
        $data = array();

        // Sending to RabbitMQ Queue if send_to_queue is set to True and SYNC_PRODUCT_SOLR is set to 1 in config.php
        if ($send_to_queue && PUSH_SYNC_PRODUCT_SOLR_QUEUE == 1) {

            try {
                $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                $channel = $connection->channel();

                // third parameter is for queue durability. we set it to true
                // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
                // passive - false ; exclusive - false; auto-delete - false
                $channel->queue_declare($this->_queue_name, false, true, false, false);
                //echo "<pre>"; print_r($this); echo "</pre>";
                $data = base64_encode(serialize($this)); // Sending the current SMS object as message for reconstruction
                // delivery_mode = 2 makes message persistent (durable)
                $msg = new AMQPMessage($data, array('delivery_mode' => 2));
                
                $channel->basic_publish($msg, '', $this->_queue_name); // send to SYNC_PRODUCT_SOLR
                $channel->close();
                $connection->close();

                return true;

            } catch (\Exception $e) {
                // Now we ensure that the actual SMS is still sent
                goto SYNC_PRODUCT_SOLR;
            }
        } else {
            goto SYNC_PRODUCT_SOLR;
        }


        SYNC_PRODUCT_SOLR:
            $product_id         = $this->product_id;
            $table_name	        = $this->table_name;
            $change_details     = $this->change_details;

            $details = json_decode($change_details);

            if($table_name == 'oc_ms_seller') {
                foreach($details as $key => $value){
                    if( $key == 'nickname' || $key == 'city' || $key == 'vacation_mode' || $key == 'seller_status' || $key == 'app_only' || $key == 'non_serviceable_areas' ){
                        $data['seller_id'] = $details->seller_id;
                        if( $key == 'non_serviceable_areas' ){
                            $data['value'] = explode(',', $value->new);
                        }
                        else {
                            $data['value'] = $value->new;
                        }
                        if( SOLR_ENABLED && SOLR_WSBOX_ENABLED ){
                            SolrProduct::atomicBulkUpdateToSolr($data, $key);
                        }
                    }

                }
            } elseif($table_name == 'oc_review_rules') {
                $data['seller_id'] = $details->seller_id;
                SolrProduct::atomicBulkUpdateToSolr($data, 'seller_rating');
            } else { 
                $multi_valued_field = array("oc_product_to_category", "oc_product_filter", "oc_product_to_store");
                if(in_array($table_name,$multi_valued_field)){
                    foreach($details as $key => $value) {
                        if($key == 'product_id'){
                            $data[$key] = $value;
                        }elseif($key == 'category_id' || $key == 'filter_id' || $key == 'store_id'){
                            //if(!empty($value->new)) {
                                $data['fields'][$key] = explode(',', $value->new);
                                if($key == 'filter_id'){
                                    //Dynamic filter_group_df
                                    $filter_by_group = array();
                                    if (count($data['fields'][$key]) > 0) {
                                        //dynamic filed with filter group id
                                        foreach($data['fields'][$key] as $filter) {
                                            if($filter > 0) {

                                                $filter_group_id = SolrProduct::getFilterGroup($filter);
                                                $filter_by_group[$filter_group_id][] = $filter;
                                            }
                                        }
                                        if (count($filter_by_group) > 0) {
                                            foreach ($filter_by_group as $group_id => $arr_filter_id) {
                                                $dynamic_filed_name = 'filter_group_df_'.$group_id;
                                                $data['fields'][$dynamic_filed_name] = $arr_filter_id;
                                            }
                                        }
                                    }
                                }
                            //}
                        }else{
                            $data['fields'][$key] = $value->new;
                        }
                    }
                }else{
                    foreach($details as $key => $value) {
                        if($key != 'product_id'){
                            $data['fields'][$key] = $value->new;
                        } else {
                            $data[$key] = $value;
                        }
                    }
                }
                if( SOLR_ENABLED && SOLR_WSBOX_ENABLED ){
                   SolrProduct::atomicUpdateToSolr($data);
                }
            }
            
            return true;
    }

    public function dailyOnceUpdateToSolr($send_to_queue=true){

        // Sending to RabbitMQ Queue if send_to_queue is set to True and SYNC_PRODUCT_SOLR is set to 1 in config.php
        if ($send_to_queue && DAILY_ONCE_PUSH_SYNC_SOLR_QUEUE == 1) {

            try {
                $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                $channel = $connection->channel();

                // third parameter is for queue durability. we set it to true
                // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
                // passive - false ; exclusive - false; auto-delete - false
                $channel->queue_declare($this->_daily_once_queue, false, true, false, false);
                //echo "<pre>"; print_r($this); echo "</pre>";
                $data = base64_encode(serialize($this)); // Sending the current SMS object as message for reconstruction
                // delivery_mode = 2 makes message persistent (durable)
                $msg = new AMQPMessage($data, array('delivery_mode' => 2));

                $channel->basic_publish($msg, '', $this->_daily_once_queue); // send to SYNC_PRODUCT_SOLR
                $channel->close();
                $connection->close();

                return true;

            } catch (\Exception $e) {
                // Now we ensure that the actual SMS is still sent
                goto DAILY_ONCE_SYNC_SOLR;
            }
        } else {
            goto DAILY_ONCE_SYNC_SOLR;
        }


        DAILY_ONCE_SYNC_SOLR:
        $product_id         = $this->product_id;
        $table_name	        = $this->table_name;
        $change_details     = $this->change_details;

        $details = json_decode($change_details);
       // echo "<pre>"; print_r($details); die;

        foreach($details as $key => $value) {
            if($key != 'product_id'){
                $data['fields'][$key] = $value->new;
            } else {
                $data[$key] = $value;
            }
        }

        if( SOLR_ENABLED && SOLR_WSBOX_ENABLED ){
            SolrProduct::atomicUpdateToSolr($data);
        }

        return true;
    }
}
