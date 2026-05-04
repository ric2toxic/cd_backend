<?php
class ControllerPaymentCod extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/cod');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/cod', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('cod', $this->request->post);

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
			'href' => $this->url->link('payment/cod', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/cod', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['cod_total'])) {
			$data['cod_total'] = $this->request->post['cod_total'];
		} else {
			$data['cod_total'] = $this->config->get('cod_total');
		}

		if (isset($this->request->post['cod_order_status_id'])) {
			$data['cod_order_status_id'] = $this->request->post['cod_order_status_id'];
		} else {
			$data['cod_order_status_id'] = $this->config->get('cod_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['cod_geo_zone_id'])) {
			$data['cod_geo_zone_id'] = $this->request->post['cod_geo_zone_id'];
		} else {
			$data['cod_geo_zone_id'] = $this->config->get('cod_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['cod_status'])) {
			$data['cod_status'] = $this->request->post['cod_status'];
		} else {
			$data['cod_status'] = $this->config->get('cod_status');
		}

		if (isset($this->request->post['cod_sort_order'])) {
			$data['cod_sort_order'] = $this->request->post['cod_sort_order'];
		} else {
			$data['cod_sort_order'] = $this->config->get('cod_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/cod.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/cod')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}