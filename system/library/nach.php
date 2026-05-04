<?php

declare(strict_types=1);

/**
 * Action (Model) Class for wsb_credit_nach_schedule and wsb_credit_nach_schedule_log tables.
 * It is Singleton Class, as only one instance is enough for this model, 
 * since we need this class only to execute queries.
 * 
 * @usage To get an instance of this class:
 *     $nach_obj = NachAction::getInstance($registry) // $registry is an instance of Registry object
 * 
 * Following functions are useful:
 *  - To get Suborders, for which NACH schedule is required to be generated, use getSubordersForNachSchedule function.
 *  - To get generated NACH schedules for tabular display (in admin panel, etc) with relevant details from other tables, 
 *     use getNachSchedulesWithAdditionalInfo function.
 *  - To update bank receipt(s), post the response received from the Bank servers, use getNachSchedulesForBankReceipt function.
 *  - To get NACH schedules (details from wsb_credit_nach_schedule table), use getNachSchedules function.
 *  - To add a new NACH schedule, use addNachSchedule function.
 *  - To edit an existing NACH schedule, use editNachSchedule function.
 *  - To get changelog of a NACH schedule, use getNachScheduleLog function.
 * 
 * @todo There is a potential to create an additional NachSchedule class, which will then represent 
 * a specific row in the wsb_credit_nach_schedule table, in Object form. Basically, GET function of the NachAction class, 
 * will return an array of NachSchedule Objects. ORM can also be used to implement the same.
 * 
 * @author Nishu, 2019
 * @author Madhur, 2019
 */
class NachAction {

	/************************ Methods *********************************/

	////////////////////////// PUBLIC //////////////////////////////////

    /**
     * Get Singleton Instance
     * @param Registry class object
     * @return NachAction
     * @throws Exception if Registry class object does not contain
     * valid Database object at 'db' key and User object at 'user' key
     */
	public static function getInstance(Registry $registry) : self {
		
		if ( empty(self::$instance) ) {
			self::$instance = new NachAction($registry);
		}
		
		return self::$instance;
	}
	
    /**
     * Public method to get details of all the wsb_credit Suborder(s), for which NACH schedule is to be generated. 
     * 
     * @param bool $nach_not_created_once Set to True, if we need to consider only those Suborder(s), with no NACH schedule generated yet.
     * @param bool $auto_nach_enabled Set to True, to get only those customers whose Auto NACH Schedule generation is enabled.
     * @param bool $check_delivered Set to True, to get only those suborders which are delivered, and follow days_before_nach_start rule,
     *                                 OR, are failed, and have atleast one active Credit note generated for them.
     * 
     * @return array $data - array of array. Internal array contains order_id, suborder_id, delivered_date,
     *                                                         nach_schedule_crontab, days_before_nach_start, schedule_days_for_nach 
     * 
     * @usage Primary usage conditions are: No NACH schedule generated yet, Auto Nach Enabled, Delivered Suborder following days_before_nach_start rule
     *        
     *     $nach_to_be_generated = $nach_obj->getSubordersForNachSchedule(true, true, true);
     * 
     */
    public function getSubordersForNachSchedule(bool $nach_not_created_once, 
                                                bool $auto_nach_enabled, 
                                                bool $check_delivered) : array {
		
		// Initializing output variable
		$result = array();
		
		// Preparing SQL
        $sql = "SELECT 
                  o.order_id, 
                  osub.suborder_id,
                  osub.delivered_date,
                  o.payment_code,
                  cwc.nach_schedule_crontab,
                  cwc.days_before_nach_start,
                  cwc.schedule_days_for_nach
                FROM
                ".DB_PREFIX."order AS o
                    INNER JOIN
                ".DB_PREFIX."suborder AS osub ON osub.order_id = o.order_id AND 
                                                 osub.order_status_id > 0 AND 
                                                 osub.order_status_id <> 2 AND 
                                                 osub.wsb_credit_nach_done = 'NO' 
                    INNER JOIN
                ".DB_PREFIX."customer_wsb_credit AS cwc ON o.customer_id = cwc.customer_id ";
        
        // Checking if Auto Nach Enabled is Required
        $sql .= ($auto_nach_enabled ? " AND cwc.auto_nach_enabled = 1 " : "");
        
        // Checking if NACH not generated once is Required
        $sql .= ($nach_not_created_once ? " LEFT JOIN ".DB_PREFIX."wsb_credit_nach_schedule AS nach_sch 
                                                   ON nach_sch.suborder_id = osub.suborder_id " : "");
                                                   
		$sql .= " WHERE o.payment_code = 'wsb_credit' 
		                AND o.stock_transfer = 0 
		                AND o.store_id IN (" . WSB_STORES_ID . ") 
		                AND o.franchise_id = 0 ";

		// Checking if NACH not generated once is Required
		$sql .= ($nach_not_created_once ? " AND nach_sch.suborder_id IS NULL " : "");
		
		// Checking if Delivered Suborder condition is required 
        // We start doing NACH debit from MIN(2nd day OR (days_before_nach_start + 1)) day from delivery date (considered 1st day)
        // So schedule will be created on the (days_before_nach_start) day. Note that the delivery date is 
        // cosnidered 1st day, not the 0th day. So difference has to account for (+1)
        // On the other hand, if suborder status is failed, we check that there must be atleast one active credit note for suborder
        if ( $check_delivered ) {
            $sql .= " AND ( (osub.order_status_id IN (" . implode(',', ORDER_STATUS_CLUSTERS['delivered']) . ") 
                             AND osub.delivered_date IS NOT NULL 
                             AND DATEDIFF(CURRENT_DATE(), osub.delivered_date) + 1 >= LEAST(cwc.days_before_nach_start,2)) 
                           OR
                            (osub.order_status_id IN (" . implode(',', ORDER_STATUS_CLUSTERS['failed']) . ") 
                             AND EXISTS (SELECT 1 FROM " .DB_PREFIX."credit_note ocn 
                                         WHERE ocn.suborder_id = osub.suborder_id 
                                           AND ocn.credit_note_status = 1))
                          ) ";
        }
		                              
		// Executing SQL
        $qry = $this->db->query($sql);

		if ($qry->num_rows) {
			$result = $qry->rows;
		}
		
