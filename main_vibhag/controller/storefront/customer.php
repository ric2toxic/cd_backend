<?php
class ControllerStorefrontCustomer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/customer');

		$this->getList();
	}

	public function delete() {
		$this->load->language('sale/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/customer');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $customer_id) {
				$this->model_sale_customer->deleteCustomer($customer_id);
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

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_store_list'])) {
				$url .= '&filter_store_list=' . $this->request->get['filter_store_list'];
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

			$this->response->redirect($this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('sale/customer', $data);

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

		if (isset($this->request->get['filter_date_added'])) {
		 $filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_store_list'])) {
			$filter_store_list = $this->request->get['filter_store_list'];
		} else {
			$filter_store_list = '';
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
			$sort = 'c.last_cart_modified';
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

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_store_list'])) {
			$url .= '&filter_store_list=' . $this->request->get['filter_store_list'];
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
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('sale/customer/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('storefront/customer/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['customers'] = array();

		if(isset($filter_store_list) && ($filter_store_list == 'all') || !isset($filter_store_list)){
			$filter_data = array(
					'filter_name'              => $filter_name,
					'filter_email'             => $filter_email,
					'filter_telephone'         => $filter_telephone,
					'filter_status'            => $filter_status,
					'filter_date_added'        => $filter_date_added,
					'filter_is_dropshipper'    => $filter_is_dropshipper,
					'sort'                     => $sort,
					'order'                    => $order,
					'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
					'limit'                    => $this->config->get('config_limit_admin')
			);
		}else{
			$filter_data = array(
					'filter_name'              => $filter_name,
					'filter_email'             => $filter_email,
					'filter_telephone'         => $filter_telephone,
					'filter_status'            => $filter_status,
					'filter_date_added'        => $filter_date_added,
					'filter_store_list'        => $filter_store_list,
					'filter_is_dropshipper'    => $filter_is_dropshipper,
					'sort'                     => $sort,
					'order'                    => $order,
					'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
					'limit'                    => $this->config->get('config_limit_admin')
			);
		}

		$customer_total = $this->model_sale_customer->getTotalCustomers($filter_data);

		$results = $this->model_sale_customer->getCustomers($filter_data);

		foreach ($results as $result) {

			$login_info = $this->model_sale_customer->getTotalLoginAttempts($result['email']);

			if ($login_info && $login_info['total'] >= $this->config->get('config_login_attempts')) {
				$unlock = $this->url->link('sale/customer/unlock', 'token=' . $this->session->data['token'] . '&email=' . $result['email'] . $url, 'SSL');
			} else {
				$unlock = '';
			}

			$data['customers'][] = array(
				'customer_id'    => $result['customer_id'],
				'name'           => $result['name'],
				'email'          => $result['email'],
                'telephone'      => $result['telephone'],
				'status'         => $data['text_enabled'],
				'ip'             => $result['ip'],
				'date_added'     => date($data['date_format_short'], strtotime($result['date_added'])),
                'last_login'     => '',
                'cart_items'     => $result['cart_items'],
                'last_cart_modified' => ($result['last_cart_modified'] && $result['last_cart_modified'] != '0000-00-00 00:00:00') ? date($data['datetime_format'], strtotime($result['last_cart_modified'])) : 'Never',
				'is_dropshipper' => $result['is_dropshipper'],
				'unlock'         => $unlock,
				'edit'           => $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] . $url, 'SSL'),
                'app_installed'  => ($result['ws_gcm_registration_id'] != '') ? true : false,
                'company'        => isset($result['company']) ? $result['company'] : '',
                'city'           => isset($result['city']) ? $result['city'] : ''
			);
		}


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

		if (isset($this->request->get['filter_store_list'])) {
			$url .= '&filter_store_list=' . $this->request->get['filter_store_list'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_email'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=c.email' . $url, 'SSL');
        $data['sort_telephone'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=c.telephone' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=c.status' . $url, 'SSL');
		$data['sort_is_dropshipper'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=c.is_dropshipper' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=c.date_added' . $url, 'SSL');
        $data['sort_last_login'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=last_login' . $url, 'SSL');
        $data['sort_cart_items'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=cart_items' . $url, 'SSL');
        $data['sort_last_cart_modified'] = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . '&sort=c.last_cart_modified' . $url, 'SSL');

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

		if (isset($this->request->get['filter_store_list'])) {
			$url .= '&filter_store_list=' . $this->request->get['filter_store_list'];
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
		$pagination->url = $this->url->link('storefront/customer', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
        $data['filter_telephone'] = $filter_telephone;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_store_list'] = $filter_store_list;
		$data['filter_is_dropshipper'] = $filter_is_dropshipper;

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('storefront/customer_list.tpl', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
