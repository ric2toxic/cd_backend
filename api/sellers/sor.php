<?php
     require_once('system.php');
     require_once( DIR_SYSTEM . 'library/currency.php' );
     require_once( DIR_SYSTEM . 'library/seller/order_stores.php' );
     require_once( DIR_SYSTEM . 'library/securefiledownload.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );

    class SorController extends SystemController
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

      /*Seller Penal - SOR Invoices */      
      public function sorInvoices() {

        if(empty($this->request['id'])) 
        {
        
          return $this->getInvoicesList();
        
        }else{
        
          return $this->sorInvoiceDetail();
        
        }
      }

      public function getInvoicesList()
      {
          $response     = array();
          $data         = array();
          $sor_invoices = array();
          $paid_amounts = array();
          $total_purchase_returns = array();
          $total_sold_pieces = array();
          
          $this->load->model('tool/image');
          
          $seller_id      = $this->customer->getId();
          $sort           = $this->request['sort']  ?? 'order_processing_date';
          $order          = $this->request['order'] ?? 'DESC';
          $start          = $this->request['start'] ?? 0;
          $limit          = $this->request['limit'] ?? $this->config->get('config_limit_admin');
          $purchase_id    = $this->request['id'] ?? NULL;

          $filter_data = array(
                                'purchase_id'       => $purchase_id,
                                'sort'              => $sort,
                                'order'             => $order,
                                'start'             => $start,
                                'limit'             => $limit
                              );

          /*call new methods to get sor invoices data*/ 
          $sor_invoices = $this->_obj_order_stores->getAllSorInvoices($this->db, $seller_id, $filter_data, false);

          /*get invoices total count for pagination*/
          $total_results = $this->_obj_order_stores->getAllSorInvoices($this->db, $seller_id, $filter_data, true);        

          if (!empty($sor_invoices)) {   

            $purchase_ids       = array_column($sor_invoices, 'purchase_id');

            if(!empty($purchase_ids)){

              $total_pieces_returns_to_seller = $this->_obj_order_stores->getTotalPiecesReturnToSeller($this->db, $purchase_ids);
              $total_pieces_sold_to_customers = $this->_obj_order_stores->getTotalPiecesSoldToCustomers($this->db, $purchase_ids);
              $total_pieces_return_amount     = $this->_obj_order_stores->getTotalPiecesReturnAmount($this->db, $purchase_ids);
              $total_paid_amounts             = $this->_obj_order_stores->getTotalPaidAmountForSorInvoice( $this->db, $seller_id, $purchase_ids);

              foreach ($sor_invoices as $key => $value)
              {
                //get total return pieces to seller for the purchase id
                $pieces_returned_to_seller = $total_pieces_returns_to_seller[$value['purchase_id']]['total_pieces_returned_to_seller'] ?? 0; 
                //get total return pieces to seller amount for the purchase id
                $amount_returned_to_seller = $total_pieces_returns_to_seller[$value['purchase_id']]['total_return_amount'] ?? 0;  
                //get total pieces sold
                $pieces_sold      = $total_pieces_sold_to_customers[$value['purchase_id']]['pieces_sold'] ?? 0;
                //get total amount sold
                $amount_sold      = $total_pieces_sold_to_customers[$value['purchase_id']]['amount_sold'] ?? 0;
                //get total pieces return
                $pieces_returned  = $total_pieces_return_amount[$value['purchase_id']]['pieces_returned'] ?? 0;
                //get total return pieces amount
                $amount_returned  = $total_pieces_return_amount[$value['purchase_id']]['amount_returned'] ?? 0;
                //get total amount paid
                $amount_paid      = $total_paid_amounts[$value['purchase_id']] ?? 0;

                //calculate net pieces sold to customer 
                $net_pieces_sold_to_customer = ( (int)$pieces_sold - (int)$pieces_returned );
                //calculate net amount sold to customer
                $net_amount_sold_to_customer = ( (float)$amount_sold - (float)$amount_returned );
                //calculate total amount paid to seller
                $amount_paid_to_seller   = $amount_paid;
                
                $unsold_pieces  = ( ( $value['total_pieces'] - $net_pieces_sold_to_customer ) - $pieces_returned_to_seller );
                $unsold_amount  = ( (float)$value['invoice_amount'] ) - ( (float)$net_amount_sold_to_customer + (float)$amount_returned_to_seller ) ;
                $seller_balance = ( (float)$net_amount_sold_to_customer - (float)$amount_paid_to_seller );

                $value['id']                            = $value['purchase_id'];
                $value['total_purchase_return_pieces']  = $pieces_returned_to_seller;
                $value['total_purchase_return_amount']  = $this->currency->format( $amount_returned_to_seller,'INR',1);
                $value['total_amount']                  = $this->currency->format( $value['invoice_amount'], 'INR',1 );
                $value['paid_amount']                   = $this->currency->format( $amount_paid_to_seller, 'INR',1 );
                $value['amount_sold']                   = $this->currency->format( $net_amount_sold_to_customer, 'INR', 1 );
                $value['pieces_sold']                   = $net_pieces_sold_to_customer;
                $value['unsold_amount']                 = $this->currency->format( $unsold_amount ,'INR',1 );
                $value['seller_balance']                = $this->currency->format( $seller_balance ,'INR',1 );
                $value['unsold_pieces']                 = $unsold_pieces;
                
                $response[] = $value;
              }
            }
          } 

          $this->data_packet->data           = $response;
          $this->data_packet->totalRecord    = $total_results;
          $this->data_packet->statusCode     = 200;
          return $this->data_packet;
      }

      /*Seller Penal - SOR Invoice Products Listing */
      public function sorInvoiceDetail()
      { 

        $this->load->model('tool/image');
        
        $purchase_id = $this->request['id'] ?? 0;
        
        $invoice_details = $this->_obj_order_stores->getSorInvoiceDetails($this->db, $purchase_id);

        $response = array();

        if(!empty($invoice_details))
        {
          $width  = $this->config->get('config_image_additional_width');
          $height = $this->config->get('config_image_additional_height');

          foreach ($invoice_details as $key => $value) 
          {
           
           $image  = $this->model_tool_image->resize( $value['image'], 100, 150);
           $sku_temp = substr($value['sku'],5);
          
           $response['id']                = $value['purchase_id'];
           $response['purchase_id']       = $value['purchase_id'];
           $response['seller_id']         = $value['seller_id'];
           $response['invoice_no']        = $value['invoice_no'];
           $response['invoice_date']      = $value['invoice_date']; 
           $response['invoice_products']
                    [$sku_temp][]= array(
                                        'sku'                     => $value['sku'],
                                        'image'                   => $image,
                                        'width'                   => $width,
                                        'height'                  => $height,
                                        'pieces'                  => $value['total_pieces_purchased'],
                                        'total_pur_amt'           => $value['total_amount_purchased'],
                                        'return_quantity'         => $value['pieces_returned_to_seller'],
                                        'productReturnAmount'     => $value['amount_returned_to_seller'],
                                        'purchase_return_pieces'  => $value['pieces_returned_to_seller'],
                                        'pieces_sold'             => ($value['total_pieces_purchased'] - $value['pieces_returned_to_seller']),
                                        'amount_sold'             => $value['net_amount_sold_to_customer'],
                                        'transfer_price_per_piece'=> $value['transfer_price_per_piece']
                                      );
           }
        }
        
        $this->data_packet->data           = array($response);
        $this->data_packet->totalRecord    = 1;
        $this->data_packet->statusCode     = 200;
        return $this->data_packet;
      }


    public function sorSku() 
    {
        $sorSkusData      = array();
        $total_results    = 0;

           $sorSkusData = array();
          $this->load->model('tool/image');

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
              $limit =$this->config->get('config_limit_admin');
          }    
          
          if( !empty($this->request['filter_invoice_no']) ){

            $data['filter_invoice_no'] = $this->request['filter_invoice_no'];
          } else {
            $data['filter_invoice_no'] = NULL;
          }

          if( !empty($this->request['id']) ){

            $data['product_id'] = $this->request['id'];
          } else {
            $data['product_id'] = NULL;
          }


          $seller_id      = $this->customer->getId();
          $sorSkusData    = array();
          $i              = 0;
          $sorSkuWiseData = array();
          $total_results  = 0;


          $filter_data = array(
            'filter_invoice_no'            => $data['filter_invoice_no'],
            'product_id'                   => $data['product_id'],
            'sort'                         => $sort,
            'order'                        => $order,
            'start'                        => $start,
            'limit'                        => $limit,
          );  

          /*
          * get count for paging
          */
          $total_results    = $this->_obj_order_stores->getSorProductList($this->db, $seller_id, $filter_data,'get_count');
          /*
          * get sor invoice records
          */  
          $sorSkuWiseData   = $this->_obj_order_stores->getSorProductList($this->db, $seller_id, $filter_data);
    
    
          if (!empty($sorSkuWiseData)) {
              
            $product_id_array       = array();
            $products_return_array    = array();
            $product_id_array       = array_column($sorSkuWiseData, 'product_id');
            /*
            * get all product sale
            */
            $sor_sales_quantity     = $this->_obj_order_stores->getSorProductSoldQuantity( $this->db, $product_id_array);
            /*
            * get all product sales return
            */
            $products_sale_return     = $this->_obj_order_stores->getSorProductsSalesReturns($this->db, $product_id_array);
            /*
            * get all product purchase return
            */
            $products_purchase_return   = $this->_obj_order_stores->getSorProductsPurchaseReturns($this->db, $product_id_array);
            /*
            * get paid amount against invoice
            */
            $paid_amount_for_products = $this->_obj_order_stores->getTotalPaidAmountAgainstSingleSorSku($this->db, $product_id_array, $seller_id);

      foreach ($sorSkuWiseData as $key => $valuein) {
        /*
        * calculate total saold items and total amount
        */
        $productSoldQuantity  = 0;
        $productSoldAmount    = 0;
        $net_quantity       = 0;
        $productActualSoldQuantity = 0;
        $productActualSoldAmount = 0;
        $productReturnQuantity   = 0;
        $productReturnAmount     = 0;

        if (isset($products_purchase_return[$valuein['product_id']])) {

          $sorSkusData[$valuein['sku']]['purchase_return_pieces'] = $products_purchase_return[$valuein['product_id']]['quantity'];
          $sorSkusData[$valuein['sku']]['purchase_return_amt']  = ($products_purchase_return[$valuein['product_id']]['quantity'] * $valuein['transfer_price_per_piece']);         

        }else{

          $sorSkusData[$valuein['sku']]['purchase_return_pieces'] = 0;
          $sorSkusData[$valuein['sku']]['purchase_return_amt']  = 0;
        }

        $sorSkusData[$valuein['sku']]['total_pur_qty']        = $valuein['total_pur_qty'];
        $sorSkusData[$valuein['sku']]['total_pur_amt']        = ($valuein['total_pur_qty'] * $valuein['transfer_price_per_piece']);

        if (!empty($sor_sales_quantity)) {

          $return_quantity = 0;
            
          if (isset($products_sale_return[$valuein['product_id']])) {
            
            $return_quantity     = $products_sale_return[$valuein['product_id']];

            $productActualSoldQuantity   = $sor_sales_quantity[$valuein['product_id']]['total_sold_qty'];

            $productActualSoldAmount     = ($sor_sales_quantity[$valuein['product_id']]['total_sold_qty']*$sor_sales_quantity[$valuein['product_id']]['transfer_price_per_piece']);
            
            $productSoldQuantity   = ($sor_sales_quantity[$valuein['product_id']]['total_sold_qty'] - $return_quantity);

            $productSoldAmount     = (($sor_sales_quantity[$valuein['product_id']]['total_sold_qty'] - $return_quantity)*$sor_sales_quantity[$valuein['product_id']]['transfer_price_per_piece']);
                       
            $productReturnQuantity   = $return_quantity;

            $productReturnAmount     = ($return_quantity*$sor_sales_quantity[$valuein['product_id']]['transfer_price_per_piece']);;


          }else{
          
           
           if(isset($sor_sales_quantity[$valuein['product_id']]))
           {
             $productSoldQuantity     = $sor_sales_quantity[$valuein['product_id']]['total_sold_qty'];
             $productSoldAmount     = ($sor_sales_quantity[$valuein['product_id']]['total_sold_qty']*$sor_sales_quantity[$valuein['product_id']]['transfer_price_per_piece']);

             $productActualSoldQuantity = $productSoldQuantity;
             $productActualSoldAmount   = $productSoldAmount;
            } 
          }
        }
        /*
        * get image size and path
        */        
        $image  = $this->model_tool_image->resize( $valuein['image'], 100, 150);
                $width  = $this->config->get('config_image_additional_width');
                $height = $this->config->get('config_image_additional_height');
                /*
        * get sold quantity and amount of sor product
        */
        $totalPaidAmount  = 0;
        $trxn_done          = '';
        $totalUnsoldAmount  = 0;
        
        if (isset($paid_amount_for_products[$valuein['product_id']])) {
          
          if ($return_quantity>0) {

            $totalPaidAmount  = ($paid_amount_for_products[$valuein['product_id']]['total_paid_amount']-($return_quantity*$paid_amount_for_products[$valuein['product_id']]['transfer_price_per_piece']));
          }else{

            $totalPaidAmount  = $paid_amount_for_products[$valuein['product_id']]['total_paid_amount'];
          }
        }
        
        $sorSkusData[$valuein['sku']]['id']           = $valuein['product_id'];
        $sorSkusData[$valuein['sku']]['purchase_id']  = $valuein['purchase_id'];
        $sorSkusData[$valuein['sku']]['sku']          = $valuein['sku'];
        $sorSkusData[$valuein['sku']]['image']        = $image;
        $sorSkusData[$valuein['sku']]['width']        = $width;
        $sorSkusData[$valuein['sku']]['height']       = $height;
        
        if ($totalPaidAmount>0) {
          $sorSkusData[$valuein['sku']]['paid_amount']    = $this->currency->format($totalPaidAmount,'INR',1);
        }else{
          $sorSkusData[$valuein['sku']]['paid_amount']    = $totalPaidAmount;
        }
                
              $sorSkusData[$valuein['sku']]['total_return_qty']   = $productReturnQuantity;
               
               if ($productReturnAmount>0) {
                $sorSkusData[$valuein['sku']]['product_return_amt']   = $this->currency->format($productReturnAmount); 
                }
               else {
                $sorSkusData[$valuein['sku']]['product_return_amt']   = $productReturnAmount; 
               } 

        $sorSkusData[$valuein['sku']]['total_sold_qty']   = $productSoldQuantity;

        $sorSkusData[$valuein['sku']]['total_actual_sold_qty']  = $productActualSoldQuantity;
        
        $totalUnsoldAmount  = (($sorSkusData[$valuein['sku']]['total_pur_amt'] - $productSoldAmount) - $sorSkusData[$valuein['sku']]['purchase_return_amt']);

        $sorSkusData[$valuein['sku']]['total_pur_amt']    = $this->currency->format($sorSkusData[$valuein['sku']]['total_pur_amt'],'INR',1);        

        if ($productSoldAmount>0) {
          $sorSkusData[$valuein['sku']]['total_sold_amt']     = $this->currency->format($productSoldAmount,'INR',1);
        }else{
          $sorSkusData[$valuein['sku']]['total_sold_amt']     = $productSoldAmount;
        }

        if ($productActualSoldAmount>0) {
          $sorSkusData[$valuein['sku']]['total_actual_sold_amt']    = $this->currency->format($productActualSoldAmount,'INR',1);
        }else{
          $sorSkusData[$valuein['sku']]['total_actual_sold_amt']    = $productActualSoldAmount;
        }       

        if ($totalUnsoldAmount>0) {
          $sorSkusData[$valuein['sku']]['total_unsold_amt']     = $this->currency->format($totalUnsoldAmount,'INR',1);
        }else{
          $sorSkusData[$valuein['sku']]['total_unsold_amt']     = '';
        }

        $sorSkusData[$valuein['sku']]['unsold_pieces']    = (($sorSkusData[$valuein['sku']]['total_pur_qty']-$sorSkusData[$valuein['sku']]['total_sold_qty']) - $sorSkusData[$valuein['sku']]['purchase_return_pieces']);
        
        $sorSkusData[$valuein['sku']]['seller_balance']        = $this->currency->format($productActualSoldAmount-$totalPaidAmount);    


        $sorSkusData[$valuein['sku']]['trxn_done']      = '<i class="fa fa-info-circle paid_tooltip" aria-hidden="true" style="color:blue; margin-left:10px;"></i>';
        $sorSkusData[$valuein['sku']]['purchase_id']    = $valuein['purchase_id'];
        $sorSkusData[$valuein['sku']]['product_id']     = $valuein['product_id'];
          
       }
     }

      $this->data_packet->data           = array_values($sorSkusData);
      $this->data_packet->totalRecord    =  $total_results;
      $this->data_packet->statusCode     = 200;
      return $this->data_packet; 

  }


 public function sellerPaymentDetailOfSingleSku(){
        $paid_data = array();
        $total_results    = 0;

          $paid_data = array();
          $data = array();
          $seller_id          = $this->customer->getId();
          $data['seller_id']  = $seller_id;
          $total_results      = 0;

          if ( !empty($this->request['sort']) ) {
            $sort = $this->request['sort'];
          } else {
            $sort = 'trxn_utr_date';
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
              $limit =$this->config->get('config_limit_admin');
          }    
          
          if( !empty($this->request['id']) ){
            $data['product_id'] = $this->request['id'];
          } else {
            $data['product_id'] = NULL;
          }

          if( !empty($this->request['purchase_id']) ){
            $data['purchase_id'] = $this->request['purchase_id'];
          } else {
            $data['purchase_id'] = NULL;
          }

    $filter_data = array(
      'product_id'                 => $data['product_id'],
      'purchase_id'                => $data['purchase_id'],
      'seller_id'                  => $data['seller_id'],
      'sort'                         => $sort,
      'order'                        => $order,
      'start'                        => $start,
      'limit'                        => $limit
    );


    $total_results    = $this->_obj_order_stores->getPaidAmountDetailAgainstSingleSorSku($this->db, $filter_data, 'get_count');

    $paid_data    = $this->_obj_order_stores->getPaidAmountDetailAgainstSingleSorSku($this->db, $filter_data);
    
    
    $return_data    = $this->_obj_order_stores->getSorProductsSalesReturns($this->db, array($data['product_id']),'single_sku');

   if(!empty($paid_data) && !empty($return_data )){
      foreach ($paid_data as $key => $value) {
        if (in_array($value['order_product_id'], $return_data)) {
          unset($paid_data[$key]);
        }
      }
    }
    /*
    * calculate total sold paid amount
    */
    if (!empty($paid_data)) {
      foreach ($paid_data as $key => $value) {
        $paid_data[$key]['id']          = $value['suborder_id'];
        $paid_data[$key]['total_piece'] = ($value['quantity']*$value['piece_in_set']);
        $paid_data[$key]['sold_amt']    = $this->currency->format(($value['quantity']*$value['piece_in_set']*$value['transfer_price_per_piece']),'INR',1);
      }
    }

      $this->data_packet->data           = $paid_data;
      $this->data_packet->totalRecord    =  $total_results;
      $this->data_packet->statusCode     = 200;
      return $this->data_packet; 

  }

}