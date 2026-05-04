<?php
class ModelLocalisationLocation extends Model {
	public function addLocation($data) {
            
                $sql = "INSERT INTO " . DB_PREFIX . "location SET "
                        . "name = '" . $this->db->escape($data['name']) . "', "
                        . "location_type = '" . $this->db->escape($data['location_type']) . "', "
                        . "address = '" . $this->db->escape($data['address']) . "', "
                        . "city = '" . $this->db->escape($data['city']) . "', "
                        . "postcode = '" . $this->db->escape($data['postcode']) . "', "
                        . "state = '" . $this->db->escape($data['state']) . "', "
                        . "country = '" . $this->db->escape($data['country']) . "', "
                        . "telephone = '" . $this->db->escape($data['telephone']) . "', "
                        . "show_on = '" . $this->db->escape($data['show_on']) . "', "
                        . "geocode = '" . $this->db->escape($data['geocode']) . "', "
                        . "direction_url = '" . $this->db->escape($data['direction_url']) . "', "
                        . "status = '" . $this->db->escape($data['status']) . "', "
                        . "image = '" . $this->db->escape($data['image']) . "', "
                        . "timing = '" . $this->db->escape($data['timing']) . "', "
                        . "sort_order = '" . $this->db->escape($data['sort_order']) . "', "
                        . "comment = '" . $this->db->escape($data['comment']) . "' "; 
                $this->db->query($sql);
	}

	public function editLocation($location_id, $data) {
            
                $sql = "UPDATE " . DB_PREFIX . "location SET "
                        . "name = '" . $this->db->escape($data['name']) . "', "
                        . "location_type = '" . $this->db->escape($data['location_type']) . "', "
                        . "address = '" . $this->db->escape($data['address']) . "', "
                        . "city = '" . $this->db->escape($data['city']) . "', "
                        . "city = '" . $this->db->escape($data['city']) . "', "
                        . "postcode = '" . $this->db->escape($data['postcode']) . "', "
                        . "state = '" . $this->db->escape($data['state']) . "', "
                        . "country = '" . $this->db->escape($data['country']) . "', "
                        . "telephone = '" . $this->db->escape($data['telephone']) . "', "
                        . "show_on = '" . $this->db->escape($data['show_on']) . "', "
                        . "geocode = '" . $this->db->escape($data['geocode']) . "', "
                        . "direction_url = '" . $this->db->escape($data['direction_url']) . "', "
                        . "status = '" . $this->db->escape($data['status']) . "', "
                        . "image = '" . $this->db->escape($data['image']) . "', "
                        . "timing = '" . $this->db->escape($data['timing']) . "', "
                        . "sort_order = '" . $this->db->escape($data['sort_order']) . "', "
                        . "comment = '" . $this->db->escape($data['comment']) . "' ,"
                        . "date_modified = NOW() "." " 
                        . "WHERE location_id = '" . (int)$location_id . "'";
                $this->db->query($sql);
	}

	public function deleteLocation($location_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "location WHERE location_id = " . (int)$location_id);
	}

	public function getLocation($location_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "location WHERE location_id = '" . (int)$location_id . "'");

		return $query->row;
	}

	public function getLocations($data = array()) {
		$sql = "SELECT location_id, name, address, city, postcode, state, country, sort_order, status FROM " . DB_PREFIX . "location";

		$sort_data = array(
			'name',
			'address',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
		}

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
	}

	public function getTotalLocations() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "location");

		return $query->row['total'];
	}
}
