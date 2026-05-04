<?php

declare(strict_types=1);

/*
 * Action (Model) Class for bank_holidays table.
 * It is Singleton Class, as only one instance is enough for this model, 
 * since we need this class only to execute queries on the bank_holidays table.
 * 
 * @usage To get an instance of this class:
 *    $bank_holiday_obj = BankHolidayAction::getInstance($registry) // $registry is an instance of Registry object
 * 
 * Following functions are useful:
 *  - To get details of bank holiday dates based on specific filters, use getBankHolidayDates function
 *  - To add a New bank holiday date, use addBankHolidayDate function
 *  - To add multiple Bank holiday dates in Safe Mode (dont fail if any date has issue), use addBankHolidayDateSafeMode function
 *  - To check if a date is Bank holiday, use checkIfDateIsBankHoliday function
 * 
 * @author Madhur, 2019
 * 
 */
class BankHolidayAction {
	                                     
	/************************ Methods *********************************/

	////////////////////////// PUBLIC //////////////////////////////////
	
	/*
	 * Get Singleton Instance
	 * @param Registry class object
	 * @throws Exception if Registry class object does not contain 
	 * valid Database object at 'db' key and User object at 'user' key
	 */
	public static function getInstance(Registry $registry) : self {
		
		if ( empty(self::$instance) ) {
			self::$instance = new BankHolidayAction($registry);
		}
		
		return self::$instance;
	}
	
	/*
	 * Primary GET function to fetch bank holiday dates based on given $filter_data
	 * @param $filter_data (array). Keys in the $filter_data array are defined as below: 
	 * 'filter_date_from' - String in Y-m-d format. Start date for the bank holiday dates.
	 * 'filter_date_end'  - String in Y-m-d format. End date for the bank holiday dates
	 * 'filter_month'     - Integer. Valid values 1-12. Get bank holiday dates of a specific month
	 * 'filter_year'      - Int. Get bank holiday dates of a specific year.
	 * 'sort'             - String. Allowed values only: ASC or DESC. Invalid values are ignored and no sorting done.
	 * 'limit'            - Int. Should be > 0 (if provided)
	 * 
	 * @param $select_data (array). Specify the fields to be fetched. Check BANK_HOLIDAYS_FIELDS for complete list of 
	 * fields that can be selected. If empty array provided, then we fetch only holiday_date key.
	 * It is a good practice to specify only the required fields, to minimize the database load.
	 * 
	 * @return Array of array. Inner level array contains individual holiday dates with fields (provided as in $select_data).
	 * Returns empty array if no date is found matching the given $filter_data conditions.
	 * 
	 * @throws Various exceptions depending on invalid input params in $filter_data, $select_data.
	 */
	public function getBankHolidayDates(array $filter_data, array $select_data) : array {
		
		// Initialize output variable
		$result = array();
		
		// Validation of input parameters
		$where_sql = $this->validateFilterDataForGetBankHolidayDates($filter_data);
		$select_sql = $this->validateSelectDataForGetBankHolidayDates($select_data);
		
		// Prepare and Execute SQL		
        $sql = $select_sql . " " . $where_sql;
        $qry = $this->db->query($sql);

		// Get result and return
        if($qry->num_rows){
            $result = $qry->rows;
        }
        
        return $result;

	}
	
	
	/*
	 * Public function to check if the Input date is a Bank Holiday or not.
	 * @param $date - String. Date to check against. It should be a valid date of yyyy-mm-dd format
	 * @return Bool. True if $data is a Bank Holiday, else False.
	 * @throw Exception if the Input date is not a valid date format.
	 */
	public function checkIfDateIsBankHoliday(string $date) : bool {
		
		// Checking that the $date input is a valid date of yyyy-mm-dd format
		$date = trim($date ?? '');
		if ( empty($date) && !validateDate($date, 'Y-m-d') ) {
			throw new \Exception('Invalid Input date: ' . $date . '. Should be in yyyy-mm-dd format.');
		}
		
		// using getBankHolidayDates function to check if a row exists for the input $date or not.
		$filter_data = array('filter_date_from' => $date, 'filter_date_to' => $date, 'limit' => 1);
		$select_data = array('holiday_date');
		$result = $this->getBankHolidayDates($filter_data, $select_data);
		
		return (count($result) ? true : false);		
	}

	
	/*
	 * Public function to Add a New Bank Holiday date in the database.
	 * @param $data array . Acceptable keys are 'holiday_date', 'remark'. 'holiday_date' key is mandatory.
	 * @throws Exception if the 'holiday_date' key is missing or empty value or is not a valid date of yyyy-mm-dd format
	 * @throws Exception if the input holiday_date is already a Bank holiday.
	 * @throws Exception if valid User object instance was not available in Registry during construct of BankHolidayAction object.
	 */
	public function addBankHolidayDate(array $data) : void {
		
		// Check that valid User instance is available for logging.
		$this->checkIfUserIsValid();
		
		$holiday_date = trim($data['holiday_date'] ?? '');
		// Ensuring that holiday_date is present and is a valid date of yyyy-mm-dd format
		if ( empty($holiday_date) && !validateDate($holiday_date, 'Y-m-d') ) {
			throw new \Exception('Invalid Input Holiday date: ' . $holiday_date . '. Should be in yyyy-mm-dd format.');
		} 
		
		// Check if the date already exists as a holiday or not
		if ($this->checkIfDateIsBankHoliday($holiday_date)) {
			throw new \Exception('Input Holiday date: ' . $holiday_date . ' is already a Bank Holiday. Duplicate entry is not allowed.');
		}
		
		// remark
		$remark = trim($data['remark'] ?? '');
		
		// Preparing and Executing SQL
	    $sql = "INSERT INTO " . DB_PREFIX . "bank_holidays
                SET 
                  holiday_date       = '" . $this->db->escape($holiday_date) . "',
                  remark             = '" . $this->db->escape($remark) . "',
                  user_id            = "  . (int)$this->user->getId() . ",
                  user_name          = '" . $this->db->escape($this->user->getUserName()["name"]) . "'";
        $this->db->query($sql);
    }
	
