<?php
class ControllerAccountsPendingOrder extends Controller {
	private $error = array();
	private $_order_info = array();
	private $_delivered_state_id = 15;
	private $_failed_state_id = 8;
	public function index() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

		$this->getList();
	}

	protected function getList() {
		// load All models
		$this->load->model('sale/customer');

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

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

        if (isset($this->request->get['filter_low'])) {
			$filter_low = $this->request->get['filter_low'];
		} else {
			$filter_low = null;
		}

		if (isset($this->request->get['filter_high'])) {
			$filter_high = $this->request->get['filter_high'];
		} else {
			$filter_high = null;
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

        // General URL (without sort or page)
		$url = '';

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

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

        if (isset($this->request->get['filter_low'])) {
			$url .= '&filter_low=' . $this->request->get['filter_low'];
		}

		if (isset($this->request->get['filter_high'])) {
			$url .= '&filter_high=' . $this->request->get['filter_high'];
		}

   		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
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


	    $data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('sale/order', $data);


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_pending_orders'],
			'href' => $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
		);

		$data['orders'] = array();

		$filter_data = array(
            'filter_order_no'      => $filter_order_no, 
			'filter_customer'	   => $filter_customer,
            'filter_company'	   => $filter_company,
            'filter_city'          => $filter_city,
			'filter_order_status'  => 1,
			'filter_low'	       => $filter_low,
            'filter_high'          => $filter_high,
			'filter_date_added'    => $filter_date_added,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);
        
        //$order_total = $this->model_sale_order->getTotalOrders($filter_data);
			
		$data_result   = $this->model_sale_order->getOrders($filter_data);
        $orders        = $data_result['data'] ?? array();
        $order_total   = $data_result['row_count'] ?? 0;

        $results = array();
        if ($orders) {
            
            $selector = array('order' => array('select' => array('order_no','customer_id','firstname','lastname','telephone',
                                                                 'shipping_company','shipping_city','shipping_postcode',
                                                                 'payment_code','code_version','date_added','sales_staff_id',
                                                                 'total','currency_code','currency_value','comment','store_name')), 
                              'suborder' => array('select' => array('order_status_id', 'shipping_method','total')), 
                              'order_history' => array() 
                             );

            foreach ($orders as $order_row) {
                $results[$order_row['order_id']] = OrderInfo::getOrderInfo($this->db, $order_row['order_id'], '', $selector);
                
            }
        }
        
		//get all order status
		$this->load->model('localisation/order_status');
		$order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();
		$order_statuses = array();
		foreach($order_statuses_qry as $order_status_data){
			$order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
		}

		foreach($results as $key => $result){
			//echo "<pre>";print_r($failed_date);die;
			/*$gati_pincode_status = false;
            $pincode_info = $this->model_sale_order->getGatiPincodeInfo($result['order']['shipping_postcode']);

            if ( $pincode_info ) {
                if ( ($result['order']['payment_code']=='cod' and $pincode_info['cod']==1) or !($result['order']['payment_code']=='cod') ) {
                    $gati_pincode_status = $pincode_info['serviceability'];
                }
            }*/

            $payment_history  = $this->model_sale_order->getOrderPaymentHistory($result['order']['order_id']);

			$data['orders'][$key]['order'] = $result['order'];


			// additional info in order
			$data['orders'][$key]['order']['customer'] 	= $result['order']['firstname'] .' ' .$result['order']['lastname'];
			$data['orders'][$key]['order']['payment_mode'] 	= (trim(strtolower($result['order']['payment_code']))=='cod') ? 'C' : 'P';
			//$data['orders'][$key]['order']['gati_pincode_status'] = $gati_pincode_status;
			$data['orders'][$key]['order']['total_amount'] 	= $result['order']['total'];
			$data['orders'][$key]['order']['payment_history']	= $payment_history;
			
            // Currency formatting on order total
            $data['orders'][$key]['order']['total'] = $this->currency->format($data['orders'][$key]['order']['total'], 
                                                                              $result['order']['currency_code'],
                                                                              $result['order']['currency_value'],
                                                                              true);

			$data['orders'][$key]['status'] = array();
				$data['orders'][$key]['order_return_url'] = $this->url->link('sale/return/order', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'], 'SSL');

			// additional info in suborder
			$all_suborder_status = array();
			
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

		$data['sort_order'] = $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $sort_url, 'SSL');
		$data['sort_customer'] = $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . '&sort=customer' . $sort_url, 'SSL');
        $data['sort_company'] = $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . '&sort=o.shipping_company' . $sort_url, 'SSL');
        $data['sort_city'] = $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . '&sort=o.shipping_city' . $sort_url, 'SSL');
		$data['sort_total'] = $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . '&sort=o.total' . $sort_url, 'SSL');
		$data['sort_date_added'] = $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $sort_url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $sort_url, 'SSL');


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
		$pagination->url = $this->url->link('accounts/pendingorder', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_order_no']     = $filter_order_no;
		$data['filter_customer']     = $filter_customer;
        $data['filter_company']      = $filter_company;
        $data['filter_city']         = $filter_city;
		$data['filter_date_added']   = $filter_date_added;
        $data['filter_low']          = $filter_low;
		$data['filter_high']         = $filter_high;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('accounts/pendingorder.tpl', $data));
	}
}

