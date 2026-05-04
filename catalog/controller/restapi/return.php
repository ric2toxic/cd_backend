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
class ControllerRestapiReturn extends Controller
{

    ///////-----------Private Member variables--------------------

    private $data_packet = array();




    public  $registry;
    private $_limit_in_days_for_replacement = 15;
    private $_limit_in_days_for_quality     = 4;
    private $_order_no;
    private $_order_id;
    private $_master_return_id;






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
    public function getReturnListData(){
      $return_list = array();

      //Check No data passed in get parameters
      if ($this->request->server['REQUEST_METHOD'] == 'GET') {
       
        //Set request data to $data
        $inputJSON   = file_get_contents('php://input');
        $data = json_decode( $inputJSON, TRUE );

        //Check for required data keys
        if(!empty($data['user_id']) ){

          $rest_api = new Restapi($this->registry);
          $this->data_packet = $rest_api->validateRestApiAccess($data);

          if($this->data_packet['statusCode'] != 200){
            //Set and return Response of API
            echo json_encode($this->data_packet); exit();
          }
          $data['customer_id'] = $data['user_id'];

          //Get Return(s) List by given request params for single customer
          $return_list = $this->return_info->getReturnListData($data);

          if(!empty($return_list)){
            
            
            //Set data for Response 
            $this->data_packet['statusCode'] = 200;
            $this->data_packet['message']    = "Data Successfully Found.";
          }else{
            $this->data_packet['message']    = "Data not found.";
          }
        }else{
          $this->data_packet['statusCode'] = 999;
          $this->data_packet['message']    = "User Id is missing.";
        }

      }else{
        $this->data_packet['statusCode'] = 999;
        $this->data_packet['message']    = "REQUEST_METHOD is not of type GET";
      }
      $this->data_packet['data']         = $return_list;
      //Set and return Response of API
      echo json_encode($this->data_packet); exit();
    }

