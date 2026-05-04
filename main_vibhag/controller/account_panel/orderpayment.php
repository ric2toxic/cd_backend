<?php
class ControllerAccountPanelOrderpayment extends Controller{    
  private $error = array();

  public function index() {

    $this->load->model('account_panel/orderpayment');

    $this->config->set('config_limit_admin',10);
    
    $data = array();// Initializing the data array to be passed on to template f
    $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
    $this->document->setTitle('Order Pyment Report');

    //filtering
    $filter_customer_id  = $this->request->get['filter_customer_id'] ?? NULL;
    $filter_order_no     = $this->request->get['filter_order_no'] ?? NULL;
    $filter_verified     = $this->request->get['filter_verified'] ?? NULL;
    $filter_payment_type = $this->request->get['filter_payment_type'] ?? NULL;
    $filter_date_from    = $this->request->get['filter_date_from'] ?? NULL;
    $filter_date_to      = $this->request->get['filter_date_to'] ?? NULL;
    $filter_tracking_no  = $this->request->get['filter_tracking_no'] ?? NULL;
    $filter_total_from   = $this->request->get['filter_total_from'] ?? NULL;
    $filter_total_to     = $this->request->get['filter_total_to'] ?? NULL;
    $filter_order_status = $this->request->get['filter_order_status'] ?? NULL;
    $filter_cr_note      = $this->request->get['filter_cr_note'] ?? 1;
    $filter_payment_code = $this->request->get['filter_payment_code'] ?? NULL;
    
    $page  = $this->request->get['page'] ?? 1;
    $filter_page_limit = 0;
    if(
        empty($this->request->get['req_type']) 
        || $this->request->get['req_type'] != 'button-download'
    ){
      $filter_page_limit = $this->request->get['filter_page_limit'] ?? $this->config->get('config_limit_admin');
    }

    $url = '';
   
    if (isset($this->request->get['filter_customer_id'])) {
        $url .= '&filter_customer_id=' .$this->request->get['filter_customer_id'];
    }

    if (isset($this->request->get['filter_order_no'])) {
        $url .= '&filter_order_no=' .$this->request->get['filter_order_no'];
    }
    if (isset($this->request->get['filter_verified'])) {
        $url .= '&filter_verified=' .$this->request->get['filter_verified'];
    }
    if (isset($this->request->get['filter_payment_type'])) {
        $url .= '&filter_payment_type=' .$this->request->get['filter_payment_type'];
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
    if (isset($this->request->get['filter_order_status'])) {
        $url .= '&filter_order_status=' .$this->request->get['filter_order_status'];
    }
    if (isset($this->request->get['filter_cr_note'])) {
        $url .= '&filter_cr_note=' .$this->request->get['filter_cr_note'];
    }
    if (isset($this->request->get['filter_payment_code'])) {
        $url .= '&filter_payment_code=' . $this->request->get['filter_payment_code'];
    }

    $filter_data = array(
                    'filter_customer_id'  => $filter_customer_id,
                    'filter_order_no'     => $filter_order_no,
                    'filter_verified'     => $filter_verified,
                    'filter_payment_type' => $filter_payment_type,
                    'filter_date_from'    => $filter_date_from,
                    'filter_date_to'      => $filter_date_to,
                    'filter_tracking_no'  => $filter_tracking_no,
                    'filter_total_from'   => $filter_total_from,
                    'filter_total_to'     => $filter_total_to,
                    'filter_order_status' => $filter_order_status,
                    'filter_cr_note'      => $filter_cr_note,
                    'filter_payment_code' => $filter_payment_code,
                    'start'               => ($page - 1) * $this->config->get('config_limit_admin'),
                    'limit'               => $filter_page_limit
                );

    $this->load->model('sale/order');
    $data['payment_codes'] = $this->model_sale_order->getAllPaymentCodes();

    //Get all order status
    $this->load->model('localisation/order_status');
    $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();
    $order_statuses_qry = array_combine(
                           array_column($order_statuses_qry, 'order_status_id'), 
                           $order_statuses_qry);
    $data['order_statuses_qry'] = $order_statuses_qry;

    //Get Base result data for order_recceipt_report
    $result = $this->model_account_panel_orderpayment->getOrdersForOrderReceiptReport($filter_data);
    $order_data  = $result['data'] ?? array();
    $total_count = $result['total_count'] ?? 0;

    $order_ids         = array_column($order_data, 'order_id');
    $data['order_ids'] = $order_ids;
    $data['orders']    = $order_data;

    if(!empty($order_data )){

      //Get All suborder details for given order_ids with given field_list
      $field_list    = "courier_partner, tracking_no, total, order_status_id, invoice_date";
      $all_suborders = Suborder::getSuborderInfoByOrderIds($this->db, $order_ids, $field_list);

      //Set All Suborder's status name by looping over it
      foreach ($all_suborders as $order_id => $suborders) {
          $min_invoice_date = date('Y-m-d H:i:s');
          //Loop over order wise suborders
          foreach ($suborders as $suborder_id => $suborder) {
              if( !empty($suborder['invoice_date']) && strtotime($suborder['invoice_date']) < strtotime($min_invoice_date)){
                $min_invoice_date = $suborder['invoice_date'];
              }
              $order_status_id = $suborder['order_status_id'];
              $status_name     = $order_statuses_qry[$order_status_id]['name'] ?? '';
              $all_suborders[$order_id][$suborder_id]['status_name'] = $status_name; 
          }

          $data['orders'][$order_id]['min_invoice_date'] = $min_invoice_date;

      }
      $data['subOrders'] = $all_suborders;

      //Get All CNs for given order_ids
      //Get All suborder details for given order_ids with given field_list
      $field_list  = "CONCAT(credit_note_prefix, credit_note_no) AS cn_no, credit_note_amount, net_refundable ";
      $all_cn         = CreditNote::getCnInfoByOrderIds($this->db, $order_ids, $field_list);
      $data['all_cn'] = $all_cn;

      //Get all payment details for given order_ids
      $all_payments = OrderInfo::getPaymentDetailsByOrderIds($this->db, $order_ids, 0);
      $data['all_payments'] = $all_payments ?? array();
      $data['payments']     = $all_payments['payments'] ?? array();
      $data['coupon_cashback_discount'] = $all_payments['cashback_coupon'] ?? array();

      //Calculate Balance amount for given order ids
      $data['all_order_balance'] = $this->calculateOrderWiseBalance($data);
   
    }

  if(
        !empty($this->request->get['req_type']) 
        && $this->request->get['req_type'] == 'button-download'
    ){
      $this->downloadaCsv($data);
      exit();
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

    $pagination = new Pagination();

    $pagination->total = $total_count;
    $pagination->page = $page;
    $pagination->limit = $this->config->get('config_limit_admin');

    $pagination->url = $this->url->link('account_panel/orderpayment', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

    $data['pagination'] = $pagination->render();

    $data['results'] = sprintf($data['text_pagination'],
                                ($total_count) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
                                ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_count - $this->config->get('config_limit_admin'))) ? $total_count : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
                                    $total_count, ceil($total_count / $this->config->get('config_limit_admin')));

    $data['filter_customer_id']  = $filter_customer_id;
    $data['filter_order_no']     = $filter_order_no;
    $data['filter_payment_type'] = $filter_payment_type;
    $data['filter_verified']     = $filter_verified;
    $data['filter_date_from']    = $filter_date_from;
    $data['filter_date_to']      = $filter_date_to;
    $data['filter_tracking_no']  = $filter_tracking_no;
    $data['filter_total_from']   = $filter_total_from;
    $data['filter_total_to']     = $filter_total_to;
    $data['filter_order_status'] = $filter_order_status;
    $data['filter_cr_note']      = $filter_cr_note;
    $data['filter_payment_code'] = $filter_payment_code;

    $data['page_limit_array']    = array('30','60','100','200','500','1000');
    $data['filter_page_limit']   = $filter_page_limit;

    $data['token'] = $this->session->data['token'];
    $data['route'] = $this->request->get['route'];

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('account_panel/orderpayment.tpl', $data));
  }


