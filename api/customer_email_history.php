<?php
     require_once(__DIR__.'/system.php');
     require_once(DIR_SYSTEM.'library/solr/lookup.php');
     require_once(DIR_CATALOG.'form/register_form.php');
     require_once(DIR_SYSTEM.'library/customer.php');
     require_once( DIR_SYSTEM . 'library/operations/payment_gateway/wsb_credit.php' );

    class Customer_email_historyController extends SystemController
    {
        private $error = "";

        public function __construct($params) {

            parent::__construct($params);

            $inputJSON = file_get_contents('php://input');
            $app_request_data = json_decode($inputJSON, TRUE);

        }

      public function saveCustomerEmailHistory()
      {
        $local_image_save_dir='../image/attachments_files/';
        if (!file_exists($local_image_save_dir)) {
        mkdir($local_image_save_dir, 0777, true);
        }
        $this->load->model('account/customer_email_history');
        $inputJSON = file_get_contents('php://input');
        $request = json_decode($inputJSON, TRUE);
        if(!empty($request)){
        $check_access_token=$this->validateAccessTokenForUser($request);
        if($check_access_token['status']=='1'){
          if(!empty($request['mail_list'])){
            foreach ($request['mail_list'] as $key => $value) {
                $file_replace_array=array();
                $file_array=array();
                if(!empty($value['message_id'])){
                  $mssg_id=$value['message_id'];
                }else{
                  $mssg_id=uniqid();
                }

                if(!empty($value['attachments'])){
                    foreach ($value['attachments'] as $attachment_key => $attachment_value) {
                      $file_name=uniqid().$attachment_value['file_extension_name'];
                      $file_replace_array[$attachment_key]=$attachment_value['file_name'];
                      $encoded_attachment_content=$attachment_value['data'];
                      $switched = str_replace(['-', '_'], ['+', '/'], $encoded_attachment_content);
                      $attachment_content_decoded = base64_decode($switched);


                      $file = $local_image_save_dir . $file_name;
                      file_put_contents($file, $attachment_content_decoded);

                            if(SITE_ENVIRONMENT == 'Production'){
                                  $file_folder_name = $mssg_id;
                              }else{
                                  $file_folder_name = 'staging_' .$mssg_id;  
                              }
                              $uploads_dir = 'bank_attachment/'.$file_folder_name.'/files/';
                              $result = $this->uploadImages($uploads_dir,$file_name,$file);
                              if($result){
                                $server_file_name=STATIC_CONTENT_URL_SSL.$uploads_dir.$file_name;
                                $server_file_path=$uploads_dir.$file_name;
                                $file_array[$attachment_key][$attachment_value['file_name']]=$server_file_path;
                                unlink($file);
                              }
                    }
                }
                if(!empty($value['body'])){
                  $encoded_body_content=$value['body'];
                      $switched = str_replace(['-', '_'], ['+', '/'], $encoded_body_content);
                      $body_content_decoded = base64_decode($switched);
                      if(!empty($file_array)){
                        foreach ($file_array as $filename => $filevalue) {
                          foreach ($filevalue as $file_name => $file_value) {
                          $body_content_decoded=str_replace('cid:'.$file_name, $file_value, $body_content_decoded);
                          }
                        }
                      }
                          $html_file_name=$mssg_id.'.html';
                          $html_file = $local_image_save_dir. $html_file_name;
                          file_put_contents($html_file, $body_content_decoded);
                          if(SITE_ENVIRONMENT == 'Production'){
                                  $file_folder_name = $mssg_id;
                              }else{
                                  $file_folder_name = 'staging_' .$mssg_id;  
                              }
                              $uploads_dir = 'bank_attachment/'.$file_folder_name.'/files/';
                              $result = $this->uploadImages($uploads_dir,$html_file_name,$html_file);
                              if($result){
                                $save_data=array();
                                $time=round($value['date']/1000);
                                $save_data['date']=date('Y-m-d H:i:s',$time);
                                $save_data['user_id']=$request['user_id'];
                                $save_data['from']=$value['from'];
                                $save_data['message_id']=$value['message_id'];
                                $save_data['subject']=$value['subject'];
                                $save_data['to']=$value['to'];
                                $save_data['account_email_id']=$request['account_email_id']??'0';
                                $save_data['file_path']=$uploads_dir.$html_file_name;
                                $last_insert_id = $this->model_account_customer_email_history->saveCustomerEmailHistory($save_data);
                                if(!empty($last_insert_id)){
                                  if(!empty($file_array)){
                                      foreach ($file_array as $filename => $filevalue) {
                                        foreach ($filevalue as $file_name => $file_value) {
                                        $save_data['date']=date('Y-m-d H:i:s',$time);
                                        $save_data['customer_email_history_id']=$last_insert_id;
                                        $save_data['file_path']=$file_value;
                                        $last_attach_insert_id = $this->model_account_customer_email_history->saveCustomerEmailHistoryAttachment($save_data);
                                      }
                                      }
                                    }
                                }
                                //$server_file_name=STATIC_CONTENT_URL_SSL.$uploads_dir.$html_file_name;
                                unlink($html_file);
                              }

                }else{
                  $save_data=array();
                                $time=round($value['date']/1000);
                                $save_data['date']=date('Y-m-d H:i:s',$time);
                                $save_data['user_id']=$request['user_id'];
                                $save_data['from']=$value['from']??'0';
                                $save_data['message_id']=$value['message_id']??'0';
                                $save_data['subject']=$value['subject']??'0';
                                $save_data['to']=$value['to']??'0';
                                $save_data['account_email_id']=$request['account_email_id']??'0';
                                $save_data['file_path']='';
                                $last_insert_id = $this->model_account_customer_email_history->saveCustomerEmailHistory($save_data);
                                if(!empty($last_insert_id)){
                                  if(!empty($file_array)){
                                      foreach ($file_array as $filename => $filevalue) {
                                        foreach ($filevalue as $file_name => $file_value) {
                                        $save_data['date']=date('Y-m-d H:i:s',$time);
                                        $save_data['customer_email_history_id']=$last_insert_id;
                                        $save_data['file_path']=$file_value;
                                        $last_attach_insert_id = $this->model_account_customer_email_history->saveCustomerEmailHistoryAttachment($save_data);
                                      }
                                      }
                                    }
                                }
                }
              
            }
                      $rt['status'] = '1';
                      $rt['status_text'] = 'Success';
                      $rt['message'] = 'Success';
          }
        }else{
          $rt=$check_access_token;
        }
      }else{
        $rt['error_code'] = '1007';
        $rt['status'] = '0';
        $rt['status_text'] = 'Failed';
        $rt['message'] = 'No request found.';
      }
        echo json_encode($rt); exit;
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




        public function uploadImages($directory,$filename,$file_tmp){
                // Check to see if any PHP files are trying to be uploaded
        
        // $file_name_with_full_path = $_FILES[$fileType]['tmp_name'];
            
            if (function_exists('curl_file_create')) { // php 5.5+
                        $cFile = curl_file_create($file_tmp);
            } else { // 
                        $cFile = '@' . realpath($file_tmp);
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
        if (!empty($result['success'])) {
            return true;

        }else{
            return false;
        }
        
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


      public function getEmailIdList(){
        $this->load->model('account/customer_email_history');
        $inputJSON = file_get_contents('php://input');
        $request = json_decode($inputJSON, TRUE);
        $check_access_token=$this->validateAccessTokenForUser($request);
        if($check_access_token['status']=='1'){
           $email_list = $this->model_account_customer_email_history->get_email_id_list($request['user_id']);
           if(!empty($email_list->rows)){
            $rt['status'] = '1';
            $rt['email_id_list'] = $email_list->rows;
            $rt['message'] = 'Email id list';
           }else{
            $rt['status'] = '1';
            $rt['email_id_list'] = array();
            $rt['message'] = 'Email id list';
           }
        }else{
          $rt=$check_access_token;
        }
        echo json_encode($rt); exit;
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

    }