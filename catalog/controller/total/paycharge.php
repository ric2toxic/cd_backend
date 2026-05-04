<?php
class ControllerTotalPaycharge extends Controller {
	private $error = array();

	public function index() {
		$fs_mod = 'PayCharge Free v5.0';
		$data['fs_version'] = '<a href="http://www.opencart.com/index.php?route=extension/extension&filter_username=fabiom7">' . $fs_mod . '</a><br />Powered by <a href="http://www.fabiom7.com">fabiom7</a>';

		$this->load->language('total/paycharge');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paycharge', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['column_rules'] = $this->language->get('column_rules');
		$data['column_charge'] = $this->language->get('column_charge');
		$data['column_description'] = $this->language->get('column_description');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_percentage'] = $this->language->get('entry_percentage');

		$data['help_sort_order'] = $this->language->get('help_sort_order');
		$data['help_charge'] = $this->language->get('help_charge');

		$data['button_paycharge_add'] = $this->language->get('button_paycharge_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

   		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),      		
   		);

   		$data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_total'),
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'),
   		);

   		$data['breadcrumbs'][] = array(
       		'text' => $this->language->get('heading_title'),
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
					'name' => $this->language->get('heading_title'),
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
		} elseif ($this->config->get('paycharge')) { 
			$paycharges = $this->config->get('paycharge');
		} else {
			$paycharges = array('0' => array('payment_method' => 0, 'valuep' => ''));
			
		}
		$data['paycharge'] = $paycharges[0];
		//echo "<pre>"; print_r($data['paycharge']); die;
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
}