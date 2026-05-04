<?php
class ControllerAccountPanelCod extends Controller{    
    private $error = array();

    public function index() {

        $this->load->model('account_panel/cod');
        
        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
        //$this->document->setTitle($this->language->get('heading_title'));     
        $this->document->setTitle('COD Report');

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
        /*
        if (isset($this->request->get['filter_order_status'])) {
            $filter_order_status = $this->request->get['filter_order_status'];
        } else {
            $filter_order_status = null;
        }
        */
        if (isset($this->request->get['filter_payment_mode'])) {
            $filter_payment_mode = $this->request->get['filter_payment_mode'];
        } else {
            $filter_payment_mode = null;
        }        
        if (isset($this->request->get['filter_cod'])) {
            $filter_cod = $this->request->get['filter_cod'];
        } else {
            $filter_cod = null;
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
        if(!empty($this->request->get['filter_date_from'])){
            $url .= '&filter_date_from=' .$this->request->get['filter_date_from'];
        }
        if(!empty($this->request->get['filter_date_to'])){
            $url .= '&filter_date_to=' .$this->request->get['filter_date_to'];
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
        /*
        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' .$this->request->get['filter_order_status'];
        }
        */
        if (isset($this->request->get['filter_payment_mode'])) {
            $url .= '&filter_payment_mode=' .$this->request->get['filter_payment_mode'];
        }        
        if (isset($this->request->get['filter_cod'])) {
            $url .= '&filter_cod=' .$this->request->get['filter_cod'];
        }


        $filter_data = array(
            'filter_order_no'        => $filter_order_no,
            'filter_suborder_id'     => $filter_suborder_id,
            'filter_date_from'  => $filter_date_from,
            'filter_date_to'    => $filter_date_to,
            'filter_tracking_no'        => $filter_tracking_no,
            'filter_total_from'    => $filter_total_from,
            'filter_total_to'    => $filter_total_to,
            //'filter_order_status'    => $filter_order_status,
            'filter_payment_mode'    => $filter_payment_mode,
            'filter_cod'    => $filter_cod,
            // 'sort'           => $sort,
            // 'order'          => $order,
            'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
            //'limit'             => $this->config->get('config_limit_admin')
            'limit'             => $filter_page_limit
        );

        //$paymentModes = $this->model_account_panel_cod->getPaymentModes();
        //$data['paymentModes'] = $paymentModes;        

        $cods = $this->model_account_panel_cod->getCods();
        $data['cods'] = $cods;
        /*
        //get all order status
        $this->load->model('localisation/order_status');
        $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

        $order_statuses = array();

        $data['order_statuses_qry'] = $order_statuses_qry;
        */
        $subOrderDetails = $this->model_account_panel_cod->getSubOrders($filter_data);
        $subOrderCount = $this->model_account_panel_cod->getSubOrdersCount($filter_data);
        $data['subOrderDetails'] = $subOrderDetails;

        $data['subOrders']  = array();

        if(!empty($subOrderDetails)){
            foreach ($subOrderDetails as $key => $subOrderDetail) {
                //$data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']] = $subOrderDetail;
                $data['subOrders'][$subOrderDetail['order_id']] = $subOrderDetail;
                
                $data['subOrders'][$subOrderDetail['order_id']]['suborder'] = $this->model_account_panel_cod->getSubOrdersByOrderID($subOrderDetail['order_id'], $subOrderDetail['payment_gateway']);
                
                $data['subOrders'][$subOrderDetail['order_id']]['amounts'] = $this->model_account_panel_cod->getReceiptByOrderId($subOrderDetail['order_id'], $subOrderDetail['payment_gateway']);

            }
        }
//echo "<pre>";print_r($data['subOrders']);die;
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
        if (isset($this->request->get['sort'])) {
            $pagination_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $pagination_url .= '&order=' . $this->request->get['order'];
        }
        */
        $pagination = new Pagination();
        //$pagination->total = $product_total;
        $pagination->total = $subOrderCount;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        //$pagination->limit = 2;
        $pagination->url = $this->url->link('account_panel/cod', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'],
                                    ($subOrderCount) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
                                    ((($page - 1) * $this->config->get('config_limit_admin')) > ($subOrderCount - $this->config->get('config_limit_admin'))) ? $subOrderCount : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
                                        $subOrderCount, ceil($subOrderCount / $this->config->get('config_limit_admin')));

        $data['filter_order_no'] = $filter_order_no;
        $data['filter_suborder_id'] = $filter_suborder_id;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        $data['filter_tracking_no'] = $filter_tracking_no;
        $data['filter_total_from'] = $filter_total_from;
        $data['filter_total_to'] = $filter_total_to;
        //$data['filter_order_status'] = $filter_order_status;
        $data['filter_payment_mode'] = $filter_payment_mode;
        $data['filter_cod'] = $filter_cod;

        $data['page_limit_array'] = array('30','60','100','200','500','1000');
        $data['filter_page_limit'] = $filter_page_limit;

        $data['token'] = $this->session->data['token'];
        $data['route'] = $this->request->get['route'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account_panel/cod.tpl', $data));
    }

    public function exportcsv() {

        $this->load->model('account_panel/cod');
        
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
        /*
        if (isset($this->request->get['filter_order_status'])) {
            $filter_order_status = $this->request->get['filter_order_status'];
        } else {
            $filter_order_status = null;
        }
        */
        if (isset($this->request->get['filter_payment_mode'])) {
            $filter_payment_mode = $this->request->get['filter_payment_mode'];
        } else {
            $filter_payment_mode = null;
        }        
        if (isset($this->request->get['filter_cod'])) {
            $filter_cod = $this->request->get['filter_cod'];
        } else {
            $filter_cod = null;
        }



        $url = '';

        if (isset($this->request->get['filter_order_no'])) {
            $url .= '&filter_order_no=' .$this->request->get['filter_order_no'];
        }
        if (isset($this->request->get['filter_suborder_id'])) {
            $url .= '&filter_suborder_id=' .$this->request->get['filter_suborder_id'];
        }
        if(!empty($this->request->get['filter_date_from'])){
            $url .= '&filter_date_from=' .$this->request->get['filter_date_from'];
        }
        if(!empty($this->request->get['filter_date_to'])){
            $url .= '&filter_date_to=' .$this->request->get['filter_date_to'];
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
        /*
        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' .$this->request->get['filter_order_status'];
        }
        */
        if (isset($this->request->get['filter_payment_mode'])) {
            $url .= '&filter_payment_mode=' .$this->request->get['filter_payment_mode'];
        }        
        if (isset($this->request->get['filter_cod'])) {
            $url .= '&filter_cod=' .$this->request->get['filter_cod'];
        }

        $filter_data = array(
            'filter_order_no'        => $filter_order_no,
            'filter_suborder_id'     => $filter_suborder_id,
            'filter_date_from'  => $filter_date_from,
            'filter_date_to'    => $filter_date_to,
            'filter_tracking_no'        => $filter_tracking_no,
            'filter_total_from'    => $filter_total_from,
            'filter_total_to'    => $filter_total_to,
            //'filter_order_status'    => $filter_order_status,
            'filter_payment_mode'    => $filter_payment_mode,
            'filter_cod'    => $filter_cod
        );
//echo "<pre>"; print_r($filter_data); die;
        $subOrderDetails = $this->model_account_panel_cod->getSubOrders($filter_data);
//echo "<pre>"; print_r($subOrderDetails); die;
        $data['subOrderDetails'] = $subOrderDetails;

        $data['subOrders']  = array();

        if(!empty($subOrderDetails)){
            foreach ($subOrderDetails as $key => $subOrderDetail) {
                //$data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']] = $subOrderDetail;
                $data['subOrders'][$subOrderDetail['order_id']] = $subOrderDetail;
                
                //$data['subOrders'][$subOrderDetail['order_id']]['suborder'] = $this->model_account_panel_cod->getSubOrdersByOrderID($subOrderDetail['order_id'], $subOrderDetail['payment_gateway']);
                
                //$data['subOrders'][$subOrderDetail['order_id']]['amounts'] = $this->model_account_panel_cod->getReceiptByOrderId($subOrderDetail['order_id'], $subOrderDetail['payment_gateway']);

                $data['subOrders'][$subOrderDetail['order_id']]['suborder'] = $this->model_account_panel_cod->getSubOrdersByOrderIDCSV($subOrderDetail['order_id'], $subOrderDetail['payment_gateway']);

            }
        }
        //echo "<pre>"; print_r($data['subOrders']); die;

        $subOrders = $data['subOrders'];


        $co = count($subOrders);
        if ($co > 0)
        {

            $filename = "cod.csv";
            
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=$filename");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            $content = array();
                    
            //$title = array("Order No.","Customer ID","Customer Name","Order Date","Order Value", "Amount Receivable", "Balance");
            $title = array("Order No.","Customer ID","Customer Name","Order Date","Suborder ID", "Courier Partner", "Tracking No.", "Order Status", "Amount Receivable", "Amount Recd.", "Balance");

            foreach ($subOrders as $key => $subOrder) {  
            	
                foreach($subOrder['suborder'] as $suborderVal){     
                    $row = array();    

                    $order_no = $subOrder['order_no'] ;
                    $customer_id = $subOrder['customer_id'] ;
                    $customer_name = $subOrder['customer_name'] ;
                    $date_added = $subOrder['date_added'] ;

                    $suborder_id = $suborderVal['suborder_id'];
                    $courier_partner = $suborderVal['courier_partner'];
                    $tracking_no = $suborderVal['tracking_no'];
                    $net_receivable = $suborderVal['net_receivable'];
                    $name = $suborderVal['name'];
                    $amount = $suborderVal['amount'];
                    $bal = $net_receivable - $amount;


                    $row[] = stripslashes( $order_no );
                    $row[] = stripslashes( $customer_id );
                    $row[] = stripslashes( $customer_name );
                    $row[] = stripslashes( $date_added );   

                    $row[] = stripslashes( $suborder_id );
                    
                    $row[] = stripslashes( $courier_partner );
                    $row[] = stripslashes( $tracking_no );
                    $row[] = stripslashes( $name );
                    $row[] = stripslashes( $net_receivable );
                    $row[] = stripslashes( $amount );
                    $row[] = stripslashes( $bal );

                $content[] = $row;                

                }


            }

            $output = fopen('php://output', 'w');
            fputcsv($output, $title);
            
            foreach ($content as $con) {
                fputcsv($output, $con);
            }
        }



    }





}

?>
