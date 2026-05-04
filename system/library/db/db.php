<?php
/*
 * DB class to manage database connections for READ or WRITE servers
 * Note: To use this class need to pass list of READ and WRITE server list in
 *       DB class constructor
 * Generally, we will define the server list in the db_config.php file
 * and utilize the declared constant as parameter in the constructor.
 * If we want this DB object to use only WRITE servers, see useWriteDbOnly() function.
 *     For database operations from any section - Website, CRM, SRM etc...
 *       need to create DB class object.
 */

declare (strict_types = 1); //declare strict typing

namespace Database;

// require_once __DIR__ . '/db_config.php';

class DB {

	/******************/
	/****  PUBLIC  ****/
	/******************/

	// Class constructor with database servers (READ/WRITE) list as parameters
	public function __construct($db_servers) {
		// Set database engine
		$this->setDbEngine();

		//Set MAX_CONTINUOUS_SELECT_ON_WRITE_DB
		$this->setMaxContinuousSelectOnWriteDB();

		// Set database write commands
		$this->setWriteDbCommands();

		// Set database class to connect database
		$this->setDBClass();

		// Set db_servers
		$this->db_servers = $db_servers;

		// Bifurcate the servers into Read and Write
		$this->bifurcateDbServers();

		// Shuffle DB servers array based on weightage
		$this->shuffleDbServersOnWeightage($this->read_db_servers);
		$this->shuffleDbServersOnWeightage($this->write_db_servers);

		// Make Database connection object for Read and Write operations
		$this->read_db_conn = $this->makeConnection();
		$this->write_db_conn = $this->makeConnection();

		// By default, first active connection will be READ
		$this->setActiveConnectionToRead();
	}

	/*
		    * Public method to set active DB Class object to use only for Write operations
		    *           for either READ or WRITE operations.
		    * @return void
		    * @author: MSA August 2018
	*/
	public function useWriteDbOnly(): void{
		$this->use_write_db_only = true;
		$this->setActiveConnectionToWrite(true);
	}

	/*
		    * Public method to execute sql query on active database object
		    * for either READ or WRITE operations.
		    * @param string $sql
		    * @param bool $useMasterOnly
		    * @return object
		    * @author: MSA August 2018
	*/
	public function query(string $sql, bool $query_write_db_only = false) {

		$result = array();

		if ($query_write_db_only || $this->use_write_db_only) {
			$this->setActiveConnectionToWrite(true);
		} else {
			$this->updateActiveConnection($sql);
		}

		if ($this->is_active_conn_write) {
			$server = 'Write';
		} else {
			$server = 'Read';
		}

		$start = microtime(true);
		$result = $this->active_db_conn->query($sql);
		$duration = microtime(true) - $start;

		if (isset($_REQUEST['profiling']) && $_REQUEST['profiling'] == DEBUG_SQL_PROFILE) {

			if (file_exists('../system/helper/debug.php')) {
				require_once '../system/helper/debug.php';
			}

			$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
			\Debug::triggerSql('sql', $sql, $caller, $duration, $server);
		}
		return $result;
	}

	/*
		    * Public method to escape string
		    * @return string
		    * @author: MSA August 2018
	*/
	public function escape($value) {
		return $this->active_db_conn->escape($value);
	}

	/*
		    * Public method to check for count affected
		    * @return integer
		    * @author: MSA August 2018
	*/
	public function countAffected(): int {
		return $this->active_db_conn->countAffected();
	}

	/*
		    * Public method to get last inserted id
		    * @return integer
		    * @author: MSA August 2018
	*/
	public function getLastId(): int {
		return $this->active_db_conn->getLastId();
	}

	/*
		     * Public method to get all possible ENUM values defined
		     * in a given $table and its $field
		     *
		     * @param $table - string - Name of the table
		     * @param $field - string - Name of the field (column defined as ENUM datatype)
		     *
		     * @return Array of all possible Enum values (if defined). Returns empty array if invalid input(s)
		     * @author Madhur, 2019
	*/
	public function getEnumValues(string $table, string $field): array{
		return $this->active_db_conn->getEnumValues($table, $field);
	}

	/*
		    * Public method to result for single field query
		    * @param string $sql
		    * @param string $field
		    * @return array
		    * @author: MSA August 2018
	*/
	public function singleFieldquery(string $sql, string $field) {
		return $this->active_db_conn->singleFieldquery($sql, $field);
	}

	/*
		    * Public method to make copy of database table row
		    * It will ALWAYS run on WRITE DB server.
		    * @param string $table_name
		    * @param array $key_in
		    * @param array $key_out
		    * @param bool $alter_original
		    * @param array $skip_fields
		    * @return integer
		    * @author: MSA August 2018
	*/
	public function copyRow(string $table_name,
		array $key_in,
		array $key_out,
		bool $alter_original = false,
		array $skip_fields = array()) {
		$this->setActiveConnectionToWrite(true);
		return $this->active_db_conn->copyRow($table_name,
			$key_in,
			$key_out,
			$alter_original,
			$skip_fields);
	}

