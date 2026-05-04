<?php
class ControllerPaymentAuthorizenetAim extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/authorizenet_aim');
         $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/authorizenet_aim', $data);
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('authorizenet_aim', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['login'])) {
			$data['error_login'] = $this->error['login'];
		} else {
			$data['error_login'] = '';
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
			'href' => $this->url->link('payment/authorizenet_aim', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/authorizenet_aim', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['authorizenet_aim_login'])) {
			$data['authorizenet_aim_login'] = $this->request->post['authorizenet_aim_login'];
		} else {
			$data['authorizenet_aim_login'] = $this->config->get('authorizenet_aim_login');
		}

		if (isset($this->request->post['authorizenet_aim_key'])) {
			$data['authorizenet_aim_key'] = $this->request->post['authorizenet_aim_key'];
		} else {
			$data['authorizenet_aim_key'] = $this->config->get('authorizenet_aim_key');
		}

		if (isset($this->request->post['authorizenet_aim_hash'])) {
			$data['authorizenet_aim_hash'] = $this->request->post['authorizenet_aim_hash'];
		} else {
			$data['authorizenet_aim_hash'] = $this->config->get('authorizenet_aim_hash');
		}

		if (isset($this->request->post['authorizenet_aim_server'])) {
			$data['authorizenet_aim_server'] = $this->request->post['authorizenet_aim_server'];
		} else {
			$data['authorizenet_aim_server'] = $this->config->get('authorizenet_aim_server');
		}

		if (isset($this->request->post['authorizenet_aim_mode'])) {
			$data['authorizenet_aim_mode'] = $this->request->post['authorizenet_aim_mode'];
		} else {
			$data['authorizenet_aim_mode'] = $this->config->get('authorizenet_aim_mode');
		}

		if (isset($this->request->post['authorizenet_aim_method'])) {
			$data['authorizenet_aim_method'] = $this->request->post['authorizenet_aim_method'];
		} else {
			$data['authorizenet_aim_method'] = $this->config->get('authorizenet_aim_method');
		}

		if (isset($this->request->post['authorizenet_aim_total'])) {
			$data['authorizenet_aim_total'] = $this->request->post['authorizenet_aim_total'];
		} else {
			$data['authorizenet_aim_total'] = $this->config->get('authorizenet_aim_total');
		}

		if (isset($this->request->post['authorizenet_aim_order_status_id'])) {
			$data['authorizenet_aim_order_status_id'] = $this->request->post['authorizenet_aim_order_status_id'];
		} else {
			$data['authorizenet_aim_order_status_id'] = $this->config->get('authorizenet_aim_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['authorizenet_aim_geo_zone_id'])) {
			$data['authorizenet_aim_geo_zone_id'] = $this->request->post['authorizenet_aim_geo_zone_id'];
		} else {
			$data['authorizenet_aim_geo_zone_id'] = $this->config->get('authorizenet_aim_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['authorizenet_aim_status'])) {
			$data['authorizenet_aim_status'] = $this->request->post['authorizenet_aim_status'];
		} else {
			$data['authorizenet_aim_status'] = $this->config->get('authorizenet_aim_status');
		}

		if (isset($this->request->post['authorizenet_aim_sort_order'])) {
			$data['authorizenet_aim_sort_order'] = $this->request->post['authorizenet_aim_sort_order'];
		} else {
			$data['authorizenet_aim_sort_order'] = $this->config->get('authorizenet_aim_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/authorizenet_aim.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/authorizenet_aim')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['authorizenet_aim_login']) {
			$this->error['login'] = $this->language->get('error_login');
		}

		if (!$this->request->post['authorizenet_aim_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}

		return !$this->error;
	}
}