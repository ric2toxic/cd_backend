<?php


class ControllerSellersellerupload extends Controller {

	//Author Divya Porwal
	// February 2017

	
	public function index(){
		
		$this->getForm();

	}
	
	public function getForm() {
				
		$data = array();
		//array to storing all the categories
		$allcategories = array();		
		//getting seller _id
		$seller_id = $this->customer->getId();
		
		$this->load->model('catalog/category');
		$data['header_seller'] = $this->load->controller('common/seller_header');
		$data['footer_seller'] = $this->load->controller('common/seller_footer');
		
		//getting seller status from ms_seller
     	$status = $this->getSellerStatus($seller_id);
     
        //getting all categories id and names
     	$allcategories = $this->model_catalog_category->getAllCategoryIds();
     	
		$tax_inventory = new TaxBasedInventory($this);
	
		//getting import categories to show in dropdown menu
		$data['category'] = $tax_inventory->getImportCategories($seller_id);
		
		if (!$this->customer->isLogged() || empty((int)($seller_id)) || ((int)($status) != 1)) {
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/multiseller/seller_upload.tpl')) {
			$this->response->setOutput($this->load->view('default/template/multiseller/seller_upload.tpl', $data));
		}
	}
	
	
	//public method to get headers of excel file
	public function get_headers($rowData) {
	  
        $headers = array();
    	$k = 0;
    	$array_header = array();
    	foreach($rowData['rowData'][1] as $var) {
			
			if($var != null or $var != "") {
				$headers[$k] = $var;
				$k++; 
			}
		}
		
		$array_header['headers'] = $headers;
    	$array_header['col_count'] = $k;
   
		return $array_header;
	}
	
	
	//reading the contents of sheet
	public function get_sheet_data($rowData,$headers){
		
		$headers_info = $headers['headers'];
		$col_count = $headers['col_count'];
		$index=0;
		$sheet_data = array();
		$k = 0;
		
		//first row is header and second row is examples section
		//$i = 2
		for($i=2; $i < sizeof($rowData['rowData']); $i++){
			$j = 0;
			foreach($rowData['rowData'][$i] as $var){
					$content[$index][$j] = $var;
					$j++;
			}
			$content[$index] = array_slice($content[$index],0,$col_count,true);
			$index++;
		}
	
		for($i=0;$i<count($content); $i++) {
				$sheet_data[$k] = array_combine($headers_info, $content[$i]);
				$k++;
		}
		return $sheet_data;
	}
	
	public function customise_data($headers_info, $col_count, $sheet_data, $row_count, $rules_array) {
		//preparing data to send to tpl
		//headers => headings
		//col_count => column_count
		//row_count => row_count
		//rules => rules_array
		$validate_data = array();
		$validate_data['headers'] = $headers_info;
		$validate_data['col_count'] = $col_count;
		$validate_data['sheet_data'] = $sheet_data;
		$validate_data['row_count'] = $row_count;
		$validate_data['rules'] = $rules_array;
		
		return $validate_data;
	}
	
	// Get only seller status for a seller_id
    public function getSellerStatus($seller_id) {

        $query = $this->db->query("SELECT seller_status FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");

        if ($query->num_rows)
            return (int)($query->row['seller_status']);
        else
            return false;
    }
    
    /*//method to download file online
	public function download() {
	        
		ob_clean();
		
		$file = DIR_SYSTEM . 'upload/inventory_download/download_sample.xlsx';

		header('Content-Description: File Transfer');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
	}*/
	

	/* 
	 * method to download dynamic file online
	 * @author Divya Porwal
	 * */
	public function download() {
		
		//getting category id from post data
		$category_id = json_decode(base64_decode($this->request->post['category_id_download']));
			
		//getting seller_id
		$seller_id = $this->customer->getId();
			
		/**
		 * taxable : $bool = 2;
		 * composite : $bool = 3;
		 * non taxable : $bool = 1;
		 * */
			
		//getting if seller is taxable or not!
		$bool_tax = SellerInfo::checkIfSellerCanListTaxableProducts($this->db,$seller_id);
			
		//getting bool_category_taxable for selected category
		$bool_category_taxable = $this->db->query("SELECT taxable FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'")->row['taxable'];
		
		$tax_based_inventory = new TaxBasedInventory($this);
		$tax_based_inventory->downloadInventory($category_id,$seller_id,$bool_category_taxable,$bool_tax);
		
	}
	