	/*
		    * Public method to execute query
		    * @return string $sql
		    * @author: MSA August 2018
	*/
	public function sp_query($sql) {
		return $this->active_db_conn->sp_query($sql);
	}

	/******************/
	/****  PRIVATE ****/
	/******************/

	// Database Connections
	private $active_db_conn; // DB connection currently used for a query etc
	private $write_db_conn; // DB connection to Write server
	private $read_db_conn; // DB connection to Read server
	private $use_write_db_only = false; // for always using Write DB connection
	private $is_active_conn_write = false; // if active connection is Write

	// Database Servers
	private $db_servers = ''; // All DB servers
	private $read_db_servers = array(); // All READ DB Servers
	private $write_db_servers = array(); // All WRITE DB Servers

	// Database Drivers
	private $db_engine;
	private $db_class;

	// At times, we need to immediate call read operation after a write.
	// This read operation is on the data, which has just been written by us.
	// Due to slave lag at times, read gives inconsistent data.
	// So, we allow for 2 SELECT queries post a write query on Write DB connection.
	private $select_on_write_conn_counter = 0;
	private $max_continuous_select_on_write_db;

	// If we are in Transactional queries, we do it on Write DB connection only.
	// It is continued until we hit a COMMIT or ROLLBACK
	private $wait_for_commit_rollback = false;

	// Query keywords used to identify if it is a WRITE operation.
	private $write_commands = array();

	/*
		    * private method to set create Read and Write DB connections.
		    * @return: DB Connection Object (It can be of MySQLi, PDO etc)
		    * @author: MSA August 2018
	*/
	// private function makeConnection(array $db_servers) {

	// 	// loop over servers list to make database connection
	// 	foreach ($db_servers as $key => $db_server) {
	// 		try {
	// 			$db_link_obj = new $this->db_class();
	// 			$db_link_obj->setConnectionCredentials((string) $db_server['host'],
	// 				(string) $db_server['username'],
	// 				(string) $db_server['password'],
	// 				(string) $db_server['database'],
	// 				(int) $db_server['port']
	// 			);
	// 			$db_link_obj->connect();
	// 			return $db_link_obj;
	// 		} catch (\Throwable $t) {
	// 			/* @todo */
	// 			/*-- EMERGENCY Logging using Logger framework */

	// 		}
	// 	}

	// 	// If we are here, it means No connection could  be made.
	// 	throw new \Exception("Database\DB::Function makeConnection::EMERGENCY::" .
	// 		"No Connection could be made.");
	// 	/* @todo */
	// 	// Use Logger "Params - " . serialize($db_servers)
	// }
	private function makeConnection() {

		// loop over servers list to make database connection
		try {
			$db_link_obj = new $this->db_class();
			$db_link_obj->setConnectionCredentials((string) DB_HOSTNAME,
				(string) DB_USERNAME,
				(string) DB_PASSWORD,
				(string) DB_DATABASE,
				(int) DB_PORT
			);
			$db_link_obj->connect();
			return $db_link_obj;
		} catch (\Throwable $t) {
			/* @todo */
			/*-- EMERGENCY Logging using Logger framework */

		}

		// If we are here, it means No connection could  be made.
		throw new \Exception("Database\DB::Function makeConnection::EMERGENCY::" .
			"No Connection could be made.");
		/* @todo */
		// Use Logger "Params - " . serialize($db_servers)
	}

	/*
		    * private method to set DB engine
		    * It uses DB_ENGINE constant defined in the db_config.php
		    * If constant is not found, it defaults to 'mysqli'.
		    * @author: MSA August 2018
	*/
	private function setDbEngine() {
		if (defined('DB_ENGINE')) {
			$this->db_engine = DB_ENGINE;
		} else {
			$this->db_engine = 'mysqli';
		}
	}

	/*
		    * private method to set max number of select query run
		    *       on write server after a DML query
		    * It uses MAX_CONTINUOUS_SELECT_ON_WRITE_DB constant defined in the db_config.php
		    * If constant is not found, it defaults to 2.
		    * @author: MSA August 2018
	*/
	private function setMaxContinuousSelectOnWriteDB() {
		if (defined('MAX_CONTINUOUS_SELECT_ON_WRITE_DB')) {
			$this->max_continuous_select_on_write_db = MAX_CONTINUOUS_SELECT_ON_WRITE_DB;
		} else {
			$this->max_continuous_select_on_write_db = 2;
		}
	}

	/*
		    * private method to set Write DB commands
		    * It uses DB_WRITE_COMMANDS constant defined in the db_config.php
		    * If constant is not found, it defaults to command list.
		    * @author: MSA August 2018
	*/
	private function setWriteDbCommands() {
		$this->write_commands = array(
			'create',
			'alter',
			'drop',
			'truncate',
			'comment',
			'rename',
			'insert',
			'update',
			'delete',
			'merge',
			'call',
			'lock',
			'unlock',
			'start',
			'commit',
			'rollback',
			'savepoint',
			'set',
			'optimize',
		);

	}

