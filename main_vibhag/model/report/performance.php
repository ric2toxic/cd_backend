<?php
class ModelReportPerformance extends Model {

	public function getPerformanceDetails($data = array(), $sort='ASC'){

        $sql = "SELECT  o.order_id,os.name
                FROM oc_order o
                INNER JOIN oc_suborder osub ON  o.order_id = osub.order_id ";
        
        if (isset($data['filter_sales_staff'])) {
            $sql .= " LEFT JOIN oc_order_sales_staff oss ON oss.order_id = o.order_id ";
        }
        
        $sql .= " INNER JOIN oc_order_status os ON  osub.order_status_id = os.order_status_id
                  WHERE os.language_id = 1 ";
        
        if (empty($data['filter_franchise_order'])) {
            $sql .= " AND o.franchise_id = 0 ";
        }
        
                    

        if (!empty($data['filter_order_no'])) {
            $sql .= " AND o.order_no >= '" . (float)$data['filter_order_no'] . "'";
        }

        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(o.date_added) >=  DATE('" . $this->db->escape($data['filter_date_from']) . "')";
        }

        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_to']) . "')";
        }
        if (isset($data['filter_sales_staff'])) {
            $sql .= " AND oss.sales_staff_id = '" . (int)$data['filter_sales_staff'] . "'";
        }

        if (isset($data['filter_city'])) {
            $sql .= " AND o.shipping_city LIKE '%" . $data['filter_city'] . "%'";
        }

        if (isset($data['filter_order_status'])) {
            $sql .= " AND osub.order_status_id = " . (int)$data['filter_order_status'] . "";
        }

        if (isset($data['filter_payment_code'])) {
            $sql .= " AND o.payment_code = '" . $this->db->escape($data['filter_payment_code']) . "'";
        }

        $sql .= (" AND o.store_id IN (".WSB_STORES_ID .") GROUP BY o.order_id ORDER BY o.order_id " . $sort);

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return $query->rows; 
        }else{
            return false;
        }

    }

    public function getTotalPerfomanceDetails($data = array()){

        $sql = "SELECT COUNT(DISTINCT o.order_id) AS total FROM oc_order as o 
                INNER JOIN oc_suborder osub ON osub.order_id = o.order_id ";
                
        if (isset($data['filter_sales_staff'])) {
            $sql .= " LEFT JOIN oc_order_sales_staff oss ON oss.order_id = o.order_id ";
        }
        
        $sql .=" WHERE osub.order_status_id > 0  
                   AND o.store_id IN (".WSB_STORES_ID .") 
                   AND o.franchise_id = 0 " ;

        if (!empty($data['filter_order_no'])) {
			$sql .= " AND o.order_no >= '" . (float)$data['filter_order_no'] . "'";
		}

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE(o.date_added) >=  DATE('" . $this->db->escape($data['filter_date_from']) . "')";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_to']) . "')";
		}

        if (isset($data['filter_sales_staff'])) {
			$sql .= " AND oss.sales_staff_id = '" . (int)$data['filter_sales_staff'] . "'";
		}

		if (isset($data['filter_city'])) {
			$sql .= " AND o.shipping_city LIKE '%" . $data['filter_city'] . "%'";
		}

		if (isset($data['filter_order_status'])) {
            $sql .= " AND osub.order_status_id = " . (int)$data['filter_order_status'] . "";
        }

        if (isset($data['filter_payment_code'])) {
			$sql .= " AND o.payment_code = '" . $this->db->escape($data['filter_payment_code']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
    }

    public function getSalesStaffList() {
        $sql = "SELECT staff_id, name FROM " . DB_PREFIX . "sales_staff WHERE 1 ORDER BY name ASC";
        $query = $this->db->query($sql)->rows;

        foreach($query as $val){
            $result[$val['staff_id']] = $val['name'];
        }

        return $result;
	}
    /**
	 * getOrderTagedSalesStaff
	 * Get Sales staff which are taged in an Order by oc_order_sales_staff
	 * @param  INTEGER  order_id
	 * @return ARRAY    result
	 * @author Garvit 
	 */
	public function getOrderTagedSalesStaff($order_id) {
		$result = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_sales_staff WHERE order_id = ".$order_id)->rows;
        return $result;
	}

    /**
     * Method for get Distinct payment code
     * @return array of payment code
     * @author vikas,2017 
     */
    public function getDistinctPaymentCode() {
        $sql = "SELECT DISTINCT payment_code FROM " . DB_PREFIX . "order";
        $result = $this->db->query($sql)->rows;
        return array_filter(array_column($result, 'payment_code'));
    }
    
    
    public function getDocketDimensionsString($courier, $courier_dockets = array())
    {
        $sql = '';
        if(strtolower($courier) == 'gati'){
            $ids = implode(',', $courier_dockets);
            $sql = "SELECT docket_no, post_data, 'gati' as 'courier' FROM " . DB_PREFIX . "gati_dockets "
                    . "WHERE FIND_IN_SET(docket_no, '".$ids."') and post_data !='' "; 
        
            //echo $sql; die;
        }
        if(strtolower($courier) == 'dotzot'){
            $ids = implode(',', $courier_dockets);
            $sql = "SELECT docket_no, post_data, 'dotzot' as 'courier' FROM " . DB_PREFIX . "dotzot_dockets "
                    . "WHERE FIND_IN_SET(docket_no, '".$ids."') and post_data !='' "; 
        }
        if(strtolower($courier) == 'bluedart'){
            $ids = implode(',', $courier_dockets);
            $sql = "SELECT docket_no, post_data, 'bluedart' as 'courier' FROM " . DB_PREFIX . "bluedart_dockets "
                    . "WHERE FIND_IN_SET(docket_no, '".$ids."') and post_data !='' "; 
        }
        if(strtolower($courier) == 'connectindia'){
            $ids = implode(',', $courier_dockets);
            $sql = "SELECT docket_no, post_data, 'connectindia' as 'courier' FROM " . DB_PREFIX . "connect_india_dockets "
                    . "WHERE FIND_IN_SET(docket_no, '".$ids."') and post_data !='' "; 
        }
        if(!empty($sql)) {
            
            $query = $this->db->query($sql);

            if( $query->num_rows ){
                return $query->rows; 
            }else{
                return false;
            }
        } else { 
            
            return false;
        }
    }
    
    public function getCourierDocketDetails($docket_no,$courier_name)
    {
         $sql = '';
         
        if(strtolower($courier_name) == 'gati')
        {
           $sql = "SELECT post_data, 'gati' as 'courier' FROM " . DB_PREFIX . "gati_dockets WHERE docket_no = '". $this->db->escape($docket_no)."'"; 
        }
        if(strtolower($courier_name) == 'dotzot')
        {
           $sql = "SELECT post_data, 'dotzot' as 'courier' FROM " . DB_PREFIX . "dotzot_dockets WHERE docket_no = '". $this->db->escape($docket_no)."'"; 
        }
        if(strtolower($courier_name) == 'bluedart')
        {
           $sql = "SELECT post_data, 'bluedart' as 'courier' FROM " . DB_PREFIX . "bluedart_dockets WHERE docket_no = '". $this->db->escape($docket_no)."'"; 
        }
        if(strtolower($courier_name) == 'connectindia' || strtolower($courier_name) == 'connect-india') 
        {
           $sql = "SELECT post_data, 'connectindia' as 'courier' FROM " . DB_PREFIX . "connect_india_dockets WHERE docket_no = '". $this->db->escape($docket_no)."'"; 
        }
        
        if(!empty($sql)) {
            
            $query = $this->db->query($sql);

            if( $query->num_rows ){
                return $query->rows; 
            }else{
                return false;
            }
        } else { 
            
            return false;
        }
    }
    
}
