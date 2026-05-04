<?php
class ControllerModulePPButton extends Controller {
	public function index() {

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('module/pp_button', $data);

		$this->load->model('setting/setting');

		$this->document->setTitle($data['heading_title']);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pp_button', $this->request->post);

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
			'href' => $this->url->link('module/pp_button', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('module/pp_button', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['pp_button_status'])) {
			$data['pp_button_status'] = $this->request->post['pp_button_status'];
		} else {
			$data['pp_button_status'] = $this->config->get('pp_button_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/pp_button.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/pp_button')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}