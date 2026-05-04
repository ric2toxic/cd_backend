<?php
class ControllerCommonLogin extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('common/login');

		$data = array(); // Initializing the data array to be passed on to template files
		// Autoloading the lanugage
		$this->load->autoLoadLanguage('common/login', $data);

		$this->document->setTitle($data['heading_title']);

		if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->data['token'])) {
			$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->session->data['token'] = md5(mt_rand());

			if (isset($this->session->data['old_password_status']) && $this->session->data['old_password_status']) {
				/*
					if user loggedin with old password, logout user session and redirect to login page
					user need to login with new password string and update saved password in browser saved password.
				*/
				unset($this->session->data['token']);
				unset($this->session->data['chaabee']);
				$this->response->redirect($this->url->link('common/login', '', 'SSL'));

			} else {

				$landing_page = $this->user->isUserHasDefaultLandingPage();

				if (!empty($landing_page['dont_show_dashboard']) && !empty($landing_page['default_landing_page_url'])) {
					$this->response->redirect($this->url->link($landing_page['default_landing_page_url'], 'token=' . $this->session->data['token'], 'SSL'));

				} else if (!empty($landing_page['dont_show_dashboard']) && empty($landing_page['default_landing_page_url'])) {

					throw new \Exception('Wrong default landing page setting for user profile');

				} else if (empty($landing_page['dont_show_dashboard']) && !empty($landing_page['default_landing_page_url'])) {

					$this->response->redirect($this->url->link($landing_page['default_landing_page_url'], 'token=' . $this->session->data['token'], 'SSL'));

				} else {

					if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) === 0 || strpos($this->request->post['redirect'], HTTPS_SERVER) === 0)) {
						$this->response->redirect($this->request->post['redirect'] . '&token=' . $this->session->data['token']);
					} else {
						$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
					}

				}

			}

		}
		if ((isset($this->session->data['token']) && !isset($this->request->get['token'])) || ((isset($this->request->get['token']) && (isset($this->session->data['token']) && ($this->request->get['token'] != $this->session->data['token']))))) {
			$this->error['warning'] = $data['error_token'];
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['action'] = $this->url->link('common/login', '', 'SSL');

		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];
		} else {
			$data['username'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->get['route'])) {
			$route = $this->request->get['route'];

			unset($this->request->get['route']);
			unset($this->request->get['token']);

			$url = '';

			if ($this->request->get) {
				$url .= http_build_query($this->request->get);
			}

			$data['redirect'] = $this->url->link($route, $url, 'SSL');
		} else {
			$data['redirect'] = '';
		}

		$data['old_password_status'] = $this->session->data['old_password_status'] ?? 0;
		$data['new_password_string'] = $this->session->data['new_password_string'] ?? '';
		$data['username'] = $this->session->data['username'] ?? '';

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/login.tpl', $data));
	}

	protected function validate() {
		if (!isset($this->request->post['username']) || !isset($this->request->post['password']) || !$this->user->login($this->request->post['username'], $this->request->post['password'])) {
			$this->error['warning'] = $this->language->get('error_login');
		}
		return !$this->error;
	}

	public function check() {

		$route = isset($this->request->get['route']) ? $this->request->get['route'] : '';

		$ignore = array(
			'common/login',
			'common/reset',
		);
		if (!$this->user->isLogged() && !in_array($route, $ignore)) {
			return new Action('common/login');
		}

		if (isset($this->request->get['route'])) {
			$ignore = array(
				'common/login',
				'common/logout',
				'common/reset',
				'error/not_found',
				'error/permission',
			);

			if (!in_array($route, $ignore) && (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token']))) {
				return new Action('common/login');
			}
		} else {
			if (!isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
				return new Action('common/login');
			}
		}
	}
	public function auto() {
		$this->load->model('user/user');
		$token = $this->request->get['token'];
		$auto_login_id = $this->request->get['user_id'];
		$user_details = $this->model_user_user->getUser($auto_login_id);

		$this->user->logout();
		unset($this->session->data['token']);

		$obj = new User($this->registry);
		$obj->setId($user_details['user_id']);

		$this->session->data['token'] = md5(mt_rand());
		$this->session->data['user_id'] = $user_details['user_id'];
		$this->session->data['branch_code'] = $user_details['branch_code'];

		$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));

	}
}