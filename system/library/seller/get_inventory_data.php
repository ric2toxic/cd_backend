<?php

	class GetInventoryData {
		
		private $_db;
		private $_seller_id;
		private $_category;
		private $_status;
		private $_registry;
		
		public function __construct($registry,$category = '',$seller_id = '',$status = '') {
			
			$this->_status = $status;
			$this->_registry = $registry;

			if(method_exists($registry,'get')){
				$this->_db = $registry->get('db');
			}
			else{
				$this->_db = $registry->db;
			}

			$this->_seller_id = $seller_id;
			$this->_category = $category;
		}

		public function getInventoryData($filter_data = array()) {
		
			$sql = "SELECT * FROM `" . DB_PREFIX . "inventory_import_product` AS p INNER JOIN 
					`" . DB_PREFIX ."inventory_import_csv` AS c WHERE p.import_csv_id = c.import_csv_id 
						AND p.validated = 1";
		
			if(!empty($filter_data['filter_status'])) {
				
				$sql .= " AND p.status = " . $filter_data['filter_status'] . "";
				
			} else {
				$filter_data['filter_status'] = 0;
				$sql .= " AND p.status = " . $filter_data['filter_status'] . "";

			}
			
			if(!empty($filter_data['filter_seller_id']) && is_array($filter_data['filter_seller_id'])) {
				
				$sellers = array();
				
				foreach($filter_data['filter_seller_id'] as $key=>$seller_id) {
					array_push($sellers,$seller_id['seller_id']);
					
				}
				
				$implode = implode(",",$sellers);
		
				$sql .= " AND c.seller_id  IN ( " .$implode." ) ";
				
			} else {
				
				$sql .= " AND c.seller_id  = " . (int)$filter_data['filter_seller_id'] . "";
			}
			
			if(!empty($filter_data['filter_category_id']) && is_array($filter_data['filter_category_id'])) {
				
				$categories = array();

				foreach($filter_data['filter_category_id'] as $key=>$category_id) {
						
					array_push($categories,$category_id['category_id']);
				
				}
				$implode_category = implode(",",$categories);
				
				$sql .= " AND c.category_id  IN ( ".$implode_category.")";
				
			} else {
				$sql .= " AND c.category_id  = " . (int)$filter_data['filter_category_id'] . "";
			}
			
			if (isset($filter_data['start']) || isset($filter_data['limit'])) {
				
				if ($filter_data['start'] < 0) {
					$filter_data['start'] = 0;
				}

				if ($filter_data['limit'] < 1) {
					$filter_data['limit'] = 4;
				}
				
				$sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
			}
						
			if ($this->_db->query($sql)->num_rows) {
				return $this->_db->query($sql)->rows;
			} else {
				return false;
			}
			
		}
	
		/*public function getInventoryData() {
			$result = array();
			
			$category = $this->_category;
			$seller = $this->_seller_id;
			$status = $this->_status;
			
			
			$sql = "SELECT * FROM `" . DB_PREFIX . "inventory_import_product` AS p INNER JOIN 
					`" . DB_PREFIX ."inventory_import_csv` AS c WHERE p.import_csv_id = c.import_csv_id 
						AND p.validated = 1";
			
			if(!empty($status)) {
				$sql .= "AND p.status = "."$status"."";
			}
			if(!empty($seller)) {
				
				$sellers = array();
				
				if(is_array($seller)) {
					
					foreach($seller as $key=>$seller_id) {
						array_push($sellers,$seller_id['seller_id']);
					}
					
					$implode = implode(",",$sellers);
				} else {
					
					$implode = $seller;
				}
		
				$sql .= " AND c.seller_id  IN ( " .$implode." ) ";
			}
			
			if(!empty($category)) {
				
				$categories = array();
				
				if(is_array($category)) {

					foreach($category as $key=>$category_id) {
						
						array_push($categories,$category_id['category_id']);
						
					}
					$implode_category = implode(",",$categories);

				} else {
					
					$implode_category = $category;
				}				
				$sql .= " AND c.category_id  IN ( ".$implode_category.")";
			}
			
			return($this->_db->query($sql)->rows);
		}*/
		
		/*
		 * old logic getting the closest number
		 * */
	/*	public function getClosestElem($number,$rates=array()) {
			
			foreach ($rates as $i) {
				$smallest[$i] = abs($i - $number);
			}
			
			asort($smallest);
			
			return key($smallest);
		}
		*/
		//old getting tax class ids from the tax excel values
	/*	public function getTaxClassIds($tax_rates = array()) {
			$rates = array();
			
			foreach($tax_rates as $tax_rate) {
				$tax_rate_sql = "SELECT tax_rate_id from `" .DB_PREFIX. "tax_rate` AS t WHERE t.rate = 
									".(float)($tax_rate['rate'])."";
				$tax_rate_query = $this->_db->query($tax_rate_sql);
				if($tax_rate_query->num_rows >= 1) {
					$tax_rate_id = $tax_rate_query->row['tax_rate_id'];
					$sql_tax_class_id = "SELECT tax_class_id from `" .DB_PREFIX. "tax_rule` AS tc WHERE tc.tax_rate_id = 
										".(int)($tax_rate_id)."";
					$tax_class_query = $this->_db->query($sql_tax_class_id);
				} else {
					$tax_class_query->row['tax_class_id'] = "Please Set the Rule Id";
				}
				$rates[$tax_class_query->row['tax_class_id']] = $tax_rate['rate'];
			}
			return $rates;	
		} */
		
		/**
		 * old logic to get the tax rates
		 * */
		 
		/*public function getTaxRates($tax_class_ids = array()) {
			$tax_rates = array();
			
			foreach($tax_class_ids as $tax_class_id) {
				$tax_rate_id_sql = "SELECT tax_rate_id from `" .DB_PREFIX. "tax_rule` AS t WHERE t.tax_class_id = 
									".(int)($tax_class_id)."";
				$tax_rate_id_query = $this->_db->query($tax_rate_id_sql);
				if($tax_rate_id_query->num_rows >= 1) {
					$tax_rate_id = $tax_rate_id_query->row['tax_rate_id'];
					$tax_value_sql = "SELECT rate from `" .DB_PREFIX. "tax_rate` AS t WHERE t.tax_rate_id = 
										".(int)($tax_rate_id)."";
					$tax_rates[$tax_class_id] = $this->_db->query($tax_value_sql)->row['rate'];
				}
			}
			return $tax_rates;
		}*/
		
		/**
		 * old logic to get the tax data from seller ids
		 * */
	/*	public function getTaxDataFromSellerId($registry,$seller_id) {
			
			$permissible_tax_class_ids = array();
			
			$sql2 = "SELECT zone_id from `". DB_PREFIX. "ms_seller` AS s WHERE ";
			
			if(!empty($seller_id)) {
				
				$sellers = array();
				
				if(is_array($seller_id)) {
					
					foreach($seller_id as $key=>$seller_id) {
						array_push($sellers,$seller_id['seller_id']);
					}
					
					$implode = implode(",",$sellers);
				} else {
					
					$implode = $seller_id;
				}
		
				$sql2 .= " s.seller_id  IN ( " .$implode." ) ";
			}
			
			$zone_id = $this->_db->query($sql2)->row['zone_id'];

			//calling seller invoice to find out rule id based on zone_id
			$seller_invoice = new SellerInvoice($registry);
			$rule_id = $seller_invoice->getInputRuleId($zone_id);
		
			//finding out input type and tax_class ids from rule id
			$sql3 = "SELECT input_type,tax_class_ids from `" .DB_PREFIX. "vat_input_rules` AS v WHERE v.rule_id = ".(int)($rule_id)."";
			$query3 = $this->_db->query($sql3);
			$rates_sql = "SELECT * from `" .DB_PREFIX. "tax_rate`";
			$query_rates = $this->_db->query($rates_sql);
			
			//getting tax class ids corresponding to all the rates and storing bothto be used in tpl
			$rates = $this->getTaxClassIds($query_rates->rows);
				
			//getting input type and permissible tax_class ids from rule id
			$input_type = $query3->row['input_type'];
			$permissible_tax_class_ids = $this->_db->query($sql3)->row['tax_class_ids'];
			
			return array($input_type, $permissible_tax_class_ids);
		}
		
		*/
		/**
		 * old logic for tax based calculation
		 * */
		 
		/**public function getTaxDetails($registry,$data) {
			
			$result = array();
			$main_data = array();
			$rates = array();
			$permissible_tax_class_ids = array();
			$permissible_rates = array();
			$permissible_rates_array = array();
			
			//getting permissible tax values and input type from seller id
		/*	foreach($data as $key1=>$import_data) {
			
				$permissible_tax_class_ids = $this->getTaxDataFromSellerId($registry,$import_data[0]['seller_id'])[1];
				$input_type = $this->getTaxDataFromSellerId($registry,$import_data[0]['seller_id'])[0];
				$rates_sql = "SELECT * from `" .DB_PREFIX. "tax_rate`";
				//getting tax class ids corresponding to all the rates and storing bothto be used in tpl
				$rates = $this->getTaxClassIds($this->_db->query($rates_sql)->rows);
				
				$bool_tax = SellerInfo::checkIfSellerCanListTaxableProducts($registry->db,$import_data[0]['seller_id']);
				
				
				if($input_type == "input_available") {
					
					//input available
					$input_type = 1;
					
					foreach($import_data as $key=>$val) {
						
						$product_data = json_decode($val['import_data'],true);
						
						$import_csv_id = $import_data[0]['import_csv_id'];
						$category_id = $this->_db->query("SELECT category_id from `" .DB_PREFIX. "inventory_import_csv` WHERE import_csv_id = ". (int)($import_csv_id)."")->row['category_id'];
						
						$bool_category_taxable = $this->_db->query("SELECT taxable FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'")->row['taxable'];
						
						if((int)($bool_tax) == 1 || (int)($bool_category_taxable) == 0) {
							
							$product_data['tax_class_id'] = 0;
							$product_data['sale_tax'] = 0;
							$product_data['tax_rate'] = 0;
							
						} else if((int)($bool_tax) == 3 && (int)($bool_category_taxable) != 0){
							
							$tax_rate_sql = "SELECT tax_rate_id FROM
							 `" .DB_PREFIX. "tax_rate` AS t WHERE t.rate = 
										".(float)($product_data['Tax'])."";
							
							$tax_rate_query = $this->_db->query($tax_rate_sql);
							
							if($tax_rate_query->num_rows >= 1) {
							
								$tax_rate_id = $tax_rate_query->row['tax_rate_id'];
								$sql_tax_class_id = "SELECT tax_class_id from `" .DB_PREFIX. "tax_rule` AS tc WHERE tc.tax_rate_id = 
												".(int)($tax_rate_id)."";
								$tax_class_query = $this->_db->query($sql_tax_class_id);
								$tax_class_id = $tax_class_query->row['tax_class_id'];
								$product_data['tax_class_id'] = $tax_class_id;
								$product_data['sale_tax'] = 0;
								$product_data['tax_rate'] = $rates[$tax_class_id];
								
							
								if(!isset($tax_rate_query->row['tax_rate_id']) && $import_data['Tax']){
								
									$product_data['tax_class_id'] = 'tax class id not set! Please set the tax rate id';
									$product_data['sale_tax'] = 0;
									$product_data['tax_rate'] = 'tax rate not set! Please set the tax rate id';
								}
							} else {
								$product_data['tax_class_id'] = 'tax class id not set! Please set the tax rate id';
								$product_data['sale_tax'] = 0;
								$product_data['tax_rate'] = 'tax rate not set! Please set the tax rate id';
							}
						} else {
								$tax_rate_sql = "SELECT tax_rate_id FROM
							 `" .DB_PREFIX. "tax_rate` AS t WHERE t.rate = 
										".(float)($product_data['Tax'])."";
							
							$tax_rate_query = $this->_db->query($tax_rate_sql);
							
							if($tax_rate_query->num_rows >= 1) {
							
								$tax_rate_id = $tax_rate_query->row['tax_rate_id'];
								$sql_tax_class_id = "SELECT tax_class_id from `" .DB_PREFIX. "tax_rule` AS tc WHERE tc.tax_rate_id = 
												".(int)($tax_rate_id)."";
								$tax_class_query = $this->_db->query($sql_tax_class_id);
								$tax_class_id = $tax_class_query->row['tax_class_id'];
								$product_data['tax_class_id'] = $tax_class_id;
								$product_data['sale_tax'] = (float)($product_data['Tax']);
								$product_data['tax_rate'] = $rates[$tax_class_id];
								
								
								if(!isset($tax_rate_query->row['tax_rate_id']) && $import_data['Tax']){
								
									$product_data['tax_class_id'] = 'tax class id not set! Please set the tax rate id';
									$product_data['sale_tax'] = (float)($product_data['Tax']);
									$product_data['tax_rate'] = 'tax rate not set! Please set the tax rate id';
								}
							} else {
								$product_data['tax_class_id'] = 'tax class id not set! Please set the tax rate id';
								$product_data['sale_tax'] = (float)($product_data['Tax']);
								$product_data['tax_rate'] = 'tax rate not set! Please set the tax rate id';
							}
							
						}
						$product_data['input_type'] = 1;
						$naming_filters_ids = $this->_db->query("SELECT naming_filters FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'")->row['naming_filters'];
						
						if(!empty($naming_filters_ids)) {
							
							$naming_filter_names = $this->_db->query("SELECT name FROM " . DB_PREFIX . 
												"filter_group_description WHERE language_id = 1 AND filter_group_id IN (" . $naming_filters_ids . ")")->rows;
						
							$naming_filter_group_names = array();
							foreach($naming_filter_names as $filter_names) {
								array_push($naming_filter_group_names,$filter_names['name']);
							}
							
						} else {
							$naming_filter_group_names = '';
						}
						
						$product_data['naming_filters'] = $naming_filter_group_names;
						
						$main_data[$key1][$key] = $product_data;						
					}
					
				} else {
					
					

					//input not available
					$input_type = 0;
					
					//getting permissible tax class ids from oc_vat_input_rules
					$permissible_tax_class_ids = explode("," ,$permissible_tax_class_ids);
					
					//getting array with tax class id as key and tax rate as value
					$permissible_rates = $this->getTaxRates($permissible_tax_class_ids);
				
					foreach($import_data as $key=>$val) {
						$import_csv_id = $import_data[0]['import_csv_id'];
						$category_id = $this->_db->query("SELECT category_id from `" .DB_PREFIX. "inventory_import_csv` WHERE import_csv_id = ". (int)($import_csv_id)."")->row['category_id'];
						
						
						//getting import data
						$product_data = json_decode($val['import_data'],true);
						
						//if tax = 0, freeze and set tax_class_id = 0 and set sale_tax as 0
						if((float)($product_data['Tax']) == 0) {
							$product_data['tax_rate'] = 0;
							$product_data['sale_tax'] = 0;
							$product_data['tax_class_id'] = 0;
								
						} else if((float)($product_data['Tax']) > 0) {
							
							//if tax >=0 , get all the permissible comma separated tax class ids and then find the closest
							//value from tax rates and set the tax class id corresponding to that value
								
							$selected = $this->getClosestElem((float)($product_data['Tax']),$permissible_rates);
							$product_data['tax_rate'] = $selected;
							$product_data['sale_tax'] = 0;
							$product_data['tax_class_id'] = array_search($selected,$permissible_rates);	
						}
						
						$product_data['input_type'] = 0;
						$naming_filters_ids = $this->_db->query("SELECT naming_filters FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'")->row['naming_filters'];
						
						if(!empty($naming_filters_ids)) {
							
							$naming_filter_names = $this->_db->query("SELECT name FROM " . DB_PREFIX . 
												"filter_group_description WHERE language_id = 1 AND filter_group_id IN (" . $naming_filters_ids . ")")->rows;
						
							$naming_filter_group_names = array();
							foreach($naming_filter_names as $filter_names) {
								array_push($naming_filter_group_names,$filter_names['name']);
							}
							
						} else {
							$naming_filter_group_names = '';
						}
					
						$product_data['naming_filters'] = $naming_filter_group_names;
					
						$main_data[$key1][$key] = $product_data;
						$permissible_rates_array[$key1] = $permissible_rates;
						
						}
					}					
				}
				
				return $permissible_rates_array? array($main_data,$permissible_rates_array) : array($main_data);	
			}*/
			
			/*
			 * method to get tax details from seller
			 * the code gets purchase firm zone id for the seller
			 * based on the purchase firm zone id for the seller, fetches the tax rates for this category
			 * decides on the basis of taxable and on taxable seller and outputs the tax
			 * 
			 * @input : (int) seller_id, (float) tax rate(input from excel sheet by seller)
			 * @output : array(seller_tax,tax_class_id)
			 * author : divya porwal
			 * */
			 
			public function getTaxForSeller($excel_sheet_tax, $seller_zone_id, $pickup_city_code) {
				/**
					* Get purchase firm zone id
					* based on the purchase firm zone id for the seller,
					* get the tax rates for this category
					* */
					 
				$seller_invoice = new SellerInvoice($this->_registry);
				
				$purchase_zone_id = $seller_invoice->getInputRuleId($seller_zone_id, $pickup_city_code, '',array('zone_id','input_type'))['zone_id'] ; 
				
				$tax_based_inventory = new TaxBasedInventory($this->_registry);
					
				$tax_classes = $tax_based_inventory->getSuitableTaxClassesForSeller($this->_category, $purchase_zone_id);	 
				
				
				if (preg_match('/CST/',$excel_sheet_tax) || preg_match('/TaxFree/',$excel_sheet_tax)) {
						
					$seller_tax = 'TaxFree';
				} else {
					$seller_tax = $excel_sheet_tax;
				}
				
				                                                                                            	
				/*
				 if seller_tax is taxfree and tax rates for the purchase firm zone contains tax free, 
				 then output tax (tax_class_id of oc_product) will be taxfree.
				 */
				
				if($excel_sheet_tax == 'TaxFree' && in_array('TaxFree',$tax_classes)) {
					
					$tax_class_id = 0;
						
				} else if(preg_match('/CST/',$excel_sheet_tax)) {
					
					/*
					 * seller_tax is CST(2%) and tax rates 
					 * for the purchase firm zone contains 
					 * tax_rate > 0 then chose the one > 0
					 */
					 $count = 0;
					 /*
					  * checking if only 0 is present in tax classes
					  * 
					  * */
					
					 if(min($tax_classes) == 0) {
						
						$tax_class_id = "Contact administrator";
							
					 } else {
						 
						 foreach($tax_classes as $tax_class) {
							 
							if($tax_class > 0) {
								$count++;
							}
							
							if($count > 1) {
								/*
								 * if more than one values with same zone id and tax class
								 * */
								$tax_class_id = "Contact administrator";
								break;
							}
						}
							
						if($count == 1) {
							
							/*
							 * if only one value corresponding to the zone id 
							 * and non zero
							 * */
							 
							$tax_class_id = max($tax_classes);
						}
					 }
					 
					} else if(($excel_sheet_tax != 'CST (2%)' && $excel_sheet_tax != 'TaxFree') && in_array($excel_sheet_tax,$tax_classes)) {
						/*
						 * if seller_tax is rest of the rates and we also have the same tax 
						 * rate in the purchase firm zone rate,
						 * */ 
						$tax_class_id = $excel_sheet_tax;
					} else {
						/*
						 * show in Admin panel - contact administrator if any other error persists
						 * */
						$tax_class_id = "Contact administrator";
					}
				return array($seller_tax, $tax_class_id);
			}
			
			
			/*
			 * method to download file from file link
			 * @input : file_path
			 * @output : void
			 * @author : Divya
			 * */
			
			public function downloadFile($file_path) {
				
				ob_clean();
			
				$file =  $file_path;

				header('Content-Description: File Transfer');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				readfile($file);

				exit;
				
			}
		}
		
		
	
	
?>
