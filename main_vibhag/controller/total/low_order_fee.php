<?php
class ControllerTotalLowOrderFee extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('total/low_order_fee');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('total/low_order_fee', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('low_order_fee', $this->request->post);

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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['text_total'],
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('total/low_order_fee', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('total/low_order_fee', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['low_order_fee_total'])) {
			$data['low_order_fee_total'] = $this->request->post['low_order_fee_total'];
		} else {
			$data['low_order_fee_total'] = $this->config->get('low_order_fee_total');
		}

		if (isset($this->request->post['low_order_fee_fee'])) {
			$data['low_order_fee_fee'] = $this->request->post['low_order_fee_fee'];
		} else {
			$data['low_order_fee_fee'] = $this->config->get('low_order_fee_fee');
		}

		if (isset($this->request->post['low_order_fee_tax_class_id'])) {
			$data['low_order_fee_tax_class_id'] = $this->request->post['low_order_fee_tax_class_id'];
		} else {
			$data['low_order_fee_tax_class_id'] = $this->config->get('low_order_fee_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['low_order_fee_status'])) {
			$data['low_order_fee_status'] = $this->request->post['low_order_fee_status'];
		} else {
			$data['low_order_fee_status'] = $this->config->get('low_order_fee_status');
		}

		if (isset($this->request->post['low_order_fee_sort_order'])) {
			$data['low_order_fee_sort_order'] = $this->request->post['low_order_fee_sort_order'];
		} else {
			$data['low_order_fee_sort_order'] = $this->config->get('low_order_fee_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/low_order_fee.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/low_order_fee')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}