<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Notification
{
    private $msg;
    private $registrationIds;
    private $API_ACCESS_KEY = 'AIzaSyCqnxMbpr2Y-51AH5bdIO7sR7v-oJQF4iE';
    private $SELLER_API_ACCESS_KEY = 'AAAAhZiSl8A:APA91bGLi7EQO3VVh74vj47Il2iWmRTmGgHxLF_ZOaorky1PKGZvnCK86MhGCnJ-tiWdrfAEK1Y-B0kb82VDAEO5OnMt37qPcmV_vQf_zQiMx6I3OyroZDHh2f66LB3ehKKOj4g6agJq';


    function __construct($registrationIds = '', $message = ''){
        $this->msg = $message;
        $this->registrationIds = $registrationIds;

    }

    /**
     * Get Customers
     * function sendPushNotification
     * @date 16-01-2016
     * @return true
     * */
    public function sendPushNotification($send_to_queue=true){

        // Sending to RabbitMQ Queue if send_to_queue is set to True and SMS_QUEUE is set to 1 in config.php
        if ($send_to_queue && PUSH_NOTIFICATION_QUEUE) {

            try {
                $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
                $channel = $connection->channel();

                // third parameter is for queue durability. we set it to true
                // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
                // passive - false ; exclusive - false; auto-delete - false
                $channel->queue_declare('push_notification_queue', false, true, false, false);

                $data = base64_encode(serialize($this)); // Sending the current SMS object as message for reconstruction

                // delivery_mode = 2 makes message persistent (durable)
                $msg = new AMQPMessage($data, array('delivery_mode' => 2));
                $channel->basic_publish($msg, '', 'push_notification_queue'); // send to sms_queue

                $channel->close();
                $connection->close();

                return true;

            } catch (\Exception $e) {
                // Now we ensure that the actual SMS is still sent
                goto SEND_PUSH_NOTIFICATION;
            }
        } else {
            goto SEND_PUSH_NOTIFICATION;
        }


        SEND_PUSH_NOTIFICATION:
        // API access key from Google API's Console
        //define( 'API_ACCESS_KEY', 'YOUR-API-ACCESS-KEY-GOES-HERE' );
        //define( 'API_ACCESS_KEY', 'define( 'API_ACCESS_KEY', 'YOUR-API-ACCESS-KEY-GOES-HERE' );' );

        $fields = array
        (
            'registration_ids' 	=> $this->registrationIds,
            'data'			=> $this->msg
        );

        $headers = array
        (
            'Authorization: key=' . $this->API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        if(curl_error($ch)) {
            echo 'error: '. curl_error($ch);
        }
        curl_close( $ch );
        return $result; 
    }

    /**
     * send push notification to seller
     * function sendPushNotificationToSellers
     * @date March 2018
     * @return true
     * */
    public function sendPushNotificationToSellers(){
        $fields = array
        (
            'registration_ids' 	=> $this->registrationIds,
            'data'			=> $this->msg,
            'notification'  => $this->msg['custom_notification'],
            'priority'      => 'high',
        );

        $headers = array
        (
            'Authorization: key=' . $this->SELLER_API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        if(curl_error($ch)) {
            echo 'error: '. curl_error($ch);
        }
        curl_close( $ch );
        return $result;
    }
}
