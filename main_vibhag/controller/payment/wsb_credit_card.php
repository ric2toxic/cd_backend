<?php /* Opencart Module v2.0 for Citrus Payment Gateway - Copyrighted file (viatechs.in) - Please do not modify/refactor/disasseble/extract any or all part content  */ ?>
<?php 
	class ControllerPaymentWsbCreditCard extends Controller
	{
		private $error = array();
		
		public function index() 
		{
			
			$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/wsb_credit_card', $data);
			$this->document->setTitle($data['heading_title']);
			$this->load->model('setting/setting');
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate($data)) 
			{
				$this->model_setting_setting->editSetting('wsb_credit_card', $this->request->post);
				$this->session->data['success'] = $data['text_success'];
				$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
			}

			$data['help_encryption'] = $this->language->get('help_encryption');

			$tdata['breadcrumbs'] = array();   
			$data['breadcrumbs'][] = array('text'=> $data['heading_title'],'href'=> $this->url->link('payment/wsb_credit_card', 'token=' . $this->session->data['token'], 'SSL'),'separator' => ' :: ');
			$data['action'] = $this->url->link('payment/wsb_credit_card', 'token=' . $this->session->data['token'], 'SSL');
			$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
			$data['wsb_credit_card_sort_order'] = '';
			if (isset($this->request->post['wsb_credit_card_order_status_id']))
			{
				$data['wsb_credit_card_order_status_id'] = $this->request->post['wsb_credit_card_order_status_id'];
			} 
			else 
			{
				$data['wsb_credit_card_order_status_id'] = $this->config->get('wsb_credit_card_order_status_id');
			} 
			if (isset($this->request->post['wsb_credit_card_order_fail_status_id']))
			{
				$data['wsb_credit_card_order_fail_status_id'] = $this->request->post['wsb_credit_card_order_fail_status_id'];
			} 
			else 
			{
				$data['wsb_credit_card_order_fail_status_id'] = $this->config->get('wsb_credit_card_order_fail_status_id');
			} 
			$this->load->model('localisation/order_status');
			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
			
			if (isset($this->request->post['wsb_credit_card_module'])) 
			{
				$data['wsb_credit_card_module'] = $this->request->post['wsb_credit_card_module'];
			} 
			else 
			{
				$data['wsb_credit_card_module'] = $this->config->get('wsb_credit_card_module');
			}
			if (isset($this->request->post['wsb_credit_card_vanityurl'])) 
			{
				$data['wsb_credit_card_vanityurl'] = $this->request->post['wsb_credit_card_vanityurl'];
			} 
			else 
			{
				$data['wsb_credit_card_vanityurl'] = $this->config->get('wsb_credit_card_vanityurl');
			} 
			if (isset($this->request->post['wsb_credit_card_access_key'])) 
			{
				$data['wsb_credit_card_access_key'] = $this->request->post['wsb_credit_card_access_key'];
			} 
			else 
			{
				$data['wsb_credit_card_access_key'] = $this->config->get('wsb_credit_card_access_key');
			}
			if (isset($this->request->post['wsb_credit_card_secret_key'])) 
			{
				$data['wsb_credit_card_secret_key'] = $this->request->post['wsb_credit_card_secret_key'];
			} 
			else 
			{
				$data['wsb_credit_card_secret_key'] = $this->config->get('wsb_credit_card_secret_key');
			}

			if (isset($this->request->post['wsb_credit_card_status']))
			{
				$data['wsb_credit_card_status'] = $this->request->post['wsb_credit_card_status'];
			} 
			else 
			{
				$data['wsb_credit_card_status'] = $this->config->get('wsb_credit_card_status');
			}
			if (isset($this->request->post['wsb_credit_card_sort_order']))
			{
				$data['wsb_credit_card_sort_order'] = $this->request->post['wsb_credit_card_sort_order'];
			} 
			else 
			{
				$data['wsb_credit_card_sort_order'] = $this->config->get('wsb_credit_card_sort_order');
			}
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('payment/wsb_credit_card.tpl', $data));
			
			
			
		}
		
		private function validate(&$data) 
		{
			if (!$this->user->hasPermission('modify', 'payment/wsb_credit_card'))
			{
				$this->error['warning'] = $this->language->get('error_permission');
			}
			
			if (empty($this->request->post['wsb_credit_card_module'])) 
			{
				$this->error['wsb_credit_card_module'] = $this->language->get('error_module');
			}
			if (empty($this->request->post['wsb_credit_card_vanityurl'])) 
			{
				$this->error['wsb_credit_card_vanityurl'] = $this->language->get('error_vanityrul');
			}
			if (empty($this->request->post['wsb_credit_card_access_key'])) 
			{
				$this->error['wsb_credit_card_accesskey'] = $this->language->get('error_accesskey');
			}
			if (empty($this->request->post['wsb_credit_card_secret_key'])) 
			{
				$this->error['wsb_credit_card_secretkey'] = $this->language->get('error_secretkey');
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