<?php
class ControllerSellerUpdateBulkPrice extends Controller {
	private $error = array();

	public function index() {

		$this->load->model('seller/product');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('seller/update_bulk_price', $data);

		$this->document->setTitle($data['heading_title']);
		
		$data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $data['ms_account_dashboard'],
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $data['ms_account_bulk_price'],
				'href' => $this->url->link('seller/update_bulk_price', '', 'SSL'),
			)
		));
		
		
		$data['download_wholesale_list'] = $this->url->link('seller/update_bulk_price/getAllSellerProducts&get_products=wholesale', 'SSL');
		$data['import_product_list'] = $this->url->link('seller/update_bulk_price/index&upload_products=true', '', 'SSL');
		
		$data['header_seller'] = $this->load->controller('common/seller_header');
		$data['footer_seller'] = $this->load->controller('common/seller_footer');
		
		if ( !empty($this->request->get['upload_products']) ) {

			if(empty($this->request->files['seller_product']['name'])){
				$this->response->redirect($this->url->link('seller/update_bulk_price', "", 'SSL'));
			}
			// Sanitize the filename
			$filename = basename(html_entity_decode($this->request->files['seller_product']['name'], ENT_QUOTES, 'UTF-8'));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
				$this->error['warning']['filename']  = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array('csv',
							 'xls',
							 'xlsx');

			if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
				$this->error['warning']['filetype'] = $this->language->get('error_filetype');

			}
			// Allowed file mime types
			$allowed = array('application/vnd.ms-excel',
							 'text/plain',
							 'text/csv',
							 'text/tsv');

			if (!in_array($this->request->files['seller_product']['type'], $allowed)) {
				$this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
				// $json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['seller_product']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
			}

			// Return any upload error
			if ($this->request->files['seller_product']['error'] != UPLOAD_ERR_OK) {
				$this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['seller_product']['error']);
			}
			if (!$this->error) {
				$file_handle = fopen($this->request->files['seller_product']['tmp_name'], 'r');
				
				while (!feof($file_handle) ) {
					$line_of_text[] = fgetcsv($file_handle, 1024);
				}
			
				fclose($file_handle);
				
				
				$product_info = array();
				$product_ids_price_updated = array();
				$product_ids_qty_updated = array();
				$product_ids_hsn_code_updated = array();
				$i = 0;
                
                $nickname = SellerInfo::getSellerFirmDetails($this->db, $this->customer->getId())['nickname'];
                
				foreach($line_of_text as $cav_data){
					if (empty($cav_data['0']) || $i ==0 || $i == 1) {
						$i++;
						continue;
					}
					
					$product_id = $cav_data['0'];
					$sku 		= $cav_data['2'];
					$old_price 	= $cav_data['3'];
					$old_stock 	= $cav_data['4'];
					$new_price 	= $cav_data['5'];
					$new_stock 	= $cav_data['6'];
					$hsn_code   = trim($cav_data['7']);					
					
					$run_query = 0; $price_update = 0; $stock_update = 0;
					$sql = "UPDATE " . DB_PREFIX . "product SET ";
					if( !empty($new_price) && is_numeric($new_price) ) {
						$sql .= " price = LEAST(price, " . (float)$new_price . ") ";
						$product_ids_price_updated[] = $product_id;
						$run_query = 1;
						$price_update = 1;

						if( $new_price < $old_price ){
							$insert_price_log = " INSERT INTO " . DB_PREFIX . "seller_change_log
											  SET seller_id = '".(int)$this->customer->getId()."',
												  product_id = '". (int)$product_id."',  
                                                  product = '" . $this->db->escape($sku) . "', 
                                                  nickname = '" . $this->db->escape($nickname) . "', 
												  updated_type = '".$this->db->escape('price')."',
												  old = '" . (float)$old_price . "',
												  new = '" . (float)$new_price . "',
												  modified = NOW()";
							$this->db->query($insert_price_log);	
						}
					}

					if( is_numeric($new_stock) ){

						$sql .= ($price_update == 1 ? ", " : "");
						$sql .= " quantity = " . (int)$new_stock;
						$stock_update = 1;
						$product_ids_qty_updated[] = $product_id;
						

						$insert_stock_log = " INSERT INTO " . DB_PREFIX . "seller_change_log
											  SET seller_id = '".(int)$this->customer->getId()."',
												  product_id = '". (int)$product_id."', 
                                                  product = '" . $this->db->escape($sku) . "', 
                                                  nickname = '" . $this->db->escape($nickname) . "', 
										  		  updated_type = '".$this->db->escape('set_quantity')."',
												  old = '" . (int)$old_stock . "',
												  new = '" . (int)$new_stock . "',
												  modified = NOW()";
						$this->db->query($insert_stock_log);
					}
					
					if( strlen($hsn_code) >= 4 && strlen($hsn_code) <= 8 && preg_match("/[0-9]{4,}/", $hsn_code) ){
						
						$sql .= ($stock_update == 1 || $price_update == 1 ? ", " : "");
						$hsncode = $this->model_seller_product->checkHSNCodeInProductBySeller($product_id);
						if($hsncode){
							$hsncode = $hsncode;
						}else {
							$hsncode = $hsn_code;
						}
						$sql .= " hsn_code = '". $this->db->escape($hsncode). "' ";
						$run_query = 1;
						$product_ids_hsn_code_updated[] = $product_id;

						$hsn_code_log = "INSERT INTO " . DB_PREFIX . "seller_change_log
											  SET seller_id = '".(int)$this->customer->getId()."',
												  product_id = '". (int)$product_id."', 
                                                  product = '" . $this->db->escape($sku) . "', 
                                                  nickname = '" . $this->db->escape($nickname) . "', 
										  		  updated_type = '".$this->db->escape('hsn_code')."',
												  old = '".$this->db->escape($hsn_code)."',
												  new = '".$this->db->escape($hsn_code)."',
												  modified = NOW()";
					
						$this->db->query($hsn_code_log);
					}
					
					
					if ($run_query) {
						$sql .= " WHERE product_id = " . (int)$product_id;
						$this->db->query($sql);
					}	
				}
				$this->error['warning']['success'] = 'The file has been uploaded succesfully. Please note that price increases and Invalid HSN Codes have been ignored!';
			}
			
			$data['errors'] = 	$this->error;
		}
		$this->response->setOutput($this->load->view('default/template/multiseller/update_bulk_price.tpl', $data));
	}	
	

	/**
	 * Method for get all seller products from store_id = 0 i.e. Wholesalebox store
	 * @return : NULL / csv file download
	 * @author : Vikas , 2017
	 **/
	public function getAllSellerProducts(){
       $this->load->model('seller/product');
       $data = array();

        $seller_id = $this->customer->getId();
        $con_wholesale['seller_id']=$seller_id;

        $cols = array();

        $filter_data = array();

        $seller_product_data = $this->model_seller_product->getSellerProductsForBulkUpdate($seller_id);  
           
        // Check if this seller is not a WSB store seller
        $wsb_store_sellers = explode(',',WSB_STORE_SELLERS);
        $seller_is_wsb_store = false;
        if ( in_array($seller_id, $wsb_store_sellers) ) {
            $seller_is_wsb_store = true;
        }
        
        if (!file_exists(DIR_SYSTEM.'upload/assets/seller_products/'.$seller_id)) {
            mkdir(DIR_SYSTEM.'upload/assets/seller_products/'.$seller_id, 0777,true);
        }
	
		$file_csv = DIR_SYSTEM.'upload/assets/seller_products/'.$seller_id.'/seller_wholesale_product_data.csv';
		$fp = fopen($file_csv, 'w');
    	
        if(isset($this->request->get['get_products']) && $this->request->get['get_products'] == "wholesale"){
			$i = 1;			
			foreach($seller_product_data as $result){
				
				if( !$seller_is_wsb_store && $result['store_sales'] !='NO'){
					continue;
				} 

			    $pid =  (int)$result['product_id'];
                $name = html_entity_decode($result['name']);
			    $sku =  $result['sku'];
				$current_price = (float)$result['price'];
				$current_stock = (int)$result['quantity'];
				$new_price = "";
				$new_stock = "";
				$hsn_code = ((int)$result['hsn_code'] ? $result['hsn_code'] : '');
				
				if($i == 1){
					$i++;
					$data = array('Product ID', 'Name', 'SKU','Current price','Current Stock','New Price', 'New Stock', 'HSN Code');
				}else{
					$i++;
					$data = array($pid,$name, $sku,$current_price,$current_stock,$new_price,$new_stock,$hsn_code);
				}

				fputcsv($fp, $data);
			}
		}

        fclose($fp);
         
		$file = $file_csv;
		$datetime = Date("Y-m-dH:i:s");
		$datetime = strtotime($datetime);
        $nickname = SellerInfo::getSellerFirmDetails($this->db, $this->customer->getId())['nickname'];
		$file_name = "inventory_".$datetime."_".$nickname.".csv";
        header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename="'.$file_name.'"');
		readfile($file);        
	}
}
