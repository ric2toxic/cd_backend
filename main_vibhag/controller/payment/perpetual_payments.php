<?php
class ControllerPaymentPerpetualPayments extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/perpetual_payments');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/perpetual_payments', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('perpetual_payments', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['auth_id'])) {
			$data['error_auth_id'] = $this->error['auth_id'];
		} else {
			$data['error_auth_id'] = '';
		}

		if (isset($this->error['auth_pass'])) {
			$data['error_auth_pass'] = $this->error['auth_pass'];
		} else {
			$data['error_auth_pass'] = '';
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
			'href' => $this->url->link('payment/perpetual_payments', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/perpetual_payments', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['perpetual_payments_auth_id'])) {
			$data['perpetual_payments_auth_id'] = $this->request->post['perpetual_payments_auth_id'];
		} else {
			$data['perpetual_payments_auth_id'] = $this->config->get('perpetual_payments_auth_id');
		}

		if (isset($this->request->post['perpetual_payments_auth_pass'])) {
			$data['perpetual_payments_auth_pass'] = $this->request->post['perpetual_payments_auth_pass'];
		} else {
			$data['perpetual_payments_auth_pass'] = $this->config->get('perpetual_payments_auth_pass');
		}

		if (isset($this->request->post['perpetual_payments_test'])) {
			$data['perpetual_payments_test'] = $this->request->post['perpetual_payments_test'];
		} else {
			$data['perpetual_payments_test'] = $this->config->get('perpetual_payments_test');
		}

		if (isset($this->request->post['perpetual_payments_total'])) {
			$data['perpetual_payments_total'] = $this->request->post['perpetual_payments_total'];
		} else {
			$data['perpetual_payments_total'] = $this->config->get('perpetual_payments_total');
		}

		if (isset($this->request->post['perpetual_payments_order_status_id'])) {
			$data['perpetual_payments_order_status_id'] = $this->request->post['perpetual_payments_order_status_id'];
		} else {
			$data['perpetual_payments_order_status_id'] = $this->config->get('perpetual_payments_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['perpetual_payments_geo_zone_id'])) {
			$data['perpetual_payments_geo_zone_id'] = $this->request->post['perpetual_payments_geo_zone_id'];
		} else {
			$data['perpetual_payments_geo_zone_id'] = $this->config->get('perpetual_payments_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['perpetual_payments_status'])) {
			$data['perpetual_payments_status'] = $this->request->post['perpetual_payments_status'];
		} else {
			$data['perpetual_payments_status'] = $this->config->get('perpetual_payments_status');
		}

		if (isset($this->request->post['perpetual_payments_sort_order'])) {
			$data['perpetual_payments_sort_order'] = $this->request->post['perpetual_payments_sort_order'];
		} else {
			$data['perpetual_payments_sort_order'] = $this->config->get('perpetual_payments_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/perpetual_payments.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/perpetual_payments')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['perpetual_payments_auth_id']) {
			$this->error['auth_id'] = $this->language->get('error_auth_id');
		}

		if (!$this->request->post['perpetual_payments_auth_pass']) {
			$this->error['auth_pass'] = $this->language->get('error_auth_pass');
		}

		return !$this->error;
	}
}