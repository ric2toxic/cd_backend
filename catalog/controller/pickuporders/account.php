<?php
/**
*
*/
class ControllerOpsapiAccount extends Controller
{

    public $access_token = '';
    public $error = array();
    public $user = array();

    public function index(){

       
    }

    public function login(){
        //Set headers for CROSS domain policy
        $this->load->model('opsapi/global');
        $this->model_opsapi_global->headers();


        if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
            $requestBody = $_POST;

            if(!$this->loginValidation($requestBody)){
                if(isset($this->error['warning'])){
                    $response['status'] = 'Failure';
                    $response['error'] = $this->error['warning'];
                    $response['access_token'] = '';

                }
            }else{
                $this->load->model('opsapi/account');

                //$sellerInfo = $this->model_opsapi_account->getSellerInfo($this->customer->getId);
                //print_r($sellerInfo);
                $response['status'] = 'Success';
                $response['access_token'] = $this->access_token;
                $response['user_info']['name'] = $this->user['name'];
                $response['user_info']['user_id'] = $this->user['user_id'];
                $response['user_info']['email'] = $this->user['email'];
                $response['user_info']['username'] = $this->user['username'];

            }


           echo json_encode($response);



        }
        $response = array('status'=>'Success');
        //echo base64_encode($inputJSON);
    }

    public function loginValidation($post){

        // Check how many login attempts have been made.
        $this->load->model('account/customer');
        $this->load->model('opsapi/account');




        if (!$this->error) {
            $this->user = $this->model_opsapi_account->login($post['username'], $post['password']);

           if (count($this->user) > 0) {

                $this->access_token = $this->model_opsapi_account->setOpsAccessToken($this->user['user_id'], $post['username']);


            } else {

                $this->error['warning'] = $this->language->get('error_login');


            }
        }
        return !$this->error;
    }
}