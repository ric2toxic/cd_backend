<?php
include_once DIR_SYSTEM . '../rabbitmq/task_directive_constants.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

Class ControllerCronNotification extends controller {

    public function notificationLatestDesignBasedOnPreference(){

        if($this->request->get['test'] == 1) {
            $sql = "SELECT distinct cp.customer_id 
                FROM " . DB_PREFIX . "customer_preference cp
                LEFT JOIN " .DB_PREFIX. "customer c
                ON cp.customer_id = c.customer_id
                WHERE c.ws_gcm_registration_id != ''
                AND cp.customer_id IN (12163,4042,198)";
        } else {
            $sql = "SELECT distinct cp.customer_id 
                FROM " . DB_PREFIX . "customer_preference cp
                LEFT JOIN " .DB_PREFIX. "customer c
                ON cp.customer_id = c.customer_id
                WHERE c.ws_gcm_registration_id != ''
                AND  c.app_version >= '24'";
        }

        $customer_ids = $this->db->query($sql)->rows;

        $this->load->model('notification/image');
        $j = 0;
        $data = array();

        $today = Date('d_M_y');
        $dir_name = '';
        // Make a directory daily before send image notification. To save image.
        if (!file_exists(DIR_IMAGE."notification/" . $today)) {
            $dir_name = DIR_IMAGE."notification/" . $today;
            mkdir($dir_name, 0777);
        }
        $image_text = '';

        foreach ($customer_ids as $value) {
            $categories_for_latest_items = array();
            // Get caterory, price and filter according to customer_preferences.
            $sql = "SELECT category_id, filter_id, min_price, max_price FROM " . DB_PREFIX . "customer_preference WHERE customer_id = " .$value['customer_id']. " ORDER BY sent_last_preference asc";
            $customer_preference = $this->db->query($sql)->rows;

            $data[$j]['customer_id'] = $value['customer_id'];
            $catids = array_column($customer_preference, 'category_id');
            $data[$j]['category_ids'] = implode(',',$catids);
            // Create an image with the help of category id. if category come two times then image create only onces.
            // And these images were create single time for all users.
            foreach ($catids as $category_id) {
                $categories_for_latest_items[] =  $category_id;
                if(!file_exists($dir_name."/".$category_id.".png")) {

                    $sql = "SELECT p.product_id, p2c.category_id, p.image, p.date_added FROM " . DB_PREFIX . "product p
                    INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
                    WHERE p2c.category_id = '". $category_id ."' AND p.status = 1 AND p.price > 0 AND DATE(p.date_added) >= DATE_ADD(CURDATE(), INTERVAL -1 DAY) ORDER BY p.date_added DESC LIMIT 2";
                    $query = $this->db->query($sql);
                    if ($query->num_rows > 0 ) {
                        $data[$j]['categories_products'][$category_id] = $query->rows;

                        // Make an Image Collage using date added in desending order.
                        // That Means only new product image will come according to its category_id.

                        $this->model_notification_image->image($data[$j]['categories_products'][$category_id], $category_id, $image_text);
                    }
                }

            }
            // echo "<pre>"; print_r($data); echo "</pre>"; die;
            // Send images to customers according to its preferences

            $sql = "SELECT ws_gcm_registration_id FROM " . DB_PREFIX . "customer WHERE customer_id = '". $value['customer_id'] ."'";
            $ws_gcm_registration_id = $this->db->query($sql)->row;
            if(!empty($ws_gcm_registration_id) && !empty($categories_for_latest_items)){
                $ws_gcm_id = $ws_gcm_registration_id['ws_gcm_registration_id'];
                foreach ($categories_for_latest_items as $cat) {
                    $this->sendImageNotificationToCustomers($data[$j]['customer_id'], $cat, $ws_gcm_id);
                    $sql = "UPDATE " . DB_PREFIX . "customer_preference SET sent_last_preference = now() WHERE customer_id = ".$data[$j]['customer_id']." AND category_id = '".$cat."'";
                    $this->db->query($sql);
                    break;
                }
            }
            $j++;
        }
    }

    public function sendImageNotificationToCustomers($customer_id, $category_id, $ws_gcm_id){

        $this->load->model('notification/image');

        $data['message_type'] = 2; //auto send as per perference

        $data['url'] = '';
        $data['message'] = 'New Arrivals';
        $data['title'] = 'New Arrivals';

        $name       = $category_id.".png";
        $today      = Date('d_M_y');
        $data['image']  = HTTP_SERVER. "image/notification/" . $today ."/". $name;

        $sql = "INSERT INTO " . DB_PREFIX . "notification_message 
                SET message_type = '" . (int)$data['message_type'] . "', 
                url = '" . $this->db->escape($data['url']) . "', 
                category_id = '" . $this->db->escape($category_id) . "', 
                message = '" . $this->db->escape($data['message']) . "', 
                image = '" . $this->db->escape($data['image']) . "', 
                notes = 'Auto custom preference',
                send_date = NOW()";
        $this->db->query($sql);

        $notf_id = $this->db->getLastId();

        $filter_options['headline'] = 'New Arrivals';
        $filter_options['sort_options'] = 'latest_designs';

        $msg = array
        (
            'notification_id'   =>  $notf_id,
            'category_id'       =>  isset($category_id)?$category_id:'',
            'image'             =>  $data['image'],
            'msg_type'          =>  $data['message_type'],
            'url'               =>  '',
            'message'           =>  $data['message'],
            'title'             =>  $data['title'],
            'content_info'      =>   $data['title'],
            'subtitle'          =>  '',
            'tickerText'        =>  '',
            'filter_arr'        => $filter_options,
            'lights'            => 1,
            'vibrate'           => 0,
            'sound'             => 0,
            'show_instantly'    => 0 //1 or deleting this params would send PN immideatly else it would be cached by APP
        );


        $cids = array();

        $cids[] = $ws_gcm_id;
        $notification = New Notification($cids, $msg);
        $notification->sendPushNotification();
        //$this->model_notification_image->sendPushNotification($cids, $msg);
    }

   //Send PN if customer has items in cart for last 24Hrs.
    public function sendPushNotificationIfItemInCart(){   

        $this->load->model('notification/notification');                
        $customer_data =  $this->model_notification_notification->pushNotificationForCartItem();      
        $cust_gsm_id = array_column($customer_data, 'ws_gcm_registration_id');          
        $cust_id = array_column($customer_data, 'customer_id'); 
        $data['message_type']=4;                                     
        $this->load->language('cron/cron');                                  
        $message = $this->language->get('message_cart');  
        $title = $this->language->get('title_cart');                            
 
        $msg = array(  
        'msg_type'          =>  $data['message_type'],
        'message_2'         =>  $message,
        'title'             =>  $title,
        'subtitle'          =>  '',
        'tickerText'        =>  '',
        'vibrate'           =>  1,
        'sound'             =>  1);
        $notification = New Notification($cust_gsm_id, $msg); 
        $res = $notification->sendPushNotification();                                
        if(!empty($cust_id)) 
        $this-> _notifiedUpdate($cust_id);      
    }

    private function _notifiedUpdate($cust_id){          

        $customer_ids =   implode(",", $cust_id);       
          
        $this->load->model('notification/notification');               
         
        $this->model_notification_notification->updateNotifiedField($customer_ids);    
    } 

    /**
     * latestArrivalNotificationToCustomers
     * @info    Get Latest Arrival Products According Customer Preference
     * @author  Garvit
     **/
    public function latestArrivalNotificationToCustomers() {
        $start = microtime(true);
        $solr = new SolrProduct($this);
        $filter = array();
        $filter['days']                 = 3;
        $filter['is_facet']             = 1;
        $filter['facet_field']          = 'category_id';
        // Get Category By Latest Products In 3 Days
        $solr_result = $solr->getProductFromSolrOnly($filter)['result_facet_field'];
    
        $category_arr = array();
        $latest_product = array();
        foreach ($solr_result as $catgory_id => $product_count) {
            $filter = array();
            if($product_count > 0){
                $filter['select_fields']        = array('id');
                $filter['filter_category_id']   = $catgory_id;
                $filter['sort_data_by']         = 'date_added';
                $filter['order_data_by']        = 'desc';
                $filter['filter_limit']         = 4; // Product's Limit
                $filter['is_facet']             = 1;
                $filter['facet_field']          = 'selling_price';

                // Get Latest Product by Category
                $facet_result = $solr->getProductFromSolrOnly($filter);
                $latest_product[$catgory_id]['products'] = $facet_result['result_select_fields'];
                $facet_price = $facet_result['result_facet_field'];
                $facet_price_arr = array();
                foreach($facet_price as $fp => $fac_price ){
                    if($fac_price > 0){
                        $facet_price_arr[] = $fp; 
                    }
                }

                // Get Images of products 
                $temp_array = array();
                $temp_array = array_column($latest_product[$catgory_id]['products'], 'id');       
                $sql = "SELECT product_id, image, price FROM `".DB_PREFIX."product` WHERE product_id IN (".implode(',',$temp_array).")";
                $product_img = $this->db->query($sql)->rows;
                foreach($product_img as $val){
                    $latest_product[$catgory_id]['products'][$val['product_id']]['image'] = $val['image'];
                    $latest_product[$catgory_id]['products'][$val['product_id']]['price'] = Cart::getPrice($val, $this)['original_selling_price'];
                }

                // Get min price of category and name of category
                $sql = "SELECT category_id, name FROM " . DB_PREFIX . "category_description WHERE category_id = '". $catgory_id ."' AND language_id = 1";
                $category_info = $this->db->query($sql)->row;
                $latest_product[$catgory_id]['category_info'] = $category_info;
                $latest_product[$catgory_id]['category_info']['price'] = min($facet_price_arr);
                $category_arr[] = $catgory_id;
            }
        }

        if(count($latest_product) > 0){
            // here we make a json for category_id 
            // because we need a category_id on the basis of order by sent_last_preference asc and group by customer_id
            // AND Group by gives a random raw so, we make a json of category_id order by sent_last_preference asc
            $sql = "SELECT cp.customer_id, c.ws_gcm_registration_id as gcm_id, CONCAT('[',GROUP_CONCAT(CONCAT('{\"category_id\":',cp.category_id,'}') ORDER BY cp.sent_last_preference asc),']') AS customer_preference
                    FROM `".DB_PREFIX."customer_preference` cp
                    LEFT JOIN `".DB_PREFIX."customer` c ON cp.customer_id = c.customer_id
                    WHERE c.ws_gcm_registration_id != ''
                    AND c.app_version > 61 
                    AND cp.category_id IN (".implode(',',$category_arr).")
                    AND cp.category_id > 0 ";
            if(isset($this->request->get['test']) && $this->request->get['test'] == 1) {
                $sql .=" AND c.customer_id IN (50,12163,4042,198) ";
            }
            $sql .=" GROUP BY cp.customer_id";
            $customer_info = $this->db->query($sql)->rows;

            // Save notification into DB
            $notf_id = $this->_saveNotification();
            
            // ADD GCM_ID, And customer_id in $latest_product array 
            foreach ($customer_info as $preference) {
                $customer_preference = json_decode($preference['customer_preference'], true);
                $latest_product[$customer_preference[0]['category_id']]['store_id'][0]['customer_id'][]    = $preference['customer_id'];
                $latest_product[$customer_preference[0]['category_id']]['store_id'][0]['customer_gcm'][]   = $preference['gcm_id'];
            }

            foreach($latest_product as $products) {
                if(isset($products['store_id'])){
                    foreach($products['store_id'] as $customer_data){
                        if(isset($customer_data['customer_id'])) {
                            $this->_sendLatestArrivalNotificationToCustomers($products, $notf_id, $customer_data);
                            
                            // save last notificaion category_id in customer_preference table on the basis of customer_id
                            $sql = "UPDATE " . DB_PREFIX . "customer_preference SET sent_last_preference = now() WHERE customer_id IN (".implode(',', $customer_data['customer_id']).") AND category_id = '".$products['category_info']['category_id']."'";
                            $this->db->query($sql);
                        }
                    }
                }
            } 

            $end = microtime(true);
            echo "SEND Time: ". ($end - $start).' seconds';
        }
    }

    /**
     * _sendLatestArrivalNotificationToCustomers
     * @info    SEND Latest Arrival Notification To Customer 
     * STATIC   is_show_price set 1 for send price with images.
     * STATIC   is_show_test_on_image set 1 for show text on image.
     * @author  Garvit
     **/
    private function _sendLatestArrivalNotificationToCustomers($response, $notf_id, $customer_data) {

        $customers_gcm  = $customer_data['customer_gcm'];
        $category_id    = $response['category_info']['category_id'];

        $currency   = $this->config->get('config_currency');

        $text_on_image  = "Buy ". $response['category_info']['name'] ." at wholesale price, starting from ". $this->currency->format((int)$response['category_info']['price'], $currency);
        $final_img = array();
        $counter = 0;

        $this->load->model('tool/image');
        foreach($response['products'] as $value) {
            if(!empty($value['image']) && file_exists(DIR_IMAGE.$value['image'])){
                $final_img[$counter]['image']       = $this->model_tool_image->resizeBasedOnLargeDimension($value['image'], $this->config->get('config_image_thumb_width'));
            }else{
                $no_image = $this->config->get('config_url').'image/no_image.png';
                $final_img[$counter]['image']       = $this->model_tool_image->resize($no_image, $this->config->get('config_image_thumb_width'));
            }
            $final_img[$counter]['price']           = $this->currency->format((int)$value['price'], $currency);
            $final_img[$counter]['is_show_price']   = 1;
            $counter++;
        }
   
        $data = $this->_latestArrivalNotificationData();
        $msg = array
        (   'notification_id'       => $notf_id,
            'category_id'           => $category_id,
            'image_array'           => $final_img,
            'msg_type'              => $data['message_type'],
            'text_on_image'         => $text_on_image,
            'is_show_text_on_image' => 1,
            'url'                   => '',
            'message'               => $data['message'],
            'title'                 => $data['title'],
            'content_info'          => $response['category_info']['name'],
            'filter_arr'            => $data['filter_options'],
            'subtitle'              => '',
            'tickerText'            => '',
            'show_instantly'        => 0,
            'vibrate'               => 1,
            'lights'                => 1,
            'sound'                 => 1
        );

        $chunk_customers_gcm = array_chunk($customers_gcm, 500);
        foreach($chunk_customers_gcm as $cids){
            $notification = New Notification($cids, $msg);
            $notification->sendPushNotification();
        }
    }

    /**
     * _saveNotification
     * @info    Insert Notification Message to Datebase
     * @author  Garvit
     **/
    private function _saveNotification() {
        $data = $this->_latestArrivalNotificationData();
        
        $sql = "INSERT INTO " . DB_PREFIX . "notification_message 
                SET message_type = '" . (int)$data['message_type'] . "', 
                url = '" . $this->db->escape($data['url']) . "', 
                message = '" . $this->db->escape($data['message']) . "', 
                notes = '" . $this->db->escape($data['notes']) . "', 
                send_date = NOW()";
        $query = $this->db->query($sql);
        return $this->db->getLastId();
    }

    /**
     * _latestArrivalNotificationData
     * @info    Define latest Arrival Notification Message Data 
     * @author  Garvit
     **/
    private function _latestArrivalNotificationData(){
        $data = array();
        $data['message_type'] = 5; //auto send as per perference
        $data['url'] = '';
        $data['message'] = 'Latest Arrivals';
        $data['title'] = 'Latest Arrivals';
        $data['notes'] = 'Live';
        $data['filter_options']['headline'] = 'Latest Arrivals';
        $data['filter_options']['sort_options'] = 'latest_designs';
        return $data;
    }
    /**
    * send notification to all customer which are purchase a seller's product from particular store
    * @return products_id_array
    * @return category_array
    * @author kalyan 19th Mar, 2018
    */    
    public function sendNotificationToStoreCustomers(){
        
        $products_detail    = array();

        $sql =  " SELECT opn.seller_id,opn.product_id 
                FROM " . DB_PREFIX . "product_notification opn
                INNER JOIN " . DB_PREFIX . "product op
                    ON opn.product_id=op.product_id
                WHERE opn.operation_type = 'STOCK_TRANSFER' 
                    AND op.status = '1' 
                    AND op.stock_status_id='7'  
                    AND op.image IS NOT NULL 
                    ";

        $result = $this->db->query($sql);

        if($result->num_rows>0){

            foreach ($result->rows as $key => $value) {

                $products_detail[$value['seller_id']]['products_id_array'][] = $value['product_id'];

            }
        }
        /*
        * if record is available than create a queue
        */
        if (!empty($products_detail)) {
            
            /*
            * send push notification to seller customer when stock transfer to specfic store
            */
            $connection     = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $channel        = $connection->channel();
            $queue_name     = 'STAGING_GENERAL_TASKS_QUEUE';

                
            if (SITE_ENVIRONMENT == 'Production') {
               $queue_name = 'GENERAL_TASKS_QUEUE';
            }

            // third parameter is for queue durability. we set it to true
            // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
            // passive - false ; exclusive - false; auto-delete - false
            $channel->queue_declare($queue_name, false, true, false, false);

            $notification_data = array('datas'=>$products_detail);

            $que_data = array(
                            'constant_value' => unserialize(NOTIFICATIONTOSTORECUSTOMERS),
                            'data_array' => $notification_data
                        );
            $queue_object = base64_encode(serialize($que_data));

            // delivery_mode = 2 makes message persistent (durable)
            $msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
            $channel->basic_publish($msg, '', $queue_name);

            $channel->close();
            $connection->close(); //Closes Connection
        }    
    }


    /**
     * send a notification to all customers who purchased a product in the category of seller
     * @return void()
     * @author Manoj Singh Rajpurohit 11th Jan, 2019
     */
    public function sendSellerNewSkuUploadNotificationToCustomer(){

        $this->load->model('tool/image');
        $notification_data  = array();
        $ack_array = array();
        $array_cat = array();
        $array_count = array();

        $sql_get_products = "SELECT pn.seller_id, pn.category_id,pn.product_id ,
								    cd.name as category_name,
								    substring_index(GROUP_CONCAT(p.image) , ',',10) as product_images,   
								    substring_index(GROUP_CONCAT(pn.product_id) , ',',10) as product_ids   
								FROM ".DB_PREFIX."product_notification pn 
								INNER JOIN ".DB_PREFIX."product p ON p.product_id = pn.product_id    
								INNER JOIN ".DB_PREFIX."category_description cd ON cd.category_id = pn.category_id AND cd.language_id = '".(int)$this->config->get('config_language_id')."'
								WHERE pn.operation_type = 'NEW_PRODUCT' 								
								AND p.image != '' AND p.image IS NOT NULL 
								GROUP BY pn.seller_id, pn.category_id";

        /*
         * AND p.stock_status_id = 7
                                AND p.status = 1
                                AND p.hsn_code != ''  AND p.hsn_code IS NOT NULL
                                AND p.is_associate = 0
                                AND p.is_archived = 0
                                AND p.quantity > 0 	* */

        $result_products = $this->db->query($sql_get_products);

        if($result_products->num_rows > 0)
        {
            foreach ($result_products->rows as $product_value)
            {
                $ack_array[] =  array("seller_id"=>$product_value['seller_id'], "category_id"=>$product_value['category_id']);

                // Set Images for products.
                $get_image_array = explode(',', $product_value['product_images']);
                $image_array = array();
                foreach($get_image_array as $img_val)
                {
                    $image_array[] = array('image'=>$this->model_tool_image->resize($img_val, 500, 250));
                }

                // Set Title and Message for notification
                if ($product_value['category_name']!='') {

                    $title          = 'Latest Designs in '.$product_value['category_name'];
                    $sub_title      = 'Latest '.$product_value['category_name'].' designs based on your preferences.';
                    $message        = 'View latest and fresh '.$product_value['category_name'].' designs. Be the first one to buy them.';

                }else{

                    $title          = 'Latest Designs';
                    $sub_title      = 'Latest Designs based on your preferences.';
                    $message        = 'View latest and fresh designs. Be the first one to buy them.';
                }

                $sql =  " SELECT oc.customer_id,
							   oc.email,
							   oc.firstname,
							   oc.lastname,
							   oc.status        
						FROM ".DB_PREFIX."customer_preferences cp 						  
						  INNER JOIN ".DB_PREFIX."customer oc ON oc.customer_id = cp.customer_id						  
						WHERE cp.seller_id = '".(int)$product_value['seller_id']."' 
						AND cp.category_id = '".(int)$product_value['category_id']."' ";

                $result = $this->db->query($sql);

                if($result->num_rows>0){

                    $test_customer_data = array();
                    $test_customer_data[] = array("customer_id"=>"16867","email"=>"sinmanoj@gmail.com",
                        "firstname"=>"Manoj","lastname"=>"Singh","status"=>"1");
                    $test_customer_data[] = array("customer_id"=>"4042","email"=>"rakesh.shekhawat+2@gmail.com",
                        "firstname"=>"Rakesh","lastname"=>"Shekhawat","status"=>"1");
                    $test_customer_data[] = array("customer_id"=>"12163","email"=>"neeraj@aapnnihotel.in",
                        "firstname"=>"Neeraj","lastname"=>"Neeraj","status"=>"1");
                    $merge_customer_data = array_merge($result->rows, $test_customer_data);


                    $i=1;
                    foreach ($merge_customer_data as $key => $value) {

                        $notification_data[] = array("type"=>"customer",
                            "id"=>$value['customer_id'],
                            //"seller_id"=>$product_value['seller_id'],
                            //"category_id"=>$product_value['category_id'],
                            "is_pn_to_send"=>true,
                            "is_sms_to_send"=>false,
                            "is_email_to_send"=>false,
                            "email_to_head"=>false,
                            "pn_to_head"=>false,
                            "sms_to_head"=>false,
                            "pn"=> array( "notification_id" => rand(10,99999),
                                "text_on_image" => $message,
                                "category_id" => $product_value['category_id'],
                                "content_info" => $product_value['category_name'],
                                "filter_arr" => array('headline'=>'Latest Arrivals', 'sort_options'=>'latest_designs'),
                                "image_array" => $image_array,
                                "lights" => "0",
                                "is_show_text_on_image" => "1",
                                "message" => $message,
                                "msg_type" => "5",
                                "sound" => "1",
                                "sub_title" => $sub_title,
                                "title" => $title,
                                "vibrate" => "1",
                            )
                        );

                        $i++;
                    }// get customer loop

                    $array_cat[] = $product_value['category_name'];
                    $array_count[] = $i;

                }// get customer if condition

            }// get products loop
        } // get products condition

        $newDArray = array();
        // New datas will be put in this new array.
        $new_array = array(array('name'=>$array_cat, 'count'=>$array_count));
        $tot = 0;
        foreach($new_array as $val) {
            foreach($val['name'] as $key => $name) {

                if (!isset($newDArray[$name])) {
                    // Init $newArray[A] for example
                    $newDArray[$name] = 0;
                }

                $newDArray[$name] += $val['count'][$key];
                $tot += $val['count'][$key];
                // Add the right 'count' to $newArray[A]
            }
        }

        $html_for_mail  =  '<table border="1">';
        $html_for_mail .= '<th>Category Name</th>						
							<th>Customers(#)</th>';

        foreach($newDArray as $cat_name=>$val) {

            $html_for_mail .= "<tr><td>".$cat_name."</td>
								<td>".$val."</td>";

        }
        $html_for_mail .= "<tr><td>Total</td>
								<td>".$tot."</td>";
        $html_for_mail .= "</td></tr></table>";


        // Generate Queue
        if (!empty($notification_data)) {

            // Send notification mail
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl';
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            $mail->setFrom($this->config->get('config_mail_smtp_username'), 'Wholesale Box');
            $mail->addAddress(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);

            try{

                $connection     = new AMQPStreamConnection('localhost', '5672', 'guest', 'guest');
                $channel        = $connection->channel();

                $queue_name = 'STAGING_GENERAL_TASKS_QUEUE';

                if (SITE_ENVIRONMENT == 'Production') {

                    $queue_name = 'GENERAL_TASKS_QUEUE';
                }

                // third parameter is for queue durability. we set it to true
                // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
                // passive - false ; exclusive - false; auto-delete - false
                $channel->queue_declare($queue_name, false, true, false, false);

                $notification_to_customer = array('datas'=>$notification_data);

                $queue_send_data = array(
                    'constant_value' => unserialize(SLRNEWSKUNOTIFITOCSTMR),
                    'data_array' => $notification_to_customer
                );

                $queue_object = base64_encode(serialize($queue_send_data));

                // delivery_mode = 2 makes message persistent (durable)
                $msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
                $channel->basic_publish($msg, '', $queue_name); // send to sms_queue

                $channel->close();
                $connection->close();


                // After making the queue, delete from products_notification table those are acknowledged
                $this->__deleteSellerProductsFromNotificationTable($ack_array);

                $mail->Subject = 'Notification To Customers For New Inventory By Seller - ' . date('d-m-Y');
                $mail->msgHTML($html_for_mail);
                $mail->send();

            } catch (Exception $e) {
                $error_message = 'Message: ' .$e->getMessage();

                $mail->Subject = 'ERROR: Notification To Customers For New Inventory By Seller - ' . date('d-m-Y');
                $mail->msgHTML($error_message);
                $mail->send();

            }

        }


    }



    /**
    * send notification to all customer which are purchase a product in category of seller
    * @return array of seller category and product id
    * @author kalyan 19th Mar, 2018
    */
    public static function x_sendSellerNewSkuUploadNotificationToCustomer(){

        $products_detail    = array();

        $sql =  " SELECT opn.seller_id, opn.product_id, opn.category_id 
                FROM " . DB_PREFIX . "product_notification opn
                INNER JOIN " . DB_PREFIX . "product op
                    ON opn.product_id=op.product_id
                WHERE opn.operation_type = 'NEW_PRODUCT' 
                    AND op.status = '1' 
                    AND op.stock_status_id='7' 
                    AND op.image IS NOT NULL 
                    ";

        $result = $this->db->query($sql);

        if($result->num_rows>0){

            foreach ($result->rows as $key => $value) {

                if (count($products_detail[$value['category_id']]['product_id_array'])<10) {
                    $products_detail[$value['category_id']]['product_id_array'][]  = $value['product_id'];
                }

                $products_detail[$value['category_id']]['seller_id_array'][]   = $value['seller_id'];
            }
        }

        /*
        * start new work
        */
        $all_seller_id_array = array();
        $k = 0;
        $i = 0;

        $user_data = array();
        if (!empty($products_detail)) {

            foreach ($products_detail as $category_id => $param_data) {
            
            $jsonData                     = '';
            $api_data                     = array();
            $customer_id_array            = array();
            $customer_data_array          = array(); 
            $cc_email_array               = array();
            $product_image_array          = array();
            $product_array                = array();
            $image_array                  = array();
            $seller_id_array              = array();
            $subject                      = '';
            $html_body                    = '';
            $seller_id_array              = array_unique($param_data['seller_id_array']);
            $all_seller_id_array[$k]      = array_unique($param_data['seller_id_array']);
            $k++;

            /*
            * get all customers id array
            */
            $customer_id_array = $this->__getCustomersByCategoryAndSeller($category_id, $seller_id_array);
            
            if (!empty($customer_id_array)) {

              $customer_data_array = $this->__getCustomerData($customer_id_array);
            }

            /*
            * get category name
            */
            $category_name        = $this->__getCategoryName($category_id);
            $product_id_array     = $param_data['product_id_array'];
            
            /*
            * set message, title and subtitle
            */        
            if ($category_name!='') {

              $title          = 'Latest Designs in '.$category_name.'!';
              //$subject        = 'Just launched! Fresh designs in '.$category_name.' at lowest factory price!';
            }else{

              $title          = 'Latest Designs!';
              //$subject        = 'Just launched! Fresh designs at lowest factory price!';
            }
            
            $sub_title      = 'Latest '.$category_name.' designs based on your preference!';
            $message        = "View latest and fresh ".$category_name." designs!! Be the first one to buy them!";
            
            /*
            * set user data
            */            
            $static_customer_data = array();

            $static_customer_data[0]['customer_id']  = '28';
            $static_customer_data[0]['firstname']    = 'Manoj Singh';        
            $static_customer_data[0]['lastname']     = 'Rajpurohit';     
            $static_customer_data[0]['email']        = 'sinmanoj@gmail.com';

            $static_customer_data[1]['customer_id']  = '29';
            $static_customer_data[1]['firstname']    = 'Rakesh';        
            $static_customer_data[1]['lastname']     = 'Shekhawat';          
            $static_customer_data[1]['email']        = 'rakesh.shekhawat+2@gmail.com';            

            if (!empty($customer_data_array)) {

              $customer_count = count($customer_data_array);
              
              foreach ($static_customer_data as $key => $value) {
                
                $customer_data_array[$customer_count]['customer_id']  = $value['customer_id'];
                $customer_data_array[$customer_count]['firstname']    = $value['firstname'];     
                $customer_data_array[$customer_count]['lastname']     = $value['lastname'];       
                $customer_data_array[$customer_count]['email']        = $value['email'];

                $customer_count++;
              }

            }else{

              $customer_data_array[0]['customer_id']  = '28';
              $customer_data_array[0]['firstname']    = 'Manoj Singh';        
              $customer_data_array[0]['lastname']     = 'Rajpurohit';       
              $customer_data_array[0]['email']        = 'sinmanoj@gmail.com';

              $customer_data_array[1]['customer_id']  = '29';
              $customer_data_array[1]['firstname']    = 'Rakesh';        
              $customer_data_array[1]['lastname']     = 'Shekhawat';      
              $customer_data_array[1]['email']        = 'rakesh.shekhawat+2@gmail.com';

            }
            
            if (!empty($customer_data_array)) {
                
              foreach ($customer_data_array as $key => $customer_data) {

                //$html_body        = '';
                //$proudct_html     = '';
                $currency_type    = 'INR';
                  
                $postedDataArray = array(
                'product_id_string' => implode(',', $product_id_array),
                'currency' => $currency_type,
                'category_id'=>$category_id
                );

                $curl_url = HTTPS_SERVER."api/product/getProductDetailsByProductId";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $curl_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postedDataArray);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $resultSet = curl_exec($ch);
                $productBoxData = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $resultSet), true); //sanitize the json and decode to array
                
                curl_close($ch);

                if (!empty($productBoxData)) {
                    foreach ($productBoxData as $key => $value) {              
                        $image_array[]  = array('image'=> $value['image']);
                    }
                }
                           
                
                /*$html_body = file_get_contents(DIR_TEMPLATE. 'default/template/mail/email_to_customer_seller_upload_new_sku.tpl');
                
                $proudct_html = $this->__setProductHtmlForInventoryImport($productBoxData);*/

                /*$html_body = str_replace('##CUSTOMER_NAME##', $customer_data['firstname'].' '.$customer_data['lastname'], $html_body);
                $html_body = str_replace('##PRODUCT_DATA##', $proudct_html, $html_body);
                $html_body = str_replace('##CATEGORY_NAME##', $category_name, $html_body);
                $html_body = str_replace('CATEGORY_URL', current($productBoxData)['category_url'], $html_body);*/

                $user_data[$i]['type']                          = 'customer'; 
                $user_data[$i]['id']                            = $customer_data['customer_id']; 
                $user_data[$i]['is_pn_to_send']                 = true;

                $user_data[$i]['pn']['notification_id']         = rand(10,99999);//notification id must be unique for every message
                $user_data[$i]['pn']['text_on_image']           = $message;
                $user_data[$i]['pn']['category_id']             = $category_id;
                $user_data[$i]['pn']['content_info']            = $category_name; 
                $user_data[$i]['pn']['filter_arr']              = array('headline'=>'Latest Arrivals', 'sort_options'=>'latest_designs'); 
                $user_data[$i]['pn']['image_array']             = $image_array;
                $user_data[$i]['pn']['lights']                  = "0";              
                $user_data[$i]['pn']['is_show_text_on_image']   = "1";
                $user_data[$i]['pn']['message']                 = $message;
                $user_data[$i]['pn']['msg_type']                = "5";
                $user_data[$i]['pn']['sound']                   = "1";
                $user_data[$i]['pn']['sub_title']               = $sub_title;
                $user_data[$i]['pn']['title']                   = $title;
                $user_data[$i]['pn']['vibrate']                 = 1;

                $user_data[$i]['is_sms_to_send']                = false;

                $user_data[$i]['is_email_to_send']              = false;              
               /* $user_data[$i]['email']['cc']                   = $cc_email_array;
                $user_data[$i]['email']['to']                   = $customer_data['email']; 
                $user_data[$i]['email']['subject']              = $subject;
                $user_data[$i]['email']['body']                 = serialize($html_body);
                $user_data[$i]['email']['is_html']              = true;*/

                $user_data[$i]['email_to_head']                 = false;
                $user_data[$i]['pn_to_head']                    = false;
                $user_data[$i]['sms_to_head']                   = false;
                
                $i++;  
                                    
              }
            }
          }
            /*
            * delete all new product of seller from product_notification table
            */            
            $this->__deleteSellerProductsFromNotificationTable(array_map('current', $all_seller_id_array));
        }
        /*
        * end new work
        */
        if (!empty($user_data)) {

            $connection     = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $channel        = $connection->channel();

            $queue_name = 'STAGING_GENERAL_TASKS_QUEUE';
                
            if (SITE_ENVIRONMENT == 'Production') {

               $queue_name = 'GENERAL_TASKS_QUEUE';
            }            

            // third parameter is for queue durability. we set it to true
            // so that even if rabbitmq-server stops or crashes, queue is recreated and not deleted from memory
            // passive - false ; exclusive - false; auto-delete - false
            $channel->queue_declare($queue_name, false, true, false, false);
            
            $notification_data = array('datas'=>$user_data);
            
            $queue_send_data = array(
                            'constant_value' => unserialize(SLRNEWSKUNOTIFITOCSTMR),
                            'data_array' => $notification_data
                        );
            $queue_object = base64_encode(serialize($queue_send_data));

            // delivery_mode = 2 makes message persistent (durable)
            $msg = new AMQPMessage($queue_object, array('delivery_mode' => 2));
            $channel->basic_publish($msg, '', $queue_name); // send to sms_queue

            $channel->close();
            $connection->close();
        }
    }

    /**
    * method for get customer of seller's which all are purchase a product in specfic category
    * @param categories id array
    * @param seller id
    * @return customer id array
    * @author kalyan 14th Dec, 2017
    */   
    private function __getCustomersByCategoryAndSeller($category_id, $seller_id_array) {

       if(empty($seller_id_array) || empty($category_id)) {
           return false;
       }
      
       $formatted_seller_id = implode("','", $seller_id_array);

       $sql = "SELECT GROUP_CONCAT(DISTINCT(ocp.customer_id)) AS customer_ids
               FROM oc_customer_preferences ocp
               INNER JOIN " . DB_PREFIX . "customer oc
                    ON ocp.customer_id=oc.customer_id 
               WHERE ocp.seller_id IN ('".$formatted_seller_id."') 
                AND ocp.category_id='".$category_id."'
               GROUP BY ocp.seller_id";

       $result = $this->db->query($sql);

       if($result->num_rows){
           foreach ($result->rows as $key => $ids) {
               if(!empty($ids['customer_ids'])) {
                   return array_filter(explode(',', $ids['customer_ids']));
               }
           }
       }else{
        return false; 
       }
       
   }
    /**
    * method for get category name
    * @param category id
    * @return category name
    * @author kalyan 14th Dec, 2017
    */  
    private function __getCategoryName($category_id){
      
      $sql = "SELECT name 
              FROM oc_category_description
              WHERE category_id = '".(int)$category_id."' AND language_id = '1'
              ";
             
      $result = $this->db->query($sql);

      if ($result->num_rows > 0) {
        
        return $result->row['name'];
      }
    }

    /**
    * method for set html for email when Inventory import for seller
    * @param product array
    * @return html
    * @author kalyan 23th Feb, 2018
    */
     private function __setProductHtmlForInventoryImport($data){

      if (!empty($data)) {

        $html = '';

        foreach($data as $proudcts){

        $html .= '  
                  <div style="width:50%;display:inline-block;vertical-align:top">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody>
                        <tr>
                          <td align="center" style="padding:0 10px" valign="top">
                          <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody>
                              <tr>
                                <td height="15" style="height:15px;line-height:15px"> </td>
                              </tr>
                              <tr>
                                <td align="center" width="290px">
                                  <a href="'. $proudcts['prolink'].'" target="_blank">
                                    <img alt="prod4;" src="'. $proudcts['image'].'" style="display: block;width: 100%;"/> 
                                  </a>
                                </td>
                              </tr>
                              <tr>
                                <td height="15" style="height:15px;line-height:15px"> </td>
                              </tr>
                              <tr>
                                <td align="left" style="font-family:lato;font-size:14px;line-height:22px;text-align:center" valign="top"><b><a href="'. $proudcts['prolink'].'" style="text-decoration:none;color:#282f89" target="_blank">'. $proudcts['name'].'</a> </b></td>
                              </tr>
                              <tr>
                                <td height="4" style="height:4px;line-height:4px"> </td>
                              </tr>
                              <tr>
                                <td align="center" style="font-family:lato;font-size:15px;line-height:22px;font-weight:400;color:#181818" valign="top"><b>'. $proudcts['price'].' </b></td>
                              </tr>
                              <tr>
                                <td height="4" style="height:4px;line-height:4px"> </td>
                              </tr>
                              <tr>
                                <td align="center" style="font-family:lato;font-size:12px;line-height:22px;font-weight:400;color:#898788" valign="top;">'. $proudcts['set_description'].'</td>
                              </tr>
                              <tr>
                                <td height="4" style="height:4px;line-height:4px"> </td>
                              </tr>                              
                              <tr>
                                <td height="4" style="height:4px;line-height:4px"> </td>
                              </tr>
                              <tr>
                                <td height="15" style="height:15px;line-height:15px"> </td>
                              </tr>
                            </tbody>
                          </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>';
        
        }
        return $html;
      }
    }
    /**
    * method for delete seller products which all received notification
    * @param seller id
    * @param product id array
    * @author kalyan 19th Mar, 2018
    */
    private function __deleteSellerProductsFromNotificationTable($notification_ack_array) {


        if(empty($notification_ack_array)) {
            return false;
        }

        foreach($notification_ack_array as $val){

            $sql = "DELETE 
               FROM " . DB_PREFIX . "product_notification 
               WHERE seller_id = '".$val['seller_id']."' AND category_id = '".$val['category_id']."' AND operation_type='NEW_PRODUCT' ";
            $result = $this->db->query($sql);
        }

    }
    /**
    * method for get get customers data based on customer id
    * @param customer id array
    * @author kalyan 23th Feb, 2018
    */   
    private function __getCustomerData($customer_id_array) {

       if(empty($customer_id_array)) {
           return false;
       }
       $formatted_customer_id_array = implode("','", $customer_id_array);
       $sql = "SELECT  oc.customer_id,oc.email,oc.firstname,oc.lastname
                FROM " . DB_PREFIX . "customer oc                
                WHERE oc.customer_id IN('".$formatted_customer_id_array."') 
                  AND oc.status='1'  
                ";
                
        $query = $this->db->query($sql);

       if($query->num_rows>0){
            return $query->rows;
       }else{
            return false;
       }
   }  

}