	//checking for erroneous sheets in input
	public function validateSheet($highestRow, $highestColumn, $sheet, $headers, $size) {
		$sheet_error = '';
		$higestRow = $highestRow;
		$highestColumn = $highestColumn;
		
		//checking for exact column numbers 	
		if(($headers['col_count'] != 6 )|| $higestRow == 1 || $highestColumn == 'A') {
			$sheet_error = "Error !Sheet format not accepted!";

		} else if($headers['headers'][0] != 'SKU Code' || $headers['headers'][1] !=  'Transfer Price' || $headers['headers'][2] != 'AvailableSets' || $headers['headers'][3] != 'Weight of a Piece (in gm)' || $headers['headers'][4] != 'Tax' || $headers['headers'][5] != 'Any additional Comments') 
		{ //checking for exact headers in columns
			$sheet_error = "Error ! Headings do not match with the actual sheet!";		
		}
		if(($higestRow == 1 && $highestColumn == 'A') || $size == 4) {
			//checking for empty sheets
			$sheet_error = 'Error! Empty sheet not accepted!';
		} else if($highestRow > 54 ) {
			//checking for empty sheets
			$sheet_error = 'Error! Maximum 50 entries allowed!';
		} 
		//checking for empty sheets with only headers available
		if($higestRow <= 3 && $highestColumn <= 'F') {
			$sheet_error = 'Error! Empty sheet not accepted!';
		} 
		
		
		return $sheet_error;
	}
	
