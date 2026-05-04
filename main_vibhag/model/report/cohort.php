<?php

declare(strict_types=1);

/*
 * Model Class for Cohort Analysis Report.
 * @author Madhur Bhaiya, 2019
 */
class ModelReportCohort extends Model {
	

    // Private Variables - List of Allowed filters, cohort types etc.
    
    /* 
     * Allowed Cohort types:  
     * - 'master_id'   : To check retention based on unique master_id(s) ordering
     * - 'order_count' : To check retention based on total order counts of ordering master_id(s)
     * - 'order_total' : To check retention based on total order value (GMV) of ordering master_id(s)
     * 
     * @note: At a time, only one type of cohort can be selected to generate the report.
     * @author Madhur Bhaiya, 2019
     */
    private const ALLOWED_COHORT_TYPES = array('master_id',
                                               'order_count', 
                                               'order_total');
                                               
    /*
     * Available filters on types of Orders to consider
     * - 'failed_order' : Consider COD Failed Orders. Allowed values: empty or not empty
     * - 'non_INR'      : Consider non-INR Orders. Allowed values: empty or not empty
     * - 'franchise'    : Consider Orders sold via Franchise stores. Allowed values: empty or not empty
     * - 'containing_payment_code' : Select Payment Method. Allowed values: array of payment_code (order table field)
     * - 'excluding_payment_code' : Select Payment Method. Allowed values: array of payment_code (order table field)
     * - 'shipping_zone_id : Select Shipping Country. Allowed values: array of shipping_zone_id (order table field)
     * 
     * @note: Multiple filters can be selected at a moment. Filters such as payment_code, shipping_zone_id etc 
     *        even allow an array of values to be provided.
     * @author Madhur Bhaiya, 2019
     */
    private const ALLOWED_ORDER_FILTERS = array('failed_order', 
                                                'non_INR', 
                                                'franchise', 
                                                'containing_payment_code',
                                                'excluding_payment_code',
                                                'contains_category_order_ids',
                                                'exclusive_category_order_ids',
                                                'shipping_zone_id');
                        
