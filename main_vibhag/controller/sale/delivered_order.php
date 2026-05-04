<?php
class ControllerSaleDeliveredOrder extends Controller {
	private $error = array();

	public function index() {
		//$this->load->language('sale/Order');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('sale/order', $data);

		$this->document->setTitle($data['heading_delivered_title']);

		$this->load->model('sale/order');
		$this->load->model('sale/customer');
		//$data = $this->model_sale_customer->getCustomers();
		
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}
        
        if (isset($this->request->get['filter_order_no'])) {
			$filter_order_no = $this->request->get['filter_order_no'];
		} else {
			$filter_order_no = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}
        
        if (isset($this->request->get['filter_city'])) {
			$filter_city = $this->request->get['filter_city'];
		} else {
			$filter_city = null;
		}
        
        if (isset($this->request->get['filter_company'])) {
			$filter_company = $this->request->get['filter_company'];
		} else {
			$filter_company = null;
		}


		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_no';
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

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
        
        if (isset($this->request->get['filter_order_no'])) {
			$url .= '&filter_order_no=' . $this->request->get['filter_order_no'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_company'])) {
			$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'text' => $data['heading_delivered_title'],
			'href' => $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['orders'] = array();

		$filter_data = array(
			'filter_order_id'      => $filter_order_id,
            'filter_order_no'      => $filter_order_no,
			'filter_customer'	   => $filter_customer,
            'filter_company'	   => $filter_company,
            'filter_city'          =>  $filter_city,
			'filter_total'         => $filter_total,
			'filter_date_added'    => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_sale_order->getTotalOrders($filter_data);
		//echo "<pre>"; print_r($order_total); die;


		$results = $this->model_sale_order->delivered_order_customer($filter_data);

		    

 		foreach ($results as $result) {
        //echo "<pre>"; print_r($result); die;
            $lastUpdateTime = $this->model_sale_order->getHistoryTime($result['order_id'], $result['order_status_id']);

        	$res = $this->model_sale_order->getOrder($result['order_id']);

		    $this->load->model('sale/customer');
		    $result['is_dropshipper'] = $this->model_sale_customer->getisdropshipper($result['customer_id']);
    
            $advance_collected = $this->model_sale_order->getAdvanceCollected($result['order_id']);
            
            if ($advance_collected->rows) {
                $advance_collected = $this->currency->format(abs((float)($advance_collected->row['value'])), $result['currency_code'], $result['currency_value']);
                
            } else {
                $advance_collected = false;
            }

			$data['orders'][] = array(
				'order_id'      => $result['order_id'],
                'order_no'      => $result['order_no'],
				'customer'      => $result['customer'],
                'company'       => $result['shipping_company'],
                'city'              => $result['shipping_city'],
				'status'        => $result['status'],
				'is_dropshipper'=> $result['is_dropshipper'],
                'payment_mode' => ($result['payment_code']=='cod') ? 'C' : 'P',
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($data['date_format_short'], strtotime($result['date_added'])),
				'shipping_code' => $result['shipping_code'],
				'view'          => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
				'Client_Dispute' => $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
				'complete'      => $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
                'advance'       => $advance_collected
			);
		}


		$data['entry_return_id'] = $this->language->get('entry_return_id');

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

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
        
        if (isset($this->request->get['filter_order_no'])) {
			$url .= '&filter_order_no=' . $this->request->get['filter_order_no'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_company'])) {
			$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}


		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
		$data['sort_customer'] = $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
        $data['sort_company'] = $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&sort=o.shipping_company' . $url, 'SSL');
        $data['sort_city'] = $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&sort=o.shipping_city' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_total'] = $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
        
        if (isset($this->request->get['filter_order_no'])) {
			$url .= '&filter_order_no=' . $this->request->get['filter_order_no'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_company'])) {
			$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . urlencode(html_entity_decode($this->request->get['filter_city'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/delivered_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
        $data['filter_order_no'] = $filter_order_no;
		$data['filter_customer'] = $filter_customer;
        $data['filter_company'] = $filter_company;
        $data['filter_city'] = $filter_city;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;
 
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');



		// Unset any past sessions this page date_added for the api to work.
		unset($this->session->data['cookie']);

		// Set up the API session
		if ($this->user->hasPermission('modify', 'sale/order')) {
			$this->load->model('user/api');

			$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

			if ($api_info) {
				$curl = curl_init();

				// Set SSL if required
				if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
					curl_setopt($curl, CURLOPT_PORT, 443);
				}

				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLINFO_HEADER_OUT, true);
				curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

				$json = curl_exec($curl);

				if (!$json) {
					$data['error_warning'] = sprintf($data['error_curl'], curl_error($curl), curl_errno($curl));
				} else {
					$response = json_decode($json, true);
				}

				if (isset($response['cookie'])) {
					$this->session->data['cookie'] = $response['cookie'];
				}
			}
		}

		if (isset($response['cookie'])) {
			$this->session->data['cookie'] = $response['cookie'];
		} else {
			$data['error_warning'] = $this->language->get('error_permission');
		}

		$this->response->setOutput($this->load->view('sale/delivered_order.tpl', $data));
	}
}
