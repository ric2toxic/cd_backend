<?php
class ModelSaleShippingLabel extends Model {

	/**
	 * Method to return an list of courier partners
	 * @param null
	 * Output: list of courier
	 * Author: vikas
	 */

	public function listOfCouriers(){
		$query = $this->db->query("SELECT * FROM " .DB_PREFIX. "courier_partners");
		return $query->rows;
	}

	/**
	 * Method to return an list of courier partners with reverse shipment facility
	 * @param String $type 
	 * Output: list of courier
	 * Author: msa
	 */
	public function getCourierListByServiceType($type='forward')
	{
		$sql = "SELECT * FROM " .DB_PREFIX. "courier_partners ";
		if($type == 'reverse')
		{
			$sql .= " WHERE is_reverse_shipment = 1 ";
		}else if($type == 'forward')
		{
			$sql .= " WHERE is_forward_shipment = 1 ";
		}
		$query = $this->db->query($sql);
		return $query->rows;
	}

	/**
	 * Method to adding a new warehouse address
	 * @param null
	 * Output: null
	 * Author: vikas
	 */

	public function addAddress($data = array()){
		$sql = "INSERT INTO ".DB_PREFIX."warehouse_address
					SET warehouse_name = '". $this->db->escape(trim($data['warehouse_name']))."',
						address_1 = '".$this->db->escape(trim($data['address_1']))."',
						address_2 = '".$this->db->escape(trim($data['address_2']))."',
						city = '".$this->db->escape(trim($data['city']))."',
						postcode = '".$this->db->escape(trim($data['postcode']))."',
						zone_id = '".(int)$data['state']."',
						telephone = '".$this->db->escape(trim($data['telephone']))."'
			   ";
		$this->db->query($sql);
	}


	/**
	 * Method to get warehouse address
	 * @param searching data
	 * Output: get searching address data
	 * Author: vikas
	 */