    /*
     * Available filters on types of Customers to consider
     * - 'min_order_count' : Minimum number of non-Cancelled orders. Allowed values: positive integer (> 0)
     * - 'avg_order_value' : Average Order Size. Allowed values: positive number (> 0) 
     * - 'is_self_ordering_customer' : Customer is tagged self_order (= 1). Allowed values: empty or not empty
     * - 'has_gst' : Customer has GST. Allowed values: empty or not empty
     * - 'has_not_gst' : Customer has GST. Allowed values: empty or not empty
     * - 'has_fashcart' : Customer has FashCart. Allowed values: empty or not empty
     * - 'has_not_fashcart' : Customer has FashCart. Allowed values: empty or not empty
     * - 'has_app' : Customer has App. Allowed values: empty or not empty           
     * - 'has_not_app' : Customer has App. Allowed values: empty or not empty           
     * - 'has_wsb_credit' : Customer has WSB Credit Enabled. Allowed values: empty or not empty
     * - 'has_not_wsb_credit' : Customer has WSB Credit Enabled. Allowed values: empty or not empty
     * - 'has_neogrowth_credit' : Customer has NeoGrowth Credit Enabled. Allowed values: empty or not empty
     * - 'has_not_neogrowth_credit' : Customer has NeoGrowth Credit Enabled. Allowed values: empty or not empty
     * - 'has_membership_atleast_once' : Customer has taken Membership atleast once. Allowed values: empty or not empty
     * - 'seller' : Consider Sellers also. Allowed values: empty or not empty
     * - 'skip_master_id' : Master id(s) to Skip. Allowed values: empty or a string of comma separated master id(s)
     * - 'consider_master_id' : Master id(s) to Consider only. Allowed values: empty or a string of comma separated master id(s)
     * - 'has_membership_atleast_once' : Customer has atleast one time applied for membership. Allowed values: empty or not empty.
     * - 'has_cod_security' : Customer has currently active COD security (deposit with us > 0). Allowed values: empty or not empty
     * - 'has_not_cod_security' : Customer has currently active COD security (deposit with us > 0). Allowed values: empty or not empty
     * - 'is_self_ordering_customer' : Self ordering customer. Allowed values: empty or not empty.

     * 
     * @note: Multiple filters can be selected at a moment.
     * @author Madhur Bhaiya, 2019
     */
    private const ALLOWED_CUSTOMER_FILTERS = array('min_order_count', 
                                                   'avg_order_value', 
                                                   'has_gst', 
                                                   'has_not_gst', 
                                                   'has_fashcart', 
                                                   'has_not_fashcart', 
                                                   'has_app', 
                                                   'has_not_app', 
                                                   'has_wsb_credit', 
                                                   'has_not_wsb_credit', 
                                                   'has_neogrowth_credit', 
                                                   'has_not_neogrowth_credit', 
                                                   'has_membership_atleast_once', 
                                                   'seller', 
                                                   'skip_master_id', 
                                                   'consider_master_id',
                                                   'has_membership_atleast_once',
                                                   'has_cod_security',
                                                   'has_not_cod_security',
                                                   'is_self_ordering_customer');
                                                   
                                                   
    /*
     * Function to get Cohort data, to get a measure of the customers getting retained.
     * Y axis of the cohort represents the Acquisition month.
     * X axis of the cohort represents the customer ordering behaviour in subsequent months as per defined interval.
     * 
     * @param $cohort_type: String. Check ALLOWED_COHORT_TYPES for definition and allowed values.
     * @param $x_axis: Array. Can contain either 'month_interval' or 'num_intervals'. 
     *                 At a time only one of the key will be considered. 'month_interval' will take 
     *                 precedence over 'num_intervals'. Both the key values should be positive integer (>= 1). 
     *                 If invalid value, we consider it as 1.
     * @param $y_axis: Array of 'date_start', 'date_end', 'sort'. 
     *                 'date_start' and 'date_end' should be in yyyy-mm-dd format.
     *                 'sort' - Allowed values are 'DESC' and 'ASC'. Default: 'ASC'. Invalid value ignored and 'ASC' used.
     * @param $order_filters: Array. Check ALLOWED_ORDER_FILTERS for definition and allowed values.
     * @param $customer_filters: Array. Check ALLOWED_CUSTOMER_FILTERS for definition and allowed values.
     * @param $acq_month: String. Can contain Acq. Month name or empty
     * 
     * @return Array. Result-set of SQL query.
     * 
     * @note Throws various exceptions for invalid input parameters.
     * @author Madhur Bhaiya, 2019
     */
    public function getCohortData(string $cohort_type, 
                                  array $x_axis, 
                                  array $y_axis, 
                                  array $order_filters, 
                                  array $customer_filters,
                                  string $acq_month = '') : array {

         // Validate various input params
         $this->validateCohortType($cohort_type);
         $this->validateXAxisParam($x_axis);
         $this->validateYAxisParam($y_axis);
         $this->validateOrderFilters($order_filters);
         $this->validateCustomerFilters($customer_filters);
         
         // Preparing SQL elements
         $this->prepareBaseSQL();
         $this->applyYAxisParamsToSQL($y_axis);
         $this->applyOrderFiltersToSQL($order_filters);
         $this->applyCustomerFiltersToSQL($customer_filters);

         // Dynamically determine the date_start value 
         $date_start = $this->getDateStartFromInnerSQL();

         $result = array();

         if (!empty($date_start)) {
         
         	$this->prepareSelectForSQL($cohort_type, $x_axis, $date_start);
         
         	// Get the SQL and execute
         	$sql = $this->generateSQL();

         
         	$query = $this->db->query($sql);
         	if ($query->num_rows) {
			    $result = $query->rows;
		 	}
		}
		 
		 return $result;

    }
	 
	 
	/*
	 * Private function to validate $cohort_type param input
	 * It should be a string value from any of the ALLOWED_COHORT_TYPES.
	 * Throws Exception if invalid value.
	 * @author Madhur Bhaiya, 2019
	 */
	private function validateCohortType(string $cohort_type) : void {
		 		 
	    if (!in_array($cohort_type, self::ALLOWED_COHORT_TYPES)) {
		    throw new \Exception($cohort_type . ' is Invalid cohort_type!');
		}
	}
	
	
	/*
     * Private function to validate $x_axis param input
     * Function takes parameter by reference and key values are defaulted to 1, 
     * if there values are not valid (not positive integer > 0)
     * @author Madhur Bhaiya, 2019
     */
    private function validateXAxisParam(array &$x_axis) : void {
		
		if ( isset($x_axis['month_interval']) ) {
			$x_axis['month_interval'] = ((int)$x_axis['month_interval'] >= 1 ? (int)$x_axis['month_interval'] : 1);
		}

		if ( isset($x_axis['num_intervals']) ) {
			$x_axis['num_intervals'] = ((int)$x_axis['num_intervals'] >= 1 ? (int)$x_axis['num_intervals'] : 1);
		}
	}
	
     
    /*
     * Private function to validate $y_axis param input
     * It can have only 'date_start' and 'date_end' keys.
     * These should be valid dates in yyyy-mm-dd format.
     * date_end (if provided) should be greater than or equal to date_start (if provided).
     * If date_start is not provided, we consider it to be '2015-09-01' (Starting month of WSB)
     * Throws exception if conditions not satisfied.
     * @author Madhur Bhaiya, 2019
     */
    private function validateYAxisParam(array &$y_axis) : void {
		  
        // Cannot contain keys other than date_start and date_end
	    if ( !empty(array_diff_key($y_axis, array_flip(array('date_start','date_end','sort')))) ) {
	        throw new \Exception('Invalid keys in $y_axis parameter found!');
        }
		  
		// Check if the keys are of valid yyyy-mm-dd format
		if (!empty($y_axis['date_start']) && !validateDate($y_axis['date_start'], 'Y-m-d')) {
			throw new \Exception('Invalid date_start value: ' . $y_axis['date_start'] . ' Should be in yyyy-mm-dd format.');
		} 
		
		if (!empty($y_axis['date_end']) && !validateDate($y_axis['date_end'], 'Y-m-d')) {
			throw new \Exception('Invalid date_end value: ' . $y_axis['date_end'] . ' Should be in yyyy-mm-dd format.');
		}
		
		// If date_start exists then it should not be more than the current date
		if (!empty($y_axis['date_start']) && strtotime($y_axis['date_start']) > time()) {
			throw new \Exception('Invalid date_start value: ' . $y_axis['date_end'] . '; cannot be more than Current Date: ' . date('Y-m-d'));
		}
		// If both date_start and date_end exists, then show error if date_start > date_end
		if (!empty($y_axis['date_start']) && !empty($y_axis['date_end']) 
		    && strtotime($y_axis['date_start']) > strtotime($y_axis['date_end'])) {
		
		    throw new \Exception('date_start: ' . $y_axis['date_start'] . 'cannot be more than date_end: ' . $y_axis['date_end']);
		}

		// If date_start is not provided, or less than 1 September 2015; we set to 1 Sept 2015
		if ( empty($y_axis['date_start']) || strtotime($y_axis['date_start']) < strtotime('2015-09-01') ) {

			$y_axis['date_start'] = '2015-09-01';
		}
		
		// 'sort' should be either DESC or ASC only. If invalid, defauled to ASC
		$sort = $y_axis['sort'] ?? '';
		$sort = strtoupper(trim($sort)); // Trimming and Uppercasing to handle asc or desc values
		$y_axis['sort'] = ($sort !== 'DESC' && $sort !== 'ASC' ? 'ASC' : $sort);
		
	}


