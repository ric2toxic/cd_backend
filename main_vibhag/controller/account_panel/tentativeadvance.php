<?php

include_once '../rabbitmq/task_directive_constants.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ControllerAccountPanelTentativeadvance extends Controller{    
    private $error = array();

    /**
     * Method to show Account Panel -> Tentative Advance Menu
     * Used to show Tentative Advance Entries and its details
     * Author: Murtaza
     */

    public function index() {
        $user_id = $this->user->getId();

        //$this->load->model('account_panel/bankpayment');
        $this->load->model('account_panel/tentativeadvance');

        
        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
        //$this->document->setTitle($this->language->get('heading_title'));     
        $this->document->setTitle('Tentative Advance');


        //filtering
        if (isset($this->request->get['staff_id'])) {
            $staff_id = $this->request->get['staff_id'];
        } else {
            $staff_id = null;
        }        
        if (isset($this->request->get['filter_order_no'])) {
            $filter_order_no = $this->request->get['filter_order_no'];
        } else {
            $filter_order_no = null;
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
        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = "deposited";
        }
        
        if (isset($this->request->get['filter_confirm'])) {
            $filter_confirm = $this->request->get['filter_confirm'];
        } else {
            //$filter_confirm = "101";
            $filter_confirm = "0";
        }

        if (isset($this->request->get['filter_staff'])) {
            $filter_staff = $this->request->get['filter_staff'];
        } else {
            //$filter_staff = "999999";
            $filter_staff = null;
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

        if (isset($this->request->get['filter_ref'])) {
            $filter_ref = $this->request->get['filter_ref'];
        } else {
            $filter_ref = null;
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

        if (isset($this->request->get['staff_id'])) {
            $url .= '&staff_id=' .$this->request->get['staff_id'];
        }        
        if (isset($this->request->get['filter_order_no'])) {
            $url .= '&filter_order_no=' .$this->request->get['filter_order_no'];
        }
        if(!empty($this->request->get['filter_date_from'])){
            $url .= '&filter_date_from=' .$this->request->get['filter_date_from'];
        }
        if(!empty($this->request->get['filter_date_to'])){
            $url .= '&filter_date_to=' .$this->request->get['filter_date_to'];
        }
        // if (isset($this->request->get['filter_order'])) {
        //     $url .= '&filter_order=' .$this->request->get['filter_order'];
        // }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' .$this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_confirm'])) {
            $url .= '&filter_confirm=' .$this->request->get['filter_confirm'];
        }
        if (isset($this->request->get['filter_staff'])) {
            $url .= '&filter_staff=' .$this->request->get['filter_staff'];
        }
        if (isset($this->request->get['filter_amount_from'])) {
            $url .= '&filter_amount_from=' .$this->request->get['filter_amount_from'];
        }
        if (isset($this->request->get['filter_amount_to'])) {
            $url .= '&filter_amount_to=' .$this->request->get['filter_amount_to'];
        }
        if (isset($this->request->get['filter_ref'])) {
            $url .= '&filter_ref=' .$this->request->get['filter_ref'];
        }

        
        $filter_data = array(
            'staff_id'        => $staff_id,
            'filter_order_no'        => $filter_order_no,
            //'filter_order'      => $filter_order,
            'filter_date_from'  => $filter_date_from,
            'filter_date_to'    => $filter_date_to,
            //'filter_is_deposited'    => $filter_is_deposited,
            'filter_status'    => $filter_status,
            'filter_confirm'    => $filter_confirm,
            'filter_staff'    => $filter_staff,
            'filter_amount_from'    => $filter_amount_from,
            'filter_amount_to'    => $filter_amount_to,
            'filter_ref'        => $filter_ref,
            // 'sort'           => $sort,
            // 'order'          => $order,
            'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
            //'limit'             => $this->config->get('config_limit_admin')
            'limit'             => $filter_page_limit
        );

        $results = $this->model_account_panel_tentativeadvance->getTentativeAdvances($filter_data);

        $getTentativeAdvanceCount = $this->model_account_panel_tentativeadvance->getTentativeAdvanceCount($filter_data);
//echo "<pre>";print_r($results);die;
        $data['tentativeAdvances'] = array();
        foreach($results as $keys => $values){
            $data['tentativeAdvances'][] = array(
            'tentative_advance_id' => $values['tentative_advance_id'],
            'order_id' => $values['order_id'],
            'order_no' => $values['order_no'],
            'payment_mode' => $values['payment_mode'],
            'cheque_no' => $values['cheque_no'],
            'collection_date' => $values['collection_date'],
            
            'txn_id' => $values['txn_id'],
            
            'dated' => $values['dated'],
            'amount' => $values['amount'],
            'notes' => $values['notes'],
            'order_payment_id' => $values['order_payment_id'],            
            'msgadmin' => $values['msgadmin'],
            'bank_deposited' => $values['bank_deposited'],
            'bank_deposited_image' => $values['bank_deposited_image'],
            'branch_name' => $values['branch_name'],
            'staff_id' => $values['staff_id'],
            'staff_name' => $values['staff_name'],
            'date_created' => $values['date_created'],
            'confirm' => $values['confirm'],
            'confirm_user' => $values['confirm_user'],
            'transaction_status' => $values['transaction_status'],
            'merchant_txn_id' => $values['merchant_txn_id'],
            );         
        }
//echo "<pre>";print_r($data['tentativeAdvances']);die;
        $orders = $this->model_account_panel_tentativeadvance->getOrders();
        $data['orders'] = $orders;

        $staffs = $this->model_account_panel_tentativeadvance->getStaffs();
        $data['staffs'] = $staffs;

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
        //$pagination->total = $paymentCount;
        $pagination->total = $getTentativeAdvanceCount;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        //$pagination->limit = 2;
        $pagination->url = $this->url->link('account_panel/tentativeadvance', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'],
                                    ($getTentativeAdvanceCount) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
                                    ((($page - 1) * $this->config->get('config_limit_admin')) > ($getTentativeAdvanceCount - $this->config->get('config_limit_admin'))) ? $getTentativeAdvanceCount : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
                                        $getTentativeAdvanceCount, ceil($getTentativeAdvanceCount / $this->config->get('config_limit_admin')));

        $data['staff_id'] = $staff_id;
        $data['filter_order_no'] = $filter_order_no;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        //$data['filter_is_deposited'] = $filter_is_deposited;
        $data['filter_status'] = $filter_status;
        $data['filter_confirm'] = $filter_confirm;
        $data['filter_staff'] = $filter_staff;
        $data['filter_amount_from'] = $filter_amount_from;
        $data['filter_amount_to'] = $filter_amount_to;
        $data['filter_ref'] = $filter_ref;
        //$data['filter_order'] = $filter_order;
        $data['page_limit_array'] = array('30','60','100','200','500','1000');
        $data['filter_page_limit'] = $filter_page_limit;
        

        $data['token'] = $this->session->data['token'];
        $data['route'] = $this->request->get['route'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account_panel/tentativeadvance.tpl', $data));
        
    }

//
    public function getTentativeAdvancesByOrderID(){

        $this->load->model('account_panel/tentativeadvance');

        $tentative_advance_id = $this->request->post['tentative_advance_id'];
        $msgadmin = $this->request->post['msgadmin'];
        $confirm = $this->request->post['confirm'];
        
        $request = $this->model_account_panel_tentativeadvance->getTentativeAdvancesByID( $tentative_advance_id );
        //echo "<pre>";print_r($request);die;
        $getTentativeAdvancesByOrderIDDetails = $this->model_account_panel_tentativeadvance->getTentativeAdvancesByOrderID($request['order_id']);
        //echo "<pre>";print_r($getTentativeAdvancesByOrderIDDetails);//die;

        $response = array();
        if ( !$getTentativeAdvancesByOrderIDDetails ){
            $response['norecordd'] = 0;
            $response['getTentativeAdvancesByOrderIDDetails'] =  '' ;
            echo json_encode($response) ; 
        }
        else
        {
            //echo "<pre>";print_r($getTentativeAdvancesByOrderIDDetails);die;
            $response['norecordd'] = 1;
            $response['getTentativeAdvancesByOrderIDDetails'] =  $getTentativeAdvancesByOrderIDDetails ;
            echo json_encode($response) ;            
        }
    }
//
    public function getOcOrderPaymentRecord(){

        $this->load->model('account_panel/tentativeadvance');

        $tentative_advance_id = $this->request->post['tentative_advance_id'];
        $msgadmin = $this->request->post['msgadmin'];
        $confirm = $this->request->post['confirm'];
        
        $request = $this->model_account_panel_tentativeadvance->getTentativeAdvancesByID( $tentative_advance_id );
        //echo "<pre>";print_r($request);//die;
        $getOcOrderPayment = $this->model_account_panel_tentativeadvance->getOcOrderPaymentDetail($request['order_id'], $request['amount'], 1);
        //echo "<pre>";print_r($getOcOrderPayment);//die;
        $getTentativeAdvancesByOrderIDDetails = $this->model_account_panel_tentativeadvance->getTentativeAdvancesByOrderID($request['order_id']);
        //echo "<pre>";print_r($getTentativeAdvancesByOrderIDDetails);die;

        $response = array();
        if ( !$getOcOrderPayment ){
            $response['norecord'] = 0;
            $response['getOcOrderPayment'] =  '' ;
            //echo json_encode($response);
        }
        else
        {
            $response['norecord'] = 1;
            $response['getOcOrderPayment'] =  $getOcOrderPayment ;
            //echo json_encode($response);
        }


        if ( !$getTentativeAdvancesByOrderIDDetails ){
            $response['norecord2'] = 0;
            $response['getTentativeAdvancesByOrderIDDetails'] =  '' ;
            //echo json_encode($response);
        }
        else
        {
            $response['norecord2'] = 1;
            $response['getTentativeAdvancesByOrderIDDetails'] =  $getTentativeAdvancesByOrderIDDetails ;
            //echo json_encode($response);
        }
        //echo "yes";//die;
        //echo "<pre>";print_r($response);die;

        echo json_encode($response);
    }

    public function updateTentativeAdvanceConfirm(){

        $this->load->model('account_panel/tentativeadvance');

            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName()['username'];
            $datedCM = date("Y-m-d H:i:s");
            $user_array = array(
                                    'user_id' => $user_id,
                                    'user_name' => $user_name,
                                    'date' => $datedCM
                                  );

        $tentative_advance_id = $this->request->post['tentative_advance_id'];
        $msgadmin = $this->request->post['msgadmin'];
        $confirm = $this->request->post['confirm'];

        $merchant_txn_id = $this->request->post['merchant_txn_id'];

        if( strlen($merchant_txn_id) > 0 ) {

            $request = $this->model_account_panel_tentativeadvance->getTentativeAdvancesByID( $tentative_advance_id );
            //echo "<pre>";print_r($request);die;


            if ($tentative_advance_id == "" || strlen($tentative_advance_id) == 0 || !is_numeric($tentative_advance_id)) {
                $response['status'] =  1;
            }
            else if ($confirm == "" || !is_numeric($confirm)) {
                $response['status'] =  2;
            }
            else
            {

                if (strlen($tentative_advance_id) > 0 && is_numeric($tentative_advance_id) && is_numeric($confirm))
                {
                    if ($confirm == 1)
                    {
                        $bank_transfer_mode = "";
                        if ($request['payment_mode'] == "cheque")
                        {
                            $bank_transfer_mode = "cheque_deposited";
                        }
                        else
                        {
                            $bank_transfer_mode = "instant";
                        }

                        $response = array(
                            'tentative_advance_id'        => $tentative_advance_id,
                            'bank_transfer_mode'          => $bank_transfer_mode,
                            'bank_name'                   => $request['payment_mode'],
                            'bank_amount'                 => $request['amount'],
                            'order_id'                    => $request['order_id'],
                            'order_no'                    => $request['order_no'],
                            'successfull'                 => 1,
                            'payment_date'                => $request['dated'],
                            'payment_reff_no'             => $merchant_txn_id,
                            'user_id'                     => $user_id,
                            'serialize_response'          => serialize($user_array)
                        );

                        $pmntID = OrderPayment::applyBankTransferAdvance($this, $response);

                        if ($pmntID > 0)
                        {
                            $this->model_account_panel_tentativeadvance->updateTentativeAdvanceForConfirmMsgAdminById( $confirm, $msgadmin, $pmntID, $tentative_advance_id );
                            
                            $this->curlRequestCRM($request['crm_user_id'], $tentative_advance_id, $confirm);

                            $response['status'] =  51;
                        }
                    }
                    else if ($confirm == 0 || $confirm == 2)
                    {
                        $this->model_account_panel_tentativeadvance->updateTentativeAdvanceForConfirmMsgAdminById( $confirm, $msgadmin, 0, $tentative_advance_id );

                        $this->curlRequestCRM($request['crm_user_id'], $tentative_advance_id, $confirm);

                        $response['status'] =  51;
                    }
                    else
                    {
                        $response['status'] =  333;   
                    }
                }
            }
        }
        else
        {
            $response['status'] =  3;
        }
        
        echo json_encode( $response );
    }

    public function curlRequestCRM($crm_user_id, $tentative_advance_id, $confirm){

        $sql = "SELECT id, access_token FROM users WHERE id = '" .(int)$crm_user_id . "'";

        $db_crm = new Database\DB( DBCRM_SERVERS ); 
        $resultQry = $db_crm->query($sql);

        $user_id = $resultQry->row['id'];
        $access_token = $resultQry->row['access_token'];


        $data = array('tentative_advance_id' => $tentative_advance_id,
                     'status' => $confirm,
                     'user_id' => $user_id,                     
                     'access_token' => $access_token
                    );

        // $data = json_encode($data); // commented by vikas, 2017 and curl are used in tasks.php file in rabbitmq folder at root
        

        // pr($data); die;
        //$curl_url = "https://www.wholesalebox.biz/staging/crmapi/leads/updateCollectionStatus";
        //$curl_url = $this->request->server['HTTP_HOST'] . "/crmapi/leads/updateCollectionStatus";
        //$curl_url = HTTP_SERVER. "/crmapi/leads/updateCollectionStatus";
        

        //////////////////////////////////////
        // commented by vikas, 2017 and curl are used in tasks.php file in rabbitmq folder at root
        /////////////////////////////////////

        /*$curl_url = WSBOX_CRM_URL. "crmapi/leads/updateCollectionStatus";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result  = curl_exec($ch);

        curl_close($ch);
        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $result = json_decode($result);*/

        ////////////////////////////////////
        /////// END  Commented ////////////
        //////////////////////////////////

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $queue_name = 'STAGING_GENERAL_TASKS_QUEUE';
            
        if (SITE_ENVIRONMENT == 'Production') {
           $queue_name = 'GENERAL_TASKS_QUEUE';
        }

        // third parameter is for queue durability. we set it to true
        // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
        // passive - false ; exclusive - false; auto-delete - false
        $channel->queue_declare($queue_name, false, true, false, false);

        
        $data = array(
                        'constant_value' => unserialize(TENTATIVEADVANCE),
                        'data_array' => $data
                    );
        $queue_object = base64_encode(serialize($data));

        // delivery_mode = 2 makes message persistent (durable)
        $msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
        $channel->basic_publish($msg, '', $queue_name); // send to sms_queue

        $channel->close();
        $connection->close();
    }




    public function updateTentativeAdvanceConfirm2(){

        $this->load->model('account_panel/tentativeadvance');

            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName()['username'];
            $datedCM = date("Y-m-d H:i:s");
            $user_array = array(
                                    'user_id' => $user_id,
                                    'user_name' => $user_name,
                                    'date' => $datedCM
                                  );

        $tentative_advance_id = $this->request->post['tentative_advance_id'];
        $msgadmin = $this->request->post['msgadmin'];
        $confirm = $this->request->post['confirm'];

        $pymtID = $this->request->post['payment_id'];

        $merchant_txn_id = $this->request->post['merchant_txn_id'];
        
        if( strlen($merchant_txn_id) > 0 ) {
            $request = $this->model_account_panel_tentativeadvance->getTentativeAdvancesByID( $tentative_advance_id );
            //echo "<pre>";print_r($request);die;

            if ($pymtID == "" || strlen($pymtID) == 0 || !is_numeric($pymtID)) {
                $response['status'] =  11;
            }
            else if ($tentative_advance_id == "" || strlen($tentative_advance_id) == 0 || !is_numeric($tentative_advance_id)) {
                $response['status'] =  1;
            }
            else if ($confirm == "" || !is_numeric($confirm)) {
                $response['status'] =  2;
            }
            else
            {
                if (strlen($pymtID) > 0 && is_numeric($pymtID) && strlen($tentative_advance_id) > 0 && is_numeric($tentative_advance_id) && is_numeric($confirm))
                {
                    if ($confirm == 1)
                    {

                        //$getOcOrderPayment = $this->model_account_panel_tentativeadvance->getOcOrderPaymentDetail($pymtID);
                        if ($request['payment_mode'] == 'cheque')
                        {
                            $this->model_account_panel_tentativeadvance->updateOcOrderPayment($pymtID, $merchant_txn_id);
                        }
                        else if ($request['payment_mode'] == 'cash')
                        {
                            
                            $responses = array(
                                'payment_id'                  => $pymtID,
                                'payment_reff_no'             => $merchant_txn_id,
                                'bank_name'                   => 'bank_transfer',
                                'bank_amount'                 => $request['amount'],
                                'payment_date'                => $request['dated'],
                                'user_id'                     => $user_id
                            );
                            OrderPayment::updateCashAdvanceToBankTransfer($this, $responses);
                        }

                        $this->model_account_panel_tentativeadvance->updateTentativeAdvanceForConfirmMsgAdminById( $confirm, $msgadmin, $pymtID, $tentative_advance_id );
                        
                        //CRM code curl request -- start//
                        $this->curlRequestCRM($request['crm_user_id'], $tentative_advance_id, $confirm);  
                        //CRM code curl request -- end//

                        $response['status'] =  51;
                    }
                }
            }
        }
        else
        {
            $response['status'] =  3;
        }

        echo json_encode( $response );
    }




}

?>
