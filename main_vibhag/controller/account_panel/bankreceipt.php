<?php
include_once DIR_SYSTEM . '../rabbitmq/task_directive_constants.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ControllerAccountPanelBankreceipt extends Controller{    
    private $error = array();

    /**
     * Method to show Account Panel -> Bank Receipt Menu
     * Used to show Bank Receipt Entries and its details
     * Author: Murtaza
     */

    public function index() {
        $user_id = $this->user->getId();

        $this->load->model('account_panel/bankreceipt');

        $cod_security_deposit_ledger_id = COD_SECURITY_LEDGER_ID;

        
        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
        //$this->document->setTitle($this->language->get('heading_title'));     
        $this->document->setTitle('Bank Receipt');


        //filtering
        if (isset($this->request->get['filter_ref'])) {
            $filter_ref = $this->request->get['filter_ref'];
        } else {
            $filter_ref = null;
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
        if (isset($this->request->get['filter_order'])) {
            $filter_order = $this->request->get['filter_order'];
        } else {
            $filter_order = "DESC";
        }

        if (isset($this->request->get['filter_amount_from'])) {
            $filter_amount_from = $this->request->get['filter_amount_from'];
        } else {
            $filter_amount_from = null;
        }

        if (isset($this->request->get['filter_amount_to'])) {
            $filter_amount_to = $this->request->get['filter_amount_to'];
        } else {
            $filter_amount_to = null;
        }

        if (isset($this->request->get['filter_bank'])) {
            $filter_bank = $this->request->get['filter_bank'];
        } else {
            $filter_bank = null;
        }

        if (isset($this->request->get['filter_inflow'])) {
            $filter_inflow = $this->request->get['filter_inflow'];
        } else {
            $filter_inflow = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            //$filter_confirm = "101";
            $filter_status = "0";
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        // if (isset($this->request->get['limit'])) {
        //     $limit = $this->request->get['limit'];
        // } else {
        //     $limit = $this->config->get('config_limit_admin');
        // }

        if (isset($this->request->get['filter_page_limit'])) {
            $filter_page_limit = $this->request->get['filter_page_limit'];
        } else {
            $filter_page_limit = $this->config->get('config_limit_admin');
        }



        $url = '';

        if (isset($this->request->get['filter_ref'])) {
            $url .= '&filter_ref=' .$this->request->get['filter_ref'];
        }
        if(!empty($this->request->get['filter_date_from'])){
            //$sql .= " AND invoice_date >= '".$this->db->escape($filters['filter_date_from'])."' ";
            $url .= '&filter_date_from=' .$this->request->get['filter_date_from'];
        }
        if(!empty($this->request->get['filter_date_to'])){
            //$sql .= " AND invoice_date <= '".$this->db->escape($filters['filter_date_to'])."' ";
            $url .= '&filter_date_to=' .$this->request->get['filter_date_to'];
        }
        if (isset($this->request->get['filter_order'])) {
            //$url .= $this->request->get['filter_order'];
            $url .= '&filter_order=' .$this->request->get['filter_order'];
        }
        if (isset($this->request->get['filter_amount_from'])) {
            $url .= '&filter_amount_from=' .$this->request->get['filter_amount_from'];
        }
        if (isset($this->request->get['filter_amount_to'])) {
            $url .= '&filter_amount_to=' .$this->request->get['filter_amount_to'];
        }
        if (isset($this->request->get['filter_bank'])) {
            $url .= '&filter_bank=' .$this->request->get['filter_bank'];
        }
        if (isset($this->request->get['filter_inflow'])) {
            $url .= '&filter_inflow=' .$this->request->get['filter_inflow'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' .$this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_page_limit'])) {
            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
        }

        
        $filter_data = array(
            'filter_ref'        => $filter_ref,
            'filter_order'      => $filter_order,
            'filter_date_from'  => $filter_date_from,
            'filter_date_to'    => $filter_date_to,
            'filter_amount_from'    => $filter_amount_from,
            'filter_amount_to'    => $filter_amount_to,
            'filter_bank'    => $filter_bank,
            'filter_inflow'    => $filter_inflow,
            'filter_status'    => $filter_status,
            // 'sort'           => $sort,
            // 'order'          => $order,
            'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
            //'limit'             => $this->config->get('config_limit_admin')
            'limit'             => $filter_page_limit
        );

        //$results = $this->model_account_panel_bankreceipt->getBankReceipts();
        $results = $this->model_account_panel_bankreceipt->getBankReceipts($filter_data);
        //echo "<pre>";print_r($results);die;
        $receiptCount = $this->model_account_panel_bankreceipt->getReceiptCount($filter_data);

        $data['meeshoLedgerID']      = 14723;
        $data['clubfactoryLedgerID'] = 15411;
        $data['receipts'] = array();
        foreach($results as $keys => $values){
            $data['receipts'][] = array(
            'receipt_id' => $values['receipt_id'],
            'dated' => $values['dated'],
            'ledger_id' => $values['ledger_id'],
            'ledger_name' => $values['ledger_name'],
            'amount' => $values['amount'],
            'mode' => $values['mode'],
            'reference' => $values['reference'],
            'no_of_order' => $values['no_of_order'],
            'narration' => $values['narration'],
            'confirm' => $values['confirm'],
            'ledger_id2' => $values['ledger_id2'],
            'group_id' => $values['group_id'],
            'csv_import2' => $this->url->link('account_panel/bankreceipt/addAjaxBankReceiptSub','token='.$this->session->data['token'].'&receipt_id='.$values['receipt_id'], 'SSL'),
            );
        }


        $ledgers = $this->model_account_panel_bankreceipt->getLedgers();
        $data['ledgers'] = $ledgers;

        $banks = $this->model_account_panel_bankreceipt->getBanks();
        $data['banks'] = $banks;

        $staffs = $this->model_account_panel_bankreceipt->getStaffs();
        $data['staffs'] = $staffs;

        $doneCount = $this->model_account_panel_bankreceipt->getDoneCount();
        $pendingCount = $this->model_account_panel_bankreceipt->getPendingCount();
        $data['doneCount'] = $doneCount;
        $data['pendingCount'] = $pendingCount;
        
        $data['membership_ledger_id'] = MEMBERSHIP_LEDGER_ID;

        $data['user_id'] = $user_id;

        // Autoloading the lanugage
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
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
        $pagination->total = $receiptCount;
        $pagination->page = $page;
        //$pagination->limit = $this->config->get('config_limit_admin');
        $pagination->limit = $filter_page_limit;
        //$pagination->limit = 2;
        $pagination->url = $this->url->link('account_panel/bankreceipt', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();
/*
        $data['results'] = sprintf($data['text_pagination'],
                                    ($receiptCount) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
                                    ((($page - 1) * $this->config->get('config_limit_admin')) > ($receiptCount - $this->config->get('config_limit_admin'))) ? $receiptCount : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
                                        $receiptCount, ceil($receiptCount / $this->config->get('config_limit_admin')));
*/
        $data['results'] = sprintf($data['text_pagination'],
                                    ($receiptCount) ? (($page - 1) * $filter_page_limit) + 1 : 0,
                                    ((($page - 1) * $filter_page_limit) > ($receiptCount - $filter_page_limit)) ? $receiptCount : ((($page - 1) * $filter_page_limit) + $filter_page_limit),
                                        $receiptCount, ceil($receiptCount / $filter_page_limit));

        $data['filter_ref'] = $filter_ref;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        $data['filter_order'] = $filter_order;
        $data['filter_amount_from'] = $filter_amount_from;
        $data['filter_amount_to'] = $filter_amount_to;
        $data['filter_bank'] = $filter_bank;
        $data['filter_inflow'] = $filter_inflow;
        $data['filter_status'] = $filter_status;
        $data['filter_page_limit'] = $filter_page_limit;
        $data['page_limit_array'] = array('30','60','100','200','500','1000');



        $data['csv_export'] = $this->url->link('account_panel/bankreceipt/exportcsv', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['csv_import'] = $this->url->link('account_panel/bankreceipt/importcsv', 'token=' . $this->session->data['token'] . $url, 'SSL');
        //$data['deleteAll'] = $this->url->link('account_panel/bankreceipt/deleteAll', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['action_csv_import2'] = $this->url->link('account_panel/bankreceipt/importcsv2','token='.$this->session->data['token'], 'SSL');
        $data['order_detail'] = $this->url->link('account_panel/bankreceipt/getOrderDetail','token='.$this->session->data['token'], 'SSL');
        $data['token'] = $this->session->data['token'];
        $data['route'] = $this->request->get['route'];
        
        $data['cod_security_deposit_ledger_id'] = $cod_security_deposit_ledger_id;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account_panel/bankreceipt.tpl', $data));
    }

    public function importcsv() {

        $response = array();
        $this->load->model('account_panel/bankreceipt');
            
            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName()['username'];
            $datedCM = date("Y-m-d H:i:s");
            $user_array = array(
                                    'user_id' => $user_id,
                                    'user_name' => $user_name,
                                    'date' => $datedCM
                                  );
            $row = 0;
            $bankGroupID = 8;


            if(empty($this->request->files['fileToUpload']['name'])){
                $this->response->redirect($this->url->link('account_panel/bankreceipt', "", 'SSL'));
                $response['status'] = 22;
                echo json_encode( $response );
                exit();   
            }
            // Sanitize the filename
            $filename = basename(html_entity_decode($this->request->files['fileToUpload']['name'], ENT_QUOTES, 'UTF-8'));

            // Validate the filename length
            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
                $this->error['warning']['filename']  = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array('csv',
                             'xls',
                             'xlsx');

            if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
                $this->error['warning']['filetype'] = $this->language->get('error_filetype');

            }
            // Allowed file mime types
            $allowed = array('application/vnd.ms-excel',
                             'text/plain',
                             'text/csv',
                             'text/tsv');

            if (!in_array($this->request->files['fileToUpload']['type'], $allowed)) {
                $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
                // $json['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['fileToUpload']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
            }

            // Return any upload error
            if ($this->request->files['fileToUpload']['error'] != UPLOAD_ERR_OK) {
                $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUpload']['error']);
            }

            if (!$this->error) {
                $file_handle = fopen($this->request->files['fileToUpload']['tmp_name'], 'r');
                while (!feof($file_handle) ) {
                    $line_of_text[] = fgetcsv($file_handle, 1024);
                }
                fclose($file_handle);
                
                ////////////condition of column for CSV and Bank Receipt CSV Only Import//////
                $getColumnsBankReceiptCSV = array_values($line_of_text)[0];
                //echo $countColumnsBankReceiptCSV = count($getColumnsBankReceiptCSV);die;

                if ($getColumnsBankReceiptCSV[0]=='Date' && $getColumnsBankReceiptCSV[1]=='Bank' && $getColumnsBankReceiptCSV[2]=='Amount' && $getColumnsBankReceiptCSV[3]=='Mode' && $getColumnsBankReceiptCSV[4]=='Reference' && $this->request->files['fileToUpload']['name'] =='bankreceipt.csv')
                {
                    //  // // // // // 5. Conditions Others- Start// // // // // //
                   // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                    $dated_arr = array_column($line_of_text,0);
                    unset($dated_arr[0]);
                    $checkDated_arr = 0;
                    foreach ( $dated_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkDated_arr = 1;
                            $response['status'] = 2;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                       
                        }
                    }
                    $row = 0;

                    // // // // // // 5(b). Check Bank Ledger Name not blank --- Start --- // // // // // // //
                    $ledgerName_arr = array_column($line_of_text,1);
                    unset($ledgerName_arr[0]);
                    $checkLedgerName_arr = 0;
                    foreach ( $ledgerName_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkLedgerName_arr = 1;
                            $response['status'] = 3;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();
                        }
                    }
                    $row = 0;
                    // // // // // // 5(c). Check Amount Is_Numeric and positive --- Start --- // // // // // // //
                    $amount_arr = array_column($line_of_text,2);
                    unset($amount_arr[0]);
                    $checkAmount_arr = 0;
                    foreach ( $amount_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkAmount_arr = 1;
                            $response['status'] = 4;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();
                        }
                        if($value <= 0) {
                            $checkAmount_arr = 1;
                            $response['status'] = 5;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();
                        }
                    }
                    $row = 0;
                    // // // // // // 5(d). Check Mode not blank --- Start --- // // // // // // //
                    $mod_arr = array_column($line_of_text,3);
                    unset($mod_arr[0]);
                    $checkMod_arr = 0;
                    foreach ( $mod_arr as $value ){
                        if( strlen($value) < 1 ) {
                            //$checkMod_arr = 1;
                            // $response['status'] = 6;
                            // echo json_encode( $response );
                            // exit();
                        }
                    }
                    // // // // // // 5(d). Check Ref No. not blank --- Start --- // // // // // // //
                    $ref_arr = array_column($line_of_text,4);
                    unset($ref_arr[0]);
                    $checkRef_arr = 0;
                    foreach ( $ref_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkRef_arr = 1;
                            $response['status'] = 7;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                         
                        }
                        if( strlen($value) > 1 ) {
                            $checkReceiptForRef = $this->model_account_panel_bankreceipt->getReceiptForRef($value);                            
                            if ( $checkReceiptForRef ) {
                                $checkRef_arr = 1;
                                $response['status'] =  77;
                                $response['row'] =  $row+1;
                                echo json_encode( $response );
                                exit();                                
                            }
                        }
                    }

                    // // // // // // 5(d1). Check Ref No. not blank on CSV (Frontend) --- Start --- // // // // // // //
                    if(count(array_unique($ref_arr))<count($ref_arr))
                    {
                        // Array has duplicates
                        $checkRef_arr = 1;
                        $response['status'] =  777;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                              
                    }
                    //  // // // // // 5. Conditions Others- End// // // // // //

                    if ($checkDated_arr !=1 && $checkLedgerName_arr !=1 && $checkAmount_arr !=1 && $checkRef_arr !=1){

                        $i = 0;
                        foreach($line_of_text as $cav_data){
                            if (empty($cav_data['0']) || $i ==0) {
                                $i++;
                                continue;
                            }
                            //echo "<pre>"; print_r($cav_data['0']); die;

                            $dated = $cav_data['0'];
                            //$bank        = $cav_data['1'];
                            $ledgerName        = $cav_data['1'];
                            $amount  = $cav_data['2'];
                            $mode  = $cav_data['3'];
                            $reference  = $cav_data['4'];

                            $datedx = "";
                            if( strlen( $dated ) > 1 ) {
                                $datedx = $dated;
                                $datedx = date("Y-m-d", strtotime($datedx));
                            }
                            
                            //$bankInLedgerTable = $this->model_account_panel_bankreceipt->getBankInLedgerTable($bank);
                            $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                            //if ( !$bankInLedgerTable ){
                            if ( $ledgerNameInLedgerTable == 0 ){
                                $ledger_id_new = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $bankGroupID, 0, $user_id, $datedCM, $user_array);
                                $sql = $this->model_account_panel_bankreceipt->InsertReceipt($datedx, $ledger_id_new, $amount, $mode, $reference, $user_id, $datedCM, $user_array);
                            }
                            else
                            {
                                //Old Ledgers Case
                                $ledger_id_old = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                                $sql = $this->model_account_panel_bankreceipt->InsertReceipt($datedx, $ledger_id_old, $amount, $mode, $reference, $user_id, $datedCM, $user_array);
                            }
                            $response['status'] = 53;
                        }                        
                    }
                }
                else
                {
                    $response['status'] = 8;
                    echo json_encode( $response );
                    exit();
                } 
            }
            echo json_encode( $response );           
    }

    public function getOrderDetail() {

        $this->load->model('account_panel/bankreceipt');
        $order_no = $this->request->post['order_no'];
        $amount = $this->request->post['amount'];
        $successful = 1;
        $bank_transfer = "bank_transfer";
        $cash = "cash";
        $upi = "upi";
        $txn_status = 'REFUND SUCCESS';//diff (it is found in rare case)

        $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetail($order_no, $amount, $successful, $bank_transfer, $cash, $upi, $txn_status);
        //echo "<pre>";print_r($orderDetails);die;
        //$orderDetails = $this->model_account_panel_bankreceipt->getOrderDetail($order_no, $successful, $bank_transfer, $cash);
        
        $response = array();
        if ( !$orderDetails ){
            $response['norecord'] = 0;
            $response['orderDetails'] =  '' ;
            echo json_encode($response) ; 
        }
        else
        {
            //echo "<pre>";print_r($orderDetails);die;
            $response['norecord'] = 1;
            $response['orderDetails'] =  $orderDetails ;
            echo json_encode($response) ;            
        }
    }

    /**
    * Public method to get customer details using customer id
    * @param: integer customer_id
    * @return: JSON 
    * @author: MSA August 2018
    */
    public function getCustomerDetail(): void
    {
        $this->load->model('account_panel/bankreceipt');
        
        $orderDetails = array();

        $customer_id = $this->request->get['customer_id'] ?? null;
        
        $orderDetails = $this->model_account_panel_bankreceipt
                             ->getCustomerDetails($customer_id);
       
        echo json_encode($orderDetails) ;
    }

    /**
    * Public method to set customer COD Security Deposit
    * @param: integer customer_id
    * @return: void 
    * @author: MSA August 2018
    */
    public function setCustomerCODSecurityDeposit(): void
    {
        $this->load->model('account_panel/bankreceipt');
        $this->load->model('accounts/salesreports');

        $customer_id    = $this->request->post['customer_id']?? 0 ;
        $ledger_id      = $this->request->post['ledger_id']  ?? 0 ;
        $group_id       = COD_SECURITY_GROUP_ID; 
        $receipt_id     = $this->request->post['receipt_id'] ?? 0 ;

       
        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'date' => $datedCM
                    );

        // get Customer ladger name for customer COD Security Deposit
        $ledger = $this->model_account_panel_bankreceipt->getCustomerLedgerName( $customer_id );
        if(empty($ledger)) {
          echo json_encode(array('error'=>'Error:: unable to generate ladger for selected customer.'));   
          exit;  
        }
        /*reset ledger name for COD security ledger */
        $ledger = $ledger . '-COD-Security';
        $customer_ledger_id = $this->model_account_panel_bankreceipt
                                                 ->isCodSecurityCustomerLedgerExists(
                                                        $ledger,
                                                        $customer_id, 
                                                        $group_id
                                                    );
            // Add new customer ledger for COD security deposit
            if ( !$customer_ledger_id ) {
                
                // save ladger details for customer COD Security Deposit
                $customer_ledger_id  = $this->model_account_panel_bankreceipt->saveLedger( $ledger, 
                                                                    $group_id, 
                                                                    $customer_id, 
                                                                    $user_id, 
                                                                    $datedCM, 
                                                                    $user_array,
                                                                    1
                                                                );

                }
                
            // check receipt entry in sub table entry    
                $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);

                if ( !$checkReceiptSubForReceiptID ) {

                    //get receipt details
                    $receipt_details = $this->model_account_panel_bankreceipt->getReceiptByID( $receipt_id );
                    
                     // save receipt sub ladger data for customer COD security deposit
                    $receipt_sub_id = $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( 
                                                                                       $receipt_id, 
                                                                                       $ledger_id, 
                                                                                       $group_id, 
                                                                                       $receipt_details['amount'], 
                                                                                       $user_id, 
                                                                                       $datedCM, 
                                                                                       $user_array
                                                                                   );
                     
                    // save receipt sub csv data for customer COD security deposit
                    $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSV( 
                                                                                       $receipt_id, 
                                                                                       $receipt_sub_id, 
                                                                                       'CODSecurityDeposit', 
                                                                                       $receipt_details['dated'], 
                                                                                       $customer_ledger_id, 
                                                                                       $receipt_details['amount'], 
                                                                                       0, 
                                                                                       $receipt_details['reference'], 
                                                                                       0, 
                                                                                       0
                                                                                    );
                    
                    //save data in master customer table
                    $this->saveCustomerCODSecurityBalance( $customer_id, 
                                                           $receipt_details['amount'], 
                                                           'oc_receipt_sub_csv', 
                                                           $receipt_sub_csv_id 
                                                        );
                    
                    echo json_encode(array('status'=>'COD security deposit entry has been saved Successfully'));   
                
                }else{

                    echo json_encode(array('error'=>'Sub Receipt COD security deposit entry already exists'));   
                }
                           
    }

    /**
    * Public method to set master customer table data for COD security balance
    * @param: integer customer_id
    * @param: float amount
    * @return: void 
    * @author: MSA August 2018
    */
    public function saveCustomerCODSecurityBalance( int $customer_id, 
                                                    float $amount, 
                                                    string $table_name,
                                                    int $table_id 
                                                ): void
    {
        $customerObj = new Customer($this->registry);
        $customerDtl = $customerObj->getCustomerById($customer_id);
        $this->model_account_panel_bankreceipt->setCustomerCODSecurityBalance( 
                                                                     $customerDtl['master_id'], 
                                                                     $amount
                                                                    ); 
        /*Save COD security receipt table data */
        $this->model_account_panel_bankreceipt
             ->setCustomerCODSecurityReceiptTableData(
                    $customerDtl['master_id'],
                    $table_name,
                    $table_id
                );   
    }

    //public function downloadSample(){
    public function exportcsv(){
        $file = DIR_DOWNLOAD.'bankreceipt.csv';
        // echo $file;die;
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-disposition: attachment; filename=' . basename($file));
        header('Expires: 0');
        header('Cache-Control: no-cache');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit();
    }

    public function editReceiptForLedger(){

        $this->load->model('account_panel/bankreceipt');

        $row_id = $_POST['row_id'];
        $input_value = $_POST['input_value'];

        $amount = $this->model_account_panel_bankreceipt->getAmountOfReceipt( $row_id);

        $result = $this->model_account_panel_bankreceipt->updateReceiptForLedger( $row_id, $input_value, $amount );
        //$optionname;
    }     

    public function editReceiptForConfirm(){
        $this->load->model('account_panel/bankreceipt');

        $row_id = $_POST['row_id'];
        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $row_id);                        
        
        $status = 1;
        if (isset($checkReceiptSubForReceiptID) && !empty($checkReceiptSubForReceiptID))
        {
            $result = $this->model_account_panel_bankreceipt->updateReceiptForConfirm($row_id);
            $status = 11;
            echo json_encode( $status );
        }
        else
        {
            $status = 1;
            echo json_encode( $status );  
        }
    }

    /**
     * Private function to check validity of file for input
     *    Validation like: file_type
     * @param: array of uploaded files 
     * @author: Nishu, May 2019
    */
    private function checkFileValidity($files){
        $response = array();
        if(empty($files['fileToUpload2']['name'])){
            $response['msgs'][] = array("row" => '', "name" => 'CSV', "msg" => "Please upload valid CSV." );
        }

        // Sanitize the filename
        $filename = basename(html_entity_decode($files['fileToUpload2']['name'], ENT_QUOTES, 'UTF-8'));

        // Validate the filename length
        if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
            $response['msgs'][] = array("row" => '', "name" => 'error_filename', "msg" => "Not Valid CSV Name." );
        }

        // Allowed file extension types
        $allowed = array('csv',
                         'xls',
                         'xlsx');

        if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
            $response['msgs'][] = array("row" => '', "name" => 'error_filetype', "msg" => "Not Valid CSV Type." );

        }
        // Allowed file mime types
        $allowed = array('application/vnd.ms-excel',
                         'text/plain',
                         'text/csv',
                         'text/tsv');

        if (!in_array($files['fileToUpload2']['type'], $allowed)) {
            $response['msgs'][] = array("row" => '', "name" => 'error_filetype', "msg" => "Not Valid CSV Name." );
        }

        // Check to see if any PHP files are trying to be uploaded
        $content = file_get_contents($files['fileToUpload2']['tmp_name']);

        if (preg_match('/\<\?php/i', $content)) {
            $response['msgs'][] = array("row" => '', "name" => 'nophpfile', "msg" => "Not Valid CSV type." );
        }

        // Return any upload error
        if ($files['fileToUpload2']['error'] != UPLOAD_ERR_OK) {
            $response['msgs'][] = array("row" => '', "name" => 'neterror', "msg" => "File upload error." );
        }
        if(!empty($response)){
            echo json_encode( $response );
            exit();
        }
        return;
    }

    /**
     * @info private method to check csv column format and csv file_name if given
     * @param: file
     * @param: Array of columns
     * @param: String file_name optional
     * @uathor: Nishu, May 2019
    */
    private function checkCsvFormatValidity($files, $columns, $file_name){
        $response = array();
        $file_handle = fopen($files['fileToUpload2']['tmp_name'], 'r');
        while (!feof($file_handle) ) {
            $line_of_text[] = fgetcsv($file_handle, 1024);
        }
        fclose($file_handle);

        ////////////4. Condition of column of CSV and Crdit Agency CSV Import Only//////
        $getColumnsCACSV = array_values($line_of_text)[0];

        //Check columns must be exactly matched
        if(!empty($columns)){
            foreach ($columns as $key => $column_name) {
                if($getColumnsCACSV[$key] != $column_name){
                    $response['msgs'][] = array("row" => '0', "name" => 'Invalid Column', "msg" => "Mistake in column Name, Required: <b>".$column_name."</b> but Given: <b>". $getColumnsCACSV[$key]."</b>");
                    echo json_encode( $response );
                    exit();
                }
            }
        }

        //File_name validation
        if(!empty($file_name) && $files['fileToUpload2']['name'] != $file_name){
            $response['msgs'][] = array("row" => '0', "name" => 'Invalid File Name', "msg" => "Incorrect File Name, Required: <b>".$file_name."</b> but Given: <b>". $files['fileToUpload2']['name'])."</b>";
            echo json_encode( $response );
            exit();
        }
        
        return $line_of_text;
    }

    /**
     * @info : Private method to data content validation
     * @param: array $data_content
     *             colmns sequence:
     *               -->'Date', 
     *               -->'Order No', 
     *               -->'Amount', 
     *               -->'Charges'
     * @author: Nishu, May 2019
    */
    private function dateValidateForCsv($data_content){
        $response = array();

        /////Date Validation
        $dated_arr = array_column($data_content, 0);
        unset($dated_arr[0]); //unset header comunn name key from array

        foreach ( $dated_arr as $key => $value ){
            $value = preg_replace( '/[^[:print:]]/', '', $value);
            if( strlen($value) < 1 ) {
                $response['msgs'][] = array("row" => $key, "name" => 'Date', "msg" => "Please Enter proper date in uploaded CSV = ". $value );
            }
        }

        //////////Order No validation
        $orderNo_arr = array_column($data_content, 1);
        unset($orderNo_arr[0]); //unset header comunn name key from array

        foreach ( $orderNo_arr as $key => $value ){
            $value = preg_replace( '/[^[:print:]]/', '', $value);
            if( !is_numeric(trim($value)) ) {
                $checkedError = 1;
                $response['msgs'][] = array("row" => $key, "name" => 'Order No', "msg" => "Please Enter Order No. in Numeric = ". $value );
            }
            if ($value != 666666)
            {
                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($value);

                if ( !$CustomerName ) {
                    $checkedError = 1;
                    $response['msgs'][] = array("row" => $key, "name" => 'Order No', "msg" => "Order No. not found = ". $value );
                }
            }
        }

        /////////Amount validation 
        $amount_arr = array_column($data_content, 3);
        unset($amount_arr[0]);

        $charges_arr = array_column($data_content,4);
        unset($charges_arr[0]);
       
        $co = count($amount_arr);
        for ($k=1; $k <= $co; $k++) {
            ///////amount validation
            if( !is_numeric(trim($amount_arr[$k])) ) {
                $response['msgs'][] = array("row" => $k, "name" => 'Amount', "msg" => "Please Enter Amount in Numeric = ". $amount_arr[$k] );
            }

            /////Charges validation
            if( !is_numeric(trim($charges_arr[$k])) ) {
                $response['msgs'][] = array("row" => $k, "name" => 'Charges', "msg" => "Please Enter Charges in Numeric = ". $charges_arr[$k] );
            }elseif(trim($charges_arr[$k]) < 0){
                $response['msgs'][] = array("row" => $k, "name" => 'Charges', "msg" => "Charges Value can not be negative, Given value = ". $value );
            }
        }
        
        //If error response is not empty
        if(!empty($response)){
            echo json_encode( $response );
            exit();
        }
        return;

    }

    /**
     * @info: Private method to upload .csv for ClubFactory and insert/update payment enteries into DB
     * @author: Nishu, May 2019
    */
    private function uploadSheetForClubFactory($clubfactoryLedgerID, $clubfactoryChargesLedgerID){
        $payment_gateway  = "club_factory";
        $roundOffLedgerID = 15;
        $sundryDebtorsGroupID = 10;

        $receipt_id      = $_REQUEST['receipt_id'];
        $groupid         = $this->request->post['groupidAA'];
        $amountOfReceipt = (float)($_REQUEST['amountAjax'] ?? 0);
        $dateOfReceipt   = $this->request->post['datedROAA'];//it is only used for round off
        $refOfReceipt    = $this->request->post['refAA'];// it is used in CA

        //To validate file existance, file_name, file_type etc.
        $this->checkFileValidity($this->request->files);

        //if no error in csv
        if (empty($this->error) ) {

            $columns   = array('Date', 'Order No', 'Ref', 'Amount', 'Charges');
            
            $file_name = '';
            $data_content = $this->checkCsvFormatValidity($this->request->files, $columns, $file_name);

            if( !empty($data_content) && strlen($refOfReceipt) > 0 ){

                //Data validation for uploaded .csv file
                $this->dateValidateForCsv($data_content);

                $user_id    = $this->user->getId();
                $user_name  = $this->user->getUserName()['username'];
                $datedCM    = date("Y-m-d H:i:s");
                $user_array = array(
                                    'user_id' => $user_id,
                                    'user_name' => $user_name,
                                    'date' => $datedCM
                                    );

                $date_arr    = array_column($data_content, 0);
                $orderNo_arr = array_column($data_content, 1);
                $ref_arr     = array_column($data_content, 2);
                $amount_arr  = array_column($data_content, 3);
                $charges_arr = array_column($data_content, 4);

                //Unset Header of csv file
                unset($date_arr[0]);
                unset($orderNo_arr[0]);
                unset($ref_arr[0]);
                unset($amount_arr[0]);
                unset($charges_arr[0]);

                $amount_total  = (float)array_sum($amount_arr);
                $charges_total = (float)array_sum($charges_arr);

                $AmtMinusCharges = (float)($amount_total - $charges_total);

                $checkAmountTotal = 0;
                $diff = round(($amountOfReceipt - $AmtMinusCharges), 2);
                $diff2 = abs($diff);
               
                if( abs($diff) > 0.1 ){
                    $response['msgs'][] = array("row" => '', "name" => 'Amount Diff', "msg" => "Total Amount Not Matched in CSV, Amount Diff  of Rs. = ". $diff );
                    echo json_encode( $response );
                    exit();
                }
                
                //Load Model
                $this->load->model('account_panel/bankreceipt');
                $this->load->model('accounts/salesreports');

                //Total number of orders to update into oc_receipt
                $countNoOfOrders = count($data_content) - 2;
                $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, $countNoOfOrders);

                ///////Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                $receipt_sub_id = $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $clubfactoryLedgerID, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
               
                //////6(b). Insertion/Updation in Receipt Sub CSV table)-- Start --- /////
                unset($data_content[0]); //Unset header keys
                
                foreach($data_content as $key =>$csv_row){
                    if(!is_array($csv_row)){
                        continue;
                    }
                    $date           = date("Y-m-d", strtotime($csv_row[0]));
                    $order_no       = preg_replace( '/[^[:print:]]/', '', $csv_row[1]);
                    $ref            = preg_replace( '/[^[:print:]]/', '', $csv_row[2]);
                    $amount         = (float)($csv_row[3] ?? 0);
                    $charges        = (float)$csv_row[4];

                    $CustomerName   = $this->model_account_panel_bankreceipt->getCustomerNameForBR($order_no);

                    $order_id    = (int)$CustomerName['order_id'];
                    $customer_id = (int)$CustomerName['customer_id'];

                    $txn_status = ($amount < 0) ? 'REFUND SUCCESS' : 'SUCCESS';

                    //Get ledger_id for customer_id
                    $ledger_id = 0;
                    $pmntID    = 0;

                    if($amount != 0 ){
                        //Get ledger name for customer_id
                        $ledgerName  = $this->model_accounts_salesreports->getCustomerLedger((int)$customer_id ) ;
                        $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);
                        if ( $ledgerNameInLedgerTable == 0 ){

                            $ledger_id = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $customer_id, $user_id, $datedCM, $user_array);

                            $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id, $customer_id);
                        }else{
                            $ledger_id = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                        }
                        //Add payment entry against order
                        $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, $txn_status, $payment_gateway, $amount, $date, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);
                    }

                    //Entry if amount > 0, against customer ledger and entry into oc_receipt_sub_csv
                    if ($amount > 0) {
                        $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $payment_gateway, $date, $ledger_id, $amount, $order_no, $ref, $pmntID, $order_id);

                        
                    }elseif ($amount < 0){ //Entry if amount < 0, against charges ledger and entry into oc_receipt_sub_chargesdr
                        
                        //if amount is negative means given amount is as charges against customer's ledger in oc_receipt_sub_chargesdr table
                        $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $date, $ledger_id, abs($amount), $order_no, $ref, $pmntID, $order_id);
                    }

                    if($charges > 0){// Entry for charges
                        $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, $receipt_sub_csv_id, $date, $clubfactoryChargesLedgerID, $charges, $order_no, $ref, $pmntID, $order_id);
                    }
                }///End of Foreach Loop
            }
        }
    }

    public function addAjaxBankReceiptSub() {      

        $response = array();
        $checkedError = 0;

        $this->load->model('account_panel/bankreceipt');                    
        ////1. Get All Values -- Start --- //////////////////
        $ledgerid = $this->request->post['ledgeridAA'];
        $groupid = $this->request->post['groupidAA'];
        $receipt_id = $_REQUEST['receipt_id'];
        
        $amountOfReceipt = (float)($_REQUEST['amountAjax'] ?? 0);

        $datedRO = $this->request->post['datedROAA'];//it is only used for round off
        $refAA = $this->request->post['refAA'];// it is used in CA
        
        $row = 0;

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );
        $round_off = 0;
        $roundOffLedgerID = 15;
        
        $cod_balance = 0;
        $codBalanceLedgerID = 7300;

        $credit_agency_balance = 0;
        $creditAgencyBalanceLedgerID = 7373;

        $fedexLedgerID    = 1;
        $gati_kweLedgerID = 2;
        $gati_ltdLedgerID = 3;

        $dotzotLedgerID        = 6130;
        $connect_indiaLedgerID = 6131;
        $bluedartLedgerID      = 6132;
        $truxCargoLedgerID     = 14506;
        $delhiveryLedgerID     = 14507;

        $citrusLedgerID   = 4;
        $mswipeLedgerID   = 18093;
        $meeshoLedgerID   = 14723;
        $paytmLedgerID    = 5;
        $razorpayLedgerID = 6;
        $clubfactoryLedgerID = 15411;

        $citrusChargesLedgerID = 7;
        $mswipeChargesLedgerID = 18094;
        $meeshoChargesLedgerID = 14724;
        $paytmChargesLedgerID = 8;
        $razorpayChargesLedgerID = 9;
        $clubfactoryChargesLedgerID = 15412;

        $neogrowthLedgerID = 5579;
        $neogrowthChargesLedgerID = 5580;

        $udaanLedgerID        = 14924;
        $udaanChargesLedgerID = 14925;

        $sundryDebtorsGroupID = 10;        
        $CODGroupID = 1;
        $paymentgatewayGroupID = 2;
        $creditAgencyGroupID = 21;
        $bankGroupID = 8;
        $suspenseGroupID = 5;
        $expensesGroupID = 12;
        $incomesGroupID = 13;
        $cashGroupID = 18;
        $sundryDebtorsInvGroupID = 19;
        $advancesGroupID = 15;
        $dutiesTaxesGroupID = 16;
        $securityDepositGroupID = 20;
        $fixedAssetsGroupID = 9;
        $investmentGroupID = 14;
        $bankingGroupID = 25;

        ///////To handle Club factory cases////////////////
        if ($ledgerid == $clubfactoryLedgerID)
        {
            $this->uploadSheetForClubFactory($clubfactoryLedgerID, $clubfactoryChargesLedgerID);

            $response['msgs'] = 200;

        }/////End of 'if ($ledgerid == $clubfactoryLedgerID)'

        /////////// COD CSV COD CSV COD CSV COD CSV COD CSV ///////////////////////////////
        elseif ($groupid == $CODGroupID){
            //////// 2. Checking of CSV Default Code - start /////////
            if(empty($this->request->files['fileToUpload2']['name'])){
                $checkedError = 1;
                $response['msgs'][] = array("row" => '', "name" => 'CSV', "msg" => "Please insert COD CSV" );
                echo json_encode( $response );
                exit();       
            }

            // Sanitize the filename
            $filename = basename(html_entity_decode($this->request->files['fileToUpload2']['name'], ENT_QUOTES, 'UTF-8'));

            // Validate the filename length
            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
                $this->error['warning']['filename']  = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array('csv',
                             'xls',
                             'xlsx');

            if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
                $this->error['warning']['filetype'] = $this->language->get('error_filetype');

            }
            // Allowed file mime types
            $allowed = array('application/vnd.ms-excel',
                             'text/plain',
                             'text/csv',
                             'text/tsv');

            if (!in_array($this->request->files['fileToUpload2']['type'], $allowed)) {
                $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
                // $json['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['fileToUpload2']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
            }

            // Return any upload error
            if ($this->request->files['fileToUpload2']['error'] != UPLOAD_ERR_OK) {
                $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUpload2']['error']);
            }
            ////////2. Checking of CSV Default Code - End /////////


            //if no error in csv
            if (!$this->error) {
                ///////3. Now CSV Read from here./////////
                $file_handle = fopen($this->request->files['fileToUpload2']['tmp_name'], 'r');
                while (!feof($file_handle) ) {
                    $line_of_text[] = fgetcsv($file_handle, 1024);
                }
                fclose($file_handle);

                ////////////4. Condition of column of CSV and COD CSV Import Only//////
                $getColumnsCODCSV = array_values($line_of_text)[0];

                if ($getColumnsCODCSV[0]=='Date' && $getColumnsCODCSV[1]=='Order No' && $getColumnsCODCSV[2]=='Ref' && $getColumnsCODCSV[3]=='Amount' && $this->request->files['fileToUpload2']['name'] =='COD_CSV.csv')
                {
                    //  // // // // // 5. Conditions Others- Start// // // // // //

                    //  // // // // // 5. Check ds_bulk entry// // // // // //
                    //if it is then no updating 
                    $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
                    
                    if ( $DS_Bulk_Entry )
                    {
                        $checkedError = 1;
                        $response['msgs'][] = array("row" => '', "name" => 'DS Bulk or Credit Agency Entry', "msg" => "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team" );
                        echo json_encode( $response );
                        exit();
                    }
                    // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                    $dated_arr = array_column($line_of_text,0);
                    unset($dated_arr[0]);
                    foreach ( $dated_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Date', "msg" => "Please Enter proper date in COD CSV = ". $value );
                        }
                    }

                    $row = 0;
                    // // // // // // 5(b). Check Order No Is_Numeric --- Start --- // // // // // // //
                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);
                    foreach ( $orderNo_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Order No', "msg" => "Please Enter Order No. in Numeric = ". $value );
                        }
                    }

                    $row = 0;
                    // // // // // // 5(b2). Check Order No in Oc_order Table --- Start --- // // // // // // //
                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);
                    foreach ( $orderNo_arr as $value ){
                        $row++;
                        if ($value != 666666)
                        {
                            //echo "yesss";die;
                            $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($value);

                            if ( !$CustomerName ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row+1, "name" => 'Order No', "msg" => "Order No. not found = ". $value );
                            }
                        }
                    }

                    $row = 0;
                    $row2 = 0;
                    // // // 5(d). Check Ref No. not blank and oc_suborder oc_order // // // // //
                    $payment_gateway = "";
                    if ($ledgerid == $fedexLedgerID)
                    {
                        $payment_gateway = "fedex";
                    }
                    else if ($ledgerid == $meeshoLedgerID)
                    {
                        $payment_gateway = "meesho";
                    }
                    else if ($ledgerid == $gati_kweLedgerID)
                    {
                        $payment_gateway = "gati_kwe";
                    }
                    else if ($ledgerid == $gati_ltdLedgerID)
                    {
                        $payment_gateway = "gati_ltd";
                    }                    
                    else if ($ledgerid == $dotzotLedgerID)
                    {
                        $payment_gateway = "dotzot";
                    }                    
                    else if ($ledgerid == $connect_indiaLedgerID)
                    {
                        $payment_gateway = "connect-india";
                    }                    
                    else if ($ledgerid == $bluedartLedgerID)
                    {
                        $payment_gateway = "bluedart";
                    }else if ($ledgerid == $truxCargoLedgerID)
                    {
                        $payment_gateway = "trux_cargo";
                    }else if ($ledgerid == $delhiveryLedgerID)
                    {
                        $payment_gateway = "delhivery";
                    }                    

                    $COD = "";
                    if ($ledgerid == $fedexLedgerID)
                    {
                        $COD = "FeDex";
                    }
                    else if ($ledgerid == $gati_kweLedgerID)
                    {
                        $COD = "Gati";
                    }
                    else if ($ledgerid == $gati_ltdLedgerID)
                    {
                        $COD = "Gati";
                    }                    
                    else if ($ledgerid == $dotzotLedgerID)
                    {
                        $COD = "DotZot";
                    }                    
                    else if ($ledgerid == $connect_indiaLedgerID)
                    {
                        $COD = "ConnectIndia";
                    }                    
                    else if ($ledgerid == $bluedartLedgerID)
                    {
                        $COD = "BlueDart";
                    }else if ($ledgerid == $truxCargoLedgerID)
                    {
                        $COD = "Delhivery";
                    }else if ($ledgerid == $delhiveryLedgerID)
                    {
                        $COD = "Delhivery";
                    }    

                    $ref_arr = array_column($line_of_text,2);
                    unset($ref_arr[0]);
                    foreach ( $ref_arr as $key => $value ){
                        $order_no = trim($orderNo_arr[$key] ?? '');
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Ref', "msg" => "Please Enter Ref.No. in COD CSV = ". $value );
                        }
                        $row2++;
                        if( strlen($value) > 1 ) {

                            if ($value != 666666)
                            {
                                $CustomerName2 = $this->model_account_panel_bankreceipt->getCustomerNameForCOD($value, $COD, $order_no);
                                if ( !$CustomerName2 ) {
                                    $checkedError = 1;
                                    $response['msgs'][] = array("row" => $row2+1, "name" => 'Ref', "msg" => "Tracking No. not found = ". $value );
                                }
                            }
                        }
                    }

                    $row = 0;
                    $row2 = 0;
                    // // // // // // 5(e). Check Amount Is_Numeric and positive --- Start --- // // // // // // //
                    $amount_arr = array_column($line_of_text,3);
                    unset($amount_arr[0]);
                   
                    $co = count($amount_arr);
                    for ($k=1; $k <= $co; $k++) {
                        $row++;
                        $row2++;

                        if( !is_numeric(trim($amount_arr[$k])) ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Amount', "msg" => "Please Enter Amount in Numeric = ". $amount_arr[$k] );
                        }

                        if ($ref_arr[$k] == 666666 )
                        {
                            if( $amount_arr[$k] == 0 ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row2+1, "name" => 'Amount', "msg" => "Please Enter Amount not equals to zero" );
                            }
                            else
                            {
                                $cod_balance = $amount_arr[$k];
                            }
                        }
                    }

                    //  // // // // // 5(f). Total Amt. of CSV (check vd amountOfReceipt Value) --- Start --- // // // // // //
                    $amountTotal_arr = array_column($line_of_text,3);
                    unset($amountTotal_arr[0]);
                    $amountTtl_arr = array_sum($amountTotal_arr);
                    $amountTtl_arr = (float)$amountTtl_arr;
                    $checkAmountTotal = 0;

                    $diff = (round($amountOfReceipt,2) - round($amountTtl_arr,2));
                    $diff1 = round($diff,2);
                    $diff2 = abs($diff1);
                    if ($diff2 != 0)
                    {
                        if ($diff2 > 0.5){
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => '', "name" => 'Total Amount', "msg" => "Total Amount Not Matched in COD CSV, Diff  of Rs. = ". $diff1 );
                        }
                        else
                        {
                            $round_off = $diff1;
                            //$amountOfReceipt = $amountOfReceipt + $round_off;
                        }
                    }

                    $countNoOfOrders = count($line_of_text) - 2;

                    //  // // // // // 5. Conditions Others- End// // // // // //

                    if ($checkedError !=1 && $ledgerid > 0){

                        ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, $countNoOfOrders);
                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                        if ( !$checkReceiptSubForReceiptID ) {
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }
                        
                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);
                        
                        if ($round_off > 0)
                        {   
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSV($receipt_id, $receipt_sub_id, $COD, $datedRO, $roundOffLedgerID, abs($round_off), '', 'round_off_cod', 0, 0);
                        }
                        else if ($round_off < 0)
                        {
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedRO, $roundOffLedgerID, abs($round_off), '', 'round_off_cod', 0, 0);
                        }
                        
                        if ($cod_balance > 0)
                        {   
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSV($receipt_id, $receipt_sub_id, $COD, $datedRO, $codBalanceLedgerID, abs($cod_balance), '', 'cod_balance', 0, 0);
                        }
                        else if ($cod_balance < 0)
                        {
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedRO, $codBalanceLedgerID, abs($cod_balance), '', 'cod_balance', 0, 0);
                        }
                        //////6(b). Insertion Updation in Receipt Sub CSV table)-- Start --- /////  
                        $i = 0;
                        foreach($line_of_text as $cav_data){
                            if (empty($cav_data['0']) || $i ==0) {
                                $i++;
                                continue;
                            }

                            $dated          = $cav_data['0'];
                            $order_no       = $cav_data['1'];
                            $ref            = $cav_data['2'];
                            $amount         = (float)$cav_data['3'];
                            $cod_or_pg      = 'COD';
                            
                            if ($order_no != 666666)
                            {
                                $datedx = "";
                                if( strlen( $dated ) > 1 ) {
                                    $datedx = $dated;
                                    $datedx = date("Y-m-d", strtotime($datedx));
                                }

                                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForCOD($ref, $COD, $order_no);

                                $this->load->model('accounts/salesreports');
                                $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                                $order_id = $CustomerName['order_id'];

                                $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                                if ( $ledgerNameInLedgerTable == 0 ){
                                    $ledger_id_new = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);
                                    
                                    $gst_number = '';
                                    if (empty($CustomerName['gst_number'])) {
                                        $gst_number = 'N/A';
                                    }
                                    else
                                    {
                                        $gst_number = $CustomerName['gst_number'];   
                                    }                                

                                    $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id_new, $CustomerName['customer_id']);

                                    $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, 'SUCCESS', 'cod', $amount, $datedx, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);

                                    if($amount < 0) {
                                       $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedx, $ledger_id_new, abs($amount), $order_no, $ref, $pmntID, $order_id); 
                                    } else {
                                        $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSV($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id_new, $amount, $order_no, $ref, $pmntID, $order_id);
                                    }
                                    
                                }
                                else
                                {
                                    $ledger_id_old = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);

                                    $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, 'SUCCESS', 'cod', $amount, $datedx, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);

                                    if($amount < 0) {
                                        $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedx, $ledger_id_old, abs($amount), $order_no, $ref, $pmntID, $order_id); 
                                    } else {
                                         $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSV($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id_old, $amount, $order_no, $ref, $pmntID, $order_id);
                                    }
                                   
                                }
                            }
                        }
                        $response['msgs'] =  51;
                    }
                    else
                    {
                        echo json_encode( $response );
                        exit();                           
                    }
                }
                else
                {   
                    $response['msgs'][] = array("row" => '', "name" => 'Invalid', "msg" => "Invalid COD CSV! Mistake in columns" );
                    echo json_encode( $response );
                    exit();   
                }
            }        
        }
        /////////// Payment Gateway CSV Payment Gateway CSV Payment Gateway CSV///////////////////////////////
        /////////// Payment Gateway CSV Payment Gateway CSV Payment Gateway CSV///////////////////////////////
        /////////// Payment Gateway CSV Payment Gateway CSV Payment Gateway CSV///////////////////////////////
        else if ($groupid == $paymentgatewayGroupID ){
            //////// 2. Checking of CSV Default Code - start /////////
            if(empty($this->request->files['fileToUpload2']['name'])){
                $checkedError = 1;
                $response['msgs'][] = array("row" => '', "name" => 'CSV', "msg" => "Please insert Payment gateway CSV" );
                echo json_encode( $response );
                exit();   
            }
            // Sanitize the filename
            $filename = basename(html_entity_decode($this->request->files['fileToUpload2']['name'], ENT_QUOTES, 'UTF-8'));

            // Validate the filename length
            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
                $this->error['warning']['filename']  = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array('csv',
                             'xls',
                             'xlsx');

            if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
                $this->error['warning']['filetype'] = $this->language->get('error_filetype');

            }
            // Allowed file mime types
            $allowed = array('application/vnd.ms-excel',
                             'text/plain',
                             'text/csv',
                             'text/tsv');

            if (!in_array($this->request->files['fileToUpload2']['type'], $allowed)) {
                $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
                // $json['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['fileToUpload2']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
            }

            // Return any upload error
            if ($this->request->files['fileToUpload2']['error'] != UPLOAD_ERR_OK) {
                $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUpload2']['error']);
            }
            ////////2. Checking of CSV Default Code - End /////////

            //if no error in csv
            if (!$this->error) {
                ///////3. Now CSV Read from here./////////
                $file_handle = fopen($this->request->files['fileToUpload2']['tmp_name'], 'r');
                while (!feof($file_handle) ) {
                    $line_of_text[] = fgetcsv($file_handle, 1024);
                }
                fclose($file_handle);

                ////////////4. Condition of column of CSV and Payment Gateway CSV Import Only//////
                $getColumnsPGCSV = array_values($line_of_text)[0];

                if ($getColumnsPGCSV[0]=='Date' && $getColumnsPGCSV[1]=='Order No' && $getColumnsPGCSV[2]=='Ref' && $getColumnsPGCSV[3]=='Amount' && $getColumnsPGCSV[4]=='Charges' && $this->request->files['fileToUpload2']['name'] =='Payment_Gateway_CSV.csv')
                {
                    //  // // // // // 5. Conditions Others- Start// // // // // //

                    //  // // // // // 5. Check ds_bulk entry// // // // // //
                    //if it is then no updating 
                    $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
                    
                    if ( $DS_Bulk_Entry )
                    {
                        $checkedError = 1;
                        $response['msgs'][] = array("row" => '', "name" => 'DS Bulk or Credit Agency Entry', "msg" => "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team" );
                        echo json_encode( $response );
                        exit();
                    }

                    // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                    $dated_arr = array_column($line_of_text,0);
                    unset($dated_arr[0]);
                    foreach ( $dated_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Date', "msg" => "Please Enter proper date in Payment Gateway CSV = ". $value );
                        }
                    }
                    $row = 0;

                    // // // // // // 5(b). Check Order No Is_Numeric // // // // //
                    
                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);
                    foreach ( $orderNo_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Order No', "msg" => "Please Enter Order No. in Numeric = ". $value );
                        }
                    }
                    $row = 0;

                    // // // 5(d). Check Ref No. not blank and oc_order_payment oc_order // // // // //
                    $payment_gateway = "";
                    $ledgerForCharges = "";
                    if ($ledgerid == $citrusLedgerID)
                    {
                        $payment_gateway = "citrus";
                        $ledgerForCharges = $citrusChargesLedgerID;
                    }
                    elseif ($ledgerid == $paytmLedgerID)
                    {
                        $payment_gateway = "paytm";
                        $ledgerForCharges = $paytmChargesLedgerID;
                    }
                    elseif ($ledgerid == $razorpayLedgerID)
                    {
                        $payment_gateway = "razorpay";
                        $ledgerForCharges = $razorpayChargesLedgerID;
                    }if ($ledgerid == $mswipeLedgerID)
                    {
                        $payment_gateway = "mswipe";
                        $ledgerForCharges = $mswipeChargesLedgerID;
                    }
                    
                    $ref_arr = array_column($line_of_text,2);
                    unset($ref_arr[0]);
                    foreach ( $ref_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Ref', "msg" => "Please Enter Ref.No. in Payment Gateway CSV = ". $value );
                        }
                    }

                    $row = 0;
                    $row2 = 0;
                    
                    // // // // // // 5(e). Check Amount Is_Numeric and positive // // // // //
                    $cod_or_pg = 'PG';
                    $amount_arr = array_column($line_of_text,3);
                    unset($amount_arr[0]);
                    foreach ( $amount_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Amount', "msg" => "Please Enter Amount in Numeric = ". $value );
                        }
                        $row2++;
                        if( $value == 0 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row2+1, "name" => 'Amount', "msg" => "Please Enter Amount not equals to zero" );
                        }                        
                        if( $value < 0 ) {
                            $cod_or_pg = 'PGBR';
                        }                         
                    }

                    $row = 0;
                    $row2 = 0;

                    // // // // // // 5(f). Check Charges Is_Numeric and positive // // // // //
                    $charges_arr = array_column($line_of_text,4);
                    unset($charges_arr[0]);
                    foreach ( $charges_arr as $value ){
                        $row++;
                        $row2++;
                        if (strlen($value) > 0)
                        {
                            if( !is_numeric(trim($value)) ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row+1, "name" => 'Charges', "msg" => "Please Enter Charges in Numeric = ". $value );
                            }
                            if( $value <= 0 ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row2+1, "name" => 'Charges', "msg" => "Please Enter Positive Charges Value in Payment Gateway CSV = ". $value );
                            }
                        }
                    }
                    $row = 0;

                    // // // 5(gg). if value in amount > 0 then charges should be there // // // // //
                    
                    $amount_arrx = array_column($line_of_text,3);
                    unset($amount_arrx[0]);
                    //echo "<pre>";print_r($amount_arrx);//die;
                    $charges_arrx = array_column($line_of_text,4);
                    unset($charges_arrx[0]); 
                    //echo "<pre>";print_r($charges_arrx);die;
                    $co = count($amount_arrx);
                    for ($k=1; $k <= $co; $k++) {
                        $row++;
                        if ($amount_arrx[$k] > 0)
                        {
                            if ($charges_arrx[$k] > 0)
                            {
                                //echo "<br>";
                                //echo $amount_arrx[$k];
                                //echo "yesok2";die;
                            }
                            else
                            {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row+1, "name" => 'Total', "msg" => "Please fill value in Charges = ". $charges_arrx[$k] );
                            }
                        }
                    }
                    $row = 0;

                    /*
                    // // // // // // 5(b). Checking order_no and ref no differently // // // // //
                    $amount_arrx2 = array_column($line_of_text,3);
                    unset($amount_arrx2[0]);
                    
                    $orderNo_arr2 = array_column($line_of_text,1);
                    unset($orderNo_arr2[0]); 
                    $checkOrderNo_arr2 = 0;
                    
                    $ref_arr2 = array_column($line_of_text,2);
                    unset($ref_arr2[0]); 
                    $checkRef_arr2 = 0;
                    
                    $co = count($amount_arrx2);
                    for ($k=1; $k <= $co; $k++) {

                        $row++;
                        if ((float)($amount_arrx2[$k]) < 0)
                        {
                            if( strlen($orderNo_arr2[$k]) > 1 ) {
                                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($orderNo_arr2[$k]);

                                if ( !$CustomerName ) {
                                    $checkOrderNo_arr2 = 1;
                                    $response['status'] =  255;
                                    $response['row'] =  $row+1;
                                    echo json_encode( $response );
                                    exit();                                
                                }
                            }
                        }
                        else if ((float)($amount_arrx2[$k]) > 0)
                        {
                            if( strlen($ref_arr2[$k]) > 1 ) {
                                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForPG($ref_arr2[$k], $payment_gateway, 1);

                                if ( !$CustomerName ) {
                                    $checkRef_arr2 = 1;
                                    $response['status'] =  25;
                                    $response['row'] =  $row+1;
                                    echo json_encode( $response );
                                    exit();                                  
                                }
                            }                            
                        }
                    }
                    $row = 0;
                    */

                    // // // 5(f). Total Amt. minus charges of CSV (check vd amountOfReceipt Value) // // // // //
                    $amountTotal_arr = array_column($line_of_text,3);
                    unset($amountTotal_arr[0]);
                    $amountTtl_arr = array_sum($amountTotal_arr);
                    $amountTtl_arr = (float)$amountTtl_arr;

                    $chargesTotal_arr = array_column($line_of_text,4);
                    unset($chargesTotal_arr[0]);
                    $chargesTtl_arr = array_sum($chargesTotal_arr);
                    $chargesTtl_arr = (float)$chargesTtl_arr;

                    $AmtMinusCharges = $amountTtl_arr - $chargesTtl_arr;
                    $AmtMinusCharges = (float)$AmtMinusCharges;

                    $checkAmountTotal = 0;
                    $diff = (round($amountOfReceipt,2) - round($AmtMinusCharges,2));
                    $diff1 = round($diff,2);
                    $diff2 = abs($diff1);
                    if ($diff2 != 0)
                    {
                        if ($diff2 > 0.5){
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => '', "name" => 'Diff', "msg" => "Total Amount Not Matched in Payment Gateway CSV, Diff. of Rs. = ". $diff1 );
                        }
                        else
                        {
                            $round_off = $diff1;
                            //$amountOfReceipt = $amountOfReceipt + $round_off;
                        }
                    }

                    // // // // // // 5(b). Checking order_no and ref no differently // // // // //
                    $amount_arrx2 = array_column($line_of_text,3);
                    unset($amount_arrx2[0]);
                    
                    $orderNo_arr2 = array_column($line_of_text,1);
                    unset($orderNo_arr2[0]); 
                    
                    $ref_arr2 = array_column($line_of_text,2);
                    unset($ref_arr2[0]); 
                    
                    $pmntIDQRDArray = array();

                    $co = count($amount_arrx2);
                    
                    if ($checkedError ==1)
                    {
                        echo json_encode( $response );  //exit bcos of QR code
                        exit();
                    }
                    else
                    {
                        for ($k=1; $k <= $co; $k++) {

                            $row++;
                            if ((float)($amount_arrx2[$k]) < 0)
                            {
                                if( strlen($orderNo_arr2[$k]) > 1 ) {

                                    $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($orderNo_arr2[$k]);

                                    if ( !$CustomerName ) {
                                        $checkedError = 1;
                                        $response['msgs'][] = array("row" => $row+1, "name" => 'Order No', "msg" => "Order No. not found = ". $orderNo_arr2[$k] );
                                        echo json_encode( $response );  //exit bcos of QR code
                                        exit();
                                    }
                                }
                            }
                            else if ((float)($amount_arrx2[$k]) > 0)
                            {
                                if( strlen($ref_arr2[$k]) > 1 ) {
                                    $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForPG($ref_arr2[$k], $payment_gateway, 1);

                                    if ( !$CustomerName ) {

                                        $getOrdersForQRCode = $this->model_account_panel_bankreceipt->getOrderDetailForQRCode($orderNo_arr2[$k], $amount_arrx2[$k], 1);//Check No Linking

                                        if ( !$getOrdersForQRCode ) {
                                            $checkedError = 1;
                                            $response['msgs'][] = array("row" => $row+1, "name" => 'Order and Ref', "msg" => "Order No. and Ref. No. not found = ". $orderNo_arr2[$k].", and ". $ref_arr2[$k] );
                                            echo json_encode( $response );  //exit bcos of QR code
                                            exit();
                                        }
                                        else
                                        {
                                            //Array push for QR Code Pop up
                                            $pmntIDQRDArray[] = $getOrdersForQRCode;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // // // // // // 5(b). Checking QR Code // // // // //

                    $countQRCodeEntries =  count($pmntIDQRDArray);

                    $checkPmntIDQRD_arr = 0;
                    if(!empty($pmntIDQRDArray)){

                        $checkPmntIDQRD_arr = 1;
                        
                        // $response['status'] =  256;
                        // $response['row'] =  $row+1;
                        // $response['pmntIDQRDArray'] =  $pmntIDQRDArray ;

                        $response['msgs'] =  256;
                        $response['pmntIDQRDArray'] =  $pmntIDQRDArray ;


                        $QRCodeDataArray = array(
                            'ledgerid'                => $ledgerid,
                            'groupid'                 => $groupid,
                            'receipt_id'              => $receipt_id,
                            'amountOfReceipt'         => $amountOfReceipt,
                            'datedRO'                 => $datedRO,
                            'user_id'                 => $user_id,
                            'user_name'               => $user_name,
                            'datedCM'                 => $datedCM,
                            'user_array'              => $user_array,
                            'round_off'               => $round_off,
                            'roundOffLedgerID'        => $roundOffLedgerID,

                            'fedexLedgerID'           => $fedexLedgerID,
                            'meeshoLedgerID'          => $meeshoLedgerID,
                            'gati_kweLedgerID'        => $gati_kweLedgerID,
                            'gati_ltdLedgerID'        => $gati_ltdLedgerID,

                            'citrusLedgerID'          => $citrusLedgerID,
                            'mswipeLedgerID'          => $mswipeLedgerID,
                            'paytmLedgerID'           => $paytmLedgerID,
                            'razorpayLedgerID'        => $razorpayLedgerID,

                            'clubfactoryLedgerID'        => $clubfactoryLedgerID,
                            'clubfactoryChargesLedgerID' => $clubfactoryChargesLedgerID,

                            'citrusChargesLedgerID'   => $citrusChargesLedgerID,
                            'mswipeChargesLedgerID'   => $mswipeChargesLedgerID,
                            'paytmChargesLedgerID'    => $paytmChargesLedgerID,
                            'razorpayChargesLedgerID' => $razorpayChargesLedgerID,
                            'clubfactoryChargesLedgerID' => $clubfactoryChargesLedgerID, 

                            'sundryDebtorsGroupID'    => $sundryDebtorsGroupID,
                            'CODGroupID'              => $CODGroupID,
                            'paymentgatewayGroupID'   => $paymentgatewayGroupID,
                            'bankGroupID'             => $bankGroupID,
                            'suspenseGroupID'         => $suspenseGroupID,
                            'line_of_text'            => $line_of_text,
                            'countQRCodeEntries'      => $countQRCodeEntries
                        );

                        // $response['QRCodeDataArray'] =  base64_encode(serialize($QRCodeDataArray));

                        // echo json_encode( $response );
                        // exit();

                        $response['QRCodeDataArray'] =  base64_encode(serialize($QRCodeDataArray));

                        echo json_encode( $response );
                        exit();
                    }


                    $countNoOfOrders = count($line_of_text) - 2;

                    //  // // // // // 5. Conditions Others- End// // // // // //

                    if ($checkedError !=1 && $ledgerid > 0){

                        ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, $countNoOfOrders);
                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                        if ( !$checkReceiptSubForReceiptID ) {
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }

                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

                        if ($round_off > 0)
                        {
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $payment_gateway, $datedRO, $roundOffLedgerID, abs($round_off), '', 'round_off_pg', 0, 0);
                        }
                        else if ($round_off < 0)
                        {
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedRO, $roundOffLedgerID, abs($round_off), '', 'round_off_pg', 0, 0);
                        }

                        //////6(b). Insertion Updation in Receipt Sub CSV table)-- Start --- /////  
                        $i = 0;
                        foreach($line_of_text as $cav_data){
                            if (empty($cav_data['0']) || $i ==0) {
                                $i++;
                                continue;
                            }

                            $dated              = $cav_data['0'];
                            $order_no           = $cav_data['1'];
                            //$ledgerName       = $cav_data['2'];
                            $ref                = $cav_data['2'];//merchant_txn_id
                            $amount             = $cav_data['3'];
                            $charges            = $cav_data['4'];

                            $datedx = "";
                            if( strlen( $dated ) > 1 ) {
                                $datedx = $dated;
                                $datedx = date("Y-m-d", strtotime($datedx));
                            }

                            $CustomerName = "";
                            $ledgerName = "";
                            $order_id = 0;
                            if ((float)($amount) < 0)
                            {
                                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($order_no);

                                $this->load->model('accounts/salesreports');
                                $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                                /*
                                $ledgerName = $CustomerName['customer_id'];
                                $ledgerName .= (!empty(trim($CustomerName['firstname'])) ? '_' . trim($CustomerName['firstname']) : '');
                                $ledgerName .= (!empty(trim($CustomerName['lastname'])) ? '_' . trim($CustomerName['lastname']) : '');
                                $ledgerName .= (!empty(trim($CustomerName['payment_company'])) ? '_' . trim($CustomerName['payment_company']) : '');
                                */
                                $order_id = $CustomerName['order_id'];
                            }
                            else if ((float)($amount) > 0)
                            {
                                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForPG($ref, $payment_gateway, 1);
                                //echo "<pre>";print_r($CustomerName);//die;

                                $this->load->model('accounts/salesreports');
                                $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                                /*
                                $ledgerName = $CustomerName['customer_id'];
                                $ledgerName .= (!empty(trim($CustomerName['firstname'])) ? '_' . trim($CustomerName['firstname']) : '');
                                $ledgerName .= (!empty(trim($CustomerName['lastname'])) ? '_' . trim($CustomerName['lastname']) : '');
                                $ledgerName .= (!empty(trim($CustomerName['payment_company'])) ? '_' . trim($CustomerName['payment_company']) : '');
                                */
                                $order_payment_id = $CustomerName['payment_id'];
                                $order_id = $CustomerName['order_id'];
                            }

                            $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                            $ledger_id = 0;
                            if ( $ledgerNameInLedgerTable == 0 ){

                                $ledger_id = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);

                                //ask from mdhrSir
                                $gst_number = '';
                                if (empty($CustomerName['gst_number'])) {
                                    $gst_number = 'N/A';
                                }
                                else
                                {
                                    $gst_number = $CustomerName['gst_number'];   
                                }      

                                $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id, $CustomerName['customer_id']);
                            }
                            else
                            {
                                $ledger_id = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                            }

                            if ($amount > 0)
                            {
                                //$cod_or_pg = 'PG';
                                $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id, $amount, $order_no, $ref, $order_payment_id, $order_id);
                                
                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, $receipt_sub_csv_id, $datedx, $ledgerForCharges, $charges, $order_no, $ref, $order_payment_id, $order_id);

                                $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $order_payment_id, $order_id);//08112017
                            }
                            else
                            {
                                $this->load->model('account_panel/bankpayment');

                                $pmntID = $this->model_account_panel_bankpayment->getRefundFromOcOrderPayment($order_id, $ref, -abs($amount), $payment_gateway);

                                if(!$pmntID) {
                                    $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', '', $amount, $datedx, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);
                                } else {
                                    $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $pmntID, $order_id);
                                }

                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedx, $ledger_id, abs($amount), $order_no, $ref, $pmntID, $order_id);
                            }
                            //$response['status'] =  52;
                        }
                        
                        $response['msgs'] =  52;
                    }
                    else
                    {
                        echo json_encode( $response );
                        exit();                           
                    }
                }
                else
                {
                    // $response['status'] =  21;
                    // echo json_encode( $response );
                    // exit();
                    $response['msgs'][] = array("row" => '', "name" => 'Invalid', "msg" => "Invalid Payment Gateway CSV! Mistake in columns" );
                    echo json_encode( $response );
                    exit();   
                }
            }
        }///

        //Meesho_order block start
        else if ($groupid == $sundryDebtorsGroupID && $ledgerid == $meeshoLedgerID){
            //////// 2. Validation for CSV  /////////
            $this->error = $this->fileUploadValidationForBankReceipt();

            //if no error in csv
            if (!$this->error) {
                ///////3. Now CSV Read from here./////////
                $file_handle = fopen($this->request->files['fileToUpload2']['tmp_name'], 'r');
                while (!feof($file_handle) ) {
                    $line_of_text[] = fgetcsv($file_handle, 1024);
                }
                fclose($file_handle);

                ////////////4. Condition of column of CSV and Payment Gateway CSV Import Only//////
                $getColumnsPGCSV = array_values($line_of_text)[0];

                if ($getColumnsPGCSV[0]=='Date' && $getColumnsPGCSV[1]=='Order No' && $getColumnsPGCSV[2]=='Ref' && $getColumnsPGCSV[3]=='Amount' && $getColumnsPGCSV[4]=='Charges' && $this->request->files['fileToUpload2']['name'] =='Payment_Gateway_CSV.csv')
                {
                    
                    //If it is then no updating 
                    $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
                    if ( $DS_Bulk_Entry )
                    {
                        $checkedError = 1;
                        $response['msgs'][] = array("row" => '', "name" => 'DS Bulk or Credit Agency Entry', "msg" => "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team" );
                        echo json_encode( $response );
                        exit();
                    }

                    // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                    $dated_arr = array_column($line_of_text,0);
                    unset($dated_arr[0]);
                    foreach ( $dated_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Date', "msg" => "Please Enter proper date in Meesho CSV = ". $value );
                        }
                    }
                    $row = 0;

                    // // // // // // 5(b). Check Order No Is_Numeric // // // // //
                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);
                    foreach ( $orderNo_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Order No', "msg" => "Please Enter Order No. in Numeric = ". $value );
                        }
                    }
                    $row = 0;

                    // // // 5(d). Check Ref No. not blank and oc_order_payment oc_order // // // // //
                    $payment_gateway = "";
                    $ledgerForCharges = "";
                    if ($ledgerid == $meeshoLedgerID)
                    {
                        $payment_gateway  = "meesho";
                        $ledgerForCharges = $meeshoChargesLedgerID;
                    }
                   
                    $ref_arr = array_column($line_of_text, 2);
                    unset($ref_arr[0]);
                    foreach ( $ref_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Ref', "msg" => "Please Enter Ref.No. in Meesho CSV = ". $value );
                        }
                    }

                    $row = 0;
                    // // // // // // 5(e). Check Amount Is_Numeric and positive // // // // //
                    $cod_or_pg = 'PG';
                    $amount_arr = array_column($line_of_text,3);
                    unset($amount_arr[0]);
                    foreach ( $amount_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Amount', "msg" => "Please Enter Amount in Numeric = ". $value );
                        }
                        if( $value == 0 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Amount', "msg" => "Please Enter Amount not equals to zero" );
                        }                        
                        if( $value < 0 ) {
                            $cod_or_pg = 'PGBR';
                        }                         
                    }

                    $row = 0;
                    // // // // // // 5(f). Check Charges Is_Numeric and positive // // // // //
                    $charges_arr = array_column($line_of_text,4);
                    unset($charges_arr[0]);
                    foreach ( $charges_arr as $value ){
                        $row++;
                        if (strlen($value) > 0)
                        {
                            if( !is_numeric(trim($value)) ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row+1, "name" => 'Charges', "msg" => "Please Enter Charges in Numeric = ". $value );
                            }
                            if( $value <= 0 ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row+1, "name" => 'Charges', "msg" => "Please Enter Positive Charges Value in Payment Gateway CSV = ". $value );
                            }
                        }
                    }
                   
                    // // // 5(gg). if value in amount > 0 then charges should be there // // // // //
                    $co = count($amount_arr);
                    for ($k=1; $k <= $co; $k++) {
                        if ($amount_arr[$k] > 0)
                        {
                            if ($charges_arr[$k] <= 0)
                            {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $k+1, "name" => 'Total', "msg" => "Please fill value in Charges = ". $charges_arr[$k] );
                            }
                        }
                    }

                    $row = 0;
                    // // // 5(f). Total Amt. minus charges of CSV (check vd amountOfReceipt Value) // // // // //
                    $amountTtl_arr   = array_sum($amount_arr);
                    $amountTtl_arr   = (float)$amountTtl_arr;

                    $chargesTtl_arr  = array_sum($charges_arr);
                    $chargesTtl_arr  = (float)$chargesTtl_arr;

                    $AmtMinusCharges = $amountTtl_arr - $chargesTtl_arr;
                    $AmtMinusCharges = (float)$AmtMinusCharges;

                    $checkAmountTotal = 0;
                    $diff = (round($amountOfReceipt,2) - round($AmtMinusCharges,2));
                    $diff1 = round($diff,2);
                    $diff2 = abs($diff1);
                    if ($diff2 != 0)
                    {
                        if ($diff2 > 0.5){
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => '', "name" => 'Diff', "msg" => "Total Amount Not Matched in Meesho CSV, Diff. of Rs. = ". $diff1 );
                        }
                        else
                        {
                            $round_off = $diff1;
                        }
                    }

                    // // // // // // 5(b). Checking order_no and ref no differently // // // // //
                    $orderNo_arr2 = array_column($line_of_text,1);
                    unset($orderNo_arr2[0]); 
                    
                    if ($checkedError ==1)
                    {
                        echo json_encode( $response );  //exit bcos of QR code
                        exit();
                    }
                    else
                    {
                        for ($k=1; $k <= $co; $k++) {
                            $order_no = $orderNo_arr2[$k];
                            $ref      = $ref_arr[$k];
                           
                            if ((float)($amount_arr[$k]) > 0)
                            {
                                $linkedOrderPayment = $this->model_account_panel_bankreceipt->getLinkedOrderPayment($ref, $payment_gateway );
                                if(!empty($linkedOrderPayment)){
                                    $checkedError = 1;
                                    $response['msgs'][] = array("row" => $k+1, "name" => 'Order and Ref already linked entry exist', "msg" => "Order No. and Ref. No. already exist in OrderPayment = ". $order_no.", and merchant_txn_id : ". $ref );
                                    echo json_encode( $response );  //exit bcos of QR code
                                    exit();  
                                }
                            }
                        }
                    }

                    //  // // // // // 5. Conditions Others- End// // // // // //
                    if ($checkedError !=1 && $ledgerid > 0){

                        ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                        $countNoOfOrders = count($line_of_text) - 2;
                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, $countNoOfOrders);
                        
                        //Entry into oc_receipt_sub table
                        $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $meeshoLedgerID, $sundryDebtorsGroupID, $amountOfReceipt, $user_id, $datedRO, $user_array );

                        //Get receipt_sub_id
                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

                        //Check orderPyament details linked with given payment gateway
                        for ($k=1; $k <= $co; $k++) {
                            $order_no = $orderNo_arr2[$k];
                            $ref      = $ref_arr[$k];
                           
                            if($amount_arr[$k] == $charges_arr[$k]){
                                continue; //Ignore those cases in which amount and chargeres having same value
                            }

                            $order_data = OrderInfo::getOrderIdsByOrderNos($this->db, $order_no); 
                            $order_id   = $order_data[$order_no]['order_id'] ?? 0;

                            //Get orderPayment entry if exist with given payment_gateway but not linked
                            $orderPaymentDetails = $this->model_account_panel_bankreceipt->getOpidByMerchantTrxnAndPaymentGateway($ref, $payment_gateway, $order_no );

                            $order_payment_data = array();

                            if(empty($orderPaymentDetails) ){
                                //To insert data in order payment table
                                $order_payment_data['order_id']          = $order_id;
                                $order_payment_data['merchant_txn_id']   = $ref;
                                $order_payment_data['order_no']          = $order_no;
                                $order_payment_data['txn_status']        = 'SUCCESS';
                                $order_payment_data['txn_date_time']     = $datedRO;
                                $order_payment_data['payment_mode']      = 'meesho';
                                $order_payment_data['amount']            = abs($amount_arr[$k]);
                                $order_payment_data['txn_date_time']     = date("Y-m-d", strtotime($dated_arr[$k]) );
                                $order_payment_data['date_added']        = date("Y-m-d H:i:s");
                                $order_payment_data['payment_gateway']   = $payment_gateway;
                                $order_payment_data['successfull']       = 1;
                                $order_payment_data['user_id']           = $user_id;
                                $order_payment_data['rec_pay_tablename'] = 'receipt';
                                $order_payment_data['rec_pay_id']        = $receipt_id;
                                $order_payment_data['rec_pay_sub_id']    = $receipt_sub_id;

                                OrderPayment::insertOrderPayment($this->db,$order_payment_data); 

                                //Get orderPayment entry if exist with given payment_gateway but not linked
                                $orderPaymentDetails = $this->model_account_panel_bankreceipt->getOpidByMerchantTrxnAndPaymentGateway($ref, $payment_gateway, $order_no);
                            }else{
                                $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $orderPaymentDetails['payment_id'], $order_id);
                            }
                            
                            $customer_ledger_id = 14761;
                            $order_payment_id   = $orderPaymentDetails['payment_id'] ?? 0;
                           
                            //Insert into oc_receipt_sub_csv table
                            $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $payment_gateway, $datedRO, $customer_ledger_id, abs($amount_arr[$k]), $order_no, $ref.'-meesho_order_charges', $order_payment_id, $order_id);

                            //Entry into oc_receipt_sub_chargesdr
                            if($charges_arr[$k] > 0){
                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, $receipt_sub_csv_id, $datedRO, $meeshoChargesLedgerID, abs($charges_arr[$k]), $order_no, $ref.'-meesho_order_charges', $order_payment_id, $order_id);
                            }

                        }
                        $response['msgs'] =  52;
                    }
                    else
                    {
                        echo json_encode( $response );
                        exit();                           
                    }
                }
                else
                {
                    $response['msgs'][] = array("row" => '', "name" => 'Invalid', "msg" => "Invalid CSV! Mistake in columns" );
                    echo json_encode( $response );
                    exit();   
                }
            }
        }
        //Meesho_order block end

        else if ($groupid == $creditAgencyGroupID)
        {
            ///////////////////// Credit Agency ////////////////////////////////
            ///////////////////// Credit Agency ////////////////////////////////
            ///////////////////// Credit Agency ////////////////////////////////

            //////// 2. Checking of CSV Default Code - start /////////
            $this->checkFileValidity($this->request->files);
            ////////2. Checking of CSV Default Code - End /////////


            //if no error in csv
            if (!$this->error) {
                ///////3. Now CSV Read from here./////////
                $file_handle = fopen($this->request->files['fileToUpload2']['tmp_name'], 'r');
                while (!feof($file_handle) ) {
                    $line_of_text[] = fgetcsv($file_handle, 1024);
                }
                fclose($file_handle);

                ////////////4. Condition of column of CSV and Crdit Agency CSV Import Only//////
                $getColumnsCACSV = array_values($line_of_text)[0];

                if ($getColumnsCACSV[0]=='Date' && $getColumnsCACSV[1]=='Order No' && $getColumnsCACSV[2]=='Amount' && $this->request->files['fileToUpload2']['name'] =='Credit_Agency_CSV.csv')
                {
                    //  // // // // // 5. Conditions Others- Start// // // // // //

                    //  // // // // // 5. Check ds_bulk entry// // // // // //
                    //if it is then no updating 
                    $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
                    
                    if ( $DS_Bulk_Entry )
                    {
                        $checkedError = 1;
                        $response['msgs'][] = array("row" => '', "name" => 'DS Bulk or Credit Agency Entry', "msg" => "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team" );
                        echo json_encode( $response );
                        exit();
                    }

                    // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                    $dated_arr = array_column($line_of_text,0);
                    unset($dated_arr[0]);
                    foreach ( $dated_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Date', "msg" => "Please Enter proper date in Credit Agency CSV = ". $value );
                        }
                    }
                    $row = 0;

                    // // // // // // 5(b). Check Order No Is_Numeric --- Start --- // // // // // // //
                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);
                    foreach ( $orderNo_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Order No', "msg" => "Please Enter Order No. in Numeric = ". $value );
                        }
                    }

                    $row = 0;
                    // // // // // // 5(b2). Check Order No in Oc_order Table --- Start --- // // // // // // //
                    /*
                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);
                    foreach ( $orderNo_arr as $value ){
                        $row++;
                        $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($value);

                        if ( !$CustomerName ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Order No', "msg" => "Order No. not found = ". $value );
                        }
                    }
                    */
                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);
                    foreach ( $orderNo_arr as $value ){
                        $row++;
                        if ($value != 666666)
                        {
                            //echo "yesss";die;
                            $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($value);

                            if ( !$CustomerName ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row+1, "name" => 'Order No', "msg" => "Order No. not found = ". $value );
                            }
                        }
                        //echo "no";die;
                    }

                    $row = 0;
                    // // // 5(d). Check Ref No. not blank and oc_suborder oc_order // // // // //
                    $credit_agency = "";
                    $ledgerForCharges = "";
                    $cod_or_pg = "";
                    if ($ledgerid == $neogrowthLedgerID)
                    {
                        $credit_agency = "neogrowth";
                        $ledgerForCharges = $neogrowthChargesLedgerID;
                        $cod_or_pg = "credit_agency";
                    }else if ($ledgerid == $udaanLedgerID)
                    {
                        $credit_agency = "udaan_credit";
                        $ledgerForCharges = $udaanChargesLedgerID;
                        $cod_or_pg = "credit_agency";
                    }

                    $row = 0;
                    $row2 = 0;
                    // // // // // // 5(e). Check Amount Is_Numeric and positive --- Start --- // // // // // // //
                    $amount_arr = array_column($line_of_text,2);
                    unset($amount_arr[0]);
                   
                    $co = count($amount_arr);
                    for ($k=1; $k <= $co; $k++) {
                        $row++;
                        $row2++;

                        if( !is_numeric(trim($amount_arr[$k])) ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row+1, "name" => 'Amount', "msg" => "Please Enter Amount in Numeric = ". $amount_arr[$k] );
                        }
                        if( $amount_arr[$k] == 0 ) {
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => $row2+1, "name" => 'Amount', "msg" => "Please Enter Amount not equals to zero" );
                        }  

                        if ($checkedError !=1 )
                        {
                            if ($orderNo_arr[$k] == 666666 )
                            {
                                $credit_agency_balance = $amount_arr[$k];
                            }
                        }
                    }

                    $row = 0;
                    $row2 = 0;
                    // // // // // // 5(f). Check Charges Is_Numeric and positive // // // // //
                    $charges_arr = array_column($line_of_text,3);
                    unset($charges_arr[0]);
                    foreach ( $charges_arr as $value ){
                        $row++;
                        $row2++;
                        if (strlen($value) > 0)
                        {
                            if( !is_numeric(trim($value)) ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row+1, "name" => 'Charges', "msg" => "Please Enter Charges in Numeric = ". $value );
                            }
                            if( $value <= 0 ) {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row2+1, "name" => 'Charges', "msg" => "Please Enter Positive Charges Value = ". $value );
                            }
                        }
                    }

                    $row = 0;

                    // // // 5(gg). if value in amount > 0 then charges should be there // // // // //
                    
                    $amount_arrx = array_column($line_of_text,2);
                    unset($amount_arrx[0]);
                    
                    $charges_arrx = array_column($line_of_text,3);
                    unset($charges_arrx[0]); 
                    
                    $co = count($amount_arrx);
                    for ($k=1; $k <= $co; $k++) {
                        $row++;
                        if ($orderNo_arr[$k] != 666666 )
                        {
                            if ($amount_arrx[$k] > 0)
                            {
                                if ($charges_arrx[$k] > 0)
                                {
                                    //echo "<br>";
                                    //echo $amount_arrx[$k];
                                    //echo "yesok2";die;
                                }
                                else
                                {
                                    $checkedError = 1;
                                    $response['msgs'][] = array("row" => $row+1, "name" => 'Charges', "msg" => "Please fill value in Charges = ". $charges_arrx[$k] );
                                }
                            }
                            else if ($amount_arrx[$k] < 0)
                            {
                                if ($charges_arrx[$k] > 0)
                                {
                                    $checkedError = 1;
                                    $response['msgs'][] = array("row" => $row+1, "name" => 'Charges', "msg" => "Please remove charges value in refund entry = ". $charges_arrx[$k] );
                                }
                            }
                        }
                        else
                        {
                            if (strlen($charges_arrx[$k]) > 0)
                            {
                                $checkedError = 1;
                                $response['msgs'][] = array("row" => $row+1, "name" => 'Charges', "msg" => "Please remove charges value = ". $charges_arrx[$k] );
                            }
                        }
                    }

                    $row = 0;

                    // // // 5(f). Total Amt. minus charges of CSV (check vd amountOfReceipt Value) // // // // //
                    $amountTotal_arr = array_column($line_of_text,2);
                    unset($amountTotal_arr[0]);
                    $amountTtl_arr = array_sum($amountTotal_arr);
                    $amountTtl_arr = (float)$amountTtl_arr;

                    $chargesTotal_arr = array_column($line_of_text,3);
                    unset($chargesTotal_arr[0]);
                    $chargesTtl_arr = array_sum($chargesTotal_arr);
                    $chargesTtl_arr = (float)$chargesTtl_arr;

                    $AmtMinusCharges = $amountTtl_arr - $chargesTtl_arr;
                    $AmtMinusCharges = (float)$AmtMinusCharges;

                    $checkAmountTotal = 0;
                    $diff = (round($amountOfReceipt,2) - round($AmtMinusCharges,2));
                    $diff1 = round($diff,2);
                    $diff2 = abs($diff1);
                    if ($diff2 != 0)
                    {
                        if ($diff2 > 0.5){
                            $checkedError = 1;
                            $response['msgs'][] = array("row" => '', "name" => 'Diff', "msg" => "Total Amount Not Matched in Credit Agency CSV, Diff  of Rs. = ". $diff1 );
                        }
                        else
                        {
                            $round_off = $diff1;
                            //$amountOfReceipt = $amountOfReceipt + $round_off;
                        }
                    }
                    
                    $countNoOfOrders = count($line_of_text) - 2;

                    //  // // // // // 5. Conditions Others- End// // // // // //

                    if ($checkedError !=1 && $ledgerid > 0 && strlen($refAA) > 0){

                        ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, $countNoOfOrders);
                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                        if ( !$checkReceiptSubForReceiptID ) {
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }
                        
                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

                        if ($round_off > 0)
                        {   
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSV($receipt_id, $receipt_sub_id, $credit_agency, $datedRO, $roundOffLedgerID, abs($round_off), '', 'round_off_ca', 0, 0);
                        }
                        else if ($round_off < 0)
                        {
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedRO, $roundOffLedgerID, abs($round_off), '', 'round_off_ca', 0, 0);
                        }

                        if ($credit_agency_balance > 0)
                        {   
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSV($receipt_id, $receipt_sub_id, $credit_agency, $datedRO, $creditAgencyBalanceLedgerID, abs($credit_agency_balance), '', 'credit_agency_balance', 0, 0);
                        }
                        else if ($credit_agency_balance < 0)
                        {
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedRO, $creditAgencyBalanceLedgerID, abs($credit_agency_balance), '', 'credit_agency_balance', 0, 0);
                        }

                        //////6(b). Insertion Updation in Receipt Sub CSV table)-- Start --- /////  
                        $i = 0;
                        foreach($line_of_text as $cav_data){
                            if (empty($cav_data['0']) || $i ==0) {
                                $i++;
                                continue;
                            }

                            $dated          = $cav_data['0'];
                            $order_no       = $cav_data['1'];
                            $amount         = $cav_data['2'];
                            $charges         = $cav_data['3'];

                            if ($order_no != 666666)
                            {
                                $datedx = "";
                                if( strlen( $dated ) > 1 ) {
                                    $datedx = $dated;
                                    $datedx = date("Y-m-d", strtotime($datedx));
                                }

                                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($order_no);

                                $this->load->model('accounts/salesreports');
                                $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                                $order_id = $CustomerName['order_id'];

                                $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                                $ledger_id = 0;
                                if ( $ledgerNameInLedgerTable == 0 ){

                                    $ledger_id = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);

                                    //ask from mdhrSir
                                    $gst_number = '';
                                    if (empty($CustomerName['gst_number'])) {
                                        $gst_number = 'N/A';
                                    }
                                    else
                                    {
                                        $gst_number = $CustomerName['gst_number'];   
                                    }      

                                    $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id, $CustomerName['customer_id']);
                                }
                                else
                                {
                                    $ledger_id = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                                }

                                if ($amount > 0)
                                {
                                    $getOrderForCA = $this->model_account_panel_bankreceipt->getOcOrderForCA($order_no, $amount, 1);

                                    if ( !$getOrderForCA ) {

                                        // before insert payment for credit agency, check whether payment entry is already created or not.
                                        // if payment entry is already their then update, otherwise insert 
                                        $payment_data = $this->model_account_panel_bankreceipt->getOrderPaymentUsingOrderAndPayment((string)$order_no, (string)$credit_agency);
                                        // also receipt id should be empty, i.e. order payment should not be linked with any other receipt 
                                        if (!empty($payment_data) && empty($payment_data['rec_pay_id'])) {
                                          $pmntID = $payment_data['payment_id'];
                                          $p_data = array(
                                            'rec_pay_tablename' => 'receipt',
                                            'rec_pay_id' => $receipt_id,
                                            'rec_pay_sub_id' => $receipt_sub_id,
                                            'merchant_txn_id' => $refAA,
                                            'txn_date_time' => $datedx,
                                            'amount' => $amount,
                                            'user_id' => $user_id
                                          );
                                          //$this->checkAndSendMailOnNeogrowthAmountMismatch((int)$order_id, (string)$order_no, (int)$pmntID, $p_data, $payment_data);
                                          $this->model_account_panel_bankreceipt->updateOrderPaymentUsingPaymentId((int)$pmntID, $p_data);
                                        }
                                        else {
                                          $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $refAA, $order_no, 'SUCCESS', $cod_or_pg, $amount, $datedx, date("Y-m-d H:i:s"), $credit_agency, 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);
                                        }
                                        
                                        $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id, $amount, $order_no, $refAA, $pmntID, $order_id);
                                        
                                        $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, $receipt_sub_csv_id, $datedx, $ledgerForCharges, $charges, $order_no, $refAA, $pmntID, $order_id);

                                    }
                                    else
                                    {

                                        $order_payment_id = $getOrderForCA['payment_id'];

                                        $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id, $amount, $order_no, $refAA, $order_payment_id, $order_id);
                                        
                                        $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, $receipt_sub_csv_id, $datedx, $ledgerForCharges, $charges, $order_no, $refAA, $order_payment_id, $order_id);

                                        $this->model_account_panel_bankreceipt->updateOcOrderPaymentForTxnDateAndTxnID($order_payment_id, $datedRO, $refAA); //bcos issue of 01/01/1970 

                                        $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $order_payment_id, $order_id);//08112017
                                    }
                                }
                                else
                                {
                                    $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $refAA, $order_no, 'REFUND SUCCESS', '', $amount, $datedx, date("Y-m-d H:i:s"), $credit_agency, 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);

                                    $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedx, $ledger_id, abs($amount), $order_no, $refAA, $pmntID, $order_id);
                                }
                            }
                        }
                        $response['msgs'] = 53;
                    }
                    else
                    {
                        echo json_encode( $response );
                        exit();                           
                    }
                }
                else
                {
                    $response['msgs'][] = array("row" => '', "name" => 'Invalid', "msg" => "Invalid Credit Agency CSV! Mistake in columns" );
                    echo json_encode( $response );
                    exit();
                }
            } 
        }
        /////Insertion and updation Other than COD and Payment Gateways means in Bank Ledger Account///////////////
        else if ($groupid == $cashGroupID || $groupid == $bankGroupID || $groupid == $expensesGroupID || $groupid == $sundryDebtorsInvGroupID || $groupid == $dutiesTaxesGroupID || $groupid == $incomesGroupID || $groupid == $securityDepositGroupID || $groupid == $fixedAssetsGroupID || $groupid == $investmentGroupID || $groupid == $advancesGroupID || $groupid == $suspenseGroupID || $groupid == $bankingGroupID)
        {
            if ($ledgerid > 0){

                //  // // // // // 5. Check ds_bulk entry// // // // // //
                //if it is then no updating 
                $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
                
                if ( $DS_Bulk_Entry )
                {
                    $checkDS_bulk_Entry = 1;
                    $checkedError = 1;
                    $response['msgs'][] = array("row" => '', "name" => 'DS Bulk or Credit Agency Entry', "msg" => "This entry will not be changed, For more details on what information you will need to provide, please contact to Tech. Team" );
                    echo json_encode( $response );
                    exit();
                }

                $checkReceiptSubForReceiptID2 = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                if ( !$checkReceiptSubForReceiptID2 ) {
                    $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                    
                } else {
                    $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 0);
                    $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                    $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                    $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                    //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                    $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                    $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                    $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                    $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                }
                //$response['status'] =  53;
                //$response['msgs'][] = array("row" => '', "name" => '', "msg" => "Entry has been saved Successfully" );
                $response['msgs'] = 54;
            }
        }
        else {
            $response['msgs'] = 99;
        }
        
        echo json_encode( $response );
        exit();
    }//end function

    /**
     * @info : Public method to get validate uploaded csv's format and type
     * @author: Nishu, April 2019
    */
    public function fileUploadValidationForBankReceipt(){
        $response = array();

        if(empty($this->request->files['fileToUpload2']['name'])){
            $checkedError = 1;
            $response['msgs'][] = array("row" => '', "name" => 'CSV', "msg" => "Please insert Payment gateway CSV" );
            echo json_encode( $response );
            exit();   
        }

        // Sanitize the filename
        $filename = basename(html_entity_decode($this->request->files['fileToUpload2']['name'], ENT_QUOTES, 'UTF-8'));

        $error_arr = array();

        // Validate the filename length
        if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
            $error_arr['warning']['filename']  = $this->language->get('error_filename');
        }

        // Allowed file extension types
        $allowed = array('csv',
                         'xls',
                         'xlsx');

        if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
            $error_arr['warning']['filetype'] = $this->language->get('error_filetype');
        }

        // Allowed file mime types
        $allowed = array('application/vnd.ms-excel',
                         'text/plain',
                         'text/csv',
                         'text/tsv');

        if (!in_array($this->request->files['fileToUpload2']['type'], $allowed)) {
            $error_arr['warning']['file_mime_type'] = $this->language->get('error_filetype');
        }

        // Check to see if any PHP files are trying to be uploaded
        $content = file_get_contents($this->request->files['fileToUpload2']['tmp_name']);

        if (preg_match('/\<\?php/i', $content)) {
            $error_arr['warning']['nophpfile'] = $this->language->get('error_filetype') ;
        }

        // Return any upload error
        if ($this->request->files['fileToUpload2']['error'] != UPLOAD_ERR_OK) {
            $error_arr['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUpload2']['error']);
        }

        return $error_arr;
    }

    public function addAjaxDirectSales() {      

        $response = array();

        $this->load->model('account_panel/bankreceipt');    

        ////1. Get All Values -- Start --- //////////////////
        $ledgerid = $this->request->post['ledgerid'];
        $groupid = $this->request->post['groupid'];
        $receipt_id = $_REQUEST['receipt_id'];
        $amountOfReceipt = $_REQUEST['amountOfReceipt'];
        //$order_id = $this->request->post['order_id'];
        $payment_id = $this->request->post['payment_id'];
        $mode = "DS";
        // $user_id = $this->user->getId();
        // $datedCM = date("Y-m-d H:i:s");

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        $sundryDebtorsGroupID = 10;
        $directSalesGroupID = 3;

        if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
            $response['status'] =  1;
        }
        else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
            $response['status'] =  1;
        }
        else if ($receipt_id == "" || strlen($receipt_id) == 0 || !is_numeric($receipt_id)) {
            $response['status'] =  1;
        }
        else if ($amountOfReceipt == "" || strlen($amountOfReceipt) == 0 || !is_numeric($amountOfReceipt)) {
            $response['status'] =  1;
        }
        else if ($payment_id == "" || strlen($payment_id) == 0 || !is_numeric($payment_id)) {
            $response['status'] =  1;
        }
        else
        {
            if ($groupid == $directSalesGroupID){
                if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($receipt_id) > 0 && is_numeric($receipt_id) && strlen($amountOfReceipt) > 0 && is_numeric($amountOfReceipt) && strlen($payment_id) > 0 && is_numeric($payment_id))
                {
                    $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailByPaymentID($payment_id, $amountOfReceipt, 1, 'bank_transfer', 'cash');

                    $paymentIDInReceiptSubCSV = $this->model_account_panel_bankreceipt->getReceiptSubCSVByPaymentID($orderDetails['order_id'], $payment_id);                    

                    //echo "<pre>";print_r($paymentIDInReceiptSubCSV);die;
                    if ( !$paymentIDInReceiptSubCSV ) {
                        ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 1);
                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);

                        if ( !$checkReceiptSubForReceiptID ) {
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            // $this->model_account_panel_bankreceipt->deleteReceiptSub($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteReceiptSubCSV($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteReceiptSubChargesDr($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }

                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

                            $dated          = $orderDetails['txn_date_time'];
                            $order_no       = $orderDetails['order_no'];
                            //$ledgerName     = $cav_data['2'];
                            $ref            = $orderDetails['merchant_txn_id'];
                            $amount         = $orderDetails['amount'];
                            $cod_or_pg      = 'DS';

                            $datedx = "";
                            if( strlen( $dated ) > 1 ) {
                                $datedx = $dated;
                                $datedx = date("Y-m-d", strtotime($datedx));
                            }

                            //$CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForDS($ref, 1);
                            //$order_payment_id = $orderDetails['payment_id'];
                            $order_id = $orderDetails['order_id'];

                            $ledgerName = $orderDetails['customer_id'];
                            $ledgerName .= (!empty(trim($orderDetails['firstname'])) ? '_' . trim($orderDetails['firstname']) : '');
                            $ledgerName .= (!empty(trim($orderDetails['lastname'])) ? '_' . trim($orderDetails['lastname']) : '');
                            $ledgerName .= (!empty(trim($orderDetails['payment_company'])) ? '_' . trim($orderDetails['payment_company']) : '');

                            $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);
                        
                            if ( $ledgerNameInLedgerTable == 0 ){
                                $ledger_id_new = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $user_id, $datedCM, $user_array);
                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id_new, $amount, $order_no, $ref, $payment_id, $order_id);                         
                            }
                            else
                            {
                                $ledger_id_old = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id_old, $amount, $order_no, $ref, $payment_id, $order_id);
                            }
                            $response['status'] =  51;
                    }
                    else
                    {
                        $response['status'] =  2;
                    }
                }
            }

        }
        echo json_encode( $response );
    }//end function

    public function addAjaxBuyersRefund() {      

        $response = array();

        $this->load->model('account_panel/bankreceipt');    

        ////1. Get All Values -- Start --- //////////////////
        $order_no = $this->request->post['order_no'];
        $RefNoBankPymt = $this->request->post['RefNoBankPymt'];;

        $ledgerid = $this->request->post['ledgerid'];
        $groupid = $this->request->post['groupid'];
        $receipt_id = $_REQUEST['receipt_id'];
        $amountOfReceipt = $_REQUEST['amountOfReceipt'];
        $datedRO = $this->request->post['datedROAA'];
        $ref = $this->request->post['ref'];//merchant txn
        $payment_mode = $this->request->post['bank_name'];//bank name

        $mode = "BB";

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        $sundryDebtorsGroupID = 10;
        $buyersRefundGroupID = 4;
        $cod_or_pg      = 'BRBB';

        
        $response = array();
        $ocOrderDetails = "";
        if ($order_no == "" || strlen($order_no) == 0 || !is_numeric($order_no)) {
            $response['status'] =  11;
            echo json_encode( $response );
            exit();
            
        }
        if ($RefNoBankPymt == "" || strlen($RefNoBankPymt) == 0) {
            $response['status'] =  12;
            echo json_encode( $response );
            exit();
        }

        //  // // // // // 5. Check ds_bulk entry 12122017// // // // // //
        //if it is then no updating 
        $checkDS_bulk_Entry = 0;
        $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
        
        if ( $DS_Bulk_Entry )
        {
            $checkDS_bulk_Entry = 1;
            $response['status'] =  666;
            echo json_encode( $response );
            exit();
        }

        $ocOrderDetails = $this->model_account_panel_bankreceipt->getOcOrderDetailForBuyersRefund($order_no, $RefNoBankPymt, -abs($amountOfReceipt), 'refund', 1);
        
        if ( !$ocOrderDetails ){
            $response['status'] =  1;
            echo json_encode($response) ;
            exit();
        }
        else
        {
            //echo "<pre>";print_r($ocOrderDetails);die;
            if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
                $response['status'] =  1;
            }
            else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
                $response['status'] =  1;
            }
            else if ($receipt_id == "" || strlen($receipt_id) == 0 || !is_numeric($receipt_id)) {
                $response['status'] =  1;
            }
            else if ($amountOfReceipt == "" || strlen($amountOfReceipt) == 0 || !is_numeric($amountOfReceipt)) {
                $response['status'] =  1;
            }
            else if ($order_no == "" || strlen($order_no) == 0 || !is_numeric($order_no)) {
                $response['status'] =  1;
            }
            else
            {
                if ($groupid == $buyersRefundGroupID){
                    if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($receipt_id) > 0 && is_numeric($receipt_id) && strlen($amountOfReceipt) > 0 && is_numeric($amountOfReceipt) && strlen($order_no) > 0 && is_numeric($order_no))
                    {
                        ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 1);
                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                        if ( !$checkReceiptSubForReceiptID ) {
                           $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            // $this->model_account_panel_bankreceipt->deleteReceiptSub($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteReceiptSubCSV($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteReceiptSubChargesDr($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017

                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }

                            $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

                            $order_id = $ocOrderDetails['order_id'];

                            $this->load->model('accounts/salesreports');
                            $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($ocOrderDetails['customer_id']) ;
                            /*
                            $ledgerName = $ocOrderDetails['customer_id'];
                            $ledgerName .= (!empty(trim($ocOrderDetails['firstname'])) ? '_' . trim($ocOrderDetails['firstname']) : '');
                            $ledgerName .= (!empty(trim($ocOrderDetails['lastname'])) ? '_' . trim($ocOrderDetails['lastname']) : '');
                            $ledgerName .= (!empty(trim($ocOrderDetails['payment_company'])) ? '_' . trim($ocOrderDetails['payment_company']) : '');                            
                            */
                            $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);
                        
                            if ( $ledgerNameInLedgerTable == 0 ){
                                $ledger_id_new = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $ocOrderDetails['customer_id'], $user_id, $datedCM, $user_array);
                                
                                //ask from mdhrSir
                                $gst_number = '';
                                if (empty($ocOrderDetails['gst_number'])) {
                                    $gst_number = 'N/A';
                                }
                                else
                                {
                                    $gst_number = $ocOrderDetails['gst_number'];   
                                }                                

                                $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id_new, $ocOrderDetails['customer_id']);

                                $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', $payment_mode, $amountOfReceipt, $datedRO, date("Y-m-d H:i:s"), 'bank_transfer', 0, $user_id, 'receipt', $receipt_id, $receipt_sub_id);

                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedRO, $ledger_id_new, $amountOfReceipt, $order_no, $ref, $pmntID, $order_id);
                            }
                            else
                            {
                                $ledger_id_old = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);

                                $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', $payment_mode, $amountOfReceipt, $datedRO, date("Y-m-d H:i:s"), 'bank_transfer', 0, $user_id, 'receipt', $receipt_id, $receipt_sub_id);

                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedRO, $ledger_id_old, $amountOfReceipt, $order_no, $ref, $pmntID, $order_id);
                            }
                            $response['status'] =  51;
                        //}
                        //else
                        //{
                            //$response['status'] =  2;
                        //}                         
                    }
                }
            }
        }
        echo json_encode( $response );
    }//end function

    public function addAjaxSalary() {      

        $response = array();

        $this->load->model('account_panel/bankreceipt');    

        ////1. Get All Values -- Start --- //////////////////
        //$order_no = $this->request->post['order_no'];
        $emp_code = $this->request->post['emp_code'];
        $ledgerid = $this->request->post['ledgerid'];
        $groupid = $this->request->post['groupid'];
        $receipt_id = $_REQUEST['receipt_id'];
        $amountOfReceipt = $_REQUEST['amountOfReceipt'];
        $datedRO = $this->request->post['datedROAA'];
        $ref = $this->request->post['ref'];//merchant txn
        $payment_mode = $this->request->post['bank_name'];//bank name

        $mode = "BB";

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        $sundryDebtorsGroupID = 10; 
        $buyersRefundGroupID = 4;
        $expensesGroupID = 12;
        $cod_or_pg      = 'SalaryChBounced';

        //  // // // // // 5. Check ds_bulk entry 12122017// // // // // //
        //if it is then no updating 
        $checkDS_bulk_Entry = 0;
        $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
        
        if ( $DS_Bulk_Entry )
        {
            $checkDS_bulk_Entry = 1;
            $response['status'] =  666;
            echo json_encode( $response );
            exit();
        }

        $LedgerDetail = $this->model_account_panel_bankreceipt->getLedgerByEmpCode($emp_code);

        $response = array();
        if ( !$LedgerDetail ){
            $response['status'] =  1;
            echo json_encode($response) ;
            exit();
        }
        else
        {
            //echo "<pre>";print_r($LedgerDetail);die;
            if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
                $response['status'] =  1;
            }
            else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
                $response['status'] =  1;
            }
            else if ($receipt_id == "" || strlen($receipt_id) == 0 || !is_numeric($receipt_id)) {
                $response['status'] =  1;
            }
            else if ($amountOfReceipt == "" || strlen($amountOfReceipt) == 0 || !is_numeric($amountOfReceipt)) {
                $response['status'] =  1;
            }
            else if ($emp_code == "" || strlen($emp_code) == 0) {
                $response['status'] =  1;
            }
            else
            {
                if ($groupid == $expensesGroupID)
                {
                    if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($receipt_id) > 0 && is_numeric($receipt_id) && strlen($amountOfReceipt) > 0 && is_numeric($amountOfReceipt) && strlen($emp_code) > 0)
                    {
                        ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 1);
                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                        if ( !$checkReceiptSubForReceiptID ) {
                           $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            // $this->model_account_panel_bankreceipt->deleteReceiptSub($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteReceiptSubCSV($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteReceiptSubChargesDr($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }

                            $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

                            //$order_id = $ocOrderDetails['order_id'];

                            $ledger_id2 = $LedgerDetail['ledger_id'];                       
                        
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedRO, $ledger_id2, $amountOfReceipt, '', $ref, 0, 0);

                            $response['status'] =  51;
                        //}
                        //else
                        //{
                            //$response['status'] =  2;
                        //}                         
                    }
                }
            }
        }
        echo json_encode( $response );
    }//end function

    public function deleteAll() {
        /*
        $this->load->model('account_panel/bankreceipt');
        $this->model_account_panel_bankreceipt->deleteAllReceipts();
        $this->response->redirect($this->url->link('account_panel/bankreceipt', '&token=' . $this->request->get['token'] . $url, 'SSL'));         
        */
    }

    /**
     * @info: method to validate NACH bank receipt CSV
     * @param: Array
     * @return: Nishu, April 2019
    */
    private function validateNachResponseCsv(array $csv_data) : array {
        $response = array();
        $response['error_msg'] = '';
        $response['status']    = 1;

        $columnsOfCSV = array_values($csv_data[0]);

        if( ($columnsOfCSV[0] != 'UMRN' && $columnsOfCSV[0] != 'LAN') 
            || $columnsOfCSV[1] != 'Date' || $columnsOfCSV[2] != 'Amount' || $columnsOfCSV[3] != 'Status' ){
            $response['status'] = 0;
            $response['error_msg'] .= 'CSV column(s) are not in proper order OR column(s) are missing. Please follow the format!<br>';
        }else{
            //Unset header row of CSV to validate values 
            unset($csv_data[0]);

            $umrn_arr   = array_column($csv_data, 0);
            $date_arr   = array_column($csv_data, 1); 
            $amount_arr = array_column($csv_data, 2);
            $status_arr = array_column($csv_data, 3);
            
            $total_amount         = 0.00;
            $total_receipt_amount = $this->request->post['amountAA'];
            
            $row_count = count($umrn_arr);

            for ($i=0; $i < $row_count; $i++) {
                if( !is_numeric(trim($amount_arr[$i])) ) {
                    $response['status'] = 0;
                    $response['error_msg'] .= "Row-". ($i+1) . ": Please Enter Amount in Numeric = ". $amount_arr[$i] ." for UMRN No: ". $umrn_arr[$i] .",<br>";
                }

                if( !is_numeric(trim($status_arr[$i])) ) {
                    $response['status'] = 0;
                    $response['error_msg'] .= "Row-". ($i+1) . ": Please Enter Status in Numeric = ". $status_arr[$i] ." for UMRN No: ". $umrn_arr[$i] .",<br>";
                }else if(trim($status_arr[$i]) != 0 && trim($status_arr[$i]) != 1 && trim($status_arr[$i]) != 2){
                    $response['status'] = 0;
                    $response['error_msg'] = "Row-". ($i+1) . ": Entered Status can be 0, 1 or 2, given status is = ". $status_arr[$i] ." for UMRN No: ". $umrn_arr[$i] . ",<br>";
                }

                //Total amount for status = 1, to match with receipt amount
                if(trim($status_arr[$i]) == 1){
                    $total_amount += (float)trim($amount_arr[$i]);
                }

            }
            if($response['status'] == 1 && abs($total_amount-$total_receipt_amount) > 0.01 ){
                $response['status'] = 0;
                $response['error_msg'] = "Sum of amount breakup in CSV file = ". $total_amount .", is not matching with total Receipt amount: ". $total_receipt_amount;
            }
        }

        if($response['status'] != 1){
            echo json_encode($response);
            exit();
        }
        return $response;
    }

    /**
     * @info: Method to upload NACH bank response sheet
     * @author: Nishu, April 2019
    */
    private function uploadNachResponseCsv(array $csv_data) : void {

        //Validate given data in .csv file 
        $response = $this->validateNachResponseCsv($csv_data);

        if($response['status'] == 1){

            //Receipt related info
            //$receipt_data = $this->request->post ?? array();

            //Unset Header Row from CSV data
            if(!empty($csv_data[0])){
                unset($csv_data[0]);
            }

            //Create object for NachBehaviour class
            $nach_obj = new NachBehaviour($this->registry);
            $data = array();

            $merchant_txn_id = $this->request->post['refAA'] ?? '';
            $user_id         = (int)($this->user->getId() ?? 0);
            //Verify given data in .csv file, at NACH schedule level
            $nach_response = $nach_obj->updateBankReceipt($csv_data, $data, $merchant_txn_id, $user_id);

            //Handle nach failure response
            if($nach_response['status'] != 1){
                echo json_encode($nach_response);
                exit();
            }

            //DB related changes
            $this->dbUpdateForNachUpload($csv_data, $data);
        }

        $response['status'] = 200;
        echo json_encode($response);
        exit();
    }

    /**
     * @info: Private method to update total number of order(s) against bank payment receipt
     * @param: int $receipt_id, array $data
     * @author: Nishu, May 2019
    */
    private function updateNoOfOrdersInReceipt(int $receipt_id, array $data){

        if(!empty($data)){
            //Load Model
            $this->load->model('account_panel/bankreceipt');
            $all_order_nos = array();
            
            //Loop data
            foreach ($data as $key => $value) {
                //Loop order wise
                foreach ($value['orders'] as $order_id => $order_data) {
                    if($order_data['status'] == 1){
                        $all_order_nos[$order_id] = $order_data['order_no'];
                    }
                }
            }

            $countNoOfOrders = count($all_order_nos);
            $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, $countNoOfOrders);
        }
    }

    /**
     * @info: Public method to do DB entries for NACH bank response upload(Bulk data upload)
     * @param: array, array
     * @author: Nishu
    */
    private function dbUpdateForNachUpload($csv_data, $data){
        if(!empty($csv_data)){

            $bounced_data     = array();

            //Set Post request data
            $ledgerid        = $this->request->post['ledgeridDS'];
            $groupid         = $this->request->post['groupidDS'];
            $receipt_id      = $this->request->post['receipt_idDS'];
            $amountOfReceipt = $this->request->post['amountAA'];
            $datedRO         = $this->request->post['datedROAA'];

            $user_id    = $this->user->getId();
            $user_name  = $this->user->getUserName()['username'];
            $datedCM    = date("Y-m-d H:i:s");
            $user_array = array(
                                'user_id' => $user_id,
                                'user_name' => $user_name,
                                'date' => $datedCM
                                );

            //Load Model
            $this->load->model('account_panel/bankreceipt');
            $this->load->model('accounts/salesreports');

            //Update number of orders in oc_receipt table
            $this->updateNoOfOrdersInReceipt($receipt_id, $data);

            //Entry into oc_receipt_sub table
            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedRO, $user_array );

            //Get receipt_sub_id
            $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

            //Loop data
            foreach ($data as $key => $value) {
                //Loop order wise
                foreach ($value['orders'] as $order_id => $order_data) {
                    $order_payment_id = (int)$order_data['order_payment_id'];

                    if($order_data['status'] == 1 && !empty($order_payment_id)){
                        $customer_id          = (int)($order_data['customer_id'] ?? 0);
                        $sundryDebtorsGroupID = 10;

                        $ledgerName = $this->model_accounts_salesreports->getCustomerLedger((int)$customer_id) ;

                        $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                        $customer_ledger_id = 0;
                        if ( $ledgerNameInLedgerTable == 0 ){

                            $customer_ledger_id = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $customer_id, $user_id, $datedCM, $user_array);

                            $this->model_account_panel_bankreceipt->updateCustomerLedger( $customer_ledger_id, $customer_id);
                        } else {
                            $customer_ledger_id = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                        }

                        //Insert into oc_receipt_sub_csv table
                        $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, 'wsb_credit_nach', $datedRO, $customer_ledger_id, $order_data['amount'], $order_data['order_no'], 'NACH sheet uplaod', $order_payment_id, $order_id);

                        //Update receipt ID and receipt_sub_id into oc_order_payment table
                        $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $order_payment_id, $order_id);
                    }elseif($order_data['status'] == 0){
                        $bounced_data[$key] = $value;
                    }
                }
            }

            //send BOUNCE summary mail
            if(!empty($bounced_data)){
                $this->sendNachBouncedSummary($bounced_data);
            }
        }
        $response['status'] = 200;
        return $response;        
    }

    /**
     * @info: Private method to send mail to operation team for NACH failure response
     * @param: array()
     * @author: Nishu, April 2019
    */
    private function sendNachBouncedSummary(array $bounce_data_for_customer){
        $r = 0;
        $data = array();

        //Loop over given CSV data, for those enteries which mark as Failure/Bounce
        foreach ($bounce_data_for_customer as $key => $value) {
            //Loop order wise
            foreach ($value['orders'] as $order_id => $order_data) {
            
                // $data[$r]['umrn_no']     = $order_data['umrn_no'];
                // $data[$r]['date']        = $order_data['date'];
                // $data[$r]['amount']      = $order_data['amount'];
                $data[$r]['customer_id'] = $order_data['customer_id'];

                $r++;
            }
        }

        if(!empty($data)){
            
            //Send Bounced Payment Summary mail 
            //$this->sendNachBouncedSummaryMail($data);
           
            //Send mail for individual order(s) in Pending/Processed/Shipped status with payment_method= wsb_credit
            $this->sendMailUnderProcessOrderOnHoldAndCreditBlocked($data);
        }
    }


    /**
     * @info: Private method to send mail to operation team for NACH failure response
     * @param: array()
     * @author: Nishu, April 2019
    */
    // private function sendNachBouncedSummaryMail($data = array()){
    //     if(!empty($data)){
    //       $html = MailTemplate::sendNachBouncedSummaryHtml($data);
          
    //       $mail = new PHPMailer();
    //       $mail->isSMTP();
    //       $mail->Host = $this->config->get('config_mail_smtp_hostname');
    //       $mail->Port = $this->config->get('config_mail_smtp_port');
    //       $mail->SMTPSecure = 'ssl';
    //       $mail->SMTPAuth = true;
    //       $mail->Username = $this->config->get('config_mail_smtp_username');
    //       $mail->Password = $this->config->get('config_mail_smtp_password');

    //       $mail->setFrom(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);

    //       $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
    //       $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
    //       $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
    //       $mail->addCC(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
    //       $mail->addCC(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
    //       $mail->addCC(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
    //       $mail->addCC(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
    //       $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
    //       $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
    //       $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);

    //       $mail->Subject = "NACH payment BOUNCE Summary  - " . date('d M Y H:i:s', time());

    //       $mail->msgHTML($html);
    //       $mail->send(1, false);
    //     }
    //     return true;
    // }

    /**
     * @info: Private method to send mail to operation team for Under processd orders on hold and credit blocked due to NACH payment failure/bounce response
     * @param: array()
     * @author: Nishu, April 2019
    */
    private function sendMailUnderProcessOrderOnHoldAndCreditBlocked($mail_data){
        if(!empty($mail_data)){

            //Get Unique customer_ids for credit blocked
            $customer_ids = array_unique( array_column($mail_data, 'customer_id') );

            // Need to sanitize the input string, which is supposed to be a comma separated string of "int" customer_id(s)
            // So, we explode back to array. array_map to convert them to integers (to prevent SQL injection)
            // Afterwards, we array_filter it out to remove invalid values. Then array_unique to remove duplicates
            $customer_ids = array_unique(array_filter(array_map('intval', $customer_ids), function($v) {return $v > 0;}));

            foreach ($customer_ids as $customer_id) {
                //BLOCK Customer's WSB Credit status due to NACH payment bounce
                $row_data = array();
                $row_data['customer_id'] = (int)$customer_id;
                $row_data['status']      = 'BLOCKED';
                $row_data['comment']     = 'Auto Blocked Credit due to payment bounced.';
                $wsb_credit = new WsbCreditPayment($this);
                $wsb_credit->updateWsbCreditStatus($row_data);
            }

            $order_statuses = array_merge(
                                ORDER_STATUS_CLUSTERS['order_received'],
                                ORDER_STATUS_CLUSTERS['processed'],
                                ORDER_STATUS_CLUSTERS['shipped']
                              );

            //Get order(S) for given customer_ids with flag fully under processed/shipped or not
            $order_data = OrderInfo::getOrdersForGivenStatusByCustomerIds($this->db, $customer_ids, $order_statuses);

            if(!empty($order_data)){

                //Send Mail regarding Order List in state: Pending, Processed and Shipped state
                $this->sendMailUnderProcessOrderOnHold($order_data);
            }
        }
    }

    /**
       * @info: Public static method to send mail internal team for NACH Schedules bank Failure response
       * @param : $data array
       * @author: Nishu, April 2019
    */
    private function sendMailUnderProcessOrderOnHold($data = array()){
        if(!empty($data) ){
          
          $html = MailTemplate::sendMailUnderProcessOrderOnHoldHtml($data);

          $mail = new  PHPMailer();
          $mail->isSMTP();
          $mail->Host = $this->config->get('config_mail_smtp_hostname');
          $mail->Port = $this->config->get('config_mail_smtp_port');
          $mail->SMTPSecure = 'ssl';
          $mail->SMTPAuth = true;
          $mail->Username = $this->config->get('config_mail_smtp_username');
          $mail->Password = $this->config->get('config_mail_smtp_password');

          $mail->setFrom(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);

          $mail->addAddress(EMAIL_IDS['nitesh']['email_id'], EMAIL_IDS['nitesh']['name']);
          $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
          $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
          $mail->addCC(EMAIL_IDS['credit']['email_id'], EMAIL_IDS['credit']['name']);
          $mail->addCC(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
          $mail->addCC(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
          $mail->addCC(EMAIL_IDS['operations']['email_id'], EMAIL_IDS['operations']['name']);
          $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
          $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
          $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);

          $mail->Subject = "URGENT! NACH Credit Payment BOUNCED - All Orders for those Customer(s), ".date('d M Y');

          $mail->msgHTML($html);
          $mail->send(1, false);
         
        } //If condition empty check
        return true;
    }

    /**
     * @info: Puclic Method to upload CSV by adding order payment and verify before linking 
     * @author: Nishu, Jan 2019
    */
    public function uploadDirectSalesBulkCSV($csv_file){
        $response                = array();
        $response['status']      = 1;
        
        $getData          = array();
        $all_order_nos    = array();
        $csv_total_amount = 0.00;
        $row_data         = array();
        $bank_amt         = (float)$this->request->post['amountAA'];

        if($_FILES["fileToUpload"]["size"] > 0)
        {
            $filename=$_FILES["fileToUpload"]["tmp_name"];      
            $getData = array_map('str_getcsv', file($filename));

            if(in_array('UMRN', $getData[0]) || in_array('LAN', $getData[0])){
                //Sheet is uploaded for NACH Schedules
                $response = $this->uploadNachResponseCsv($getData);
                return $response;
            }

            if(!empty($getData[0])){
                unset($getData[0]);
            }

            foreach ($getData as $value) {
                $all_order_nos[]   = $value[0];
                
                //Add Amount only for successful status = 1
                if((int)$value[2] == 1){
                    $csv_total_amount += (float)$value[1];
                } 
            }
            //If CSV's total amount breakup not match with total amount 
            if( abs($bank_amt - $csv_total_amount) > 0.0001 ) {
                $response['status'] =  17;
                return $response;
            }
            
            //Validate for duplicate order nos
            $all_unique_order_nos = array_unique($all_order_nos);
            if(count($all_unique_order_nos) <> count($all_order_nos) ){
                $response['status'] =  19;
                return $response;
            }

            //Get Order Ids for all given order nos
            $order_details = OrderInfo::getOrderIdsByOrderNos($this->db, $all_order_nos);

            //Check if any order no not having order Id
            if( count($all_order_nos) <> count($order_details)){
                $response['status'] =  18;
                return $response;
            }

            $response['payment_ids'] = array();

            //Loop to set row data array
            foreach ($getData as $value) {
                $order_no    = $value[0];
                $order_id    = $order_details[$order_no]['order_id'] ?? 0;
                $successfull = (int)$value[2] ?? 0;
                $txn_status = 'SUCCESS';
                if( $successfull != 1){
                    $txn_status = "FAILED";
                }
                //Refrence text for trxn
                $ref_text = $value[3] ?? "";

                $row_data['order_id']           = $order_id;
                $row_data['order_no']           = $order_no;
                $row_data['successfull']        = $successfull;
                $row_data['merchant_txn_id']    = $this->request->post['refAA'];
                $row_data['payment_link']       = $this->request->post['bank_nameAA'];
                $row_data['amount']             = $value[1];
                $row_data['bank_transfer_mode'] = 'instant';
                $row_data['txn_status']         = $txn_status;
                $payment_date                   = $this->request->post['datedROAA'];
                $row_data['txn_date_time']      = date("Y-m-d H:i:s", strtotime($payment_date));
                $row_data['date_added']         = date("Y-m-d H:i:s");
                $row_data['reference']          = 'NACH '.$txn_status.' - WholesaleBox Credit '.trim($ref_text);
                $row_data['payment_mode']       = 'NACH-WholesaleBox Credit';
                $row_data['payment_gateway']    = 'wsb_credit_nach';
                $row_data['serialize_response'] = serialize($row_data);
                $row_data['user_id']            = $this->user->getId();

                //Do entry in order payment and return order_payment_id
                $op_id = OrderPayment::insertOrderPayment($this->db, $row_data);

                if($successfull == 1){
                    $response['payment_ids'][] = $op_id;
                }
            }
        }
        
        return $response;
    }

    public function addAjaxDirectSales2() {

        $response = array();
        
        //Bulk upload CSV
        if( $_FILES["fileToUpload"]["size"] > 0 ){
            $response = $this->uploadDirectSalesBulkCSV($_FILES);
            if($response['status'] != 1){
                echo json_encode( $response );
                exit();
            }
            $payment_id      = $response['payment_ids'] ?? array();//Array
        }else{
            $payment_id      = $this->request->post['payment_idAA'];//Array
        }

        $this->load->model('account_panel/bankreceipt');

        ////1. Get All Values -- Start --- //////////////////
        $ledgerid        = $this->request->post['ledgeridDS'];
        $groupid         = $this->request->post['groupidDS'];
        $receipt_id      = $this->request->post['receipt_idDS'];
        $amountOfReceipt = $this->request->post['amountAA'];
       
        if (!empty($this->request->post['dsb'])) {
            $staffDsb_id = $this->request->post['dsb'];
        } else {
            $staffDsb_id = $this->request->post['staff'] ?? 0;//added after
        }
        
        $mode = "DS";

        $staffDsb_amt = 0;//added after 
        $datedRO = $this->request->post['datedROAA'];//it is only used for round off trnfr in Staff
        $refAA = $this->request->post['refAA'];//Only used to update merchantTxnID of OcOrderPayment

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        $sundryDebtorsGroupID = 10;
        $staffSalesGroupID = 17;
        $directSalesGroupID = 3;

        //$directSalesBalanceLedger_id = 7588; //local
        $directSalesBalanceLedger_id = 7634; //live

        if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
            $response['status'] =  1;
        }
        else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
            $response['status'] =  1;
        }
        else if ($receipt_id == "" || strlen($receipt_id) == 0 || !is_numeric($receipt_id)) {
            $response['status'] =  1;
        }
        else if ($amountOfReceipt == "" || strlen($amountOfReceipt) == 0 || !is_numeric($amountOfReceipt)) {
            $response['status'] =  1;
        }
        else
        {
            if ($groupid == $directSalesGroupID)
            {
                if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($receipt_id) > 0 && is_numeric($receipt_id) && strlen($amountOfReceipt) > 0 && is_numeric($amountOfReceipt))
                {
                    //  // // // // // 5. Check ds_bulk entry// // // // // //
                    //if it is then no updating 
                    $checkDS_bulk_Entry = 0;
                    $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
                    
                    if ( $DS_Bulk_Entry )
                    {
                        $checkDS_bulk_Entry = 1;
                        $response['status'] =  666;
                        echo json_encode( $response );
                        exit();
                    }

                    ////////checking order_payment_id/////////
                    $row = 0;
                    $checkPayment_id = 0;
                    foreach ( $payment_id as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkPayment_id = 1;
                            $response['status'] =  11;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();
                        }
                        if($value <= 0) {
                            $checkPayment_id = 1;
                            $response['status'] =  12;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();
                        }
                    }
                    $row = 0;
                    
                    $checkPayment_idDuplciate = 0;
                    if(count(array_unique($payment_id))<count($payment_id))
                    {
                        // Array has duplicates
                        $checkPayment_idDuplciate = 1;
                        $response['status'] =  13;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                              
                    }                        
                    
                    

                    $checkPayment_idInRecSubCSVTable = 0;
                    foreach ( $payment_id as $value ){
                        $row++;
                        if( strlen($value) > 1 ) {

                            $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailByPaymentID($value, $amountOfReceipt, 1, 'bank_transfer', 'cash', 'upi');

                            $paymentIDInReceiptSubCSV = $this->model_account_panel_bankreceipt->getReceiptSubCSVByPaymentID($orderDetails['order_id'], $value);//2bdeleted
                            
                            if ( $paymentIDInReceiptSubCSV ) {
                                $checkPayment_idInRecSubCSVTable = 1;
                                $response['status'] =  14;
                                $response['row'] =  $row+1;
                                echo json_encode( $response );
                                exit();                                
                            }
                        }
                    }

                    //Check payment Id not in same order ID
                    $checkPayment_idNotInSameOrderIDDuplicacy = 0;
                    $orderIDArrr = array();
                    foreach ( $payment_id as $value ){
                        $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailByPaymentID($value, $amountOfReceipt, 1, 'bank_transfer', 'cash', 'upi');
                        $orderIDArrr[$orderDetails['order_id']][] = $value;
                    }
                    
                    $row = 0;
                    $amount_arrxx = array();
                    foreach ( $payment_id as $value ){

                        $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailByPaymentID($value, $amountOfReceipt, 1, 'bank_transfer', 'cash', 'upi');

                        $amount_arrxx[] = $orderDetails['amount'];
                    }
                    
                    $amountTtl_arr = array_sum($amount_arrxx);
                    $amountTtl_arr = (float)$amountTtl_arr;
                    $checkAmountTotal = 0;

                    $diff = (round($amountOfReceipt,2) - round($amountTtl_arr,2));
                    $diff1 = round($diff,2);
                    
                    if ($diff1 != 0)
                    {
                        //dsb checking bcos -> Direct Sales Balance Ledger ID = '7634', we use 'dsb' bcos in future it can be possible of staff_id = 7634
                        if ($staffDsb_id == 'dsb')
                        {
                            $staffDsb_amt = $diff1;
                        }
                        else
                        {
                            if ($staffDsb_id == 0){
                                $checkAmountTotal = 1;
                                $response['status'] =  15;
                                $response['row'] =  $diff1;
                                echo json_encode( $response );
                                exit();
                            }
                            else
                            {
                                $staffDsb_amt = $diff1;
                            }
                        }
                    }

                    if ($checkPayment_id !=1 && $checkPayment_idDuplciate !=1 && $checkPayment_idInRecSubCSVTable!=1 && $checkPayment_idNotInSameOrderIDDuplicacy!=1)
                    {

                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 1);
                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);

                        if ( !$checkReceiptSubForReceiptID ) {
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );

                        } else {
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }

                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);
                        
                        if ($diff1 != 0)
                        {
                            if ($staffDsb_id == 'dsb')
                            {
                                if ($staffDsb_amt > 0)
                                {   
                                    $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, 'DSB', $datedRO, $directSalesBalanceLedger_id, abs($staffDsb_amt), '', 'round_off_DSB', 0, 0);
                                }
                                else if ($staffDsb_amt < 0)
                                {
                                    $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedRO, $directSalesBalanceLedger_id, abs($staffDsb_amt), '', 'round_off_DSB', 0, 0);
                                }
                            }
                            else
                            {
                                if ($staffDsb_id > 0){
                                    $staffName = $this->model_account_panel_bankreceipt->getStaffByID($staffDsb_id);

                                    $ledgerName = $staffName['staff_id'];
                                    $ledgerName .= (!empty(trim($staffName['name'])) ? '_' . trim($staffName['name']) : '');

                                    $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                                    $ledger_id = 0;
                                    if ( $ledgerNameInLedgerTable == 0 ){
                                        $ledger_id = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $staffSalesGroupID, 0, $user_id, $datedCM, $user_array);
                                    }
                                    else
                                    {
                                        //Old Ledgers Case
                                        $ledger_id = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                                    }

                                    if ($staffDsb_amt > 0)
                                    {   
                                        $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, 'staff', $datedRO, $ledger_id, abs($staffDsb_amt), '', 'round_off_staff_DS', 0, 0);
                                    }
                                    else if ($staffDsb_amt < 0)
                                    {
                                        $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedRO, $ledger_id, abs($staffDsb_amt), '', 'round_off_staff_DS', 0, 0);
                                    }
                                }
                            }
                        }

                        //////6(b). Insertion Updation in Receipt Sub CSV table)-- Start --- /////  

                        foreach ( $payment_id as $value ){

                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentForTxnDateAndTxnID($value, $datedRO, $refAA); //bcos issue of 01/01/1970 txn_date_time
                            $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailByPaymentID($value, $amountOfReceipt, 1, 'bank_transfer', 'cash', 'upi');

                            $dated  = $orderDetails['txn_date_time'] ?? $orderDetails['date_added'];
                            $order_no       = $orderDetails['order_no'];
                            $ref            = $orderDetails['merchant_txn_id'];
                            $amount         = $orderDetails['amount'];
                            $cod_or_pg      = 'DS';

                            

                            $datedx = "";
                            if( strlen( $dated ) > 1 ) {
                                $datedx = $dated;
                                $datedx = date("Y-m-d", strtotime($datedx));
                            }
                            $order_id = $orderDetails['order_id'];

                            $this->load->model('accounts/salesreports');
                            $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($orderDetails['customer_id']) ;

                            $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);
                        
                            if ( $ledgerNameInLedgerTable == 0 ){
                                
                                $ledger_id_new = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $orderDetails['customer_id'], $user_id, $datedCM, $user_array);
                                
                                //ask from mdhrSir
                                $gst_number = '';
                                if (empty($orderDetails['gst_number'])) {
                                    $gst_number = 'N/A';
                                }
                                else
                                {
                                    $gst_number = $orderDetails['gst_number'];   
                                }      

                                $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id_new, $orderDetails['customer_id']);
                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id_new, $amount, $order_no, $ref, $value, $order_id);

                                $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $value, $order_id);//08112017
                            }
                            else
                            {
                                $ledger_id_old = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id_old, $amount, $order_no, $ref, $value, $order_id);
                                $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $value, $order_id);//08112017
                            }
                            $response['status'] =  51;
                        }


                    }
                }
            }

        }
        echo json_encode( $response );
    }//end function


    public function getStaffs() {

        $this->load->model('account_panel/bankreceipt');
        $payIDAA = $this->request->post['payIDAA'];

        $orderIDArray = array();
        foreach ( $payIDAA as $value ){
            $orderIDs = $this->model_account_panel_bankreceipt->getOrderIDByPaymentIDOnlyForSalesStaff($value);
            $orderIDArray[] = $orderIDs['order_id'];
        }

        $staffs = $this->model_account_panel_bankreceipt->getStaffsForDS($orderIDArray);

        $staffListArr = array();

        foreach ($staffs as $key => $staff) {
          if(isset( $staff['status'] ) ){ 
              $staffListArr[$key]['sales_staff_id'] = $key;
              $staffListArr[$key]['name'] = $staff['name'];
           }
        }

        $response = array();
        if ( !$staffListArr ){
            $response['norecord'] = 0;
            $response['staffListArr'] =  '' ;
            echo json_encode($response) ; 
        }
        else
        {
            $response['norecord'] = 1;
            $response['staffListArr'] =  $staffListArr ;
            echo json_encode($response) ;            
        }
    }

    ////////////////////////////////////////QR Code ///////////////////////////////////////////////
    ////////////////////////////////////////QR Code ///////////////////////////////////////////////
    ////////////////////////////////////////QR Code ///////////////////////////////////////////////
    public function addAjaxQRCode() {

        $response = array();

        $this->load->model('account_panel/bankreceipt');    

        $payment_idQR = $this->request->post['payment_idQR'];//Array
        //echo "<pre>";print_r($payment_idQR);
        $QRCodeDataArray = $this->request->post['QRCodeDataArray'];//Array
        $QRCodeDataArray = unserialize(base64_decode($QRCodeDataArray));
        $line_of_text = $QRCodeDataArray['line_of_text'];

        $ledgerid = $QRCodeDataArray['ledgerid'];
        $groupid = $QRCodeDataArray['groupid'];
        $receipt_id = $QRCodeDataArray['receipt_id'];
        $amountOfReceipt = $QRCodeDataArray['amountOfReceipt'];
        $datedRO = $QRCodeDataArray['datedRO'];

        $row = 0;

        $mode = "QR";

        $user_id = $QRCodeDataArray['user_id'];
        $user_name = $QRCodeDataArray['user_name'];
        $datedCM = $QRCodeDataArray['datedCM'];
        $user_array = $QRCodeDataArray['user_array'];

        $round_off = $QRCodeDataArray['round_off'];
        $roundOffLedgerID = $QRCodeDataArray['roundOffLedgerID'];
        
        $fedexLedgerID    = $QRCodeDataArray['fedexLedgerID'];
        $meeshoLedgerID   = $QRCodeDataArray['meeshoLedgerID'];
        $gati_kweLedgerID = $QRCodeDataArray['gati_kweLedgerID'];
        $gati_ltdLedgerID = $QRCodeDataArray['gati_ltdLedgerID'];

        $citrusLedgerID = $QRCodeDataArray['citrusLedgerID'];
        $mswipeLedgerID = $QRCodeDataArray['mswipeLedgerID'];
        $paytmLedgerID = $QRCodeDataArray['paytmLedgerID'];
        $razorpayLedgerID = $QRCodeDataArray['razorpayLedgerID'];
        $clubfactoryLedgerID = $QRCodeDataArray['clubfactoryLedgerID'] ?? 15411;

        $citrusChargesLedgerID = $QRCodeDataArray['citrusChargesLedgerID'];
        $mswipeChargesLedgerID = $QRCodeDataArray['mswipeChargesLedgerID'];
        $paytmChargesLedgerID = $QRCodeDataArray['paytmChargesLedgerID'];
        $razorpayChargesLedgerID = $QRCodeDataArray['razorpayChargesLedgerID'];
        $clubfactoryChargesLedgerID = $QRCodeDataArray['clubfactoryChargesLedgerID'];

        $sundryDebtorsGroupID = $QRCodeDataArray['sundryDebtorsGroupID'];
        $CODGroupID = $QRCodeDataArray['CODGroupID'];
        $paymentgatewayGroupID = $QRCodeDataArray['paymentgatewayGroupID'];
        $bankGroupID = $QRCodeDataArray['bankGroupID'];
        $suspenseGroupID = $QRCodeDataArray['suspenseGroupID'];
        
        $countQRCodeEntries = $QRCodeDataArray['countQRCodeEntries'];

        $countNoOfOrders = count($line_of_text) - 2;


        $payment_gateway = "";
        $ledgerForCharges = "";
        if ($ledgerid == $citrusLedgerID)
        {
            $payment_gateway = "citrus";
            $ledgerForCharges = $citrusChargesLedgerID;
        }
        elseif ($ledgerid == $paytmLedgerID)
        {
            $payment_gateway = "paytm";
            $ledgerForCharges = $paytmChargesLedgerID;
        }
        elseif ($ledgerid == $razorpayLedgerID)
        {
            $payment_gateway = "razorpay";
            $ledgerForCharges = $razorpayChargesLedgerID;
        }
        elseif ($ledgerid == $clubfactoryLedgerID)
        {
            $payment_gateway = "club_factory";
            $ledgerForCharges = $clubfactoryChargesLedgerID;
        }
        elseif ($ledgerid == $mswipeLedgerID)
        {
            $payment_gateway = "mswipe";
            $ledgerForCharges = $mswipeChargesLedgerID;
        }


        ////1. Get All Values -- Start --- //////////////////

        if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
            $response['status'] =  1;
        }
        else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
            $response['status'] =  1;
        }
        else if ($receipt_id == "" || strlen($receipt_id) == 0 || !is_numeric($receipt_id)) {
            $response['status'] =  1;
        }
        else if ($amountOfReceipt == "" || strlen($amountOfReceipt) == 0 || !is_numeric($amountOfReceipt)) {
            $response['status'] =  1;
        }        
        else if ($ledgerForCharges == "" || strlen($ledgerForCharges) == 0 || !is_numeric($ledgerForCharges)) {
            $response['status'] =  1;
        }
        // else if ($payment_id == "" || strlen($payment_id) == 0 || !is_numeric($payment_id)) {
        //     $response['status'] =  1;
        // }
        else
        {
            if ($groupid == $paymentgatewayGroupID)
            {
                if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($receipt_id) > 0 && is_numeric($receipt_id) && strlen($amountOfReceipt) > 0 && is_numeric($amountOfReceipt))
                {
                    

                    /////////////////// check (count QR Code entries both side (if user change array name in inspect element))/////
                    $checkCountQREntries = 0;
                    if (count($payment_idQR) == $countQRCodeEntries)
                    {
                        //echo "yes";die;
                    }
                    else
                    {
                        $checkCountQREntries = 1;
                        $response['status'] =  93;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();
                    }

                    //////////////////////////////// checking order_payment_id/////////////////////////

                    //echo "<pre>";print_r($line_of_text);//die;
                    $amount_arrx = array_column($line_of_text,3);
                    unset($amount_arrx[0]);

                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);

                    $ref_arr = array_column($line_of_text,2);
                    unset($ref_arr[0]); 

                    $co = count($orderNo_arr);

                    //check oc_order_payment table by payment id and (order no, amount and all)
                    $checkPayment_idQR = 0;
                    foreach ( $payment_idQR as $value ){
                        if( strlen($value) > 1 ) {
                            
                            $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailByPaymentIDQR($value);
                            //echo $orderDetails['order_no'];//die;
                            if ( !$orderDetails ) {
                                $checkPayment_idQR = 1;
                                $response['status'] =  92;
                                $response['row'] =  $row+1;
                                echo json_encode( $response );
                                exit();   
                            }
                            /*
                            else
                            {
                                for ($k=1; $k <= $co; $k++) {

                                    if ($orderNo_arr[$k] > 0)
                                    {
                                        if ($orderNo_arr[$k] == $orderDetails['order_no']){
                                            $getOrdersForQRCode2 = $this->model_account_panel_bankreceipt->getOrderDetailForQRCode2($value, $orderNo_arr[$k], $amount_arrx[$k], 1);//Check 

                                            if ( !$getOrdersForQRCode2 ) {
                                                $checkPayment_idQR = 1;
                                                $response['status'] =  922;
                                                //$response['row'] =  $row+1;
                                                $response['row'] =  $orderNo_arr[$k];
                                                echo json_encode( $response );
                                                exit();
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $checkPayment_idQR = 1;
                                        $response['status'] =  99;
                                        $response['row'] =  $row+1;
                                        echo json_encode( $response );
                                        exit();   
                                    }
                                }
                            }
                            */
                        }
                        else
                        {
                            $checkPayment_idQR = 1;
                            $response['status'] =  91;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();   
                        }
                    }

                    $cod_or_pg = 'PG';
                    $amount_arr = array_column($line_of_text,3);
                    unset($amount_arr[0]);
                    foreach ( $amount_arr as $value ){
                        if( $value < 0 ) {
                            $cod_or_pg = 'PGBR';
                        }                         
                    }

                    if ($checkCountQREntries !=1 && $checkPayment_idQR !=1)
                    {
                        //Update merchant txn id in oc_order_payment table
                        /*
                        foreach ( $payment_idQR as $value ){
                            if( strlen($value) > 1 ) {

                                $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailByPaymentIDQR($value);

                                for ($k=1; $k <= $co; $k++) {

                                    if ($orderNo_arr[$k] > 0)
                                    {
                                        if ($orderNo_arr[$k] == $orderDetails['order_no']){
                                            //echo "yesh";die;
                                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentForQR($value, $orderDetails['order_no'], $ref_arr[$k], $payment_gateway);
                                        }
                                    }
                                }
                            }
                        }
                        */

                        $iValue = 0;
                        foreach ( $payment_idQR as $value ){
                            $iValue++;
                            if( strlen($value) > 1 ) {

                                $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailByPaymentIDQR($value);

                                $this->model_account_panel_bankreceipt->updateOcOrderPaymentForQR($value, $orderDetails['order_no'], $ref_arr[$iValue], $payment_gateway);
                            }
                        }

                        ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, $countNoOfOrders);

                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                        if ( !$checkReceiptSubForReceiptID ) {
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            // $this->model_account_panel_bankreceipt->deleteReceiptSub($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteReceiptSubCSV($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteReceiptSubChargesDr($receipt_id);
                            // $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }


                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

                        if ($round_off > 0)
                        {
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $payment_gateway, $datedRO, $roundOffLedgerID, abs($round_off), '', 'round_off_pg', 0, 0);
                        }
                        else if ($round_off < 0)
                        {
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedRO, $roundOffLedgerID, abs($round_off), '', 'round_off_pg', 0, 0);
                        }

                        //////6(b). Insertion Updation in Receipt Sub CSV table)-- Start --- /////  
                        $i = 0;
                        foreach($line_of_text as $cav_data){
                            if (empty($cav_data['0']) || $i ==0) {
                                $i++;
                                continue;
                            }

                            $dated              = $cav_data['0'];
                            $order_no           = $cav_data['1'];
                            //$ledgerName       = $cav_data['2'];
                            $ref                = $cav_data['2'];//merchant_txn_id
                            $amount             = $cav_data['3'];
                            $charges            = $cav_data['4'];
                            //$cod_or_pg          = 'PG';

                            $datedx = "";
                            if( strlen( $dated ) > 1 ) {
                                $datedx = $dated;
                                $datedx = date("Y-m-d", strtotime($datedx));
                            }
                            
                            //update txn_date_time by CSV date
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentForQRTxnDateTime($datedx, $order_no, $ref, $payment_gateway, 1); //bcos issue of 01/01/1970 txn_date_time

                            $CustomerName = "";
                            $ledgerName = "";
                            $order_id = 0;
                            if ((float)($amount) < 0)
                            {
                                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($order_no);
                                //echo "<pre>";print_r($CustomerName);//die;

                                $this->load->model('accounts/salesreports');
                                $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                                /*
                                $ledgerName = $CustomerName['customer_id'];
                                $ledgerName .= (!empty(trim($CustomerName['firstname'])) ? '_' . trim($CustomerName['firstname']) : '');
                                $ledgerName .= (!empty(trim($CustomerName['lastname'])) ? '_' . trim($CustomerName['lastname']) : '');
                                $ledgerName .= (!empty(trim($CustomerName['payment_company'])) ? '_' . trim($CustomerName['payment_company']) : '');
                                */

                                $order_id = $CustomerName['order_id'];
                            }
                            else if ((float)($amount) > 0)
                            {
                                $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForPG($ref, $payment_gateway, 1);

                                //echo "<pre>";print_r($CustomerName);//die;

                                $this->load->model('accounts/salesreports');
                                $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                                /*
                                $ledgerName = $CustomerName['customer_id'];
                                $ledgerName .= (!empty(trim($CustomerName['firstname'])) ? '_' . trim($CustomerName['firstname']) : '');
                                $ledgerName .= (!empty(trim($CustomerName['lastname'])) ? '_' . trim($CustomerName['lastname']) : '');
                                $ledgerName .= (!empty(trim($CustomerName['payment_company'])) ? '_' . trim($CustomerName['payment_company']) : '');
                                */

                                $order_payment_id = $CustomerName['payment_id'];
                                $order_id = $CustomerName['order_id'];
                            }

                            $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                            $ledger_id = 0;
                            if ( $ledgerNameInLedgerTable == 0 ){
  
                                $ledger_id = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);

                                //ask from mdhrSir
                                $gst_number = '';
                                if (empty($CustomerName['gst_number'])) {
                                    $gst_number = 'N/A';
                                }
                                else
                                {
                                    $gst_number = $CustomerName['gst_number'];   
                                }                                

                                $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id, $CustomerName['customer_id']);
                            }
                            else
                            {
                                $ledger_id = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                            }

                            if ($amount > 0)
                            {
                                //$cod_or_pg = 'PG';
                                $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id, $amount, $order_no, $ref, $order_payment_id, $order_id);

                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, $receipt_sub_csv_id, $datedx, $ledgerForCharges, $charges, $order_no, $ref, $order_payment_id, $order_id);
                                
                                $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $order_payment_id, $order_id);//08112017
                            }
                            else
                            {
                                $this->load->model('account_panel/bankpayment');
                                
                                $pmntID = $this->model_account_panel_bankpayment->getRefundFromOcOrderPayment($order_id, $ref, -abs($amount), $payment_gateway);

                                if (!$pmntID) {
                                    $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', '', $amount, $datedx, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);
                                }

                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, 0, $datedx, $ledger_id, abs($amount), $order_no, $ref, $pmntID, $order_id);
                            }
                            $response['status'] =  52;
                        }
                    }
                }
            }

        }
        echo json_encode( $response );
    }//end function

    public function importDirectSalesBulkCSV() {

        $this->load->model('account_panel/bankreceipt');

        $directSalesGroupID = 3;
        $directSalesLedgerID = 10;
        $sundryDebtorsGroupID = 10;

        $response = array();

        $row = 0;

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        //////// 1. Checking of CSV Default Code - start /////////
        if(empty($this->request->files['fileToUploadDirectSalesBulkCSV']['name'])){
                //$this->response->redirect($this->url->link('account_panel/bankpayment', "", 'SSL'));
            $response['status'] = 1;
            echo json_encode( $response );
            exit();                  
        }
        // Sanitize the filename
        $filename = basename(html_entity_decode($this->request->files['fileToUploadDirectSalesBulkCSV']['name'], ENT_QUOTES, 'UTF-8'));

        // Validate the filename length
        if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
            $this->error['warning']['filename']  = $this->language->get('error_filename');
        }

        // Allowed file extension types
        $allowed = array('csv',
                         'xls',
                         'xlsx');

        if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
            $this->error['warning']['filetype'] = $this->language->get('error_filetype');
        }
        // Allowed file mime types
        $allowed = array('application/vnd.ms-excel',
                         'text/plain',
                         'text/csv',
                         'text/tsv');

        if (!in_array($this->request->files['fileToUploadDirectSalesBulkCSV']['type'], $allowed)) {
            $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
            // $json['error'] = $this->language->get('error_filetype');
        }

        // Check to see if any PHP files are trying to be uploaded
        $content = file_get_contents($this->request->files['fileToUploadDirectSalesBulkCSV']['tmp_name']);

        if (preg_match('/\<\?php/i', $content)) {
            $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
        }

        // Return any upload error
        if ($this->request->files['fileToUploadDirectSalesBulkCSV']['error'] != UPLOAD_ERR_OK) {
            $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUploadDirectSalesBulkCSV']['error']);
        }

        ////////1. Checking of CSV Default Code - End /////////

        //if no error in csv
        if (!$this->error) {
            ///////2. Now CSV Read here./////////
            $file_handle = fopen($this->request->files['fileToUploadDirectSalesBulkCSV']['tmp_name'], 'r');
            while (!feof($file_handle) ) {
                $line_of_text[] = fgetcsv($file_handle, 1024);
            }
            fclose($file_handle);

            ////////////3. Condition of column of CSV and Direct Sales Bulk CSV Import Only//////
            $getColumnsDirectSalesBulkCSV = array_values($line_of_text)[0];

            if ($getColumnsDirectSalesBulkCSV[0]=='Date' && $getColumnsDirectSalesBulkCSV[1]=='Order No' && $getColumnsDirectSalesBulkCSV[2]=='Amount' && $getColumnsDirectSalesBulkCSV[3]=='Ref' && $this->request->files['fileToUploadDirectSalesBulkCSV']['name'] =='Direct_Sales_Bulk_CSV.csv')
            {
                //  // // // // // 5. Conditions Others- Start// // // // // //

                // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                $dated_arr = array_column($line_of_text,0);
                unset($dated_arr[0]);
                $checkDated_arr = 0;
                foreach ( $dated_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkDated_arr = 1;
                        $response['status'] =  2;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                }

                // // // // // // 5(b). Check Order No Is_Numeric --- Start --- // // // // // // //
                $row = 0;
                $orderNo_arr = array_column($line_of_text,1);
                unset($orderNo_arr[0]);
                $checkOrderNo_arr = 0;
                foreach ( $orderNo_arr as $value ){
                    $row++;
                    if( !is_numeric(trim($value)) ) {
                        $checkOrderNo_arr = 1;
                        $response['status'] =  3;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                    if( strlen($value) > 1 ) {
                        $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($value);
                        if ( !$CustomerName ) {
                            $checkOrderNo_arr = 1;
                            $response['status'] =  4;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                                
                        }
                    } 
                }

                // // // 5(c). Checking Duplicate Order No  // // // // //          //2baskd from vksG mdhrSir
                if(count(array_unique($orderNo_arr))<count($orderNo_arr))
                {
                    // Array has duplicates
                    $checkOrderNo_arr = 1;
                    $response['status'] =  44;
                    $response['row'] =  $row+1;
                    echo json_encode( $response );
                    exit();                              
                } 

                // // // // // // 5(d). Check Amount Is_Numeric and positive --- Start --- // // // // // // //
                $row = 0;
                $amount_arr = array_column($line_of_text,2);
                unset($amount_arr[0]);
                $checkAmount_arr = 0;
                foreach ( $amount_arr as $value ){
                    $row++;
                    if( !is_numeric(trim($value)) ) {
                        $checkAmount_arr = 1;
                        $response['status'] =  5;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                    if($value <= 0) {
                        $checkAmount_arr = 1;
                        $response['status'] =  6;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                }

                // // // 5(e). Check Bank Ref No. not blank // // // // //
                $row = 0;
                $bank_ref_arr = array_column($line_of_text,3);
                unset($bank_ref_arr[0]);
                $checkBankRef_arr = 0;
                foreach ( $bank_ref_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkBankRef_arr = 1;
                        $response['status'] =  7;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();
                    }
                }

                // // // 5(f). other most most most important conditions checking:- // // // // //
                $unique_bank_ref_arr = array_unique($bank_ref_arr);
                $unique_bank_ref_arr = array_values($unique_bank_ref_arr);

                // // // // // // 5(f-a). Check Bank Ref No.  // // // // // // //
                foreach ( $unique_bank_ref_arr as $value ){
                    //Ref No. should be in Payment Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInPayment = $this->model_account_panel_bankreceipt->getReceiptForRef2($value);
                        if ( !$checkRefEntryInPayment ) {
                            $checkBankRef_arr = 1;
                            $response['status'] =  8;
                            //$response['row'] =  $row+1;
                            $response['row'] =  $value;
                            echo json_encode( $response );
                            exit();                                
                        }
                    }
                    //Ref No. should not be in Payment Sub Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInPaymentSub = $this->model_account_panel_bankreceipt->getReceiptSubForRef($value);
                        if ( $checkRefEntryInPaymentSub ) {
                            $checkBankRef_arr = 1;
                            $response['status'] =  9;
                            //$response['row'] =  $row+1;
                            $response['row'] =  $value;
                            echo json_encode( $response );
                            exit();                                
                        }
                    }
                }

                //$unique_bank_ref_count = array_count_values($bank_ref_arr);
                //echo "<pre>";print_r($unique_bank_ref_count);//die;

                // // // // // // 5(g-b). reference no. wise Amount calculation   // // // // // // //
                $calculated_amt_arr = array();
                
                $co = count($bank_ref_arr);

                foreach ( $unique_bank_ref_arr as $key => $value ){
                    $sum = 0;
                    for ($k=1; $k <= $co; $k++) {
                        if ($bank_ref_arr[$k] == $value)
                        {
                            if ($amount_arr[$k] > 0)
                            {
                                $sum  = $sum + $amount_arr[$k];
                            }
                        }
                    }
                    array_push($calculated_amt_arr, $sum);
                }

                $bank_ref_arr_count = count(array_unique($bank_ref_arr));
                $amt_arr_count = count(array_unique($calculated_amt_arr));

                $checkCountRefAndAmt = 0;
                if ($bank_ref_arr_count != $amt_arr_count)
                {
                    $checkCountRefAndAmt = 1;
                    $response['status'] =  99;
                    $response['row'] =  $row+1;
                    echo json_encode( $response );
                    exit(); 
                }

                //unique_bank_ref_arr
                // <pre>Array
                // (
                //     [0] => 226
                //     [1] => 227
                // )
                //calculated_amt_arr
                // Array
                // (
                //     [0] => 150
                //     [1] => 50
                // )
                // // // // // // 5(g-c). Reference wise calculated Amount matching with bank payment amount   // // // // // // //
                $checkAmtOfPayment = 0;
                $co = count($unique_bank_ref_arr);
                for ($k=0; $k < $co; $k++)
                {
                    $amountOfReceipt = $this->model_account_panel_bankreceipt->getAmountByRef($unique_bank_ref_arr[$k]);
                    if (round($amountOfReceipt,2) != round($calculated_amt_arr[$k],2))
                    {
                        //$checkAmtOfPayment = 1;
                        $response['status'] =  10;
                        //$response['row'] =  $row+1;
                        $response['row'] =  $unique_bank_ref_arr[$k];
                        echo json_encode( $response );
                        exit(); 
                    }
                }
/*
                //////5(g-h). Conditions : order No. should not be in oc_order_payment --- /////
                $checkAlreadyDoneEntry = 0;
                $i = 0;
                foreach($line_of_text as $cav_data){
                    if (empty($cav_data['0']) || $i ==0) {
                        $i++;
                        continue;
                    }

                    $order_no    = $cav_data['1'];
                    $amount      = $cav_data['2'];
                    $bank_ref    = $cav_data['3'];

                    $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailForDS($order_no, $amount, 1);
                    
                    $row++;
                    if ($orderDetails) {
                        $checkAlreadyDoneEntry = 1;
                        $response['status'] =  11;
                        $response['row'] =  $order_no;
                        echo json_encode( $response );
                        exit();   
                    }
                }
*/
                //////5(g-h). Conditions : order No. should not more than 2 times in oc_order_payment --- /////
                $check2TimesEntry = 0;
                $i = 0;
                foreach($line_of_text as $cav_data){
                    if (empty($cav_data['0']) || $i ==0) {
                        $i++;
                        continue;
                    }

                    $order_no    = $cav_data['1'];
                    $amount      = $cav_data['2'];
                    $bank_ref    = $cav_data['3'];

                    $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailForDS($order_no, $amount, 1);
                    //echo "<pre>";print_r($orderDetails);//die;
                    //order no not more than 1 entry in oc_order_payment
                    $co = count($orderDetails);//die;
                    if ($co > 1) {
                        $check2TimesEntry = 1;
                        $response['status'] =  12;
                        $response['row'] =  $order_no;
                        echo json_encode( $response );
                        exit();
                    }
                    elseif ($co == 1)
                    {
                        $rec_pay_tablename = $orderDetails[0]['rec_pay_tablename'];
                        $rec_pay_id = $orderDetails[0]['rec_pay_id'];
                        $rec_pay_sub_id = $orderDetails[0]['rec_pay_sub_id'];

                        //if ((strlen($rec_pay_tablename) != 'not_applicable') || ($rec_pay_id > 0) || ($rec_pay_sub_id > 0) )
                        //if (($rec_pay_tablename != 'not_applicable') || ($rec_pay_id > 0) || ($rec_pay_sub_id > 0) )
                        if (($rec_pay_tablename != 'not_applicable') || ($rec_pay_id > 0) || ($rec_pay_sub_id > 0) )
                        {
                            $check2TimesEntry = 1;
                            $response['status'] =  13;
                            $response['row'] =  $order_no;
                            echo json_encode( $response );
                            exit();
                        }

                    }

                    //$orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailAlreadyLinked($order_no, $amount, 1);

                }
//echo "out";die;

                $row = 0;
                if ($checkDated_arr !=1 && $checkOrderNo_arr !=1 && $checkAmount_arr !=1 && $checkBankRef_arr !=1 && $checkCountRefAndAmt !=1 && $checkAmtOfPayment !=1 && $check2TimesEntry !=1)
                {
                    //////5. Insertion Updation in Receipt Sub, Receipt Sub CSV tables)-- Start --- /////  
                    foreach ( $unique_bank_ref_arr as $value ){

                        ////1. Get All Values -- Start --- //////////////////

                        ////5(a). Insertion Updation in Receipt Sub -- Start --- /////
                        $getReceiptData = $this->model_account_panel_bankreceipt->getReceiptData($value);

                        ////1. Get All Values -- Start --- //////////////////
                        $ledgerid = $directSalesLedgerID;
                        $groupid = $directSalesGroupID;
                        $receipt_id = $getReceiptData['receipt_id'];
                        $amountOfReceipt = $getReceiptData['amount'];
                        $datedRO = $getReceiptData['dated'];
                        // $ref = $getReceiptData['reference'];
                        // $bank_name = $getReceiptData['ledger_name'];//bank name

                        $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 1);
                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);


                        if ( !$checkReceiptSubForReceiptID ) {
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }

                        //$payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);
                    }

                    //////5. Insertion Updation in Receipt Sub CSV tables-- Start --- /////  

                    $i = 0;
                    foreach($line_of_text as $cav_data){
                        if (empty($cav_data['0']) || $i ==0) {
                            $i++;
                            continue;
                        }

                        $dated             = $cav_data['0'];
                        $order_no          = $cav_data['1'];
                        //$ref               = $cav_data['2'];//merchant_txn_id
                        $amount            = $cav_data['2'];
                        $bank_ref          = $cav_data['3'];
                        //$payment_gateway   = $cav_data['5'];
                        $cod_or_pg         = 'DS_Bulk';

                        $datedx = "";
                        if( strlen( $dated ) > 1 ) {
                            $datedx = $dated;
                            $datedx = date("Y-m-d", strtotime($datedx));
                        }
                        
                        $getReceiptData = $this->model_account_panel_bankreceipt->getReceiptData($bank_ref);
                        //echo "<pre>"print_r($getReceiptData);die;
                        $receipt_id = $getReceiptData['receipt_id'];
                        $bank_name = $getReceiptData['ledger_name'];//bank name

                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);


                        //$amount = -abs($amount);
                        $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($order_no);

                        $this->load->model('accounts/salesreports');
                        $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                        /*
                        $ledgerName = $CustomerName['customer_id'];
                        $ledgerName .= (!empty(trim($CustomerName['firstname'])) ? '_' . trim($CustomerName['firstname']) : '');
                        $ledgerName .= (!empty(trim($CustomerName['lastname'])) ? '_' . trim($CustomerName['lastname']) : '');
                        $ledgerName .= (!empty(trim($CustomerName['payment_company'])) ? '_' . trim($CustomerName['payment_company']) : '');
                        */

                        $order_id = $CustomerName['order_id'];
                        $order_no = $CustomerName['order_no'];

                        $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                        $ledger_id = 0;
                        if ( $ledgerNameInLedgerTable == 0 ){

                            $ledger_id = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);

                            $gst_number = '';
                            if (empty($CustomerName['gst_number'])) {
                                $gst_number = 'N/A';
                            }
                            else
                            {
                                $gst_number = $CustomerName['gst_number'];   
                            }

                            $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id, $CustomerName['customer_id']);
                        }
                        else
                        {
                            //Old Ledgers Case
                            $ledger_id = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                        }

                        $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailForDS($order_no, $amount, 1);
                        $co = count($orderDetails);
                        if ($co == 1) {

                            $order_payment_id = $orderDetails[0]['payment_id'];

                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentForTxnDateAndTxnID($order_payment_id, $datedx, $bank_ref); //bcos issue of 01/01/1970 txn_date_time
                            
                            $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id, $amount, $order_no, $bank_ref, $order_payment_id, $order_id);

                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentForRec_pay_ID('receipt', $receipt_id, $receipt_sub_id, $order_payment_id, $order_id);//08112017

                        }
                        elseif ($co == 0){

                                $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $bank_ref, $order_no, 'SUCCESS', $bank_name, $amount, $datedx, date("Y-m-d H:i:s"), 'bank_transfer', 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);

                                $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedx, $ledger_id, $amount, $order_no, $bank_ref, $pmntID, $order_id);

                        }
                        $response['status'] =  51;
                    }
                }
            }
            else
            {
                $response['status'] =  50;
            }
        }
        echo json_encode( $response );
    }

    public function importDirectSalesBulkCSVx() {

        $this->load->model('account_panel/bankreceipt');

        $directSalesGroupID = 3;
        $directSalesLedgerID = 10;
        $sundryDebtorsGroupID = 10;

        $response = array();

        $row = 0;

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        //////// 1. Checking of CSV Default Code - start /////////
        if(empty($this->request->files['fileToUploadDirectSalesBulkCSV']['name'])){
                //$this->response->redirect($this->url->link('account_panel/bankpayment', "", 'SSL'));
            $response['status'] = 1;
            echo json_encode( $response );
            exit();                  
        }
        // Sanitize the filename
        $filename = basename(html_entity_decode($this->request->files['fileToUploadDirectSalesBulkCSV']['name'], ENT_QUOTES, 'UTF-8'));

        // Validate the filename length
        if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
            $this->error['warning']['filename']  = $this->language->get('error_filename');
        }

        // Allowed file extension types
        $allowed = array('csv',
                         'xls',
                         'xlsx');

        if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
            $this->error['warning']['filetype'] = $this->language->get('error_filetype');
        }
        // Allowed file mime types
        $allowed = array('application/vnd.ms-excel',
                         'text/plain',
                         'text/csv',
                         'text/tsv');

        if (!in_array($this->request->files['fileToUploadDirectSalesBulkCSV']['type'], $allowed)) {
            $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
            // $json['error'] = $this->language->get('error_filetype');
        }

        // Check to see if any PHP files are trying to be uploaded
        $content = file_get_contents($this->request->files['fileToUploadDirectSalesBulkCSV']['tmp_name']);

        if (preg_match('/\<\?php/i', $content)) {
            $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
        }

        // Return any upload error
        if ($this->request->files['fileToUploadDirectSalesBulkCSV']['error'] != UPLOAD_ERR_OK) {
            $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUploadDirectSalesBulkCSV']['error']);
        }

        ////////1. Checking of CSV Default Code - End /////////

        //if no error in csv
        if (!$this->error) {
            ///////2. Now CSV Read here./////////
            $file_handle = fopen($this->request->files['fileToUploadDirectSalesBulkCSV']['tmp_name'], 'r');
            while (!feof($file_handle) ) {
                $line_of_text[] = fgetcsv($file_handle, 1024);
            }
            fclose($file_handle);

            ////////////3. Condition of column of CSV and Direct Sales Bulk CSV Import Only//////
            $getColumnsDirectSalesBulkCSV = array_values($line_of_text)[0];

            if ($getColumnsDirectSalesBulkCSV[0]=='Order No' && $getColumnsDirectSalesBulkCSV[1]=='Bank Ref' && $this->request->files['fileToUploadDirectSalesBulkCSV']['name'] =='Direct_Sales_Bulk_CSV.csv')
            {
                //  // // // // // 5. Conditions Others- Start// // // // // //

                // // // // // // 5(a). Check Order No Is_Numeric --- Start --- // // // // // // //
                $row = 0;
                $orderNo_arr = array_column($line_of_text,0);
                unset($orderNo_arr[0]);
                $checkOrderNo_arr = 0;
                foreach ( $orderNo_arr as $value ){
                    $row++;
                    if( !is_numeric(trim($value)) ) {
                        $checkOrderNo_arr = 1;
                        $response['status'] =  2;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                    if( strlen($value) > 1 ) {
                        $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($value);
                        if ( !$CustomerName ) {
                            $checkOrderNo_arr = 1;
                            $response['status'] =  3;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                                
                        }
                    } 
                }

                // // // 5(b). Checking Duplicate Bank Ref  // // // // //
                $bank_ref_arr = array_column($line_of_text,1);
                unset($bank_ref_arr[0]);
                $checkBankRef_arr = 0;

                if(count(array_unique($bank_ref_arr))<count($bank_ref_arr))
                {
                    // Array has duplicates
                    $checkBankRef_arr = 1;
                    $response['status'] =  4;
                    $response['row'] =  $row+1;
                    echo json_encode( $response );
                    exit();                              
                } 

                // // // 5(c). Check Bank Ref No. not blank // // // // //
                $row = 0;

                foreach ( $bank_ref_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkBankRef_arr = 1;
                        $response['status'] =  5;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();
                    }
                    //Ref No. should be in Receipt Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInReceipt = $this->model_account_panel_bankreceipt->getReceiptForRef2($value);
                        if ( !$checkRefEntryInReceipt ) {
                            $checkBankRef_arr = 1;
                            $response['status'] =  6;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                                
                        }
                    }
                    //Ref No. should not be in Receipt Sub Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInReceiptSub = $this->model_account_panel_bankreceipt->getReceiptSubForRef($value);
                        if ( $checkRefEntryInReceiptSub ) {
                            $checkBankRef_arr = 1;
                            $response['status'] =  7;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                                
                        }
                    }
                }
                $row = 0;
                if ($checkOrderNo_arr !=1 && $checkBankRef_arr !=1)
                {
                    //////5. Conditions : order No. should not be in oc_order_payment --- /////
                    $i = 0;
                    foreach($line_of_text as $cav_data){
                        if (empty($cav_data['0']) || $i ==0) {
                            $i++;
                            continue;
                        }

                        $order_no       = $cav_data['0'];
                        $bank_ref       = $cav_data['1'];

                        $getReceiptData = $this->model_account_panel_bankreceipt->getReceiptData($bank_ref);

                        $amountOfReceipt = $getReceiptData['amount'];

                        $orderDetails = $this->model_account_panel_bankreceipt->getOrderDetailForDS($order_no, $amountOfReceipt, 1);
                        
                        $row++;
                        //if ( !empty($orderDetails) ) {
                        if ($orderDetails) {
                            //$checkPayment_idQR = 1;
                            $response['status'] =  8;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();   
                        }
                    }
                    
                    //////5. Insertion Updation in Payment Sub, Payment Sub CSV tables-- Start --- /////  
                    $i = 0;
                    foreach($line_of_text as $cav_data){
                        if (empty($cav_data['0']) || $i ==0) {
                            $i++;
                            continue;
                        }

                        $order_no       = $cav_data['0'];
                        $bank_ref       = $cav_data['1'];
                        $cod_or_pg      = 'DS';

                        ////5(a). Insertion Updation in Receipt Sub -- Start --- /////
                        $getReceiptData = $this->model_account_panel_bankreceipt->getReceiptData($bank_ref);

                        ////1. Get All Values -- Start --- //////////////////
                        $ledgerid = $directSalesLedgerID;
                        $groupid = $directSalesGroupID;
                        $receipt_id = $getReceiptData['receipt_id'];
                        $amountOfReceipt = $getReceiptData['amount'];
                        $datedRO = $getReceiptData['dated'];
                        $ref = $getReceiptData['reference'];
                        $bank_name = $getReceiptData['ledger_name'];//bank name

                        $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);

                        if ( !$checkReceiptSubForReceiptID ) {
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        } else {
                            $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                            $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                            //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                            $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                            $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                        }

                        $receipt_sub_id = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptSubID($receipt_id);

                        $CustomerName = $this->model_account_panel_bankreceipt->getCustomerNameForBR($order_no);

                        $this->load->model('accounts/salesreports');
                        $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                        $order_id = $CustomerName['order_id'];

                        $ledgerNameInLedgerTable = $this->model_account_panel_bankreceipt->getLedgerNameInLedgerTable($ledgerName);

                        if ( $ledgerNameInLedgerTable == 0 ){

                            $ledger_id_new = $this->model_account_panel_bankreceipt->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);

                            $gst_number = '';
                            if (empty($CustomerName['gst_number'])) {
                                $gst_number = 'N/A';
                            }
                            else
                            {
                                $gst_number = $CustomerName['gst_number'];   
                            }

                            $this->model_account_panel_bankreceipt->updateCustomerLedger( $ledger_id_new, $CustomerName['customer_id']);

                            $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, 'SUCCESS', $bank_name, $amountOfReceipt, $datedRO, date("Y-m-d H:i:s"), 'bank_transfer', 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);

                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedRO, $ledger_id_new, $amountOfReceipt, $order_no, $ref, $pmntID, $order_id);
                        }
                        else
                        {
                            //Old Ledgers Case
                            $ledger_id_old = $this->model_account_panel_bankreceipt->getLedgerIDInLedgerTable($ledgerName);
                            
                            $pmntID = $this->model_account_panel_bankreceipt->insertOcOrderPayment( $order_id, $ref, $order_no, 'SUCCESS', $bank_name, $amountOfReceipt, $datedRO, date("Y-m-d H:i:s"), 'bank_transfer', 1, $user_id, 'receipt', $receipt_id, $receipt_sub_id);
                        
                            $sql = $this->model_account_panel_bankreceipt->InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $datedRO, $ledger_id_old, $amountOfReceipt, $order_no, $ref, $pmntID, $order_id);
                        }
                        $response['status'] =  51;
                    }
                }
            }
            else
            {
                $response['status'] =  50;
            }
        }
        echo json_encode( $response );
    }

    public function getPaymentByAmountLedgerID(){

        $this->load->model('account_panel/bankreceipt');

        // $amount = $this->request->post['amount'];
        // $ledger_id = $this->request->post['ledgerid'];
        $receipt_id = $this->request->post['receipt_id'];
        
        $getReceiptData = $this->model_account_panel_bankreceipt->getReceiptByID($receipt_id);
        
        $amount = $getReceiptData['amount'];
        $ledger_id = $getReceiptData['ledger_id'];

        $getPaymentByAmountLedgerID = $this->model_account_panel_bankreceipt->getPaymentByAmountLedgerID($amount, $ledger_id);

        $response = array();
        if ( !$getPaymentByAmountLedgerID ){
            $response['norecord'] = 0;
            $response['getPaymentByAmountLedgerID'] =  '' ;
        }
        else
        {
            $response['norecord'] = 1;
            $response['getPaymentByAmountLedgerID'] =  $getPaymentByAmountLedgerID ;
        }

        echo json_encode($response);
    }

    public function addAjaxBounce() { //xxxxxxxxxxxxxxxxxxxxxxxxxxxxx

        $response = array();

        $this->load->model('account_panel/bankreceipt');//both
        $this->load->model('account_panel/bankpayment');//both

        ////1. Get All Values -- Start --- //////////////////

        $payment_id = $this->request->post['payment_id'];        
        $receipt_id = $_REQUEST['receipt_id'];

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        //$bounceLedgerID = 372;//local ledger id
        $bounceLedgerID = 3936;//live ledger id
        $bounceGroupID = 5;

        //  // // // // // 5. Check ds_bulk entry 12122017// // // // // //
        //if it is then no updating 
        $checkDS_bulk_Entry = 0;
        $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
        
        if ( $DS_Bulk_Entry )
        {
            $checkDS_bulk_Entry = 1;
            $response['status'] =  666;
            echo json_encode( $response );
            exit();
        }

        $getPaymentData = $this->model_account_panel_bankreceipt->getPaymentByID($payment_id);
        $getReceiptData = $this->model_account_panel_bankreceipt->getReceiptByID($receipt_id);
        //echo "<pre>";print_r($getPaymentData);die;
        //echo $getReceiptData['ledger_id'];
        //echo "<pre>";print_r($getReceiptData);die;

        $response = array();
        if ( !$getPaymentData ){
            $response['status'] =  1;
            echo json_encode($response) ;
            exit();
        }
        else
        {
            if (strlen($payment_id) > 0 && is_numeric($payment_id) && strlen($receipt_id) > 0 && is_numeric($receipt_id))
            {
                ////6(a). Insertion Updation in Receipt Sub  /////
                $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 0);
                $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                if ( !$checkReceiptSubForReceiptID ) {
                   $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $bounceLedgerID, $bounceGroupID, $getReceiptData['amount'], $user_id, $datedCM, $user_array );
                } else {
                    $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                    $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                    $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                    //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                    $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                    $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                    $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                    $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $bounceLedgerID, $bounceGroupID, $getReceiptData['amount'], $user_id, $datedCM, $user_array );
                }

                ////6(a). Insertion Updation in Payment Sub  /////
                $file_path = "";
                $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                if ( !$checkPaymentSubForPaymentID ) {
                    $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $bounceLedgerID, $bounceGroupID, $getPaymentData['amount'], $file_path, $user_id, $datedCM, $user_array );
                } else {
                    //it will not enter in this section any how.
                    $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                    $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                    $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                    //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                    $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                    $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $bounceLedgerID, $bounceGroupID, $getPaymentData['amount'], $file_path, $user_id, $datedCM, $user_array );
                }

                $response['status'] =  51;
            }
            
        }
        echo json_encode( $response );
    }//end function

    public function addAjaxBounce2() {

        $response = array();

        $this->load->model('account_panel/bankreceipt');    

        ////1. Get All Values -- Start --- //////////////////
        
        $ledgerid = $this->request->post['ledgerid'];
        $groupid = $this->request->post['groupid'];
        $receipt_id = $_REQUEST['receipt_id'];
        $amountOfReceipt = $_REQUEST['amountOfReceipt'];
        $datedRO = $this->request->post['datedROAA'];

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        //$bounceLedgerID = 372;//local ledger id
        $bounceLedgerID = 3936;//live ledger id
        $bounceGroupID = 5;

        //  // // // // // 5. Check ds_bulk entry 12122017// // // // // //
        //if it is then no updating 
        $checkDS_bulk_Entry = 0;
        $DS_Bulk_Entry = $this->model_account_panel_bankreceipt->getReceiptSubCSVByReceiptIDForDS_Bulk( $receipt_id, 'DS_Bulk', 'credit_agency');
        
        if ( $DS_Bulk_Entry )
        {
            $checkDS_bulk_Entry = 1;
            $response['status'] =  666;
            echo json_encode( $response );
            exit();
        }

        //echo "<pre>";print_r($LedgerDetail);die;
        if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
            $response['status'] =  1;
        }
        else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
            $response['status'] =  1;
        }
        else if ($receipt_id == "" || strlen($receipt_id) == 0 || !is_numeric($receipt_id)) {
            $response['status'] =  1;
        }
        else if ($amountOfReceipt == "" || strlen($amountOfReceipt) == 0 || !is_numeric($amountOfReceipt)) {
            $response['status'] =  1;
        }
        else if ($datedRO == "" || strlen($datedRO) == 0) {
            $response['status'] =  1;
        }
        else
        {
            if ($groupid == $bounceGroupID)
            {
                if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($receipt_id) > 0 && is_numeric($receipt_id) && strlen($amountOfReceipt) > 0 && is_numeric($amountOfReceipt) && strlen($datedRO) > 0)
                {
                    ////6(a). Insertion Updation in Receipt Sub (Not Receipt_sub_csv table)-- Start --- /////
                    $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 0);
                    $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                    if ( !$checkReceiptSubForReceiptID ) {
                       $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                    } else {
                        $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                        $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                        $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                        //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                        $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                        $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                        $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                        $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $ledgerid, $groupid, $amountOfReceipt, $user_id, $datedCM, $user_array );
                    }

                    $response['status'] =  51;                     
                }
            }
        }
      
        echo json_encode( $response );
    }//end function

    /**
    * Public method to get membership level details
    * @return: JSON 
    * @author: Devendra, October 2018
    */
    public function getMembershipLevelDetails(): void
    {
        $membership_levels = array();
        
        $membership_obj = new Membership($this);
        
        $membership_levels = $membership_obj->getAllActiveMembershipLevels();
       
        echo json_encode($membership_levels) ;
    }
    
    /**
   * Public method to deposite membership fee
   * @return: success or failure 
   * @author: Devendra October 2018
   */
   public function depositeMembershipFee(): void
   {
       $this->load->model('account_panel/bankreceipt');
       $this->load->model('accounts/salesreports');

       $customer_id    = $this->request->post['customer_id']?? 0 ;
       $ledger_id      = $this->request->post['ledger_id']  ?? 0 ;
       $group_id       = $this->request->post['group_id']   ?? 0 ;
       $receipt_id     = $this->request->post['receipt_id'] ?? 0 ;
       $membership_id  = $this->request->post['membership_id'] ?? 0 ;
       
       if (!$customer_id || !$ledger_id || !$group_id || !$receipt_id || !$membership_id) {
         echo json_encode(
           array(
             'status' => 0,
             'error'=>'some data is missing. please refresh the page and try again.'
           )
         );   
         exit;  
       }

       $all_master_ids = Customer::getMasterIdsByCustomerIds($this->db, $customer_id);
       $master_id = $all_master_ids[0]['master_id'];
      
       $user_id = $this->user->getId();
       $user_name = $this->user->getUserName()['username'];
       $datedCM = date("Y-m-d H:i:s");
       $user_array = array(
                   'user_id' => $user_id,
                   'user_name' => $user_name,
                   'date' => $datedCM
                   );
    
        // check receipt entry in sub table entry    
       $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
       
       if ($checkReceiptSubForReceiptID) {
         echo json_encode(
           array(
             'status' => 0,
             'error'=>'Sub Receipt Membership fee deposit entry already exists.'
           )
         );
         exit;   
       }
       
       $membership_obj = new Membership($this);
       $membership_data = $membership_obj->getMembershipLevelDetail($membership_id);

       //get receipt details
       $receipt_details = $this->model_account_panel_bankreceipt->getReceiptByID( $receipt_id );
       
       if ($membership_data['membership_fees'] !== $receipt_details['amount']) {
         echo json_encode(
           array(
             'status' => 0,
             'error'=>'Membership fee amount does not matches with receipt amount.'
           )
         );
         exit;   
       }
       
       $customer_ledger = $this->model_account_panel_bankreceipt->getLedgerUsingCustomerAndGroup(
                                                                     $customer_id, 
                                                                     CUSTOMER_MEMBERSHIP_GROUP_ID
                                                                   );
       // if customer ledger does not exist, then add
       if ( empty($customer_ledger) ) {
          $ledger = $this->model_account_panel_bankreceipt->getCustomerLedgerName( $customer_id );
          // save ladger details
          $customer_ledger_id  = $this->model_account_panel_bankreceipt->saveLedger( $ledger, 
                                    CUSTOMER_MEMBERSHIP_GROUP_ID, 
                                    $customer_id, 
                                    $user_id, 
                                    $datedCM, 
                                    $user_array
                                  );
       } else {
          $customer_ledger_id = $customer_ledger['ledger_id'];
       }
            
       
        // save receipt sub ladger data for customer Membership
       $receipt_sub_id = $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( 
                                                                          $receipt_id, 
                                                                          $ledger_id, 
                                                                          $group_id, 
                                                                          $receipt_details['amount'], 
                                                                          $user_id, 
                                                                          $datedCM, 
                                                                          $user_array
                                                                      );
                                                                      
        // save receipt sub csv data for customer membership fee deposit
      $receipt_sub_csv_id = $this->model_account_panel_bankreceipt->InsertReceiptSubCSV( 
                                                                           $receipt_id, 
                                                                           $receipt_sub_id, 
                                                                           'MembershipFeeDeposit', 
                                                                           $receipt_details['dated'], 
                                                                           $customer_ledger_id, 
                                                                           $receipt_details['amount'], 
                                                                           0, 
                                                                           $receipt_details['reference'], 
                                                                           0, 
                                                                           0
                                                                        );
       
    
      $purchase_data = Customer::getTotalPurchaseAndReturnByGivenMasterId($this->db, $master_id, 90);   
      
      $expiry_date = date('Y-m-d', strtotime("+6 months", strtotime($receipt_details['dated'])));
      
      $target_amount_for_free = ($purchase_data['total_purchase'] - $purchase_data['total_return']) * 2 * $membership_data['target_for_free_factor'];
      
      $membership_obj->addCustomerMembership(
         $master_id,
         $membership_id,
         $purchase_data = array(
           'purchase_value' => $purchase_data['total_purchase'],
           'return_value' => $purchase_data['total_return'],
           'purchase_return_months' => 3,
           'target_amount_for_free' => $target_amount_for_free,
           'start_date' => $receipt_details['dated'],
           'expiry_date' => $expiry_date,
           'table_name' => 'oc_receipt_sub_csv',
           'table_id' => $receipt_sub_csv_id
         )
      );
       
      echo json_encode(
         array(
           'status' => 1,
           'success' => 'Membership fee deposit entry has been successfully saved.'
         )
      ); 
      exit;                
   }
   
   /*
   * private method to send mail, if order payment amount does not matches with 
    total advance vouchers amount for neogrowth orders.
    @params: order id, order no, payment id, new payment data, old payment data 
    @author: Devendra, Nov 2018
    */
   private function checkAndSendMailOnNeogrowthAmountMismatch(int $order_id, string $order_no, int $payment_id, array $payment_data, array $old_payment_data) {
     $advance_vouchers = AdvanceVoucherLib::getActiveAdvanceVouchersWithPayment($this->db, $order_id, $payment_id);
     
     $advance_voucher_amount = 0.0;
     
     foreach ($advance_vouchers as $advance_voucher) {
       $advance_voucher_amount += $advance_voucher['value'];
     }
     
     // if advance voucher total and new payment amount is equal then return
     if ($advance_voucher_amount == $payment_data['amount']) {
       return;
     }
     
     $body = "Advance vouchers total amount does not match with new order payment amount update by credit agent csv.<br/>
              Please see the following details: <br/>
              <ul>
                <li>Order Id: " . $order_id . "</li>
                <li>Order No: " . $order_no . "</li>
                <li>Old payment amount: " . $old_payment_data['amount'] . "</li>
                <li>Updated payment amount: " . $payment_data['amount'] . "</li>
                <li>Reciept Id: " . $payment_data['rec_pay_id'] . "</li>
                <li>Payment Id: " . $payment_id . "</li>
                <li>Advance vouchers total: " . $advance_voucher_amount . "</li>
              </ul>
              " ;

     $mail = new PHPMailer();
     $mail->isSMTP();
     $mail->Host = $this->config->get('config_mail_smtp_hostname');
     $mail->Port = $this->config->get('config_mail_smtp_port');
     $mail->SMTPSecure = 'ssl';
     $mail->SMTPAuth = true;
     $mail->Username = $this->config->get('config_mail_smtp_username');
     $mail->Password = $this->config->get('config_mail_smtp_password');

     $mail->addAddress(EMAIL_IDS['madhur']['email_id'],EMAIL_IDS['madhur']['name']);

     $mail->Subject = "Alert! advance voucher amount mismatch with credit agency csv amount for Neogrowth orders.";
     $mail->msgHTML($body);
     $mail->send(1,false);
   }
}

?>
