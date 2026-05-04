<?php
class ModelLogisticDotzot extends Model {
	
	/**
    * Public function addDotZotPincodes to add pincodes
    * @param  Array $data
    * @return true
    * @author MSA, August 2019
    */
	public function addDotZotPincodes( $data ) {
		$sql = "INSERT INTO `" . DB_PREFIX . "dotzot_pincodes` 
				(`product`,`pincode`,`city`,`state`,`region`,
				 `prepaid`,`cod`,`reverse_pickup`,`pickup`,`created`
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
		return ['Sr No','product','pincode','city',
				'state','region','prepaid',
				'cod','reverse_pickup',
				'pickup'
			   ];
	}

	/**
    * Public function truncateDotZotPincodesTable to truncate table data
    * @param  void
    * @return void
    * @author MSA, August 2019
    */
	public function truncateDotZotPincodesTable(){
		$sql = "TRUNCATE TABLE `" . DB_PREFIX . "dotzot_pincodes`";
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
		$sql = "SELECT product,pincode,city,state,region,prepaid,
					   cod,reverse_pickup,pickup,status
			   FROM `" . DB_PREFIX . "dotzot_pincodes` 
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
				$dotzot_pincode = explode("|", $value);
				$pincode = $dotzot_pincode[0] ?? '';
				$product = $dotzot_pincode[1] ?? '';
				$sql = "UPDATE `" . DB_PREFIX . "dotzot_pincodes`
						SET 
							status = IF(status=1, 0, 1)
						WHERE
							pincode = '".$this->db->escape($pincode)."'
							AND 
							product = '".$this->db->escape($product)."'
						";
				$this->db->query($sql);
			}
		}
	}
}
