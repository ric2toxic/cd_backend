<?php
class ModelModuleDealOfDay extends Model {

	public function getSellerList(){
		$query = "SELECT c.customer_id,c.firstname, s.company FROM ". DB_PREFIX ."ms_seller as s  LEFT JOIN ".DB_PREFIX."customer as c ON c.customer_id= s.seller_id  ORDER By c.customer_id ASC";
		$result = $this->db->query($query);
		return $result->rows;
	}
}






