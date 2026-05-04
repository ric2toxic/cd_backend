<?php
class ControllerCrmapiCrmapi extends Controller 
{    
	/*
    * Get Resent Activities
    * Date : 29-04-2017
    * Description : get resent activities of customers
    */
    public function getResentActivities()
    { 

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
        }

        $customer_ids   =  $request['customer_ids'];
        $limit          =  $request['limit'];
        $offset         =  $request['offset'];
        $flag           =  $request['flag'];
       
        //return both array      
        $activities = array('activities' => array(), 'count' => 0);


        echo json_encode($activities); exit;     
    }

    /**
    * Get pincode details for check COD/ESS from logistic gati,fedex
    * Date : 05-05-2017
    * Function changed by nilesh as per new requirement in ESS location
    */
   public function getPincodeDetails() {
        $this->load->library('logisticsadvisor');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode($inputJSON, TRUE);
        }
        $pincode = !empty($request['pincode']) ? $request['pincode'] : '';
        $order_info = array('pincode' => $pincode, 'zone_id' => 0);
        $logisticsadvisor = new Logisticsadvisor($order_info, $this);
        $courier_servicablity = $logisticsadvisor->getAdvise();
        $data = array();
        $courier = '';
        foreach ($courier_servicablity as $key => $value) {
            if (isset($value['serviceability'])) {
                if ($value['courier'] == 'Delhivery') {
					$courier = 'Delhivery (Air)';
				} else if ($value['courier'] == 'Trux Cargo') {
					$courier = 'Delhivery surface';
				} else {
					$courier = $value['courier'];
				}
                $data['logistic_data'][] = array(
                    'courier_logistic' => $courier,
                    'is_serviceable' => $value['is_serviceable'],
                    'serviceability' => $value['serviceability'],
                    'cod' => $value['cod'],
                    'prepaid' => $value['prepaid'],
                    'serviceability' => $value['serviceability'],
                    'location' => $value['location'],
                    'distance' => $value['distance'],
                    'additional_notes' => $value['additional_notes']
                );
            }
        }


        $courier_servicablity = $this->load->view(DIR_ADMIN_TEMPLATE . 'sale/order_info_courier_advisory_table.tpl', $data, true);

        echo json_encode($courier_servicablity);
        exit;
    }

    public function tentativeAdvance(){

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
        }

        $data = array();
        $this->load->model('crm/tentativeadvance');
        
       $crm_user_id = (isset($request['crm_user_id']))?$request['crm_user_id']:'';
       $access_token = (isset($request['access_token']))?$request['access_token']:'';
       if($crm_user_id  > 0){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please enter crm_user_id.!';
                       echo json_encode($data); exit;  
       }
       if($access_token !=''){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please enter access_token.!';
                       echo json_encode($data); exit;  
       }

       $validation = $this->model_crm_tentativeadvance->userValidation($crm_user_id, $access_token);
       if( $validation ){

           $staff_id = (isset($request['staff_id']))?$request['staff_id']:'';
           $order_id = (isset($request['order_id']))?$request['order_id']:'';
           $payment_mode = (isset($request['payment_mode']))?$request['payment_mode']:'';
           $cheque_no = (isset($request['cheque_no']))?$request['cheque_no']:'';
           $col_date_valid = (isset($request['col_date_valid']))?$request['col_date_valid']:'';
           $collection_date = (isset($request['collection_date']))?$request['collection_date']:'';
           $deposit_date = (isset($request['deposit_date']))?$request['deposit_date']:'';
           $txn_id = (isset($request['txn_id']))?$request['txn_id']:'';
           $txn_date = (isset($request['txn_date']))?$request['txn_date']:'';
           $amount = (isset($request['amount']))?$request['amount']:'';
           $notes = (isset($request['notes']))?$request['notes']:'';
           $branch_name = (isset($request['branch_name']))?$request['branch_name']:'';
           $bank_deposited = (isset($request['bank_deposited']))?$request['bank_deposited']:'';
           $bank_deposited_image = (isset($request['bank_deposited_image']))?$request['bank_deposited_image']:'';
           // $is_collected = (isset($request['is_collected']))?$request['is_collected']:'';
           // $is_deposited = (isset($request['is_deposited']))?$request['is_deposited']:'';
           // $is_transacted = (isset($request['is_transacted']))?$request['is_transacted']:'';
           $transaction_status = (isset($request['transaction_status']))?$request['transaction_status']:'';
           
            // content load in database using model
            //$staff_namex = $this->model_account_panel_tentativeadvance->getStaff($staff_id);

            if (strlen($payment_mode) > 0 && strlen($transaction_status) > 0)
            {
                if ($payment_mode == "cash")
                {
                    $cheque_no = "";
                    $txn_id = "";
                    $txn_date = "";

                  if ($transaction_status == "will_collect")
                  {
                    $deposit_date = "";
                    if ($col_date_valid == 1)
                    {
                      if (strlen($collection_date) == 0)
                      {
                          $data['status'] = '0';
                          $data['status_text'] = 'error';
                          $data['message'] = 'Please enter Collection Date!';
                          echo json_encode($data); exit;        
                      }
                    }
                    else
                    {
                      $collection_date = "";
                    }
                  }
                  else if ($transaction_status == "will_deposit")
                  {
                      if ($col_date_valid == 1)
                      {
                        if (strlen($collection_date) == 0)
                        {
                            $data['status'] = '0';
                            $data['status_text'] = 'error';
                            $data['message'] = 'Please enter Collection Date!';
                            echo json_encode($data); exit;        
                        }
                      }
                      else
                      {
                        $collection_date = "";
                      }
                      if (strlen($deposit_date) == 0)
                      {
                          $data['status'] = '0';
                          $data['status_text'] = 'error';
                          $data['message'] = 'Please enter Deposit Date!';
                          echo json_encode($data); exit;        
                      }
                  }
                  else if ($transaction_status == "deposited")
                  {
                      if ($col_date_valid == 1)
                      {
                        if (strlen($collection_date) == 0)
                        {
                            $data['status'] = '0';
                            $data['status_text'] = 'error';
                            $data['message'] = 'Please enter Collection Date!';
                            echo json_encode($data); exit;        
                        }
                      }
                      else
                      {
                        $collection_date = "";
                      }
                      if (strlen($deposit_date) == 0)
                      {
                          $data['status'] = '0';
                          $data['status_text'] = 'error';
                          $data['message'] = 'Please enter Deposit Date!';
                          echo json_encode($data); exit;        
                      }
                  }
                  else
                  {
                      $data['status'] = '0';
                      $data['status_text'] = 'error';
                      $data['message'] = 'wrong entry, Please select will collect or will deposit or deposited!';
                      echo json_encode($data); exit;
                  }


                }
                else if ($payment_mode == "cheque")
                {
                    $txn_id = "";
                    $txn_date = "";
                    if ($transaction_status == "will_collect")
                    {
                      $cheque_no = "";
                      $deposit_date = "";
                      if ($col_date_valid == 1)
                      {
                        if (strlen($collection_date) == 0)
                        {
                            $data['status'] = '0';
                            $data['status_text'] = 'error';
                            $data['message'] = 'Please enter Collection Date!';
                            echo json_encode($data); exit;        
                        }
                      }
                      else
                      {
                        $collection_date = "";
                      }
                    }
                    else if ($transaction_status == "will_deposit")
                    {
                        if (strlen($cheque_no) == 0)
                        {
                            $data['status'] = '0';
                            $data['status_text'] = 'error';
                            $data['message'] = 'Please enter cheque no!';
                            echo json_encode($data); exit;
                        }
                        if ($col_date_valid == 1)
                        {
                          if (strlen($collection_date) == 0)
                          {
                              $data['status'] = '0';
                              $data['status_text'] = 'error';
                              $data['message'] = 'Please enter Collection Date!';
                              echo json_encode($data); exit;        
                          }
                        }
                        else
                        {
                          $collection_date = "";
                        }
                        if (strlen($deposit_date) == 0)
                        {
                            $data['status'] = '0';
                            $data['status_text'] = 'error';
                            $data['message'] = 'Please enter Deposit Date!';
                            echo json_encode($data); exit;        
                        }
                    }
                    else if ($transaction_status == "deposited")
                    {
                        if (strlen($cheque_no) == 0)
                        {
                            $data['status'] = '0';
                            $data['status_text'] = 'error';
                            $data['message'] = 'Please enter cheque no!';
                            echo json_encode($data); exit;
                        }
                        if ($col_date_valid == 1)
                        {
                          if (strlen($collection_date) == 0)
                          {
                              $data['status'] = '0';
                              $data['status_text'] = 'error';
                              $data['message'] = 'Please enter Collection Date!';
                              echo json_encode($data); exit;        
                          }
                        }
                        else
                        {
                          $collection_date = "";
                        }
                        if (strlen($deposit_date) == 0)
                        {
                            $data['status'] = '0';
                            $data['status_text'] = 'error';
                            $data['message'] = 'Please enter Deposit Date!';
                            echo json_encode($data); exit;        
                        }
                    }
                    else
                    {
                        $data['status'] = '0';
                        $data['status_text'] = 'error';
                        $data['message'] = 'wrong entry, Please select will collect or will deposit or deposited!';
                        echo json_encode($data); exit;
                    }
                }
                else if ($payment_mode == "neft" || $payment_mode == "payment_link" || $payment_mode == "paytm" || $payment_mode == "upi")
                {
                    $cheque_no = "";
                    $collection_date = "";
                    $deposit_date = "";

                    if ($transaction_status == "transacted")
                    {
                      if (strlen($txn_id) == 0)
                      {
                          $data['status'] = '0';
                          $data['status_text'] = 'error';
                          $data['message'] = 'Please enter txn_id!';
                          echo json_encode($data); exit;
                      }
                      if (strlen($txn_date) == 0)
                      {
                          $data['status'] = '0';
                          $data['status_text'] = 'error';
                          $data['message'] = 'Please enter txn date!';
                          echo json_encode($data); exit;
                      }
                    }
                    else if ($transaction_status == "will_transact")
                    {
                      $txn_id = "";
                      if (strlen($txn_date) == 0)
                      {
                          $data['status'] = '0';
                          $data['status_text'] = 'error';
                          $data['message'] = 'Please enter txn date!';
                          echo json_encode($data); exit;
                      }
                    }
                    else
                    {
                        $data['status'] = '0';
                        $data['status_text'] = 'error';
                        $data['message'] = 'wrong entry, Please select Transacted or will transact!';
                        echo json_encode($data); exit;
                    }
                }
                else if ($payment_mode == "credit")
                {
                    $cheque_no = "";
                    $txn_id = "";
                    $txn_date = "";

                    $collection_date = "";
                    $deposit_date = "";

                    if ($transaction_status != "on_credit")
                    {
                        $data['status'] = '0';
                        $data['status_text'] = 'error';
                        $data['message'] = 'wrong entry, Please select on_credit!';
                        echo json_encode($data); exit;
                    }
                }
                else
                {
                  $data['status'] = '0';
                  $data['status_text'] = 'error';
                  $data['message'] = 'wrong entry, Please select payment mode!';
                  echo json_encode($data); exit;
                }
            }
            else if (strlen($payment_mode) == 0)
            {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Please enter Payment Mode!';
                echo json_encode($data); exit;    
            }
            else if (strlen($transaction_status) == 0)
            {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Please enter Transaction status!';
                echo json_encode($data); exit;    
            }


            
            if ($staff_id == "" || $staff_id == 0 || !is_numeric($staff_id)) {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Staff ID error!';
            }
            else if ($order_id == "" || $order_id == 0 || !is_numeric($order_id)) {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Please enter Order No!';
            }
            else if ($amount == "" || $amount == 0 || !is_numeric($amount)) {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Please enter amount!';
            }
            else
            {
                if (strlen($staff_id) > 0 && is_numeric($staff_id) && strlen($order_id) > 0 && is_numeric($order_id) && strlen($payment_mode) > 0 && strlen($amount) > 0 && is_numeric($amount) && strlen($transaction_status) > 0)
                {
                    $staff_namex = $this->model_crm_tentativeadvance->getStaff($staff_id);
                    $staff_name = $staff_namex["name"].'_'.$staff_namex["staff_id"];
                    $datedCM = date("Y-m-d H:i:s");
                    $staff_array = array(
                                        'staff_id' => $staff_id,
                                        'staff_name' => $staff_name,
                                        'date' => $datedCM
                                        );

                    //$result = $this->model_crm_tentativeadvance->insertTentativeAdvance( $filter_date_from, $order_id, $payment_mode, $cheque_no, $cheque_date, $cheque_to_be_deposited, $txn_id, $amount, $notes, $staff_id, $datedCM, $staff_array );

                    $dated = "";

                    if ($payment_mode == "credit")
                    {
                      $dated = $datedCM;
                    }
                    else
                    {
                      if (strlen($deposit_date) > 0)
                      {
                        $dated = $deposit_date;
                      }
                      else if (strlen($txn_date) > 0)
                      {
                        $dated = $txn_date;
                      }
                      else
                      {
                        $dated = $collection_date; 
                      }
                    }

                    if (strlen($dated) == 0)
                    {
                      $dated = $datedCM;
                    }

                    $result = $this->model_crm_tentativeadvance->insertTentativeAdvance( $order_id, $payment_mode, $cheque_no, $collection_date, $txn_id, $dated, $amount, $notes, $bank_deposited, $bank_deposited_image, $branch_name, $staff_id, $datedCM, $staff_array, $transaction_status );

                    $data['status'] = '1';
                    $data['status_text'] = 'success';
                    $data['message'] = 'Entry has been saved Successfully!';
                    $data['tentative_advance_id'] = $result;
                }
                else
                {
                    $data['status'] = '0';
                    $data['status_text'] = 'Failed';
                    $data['message'] = 'Invalid entry!';
                }
            }

        } else {
            $data['status'] = '0';
            $data['status_text'] = 'Failed';
            $data['message'] = 'User can not access!';
        }

        echo json_encode($data); exit;    
    }

 public function updateTentativeAdvance(){

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
        }

        $data = array();
        $this->load->model('crm/tentativeadvance');
        
       $crm_user_id = (isset($request['crm_user_id']))?$request['crm_user_id']:'';
       $access_token = (isset($request['access_token']))?$request['access_token']:'';
       if($crm_user_id  > 0){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please enter crm_user_id.!';
                       echo json_encode($data); exit;  
       }
       if($access_token !=''){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please enter access_token.!';
                       echo json_encode($data); exit;  
       }

       $validation = $this->model_crm_tentativeadvance->userValidation($crm_user_id, $access_token);
       if( $validation ){            

           $staff_id = (isset($request['staff_id']))?$request['staff_id']:'';
           $tentative_advance_id = (isset($request['tentative_advance_id']))?$request['tentative_advance_id']:'';
           $confirm = (isset($request['confirm']))?$request['confirm']:'';
                  
            if ($staff_id == "" || $staff_id == 0 || !is_numeric($staff_id)) {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Staff ID error!';
            }
            else if ($tentative_advance_id == "" || $tentative_advance_id == 0 || !is_numeric($tentative_advance_id)) {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Tentative Advance ID error!';
            }
            else if ($confirm == "" || $confirm == 0 || !is_numeric($confirm)) {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Please enter status!';
            }
            else
            {
                if (strlen($staff_id) > 0 && is_numeric($staff_id) && strlen($tentative_advance_id) > 0 && is_numeric($tentative_advance_id) && strlen($confirm) > 0 && is_numeric($confirm))
                {
                    $staff_namex = $this->model_crm_tentativeadvance->getStaff($staff_id);
                    $staff_name = $staff_namex["name"].'_'.$staff_namex["staff_id"];
                    $datedCM = date("Y-m-d H:i:s");
                    $staff_array = array(
                                        'staff_id' => $staff_id,
                                        'staff_name' => $staff_name,
                                        'date' => $datedCM
                                        );

                    $result = $this->model_crm_tentativeadvance->updateTentativeAdvanceByID($tentative_advance_id, $confirm, $datedCM, $staff_array);

                    $data['status'] = '1';
                    $data['status_text'] = 'success';
                    $data['message'] = 'Entry has been updated Successfully!';
                    //$data['tentative_advance_id'] = $result;
                }
                else
                {
                    $data['status'] = '0';
                    $data['status_text'] = 'Failed';
                    $data['message'] = 'Invalid entry!';
                }
            }

        } else {
            $data['status'] = '0';
            $data['status_text'] = 'Failed';
            $data['message'] = 'User can not access!';
        }
        echo json_encode($data); exit;    
    }

    public function getListTentativeAdvance(){

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
        }

        $data = array();
        $this->load->model('crm/tentativeadvance');
        
        $crm_user_id = $request['crm_user_id'];
        $access_token = $request['access_token'];

        $validation = $this->model_crm_tentativeadvance->userValidation($crm_user_id, $access_token);

        if( $validation ){
            $results = $this->model_crm_tentativeadvance->getTentativeAdvances();
            $data['results'] = $results;
        }
        else 
        {
            $data['status'] = '0';
            $data['status_text'] = 'Failed';
            $data['message'] = 'User can not access!';
        }
        echo json_encode($data); exit; 
    }

    public function getListTentativeAdvanceDateWise(){

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
        }
        
        $data = array();
        $this->load->model('crm/tentativeadvance');

       $crm_user_id = (isset($request['crm_user_id']))?$request['crm_user_id']:'';
       $access_token = (isset($request['access_token']))?$request['access_token']:'';
       if($crm_user_id  > 0){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please enter crm_user_id.!';
                       echo json_encode($data); exit;  
       }
       if($access_token !=''){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please enter access_token.!';
                       echo json_encode($data); exit;  
       }

        $validation = $this->model_crm_tentativeadvance->userValidation($crm_user_id, $access_token);

        if( $validation ){
            $staff_id = $request['staff_id'];
            $order_id = $request['order_id'];
            $filter_date_from = $request['filter_date_from'];
            $filter_date_to = $request['filter_date_to'];

            $filter_date_from = date("Y-m-d H:i:s", strtotime($filter_date_from));
            $filter_date_to = date("Y-m-d 23:59:59", strtotime($filter_date_to));

            $results = $this->model_crm_tentativeadvance->getTentativeAdvancesDateWise($staff_id, $order_id, $filter_date_from, $filter_date_to);
            $data['results'] = $results;    
        }
        else 
        {
            $data['status'] = '0';
            $data['status_text'] = 'Failed';
            $data['message'] = 'User can not access!';
        }
        echo json_encode($data); exit; 
    }

