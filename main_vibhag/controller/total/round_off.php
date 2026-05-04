<?php
class ControllerTotalRoundOff extends Controller {
	private $error = array();

	public function index() {
		$data = array();
		$this->load->autoLoadLanguage('total/round_off', $data);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('round_off', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_total'),
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('total/round_off', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('total/round_off', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['round_off_status'])) {
			$data['round_off_status'] = $this->request->post['round_off_status'];
		} else {
			$data['round_off_status'] = $this->config->get('round_off_status');
		}

		if (isset($this->request->post['round_off_sort_order'])) {
			$data['round_off_sort_order'] = $this->request->post['round_off_sort_order'];
		} else {
			$data['round_off_sort_order'] = $this->config->get('round_off_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/round_off.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/round_off')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
