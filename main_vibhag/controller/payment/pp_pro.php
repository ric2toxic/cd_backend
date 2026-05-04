<?php
class ControllerPaymentPPPro extends Controller {
	private $error = array();

	public function index() {
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/pp_pro', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pp_pro', $this->request->post);

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

		if (isset($this->error['signature'])) {
			$data['error_signature'] = $this->error['signature'];
		} else {
			$data['error_signature'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('payment/pp_pro', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/pp_pro', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['pp_pro_username'])) {
			$data['pp_pro_username'] = $this->request->post['pp_pro_username'];
		} else {
			$data['pp_pro_username'] = $this->config->get('pp_pro_username');
		}

		if (isset($this->request->post['pp_pro_password'])) {
			$data['pp_pro_password'] = $this->request->post['pp_pro_password'];
		} else {
			$data['pp_pro_password'] = $this->config->get('pp_pro_password');
		}

		if (isset($this->request->post['pp_pro_signature'])) {
			$data['pp_pro_signature'] = $this->request->post['pp_pro_signature'];
		} else {
			$data['pp_pro_signature'] = $this->config->get('pp_pro_signature');
		}

		if (isset($this->request->post['pp_pro_test'])) {
			$data['pp_pro_test'] = $this->request->post['pp_pro_test'];
		} else {
			$data['pp_pro_test'] = $this->config->get('pp_pro_test');
		}

		if (isset($this->request->post['pp_pro_method'])) {
			$data['pp_pro_transaction'] = $this->request->post['pp_pro_transaction'];
		} else {
			$data['pp_pro_transaction'] = $this->config->get('pp_pro_transaction');
		}

		if (isset($this->request->post['pp_pro_total'])) {
			$data['pp_pro_total'] = $this->request->post['pp_pro_total'];
		} else {
			$data['pp_pro_total'] = $this->config->get('pp_pro_total');
		}

		if (isset($this->request->post['pp_pro_order_status_id'])) {
			$data['pp_pro_order_status_id'] = $this->request->post['pp_pro_order_status_id'];
		} else {
			$data['pp_pro_order_status_id'] = $this->config->get('pp_pro_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['pp_pro_geo_zone_id'])) {
			$data['pp_pro_geo_zone_id'] = $this->request->post['pp_pro_geo_zone_id'];
		} else {
			$data['pp_pro_geo_zone_id'] = $this->config->get('pp_pro_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['pp_pro_status'])) {
			$data['pp_pro_status'] = $this->request->post['pp_pro_status'];
		} else {
			$data['pp_pro_status'] = $this->config->get('pp_pro_status');
		}

		if (isset($this->request->post['pp_pro_sort_order'])) {
			$data['pp_pro_sort_order'] = $this->request->post['pp_pro_sort_order'];
		} else {
			$data['pp_pro_sort_order'] = $this->config->get('pp_pro_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/pp_pro.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/pp_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['pp_pro_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['pp_pro_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['pp_pro_signature']) {
			$this->error['signature'] = $this->language->get('error_signature');
		}

		return !$this->error;
	}
}