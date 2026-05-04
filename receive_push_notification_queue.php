<?php

// prevent external access
/*
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // If a "remote" address is set, we know that this is not a CLI call
    header('HTTP/1.1 403 Forbidden');
    die('Access denied. Go away, shoo!');
}
*/
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/system/library/notification.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
list(,,$consumer_count) = $channel->queue_declare('push_notification_queue', false, true, false, false);

// exit if already max_consumers listening onto this queue
if ($consumer_count >= MAX_CONSUMERS)
	exit();

$callback = function($msg){
	$notification = unserialize(base64_decode($msg->body));
	
	$success = false;
	while ($success == false) {
		$success = $notification->sendPushNotification(false); // false means dont send to queue
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
$channel->basic_consume('push_notification_queue', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}
