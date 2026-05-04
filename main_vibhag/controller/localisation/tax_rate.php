<?php
class ControllerLocalisationTaxRate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('localisation/tax_rate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/tax_rate');

		$this->getList();
	}

	public function add() {
		$this->load->language('localisation/tax_rate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/tax_rate');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_tax_rate->addTaxRate($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('localisation/tax_rate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/tax_rate');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_localisation_tax_rate->editTaxRate($this->request->get['tax_rate_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/tax_rate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/tax_rate');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $tax_rate_id) {
				$this->model_localisation_tax_rate->deleteTaxRate($tax_rate_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'tr.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('localisation/tax_rate', $data);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('localisation/tax_rate/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('localisation/tax_rate/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['tax_rates'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$tax_rate_total = $this->model_localisation_tax_rate->getTotalTaxRates();

		$results = $this->model_localisation_tax_rate->getTaxRates($filter_data);
		
		$output_result = array();
		
		foreach($results as $key=>$val) {
			$output_result[$val['name']][] = $val;
		}
		
		foreach ($output_result as $result) {
			$data['tax_rates'][] = array(
				'tax_rate_id'   => $result[0]['tax_rate_id'],
				'name'          => $result[0]['name'],
				'type'          => ($result[0]['type'] == 'F' ? $data['text_amount'] : $data['text_percent']),
				'date_added'    => date($data['date_format_short'],strtotime($result[0]['date_added'])),
				'date_modified' => date($data['date_format_short'],strtotime($result[0]['date_modified'])),
				'sub_arr'       => $result,
				'edit'          => $this->url->link('localisation/tax_rate/edit', 'token=' . $this->session->data['token'] . '&tax_rate_id=' . $result[0]['tax_rate_id'] . $url, 'SSL')
			);
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . '&sort=tr.name' . $url, 'SSL');
		$data['sort_type'] = $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . '&sort=tr.type' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . '&sort=tr.date_added' . $url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . '&sort=tr.date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$pagination = new Pagination();
		$pagination->total = sizeof($output_result);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], (sizeof($output_result)) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > (sizeof($output_result) - $this->config->get('config_limit_admin'))) ? sizeof($output_result) : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), sizeof($output_result), ceil(sizeof($output_result) / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('localisation/tax_rate_list.tpl', $data));
	}

	protected function getForm() {
	
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('localisation/tax_rate', $data);

		$data['text_form'] = !isset($this->request->get['tax_rate_id']) ? $data['text_add'] : $data['text_edit'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['tax_rate_end_range'])) {
			$data['error_tax_rate_end_range'] = $this->error['tax_rate_end_range'];
		} else {
			$data['error_tax_rate_end_range'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['tax_rate_id'])) {
			$data['action'] = $this->url->link('localisation/tax_rate/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('localisation/tax_rate/edit', 'token=' . $this->session->data['token'] . '&tax_rate_id=' . $this->request->get['tax_rate_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'] . $url, 'SSL');
	
		if (isset($this->request->get['tax_rate_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$tax_rate_info = $this->model_localisation_tax_rate->getTaxRate($this->request->get['tax_rate_id']);
		}	
	
	
		if (isset($this->request->post['tax_arr'])) {
			$data['tax_arr'] = $this->request->post['tax_arr'];
		} elseif (!empty($tax_rate_info)) {
			$data['tax_arr'] = $tax_rate_info;
		} else {
			$data['tax_arr'] = '';
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($tax_rate_info)) {
			$data['name'] = $tax_rate_info[sizeof($tax_rate_info)-1]['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['tax_rate_end_range'])) {
			$data['tax_rate_end_range'] = $this->request->post['tax_rate_end_range'];
		} elseif (!empty($tax_rate_info)) {
			$data['tax_rate_end_range'] = $tax_rate_info[sizeof($tax_rate_info)-1]['rate'];
		} else {
			$data['tax_rate_end_range'] = '';
		}

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($tax_rate_info)) {
			$data['type'] = $tax_rate_info[sizeof($tax_rate_info)-1]['type'];
		} else {
			$data['type'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/tax_rate_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/tax_rate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['tax_rate_end_range']) {
			$this->error['tax_rate_end_range'] = $this->language->get('error_tax_rate_end_range');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/tax_rate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('localisation/tax_class');

		foreach ($this->request->post['selected'] as $tax_rate_id) {
			$tax_rule_total = $this->model_localisation_tax_class->getTotalTaxRulesByTaxRateId($tax_rate_id);

			if ($tax_rule_total) {
				$this->error['warning'] = sprintf($this->language->get('error_tax_rule'), $tax_rule_total);
			}
		}

		return !$this->error;
	}
}
