<?php
  require_once('system.php');
  require_once( DIR_SYSTEM . 'library/operations/returns/return_info.php' );
  require_once( DIR_SYSTEM . 'library/operations/returns/return_action_base.php' );

  class returnController extends SystemController {

    private $data        = '';
    private $token       = '';
    private $message     = 'No Data Found';
    private $statusCode  = 999;

        
    public function __construct($params) {

      parent::__construct($params);
      
      // ReturnActionBase Class Object
      $return_action_base = new ReturnActionBase($this->registry);
      $this->registry->set('return_action_base', $return_action_base);

      // ReturnInfo Class Object
      $return_info = new ReturnInfo($this->registry);
      $this->registry->set('return_info', $return_info);
    }
    
    /**
     * @info : Public method(API) to get Return details
     * @author: Nishu, July  2018
    */    
    public function getReturnDetails() {

      //base64_decode 'data' key from request
      $this->decodeApiData($this->request['data']); 
     
      if(!empty($this->request['token'])){
        $this->token = $this->request['token'];
      }
     
      //Check for required data keys
      if(!empty($data['order_id']) && !empty($data['customer_id']) && !empty($data['master_return_id'])){

        //Get Returns List by given request params
        $result = $this->return_info->getReturnDetailsByOrderId($this->request);
        
        if(!empty($result)){
          $this->data           = json_encode($result);
          $this->statusCode     = 200;
          $this->message        = 'Data Fetch Successfully';
        }
      }

      //Set and return Response pf API
      return $this->__response();
    }

    /**
     * @info : Public method(API) to get ReverseShipmentDetails 
     *             for specific master_return_id / reseverse_shipment_tracking_id
     * @author: Nishu, July  2018
    */    
    public function getReverseShipmentDetails() {

      //base64_decode 'data' key from request
      $this->decodeApiData($this->request['data']); 
     
      if(!empty($this->request['token'])){
        $this->token = $this->request['token'];
      }
      //Set default value to $this->message
      $this->message        = 'No Data Found';

      //Check for required data keys
      if(!empty($data['order_id']) && !empty($data['customer_id']) && !empty($data['master_return_id'])){

        //Get Returns List by given request params
        $result = $this->return_info->getReturnDetailsByOrderId($this->request);
        
        if(!empty($result)){
          $this->data           = json_encode($result);
          $this->statusCode     = 200;
          $this->message        = 'Data Fetch Successfully';
        }
      }

      //Set and return Response pf API
      return $this->__response();
    }

    /**
     * @info: Public method to get returns list for given customer_id
     *        after all validation, related to access authority
     * @author: Nishu, July 2018
    */
    public function getReturnListData(){
      
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        $resp = array();
        //Set request data to $filter_data
        $filter_data = $this->request;

        $customer_id = (int)($filter_data['customer_id'] ?? 0);
       
        //Check for required data keys
        if(!empty($filter_data['customer_id']) ){

          $filter_data['limit'] = (int)($filter_data['limit'] ?? 5) + 1;

          //Get Return(s) List by given request params for single customer
          $return_list = $this->return_info->getReturnListData($filter_data);
          
          $resp['beyond_master_return_id'] = "";
          if(!empty($return_list[($filter_data['limit'] -1 )])){
            //$extra_return = $return_list[$filter_data['limit']];
            unset($return_list[($filter_data['limit'] -1 )]);
            $all_master_return_ids = array_column($return_list, 'master_return_id');
            $resp['beyond_master_return_id'] = min($all_master_return_ids);
          }

          $resp['returns'] = $return_list;
          
          if(!empty($return_list)){
            
            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->data       = $resp;
            $this->data_packet->message    = "Data Successfully Found.";
          }else{
            $this->data_packet->message    = "Data not found.";
          }
        }else{
          $this->data_packet->statusCode   = 999;
          $this->data_packet->message      = "User Id is missing.";
        }

      }else{
        $this->data_packet->statusCode = 999;
        $this->data_packet->message    = "Invalid Request Params";
      }
      
      //Set and return Response of API
      echo json_encode($this->data_packet); exit();
    }

    /**
     * @info: Public method(API) for return detail page, for product return details
     * @return: array
     * @author: Nishu, Sept 2019
    */
    public function getReturnDetailPageData(){
      
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $filter_data
        $filter_data = $this->request;
       
        //Check for required data keys
        if(!empty($filter_data['customer_id']) ){

          //Check for required data keys
          if(!empty($filter_data['master_return_id']) ){

              $resp = $this->return_info->getReturnDetailPage($filter_data);

              if(!empty($resp)){
                
                //Set data for Response 
                $this->data_packet->statusCode = 200;
                $this->data_packet->message    = "Data Successfully Found.";
                $this->data_packet->data       = $resp;
              }else{
                $this->data_packet->statusCode = 999;
                $this->data_packet->message    = "Data not found.";
              }
          }else{
            $this->data_packet->statusCode   = 999;
            $this->data_packet->message        = "Master Return Id is missing.";
          }

          
        }else{
          $this->data_packet->statusCode   = 999;
          $this->data_packet->message      = "User Id is missing.";
        }

      }else{
        $this->data_packet->statusCode = 999;
        $this->data_packet->message    = "Invalid Request Params";
      }
      
      echo json_encode($this->data_packet); exit();
    }

    /**
     * @info: Public method(API) for return detail page
     * 
     * @return: Bank Details
     * 
     * @author: Nishu, Sept 2019
    */
    public function getCustomerBankInfo(){
      
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $filter_data
        $filter_data = $this->request;
       
        //Check for required data keys
        if(!empty($filter_data['customer_id']) ){

          //Get Bank details for single customer
          $selector = array('bank_ac_holder_name', 'bank_ac_number', 'ifsc_code');
          $bank_data = Customer::getCustomerInfo($this->db, (int)$filter_data['customer_id'], $selector);

          if(!empty($bank_data[$filter_data['customer_id']]) ) {
            $bank_details = $bank_data[$filter_data['customer_id']] ?? array();
            
            $ifsc_code    = $bank_details['ifsc_code'] ?? '';
            $bank_address = getBankAddressByIFSC($ifsc_code);
            $bank_details['bank_address'] = $bank_address;

            $bank_details['bank_url'] = $this->url->link('account/bank_details', '', 'SSL');

            

            //Set data for Response 
            $this->data_packet->statusCode = 200;
            $this->data_packet->message    = "Data Successfully Found.";
            $this->data_packet->data       = $bank_details;
          }else{
            $this->data_packet->statusCode = 999;
            $this->data_packet->message    = "Data not found.";
          }
          
        }else{
          $this->data_packet->statusCode   = 999;
          $this->data_packet->message      = "Customer Id is missing.";
        }

      }else{
        $this->data_packet->statusCode = 999;
        $this->data_packet->message    = "Invalid Request Params";
      }
      
      echo json_encode($this->data_packet); exit();
    }

    /**
     * @info: Public method API to upload Courier details by customer
     *         Only for those returns whose pickup-type is 'self_courier'
     * @author: Nishu, Sept 2019
    */
    public function uploadCourierDetails(){
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $filter_data
        $filter_data = $this->request;
       
        //Check for required data keys
        if(!empty($filter_data['customer_id']) ){

          //Check for required data keys
          if(!empty($filter_data['master_return_id']) ){

            if(!empty($_FILES['shipment_slip']) ){ 

              if(!empty($filter_data['tracking_no']) ){ 
              
                if(!empty($filter_data['shipment_company']) ){

                  if(!empty($filter_data['order_id']) ){

                    if(!empty($filter_data['order_no']) ){

                      $image_name  = $_FILES['shipment_slip']["name"];
                      $tmpName     = $_FILES['shipment_slip']["tmp_name"];

                      $this->load->model('restapi/return');

                      $filter_data['shipping_slip'] = date("Y-m-d").$image_name;
                      $targetPath = DIR_UPLOAD.$filter_data['shipping_slip'];
                      move_uploaded_file($tmpName,$targetPath);
                      $result = $this->model_restapi_return->checkShipmentId($filter_data['master_return_id']);

                      if(empty($result)){
                        $this->data_packet->statusCode = 999;
                        $this->data_packet->message    = "Invalid master_return_id to upload courier details(Already shipment details uploaded).";
                        echo json_encode($this->data_packet); exit();
                      }
                      $filter_data['master_return_ids'] = $filter_data['master_return_id'];
                      $is_valid = $this->model_restapi_return->uploadCourierDetails($filter_data);
                      
                      if($is_valid){
                        //Set data for Response 
                        $this->data_packet->statusCode = 200;
                        $this->data_packet->message    = "Courier details uploaded successfully.";
                      }else{
                        $this->data_packet->statusCode = 999;
                        $this->data_packet->message    = "Courier details upload failed.";
                      }
                    }else{
                      $this->data_packet->statusCode   = 999;
                      $this->data_packet->message      = "Order No is missing.";
                    }
                  }else{
                    $this->data_packet->statusCode     = 999;
                    $this->data_packet->message        = "Order Id is missing.";
                  }

                }else{
                  $this->data_packet->statusCode       = 999;
                  $this->data_packet->message          = "Shipment Company Name is missing.";
                }

              }else{
                $this->data_packet->statusCode         = 999;
                $this->data_packet->message            = "Tracking Number is missing.";
              }

            }else{
              $this->data_packet->statusCode           = 999;
              $this->data_packet->message              = "Shipment Slip is missing.";
            }

          }else{
            $this->data_packet->statusCode            = 999;
            $this->data_packet->message               = "Master Return Id is missing.";
          }

        }else{
          $this->data_packet->statusCode              = 999;
          $this->data_packet->message                 = "Customer Id is missing.";
        }

      }else{
        $this->data_packet->statusCode                = 999;
        $this->data_packet->message                   = "Invalid Request Params";
      }
      
      echo json_encode($this->data_packet); exit();
    }

    /**
     * @info : Public method to get uploaded courier details for self-shippment returns type
     * @return: array, Courier details
     * @author: Nishu, Sept 2019 
    */
    public function getUploadedCourierDetails(){
      //Check No data passed in get parameters
      if( !empty($this->request) ){
        
        //Set request data to $data
        $data = $this->request;
       
        //Check for required data keys
        if(!empty($data['customer_id']) ){

          //Check for required data keys
          if(!empty($data['master_return_id']) ){

            //Get Returns shippment Couirer Details
            $courier_details = $this->return_info->getUploadedCourierDetails($data);

            if(!empty($courier_details) ) {
              
              //Set data for Response 
              $this->data_packet->statusCode = 200;
              $this->data_packet->message    = "Data Successfully Found.";
              $this->data_packet->data       = $courier_details;
            }else{
              $this->data_packet->statusCode = 200;
              $this->data_packet->message    = "Data not found.";
            }
          }else{
            $this->data_packet->statusCode   = 999;
            $this->data_packet->message      = "Master Return Id is missing.";
          }
          
        }else{
          $this->data_packet->statusCode   = 999;
          $this->data_packet->message      = "Customer Id is missing.";
        }

      }else{
        $this->data_packet->statusCode = 999;
        $this->data_packet->message    = "Invalid Request Params";
      }
      
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