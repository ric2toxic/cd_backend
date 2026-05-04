<?php
class ControllerTotalKlarnaFee extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('total/klarna_fee');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('total/klarna_fee', $data);

		$this->document->setTitle($data['heading_title']);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$status = false;

			foreach ($this->request->post['klarna_fee'] as $klarna_account) {
				if ($klarna_account['status']) {
					$status = true;

					break;
				}
			}

			$this->model_setting_setting->editSetting('klarna_fee', array_merge($this->request->post, array('klarna_fee_status' => $status)));

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
			'href' => $this->url->link('total/klarna_fee', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('total/klarna_fee', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		$data['countries'] = array();

		$data['countries'][] = array(
			'name' => $data['text_germany'],
			'code' => 'DEU'
		);

		$data['countries'][] = array(
			'name' => $data['text_netherlands'],
			'code' => 'NLD'
		);

		$data['countries'][] = array(
			'name' => $data['text_denmark'],
			'code' => 'DNK'
		);

		$data['countries'][] = array(
			'name' => $data['text_sweden'],
			'code' => 'SWE'
		);

		$data['countries'][] = array(
			'name' => $data['text_norway'],
			'code' => 'NOR'
		);

		$data['countries'][] = array(
			'name' => $data['text_finland'],
			'code' => 'FIN'
		);

		if (isset($this->request->post['klarna_fee'])) {
			$data['klarna_fee'] = $this->request->post['klarna_fee'];
		} else {
			$data['klarna_fee'] = $this->config->get('klarna_fee');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/klarna_fee.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/klarna_fee')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}