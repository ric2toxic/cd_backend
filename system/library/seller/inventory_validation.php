<?php
	
	//Author Divya Porwal
	// February 2017
	class InventoryValidation {
		
		// array for storing xlsx headers
		private $values_array = array("SKU Code", "Transfer Price", "AvailableSets", "Weight of a Piece (in gm)",
									 "Any additional Comments", "Tax");
		
		//removed elements from the csv		 
		//"Size Set / Color Set","Size", "Set Description", "Pieces in Set", "Images"		
							 
		// array for storing sku_codes 
		private $_sku_code_arr = array();
		
		// array for storing size_color_set 
	//	private $_size_color_set_arr = array();
		
		// array for storing size 
		private $_size_arr = array();
		
		// array for storing set_description 	
		//private $_set_description_arr = array();
		
		// array for storing pieces_in_set 
	//	private $_pieces_in_set_arr = array();
		
		// array for storing transfer_price 
		private $_transfer_price_arr = array();
		
		//array for storing available_sets
		private $_available_sets_arr = array();
		
		//array for storing weight
		private $_weight_of_piece_arr = array();
		
		//array for storing comment
		private $_comment_arr = array();
		
		//array for storing images
		//private $_images_arr = array();
		
		//array for storing tax
		private $_tax_arr = array();
		
		//array for storing errors
		private $_errors_arr =  array();
		
		//array for storing warnings
		private $_warnings_arr = array();
		
		//global min_weight
		private $_min_weight;
		
		//global max_weight
		private $_max_weight;
		
		//seller tax with input
	//	private $_seller_tax_ip = array('ST','DL');
		
		//seller tax with no input
	//	private $_seller_tax_with_no_ip = array('JP');
		
		//variable for commission
	//	private $_commission = 10;
		
		
		//array for storing data
		
		/*private $_data = array("SKU Code","Size Set / Color Set","Size", "Set Description", 
									"Pieces in Set", "Transfer Price", "AvailableSets", "Weight of a Piece",
									 "Any additional Comments", "Tax", "Images");*/
		
		//friends array for php unit testing
		private $_friends = array ();
		
		//allowed classes for php unit testing
		private $_allowed_classes = array('ValidationTest');
		
		//storing dictionary values
		private $_dictionary = array();
		
		private $_seller_id;
		private $_db;
		private $_category;
		//private $_config;
		private $_final_arr = array();
		private $_data = array();
		private $_registry;

		/**
		 * Constructor
		 * @param $db (database object)
		 * @param $seller_id (int)
		 * @param $category(int)
		 * @author Divya, 2017
		 */ 
		public function __construct($registry, $seller_id, $category) {
			$this->_seller_id = $seller_id;
			$this->_registry = $registry;
			$this->_db = $registry->db;
			$this->_category = $category;
			$this->_max_weight = 2000; //global max_weight
			$this->_min_weight = 100; //global min_weight
			
		}
		
		/**
		 * Get magic method
		 * to be used for unit testing only
		 */
		/*public function __get($property) {
			echo "entered get";
			$trace = debug_backtrace();
		    $class = $trace[1]['class'];
			if(in_array( $class, $this->_allowed_classes) && in_array($property,$this->_friends)  && method_exists($this,$property)) {
				return $this->$property($this->_data);
			} else {
				trigger_error('Cannot access private property ' . __CLASS__ . '::$' . $property, E_USER_ERROR);
			}
			
		}
		
		/** Set Magic Method
		 * to be used for unit testing only
		public function __set($property,$data) {
			echo "entered set";
			$trace = debug_backtrace();
		    $class = $trace[1]['class'];
		    if( in_array( $class, $this->_allowed_classes) && !in_array($property,$this->_friends) && method_exists($this,$property)){
				$this->_data = $data;
				$this->_friends[] = $property;
			}
			else{
				trigger_error('Cannot access private property ' . __CLASS__ . '::$' . $property, E_USER_ERROR);
			}
		}	
		*/
		
		//Public function to call validators
		//@param $data(array)
		public function callValidators($data) {
			
			$rules = $data['rules'];
			$sheet_data = $data['sheet_data'];
			$row_count = $data['row_count'];
			
			foreach ($rules as $arr) {
				$func = $arr;
				
				$this->$func($sheet_data);
				
			}
			
			for($i = 0; $i < $row_count; $i++) {
				if(!empty($data['sheet_data'][$i])){
					foreach($data['sheet_data'][$i] as $key=>$value) {
						$data['sheet_data'][$i][$key] = $this->_data[$key][$i];
					}	
				}	
				
			}
			$this->_final_arr = $this->_manipulate_data($data,$this->_errors_arr);

			return $this->_final_arr;
		}

	
		private function _customize_errors($errors_data,$row_count) {
			$customized_arr = array();
			$row_count = $row_count-3; // first three rows are headers, example and format rows, hence ignoring
			
			foreach ($errors_data as $key => $value) {
				for($k=0;$k < $row_count; $k++) {
					if(isset($errors_data[$key][$k])) {
						$customized_arr[$k][$key] = $errors_data[$key][$k];	
					}
				}
			}
			
			return $customized_arr;
		}
		
		//private function to manipulate the data (adding errors and the main data)
		private function _manipulate_data($data,$errors_data) {
			$sheet_data = $data['sheet_data'];

			$final_array = array();
			$customized_arr = array();
			$row_count = $data['row_count'];
			$col_count = $data['col_count'];
			$flag = '';
			$warning = '';
			$output = array();
			$errors_array = array(); //errors output on a different page
			
			foreach($errors_data as $key=>$value) {
				for($i = 0; $i < sizeof($value); $i++) {
					if($value[$i] != "Please recheck the weight"  && $value[$i] != "" ) {
						$flag = 1; // error is found in sheet
						break;
					} else if($value[$i] == "" || $value[$i] == "Please recheck the weight") {
						$flag = 0; // error still not found
					} 
					if( $value[$i] == "Please recheck the weight") {
						$warning = 1; // error still not found
					} 
				} if($flag == 1)
					break; // break out of the loop once the error is found
			}
			
			$customized_arr = $this->_customize_errors($errors_data,$row_count);	

			/**
			 * merging customised errors array and sheet data
			 */
			 for ($i=0; $i<count($sheet_data); $i++) {
				$final_array[] = $sheet_data[$i]; //adding the sheet data as well as errors data
				$final_array[] = $customized_arr[$i];
			}
			$output['final_array'] = $final_array;
			$output['flag'] = $flag; //sending flag parameter
			$output['warning'] = $warning;
			
			return $output;
			
		}
		
		
		//database checking for duplicate skucode
		public function getProductIdBySkuAndSeller($sku_code) {
			$seller_id = $this->_seller_id;

			//using db->escape to escape single quote
			$q = "SELECT p.*
			  FROM ".DB_PREFIX."product p
			  LEFT JOIN " . DB_PREFIX . "ms_product mp ON (p.product_id = mp.product_id) 
			  WHERE p.sku='". $this->_db->escape(trim($sku_code))."' AND mp.seller_id = '".$seller_id."'";			  
			$query = $this->_db->query($q);
			return $query->row;
		}
		
		//getting seller names from seller id
		public function getSellerName() {
			$seller_id = $this->_seller_id;
			$q = "SELECT nickname
			  FROM ".DB_PREFIX."ms_seller  WHERE
			  seller_id = '".(int)($seller_id)."'";
			$query = $this->_db->query($q);
			return $query->row;
		}
		
		//getting seller company from seller id
		public function getSellerCompany() {
			$seller_id = $this->_seller_id;
			$q = "SELECT company FROM ".DB_PREFIX."ms_seller WHERE seller_id = '".(int)($seller_id)."'";
			$query = $this->_db->query($q);
			return $query->row;
		}
		
		//getting dictionary words
	/*	public function getDictionary() {
			$q = "SELECT * FROM ".DB_PREFIX."search_dictionary";
			$query = $this->_db->query($q);
			return $query->rows;
			//need to write code for dictionary 
		}
		*/
		
		//getting weight range for category id
		public function getWeightRange() {
			$q = "SELECT min_weight, max_weight FROM ".DB_PREFIX."category WHERE category_id = '".$this->_category."'" ;
			$query = $this->_db->query($q);
			return $query->row;	
		}
		
		
		//checking if category is taxable or not!
		public function checkTax() {
			$q = "SELECT taxable FROM ".DB_PREFIX."category WHERE category_id = '".$this->_category."'" ;
			$query = $this->_db->query($q);
			return $query->row;	
		}
			
		//setter for skucode
		private function _set_skucode($sheet_data){
			for($i=0;$i<count($sheet_data); $i++){
				$this->values_array['SKU Code'] = preg_replace('/[^A-Za-z0-9\-\_\s\'\"\&\(\)\[\]\}\{\^\$\#\@\!\~]/', '',utf8_encode(trim($sheet_data[$i]['SKU Code'])));
				array_push($this->_sku_code_arr,str_replace("'","",$this->values_array['SKU Code']));
			}
			
			array_push($this->_data,$this->_sku_code_arr);
			$this->_data['SKU Code'] = $this->_data[0];
			unset($this->_data[0]);
			//print_r($this->_data);

		}
		
		//setter for size_color_set
		/*private function _set_size_color_set($sheet_data) {
			
			for($i=0;$i<count($sheet_data); $i++){
				$this->values_array['Size Set / Color Set'] = utf8_encode($sheet_data[$i]['Size Set / Color Set']);
				array_push($this->_size_color_set_arr,$this->values_array['Size Set / Color Set']);
			}
			array_push($this->_data,$this->_size_color_set_arr);		
		}
		
		//setter for size
		private function _set_size($sheet_data) {
			for($i=0;$i<count($sheet_data); $i++){
				$this->values_array['Size'] = utf8_encode($sheet_data[$i]['Size']);
				array_push($this->_size_arr,$this->values_array['Size']);
			}
			array_push($this->_data,$this->_size_arr);		
		}
		
		//setter for description
		private function _set_description($sheet_data) {
			for($i=0;$i<count($sheet_data); $i++){
				$this->values_array['Set Description'] = utf8_encode($sheet_data[$i]['Set Description']);
				array_push($this->_set_description_arr,$this->values_array['Set Description']);
			}
			array_push($this->_data,$this->_set_description_arr);			
		}
		
		//setter for piece_in_set
		private function _set_pieces_in_set($sheet_data) {
			for($i=0;$i<count($sheet_data); $i++){
				$this->values_array['Pieces in Set'] = utf8_encode($sheet_data[$i]['Pieces in Set']);
				array_push($this->_pieces_in_set_arr,$this->values_array['Pieces in Set']);
			}
			array_push($this->_data,$this->_pieces_in_set_arr);	
		}
		*/
		//setter for transfer_price
		private function _set_transfer_price($sheet_data) {
			for($i=0;$i<count($sheet_data); $i++){
				$this->values_array['Transfer Price'] = rtrim(utf8_encode($sheet_data[$i]['Transfer Price']), '.');
			//	preg_replace('[a]','',utf8_encode($sheet_data[$i]['Transfer Price']));
				array_push($this->_transfer_price_arr,$this->values_array['Transfer Price']);
			}
			array_push($this->_data,$this->_transfer_price_arr);	
			$this->_data['Transfer Price'] = $this->_data[1];
			unset($this->_data[1]);		

		}
		
		//setter for available_sets
		private function _set_available_sets($sheet_data) {
			for($i=0;$i<count($sheet_data); $i++){
			
				if($sheet_data[$i]['AvailableSets'] == "") {
					$this->values_array['AvailableSets'] = 0;
				} else {
					$this->values_array['AvailableSets'] = $sheet_data[$i]['AvailableSets'];
				}
				if(preg_match_all('/./',$sheet_data[$i]['AvailableSets'])) {
					$this->values_array['AvailableSets'] = rtrim(utf8_encode($sheet_data[$i]['AvailableSets']), '.');
				}
				if($this->values_array['AvailableSets'] == "") {
					$this->values_array['AvailableSets'] = 0;
				}
				if($this->values_array['AvailableSets'] == "'") {
					$this->values_array['AvailableSets'] = 0;
				}
				array_push($this->_available_sets_arr,$this->values_array['AvailableSets']);
			}
			array_push($this->_data,$this->_available_sets_arr);	
			$this->_data['AvailableSets'] = $this->_data[2];
			unset($this->_data[2]);		
	
		}
		
		//setter for weight_of_piece
		private function _set_weight_of_piece($sheet_data) {
			$k = 0;
			for($i=0;$i<count($sheet_data); $i++){
				
				$this->values_array['Weight of a Piece (in gm)'] = rtrim(utf8_encode($sheet_data[$i]['Weight of a Piece (in gm)']), '.');
				if((int)($this->values_array['Weight of a Piece (in gm)']) > $this->_max_weight || (int)($this->values_array['Weight of a Piece (in gm)']) < $this->_min_weight) {
					$this->_warnings_arr['Weight of a Piece (in gm)'][$i] = "Please recheck this weight";
				}
				array_push($this->_weight_of_piece_arr,$this->values_array['Weight of a Piece (in gm)']);
			}
			//echo "<pre>";
			//print_r($this->_warnings_arr);
			array_push($this->_data,$this->_weight_of_piece_arr);	
			$this->_data['Weight of a Piece (in gm)'] = $this->_data[3];
			unset($this->_data[3]);					
		}
		
		//setter for comment
		private function _set_comment($sheet_data) {
			for($i=0;$i<count($sheet_data); $i++){	
				
				$this->values_array['Any additional Comments'] = preg_replace('/[^A-Za-z0-9\-\s\'\"]/', '', utf8_encode($sheet_data[$i]['Any additional Comments'])); 
				array_push($this->_comment_arr,$this->values_array['Any additional Comments']);
			}
			
			array_push($this->_data,$this->_comment_arr);	
			$this->_data['Any additional Comments'] = $this->_data[4];
			unset($this->_data[4]);		
		}
		
		//setter for tax
		private function _set_tax($sheet_data) {
			for($i=0;$i<count($sheet_data); $i++) {
				if(empty($sheet_data[$i]['Tax'])) {
					$this->values_array['Tax'] = 0;
				} else {
					$this->values_array['Tax'] = $sheet_data[$i]['Tax'];
				}
				array_push($this->_tax_arr,$this->values_array['Tax']);
			}
			array_push($this->_data,$this->_tax_arr);		
			$this->_data['Tax'] = $this->_data[5];
			unset($this->_data[5]);	
		}
		

		//getter for skucode
		private function _get_skucode() {
			
			return $this->_data['SKU Code'];
			
		}
		
		//getter for size_color_set
	/*	private function _get_size_color_set() {
			
			return $this->_size_color_set_arr;
			
		}
		
		//getter for size
		private function _get_size() {
			
			return $this->_size_arr;
			
		}
		
		//getter for description
		private function _get_description() {
			
			return $this->_set_description_arr;
			
		}
		
		//getter for piece_in_set
		private function _get_pieces_in_set() {
			
			return $this->_pieces_in_set_arr;
			
		}
		
		*/
		//getter for transfer_price
		private function _get_transfer_price() {
			
			return $this->_data['Transfer Price'] ;
			
		}
		
		//getter for available_sets
		private function _get_available_sets() {
			
			return$this->_data['AvailableSets'];
			
		}
		
		//getter for weight_of_piece
		private function _get_weight_of_piece() {
			
			return $this->_data['Weight of a Piece (in gm)'];
			
		}
		
		//getter for comments
		private function _get_comment() {
			
			return $this->_data['Any additional Comments'];
			
		}
		
		//getter for tax
		private function _get_tax() {
			
			return $this->_data['Tax'];
			
		}
		

		public function test_hash_minus($sku_data) {
			$test_hash_minus = array();
			foreach($sku_data as $var) {
				$replaced_str = preg_replace('/[-_]+/',"",$var);
				array_push($test_hash_minus,strtolower($replaced_str));
				
			}
			return $test_hash_minus;
		}
			
		//validate function for sku_code
		private function _validate_sku($sheet_data){
			$sku_data = array();
			$this->_set_skucode($sheet_data);
			$sku_data = $this->_get_skucode();
			$i = 0;

			$sku_no_hash_arr = $this->test_hash_minus($sku_data);
			
			foreach($sku_data as $var) {
				$var = trim($var);
				$regexp = "/^[a-zA-Z0-9-_]+$/";
				$pdata = $this->getProductIdBySkuAndSeller($var);
				if(!preg_match("/^[-a-zA-Z_0-9]*$/",$var)) {
						$msg = "invalid sku code.Only alphabets,Numbers,underscore(_) and dashes(-) are allowed.";
						$this->_errors_arr['SKU Code'][$i] = $msg;
						$i++;
				} else if(empty($var)){
						$msg = "invalid sku code. Cannot be empty";
						$this->_errors_arr['SKU Code'][$i] = $msg;
						$i++;
				} else if(!empty($pdata)){
						$msg = "sku code already exists";
						$this->_errors_arr['SKU Code'][$i] = $msg;
						$i++;
				} else if(preg_match("/^[-_]+$/",$var)) {
						$msg = "invalid format for sku code";
						$this->_errors_arr['SKU Code'][$i] = $msg;
						$i++;
				} else if((preg_match("/[-_]+/",$var) && ($replaced_str = preg_replace('/[-_]+/',"",$var))
						&& (array_count_values($sku_no_hash_arr)[strtolower($replaced_str)] > 1) )) {
						//finding duplicate values without -,_, and case insensitive
						$msg = "duplicate entries of SKU Code cannot exist";
						$this->_errors_arr['SKU Code'][$i] = $msg;
						$i++;	
				} else if(array_count_values(array_map('strtolower',$sku_data))[trim(strtolower($var))] > 1) {
					    //case insentive search for sku codes
						$msg = "duplicate entries of SKU Code cannot exist";
						$this->_errors_arr['SKU Code'][$i] = $msg;
						$i++; 
				} else {
						$msg = "";
						$this->_errors_arr['SKU Code'][$i] = $msg;
						$i++;
				} 
			}
			//(array_count_values(array_map(function($val){ return preg_replace('/[-_]+/',"",$val); },array_map('strtolower', $sku_data)))[strtolower($var)] > 1) || !preg_match("/[-_]+/",$var) && 
			//(array_count_values(array_map(function($val){ return preg_replace('/[-_]+/',"",$val); },array_map('strtolower', $sku_data)))[strtolower($var)] > 1)
			return $this->_errors_arr['SKU Code'];
		}
			
			
		//validate function for size_or_color	
		/*private function _validate_size_or_color($sheet_data) {
			$size_color_set_data = array();
			$this->_set_size_color_set($sheet_data);
			$size_color_set_data = $this->_get_size_color_set();
			$i=0;
			foreach($size_color_set_data as $var) {
				if(empty($var)){
					$msg = "Size/Color set cannot be empty.";
					$this->_errors_arr['Size Set / Color Set'][$i] = $msg;
					$i++;
				}
				else if(preg_match("/^color set*$/i",$var) or preg_match("/^size set*$/i",$var)) {
					$msg = "";
					$this->_errors_arr['Size Set / Color Set'][$i] = $msg;
					$i++;
				} else {
					$msg = "Invalid size/color set. Please check the example for reference";
					$this->_errors_arr['Size Set / Color Set'][$i] = $msg;
					$i++;	
				}
			}
			return $this->_errors_arr['Size Set / Color Set'];			
		}
		
		
		//validate function for size	
		private function _validate_size($sheet_data) {
			
			$size_arr_data = array();
			$this->_set_size($sheet_data);
			$size_arr_data = $this->_get_size();
			if(empty($this->_size_color_set_arr)) {
				$this->_set_size_color_set($sheet_data);
			}
			
			$k=0;
			for($i = 0; $i < sizeof($size_arr_data); $i++){
				if(preg_match("/^color set*$/i",$this->_size_color_set_arr[$i])) {
					if((int)($size_arr_data[$i])) {
						$msg = "";
						$this->_errors_arr['Size'][$k] = $msg;
						$k++;
					} else if(!isset($size_arr_data[$i])) {
						$msg = "Invalid size. This field cannot be left blank.";
						$this->_errors_arr['Size'][$k] = $msg;
						$k++;
					} else {
						$msg = "Invalid size. This field must be numeric only.";
						$this->_errors_arr['Size'][$k] = $msg;
						$k++;
					}
				} else if(preg_match("/^size set*$/i",$this->_size_color_set_arr[$i])) {
					if(isset($size_arr_data[$i])) {
						$msg = "Invalid size. This field must be empty for size set.";
						$this->_errors_arr['Size'][$k] = $msg;
						$k++;
					} else {
						$msg = "";
						$this->_errors_arr['Size'][$k] = $msg;
						$k++;
					}
				}
			}
			return $this->_errors_arr['Size'];
		}
	
	
		
		
		//validate function for set_description
		private function _validate_set_description($sheet_data) {
			
			$set_description_arr_data = array();
			$error = array();
			$this->_set_description($sheet_data);
			$set_description_arr_data = $this->_get_description();
			if(empty($this->_size_color_set_arr)) {
				$this->_set_size_color_set($sheet_data);
				$this->_set_pieces_in_set($sheet_data);
			}
			$pieces = array();
			$pieces = $this->_set_pieces_in_set($sheet_data);
			$k = 0;
			
			for($i = 0; $i < sizeof($set_description_arr_data); $i++) {
				
				if(empty($set_description_arr_data[$i])) {
					$msg = "Invalid description. This field cannot be left blank";
					$this->_errors_arr['Set Description'][$k] = $msg;
					$k++;
				} else if(preg_match("/^color set*$/i",$this->_size_color_set_arr[$i])) {
					$set_description_arr_data[$i] = trim($set_description_arr_data[$i]);
					$color_set = explode(",",$set_description_arr_data[$i]);
					$size_of_color_set = sizeof($color_set);
					
					if(preg_match('/[0-9]/', $set_description_arr_data[$i])) {
						$msg = "Numeric fields not allowed for color set. Please ensure colors";
						$this->_errors_arr['Set Description'][$k] = $msg;
						$k++;
					} else if($size_of_color_set != (int)($this->_pieces_in_set_arr[$i])) {
						$msg = "Number of colors do not match the number of pieces.";
						$this->_errors_arr['Set Description'][$k] = $msg;
						$k++;
					} else {
						$msg = "";
						$this->_errors_arr['Set Description'][$k] = $msg;
						$k++;
					}
				} else if(preg_match("/^size set*$/i",$this->_size_color_set_arr[$i])) {
					$set_description_arr_data[$i] = trim($set_description_arr_data[$i]);
					$size_set = explode(",",$set_description_arr_data[$i]);
					if(preg_match("/\w/", $set_description_arr_data[$i]) && !is_numeric($set_description_arr_data[$i])) {
						$msg = "Alphabet fields not allowed for size set. Please ensure numeric sizes";
						$this->_errors_arr['Set Description'][$k] = $msg;
						$k++;	
					} else if(sizeof($size_set)!= (int)($this->_pieces_in_set_arr[$i])) {
						$msg = "Number of colors do not match the number of pieces";
						$this->_errors_arr['Set Description'][$k] = $msg;
						$k++;
					} else {
						$msg = "";
						$this->_errors_arr['Set Description'][$k] = $msg;
						$k++;
					}
				}
			}
			return($this->_errors_arr['Set Description']);

		}
		
		//validate function for pieces_in_set
		private function _validate_pieces_in_set($sheet_data) {
			
			$pieces_in_set_arr_data = array();
			$error = array();
			$this->_set_pieces_in_set($sheet_data);
			$pieces_in_set_arr_data = $this->_get_pieces_in_set();
			$k=0;
			foreach($pieces_in_set_arr_data as $var){
				if((int)($var)){
					$msg = "";
					$this->_errors_arr['Pieces in Set'][$k] = $msg;
					$k++;
				} else {
					$msg = "Invalid entry. This field must be numeric only";
					$this->_errors_arr['Pieces in Set'][$k] = $msg;
					$k++;
				}
			}
			return $this->_errors_arr['Pieces in Set'];
		}
		
		*/
		
		//validate function for validate_price
		private function _validate_price($sheet_data) {
			
			$transfer_price_arr_data = array();
			$error = array();
			$this->_set_transfer_price($sheet_data);
			$transfer_price_arr_data = $this->_get_transfer_price();
			$k=0;
			foreach($transfer_price_arr_data as $var){	
				$var = trim($var);	
				
				if(is_numeric($var) && (int)($var) > 0){
					$msg = "";
					//Please ensure that price has VAT/CST included in it";
					$this->_errors_arr['Transfer Price'][$k] = $msg;
					$k++;
				} else if(empty($var)) {
					$msg = "Invalid entry. This field cannot be left blank or zero";
					$this->_errors_arr['Transfer Price'][$k] = $msg;
					$k++;
				} else {
					$msg = "Invalid entry. This field must be positive number only";
					$this->_errors_arr['Transfer Price'][$k] = $msg;
					$k++;
				}
			}
			return $this->_errors_arr['Transfer Price'];
		}
		
		
		//validate function for available_sets
		private function _validate_available_sets($sheet_data) {
			
			$available_sets_arr_data = array();
			$error = array();
			$this->_set_available_sets($sheet_data);
			$available_sets_arr_data = $this->_get_available_sets();
			$k=0;
			foreach($available_sets_arr_data as $var){
				$var = trim($var);
				if($var == (int)($var) && $var >= 0){
					$msg = "";
					$this->_errors_arr['AvailableSets'][$k] = $msg;
					$k++;
				} else if(empty($var)) {
					$msg = "Invalid entry. This field cannot be left blank";
					$this->_errors_arr['AvailableSets'][$k] = $msg;
					$k++;
				} else if(is_float($var)) {
					$msg = "Invalid entry.";
					$this->_errors_arr['AvailableSets'][$k] = $msg;
					$k++;
					
				} else if (!is_numeric($var)){
					$msg = "Invalid entry. This field must be numeric only";
					$this->_errors_arr['AvailableSets'][$k] = $msg;
					$k++;
				} else {
					$msg = "Invalid entry. This field must be nonnegative integer only";
					$this->_errors_arr['AvailableSets'][$k] = $msg;
					$k++;
				}
			}
			return $this->_errors_arr['AvailableSets'];
		}
		
		
		//validate function for weight_of_piece
		private function _validate_weight($sheet_data){
			
			$weight_arr_data = array();
			$error = array();
			$this->_set_weight_of_piece($sheet_data);
			$weight_arr_data = $this->_get_weight_of_piece();
			$category = $this->_category;
			$range = $this->getWeightRange();
			
			/**
			 *
				category_min = 0, use global min_Weight
				category_max = 0, use global max_Weight
			* */
			
			$min_weight = (float)$range['min_weight'] ? (float)$range['min_weight'] :(float)$this->_min_weight;
			$max_weight = (float)$range['max_weight'] ? (float)$range['max_weight'] : (float)$this->_max_weight;
		
	
			$k=0;
			foreach($weight_arr_data as $var){	
				
				if(empty($var) || (int)$var === 0) {
					$msg = "Invalid entry. This field cannot be left blank or zero";
					$this->_errors_arr['Weight of a Piece (in gm)'][$k] = $msg;
					$k++;
				} else if (!is_numeric($var)){
					$msg = "Invalid entry. This field must be numeric only";
					$this->_errors_arr['Weight of a Piece (in gm)'][$k] = $msg;
					$k++;
				} else if((int)($var) > $max_weight || (int)($var) < $min_weight) {
					$msg = "Please recheck the weight";
					$this->_errors_arr['Weight of a Piece (in gm)'][$k] = $msg;
					$k++;
				} else {
					$msg = "";
					$this->_errors_arr['Weight of a Piece (in gm)'][$k] = $msg;
					$k++;
				}
			}
			return $this->_errors_arr['Weight of a Piece (in gm)'];	
		}
		
		
		//validate function for comments
		private function _validate_comment($sheet_data) {
			
			$comment_arr_data = array();
			$error = array();
			$this->_set_comment($sheet_data);
			$comment_arr_data = $this->_get_comment();
			$seller_company = $this->getSellerCompany();
			$seller_name = $this->getSellerName();
			$name = $seller_name['nickname'];
			$company = $seller_company['company'];
		//	$this->_dictionary = $this->getDictionary();
		//	$dictionary = $this->_dictionary;
			$k = 0;
			foreach($comment_arr_data as $var) {
				$var1 = $var;
				$var1 = " " . $var1;
				$var = explode(" ",$var);
				if(stripos($var1,$name)) {
					$msg = "Personal information like Name is not allowed";
					$this->_errors_arr['Any additional Comments'][$k] = $msg;
					$k++; 
				} else if(stripos($var1,$company)) {
					$msg = "Personal information like company name is not allowed";
					$this->_errors_arr['Any additional Comments'][$k] = $msg;
					$k++; 
				} else if(preg_match_all('/[0-9][\-|\s][0-9][\-|\s][0-9][\-|\s][0-9][\-|\s][0-9][\-|\s][0-9][\-|\s][0-9][\-|\s][0-9][\-|\s][0-9][\-|\s][0-9]|[0-9]{3}[\s|\-][0-9]{6}|[0-9]{3}[\s|\-][0-9]{3}[\s|\-][0-9]{4}|[0-9]{9}|[0-9]{3}[\-|\s][0-9]{3}[\-|\s][0-9]{4}|[0-9]{2}[\-|\s][0-9]{2}[\-|\s][0-9]{2}[0-9]{2}[\-|\s][0-9]{2}|[0-9]{5}[\-|\s][0-9]{5}/', $var1)) {
					$msg = "Phone number is not allowed";
					$this->_errors_arr['Any additional Comments'][$k] = $msg;
					$k++;
				} else if(filter_var($var1, FILTER_VALIDATE_EMAIL) === true){
					$msg = "Email Id is not allowed";
					$this->_errors_arr['Any additional Comments'][$k] = $msg;
					$k++;
				} else {
					$msg = "";
					$this->_errors_arr['Any additional Comments'][$k] = $msg;
					$k++;
				}
			}
			return $this->_errors_arr['Any additional Comments'];	
		}
		
		//validate function for tax
		private function _validate_tax($sheet_data) {
			
			$tax_arr_data = array();
			$error = array();
			$tax_rates = array();
			$this->_set_tax($sheet_data);
			$tax_arr_data = $this->_get_tax();	
			$permissible_tax_class_ids = array();
			$k=0;
			
			//getting if seller is taxable or not!
			$bool_tax = SellerInfo::checkIfSellerCanListTaxableProducts($this->_db,$this->_seller_id);
     	
			$seller_invoice = new SellerInvoice($this->_registry);
			$seller_input_rules = array();

			$seller_info = SellerInfo::getSellerZoneIdAndPickupCityCode($this->_db, $seller_id);

			$seller_zone_id  	= $seller_info['zone_id'];
			$pickup_city_code   = $seller_info['pickup_city_code'];

			$seller_input_rules = $seller_invoice->getInputRuleId($seller_zone_id, $pickup_city_code, '',array('purchase_firm_id','input_type')) ; 

			$tax_based_inventory = new TaxBasedInventory($this->_registry);
			
			if($tax_based_inventory->sellerIsSuitable($this->_seller_id,$this->_category) == "suitable") {
				
				//getting bool_category_taxable for selected category
				$bool_category_taxable = $this->_db->query("SELECT taxable FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$this->_category . "'")->row['taxable'];
			
				
				if($seller_input_rules['input_type'] == "input_available") {

					if($bool_category_taxable == 0) {
						
						foreach($tax_arr_data as $var){	
							
							if($var === "TaxFree") {
								$msg = "";
								$this->_errors_arr['Tax'][$k] = $msg;
								$k++;
							} else {
								$msg = "Invalid entry. This item is taxfree!";
								$this->_errors_arr['Tax'][$k] = $msg;
								$k++;
							}
						}
						
					} else if($bool_category_taxable == 1){
						
						/*
						 * if category is taxable, then check for all the pairs of tax rates 
						 * stored corresponding to the category id and check for those tax class ids 
						 * which lie in seller zone
						 * 
						 * */
						 
						$tax_classes = array();
						$tax_classes = $tax_based_inventory->getSuitableTaxClassesForSeller($this->_category,$seller_zone_id);

						foreach($tax_arr_data as $var){
							
							
							if((!in_array($var,$tax_classes)) || $var === 0) {
								
								$msg = "Invalid entry. Please select items from the dropdown menu provided in excel sheet only!!";
								$this->_errors_arr['Tax'][$k] = $msg;
								$k++;
								
							} else {
								
								$msg = "";
								$this->_errors_arr['Tax'][$k] = $msg;
								$k++;
							}
						}	
					}	
				} 
			} 
			
			return array($this->_errors_arr['Tax']);	
		}
}

?>
