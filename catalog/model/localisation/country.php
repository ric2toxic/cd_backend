<?php
class ModelLocalisationCountry extends Model {
	public function getCountry($country_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");

		return $query->row;
	}

	public function getCountries() {
		$country_data = $this->cache->get('country.status');

		if (!$country_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' ORDER BY name ASC");

			$country_data = $query->rows;

			$this->cache->set('country.status', $country_data);
		}

		return $country_data;
	}
	
	public function getCountryByCode($country_code = 'IN') {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $country_code . "' AND status = '1'");

		return $query->row;
	}

    public function getCountryIdByName($name) {
        $sql = "SELECT country_id FROM " . DB_PREFIX . "country WHERE LOWER(name) like '" . strtolower($name) . "%' AND status = '1' LIMIT 0, 1";
        $query = $this->db->query($sql);

        if ($query->num_rows > 0) {
            return $query->row['country_id'];
        } else {
            return 0;
        }
    }
}