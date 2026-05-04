<?php
class MsOrderData extends Model {
	/** orders **/
	public function getOrdersStats($data = array(), $sort = array()) {

        $amount_query = "SELECT SUM(op.transfer_price_per_piece * op.quantity * op.piece_in_set) as total_amount ";
        $order_query = "SELECT COUNT(DISTINCT o.order_id) as total_order ";

        $sql = "FROM `" . DB_PREFIX . "order` o
			    INNER JOIN `" . DB_PREFIX . "suborder` osub ON osub.order_id = o.order_id
                INNER JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
			    WHERE op.seller_id = '" . (int)$data['seller_id'] . "'
			      AND osub.order_status_id > 0 ";

		if ( !empty($data['order_status']) ) {
		   $sql .= " AND osub.order_status_id IN (" . $this->db->escape(implode(',', $data['order_status'])) . ")";
		}

		if ( !empty($data['stores']) ) {
		   $sql .= " AND o.store_id IN (" . $this->db->escape(implode(',', $data['stores'])) . ")";
		}

		$order_query .= $sql;
		$order_res = $this->db->query($order_query);

		$result = array();
		$result['total_order'] = isset($order_res->row['total_order']) ? (int)$order_res->row['total_order'] : 0;

		if ( !empty($sort['order_by']) and !empty($sort['order_way']) ) {
		   $sql .= " ORDER BY " . $sort['order_by'] . " " . $sort['order_way'];
		}

		if ( isset($sort['limit']) and isset($sort['offset']) ) {
		   $sql .= " LIMIT " . (int)$sort['offset'].', '.(int)($sort['limit']);
		}

		$amount_query .= $sql;
		$amount_res = $this->db->query($amount_query);
		$result['total_amount'] = isset($amount_res->row['total_amount']) ? (float)$amount_res->row['total_amount'] : 0;

		return $result;
	}

