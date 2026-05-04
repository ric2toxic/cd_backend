<?php
class ControllerTotalCashback extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('total/cashback');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('cashback', $this->request->post);
            
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_cashback_rate'] = $this->language->get('entry_cashback_rate');
        $data['entry_cashback_validity'] = $this->language->get('entry_cashback_validity');
        $data['entry_cashback_scheme'] = $this->language->get('entry_cashback_scheme');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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
			'href' => $this->url->link('total/cashback', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('total/cashback', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['cashback_status'])) {
			$data['cashback_status'] = $this->request->post['cashback_status'];
		} else {
			$data['cashback_status'] = $this->config->get('cashback_status');
		}
        
        if (isset($this->request->post['cashback_website_order'])) {
			$data['cashback_website_order'] = $this->request->post['cashback_website_order'];
		} else {
			$data['cashback_website_order'] = $this->config->get('cashback_website_order');
		}
        
        if (isset($this->request->post['cashback_app_order'])) {
			$data['cashback_app_order'] = $this->request->post['cashback_app_order'];
		} else {
			$data['cashback_app_order'] = $this->config->get('cashback_app_order');
		}

		if (isset($this->request->post['cashback_sort_order'])) {
			$data['cashback_sort_order'] = $this->request->post['cashback_sort_order'];
		} else {
			$data['cashback_sort_order'] = $this->config->get('cashback_sort_order');
		}
        
        if (isset($this->request->post['cashback_rate'])) {
			$data['cashback_rate'] = (float)$this->request->post['cashback_rate'];
		} else {
			$data['cashback_rate'] = (float)$this->config->get('cashback_rate');
		}
        
        if (isset($this->request->post['cashback_validity'])) {
			$data['cashback_validity'] = (int)$this->request->post['cashback_validity'];
		} else {
			$data['cashback_validity'] = (int)$this->config->get('cashback_validity');
		}
        
        if (isset($this->request->post['cashback_scheme'])) {
			$data['cashback_scheme'] = $this->request->post['cashback_scheme'];
		} else {
			$data['cashback_scheme'] = $this->config->get('cashback_scheme');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/cashback.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/cashback')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}