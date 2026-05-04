<?php

class ControllerOperationsLazypayOrder extends Controller {

    private $error = array();
    private $_order_info = array();
    private $_delivered_state_id = 15;
    private $_failed_state_id = 8;
    private $_cancelled_state_id = 2;
    private $_processed_state_id = 9;
    private $_tentative_processed_state_id = 16;
    private $_return_states = array(2, 8, 9, 11, 15, 16);

    public function index() {

        $this->load->language('operations/lazypay_order');
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

        $filter_customer_id      = $this->request->get['filter_customer_id'] ?? null;
        $filter_order_id         = $this->request->get['filter_order_id'] ?? null;
        $filter_order_no         = $this->request->get['filter_order_no'] ?? null;
        $filter_customer         = $this->request->get['filter_customer'] ?? null;
        $filter_city             = $this->request->get['filter_city'] ?? null; 
        $filter_company          = $this->request->get['filter_company'] ?? null;
        $filter_sales_zone       = $this->request->get['filter_sales_zone'] ?? null;
        $filter_sales_staff      = $this->request->get['filter_sales_staff'] ??null;
        $filter_sales_staff_id   = $this->request->get['filter_sales_staff_id'] ?? null;
        $filter_order_status     = $this->request->get['filter_order_status'] ?? null;
        $filter_date_added       = $this->request->get['filter_date_added'] ?? null;
        $filter_date_modified    = $this->request->get['filter_date_modified'] ?? null;
        $filter_total_low        = $this->request->get['filter_total_low'] ?? null;
        $filter_total_high       = $this->request->get['filter_total_high'] ?? null;
        $filter_pickup_city_code = $this->request->get['filter_pickup_city_code'] ?? null;
        $filter_payment_code     = $this->request->get['filter_payment_code'] ?? null;
        $filter_shipping_pincode = $this->request->get['filter_shipping_pincode'] ?? null;
        $filter_good_process     = $this->request->get['filter_good_process'] ?? null;
        $filter_courier          = $this->request->get['filter_courier'] ?? null;
        $filter_gst_number       = $this->request->get['filter_gst_number'] ?? null;
        $filter_franchise_tab    = $this->request->get['filter_franchise_tab'] ?? null;
        $filter_franchise_id     = $this->request->get['filter_franchise_id'] ?? 0;
        $filter_tracking_no      = $this->request->get['filter_tracking_no'] ?? null;
        //$franchise_data_download_csv = $this->request->get['franchise_data_download'] ?? 0;

        $franchise_data_download = false;
        if (isset($this->request->get['filter_franchise_id'])) {
            $franchise_data_download = true;
        }

        $sort  = $this->request->get['sort']  ?? 'o.order_id';
        $order = $this->request->get['order'] ?? 'DESC';
        $page  = $this->request->get['page']  ?? 1;

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

        $this->load->autoLoadLanguage('operations/lazypay_order', $data);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
          'text' => $data['heading_title'],
          'href' => $this->url->link('operations/lazypay_order', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['orders'] = array();

        $filter_data = array(
            'filter_customer_id' => $filter_customer_id,
            'filter_order_id' => $filter_order_id,
            'filter_order_no' => $filter_order_no,
            'filter_customer' => $filter_customer,
            'filter_order_status' => $filter_order_status,
            'filter_date_added' => $filter_date_added,
            'filter_payment_code' => 'lazypay',
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
        
        $results = array();
        $data_result   = $this->model_sale_order->getOrders($filter_data);
        $orders        = $data_result['data'] ?? array();
        $order_total   = $data_result['row_count'] ?? 0;
        
        $customer_ids  = array();
        $customer_obj  = new Customer($this);
        
         if ($orders) {
            
            $selector = array('order' => 
                                    array('select' => 
                                        array('order_no',
                                            'customer_id',
                                            'firstname',
                                            'lastname',
                                            'telephone',
                                            'shipping_firstname', 
                                            'shipping_lastname',
                                            'shipping_company',
                                            'shipping_city',
                                            'shipping_postcode',
                                            'shipping_zone_id',
                                            'payment_code',
                                            'code_version',
                                            'date_added',
                                            'total',
                                            'currency_code',
                                            'currency_value',
                                            'comment',
                                            'store_name',
                                            'stock_transfer', 
                                            'live_currency_conversion_rate',
                                            'order_from',
                                            'store_voucher',
                                            'self_order'
                                            )
                                        ),
                                'suborder' => 
                                    array('select' => 
                                        array('order_status_id', 
                                            'shipping_method',
                                            'total',
                                            'invoice_no',
                                            'invoice_prefix',
                                            'invoice_date',
                                            'courier_partner',
                                            'tracking_no' 
                                        )
                                    ),
                                'order_history' => array()
                            );

            // get customer upi vpa 
            $upi = new Upi($this);
            $lazypay = new LazypayPayment($this);

            $order_ids = array_unique(array_column($orders, 'order_id'));
            $cn_data   = CreditNote::getCnDetailsToCalculateOrderBalance($this->db, $order_ids);

            foreach ($orders as $order_row) {
                $order_id = (int)$order_row['order_id'];

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

                $results[$order_id] = OrderInfo::getOrderInfo($this->db, $order_id, '', $selector);
                $results[$order_id]['order']['operations_status'] = $order_row['operations_status'];
                
                $results[$order_id]['order']['cn_amount']         = $cn_amount;
                $results[$order_id]['order']['cn_penality']       = $cod_failed_penalty;
                $results[$order_id]['order']['less_cash_discount']= $less_cash_discount;
                $results[$order_id]['order']['other_charges']     = $other_charges;

                $cust_id = $results[$order_id]['order']['customer_id'];
                $customer_ids[] = $cust_id;

                $results[$order_id]['order']['lazypay_freezed_amount'] = $lazypay->lazyPayFreezedPreAuthAmount($order_id);
                $all_suborder_cancelled = OrderInfo::isAllSuborderMarkedCancelled($this->db, $order_id);
                
                $lazypay_preauth_released_status = $lazypay->isLazyPayPreAuthReleased($order_id);
                $lazypay_preauth_purchased_status = $lazypay->isLazyPayPreAuthPurchased($order_id);
                
                $lazypay_release_show_button = 0;
                if(
                    $all_suborder_cancelled
                    &&
                    !$lazypay_preauth_released_status
                    &&
                    !$lazypay_preauth_purchased_status
                ) {
                    $lazypay_release_show_button = 1;
                }
                $results[$order_id]['order']['lazypay_release_show_button'] = $lazypay_release_show_button;
            }
        }

        $customer_ids  = array_unique($customer_ids);
        $fields        = 'customer_id, master_id, has_website, ws_gcm_registration_id, self_order';
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

        // Life Time Value show on order list page
        $ltvTotalOrders         = array();
        $ltvOrdersReturn        = array();
        $ltvCodFailedOrders     = array();
        $ltvDeliveryIssueOrders = array();            
        $ltvCancelledOrders     = array();            
        if(!empty($all_customer_ids)){
            $this->load->model('report/customer');
            $ltvTotalOrders         = $this->model_report_customer->getLifeTimeOrdersCountOfCustomers($all_customer_ids);
            $ltvOrdersReturn        = $this->model_report_customer->getLifeTimeReturnsCountOfCustomers($all_customer_ids);
            $ltvCodFailedOrders     = $this->model_report_customer->getLifeTimeCODFailedCountOfCustomers($all_customer_ids);
            $ltvDeliveryIssueOrders = $this->model_report_customer->getLifeTimeDeliveryIssuesCountOfCustomers($all_customer_ids);            
            $ltvCancelledOrders     = $this->model_report_customer->getLifeTimeCountCancelledOrdersOfCustomers($all_customer_ids);            
        }

        // get all payment Codes
        $data['payment_codes'] = $this->model_sale_order->getAllPaymentCodes();
        
        //get all order status
        $this->load->model('localisation/order_status');
        $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

        $order_statuses = array_combine(
                             array_column($order_statuses_qry, 'order_status_id'), 
                             array_column($order_statuses_qry, 'name'));
        
        $order_statuses[0] = 'Missing Order';

        $data['sales_staff_list'] = $this->model_sale_order->getSalesStaffList();

        $order_advance_total = array();
        $order_advance_breakup = array();
        $advance_locked_status = array();

       foreach ($results as $key => $result) {
            $order_id = $result['order']['order_id'];
            $collect_suborder_status_id = array();
            if ($result['order']['code_version'] != '1.0') {
                $order_advance_total[$order_id] = OrderInfo::getTotalAdvance($this->db, $order_id);
                $order_advance_breakup[$result['order']['order_id']] = AdvanceVoucher::getAdvanceVouchersBySuborderGroup($this->db, $order_id);
            }

            $payment_history           = $this->model_sale_order->getOrderPaymentHistory($order_id);
            $tentative_advance_history = $this->model_sale_order->getTentativeAdvanceHistory($order_id);
            $getOnlyTentativeAmount    = $this->model_sale_order->getOnlyTentativeAmount($order_id);

            $amountTentativeAdvance  = 0;
            $amountTentativeAdvances = $this->model_sale_order->getAmountTentativeAdvance($order_id);

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
            $data['order_taged_sales_staff'] = $this->model_sale_order->getOrderTagedSalesStaff($order_id);
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
            $data['orders'][$key]['order']['order_processing_date'] = $this->model_sale_order->getOrderProcessingDate($order_id);
            $data['orders'][$key]['order']['master_id']             = $customer_info[$customer_id]['master_id'] ?? 0;
            $data['orders'][$key]['order']['sales_staff_name']      = implode('<br>', $tag_sales_staff);
            $data['orders'][$key]['order']['customer']              = $result['order']['firstname'] . ' ' . $result['order']['lastname'];
            $data['orders'][$key]['order']['website_link']          = $customer_website_ulrs->$customer_id ?? '';
            $data['orders'][$key]['order']['payment_mode']          = (trim(strtolower($result['order']['payment_code'])) == 'credit') ? 'Credit' : ((trim(strtolower($result['order']['payment_code'])) == 'cod') ? 'C' : 'P');
            $data['orders'][$key]['order']['total_amount']          = $result['order']['total'];
            $data['orders'][$key]['order']['payment_history']       = $payment_history;
            $data['orders'][$key]['order']['tentative_advance_history'] = $tentative_advance_history;
            $data['orders'][$key]['order']['getOnlyTentativeAmount']    = $getOnlyTentativeAmount;
            $data['orders'][$key]['order']['app_installed']         = $this->model_sale_order->getGcmRegId($customer_id);
            
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
                    $data['orders'][$key]['suborder'][$key_suborder_id]['view'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'] . '&suborder_id=' . $key_suborder_id . $general_url. '&filter_customer_id=' . $result['order']['customer_id'], 'SSL');
                } else {
                    $data['orders'][$key]['suborder'][$key_suborder_id]['view'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'] . '&suborder_id=' . $key_suborder_id . $general_url. '&filter_customer_id=' . $result['order']['customer_id'], 'SSL');
                }

                if( $suborder_data['invoice_no'] == 0 || in_array($this->user->getId(), explode(',',ADMIN_IDS)) || in_array($this->user->getId(), explode(',',OPERATIONS_ADMIN_IDS))) 
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

                $get_credit_note_amt = $buyer_invoice->getTotalCreditNoteForOrder($order_id);
                $payment_code        = $data['orders'][$key]['order']['payment_code'];
                $order_total_amt     = $data['orders'][$key]['order']['total_amount'];
                $payment_received    = abs($get_credit_note_amt) + abs($data['orders'][$key]['order']['payment_history']['payed_by_customer']);
                $payment_received    = $payment_received + $amountTentativeAdvance;

                $order_status_id = $suborder_data['order_status_id'];
                $collect_suborder_status_id[$key_suborder_id] = $suborder_data['order_status_id'];

                $data['orders'][$key]['suborder'][$key_suborder_id]['delivery_date']='';
                if (!empty($suborder_data['order_history'])) {
                    foreach ($suborder_data['order_history'] as $suborder) {
                        if ($suborder['order_status_id'] == '15') {
                          $data['orders'][$key]['suborder'][$key_suborder_id]['delivery_date'] = date("d-m-Y", strtotime($suborder['date_added'])) ;
                        }
                    }
                }
            }
            $franchise_data = array();
            
            //color coding for credit
            $data['orders'][$key]['order']['credit_color']    = false;
            

            $data['orders'][$key]['order']['franchise_color'] = false;

            if ( !empty($result['order']['store_voucher']) ) {
                $franchise = new Franchise($this);
                $franchise_data = $franchise->getFranchiseData($result['order']['customer_id']);    
            }

            if (trim(strtolower($result['order']['payment_code'])) == 'credit') {
                $data['orders'][$key]['order']['credit_color'] = true;
            } else if(!empty($franchise_data) && ($result['order']['store_voucher'] == $franchise_data['franchise_coupon'])){
                $data['orders'][$key]['order']['franchise_color'] = true;
            }

            // get total of Life Time Values
            $getLtvTotalOrders         = 0;
            $getLtvTotalOrders_amt     = 0;
            
            $getLtvOrdersReturn        = 0;
            $getLtvOrdersReturnAmt     = 0; 

            $getLtvCodFailedOrders     = 0;
            $getLtvCodFailedOrdersAmt  = 0;
            
            $getLtvDeliveryIssueOrders = 0;
            $getLtvDeliveryIssueOrdersAmt = 0;

            $getLtvCancelledOrders     = 0;
            $getLtvCancelledOrdersAmt  = 0;

            $get_last_order_date = array();

            if(!empty($all_related_ids_array)){
                foreach ($all_related_ids_array as $cust_id) {
                    $getLtvTotalOrders         += $ltvTotalOrders[$cust_id]['total_orders'] ?? 0 ;
                    $getLtvTotalOrders_amt     += $ltvTotalOrders[$cust_id]['total_orders_amt'] ?? 0 ;
                    
                    $getLtvOrdersReturn        += $ltvOrdersReturn[$cust_id]['total_orders'] ?? 0 ;
                    $getLtvOrdersReturnAmt     += $ltvOrdersReturn[$cust_id]['credit_note_amount'] ?? 0 ;
                    
                    $getLtvCodFailedOrders     += $ltvCodFailedOrders[$cust_id]['total_orders'] ?? 0;
                    $getLtvCodFailedOrdersAmt  += $ltvCodFailedOrders[$cust_id]['codFailed_amount'] ?? 0;
                    
                    $getLtvDeliveryIssueOrders += $ltvDeliveryIssueOrders[$cust_id]['total_orders'] ?? 0;
                    $getLtvDeliveryIssueOrdersAmt += $ltvDeliveryIssueOrders[$cust_id]['deliveryIssueAmt'] ?? 0;                                                       
                    $getLtvCancelledOrders     += $ltvCancelledOrders[$cust_id]['total_orders'] ?? 0 ;
                    $getLtvCancelledOrdersAmt  += $ltvCancelledOrders[$cust_id]['cancelled_amount'] ?? 0;

                    $get_last_order_date[]      = $ltvTotalOrders[$cust_id]['last_order_date'] ?? '' ; 
                }
            }

            // order amount in percentage
            $getLtvTotalReturnPer           = (($getLtvOrdersReturnAmt) 
                                                    ? round(100*(float)$getLtvOrdersReturnAmt/(float)$getLtvTotalOrders_amt, 2 )
                                                    : 0 ) . '%';
            $getLtvTotalCodFailedPer        = (($getLtvCodFailedOrdersAmt) 
                                                    ? round(100*(float)$getLtvCodFailedOrdersAmt/(float)$getLtvTotalOrders_amt, 2 )
                                                    : 0 ) . '%';
            $getLtvTotalDeliveryIssuePer    = (($getLtvDeliveryIssueOrdersAmt) 
                                                    ? round(100*(float)$getLtvDeliveryIssueOrdersAmt/(float)$getLtvTotalOrders_amt, 2 )
                                                    : 0 ) . '%';
            $getLtvTotalCancelledOrdersPer  = (($getLtvCancelledOrdersAmt) 
                                                    ? round(100*(float)$getLtvCancelledOrdersAmt/(float)$getLtvTotalOrders_amt, 2 )
                                                    : 0 ) . '%';


            // Life Time Value show on order list page against of particular order 
            $data['orders'][$key]['order']['ltvTotalOrders'] = array('total_orders'=>$getLtvTotalOrders, 
                                                               'total_orders_amt'  => abbreviateInIndianCurrency($getLtvTotalOrders_amt),
                                                                'last_order_date' => max($get_last_order_date) );

            $data['orders'][$key]['order']['ltvTotalReturn']        = array('total_orders'      => $getLtvOrdersReturn,
                                                                            'totalOrderAmtPer'  => $getLtvTotalReturnPer
                                                                           );

            $data['orders'][$key]['order']['ltvCodFailedOrders']    = array('total_orders'      => $getLtvCodFailedOrders,
                                                                            'totalOrderAmtPer'  => $getLtvTotalCodFailedPer
                                                                           );
            
            $data['orders'][$key]['order']['ltvDeliveryIssueOrders']= array('total_orders'      => $getLtvDeliveryIssueOrders,
                                                                            'totalOrderAmtPer'  => $getLtvTotalDeliveryIssuePer
                                                                           );
            $data['orders'][$key]['order']['ltvCancelledOrders']    = array('total_orders'      => $getLtvCancelledOrders,
                                                                            'totalOrderAmtPer'  => $getLtvTotalCancelledOrdersPer
                                                                           );
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

        if (isset($this->session->data['error_warning'])) {
            $data['error_warning'] = $this->session->data['error_warning'];

            unset($this->session->data['error_warning']);
        } else {
            $data['error_warning'] = '';
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

        $data['sort_order'] = $this->url->link('operations/lazypay_order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $sort_url, 'SSL');
        $data['sort_customer'] = $this->url->link('operations/lazypay_order', 'token=' . $this->session->data['token'] . '&sort=customer' . $sort_url, 'SSL');
        $data['sort_date_added'] = $this->url->link('operations/lazypay_order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $sort_url, 'SSL');
        $data['sort_date_modified'] = $this->url->link('operations/lazypay_order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $sort_url, 'SSL');

        $data['lazypay_action_url'] = $this->url->link('operations/lazypay_order/takeLazypayAction', 'token=' . $this->session->data['token'], 'SSL');

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
        $pagination->url = $this->url->link('operations/lazypay_order', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

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
        //pr($data['orders']); die;
        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('operations/lazypay_order_list.tpl', $data));
    }

    public function takeLazypayAction() {
    
    $this->load->model('checkout/order', 'frontend');
    
    if (method_exists($this->registry, 'get')) {
        $model_checkout_order = $this->registry->get('frontend_model_checkout_order');
    } else {
        $model_checkout_order = $this->registry->frontend_model_checkout_order;
    }

    //$model_checkout_order = $this->registry->get('frontend_model_checkout_order');

      $action = $this->request->post['action'];
      $data = array(
        'order_id'      => $this->request->post['order_id'] ?? 0,
        'order_no'      => $this->request->post['order_no'] ?? 0,
        'customer_id'   => $this->request->post['customer_id'] ?? 0,
        'credit_note_id'=> $this->request->post['credit_note_id'] ?? '',
        'detail'        => $this->request->post['detail'] ?? '',
        'total'         => $this->request->post['total'] ?? 0,
        'action'        => $action,
        'order_status_id' => $this->config->get('lazypay_order_status_id')
      );

      $credit_payment_gateway = new LazypayPayment($this);

      try {

            switch ($action) 
            {
            
                case 'capture':

                  $response = $credit_payment_gateway->captureOrder($data, $model_checkout_order);

                  break;

                case 'refund':

                  $response = $credit_payment_gateway->refundOrder($data, $model_checkout_order);

                  break;
                  
                case 'release':

                  $response = $credit_payment_gateway->releaseOrder($data, $model_checkout_order);

                  break;  

                default:

                  break;
            }

      } catch ( Exception $e ) {

         $response['error_warning'] = $e->getMessage(); 
      } 

    if(isset($response['success']) && $response['success']) {
        $this->session->data['success'] = $response['message'] ?? "Transaction is successful.";  
    }else if(isset($response['error_warning']) && $response['error_warning']) {
        $this->session->data['error_warning'] = $response['message'] ?? "Some error occurred in LazyPay.";    
    }

      $this->response->redirect($this->url->link('operations/lazypay_order', 'token=' . $this->session->data['token'], 'SSL'));
    }
    
    public function getLazypayTransactions() {
      $order_no = $this->request->get['order_no'];
      $credit_payment_gateway = new LazypayPayment($this);
      $data['order_no'] = $order_no;
      $data['lazypay_transactions'] = $credit_payment_gateway->getLazypayTransactionLogUsingOrderNo($order_no);
      $this->response->setOutput($this->load->view('operations/lazypay_transactions.tpl', $data));
    }

}
