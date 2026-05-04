<?php
class ControllerShippingItem extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('shipping/item');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('shipping/item', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('item', $this->request->post);

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
			'href' => $this->url->link('shipping/item', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('shipping/item', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['item_cost'])) {
			$data['item_cost'] = $this->request->post['item_cost'];
		} else {
			$data['item_cost'] = $this->config->get('item_cost');
		}

		if (isset($this->request->post['item_tax_class_id'])) {
			$data['item_tax_class_id'] = $this->request->post['item_tax_class_id'];
		} else {
			$data['item_tax_class_id'] = $this->config->get('item_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['item_geo_zone_id'])) {
			$data['item_geo_zone_id'] = $this->request->post['item_geo_zone_id'];
		} else {
			$data['item_geo_zone_id'] = $this->config->get('item_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['item_status'])) {
			$data['item_status'] = $this->request->post['item_status'];
		} else {
			$data['item_status'] = $this->config->get('item_status');
		}

		if (isset($this->request->post['item_sort_order'])) {
			$data['item_sort_order'] = $this->request->post['item_sort_order'];
		} else {
			$data['item_sort_order'] = $this->config->get('item_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('shipping/item.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/item')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}