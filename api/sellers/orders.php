<?php
     require_once('system.php');
     require_once( DIR_SYSTEM . 'library/currency.php' );
     require_once( DIR_SYSTEM . 'library/seller/order_stores.php' );
     require_once( DIR_SYSTEM . 'library/seller/seller_info.php' );
     require_once( DIR_SYSTEM . 'library/operations/orders/order_edit.php' );
     require_once( DIR_SYSTEM . 'library/securefiledownload.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );

    class ordersController extends SystemController
    {
        private $error = array();
        private $_obj_order_stores;
        private $record_limits = 10;

        public function __construct($params) {

            parent::__construct($params);
            $this->_obj_order_stores = new OrderStores();
            // Currency
            $this->registry->set('currency', new Currency($this->registry));
            // SecureFileDownload                                        
            $this->registry->set('securefiledownload', new SecureFileDownload($this->registry));
        }
        /**
         * customer login
         */

       public function getPickpupOrderRequested() 
        {
          $pickup_requested = array();
          $total_results    = 0;
          $pickup_requested = array();
          if( !empty($this->request['id']) ){
             $suborder_id = $this->request['id'];
          } else {
            $suborder_id = NULL;
          }

          if( !empty($this->request['filter_order_no_requested']) ){
             $filter_order_no_requested = $this->request['filter_order_no_requested'];
          } else {
            $filter_order_no_requested = NULL;
          }

          if( !empty($this->request['filter_order_processing_date_from']) ){

           $filter_order_processing_date_from = $this->request['filter_order_processing_date_from'];
          } else {
            $filter_order_processing_date_from = NULL;
          }

          if( !empty($this->request['filter_order_processing_date_to']) ){
            $filter_order_processing_date_to = $this->request['filter_order_processing_date_to'];
          } else {
            $filter_order_processing_date_to = NULL;
          }

         if( !empty($this->request['filter_order_amount_from']) ){
           $filter_order_amount_from = $this->request['filter_order_amount_from'];
         } else {
         $filter_order_amount_from = NULL;
          }

         if( !empty($this->request['filter_order_amount_to']) ){
           $filter_order_amount_to = $this->request['filter_order_amount_to'];
          } else {
           $filter_order_amount_to = NULL;
          }

         if( !empty($this->request['edit_type_status'])  ){
          $edit_type_status = $this->request['edit_type_status'];
          } else {
          $edit_type_status = null;
         }

         if ( !empty($this->request['sort']) ) {
          $sort = $this->request['sort'];
         } else {
          $sort = 'order_processing_date';
         }

        if ( !empty($this->request['order']) ) {
            $order = $this->request['order'];
         } else {
          $order = 'DESC';
         }

         if ( !empty($this->request['start']) ) {
          $start = $this->request['start'];
          } else {
           $start = 0;
          }

          if( !empty($this->request['limit'])  ){
              $limit = $this->request['limit'];
          } else {
              $limit = $this->record_limits;
          }           

         $filter_data = array(
              'filter_order_no_requested'        => $filter_order_no_requested,
              'filter_order_processing_date_from'=> $filter_order_processing_date_from,
              'filter_order_processing_date_to'  => $filter_order_processing_date_to,
              'filter_order_amount_from'         => $filter_order_amount_from,
              'filter_order_amount_to'           => $filter_order_amount_to,
              'edit_type_status'                 => $edit_type_status,
              'sort'                             => $sort,
              'order'                            => $order,
              'start'                            => $start,
              'limit'                            => $limit,
              'suborder_id'                      => $suborder_id
            );

          $seller_id = $this->customer->getId();
          $getPickpupOrderRequested = $this->_obj_order_stores->getPickpupOrderRequested( $this->db, $seller_id, $filter_data);

          $total_results = $this->_obj_order_stores->getTotalOrderPickpupRequested( $this->db, $seller_id, $filter_data);

        
         if( !empty($getPickpupOrderRequested) ){
          foreach($getPickpupOrderRequested as $order_id_key => $orders_data ){

           foreach($orders_data as $pickuporder_value){
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

               // enable or disable seller edited after generating invoice
              $data['product_received'] = true;
             $pickup_status = $this->_obj_order_stores->getSellerInvoiceGenerated($this->registry, $this, $pickuporder_value['order_id'], $pickuporder_value['suborder_id'], $seller_id);

              $pickup_requested[] = array(
                    'id'              => $pickuporder_value['suborder_id'],
                    'order_id'        => $pickuporder_value['order_id'],
                    'suborder_id'         => $pickuporder_value['suborder_id'],
                    'order_no'        => $pickuporder_value['order_no'],
                    'order_processing_date' => $pickuporder_value['order_processing_date'], //$order_processing_date,
                    'date_ranges'     => $date_ranges,
                    'total'         => $this->currency->format($pickuporder_value['total'],'INR',1),
                    'edit_type_status'    => $pickuporder_value['edit_type_status'] ,
                    'pending_orders'    =>  (!empty($suborder_id)) ? $this->_obj_order_stores->getOrderProducts($this, $pickuporder_value['order_id'],  $pickuporder_value['suborder_id'], $seller_id, array('YES','SELLER_LATER_DISPATCH')) : '',
                    'seller_not_given'    =>  (!empty($suborder_id)) ? $this->_obj_order_stores->getOrderProducts($this, $pickuporder_value['order_id'], $pickuporder_value['suborder_id'],$seller_id, array('SELLER_NOT_SUPPLIED')) : '',
                   'sllr_invc_genrted'   =>  (!empty($suborder_id)) ? $this->_obj_order_stores->getSellerInvoiceGenerated($this->registry,$this, $pickuporder_value['order_id'],$pickuporder_value['suborder_id'],$seller_id) : '',
               );

               }
              }
             }
        
          $this->data_packet->data        = $pickup_requested;
          $this->data_packet->totalRecord    =  $total_results;
          $this->data_packet->statusCode  = 200;
          return $this->data_packet;
        }


       public function getPickupOrderDone() 
        { 
          $order_pickup_data = array();
          $total_results    = 0;

           $order_pickup_data = array();

           if( !empty($this->request['id']) ){
             $suborder_id = $this->request['id'];
            } else {
            $suborder_id = NULL;
           }

            if( !empty($this->request['filter_month_range']) ){
              $filter_month_range = $this->request['filter_month_range'];
            } else {
              $filter_month_range = NULL;
            }

            if( !empty($this->request['filter_year_range']) ){
              $filter_year_range = $this->request['filter_year_range'];
            } else {
              $filter_year_range = NULL;
            }

            if( !empty($this->request['filter_order_no']) ){
              $filter_order_no = $this->request['filter_order_no'];
            } else {
              $filter_order_no = NULL;
            }

            if( !empty($this->request['filter_invoice_date_from']) ){
              $filter_invoice_date_from = $this->request['filter_invoice_date_from'];
            } else {
              $filter_invoice_date_from = NULL;
            }

            if( !empty($this->request['filter_invoice_date_to']) ){
              $filter_invoice_date_to = $this->request['filter_invoice_date_to'];
            } else {
              $filter_invoice_date_to = NULL;
            }

            if( !empty($this->request['filter_invoice_no']) ){
              $filter_invoice_no = $this->request['filter_invoice_no'];
            } else {
              $filter_invoice_no = NULL;
            }

            if( !empty($this->request['filter_sale_from']) ){
              $filter_sale_from = $this->request['filter_sale_from'];
            } else {
              $filter_sale_from = NULL;
            }

            if( !empty($this->request['filter_sale_to']) ){
              $filter_sale_to = $this->request['filter_sale_to'];
            } else {
              $filter_sale_to = NULL;
            }

            if( !empty($this->request['filter_payment_status'])  ){
              $filter_payment_status = $this->request['filter_payment_status'];
            } else {
              $filter_payment_status = NULL;
            }

            
            if ( !empty($this->request['sort']) ) {
              $sort = $this->request['sort'];
            } else {
              $sort = 'o.order_id';
            }

            if ( !empty($this->request['order']) ) {
              $order = $this->request['order'];
            } else {
              $order = 'DESC';
            }

            if ( !empty($this->request['start']) ) {
              $start = $this->request['start'];
            } else {
              $start = 0;
            }

            if( !empty($this->request['limit'])  ){
              $limit = $this->request['limit'];
            } else {
              $limit = $this->record_limits;
            }

            if ( !empty($this->request['download_pickup_done_report']) ) {
              $download_pickup_done_report = $this->request['download_pickup_done_report'];
            } else {
              $download_pickup_done_report = false;
            }
            if ( !empty($this->request['filter_sor_record']) ) {
              $filter_sor_record = $this->request['filter_sor_record'];
            } else {
              $filter_sor_record = false;
            }


            $seller_id = $this->customer->getId();
            $filter_data = array();

            $filter_data = array(
              'filter_order_no'       => $filter_order_no,
              'filter_invoice_date_from'  => $filter_invoice_date_from,
              'filter_invoice_date_to'  => $filter_invoice_date_to,
              'filter_invoice_no'     => $filter_invoice_no,
              'filter_sale_from'      => $filter_sale_from,
              'filter_sale_to'      => $filter_sale_to,
              'filter_payment_status'     => $filter_payment_status,
              'sort'                    => $sort,
              'order'                   => $order,
              'start'                   => $start,
              'limit'                   => $limit,
              'download_pickup_done_report'=> $download_pickup_done_report,
              'filter_sor_record'       =>  $filter_sor_record,
              'suborder_id'             =>  $suborder_id
            );

           if(empty($suborder_id))
           {
            $order_pickup_data[0]=array('id'=>0, 'sale'=>'', 'seller_invoices'=>array(), 'return_amount'=>'', 'penalty_amount'=>'', 'net_payable_amount'=>'', 'trxn_done'=>'');
           } 

           $results = $this->_obj_order_stores->getOrderPickupDone( $this->db, $seller_id, $filter_data);

           $total_results = $this->_obj_order_stores->getTotalRecordPickupDone( $this->db, $seller_id, $filter_data);


          if( !empty($results) )
          {
          foreach ($results['records'] as $key => $result) {
            // calculate amount of debit note of perticular order id
            $total_amt_debit_note = 0; 
            if(!empty($results['debit_notes'][$result['suborder_id']])){
             foreach($results['debit_notes'][$result['suborder_id']] as $debit_note_values){
             $total_amt_debit_note += $debit_note_values['debit_note_amount'];
             }
           }

            //get seller invoice download link
            $seller_inv_dload_link = array();
            $check_trxn_done = false;
              if(!empty($results['seller_invoices'][$result['suborder_id']])){
                // array of seller invoice corresponding to order_id
                $seller_invoice_data = $results['seller_invoices'][$result['suborder_id']];
                    
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

            if( !empty($results['debit_notes'][$result['suborder_id']]) ){
              $debit_note_data = $results['debit_notes'][$result['suborder_id']];
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

             $net_payable_amount  = $result['sale_amt'] - $total_amt_debit_note ;
             $sor_invoice_no     = '';
             $sor_invoice_date     = '';
             $paymentToolTip     = '';
              $sor_payment      = array();  

              if (isset($result['sor_invoice_no'])) {

              $sor_invoice_no     = $result['sor_invoice_no'];
              $sor_payment  = $this->_obj_order_stores->getSorOrderPayments($this->db, $result['order_id'], $seller_id );
               }
              if (isset($result['sor_invoice_date'])) {
               $sor_invoice_date     = $result['sor_invoice_date'];
               }
              /*
                * set the string for payment tool tip
              */
        if(!empty($results['seller_invoices'][$result['suborder_id']])){
          
          $paymentToolTip .= '<div class="paid_detail">';
                $paymentToolTip .= '<ul>';

          foreach ($results['seller_invoices'][$result['suborder_id']] as $keyse => $valuese) {
            
            if($valuese['trxn_done'] == 'BANK_REQUESTED' || $valuese['trxn_done'] == 'BANK_SUCCESS' || $valuese['trxn_done'] == 'NOT_APPLICABLE'){

                      $paymentToolTip .=     '<li>Inv. No. ' . $valuese["seller_invoice_prefix"].''.$valuese["seller_invoice_no"] . '</li>';
                      $paymentToolTip .=     '<li>Amount. ' . (($valuese["trxn_amount"]) ? $valuese["trxn_amount"] : ''). '</li>';
                      
                      if($valuese["trxn_utr"]!='' && $valuese["trxn_utr_date"]!=''){
                         
                          $paymentToolTip .=     '<li>Ref. ' . (($valuese["trxn_utr"]) ? $valuese["trxn_utr"] : '' ).'</li>';
                          $paymentToolTip .=     '<li>Date. ' . (($valuese["trxn_utr_date"]) ? $valuese["trxn_utr_date"] : '').'</li>';
                      }
                  }
                }  

        }

        if(!empty($sor_payment)){
          /*if ($paymentToolTip=='') {
            $paymentToolTip .= '<div class="paid_detail">';
                  $paymentToolTip .= '<ul>';
          }*/
          foreach ($sor_payment as $keysor => $valuesor) {
            if ($valuesor['trxn_done'] == 'BANK_REQUESTED' || $valuesor['trxn_done'] == 'BANK_SUCCESS' || $valuesor['trxn_done'] == 'NOT_APPLICABLE') {
              $check_trxn_done = true;
                      /*$paymentToolTip .=     '<li>Sub Order. No. ' . $valuesor["suborder_id"].'</li>';
                      $paymentToolTip .=     '<li>Amount. ' . (($valuesor["total_paid_amount"]) ? $valuesor["total_paid_amount"] : ''). '</li>';
                      
                      if($valuesor["trxn_utr"]!='' && $valuesor["trxn_utr_date"]!=''){
                         
                          $paymentToolTip .=     '<li>Ref. ' . (($valuesor["trxn_utr"]) ? $valuesor["trxn_utr"] : '' ).'</li>';
                          $paymentToolTip .=     '<li>Date. ' . (($valuesor["trxn_utr_date"]) ? $valuesor["trxn_utr_date"] : '').'</li>';
                      }*/

                  }
          }
        }

        /*if ($paymentToolTip!='') {

                  $paymentToolTip .= '</ul>';
                  $paymentToolTip .= '</div>'; 
        }*/

        /*
        * setting all data in array
        */
        $order_pickup_data[] = array(
          'id'          => $result['suborder_id'],
          'order_id'    => $result['order_id'],
          'order_no'    => $result['order_no'],
          'suborder_id'     => $result['suborder_id'],
          'order_date_added' => (!empty($results['order_process_lists'][$result['order_id']]) ? $results['order_process_lists'][$result['order_id']]['order_processing_date'] : $result['order_date']),
          'sale'      => number_format($result['sale_amt'],2),
          'seller_invoices'=> (!empty($results['seller_invoices'][$result['suborder_id']]) ? $results['seller_invoices'][$result['suborder_id']] : array()),
          'debit_notes'=> (!empty($results['debit_notes'][$result['suborder_id']]) ? $results['debit_notes'][$result['suborder_id']] : array()),
          'return_amount' => number_format($total_amt_debit_note,2),
          'penalty_amount'=> 0,
          'net_payable_amount' => number_format($net_payable_amount,2),
          'trxn_done'   => ($check_trxn_done) ? 'Paid' : ((!$net_payable_amount) ? 'Full Returned' : 'none'),
          'product_data'  => $this->_obj_order_stores->getOrderProducts($this, $result['order_id'], $result['suborder_id'], $seller_id, array() ),
          'seller_invoice_link' => $seller_inv_dload_link,
          'debit_note_link'=> $debit_note_url,
          'sllr_invc_genrted' =>  $this->_obj_order_stores->getSellerInvoiceGenerated($this->registry, $this, $result['order_id'], $result['suborder_id'], $seller_id ),
          'seller_sor_product'  =>  $this->_obj_order_stores->getOrderProducts($this, $result['order_id'],$result['suborder_id'], $seller_id, array(),'','get_sor'),
          'sor_invoice_no'=>$sor_invoice_no,
          'sor_invoice_date'=>$sor_invoice_date,
          'payment_tool_tip'=>$paymentToolTip,
        );
            }
          }

          $this->data_packet->data           = $order_pickup_data;
          $this->data_packet->totalRecord    =  $total_results;
          $this->data_packet->statusCode     = 200;
          return $this->data_packet;
        }



       public function getTentativeOrders() 
        {
          $tentative_order = array();
          $total_tentative_order    = 0;

            $tentative_order = array();
            if ( !empty($this->request['id']) ) {
              $suborder_id = $this->request['id'];
            } else {
              $suborder_id = null;
            }
            
            if ( !empty($this->request['sort']) ) {
              $sort = $this->request['sort'];
            } else {
              $sort = 'o.order_id';
            }

            if ( !empty($this->request['order']) ) {
              $order = $this->request['order'];
            } else {
              $order = 'DESC';
            }

            if ( !empty($this->request['start']) ) {
              $start = $this->request['start'];
            } else {
              $start = 0;
            }

            if( !empty($this->request['limit'])  ){
              $limit = $this->request['limit'];
            } else {
              $limit = $this->record_limits;
            }

            $this->load->model('localisation/order_status');
            $seller_id = $this->customer->getId();
            $filter_data = array();

            $filter_data = array(
             'sort' => $sort,
             'order'=> $order,
             'start'=> $start,
             'limit'=> $limit,
             'suborder_id'=>$suborder_id
            );


          $tentative_order_arr = $this->_obj_order_stores->getOrderTentative( $this->db, $seller_id, $filter_data);

          $total_tentative_order = $this->_obj_order_stores->getTotalOrderTentative( $this->db, $seller_id, $filter_data);


          $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();
           $order_statuses = array();
           foreach($order_statuses_qry as $order_status_data){
            $order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
            }


       if( !empty($tentative_order_arr) ){
        foreach($tentative_order_arr as $tentative_value ){
        $tentative_order[] = array(
          'id'       => $tentative_value['suborder_id'], 
          'order_no' => $tentative_value['order_no'],
          'order_date_added' => $tentative_value['order_date_added'],
          'order_status' => $order_statuses[$tentative_value['order_status_id']],
          'total' => $tentative_value['total'],
          'product_data' => $this->_obj_order_stores->getOrderProducts($this, $tentative_value['order_id'],$tentative_value['suborder_id'], $seller_id, array('edit_type'=>'Yes') ),         
          );
        }
       }

          $this->data_packet->data           = $tentative_order;
          $this->data_packet->totalRecord    =  $total_tentative_order;
          $this->data_packet->statusCode     = 200;
          return $this->data_packet;
        }


       public function getSorOrders() 
        {

           $order_pickup_data = array();
           $total_results     = 0;
         
         $order_pickup_data = array();

         if( !empty($this->request['filter_month_range']) ){
             $filter_month_range = $this->request->get['filter_month_range'];
          } else {
             $filter_month_range = NULL;
          }

          if( !empty($this->request['filter_year_range']) ){
            $filter_year_range = $this->request['filter_year_range'];
           } else {
            $filter_year_range = NULL;
           }

            if (!empty($this->request['id']) ) {
              $suborder_id = $this->request['id'];
            } else {
              $suborder_id = null;
            }           

           if( !empty($this->request['filter_order_no']) ){
             $filter_order_no = $this->request['filter_order_no'];
             } else {
              $filter_order_no = NULL;
            }

           if( !empty($this->request['filter_invoice_date_from']) ){
             $filter_invoice_date_from = $this->request['filter_invoice_date_from'];
            } else {
             $filter_invoice_date_from = NULL;
            }

          if( !empty($this->request['filter_invoice_date_to']) ){
            $filter_invoice_date_to = $this->request['filter_invoice_date_to'];
           } else {
            $filter_invoice_date_to = NULL;
           }

          if( !empty($this->request['filter_invoice_no']) ){
            $filter_invoice_no = $this->request['filter_invoice_no'];
           } else {
            $filter_invoice_no = NULL;
           }

          if( !empty($this->request['filter_sale_from']) ){
           $filter_sale_from = $this->request['filter_sale_from'];
          } else {
           $filter_sale_from = NULL;
           }

           if( !empty($this->request['filter_sale_to']) ){
           $filter_sale_to = $this->request['filter_sale_to'];
           } else {
           $filter_sale_to = NULL;
           }

          if( !empty($this->request['filter_payment_status'])  ){
            $filter_payment_status = $this->request['filter_payment_status'];
          } else {
            $filter_payment_status = NULL;
          }

          if ( !empty($this->request['download_pickup_done_report']) ) {
              $download_pickup_done_report = $this->request['download_pickup_done_report'];
          } else {
              $download_pickup_done_report = false;
          }           


          if ( !empty($this->request['sort']) ) {
              $sort = $this->request['sort'];
          } else {
              $sort = 'o.order_id';
          }

          if ( !empty($this->request['order']) ) {
              $order = $this->request['order'];
          } else {
              $order = 'DESC';
          }

          if ( !empty($this->request['start']) ) {
              $start = $this->request['start'];
          } else {
              $start = 0;
          }

          if( !empty($this->request['limit'])  ){
              $limit = $this->request['limit'];
          } else {
              $limit = $this->record_limits;
          }

          $this->load->model('localisation/order_status');
          $seller_id = $this->customer->getId();
          $filter_data = array();

          $filter_data = array(
              'filter_order_no'       => $filter_order_no,
              'filter_invoice_date_from'  => $filter_invoice_date_from,
              'filter_invoice_date_to'  => $filter_invoice_date_to,
              'filter_invoice_no'     => $filter_invoice_no,
              'filter_sale_from'      => $filter_sale_from,
              'filter_sale_to'      => $filter_sale_to,
              'filter_payment_status'     => $filter_payment_status,
              'sort'                    => $sort,
              'order'                   => $order,
              'start'                   => $start,
              'limit'                   => $limit,
              'download_pickup_done_report'=> $download_pickup_done_report,
              'filter_sor_record'       => 'onlysor',
              'suborder_id'                => $suborder_id
          );

          if(empty($suborder_id))
           {
             $order_pickup_data[0]=array('id'=>0, 'sale'=>'', 'sor_invoices'=>array(), 'return_amount'=>'', 'penalty_amount'=>'', 'net_payable_amount'=>'', 'trxn_done'=>'none');
           }

          $results = $this->_obj_order_stores->getOrderSorProduct( $this->db, $seller_id, $filter_data);
            
          $total_results = $this->_obj_order_stores->getTotalRecordSorProduct( $this->db, $seller_id, $filter_data);
         

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

        $sor_invoice_no     = '';
        $sor_invoice_date     = '';
        $paymentToolTip     = '';
        $sor_payment        = array();

        if (isset($result['sor_invoice_no'])) {

          $sor_invoice_no     = $result['sor_invoice_no'];
          $sor_payment  = $this->_obj_order_stores->getSorOrderPayments($this->db, $result['order_id'], $seller_id );
        }
        if (isset($result['sor_invoice_date'])) {

          $sor_invoice_date     = $result['sor_invoice_date'];
        }
        /*
        * set the string for payment tool tip
        */
        if(!empty($results['seller_invoices'][$result['order_id']])){
          
          $paymentToolTip .= '<div class="paid_detail">';
                $paymentToolTip .= '<ul>';

          foreach ($results['seller_invoices'][$result['order_id']] as $keyse => $valuese) {
            
            if($valuese['trxn_done'] == 'BANK_REQUESTED' || $valuese['trxn_done'] == 'BANK_SUCCESS' || $valuese['trxn_done'] == 'NOT_APPLICABLE'){

                      $paymentToolTip .=     '<li>Inv. No. ' . $valuese["seller_invoice_prefix"].''.$valuese["seller_invoice_no"] . '</li>';
                      $paymentToolTip .=     '<li>Amount. ' . (($valuese["trxn_amount"]) ? $valuese["trxn_amount"] : ''). '</li>';
                      
                      if($valuese["trxn_utr"]!='' && $valuese["trxn_utr_date"]!=''){
                         
                          $paymentToolTip .=     '<li>Ref. ' . (($valuese["trxn_utr"]) ? $valuese["trxn_utr"] : '' ).'</li>';
                          $paymentToolTip .=     '<li>Date. ' . (($valuese["trxn_utr_date"]) ? $valuese["trxn_utr_date"] : '').'</li>';
                      }
                  }
                }  

        }

        if(!empty($sor_payment)){

          /*if ($paymentToolTip=='') {

            $paymentToolTip .= '<div class="paid_detail">';
                  $paymentToolTip .= '<ul>';
          }*/
          foreach ($sor_payment as $keysor => $valuesor) {
            if (($valuesor['trxn_done'] == 'BANK_REQUESTED' || $valuesor['trxn_done'] == 'BANK_SUCCESS' || $valuesor['trxn_done'] == 'NOT_APPLICABLE') && $valuesor['suborder_id'] == $result['suborder_id']) {
              $check_trxn_done = true;
              /*$paymentToolTip .=     '<li>Sub Order. No. ' . $valuesor["suborder_id"].'</li>';
                      $paymentToolTip .=     '<li>Amount. ' . (($valuesor["total_paid_amount"]) ? $valuesor["total_paid_amount"] : ''). '</li>';
                      
                      if($valuesor["trxn_utr"]!='' && $valuesor["trxn_utr_date"]!=''){
                         
                          $paymentToolTip .=     '<li>Ref. ' . (($valuesor["trxn_utr"]) ? $valuesor["trxn_utr"] : '' ).'</li>';
                          $paymentToolTip .=     '<li>Date. ' . (($valuesor["trxn_utr_date"]) ? $valuesor["trxn_utr_date"] : '').'</li>';
                      }*/
                  }
          }
        }
        /*if ($paymentToolTip!='') {

                  $paymentToolTip .= '</ul>';
                  $paymentToolTip .= '</div>'; 
        }*/
        /*
        * set sor data
        */
        if($check_trxn_done)
        {
          $order_status_name = 'Paid';
        }
        else if ((int)$result['order_status_id'] == (int)ORDER_STATUS['Canceled']) 
        {
          $order_status_name = 'Cancelled';
        }
        else if (!$check_trxn_done && $result['order_status_id'] == (int)ORDER_STATUS['Delivered']) 
        {
          $order_status_name = 'Payment Pending';
        }
        else
        {
           $order_status_name = $result['order_status_name'];
        }


        $order_pickup_data[] = array(
          'id'          => $result['suborder_id'],
          'order_id'    => $result['order_id'],
          'order_no'    => $result['order_no'],
          'suborder_id'   => $result['suborder_id'],
          'order_processing_date'=>$result['order_processing_date'],
          'order_date_added' => (!empty($results['order_process_lists'][$result['order_id']]) ? $results['order_process_lists'][$result['order_id']]['order_processing_date'] : $result['date_added']),
          'sale_amt'      => number_format($result['sale_amt'],2),
          'debit_notes'=> (!empty($results['debit_notes'][$result['order_id']]) ? $results['debit_notes'][$result['order_id']] : array()),
          'return_amount' => number_format($total_amt_debit_note,2),
          'penalty_amount'=> 0,
          'net_payable_amount' => number_format($result['sale_amt'] - $total_amt_debit_note,2),
          'trxn_done'   => ($check_trxn_done) ? 'Paid' : '',
          'seller_invoice_link' => $seller_inv_dload_link,
          'debit_note_link'=> $debit_note_url,
          'sllr_invc_genrted' => (!empty($suborder_id)) ? $this->_obj_order_stores->getSellerInvoiceGenerated($this->registry, $this, $result['order_id'], $result['suborder_id'], $seller_id ) : '',
          'seller_sor_product'  =>  (!empty($suborder_id)) ? $this->_obj_order_stores->getSorSubOrderProducts($this, $result['order_id'],$seller_id) : '',
          'sor_invoice_no'=>$sor_invoice_no,
          'sor_invoice_date'=>$sor_invoice_date,
          'sor_invoices' => array(0=>array('sor_invoice_no'=>$sor_invoice_no, 'order_processing_date'=>$result['order_processing_date'])),
          //'payment_tool_tip'=>$paymentToolTip,
          'order_status_id'=> $result['order_status_id'],
          'order_status_name'=> $order_status_name
        );
       }
     }   


      $this->data_packet->data           = $order_pickup_data;
      $this->data_packet->totalRecord    =  $total_results;
      $this->data_packet->statusCode     = 200;
      return $this->data_packet;

  }


  public function splitOrderProducts(){
    
    $json = array();
    
    $changes_obj        = json_decode(str_replace('&quot;', '"', $this->request['changes']));
    $seller_approved    = array_filter(explode(",", $this->request['SELLER_APPROVED']));
    $seller_partial     = array_filter(explode(",", $this->request['SELLER_PARTIAL']));
    $seller_not_supplied= array_filter(explode(",", $this->request['SELLER_NOT_SUPPLIED']));
    $seller_latter_dispatch = array_filter(explode(",", $this->request['SELLER_LATER_DISPATCH']));
    $product_ids        = array_filter(explode(",", $this->request['product_ids']));

    foreach($changes_obj as $key => $value)
    {
      foreach($value as $key2 => $value2)
      {
        $changes[$key][$key2]['product_id'] = $value2->product_id;
        $changes[$key][$key2]['value'] = $value2->value;
      }
    }
  
    $order_product_ids = array_keys($changes);
    $seller_id  = $this->customer->getId();
    $nick_name = SellerInfo::getSellerFirmDetails($this->db, $seller_id)['nickname'];
    $json = array('error'=>0);
 
    // get a single record of order_id, suborder_id, and seller_id corresponding to order_product_id 
    $same_order_id_and_suborder_id = $this->_obj_order_stores->getSameOrderIdandSubOrderIdandSellerId($this->db, $order_product_ids);


    if(empty($same_order_id_and_suborder_id))
     {
        $json['error_msg'] =  "Something went wrong. Please try again!"; 
        $json['error'] =  4; 
     }

    if( $same_order_id_and_suborder_id['seller_id'] != $this->customer->getId() )
    {
      $json['error_msg'] =  'You can not access another seller account at the same time ! Please try again after login.'; 
      $json['redirect_link'] =  $this->url->link('account/logout', '', 'SSL'); 
      $json['error'] =  2; 
    }

    $order_id   = (int)$same_order_id_and_suborder_id['order_id'];
    $suborder_id= $same_order_id_and_suborder_id['suborder_id'];

    if( $order_id && $suborder_id && $seller_id )
    {
      $checkOrderProductIds = $this->_obj_order_stores->checkOrderProductIdsIsAvailableOrNot( $this->db, $order_product_ids );
      if(!$checkOrderProductIds){
        $json['error_msg'] =  "Something went wrong. Please try again.!"; 
        $json['error'] =  4; 
      }
    }

    if(count($seller_approved) > 0 || count($seller_partial) > 0)
    { 
      $invoice_no = trim($this->request['invoice_number']);
      $invoice_date = $this->request['invoice_date'];
      // regex for invoice no
      if(!preg_match("/^[A-Za-z0-9\/-]{1,16}$/", $invoice_no, $output_array))
      {
        $json['error_msg'] =  'Invalid Invoice number format !! Invoice number can only be 16 characters long; can contain either alphabets (a-z, A-Z), digits (0-9), hyphen (-), and/or forward slash (/).'; 
        $json['error'] =  3; 
      }
      else if(empty($invoice_date))
      {
        $json['error_msg'] =  'Please select invoice date.'; 
        $json['error'] =  4; 
      } 
      else
      {
        $invoice_no_allowed = $this->_obj_order_stores->checkSellerInvoiceNoIsUnique( $this->db, $seller_id , $invoice_no );
        if( !$invoice_no_allowed )
        {
          $json['error_msg'] =  "This invoice number is already used! Please enter new invoice number."; 
          $json['error'] =  1; 
        }
        else
        {
          $obj = new SellerInvoice($this->registry);
          $file_name = $obj->generateInvoiceNo($order_id, $suborder_id, $seller_id, $invoice_no, $invoice_date);
          $seller_invoice_id = 0;
          $seller_invoice_id = (int)$file_name['seller_invoice_id'];
          $file_name = base64_encode(serialize($file_name));
           $json['link'] = $this->securefiledownload->getDownloadLink('seller_invoice',$file_name,false);
           $json['error'] = 0 ;
         } 
       } 
     }     
      
      
     if($json['error'] == 0) 
     {
          foreach( $changes as $order_product_id_key => $values ){
            // $order_product_id_array[] = $order_product_id_key;
              $edit_history = array(
              'user_id'   => $seller_id,
              'user_name'   => $nick_name,
              'user_type' => 'seller',
              'user_ip'    => $this->getIpAddress,
              'user_agent'=> $_SERVER['HTTP_USER_AGENT'],
              'date_added'=> date('d-m-Y H:i:s'),
              'comment'   => key($values)
             );
   
             $details = array(
              'edit_type'   => $values,
              'edit_history'  => $edit_history
              );

            if(in_array(key($values), array('SELLER_APPROVED'))){
              $this->_obj_order_stores->updateEditTypeBySeller( $this->db, $order_product_id_key, $details, $seller_id, $seller_invoice_id);

            } else if(in_array(key($values), array('SELLER_NOT_SUPPLIED','SELLER_LATER_DISPATCH'))){
              $this->_obj_order_stores->updateEditTypeBySeller( $this->db, $order_product_id_key, $details, $seller_id, '');

            } else if(in_array(key($values), array('SELLER_PARTIAL'))) {

              $edit_history['comment'] = 'SELLER_NOT_SUPPLIED';
              $details = array(
              'edit_type'   => 'SELLER_NOT_SUPPLIED',
              'edit_history'  => $edit_history
              );        
              
              $details['quantity']      = $values['SELLER_PARTIAL']['value'];  
              $details['old_edit_type'] = 'SELLER_PARTIAL';
              OrderEdit::splitOrderProduct( $this->db, $order_product_id_key, $details, $seller_invoice_id);           
            }
          }   
          //Update order and suborder total
          OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $suborder_id);
           $this->_obj_order_stores->SendNotificationToSalesStaff($this, $order_id);
         
      }

      $this->data_packet->data    = $json;
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
    }


   public function sellerEditedAfterGeneratingInvoice(){

    $result = array("error"=>0, "error_msg"=>"");

      $order_id     = $this->request['order_id'];
      $suborder_id  = $this->request['suborder_id'];
      $changes_obj  = json_decode(str_replace('&quot;', '"', $this->request['changes']));
      $getSellerid  = ($this->request['customer_id']) ? $this->request['customer_id'] : 0 ;
      $seller_id    = $this->customer->getId();
      $invoice_no   = $this->request['invoice_number'];
      $invoice_date = $this->request['invoice_date'];
      $sllr_inv_id  = $this->request['seller_invoice_id'];

     if(!empty($changes_obj))
     { 
       foreach($changes_obj as $key => $value)
       {
         foreach($value as $key2 => $value2)
         {
           if(isset($value2->product_id) && isset($value2->value) && isset($value2->seller_invoice_id))
           {
             $changes[$key][$key2]['product_id'] = $value2->product_id;
             $changes[$key][$key2]['value'] = $value2->value;
             $changes[$key][$key2]['seller_invoice_id'] = $value2->seller_invoice_id;
           }  
         }
       }
      }  
      // check invoice number when seller given new invoice number then check in database that new invoice number are already saved or Not. if new invoice number already saved in database then error message show.(Please enter new invoice number.)
      if($getSellerid != $seller_id){
        $$result['error_msg'] =  'You can not access another seller account at the same time ! Please try again after login.'; 
        $result['error'] =  1; 
      }


     $nick_name = SellerInfo::getSellerFirmDetails($this->db, $seller_id)['nickname'];
      if($result['error'] == 0 && !empty($changes))
      {
          $updateInvoiceData = array(
                        'invoice_no'  => $invoice_no,
                        'invoice_date'=> $invoice_date,
                        'sllr_inv_id' => $sllr_inv_id,
                        'seller_id'   => $seller_id 
                      );
        

        if(!preg_match("/^[A-Za-z0-9\/-]{1,16}$/", $invoice_no))
         {
            $result['error_msg'] =  'Invalid Invoice number format !! Invoice number can only be 16 characters long; can contain either alphabets (a-z, A-Z), digits (0-9), hyphen (-), and/or forward slash (/).'; 
           $result['error'] =  1; 
         } 
         else
         {
            $invoice_no_allowed = $this->_obj_order_stores->sellerUpdateInvoiceNoAndDateAfterGeneratingInvoice($this->db, $updateInvoiceData );
         
             if($invoice_no_allowed )
             {
               foreach( $changes as $order_product_id_key => $values ){
                 $edit_history = array(
                 'user_id'    => $seller_id,
                 'user_name'  => $nick_name,
                 'user_type'  => 'seller',
                 'user_ip'    => $_SERVER['REMOTE_ADDR'],
                 'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                 'date_added' => date('d-m-Y H:i:s'),
                 'comment'    => key($values),
                 'edited'     => 'seller_edited_after_generating_invoice'
                );

                $details = array(
                 'edit_type'   => $values,
                 'edit_history'  => $edit_history
                );
                 $this->_obj_order_stores->sellerEditedAfterGeneratingInvoice( $this->db, 
                                          $order_id, 
                                          $suborder_id, 
                                          $order_product_id_key, 
                                          $details, $seller_id
                                        );          
               }

               $this->_obj_order_stores->updateSellerInvoiceForNoGoods($this->db, $updateInvoiceData );
               //Update order and suborder total
               OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $suborder_id);
              }
              else
              {
                 $result['error_msg'] =  "This invoice number is already used! Please enter new invoice number."; 
                 $result['error'] =  1; 
              } 
          }    

      } else {
          $result['error'] = 1;
          $result['error_msg'] = 'Please select any actions!';
        }
    

      $this->data_packet->data  = $result;
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;   

  }
    

  public function revertGoodsBySeller(){
      $order_product_ids = array_filter(explode(",", $this->request['order_product_ids']));
      $seller_id         = $this->request['seller_id'];
      $order_id          = $this->request['order_id'];
      $suborder_id       = $this->request['suborder_id'];

      $return_value  = $this->_obj_order_stores->revertGoodsBySeller($this->db, $order_product_ids, $seller_id, $order_id, $suborder_id);    
      $json = array();
      if($return_value == 1){
        $json['error'] = 1;
        $json['message'] = 'Dear Seller, You cannot revert now. Order has been invoiced to customer already, hence marked closed.';
      }
      else
      {
        $json['error'] = 0;
        $json['message'] = 'Order revert successfully.';
      }

      $this->data_packet->data = $json;
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;

    }

}