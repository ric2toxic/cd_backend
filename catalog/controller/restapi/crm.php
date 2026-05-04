<?php
/**
 * Author: Anurag Jain
 * Date: 7 March 2018
 */

    class ControllerRestapiCrm extends Controller{

        public function __construct($registry) {
            parent::__construct($registry);
            $this->registry = $registry;
            $this->_validateRequest();
        }

        // validate the user
        private function _validateRequest() {
            // check for expected request type
            if ($this->request->server['REQUEST_METHOD'] != 'POST') {
                $response = array(
                    'error_code' => '1',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Request type not accepted.'
                );

                echo json_encode($response);
                exit;
            }

            // fetch the post data
            $inputJSON = file_get_contents('php://input');
            $request = json_decode( $inputJSON, TRUE );

            // check for access token
            // if (!isset($request['access_token'])) {
            //     $response = array(
            //         'error_code' => '2',
            //         'status' => '0',
            //         'status_text' => 'Failed',
            //         'message' => 'Request does not have access token.'
            //     );
            // 
            //     echo json_encode($response);
            //     exit;
            // }

            // check for user id
            if (!isset($request['user_id'])) {
                $response = array(
                    'error_code' => '3',
                    'status' => '0',
                    'status_text' => 'Failed',
                    'message' => 'Request does not have user id.'
                );

                echo json_encode($response);
                exit;
            }

            // authenticate user
            // $this->load->model('restapi/service');
            // $check_access_token = $this->model_restapi_service->checkUserByAccessToken($request['access_token'],$request['user_id']);
            // 
            // if ($check_access_token == 0) {
            //     $response = array(
            //         'error_code' => '4',
            //         'status' => '0',
            //         'status_text' => 'Failed',
            //         'message' => 'Authentication failed!!'
            //     );
            // 
            //     echo json_encode($response);
            //     exit;
            // }
        }

        
        public function unblock_sales_staff() {
          
          $inputJSON = file_get_contents('php://input');
    			$request = json_decode( $inputJSON, TRUE );
    			$this->load->model('restapi/service');
    			$rt = array();
          
          $unblock_result = $this->model_restapi_service->unblockSalesStaff($request['staff_id'], $request['user_id']);
          $rt['status'] = "1";
          $rt['status_text'] = 'FSE unblocked.';
          $rt['message'] = "FSE unblocked successfully.";
          echo json_encode($rt); exit;
        }
        
         
    /**
     * @author Vishnu Shekhawat
     * @description log text in crm 
     */
    public function logTextInCrm() {

        $this->load->model('restapi/service');
        $this->load->model('lead/lead');

        $this->load->model('short_message_logs/short_message_logs');
        $sms_model = $this->model_short_message_logs_short_message_logs;

        $inputJSON = file_get_contents('php://input');
        $request = json_decode($inputJSON, TRUE);
        $rt = array();
        if (isset($request['access_token'])) {
            $user_id = $request['user_id'] ?? 0;
            $access_token = $request['access_token'] ?? '';
            $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
            if ($check_access_token > 0) {
                
                if (isset($request['message_array']) && !empty($request['message_array']) && is_array($request['message_array'])) {
                    $success_message_count = 0;
                    $total_message = 0;
                    $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);
                    
                    /* get customers latest timestamp; to be verified for duplicacy */
                    $latest_timestamp = $this->model_lead_lead->getLastSyncTimeStamp( $user_id, $aws_mysqli );

                    foreach ($request['message_array'] as $message_data) {
                        $total_message++;
                        $sender_name = (isset($message_data['sender_name']) && !empty(trim($message_data['sender_name']))) ? $message_data['sender_name'] : '';
                        $message = (isset($message_data['body']) && !empty(trim($message_data['body']))) ? $message_data['body'] : '';
                        $timestamp = (isset($message_data['timestamp']) && !empty(trim($message_data['timestamp']))) ? $message_data['timestamp'] : '';

                        /* message is not duplicate if message timestamp is greater than latest found timestamp from DB */ 
                        if ( $timestamp > $latest_timestamp ) {
                            
                            $rs = $this->model_lead_lead->saveLogInCrm($user_id, $message, $sender_name, $timestamp, $aws_mysqli);
                            if ($rs) {
                                $success_message_count++;
                            }
                        }
                    }

                    /** updating all_sms criteria **/

                    // get old stored criteria
                    $old_critera = $sms_model->getCustomerOldSyncedCriteria( (int) $user_id );

                    if ( !in_array( 'all_sms', $old_critera )) {

                        $sms_model->addCustomerCriteriaAvailability( (int) $user_id, 'all_sms' );
                    }

                    if ($aws_mysqli) {
						// Database is reachable
						mysqli_close($aws_mysqli);
					}
                    $rt = array();
                    $rt['success_code'] = '1001';
                    $rt['status'] = '1';
                    $rt['status_text'] = 'Success';
                    $rt['message'] = $success_message_count . ' messages send successfully out of ' . $total_message . '.';


                } else {
                    $rt = array();
                    $rt['success_code'] = '1002';
                    $rt['status'] = '0';
                    $rt['status_text'] = 'Failed';
                    $rt['message'] = 'Empty Message list.';
                }
            } else {
                $rt = array();
                $rt['error_code'] = '1002';
                $rt['status'] = '0';
                $rt['status_text'] = 'Failed';
                $rt['message'] = 'Invalid Access Token.';
            }
        } else {
            $rt = array();
            $rt['error_code'] = '1002';
            $rt['status'] = '0';
            $rt['status_text'] = 'Signup failed';
            $rt['message'] = 'http request does not have access token.';
        }

        echo json_encode($rt);
        exit;
    }

    
    public function getLastSyncTimeStamp() {
        $this->load->model('restapi/service');
        $this->load->model('lead/lead');

        $inputJSON = file_get_contents('php://input');
        $request = json_decode($inputJSON, TRUE);
        //echo $this->config->get('config_store_id');
        $rt = array();
        if (!isset($request['access_token']) || empty($request['access_token'])) {
            $rt = array();
            $rt['error_code'] = '1002';
            $rt['status'] = '0';
            $rt['status_text'] = 'Signup failed';
            $rt['message'] = 'http request does not have access token.';
            echo json_encode($rt);
            exit;
        }

        $user_id = $request['user_id'] ?? 0;
        $access_token = $request['access_token'] ?? '';
        $check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_id);
        if ($check_access_token > 0) {

            $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);
            $lastSyncTimeStamp = $this->model_lead_lead->getLastSyncTimeStamp( $user_id, $aws_mysqli );
                
            $rt = array();
            $rt['success_code'] = '1001';
            $rt['status'] = '1';
            $rt['status_text'] = 'Success';
            $rt['last_timestamp'] = $lastSyncTimeStamp;
           
        } else {
            $rt = array();
            $rt['error_code'] = '1002';
            $rt['status'] = '0';
            $rt['status_text'] = 'Failed';
            $rt['message'] = 'Invalid Access Token.';
        }


        echo json_encode($rt);
        exit;
    }

    /**
     * get customer prefs for crm (group by pref type)
     * @author Anurag Jain, 21st June 2019
     */
    public function getCustomerCategoriesByPreferenceType()
    {
        $inputJSON = file_get_contents('php://input');
        $request = json_decode( $inputJSON, TRUE );

        $rt = array();

        if ( isset($request['access_key'] )) {
            if ( $request['access_key'] == WEB_API_ACCESS_KEY ) {

                $rt['status'] = '1';
                $rt['status_text'] = 'Success';
                $rt['customer_preference_categories'] = array();

                $this->load->model('preferences');

                $pref_type_category_ids = array();

                $customer_ids = $request['user_id'] ?? '';

                $customer_ids_arr = array();
                if ( !empty( $customer_ids ) ) {
                    $customer_ids_arr = explode( ',', $customer_ids );
                }
                
                if ( !empty( $customer_ids_arr )) {

                    $pref_type_category_ids = $this->model_preferences->getCustomerCategoriesByPreferenceTypeForCRM( $customer_ids_arr );
                }

                if ( !empty( $pref_type_category_ids )) {
                    $rt['customer_preference_categories'][] = $pref_type_category_ids;
                }
            } else {

                $rt['status'] = '0';
                $rt['status_text'] = 'Failed';
                $rt['message'] = 'Invalid Http request.';
            }
        } else {

            $rt['status'] = '0';
            $rt['status_text'] = 'Failed';
            $rt['message'] = 'Invalid Http request.';
        }

        echo json_encode($rt);
        exit;
    }
}
?>
