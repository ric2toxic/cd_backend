<?php

declare( strict_types = 1 );

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CustomerActivityLog
{
	private $_customer_activity_log_queue_name;
	private $_registry;
	private static $_aws_mysqli;

	function __construct( $registry = NULL )
	{
		$this->_registry = $registry;
		$this->_customer_activity_log_queue_name = self::getCustomerActivityLogQueueName();
		self::$_aws_mysqli = DATABASE\DB::makeAWSConnection( RDS_WSB_DB );
	}

	public static function getCustomerActivityLogQueueName(): string
	{
		$queue_name = 'STAGING_PUSH_CUSTOMER_ACTIVITY_LOGS';

		if ( strtolower( SITE_ENVIRONMENT ) == 'production' ) {
		    $queue_name = 'PUSH_CUSTOMER_ACTIVITY_LOGS';
		}

		return $queue_name;
	}

    /**
     * @param  boolean $send_to_queue [description]
     * @param  array  $data          [description]
     * @return boolean
     * @author Anurag Jain, 18th June 2019
     */
    public function logCustomerRecentActivity( bool $send_to_queue = true, array $data ): bool
    {
        $activity_data = $this->prepareCustomerActivityData( $data );
        $push_status = $this->pushCustomerActivityToQueue( $send_to_queue, $activity_data );
            
        return true;
    }

    /**
     * @param  array $data [description]
     * @return array       [description]
     * @author Anurag Jain, 18th June 2019
     */
    protected function prepareCustomerActivityData( array $data ): array
    {
        $activity_data = array();

        $request_by = $data['request_by'] ?? "";

		$activity_data['customer_id']    = !empty( $data['user_id'] ) ? $data['user_id'] : ( $this->_registry->customer->getId() ?? '0' );
		$activity_data['session_id']     = $data['session_id'] ?? '';
		$activity_data['category_id']    = !empty( $data['filter_category_id'] ) ? $data['filter_category_id'] : '0';
		$activity_data['search_keyword'] = $data['keyword'] ? trim( $data['keyword'] ) : '';
		$activity_data['page_count']     = !empty( $data['page'] ) ? $data['page'] : '1';
		$activity_data['ip_address']     = trim( getClientIpAddress() );
		$activity_data['os_platform']    = getPlatform( $request_by );
		$activity_data['user_agent']     = trim( getBrowser() );
		$activity_data['device_id']      = $data['device_id'] ?? '';
		$activity_data['gcm_id']         = $data['gcm_id'] ?? '';
		$activity_data['activity_date']  = date('Y-m-d H:i:s');

        return $activity_data;
    }

    /**
     * @param  boolean $send_to_queue [description]
     * @param  array  $activity_data [description]
     * @return bool
     * @author Anurag Jain, 18th June 2019
     */
    protected function pushCustomerActivityToQueue( bool $send_to_queue = true, array $activity_data ): bool
    {
        // Sending to RabbitMQ Queue
        if ( $send_to_queue ) {

            try {
                $connection = new AMQPStreamConnection( 'localhost', 5672, 'guest', 'guest' );
                $channel = $connection->channel();
                $channel->queue_declare( $this->_customer_activity_log_queue_name, false, true, false, false );

                $msg = base64_encode( serialize( $activity_data ));
                
                // delivery_mode = 2 makes message persistent (durable)
                $msg = new AMQPMessage( $msg, array( 'delivery_mode' => 2 ));
                
                $channel->basic_publish( $msg, '', $this->_customer_activity_log_queue_name );
                $channel->close();
                $connection->close();

                return true;

            } catch ( \Exception $e ) {
                // Now we ensure that the actual SMS is still sent
                return true;
            }
        }

        return true;
    }

    public function receiveCustomerActivityLogsQueue()
    {
		$connection = new AMQPStreamConnection( 'localhost', 5672, 'guest', 'guest' );
		$channel = $connection->channel();

		define('MAX_CONSUMERS', 4);
		define('MAX_RUN_TIME', 60*15); // 15 mins
		define('START_TIME', time());

		list( ,,$consumer_count ) = $channel->queue_declare( $this->_customer_activity_log_queue_name, false, true, false, false );

		// exit if already max_consumers listening onto this queue
		if ( $consumer_count >= MAX_CONSUMERS )
		    exit();

		$callback = function( $msg ) {

		    $log_data = unserialize( base64_decode( $msg->body ));
		    $aws_mysqli = self::$_aws_mysqli;
			
			$this->insertActivityLogInDB( $aws_mysqli, $log_data );

		    // Acknowledge the successful delivery
		    $msg->delivery_info['channel']->basic_ack( $msg->delivery_info['delivery_tag'] );

		    // Check if time limit has crossed
		    if ( START_TIME + MAX_RUN_TIME < time() ) {
		        $msg->delivery_info['channel']->basic_cancel( $msg->delivery_info['consumer_tag'] );
		    }
		};

		$channel->basic_qos(null, 1, null); // 1 in second argument ensures fair dispatch
		$channel->basic_consume( $this->_customer_activity_log_queue_name, '', false, false, false, false, $callback );

		while( count( $channel->callbacks )) {
		    $channel->wait();
		}
    }

	/**
	 * @param  array $log_data [description]
	 * @return bool           [description]
	 * @author Anurag Jain, 18th June 2019
	 */
	function insertActivityLogInDB( $aws_mysqli, $log_data ): bool
	{
		if ( empty( $aws_mysqli ) || empty( $log_data )) {
			return false;
		}

	    $insert_sql = "
	                    INSERT INTO customer_activity_logs 
	                    SET ";

	    $insert_sql_columns = array();

	    if ( !empty( $log_data['customer_id'] )) {
	    	$insert_sql_columns[] = "customer_id = '". (int) $log_data['customer_id'] ."'";
	    }
	    if ( !empty( $log_data['session_id'] )) {
	    	$insert_sql_columns[] = "session_id = '". $aws_mysqli->real_escape_string( (string) $log_data['session_id'] ) ."'";
	    }
	    if ( !empty( $log_data['category_id'] )) {
	    	$insert_sql_columns[] = "category_id = '". (int) $log_data['category_id'] ."'";
	    }
	    if ( !empty( $log_data['search_keyword'] )) {
	    	$insert_sql_columns[] = "search_keyword = '". $aws_mysqli->real_escape_string( (string) $log_data['search_keyword'] ) ."'";
	    }
	    if ( !empty( $log_data['page_count'] )) {
	    	$insert_sql_columns[] = "page_count = '". (int) $log_data['page_count'] ."'";
	    }
	    if ( !empty( $log_data['ip_address'] )) {
	    	$insert_sql_columns[] = "ip_address = INET_ATON('". $aws_mysqli->real_escape_string( (string) $log_data['ip_address'] ) ."')";
	    }
	    if ( !empty( $log_data['os_platform'] )) {
	    	$insert_sql_columns[] = "platform = '". $aws_mysqli->real_escape_string( (string) $log_data['os_platform'] ) ."'";
	    }
	    if ( !empty( $log_data['user_agent'] )) {
	    	$insert_sql_columns[] = "user_agent = '". $aws_mysqli->real_escape_string( (string) $log_data['user_agent'] ) ."'";
	    }
	    if ( !empty( $log_data['device_id'] )) {
	    	$insert_sql_columns[] = "device_id = '". $aws_mysqli->real_escape_string( (string) $log_data['device_id'] ) ."'";
	    }
	    if ( !empty( $log_data['gcm_id'] )) {
	    	$insert_sql_columns[] = "gcm_id = '". $aws_mysqli->real_escape_string( (string) $log_data['gcm_id'] ) ."'";
	    }

        $insert_sql_columns[] = "activity_date = '" . $aws_mysqli->real_escape_string( (string) $log_data['activity_date'] ) . "'";

        $insert_sql .= implode( " , " , $insert_sql_columns );

	    return $aws_mysqli->query( $insert_sql );
	}

	/**
	 * @param  array  $customer_ids [description]
	 * @return string               [description]
     * @author Anurag Jain, 21st June 2019
	 */
	public function getCustomerBrowsingCategories( array $customer_ids )
	{
		$sql = "
                SELECT 
                	GROUP_CONCAT( DISTINCT category_id ) AS category_ids 
                FROM customer_activity_logs 
                WHERE 
                	customer_id IN (". implode( ',', $customer_ids ) .")
                	AND category_id IS NOT NULL";

        $category_result = self::$_aws_mysqli->query( $sql );

        if ( $category_result->num_rows ) {

	        $row = $category_result->fetch_row();

	        $category_ids = $row[0] ?? "";
	    }

	    return $category_ids ?? "";
	}
}
?>