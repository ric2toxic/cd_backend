
<?php
	//Author Divya Porwal
	//2017
	class InsertInventory {
		
	private $_db;
	private $_import_product_id;
	
	public function __construct($db) {
		$this->_db = $db;
	}
	
	//inserting file details into database 
	//first step for file info submission
	public function insertUploadFileDetails($file_data) {
		$values = array();
		$values[1] = $file_data;
		$values = implode("','",$values[1]);
		$values = "'".$values."'";
		$sql = "INSERT INTO `".DB_PREFIX . "inventory_import_csv` (seller_id,category_id,file_path, date_added) VALUES ($values)";
		$query = $this->_db->query($sql);
		$import_csv_id = $this->_db->getLastId();
		return $import_csv_id;
		
	}
	
	//inserting jqgrid data into file
	public function insertInitialFile($validate_data,$import_csv_id, $flag, $date_time) {
		$history = "";
		$date_added = $date_time;
		$product_ids = array();
		foreach($validate_data as $data) {
			
			$serialize_data = str_replace("'", "", json_encode($data));
			$validated = 0;
			$sql = "INSERT INTO `".DB_PREFIX . "inventory_import_product` 
				(import_csv_id,import_data,validated,date_added, history) 
				VALUES 
				('$import_csv_id','$serialize_data','$validated', '$date_time','$history')";
			$query = $this->_db->query($sql);
			array_push($product_ids,$this->_db->getLastId());			
		}
		return $product_ids;
	}
	
	
	//get prvious history 
	public function getHistory($product_id) {

		$sql = "SELECT history FROM `" . DB_PREFIX . "inventory_import_product` WHERE  import_product_id = '" .(int)($product_id). "' ";
		$query = $this->_db->query($sql);
		
		return $query->row['history'];		
		
	}
	
	//get previous data 
	public function getLastData($product_id) {
		$sql = "SELECT import_data
				FROM `" . DB_PREFIX . "inventory_import_product` WHERE  import_product_id = '" .(int)($product_id). "' ";
		$query = $this->_db->query($sql);
		return $query->row['import_data'];
	}
	
	//update new history
	public function updateNewHistory($new_history, $product_id) {
		$new_history = str_replace("'", "", $new_history);

		$sql = "UPDATE `" . DB_PREFIX . "inventory_import_product` SET history = '" .$this->_db->escape(trim($new_history)). "'
				WHERE  import_product_id = $product_id";

		$query = $this->_db->query($sql);
	}
	
	//get previous date 
	public function getLastDate($product_id) {
		$sql = "SELECT date_added
				FROM `" . DB_PREFIX . "inventory_import_product` WHERE  import_product_id = '" .(int)($product_id). "' ";
		$query = $this->_db->query($sql);
		return $query->row['date_added'];
		
	}
	
	//update with new date 
	public function updateNewDate($date_added, $product_id) {
		$sql = "UPDATE `" . DB_PREFIX . "inventory_import_product` SET date_added = '" .$this->_db->escape(trim($date_added)). "'
				WHERE  import_product_id = $product_id";
		$query = $this->_db->query($sql);
	}
	
	//update new import data
	public function updateNewImportData($validate_data,$product_id) {
		
		$sql = "UPDATE `" . DB_PREFIX . "inventory_import_product` SET import_data = '" .$this->_db->escape(trim($validate_data)). "'
				WHERE  import_product_id = ".(int)($product_id)."";
		$query = $this->_db->query($sql);
	}
	
	//update the validator
	public function updateValidate($validated,$product_id) {
		$sql = "UPDATE `" . DB_PREFIX . "inventory_import_product` SET validated = '" .(int)($validated). "'
				WHERE  import_product_id = ".(int)($product_id)."";
		$query = $this->_db->query($sql);
	}
	
	public function deleteProduct($product_id) {
		$sql = "DELETE FROM `" . DB_PREFIX . "inventory_import_product` WHERE import_product_id = '" .(int)($product_id). "'";
		$query = $this->_db->query($sql);
	}
	
	//insert revisions of data into database upon click of submit button in seller inventory
	public function insertData($import_csv_id,$final_data,$flag,$date_time,$product_ids,$product_ids_old) {
		
		$date_added = $date_time;
		$deleted_products = array();
		$deleted_products = array_diff($product_ids_old,$product_ids);
		
		foreach($deleted_products as $delete) {
			$this->deleteProduct($delete);
		}
	
		
		foreach($final_data as $key=>$data) {
			$serialize_data = str_replace("'", "",json_encode($data));
			$history = $this->getHistory($product_ids[$key]);
			$last_validated_data = $this->getLastData($product_ids[$key]);
			$last_date = $this->getLastDate($product_ids[$key]);
			if($history == "") {
				$new_history = array(
									$last_date => $last_validated_data,
									);
				$new_history = json_encode($new_history);
			} else {
				$new_history = array(
									$last_date => $history,
									);	
				$new_history = json_encode($new_history);
			}

			//updating new history
			$this->updateNewHistory($new_history,$product_ids[$key]);
			//updating new date
			$this->updateNewDate($date_added, $product_ids[$key]);
			//updating new import data
			$this->updateNewImportData($serialize_data,$product_ids[$key]);
		}
	}

	public function insertFilterWithIds($filter_group,$filter_ids,$date_time,$product_ids) {
		
		$date_added = $date_time;
		foreach($product_ids as $key=>$product) {
			$history = $this->getHistory($product);
			$last_validated_data = $this->getLastData($product);
			$last_date = $this->getLastDate($product);
			$new_history = $history;
			$new_history .= ",";
			$new_history .= $last_validated_data;
			$new_history .= ",";
			$new_history .= $last_date;
			
			//updating new history
			$this->updateNewHistory($new_history,$product);
			$decoded_array = json_decode($last_validated_data,true);
			//initializing filter_id and filter name in database
			for($k = 0; $k < sizeof($filter_group); $k++) {
				$decoded_array[$filter_group[$k]] = $filter_ids[$k][$key];
			}

			$serialize_data = json_encode($decoded_array);

			//updating new date
			$this->updateNewDate($date_added, $product);
			//updating new import data
			$this->updateNewImportData($serialize_data,$product);
		}
	}
	
	public function insertSet($set_type, $set_type_data,$product_ids, $date_time,$exists_other_set) {
		
		$date_added = $date_time;
	   
		foreach($product_ids as $key=>$product_id) {
			if($set_type == 3) {
				//updating validate after not specific set submission (setype = 0 for size set, 1 for color set , 2 for free set, 3 not specific)
				$validated = 1;
				$this->updateValidate($validated,$product_id);
			}
			if(($set_type == 0 ||  $set_type == 1 || $set_type == 2) && $exists_other_set == 0) {
				//updating validate after submission 
				$validated = 1;
				$this->updateValidate($validated,$product_id);
			} 
			
		
			
			$json_data = json_encode($set_type_data[$key]);
			
			$history = $this->getHistory($product_id);
			$last_validated_data = $this->getLastData($product_id);
			$last_date = $this->getLastDate($product_id);

			$new_history = $history;
			$new_history .= ",";
			$new_history .= $last_validated_data;
			$new_history .= ",";
			$new_history .= $last_date;
			
			//updating new history
			$this->updateNewHistory($new_history,$product_id);
			//updating new date
			$this->updateNewDate($date_added, $product_id);
		
			//updating new import data
			$this->updateNewImportData($json_data,$product_id);
			
		}
		$sql = "DELETE FROM `" . DB_PREFIX . "inventory_import_product` WHERE import_data = 'null'";
		$this->_db->query($sql);
	}
		
}


?>
