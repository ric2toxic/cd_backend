<?php
  declare(strict_types=1);

  require_once('system.php');
  require_once( DIR_SYSTEM . 'library/operations/orders/customer_order_info.php' );
  require_once( DIR_SYSTEM . 'library/operations/orders/order_product.php' );
  require_once( DIR_SYSTEM . 'library/operations/orders/order_product_review.php' );
  require_once( DIR_SYSTEM . 'library/operations/orders/validate_customer_order.php' );
  require_once( DIR_SYSTEM . 'library/securefiledownload.php' );
  require_once(DIR_SYSTEM.'library/cart.php');

  class ordersController extends SystemController {

    private $_order_ids  = array();
        
    public function __construct($params) {

      parent::__construct($params);

      // OrderInfo
      $order_info = new OrderInfo($this->registry);
      $this->registry->set('order_info', $order_info);

      // CustomerOrderInfo
      $customer_order_info = new CustomerOrderInfo($this);

      $this->registry->set('customer_order_info', $customer_order_info);

      // Validate CustomerOrder(s)
      $validate_customer_order = new ValidateCustomerOrder($this->registry);
      $this->registry->set('validate_customer_order', $validate_customer_order);

      // SecureFileDownload                                        
      $this->registry->set('securefiledownload', new SecureFileDownload($this->registry));
      
       // Cart
      $this->registry->set('cart', new Cart($this->registry));
    }
    
    /**
     * @info : Public method(API) to get Order List for customer
     * @author: Nishu, July 2018
    */    
    public function getOrderList() {
      $order_info = array();
      
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $data
        $data = $this->request;

        //Check for required data keys
        if(!empty($data['customer_id'])){

          //Get Order(s) List by given request params for single customer
          $result = $this->customer_order_info->getAllOrdersByCustomerId($data);

          if(!empty($result)){
            //Get All OrderIds by using array_keys
            $this->_order_ids = array_keys($result);

            //Get OrderInfo details
            $order_info = $this->customer_order_info->getMultipleOrderDetails($this->_order_ids);

            //Set OrderInfo data according API Format
            $order_info = $this->customer_order_info->getManipulateOrderInfo( $order_info );

            //set Payment status order wise
            $order_data = $this->customer_order_info->calculateTotalReceivedAndPendingBalanceAmount($order_info);

            $order_info = $this->customer_order_info->setPaymentStatusToOrderInfo($order_data, $order_info);

            $order_info = $this->customer_order_info->getPaymentLinkAndRquestNewPaymentLink($order_info);

            //Beyond order id passed for using in Next API calling, set default to empty string
            $order_info['beyond_order_id'] = min(array_keys($order_info['orders']));
            $data['beyond_order_id']       = $order_info['beyond_order_id'];

            //Is more order(s) are pending to show on order list page, 
            //If any order is not listed yet beyond_order_id will be set to empty string
            $data['offset']    = 1;
            $more_orders_avail = $this->customer_order_info->getAllOrdersByCustomerId($data);
           
            if(empty($more_orders_avail)){
              //Beyond order id passed for using in Next API calling
              $order_info['beyond_order_id'] = '';
            }

            //Reset array's key, To prevent browser's default functionality of auto sorting from DESC to ASC
            $order_info['orders']   = array_values($order_info['orders']);

            //Set Order List filters or data back in response
            $order_info['customer_id']                = $data['customer_id'] ?? '';
            $order_info['order_type_filter']          = $data['order_type_filter'] ?? '';
            $order_info['filter_order_or_invoice_no'] = $data['filter_order_or_invoice_no'] ?? '';

            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->message    = "Data Successfully Found.";
          }else{
            $this->data_packet->message    = "Data not found.";
          }
        }else{
            $this->data_packet->message    = "Customer Id is missing.";
          //throw new Exception("Customer Id is missing.");
        }        
      }else{
        //throw new Exception("Required Parameters Missing.");
        $this->data_packet->message    = "Required Parameters Missing.";
      }

      $this->data_packet->data       = $order_info;

      //Set and return Response of API
      return $this->data_packet;
    }

    /**
     * @info : Public method(API) to generate new payment link
     * @author: Nishu, Aug 2018
    */
    public function rquestNewPaymentLink(){

      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $data
        $data = $this->request;

        //Check for required data keys
        if(!empty($data) && !empty($data['customer_id']) && !empty($data['order_id'])){

          //Request new payment link
          $payment_url = $this->customer_order_info->rquestNewPaymentLink($data);
          $data['url'] = $payment_url;

          //Set data for Response 
          $this->data_packet->statusCode = 200;
          $this->data_packet->data       = $data;

          if(!empty($payment_url)){
            $this->data_packet->message    = "Payment link generated and sent successfully.";
          }else{
            $this->data_packet->message    = "Something went wrong, please try after some time.";
          }
        }

      }//End of If

      //Set and return Response of API
      return $this->data_packet;
    }

    /**
     * @info : Public method(API) to get ShippingPreferences Order details page
     * @author: Nishu, Aug  2018
    */    
    public function shippingPreferencesForOrderDetail() {

      //Check No data passed in get parameters
      if( !empty($this->request) ){
       
        //Set request data to $data
        $data     = $this->request;
        $res_data = $data;

        //Check for required data keys
        if(
          !empty($data) 
          && !empty($data['order_id'])
          && !empty($data['suborder_id']) 
          && !empty($data['customer_id']) 
        ){
          $order_id    = (int)$data['order_id'];
          $suborder_id = $data['suborder_id'];

          //Validate if Cutomer is associated with order or not
          if($this->validate_customer_order->validateCustomerWithOrder($this->db, $data)){

            //List of suborder-ids can get update i.e. suborders not having buyer invoice
            $suborders    = Suborder::getAllSubordersNotHavingBuyerInvoice($this->db, $order_id);
            
            //Get all suborder_id(s) for all suborder(s) not having buyer invoice
            $suborder_ids             = array_column($suborders, 'suborder_id');
            $data['suborder_ids']     = $suborder_ids;
            $res_data['suborder_ids'] = implode(', ', $suborder_ids);

            //Check This API calling is for update shipping preferences of suborder
            // And also check suborder(s) is available to update i.e. still buyer invoice is not generated
            if(!empty($data['update_preferences']) && !empty($suborder_ids)){

              //Update shipping prefrences for suborder(s), buyer invoice is not generated
              Suborder::updateShippingPreferencesForOrderDetailPage($this->db, $data);
          
            }else{
              $res_data = $this->setShippingPrefernces($suborder_id, $suborders, $res_data);
            }

            //Set Show edit button or not for shiping preferences
            $res_data = Suborder::isShippingPreferenceEditable($this->db, $res_data);

            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->message    = "Data Successfully Found.";
            $this->data_packet->data       = $res_data;
          }else{
            //throw new Exception("Invalid data passed for customer.");
            $this->data_packet->message    = "Invalid data passed for customer.";
          }
        }else{
          //throw new Exception("Required Parameters Missing.");
          $this->data_packet->message    = "Required Parameters Missing.";
        }
      }else{
        //throw new Exception("Required Parameters Missing.");
        $this->data_packet->message    = "Required Parameters Missing.";
      }

      //Return Response of API
      return $this->data_packet;
    }

    /**
     * Private method to set shipping prefernces data 
     *     like: no_wsb_tape, no_invoice_with_shipment and courier_preference
     * @param: $suborder_id String, $suborders Array
     * @return: array
     * @author: Nishu, Aug 2018
    */
    private function setShippingPrefernces(string $suborder_id, array $suborders, array $res_data) : array{

      if(!empty($suborders[$suborder_id])){
        $suborder_data = $suborders[$suborder_id];
      }else{
        $selector = array(
                      'suborder_id',
                      'order_status_id',
                      'no_wsb_tape',
                      'no_invoice_with_shipment',
                      'courier_partner_preference'
                    );
        $suborder_data = Suborder::getSuborderInfo($this->db, $suborder_id, $selector);
      }
      $res_data['no_wsb_tape']              = $suborder_data['no_wsb_tape'] ?? '';
      $res_data['no_invoice_with_shipment'] = $suborder_data['no_invoice_with_shipment'] ?? '';
      $res_data['courier_preference']       = $suborder_data['courier_partner_preference'] ?? '';

      return $res_data;
    }

    /**
     * Public Method to Reorder Order Product, Product with same quantity will be added to cart
     * @author: Nishu, Aug 2018
    */
    public function reorderOrderProduct(){
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $data
        $data = $this->request;

        //Check for required data keys
        if(!empty($data) && !empty($data['customer_id']) && !empty($data['order_product_id'])){
          //Validate if Cutomer is associated with orderProduct or not
          if($this->validate_customer_order->validateCustomerWithOrder($this->db, $data)){

            //set Preferances details suborder wise
            $order_product = new OrderProduct($this);
            $is_reorder = $order_product->reorderOrderProduct($data);
            if(!empty($is_reorder)){
              //Set data for Response 
              $this->data_packet->statusCode = 200;
              $this->data_packet->message    = "Reordered Successfully.";
              $this->data_packet->data       = $this->cart->countProducts();
            }else{
              //Set data for Response 
              //throw new Exception("Reordered Failed.");
              $this->data_packet->message    = "Reordered Failed.";
            }
          }else{
            //throw new Exception("Invalid data passed for customer.");
            $this->data_packet->message    = "Invalid data passed for customer.";
          }
        }else{
          //throw new Exception("Required Parameters Missing.");
          $this->data_packet->message    = "Required Parameters Missing.";
        }
      }else{
        //throw new Exception("Required Parameters Missing.");
        $this->data_packet->message    = "Required Parameters Missing.";
      }
      //Return Response of API
      return $this->data_packet;
    }

    /**
     * Public Method to Reorder Order Product, Product with same quantity will be added to cart
     * @author: Nishu, Aug 2018
    */
    public function orderProductLikeDislike(){
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $data
        $data = $this->request;

        //Check for required data keys
        if(
          !empty($data) 
          && !empty($data['customer_id']) 
          && !empty($data['order_product_id'])
          && !empty($data['review'])
        ){
          //Validate if Cutomer is associated with orderProduct or not
          if($this->validate_customer_order->validateCustomerWithOrder($this->db, $data)){
            
            $order_product_review_data = array();
            $order_product_review_data['order_product_id'] = (int)$data['order_product_id'];
            $order_product_review_data['product_review']   = $data['review'];
        
            //---------Add/Update OrderProductReview in oc_order_product_review
            OrderProductReview::addOrderProductReview($this->db, $order_product_review_data);

            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->message    = "Successfully Done.";
            $this->data_packet->data       = $data;

          }else{
            //throw new Exception("Invalid data passed for customer.");
            $this->data_packet->message    = "Invalid data passed for customer.";
          }
        }else{
          //hrow new Exception("Required Parameters Missing.");
          $this->data_packet->message    = "Required Parameters Missing.";
        }
      }else{
        //throw new Exception("Required Parameters Missing.");
        $this->data_packet->message    = "Required Parameters Missing.";
      }
      
      //Return Response of API
      return $this->data_packet;
    }

    /**
     * @info : Public method(API) to get SuborderWise details
     * @author: Nishu, Aug  2018
    */    
    public function getOrderProductDetailsBySuborderId() {
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $data
        $data = $this->request;

        if(
          !empty($data) 
          && !empty($data['customer_id']) 
          && !empty($data['order_id']) 
          && !empty($data['suborder_id']) 
        ){
          //Validate if Cutomer is associated with orderProduct or not
          if($this->validate_customer_order->validateCustomerWithOrder($this->db, $data)){
            $order_id    = (int)$data['order_id'];
            $suborder_id = $data['suborder_id'];


            //Get suborder Status
            $selector = array( 'order_status_id' );
            $suborder_data = Suborder::getSuborderInfo($this->db, $suborder_id, $selector);

            $suborder_status = $suborder_data['order_status_id'];

            //Set BuyerInvoice object
            $buyer_invoice = new BuyerInvoice($this->registry);
            $buyer_invoice->setOptions('show_image', true);

            //Get all products in case suborder status cancelled, what ever order_product edit_type
            if((int)$suborder_status == (int)ORDER_STATUS['Canceled']  ){
              $buyer_invoice->setOptions('get_all_product', true);              
            }

            //Get basic/row details of order product
            $order_products = $buyer_invoice->getProductsArrayBySuborderId($order_id, $suborder_id);

            //Set Product wise amount details
            $order_products = OrderProduct::calculateOrderProductAmountBreakup($this, $order_products);

            //Set Extra links with OrderProduct
            $order_products = OrderProduct::setExtraLinksForOrderProduct($this->url, $order_products, (int)$data['customer_id']);            

            if(!empty($order_products)){
              //Set data for Response 
              $this->data_packet->statusCode = 200;
              $this->data_packet->message    = "Data Found Successfully.";
              $this->data_packet->data       = $order_products;
            }else{
              //Set data for Response 
              //throw new Exception("No Data Found.");
               $this->data_packet->message    = "No Data Found.";
            }
          }else{
            //throw new Exception("Invalid data passed for customer.");
            $this->data_packet->message    = "Invalid data passed for customer.";
          }
        }else{
          //throw new Exception("Required Parameters Missing.");
          $this->data_packet->message    = "Required Parameters Missing.";
        }
      }else{
        //throw new Exception("Required Parameters Missing.");
        $this->data_packet->message    = "Required Parameters Missing.";
      }

      //Set and return Response of 
      return $this->data_packet;
    }

    /**
     * @info : Public method(API) to get orderWise details
     * @author: Nishu, Aug  2018
    */    
    public function getOrderInfoAndTotalAmountBreakup() {

      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $data
        $data = $this->request;
        if(
          !empty($data) 
          && !empty($data['customer_id']) 
          && !empty($data['order_id']) 
          && !empty($data['suborder_id']) 
        ){
          //Validate if Cutomer is associated with orderProduct or not
          if($this->validate_customer_order->validateCustomerWithOrder($this->db, $data)){
            
            $order_id      = (int)$data['order_id'];
            $suborder_id   = (string)$data['suborder_id'];
            $result        = array();
            $order         = array();

            //Set BuyerInvoice object
            $buyer_invoice = new BuyerInvoice($this->registry);
            
            //Get Row data of Order Details
            $order_info = $this->customer_order_info->getMultipleOrderDetails( array($order_id));

            //Set Payment/Shipping address related keys as sub-array
            $order_info = $this->customer_order_info->setAddressBlocksOfOrder( $order_id, $order_info );

            //Suborder details 
            $suborder   = $order_info[$order_id]['suborder'][$suborder_id] ?? array();

            //Set order related details 
            $order['orders'][$order_id] = $order_info[$order_id]['order'] ?? array();

            //Set default key values
            $order = $this->customer_order_info->setOrderKeyWithDefaultValue($order_id, $order);

            //Set suborder status related details
            $suborder_details = $this->customer_order_info->setIndividualSuborderDetails($suborder_id, $suborder);
            
            //Set Suborder status
            $suborder_status = (int)$suborder_details['order_status_id'];

            //Get all products in case suborder status cancelled, what ever order_product edit_type
            if( $suborder_status == (int)ORDER_STATUS['Canceled']  ){
              $buyer_invoice->setOptions('get_all_product', true);
            }

            $currency_code  = $order['orders'][$order_id]['currency_code'] ?? 'INR';
            $currency_value = $order['orders'][$order_id]['currency_value'] ?? '1';

            $buyer_invoice->setOptions('currency_code',  $currency_code);
            $buyer_invoice->setOptions('currency_value', $currency_value);

            //Get Amount breakup for suborder detail page
            $total_breakup = $buyer_invoice->getTotals($order_id, $suborder_id);
            $result['total_breakup'] = $buyer_invoice->formatOrderTotalValues( $total_breakup );
           
            //set Total Payment(s) order wise
            $payment_gateway = new PaymentGatewayBase($this);
            $order_data = $payment_gateway->calculateTotalReceivedAndPendingBalanceAmount($order['orders']);
            $order_info[$order_id]['order'] = $order_data[$order_id];

            //Set PaymentInfo related keys as sub-array
            $order_info = $this->customer_order_info->setPaymentInfoOrderWise( $order_id, $order_info );


            //Set Order & Suborder details
            $result['order_info']       = array_merge( $order['orders'][$order_id], $suborder_details, $order_info[$order_id]['order']);

            if(!empty($result)){
              //Set data for Response 
              $this->data_packet->statusCode = 200;
              $this->data_packet->message    = "Data Found Successfully.";
              $this->data_packet->data       = $result;
            }else{
              //Set data for Response 
              //throw new Exception("No Data Found.");
              $this->data_packet->message    = "No Data Found.";
            }
          }else{
            //throw new Exception("Invalid data passed for customer.");
            $this->data_packet->message    = "Invalid data passed for customer.";
          }
        }else{
          //throw new Exception("Required Parameters Missing.");
          $this->data_packet->message    = "Required Parameters Missing.";
        }
      }else{
        //throw new Exception("Required Parameters Missing.");
        $this->data_packet->message    = "Required Parameters Missing.";
      }

      //Set and return Response of 
      return $this->data_packet;
    }



   
  }//End of  Class
