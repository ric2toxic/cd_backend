<?php
class ControllerTotalHandling extends Controller {
	private $error = array();

	public function index() {

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('total/handling', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('handling', $this->request->post);

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
			'href' => $this->url->link('total/handling', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('total/handling', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['handling_total'])) {
			$data['handling_total'] = $this->request->post['handling_total'];
		} else {
			$data['handling_total'] = $this->config->get('handling_total');
		}

		if (isset($this->request->post['handling_fee'])) {
			$data['handling_fee'] = $this->request->post['handling_fee'];
		} else {
			$data['handling_fee'] = $this->config->get('handling_fee');
		}

		if (isset($this->request->post['handling_tax_class_id'])) {
			$data['handling_tax_class_id'] = $this->request->post['handling_tax_class_id'];
		} else {
			$data['handling_tax_class_id'] = $this->config->get('handling_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['handling_status'])) {
			$data['handling_status'] = $this->request->post['handling_status'];
		} else {
			$data['handling_status'] = $this->config->get('handling_status');
		}

		if (isset($this->request->post['handling_sort_order'])) {
			$data['handling_sort_order'] = $this->request->post['handling_sort_order'];
		} else {
			$data['handling_sort_order'] = $this->config->get('handling_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/handling.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/handling')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}