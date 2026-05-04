<?php
class ModelReportCustomer extends Model {
	public function getTotalCustomersByDay() {
		$customer_data = array();

		for ($i = 0; $i < 24; $i++) {
			$customer_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");

		foreach ($query->rows as $result) {
			$customer_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getTotalCustomersByWeek() {
		$customer_data = array();

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$order_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('w', strtotime($result['date_added']))] = array(
				'day'   => date('D', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getTotalCustomersByMonth() {
		$customer_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$customer_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getTotalCustomersByYear() {
		$customer_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$customer_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");
		foreach ($query->rows as $result) {
			$customer_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}


	public function getCredit($data = array()) {
		$sql = "SELECT ct.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, SUM(ct.amount) AS total FROM `" . DB_PREFIX . "customer_transaction` ct LEFT JOIN `" . DB_PREFIX . "customer` c ON (ct.customer_id = c.customer_id) WHERE 1=1 ";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(ct.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(ct.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " GROUP BY ct.customer_id ORDER BY total DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCredit($data = array()) {
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_transaction`";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCustomersOnline($data = array()) {
		$sql = "SELECT co.ip, co.customer_id, co.url, co.referer, co.date_added FROM " . DB_PREFIX . "customer_online co LEFT JOIN " . DB_PREFIX . "customer c ON (co.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_ip'])) {
			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "co.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode)." AND co.store_id IN (".WSB_STORES_ID .")";
        } else {
            $sql .= " WHERE co.store_id IN (".WSB_STORES_ID .")";
        }

		$sql .= " ORDER BY co.date_added DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCustomersOnline($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_online` co LEFT JOIN " . DB_PREFIX . "customer c ON (co.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_ip'])) {
			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "co.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode)." AND co.store_id IN (".WSB_STORES_ID .")";
		}else{
			$sql .= " WHERE co.store_id IN (".WSB_STORES_ID .")";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}


	public function getSellerActivities($data = array()){
		$sql = "SELECT * FROM `" . DB_PREFIX . "seller_change_log` ";

		$implode = array();

		if (!empty($data['filter_product'])) {
			$implode[] = "product LIKE '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_nickname'])) {
			$implode[] = "nickname LIKE '" . $this->db->escape($data['filter_nickname']) . "'";
		}

		if (!empty($data['filter_date'])) {
			$implode[] = "modified LIKE '" . $this->db->escape($data['filter_date']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;

	}

	public function getTotalSellerActivities($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "seller_change_log`";

		$implode = array();


		if (!empty($data['filter_product'])) {
			$implode[] = "product LIKE '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_nickname'])) {
			$implode[] = "nickname LIKE '" . $this->db->escape($data['filter_nickname']) . "'";
		}

		if (!empty($data['filter_date'])) {
			$implode[] = "modified LIKE '" . $this->db->escape($data['filter_date']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getCustomersWhoGivenFirstOrderInDateRange($start_date, $end_date, $signup_in_date_range=0) {

	    if ($signup_in_date_range == 1) {
	        $check_signup_in_date_range = "  AND (DATE(`c`.`date_added`) >= DATE('".$start_date."')) 
	                                        AND (DATE(`c`.`date_added`) <= DATE('".$end_date."') )";
        } else {
            //Signed up before date range but first order in date range
            $check_signup_in_date_range = " AND (DATE(`c`.`date_added`) <= DATE('".$start_date."')) ";
        }

	    $q = "
	            SELECT
                    `c`.`customer_id` AS `customer_id`
                FROM
                ((`".DB_PREFIX."customer` `c`
                    JOIN `".DB_PREFIX."order` `o` ON ((`o`.`customer_id` = `c`.`customer_id`)))
                    JOIN `".DB_PREFIX."suborder` `osub` ON ((`osub`.`order_id` = `o`.`order_id`)))
                WHERE
                (
                    (`osub`.`order_status_id` > 0)
                    AND (`osub`.`order_status_id` <> 2)
                   ".$check_signup_in_date_range."
                    
                    AND (DATE(`o`.`date_added`) >= DATE('".$start_date."') )
                    AND (DATE(`o`.`date_added`) <= DATE('" . $end_date . "'))
                    AND `o`.`franchise_id` = 0 
                )
                GROUP BY `o`.`customer_id`
                    
	    ";

        $query = $this->db->singleFieldquery($q, 'customer_id');

        return $query->rows;




    }

    public function getTotalCustomer($start_date='', $end_date='') {
        $where = array();
        $where_string = '';

        if( $start_date != '') {
            $where[] = " DATE(c.date_added) >= DATE('".$start_date."')";
        }

        if( $end_date != '') {
            $where[] = " DATE(c.date_added) <= DATE('".$end_date."')";
        }

        if (count($where) > 0) {
            $where_string = " AND ". implode(" AND ", $where);

        }
        $q = "SELECT
                    count(`c`.`customer_id`) as total
                FROM
                   `".DB_PREFIX."customer` `c`
                WHERE 1 = 1
             ". $where_string;

        $query = $this->db->query($q);


        return (int)$query->row['total'];
    }


	/**
	 * avgCustomerAcquisition
	 * Get customers and Orders which are signup and order placed in a spacific time period and thst order is tag to our tele or field sales person
	 * @param 	start_date 	DATE-TIME
	 * @param 	end_date   	DATE-TIME
	 * @return  result 		ARRAY
	 * @author 	Garvit Joshi
	 */
	public function avgCustomerAcquisition($start_date, $end_date) {
	
		$q = "SELECT c.customer_id, o.order_id, ss.role, ss.staff_id, ss.active_status 
				FROM ".DB_PREFIX."customer c 
				INNER JOIN ".DB_PREFIX."order o ON (c.customer_id = o.customer_id) 
				INNER JOIN ".DB_PREFIX."suborder so ON (o.order_id = so.order_id) 
				INNER JOIN ".DB_PREFIX."order_sales_staff oss ON (o.order_id = oss.order_id) 
				INNER JOIN ".DB_PREFIX."sales_staff ss ON (oss.sales_staff_id = ss.staff_id) 
				WHERE o.date_added BETWEEN STR_TO_DATE('".$start_date."', '%Y-%m-%d') AND STR_TO_DATE('".$end_date."', '%Y-%m-%d') 
				AND c.date_added BETWEEN STR_TO_DATE('".$start_date."', '%Y-%m-%d') AND STR_TO_DATE('".$end_date."', '%Y-%m-%d') 
				AND so.order_status_id != 0 
				AND o.franchise_id = 0 
				GROUP BY o.order_id ASC";
		$result = $this->db->query($q)->rows;
		return $result;
	}

	public function getSalesStaff($start_date, $end_date) {
		$staff['total_sales_staff'] 	= $this->db->query("SELECT staff_id FROM ".DB_PREFIX."sales_staff ss WHERE date_added < STR_TO_DATE('".$end_date."', '%Y-%m-%d')")->rows;
		$staff['active_sales_staff'] 	= $this->db->query("SELECT staff_id, role FROM ".DB_PREFIX."sales_staff ss WHERE active_status = 1 AND date_added < STR_TO_DATE('".$end_date."', '%Y-%m-%d') ORDER BY role ASC")->rows;
		return $staff;
	}

	public function getNumberOfRegistrations($start_date, $end_date) {
		$sql = "SELECT count(customer_id) as total_customers 
				FROM oc_customer 
				WHERE date_added BETWEEN STR_TO_DATE('".$start_date."', '%Y-%m-%d') AND STR_TO_DATE('".$end_date."', '%Y-%m-%d')";
		$data['total_registration'] = $this->db->query( $sql )->row;
		$sql = "SELECT count(customer_id) as total_customers 
				FROM oc_customer 
				WHERE date_added BETWEEN STR_TO_DATE('".$start_date."', '%Y-%m-%d') AND STR_TO_DATE('".$end_date."', '%Y-%m-%d')
				AND app_version != 0";
		$data['total_app_registration'] = $this->db->query( $sql )->row;
		return 	$data;
	}

	/**
	* Method for get Life time Order count of customer with total amount
	* @param : $customer_id : array of customer Id
	* @return : total orders with total orders amount of corresponding customer id
	* @author : vikas, 2018
	*/
	public function getLifeTimeOrdersCountOfCustomers($customer_ids){
		$customer_ids = array_unique(explode(',', implode(',', $customer_ids)));
		$sql = "SELECT o.customer_id, 
				   count(DISTINCT o.order_id) as total_orders,
				   SUM(osub.total) as total_orders_amt,
				   MAX(IF( osub.order_status_id !=2 , DATE(osub.date_added), NULL )) as order_date
				FROM ".DB_PREFIX."order o
				INNER JOIN ".DB_PREFIX."suborder osub
				  ON osub.order_id = o.order_id
				WHERE o.customer_id IN( " . implode(',', $customer_ids) . " )
				  AND osub.order_status_id > 0
				  AND o.store_id IN (0,2,9)
				  AND o.franchise_id = 0 
				GROUP BY o.customer_id  ";
		$query = $this->db->query($sql);

		$results = array();
		if( $query->num_rows ){

			array_walk($query->rows, function(&$v, $k) use(&$results){
				$results[$v['customer_id']]['total_orders']     = $v['total_orders'];
				$results[$v['customer_id']]['total_orders_amt'] = $v['total_orders_amt'];
				$results[$v['customer_id']]['last_order_date']  = $v['order_date'];
			});

			$remaining_customer_ids = array_diff(
				                        $customer_ids,
				                        array_column($query->rows, 'customer_id')
				                      );

			foreach ($remaining_customer_ids as $customer_id) {
				$results[$customer_id]['total_orders']     = 0 ; 
				$results[$customer_id]['total_orders_amt'] = 0;
				$results[$customer_id]['last_order_date']  = '';
			}
		}	

		return $results;	
	}

	/**
	* Method for get Life time total return order of customer and with return amount
	* @param : $customer_id : array of customer Id
	* @return : total order return with total return amount of corresponding customer id
	* @author : vikas, 2018
	*/
	public function getLifeTimeReturnsCountOfCustomers($customer_ids){
		$customer_ids = array_unique(explode(',', implode(',', $customer_ids)));
		$sql = "SELECT o.customer_id, 
					   count(DISTINCT ocn.order_id) as total_orders,
					   SUM(ocn.credit_note_amount) as credit_note_amount
				FROM ".DB_PREFIX."credit_note ocn
				INNER JOIN ".DB_PREFIX."order o
				  ON o.order_id = ocn.order_id
				INNER JOIN ".DB_PREFIX."suborder osub
				  ON osub.order_id = o.order_id
				WHERE o.customer_id IN ( " . implode(',', $customer_ids) . " ) 
				  AND o.franchise_id = 0 
				  AND o.store_id IN (0,2,9)
				  AND osub.suborder_id = ocn.suborder_id 
				  AND osub.order_status_id > 0
				  AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 
				  AND osub.invoice_no > 0 
				  AND ocn.credit_note_status = 1
				  AND ocn.is_cod_failed != 1
				GROUP BY o.customer_id";
		$query = $this->db->query($sql);

		$results = array();
		if( $query->num_rows ){
			array_walk($query->rows, function(&$v, $k) use(&$results){
				$results[$v['customer_id']]['total_orders'] 	 = $v['total_orders'];
				$results[$v['customer_id']]['credit_note_amount']= $v['credit_note_amount'];
			});

			$remaining_customer_ids = array_diff($customer_ids, array_column($query->rows, 'customer_id'));

			foreach ($remaining_customer_ids as $customer_id) {
				$results[$customer_id]['total_orders'] 	 	= 0;
				$results[$customer_id]['credit_note_amount']= 0;
			}
		}	

		return $results;	
	}

	/**
	* Method for get Life time total COD failed count of customer with total COD failed Amount
	* @param : $customer_id : array of customer Id
	* @return : total order of Cod Failed with total COD Failed amount of corresponding customer id
	* @author : vikas, 2018
	*/
	public function getLifeTimeCODFailedCountOfCustomers($customer_ids){
		$customer_ids = array_unique(explode(',', implode(',', $customer_ids)));
		$sql = "SELECT o.customer_id, 
					   count(DISTINCT o.order_id) as total_orders,
					   SUM(osub.total) as total_amount
				FROM ".DB_PREFIX."order o
				INNER JOIN ".DB_PREFIX."suborder osub
				  ON osub.order_id = o.order_id
				WHERE o.customer_id IN( " . implode(',', $customer_ids) . " )
				  AND osub.order_status_id IN (8, 12)
				  AND o.store_id IN (0,2,9)
				  AND o.franchise_id = 0 
				GROUP BY o.customer_id ";

		$query = $this->db->query($sql);

		$results = array();
		if( $query->num_rows ){
			array_walk($query->rows, function(&$v, $k) use(&$results){
				$results[$v['customer_id']]['total_orders'] 	= $v['total_orders'];
				$results[$v['customer_id']]['codFailed_amount']	= $v['total_amount'];
			});

			$remaining_customer_ids = array_diff($customer_ids, array_column($query->rows, 'customer_id'));

			foreach ($remaining_customer_ids as $customer_id) {
				$results[$customer_id]['total_orders'] 	 	= 0;
				$results[$customer_id]['codFailed_amount']	= 0;
			}
		}	

		return $results;	
	}

	/**
	* Method for get Life time total Delivery issues of customer with total Issue Amount
	* @param : $customer_id : array of customer Id
	* @return : total order Delivery Issue with total order delivery amount of corresponding customer id
	* @author : vikas, 2018
	*/
	public function getLifeTimeDeliveryIssuesCountOfCustomers($customer_ids){
		$customer_ids = array_unique(explode(',', implode(',', $customer_ids)));
		$sql = "SELECT 
					   inner_data.customer_id, 
					   SUM(inner_data.total) as total_amount, 
					   count(distinct order_id) as total_orders 
				FROM 
					(SELECT o.customer_id, 
					        o.order_id, 
					        osub.suborder_id, 
					        osub.total 
					FROM ".DB_PREFIX."order o 
					INNER JOIN ".DB_PREFIX."suborder osub 
					  ON osub.order_id = o.order_id 
					INNER JOIN ".DB_PREFIX."order_history oh 
					  ON oh.order_id = o.order_id
					WHERE o.customer_id IN( " . implode(',', $customer_ids) . " )
					  AND oh.order_status_id = 17
					  AND o.store_id IN (". WSB_STORES_ID .")
					  AND o.franchise_id = 0
					  AND oh.suborder_id = osub.suborder_id 
					GROUP BY osub.suborder_id 
					) as inner_data 
				GROUP BY inner_data.customer_id ";
		$query = $this->db->query($sql);

		$results = array();
		if( $query->num_rows ){
			array_walk($query->rows, function(&$v, $k) use(&$results){
				$results[$v['customer_id']]['total_orders'] 	= $v['total_orders'];
				$results[$v['customer_id']]['deliveryIssueAmt']	= $v['total_amount'];
			});

			$remaining_customer_ids = array_diff($customer_ids, array_column($query->rows, 'customer_id'));

			foreach ($remaining_customer_ids as $customer_id) {
				$results[$customer_id]['total_orders'] 		= 0;
				$results[$customer_id]['deliveryIssueAmt']	= 0;
			}
		}	

		return $results;	
	}

	/**
	* Method for get Life time total Cancelled Order of customer with Total cancelled Order Amount
	* @param : $customer_id : array of customer Id
	* @return : total Cancelled Orders with total cancelled orders amount of corresponding customer id
	* @author : vikas, 2018
	*/
	public function getLifeTimeCountCancelledOrdersOfCustomers($customer_ids){
		$customer_ids = array_unique(explode(',', implode(',', $customer_ids)));
		$sql = "SELECT tbl.customer_id, COUNT(DISTINCT tbl.order_id) as total_orders, SUM(tbl.total) as total_amount 
		        FROM ( 
		               SELECT o.customer_id, o.order_id, osub.total
				       FROM ".DB_PREFIX."order o
				       INNER JOIN ".DB_PREFIX."suborder osub
				               ON osub.order_id = o.order_id 
				       INNER JOIN ".DB_PREFIX."order_product oop 
				               ON oop.order_id = osub.order_id 
				       WHERE o.customer_id IN( " . implode(',', $customer_ids) . " )
				         AND osub.order_status_id = 2
				         AND o.store_id IN (0,2,9) 
				         AND o.franchise_id = 0 
				         AND osub.suborder_id = oop.suborder_id 
				         AND oop.edit_type != 'SELLER_NOT_SUPPLIED' 
				       GROUP BY osub.suborder_id 
				     ) AS tbl 
                WHERE 1 = 1 
				GROUP BY tbl.customer_id ";

		$query = $this->db->query($sql);

		$results = array();
		if( $query->num_rows ){
			array_walk($query->rows, function(&$v, $k) use(&$results){
				$results[$v['customer_id']]['total_orders'] 	= $v['total_orders'];
				$results[$v['customer_id']]['cancelled_amount']	= $v['total_amount'];
			});

			$remaining_customer_ids = array_diff($customer_ids, array_column($query->rows, 'customer_id'));

			foreach ($remaining_customer_ids as $customer_id) {
				$results[$customer_id]['total_orders'] 	 	= 0;
				$results[$customer_id]['cancelled_amount']	= 0;
			}
		}	

		return $results;
    }
    
     /**
	   * Public Method to get order stats for a given customer id
	   * @param: $customer_id Int
	   * @return $order stats Array
	   * @author: Devendra, August 2019
	*/
    public function getOrderStats(int $customer_id) : array {
        $all_related_ids = Customer::getRelatedIdsByCustomerId($this->db, (int)$customer_id);
        $all_related_ids_array = explode(',', $all_related_ids);
        $total_orders_arr		= $this->getLifeTimeOrdersCountOfCustomers($all_related_ids_array);
        $return_orders_arr 		= $this->getLifeTimeReturnsCountOfCustomers($all_related_ids_array);
        $cod_failed_orders_arr 	= $this->getLifeTimeCODFailedCountOfCustomers($all_related_ids_array);
        $delivery_issue_orders_arr = $this->getLifeTimeDeliveryIssuesCountOfCustomers($all_related_ids_array);
        $cancelled_orders_arr     = $this->getLifeTimeCountCancelledOrdersOfCustomers($all_related_ids_array);           

        $total_orders         = 0;
        $total_orders_amount     = 0;
        
        $return_orders       = 0;
        $return_orders_amount     = 0; 

        $cod_failed_orders      = 0;
        $cod_failed_orders_amount  = 0;
        
        $delivery_issue_orders = 0;
        $delivery_issue_orders_amount = 0;

        $cancelled_orders     = 0;
        $cancelled_orders_amount  = 0;
        
        $last_order_date_arr = array();
        

        foreach ($all_related_ids_array as $cust_id) {
            $total_orders         += !empty($total_orders_arr[$cust_id]['total_orders']) 
                                                ? $total_orders_arr[$cust_id]['total_orders'] : 0 ;
            $total_orders_amount     += !empty($total_orders_arr[$cust_id]['total_orders_amt']) 
                                                ? $total_orders_arr[$cust_id]['total_orders_amt'] : 0 ;
            
            $return_orders        += !empty($return_orders_arr[$cust_id]['total_orders']) 
                                                ? $return_orders_arr[$cust_id]['total_orders'] : 0 ;
            $return_orders_amount     += !empty($return_orders_arr[$cust_id]['credit_note_amount']) 
                                                ? $return_orders_arr[$cust_id]['credit_note_amount'] : 0 ;
            
            $cod_failed_orders     += !empty($cod_failed_orders_arr[$cust_id]['total_orders']) 
                                                ? $cod_failed_orders_arr[$cust_id]['total_orders'] : 0 ;
            $cod_failed_orders_amount  += !empty($cod_failed_orders_arr[$cust_id]['codFailed_amount']) 
                                                ? $cod_failed_orders_arr[$cust_id]['codFailed_amount'] : 0 ;
            
            $delivery_issue_orders += !empty($delivery_issue_orders_arr[$cust_id]['total_orders']) 
                                                ? $delivery_issue_orders_arr[$cust_id]['total_orders'] : 0 ;
            $delivery_issue_orders_amount += !empty($delivery_issue_orders_arr[$cust_id]['deliveryIssueAmt']) 
                                                ? $delivery_issue_orders_arr[$cust_id]['deliveryIssueAmt'] : 0 ;                                                       
            $cancelled_orders     += !empty($cancelled_orders_arr[$cust_id]['total_orders']) 
                                                ? $cancelled_orders_arr[$cust_id]['total_orders'] : 0 ;
            $cancelled_orders_amount  += !empty($cancelled_orders_arr[$cust_id]['cancelled_amount']) 
                                                ? $cancelled_orders_arr[$cust_id]['cancelled_amount'] : 0 ;

            $last_order_date_arr[]        = !empty($total_orders_arr[$cust_id]['last_order_date']) 
                                                ? $total_orders_arr[$cust_id]['last_order_date'] : '' ;                                                       
        }
        
        // order amount in percentage
        $return_orders_percentage           = (($return_orders_amount) 
                                                ? round(100*(float)$return_orders_amount/(float)$total_orders_amount, 2 )
                                                : 0 ) . '%';
        $cod_failed_orders_percentage        = (($cod_failed_orders_amount) 
                                                ? round(100*(float)$cod_failed_orders_amount/(float)$total_orders_amount, 2 )
                                                : 0 ) . '%';
        $delivery_issue_orders_percentage    = (($delivery_issue_orders_amount) 
                                                ? round(100*(float)$delivery_issue_orders_amount/(float)$total_orders_amount, 2 )
                                                : 0 ) . '%';
        $cancelled_orders_percentage  = (($cancelled_orders_amount) 
                                                ? round(100*(float)$cancelled_orders_amount/(float)$total_orders_amount, 2 )
                                                : 0 ) . '%';
        
        $order_stats = array(
            'total_orders' => array(
                'total_orders' => $total_orders,
                'total_orders_amt' => abbreviateInIndianCurrency($total_orders_amount)
            ),
            'return_orders' => array(
                'total_orders' => $return_orders,
                'total_orders_amt' => $return_orders_amount,
                'total_order_amt_per' => $return_orders_percentage
            ),
            'cod_failed_orders' => array(
                'total_orders' => $cod_failed_orders,
                'total_orders_amt' => $cod_failed_orders_amount,
                'total_order_amt_per' => $cod_failed_orders_percentage
            ),
            'delivery_issue_orders' => array(
                'total_orders' => $delivery_issue_orders,
                'total_orders_amt' => $delivery_issue_orders_amount,
                'total_order_amt_per' => $delivery_issue_orders_percentage
            ),
            'cancelled_orders' => array(
                'total_orders' => $cancelled_orders,
                'total_orders_amt' => $cancelled_orders_amount,
                'total_order_amt_per' => $cancelled_orders_percentage
            ),
            'last_order_date' => max($last_order_date_arr),
            'all_related_customer_ids' => $all_related_ids
        );

        return $order_stats;
    }
}