    /**
     * @info: Public method(API) for return detail page, for product return details
     * @return: array
     * @author: Nishu, Sept 2019
    */
    public function getReturnDetailPageData(){
      $resp = array();
      
      //Check No data passed in get parameters
      if ($this->request->server['REQUEST_METHOD'] == 'GET') {
       
        //Set request data to $data
        $inputJSON   = file_get_contents('php://input');
        $data = json_decode( $inputJSON, TRUE );

        //Check for required data keys
        if(!empty($data['user_id']) ){


          $rest_api = new Restapi($this->registry);
          $this->data_packet = $rest_api->validateRestApiAccess($data);

          if($this->data_packet['statusCode'] != 200){
            //Set and return Response of API
            echo json_encode($this->data_packet); exit();
          }
          $data['customer_id'] = $data['user_id'];

          //Check for required data keys
          if(!empty($data['master_return_id']) ){

              $resp = $this->return_info->getReturnDetailPage($data);

              if(!empty($resp)){
                
                //Set data for Response 
                $this->data_packet['statusCode'] = 200;
                $this->data_packet['message']    = "Data Successfully Found.";
                $this->data_packet['data']       = $resp;
              }else{
                $this->data_packet['statusCode'] = 999;
                $this->data_packet['message']    = "Data not found.";
              }
          }else{
            $this->data_packet['statusCode'] = 999;
            $this->data_packet['message']    = "Master Return Id is missing.";
          }

          
        }else{
          $this->data_packet['statusCode'] = 999;
          $this->data_packet['message']    = "User Id is missing.";
        }

      }else{
        $this->data_packet['statusCode'] = 999;
        $this->data_packet['message']    = "REQUEST_METHOD is not of type GET";
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
      if ($this->request->server['REQUEST_METHOD'] == 'GET') {
       
        //Set request data to $data
        $inputJSON   = file_get_contents('php://input');
        $data = json_decode( $inputJSON, TRUE );

        //Check for required data keys
        if(!empty($data['user_id']) ){

          $rest_api = new Restapi($this->registry);
          $this->data_packet = $rest_api->validateRestApiAccess($data);

          if($this->data_packet['statusCode'] != 200){
            //Set and return Response of API
            echo json_encode($this->data_packet); exit();
          }

              
          $data['customer_id'] = $data['user_id'];

          //Get Bank details for single customer
          $selector = array('bank_ac_holder_name', 'bank_ac_number', 'ifsc_code');
          $bank_data = Customer::getCustomerInfo($this->db, (int)$data['customer_id'], $selector);

          if(!empty($bank_data[$data['customer_id']]) ){

            $bank_details = $bank_data[$data['customer_id']] ?? array();
            
            $ifsc_code    = $bank_details['ifsc_code'] ?? '';
            $bank_address = getBankAddressByIFSC($ifsc_code);
            $bank_details['bank_address'] = $bank_address;
            
            //Set data for Response 
            $this->data_packet['statusCode'] = 200;
            $this->data_packet['message']    = "Data Successfully Found.";
            $this->data_packet['data']       = $bank_details;
          }else{
            $this->data_packet['statusCode'] = 999;
            $this->data_packet['message']    = "Data not found.";
          }

        }else{
          $this->data_packet['statusCode'] = 999;
          $this->data_packet['message']    = "User Id is missing.";
        }

      }else{
        $this->data_packet['statusCode'] = 999;
        $this->data_packet['message']    = "REQUEST_METHOD is not of type GET";
      }
      
      echo json_encode($this->data_packet); exit();
    }

    /**
     * @info: Public method API to upload Courier details by customer
     *         Only for those returns whose pickup-type is 'self_courier'
     * @author: Nishu, Sept 2019
    */
    public function uploadCourierDetails_new(){
      //Check No data passed in get parameters
      if ($this->request->server['REQUEST_METHOD'] == 'POST') {

        //Set request data to $filter_data
        $filter_data = $_REQUEST;

        //Check for required data keys
        if(!empty($filter_data['user_id']) ){

          $rest_api = new Restapi($this->registry);
          $this->data_packet = $rest_api->validateRestApiAccess($filter_data);

          if($this->data_packet['statusCode'] != 200){
            //Set and return Response of API
            echo json_encode($this->data_packet); exit();
          }

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
                        $this->data_packet['statusCode'] = 999;
                        $this->data_packet['message']    = "Invalid master_return_id to upload courier details(Already shipment details uploaded)";
                        echo json_encode($this->data_packet); exit();
                      }
                      $filter_data['master_return_ids'] = $filter_data['master_return_id'];
                      $is_valid = $this->model_restapi_return->uploadCourierDetails($filter_data);
                      
                      if($is_valid){
                        //Set data for Response 
                        $this->data_packet['statusCode'] = 200;
                        $this->data_packet['message']    = "Courier details uploaded successfully.";
                      }else{
                        $this->data_packet['statusCode'] = 999;
                        $this->data_packet['message']    = "Courier details upload failed.";
                      }
                    }else{
                      $this->data_packet['statusCode']   = 999;
                      $this->data_packet['message']      = "Order No is missing.";
                    }
                  }else{
                    $this->data_packet['statusCode']     = 999;
                    $this->data_packet['message']        = "Order Id is missing.";
                  }

                }else{
                  $this->data_packet['statusCode']       = 999;
                  $this->data_packet['message']          = "Shipment Company Name is missing.";
                }

              }else{
                $this->data_packet['statusCode']         = 999;
                $this->data_packet['message']            = "Tracking Number is missing.";
              }

            }else{
              $this->data_packet['statusCode']           = 999;
              $this->data_packet['message']              = "Shipment Slip is missing.";
            }

          }else{
            $this->data_packet['statusCode']            = 999;
            $this->data_packet['message']               = "Master Return Id is missing.";
          }

        }else{
          $this->data_packet['statusCode']              = 999;
          $this->data_packet['message']                 = "Customer Id is missing.";
        }

      }else{
        $this->data_packet['statusCode']                = 999;
        $this->data_packet['message']                   = "REQUEST_METHOD is not of type POST";
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
      $resp = array();
      
      //Check No data passed in get parameters
      if ($this->request->server['REQUEST_METHOD'] == 'GET') {
       
        //Set request data to $data
        $inputJSON   = file_get_contents('php://input');
        $data = json_decode( $inputJSON, TRUE );

        //Check for required data keys
        if(!empty($data['user_id']) ){

          $rest_api = new Restapi($this->registry);
          $this->data_packet = $rest_api->validateRestApiAccess($data);

          if($this->data_packet['statusCode'] != 200){
            //Set and return Response of API
            echo json_encode($this->data_packet); exit();
          }
          $data['customer_id'] = (int)$data['user_id'];

          //Check for required data keys
          if(!empty($data['master_return_id']) ){

            //Get Returns shippment Couirer Details
            $courier_details = $this->return_info->getUploadedCourierDetails($data);

            if(!empty($courier_details) ) {
              
              //Set data for Response 
              $this->data_packet['statusCode'] = 200;
              $this->data_packet['message']    = "Data Successfully Found.";
              $this->data_packet['data']       = $courier_details;
            }else{
              $this->data_packet['statusCode'] = 200;
              $this->data_packet['message']    = "Data not found.";
            }
          }else{
            $this->data_packet['statusCode']   = 999;
            $this->data_packet['message']      = "Master Return Id is missing.";
          }
          
        }else{
          $this->data_packet['statusCode']     = 999;
          $this->data_packet['message']        = "User Id is missing.";
        }

      }else{
        $this->data_packet['statusCode'] = 999;
        $this->data_packet['message']    = "REQUEST_METHOD is not of type GET";
      }
      
      echo json_encode($this->data_packet); exit();
    }

    


