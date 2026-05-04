<?php
class ModelReportSale extends Model {
	public function getTotalSales($data = array()) {
		$sql = "SELECT SUM(osub.total) AS total
                FROM ". DB_PREFIX ."suborder osub 
                INNER JOIN " . DB_PREFIX . "order o ON o.order_id = osub.order_id  
                WHERE osub.order_status_id > '0' 
                  AND osub.order_status_id != '2'
                AND o.store_id IN (". WSB_STORES_ID .") 
                AND o.franchise_id = 0 
                ";

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrdersByCountry() {
		$query = $this->db->query("SELECT COUNT(DISTINCT o.order_id) AS total, 
								   		  SUM(o.total) AS amount, 
								   		  c.iso_code_2 
								   FROM `" . DB_PREFIX . "order` o 
								   LEFT JOIN `" . DB_PREFIX . "country` c 
								     ON (o.payment_country_id = c.country_id)
								   INNER JOIN `" . DB_PREFIX . "suborder` osub    
								     ON (osub.order_id = o.order_id)
								   WHERE osub.order_status_id > '0' 
								     AND o.store_id IN (".WSB_STORES_ID .") 
								     AND o.franchise_id = 0   
								   GROUP BY o.payment_country_id");

		return $query->rows;
	}

	public function getTotalOrdersByDay() {
		$implode = array();

		$order_data = array();

		for ($i = 0; $i < 24; $i++) {
			$order_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(DISTINCT o.order_id) AS total, 
										  HOUR(o.date_added) AS hour 
								   FROM `" . DB_PREFIX . "order` o
								   INNER JOIN " . DB_PREFIX . "suborder osub
								     ON (osub.order_id = o.order_id) 
								   WHERE (osub.order_status_id > 0 && osub.order_status_id != 2)
								     AND o.store_id IN (".WSB_STORES_ID .") 
								     AND DATE(o.date_added) = DATE(NOW()) 
								     AND o.franchise_id = 0 
								   GROUP BY HOUR(o.date_added) 
								   ORDER BY o.date_added ASC");

		foreach ($query->rows as $result) {
			$order_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}

		return $order_data;
	}

	public function getTotalOrdersByWeek() {
		$implode = array();

		$order_data = array();

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$order_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(DISTINCT o.order_id) AS total, 
										  o.date_added 
								   FROM `" . DB_PREFIX . "order` o
								   INNER JOIN " . DB_PREFIX . "suborder osub
								     ON (osub.order_id = o.order_id) 
								   WHERE (osub.order_status_id > 0 && osub.order_status_id != 2)
								     AND o.store_id IN (".WSB_STORES_ID .")  
								     AND DATE(o.date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') 
								     AND o.franchise_id = 0 
								     GROUP BY DAYNAME(o.date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('w', strtotime($result['date_added']))] = array(
				'day'   => date('D', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $order_data;
	}

	public function getTotalOrdersByMonth() {
		$implode = array();

		$order_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$order_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(DISTINCT o.order_id) AS total, 
								     	  o.date_added 
								   FROM " . DB_PREFIX . "order o 
								   INNER JOIN " . DB_PREFIX . "suborder osub
								     ON (osub.order_id = o.order_id) 
								   WHERE (osub.order_status_id > 0 && osub.order_status_id != 2)
								   	 AND o.store_id IN (".WSB_STORES_ID .")  
								   	 AND DATE(o.date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' 
								   	 AND o.franchise_id = 0 
								   GROUP BY DATE(o.date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $order_data;
	}

	public function getTotalOrdersByYear() {
		$implode = array();

		$order_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$order_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(DISTINCT o.order_id) AS total, 
										  o.date_added 
								   FROM `" . DB_PREFIX . "order` o 
								   INNER JOIN " . DB_PREFIX . "suborder osub
								     ON (osub.order_id = o.order_id) 
								   WHERE (osub.order_status_id > 0 && osub.order_status_id != 2)
								     AND o.store_id IN (".WSB_STORES_ID .")  
								     AND YEAR(o.date_added) = YEAR(NOW()) 
								     AND o.franchise_id = 0 
								   GROUP BY MONTH(o.date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $order_data;
	}

	public function getOrders($data = array()) {
        
        // Talk to Madhur before trying to modify this query
        
        $sql = '';
        
        if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'day';
		}
        
        switch($group) {
            
            case 'day';
                $sql .= "SELECT DATE(o.date_added) AS date_start,
						        DATE(o.date_added) AS date_end, ";
				break;
			case 'week':
				$sql .= "SELECT GREATEST(DATE(o.date_added) + INTERVAL (1-DAYOFWEEK(o.date_added)) DAY, DATE('" . $this->db->escape($data['filter_date_start']) . "')) AS date_start, 
                                LEAST(DATE(o.date_added) + INTERVAL (7-DAYOFWEEK(o.date_added)) DAY, DATE('" . $this->db->escape($data['filter_date_end']) . "')) AS date_end, ";
				break;
			case 'month':
				$sql .= "SELECT GREATEST(DATE(o.date_added) + INTERVAL (1-DAYOFMONTH(o.date_added)) DAY, DATE('" . $this->db->escape($data['filter_date_start']) . "')) AS date_start, 
						        LEAST(LAST_DAY(o.date_added), DATE('" . $this->db->escape($data['filter_date_end']) . "')) AS date_end, ";
				break;
			case 'year':
				$sql .= "SELECT GREATEST(DATE(o.date_added) + INTERVAL (1-DAYOFYEAR(o.date_added)) DAY, DATE('" . $this->db->escape($data['filter_date_start']) . "')) AS date_start, 
                                LEAST(LAST_DAY(o.date_added) + INTERVAL (12-MONTH(o.date_added)) MONTH, DATE('" . $this->db->escape($data['filter_date_end']) . "')) AS date_end, ";
				break;
            
        }
        
		$sql .= " COUNT(DISTINCT o.order_id) AS orders, 
                  COUNT(osub.suborder_id) AS suborders, 
                  COUNT(DISTINCT CASE WHEN osub.order_status_id = 1 THEN o.order_id ELSE NULL END) AS pending_orders, 
                  COUNT(CASE WHEN osub.order_status_id = 1 THEN osub.suborder_id ELSE NULL END) AS pending_suborders, 
                  SUM(ROUND(o.currency_value*osub.total/o.live_currency_conversion_rate,2)) AS total,
                  SUM(CASE WHEN osub.order_status_id = 1 THEN ROUND(o.currency_value*osub.total/o.live_currency_conversion_rate,2) ELSE 0 END) AS pending_total 
				FROM " . DB_PREFIX . "order o 
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id ";

        if ($data['filter_order_status_id'] == -2) {
            $sql .= " WHERE osub.order_status_id > 0 AND osub.order_status_id <> 2";
		} elseif (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE osub.order_status_id = " . (int)$data['filter_order_status_id'];
		} else {
			$sql .= " WHERE osub.order_status_id > 0";
		}
		/*
		* this condition returns without franchise id orders
		*/
		$sql .= " AND o.franchise_id = 0 ";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND o.date_added >= '" . $this->db->escape(trim($data['filter_date_start']) . " 00:00:00") . "' ";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND o.date_added <= '" . $this->db->escape(trim($data['filter_date_end']) . " 23:59:59") . "' ";
		}

        $sql .= " AND o.store_id IN (".WSB_STORES_ID .") 
                  AND o.stock_transfer = 0 ";
                  
        // GROUP BY
        $sql .= " GROUP BY date_start, date_end ";
        // ORDER BY
        $sql .= " ORDER BY date_start DESC ";
        
		$query = $this->db->query($sql);
        
        return $query->rows;
	}

	public function getTotalOrders($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added)) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added)) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added)) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}

        $sql .= " INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id ";
        
		if ($data['filter_order_status_id'] == -2) {
            $sql .= " WHERE osub.order_status_id > '0' AND osub.order_status_id != '2'";
        } elseif (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE osub.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE osub.order_status_id > '0'";
		}
		/*
		* this condition returns without franchise id orders
		*/
		$sql .= " AND o.franchise_id = 0 ";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " AND o.store_id IN (".WSB_STORES_ID .") AND o.stock_transfer = 0";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTaxes($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (ot.order_id = o.order_id) WHERE ot.code = 'tax' AND o.store_id IN (".WSB_STORES_ID .")";
		/*
		* this condition returns without franchise id orders
		*/
		$sql .= " AND o.franchise_id = 0 ";
		
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
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

	public function getTotalTaxes($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}

		$sql .= " LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'tax' AND o.store_id IN (".WSB_STORES_ID .")";
		/*
		* this condition returns without franchise id orders
		*/
		$sql .= " AND o.franchise_id = 0 ";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
