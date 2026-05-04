<?php
class ControllerPaymentNOCHEX extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/nochex');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/nochex', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('nochex', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['merchant'])) {
			$data['error_merchant'] = $this->error['merchant'];
		} else {
			$data['error_merchant'] = '';
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
			'href' => $this->url->link('payment/nochex', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/nochex', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['nochex_email'])) {
			$data['nochex_email'] = $this->request->post['nochex_email'];
		} else {
			$data['nochex_email'] = $this->config->get('nochex_email');
		}

		if (isset($this->request->post['nochex_account'])) {
			$data['nochex_account'] = $this->request->post['nochex_account'];
		} else {
			$data['nochex_account'] = $this->config->get('nochex_account');
		}

		if (isset($this->request->post['nochex_merchant'])) {
			$data['nochex_merchant'] = $this->request->post['nochex_merchant'];
		} else {
			$data['nochex_merchant'] = $this->config->get('nochex_merchant');
		}

		if (isset($this->request->post['nochex_template'])) {
			$data['nochex_template'] = $this->request->post['nochex_template'];
		} else {
			$data['nochex_template'] = $this->config->get('nochex_template');
		}

		if (isset($this->request->post['nochex_test'])) {
			$data['nochex_test'] = $this->request->post['nochex_test'];
		} else {
			$data['nochex_test'] = $this->config->get('nochex_test');
		}

		if (isset($this->request->post['nochex_total'])) {
			$data['nochex_total'] = $this->request->post['nochex_total'];
		} else {
			$data['nochex_total'] = $this->config->get('nochex_total');
		}

		if (isset($this->request->post['nochex_order_status_id'])) {
			$data['nochex_order_status_id'] = $this->request->post['nochex_order_status_id'];
		} else {
			$data['nochex_order_status_id'] = $this->config->get('nochex_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['nochex_geo_zone_id'])) {
			$data['nochex_geo_zone_id'] = $this->request->post['nochex_geo_zone_id'];
		} else {
			$data['nochex_geo_zone_id'] = $this->config->get('nochex_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['nochex_status'])) {
			$data['nochex_status'] = $this->request->post['nochex_status'];
		} else {
			$data['nochex_status'] = $this->config->get('nochex_status');
		}

		if (isset($this->request->post['nochex_sort_order'])) {
			$data['nochex_sort_order'] = $this->request->post['nochex_sort_order'];
		} else {
			$data['nochex_sort_order'] = $this->config->get('nochex_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/nochex.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/nochex')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['nochex_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!$this->request->post['nochex_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		return !$this->error;
	}
}