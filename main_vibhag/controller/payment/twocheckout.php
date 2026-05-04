<?php
class ControllerPaymentTwoCheckout extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/twocheckout');
		 $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/twocheckout', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('twocheckout', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['account'])) {
			$data['error_account'] = $this->error['account'];
		} else {
			$data['error_account'] = '';
		}

		if (isset($this->error['secret'])) {
			$data['error_secret'] = $this->error['secret'];
		} else {
			$data['error_secret'] = '';
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
			'href' => $this->url->link('payment/twocheckout', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/twocheckout', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['twocheckout_account'])) {
			$data['twocheckout_account'] = $this->request->post['twocheckout_account'];
		} else {
			$data['twocheckout_account'] = $this->config->get('twocheckout_account');
		}

		if (isset($this->request->post['twocheckout_secret'])) {
			$data['twocheckout_secret'] = $this->request->post['twocheckout_secret'];
		} else {
			$data['twocheckout_secret'] = $this->config->get('twocheckout_secret');
		}

		if (isset($this->request->post['twocheckout_display'])) {
			$data['twocheckout_display'] = $this->request->post['twocheckout_display'];
		} else {
			$data['twocheckout_display'] = $this->config->get('twocheckout_display');
		}

		if (isset($this->request->post['twocheckout_test'])) {
			$data['twocheckout_test'] = $this->request->post['twocheckout_test'];
		} else {
			$data['twocheckout_test'] = $this->config->get('twocheckout_test');
		}

		if (isset($this->request->post['twocheckout_total'])) {
			$data['twocheckout_total'] = $this->request->post['twocheckout_total'];
		} else {
			$data['twocheckout_total'] = $this->config->get('twocheckout_total');
		}

		if (isset($this->request->post['twocheckout_order_status_id'])) {
			$data['twocheckout_order_status_id'] = $this->request->post['twocheckout_order_status_id'];
		} else {
			$data['twocheckout_order_status_id'] = $this->config->get('twocheckout_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['twocheckout_geo_zone_id'])) {
			$data['twocheckout_geo_zone_id'] = $this->request->post['twocheckout_geo_zone_id'];
		} else {
			$data['twocheckout_geo_zone_id'] = $this->config->get('twocheckout_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['twocheckout_status'])) {
			$data['twocheckout_status'] = $this->request->post['twocheckout_status'];
		} else {
			$data['twocheckout_status'] = $this->config->get('twocheckout_status');
		}

		if (isset($this->request->post['twocheckout_sort_order'])) {
			$data['twocheckout_sort_order'] = $this->request->post['twocheckout_sort_order'];
		} else {
			$data['twocheckout_sort_order'] = $this->config->get('twocheckout_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/twocheckout.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/twocheckout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['twocheckout_account']) {
			$this->error['account'] = $this->language->get('error_account');
		}

		if (!$this->request->post['twocheckout_secret']) {
			$this->error['secret'] = $this->language->get('error_secret');
		}

		return !$this->error;
	}
}