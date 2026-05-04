<?php
/**
 * Author: Devendra Dhayal
 * Date: 2/1/18
 * Time: 12:32 PM
 */

    class ControllerRestapiSeller extends Controller{

        private $_obj_order_stores;
        private $_record_limits = 20;
        private $_obj_returns;

        public function __construct($registry)
        {
            parent::__construct($registry);
            $this->registry = $registry;

            $this->_validateRequest();

            $this->_obj_order_stores = new OrderStores();
            $this->_obj_returns = new Returns();

            $product = new Product($this->registry);
            $this->registry->set('product', $product);

            // Currency
            $this->registry->set('currency', new Currency($this->registry));
            
            // SecureFileDownload                                        
            $this->registry->set('securefiledownload', new SecureFileDownload($this->registry));

        }

        // validate the user
        private function _validateRequest() {
            // check for expected request type
            if ($this->request->server['REQUEST_METHOD'] != 'POST') {
                $response = array(
                    'error_code' => '1',
                    'status' => '0',
                    'status_text' => 'Failed. Invalid Request Type: '.$this->request->server['REQUEST_METHOD'],
                    'message' => 'Request type not accepted.'
                );

                echo json_encode($response);
                exit;
            }

            // fetch the post data
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
            // check for access token
            if (!isset($request['access_token'])) {
                $response = array(
                    'error_code' => '2',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Request does not have access token.'
                );

                echo json_encode($response);
                exit;
            }

            // check for user id
            if (!isset($request['user_id'])) {
                $response = array(
                    'error_code' => '3',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Request does not have user id.'
                );

                echo json_encode($response);
                exit;
            }

            // authenticate user
            $this->load->model('restapi/service');
            $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$request['access_token'], (int)$request['user_id']);

            if ($check_access_token == 0) {
                $response = array(
                    'error_code' => '-1',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Authentication failed!!'
                );

                echo json_encode($response);
                exit;
            }
        }

        /**
         * Function : getPickUpRequestedOrders
         * Type : Post
         * Request Parameters : user_id,access_token,filters
         * @author Devendra Dhayal
         */
        public function getPickUpRequestedOrders(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $data = array();
            $data['seller_id'] 	= $request['user_id'];

            if (!empty($request['filters'])) {
                $data['filters'] = $request['filters'];
            } else {
                $data['filters'] = array(
                    'order_no'          => '',
                    'date_from'         => '',
                    'date_to'           => '',
                    'amount_from'       => '',
                    'amount_to'         => '',
                    'edit_type_status'  => 'all'
                );
            }

            $data['sortByTypes'] = array(
                'order_processing_date_time-ASC' => 'Oldest to Newest',
                'order_processing_date_time-DESC' => 'Newest to Oldest',
            );

            $data['invoiceTypes'] = array(
                'all' => 'All',
                'pending' => 'Pending',
                'invoiced' => 'Invoiced',
                'partial' => 'Partial',
                'cancelled' => 'Cancelled'
            );

            if (!empty($request['filters']['order_no'])) {
                $filter_order_no_requested = $request['filters']['order_no'];
            } else {
                $filter_order_no_requested = NULL;
            }

            if (!empty($request['filters']['date_from'])) {
                $filter_order_processing_date_from = date('Y-m-d' , strtotime($request['filters']['date_from']));
            } else {
                $filter_order_processing_date_from = NULL;
            }

            if (!empty($request['filters']['date_to'])) {
                $filter_order_processing_date_to = date('Y-m-d' , strtotime($request['filters']['date_to']));
            } else {
                $filter_order_processing_date_to = NULL;
            }

            if (!empty($request['filters']['amount_from'])) {
                $filter_order_amount_from = $request['filters']['amount_from'];
            } else {
                $filter_order_amount_from = NULL;
            }

            if (!empty($request['filters']['amount_to'])) {
                $filter_order_amount_to = $request['filters']['amount_to'];
            } else {
                $filter_order_amount_to = NULL;
            }

            if (!empty($request['filters']['edit_type_status']) && ($request['filters']['edit_type_status'] != 'all')) {
                $edit_type_status = $request['filters']['edit_type_status'];
            } else {
                $edit_type_status = null;
            }

            if ( !empty($request['page']) ) {
                $page = $request['page'];
            } else {
                $page = 1;
            }

            if(strtotime($filter_order_processing_date_from) > strtotime($filter_order_processing_date_to)) {
                $response = array(
                    'error_code' => '1000',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'From date should be less then To date !!'
                );

                echo json_encode($response);
                exit;
            }

            if($filter_order_amount_from > $filter_order_amount_to) {
                $response = array(
                    'error_code' => '1',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'From price should be less then To price !!'
                );

                echo json_encode($response);
                exit;
            }

            $data['page'] = $page;

            $filter_record_range = $this->_record_limits;

            $data['records_per_page'] = $filter_record_range;

            if (isset($request['sort_by'])) {
                $data['sort_by'] = $request['sort_by'];
                $temp_data = explode('-', $request['sort_by']);
                $sort = $temp_data[0];
                $order = $temp_data[1];
            } else {
                $data['sort_by'] = 'order_processing_date_time-ASC';
                $sort = 'order_processing_date_time';
                $order = 'ASC';
            }


            $filter_data = array(
                'filter_order_no_requested' 	   => $filter_order_no_requested,
                'filter_order_processing_date_from'=> $filter_order_processing_date_from,
                'filter_order_processing_date_to'  => $filter_order_processing_date_to,
                'filter_order_amount_from' 	 	   => $filter_order_amount_from,
                'filter_order_amount_to' 	 	   => $filter_order_amount_to,
                'edit_type_status' 	 	   		   => $edit_type_status,
                'sort'                 	 		   => $sort,
                'order'                	 		   => $order,
                'start'                	 		   => ($page - 1) * $filter_record_range,
                'limit'                	 		   => $filter_record_range
            );

            $seller_id = $data['seller_id'];

            $getPickpupOrderRequested = $this->_obj_order_stores->getPickpupOrderRequested( $this->db, $seller_id, $filter_data);

            $total_results = $this->_obj_order_stores->getTotalOrderPickpupRequested( $this->db, $seller_id, $filter_data);

            $data['total_records'] = $total_results;

            $data['total_pages'] = ceil($total_results/$this->_record_limits);

            $data['pickup_requested'] = array();

            if (!empty($getPickpupOrderRequested)) {
                foreach ($getPickpupOrderRequested as $order_id_key => $orders_data) {
                    foreach ($orders_data as $pickuporder_value) {
                        //*****************************************//
                        // Date Range (create Invoice date range) //
                        //***************************************//
                        $ord_prcesing_date = date('d-m-Y',strtotime($pickuporder_value['order_processing_date']));
                        $current_date = date('d-m-Y');
                        $before_two_days = date('d-m-Y', strtotime('-2 days', strtotime($current_date)));
                        $date_array = array($ord_prcesing_date,$before_two_days);
                        $max_date = max(array_map('strtotime', $date_array));
                        $start_date = date('d-m-Y',$max_date);

                        $begin = new DateTime($start_date);
                        $end = new DateTime($current_date);
                        $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
                        $date_ranges = array();
                        foreach($daterange as $date){
                            $date_ranges[] = $date->format("d F Y");
                        }
                        $date_ranges[] = date('d F Y', strtotime($current_date));
                        //******************//
                        // End Date Range  //
                        //****************//

                        $processing_date = date("d-m-Y", strtotime($pickuporder_value['order_processing_date']));

                        $data['orders'][] = array(
                            'title' => 'Order No: '.$pickuporder_value['order_no'],
                            'order_id' 				=> $pickuporder_value['order_id'],
                            'suborder_id'        	=> $pickuporder_value['suborder_id'],
                            'order_no' 				=> $pickuporder_value['order_no'],
                            'order_processing_date'	=> $processing_date, //$order_processing_date,
                            'date_ranges'			=> $date_ranges,
                            'total' 				=> $this->currency->format( $pickuporder_value['total'],
                                'INR',
                                1
                            ),
                            'edit_type_status'		=> $pickuporder_value['edit_type_status']
                        );
                    }
                }
            }

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

           echo json_encode($data);
           exit;
        }

        /**
         * Function : _getPickUpOrder (private)
         * Parameters : $order_id,$suborder_id,$seller_id
         * @author Devendra Dhayal
         */
        private function _getPickUpOrder($order_id, $suborder_id, $seller_id){

            $order_processing_date = $this->_obj_order_stores->getSubOrderProcessingDate($this->db, $suborder_id);

            //*****************************************//
            // Date Range (create Invoice date range) //
            //***************************************//
            $ord_prcesing_date = date('d-m-Y',strtotime($order_processing_date));
            $current_date = date('d-m-Y');
            $before_two_days = date('d-m-Y', strtotime('-2 days', strtotime($current_date)));
            $date_array = array($ord_prcesing_date,$before_two_days);
            $max_date = max(array_map('strtotime', $date_array));
            $start_date = date('d-m-Y',$max_date);

            $begin = new DateTime($start_date);
            $end = new DateTime($current_date);
            $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
            $date_ranges = array();
            foreach($daterange as $date){
                $date_ranges[] = $date->format("d/m/Y");
            }
            $date_ranges[] = date('d/m/Y', strtotime($current_date));
            //******************//
            // End Date Range  //
            //****************//

            $product_data = array();
            $product_tabs = array();

            // get pending products
            $products = $this->_obj_order_stores->getOrderProducts(
                            $this,
                            $order_id,
                            $suborder_id,
                            $seller_id, array('YES',
                                'SELLER_LATER_DISPATCH'
                            )
                        );
            if( !empty($products) ){
                foreach ( $products[$suborder_id] as $key => $product ){
                  $product['is_associate'] = false;
                  if (!empty($product['sibling_associates'])) {
                    $product['is_associate'] = true;
                    foreach ($product['sibling_associates'] as $order_product_value) {
                      $product['combo_products'][] = $products[$suborder_id][$order_product_value];
                    }
                  }
                    $product_data['pending']['products'][] = $product;
                }
                $product_data['pending']['total_products'] = sizeof($product_data['pending']['products']);
                $product_data['pending']['min_action_required'] = $product_data['pending']['total_products'];

                $product_tabs['pending'] = 'PENDING';
            }

            // get seller not given products
            $products = $this->_obj_order_stores->getOrderProducts(
                            $this,
                            $order_id,
                            $suborder_id,
                            $seller_id, array('SELLER_NOT_SUPPLIED')
                        );
            if( !empty($products) ){
                foreach ( $products[$suborder_id] as $key => $product ){
                  $product['is_associate'] = false;
                  if (!empty($product['sibling_associates'])) {
                    $product['is_associate'] = true;
                    foreach ($product['sibling_associates'] as $order_product_value) {
                      $product['combo_products'][] = $products[$suborder_id][$order_product_value];
                    }
                  }
                    $product_data['seller_not_given']['products'][] = $product;
                }
                $product_data['seller_not_given']['total_products'] = sizeof($product_data['seller_not_given']['products']);
                $product_data['seller_not_given']['min_action_required'] = $product_data['seller_not_given']['total_products'];

                $product_tabs['seller_not_given'] = 'GOODS NOT GIVEN';
            }

            // get invoiced products
            $invoices = $this->_obj_order_stores->getSellerInvoiceGenerated(
                            $this->registry,
                            $this,
                            $order_id,
                            $suborder_id,
                            $seller_id
                        );
            if( !empty($invoices) ){
                foreach ( $invoices as $key => $invoice ) {
                    $product_data[$invoice['invoice_no']] = $invoice;
                    $product_data[$invoice['invoice_no']]['date'] = date('d/m/Y', strtotime($invoice['date']));
                    $product_data[$invoice['invoice_no']]['seller_invoice_id'] = $key;
                    $invoice_amount = 0;
                    $total_products = 0;

                    foreach ($invoice['products_data'][$suborder_id] as $key => $product) {
                      $product['is_associate'] = false;
                      if (!empty($product['sibling_associates'])) {
                        $product['is_associate'] = true;
                        foreach ($product['sibling_associates'] as $order_product_value) {
                          $product['combo_products'][] = $invoice['products_data'][$suborder_id][$order_product_value];
                        }
                      }
                        $product_data[$invoice['invoice_no']]['products'][] = $product;
                        $invoice_amount += $product['amount_per_piece'] * $product['no_of_piece'];
                        if ($product['pickup_status'] != 'Picked_Up' && $product['pickup_status'] != 'Received') $total_products += 1;
                    }
                    $product_data[$invoice['invoice_no']]['total_products'] = sizeof($product_data[$invoice['invoice_no']]['products']);

                    // min_action_required key is used for invoice change
                    $product_data[$invoice['invoice_no']]['min_action_required'] = $product_data[$invoice['invoice_no']]['total_products'];

                    if ($total_products == 0) {
                        $product_data[$invoice['invoice_no']]['edit_invoice_available'] = false;
                    }
                    $product_data[$invoice['invoice_no']]['invoice_amount'] = $this->currency->format($invoice_amount, 'INR', 1);

                    unset($product_data[$invoice['invoice_no']]['products_data']);
                    $product_tabs[$invoice['invoice_no']] = $invoice['invoice_no'];
                }
            }

            $processing_date = date("d-m-Y", strtotime($order_processing_date));

            $data['order_detail'] = array(
                'order_id' 				=> $order_id,
                'suborder_id'        	=> $suborder_id,
                'order_processing_date'	=> $processing_date, //$order_processing_date,
                'date_ranges'			=> $date_ranges,
                'product_tabs'		    => $product_tabs,
                'data'                  => $product_data
            );

            return $data;
        }

        /**
         * Function : getPickUpOrderDetail
         * Type : Post
         * Request Parameters : user_id,access_token,order_id, suborder_id, tab
         * @author Devendra Dhayal
         */
        public function getPickUpOrderDetail(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $seller_id = $request['user_id'];
            $order_id = $request['order_id'];
            $suborder_id = $request['suborder_id'];

            $data = $this->_getPickUpOrder($order_id, $suborder_id, $seller_id);

            if(!empty($request['tab']) && isset($data['order_detail']['product_tabs'][$request['tab']])){
                $data['order_detail']['tab'] = $request['tab'];
            } else {
                reset($data['order_detail']['product_tabs']);
                $data['order_detail']['tab'] = key($data['order_detail']['product_tabs']);
            }

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /**
         * Function : splitOrderProducts
         * Type : Post
         * Request Parameters : user_id,access_token,order_id, suborder_id, changes, invoice_number, invoice_date
         * @author Devendra Dhayal
         */
        public function splitOrderProducts(){

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $order_id 	= $request['order_id'];
            $suborder_id= $request['suborder_id'];
            $changes 	= $request['changes'];
            $seller_id 	= $request['user_id'];

            $invoice_no = trim($request['invoice_number']);
            $invoice_date = $request['invoice_date'];


            // echo $invoice_no.match('/^[A-Za-z0-9/-]{1,16}$/');
            if(!preg_match("/^[A-Za-z0-9\/-]{1,16}$/", $invoice_no, $output_array)){
                $response = array(
                    'error_code' => '1000',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Invalid Invoice number format !! Invoice number can only be 16 characters long; can contain either alphabets (a-z, A-Z), digits (0-9), hyphen (-), and/or forward slash (/).'
                );
                echo json_encode($response);
                exit;
            }

            $invoice_no_allowed = $this->_obj_order_stores->checkSellerInvoiceNoIsUnique( $this->db, $seller_id , $invoice_no );
            if( !$invoice_no_allowed ){
                $response = array(
                    'error_code' => '1001',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'This invoice number is already used! Please enter new invoice number.'
                );
                echo json_encode($response);
                exit;
            }

            $nick_name = SellerInfo::getSellerFirmDetails($this->db, $seller_id)['nickname'];

            $order_product_ids_array = array();
            foreach( $changes as $order_product_id_key => $values ){
                $order_product_ids_array[] = $order_product_id_key;
            }

            $checkOrderProductIds = $this->_obj_order_stores->checkOrderProductIdsIsAvailableOrNot( $this->db, $order_product_ids_array );

            if(!$checkOrderProductIds){
                $response = array(
                    'error_code' => '1003',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Something went wrong. Please try again!'
                );
                echo json_encode($response);
                exit;
            }

            $send_notification = false;

            // check wether partail edit available or not
            foreach( $changes as $order_product_id_key => $values ) {
                if ($values['status'] == 'SELLER_PARTIAL') {
                    $is_partial_edit_available = $this->_obj_order_stores->checkPartialOrder($this, $values['product_id'], $order_product_id_key);
                    if (!$is_partial_edit_available) {
                        $response = array(
                            'error_code' => '1003',
                            'status' => '0',
                            'status_text' => 'Failed',
                            'message' => 'Partial Edit is not available for some products.'
                        );
                        echo json_encode($response);
                        exit;
                    }
                }

                if (!$send_notification && in_array($values['status'], array('SELLER_PARTIAL', 'SELLER_NOT_SUPPLIED', 'SELLER_LATER_DISPATCH'))) {
                    $send_notification = true;
                }
            }

            $obj = new SellerInvoice($this->registry);
            $file_name = $obj->generateInvoiceNo($order_id, $suborder_id, $seller_id, $invoice_no, $invoice_date);

            if ( !empty($file_name)
                && isset($file_name['seller_invoice_id'])
                && !empty((int)$file_name['seller_invoice_id']) ) {

                $seller_invoice_id = (int)$file_name['seller_invoice_id'];

                // $order_product_id_array = array();
                foreach( $changes as $order_product_id_key => $values ){
                    // $order_product_id_array[] = $order_product_id_key;

                    $edit_history = array(
                        'user_id' 	=> $seller_id,
                        'user_name' 	=> $nick_name,
                        'user_type' => 'seller',
                        'user_ip'	=> $this->request->getIpAddress,
                        'user_agent'=> $_SERVER['HTTP_USER_AGENT'],
                        'date_added'=> date('d-m-Y H:i:s'),
                        'comment' 	=> $values['status']
                    );

                    $edit_type = array(
                        $values['status'] => array(
                            "value" => $values['value'],
                            "product_id" => $values['product_id']
                        )
                    );

                    $details = array(
                        'edit_type' 	=> $edit_type,
                        'edit_history' 	=> $edit_history

                    );

                    if(in_array($values['status'], array('SELLER_APPROVED'))){
                        $this->_obj_order_stores->updateEditTypeBySeller( $this->db, $order_product_id_key, $details, $seller_id, $seller_invoice_id);

                    } else if(in_array($values['status'], array('SELLER_NOT_SUPPLIED','SELLER_LATER_DISPATCH'))){
                        $this->_obj_order_stores->updateEditTypeBySeller( $this->db, $order_product_id_key, $details, $seller_id, '');

                    } else if(in_array($values['status'], array('SELLER_PARTIAL'))) {

                        $edit_history['comment'] = 'SELLER_NOT_SUPPLIED';
                        $details = array(
                            'edit_type' 	=> 'SELLER_NOT_SUPPLIED',
                            'edit_history' 	=> $edit_history
                        );

                        $details['quantity'] = $values['value'];
                        //Code changes by Nilesh as per new requirement - for generic function splitOrderProduct will split accordingly
                        $details['old_edit_type'] = 'SELLER_PARTIAL';

                        OrderEdit::splitOrderProduct( $this->db, $order_product_id_key, $details, $seller_invoice_id);
                    }
                }
                
                //Update order and suborder total
                OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $suborder_id);

                if ($send_notification) {
                    $this->_obj_order_stores->SendNotificationToSalesStaff($this, $order_id);
                }

            } else {
                $response = array(
                    'error_code' => '1003',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Something went wrong and invoice could not be generated. Please try again after some time!'
                );
                echo json_encode($response);
                exit;
            }

            $data = $this->_getPickUpOrder($order_id, $suborder_id, $seller_id);

            reset($data['order_detail']['product_tabs']);
            $data['order_detail']['tab'] = key($data['order_detail']['product_tabs']);

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;

        }

        /**
         * Function : sellerEditedAfterGeneratingInvoice
         * Type : Post
         * Request Parameters : user_id,access_token,order_id, suborder_id, changes, invoice_number, invoice_date, seller_invoice_id
         * @author Devendra Dhayal
         */
        public function sellerEditedAfterGeneratingInvoice(){

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $order_id 	= $request['order_id'];
            $suborder_id= $request['suborder_id'];
            $changes 	= $request['changes'];
            $seller_id 	= $request['user_id'];
            $invoice_no = trim($request['invoice_number']);
            $invoice_date = $request['invoice_date'];
            $sllr_inv_id = $request['seller_invoice_id'];

            $nick_name = SellerInfo::getSellerFirmDetails($this->db, $seller_id)['nickname'];

            if(!empty($changes)){

                $updateInvoiceData = array(
                    'invoice_no'  => $invoice_no,
                    'invoice_date'=> $invoice_date,
                    'sllr_inv_id' => $sllr_inv_id,
                    'seller_id'	  => $seller_id
                );

                $invoice_no_allowed = $this->_obj_order_stores->sellerUpdateInvoiceNoAndDateAfterGeneratingInvoice($this->db, $updateInvoiceData );
                if( !$invoice_no_allowed ){
                    $response = array(
                        'error_code' => '1000',
                        'status' => '0',
                        'status_text' => 'Failed',
                        'message' => 'This invoice number is already used! Please enter new invoice number.'
                    );
                    echo json_encode($response);
                    exit;
                }

                $send_notification = false;

                // check wether partail edit available or not
                foreach( $changes as $order_product_id_key => $values ) {
                    if ($values['status'] == 'SELLER_PARTIAL') {
                        $is_partial_edit_available = $this->_obj_order_stores->checkPartialOrder($this, $values['product_id'], $order_product_id_key);
                        if (!$is_partial_edit_available) {
                            $response = array(
                                'error_code' => '1003',
                                'status' => '0',
                                'status_text' => 'Failed',
                                'message' => 'Partial Edit is not available for some products.'
                            );
                            echo json_encode($response);
                            exit;
                        }
                    }

                    if (!$send_notification && in_array($values['status'], array('SELLER_PARTIAL', 'SELLER_NOT_SUPPLIED', 'SELLER_LATER_DISPATCH'))) {
                        $send_notification = true;
                    }
                }

                foreach( $changes as $order_product_id_key => $values ){

                    $edit_history = array(
                        'user_id' 	=> $seller_id,
                        'user_name' 	=> $nick_name,
                        'user_type' => 'seller',
                        'user_ip'	=> $this->request->getIpAddress,
                        'user_agent'=> $_SERVER['HTTP_USER_AGENT'],
                        'date_added'=> date('d-m-Y H:i:s'),
                        'comment' 	=> $values['status'],
                        'edited' 	=> 'seller_edited_after_generating_invoice'
                    );

                    $edit_type = array(
                        $values['status'] => array(
                            "value" => (int)$values['value'],
                            "product_id" => $values['product_id'],
                            "seller_invoice_id" => $sllr_inv_id
                        )
                    );

                    $details = array(
                        'edit_type' 	=> $edit_type,
                        'edit_history' 	=> $edit_history

                    );

                    $this->_obj_order_stores->sellerEditedAfterGeneratingInvoice( $this->db,
                        $order_id,
                        $suborder_id,
                        $order_product_id_key,
                        $details, $seller_id
                    );
                }
                
                //Update order and suborder total
                OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $suborder_id);

                // update seller invoice according to no of products in this invoice
                $this->_obj_order_stores->updateSellerInvoiceForNoGoods($this->db, $updateInvoiceData );

                if ($send_notification) {
                    $this->_obj_order_stores->SendNotificationToSalesStaff($this, $order_id);
                }

                $data = $this->_getPickUpOrder($order_id, $suborder_id, $seller_id);

                reset($data['order_detail']['product_tabs']);
                $data['order_detail']['tab'] = key($data['order_detail']['product_tabs']);

                $data['status'] = '1';
                $data['status_text'] = 'Success';
                $data['message'] = 'Success';

                echo json_encode($data);
                exit;

            } else {
                $response = array(
                    'error_code' => '1001',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Please select any actions!'
                );
                echo json_encode($response);
                exit;
            }
        }

        /**
         * Function : revertGoodsBySeller
         * Type : Post
         * Request Parameters : user_id,access_token,order_id, suborder_id, changes
         * @author Devendra Dhayal
         */
        public function revertGoodsBySeller(){

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $changes = $request['changes'];
            $order_product_ids = array();
            foreach ($changes as $order_product_id => $value) {
                if ($value['status'] == 1) {
                    $order_product_ids[] = $order_product_id;
                }
            }
            $seller_id = $request['user_id'];
            $order_id = $request['order_id'];
            $suborder_id = $request['suborder_id'];
            $return_value = $this->_obj_order_stores->revertGoodsBySeller($this->db, $order_product_ids, $seller_id, $order_id, $suborder_id);

            if($return_value == 1){
                $response = array(
                    'error_code' => '1003',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Dear Seller, You cannot revert now. Order has been invoiced to customer already, hence marked closed.'
                );
                echo json_encode($response);
                exit;
            }

            $data = $this->_getPickUpOrder($order_id, $suborder_id, $seller_id);

            reset($data['order_detail']['product_tabs']);
            $data['order_detail']['tab'] = key($data['order_detail']['product_tabs']);

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /**
         * Function : setMarkOutOfStock
         * Type : Post
         * Request Parameters : user_id,access_token,order_id, suborder_id, changes
         * @author Devendra Dhayal
         */
        public function setMarkOutOfStock(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $requestData = array();

            $requestData['order_id'] 	= $request['order_id'];
            $requestData['suborder_id'] = $request['suborder_id'];
            $changes 	= $request['changes'];
            $requestData['seller_id'] 	= $request['user_id'];

            $product_ids_array = array();
            $order_product_ids_array = array();
            foreach( $changes as $order_product_id_key => $values ){
                $product_ids_array[] = $values['product_id'];
                $order_product_ids_array[] = $order_product_id_key;
            }

            $requestData['product_ids'] = $product_ids_array;
            $requestData['order_product_ids'] = $order_product_ids_array;

            $this->_obj_order_stores->setMarkOutOfStock( $this->db, $requestData, $requestData['seller_id'] );

            $this->_obj_order_stores->SendNotificationToSalesStaff($this, $request['order_id']);

            $data = $this->_getPickUpOrder($requestData['order_id'], $requestData['suborder_id'], $requestData['seller_id']);

            reset($data['order_detail']['product_tabs']);
            $data['order_detail']['tab'] = key($data['order_detail']['product_tabs']);

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /**
         * Function : get Pickup done orders
         * Type : Post
         * Request Parameters : user_id,access_token,filters
         * @author Devendra Dhayal
         */

        public function getPickUpDoneOrders(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $data = array();
            $data['seller_id'] 	= $request['user_id'];

            $seller_id = $request['user_id'];
            $access_token = $request['access_token'];

            $data['getQuarters'] = getQuarters();
            $data['getFinancialYears'] = getFinancialYears(2016, '');

            if (!empty($request['filters'])) {
                $data['filters'] = $request['filters'];
            } else {
                $data['filters'] = array(
                    'order_invoice'       => '',
                    'invoice_date_from'   => '',
                    'invoice_date_to'     => '',
                    'sale_from'           => '',
                    'sale_to'             => '',
                    'month_range'         => '',
                    'year_range'          => '',
                    'payment_status'      => '',
                    'sor_record'          => 'all'
                );
            }

            $data['orderTypes'] = array(
                'all' => 'All Orders',
                'withoutsor' => 'Online Orders',
                'onlysor' => 'SOR Orders'
            );

            $data['paymentStatus'] = array(
                'paid' => 'Paid',
                'un_paid' => 'Un Paid'
            );

            $data['sortByTypes'] = array(
                'o.order_id-ASC' => 'Oldest to Newest',
                'o.order_id-DESC' => 'Newest to Oldest',
            );

            $data['date_ranges'] = array(
                'Q1' => array('start' => '01-04-', 'end' => '30-06-'),
                'Q2' => array('start' => '01-07-', 'end' => '30-09-'),
                'Q3' => array('start' => '01-10-', 'end' => '31-12-'),
                'Q4' => array('start' => '01-01-', 'end' => '31-03-')
            );

            if( !empty($request['filters']['order_invoice']) ){
                $filter_order_invoice = $request['filters']['order_invoice'];
            } else {
                $filter_order_invoice = NULL;
            }

            if( !empty($request['filters']['invoice_date_from']) ){
                $filter_invoice_date_from = $request['filters']['invoice_date_from'];
            } else {
                $filter_invoice_date_from = NULL;
            }

            if( !empty($request['filters']['invoice_date_to']) ){
                $filter_invoice_date_to = $request['filters']['invoice_date_to'];
            } else {
                $filter_invoice_date_to = NULL;
            }

            if( !empty($request['filters']['sale_from']) ){
                $filter_sale_from = $request['filters']['sale_from'];
            } else {
                $filter_sale_from = NULL;
            }

            if( !empty($request['filters']['sale_to']) ){
                $filter_sale_to = $request['filters']['sale_to'];
            } else {
                $filter_sale_to = NULL;
            }

            if( !empty($request['filters']['payment_status'])  ){
                $filter_payment_status = $request['filters']['payment_status'];
            } else {
                $filter_payment_status = NULL;
            }

            if ( !empty($request['filters']['sor_record']) ) {
                $filter_sor_record = $request['filters']['sor_record'];
            } else {
                $filter_sor_record = false;
            }

            $filter_record_range = $this->_record_limits;

            if (isset($request['sort_by'])) {
                $data['sort_by'] = $request['sort_by'];
                $temp_data = explode('-', $request['sort_by']);
                $sort = $temp_data[0];
                $order = $temp_data[1];
            } else {
                $data['sort_by'] = 'o.order_id-DESC';
                $sort = 'o.order_id';
                $order = 'DESC';
            }

            if ( !empty($request['page']) ) {
                $page = $request['page'];
            } else {
                $page = 1;
            }

            $data['page'] = $page;
            $data['records_per_page'] = $filter_record_range;


            $filter_data = array(
                'filter_order_invoice'      => $filter_order_invoice,
                'filter_order_no' 		 	=> NULL,
                'filter_invoice_date_from'	=> $filter_invoice_date_from,
                'filter_invoice_date_to' 	=> $filter_invoice_date_to,
                'filter_invoice_no' 	 	=> NULL,
                'filter_sale_from' 	 	 	=> $filter_sale_from,
                'filter_sale_to' 	 	 	=> $filter_sale_to,
                'filter_payment_status'     => $filter_payment_status,
                'sort'                 	 	=> $sort,
                'order'                	 	=> $order,
                'start'                	 	=> ($page - 1) * $filter_record_range,
                'limit'                	 	=> $filter_record_range,
                'download_pickup_done_report'=> false,
                'filter_sor_record'		    => $filter_sor_record
            );

            $results = $this->_obj_order_stores->getOrderPickupDone( $this->db, $seller_id, $filter_data);

            $total_results = $this->_obj_order_stores->getTotalRecordPickupDone( $this->db, $seller_id, $filter_data);

            $data['total_records'] = $total_results;

            $data['total_pages'] = ceil($total_results/$this->_record_limits);

            $data['pickup_data'] = array();

            if( !empty($results) ){
                foreach ($results['records'] as $key => $result) {
                    // calculate amount of debit note of perticular order id
                    $total_amt_debit_note = 0;
                    if(!empty($results['debit_notes'][$result['order_id']])){
                        foreach($results['debit_notes'][$result['order_id']] as $debit_note_values){
                            $total_amt_debit_note += $debit_note_values['debit_note_amount'];
                        }
                    }

                    //get seller invoice download link
                    $seller_inv_dload_link = array();
                    $check_trxn_done = false;
                    if(!empty($results['seller_invoices'][$result['order_id']])){
                        // array of seller invoice corresponding to order_id
                        $seller_invoice_data = $results['seller_invoices'][$result['order_id']];

                        foreach($seller_invoice_data as $seller_inv_id_key => $seller_inv_id_value){
                            if($seller_inv_id_value['trxn_done'] == 'BANK_REQUESTED' || $seller_inv_id_value['trxn_done'] == 'BANK_SUCCESS' || $seller_inv_id_value['trxn_done'] == 'NOT_APPLICABLE'){
                                $check_trxn_done = true;
                            }
                            $seller_invoice = array();
                            $seller_invoice['seller_invoice_id'] = (int)$seller_inv_id_value['seller_invoice_id'];
                            $seller_invoice = serialize($seller_invoice);
                            $secureFileDload = new SecureFileDownload($this->registry);
                            $seller_inv_dload_link[$seller_inv_id_key]['label'] = $seller_inv_id_value['seller_invoice_prefix'].''.$seller_inv_id_value['seller_invoice_no'];
                            $seller_inv_dload_link[$seller_inv_id_key]['url'] = $secureFileDload->getDownloadLink('seller_invoice', base64_encode($seller_invoice), false) . '&user_id='.$seller_id.'&access_token='.$access_token;
                        }
                    }

                    // get download link of debit note
                    $debit_note_url = array();

                    if( !empty($results['debit_notes'][$result['order_id']]) ){
                        $debit_note_data = $results['debit_notes'][$result['order_id']];
                        foreach ($debit_note_data as $key => $value) {
                            $encode_file = array();
                            $encode_file['order_no'] = $result["order_no"];
                            $encode_file['debit_note_id'] = $value['debit_note_id'];
                            $encode_file = base64_encode(serialize($encode_file));
                            $dload_url['label'] = $value['debit_note_prefix'].''.$value['debit_note_no'];
                            $dload_url['url'] = $this->securefiledownload->getDownloadLink('seller_debit_note',$encode_file, false). '&user_id='.$seller_id.'&access_token='.$access_token;
                            $debit_note_url[$key] = $dload_url;
                        }
                    }

                    $net_payable_amount 	= $result['sale_amt'] - $total_amt_debit_note ;
                    $sor_invoice_no 		= '';
                    $sor_invoice_date 		= '';
                    $paymentToolTip			= '';
                    $sor_payment 			= array();
                    $contain_sor            = false;

                    if (isset($result['sor_invoice_no'])) {
                        $sor_invoice_no = $result['sor_invoice_no'];
                        $sor_payment  = $this->_obj_order_stores->getSorOrderPayments($this->db, $result['order_id'], $seller_id );
                    }
                    if (isset($result['sor_invoice_date'])) {

                        $sor_invoice_date = $result['sor_invoice_date'];
                    }

                    if(!empty($sor_invoice_no)) $contain_sor = true;

                    $payment_info = array();

                    /*
                    * set the string for payment tool tip
                    */
                    if(!empty($results['seller_invoices'][$result['order_id']])){

                        foreach ($results['seller_invoices'][$result['order_id']] as $keyse => $valuese) {

                            if($valuese['trxn_done'] == 'BANK_REQUESTED' || $valuese['trxn_done'] == 'BANK_SUCCESS' || $valuese['trxn_done'] == 'NOT_APPLICABLE'){

                                //$paymentToolTip .=     '<li>Inv. No. ' . $valuese["seller_invoice_prefix"].''.$valuese["seller_invoice_no"] . '</li>';
                                //$paymentToolTip .=     '<li>Amount. ' . (($valuese["trxn_amount"]) ? $valuese["trxn_amount"] : ''). '</li>';

                                if($valuese["trxn_utr"]!='' && $valuese["trxn_utr_date"]!=''){
                                    array_push($payment_info, 'Ref. ' . (($valuese["trxn_utr"]) ? $valuese["trxn_utr"] : ''));
                                    array_push($payment_info, 'Date. ' . (($valuese["trxn_utr_date"]) ? $valuese["trxn_utr_date"] : ''));
                                }
                            }
                        }

                    }

                    if(!empty($sor_payment)){
                        foreach ($sor_payment as $keysor => $valuesor) {
                            if (($valuesor['trxn_done'] == 'BANK_REQUESTED' || $valuesor['trxn_done'] == 'BANK_SUCCESS' || $valuesor['trxn_done'] == 'NOT_APPLICABLE') && $valuesor['suborder_id'] == $result['suborder_id']) {
                                $check_trxn_done = true;
                                //$paymentToolTip .=     '<li>Sub Order. No. ' . $valuesor["suborder_id"].'</li>';
                                //$paymentToolTip .=     '<li>Amount. ' . (($valuesor["total_paid_amount"]) ? $valuesor["total_paid_amount"] : ''). '</li>';

                                if($valuesor["trxn_utr"]!='' && $valuesor["trxn_utr_date"]!=''){
                                    array_push($payment_info, 'Ref. ' . (($valuese["trxn_utr"]) ? $valuese["trxn_utr"] : ''));
                                    array_push($payment_info, 'Date. ' . (($valuese["trxn_utr_date"]) ? $valuese["trxn_utr_date"] : ''));
                                }
                            }
                        }
                    }

                    $order_date = (!empty($results['order_process_lists'][$result['order_id']]['order_processing_date']) ? $results['order_process_lists'][$result['order_id']]['order_processing_date'] : $result['order_date']);


                    /*
                    * setting all data in array
                    */
                    $data['pickup_data'][] = array(
                        'order_id' 		=> $result['order_id'],
                        'order_no' 		=> $result['order_no'],
                        'suborder_id' 		=> $result['suborder_id'],
                        'order_date_added' => date("d-m-Y", strtotime($order_date)),
                        'sale' 			=> number_format($result['sale_amt'],2),
                        'seller_invoices'=> (!empty($results['seller_invoices'][$result['order_id']]) ? $results['seller_invoices'][$result['order_id']] : array()),
                        'debit_notes'=> (!empty($results['debit_notes'][$result['order_id']]) ? $results['debit_notes'][$result['order_id']] : array()),
                        'return_amount' => number_format($total_amt_debit_note,2),
                        'penalty_amount'=> 0,
                        'net_payable_amount' => number_format($net_payable_amount,2),
                        'trxn_done'		=> ($check_trxn_done) ? 'Paid' : ((!$net_payable_amount) ? 'Full Returned' : 'Un Paid'),
                        'seller_invoice_link' => $seller_inv_dload_link,
                        'debit_note_link'=> $debit_note_url,
                        'sor_invoice_no'=>$sor_invoice_no,
                        'sor_invoice_date'=>$sor_invoice_date,
                        'contain_sor' => $contain_sor,
                        'payment_info' => $payment_info
                    );
                }
            }

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /**
         * Function : download pickup done orders report
         * Type : Post
         * Request Parameters : user_id,access_token,filters
         * @author Devendra Dhayal
         */

        public function downloadPickUpDoneOrdersReport(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $data = array();
            $data['seller_id'] 	= $request['user_id'];

            $seller_id = $request['user_id'];


            if( !empty($request['filters']['month_range']) ){
                $filter_month_range = $request['filters']['month_range'];
            } else {
                $filter_month_range = NULL;
            }

            if( !empty($request['filters']['year_range']) ){
                $filter_year_range = $request['filters']['year_range'];
            } else {
                $filter_year_range = NULL;
            }

            if( !empty($request['filters']['order_invoice']) ){
                $filter_order_invoice = $request['filters']['order_invoice'];
            } else {
                $filter_order_invoice = NULL;
            }

            if( !empty($request['filters']['invoice_date_from']) ){
                $filter_invoice_date_from = $request['filters']['invoice_date_from'];
            } else {
                $filter_invoice_date_from = NULL;
            }

            if( !empty($request['filters']['invoice_date_to']) ){
                $filter_invoice_date_to = $request['filters']['invoice_date_to'];
            } else {
                $filter_invoice_date_to = NULL;
            }

            if( !empty($request['filters']['sale_from']) ){
                $filter_sale_from = $request['filters']['sale_from'];
            } else {
                $filter_sale_from = NULL;
            }

            if( !empty($request['filters']['sale_to']) ){
                $filter_sale_to = $request['filters']['sale_to'];
            } else {
                $filter_sale_to = NULL;
            }

            if( !empty($request['filters']['payment_status'])  ){
                $filter_payment_status = $request['filters']['payment_status'];
            } else {
                $filter_payment_status = NULL;
            }

            if ( !empty($request['filters']['sor_record']) ) {
                $filter_sor_record = $request['filters']['sor_record'];
            } else {
                $filter_sor_record = false;
            }

            $filter_record_range = $this->_record_limits;

            $sort = 'o.order_id';

            $order = 'DESC';

            if ( !empty($request['page']) ) {
                $page = $request['page'];
            } else {
                $page = 1;
            }

            $download_pickup_done_report = true;

            $filter_data = array(
                'filter_order_invoice'      => $filter_order_invoice,
                'filter_order_no' 		 	=> NULL,
                'filter_invoice_date_from'	=> $filter_invoice_date_from,
                'filter_invoice_date_to' 	=> $filter_invoice_date_to,
                'filter_invoice_no' 	 	=> NULL,
                'filter_sale_from' 	 	 	=> $filter_sale_from,
                'filter_sale_to' 	 	 	=> $filter_sale_to,
                'filter_payment_status'     => $filter_payment_status,
                'sort'                 	 	=> $sort,
                'order'                	 	=> $order,
                'start'                	 	=> ($page - 1) * $filter_record_range,
                'limit'                	 	=> $filter_record_range,
                'download_pickup_done_report'=> $download_pickup_done_report,
                'filter_sor_record'		    => $filter_sor_record
            );

            $results = $this->_obj_order_stores->getOrderPickupDone( $this->db, $seller_id, $filter_data);

            $data['order_pickup_data'] = array();

            if( !empty($results) ){
                foreach ($results['records'] as $key => $result) {
                    // calculate amount of debit note of perticular order id
                    $total_amt_debit_note = 0;
                    if(!empty($results['debit_notes'][$result['order_id']])){
                        foreach($results['debit_notes'][$result['order_id']] as $debit_note_values){
                            $total_amt_debit_note += $debit_note_values['debit_note_amount'];
                        }
                    }

                    //get seller invoice download link
                    $seller_inv_dload_link = array();
                    $check_trxn_done = false;
                    if(!empty($results['seller_invoices'][$result['order_id']])){
                        // array of seller invoice corresponding to order_id
                        $seller_invoice_data = $results['seller_invoices'][$result['order_id']];

                        foreach($seller_invoice_data as $seller_inv_id_key => $seller_inv_id_value){
                            if($seller_inv_id_value['trxn_done'] == 'BANK_REQUESTED' || $seller_inv_id_value['trxn_done'] == 'BANK_SUCCESS' || $seller_inv_id_value['trxn_done'] == 'NOT_APPLICABLE'){
                                $check_trxn_done = true;
                            }
                            $seller_invoice = array();
                            $seller_invoice['seller_invoice_id'] = (int)$seller_inv_id_value['seller_invoice_id'];
                            $seller_invoice = serialize($seller_invoice);
                            $secureFileDload = new SecureFileDownload($this->registry);
                            $seller_inv_dload_link[$seller_inv_id_key]['label'] = $seller_inv_id_value['seller_invoice_prefix'].''.$seller_inv_id_value['seller_invoice_no'];
                            $seller_inv_dload_link[$seller_inv_id_key]['url'] = $secureFileDload->getDownloadLink('seller_invoice', base64_encode($seller_invoice), false);
                        }
                    }

                    // get download link of debit note
                    $debit_note_url = array();

                    if( !empty($results['debit_notes'][$result['order_id']]) ){
                        $debit_note_data = $results['debit_notes'][$result['order_id']];
                        foreach ($debit_note_data as $key => $value) {
                            $encode_file = array();
                            $encode_file['order_no'] = $result["order_no"];
                            $encode_file['debit_note_id'] = $value['debit_note_id'];
                            $encode_file = base64_encode(serialize($encode_file));
                            $dload_url['label'] = $value['debit_note_prefix'].''.$value['debit_note_no'];
                            $dload_url['url'] = $this->securefiledownload->getDownloadLink('seller_debit_note',$encode_file, false);
                            $debit_note_url[$key] = $dload_url;
                        }
                    }

                    $net_payable_amount 	= $result['sale_amt'] - $total_amt_debit_note ;
                    $sor_invoice_no 		= '';
                    $sor_invoice_date 		= '';
                    $paymentToolTip			= '';
                    $sor_payment 			= array();

                    if (isset($result['sor_invoice_no'])) {

                        $sor_invoice_no 		= $result['sor_invoice_no'];
                        $sor_payment  = $this->_obj_order_stores->getSorOrderPayments($this->db, $result['order_id'], $seller_id );
                    }
                    if (isset($result['sor_invoice_date'])) {

                        $sor_invoice_date 		= $result['sor_invoice_date'];
                    }
                    /*
                    * setting all data in array
                    */
                    $data['order_pickup_data'][] = array(
                        'order_id' 		=> $result['order_id'],
                        'order_no' 		=> $result['order_no'],
                        'suborder_id' 		=> $result['suborder_id'],
                        'order_date_added' => (!empty($results['order_process_lists'][$result['order_id']]) ? $results['order_process_lists'][$result['order_id']]['order_processing_date'] : $result['date_added']),
                        'sale' 			=> number_format($result['sale_amt'],2),
                        'seller_invoices'=> (!empty($results['seller_invoices'][$result['order_id']]) ? $results['seller_invoices'][$result['order_id']] : array()),
                        'debit_notes'=> (!empty($results['debit_notes'][$result['order_id']]) ? $results['debit_notes'][$result['order_id']] : array()),
                        'return_amount' => number_format($total_amt_debit_note,2),
                        'penalty_amount'=> 0,
                        'net_payable_amount' => number_format($net_payable_amount,2),
                        'trxn_done'		=> ($check_trxn_done) ? 'Paid' : ((!$net_payable_amount) ? '<p style="background:red;">Full Returned</p>' : ''),
                        'product_data' 	=> $this->_obj_order_stores->getOrderProducts($this, $result['order_id'], $result['suborder_id'], $seller_id, array() ),
                        'seller_invoice_link' => $seller_inv_dload_link,
                        'debit_note_link'=> $debit_note_url,
                        'sllr_invc_genrted'	=>	$this->_obj_order_stores->getSellerInvoiceGenerated($this->registry, $this, $result['order_id'], $result['suborder_id'], $seller_id ),
                        'seller_sor_product'	=>	$this->_obj_order_stores->getOrderProducts($this, $result['order_id'],$result['suborder_id'], $seller_id, array(),'','get_sor'),
                        'sor_invoice_no'=>$sor_invoice_no,
                        'sor_invoice_date'=>$sor_invoice_date
                    );
                }
            }


            $download_results = $data['order_pickup_data'];

            $file_name = DIR_DOWNLOAD .'order_pickup_done.csv';
            $fp = fopen($file_name, 'w');

            $data = array('Order No',
                'Invoice / Debit No.',
                'Invoice / Debit Note Date',
                'Invoice / Debit Amount',
                'Payment Done',
                'Payment Amount',
                'Payment Date',
                'Payment Reference No.');
            fputcsv($fp, $data);
            if( !empty($results) ) {

                foreach ($download_results as $key => $sub_value) {
                    foreach( $sub_value['seller_invoices'] as $invoice_key => $invoice_values ){
                        $order_no 		= ($sub_value['order_no'])?$sub_value['order_no']: '';
                        $invoice_no 	= $invoice_values['seller_invoice_prefix'] .'' . $invoice_values['seller_invoice_no'];
                        $date_added 	= ($invoice_values['date_added']) ? date('d-m-Y',strtotime($invoice_values['date_added'])) : '';
                        $sale 			= ($invoice_values['seller_inv_amount']) ? $invoice_values['seller_inv_amount'] : '';
                        $payment_done 	= ($invoice_values['trxn_done']) ? $invoice_values['trxn_done'] : '';
                        $payment_amount = ($invoice_values['trxn_amount']) ? $invoice_values['trxn_amount'] : '';
                        $payment_date 	= ($invoice_values['trxn_utr_date']) ? date('d-m-Y',strtotime($invoice_values['trxn_utr_date'])) : '';
                        $payment_reference_no= ($invoice_values['trxn_utr']) ? $invoice_values['trxn_utr'] : '';
                        $data = array($order_no,
                            $invoice_no,
                            $date_added,
                            $sale,
                            $payment_done,
                            $payment_amount,
                            $payment_date,
                            $payment_reference_no);
                        fputcsv($fp, $data);
                    }
                }

                foreach($download_results as $sub_debit_key => $sub_debit_value){
                    if(!empty($sub_debit_value['debit_notes'])){
                        foreach( $sub_debit_value['debit_notes'] as $debit_key => $debit_values ){
                            $order_no 			= ($sub_debit_value['order_no']) ? $sub_debit_value['order_no']: '';
                            $debit_no 			= $debit_values['debit_note_prefix'] .'' . $debit_values['debit_note_no'];
                            $date_added 		= ($debit_values['date_added']) ? date('d-m-Y',strtotime($debit_values['date_added'])) : '';
                            $debit_note_amount	= ($debit_values['debit_note_amount']) ? $debit_values['debit_note_amount'] : '';
                            $debit_payment_done	= ($debit_values['trxn_done']) ? $debit_values['trxn_done'] : '';
                            $debit_payment_amount= ($debit_values['trxn_amount']) ? $debit_values['trxn_amount'] : '';
                            $debit_payment_date	= ($debit_values['trxn_utr_date']) ? date('d-m-Y',strtotime($debit_values['trxn_utr_date'])) : '';
                            $debit_payment_reference_no= ($debit_values['trxn_utr']) ? date('d-m-Y',strtotime($debit_values['trxn_utr'])) : '';
                            $data = array($order_no,
                                $debit_no,
                                $date_added,
                                $debit_note_amount,
                                $debit_payment_done,
                                $debit_payment_amount,
                                $debit_payment_date,
                                $debit_payment_reference_no);
                            fputcsv($fp, $data);
                        }
                    }
                }
            }

            fclose($fp);

            if (file_exists($file_name)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_name));
                ob_clean();
                flush();
                readfile($file_name);
                exit();
            }
        }

        /**
         * Function : _getPickUpOrder (private)
         * Parameters : $order_id,$suborder_id,$seller_id
         * @author Devendra Dhayal
         */
        private function _getPickUpDoneOrder($order_id, $suborder_id, $seller_id){

            $product_data = array();
            $product_tabs = array();

            // get invoiced products
            $invoices = $this->_obj_order_stores->getSellerInvoiceGenerated(
                $this->registry,
                $this,
                $order_id,
                $suborder_id,
                $seller_id
            );
            if( !empty($invoices) ){
                foreach ( $invoices as $key => $invoice ) {
                    $product_data[$invoice['invoice_no']] = $invoice;
                    $product_data[$invoice['invoice_no']]['date'] = date('d/m/Y', strtotime($invoice['date']));
                    $product_data[$invoice['invoice_no']]['seller_invoice_id'] = $key;
                    $invoice_amount = 0;

                    foreach ($invoice['products_data'][$suborder_id] as $key => $product) {
                        $product_data[$invoice['invoice_no']]['products'][] = $product;
                        $invoice_amount += $product['amount_per_piece'] * $product['no_of_piece'];
                    }
                    $product_data[$invoice['invoice_no']]['total_products'] = sizeof($product_data[$invoice['invoice_no']]['products']);

                    $product_data[$invoice['invoice_no']]['invoice_amount'] = $this->currency->format($invoice_amount, 'INR', 1);

                    unset($product_data[$invoice['invoice_no']]['products_data']);
                    $product_tabs[$invoice['invoice_no']] = $invoice['invoice_no'];
                }
            }

            // get seller sor products
            /*$products = $this->_obj_order_stores->getOrderProducts(
                $this,
                $order_id,
                $suborder_id,
                $seller_id,
                array(),
                '',
                'get_sor'
            );
            if( !empty($products) ){
                $total_amount = 0;
                foreach ( $products[$suborder_id] as $key => $product ){
                    $product_data['seller_sor']['products'][] = $product;
                    $total_amount += $product['total_amount'];
                }
                $product_data['seller_sor']['total_products'] = sizeof($product_data['seller_sor']['products']);
                $product_data['seller_sor']['total_amount'] = $this->currency->format($total_amount, 'INR', 1);

                $product_tabs['seller_sor'] = 'SOR Products';
            }*/

            $data['order_detail'] = array(
                'order_id' 				=> $order_id,
                'suborder_id'        	=> $suborder_id,
                'product_tabs'		    => $product_tabs,
                'data'                  => $product_data
            );

            return $data;
        }

        /**
         * Function : getPickUpDoneOrderDetail
         * Type : Post
         * Request Parameters : user_id,access_token,order_id, suborder_id, tab
         * @author Devendra Dhayal
         */
        public function getPickUpDoneOrderDetail(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $seller_id = $request['user_id'];
            $order_id = $request['order_id'];
            $suborder_id = $request['suborder_id'];

            $data = $this->_getPickUpDoneOrder($order_id, $suborder_id, $seller_id);

            if(!empty($request['tab']) && isset($data['order_detail']['product_tabs'][$request['tab']])){
                $data['order_detail']['tab'] = $request['tab'];
            } else {
                reset($data['order_detail']['product_tabs']);
                $data['order_detail']['tab'] = key($data['order_detail']['product_tabs']);
            }

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /* *******
         * Function : update seller's gcm id
         * Request Parameters : user_id,access_token, gcm_id
         * Type : Post
         *
         ******* */
        public function updateSellerGCMId()
        {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);

            $user_id = $request['user_id'];
            $gcm_id = $request['gcm_id'];

            $this->customer->updateGCMId($user_id, $gcm_id);

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /* *******
         * Function : get seller configuration
         * Request Parameters : user_id,access_token
         * Type : Post
         *
         ******* */
        public function getSellerConfig()
        {
            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';
            $data['config'] = array(
                'how_to_use_app_url' => 'https://www.youtube.com/embed/RfHaCueZ60k',
                'helpline_numbers' => array('9116134791', '9649558363'),
                'minimum_version_required' => 1
            );

            echo json_encode($data);
            exit;
        }

        /* *******
         * Function : get tentative orders
         * Request Parameters : user_id,access_token
         * Type : Post
         *
         ******* */
        public function getTentativeOrders(){

            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $data = array();
            $data['seller_id'] 	= $request['user_id'];

            $data['sortByTypes'] = array(
                'order_date_added-ASC' => 'Oldest to Newest',
                'order_date_added-DESC' => 'Newest to Oldest',
            );

            $seller_id = $request['user_id'];

            $this->load->model('localisation/order_status');

            $filter_record_range = $this->_record_limits;
            $data['records_per_page'] = $filter_record_range;

            if (isset($request['sort_by'])) {
                $data['sort_by'] = $request['sort_by'];
                $temp_data = explode('-', $request['sort_by']);
                $sort = $temp_data[0];
                $order = $temp_data[1];
            } else {
                $data['sort_by'] = 'order_date_added-ASC';
                $sort = 'order_date_added';
                $order = 'ASC';
            }


            if ( !empty($request['page']) ) {
                $page = $request['page'];
            } else {
                $page = 1;
            }

            $filter_data = array(
                'sort'						=> $sort,
                'order'						=> $order,
                'start'                	 	=> ($page - 1) * $filter_record_range,
                'limit'                	 	=>  $filter_record_range
            );

            $tentative_order = $this->_obj_order_stores->getOrderTentative( $this->db, $seller_id, $filter_data);

            $total_tentative_order = $this->_obj_order_stores->getTotalOrderTentative( $this->db, $seller_id);

            $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();
            $order_statuses = array();
            foreach($order_statuses_qry as $order_status_data){
                $order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
            }

            $data['tentative_order'] = array();
            if( !empty($tentative_order) ){
                foreach($tentative_order as $tentative_value ){
                    $data['tentative_order'][] = array(
                        'order_id' => $tentative_value['order_id'],
                        'suborder_id' => $tentative_value['suborder_id'],
                        'order_no' => $tentative_value['order_no'],
                        'order_date_added' => date('d-m-Y',strtotime($tentative_value['order_date_added'])),
                        'order_status' => $order_statuses[$tentative_value['order_status_id']],
                        'total' => $this->currency->format( $tentative_value['total'],
                            'INR',
                            1
                        )
                    );
                }
            }

            $data['total_records'] = $total_tentative_order;
            $data['total_pages'] = ceil($total_tentative_order/$this->_record_limits);
            $data['page'] = $page;

            $data['description_title'] = 'These are not confirmed orders.';
            $data['description'] = array(
                'These are not confirmed orders, but tentative orders placed by the buyers with wholesalebox.',
                'These orders are not yet informed to you and processed, as we are confirming few things with the customer.',
                'These orders are just an indication for you the upcoming stock requirement, so that you may get them ready, but please note that these are not yet confirmed and can be cancelled also.'
            );

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /**
         * Function : getTentativeOrderDetail
         * Type : Post
         * Request Parameters : user_id,access_token,order_id, suborder_id
         * @author Devendra Dhayal
         */
        public function getTentativeOrderDetail(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $seller_id = $request['user_id'];
            $order_id = $request['order_id'];
            $suborder_id = $request['suborder_id'];

            $products = $this->_obj_order_stores->getOrderProducts($this, $order_id, $suborder_id, $seller_id );
            if( !empty($products) ){
                foreach ( $products[$suborder_id] as $key => $product ){
                    $data['products'][] = $product;
                }
                $data['total_products'] = sizeof($data['products']);
            }

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }


        /**
         * Function : get SOR orders
         * Type : Post
         * Request Parameters : user_id,access_token,filters
         * @author Devendra Dhayal
         */

        public function getSOROrders(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $data = array();
            $data['seller_id'] 	= $request['user_id'];

            $seller_id = $request['user_id'];
            $access_token = $request['access_token'];

            $data['getQuarters'] = getQuarters();
            $data['getFinancialYears'] = getFinancialYears(2016, '');

            if (!empty($request['filters'])) {
                $data['filters'] = $request['filters'];
            } else {
                $data['filters'] = array(
                    'order_invoice'       => '',
                    'invoice_date_from'   => '',
                    'invoice_date_to'     => '',
                    'sale_from'           => '',
                    'sale_to'             => '',
                    'month_range'         => '',
                    'year_range'          => '',
                    'payment_status'      => ''
                );
            }

            $data['sortByTypes'] = array(
                'order_processing_date_time-ASC' => 'Oldest to Newest',
                'order_processing_date_time-DESC' => 'Newest to Oldest',
            );

            $data['paymentStatus'] = array(
                'paid' => 'Paid',
                'un_paid' => 'Un Paid'
            );

            $data['date_ranges'] = array(
                'Q1' => array('start' => '01-04-', 'end' => '30-06-'),
                'Q2' => array('start' => '01-07-', 'end' => '30-09-'),
                'Q3' => array('start' => '01-10-', 'end' => '31-12-'),
                'Q4' => array('start' => '01-01-', 'end' => '31-03-')
            );

            if( !empty($request['filters']['order_invoice']) ){
                $filter_order_no = $request['filters']['order_invoice'];
            } else {
                $filter_order_no = NULL;
            }

            if( !empty($request['filters']['invoice_date_from']) ){
                $filter_invoice_date_from = $request['filters']['invoice_date_from'];
            } else {
                $filter_invoice_date_from = NULL;
            }

            if( !empty($request['filters']['invoice_date_to']) ){
                $filter_invoice_date_to = $request['filters']['invoice_date_to'];
            } else {
                $filter_invoice_date_to = NULL;
            }

            $filter_invoice_no = NULL;

            if( !empty($request['filters']['sale_from']) ){
                $filter_sale_from = $request['filters']['sale_from'];
            } else {
                $filter_sale_from = NULL;
            }

            if( !empty($request['filters']['sale_to']) ){
                $filter_sale_to = $request['filters']['sale_to'];
            } else {
                $filter_sale_to = NULL;
            }

            if( !empty($request['filters']['payment_status'])  ){
                $filter_payment_status = $request['filters']['payment_status'];
            } else {
                $filter_payment_status = NULL;
            }

            $filter_record_range = $this->_record_limits;

            if (isset($request['sort_by'])) {
                $data['sort_by'] = $request['sort_by'];
                $temp_data = explode('-', $request['sort_by']);
                $sort = $temp_data[0];
                $order = $temp_data[1];
            } else {
                $data['sort_by'] = 'order_processing_date_time-DESC';
                $sort = 'order_processing_date_time';
                $order = 'DESC';
            }

            if ( !empty($request['page']) ) {
                $page = $request['page'];
            } else {
                $page = 1;
            }

            $data['page'] = $page;
            $data['records_per_page'] = $filter_record_range;


            $filter_data = array(
                'filter_order_no' 		 	=> $filter_order_no,
                'filter_invoice_date_from'	=> $filter_invoice_date_from,
                'filter_invoice_date_to' 	=> $filter_invoice_date_to,
                'filter_invoice_no' 	 	=> $filter_invoice_no,
                'filter_sale_from' 	 	 	=> $filter_sale_from,
                'filter_sale_to' 	 	 	=> $filter_sale_to,
                'filter_payment_status'     => $filter_payment_status,
                'sort'                 	 	=> $sort,
                'order'                	 	=> $order,
                'start'                	 	=> ($page - 1) * $filter_record_range,
                'limit'                	 	=> $filter_record_range,
                'download_pickup_done_report'=> false,
                'filter_sor_record'		    => 'onlysor'
            );

            $results = $this->_obj_order_stores->getOrderSorProduct( $this->db, $seller_id, $filter_data);

            $total_results = $this->_obj_order_stores->getTotalRecordSorProduct( $this->db, $seller_id, $filter_data);

            $data['total_records'] = $total_results;

            $data['total_pages'] = ceil($total_results/$this->_record_limits);

            $data['pickup_data'] = array();

            if( !empty($results) ){
                foreach ($results['records'] as $key => $result) {
                    // calculate amount of debit note of perticular order id
                    $total_amt_debit_note = 0;
                    if(!empty($results['debit_notes'][$result['order_id']])){
                        foreach($results['debit_notes'][$result['order_id']] as $debit_note_values){
                            $total_amt_debit_note += $debit_note_values['debit_note_amount'];
                        }
                    }

                    //get seller invoice download link
                    $seller_inv_dload_link = array();
                    $check_trxn_done = false;
                    if(!empty($results['seller_invoices'][$result['order_id']])){
                        // array of seller invoice corresponding to order_id
                        $seller_invoice_data = $results['seller_invoices'][$result['order_id']];

                        foreach($seller_invoice_data as $seller_inv_id_key => $seller_inv_id_value){
                            if($seller_inv_id_value['trxn_done'] == 'BANK_REQUESTED' || $seller_inv_id_value['trxn_done'] == 'BANK_SUCCESS' || $seller_inv_id_value['trxn_done'] == 'NOT_APPLICABLE'){
                                $check_trxn_done = true;
                            }
                            $seller_invoice = array();
                            $seller_invoice['seller_invoice_id'] = (int)$seller_inv_id_value['seller_invoice_id'];
                            $seller_invoice = serialize($seller_invoice);
                            $secureFileDload = new SecureFileDownload($this->registry);
                            $seller_inv_dload_link[$seller_inv_id_key]['label'] = $seller_inv_id_value['seller_invoice_prefix'].''.$seller_inv_id_value['seller_invoice_no'];
                            $seller_inv_dload_link[$seller_inv_id_key]['url'] = $secureFileDload->getDownloadLink('seller_invoice', base64_encode($seller_invoice), false) . '&user_id='.$seller_id.'&access_token='.$access_token;
                        }
                    }

                    // get download link of debit note
                    $debit_note_url = array();

                    if( !empty($results['debit_notes'][$result['order_id']]) ){
                        $debit_note_data = $results['debit_notes'][$result['order_id']];
                        foreach ($debit_note_data as $key => $value) {
                            $encode_file = array();
                            $encode_file['order_no'] = $result["order_no"];
                            $encode_file['debit_note_id'] = $value['debit_note_id'];
                            $encode_file = base64_encode(serialize($encode_file));
                            $dload_url['label'] = $value['debit_note_prefix'].''.$value['debit_note_no'];
                            $dload_url['url'] = $this->securefiledownload->getDownloadLink('seller_debit_note',$encode_file, false) . '&user_id='.$seller_id.'&access_token='.$access_token;
                            $debit_note_url[$key] = $dload_url;
                        }
                    }

                    $net_payable_amount 	= $result['sale_amt'] - $total_amt_debit_note ;
                    $sor_invoice_no 		= '';
                    $sor_invoice_date 		= '';
                    $paymentToolTip			= '';
                    $sor_payment 			= array();
                    $contain_sor            = false;

                    if (isset($result['sor_invoice_no'])) {
                        $sor_invoice_no 		= $result['sor_invoice_no'];
                        $sor_payment  = $this->_obj_order_stores->getSorOrderPayments($this->db, $result['order_id'], $seller_id );
                    }
                    if (isset($result['sor_invoice_date'])) {

                        $sor_invoice_date 		= $result['sor_invoice_date'];
                    }

                    if(!empty($sor_invoice_no)) $contain_sor = true;

                    $payment_info = array();

                    /*
                    * set the string for payment tool tip
                    */
                    if(!empty($results['seller_invoices'][$result['order_id']])){

                        foreach ($results['seller_invoices'][$result['order_id']] as $keyse => $valuese) {

                            if($valuese['trxn_done'] == 'BANK_REQUESTED' || $valuese['trxn_done'] == 'BANK_SUCCESS' || $valuese['trxn_done'] == 'NOT_APPLICABLE'){

                                //$paymentToolTip .=     '<li>Inv. No. ' . $valuese["seller_invoice_prefix"].''.$valuese["seller_invoice_no"] . '</li>';
                                //$paymentToolTip .=     '<li>Amount. ' . (($valuese["trxn_amount"]) ? $valuese["trxn_amount"] : ''). '</li>';

                                if($valuese["trxn_utr"]!='' && $valuese["trxn_utr_date"]!=''){
                                    array_push($payment_info, 'Ref. ' . (($valuese["trxn_utr"]) ? $valuese["trxn_utr"] : ''));
                                    array_push($payment_info, 'Date. ' . (($valuese["trxn_utr_date"]) ? $valuese["trxn_utr_date"] : ''));
                                }
                            }
                        }

                    }

                    if(!empty($sor_payment)){
                        foreach ($sor_payment as $keysor => $valuesor) {
                            if (($valuesor['trxn_done'] == 'BANK_REQUESTED' || $valuesor['trxn_done'] == 'BANK_SUCCESS' || $valuesor['trxn_done'] == 'NOT_APPLICABLE') && $valuesor['suborder_id'] == $result['suborder_id']) {
                                $check_trxn_done = true;
                                //$paymentToolTip .=     '<li>Sub Order. No. ' . $valuesor["suborder_id"].'</li>';
                                //$paymentToolTip .=     '<li>Amount. ' . (($valuesor["total_paid_amount"]) ? $valuesor["total_paid_amount"] : ''). '</li>';

                                if($valuesor["trxn_utr"]!='' && $valuesor["trxn_utr_date"]!=''){
                                    array_push($payment_info, 'Ref. ' . (($valuese["trxn_utr"]) ? $valuese["trxn_utr"] : ''));
                                    array_push($payment_info, 'Date. ' . (($valuese["trxn_utr_date"]) ? $valuese["trxn_utr_date"] : ''));
                                }
                            }
                        }
                    }

                    $trxn_status = ($check_trxn_done) ? 'Paid' : ((!$net_payable_amount) ? 'Full Returned' : 'Un Paid');
                    if (isset($result['order_status_id']) && $result['order_status_id'] == '2') {
                        $trxn_status = 'Cancelled';
                    }

                    $order_date = (!empty($results['order_process_lists'][$result['order_id']]['order_processing_date']) ? $results['order_process_lists'][$result['order_id']]['order_processing_date'] : $result['date_added']);

                    /*
                    * setting all data in array
                    */
                    $data['pickup_data'][] = array(
                        'order_id' 		=> $result['order_id'],
                        'order_no' 		=> $result['order_no'],
                        'suborder_id' 		=> $result['suborder_id'],
                        'order_date_added' => date("d-m-Y", strtotime($order_date)),
                        'sale' 			=> number_format($result['sale_amt'],2),
                        'seller_invoices'=> (!empty($results['seller_invoices'][$result['order_id']]) ? $results['seller_invoices'][$result['order_id']] : array()),
                        'debit_notes'=> (!empty($results['debit_notes'][$result['order_id']]) ? $results['debit_notes'][$result['order_id']] : array()),
                        'return_amount' => number_format($total_amt_debit_note,2),
                        'penalty_amount'=> 0,
                        'net_payable_amount' => number_format($net_payable_amount,2),
                        'trxn_done'		=> $trxn_status,
                        'seller_invoice_link' => $seller_inv_dload_link,
                        'debit_note_link'=> $debit_note_url,
                        'sor_invoice_no'=>$sor_invoice_no,
                        'sor_invoice_date'=>$sor_invoice_date,
                        'contain_sor' => $contain_sor,
                        'payment_info' => $payment_info
                    );
                }
            }

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /**
         * Function : _getSOROrder (private)
         * Parameters : $order_id,$suborder_id,$seller_id
         * @author Devendra Dhayal
         */
        private function _getSOROrder($order_id, $suborder_id, $seller_id){

            $product_data = array();
            $product_tabs = array();

            // get seller sor products
            $products = $this->_obj_order_stores->getSorSubOrderProducts(
                $this,
                $order_id,
                $seller_id
            );
            if( !empty($products) ){
                $total_amount = 0;
                foreach ( $products[$suborder_id] as $key => $product ){
                    $product_data['seller_sor']['products'][] = $product;
                    $total_amount += $product['amount_per_piece'] * $product['no_of_piece'];
                }
                $product_data['seller_sor']['total_products'] = sizeof($product_data['seller_sor']['products']);
                $product_data['seller_sor']['total_amount'] = $this->currency->format($total_amount, 'INR', 1);

                $product_tabs['seller_sor'] = 'SOR Products';
            }

            $data['order_detail'] = array(
                'order_id' 				=> $order_id,
                'suborder_id'        	=> $suborder_id,
                'product_tabs'		    => $product_tabs,
                'data'                  => $product_data
            );

            return $data;
        }

        /**
         * Function : getSOROrderDetail
         * Type : Post
         * Request Parameters : user_id,access_token,order_id, suborder_id, tab
         * @author Devendra Dhayal
         */
        public function getSOROrderDetail(){
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            $seller_id = $request['user_id'];
            $order_id = $request['order_id'];
            $suborder_id = $request['suborder_id'];

            $data = $this->_getSOROrder($order_id, $suborder_id, $seller_id);

            if(!empty($request['tab']) && isset($data['order_detail']['product_tabs'][$request['tab']])){
                $data['order_detail']['tab'] = $request['tab'];
            } else {
                reset($data['order_detail']['product_tabs']);
                $data['order_detail']['tab'] = key($data['order_detail']['product_tabs']);
            }

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }

        /* *******
         * Function : update seller app info
         * Request Parameters : user_id,access_token, device_info, platform, app_version_code
         * Type : Post
         * @author Devendra Dhayal
         *
         ******* */
        public function updateSellerDeviceInfo()
        {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);

            $user_id = $request['user_id'];

            $app_info = array(
                'device_info' => serialize($request['device_info']),
                'app_version_code' => $request['app_version_code']
            );

            $sellerProfile = new SellerProfile($this);
            $sellerProfile->updateSellerDeviceInfo($user_id, $app_info);

            $data['status'] = '1';
            $data['status_text'] = 'Success';
            $data['message'] = 'Success';

            echo json_encode($data);
            exit;
        }
        /* *******
         * Function : get Returns of seller
         * Request Parameters : user_id,access_token,filters
         * Type : Post
         * @author Kusum Joshi
         *
         ******* */
        public function getReturnList(){

            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);

            //Z9RBKSogldfu40NX
            //16
            // $request['user_id']='16';
            // $request['access_token']='Z9RBKSogldfu40NX';
            $data = array();
            $data['seller_id']  = $request['user_id'];

            $seller_id = $request['user_id'];
            $access_token = $request['access_token'];

            if (!empty($request['filters'])) {
                $data['filters'] = $request['filters'];
            } else {
                $data['filters'] = array(
                    'order_debit'       => '',
                    'debit_note_date_from'   => '',
                    'debit_note_date_to'     => '',
                    'search_picker'    => 'order_no',
                    'return_action_id' => 'SELLER_PANEL_TENTATIVE_RETURNS'
                );
            }

             if( !empty($request['id']) ){
            $id = explode("-", $request['id']);
            $master_return_id  = $id[0];
            $seller_invoice_id = $id[1];
            $return_view_type  = $id[2];
            } else {
            $master_return_id = NULL;
            $seller_invoice_id= NULL;
            $return_view_type = NULL;
            }
            $picker_list = array();
            $picker_list['order_no'] = 'Order No';
            $picker_list['debit_note_no'] = 'Debit Note No';
    
            $data['return_search_picker'] = $picker_list;

            $return_tabs = array();

            $return_tabs['SELLER_PANEL_TENTATIVE_RETURNS'] = 'Tentative';
            $return_tabs['SELLER_PANEL_APPROVED_RETURNS'] = 'Request Approved';
            $return_tabs['SELLER_PANEL_DELIVERED_RETURNS'] = 'Delivered';
            $return_tabs['SELLER_PANEL_DISPUTED_RETURNS'] = 'Delivery Dispute';

            $data['return_tabs'] = $return_tabs;

            

            if( !empty($request['filters']['order_debit'])  && !empty($request['filters']['search_picker']) && $request['filters']['search_picker'] == 'order_no'){
                $filter_order_no = $request['filters']['order_debit'];
                $filter_debit_note_no = NULL;
            } else if (!empty($request['filters']['order_debit'])  && !empty($request['filters']['search_picker']) && $request['filters']['search_picker'] == 'debit_note_no') {
                $filter_order_no = NULL;
                $filter_debit_note_no = $request['filters']['order_debit'];
            }else {
                $filter_order_no = NULL;
                $filter_debit_note_no = NULL;
            }

            if( !empty($request['filters']['debit_note_date_from']) ){
                //date('Y-m-d' , strtotime($request['filters']['date_from']));
                $filter_debit_note_date_from = date('Y-m-d' , strtotime($request['filters']['debit_note_date_from']));
            } else {
                $filter_debit_note_date_from = NULL;
            }

            if( !empty($request['filters']['debit_note_date_to']) ){
                $filter_debit_note_date_to = date('Y-m-d' , strtotime($request['filters']['debit_note_date_to']));
            } else {
                $filter_debit_note_date_to = NULL;
            }


            if((!empty($request['filters']['return_action_id']) && $request['filters']['return_action_id'] == 'SELLER_PANEL_TENTATIVE_RETURNS') || (!empty($return_view_type) && $return_view_type == 'tentative'))
            {
              $return_action_id = implode(",", SELLER_PANEL_TENTATIVE_RETURNS);
              $return_view_type = 'tentative';
              $data['tab'] = 'SELLER_PANEL_TENTATIVE_RETURNS';
            }
            else if((!empty($request['filters']['return_action_id']) && $request['filters']['return_action_id'] == 'SELLER_PANEL_APPROVED_RETURNS') || (!empty($return_view_type) && $return_view_type == 'approved'))
            {
              $return_action_id = implode(",", SELLER_PANEL_APPROVED_RETURNS);
              $return_view_type = 'approved';
              $data['tab'] = 'SELLER_PANEL_APPROVED_RETURNS';
            }
            else if((!empty($request['filters']['return_action_id']) && $request['filters']['return_action_id'] == 'SELLER_PANEL_DISPUTED_RETURNS') || (!empty($return_view_type) && $return_view_type == 'disputed'))
            {  
              $return_action_id = implode(",", SELLER_PANEL_DISPUTED_RETURNS);
              $return_view_type = 'disputed';
              $data['tab'] = 'SELLER_PANEL_DISPUTED_RETURNS';
            }
            else if((!empty($request['filters']['return_action_id']) && $request['filters']['return_action_id'] == 'SELLER_PANEL_DELIVERED_RETURNS') || (!empty($return_view_type) && $return_view_type == 'delivered'))
            { 
              $return_action_id = implode(",", array_merge(SELLER_PANEL_TENTATIVE_RETURNS, SELLER_PANEL_APPROVED_RETURNS, SELLER_PANEL_DISPUTED_RETURNS));
              $return_view_type = 'delivered';
              $data['tab'] = 'SELLER_PANEL_DELIVERED_RETURNS';
            }
            else
            {
              $return_action_id = implode(",", SELLER_PANEL_TENTATIVE_RETURNS);
              $return_view_type = 'tentative';
              $data['tab'] = 'SELLER_PANEL_TENTATIVE_RETURNS';
            }


            $filter_record_range = $this->_record_limits;

            if (isset($request['sort_by'])) {
                $data['sort_by'] = $request['sort_by'];
                $temp_data = explode('-', $request['sort_by']);
                $sort = $temp_data[0];
                $order = $temp_data[1];
            } else {
                $data['sort_by'] = 'oo.order_id-DESC';
                $sort = 'oo.order_id';
                $order = 'DESC';
            }

            if ( !empty($request['page']) ) {
                $page = $request['page'];
            } else {
                $page = 1;
            }

            $data['page'] = $page;
            $data['records_per_page'] = $filter_record_range;

            $filter_data = array(
                'filter_order_no'      => $filter_order_no,
                'debit_note_no'        => $filter_debit_note_no,
                'debit_note_date_from' => $filter_debit_note_date_from,
                'debit_note_date_to' => $filter_debit_note_date_to,
                'return_view_type' => $return_view_type,
                'return_action_id' => $return_action_id,
                'master_return_id' => $master_return_id,
                'seller_invoice_id' => $seller_invoice_id,
                'sort'           => $sort,
                'order'          => $order,
                'start'          => ($page - 1) * $filter_record_range,
                'limit'          => $filter_record_range,
            );
        if($filter_data['return_view_type'] == 'delivered')
          {
            $getReturnOrderDetails = $this->_obj_returns->getDeliveredReturns( $this->db, $seller_id, $filter_data);
          }
          else
          {
            $getReturnOrderDetails = $this->_obj_returns->getReturnOrders( $this->db, $seller_id, $filter_data);
          }  

          $total_results = $getReturnOrderDetails[1];

          $return_order_data = array();
          if( !empty($getReturnOrderDetails[0]) )
          {
            foreach($getReturnOrderDetails[0] as $key=>$return_order ){
              $download_debit_note='';
              $debit_note_pdf='';
              $download_invoice_note='';

              if(!empty($return_order['debit_note_id']))
              {
                $encode_file = array();
                $encode_file['order_no']      = $return_order['order_no'];
                $encode_file['debit_note_id'] = $return_order['debit_note_id'];
                $encode_file = base64_encode(serialize($encode_file));
                $download_debit_note = $this->securefiledownload->getDownloadLink('seller_debit_note',$encode_file, false);
                $debit_note_pdf      = $return_order['debit_note_no'];
              }

              if(!empty($return_order['seller_invoice_id']))
              {
                $seller_invoice = array();
                $seller_invoice['order_id'] = $return_order['order_id'];
                $seller_invoice['suborder_id'] = $return_order['suborder_id'];
                $seller_invoice['seller_invoice_no'] = $return_order['seller_invoice_number'];
                $seller_invoice['seller_invoice_id'] = $return_order['seller_invoice_id'];
                $seller_invoice['seller_invoice_prefix'] =  '' ;
                $seller_invoice = base64_encode(serialize($seller_invoice));
                $download_invoice_note = $this->securefiledownload->getDownloadLink('seller_invoice',$seller_invoice, false);
              } 

               $uniqe_id =  $return_order['master_return_id'].'-'.$return_order['seller_invoice_id'].'-'.$filter_data['return_view_type'];

               if($return_order['seller_invoice_date'] && !empty($return_order['seller_invoice_date'])){
                   $return_order['seller_invoice_date'] = date('d/m/Y' , strtotime($return_order['seller_invoice_date']));
                }
                if($return_order['debit_note_date'] && !empty($return_order['debit_note_date'])){
                   $return_order['debit_note_date'] = date('d/m/Y' , strtotime($return_order['debit_note_date']));
                }
              $return_order_data[] = array(
                'id'                     => $uniqe_id,
                'master_return_id'       => $return_order['master_return_id'],
                'order_id'               => $return_order['order_id'],
                'suborder_id'            => $return_order['suborder_id'],
                'seller_invoice_id'      => $return_order['seller_invoice_id'],
                'seller_invoice_number'  => $return_order['seller_invoice_number'],
                'seller_invoice_date'    => $return_order['seller_invoice_date'],
                'order_no'               => $return_order['order_no'],
                'order_product_ids'      => $return_order['order_product_ids'],
                'quantity'               => $return_order['quantity'],
                'return_amount'          => $return_order['return_amount'],
                'debit_note_amount'      => $return_order['debit_note_amount'],
                'amount'                 => (isset($return_order['debit_note_amount'])) ? $return_order['debit_note_amount'] : $return_order['return_amount'],
                'debit_note_date'        => $return_order['debit_note_date'],
                'debit_note_no'          => $return_order['debit_note_no'],
                'reason_name'            => $return_order['reason_names'],
                'download_debit_note'    => $download_debit_note,
                'debit_note_pdf'         => $debit_note_pdf,
                'download_invoice_pdf'  => $download_invoice_note
                );
            }
          }


        $data['status'] = '1';
        $data['status_text'] = 'Success';
        $data['message'] = 'Success';
        $data['returns'] = $return_order_data;
        $data['total_records'] = $total_results;
        $data['total_pages'] = ceil($total_results/$this->_record_limits);
        echo json_encode($data);
        exit;

        }

        /* *******
         * Function : get Seller Replacements
         * Request Parameters : user_id,access_token,filters
         * Type : Post
         * @author Kusum Joshi
         *
         ******* */
        public function getReplacementList()
        {

            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);

            $data = array();
            $data['seller_id']  = $request['user_id'];

            $seller_id = $request['user_id'];
            $access_token = $request['access_token'];

            if (!empty($request['filters'])) {
                $data['filters'] = $request['filters'];
            } else {
                $data['filters'] = array(
                    'order_search'       => '',
                    'replacement_action_id' => 'SELLER_PANEL_TENTATIVE_REPLACEMENTS'
                );
            }
            if( !empty($request['request_id']) ){
            $id = explode("-", $request['request_id']);
            $master_return_id  = $id[0];
            $seller_invoice_id = $id[1];
            $replacement_view_type  = $id[2];
            } else {
            $master_return_id = NULL;
            $seller_invoice_id= NULL;
            $replacement_view_type = NULL;
            }

            $replacement_tabs = array();

            $replacement_tabs['SELLER_PANEL_TENTATIVE_REPLACEMENTS'] = 'Tentative';
            $replacement_tabs['SELLER_PANEL_APPROVED_REPLACEMENTS'] = 'Request Approved';
            $replacement_tabs['SELLER_PANEL_DELIVERED_REPLACEMENTS'] = 'Delivered';
            $replacement_tabs['SELLER_PANEL_DISPUTED_REPLACEMENTS'] = 'Delivery Dispute';

            $data['replacement_tabs'] = $replacement_tabs;

            if( !empty($request['filters']['order_search']) ){
                $filter_order_no = $request['filters']['order_search'];
            } else {
                $filter_order_no = NULL;
            }
            if((!empty($request['filters']['replacement_action_id']) && $request['filters']['replacement_action_id'] == 'SELLER_PANEL_TENTATIVE_REPLACEMENTS') || (!empty($replacement_view_type) && $replacement_view_type == 'tentative'))
            {
              $replacement_action_id = implode(",", SELLER_PANEL_TENTATIVE_REPLACEMENTS);
              $replacement_view_type = 'tentative';
              $data['tab'] = 'SELLER_PANEL_TENTATIVE_REPLACEMENTS';
            }
            else if((!empty($request['filters']['replacement_action_id']) && $request['filters']['replacement_action_id'] == 'SELLER_PANEL_APPROVED_REPLACEMENTS') || (!empty($replacement_view_type) && $replacement_view_type == 'approved'))
            {
              $replacement_action_id = implode(",", SELLER_PANEL_APPROVED_REPLACEMENTS);
              $replacement_view_type = 'approved';
              $data['tab'] = 'SELLER_PANEL_APPROVED_REPLACEMENTS';
            }
            else if((!empty($request['filters']['replacement_action_id']) && $request['filters']['replacement_action_id'] == 'SELLER_PANEL_DISPUTED_REPLACEMENTS') || (!empty($replacement_view_type) && $replacement_view_type == 'disputed'))
            {  
              $replacement_action_id = implode(",", SELLER_PANEL_DISPUTED_REPLACEMENTS);
              $replacement_view_type = 'disputed';
              $data['tab'] = 'SELLER_PANEL_DISPUTED_REPLACEMENTS';
            }
            else if((!empty($request['filters']['replacement_action_id']) && $request['filters']['replacement_action_id'] == 'SELLER_PANEL_DELIVERED_REPLACEMENTS') || (!empty($replacement_view_type) && $replacement_view_type == 'delivered'))
            { 
              $replacement_action_id = implode(",", SELLER_PANEL_DELIVERED_REPLACEMENTS);
              $replacement_view_type = 'delivered';
              $data['tab'] = 'SELLER_PANEL_DELIVERED_REPLACEMENTS';
            }
            else
            {
              $replacement_action_id = implode(",", SELLER_PANEL_TENTATIVE_REPLACEMENTS);
              $replacement_view_type = 'tentative';
              $data['tab'] = 'SELLER_PANEL_TENTATIVE_REPLACEMENTS';
            }

            $filter_record_range = $this->_record_limits;

            if (isset($request['sort_by'])) {
                $data['sort_by'] = $request['sort_by'];
                $temp_data = explode('-', $request['sort_by']);
                $sort = $temp_data[0];
                $order = $temp_data[1];
            } else {
                $data['sort_by'] = 'oo.order_id-DESC';
                $sort = 'oo.order_id';
                $order = 'DESC';
            }

            if ( !empty($request['page']) ) {
                $page = $request['page'];
            } else {
                $page = 1;
            }

            $data['page'] = $page;
            $data['records_per_page'] = $filter_record_range;

            $filter_data = array(
                'filter_order_no'      => $filter_order_no,
                'replacement_view_type' => $replacement_view_type,
                'replacement_action_id' => $replacement_action_id,
                'sort'           => $sort,
                'order'          => $order,
                'start'          => ($page - 1) * $filter_record_range,
                'limit'          => $filter_record_range,
                'master_return_id' => $master_return_id,
                'seller_invoice_id' => $seller_invoice_id,
            );
            
            $getReplaceOrderDetails = $this->_obj_returns->getReplacementOrders( $this->db, $seller_id, $filter_data);

            $total_results = $getReplaceOrderDetails[1];

            $replace_order_data = array();
            if( !empty($getReplaceOrderDetails[0]) )
            {
            
                foreach($getReplaceOrderDetails[0] as $key=>$replace_order ){
                $download_invoice_note='';
                if(!empty($replace_order['seller_invoice_id']))
                {
                $seller_invoice = array();
                $seller_invoice['order_id'] = $replace_order['order_id'];
                $seller_invoice['suborder_id'] = $replace_order['suborder_id'];
                $seller_invoice['seller_invoice_no'] = $replace_order['seller_invoice_number'];
                $seller_invoice['seller_invoice_id'] = $replace_order['seller_invoice_id'];
                $seller_invoice['seller_invoice_prefix'] =  '' ;
                $seller_invoice = base64_encode(serialize($seller_invoice));
                $download_invoice_note = $this->securefiledownload->getDownloadLink('seller_invoice',$seller_invoice, false);
                }
                if($replace_order['seller_invoice_date'] && !empty($replace_order['seller_invoice_date'])){
                   $replace_order['seller_invoice_date'] = date('d/m/Y' , strtotime($replace_order['seller_invoice_date']));
                }
              $replace_order_data[] = array(
                'id'                     => $replace_order['master_return_id'].'-'.
                                            $replace_order['seller_invoice_id'].'-'.
                                            $filter_data['return_view_type'],
                'master_return_id'       => $replace_order['master_return_id'],
                'order_id'               => $replace_order['order_id'],
                'suborder_id'            => $replace_order['suborder_id'],
                'seller_invoice_id'      => $replace_order['seller_invoice_id'],
                'seller_invoice_number'  => $replace_order['seller_invoice_number'],
                'seller_invoice_date'    => $replace_order['seller_invoice_date'],
                'order_no'               => $replace_order['order_no'],
                'order_product_ids'      => $replace_order['order_product_ids'],
                'quantity'               => $replace_order['quantity'],
                'reason_name'            => $replace_order['reason_names'],
                'download_invoice_note'    => $download_invoice_note
                );
            }
          }

        $data['status'] = '1';
        $data['status_text'] = 'Success';
        $data['message'] = 'Success';
        $data['replacements'] = $replace_order_data;
        $data['total_records'] = $total_results;
        $data['total_pages'] = ceil($total_results/$this->_record_limits);
        echo json_encode($data);
        exit;

        }

        public function getReturnProductList(){

            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);

            $data = array();
            $data['seller_id']  = $request['user_id'];

            $seller_id = $request['user_id'];
            $access_token = $request['access_token'];
            $total_results = 0;
            $product_record_array = array();
           if( !empty($request['request_id']) ){
            $id = explode("-", $request['request_id']);
            $master_return_id  = $id[0];
            $seller_invoice_id = $id[1];
          } else {
            $master_return_id = NULL;
            $seller_invoice_id= NULL;
          }

          if( !empty($request['order_product_ids']) ){
            $order_product_ids = $request['order_product_ids'];
          } else {
            $order_product_ids = NULL;
          }  

          if ( !empty($request['sort']) ) {
            $sort = $request['sort'];
          } else {
            $sort = 'debit_note_date';
          }

          if ( !empty($request['order']) ) {
            $order = $request['order'];
          } else {
            $order = 'DESC';
          }

          if ( !empty($request['page']) ) {
                $page = $request['page'];
            } else {
                $page = 1;
            }

            $data['page'] = $page;


          if( !empty($request['limit'])  ){
              $limit = $request['limit'];
          } else {
              $limit = $this->_record_limits;
          }          


        $filter_data = array(
          'master_return_id'      => $master_return_id,
          'order_product_ids'     => $order_product_ids,
          'sort'                  => $sort,
          'order'                 => $order,
          'start'                 => ($page - 1) * $this->_record_limits,
          'limit'                 => $limit
        );
    
    // for pickup requested
    $getReturnProductDetails = $this->_obj_returns->getReturnProductDetails( $this, $seller_id, $filter_data);
          
    $product_record_array = $getReturnProductDetails[0];
    $total_results        = $getReturnProductDetails[1];
     
     $product_images = array();

     $this->load->model('tool/image'); 
     if(!empty($order_product_ids)){
        $product_images = $this->product->getOrderProductOptionImages($this->db, explode(",", $order_product_ids), 1);
     }
    
    if(!empty($product_images)){

      foreach($product_images as $key => $image) {
         $product_record_array[$key]['image'] = $this->model_tool_image->resize($image,
                                                                  $this->config->get('config_image_additional_width'),
                                                                  $this->config->get('config_image_additional_height')
                                                            );
         $product_record_array[$key]['mobile_image'] = $this->model_tool_image->resizeBasedOnLargeDimension($image, '400');
      }
    }

        $data['status']        = '1';
        $data['status_text']   = 'Success';
        $data['message']       = 'Success';
        $data['product_data']  = array_values($product_record_array);
        $data['total_results'] = $total_results;
        $data['total_pages']   = ceil($total_results/$this->_record_limits);
        $data['request_id']    = $request['request_id'];
        $data['order_no']      = $request['order_no'];
        echo json_encode($data);
        exit;

    }
        
    }

?>
