<?php
class ControllerAccountPanelLedger extends Controller{    
    private $error = array();

    /**
     * Method to show Account Panel -> Ledger Menu
     * Used to show Ledger and its details
     * Author: Murtaza
     */

    public function index() {

        $this->load->model('account_panel/ledger');
        
        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
  
        $this->document->setTitle('Ledger');

        //filtering
        if (isset($this->request->get['filter_ledger_name'])) {
            $filter_ledger_name = $this->request->get['filter_ledger_name'];
        } else {
            $filter_ledger_name = null;
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

        if (isset($this->request->get['filter_ledger_name'])) {
            $url .= '&filter_ledger_name=' .$this->request->get['filter_ledger_name'];
        }

        $filter_data = array(
            'filter_ledger_name'        => $filter_ledger_name,
            // 'sort'           => $sort,
            // 'order'          => $order,
            'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
            //'limit'             => $this->config->get('config_limit_admin')
            'limit'             => $filter_page_limit
        );

        $results = $this->model_account_panel_ledger->getLedgers($filter_data);

        $ledgerCount = $this->model_account_panel_ledger->getLedgersCount($filter_data);

        $data['ledgers'] = array(); 
        foreach($results as $keys => $values){
            $data['ledgers'][] = array(
                'ledger_id' => $values['ledger_id'],
                'ledger_name' => $values['ledger_name'],
                'group_id' =>$values['group_id'],
                'group_name' =>$values['group_name'],
                'opening_balance' =>$values['opening_balance'],
                'drcr' =>$values['drcr'],
                'op_bal' =>$values['op_bal'],
                'user_id' =>$values['user_id'],
                'date_created' =>$values['date_created'],
                'date_modified' =>$values['date_modified'],
                'status' =>$values['status'],
                'delete_url' => $this->url->link('account_panel/ledger/deleteLedger','token='.$this->session->data['token'].'&ledger_id='.$values['ledger_id'], 'SSL'),
            );
        }

        $groups = $this->model_account_panel_ledger->getGroups();
        $data['groups'] = $groups;

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

        $pagination = new Pagination();
        //$pagination->total = $product_total;
        $pagination->total = $ledgerCount;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        //$pagination->limit = 2;
        $pagination->url = $this->url->link('account_panel/ledger', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'],
                                    ($ledgerCount) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
                                    ((($page - 1) * $this->config->get('config_limit_admin')) > ($ledgerCount - $this->config->get('config_limit_admin'))) ? $ledgerCount : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
                                        $ledgerCount, ceil($ledgerCount / $this->config->get('config_limit_admin')));

        $data['filter_ledger_name'] = $filter_ledger_name;
        
        $data['token'] = $this->session->data['token'];
        $data['route'] = $this->request->get['route'];


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account_panel/ledger.tpl', $data));
    }

    public function addLedgerAjax() {

        $this->load->model('account_panel/ledger');
        $response = array();

            $ledgerA = trim($_REQUEST['ledgerA']);  //it used only in Ajax to update in ledger table
            $ledger = trim($_REQUEST['ledger']);
            $group_id = trim($_REQUEST['group_id']);
            $opening_balance = trim($_REQUEST['opening_balance']);
            $drcr = trim($_REQUEST['drcr']);

            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName()['username'];
            $datedCM = date("Y-m-d H:i:s");
            $user_array = array(
                                    'user_id' => $user_id,
                                    'user_name' => $user_name,
                                    'date' => $datedCM
                                  );            

            if ($ledger == "" && strlen($ledger) == 0 ) {
                $response['status'] =  3;
            }
            else if ($group_id == "" && strlen($group_id) == 0 ) {
                $response['status'] =  4;
            }
            else if($opening_balance < 0) {
                $response['status'] =  44;
            }
            else
            {
                if (isset($ledgerA) && !empty($ledgerA)) {
                    if (strlen($ledger) > 0 && strlen($group_id) > 0 && is_numeric($group_id))
                    {
                        if ($ledgerA != 1 && $ledgerA != 2 && $ledgerA != 3 && $ledgerA != 4 && $ledgerA != 5 && $ledgerA != 6 && $ledgerA != 7 && $ledgerA != 8 && $ledgerA != 9 && $ledgerA != 10 && $ledgerA != 11 && $ledgerA != 12 && $ledgerA != 13 && $ledgerA != 14 && $ledgerA != 15 && $group_id != 1 && $group_id != 2 && $group_id != 3 && $group_id != 4 && $group_id != 6 && $group_id != 7 && $group_id != 8 && $group_id != 10)
                        {
                            $result = $this->model_account_panel_ledger->updateLedgerThrGridById( $ledger, $group_id, $opening_balance, $drcr, $user_id, $datedCM, $user_array, $ledgerA );
                            $response['status'] =  2;
                        }
                        else
                        {
                            $response['status'] =  22;
                        }
                    }
                }
                else{
                    if (strlen($ledger) > 0 && strlen($group_id) > 0 && is_numeric($group_id))
                    {
                        $ledgerNameInLedgerTable = $this->model_account_panel_ledger->getLedgerNameInLedgerTable($ledger);
                        if ( $ledgerNameInLedgerTable != 0 ){
                            $response['status'] =  6;
                        }
                        else
                        {
                            if ($group_id != 1 && $group_id != 2 && $group_id != 3 && $group_id != 4 && $group_id != 7)
                            {
                                $result = $this->model_account_panel_ledger->saveLedger( $ledger, $group_id, $opening_balance, $drcr, $user_id, $datedCM, $user_array );
                                $response['status'] =  1;
                            }
                        }
                    }
                }
           }
           echo json_encode( $response );
    }

    public function getLedgerRecordThroughAjax(){
        $this->load->model('account_panel/ledger');
        $ledger_id = $_POST['ledger_id'];
        $response = array();
        $getLedgerByIdAjax = $this->model_account_panel_ledger->getLedgerByIdAjax( $ledger_id );
        $response =  $getLedgerByIdAjax; 

        /////////checking bcos if entries in below table than ledger group not to be changed at any cost - deep sense
        $countIfLedgerIdInReceipt = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_receipt"); 
        $countIfLedgerIdInReceiptSub = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_receipt_sub"); 
        $countIfLedgerIdInReceiptSubCSV = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_receipt_sub_csv"); 
        $countIfLedgerIdInReceiptSubChargesDr = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_receipt_sub_chargesdr"); 
        $countIfLedgerIdInPayment = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_payment"); 
        $countIfLedgerIdInPaymentSub = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_payment_sub"); 
        $countIfLedgerIdInPaymentSubCSV = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_payment_sub_csv"); 

        if( $countIfLedgerIdInReceipt == 0 && $countIfLedgerIdInReceiptSub == 0 && $countIfLedgerIdInReceiptSubCSV == 0 && $countIfLedgerIdInReceiptSubChargesDr == 0 && $countIfLedgerIdInPayment == 0 && $countIfLedgerIdInPaymentSub == 0 && $countIfLedgerIdInPaymentSubCSV == 0) {

            //means no entry in above tables so change in ledger group
            $response['status'] =  1;
        }
        else {

            //means entry in above tables so No change in ledger group
            $response['status'] =  2;
        }
 
        echo json_encode($response) ;
    }

    public function deleteLedger(){
        $this->load->model('account_panel/ledger');
        $response = array();

        $ledger_id = $this->request->post["ledger_id"];
        $group_id = $this->request->post["group_id"];
        
        if ($ledger_id != 1 && $ledger_id != 2 && $ledger_id != 3 && $ledger_id != 4 && $ledger_id != 5 && $ledger_id != 6 && $ledger_id != 7 && $ledger_id != 8 && $ledger_id != 9 && $ledger_id != 10 && $ledger_id != 11 && $ledger_id != 12 && $ledger_id != 13 && $ledger_id != 14 && $ledger_id != 15 && $group_id != 1 && $group_id != 2 && $group_id != 3 && $group_id != 4 && $group_id != 5 && $group_id != 6 && $group_id != 7 && $group_id != 8 && $group_id != 10)
        {
            $countIfLedgerIdInReceipt = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_receipt"); 
            $countIfLedgerIdInReceiptSub = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_receipt_sub"); 
            $countIfLedgerIdInReceiptSubCSV = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_receipt_sub_csv"); 
            $countIfLedgerIdInReceiptSubChargesDr = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_receipt_sub_chargesdr"); 
            $countIfLedgerIdInPayment = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_payment"); 
            $countIfLedgerIdInPaymentSub = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_payment_sub"); 
            $countIfLedgerIdInPaymentSubCSV = $this->model_account_panel_ledger->getLedgerIDInAllTable($ledger_id, "oc_payment_sub_csv"); 

        
            if( $countIfLedgerIdInReceipt != 0 || $countIfLedgerIdInReceiptSub != 0 || $countIfLedgerIdInReceiptSubCSV != 0 || $countIfLedgerIdInReceiptSubChargesDr != 0 || $countIfLedgerIdInPayment != 0 || $countIfLedgerIdInPaymentSub != 0 || $countIfLedgerIdInPaymentSubCSV != 0) 
            {
                $response['status'] =  1;
            }
            else {
                $result = $this->model_account_panel_ledger->deleteLedgerById( $ledger_id );

                $response['status'] =  51;
            }
        }
        else
        {
            $response['status'] =  2;
        }
        echo json_encode( $response );
    }

    public function ledgerdisplay() {

        $this->load->model('account_panel/ledger');
        
        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
        //$this->document->setTitle($this->language->get('heading_title'));     
        $this->document->setTitle('Ledger Display');
        $data['action'] = $this->url->link('account_panel/ledger/ledgerdisplay','token='.$this->session->data['token'],'SSL');

        if (isset($this->request->get['ledger_id'])) {
            $ledger_id = $this->request->get['ledger_id'];
        } else {
            $ledger_id = null;
        }              
        if (isset($this->request->get['filter_ledger_name'])) {
            $filter_ledger_name = $this->request->get['filter_ledger_name'];
        } else {
            $filter_ledger_name = null;
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
            $filter_order = "ASC";
        }
        if (isset($this->request->get['filter_ref'])) {
            $filter_ref = $this->request->get['filter_ref'];
        } else {
            $filter_ref = null;
        }
        if (isset($this->request->get['filter_order_no'])) {
            $filter_order_no = $this->request->get['filter_order_no'];
        } else {
            $filter_order_no = null;
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

        if (isset($this->request->get['ledger_id'])) {
            $url .= '&ledger_id=' .$this->request->get['ledger_id'];
        }        
        if (isset($this->request->get['filter_ledger_name'])) {
            $url .= '&filter_ledger_name=' .$this->request->get['filter_ledger_name'];
        }
        if (isset($this->request->get['filter_date_from'])) {
            $url .= '&filter_date_from=' .$this->request->get['filter_date_from'];
        }
        if (isset($this->request->get['filter_date_to'])) {
            $url .= '&filter_date_to=' .$this->request->get['filter_date_to'];
        }      
        if (isset($this->request->get['filter_order'])) {
            //$url .= $this->request->get['filter_order'];
            $url .= '&filter_order=' .$this->request->get['filter_order'];
        }
        if (isset($this->request->get['filter_ref'])) {
            $url .= '&filter_ref=' .$this->request->get['filter_ref'];
        }
        if (isset($this->request->get['filter_order_no'])) {
            $url .= '&filter_order_no=' .$this->request->get['filter_order_no'];
        }
        if (isset($this->request->get['filter_page_limit'])) {
            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
        }
        $filter_data = array(
            'ledger_id'        => $ledger_id,
            'filter_date_from'  => $filter_date_from,
            'filter_date_to'    => $filter_date_to,
            'filter_order'      => $filter_order,
            'filter_ref'        => $filter_ref,
            'filter_order_no'   => $filter_order_no,
            // 'sort'           => $sort,
            // 'order'          => $order,
            'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
            //'limit'             => $this->config->get('config_limit_admin')
            'limit'             => $filter_page_limit
        );

        $clientLedgers = $this->model_account_panel_ledger->getClientLedgerById($filter_data);
        $data['clientLedgers'] = $clientLedgers;

        $clientLedgersCount = $this->model_account_panel_ledger->getClientLedgerByIdCount($filter_data);
//echo "<pre>";print_r($clientLedgersCount);die;
        $clientLedgersOpBal = $this->model_account_panel_ledger->getClientLedgerByIdOpBal($filter_data);
        $clientLedgersClBal = $this->model_account_panel_ledger->getClientLedgerByIdClBal($filter_data);
//echo "<pre>";print_r($clientLedgersOpBal);die;
//echo "<pre>";print_r($clientLedgersClBal);die;
        $data['opBal'] = $clientLedgersOpBal['Bal'];

        //echo $data['opBal'];die;

        $data['clBal'] = $clientLedgersOpBal['Bal'] + $clientLedgersClBal['Bal'];

        $drTotal = 0;
        $crTotal = 0;
        if(isset($clientLedgers)) {
            $drTotal =  array_sum(array_map(function($clientLedgers) { 
                                        return $clientLedgers['Dr']; 
                                    }, $clientLedgers));
            $crTotal =  array_sum(array_map(function($clientLedgers) { 
                                        return $clientLedgers['Cr']; 
                                    }, $clientLedgers));
        } 
        $data['drTotal'] = $drTotal;
        $data['crTotal'] = $crTotal;
        $data['bal'] = $drTotal-$crTotal;

        $ledgers = $this->model_account_panel_ledger->getLedgers();
        $data['ledgers'] = $ledgers;

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

        $pagination = new Pagination();
        //$pagination->total = 4;
        $pagination->total = $clientLedgersCount;
        $pagination->page = $page;
        $pagination->limit = $filter_page_limit;
        $pagination->url = $this->url->link('account_panel/ledger/ledgerdisplay', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'],
                                    ($clientLedgersCount) ? (($page - 1) * $filter_page_limit) + 1 : 0,
                                    ((($page - 1) * $filter_page_limit) > ($clientLedgersCount - $filter_page_limit)) ? $clientLedgersCount : ((($page - 1) * $filter_page_limit) + $filter_page_limit),
                                        $clientLedgersCount, ceil($clientLedgersCount / $filter_page_limit));

        $data['ledger_id'] = $ledger_id;
        $data['filter_ledger_name'] = $filter_ledger_name;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        $data['filter_order'] = $filter_order;
        $data['filter_ref'] = $filter_ref;
        $data['filter_order_no'] = $filter_order_no;
        $data['filter_page_limit'] = $filter_page_limit;
        $data['page_limit_array'] = array('30','60','100','200','500','1000');

        $data['token'] = $this->session->data['token'];
        $data['route'] = $this->request->get['route'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account_panel/ledgerdisplay.tpl', $data));

    }
    public function getLedger(){

        $this->load->model('account_panel/ledger');
        $json = array();
        //$ledger_id = $_POST['filter_ledger_id'];
        $ledger_name = $this->request->get['filter_ledger_name'];
        $response = array();
        $getLedgerByNameAjax = $this->model_account_panel_ledger->getLedgerByNameAjax( $ledger_name );
        $results =  $getLedgerByNameAjax; 

            foreach ($results as $result) {
                $json[] = array(
                    'ledger_id'   => $result['ledger_id'],
                    'ledger_name' => strip_tags(html_entity_decode($result['ledger_name'], ENT_QUOTES, 'UTF-8'))
                );
            }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    //////////////// Ledger Detail //////////////////////////////////////////
    public function ledgerdetail() {

        $this->load->model('account_panel/ledger');
        
        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
        //$this->document->setTitle($this->language->get('heading_title'));     
        $this->document->setTitle('Ledger with Detail');
        
        $data['sku'] = "";
        $data['ledger_id'] = "";
        $data['filter_date_from'] = "";
        $data['filter_date_to'] = "";
        $data['store_sales'] = "";

        $data['order_by'] = 'purchase_id';
        $data['order'] = 'desc';
        $data['page'] = 1;

        $url = array();
        foreach ($this->request->get as $key => $value) {
             $data[$key] = $value;
             $url[] = "$key=$value";
        }

        $data['offset'] = ($data['page'] - 1) * $this->config->get('config_limit_admin');
        $url = implode('&',$url);

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        
        $data['start'] = ($page - 1) * $this->config->get('config_limit_admin');
        $data['limit'] = $this->config->get('config_limit_admin');

        $ledgers = $this->model_account_panel_ledger->getLedgers();
        $data['ledgers'] = $ledgers;
        //echo "<pre>";print_r($data);die;
        /*
        $clientLedgers = $this->model_account_panel_ledger->getClientLedgerDetailById($data);
        $data['clientLedgers'] = $clientLedgers;
        //echo "<pre>";print_r($clientLedgers);die;

        $data['records'] = array();
        foreach($clientLedgers as $key => $clientLedger){
            $data['records'][$clientLedger['trxn_utr']][] = $clientLedger;
        }

        $data['records'] = array();
        foreach($clientLedgers as $key => $clientLedger){
            $data['records'][$clientLedger['trxn_utr']][] = $this->model_account_panel_ledger->getOrderDetails($clientLedger['trxn_utr']);
        }
        */
        //echo "<pre>"; print_r($data['records']); die;

        $subOrderDetails = $this->model_account_panel_ledger->getSubOrders();
        $data['subOrderDetails'] = $subOrderDetails;

        $data['subOrders']  = array();
        /*
        if(!empty($subOrderDetails)){
            foreach ($subOrderDetails as $key => $subOrderDetail) {
                $data['subOrders'][$key]['suborder_id'] = $subOrderDetail['suborder_id'];
                $data['subOrders'][$key]['order_id'] = $subOrderDetail['order_id'];
                $data['subOrders'][$key]['order_no'] = $subOrderDetail['order_no'];
                $data['subOrders'][$key]['date_added'] = $subOrderDetail['date_added'];
                $data['subOrders'][$key]['firstname'] = $subOrderDetail['firstname'];
                $data['subOrders'][$key]['lastname'] = $subOrderDetail['lastname'];
                $data['subOrders'][$key]['payment_company'] = $subOrderDetail['payment_company'];
                $data['subOrders'][$key]['customer_id'] = $subOrderDetail['customer_id'];
                $data['subOrders'][$key]['invoiceAmt'] = AdvanceVoucherLib::getTotalSubOrderInvoiceAmount($this->db, $subOrderDetail['suborder_id']);
                $data['subOrders'][$key]['creditNoteAmt'] = '';
                $data['subOrders'][$key]['receipts'] = $this->model_account_panel_ledger->getReceiptByOrderId($subOrderDetail['order_id']);
                $data['subOrders'][$key]['payments'] = $this->model_account_panel_ledger->getPaymentByOrderId($subOrderDetail['order_id']);
            }
        }
        echo "<pre>"; print_r($data['subOrders']); die;
        */


        if(!empty($subOrderDetails)){
            foreach ($subOrderDetails as $key => $subOrderDetail) {
                //$data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']][] = $subOrderDetail;
                $data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']] = $subOrderDetail;
                $data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']]['invoiceAmt'] = AdvanceVoucherLib::getTotalSubOrderInvoiceAmount($this->db, $subOrderDetail['suborder_id']);
                $data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']]['creditNoteAmt'] = $this->model_account_panel_ledger->getCreditNoteAmtBySubOrderId($subOrderDetail['suborder_id']);
                $data['subOrders'][$subOrderDetail['order_id']]['receipts'] = $this->model_account_panel_ledger->getReceiptByOrderId($subOrderDetail['order_id']);
                $data['subOrders'][$subOrderDetail['order_id']]['payments'] = $this->model_account_panel_ledger->getPaymentByOrderId($subOrderDetail['order_id']);

                 //$invoiceAmt1 += $data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']]['invoiceAmt'];
                 //$data['subOrders'][$subOrderDetail['order_id']]['balance'] = $invoiceAmt1;

                //$data['subOrders'][$subOrderDetail['order_id']]['balance'] = ($data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']]['invoiceAmt'] - $data['subOrders'][$subOrderDetail['order_id']]['suborder'][$subOrderDetail['suborder_id']]['creditNoteAmt'] );
            }
        }

        //echo "<pre>";print_r($data['subOrders']);die;
        //echo "<pre>";print_r($data['subOrders'][$subOrderDetail['order_id']]['suborder']);die;


        //$clientLedgers = $this->model_account_panel_ledger->getClientLedgerDetailById($data);
        //$data['clientLedgers'] = $clientLedgers;


        $drTotal = 0;
        $crTotal = 0;
        /*
        if(isset($clientLedgers)) {
            $drTotal =  array_sum(array_map(function($clientLedgers) { 
                                        return $clientLedgers['Dr']; 
                                    }, $clientLedgers));
            $crTotal =  array_sum(array_map(function($clientLedgers) { 
                                        return $clientLedgers['Cr']; 
                                    }, $clientLedgers));
        } 
        $data['drTotal'] = $drTotal;
        $data['crTotal'] = $crTotal;
        */
        //echo "<pre>";print_r($clientLedgers);die;

        // Autoloading the lanugage
        $data['breadcrumbs'] = array();

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
        
        $wsb_purchase_total = 50;
        $pagination = new Pagination();
        //$pagination->total = $wsb_purchase_total;
        $pagination->total = $wsb_purchase_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('wsb_purchase/import/analysis', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($wsb_purchase_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($wsb_purchase_total - $this->config->get('config_limit_admin'))) ? $wsb_purchase_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $wsb_purchase_total, ceil($wsb_purchase_total / $this->config->get('config_limit_admin')));


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account_panel/ledgerdetail.tpl', $data));
        
    }




}

?>
