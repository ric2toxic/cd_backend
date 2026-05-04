<?php
class ControllerPaymentCredit extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/credit');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/credit', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('credit', $this->request->post);

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
			'href' => $this->url->link('payment/credit', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/credit', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['credit_total'])) {
			$data['credit_total'] = $this->request->post['credit_total'];
		} else {
			$data['credit_total'] = $this->config->get('credit_total');
		}

        if (isset($this->request->post['credit_neo_partner_id'])) {
            $data['credit_neo_partner_id'] = $this->request->post['credit_neo_partner_id'];
        } else {
            $data['credit_neo_partner_id'] = $this->config->get('credit_neo_partner_id');
        }

        if (isset($this->request->post['credit_neo_partner_key'])) {
            $data['credit_neo_partner_key'] = $this->request->post['credit_neo_partner_key'];
        } else {
            $data['credit_neo_partner_key'] = $this->config->get('credit_neo_partner_key');
        }

		if (isset($this->request->post['credit_order_status_id'])) {
			$data['credit_order_status_id'] = $this->request->post['credit_order_status_id'];
		} else {
			$data['credit_order_status_id'] = $this->config->get('credit_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['credit_geo_zone_id'])) {
			$data['credit_geo_zone_id'] = $this->request->post['credit_geo_zone_id'];
		} else {
			$data['credit_geo_zone_id'] = $this->config->get('credit_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['credit_status'])) {
			$data['credit_status'] = $this->request->post['credit_status'];
		} else {
			$data['credit_status'] = $this->config->get('credit_status');
		}

		if (isset($this->request->post['credit_sort_order'])) {
			$data['credit_sort_order'] = $this->request->post['credit_sort_order'];
		} else {
			$data['credit_sort_order'] = $this->config->get('credit_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/credit.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/credit')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}