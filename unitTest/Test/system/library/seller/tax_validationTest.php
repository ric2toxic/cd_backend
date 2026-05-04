<?php


class TaxValidationTest extends OpenCartTest {

	private $_registry;
	private $_sheet_data;
	private $_array_data;
	private $_tax_validation_helper;
	private $_seller_id;
	private $_category_id;
	private $_seller_zone_id;
	private $_tax_object;
	private $_get_inventory_data;
	private $_excel_sheet_tax;
	
	
	/*
	 * methods to be tested : 
	 * getTaxForSeller
	 * getSuitableTaxClassesForSeller
	 * getImportCategories
	 * sellerIsSuitable
	 * */
	 
	public function __construct() {
		
		$this->_registry = $this;
        $this->_seller_id = 16;
        $this->_category_id = 61;
        $this->_seller_zone_id = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "ms_seller 
								WHERE seller_id = '" . (int)$this->_seller_id  . "'")->row['zone_id'];
		$this->_tax_object = new TaxBasedInventory($this);
		$this->_get_inventory_data = new GetInventoryData($this,$this->_category_id,$this->_seller_id,'');		
		$this->_excel_sheet_tax = 'VAT (14.5%)';
    }
    
	public function testSellerIsSuitable() {
		
		
		$code_output = $this->_tax_object->sellerIsSuitable($this->_seller_id, $this->_category_id);
		$expected_data = 'suitable';
		$this->assertEquals( $expected_data , $code_output);
	}

	public function testGetTaxForSeller() {
		
		$code_output = $this->_get_inventory_data->getTaxForSeller($this->_excel_sheet_tax,$this->_seller_zone_id);
		$expected_output = array('VAT (14.5%)','VAT (14.5%)');
		$this->assertEquals( $expected_output , $code_output);
	
	}
	
	public function testGetSuitableTaxClassesForSeller() {
		
		$code_output = $this->_tax_object->getSuitableTaxClassesForSeller($this->_category_id , $this->_seller_zone_id);
		$expected_output = array('VAT (14.5%)','CST (2%)');
		$this->assertEquals( $expected_output , $code_output);
	}
}

?>
