<?php
     require_once('system.php');
     require_once( DIR_SYSTEM . 'library/currency.php' );
     require_once( DIR_SYSTEM . 'library/operations/returns/return_action_clusters.php' );
     require_once( DIR_SYSTEM . 'library/seller/returns.php' );
     require_once( DIR_SYSTEM . 'library/securefiledownload.php' );
     require_once( DIR_SYSTEM . 'library/product.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );

    class ReturnsController extends SystemController
    {
        private $error = array();
        private $_obj_order_stores;
        private $record_limits = 10;

        public function __construct($params) {

            parent::__construct($params);

            $this->_obj_returns = new Returns();
            // Currency
            $this->registry->set('currency', new Currency($this->registry));
            
            // SecureFileDownload                                        
            $this->registry->set('securefiledownload', new SecureFileDownload($this->registry));

            // product      
            $product = new Product($this->registry);
            $this->registry->set('product', $product);
        }
        /**
         * customer login
         */
      private function __setReturnFilters($return_action_id, $return_view_type)
       {
          if( !empty($this->request['id']) ){
            $id = explode("-", $this->request['id']);
            $master_return_id  = $id[0];
            $seller_invoice_id = $id[1];
            $return_view_type  = $id[2];
          } else {
            $master_return_id = NULL;
            $seller_invoice_id= NULL;
          }

          if( !empty($this->request['filter_order_no']) ){
            $filter_order_no = $this->request['filter_order_no'];
          } else {
            $filter_order_no = NULL;
          }

          if( !empty($this->request['debit_note_no']) ){
            $debit_note_no = $this->request['debit_note_no'];
          } else {
            $debit_note_no = NULL;
          }

          if( !empty($this->request['debit_note_date_from']) ){
            $debit_note_date_from = $this->request['debit_note_date_from'];
          } else {
            $debit_note_date_from = NULL;
          }

          if( !empty($this->request['debit_note_date_to']) ){
            $debit_note_date_to = $this->request['debit_note_date_to'];
          } else {
            $debit_note_date_to = NULL;
          }

          if ( !empty($this->request['sort']) ) {
            $sort = $this->request['sort'];
          } else {
            $sort = 'oo.order_id';
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
          'return_view_type'             => $return_view_type,
          'master_return_id'             => $master_return_id,
          'seller_invoice_id'            => $seller_invoice_id,
          'filter_order_no'              => $filter_order_no,
          'return_action_id'             => $return_action_id,
          'debit_note_no'                => $debit_note_no,
          'debit_note_date_from'         => $debit_note_date_from, 
          'debit_note_date_to'           => $debit_note_date_to, 
          'sort'                         => $sort,
          'order'                        => $order,
          'start'                        => $start,
          'limit'                        => $limit
          );

         return $filter_data;
       }

       public function getReturnOrders($return_action_id, $return_view_type) 
        {
          $total_results = 0;
          $return_order_data = array();

          $return_order_data = array();
          $filter_data = $this->__setReturnFilters($return_action_id, $return_view_type);
          $seller_id = $this->customer->getId();
          // for pickup requested
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

    $this->data_packet->data           = $return_order_data;
    $this->data_packet->totalRecord    =  $total_results;
  }

  public function product_list() 
        { 
        $total_results = 0;
        $product_record_array = array();

          $product_record_array = array();
          if( !empty($this->request['id']) ){
            $id = explode("-", $this->request['id']);
            $master_return_id  = $id[0];
            $seller_invoice_id = $id[1];
          } else {
            $master_return_id = NULL;
            $seller_invoice_id= NULL;
          }

          if( !empty($this->request['order_product_ids']) ){
            $order_product_ids = $this->request['order_product_ids'];
          } else {
            $order_product_ids = NULL;
          }  

          if ( !empty($this->request['sort']) ) {
            $sort = $this->request['sort'];
          } else {
            $sort = 'debit_note_date';
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
          'master_return_id'      => $master_return_id,
          'order_product_ids'     => $order_product_ids,
          'sort'                  => $sort,
          'order'                 => $order,
          'start'                 => $start,
          'limit'                 => $limit
        );
    
     $seller_id = $this->customer->getId();
    // for pickup requested
    $getReturnProductDetails = $this->_obj_returns->getReturnProductDetails( $this, $seller_id, $filter_data);
          
    $product_record_array = $getReturnProductDetails[0];
    $total_results       = $getReturnProductDetails[1];
      
     $this->load->model('tool/image'); 

     $product_images = $this->product->getOrderProductOptionImages($this->db, explode(",", $order_product_ids), 1);
      
      foreach($product_images as $key => $image)
      {
         $product_record_array[$key]['image'] = $this->model_tool_image->resize($image,
          $this->config->get('config_image_additional_width'),
          $this->config->get('config_image_additional_height')
                                                                        );
         $product_record_array[$key]['mobile_image'] = $this->model_tool_image->resizeBasedOnLargeDimension($image, '400');
      }

    $this->data_packet->data           = array_values($product_record_array);
    $this->data_packet->totalRecord    =  $total_results;
    $this->data_packet->statusCode     = 200;
    return $this->data_packet;

  }

  public function getTentativeReturns()
   {
      $return_action_id = implode(",", SELLER_PANEL_TENTATIVE_RETURNS);
      $return_view_type = 'tentative';
      $this->getReturnOrders($return_action_id, $return_view_type);
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
   }

  public function getApprovedReturns()
   {
      $return_action_id = implode(",", SELLER_PANEL_APPROVED_RETURNS);
      $return_view_type = 'approved';
      $this->getReturnOrders($return_action_id, $return_view_type);
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
   }

  public function getDeliveredReturns()
   {
      $return_action_id = implode(",", array_merge(SELLER_PANEL_TENTATIVE_RETURNS, SELLER_PANEL_APPROVED_RETURNS, SELLER_PANEL_DISPUTED_RETURNS));
      $return_view_type = 'delivered';
      $this->getReturnOrders($return_action_id, $return_view_type);
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
   }

  public function getDisputeReturns()
   {
      $return_action_id = implode(",", SELLER_PANEL_DISPUTED_RETURNS);
      $return_view_type = 'disputed';
      $this->getReturnOrders($return_action_id, $return_view_type);
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
   }

 }