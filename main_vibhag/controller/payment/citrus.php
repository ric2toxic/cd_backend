<?php /* Opencart Module v2.0 for Citrus Payment Gateway - Copyrighted file (viatechs.in) - Please do not modify/refactor/disasseble/extract any or all part content  */ ?>
<?php 
	class ControllerPaymentCitrus extends Controller 
	{
		private $error = array();
		
		public function index() 
		{
			//$this->load->language('payment/citrus');
			$data = array(); // Initializing the data array to be passed on to template files
	        // Autoloading the lanugage
	        $this->load->autoLoadLanguage('payment/citrus', $data);
			$this->document->setTitle($data['heading_title']);
			$this->load->model('setting/setting');
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) 
			{
				$this->model_setting_setting->editSetting('citrus', $this->request->post);
				$this->session->data['success'] = $data['text_success'];
				$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
			}

			$data['help_encryption'] = $this->language->get('help_encryption');

			$tdata['breadcrumbs'] = array();   
			$data['breadcrumbs'][] = array('text'=> $data['heading_title'],'href'=> $this->url->link('payment/citrus', 'token=' . $this->session->data['token'], 'SSL'),'separator' => ' :: ');
			$data['action'] = $this->url->link('payment/citrus', 'token=' . $this->session->data['token'], 'SSL');
			$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
			$data['citrus_module'] = '';
			$data['citrus_vanityurl'] = '';
			$data['citrus_access_key'] = '';
			$data['citrus_secret_key'] = '';
			$data['citrus_sort_order'] = '';
			if (isset($this->request->post['citrus_order_status_id'])) 
			{
				$data['citrus_order_status_id'] = $this->request->post['citrus_order_status_id'];
			} 
			else 
			{
				$data['citrus_order_status_id'] = $this->config->get('citrus_order_status_id'); 
			} 
			if (isset($this->request->post['citrus_order_fail_status_id'])) 
			{
				$data['citrus_order_fail_status_id'] = $this->request->post['citrus_order_fail_status_id'];
			} 
			else 
			{
				$data['citrus_order_fail_status_id'] = $this->config->get('citrus_order_fail_status_id'); 
			} 
			$this->load->model('localisation/order_status');
			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
			if (isset($this->request->post['citrus_module'])) 
			{
				$data['citrus_module'] = $this->request->post['citrus_module'];
			} 
			else 
			{
				$data['citrus_module'] = $this->config->get('citrus_module');
			}
			if (isset($this->request->post['citrus_vanityurl'])) 
			{
				$data['citrus_vanityurl'] = $this->request->post['citrus_vanityurl'];
			} 
			else 
			{
				$data['citrus_vanityurl'] = $this->config->get('citrus_vanityurl');
			} 
			if (isset($this->request->post['citrus_access_key'])) 
			{
				$data['citrus_access_key'] = $this->request->post['citrus_access_key'];
			} 
			else 
			{
				$data['citrus_access_key'] = $this->config->get('citrus_access_key');
			}
			if (isset($this->request->post['citrus_secret_key'])) 
			{
				$data['citrus_secret_key'] = $this->request->post['citrus_secret_key'];
			} 
			else 
			{
				$data['citrus_secret_key'] = $this->config->get('citrus_secret_key');
			}
			if (isset($this->request->post['citrus_status'])) 
			{
				$data['citrus_status'] = $this->request->post['citrus_status'];
			} 
			else 
			{
				$data['citrus_status'] = $this->config->get('citrus_status');
			}
			if (isset($this->request->post['citrus_sort_order'])) 
			{
				$data['citrus_sort_order'] = $this->request->post['citrus_sort_order'];
			} 
			else 
			{
				$data['citrus_sort_order'] = $this->config->get('citrus_sort_order');
			}
			
						
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('payment/citrus.tpl', $data));
			
			
			
		}
		
		private function validate() 
		{
			if (!$this->user->hasPermission('modify', 'payment/citrus')) 
			{
				$this->error['warning'] = $this->language->get('error_permission');
			}
			if (empty($this->request->post['citrus_module'])) 
			{
				$this->error['citrus_module'] = $this->language->get('error_module');
			}
			if (empty($this->request->post['citrus_vanityurl'])) 
			{
				$this->error['citrus_vanityurl'] = $this->language->get('error_vanityrul');
			}
			if (empty($this->request->post['citrus_access_key'])) 
			{
				$this->error['citrus_access_key'] = $this->language->get('error_accesskey');
			}
			if (empty($this->request->post['citrus_secret_key'])) 
			{
				$this->error['citrus_secret_key'] = $this->language->get('error_secretkey');
			}
			if (count($this->error) > 0)
			{  
				foreach($this->error as $k=>$v)  
				{        
					$data['error_'.$k] = $v;    
				}
			}
			if (!$this->error) {return true;} else {return false;}
		}
	}
?>