<?php

class ControllerSaleOrder extends Controller {

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

        if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
            
            $this->load->language('franchise/order');
            $this->document->setTitle($this->language->get('heading_title'));

            
        } else {
            
            $this->load->language('sale/order');
            $this->document->setTitle($this->language->get('heading_title'));

        }
        
        //$this->load->model('sale/order');
        $this->getList();
    }

    protected function getList() {

        // load All models
        $this->load->model('sale/customer');
        $this->load->model('sale/order');

        // Initializing
        $data = array();
        $filter_data = array();
        $general_url = '';

        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        // Looping over GET request params
        foreach ( $this->request->get as $key => $value ) {
            // Deal with filter_% keys
            if ( stripos($key, 'filter_') === 0 ) {
                // Filter(s) to get Orders from Model
                $filter_data[$key] = $value;

                // Populating URL
                $general_url .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));

                // Populating data array
                $data[$key] = $value;
            }
        }

        // Sorting is always ORDER BY order_id DESC; Getting Page number
        $page  = $this->request->get['page']  ?? 'FIRST';
        $filter_data['limit'] = 15;
        $filter_data['page'] = $page;

        // Breadcrumbs
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



        $data['zone_areas'] = $this->db->getEnumValues('oc_zone', 'zone_area');

        $results     = array();

        $orders        = $this->model_sale_order->getOrders($filter_data)['data'] ?? array();

        $customer_ids  = array();
        $customer_obj  = new Customer($this);

        $order_ids = array();

        if ($orders) {
            
            $selector = array(
                'suborder' =>
                                    array('select' => 
                                        array('order_status_id', 
                                            'shipping_method',
                                            'total',
                                            'invoice_no',
                                            'invoice_prefix',
                                            'invoice_date',
                                            'courier_partner',
                                            'tracking_no',
                                            'delivered_date'
                                        )
                                    ),
                                'order_history' => array()
                            );
            
            $order_ids = array_unique(array_column($orders, 'order_id'));
            $cn_data   = CreditNote::getCnDetailsToCalculateOrderBalance($this->db, $order_ids);

            $order_details_arr = OrderInfo::getOrderInfo($this->db, $order_ids, '', $selector);

            foreach ($orders as $order_row) {
                $order_id = (int)$order_row['order_id'];

                $results[$order_id] = $order_details_arr[$order_id];
                $results[$order_id]['order'] = $order_row;
                
                $results[$order_id]['order']['rbl_dpd_status'] = 0;
                if( !empty($order_row['payment_code']) 
                    && 
                    $order_row['payment_code'] == 'rbl_credit'
                ){
                   $results[$order_id]['order']['rbl_dpd_status'] = $this->model_sale_order->getOrderRblDPDStatus($order_id);  
                }

                $cn_amount = 0;
                if(!empty($cn_data[$order_id])){
                    $cn_amount = $cn_data[$order_id]['cn_amount'];
                }

                $cod_failed_penalty = 0;
                if(!empty($cn_data[$order_id])){
                    $cod_failed_penalty = $cn_data[$order_id]['cod_failed_penalty'];
                }

                $less_cash_discount = 0;
                if(!empty($cn_data[$order_id])){
                    $less_cash_discount = $cn_data[$order_id]['less_cash_discount'];
                }

                $other_charges = 0;
                if(!empty($cn_data[$order_id])){
                    $other_charges = $cn_data[$order_id]['other_charges'];
                }

                $results[$order_id]['order']['cn_amount']         = $cn_amount;
                $results[$order_id]['order']['cn_penality']       = $cod_failed_penalty;
                $results[$order_id]['order']['less_cash_discount']= $less_cash_discount;
                $results[$order_id]['order']['other_charges']     = $other_charges;

                $cust_id = $results[$order_id]['order']['customer_id'];
                if(!empty($cust_id)){
                    $customer_ids[] = $cust_id;
                }
            }
        }

        $customer_ids  = array_unique($customer_ids);
        $fields        = 'customer_id, master_id, has_website, ws_gcm_registration_id, self_order, upi_vpa';
        $customer_info = $customer_obj->getCutomerInfo($customer_ids, $fields);

        $customer_having_website= array();
        //Get CustomerIds list, who has thier website
        foreach ($customer_info as $cid => $value) {
            if($value['has_website'] == 1){
                $customer_having_website[] = $cid; 
            }
        }
        //customer's websites URLs with given customer ids
        $customer_website_ulrs   = $customer_obj->getCutomerWebsiteUrl($customer_having_website);
        
        // Finding all the related id(s) and its duplicate/master marking status
        $duplicate_master_ids = $this->model_sale_customer->getAllMasterDuplicateCustomerIds($customer_ids);
        // all customer id of master id
        $all_customer_ids = array_column($duplicate_master_ids, 'all_related_ids');
        
        // get all payment Codes
        $this->load->model('extension/extension');
        $data['payment_codes'] = $this->model_extension_extension->getInstalled('payment');
        //get all order status
        $this->load->model('localisation/order_status');
        $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

        $order_statuses = array_combine(
                             array_column($order_statuses_qry, 'order_status_id'), 
                             array_column($order_statuses_qry, 'name'));
        
        $order_statuses[0] = 'Missing Order';

        $data['sales_staff_list'] = $this->model_sale_order->getSalesStaffList();

        $order_advance_total   = array();
        $order_advance_breakup = array();
        $advance_locked_status = array();

        $this->load->model('sale/customer_credit_application');

        $orders_payment_history_arr = $this->model_sale_order->getOrdersPaymentHistory($order_ids);
        $total_advance_arr = OrderInfo::getTotalAdvanceByOrderIds($this->db, $order_ids);
        $advance_voucher_arr = AdvanceVoucher::getAdvanceVouchersBySuborderGroup($this->db, $order_ids);
        $tentative_advance_history_arr = $this->model_sale_order->getTentativeAdvanceHistoryByOrderIds($order_ids);
        $amount_tentative_advances_arr = $this->model_sale_order->getAmountTentativeAdvanceByOrderIds($order_ids);;
        $order_processing_date_arr = $this->model_sale_order->getOrderProcessingDateByOrderIds($order_ids);
        $app_installed_status_arr = $this->model_sale_order->getAppInstalledStatusByCustomerIds($customer_ids);
        $credit_leads_arr = $this->model_sale_customer_credit_application->checkCreditLeadByCustomerIds(implode(",", $customer_ids));
        $only_tentative_amount_arr = $this->model_sale_order->getOnlyTentativeAmountByOrderIds($order_ids);
        $order_taged_sales_staff_arr = $this->model_sale_order->getOrderTagedSalesStaffByOrderIds($order_ids);
        
        $franchise = new Franchise($this);
        $franchise_data_arr = array();

        foreach ($results as $key => $result) {
           
            $order_id = $result['order']['order_id'];
            $collect_suborder_status_id = array();
            if ($result['order']['code_version'] != '1.0') {
                $order_advance_total[$order_id] = $total_advance_arr[$order_id];
                $order_advance_breakup[$result['order']['order_id']] = $advance_voucher_arr[$order_id];
            }

            $payment_history           = $orders_payment_history_arr[$order_id];

            $tentative_advance_history = $tentative_advance_history_arr[$order_id];
            $getOnlyTentativeAmount    = $only_tentative_amount_arr[$order_id];

            $amountTentativeAdvance  = 0;
            $amountTentativeAdvances = $amount_tentative_advances_arr[$order_id];

            if (!empty($amountTentativeAdvances)) {
                if ($amountTentativeAdvances['confirm'] == 0) {
                    if (($amountTentativeAdvances['transaction_status'] == 'will_deposit' && $amountTentativeAdvances['payment_mode'] != 'cheque') || $amountTentativeAdvances['transaction_status'] == 'deposited') {
                        $amountTentativeAdvance = $amountTentativeAdvances['amount'];
                    }
                }
            }

            $data['orders'][$key]['order']    = $result['order'];
            $data['orders'][$key]['suborder'] = $result['suborder'];

            // additional info in order
            $data['order_taged_sales_staff'] = $order_taged_sales_staff_arr[$order_id];
            $tag_sales_staff = array();
            if (!empty($data['order_taged_sales_staff'])) {
                foreach ($data['order_taged_sales_staff'] as $staff) {
                    
                    if ($staff['name'] != '' && $staff['active_status'] != '' ) {
                        $active_status = ($staff['active_status'] == 1) ? 'active' : 'inactive';
                        $tag_sales_staff_name = $staff['role'] . '-' . $staff['name'] . '-' . $staff['telephone'] . '-' . $active_status;             
                        $tag_sales_staff[] = $tag_sales_staff_name;
                    } else {
                        $tag_sales_staff[] = '--None--';
                    }
                
                }
            } else {
                $tag_sales_staff[] = '--None--';
            }
            $customer_id = $result['order']['customer_id'];

            $data['orders'][$key]['order']['customer_self_order']   = $customer_info[$customer_id]['self_order'];
            $data['orders'][$key]['order']['customer_vpa']          = $customer_info[$customer_id]['upi_vpa'] ?? '';
            // get order processing date
            $data['orders'][$key]['order']['order_processing_date'] = $order_processing_date_arr[$order_id];
            $data['orders'][$key]['order']['master_id']             = $customer_info[$customer_id]['master_id'] ?? 0;
            $data['orders'][$key]['order']['sales_staff_name']      = implode('<br>', $tag_sales_staff);
            $data['orders'][$key]['order']['customer']              = $result['order']['firstname'] . ' ' . $result['order']['lastname'];
            $data['orders'][$key]['order']['website_link']          = $customer_website_ulrs->$customer_id ?? '';

            $payment_code = trim(strtolower($result['order']['payment_code'])) ?? '';
          
            $data['orders'][$key]['order']['payment_mode']          = (in_array($payment_code, CREDIT_PAYMENT_CODES) ? $payment_code : ($payment_code == 'cod' ? 'C' : 'P') );

            $data['orders'][$key]['order']['total_amount']          = $result['order']['total'];
            $data['orders'][$key]['order']['payment_history']       = $payment_history;
            $data['orders'][$key]['order']['tentative_advance_history'] = $tentative_advance_history;
            $data['orders'][$key]['order']['getOnlyTentativeAmount']    = $getOnlyTentativeAmount;
            $data['orders'][$key]['order']['app_installed']         = $app_installed_status_arr[$customer_id];

            $data['orders'][$key]['order']['credit_lead']         	= $credit_leads_arr[$customer_id] ?? 0;
            $data['orders'][$key]['order']['cashback_coupon_amount'] = $payment_history['cashback_coupon_amount'];

            $balance_total_amount = $this->currency->format(
                                            str_replace(',', '', 
                                                ( $payment_history['payed_by_customer']  
                                                 + $payment_history['cashback_coupon_amount'] 
                                                ) 
                                            ), 
                                            $result['order']['currency_code'],
                                            $result['order']['live_currency_conversion_rate'],
                                            false
                                    )
                                    - 
                                    $this->currency->format(
                                            str_replace(',', '', 
                                                $data['orders'][$key]['order']['total']
                                            ), 
                                            $result['order']['currency_code'],
                                            $result['order']['currency_value'],
                                            false
                                    );
            
            $data['orders'][$key]['order']['balance_total_amount'] = $balance_total_amount;

            // both are show on list page. ///////////////
            $data['orders'][$key]['order']['net_payment'] = $result['order']['total'] - $payment_history['cashback_coupon_amount'];
            //////////////////////////////
           
            //condition applied for where customer id is not available with us in BD now
            if ( isset($data['orders'][$key]['order']['customer_id']) ) {
                $all_related_ids = $duplicate_master_ids[$data['orders'][$key]['order']['customer_id']]['all_related_ids'];
                
                $data['orders'][$key]['order']['id_status'] = $duplicate_master_ids[$data['orders'][$key]['order']['customer_id']]['id_status'];
            }

            $all_related_ids_array = explode(',',$all_related_ids);
            
            if (
                isset($this->request->get['filter_franchise_tab']) && 
                $this->request->get['filter_franchise_tab'] == '1'
            ) {
                $data['orders'][$key]['order']['total_order_link'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_franchise_tab=' . '1' .
                    '&filter_customer_id=' . $all_related_ids .
                    '&page=1', 'SSL');
            } else {
               $data['orders'][$key]['order']['total_order_link'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] .
                    '&filter_customer_id=' . $all_related_ids .
                    '&page=1', 'SSL');
            }
            
            $data['orders'][$key]['order']['customer_link'] = $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] .
                    '&customer_id=' . $result['order']['customer_id'], 'SSL');
            $data['orders'][$key]['order']['credit_tab']     = $this->url->link('sale/customer_credits', 
                                                     'token=' . $this->session->data['token'] .
                                                     '&customer_id=' . $result['order']['customer_id'], 'SSL');
            
            // Currency formatting on order total
            $data['orders'][$key]['order']['total'] = $this->currency->format(
                                                            $data['orders'][$key]['order']['total'],
                                                            $result['order']['currency_code'],
                                                            $result['order']['currency_value'],
                                                            true);

			$data['orders'][$key]['status'] = array();

			$data['orders'][$key]['show_return_btn']  = true;
			$data['orders'][$key]['order_return_url'] = $this->url->link('sale/return/return_form', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'], 'SSL');

			// additional info in suborder
			$all_suborder_status = array();
            // for dont disptach alert
            $order_status = array();

			foreach ($result['suborder'] as $key_suborder_id => $suborder_data) {

                //Code added by Nilesh for suborder tracking after shipped
                $data['orders'][$key]['suborder'][$key_suborder_id]['tracking_url'] = $this->model_sale_order->getTrackingUrl($suborder_data['courier_partner']);
                $data['orders'][$key]['suborder'][$key_suborder_id]['tracking_no'] = (!empty($suborder_data['tracking_no']) ? $suborder_data['tracking_no'] : '' );
                
                $order_status[] = $suborder_data['order_status_id'];
                $data['orders'][$key]['suborder'][$key_suborder_id]['status'] = $order_statuses[$suborder_data['order_status_id']] ;
			    $data['orders'][$key]['status'][$key_suborder_id]['status'] = $key_suborder_id . " : " .  $order_statuses[$suborder_data['order_status_id']] ;
                if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
                    $data['orders'][$key]['suborder'][$key_suborder_id]['view'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'] . '&suborder_id=' . $key_suborder_id . '&order_page_filters=' . base64_encode($general_url), 'SSL');
                } else {
                    $data['orders'][$key]['suborder'][$key_suborder_id]['view'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'] . '&suborder_id=' . $key_suborder_id . '&order_page_filters=' . base64_encode($general_url), 'SSL');
                }
                
                if( ($suborder_data['invoice_no'] == 0 && $result['order']['rbl_dpd_status'] == 0) || in_array($this->user->getId(), explode(',',ADMIN_IDS)) || (in_array($this->user->getId(), explode(',',OPERATIONS_ADMIN_IDS)) && $result['order']['rbl_dpd_status'] == 0)  ) 
                {
                    $data['orders'][$key]['suborder'][$key_suborder_id]['edit'] = $this->url->link('sale/edit_order',
				        'token=' . $this->session->data['token'] .
				        '&order_id=' . $order_id .'&suborder_id=' . $key_suborder_id, 'SSL');
                }

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

                $data['orders'][$key]['suborder'][$key_suborder_id]['red_flag']     = $red_flag;
                $data['orders'][$key]['suborder'][$key_suborder_id]['dispute_flag'] = $dispute_flag;

                $order_status_id = $suborder_data['order_status_id'];
                $collect_suborder_status_id[$key_suborder_id] = $suborder_data['order_status_id'];
            }
            $franchise_data = array();
            
            //color coding for credit
            $data['orders'][$key]['order']['credit_color']    = false;
            

            $data['orders'][$key]['order']['franchise_color'] = false;

            if ( !empty($result['order']['store_voucher']) ) {
                if(!isset($franchise_data_arr[$result['order']['customer_id']])) {
                    $franchise_data_arr[$result['order']['customer_id']] = $franchise->getFranchiseData($result['order']['customer_id']);
                }
                $franchise_data = $franchise_data_arr[$result['order']['customer_id']];
            }
            $payment_code = trim(strtolower($result['order']['payment_code']));
            if ( in_array($payment_code, CREDIT_PAYMENT_CODES) ) {
                $data['orders'][$key]['order']['credit_color'] = true;
            } else if(!empty($franchise_data) && ($result['order']['store_voucher'] == $franchise_data['franchise_coupon'])){
                $data['orders'][$key]['order']['franchise_color'] = true;
            }

        }

        
        $data['order_total_advance']   = json_encode($order_advance_total);
        $data['order_advance_breakup'] = json_encode($order_advance_breakup);
        $data['advance_locked_status'] = json_encode($advance_locked_status);
        $data['user']                  = $this->user->getUserName();
        $data['user_id']               = $this->user->getId();
        $data['token']                 = $this->session->data['token'];

        if (!empty($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (!empty($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (!empty($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

		$pagination = new PaginationV2();
        $pagination->page = $page;
        $pagination->next = end($orders)['order_id'];
        $pagination->total = count($orders);
        $pagination->limit = 15;
        $pagination->url = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $general_url . '&page={page}', 'SSL');
        $data['pagination'] = $pagination->render();

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['header']          = $this->load->controller('common/header');
        $data['column_left']     = $this->load->controller('common/column_left');
        $data['footer']          = $this->load->controller('common/footer');
        $data['duplicate_popup'] = $this->load->view('sale/marked_duplicate_popup.tpl');
        $this->response->setOutput($this->load->view('sale/order_list.tpl', $data));
    }

    public function getForm() {
        $this->load->model('sale/customer');

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        $data['text_form'] = !isset($this->request->get['order_id']) ? $data['text_add'] : $data['text_edit'];

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

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
            'href' => $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['cancel'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['order_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
        }

        if (!empty($order_info)) {
            $data['order_id'] = $this->request->get['order_id'];
            $data['store_id'] = $order_info['store_id'];

            $data['customer'] = $order_info['customer'];
            $data['customer_id'] = $order_info['customer_id'];
            $data['firstname'] = $order_info['firstname'];
            $data['lastname'] = $order_info['lastname'];
            $data['email'] = $order_info['email'];
            $data['telephone'] = $order_info['telephone'];
            $data['account_custom_field'] = $order_info['custom_field'];

            $this->load->model('sale/customer');

            $data['addresses'] = $this->model_sale_customer->getAddresses($order_info['customer_id']);
            $data['payment_firstname'] = $order_info['payment_firstname'];
            $data['payment_lastname'] = $order_info['payment_lastname'];
            $data['payment_company'] = $order_info['payment_company'];
            $data['payment_address_1'] = $order_info['payment_address_1'];
            $data['payment_address_2'] = $order_info['payment_address_2'];
            $data['payment_city'] = $order_info['payment_city'];
            $data['payment_postcode'] = $order_info['payment_postcode'];
            $data['payment_country_id'] = $order_info['payment_country_id'];
            $data['payment_zone_id'] = $order_info['payment_zone_id'];
            $data['payment_custom_field'] = $order_info['payment_custom_field'];
            $data['payment_method'] = $order_info['payment_method'];
            $data['payment_code'] = $order_info['payment_code'];

            $data['shipping_firstname'] = $order_info['shipping_firstname'];
            $data['shipping_lastname'] = $order_info['shipping_lastname'];
            $data['shipping_company'] = $order_info['shipping_company'];
            $data['shipping_address_1'] = $order_info['shipping_address_1'];
            $data['shipping_address_2'] = $order_info['shipping_address_2'];
            $data['shipping_city'] = $order_info['shipping_city'];
            $data['shipping_postcode'] = $order_info['shipping_postcode'];
            $data['shipping_country_id'] = $order_info['shipping_country_id'];
            $data['shipping_zone_id'] = $order_info['shipping_zone_id'];
            $data['shipping_custom_field'] = $order_info['shipping_custom_field'];
            $data['shipping_method'] = $order_info['shipping_method'];
            $data['shipping_code'] = $order_info['shipping_code'];

            // Add products to the API
            $data['order_products'] = array();

            $products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);
            //echo "<pre>"; print_r($products); exit;
            if (isset($this->request->get['order_id'])) {
                $data['order_edit'] = $this->request->get['order_id'];
            } else {
                $data['order_edit'] = '';
            }
            foreach ($products as $product) {
                // todo check
                $seller = $this->MsLoader->MsSeller->getSeller(
                        $this->MsLoader->MsProduct->getSellerId($product['product_id']), array(
                    'product_id' => $product['product_id']
                        )
                );

                $data['order_products'][] = array(
                    'seller' => array(
                        'seller_id' => isset($seller['seller_id']) ? $seller['seller_id'] : '',
                        'nickname' => isset($seller['ms.nickname']) ? $seller['ms.nickname'] : ''
                    ),
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']),
                    'quantity' => $product['quantity'],
                    'piece_in_set' => $product['piece_in_set'],
                    'comment' => $product['comment'],
                    'price' => $product['price'],
                    'total' => $product['total'],
                    'tax' => $product['tax']
                );
            }

            // Add vouchers to the API
            $data['order_vouchers'] = $this->model_sale_order->getOrderVouchers($this->request->get['order_id']);

            $data['coupon'] = '';
            $data['voucher'] = '';

            $data['order_totals'] = array();

            $order_totals = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);

            foreach ($order_totals as $order_total) {
                // If coupon, voucher or reward points
                $start = strpos($order_total['title'], '(') + 1;
                $end = strrpos($order_total['title'], ')');

                if ($start && $end) {
                    if ($order_total['code'] == 'coupon') {
                        $data['coupon'] = substr($order_total['title'], $start, $end - $start);
                    }

                    if ($order_total['code'] == 'voucher') {
                        $data['voucher'] = substr($order_total['title'], $start, $end - $start);
                    }
                }
            }

            $data['order_status_id'] = $order_info['order_status_id'];
            $data['comment'] = $order_info['comment'];
            $data['affiliate_id'] = $order_info['affiliate_id'];
            $data['affiliate'] = $order_info['affiliate_firstname'] . ' ' . $order_info['affiliate_lastname'];
            $data['currency_code'] = $order_info['currency_code'];
        } else {
            $data['order_id'] = 0;
            $data['store_id'] = '';
            $data['customer'] = '';
            $data['customer_id'] = '';
            $data['firstname'] = '';
            $data['lastname'] = '';
            $data['email'] = '';
            $data['telephone'] = '';
            $data['customer_custom_field'] = array();

            $data['addresses'] = array();

            $data['payment_firstname'] = '';
            $data['payment_lastname'] = '';
            $data['payment_company'] = '';
            $data['payment_address_1'] = '';
            $data['payment_address_2'] = '';
            $data['payment_city'] = '';
            $data['payment_postcode'] = '';
            $data['payment_country_id'] = '';
            $data['payment_zone_id'] = '';
            $data['payment_custom_field'] = array();
            $data['payment_method'] = '';
            $data['payment_code'] = '';

            $data['shipping_firstname'] = '';
            $data['shipping_lastname'] = '';
            $data['shipping_company'] = '';
            $data['shipping_address_1'] = '';
            $data['shipping_address_2'] = '';
            $data['shipping_city'] = '';
            $data['shipping_postcode'] = '';
            $data['shipping_country_id'] = '';
            $data['shipping_zone_id'] = '';
            $data['shipping_custom_field'] = array();
            $data['shipping_method'] = '';
            $data['shipping_code'] = '';

            $data['order_products'] = array();
            $data['order_vouchers'] = array();
            $data['order_totals'] = array();

            $data['order_status_id'] = $this->config->get('config_order_status_id');
            $data['comment'] = '';
            $data['affiliate_id'] = '';
            $data['affiliate'] = '';
            $data['currency_code'] = $this->config->get('config_currency');

            $data['coupon'] = '';
            $data['voucher'] = '';
        }

        // Stores
        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        // Custom Fields
        $this->load->model('sale/custom_field');

        $data['custom_fields'] = array();

        $filter_data = array(
            'sort' => 'cf.sort_order',
            'order' => 'ASC'
        );

        $custom_fields = $this->model_sale_custom_field->getCustomFields($filter_data);

        foreach ($custom_fields as $custom_field) {
            $data['custom_fields'][] = array(
                'custom_field_id' => $custom_field['custom_field_id'],
                'custom_field_value' => $this->model_sale_custom_field->getCustomFieldValues($custom_field['custom_field_id']),
                'name' => $custom_field['name'],
                'value' => $custom_field['value'],
                'type' => $custom_field['type'],
                'location' => $custom_field['location'],
                'sort_order' => $custom_field['sort_order']
            );
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        $this->load->model('localisation/currency');

        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        $data['voucher_min'] = $this->config->get('config_voucher_min');

        $this->load->model('sale/voucher_theme');

        $data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/order_form.tpl', $data));
    }

    public function info() {

        // Load all models here
        $this->load->model('sale/order');
        $this->load->model('sale/customer');
        $this->load->model('setting/store');
        $this->load->model('localisation/order_status');
        $this->load->model('logistic/connect_india');

        $data = array(); // Initializing the data array to be passed on to template files
        // Read all get requests
        // @todo: This needs to be automatized based on rules file
        if (isset($this->request->get['order_id'])) {
            $data['order_id'] = $this->request->get['order_id'];
        } else {
            $data['order_id'] = 0;
        }

        if (isset($this->request->get['suborder_id'])) {
            $data['suborder_id'] = $this->request->get['suborder_id'];
        } else {
            $data['suborder_id'] = $data['order_id'];
        }

        $this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], array('order' => array(),
                    'suborder' => array())
        );

        $data['user_id'] = $this->user->getId();
        $data['is_dont_dispatch'] = 0;

        if (!empty($this->_order_info['order']) && !empty($this->_order_info['suborder'])) {

            $order_info = $this->_order_info['order'];
            if (!empty($order_info['operations_status']) && $order_info['operations_status'] == 'dont_dispatch' && $order_info['stock_transfer'] == 0) {
                $data['is_dont_dispatch'] = 1;
            }
            $suborder_info = $this->_order_info['suborder'][$data['suborder_id']];
            $data['sale_order_link_with_order_no'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] .
                    '&filter_order_no=' . $order_info['order_no'], 'SSL');

            $data['order_cancelled'] = false;
            $data['payment_code'] = $order_info['payment_code'];

            $data['no_wsb_tape'] = $suborder_info['no_wsb_tape'];
            $data['no_invoice_with_shipment'] = $suborder_info['no_invoice_with_shipment'];
            $data['courier_partner_preference'] = $suborder_info['courier_partner_preference'];

            $data['self_order'] = $order_info['self_order'];

            // if payment code is credit, the get the neogorwth status for this order
            if($data['payment_code'] == 'credit'){
                $credit_payment_gateway = new CreditPayment($this);
                $neo_credit_data = $credit_payment_gateway->getTransactionStatus($order_info['order_no']);
                $data['neo_growth_status'] = $neo_credit_data['status'] ?? '';
                $data['neo_growth_message'] = $neo_credit_data['message'] ?? '';
            }
            
            if ($suborder_info['order_status_id'] == 2) { // SubOrder is not cancelled
                $data['order_cancelled'] = true;
            }
            // Autoloading the lanugage
            if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
            
                $this->load->autoLoadLanguage('franchise/order', $data);
                $this->document->setTitle($data['heading_title']);
            } else {
                
                $this->load->autoLoadLanguage('sale/order', $data);
                $this->document->setTitle($data['heading_title']);
            }

            //Check for buyer invoice id button
            $data['error_msg_not_show_button'] = '';
            $error_arr['error'] = $this->model_sale_order->validateBuyerInvoiceId($data['order_id'], $data['suborder_id']);
            if (!empty($error_arr['error'])) {
                $error_msg = $this->validateBuyerInvoiceId($error_arr);
                if (!empty($error_msg)) {
                    $data['error_msg_not_show_button'] = $error_msg;
                }
            }

            $data['token'] = $this->session->data['token'];
            $data['stores'] = $this->model_setting_store->getStores(); // get stores list for customer login button
            // Creating URLs
            // @todo: AutoURL builder based on the get/post data
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

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }


            // @todo: Need auto Breadcrumb builder
            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $data['text_home'],
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );

            if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
                $data['breadcrumbs'][] = array(
                'text' => $data['heading_title'],
                'href' => $this->url->link('franchise/order', 'token=' . $this->session->data['token'] . $url .'&filter_franchise_tab='. '1' , 'SSL')
                );
            } else {
                $data['breadcrumbs'][] = array(
                'text' => $data['heading_title'],
                'href' => $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL')
                );
            }


            // Various buttons at the top of the page
            $file_name = array();
            $file_name['order_id'] = (int) $this->request->get['order_id'];
            $file_name['suborder_id'] = $this->request->get['suborder_id'];
            $file_name = serialize($file_name);
            $file_name = base64_encode($file_name);

            $data['detail_invoice'] = $this->securefiledownload->getDownloadLink('buyer_b2b_invoice', $file_name, false); // this is same as B2B invoice

            $data['send_mail_buyer'] = $this->url->link('sale/order/resendMailToBuyer', 'token=' . $this->session->data['token'] . '&order_id=' . (int) $this->request->get['order_id'], 'SSL');

            $data['edit'] = $this->url->link('sale/order/edit', 'token=' . $this->session->data['token'] . '&order_id=' . (int) $this->request->get['order_id'], 'SSL');

            if (isset($this->request->get['order_page_filters'])) {
                $url = base64_decode($this->request->get['order_page_filters']);
            }
            
            if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
                $data['cancel'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url . '&filter_franchise_tab=' . '1', 'SSL');
            } else {
                $data['cancel'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL');
            }

            $data['shipping_label'] = $this->url->link('sale/shipping_label', 'token=' . $this->session->data['token'] .
                    '&order_id=' . $data['order_id'], 'SSL');

            $data['shipping_charge_collected'] = $this->model_sale_order->getShippingCharge($order_info, $suborder_info);

            $order_info['sales_staff_list'] = $this->model_sale_order->getSalesStaffList();
            $order_info['order_taged_sales_staff'] = $this->model_sale_order->getOrderTagedSalesStaff($this->request->get['order_id']);

            // Autopopulating data array based on the order data received from the model
            foreach ($order_info as $key => $order_infos) {
                $data[$key] = $order_infos;
            }
            $data['suborder_weight'] = OrderEdit::getProductWeightForShipping($this->db, $data['order_id'], $data['suborder_id']);
            $data['date_added'] = date('d-M-Y', strtotime($data['date_added']));
            $data['date_modified'] = date('d-M-Y', strtotime($data['date_modified']));
            $data['total'] = $this->currency->format($suborder_info['total'], $order_info['currency_code'], $order_info['currency_value']);
            // without currency format
            $data['without_crncy_frmt_total'] = $suborder_info['total'];

            // invoice date
            $data['invoice_date'] =  $suborder_info['invoice_date'];
            
            if ($this->_order_info['suborder'][$data['suborder_id']]['invoice_no']) {
                $data['invoice_no'] = $suborder_info['invoice_prefix'] . $suborder_info['invoice_no'];
            } else {
                $data['invoice_no'] = '';
            }

            // Weight Calculation
            if (!empty($this->_order_info['suborder'][$data['suborder_id']]['order_product']) && !empty($order_info['code_version'] != '1.0')) {
                $data['weight'] = 0;
                foreach ($this->_order_info['suborder'][$data['suborder_id']]['order_product'] as $product) {
                    $data['weight'] += (float) $product['weight_per_piece'];
                }
                $data['weight'] = $this->weight->format($data['weight'], $order_info['weight_class_id']);
            }

            $data['is_dropshipper'] = $this->model_sale_customer->getisdropshipper($order_info['customer_id']);

            if ($this->order_info['customer_id']) {
                $data['customer'] = $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->order_info['customer_id'], 'SSL');
                $data['total_order_link'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_customer_id=' . $this->order_info['customer_id'], 'SSL');
            } else {
                $data['customer'] = '';
                $data['total_order_link'] = '';
            }


            $data['shipping_method'] = $suborder_info['shipping_method'];
            $data['tracking_url'] = $this->model_sale_order->getTrackingUrl($suborder_info['courier_partner']);
            $data['tracking_no'] = (!empty($suborder_info['tracking_no']) ? $suborder_info['tracking_no'] : '' );
            $data['courier_partner'] = (!empty($suborder_info['courier_partner']) ? $suborder_info['courier_partner'] : '' );

            // Current User
            $data['user'] = $this->user->getUserName()['name'];
            $data['user_id'] = $this->user->getId();


            if (in_array($data['user_id'], explode(',', ADMIN_IDS))) {
                $data['admin'] = 1;
            } else {
                $data['admin'] = 0;
            }
            if (in_array($this->user->getId(), explode(',',OPERATIONS_ADMIN_IDS))) {
                $data['is_operations_superadmin'] = 1;
            } else {
                $data['is_operations_superadmin'] = 0;
            }
            
            // get last updated history
            $data['last_update_history'] = !empty($suborder_info['order_history']) ?
                    end($suborder_info['order_history']) : array();

            // Get order status
            $order_status_info = $this->model_localisation_order_status->getOrderStatus($suborder_info['order_status_id']);
            if ($order_status_info) {
                $data['order_status'] = $order_status_info['name'];
            } else {
                $data['order_status'] = '';
            }


            // @note: This is a temporary fix. to get rid of API way of working
            // @todo: Get OrderState classes to do the API activity. Current order edit methods
            // are open to public and a security risk
            $data['https_catalog'] = '';
            if ($this->user->hasPermission('modify', 'sale/order')) {
                $data['https_catalog'] = HTTPS_CATALOG;
            }

            // Custom Fields
            $this->load->model('sale/custom_field');
            $custom_fields = $this->model_sale_custom_field->getCustomFields();

            $buyer_invoice = new BuyerInvoice($this);
            $orderTotal = $buyer_invoice->getTotals($data['order_id'], $data['suborder_id']);
            
            $order_total = 0;
            if (!empty($orderTotal['total_amt'])) {
                $order_total = $orderTotal['total_amt']['value'];
            }
            $data['order_total'] = $order_total;
            
            $net_payble = 0;
            if (!empty($orderTotal['net_amount'])) {
                $net_payble = $orderTotal['net_amount']['value'];
            }
            
            if( ( $data['payment_code'] == 'cod' || 
                  in_array($data['payment_code'], CREDIT_PAYMENT_CODES)
                ) && $net_payble > 0
            ){
                $data['net_payble'] = $net_payble;        
            }else { 
                $data['net_payble'] = 0;        
            }
            
            $data['shipping_code'] = $suborder_info['shipping_code'];
            
            $data['order_status_id'] = $suborder_info['order_status_id'];
            
            if(isset($order_info['payment_code']) && strtolower(trim($order_info['payment_code'])) != "cod"){
                $data['payment_mode'] = 'prepaid';
            } else if(isset($order_info['payment_code']) && (strtolower(trim($order_info['payment_code'])) == "cod" || in_array($order_info['payment_code'], CREDIT_PAYMENT_CODES)) && $data['net_payble'] > 0) { 
                $data['payment_mode'] = 'cod';
            }else{
                $data['payment_mode'] = 'prepaid'; 
            }

            $data['cod_security_amount'] = '';
            
            $availableCODSecurityForSuborderStatus = array_merge(
                                                        ORDER_STATUS_CLUSTERS['order_received'],
                                                        ORDER_STATUS_CLUSTERS['processed'],
                                                        ORDER_STATUS_CLUSTERS['shipped'],
                                                        ORDER_STATUS_CLUSTERS['failed']
                                                        );
            /* CALL stored procedure to get customer available COD security balance */
            if( $data['payment_mode'] == 'cod' && in_array($suborder_info['order_status_id'], $availableCODSecurityForSuborderStatus) )
            {
                $res = $this->db->query("CALL calculateAvailableCodSecurityAmountForOrder(".$data['customer_id'].",".$data['order_id'].",@cod_amount)");
                $res = $this->db->query("SELECT @cod_amount;");
                $data['cod_security_amount'] = $res->row['@cod_amount'];
                $data['cod_security_amount_formated'] = $this->currency->format($res->row['@cod_amount']);
            }

            $data['wsb_credit_balance'] = 0;
            $customer_id  = $this->_order_info['order']['customer_id'] ?? 0;
            $payment_code = $this->_order_info['order']['payment_code'] ?? '';
            if($payment_code == 'wsb_credit'){
                //Get Available Wsb credit balance
                $wsb_credit_payment = new WsbCreditPayment($this);
                $data['wsb_credit_balance'] = $wsb_credit_payment->getUsedWsbCreditBalanceForOrderId($customer_id, $data['order_id']);
            }
            
            $data['rbl_dpd_status'] = 0;
            if( !empty($data['payment_code']) 
                && 
                $data['payment_code'] == 'rbl_credit'
            ){
                $data['rbl_dpd_status'] = $this->model_sale_order->getOrderRblDPDStatus($data['order_id']);    
                $data['text_rbl_dpd_count_status'] = $this->language->get('text_rbl_dpd_count_status');
            }

            // Creating various Order Detail tabs in separate emthods
            $data['order_info_tab_shipping_label'] = true;
            
            $data['order_info_label_courier_advisory'] = $this->orderInfoLabelCourierAdvisory($data);
            
            $data['logged_user_id'] = $this->user->getId();

            //$products = $buyer_invoice->getProductsArrayBySuborderIdEditTypeWise($data['order_id'], $data['suborder_id']);
            //$check_product_edit_type = $this->model_sale_order->checkProductEditTypeForBreakSuborder($data['order_id'], $data['suborder_id']);
            $data['order_info_tab_payment_details'] = $this->orderInfoCustomerAddressDetails($data, 'payment', $custom_fields);
            
            $data['order_info_tab_shipping_details'] = $this->orderInfoCustomerAddressDetails($data, 'shipping', $custom_fields);
            // Creating various short snippet info windows in separate methods (for reusability)
            $data['order_info_label_important_information'] = $this->orderInfoLabelImportantInformation($data);

        //Operation - order info page, tab permission settings
              $info_tabs = [
                            'orderInfoTabSellerProductBreak_up',
                            'orderInfoTabCourierPartners',
                            'orderInfoTabAdditionalInformation',
                            'orderInfoLabelCourierAdvisory',
                            'orderInfoTabHistory',
                            'orderInfoTabProducts',
                            'breakSuborderProducts',
                            'orderInfoCustomerAddressDetails',
                            'orderInfoLabelImportantInformation',
                            'orderInfoTabPaymentHistory',
                            'orderInfoTabOrderProductStatus'
                           ];
            $data['allowed_tabs'] = array();
            if(!empty($info_tabs))
            {
                foreach($info_tabs as $tab)
                {
                   $permission_url = 'sale/order/'.$tab;
                   if( $this->user->hasPermission('access', $permission_url, true) )
                   {
                       $data['allowed_tabs'][] = $tab;
                   }
                }
            }
            
            // Generating common controller views
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
                $data['request_page'] = 'franchise_info_order';
            } else {
                $data['request_page'] = 'sale_info_order';
            }

            $this->response->setOutput($this->load->view('sale/order_info.tpl', $data));
        } else {

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

            if (isset($this->request->get['filter_sales_zone'])) {
                $url .= '&filter_sales_zone=' . urlencode(html_entity_decode($this->request->get['filter_sales_zone'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_sales_staff'])) {
                $url .= '&filter_sales_staff=' . urlencode(html_entity_decode($this->request->get['filter_sales_staff'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_sales_staff_id'])) {
                $url .= '&filter_sales_staff_id=' . urlencode(html_entity_decode($this->request->get['filter_sales_staff_id'], ENT_QUOTES, 'UTF-8'));
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

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

            if (isset($this->request->get['filter_pickup_city_code'])) {
                $url .= '&filter_pickup_city_code=' . $this->request->get['filter_pickup_city_code'];
            }

            if (isset($this->request->get['filter_customer_id'])) {
                $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
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

            if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
                $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url . '&filter_franchise_tab=' . '1', 'SSL'));
            } else {
                $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            }
            //return false;
        }
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    // used in seller prdouct break up
    public function updateOrderProductField() {
        $this->load->language('sale/order');

        $json = array();

        if (isset($this->request->get['order_product_id'])
                and isset($this->request->get['field'])
                and isset($this->request->get['field_val'])
        ) {

            $order_product_id = (int) ($this->request->get['order_product_id']);
            $field = $this->request->get['field'];
            $field_val = $this->request->get['field_val'];

            $this->load->model('sale/order');

            $result = $this->model_sale_order->updateOrderProductField($order_product_id, $field, $field_val);

            if ($result) {
                $json['success'] = 'Successful updated the order product field';
            } else {
                $json['error'] = $this->language->get('error_action');
            }
        } else {
            $json['error'] = $this->language->get('error_action');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    // cancel Suborder Buyer Invoice
    public function cancelSuborderBuyerInvoice() {
        $this->load->language('sale/order');

        $json = array();

        if (isset($this->request->post['yes']) && !empty ($this->request->post['order_id']) && !empty ($this->request->post['suborder_id'])) {

            $order_id = $this->request->post['order_id'];
            $suborder_id = $this->request->post['suborder_id'];
            $result = OrderEdit::cancelSuborderBuyerInvoice($this, $order_id, $suborder_id);

            if ($result) {
                $json['success'] = 'Successful canceled';
            } else {
                $json['error'] = $this->language->get('error_action');
            }
        } else {
            $json['error'] = $this->language->get('error_action');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // used in seller_break_up product
    public function deleteOrderProduct() {
        $this->load->language('sale/order');

        $json = array();

        if (isset($this->request->get['order_product_id']) && isset($this->request->get['order_product_id'])) {
            $order_product_id = (int) ($this->request->get['order_product_id']);
            $order_id = (int) ($this->request->get['order_id']);
            $this->load->model('sale/order');

            $res = $this->model_sale_order->deleteOrderProduct($order_product_id, $order_id);

            if ($res) {
                $json['success'] = 'Successful deleted the order product';
            } else {
                $json['error'] = $this->language->get('error_action');
            }
        } else {
            $json['error'] = $this->language->get('error_action');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // used in order details
    public function updateSalesStaff() {
        $this->load->language('sale/order');
        $json = array();

        /** All previous sales staff ids * */
        $sales_staff_ids_previous = array();
        $sales_staff = array();

        if (!empty($this->request->post['sales_staff_ids_previous'])) {
            $sales_staff_ids_previous = explode(',', $this->request->post['sales_staff_ids_previous']);
        }

        /** Current sales staff ids * */
        if (!empty($this->request->post['sales_staff'])) {
            $sales_staff = explode(',', $this->request->post['sales_staff']);
        }

        if (isset($this->request->post['sales_staff']) && !empty($this->request->post['sales_staff'])) {

            foreach ($sales_staff as $key => $value) {
                //print_r($this->request->post['sales_staff_ids']); exit;
                $this->request->post['sales_staff'] = $value;
                $json['id'] = $value;
                
                if (isset($this->request->post['order_id']) 
                    && isset($this->request->post['order_no']) 
                    && isset($this->request->post['sales_staff'])) {
                   
                    $order_tag_data = [
                    'order_id' => $this->request->post['order_id'],
                    'order_no' => $this->request->post['order_no'],
                    'sales_staff_id' => $this->request->post['sales_staff'],
                    'customer_id'=> $this->request->post['customer_id'],
                    'amount' => $this->request->post['total'],
                    'type' => $this->request->post['type'],
                    'tag_type' => 'Manual',
                    'user_id' => $this->user->getId()
                    ];
                    
                    $order_tag = new ordertag($order_tag_data);
                    $result = $order_tag->updateSalesStaff();
                    
//                    $this->load->model('sale/order');
//                    $result = $this->model_sale_order->updateSalesStaff($this->request->post['order_id'], 
//                                                                        $this->request->post['order_no'], 
//                                                                        $this->request->post['sales_staff'], 
//                                                                        $this->request->post['customer_id'], 
//                                                                        $this->request->post['total'], 
//                                                                        $this->request->post['type']);

                    if (is_array($result) && $result['force_tag'] == 1) {
                        $json['force_tag'] = $result['return_msg'];
                    } elseif ($result == "done") {
                        $json['success'] = 'Successful updated the sales staff';
                    } else {
                        $json['error'] = $result;
                    }
                } else {
                    $json['error'] = $this->language->get('error_action');
                }
            }

            /*
             * If Order is successfully tagged to sales staff then Push to rabbit mq in library/crm/order.php 
             */
            if (isset($json['success']) && !empty($json['success'])) {

                /** Update sales_staff with merge sales_staff * */
                $this->request->post['sales_staff'] = $sales_staff;
                $this->request->post['sales_staff_ids_previous'] = $sales_staff_ids_previous;
                $user_name = $this->user->getUserName($this->user->getId());

                $this->request->post['operation_staff'] = $user_name;
                $order_crm_leads = new OrderCrmApi($this->request->post);
                $order_crm_leads->updateLeadData();
            }
        } else {
            /** Update sales staff with previous sales staff ids * */
            if (!empty($sales_staff_ids_previous)) {
                /** Update sales_staff with merge sales_staff * */
                $this->request->post['sales_staff'] = $sales_staff;
                $this->request->post['sales_staff_ids_previous'] = $sales_staff_ids_previous;
                $user_name = $this->user->getUserName($this->user->getId());
                $this->request->post['operation_staff'] = $user_name;
                $order_crm_leads = new OrderCrmApi($this->request->post);
                $order_crm_leads->updateLeadData();
                $json['success'] = 'Successful updated the sales staff';
            }
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // used in order details
    public function createInvoiceNo() {
        $this->load->language('sale/order');
        $json = array();

        if (empty($this->request->get['order_id']) || empty($this->request->get['suborder_id'])) {
            $json['error'] = $this->language->get('error_action');
        } else {

            $order_id = (int) $this->request->get['order_id'];
            $suborder_id = $this->request->get['suborder_id'];
            $shipping_method = $this->request->get['shipping_method'];

            $this->load->model('sale/order');

            /*
             * get all suborder products
             */
            $selector = array(
                        'order'         => array('select' => array('order_id', 'customer_id', 'payment_code')),
                        'suborder'      => array('select' => array('order_id', 'suborder_id')),
                        'order_product' => array(
                                            'select' => array('order_product_id', 'edit_type', 'seller_id', 'seller_invoice_id')
                                           )
                        );

            $getSubOrderProducts = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
            
            $invoice_no = $this->model_sale_order->createByerInvoiceNo($order_id, $suborder_id, $getSubOrderProducts['order']['payment_code']);

            if (!empty($invoice_no['error'])) {
                $error_msg = $this->validateBuyerInvoiceId($invoice_no);
                if (!empty($error_msg['error'])) {
                    return $this->response->setOutput(json_encode($error_msg));
                }
            }

            if (!empty($invoice_no)) {
                
                if ($shipping_method != '' && strtolower(trim($shipping_method)) == 'store pickup') {
                   
                    $this->orderProductPickupStatus($order_id, $suborder_id, $shipping_method, $getSubOrderProducts);
                }
                $json['invoice_no'] = $invoice_no['invoice_no'];

                //-----------To add/update product quantity for franchise stock--- Start
                   //get Customer_id
                if(empty($getSubOrderProducts['order'])){
                    
                    //OrderInfo selector
                    $selector = array( 'order' => array('select' => array('order_id', 'customer_id')) );

                    $order_info  = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
                    
                    $customer_id = (int)$order_info['order']['customer_id'] ?? 0;
                }else{
                    $customer_id = (int)$getSubOrderProducts['order']['customer_id'] ?? 0;
                }

                $franchise_product = new FranchiseProduct($this);

                if( $franchise_product->isCustomerFranchise($customer_id) ){
                    $franchise_info = array();
                    $franchise_info['customer_id'] = $customer_id;
                    $franchise_info['order_id']    = $order_id ?? 0;
                    $franchise_info['suborder_id'] = $suborder_id ?? '';

                    $franchise_product->AddOrUpdateFranchiseProduct($franchise_info);
                }
                //-----------To add/update product quantity for franchise stock--- End

            } else {
                $json['error'] = $this->language->get('error_action');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function validateBuyerInvoiceId($invoice_no) {
        $error_msg['error'] = '';
        if (!empty($invoice_no['error'])) {
            if (!empty($invoice_no['error']['seller_not_invoice_generate'])) {
                $error_msg['error'] .= '<b>Seller(s) are STILL NOT INVOICED BY SELLER.</b></br>';
                foreach ($invoice_no['error']['seller_not_invoice_generate'] as $seller) {
                    $error_msg['error'] .= '<span>' . $seller['model'] . ', ' . $seller['company'] . ' [<b>' . $seller['nickname'] . '</b>]</span> </br>';
                }
            }
            if (!empty($invoice_no['error']['seller_not_marked_received'])) {
                $error_msg['error'] .= '</br><b>NOT MARKED RECEIVED. If you have Marked Issue, please get Debit Note generated. If you have Marked Received, Please contact Tech Admin..</b></br>';
                foreach ($invoice_no['error']['seller_not_marked_received'] as $seller) {
                    $error_msg['error'] .= '<span>' . $seller['model'] . ', ' . $seller['company'] . ' [<b>' . $seller['nickname'] . '</b>]</span> </br>';
                }
            }
            if (!empty($invoice_no['error']['wsb_seller_not_marked_received'])) {
                $error_msg['error'] .= '</br><b>WSB SELLER: NOT MARKED RECEIVED. </b></br>';
                foreach ($invoice_no['error']['wsb_seller_not_marked_received'] as $seller) {
                    $error_msg['error'] .= '<span>' . $seller['model'] . ', ' . $seller['company'] . ' [<b>' . $seller['nickname'] . '</b>]</span> </br>';
                }
            }
            if (!empty($invoice_no['error']['seller_pickup_issue'])) {
                $error_msg['error'] .= '</br><b>Pickup status are MARKED ISSUE. Please get Debit Note generated and GOODS RETURNED back to seller with DN </b></br>';
                foreach ($invoice_no['error']['seller_pickup_issue'] as $seller) {
                    $error_msg['error'] .= '<span>' . $seller['model'] . ', ' . $seller['company'] . ' [<b>' . $seller['nickname'] . '</b>]</span> </br>';
                }
            }
            if (!empty($invoice_no['error']['buyer_invoice_will_not_generate'])) {
                $error_msg['error'] .= $invoice_no['error']['buyer_invoice_will_not_generate'];
            }
            if (!empty($invoice_no['error']['operation_status_issue'])) {
                $error_msg['error'] .= $invoice_no['error']['operation_status_issue'];
            }
            
            if (!empty($invoice_no['error']['gst_no_mismatch'])) {
                $error_msg['error'] .= $invoice_no['error']['gst_no_mismatch'];
            }
            
            if (!empty($invoice_no['error']['wsb_credit_low'])) {
                $error_msg['error'] .= $invoice_no['error']['wsb_credit_low'];
            }

            if (!empty($invoice_no['error']['rbl'])) {
                $error_msg['error'] .= $invoice_no['error']['rbl'];
            }

            return $error_msg;
        }
    }

    // used in order details
    public function cancelInvoiceNo() {

        $this->load->language('sale/order');
        $json = array();

        if (empty($this->request->get['order_id']) || empty($this->request->get['suborder_id'])) {
            $json['error'] = $this->language->get('error_action');
        } else {

            $order_id = (int) $this->request->get['order_id'];
            $suborder_id = $this->request->get['suborder_id'];

            $this->load->model('sale/order');
            $invoice_no = $this->model_sale_order->cancelInvoiceNo($order_id, $suborder_id);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    // used for history tab
    public function history() {

        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['histories'] = array();

        $this->load->model('sale/order');

        $results = $this->model_sale_order->getOrderHistories($this->request->get['order_id'], $this->request->get['suborder_id'], ($page - 1) * 10, 10);
        foreach ($results as $result) {
            $data['histories'][] = array(
                'notify_email' => $result['notify_email'] ? $data['text_yes'] : $data['text_no'],
                'status' => $result['status'],
                'comment' => nl2br($result['comment']),
                'notes' => nl2br($result['notes']),
                'date_added' => date($data['datetime_format'], strtotime($result['date_added'])),
                'notify_sms' => $result['notify_sms'] ? $data['text_yes'] : $data['text_no'],
                'user' => $result['user']
            );
        }

        $history_total = $this->model_sale_order->getTotalOrderHistories($this->request->get['order_id'], $this->request->get['suborder_id']);

        $pagination = new Pagination();
        $pagination->total = $history_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('sale/order/history', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&suborder_id=' . $this->request->get['suborder_id'] . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

        $this->response->setOutput($this->load->view('sale/order_history.tpl', $data));
    }

    public function invoice() {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        $data['title'] = $this->language->get('text_invoice');

        if ($this->request->server['HTTPS']) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }

        $data['lang'] = $this->language->get('code');


        $this->load->model('sale/order');

        $this->load->model('setting/setting');

        $data['orders'] = array();

        $orders = array();

        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } elseif (isset($this->request->get['order_id'])) {
            $orders[] = $this->request->get['order_id'];
        }

        foreach ($orders as $order_id) {
            $order_info = $this->model_sale_order->getOrder($order_id);

            if ($order_info) {

                // Getting Tin Number of Payment and Shipping address
                $payment_tin_no = '';
                $shipping_tin_no = '';
                $this->load->model('sale/custom_field');
                $data['account_custom_fields'] = array();
                $custom_fields = $this->model_sale_custom_field->getCustomFields();

                foreach ($custom_fields as $custom_field) {
                    if ($custom_field['type'] == 'text' && $custom_field['location'] == 'address' && $custom_field['name'] == 'TIN Number') {

                        if (isset($order_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
                            if (!($this->request->get['type'] == 'b2c'))
                                $shipping_tin_no = $order_info['shipping_custom_field'][$custom_field['custom_field_id']];
                        }

                        if (isset($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                            if (!($this->request->get['type'] == 'b2c'))
                                $payment_tin_no = $order_info['payment_custom_field'][$custom_field['custom_field_id']];
                        }
                    }
                }

                $store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

                if ($store_info) {
                    $store_address = $store_info['config_address'];
                    $store_email = $store_info['config_email'];
                    $store_telephone = $store_info['config_telephone'];
                    $store_fax = $store_info['config_fax'];
                } else {
                    $store_address = $this->config->get('config_address');
                    $store_email = $this->config->get('config_email');
                    $store_telephone = $this->config->get('config_telephone');
                    $store_fax = $this->config->get('config_fax');
                }

                if ($order_info['invoice_no']) {
                    $invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                } else {
                    $invoice_no = $order_info['order_no'];
                }


                if (!$payment_tin_no) {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}';
                } else {
                    $format = '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => trim($order_info['payment_firstname']),
                    'lastname' => trim($order_info['payment_lastname']),
                    'company' => ( (!$payment_tin_no and $order_info['payment_company']) ? ('c/o ' . $order_info['payment_company']) : ($order_info['payment_company']) ),
                    'address_1' => $order_info['payment_address_1'],
                    'address_2' => $order_info['payment_address_2'],
                    'city' => trim($order_info['payment_city']),
                    'postcode' => ( $order_info['payment_postcode'] ? ('- ' . $order_info['payment_postcode']) : '' ),
                    'zone' => $order_info['payment_zone'],
                    'zone_code' => $order_info['payment_zone_code'],
                    'country' => $order_info['payment_country']
                );

                $payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                if (!$shipping_tin_no) {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}';
                } else {
                    $format = '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => trim($order_info['shipping_firstname']),
                    'lastname' => trim($order_info['shipping_lastname']),
                    'company' => ( (!$shipping_tin_no and $order_info['shipping_company']) ? ('c/o ' . $order_info['shipping_company']) : ($order_info['shipping_company']) ),
                    'address_1' => $order_info['shipping_address_1'],
                    'address_2' => $order_info['shipping_address_2'],
                    'city' => trim($order_info['shipping_city']),
                    'postcode' => ( $order_info['shipping_postcode'] ? ('- ' . $order_info['shipping_postcode']) : '' ),
                    'zone' => $order_info['shipping_zone'],
                    'zone_code' => $order_info['shipping_zone_code'],
                    'country' => $order_info['shipping_country']
                );

                $shipping_name = $order_info['shipping_firstname'];
                if ($order_info['shipping_lastname']) {
                    $shipping_name = $shipping_name . " " . $order_info['shipping_lastname'];
                }

                $shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                $this->load->model('tool/upload');

                $product_data = array();

                $products = $this->model_sale_order->getOrderProducts($order_id);

                $this->load->model('catalog/product');

                $total_pieces_order = 0;

                foreach ($products as $product) {

                    // todo check
                    $seller = $this->MsLoader->MsSeller->getSeller(
                            $this->MsLoader->MsProduct->getSellerId($product['product_id']), array(
                        'product_id' => $product['product_id']
                            )
                    );

                    $option_data = array();

                    $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

                    foreach ($options as $option) {
                        if ($option['type'] != 'file') {
                            $value = $option['value'];
                        } else {
                            $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                            if ($upload_info) {
                                $value = $upload_info['name'];
                            } else {
                                $value = '';
                            }
                        }

                        $option_data[] = array(
                            'name' => $option['name'],
                            'value' => $value
                        );
                    }

                    $piece_in_set = (int) $product['piece_in_set'] > 1 ? (int) $product['piece_in_set'] : 1;

                    $total_pieces_order += (int) ($product['total_pieces']);

                    $product_data[] = array(
                        'name' => $product['name'],
                        'model' => $product['model'],
                        'option' => $option_data,
                        'quantity' => $product['quantity'],
                        'price_per_piece' => $this->currency->format($product['price_per_piece'], $order_info['currency_code'], $order_info['currency_value']),
                        'total' => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']),
                        'tax' => $this->currency->format($product['tax']),
                        'total_pieces' => $product['total_pieces']
                    );
                }

                $voucher_data = array();

                $vouchers = $this->model_sale_order->getOrderVouchers($order_id);

                foreach ($vouchers as $voucher) {
                    $voucher_data[] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
                    );
                }

                $total_data = array();

                $totals = $this->model_sale_order->getOrderTotals($order_id);
                $amount_payable = 0;

                foreach ($totals as $total) {

                    if ($total['code'] == 'shipping') {
                        $total['title'] = $this->language->get('text_shipping');
                    } elseif ($total['code'] == 'tax') {
                        $total['title'] = $this->language->get('text_tax');
                    } elseif ($total['code'] == 'total') {
                        $total['title'] = $this->language->get('text_total_amount');
                        $amount_payable = (float) ($total['value']);
                    }

                    $total_data[] = array(
                        'title' => $total['title'],
                        'code' => $total['code'],
                        'text' => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
                    );
                }

                // Generating barcode for if tracking num ber generated
                $barcode_filepath = false;
                $gati_ou = false;
                if ($order_info['tracking_no'] and $order_info['courier_partner'] == 'Gati') {
                    $pincode_info = $this->model_sale_order->getGatiPincodeInfo($order_info['shipping_postcode']);
                    $gati_ou = $pincode_info ? $pincode_info['ou'] : false;
                }

                $data['orders'][] = array(
                    'order_id' => $order_id,
                    'order_no' => $order_info['order_no'],
                    'invoice_no' => $invoice_no,
                    'invoice_date' => date($data['date_format_short'], ($order_info['invoice_date'] != '0000-00-00' ? strtotime($order_info['invoice_date']) : time())),
                    'date_added' => date($data['date_format_short'], strtotime($order_info['date_added'])),
                    'store_name' => $order_info['store_name'],
                    'store_url' => rtrim($order_info['store_url'], '/'),
                    'store_address' => nl2br($store_address),
                    'store_email' => $store_email,
                    'store_telephone' => $store_telephone,
                    'store_fax' => $store_fax,
                    'email' => $order_info['email'],
                    'telephone' => $order_info['telephone'],
                    'shipping_name' => $shipping_name,
                    'shipping_address' => $shipping_address,
                    'shipping_method' => $order_info['shipping_method'],
                    'payment_address' => $payment_address,
                    'payment_method' => $order_info['payment_method'],
                    'payment_code' => trim(strtolower($order_info['payment_code'])),
                    'product' => $product_data,
                    'voucher' => $voucher_data,
                    'total' => $total_data,
                    'order_total' => $this->currency->format($amount_payable, $order_info['currency_code'], $order_info['currency_value']),
                    'comment' => nl2br($order_info['comment']),
                    'shipping_tin_no' => $shipping_tin_no,
                    'payment_tin_no' => $payment_tin_no,
                    'total_pieces_order' => $total_pieces_order,
                    'cform_submit' => $order_info['cform_submit'],
                    'cst_with_cform' => $this->currency->format($order_info['cst_with_cform'], $order_info['currency_code'], $order_info['currency_value']),
                    'refundable_cform' => $this->currency->format($order_info['refundable_cform'], $order_info['currency_code'], $order_info['currency_value']),
                    'wayBillReqd' => $this->model_sale_order->isWayBillReqd($order_id),
                    'tracking_no' => $order_info['tracking_no'],
                    'barcode' => $barcode_filepath,
                    'gati_ou' => $gati_ou
                );
            }
        }

        if (isset($this->request->get['type'])) {
            if ($this->request->get['type'] == 'detail') {
                $data['base'] = $data['base'] . '../';
                $this->response->setOutput($this->load->view('../../../catalog/view/theme/default/template/account/order_invoice_detail.tpl', $data));
            } elseif ($this->request->get['type'] == 'b2b') {
                $this->response->setOutput($this->load->view('sale/order_invoice_b2b.tpl', $data));
            } elseif ($this->request->get['type'] == 'b2c') {
                $this->response->setOutput($this->load->view('sale/order_invoice_b2c.tpl', $data));
            }
        }
    }

    public function api() {
        $this->load->language('sale/order');

        if ($this->validate()) {
            // Store
            if (isset($this->request->get['store_id'])) {
                $store_id = $this->request->get['store_id'];
            } else {
                $store_id = 0;
            }

            $this->load->model('setting/store');

            $store_info = $this->model_setting_store->getStore($store_id);

            if ($store_info) {
                $url = $store_info['ssl'];
            } else {
                $url = HTTPS_CATALOG;
            }
            if (isset($this->request->get['api'])) {
                // Include any URL perameters
                $url_data = array();

                foreach ($this->request->get as $key => $value) {
                    if ($key != 'route' && $key != 'token' && $key != 'store_id') {
                        $url_data[$key] = $value;
                    }
                }

                $curl = curl_init();

                // Set SSL if required
                if (substr($url, 0, 5) == 'https') {
                    curl_setopt($curl, CURLOPT_PORT, 443);
                }

                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, $url . 'index.php?route=' . $this->request->get['api'] . ($url_data ? '&' . http_build_query($url_data) : ''));

                if ($this->request->post) {
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->request->post));
                }

                curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

                $json = curl_exec($curl);

                curl_close($curl);
            }
        } else {
            $response = array();

            $response['error'] = $this->error;

            $json = json_encode($response);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($json);
    }

    /**
     * Send email to sellers for order
     */
    public function sendOrderEmailToSeller() {

        $this->load->model('sale/order');

        $seller_id = $this->request->get['seller_id'];
        $order_id = $this->request->get['order_id'];
        $suborder_id = $this->request->get['suborder_id'];

        $this->model_sale_order->sendSellerMail($order_id, $suborder_id, $seller_id, '', true);
        $mail_count = $this->model_sale_order->getSellerMailLog($order_id, $suborder_id, $seller_id);
        $mail_log_count = $this->model_sale_order->getSellerMailLogCount($order_id, $suborder_id, $seller_id);
        $arr_response = array('success' => 'Email sent', 'mail_count' => $mail_count, 'mail_log_count'=>$mail_log_count);

        $json = json_encode($arr_response);

        echo $json;
    }

    public function downloadSellerPdf() {
        if (!$this->user->hasPermission('view', 'sale/order')) {
            $data = base64_decode($this->request->post['html']);
            require_once(DIR_SYSTEM . 'library/tcpdf/tcpdf.php');
            require_once(DIR_SYSTEM . 'library/tcpdf/config/tcpdf_config.php');
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor(PDF_AUTHOR);
            $pdf->SetTitle('Wholesalebox Invoice');
            $pdf->SetSubject('Wholesalebox Invoice');
            $pdf->SetKeywords('Wholesalebox, Buyer, Invoice');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            // set margins
            $pdf->SetMargins(5, 0, 5);
            //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            // ---------------------------------------------------------
            // set font
            $pdf->SetFont('times', 'B', 12);
            // add a page
            $pdf->AddPage();
            $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
            $pdf->SetFont('times', '', 8);
            $pdf->writeHTML($data, true, false, false, false, '');
            //Close and output PDF document
            $action = 'F';
            $file_path = DIR_DOWNLOAD . "seller_product_breakups.pdf";
            $pdf->Output($file_path, $action);
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-disposition: attachment; filename=' . basename($file_path));
            header('Expires: 0');
            header('Cache-Control: no-cache');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            ob_clean();
            flush();
            readfile($file_path);
            exit();
        } else {
            //exit("You have no permission for download this pdf.");
        }
    }

    //tab seller-product-break-up

    public function sellerproductbreakup() {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        $data['title'] = $this->language->get('text_shipping');

        if ($this->request->server['HTTPS']) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }
        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }
        if (isset($this->request->get['suborder_id'])) {
            $suborder_id = $this->request->get['suborder_id'];
        } else {
            $suborder_id = 0;
        }
        $this->load->model('sale/order');
        $selector = array('order' => array());
        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, '', $selector);
        $order_info = $order_info['order'];
        $order_no = $order_info['order_no'];


        $seller_products = $this->model_sale_order->getProductsBySeller($order_id, $suborder_id, '', array('YES', 'SELLER_APPROVED', 'SELLER_PARTIAL'));
        $sellers = array();
        $indexs = array();
        $seller_breakup = array();
        $seller_totals = array();
        $seller_company = array();
        $data['show_store_sales_notice'] = false;
        // Getting list of unique sellers
        foreach ($seller_products['products'] as $product) {
            if ($product['store_sales'] != 'NO') {
                $data['show_store_sales_notice'] = true;
            }
            if (empty($indexs[$product['suborder_id']])) {
                $indexs[$product['suborder_id']] = 1;
            }
            $seller = $product['seller_id'];
            if (!(in_array($seller, $sellers))) {
                array_push($sellers, $seller);
                $seller_breakup[$seller] = array();
                $seller_company[$seller] = $seller_products['sellers'][$product['seller_id']]['company'] . " - " .
                        $seller_products['sellers'][$product['seller_id']]['nickname'];
                $tmp_total = 0;
            }

            if (!$product['comment']) {
                // Singles - Will probably have options
                $options = $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

                if ($options)
                    $product['comment'] = $data['text_singles_set_desc'];

                foreach ($options as $option) {

                    if ($option['type'] != 'file') {
                        $product['comment'] = $product['comment'] . " " . $option['name'] . " " . $option['value'];
                    }
                }
            }

            array_push($seller_breakup[$seller], array(
                'index' => $indexs[$product['suborder_id']],
                'order_product_id' => $product['order_product_id'],
                //'order_no' => $product['order_no'],
                'image' => $seller_products['images'][$product['product_id']],
                'sku' => $product['seller_sku'],
                'set_description' => $product['comment'],
                'quantity' => $product['quantity'],
                'model' => $product['model'],
                'store_sales' => $product['store_sales'],
                'total_pieces' => $product['total_pieces'],
                'transfer_price' => $this->currency->format($product['transfer_price_per_piece'], $order_info['currency_code'], $order_info['currency_value']),
                'amount' => $this->currency->format((int) $product['total_pieces'] * (float) $product['transfer_price_per_piece'], $order_info['currency_code'], $order_info['currency_value'])));

            if (!empty($indexs[$product['suborder_id']])) {
                $indexs[$product['suborder_id']] ++;
            }
            $tmp_total += (int) $product['total_pieces'] * (float) $product['transfer_price_per_piece'];
            $seller_totals[$seller] = $this->currency->format($tmp_total, $order_info['currency_code'], $order_info['currency_value']);
        }

        $data['sellers'] = $sellers;
        $data['seller_breakup'] = $seller_breakup;
        $data['seller_totals'] = $seller_totals;
        $data['seller_company'] = $seller_company;
        $data['order_no'] = $order_no;
        $data['suborder_id'] = $suborder_id;
        $data['dwnld_link'] = $this->url->link('sale/order/downloadSellerPdf', '&token=' . $this->request->get['token'], 'SSL');

        $this->response->setOutput($this->load->view('sale/seller_product_breakup.tpl', $data));
    }
    public function getGatiDocket()
    {
        $this->load->model('sale/order');
        $service_type = $this->request->get['gati_service_type'];
        $docket = $this->model_sale_order->getGatiDocket($service_type);
        echo trim($docket);
    }
    public function courierApi() {
        
        $this->load->model('sale/order');
        $this->load->model('sale/shipping_label');
        $this->load->model('sale/courier_dockets');
        $this->load->model('logistic/dotzot');
        $this->load->model('logistic/connect_india');
        
        $courier = '';
        if (!empty($this->request->get['courier'])) {
            $courier = $this->request->get['courier'];
        }

        $proceed = true;
        $res_error = array();
        
        if (!empty($this->request->get['order_id']) && 
            !empty($this->request->get['suborder_id']) && 
            !empty($this->request->get['warehouse_id'])
        ) {
            $order_id       = $this->request->get['order_id'];
            $suborder_id    = $this->request->get['suborder_id'];
            $warehouse_id   = $this->request->get['warehouse_id'];
                    
            $buyer_invoice  = new BuyerInvoice($this);
            $productDetails = $buyer_invoice->getOrderProductsDetailWithOrderInfo($order_id, $suborder_id);
            $totals         = $buyer_invoice->getTotals($order_id, $suborder_id);
            $warehouseInfo  = $this->model_sale_shipping_label->getAddress($warehouse_id);
             
            if(empty($productDetails['InvoiceNo'])) { 
                $proceed = false;
                $res_error['error'] = "Invoice number not generated for this order!!";
            }
            if(empty($totals)) { 
                $proceed = false;
                $res_error['error'] = "Error in order total amount calculation";
            }
            if(empty($warehouseInfo)) { 
                $proceed = false;
                $res_error['error'] = "Warehouse value can not be blank.";
            }
            
        } else {
            $proceed = false;
            $res_error['error'] = "Invalid API data (order id OR sub-order id)";
        }
        
        if(!$proceed) { 
            $json = json_encode($res_error);
            $this->response->addHeader('Content-Type: application/json');
            return $this->response->setOutput($json);
        }

        $param = array(
                    'registry'      => $this->registry,
                    'db'            => $this->db,
                    'courier_name'  => $courier,
                    'request'       => $this->request->get,
                    'orderInfo'     => $productDetails,
                    'totals'        => $totals,
                    'warehouseInfo' => $warehouseInfo
                );

        $courierObject  = CourierFactory::build($courier,$param);
        if( is_object($courierObject) ) {
            $response  =  $courierObject->process();
        } else { 
            $response  = $courierObject;
        }
        
        $json = json_encode($response);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($json);
        
    }
    
    public function send_file($name) {
        $today = Date('d_M_y');
        OB_END_CLEAN();
        $path = '../download/' . $today . '/' . $name;
        IF (!IS_FILE($path) or CONNECTION_STATUS() != 0)
            return(FALSE);
        HEADER("Cache-Control: no-store, no-cache, must-revalidate");
        HEADER("Cache-Control: post-check=0, pre-check=0", FALSE);
        HEADER("Pragma: no-cache");
        HEADER("Expires: " . GMDATE("D, d M Y H:i:s", MKTIME(DATE("H") + 2, DATE("i"), DATE("s"), DATE("m"), DATE("d"), DATE("Y"))) . " GMT");
        HEADER("Last-Modified: " . GMDATE("D, d M Y H:i:s") . " GMT");
        HEADER("Content-Type: application/octet-stream");
        HEADER("Content-Length: " . (string) (filesize($path)));
        HEADER("Content-Disposition: inline; filename=$name");
        HEADER("Content-Transfer-Encoding: binary\n");
        IF ($file = fopen($path, 'rb')) {
            WHILE (!feof($file) and ( CONNECTION_STATUS() == 0)) {
                print(fread($file, 1024 * 8));
                FLUSH();
            }
            fclose($file);
        }
        return ((CONNECTION_STATUS() == 0) and ! CONNECTION_ABORTED());
    }

    // Used to apply advance for a whole Order
    public function applyAdvance() {

        $this->load->language('sale/order');

        $json = array();

        if (!empty($this->request->get['order_id']) and ! empty($this->request->get['advance'])) {
            $order_id = (int) ($this->request->get['order_id']);
            $order_no = $this->request->get['order_no'];
            $advance = (float) ($this->request->get['advance']);
            $adv_pay_date = $this->request->get['adv_payment_date'];
            $adv_adtinal_remrks = $this->request->get['adv_adtinal_remrks'];
            $sales_person_id = $this->request->get['sales_person_id'];
            $sales_person_name = $this->request->get['sales_person_name'];
            $adv_payment_date = date("Y-m-d H:i:s", strtotime($adv_pay_date));
            $serialize_json = serialize($this->request->get);
            $result = OrderPayment::applyCashAdvance($this, $advance, $order_id, $order_no, $adv_payment_date, $adv_adtinal_remrks, $sales_person_id, $sales_person_name, $serialize_json
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // Used to apply advance for a whole Order
    public function applyWsbCreditAmount() {

        $this->load->language('sale/order');

        $json = array();

        if (
            !empty($this->request->get['order_id']) &&
            !empty($this->request->get['amount'])
        ) {
            $order_id         = (int) ($this->request->get['order_id']);
            $order_no         = $this->request->get['order_no'] ?? '';
            $amount           = $this->request->get['amount'] ?? '';
            $remarks          = $this->request->get['adtinal_remrks'];
            $serialize_json   = serialize($this->request->get);
            $result = OrderPayment::applyWsbCreditAmount($this, $amount, $order_id, $order_no, $remarks, $serialize_json );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    // Used to apply promocode for a whole Order
    public function applyPromoCodeDiscount() {

        $this->load->language('sale/order');

        $json = array();

        if (!empty($this->request->get['order_id']) && !empty($this->request->get['promo_amount'])) {
            $json = $this->_validatePromocodeDetails($this->request->get);
            if (empty($json)) {
                $order_id = (int) ($this->request->get['order_id']);
                $order_no = $this->request->get['order_no'];
                $promo_amount = (float) ($this->request->get['promo_amount']);
                $promo_code = $this->request->get['promo_code'];
                $promo_code_against = $this->request->get['promo_code_against'];
                $order_no_against = $this->request->get['order_no_against'];
                $promo_adtinal_remrks = $this->request->get['promo_adtinal_remrks'];
                $json_format = json_encode($this->request->get);
                $reference = 'Promocode: ' . $promo_code . ' applied against Order No.' . $order_no_against . ' due to ' . $promo_code_against . (!empty($promo_adtinal_remrks) ? ('. Additional Remarks: ' . $promo_adtinal_remrks) : '');

                $payment_array = array(
                    'order_id' => $order_id,
                    'merchant_txn_id' => $promo_code,
                    'order_no' => $order_no,
                    'txn_status' => 'SUCCESS',
                    'payment_mode' => 'Coupon - ' . $promo_code,
                    'amount' => $promo_amount,
                    'payment_gateway' => 'coupon',
                    'bank_transfer_mode' => 'not_applicable',
                    'successfull' => '1',
                    'reference' => $reference,
                    'payment_link' => $reference,
                    'json_format' => $json_format,
                    'user_id' => $this->user->getId(),
                    'sales_staff_id' => 0
                );
                $result = OrderPayment::insertOrderPayment($this->db, $payment_array);
                $json['success'] = 'Updated successfully.'; 
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function _validatePromocodeDetails($data = array()) {
        $json = array();
        $json_string = '';
        if (empty($data['order_id']) ||
                empty($data['customer_id']) ||
                empty($data['promo_code_against'])) {
            $json_string .= 'Promo code against is required.';
        }
        if (($data['promo_code_against'] == 'Client_Retention' ||
                $data['promo_code_against'] == 'Extra_Discount')) {
            //Do nothing
        } else {
            if (empty($data['order_no_against'])) {
                $json_string .= 'Order No Against is required for the selected reason.';
            }
        }
        $customer_id = 0;
        if (!empty($data['order_no_against'])) {
            $customer_id = (int)OrderInfo::getCustomerIdFromOrderNo($this->db, $data['order_no_against']);
        }
        if ( empty($customer_id) ) {
            $json_string .= 'Order No ' . $data['order_no_against'] . ' does not exists in our records. Enter correct Order No!';
        } elseif ((int)$customer_id !== (int)$data['customer_id']) {
            $json_string .= 'Order No ' . $data['order_no_against'] . ' does not exist against the current Order No ' . $data['order_no'] . '\'s customer (cid: ' . $data['customer_id'] . ')';
        }

        if (!empty($json_string)) {
            $json['error'] = $json_string;
        }
        return $json;
    }

    //get order information from order table by particular order id by vikas
    // order_edit address
    public function getInformationByOrderId() {
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('sale/order', $data);

        $this->load->model('sale/order');

        $data['token'] = $this->session->data['token'];

        $order_id = $this->request->post['order_id'];
        $suborder_id = $this->request->post['suborder_id'];

        $order_information = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id);

        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();

        $this->load->model('localisation/zone');
        $data['zones'] = $this->model_localisation_zone->getZonesByCountryId($order_information['order']['payment_country_id']);

        // Custom Fields
        $this->load->model('sale/custom_field');
        $data['custom_fields'] = $this->model_sale_custom_field->getCustomFields();

        $data['payment_firstname'] = $order_information['order']['shipping_firstname'];
        $data['payment_lastname'] = $order_information['order']['shipping_lastname'];
        $data['payment_company'] = $order_information['order']['shipping_company'];
        $data['payment_telephone'] = $order_information['order']['telephone'];
        $data['payment_address_1'] = $order_information['order']['shipping_address_1'];
        $data['payment_address_2'] = $order_information['order']['shipping_address_2'];
        $data['payment_city'] = $order_information['order']['shipping_city'];
        $data['payment_postcode'] = $order_information['order']['shipping_postcode'];
        $data['payment_country'] = $order_information['order']['shipping_country'];
        $data['payment_country_id'] = $order_information['order']['shipping_country_id'];
        $data['payment_zone'] = $order_information['order']['shipping_zone'];
        $data['payment_zone_id'] = $order_information['order']['shipping_zone_id'];
        $data['gst_number'] = $order_information['order']['gst_number'];
        $data['gst'] = $order_information['suborder'][$suborder_id]['gst'];
        $data['payment_custom_field'] = unserialize($order_information['order']['shipping_custom_field']);

        $data['edit_address_detail'] = $this->url->link('sale/order/order_update_address', 'token=' . $this->session->data['token'] . '&order_id=' . (int) $order_id . '&suborder_id=' . $suborder_id . '&type=edit_address', 'SSL');
        $this->response->setOutput($this->load->view('sale/order_edit_address.tpl', $data));
    }

    //update order information in order table by particular order id by vikas
    // order_edit_address
    public function order_update_address() {
        $this->load->model('sale/order');
        $this->request->post['order_id'] = $this->request->get['order_id'];
        $this->request->post['suborder_id'] = $this->request->get['suborder_id'];
        $country_id = $this->request->post['country'];

        if (!empty($this->request->post['zone'])) {
            $this->load->model('localisation/country');
            $country_name_list = $this->model_localisation_country->getCountry($country_id);
        }
        if (!empty($this->request->post['zone'])) {
            $zone_id = $this->request->post['zone'];
            $this->load->model('localisation/zone');
            $zone_name_list = $this->model_localisation_zone->getZone($zone_id);
        }

        $this->request->post['country_name'] = $country_name_list['name'];
        $this->request->post['zone_name'] = $zone_name_list['name'];

        $this->model_sale_order->updateEditAdress($this->request->post);

        $url = '';

        if (isset($this->request->get['order_id'])) {
            $url .= '&order_id=' . $this->request->get['order_id'];
            $url .= '&suborder_id=' . $this->request->get['suborder_id'];
        }
        $this->response->redirect($this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    }

    //get Zone by Country id by vikas
    // order_edit_address
    public function getZones() {
        $this->load->model('localisation/zone');
        $data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->request->post['country_id']);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    //again mail send to buyer
    public function resendMailToBuyer() {
        $this->load->model('sale/order');

        $this->model_sale_order->resendMailToBuyer($this->request->post['order_id'], $this->request->post['suborder_id']);
        $arr_response = 'Email sent';

        //$json = json_encode($arr_response);

        echo $arr_response;
    }

    //apply change Shipping Charge by vikas (14-06-2016)
    public function changeShippingCharge() {
        $this->load->language('sale/order');

        $json = array();

        if (!empty($this->request->get['order_id']) && !empty($this->request->get['suborder_id']) && isset($this->request->get['shipping_charge'])) {

            $order_id = (int) ($this->request->get['order_id']);
            $suborder_id = $this->request->get['suborder_id'];
            $shipping_value = (float) ($this->request->get['shipping_charge']);

//            $this->load->model('sale/order');
//            $result = $this->model_sale_order->changeShippingCharge($shipping_value, $order_id, $suborder_id);
            $result = OrderEdit::changeShippingCharge($this->db, $shipping_value, $order_id, $suborder_id);

            if ($result) {
                $json['payable'] = (float) $result;
            } else {
                $json['error'] = $this->language->get('error_action');
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    //using in shipping lable tab List of shipping Label
    public function shippingLabel() {
        $this->load->model('sale/shipping_label');
        $this->load->model('sale/order');
        $this->load->language('sale/shipping_label');

            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }

            if (isset($this->request->get['suborder_id'])) {
                $suborder_id = $this->request->get['suborder_id'];
            } else {
                $suborder_id = 0;
            }

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }

            $data['shipping_label'] = array();
            $start = ($page - 1) * 10;
            $limit = 10;
            $data_shipping_label = $this->model_sale_shipping_label->getShippingLabel($order_id, $suborder_id, $start, $limit);

            foreach ($data_shipping_label as $shipping_label) {
                
                $download_link = $this->securefiledownload->getDownloadLink('shipping_label', $shipping_label['file_name']);
                
                $data['shipping_label'][] = array(
                    'shipping_label_id' => $shipping_label['shipping_label_id'],
                    'warehouse' => $shipping_label['warehouse_name'] . '<br>' .
                    $shipping_label['address_1'] . '<br>' .
                    $shipping_label['address_2'] . '<br>' .
                    $shipping_label['city'] . ' - ' .
                    $shipping_label['postcode'] . '<br>' .
                    $shipping_label['zone_name'] . ' - ' .
                    $shipping_label['country_name'] . '<br>' .
                    $shipping_label['telephone'],
                    'courier_name' => $shipping_label['courier_name'],
                    'tracking_no' => $shipping_label['tracking_no'],
                    'weight' => $shipping_label['weight'],
                    'download_link' => $download_link
                );
            }

            $data['token'] = $this->session->data['token'];


            $shipping_label_total = $this->model_sale_shipping_label->getTotalShippingLabel($order_id, $suborder_id);

            $pagination = new Pagination();
            $pagination->total = $shipping_label_total;
            $pagination->page = $page;
            $pagination->limit = 10;
            $pagination->url = $this->url->link('sale/order/shippingLabel', 'token=' . $this->session->data['token'] . '&order_id=' . $order_id . '&suborder_id=' . $suborder_id . '&page={page}', 'SSL');

            $data['pagination'] = $pagination->render();

            $data['results'] = sprintf($this->language->get('text_pagination'), ($shipping_label_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($shipping_label_total - 10)) ? $shipping_label_total : ((($page - 1) * 10) + 10), $shipping_label_total, ceil($shipping_label_total / 10));

        $this->response->setOutput($this->load->view('sale/shipping_label.tpl', $data));
    }

    //using in courier tab get search address by ajax
    public function getSearchAddress() {
        $json = array();

        $this->load->model('sale/shipping_label');

        if (isset($this->request->post['filter_warehouse'])) {
            $search_warehouse = $this->request->post['filter_warehouse'];
        } else {
            $search_warehouse = '';
        }

        if (isset($this->request->post['filter_city'])) {
            $search_city = $this->request->post['filter_city'];
        } else {
            $search_city = '';
        }

        $filter_data = array(
            'search_warehouse' => $search_warehouse,
            'search_city' => $search_city
        );

        //get warehouse addresses
        $company_addresses = $this->model_sale_shipping_label->getAddresses($filter_data);

        

        foreach ($company_addresses as $address) {
            
            $warehouse_city = strtolower(str_replace(' ','_',$address['city']));
            if(array_key_exists($warehouse_city,FEDEX_ACCESS)) {
              $fedex_account_numbers = array_keys(FEDEX_ACCESS[$warehouse_city]);      
            }else{
              $fedex_account_numbers = array_keys(FEDEX_ACCESS['jaipur']);   
            }

            $json[] = array(
                'warehouse_id' => $address['warehouse_id'],
                'warehouse_name' => $address['warehouse_name'],
                'address_1' => $address['address_1'],
                'address_2' => $address['address_2'],
                'city' => $address['city'],
                'postcode' => $address['postcode'],
                'zone_name' => $address['zone_name'],
                'country_name' => $address['country_name'],
                'telephone' => $address['telephone'],
                'gati_vendor_code' => $address['gati_vendor_code'],
                'bluedart_vendor_code' => $address['bluedart_vendor_code'],
                'bluedart_surface_customer_code' => $address['bluedart_surface_customer_code'],
                'bluedart_apex_customer_code' => $address['bluedart_apex_customer_code'],
                'bluedart_origin_area' => $address['bluedart_origin_area'],
                'fedex_account_numbers' => $fedex_account_numbers
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    //using in shipping label and courier tab generate shipping label pdf
    public function shippingLabelPdf() {
        $this->load->model('sale/shipping_label');
        $this->load->language('sale/shipping_label');
        $this->load->language('sale/order');
        $this->load->model('sale/courier_dockets');

        if (isset($this->request->get['shipping_label_id'])) {
            $shipping_label_id = $this->request->get['shipping_label_id'];
        } else {
            $shipping_label_id = 0;
        }

        
        if (isset($this->request->get['no_of_pkg'])) {
            $no_of_pkg = $this->request->get['no_of_pkg'];
        } else {
            $no_of_pkg = 0;
        }
        
        if (!isset($this->request->get['isNoDocket'])) {
            $isNoDocket = 1;
        } else {
            $isNoDocket = 0;
        }
        
        //echo $shipping_label_id; die;
        // warehouse address and weight and docket no. show on shipping label pdf
        $shipping_label_address = $this->model_sale_shipping_label->getShippingLabelAddress($shipping_label_id);
        $order_id      = $shipping_label_address['order_id'];
        $suborder_id   = $shipping_label_address['suborder_id'];
        $warehouse_id  = $shipping_label_address['from_warehouse_id'];
              
        if(empty($warehouse_id)) {
            throw new \Exception('WarehouseId not found with shipping label id('.$shipping_label_id.') data. ');
        }

        $buyer_invoice  = new BuyerInvoice($this);
        $productDetails = $buyer_invoice->getOrderProductsDetailWithOrderInfo($order_id, $suborder_id);

        $totals         = $buyer_invoice->getTotals($order_id, $suborder_id);
        $order_total = 0;
        if (!empty($totals['total_amt'])) {
            $order_total = round( $totals['total_amt']['value'], 2 );
        }
        $net_payable = 0;
        if (!empty($totals['net_amount'])) {
            $net_payable = round( $totals['net_amount']['value'], 2 );
        }
        
        $order_product_ids = array();
        $goods_label = 'GARMENTS';
        if(!empty($productDetails['itemdtl'])) {
           $order_product_ids = array_column($productDetails['itemdtl'], 'ItemID');
           $product_categories  = $this->model_sale_shipping_label->getOrderProductsCategoryNames($order_product_ids);
           if(!empty($product_categories)){
              $goods_label = $product_categories;  
           }
        }

        $warehouseInfo  = $this->model_sale_shipping_label->getAddress($warehouse_id);
        
        $docket_details = $this->model_sale_courier_dockets->getDocketDetails($shipping_label_address['tracking_no'], $suborder_id);
        $post_values = isset($docket_details['post_values']) ? json_decode($docket_details['post_values'], true) : '';
        
        $data = array();

        if (!file_exists(DIR_IMAGE . '/barcode/')) {
            $oldmask = umask(0);
            mkdir(DIR_IMAGE . '/barcode/', 0775, true);
            umask($oldmask);
        }

        $filepath = '';
        $filepath1 = '';
        if ($shipping_label_address['barcode']) {
          
            if(trim($shipping_label_address['tracking_no'])!=='')
            {
               $filepath = DIR_IMAGE . 'barcode/' . trim($shipping_label_address['tracking_no']) . '.png'; 
            }
            if(trim($shipping_label_address['order_no'])!=='')
            {
               $filepath1 = DIR_IMAGE . 'barcode/' . trim($shipping_label_address['order_no']) . '.png';
            }
            
            $text = trim($shipping_label_address['tracking_no']);
            $text1 = trim($shipping_label_address['order_no']);
            $size = 60;
            $orientation = 'horizontal';
            $code_type = 'code128';
            $print = true;
            barcode($filepath, $text, $size, $orientation, $code_type, $print);
            barcode($filepath1, $text1, $size, $orientation, $code_type, $print);
        }

        if (trim(strtolower($shipping_label_address['payment_code'])) == 'cod') {
            $payment_mode = sprintf($this->language->get('text_cash_delivery'), $this->currency->format(ceil($shipping_label_address['total']), 'INR', 1));
        } else {
            $payment_mode = $this->language->get('text_prepaid');
        }

        $courier = '';
        if (isset($shipping_label_address['courier_name']) && trim($shipping_label_address['courier_name']) != '') {
            $courier = ucfirst(strtolower($shipping_label_address['courier_name']));
        }

        $courier_dementions = $this->getCourierDimensions($courier,$docket_details);
        
        $weight = !empty($courier_dementions['weight'])
                        ? $courier_dementions['weight']
                        : '';
        $breadth= !empty($courier_dementions['breadth'])
                        ? $courier_dementions['breadth']
                        : '';
        $height = !empty($courier_dementions['height'])
                        ? $courier_dementions['height']
                        : '';
        $length = !empty($courier_dementions['length'])
                        ? $courier_dementions['length']
                        : '';
        $count = !empty($courier_dementions['count'])
                        ? $courier_dementions['count']
                        : 1;
        $items  = !empty($post_values['Request']['Services']['itemdtl'])
                        ? $post_values['Request']['Services']['itemdtl']
                        : '';
         $service_type = !empty($courier_dementions['service_type'])
                        ? $courier_dementions['service_type']
                        : '';                

        if($docket_details['post_url'] == 'No-Docket-Generation')
        {
            $post_values = json_decode($docket_details['post_values'],true);
            $weight     =  $post_values['weight'];
        }
        $payment_mode = 'cod';
        
        $cod_amount = 0;
        if(isset($docket_details['cod_amount']) && trim($docket_details['cod_amount'])!=''){
            $cod_amount = round($docket_details['cod_amount'],2);
        }else{
            $cod_amount = round($net_payable,2);
        }
        $parcel_amount = 0;
        if(isset($docket_details['parcel_amount']) && trim($docket_details['parcel_amount'])!=''){
            $parcel_amount = round($docket_details['parcel_amount'],2);
        }else{
            $parcel_amount = round($order_total,2);
        }
        $payment_method = 'prepaid';
        $payment_code   = $productDetails['payment_code'] ?? '';
        $payment_code   = strtolower(trim($productDetails['payment_code']));

        if(isset($docket_details['payment_mode']) && trim($docket_details['payment_mode'])!='')
        {
            $payment_method = $docket_details['payment_mode'];
        }else{
            if( $payment_code != "cod" && !in_array($payment_code, CREDIT_PAYMENT_CODES) ){
                $payment_method = 'prepaid';
            } else if(
                ($payment_code == "cod" || in_array($payment_code, CREDIT_PAYMENT_CODES)) 
                && $net_payable > 0
            ) { 
                $payment_method = 'cod';
            }else{
                $payment_method = 'prepaid';
            }
        }
        if(!empty($shipping_label_address['shipping_alternate_telephone'])) {
            $alternate_numbers = json_decode($shipping_label_address['shipping_alternate_telephone'],true);
            $alternate_numbers = array_merge($alternate_numbers, array($shipping_label_address['shipping_telephone']));
        }else{
            $alternate_numbers = array($shipping_label_address['shipping_telephone']);
        }

        $data['shipping_address'] = array(
            'order_no'              => $shipping_label_address['suborder_id'],
            'shipping_customername' => $shipping_label_address['shipping_firstname'] . " " . $shipping_label_address['shipping_lastname'],
            'shipping_company'      => $shipping_label_address['shipping_company'],
            'shipping_address_1'    => $shipping_label_address['shipping_address_1'],
            'shipping_address_2'    => $shipping_label_address['shipping_address_2'],
            'shipping_postcode'     => $shipping_label_address['shipping_postcode'],
            'shipping_city'         => $shipping_label_address['shipping_city'],
            'shipping_zone'         => $shipping_label_address['shipping_zone'],
            'shipping_country'      => $shipping_label_address['shipping_country'],
            'shipping_postcode'     => $shipping_label_address['shipping_postcode'],
            'telephone'             => $shipping_label_address['shipping_telephone'],
            'alternate_numbers'     => $alternate_numbers,
            'payment_method'        => $payment_method,
            'cod_amount'            => $cod_amount, //round($docket_details['cod_amount'],2),
            'parcel_amount'         => $parcel_amount, //round($docket_details['parcel_amount'],2),
            'weight'                => (float) $shipping_label_address['weight'],
            'tracking_no'           => $shipping_label_address['tracking_no'],
            'filepath'              => $filepath,
            'filepath1'             => $filepath1,
            'barcode_img_tracking'  => (trim($shipping_label_address['tracking_no'])!='') ? DIR_IMAGE . 'barcode/' .trim($shipping_label_address['tracking_no']) . '.png' : '',
            'barcode_img_order'     => (trim($shipping_label_address['order_no'])!=='') ? DIR_IMAGE . 'barcode/' . trim($shipping_label_address['order_no']) . '.png' : '',
            'site_logo'             => DIR_IMAGE . 'site_logo.png',
            'no_of_pkg'             => $no_of_pkg,
            'invoice_number'        => $productDetails['invoice_number'],
            'invoice_date'          => $productDetails['invoice_date'],
            'gstin'                 => $productDetails['gstin'],
            'pan_no'                => $productDetails['pan_no'],
            'courier'               => $courier,
            'breadth'               => $breadth,
            'height'                => $height,
            'length'                => $length,
            'count'                 => $count,
            'service_type'          => $service_type,
            'items'                 => $items,
            'goods_label'           => $goods_label,
            'payment_code'          => $productDetails['payment_code']
        );
        
        $customer_code = '';
        if(strtolower(trim($shipping_label_address['courier_name'])) == 'bluedart' && $isNoDocket){
            $customer_code = $warehouseInfo['bluedart_surface_customer_code'];
        }
        
        $data['from_address'] = array(
            'warehouse_name'    => $warehouseInfo['warehouse_name'],
            'address_1'         => $warehouseInfo['address_1'],
            'address_2'         => $warehouseInfo['address_2'],
            'city'              => $warehouseInfo['city'],
            'postcode'          => $warehouseInfo['postcode'],
            'state'             => $warehouseInfo['state'],
            'country'           => $warehouseInfo['country'],
            'telephone'         => $warehouseInfo['telephone'],
            'email'             => $warehouseInfo['email'],
            'customer_code'     => $customer_code,
        );

        $file_name_wout_ext = trim($shipping_label_address['order_no']) . '_' . trim($shipping_label_address['courier_name']) . '_' . trim($shipping_label_address['tracking_no']);    
        $file_links = $this->securefiledownload->generateFileDownload('shipping_label', $file_name_wout_ext); 

       ob_start();
        include(DIR_TEMPLATE . 'sale/shipping-label-pdf-layout.php');    /// need to move this file in template folder
        $html = ob_get_contents();
       ob_get_clean();
       
       //echo $html; die;

       $this->generatePdf($html,$file_links);
        
        // Generating shipping_label file path and links using SecureFileDownload
        // File name will be <order_no>_<courier_name>_<tracking_no>.pdf
        $file_name_wout_ext = trim($shipping_label_address['order_no']) . '_' . trim($shipping_label_address['courier_name']) . '_' .
                trim($shipping_label_address['tracking_no']);
        $file_links = $this->securefiledownload->generateFileDownload('shipping_label', $file_name_wout_ext);

        //adding shipping label to database
        $this->model_sale_shipping_label->updateShippingLabelFilePath($shipping_label_id, $file_links['db_file_string']);

        // Download Shipping Label
        header("Location: " . $file_links['download_link']);
        
    }
    
    public function generatePdf($html,$file_links){
    
      require_once( DIR_SYSTEM . 'library/html2pdf/MyHtml2Pdf.php');
      
      try{
            $html2pdf = new MyHtml2Pdf('P','A4','en', false, 'UTF-8');
            $html2pdf->pdf->SetDisplayMode('fullpage');    
            $html2pdf->writeHTML($html);
            ob_end_clean();
            $html2pdf->output($file_links['clean_file_path'] , 'F');
      } catch (Exception $ex) {

          //echo $ex->getMessage();
      }
        
    }
    
    protected function getCourierDimensions($courier, $docket_details)
    {
        $post_values = isset($docket_details['post_values']) ? $docket_details['post_values'] : array();
        $post_url    = isset($docket_details['post_url']) ? $docket_details['post_url'] : '';
        
        $dimentions = array('weight'=>0,'breadth'=>0,'length'=>0,'height'=>0,'count'=>0,'service_type'=>'');

        
        if($post_url === 'No-Docket-Generation') {
                
            $postValues = json_decode($post_values,true);

            if(isset($postValues['weight']))
            {
                $dimentions['weight'] = (int)$postValues['weight'];
            }
            return $dimentions; 
        }   
                
        
        if(strtolower($courier) == 'gati' && !empty($post_values)){
            
            $postValues = new SimpleXMLElement($post_values);

            if(isset($postValues->details->req->PKGDETAILS->PKG_INFO[0]))
            {
                $dimentions['count']   = (int)$postValues->details->req->NO_OF_PKGS;
                $dimentions['weight']  = (int)$postValues->details->req->PKGDETAILS->PKG_INFO[0]->PKG_WT;
                $dimentions['breadth'] = (int)convertUnit($postValues->details->req->PKGDETAILS->PKG_INFO[0]->PKG_BR,'INCH_TO_CM');
                $dimentions['height']  = (int)convertUnit($postValues->details->req->PKGDETAILS->PKG_INFO[0]->PKG_HT,'INCH_TO_CM');
                $dimentions['length']  = (int)convertUnit($postValues->details->req->PKGDETAILS->PKG_INFO[0]->PKG_LN,'INCH_TO_CM');
            }
        }
        
        if(strtolower($courier) == 'dotzot' && !empty($post_values)){
           
            if(strtolower($docket_details['post_type']) === 'json') {
                $postValues = json_decode($post_values,true);
                
                if(isset($postValues['DocketList'][0]['Weight']))
                {
                    $dimentions['weight'] = (int)$postValues['DocketList'][0]['Weight'];
                }
            
            }else{
                
                $postValues = new SimpleXMLElement($post_values);
                
                if(isset($postValues->DocketList->DocketList->Weight)) {
                    $dimentions['weight'] = (int)$postValues->DocketList->DocketList->Weight;
                }
                if(isset($postValues->DocketList->DocketList->NoOfPieces)) {
                    $dimentions['count'] = (int)$postValues->DocketList->DocketList->NoOfPieces;
                }
                if(isset($postValues->DocketList->DocketList->TypeOfService)) {
                    $service_type = (string)$postValues->DocketList->DocketList->TypeOfService;
                    if(strtolower($service_type) == 'economy') {
                        $dimentions['service_type'] = 'Surface';
                    }else{
                       $dimentions['service_type'] = 'Apex'; 
                    }
                }
            }
        }
        
        if(strtolower($courier) == 'bluedart' && !empty($post_values)){
            
            $postValues = json_decode($post_values,true);
            
            $dimentions['weight'] = isset($postValues['Request']['Services']['ActualWeight'])
                                    ? $postValues['Request']['Services']['ActualWeight']
                                    : '';
            $dimentions['breadth']= isset($postValues['Request']['Services']['Dimensions']['Dimension']['Breadth'])
                                    ? $postValues['Request']['Services']['Dimensions']['Dimension']['Breadth']
                                    : '';
            $dimentions['height'] = isset($postValues['Request']['Services']['Dimensions']['Dimension']['Height'])
                                    ? $postValues['Request']['Services']['Dimensions']['Dimension']['Height']
                                    : '';
            $dimentions['length'] = isset($postValues['Request']['Services']['Dimensions']['Dimension']['Length'])
                                    ? $postValues['Request']['Services']['Dimensions']['Dimension']['Length']
                                    : '';
            $dimentions['count'] = isset($postValues['Request']['Services']['Dimensions']['Dimension']['Count'])
                                ? $postValues['Request']['Services']['Dimensions']['Dimension']['Count']
                                : '';
        }
        if((strtolower($courier) == 'connect-india' || strtolower($courier) == 'connectindia') && !empty($post_values)){
            $postValues = json_decode($post_values,true);
            $dimentions['weight'] = isset($postValues['consignments'][0]['weightInKilogram'])
                                    ? $postValues['consignments'][0]['weightInKilogram']
                                    : '';
            $dimentions['breadth']= isset($postValues['consignments'][0]['widthInMeter'])
                                    ? $postValues['consignments'][0]['widthInMeter']
                                    : '';
            $dimentions['height'] = isset($postValues['consignments'][0]['heightInMeter'])
                                    ? $postValues['consignments'][0]['heightInMeter']
                                    : '';
            $dimentions['length'] = isset($postValues['consignments'][0]['lengthInMeter'])
                                    ? $postValues['consignments'][0]['lengthInMeter']
                                    : '';
        }
        
        if(strtolower($courier) == 'fedex'){
            $postValues = json_decode($post_values,true);

            $dimentions['weight'] = isset($postValues['RequestedShipment']['RequestedPackageLineItems'][0]['Weight']['Value'])
                                    ? $postValues['RequestedShipment']['RequestedPackageLineItems'][0]['Weight']['Value']
                                    : '';
            $dimentions['breadth']= isset($postValues['RequestedShipment']['RequestedPackageLineItems'][0]['Dimensions']['Width'])
                                    ? $postValues['RequestedShipment']['RequestedPackageLineItems'][0]['Dimensions']['Width']
                                    : '';
            $dimentions['height'] = isset($postValues['RequestedShipment']['RequestedPackageLineItems'][0]['Dimensions']['Height'])
                                    ? $postValues['RequestedShipment']['RequestedPackageLineItems'][0]['Dimensions']['Height']
                                    : '';
            $dimentions['length'] = isset($postValues['RequestedShipment']['RequestedPackageLineItems'][0]['Dimensions']['Length'])
                                    ? $postValues['RequestedShipment']['RequestedPackageLineItems'][0]['Dimensions']['Length']
                                    : '';
            if(isset($postValues['RequestedShipment']['ServiceType'])) {
                $dimentions['service_type'] = (strtolower($postValues['RequestedShipment']['ServiceType']) == 'standard_overnight')?'Overnight':'Economy';
            }
        }
       return $dimentions;
    }
    
    /* START
     * @note: separate-separate functions of order-info tabs
     * @author: Vikas, 2016
     */

    protected function orderInfoCustomerAddressDetails(&$data, $prefix, $custom_fields) {
        $this->load->model('tool/upload');
        $order_info = $this->_order_info['order'];
//        echo "<pre>";
//        print_R($order_info);
//        die;

        if ($prefix == 'payment')
            $data[$prefix . '_method'] = $order_info[$prefix . '_method'];

        $data[$prefix . '_firstname'] = $order_info[$prefix . '_firstname'];
        $data[$prefix . '_lastname'] = $order_info[$prefix . '_lastname'];
        $data[$prefix . '_company'] = $order_info[$prefix . '_company'];
        $data[$prefix . '_address_1'] = $order_info[$prefix . '_address_1'];
        $data[$prefix . '_address_2'] = $order_info[$prefix . '_address_2'];
        $data[$prefix . '_city'] = $order_info[$prefix . '_city'];
        $data[$prefix . '_postcode'] = $order_info[$prefix . '_postcode'];
        $data[$prefix . '_zone'] = $order_info[$prefix . '_zone'];
        $data[$prefix . '_country'] = $order_info[$prefix . '_country'];
        $data[$prefix . '_zone_id'] = $order_info[$prefix . '_zone_id'];
        $data[$prefix . '_country_id'] = $order_info[$prefix . '_country_id'];
        $data[$prefix . '_gst_number'] = $order_info['gst_number'];

        $address_custom_field = unserialize($order_info[$prefix . '_custom_field']);

        if(!empty($data['alternate_contact_number'])) {
            $data[$prefix .'_alternate_numbers'] = json_decode($data['alternate_contact_number'], true);
        }

        // Custom fields
        $data[$prefix . '_custom_fields'] = array();

        if (!empty($address_custom_field)) {
            foreach ($custom_fields as $custom_field) {

                if (!empty($custom_field['location']) && $custom_field['location'] == 'address') {
    
                    if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
                        $custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($order_info[$prefix . '_custom_field'][$custom_field['custom_field_id']]);
    
                        if ($custom_field_value_info) {
                            $data[$prefix . '_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $custom_field_value_info['name'],
                                'sort_order' => $custom_field['sort_order']
                            );
                        }
                    }
    
                    if ($custom_field['type'] == 'checkbox' && is_array($order_info[$prefix . '_custom_field'][$custom_field['custom_field_id']])) {
                        foreach ($order_info[$prefix . '_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
                            $custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($custom_field_value_id);
    
                            if ($custom_field_value_info) {
                                $data[$prefix . '_custom_fields'][] = array(
                                    'name' => $custom_field['name'],
                                    'value' => $custom_field_value_info['name'],
                                    'sort_order' => $custom_field['sort_order']
                                );
                            }
                        }
                    }
    
                    if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] != 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
    
                        if (isset($address_custom_field[$custom_field['custom_field_id']])) {
                            $data[$prefix . '_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $address_custom_field[$custom_field['custom_field_id']],
                                'sort_order' => $custom_field['sort_order']
                            );
                        }
                    }
    
                    if ($custom_field['type'] == 'file') {
                        $upload_info = $this->model_tool_upload->getUploadByCode($order_info[$prefix . '_custom_field'][$custom_field['custom_field_id']]);
    
                        if ($upload_info) {
                            $data[$prefix . '_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $upload_info['name'],
                                'sort_order' => $custom_field['sort_order']
                            );
                        }
                    }
                }
            }
        }

        return $this->load->view('sale/order_info_tab_' . $prefix . '_details.tpl', $data);
    }

    /* protected function orderInfoTabProducts(&$data) {

      // Form C
      $data['cform_submit'] = $this->_order_info['suborder'][0]['cform_submit'];
      $data['cst_with_cform'] = $this->currency->format($this->_order_info['suborder'][0]['cst_with_cform']);
      $data['refundable_cform'] = $this->currency->format($this->_order_info['suborder'][0]['refundable_cform']);

      $data['products'] = array();

      $this->load->model('tool/image');
      $this->load->model('catalog/product');

      $data['total_order_pieces'] = 0;
      $product_index = 0;

      // Array for all those order product ids which have option with them
      if ( !empty($this->_order_info['order_option']) )
      $order_options = array_column($this->_order_info['order_option'],
      'order_product_id');

      foreach ($this->_order_info['order_product'] as $product) {

      $option_data = array();
      // Find the corresponding order_option_id row key
      $option_key = false;
      if ( !empty($order_options) )
      $option_key = array_search($product['order_product_id'], $order_options);

      if ($option_key !== false) {

      $option = $this->_order_info['order_option'][$option_key];

      if ($option['type'] != 'file') {
      $option_data[] = array(
      'name'  => $option['name'],
      'value' => $option['value'],
      'type'  => $option['type']
      );
      } else {
      $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

      if ($upload_info) {
      $option_data[] = array(
      'name'  => $option['name'],
      'value' => $upload_info['name'],
      'type'  => $option['type'],
      'href'  => $this->url->link('tool/upload/download', 'token=' . $this->session->data['token'] . '&code=' . $upload_info['code'], 'SSL')
      );
      }
      }
      }


      // updating total pieces and product index
      $data['total_order_pieces'] += $product['total_pieces'];
      $product_index += 1;

      // get product image
      $image = $this->model_catalog_product->getProduct($product['product_id'],
      array('image'))['image'];

      $data['products'][] = array(
      'index'            => $product_index,
      'order_product_id' => $product['order_product_id'],
      'product_id'       => $product['product_id'],
      'name'    	 	   => $product['name'],
      'model'    		   => $product['model'],
      'set_description'  => $product['comment'],
      'image'            => $this->model_tool_image->resize($image, 150,150),
      'option'   		   => $option_data,
      'quantity'		   => $product['quantity'],
      'total_pieces'     => $product['total_pieces'],
      'price_per_piece'  => $this->currency->format($product['price_per_piece'],
      $this->_order_info['order'][0]['currency_code'],
      $this->_order_info['order'][0]['currency_value']),
      'discount_per_piece'=> $this->currency->format($product['discount_per_piece'],
      $this->_order_info['order'][0]['currency_code'],
      $this->_order_info['order'][0]['currency_value']),
      'total'    		   => $this->currency->format($product['total'],
      $this->_order_info['order'][0]['currency_code'],
      $this->_order_info['order'][0]['currency_value']),
      'tax'    		   => $this->currency->format($product['tax'],
      $this->_order_info['order'][0]['currency_code'],
      $this->_order_info['order'][0]['currency_value']),
      'href'     		   => $this->url->link('catalog/product/edit',
      'token=' . $this->session->data['token'] . '
      &product_id=' . $product['product_id'],
      'SSL'),
      );
      }


      $data['totals'] = array();

      foreach ($this->_order_info['order_total'] as $total) {
      $data['totals'][] = array(
      'code' => $total['code'],
      'title' => $total['title'],
      'text'  => $this->currency->format($total['value'],
      $this->_order_info['order'][0]['currency_code'],
      $this->_order_info['order'][0]['currency_value']),
      );
      }

      return $this->load->view('sale/order_info_tab_products.tpl', $data);
      } */

    public function orderInfoTabProducts() {
        $order_id = $this->request->get['order_id'];
        $suborder_id = $this->request->get['suborder_id'];
        $data = array(
            'order_id' => $order_id,
            'suborder_id' => $suborder_id
        );

        $data['token'] = $this->session->data['token'];

        $this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], array('order' => array(),
                    'suborder' => array())
        );

        $product_link = 'catalog/product/edit&token=' . $this->session->data['token'];
        $buyer_invoice = new BuyerInvoice($this);
        $buyer_invoice->setOptions('file_type', 'b2b');
        $buyer_invoice->setOptions('product_link', $product_link);
        $buyer_invoice->setOptions('show_image', TRUE);
        $buyer_invoice->setOptions('only_html', true);
        $buyer_invoice->setOrderInfo($this->_order_info);
        $products = $buyer_invoice->getProductsArrayBySuborderId($order_id, $suborder_id);
        $totals = !empty($products) ? $buyer_invoice->getTotals($order_id, $suborder_id) : array();
        foreach ($products as $product) {
            if ($product['store_pickup']) {
                $data['text_immediate_pickup'] = "Immediate Delivery done from Store";
                break;
            }
        }

        // array of customer comment on perticular product
        $data['customer_comment_array'] = array();
        foreach ($products as $key => $product_data) {
            if(!empty($product_data['customer_comment'])){
                $data['customer_comment_array'][$product_data['order_product_id']] =  array(
                                                                                        'model' => $product_data['model'],
                                                                                        'set_description' => $product_data['comment'],
                                                                                        'customer_comment' => $product_data['customer_comment']);
            }            
        }

        $data['product_info'] = $buyer_invoice->getProductsHtml($products, $totals);
        $this->response->setOutput($this->load->view('sale/order_info_tab_products.tpl', $data));
    }

    public function orderInfoTabOriginalProductStatusInfo() {
        $order_id = $this->request->get['order_id'];
        $suborder_id = $this->request->get['suborder_id'];
        $data = array(
            'order_id' => $order_id,
            'suborder_id' => $suborder_id
        );

        $data['token'] = $this->session->data['token'];

        $this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], array('order' => array(),
                    'suborder' => array())
        );

        $product_link = 'catalog/product/edit&token=' . $this->session->data['token'];
        $buyer_invoice = new BuyerInvoice($this);
        $buyer_invoice->setOptions('file_type', 'b2b');
        $buyer_invoice->setOptions('product_link', $product_link);
        $buyer_invoice->setOptions('show_image', TRUE);
        $buyer_invoice->setOptions('only_html', true);
        $buyer_invoice->setOrderInfo($this->_order_info);
        $buyer_invoice->setOptions('get_all_product', TRUE);

        $products = $buyer_invoice->getProductsArrayBySuborderId($data['order_id'], $data['suborder_id']);
        $totals = $buyer_invoice->getTotals($data['order_id'], $data['suborder_id']);
        $original_product_info = $buyer_invoice->getProductsHtml($products, $totals);
        $this->response->setOutput($original_product_info);
    }

    public function orderInfoTabSellerProductBreak_up() {
        $order_id = $this->request->get['order_id'];
        $order_no = $this->request->get['order_no'];
        $suborder_id = $this->request->get['suborder_id'];
        
        $data = array(
            'order_id' => $order_id,
            'order_no' => $order_no,
            'suborder_id' => $suborder_id,
            'user_id' => $this->user->getId()
        );
        $this->load->autoLoadLanguage('sale/order', $data);

        $data['token'] = $this->session->data['token'];

        $this->load->model('sale/order');
        // Getting seller invoice data
        $seller_invoice = $this->model_sale_order->getSellerInvoices($data['order_id'], $data['suborder_id']);
        $data['breakup_print'] = $this->url->link('sale/order/sellerproductbreakup', 'token=' . $this->session->data['token'] .
                '&order_id=' . $data['order_id'] .
                '&suborder_id=' . $data['suborder_id'], 'SSL');

        // Seller-wise Product Break-up
        $seller_products = $this->model_sale_order->getProductsBySeller($data['order_id'], $data['suborder_id'], '', array('YES',
            'SELLER_APPROVED',
            'SELLER_PARTIAL',
            'REJECTED_WRONG_PRODUCT',
            'REJECTED_SELLER_DAMAGE',
            'REJECTED_WSB_DAMAGE',
            'DAMAGE_BY_COURIER_COMPANY'));
        $sellers = array();
        $seller_breakup = array();
        $seller_totals = array();
        $seller_company = array();

        // Sellers status information
        $seller_statuses = array();

        $data['total_purchase_value'] = 0;
        $invoice_links = array();
        $data['show_store_sales_notice'] = false;
        // Getting list of unique sellers
        $indexs = array();

        $this->load->model('tool/image');

        if (!empty($seller_products)) {
            foreach ($seller_products['products'] as $product) {
                if ($product['store_sales'] != 'NO') {
                    $data['show_store_sales_notice'] = true;
                }
                if (empty($indexs[$product['suborder_id']])) {
                    $indexs[$product['suborder_id']] = 1;
                }
                $seller = $product['seller_id'];

                if (!empty($seller_invoice[$seller])) {
                    $result = array();
                    foreach ($seller_invoice[$seller] as $key => $values) {
                        $result = array_merge($values, array('order_id' => $data['order_id'],
                            'suborder_id' => $data['suborder_id']));
                        $file_name = base64_encode(serialize($result));
                        $invoice_links[$seller][$values['seller_invoice_prefix'] . '' . $values['seller_invoice_no']] = $this->securefiledownload->getDownloadLink('seller_invoice', $file_name, false);
                    }
                    // $seller_invoice[$seller]['order_id'] = $data['order_id'];
                    // $seller_invoice[$seller]['suborder_id'] = $data['suborder_id'];
                    // $file_name = base64_encode(serialize($seller_invoice[$seller]));
                    // $invoice_links[$seller] = $this->securefiledownload->getDownloadLink('seller_invoice', $file_name, false);
                }

                if (!(in_array($seller, $sellers))) {
                    array_push($sellers, $seller);
                    $seller_breakup[$seller] = array();
                    $seller_company[$seller] = $seller_products['sellers'][$product['seller_id']]['company'] . " - " .
                            $seller_products['sellers'][$product['seller_id']]['nickname'];
                    $seller_totals[$seller] = 0;
                }

                if (!$product['comment']) {
                    // Singles - Will probably have options
                    $options = $this->model_sale_order->getOrderOptions($data['order_id'], $product['order_product_id']);

                    if ($options)
                        $product['comment'] = $data['text_singles_set_desc'];

                    foreach ($options as $option) {

                        if ($option['type'] != 'file') {
                            $product['comment'] = $product['comment'] . " " . $option['name'] . " " . $option['value'];
                        }
                    }
                }

                $total_pieces = (int) ($product['piece_in_set'] * $product['quantity']);

                $img = (!empty($seller_products['images'][$product['product_id']]) ) ? $seller_products['images'][$product['product_id']] : '';

                $image = '';
                $width = '';
                $height = '';

                if ($img != '') {
                    $image = $this->model_tool_image->resize($img, 100, 150);
                    $width = $this->config->get('config_image_additional_width');
                    $height = $this->config->get('config_image_additional_height');
                }

                array_push($seller_breakup[$seller], array(
                    'index' => $indexs[$product['suborder_id']],
                    'order_product_id' => $product['order_product_id'],
                    //'order_no' => $product['order_no'],
                    'image' => $image,
                    'width' => $width,
                    'height' => $height,
                    'sku' => $product['seller_sku'],
                    'expected_dispatch_date' => $seller_products['expected_dispatch_date'][$product['product_id']],
                    'set_description' => $product['comment'],
                    'seller_invoice_id' => (!empty($product['seller_invoice_id']) ? $product['seller_invoice_id'] : 0),
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'piece_in_set' => $product['piece_in_set'],
                    'model' => $product['model'],
                    'total_pieces' => $total_pieces,
                    'store_sales' => $product['store_sales'],
                    'transfer_price' => $this->currency->format($product['transfer_price_per_piece'], 'INR', 1),
                    'pickup_status' => $product['pickup_status'],
                    'pickup_last_modified' => $product['pickup_last_modified'],
                    'customer_comment' => $product['customer_comment'],
                    'amount' => $this->currency->format((int) $total_pieces * (float) $product['transfer_price_per_piece'], 'INR', 1)));

                if (!empty($indexs[$product['suborder_id']])) {
                    $indexs[$product['suborder_id']] ++;
                }

                $seller_totals[$seller] += (int) $total_pieces * (float) $product['transfer_price_per_piece'];
                $data['total_purchase_value'] += (int) $total_pieces * (float) $product['transfer_price_per_piece'];
            }

            foreach ($seller_totals as $seller => $total) {
                $seller_totals[$seller] = $this->currency->format($total, 'INR', 1);
            }
        }
        $data['total_purchase_value'] = $this->currency->format($data['total_purchase_value'], 'INR', 1);

        $data['sellers'] = $sellers;
        $data['seller_breakup'] = $seller_breakup;
        $data['seller_invoice_generate'] = !empty($seller_products) ? $seller_products['sellers'][$seller]['seller_invoice_generate'] : '';
        $data['seller_totals'] = $seller_totals;
        $data['seller_company'] = $seller_company;
        $data['invoice_links'] = $invoice_links;
        // Seller statuses
        $data['seller_statuses'] = $seller_statuses;
        $data['seller_wise_order_products'] = $seller_products;

        $data['seller_not_given']      = $this->getSellerProductBreakupEditTypeWise((int)$data['order_id'], $data['suborder_id'], 'SELLER_NOT_SUPPLIED');
       
        $data['seller_later_dispatch'] = $this->getSellerProductBreakupEditTypeWise((int)$data['order_id'], $data['suborder_id'], 'SELLER_LATER_DISPATCH');

        $data['cancelled_by_customer'] = $this->getSellerProductBreakupEditTypeWise((int)$data['order_id'], $data['suborder_id'], 'CANCELLED_BY_CUSTOMER');

        if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') {
            $data['request_page'] = 'franchise_seller_product_breakup';
        } else {
            $data['request_page'] = 'sale_seller_product_breakup';
        }
        //Seller mail info
        $data['order_info_seller_mail_log'] = $this->model_sale_order->getSellerMailLog($data['order_id'], $data['suborder_id']);
        $data['order_info_seller_mail_log_count'] = $this->model_sale_order->getSellerMailLogCount($data['order_id'], $data['suborder_id']);

        $this->response->setOutput($this->load->view('sale/order_info_tab_seller_product_break_up.tpl', $data));
    }

    public function orderInfoTabOrderProductStatus() {
        $order_id = $this->request->get['order_id'];
        $suborder_id = $this->request->get['suborder_id'];
        $data = array(
            'order_id' => $order_id,
            'suborder_id' => $suborder_id
        );

        $data['token'] = $this->session->data['token'];

        $this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], array('order' => array(),
                    'suborder' => array())
        );

        $order_info = $this->_order_info['order'];
        $suborder_info = $this->_order_info['suborder'][$data['suborder_id']];
        
        $data['order_status_id'] = $suborder_info['order_status_id'];

        $this->load->model('sale/order');

        $seller_products = $this->model_sale_order->getProductsBySeller($data['order_id'], $data['suborder_id'], '', array('YES',
        'SELLER_APPROVED',
        'SELLER_PARTIAL',
        'REJECTED_WRONG_PRODUCT',
        'REJECTED_SELLER_DAMAGE',
        'REJECTED_WSB_DAMAGE',
        'DAMAGE_BY_COURIER_COMPANY'));


        $tmp_data = array();
        $tmp_data['order_id'] = $data['order_id'];
        $seller_data = $seller_products;
        $tmp_data['out_of_stock_item'] = array();
        $product_out_of_stock = false;
        if (!empty($seller_data)) {
            foreach ($seller_data['products'] as $key => $order_product) {

                $current_stock = $this->model_sale_order->checkCurrentStockByProductId(
                        $order_product['product_id'], $order_product['order_product_id']
                );
                $seller_data['products'][$key]['updated_by'] = '';
                $seller_data['products'][$key]['current_quantity'] = $current_stock['quantity'];
                if ($current_stock['quantity'] <= 0) {
                    $save_comment = false;
                    $seller_data['products'][$key]['stock_status'] = 'Out of Stock';
                    $editor = $this->model_sale_order->checkProductLastChangeLog($order_product['product_id']);
                    if (!empty($editor['user'])) {
                        $seller_data['products'][$key]['updated_by'] = $editor['user'];
                    }
                    if (!empty($editor['edit_by'])) {
                        $seller_data['products'][$key]['updated_by'] .= ' (' . $editor['edit_by'] . ')';
                        $save_comment = true;
                    }
                    if (!empty($editor['date_added'])) {
                        $seller_data['products'][$key]['updated_by'] .= ' on ' . date('d-m-Y', strtotime($editor['date_added']));
                    }
                    if ($save_comment) {
                        $tmp_data['out_of_stock_item'][] = $order_product['model'] . ' is marked out of stock by ' . $seller_data['products'][$key]['updated_by'] . '. ';
                    }
                    if (!$product_out_of_stock) {
                        $product_out_of_stock = true;
                    }
                }
            }
            if ($product_out_of_stock && in_array($data['order_status_id'], array(1, 9, 16))) {
                $tmp_data['send_out_of_stock_email_url'] = $this->url->link('sale/order/sendOutOfStatusEmail', '', 'SSL');
                $tmp_data['token'] = 'token=' . $this->request->get['token'];
            }
        }

        $tmp_data['order_products_for_status'] = $seller_data;
        $this->response->setOutput($this->load->view('sale/order_info_tab_order_product_status.tpl', $tmp_data));
    }

    public function sendOutOfStatusEmail() {
        if (isset($this->request->post)) {
            $this->load->model('sale/order');
            $data['order_product_id'] = $this->request->post['order_product_id'];
            $data['order_id'] = $this->request->post['order_id'];
            if (!empty($data['order_product_id'])) {
                if ($this->model_sale_order->sendOutOfStockMailToCustomer($data)) {
                    echo json_encode(array('status' => 'success', 'error' => false));
                } else {
                    echo json_encode(array('status' => 'failed', 'error' => 'Error while sending mail
                    '));
                }
            }
        } else {
            echo json_encode(array('status' => 'failed', 'error' => 'No Products for sending mail.'));
        }
    }

    public function orderInfoTabHistory() {
        $order_id = $this->request->get['order_id'];
        $suborder_id = $this->request->get['suborder_id'];
        $data = array(
            'order_id' => $order_id,
            'suborder_id' => $suborder_id
        );

        $this->load->autoLoadLanguage('sale/order', $data);

        $data['token'] = $this->session->data['token'];
        $data['user'] = $this->user->getUserName()['name'];
        $data['user_id'] = $this->user->getId();

        $this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], array('order' => array(),
                    'suborder' => array())
        );

        $order_info = $this->_order_info['order'];
        $suborder_info = $this->_order_info['suborder'][$data['suborder_id']];

        $data['franchise_id'] = $order_info['franchise_id'];

        $data['no_wsb_tape'] = $suborder_info['no_wsb_tape'];
        $data['no_invoice_with_shipment'] = $suborder_info['no_invoice_with_shipment'];
        $data['courier_partner_preference'] = $suborder_info['courier_partner_preference'];

        $data['tracking_no'] = (!empty($suborder_info['tracking_no']) ? $suborder_info['tracking_no'] : '' );
        $data['courier_partner'] = (!empty($suborder_info['courier_partner']) ? $suborder_info['courier_partner'] : '' );

        $this->load->model('localisation/order_status');
        
        $order_status_id    = (int) $this->_order_info['suborder'][$data['suborder_id']]['order_status_id'];
        $invoice_no         = (int)( $this->_order_info['suborder'][$data['suborder_id']]['invoice_no'] ?? 0 );
        $buyer_invoice_id   = (int)( $this->_order_info['suborder'][$data['suborder_id']]['buyer_invoice_id'] ?? 0 );
        if(!empty($order_status_id)) {
            $post_actions = $this->model_localisation_order_status->getOrderStatusPostActions($order_status_id, $invoice_no, $buyer_invoice_id);
            $post_actions = array('post_actions'=>$post_actions);
        } else { 
            //Handle post actions for missing orders
            $post_actions = array( 'post_actions' => ORDER_STATUS['Pending'] );
        }

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses($post_actions);
        $data['order_status_id'] = $this->_order_info['suborder'][$data['suborder_id']]['order_status_id'];
        
        $data['can_add_history'] = true;
        $data['history_add_error'] = '';
        $admin_mode = in_array($this->user->getId(), explode(',', ADMIN_IDS)); 
        if (!$admin_mode && $this->_order_info['order']['payment_code'] == 'credit' && $data['order_status_id'] == 0) {
          $credit_payment_gateway = new CreditPayment($this);
          $neo_credit_data = $credit_payment_gateway->getTransactionStatus($this->_order_info['order']['order_no']);
          if (!empty($neo_credit_data['code']) && $neo_credit_data['code'] != 'ss-202') {
            $data['can_add_history'] = false;
            $data['history_add_error'] = 'You can\'t add history to this order, because this order is placed on NeoGrowth Credit, but purchase transaction was failed.';
          }
        }
        
        // Get courier partners
        $data['courier_partners'] = $this->model_localisation_order_status->getCourierPartners();
        $data['deleted_order_history'] = OrderEdit::getDeletedOrderHistoryItemOfSuborderHtml($this, $data['order_id'], $data['suborder_id']);

        $data['order_cancelled_status'] = $this->getCancelledOrderCommentsOptions();

        $this->response->setOutput($this->load->view('sale/order_info_tab_history.tpl', $data));
    }

    public function orderInfoTabCourierPartners() {

        $order_id = $this->request->get['order_id'];
        $suborder_id = $this->request->get['suborder_id'];
        $data = array(
            'order_id' => $order_id,
            'suborder_id' => $suborder_id
        );

        $data['token'] = $this->session->data['token'];

        $this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], array('order' => array(),
        'suborder' => array())
        );

        $order_info = $this->_order_info['order'];
        $suborder_info = $this->_order_info['suborder'][$data['suborder_id']];

        $data['payment_code'] = $order_info['payment_code'];
        $data['shipping_method'] = $suborder_info['shipping_method'];

        $buyer_invoice = new BuyerInvoice($this);
        $orderTotal = $buyer_invoice->getTotals($data['order_id'], $data['suborder_id']);
        
        $order_total = 0;
        if (!empty($orderTotal['total_amt'])) {
            $order_total = $orderTotal['total_amt']['value'];
        }
        $data['order_total'] = $order_total;
        
        $net_payble = 0;
        if (!empty($orderTotal['net_amount'])) {
            $net_payble = $orderTotal['net_amount']['value'];
        }
        
        if( ( $data['payment_code'] == 'cod' || 
                in_array($data['payment_code'], CREDIT_PAYMENT_CODES)
            ) && $net_payble > 0
        ){
            $data['net_payble'] = $net_payble;        
        }else { 
            $data['net_payble'] = 0;        
        }

        $isDotZot = 0;
        if ( ( $data['payment_code'] == 'cod' || 
                in_array($data['payment_code'], CREDIT_PAYMENT_CODES) 
                ) && $net_payble > 0 && $net_payble <= 3000
        ) {
            $isDotZot = 1;
        }
        $data['isDotZot'] = $isDotZot;

        $data['shipping_postcode'] = $order_info['shipping_postcode'];

        if ($this->_order_info['suborder'][$data['suborder_id']]['invoice_no']) {
            $data['invoice_no'] = $suborder_info['invoice_prefix'] . $suborder_info['invoice_no'];
        } else {
            $data['invoice_no'] = '';
        }

        $data['https_catalog'] = '';
        if ($this->user->hasPermission('modify', 'sale/order')) {
            $data['https_catalog'] = HTTPS_CATALOG;
        }

        $this->load->model('sale/order');
        $data['docket'] = $this->model_sale_order->getGatiDocket();

        $this->load->model('localisation/order_status');

        // Get courier partners
        $data['courier_partners'] = $this->model_localisation_order_status->getCourierPartners();

        $this->load->autoLoadLanguage('sale/shipping_label', $data);
        $this->load->model('sale/shipping_label');

        $filter_data = array(
            'search_warehouse' => '',
            'search_city' => ''
        );

        //show default warehouse addresses in courier partner tab
        $data['getAddressOfWarehouse'] = $this->model_sale_shipping_label->getAddresses($filter_data);
        
        /*if( $data['shipping_code'] == 'weight.weight_6' || $data['shipping_code'] == 'weight.weight_8')
        {
            $data['shipping_code_type'] = 'apex';
        }else{
            $data['shipping_code_type'] = 'surface';
        }*/
        
        $data['shipping_code_type'] = 'surface';
        
        $data['isServicablePostcode'] = 1;
        $this->load->model('logistic/connect_india');
        $checkConnectIndiaServicablePostcode = $this->model_logistic_connect_india->getFilterPincodes(['filter_pincode'=>$data['shipping_postcode']]);
        if(empty($checkConnectIndiaServicablePostcode))
        {
            $data['isServicablePostcode'] = 0;
        }
        
        $data['fedex_service_types'] = $this->_fedex_service_types;
        
        $this->response->setOutput($this->load->view('sale/order_info_tab_courier_partners.tpl', $data));
    }

    public function orderInfoTabAdditionalInformation() {
        $order_id = $this->request->get['order_id'];
        $suborder_id = $this->request->get['suborder_id'];
        $data = array(
            'order_id' => $order_id,
            'suborder_id' => $suborder_id
        );

        $this->load->autoLoadLanguage('sale/order', $data);

        $data['token'] = $this->session->data['token'];

        $this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], array('order' => array(),
                    'suborder' => array())
        );

        $order_info = $this->_order_info['order'];

        foreach ($order_info as $key => $order_infos) {
            $data[$key] = $order_infos;
        }

        $this->response->setOutput($this->load->view('sale/order_info_tab_additional_information.tpl', $data));
    }

    private function orderInfoLabelImportantInformation(&$data) {

        return $this->load->view('sale/order_info_important_information.tpl', $data);
    }

    public function orderInfoLabelCourierAdvisory(&$data = NULL, $order_infos = array()) {

        $order_info = array();
        $data_ajax = false;
        if ( !isset($data) ) {
            // Check for input in POST
            if ( !empty($this->request->post['shipping_postcode']) 
              && !empty($this->request->post['payment_code']) 
              && !empty($this->request->post['shipping_zone_id']) ) {

                $order_info = array(
                    'pincode'      => $this->request->post['shipping_postcode'],
                    'payment_code' => $this->request->post['payment_code'],
                    'zone_id'      => $this->request->post['shipping_zone_id']
                );
                $data_ajax = true;
            } else {
                return false;
            }

        } else {
            $order_info = array(
                'pincode' => (isset($this->_order_info['order']['shipping_postcode']) ? ($this->_order_info['order']['shipping_postcode']) : ( (!empty($order_infos)) ? $order_infos['pincode'] : "" )),
                //'weight' => $this->_order_info['suborder']['weight'],
                'payment_code' => (isset($this->_order_info['order']['payment_code']) ? ($this->_order_info['order']['payment_code']) : ( (!empty($order_infos)) ? $order_infos['payment_code'] : "" )),
                'zone_id' => (isset($this->_order_info['order']['shipping_zone_id']) ? ($this->_order_info['order']['shipping_zone_id']) : ( (!empty($order_infos)) ? $order_infos['zone_id'] : "" ))
            );
        }
        
        $obj_logistic_advisor = new LogisticsAdvisor($order_info, $this);
        $data_value = $obj_logistic_advisor->getAdvise();
        $data['logistic_data'] = array();
        foreach ($data_value as $key => $value) {
            if (isset($value['serviceability'])) {
                $data['logistic_data'][] = array(
                    'courier_logistic'  => $value['courier'],
                    'is_serviceable'    => $value['is_serviceable'],
                    'serviceability'    => $value['serviceability'],
                    'cod'               => $value['cod'],
                    'prepaid'           => $value['prepaid'],
                    'serviceability'    => $value['serviceability'],
                    'location'          => $value['location'],
                    'distance'          => $value['distance'],
                    'additional_notes'  => $value['additional_notes']
                );
            }

            if ($key == 'paperwork') {
                $data['paperwork'] = array(
                    'zone_id' => ($value['zone_id']) ? $value['zone_id'] : '',
                    'state' => ($value['state']) ? $value['state'] : '',
                    'b2c_stat_levy_type' => ($value['b2c_stat_levy_type']) ? $value['b2c_stat_levy_type'] : '',
                    'b2c_stat_levy_liable' => ($value['b2c_stat_levy_liable']) ? $value['b2c_stat_levy_liable'] : '',
                    'b2c_paperwork_req' => ($value['b2c_paperwork_req']) ? $value['b2c_paperwork_req'] : '',
                    'b2c_paperwork_exem_lim' => ($value['b2c_paperwork_exem_lim']) ? $value['b2c_paperwork_exem_lim'] : '',
                    'b2b_paperwork_inb_req' => ($value['b2b_paperwork_inb_req']) ? $value['b2b_paperwork_inb_req'] : '',
                    'b2b_paperwork_outb_req' => ($value['b2b_paperwork_outb_req']) ? $value['b2b_paperwork_outb_req'] : '',
                    'paperwork_additional_notes' => ($value['additional_notes']) ? $value['additional_notes'] : ''
                );
            }
        }

        $data['courier_advisory_table'] = $this->load->view('sale/order_info_courier_advisory_table.tpl',$data);
        
        $data['data_ajax'] = $data_ajax;
        if($data_ajax){
            $this->load->autoLoadLanguage('sale/order',$data);
            echo $this->load->view('sale/order_info_courier_advisory.tpl',$data);
            exit;
        } else {
            return $this->load->view('sale/order_info_courier_advisory.tpl', $data);    
        }
        
    }
    
    public function orderInfoTabPaymentHistory() {
        $order_id = $this->request->get['order_id'];
        $suborder_id = $this->request->get['suborder_id'];
        $data = array(
            'order_id' => $order_id,
            'suborder_id' => $suborder_id
        );

        $this->load->autoLoadLanguage('sale/order', $data);

        $data['token'] = $this->session->data['token'];

        $this->_order_info = OrderInfo::getOrderInfo($this->db, $data['order_id'], $data['suborder_id'], array('order' => array(),
        'suborder' => array())
        );

        $order_info = $this->_order_info['order'];
        $suborder_info = $this->_order_info['suborder'][$data['suborder_id']];

        $data['without_crncy_frmt_total'] = $suborder_info['total'];
        $data['particular_order_total'] = $this->_order_info['order']['total'];

        $cn_data   = CreditNote::getCnDetailsToCalculateOrderBalance($this->db, $order_id);
        $cn_amount = 0;
        if(!empty($cn_data[$order_id])){
            $cn_amount = $cn_data[$order_id]['cn_amount'];
        }

        $cod_failed_penalty = 0;
        if(!empty($cn_data[$order_id])){
            $cod_failed_penalty = $cn_data[$order_id]['cod_failed_penalty'];
        }

        $less_cash_discount = 0;
        if(!empty($cn_data[$order_id])){
            $less_cash_discount = $cn_data[$order_id]['less_cash_discount'];
        }

        $other_charges = 0;
        if(!empty($cn_data[$order_id])){
            $other_charges = $cn_data[$order_id]['other_charges'];
        }
        $this->load->model('sale/order');
        $data['order'] = array(
            'without_crncy_frmt_total'  => $data['without_crncy_frmt_total'],
            'order_id'                  => $order_id,
            'order_total'               => $data['without_crncy_frmt_total'],
            'currency_code'             => $order_info['currency_code'],
            'currency_value'            => $order_info['currency_value'],
            'particular_order_total'    => $data['particular_order_total'],
            'payment_history'           => $this->model_sale_order->getOrderPaymentHistory($order_id),
            'cn_amount'                 => $cn_amount,
            'cod_failed_penalty'        => $cod_failed_penalty,
            'less_cash_discount'        => $less_cash_discount,
            'other_charges'             => $other_charges,
            'tentative_advance_history' => $this->model_sale_order->getTentativeAdvanceHistory($order_id)
        );
        $this->response->setOutput($this->load->view('sale/order_info_tab_payment_history.tpl', $data));
    }

    public function generatePaymentLink($data=array()) {

        if ($this->user->haspermission('modify', 'sale/order')) {
            $data['order_id'] = $this->request->post['order_id'];
            $data['order_no'] = $this->request->post['order_no'];
            $data['percentage'] = $this->request->post['percentage'];
            $data['amount'] = $this->request->post['amount'];
            $data['order_total'] = $this->request->post['order_total'];
            $data['user_id'] = $this->user->getId();
            $data['payer_vpa'] = $this->request->post['customer_vpa'];

            if(empty($data['percentage']) || empty($data['amount'])){
                $response['responseMsg'] = 'error';
                $response['error_message'] = 'Amount can not be ZERO(0) or Less than 0. Please Enter a Amount Value';
            } else {
                
                $payment = isset($this->request->post['payment'])?$this->request->post['payment']:'';

                //$response = array();
                // For Citrus
                if($payment == 'citrus' || $payment == 'all'){

                    $payment_gateway = PaymentGatewayFactory::getPaymentGateway($this);
                    $response['citrus'] = $payment_gateway->generatePaymentLink($data);

                }
                
                // For Razorpay
                if($payment == 'razorpay' || $payment == 'all'){

                    $payment_gateway = new Razorpay($this);
                    $data['call_from_backend'] = 1;
                    $response['razorpay'] = $payment_gateway->generatePaymentLink($data);

                }
                
                // For UPI
                if($payment == 'upi' || $payment == 'all') {
                    // UPI Payment Link
                    $data['backend'] = 'backend';
                    $payment_gateway = new UPI($this);
                    $response['upi'] = $payment_gateway->generatePaymentLink($data);       
                }

                $response['responseMsg'] = "FAIL";
                if(!empty($response['citrus']) && !empty($response['upi']) && !empty($response['razorpay'])) {
                    if((strtoupper($response['citrus']['responseMsg']) == "SUCCESS") || (strtoupper($response['upi']['responseMsg']) == "SUCCESS") || (strtoupper($response['razorpay']['responseMsg']) == "SUCCESS")) {
                        $response['responseMsg'] = "SUCCESS";
                    }
                } else if(!empty($response['citrus'])) {
                    if((strtoupper($response['citrus']['responseMsg']) == "SUCCESS")) {
                        $response['responseMsg'] = "SUCCESS";
                    }
                } else if(!empty($response['upi'])) {
                    if((strtoupper($response['upi']['responseMsg']) == "SUCCESS")) {
                        $response['responseMsg'] = "SUCCESS";
                    }
                } else if(!empty($response['razorpay'])) {
                    if((strtoupper($response['razorpay']['responseMsg']) == "SUCCESS")) {
                        $response['responseMsg'] = "SUCCESS";
                    }
                }

                $user = $this->user->getUserName($data['user_id']);
                $response['user'] = $user['name'];
            }
            
        } else {
            $response = 'You dont have permission to send PaymentLink';
        }
        
        echo json_encode($response);
    }

    public function manualBankTransfer() {

        if ($this->user->haspermission('modify', 'accounts/sellerpayments')) {
            $data['order_id']        = $this->request->post['order_id'];
            $data['order_no']        = $this->request->post['order_no'];
            $data['order_total']     = $this->request->post['order_total'];
            $data['payment_reff_no'] = $this->request->post['payment_reff_no'];
            $data['bank_name']       = $this->request->post['bank_name'];
            $data['bank_amount']     = $this->request->post['bank_amount'];
            $data['date']            = $this->request->post['payment_date'];
            $data['successfull']     = 1; 
            $data['payment_date'] = date("Y-m-d H:i:s", strtotime($data['date']));
            $data['bank_transfer_mode'] = $this->request->post['bank_transfer_mode'];
            $data['serialize_response'] = serialize($data);
            $data['user_id'] = $this->user->getId();
            
            $this->load->model('sale/order');
            if (isset($this->request->post['update'])) {
                $data['payment_id'] = $this->request->post['payment_id'];
                OrderPayment::updateCashAdvanceToBankTransfer($this, $data);
            } else {
                $order_payment_data = OrderPayment::applyBankTransferAdvance($this, $data);
                if(!$order_payment_data){
                    $response['error'] = 'error';
                    $response['message'] = 'This Payment Reff No. already exists.';
                    echo json_encode($response);
                    exit();      
                }
            }

            $user = $this->user->getUserName($data['user_id']);
            $response['user'] = $user['name'];
            $response['success'] = 'success';
            $response['bnk_trnfr_mode'] = $data['bank_transfer_mode'];
        } else {
            $response = 'You dont have manual Bank Transfer permission';
        }
        echo json_encode($response);
    }

    
    public function paymentRefund() {
/*
        if ($this->user->haspermission('modify', 'accounts/sellerpayments')) {
            $data['order_no'] = $this->request->post['order_no'];
            $data['merchant_txn_id'] = $this->request->post['merchant_txn_id'];
            $data['refund_amount'] = $this->request->post['refund_amount'];
            $data['user_id'] = $this->user->getId();

            $citrus = new Citrus($this);
            $order_info = $citrus->getOrderDetailByMerchant_txn_id($data['merchant_txn_id']);
            $gateway = $order_info['payment_gateway'];
            if ($gateway == 'razorpay') {
                $payment_gateway = new Razorpay($this);
            } else {
                $payment_gateway = new Citrus($this);
            }

            $response = $payment_gateway->paymentRefund($data);
//            echo "<pre>";
//            print_r($response);
//            die;
        } else {
            $response = 'You dont have Payment Refund permission';
        }
        echo json_encode($response);
        
        */
    }

    public function saveAdvance() {
        return;
        if (!$this->user->haspermission('modify', 'sale/order')) {
            echo json_encode(array('success' => 0, 'message' => "You don't have permission to update advance."));
            return;
        }



        if (!empty($this->request->post['advance']) && !empty($this->request->post['order_id'])) {
            $order_id = $this->request->post['order_id'];
            $advance_data = array();
            foreach ($this->request->post['advance'] as $suborder_id => $advance) {
                if (!empty($advance['value'])) {
                    $advance_data[$suborder_id] = $advance;
                    $advance_data[$suborder_id]['locked'] = !empty($advance['locked']) ? 'true' : 'false';
                }
            }

            echo OrderPayment::applyAdvance($this->db, $order_id, $advance_data);
        }
    }

    /**
     * Method for Non gati docket information save in shipping label table
     * @author : vikas, 2017
     */
    public function nonGatiDocket() {
        $this->load->model('sale/shipping_label');
        $this->load->model('sale/courier_dockets');
        
        $json = array();
        if (!empty($this->request->get)) {
            $order_id = $this->request->get['order_id'];
            $suborder_id = $this->request->get['suborder_id'];
            $courier = $this->request->get['courier'];
            $docket = $this->request->get['docket'];
            $weight = $this->request->get['weight'];
            $barcode = $this->request->get['barcode'];
            $warehouse_id = $this->request->get['warehouse_id'];

            /*if(!empty($order_id) && !empty($suborder_id))  
            {
                $buyer_invoice  = new BuyerInvoice($this);
                $productDetails = $buyer_invoice->getOrderProductsDetailWithOrderInfo($order_id, $suborder_id);
                $totals         = $buyer_invoice->getTotals($order_id, $suborder_id);
                $order_total = 0;
                if (!empty($totals['total_amt'])) {
                    $order_total = round( $totals['total_amt']['value'], 2 );
                }
                $net_payable = 0;
                if (!empty($totals['net_amount'])) {
                    $net_payable = round( $totals['net_amount']['value'], 2 );
                }
                if( isset($productDetails['payment_code']) && strtolower(trim($productDetails['payment_code'])) != "cod"){
                    $payment_mode = 'prepaid';
                } else if(isset($productDetails['payment_code']) && strtolower(trim($productDetails['payment_code'])) == "cod" && $net_payable > 0) { 
                    $payment_mode = 'cod';
                }else{
                    $payment_mode = 'prepaid';
                }
               
               $courier_data = array(
                   'order_id'           => $order_id,
                   'suborder_id'        => $suborder_id,
                   'courier_partners_id'=> $this->model_sale_courier_dockets->getCourierPartnerIdByName($courier),
                   'docket_no'          => $docket,
                   'payment_mode'       => $payment_mode,
                   'cod_amount'         => $net_payable,
                   'parcel_amount'      => $order_total,
                   'post_mode'          => 'REST',
                   'post_type'          => 'JSON',
                   'post_url'           => 'No-Docket-Generation',
                   'post_values'        => json_encode(array('order_id'=>$order_id,'suborder_id'=>$suborder_id,
                                                             'courier'=>$courier,'docket'=>$docket,'weight'=>$weight,
                                                             'warehouse_id'=>$warehouse_id
                                                            )),
                   'response_type'      => 'JSON',
                   'response_value'     => json_encode(array('docket'=>$docket,'weight'=>$weight,'warehouse_id'=>$warehouse_id)),
                   'date_added'         => date('Y-m-d'),
               );
                $this->model_sale_courier_dockets->saveCourierDocketData($courier_data);
            } */
          
            $shipping_label_id = $this->model_sale_shipping_label->addShippingLabel($order_id, $suborder_id, $courier, $docket, $weight, $warehouse_id, $barcode);
            $json['shipping_label_id'] = $shipping_label_id;
            $json['success'] = 1;
        } else {
            $json['error'] = 'Your order and suborder number is wrong !!';
        }


        $json = json_encode($json);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($json);
    }

    /**
     * Method for change bank transfer mode (using in payment history)
     * @return null
     * vikas, 2017
     */
    public function changeBankTransferMode() {

        if (!empty($this->request->post)) {
            $json = array();
            OrderPayment::changeBankTransferMode($this, $this->request->post);
            $json['success'] = 'success';
            $json['bnk_trnfr_mode'] = $this->request->post['bank_transfer_mode'];

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        } else {
            return false;
        }
    }

    /**
     * Public method to get seller breakup formated data , EDIT_TYPE wise
     * @param: integer $order_id, string $suborder_id, string $edit_type
     * @return: array $data
     * @author: Written By Vikas 2017, Optimized by Nishu, Sept 2018
    */
    public function getSellerProductBreakupEditTypeWise(int $order_id, string $suborder_id, string $edit_type) : array{

        // Seller-wise Product Break-up
        $seller_products = $this->model_sale_order->getProductsBySeller($order_id, $suborder_id, '', array($edit_type));
        $sellers = array();
        $seller_breakup = array();
        $seller_totals = array();
        $seller_company = array();

        // Sellers status information
        $seller_statuses = array();

        $data['total_purchase_value']    = 0;
        $invoice_links                   = array();
        $data['show_store_sales_notice'] = false;

        // Getting list of unique sellers
        if (!empty($seller_products)) {
            foreach ($seller_products['products'] as $product) {
                if ($product['store_sales'] != 'NO') {
                    $data['show_store_sales_notice'] = true;
                }
                $seller = $product['seller_id'];

                if (!empty($seller_invoice[$seller])) {
                    $seller_invoice[$seller]['order_id'] = $order_id;
                    $seller_invoice[$seller]['suborder_id'] = $suborder_id;
                    $file_name = base64_encode(serialize($seller_invoice[$seller]));
                    $invoice_links[$seller] = $this->securefiledownload->getDownloadLink('seller_invoice', $file_name, false);
                }

                if (!(in_array($seller, $sellers))) {
                    array_push($sellers, $seller);
                    $seller_breakup[$seller] = array();
                    $seller_company[$seller] = $seller_products['sellers'][$product['seller_id']]['company'] . " - " .
                            $seller_products['sellers'][$product['seller_id']]['nickname'];
                    $seller_totals[$seller] = 0;
                }

                if (!$product['comment']) {
                    // Singles - Will probably have options
                    $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

                    if ($options)
                        $product['comment'] = $data['text_singles_set_desc'];

                    foreach ($options as $option) {

                        if ($option['type'] != 'file') {
                            $product['comment'] = $product['comment'] . " " . $option['name'] . " " . $option['value'];
                        }
                    }
                }
                $expected_dispatch_date = $seller_products['expected_dispatch_date'][$product['product_id']] ?? '';
                if(!empty($expected_dispatch_date) ){
                    $expected_dispatch_date = date('d M Y', strtotime($expected_dispatch_date));
                }

                $total_pieces = (int) ($product['piece_in_set'] * $product['quantity']);

                $image = $seller_products['images'][$product['product_id']] ?? '';

                $transfer_price = $this->currency->format($product['transfer_price_per_piece'], 'INR', 1);
                $amount = $this->currency->format((int) $total_pieces * (float) $product['transfer_price_per_piece'], 'INR', 1);
                array_push($seller_breakup[$seller], array(
                            'order_product_id'       => $product['order_product_id'],
                            //'order_no'             => $product['order_no'],
                            'product_id'             => $product['product_id'],
                            'image'                  => $image,
                            'sku'                    => $product['seller_sku'],
                            'expected_dispatch_date' => $expected_dispatch_date ?? '',
                            'set_description'        => $product['comment'],
                            'quantity'               => $product['quantity'],
                            'piece_in_set'           => $product['piece_in_set'],
                            'model'                  => $product['model'],
                            'total_pieces'           => $total_pieces,
                            'store_sales'            => $product['store_sales'],
                            'transfer_price'         => $transfer_price,
                            'amount'                 => $amount )
                        );

                $seller_totals[$seller] += (int) $total_pieces * (float) $product['transfer_price_per_piece'];
                $data['total_purchase_value'] += (int) $total_pieces * (float) $product['transfer_price_per_piece'];
            }

            foreach ($seller_totals as $seller => $total) {
                $seller_totals[$seller] = $this->currency->format($total, 'INR', 1);
            }
        }
        $data['total_purchase_value'] = $this->currency->format($data['total_purchase_value'], 'INR', 1);

        $data['sellers'] = $sellers;
        $data['seller_breakup'] = $seller_breakup;

        $data['seller_totals'] = $seller_totals;
        $data['seller_company'] = $seller_company;
        $data['invoice_links'] = $invoice_links;
        // Seller statuses
        $data['seller_statuses'] = $seller_statuses;
        $data['seller_wise_order_products'] = $seller_products;
        // echo "<pre>ddd:"; print_r($data); die;
        return $data;

    }

    /**
     * change pickup status of order product
     * @param: order_id, suborder_id, pickupstatus,order_product_id
     * @return : json when call by ajax and and nothing when call by function
     * @author : kalyan 23th Oct, 2017
     */
    public function orderProductPickupStatus($orderId = '', $suborderId = '', $shipping_method = '', $products = array()) {

        $orderPickup = new OrderPickup($this->registry);
        $username = $this->user->getUserName();
        $userId = $this->user->getId();
        /*
         * check request is comming from ajax
         */
        if (isset($this->request->get['order_product_id']) && $this->request->get['order_product_id'] != '') {
            $data = array();
            $data['data'] = $this->request->get;
            $data['user_id'] = $userId;
            $data['user_name'] = $username['username'];
            $data['user_type'] = 'Admin';
        }
        /*
         * check request is calling from function
         */
        if ($suborderId != '' && $orderId != '' && strtolower(trim($shipping_method)) == 'store pickup') {

            if (!empty($products) && count($products) > 0) {

                $userNmae = $username['username'];
                /*
                 * update multiple entery of order product table using loop
                 */
                foreach ($products['suborder'][$suborderId]['order_product'] as $key => $value) {
                    /*
                     * if order product field "edit_type" has YES status than update the entry
                     */
                    if ($value['edit_type'] == 'YES') {
                        $data = array();
                        $data['data']['order_product_id'] = $value['order_product_id'];
                        $data['data']['received_status'] = 'Received';
                        $data['user_id'] = $userId;
                        $data['user_name'] = $userNmae;
                        $data['user_type'] = 'Admin';
                        $data['data']['issue_box'] = '';
                        $data['data']['seller_invoice_id'] = $value['seller_invoice_id'];
                        $data['data']['seller_id_no'] = $value['seller_id'];
                        $data['data']['order_id'] = $orderId;
                        $data['data']['suborder_id'] = $suborderId;

                        $orderPickup->changeOrderProductPickupStatus($data);
                    }
                }
            }
        }
        /*
         * update single entry of order product table when request is comming from ajax
         */
        if (isset($this->request->get['order_product_id']) && $this->request->get['order_product_id'] != '') {
            if ($this->request->get['received_status'] == 'partial_receive') {
                if (((int) $this->request->get['total_piece_new'] > 0) && ((int) $this->request->get['total_piece_new'] !== (int) $this->request->get['total_piece_old'])) {
                    $data['total_piece_new'] = $this->request->get['total_piece_new'];
                    $data['total_piece_old'] = $this->request->get['total_piece_old'];
                } else {
                    $error['error'] = "Unable to change status";
                    $this->response->addHeader('Content-Type: application/json');
                    $this->response->setOutput(json_encode($error['error']));
                }
            }

            $status = $orderPickup->changeOrderProductPickupStatus($data);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($status));
        }
    }

    /**
     * display customer search popup
     * @author: kalyan 26th sep 2017
     */
    public function getDuplicateCustomers() {

        $this->load->model('sale/customer');
        if (!empty($this->request->get['customer_name'])) {
            $data['filter_name'] = $this->request->get['customer_name'];
        }
        if (!empty($this->request->get['customer_email'])) {
            $data['filter_email'] = $this->request->get['customer_email'];
        }
        if (!empty($this->request->get['customer_phone'])) {
            $data['filter_telephone'] = $this->request->get['customer_phone'];
        }
        if (!empty($this->request->get['customer_city'])) {
            $data['filter_city'] = $this->request->get['customer_city'];
        }
        if (!empty($this->request->get['company_name'])) {
            $data['filter_company_name'] = $this->request->get['company_name'];
        }
        if (!empty($this->request->get['customer_cid'])) {
            $data['filter_customer_cid'] = $this->request->get['customer_cid'];
        }
        if (!empty($this->request->get['customer_postcode'])) {
            $data['filter_customer_postcode'] = $this->request->get['customer_postcode'];
        }
        if (!empty($this->request->get['customer_id'])) {
            $data['filter_not_customer_id'] = $this->request->get['customer_id'];
        }
        
        if(!empty($this->request->get['change_customer_flag'])) {
            $data['change_customer_flag'] = $this->request->get['change_customer_flag'];
            $data['order_id'] = $this->request->get['order_id'];
            $data['suborder_id'] = $this->request->get['suborder_id'];
            $data['save'] = $this->url->link('sale/edit_order/save', '&token=' . $this->session->data['token'], 'SSL');
        }

        $data['limit'] = 100; // applying limit on get customers, otherwise it get all the records
        $customers = $this->model_sale_customer->getCustomers($data);
        if (!empty($customers) && count($customers) > 0) {
            foreach ($customers as $key => $value) {
                $isMasterId = $this->model_sale_customer->checkCustomerIdIsMasterId($value['customer_id']);
                $customers[$key]['is_master_id'] = $isMasterId;
                if (!$isMasterId && $value['customer_id'] != $value['master_id']) {
                    unset($customers[$key]);
                }
            }
            $data['customers'] = $customers;

            $this->response->setOutput($this->load->view('sale/master_customer_list.tpl', $data));
        }
    }

    /**
     * validate and set a master id to order customer
     * @param: customer id
     * @author: kalyan 26th sep 2017
     */
    public function setMasterCustomerId() {

        $this->load->language('sale/order');

        $userData = $this->user->getUserName();

        if ($this->user->hasPermission('modify', 'sale/customer')) {

            $this->load->model('sale/customer');
            $json = array();

            $orderCustomerId = $this->request->get['order_customer_id'];
            $masterCustomerId = $this->request->get['master_customer_id'];

            if ($orderCustomerId == '' || $masterCustomerId == '') {
                return 'blank_id';
            } else {

                $orderCustomerStatus = $this->model_sale_customer->checkCustomerIdIsMasterId($orderCustomerId);

                if (!$orderCustomerStatus) {

                    $hasMasterCustomerId = $this->model_sale_customer->checkMasterCustomerHasMasterId($masterCustomerId);

                    if (!$hasMasterCustomerId) {

                        $this->model_sale_customer->setMasterCustomerIdToOrderCustomer($orderCustomerId, $masterCustomerId);
                        $this->sendDuplicateCustomerRecordToCrmApi($orderCustomerId, $masterCustomerId, $userData);
                        $json['status'] = 1;
                        $json['message'] = 'Master customer id has been set to order customer.';
                    } else {
                        $this->model_sale_customer->setMasterCustomerIdToOrderCustomer($orderCustomerId, $hasMasterCustomerId);
                        $this->sendDuplicateCustomerRecordToCrmApi($orderCustomerId, $masterCustomerId, $userData);
                        $json['status'] = 1;
                        $json['message'] = 'Master customer id has been set to order customer.';
                    }
                    /*** Update GST Number of duplicate customer with master customer gst number ***/
                    $this->updateGSTNumberOfDuplicateCustomer($masterCustomerId, $orderCustomerId);
                } else {
                    $json['status'] = 3;
                    $json['message'] = 'Order customer is a master customer.';
                }
            }
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        } else {
            $json['status'] = 2;
            $json['permission_error'] = $this->language->get('error_permission');

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
    }

    /**
     * send duplicate customer lead to CRM API
     * @param: order_customer_id
     * @param: master_customer_id
     * @param: logged_in_user_id
     * @author: kalyan 13th Nov. 2017
     */
    public function sendDuplicateCustomerRecordToCrmApi($orderCustomerId, $masterCustomerId, $userData) {
        /*
         * send Push Notification To Agent
         */
        $apiData = array();

        if (!empty($masterCustomerId) && !empty($orderCustomerId) && !empty($userData)) {

            $apiData['main_customer_id'] = $masterCustomerId;
            $apiData['merge_customer_ids'] = array($orderCustomerId);
            $apiData['comment'] = 'This leads are merged by ' . $userData['name'] . ' Due to duplicate customers request from khufiya vibhag.';

            $jsonData = json_encode($apiData);

            $url = 'https://www.wholesalebox.biz/staging/cron/mergeDuplicateCustomers';

            if (SITE_ENVIRONMENT == 'Production') {
                $url = 'https://www.wholesalebox.biz/cron/mergeDuplicateCustomers';
            }

            $curl = curl_init();

            // Set SSL if required
            if (substr($url, 0, 5) == 'https') {
                curl_setopt($curl, CURLOPT_PORT, 443);
            }

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS,  $jsonData);

            $json = curl_exec($curl);
            $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);
            curl_close($curl);
        }
    }


    /**
    * Method for data insert of paytm offline QR
    * @request: order_id : Integer of order id
    * @request: order_no : String of order no
    * @request: order_total : float of order no
    * @request: paytm_payment_reff_no : String of paytm payment reff no.
    * @request: paytm_amount: float of paytm amount
    * @request: paytm_payment_date : dateTime of payment date
    * @return : json_format value with success message
    * @author: vikas. 2018
    */
    public function applyPaytmOfflineQR() {

        $data['order_id'] = $this->request->post['order_id'];
        $data['order_no'] = $this->request->post['order_no'];
        $data['order_total'] = $this->request->post['order_total'];

        $data['paytm_payment_reff_no'] = $this->request->post['paytm_payment_reff_no'];
        $data['paytm_amount'] = $this->request->post['paytm_amount'];
        $data['paytm_payment_date'] = date("Y-m-d H:i:s", strtotime($this->request->post['paytm_payment_date']));

        $data['serialize_response'] = serialize($data);
        $data['user_id'] = $this->user->getId();

        $order_payment_data = OrderPayment::applyPaytmOfflineQR($this, $data);
        if(!$order_payment_data){
            $response['error'] = 'error';
            $response['message'] = 'This Payment Reff No. already exists.';
            echo json_encode($response);
            exit();      
        }

        $user = $this->user->getUserName($data['user_id']);
        $response['user'] = $user['name'];
        $response['success'] = 'success';
        echo json_encode($response);
    }

    /**
    * getsalesStaff
    * get sales staff details for autopopulate list
    * @param  : $term
    * @return : json
    * @author : Manish, 22/02/18
    */
    public function getsalesStaff() {
        $data = array();
        if (isset($this->request->get['term'])) {            
            $term = trim($this->request->get['term']);                  
            $this->load->model('sale/order');
            $data = $this->model_sale_order->getSalesStaffBySearch($term);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($data));      
        }               
    }

    /**
     * updateWSBTapeStatus
     * @param  : order_id, no_wsb_tape
     * @return : json
     * @author : Devendra Dhayal, 04/04/2018
     */
    public function updateWSBTapeStatus() {
        $this->load->language('sale/order');

        $json = array();

        if (
            !isset($this->request->post['order_id']) &&
            !isset($this->request->post['suborder_id']) &&
            !isset($this->request->post['no_wsb_tape'])
        ) {
            $json['error'] = $this->language->get('error_action');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            exit;
        }

        $order_id = (int) ($this->request->post['order_id']);
        $suborder_id = "'" . $this->request->post['suborder_id'] . "'";
        $value = (int) ($this->request->post['no_wsb_tape']);
        $field = 'no_wsb_tape';

        $this->load->model('sale/order');

        $result = OrderEdit::updateSubOrderField($this->db, $order_id, $suborder_id, $field, $value);
        if ($result) {
            $edit_arr = array(
                'order_id' => $order_id,
                'suborder_id' => $suborder_id,
                'edit_type' => 'NO_WSB_TAPE_STATUS',
                'field_name' => 'no_wsb_tape',
                'old_value' => $value ? 0 : 1,
                'new_value' => $value,
                'user_id' => $this->user->getId(),
                'name' => $this->user->getUserName($this->user->getId())['name'],
                'user_name' => $this->user->getUserName($this->user->getId())['username'],
                'comment' => ''
            );
            OrderEdit::saveEditHistory($this->db, $edit_arr);
        } else {
            $json['error'] = $this->language->get('error_action');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            exit;
        }

        $json['success'] = 'Successful updated the status.';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * updateSendInvoiceStatus
     * @param  : order_id, no_wsb_tape
     * @return : json
     * @author : Devendra Dhayal, 04/04/2018
     */
    public function updateSendInvoiceStatus() {
        $this->load->language('sale/order');

        $json = array();

        if (
            !isset($this->request->post['order_id']) &&
            !isset($this->request->post['suborder_id']) &&
            !isset($this->request->post['no_invoice_with_shipment'])
        ) {
            $json['error'] = $this->language->get('error_action');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            exit;
        }

        $order_id = (int) ($this->request->post['order_id']);
        $suborder_id = "'" . $this->request->post['suborder_id'] . "'";
        $value = (int) ($this->request->post['no_invoice_with_shipment']);
        $field = 'no_invoice_with_shipment';

        $this->load->model('sale/order');

        $result = OrderEdit::updateSubOrderField($this->db, $order_id, $suborder_id, $field, $value);
        if ($result) {
            $edit_arr = array(
                'order_id' => $order_id,
                'suborder_id' => $suborder_id,
                'edit_type' => 'NO_INVOICE_STATUS',
                'field_name' => 'no_invoice_with_shipment',
                'old_value' => $value ? 0 : 1,
                'new_value' => $value,
                'user_id' => $this->user->getId(),
                'name' => $this->user->getUserName($this->user->getId())['name'],
                'user_name' => $this->user->getUserName($this->user->getId())['username'],
                'comment' => ''
            );

            OrderEdit::saveEditHistory($this->db, $edit_arr);
        } else {
            $json['error'] = $this->language->get('error_action');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            exit;
        }

        $json['success'] = 'Successful updated the status.';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    /**
    * update self order in orders and customer table
    * @param : order_id, customer_id, self_order
    * @return : json
    * @author : Manish, 02-08-18
    */
    public function updateSelfOrder() {
        $data = array();        
        
        if (!empty($this->request->post)) { 
            $this->load->model('sale/order');
            $data = $this->model_sale_order->updateSelfOrder($this->request->post['order_id'], $this->request->post['customer_id'], $this->request->post['self_order']);
        }       
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }
    
    /**
     * Public function to update gst number of duplicate customer
     * @param : int masterCustomerId, int duplicateCustomerId
     * @return : bool
     * @author : Anurag, Aug 2018
     */
    public function updateGSTNumberOfDuplicateCustomer(int $masterCustomerId, int $duplicateCustomerId): bool {
        
        /*** fetch duplicate_customer_gst_number; will be needed if master customer gst is not there ***/
        $duplicate_customer_obj = new CustomerEntity($this->registry, $duplicateCustomerId);
        $duplicate_customer_gst_number = $duplicate_customer_obj->getGSTNumber();
        
        /*** fetch master_customer_gst_number ***/
        $master_customer_obj = new CustomerEntity($this->registry, $masterCustomerId);
        $master_customer_gst_number = $master_customer_obj->getGSTNumber();
        $eligible_gst_number = $master_customer_gst_number;
        
        /*** decide GST Number to update***/
        if(empty($eligible_gst_number) && !empty($duplicate_customer_gst_number)) {
            $eligible_gst_number = $duplicate_customer_gst_number;
        }
        
        /*** update GST Number ***/
        if( empty($master_customer_gst_number) || empty($duplicate_customer_gst_number)) {
            $duplicate_customer_obj->setGSTNumber($eligible_gst_number);
        }
        
        return true;
    }
    
    /**
     * Function refreshOrderTotal is used for refresh order total amount
     * @author : Nilesh, 2018
     */
    public function refreshOrderTotal() {
        $json = array();

        if (!empty($this->request->post['order_id']) && ((int) $this->request->post['order_id'] > 0)) {
            $order_id = (int) $this->request->post['order_id'];
            OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id);
            $json['success'] = 'Order Total updated successfully.';
        } else {
            $json['error'] = $this->language->get('error_action');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    /**
     * Function to call front side API to udpate order history
     * @author : MSA, Dec. 2018
     */
    public function setOrderHistory()
    {
        $get_params = $this->request->get;
        if(!empty($get_params['token'])) {
            unset($get_params['token']);
        }
        if(!empty($get_params['route'])) {
            unset($get_params['route']); 
        }
        $url = HTTPS_CATALOG .'index.php?route=api/order/history&'. http_build_query($get_params);
        $post_params = $this->request->post;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        if(curl_error($ch)){
            $result['error'] = curl_error($ch); 
        }
        curl_close($ch);

      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode($result));

    }

    /**
     * @info: Public function get previous order's date
     * @author: Nishu, Dec 2018
    */
    public function getOrderPreviousDate() {
        $order_previous_date = 'NA';
        if(
            !empty($this->request->post['order_id']) 
            && !empty($this->request->post['master_id'])
            && !empty($this->request->post['order_date'])
        ){
            $order_id   = (int)$this->request->post['order_id'];
            $master_id  = (int)$this->request->post['master_id'];
            $order_date = $this->request->post['order_date'];

            $this->load->model('sale/order');
            
            //Get Previous Order Date master_id wise with given order_ids as array
            $order_previous_date = $this->model_sale_order->getOrderPreviousDate($order_id, $master_id, $order_date);
        }
        echo $order_previous_date; exit();
        
    }

    /**
     * @info: Public function get previous order's date
     * @author: Nishu, Dec 2018
    */
    public function getWsbCreditdetails() {
        $wsb_credit_balance_details = array();
        $order_id    = $this->request->post['order_id'] ?? 0;
        $customer_id = $this->request->post['customer_id'] ?? 0;
        $customer_id = (int)$customer_id;
        $order_id    = (int)$order_id;

        $wsb_credit = new WsbCreditPayment($this);
        $wsb_credit_details = $wsb_credit->getWsbCreditDetailByCustomerIds($customer_id);
        $wsb_credit_details = $wsb_credit_details[$customer_id] ?? array();
 
        $wsb_credit_balance_details['customer_id']   = $customer_id;
        $wsb_credit_balance_details['credit_status'] = $wsb_credit_details['status'] ?? '';
        $total_credit_limit = $wsb_credit_details['credit_limit'] ?? 0;
        $wsb_credit_balance_details['credit_limit']  = $total_credit_limit;
        if(!empty($order_id)){
            //WSB Credit Balance limit used in this particuler order
            $credit_used_for_this_order = $wsb_credit->getUsedWsbCreditBalanceForOrderId($customer_id, $order_id);
            $wsb_credit_balance_details['credit_used_in_order'] = $credit_used_for_this_order;
        }
        //Total used WSB credit limit 
        $total_used_credit_bal = $wsb_credit->getUsedWsbCreditBalanceForAllOrder($customer_id);
        $wsb_credit_balance_details['total_used_credit_bal'] = $total_used_credit_bal;
        //Total available credit balance
        $total_available_bal = $total_credit_limit - $total_used_credit_bal;
        $wsb_credit_balance_details['total_available_bal'] = ROUND($total_available_bal, 2);

        echo json_encode($wsb_credit_balance_details); exit();
    }

    /**
     * Public method to calculate amount for wsb_credit limit 
     *    - This value can exceed wsb_limit 
     *    - This option is used to conver credit order to prepaid order only by doing credit payment 
     *        entry in oc_order_payment but not changing payment_method for order
     * @author: Nishu, Feb 2019
    */
    public function getUsedWsbCreditInOrder() {
        $wsb_credit_amount = 0;
        $order_id    = $this->request->get['order_id'] ?? 0;
        $order_id    = (int)$order_id;
        
        $payment_details   = OrderInfo::getPaymentDetailsByOrderIds($this->db, array($order_id) );
        $used_wsb_credit   = $payment_details['used_wsb_credit'][$order_id] ?? 0; 
        $wsb_credit_amount = (float)$used_wsb_credit;
 
        echo $wsb_credit_amount; exit();
    }

    /**
     * Public method to set cancelled order status comments
     * @author: MSA, May 2019
    */
    public function getCancelledOrderCommentsOptions()
    {
        return array(
                "Customer not willing to pay advance amount. Asking for full COD",
                "Pincode not serviceable on COD, customer not willing to prepay",
                "Pincode serviceable on COD, customer not willing to pay the entry tax",
                "Customer didn't place the order. It was forcefully placed by sales agent",
                "Not available for payment/ Out of station",
                "Don't know how to do online payment. Bank is far. Asking for full COD",
                "Even after repeated follow-up customer not making the payment",
                "Mobile number invalid/not reachable",
                "Not picking calls even after repeated attempts from different office numbers",
                "Cancelled as per customer request",
                "Complete stock not supplied by Seller(s)",
                "Client wants to take Order only on Credit",
                "Client placed another order, thus canceling this one" 
        );
    }


    /**
     * display customer search popup on order edit page
     * @author: Devendra, Sep 2019
     */
    public function getCustomers() {

        $this->load->model('sale/customer');
        if (!empty($this->request->get['customer_name'])) {
            $data['filter_name'] = $this->request->get['customer_name'];
        }
        if (!empty($this->request->get['customer_email'])) {
            $data['filter_email'] = $this->request->get['customer_email'];
        }
        if (!empty($this->request->get['customer_phone'])) {
            $data['filter_telephone'] = $this->request->get['customer_phone'];
        }
        if (!empty($this->request->get['customer_city'])) {
            $data['filter_city'] = $this->request->get['customer_city'];
        }
        if (!empty($this->request->get['company_name'])) {
            $data['filter_company_name'] = $this->request->get['company_name'];
        }
        if (!empty($this->request->get['customer_cid'])) {
            $data['filter_customer_id'] = $this->request->get['customer_cid'];
        }
        if (!empty($this->request->get['customer_postcode'])) {
            $data['filter_customer_postcode'] = $this->request->get['customer_postcode'];
        }
        if (!empty($this->request->get['customer_id'])) {
            $data['filter_customer_id'] = $this->request->get['customer_id'];
        }
        
        if(!empty($this->request->get['change_customer_flag'])) {
            $data['change_customer_flag'] = $this->request->get['change_customer_flag'];
            $data['order_id'] = $this->request->get['order_id'];
            $data['suborder_id'] = $this->request->get['suborder_id'];
            $data['save'] = $this->url->link('sale/edit_order/save', '&token=' . $this->session->data['token'], 'SSL');
        }

        $data['limit'] = 100; // applying limit on get customers, otherwise it get all the records
        $customers = $this->model_sale_customer->getCustomers($data);
        if (empty($customers)) {
            $customers2 = $this->model_sale_customer->getCustomersWithoutCart($data);
            $customers = array_merge($customers, $customers2);
        }
        
        if (!empty($customers) && count($customers) > 0) {
            foreach ($customers as $key => $value) {
                $isMasterId = $this->model_sale_customer->checkCustomerIdIsMasterId($value['customer_id']);
                $customers[$key]['is_master_id'] = $isMasterId;
            }
            $data['customers'] = $customers;
            $this->response->setOutput($this->load->view('sale/master_customer_list.tpl', $data));
        }
    }

}
