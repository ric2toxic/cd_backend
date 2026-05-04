<?php

	class TaxBasedInventory {
		private $_db;
		private $_registry;
	
		public function __construct($registry) {
			
			$this->_db = $registry->db;
			$this->_registry = $registry;
		}
		
		/*
		 * method to download excel sheet based on seller and category"
		 * bool_tax = 3 //composite seller  
		 * bool_tax = 1 //taxfree seller
		 * bool_tax = 2 // taxable seller
		 * 
		 * */
		 
		public function downloadInventory($category_id,$seller_id,$bool_category_taxable,$bool_tax) {
			
			$action = '';
			
			
			//getting if seller is suitable or not
			if($this->sellerIsSuitable($seller_id,$category_id) == "suitable") {
				require_once 'system/library/PHPExcel/IOFactory.php';
				
				$phpExcel = new PHPExcel;
				$phpExcel->getDefaultStyle()->getFont()->setName('Arial Black');
				$phpExcel->getDefaultStyle()->getFont()->setSize(14);
				$phpExcel ->getProperties()->setTitle("Bulk Inventory Listing");

				$phpExcel ->getProperties()->setCreator("Wholesalebox");

				$phpExcel ->getProperties()->setDescription("Excel SpreadSheet for bulk inventory listing");

				// Creating PHPExcel spreadsheet writer object

				$writer = PHPExcel_IOFactory::createWriter($phpExcel, "Excel2007");

				// When creating the writer object, the first sheet is also created

				// will get the already created sheet

				$sheet = $phpExcel ->getActiveSheet();

				// setting title of the sheet

				$sheet->setTitle('Download Inventory Listing');

				// Creating spreadsheet header

				$sheet ->getCell('A1')->setValue('SKU Code');
				$sheet ->getCell('B1')->setValue('Transfer Price');
				$sheet ->getCell('C1')->setValue('AvailableSets');
				$sheet ->getCell('D1')->setValue('Weight of a Piece (in gm)');
				$sheet ->getCell('E1')->setValue('Tax');
				$sheet ->getCell('F1')->setValue('Any additional Comments');
				
				$sheet ->getCell('A2')->setValue('Code can only contain either alphabets (A-Z) or numbers (0-9) or "-" or "_" characters. Any other symbol used such as space, %, (, ), # etc will be rejected.');
				$sheet ->getCell('B2')->setValue('This is transfer price per piece inclusive of VAT that we will pay you');
				$sheet ->getCell('C2')->setValue('This is the number of sets you have in stock for Wholesalebox (you can edit this later from your panel)');
				$sheet ->getCell('D2')->setValue('Enter in grams');
				$sheet ->getCell('F2')->setValue('Mention all the additional information which you feel should be there in the description or is missing from the available filter lists.');
				
				$sheet ->getCell('A3')->setValue('eg: ABC-123XL');
				$sheet ->getCell('B3')->setValue('Eg: 200');
				$sheet ->getCell('C3')->setValue('Eg: 10');
				$sheet ->getCell('D3')->setValue('Eg: 250');
				$sheet ->getCell('E3')->setValue('Select Tax from dropdown values, e.g : 5.5');
				$sheet ->getCell('F3')->setValue('eg: Rayon 150 gsm; kundan work done on neck; wash care information etc.Important for Anarkalis - interlocking done on kali fold of anarkali?');

				$seller_invoice = new SellerInvoice($this->_registry);

				$seller_info = SellerInfo::getSellerZoneIdAndPickupCityCode($this->_db, $seller_id);

				$seller_zone_id  	= $seller_info['zone_id'];
				$pickup_city_code   = $seller_info['pickup_city_code'];
				
				$seller_input_rules = array();
				
				$seller_input_rules = $seller_invoice->getInputRuleId($seller_zone_id, $pickup_city_code, '',array('purchase_firm_id','input_type')) ; 
				$rule_id = $seller_input_rules['rule_id'];
				$purchase_firm_id = $seller_input_rules['purchase_firm_id'];
				$input_type = $seller_input_rules['input_type'];
				
			
				if($input_type == "input_available") {
					
					if($bool_category_taxable == 0) {
						//TaxFree listing in tax column
						$sheet ->getCell('E2')->setValue('Please select Tax option from below dropdown');

						for ($i = 4; $i <= 54; $i++) {
							
							$objValidation =  $phpExcel ->getActiveSheet()->getCell('E' . $i)->getDataValidation();
							//setting type for data validation
							$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
							$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
							$objValidation->setAllowBlank(false);
							$objValidation->setShowInputMessage(true);
							$objValidation->setShowDropDown(true);
							$objValidation->setShowDropDown(true);
							$objValidation->setPromptTitle('Pick from list');
							$objValidation->setPrompt('Please pick Tax from the drop-down list.');
							$objValidation->setErrorTitle('Input error');
							$objValidation->setError('Value is not in list');
							$objValidation->setFormula1('"TaxFree"');
							$sheet->getStyle('E'.$i)->getFont()->setBold(true)->setSize(12);
						}	
					} else {
						
						//if category is taxable, then show all the tax rates stored in oc_category
						$tax_classes = array();
						$seller_tax_rates  = $this->_db->query("SELECT tax_rates FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'")->row['tax_rates'];

						foreach(unserialize($seller_tax_rates) as $zone_tax_pair) {
							$zone_tax_pair_array = explode("=>",$zone_tax_pair);
							$zone_id = $zone_tax_pair_array[0];
							$tax_class_id = $zone_tax_pair_array[1];
							
							if($zone_id == 	$seller_zone_id) {
								$query = $this->_db->query("SELECT title FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '" . (int)$tax_class_id . "'");
								array_push($tax_classes,$query->row['title']);
							}					
						}
						
						for ($i = 4; $i <= 54; $i++) {
								
							$objValidation =  $phpExcel ->getActiveSheet()->getCell('E' . $i)->getDataValidation();
							//setting type for data validation
							$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
							$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
							$objValidation->setAllowBlank(false);
							$objValidation->setShowInputMessage(true);
							$objValidation->setShowDropDown(true);
							$objValidation->setShowDropDown(true);
							$objValidation->setPromptTitle('Pick from list');
							$objValidation->setPrompt('Please pick Tax from the drop-down list.');
							$objValidation->setErrorTitle('Input error');
							$objValidation->setError('Value is not in list');
							$objValidation->setFormula1('"'.implode(",",$tax_classes).'"');
							$sheet->getStyle('E'.$i)->getFont()->setBold(true)->setSize(10);
						}
					}
					
				} else if($input_type == "no_input") {
					
					if($bool_category_taxable == 0) {
						
						//TaxFree listing in tax column
						$sheet ->getCell('E2')->setValue('Please select Tax option from below dropdown');
					
						for ($i = 4; $i <= 54; $i++) {
							
							$objValidation =  $phpExcel ->getActiveSheet()->getCell('E' . $i)->getDataValidation();
							//setting type for data validation
							$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
							$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
							$objValidation->setAllowBlank(false);
							$objValidation->setShowInputMessage(true);
							$objValidation->setShowDropDown(true);
							$objValidation->setShowDropDown(true);
							$objValidation->setPromptTitle('Pick from list');
							$objValidation->setPrompt('Please pick Tax from the drop-down list.');
							$objValidation->setErrorTitle('Input error');
							$objValidation->setError('Value is not in list');
							$objValidation->setFormula1('"TaxFree"');
							$sheet->getStyle('E'.$i)->getFont()->setBold(true)->setSize(12);
						}	
					} else if($bool_category_taxable == 1) {
						
						/*
						 * if category is taxable, then if category is taxable -  CST(2%)
						 * (tax class id hardcoded); 
						 * if for the seller zone_id, it is also stored that the product 
						 * can be taxfree also (eg: bedsheets), option of TaxFree given
						 * */
						 
						$cst_class_id = 12;
						$cst = "CST (2%)";
						
						$tax_classes = array();
					
						$seller_tax_rates  = $this->_db->query("SELECT tax_rates FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'")->row['tax_rates'];
						
						foreach(unserialize($seller_tax_rates) as $zone_tax_pair) {
							$zone_tax_pair_array = explode("=>",$zone_tax_pair);
							$zone_id = $zone_tax_pair_array[0];
							$tax_class = $zone_tax_pair_array[1];
							
							if($zone_id == $seller_zone_id) {
								array_push($tax_classes,'TaxFree');
								break;
							}
						}
						
						array_push($tax_classes, $cst);

						
						$sheet ->getCell('E2')->setValue('Please select Tax option from below dropdown');

						for ($i = 4; $i <= 54; $i++) {
							
							$objValidation =  $phpExcel ->getActiveSheet()->getCell('E' . $i)->getDataValidation();
							//setting type for data validation
							$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
							$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
							$objValidation->setAllowBlank(false);
							$objValidation->setShowInputMessage(true);
							$objValidation->setShowDropDown(true);
							$objValidation->setShowDropDown(true);
							$objValidation->setPromptTitle('Pick from list');
							$objValidation->setPrompt('Please pick Tax from the drop-down list.');
							$objValidation->setErrorTitle('Input error');
							$objValidation->setError('Value is not in list');
							$objValidation->setFormula1('"'.implode(",",$tax_classes).'"');
							$sheet->getStyle('E'.$i)->getFont()->setBold(true)->setSize(12);
						}
					}
				}
				
				// Making headers text bold and larger
				$sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(12);
				$sheet->getStyle('A2:F2')->getFont()->setItalic(true)->setSize(10);
				$sheet->getStyle('A3:F3')->getFont()->setItalic(true)->setSize(10);


				// Autosize the columns

				$sheet->getColumnDimension('A')->setAutoSize(true);

				$sheet->getColumnDimension('B')->setAutoSize(true);

				$sheet->getColumnDimension('C')->setAutoSize(true);
				
				$sheet->getColumnDimension('D')->setAutoSize(true);
				
				$sheet->getColumnDimension('E')->setAutoSize(true);
				
				$sheet->getColumnDimension('F')->setAutoSize(true);

				// download file 
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header("Content-Disposition: attachment; filename=\"bulk_inventory.xlsx\"");
				header("Cache-Control: max-age=0");

				ob_clean();
				$writer->save('php://output');
				
			} else {
				
				// alert("Contact us for your Inventory Listing !");
				 
			}
			return $action;
		}
		
		/*
		 * @input category_id 
		 * @input seller_id 
		 * checks whether seller is suitable or not. 
		 * Composite seller will return not suitable;
		 * */

		public function sellerIsSuitable($seller_id, $category_id) {
			
			/*
			 * getting if seller is taxable or not!
			 * bool_tax = 3 //composite seller  
			 * bool_tax = 1 //taxfree seller
		     * bool_tax = 2 // taxable seller
		     * */
			$bool_tax = SellerInfo::checkIfSellerCanListTaxableProducts($this->_db,$seller_id);
			
			//getting bool_category_taxable for selected category
			$bool_category_taxable = $this->_db->query("SELECT taxable FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'")->row['taxable'];
			
			/*
			 * if seller is composite
			 * if seller is taxfree and category is taxable
			 * condition : not suitable
			 * */
			if(($bool_tax == 3) || ($bool_tax == 1 && $bool_category_taxable == 1)) {
				return "not suitable";
			} else {
				return "suitable";
			}
		}	
		
		
		/*
		 * @input seller_id 
		 * @output array (categories)
		 * returns array of categories given a seller id
		 * composite seller will return empty array
		 * getting import categories using seller_id 
		 * and showing only those categories where show_import_id = 1 and status = 1
		 * */
		 
		public function getImportCategories($seller_id='') {
			$selected = array();
			$show_import = array();
			if(empty($seller_id)) {	
				
				$sql_check_import = "SELECT c1.category_id , name FROM `".DB_PREFIX . "category` as c1 INNER JOIN  
										`".DB_PREFIX . "category_description` as c2 WHERE c1.category_id = c2.category_id AND 
											c1.show_for_import = '1'  AND c1.status = '1' AND c2.language_id = '1'";				
			} else  {

				//getting if seller is taxable or not!
				$bool_tax = SellerInfo::checkIfSellerCanListTaxableProducts($this->_db,$seller_id);

				//showing only category_ids having show_import_id as 1 and status = 1 
				$sql_check_import = '';
				$sql_check_import .= "SELECT c1.category_id , name FROM `".DB_PREFIX . "category` as c1 INNER JOIN  
										`".DB_PREFIX . "category_description` as c2 WHERE c1.category_id = c2.category_id AND 
											c1.show_for_import = '1'  AND c1.status = '1' AND c2.language_id = '1'";
				if((int)$bool_tax === 1) {
					//if non taxable, show only the non taxable categories
					$sql_check_import .=  " AND c1.taxable = '0'";

				} else if((int)($bool_tax) === 2){
					
					//if taxable seller , then show all the categories (taxable and non taxable)
					$sql_check_import .= "";			
				} if((int)($bool_tax) == 3) {
					
					//return empty array in case of composite seller
					return array();
				}
			}
			
			$show_import = $this->_db->query($sql_check_import)->rows;
		
			//getting category ids for show_import
			//$categories_new = array_column($show_import,'category_id');
			
			return $show_import;
		}
		
		/*
		 * @input category_id 
		 * @input seller_zone_id
		 * @output array (tax_class_ids)
		 * returns array of tax_class_ids given a seller id and seller_zone_id
		 * returns suitable tax class ids for seller given category id and seller zone id.
		 * Method is used to check for all the tax rates in oc_category and return tax rates for given
		 * seller_zone_id and catetgory_id 
		 * */
		
		public function getSuitableTaxClassesForSeller($category_id, $seller_zone_id) {

		
			$tax_classes = array();
			$seller_tax_rates_array = array();
			
			//getting seller tax rates for that seller zone id
			$seller_tax_rates  = unserialize($this->_db->query("SELECT tax_rates FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'")->row['tax_rates']);
			
			if(!is_array($seller_tax_rates)) {
				array_push($seller_tax_rates_array,$seller_tax_rates);
			} else {
				$seller_tax_rates_array = $seller_tax_rates;
			}
		
			
			foreach($seller_tax_rates_array as $zone_tax_pair) {
				$zone_tax_pair_array = explode("=>",$zone_tax_pair);
				
				$zone_id = $zone_tax_pair_array[0];
				$tax_class_id = $zone_tax_pair_array[1];
	
				if($zone_id == 	$seller_zone_id) {
					
					if($tax_class_id == 0) {
						array_push($tax_classes,0);
					} else {
						$query = $this->_db->query("SELECT title FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '" . (int)$tax_class_id . "'");
						array_push($tax_classes,$query->row['title']);
					}
				}					
			}
			
			return $tax_classes;
		}
	}
	
?>
