<?php
class Restapi {

    ///////////////////////// Private /////////////////////////////
    private $data = array();
    private $header = array();

    private function _transformRequest($request) {
        if (!isset($request['app_version_code'])) {
            $request['app_version_code'] = 0;
        } else {
          $request['app_version_code'] = (int)$request['app_version_code'];
        }

        return $request;
    }

    /**
     * Function : manage_error_reporting
     * @author Rahul 11th June 2018
     * Output : if header disable_error_reporting is not empty then error_repoting set 0
     * */
    private function manage_error_reporting() {
        $headers = getallheaders();
            if(!empty($headers['disable_error_reporting'])) {
              error_reporting(0);
            }
        return true;
    }

    private function checkUserByAccessToken($access_token, $user_id){
        
        $access_token = trim($access_token);
        // If empty access token (meaning, invalid token), return 0 (false)
        if ( empty($access_token) ) 
            return 0;

        $sql = "SELECT ws_access_token 
                FROM " . DB_PREFIX . "customer 
                WHERE customer_id = '" . (int)$user_id . "'";
        $query = $this->db->query($sql);
        return (int)(($query->row['ws_access_token'] ?? '') === $access_token);
    }


    ///////////////////////// Public /////////////////////////////

    public function __construct($registry) {
        $this->registry  = $registry;

        if (method_exists($registry, 'get')) {
            $this->config  = $registry->get('config');
            $this->db      = $registry->get('db');
            $this->load    = $registry->get('load');
            // $this->request = $registry->get('request');
            // $this->session = $registry->get('session');
        }else{
            $this->config  = $registry->config;
            $this->db      = $registry->db;
            $this->load    = $registry->load;
            // $this->request = $registry->request;
            // $this->session = $registry->session;
        }
    }

    public function getRequestData(){
        return $this->data;
    }

    public function setRequestData($request){

        $request = $this->_transformRequest($request);
        $this->data = $request;
    }

    public function getRequestHeader(){
        return $this->header;
    }

    public function setRequestHeader($header){
        $this->header = $header;
    }

    /**
     * @info: Public method to validate rest APIs calling is valid or not
     * @param: array $req
     * @return: array $res
     * @author: Nishu, 28th June 2019
    */
    public function validateRestApiAccess(array $request) : array {
        ini_set('display_errors',1);
        error_reporting(E_ALL);
        $this->manage_error_reporting();

        $res = array();

        if ($this->config->get('config_app_maintenance') == 1 ) {
            $res['statusCode'] = '8888';
            $res['data']       = array();
            $res['message']   = 'Hey!! Engineers @ work!!. We will be back shortly. C Ya';
            
        }elseif(isset($request['access_token']) && isset($request['user_id']) ){

            $access_token       = $request['access_token'];
            $user_id            = $request['user_id'];

            $this->load->model('restapi/service');
            $check_access_token = $this->checkUserByAccessToken($access_token, $user_id);
            
            if($check_access_token > 0){
                //Valid Access Token
                $res['statusCode'] = '200';
                $res['data']       = array();
                $res['message']    = 'Valid Access Token.';
            } else {
                //Invalid access token 
                $res['statusCode'] = '1003';
                $res['data']       = array();
                $res['message']    = 'Invalid Access Token.';
            }
            
        } else {
            //check code for data packet
            $res['statusCode'] = '1003';
            $res['data']       = array();
            $res['message']    = 'Invalid Access Token Or User Id.';
        }
        return $res;
    }




}
