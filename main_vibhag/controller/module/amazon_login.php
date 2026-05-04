<?php
class ControllerModuleAmazonLogin extends Controller {

	private $error = array();

	public function index() {
		//$this->language->load('module/amazon_login');

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('module/amazon_login', $data);

		$this->load->model('setting/setting');
		$this->load->model('design/layout');

		$this->document->setTitle($data['heading_title']);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('amazon_login', $this->request->post);

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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['text_module'],
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('module/amazon_login', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('module/amazon_login', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['amazon_login_button_type'])) {
			$data['amazon_login_button_type'] = $this->request->post['amazon_login_button_type'];
		} elseif ($this->config->get('amazon_login_button_type')) {
			$data['amazon_login_button_type'] = $this->config->get('amazon_login_button_type');
		} else {
			$data['amazon_login_button_type'] = 'LwA';
		}

		if (isset($this->request->post['amazon_login_button_colour'])) {
			$data['amazon_login_button_colour'] = $this->request->post['amazon_login_button_colour'];
		} elseif ($this->config->get('amazon_login_button_colour')) {
			$data['amazon_login_button_colour'] = $this->config->get('amazon_login_button_colour');
		} else {
			$data['amazon_login_button_colour'] = 'gold';
		}

		if (isset($this->request->post['amazon_login_button_size'])) {
			$data['amazon_login_button_size'] = $this->request->post['amazon_login_button_size'];
		} elseif ($this->config->get('amazon_login_button_size')) {
			$data['amazon_login_button_size'] = $this->config->get('amazon_login_button_size');
		} else {
			$data['amazon_login_button_size'] = 'medium';
		}

		if (isset($this->request->post['amazon_login_status'])) {
			$data['amazon_login_status'] = $this->request->post['amazon_login_status'];
		} else {
			$data['amazon_login_status'] = $this->config->get('amazon_login_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/amazon_login.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/amazon_login')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function install() {
			$this->load->model('extension/event');
			$this->model_extension_event->addEvent('amazon_login', 'post.customer.logout', 'module/amazon_login/logout');
	}

	public function uninstall() {
			$this->load->model('extension/event');
			$this->model_extension_event->deleteEvent('amazon_login');
	}

}
