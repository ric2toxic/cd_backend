<?php
class ControllerShippingWeight extends Controller {
	private $error = array();

	public function index() { 
		//$this->load->language('shipping/weight');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('shipping/weight', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('weight', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			if (isset($this->request->get['search']) && $this->request->get['search'] != '') {
				$this->response->redirect($this->url->link('shipping/weight', 'token=' . $this->session->data['token'].'&search='.$this->request->get['search'], 'SSL'));
			} else {
				$this->response->redirect($this->url->link('shipping/weight', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}
		$data['token'] = $this->session->data['token'];

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
			'href' => $this->url->link('shipping/weight', 'token=' . $this->session->data['token'], 'SSL')
		);


		$data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('localisation/geo_zone');

		if (isset($this->request->get['search']) && $this->request->get['search'] != '') {
			$data['search'] = $this->request->get['search'];
			$geo_zones = $this->model_localisation_geo_zone->getGeoZonesNew($data);
		} else {
			$data['search'] = '';
			$geo_zones = $this->model_localisation_geo_zone->getGeoZones($data);
		}

		$data['action'] = $this->url->link('shipping/weight', 'token=' . $this->session->data['token'], 'SSL');

		foreach ($geo_zones as $geo_zone) {
			if (isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_rate']) && isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_check'])) {
				$data['weight_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_rate'];
				$data['weight_' . $geo_zone['geo_zone_id'] . '_check'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_check'];
				$data['weight_' . $geo_zone['geo_zone_id'] . '_cost_after_string_range'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_cost_after_string_range'];
			} else {
				$data['weight_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_rate');
				$data['weight_' . $geo_zone['geo_zone_id'] . '_check'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_check');
				$data['weight_' . $geo_zone['geo_zone_id'] . '_cost_after_string_range'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_cost_after_string_range');
				
			}
			if (isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_meta'])) {
				$data['weight_' . $geo_zone['geo_zone_id'] . '_meta'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_meta'];
			} else {
				$data['weight_' . $geo_zone['geo_zone_id'] . '_meta'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_meta');
				//echo "<pre>"; print_r($data['weight_' . $geo_zone['geo_zone_id'] . '_meta'] );
			}

			if (isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_status'])) {
				$data['weight_' . $geo_zone['geo_zone_id'] . '_status'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_status'];
			} else {
				$data['weight_' . $geo_zone['geo_zone_id'] . '_status'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_status');
			}
		}

		$data['geo_zones'] = $geo_zones;

		if (isset($this->request->post['weight_tax_class_id'])) {
			$data['weight_tax_class_id'] = $this->request->post['weight_tax_class_id'];
		} else {
			$data['weight_tax_class_id'] = $this->config->get('weight_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['weight_status'])) {
			$data['weight_status'] = $this->request->post['weight_status'];
		} else {
			$data['weight_status'] = $this->config->get('weight_status');
		}

		if (isset($this->request->post['weight_sort_order'])) {
			$data['weight_sort_order'] = $this->request->post['weight_sort_order'];
		} else {
			$data['weight_sort_order'] = $this->config->get('weight_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('shipping/weight.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/weight')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
