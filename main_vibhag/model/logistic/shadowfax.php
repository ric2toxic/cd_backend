<?php
class ModelLogisticShadowfax extends Model {
	public function addShadowfaxPincodes( $fields , $data, $jsonUser = '') {
		$sql = "INSERT INTO `" . DB_PREFIX . "shadowfax_pincodes` (`pincode`,`hub`,`city`) VALUES ";
		foreach ($data as $values){
			$query = implode(",", $values);
			$this->db->query($sql.$query);
		}
		return true;
	}
	public function truncateShadowfaxPincodesTable(){
		$sql = "TRUNCATE TABLE `" . DB_PREFIX . "shadowfax_pincodes`";
		$this->db->query($sql);
		return true;
	}

	public function getFilterPincodes($data= array()){
		$sql = "SELECT * FROM `" . DB_PREFIX . "shadowfax_pincodes` WHERE `pincode`LIKE '%" . $this->db->escape(trim($data['filter_customer_id'])) . "%' ";
		$query = $this->db->query($sql);
		return $query->rows;
	}
}
