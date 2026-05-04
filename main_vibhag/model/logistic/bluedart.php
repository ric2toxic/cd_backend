<?php
class ModelLogisticBluedart extends Model {
	
	/**
    * Public function addBluedartPincodes to add pincodes
    * @param  Array data
    * @return true
    * @author MSA, August 2019
    */
	public function addBluedartPincodes($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "bluedart_pincodes` 
				(`cscrcd`, `pincode`, `city`, `bdel_loc`, 
				 `state`, `cod`, `prepaid`, `mode`
				) 
				VALUES 
			   ";
		foreach ($data as $values){
			$query = implode(",", $values);
			$this->db->query($sql.$query);
		}
		return true;
	}

	/**
    * Public function requiredCSVfileColumns to send list of required columns in pincode CSV file
    * @param  void
    * @return array
    * @author MSA, August 2019
    */
	public function requiredCSVfileColumns()
	{
		return ['cscrcd', 'pincode', 'city', 'bdel_loc', 
				'state', 'cod', 'prepaid', 'mode'
			   ];
	}

	/**
    * Public function truncateBluedartPincodesTable to truncate table data
    * @param  void
    * @return void
    * @author MSA, August 2019
    */
	public function truncateBluedartPincodesTable(){
		$sql = "TRUNCATE TABLE `" . DB_PREFIX . "bluedart_pincodes`";
		$this->db->query($sql);
		return true;
	}

	/**
    * Public function getFilterPincodes to filter pincodes data
    * @param  array $data [filters]
    * @return array pincodes list
    * @author MSA, August 2019
    */
	public function getFilterPincodes($data= array()){
		$pincodes_list = explode(",", trim($data['filter_pincodes']));
		$where = "";
		if(count($pincodes_list) > 1) {
			$pincodes_list = implode(",", array_filter( array_map( "trim", $pincodes_list ) ) );
			$where = " `pincode` IN (" . $this->db->escape(trim($pincodes_list)) . ")  ";	
		}else{
			$where = " `pincode`LIKE '%" . $this->db->escape(trim($data['filter_pincodes'])) . "%'  ";
		}
		$sql = "SELECT cscrcd, pincode, city, bdel_loc,state, cod, prepaid, mode, status
				FROM `" . DB_PREFIX . "bluedart_pincodes` 
				WHERE " . $where ;
		$query = $this->db->query($sql);
		return $query->rows;
	}

	/**
    * Public function updatePincodeStatus to toggle pincodes status
    * @param  array $pincodes 
    * @return void
    * @author MSA, August 2019
    */
	public function updatePincodeStatus(array $pincodes)
	{
		if(!empty($pincodes)) 
		{
			foreach ($pincodes as $key => $value) {
				$bluedart_pincode = explode("|", $value);
				$pincode = $bluedart_pincode[0] ?? '';
				$mode    = $bluedart_pincode[1] ?? '';
				$sql = "UPDATE `" . DB_PREFIX . "bluedart_pincodes`
						SET 
							status = IF(status=1, 0, 1)
						WHERE
							pincode = '".$this->db->escape($pincode)."'
							AND 
							mode = '".$this->db->escape($mode)."'
						";
				$this->db->query($sql);
			}
		}
	}

}
