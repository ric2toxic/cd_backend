<?php
class ControllerOperationsOrder extends Controller {
	private $error = array();
	private $_order_info = array();
	private $_delivered_state_id = 15;
	private $_failed_state_id = 8;

    private $_order_pickup  = array(9,16);
    private $_order_transit   = array(4,13,14);
    private $_order_tantative = array(1);


    private $_encrypt_method = "AES-256-CBC";
    private $_secret_key = 'abc123093';

	public function index() {

		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

		$this->getList();
	}

	protected function getList() {
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', 'haha123'), 0, 16);
		// load All models
		$this->load->model('sale/customer');

		if (isset($this->request->get['filter_customer_id'])) {
			$filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = null;
		}

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

		if (isset($this->request->get['filter_pickup_city_code'])) {
			$filter_pickup_city_code = $this->request->get['filter_pickup_city_code'];
		} else {
			$filter_pickup_city_code = null;
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

        // brach code is belong to operations
        if(!empty($this->session->data['branch_code'])){
            $branch_code = $this->session->data['branch_code'];
        } else {
            $branch_code = '';
        }

        if(!empty($this->request->get['order_pickup'])){
            $ord_pckup_decode = base64_decode($this->request->get['order_pickup']);
            $order_pickup = openssl_decrypt($ord_pckup_decode, $this->_encrypt_method, $this->_secret_key, 0, $iv);
        } else {
            $order_pickup = '';
        }

        if(!empty($this->request->get['order_transit'])){
            $ord_trnsit_decode = base64_decode($this->request->get['order_transit']);
            $order_transit = openssl_decrypt($ord_trnsit_decode, $this->_encrypt_method, $this->_secret_key, 0, $iv);
        } else {
            $order_transit = '';
        }

        if(!empty($this->request->get['order_tantative'])){
            $ord_tntive_decode = base64_decode($this->request->get['order_tantative']);
            $order_tantative = openssl_decrypt($ord_tntive_decode, $this->_encrypt_method, $this->_secret_key, 0, $iv);
        } else {
            $order_tantative = '';
        }


        // General URL (without sort or page)
		$url = '';

        if (isset($this->request->get['order_pickup'])) {
            $url .= '&order_pickup=' . $this->request->get['order_pickup'];
        }

        if (isset($this->request->get['order_transit'])) {
            $url .= '&order_transit=' . $this->request->get['order_transit'];
        }

        if (isset($this->request->get['order_tantative'])) {
            $url .= '&order_tantative=' . $this->request->get['order_tantative'];
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
	    $this->load->autoLoadLanguage('operations/order', $data);


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
		);

		$data['orders'] = array();

		$filter_data = array(
            'branch_code'     => $branch_code,
            'order_pickup'    => $order_pickup,
            'order_transit'   => $order_transit,
            'order_tantative' => $order_tantative,        
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		// Stores
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

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
                $suborder_id = '';
                if( !empty($branch_code) ){
                    $suborder_id = $order_row['order_no'].'-'.$branch_code;    
                }
                
                $results[$order_row['order_id']] = OrderInfo::getOrderInfo($this->db, $order_row['order_id'], $suborder_id, $selector);
                $results[$order_row['order_id']]['order']['operations_status'] = $order_row['operations_status'];
            }
        }

		//get all order status
		$this->load->model('localisation/order_status');
		$order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

		$order_statuses = array();
		foreach($order_statuses_qry as $order_status_data){
			$order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
		}
        $order_statuses[0] = 'Missing Order';

        $data['sales_staff_list'] = $this->model_sale_order->getSalesStaffList();

        $order_advance_total = array();
        $order_advance_breakup = array();
        $advance_locked_status = array();

		foreach($results as $key => $result){

            if( $result['order']['code_version'] != '1.0'){
                $order_advance_total[$result['order']['order_id']] = OrderInfo::getTotalAdvance( $this->db , $result['order']['order_id'] );
                $order_advance_breakup[$result['order']['order_id']] = OrderInfo::getAdvanceBreakup( $this->db , $result['order']['order_id'] );
                foreach( $order_advance_breakup[$result['order']['order_id']] as $key => $advance_breakup ){
                    if( $advance_breakup['invoice_no'] > 0 ){
                        $advance_locked_status[$key] = true;
                    }
                    else{
                        $advance_locked_status[$key] = false;
                    }
                }

            }

			$delivered_date = $this->model_sale_order->getHistoryTime(
																		$result['order']['order_id'],
																		$this->_delivered_state_id
																	);
			$failed_date = $this->model_sale_order->getHistoryTime(
																	$result['order']['order_id'],
																	$this->_failed_state_id
																);
			$gati_pincode_status = false;
            $pincode_info = $this->model_sale_order->getGatiPincodeInfo($result['order']['shipping_postcode']);
            if ( $pincode_info ) {
                if ( ($result['order']['payment_code']=='cod' and $pincode_info['cod']==1) or !($result['order']['payment_code']=='cod') ) {
                    $gati_pincode_status = $pincode_info['serviceability'];
                }
            }

            $payment_history  = $this->model_sale_order->getOrderPaymentHistory($result['order']['order_id']);

			$data['orders'][$key]['order'] = $result['order'];
			$data['orders'][$key]['suborder'] = $result['suborder'];

			$payment_code = trim(strtolower($result['order']['payment_code']));
			// additional info in order
			$data['orders'][$key]['order']['customer'] 	= $result['order']['firstname'] .' ' .$result['order']['lastname'];
			$data['orders'][$key]['order']['payment_mode'] 	= (in_array($payment_code, CREDIT_PAYMENT_CODES) ? $payment_code : ($payment_code == 'cod' ? 'C' : 'P') );
			$data['orders'][$key]['order']['gati_pincode_status'] = $gati_pincode_status;
			$data['orders'][$key]['order']['total_amount'] 	= $result['order']['total'];
			$data['orders'][$key]['order']['payment_history']	= $payment_history;
			$data['orders'][$key]['order']['app_installed'] = $this->model_sale_order->getGcmRegId($result['order']['customer_id']);
			$data['orders'][$key]['order']['total_order_link'] = $this->url->link('sale/order',
																				  'token=' . $this->session->data['token'] .
																				  '&filter_customer_id=' . $result['order']['customer_id'] .
                                                                                  '&page=1',
																				  'SSL');
			$data['orders'][$key]['order']['customer_link'] = $this->url->link('sale/customer/edit',
																			   'token=' . $this->session->data['token'] .
																			   '&customer_id=' . $result['order']['customer_id'],
																			   'SSL');
            // Currency formatting on order total
            $data['orders'][$key]['order']['total'] = $this->currency->format($data['orders'][$key]['order']['total'],
                                                                              $result['order']['currency_code'],
                                                                              $result['order']['currency_value'],
                                                                              true);

			$data['orders'][$key]['status'] = array();
			$data['orders'][$key]['show_return_btn'] = !empty($delivered_date) || !empty($failed_date) ? true : false;
			$data['orders'][$key]['order_return_url'] = $this->url->link('sale/return/order', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'], 'SSL');
			// additional info in suborder
			$all_suborder_status = array();
            // for don't disptach alert
            $order_status = array();
			foreach ($result['suborder'] as $key_suborder_id => $suborder_data) {
                $order_status[] = $suborder_data['order_status_id'];
				$data['orders'][$key]['suborder'][$key_suborder_id]['status'] = $order_statuses[$suborder_data['order_status_id']] ;
				$data['orders'][$key]['status'][$key_suborder_id]['status'] = $key_suborder_id . " : " .  $order_statuses[$suborder_data['order_status_id']] ;
				$data['orders'][$key]['suborder'][$key_suborder_id]['view'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'] . '&suborder_id=' . $key_suborder_id . $general_url, 'SSL');

                // Currency formatting on suborder total
                $suborder_total = $this->currency->format($data['orders'][$key]['suborder'][$key_suborder_id]['total'],
                                                          $result['order']['currency_code'],
                                                          $result['order']['currency_value'],
                                                          true);
                $data['orders'][$key]['suborder'][$key_suborder_id]['total'] = $suborder_total;

				if (!empty($suborder_data['order_history'])) {
					$data['last_update_history'] = end($suborder_data['order_history']);
					$lastUpdateTime = end($suborder_data['order_history'])['date_added'];
				} else {
					$data['last_update_history'] = '';
					$lastUpdateTime = false;
				}

				$red_flag = false;
				$dispute_flag = $suborder_data['order_status_id'] == 6 ? true : false;

	            // If pending for long (more than 2 days)
	            if ( $suborder_data['order_status_id'] == 1 && (time() - strtotime($lastUpdateTime)) > (2*24*60*60) ) {
	                $red_flag = true;

	            } elseif ( ($suborder_data['order_status_id'] == 9 || $suborder_data['order_status_id'] == 16)
                         && (time() - strtotime($lastUpdateTime)) > (1*24*60*60) ) {
	                $red_flag = true;

	            } elseif ( ($suborder_data['order_status_id'] == 13 || $suborder_data['order_status_id'] == 14)
                         && (time() - strtotime($lastUpdateTime)) > (5*24*60*60) ) {
	                $red_flag = true;

	            } elseif ( $suborder_data['order_status_id'] == 4 && (time() - strtotime($lastUpdateTime)) > (1*24*60*60) ) {
	                $red_flag = true;
	            }

	            $data['orders'][$key]['suborder'][$key_suborder_id]['red_flag'] = $red_flag;
	            $data['orders'][$key]['suborder'][$key_suborder_id]['dispute_flag'] = $dispute_flag;
			}
		}

        $data['order_total_advance'] = json_encode($order_advance_total);
        $data['order_advance_breakup'] = json_encode($order_advance_breakup);
        $data['advance_locked_status'] = json_encode($advance_locked_status);
        $data['user'] = $this->user->getUserName();
		$data['token'] = $this->session->data['token'];

        // these actions are set on tabs
        $ord_pckup_encrypted = openssl_encrypt(implode(',', $this->_order_pickup), 
                                                           $this->_encrypt_method, 
                                                           $this->_secret_key, 
                                                           0, 
                                                           $iv);
        $ord_trnsit_encrypted = openssl_encrypt(implode(',', $this->_order_transit), 
                                                           $this->_encrypt_method, 
                                                           $this->_secret_key, 
                                                           0, 
                                                           $iv);
        $ord_tntive_encrypted = openssl_encrypt(implode(',', $this->_order_tantative), 
                                                           $this->_encrypt_method, 
                                                           $this->_secret_key, 
                                                           0, 
                                                           $iv);

        $data['order_pickup'] = $this->url->link('operations/order', 
                                                   'token=' . $this->session->data['token'] . 
                                                   '&order_pickup=' . (base64_encode($ord_pckup_encrypted)), 
                                                   'SSL');
        $data['order_transit'] = $this->url->link('operations/order',
                                                  'token=' . $this->session->data['token'] . 
                                                  '&order_transit=' . (base64_encode($ord_trnsit_encrypted)),
                                                  'SSL');
        $data['order_tantative'] = $this->url->link('operations/order',
                                                    'token=' . $this->session->data['token'] . 
                                                    '&order_tantative=' . (base64_encode($ord_tntive_encrypted)), 
                                                    'SSL');



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

		$data['sort_order'] = $this->url->link('operations/order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $sort_url, 'SSL');
		$data['sort_customer'] = $this->url->link('operations/order', 'token=' . $this->session->data['token'] . '&sort=customer' . $sort_url, 'SSL');
        $data['sort_company'] = $this->url->link('operations/order', 'token=' . $this->session->data['token'] . '&sort=o.shipping_company' . $sort_url, 'SSL');
        $data['sort_city'] = $this->url->link('operations/order', 'token=' . $this->session->data['token'] . '&sort=o.shipping_city' . $sort_url, 'SSL');
		$data['sort_total'] = $this->url->link('operations/order', 'token=' . $this->session->data['token'] . '&sort=o.total' . $sort_url, 'SSL');
		$data['sort_date_added'] = $this->url->link('operations/order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $sort_url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('operations/order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $sort_url, 'SSL');


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
		$pagination->url = $this->url->link('operations/order', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($data['text_pagination'], ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id']     = $filter_order_id;
        $data['filter_order_no']     = $filter_order_no;
		$data['filter_customer']     = $filter_customer;
        $data['filter_company']      = $filter_company;
        $data['filter_city']         = $filter_city;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_date_added']   = $filter_date_added;
		$data['filter_date_modified']= $filter_date_modified;
        $data['filter_total_low']    = $filter_total_low;
		$data['filter_total_high']   = $filter_total_high;
		$data['filter_pickup_city_code']= $filter_pickup_city_code;
        $data['tab_order_pickup']       = $order_pickup;
        $data['tab_order_transit']      = $order_transit;
        $data['tab_order_tantative']    = $order_tantative;
    

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('operations/order_list.tpl', $data));
	}
}
