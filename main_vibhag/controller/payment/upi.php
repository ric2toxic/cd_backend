<?php 
	class ControllerPaymentUPI extends Controller
	{
		private $error = array();
		
		public function index() 
		{
			$data = array(); // Initializing the data array to be passed on to template files
	        // Autoloading the lanugage
	        $this->load->autoLoadLanguage('payment/upi', $data);
			$this->document->setTitle($data['heading_title']);
			$this->load->model('setting/setting');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) 
			{
				$this->model_setting_setting->editSetting('upi', $this->request->post);
				$this->session->data['success'] = $data['text_success'];
				$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
			}


			$this->load->model('localisation/order_status');
			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

			$data['help_encryption'] = $this->language->get('help_encryption');

			$tdata['breadcrumbs'] = array();   
			$data['breadcrumbs'][] = array('text'=> $data['heading_title'],'href'=> $this->url->link('payment/upi', 'token=' . $this->session->data['token'], 'SSL'),'separator' => ' :: ');
			$data['action'] = $this->url->link('payment/upi', 'token=' . $this->session->data['token'], 'SSL');
			$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

			$data['upi_sort_order'] = '';
			$data['upi_status'] = '';
			$data['upi_order_fail_status_id'] = '';
			$data['upi_order_status_id'] = '';
			$data['upi_wsb_vpa'] = '';
			$data['upi_web_app_id'] = '';
			$data['upi_app_app_id'] = '';
			$data['upi_vpa_mobile'] = '';
			$data['upi_module'] = '';
			$data['upi_merchant_code'] = '';
			$data['upi_merchant_name'] = '';

			if (isset($this->request->post['upi_sort_order'])){
				$data['upi_sort_order'] = $this->request->post['upi_sort_order'];
			} else{
				$data['upi_sort_order'] = $this->config->get('upi_sort_order');
			}

			if (isset($this->request->post['upi_status'])){
				$data['upi_status'] = $this->request->post['upi_status'];
			} else{
				$data['upi_status'] = $this->config->get('upi_status');
			}

			if (isset($this->request->post['upi_order_fail_status_id'])){
				$data['upi_order_fail_status_id'] = $this->request->post['upi_order_fail_status_id'];
			} else{
				$data['upi_order_fail_status_id'] = $this->config->get('upi_order_fail_status_id');
			}

			if (isset($this->request->post['upi_order_status_id'])){
				$data['upi_order_status_id'] = $this->request->post['upi_order_status_id'];
			} else{
				$data['upi_order_status_id'] = $this->config->get('upi_order_status_id');
			}

			if (isset($this->request->post['upi_wsb_vpa'])){
				$data['upi_wsb_vpa'] = $this->request->post['upi_wsb_vpa'];
			} else{
				$data['upi_wsb_vpa'] = $this->config->get('upi_wsb_vpa');
			}

			if (isset($this->request->post['upi_web_app_id'])){
				$data['upi_web_app_id'] = $this->request->post['upi_web_app_id'];
			} else{
				$data['upi_web_app_id'] = $this->config->get('upi_web_app_id');
			}

			if (isset($this->request->post['upi_app_app_id'])){
				$data['upi_app_app_id'] = $this->request->post['upi_app_app_id'];
			} else{
				$data['upi_app_app_id'] = $this->config->get('upi_app_app_id');
			}

			if (isset($this->request->post['upi_vpa_mobile'])){
				$data['upi_vpa_mobile'] = $this->request->post['upi_vpa_mobile'];
			} else{
				$data['upi_vpa_mobile'] = $this->config->get('upi_vpa_mobile');
			}

			if (isset($this->request->post['upi_module'])){
				$data['upi_module'] = $this->request->post['upi_module'];
			} else{
				$data['upi_module'] = $this->config->get('upi_module');
			}

			if (isset($this->request->post['upi_merchant_code'])){
				$data['upi_merchant_code'] = $this->request->post['upi_merchant_code'];
			} else{
				$data['upi_merchant_code'] = $this->config->get('upi_merchant_code');
			}

			if (isset($this->request->post['upi_merchant_name'])){
				$data['upi_merchant_name'] = $this->request->post['upi_merchant_name'];
			} else{
				$data['upi_merchant_name'] = $this->config->get('upi_merchant_name');
			}

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('payment/upi.tpl', $data));
		}

		private function validate() 
		{
			if (!$this->user->hasPermission('modify', 'payment/upi')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			if (empty($this->request->post['upi_module'])) {
				$this->error['error_module'] = $this->language->get('error_module');
			}
			if (empty($this->request->post['upi_merchant_name'])) {
				$this->error['error_merchant_name'] = $this->language->get('error_merchant_name');
			}
			if (empty($this->request->post['upi_merchant_code'])) {
				$this->error['error_merchant_code'] = $this->language->get('error_merchant_code');
			}
			if (empty($this->request->post['upi_wsb_vpa'])) {
				$this->error['error_wsb_vpa'] = $this->language->get('error_wsb_vpa');
			}
			if (empty($this->request->post['upi_web_app_id'])) {
				$this->error['error_web_app_id'] = $this->language->get('error_web_app_id');
			}
			if (empty($this->request->post['upi_app_app_id'])) {
				$this->error['error_app_app_id'] = $this->language->get('error_app_app_id');
			}
			if (empty($this->request->post['upi_vpa_mobile'])) {
				$this->error['error_vpa_mobile'] = $this->language->get('error_vpa_mobile');
			}
			
			if (count($this->error) > 0){  
				foreach($this->error as $k=>$v)  
				{        
					$data['error_'.$k] = $v;    
				}
			}
			if (!$this->error) {return true;} else {return false;}
		}
	}
?>