<?php
class ControllerShippingPickup extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('shipping/pickup');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('shipping/pickup', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pickup', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
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
			'text' => $data['text_shipping'],
			'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('shipping/pickup', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('shipping/pickup', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['pickup_geo_zone_id'])) {
			$data['pickup_geo_zone_id'] = $this->request->post['pickup_geo_zone_id'];
		} else {
			$data['pickup_geo_zone_id'] = $this->config->get('pickup_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['pickup_status'])) {
			$data['pickup_status'] = $this->request->post['pickup_status'];
		} else {
			$data['pickup_status'] = $this->config->get('pickup_status');
		}

		if (isset($this->request->post['pickup_sort_order'])) {
			$data['pickup_sort_order'] = $this->request->post['pickup_sort_order'];
		} else {
			$data['pickup_sort_order'] = $this->config->get('pickup_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('shipping/pickup.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/pickup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}