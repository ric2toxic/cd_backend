<?php


	class SetInventoryData {
		private $_db;
		private $_import_csv_id;
		
		public function __construct($db,$import_csv_id = '') {
			$this->_db = $db;
			$this->_import_csv_id = $import_csv_id;	
		}
		
		public function setComment($comment,$status, $product_ids) {
		
			
			foreach($product_ids as $product_id) {
				
				$sql = "UPDATE `" . DB_PREFIX . "inventory_import_product` SET comment = '".$comment."'
					,status = ".(int)($status)." WHERE import_product_id = ".(int)($product_id['import_product_id'])."";
				$this->_db->query($sql);
			}
		}
	}
	
?>
