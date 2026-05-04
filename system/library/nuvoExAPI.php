<?php
class NuvoExAPI {
    public function __construct( ){ }

    /**
    * Public function to check Given Pincode is servicable or not by ShadowFax for reverse shipping
    * @param: $pincode
    * @return: Boolen
    * @author: Nishu, August 2017
    */
    public function checkAreaServiceableOrNot($pincode){
        // Initiate CURL Request
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, SHADOWFAX_BASE_URL."/api/v2/clients/requests/check_serviceable_pickup_pincode?pincode=".$pincode);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                          'Authorization: Token '.SHADOWFAX_TOKEN
                        ));

       // curl_setopt($ch, CURLOPT_USERPWD, NUVOEX_USER . ":" . NUVOEX_PASSWORD);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        $result = json_decode($result);
        if (strpos($result->message, "is serviceable") !== false) {
            return true;
        }else{
            return false;
        }
    }

    /**
    * Public function to Create a new order in ShadowFax shipment
    * @param: $data
    * @return: Boolen
    * @author: Nishu, August 2017
    */
    public function addShadowFaxShippment($data){
        // Initiate CURL Request
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, SHADOWFAX_BASE_URL."/api/v3/clients/requests");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                          'Authorization: Token '.SHADOWFAX_TOKEN,
                          'Content-Type: application/json'
                        ));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        $result = json_decode($result);
        
        $responseData = array();
        $responseData['aws_docket'] = '';
        if(isset($result->client_request_id)){
            $responseData['response']       = 'success';
            $responseData['response_text']  = $result;
            $responseData['aws_docket']     = $result->client_request_id;
        }else{
            $responseData['response']       = 'fail';
            if(isset($result->errors)){
                $responseData['error_text']  = $result->errors;
            }
            $responseData['response_text'] = $result;
        }
        return $responseData;
    }

    /**
    * Public function to invoke get Reverse History dynamically
    * @param: $shipment_type, $tracking_no
    * @return: array or string
    * @author: Nishu, Nov 2017
    */
    /*public function getDynamicReverseShippmentHistory($shipment_type, $tracking_no){
        if(strtolower($shipment_type) == "nuvoex"){
            $data = $this->getNuvoexShippmentHistory($tracking_no);
        }else{
            $data = $this->getShadowFaxShippmentHistory($tracking_no);
        }
        return $data;
    }*/

    /**
    * Public function to get history of NuvoEx shipment
    * @param: $tracking_no
    * @return: array or string
    * @author: Nishu, August 2017
    */
    /*public function getNuvoexShippmentHistory($tracking_no){
        // Initiate CURL Request
        $ch   = curl_init();
        $url  = NUVOEX_BASE_URL."/api/awb/history/".$tracking_no;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
       
        curl_setopt($ch, CURLOPT_USERPWD, NUVOEX_USER . ":" . NUVOEX_PASSWORD);

        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        $result = json_decode($result);
        print_r($result);
        $data = array();
        if(isset($result->status) && !($result->status)){
            $data['response'] = 'fail';
            $data['reason'] = trim($result->reason);
        }else{
            $data['response'] = 'success';
            $data['history'] = $result->history;
        }
        return $data;
    }*/

    /**
    * Public function to get history of ShadowFax shipment
    * @param: $tracking_no
    * @return: array or string
    * @author: Nishu, Nov 2017
    */
    public function getShadowFaxShippmentHistory($tracking_no){
        // Initiate CURL Request
        $ch     = curl_init();
        $url    = SHADOWFAX_BASE_URL."/api/v2/clients/requests/".$tracking_no;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                          'Authorization: Token '.SHADOWFAX_TOKEN
                        ));
       
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        $result = json_decode($result);
        $data = array();
        if(isset($result->status)){
            $data['response'] = 'success';
            $data['history']  = $result->pickup_request_state_histories;
        }else{
            $data['response'] = 'fail';
            $data['reason']   = (isset($result->responseMsg) ? $result->responseMsg : "Invalid AWS Number.");
        }
        return $data;
    }

}

?>
