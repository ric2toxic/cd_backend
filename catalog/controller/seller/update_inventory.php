<?php
class ControllerSellerUpdateInventory extends Controller {
	private $error = array();
	public function index() {
		
		$this->load->language('seller/update_inventory');
		$this->load->model('seller/category');
		$this->load->model('setting/store');
		$this->document->setTitle($this->language->get('heading_title'));
		$data['download_excel'] = $this->language->get('text_download_file');
		$data['upload_excel'] = $this->language->get('text_upload_file');
		//echo "herre"; exit;
		$stores = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
		//echo "<pre>"; print_r($stores); exit;
		foreach($stores as $store){
			$stores_data[] = $this->model_setting_store->getStore($store['store_id']);	
		}
		$data['stores_data'] = $stores_data;
		
		$filter_data = array();
	    /*$total_categories = $this->model_seller_category->getCategories($filter_data);
	    foreach($total_categories as $category){
			if($category['parent_id'] == 0){
				$categories[] = $category;
			}
		}*/
		$categories = $this->variables->get_variables();
	    $data['categories'] = $categories;
	    //echo "<pre>"; print_r($categories); exit;
		$data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_account_dashboard'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_upload_inventory'),
				'href' => $this->url->link('seller/update_inventory', '', 'SSL'),
			)
		));
		
		
		$data['download_inventry_list'] = $this->url->link('seller/update_inventory/download_excel', '', 'SSL');
		$data['import_inventry_list'] = $this->url->link('seller/update_inventory/ImportInventry', '', 'SSL');
		
		$data['header_seller'] = $this->load->controller('common/seller_header');
		$data['footer_seller'] = $this->load->controller('common/seller_footer');

		$this->response->setOutput($this->load->view('default/template/multiseller/update_inventory.tpl', $data));
		
	
	}
	 /**
	 * @function download_excel
	 * method Get
	 **/
	 
	 public function download_excel(){
		if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
        } else {
            $category_id = '61';//$this->config->get('config_product_limit');
        }
        $variables = $this->variables->get_variables();
        $cat_folder = $variables[$category_id];
		$file = DIR_SYSTEM.'upload/assets/download/format/'.$cat_folder.'/Wholesalebox - Ladies Garments_v2.xls';    
        header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename="'.$file.'"');
		readfile($file);
	 }
	 
	
	/**
	 * @function ImportInventry
	 **/
	public function ImportInventry(){
		
		
		$this->load->model('seller/update_inventory');
		$this->load->model('account/customer');
		
		$this->load->language('seller/update_inventory');
		$this->load->model('seller/category');
		$this->document->setTitle($this->language->get('heading_title'));
		$data['download_excel'] = $this->language->get('text_download_file');
		$data['upload_excel'] = $this->language->get('text_upload_file');
		//echo "herre"; exit;
		
		$stores = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
		//echo "<pre>"; print_r($this->request->post); //exit;
		foreach($stores as $store){
			$stores_data[] = $this->model_setting_store->getStore($store['store_id']);	
		}
		$data['stores_data'] = $stores_data;
		$filter_data = array();

		$categories = $this->variables->get_variables();
		if(isset($this->request->post['cat_id']) && !empty($categories[$this->request->post['cat_id']])){
			$model = $categories[$this->request->post['cat_id']];
		}else{
			$model = 'kurti';			
		}
		
	    $data['categories'] = $categories;
	    
		$data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_account_dashboard'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_upload_inventory'),
				'href' => $this->url->link('seller/update_inventory', '', 'SSL'),
			)
		));
		
		
		if(isset($this->request->post['stores'])){
			$store_selected = $this->request->post['stores'];
		}else{
			$store_selected = array('0');
		}
		if(!isset($_FILES['seller_product']['tmp_name']) && empty($_FILES['seller_product']['tmp_name'])){
			$this->response->redirect($this->url->link('seller/update_inventory', '', 'SSL'));
		}
		$this->excelreader->read($_FILES['seller_product']['tmp_name']);
		//$this->excelreader->read("/home/ravindra/Desktop/Kurti_v1.xls");
		$sheet_data = $this->excelreader->sheets;
		
		$warnings = array();
		$success = array();
		$results = array();
		$product_data_key = array();
		$this->load->model('importvalidation/kurti');
		foreach($sheet_data as $res){
			$i = 0;
			$a = 0;
			foreach($res['cells'] as $product_data){
				if($i == 0){ 
					$product_data_key = $product_data;
				}

				if($i >= 4){  
					if(isset($product_data[2]) && !empty($product_data[2])){ 
						$param['product_data_key'] = $product_data_key;
						$param['product_data'] = $product_data;
						
						$rt = $this->model_importvalidation_kurti->validate($param);
						
						if(empty($rt)){
							//Product save
							$this->model_importvalidation_kurti->meta_data();
							$product_data_map = $this->model_importvalidation_kurti->product_data_map();
							$product_data_map['product_store'] = $store_selected;
							$result = $this->model_seller_update_inventory->addProduct($product_data_map);
							
							$success['product_sku']	= $product_data[2];
							$success['product_name'] = $product_data[1];
							$success['warnings'] = $rt;
						}else{
							$success['product_sku']	= $product_data[2];
							$success['product_name'] = $product_data[1];
							$success['warnings'] = $rt;
							$warnings[] = $rt;
						}
						$results[] = $success;
					}
					
				}
				
				$i++;
			}
		}

		$data['results'] = $results;
		$data['download_inventry_list'] = $this->url->link('seller/update_inventory/download_excel', '', 'SSL');
		$data['import_inventry_list'] = $this->url->link('seller/update_inventory/ImportInventry', '', 'SSL');
		
		$data['header_seller'] = $this->load->controller('common/seller_header');
		$data['footer_seller'] = $this->load->controller('common/seller_footer');
		
		$this->response->setOutput($this->load->view('default/template/multiseller/update_inventory.tpl', $data));
		
	}	
	
}
