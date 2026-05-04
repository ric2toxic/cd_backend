<?php
class ModelInventoryProductToModerate extends Model {

	public function getProducts($data = array()) {
		$sql = "SELECT p.product_id,
					   p.image,	
					   p.model,
					   p.price,
					   p.seller_tax,
					   p.commission,
					   p.piece_in_set,
					   p.weight, 
					   pd.name, 
					   pd.set_description  
				FROM " . DB_PREFIX . "product p
				INNER JOIN " . DB_PREFIX . "product_description pd 
				  ON pd.product_id = p.product_id 
				     AND pd.language_id = " . (int)$this->config->get('config_language_id') . " 
				WHERE p.status = 2 
				ORDER BY p.product_id ";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		return $this->db->query($sql)->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(*) AS total
				FROM " . DB_PREFIX . "product p 
				WHERE p.status = 2";
		$query = $this->db->query($sql);
		return (int)($query->row['total'] ?? 0);
	}

	// product moderate approve
	public function updateProductModerateApprove($product_id = array()){
		if(is_array($product_id)){
			$sql = "UPDATE oc_product SET status = 1 WHERE product_id IN (". implode(',',$product_id) . ")";
			$this->db->query($sql);
			
		}else{
			$sql = "UPDATE oc_product SET status = 1 WHERE product_id IN (".$product_id .")";
			$this->db->query($sql);			
			
			
		}
	}

	// product moderate reject
	public function updateProductModerateReject($product_id = array()){
		if(is_array($product_id)){
			$sql = "UPDATE oc_product SET status = 4 WHERE product_id IN (". implode(',',$product_id) . ")";
			$this->db->query($sql);

		}else{
			$sql = "UPDATE oc_product SET status = 4 WHERE product_id IN (".$product_id .")";
			$this->db->query($sql);	
			
		}
		
	}


}
