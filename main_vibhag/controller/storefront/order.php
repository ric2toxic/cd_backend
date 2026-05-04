<?php
class ControllerStorefrontOrder extends Controller {

	public function index() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

		$this->getList();
	}

	public function delete() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

		unset($this->session->data['cookie']);

		if (isset($this->request->get['order_id']) && $this->validate()) {
			// API
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
					$this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
				} else {
					$response = json_decode($json, true);

					if (isset($response['cookie'])) {
						$this->session->data['cookie'] = $response['cookie'];
					}

					curl_close($curl);
				}
			}
		}

		if (isset($this->session->data['cookie'])) {
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
			curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/order/delete&order_id=' . $this->request->get['order_id']);
			curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

			$json = curl_exec($curl);

			if (!$json) {
				$this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
			} else {
				$response = json_decode($json, true);

				curl_close($curl);

				if (isset($response['error'])) {
					$this->error['warning'] = $response['error'];
				}
			}
		}

		if (isset($response['error'])) {
			$this->error['warning'] = $response['error'];
		}

		if (isset($response['success'])) {
			$this->session->data['success'] = $response['success'];

			$url = '';

			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
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

			if (isset($this->request->get['filter_store_list'])) {
				$url .= '&filter_store_list=' . $this->request->get['filter_store_list'];
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

			$this->response->redirect($this->url->link('storefront/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('sale/order', $data);

	    $this->load->model('sale/customer');
	    $this->load->model('report/customer');

        // Form Validation

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

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = null;
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

		if (isset($this->request->get['filter_store_list'])) {
			$filter_store_list = $this->request->get['filter_store_list'];
		} else {
			$filter_store_list = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['filter_total_low'])) {
			$filter_total_low = $this->request->get['filter_total_low'];
		} else {
			$filter_total_low = null;
		}

		if (isset($this->request->get['filter_total_high'])) {
			$filter_total_high = $this->request->get['filter_total_high'];
		} else {
			$filter_total_high = null;
		}

 		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
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


        // General URL Generation (without sort or page)
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

		if (isset($this->request->get['filter_store_list'])) {
			$url .= '&filter_store_list=' . $this->request->get['filter_store_list'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['filter_total_low'])) {
			$url .= '&filter_total_low=' . $this->request->get['filter_total_low'];
		}

		if (isset($this->request->get['filter_total_high'])) {
			$url .= '&filter_total_high=' . $this->request->get['filter_total_high'];
		}


        // URL for General links to ensure we reach same settings again on the list page
        $general_url = $url;
		if (isset($this->request->get['sort'])) {
			$general_url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$general_url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$general_url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
		);

		$data['orders'] = array();
        $filter_data = array(
		    'filter_order_id'      => $filter_order_id,
            'filter_order_no'      => $filter_order_no,
            'filter_customer'	   => $filter_customer,
            'filter_company'	   => $filter_company,
            'filter_city'          => $filter_city,
            'filter_order_status'  => $filter_order_status,
            'filter_date_added'    => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'filter_total_low'	   => $filter_total_low,
            'filter_total_high'    => $filter_total_high,
            'sort'                 => $sort,
            'order'                => $order,
            'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                => $this->config->get('config_limit_admin')
		);

		if ( isset($filter_store_list) and $filter_store_list == 'all' ) {}
        else if (!empty($filter_store_list)) { // In rest all cases we set

            $filter_data['filter_store_list'] = $filter_store_list;
		}

		// $order_total = $this->model_sale_order->getTotalOrders($filter_data);
		
		$data_result   = $this->model_sale_order->getOrders($filter_data);
        $orders        = $data_result['data'] ?? array();
        $order_total   = $data_result['row_count'] ?? 0;

		$results = array();	
		$customer_ids = array();
		
		if( $orders ) {
			$selector = array(
							'order' => array('select' => array(
																'order_no', 'date_added', 'firstname', 'lastname',
																'customer_id', 'shipping_company', 'shipping_city', 
																'total', 'comment','currency_code','currency_value',
																'store_name', 'payment_code','comment', 'date_modified'
															))
						);

			foreach( $orders as $order_row ) {
				$results[$order_row['order_id']] = OrderInfo::getOrderInfo($this->db, $order_row['order_id'],'',$selector);
				$customer_ids[] = $results[$order_row['order_id']]['order']['customer_id'];
			}			
		}

		$customer_ids = array_unique($customer_ids);
		// Finding all the related id(s) and its duplicate/master marking status
        $duplicate_master_ids = $this->model_sale_customer->getAllMasterDuplicateCustomerIds($customer_ids);
        // all customer id of master id
        $all_customer_ids = array_column($duplicate_master_ids, 'all_related_ids');

        // Life Time Value show on order list page
        $ltvTotalOrders         = array();
        if(!empty($all_customer_ids)){
            $ltvTotalOrders  = $this->model_report_customer->getLifeTimeOrdersCountOfCustomers($all_customer_ids);
        }

 		foreach ($results as $result) {

 			$all_related_ids = $duplicate_master_ids[$result['order']['customer_id']]['all_related_ids'];
        	$all_related_ids_array = explode(',',$all_related_ids);            

        	$getLtvTotalOrders  = 0;
            if(!empty($all_related_ids_array)){
                foreach ($all_related_ids_array as $cust_id) {
                    $getLtvTotalOrders += !empty($ltvTotalOrders[$cust_id]['total_orders']) ? $ltvTotalOrders[$cust_id]['total_orders'] : 0 ;
                }
            }

	 		//foreach ($results as $order_id => $result) {

	            $advance_collected = $this->model_sale_order->getAdvanceCollected($result['order']['order_id']);

	            if ($advance_collected->rows) {
	                $advance_collected_amt = $this->currency->format(abs((float)($advance_collected->row['value'])),
	                                                             $result['order']['currency_code'], $result['order']['currency_value']);
	                // advace amount without currency format
	                $advance_amount = abs((float)($advance_collected->row['value']));

	            } else {
	                $advance_collected_amt = false;
	            }

				$data['orders'][] = array(
					'order_id'      => $result['order']['order_id'],
	                'order_no'      => $result['order']['order_no'],
					'customer'      => $result['order']['firstname'] . ' '. $result['order']['lastname'],
					'customer_id'   => $result['order']['customer_id'],
	                'store_name'    => $result['order']['store_name'],
	                'company'       => $result['order']['shipping_company'],
	                'city'          => $result['order']['shipping_city'],
	                'payment_mode'  => ($result['order']['payment_code']=='cod') ? 'C' : 'P',
	                'comment'       => $result['order']['comment'],
					'total'         => $this->currency->format($result['order']['total'], $result['order']['currency_code'], $result['order']['currency_value']),
					'date_added'    => date($data['date_format_short'], strtotime($result['order']['date_added'])),
					'date_modified' => date($data['date_format_short'], strtotime($result['order']['date_modified'])),
					//'view'          => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $order_id . $general_url, 'SSL'),
					//'edit'          => $this->url->link('sale/order/edit', 'token=' . $this->session->data['token'] . '&order_id=' . $order_id . $general_url, 'SSL'),
					//'delete'        => $this->url->link('storefront/order/delete', 'token=' . $this->session->data['token'] . '&order_id=' . $order_id . $general_url, 'SSL'),
	                'advance'       => $advance_collected_amt,
	                'advance_amount'=> $advance_amount,
					'count_order'	=> $getLtvTotalOrders,
					'customer_link' => $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['order']['customer_id'], 'SSL')
				);
			}
		//}

		$data['entry_return_id'] = $this->language->get('entry_return_id');

		$data['button_detail_invoice_print'] = $this->language->get('button_invoice_print');


		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

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


        // URL For sorting
        $sort_url = $url;
		if ($order == 'ASC') {
			$sort_url .= '&order=DESC';
		} else {
			$sort_url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$sort_url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $sort_url, 'SSL');
		$data['sort_customer'] = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . '&sort=customer' . $sort_url, 'SSL');
        $data['sort_company'] = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . '&sort=o.shipping_company' . $sort_url, 'SSL');
        $data['sort_city'] = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . '&sort=o.shipping_city' . $sort_url, 'SSL');
		$data['sort_status'] = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . '&sort=status' . $sort_url, 'SSL');
		$data['sort_total'] = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . '&sort=o.total' . $sort_url, 'SSL');
		$data['sort_date_added'] = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $sort_url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $sort_url, 'SSL');


        // URL for pagination
        $pagination_url = $url;
		if (isset($this->request->get['sort'])) {
			$pagination_url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$pagination_url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('storefront/order', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id']      = $filter_order_id;
        $data['filter_order_no']      = $filter_order_no;
		$data['filter_customer']      = $filter_customer;
        $data['filter_company']       = $filter_company;
        $data['filter_city']          = $filter_city;
		$data['filter_order_status']  = $filter_order_status;
		$data['filter_date_added']    = $filter_date_added;
		$data['filter_store_list']    = $filter_store_list;
		$data['filter_date_modified'] = $filter_date_modified;
		$data['filter_total_low']     = $filter_total_low;
		$data['filter_total_high']    = $filter_total_high;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('storefront/order_list.tpl', $data));
	}

	protected function validate() {
		if ( !$this->user->hasPermission('modify', 'sale/order')
            and !$this->user->hasPermission('modify', 'storefront/order') ) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