	/*
	 * Public function to Add multiple new Bank Holiday date(s) in the database, in Safe Mode.
	 * 
	 * @param $data array (of arrays). Each internal array carries detail about a specific date.
	 * In internal array, Acceptable keys are 'holiday_date', 'remark'. 'holiday_date' key is mandatory.
	 * This function does not throw an Exception if any of the internal arrays (date(s)) have issues. Instead, it would return 
	 * an array summarizing the results of addBankHolidayDate operations on each date.
	 * 
	 * @return $summary array. Contains two keys. 
	 *  - 'success' - An array of all the dates successfully added. Empty array means no success.
	 *  - 'error' - An array of all the Exception messages. Empty array means no error.
	 * 
	 * @note This function can be used if we dont want to stop adding dates midway, if any of the intermediate dates 
	 * causes some Exception, and let add Operation happen until the last date; and get a summary at the end
	 * 
	 * @throws Exception if valid User object instance was not available in Registry during construct of BankHolidayAction object.
	 */
	public function addBankHolidayDateSafeMode(array $data) : array {
		
		// Check that valid User instance is available for logging.
		$this->checkIfUserIsValid();
		
		// Initializing output
		$summary = array();
		$summary['success'] = array();
		$summary['error'] = array();
		
		// Add dates one by one and catch the response into summary
		foreach ($data as $date_to_add) {
			
			try {
				$this->addBankHolidayDate($date_to_add);
				$summary['success'][] = $date_to_add['holiday_date']; // we are here --> means success
			} catch (\Exception $e) {
				$summary['error'][] = $e->getMessage();				
			}
		}
		
		return $summary;
	}
	
	
	////////////////////////// PRIVATE /////////////////////////////////
	
	/*
	 * Class Constructor - Private function. Called by getInstance method to return singleton object
	 * @param $registry - Registry object (to get Database & User object)
	 * 
	 * @throws Exception if 'db' key in the $registry does not contain valid object of Database class.
	 */
	private function __construct(Registry $registry) {
		
		$this->registry = $registry;
		
		// Throw Exception if Database\DB instance is not available
		if ( empty($registry->get('db')) || !($registry->get('db') instanceof Database\DB) ) {
			throw new \Exception('Valid Database\DB instance does not exist in the $registry during __construct of BankHolidayAction object!');
		}
		$this->db      = $registry->get('db');
		
		// Get user object (if available)
		if ( !empty($registry->get('user')) && $registry->get('user') instanceof User ) {
			$this->user    = $registry->get('user');
		}
	}
	
