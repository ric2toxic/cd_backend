<?php
class ControllerPaymentPPPayflow extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/pp_payflow');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/pp_payflow', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pp_payflow', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['vendor'])) {
			$data['error_vendor'] = $this->error['vendor'];
		} else {
			$data['error_vendor'] = '';
		}

		if (isset($this->error['user'])) {
			$data['error_user'] = $this->error['user'];
		} else {
			$data['error_user'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['partner'])) {
			$data['error_partner'] = $this->error['partner'];
		} else {
			$data['error_partner'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['text_pp_express'],
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('payment/pp_payflow', 'token=' . $this->session->data['token'], 'SSL'),
		);

		$data['action'] = $this->url->link('payment/pp_payflow', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['pp_payflow_vendor'])) {
			$data['pp_payflow_vendor'] = $this->request->post['pp_payflow_vendor'];
		} else {
			$data['pp_payflow_vendor'] = $this->config->get('pp_payflow_vendor');
		}

		if (isset($this->request->post['pp_payflow_user'])) {
			$data['pp_payflow_user'] = $this->request->post['pp_payflow_user'];
		} else {
			$data['pp_payflow_user'] = $this->config->get('pp_payflow_user');
		}

		if (isset($this->request->post['pp_payflow_password'])) {
			$data['pp_payflow_password'] = $this->request->post['pp_payflow_password'];
		} else {
			$data['pp_payflow_password'] = $this->config->get('pp_payflow_password');
		}

		if (isset($this->request->post['pp_payflow_partner'])) {
			$data['pp_payflow_partner'] = $this->request->post['pp_payflow_partner'];
		} elseif ($this->config->has('pp_payflow_partner')) {
			$data['pp_payflow_partner'] = $this->config->get('pp_payflow_partner');
		} else {
			$data['pp_payflow_partner'] = 'PayPal';
		}

		if (isset($this->request->post['pp_payflow_test'])) {
			$data['pp_payflow_test'] = $this->request->post['pp_payflow_test'];
		} else {
			$data['pp_payflow_test'] = $this->config->get('pp_payflow_test');
		}

		if (isset($this->request->post['pp_payflow_method'])) {
			$data['pp_payflow_transaction'] = $this->request->post['pp_payflow_transaction'];
		} else {
			$data['pp_payflow_transaction'] = $this->config->get('pp_payflow_transaction');
		}

		if (isset($this->request->post['pp_payflow_total'])) {
			$data['pp_payflow_total'] = $this->request->post['pp_payflow_total'];
		} else {
			$data['pp_payflow_total'] = $this->config->get('pp_payflow_total');
		}

		if (isset($this->request->post['pp_payflow_order_status_id'])) {
			$data['pp_payflow_order_status_id'] = $this->request->post['pp_payflow_order_status_id'];
		} else {
			$data['pp_payflow_order_status_id'] = $this->config->get('pp_payflow_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['pp_payflow_geo_zone_id'])) {
			$data['pp_payflow_geo_zone_id'] = $this->request->post['pp_payflow_geo_zone_id'];
		} else {
			$data['pp_payflow_geo_zone_id'] = $this->config->get('pp_payflow_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['pp_payflow_status'])) {
			$data['pp_payflow_status'] = $this->request->post['pp_payflow_status'];
		} else {
			$data['pp_payflow_status'] = $this->config->get('pp_payflow_status');
		}

		if (isset($this->request->post['pp_payflow_sort_order'])) {
			$data['pp_payflow_sort_order'] = $this->request->post['pp_payflow_sort_order'];
		} else {
			$data['pp_payflow_sort_order'] = $this->config->get('pp_payflow_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/pp_payflow.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/pp_payflow')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['pp_payflow_vendor']) {
			$this->error['vendor'] = $this->language->get('error_vendor');
		}

		if (!$this->request->post['pp_payflow_user']) {
			$this->error['user'] = $this->language->get('error_user');
		}

		if (!$this->request->post['pp_payflow_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['pp_payflow_partner']) {
			$this->error['partner'] = $this->language->get('error_partner');
		}

		return !$this->error;
	}
}