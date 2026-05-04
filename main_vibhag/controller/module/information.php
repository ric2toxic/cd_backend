<?php
class ControllerModuleInformation extends Controller {
	private $error = array();

	public function index() {

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('module/information', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('information', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
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
			'text' => $data['text_module'],
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('module/information', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('module/information', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['information_status'])) {
			$data['information_status'] = $this->request->post['information_status'];
		} else {
			$data['information_status'] = $this->config->get('information_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/information.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}