	public function getAddresses($data = array()){
        
        $search_warehouse = trim($data['search_warehouse'] ?? '');
        $search_city = trim($data['search_city'] ?? '');

		$sql = "SELECT  owa.warehouse_id,
 						owa.warehouse_name,
 						owa.address_1,
 						owa.address_2,
 						owa.city,
 						owa.postcode,
 						owa.telephone,
 						oz.name as zone_name,
                        oz.code,
 						oc.name as country_name,
 						owa.gati_vendor_code,
 						owa.nuvoex_vendor_code,
                        owa.bluedart_vendor_code,
                        owa.bluedart_surface_customer_code,
                        owa.bluedart_apex_customer_code,
                        owa.bluedart_origin_area,
                        owa.email,
                        owa.connect_india_origin_code
 				  FROM ". DB_PREFIX ."warehouse_address owa
				  INNER JOIN ". DB_PREFIX ."zone oz
				   	ON oz.zone_id = owa.zone_id
				  INNER JOIN ". DB_PREFIX ."country oc
				   	ON oc.country_id = oz.country_id ";
		if ( !empty($search_warehouse) || !empty($search_city) ) {
			$sql .= "WHERE MATCH(owa.warehouse_name,owa.city)
				  		   AGAINST ('" . $this->db->escape($search_warehouse) . " " . $this->db->escape($search_city) 
                                  . "' IN BOOLEAN MODE)
                     AND owa.status = 1 ";
		} else {
			$sql .= "WHERE owa.status = 1 
                     LIMIT 8";
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * Method to get selected address and show on shipping label
	 * @param warehouse_address_id (selected_address)
	 * Output: get address
	 * Author: vikas
	 */

	public function getAddress($warehouse_address_id){
		$sql = "SELECT  owa.warehouse_name,
						owa.address_1,
						owa.address_2,
						owa.city,
						owa.telephone,
						owa.postcode,
						owa.gstin,
						oz.name as state,
                        oz.code,
                        oz.zone_id,
						oc.name as country,
                        owa.gati_vendor_code,
 						owa.nuvoex_vendor_code,
                        owa.bluedart_vendor_code,
                        owa.bluedart_surface_customer_code,
                        owa.bluedart_apex_customer_code,
                        owa.bluedart_origin_area,
                        owa.email
				FROM ". DB_PREFIX ."warehouse_address owa
				INNER JOIN ". DB_PREFIX ."zone oz
				  ON oz.zone_id = owa.zone_id
				INNER JOIN ". DB_PREFIX ."country oc
				  ON oc.country_id = oz.country_id
				WHERE warehouse_id = " . (int)$warehouse_address_id;
		$query = $this->db->query($sql);

		return $query->row;


	}

	/**
	 * Method to adding a new shipping label
	 * @param null
	 * Output: null
	 * Author: vikas
	 */
	public function addShippingLabel($order_id, $suborder_id, $courier, $docket, $weight, $warehouse_address, $barcode = '1' ){

		$sql = "INSERT INTO ". DB_PREFIX ."shipping_label
				SET order_id = '" . (int)$order_id."',
				    suborder_id = '" . $this->db->escape($suborder_id)."',
					from_warehouse_id = '" . (int)$warehouse_address . "',
					courier_name = '" . $this->db->escape($courier) . "',
					tracking_no = '" . $this->db->escape($docket) . "',
					weight = '" . (float)$weight . "',
					barcode = '" . (int)$barcode ."'";

		$this->db->query($sql);
		return $this->db->getLastId();
	}


	/**
	 * Method to get a courier name by courier id
	 * @param courier id
	 * Output: Courier Name
	 * Author: vikas
	 */
	public function getCourierName($courier_id){
		$get_courier_name = $this->db->query("SELECT courier_name
                                              FROM " . DB_PREFIX . "courier_partners
                                              WHERE id = '" . (int)$courier_id . "'");

		return $get_courier_name->row['courier_name'];
	}
    
    public function getWarehouseAddressForShippingLabel(int $warehouse_id) : array {
        
        if ( $warehouse_id <= 0 )
            return array();
        
        $wa_sql = "SELECT owa.warehouse_name,
                          owa.address_1,
                          owa.address_2,
                          owa.city,
                          owa.postcode,
                          owa.telephone, 
                          oz.name AS zone_name, 
                          oc.name AS country_name   
                   FROM ". DB_PREFIX ."warehouse_address owa 
                   JOIN ". DB_PREFIX ."zone oz ON oz.zone_id = owa.zone_id 
                   JOIN ". DB_PREFIX ."country oc ON oc.country_id = oz.country_id 
                   WHERE owa.warehouse_id = " . (int)$warehouse_id;
        $wa_query = $this->db->query($wa_sql);
        return $wa_query->row;
    }


	/**
	 * Method to get addresses and data show on pdf
	 * @param shipping_label__id
	 * Output: shipping label address
	 * Author: vikas
	 */
	public function getShippingLabelAddress($shipping_label__id){

		$sql = "SELECT  shipping_label_id,
						order_id,
						suborder_id,
						from_warehouse_id,
					    courier_name,
					    tracking_no,
						weight,
						barcode,
                        file_name
				FROM " . DB_PREFIX . "shipping_label
				WHERE shipping_label_id = '" . (int)($shipping_label__id) . "'";

		$query = $this->db->query($sql);

		$shipping_address = $this->db->query("SELECT order_no,
													 shipping_firstname,
													 shipping_lastname,
													 shipping_company,
													 shipping_address_1,
													 shipping_address_2,
													 shipping_postcode,
													 shipping_city,
													 shipping_zone,
													 shipping_country,
													 telephone as shipping_telephone,
													 alternate_contact_number,
													 payment_method,
													 payment_code,
													 total
											FROM ". DB_PREFIX ."order
											WHERE order_id = '" . (int)($query->row['order_id']) . "' 
                                                  AND franchise_id = 0 ");

		$query->row['order_no']	 			= $shipping_address->row['order_no'];
		$query->row['shipping_firstname'] 	= $shipping_address->row['shipping_firstname'];
		$query->row['shipping_lastname'] 	= $shipping_address->row['shipping_lastname'];
		$query->row['shipping_company'] 	= $shipping_address->row['shipping_company'];
		$query->row['shipping_address_1'] 	= $shipping_address->row['shipping_address_1'];
		$query->row['shipping_address_2'] 	= $shipping_address->row['shipping_address_2'];
		$query->row['shipping_postcode'] 	= $shipping_address->row['shipping_postcode'];
		$query->row['shipping_city'] 		= $shipping_address->row['shipping_city'];
		$query->row['shipping_zone'] 		= $shipping_address->row['shipping_zone'];
		$query->row['shipping_country'] 	= $shipping_address->row['shipping_country'];
		$query->row['shipping_telephone'] 	= $shipping_address->row['shipping_telephone'];
		$query->row['shipping_alternate_telephone'] = $shipping_address->row['alternate_contact_number'];
		$query->row['payment_method'] 		= $shipping_address->row['payment_method'];
		$query->row['payment_code'] 		= $shipping_address->row['payment_code'];

        $buyerinvoice = new BuyerInvoice($this);
        $netAmount = $buyerinvoice->getTotals($query->row['order_id'],$query->row['suborder_id']);
        $query->row['total'] = $netAmount['net_amount']['value'];

        if( $query->row['total'] <= 0){
            $query->row['payment_code'] = 'prepaid';
        }

    	//get short name of state by shipping pincde
		$short_name_state = $this->db->query("SELECT ou
											  FROM ". DB_PREFIX ."gati_pincodes
											  WHERE pincode = '" . $this->db->escape($shipping_address->row['shipping_postcode']) . "'");
		$query->row['short_name_state']	 = isset($short_name_state->row['ou']) ? $short_name_state->row['ou'] : '';

		$warehouse_address = $this->getWarehouseAddressForShippingLabel((int)$query->row['from_warehouse_id']);
        
        foreach ($warehouse_address as $kk => $vv) {
            $query->row[$kk] = $vv;
        }

		return $query->row;
	}

	//update file path when click on download shipping label pdf file by shipping label id
	public function  updateShippingLabelFilePath($shipping_label_id, $file_name){
		$sql = "UPDATE ". DB_PREFIX ."shipping_label
					SET file_name = '" . $this->db->escape($file_name). "'
					WHERE shipping_label_id = ".(int)$shipping_label_id ."";
		$this->db->query($sql);
	}


	/**
	 * Method to return list of shipping label
	 * @param int order_id
	 * Output: list of shipping label
	 * Author: vikas
	 */
	public function getShippingLabel($order_id, $suborder_id, $start, $limit){
		if ((int)$start < 0) {
			$start = 0;
		}

		if ((int)$limit < 1) {
			$limit = 10;
		}

		$sql = "SELECT 	 shipping_label_id,
						 courier_name,
						 order_id,
						 suborder_id,
						 from_warehouse_id,
 						 tracking_no,
 						 weight,
 						 file_name
				FROM ". DB_PREFIX ."shipping_label
				WHERE  order_id = '".(int)$order_id. "'
					AND suborder_id = '".$this->db->escape($suborder_id)."'
				ORDER BY shipping_label_id ASC
				LIMIT ". (int)$start. "," . (int)$limit."
				";

		$query = $this->db->query($sql);

		foreach($query->rows as $key => $data){
			$warehouse_address = $this->getWarehouseAddressForShippingLabel((int)$data['from_warehouse_id']);
            
            foreach ( $warehouse_address as $kk => $vv ) {
                $query->rows[$key][$kk] = $vv;
            }
		}
		return $query->rows;
	}

	/**
	 * Method to return total shipping label
	 * @param int order_id
	 * Output: total shipping label
	 * Author: vikas
	 */
	public function getTotalShippingLabel($order_id,$suborder_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
									FROM " . DB_PREFIX . "shipping_label osl
									WHERE order_id = '" . (int)$order_id . "'
										AND suborder_id = '".$this->db->escape($suborder_id)."'");

		return $query->row['total'];
	}

	/**
	 * Method to get ware house details by nuvoex vendor id
	 * @param int vendor_id
	 * Output: total shipping label
	 * Author: vikas
	 */
	public function getWareHouseByNuvoexVendorCode($vendor_id) {
		$query = $this->db->query("SELECT wa.*, zone.name as zone_name
									FROM " . DB_PREFIX . "warehouse_address wa
									INNER JOIN " . DB_PREFIX . "zone as zone ON zone.zone_id = wa.zone_id AND zone.status = 1
									WHERE  
										wa.status = 1
											AND
										wa.nuvoex_vendor_code = '" .$this->db->escape($vendor_id)."'");
		if($query->num_rows > 0){
			return $query->row;
		}
	}

	/**
	 * Method to get order product category names
	 * @param: array order product ids
	 * @return: array category names
	 * Author: MSA Jan 2019
	 */
	public function getOrderProductsCategoryNames(array $order_product_ids)
	{
		$categories = '';
		
		if(!empty($order_product_ids)) {

			$order_product_ids = implode(',', $order_product_ids);

			$sql = "
				SELECT
				  GROUP_CONCAT( DISTINCT( ocd.name ) SEPARATOR '/' ) as categories
				FROM
				  " . DB_PREFIX . "order_product as op
				INNER JOIN " . DB_PREFIX . "product_to_category as pc 
							ON pc.product_id = op.product_id
				INNER JOIN " . DB_PREFIX . "category_path as ocp 
							ON ocp.category_id = pc.category_id 
								AND 
							   ocp.level = 2			
				INNER JOIN " . DB_PREFIX . "category_description as ocd 
							ON ocd.category_id = pc.category_id
				WHERE
					op.order_product_id IN (".$order_product_ids.")
					AND
					ocd.language_id = 1
			";
			$result = $this->db->query($sql);

			if($result->num_rows) {
				$categories = $result->row['categories'];
			}
		}
		
		return $categories;
	}



}