	public function getOrderTotal($order_id, $data) {
		/* SELECT SUM(seller_net_amt) as 'total_amt',
				  SUM(store_commission_pct) as 'total_pct',
				  SUM(store_commission_flat) as 'total_flat' */
		$sql = "SELECT SUM(transfer_price_per_piece * piece_in_set * quantity) as 'total'
				FROM `" . DB_PREFIX . "order_product` mopd
				WHERE order_id = " . (int)$order_id
				. (isset($data['seller_id']) ? " AND mopd.seller_id =  " .  (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	public function getOrderData($data = array()) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "order_product
				WHERE 1 = 1"
				. (isset($data['product_id']) ? " AND product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['order_id']) ? " AND order_id =  " .  (int)$data['order_id'] : '');

		$res = $this->db->query($sql);

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	public function getOrderProducts($data) {
		$sql = "SELECT p.image,
                       op.product_id,
                       op.name,
                       op.comment,
                       op.seller_sku,
                       op.quantity,
                       op.piece_in_set,
                       op.transfer_price_per_piece,
                       op.seller_input_tax,
                       op.store_sales,
                       op.seller_cst
				FROM " . DB_PREFIX . "order_product op
				INNER JOIN " . DB_PREFIX . "product p ON op.product_id = p.product_id 
				WHERE 1 = 1"
				. (isset($data['order_id']) ? " AND op.order_id = " . (int)$data['order_id'] : '')
                . (isset($data['suborder_id']) ? " AND op.suborder_id = '" . $this->db->escape($data['order_id']) . "'" : '')
				. (isset($data['seller_id']) ? " AND op.seller_id =  " . (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);

		return ($res->num_rows ? $res->rows : false);
	}

	/**
	* This function returns all the orders with a given status as array
	* @author Parth Gupta
	* @param array of order status id
	* @return Array of all orders with the order status
	*/
	public function getAllOrders($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';

		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		   $sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*,"
				.  "SUM(transfer_price_per_piece) as total, SUM(quantity) as number_of_sets, SUM(op.transfer_price_per_piece * op.piece_in_set * op.quantity) as total_amount, "
				." op.order_no as order_no
		FROM `" . DB_PREFIX . "order_product` op
		INNER JOIN `" . DB_PREFIX . "order` o
		USING (order_id) WHERE o.store_id IN (".WSB_STORES_ID .") "
		. (isset($data['order_status']) && $data['order_status'] ? " AND o.order_status_id IN  (" .  $this->db->escape(implode(',', $data['order_status'])) . ")" : '')

		. $wFilters

		. " GROUP BY order_id HAVING 1 = 1 "

		. $hFilters

		. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
		. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");

		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		return $res->rows;
	}

	public function getSellerStoreCustomers(){
		$sql = "SELECT * FROM ".DB_PREFIX."customer" ;

		return $this->db->query($sql)->rows;
	}

	public function getLatestOrders($seller_id, $limit=5) {

		$sql = "SELECT o.order_id,
		               o.order_no,
                       op.suborder_id,
		               osub.order_status_id,
		               o.date_added
		        FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id
		        INNER JOIN " . DB_PREFIX . "order_product op ON op.order_id = o.order_id
		        WHERE osub.order_status_id > 2
		          AND op.seller_id = '" . (int)$seller_id . "'
		        GROUP BY o.order_id
		        ORDER BY o.date_added DESC
		        LIMIT 0, " . (int)$limit;
		$query = $this->db->query($sql);
		$result = array();
        $data = array();
		foreach ($query->rows as $order) {

			$status_shown_to_seller ='';

			switch ($order['order_status_id']) {
				case 9:
				case 16:
					$status_shown_to_seller = "Pickup Requested";
					break;
				default:
					$status_shown_to_seller = "Pickup Done";
					break;
				}

                $sql  = "SELECT sum(transfer_price_per_piece * piece_in_set * quantity) as purchase_value ";
                $sql .=    "FROM ".DB_PREFIX."order_product oop ";
                $sql .=    "WHERE oop.order_id = '".(int)$order['order_id']."' AND ";
                $sql .=          "oop.suborder_id = '".$this->db->escape($order['suborder_id'])."' AND ";
                $sql .=          "oop.seller_id = '".(int)$seller_id."'";
                $purchase_value = $this->db->query($sql);
                $purchase_value = (float)$purchase_value->row['purchase_value'];

				$data[] = array(
						'order_id' => $order['order_id'],
                        'suborder_id' => $order['suborder_id'],
						'order_no' => $order['order_no'],
						'status' => $status_shown_to_seller,
						'order_date' =>  date($this->language->get('date_format_short'), strtotime($order['date_added'])),
						'total' => $this->currency->format($purchase_value, $this->config->get('config_currency'))
				);
			}

		return $data;
	}

	public function getOrdersOfSellerStores($data = array(), $sort = array(), $cols = array()) {

		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				o.order_id,
                o.order_no,
                osub.order_status_id,
                o.firstname,
                o.lastname,
                o.email,
                o.telephone,
                o.shipping_address_1,
                o.shipping_address_2,
                o.shipping_city,
                o.shipping_zone,
                o.shipping_postcode,
                o.shipping_country,
                osub.date_added,
                osub.total FROM `" . DB_PREFIX . "order` o
                INNER JOIN `" . DB_PREFIX . "suborder` osub ON osub.order_id = o.order_id
				WHERE 1=1"
			. (isset($data['order_status']) && $data['order_status'] ? " AND osub.order_status_id NOT IN  (" .  $this->db->escape(implode(',', $data['order_status'])) . ")" : '')

			. (isset($data['stores']) && $data['stores'] ? " AND o.store_id IN (" .  $this->db->escape(implode(',', $data['stores'])) . ")" : '');

		if(isset($data['serching_filter']['order_filter']) && !empty($data['serching_filter']['order_filter'])){
			$sql.= " AND o.order_no LIKE '%" .$data['serching_filter']['order_filter']. "%'";
		}
		if(isset($data['serching_filter']['customer_filter']) && !empty($data['serching_filter']['customer_filter'])){
			$sql.= " AND (o.firstname LIKE '%".$data['serching_filter']['customer_filter']."%' OR o.lastname LIKE '%".$data['serching_filter']['customer_filter']."%')";
		}
		if(isset($data['serching_filter']['customer_number_filter']) && !empty($data['serching_filter']['customer_number_filter'])){
			$sql.= " AND (o.telephone LIKE '%".$data['serching_filter']['customer_number_filter']."%')";
		}
		if(isset($data['serching_filter']['customer_address_filter']) && !empty($data['serching_filter']['customer_address_filter'])){
			$sql.= " AND (o.shipping_address_1 LIKE '%".$data['serching_filter']['customer_address_filter']."%' OR
							o.shipping_address_2 LIKE '%".$data['serching_filter']['customer_address_filter']."%' OR
							o.shipping_city LIKE '%".$data['serching_filter']['customer_address_filter']."%' OR
							o.shipping_zone LIKE '%".$data['serching_filter']['customer_address_filter']."%' OR
							o.shipping_postcode LIKE '%".$data['serching_filter']['customer_address_filter']."%' OR
							o.shipping_country LIKE '%".$data['serching_filter']['customer_address_filter']."%')";
		}

		if(isset($data['serching_filter']['status_filter']) && !empty($data['serching_filter']['status_filter'])){
			$sql.= " AND osub.order_status_id = (SELECT order_status_id FROM oc_order_status WHERE name LIKE '%".$data['serching_filter']['status_filter']."%' AND language_id = '1')";
		}
		if(isset($data['serching_filter']['date_filter']) && !empty($data['serching_filter']['date_filter'])){
			$sql.= " AND osub.date_added LIKE '%".$data['serching_filter']['date_filter']."%'";
		}


			$sql.= " GROUP BY o.order_id HAVING 1 = 1 "
			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");

		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		return $res->rows;
	}

	public function getPickupRequestedData($data = array(), $sort = array(), $cols = array()) {

		$sql = "SELECT
        				SQL_CALC_FOUND_ROWS
        				o.order_id,
                        osub.order_status_id,
        			    o.order_no as order_no,
                        o.date_added,
                        oop.suborder_id,
                        oop.seller_id
				FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id
				INNER JOIN " . DB_PREFIX . "order_product oop ON oop.order_id = o.order_id
				WHERE oop.seller_id = " . (int)$data['seller_id']

			. (isset($data['order_status']) && $data['order_status'] ? " AND osub.order_status_id IN  (" .  $this->db->escape(implode(',', $data['order_status'])) . ")" : ' AND osub.order_status_id != 0 ')

			. (isset($data['stores']) && $data['stores'] ? " AND o.store_id IN (" .  $this->db->escape(implode(',', $data['stores'])) . ")" : '');

		if(isset($data['serching_filter']['order_filter']) && !empty($data['serching_filter']['order_filter'])){
			$sql.= " AND o.order_no LIKE '%" .$data['serching_filter']['order_filter']."%'";
		}
		if(isset($data['serching_filter']['date_filter']) && !empty($data['serching_filter']['date_filter'])){
			$sql.= " AND o.date_added LIKE '%".$data['serching_filter']['date_filter']."%'";
		}

			$sql.= " GROUP BY o.order_id HAVING 1 = 1 "

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");

		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
        foreach ( $res->rows as $key => $value) {
            $sql  = "SELECT sum(transfer_price_per_piece * piece_in_set * quantity) as total_amount ";
            $sql .=    "FROM ".DB_PREFIX."order_product oop ";
            $sql .=    "WHERE oop.order_id = '".(int)$value['order_id']."' AND ";
            $sql .=          "oop.suborder_id = '".$this->db->escape($value['suborder_id'])."' AND ";
            $sql .=          "oop.seller_id = '".(int)$value['seller_id']."'";
            $total_amount = $this->db->query($sql);
            $total_amount = (float)$total_amount->row['total_amount'];
            $res->rows[$key]['total_amount'] = $total_amount;

            $sql  = "SELECT invoice_no FROM ".DB_PREFIX."suborder ";
            $sql .=     "WHERE order_id = '".(int)$value['order_id']."' AND ";
            $sql .=           "suborder_id = '".$this->db->escape($value['suborder_id'])."' ";
            $result = $this->db->query($sql);
            if( $result->num_rows > 0 ){
                $res->rows[$key]['invoice_no'] = (int)$result->row['invoice_no'];
            }
        }
		return $res->rows;
	}
}
?>
