<?php
class ControllerTotalVoucher extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('total/voucher');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('total/voucher', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('voucher', $this->request->post);

			$this->session->data['success'] = $data['text_success'];

			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
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
			'text' => $data['text_total'],
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('total/voucher', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('total/voucher', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['voucher_status'])) {
			$data['voucher_status'] = $this->request->post['voucher_status'];
		} else {
			$data['voucher_status'] = $this->config->get('voucher_status');
		}

		if (isset($this->request->post['voucher_sort_order'])) {
			$data['voucher_sort_order'] = $this->request->post['voucher_sort_order'];
		} else {
			$data['voucher_sort_order'] = $this->config->get('voucher_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/voucher.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/voucher')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}