	/*
     * Private function to validate $order_filters param input
     * It can have only keys, as defined in ALLOWED_ORDER_FILTERS.
     * Throws exception if conditions not satisfied.
     * @author Madhur Bhaiya, 2019
     */
    private function validateOrderFilters(array $order_filters) : void {
		  
        // Cannot contain keys other than date_start and date_end
	    if ( !empty(array_diff_key($order_filters, array_flip(self::ALLOWED_ORDER_FILTERS))) ) {
	        throw new \Exception('Invalid keys in $order_filters parameter found!');
        }
        
        // 'containing_payment_code' : Select Payment Method. Allowed values: array of containing_payment_code (order table field)
        // Validating that it should be an array
        if ( !empty($order_filters['containing_payment_code']) && !is_array($order_filters['containing_payment_code']) ) {
			throw new \Exception('containing_payment_code should be an Array in the $order_filters parameter!');
		}

        // 'excluding_payment_code' : Select Payment Method. Allowed values: array of excluding_payment_code (order table field)
        // Validating that it should be an array
        if ( !empty($order_filters['excluding_payment_code']) && !is_array($order_filters['excluding_payment_code']) ) {
            throw new \Exception('excluding_payment_code should be an Array in the $order_filters parameter!');
        }
		
		// 'shipping_zone_id : Select Shipping Country. Allowed values: array of shipping_zone_id (order table field)
		// Validating that it should be an array
        if ( !empty($order_filters['shipping_zone_id']) && !is_array($order_filters['shipping_zone_id']) ) {
			throw new \Exception('shipping_zone_id should be an Array in the $order_filters parameter!');
		}
	}
	
	
	/*
     * Private function to validate $customer_filters param input
     * It can have only keys, as defined in ALLOWED_CUSTOMER_FILTERS.
     * Throws exception if conditions not satisfied.
     * @author Madhur Bhaiya, 2019
     */
    private function validateCustomerFilters(array &$customer_filters) : void {
		  
        // Cannot contain keys other than date_start and date_end
	    if ( !empty(array_diff_key($customer_filters, array_flip(self::ALLOWED_CUSTOMER_FILTERS))) ) {
	        throw new \Exception('Invalid keys in $customer_filters parameter found!');
        }
        
        // 'skip_master_id' : Master id(s) to Skip. Allowed values: empty or a string of comma separated master id(s)
		if ( !empty($customer_filters['skip_master_id']) ) {
			
			$customer_filters['skip_master_id'] = trim($customer_filters['skip_master_id']);
			
			if ( !empty($customer_filters['skip_master_id']) ) {
				
				// Validating that it is a string of comma separated integers
				$re = '/^\d+(?:,\d+)*$/';
				if ( !preg_match($re, $customer_filters['skip_master_id']) ) {
					throw new \Exception('Master Id(s) to Skip are not Valid Comma separated string of Integers!');
				}
			}
		}

		// 'consider_master_id' : Master id(s) to Consider only. Allowed values: empty or a string of comma separated master id(s)
		if ( !empty($customer_filters['consider_master_id']) ) {
			
			$customer_filters['consider_master_id'] = trim($customer_filters['consider_master_id']);
			
			if ( !empty($customer_filters['consider_master_id']) ) {
				
				// Validating that it is a string of comma separated integers
				$re = '/^\d+(?:,\d+)*$/';
				if ( !preg_match($re, $customer_filters['consider_master_id']) ) {
					throw new \Exception('Master Id(s) to Consider are not Valid Comma separated string of Integers!');
				}
			}
		}

	}	
	
	
	/*
	 * Function to prepare base SQL query elements.
	 * @author Madhur Bhaiya, 2019
	 */
	private function prepareBaseSQL() : void {
		
		// Inner FROM / JOIN 
		$this->inner_from_join_sql = " FROM " . DB_PREFIX . "order o1 
		                               JOIN " . DB_PREFIX . "suborder osub1 ON osub1.order_id = o1.order_id 
		                               JOIN " . DB_PREFIX . "customer c1 ON c1.customer_id = o1.customer_id ";
        // Storing the tables already consider in Inner FROM/JOIN
        $this->inner_from_join[] = 'order';
        $this->inner_from_join[] = 'suborder';
        $this->inner_from_join[] = 'customer';

        // Outer FROM / JOIN
        $this->outer_from_join_sql = " JOIN " . DB_PREFIX . "customer c2 ON c2.master_id = dt.master_id 
		                               JOIN " . DB_PREFIX . "order o2 ON o2.customer_id = c2.customer_id  
		                               JOIN " . DB_PREFIX . "suborder osub2 ON osub2.order_id = o2.order_id ";
        // Storing the tables already consider in Outer FROM/JOIN
        $this->outer_from_join[] = 'customer';
        $this->outer_from_join[] = 'order';
        $this->outer_from_join[] = 'suborder';
		                               
        
        // WHERE
        $this->inner_where[] = "osub1.order_status_id > " . (int)ORDER_STATUS['Missing']; // Ignore Missing Orders
        $this->outer_where[] = "osub2.order_status_id > " . (int)ORDER_STATUS['Missing']; // Ignore Missing Orders
        
        $this->inner_where[] = "osub1.order_status_id <> " . (int)ORDER_STATUS['Canceled']; // Ignore Canceled Orders
        $this->outer_where[] = "osub2.order_status_id <> " . (int)ORDER_STATUS['Canceled']; // Ignore Canceled Orders
        
        $this->inner_where[] = "o1.store_id IN (" . WSB_STORES_ID . ")";
        $this->outer_where[] = "o2.store_id IN (" . WSB_STORES_ID . ")";
        
        $this->inner_where[] = "o1.stock_transfer = 0"; // Ignore Stock Transfer Orders
        $this->outer_where[] = "o2.stock_transfer = 0"; // Ignore Stock Transfer Orders
        
        
        // Inner SELECT
        $this->inner_select_sql = " SELECT c1.master_id, 
                                           CONCAT(DATE_FORMAT(MIN(o1.date_added), '%Y-%m'), '-01') AS first_ym ";	
                                           
        
        // GROUP BY
        $this->inner_group_by_sql = " GROUP BY c1.master_id ";
        $this->outer_group_by_sql = " GROUP BY dt.first_ym ";
		
	}
	
