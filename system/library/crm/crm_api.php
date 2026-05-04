<?php 
class CrmApi {

    public function sendNotifications($data){
        if(!empty($data)){
            $data_string = json_encode($data);
            $url = 'https://www.wholesalebox.biz/crmapi/Notifications/sendNotificationFromWeb';
            // Set some options - we are passing in a useragent too here
            $ch = curl_init($url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );     
            // Send the request & save response to $resp
            $resp = curl_exec($ch);
            print_r($resp);
        }
    }
}
