<?php
class ModelRestapiCustomerwebsite extends Model {

	/**
    * method for check Eligibility Based On Order by userId and order product is also delivered
    * @author Rahul 11th June 2018
    * */
    public function checkEligibilityBasedOnOrder( int $userId ) {

    	$delivered_status=implode(',',ORDER_STATUS_CLUSTERS['delivered']);

    	$sql = "SELECT 
    				COUNT(distinct oo.order_id) AS count_delivered
				FROM 
					" . DB_PREFIX . "order oo
				INNER JOIN 
					" . DB_PREFIX . "order_history oh 
						ON oh.order_id = oo.order_id
				WHERE
			  		oo.customer_id = '" . (int)$userId . "' 
			  		AND 
			  		oh.order_status_id IN (".$this->db->escape($delivered_status).")
			  	";
    	
    	$check_delivery_query = $this->db->query( $sql );
    			
		if (1 || $check_delivery_query->row['count_delivered'] > 0 ) {

			return true;

		} else {

			return false;
		}
    }

	/**
	 * Function : addNewCustomerProduct
	 * Method to get list of all product which is order by user in 3 month and order_status_id is greater than 2
	 * Parameters : customer id in string comma saperated
	 * @author Rahul 26th June 2018
	 * Output : cutomer product list
	 * */
    public function addNewCustomerProduct( string $customer_str ){

   	    	$delivered_status=implode(',',ORDER_STATUS_CLUSTERS['delivered']);
   	
   			$this->load->model('restapi/service');
    
            $query = $this->db->query("SELECT
						  o.order_id,
						  o.order_no,
						  op.order_product_id,
						  op.price_per_piece AS price,
						  o.customer_id,
						  p.product_id,
						  p.sku,
						  op.name,
						  p.image,
						  oh.date_added,
						  os.date_added as order_date_added
						FROM
						  " . DB_PREFIX . "order o
						INNER JOIN
						  " . DB_PREFIX . "order_product op ON o.order_id = op.order_id
						INNER JOIN
						  " . DB_PREFIX . "product p ON op.product_id = p.product_id
						INNER JOIN
						  " . DB_PREFIX . "suborder os ON o.order_id = os.order_id
						INNER JOIN
						  " . DB_PREFIX . "order_history oh ON os.order_id = oh.order_id
						WHERE 
						  o.franchise_id = 0 AND
						  os.suborder_id = oh.suborder_id AND 
						  o.customer_id IN (".$this->db->escape($customer_str).") AND oh.order_status_id in($delivered_status) AND oh.date_added >= DATE_SUB(NOW(),
						  INTERVAL 3 MONTH)
						GROUP BY
						   o.customer_id, p.product_id
						ORDER BY
						  os.date_added DESC");

                $allDetails=$query->rows;

                $customerArray=array();
					foreach ($allDetails as $key => $value) {
						if(!in_array($value['customer_id'], $customerArray)){
							$customerArray[$key]=$value['customer_id'];
						}
						$getProductImages=$this->model_restapi_service->getProductImages($value['product_id']);
						$allDetails[$key]['product_all_images']=$getProductImages;
						$allDetails[$key]['slug']=$this->slugify($value['name']);
					}

					if(count($customerArray)>0){
						$customer_ids=implode(',', $customerArray);
						$this->updateCustomerWebsiteStatus($customer_ids,'1','1');
					}
                return $allDetails;
    }

	/**
	 * Function : addExistingCustomerProduct
	 * Method to get list of all product which is order by user in 6 hour and order_status_id is greater than 2
	 * Parameters : customer id in string comma saperated
	 * @author Rahul 26th June 2018
	 * Output : cutomer product list
	 * */
    public function addExistingCustomerProduct( string $customer_str ) {  
		
		$this->load->model('restapi/service');
		
		$delivered_status=implode(',',ORDER_STATUS_CLUSTERS['delivered']);
    	
    	$query = $this->db->query("SELECT
						  o.order_id,
						  o.order_no,
						  op.order_product_id,
						  op.price_per_piece AS price,
						  o.customer_id,
						  p.product_id,
						  p.sku,
						  op.name,
						  p.image,
						  oh.date_added,
						  os.date_added as order_date_added
						FROM
						  " . DB_PREFIX . "order o
						INNER JOIN
						  " . DB_PREFIX . "order_product op ON o.order_id = op.order_id
						INNER JOIN
						  " . DB_PREFIX . "product p ON op.product_id = p.product_id
						INNER JOIN
						  " . DB_PREFIX . "suborder os ON o.order_id = os.order_id
						 INNER JOIN
				  		 " . DB_PREFIX . "order_history oh ON os.order_id = oh.order_id
						WHERE 
							o.franchise_id = 0 AND
				  			os.suborder_id = oh.suborder_id AND 
						 	o.customer_id IN (".$this->db->escape($customer_str).") AND oh.order_status_id in($delivered_status) AND oh.date_added >= DATE_SUB(NOW(), INTERVAL 6 HOUR)
						GROUP BY
						  o.customer_id, p.product_id
						ORDER BY
						  os.date_added DESC");
        $allDetails=$query->rows;
			foreach ($allDetails as $key => $value) {
				$getProductImages=$this->model_restapi_service->getProductImages($value['product_id']);
				$allDetails[$key]['product_all_images']=$getProductImages;
				$allDetails[$key]['slug']=$this->slugify($value['name']);
			}
        return $allDetails;
   }

   	/**
	 * Function : updateCustomerWebsiteStatus
	 * Method to get Method to update customer has_website status 
	 * Parameters : customer id in string comma saperated
	 * @author Rahul 26th June 2018
	 * Output : cutomer product list
	 * */
   public function updateCustomerWebsiteStatus( int $customer_id, $status, $customers_str=0 ){
   	if($customers_str=='0'){
   		$query = $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `has_website`='".$this->db->escape($status)."' WHERE `customer_id`='". (int) $customer_id. "' ");
   	}else{
   		$query = $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `has_website`='".$this->db->escape($status)."' WHERE `customer_id` IN (".$this->db->escape($customer_id).")");
   	}
   	return true;
   }

   /**
	 * Function : slugify
	 * @author Rahul 26th June 2018
	 * Output : for slugify string
	 * */
   static public function slugify(string $text)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);  // transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);  // remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);  // trim
		$text = trim($text, '-');  // remove duplicate -
		$text = preg_replace('~-+~', '-', $text);  // lowercase
		$text = strtolower($text);  if (empty($text)) {
		   return 'n-a';
		}  return $text;
	}