	/*
		    * private method to set DB Engine Class, for making connection.
		    * @return: void
		    * @author: MSA August 2018
	*/
	private function setDBClass(): void {
		require_once __DIR__ . '/db_engine/' . $this->db_engine . '.php';
		$this->db_class = 'Database\\DBEngine\\' . $this->db_engine;
	}

	/*
		    * private method to bifurcate DB Servers into Read and Write.
		    * Slave servers will be Read only.
		    * Master servers can be both Read and Write.
		    * @return: void
		    * @author: MSA August 2018
	*/
	private function bifurcateDbServers(): void {
		// foreach ($this->db_servers as $value) {

		// 	// Defaulting to true to avoid risk of writing on a slave server
		// 	$is_slave = (bool) ($value['is_slave'] ?? true);

		// 	// Every server can be Read server
		// 	$this->read_db_servers[] = $value;
		// 	if (!$is_slave) {
		// 		// Only non-Slave can be Write
		// 		$this->write_db_servers[] = $value;
		// 	}
		// }
	}

	/*
		    * Private method to shuffle DB servers array.
		    * As per weightage, a randomly selected Server
		    * will be pushed to beginning.
		    * @param &$servers array (passed by reference)
		    * @return void
		    * @author MSA August 2018
	*/
	private function shuffleDbServersOnWeightage(array &$servers): void{
		// get total weightage sum
		$weightage_sum = (int) array_sum(array_column($servers, 'weightage'));

		// if invalid sum of weightage, do nothing to array
		if ($weightage_sum > 0) {

			// choose a random between 1 and the sum of the weights.
			$random = random_int(1, $weightage_sum);

			//loop over servers list
			foreach ($servers as $key => $details) {

				// ***The next two lines are the heart of this algorithm***
				// decrement the random by the current weighting.
				$random -= $details['weightage'];

				// The larger the weighting, the more likely random is less than zero.
				if ($random <= 0) {

					// remove the $key from array
					unset($servers[$key]);
					// move it to beginning
					array_unshift($servers, $details);

					// Break out of loop - our job is done
					break;
				}
			}
		}
	}

	/*
		    * private method to update active db connection based on active query
		    * @return: void
		    * @author: MSA August 2018
	*/
	private function updateActiveConnection(string $sql): void{

		$dml_query = false;

		$words = str_word_count(strtolower(trim($sql)), 1);
		$first_word = isset($words[0]) ? $words[0] : '';
		$second_word = isset($words[1]) ? $words[1] : '';

		if (in_array($first_word, $this->write_commands)) {
			// if it is not "set" then we set to master link
			if ($first_word !== 'set'
				|| ($first_word === 'set' && $second_word === 'autocommit')
				|| ($first_word === 'set' && $second_word === 'transaction')
			) {
				$dml_query = true;

				// If we Lock tables or Begin a Transaction, we should run on Write servers only
				// till we Commit/Rollback or Unlock Tables
				if (($first_word === 'start' && $second_word === 'transaction')
					|| $first_word === 'lock') {
					$this->wait_for_commit_rollback = true;
				}

				// We are doing Commit/Rollback or Unlock Tables
				if ($first_word === 'commit'
					|| $first_word === 'rollback'
					|| $first_word === 'unlock') {
					$this->wait_for_commit_rollback = false;
				}
			}
		}

		// It's a insert/update/delete/etc query - to be run on Write Db only
		if ($dml_query || $this->wait_for_commit_rollback) {
			$this->setActiveConnectionToWrite(true);

		} elseif (!$dml_query && $this->is_active_conn_write) {
			// Not a DML query, but we are running on Master
			$this->select_on_write_conn_counter++;

			if ($this->select_on_write_conn_counter > $this->max_continuous_select_on_write_db) {
				$this->setActiveConnectionToRead();
			}

		} else {
			$this->setActiveConnectionToRead();
		}

	}

	private function setActiveConnectionToWrite(bool $reset_select_counter): void{

		$this->is_active_conn_write = true;
		$this->active_db_conn = $this->write_db_conn;
		if ($reset_select_counter) {
			$this->select_on_write_conn_counter = 0;
		}
	}

	private function setActiveConnectionToRead(): void{

		$this->is_active_conn_write = false;
		$this->active_db_conn = $this->read_db_conn;
		$this->select_on_write_conn_counter = 0; // also reset the SELECT queries counter

	}

	/*
		    * private method to set create Read and Write AWS DB connections.
		    * @return: DB Connection Object
		    * @author: Anurag Jain,
	*/
	public static function makeAWSConnection(string $db_name = RDS_SMSLOG_DB) {
		try {

			$aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, $db_name);
			return $aws_mysqli;

		} catch (\Throwable $t) {
			throw new \Exception("ERROR: Unexpected error: Could not connect to MySQL instance.");
		}

		// If we are here, it means No connection could  be made.
		throw new \Exception("Database\DB::Function makeConnection::EMERGENCY::" .
			"No Connection could be made.");
	}

}
