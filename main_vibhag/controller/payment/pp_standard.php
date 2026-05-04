<?php
class ControllerPaymentPPStandard extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('payment/pp_standard');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('payment/pp_standard', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pp_standard', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}


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
			'href' => $this->url->link('payment/pp_standard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/pp_standard', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['pp_standard_email'])) {
			$data['pp_standard_email'] = $this->request->post['pp_standard_email'];
		} else {
			$data['pp_standard_email'] = $this->config->get('pp_standard_email');
		}

		if (isset($this->request->post['pp_standard_test'])) {
			$data['pp_standard_test'] = $this->request->post['pp_standard_test'];
		} else {
			$data['pp_standard_test'] = $this->config->get('pp_standard_test');
		}

		if (isset($this->request->post['pp_standard_transaction'])) {
			$data['pp_standard_transaction'] = $this->request->post['pp_standard_transaction'];
		} else {
			$data['pp_standard_transaction'] = $this->config->get('pp_standard_transaction');
		}

		if (isset($this->request->post['pp_standard_debug'])) {
			$data['pp_standard_debug'] = $this->request->post['pp_standard_debug'];
		} else {
			$data['pp_standard_debug'] = $this->config->get('pp_standard_debug');
		}

		if (isset($this->request->post['pp_standard_total'])) {
			$data['pp_standard_total'] = $this->request->post['pp_standard_total'];
		} else {
			$data['pp_standard_total'] = $this->config->get('pp_standard_total');
		}

		if (isset($this->request->post['pp_standard_canceled_reversal_status_id'])) {
			$data['pp_standard_canceled_reversal_status_id'] = $this->request->post['pp_standard_canceled_reversal_status_id'];
		} else {
			$data['pp_standard_canceled_reversal_status_id'] = $this->config->get('pp_standard_canceled_reversal_status_id');
		}

		if (isset($this->request->post['pp_standard_completed_status_id'])) {
			$data['pp_standard_completed_status_id'] = $this->request->post['pp_standard_completed_status_id'];
		} else {
			$data['pp_standard_completed_status_id'] = $this->config->get('pp_standard_completed_status_id');
		}

		if (isset($this->request->post['pp_standard_denied_status_id'])) {
			$data['pp_standard_denied_status_id'] = $this->request->post['pp_standard_denied_status_id'];
		} else {
			$data['pp_standard_denied_status_id'] = $this->config->get('pp_standard_denied_status_id');
		}

		if (isset($this->request->post['pp_standard_expired_status_id'])) {
			$data['pp_standard_expired_status_id'] = $this->request->post['pp_standard_expired_status_id'];
		} else {
			$data['pp_standard_expired_status_id'] = $this->config->get('pp_standard_expired_status_id');
		}

		if (isset($this->request->post['pp_standard_failed_status_id'])) {
			$data['pp_standard_failed_status_id'] = $this->request->post['pp_standard_failed_status_id'];
		} else {
			$data['pp_standard_failed_status_id'] = $this->config->get('pp_standard_failed_status_id');
		}

		if (isset($this->request->post['pp_standard_pending_status_id'])) {
			$data['pp_standard_pending_status_id'] = $this->request->post['pp_standard_pending_status_id'];
		} else {
			$data['pp_standard_pending_status_id'] = $this->config->get('pp_standard_pending_status_id');
		}

		if (isset($this->request->post['pp_standard_processed_status_id'])) {
			$data['pp_standard_processed_status_id'] = $this->request->post['pp_standard_processed_status_id'];
		} else {
			$data['pp_standard_processed_status_id'] = $this->config->get('pp_standard_processed_status_id');
		}

		if (isset($this->request->post['pp_standard_refunded_status_id'])) {
			$data['pp_standard_refunded_status_id'] = $this->request->post['pp_standard_refunded_status_id'];
		} else {
			$data['pp_standard_refunded_status_id'] = $this->config->get('pp_standard_refunded_status_id');
		}

		if (isset($this->request->post['pp_standard_reversed_status_id'])) {
			$data['pp_standard_reversed_status_id'] = $this->request->post['pp_standard_reversed_status_id'];
		} else {
			$data['pp_standard_reversed_status_id'] = $this->config->get('pp_standard_reversed_status_id');
		}

		if (isset($this->request->post['pp_standard_voided_status_id'])) {
			$data['pp_standard_voided_status_id'] = $this->request->post['pp_standard_voided_status_id'];
		} else {
			$data['pp_standard_voided_status_id'] = $this->config->get('pp_standard_voided_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['pp_standard_geo_zone_id'])) {
			$data['pp_standard_geo_zone_id'] = $this->request->post['pp_standard_geo_zone_id'];
		} else {
			$data['pp_standard_geo_zone_id'] = $this->config->get('pp_standard_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['pp_standard_status'])) {
			$data['pp_standard_status'] = $this->request->post['pp_standard_status'];
		} else {
			$data['pp_standard_status'] = $this->config->get('pp_standard_status');
		}

		if (isset($this->request->post['pp_standard_sort_order'])) {
			$data['pp_standard_sort_order'] = $this->request->post['pp_standard_sort_order'];
		} else {
			$data['pp_standard_sort_order'] = $this->config->get('pp_standard_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/pp_standard.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/pp_standard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['pp_standard_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		return !$this->error;
	}
}