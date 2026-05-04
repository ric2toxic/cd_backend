<?php

class ControllerNotificationNotification extends Controller
{
    /***
    * Get Customers and send notification them on mobile app
    ***/
    public function notification() {

      //$this->load->language('common/menu');
      $data = array(); // Initializing the data array to be passed on to template files
      // Autoloading the lanugage
      $this->load->autoLoadLanguage('common/menu', $data);
    
      $this->load->model('notification/notification');
      $url = '';

      $data['heading_title'] = $this->language->get('text_notification');
     
      $data['cancel'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'] . $url, 'SSL');

      $data['breadcrumbs'] = array();

      $data['breadcrumbs'][] = array(
        'text' => $data['text_home'],
        'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
      );

      $data['breadcrumbs'][] = array(
        'text' => $this->language->get('notification'),
        'href' => $this->url->link('notification/notification/notification', 'token=' . $this->session->data['token'] . $url, 'SSL')
      );

      $data['default_image'] = '/image/no_image.png';
      $data['default_image_width'] = $this->config->get('config_image_category_width');
      $data['default_image_height'] = $this->config->get('config_image_category_height');
      $this->document->setTitle($data['text_notification']);

      $data['notification_type'] = 1;

      // Here we send a notification to spacific customer's
      if(!empty($this->request->post['customers']) ) {
        
        $sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer_name, c.telephone as customer_mobile 
                FROM ".DB_PREFIX."customer AS c 
                WHERE c.ws_gcm_registration_id != '' 
                AND c.app_version > 24 ";
        if( !empty($this->request->post['customers']) ) {
            $sql .= " AND c.customer_id IN ( ".$this->request->post['customers']." )"; 
        }
        
        $sql .= " ORDER BY c.customer_id ASC";
        
        $data['customers'] = $this->db->query($sql)->rows;
      }
      $data['header'] = $this->load->controller('common/header');
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['footer'] = $this->load->controller('common/footer');

      $data['message_type'] = $this->model_notification_notification->getNotificationMessageType();

      //  echo "<pre>"; print_r($data['message_type']); die;

      $this->response->setOutput($this->load->view('common/customer_notification.tpl', $data));
    }

    public function sendPushNotificationToCustomers(){

        $this->load->model('notification/notification');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        if(isset($this->request->post['message_type'])){
            $data['message_type'] = $this->request->post['message_type'];
        }

        if(isset($this->request->post['URL'])){
            $data['url'] = $this->request->post['URL'];
        }

        if(isset($this->request->post['notification_img_path'])){
            $data['notification_img_path'] = $this->request->post['notification_img_path'];
        }

        if(isset($this->request->post['category_id']) && !empty($this->request->post['category_id'])){
            $data['category_id'] = $this->request->post['category_id'];
            $data['category_parent_id'] = $this->model_notification_notification->check_category_type($data['category_id']);
            foreach ($data['category_parent_id'] as $value) {
                $parent_id = $value['parent_id'];
            }
            $data['category_type_multi'] = $this->model_notification_notification->check_category_type_multi($data['category_id']);
            
            foreach ($data['category_type_multi'] as $value) {
                $category_type_multi = $value['parent_id'];
            }
            //print_r($data['category_type_multi']);
            if(isset($parent_id)){
                if($parent_id == 0){
                    if(isset($category_type_multi)){
                        $data['category_id_type'] = 'Multi';
                    }else{
                        $data['category_id_type'] = 'Single';
                    }
                }else{
                    $data['category_id_type'] = 'Multi';
                }
            }else{
                $data['category_id_type'] = '';
            }
        }else{
            $data['category_id'] = 0;
            $data['category_id_type'] = '';
        }

        if(isset($this->request->post['message'])){
            $data['message'] = $this->request->post['message'];
        }

        if (isset($this->request->post['search_url'])) {
            $data['search_url'] = $this->request->post['search_url'];        
        }
        $url = parse_url($data['search_url']); 
        
        if (isset($url['path'])) {
            $path = explode('/', $url['path']);
            $new_path = array_pop($path);
            $cat  = $this->db->query("SELECT query FROM ".DB_PREFIX."url_alias WHERE keyword='".$new_path ."'" );
            if ($cat->num_rows) {
            // echo "<pre>"; print_r(explode('=', $cat->row['query'])[0]); die;
            if (explode('=', $cat->row['query'])[0] == 'category_id') {
                $data['category_id'] = explode('=', $cat->row['query'])[1];
            } elseif(explode('=', $cat->row['query'])[0] == 'product_id'){
                $data['product_id'] = explode('=', $cat->row['query'])[1];
            }

            }
        }
        
        if (isset($url['query'])) {
            $query_arr = explode('&amp;', $url['query']);        
        } elseif (isset($url['fragment'])) {
            $query_arr = explode('&amp;', substr($url['fragment'], 1));    
            $set_filter = true;            
        }
        
        $inner_query_arr = [];
        $i = 0;
        
        if(!empty($query_arr) && isset($query_arr)){
            foreach ($query_arr as $value) {
                // echo $value ."<br>";
                $arr = explode('=', $value);
                if ($arr[0] == 'filter') {
                    // echo "string"; die;
                    $inner_query_arr['filter'] =$arr[1] ;
                }
                if ($arr[0] == 'rating_filter') {
                    switch ($arr[1]) {
                        case 1:
                            if (isset($inner_query_arr['filter'])) {
                            $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20001";
                            }else { 
                                $inner_query_arr['filter_options'] = "20001";
                            }

                        break;
                        case 2:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20002";
                            }  else {
                                $inner_query_arr['filter_options'] =  "20002";
                            }              
                        break;
                        case 3:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20003";
                            } else {
                                $inner_query_arr['filter_options'] = "20003";
                            }                
                        break;
                        case 4:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20004";
                            } else {
                                $inner_query_arr['filter_options'] =  "20004";
                            }               
                        break;
                        case 5:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20005";
                            } else {
                                $inner_query_arr['filter_options'] = "20005";
                            }                
                        break; 
                        default:
                            if (isset($inner_query_arr['filter'])) {
                                $inner_query_arr['filter_options'] = $inner_query_arr['filter'] . "," . "20000";
                            } else {
                                $inner_query_arr['filter_options'] =  "20000";
                            }                
                        break;
                    }   
                }if ($arr[0] == 'sort') {
                    $inner_temp = $arr[1];
                }
                if ($arr[0] == 'order') {
                    if ($arr[1] == 'DESC') {
                        if (isset($inner_temp)) {
                            if ($inner_temp == 'p.date_added') {
                                $inner_query_arr['sort_options'] = 'latest_designs';
                            } elseif ($inner_temp == 'p.selling_price') {
                                $inner_query_arr['sort_options'] = 'price_high_to_low';
                            } 
                        }
                    } else {
                        if (isset($inner_temp)) {
                            if ($inner_temp == 'p.selling_price') {
                                $inner_query_arr['sort_options'] = 'price_low_to_high';
                            } 
                        }
                    }
                } if ($arr[0] == 'search') {
                    $inner_query_arr['search_term'] = $arr[1];
                } if ($arr[0] == 'category_id') {
                    $data['category_id'] = $arr[1];
                } if ($arr[0] == 'price_filter') {
                    $data['price_filter'] = $arr[1];
                }

