<?php
namespace Database\DBEngine;
use Exception;

final class MySQLi {
	private $link;

	private $server_wait_timeout = 30; // set default
	private $start_conn_time = 0; // set default
	private $conn_credentials = array();

	public function __construct() {

		$friends_class = array("Database\DB");
		$calling_class = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? '';

		if (!in_array($calling_class, $friends_class)) {
			throw new Exception('MySQLi instance can only be constructed by Datebase\DB. Invalid Class: ' . $calling_class);
		}
	}

	/**
	 * Method to set connection Credentials
	 * @param $hostname string
	 * @param $username string
	 * @param $password string
	 * @param $database string
	 * @param $port int
	 */
	public function setConnectionCredentials(string $hostname, string $username, string $password, string $database, int $port) {

		$this->conn_credentials = array(
			'hostname' => $hostname,
			'username' => $username,
			'password' => $password,
			'database' => $database,
			'port' => $port,
		);
	}

	/**
	 * Method to initiate connection to the MySQL Server
	 * using an instance of \MySQLi
	 * @throws Exception (if connection could not be made)
	 */
	public function connect() {

		$this->link = new \mysqli($this->conn_credentials['hostname'],
			$this->conn_credentials['username'],
			$this->conn_credentials['password'],
			$this->conn_credentials['database'],
			$this->conn_credentials['port']);

		if ($this->link->connect_error) {
			throw new Exception('Error: Could not make a database link (' . $this->link->connect_errno . ') ' . $this->link->connect_error);
			exit();
		}

		$this->start_conn_time = (float) (microtime(true)); // counter start for DB connection

		$this->link->set_charset("utf8");
		$this->link->query("SET SQL_MODE = ''");

		// get value of wait_timeout from mysql server
		$get_wait_timeout = $this->link->query("show variables like 'wait_timeout'");
		if (isset($get_wait_timeout->row['Variable_name']) && $get_wait_timeout->row['Variable_name'] == "wait_timeout") {
			$this->server_wait_timeout = (float) ($get_wait_timeout->row['Value']);
		}

	}

