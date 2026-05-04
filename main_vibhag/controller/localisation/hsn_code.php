<?php

class ControllerLocalisationHsnCode extends Controller {
	
	private $error = array();

	public function index() {
		
		$this->load->model('localisation/hsn_code');

		$this->getList();
	}
	
	public function add() {
		$this->load->language('localisation/hsn_code');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/hsn_code');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		
			$this->model_localisation_hsn_code->add($this->request->post);

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

			$this->response->redirect($this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('localisation/hsn_code');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/hsn_code');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_hsn_code->edit($this->request->get['id'], $this->request->post);

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

			$this->response->redirect($this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/hsn_code');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/hsn_code');

		if (isset($this->request->post['selected']) ) {
			
			foreach ($this->request->post['selected'] as $hsn_id) {
				$this->model_localisation_hsn_code->delete($hsn_id);
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

			$this->response->redirect($this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}


	
	protected function getList() {
		
		$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('localisation/hsn_code', $data);
		
		$this->document->setTitle($data['heading_title']);
		
		$sort  = $this->request->get['sort'] ?? 'id';
		$order = $this->request->get['order'] ?? 'ASC'; 

		$page               = $this->request->get['page'] ?? 1;
        $filter_hsn_code    = $this->request->get['filter_hsn_code'] ?? '';
        $filter_description = $this->request->get['filter_description'] ?? '';
		
		$url = '';
		
		$data['token'] = $this->session->data['token'];

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
			'href' => $this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('localisation/hsn_code/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('localisation/hsn_code/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['categories'] = array();

        if (isset($this->request->get['filter_hsn_code'])) {
            $url .= '&filter_hsn_code=' . urlencode(html_entity_decode($this->request->get['filter_hsn_code'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_description'])) {
            $url .= '&filter_description=' . urlencode(html_entity_decode($this->request->get['filter_description'], ENT_QUOTES, 'UTF-8'));
        }
        
        $filter_data = array(
            'filter_hsn_code'       => $filter_hsn_code,
            'filter_description'    => $filter_description,
            'sort'                  => $sort,
            'order'                 => $order,
            'start'                 => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                 => $this->config->get('config_limit_admin')
        );
        
		$hsn_total = $this->model_localisation_hsn_code->getTotalHSNCodes($filter_data);
		
		$results = $this->model_localisation_hsn_code->getHSNCodes($filter_data);	
		$data['hsn_codes'] = array();
		$this->load->model('localisation/tax_class');
		
		foreach ($results as $result) {
			$data['hsn_codes'][] = array(
				'hsn_id'        => $result['id'],
				'hsn_code'      => $result['hsn_code'],
				'tax_class_title'  => $this->model_localisation_tax_class->getTaxClass($result['tax_class_id'])['title'],
				'tax_class_id'  => $result['tax_class_id'],
				'hsn_description' => $result['hsn_description'],
				'edit'        => $this->url->link('localisation/hsn_code/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL'),
				'delete'      => $this->url->link('localisation/hsn_code/delete', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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
		
		$data['sort_name'] = $this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . '&sort=id' . $url, 'SSL');
		$data['sort_description'] = $this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . '&sort=id' . $url, 'SSL');
		$data['sort_hsn_class_id'] = $this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . '&sort=hsn_class_id' . $url, 'SSL');

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $hsn_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($data['text_pagination'], ($hsn_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($hsn_total - $this->config->get('config_limit_admin'))) ? $hsn_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $hsn_total, ceil($hsn_total / $this->config->get('config_limit_admin')));
		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['filter_hsn_code'] =  $filter_data['filter_hsn_code']?$filter_data['filter_hsn_code']:'';
		$data['filter_hsn_description'] = $filter_data['filter_description'] ?? '';
		$this->response->setOutput($this->load->view('localisation/hsn_code_list.tpl', $data));
	}
	
	
	protected function getForm() {
		$this->load->model('localisation/hsn_code');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('localisation/hsn_code', $data);

		$data['text_form'] = !isset($this->request->get['tax_class_id']) ? $data['text_add'] : $data['text_edit'];
		$data['token'] = $this->session->data['token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['hsn_code'])) {
			$data['error_hsn_code'] = $this->error['hsn_code'];
		} else {
			$data['error_hsn_code'] = '';
		}

		if (isset($this->error['hsn_class_id'])) {
			$data['error_hsn_class_id'] = $this->error['hsn_class_id'];
		} else {
			$data['error_hsn_class_id'] = '';
		}
		
		if (isset($this->error['hsn_date_added'])) {
			$data['error_hsn_date_added'] = $this->error['hsn_date_added'];
		} else {
			$data['error_hsn_date_added'] = '';
		}	
		
		if (isset($this->error['hsn_description'])) {
			$data['error_hsn_description'] = $this->error['hsn_description'];
		} else {
			$data['error_hsn_description'] = '';
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
			'href' => $this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('localisation/hsn_code/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('localisation/hsn_code/edit', 'token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('localisation/hsn_code', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$hsn_code_info = $this->model_localisation_hsn_code->getHSNCodeInfo($this->request->get['id']);
		}
		
		if (isset($this->request->post['hsn_code'])) {
			$data['hsn_code'] = $this->request->post['hsn_code'];
		} elseif (!empty($hsn_code_info)) {
			$data['hsn_code'] = $hsn_code_info['hsn_code'];
		} else {
			$data['hsn_code'] = '';
		}
		
		if (isset($this->request->post['hsn_description'])) {
			$data['hsn_description'] = $this->request->post['hsn_description'];
		} elseif (!empty($hsn_code_info)) {
			$data['hsn_description'] = $hsn_code_info['hsn_description'];
		} else {
			$data['hsn_description'] = '';
		}
		
		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($hsn_code_info)) {
			$new_date = date("m/d/Y", strtotime($hsn_code_info['date_added']));
			$data['date_added'] = $new_date;
		} else {
			$data['date_added'] = '';
		}
		
		$this->load->model('localisation/tax_class');

		if (isset($this->request->post['tax_class_title'])) {
			$data['tax_class_title'] = $this->request->post['tax_class_title'];
		} elseif (!empty($hsn_code_info)) {
			$data['tax_class_title'] = $this->model_localisation_tax_class->getTaxClass($hsn_code_info['tax_class_id'])['title'];
		} else {
			$data['tax_class_title'] = '';
		}
		
		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($hsn_code_info)) {
			$data['tax_class_id'] = $hsn_code_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = '';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/hsn_code_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/hsn_code')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!(strlen($this->request->post['hsn_code']) >= 4 && strlen($this->request->post['hsn_code']) <= 8 && preg_match("/[0-9]{4,}/", $this->request->post['hsn_code']))) {
			$this->error['hsn_code'] = $this->language->get('error_hsn_code');
		}
		
		if ((empty($this->request->post['hsn_description']) ) ) {
			$this->error['hsn_description'] = $this->language->get('error_hsn_description');
		}
		
		if ((empty($this->request->post['hsn_class_id']) ) ) {
			$this->error['hsn_class_id'] = $this->language->get('error_hsn_class_id');
		}
		
		return !$this->error;
	}

	//tax_class names for hsn_code;
	public function autocomplete() {
		
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			
			$this->load->model('localisation/tax_class');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 15
			);
			$sql = "SELECT * FROM " . DB_PREFIX . "tax_class WHERE status = 1";
			
			$query = $this->db->query($sql);
			if($query->num_rows) {
				$tax_class_list = $query->rows;
				foreach ($tax_class_list as $tax_class) {
					$json[] = array(
						'tax_class_id' => $tax_class['tax_class_id'],
						'title'      => strip_tags(html_entity_decode($tax_class['title'], ENT_QUOTES, 'UTF-8'))
					);
				}	
			}
		}
	
		$sort_order = array();
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['title'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
}


?>