                $i++;
            }
        }

        $customers = array();
        $cids = array();
        $this->load->model('notification/notification');

        if (!empty($this->request->post['customers'])) {
            $send_notification_customers_ids = implode(',', $this->request->post['customers']);
        }else{
            $send_notification_customers_ids = '';
        }

        // get All customers gcm_id
        $customers_gsm_arr = $this->model_notification_notification->getCustomerGcmId($send_notification_customers_ids);
        $customers_gcm = array_column($customers_gsm_arr, 'ws_gcm_registration_id');

        if(isset($this->request->post['notification_type'])){
            $notification_type = $this->request->post['notification_type'];
        }else{
            $data['notification_type'] = 0;
            $notification_type = 0;
        }

        if($notification_type == 1){
            $data['notes'] = 'Live';
        }else{
            $data['notes'] = 'Preview';
        }
        $notf_id = $this->model_notification_notification->sendNotification($data);

        $msg = array
        (
            'URL'             => $data['url'],
            'category_id'     => isset($data['category_id'])?$data['category_id']:'',
            'product_id'      => isset($data['product_id'])?$data['product_id']:'',
            'should_download' => '',
            //'image'         => $data['image_path'],
            'image'           => $data['notification_img_path'],
            'category_type'   => $data['category_id_type'],
            'notification_id' => $notf_id,
            'filter_arr'      => $inner_query_arr,
            'title'           => '',
            'subtitle'        => '',
            'tickerText'      => '',
            'vibrate'         => 1,
            'sound'           => 1
        );
        if($data['message_type'] != 4){
            $msg['message'] = $data['message'];
        }else{
            $msg['message_2'] = $data['message'];
        }

        if ($notification_type == 1) {
            $chunk_customers_gcm = array_chunk($customers_gcm, 500);

            foreach($chunk_customers_gcm as $cids){
                $notification = New Notification($cids, array_merge($msg ,array('msg_type' => $data['message_type'])));
                $notification->sendPushNotification();
            }
        }else{
            
            $arr_user_ids = array(50, 4042, 12163, 198, 12766, 22588);
            $cids = $this->model_notification_notification->getGCMById($arr_user_ids);

            $notification = New Notification($cids,array_merge($msg ,array('msg_type'  =>  $data['message_type'])));
            $notification->sendPushNotification(false);
        }
        
        $this->response->redirect($this->url->link('notification/notification/notification', 'token=' . $this->session->data['token']  , 'SSL'));
    }

    public function autocomplete() {
       $this->load->model('notification/notification');
      $json = array();

      if (isset($this->request->get['category_name'])) {
        
        if (isset($this->request->get['category_name'])) {
          $category_name = $this->request->get['category_name'];
        } else {
          $category_name = '';
        }

        if (isset($this->request->get['limit'])) {
          $limit = $this->request->get['limit'];
        } else {
          $limit = 30;
        }

        $filter_data = array(
          'category_name'  => $category_name,
          'start'        => 0,
          'limit'        => $limit
        );

        $results = $this->model_notification_notification->getCategory($filter_data);
        foreach ($results as $result) {
         // $option_data = array();
          $json[] = array(
            'category_id' => $result['category_id'],
            'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
          );
        }
      }
      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode($json));
    }

    // call by ajax for upload image
    public function uploadImage(){

        $data['image_path'] = '';

        if(isset($_FILES['image'])){
            $uploads = DIR_IMAGE.'notification';
            $tmp_name = $_FILES['image']['tmp_name'];
            $name = $_FILES['image']['name'];
            $filepath = $uploads."/".$name;
            if(move_uploaded_file($tmp_name, $filepath)){
                $data['image_path'] = HTTPS_CATALOG."image/notification/".$name;
            }
            $data['image'] = $name;
        }
        echo $data['image_path'];
    }


}
