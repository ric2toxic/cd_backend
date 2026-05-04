<?php
require_once __DIR__.'/../../config.php';
require_once DIR_SYSTEM.'library/db/db.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
if ( SOLR_ORDER_SYNC_QUEUE ) {
    $db = new Database\DB( DB_SERVERS );
    $sql  = "SELECT change_id, ";
    $sql .=         "order_id, ";
    $sql .=         "table_name, ";
    $sql .=         "change_details ";
    $sql .=     "FROM ".DB_PREFIX."order_change_log ";
    $sql .=     "WHERE solr_synced = 0 ORDER BY order_id ASC, table_name ASC, change_id desc";
    $results = $db->query($sql);
    if($results->num_rows > 0){
       $changes = array();
       $change_id = array_column( $results->rows, 'change_id' );
       $sql  = "UPDATE ".DB_PREFIX."order_change_log ";
       $sql .=          "SET solr_synced = 1 ";
       $sql .=  "WHERE change_id IN (".implode(",",$change_id).")";
       $db->query($sql);
    }
    $changes = array();
    $order_product_id_to_update = array();

    foreach ( $results->rows as $key => $value) {
        if(!empty($value['change_details'])){
            $changes_details = json_decode($value['change_details'],true);
            foreach ( $changes_details as $key => $change) {
                if(empty($changes[$value['order_id']]['fields'][$key]) && $key != 'order_id' && $key != 'products' ){
                    $changes[$value['order_id']]['fields'][$key] = $change['new'];
                    $changes[$value['order_id']]['change_id'][] = $value['change_id'];
                    $changes[$value['order_id']]['order_id'] = $value['order_id'];

                }
                else if($key == 'products'){
                    $order_product_id_to_update = array_merge($order_product_id_to_update,explode( "," , $change['new']));
                }
            }
        }

    }
    $order_product_details = array();
    if(!empty($order_product_id_to_update)){
        $sql  = "SELECT order_id,";
        $sql .=        "GROUP_CONCAT( DISTINCT(CONCAT( oop.order_product_id,'-', oop.product_id,'-', oop.seller_id)) SEPARATOR ',' ) as seller_to_products,";
        $sql .=        "GROUP_CONCAT( DISTINCT( oop.product_id) ) as sellers, ";
        $sql .=        "GROUP_CONCAT( DISTINCT( oms.nickname) ) as seller_nicknames ";
        $sql .= "FROM ".DB_PREFIX."order_product oop ";
        $sql .= "INNER JOIN ".DB_PREFIX."ms_seller oms ON (oms.seller_id = oop.seller_id) ";
        $sql .= "WHERE order_product_id IN ( ".implode(",",$order_product_id_to_update)." )";
        $sql .= "GROUP BY order_id";
        $order_product_details = $db->query($sql);
        if($order_product_details->num_rows > 0){
            foreach ($order_product_details->rows as $key => $row) {
                $changes[$row['order_id']]['fields'] = array_merge($changes[$row['order_id']]['fields'],$row);
                unset($changes[$row['order_id']]['fields']['order_id']);
            }
        }

    }


    $method = '';

    try {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        // third parameter is for queue durability. we set it to true
        // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
        // passive - false ; exclusive - false; auto-delete - false
        $channel->queue_declare('order_solr_sync_queue', false, true, false, false);

        $method = 'queue';

    }
    catch (\Exception $e) {

        $registry = new stdClass();
        $registry->solr_config = array(
                            'endpoint' => array(
                                'localhost' => array(
                                        'host' => 'localhost',
                                        'port' => 8983,
                                        'path' => '/solr/orders/',
                                        )
                            )
                        );
        $solr_order = new SolrOrder($registry);
        $method = 'method';

    }

    foreach( $changes as $order_id => $change ){
        if( $method == 'queue'){
            $data = base64_encode(serialize($change));
            $msg = new AMQPMessage($data, array('delivery_mode' => 2));
            $channel->basic_publish( $msg, '', 'order_solr_sync_queue' ); // send to email_queue
        }
        else if($method == 'method'){
            if(!empty($change['change_id'])){

                $data['order_id'] = $changes['order_id'];
                $data['fields'] = $changes['fields'];
                $change_id = $changes['change_id'];

                if( $solr_order->atomicUpdateToSolr($data) ){
                    $sql  = "UPDATE ".DB_PREFIX."order_change_log ";
                    $sql .=       "SET solr_synced_date = now() ";
                    $sql .= "WHERE change_id IN (".implode(",",$change_id).")";
                    $db->query($sql);
                }
            }

        }
    }

    if($method == 'queue'){
        $channel->close();
        $connection->close();
        echo "connection closed";
        return true;
    }
    else{
        unset($solr_order);
    }

}
?>
