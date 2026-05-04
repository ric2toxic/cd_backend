<?php
  require_once('system.php');
  require_once( DIR_SYSTEM . 'library/operations/returns/return_info.php' );
  require_once( DIR_SYSTEM . 'library/operations/orders/order_product.php' );
  require_once( DIR_SYSTEM . 'library/operations/returns/sor_order_product.php' );

  class AddreturnController extends SystemController {

    private $data        = '';
    private $token       = '';
    private $message     = 'No Data Found';
    private $statusCode  = 999;
    private $_limit_in_days_for_replacement = 15;
    private $_limit_in_days_for_quality     = 4;

    public function __construct($params) {

      parent::__construct($params);

      // ReturnInfo Class Object
      $return_info = new ReturnInfo($this->registry);
      $this->registry->set('return_info', $return_info);
    }
    
    /**
     * @info: Public method to get returns list for given customer_id
     *        after all validation, related to access authority
     * @author: Nishu, July 2018
    */
    public function getAddReturnPageDetails(){
      $resp = array();

      //Check No data passed in get parameters
      if (!empty($this->request)) {
       
        //Set request data to $filter_data
        $data = $this->request;

        //Check for required data keys
        if(!empty($data['customer_id']) ){

          //Check for required data keys
          if(!empty($data['order_id']) ){

            $data['limit_in_days_for_quality']     = $this->_limit_in_days_for_quality;
            $data['limit_in_days_for_replacement'] = $this->_limit_in_days_for_replacement;

            //Get Return(s) List by given request params for single customer
            $returnable_products = $this->return_info->getReturnableProducts($data);

            if(!empty($returnable_products)){
              $order_product_ids = array_keys($returnable_products);
              $order_no = array_unique( array_column($returnable_products, 'order_no') );
              $resp = $this->return_info->getPickupAddress($order_product_ids, (int)$data['order_id'], $order_no[0]);
              $resp['products'] = $returnable_products;
            }

            if(!empty($resp)){
              //Set data for Response 
              $this->data_packet->statusCode = 200;
              $this->data_packet->data       = $resp;
              $this->data_packet->message    = "Data Successfully Found.";
            }else{
              $this->data_packet->statusCode = 200;
              $this->data_packet->message    = "Data not found.";
            }
          }else{
            $this->data_packet->statusCode = 999;
            $this->data_packet->message    = "Order Id is missing.";
          }
        }else{
          $this->data_packet->statusCode = 999;
          $this->data_packet->message    = "Customer Id is missing.";
        }

      }else{
        $this->data_packet->statusCode = 999;
        $this->data_packet->message    = "Invalid Request!!";
      }
      
      //Set and return Response of API
      echo json_encode($this->data_packet); exit();
    }


        /**
     * @info: Public method to add return by submit add return form
     * @author: Nishu, Sept 2019
    */
    public function submitAddReturnForm(){
      
      //Check No data passed in get parameters
      if (!empty($this->request)) {
       
        //Set request data to $filter_data
        $data = $this->request;

        //Check for required data keys
        if(!empty($data['customer_id']) ){

          //Check for required data keys
          if(!empty($data['order_id']) ){

            //Validate Add return data, reeived in request
            $is_valid = $this->return_info->validateAddReturnsData($data);

            if(!empty($is_valid) && $is_valid['is_valid'] == false){
                //Set and return Response of API
                $this->data_packet->statusCode = 999;
                $this->data_packet->message    = $is_valid['message'] ?? "Invalid Params to add return!";
                echo json_encode($this->data_packet); exit();
            }

          //Add reutrn 
          $is_added = $this->return_info->addNewReturnFromClient($data);
  
          if(!empty($is_added)){
            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->message    = "Return Successfully Added.";
          }else{
            $this->data_packet->statusCode = 200;
            $this->data_packet->message    = "Add Return Failed.";
          }
        }else{
          $this->data_packet->statusCode = 999;
          $this->data_packet->message    = "Order Id is missing.";
        }
      }else{
        $this->data_packet->statusCode  = 999;
        $this->data_packet->message     = "Customer Id is missing.";
      }

      }else{
        $this->data_packet->statusCode = 999;
        $this->data_packet->message    = "Invalid Request!!";
      }
      
      //Set and return Response of API
      echo json_encode($this->data_packet); exit();
    }










    /**
     * @info: Private Method to set response object globally\
     * @param: None
     * @return: Void
     * @author: Nishu, July 2018
    */
    private function __response(){
      // response function
      $response = array();
      $response['statusCode'] = $this->statusCode;
      $response['token']      = $this->token;
      $response['message']    = $this->message;
      $response['data']       = $this->data;
      
      return $response; 
    }

  }