		return $result;
	}
	
	
	/**
	 * Primary GET function to fetch NACH Schedule details from wsb_credit_nach_schedule table only.
	 * 
	 * @param array $filter_data Keys in the $filter_data array are defined as below:
     * 
	 *  - 'filter_suborder_id' - Array/Comma-separated/Single String suborder_id(s). Empty values are ignored.
     * 
	 *  - 'filter_nach_schedule_id' - Array/Comma-separated/Single Integer nach_schedule_id(s). Empty int values are ignored.
     *  - 'filter_skip_nach_schedule_id' - Array/Comma-separated/Single Integer nach_schedule_id(s). Empty int values are ignored.
     *                                     Use this filter to ignore certain nach_schedule_id(s)
     * 
	 *  - 'filter_nach_debit_date_from' -  String in Y-m-d format. Start date for the nach_debit_date. 
     *                                     @throws Exception if invalid format for non-empty value.
     * 
	 *  - 'filter_nach_debit_date_to' -  String in Y-m-d format. End date for the nach_debit_date. 
     *                                   @throws Exception if invalid format for non-empty value.
     * 
	 *  - 'filter_status' - Array/Comma-separated/Single String status value(s).
     *                      Only values in WSB_CREDIT_NACH_SCHEDULE_STATUS_ENUM_VALUES are allowed. 
     *                      Invalid Enum values are ignored.
     * 
	 *  - 'filter_deffered_by_customer' - Int. Either 0 or 1. Ignored if type-level matching for 0 or 1 is not true.
     * 
	 *  - 'filter_customer_id' - Array/Comma-separated/Single Integer customer_id(s). Empty int values are ignored.
	 *                           If this filter is used, then customer_id will come as an additional column in the result-set.
	 *                           Don't specify 'customer_id' in $select_data param, otherwise it will throw error. Its value will 
	 *                           come by default, if filtering is done on customer_id. Currently, we cannot sort on the customer_id.
     * 
	 *  - 'filter_customer_nach_details_id' - Array/Comma-separated/Single of Integer customer_nach_details_id(s) and/or (NULL or NOT_NULL)
	 *                                        To check IS NULL, you can also specify 'NULL' value in the array/string. 
     *                                        To check IS NOT NULL, specify 'NOT_NULL'.
     * 
	 *     @note 'NULL' and 'NOT_NULL' are case-sensitive. If both NULL and NOT_NULL are specified, then only 'NULL' is considered.
	 *     eg: $filter_data['filter_customer_nach_details_id'] = array('NULL', 2, 5); // checks for either NULL or 2 or 5
	 *     eg: $filter_data['filter_customer_nach_details_id'] = array('NULL');       // checks for NULL only
	 *     eg: $filter_data['filter_customer_nach_details_id'] = array('NOT_NULL');   // checks for NOT_NULL only
	 *     eg: $filter_data['filter_customer_nach_details_id'] = array('NOT_NULL', 'NULL', 3);   // checks for NULL or 3
	 *
	 *  - 'filter_order_payment_id' - Array/Comma-separated/Single of Integer order_payment_id(s) and/or (NULL or NOT_NULL)
	 *                                Usage similar to details for the 'filter_customer_nach_details_id'
	 * 
	 *  - 'sort' - Array of Array. Multi-dimensional array for multi-level sorting. First internal array will provide first level sorting and so on..
	 *                             Internal array needs to have following keys mandatorily: 'field', 'order'
	 *                             'field' need to one of the values from WSB_CREDIT_NACH_SCHEDULE_FIELDS
	 *                             'order' can only be either 'ASC' or 'DESC'
	 *             Exception raised if any of the above mentioned conditions not fulfilled.
	 *             eg: array(array('field' => 'nach_debit_date', 'order' => 'ASC'), 
	 *                       array('field' => 'suborder_id', 'order' => 'DESC'))
	 *  - 'limit' - Int. If provided, greater than 0 value is considered, else ignored.
	 * 
	 * @param array $select_data Specify the fields to be fetched. Check WSB_CREDIT_NACH_SCHEDULE_FIELDS for complete list of
	 * fields that can be selected. If empty array provided, then we fetch only nach_schedule_id key.
	 * It is a good practice to specify only the required fields, to minimize the database load.
     *
     * @param bool $valid_debit_amount Set to true if only valid Debit schedules to be fetched (debit_amount > 0). Defaults to false.
	 * 
	 * @return array of array. Inner level array contains individual nach schedule details with fields (provided as in $select_data).
	 * Returns empty array if no nach schedule is found matching the given $filter_data conditions.
	 * 
	 * @throws Exception depending on invalid input params in $filter_data, $select_data.
	 */
	public function getNachSchedules(array $filter_data, array $select_data, bool $valid_debit_amount = false) : array {

		// Validation of input parameters and generation of SQL query string
		$select_sql = $this->validateSelectDataForGetNachSchedules($select_data, $filter_data);
		$where_sql = $this->validateFilterDataForGetNachSchedules($filter_data, $valid_debit_amount);
		
		// Prepare and Execute SQL		
        $sql = $select_sql . " " . $where_sql;
        $qry = $this->db->query($sql);

        return $qry->rows;
	}

	
	/**
	 * Public method to add a new NACH schedule in the database.
	 * 
	 * @param array $data Mandatorily required Keys are 'suborder_id', 'nach_debit_date' and 'nach_debit_amount'
	 * @param bool $log Normally it is not needed to log the creation of a new Schedule. However, sometimes a new Schedule
	 *               is created due to modifications in an Existing schedule. It is desirable to log this behaviour at times.
	 *               Set to True, to enable logging. Defaulted to false.
	 *               If logging is required, then 'comment' key is required in the $data param input, with non-empty value.
	 * @param string $comment Defaulted to empty string. If $log is true, trimmed $comment should be non-empty.
	 * 
	 * @note In parameters input to this function, ensure that the code logic leads to the following: 
	 * - Combination of 'suborder_id' and 'nach_debit_date' values does not exist in the database already.
	 *   If duplicate, it is rather recommended to call editNachSchedule function and update the nach_debit_amount accordingly.
	 * - 'suborder_id' is a valid value present in the suborder table. Otherwise, Foreign key constraint will throw error.
	 *
	 * @throws Exception if any of the mandatory keys is not present. 
	 * @throws Exception if nach_debit_date is not a valid date of yyyy-mm-dd format.
	 * @throws Exception if nach_debit_date is lower than the current system date. There is no point of adding NACH schedule in past date.
	 * @throws Exception if nach_debit_amount <= 0. It should be greater than zero (even if 0.01). Otherwise no point of creating a schedule
	 * @throws Exception if $log is true, and trimmed $comment is empty. Comment is required when logging is being done.
	 * 
	 * @return int $nach_schedule_id Auto-increment primary key from the table on successful insertion of NACH schedule.
     */
	public function addNachSchedule(array $data, bool $log = false, string $comment = '') : int {
		
		// Validations of input params
		// nach_debit_date
		$nach_debit_date = trim($data['nach_debit_date'] ?? '');
		if ( empty($nach_debit_date) || !validateDate($nach_debit_date, 'Y-m-d') ) {
			throw new \Exception('Invalid/Empty nach_debit_date: ' . $nach_debit_date . '. A valid date in yyyy-mm-dd format is needed.');
		} elseif ( strtotime($nach_debit_date) < strtotime(date('Y-m-d')) ) {
			throw new \Exception('NACH Schedule cannot be created for Past dates. Date provided: ' . $nach_debit_date. '. Required date >= ' . date('Y-m-d'));
		}
		
		// suborder_id
		$suborder_id = trim($data['suborder_id'] ?? '');
		if ( empty($suborder_id) ) {
			throw new \Exception('Empty suborder_id value provided while creating NACH Schedule. A valid suborder_id is needed.');
		}
		
		// nach_debit_amount
		$nach_debit_amount = (float)($data['nach_debit_amount'] ?? 0.00);
		if ( $nach_debit_amount <= 0.00 ) {
			throw new \Exception('nach_debit_amount should be positive (greater than zero). Provided: ' . $nach_debit_amount);
		}
		
		// comment (if logging is enabled)
		$comment = trim($comment);
		if ($log && empty($comment)) {
			throw new \Exception('Non-empty comment is required when Logging is enabled during addNachSchedule.');
		}

		// Create entry for nach schedule 
		$insert_sql = " INSERT INTO 
                        ".DB_PREFIX."wsb_credit_nach_schedule
                        SET
                          suborder_id       = '" . $this->db->escape($suborder_id) . "',
                          nach_debit_date   = '" . $this->db->escape($nach_debit_date) . "',
                          nach_debit_amount = " . $nach_debit_amount . "
					  ";
		$this->db->query($insert_sql);
		$nach_schedule_id = $this->db->getLastId();
		
		// Log entry if required
		if ($log) {
			$log_details = array('suborder_id' => array('old' => '', 'new' => $suborder_id), 
			                     'nach_debit_date' => array('old' => '', 'new' => $nach_debit_date), 
			                     'nach_debit_amount' => array('old' => '', 'new' => $nach_debit_amount));
			$this->logNachScheduleChanges($nach_schedule_id, $log_details, $comment);
		}

        return $nach_schedule_id;
	}
	
	
	/**
	 * Public method to edit a NACH schedule in the wsb_credit_nach_schedule table.
	 * It also creates a row logging changes fieldwise, if there is a difference between the OLD and NEW value, 
	 * in the wsb_credit_nach_schedule_log table.
	 * 
	 * Currently allowed editable fields are (other fields are ignored): 
	 *  - nach_debit_amount 
	 *  - status 
	 *  - deffered_by_customer 
	 *  - customer_nach_details_id
	 *  - order_payment_id 
	 * 
	 * @param int $nach_schedule_id Value of the nach_schedule_id field, to be edited.
	 * @param array $edit_details (Key = Field name => Value = New value)
	 * @param string $comment Trimmed $comment should be non-empty.
	 * 
	 * @usage eg: $nach_object->editNachSchedule(12, array('status' => 'DISABLED', nach_debit_amount => 0), 'Disabling schedule as no balance recovery pending');
	 * 
	 * @note 'customer_nach_details_id' - there is a Foreign key constraint on this field. Ensure that the new value 
	 *        provided is a valid value existing in the customer_nach_details table, else MySQL Exception is thrown.
	 * 
	 * @note 'order_payment_id' - there is a Foreign key constraint on this field. Ensure that the new value 
	 *        provided is a valid value existing in the order_payment table, else MySQL Exception is thrown.
	 * 
	 * @throws Exception if $nach_schedule_id does not exist.
	 * @throws Exception if new value of float-typecasted nach_debit_amount is negative (< 0).
	 * @throws Exception if new value of 'status' is not in the WSB_CREDIT_NACH_SCHEDULE_STATUS_ENUM_VALUES.
	 * @throws Exception if new value of int-typecasted deffered_by_customer is not 0 or 1.
	 * @throws Exception if the data provided does not satisfy constraints like Foreign key etc.
	 * @throws Exception if trimmed $comment is empty string.
	 * 
	 * @return array of array, giving log details. Keys are: 
	 *  - 'log_id' - int auto-increment field from the wsb_credit_nach_schedule_log table (after log inserted).
	 *  - 'field_name' - field name which is edited and logged.
	 *  - 'old_value'
	 *  - 'new_value'
	 *
	 *  If there is no edit done, then it returns an empty array.
     */
	public function editNachSchedule(int $nach_schedule_id, array $edit_details, string $comment) : array {
		
		$editable_fields = array('nach_debit_amount', 'status', 'deffered_by_customer', 
		                         'customer_nach_details_id', 'order_payment_id');
		
		// check which fields are being edited, after removing non-editable fields. This will also be $select_data to get Old values
		$fields_to_edit = array_filter(array_keys($edit_details), function($v) use ($editable_fields){ return in_array($v,$editable_fields ); });
		// Return blank array if nothing to edit
		if ( empty($fields_to_edit) ) return array();
		
		// GET old values and also check if the $nach_schedule_id exists or not
		$old_details = $this->getNachSchedules(array('filter_nach_schedule_id' => $nach_schedule_id), $fields_to_edit);
		// Throw exception if $nach_schedule_id does not exist
		if ( empty($old_details[0]) ) throw new \Exception('Invalid nach_schedule_id: ' . $nach_schedule_id . ' given in the editNachSchedule method.');
		
		$old_details = $old_details[0];
		
		// comment (if logging is enabled)
		$comment = trim($comment);
		if ( empty($comment) ) throw new \Exception('Non-empty comment is required during editNachSchedule method.');
		
		$sql_elements = array();
		$log_details = array();
		
		// Loop over fields to edit
		foreach ($fields_to_edit as $field) {
			
			$old_value = $old_details[$field];
			$new_value = $edit_details[$field];
			
			// Typecasting/Sanitizing values, as per the field to be edited
			if ( $field === 'nach_debit_amount' ) {
				$old_value = (float)$old_value;
				$new_value = (float)$new_value;
			} elseif ( $field === 'deffered_by_customer' || $field === 'customer_nach_details_id' || $field === 'order_payment_id' ) {
				$old_value = (int)$old_value;
				$new_value = (int)$new_value;
			} elseif ( $field === 'status' ) {
				$new_value = $this->db->escape(strtoupper(trim($new_value))); // nothing to do for old value
			}
			
			// Edit if old value is not equal to new value
			if ( $new_value !== $old_value ) {
				
				// Validations
				if ( $field === 'nach_debit_amount' ) {
					if ( $new_value < 0.00 )
						throw new \Exception('nach_debit_amount cannot be negative in editNachSchedule. Provided: ' . $new_value);
				
				} elseif ( $field === 'deffered_by_customer' ) {
					if ( $new_value !== 0 && $new_value !== 1 ) 
						throw new \Exception('deffered_by_customer should be 0 or 1 in editNachSchedule. Provided: ' . $new_value);
					
				} elseif ( $field === 'status' ) {
					if ( !in_array($new_value, self::WSB_CREDIT_NACH_SCHEDULE_STATUS_ENUM_VALUES) )
						throw new \Exception('Invalid status Enum Value: ' . $new_value . ' provided in editNachSchedule. Check WSB_CREDIT_NACH_SCHEDULE_STATUS_ENUM_VALUES for allowed values.');
				}
				
				// SQL 
				$sql_elements[] = $field . " = '" . $new_value. "' ";
				
				// Logging
				$log_details[$field] = array('old' => $old_value, 'new' => $new_value);
			}
		} // end foreach loop
		
		// Preparing UPDATE sql query string and execution
		if ( !empty($sql_elements) && !empty($log_details) ) {
			$sql = "UPDATE " . DB_PREFIX . "wsb_credit_nach_schedule 
			        SET " . implode(", ", $sql_elements) . " 
			        WHERE nach_schedule_id = " . (int)$nach_schedule_id;
			$query_result = $this->db->query($sql);
			
			// if successful update then Log it
			if ( $query_result ) {
				return $this->logNachScheduleChanges($nach_schedule_id, $log_details, $comment);
			}
		}
		
		// Return blank array if we are exiting from here
		return array();
	}


	/**
	 * Public method to get changelog (edit history) of a specific NACH schedule.
	 * @param int $nach_schedule_id Schedule id whose log we need.
	 * 
	 * @note This function does not necessarily guarantee log values in a chronological order.
	 * Also, sorting flexibility is not provided via a param in this function, for SQL optimization considerations.
	 * It is recommended to do sorting at Application code (PHP) level, once the data is received from this method.
	 * @note While sorting on date_added field, in Application code, do take care of the fact that it is returned 
	 * as string datatype; and may behave differently compared to sorting on datetime datatype.
	 * 
	 * @return array of array - Internal array represent a specific log entry (row). Returns blank array if no log found.
	 */
	public function getNachScheduleLog(int $nach_schedule_id) : array {
				
		$sql = "SELECT " . implode(",", self::WSB_CREDIT_NACH_SCHEDULE_LOG_FIELDS) . " 
		        FROM " . DB_PREFIX . "wsb_credit_nach_schedule_log 
		        WHERE nach_schedule_id = " . (int)$nach_schedule_id;
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	
	/**
     * Public function to get NACH schedule with additional details from various other tables, 
     * to generate Tentative NACH Sheet, as well as Bank upload sheet.
     *
     * @warning For generating daily NACH sheet for Bank upload, dont forget to apply following additional filters:
     *  - 'filter_status' : 'NOT_DONE'
     *  - 'filter_order_payment_id' : 'NULL'
     *  - 'filter_date' : date('Y-m-d')
     *  - 'filter_deffered_by_customer' : 0
     * 
     * @param array $filter_data Defaulted to blank array
     * Available filter keys:
     * 
     *  - 'filter_customer_name' - Filter on name of the customer (<firstname> <space> <lastname>)
     * 
	 *  - 'filter_customer_id' - Array/Comma-separated/Single Integer customer_id(s). Empty int values are ignored.
     * 
     *  - 'filter_order_no' - Filter on Order no. Can contain substring from the actual order no (LIKE %..% behaviour)
     * 
     *  - 'filter_umrn_lan' - Filter on UMRN / LAN no of the NACH schedule (LIKE %..% behaviour)
     *                      (if customer_nach_details_id not updated in the schedule table, then it filters on current active)
     * 
     *  - 'filter_suborder_id' - Array/Comma-separated/Single String suborder_id(s). Empty values are ignored.
     * 
	 *  - 'filter_nach_debit_date_from' -  String in Y-m-d format. Start date for the nach_debit_date. 
     *                                     @throws Exception if invalid format for non-empty value.
     * 
	 *  - 'filter_nach_debit_date_to' -  String in Y-m-d format. End date for the nach_debit_date. 
     *                                   @throws Exception if invalid format for non-empty value.
     * 
     *  - 'filter_deffered_by_customer' - Check if deffered_by_customer to be fetched or not. 
     *                                    (0 = dont bring deffered ones; 1 = bring deffered ones only; anything else = bring All)
     * 
     * 	- 'filter_status' - Array/Comma-separated/Single String status value(s).
     *                      Only values in WSB_CREDIT_NACH_SCHEDULE_STATUS_ENUM_VALUES are allowed. 
     *                      Invalid enum values are ignored.
     * 
	 *  - 'filter_order_payment_id' - Array/Comma-separated/Single of Integer order_payment_id(s) and/or (NULL or NOT_NULL)
	 *                                Usage similar to details for the 'filter_customer_nach_details_id' in getNachSchedules method.
     * 
     *  - 'start' - For pagination, specify the Integer start number of the rows. Should be >= 0. Defaults to 0
     * 
     *  - 'limit' - For pagination, specify the Integer number of rows to fetch in a page. Should be > 0. Ignored if no value / <= 0
     * 
     * @param bool $valid_debit_amount Set to true if only valid Debit schedules to be fetched (debit_amount > 0).
     * This should be set to true when getting Actual debit sheet for Bank upload.
     * 
     * @return array (of Array). Internal array represents an individual NACH schedule with details (row).
     * @author Nishu, May 2019
     */
    public function getNachSchedulesWithAdditionalInfo(array $filter_data, bool $valid_debit_amount) : array {

        // Check for valid debit amount
        $whr = ($valid_debit_amount ? " WHERE wcns.nach_debit_amount > 0 " : " WHERE 1 = 1 ");
        
        // Filter data
        $whr .= $this->validateFilterSuborderId($filter_data); // filter_suborder_id 
        $whr .= $this->validateFilterStatus($filter_data); // filter_status
        $whr .= $this->validateFilterDefferedByCustomer($filter_data); // filter_deffered_by_customer
        $whr .= $this->validateFilterOrderPaymentId($filter_data); // filter_order_payment_id 
        $whr .= $this->validateFilterNachDebitDates($filter_data); // filter_nach_debit_date_from & filter_nach_debit_date_to 
        
        // Other filters - which are currently not used anywhere; else they can be shifted to private function and reused everywhere        
        $filter_customer_name = trim($filter_data['filter_customer_name'] ?? '');
        $whr .= (!empty($filter_customer_name) ? " AND CONCAT(c.firstname, ' ', c.lastname) LIKE '%".$this->db->escape($filter_customer_name)."%' " : "");
        
		$filter_customer_id = $filter_data['filter_customer_id'] ?? array();
		$filter_customer_id = (!is_array($filter_customer_id) ? 
                               array_map('trim', explode(',', (string)$filter_customer_id)) : 
                               $filter_customer_id);
		$filter_customer_id = array_unique(array_filter(array_map('intval', $filter_customer_id)));
        $whr .= (!empty($filter_customer_id) ? " AND c.customer_id IN (" . implode(',', $filter_customer_id) . ") " : "");

		$filter_umrn_lan = trim($filter_data['filter_umrn_lan'] ?? '');
        if ( !empty($filter_umrn_lan) ) {
            $whr .= " AND ( COALESCE(cnd1.umrn_no, cnd2.umrn_no) LIKE '%".$this->db->escape($filter_umrn_lan)."%' 
                            OR COALESCE(cnd1.lan_no, cnd2.lan_no) LIKE '%".$this->db->escape($filter_umrn_lan)."%' ) ";
        }

		$filter_order_no = trim($filter_data['filter_order_no'] ?? '');
		$whr .= (!empty($filter_order_no) ? " AND o.order_no LIKE '%".$this->db->escape($filter_order_no)."%' " : "");

		// Finalizing SQL query
        $sql = "SELECT 
                   wcns.*,
                   o.order_id,
                   o.order_no,
                   c.customer_id,
                   osub.total AS suborder_total,
                   osub.delivered_date,
                   CONCAT(c.firstname, ' ', c.lastname) AS customer_name,
                   COALESCE(cnd1.account_name, cnd2.account_name) AS account_name,
                   COALESCE(cnd1.account_no  , cnd2.account_no) AS account_no, 
                   COALESCE(cnd1.ifsc_code   , cnd2.ifsc_code) AS ifsc_code, 
                   COALESCE(cnd1.umrn_no     , cnd2.umrn_no) AS umrn_no, 
                   COALESCE(cnd1.bank_type   , cnd2.bank_type) AS bank_type, 
                   COALESCE(cnd1.lan_no      , cnd2.lan_no) AS lan_no 
                FROM
                    ".DB_PREFIX."wsb_credit_nach_schedule AS wcns
                INNER JOIN 
                    ".DB_PREFIX."suborder AS osub ON osub.suborder_id = wcns.suborder_id
                INNER JOIN
                    ".DB_PREFIX."order AS o ON o.order_id = osub.order_id
                INNER JOIN 
                    ".DB_PREFIX."customer AS c ON c.customer_id = o.customer_id
                LEFT JOIN
                    ".DB_PREFIX."customer_nach_details AS cnd1 ON cnd1.id = wcns.customer_nach_details_id 
                LEFT JOIN 
                    ".DB_PREFIX."customer_nach_details AS cnd2 ON cnd2.customer_id = c.customer_id 
                    AND cnd2.active_status = 1 
                    AND cnd2.status = 1 
               " . $whr;
    
        $sql .= " ORDER BY wcns.nach_schedule_id ASC ";
        
        // Pagination - query elements
        $limit = (int)($filter_data['limit'] ?? 0);
        if ( $limit > 0 ) {
            if ( isset($filter_data['start']) && (int)$filter_data['start'] >= 0 ) {
                $sql .= " LIMIT " . (int)$filter_data['start'] . "," . $limit;
            } else {
                $sql .= " LIMIT " . $limit;
            }
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }


    /**
     * Public function to get NACH details with limited additional info.
     * It is primarily used to update bank receipt(s), post the response received from the Bank servers.
     *
     * The difference between this function and getNachSchedulesWithAdditionalInfo is that it assumes that
     * customer_nach_details_id is already populated in the wsb_credit_nach_schedule table.
     * Also, it fetches lesser number of fields compared to the latter function.
     *
     * @note It considers only those NACH Schedules, which have nach_debit_amount > 0
     *
     * @warning For getting NACH details for validating Bank receipts, dont forget to apply following additional filters:
     *  - 'filter_status' : array('NOT_DONE', 'BANK_PENDING')
     *  - 'filter_order_payment_id' : 'NULL'
     *  - 'filter_deffered_by_customer' : 0
     *
     * Additionally, 'filter_umrn_lan' and 'filter_date' will also be needed, to validate a specific receipt.
     *
     * @param array $filter_data Defaulted to blank array
     * Available filter keys:
     *
     *  - 'filter_umrn_lan' - String - UMRN no OR LAN no of the NACH schedule (Exact Match needed).
     *                                It assumes that customer_nach_details_id is already populated.
     *
     *  - 'filter_date' - String - Specify the date for which NACH schedule(s) to be fetched.
     *
     *  - 'filter_deffered_by_customer' - Check if deffered_by_customer to be fetched or not.
     *                                 (0 = dont bring deffered ones; 1 = bring deffered ones only; anything else = bring All)
     *
     *  - 'filter_status' - Array/Comma-separated/Single String status value(s).
     *                      Only values in WSB_CREDIT_NACH_SCHEDULE_STATUS_ENUM_VALUES are allowed.
     *                      Invalid enum values are ignored.
     *
     *  - 'filter_order_payment_id' - Array/Comma-separated/Single of Integer order_payment_id(s) and/or (NULL or NOT_NULL)
     *                                Usage similar to details for the 'filter_customer_nach_details_id' in getNachSchedules method.
     *
     * @return array (of Array). Internal array represents a single order with aggregated NACH debit details
     * @throws Exception
     * @author Madhur, 2019
     */
    public function getNachSchedulesForBankReceipt(array $filter_data = array()) : array {

		// WHERE (mandatory condition - amount > 0)
        $whr = " WHERE wcns.nach_debit_amount > 0 ";
        $whr .= $this->validateFilterStatus($filter_data);             // filter_status
        $whr .= $this->validateFilterDefferedByCustomer($filter_data); // filter_deffered_by_customer
        $whr .= $this->validateFilterOrderPaymentId($filter_data);     // filter_order_payment_id 
        
        // Other filters - which are currently not used anywhere; else they can be shifted to private function and reused everywhere
		$filter_umrn_lan = trim($filter_data['filter_umrn_lan'] ?? '');
        if ( !empty($filter_umrn_lan) ) {
            $whr .= " AND ( cnd.umrn_no = '" . $this->db->escape($filter_umrn_lan) . "' 
                            OR cnd.lan_no = '" . $this->db->escape($filter_umrn_lan) . "' ) ";
        }
		
		$filter_date = trim($filter_data['filter_date'] ?? '');
        if ( !empty($filter_date) ) {
            if (!validateDate($filter_date, 'Y-m-d')) { // check for valid yyyy-mm-dd			
                throw new \Exception('Invalid filter_date value: ' . $filter_date . ' . Should be in yyyy-mm-dd format.');
            } else {
                $whr .= " AND wcns.nach_debit_date = '".$this->db->escape($filter_date)."'";
            }
        }
        
        // Finalizing SQL query                      
        $sql = "SELECT 
                  o.order_id,
                  o.order_no,
                  osub.suborder_id, 
                  cnd.customer_id, 
                  wcns.nach_schedule_id, 
                  wcns.nach_debit_amount, 
                  wcns.status 
                FROM
                    ".DB_PREFIX."wsb_credit_nach_schedule AS wcns
                INNER JOIN 
                    ".DB_PREFIX."suborder AS osub ON osub.suborder_id = wcns.suborder_id
                INNER JOIN
                    ".DB_PREFIX."order AS o ON o.order_id = osub.order_id
                INNER JOIN
                    ".DB_PREFIX."customer_nach_details AS cnd ON cnd.id = wcns.customer_nach_details_id 
               " . $whr;
               
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
	 * Public method to set customer_nach_details_id for the given $nach_schedule_ids.
     * It basically fetches the current Active and Default customer_nach_details_id for the 
     * customer (of the input nach_schedule_id), and updates it.
     * 
     * @param array $nach_schedule_ids Array of the NACH Schedule id(s) to update.
     * 
     * @warning This method does not log changes, as well as the changes are Irreversible.
     *          So the caller must take care of the fact that only those nach schedules 
     *          get updated which are editable and time related restrictions are also checked for.
     * 
     * @note This method ensures following restrictions: 
     *  - Only those NACH schedule id(s) will be Updated where nach_debit_date >= Current System Date
     *
     */
    public function updateCustomerNachDetailsIdToDefault(array $nach_schedule_ids) : void {
        
    	if( !empty($nach_schedule_ids) ) {
            
	    	$sql = "
	    			UPDATE ".DB_PREFIX."wsb_credit_nach_schedule AS wcns
					        INNER JOIN
					    ".DB_PREFIX."suborder AS osub ON wcns.suborder_id = osub.suborder_id
					        INNER JOIN
					    ".DB_PREFIX."order AS o ON o.order_id = osub.order_id
					        INNER JOIN
					    ".DB_PREFIX."customer_nach_details AS cnd ON cnd.customer_id = o.customer_id
					        AND cnd.active_status = 1
					        AND cnd.status = 1 
					SET 
					    wcns.customer_nach_details_id = cnd.id
					WHERE
					    wcns.nach_schedule_id IN (". implode(',', $nach_schedule_ids) .") 
                        AND wcns.nach_debit_date >= CURRENT_DATE() ";                
            
	    	//Execute Update Query
	    	$this->db->query($sql);
    	}
    }
    
    
    /**
	 * Public method to IRREVERSIBLY Delete a NACH schedule in the wsb_credit_nach_schedule table, 
     * and log the schedule details (row) in the wsb_credit_nach_schedule_log table.
     * 
	 * @warning Use this method very carefully, as deletion operation removes the entry from the table completely.
     * We should generally avoid using this method.
	 * 
	 * @param int $nach_schedule_id Value of the nach_schedule_id field, whose row will be deleted completely.
	 * @param string $comment Trimmed $comment should be non-empty.
	 * 
	 * @usage eg: $nach_object->deleteNachSchedule(12,'Removing as schedule is being regenerated');
	 * 
	 * @throws Exception if $nach_schedule_id does not exist.
	 * @throws Exception if trimmed $comment is empty string.
	 * 
	 * @return array of array, giving log details. Keys are: 
	 *  - 'log_id' - int auto-increment field from the wsb_credit_nach_schedule_log table (after log inserted).
	 *  - 'field_name' - field name which is edited and logged.
	 *  - 'old_value'
	 *  - 'new_value'
	 *
	 *  If there is no deletion done, then it returns an empty array.
     */
    /** @noinspection PhpUnused */
    public function deleteNachSchedule(int $nach_schedule_id, string $comment) : array {
        
        // comment
		$comment = trim($comment);
		if ( empty($comment) ) throw new \Exception('Non-empty comment is required during deleteNachSchedule method.');
		
		// GET old values and Throw exception if $nach_schedule_id does not exist
		$old_details = $this->getNachSchedules(array('filter_nach_schedule_id' => $nach_schedule_id), 
                                               self::WSB_CREDIT_NACH_SCHEDULE_FIELDS);
		if ( empty($old_details[0]) ) { 
            throw new \Exception('Invalid nach_schedule_id: ' . $nach_schedule_id . ' given in the deleteNachSchedule method.');
        }
		
        // We will first Log the old values, so that if there is any unrecoverable error, during/post deletion, 
        // we would have still logged the old values (even if they may not be deleted)
		$log_details = array();
		foreach (self::WSB_CREDIT_NACH_SCHEDULE_FIELDS as $field) {
			
			$old_value = trim($old_details[0][$field] ?? '');
            if ( !empty($old_value) ) {
                $log_details[$field] = array('old' => $old_value, 'new' => '');
			}
		}
        $log_result = $this->logNachScheduleChanges($nach_schedule_id, $log_details, $comment);
		
		// DELETE sql query and execution
        $sql = "DELETE FROM " . DB_PREFIX . "wsb_credit_nach_schedule 
                WHERE nach_schedule_id = " . (int)$nach_schedule_id;
        $this->db->query($sql);
        
        return $log_result;
	}

	
	////////////////////////// PRIVATE /////////////////////////////////
	
	/**
	 * Class Constructor - Private function. Called by getInstance method to return singleton object
	 * @param Registry $registry Registry object (to get Database & User object)
	 * 
	 * @throws Exception if 'db' key in the $registry does not contain valid object of Database class
	 * 
	 * @todo Potential of having Customer class object available instead of User class (NACH schedule will be edited from frontend)
	 */
	private function __construct(Registry $registry) {
		
		$this->registry = $registry;
		
		// Throw Exception if Database\DB instance is not available
		if ( empty($registry->get('db')) || !($registry->get('db') instanceof Database\DB) ) {
			throw new \Exception('Valid Database\DB instance does not exist in the $registry during __construct of NachAction object!');
		}
		$this->db      = $registry->get('db');
		
		// Get user object (if available), else we treat it as Auto-Generated
		if ( !empty($registry->get('user')) && $registry->get('user') instanceof User ) {
			$this->user    = $registry->get('user');
		}		
	}

    /**
     * Log changes done to a NACH schedule in the wsb_credit_nach_schedule_log table.
     *
     * @param int $nach_schedule_id
     * @param array $log_details array of array.
     *  - Structure: array(<field_name_1> => array('old' => <old_value>, 'new' => <new_value>),
     *                     <field_name_2> => array('old' => <old_value>, 'new' => <new_value>), ...)
     *  - 'old' and 'new' keys are mandatory, otherwise Exception is thrown.
     *  - field_name key value has to be from WSB_CREDIT_NACH_SCHEDULE_FIELDS, otherwise Exception is thrown
     * @param string comment
     *
     * @return array of array, giving log details. Keys are:
     *  - 'log_id' - int auto-increment field from the wsb_credit_nach_schedule_log table (after log inserted).
     *  - 'field_name' - field name which is edited and logged.
     *  - 'old_value'
     *  - 'new_value'
     *
     * @throws Exception
     */
	private function logNachScheduleChanges(int $nach_schedule_id, array $log_details, string $comment) : array {
		
		$log_result = array();
        
        // checking the User details
        $user_id = 0;
        $user_name = 'Auto-Generated';
        if ( !empty($this->user) && $this->user instanceof User ) {
            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName()['name'];
        }
		
		// Loop over fields to log one-by-one
		foreach ($log_details as $field_name => $details) {
			
			// field_name has to be from WSB_CREDIT_NACH_SCHEDULE_FIELDS
			if ( !in_array($field_name, self::WSB_CREDIT_NACH_SCHEDULE_FIELDS) ) 
				throw new \Exception('Invalid field name: ' . $field_name . ' provided in logNachScheduleChanges. Check WSB_CREDIT_NACH_SCHEDULE_FIELDS for allowed fields.');
				
			// check for old and new key to be present
			if ( !isset($details['old']) || !isset($details['new']) ) 
				throw new \Exception('old/new Keys missing in logNachScheduleChanges. log_details provided: ' . var_export($log_details, true));
				
			// INSERT SQL
			$sql = "INSERT INTO " . DB_PREFIX . "wsb_credit_nach_schedule_log 
			        SET nach_schedule_id = " . (int)$nach_schedule_id . ", 
			            field_name = '" . $this->db->escape($field_name) . "', 
			            old_value = '" . $this->db->escape($details['old']) . "', 
			            new_value = '" . $this->db->escape($details['new']) . "', 
			            comment = '" . $this->db->escape($comment) . "', 
			            user_id = "  . (int)$user_id . ",
			            user_name = '" . $this->db->escape($user_name) . "', 
			            user_agent = '" . $this->db->escape($_SERVER['HTTP_USER_AGENT']) . "', 
			            ip_address = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "'";
			$log_id = $this->db->query($sql);
			
			if ($log_id) {
				// populating result array
				$log_result[] = array('log_id' => $log_id, 
									  'field_name' => $field_name, 
			                          'old_value' => $details['old'], 
			                          'new_value' => $details['new']
			                         );
			}
		}

        return $log_result;
	}
	
	/**
	 * Private function to validate $select_data param input
	 * provided in the getNachSchedules function.
	 * It should be a array of strings; array values to be from WSB_CREDIT_NACH_SCHEDULE_FIELDS.
	 * If no field provided, then it defaults to nach_schedule_id key.
	 * 
	 * @param array $select_data
	 * @param array $filter_data This is required to apply filter_customer_id key (if provided).
	 *        Due to customer_id field not directly available in wsb_credit_nach_schedule table, 
     *        we will be required to JOIN with suborder table and then order table.
	 * 
	 * @return string SELECT part of the query string
	 * 
	 * @throws Exception if invalid field(s) provided.
	 */
	private function validateSelectDataForGetNachSchedules(array $select_data, array $filter_data) : string {
		
		// Sanitizing select_data
		$select_data = array_filter(array_map(function($v){return strtolower(trim($v));},($select_data ?? array())));
		
		// filter_customer_id Sanitization - Get customer_id also, if this filtering is provided
		$filter_customer_id = $filter_data['filter_customer_id'] ?? array();
		$filter_customer_id = (!is_array($filter_customer_id) ? 
                               array_map('trim', explode(',', (string)$filter_customer_id)) : 
                               $filter_customer_id);
		$filter_customer_id = array_unique(array_filter(array_map('intval', $filter_customer_id)));
		
		// SELECT fields
		$select_fields_arr = array();
		// Default to nach_schedule_id if no field provided to fetch
		if ( empty($select_data) ) {
			$select_fields_arr[] = 'wcns.nach_schedule_id';
		} else {
			
			// Throw exception if there is any invalid field to fetch
			foreach ($select_data as $select_field) {						
				if (!in_array($select_field, self::WSB_CREDIT_NACH_SCHEDULE_FIELDS)) {
					throw new \Exception('Invalid select_data: ' . $select_field . ' provided. Check WSB_CREDIT_NACH_SCHEDULE_FIELDS for allowed fields.');
				}
				$select_fields_arr[] = 'wcns.' . $select_field;
			}
		}
		
		// FROM clause
		$from = " FROM " . DB_PREFIX . "wsb_credit_nach_schedule AS wcns ";
		
		// filter_customer_id application
		if ( !empty($filter_customer_id) ) {
			
			$from .= " INNER JOIN " . DB_PREFIX . "suborder osub ON osub.suborder_id = wcns.suborder_id 
			           INNER JOIN " . DB_PREFIX . "order o ON o.order_id = osub.order_id 
			                                                  AND o.customer_id IN (" . implode(',', $filter_customer_id) . ") ";
			           
			$select_fields_arr[] = 'o.customer_id';
		}
		
		// SELECT clause
		$select = "SELECT " . implode(", ", $select_fields_arr);
		
		// Result SQL (SELECT + FROM)
		$select_sql = $select . " " . $from;		
		return $select_sql;
	}

    /**
     * Private function to validate $filter_data param input
     * provided in the getNachSchedules function, and generate WHERE part of query.
     * Rules for the key(s) is defined in the documentation of getNachSchedules function.
     *
     * @param array $filter_data
     * @param bool $valid_debit_amount
     * @return string WHERE part of the query string
     *
     * @throws Exception if invalid $filter_data provided, as per defined validation rules.
     */
    private function validateFilterDataForGetNachSchedules(array $filter_data, bool $valid_debit_amount) : string {

        // Check for valid debit amount
        $where_sql = ($valid_debit_amount ? " WHERE wcns.nach_debit_amount > 0 " : " WHERE 1 = 1 ");
		
		// filter_suborder_id
		$where_sql .= $this->validateFilterSuborderId($filter_data);
		
		// filter_customer_nach_details_id (Int / NULLable)
		$where_sql .= $this->validateFilterCustomerNachDetailsId($filter_data);
		
		// filter_order_payment_id (Int / NULLable)
		$where_sql .= $this->validateFilterOrderPaymentId($filter_data);
		
		// filter_status
		$where_sql .= $this->validateFilterStatus($filter_data);
		
		// filter_deffered_by_customer
		$where_sql .= $this->validateFilterDefferedByCustomer($filter_data);
        
        // filter_nach_debit_date_from & filter_nach_debit_date_to
        $where_sql .= $this->validateFilterNachDebitDates($filter_data);
		
		// @todo - Following filters can also be moved to individual Private functions. 
		// Since currently they are only used by this function, hence we can avoid.
		// When this filters will be used by other functions also, for eg: getNachSchedulesWithAdditionalInfo
		// we can then move to private function and use that function everywhere.
		// Further @todo is to move to a Validation framework
		

		// filter_nach_schedule_id
		$filter_nach_schedule_id = $filter_data['filter_nach_schedule_id'] ?? array();
		$filter_nach_schedule_id = (!is_array($filter_nach_schedule_id) ? 
                                    array_map('trim', explode(',', (string)$filter_nach_schedule_id)) : 
                                    $filter_nach_schedule_id);		
		$filter_nach_schedule_id = array_unique(array_filter(array_map('intval',$filter_nach_schedule_id)));
		$where_sql .= (!empty($filter_nach_schedule_id) ? " AND wcns.nach_schedule_id IN (" . implode(",",$filter_nach_schedule_id) . ") " : "");
        
        // filter_skip_nach_schedule_id
        $filter_skip_nach_schedule_id = $filter_data['filter_skip_nach_schedule_id'] ?? array();
		$filter_skip_nach_schedule_id = (!is_array($filter_skip_nach_schedule_id) ? 
                                         array_map('trim', explode(',', (string)$filter_skip_nach_schedule_id)) : 
                                         $filter_skip_nach_schedule_id);		
		$filter_skip_nach_schedule_id = array_unique(array_filter(array_map('intval',$filter_skip_nach_schedule_id)));
		$where_sql .= (!empty($filter_skip_nach_schedule_id) ? " AND wcns.nach_schedule_id NOT IN (" . implode(",",$filter_skip_nach_schedule_id) . ") " : "");
		
		// sort
		if ( !empty($filter_data['sort']) ) {
			
			$sort = $filter_data['sort'];
			
			if ( !is_array($sort) ) {
				throw new \Exception('Invalid sort key provided. It should be an array. Check documentation for rules.');
			}
			
			$counter = 1; // to check if it is first sort order, or second and so on..
			
			foreach ($sort as $level) {
			
				$field = strtolower(trim($level['field'] ?? ''));
				$order = strtoupper(trim($level['order'] ?? ''));
			
				if ( empty($field) || empty($order) ) {
					throw new \Exception('Empty field/order key values provided in the sort: ' . var_export($level,true));
				} elseif ( !in_array($field, self::WSB_CREDIT_NACH_SCHEDULE_FIELDS) ) {
					throw new \Exception('Invalid sort field: ' . $field . ' provided. Check WSB_CREDIT_NACH_SCHEDULE_FIELDS for allowed fields.');
				} elseif ( !in_array($order, array('ASC','DESC') ) ) {
					throw new \Exception('Invalid sort order: ' . $order . ' provided. Allowed order: ASC or DESC only.');
				}
				
				if ( $counter === 1 ) {
					$where_sql .= " ORDER BY " . "wcns." . $field . " " . $order;
				} else {
					$where_sql .= " , " . "wcns." . $field . " " . $order;
				}
				
				$counter++;
			}
		}
			
		// limit 
		$limit = (int)($filter_data['limit'] ?? 0);
		$where_sql .= ($limit > 0 ? " LIMIT " . $limit : "");
		
        return $where_sql;
    }
    
    
    ///// Validation functions for individual filters /////
    
    /**
     * Validate and generate SQL for filter_order_payment_id (Int / NULLable)
     * SQL Alias for wsb_credit_nach_schedule_table is 'wcns'
     * 
     * @param array $filter_data
     * @return string SQL query string part of WHERE clause
     */
    private function validateFilterOrderPaymentId(array $filter_data) : string {
		
		$filter_order_payment_id = $filter_data['filter_order_payment_id'] ?? array();
		$filter_order_payment_id = (!is_array($filter_order_payment_id) ? 
                                    array_map('trim', explode(',', (string)$filter_order_payment_id)) : 
                                    $filter_order_payment_id);
		
		$op_sql_elem = array();
		
		// check for NULL / NOT NULL
		if ( array_search('NULL', $filter_order_payment_id) !== false ) {
			$op_sql_elem[] = "wcns.order_payment_id IS NULL";
		} elseif ( array_search('NOT_NULL', $filter_order_payment_id) !== false ) {
			$op_sql_elem[] = "wcns.order_payment_id IS NOT NULL";
		}
		
		// other int order_payment_id (s)
		$filter_order_payment_id = array_unique(array_filter(array_map('intval', $filter_order_payment_id)));
		if ( !empty($filter_order_payment_id) ) {
			$op_sql_elem[] = "wcns.order_payment_id IN (" . implode(",", $filter_order_payment_id) . ")";
		}
		
		return (!empty($op_sql_elem) ? " AND (" . implode(" OR ", $op_sql_elem) . ") " : "");		
	}
	
	/**
     * Validate and generate SQL for filter_customer_nach_details_id (Int / NULLable)
     * SQL Alias for wsb_credit_nach_schedule_table is 'wcns'
     * 
     * @param array $filter_data
     * @return string SQL query string part of WHERE clause
     */
    private function validateFilterCustomerNachDetailsId(array $filter_data) : string {
		
		$filter_customer_nach_details_id = $filter_data['filter_customer_nach_details_id'] ?? array();
		$filter_customer_nach_details_id = (!is_array($filter_customer_nach_details_id) ? 
                                            array_map('trim', explode(',', (string)$filter_customer_nach_details_id)) : 
                                            $filter_customer_nach_details_id);
		$cnd_sql_elem = array();
		
		// check for NULL / NOT NULL
		if ( array_search('NULL', $filter_customer_nach_details_id) !== false ) {
			$cnd_sql_elem[] = "wcns.customer_nach_details_id IS NULL";
		} elseif ( array_search('NOT_NULL', $filter_customer_nach_details_id) !== false ) {
			$cnd_sql_elem[] = "wcns.customer_nach_details_id IS NOT NULL";
		}
		
		// other int customer_nach_details_id (s)
		$filter_customer_nach_details_id = array_unique(array_filter(array_map('intval', $filter_customer_nach_details_id)));
		if ( !empty($filter_customer_nach_details_id) ) {
			$cnd_sql_elem[] = "wcns.customer_nach_details_id IN (" . implode(",", $filter_customer_nach_details_id) . ")";
		}
		
		return (!empty($cnd_sql_elem) ? " AND (" . implode(" OR ", $cnd_sql_elem) . ") " : "");	
	}
	
	/**
     * Validate and generate SQL for filter_status
     * SQL Alias for wsb_credit_nach_schedule_table is 'wcns'
     * 
     * @param array $filter_data
     * @return string SQL query string part of WHERE clause
     */
    private function validateFilterStatus(array $filter_data) : string {
		
		$filter_status = $filter_data['filter_status'] ?? array();
		$filter_status = (!is_array($filter_status) ? explode(',', (string)$filter_status) : $filter_status);
        
        $filter_status_sanitized = array();
        foreach ($filter_status as $status) {
            $status_sanitized = strtoupper(trim($status));
            if ( in_array($status_sanitized, self::WSB_CREDIT_NACH_SCHEDULE_STATUS_ENUM_VALUES) ) {
                $filter_status_sanitized[] = $status_sanitized;
            }
        }
        $filter_status_sanitized = array_unique($filter_status_sanitized);

		return (!empty($filter_status_sanitized) ? " AND wcns.status IN ('" . implode("','",$filter_status_sanitized) . "') " : "");
	}

	/**
     * Validate and generate SQL for filter_deffered_by_customer 
     * SQL Alias for wsb_credit_nach_schedule_table is 'wcns'
     * 
     * @param array $filter_data
     * @return string SQL query string part of WHERE clause
     */
    private function validateFilterDefferedByCustomer(array $filter_data) : string {
	
		$filter_deffered_by_customer = $filter_data['filter_deffered_by_customer'] ?? '';
		return ($filter_deffered_by_customer === 0 || $filter_deffered_by_customer === 1) ? " AND wcns.deffered_by_customer = " . (int)$filter_deffered_by_customer . " " : "";
	}
	
	/**
     * Validate and generate SQL for filter_suborder_id  
     * SQL Alias for wsb_credit_nach_schedule_table is 'wcns'
     * 
     * @param array $filter_data
     * @return string SQL query string part of WHERE clause
     */
    private function validateFilterSuborderId(array $filter_data) : string {
	
		$filter_suborder_id = $filter_data['filter_suborder_id'] ?? array();
		$filter_suborder_id = (!is_array($filter_suborder_id) ? explode(',', (string)$filter_suborder_id) : $filter_suborder_id);
		$filter_suborder_id = array_unique(array_filter(array_map('trim',$filter_suborder_id)));
		return (!empty($filter_suborder_id) ? " AND wcns.suborder_id IN ('" . implode("','",$filter_suborder_id) . "') " : "");
	}

    /**
     * Validate and generate SQL for filter_nach_debit_date_from and filter_nach_debit_date_to
     * SQL Alias for wsb_credit_nach_schedule_table is 'wcns'
     *
     * @param array $filter_data
     * @return string SQL query string part of WHERE clause
     * @throws Exception
     */
    private function validateFilterNachDebitDates(array $filter_data) : string {
        
        $where_sql = '';
        
        // filter_nach_debit_date_to
		$filter_nach_debit_date_to = trim($filter_data['filter_nach_debit_date_to'] ?? '');
		if ( !empty($filter_nach_debit_date_to) ) {
			if (!validateDate($filter_nach_debit_date_to, 'Y-m-d')) { // check for valid yyyy-mm-dd			
				throw new \Exception('Invalid filter_nach_debit_date_to value: ' . $filter_nach_debit_date_to . ' Should be in yyyy-mm-dd format.');
			} else {
				$where_sql .= " AND wcns.nach_debit_date <= '" . $this->db->escape($filter_nach_debit_date_to) . "' ";
			}
		}
		
		// filter_nach_debit_date_from
		$filter_nach_debit_date_from = trim($filter_data['filter_nach_debit_date_from'] ?? '');
		if ( !empty($filter_nach_debit_date_from) ) {
			if (!validateDate($filter_nach_debit_date_from, 'Y-m-d')) { // check for valid yyyy-mm-dd
				throw new \Exception('Invalid filter_nach_debit_date_from value: ' . $filter_nach_debit_date_from . ' Should be in yyyy-mm-dd format.');
			} elseif (!empty($filter_nach_debit_date_to) && strtotime($filter_nach_debit_date_from) > strtotime($filter_nach_debit_date_to)) { // date_from should be <= date_to
				throw new \Exception('filter_nach_debit_date_from: ' . $filter_nach_debit_date_from . ' cannot be more than filter_nach_debit_date_to: ' . $filter_nach_debit_date_to);
			} else {
				$where_sql .= " AND wcns.nach_debit_date >= '" . $this->db->escape($filter_nach_debit_date_from) . "' ";
			}
		}

		return $where_sql;
	}

	
	/************************ Attributes ******************************/
	
	////////////////////////// PRIVATE /////////////////////////////////	
	
	/**
	 * All fields in the wsb_credit_nach_schedule table
	 * @note Whenever there is change in these fields, it is required to update the code here as well.
	 */
	private const WSB_CREDIT_NACH_SCHEDULE_FIELDS = array('nach_schedule_id', 
                                                          'suborder_id', 
                                                          'nach_debit_date',
                                                          'nach_debit_amount',
                                                          'status',
                                                          'deffered_by_customer',
                                                          'customer_nach_details_id', 
                                                          'order_payment_id', 
                                                          'date_added', 
                                                          'date_modified' 
                                                         );

    /**
     * Possible Enum values in the 'status' field of the wsb_credit_nach_schedule_table
     */
    private const WSB_CREDIT_NACH_SCHEDULE_STATUS_ENUM_VALUES = array('NOT_DONE', 
                                                                      'BANK_SUCCESS', 
                                                                      'BANK_FAILURE', 
                                                                      'BANK_PENDING', 
                                                                      'SHIFT_SCHEDULE', 
                                                                      'SUDDEN_BANK_HOLIDAY', 
                                                                      'DISABLED');
                                                                      
	/**
	 * All fields in the wsb_credit_nach_schedule_log table
	 * @note Whenever there is change in these fields, it is required to update the code here as well.
	 */
	private const WSB_CREDIT_NACH_SCHEDULE_LOG_FIELDS = array('log_id', 
	                                                          'nach_schedule_id', 
	                                                          'field_name', 
	                                                          'old_value', 
	                                                          'new_value', 
	                                                          'comment', 
	                                                          'user_id', 
	                                                          'user_name', 
	                                                          'user_agent', 
	                                                          'ip_address', 
	                                                          'date_added');

    /**
     * Registry Class object
     */
    private $registry = null;
                                         
	/**
	 * Database Class object, to execute queries
	 */
	private $db = null;
	
	/**
	 * User class object, to get the details of the User performing these actions.
	 */
	private $user = null;
	
	/**
	 * Singleton Instance of the self
	 */
	private static $instance = null;	
}