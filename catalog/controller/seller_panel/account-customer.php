<?php
class ControllerSellerAccountCustomer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('seller/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/customer');

		$this->getList();
	}

	public function add() {
		$this->load->language('seller/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			// Hard Coding customer group_id

			$this->model_seller_customer->addCustomer($this->request->post);


			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_store'])) {
				$url .= '&filter_store=' . $this->request->get['filter_store'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {

		$this->load->language('seller/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_seller_customer->editCustomer($this->request->get['customer_id'], $this->request->post);
			
			$this->model_seller_customer->customer_preferences($this->request->get['customer_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_store'])) {
				$url .= '&filter_store=' . $this->request->get['filter_store'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		

		$this->getForm();
	}

	public function delete() {
		$this->load->language('seller/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/customer');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $customer_id) {
				$this->model_seller_customer->deleteCustomer($customer_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_store'])) {
				$url .= '&filter_store=' . $this->request->get['filter_store'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	public function unlock() {
		$this->load->language('seller/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('seller/customer');

		if (isset($this->request->get['email']) && $this->validateUnlock()) {
			$this->model_seller_customer->deleteLoginAttempts($this->request->get['email']);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_store'])) {
				$url .= '&filter_store=' . $this->request->get['filter_store'];
			}
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('seller/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}
        
        if (isset($this->request->get['filter_telephone'])) {
			$filter_telephone = $this->request->get['filter_telephone'];
		} else {
			$filter_telephone = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
	
		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
		 $filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_is_dropshipper'])) {
		 	$filter_is_dropshipper = $this->request->get['filter_is_dropshipper'];
		} else {
			if(!empty($this->request->get['filter_is_dropshipper'])){
				$filter_is_dropshipper = $this->request->get['filter_is_dropshipper'];	
			}else{
				$filter_is_dropshipper = 0;
			}
			
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'c.date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_is_dropshipper'])) {
			$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$seller_stores = array_column($this->MsLoader->MsProduct->getSellerStores($this->customer->getId()) , 'store_id');
		
		$str_data =array();

		foreach ($seller_stores as $seller_store) {
			$str_data[] = $this->MsLoader->MsProduct->getStore($seller_store);
		}
		$data['seller_stores'] = $str_data;
		if (isset($this->request->get['filter_store']) && !empty($this->request->get['filter_store'])) {
			$store = array($this->request->get['filter_store']);
		} else {
			$store = $seller_stores;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('seller_panel/account-order', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('seller/account-customer/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('seller/account-customer/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['customers'] = array();


		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_email'             => $filter_email,
            'filter_telephone'         => $filter_telephone,
			'filter_status'            => $filter_status,
			'filter_date_added'        => $filter_date_added,
			'filter_is_dropshipper'    => $filter_is_dropshipper,
			'sort'                     => $sort,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin'),
			'stores'                   => $store
		);

		$customer_total = $this->model_seller_customer->getTotalCustomers($filter_data);

		$results = $this->model_seller_customer->getCustomers($filter_data);

		foreach ($results as $result) {

			$login_info = $this->model_seller_customer->getTotalLoginAttempts($result['email']);

			if ($login_info && $login_info['total'] >= $this->config->get('config_login_attempts')) {
				$unlock = $this->url->link('seller/account-customer/unlock', 'token=' . $this->session->data['token'] . '&email=' . $result['email'] . $url, 'SSL');
			} else {
				$unlock = '';
			}

			$data['customers'][] = array(
				'customer_id'    => $result['customer_id'],
				'name'           => $result['name'],
				'email'          => $result['email'],
                'telephone'      => $result['telephone'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'ip'             => $result['ip'],
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'last_login'     => $result['last_login'] ? date($this->language->get('date_format_short'), strtotime($result['last_login'])) : 'Never',
				'is_dropshipper' => $result['is_dropshipper'],
				'unlock'         => $unlock,
				'edit'           => $this->url->link('seller/account-customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] . $url, 'SSL'),
                'app_installed'  => ($result['ws_gcm_registration_id'] != '') ? true : false,
				'max_credit_limit' =>$result['max_credit_limit'],
				'credit_limit_consumed' =>$result['credit_limit_consumed'],
				'is_credit_allowed' =>$result['is_credit_allowed'],

			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_active'] = $this->language->get('text_active');
		$data['text_panding'] = $this->language->get('text_panding');
		$data['text_block'] = $this->language->get('text_block');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_max_credit_limit'] = $this->language->get('text_max_credit_limit');
		$data['text_credit_limit_consumed'] = $this->language->get('text_credit_limit_consumed');
		$data['button_text_reset_credit'] = $this->language->get('button_text_reset_credit');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_email'] = $this->language->get('column_email');
        $data['column_telephone'] = $this->language->get('column_telephone');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_ip'] = $this->language->get('column_ip');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');
        $data['column_last_login'] = $this->language->get('column_last_login');
        $data['column_credit_limit'] = $this->language->get('column_credit_limit');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_store_id'] = $this->language->get('entry_store_id');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_ip'] = $this->language->get('entry_ip');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_dropshipper'] = $this->language->get('entry_dropshipper');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_login'] = $this->language->get('button_login');
		$data['button_unlock'] = $this->language->get('button_unlock');

		$data['token'] = $this->session->data['token'];

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_is_dropshipper'])) {
			$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_email'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . '&sort=c.email' . $url, 'SSL');
        $data['sort_telephone'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . '&sort=c.telephone' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . '&sort=c.status' . $url, 'SSL');
		$data['sort_is_dropshipper'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . '&sort=c.is_dropshipper' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . '&sort=c.date_added' . $url, 'SSL');
        $data['sort_last_login'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . '&sort=last_login' . $url, 'SSL');
		$data['sort_credit_limit'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . '&sort=c.credit_limit_consumed' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . urlencode(html_entity_decode($this->request->get['filter_telephone'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_is_dropshipper'])) {
			$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $customer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
        $data['filter_telephone'] = $filter_telephone;
		$data['filter_status'] = $filter_status;
		$data['filter_store'] = $filter_store;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_is_dropshipper'] = $filter_is_dropshipper;

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/seller_header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/seller_footer');

		list($template) = $this->MsLoader->MsHelper->loadTemplate('customer_list');

		$this->response->setOutput($this->load->view($template, $data));

		// $this->response->setOutput($this->load->view('seller/account-customer_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['customer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_add_ban_ip'] = $this->language->get('text_add_ban_ip');
		$data['text_remove_ban_ip'] = $this->language->get('text_remove_ban_ip');
		$data['text_active'] = $this->language->get('text_active');
		$data['text_panding'] = $this->language->get('text_panding');
		$data['text_block'] = $this->language->get('text_block');
		$data['text_select'] = $this->language->get('text_select');


		$data['text_credit_allowed']           = $this->language->get('text_credit_allowed');
		$data['text_max_credit_limit']         = $this->language->get('text_max_credit_limit');
		$data['text_credit_limit_consumed']    = $this->language->get('text_credit_limit_consumed');
		$data['button_credit_limit_reset_date']= $this->language->get('button_credit_limit_reset_date');

		$data['entry_store_id'] = $this->language->get('entry_store_id');
		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_confirm'] = $this->language->get('entry_confirm');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_default'] = $this->language->get('entry_default');
		$data['entry_comment'] = $this->language->get('entry_comment');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_amount'] = $this->language->get('entry_amount');
		$data['entry_points'] = $this->language->get('entry_points');
		$data['entry_dropshipper'] = $this->language->get('entry_dropshipper');
		$data['entry_categories'] = $this->language->get('entry_categories');
		$data['entry_filters'] = $this->language->get('entry_filters');
		$data['entry_price_filter'] = $this->language->get('entry_price_filter');
		$data['entry_price_min'] = $this->language->get('entry_price_min');
		$data['entry_price_max'] = $this->language->get('entry_price_max');

		$data['help_safe'] = $this->language->get('help_safe');
		$data['help_points'] = $this->language->get('help_points');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_address_add'] = $this->language->get('button_address_add');
		$data['button_history_add'] = $this->language->get('button_history_add');
		$data['button_transaction_add'] = $this->language->get('button_transaction_add');
		$data['button_reward_add'] = $this->language->get('button_reward_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_upload'] = $this->language->get('button_upload');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_address'] = $this->language->get('tab_address');
		$data['tab_history'] = $this->language->get('tab_history');
		$data['tab_transaction'] = $this->language->get('tab_transaction');
		$data['tab_reward'] = $this->language->get('tab_reward');
		$data['tab_ip'] = $this->language->get('tab_ip');
		$data['tab_preferences'] = $this->language->get('Preferences');

		$data['token'] = $this->session->data['token'];
		// $data['categories_data'] = $this->model_seller_customer->get_customer_preferences($this->request->get['customer_id']);
		// $data['cust_quality'] = $this->model_seller_customer->get_customer_quality($this->request->get['customer_id']);

		$quality = array();
		$category = array();
		$filter_ids = array();

		$data['quality'] = '';

		// foreach ($data['categories_data'] as $value) {
		// 	if(!empty($value['category_id'])){
		// 		$data['category'][] 	= 	$value['category_id'];
		// 		$data['min_price'][]	= 	$value['min_price'];
		// 		$data['max_price'][]	=	$value['max_price'];
		// 	}else{
		// 		$data['category'] 	= 	'';
		// 		$data['min_price'] 	=	'';
		// 		$data['max_price']	=	'';
		// 	}
		// }

		// $data['categories'] = $this->model_seller_customer->getcategories();

		if (isset($this->request->get['customer_id'])) {
			$data['customer_id'] = $this->request->get['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
	
		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_is_dropshipper'])) {
			$url .= '&filter_is_dropshipper=' . $this->request->get['filter_is_dropshipper'];
		}

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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['customer_id'])) {
			$data['action'] = $this->url->link('seller/account-customer/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('seller/account-customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('seller/account-customer', 'token=' . $this->session->data['token'] . $url, 'SSL');

		
		$seller_stores = array_column($this->MsLoader->MsProduct->getSellerStores($this->customer->getId()) , 'store_id');
		
		$str_data =array();

		foreach ($seller_stores as $seller_store) {
			$str_data[] = $this->MsLoader->MsProduct->getStore($seller_store);
		}
		$data['seller_stores'] = $str_data;

		$seler_store_id = implode(',', $seller_stores);

		if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$customer_info = $this->model_seller_customer->getCustomer($this->request->get['customer_id']);
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['is_dropshipper'])) {
			$data['is_dropshipper'] = $this->request->post['is_dropshipper'];
		} elseif (!empty($customer_info)) {
			$data['is_dropshipper'] = $customer_info['is_dropshipper'];
		} else {
			$data['is_dropshipper'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($customer_info)) {
			$data['status'] = $customer_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['is_credit_allowed'])) {
			$data['is_credit_allowed'] = $this->request->post['is_credit_allowed'];
		} elseif (!empty($customer_info)) {
			$data['is_credit_allowed'] = $customer_info['is_credit_allowed'];
		} else {
			$data['is_credit_allowed'] = 0;
		}

		if (isset($this->request->post['max_credit_limit'])) {
			$data['max_credit_limit'] = $this->request->post['max_credit_limit'];
		} elseif (!empty($customer_info)) {
			$data['max_credit_limit'] = $customer_info['max_credit_limit'];
		} else {
			$data['max_credit_limit'] = 0;
		}

		if (isset($this->request->post['credit_limit_consumed'])) {
			$data['credit_limit_consumed'] = $this->request->post['credit_limit_consumed'];
		} elseif (!empty($customer_info)) {
			$data['credit_limit_consumed'] = $customer_info['credit_limit_consumed'];
		} else {
			$data['credit_limit_consumed'] = 0;
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['address'])) {
			$data['addresses'] = $this->request->post['address'];
		} elseif (isset($this->request->get['customer_id'])) {
			$data['addresses'] = $this->model_seller_customer->getAddresses($this->request->get['customer_id']);
		} else {
			$data['addresses'] = array();
		}

		if (isset($this->request->post['address_id'])) {
			$data['address_id'] = $this->request->post['address_id'];
		} elseif (!empty($customer_info)) {
			$data['address_id'] = $customer_info['address_id'];
		} else {
			$data['address_id'] = '';
		}

		$data['header'] = $this->load->controller('common/seller_header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/seller_footer');

		if(!empty($customer_info)){
			list($template) = $this->MsLoader->MsHelper->loadTemplate('customer_form');
			$this->response->setOutput($this->load->view($template, $data));	
		}
		// $this->response->setOutput($this->load->view('seller/account-customer_form.tpl', $data));
	}

	protected function validateForm() {
		// if (!$this->user->hasPermission('modify', 'seller/account-customer')) {
		// 	$this->error['warning'] = $this->language->get('error_permission');
		// }

		if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		//if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
		//	$this->error['lastname'] = $this->language->get('error_lastname');
		//}

		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		// $customer_info = $this->model_seller_customer->getCustomerByEmail($this->request->post['email']);

		// if (!isset($this->request->get['customer_id'])) {
		// 	if ($customer_info) {
		// 		$this->error['warning'] = $this->language->get('error_exists');
		// 	}
		// } else {
		// 	if ($customer_info && ($this->request->get['customer_id'] != $customer_info['customer_id'])) {
		// 		$this->error['warning'] = $this->language->get('error_exists');
		// 	}
		// }

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if ($this->request->post['password'] || (!isset($this->request->get['customer_id']))) {
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		if (isset($this->request->post['address'])) {
			foreach ($this->request->post['address'] as $key => $value) {
				if ((utf8_strlen($value['firstname']) < 1) || (utf8_strlen($value['firstname']) > 32)) {
					$this->error['address'][$key]['firstname'] = $this->language->get('error_firstname');
				}

				// if ((utf8_strlen($value['lastname']) < 1) || (utf8_strlen($value['lastname']) > 32)) {
				// 	$this->error['address'][$key]['lastname'] = $this->language->get('error_lastname');
				// }

				if ((utf8_strlen($value['address_1']) < 3) || (utf8_strlen($value['address_1']) > 128)) {
					$this->error['address'][$key]['address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen($value['city']) < 2) || (utf8_strlen($value['city']) > 128)) {
					$this->error['address'][$key]['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($value['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($value['postcode']) < 2 || utf8_strlen($value['postcode']) > 10)) {
					$this->error['address'][$key]['postcode'] = $this->language->get('error_postcode');
				}

				if ($value['country_id'] == '') {
					$this->error['address'][$key]['country'] = $this->language->get('error_country');
				}

				if (!isset($value['zone_id']) || $value['zone_id'] == '') {
					$this->error['address'][$key]['zone'] = $this->language->get('error_zone');
				}

			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		// if (!$this->user->hasPermission('modify', 'seller/account-customer')) {
		// 	$this->error['warning'] = $this->language->get('error_permission');
		// }

		return !$this->error;
	}

	protected function validateUnlock() {
		if (!$this->user->hasPermission('modify', 'seller/account-customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateHistory() {
		if (!$this->user->hasPermission('modify', 'seller/account-customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->post['comment']) || utf8_strlen($this->request->post['comment']) < 1) {
			$this->error['warning'] = $this->language->get('error_comment');
		}

		return !$this->error;
	}

	public function login() {
		$json = array();

		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		$this->load->model('seller/account-customer');

		$seller_stores = array_column($this->MsLoader->MsProduct->getSellerStores($this->customer->getId()) , 'store_id');
		$seler_store_id = implode(',', $seller_stores);

		$customer_info = $this->model_seller_customer->getCustomer($customer_id);

		if ($customer_info) {
			$token = md5(mt_rand());

			$this->model_seller_customer->editToken($customer_id, $token);

			if (isset($this->request->get['store_id'])) {
				$store_id = $this->request->get['store_id'];
			} else {
				$store_id = 0;
			}

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($store_id);

			if ($store_info) {
				$this->response->redirect($store_info['url'] . 'index.php?route=account/login&token=' . $token);
			} else {
				$this->response->redirect(HTTP_CATALOG . 'index.php?route=account/login&token=' . $token);
			}
		} else {
			$this->load->language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_not_found'] = $this->language->get('text_not_found');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['header'] = $this->load->controller('common/seller_header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found.tpl', $data));
		}
	}

	public function history() {
		$this->load->language('seller/customer');

		$this->load->model('seller/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateHistory()) {
			$this->model_seller_customer->addHistory($this->request->get['customer_id'], $this->request->post['comment']);

			$data['success'] = $this->language->get('text_success');
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_comment'] = $this->language->get('column_comment');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->model_seller_customer->getHistories($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'comment'     => $result['comment'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_seller_customer->getTotalHistories($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('seller/customer/history', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		// $this->response->setOutput($this->load->view('seller/customer_history.tpl', $data));

		list($template) = $this->MsLoader->MsHelper->loadTemplate('customer_history');

		$this->response->setOutput($this->load->view($template, $data));
	}

	public function transaction() {
		$this->load->language('seller/customer');

		$this->load->model('seller/account-customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'seller/customer')) {
			$this->model_seller_customer->addTransaction($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['amount']);

			$data['success'] = $this->language->get('text_success');
		} else {
			$data['success'] = '';
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'seller/customer')) {
			$data['error_warning'] = $this->language->get('error_permission');
		} else {
			$data['error_warning'] = '';
		}

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_balance'] = $this->language->get('text_balance');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_amount'] = $this->language->get('column_amount');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['transactions'] = array();

		$results = $this->model_seller_customer->getTransactions($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->currency->format($this->model_seller_customer->getTransactionTotal($this->request->get['customer_id']), $this->config->get('config_currency'));

		$transaction_total = $this->model_seller_customer->getTotalTransactions($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('seller/account-customer/transaction', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($transaction_total - 10)) ? $transaction_total : ((($page - 1) * 10) + 10), $transaction_total, ceil($transaction_total / 10));

		$this->response->setOutput($this->load->view('seller/customer_transaction.tpl', $data));
	}

	public function reward() {
		$this->load->language('seller/customer');

		$this->load->model('seller/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'seller/account-customer')) {
			$this->model_seller_customer->addReward($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['points']);

			$data['success'] = $this->language->get('text_success');
		} else {
			$data['success'] = '';
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'seller/account-customer')) {
			$data['error_warning'] = $this->language->get('error_permission');
		} else {
			$data['error_warning'] = '';
		}

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_balance'] = $this->language->get('text_balance');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_points'] = $this->language->get('column_points');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['rewards'] = array();

		$results = $this->model_seller_customer->getRewards($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['rewards'][] = array(
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->model_seller_customer->getRewardTotal($this->request->get['customer_id']);

		$reward_total = $this->model_seller_customer->getTotalRewards($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $reward_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('seller/customer/reward', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($reward_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($reward_total - 10)) ? $reward_total : ((($page - 1) * 10) + 10), $reward_total, ceil($reward_total / 10));

		// $this->response->setOutput($this->load->view('seller/customer_reward.tpl', $data));

		list($template) = $this->MsLoader->MsHelper->loadTemplate('customer_history');

		$this->response->setOutput($this->load->view($template, $data));



	}

	public function autocomplete() {
		$json = array();
        //echo "dfs"; die;
		if ( isset($this->request->get['filter_name']) || isset($this->request->get['filter_email']) || isset($this->request->get['filter_telephone']) || isset($this->request->get['filter_all'])) {

            if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

            if (isset($this->request->get['filter_telephone'])) {
				$filter_telephone = $this->request->get['filter_telephone'];
			} else {
				$filter_telephone = '';
			}

			if (isset($this->request->get['filter_is_dropshipper'])) {
				$filter_is_dropshipper = $this->request->get['filter_is_dropshipper'];
			} else {
				$filter_is_dropshipper = 0;
			}

			if($this->request->get['filter_all']){
				$filter_name = $this->request->get['filter_all'];
				$filter_telephone = $this->request->get['filter_all'];
				$filter_email = $this->request->get['filter_all'];
			}

			$this->load->model('seller/customer');

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_email' => $filter_email,
                'filter_telephone' => $filter_telephone,
                'filter_is_dropshipper' => $filter_is_dropshipper,
				'start'        => 0,
				'limit'        => 50
			);
			if($this->request->get['filter_all']) {
				$results = $this->model_seller_customer->getCustomersByNameMobileEmail($filter_data);
			}else{
				$results = $this->model_seller_customer->getCustomers($filter_data);
			}
			foreach ($results as $result) {
				$json[] = array(
					'customer_id'       => $result['customer_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email'],
					'is_dropshipper'	=> $result['is_dropshipper'],
					'telephone'         => $result['telephone'],
					'address'           => $this->model_seller_customer->getAddresses($result['customer_id'])
				);
			}
		}
      // echo "<pre>"; print_r($json[0]['address']); die;
		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function address() {
		$json = array();

		if (!empty($this->request->get['address_id'])) {
			$this->load->model('seller/customer');

			$json = $this->model_seller_customer->getAddress($this->request->get['address_id']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	// update Credit limit consumed value by ajax by vikas (15-07-2016)
	public function updateCreditLimitConsumed(){
		$customer_id = $this->request->post['customer_id'];

		$this->load->model('seller/customer');
		$this->model_seller_customer->updateCreditLimitConsumed($customer_id);
	}
}
