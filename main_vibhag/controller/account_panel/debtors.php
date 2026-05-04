<?php
class ControllerAccountPanelDebtors extends Controller{    
    private $error = array();

    public function index() {

        $this->load->model('account_panel/debtors');
        
        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
        //$this->document->setTitle($this->language->get('heading_title'));     
        $this->document->setTitle('Sundry Debtors Report');

        //filtering
        if (isset($this->request->get['filter_order_no'])) {
            $filter_order_no = $this->request->get['filter_order_no'];
        } else {
            $filter_order_no = null;
        }
        if (isset($this->request->get['filter_suborder_id'])) {
            $filter_suborder_id = $this->request->get['filter_suborder_id'];
        } else {
            $filter_suborder_id = null;
        }

        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = null;
        }
        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = null;
        }
        if (isset($this->request->get['filter_tracking_no'])) {
            $filter_tracking_no = $this->request->get['filter_tracking_no'];
        } else {
            $filter_tracking_no = null;
        }
        if (isset($this->request->get['filter_total_from'])) {
            $filter_total_from = $this->request->get['filter_total_from'];
        } else {
            $filter_total_from = null;
        }
        if (isset($this->request->get['filter_total_to'])) {
            $filter_total_to = $this->request->get['filter_total_to'];
        } else {
            $filter_total_to = null;
        }
        if (isset($this->request->get['filter_order_status'])) {
            $filter_order_status = $this->request->get['filter_order_status'];
        } else {
            $filter_order_status = null;
        }
        if (isset($this->request->get['filter_payment_mode'])) {
            $filter_payment_mode = $this->request->get['filter_payment_mode'];
        } else {
            $filter_payment_mode = null;
        }



        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_limit_admin');
        }

        if (isset($this->request->get['filter_page_limit'])) {
            $filter_page_limit = $this->request->get['filter_page_limit'];
        } else {
            $filter_page_limit = $this->config->get('config_limit_admin');
        }


        $url = '';

        if (isset($this->request->get['filter_order_no'])) {
            $url .= '&filter_order_no=' .$this->request->get['filter_order_no'];
        }
        if (isset($this->request->get['filter_suborder_id'])) {
            $url .= '&filter_suborder_id=' .$this->request->get['filter_suborder_id'];
        }
        if(!empty($filters['filter_date_from'])){
            //$sql .= " AND invoice_date >= '".$this->db->escape($filters['filter_date_from'])."' ";
            $url .= '&filter_date_from >=' .$this->request->get['filter_date_from'];
        }
        if(!empty($filters['filter_date_to'])){
            //$sql .= " AND invoice_date <= '".$this->db->escape($filters['filter_date_to'])."' ";
            $url .= '&filter_date_to <=' .$this->request->get['filter_date_to'];
        }
        if (isset($this->request->get['filter_tracking_no'])) {
            $url .= '&filter_tracking_no=' .$this->request->get['filter_tracking_no'];
        }
        if (isset($this->request->get['filter_total_from'])) {
            $url .= '&filter_total_from=' .$this->request->get['filter_total_from'];
        }
        if (isset($this->request->get['filter_total_to'])) {
            $url .= '&filter_total_to=' .$this->request->get['filter_total_to'];
        }
        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' .$this->request->get['filter_order_status'];
        }
        if (isset($this->request->get['filter_payment_mode'])) {
            $url .= '&filter_payment_mode=' .$this->request->get['filter_payment_mode'];
        }


        $filter_data = array(
            'filter_order_no'        => $filter_order_no,
            'filter_suborder_id'     => $filter_suborder_id,
            'filter_date_from'  => $filter_date_from,
            'filter_date_to'    => $filter_date_to,
            'filter_tracking_no'        => $filter_tracking_no,
            'filter_total_from'    => $filter_total_from,
            'filter_total_to'    => $filter_total_to,
            'filter_order_status'    => $filter_order_status,
            'filter_payment_mode'    => $filter_payment_mode,
            // 'sort'           => $sort,
            // 'order'          => $order,
            'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
            //'limit'             => $this->config->get('config_limit_admin')
            'limit'             => $filter_page_limit
        );
/*
        $paymentModes = $this->model_account_panel_debtors->getPaymentModes();
        $data['paymentModes'] = $paymentModes;

        //get all order status
        $this->load->model('localisation/order_status');
        $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

        $order_statuses = array();
        
        $data['order_statuses_qry'] = $order_statuses_qry;
//echo "<pre>";print_r($order_statuses_qry);die;
*/

        
        //$subOrderDetails = $this->model_account_panel_debtors->getSubOrders($filter_data);
        $debtors = $this->model_account_panel_debtors->getDebtors($filter_data);
        $data['debtors'] = $debtors;
