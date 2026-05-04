<?php
class ControllerPaymentPayza extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/payza');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/payza', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payza', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
		}

		if (isset($this->error['security'])) {
			$data['error_security'] = $this->error['security'];
		} else {
			$data['error_security'] = '';
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
			'href' => $this->url->link('payment/payza', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/payza', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['payza_merchant'])) {
			$data['payza_merchant'] = $this->request->post['payza_merchant'];
		} else {
			$data['payza_merchant'] = $this->config->get('payza_merchant');
		}

		if (isset($this->request->post['payza_security'])) {
			$data['payza_security'] = $this->request->post['payza_security'];
		} else {
			$data['payza_security'] = $this->config->get('payza_security');
		}

		$data['callback'] = HTTP_CATALOG . 'index.php?route=payment/payza/callback';

		if (isset($this->request->post['payza_total'])) {
			$data['payza_total'] = $this->request->post['payza_total'];
		} else {
			$data['payza_total'] = $this->config->get('payza_total');
		}

		if (isset($this->request->post['payza_order_status_id'])) {
			$data['payza_order_status_id'] = $this->request->post['payza_order_status_id'];
		} else {
			$data['payza_order_status_id'] = $this->config->get('payza_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payza_geo_zone_id'])) {
			$data['payza_geo_zone_id'] = $this->request->post['payza_geo_zone_id'];
		} else {
			$data['payza_geo_zone_id'] = $this->config->get('payza_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payza_status'])) {
			$data['payza_status'] = $this->request->post['payza_status'];
		} else {
			$data['payza_status'] = $this->config->get('payza_status');
		}

		if (isset($this->request->post['payza_sort_order'])) {
			$data['payza_sort_order'] = $this->request->post['payza_sort_order'];
		} else {
			$data['payza_sort_order'] = $this->config->get('payza_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/payza.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/payza')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payza_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['payza_security']) {
			$this->error['security'] = $this->language->get('error_security');
		}

		return !$this->error;
	}
}