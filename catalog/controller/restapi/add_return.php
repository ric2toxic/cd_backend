<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * @info : This is class for RestAPIs for mobile app abouts returns from customer against order(s)
 *         contains API(s) related to 
 *          -> Returns list for customer
 *          -> Return detail page APIs
 *
 * @author: Nishu, Sept 2019
*/
class ControllerRestapiAddReturn extends Controller
{

    ///////-----------Private Member variables--------------------

    private $data_packet = array();




    public  $registry;
    private $_limit_in_days_for_replacement = 15;
    private $_limit_in_days_for_quality     = 4;


    ///////-----------Public Methods-----------------------------

    public function __construct($registry) { 
        parent::__construct($registry);
        $this->registry = $registry;

        // CustomerOrderInfo
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
      if ($this->request->server['REQUEST_METHOD'] == 'GET') {
       
        //Set request data to $data
        $inputJSON   = file_get_contents('php://input');
        $data = json_decode( $inputJSON, TRUE );

        //Check for required data keys
        if(!empty($data['user_id']) ){

          //Check for required data keys
          if(!empty($data['order_id']) ){

            $rest_api = new Restapi($this->registry);
            $this->data_packet = $rest_api->validateRestApiAccess($data);

            if($this->data_packet['statusCode'] != 200){
              //Set and return Response of API
              echo json_encode($this->data_packet); exit();
            }
            $data['customer_id'] = $data['user_id'];

            //Get Return(s) List by given request params for single customer
            $data['limit_in_days_for_quality']     = $this->_limit_in_days_for_quality;
            $data['limit_in_days_for_replacement'] = $this->_limit_in_days_for_replacement;
            $returnable_products = $this->return_info->getReturnableProducts($data);

            if(!empty($returnable_products)){
              $order_product_ids = array_keys($returnable_products);
              $order_no = array_unique( array_column($returnable_products, 'order_no') );
              $resp = $this->return_info->getPickupAddress($order_product_ids, (int)$data['order_id'], $order_no[0]);
              $resp['products'] = $returnable_products;
            }

            if(!empty($resp)){
              //Set data for Response 
              $this->data_packet['statusCode'] = 200;
              $this->data_packet['data']       = $resp;
              $this->data_packet['message']    = "Data Successfully Found.";
            }else{
              $this->data_packet['statusCode'] = 200;
              $this->data_packet['message']    = "Data not found.";
            }
          }else{
            $this->data_packet['statusCode'] = 999;
            $this->data_packet['message']    = "Order Id is missing.";
          }
        }else{
          $this->data_packet['statusCode'] = 999;
          $this->data_packet['message']    = "User Id is missing.";
        }

      }else{
        $this->data_packet['statusCode'] = 999;
        $this->data_packet['message']    = "REQUEST_METHOD is not of type GET";
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
      if ($this->request->server['REQUEST_METHOD'] == 'POST') {
       
        //Set request data to $data
        $inputJSON   = file_get_contents('php://input');
        $data = json_decode( $inputJSON, TRUE );

        //Check for required data keys
        if(!empty($data['user_id']) ){

          //Check for required data keys
          if(!empty($data['order_id']) ){

            $rest_api = new Restapi($this->registry);
            $this->data_packet = $rest_api->validateRestApiAccess($data);

            if($this->data_packet['statusCode'] != 200){
              //Set and return Response of API
              echo json_encode($this->data_packet); exit();
            }
            $data['customer_id'] = $data['user_id'];

        //Validate Add return data, reeived in request
        $is_valid = $this->return_info->validateAddReturnsData($data);

        if(!empty($is_valid) && $is_valid['is_valid'] == false){
            //Set and return Response of API
            $this->data_packet['statusCode'] = 999;
            $this->data_packet['message']    = $is_valid['message'] ?? "Invalid Params to add return!";
            echo json_encode($this->data_packet); exit();
        }

          //Add reutrn 
          $is_added = $this->return_info->addNewReturnFromClient($data);
  
          if(!empty($is_added)){
            //Set data for Response 
            $this->data_packet['statusCode'] = 200;
            $this->data_packet['message']    = "Return Successfully Added.";
          }else{
            $this->data_packet['statusCode'] = 200;
            $this->data_packet['message']    = "Add Return Failed.";
          }
        }else{
          $this->data_packet['statusCode'] = 999;
          $this->data_packet['message']    = "Order Id is missing.";
        }
      }else{
        $this->data_packet['statusCode'] = 999;
        $this->data_packet['message']    = "User Id is missing.";
      }

      }else{
        $this->data_packet['statusCode'] = 999;
        $this->data_packet['message']    = "REQUEST_METHOD is not of type GET";
      }
      
      //Set and return Response of API
      echo json_encode($this->data_packet); exit();
    }


    
}