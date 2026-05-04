<?php
class ModelLocalisationHsnCode extends Model {
	
	public function add($data){
		
		$sql = "INSERT INTO " . DB_PREFIX . "hsn
				SET hsn_code = '" . $this->db->escape($data['hsn_code']) . "',
					hsn_description = '" . $this->db->escape($data['hsn_description']) . "',
					date_added = NOW(), 
					modification_history  = '',
					tax_class_id = '" . (int)($data['tax_class_id'])  . "'";
		$this->db->query($sql);	
	}
	
	public function edit($id, $data){
		$modification = array();
		$sql = "SELECT hsn_code, 
					   hsn_description,
					   tax_class_id,
					   modification_history 
			    FROM ". DB_PREFIX. "hsn 
			    WHERE id = ". (int)$id ."";
		$data_query = $this->db->query($sql);

		if(!empty($data_query->row['modification_history'])) {
			$modification = unserialize($data_query->row['modification_history']);
			unset($data_query->row['modification_history']);
			$modification[] = $data_query->row;

		} else {
			unset($data_query->row['modification_history']);
			$modification = array(
				'0' => $data_query->row
			);
			$modification = $modification;
		}
		
		$sql = "UPDATE " . DB_PREFIX . "hsn
				SET hsn_code = '" . $this->db->escape($data['hsn_code']) . "',
					hsn_description = '" . $this->db->escape($data['hsn_description']) . "',
					date_added = NOW(), 
					tax_class_id = '" . (int)($data['tax_class_id'])  . "',
					modification_history  = '". $this->db->escape(serialize($modification)) ."'
				WHERE id = " . (int)$id . "";

		$this->db->query($sql);	
	}
	
	public function delete($id){
		$sql = "DELETE FROM " . DB_PREFIX . "hsn WHERE id = " . (int)$id ."";
		$this->db->query($sql);
	}
	
	public function getTotalHSNCodes($data) {
		
		$sql = "SELECT COUNT(id) as total FROM " . DB_PREFIX . "hsn";
			
		if (!empty($data['filter_description'])) {
            $sql .= " WHERE MATCH(hsn_description) 
					  AGAINST('". $this->db->escape($data['filter_description']) ."')";
        } else {
			$sql .= " WHERE 1=1 ";
		}
        
		if (!empty($data['filter_hsn_code'])) {
            $sql .= " AND hsn_code LIKE '%" . $this->db->escape($data['filter_hsn_code']) . "%'";
        }
		
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	
	public function getHSNCodes($data = array()) {
	
		$sql = "SELECT * FROM " . DB_PREFIX . "hsn";
		
		$sort_data = array(
			'hsn_code',
            'hsn_description',
		);
		
		if (!empty($data['filter_description'])) {
            $sql .= " WHERE MATCH(hsn_description) 
					  AGAINST('". $this->db->escape($data['filter_description']) ."')";
        } else {
			$sql .= " WHERE 1=1 ";
		}
        
		if (!empty($data['filter_hsn_code'])) {
            $sql .= " AND hsn_code LIKE '%" . $this->db->escape($data['filter_hsn_code']) . "%'";
        } 

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id";
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
	
	public function getHSNCodeInfo($hsn_id) {
		
		if(isset($hsn_id)) {
			$query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "hsn WHERE id = ". (int)($hsn_id)."");
			return $query->row;
		}
		
		
	}
	
}