    /**
     * Private method to calculate order wise balance amount with given data
     * @param: array $data, array $all_payments
     * @return: array 
     * @author: Nishu, Jan 2019
    */
    private function calculateOrderWiseBalance(array $data) : array{
        $result    = array();
        $order_ids = $data['order_ids'] ?? array();

        if(!empty($order_ids)){
          $filter_data = array(
                          "where"         => array(" o.order_id IN (". implode(',', $order_ids) . ") " ),
                          "suborder"      => array("where" => array(" order_id IN (".implode(',', $order_ids).") " ) ),
                          "order_payment" => array("where" => array(" order_id IN (".implode(',', $order_ids).") " ) ),
                          "credit_note"   => array("where" => array(" order_id IN (".implode(',', $order_ids).") " ) )
                        );

          //get Order Balance 
          $order_wise_bal = OrderAccounts::getOrderBalance($this->db, $filter_data);
        
          if( !empty($order_wise_bal)) {
            
            $result = array_combine(
                        array_column($order_wise_bal, 'order_id'), 
                        array_column($order_wise_bal, 'order_bal')
                      );
          }
        }
        
        return $result;
    }

    /**
     * @info: Public Method to download CSV file
     * @param: array $data
     * @author: Nishu, Jan 2019
    */
    public function downloadaCsv(array $data){
       
        $filename = "Order_Recceipt_Report.csv";
            
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        $output = fopen('php://output', 'w');

        if(!empty($data['orders'])){
            $content = array();

            $title = array(
                      "Order No.",
                      "Customer ID",
                      "Customer Name",
                      "Order Date",
                      "Order Value",
                      "suborder value",
                      "Courier",
                      "Tracking No.",
                      "Order Status",
                      "CN No.",
                      "CN Amt",
                      "Cashback/Coupon", 
                      "Payment Date",
                      "Amount",
                      "Transaction Ref.No.",
                      "Payment Company", 
                      "Bank",
                      "Balance"
                    );

            foreach ($data['orders'] as $order_id => $order ) {
            
               
                $order_no        = $order['order_no'] ;
                $customer_id     = $order['customer_id'] ;
                $customer_name   = $order['customer_name'] ;
                $order_date      = $order['order_date'] ;
                $total           = round($order['total'], 2) ;
                $cashback_coupon = $data['coupon_cashback_discount'][$order_id] ?? 0 ;
                $amount_received = $data['all_payments']['paid_amt'][$order_id] ?? 0.00 ;
                $amount_received -= $data['all_payments']['used_wsb_credit'][$order_id] ?? 0.00 ;
                $payment_code    = $order['payment_code'] ;
                $balance         = $data['all_order_balance'][$order_id] ?? 0;
                $balance         = round($balance, 2);

                //Total Suborders in that order
                $suborder_count = 1;
                $all_osub_ids = array();
                if(!empty($data['subOrders'][$order_id])){
                  $suborder_count = count($data['subOrders'][$order_id]);
                  $all_osub_ids = array_keys($data['subOrders'][$order_id]);
                }

                //Total CN(s) in that order
                $cn_count = 1;
                $all_cn_ids = array();
                if(!empty($data['all_cn'][$order_id])){
                  $cn_count = count($data['all_cn'][$order_id]);
                  $all_cn_ids = array_keys($data['all_cn'][$order_id]);
                }

                //Total Payment enteries in that order
                $payment_count = 1;
                if(!empty($data['payments'][$order_id])){
                  $payment_count = count($data['payments'][$order_id]);
                }

                $row_count = MAX($suborder_count, $cn_count, $payment_count);
                for($r = 1; $r<=$row_count; $r++) {
                  
                  $row = array();

                  $row[] = stripslashes( $order_no );
                  $row[] = stripslashes( $customer_id );
                  $row[] = stripslashes( $customer_name );
                  $row[] = stripslashes( $order_date );
                  if($r == 1){
                    $row[] = stripslashes( $total );   
                  }else{
                    $row[] = '';
                  }
                  
                  $suborder_id = $all_osub_ids[$r-1] ?? '';
                  if(!empty($suborder_id)){
                    $suborder = $data['subOrders'][$order_id][$suborder_id] ?? array();
                    $osub_val = $suborder['total'] ?? 0;
                    $row[] = Round($osub_val, 2); 
                    $courier_partner = $suborder['courier_partner'] ?? 0;
                    $row[] = trim($courier_partner); 
                    $tracking_no = $suborder['tracking_no'] ?? 0;
                    $row[] = trim($tracking_no); 
                    $status_name = $suborder['status_name'] ?? 0;
                    $row[] = trim($status_name); 
                  }else{
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                  }

                  $cn_id = $all_cn_ids[$r-1] ?? '';
                  if(!empty($cn_id)){
                    $cn = $data['all_cn'][$order_id][$cn_id] ?? array();
                    $cn_no = $cn['cn_no'] ?? 0;
                    $row[] = trim($cn_no); 
                    $credit_note_amount = $cn['credit_note_amount'] ?? 0;
                    $row[] = trim($credit_note_amount); 
                  }else{
                    $row[] = '';
                    $row[] = '';
                  }

                  if($r == 1){
                    $row[] = stripslashes( $cashback_coupon );
                  }else{
                    $row[] = '';
                  }
                  
                  $payment = $data['payments'][$order_id][$r-1] ?? array();
                  if(!empty($payment)){
                       
                    $payment_type = $payment['payment_gateway'] ?? '';
                    if($payment['payment_gateway'] == 'wsb_credit_nach'){
                      $payment_type = 'Others';
                    }else if($payment['amount'] < 0 ){
                      $payment_type = 'Refund';
                    }else if($payment['amount'] > 0 && strtotime($payment['txn_date_time']) <= strtotime($order['min_invoice_date']) ){
                      $payment_type = 'Advance';
                    }

                    $payment_date = $payment['payment_date'] ?? '';
                    $row[] = date("d-m-Y H:i:s", strtotime($payment_date)); 
                    $amount = $payment['amount'] ?? 0;
                    $row[] = round($amount, 2); 
                    $merchant_txn_id = $payment['merchant_txn_id'] ?? 'N/A';
                    $row[] = $merchant_txn_id; 
                    $payment_gateway = $payment['payment_gateway'] ?? '';
                    $row[] = $payment_gateway. '('.$payment_type .')';
                    $payment_mode = $payment['payment_mode'] ?? '';
                    $row[] = $payment_mode;
                  }else{
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                  }
                  if($r == 1){
                    $row[] = stripslashes( $balance ); 
                  }else{
                    $row[] = '';
                  }   
                  
                  $content[] = $row;
                }

            }

            fputcsv($output, $title);
            
            foreach ($content as $con) {
                fputcsv($output, $con);
            }
        }else{
            fputcsv($output, array('No Data'));
        }
        exit();
    }

}

?>
