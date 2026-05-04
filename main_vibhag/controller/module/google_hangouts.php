<?php
class ControllerModuleGoogleHangouts extends Controller {
	private $error = array();

	public function index() {
        $data = array();
        //autoloading the language
        $this->load->autoLoadLanguage('module/google_hangouts',$data);	
       
		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('google_hangouts', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
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
			'href' => $this->url->link('module/google_hangouts', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('module/google_hangouts', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['google_hangouts_code'])) {
			$data['google_hangouts_code'] = $this->request->post['google_hangouts_code'];
		} else {
			$data['google_hangouts_code'] = $this->config->get('google_hangouts_code');
		}

		if (isset($this->request->post['google_hangouts_status'])) {
			$data['google_hangouts_status'] = $this->request->post['google_hangouts_status'];
		} else {
			$data['google_hangouts_status'] = $this->config->get('google_hangouts_status');
		}	

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/google_hangouts.tpl', $data));
	}

	protected function validate() {
        
		if (!$this->user->hasPermission('modify', 'module/google_hangouts')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['google_hangouts_code']) {
			$this->error['code'] = $this->language->get('error_code');
		}

		return !$this->error;
	}
}