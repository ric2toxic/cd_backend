<?php
class ModelLogisticDelhivery extends Model {
	
	/**
    * Public function addDelhiveryPincodes to add pincodes
    * @param  Array $data
    * @return true
    * @author MSA, August 2019
    */
	public function addDelhiveryPincodes( $data ) {
		$sql = "INSERT INTO `" . DB_PREFIX . "delhivery_pincodes` 
				(`pincode`,`prepaid`,`pickup`,`repl`,`cod`,
				 `dispatch_center`,`city`,`state`,`state_code`,
				 `sort_code`,`value_capping`
				) VALUES ";
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
		return ['pincode','prepaid','pickup','repl',
				'cod','dispatch_center','city','state',
				'state_code','sort_code','value_capping'
			   ];
	}

	/**
    * Public function truncateDelhiveryPincodesTable to truncate table data
    * @param  void
    * @return void
    * @author MSA, August 2019
    */
	public function truncateDelhiveryPincodesTable(){
		$sql = "TRUNCATE TABLE `" . DB_PREFIX . "delhivery_pincodes`";
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
		$sql = "SELECT pincode,prepaid,pickup,repl,cod,dispatch_center,city,state,
					   state_code,sort_code,value_capping,status
				 FROM `" . DB_PREFIX . "delhivery_pincodes` 
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
				$sql = "UPDATE `" . DB_PREFIX . "delhivery_pincodes`
						SET 
							status = IF(status=1, 0, 1)
						WHERE
							pincode = '".$this->db->escape($value)."'
						";
				$this->db->query($sql);
			}
		}
	}

}
