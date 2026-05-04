<?php
class ControllerPaymentMswipeCredit extends Controller {
	private $error = array();

	public function index() {
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/mswipe_credit', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('mswipe_credit', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'href' => $this->url->link('payment/mswipe_credit', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/mswipe_credit', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['mswipe_credit_total'])) {
			$data['mswipe_credit_total'] = $this->request->post['mswipe_credit_total'];
		} else {
			$data['mswipe_credit_total'] = $this->config->get('mswipe_credit_total');
		}

		if (isset($this->request->post['mswipe_credit_order_status_id'])) {
			$data['mswipe_credit_order_status_id'] = $this->request->post['mswipe_credit_order_status_id'];
		} else {
			$data['mswipe_credit_order_status_id'] = $this->config->get('mswipe_credit_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['mswipe_credit_status'])) {
			$data['mswipe_credit_status'] = $this->request->post['mswipe_credit_status'];
		} else {
			$data['mswipe_credit_status'] = $this->config->get('mswipe_credit_status');
		}

		if (isset($this->request->post['mswipe_credit_sort_order'])) {
			$data['mswipe_credit_sort_order'] = $this->request->post['mswipe_credit_sort_order'];
		} else {
			$data['mswipe_credit_sort_order'] = $this->config->get('mswipe_credit_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/mswipe_credit.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/mswipe_credit')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}