<?php
class ModelLogisticConnectIndia extends Model {
	public function addPincodes($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "connect_india_pincodes` (`state`,`district`,`pincode`,`comment`) VALUES ";
                foreach ($data as $values){
			$query = implode(",", $values);
                        $this->db->query($sql.$query);
		}
		return true;
	}
	public function truncatePincodesTable(){
		$sql = "TRUNCATE TABLE `" . DB_PREFIX . "connect_india_pincodes`";
		$this->db->query($sql);
		return true;
	}

	public function getFilterPincodes($data= array()){
		$sql = "SELECT * FROM `" . DB_PREFIX . "connect_india_pincodes` WHERE `pincode`LIKE '%" . $this->db->escape(trim($data['filter_pincode'])) . "%' ";
                $query = $this->db->query($sql);
		return $query->rows;
	}
}