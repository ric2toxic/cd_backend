<?php
class ControllerPaymentPayMate extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/paymate');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/paymate', $data);
        //echo "<pre>";print_r($data);die;
		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paymate', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['text_payment'],
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('payment/paymate', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/paymate', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['paymate_username'])) {
			$data['paymate_username'] = $this->request->post['paymate_username'];
		} else {
			$data['paymate_username'] = $this->config->get('paymate_username');
		}

		if (isset($this->request->post['paymate_password'])) {
			$data['paymate_password'] = $this->request->post['paymate_password'];
		} elseif ($this->config->get('paymate_password')) {
			$data['paymate_password'] = $this->config->get('paymate_password');
		} else {
			$data['paymate_password'] = md5(mt_rand());
		}

		if (isset($this->request->post['paymate_test'])) {
			$data['paymate_test'] = $this->request->post['paymate_test'];
		} else {
			$data['paymate_test'] = $this->config->get('paymate_test');
		}

		if (isset($this->request->post['paymate_total'])) {
			$data['paymate_total'] = $this->request->post['paymate_total'];
		} else {
			$data['paymate_total'] = $this->config->get('paymate_total');
		}

		if (isset($this->request->post['paymate_order_status_id'])) {
			$data['paymate_order_status_id'] = $this->request->post['paymate_order_status_id'];
		} else {
			$data['paymate_order_status_id'] = $this->config->get('paymate_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['paymate_geo_zone_id'])) {
			$data['paymate_geo_zone_id'] = $this->request->post['paymate_geo_zone_id'];
		} else {
			$data['paymate_geo_zone_id'] = $this->config->get('paymate_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['paymate_status'])) {
			$data['paymate_status'] = $this->request->post['paymate_status'];
		} else {
			$data['paymate_status'] = $this->config->get('paymate_status');
		}

		if (isset($this->request->post['paymate_sort_order'])) {
			$data['paymate_sort_order'] = $this->request->post['paymate_sort_order'];
		} else {
			$data['paymate_sort_order'] = $this->config->get('paymate_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/paymate.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/paymate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['paymate_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['paymate_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		return !$this->error;
	}
}
