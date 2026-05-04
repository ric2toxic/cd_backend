<?php
class ControllerTotalPaycharge extends Controller {
	private $error = array();

	public function index() {
		$fs_mod = 'PayCharge Free v5.0';
        $data = array(); // Initializing the data array to be passed on to template files
		$data['fs_version'] = '<a href="http://www.opencart.com/index.php?route=extension/extension&filter_username=fabiom7">' . $fs_mod . '</a><br />Powered by <a href="http://www.fabiom7.com">fabiom7</a>';

		//$this->load->language('total/paycharge');

	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('total/paycharge', $data);
		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');
		$this->load->model('catalog/category');

		$sendAllData = array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('paycharge', $this->request->post);


			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

   		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text' => $data['text_home'],
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
   		);

   		$data['breadcrumbs'][] = array(
       		'text' => $data['text_total'],
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'),
   		);

   		$data['breadcrumbs'][] = array(
       		'text' => $data['heading_title'],
			'href' => $this->url->link('total/paycharge', 'token=' . $this->session->data['token'], 'SSL'),
   		);

		$data['action'] = $this->url->link('total/paycharge', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('extension/extension');

		$data['payments'] = array();

		foreach ($this->model_extension_extension->getInstalled('payment') as $payment) {
			if (file_exists(DIR_APPLICATION . 'controller/payment/' . $payment . '.php')) {
				$this->load->language('payment/' . $payment);
				$data['payments'][] = array(
					'name' => strtoupper(str_replace('_',' ',$payment)),
					'code' => $payment,
				);
			}
		}
        
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['paycharge_status'])) {
			$data['paycharge_status'] = $this->request->post['paycharge_status'];
		} else {
			$data['paycharge_status'] = $this->config->get('paycharge_status');
		}

		if (isset($this->request->post['paycharge_sort_order'])) {
			$data['paycharge_sort_order'] = $this->request->post['paycharge_sort_order'];
		} else {
			$data['paycharge_sort_order'] = $this->config->get('paycharge_sort_order');
		}

		if (isset($this->request->post['paycharge'])) {
			$paycharges = $this->request->post['paycharge'];
		} else {
			$paycharges = $this->model_setting_setting->getPaycharges();
			if(empty($paycharges)) {
				$paycharges = array('0' => array('payment_method' => 0, 'valuep' => ''));
			}
		} 

		$data['paycharges'] = $paycharges;
		$data['paycharge'] = $paycharges[0] ?? array();

		//get product categories
		$data['categories'] = array();
		$categories_1 = $this->model_catalog_category->getCategoriesByParent(0);
		foreach ($categories_1 as $category_1) {
			$level_2_data = array();

			$categories_2 = $this->model_catalog_category->getCategoriesByParent($category_1['category_id']);

			foreach ($categories_2 as $category_2) {
				$level_3_data = array();

				$categories_3 = $this->model_catalog_category->getCategoriesByParent($category_2['category_id']);

				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
							'category_id' => $category_3['category_id'],
							'name'        => $category_3['name'],
					);
				}

				$level_2_data[] = array(
						'category_id' => $category_2['category_id'],
						'name'        => $category_2['name'],
						'children'    => $level_3_data
				);
			}

			$data['categories'][] = array(
					'category_id' => $category_1['category_id'],
					'name'        => $category_1['name'],
					'children'    => $level_2_data
			);
		}

		$data['image_path'] = HTTPS_CATALOG.'khufiya_vibhag/view/image/ajax-loader.gif';
		
		$data['token'] = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/paycharge.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/paycharge')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}


	public function paycharge_rules()
	{
		$pid 	= $this->request->get['pid'] ?? '';
		$pmodel = $this->request->get['pmodel'] ?? '';
		$psku 	= $this->request->get['psku'] ?? '';
		$catid 	= $this->request->get['catid'] ?? '';
		$seller_nickname 	= $this->request->get['seller_nickname'] ?? '';
		$this->load->model('setting/setting');
		$this->load->model('catalog/category');
		$this->load->model('sellers/sellers');
		$this->load->model('catalog/product');

		$result = array();
		if(!empty($pid)) 
		{
			if(!empty($pid)) {
				$product = $this->model_catalog_product->getProduct($pid);
				if(!empty($product)) {
					$result['product'][$product['product_id']] = array(
			        		'product_id' 		=> $product['product_id'],
			        		'name'		 		=> $product['name'],
			        		'model'				=> $product['model'],
			        		'sku'				=> $product['sku'],
			        		'isException' 		=> $this->model_setting_setting->isPaychargeExceptionExists('product',$product['product_id']),
			        		'applicableOnMembership' => $this->model_setting_setting->getApplicableOnMembershipDiscountStatus('product',$product['product_id'])
			        	);
				}
			}			
		}// end of pcode

		if(!empty($pmodel) || !empty($psku)) 
		{	
			$model = array();
			$sku = array();
			if(!empty($pmodel)) {
				$model = array('filter_model' => $pmodel);
			}
			if(!empty($psku)) {
				$sku = array('filter_seller_sku' => $psku);
			}
			$filter_data = array_merge($model,$sku);
			if(!empty($filter_data)) {
				$product = $this->model_catalog_product->getProducts($filter_data);
				if(!empty($product['products'])) {
					foreach ($product['products'] as $key => $value) {
						$result['product'][$value['product_id']] = array(
			        		'product_id' 		=> $value['product_id'],
			        		'name'		 		=> $value['name'],
			        		'model'				=> $value['model'],
			        		'sku'				=> $value['sku'],
			        		'isException' 		=> $this->model_setting_setting->isPaychargeExceptionExists('product',$value['product_id']),
			        		'applicableOnMembership' => $this->model_setting_setting->getApplicableOnMembershipDiscountStatus('product',$value['product_id'])
			        	);
					}
				}
			}
		}
		
		if(!empty($result['product'])) {
			$result['product'] = array_values($result['product']);
		}

		if(!empty($catid))
		{
			$category = $this->model_catalog_category->getCategory($catid);
	        if(!empty($category)){
	        	$result['category'] = array(
	        		'category_id' => $catid,
	        		'name' 		  => $category['name'],
	        		'isException' 		=> $this->model_setting_setting->isPaychargeExceptionExists('category',$catid),
	        		'applicableOnMembership' => $this->model_setting_setting->getApplicableOnMembershipDiscountStatus('category',$catid)
	        	);
	        }
		}// end of catid

		if(!empty($seller_nickname))
		{
			$seller_info = SellerInfo::getSellerByNickname($this->db, $seller_nickname);
        	if(!empty($seller_info['nickname']))
        	{
        		$result['seller'] = array(
	        		'seller_id' 	=> $seller_info['seller_id'],
	        		'nickname' 		=> $seller_info['nickname'] ?? '',
	        		'company'		=> $seller_info['company'] ?? '',
	        		'isException' 	=> $this->model_setting_setting->isPaychargeExceptionExists('seller',$seller_info['seller_id']),
	        		'applicableOnMembership' => $this->model_setting_setting->getApplicableOnMembershipDiscountStatus('seller',$seller_info['seller_id'])
	        	);
        	}
		}// end of sid
		echo json_encode($result); 
	}

	public function updateException()
	{
		$action 	= $this->request->get['action'] ?? '';
		$type 		= $this->request->get['type'] ?? '';
		$type_id 	= $this->request->get['type_id'] ?? '';
		$applicable = $this->request->get['applicable'] ?? '';
		
		$this->load->model('setting/setting');
		try{
			switch ($action) {
				case 'add':
					$this->model_setting_setting->addException($type, $type_id, $applicable);
					break;
				
				case 'delete':
					$this->model_setting_setting->deleteException($type, $type_id);
					break;
				default:
					# code...
					break;
			}
			echo 'success';
		}catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function updateApplicableStatus()
	{
		$exception_rule_id = $this->request->get['exception_rule_id'] ?? '';
		$applicable_status = $this->request->get['applicable_status'] ?? '';
		$this->load->model('setting/setting');
		$this->model_setting_setting->updateApplicableOnMembershipStatus($exception_rule_id, $applicable_status);
		echo 'success';
	}

}
