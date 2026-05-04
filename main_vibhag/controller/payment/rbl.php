<?php
class ControllerPaymentRbl extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/credit');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/rbl', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('rbl', $this->request->post);

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
			'href' => $this->url->link('payment/rbl', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/rbl', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['rbl_total'])) {
			$data['rbl_total'] = $this->request->post['rbl_total'];
		} else {
			$data['rbl_total'] = $this->config->get('rbl_total');
		}

        if (isset($this->request->post['rbl_access_key'])) {
            $data['rbl_access_key'] = $this->request->post['rbl_access_key'];
        } else {
            $data['rbl_access_key'] = $this->config->get('rbl_access_key');
        }

        if (isset($this->request->post['rbl_secret_key'])) {
            $data['rbl_secret_key'] = $this->request->post['rbl_secret_key'];
        } else {
            $data['rbl_secret_key'] = $this->config->get('rbl_secret_key');
        }

		if (isset($this->request->post['rbl_order_status_id'])) {
			$data['rbl_order_status_id'] = $this->request->post['rbl_order_status_id'];
		} else {
			$data['rbl_order_status_id'] = $this->config->get('rbl_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['rbl_geo_zone_id'])) {
			$data['rbl_geo_zone_id'] = $this->request->post['rbl_geo_zone_id'];
		} else {
			$data['rbl_geo_zone_id'] = $this->config->get('rbl_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['rbl_status'])) {
			$data['rbl_status'] = $this->request->post['rbl_status'];
		} else {
			$data['rbl_status'] = $this->config->get('rbl_status');
		}

		if (isset($this->request->post['rbl_sort_order'])) {
			$data['rbl_sort_order'] = $this->request->post['rbl_sort_order'];
		} else {
			$data['rbl_sort_order'] = $this->config->get('rbl_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/rbl.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/rbl')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
