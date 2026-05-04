<?php
class ControllerFeedGoogleBase extends Controller {
	private $error = array();

	public function index() {

		$data = array();
		$this->load->autoLoadLanguage('feed/google_base',$data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('google_base', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
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
			'text' => $data['text_feed'],
			'href' => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('feed/google_base', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('feed/google_base', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['google_base_status'])) {
			$data['google_base_status'] = $this->request->post['google_base_status'];
		} else {
			$data['google_base_status'] = $this->config->get('google_base_status');
		}

		$data['data_feed'] = HTTP_CATALOG . 'index.php?route=feed/google_base';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('feed/google_base.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'feed/google_base')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}