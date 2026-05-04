<?php
class ControllerCommonReset extends Controller {
	private $error = array();

	public function index() {
		if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->data['token'])) {
			$this->response->redirect($this->url->link('common/dashboard', '', 'SSL'));
		}

		if (!$this->config->get('config_password')) {
			$this->response->redirect($this->url->link('common/login', '', 'SSL'));
		}

		if (isset($this->request->get['code'])) {
			$code = $this->request->get['code'];
		} else {
			$code = '';
		}

		$this->load->model('user/user');

		$user_info = $this->model_user_user->getUserByCode($code);

		if ($user_info) {
			$this->load->language('common/reset');

			$data = array(); // Initializing the data array to be passed on to template files
            // Autoloading the lanugage
            $this->load->autoLoadLanguage('common/reset', $data);

			$this->document->setTitle($data['heading_title']);

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				$this->model_user_user->editPassword($user_info['user_id'], $this->request->post['password']);

				$this->session->data['success'] = $data['text_success'];

				$this->response->redirect($this->url->link('common/login', '', 'SSL'));
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $data['text_home'],
				'href' => $this->url->link('common/dashboard', '', 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $data['heading_title'],
				'href' => $this->url->link('common/reset', '', 'SSL')
			);

			if (isset($this->error['password'])) {
				$data['error_password'] = $this->error['password'];
			} else {
				$data['error_password'] = '';
			}

			if (isset($this->error['confirm'])) {
				$data['error_confirm'] = $this->error['confirm'];
			} else {
				$data['error_confirm'] = '';
			}

			$data['action'] = $this->url->link('common/reset', 'code=' . $code, 'SSL');

			$data['cancel'] = $this->url->link('common/login', '', 'SSL');

			if (isset($this->request->post['password'])) {
				$data['password'] = $this->request->post['password'];
			} else {
				$data['password'] = '';
			}

			if (isset($this->request->post['confirm'])) {
				$data['confirm'] = $this->request->post['confirm'];
			} else {
				$data['confirm'] = '';
			}

			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('common/reset.tpl', $data));
		} else {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSettingValue('config', 'config_password', '0');

			return new Action('common/login');
		}
	}

	protected function validate() {
		if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if ($this->request->post['confirm'] != $this->request->post['password']) {
			$this->error['confirm'] = $this->language->get('error_confirm');
		}

		return !$this->error;
	}
}