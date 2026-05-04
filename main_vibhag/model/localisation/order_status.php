<?php
class ModelLocalisationOrderStatus extends Model {
	public function addOrderStatus($data) {
		foreach ($data['order_status'] as $language_id => $value) {
			if (isset($order_status_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_status SET order_status_id = '" . (int)$order_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_status SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$order_status_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('order_status');
	}

	public function editOrderStatus($order_status_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "'");

		foreach ($data['order_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_status SET order_status_id = '" . (int)$order_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('order_status');
	}

	public function deleteOrderStatus($order_status_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "'");

		$this->cache->delete('order_status');
	}

	public function getOrderStatus($order_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getOrderStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			// if(!empty($data['post_actions']) && !in_array( $this->user->getId(), explode(',', ADMIN_IDS) ) ){
			// 	$sql .= " AND order_status_id IN (".$data['post_actions'].") ";
			// }

			if(!empty($data['post_actions'])){
				$sql .= " AND order_status_id IN (".$data['post_actions'].") ";
			}

			$sql .= " ORDER BY name";

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
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
		} else {
			$order_status_data = $this->cache->get('order_status.' . (int)$this->config->get('config_language_id'));

			if (!$order_status_data) {
				$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$order_status_data = $query->rows;

				$this->cache->set('order_status.' . (int)$this->config->get('config_language_id'), $order_status_data);
			}

			return $order_status_data;
		}
	}

	public function getOrderStatusDescriptions($order_status_id) {
		$order_status_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "'");

		foreach ($query->rows as $result) {
			$order_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $order_status_data;
	}

	public function getTotalOrderStatuses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
	public function getCourierPartners(){

		$sql = "SELECT *  
				FROM " . DB_PREFIX . "courier_partners
				WHERE
					status = 1
				";

		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function getOrderStatusPostActions(int $order_status_id, 
											  int $invoice_no, 
											  int $buyer_invoice_id, 
											  int $language_id = 1
											)
	{
		$sql = "
				SELECT 
					GROUP_CONCAT(order_status_id) as post_actions
				FROM " . DB_PREFIX . "order_status
				WHERE FIND_IN_SET(order_status_id, 
									(
									SELECT IF( post_actions IS NOT NULL, 
												CONCAT(post_actions,',',".$order_status_id."),
												".$order_status_id."
											) as post_actions
									FROM " . DB_PREFIX . "order_status
									WHERE
										order_status_id = '".(int)$order_status_id."'
										AND
										language_id  = '".(int)$language_id."'
									)
								)  
					AND
					language_id  = '".(int)$language_id."'
				";
		if($invoice_no == 0 && $buyer_invoice_id == 0) {
			$sql .= " AND is_invoice_required = 0 ";
		}
		$query = $this->db->query($sql);
		if($query->num_rows) {
			if(empty($query->row['post_actions'])){
				return $order_status_id;
			}else{
				return $query->row['post_actions'];
			}
		}
	}
}