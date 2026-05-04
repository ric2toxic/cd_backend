<?php

// prevent external access
/*
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // If a "remote" address is set, we know that this is not a CLI call
    header('HTTP/1.1 403 Forbidden');
    die('Access denied. Go away, shoo!');
}
*/
require_once __DIR__ . '/../../config.php';
require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . '../vendor/autoload.php');
require_once(DIR_SYSTEM . 'cron/syncproducttosolr.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$queue_name = 'STAGING_DAILY_ONCE_PUSH_SYNC_SOLR_QUEUE';
if (SITE_ENVIRONMENT == 'Production') {
    $queue_name = 'DAILY_ONCE_PUSH_SYNC_SOLR_QUEUE';
}



$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Max number of consumers to be set on this email_queue is 2
// Helps in optimizing Memory requirement - which is an issue in PHP anyways
define('MAX_CONSUMERS', 2);

// Also, ensuring that it each consumer does not run for more than 15 minutes
define('MAX_RUN_TIME', 900); // Seconds (15 minutes)

// Note the start time
define('START_TIME', time());

// third parameter is for queue durability. we set it to true
// so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
// passive - false ; exclusive - false; auto-delete - false
list(,,$consumer_count) = $channel->queue_declare($daily_once_queue, false, true, false, false);

// exit if already max_consumers listening onto this queue
if ($consumer_count >= MAX_CONSUMERS)
	exit();

$callback = function($msg){
	$change_product = unserialize(base64_decode($msg->body));
	$success = false;
	while ($success == false) {
		try {
			$success = $change_product->dailyOnceUpdateToSolr(false); // false means dont send to queue
		} catch (\Exception $e){
			error_log('\n' . $e->getMessage() . '\n Serialized Email Object: ' . base64_decode($msg->body), 
					3, 
					__DIR__ . '/change_daily_once_queue_errors.log');
			
            $db = new Database\DB( DB_SERVERS );
			$sql = "SELECT `key`, `value` FROM " . DB_PREFIX . "setting 
                    WHERE `store_id` = 0 
                      AND `code` LIKE 'config' 
                      AND `key` IN ('config_mail_smtp_hostname', 
                                    'config_mail_smtp_port', 
                                    'config_mail_smtp_username', 
                                    'config_mail_smtp_password'
                                   )";
			$result = $db->query($sql)->rows;
			if ($result->num_rows) {
                $mail_parameters = array_combine(array_column($result, 'key'),
                                                 array_column($result, 'value')
                                                );
				$mail_obj = new PHPMailer();
				$mail_obj->isSMTP();
				$mail_obj->Host 		= $mail_parameters['config_mail_smtp_hostname'];
				$mail_obj->Port 		= $mail_parameters['config_mail_smtp_port'];
				$mail_obj->SMTPSecure 	= 'ssl';
				$mail_obj->SMTPAuth 	= true;
				$mail_obj->Username 	= $mail_parameters['config_mail_smtp_username'];
				$mail_obj->Password 	= $mail_parameters['config_mail_smtp_password'];
				$mail_obj->Subject 		= 'Error in Sync Product Solr Worker Queue '.Date("d/m/Y");
				$mail_obj->Body 		= '\n' . $e->getMessage() . '\n Serialized Daily Once Sync Solr Object: ' . base64_decode($msg->body);
				$mail_obj->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
                $mail_obj->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
			}
		}
	}
	
	// Acknowledge the successful delivery
	$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

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
$channel->basic_consume($daily_once_queue, '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}
