<?php
class ControllerTotalShipping extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('total/shipping');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('total/shipping', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
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
			'text' => $data['text_total'],
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('total/shipping', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('total/shipping', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['shipping_estimator'])) {
			$data['shipping_estimator'] = $this->request->post['shipping_estimator'];
		} else {
			$data['shipping_estimator'] = $this->config->get('shipping_estimator');
		}

		if (isset($this->request->post['shipping_status'])) {
			$data['shipping_status'] = $this->request->post['shipping_status'];
		} else {
			$data['shipping_status'] = $this->config->get('shipping_status');
		}

		if (isset($this->request->post['shipping_sort_order'])) {
			$data['shipping_sort_order'] = $this->request->post['shipping_sort_order'];
		} else {
			$data['shipping_sort_order'] = $this->config->get('shipping_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/shipping.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/shipping')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}