	//method for uploading xlsx sheet and extracting data from it
	public function upload() {
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->request->post['category_id']) && !empty($this->customer->getId())) {
			$category_id = json_decode(base64_decode($this->request->post['category_id']));
			$seller_id = $this->customer->getId();	
			$status = $this->getSellerStatus($seller_id);
			//array for storing product_ids for the files inserted
			$product_ids = array();
			$date_time=  date("Y-m-d");	
			$validate_data = array();
			$time = date("h:i:sa");
			$date_time = $date_time." ".$time;
			$upload_var = array();
			$validate_data = array();
			$upload_var = $this->import($category_id);
			$inputFile = $upload_var['inputfile'];
			$seller_id = $this->customer->getId();
			$rowData = array();

			//php excel to parse xlsx, xls
			try {
				$inputFileType = PHPExcel_IOFactory::identify($inputFile);		
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFile);

			} catch (Exception $e) {
				die($e->getMessage());
			}
				
			$sheetNames = $objPHPExcel->getSheetNames();			
			$sheet = $objPHPExcel->getSheet(0);     //Selecting sheet 0
			$highestRow = $sheet->getHighestDataRow(); //getting highest row number
			$highestColumn = $sheet->getHighestDataColumn();  //getting highest column number
			
			$colNumber = PHPExcel_Cell::columnIndexFromString($highestColumn);
			$worksheet = $objPHPExcel->getActiveSheet();
			
			$file_data = array(
				'seller_id' => $seller_id ,
				'category_id' => $category_id ,
				'file_path' => $inputFile ,
				'date_added' => $date_time,
			);
				
			//extracting rowData
			for($row = 0; $row <= $highestRow; $row++){
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row);	
				$data['rowData'][] = $rowData[0];
			
			}
		
			//getting headers
			$headers = $this->get_headers($data);
			$headers_info = $headers['headers'];
			$col_count = $headers['col_count'];
			
			//validation for the uploaded file
			if(!empty($this->validateSheet($highestRow, $highestColumn, $sheet, $headers,count($data['rowData'])))) {
				echo $this->validateSheet($highestRow, $highestColumn, $sheet, $headers, count($data['rowData']));

			} else {
				//getting import_csv_id from inventory_import_product table
				//insert file details initial

				$upload_data = new InsertInventory($this->db);
				$this->_import_csv_id = $upload_data->insertUploadFileDetails($file_data);
			
				//rules description
				$rules_array = array(
									'SKU Code' => '_validate_sku',
									'Transfer Price' => '_validate_price',
									'AvailableSets' => '_validate_available_sets',
									'Weight of a Piece (in gm)' => '_validate_weight' ,
									'Any additional Comments' => '_validate_comment',
									'Tax' => '_validate_tax',
									);		
				
				// getting sheet data		
				$sheet_data = $this->get_sheet_data($data,$headers); 
				
				$row_count = sizeof($sheet_data)+1;
				$data['sheet_data'] = $sheet_data;
				$data['Row'] = $row_count;
				$data['Col'] = $col_count;
				
				//shifting the array twice to avoid the information and example rows in xlsx sheet
				array_shift($sheet_data);
				array_shift($sheet_data);
				
				$validate_data = $this->customise_data($headers_info, $col_count, $sheet_data, $row_count, $rules_array);
			
				//start validation for the xlsx
				$validation_object = new InventoryValidation($this,$seller_id,$category_id);
				$errors_data = array();
				$data_recieved = $validation_object->callValidators($validate_data);
				$final_data = $data_recieved['final_array'];
				$flag = $data_recieved['flag'];
				
				//inserting the file upload details in the inventory_import_product table once
				$upload_data = new InsertInventory($this->db);
				$product_ids = $upload_data->insertInitialFile($validate_data['sheet_data'],$this->_import_csv_id,$flag, $date_time);
				
				$view_data['headers'] = $headers;
				$view_data['row_count'] = $row_count;
				$view_data['col_count'] = $col_count;
				$view_data['rules'] = $rules_array;
				$view_data['flag'] = $flag;
				$view_data['original_data'] = $final_data;
				$view_data['import_csv_id'] = $this->_import_csv_id;
				$view_data['indexes_of_error'] = array();
				$view_data['category_id'] = $category_id;
				$view_data['product_ids'] = $product_ids;
				/*if($flag == 1) {
					for($i = 1; $i < sizeof($final_data);$i = $i+2) {
						if (array_filter($final_data[$i])) {
							$errors_array['final_arr'][] = $final_data[$i-1];
							$errors_array['final_arr'][] = $final_data[$i];
							$errors_array['index'][] = $i-1;
						}	
					}
					//data to send to view
					$view_data['final_data'] = $errors_array['final_arr'];
					$view_data['indexes_of_error'] = $errors_array['index'];
				} else {
					$view_data['final_data'] = $final_data;
				} */
			
				$view_data['final_data'] = $final_data;
				$this->response->setOutput($this->load->view('default/template/multiseller/jqgrid.tpl',$view_data));
			}     
		}  		  
	}
	
	public function change_jqgrid_data(){
		//calling this method when changes are made to jqgrid table
		
		$date_time=  date("Y-m-d");	
		$time = date("h:i:sa");
		$date_time = $date_time." ".$time;
		$seller_id =  $this->customer->getId();
		$jqgrid_data = array();
		$product_ids = array();
		if(isset($this->request->post['griddata'])) {
			$griddata 	= 	$this->request->post['griddata'];
			$category_id = $this->request->post['category_id'];
			$row_count = $this->request->post['row_count'];
			$col_count = $this->request->post['col_count'];
			$headers = $this->request->post['headers'];
			$rules = $this->request->post['rules'];
			$import_csv_id = $this->request->post['import_csv_id'];
			$product_ids = $this->request->post['product_ids'];
			$product_ids_old = $this->request->post['product_ids_old'];
		
		}
    	
		$k = 0;
		for($i = 0; $i < sizeof($griddata); $i=$i+2) {
			$jqgrid_data[$k] = $griddata[$i];
			$k++; 
		}
	
		//removing the id key from the gridata for processing again
		foreach ($jqgrid_data as $key=>$value) {
			unset($jqgrid_data[$key]['id']);
		}
	
		$validate_arr = $this->customise_data($headers, $col_count, $jqgrid_data, $row_count, $rules);
		$validation_object = new InventoryValidation($this,$seller_id,$category_id);
		$data_recieved = $validation_object->callValidators($validate_arr);
		$final_data = $data_recieved['final_array'];
		$flag = $data_recieved['flag'];
		$warning = $data_recieved['warning'];
		$view_data = array();
		$view_data['headers'] = $headers;
		$view_data['row_count'] = $row_count;
		$view_data['col_count'] = $col_count;
		$view_data['final_data'] = $final_data;
		$view_data['rules'] = $rules;
		$view_data['flag'] = $flag;
		$view_data['warning'] = $warning;
		
	/*	if($flag == 1) {
			for($i = 1; $i < sizeof($final_data);$i = $i+2) {
				if (array_filter($final_data[$i])) {
					$errors_array['final_arr'][] = $final_data[$i-1];
					$errors_array['final_arr'][] = $final_data[$i];
					$errors_array['index'][] = $i-1;
				}	
			}
			
			//data to send to view
			//$view_data['final_data'] = $errors_array['final_arr'];
			//$indexes = array();
			//$indexes = $errors_array['index'];
		} else {
			$view_data['final_data'] = $final_data;
		} */
		
	
		
		$j = 0;
		
		/*if(isset($errors_array['index'])) {
			for($i = 0; $i < sizeof($original_data); $i++,$j=$j+2) {
				if(isset($indexes_of_error[$i]) && isset($final_data[$j])) {
					
					$original_data[$indexes_of_error[$i]] = $final_data[$j];	
				}		
			}

		} else if(!isset($errors_array['index'])) {
			
			for($i = 0; $i < sizeof($final_data); $i=$i+2) {
				if(isset($final_data[$i])) {
					$original_data[$i] = $final_data[$i];
				}	
			}		
		}*/
	
	    //storing sku codes in original array. this is to check for duplicate skus 
	/*	$sku_codes = array();
		
		//storing sku codes of array which is shown in grid in lower case without - and _
		$array = array();
		
		$array = array_map(function($val) { return preg_replace('/[-_]+/',"",$val); }, array_map('strtolower', array_column($final_data, 'SKU Code')));
		//getting sku_codes from previous data to check for duplicates
		$sku_codes = array_count_values(array_map(function($val) { return preg_replace('/[-_]+/',"",$val); }, array_map('strtolower', array_column($original_data, 'SKU Code'))));
	
		//this will check for duplicate sku methods and then assign the next index of array to the error message
	
		if(in_array("duplicate entries of sku code cannot exist",array_keys($sku_codes))) {
			foreach($sku_codes as $key=>$value) {
				if(($value > 1 && $key != "" && $key != "duplicate entries of sku code cannot exist")) {
					$index = array_keys($array, $key);
					$index_val = array_values($array);
					for($i = 0; $i<sizeof($index); $i++) {
						if(isset($final_data[$index[$i]+1])) {
							
							if($final_data[$index[$i]+1]['SKU Code'] != "duplicate entries of sku code cannot exist") {
								
								if(($index[$i]+1)%2 !=0) {
									$view_data['flag'] = 1;
									$flag = 1;
									$final_data[$index[$i]+1]['SKU Code'] = "duplicate entries of sku code cannot exist";
								}						
							} 
						}
					}
				}
			}
		}
		
		//initialising final_data
		$view_data['final_data'] = $final_data;

		//flag = 0 => errors are not present
		if($flag ==0) {
			$j = 0;
			if(isset($errors_array['index'])) {
				for($i = 0; $i < sizeof($original_data); $i++,$j=$j+2) {
					if(isset($errors_array['index'][$i])) {
					
						$original_data[$errors_array['index'][$i]] = $final_data[$j];
						$original_data[$errors_array['index'][$i]+1]['SKU Code'] = '';
						$original_data[$errors_array['index'][$i]+1]['Transfer Price'] = '';
						$original_data[$errors_array['index'][$i]+1]['AvailableSets'] = '';
						$original_data[$errors_array['index'][$i]+1]['Weight of a Piece (in gm)'] = '';
						$original_data[$errors_array['index'][$i]+1]['Any additional Comments'] = '';
						$original_data[$errors_array['index'][$i]+1]['Tax'] = '';	
					}		
				}
			} else if(!isset($errors_array['index'])) {
				
				for($i = 0; $i < sizeof($final_data); $i=$i+2) {
					$original_data[$i] = $final_data[$i];
					$original_data[$i+1]['SKU Code'] = '';
					$original_data[$i+1]['Transfer Price'] = '';
					$original_data[$i+1]['AvailableSets'] = '';
					$original_data[$i+1]['Weight of a Piece (in gm)'] = '';
					$original_data[$i+1]['Any additional Comments'] = '';
					$original_data[$i+1]['Tax'] = '';	
					}		
				}
				
				$data_insert = array_filter(array_map('array_filter', $original_data));
				
				$insert->insertData($import_csv_id,$data_insert, $flag, $date_time);
			
		
				$this->response->setOutput(json_encode( 
												array(
													'final_data' => $original_data,
													'flag'=>$view_data['flag'],
													'warning' => $view_data['warning'],
												)
											));	
			}
			*/
			
		//getting filters from database if no errors are present in the sheet
		if($flag === 0) {
			$insert = new InsertInventory($this->db);
			$insert->insertData($import_csv_id,$jqgrid_data, $flag, $date_time,$product_ids,$product_ids_old);
			//fetching filters
			$this->load->model('filters');
			$filter_groups= $this->model_filters->getFilterGroups($category_id);
			$non_mandatory_filter_groups= $this->model_filters->getNonMandatoryFilters($category_id);
			$filters = $this->model_filters->getFilters($filter_groups);
			$non_mandatory_filters = $this->model_filters->getFilters($non_mandatory_filter_groups);
		
			$this->response->setOutput(json_encode( 
											array(
												'final_data' => $view_data['final_data'],
												'flag'=>$view_data['flag'],
												'filters' => $filters,
												'filter_groups' => $filter_groups,
												'non_mandatory_filter_groups' => $non_mandatory_filter_groups,
												'non_mandatory_filters' => $non_mandatory_filters,
												'warning' => $view_data['warning'],
											)
										));	
			
		} else if($view_data['flag'] == 1) {
			$insert = new InsertInventory($this->db);
			$insert->insertData($import_csv_id,$jqgrid_data, $flag, $date_time,$product_ids,$product_ids_old);
			
			//looping again for the same checking in case errors are present
			$this->response->setOutput(json_encode( 
											array(
												'final_data' => $view_data['final_data'],
												'flag'=>$view_data['flag'],
												'warning' => $view_data['warning'],	
											)
										));	
		}
		
    }
    
    //updating filters in database and adding filters to it
    public function filtersId() {
		
		$date_time=  date("Y-m-d");	
		$time = date("h:i:sa");
		$date_time = $date_time." ".$time;
		
		if(!empty($this->request->post['filter_group'])){
			$filter_group = $this->request->post['filter_group'];
			$filter_ids = $this->request->post['filter_ids'];
			$product_ids = $this->request->post['product_ids'];
			$insert = new InsertInventory($this->db);
			$insert->insertFilterWithIds($filter_group,$filter_ids,$date_time,$product_ids);
		}
	}
	
	//getting size set grid and updating data in datbase
	 public function getSize() {
		
		$date_time=  date("Y-m-d");	
		$time = date("h:i:sa");
		$date_time = $date_time." ".$time;
		
		if(!empty($this->request->post['Size_Set'])){
			$Size_Set 	= 	$this->request->post['Size_Set'];
			$product_ids 	= 	$this->request->post['product_ids'];
			$set_type_arr = $this->request->post['set_type_arr'];
		
			$insert = new InsertInventory($this->db);
			
			//0 for size set
			foreach($set_type_arr as $key=>$value) {
				//if set type size set or free set found, then break
				if($value['Set Type'] == 2 || $value['Set Type'] == 3) {
					$exists_other_set = 1;
					break;
				} else {
					//if set type free set not found, validated needs to be 1 after size set submission only
					$exists_other_set = 0;
				}
			}
		
			$insert->insertSet(0,$Size_Set,$product_ids,$date_time,$exists_other_set);
			$this->response->setOutput(json_encode( 
												array(
													'msg' => $exists_other_set,
												)
											));	
		}
	}
	
	//getting color set grid and updating data in datbase
	public function getColor() {
		
		$date_time=  date("Y-m-d");	
		$time = date("h:i:sa");
		$date_time = $date_time." ".$time;
		
		if(!empty($this->request->post['Color_Set'])){
			$Color_Set 	= 	$this->request->post['Color_Set'];
			$product_ids 	= 	$this->request->post['product_ids'];
			$set_type_arr = $this->request->post['set_type_arr'];

			
			foreach($set_type_arr as $key=>$value) {
				//if set type size set or free set found, then break
				if($value['Set Type'] == 2 || $value['Set Type'] == 0 || $value['Set Type'] == 3) {
					$exists_other_set = 1;
					break;
				} else {
					//if set type size set not found or free set not found, validated needs to be 1 after color set submission only
					$exists_other_set = 0;
				}
			}
			
			$insert = new InsertInventory($this->db);
			//1 for color set
			$insert->insertSet(1,$Color_Set,$product_ids,$date_time,$exists_other_set);
			$this->response->setOutput(json_encode( 
												array(
													'msg' => $exists_other_set,
												)
											));	
		}
	}
	
	//getting free set grid and updating data in datbase
	public function getFreeSize() {
		
		$date_time=  date("Y-m-d");	
		$time = date("h:i:sa");
		$date_time = $date_time." ".$time;
		
		if(!empty($this->request->post['Free_Size'])){
			$Free_Set 	= 	$this->request->post['Free_Size'];
			$product_ids 	= 	$this->request->post['product_ids'];
			$set_type_arr = $this->request->post['set_type_arr'];
			
			foreach($set_type_arr as $key=>$value) {
				//if set type size set or free set found, then break
				if($value['Set Type'] == 3) {
					$exists_other_set = 1;
					break;
				} else {
					//if set type size set not found or free set not found, validated needs to be 1 after color set submission only
					$exists_other_set = 0;
				}
			}
			$insert = new InsertInventory($this->db);
			//2 for free set
			$insert->insertSet(2,$Free_Set,$product_ids,$date_time,$exists_other_set);
			
			$this->response->setOutput(json_encode( 
												array(
													'msg' => $exists_other_set,
												)
											));	
		}
	}
    
    public function getNotSpecificSet() {
		
		$date_time=  date("Y-m-d");	
		$time = date("h:i:sa");
		$date_time = $date_time." ".$time;
		//by default setting exists_other set as 1 
		$exists_other_set = 1;
		
		if(!empty($this->request->post['not_specific_data'])){
			$not_specific_data 	= 	$this->request->post['not_specific_data'];
			$product_ids 	= 	$this->request->post['product_ids'];
			$set_type_arr = $this->request->post['set_type_arr'];
			
			
			$insert = new InsertInventory($this->db);
			//3 for free set
			$insert->insertSet(3,$not_specific_data,$product_ids,$date_time,$exists_other_set);
			
			$this->response->setOutput(json_encode( 
												array(
													'msg' => $exists_other_set,
												)
											));	
		}
		
	}
	
	public function import($category_id){
		
		$category_name = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'")->row['name'];
		
		$date = date('Y_M_D');
		$time = date("h_i_sa");
		$var = array();
		$var['date_time'] = $date.'_'.$time;
		$seller_id = $this->customer->getId();
		$timestamp = time();

		$file_name_new = $seller_id.'_'.$category_name.'_'.$timestamp;
		
		
		date_default_timezone_set('Asia/Kolkata');
		
		$ext = "";
		require 'system/library/PHPExcel/IOFactory.php';
		$uploads_dir = DIR_SYSTEM . 'upload/';
		if( isset($_FILES['file']['name'])) {
			
			$file_name = $_FILES['file']['name'];
			$ext = pathinfo($file_name,PATHINFO_EXTENSION);
		}
		
		if(($ext == "xlsx")||($ext == "xls")){

			$file_name = $_FILES['file']['tmp_name'];
			move_uploaded_file($_FILES["file"]["tmp_name"] , DIR_UPLOAD_SELLER_INVENTORY.$_FILES["file"]["name"]);
			//renaming file to seller_id._.timestamp
			rename(DIR_UPLOAD_SELLER_INVENTORY.$_FILES["file"]["name"], DIR_UPLOAD_SELLER_INVENTORY.$file_name_new.'.'.$ext);
			
			$file_path =  DIR_UPLOAD_SELLER_INVENTORY.$file_name_new.'.'.$ext;
			exec("chmod 0777 $file_path");     
			$inputFile = $file_path;
			$date_time = $date.$time;
			$import_var = array();
			$import_var['inputfile'] = $inputFile;
			$import_var['date_time'] = $date_time;
			return $import_var;
			
		} else {
			echo '<p style="color:red;">Please upload file with xlsx , xls extension only</p>';
		}
	}
	
	public function getDelete() {
		//print_r($_POST);
	}
	
	/*
	 * method to get new category added by seller
	 * @author : Divya Porwal
	 * */
	 
	public function getNewCategoryAndSendMail() {
		
		if(isset($this->request->post['category_name'])) {
			$category_name = $this->request->post['category_name'];
			
			$seller_id = $this->customer->getId();
			$seller_mail = $this->db->query("SELECT email from ". DB_PREFIX. "ms_seller WHERE seller_id = ".(int)($seller_id)."")->row['email'];
			$text = MailTemplate::getGeneralHeader();
			$text .= '<div style="width:680px;">';
			$text .= "<br>Dear All,<br>\n\n";
			$text .= "Greetings from WholesaleBox!<br>\n\n";
			$text .= "This is to bring to your notice that a new category named ". $category_name ." has been requested.\n"; 
			$text .= MailTemplate::getGeneralFooter();
			$text .= '</div>';
			
			$mail = new  PHPMailer();
			$mail->isSMTP();
			$mail->Host = $this->config->get('config_mail_smtp_hostname');
			$mail->Port = $this->config->get('config_mail_smtp_port');
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->Username = $this->config->get('config_mail_smtp_username');
			$mail->Password = $this->config->get('config_mail_smtp_password');
			$mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
			$mail->addAddress($seller_mail, "Sellers WholesaleBox");
			$mail->addCC(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);
			$mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
			$mail->Subject = "Notification for Archived Inventory - " . date('d/M/Y', time());
			$mail->msgHTML($text);	
			$mail->send();   
			
		}
	}
		

}
?>
