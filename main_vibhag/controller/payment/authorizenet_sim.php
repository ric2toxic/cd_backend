<?php
class ControllerPaymentAuthorizeNetSim extends Controller {
	private $error = array();

	public function index() {

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/authorizenet_sim', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('authorizenet_sim', $this->request->post);

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

		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
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
			'href' => $this->url->link('payment/authorizenet_sim', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/authorizenet_sim', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['authorizenet_sim_merchant'])) {
			$data['authorizenet_sim_merchant'] = $this->request->post['authorizenet_sim_merchant'];
		} else {
			$data['authorizenet_sim_merchant'] = $this->config->get('authorizenet_sim_merchant');
		}

		if (isset($this->request->post['authorizenet_sim_key'])) {
			$data['authorizenet_sim_key'] = $this->request->post['authorizenet_sim_key'];
		} else {
			$data['authorizenet_sim_key'] = $this->config->get('authorizenet_sim_key');
		}

		if (isset($this->request->post['authorizenet_sim_test'])) {
			$data['authorizenet_sim_test'] = $this->request->post['authorizenet_sim_test'];
		} else {
			$data['authorizenet_sim_test'] = $this->config->get('authorizenet_sim_test');
		}

		$data['callback'] = HTTP_CATALOG . 'index.php?route=payment/authorizenet_sim/callback';

		if (isset($this->request->post['authorizenet_sim_md5'])) {
			$data['authorizenet_sim_md5'] = $this->request->post['authorizenet_sim_md5'];
		} else {
			$data['authorizenet_sim_md5'] = $this->config->get('authorizenet_sim_md5');
		}

		if (isset($this->request->post['authorizenet_sim_total'])) {
			$data['authorizenet_sim_total'] = $this->request->post['authorizenet_sim_total'];
		} else {
			$data['authorizenet_sim_total'] = $this->config->get('authorizenet_sim_total');
		}

		if (isset($this->request->post['authorizenet_sim_order_status_id'])) {
			$data['authorizenet_sim_order_status_id'] = $this->request->post['authorizenet_sim_order_status_id'];
		} else {
			$data['authorizenet_sim_order_status_id'] = $this->config->get('authorizenet_sim_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['authorizenet_sim_geo_zone_id'])) {
			$data['authorizenet_sim_geo_zone_id'] = $this->request->post['authorizenet_sim_geo_zone_id'];
		} else {
			$data['authorizenet_sim_geo_zone_id'] = $this->config->get('authorizenet_sim_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['authorizenet_sim_status'])) {
			$data['authorizenet_sim_status'] = $this->request->post['authorizenet_sim_status'];
		} else {
			$data['authorizenet_sim_status'] = $this->config->get('authorizenet_sim_status');
		}

		if (isset($this->request->post['authorizenet_sim_sort_order'])) {
			$data['authorizenet_sim_sort_order'] = $this->request->post['authorizenet_sim_sort_order'];
		} else {
			$data['authorizenet_sim_sort_order'] = $this->config->get('authorizenet_sim_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/authorizenet_sim.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/authorizenet_sim')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['authorizenet_sim_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['authorizenet_sim_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}

		return !$this->error;
	}
}