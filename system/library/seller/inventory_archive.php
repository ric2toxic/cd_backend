<?php

	class InventoryArchive {
		
		private $_product_change_log;
		
		public function __construct($registry) {
			
			if(method_exists($registry,'get')){
				$this->_db = $registry->get('db');
			}
			else{
				$this->_db = $registry->db;
			}

			$this->_product_change_log = new ProductChangeLog($registry);
		}
		
		
		/**
		 * public method to update records in oc_products,
		 * upon archivin products
		 * @input : product_ids (comma separated string of product_id),
		 * archive_option   -1 - archived
		 ***************** - 0 - unset archive
		 * @output : 0/1 (0=> query failed) , (1=>query ran successfully)
		 * */
		public function setInventoryArchive($archive_option,$product_ids) {

			$sql = "SELECT product_id, is_archived,sku 
				FROM " . DB_PREFIX . "product
			    WHERE product_id IN (".$product_ids.") ";
			$query = $this->_db->query($sql);	    
			
			foreach($query->rows as $pid_is_archived){
				$changes_data=array(
								'is_archived' => array(
									'old_value' => $pid_is_archived['is_archived'],
									'new_value' => $archive_option,
									'sku'		=> $pid_is_archived['sku']
								)
				);
				$this->_product_change_log->sellerProductChangeLogs($pid_is_archived['product_id'], $changes_data);
			}

			$sql = "UPDATE " . DB_PREFIX."product SET is_archived = ".$archive_option.",
					archived_date = NOW()
					WHERE product_id IN (
					".$product_ids.")";
			$sql_query = $this->_db->query($sql);
			if($sql_query) {
				return 1;
			} else {
				return 0;
			}
			
		}
		
		/** public method to check if inventory is archved or not
		 * @input : product_is (int)
		 * @output : is_archive (int) => values(0/1) 
		 * */
		public function getInventoryArchive($product_id) {
				
			$sql = "SELECT  is_archived FROM " . DB_PREFIX."product WHERE product_id = ".$product_id."";
			return $this->_db->query($sql)->row['is_archived'];
		}
		
		
	}
?>