	/*
	 * Function to apply $y_axis parameters to SQL.
	 * Basically, date_start and date_end keys are specified in the 
	 * HAVING clause of the Innner SQL query, to fetch Customers acquired 
	 * within a specified Date Range only.
	 * @author Madhur Bhaiya, 2019
	 */
	private function applyYAxisParamsToSQL(array $y_axis) : void {
		
		// date_start
		if ( !empty($y_axis['date_start']) ) {
			
			$this->inner_having[] = "MIN(o1.date_added) >= '" . $this->db->escape(trim($y_axis['date_start'])) . " 00:00:00'";
		}
		
		// date_end
		if ( !empty($y_axis['date_end']) ) {
			
			$this->inner_having[] = "MIN(o1.date_added) <= '" . $this->db->escape(trim($y_axis['date_end'])) . " 23:59:59'";
		}
		
		// sort
		$this->outer_order_by_sql = " ORDER BY dt.first_ym " . $y_axis['sort'];
		
	}
	
	/*
	 * Function to read $order_filters and accordingly 
	 * populate SQL query elements.
	 * Check the definition of ALLOWED_ORDER_FILTERS to understand the logic.
	 * @author Madhur Bhaiya, 2019
	 */
	private function applyOrderFiltersToSQL(array $order_filters) : void {
        
        // Dont Consider COD Failed Orders 
        if ( empty($order_filters['failed_order']) ) {
			
			$s = ".order_status_id <> " . (int)ORDER_STATUS['Failed'];
			
			$this->inner_where[] = "osub1" . $s;
			$this->outer_where[] = "osub2" . $s;
			
		} // else consider them - no need to put extra WHERE condition
		
		
		// Dont Consider non-INR Orders
        if ( empty($order_filters['non_INR']) ) {
			
			$s = ".currency_code = 'INR'";
			
			$this->inner_where[] = "o1" . $s;
			$this->outer_where[] = "o2" . $s;
			
		} // else consider them - no need to put extra WHERE condition
		
		
		// Dont Consider Orders placed at Franchise Stores
		if ( empty($order_filters['franchise']) ) {
			
			$this->inner_where[] = "o1.franchise_id = 0";
			$this->outer_where[] = "o2.franchise_id = 0";
			
		} // else consider them - no need to put extra WHERE condition
		
		
		// Considers Orders from a Specific Payment Method only.
		// @note that this condition will be applied in measuring retention orders only (Outer WHERE), 
		//       not the Acquistion Date (Inner WHERE)
		if ( !empty($order_filters['payment_code']) ) {

			$this->inner_where[] = "o1.payment_code IN ('" . implode("','", $order_filters['payment_code']) . "')";
			$this->outer_where[] = "o2.payment_code IN ('" . implode("','", $order_filters['payment_code']) . "')";
		} // else consider all the orders - no need to put extra WHERE condition
		
		
		// Consider Orders Shipped to Specific Countries only
		if ( !empty($order_filters['shipping_zone_id']) ) {
			
			$s = ".shipping_zone_id IN (" . implode(",", array_map('intval', $order_filters['shipping_zone_id'])) . ")";
			
			$this->inner_where[] = "o1" . $s;
			$this->outer_where[] = "o2" . $s;
			
		} // else consider all the orders - no need to put extra WHERE condition

	}
	
	
	/*
	 * Function to read $customer_filters and accordingly 
	 * populate SQL query elements.
	 * Check the definition of ALLOWED_CUSTOMER_FILTERS to understand the logic.
	 * @author Madhur Bhaiya, 2019
	 */
	private function applyCustomerFiltersToSQL(array $customer_filters) : void {
     
        // 'min_order_count' : Minimum number of non-Cancelled orders. 
        // Applying this condition is required only when the value of min_order_count > 1
		if ( !empty($customer_filters['min_order_count']) && (int)$customer_filters['min_order_count'] > 1 ) {
		 
			$this->inner_having[] = "COUNT(DISTINCT o1.order_id) >= " . (int)$customer_filters['min_order_count'];
		}
	 
		// 'avg_order_value' : Average Order Size. Allowed values: positive number (> 0)
		if ( !empty($customer_filters['avg_order_value']) && (float)$customer_filters['avg_order_value'] > 0 ) {
		 
			$this->inner_having[] = "(SUM(osub1.total)/COUNT(DISTINCT o1.order_id)) >= " . (float)$customer_filters['avg_order_value'];
		}
		
		// 'is_self_ordering_customer' : Customer is tagged self_order (= 1)
		if ( !empty($customer_filters['is_self_ordering_customer']) ) {
		 
			$this->inner_having[] = "SUM(c1.self_order)";
		}
	 
		// 'has_gst' : Customer has GST
		if ( !empty($customer_filters['has_gst']) ) {
		 
			$this->inner_having[] = "SUM(c1.gst_number IS NOT NULL AND TRIM(c1.gst_number) <> '')";
		}
		
		// 'has_fashcart' : Customer has FashCart Website
		if ( !empty($customer_filters['has_fashcart']) ) {
		 
			$this->inner_having[] = "SUM(c1.has_website)";
		}
		
		// 'has_app' : Customer has App (Currently considering Android only)
		if ( !empty($customer_filters['has_app']) ) {

			$this->inner_having[] = "SUM(c1.ws_gcm_registration_id IS NOT NULL AND TRIM(c1.ws_gcm_registration_id) <> '')";
		}
		
		// 'has_wsb_credit' : Customer has WSB Credit Enabled
		if ( !empty($customer_filters['has_wsb_credit']) ) {
			
			// We need to join to customer_wsb_credit table
			if ( !in_array('customer_wsb_credit', $this->inner_from_join) ) {
				
				// Note that the Join here is on Customer ID, so we will have to do LEFT JOIN
				// As within a master_id cluster, there can be multiple customer_id(s)
				// Some of them may not have wsb_credit enabled; so if we do INNER JOIN, our 
				// acquisition date will change (some old customer id(s) of the same master id will get ignored
				// Hence we have to use LEFT JOIN with GROUP BY and HAVING clause.
				$this->inner_from_join_sql .= " LEFT JOIN " . DB_PREFIX . "customer_wsb_credit cwc1 
				                                ON cwc1.customer_id = c1.customer_id AND 
				                                   cwc1.status = 'ENABLED' ";
				$this->inner_from_join[] = 'customer_wsb_credit';
			}
		 
			$this->inner_having[] = "COUNT(cwc1.customer_id)";
		}
		
		// 'has_neogrowth_credit' : Customer has NeoGrowth Credit Enabled
		if ( !empty($customer_filters['has_neogrowth_credit']) ) {
			
			// We need to join to customer_credit table
			if ( !in_array('customer_credit', $this->inner_from_join) ) {
				
				// Note that the Join here is on Customer ID, so we will have to do LEFT JOIN
				// As within a master_id cluster, there can be multiple customer_id(s)
				// Some of them may not have neogrowth enabled; so if we do INNER JOIN, our 
				// acquisition date will change (some old customer id(s) of the same master id will get ignored
				// Hence we have to use LEFT JOIN with GROUP BY and HAVING clause.
				$this->inner_from_join_sql .= " LEFT JOIN " . DB_PREFIX . "customer_credit cc1 
				                                ON cc1.customer_id = c1.customer_id AND 
				                                   cc1.type = 'Neogrowth' AND 
				                                   cc1.credit_status = 1 ";
				$this->inner_from_join[] = 'customer_credit';
			}
		 
			$this->inner_having[] = "COUNT(cc1.customer_id)";
		}
		
		// 'has_membership_atleast_once' : Customer has taken Membership atleast once
		if ( !empty($customer_filters['has_membership_atleast_once']) ) {
			
			// We need to join to master_customer_membership table
			if ( !in_array('master_customer_membership', $this->inner_from_join) ) {
				
				// Note that the join here is on master_id instead of customer_id 
				// so we can directly use INNER JOIN and avoid HAVING clause
				$this->inner_from_join_sql .= " INNER JOIN " . DB_PREFIX . "master_customer_membership mcm1  
				                                ON mcm1.master_id = c1.master_id ";
				$this->inner_from_join[] = 'master_customer_membership';
			}
		}
		
		// 'has_cod_security' : Customer has currently active COD security (deposit with us > 0)
		if ( !empty($customer_filters['has_cod_security']) ) {
			
			// We need to join to cod_security table
			if ( !in_array('cod_security', $this->inner_from_join) ) {
				
				// Note that the join here is on master_id instead of customer_id 
				// so we can directly use INNER JOIN and avoid HAVING clause
				$this->inner_from_join_sql .= " INNER JOIN " . DB_PREFIX . "cod_security cs1  
				                                ON cs1.master_id = c1.master_id AND 
				                                   cs1.cod_security_balance > 0 ";
				$this->inner_from_join[] = 'cod_security';
			}
		}

		
		
		// 'seller' : Consider Sellers also
		if ( empty($customer_filters['seller']) ) {
			
			// We need to join to ms_seller table
			if ( !in_array('ms_seller', $this->inner_from_join) ) {
				
				$this->inner_from_join_sql .= " LEFT JOIN " . DB_PREFIX . "ms_seller ms1 
				                                ON ms1.seller_id = c1.customer_id AND 
				                                   ms1.seller_id <> ms1.nickname ";
				$this->inner_from_join[] = 'ms_seller';
			}
			
			$this->inner_having[] = "NOT COUNT(ms1.seller_id)";
			
		}
		
		// 'skip_master_id' : Master id(s) to Skip. Allowed values: empty or a string of comma separated master id(s)
		if ( !empty($customer_filters['skip_master_id']) ) {
				
			$this->inner_where[] = "c1.master_id NOT IN (" . $customer_filters['skip_master_id'] . ")";
		}

		// 'consider_master_id' : Master id(s) to Consider only. Allowed values: empty or a string of comma separated master id(s)
		if ( !empty($customer_filters['consider_master_id']) ) {
				
			$this->inner_where[] = "c1.master_id IN (" . $customer_filters['consider_master_id'] . ")";
		}
		
	}
	