///////////////////////////////////////////////////////////////////////////



    public function initReturnRequest(){
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $rt = array();
            $total_weight  = 0;
            $refund_amount = 0;
            $inputJSON = file_get_contents('php://input');
            $request   = json_decode($inputJSON, TRUE );
            if(isset($request['access_token'])){
                $access_token   = $request['access_token'];
                $user_id        = $request['user_id'];
                $data['return_data'] = $request['data'];
                $data['user_id'] = $user_id;
                $data['shipping_method'] = $request['shipping_method'];
                $this->load->model('restapi/service');
                $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
                if($check_access_token > 0){
                    $return = $this->returnValue($data,0);
                    if(isset($return) && !empty($return)){
                        $rt['status']      = 1;
                        $rt['status_text'] = "success";
                        $rt['message']     = 'Return summary';
                        $rt['data']        = $return['data'];
                        $rt['shipping_method'] = $request['shipping_method'];
                        $rt['shipping_adjustment_ammount'] = $return['shipping_adjustment_ammount'];
                        $rt['tentative_refund_amount']     = $return['tentative_refund_amount'];
                    }
                }else{
                    $rt['error_code'] = '1003';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Invalid Access Token.';
                }
           }else{
               $rt['error_code'] = '1002';
               $rt['status'] = '0';
               $rt['status_text'] = 'Signup failed';
               $rt['message'] = 'http request does not have access token.';
           }
        }
        echo json_encode($rt); exit;
    }

    public function addReturn(){
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
             $rt = array();
             $total_weight  = 0;
             $refund_amount = 0;
             $data = array();   
             $inputJSON = file_get_contents('php://input');
             if(!empty($inputJSON)){
                $request   = json_decode($inputJSON, TRUE );
                $access_token = $request['access_token'];
                $user_id = $request['user_id'];
                $data['shipping_method'] = $request['shipping_method'];
                $data['return_data'] = $request['data'];
                $this->_order_no = $request['order_no'];
                $this->_order_id = $request['order_id'];
             }else{
                $access_token = $this->request->post['access_token'];
                $user_id = $this->request->post['user_id'];
                $dataArray = $this->request->post['data'];
                $dataArray = htmlspecialchars_decode($dataArray);
                $data['return_data'] = json_decode($dataArray,true);
                $data['shipping_method'] = $this->request->post['shipping_method'];
                $app_version_code =  $this->request->post['app_version_code'];
                $this->_order_no = $this->request->post['order_no'];
                $this->_order_id = $this->request->post['order_id'];
             }
             $headers = getallheaders();
             if(isset($access_token)){
                 if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'CRM APP'){
                     $data['crm_user_id'] = $headers['crm_user_id'];
                 }
                 
                 
                 $data['user_id'] = $user_id;
                 if(isset($app_version_code)){
                    $data['app_version_code'] = $app_version_code;
                 }
                 $this->load->model('restapi/service');
                 $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);                
                 if($check_access_token > 0){
                     $return = $this->returnValue($data,1);
                     if(isset($return) && !empty($return)){
                         $rt['status']      = 1;
                         $rt['status_text'] = "success";
                         $rt['message']     = $return['message'];
                         $rt['shipping_method'] = $data['shipping_method'];
                         $rt['shipping_adjustment_ammount'] = $return["shipping_adjustment_ammount"];
                         $rt['tentative_refund_amount']     = $return["tentative_refund_amount"];
                     }
                }else{
                    $rt['error_code'] = '1003';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Invalid Access Token.';
                }
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Signup failed';
                $rt['message'] = 'http request does not have access token.';
            }
        }
        echo json_encode($rt); exit;
    }

    public function updateBankDetails(){
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $inputJSON = file_get_contents('php://input');
            $request   = json_decode($inputJSON, TRUE );
            if(isset($request['access_token'])){
                $access_token    = $request['access_token'];
                $user_id         = $request['user_id'];
                $this->load->model('restapi/service');
                $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
                if($check_access_token > 0){
                    $account_holder_name = $request['account_holder_name'];
                    $data['bank_ac_holder_name']    = $account_holder_name;
                    if(empty($account_holder_name) || !isset($request['account_holder_name'])){
                        $rt['status']      = 0;
                        $rt['status_text'] = "failed";
                        $rt['message']     = 'Account holder name is requried';
                        echo json_encode($rt); exit;
                    }
                    $account_number        = $request['account_number'];
                    $data['bank_ac_number']    = $account_number;
                    if(empty($account_number) || !isset($request['account_number'])){
                        $rt['status']      = 0;
                        $rt['status_text'] = "failed";
                        $rt['message']     = 'Account number is requried';
                        echo json_encode($rt); exit;
                    }
                    $ifsc_code             = $request['ifsc_code'];
                    $data['ifsc_code']    = $ifsc_code;
                    if(empty($ifsc_code) || !isset($request['ifsc_code'])){
                        $rt['status']      = 0;
                        $rt['status_text'] = "failed";
                        $rt['message']     = 'Ifsc code is requried';
                        echo json_encode($rt); exit;
                    }

                    $json = validateBankIFSC($ifsc_code,'return');
                    if($json == '"Not Found"'){
                        $rt['status']      = 0;
                        $rt['status_text'] = "failed";
                        $rt['message']     = 'Ifsc code is not valid';
                        echo json_encode($rt); exit;
                    }else{
                        $this->load->model('account/customer');
                            $data['bank_ac_number']=$request['account_number'];
                            $data['ifsc_code']=$request['ifsc_code'];
                            $data['customer_id']=$user_id;
                            $check_if_bank_account_exist=$this->model_account_customer->checkIfBankAccountExists($data);
                           
                            if(empty($check_if_bank_account_exist)){
                                $rt['status']      = 0;
                                $rt['status_text'] = "failed";
                                $rt['message']     = 'Sorry this bank account is already used by another customer. Please provide a different bank a/c details.';
                                echo json_encode($rt); exit;
                            }else{
                                 $check_if_bank_account_block=$this->model_account_customer->checkIfBankAccountIsBlock($data);
                                 if(!empty($check_if_bank_account_block)){
                                    $rt['status']      = 0;
                                    $rt['status_text'] = "failed";
                                    $rt['message']     = 'Sorry this bank account is block. Please provide a different bank a/c details.';
                                    echo json_encode($rt); exit;
                                }else{
                                    if($this->model_account_customer->updateCustomerDetails($data,$user_id)){
                                    $rt['status']      = 1;
                                    $rt['status_text'] = "Bank details sucessfully saved";
                                    }
                                }
                                
                            }
                        
                    }
                }else{
                    $rt['error_code'] = '1003';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Invalid Access Token.';
                 }
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Signup failed';
                $rt['message'] = 'http request does not have access token.';
            }
        }
        echo json_encode($rt); exit;
    }

    private function returnValue($data,$add_return_flag){

        if(isset($data) && !empty($data)){
            $this->load->model('account/return');
            $this->load->model('restapi/service');
            $this->load->model('restapi/return');
            $total_weight    = 0;
            $refund_amount   = 0;
            $last_insert_id  = 0;
            $shipping_charge = 0;
            $shipping_method = $data['shipping_method'];
            $user_id = $data['user_id'];
            $return = array();
            if(isset($data['crm_user_id'])){
                $crm_user_id = $data['crm_user_id'];
                $source = 'crm';
            }else{
                $crm_user_id = 0;
                $source = 'android_app';
            }
            $defected_images=array();

            $return_data = $data['return_data'];

            foreach ($return_data as $key => $value) {
               if(empty($value['return_quantity'])){
                    $rt['status'] = 0;
                    $rt['status_text'] = "failed";
                    $rt['message'] = "Return product quantity is required";
                    echo json_encode($rt); exit;
                }
                if(empty($value['return_reason_id'])){
                    $rt['status'] = 0;
                    $rt['status_text'] = "failed";
                    $rt['message'] = "Return Reason is required";
                    echo json_encode($rt); exit;
                }else{
                    if($value['return_reason_id'] == RETURN_REASON_IDS['Order_Error_By_Customer']){
                        $value['return_reason_id'] = RETURN_REASON_IDS['Wrong_Item_Received'];
                    }
                }

                $fix_order_product_id = !empty($value['order_product_id']) ? $value['order_product_id'] : $key ;
                $value['order_product_id'] =  $fix_order_product_id;
                $result = $this->model_restapi_return->getRefundParameters((int)$value['order_product_id']);
                $shipping_code      = '';
                if(isset($result) && !empty($result)){
                    $price_per_piece    = $result->row['price_per_piece'];
                    $discount_per_piece = $result->row['discount_per_piece'];
                    $weight_per_piece   = $result->row['weight_per_piece'];
                    $output_tax_rates   = $result->row['output_tax_rates'];
                    $value['model']     = $result->row['model'];
                    $shipping_code      = strtolower($result->row['shipping_code']);
                    $total_qty          = $result->row['quantity'] * $result->row['piece_in_set'];

                    //Check if return request is for qty more then purchased quantity from oc_order_product
                    if($value['return_quantity'] > $total_qty){
                        $value['return_quantity'] = $total_qty;
                    }
                }

                $_quality_return_reason_id = array( 
                                            RETURN_REASON_IDS['Quality_Issue'], 
                                            RETURN_REASON_IDS['Pricing_Issue'], 
                                            RETURN_REASON_IDS['Wrong_Item_Received'] 
                                        );

                if(in_array($value['return_reason_id'], $_quality_return_reason_id)){
                    //Return can not be added with quality issue,If order marked as store_pickup
                    if(in_array($shipping_code, array("store_pickup","warehouse_pickup","weight.weight_0"))){
                        $return['tentative_refund_amount']     = 0;
                        $return['shipping_adjustment_ammount'] = 0;
                        $return['message'] = "You cannot return goods due to that ". $value['returnReplacementIssue'] ." issues, as they were picked up physically by you from our warehouse / store.";
                        return $return;
                    }

                    $refund = ( ( (float)$price_per_piece +  (float)$discount_per_piece ) *
                                        ( 1 + (float)$output_tax_rates / 100 ) * (int)$value['return_quantity'] );
                    $value['refund_amount']  =  $this->currency->format($refund,'INR');
                    $refund_amount += $refund;
                }else{
                    $value['replacement_meassage'] = 'With "Manufacturing defect/damaged goods", policy we do not refund but we shall replace Manufacturing defect/damaged products within 10 days after inspection';
                }
                if(in_array($value['return_reason_id'], $_quality_return_reason_id)){
                    $total_weight += (float)$weight_per_piece * (int)$value['return_quantity'];
                }

                $_replacement_return_reason_id  = array(
                                             RETURN_REASON_IDS['Manufacturing_Defect'],
                                             RETURN_REASON_IDS['Wrong_Item_Received_Replacement']
                                            );
                $_return_reason_text    = array(
                                         RETURN_REASON_IDS['Manufacturing_Defect']    => "Manufacturing defect/damaged goods",
                                         RETURN_REASON_IDS['Quality_Issue']           => "Quality issue",
                                         RETURN_REASON_IDS['Pricing_Issue']           => "Pricing issue",
                                         RETURN_REASON_IDS['Wrong_Item_Received']     => "Worng Itmes Recevied",
                                         RETURN_REASON_IDS['Wrong_Item_Received_Replacement'] => "Wrong Item Received(Replacement)"
                                        );

                if(in_array($value['return_reason_id'],$_replacement_return_reason_id)){
                    $value['return_reason_name'] = $_return_reason_text[$value['return_reason_id']];
                    $value['return_type']        = 'Replacement';
                }
                if(in_array($value['return_reason_id'], $_quality_return_reason_id)){
                    $value['return_reason_name'] = $_return_reason_text[$value['return_reason_id']];
                    $value['return_type']        = 'Return';
                }

                if($add_return_flag){
                    $return_add['order_product_id']  = $fix_order_product_id;
                    $return_add['return_reason_id']  = $value['return_reason_id'];                    
                    $return_add['quantity']          = $value['return_quantity'];
                    $value['quantity']               = $value['return_quantity'];
                    $return_add['comment']           = $value['comment'];
                    $return_add['shipping_method']   = $shipping_method;
                    $return_add['payment_method']    = 'not_decided';
                    $return_add['crm_user_id']       = $crm_user_id;
                    if(isset($data['app_version_code'])){
                        $return_add['app_version_code']  = $data['app_version_code'];
                    }
                    
                    if(empty($this->_master_return_id)){
                        $master_return_id =  $this->model_account_return->checkLastReturn($this->_order_id, $return_add['order_product_id']);
                        if(isset($master_return_id)){
                            $last_master_return_id      = $master_return_id['master_return_id'];
                            $this->_master_return_id    = $last_master_return_id;
                        }else{
                            $master_return_data['customer_id'] = $user_id;
                            $master_return_data['shipping_method'] = $shipping_method;
                            $master_return_data['order_id'] = $this->_order_id;
                            $master_return_id = $this->model_account_return->addMasterReturn($master_return_data,$source);
                            if(!empty($master_return_id)){
                                $this->_master_return_id = $master_return_id['last_master_return_id'];
                            }
                        }
                    }
                    $return_add['master_return_id'] = $this->_master_return_id;
                    if(!empty($return_add)){
                        $images_path =  $this->model_restapi_return->addReturn($return_add,$user_id);
                        if(isset($images_path)){
                            $defected_images[$fix_order_product_id] = $images_path[$fix_order_product_id];
                        }
                        
                        /* for pickup address - self courier, add extra return with self_shipment action status */
                        if($shipping_method == 'self_courier'){
                            //Get ReturnId for latest Insertion in oc_return table
                            $return_id = $this->model_restapi_return->getAddedReturnId($return_add);
                            $return_action_base = new ReturnActionBase($this);
                            $return_action_base->updateExistingAsInactive($return_id);
                            $return_add['return_action_id'] = RETURN_ACTION_IDS['Self_Shipment'];
                            $this->model_restapi_return->addReturn($return_add,$user_id);
                        }
                    }
                    if($shipping_method == 'self_courier'){
                        $return['message']  = 'Thank You! Your return request is in processing. We shall update you shortly. You have to upload courier details with in 48 hours from order page. For any clarification,please call on +91 141 4049163';
                    }else{
                        $return['message'] = 'Thank You! Your return request is in processing. We shall update you shortly.';
                    }
                }
                $return_data[$fix_order_product_id] = $value;
            }

            if($shipping_method == 'wsb_pickup'){
                if(!empty($total_weight)){
                    $total_weight = ceil($total_weight);
                    if( $total_weight >= 1 ){
                        $shipping_charge = ($total_weight - 1) * 30 + 70;
                    }
                }else{
                    $shipping_charge = 0;
                }
            }
            if($refund_amount != 0){
                $refund_amount = (float)$refund_amount - (float)$shipping_charge;
            }else{
                $refund_amount = 0;
            }
            $refund_amount = $this->currency->format($refund_amount,'INR');
            if(empty($add_return_flag)){
                $return['data'] = $return_data;
            }
            $return['shipping_adjustment_ammount'] = $this->currency->format($shipping_charge,'INR');
            $return['tentative_refund_amount']     = $refund_amount;

            if($add_return_flag){
                 $product_data['updated']  = '';
                 $product_data['new']      = $return_data;
                 if(!empty($defected_images)){
                    $product_data['defect_images'] = $defected_images;
                 }
                 $product_data['image_width'] = $this->config->get('config_image_additional_width');
                 $product_data['image_height'] = $this->config->get('config_image_additional_height');

                 $customerInfo = $this->model_restapi_service->checkCustomerByID((int)$user_id);
                 $product_data['buyer_name']   = ucfirst($customerInfo['firstname']);
                 $product_data['buyer_email']  = $customerInfo['email'];
                 $product_data['buyer_mobile'] = $customerInfo['telephone'];

                 $product_data['order_no'] = $this->_order_no;
                 $product_data['tentative_refund_amount'] = $refund_amount;
                 $product_data['shipping_method'] = $shipping_method;
                 $product_data['comment_message'] = "We have received your return/replacement request for the following items in your order : #";
                 $template = MailTemplate::getReturnMailTemplate($product_data);
                 $subject  = "Return Request - Order No: ".$this->_order_no."-".Date("d/m/Y");
                 $this->model_account_return->sendMailFromApi($product_data,$subject,$template);
            }
     }
     return $return;
    }


    /**
    * Protected method addMissingComboOrderProducts() to add missing combo product return
    * @param  Array $data
    * @return Array $data
    * @author Nishu July 2018
    */
    /*protected function addMissingComboOrderProductsForReturn($data)
    {
        //All order_product_ids for all returns to be added
        $op_ids = array_keys($data);

        //Create Object for ReturnInfo Class
        $return_info = new ReturnInfo($this);

        //Get all order_product_ids with group over combo_product_id as array key
        $combo_products = $return_info->getComboProductForOrderProductIds($op_ids);

        //Initialize as empty array
        $combo_data = array();

        //Check if there are exist order_products as combo products
        if(!empty($combo_products) && !empty($data)) {

            //Loop for all combo products
            foreach ($combo_products as $combo_product_id => $si_wise_associate_op_ids) {
                foreach ($si_wise_associate_op_ids as $seller_invoice_id => $associate_op_ids) {
                    $missing_combo_product_from_data = array_diff(
                                                          explode(',', $associate_op_ids), 
                                                          $op_ids
                                                          );
                    //Data exist for any product for this combo order_product
                    $op_ids_exist_in_data = array_intersect(explode(',', $associate_op_ids), $op_ids);
                    
                    //If any combo order product is missing from $data
                    if(!empty($missing_combo_product_from_data) && !empty($op_ids_exist_in_data) ){
                        //Already exist order_product_id wise data in $data
                        $op_id_exist_in_data  = $op_ids_exist_in_data[0];

                        //Setting data for missing order_product from combo
                        foreach ($missing_combo_product_from_data as $key => $missing_op_id) {
                            //Copy data for missing associated order_product to $data array
                            $data[$missing_op_id]                     = $data[$op_id_exist_in_data];
                            $data[$missing_op_id]['order_product_id'] = $missing_op_id;
                        }
                    }
                }
            }
        }

        //Refactor $data for combo products on basis of qty and reason
        $data = $this->refactorReturnDataForComboProducts($data, $combo_products);

        //Update $data's values 
        return $data;
    }*/

    /**
     * @info: Public method to process combo products to add Return(s)
     *         for Quantity and Return/Replacement Reason
     * @author: Nishu, July 2018
    */
   /* public function refactorReturnDataForComboProducts($data, $combo_products){
        
        if(!empty($data) && !empty($combo_products)){
            //////////////---------Start data manipulation---------//////////////

            //Return will added for minimum qty in order_products of same combo
            foreach ($combo_products as $combo_id => $si_wise_op_ids) { 
                //si_wise_op_ids means to seller invoice id wise data
                foreach ($si_wise_op_ids as $combo_id => $op_ids) {
                    $op_ids = explode(',', $op_ids);

                    $min_qty = 0;
                    $return_reason = 0;
                    //Loop over order_product_ids combo_wise
                    foreach ($op_ids as $op_id) {
                        if(empty($min_qty) || $min_qty > $data[$op_id]['return_quantity']){
                            $min_qty         = $data[$op_id]['return_quantity'];
                            $return_reason   = $data[$op_id]['return_reason_id'];
                        }
                    }
                    
                    //OverWrite return's qty of all products from same combo
                    foreach ($op_ids as $op_id) {
                        $data[$op_id]['return_quantity']  = $min_qty;
                        $data[$op_id]['return_reason_id'] = $return_reason;
                    }
                }
            }

            //////////////---------End data manipulation---------//////////////
        }

        return $data;
    }*/



    public function myReturns(){
        $headers = getallheaders();
        if(isset($headers['crm_user_id']) && isset($headers['crm_role_id']) && (int)$headers['crm_role_id'] == (int)CRM_FRANCHISE_ROLE_ID){
            $rt['error_code'] = '1002';
            $rt['status'] = '0';
            $rt['status_text'] = 'Failed';
            $rt['message'] = 'You are not authorized to see this section.';
        }
        else if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $inputJSON = file_get_contents('php://input');
            $request   = json_decode($inputJSON, TRUE );
            if(isset($request['access_token'])){
                $access_token   = $request['access_token'];
                $customer_id    = $request['user_id'];
                $this->load->model('restapi/service');
                $this->load->model('restapi/return');
                $this->load->model('account/return');
                $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$customer_id);
                if($check_access_token > 0){
                    $master_returns = $this->model_restapi_return->getMasterReturns((int)$customer_id,0);
                    if(!empty($master_returns->num_rows)){
                        $master_return_ids = array_column($master_returns->rows,"master_return_id");
                        $master_return     = array_combine($master_return_ids,$master_returns->rows);
                        $result = $this->model_account_return->getReturnsByOrderId($master_return_ids,'');
                        foreach ($result->rows as $key => $value) {
                            $returns[$value['order_no']][$value['master_return_id']]["return_no"] = $master_return[$value['master_return_id']]['return_no']; 
                            $returns[$value['order_no']][$value['master_return_id']]["order_no"]  = $value['order_no'];
                            $returns[$value['order_no']][$value['master_return_id']]["order_product_id"]  = $value['order_product_id'];
                            $returns[$value['order_no']][$value['master_return_id']]["master_return_id"]  = $value['master_return_id'];
                            if(!isset($returns[$value['order_no']][$value['master_return_id']]["no_of_pieces"])){
                                $returns[$value['order_no']][$value['master_return_id']]["no_of_pieces"] = 0;
                            } 
                            //$returns[$value['order_no']]["return_id"]  = $value['return_id'];
                            $returns[$value['order_no']][$value['master_return_id']]["is_show_cancel"] = 0;
                            if(
                                $value['return_action_id'] == 0 
                                        || 
                                $value['return_action_id'] == RETURN_ACTION_IDS['Pending']
                            ){
                                $returns[$value['order_no']][$value['master_return_id']]["no_of_pieces"] += $value['quantity'];
                                $returns[$value['order_no']][$value['master_return_id']]["is_show_cancel"] = 1;
                            }
                        }
                        if(isset($returns) && !empty($returns)){
                            $rt['status']      = 1;
                            $rt['status_text'] = "success";
                            $rt['message']     = 'Return Found';
                            $rt['data']        = $returns;
                        }else{
                            $rt['status']      = 1;
                            $rt['status_text'] = "success";
                            $rt['message']     = 'Return Not Found';
                        }
                    }else{
                        $rt['status']      = 1;
                        $rt['status_text'] = "success";
                        $rt['message']     = 'Return Not Found';
                    }

                }else{
                    $rt['error_code'] = '1003';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Invalid Access Token.';
                }
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Signup failed';
                $rt['message'] = 'http request does not have access token.';
            }
        }
        else{
            $rt['error_code'] = '1001';
            $rt['status'] = '0';
            $rt['status_text'] = 'Failed';
            $rt['message'] = 'http request does not have post data.';
        }
        echo json_encode($rt); exit;
    }

    public function uploadCourierDetails(){
      $data = array();
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
          $data['master_return_ids'] = $this->request->post['master_return_ids'] ?? '';
          $data['tracking_no']       = $this->request->post['tracking_no'] ?? '';
          $data['shipment_company']  = $this->request->post['shipment_company'] ?? '';
          $data['order_id']          = $this->request->post['order_id'] ?? '';
          $data['order_no']          = $this->request->post['order_no'] ?? '';
          $customer_id               = $this->request->post['user_id'] ?? '';

          if(
            empty($data['master_return_ids'])
            || empty($data['tracking_no'])
            || empty($data['shipment_company'])
            || empty($data['order_id'])
            || empty($data['order_no'])
            || empty($customer_id)
          ){

            $this->load->model('restapi/service');
            $this->load->model('restapi/return');
            if(isset($this->request->post['access_token'])){
                $access_token   = $this->request->post['access_token'] ?? '';
                
                $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$customer_id);
                if($check_access_token > 0){
                   
                    $data['customer_id']       = $customer_id;
                    $image_name  = $_FILES['shipment_slip']["name"];
                    $tmpName     = $_FILES['shipment_slip']["tmp_name"];
                    $currentDate = date("Y-m-d");
                    $data['shipping_slip'] = $currentDate.$image_name;
                    $targetPath = DIR_UPLOAD.$data['shipping_slip'];
                    
                    $result = $this->model_restapi_return->checkShipmentId( (array)$data['master_return_ids'] );
                    if(!empty($result)){
                        foreach($result->rows as $master_return_id){
                                $master_return_ids[] = $master_return_id['master_return_id'];
                        }
                        $data['master_return_ids'] = $master_return_ids;
                    }
                    if($this->model_restapi_return->uploadCourierDetails($data)){
                        $rt['status']      = 1;
                        $rt['status_text'] = "success";
                        $rt['message']     = 'Courier details upload successfully';
                    }else{
                        $rt['status']      = 0;
                        $rt['status_text'] = "Failed";
                        $rt['message']     = 'Courier details upload failed';
                    }
                }else{
                    $rt['error_code'] = '1003';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Invalid Access Token.';
                }
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Signup failed';
                $rt['message'] = 'http request does not have access token.';
            }
          }else{
            $rt['status']      = 0;
            $rt['status_text'] = "Failed";
            $rt['message']     = 'Request Param is missing';
          }
        }
        echo json_encode($rt); exit;
    }

    public function getCreditNote(){
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $inputJSON = file_get_contents('php://input');
            $request   = json_decode($inputJSON, TRUE );
            if(isset($request['access_token'])){
                $access_token   = $request['access_token'];
                $customer_id    = $request['user_id'];
                $this->load->model('restapi/service');
                $this->load->model('restapi/return');
                $this->load->model('account/return');
                $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$customer_id);
                if($check_access_token > 0){
                    $result = $this->model_account_return->findCreditNote('',$customer_id);
                    if(!empty($result)){
                        $credit_note = $this->model_account_return->crediteNote($result);
                        if(!empty($credit_note)){
                            $rt['status']      = 1;
                            $rt['status_text'] = "success";
                            $rt['message']     = '';
                            $rt['data']        = $credit_note;
                        }
                    }else{
                        $rt['status']      = 1;
                        $rt['status_text'] = "success";
                        $rt['message']     = 'No Records Found';                        
                    }
                }else{
                    $rt['error_code'] = '1003';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Invalid Access Token.';
                }
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Signup failed';
                $rt['message'] = 'http request does not have access token.';
            }
        }
        echo json_encode($rt); exit;
    }

    public function ShowReturns(){
         if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $inputJSON = file_get_contents('php://input');
            $request   = json_decode($inputJSON, TRUE );
            if(isset($request['access_token'])){
                $access_token   = $request['access_token'];
                $customer_id    = $request['user_id'];
                $master_return_id = $request['master_return_id'];
                $this->load->model('restapi/service');
                $this->load->model('restapi/return');
                $this->load->model('tool/image');
                $this->load->model('catalog/product');
                $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$customer_id);
                if($check_access_token > 0){
                    $master_return_data = $this->model_restapi_return->getShipmentMethod((int)$master_return_id);
                    if(!empty($master_return_data)){
                        $show_return['shipping_method'] = $master_return_data['shipping_method'];
                        $order_id = $master_return_data['order_id'];
                    }
                    $returns_array = $this->model_restapi_return->getReturns((int)$master_return_id);
                    $order_product_ids = array_keys($returns_array);
                    $returns = array_column($returns_array,'order_product_id');
                    $return_details = array_combine($returns,$returns_array);
                    $show_return['return_data'] = $return_details;
                    $show_return['user_id'] = $customer_id;
                    $view_return = $this->returnValue($show_return,'');
                    $final_return = $view_return['data'];
                    $product_detail = $this->model_restapi_return->findProductsDetails(array_unique($returns)); 
                    if(!empty($product_detail)){
                        $product_details = array_column($product_detail,'product_id');
                        $images = $this->model_catalog_product->getProductImagesByProductsIds($product_details);
                        $i = 0;
                        foreach($product_detail as $value){
                            $final_return[$value['order_product_id']]['name'] = $value['name'];
                            $final_return[$value['order_product_id']]['model'] = $value['model'];
                            $image = $this->model_tool_image->resize(
                                                            $images[$i]['image'],
                                                            $this->config->get('config_image_additional_width'),
                                                            $this->config->get('config_image_additional_height')
                                                        );
                            $final_return[$value['order_product_id']]['image']  = $image; 
                            $i++;
                        }
                        $rt['status']      = 1;
                        $rt['status_text'] = "success";
                        $rt['message']     = 'View Return';
                        $rt['data']        = $final_return;
                        $rt['shipping_adjustment_ammount'] = $view_return['shipping_adjustment_ammount'];
                        $rt['tentative_refund_amount']     = $view_return['tentative_refund_amount'];
                        $final_return['shipping_method']   = $master_return_data['shipping_method'];
                    }else{
                        $rt['status'] = '0';
                        $rt['status_text'] = 'Failed';
                        $rt['message'] = 'Invalid Data';
                    }
                }else{
                    $rt['error_code'] = '1003';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Invalid Access Token.';
                }
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Signup failed';
                $rt['message'] = 'http request does not have access token.';
            }
         }
         echo json_encode($rt); exit;
    }

    public function cancelReturn(){
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $inputJSON = file_get_contents('php://input');
            $request   = json_decode($inputJSON, TRUE );
            if(isset($request['access_token'])){
                $access_token   = $request['access_token'];
                $customer_id    = $request['user_id'];
                $master_return_id = $request['master_return_id'];
                $this->load->model('restapi/service');
                $this->load->model('account/return');
                $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$customer_id);
                if($check_access_token > 0){
                    $result = $this->model_account_return->cancelReturn($master_return_id);
                    if($result){
                        $rt['status']      = 1;
                        $rt['status_text'] = "success";
                        $rt['message']     = 'Return Deleted';
                    }else{
                        $rt['status']      = 0;
                        $rt['status_text'] = "Failed";
                        $rt['message']     = 'Error';
                    }
                }else{
                    $rt['error_code'] = '1003';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Invalid Access Token.';
                }
            }else{
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Signup failed';
                $rt['message'] = 'http request does not have access token.';
            }
        }
         echo json_encode($rt); exit;
    }
}