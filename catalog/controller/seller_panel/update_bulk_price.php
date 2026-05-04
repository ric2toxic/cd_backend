<?php
class ControllerSellerUpdateBulkPrice extends Controller {
	private $error = array();

	public function index() {

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
				
				// echo "<pre>"; print_r($line_of_text); exit;
				$product_info = array();
				$product_ids_price_updated = array();
				$product_ids_qty_updated = array();
				$i = 0;
				foreach($line_of_text as $cav_data){
					if (empty($cav_data['0']) || $i ==0) {
						$i++;
						continue;
					}
					
					$product_id = $cav_data['0'];
					$sku 		= $cav_data['1'];
					$old_price 	= $cav_data['2'];
					$old_stock 	= $cav_data['3'];
					$new_price 	= $cav_data['4'];
					$new_stock 	= $cav_data['5'];

					$run_query = 0; $price_update = 0;
					$sql = "UPDATE " . DB_PREFIX . "product SET ";
					if( !empty($new_price) && is_numeric($new_price) ) {
						$sql .= " price = LEAST(price, " . (float)$new_price . ") ";
						$product_ids_price_updated[] = $product_id;
						$run_query = 1;
						$price_update = 1;
					}

					if( is_numeric($new_stock) ){

						$sql .= ($price_update == 1 ? ", " : "");
						$sql .= " quantity = " . (int)$new_stock;
						$run_query = 1;
						$product_ids_qty_updated[] = $product_id;
					}

					if ($run_query) {
						$sql .= " WHERE product_id = " . (int)$product_id;
						$this->db->query($sql);
					}

				}
				
				$sql = $this->db->query("UPDATE " . DB_PREFIX . "product
				 		                 SET selling_price = ceil((price * (1 + (commission/100)))/(1 + (seller_tax/100)))
				 		                 WHERE product_id IN (". implode(',',$product_ids_price_updated).")");

				$this->error['warning']['success'] = 'The file has been uploaded succesfully. Please note that price increases have been ignored!';

			}
			$data['errors'] = 	$this->error;
		}
		$this->response->setOutput($this->load->view('default/template/multiseller/update_bulk_price.tpl', $data));
	}
	/**
	 * 
	 **/
	public function ImportInventry(){
		
		
		$this->excelreader->setOutputEncoding('CP1251');
		//$this->excelreader->read($_FILES['seller_product']['tmp_name']);
		$this->excelreader->read("/home/ravindra/Desktop/Wholesalebox - Ladies Garments_v2.xls");
		//echo "<pre>"; print_r($this->excelreader->sheets); exit;
		
		$this->load->language('seller/update_bulk_price');
		//echo "<pre>"; print_r($_FILES); //exit;
		$file_handle = fopen($_FILES['seller_product']['tmp_name'], 'r');
		while (!feof($file_handle) ) {
			$line_of_text[] = fgetcsv($file_handle, 1024);
		}
		fclose($file_handle);
		// echo "<pre>"; print_r($line_of_text); exit;
		$i = 0;
		foreach($line_of_text as $data){
			if($i != "0"){
				$sku = $data['0'];
				$new_price = $data['3'];
				$new_stock = $data['4'];
				$record = $this->db->query("SELECT seller_tax, commission, piece_in_set FROM ".DB_PREFIX."product WHERE sku = '" . $this->db->escape(trim($sku)) . "' AND is_single = 0");
				// echo "<pre>"; print_r($record); exit;
				if(!empty($record->row)){
					$piece_in_set = $record->row['piece_in_set'];
					$price_per_set = $new_price * $piece_in_set;
					$seller_tax_factor = 1.0 + ( (float)$record->row['seller_tax'] / 100.0 );
					$commission_factor = 1.0 + ( (float)$record->row['commission'] / 100.0 );
					$selling_price = ceil($commission_factor * $new_price / $seller_tax_factor);
					$new = $new_price;
					$type = 'price';
					//$this->InformSellerChangeLog($type, $new, $product_id);
					//echo "UPDATE " . DB_PREFIX . "product SET price = ".$new_price.", price_per_set = ".$price_per_set.", selling_price = " . $selling_price . " WHERE sku = '" . $sku . "'"; exit;
					if (is_numeric($new_price) && is_numeric($new_stock)) {
						$this->db->query("UPDATE " . DB_PREFIX . "product SET price = ".$new_price.", quantity = ".$new_stock.", price_per_set = ".$price_per_set.", selling_price = " . $selling_price . " WHERE sku = '" . $sku . "' AND is_single = 0"); 
					}
					/*if (is_numeric($new_price) && is_numeric($new_stock)) {
						$this->db->query("UPDATE " . DB_PREFIX . "product SET price = ".$new_price.", quantity = ".$new_stock.", price_per_set = ".$price_per_set.", selling_price = " . $selling_price . " WHERE sku = '" . $sku . "' AND is_single = 0"); 
					}*/
				}
			}
			$i++;
		}
		
		$this->response->redirect($this->url->link('seller/update_bulk_price', "", 'SSL'));
		//return $line_of_text;
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
        
        if (!file_exists(DIR_SYSTEM.'upload/assets/seller_products/'.$seller_id)) {
            mkdir(DIR_SYSTEM.'upload/assets/seller_products/'.$seller_id, 0777,true);
        }
	
		$file_csv = DIR_SYSTEM.'upload/assets/seller_products/'.$seller_id.'/seller_wholesale_product_data.csv';
		$fp = fopen($file_csv, 'w');
    	
        if(isset($this->request->get['get_products']) && $this->request->get['get_products'] == "wholesale"){
			$i = 1;			
			foreach($seller_product_data as $result){
			    $pid =  $result['product_id'];
			    $sku =  $result['sku'];
				$current_price = $result['price'];
				$current_stock = $result['quantity'];
				$new_price = "";
				$new_stock = "";
				
				if($i == 1){
					$i++;
					$data = array('Product ID','SKU','Current price','Current Stock','New Price', 'New Stock');
				}else{
					$i++;
					$data = array($pid,$sku,$current_price,$current_stock,$new_price,$new_stock);
				}

				fputcsv($fp, $data);
			}
		}

        fclose($fp);
         
		$file = $file_csv;
		$datetime = Date("Y-m-dH:i:s");
		$datetime = strtotime($datetime);
		$file_name = "inventory_".$datetime."_".$this->MsLoader->MsSeller->getNickname().".csv";
        header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename="'.$file_name.'"');
		readfile($file);        
	}	
}
