<?php

class ControllerOperationsNeoGrowthOrder extends Controller {

    private $error = array();
    private $_order_info = array();
    private $_delivered_state_id = 15;
    private $_failed_state_id = 8;
    private $_cancelled_state_id = 2;
    private $_processed_state_id = 9;
    private $_tentative_processed_state_id = 16;
    private $_return_states = array(2, 8, 9, 11, 15, 16);
    private $_fedex_service_types = ['STANDARD_OVERNIGHT','FEDEX_EXPRESS_SAVER'];

    public function index() {

        $this->load->language('operations/neogrowth_order');
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('sale/order');
        $this->getList();
    }

    protected function getList() {

        $this->config->set('config_limit_admin',15);
        
        $buyer_invoice = new BuyerInvoice($this);
        // load All models
        $this->load->model('sale/customer');
        $this->load->model('sale/order');

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

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_order_no'])) {
            $url .= '&filter_order_no=' . $this->request->get['filter_order_no'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_customer_id'])) {
            $url .= '&filter_customer_id=' . urlencode(html_entity_decode($this->request->get['filter_customer_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

        $this->load->autoLoadLanguage('operations/neogrowth_order', $data);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
          'text' => $data['heading_title'],
          'href' => $this->url->link('operations/neogrowth_order', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['orders'] = array();

        $filter_data = array(
            'filter_customer_id' => $filter_customer_id,
            'filter_order_id' => $filter_order_id,
            'filter_order_no' => $filter_order_no,
            'filter_customer' => $filter_customer,
            'filter_order_status' => $filter_order_status,
            'filter_date_added' => $filter_date_added,
            'filter_payment_code' => 'credit',
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        // Stores
        $this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();
        $data['zone_areas'] = $this->db->getEnumValues('oc_zone', 'zone_area');

        //$order_total = $this->model_sale_order->getTotalOrders($filter_data);
        
        $data_result   = $this->model_sale_order->getOrders($filter_data);
        $orders        = $data_result['data'] ?? array();
        $order_total   = $data_result['row_count'] ?? 0;
        
        $results = array();
        if ($orders) {
            
            $selector = array('order' => array('select' => array('order_no','customer_id','firstname','lastname','telephone','shipping_firstname', 'shipping_lastname',
                                                                 'shipping_company','shipping_city','shipping_postcode', 'shipping_zone_id',
                                                                 'payment_code','code_version','date_added',
                                                                 'total','currency_code','currency_value','comment','store_name','stock_transfer', 
                                                                 'live_currency_conversion_rate','order_from','store_voucher','self_order')),
                              'suborder' => array('select' => array('order_status_id', 'shipping_method','total','invoice_no','invoice_prefix','invoice_date','courier_partner','tracking_no' )),
                              'order_history' => array()
                             );

            foreach ($orders as $order_row) {
                $results[$order_row['order_id']] = OrderInfo::getOrderInfo($this->db, $order_row['order_id'], '', $selector);
                $results[$order_row['order_id']]['order']['operations_status'] = $order_row['operations_status'];
              
            }
        }
        
        //get all order status
        $this->load->model('localisation/order_status');
        $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

        $order_statuses = array();
        foreach ($order_statuses_qry as $order_status_data) {
            $order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
        }
        $order_statuses[0] = 'Missing Order';

        $order_advance_total = array();
        $order_advance_breakup = array();
        $advance_locked_status = array();

        foreach ($results as $key => $result) {
            $order_id = $key;
            $collect_suborder_status_id = array();
            if ($result['order']['code_version'] != '1.0') {
                $order_advance_total[$result['order']['order_id']] = OrderInfo::getTotalAdvance($this->db, $result['order']['order_id']);
                $order_advance_breakup[$result['order']['order_id']] = AdvanceVoucher::getAdvanceVouchersBySuborderGroup($this->db, $result['order']['order_id']);
            }

            $payment_history = $this->model_sale_order->getOrderPaymentHistory($result['order']['order_id']);
            $tentative_advance_history = $this->model_sale_order->getTentativeAdvanceHistory($result['order']['order_id']);
            $getOnlyTentativeAmount = $this->model_sale_order->getOnlyTentativeAmount($result['order']['order_id']);

            $amountTentativeAdvance = 0;
            $amountTentativeAdvances = $this->model_sale_order->getAmountTentativeAdvance($result['order']['order_id']);

            if (!empty($amountTentativeAdvances)) {
                if ($amountTentativeAdvances['confirm'] == 0) {
                    if (($amountTentativeAdvances['transaction_status'] == 'will_deposit' && $amountTentativeAdvances['payment_mode'] != 'cheque') || $amountTentativeAdvances['transaction_status'] == 'deposited') {
                        $amountTentativeAdvance = $amountTentativeAdvances['amount'];
                    }
                }
            }

            $data['orders'][$key]['order'] = $result['order'];
            $data['orders'][$key]['suborder'] = $result['suborder'];


            // get order processing date
            $data['orders'][$key]['order']['order_processing_date'] = $this->model_sale_order->getOrderProcessingDate($order_id);

            $data['orders'][$key]['order']['customer'] = $result['order']['firstname'] . ' ' . $result['order']['lastname'];
            $payment_code = trim(strtolower($result['order']['payment_code']));
            $data['orders'][$key]['order']['payment_mode'] = (in_array($payment_code, CREDIT_PAYMENT_CODES) ? $payment_code : ($payment_code == 'cod' ? 'C' : 'P') );
            $data['orders'][$key]['order']['total_amount'] = $result['order']['total'];
            $data['orders'][$key]['order']['payment_history'] = $payment_history;
            $data['orders'][$key]['order']['tentative_advance_history'] = $tentative_advance_history;
            $data['orders'][$key]['order']['getOnlyTentativeAmount'] = $getOnlyTentativeAmount;
            $data['orders'][$key]['order']['app_installed'] = $this->model_sale_order->getGcmRegId($result['order']['customer_id']);
            
            $data['orders'][$key]['order']['cashback_coupon_amount'] = $payment_history['cashback_coupon_amount'];
            
            $data['orders'][$key]['order']['balance_total_amount'] = (  $this->currency->format(
                                                                                                str_replace(',', '', ( $payment_history['payed_by_customer']  + 
                                                                                                  $payment_history['cashback_coupon_amount'] ) ), 
                                                                                               $result['order']['currency_code'],
                                                                                               $result['order']['live_currency_conversion_rate'],
                                                                                               false
                                                                                               )
                                                                      - 
                                                                        $this->currency->format(
                                                                                                 str_replace(',', '', $data['orders'][$key]['order']['total']), 
                                                                                                 $result['order']['currency_code'],
                                                                                                 $result['order']['currency_value'],
                                                                                                 false
                                                                                                )
                                                                     );
            // both are show on list page. ///////////////
            $data['orders'][$key]['order']['net_payment'] = $result['order']['total'] - $payment_history['cashback_coupon_amount'];
            /////////////////////////////
          
            $data['orders'][$key]['order']['customer_link'] = $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] .
                    '&customer_id=' . $result['order']['customer_id'], 'SSL');
            // Currency formatting on order total

            $data['orders'][$key]['order']['total'] = $this->currency->format($data['orders'][$key]['order']['total'],
                                                                              $result['order']['currency_code'],
                                                                              $result['order']['currency_value'],
                                                                              true);

    			$data['orders'][$key]['status'] = array();
          
          // get neo-growth transaction stats 
          $credit_payment_gateway = new CreditPayment($this);
          $neogrowth_transaction_stats = $credit_payment_gateway->getNeoGrowthStatsOfOrder($result['order']['order_no']);
          $data['orders'][$key]['order']['neogrowth_purchased_amount'] = (float)$neogrowth_transaction_stats['purchased_amount'];
          $data['orders'][$key]['order']['neogrowth_cancelled_amount'] = (float)$neogrowth_transaction_stats['cancelled_amount'];
          $data['orders'][$key]['order']['neogrowth_delivered_amount'] = (float)$neogrowth_transaction_stats['delivered_amount'];
          $data['orders'][$key]['order']['neogrowth_return_amount'] = (float)$neogrowth_transaction_stats['return_amount'];
          
          $data['orders'][$key]['order']['neogrowth_actions'] = $credit_payment_gateway->getNeogrowthActions($result['order']['order_id']);

    			// additional info in suborder
    			$all_suborder_status = array();
                // for dont disptach alert
                $order_status = array();
    			foreach ($result['suborder'] as $key_suborder_id => $suborder_data) {
                       $order_status[] = $suborder_data['order_status_id'];
                       $data['orders'][$key]['suborder'][$key_suborder_id]['status'] = $order_statuses[$suborder_data['order_status_id']] ;
    				   $data['orders'][$key]['status'][$key_suborder_id]['status'] = $key_suborder_id . " : " .  $order_statuses[$suborder_data['order_status_id']] ;
                       
                    // Currency formatting on suborder total
                    $suborder_total = $this->currency->format($data['orders'][$key]['suborder'][$key_suborder_id]['total'], $result['order']['currency_code'], $result['order']['currency_value'], true);
                    $data['orders'][$key]['suborder'][$key_suborder_id]['total'] = $suborder_total;
                    if ($suborder_data['invoice_no'] > 0) {
                        $advance_locked_status[$key_suborder_id] = true;
                    } else {
                        $advance_locked_status[$key_suborder_id] = false;
                    }

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
                    if ($suborder_data['order_status_id'] == 1 && (time() - strtotime($lastUpdateTime)) > (2 * 24 * 60 * 60)) {
                       $red_flag = true;
                    } elseif (($suborder_data['order_status_id'] == 9 || $suborder_data['order_status_id'] == 16) && (time() - strtotime($lastUpdateTime)) > (1 * 24 * 60 * 60)) {
                       $red_flag = true;
                    } elseif (($suborder_data['order_status_id'] == 13 || $suborder_data['order_status_id'] == 14) && (time() - strtotime($lastUpdateTime)) > (5 * 24 * 60 * 60)) {
                       $red_flag = true;
                    } elseif ($suborder_data['order_status_id'] == 4 && (time() - strtotime($lastUpdateTime)) > (1 * 24 * 60 * 60)) {
                       $red_flag = true;
                    }

                    $data['orders'][$key]['suborder'][$key_suborder_id]['red_flag'] = $red_flag;
                    $data['orders'][$key]['suborder'][$key_suborder_id]['dispute_flag'] = $dispute_flag;

                    $order_status_id = $suborder_data['order_status_id'];
                    $collect_suborder_status_id[$key_suborder_id] = $suborder_data['order_status_id'];

            				/*if($data['orders'][$key]['show_return_btn'] == false){
            					$data['orders'][$key]['show_return_btn'] = (in_array((int)$order_status_id, $this->_return_states)) ? true : false;
            				}*/

                    $data['orders'][$key]['suborder'][$key_suborder_id]['delivery_date']='';
                    if (!empty($suborder_data['order_history'])) {
                        foreach ($suborder_data['order_history'] as $suborder) {
                            if ($suborder['order_status_id'] == '15') {
                            $data['orders'][$key]['suborder'][$key_suborder_id]['delivery_date'] = date("d-m-Y", strtotime($suborder['date_added'])) ;
                            }
                        }
                    }
            }

            //color coding for credit
            $data['orders'][$key]['order']['credit_color'] = false;
            $payment_code = trim(strtolower($result['order']['payment_code']));
            if( in_array($payment_code, CREDIT_PAYMENT_CODES) ) {
                $data['orders'][$key]['order']['credit_color'] = true;
            }
        }

        
        $data['order_total_advance'] = json_encode($order_advance_total);
        $data['order_advance_breakup'] = json_encode($order_advance_breakup);
        $data['advance_locked_status'] = json_encode($advance_locked_status);
        $data['user'] = $this->user->getUserName();
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
            $data['selected'] = (array) $this->request->post['selected'];
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

        $data['sort_order'] = $this->url->link('operations/neogrowth_order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $sort_url, 'SSL');
        $data['sort_customer'] = $this->url->link('operations/neogrowth_order', 'token=' . $this->session->data['token'] . '&sort=customer' . $sort_url, 'SSL');
        $data['sort_date_added'] = $this->url->link('operations/neogrowth_order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $sort_url, 'SSL');
        $data['sort_date_modified'] = $this->url->link('operations/neogrowth_order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $sort_url, 'SSL');

        $data['neogrowth_action_url'] = $this->url->link('operations/neogrowth_order/takeNeoGrowthAction', 'token=' . $this->session->data['token'], 'SSL');

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
        $pagination->url = $this->url->link('operations/neogrowth_order', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_order_id'] = $filter_order_id;
        $data['filter_order_no'] = $filter_order_no;
        $data['filter_customer'] = $filter_customer;
        $data['filter_order_status'] = $filter_order_status;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_date_modified'] = $filter_date_modified;
        $data['filter_payment_code'] = 'credit';

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('operations/neogrowth_order_list.tpl', $data));
    }

    public function takeNeoGrowthAction() {
      $action = $this->request->post['action'];
      $data = array(
        'order_no' => $this->request->post['order_no'],
        'customer_id' => $this->request->post['customer_id'],
        'detail' => $this->request->post['detail'],
        'total' => $this->request->post['total']
      );

      $credit_payment_gateway = new CreditPayment($this);
      
      switch ($action) {
        case 'cancel':
          $credit_payment_gateway->cancelOrder($data);
          break;
          
        case 'deliver':
          $credit_payment_gateway->deliverOrder($data);
          break;
        
        case 'return':
          $credit_payment_gateway->returnOrder($data);
          break;
        
        default:
          break;
      }
      
      $this->response->redirect($this->url->link('operations/neogrowth_order', 'token=' . $this->session->data['token'], 'SSL'));
    }
    
    public function getNeoGrowthTransactions() {
      $order_no = $this->request->get['order_no'];
      $credit_payment_gateway = new CreditPayment($this);
      $data['order_no'] = $order_no;
      $data['neo_growth_transactions'] = $credit_payment_gateway->getNeoGrowthTransactionLogUsingOrderNo($order_no);
      $this->response->setOutput($this->load->view('operations/neogrowth_transactions.tpl', $data));
    }

}