	/*
	 * Function to prepare a separate SQL based on Inner SQL
	 * to get the minimum date_start based on all the applied filters.
	 * This is then used to determine the dynamic number of columns required in
	 * the prepareSelectForSQL method.
	 */
	private function getDateStartFromInnerSQL() : string {

		$date_start = '';

		// prepare Inner SQL completely
		$this->inner_sql = $this->inner_select_sql;
		$this->inner_sql .= $this->inner_from_join_sql;
		if ( !empty($this->inner_where) ) {
			$inner_where_sql = ' WHERE ' . implode(' AND ', $this->inner_where);
			$this->inner_sql .= $inner_where_sql;
		}
		$this->inner_sql .= $this->inner_group_by_sql;
		if ( !empty($this->inner_having) ) {
			$inner_having_sql = ' HAVING ' . implode(' AND ', $this->inner_having);
			$this->inner_sql .= $inner_having_sql;
		}

		// Using this inner SQL as Derived table to get the minimum of all acquisition date
		$sql = "SELECT MIN(dt.first_ym) AS date_start 
		        FROM ( " . $this->inner_sql . " ) AS dt";

		// Executing the query
		$query = $this->db->query($sql);

		if ($query->num_rows) {
			$date_start = strval($query->row['date_start']);
		}

		return $date_start;
	}
	
