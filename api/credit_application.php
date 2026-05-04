<?php
     require_once(__DIR__.'/system.php');
     require_once(DIR_SYSTEM.'library/solr/lookup.php');
     require_once(DIR_CATALOG.'form/register_form.php');
     require_once(DIR_SYSTEM.'library/customer.php');
     require_once( DIR_SYSTEM . 'library/operations/payment_gateway/wsb_credit.php' );

    class Credit_applicationController extends SystemController
    {
        private $error = "";

        public function __construct($params) {

            parent::__construct($params);

            $inputJSON = file_get_contents('php://input');
            $app_request_data = json_decode($inputJSON, TRUE);
            if ( ( !empty( $app_request_data['email'] ) 
                    && ( in_array( $app_request_data['email'], array( 'test58@gmail.com', '3232323232' ))))
                 || ( !empty( $app_request_data['mobile'] ) 
                    && ( in_array( $app_request_data['mobile'], array( 'test58@gmail.com', '3232323232' )))) ) {
                $this->log_app_test_data( $app_request_data );
            }

        }


      public function log_app_test_data( $app_request_data )
      {
          $app_test_data = "Date of Request: ". date("d/m/Y H:i:s") ."\n";
          $app_test_data .= "Request URL: ". $_SERVER['REQUEST_URI'] ."\n";
          $app_test_data .= "Request Data: " . json_encode( $app_request_data ) . "\n\n\n";

          $file = fopen( DIR_LOGS . "app_google_test_logs.txt", "a" );
          fwrite( $file, $app_test_data );
          fclose( $file );
      }

      public function initialCreditApp(){
        $this->load->model('account/credit_application');
        $this->load->model('webengage/webengage');
        //add default customer data in credit application table
        $inputJSON = file_get_contents('php://input');
        $request = json_decode($inputJSON, TRUE);
        $check_access_token=$this->validateAccessTokenForUser($request);
        if($check_access_token['status']=='1' || (!empty($request['web_access']) && $request['web_access']=='1')){
          $customer_id=$request['user_id'];
        $get_customer_credit_application_id=$this->model_account_credit_application->isExitCustomerCreditApplicationVersion($customer_id);
                     if(empty($get_customer_credit_application_id['status']))
                     {
                       $addDefaultDataInCreditApplication = $this->model_account_credit_application->addDefaultDataInCreditApplication($customer_id,'3');
                       if(!empty($addDefaultDataInCreditApplication)){
                        $this->model_webengage_webengage->updateCreditApplicationStatusOnWebengage($customer_id,'0');
                        $this->model_account_credit_application->saveDeviceDetail($request,$addDefaultDataInCreditApplication);
                       }
                       $credit_application_detail = $this->model_account_credit_application->getCustomerCreditApplication($customer_id);
                     }else{
                      $credit_application_detail = $this->model_account_credit_application->getCustomerCreditApplication($customer_id);
                     }
                     $requiredResponse=$this->requiredResponse($credit_application_detail);
                     $requiredResponseWithFilePath=$this->requiredResponseWithFilePath($requiredResponse);
                     $requiredResponseWithFilePath['document_count']['value'] = $this->model_account_credit_application->getDocumentCountByFormId($credit_application_detail['id']);
                     $rt['data'] = $requiredResponseWithFilePath;
                     $rt['status'] = '1';
                     $rt['status_text'] = 'Success';
                     $rt['message'] = 'Success';
                    }else{
                      $rt=$check_access_token;
                   }
          
      echo json_encode($rt); exit;

      }

      public function initialCreditAppUsingFormId(){
        $this->load->model('account/credit_application');
        //add default customer data in credit application table
        $inputJSON = file_get_contents('php://input');
        $request = json_decode($inputJSON, TRUE);
          $customer_application_id=$request['customer_application_id'];
          $customer_id='0';
        $get_customer_credit_application_id=$this->model_account_credit_application->getCustomerCreditApplication($customer_id,$customer_application_id);
                     if(empty($get_customer_credit_application_id['status']))
                     {
                       $addDefaultDataInCreditApplication = $this->model_account_credit_application->addDefaultDataInCreditApplication($customer_id,'3');
                       $credit_application_detail = $this->model_account_credit_application->getCustomerCreditApplication($customer_id,$customer_application_id);
                     }else{
                      $credit_application_detail = $this->model_account_credit_application->getCustomerCreditApplication($customer_id,$customer_application_id);
                     }
                     $requiredResponse=$this->requiredResponse($credit_application_detail);
                     $requiredResponseWithFilePath=$this->requiredResponseWithFilePath($requiredResponse);
                     $rt['data'] = $requiredResponseWithFilePath;
                     $rt['status'] = '1';
                     $rt['status_text'] = 'Success';
                     $rt['message'] = 'Success';
          
      echo json_encode($rt); exit;

      }

      

      private function requiredResponseWithFilePath($request){
        $this->load->model('tool/image');
        foreach ($request as $key => $value) {
                      if ($key == 'address_proof_document' || $key == 'pancard' || $key == 'aadhaar_card' || $key == 'six_months_bank_statement' || $key == 'shop_photo' || $key == 'selfie_with_shop'  || $key == 'visting_card_photo') {
                        if(!empty($value['images'])){
                          $image_full_path=array();
                          foreach ($value['images'] as $key2 => $image_path) {

                            $ext = pathinfo($image_path);
                                        if(!empty($ext['extension']) && strtolower($ext['extension'])=='pdf'){
                                            $image_full_path[$key2] = STATIC_CONTENT_URL_SSL.$image_path;
                                        }else{
                                            $image_full_path[$key2] = $this->model_tool_image->resize($image_path,'200','1000');
                                        }
                            //$image_full_path[$key2]=$this->model_tool_image->getOriginalImage($image_path);
                          }
                          $request[$key]['images']=array_values($image_full_path);
                        }
                      }
                     }
                     return $request;
      }

  /**
   * Function : validateAccessTokenForUser
   * Method to validate Access Token For User
   * Request Parameters : User ID, access token
   * Type : Post
   * @author Rahul
   * Output : access to ken is valid or not
   * */
  public function validateAccessTokenForUser($request){
    $this->load->model('restapi/service');
      if (!empty($request['user_id']) && !empty($request['access_token']))
        {
        $validate_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$request['access_token'], (int)$request['user_id']);
        if ($validate_access_token == '1')
          {
          $return['status'] = '1';
          $return['status_text'] = 'Success';
          }
          else
          {
          $return['error_code'] = '1003';
          $return['status'] = '0';
          $return['status_text'] = 'Failed';
          $return['message'] = 'Invalid access token or user id.';
          }
        }
        else
        {
        $return['error_code'] = '1001';
        $return['status'] = '0';
        $return['status_text'] = 'Failed';
        $return['message'] = 'http request does not have user id or access token.';
        }

      return $return;
  }



  /**
   * Function : saveBasicInfo
   * Method to save or update credit application form
   * Request Parameters : credit application form detail
   * Type : Post
   * @author Rahul
   * Output : true or false
   * */
  public function saveBasicInfo(){
    $this->load->model('account/credit_application');
    $this->load->model('webengage/webengage');
          //$request=$this->request;
          $inputJSON = file_get_contents('php://input');
          $request = json_decode($inputJSON, TRUE);
          $check_access_token=$this->validateAccessTokenForUser($request);
            if($check_access_token['status']=='1' || (!empty($request['web_access']) && $request['web_access']=='1')){
              $request['customer_id']=$request['user_id'];
              $request['version']='3';

              //get city state vai API
              if(!empty($request['current_pincode'])){
                $response_current_pincode=$this->pincodeAddress($request['current_pincode']);
                if (!empty($response_current_pincode->statusCode) && $response_current_pincode->statusCode=='200') {
                    $request['current_city']=$response_current_pincode->data['city'];
                    $request['current_state']=$response_current_pincode->data['state'];
                }
              }
              

              if(!empty($request['permanent_pincode'])){
                $response_permanent_pincode=$this->pincodeAddress($request['permanent_pincode']);
                if (!empty($response_permanent_pincode->statusCode) && $response_permanent_pincode->statusCode=='200') {
                    $request['permanent_city']=$response_permanent_pincode->data['city'];
                    $request['permanent_state']=$response_permanent_pincode->data['state'];
                }
              }


              $validation_status=$this->validateForm($request);
              if(empty($validation_status) || (!empty($request['web_access']) && $request['web_access']=='1')){
              	$credit_application_status = $this->model_account_credit_application->getCreditApplicationDocumentStatus($request['credit_application_id']);
              	if (empty($request['khufiya_user_id']) && !empty($credit_application_status)){
                      $rt['error_code'] = '1006';
                      $rt['status'] = '0';
                      $rt['status_text'] = 'Failed';
                      $rt['message'] = "Application not update once credit application is under process.";
                    }else{
                		//for phone
		                $phoneAlredyExist = $this->phoneOrEmailValidate($request['phone_no'],'phone',$request['credit_application_id']);
		                if($phoneAlredyExist){
		                  //first form save
		                  $add_webengage='0';
		                  if(isset($request['credit_application_form_status']) && $request['credit_application_form_status'] < '1'){
		                      $request['draft']= '1';
		                      $add_webengage='1';
		                      }
		                  $saveDate = $this->model_account_credit_application->saveShortCredit($request);
		                  if($saveDate){
		                    //====webengage start===//
		                    if(!empty($request['customer_id']) && !empty($add_webengage)){
		                      $this->model_webengage_webengage->updateCreditApplicationStatusOnWebengage($request['customer_id'],'1');
		                    }
		                    //====webengage end===//
		                     $rt['status'] = '1';
		                     $credit_application_activation_status=$this->model_account_credit_application->creditApplicationStatusForBanner($request['customer_id']);

		                        $wsb_credit_payment = new WsbCreditPayment($this);
		                        $customer_id=array($request['customer_id']);
		                        $limit_data = $wsb_credit_payment->getActiveWsbCreditLimitByCustomerIds($customer_id);
		                        

		                          if(!empty($limit_data[$request['customer_id']])){
		                            $limit=$limit_data[$request['customer_id']];
		                          }else{
		                            $limit='0';
		                          }
		                            if(!empty($credit_application_activation_status['gst_number'])){
		                              $pre_approved_limit='25000';
		                            }else{
		                              $pre_approved_limit='10000';
		                            }

		                            if(!empty($request['web_access'])=='1'){
		                              $request['success_type']='1';
		                              $getQueryStringForWeb=$this->getQueryStringForWeb($request);
		                              if(isset($request['khufiya_user_id']) && $request['khufiya_user_id'] != ''){
		                                $rt['redirect_action'] = $this->url->link('account/credit_application'.$getQueryStringForWeb, '', 'SSL');
		                              }else{
		                                $rt['redirect_action'] = $this->url->link('account/credit_application/success'.$getQueryStringForWeb, '', 'SSL');
		                              }

		                              

		                            }

		                     $rt['approval_message'] = 'You have been pre-approved for credit of Rs. '.$pre_approved_limit.'/- @ 0% interest for 30 days. For activation and higher limit, please complete step 2 and upload all your KYC documents';

		                    $rt["courier_address"] = "Credit Department\nWholesalebox, B-1, Crystal Mall\nBanipark, Jaipur-302016\nPh-+918239778680";

		                    $rt["documents_to_be_couriered"] = array("Agreement","NACH Form","PDC","Self Attested PAN Copy","Self Attested ID Proof");

		                    $rt["kyc_in_progress_message"] = "Thank you for uploading your documents. Kindly print out the agreement sent on your email and courier the documents back to us. Once received we will update you on activation of credit limit. For any queries contact us on +91-8239778680";
		                     $rt['status_text'] = 'Success';
		                     $rt['message'] = 'Success';
		                  }else{
		                    $rt['error_code'] = '1007';
		                    $rt['status'] = '0';
		                    $rt['status_text'] = 'Failed';
		                    $rt['message'] = 'Credit application not save or update';
		                  }
		                }else{
		                  $rt['error_code'] = '1006';
		                  $rt['status'] = '0';
		                  $rt['status_text'] = 'Failed';
		                  $rt['message'] = 'Phone Number Already Used.';
		                }
		            }

              }else{
                  $rt['validation_error'] = $validation_status;
                  $rt['error_code'] = '1004';
                  $rt['status'] = '0';
                  $rt['status_text'] = 'Failed';
                  $rt['message'] = 'validation error';
              }
                //echo '<pre>'; print_r($validation_status); exit;
              }else{
                      $rt=$check_access_token;
                   }
      echo json_encode($rt); exit;

  }
  public function saveKycDocument(){
    $this->load->model('account/credit_application');
    $this->load->model('webengage/webengage');
    $this->load->model('lead/lead');
     //$request=$this->request;
        $inputJSON = file_get_contents('php://input');
        $request = json_decode($inputJSON, TRUE);
          $check_access_token=$this->validateAccessTokenForUser($request);
            if($check_access_token['status']=='1' || (!empty($request['web_access']) && $request['web_access']=='1')){
              $request['customer_id']=$request['user_id'];
              $request['version']='3';
              $validation_status=$this->validateForm($request);
              if(empty($validation_status) || (!empty($request['web_access']) && $request['web_access']=='1')){
                //for email
                $request['email']=$request['customer_email'];

                $credit_application_status = $this->model_account_credit_application->getCreditApplicationDocumentStatus($request['credit_application_id']);
                if(empty($request['khufiya_user_id']) && !empty($credit_application_status)){
	                  $rt['error_code'] = '1006';
	                  $rt['status'] = '0';
	                  $rt['status_text'] = 'Failed';
	                  $rt['message'] = "Application not update once credit application is under process.";
	              }else{
		                $emailAlredyExist = $this->phoneOrEmailValidate($request['email'],'email',$request['credit_application_id']);
		                if($emailAlredyExist){
		                  //Kyc  form save
		                  $add_webengage='0';
		                  if(isset($request['credit_application_form_status']) && $request['credit_application_form_status'] < '2'){
		                    $request['draft']= '2';
		                    $add_webengage='1';
		                  }

		                  $saveDate = $this->model_account_credit_application->saveShortCredit($request,$request['credit_application_id']);
		                  if($saveDate){

		                    //========push notification to agent===========//
		                  if(isset($request['khufiya_user_id']) && $request['khufiya_user_id'] != ''){
		                    $curent_document_array=$old_document_array=array();
		                      $curent_document=$this->model_account_credit_application->getDocumentCountByFormId($request['credit_application_id']);
		                      if(!empty($curent_document)){
		                        $curent_document_array=explode(',',$curent_document);
		                      }
		                      if(!empty($request['document_count'])){
		                        $old_document_array=explode(',',$request['document_count']);
		                      }
		                      //=======git diff=======//
		                      $upload_diffrent_image=array_diff($curent_document_array, $old_document_array);
		                      if(!empty($upload_diffrent_image) && !empty($request['customer_id'])){
		                              $customer_id=$request['customer_id'];
		                               $customer_name=$this->model_account_credit_application->getCreditApplicationCustomerName($customer_id);
		                                    if(!empty(trim($customer_name))){
		                                        $notification_name=$customer_name;
		                                      }else{
		                                        $notification_name='Customer';
		                                      }
		                              $lead_detail=$this->model_lead_lead->getLeadIdUsingCustomerInCrm($customer_id);
		                                    if(!empty($lead_detail['lead_id']) && !empty($lead_detail['user_id'])){
		                                        $lead_id=$lead_detail['lead_id'];
		                                        $lead_user_id=$lead_detail['user_id'];
		                                        $message='Document upload on credit form';
		                                        $title=$notification_name.' upload document on credit form';
		                                        $action=CRM_URL.'leads/view/'.$lead_id;
		                                        $this->model_account_credit_application->sendPushNotificationAgent($customer_id,$lead_id,$lead_user_id,$action,$message,$title);
		                                    }

		                      } 
		                  }

		                    //====webengage start===//
		                    if(!empty($request['customer_id']) && !empty($add_webengage)){
		                     $this->model_webengage_webengage->updateCreditApplicationStatusOnWebengage($request['customer_id'],'2');

		                    }
		                    //====webengage end===//


		                     $rt['status'] = '1';
		                     $rt['status_text'] = 'Success';
		                     $rt['message'] = 'Success';
		                     $rt['kyc_in_progress_image']='';
		                    $credit_application_activation_status=$this->model_account_credit_application->creditApplicationStatusForBanner($request['customer_id']);

		                    $wsb_credit_payment = new WsbCreditPayment($this);
		                    $customer_id=array($request['customer_id']);
		                    $limit_data = $wsb_credit_payment->getActiveWsbCreditLimitByCustomerIds($customer_id);
		                    if(!empty($limit_data[$request['customer_id']])){
		                      $limit=$limit_data[$request['customer_id']];
		                    }else{
		                      $limit='0';
		                    }

		                    if(!empty($credit_application_activation_status['message'])){
		                      $approval_message=$credit_application_activation_status['message'];
		                    }else{
		                      $approval_message="Your credit limit is now active for Rs. ".$limit."/-. Please use the “Order on WholesaleBox credit” payment method at the time of order placement.\n\nAvailable limit: Rs. ".$limit."/-";
		                    }
		                    if(!empty($credit_application_activation_status['gst_number'])){
		                        $pre_approved_limit='25000';
		                      }else{
		                        $pre_approved_limit='10000';
		                      }
		                        

		                     $rt["activation_message"] = $approval_message;

		                     $rt["courier_address"] = "Credit Department\nWholesalebox, B-1, Crystal Mall\nBanipark, Jaipur-302016\nPh-+918239778680";

		                    $rt["documents_to_be_couriered"] = array("Agreement","NACH Form","PDC","Self Attested PAN Copy","Self Attested ID Proof");

		                    $rt["kyc_in_progress_message"] = "Thank you for uploading your documents. Kindly print out the agreement sent on your email and courier the documents back to us. Once received we will update you on activation of credit limit. For any queries contact us on +91-8239778680";
		                    if(!empty($request['web_access'])=='1'){
		                              $request['success_type']='2';
		                              $getQueryStringForWeb=$this->getQueryStringForWeb($request);

		                              if(isset($request['khufiya_user_id']) && $request['khufiya_user_id'] != ''){
		                                $rt['redirect_action'] = $this->url->link('account/credit_application'.$getQueryStringForWeb, '', 'SSL');
		                              }else{
		                                $rt['redirect_action'] = $this->url->link('account/credit_application/success'.$getQueryStringForWeb, '', 'SSL');
		                              }


		                            }
		                  }else{
		                    $rt['error_code'] = '1007';
		                    $rt['status'] = '0';
		                    $rt['status_text'] = 'Failed';
		                    $rt['message'] = 'Credit application not save or update';
		                  }
		                }else{
		                  $rt['error_code'] = '1006';
		                  $rt['status'] = '0';
		                  $rt['status_text'] = 'Failed';
		                  $rt['message'] = 'Email address Already Used.';
		                }
		          }
              }else{
                  $rt['validation_error'] = $validation_status;
                  $rt['error_code'] = '1004';
                  $rt['status'] = '0';
                  $rt['status_text'] = 'Failed';
                  $rt['message'] = 'validation error';
              }
                //echo '<pre>'; print_r($validation_status); exit;
              }else{
                      $rt=$check_access_token;
                   }
      echo json_encode($rt); exit;

  }
    /**
     * phoneValidate
     * @author Rahul Singh
     * @description Check phone number allready register or not 
     * @return boolen true/false
     */
    public function phoneOrEmailValidate($value,$type,$credit_application_id)
    {
       $this->load->model('account/credit_application');

       if($credit_application_id)
       {
         $resonse = $this->model_account_credit_application->CheckPhoneOrEmailIsRegisterInEdit($type, $value, $credit_application_id);
       }
       else
       {
         $resonse = $this->model_account_credit_application->CheckPhoneOrEmailIsRegister($type, $value);
       }

       if($resonse > 0)
       {
        $result = '0';
       }
       else
       {
        $result = '1';
       }
       return $result;
    }

    private function validateForm(){
      $validation_error=array();
      $inputJSON = file_get_contents('php://input');
      $this->request = json_decode($inputJSON, TRUE);
      if(isset($this->request['email'])){
        unset($this->request['email']);
      }
      if(!empty($this->request['customer_email'])){
        $this->request['email']=$this->request['customer_email'];
      }
        //First name
        if (isset($this->request['first_name']) && !empty(utf8_strlen(trim($this->request['first_name']))) && utf8_strlen(trim($this->request['first_name'])) < 3){
            $validation_error['first_name'] = 'First Name must be minimum 3 characters!'; 
        }elseif(isset($this->request['first_name']) && empty(utf8_strlen(trim($this->request['first_name'])))){
            $validation_error['first_name'] = 'Please Fill First Name'; 
        }

       // Last name
        if (isset($this->request['last_name']) && !empty(utf8_strlen(trim($this->request['last_name']))) && utf8_strlen(trim($this->request['last_name'])) < 3){
            $validation_error['last_name'] = 'Last Name must be minimum 3 characters!'; 
        }elseif(isset($this->request['last_name']) && empty(utf8_strlen(trim($this->request['last_name'])))){
            $validation_error['last_name'] = 'Please Fill Last Name'; 
        }

        // Father name
        if (isset($this->request['father_name']) && !empty(utf8_strlen(trim($this->request['father_name']))) && utf8_strlen(trim($this->request['father_name'])) < 3){
            $validation_error['father_name'] = 'Father Name must be minimum 3 characters!'; 
        }elseif(isset($this->request['father_name']) && empty(utf8_strlen(trim($this->request['father_name'])))){
            $validation_error['father_name'] = 'Please Fill Father Name'; 
        }

        //Email Address
        if (!empty($this->request['email']) && (utf8_strlen(trim($this->request['email'])) < 3 || !preg_match('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',trim($this->request['email'])))){
                    $validation_error['email'] = 'Please enter Valid Email';  
        }elseif(isset($this->request['email']) && empty(utf8_strlen(trim($this->request['email'])))){
            $validation_error['email'] = 'Please Fill Email Address'; 
        }

        //Phone Number
        if (isset($this->request['phone_no']) && (utf8_strlen(trim($this->request['phone_no'])) != 10 || (!preg_match('/^[1-9][0-9]*$/',trim($this->request['phone_no']))))){
                    $validation_error['phone_no'] = 'Phone No must be 10 digit Number';  
        }elseif(isset($this->request['phone_no']) && empty(utf8_strlen(trim($this->request['phone_no'])))){
                    $validation_error['phone_no'] = 'Please Fill Phone Number'; 
        }

        //Date of Birth
        if(isset($this->request['dob']) && empty(utf8_strlen(trim($this->request['dob'])))){
                    $validation_error['dob'] = 'Please Fill Date of Birth'; 
        }

        //Company Name
        if(isset($this->request['company_name']) && empty(utf8_strlen(trim($this->request['company_name'])))){
                    $validation_error['company_name'] = 'Please Fill Company Name'; 
        }

        //Current Pincode
        if (isset($this->request['current_pincode']) && (utf8_strlen(trim($this->request['current_pincode'])) != 6 || !preg_match('/^[1-9][0-9]*$/',trim($this->request['current_pincode'])))){
            $validation_error['current_pincode'] = 'Pincode must be 6 digit number';  
        }
        if(isset($this->request['current_pincode']) && empty(utf8_strlen(trim($this->request['current_pincode'])))){
                    $validation_error['current_pincode'] = 'Please Fill Pincode'; 
        }

        //permanent Pincode
        if (isset($this->request['permanent_pincode']) && (utf8_strlen(trim($this->request['permanent_pincode'])) != 6 || !preg_match('/^[1-9][0-9]*$/',trim($this->request['permanent_pincode'])))){
            $validation_error['permanent_pincode'] = 'Pincode must be 6 digit number';  
        }
        if(isset($this->request['permanent_pincode']) && empty(utf8_strlen(trim($this->request['permanent_pincode'])))){
                    $validation_error['permanent_pincode'] = 'Please Fill Diffrent Pincode'; 
        }

        //Gst Number
        if(isset($this->request['gst_number']) && empty(utf8_strlen(trim($this->request['gst_number'])))){
                    $validation_error['gst_number'] = 'Please Fill Gst Number'; 
        }
        
        //Gender
        if(isset($this->request['gender']) && empty(utf8_strlen(trim($this->request['gender'])))){
                    $validation_error['gender'] = 'Please Fill Gender'; 
        }
        

        //Business Start Year
        if(isset($this->request['business_start_year']) && empty(utf8_strlen(trim($this->request['business_start_year'])))){
                    $validation_error['business_start_year'] = 'Please Select Business Start Year'; 
        }elseif (isset($this->request['business_start_year']) && (utf8_strlen(trim($this->request['business_start_year'])) != 4 || !preg_match('/^[0-9]*$/',trim($this->request['business_start_year'])))){
            $validation_error['business_start_year'] = 'Please Select valid Business Start Year'; 
        }

        //Pan Number
        if(isset($this->request['pan_no']) && empty(utf8_strlen(trim($this->request['pan_no'])))){
                    $validation_error['pan_no'] = 'Please Fill Pan Number'; 
        }
        elseif(isset($this->request['pan_no']) && !preg_match("/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/", trim($this->request['pan_no'])) ) {
            $validation_error['pan_no'] = 'Invalid pan number';  
        }


        //Aadhaar Number
        if(isset($this->request['aadhaar_no']) && empty(utf8_strlen(trim($this->request['aadhaar_no'])))){
                    $validation_error['aadhaar_no'] = 'Please Fill Aadhaar Number'; 
        }
        elseif(isset($this->request['aadhaar_no']) && (utf8_strlen(trim($this->request->post['aadhaar_no'])) != 12 || !preg_match('/^[1-9][0-9]*$/',trim($this->request->post['aadhaar_no'])))) {
            $validation_error['aadhaar_no'] = 'Invalid Aadhaar Number';  
        }

        

        //Address Proof
        if(isset($this->request['address_proof_document']) && empty(count(array_filter($this->request['address_proof_document'])))){
                    $validation_error['address_proof_document'] = 'Please Upload Address Proof Document'; 
        }

        // six months bank statement
        if(isset($this->request['six_months_bank_statement']) && empty(count(array_filter($this->request['six_months_bank_statement'])))){
                    $validation_error['six_months_bank_statement'] = 'Please Upload six months bank statement'; 
        }

        // Shop Address
        if(isset($this->request['current_address']) && empty(utf8_strlen(trim($this->request['current_address'])))){
                    $validation_error['current_address'] = 'Please Fill Shop Address'; 
        }

        // Home Address
        if(isset($this->request['permanent_address']) && empty(utf8_strlen(trim($this->request['permanent_address'])))){
                    $validation_error['permanent_address'] = 'Please Fill Home Address'; 
        }

        //diffrent_address
        if(isset($this->request['diffrent_address']) && empty(utf8_strlen(trim($this->request['diffrent_address'])))){
          
        if(isset($this->request['permanent_city']) && empty(utf8_strlen(trim($this->request['permanent_city'])))){
                    $validation_error['permanent_city'] = 'Please Fill Diffrent City'; 
        }
        if(isset($this->request['permanent_state']) && empty(utf8_strlen(trim($this->request['permanent_state'])))){
                    $validation_error['permanent_state'] = 'Please Fill Diffrent State'; 
        }
        }
        return $validation_error;

    }

    public function validateFile($file_name,$file){
          $errors     = array();
          $maxsize    = 8388608;
          $acceptable = array(
              'image/jpeg',
              'image/jpg',
              'image/png'
          );
          $acceptable_bank_document = array(
              'application/pdf',
              'image/jpeg',
              'image/jpg',
              'image/png'
          );
          if(($file["size"] >= $maxsize) || ($file["size"] == 0)) {
              $errors[] = 'File too large. File must be less than 2 megabytes.';
          }
          if($file_name=='six_months_bank_statement'){
              if(!in_array($file['type'], $acceptable_bank_document) && (!empty($file["type"]))) {
                  $errors[] = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
              }
          }else{
              if(!in_array($file['type'], $acceptable) && (!empty($file["type"]))) {
                  $errors[] = 'Invalid file type. Only JPG, GIF and PNG types are accepted.';
              }

          }
          

      if(count($errors) === 0) {
         return '0';
      }else{
        return $errors;
      }
    }

        /**
     * uploadDocument
     * @author Rahul Singh
     * @description upload document vai ajax 
     * @return status and file path
     * Date 4 Jan 2019
     */
    public function uploadDocument() {
        $this->load->model('tool/image');
        $this->load->model('account/credit_application'); 
        $this->load->model('lead/lead');
         //create file array
            $file_data = array();
            if(count($_FILES) > 0){
                foreach($_FILES as $key => $val){
                    $file_data[$key] = $this->reArrayFiles($val);

                }
            }
          $request=$this->request;
          $check_access_token=$this->validateAccessTokenForUser($request);
        if($check_access_token['status']=='1'){
            //upload document 
            $request['document_type']='application_document'; 
            if(count($file_data) > 0){
                
                if(SITE_ENVIRONMENT == 'Production'){
                    $customer_folder_name = $this->request['user_id'];
                }else{
                    $customer_folder_name = 'staging_' .$this->request['user_id'];  
                }

                if($this->request['user_id'] == '0')
                 {
                    $customer_folder_name = $customer_folder_name.'_'.$this->request['credit_application_id'];
                 }

                $uploads_dir = 'credit_application/'.$customer_folder_name.'/application_document/';
                foreach($file_data as $document_name => $files){ 
                    foreach($files as $k => $file){
                      $response = $this->validateFile($document_name,$file);
                      if(empty($response)){
                        if(!empty($file['tmp_name'])){
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $file_name = $document_name.'_'. time() . '.'.$extension;
                        $result = $this->uploadImages($uploads_dir,$file_name,$file['tmp_name']);
                        if($result){
                           $file_data[$document_name][$k]['name'] = $file_name; 
                           $request['customer_id']= $this->request['user_id'];
                           if(!empty($this->request['credit_application_id'])){
                            $request['credit_application_id']= $this->request['credit_application_id'];
                           }
                           
                           $request['draft']= '2'; 
                            $type='ajax';
                            $result_resonse = $this->model_account_credit_application->saveCreditDocument($file_data,$request,3,$type);
                            if(!empty($result_resonse['last_image_id'])){
                                $rt['image_id'] = $result_resonse['last_image_id'];
                                $ext = pathinfo($result_resonse['file_path']);
                                        if(!empty($ext['extension']) && strtolower($ext['extension'])=='pdf'){
                                            $rt['file_path'] = STATIC_CONTENT_URL_SSL.$result_resonse['file_path'];
                                        }else{
                                            $rt['file_path'] = $this->model_tool_image->resize($result_resonse['file_path'],'200','1000');
                                        }
                                
                                $rt['status'] = '1';
                                $rt['status_text'] = 'Success';
                                $rt['message'] = 'Success';
                                if(!empty($this->request['user_id']) && !empty($rt['file_path'])){
                                    $file_path['0']=$rt['file_path'];
                                    $customer_id=$this->request['user_id'];
                                    $lead_detail=$this->model_lead_lead->getLeadIdUsingCustomerInCrm($customer_id);
                                    $customer_name=$this->model_account_credit_application->getCreditApplicationCustomerName($customer_id);
                                      if(!empty(trim($customer_name))){
                                        $notification_name=$customer_name;
                                      }else{
                                        $notification_name='Customer';
                                      }
                                    if(!empty($lead_detail['lead_id']) && !empty($lead_detail['user_id'])){
                                        $lead_id=$lead_detail['lead_id'];
                                        $lead_user_id=$lead_detail['user_id'];
                                        $message='Document upload on credit form';
                                        $title=$notification_name.' upload document on credit form';
                                        $action=CRM_URL.'leads/view/'.$lead_id;
                                        $this->model_account_credit_application->sendPushNotificationAgent($customer_id,$lead_id,$lead_user_id,$action,$message,$title);
                                    }
                                }
                                $res=json_encode($rt);
                                echo $res; exit;
                            }else{
                                exit;
                            }
                        }
                        }
                      }else{
                        $rt['validation_error'] = $response;
                        $rt['error_code'] = '1001';
                        $rt['status'] = '0';
                        $rt['status_text'] = 'Failed';
                        $rt['message'] = 'validation error';
                        $res=json_encode($rt);
                        echo $res; exit;

                      }
                    }
                }
            }
                      }else{
                      $rt=$check_access_token;
                      echo json_encode($rt); exit;
                   }
            
    } 

        public function uploadImages($directory,$filename,$file_tmp){
                // Check to see if any PHP files are trying to be uploaded
        
        // $file_name_with_full_path = $_FILES[$fileType]['tmp_name'];
            
        if(is_uploaded_file($file_tmp)){
            if (function_exists('curl_file_create')) { // php 5.5+
                        $cFile = curl_file_create($file_tmp);
            } else { // 
                        $cFile = '@' . realpath($file_tmp);
            }
        }
            
        $post = array('file'=> $cFile);
        $ch = curl_init();
        $target_url = UPLOAD_CONTENT_URL_SSL.'fileupload.php?directory='.$directory.'&filename='.$filename;
        curl_setopt($ch, CURLOPT_URL,$target_url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);              
        curl_close($ch);
        $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
        $result = json_decode($result, true);
        //print_r($result);die;
        if (!empty($result['success'])) {
            return true;

        }else{
            return false;
        }
        
    } 

    public function reArrayFiles($file_post) {

            $file_ary = array();
            $file_count = count($file_post['name']);
            $file_keys = array_keys($file_post);

            for ($i=0; $i<$file_count; $i++) {
                foreach ($file_keys as $key) {
                    $file_ary[$i][$key] = $file_post[$key][$i];
                    
                }
            }

            return $file_ary;
        } 

    private function requiredResponse($request){
      $this->load->model('account/credit_application');
            $api_response = array();
            $required_fields[0] = 'first_name'; //documents
            $required_fields[1] = 'middle_name';
            $required_fields[2] = 'last_name';
            $required_fields[3] = 'father_name';
            $required_fields[4] = 'dob';
            $required_fields[5] = 'current_pincode';
            $required_fields[6] = 'documents';
            $required_fields[7] = 'email';
            $required_fields[8] = 'phone_no';
            $required_fields[9] = 'company_name';
            $required_fields[10] = 'pan_no';
            $required_fields[11] = 'aadhaar_no';
            $required_fields[12] = 'business_start_year';
            $required_fields[13] = 'months_in_current_location';
            $required_fields[14] = 'gst_number';
            $required_fields[15] = 'permanent_address';
            $required_fields[16] = 'permanent_pincode';
            $required_fields[17] = 'permanent_city';
            $required_fields[18] = 'permanent_state';
            $required_fields[19] = 'gst_number';
            $required_fields[20] = 'id';
            $required_fields[21] = 'version';
            $required_fields[22] = 'gender';
            $required_fields[23] = 'current_address';
 
            foreach($request as $key => $value) {
              if (in_array($key, $required_fields)) {
                if ($key == 'id') {
                  $api_response['credit_application_id']  = $value;
                }
                if ($key == 'version') {
                  $api_response['version']  = $value;
                  if($api_response['version']=='1' || $api_response['version']=='2'){
                      $api_response['credit_application_form_status']='0';
                  }else{
                      $api_response['credit_application_form_status']=$request['draft'];
                  }
                }
                if ($key == 'gender') {
                  $api_response['gender']['value'] = $value;
                  $api_response['gender']['is_compulsory'] = '1';
                  $api_response['gender']['regex_pattern'] = '';
                  $api_response['gender']['fields'] = array('male','female');
                }
                if ($key == 'first_name') {
                  $api_response['first_name']['value'] = $value;
                  $api_response['first_name']['is_compulsory'] = '1';
                  $api_response['first_name']['regex_pattern'] = '';
                  $api_response['first_name']['min'] = '3';
                }

                if ($key == 'middle_name') {
                  $api_response['middle_name']['value'] = $value;
                  $api_response['middle_name']['is_compulsory'] = '0';
                  $api_response['middle_name']['regex_pattern'] = '';
                }

                if ($key == 'last_name') {
                  $api_response['last_name']['value'] = $value;
                  $api_response['last_name']['is_compulsory'] = '1';
                  $api_response['last_name']['regex_pattern'] = '';
                  $api_response['last_name']['min'] = '3';
                }

                if ($key == 'father_name') {
                  $api_response['father_name']['value'] = $value;
                  $api_response['father_name']['is_compulsory'] = '1';
                  $api_response['father_name']['regex_pattern'] = '';
                  $api_response['father_name']['min'] = '3';
                }

                if ($key == 'email') {
                  $api_response['email']['value'] = $value;
                  $api_response['email']['is_compulsory'] = '1';
                  $api_response['email']['regex_pattern'] = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix';
                }

                if ($key == 'phone_no') {
                  $api_response['phone_no']['value'] = $value;
                  $api_response['phone_no']['is_compulsory'] = '1';
                  $api_response['phone_no']['regex_pattern'] = '';
                  $api_response['phone_no']['max'] = '10';
                }

                if ($key == 'dob') {
                  $api_response['dob']['value'] = $value;
                  $api_response['dob']['is_compulsory'] = '1';
                  $api_response['dob']['regex_pattern'] = 'DD-MM-YYYY';
                }

                if ($key == 'company_name') {
                  $api_response['company_name']['value'] = $value;
                  $api_response['company_name']['is_compulsory'] = '1';
                  $api_response['company_name']['regex_pattern'] = '';
                }

                if ($key == 'current_pincode') {
                  $api_response['current_pincode']['value'] = $value;
                  $api_response['current_pincode']['is_compulsory'] = '1';
                  $api_response['current_pincode']['regex_pattern'] = '';
                  $api_response['current_pincode']['min'] = '6';
                }

                if ($key == 'gst_number') {
                  $api_response['gst_number']['value'] = $value;
                  $api_response['gst_number']['is_compulsory'] = '1';
                  $api_response['gst_number']['regex_pattern'] = "^[0-9]{2}[A-Z]{3}[C,P,H,F,A,T,B,L,J,G,E]{1}[A-Z]{1}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}?$";
                }

                if ($key == 'business_start_year') {
                  $api_response['business_start_year']['value'] = $value;
                  $api_response['business_start_year']['is_compulsory'] = '1';
                  $api_response['business_start_year']['regex_pattern'] = '';
                }

                if ($key == 'months_in_current_location') {
                  $api_response['months_in_current_location']['value'] = $value;
                  $api_response['months_in_current_location']['is_compulsory'] = '0';
                  $api_response['months_in_current_location']['regex_pattern'] = '';
                }

                if ($key == 'pan_no') {
                  $api_response['pan_no']['value'] = $value;
                  $api_response['pan_no']['is_compulsory'] = '1';
                  $api_response['pan_no']['regex_pattern'] = '([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}';
                }

                if ($key == 'aadhaar_no') {
                  $api_response['aadhaar_no']['value'] = $value;
                  $api_response['aadhaar_no']['is_compulsory'] = '1';
                  $api_response['aadhaar_no']['min'] = '12';
                  $api_response['aadhaar_no']['max'] = '12';
                  $api_response['aadhaar_no']['regex_pattern'] = '';
                }

                if ($key == 'permanent_address') {
                  $api_response['permanent_address']['value'] = $value;
                  $api_response['permanent_address']['is_compulsory'] = '1';
                  $api_response['permanent_address']['regex_pattern'] = '';
                }

                if ($key == 'current_address') {
                  $api_response['current_address']['value'] = $value;
                  $api_response['current_address']['is_compulsory'] = '1';
                  $api_response['current_address']['regex_pattern'] = '';
                }

                if ($key == 'permanent_pincode') {
                  $api_response['permanent_pincode']['value'] = $value;
                  $api_response['permanent_pincode']['is_compulsory'] = '1';
                  $api_response['permanent_pincode']['regex_pattern'] = '';
                }

                if ($key == 'permanent_city') {
                  $api_response['permanent_city']['value'] = $value;
                  $api_response['permanent_city']['is_compulsory'] = '1';
                  $api_response['permanent_city']['regex_pattern'] = '';
                }

                if ($key == 'permanent_state') {
                  $api_response['permanent_state']['value'] = $value;
                  $api_response['permanent_state']['is_compulsory'] = '1';
                  $api_response['permanent_state']['regex_pattern'] = '';
                }

                // pancard aadhaar_card six_months_bank_statement shop_photo six_months_bank_statement
                $required_doc[1]='address_proof_document';
                $required_doc[1]='aadhaar_card';
                $required_doc[2]='six_months_bank_statement';
                $required_doc[3]='voter_id';
                $required_doc[4]='driving_license';

                $not_reuired_doc[1]='pancard';
                $not_reuired_doc[2]='shop_photo';
                $not_reuired_doc[3]='selfie_with_shop'; 
                $not_reuired_doc[3]='visting_card_photo';

                foreach ($required_doc as $key1 => $req_value_name) {
                  if($req_value_name=='six_months_bank_statement'){
                      $api_response[$req_value_name]['min_image'] = '1';
                      $api_response[$req_value_name]['max_image'] = '5';
                      $api_response[$req_value_name]['max'] = '8';
                      $api_response[$req_value_name]['is_compulsory'] = '1';
                      $api_response[$req_value_name]['regex_pattern'] = '';
                      $api_response[$req_value_name]['images']=array();
                  }else{
                      $api_response['address_proof_document']['min_image'] = '1';
                      $api_response['address_proof_document']['max_image'] = '5';
                      $api_response['address_proof_document']['max'] = '8';
                      $api_response['address_proof_document']['is_compulsory'] = '1';
                      $api_response['address_proof_document']['regex_pattern'] = '';
                      $api_response['address_proof_document']['images']=array();
                  }
                 
                }
                foreach ($not_reuired_doc as $key2 => $not_req_value_name) {
                  $api_response[$not_req_value_name]['min_image'] = '1';
                  $api_response[$not_req_value_name]['max_image'] = '1';
                  $api_response[$not_req_value_name]['max'] = '8';
                  $api_response[$not_req_value_name]['is_compulsory'] = '0';
                  $api_response[$not_req_value_name]['regex_pattern'] = '';
                  $api_response[$not_req_value_name]['images']=array();
                }
                if($key == 'documents' ){
                        if(count($value) > 0){ 
                            //application_document
                            foreach($value as $document_key => $document_value) {
                              if ($document_key == 'application_document') {
                                //echo '<pre>'; print_r($document_value); exit;
                                if (count($document_value) > 0) {
                                  foreach($document_value as $file_key => $file_value) {
                                    if ($file_value['name'] == 'aadhaar_card' || $file_value['name'] == 'voter_id' || $file_value['name'] == 'driving_license' || $file_value['name'] == 'address_proof_document') {
                                      
                                      $api_response['address_proof_document']['images'][$file_key] = $file_value['file_path'];
                                    }else if($file_value['name'] == 'pancard' || $file_value['name'] == 'six_months_bank_statement' || $file_value['name'] == 'shop_photo' || $file_value['name'] == 'selfie_with_shop' || $file_value['name'] == 'visting_card_photo'){
                                      $api_response[$file_value['name']]['images'][$file_key] = $file_value['file_path'];
                                    }
                                  }
                                }
                              }
                            }
                        }
                    }
              }
            }

           

            $wsb_credit_payment = new WsbCreditPayment($this);
            $customer_id='';
            $customer_id_array=array();
              if(!empty($request['customer_id'])){
                $customer_id=$request['customer_id'];
                $customer_id_array=array($request['customer_id']); 
                $limit_data = $wsb_credit_payment->getActiveWsbCreditLimitByCustomerIds($customer_id_array);
              }
            
             $credit_application_activation_status=$this->model_account_credit_application->creditApplicationStatusForBanner($customer_id);
            
            if(!empty($limit_data[$customer_id])){
              $limit=$limit_data[$customer_id];
            }else{
              $limit='0';
            }

            if(!empty($credit_application_activation_status['message'])){
              $approval_message=$credit_application_activation_status['message'];
            }else{
              $approval_message='Your credit limit has been active now for Rs. '.$limit.'/-. Please use the “Order on WholesaleBox credit” payment method at the time of order placement.';
            }
            if(!empty($credit_application_activation_status['gst_number'])){
                $pre_approved_limit='25000';
              }else{
                $pre_approved_limit='10000';
              }
            $api_response['visting_card_photo_sample_image_url']='https://cdnimages.net/img/credit_visiting_card.png';
            $api_response['selfie_sample_image_url']='http://d36qiqd7gl7e25.cloudfront.net/img/selfie-with-shop.jpeg';
            $api_response['shop_photo_sample_image_url']='http://d36qiqd7gl7e25.cloudfront.net/img/shop-photo.jpeg';
            $api_response['activation_status']=$credit_application_activation_status['activation_status'];
            $api_response['approval_message'] = 'You are eligible for pre-approved credit Rs. '.$pre_approved_limit.'/- @ 0% interest for 30 days.';

            $api_response['activation_message'] = $approval_message;

            $api_response["courier_address"] = "Credit Department\nWholesalebox, B-1, Crystal Mall\nBanipark, Jaipur-302016\nPh-+918239778680";

            $api_response["documents_to_be_couriered"] = array("Agreement","NACH Form","PDC","Self Attested PAN Copy","Self Attested ID Proof");

            $api_response["kyc_in_progress_message"] = "Thank you for uploading your documents. Kindly print out the agreement sent on your email and courier the documents back to us. Once received we will update you on activation of credit limit. For any queries contact us on +91-8239778680";




              return $api_response; 

    }


    /**
     * removeDocument
     * @author Rahul Singh
     * @description remove document 
     * @return status
     */
    public function removeDocument(){
      $return = array();
          $this->load->model('account/credit_application');
          $inputJSON = file_get_contents('php://input');
          $request = json_decode($inputJSON, TRUE);
          $check_access_token=$this->validateAccessTokenForUser($request);
            if($check_access_token['status']=='1' || (!empty($request['web_access']) && $request['web_access']=='1')){
                if(!empty($request['credit_application_id']) && !empty($request['image_url'])){
                
                  // if request is not coming from admin panel, then we need to check credit application status,
                  // if credit application is approved, then document deletion will not be allowed from frontend.
                  if (empty($request['web_access'])) {
                    $credit_application_status = $this->model_account_credit_application->getCreditApplicationDocumentStatus($request['credit_application_id']);
                    //if (in_array($credit_application_status, $approved_status)){
                    if (empty($request['khufiya_user_id'])  && !empty($credit_application_status)){
                      $return['error_code'] = '1006';
                      $return['status'] = '0';
                      $return['status_text'] = 'Failed';
                      $return['message'] = "Documents can't be removed once credit application is under process.";
                      echo json_encode($return); exit;
                    }
                  }
                  
                  $getSaveImage=explode('credit_application',$request['image_url']);
                  if(!empty($getSaveImage['1'])){
                    $document='credit_application'.$getSaveImage['1'];
                    $document_id = $this->model_account_credit_application->getCreditDocumentId($request['credit_application_id'],$document);
                    if(!empty($document_id)){
                      $this->model_account_credit_application->deleteCreditDocument($document_id);
                      $return['status'] = '1';
                      $return['status_text'] = 'Success';
                      $return['message'] = 'Success';
                    }else{
                        $return['error_code'] = '1005';
                        $return['status'] = '0';
                        $return['status_text'] = 'Failed';
                        $return['message'] = 'Document Not Found.';
                    }
                  }else{
                    $return['error_code'] = '1004';
                    $return['status'] = '0';
                    $return['status_text'] = 'Failed';
                    $return['message'] = 'Invalid image url.';
                  }
                }else{
                  $return['error_code'] = '1002';
                  $return['status'] = '0';
                  $return['status_text'] = 'Failed';
                  $return['message'] = 'http request does not have credit application id or image url.';
                }
              }else{
                $return=$check_access_token;
              }
      echo json_encode($return); exit;
    }


      public function pincodeAddress($pincode){
          $response_data = array();
          $RegisterForm = new RegisterForm();
          $this->request['postcode'] = $pincode;
          $postcodeValidation = $RegisterForm->postcodeValidation($this->request);
            if ($postcodeValidation['valid']){
                    $request['pincode'] = $pincode;
                    $data_json = json_encode($request);

                    $api_url = "https://www.wholesalebox.in/index.php?route=restapi/lookup/pincode";
                    $ch = curl_init($api_url);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_HTTPHEADER,
                        array('Content-Type: application/json',
                            'Content-Length: ' . strlen($data_json))
                    );
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);

                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);

                    $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                    $data = json_decode($result, true );


                   $city = isset($data[0]['city']) ? $data[0]['city']:'';
                   $state = isset($data[0]['zone']) ? $data[0]['zone']:'--- Please Select ---';
                   $country = isset($data[0]['country']) ? $data[0]['country']:'';
                   $country_id = isset($data[0]['country_id']) ? $data[0]['country_id']:99;;
                   $zone_id = isset($data[0]['zone_id']) ? $data[0]['zone_id']:'';

                   $response_data['error']      = 0;
                   $response_data['country_id'] = $country_id;
                   $response_data['zone_id']    = $zone_id;
                   $response_data['city']       = $city;
                   $response_data['state']      = $state;
                   $response_data['country']    = $country;

               }
               else
               {
                  $response_data['msg']   = $postcodeValidation['errors']['message'];
                  $response_data['error'] = 1;
               }

        $this->data_packet->data       = $response_data;
        $this->data_packet->message    = 'data fatch successfully';
        $this->data_packet->statusCode = 200;
        return $this->data_packet;

     }

  private function getQueryStringForWeb($request){
    $query_string = '';
            if($request['customer_id'] != ''){
                $query_string .= '&customer_id=' . $request['customer_id'];
            }else if(isset($customer_id) && $customer_id!=''){
                $query_string .= '&customer_id=' . $customer_id;
            }
            if(isset($request['token']) && $request['token'] != ''){
                $query_string .= '&token=' . $request['token']; 
            }
            
            if(isset($request['crm_user_id']) && $request['crm_user_id'] != ''){
                $query_string .= '&crm_user_id=' . $request['crm_user_id']; 
            }
            if(isset($request['crm_user_password']) && $request['crm_user_password'] != ''){
                $query_string .= '&crm_user_password=' . $request['crm_user_password']; 
            }
            if(isset($request['khufiya_user_id']) && $request['khufiya_user_id'] != ''){
                    $query_string .= '&khufiya_user_id=' . $request['khufiya_user_id']; 
            }
            if(!empty($request['draft']) && $request['draft'] =='1'){
                    $query_string .= '&form_type=2'; 
            }else{
               $query_string .= '&update_success=success';  
            }
            if(!empty($request['success_type']) && $request['success_type'] =='1'){
                    $query_string .= '&success_type='.$request['success_type']; 
            }else if(!empty($request['success_type']) && $request['success_type'] =='2'){
                    $query_string .= '&success_type='.$request['success_type']; 
            }
            
            if(!empty($request['credit_application_id'])){
                    $query_string .= '&credit_application_id='.$request['credit_application_id']; 
            }
            return $query_string;

  }

    /**
   * Function : insertBasicInfo
   * Method to save or update credit application form
   * Request Parameters : credit application form detail
   * Type : Post
   * @author Rahul
   * Output : true or false
   * */
  public function insertBasicInfo(){
    $this->load->model('account/credit_application');
    $this->load->model('webengage/webengage');
          //$request=$this->request;
          $inputJSON = file_get_contents('php://input');
          $request = json_decode($inputJSON, TRUE);
              $request['customer_id']=$request['user_id'];
              $request['version']='3';
                //for phone
                $phoneAlredyExist = $this->phoneOrEmailValidate($request['phone_no'],'phone',$request['credit_application_id']);
                if($phoneAlredyExist){
                  //first form save
                  $add_webengage='0';
                  if(isset($request['credit_application_form_status']) && $request['credit_application_form_status'] < '1'){
                      $request['draft']= '1';
                      $add_webengage='1';
                      }

                  $saveDate = $this->model_account_credit_application->saveShortCredit($request);
                  if($saveDate){
                    //====webengage start===//
                    if(!empty($request['customer_id']) && !empty($add_webengage)){
                      $this->model_webengage_webengage->updateCreditApplicationStatusOnWebengage($request['customer_id'],'1');
                    }
                    //====webengage end===//
                    $request['credit_application_id']=$saveDate;
                     $rt['status'] = '1';

                     $credit_application_activation_status=$this->model_account_credit_application->creditApplicationStatusForBanner($request['customer_id']);

                        $wsb_credit_payment = new WsbCreditPayment($this);
                        $customer_id=array($request['customer_id']);
                        $limit_data = $wsb_credit_payment->getActiveWsbCreditLimitByCustomerIds($customer_id);
                        

                          if(!empty($limit_data[$request['customer_id']])){
                            $limit=$limit_data[$request['customer_id']];
                          }else{
                            $limit='0';
                          }
                            if(!empty($credit_application_activation_status['gst_number'])){
                              $pre_approved_limit='25000';
                            }else{
                              $pre_approved_limit='10000';
                            }

                            if(!empty($request['web_access'])=='1'){
                              $request['success_type']='1';
                              $getQueryStringForWeb=$this->getQueryStringForWeb($request);
                              if(isset($request['khufiya_user_id']) && $request['khufiya_user_id'] != ''){
                                $rt['redirect_action'] = $this->url->link('account/credit_application'.$getQueryStringForWeb, '', 'SSL');
                              }else{
                                $rt['redirect_action'] = $this->url->link('account/credit_application/success'.$getQueryStringForWeb, '', 'SSL');
                              }

                            }

                     $rt['approval_message'] = 'You have been pre-approved for credit of Rs. '.$pre_approved_limit.'/- @ 0% interest for 30 days. For activation and higher limit, please complete step 2 and upload all your KYC documents';

                    $rt["courier_address"] = "Credit Department\nWholesalebox, B-1, Crystal Mall\nBanipark, Jaipur-302016\nPh-+918239778680";

                    $rt["documents_to_be_couriered"] = array("Agreement","NACH Form","PDC","Self Attested PAN Copy","Self Attested ID Proof");

                    $rt["kyc_in_progress_message"] = "Thank you for uploading your documents. Kindly print out the agreement sent on your email and courier the documents back to us. Once received we will update you on activation of credit limit. For any queries contact us on +91-8239778680";
                     $rt['status_text'] = 'Success';
                     $rt['message'] = 'Success';
                  }else{
                    $rt['error_code'] = '1007';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Credit application not save or update';
                  }
                }else{
                  $rt['error_code'] = '1006';
                  $rt['status'] = '0';
                  $rt['status_text'] = 'Failed';
                  $rt['message'] = 'Phone Number Already Used.';
                }

               
      echo json_encode($rt); exit;

  }


    }