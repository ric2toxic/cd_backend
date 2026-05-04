<?php
class ControllerPaymentLiqPay extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/liqpay');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/liqpay', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('liqpay', $this->request->post);

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

		if (isset($this->error['signature'])) {
			$data['error_signature'] = $this->error['signature'];
		} else {
			$data['error_signature'] = '';
		}

		if (isset($this->error['type'])) {
			$data['error_type'] = $this->error['type'];
		} else {
			$data['error_type'] = '';
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
			'href' => $this->url->link('payment/liqpay', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/liqpay', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['liqpay_merchant'])) {
			$data['liqpay_merchant'] = $this->request->post['liqpay_merchant'];
		} else {
			$data['liqpay_merchant'] = $this->config->get('liqpay_merchant');
		}

		if (isset($this->request->post['liqpay_signature'])) {
			$data['liqpay_signature'] = $this->request->post['liqpay_signature'];
		} else {
			$data['liqpay_signature'] = $this->config->get('liqpay_signature');
		}

		if (isset($this->request->post['liqpay_type'])) {
			$data['liqpay_type'] = $this->request->post['liqpay_type'];
		} else {
			$data['liqpay_type'] = $this->config->get('liqpay_type');
		}

		if (isset($this->request->post['liqpay_total'])) {
			$data['liqpay_total'] = $this->request->post['liqpay_total'];
		} else {
			$data['liqpay_total'] = $this->config->get('liqpay_total');
		}

		if (isset($this->request->post['liqpay_order_status_id'])) {
			$data['liqpay_order_status_id'] = $this->request->post['liqpay_order_status_id'];
		} else {
			$data['liqpay_order_status_id'] = $this->config->get('liqpay_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['liqpay_geo_zone_id'])) {
			$data['liqpay_geo_zone_id'] = $this->request->post['liqpay_geo_zone_id'];
		} else {
			$data['liqpay_geo_zone_id'] = $this->config->get('liqpay_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['liqpay_status'])) {
			$data['liqpay_status'] = $this->request->post['liqpay_status'];
		} else {
			$data['liqpay_status'] = $this->config->get('liqpay_status');
		}

		if (isset($this->request->post['liqpay_sort_order'])) {
			$data['liqpay_sort_order'] = $this->request->post['liqpay_sort_order'];
		} else {
			$data['liqpay_sort_order'] = $this->config->get('liqpay_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/liqpay.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/liqpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['liqpay_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['liqpay_signature']) {
			$this->error['signature'] = $this->language->get('error_signature');
		}

		return !$this->error;
	}
}