	/*
	 * Fuction to prepare SELECT elements for the Outer SQL query.
	 * $date_start (dynamically determined based on inner sql) and $x_axis param is used 
	 * to determine the number of `+..M` columns to be generated.
	 * $cohort_type is used to determine what type of parameter to measure.
	 * @author Madhur Bhaiya, 2019
	 */
	private function prepareSelectForSQL(string $cohort_type, array $x_axis, string $date_start) : void {
		
		// Determining Months from the Start Date (excluding start Date's month) to Current Date (including Current Month)
		$ts_start = strtotime($date_start);

		$year_start = (int)date('Y', $ts_start);
		$year_end   = (int)date('Y', time());

		$month_start = (int)date('m', $ts_start);
		$month_end   = (int)date('m', time());

		$months = (($year_end - $year_start) * 12) + ($month_end - $month_start);

		// if month_interval is not set and num_intervals is set instead.
		if ( !isset($x_axis['month_interval']) ) {
			$month_interval = 1;
			if ( isset($x_axis['num_intervals']) ) {
				$months = min($months, $x_axis['num_intervals']);	
			}
		} else {
			$month_interval = $x_axis['month_interval'];
		}

		
		// Base SELECT SQL
		$this->outer_select_sql = " SELECT DATE_FORMAT(dt.first_ym, '%b-%y') AS `Acq. Month`, 
		                                   COUNT(DISTINCT c2.master_id) AS `Acq. MIDs` ";
		                                   
		// Looping to generate SELECT columns for subsequent month intervals
		$counter = 0;
		$zeroth_month_done = false; // to be used in the case of order_count and order_total 
		while ($counter < $months) {
			
			$f = $counter + 1;
			$t = $counter + $month_interval;
			
			// 'master_id'   : To check retention based on unique master_id(s) ordering
			// if cohort_type is of master_id, we will start from +$month_interval M onwards.
			if ( $cohort_type === 'master_id' ) {
				
				$this->outer_select_sql .= " , IF(dt.first_ym + INTERVAL " . $f . " MONTH <= CONCAT(DATE_FORMAT(CURRENT_DATE(), '%Y-%m'),'-01'), 
                                                  COUNT(DISTINCT 
				                                        IF(CONCAT(DATE_FORMAT(o2.date_added, '%Y-%m'),'-01') >= (dt.first_ym + INTERVAL " . $f . " MONTH) 
				                                          AND CONCAT(DATE_FORMAT(o2.date_added, '%Y-%m'),'-01') <= (dt.first_ym + INTERVAL " . $t . " MONTH)
				                                          , c2.master_id, NULL)), 
                                                  '') AS `+" . $t . " M` ";
			
			} elseif ( $cohort_type === 'order_count' ) { // 'order_count' : To check retention based on total order counts of ordering master_id(s)
				
				// Handling the very first month (0th month) numbers
				if ( !$zeroth_month_done ) {
				
				    $this->outer_select_sql .= " , COUNT(DISTINCT 
				                                           IF(CONCAT(DATE_FORMAT(o2.date_added, '%Y-%m'),'-01') = dt.first_ym 
				                                              , o2.order_id, NULL)) AS `+0 M` ";
				    $zeroth_month_done = true;
				}
				
				$this->outer_select_sql .= " , IF(dt.first_ym + INTERVAL " . $f . " MONTH <= CONCAT(DATE_FORMAT(CURRENT_DATE(), '%Y-%m'),'-01'),  
                                                  COUNT(DISTINCT 
				                                        IF(CONCAT(DATE_FORMAT(o2.date_added, '%Y-%m'),'-01') >= (dt.first_ym + INTERVAL " . $f . " MONTH) 
				                                           AND CONCAT(DATE_FORMAT(o2.date_added, '%Y-%m'),'-01') <= (dt.first_ym + INTERVAL " . $t . " MONTH)
				                                           , o2.order_id, NULL)), 
                                                  '') AS `+" . $t . " M` ";
				
				
			} elseif ( $cohort_type === 'order_total' ) { // 'order_total' : To check retention based on total order value (GMV) of ordering master_id(s)
				
				// Handling the very first month (0th month) numbers
				if ( !$zeroth_month_done ) {
				
				    $this->outer_select_sql .= " , SUM(IF(CONCAT(DATE_FORMAT(o2.date_added, '%Y-%m'),'-01') = dt.first_ym 
				                                          , osub2.total, 0)) AS `+0 M` ";
				    $zeroth_month_done = true;
				}
				
				$this->outer_select_sql .= " , IF(dt.first_ym + INTERVAL " . $f . " MONTH <= CONCAT(DATE_FORMAT(CURRENT_DATE(), '%Y-%m'),'-01'),   
                                                  SUM(IF(CONCAT(DATE_FORMAT(o2.date_added, '%Y-%m'),'-01') >= (dt.first_ym + INTERVAL " . $f . " MONTH) 
				                                         AND CONCAT(DATE_FORMAT(o2.date_added, '%Y-%m'),'-01') <= (dt.first_ym + INTERVAL " . $t . " MONTH)
				                                      , osub2.total, 0)), 
                                                  '') AS `+" . $t . " M` ";
				
			}
			
			// Incrementing the counter
			$counter += $month_interval;
		}
	}
	
	
	/*
	 * Function to generate full SQL string from all the 
	 * elements such as SELECT, FROM/JOIN, WHERE, GROUP BY and HAVING.
	 * @author Madhur Bhaiya, 2019
	 */
	private function generateSQL() : string {
		
		// preparing Full SQL
		$sql = $this->outer_select_sql;
		$sql .= " FROM ( " .  $this->inner_sql . " ) AS dt ";
		$sql .= $this->outer_from_join_sql;
		if ( !empty($this->outer_where) ) {
			$outer_where_sql = ' WHERE ' . implode(' AND ', $this->outer_where);
			$sql .= $outer_where_sql;
		}
		$sql .= $this->outer_group_by_sql;
		$sql .= $this->outer_order_by_sql;
		
		return $sql;		
	}



    // Private SQL related 
    private $inner_from_join = array(); // FROM and JOIN elements of Inner query
    private $inner_from_join_sql = '';
    
    private $outer_from_join = array(); // FROM and JOIN elements of Outer query
    private $outer_from_join_sql = '';
    
    private $inner_select_sql = ''; // SELECT sql of Inner query
    private $outer_select_sql = ''; // SELECT sql of Outer query
    
    private $inner_where     = array(); // WHERE elements of Inner query
    private $outer_where     = array(); // WHERE elements of Outer query
    
    private $inner_group_by_sql  = ''; // GROUP BY sql of Inner query
    private $outer_group_by_sql  = ''; // GROUP BY sql of Outer query
    
    private $inner_having    = array(); // HAVING elements of Inner query
    
    private $outer_order_by_sql  = ''; // ORDER BY sql of Outer query

    private $inner_sql = ''; // Complete Inner SQL

} // end class ModelReportCohort
