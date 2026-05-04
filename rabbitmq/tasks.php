<?php
include_once (__DIR__ . '/../config.php');
require_once(DIR_SYSTEM . 'library/operations/orders/order_status.php');
require_once(DIR_SYSTEM . 'library/db/db.php');
require_once(DIR_SYSTEM . 'library/phpmailer.php');
require_once(DIR_SYSTEM . 'library/config.php');
require_once(DIR_SYSTEM . 'library/mailtemplate.php');
require_once(DIR_SYSTEM . 'library/productchangelog.php');
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/bank_transfer.php');
require_once(DIR_SYSTEM . 'library/customer.php');
require_once(DIR_SYSTEM . 'library/curl.php');
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/rbl.php');

class Tasks{
   
    public static function curlRequestCRMTask($db, $data = array()){
  		if(empty($data)){
  			return false;
  		}
      $curl_url = WSBOX_CRM_URL. "crmapi/leads/updateCollectionStatus";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $curl_url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $result  = curl_exec($ch);

      curl_close($ch);
      $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
      $result = json_decode($result);
      return true;
    }
    /**
    * send notification to all customer which are purchase a product in category of seller
    * @param db object
    * @param categories id array
    * @param seller id
    * @param products id array
    * @author kalyan 14th Dec, 2017
    */    
    public static function sendSellerNewSkuUploadNotificationToCustomer($db, $data_get){               
        
          /*
          * notification will send when images are available
          */
          if (!empty($data_get)) {

            $objDateTime                = new DateTime();
            $path_name                  = 'staging/';
            $send_wait                  = 'instant';
            $notification_sending_time  = $objDateTime->format('Y-m-d H:i:s'); 
            $minutes_to_add             = 15;    
            
            if (SITE_ENVIRONMENT == 'Production') {
               
               $path_name                   = '';
               $send_wait                   = 'later';

               $objDateTime->add(new DateInterval('PT' . $minutes_to_add . 'M'));

               $notification_sending_time   = $objDateTime->format('Y-m-d H:i:s');  
            }

            $api_data = array(
              'notification_sending_time'=>$notification_sending_time,
              'type'=>$send_wait,
              'data'=>$data_get['datas']
              );
            /*
            * api code
            */        
            $json_data = json_encode($api_data);
            $url       = 'https://www.wholesalebox.biz/'.$path_name.'crmapi/Notifications/sendNotificationFromWeb';
            $curl      = curl_init();
            // Set SSL if required
            if (substr($url, 0, 5) == 'https') {
             curl_setopt($curl, CURLOPT_PORT, 443);
            }

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$json_data);             

            $json = curl_exec($curl);
            curl_close($curl); 
          }
          return true;
    }
   /**
    * method for get product image
    * @param  db object
    * @param  product id array
    * @return products id array
    * @author kalyan 14th Dec, 2017
    */  
    public static function getProductsImages($db, $product_id_array){
        
        $product_data           = array();
        $formatted_product_ids  = implode("','", $product_id_array);
        
        $sql =  "SELECT image 
                 FROM oc_product
                 WHERE product_id IN ('".$formatted_product_ids."')
                 ";
             
        $result = $db->query($sql);

        $product_data = $result->rows;
        
        foreach ($product_data as $key => $value) {

          if (!empty($value['image'])) {

            $product_data[$key]['image'] = self::resize($value['image'],50,50);
          
          }else{
          
            unset($product_data[$key]);
          
          }

        }
        return $product_data;
    }
    /**
    * function to get full url of image
    * @param imagename
    * @return full pata of image 
    * @author kalyan 14th Dec, 2017
    */
    public static function resize($filename, $width, $height='') {
        
      $static_content_url = STATIC_CONTENT_URL;
      if(isset($_SERVER['HTTPS'])){
          $static_content_url = STATIC_CONTENT_URL_SSL;
      }

      // if nginx is enable, then no need to do resizing, directly return the url.
      if(NGINX_ENABLED == 1){
          
          $quality = 90;          
          
          return $static_content_url . 'img/dw=' . $width . ',dh=' . $height . ',q='. $quality .'/' . $filename;
      }else{

        return $static_content_url.$filename;
      }
    }   

    /**
     * Public function to set Cofig Data from oc_settings table
     * @return: $config Object
     * @author: Nishu, 1st Feb 2018
    */
    private static function getConfigData(){
        // Config Object
        $config = new Config();
        
        // Database
        $db = new Database\DB( DB_SERVERS );

        $config->set('db', $db);

        // Settings
        $query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0'");

        foreach ($query->rows as $setting) {
            if (!$setting['serialized']) {
                $config->set($setting['key'], $setting['value']);
            } else {
                $config->set($setting['key'], unserialize($setting['value']));
            }
        }
        return $config;
    }

    /**
     * @info: To send mail for custom DebitNote
     * @param: $files Array
     * @return: Void
     * @author: Nishu, 1st Feb 2018
    */
    public static function sendMailForCustomDn($db, $mail_data = array()){
      if(!empty($mail_data)){
        $files    = $mail_data['files'];
        $order_no = $mail_data['order_no'];
        //Send Mail to inform generated Custom DebitNote(s)
        $config = self::getConfigData();
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom($config->get('config_email'), 'Wholesale Box');
        $mail->addReplyTo($config->get('config_email'), 'Wholesale Box');
        
        $mail->addAddress(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addCC(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->Subject = 'Custom DebitNote(s), Order No: '.$order_no;
        $mail->Body = 'Please find the following generated Custom DebitNote(s) in attachments';
        foreach ($files as $file_name) {
            $mail->AddAttachment($file_name);
        }
        //send to admin;
        $mail->send(0,false);
      }
      return true;
    }

   /**
     * @info: Public method to send mail
     * @param: $file_name
     * @return: void
     * @author: Nishu, 1st Feb 2018
   */
   public static function sendMailWsbPurchaseDn($db, $file_name = ''){
        if(empty($file_name)){
            return false;
        }
        $config = self::getConfigData();
        
        $file_name = DIR_DLOAD_SLR_DBT_NOTE.$file_name;

        //Send Mail to inform generated DebitNote
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom($config->get('config_email'), 'Wholesale Box');
        $mail->addReplyTo($config->get('config_email'), 'Wholesale Box');
        $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
        $mail->addCC(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->Subject = 'DebitNote for WSB Purchase '.date('d-m-Y h:m');
        $mail->Body = 'Please find the pdf file of generated DebitNote in attachments';
        $mail->AddAttachment($file_name);
        //send to admin;
        $mail->send(0,false);
        return true;
   }

   /**
     * @info: Public method to send mail
     * @param: $file_name
     * @return: void
     * @author: Nishu, 1st Feb 2018
   */
   public static function sendMailWsbPurchaseCancelDn($db, $file_name = ''){
        if(empty($file_name)){
            return false;
        }
        $config = self::getConfigData();
        
        $file_name = DIR_DLOAD_SLR_DBT_NOTE.$file_name;

        //Send Mail to inform generated DebitNote
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';
        $mail->Host = $config->get('config_mail_smtp_hostname');
        $mail->Port = $config->get('config_mail_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = $config->get('config_mail_smtp_username');
        $mail->Password = $config->get('config_mail_smtp_password');
        $mail->setFrom($config->get('config_email'), 'Wholesale Box');
        $mail->addReplyTo($config->get('config_email'), 'Wholesale Box');
        $mail->addAddress(EMAIL_IDS['store_manager']['email_id'], EMAIL_IDS['store_manager']['name']);
        $mail->addCC(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addCC(EMAIL_IDS['vikas']['email_id'], EMAIL_IDS['vikas']['name']);
        $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->Subject = 'Cancelled DebitNote for WSB Purchase '.date('d-m-Y h:m');
        $mail->Body = 'Please find the pdf file of cancelled DebitNote in attachments';
        $mail->AddAttachment($file_name);
        //send to admin;
        $mail->send(0,false);
        return true;
   }

   /**
    * send notification to all customer which are purchase a seller's product from particular store
    * @param db object
    * @param seller id
    * @param products id array
    * @author kalyan 17th Dec, 2017
    */    
    public static function sendNotificationToStoreCustomers($db, $data_get){
        
        if(empty($data_get)){
            return false;
        } 

        foreach ($data_get['datas'] as $seller_id => $param_data) {
          
          $jsonData                       = '';
          $api_data                       = array();
          $user_data                      = array();
          $seller_data                    = array();
          $product_id_array               = array();
          $product_image_array            = array();
          $seller_id_array                = array();
          $image_array                    = array();
          $cc_email_array                 = array();
          $products_detail_array          = array(); 
          $products_array                 = array(); 
          $formated_category_id_array     = array();
          $category_slug_array            = array();
          $formated_category_id_string    = '';
          $seller_ids                     = '';
          $store_sales                    = '';
          $store_name                     = '';
          $subject                        = '';
          $html_body                      = '';
          /*
          * get product id array
          */
          
          $product_id_array = $param_data['products_id_array'];
          /*
          * get category id array of products
          */
          $category_data = self::getProductsCategoryIdForStockTransfer($db, $product_id_array);
          
          if (!empty($category_data)) {
            
            foreach ($category_data as $key => $value) {
             
                $formated_category_id_string .= $value['category_id'].'-'.$value['product_id'].',';
              
            }
            $formated_category_id_string = rtrim($formated_category_id_string,',');
          }
          /*
          * get seller data
          */
          $seller_data = self::getSellersByWsbProductIds($db, $product_id_array);

          if (!empty($seller_data)) {
              
              $seller_id_array  = array_unique( array_column($seller_data,'seller_id'));
              $seller_ids       = implode(',', $seller_id_array);
          }
          /*
          * get store name
          */
          $store_city = array(
              "JP" => "JAIPUR",
              "DL" => "DELHI",
              "ST" => "SURAT",
              "BL" => "BANGALORE",
              "KL" => "KOLKATA",
              "MU" => "MUMBAI",
              "BL" => "BANGALORE",
              "MB" => "MUMBAI"
          );

          $store_sales = self::getStoreName($db, $seller_id);

          $store_name  = $store_city[$store_sales];
          /*
          * get all customers id array
          */
          $customer_data_array = self::getWsbCustomers($db, $seller_id_array,$store_sales);
          
          /*
          * set message, title and subtitle
          */          
          $title          = 'New Products at Wholesalebox Store!!';
          $sub_title      = 'Visit and view latest products at Wholesalebox based on your preference!';
          $message        = 'View latest collection at our Wholesalebox store. Buy before it goes out of stock!!';
          $subject        = $title;
          /*
          * set api data
          */
          $objDateTime                = new DateTime();
          $path_name                  = 'staging/';
          $send_wait                  = 'instant';
          $notification_sending_time  = $objDateTime->format('Y-m-d H:i:s'); 
          $minutes_to_add             = 15;    
          
          if (SITE_ENVIRONMENT == 'Production') {
             
             $path_name                   = '';
             $send_wait                   = 'later';

             $objDateTime->add(new DateInterval('PT' . $minutes_to_add . 'M'));

             $notification_sending_time   = $objDateTime->format('Y-m-d H:i:s');  
          }
          
          $api_data['notification_sending_time']  = $notification_sending_time;
          $api_data['type']                       = $send_wait;
          
          /*
          * set user data
          */
          $i = 0;

          $static_customer_data = array();

          $static_customer_data[0]['customer_id']  = '16867';
          $static_customer_data[0]['firstname']    = 'Manoj Singh';        
          $static_customer_data[0]['lastname']     = 'Rajpurohit';      
          $static_customer_data[0]['email']        = 'sinmanoj@gmail.com';

          $static_customer_data[1]['customer_id']  = '4042';
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

            $customer_data_array[0]['customer_id']  = '16867';
            $customer_data_array[0]['firstname']    = 'Manoj Singh';        
            $customer_data_array[0]['lastname']     = 'Rajpurohit';    
            $customer_data_array[0]['email']        = 'sinmanoj@gmail.com';

            $customer_data_array[1]['customer_id']  = '4042';
            $customer_data_array[1]['firstname']    = 'Rakesh';        
            $customer_data_array[1]['lastname']     = 'Shekhawat';      
            $customer_data_array[1]['email']        = 'rakesh.shekhawat+2@gmail.com';

          }  
          
          if (!empty($customer_data_array)) {
              
              foreach ($customer_data_array as $key => $customer_data) {
                
                $html_body        = '';
                $proudct_html     = '';
                $currency_type    = 'INR';
                  
                $postedDataArray = array(
                    'product_id_string' => implode(',', $param_data['products_id_array']),
                    'currency' => $currency_type,
                    'formated_category_id_string'=>$formated_category_id_string
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
                    foreach ($productBoxData as $key => $pro_data) {          
                        foreach ($pro_data as $key => $value) {    
                        $image_array[] = array('image'=> $value['image']);
                        }
                    }
                }

                $html_body = file_get_contents(DIR_TEMPLATE. 'default/template/mail/email_to_store_customer_when_stock_update.tpl');
                
                $proudct_html = self::setProductHtmlForStockTransfer($productBoxData);

                $html_body = str_replace('##CUSTOMER_NAME##', $customer_data['firstname'].' '.$customer_data['lastname'], $html_body);
                $html_body = str_replace('##STORE_NAME##',ucfirst(strtolower($store_name)), $html_body);
                $html_body = str_replace('##PRODUCT_DATA##', $proudct_html, $html_body);

                $user_data[$i]['type']                              = 'customer'; 
                $user_data[$i]['id']                                = $customer_data['customer_id']; 
                $user_data[$i]['is_pn_to_send']                     = true;              
                $user_data[$i]['pn']['notification_id']             = time();
                $user_data[$i]['pn']['text_on_image']               = $message;
                $user_data[$i]['pn']['filter_arr']                  = array('headline'=>'Latest Arrivals', 'sort_options'=>'latest_designs'); 
                $user_data[$i]['pn']['image_array']                 = $image_array;
                $user_data[$i]['pn']['lights']                      = "0";
                $user_data[$i]['pn']['is_show_text_on_image']       = "1";
                $user_data[$i]['pn']['seller_ids']                  = $seller_ids;//comma seperate
                $user_data[$i]['pn']['message']                     = $message;
                $user_data[$i]['pn']['msg_type']                    = "5";
                $user_data[$i]['pn']['sound']                       = "1";
                $user_data[$i]['pn']['sub_title']                   = $sub_title;
                $user_data[$i]['pn']['title']                       = $title;
                $user_data[$i]['pn']['vibrate']                     = 1;

                $user_data[$i]['is_sms_to_send']                    = false; 
                $user_data[$i]['is_email_to_send']                  = true;
                
                $user_data[$i]['email']['cc']                       = $cc_email_array;
                $user_data[$i]['email']['to']                       = $customer_data['email'];
                $user_data[$i]['email']['subject']                  = $subject;
                $user_data[$i]['email']['body']                     = serialize($html_body);
                $user_data[$i]['email']['is_html']                  = true;

                $user_data[$i]['email_to_head']                     = false;
                $user_data[$i]['pn_to_head']                        = false;
                $user_data[$i]['sms_to_head']                       = false;
                
                $i++;  
                                    
              }
              
              $api_data['data'] = $user_data;
              
              if (!empty($productBoxData)) {
                
                /*
                * api code
                */        
                $json_data = json_encode($api_data);

                $url       = 'https://www.wholesalebox.biz/'.$path_name.'crmapi/Notifications/sendNotificationFromWeb';
                $curl      = curl_init();
                // Set SSL if required
                if (substr($url, 0, 5) == 'https') {
                   curl_setopt($curl, CURLOPT_PORT, 443);
                }
               
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, $url);
               
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS,$json_data);             

                $json = curl_exec($curl);
                curl_close($curl);
              }
          }
          /*
          * delete all record of seller
          */
          self::deleteSellerProducts($db, $seller_id, $param_data['products_id_array']);
        }
        return true;
    }
    /**
    * method for get seller array based on wsb product id array
    * @param db object
    * @param product id array
    * @author kalyan 17th Feb, 2018
    */   
    public static function getSellersByWsbProductIds($db, $product_id_array) {

       if(empty($product_id_array)) {
           return false;
       }

       $product_id_array = array_unique($product_id_array);
       
       $formatted_product_id_array = implode("','", $product_id_array);

       $sql = "SELECT wp.seller_id
               FROM oc_wsb_purchase_breakup wpb
               INNER JOIN oc_wsb_purchase wp
                ON wpb.purchase_id=wp.purchase_id 
               WHERE wpb.product_id IN('".$formatted_product_id_array."') ";
              
       $result = $db->query($sql);

       if($result->num_rows>0){
            return $result->rows;
       }else{
            return false;
       }
   }
   /**
    * method for get store name based on customer id
    * @param db object
    * @param customer id
    * @author kalyan 19th Feb, 2018
    */   
    public static function getStoreName($db, $seller_id) {
        
       if(empty($seller_id)) {
           return false;
       }

       $sql = "SELECT  oms.pickup_city_code
                FROM " . DB_PREFIX . "customer oc
                INNER JOIN " . DB_PREFIX . "ms_seller oms
                    ON oc.customer_id=oms.seller_id
                WHERE oc.customer_id= '" . (int)$seller_id . "' 
                    AND oms.seller_invoice_generate='0'";
           
        $query = $db->query($sql);
        
       if($query->num_rows){
            return $query->row['pickup_city_code'];
       }else{
            return false;
       }
   }
   /**
    * method for get get customers bassed on seller id
    * @param db object
    * @param seller id array
    * @author kalyan 19th Feb, 2018
    */   
    public static function getWsbCustomers($db, $seller_id_array,$store_sales) {

       if(empty($seller_id_array) || empty($store_sales)) {
           return false;
       }
       $formatted_seller_id_array = implode("','", $seller_id_array);
       $sql = "SELECT  oc.customer_id,oc.email,oc.firstname,oc.lastname
                FROM " . DB_PREFIX . "customer oc
                INNER JOIN " . DB_PREFIX . "order oo
                    ON oc.customer_id=oo.customer_id      
                INNER JOIN " . DB_PREFIX . "order_product oop
                    ON oo.order_id=oop.order_id
                INNER JOIN " . DB_PREFIX . "wsb_purchase_breakup owpb
                    ON oop.product_id=owpb.product_id
                INNER JOIN " . DB_PREFIX . "wsb_purchase owp
                    ON owpb.purchase_id=owp.purchase_id                        
                WHERE owp.seller_id IN('".$formatted_seller_id_array."') 
                    AND oop.store_sales='".$db->escape($store_sales)."' 
                ";
                
        $query = $db->query($sql);

       if($query->num_rows>0){
            return $query->rows;
       }else{
            return false;
       }
   }
   /**
    * method for get category id of product
    * @param db object
    * @param product id array
    * @return category id
    * @author kalyan 23th Feb, 2018
    */  
   public static function getProductsCategoryId($db, $product_id_array) {

      if(empty($product_id_array)) {
        return false;
      }
      $formatted_product_id_array = implode("','", $product_id_array);

        $sql = "SELECT optc.category_id,optc.product_id 
                FROM " . DB_PREFIX . "product_to_category optc 
                WHERE optc.product_id IN('".$formatted_product_id_array."')                 
                ORDER BY optc.category_id DESC
                LIMIT 1
                ";
                
        $query = $db->query($sql);

       if($query->num_rows>0){
            
            return $query->rows;
       }else{
            return false;
       }
    }
    /**
    * method for get category id of product for stock transfer
    * @param db object
    * @param product id array
    * @return category id
    * @author kalyan 23th Feb, 2018
    */  
   public static function getProductsCategoryIdForStockTransfer($db, $product_id_array) {

      if(empty($product_id_array)) {
        return false;
      }
      $formatted_product_id_array = implode("','", $product_id_array);

        $sql = "SELECT optc.category_id,optc.product_id 
                FROM " . DB_PREFIX . "product_to_category optc 
                WHERE optc.product_id IN('".$formatted_product_id_array."')                 
                GROUP By optc.product_id  ORDER BY optc.category_id DESC 
                
                ";
                
        $query = $db->query($sql);

       if($query->num_rows>0){
            
            return $query->rows;
       }else{
            return false;
       }
    }
    /**
    * method for get slug of category
    * @param db object
    * @param category id array
    * @return category slug
    * @author kalyan 23th Feb, 2018
    */  
   public static function getCategorySlug($db, $category_id) {
      
      if(empty($category_id)) {
        return false;
      }

      $formatted_category_id = "category_id=".$category_id;
        
      $sql = "SELECT oua.keyword,oua.query
              FROM " . DB_PREFIX . "url_alias oua 
              WHERE oua.query='".$formatted_category_id."'";
         
      $query = $db->query($sql);

      if($query->num_rows>0){
            
        return $query->row;
            
      }else{
        return false;
      }
    }
    /**
    * method for get products detail
    * @param db object
    * @param products id array
    * @return product detail array slug
    * @author kalyan 23th Feb, 2018
    */  
   public static function getProductsDetail($db, $product_id_array) {

      $product_array = array();
      
      if(empty($product_id_array)) {
           return false;
      }
      $formatted_product_id_array = implode("','", $product_id_array);
       
       $sql = "SELECT  
                op.product_id,
                op.image,
                op.selling_price,
                op.sku,
                op.model,
                opd.name,                
                opd.set_description
              FROM " . DB_PREFIX . "product op
              INNER JOIN " . DB_PREFIX . "product_description opd
                ON op.product_id=opd.product_id 
              WHERE opd.product_id IN('".$formatted_product_id_array."') 
              ";
                  
        $query = $db->query($sql);

       if($query->num_rows>0){
            
            foreach ($query->rows as $key => $value) {
              $product_array[$value['product_id']] = $value;
            }
            return $product_array;

       }else{
            return false;
       }
    }
    /**
    * method for set html for email when stock transfer to particular store
    * @param product array
    * @return html
    * @author kalyan 23th Feb, 2018
    */
    public static function setProductHtmlForStockTransfer($data){

      if (!empty($data)) {

        $html = '';

        foreach($data as $datas){
          
          $html .= '<tr style="height: 43px;    margin-top: 20px;    padding-top: 50px;">
                  <td align="center" style="padding-left:8px; border-top: solid #ccc 1px;border-bottom: solid #ccc 1px;font-size:15px!important" valign="top">
                  <div style="font-weight:bold; margin-top: 10px;width: 291px;display: inline-block;vertical-align: middle;color: #000;text-align: left;">'.$datas[0]['category_name'].'</div>
                  </td>
                  <td align="center" style="border-top: solid #ccc 1px;border-bottom: solid #ccc 1px;font-size:15px!important" valign="top">
                  <div style="margin-top: 12px;width: 290px;display: inline-block;vertical-align: middle;text-align: right;"><a style="text-decoration:none;background-color: #1e91cf;color: #fff;padding: 8px;border: solid 1px #ccc;border-radius: 10px;font-size: 15px; " href="'.$datas[0]['category_url'].'">View All</a></div>
                  </td>
                </tr>
                <tr>';

          $i=0;

          foreach($datas as $proudcts){
            if ($i>1) {
              
              break;

            }else{

              $html .= '
                    <td align="center" style="font-size:0px!important" valign="top">  
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
                                        <img alt="prod4;" src="'. $proudcts['image'].'"  style="display: block;width: 100%;"/> 
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
                      </div>
                    </td>                     
                    ';
            } 
            $i++;         
          }
          $html .= '</tr>';
        }
        return $html;
      }
    }
  /**
  * Method for Bulk product CSV Upload
  * @param : $data = array of csv data
  * @return null
  * vikas , Mar 2018
  **/
  public static function applyBulkProductCsvUpload($db, $data=array()){
    
    $source_field = 'product_list';
    $validation_data = array();

    $total_record_update = array();
    if(!empty($data['products'])){
      foreach ($data['products'] as $table_name_key => $csv_records) {
        foreach ($csv_records as $product_id_key => $csv_data) {
          $total_record_update[] = $product_id_key;
          $changes_data=array();
          $string_value = '';
          foreach ($csv_data as $csv_key => $csv_value) {
            $string_value .= $csv_key."='" . $db->escape(trim($csv_value['new_value']))."', "; 
              $changes_data[$csv_key] = $csv_value;          
          } 
          $string_value = rtrim($string_value,' , ');
          if(!empty($string_value)){
              $sql ="UPDATE " .$table_name_key . "
                   SET $string_value
                   WHERE product_id = " .(int)$product_id_key ."";

            $db->query($sql);
            ProductChangeLog::recordLogsStatic($db, $product_id_key, $changes_data, $data['additional_data']); 
          }
        }           
      }
    }
    
    $validation_data['message'] = count(array_unique($total_record_update)) .' row product(s) are update in our database.';
    $validation_data['products'] =  (isset($data['validation_data']))?$data['validation_data']:array();
  
    $html = MailTemplate::applyBulkProductCSVUploadMail($validation_data);
    $config = self::getConfigData();
    $mail = new  PHPMailer();
    $mail->isSMTP();
    $mail->Host = $config->get('config_mail_smtp_hostname');
    $mail->Port = $config->get('config_mail_smtp_port');
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Username = $config->get('config_mail_smtp_username');
    $mail->Password = $config->get('config_mail_smtp_password');

    $mail->setFrom($config->get('config_mail_smtp_username'), 'WholesaleBox');
    $mail->addAddress(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
    $mail->Subject = "Bulk product csv upload  - " . date('d/M/Y H:i:s', time());
    $mail->msgHTML($html);
    $mail->send(1,false);
    return true;
  } 
  
  /**
  * Method to send COD order alert on Desktop dialer
  * @param : $data = array of csv data
  * @return null
  * MSA , April 2019
  **/
  
  public static function addAlertInDesktopDialer($db, $data=array())
  {
	  $mobile  = $data['mobile'] ?? '';
	  $list_id = $data['list_id'] ?? '';

    if(!empty($mobile) && !empty($list_id))
	  {
		 $payload = array(
						'mobile'  => $mobile, 
						'list_id' => $list_id
						);
		$url = CRM_URL.'webapi/DesktopDialer/addInDesktopDialer';
  		try{
    			$curl = curl_init($url);
    			curl_setopt($curl, CURLOPT_URL, $url);
    			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    			curl_setopt($curl, CURLOPT_POST, true);
    			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
    			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    			$response = curl_exec($curl);
    			curl_close($curl);
    			$response = json_decode($response);
          return ( $response->status ?? 0 );
  		}catch(Exception $e){
  		  return 0;
  	  } 
	 }
   return 0;
  }
  

  public static function triggerWebengageEvent($db, $data=array())
  {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.webengage.com/v1/accounts/".WEBENGAGE_LICENSE_CODE."/events",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer ".WEBENGAGE_API_KEY,
          "Content-Type: application/json",
          "cache-control: no-cache"
        ),
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);

      $response = json_decode($response);
      $response = (array)$response->response;
      if($response['status'] == 'error')
        {
          echo $response['message'].'<br />';
           return false; 
        }
        else
        {
          return true; 
        }

    }

}

