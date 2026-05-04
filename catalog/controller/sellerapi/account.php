<?php
/**
*
*/
class ControllerSellerapiAccount extends Controller
{

    public $access_token = '';
    public $error = array();
    public $seller_data = array();

    public function index(){

        $a = 'eyJtZXRob2RfbmFtZSI6ImluZGV4LnBocD9yb3V0ZT1zZWxsZXJhcGkvYWNjb3VudC9sb2dpbiIsImRhdGEiOnsiZW1haWwiOiJmZGZkZiIsInBhc3N3b3JkIjoiZHNmZGZkIn19';
        echo base64_decode($a);
    }

    public function login(){
        //Set headers for CROSS domain policy
        $this->load->model('sellerapi/global');
        $this->model_sellerapi_global->headers();


        if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
            $requestBody = $_POST;

            if(!$this->loginValidation($requestBody)){
                if(isset($this->error['warning'])){
                    $response['status'] = 'Failure';
                    $response['error'] = $this->error['warning'];
                    $response['access_token'] = '';

                }
            }else{
                $this->load->model('sellerapi/account');

                //$sellerInfo = $this->model_sellerapi_account->getSellerInfo($this->customer->getId);
                //print_r($sellerInfo);
                $response['status'] = 'Success';
                $response['access_token'] = $this->access_token;
                $response['user_info']['company'] = $this->seller_data['company'];
                $response['user_info']['seller_id'] = $this->seller_data['seller_id'];
                $response['user_info']['email'] = $this->seller_data['email'];
                $response['user_info']['telephone'] = $this->seller_data['telephone'];

            }


           echo json_encode($response);



        }
        $response = array('status'=>'Success');
        //echo base64_encode($inputJSON);
    }

    public function loginValidation($post){

        // Check how many login attempts have been made.
        $this->load->model('account/customer');
        $this->load->model('sellerapi/account');
        $login_info = $this->model_account_customer->getLoginAttempts($post['username']);

        if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->error['warning'] = $this->language->get('error_attempts');
        }


        if (!$this->error) {
            $this->seller_data = $this->model_sellerapi_account->login($post['username'], $post['password']);
           if (count($this->seller_data) > 0) {

                $this->access_token = $this->model_sellerapi_account->setSellerAccessToken($this->seller_data['seller_id'], $post['username']);
                $this->model_account_customer->deleteLoginAttempts($post['username']);

                $this->event->trigger('post.customer.login');

            } else {

                $this->error['warning'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($post['username']);
            }
        }
        return !$this->error;
    }
}