<?php

require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . 'library/notification.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/*
* This the sms class for sending messages at various event in the workflow
* @param [type] $[name] [<description>]
* @author Parth Gupta <[<email address>]>
* @return [type] [<description>]
*/
class SMS{
	
	private $msg;
	private $num;
	 
	function __construct($message = '', $number = ''){
		// $this->msg = $message;
		$this->msg = str_replace('&#x20b9;', 'Rs.', $message);
		$this->num = $number;
	}
    
      //Using setter and getter
      /**
       * Setting the message
       * @author Parth Gupta
       * @param string $msg
       */
      public function setMessage($msg=''){
            $this->msg = $msg;
      }
      /**
       * Setting the number
       * @author Parth Gupta 
       * @param string $num 
       */
      public function setNumber($num=''){
            $this->num = $num;
      }
	// Enter the send message function here
      /**
       * Function to send message
       * @return void
       * @param $test - Defaulted to 0. If set to 1, sending sms will work on local machine as well
       * @param $send_to_queue - Defaulted to true. If not true, then it will send directly without Queuing
       * @author Parth Gupta 
       */
	public function sendMessage($test=0, $send_to_queue=true) {

		// SMS is not sent if we are on localhost. On localhost, it is sent only when $test is set to 1
		if ( (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != '127.0.0.1')
		     || $test == 1 ) {
			
			// Sending to RabbitMQ Queue if send_to_queue is set to True and SMS_QUEUE is set to 1 in config.php
			if ($send_to_queue && SMS_QUEUE) {
				
			    try {
					$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
					$channel = $connection->channel();
			
					// third parameter is for queue durability. we set it to true
					// so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
					// passive - false ; exclusive - false; auto-delete - false
					$channel->queue_declare('sms_queue', false, true, false, false);
			
					$queue_object = base64_encode(serialize(array('number' => $this->num, 
					                                              'message' => $this->msg, 
										                          'method' => 'sendMessage')));
					// delivery_mode = 2 makes message persistent (durable)
					$msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
					$channel->basic_publish($msg, '', 'sms_queue'); // send to sms_queue

					$channel->close();
					$connection->close();
				
					return true;
				
				} catch (\Exception $e) {
					// Now we ensure that the actual SMS is still sent
					goto SENDSMS;
				}
			} else {
				goto SENDSMS;
			}
			
		} else {
			return false;
		}        
        
        // SMS Sending Block of code
        SENDSMS:            
			$request = ""; //initialise the request variable
            $param['send_to'] = "91" . $this->num;
            $param['msg'] = $this->msg;

            //Have to URL encode the values
            foreach($param as $key=>$val) {
            	$request.= "&";
				//append the ampersand (&) sign after each parameter/value pair
				$request.= $key."=". urlencode($val);
				//we have to urlencode the values
            }

            //remove final (&) sign from the request
            $url = SMSGUPSHUP_URL.$request;
            $ch  = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
            $response = explode('|', $curl_scraped_page);
            return $curl_scraped_page;
	}


	public function sendOTPMessage($test=0, $send_to_queue=true) {
		// SMS is not sent if we are on localhost. On localhost, it is sent only when $test is set to 1
		if ( (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != '127.0.0.1')
		     || $test == 1 ) {

			// Sending to RabbitMQ Queue if send_to_queue is set to True and SMS_QUEUE is set to 1 in config.php
			if ($send_to_queue && SMS_QUEUE) {

			    try {
					$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
					$channel = $connection->channel();
					// third parameter is for queue durability. we set it to true
					// so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
					// passive - false ; exclusive - false; auto-delete - false
					$channel->queue_declare('sms_queue', false, true, false, false);
			
					$queue_object = base64_encode(serialize(array('number' => $this->num, 
					                                              'message' => $this->msg, 
										                          'method' => 'sendOTPMessage')));
					// delivery_mode = 2 makes message persistent (durable)
					$msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
					$channel->basic_publish($msg, '', 'sms_queue'); // send to sms_queue

					$channel->close();
					$connection->close();
				
					return true;
				
				} catch (\Exception $e) {
					// Now we ensure that the actual SMS is still sent
					goto SENDSMS;
				}
			} else {
				goto SENDSMS;
			}
			
		} else {
			return false;
		}        
        
        // SMS Sending Block of code

        SENDSMS:            
			$request = ""; //initialise the request variable
            $param['send_to'] = $this->num;
            $param['msg']     = $this->msg;

            //Have to URL encode the values
            foreach($param as $key=>$val) {
            	$request.= "&";
            	//append the ampersand (&) sign after each parameter/value pair
				$request.= $key."=". urlencode($val);
				//we have to urlencode the values
            }

            //remove final (&) sign from the request
            $url = OTPGUPSHUP_URL.$request;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
            $response = explode('|', $curl_scraped_page);
            return $curl_scraped_page;
	}

	public function sendViaQueue($method) {
		if ( method_exists($this, $method) ) {
			if($method == 'sendMessage'){
				try{
					$message 	= $this->msg;
					$number 	= $this->num;

                    $db = new Database\DB( DB_SERVERS );
					//substr($number, -10) gives last 10 digit no.
					$sql = "SELECT ws_gcm_registration_id 
                            FROM oc_customer 
                            WHERE app_version > 24 
                              AND telephone = '" . $db->escape(substr($number, -10)) . "' 
                            LIMIT 1";
					$result = $db->query($sql)->row;
					if( !empty($result['ws_gcm_registration_id']) ) {
						$gcm = array($result['ws_gcm_registration_id']);
						$msg = array
						(   'msg_type'        => 4,
							'notification_id' => time(),
							'message'         => $message,
							'title'           => 'Wholesalebox',
							'subtitle'        => '',
							'tickerText'      => '',
							'vibrate'         => 1,
							'lights'          => 1,
							'sound'           => 1
						);
						$notification = New Notification($gcm, $msg);
						$notification->sendPushNotification();
					}
					goto VIAQUEUE;
				} catch (\Exception $e) {
					goto VIAQUEUE;
				}
			} else {
				goto VIAQUEUE;
			}
			VIAQUEUE:
			return $this->{$method}(1,false);
		} else {
			return false;
		}
	}

}
