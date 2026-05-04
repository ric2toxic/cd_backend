<?php
require_once  __DIR__ . '/../vendor/autoload.php';
require_once  __DIR__ . '/../system/library/phpmailer.php';
require_once  __DIR__ . '/../config.php';
require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . 'library/commonlib.php');
require_once 'tasks.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$db = new Database\DB( DB_SERVERS );

$sql =  "SELECT `key`, `value` FROM " . DB_PREFIX . "setting 
         WHERE `store_id` = 0 
           AND `code` LIKE 'config' 
           AND `key` IN ('config_mail_smtp_hostname', 
                         'config_mail_smtp_port', 
                         'config_mail_smtp_username', 
                         'config_mail_smtp_password'
                        )";
$query = $db->query($sql);

$config_array = array();

foreach ($query->rows as $key => $value) {
    $config_array[$value['key']] = $value['value'];
}

try{

	$connection = new AMQPStreamConnection('localhost','5672','guest','guest');
	$channel = $connection->channel();

	// Max number of consumers to be set on this general_queue is 3
	// Helps in optimizing Memory requirement - which is an issue in PHP anyways
	define('MAX_CONSUMERS', 3);

	// Also, ensuring that it each consumer does not run for more than 15 minutes
	define('MAX_RUN_TIME', 900); // Seconds (15 minutes)

	// Note the start time
	define('START_TIME', time());

	$queue_name = 'STAGING_GENERAL_TASKS_QUEUE';
	            
	if (SITE_ENVIRONMENT == 'Production') {
	   $queue_name = 'GENERAL_TASKS_QUEUE';
	}

	 // third parameter is for queue durability. we set it to true
	// so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
	// passive - false ; exclusive - false; auto-delete - false
	list(,,$consumer_count) = $channel->queue_declare($queue_name, false, true, false, false);
		
	// exit if already max_consumers listening onto this queue
	if ($consumer_count >= MAX_CONSUMERS)
		exit();
	
	$callback = function($msg){
		
		global $db;

		$messages = unserialize(base64_decode($msg->body));
		$func_name = $messages['constant_value']['function_name'];
		$param_data = !empty($messages['data_array']) ? $messages['data_array'] : '' ;

		$result = Tasks::$func_name($db, $param_data);
        
        // Acknowledge the successful delivery
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        
        if (!$result) {

            $connection_republish = new AMQPStreamConnection('localhost','5672','guest','guest');
			$channel_republish = $connection_republish->channel();
			$queue_name_republish = 'STAGING_GENERAL_TASKS_QUEUE';
			if (SITE_ENVIRONMENT == 'Production') {
	   			$queue_name_republish = 'GENERAL_TASKS_QUEUE';
			}
			$channel_republish->queue_declare($queue_name_republish, false, true, false, false);

        	// delivery_mode = 2 makes message persistent (durable)
	        $msg_republish = new AMQPMessage($msg->body, array('delivery_mode' => 2));
	        $channel_republish->basic_publish($msg_republish, '', $queue_name_republish);

	        $channel_republish->close(); //Closes Channel
        	$connection_republish->close();// Closes Connection
        } 
		
        // Check if time limit has crossed
        if ( START_TIME + MAX_RUN_TIME < time() ) {
            $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
        }

	};
    
    $channel->basic_qos(null, 1, null); // 1 in second argument ensures fair dispatch
    // Fair dispatch means a worker gets only one task at a time 
    // and is not loaded with next task, untill it has finished the previous task
		
	// fourth parameter false means acknowledgement is set to true
	// dont forget to set basic_ack otherwise message will remain stuck in the queue and keep on getting 
	// delivered to other workers
	$channel->basic_consume($queue_name, '', false, false, false, false, $callback);

	while(count($channel->callbacks)){
		$channel->wait();
    }			

} catch(Exception $e){

	$mail = new PHPMailer();
    $mail->isSMTP();
    //$mail->SMTPDebug = 3;
    //$mail->Debugoutput = 'echo';
    $mail->Host = $config_array['config_mail_smtp_hostname'];
    $mail->Port = $config_array['config_mail_smtp_port'];
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Username = $config_array['config_mail_smtp_username'];
    $mail->Password = $config_array['config_mail_smtp_password'];
    $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
    $mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
    $mail->Subject = 'General Queue Consumer Error - ' . date('d/m/y H:i:s');
    $mail->Body = $e->getMessage();
    
    $mail->send(1,false);
}
