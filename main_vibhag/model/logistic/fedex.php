<?php
class ModelLogisticFedex extends Model {
	
	/**
    * Public function addDotZotPincodes to add pincodes
    * @param  Array $data
    * @return true
    * @author MSA, August 2019
    */
	public function addFedexPincodes( $data ) {
		$sql = "INSERT INTO `" . DB_PREFIX . "fedex_pincodes` 
				(`pincode`,`city`,`state`,`oda_opa_or_reg`,
				 `intl_services`,`dom_service`,
				 `cod_serviceable`
				) VALUES ";
		foreach ($data as $values){
			$query = implode(",", $values);
			$this->db->query($sql.$query);
		}
		return true;
	}

	/**
    * Public function truncateFedexPincodesTable to truncate table data
    * @param  void
    * @return void
    * @author MSA, August 2019
    */
	public function truncateFedexPincodesTable(){
		$sql = "TRUNCATE TABLE `" . DB_PREFIX . "fedex_pincodes`";
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
		$sql = "SELECT * FROM `" . DB_PREFIX . "fedex_pincodes` 
				WHERE " . $where ;
		$query = $this->db->query($sql);
		return $query->rows;
	}

	/**
    * Public function escape_list to escape string value
    * @param  string $list
    * @return string $list
    * @author MSA, August 2019
    */
	public function escape_list($list)
    {
        if(is_array($list)) {
            array_walk($list, function(&$value, &$key){
                $value = $this->db->escape($value);
            });
        }else{
            $list = $db->escape($list);
        }
        return $list;
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
				$sql = "UPDATE `" . DB_PREFIX . "fedex_pincodes`
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