	/*
     * Private function to validate $filter_data param input 
     * provided in the getBankHolidayDates function, and generate WHERE part of query.
     * 'filter_date_from', 'filter_date_to': If provided, these should be valid dates in yyyy-mm-dd format.
     * 'filter_date_to' (if provided) should be greater than or equal to 'filter_date_from' (if provided).
     * Rules for other key(s) is defined in the documentation of getBankHolidayDates function.
     * 
     * @return WHERE part of the query string
     * 
     * @throws Exception if invalid $filter_data provided, as per defined validation rules.
     */
    private function validateFilterDataForGetBankHolidayDates(array $filter_data) : string {
		
		$where_sql = " WHERE 1 = 1 ";
		  
		// filter_date_to
		$filter_date_to = trim($filter_data['filter_date_to'] ?? '');
		if ( !empty($filter_date_to) ) {
			
			if (!validateDate($filter_date_to, 'Y-m-d')) { // check for valid yyyy-mm-dd			
				throw new \Exception('Invalid filter_date_to value: ' . $filter_date_to . ' Should be in yyyy-mm-dd format.');
			} else {
				$where_sql .= " AND holiday_date <= '" . $this->db->escape($filter_date_to) . "' ";
			}
		
		}		
		
		// filter_date_from
		$filter_date_from = trim($filter_data['filter_date_from'] ?? '');
		if ( !empty($filter_date_from) ) {
			
			if (!validateDate($filter_date_from, 'Y-m-d')) { // check for valid yyyy-mm-dd
				throw new \Exception('Invalid filter_date_from value: ' . $filter_date_from . ' Should be in yyyy-mm-dd format.');
			} elseif (!empty($filter_date_to) && strtotime($filter_date_from) > strtotime($filter_date_to)) { // date_start should be <= date_end
				throw new \Exception('filter_date_from: ' . $filter_date_from . ' cannot be more than filter_date_to: ' . $filter_date_to);
			} else {
				$where_sql .= " AND holiday_date >= '" . $this->db->escape($filter_date_from) . "' ";
			}
			
		}
		
		// filter_month
		$filter_month = (int)($filter_data['filter_month'] ?? 0);
		if ( $filter_month >= 1 && $filter_month <= 12 ) { // should be between 1 to 12
			$where_sql .= " AND MONTH(holiday_date) = " . $filter_month . " ";
		}
		
		// filter_year
		$filter_year = (int)($filter_data['filter_year'] ?? 0);
		if ( $filter_year > 0 ) {
			$where_sql .= " AND YEAR(holiday_date) = " . $filter_year . " ";
		}
		
		// sort
		$sort = strtoupper(trim($filter_data['sort'] ?? ''));
		if ($sort === 'DESC' || $sort === 'ASC') {
			$where_sql .= " ORDER BY holiday_date " . $sort . " ";
		}
		
		// limit
		$limit = (int)($filter_data['limit'] ?? 0);
		$where_sql .= ($limit > 0 ? " LIMIT " . $limit . " " : "");
		
		return $where_sql;
	}
	
	
    /*
	 * Private function to validate $select_data param input
	 * provided in the getBankHolidayDates function.
	 * It should be a string value from any of the BANK_HOLIDAYS_FIELDS.
	 * If no field provided, then it defaults to holiday_date key.
	 * 
	 * @returns SELECT part of the query string
	 * 
	 * @throws Exception if invalid field(s) provided.
	 */
	private function validateSelectDataForGetBankHolidayDates(array $select_data) : string {
		
		// Sanitizing select_data
		$select_data = array_filter(array_map(function($v){return strtolower(trim($v));},($select_data ?? array())));
		
		// initiating SELECT clause
		$select_sql = "SELECT ";

		// Default to holiday_date if no field provided to fetch
		if ( empty($select_data) ) {
		
			$select_sql .= " holiday_date ";
		
		} else {
			
			// Throw exception if there is any invalid field to fetch
			foreach ($select_data as $select_field) {						
				if (!in_array($select_field, self::BANK_HOLIDAYS_FIELDS)) {
					throw new \Exception('Invalid select_data: ' . $select_field . ' provided. No such field exists in the bank_holidays table!');
				}
			}
			
			$select_sql .= implode(", ", $select_data);
		}
		
		// adding FROM clause
		$select_sql .= " FROM " . DB_PREFIX . "bank_holidays ";
		
		return $select_sql;
		
	}
	
	/*
	 * Private function check if this Object was provided with valid User 
	 * object at the time of instantiation.
	 * This check is generally done during DML (Add/Edit/Delete) operations
	 * to allow for proper logging.
	 * 
	 * @note It is recommended to call this method in any Public DML operation.
	 * It is not required during a GET operation.
	 * 
	 * @throws Exception if User instance is not available.
	 */
	private function checkIfUserIsValid() : void {
		
		// Throw Exception if User instance is not available
		if ( empty($this->user) || !($this->user instanceof User) ) {
			throw new \Exception('Valid User instance does not exist in the BankHolidayAction object!');
		}
	}
	
	
	/************************ Attributes ******************************/
	
	////////////////////////// PRIVATE /////////////////////////////////	
	
	/*
	 * All fields in the bank_holidays table
	 */
	private const BANK_HOLIDAYS_FIELDS = array('holiday_date', 
                                               'remark', 
                                               'user_id',
                                               'user_name', 
                                               'date_added'
                                              );

    /*
     * Registry Class object
     */
    private $registry = null;
                                         
	/*
	 * Database Class object, to execute queries
	 */
	private $db = null;
	
	/*
	 * User class object, to get the details of the User performing these actions.
	 */
	private $user = null;
	
	/*
	 * Singleton Instance of the self
	 */
	private static $instance = null;
}
?>