	/**
	 * Function : getCustomerProductDetailAccordingDate
	 * Method to get list of all product which is order between given date
	 * Parameters : array queryData (customer id,start_date, end_date)
	 * @author Rahul 26th June 2018
	 * Output : cutomer product list
	 * */
    public function getCustomerProductDetailAccordingDate($queryData) { 
      	
      	$delivered_status=implode(',',ORDER_STATUS_CLUSTERS['delivered']);
    	
    	$this->load->model('restapi/service');
        
        $query = $this->db->query("SELECT
								  o.order_id,
								  o.order_no,
								  op.order_product_id,
								  op.price_per_piece AS price,
								  o.customer_id,
								  p.product_id,
								  p.sku,
								  op.name,
								  p.image,
								  oh.date_added,
								  os.date_added as order_date_added
								FROM
								  " . DB_PREFIX . "order o
								INNER JOIN
								  " . DB_PREFIX . "order_product op ON o.order_id = op.order_id
								INNER JOIN
								  " . DB_PREFIX . "product p ON op.product_id = p.product_id
								INNER JOIN
								  " . DB_PREFIX . "suborder os ON o.order_id = os.order_id
								 INNER JOIN
						  		 " . DB_PREFIX . "order_history oh ON os.order_id = oh.order_id
								WHERE 
						  			os.suborder_id = oh.suborder_id AND 
								 	o.customer_id = '".(int)$queryData['customer_id']."' AND oh.order_status_id IN(".$this->db->escape($delivered_status).") AND 
								 	oh.date_added BETWEEN '".$this->db->escape($queryData['start_date'])."' AND '".$this->db->escape($queryData['end_date'])."'
								GROUP BY
								  p.product_id
								ORDER BY
								  os.date_added DESC");
        $allDetails=$query->rows;
			foreach ($allDetails as $key => $value) {
				$getProductImages=$this->model_restapi_service->getProductImages($value['product_id']);
				$allDetails[$key]['product_all_images']=$getProductImages;
				$allDetails[$key]['slug']=$this->slugify($value['name']);
			}
        return $allDetails;
   }

       /*
    * @method: to get stock status. 
    * @param: $product id 
    * @return: stock status 
    * @author: Rahul, August 2018
    */
    public function getProductStockStatusValue( int $product_id ) {

      $sql = "SELECT * FROM " . DB_PREFIX . "product  WHERE product_id = '" . (int)$product_id . "'";
      
      $op_query = $this->db->query($sql);

      if ($op_query->num_rows < 1) {
      	return false;	
      }
      
      $product_detail=$op_query->row;
      
      // Get seller_id
        $sql = "SELECT 
        			seller_id 
        		FROM 
        			" . DB_PREFIX . "ms_product
                WHERE 
                	product_id = '" . (int)$product_detail['product_id'] . "'
                ";
        $omp_query = $this->db->query($sql);

        if ($omp_query->num_rows == 0) { // If no seller then return false
        	 return false;
           } 
            
        // oc_ms_seller query
        $sql = "SELECT 
        			seller_id, 
        			cod_available, 
        			vacation_mode, 
        			seller_status, 
        			non_returnable, 
        			city 
                FROM 
                	" . DB_PREFIX . "ms_seller
                WHERE 
                	seller_id = '" . (int)$omp_query->row['seller_id'] . "'
                ";
        $oms_query = $this->db->query($sql);

        if ($oms_query->num_rows == 0) {
            return false;
        }

        $product_info = $product_detail;
        $product_info['seller_status'] = $oms_query->row['seller_status'];
        $product_info['vacation_mode'] = $oms_query->row['vacation_mode'];
        $stock_status_info = Cart::getProductStockStatus($product_info);
        if ( isset($stock_status_info['stock'])
            && $stock_status_info['stock'] === false){
            return false;
        }

      // control came here, means every associate product is in-stock, so return true
      return true;
    }



}
