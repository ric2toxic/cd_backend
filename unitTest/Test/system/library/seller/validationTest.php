<?php

	//Author Divya Porwal
	// February 2017
	
 require_once( DIR_SYSTEM . 'library/seller/inventory_validation.php' );
 require_once( 'validation_helper.php' );
 
 class ValidationTest extends OpenCartTest {
	 private $_validation_helper;
     private $_array_data;
     private $_registry;
     private $_sub_total_object;
     public $total_data = array();
     public $total = 0;
     private $_sheet_data = array();
     private $_seller_id;
     private $_category_id;
     
     
     public function __construct(){
		
        $this->_registry = $this;
        $this->_validation_helper = new ValidationHelper( $this );
        $this->_array_data = $this->_validation_helper->setData();
        $this->_sheet_data = $this->_array_data['sheet_data'];
        $this->_seller_id = 12010;
        $this->_category_id = 165001;
		$validation_object = new InventoryValidation( $this->_registry, $this->_seller_id, $this->_category_id);
    }
    

    
	 public function testValidateSkuCode() {
		$sku_code = array();
		$product_id = 2;
		$validation_object = new InventoryValidation( $this->_registry,$this->_seller_id,$this->_category_id);
		$expected = array( 'sku code already exists');
						
		for($i=0;$i<count($this->_sheet_data ); $i++){
			array_push($sku_code,$this->_sheet_data[$i]['SKU Code']);
		}	
		
		foreach($sku_code as $sku) {
			$this->_validation_helper->addRowdetails($sku,$product_id);
			$this->_validation_helper->addProductdetails($this->_seller_id,$product_id);
			$product_id++;

		}
		$validation_object->_validate_sku = $this->_array_data['sheet_data'];
		$output = $validation_object->_validate_sku;

		foreach($sku_code as $sku) {
			$product_id--;
			$this->_validation_helper->deleteProductdetails($product_id);
			$this->_validation_helper->deleteRow($sku,$product_id);

		}
		$this->assertEquals( $expected , $output);
		 
	}
	
	 
	 public function testTransferPrice() {
		 $price = array();
		 $validation_object = new InventoryValidation( $this->_registry,$this->_seller_id,$this->_category_id);
		 
		 $validation_object->_validate_price = $this->_array_data['sheet_data'];
		 $output = $validation_object->_validate_price;
		 $expected = array( 'Invalid entry. This field must be positive number only');
		 $this->assertEquals( $expected , $output);
	 }
	 
	 public function testAvailableSets() {
		 $availablesets = array();
		 $validation_object = new InventoryValidation( $this->_registry,$this->_seller_id,$this->_category_id);
		 $validation_object->_validate_available_sets = $this->_array_data['sheet_data'];
		 $output = $validation_object->_validate_available_sets;
		 $expected = array( 'Invalid entry. This field must be nonnegative numeric only');
		 $this->assertEquals( $expected , $output);
	 }
	 
	 public function testPiecesInSet() {
		 $pieces_in_set = array();
		 $validation_object = new InventoryValidation($this->_registry,$this->_seller_id,$this->_category_id);
		 $validation_object->_validate_pieces_in_set = $this->_array_data['sheet_data'];
		 $output = $validation_object->_validate_pieces_in_set;
		 $expected = array('Invalid entry. This field must be numeric only');
		 $this->assertEquals($output,$expected);
	 }
	 
	 public function testSetDescription() {
		 $validation_object = new InventoryValidation($this->_registry,$this->_seller_id,$this->_category_id);
		 $validation_object->_validate_set_description = $this->_array_data['sheet_data'];
		 $output = $validation_object->_validate_set_description;
		 $expected = array('Invalid description. This field cannot be left blank');
		 $this->assertEquals($output,$expected);
	 }
	 
	 
	 public function testSizeOrColor() {
		 $validation_object = new InventoryValidation($this->_registry,$this->_seller_id,$this->_category_id);
		 $validation_object->_validate_size_or_color = $this->_array_data['sheet_data'];
		 $output = $validation_object->_validate_size_or_color;
		 $expected = array('');
		 $this->assertEquals($output,$expected);
		 
	 }
	 
	 public function testSize() {
		 $validation_object = new InventoryValidation($this->_registry,$this->_seller_id,$this->_category_id);
		 $validation_object->_validate_size = $this->_array_data['sheet_data'];
		 $output = $validation_object->_validate_size;
		 $expected = array('Invalid size. This field must be empty for size set.');
		 $this->assertEquals($output,$expected);
	 }
	 
	 public function testWeight() {
		 $this->_validation_helper->addRowCategory($this->_category_id);
		 $validation_object = new InventoryValidation($this->_registry,$this->_seller_id,$this->_category_id);
		 $validation_object->_validate_weight = $this->_array_data['sheet_data'];
		 $output = $validation_object->_validate_weight;
  		 $this->_validation_helper->deleteRowCategory($this->_category_id);
		 $expected = array('This seems to be inapproporate weight. Please recheck');
		 $this->assertEquals($output,$expected);
	 } 
	 
	 public function testComment() {
		  $this->_validation_helper->addRowSeller($this->_seller_id);
		  $validation_object = new InventoryValidation($this->_registry, $this->_seller_id,$this->_category_id);
		  $validation_object->_validate_comment = $this->_array_data['sheet_data'];
		  $output = $validation_object->_validate_comment;
   		  $this->_validation_helper->deleteRowSeller($this->_seller_id);
		  $expected = array('Personal information like Name is not allowed');
		  $this->assertEquals($output,$expected);
	  }
	  
	 
	 
 }


?>
