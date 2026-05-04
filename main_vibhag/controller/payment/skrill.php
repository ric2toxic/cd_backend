<?php
class ControllerPaymentSkrill extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/skrill');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/skrill', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('skrill', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['entry_mb_id'] = $this->language->get('entry_mb_id');
		$data['entry_custnote'] = $this->language->get('entry_custnote');


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
			'href' => $this->url->link('payment/skrill', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/skrill', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['skrill_email'])) {
			$data['skrill_email'] = $this->request->post['skrill_email'];
		} else {
			$data['skrill_email'] = $this->config->get('skrill_email');
		}

		if (isset($this->request->post['skrill_secret'])) {
			$data['skrill_secret'] = $this->request->post['skrill_secret'];
		} else {
			$data['skrill_secret'] = $this->config->get('skrill_secret');
		}

		if (isset($this->request->post['skrill_total'])) {
			$data['skrill_total'] = $this->request->post['skrill_total'];
		} else {
			$data['skrill_total'] = $this->config->get('skrill_total');
		}

		if (isset($this->request->post['skrill_order_status_id'])) {
			$data['skrill_order_status_id'] = $this->request->post['skrill_order_status_id'];
		} else {
			$data['skrill_order_status_id'] = $this->config->get('skrill_order_status_id');
		}

		if (isset($this->request->post['skrill_pending_status_id'])) {
			$data['skrill_pending_status_id'] = $this->request->post['skrill_pending_status_id'];
		} else {
			$data['skrill_pending_status_id'] = $this->config->get('skrill_pending_status_id');
		}

		if (isset($this->request->post['skrill_canceled_status_id'])) {
			$data['skrill_canceled_status_id'] = $this->request->post['skrill_canceled_status_id'];
		} else {
			$data['skrill_canceled_status_id'] = $this->config->get('skrill_canceled_status_id');
		}

		if (isset($this->request->post['skrill_failed_status_id'])) {
			$data['skrill_failed_status_id'] = $this->request->post['skrill_failed_status_id'];
		} else {
			$data['skrill_failed_status_id'] = $this->config->get('skrill_failed_status_id');
		}

		if (isset($this->request->post['skrill_chargeback_status_id'])) {
			$data['skrill_chargeback_status_id'] = $this->request->post['skrill_chargeback_status_id'];
		} else {
			$data['skrill_chargeback_status_id'] = $this->config->get('skrill_chargeback_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['skrill_geo_zone_id'])) {
			$data['skrill_geo_zone_id'] = $this->request->post['skrill_geo_zone_id'];
		} else {
			$data['skrill_geo_zone_id'] = $this->config->get('skrill_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['skrill_status'])) {
			$data['skrill_status'] = $this->request->post['skrill_status'];
		} else {
			$data['skrill_status'] = $this->config->get('skrill_status');
		}

		if (isset($this->request->post['skrill_sort_order'])) {
			$data['skrill_sort_order'] = $this->request->post['skrill_sort_order'];
		} else {
			$data['skrill_sort_order'] = $this->config->get('skrill_sort_order');
		}

		if (isset($this->request->post['skrill_rid'])) {
			$data['skrill_rid'] = $this->request->post['skrill_rid'];
		} else {
			$data['skrill_rid'] = $this->config->get('skrill_rid');
		}

		if (isset($this->request->post['skrill_custnote'])) {
			$data['skrill_custnote'] = $this->request->post['skrill_custnote'];
		} else {
			$data['skrill_custnote'] = $this->config->get('skrill_custnote');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/skrill.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/skrill')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['skrill_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		return !$this->error;
	}
}