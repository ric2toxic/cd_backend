<?php
  declare(strict_types=1);

  require_once('system.php');
  require_once( DIR_SYSTEM . 'library/operations/orders/order_accounts.php' );
  require_once( DIR_SYSTEM . 'library/securefiledownload.php' );
  require_once(DIR_SYSTEM . 'library/currency.php');

  class AccountsController extends SystemController {

    private $_payment_gateway_particulars  = array(
                                              'wsb_credit_nach' => 'NACH Debit',
                                              'citrus'          => 'Payment gateway (Citrus)',
                                              'razorpay'        => 'Payment gateway (Razorpay)',
                                              'paytm'           => 'Payment gateway (Paytm)',
                                              'fedex'           => 'COD',
                                              'gati_kwe'        => 'COD',
                                              'gati_ltd'        => 'COD',
                                              'neogrowth'       => 'COD',
                                              'dotzot'          => 'COD',
                                              'connect-india'   => 'COD',
                                              'bluedart'        => 'COD',
                                              'lazypay'         => 'COD',
                                              'coupon'          => 'Cash Discount (Coupon)',
                                              'cashback'        => 'Cash Discount (Cashback)',
                                              'cash'            => 'Cash Deposit',
                                              'others'          => 'Bank Transfer / UPI'
                                            );

    private $_date_stmt_available  = '2018-04-01';

    public function __construct($params) {

      parent::__construct($params);

      // CustomerOrderInfo
      $order_accounts = new OrderAccounts($this);

      $this->registry->set('order_accounts', $order_accounts);

      // Currency
      $this->registry->set('currency', new Currency($this->registry));

      // SecureFileDownload                                        
      $this->registry->set('securefiledownload', new SecureFileDownload($this->registry));
    }

    /**
     * @info: Private method to get customer level account details like : 
     *        total_no_of_orders, total_debits, total_credits and total_balance
     * @param: Basic filters Like: 
     *         $filter_data array format
     *         keys-->
     *            customer_id -  (Required) 
     *            filter_order_id(optional)      : string (Can be comma sperated string for multiple order_ids)
     *            filter_order_no(optional) 
     *            filter_payment_method(optional): array (multiple payment_methods)
     *            filter_invoice_no       (optional): Sub-Order Invoice No.
     *            filter_ordered_date_from(optional): Order Placed Date(date_added from oc_order table)
     *            filter_ordered_date_to  (optional): Order Placed Date(date_added from oc_order table)
     *            filter_invoice_date_from(optional): Sub-Order Invoice Date
     *            filter_invoice_date_to  (optional): Sub-Order Invoice Date
     * example $filter_data 
     *            $filter_data = array(
     *                            'customer_id'              => <customer_id>,
     *                            'filter_order_id'          => '<order_id1>,<order_id2>,<order_id3>',
     *                            'filter_order_no'          => '<order_no>',
     *                            'filter_payment_method'    => array('wsb_credit', 'cod'),
     *                            'filter_invoice_no'        => <invoice_no>,
     *                            'filter_ordered_date_from' => <ordered_date_from>,
     *                            'filter_ordered_date_to'   => <ordered_date_to>,
     *                            'filter_invoice_date_from' => <invoice_date_from>,
     *                            'filter_invoice_date_to'   => <invoice_date_to>
     *                           );
     * @return : Array keys -->
     *                'customer_id',
     *                'total_orders' ,
     *                'total_debits',
     *                'total_credits'
     *
     * @author: Nishu, June 2019
    */
    private function getCustomerLevelAccountSummary($filter_data){
      $data = array();
      
      //Check for required data keys
      if(!empty($filter_data['customer_id']) ){

          //Get all order_ids by appling all filters
          $order_ids = $this->order_accounts->getOrderIdsForOrderAccountCalculation($this->db, $filter_data);

          $group_by = 'o.customer_id';
          $selector = array(
                        'o.customer_id',
                        'COUNT(DISTINCT o.order_id ) AS total_order_count',
                      );
          
          //data for customer level summary for his/her account statemente based in all debits and credits
          $resp = $this->order_accounts->getTotalDebitsAndTotalCreditsByOrderIds($this->db, $order_ids, $selector, $group_by);
          
          if(!empty($resp)){
            $data = $resp[0];
            
            $total_debits    = (float)($data['total_debits']  ?? 0);
            $total_credits   = (float)($data['total_credits'] ?? 0);
            $data['balance'] = round((float)($total_credits - $total_debits), 2);
            $data['is_positive_bal'] = ($data['balance'] < 0) ? false : true;

            $data['balance']       = $this->currency->money_format($data['balance'], 'INR', 1);
            $data['total_debits']  = $this->currency->money_format($data['total_debits'], 'INR', 1);
            $data['total_credits'] = $this->currency->money_format($data['total_credits'], 'INR', 1);
          }
      }
      //Set and return Response of API
      return $data;
    }

    /**
     * @info: Public method to get Order level account details for single customer like : 
     *        order_no, ordered_date, total_debits, total_credits and total_balance (To display) 
     * @param: Basic filters Like: 
     *         $filter_data array format
     *         keys-->
     *            customer_id -  (Required)      : integer
     *            filter_order_id(optional)      : string (Can be comma sperated string for multiple order_ids)
     *            filter_order_no(optional) 
     *            filter_payment_method(optional): array (multiple payment_methods)
     *            filter_invoice_no       (optional): Sub-Order Invoice No.
     *            filter_ordered_date_from(optional): Order Placed Date(date_added from oc_order table)
     *            filter_ordered_date_to  (optional): Order Placed Date(date_added from oc_order table)
     *            filter_invoice_date_from(optional): Sub-Order Invoice Date
     *            filter_invoice_date_to  (optional): Sub-Order Invoice Date
     * example $filter_data 
     *            $filter_data = array(
     *                            'customer_id'              => <customer_id>,
     *                            'filter_order_id'          => '<order_id1>,<order_id2>,<order_id3>',
     *                            'filter_order_no'          => '<order_no>',
     *                            'filter_payment_method'    => array('wsb_credit', 'cod'),
     *                            'filter_invoice_no'        => <invoice_no>,
     *                            'filter_ordered_date_from' => <ordered_date_from>,
     *                            'filter_ordered_date_to'   => <ordered_date_to>,
     *                            'filter_invoice_date_from' => <invoice_date_from>,
     *                            'filter_invoice_date_to'   => <invoice_date_to>
     *                           );
     * @return : Array keys -->
     *                'customer_id',
     *                'order_id',
     *                'order_no',
     *                'ordered_date',
     *                'total_debits',
     *                'total_credits',
     *                'running_balance',
     *                'beyond_order_id'
     *
     * @author: Nishu, June 2019
    */   
    public function getOrderLevelAccountSummary() {
      
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        $data = array();
        $customer_summary = array();

        //Set request data to $data
        $filter_data = $this->request;

        //Check for required data keys
        if(!empty($filter_data['customer_id']) ){

          $customer_id = (int)$filter_data['customer_id'];

          if(in_array($customer_id, AC_SMT_BLOCK_CUSTOMERS)){
            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->data       = array();
            $this->data_packet->message    = "Unauthorized Access.";

            //Set and return Response of API
            return $this->data_packet;
          }

          if(empty($filter_data['beyond_order_id'])){
            //Customer Summary 
            $customer_summary = $this->getCustomerLevelAccountSummary($filter_data);
          }
          
          $filter_data['limit'] = 5;
          //Get all order_ids by appling all filters
          $order_ids = $this->order_accounts->getOrderIdsForOrderAccountCalculation($this->db, $filter_data);

          $group_by = 'o.order_id';
          $selector = array(
                        'o.order_id',
                        'o.order_no',
                        'DATE(o.date_added) AS ordered_date',
                        'o.customer_id'
                      );
          
          //data for customer level summary for his/her account statemente based in all debits and credits
          $resp = $this->order_accounts->getTotalDebitsAndTotalCreditsByOrderIds($this->db, $order_ids, $selector, $group_by);

          $date_stmt_available  = date('d M Y', strtotime($this->_date_stmt_available));

          if(!empty($resp)){
            
            $beyond_order_id = min(array_column($resp, 'order_id')) ?? 0;

            $balance         = 0;
            foreach ($resp as $key => $value) {
              $detail_page_link = $this->url->link('account/order&searching_value='.$value['order_no'], '', 'SSL');

              $value['detail_page_link'] = $detail_page_link;
              
              $total_debits      = (float)($value['total_debits']  ?? 0);
              $total_credits     = (float)($value['total_credits'] ?? 0);
              $balance           = (float)($total_credits - $total_debits);
              $value['balance']  = round($balance, 2);
              $value['txn_type'] = ($value['balance'] < 0) ? 'Dr' : 'Cr';
              $value['balance']  = $this->currency->money_format(ABS($value['balance']), 'INR', 1);
              $value['total_debits']  = $this->currency->money_format(ABS($value['total_debits']), 'INR', 1);
              $value['total_credits'] = $this->currency->money_format(ABS($value['total_credits']), 'INR', 1);    
              $value['balance']       = $value['balance'];
              $value['ordered_date']  = date('d M Y', strtotime($value['ordered_date']));
              $data['orders'][]  = $value;
              
            }
            $data['date_stmt_available'] = $date_stmt_available;
            $data['customer_summary']  = $customer_summary;

            $data['balance']           = round($balance, 2);
            $data['beyond_order_id']   = (int)$beyond_order_id;
            $data['limit']             = (int)($filter_data['limit'] ?? 5);
            
            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->data       = $data;
            $this->data_packet->message    = "Data Successfully Found.";
          }else{
            $this->data_packet->statusCode = 200;
            $data['orders']                = array();
            $data['customer_summary']      = array();
            $this->data_packet->data       = $data;
            $this->data_packet->message    = "Data not found.";
          }

          $payment_methods = array(
                                "cod"          => "Cash on Delivery",
                                "prepaid"      => "Prepaid",
                                "wsb_credit"   => "WholesaleBox Credit (Pay Later Scheme)",
                                "other_credit" => "Third party Credit"
                              );
          $this->data_packet->payment_methods = $payment_methods;
          $this->data_packet->important_note  = "For order(s) placed before ". $date_stmt_available. ", please contact our Accounts Deptt.";
        }else{
          //throw new Exception("Required Parameters Missing.");
          $this->data_packet->message    = "Required Parameters Missing.";
        }        
      }else{
        //throw new Exception("Required Parameters Missing.");
         $this->data_packet->message    = "Required Parameters Missing.";
      }

      //Set and return Response of API
      return $this->data_packet;
    }

    /**
     * @info: Public method to get Order Breakup account details like : 
     *        order_no and breakup details( Like: Ref, date, download_link, invoice_no, debit_amount, credit_amount, running_balance) 
     * @param: Basic filter key: 
     *            filter_order_id(Required)      : integer
     * example $filter_data 
     *            $filter_data = array(
     *                            'filter_order_id' => '<order_id>'
     *                           );
     * @return : Array keys -->
     *                'order_no',
     *                'particular',
     *                'date',
     *                'download_link',
     *                'doc_ref',
     *                'debit_amount',
     *                'credit_amount',
     *                'running_balance'
     *
     * @author: Nishu, June 2019
    */   
    public function getOrderLevelBreakupAccountDetails() {
      
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $data
        $filter_data = $this->request;

        //Check for required data keys
        if(!empty($filter_data['filter_order_id']) ){

          $customer_id = (int) ($this->customer->getId() );

          if(in_array($customer_id, AC_SMT_BLOCK_CUSTOMERS)){
            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->data       = array();
            $this->data_packet->message    = "Unauthorized Access.";

            //Set and return Response of API
            return $this->data_packet;
          }
          
          $data = array();
          
          //order_id from request
          $filter_order_id = (int)$filter_data['filter_order_id'];

          //data for customer level summary for his/her account statemente based in all debits and credits
          $resp = $this->order_accounts->getOrderLevelBreakupAccountDetails($this->db, $filter_order_id );

          if(!empty($resp)){

            $balance       = 0;
            $total_debit   = 0;
            $total_credit  = 0;
            $running_balance = '';

            foreach ($resp as $key => $value) {

              $value['download_link']   = '';
              if($value['type'] == 'order_payment'){
                $value['particular'] = $this->_payment_gateway_particulars[$value['particular']] ?? $this->_payment_gateway_particulars['others'];
              }else if( trim($value['type']) == 'credit_note'){
                $encode_file = array();
                $encode_file['order_id']       = $value['order_id'];
                $encode_file['credit_note_id'] = $value['ref_id'];
                $encode_file = base64_encode(serialize($encode_file));
                $file_url = $this->securefiledownload->getDownloadLink('buyer_credit_note', $encode_file, false);

                $value['download_link'] = $file_url;
              }else if( trim($value['type']) == 'suborder_invoice' && trim($value['doc_ref']) != 'Tentative Purchase'){

                $value['particular'] =  'Sale Inv. (PO: '. $value['ref_id']. ')';

                //BuyerInvoice Download Link
                $file_name = array();
                $file_name['order_id']    = $value['order_id'];
                $file_name['suborder_id'] = $value['ref_id'];
                $file_name = base64_encode(serialize($file_name));
                $file_url  = $this->securefiledownload->getDownloadLink('buyer_b2b_invoice', $file_name, false);

                $value['download_link'] = $file_url;
              }
              
              $debit_amount             = (float)($value['debit_amount']  ?? 0);
              $credit_amount            = (float)($value['credit_amount'] ?? 0);

              if(empty($debit_amount) ){
                $value['debit_amount'] = '';
              }else{
                $value['debit_amount'] = $this->currency->money_format(ABS($debit_amount), 'INR', 1); 
              }

              if(empty($credit_amount) ){
                $value['credit_amount'] = '';
              }else{
                $value['credit_amount'] = $this->currency->money_format(ABS($credit_amount), 'INR', 1); 
              }

              $total_debit             += $debit_amount;
              $total_credit            += $credit_amount;

              $balance                 += (float)($credit_amount - $debit_amount);
              $balance                  = round($balance, 2);

              $value['txn_type']        = ($balance < 0) ? 'Dr' : 'Cr';
              $running_balance          = $this->currency->money_format(ABS($balance), 'INR', 1); 
              $value['running_balance'] = $running_balance;

              if(!empty($value['date'])){
                $value['date']          = date('d M Y', strtotime($value['date']));
              }
              $data['breakup'][]        = $value;
              
            }

            $data['is_positive_bal'] = ($balance < 0) ? false : true;
            $data['balance']         = $this->currency->money_format($balance, 'INR', 1); ; 
            $data['total_credit']    = $this->currency->money_format(ABS($total_credit), 'INR', 1); 
            $data['total_debit']     = $this->currency->money_format(ABS($total_debit), 'INR', 1); 
            
            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->data       = $data;
            $this->data_packet->message    = "Data Successfully Found.";
          }else{
            $this->data_packet->statusCode = 200;
            $data['breakup']               = array();  
            $this->data_packet->data       = $data;
            $this->data_packet->message    = "Data not found.";
          }
        }else{
          //throw new Exception("Required Parameters Missing.");
           $this->data_packet->message    = "Required Parameters Missing.";
        }        
      }else{
        //throw new Exception("Required Parameters Missing.");
         $this->data_packet->message    = "Required Parameters Missing.";
      }

      //Set and return Response of API
      return $this->data_packet;
    }

   
    /**
     * @info: Public method to download csv, for customer's account statement with give filter data
     * @param: Basic filters Like: 
     *         $filter_data array format
     *         keys-->
     *            customer_id -  (Required)      : integer
     *            filter_order_id(optional)      : string (Can be comma sperated string for multiple order_ids)
     *            filter_order_no(optional)      
     *            filter_payment_method(optional): array (multiple payment_methods)
     *            filter_invoice_no       (optional): Sub-Order Invoice No.
     *            filter_ordered_date_from(optional): Order Placed Date(date_added from oc_order table)
     *            filter_ordered_date_to  (optional): Order Placed Date(date_added from oc_order table)
     *            filter_invoice_date_from(optional): Sub-Order Invoice Date
     *            filter_invoice_date_to  (optional): Sub-Order Invoice Date
     * example $filter_data 
     *            $filter_data = array(
     *                            'customer_id'              => <customer_id>,
     *                            'filter_order_id'          => '<order_id1>,<order_id2>,<order_id3>',
     *                            'filter_order_no'          => '<order_no>',
     *                            'filter_payment_method'    => array('wsb_credit', 'cod'),
     *                            'filter_invoice_no'        => <invoice_no>,
     *                            'filter_ordered_date_from' => <ordered_date_from>,
     *                            'filter_ordered_date_to'   => <ordered_date_to>,
     *                            'filter_invoice_date_from' => <invoice_date_from>,
     *                            'filter_invoice_date_to'   => <invoice_date_to>
     *                           );
     * @return : Array 
     *
     * @author: Nishu, June 2019
    */
    public function downloadCsvForCustomerAccountsStatement(){
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        $data = array();

        //Set request data to $data
        $filter_data = $this->request;
        
        //Check for required data keys
        if(!empty($filter_data['customer_id']) ){

          $customer_id = (int)$filter_data['customer_id'];

          if(in_array($customer_id, AC_SMT_BLOCK_CUSTOMERS)){
            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->data       = array();
            $this->data_packet->message    = "Unauthorized Access.";

            //Set and return Response of API
            return $this->data_packet;
          }

          //Get all order_ids by appling all filters
          $order_ids = $this->order_accounts->getOrderIdsForOrderAccountCalculation($this->db, $filter_data);

          //data for customer level summary for his/her account statemente based in all debits and credits
          $csvData = $this->order_accounts->getOrderLevelBreakupAccountDetails($this->db, $order_ids );

          //Download CSV file
          $data = $this->_getCsv($customer_id, $csvData);

          //Set data for Response 
          $this->data_packet->statusCode = 200;
          $this->data_packet->data       = $data;
          $this->data_packet->message    = "Data Successfully Found.";

        }else{
          //throw new Exception("Required Parameters Missing.");
           $this->data_packet->message    = "Required Parameters Missing.";
        }

      }else{
        //throw new Exception("Required Parameters Missing.");
         $this->data_packet->message    = "Required Parameters Missing.";
      }

      //Set and return Response of API
      return $this->data_packet;
    }


    private function _getCsv(int $customer_id, $csvData){
 
      $file_name = $customer_id.'_Account_Statement_'.date('dMY').'_'.date('his').'.csv';

      $folder_path = DIR_DOWNLOAD;
      if (!file_exists($folder_path)) {
          mkdir($folder_path, 0777, true);
      }

      $filepath = $folder_path.$file_name;
      $fp       = fopen($filepath, 'w');

      ob_clean();

      if(empty($csvData)){
        $data = array("No Data Found.");
        fputcsv($fp, $data);
      }else{
        
        /*
        * heading values
        */
        $heading = array(
                      "Sr. No.",
                      "Date",
                      "Particulars",
                      "Order No", 
                      "Document No", 
                      "Debit Amount", 
                      "Credit Amount",
                      "Running Balance",
                      "Dr/Cr");

        fputcsv($fp, $heading);

        $row_count = 1;
        $balance   = 0;
        foreach ($csvData as $key => $value) {

          if(trim($value['type']) == 'order_payment'){
            $value['particular'] = $this->_payment_gateway_particulars[$value['particular']] ?? $this->_payment_gateway_particulars['others'];
          }
          
          $debit_amount    = (float)($value['debit_amount']  ?? 0);
          $credit_amount   = (float)($value['credit_amount'] ?? 0);

          if(empty($debit_amount) ){
            $value['debit_amount'] = '';
          }

          if(empty($credit_amount) ){
            $value['credit_amount'] = '';
          }

          $balance        += (float)($credit_amount - $debit_amount);

          $value['balance']  = round((float)$balance, 2);
          $value['txn_type'] = ($value['balance'] < 0) ? 'Dr' : 'Cr';
          $value['balance']  = ABS($value['balance']);
          $date = '';
          if(!empty($value['date'])){
            $date = date('d M Y', strtotime($value['date']) );
          }

          $row = array(
                    $row_count,
                    $date,
                    $value['particular'],
                    $value['order_no'],
                    $value['doc_ref'],
                    $value['debit_amount'],
                    $value['credit_amount'],            
                    $value['balance'],
                    $value['txn_type'] 
                 );
          
          fputcsv($fp, $row);
          $row_count++;
        }
   
      }
      
      fclose($fp);

      createAndDownloadZip(array($filepath), $file_name, true, true, true);
      return array('file'=>$file_name, 'file_link'=>$filepath);
    }

   
  }//End of  Class
