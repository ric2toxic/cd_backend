<?php
include_once ( DIR_SYSTEM . '../rabbitmq/task_directive_constants.php' );

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ModelDialerDialer extends Model {

    public function sendDataToDesktopDailer(string $mobile, string $list_id)
    {
        //Create Connection with rabitMQ Server
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $queue_name = 'STAGING_GENERAL_TASKS_QUEUE';
            
        if (SITE_ENVIRONMENT == 'Production') {
           $queue_name = 'GENERAL_TASKS_QUEUE';
        }

        $dialer_data = array(
                                'mobile' => $mobile, 
                                'list_id' => $list_id
                            );

        // third parameter is for queue durability. we set it to true
        // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
        // passive - false ; exclusive - false; auto-delete - false
        $channel->queue_declare($queue_name, false, true, false, false);

        $data = array(
                        'constant_value' => unserialize(ADD_LEAD_IN_DESKTOP_DIALER),
                        'data_array' => $dialer_data
                    );
        $queue_object = base64_encode(serialize($data));

        // delivery_mode = 2 makes message persistent (durable)
        $msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
        $channel->basic_publish($msg, '', $queue_name);

        $channel->close(); //Closes Channel
        $connection->close();// Closes Connection
    }    
}
