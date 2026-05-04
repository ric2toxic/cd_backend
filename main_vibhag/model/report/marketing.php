<?php
class ModelReportMarketing extends Model {
	public function getMarketing($data = array()) {
		$sql = "SELECT m.marketing_id, m.name AS campaign, m.code, m.clicks AS clicks, 
                      (SELECT COUNT(DISTINCT order_id) FROM `" . DB_PREFIX . "order` o1 
                       WHERE o1.marketing_id = m.marketing_id 
                       AND o1.franchise_id = 0 ";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o1.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o1.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o1.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o1.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " AND o1.store_id IN (".WSB_STORES_ID .")) AS `orders`, (SELECT SUM(total) FROM `" . DB_PREFIX . "order` o2 WHERE o2.marketing_id = m.marketing_id";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o2.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o2.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o2.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o2.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " AND o2.store_id IN (".WSB_STORES_ID .") GROUP BY o2.marketing_id) AS `total` FROM `" . DB_PREFIX . "marketing` m ORDER BY m.date_added ASC";

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

	public function getTotalMarketing($data = array()) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "marketing`");

		return $query->row['total'];
	}


	public function getExclusiveProducts(){
		$sql = "SELECT COUNT(product_id) AS count
				FROM ".DB_PREFIX."product 
				WHERE exclusive = 'exclusive' or exclusive = 'both'";
		$result = $this->db->query($sql);

		if($result->num_rows){
			return $result->row;	
		}else{
			return $result->row['count'] = 0;	
		}	
	}

	public function getExclusiveTotalCartProducts($data = ''){
		$sql = "SELECT cart_data FROM ".DB_PREFIX."customer_cart ";
		if( !empty($data) ){
			$sql .= " WHERE date_modified >= '".$data['start_date']."' AND date_modified <= '".$data['end_date']."'";
		}
		$result = $this->db->query($sql);
		$exclusive_cart = array();
		if($result->num_rows){
			foreach($result->rows as $value){
				$cart_data = unserialize($value['cart_data']);
				$cart_keys = array_keys($cart_data);

				for($i = 0; $i < count($cart_keys); $i++){
					$cart = base64_decode($cart_keys[$i]);
					$cart_product = unserialize($cart);
					$cart_product_id[] = $cart_product['product_id'];
				}
				$cart_product_ids = implode(",",array_unique($cart_product_id));
				
				$sql = "SELECT COUNT(product_id) AS count 
						FROM ".DB_PREFIX."product 
						WHERE exclusive = 'exclusive' 
						AND product_id IN ( ".$cart_product_ids." )";
				$total_exclusive_products = $this->db->query($sql)->row;
				if( $total_exclusive_products['count'] > 0 ){
					$exclusive_cart[] = $total_exclusive_products['count'];
				}
			}
		}
		return count($exclusive_cart);
	}

	public function getExclusiveSoldTotalProducts($data,$by){
		if(isset($data) && !empty($data)){
			$sql = "SELECT COUNT(op.exclusive) AS count
					FROM ".DB_PREFIX."order o 
					INNER JOIN ".DB_PREFIX."order_product oop ON ( oop.order_id = o.order_id )
					INNER JOIN ".DB_PREFIX."product op ON ( oop.product_id = op.product_id )
					WHERE DATE(o.date_added) >= DATE('" . $this->db->escape($data['start_date']) . "') 
						AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['end_date']) . "') 
						AND op.exclusive = 'exclusive' 
						AND o.franchise_id = 0 ";
			if( $by == 'store'){
				$sql .=  " AND oop.store_sales != 'No'";
			}
			$sql .= " GROUP BY o.order_id,oop.product_id ";
						 
			$result = $this->db->query($sql);
			if($result->num_rows){
				return $result->row;
			}else{
				return $result->row['count'] = 0;
			}
		}
	}

	public function getTopTenExclusiveProducts($data){
		if(isset($data) && !empty($data)){
			$sql = "SELECT oph.product_id,op.model
					FROM ".DB_PREFIX."product_hotness oph 
					INNER JOIN ".DB_PREFIX."product op ON ( op.product_id = oph.product_id )
					WHERE op.exclusive = 'exclusive'
					ORDER BY oph.hotness_points DESC LIMIT 10";
			
			$result = $this->db->query($sql);
			return $result->rows;
		}
	}
}
