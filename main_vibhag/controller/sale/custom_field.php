<?php
class ControllerSaleCustomField extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/custom_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/custom_field');

		$this->getList();
	}

	public function add() {
		$this->load->language('sale/custom_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/custom_field');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_custom_field->addCustomField($this->request->post);

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

			$this->response->redirect($this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('sale/custom_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/custom_field');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_custom_field->editCustomField($this->request->get['custom_field_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('sale/custom_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/custom_field');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $custom_field_id) {
				$this->model_sale_custom_field->deleteCustomField($custom_field_id);
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

			$this->response->redirect($this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('sale/custom_field', $data);
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'cfd.name';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('sale/custom_field/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('sale/custom_field/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['custom_fields'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$custom_field_total = $this->model_sale_custom_field->getTotalCustomFields();

		$results = $this->model_sale_custom_field->getCustomFields($filter_data);

		foreach ($results as $result) {
			$type = '';

			switch ($result['type']) {
				case 'select':
					$type = $data['text_select'];
					break;
				case 'radio':
					$type = $data['text_radio'];
					break;
				case 'checkbox':
					$type = $data['text_checkbox'];
					break;
				case 'input':
					$type = $data['text_input'];
					break;
				case 'text':
					$type = $data['text_text'];
					break;
				case 'textarea':
					$type = $data['text_textarea'];
					break;
				case 'file':
					$type = $data['text_file'];
					break;
				case 'date':
					$type = $data['text_date'];
					break;
				case 'datetime':
					$type = $data['text_datetime'];
					break;
				case 'time':
					$type = $data['text_time'];
					break;
			}

			$data['custom_fields'][] = array(
				'custom_field_id' => $result['custom_field_id'],
				'name'            => $result['name'],
				'location'        => $this->language->get('text_' . $result['location']),
				'type'            => $type,
				'status'          => $result['status'],
				'sort_order'      => $result['sort_order'],
				'edit'            => $this->url->link('sale/custom_field/edit', 'token=' . $this->session->data['token'] . '&custom_field_id=' . $result['custom_field_id'] . $url, 'SSL')
			);
		}

		$data['column_status'] = $this->language->get('column_status');

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

		$data['sort_name'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cfd.name' . $url, 'SSL');
		$data['sort_location'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cf.location' . $url, 'SSL');
		$data['sort_type'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cf.type' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cf.status' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cf.sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $custom_field_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($custom_field_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($custom_field_total - $this->config->get('config_limit_admin'))) ? $custom_field_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $custom_field_total, ceil($custom_field_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/custom_field_list.tpl', $data));
	}

	protected function getForm() {
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('sale/custom_field', $data);

		$data['text_form'] = !isset($this->request->get['custom_field_id']) ? $data['text_add'] : $data['text_edit'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['custom_field_value'])) {
			$data['error_custom_field_value'] = $this->error['custom_field_value'];
		} else {
			$data['error_custom_field_value'] = array();
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
			'href' => $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['custom_field_id'])) {
			$data['action'] = $this->url->link('sale/custom_field/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('sale/custom_field/edit', 'token=' . $this->session->data['token'] . '&custom_field_id=' . $this->request->get['custom_field_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['custom_field_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$custom_field_info = $this->model_sale_custom_field->getCustomField($this->request->get['custom_field_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['custom_field_description'])) {
			$data['custom_field_description'] = $this->request->post['custom_field_description'];
		} elseif (isset($this->request->get['custom_field_id'])) {
			$data['custom_field_description'] = $this->model_sale_custom_field->getCustomFieldDescriptions($this->request->get['custom_field_id']);
		} else {
			$data['custom_field_description'] = array();
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($custom_field_info)) {
			$data['location'] = $custom_field_info['location'];
		} else {
			$data['location'] = '';
		}

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($custom_field_info)) {
			$data['type'] = $custom_field_info['type'];
		} else {
			$data['type'] = '';
		}

		if (isset($this->request->post['value'])) {
			$data['value'] = $this->request->post['value'];
		} elseif (!empty($custom_field_info)) {
			$data['value'] = $custom_field_info['value'];
		} else {
			$data['value'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($custom_field_info)) {
			$data['status'] = $custom_field_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($custom_field_info)) {
			$data['sort_order'] = $custom_field_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['custom_field_value'])) {
			$custom_field_values = $this->request->post['custom_field_value'];
		} elseif (isset($this->request->get['custom_field_id'])) {
			$custom_field_values = $this->model_sale_custom_field->getCustomFieldValueDescriptions($this->request->get['custom_field_id']);
		} else {
			$custom_field_values = array();
		}

		$data['custom_field_values'] = array();

		foreach ($custom_field_values as $custom_field_value) {
			$data['custom_field_values'][] = array(
				'custom_field_value_id'          => $custom_field_value['custom_field_value_id'],
				'custom_field_value_description' => $custom_field_value['custom_field_value_description'],
				'sort_order'                     => $custom_field_value['sort_order']
			);
		}

		$data['custom_field_required'] = array();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/custom_field_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'sale/custom_field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['custom_field_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 128)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if (($this->request->post['type'] == 'select' || $this->request->post['type'] == 'radio' || $this->request->post['type'] == 'checkbox')) {
			if (!isset($this->request->post['custom_field_value'])) {
				$this->error['warning'] = $this->language->get('error_type');
			}

			if (isset($this->request->post['custom_field_value'])) {
				foreach ($this->request->post['custom_field_value'] as $custom_field_value_id => $custom_field_value) {
					foreach ($custom_field_value['custom_field_value_description'] as $language_id => $custom_field_value_description) {
						if ((utf8_strlen($custom_field_value_description['name']) < 1) || (utf8_strlen($custom_field_value_description['name']) > 128)) {
							$this->error['custom_field_value'][$custom_field_value_id][$language_id] = $this->language->get('error_custom_value');
						}
					}
				}
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/custom_field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}