<?php
class ModelReportProduct extends Model {
	public function getProductsViewed($data = array()) {
		$sql = "SELECT pd.name, p.model, p.viewed FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.viewed > 0 ORDER BY p.viewed DESC";

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

		return $query->rows;
	}

	public function getTotalProductViews() {
		$query = $this->db->query("SELECT SUM(viewed) AS total FROM " . DB_PREFIX . "product");

		return $query->row['total'];
	}

	public function getTotalProductsViewed() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE viewed > 0");

		return $query->row['total'];
	}

	public function reset() {
		//$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = '0'");
	}

	public function getPurchased($data = array()) {
		$sql = "SELECT op.name, op.model, SUM(op.quantity) AS quantity, SUM((op.total + op.tax) * op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
		/*
		* this condition returns without franchise id orders
		*/
		$sql .= " AND o.franchise_id = 0 ";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " AND o.store_id IN (".WSB_STORES_ID .") GROUP BY op.product_id ORDER BY total DESC";

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

		return $query->rows;
	}

	public function getTotalPurchased($data) {
		$sql = "SELECT COUNT(DISTINCT op.product_id) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
		/*
		* this condition returns without franchise id orders
		*/
		$sql .= " AND o.franchise_id = 0 ";
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " AND o.store_id IN (".WSB_STORES_ID .") ";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}



	/**
	 * Method for check product_id is available or not available in wsb_purchase_breakup 
	 * @param : $product_id : Integer of product id
	 * @return : true or false
	 * @author: vikas, May, 2018
	 */
	public function checkProductIdAvailableInWsbProductBreakup($product_id){
		$sql = "SELECT product_id 
				FROM " . DB_PREFIX . "wsb_purchase_breakup
				WHERE product_id = " . (int) $product_id ;
		$query = $this->db->query($sql);
		if( $query->num_rows ){
			return true;
		} else {
			return false;
		}
	}

	/**
	* Method for get pickup_city_code corresponding to purchase_firm_tin_no(GST No.)	
	* @return NULL
	* @author vikas, 2018
	*/

	public function getPickupCityCodeCorrespondingToGSTNo(){
		$sql = "SELECT pickup_city_code,
					   purchase_firm_tin_no
					FROM " . DB_PREFIX . "vat_input_rules
					WHERE status = 1 
					GROUP BY purchase_firm_tin_no";
		$query = $this->db->query($sql);
		$result = array();
		if( $query->num_rows ){
			foreach ($query->rows as $value) {
				$result[$value['purchase_firm_tin_no']] = $value['pickup_city_code'];
			}
		}
		return $result ;
	}

	/**
	* Method for get current seller nick name corresponding to purchase_firm_tin_no(GST No.)	
	* @param  : $gst_no : string type of GST NO.
	* @return : current seller nickname
	* @author : kalyan, 17th Sept. 2018
	*/
	public function getCurrentSellerNickNameByPorductId( int $product_id ) : array {
		
		$result = array();

		$sql = "SELECT 	ms.nickname,
						ms.pickup_city_code,
						ms.gst_provisional_id
					FROM " . DB_PREFIX . "ms_product omp
					INNER JOIN " . DB_PREFIX . "ms_seller ms
						ON ms.seller_id = omp.seller_id 
					INNER JOIN " . DB_PREFIX . "vat_input_rules vir
					  ON ms.purchase_firm_id = vir.purchase_firm_id 
					WHERE omp.product_id = '". (int)$product_id ."' 
					  AND vir.status = 1
					GROUP BY vir.purchase_firm_id";
		
		$query = $this->db->query($sql);
		
		if( $query->num_rows ){

			$result = $query->row;			
		}

		return $result ;
	}	

	/**
	 * Method for Get Product Order List Purchase Inventory
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getProductOrderListPurchaseInventory($product_id){
		$sql = '';
		$sql .= $this->getWsbPurchaseInvoiceEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getWsbPurchaseReturnDebitNoteEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getStockTransferOutInvoicedEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getStockTransferInInvoicedEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getStockTransferOutUnInvoicedEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getStockTransferInUnInvoicedEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getStockTransferReturnInEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getStockTransferReturnOutEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getLossBookedByWSBEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getDebitedLogisticOUTEntries($product_id);
		$sql .= ' UNION ALL ';

		/********************* Inventories Whose Purchase is Not Recorded Yet *********************/
		$sql .= $this->getMissingPurchaseStockTransferOutInvoicedEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getMissingPurchaseStockTransferInInvoicedEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getMissingPurchaseStockTransferOutUnInvoicedEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getMissingPurchaseStockTransferInUnInvoicedEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getMissingPurchaseStockTransferReturnInEntries($product_id);
		$sql .= ' UNION ALL ';
		$sql .= $this->getMissingPurchaseStockTransferReturnOutEntries($product_id);

		$sql .= " ORDER BY reference_document_date ASC, product_id ASC ";
		
		$query = $this->db->query($sql);

		$result = array();
		if( $query->num_rows ){
			foreach ($query->rows as $row) {
				$result[] = $row;
			}
		}

		return $result;
	}

	/**
	 * Method for WSB Purchase Invoice Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getWsbPurchaseInvoiceEntries($product_id){
		/* START WSB Purchase Invoice Entries */
		$sql = "SELECT 'WSB Purchase' as transaction_type,
						'' as reference_no,
						owpb.breakup_id as row_id, 
						owpb.product_id,           
						owpb.sku as seller_sku,           
						owp.invoice_no as reference_document,
						date(owp.invoice_date) as reference_document_date,
						owpb.pieces as qty_in,
						'' as qty_out, 
						'' as stock_movement_detail, 
						IF(owp.sor_purchase = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						IF(owp.gst = 1, oms2.gst_provisional_id, oms2.tin) as wsb_gst, 
						oms1.company as seller_firm,           
						owpb.transfer_price_per_piece, 
						owpb.seller_tax, 
						'' as order_status,
						'' as order_status_id,
						'' as option_name,
						'' as option_value 
				FROM ". DB_PREFIX. "wsb_purchase as owp  
				INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup as owpb ON (owpb.purchase_id = owp.purchase_id)
				INNER JOIN ". DB_PREFIX. "ms_seller as oms1 ON (oms1.seller_id = owp.seller_id)
				INNER JOIN ". DB_PREFIX. "ms_seller as oms2 ON (oms2.purchase_firm_id = owp.purchase_firm_id) 
				WHERE owpb.product_id = " . (int)$product_id . "
				GROUP BY owpb.breakup_id ";
		return $sql;		
		/* END WSB Purchase Invoice Entries */
	}


	/**
	 * Method for WSB Purchase Return Debit Note Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getWsbPurchaseReturnDebitNoteEntries($product_id){

		/* START WSB Purchase Return Debit Note Entries */
		$sql = "SELECT 'WSB Purchase Return' as transaction_type,
						'' as reference_no,
						owprb.id as row_id, 
						owprb.product_id, 
						owprb.sku as seller_sku, 
						CONCAT(owpr.debit_note_prefix, owpr.debit_note_no) as reference_document,           
						date(owpr.date_added) as reference_document_date,
						'' as qty_in,
						owprb.quantity as qty_out, 
						'' as stock_movement_detail, 
						IF(owp.sor_purchase = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm,
						oms2.gst_provisional_id as wsb_gst, 
						oms1.company as seller_firm,  
						owprb.transfer_price_per_piece, 
						owprb.tax_rate as seller_tax, 
						'' as order_status,
						'' as order_status_id,
						'' as option_name,
						'' as option_value 
				FROM ". DB_PREFIX. "wsb_purchase_return as owpr  
				INNER JOIN ". DB_PREFIX. "wsb_purchase_return_breakup as owprb ON (owprb.debit_note_id = owpr.debit_note_id) 
				INNER JOIN ". DB_PREFIX. "wsb_purchase as owp ON (owp.purchase_id = owpr.purchase_id) 
				INNER JOIN ". DB_PREFIX. "ms_seller as oms1 ON (oms1.seller_id = owp.seller_id)
				INNER JOIN ". DB_PREFIX. "ms_seller as oms2 ON (oms2.purchase_firm_id = owp.purchase_firm_id)
				WHERE owpr.debit_note_status = 1 
				  AND owprb.product_id = " . (int)$product_id . "
				GROUP BY owprb.id ";
		return $sql;		
		/* END WSB Purchase Return Debit Note Entries */
	}

	/**
	 * Method for Sales / Stock Transfer (OUT) Invoiced Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getStockTransferOutInvoicedEntries($product_id){

		/* START Sales / Stock Transfer (OUT) Invoiced Entries */
		$sql = "SELECT IF(o.stock_transfer = 0, 'Sales', 'Stock Transfer') as transaction_type,
						oop.suborder_id as reference_no, 
						oop.order_product_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id, ';', osub.invoice_prefix, osub.invoice_no) as reference_document, 
						date(osub.invoice_date) as reference_document_date, 
						'' as qty_in,
						(oop.piece_in_set*oop.quantity) as qty_out, 
						IF(o.stock_transfer = 1, CONCAT(UPPER(ovir.purchase_firm_city), ' to ', UPPER(o.payment_city)), '')  as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						ovir.purchase_firm_tin_no as wsb_gst, 
						oms1.company as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "order o 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.buyer_invoice_id = osub.buyer_invoice_id  
				INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
				INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms1 ON oms1.seller_id = owp.seller_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.purchase_firm_id = ovir.purchase_firm_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id   
				WHERE osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0 
				  AND o.store_id IN (0,2,9) 
				  AND o.franchise_id = 0  
				  AND oms3.seller_invoice_generate = 0 
				  AND oos.language_id = 1 
				  AND oop.product_id = " . (int)$product_id . "
				GROUP BY oop.order_product_id  ";
		return $sql;		
		/* END Sales / Stock Transfer (OUT) Invoiced Entries */
	}

	/**
	 * Method for Stock Transfer (IN) Invoiced Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getStockTransferInInvoicedEntries($product_id){

		/* START Stock Transfer (IN) Invoiced Entries */
		$sql = "SELECT 'Stock Transfer' as transaction_type, 
						oop.suborder_id as reference_no, 
						oop.order_product_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id, ';', osub.invoice_prefix, osub.invoice_no) as reference_document, 
						date(osub.invoice_date) as reference_document_date, 
						(oop.piece_in_set*oop.quantity) as qty_in,
						'' as qty_out, 
						CONCAT(UPPER(ovir.purchase_firm_city), ' to ', UPPER(o.payment_city)) as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						o.gst_number as wsb_gst, 
						oms1.company as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "order o 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.buyer_invoice_id = osub.buyer_invoice_id  
				INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms1 ON oms1.seller_id = owp.seller_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.seller_id = o.customer_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id  
				WHERE osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0 
				  AND o.store_id IN (0,2,9) 
				  AND o.stock_transfer = 1 
				  AND o.franchise_id = 0 
				  AND oms3.seller_invoice_generate = 0 
				  AND oos.language_id = 1
				  AND oop.product_id = " . (int)$product_id . " 
				GROUP BY oop.order_product_id  ";
		return $sql;		
		/* END Stock Transfer (IN) Invoiced Entries */
	}

	/**
	 * Method for Sales / Stock Transfer (OUT) Un-Invoiced Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getStockTransferOutUnInvoicedEntries($product_id){

		/* START Sales / Stock Transfer (OUT) Un-Invoiced Entries */
		$sql = "SELECT IF(o.stock_transfer = 0, 'Sales', 'Stock Transfer') as transaction_type, 
						oop.suborder_id as reference_no,
						oop.order_product_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id, ';', 'Un-invoiced')  as reference_document, 
						date(osub.date_added) as reference_document_date, 
						'' as qty_in,
						(oop.piece_in_set*oop.quantity) as qty_out, 
						IF(o.stock_transfer = 1, CONCAT(UPPER(ovir.purchase_firm_city), ' to ', UPPER(o.payment_city)), '')  as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						ovir.purchase_firm_tin_no as wsb_gst, 
						oms1.company as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "order o 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms1 ON oms1.seller_id = owp.seller_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.zone_id = oms3.zone_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.purchase_firm_id = ovir.purchase_firm_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id 
				WHERE oop.suborder_id = osub.suborder_id 
				  AND osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND (osub.invoice_no = 0 OR osub.invoice_no IS NULL) 
				  AND (osub.buyer_invoice_id = 0 OR osub.buyer_invoice_id IS NULL) 
				  AND o.store_id IN (0,2,9) 
				  AND o.franchise_id = 0 
				  AND oop.edit_type IN ('YES','SELLER_PARTIAL','SELLER_APPROVED','SELLER_LATER_DISPATCH','DAMAGE_BY_COURIER_COMPANY') 
				  AND oms3.seller_invoice_generate = 0 
				  AND DATE(ovir.date_begin) <= DATE(NOW())
				  AND (ovir.date_end IS NULL OR DATE(ovir.date_end) >= DATE(NOW()))
				  AND ovir.status = 1 
				  AND oos.language_id = 1
				  AND oop.product_id = " . (int)$product_id . " 
				GROUP BY oop.order_product_id   ";
		return $sql;		
		/* END Sales / Stock Transfer (OUT) Un-Invoiced Entries */
		//CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value
	}

	/**
	 * Method for Stock Transfer (IN) Un-Invoiced Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getStockTransferInUnInvoicedEntries($product_id){

		/* START Stock Transfer (IN) Un-Invoiced Entries */
		$sql = "SELECT IF(o.stock_transfer = 0, 'Sales', 'Stock Transfer') as transaction_type, 
						oop.suborder_id as reference_no, 
						oop.order_product_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id, ';', 'Un-invoiced')  as reference_document, 
						date(osub.date_added) as reference_document_date, 
						(oop.piece_in_set*oop.quantity) as qty_in,
						'' as qty_out, 
						IF(o.stock_transfer = 1, CONCAT(UPPER(ovir.purchase_firm_city), ' to ', UPPER(o.payment_city)), '')  as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						o.gst_number as wsb_gst, 
						oms1.company as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "order o 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms1 ON oms1.seller_id = owp.seller_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.seller_id = o.customer_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.zone_id = oms3.zone_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id  
				WHERE oop.suborder_id = osub.suborder_id 
				  AND osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND (osub.invoice_no = 0 OR osub.invoice_no IS NULL) 
				  AND (osub.buyer_invoice_id = 0 OR osub.buyer_invoice_id IS NULL) 
				  AND o.store_id IN (0,2,9) 
				  AND o.franchise_id = 0 
				  AND o.stock_transfer = 1 
				  AND oop.edit_type IN ('YES','SELLER_PARTIAL','SELLER_APPROVED','SELLER_LATER_DISPATCH','DAMAGE_BY_COURIER_COMPANY') 
				  AND oms3.seller_invoice_generate = 0 
				  AND DATE(ovir.date_begin) <= DATE(NOW())
				  AND (ovir.date_end IS NULL OR DATE(ovir.date_end) >= DATE(NOW()))
				  AND ovir.status = 1 
				  AND oos.language_id = 1 
				  AND oop.product_id = " . (int)$product_id . "
				GROUP BY oop.order_product_id ";
		return $sql;		
		/* END Stock Transfer (IN) Un-Invoiced Entries */
	}

	/**
	 * Method for Sales Return / Stock Transfer Return (IN) Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getStockTransferReturnInEntries($product_id){

		/* START Sales Return / Stock Transfer Return (IN) Entries */
		$sql = "SELECT IF(o.stock_transfer = 0, 'Sales Return', 'Stock Transfer Return') as transaction_type,
						oop.suborder_id as reference_no, 
						ort.return_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(ocn.credit_note_prefix, ocn.credit_note_no) as reference_document, 
						DATE(ocn.date_added) as reference_document_date,
						ort.quantity as qty_in,
						'' as qty_out, 
						IF(o.stock_transfer = 1, CONCAT('Return From ', UPPER(o.payment_city), ' to ', UPPER(ovir.purchase_firm_city)), '')  as stock_movement_detail, 
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR, 
						oms2.nickname as wsb_firm, 
						ovir.purchase_firm_tin_no as wsb_gst, 
						oms1.company as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "credit_note ocn 
				INNER JOIN ". DB_PREFIX. "return ort ON ort.credit_note_id = ocn.credit_note_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_product_id = ort.order_product_id 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.buyer_invoice_id = oop.buyer_invoice_id  
				INNER JOIN ". DB_PREFIX. "order o ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
				INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms1 ON oms1.seller_id = owp.seller_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.purchase_firm_id = ovir.purchase_firm_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id   
				WHERE
				  ocn.credit_note_status = 1 
				  AND osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0 
				  AND o.store_id IN (0,2,9) 
				  AND o.franchise_id = 0 
				  AND oms3.seller_invoice_generate = 0 
				  AND oos.language_id = 1 
				  AND oop.product_id = " . (int)$product_id . "
				GROUP BY ort.return_id  ";
		return $sql;		
		/* END Sales Return / Stock Transfer Return (IN) Entries */
	}

	/**
	 * Method for Stock Transfer Return (OUT) Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getStockTransferReturnOutEntries($product_id){

		/* START Stock Transfer Return (OUT) Entries */
		$sql = "SELECT 'Stock Transfer Return' as transaction_type,
						oop.suborder_id as reference_no, 
						ort.return_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(ocn.credit_note_prefix, ocn.credit_note_no) as reference_document, 
						DATE(ocn.date_added) as reference_document_date,
						'' as qty_in,
						ort.quantity as qty_out, 
						CONCAT('Return From ', UPPER(o.payment_city), ' to ', UPPER(ovir.purchase_firm_city))  as stock_movement_detail, 
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR, 
						oms2.nickname as wsb_firm, 
						o.gst_number as wsb_gst, 
						oms1.company as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "credit_note ocn 
				INNER JOIN ". DB_PREFIX. "return ort ON ort.credit_note_id = ocn.credit_note_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_product_id = ort.order_product_id 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.buyer_invoice_id = oop.buyer_invoice_id  
				INNER JOIN ". DB_PREFIX. "order o ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
				INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms1 ON oms1.seller_id = owp.seller_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.seller_id = o.customer_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id 
				WHERE
				  ocn.credit_note_status = 1 
				  AND osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0 
				  AND o.store_id IN (0,2,9) 
				  AND o.stock_transfer = 1 
				  AND o.franchise_id = 0 
				  AND oms3.seller_invoice_generate = 0 
				  AND oos.language_id = 1
				  AND oop.product_id = " . (int)$product_id . " 
				GROUP BY ort.return_id ";
		return $sql;		
		/* END Stock Transfer Return (OUT) Entries */
	}

	/**
	 * Method for Loss Booked By WSB (OUT) entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getLossBookedByWSBEntries($product_id){

		/* START Loss Booked By WSB (OUT) entries */
		$sql = "SELECT  'Loss Booked By WSB' as transaction_type, 
						oop.suborder_id as reference_no, 
						ort.return_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id) as reference_document, 
						date(ort.date_added) as reference_document_date, 
						'' as qty_in,
						ort.quantity as qty_out, 
						''  as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						ovir.purchase_firm_tin_no as wsb_gst, 
						oms1.company as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
						FROM ". DB_PREFIX. "return ort 
						INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_product_id = ort.order_product_id  
						INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = oop.order_id   
						INNER JOIN ". DB_PREFIX. "order o ON o.order_id = osub.order_id 
						INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
						INNER JOIN ". DB_PREFIX. "wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
						INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
						INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
						INNER JOIN ". DB_PREFIX. "ms_seller oms1 ON oms1.seller_id = owp.seller_id 
						INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.purchase_firm_id = ovir.purchase_firm_id 
						INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
						INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
						LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id  
						WHERE oop.suborder_id = osub.suborder_id 
						  AND osub.order_status_id > 0 
						  AND o.store_id IN (0,2,9) 
						  AND o.franchise_id = 0 
						  AND oms3.seller_invoice_generate = 0 
						  AND oos.language_id = 1 
						  AND ort.active_row = 1 
						  AND ort.return_action_id = 119   
						  AND oop.product_id = " . (int)$product_id . " 
						GROUP BY ort.master_return_id, ort.order_product_id ";
		return $sql;
		/* END Loss Booked By WSB (OUT) entries */ 				
	}

	/**
	 * Method for Debited to Logistics (OUT) Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getDebitedLogisticOUTEntries($product_id){

		/* START Debited to Logistics (OUT) Entries */
		$sql = "SELECT  'Debited to Logistics' as transaction_type,
						oop.suborder_id as reference_no, 
						ort.return_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osdn.debit_note_prefix, osdn.debit_note_no) as reference_document, 
						DATE(osdn.date_added) as reference_document_date,
						'' as qty_in,
						ort.quantity as qty_out, 
						''  as stock_movement_detail, 
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR, 
						oms2.nickname as wsb_firm, 
						ovir.purchase_firm_tin_no as wsb_gst, 
						oms1.company as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
						FROM ". DB_PREFIX. "seller_debit_note osdn  
						INNER JOIN ". DB_PREFIX. "return ort ON ort.debit_note_id = osdn.debit_note_id  
						INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_product_id = ort.order_product_id 
						INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = oop.order_id   
						INNER JOIN ". DB_PREFIX. "order o ON osub.order_id = o.order_id 
						INNER JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
						INNER JOIN ". DB_PREFIX. "wsb_purchase owp ON owp.purchase_id = owpb.purchase_id 
						INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
						INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
						INNER JOIN ". DB_PREFIX. "ms_seller oms1 ON oms1.seller_id = owp.seller_id 
						INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.purchase_firm_id = ovir.purchase_firm_id 
						INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
						INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
						LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id   
						WHERE
						  osdn.debit_note_status = 1 
						  AND osdn.custom_id > 0 
						  AND osub.order_status_id > 0 
						  AND osub.suborder_id = oop.suborder_id 
						  AND o.store_id IN (0,2,9) 
						  AND o.franchise_id = 0 
						  AND oms3.seller_invoice_generate = 0 
						  AND oos.language_id = 1 
						  AND oop.product_id = " . (int)$product_id . " 
						GROUP BY ort.return_id ";
		return $sql;
		/* END Debited to Logistics (OUT) Entries */
	}


