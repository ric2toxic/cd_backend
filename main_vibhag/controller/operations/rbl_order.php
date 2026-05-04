<?php

class ControllerOperationsRblOrder extends Controller {

    private $error = array();
    private $_order_info = array();
    private $_delivered_state_id = 15;
    private $_failed_state_id = 8;
    private $_cancelled_state_id = 2;
    private $_processed_state_id = 9;
    private $_tentative_processed_state_id = 16;
    private $_return_states = array(2, 8, 9, 11, 15, 16);
    private $_disbursal_button = '+24 hours'; //show disbursal button after delivered mark
    public function index() {

        $this->load->language('operations/rbl_order');
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('sale/order');
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
            'href' => $this->url->link('operations/rbl_order', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['orders'] = array();

        $results     = array();
        $filter_data['filter_payment_code'] = 'rbl_credit';
        $orders        = $this->model_sale_order->getOrders($filter_data)['data'] ?? array();
        $order_ids = array();

        if ($orders) {
            $rblPayment = new RblPayment($this);
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
            $order_details_arr = OrderInfo::getOrderInfo($this->db, $order_ids, '', $selector);
            $check_status = array_merge(ORDER_STATUS_CLUSTERS['delivered'],ORDER_STATUS_CLUSTERS['cancelled']);

            //get all order status
            $this->load->model('localisation/order_status');
            $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();
            $data['order_statuses'] = $order_statuses_qry;
            $order_statuses = array_combine(
                                 array_column($order_statuses_qry, 'order_status_id'), 
                                 array_column($order_statuses_qry, 'name'));
            $order_statuses[0] = 'Missing Order';
            foreach ($orders as $order_row) {
               
                $order_id       = (int)$order_row['order_id'];
                $order_no       = $order_row['order_no'];
                $order_data     = $order_row;
                $suborders      = $order_details_arr[$order_id]['suborder'];

                $data['orders'][$order_id]['order'] = $order_row;
                $data['orders'][$order_id]['suborder'] = $order_details_arr[$order_id]['suborder'];
                $data['orders'][$order_id]['order']['total_amount']                 = $order_row['total'];
                $data['orders'][$order_id]['order']['customer']                     = $order_row['firstname'] . ' ' . $order_row['lastname'];
                $data['orders'][$order_id]['order']['all_suborder_cancelled']       = OrderInfo::isAllSuborderMarkedCancelled($this->db, $order_id);
                $data['orders'][$order_id]['order']['order_cancellation_status']    = $rblPayment->isOrderAlreadyCancelledOnRBL($order_id); 
                $data['orders'][$order_id]['order']['order_punch_amount']           = $rblPayment->getRBLOrderPunchAmount($order_id); 
                $data['orders'][$order_id]['order']['net_payble_amount']            = $this->calculateOrderNetpayableAmount($data['orders'][$order_id]['suborder']);
                $data['orders'][$order_id]['order']['order_processing_date']        = $this->model_sale_order->getOrderProcessingDate($order_id);

                if(!empty($suborders)) {

                    foreach ($suborders as $suborder_id => $suborder_data) {
                       
                        $suborder_total = $this->currency->format($suborder_data['total'], $data['orders'][$order_id]['order']['currency_code'], $data['orders'][$order_id]['order']['currency_value'], true);

                        $data['orders'][$order_id]['suborder'][$suborder_id]['suborder_total'] = $suborder_total;
                        $data['orders'][$order_id]['suborder'][$suborder_id]['status'] = $order_statuses[$suborder_data['order_status_id']] ;
                    
                        if( $suborder_data['invoice_no'] > 0 )
                        {
                            /*invoice download link for RBL order listing */
                            $file_name = array();
                            $file_name['order_id'] = (int) $suborder_data['order_id'];
                            $file_name['suborder_id'] = $suborder_data['suborder_id'];
                            $file_name = serialize($file_name);
                            $file_name = base64_encode($file_name);
                            $data['orders'][$order_id]['suborder'][$suborder_id]['detail_invoice'] = $this->securefiledownload->getDownloadLink('buyer_b2b_invoice', $file_name, false); // this is same as B2B invoice
                            /*invoice download link for RBL order listing */
                        }

                        $data['orders'][$order_id]['suborder'][$suborder_id]['delivery_date']='';
                        if (!empty($suborder_data['order_history'])) {
                            foreach ($suborder_data['order_history'] as $suborder) {
                                if ($suborder['order_status_id'] == '15') {
                                  $data['orders'][$order_id]['suborder'][$suborder_id]['delivery_date'] = date("d-m-Y", strtotime($suborder['date_added'])) ;
                                }
                            }
                        }

                        $data['orders'][$order_id]['disbursal_status_data'][] = array(
                            'suborder_id'       => $suborder_id,
                            'invoice_no'        => $suborder_data['invoice_no'],
                            'order_status_id'   => $suborder_data['order_status_id']
                        );

                    }

                    //check sub-orders status and their invoice status for disbursal button
                    //all sub-orders must be delivered or cancelled status
                    $disbursal_button = 0;
                    if(!empty($data['orders'][$order_id]['disbursal_status_data'])) 
                    {
                        foreach ($data['orders'][$order_id]['disbursal_status_data'] as $button_key => $button_value) {
                            $check_status = array_merge(ORDER_STATUS_CLUSTERS['delivered'],ORDER_STATUS_CLUSTERS['cancelled']);
                            if(!in_array($button_value['order_status_id'],$check_status)) {
                                $disbursal_button = 0;
                                break;
                            }else if($button_value['invoice_no'] > 0 && in_array($button_value['order_status_id'],ORDER_STATUS_CLUSTERS['delivered'])) {
                                $disbursal_button = 1;
                            }
                        }

                        //check order disbursal or disbursed status
                        $is_disbursal = $rblPayment->isDisbursalRequestSentForSuborder($order_id, $order_no,'DisbursalRequest');
                        $is_disbursed = $rblPayment->isDisbursalRequestSentForSuborder($order_id, $order_no,'Disbursed');
                        if( $is_disbursal || $is_disbursed ){
                            $disbursal_button = 0;
                        }

                        $data['orders'][$order_id]['is_disbursal'] = $is_disbursal;
                        $data['orders'][$order_id]['is_disbursed'] = $is_disbursed;  
                    }
                   
                    $data['orders'][$order_id]['show_disbursal_button'] = $disbursal_button;
                }
            }
        }

        $data['user']                  = $this->user->getUserName();
        $data['user_id']               = $this->user->getId();
        $data['token']                 = $this->session->data['token'];

        if (!empty($this->session->data['error_warning'])) {
            $data['error_warning'] = $this->session->data['error_warning'];

            unset($this->session->data['error_warning']);
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

        $data['rbl_action_url'] = $this->url->link('operations/rbl_order/takeRBLAction', 'token=' . $this->session->data['token'], 'SSL');

        $pagination = new PaginationV2();
        $pagination->page = $page;
        $pagination->next = end($orders)['order_id'];
        $pagination->total = count($orders);
        $pagination->limit = 15;
        $pagination->url = $this->url->link('operations/rbl_order', 'token=' . $this->session->data['token'] . $general_url . '&page={page}', 'SSL');
        $data['pagination'] = $pagination->render();

        $data['header']          = $this->load->controller('common/header');
        $data['column_left']     = $this->load->controller('common/column_left');
        $data['footer']          = $this->load->controller('common/footer');
        $data['duplicate_popup'] = $this->load->view('sale/marked_duplicate_popup.tpl');
        $this->response->setOutput($this->load->view('operations/rbl_order_list.tpl', $data));
    }

    public function takeRBLAction() 
    {
          $action = $this->request->post['action'];
          $data = array(
            'order_id'      => $this->request->post['order_id'] ?? 0,
            'order_no'      => $this->request->post['order_no'] ?? 0,
            'suborder_id'   => $this->request->post['suborder_id'] ?? 0,
            'customer_id'   => $this->request->post['customer_id'] ?? 0,
            'customer_name'   => $this->request->post['customer_name'] ?? 0,
            'customer_company'=> $this->request->post['customer_company'] ?? 0,
            'invoice_id'    => $this->request->post['invoice_id'] ?? 0,
            'invoice_date'  => $this->request->post['invoice_date'] ?? 0,
            'date_added'  => $this->request->post['date_added'] ?? 0,
            'invoice_total' => $this->request->post['invoice_total'] ?? 0,
            'detail'        => $this->request->post['detail'] ?? '',
            'total'         => $this->request->post['total'] ?? 0,
            'order_punch_amount' => $this->request->post['order_punch_amount'] ?? 0,
            'action'        => $action,
            'order_status_id' => 1
          );
          
          $credit_payment_gateway = new RblPayment($this);

          try {

                switch ($action) 
                {
                
                    case 'disbursal_request':

                      $response = $credit_payment_gateway->generateDisbursalRequest($data);

                      break;

                    case 'cancel_request':

                      $response = $credit_payment_gateway->sendOrderCancelRequestToRBL($data);

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
            $this->session->data['error_warning'] = $response['message'] ?? "Some error occurred in RBL.";    
        }

        $this->response->redirect($this->url->link('operations/rbl_order', 'token=' . $this->session->data['token'], 'SSL'));
    }


    public function calculateOrderNetpayableAmount( array $suborders ):float
    {
        $net_payable_amount = 0;

        if( !empty( $suborders ) ) {
            
            foreach ( $suborders as $key => $value ) {
               
                if(
                    $value['invoice_no'] > 0 
                    &&
                    in_array($value['order_status_id'], ORDER_STATUS_CLUSTERS['delivered'])
                ) {

                   $buyer_invoice = new BuyerInvoice( $this );

                   $invoice_totals = $buyer_invoice->getTotals($value['order_id'], $value['suborder_id']);

                   if( !empty( $invoice_totals['net_amount']['value'] ) ) {

                        $net_payable_amount += (float)$invoice_totals['net_amount']['value'];
                   }
                }
            }
        }

        return (float)$net_payable_amount;
    }

}