	public function query($sql, $slave = false) {

		// calculate DB connected time
		$duration_conn = (float) (microtime(true)) - (float) ($this->start_conn_time);

		if ($duration_conn < ($this->server_wait_timeout - 1)) {
			$query = $this->link->query($sql);
		} else {
			// mysql gone away
			$this->link->close(); // close previous connection

			// reconnect DB connection
			$this->connect();

			$query = $this->link->query($sql);
		}

		if (!$this->link->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->num_rows = $query->num_rows;
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				$query->close();

				return $result;
			} else {
				return true;
			}
		} else {
			throw new Exception('Error: ' . $this->link->error . '<br />Error No: ' . $this->link->errno);
		}
	}

	public function escape($value) {
		return $this->link->real_escape_string($value);
	}

	public function countAffected() {
		return $this->link->affected_rows;
	}

	public function getLastId() {
		return $this->link->insert_id;
	}

	public function __destruct() {
		$this->link->close();
	}

	/**
	 * Public method to get all possible ENUM values defined
	 * in a given $table and its $field
	 *
	 * @param string $table Name of the table
	 * @param string $field Name of the field (column defined as ENUM datatype)
	 *
	 * @return array of all possible Enum values (if defined). Returns empty array if invalid input(s)
	 * @throws Exception
	 * @author Madhur, 2019
	 */
	public function getEnumValues(string $table, string $field): array{

		$enum_values = array();

		// Input sanitization
		$table = $this->escape(trim($table));
		$field = $this->escape(trim($field));

		if (!empty($table) && !empty($field)) {

			// Get column details
			$sql = "SHOW COLUMNS FROM " . $table . " WHERE Field = '" . $field . "'";
			$query = $this->query($sql);

			if (!empty($query->row['Type'])) {

				// Check if the Type is enum
				$matches = array();
				preg_match("/^enum\(\'(.*)\'\)$/", $query->row['Type'], $matches);

				if (!empty($matches[1])) {
					$enum_values = explode("','", (string) $matches[1]);
				}
			}
		}

		return $enum_values;
	}

	public function singleFieldquery($sql, $field) {
		$query = $this->link->query($sql);

		if (!$this->link->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row[$field];
				}

				$result = new \stdClass();
				$result->num_rows = $query->num_rows;
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				$query->close();

				return $result;
			} else {
				return true;
			}
		} else {
			throw new Exception('Error: ' . $this->link->error . '<br />Error No: ' . $this->link->errno);
		}
	}

	/**
	 * Public method to copy a row of a Table based on given primary/unique keys (as specified)
	 * Multiple rows can be created by copying, by different arrays at second level in $key_out
	 * @Note: If there is more than one row based on input keys, error is triggered.
	 * @param $table_name (string) - Name of the table
	 * @param $key_in (array) - Column field => value
	 *                          Must have atleast one field-row pair to get the specific row (for copying).
	 * @param $key_out - (array of array) - Second level correspond to the row which will be generated.
	 *                 - It represents field => value. It, atleast, must have keys which are in $key_in
	 *                 - Valid Sample:
	 *                            $key_in:{"txn_id":"demo_id", "txn_status":"demo_status"}
	 *                            $key_out:{{"txn_id":"demo_id", "txn_status":"demo_status", "txn_date_time":"demo_txn_date_time"}}
	 * @param $alter_original (boolean, defaulted to false) - When set to true, the original row (which
	 *                   is being copied, is also altered. First array from the $key_out is used to modify the
	 *                   original row. No of copied rows will be 1 less than the number of arrays in $key_out
	 * @param $skip_fields (array, defaulted to '') - When set to '', the original row data will copy
	 *                      otherwise it skips the fields which are in array
	 * @return last inserted id if successful; Else FALSE
	 * @warning: Errors are triggered if input conditions are not satisfied
	 * @author: Madhur, 2016
	 */
	public function copyRow($table_name, $key_in, $key_out, $alter_original = false, $skip_fields = '') {

		// Ensuring atleast one key_in field for selecting row to copy
		if (count($key_in) < 1) {
			throw new Exception('Error: copyRow Method - Atleast one $key_in field required for getting row to copy.');
		}

		// Ensuring key consistency
		foreach ($key_out as $key_row_out) {
			if (array_diff_key($key_in, $key_row_out)) {

				throw new Exception('Error: copyRow Method - $key_in and $key_out do not match');
			}
		}

		// Getting the row to Copy
		$sql = 'SELECT * FROM ' . $table_name . ' WHERE ';
		$sql .= implode(' AND ', array_map(array($this, '_createFieldValueQueryForWhere'),
			$key_in,
			array_keys($key_in)));
		$row_query = $this->query($sql);

		if ($row_query->num_rows != 1) {
			throw new Exception('Error: copyRow Method - Got ' . $row_query->num_rows . ' row(s) to copy. Required: exactly 1');
		}

		$rows_to_copy = $row_query->row;

		if (!empty($skip_fields)) {
			foreach ($skip_fields as $field) {
				unset($rows_to_copy[$field]);
				foreach ($key_out as $key => $fields) {
					unset($key_out[$key][$field]);
				}
			}
		}

		// Updating the original row if $alter_original is set to true
		if ($alter_original) {
			$sql = 'UPDATE ' . $table_name . ' SET ';
			$sql .= implode(', ', array_map(array($this, '_createFieldValueQueryForSet'),
				$key_out[0],
				array_keys($key_out[0])));
			$sql .= ' WHERE ';
			$sql .= implode(' AND ', array_map(array($this, '_createFieldValueQueryForWhere'),
				$key_in,
				array_keys($key_in)));

			$this->query($sql);
		}

		// Copying Rows - INSERT INTO table (field1, field2 ..) VALUES (1, 2 ..), (3, 4 ..), (5, 6 ..)
		$sql = 'INSERT INTO ' . $table_name;
		$sql .= ' (' . implode(', ', array_keys($rows_to_copy)) . ') VALUES ';

		$values_array = array();
		foreach ($key_out as $index => $row) {

			if ($alter_original and $index < 1) {
				continue;
			}
			// skip first key_out row

			// Creatings values string for each row and storing in an array
			$row = array_replace($rows_to_copy, $row);
			$values_array[] = '(' .
			implode(', ', array_map(array($this, '_createValueQuery'), $row)) .
				')';
		}
		if (!empty($values_array)) {
			$sql .= implode(', ', $values_array);
			$this->query($sql);
		}

		return $this->getLastId();
	}

	/**
	 * Function which prepares a WHERE argument, like: $field = 'escape($value)'
	 * @author Madhur, 2016
	 * @author Modified by Nilesh, 2018
	 */
	private function _createFieldValueQueryForWhere($value, $field) {
		//commented by Nilesh for checking null values from mysql
		//return ( $field . " = '" . $this->escape($value) . "'" );
		return ($field . ((is_null($value)) === TRUE ? ' IS NULL ' : " = '" . $this->escape($value) . "' "));
	}

	/**
	 * Function which prepares a SET argument, like: $field = 'escape($value)'
	 * @author Madhur, 2016
	 * @author Modified by Nilesh, 2018
	 */
	private function _createFieldValueQueryForSet($value, $field) {
		//commented by Nilesh for checking null values from mysql
		//return ( $field . " = '" . $this->escape($value) . "'" );
		return ($field . " = " . ((is_null($value)) === TRUE ? ' NULL ' : " '" . $this->escape($value) . "' "));
	}

	/**
	 * Function which prepares a WHERE or SET argument, like: 'escape($value)'
	 * @author Madhur, 2016
	 */
	private function _createValueQuery($value) {

		//commented by Nilesh for checking null values from mysql
		//return ( "'" . $this->escape($value) . "'" );

		return ((is_null($value)) === TRUE ? ' NULL ' : " '" . $this->escape($value) . "' ");
	}

	/**
	 * Function which Execute Multiple Query for Stored Procedure
	 * @author Garvit, 2016
	 */
	public function sp_query($sql) {

		$sqlSuccess = $this->link->multi_query($sql);
		//echo "<pre>"; print_r($sqlSuccess); die;
		if ($sqlSuccess) {
			if ($this->link->more_results()) {
				$i = 0;
				$data = array();
				// Get the first buffered result set, the one with our data.
				$resource = $this->link->use_result();
				while ($result = $resource->fetch_assoc()) {
					$data[$i] = $result;
					$i++;
				}
				// Free the first resource set.
				// If you forget this one, you will get the "out of sync" error.
				$resource->free();
				// Go through each remaining buffered result and free them as well.
				// This removes all extra result sets returned, clearing the way
				// for the next SQL command.
				while ($this->link->more_results() && $this->link->next_result()) {
					$extraResult = $this->link->use_result();
					if ($extraResult instanceof \mysqli_result) {
						$extraResult->free();
					}
				}

				$query = new \stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;

				unset($data);
				return $query;
			} else {
				return TRUE;
			}
		} else {
			exit('Error: <br />Error No: ' . $this->link->error . '<br />' . $sql);
		}
	}

	public function logQuery() {

		/*
			       $dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,5);

			       $caller = isset($dbt[2]['function']) ? $dbt[2]['function'] : null;
			       if($caller == "getProductOptions") {

			           $callerFile = isset($dbt[4]['file']) ? $dbt[4]['file'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$callerFile."\n\r",FILE_APPEND);
			           $caller = isset($dbt[4]['function']) ? $dbt[4]['function'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$caller."\n\r",FILE_APPEND);
			           $callerLine = isset($dbt[4]['line']) ? $dbt[4]['line'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$callerLine."\n\r",FILE_APPEND);

			           $callerFile = isset($dbt[3]['file']) ? $dbt[3]['file'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$callerFile."\n\r",FILE_APPEND);
			           $caller = isset($dbt[3]['function']) ? $dbt[3]['function'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$caller."\n\r",FILE_APPEND);
			           $callerLine = isset($dbt[3]['line']) ? $dbt[3]['line'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$callerLine."\n\r",FILE_APPEND);

			           $callerFile = isset($dbt[2]['file']) ? $dbt[2]['file'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$callerFile."\n\r",FILE_APPEND);
			           $caller = isset($dbt[2]['function']) ? $dbt[2]['function'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$caller."\n\r",FILE_APPEND);
			           $callerLine = isset($dbt[2]['line']) ? $dbt[2]['line'] : null;
			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$callerLine."\n\r",FILE_APPEND);

			           file_put_contents("/var/www/html/system/logs/option_debug.txt",$_SERVER['HTTP_X_FORWARDED_FOR']."\n\r",FILE_APPEND);
			           file_put_contents("/var/www/html/system/logs/option_debug.txt","====================================="."\n\r",FILE_APPEND);

		*/

		/*
			* sql query tracking code - CSV File
		*/
		/*$file = '/var/www/html/system/logs/check_query.csv';

			         $fp = fopen($file , 'r');
			         if($fp){
			          while(!feof($fp)){
			                $content = fgets($fp);
			            if($content)    $c++;
			          }
			          }
			          fclose($fp);
			           ob_clean();

			           if ($c<1) {

			          $fp = fopen($file , 'w');

			            fputcsv($fp, array(date('Y-m-d'),$sql));

			        }else{

			          $fp = fopen($file, "a");
			          fputcsv($fp, array(date('Y-m-d'),$sql));

			        }
		*/
		/*
			* sql query tracking code end
		*/
	}

}
