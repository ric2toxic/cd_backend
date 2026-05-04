<?php
     require_once('system.php');
     require_once( DIR_SYSTEM . 'library/operations/orders/order_pickup.php' );
     require_once( DIR_SYSTEM . 'library/user.php' );

    class PickupController extends SystemController
    {
        private $error = array();
        protected $helpline = array();

        public function __construct($params) {

            parent::__construct($params);

            $user = new User($this->registry);
            $this->registry->set('user', $user);
        }
        /**
         * customer login
         */

  public function login()
  {    
    $headers   = getallheaders();
    $result    = array();

    if($headers['SIGNATURE'] == 'SIGNATURE' && $headers['REQUEST_BY'] == 'ANDROID APP') 
    { 
      $inputJSON = file_get_contents('php://input');
      $request   = json_decode( $inputJSON, TRUE );

       $this->load->model('pickers/pickers');

       if(isset($request['device_id']))
       { 
          $pickers = $this->model_pickers_pickers->login($request['username'], $request['password'], $request['device_id']);
          
          //checkl admin login
          if($pickers == 0 || $pickers == 2)
          {
            $admin_login = $this->user->login($request['username'], $request['password']);

             if($admin_login)
             {
               $pickers = array();
               $pickers['id']               = $this->user->getId();
               $pickers['first_name']       = $this->user->getFirstname();
               $pickers['last_name']        = $this->user->getLastname();
               $pickers['email']            = $this->user->getEmail();
               $pickers['ws_access_token']  = $this->user->getAccessToken();
               $pickers['pickup_city_code'] = $this->user->getBranchCode();
               $pickers['device_id']        =  $this->user->getDeviceId();

               if($pickers['device_id'] == '')
               {
                 $this->user->update_device_id($pickers['id'], $request['device_id']);
               }
               else
               {
                 if($pickers['device_id'] != $request['device_id'])
                 {
                   $pickers = 0;
                 }
               }
             }
            $role = 'admin';
          }
          else
          {
            $role = 'pickup';
          }  

           if($this->method == 'POST' && $pickers == 0)
           {
             $this->message      = 'Incorrect username and password';
             $this->statusCode   =  '1004';
             $result = null;
           }
           else if($this->method == 'POST' && $pickers == 2)
           {
             $this->message      = 'Incorrect device id';
             $this->statusCode   =  '1004';
             $result = null;
           }
           else
           {
             $pickers['role'] = $role;
             if($pickers['id'] == 52)
             {  
              $access_token = $pickers['ws_access_token'];  
              $rs           = 1;
             }
             else{
               $access_token = $this->generate_access_token();
               $rs = $this->model_pickers_pickers->VerifyToken($pickers['id'],$access_token,$request['device_id'], $pickers['role']);
             }

             if($rs == 1)
               {
                 $result = $pickers; 
                 $result['ws_access_token'] = $access_token;
                 $result['device_id']       = $request['device_id'];
                 $this->statusCode          =  '200';
                 $this->message             = 'data fatch successfully';
               }
             else
               {
                  $this->message    = 'Internal error';
                  $this->statusCode =  '1003';
                  $result = null;
               }
           }
        }
        else
        {
          $this->message    = 'http request does not have device id.';
          $this->statusCode =  '1005';
          $result = null;
        }   

      }
      else
      {
        $this->message    =   'http request headers not found.';
        $this->statusCode =   '004';
        $result = null;
      }
        
        $this->data         = $result;
        return $this->__response(); 

    }


  public function picked_up()
  {    
      $headers   = getallheaders();
      $result    = array();

    if($headers['SIGNATURE'] == 'SIGNATURE' && $headers['REQUEST_BY'] == 'ANDROID APP') 
    { 
      $inputJSON = file_get_contents('php://input');
      $request   = json_decode( $inputJSON, TRUE );
      
       if ($_SERVER['HTTPS']) {
          $static_content_url =  STATIC_CONTENT_URL_SSL;
         } else {
          $static_content_url =  STATIC_CONTENT_URL ;
        }
      
     if(isset($request['access_token']))
     {
       $this->load->model('pickers/pickers');
       $this->load->model('user/user');
       $this->load->model('tool/image');

       $access_token = $request['access_token'];
       $pickup_id    = $request['pickup_id'];
       $version      = $request['version'];
      
      if($version > 1)
      {
        if(isset($request['device_id']))
        {
          $device_id    = $request['device_id'];
          $check_access_token = $this->model_pickers_pickers->checkUserByAccessToken($access_token,$pickup_id,$device_id);
        }
        else
        {
          $check_access_token = 0;
        }  
      }
      else
      {
        $check_access_token = $this->model_pickers_pickers->checkUserByAccessToken($access_token,$pickup_id);
      }  

       if($check_access_token)
        {
          $data = array();
          $data['data']['order_product_id']  = $request['order_product_id'];
          $data['data']['seller_id']         = $request['seller_id'];
          $data['data']['order_id']          = $request['order_id'];
          $data['data']['received_status']   = $request['pickup_status']==1 ? 'Picked_Up' : 'Issue';
          $data['user_id']                   = $check_access_token['id'];
          $data['user_name']                 = $check_access_token['first_name'].' '.$check_access_token['last_name'];
          $data['user_type']                 = 'Pickup';
 
        $pickup_order = $this->model_pickers_pickers->getPickupOrder($pickup_id, $request['order_product_id']);
          
        if($pickup_order['seller_invoice_id'] > 0 || $request['pickup_status'] == 0)
        {
            $orderPickup  = new OrderPickup($this->registry);
            $orderPickup->changePickupHistory($data);
          if($request['pickup_status'] == 0)
          {
           $body = array(); 
           $body['pickup'] = $check_access_token['first_name']." ".$check_access_token['last_name'];
           $body['pickup_number'] = $check_access_token['phone_no'];
           $body['pickup_id'] = $pickup_id;
           $body['pickup_address'] = $pickup_order['pickup_address'];
           $body['pickup_city'] = $pickup_order['pickup_city'];
           $body['pickup_pincode'] = $pickup_order['pickup_pincode'];
           $body['seller'] = $pickup_order['nickname'];
           $body['company'] = $pickup_order['company'];
           $body['seller_id'] = $pickup_order['seller_id'];
           $body['order_no'] = $pickup_order['order_no'];
           $body['suborder_id'] = $pickup_order['suborder_id'];
           $body['order_product_id'] = $request['order_product_id'];
           $body['product_model'] = $pickup_order['product_model'];
           $body['product_name'] = $pickup_order['product_name'];
           $body['product_comment'] = $pickup_order['product_comment'];
           $body['transfer_price_per_piece'] = $pickup_order['transfer_price_per_piece'];
           $body['total_set'] = $pickup_order['total_set'];
           $body['total_pieces'] = $pickup_order['total_pieces'];
           $body['total_price'] = $pickup_order['total_price'];
           $body['status'] = 0;

           $body_str = 'Pickup: '.$body['pickup'].', ';
           $body_str = $body_str.'Seller: '.$body['company'].', ';
           $body_str = $body_str.'Order Number: '.$body['order_no'].', ';
           $body_str = $body_str.'Product Name: '.$body['product_name'].', ';
           $body_str = $body_str.'Product Model: '.$body['product_model'].', ';
           $body_str = $body_str.'Comment: '.$body['product_comment'];

           $user_data = $this->model_user_user->getUsersByGroupId(23, $check_access_token['pickup_city_code']);
           
           $this->send_mail($user_data, $body);

           $body = json_encode($body);
           $api_data = array();
           $api_data['notification_sending_time']   = date("Y-m-d h:i:s");
           $api_data['type']                        = "instant"; 
           $api_data['data']                        = array(); 
           $i=1;
          foreach($user_data as $user)
          { 
           $api_data['data'][$i]['type']             = "wsb_user";
           $api_data['data'][$i]['id']               = $user['user_id'];
           $api_data['data'][$i]['is_pn_to_send']    = 0;
           $api_data['data'][$i]['is_sms_to_send']   = false;;
           $api_data['data'][$i]['is_email_to_send'] = false;
           $api_data['data'][$i]['email_to_head']    = false;
           $api_data['data'][$i]['pn_to_head']       = true;
           $api_data['data'][$i]['sms_to_head']      = false;
           $api_data['data'][$i]['browser_token']    = $user['wc_notification_access_token'];
           $api_data['data'][$i]['notification']['title']         = 'Pickup Issue';
           $api_data['data'][$i]['notification']['body']          =  $body_str.'jsonData'.$body;
           $api_data['data'][$i]['notification']['icon']          = 'view/image/Red_triangle_alert_icon.png';
           $api_data['data'][$i]['notification']['click_action']  = './index.php?route=sale/order/info&order_id='.$pickup_order['order_id'].'&suborder_id='.$pickup_order['suborder_id'];
           $i++;
          } 


          foreach($api_data['data'] as $api)
          {
             $fcm_data = array(
               "to" =>  $api['browser_token'],
               "notification" => $api['notification']
              );
             $data_string = json_encode($fcm_data);
             $headers = array
             (
                'Authorization: key=' . WEB_API_ACCESS_KEY,
           'Content-Type: application/json'
           );

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
           $result = curl_exec($ch);
           curl_close($ch);
           }

            /*$data_json = json_encode($api_data);
            $api_url = CRM_URL."crmapi/notifications/sendNotificationFromWeb";
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
            $result = json_decode($result, true );*/
          }
          $result = 'success';
          $this->statusCode =  '200';
          $this->message    =  'Moving to Picked Up list!';
         }
         else
          {
             $this->message    =  'Seller Invoice not genrated yet.';
             $this->statusCode =  '1003';
             $result = null;
          } 
        } 
       else
        {
          $this->message    =  'Invalid Access Token.';
          $this->statusCode =  '1002';
          $result = null;
        }  

     }
     else
     {
        $this->message    =   'http request does not have access token.';
        $this->statusCode =   '1001'; 
        $result = null;
     } 

    }
    else
    {
        $this->message    =   'http request headers not found.';
        $this->statusCode =   '004';
        $result = null;
    } 

      $this->data       = $result;
      return $this->__response();  

  }

  public function picked_up_revert()
  {    
      $headers   = getallheaders();
      $result    = array();

    if($headers['SIGNATURE'] == 'SIGNATURE' && $headers['REQUEST_BY'] == 'ANDROID APP') 
    { 
      $inputJSON = file_get_contents('php://input');
      $request   = json_decode( $inputJSON, TRUE );
      
       if ($_SERVER['HTTPS']) {
          $static_content_url =  STATIC_CONTENT_URL_SSL;
         } else {
          $static_content_url =  STATIC_CONTENT_URL ;
        }
      
     if(isset($request['access_token']))
     {
       $this->load->model('pickers/pickers');
       $this->load->model('user/user');
       $this->load->model('tool/image');

       $access_token = $request['access_token'];
       $pickup_id    = $request['pickup_id'];
       $version      = $request['version'];
      
      if($version > 1)
      {
        if(isset($request['device_id']))
        {
          $device_id    = $request['device_id'];
          $check_access_token = $this->model_pickers_pickers->checkUserByAccessToken($access_token,$pickup_id,$device_id);
        }
        else
        {
          $check_access_token = 0;
        }  
      }
      else
      {
        $check_access_token = $this->model_pickers_pickers->checkUserByAccessToken($access_token,$pickup_id);
      }  

       if($check_access_token)
        {
          $data = array();
          $data['data']['order_product_id']  = $request['order_product_id'];
          $data['data']['seller_id']         = $request['seller_id'];
          $data['data']['order_id']          = $request['order_id'];
          $data['data']['received_status']   = 'Not_Given';
          $data['user_id']                   = $check_access_token['id'];
          $data['user_name']                 = $check_access_token['first_name'].' '.$check_access_token['last_name'];
          $data['user_type']                 = 'Pickup';


          $orderPickup  = new OrderPickup($this->registry);
          $orderPickup->changePickupHistory($data);

           $pickup_order = $this->model_pickers_pickers->getPickupOrder($pickup_id, $request['order_product_id']);

           $body = array(); 
           $body['pickup'] = $check_access_token['first_name']." ".$check_access_token['last_name'];
           $body['seller_id'] = $request['seller_id'];
           $body['order_id'] = $request['order_id'];
           $body['order_product_id'] = $request['order_product_id'];
           $body['status'] = 2;

           $body_str = 'Pickup: '.$body['pickup'].', ';
           $body_str = $body_str.'Seller: '.$pickup_order['company'].', ';
           $body_str = $body_str.'Order Number: '.$pickup_order['order_no'].', ';
           $body_str = $body_str.'Product Name: '.$pickup_order['product_name'].', ';
           $body_str = $body_str.'Product Model: '.$pickup_order['product_model'].', ';
           $body_str = $body_str.'Comment: '.$pickup_order['product_comment'];

           $body = json_encode($body);

           $user_data = $this->model_user_user->getUsersByGroupId(23, $check_access_token['pickup_city_code']);
           $api_data = array();
           $api_data['notification_sending_time']   = date("Y-m-d h:i:s");
           $api_data['type']                        = "instant";
           $api_data['data']                        = array(); 
           $i=1;
           foreach($user_data as $user)
           { 
             $api_data['data'][$i]['type']             = "wsb_user";
             $api_data['data'][$i]['id']               = $user['user_id'];
             $api_data['data'][$i]['browser_token']    = $user['wc_notification_access_token'];
             $api_data['data'][$i]['notification']['title']         = 'Pickup Issue Revert';
             $api_data['data'][$i]['notification']['body']          =  $body_str.'jsonData'.$body;
             $api_data['data'][$i]['notification']['icon']          = 'view/image/Red_triangle_alert_icon.png';
             $api_data['data'][$i]['notification']['click_action']  = './index.php?route=sale/order/info&order_id='.$pickup_order['order_id'].'&suborder_id='.$pickup_order['suborder_id'];
             $i++;
          } 


          foreach($api_data['data'] as $api)
          {
             $fcm_data = array(
               "to" =>  $api['browser_token'],
               "notification" => $api['notification']
              );
             $data_string = json_encode($fcm_data);
             $headers = array
             (
                'Authorization: key=' . WEB_API_ACCESS_KEY,
           'Content-Type: application/json'
           );

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
           $result = curl_exec($ch);
           curl_close($ch);
           }



          $result = 'success';
          $this->statusCode =  '200';
          $this->message    =  'Moving to Pickup list!';

        } 
       else
        {
          $this->message    =  'Invalid Access Token.';
          $this->statusCode =  '1002';
          $result = null;
        }  

     }
     else
     {
        $this->message    =   'http request does not have access token.';
        $this->statusCode =   '1001'; 
        $result = null;
     } 

    }
    else
    {
        $this->message    =   'http request headers not found.';
        $this->statusCode =   '004';
        $result = null;
    } 

      $this->data       = $result;
      return $this->__response();  

  }


  public function pickup_list()
  {    
      $headers   = getallheaders();
      $result    = array();

    if($headers['SIGNATURE'] == 'SIGNATURE' && $headers['REQUEST_BY'] == 'ANDROID APP') 
    { 
      $inputJSON = file_get_contents('php://input');
      $request   = json_decode( $inputJSON, TRUE );
      
       if ($_SERVER['HTTPS']) {
          $static_content_url =  STATIC_CONTENT_URL_SSL;
         } else {
          $static_content_url =  STATIC_CONTENT_URL ;
        }
      
     if(isset($request['access_token']))
     {
       $this->load->model('pickers/pickers');
       $this->load->model('tool/image');

       $access_token  = $request['access_token'];
       $pickup_id     = $request['pickup_id'];
       $version       = $request['version'];
       $pickup_status = $request['pickup_status'];
      
      if($version > 1)
      {
        if(isset($request['device_id']))
        {
          $device_id    = $request['device_id'];
          $check_access_token = $this->model_pickers_pickers->checkUserByAccessToken($access_token,$pickup_id,$device_id);
        }
        else
        {
          $check_access_token = 0;
        }  
      }
      else
      {
        $check_access_token = $this->model_pickers_pickers->checkUserByAccessToken($access_token,$pickup_id);
      }  

       if($check_access_token)
        {
          $city_code = $check_access_token['pickup_city_code'];
          $this->helpline = $this->city_seller_helpline[$city_code];
          
          $pickup_list        = $this->model_pickers_pickers->getPickupList($pickup_id, $pickup_status);
          $assign_pickup_list = $this->model_pickers_pickers->getPickupAssignList($pickup_id, $pickup_status);
          if(count($assign_pickup_list) > 0) { $pickup_list = array_merge($pickup_list, $assign_pickup_list); }

            $i=1;
          if(count($pickup_list) > 0)
          {  
           foreach($pickup_list as $pickup)
           {

             $result['seller'][$pickup['seller_id']]['seller_id'] = $pickup['seller_id'];
             $result['seller'][$pickup['seller_id']]['company']   = $pickup['company'];
             $result['seller'][$pickup['seller_id']]['nickname']  = $pickup['nickname'];
             $result['seller'][$pickup['seller_id']]['primary_contact_name']= $pickup['primary_contact_name'];
             $result['seller'][$pickup['seller_id']]['primary_contact_no']  = $pickup['primary_contact_no'];
             $result['seller'][$pickup['seller_id']]['pickup_holder_name']  = $pickup['pickup_holder_name'];
             $result['seller'][$pickup['seller_id']]['pickup_contact_no']   = $pickup['pickup_contact_no'];
             $result['seller'][$pickup['seller_id']]['pickup_address']      = $pickup['pickup_address'];
             $result['seller'][$pickup['seller_id']]['pickup_city']         = $pickup['pickup_city'];
             $result['seller'][$pickup['seller_id']]['pickup_pincode']      = $pickup['pickup_pincode'];
             $result['seller'][$pickup['seller_id']]['pickup_zone_id']      = $pickup['pickup_zone_id'];
             $result['seller'][$pickup['seller_id']]['pickup_country_id']   = $pickup['pickup_country_id'];
             //$result['seller'][$pickup['seller_id']]['currency_code']       = $pickup['currency_code'];
             if(isset($result['seller'][$pickup['seller_id']]['seller_total']))
             {
               $result['seller'][$pickup['seller_id']]['seller_total'] += $pickup['total_price'];
             }
             else
             {
               $result['seller'][$pickup['seller_id']]['seller_total'] = $pickup['total_price'];
             } 

             if(isset($result['seller'][$pickup['seller_id']]['seller_total_pieces']))
             {
               $result['seller'][$pickup['seller_id']]['seller_total_pieces'] += $pickup['total_pieces'];
             }
             else
             {
               $result['seller'][$pickup['seller_id']]['seller_total_pieces'] = $pickup['total_pieces'];
             }                         
 
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_id']  = $pickup['order_id'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_no']  = $pickup['order_no'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['date_added']= $pickup['date_added'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['suborder_id']= $pickup['suborder_id'];

              $seller_invoice = $this->model_pickers_pickers->getSellerInvoice($pickup['product_invoice_id']);

             if(isset($seller_invoice['seller_invoice_id']) && !empty($seller_invoice['seller_invoice_id']))
             {
              $pickup['seller_invoice_id']           = $seller_invoice['seller_invoice_id'];
              $pickup['seller_invoice_no']           = $seller_invoice['seller_invoice_no'];
              $pickup['invoice_physically_received'] = $seller_invoice['invoice_physically_received'];
              $pickup['invoice_image']               = ($seller_invoice['invoice_image'] != '') ? $seller_invoice['invoice_image'] : '';
             }
             else
             {
              $pickup['seller_invoice_id']           = null;
              $pickup['seller_invoice_no']           = null;
              $pickup['invoice_physically_received'] = null;
              $pickup['invoice_image']               = null;
             } 

             if (!isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice']['invoice_id']) || !in_array($pickup['seller_invoice_id'], $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice']['invoice_id']))
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['invoice_id'] = $pickup['seller_invoice_id'];
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['invoice_no'] = $pickup['seller_invoice_no'];
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['invoice_received'] = $pickup['invoice_physically_received'];
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['invoice_image'] = $pickup['invoice_image'];
              
              if(isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['total_pieces']))
              {
                $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['total_pieces'] += $pickup['total_pieces'];
              }
              else
              {
                 $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['total_pieces'] = $pickup['total_pieces'];
              } 

              if(isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['order_total']))
              {
                $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['order_total'] += $pickup['total_price'];
              }
              else
              {
                 $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['invoice'][$pickup['seller_invoice_id']]['order_total'] = $pickup['total_price'];
              }  


             }
       
            
             if(isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total']))
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total'] += $pickup['total_price'];
             }
             else
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total'] = $pickup['total_price'];
             }
         
             if(isset($result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total_pieces']))
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total_pieces'] += $pickup['total_pieces'];
             }
             else
             {
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['order_total_pieces'] = $pickup['total_pieces'];
             }            
             
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['product_id']  = $pickup['product_id'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['order_product_id']  = $pickup['order_product_id'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['name']  = $pickup['product_name'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['model']  = $pickup['product_model'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['pieces']  = $pickup['total_pieces'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['total']  = $pickup['total_price'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['total_set']  = $pickup['total_set'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['transfer_price_per_piece']  = $pickup['transfer_price_per_piece'];
             $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['product_comment']  = $pickup['product_comment'];
            $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['pickup_status']  = $pickup['pickup_status'];
            $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['seller_invoice_id']  = $pickup['product_invoice_id'];

              $dimensions = unserialize($pickup['product_image_dimensions']);
              if(!empty($dimensions['width'])) { $width_orig = $dimensions['width']; }

              if(!empty($dimensions['height'])) { $height_orig = $dimensions['height']; }


              if (!empty($width_orig) && !empty($height_orig) && ($width_orig / $height_orig) > 1)
                  {
                   $image =  $this->model_tool_image->resize($pickup['product_image'], $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );
                 }
               else
                 {
                 	$image = $this->model_tool_image->resize($pickup['product_image'], $this->config->get('config_image_product_width') , $this->config->get('config_image_product_height') );
                 }   
          
               $result['seller'][$pickup['seller_id']]['orders'][$pickup['order_id']]['products'][$pickup['order_product_id']]['image']  = $image;

            $i++;
           }
         }
        else
           {
             $result = null;
           } 
           
          $this->statusCode =  '200';
          $this->message    =  'data fatch successfully';
        } 
       else
        {
          $this->message    =  'Invalid Access Token.';
          $this->statusCode =  '1002';
          $result = null;
        }  

     }
     else
     {
        $this->message    =   'http request does not have access token.';
        $this->statusCode =   '1001'; 
        $result = null;
     } 

    }
    else
    {
        $this->message    =   'http request headers not found.';
        $this->statusCode =   '004';
        $result = null;
    } 

      $this->data       = $result;
      return $this->__response();  

  }


  public function admin_pickup_list()
  {    
      $headers   = getallheaders();
      $result    = array();

    if($headers['SIGNATURE'] == 'SIGNATURE' && $headers['REQUEST_BY'] == 'ANDROID APP') 
    { 
      $inputJSON = file_get_contents('php://input');
      $request   = json_decode( $inputJSON, TRUE );
      
       if ($_SERVER['HTTPS']) {
          $static_content_url =  STATIC_CONTENT_URL_SSL;
         } else {
          $static_content_url =  STATIC_CONTENT_URL ;
        }
      
     if(isset($request['access_token']))
     {
       $this->load->model('pickers/pickers');
       $this->load->model('tool/image');

       $access_token = $request['access_token'];
       $pickup_id    = $request['pickup_id'];
       $version      = $request['version'];
      
        if(isset($request['device_id']))
        {
          $device_id    = $request['device_id'];
          $check_access_token = $this->model_pickers_pickers->checkAdminUserByAccessToken($access_token,$pickup_id,$device_id);
        }
        else
        {
          $check_access_token = 0;
        }    

     if($check_access_token)
      {  
         $check_access_token['role'] = 'admin';
         $city_code = $check_access_token['pickup_city_code'];
         if($city_code == 'JP') 
         {
          $check_access_token['city'] = 'Jaipur';
         }
         else if($city_code == 'ST') 
         {
          $check_access_token['city'] = 'Surat';
         }
         else if($city_code == 'MU') 
         {
          $check_access_token['city'] = 'Mumbai';
         }
         else
         {
          $check_access_token['city'] = $city_code;
         }


         $result = $check_access_token;
         $result['pickup_data'] = $this->model_pickers_pickers->getAllPickerInfo($city_code);
         $result['unassign_seller'] = $this->model_pickers_pickers->getUnassignSeller();
         $this->statusCode =  '200';
         $this->message    =  'data fatch successfully';
      }
      else
      {
          $this->message    =  'Invalid Access Token.';
          $this->statusCode =  '1002';
          $result = null;
      }   

    }
    else
    {
        $this->message    =   'http request does not have access token.';
        $this->statusCode =   '1001'; 
        $result = null;
    }

   }
   else
   {
        $this->message    =   'http request headers not found.';
        $this->statusCode =   '004';
        $result = null;    
   }  
      
      $this->data       = $result;
      return $this->__response();  

  }


  public function upload_invoice()
  {
      $headers   = getallheaders();
      $result    = array();

    if($headers['SIGNATURE'] == 'SIGNATURE' && $headers['REQUEST_BY'] == 'ANDROID APP') 
    { 
      //$inputJSON = file_get_contents('php://input');
      //$request   = json_decode( $inputJSON, TRUE );
      $request   = $this->request;
      
       if ($_SERVER['HTTPS']) {
          $static_content_url =  STATIC_CONTENT_URL_SSL;
         } else {
          $static_content_url =  STATIC_CONTENT_URL ;
        }
      
     if(isset($request['access_token']))
     {
       $this->load->model('pickers/pickers');
       $this->load->model('tool/image');

       $access_token  = $request['access_token'];
       $pickup_id     = $request['pickup_id'];
       $version       = $request['version'];
       $invoice_id    = $request['invoice_id'];
       $suborder_id   = $request['suborder_id'];
      
      if($version > 1)
      {
        if(isset($request['device_id']))
        {
          $device_id    = $request['device_id'];
          $check_access_token = $this->model_pickers_pickers->checkUserByAccessToken($access_token,$pickup_id,$device_id);
        }
        else
        {
          $check_access_token = 0;
        }  
      }
      else
      {
        $check_access_token = $this->model_pickers_pickers->checkUserByAccessToken($access_token,$pickup_id);
      }  

       if($check_access_token)
        {
          if(isset($_FILES['invoice']['tmp_name']) && !empty($_FILES['invoice']['tmp_name'])){
             
            $file  = $_FILES['invoice'];
            $image_name = $this->model_pickers_pickers->uploadImgUsingCurl($file,$invoice_id,$suborder_id);
            
            $invoice_path     = $image_name;
            $result           = $invoice_path;
            $this->statusCode =  '200';
            $this->message    =  'Invoice uploaded successfully';
             
            }else{
              $this->message    =  'Please select a file.';
              $this->statusCode =  '1002';
              $result           = null;
            }
        } 
       else
        {
          $this->message    =  'Invalid Access Token.';
          $this->statusCode =  '1002';
          $result = null;
        }  

     }
     else
     {
        $this->message    =   'http request does not have access token.';
        $this->statusCode =   '1001'; 
        $result = null;
     } 

    }
    else
    {
        $this->message    =   'http request headers not found.';
        $this->statusCode =   '004';
        $result = null;
    } 

      $this->data       = $result;
      return $this->__response();     
  }

          /***
   * @function generate_access_token
   * */
  public function generate_access_token(){
    $length = 16;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }


  private function send_mail($user_data, $body)
  {                                                                                                               $subject = 'Issue marked by '.$body['pickup'].' for  Order Number: '.$body['order_no'];
    foreach($user_data as $user)
    {
      $email   = $user['email'];
      $message = 'Hi ,'.$user['firstname'].' '.$user['lastname'];
      $message = $message.'<br /><br /><br />';
      $message = $message.$body['pickup'].' has marked an ISSUE. Please find below detail.';
      $message = $message.'<br /><br /><br />';
      $message = $message.'<b>Pick up person:</b> '.$body['pickup'];
      $message = $message.'<br /><br />';
      $message = $message.'<b>Phone number:</b> '.$body['pickup_number'];
      $message = $message.'<br /><br />';
      $message = $message.'<b>Order No:</b> '.$body['order_no'];
      $message = $message.'<br /><br />';
      $message = $message.'<b>Seller Name:</b> '.$body['company'];
      $message = $message.'<br /><br />';
      $message = $message.'<b>Product Name:</b> '.$body['product_name'];
      $message = $message.'<br /><br />';
      $message = $message.'<b>Product Model:</b> '.$body['product_model'];

      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->Host = $this->config->get('config_mail_smtp_hostname');
      $mail->Port = $this->config->get('config_mail_smtp_port');
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPDebug = 0;
      $mail->Debugoutput = 'html';
      $mail->SMTPAuth = true;
      $mail->Username = $this->config->get('config_mail_smtp_username');
      $mail->Password = $this->config->get('config_mail_smtp_password');
      $mail->setFrom($this->config->get('config_email'), 'WholesaleBox');
      $mail->addAddress($email, "WholesaleBox");
      $mail->Subject =  html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
      $mail->msgHTML($message);
      $mail->send();
     } 
  }


   public function create_payload_txt($api,$headers,$request,$response)
    {
        $txt = "API: ".$api."  ".date("d-m-Y h:i:s").""; 
        $myfile = file_put_contents('pickup_payload.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        $txt = "Headers\n".$headers."";
        $myfile = file_put_contents('pickup_payload.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        $txt = "Request\n".$request."";
        $myfile = file_put_contents('pickup_payload.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        $txt = "Response\n".$response."\n\n\n";
        $myfile = file_put_contents('pickup_payload.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
    } 

        private function __response(){
           // response function

           $response = array();
           $response['statusCode'] = $this->statusCode;
           $response['message']    = $this->message;
           $response['data']       = $this->data;
           if(count($this->helpline))
           {
              $response['helpline']= $this->helpline; 
           }
           return $response; 
        }

    }