public function updateBankDeposit(){
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );
        }
        $data = array();
        $this->load->model('crm/tentativeadvance');
       $crm_user_id = (isset($request['crm_user_id']))?$request['crm_user_id']:'';
       $access_token = (isset($request['access_token']))?$request['access_token']:'';
       $bank_deposited_image = (isset($request['bank_slip']))?$request['bank_slip']:'';
       if($bank_deposited_image != ''){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please Upload Image.!';
                       echo json_encode($data); exit;  
       }
       if($crm_user_id  > 0){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please enter crm_user_id.!';
                       echo json_encode($data); exit;  
       }
       if($access_token !=''){
           
       }else{
                       $data['status'] = '0';
                       $data['status_text'] = 'error';
                       $data['message'] = 'Please enter access_token.!';
                       echo json_encode($data); exit;  
       }
        $validation = $this->model_crm_tentativeadvance->userValidation($crm_user_id, $access_token);
        
        if( $validation ){
            $tentative_advance_id = (isset($request['tentative_advance_id']))?$request['tentative_advance_id']:'';
            $bank_deposited = (isset($request['bank_deposited']))?$request['bank_deposited']:'';
            //$bank_deposited_image = (isset($request['bank_deposited_image']))?$request['bank_deposited_image']:'';
            // echo $bank_deposited_image = $_FILES;//die;
            //print_r($bank_deposited_image);die;
            //Image 
//            $today = Date('d_M_Y');
//                if (!file_exists(DIR_UPLOAD.'tentative_advance/'.$today)) {
//                    mkdir(DIR_UPLOAD.'tentative_advance/'.$today, 0777, true);
//                }
//            $file_path = "";
//            // echo "<pre>"; print_r($_FILES); die;
//            if(isset($tem_path) && isset($file_name) && isset($type) && $tem_path !='' && $file_name != '' && $type !=''){
//                $file_name = $file_name;
//                $file_temp_name = $tem_path;
//                $file_path = DIR_UPLOAD.'tentative_advance/'.$today.'/'.$file_name;
//                move_uploaded_file($file_temp_name,$file_path);         
//            }
//            $bank_deposited_image = $file_path;
            //die;
            if ($tentative_advance_id == "" || $tentative_advance_id == 0 || !is_numeric($tentative_advance_id)) {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Tentative Advance ID error!';
                echo json_encode($data); exit;  
            }
            else if ($bank_deposited == "" || !is_numeric($bank_deposited)) {
                $data['status'] = '0';
                $data['status_text'] = 'error';
                $data['message'] = 'Please Select Bank Deposit Or Not!';
                echo json_encode($data); exit;  
            }
            // else if ($bank_deposited_image == "") {
            //     $data['status'] = '0';
            //     $data['status_text'] = 'error';
            //     $data['message'] = 'Please Insert Image!';
            // }
            else
            {
                if (strlen($tentative_advance_id) > 0 && is_numeric($tentative_advance_id) && strlen($bank_deposited) > 0 && is_numeric($bank_deposited))
                {
                    if ($bank_deposited == 0)
                    {
                        $bank_deposited_image = "";
                        //$is_deposited = 0;
                        //$this->model_crm_tentativeadvance->updateTentativeAdvanceForBankDepositedById( $bank_deposited, $bank_deposited_image, $is_deposited, $tentative_advance_id );
                        $this->model_crm_tentativeadvance->updateTentativeAdvanceForBankDepositedById( $bank_deposited, $bank_deposited_image, $tentative_advance_id );
                            $data['status'] = '1';
                            $data['status_text'] = 'success';
                            $data['message'] = 'Entry has been updated Successfully!';
                    }
                    else if ($bank_deposited == 1)
                    {
                        if ($bank_deposited_image == "") {
                            $data['status'] = '0';
                            $data['status_text'] = 'error';
                            $data['message'] = 'Please Insert Image!';
                        }
                        else
                        {
                            //$is_deposited = 1;
                            //$this->model_crm_tentativeadvance->updateTentativeAdvanceForBankDepositedById( $bank_deposited, $bank_deposited_image, $is_deposited, $tentative_advance_id );
                          $this->model_crm_tentativeadvance->updateTentativeAdvanceForBankDepositedById( $bank_deposited, $bank_deposited_image, $tentative_advance_id );
                            $data['status'] = '1';
                            $data['status_text'] = 'success';
                            $data['message'] = 'Entry has been updated Successfully!';
                        }
                    }
                }
                else
                {
                    $data['status'] = '0';
                    $data['status_text'] = 'Failed';
                    $data['message'] = 'Invalid entry!';
                }
            }
        } else {
            $data['status'] = '0';
            $data['status_text'] = 'Failed';
            $data['message'] = 'User can not access!';
        }
        echo json_encode($data); exit;    
    }

    /**
     * Public method which gives membership details for given customer ids 
     *    If these customer ids belongs to multiple master_ids, throw alert
     * @param:  String $customer_ids , Comma separate ids
     * @return: Array $data
     * @author: Nishu, Sept 2018
    */
    public function getCustomerMembershipInfo(){
      
      $response = array();
    
      //Check No data passed in get parameters
      if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        
        //Get post data from Post
        $inputJSON = file_get_contents('php://input');
        $requset   = json_decode( $inputJSON, TRUE );
       
        //Decode Data recieved from post param
        $data = decodeApiData($requset['data']);
      
        //Check for required data keys
        if(
          !empty($data['customer_ids'])
          && $data['access_key'] == WEB_API_ACCESS_KEY
        ){
          
          $obj = new Membership($this);
          $customer_ids = $data['customer_ids'];
          $result = $obj->getCustomerMembershipInfo($customer_ids );

          $response['status']     = 1;
          $response['message']    = "Data Successfully Found.";

        }else{
          $response['status']     = 0;
          $response['message']    = "Data Not Found.";
        }
      }else{
        $response['status']     = 0;
        $response['message']    = "Required Data Missing.";
      }

      //Set data for Response 
      $response['data']          = $result;

      echo json_encode($response);die;
    }//End of getCustomerMembershipInfo

    /**
     * Public method to get WSB_credit balance details for given customer ids 
     * @param:  String $customer_ids , Comma separate ids
     * @return: Array $data
     * @author: Nishu, Feb 2019
    */
    public function getCustomersWsbCreditBal(){
      
      $response = array();
    
      //Check No data passed in get parameters
      if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        
        //Get post data from Post
        $inputJSON = file_get_contents('php://input');
        $requset   = json_decode( $inputJSON, TRUE );
       
        //Decode Data recieved from post param
        $data = decodeApiData($requset['data']);
      
        //Check for required data keys
        if(
          !empty($data['customer_ids'])
          && $data['access_key'] == WEB_API_ACCESS_KEY
        ){
          
          $obj = new WsbCreditPayment($this);
        
          if(!is_array($data['customer_ids'])){
            $customer_ids = explode(',', $data['customer_ids']);
          }else{
            $customer_ids = $data['customer_ids'];
          }

          foreach ($customer_ids as $customer_id) {
            $result[$customer_id] = $obj->getAvaiableCreditBalance($customer_id);
          }

          $response['status']     = 1;
          $response['message']    = "Data Successfully Found.";

        }else{
          $response['status']     = 0;
          $response['message']    = "Data Not Found.";
        }
      }else{
        $response['status']     = 0;
        $response['message']    = "Required Data Missing.";
      }

      //Set data for Response 
      $response['data']          = $result;

      echo json_encode($response);die;
    }//End of getCustomerMembershipInfo


}
