<?php
class ControllerCommonLogout extends Controller {
	public function index() {

		$this->user->logout();

		unset($this->session->data['token']);
		unset($this->session->data['chaabee']);
		unset($this->session->data['old_password_status']);
		unset($this->session->data['new_password_string']);
		unset($this->session->data['username']);

		$this->response->redirect($this->url->link('common/login', '', 'SSL'));
	}
}