<?php
class ModelLocalisationGeoZone extends Model {
	public function addGeoZone($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");

		$geo_zone_id = $this->db->getLastId();

		if (isset($data['zone_to_geo_zone'])) {
			foreach ($data['zone_to_geo_zone'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
			}
		}

		$this->cache->delete('geo_zone');
	}

	public function editGeoZone($geo_zone_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_modified = NOW() WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		if (isset($data['zone_to_geo_zone'])) {
			foreach ($data['zone_to_geo_zone'] as $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
			}
		}

		$this->cache->delete('geo_zone');
	}

	public function deleteGeoZone($geo_zone_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		$this->cache->delete('geo_zone');
	}

	public function getGeoZone($geo_zone_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		return $query->row;
	}

	public function getGeoZones($data = array()) { 
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "geo_zone";

			$sort_data = array(
				'name',
				'description'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
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
		} else { 
			$geo_zone_data = $this->cache->get('geo_zone');
			
			if (!$geo_zone_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name ASC");

				$geo_zone_data = $query->rows;

				$this->cache->set('geo_zone', $geo_zone_data);
			}

			return $geo_zone_data;
		}
	}

	public function getGeoZonesNew($data = array()) {
		//echo "<pre>"; print_r($data['search']); die;
		if ($data) {
			$sql = "SELECT DISTINCT (gz.geo_zone_id) as geo_zone_id, gz.name as name, gz.description as description, gz.date_modified as date_modified, gz.date_added as date_added, gz.store_id as store_id, gz.display_name_for_admin as display_name_for_admin FROM " . DB_PREFIX . "geo_zone gz 
					INNER JOIN " . DB_PREFIX . "zone_to_geo_zone zz 
					ON (gz.geo_zone_id = zz.geo_zone_id) 
					INNER JOIN " . DB_PREFIX . "country cz 
					ON (zz.country_id = cz.country_id) 
					INNER JOIN " . DB_PREFIX . "zone sz
					ON (cz.country_id = sz.country_id)
					WHERE sz.name LIKE '%".$data['search']."%' 
					OR cz.name LIKE '%".$data['search']."%' 
					OR gz.name LIKE '%".$data['search']."%'
					OR gz.description LIKE '%".$data['search']."%'";

			$sort_data = array(
				'name',
				'description'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY gz.name";
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
			//echo $sql;
			$query = $this->db->query($sql);

			return $query->rows;
		} else {
			$geo_zone_data = $this->cache->get('geo_zone');

			if (!$geo_zone_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name ASC");

				$geo_zone_data = $query->rows;

				$this->cache->set('geo_zone', $geo_zone_data);
			}

			return $geo_zone_data;
		}
	}

	public function getTotalGeoZones() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "geo_zone");

		return $query->row['total'];
	}

	public function getZoneToGeoZones($geo_zone_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		return $query->rows;
	}

	public function getTotalZoneToGeoZoneByGeoZoneId($geo_zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		return $query->row['total'];
	}

	public function getTotalZoneToGeoZoneByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalZoneToGeoZoneByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}
}
