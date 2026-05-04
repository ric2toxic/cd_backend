 <?php
/**
*
*
* @info: Class to data process and data reterival for SOR products
*        also calculate return/replacment period for SOR OrderProduct
* @author: Nishu, 29th June 2019
*
*/
class SorOrderProduct 
{	
	// private $_sor_return_limit      = 0;
	// private $_sor_replacement_limit = 0;

	public function __construct($registry) {
		$this->registry 	= $registry;
		
		if (method_exists($registry, 'get')) {
            $this->db 		= $registry->get('db');
            $this->load 	= $registry->get('load');
        } else {
            $this->db 		= $registry->db;
            $this->load 	= $registry->load;
        }
	}

	/**
	 * @info: Public method to get SOR OrderProduct return/Replacement Period
	 * @param: Array/String $order_products
	 * @return: Array
	 * @author: Nishu, June 2019
	*/
	public function getSorPeriodByOrderProductIds($order_product_ids) {
		$data = array();
		if(!empty($order_product_ids)) {

			//If given order product ids in array form, then change it into comma seperated string 
			if(is_array($order_product_ids)){
				$order_product_ids = implode(',', $order_product_ids);
			}

			$sql = "
					SELECT
						order_product_id,
						limit_days,
						sor_type
					FROM
						". DB_PREFIX ."order_product_sor_terms
					WHERE 
						order_product_id IN (". $order_product_ids .")
			       ";
			       
			$qry = $this->db->query($sql);
			if($qry->num_rows > 0){
				$data = array_combine(
							array_column($qry->rows, 'order_product_id'), 
							array_column($qry->rows, 'limit_days')
						);
			}
		}

		return $data;
	}

}//End of Class