//echo "<pre>";print_r($debtors);die;
        $data['debtorsStatement']  = array();
        if(!empty($debtors)){
            foreach ($debtors as $key => $debtor) {
/*
                //$data['debtorsStatement'][$debtor['ledger_id']] = $debtor;
                //$data['debtorsStatement']['ledger_name'] = $debtor['ledger_name'];

                //$data['debtorsStatement'][$debtor['ledger_id']]['receipts'] = $this->model_account_panel_debtors->getReceiptByLedgerId($debtor['ledger_id']);
                //$data['debtorsStatement'][$debtor['ledger_id']]['payments'] = $this->model_account_panel_debtors->getPaymentByLedgerId($debtor['ledger_id']);
*/
                $data['debtorsStatement'][$key]['ledger_id'] = $debtor['ledger_id'];
                $data['debtorsStatement'][$key]['ledger_name'] = $debtor['ledger_name'];
                $data['debtorsStatement'][$key]['receipts'] = $this->model_account_panel_debtors->getReceiptByLedgerId($debtor['ledger_id']);
                //$data['debtorsStatement'][$key]['payments'] = $this->model_account_panel_debtors->getPaymentByLedgerId($debtor['ledger_id']);
                $data['debtorsStatement'][$key]['sales'] = $this->model_account_panel_debtors->getSubOrdersByLedgerID($debtor['ledger_id']);



            }
        }

        // Autoloading the lanugage
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('wsb_purchase/import', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        
        $data['add'] = $this->url->link('wsb_import/import/import', 'token=' . $this->session->data['token'] . $url, 'SSL');

         if (isset($this->session->data['error'])) {
             $data['error_warning'] = $this->session->data['error'];

             unset($this->session->data['error']);
         } elseif (isset($this->error['warning'])) {
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
        
        // URL for pagination
        $pagination_url = $url;
/*
        $pagination = new Pagination();
        //$pagination->total = $product_total;
        $pagination->total = $subOrderCount;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        //$pagination->limit = 2;
        $pagination->url = $this->url->link('account_panel/debtors', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'],
                                    ($subOrderCount) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
                                    ((($page - 1) * $this->config->get('config_limit_admin')) > ($subOrderCount - $this->config->get('config_limit_admin'))) ? $subOrderCount : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
                                        $subOrderCount, ceil($subOrderCount / $this->config->get('config_limit_admin')));
*/
        $data['filter_order_no'] = $filter_order_no;
        $data['filter_suborder_id'] = $filter_suborder_id;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        $data['filter_tracking_no'] = $filter_tracking_no;
        $data['filter_total_from'] = $filter_total_from;
        $data['filter_total_to'] = $filter_total_to;
        $data['filter_order_status'] = $filter_order_status;
        $data['filter_payment_mode'] = $filter_payment_mode;

        $data['page_limit_array'] = array('30','60','100','200','500','1000');
        $data['filter_page_limit'] = $filter_page_limit;

        $data['token'] = $this->session->data['token'];
        $data['route'] = $this->request->get['route'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account_panel/debtors.tpl', $data));
    }

    public function exportcsv() {

        $this->load->model('account_panel/orderpayment');
        
        $data = array();// Initializing the data array to be passed on to template f

        //filtering
        if (isset($this->request->get['filter_order_no'])) {
            $filter_order_no = $this->request->get['filter_order_no'];
        } else {
            $filter_order_no = null;
        }
        if (isset($this->request->get['filter_suborder_id'])) {
            $filter_suborder_id = $this->request->get['filter_suborder_id'];
        } else {
            $filter_suborder_id = null;
        }

        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = null;
        }
        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = null;
        }
        if (isset($this->request->get['filter_tracking_no'])) {
            $filter_tracking_no = $this->request->get['filter_tracking_no'];
        } else {
            $filter_tracking_no = null;
        }
        if (isset($this->request->get['filter_total_from'])) {
            $filter_total_from = $this->request->get['filter_total_from'];
        } else {
            $filter_total_from = null;
        }
        if (isset($this->request->get['filter_total_to'])) {
            $filter_total_to = $this->request->get['filter_total_to'];
        } else {
            $filter_total_to = null;
        }
        if (isset($this->request->get['filter_order_status'])) {
            $filter_order_status = $this->request->get['filter_order_status'];
        } else {
            $filter_order_status = null;
        }
        if (isset($this->request->get['filter_payment_mode'])) {
            $filter_payment_mode = $this->request->get['filter_payment_mode'];
        } else {
            $filter_payment_mode = null;
        }



        $url = '';

        if (isset($this->request->get['filter_order_no'])) {
            $url .= '&filter_order_no=' .$this->request->get['filter_order_no'];
        }
        if (isset($this->request->get['filter_suborder_id'])) {
            $url .= '&filter_suborder_id=' .$this->request->get['filter_suborder_id'];
        }
        if(!empty($filters['filter_date_from'])){
            //$sql .= " AND invoice_date >= '".$this->db->escape($filters['filter_date_from'])."' ";
            $url .= '&filter_date_from >=' .$this->request->get['filter_date_from'];
        }
        if(!empty($filters['filter_date_to'])){
            //$sql .= " AND invoice_date <= '".$this->db->escape($filters['filter_date_to'])."' ";
            $url .= '&filter_date_to <=' .$this->request->get['filter_date_to'];
        }
        if (isset($this->request->get['filter_tracking_no'])) {
            $url .= '&filter_tracking_no=' .$this->request->get['filter_tracking_no'];
        }
        if (isset($this->request->get['filter_total_from'])) {
            $url .= '&filter_total_from=' .$this->request->get['filter_total_from'];
        }
        if (isset($this->request->get['filter_total_to'])) {
            $url .= '&filter_total_to=' .$this->request->get['filter_total_to'];
        }
        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' .$this->request->get['filter_order_status'];
        }
        if (isset($this->request->get['filter_payment_mode'])) {
            $url .= '&filter_payment_mode=' .$this->request->get['filter_payment_mode'];
        }


        $filter_data = array(
            'filter_order_no'        => $filter_order_no,
            'filter_suborder_id'     => $filter_suborder_id,
            'filter_date_from'  => $filter_date_from,
            'filter_date_to'    => $filter_date_to,
            'filter_tracking_no'        => $filter_tracking_no,
            'filter_total_from'    => $filter_total_from,
            'filter_total_to'    => $filter_total_to,
            'filter_order_status'    => $filter_order_status,
            'filter_payment_mode'    => $filter_payment_mode,

            //'start'             => ($page - 1) * $this->config->get('config_limit_admin'),

            //'limit'             => $filter_page_limit
        );


        $data['subOrderDetails'] = '';
        $data['subOrders']  = array();

        if(!empty($subOrderDetails)){
            foreach ($subOrderDetails as $key => $subOrderDetail) {
                //$data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']] = $subOrderDetail;
                $data['subOrders'][$subOrderDetail['order_id']] = $subOrderDetail;
                
                $data['subOrders'][$subOrderDetail['order_id']]['suborder'] = $subOrderDetail['total'];
                
                $data['subOrders'][$subOrderDetail['order_id']]['amounts'] = '';
                
                $data['subOrders'][$subOrderDetail['order_id']]['balance'] = $data['subOrders'][$subOrderDetail['order_id']]['suborder'] - $data['subOrders'][$subOrderDetail['order_id']]['amounts'];
            }
        }

        $subOrders = $data['subOrders'];

//
        $co = count($subOrders);
        if ($co > 0)
        {
//
            $filename = "order_payment.csv";
            
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=$filename");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            //$formmngmntxlss = $this->model->getAllCoverCIDTableDataJoin();
            
            $content = array();
                    
            $title = array("Order No.","Order Date","Order Value", "Amount Received", "Balance");
            //$title = array("Order No.","Order Date","Order Value");
    
            //foreach ($getTransporterByAlls as $getTransporterByAll ) {
            foreach ($subOrders as $subOrder ) {
            
                $row = array();
                
                $order_no = $subOrder['order_no'] ;
                $date_added = $subOrder['date_added'] ;
                $total = $subOrder['total'] ;
                $amounts = $subOrder['amounts'] ;
                $balance = $subOrder['balance'] ;

                
                $row[] = stripslashes( $order_no );
                $row[] = stripslashes( $date_added );                                  
                $row[] = stripslashes( $total );   
                $row[] = stripslashes( $amounts );   
                $row[] = stripslashes( $balance );   


                $content[] = $row;
            }

            $output = fopen('php://output', 'w');
            fputcsv($output, $title);
            
            foreach ($content as $con) {
                fputcsv($output, $con);
            }
        }
//


    }





}

?>