/********************* Inventories Whose Purchase is Not Recorded Yet *********************/

	/**
	 * Method for Missing Purchase - Sales / Stock Transfer (OUT) Invoiced Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getMissingPurchaseStockTransferOutInvoicedEntries($product_id){

		/* START Missing Purchase - Sales / Stock Transfer (OUT) Invoiced Entries */
		$sql = "SELECT IF(o.stock_transfer = 0, 'Sales', 'Stock Transfer') as transaction_type, 
						oop.suborder_id as reference_no, 
						oop.order_product_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id, ';', osub.invoice_prefix, osub.invoice_no) as reference_document, 
						date(osub.invoice_date) as reference_document_date, 
						'' as qty_in,
						(oop.piece_in_set*oop.quantity) as qty_out, 
						IF(o.stock_transfer = 1, CONCAT(UPPER(ovir.purchase_firm_city), ' to ', UPPER(o.payment_city)), '')  as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						ovir.purchase_firm_tin_no as wsb_gst, 
						'' as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "order o 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.buyer_invoice_id = osub.buyer_invoice_id  
				LEFT JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.purchase_firm_id = ovir.purchase_firm_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id  
				WHERE osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0 
				  AND o.store_id IN (0,2,9) 
				  AND o.franchise_id = 0 
				  AND oms3.seller_invoice_generate = 0 
				  AND oos.language_id = 1 
				  AND owpb.breakup_id IS NULL 
				  AND oop.product_id = " . (int)$product_id . "
				GROUP BY oop.order_product_id ";
		return $sql;		
		/* END Missing Purchase - Sales / Stock Transfer (OUT) Invoiced Entries */
	}

	/**
	 * Method for Missing Purchase - Stock Transfer (IN) Invoiced Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getMissingPurchaseStockTransferInInvoicedEntries($product_id){

		/* START Missing Purchase - Stock Transfer (IN) Invoiced Entries */
		$sql = "SELECT 'Stock Transfer' as transaction_type, 
						oop.suborder_id as reference_no, 
						oop.order_product_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id, ';', osub.invoice_prefix, osub.invoice_no) as reference_document, 
						date(osub.invoice_date) as reference_document_date, 
						(oop.piece_in_set*oop.quantity) as qty_in,
						'' as qty_out, 
						CONCAT(UPPER(ovir.purchase_firm_city), ' to ', UPPER(o.payment_city)) as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						o.gst_number as wsb_gst, 
						'' as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "order o 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.buyer_invoice_id = osub.buyer_invoice_id  
				INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
				LEFT JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.seller_id = o.customer_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id   
				WHERE osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0 
				  AND o.store_id IN (0,2,9) 
				  AND o.stock_transfer = 1 
				  AND o.franchise_id = 0 
				  AND oms3.seller_invoice_generate = 0 
				  AND oos.language_id = 1 
				  AND owpb.breakup_id IS NULL
				  AND oop.product_id = " . (int)$product_id . " 
				GROUP BY oop.order_product_id  ";
		return $sql;		
		/* END Missing Purchase - Stock Transfer (IN) Invoiced Entries */
	}

	/**
	 * Method for Missing Purchase - Sales / Stock Transfer (OUT) Un-Invoiced Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getMissingPurchaseStockTransferOutUnInvoicedEntries($product_id){

		/* START Missing Purchase - Stock Transfer (IN) Invoiced Entries */
		$sql = "SELECT IF(o.stock_transfer = 0, 'Sales', 'Stock Transfer') as transaction_type, 
						oop.suborder_id as reference_no, 
						oop.order_product_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id, ';', 'Un-invoiced')  as reference_document, 
						date(osub.date_added) as reference_document_date, 
						'' as qty_in,
						(oop.piece_in_set*oop.quantity) as qty_out, 
						IF(o.stock_transfer = 1, CONCAT(UPPER(ovir.purchase_firm_city), ' to ', UPPER(o.payment_city)), '')  as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						ovir.purchase_firm_tin_no as wsb_gst, 
						'' as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "order o 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_id = o.order_id 
				LEFT JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.zone_id = oms3.zone_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.purchase_firm_id = ovir.purchase_firm_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id 
				WHERE oop.suborder_id = osub.suborder_id 
				  AND osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND (osub.invoice_no = 0 OR osub.invoice_no IS NULL) 
				  AND (osub.buyer_invoice_id = 0 OR osub.buyer_invoice_id IS NULL) 
				  AND o.store_id IN (0,2,9) 
				  AND o.franchise_id = 0 
				  AND oop.edit_type IN ('YES','SELLER_PARTIAL','SELLER_APPROVED','SELLER_LATER_DISPATCH','DAMAGE_BY_COURIER_COMPANY') 
				  AND oms3.seller_invoice_generate = 0 
				  AND DATE(ovir.date_begin) <= DATE(NOW())
				  AND (ovir.date_end IS NULL OR DATE(ovir.date_end) >= DATE(NOW()))
				  AND ovir.status = 1 
				  AND oos.language_id = 1 
				  AND owpb.breakup_id IS NULL 
				  AND oop.product_id = " . (int)$product_id . "
				GROUP BY oop.order_product_id ";
		return $sql;		
		/* END Missing Purchase - Sales / Stock Transfer (OUT) Un-Invoiced Entries */
	}

	/**
	 * Method for Missing Purchase - Stock Transfer (IN) Un-Invoiced Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getMissingPurchaseStockTransferInUnInvoicedEntries($product_id){

		/* START Missing Purchase - Stock Transfer (IN) Un-Invoiced Entries*/
		$sql = "SELECT IF(o.stock_transfer = 0, 'Sales', 'Stock Transfer') as transaction_type, 
						oop.suborder_id as reference_no, 
						oop.order_product_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(osub.suborder_id, ';', 'Un-invoiced')  as reference_document, 
						date(osub.date_added) as reference_document_date, 
						(oop.piece_in_set*oop.quantity) as qty_in,
						'' as qty_out, 
						IF(o.stock_transfer = 1, CONCAT(UPPER(ovir.purchase_firm_city), ' to ', UPPER(o.payment_city)), '')  as stock_movement_detail,         
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR,
						oms2.nickname as wsb_firm, 
						o.gst_number as wsb_gst, 
						'' as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "order o 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.order_id = o.order_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_id = o.order_id 
				LEFT JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.seller_id = o.customer_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.zone_id = oms3.zone_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id 
				WHERE oop.suborder_id = osub.suborder_id 
				  AND osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND (osub.invoice_no = 0 OR osub.invoice_no IS NULL) 
				  AND (osub.buyer_invoice_id = 0 OR osub.buyer_invoice_id IS NULL) 
				  AND o.store_id IN (0,2,9) 
				  AND o.franchise_id = 0 
				  AND o.stock_transfer = 1 
				  AND oop.edit_type IN ('YES','SELLER_PARTIAL','SELLER_APPROVED','SELLER_LATER_DISPATCH','DAMAGE_BY_COURIER_COMPANY') 
				  AND oms3.seller_invoice_generate = 0 
				  AND DATE(ovir.date_begin) <= DATE(NOW())
				  AND (ovir.date_end IS NULL OR DATE(ovir.date_end) >= DATE(NOW()))
				  AND ovir.status = 1 
				  AND oos.language_id = 1 
				  AND owpb.breakup_id IS NULL 
				  AND oop.product_id = " . (int)$product_id . "
				GROUP BY oop.order_product_id ";
		return $sql;		
		/* END Missing Purchase - Stock Transfer (IN) Un-Invoiced Entries */
	}

	/**
	 * Method for Missing Purchase - Sales Return / Stock Transfer Return (IN) Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getMissingPurchaseStockTransferReturnInEntries($product_id){

		/* START Missing Purchase - Sales Return / Stock Transfer Return (IN) Entries*/
		$sql = "SELECT IF(o.stock_transfer = 0, 'Sales Return', 'Stock Transfer Return') as transaction_type,
						oop.suborder_id as reference_no, 
						ort.return_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(ocn.credit_note_prefix, ocn.credit_note_no) as reference_document, 
						DATE(ocn.date_added) as reference_document_date,
						ort.quantity as qty_in,
						'' as qty_out, 
						IF(o.stock_transfer = 1, CONCAT('Return From ', UPPER(o.payment_city), ' to ', UPPER(ovir.purchase_firm_city)), '')  as stock_movement_detail, 
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR, 
						oms2.nickname as wsb_firm, 
						ovir.purchase_firm_tin_no as wsb_gst, 
						'' as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "credit_note ocn 
				INNER JOIN ". DB_PREFIX. "return ort ON ort.credit_note_id = ocn.credit_note_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_product_id = ort.order_product_id 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.buyer_invoice_id = oop.buyer_invoice_id  
				INNER JOIN ". DB_PREFIX. "order o ON osub.order_id = o.order_id 
				LEFT JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.purchase_firm_id = ovir.purchase_firm_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id 
				WHERE
				  ocn.credit_note_status = 1 
				  AND osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0 
				  AND o.store_id IN (0,2,9) 
				  AND o.franchise_id = 0 
				  AND oms3.seller_invoice_generate = 0 
				  AND oos.language_id = 1 
				  AND owpb.breakup_id IS NULL 
				  AND oop.product_id = " . (int)$product_id . "
				GROUP BY ort.return_id  ";
		return $sql;		
		/* END Missing Purchase - Sales Return / Stock Transfer Return (IN) Entries */
	}

	/**
	 * Method for Missing Purchase - Stock Transfer Return (OUT) Entries
	 * @param : $product_id : Integer of product id
	 * @author: vikas, May, 2018
	 */
	public function getMissingPurchaseStockTransferReturnOutEntries($product_id){

		/* START Missing Purchase - Stock Transfer Return (OUT) Entries*/
		$sql = "SELECT 'Stock Transfer Return' as transaction_type,
						oop.suborder_id as reference_no, 
						ort.return_id as row_id, 
						oop.product_id, 
						oop.seller_sku,
						CONCAT(ocn.credit_note_prefix, ocn.credit_note_no) as reference_document, 
						DATE(ocn.date_added) as reference_document_date,
						'' as qty_in,
						ort.quantity as qty_out, 
						CONCAT('Return From ', UPPER(o.payment_city), ' to ', UPPER(ovir.purchase_firm_city))  as stock_movement_detail, 
						IF(oop.sor_product = 1, 'YES', 'NO') as is_SOR, 
						oms2.nickname as wsb_firm, 
						o.gst_number as wsb_gst, 
						'' as seller_firm, 
						oop.transfer_price_per_piece as transfer_price_per_piece, 
						oop.seller_input_tax as seller_tax, 
						oos.name as order_status,
						osub.order_status_id as order_status_id,
						oopt.name as option_name,
						CONCAT(oopt.value,' : ', (oop.quantity	 * oop.piece_in_set), ' pics' ) as option_value 
				FROM ". DB_PREFIX. "credit_note ocn 
				INNER JOIN ". DB_PREFIX. "return ort ON ort.credit_note_id = ocn.credit_note_id 
				INNER JOIN ". DB_PREFIX. "order_product oop ON oop.order_product_id = ort.order_product_id 
				INNER JOIN ". DB_PREFIX. "suborder osub ON osub.buyer_invoice_id = oop.buyer_invoice_id  
				INNER JOIN ". DB_PREFIX. "order o ON osub.order_id = o.order_id 
				LEFT JOIN ". DB_PREFIX. "wsb_purchase_breakup owpb ON owpb.product_id = oop.product_id 
				INNER JOIN ". DB_PREFIX. "seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
				INNER JOIN ". DB_PREFIX. "vat_input_rules ovir ON ovir.rule_id = osi.vat_input_rule_id  
				INNER JOIN ". DB_PREFIX. "ms_seller oms2 ON oms2.seller_id = o.customer_id 
				INNER JOIN ". DB_PREFIX. "ms_seller oms3 ON oms3.seller_id = oop.seller_id 
				INNER JOIN ". DB_PREFIX. "order_status oos ON oos.order_status_id = osub.order_status_id
				LEFT JOIN ". DB_PREFIX. "order_option oopt ON oopt.order_product_id = oop.order_product_id 
				WHERE
				  ocn.credit_note_status = 1 
				  AND osub.order_status_id > 0 AND osub.order_status_id != 2 
				  AND osub.buyer_invoice_id > 0 AND osub.invoice_no > 0 
				  AND o.store_id IN (0,2,9) 
				  AND o.stock_transfer = 1 
				  AND o.franchise_id = 0 
				  AND oms3.seller_invoice_generate = 0 
				  AND oos.language_id = 1 
				  AND owpb.breakup_id IS NULL 
				  AND oop.product_id = " . (int)$product_id . "
				GROUP BY ort.return_id  ";
		return $sql;		
		/* END Missing Purchase - Stock Transfer Return (OUT) Entries */
	}
}
