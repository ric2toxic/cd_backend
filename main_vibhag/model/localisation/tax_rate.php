<?php
class ModelLocalisationTaxRate extends Model {
	public function addTaxRate($data) {
		
		$final_array = array();
		$last_id = $this->db->query("SELECT MAX(tax_rate_id) as tax_rate_id FROM ". DB_PREFIX ."tax_rate")->row['tax_rate_id'] + 1;

		if(isset($data['tax_rule'])) {
			foreach($data['tax_rule'] as $key=>$val) {
				if(empty(array_filter(array_values($val)))) {
					unset($data['tax_rule'][$key]);
				}
			}
		
			$final_array = array_values($data['tax_rule']);
		
			$final_array[] = array(
								'start_range' => (float)$final_array[sizeof($final_array)-1]['end_range'] + 1e-2 ,
								'end_range' => 1e10,
								'rate' => $data['tax_rate_end_range']
	
								);
			foreach($final_array as $key=>$val) {
						
				$sql = "INSERT INTO " . DB_PREFIX . "tax_rate 
                          SET 
							  tax_rate_id = ". (int)($last_id) .",
							  name = '" . $this->db->escape($data['name']) . "',  
                             `type` = '" . $this->db->escape($data['type']) . "', 
                              date_added = NOW(), 
                              date_modified = NOW(), 
                              start_range = '" .(float)($val['start_range']). "',
                              end_range   = '" .(float)($val['end_range']). "',
                              rate        = '" .(float)($val['rate']). "'";
  
                $this->db->query($sql);
			}
		} else {
			
			$final_array = array(
								'start_range' => 0,
								'end_range' => 1e10,
								'rate' => $data['tax_rate_end_range'],
								);
			
			$sql = "INSERT INTO " . DB_PREFIX . "tax_rate 
					  SET 
						 tax_rate_id = ". (int)($last_id) .",
						 name = '" . $this->db->escape($data['name']) . "',  
                         type = '" . $this->db->escape($data['type']) . "', 
                         date_added = NOW(), 
                         date_modified = NOW(), 
                         start_range = '" .(float)($final_array['start_range']). "',
                         end_range   = '" .(float)($final_array['end_range']). "',
                         rate        = '" .(float)($final_array['rate']). "'";
		
            $this->db->query($sql);
		}
	}

	public function editTaxRate($tax_rate_id, $data) {
		
		$final_array = array();
		
		if(isset($data['tax_rule'])) {
			foreach($data['tax_rule'] as $key=>$val) {
				if(empty(array_filter(array_values($val)))) {
					unset($data['tax_rule'][$key]);
				}
			}
			
			$final_array = array_values($data['tax_rule']);
		
			$final_array[] = array(
								'start_range' => (float)$final_array[sizeof($final_array)-1]['end_range'] + 1e-2 ,
								'end_range' => 1e10,
								'rate' => $data['tax_rate_end_range']
								);
		
			$sql_delete = "DELETE FROM " .DB_PREFIX ."tax_rate
						WHERE tax_rate_id = " .(int)($tax_rate_id). "";
		
			$this->db->query($sql_delete);
			
			foreach($final_array as $key=>$val) {
				
				if($key == sizeof($final_array)-1) {
					$val['end_range'] = 1e10;
				}
				
				$sql = "INSERT INTO " . DB_PREFIX . "tax_rate 
                          SET name = '" . $this->db->escape($data['name']) . "',  
                             `type` = '" . $this->db->escape($data['type']) . "', 
                              date_added = NOW(), 
                              date_modified = NOW(), 
                              start_range = '" .(float)($val['start_range']). "',
                              end_range   = '" .(float)($val['end_range']). "',
                              rate        = '" .(float)($val['rate']). "',
                              tax_rate_id = " .(int)($tax_rate_id). "";
                              
                $this->db->query($sql);
			}
		} else {
			$final_array = array();
			$final_array = array(
								'start_range' => 0 ,
								'end_range' => 1e10,
								'rate' => $data['tax_rate_end_range']
								);
			$sql_delete = "DELETE FROM " .DB_PREFIX ."tax_rate
						WHERE tax_rate_id = " .(int)($tax_rate_id). "";
		
			$this->db->query($sql_delete);
			
			$sql = "INSERT INTO " . DB_PREFIX . "tax_rate 
                          SET name = '" . $this->db->escape($data['name']) . "',  
                             `type` = '" . $this->db->escape($data['type']) . "', 
                              date_added = NOW(), 
                              date_modified = NOW(), 
                              start_range = '" .(float)($final_array['start_range']). "',
                              end_range   = '" .(float)($final_array['end_range']). "',
                              rate        = '" .(float)($final_array['rate']). "',
                              tax_rate_id = " .(int)($tax_rate_id). "";
                              
            $this->db->query($sql);
		}
	}

	public function deleteTaxRate($tax_rate_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rate WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
	}

	public function getTaxRate($tax_rate_id) {
		$query = $this->db->query("SELECT 
									tr.tax_rate_id, 
									tr.name AS name, 
									tr.type, 
									tr.date_added, 
									tr.date_modified,
									tr.start_range,
									tr.end_range,
									tr.rate
									FROM " . DB_PREFIX . "tax_rate tr
									WHERE tr.tax_rate_id = '" . (int)$tax_rate_id . "'
									ORDER BY tr.start_range");
	
		return $query->rows;
	}

	public function getTaxRates($data = array()) {
		$sql = "SELECT tr.tax_rate_id, 
					tr.name AS name, 
					tr.type, 
					tr.date_added, 
					tr.date_modified, 
					tr.start_range,
					tr.end_range,
					tr.rate
					FROM " . DB_PREFIX . "tax_rate tr";
					
		/**
		 * "SELECT CONCAT('{',
                         GROUP_CONCAT(
                               CONCAT('\"',tr.tax_rate_id,'\":{','\"tr.name\":\"', tr.name,
                                        '\",\"tr.start_range\":\"',tr.start_range,
                                        '\",\"tr.end_range\":\"',tr.end_range,
                                        '\",\"tr.rate_logic\":\"',tr.rate_logic,
                                        '\",\"tr.type\":\"',tr.type,
                                        '\",\"tr.rate\":\"',tr.rate,'\"}'
                                      )
                               ORDER BY
                               tax_rate_id
                               DESC
							),
                         '}'
                    ) as output_data
					FROM " . DB_PREFIX . "tax_rate tr";
		
		*/
		
		$sort_data = array(
			'tr.name',
			'tr.type',
			'tr.date_added',
			'tr.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY tr.name";
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

	public function getTotalTaxRates() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tax_rate");

		return $query->row['total'];
	}

	public function getTotalTaxRatesByGeoZoneId($geo_zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tax_rate WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		return $query->row['total'];
	}
}
