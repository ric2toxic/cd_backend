<?php
     require_once('system.php');
     require_once( DIR_SYSTEM . 'library/currency.php' );
     require_once( DIR_SYSTEM . 'library/operations/returns/return_action_clusters.php' );
     require_once( DIR_SYSTEM . 'library/seller/returns.php' );
     require_once( DIR_SYSTEM . 'library/securefiledownload.php' );
     require_once( DIR_SYSTEM . 'library/product.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );

    class ReplacementController extends SystemController
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
      private function __setReplaceFilter($replacement_action_id, $replacement_view_type)
       {
          if( !empty($this->request['id']) ){
            $id = explode("-", $this->request['id']);
            $master_return_id  = $id[0];
            $seller_invoice_id = $id[1];
            $replacement_view_type  = $id[2];
          } else {
            $master_return_id = NULL;
            $seller_invoice_id= NULL;
          }

          if( !empty($this->request['filter_order_no']) ){
            $filter_order_no = $this->request['filter_order_no'];
          } else {
            $filter_order_no = NULL;
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
          'replacement_view_type'        => $replacement_view_type,
          'master_return_id'             => $master_return_id,
          'seller_invoice_id'            => $seller_invoice_id,
          'filter_order_no'              => $filter_order_no,
          'replacement_action_id'        => $replacement_action_id,
          'sort'                         => $sort,
          'order'                        => $order,
          'start'                        => $start,
          'limit'                        => $limit
        );

        return $filter_data;

      }


  public function getReplacementOrders($replacement_action_id, $replacement_view_type) 
        {
          $total_results = 0;
          $replace_order_data = array();
 
          $replace_order_data = array();
          $filter_data = $this->__setReplaceFilter($replacement_action_id, $replacement_view_type);
          $seller_id = $this->customer->getId();
          // for pickup requested
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

              $replace_order_data[] = array(
                'id'                     => $replace_order['master_return_id'].'-'.
                                            $replace_order['seller_invoice_id'].'-'.
                                            $filter_data['replacement_view_type'],
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

    $this->data_packet->data           =  $replace_order_data;
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

    $this->data_packet->data           =  array_values($product_record_array);
    $this->data_packet->totalRecord    =  $total_results;
    $this->data_packet->statusCode      = 200;
    return $this->data_packet;

  }

  public function getTentativeReplacement()
   {
      $replacement_action_id = implode(",", SELLER_PANEL_TENTATIVE_REPLACEMENTS);
      $replacement_view_type = 'tentative';
      $this->getReplacementOrders($replacement_action_id, $replacement_view_type);
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
   }

  public function getApprovedReplacement()
   {
      $replacement_action_id = implode(",", SELLER_PANEL_APPROVED_REPLACEMENTS);
      $replacement_view_type = 'approved';
      $this->getReplacementOrders($replacement_action_id, $replacement_view_type);
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
   }

  public function getDeliveredReplacement()
   {
      $replacement_action_id = implode(",", SELLER_PANEL_DELIVERED_REPLACEMENTS);
      $replacement_view_type = 'delivered';
      $this->getReplacementOrders($replacement_action_id, $replacement_view_type);
      $this->data_packet->statusCode  = 200;
      return $this->data_packet;
   }

  public function getDisputeReplacement()
   {
      $replacement_action_id = implode(",", SELLER_PANEL_DISPUTED_REPLACEMENTS);
      $replacement_view_type = 'disputed';
      $this->getReplacementOrders($replacement_action_id, $replacement_view_type);
      $this->data_packet->statusCode  = 200;
       return $this->data_packet;
   }

}