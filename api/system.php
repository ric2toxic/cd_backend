<?php

require_once(DIR_SYSTEM . 'engine/controller.php');
require_once(DIR_SYSTEM . 'startup_api.php');
require_once(DIR_SYSTEM . 'library/customer.php');
require_once( DIR_SYSTEM . 'library/tax.php');

class SystemController extends Controller
{

    protected $registry;
    protected $data_packet;
   // protected $response_error;
    protected $method;
    protected $args;
    protected $request;
    protected $requested_api;
    protected $city_seller_helpline;
    protected $getIpAddress;

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    public function __construct($params) {

        //data Packet
        $this->data_packet = new DataPacket();

        //error Packet
        //$this->response_error = new ResponseError();

        // Registry
        $this->registry = new Registry();

        // Loader
        $loader = new Loader($this->registry);
        $this->registry->set('load', $loader);

        // Database
        $db = new Database\DB( DB_SERVERS );
        $this->registry->set('db', $db);

        // Cache
        $cache = new Cache('file');
        $this->registry->set('cache', $cache);

        // Creating the finalized Language object and storing in registry (set english default)
        $language = new Language('english');
        $language->load('english');
        $this->registry->set('language', $language);

        $config = new Config();


        // Store
        $store_id = 0; 
        $ssl_url = 'http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/'; 

       if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) 
        {
          $ssl_url = 'https://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/'; 
        }

        //remove api folder in url
        $ssl_url = str_replace('/api', '', $ssl_url);
       
        $store_cache = $cache->get('store');
        if (!empty($store_cache)) 
        {
          foreach($store_cache as $store)
          {
           if($store['ssl'] == $ssl_url) 
           { 
             $store_id = $store['store_id']; 
             break;
           }
          }
        }
       else
        {
          $store_query = $db->query("SELECT `store_id`, `name`, `url`, `ssl` FROM " . DB_PREFIX . "store  ORDER BY url"); 
          $cache->set('store', $store_query->rows);
          foreach($store_query->rows as $store)
          {
            if($store['ssl'] == $ssl_url) 
            { 
              $store_id = $store['store_id']; 
              break;
             }
          }
        }
         $config->set('config_store_id', $store_id);
         $config->set('config_language_id','1');

         if ($store_id == 0) {
            $config->set('config_url', HTTP_SERVER);
            $config->set('config_ssl', HTTPS_SERVER);
          }
 

        // Settings
        $store_setting_cache = $cache->get('store_setting');
        if(empty($store_setting_cache))
         {
           $query = $db->query("SELECT `setting_id`, `serialized`, `value`, `store_id`, `key`, `code`  FROM `" . DB_PREFIX . "setting` ORDER BY store_id ASC");
            $store_setting_cache = $query->rows;
           $cache->set('store_setting', $store_setting_cache);
         }

       foreach ($store_setting_cache as $result) 
        {
           if($result['store_id'] == 0 || $result['store_id'] == $store_id)
           {
             if (!$result['serialized']) 
             {
               $config->set($result['key'], $result['value']);
             } 
             else 
             {
             $config->set($result['key'], unserialize($result['value']));
              }
            }    
        }
        
        $this->registry->set('config', $config);
        // Request
        $request = new Request();
        $this->registry->set('request', $request);

        // Session - to be created only when it is not App
        $session = new Session();
        $this->registry->set('session', $session);

        $session->data['language'] = 'en';

        // Currency
        $currency = new Currency($this->registry);
        $this->registry->set('currency', $currency);

        // Tax
        $tax = new Tax($this->registry);
        $this->registry->set('tax', $tax);

        // Rest API Object
        $obj_restapi = new Restapi($this->registry);
        $this->registry->set('restapi', $obj_restapi);

        // Log
        $log = new Log($config->get('config_error_filename'));
        $this->registry->set('log', $log);

         //response for ajax request 
        $ajax = new ajax;
        $ajax->HTTP_ORIGIN();

        // Error Handler
        set_error_handler(array($this, 'error_handler'));


        // Url
        $url = new Url($config->get('config_url'),$config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'),$this->registry);
        $this->registry->set('url', $url);

        // seller helpline number
        $this->city_seller_helpline = array(
            'ST'=>array(array('contact_type'=> 'Primary', 'contact_name'=> 'Ram', 'contact_number'=> '9898256409'),array('contact_type'=> 'Secondary', 'contact_name'=> 'Jitendra', 'contact_number'=> '9909330036')),
            'DL'=>array(array('contact_type'=> 'Primary', 'contact_name'=> 'Vishal Pandey', 'contact_number'=> '8744835078'),array('contact_type'=> 'Secondary', 'contact_name'=> 'Ratan Srivastava', 'contact_number'=> '9971748657')),
            'JP'=>array(array('contact_type'=> 'Primary', 'contact_name'=> 'Nitesh', 'contact_number'=> '8890081605'),array('contact_type'=> 'Secondary', 'contact_name'=> 'Nitin', 'contact_number'=> '9916199960')),
            'MU'=>array(array('contact_type'=> 'Primary', 'contact_name'=> 'Nitesh', 'contact_number'=> '9702006014'))
        );


        // Customer
        $this->registry->set('customer', new Customer($this->registry));


        // Event
        $event = new Event($this->registry);
        $this->registry->set('event', $event);
        /*$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "event");

        foreach ($query->rows as $result) {
            $event->register($result['trigger'], $result['action']);
        }*/

        // Assign Method / Parameters/ Request Data
        $this->method = $params['method']; //method - POST/GET/PUT/DELETE
        $this->args = $params['args']; //QueryString

        $this->request = $request->request;//$params['request']; //post data
        $this->requested_api = $params['requested_api']; // api path
        $this->getIpAddress = $request->getIpAddress;

        // decode request data
        if(!empty($this->request['data']))
        {
           $request_data  = decodeApiData($this->request['data']); 
           $this->request = array_merge($this->request, $request_data);
        }

        // check if user is authenticated
        $listAuthApi = json_decode(file_get_contents(__DIR__."/list_auth_api.json"));
         
        //for "list_auth_api.json" api check 
        if(MAINTENANCE_MODE)
        {
          $this->data_packet->maintenanceMode();  
        }
        else if(isset($this->request['request']) && in_array($this->request['request'], $listAuthApi->auth_api))
        {
           if(!$this->__userAuthentication())
           {
             $this->data_packet->authenticationFail();
           }
        }
        //for seller api check
        else if(isset($this->request['request']) && (strpos($this->request['request'], 'sellers/orders') !== false 
                                                    || strpos($this->request['request'], 'sellers/payment') !== false 
                                                    || strpos($this->request['request'], 'sellers/profile') !== false 
                                                    || strpos($this->request['request'], 'sellers/replacement') !== false 
                                                    || strpos($this->request['request'], 'sellers/report') !== false 
                                                    || strpos($this->request['request'], 'sellers/returns') !== false 
                                                    || strpos($this->request['request'], 'sellers/sor') !== false))
        {   
            $result = $this->__userAuthentication();
            if(!empty($result['seller_id']) && !empty($result['nickname']) )
            { 
              if($result['seller_id'] == $result['nickname'] && strpos($this->request['request'], 'sellers/profile') === false)
               {
                  
                   $this->data_packet->sellerInactive();
               }
            }
            else
            {    
                 
                 $this->data_packet->authenticationFail();
            } 
        }
        else
        {
           $this->__userAuthentication(); 
        }


      //Set DataPacket object for repsonse
      $this->data_packet = new DataPacket();

    }


    protected function __userAuthentication()
    {
            if(!empty($this->request['customer_access_token']) && !empty($this->request['customer_id']))
            {
                $token       = $this->request['customer_access_token'];
                $customer_id = $this->request['customer_id'];

                $sql_auth = "SELECT oc.customer_id, oc.customer_access_token, ms.seller_id, ms.nickname FROM " . DB_PREFIX . "customer oc LEFT JOIN " . DB_PREFIX . "ms_seller ms on oc.customer_id = ms.seller_id  WHERE oc.customer_id = '".(int)$customer_id."' LIMIT 1";

                $rs_auth = $this->db->query($sql_auth);
                if($rs_auth->num_rows > 0 && $rs_auth->row['customer_access_token'] == $token)
                {        
                        $result = $rs_auth->row; 
                        if($this->customer->getId() != $customer_id)
                        {
                          //access token verfied and login by customer id with override true flag
                          $this->customer->login($result['customer_id'],'',1);
                          return $result;
                         }
                         else
                         {
                           return $result; 
                         }
                } 
                else
                {
                   return false;
                }

            }else{

                return false;
            }

    }

    public function error_handler($errno, $errstr, $errfile, $errline) {
        // error suppressed with @
        if (error_reporting() === 0) {
            return false;
        }

        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $error = 'Warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                break;
            default:
                $error = 'Unknown';
                break;
        }

        $debug_mode = false;
        if (isset($_GET['debug']) and $_GET['debug'] == 'yup') {
            echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
            $debug_mode = true;
        }
        //if ($this->config->get('config_error_display')) {
            echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
        //}

        ///*if ($config->get('config_error_log')) {
        //$log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
        //}*/

        if ($error == 'Fatal Error' and !$debug_mode) { // String should match exactly to the one mentioned above
            $redirect_url = sprintf("http://%s%smaintenance.html",
                /*isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',*/
                $_SERVER['SERVER_NAME'],
                $_SERVER['REQUEST_URI']);

            header("Location: " . $redirect_url);
            exit();

        }

        return true;
    }

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

 public function get_images_data($image,$dimensions=null)
        {
              if ($_SERVER['HTTPS']) {
                  $static_content_url =  STATIC_CONTENT_URL_SSL;
               } else {
                  $static_content_url =  STATIC_CONTENT_URL ;
               }

               $result = array();
            $this->load->model('tool/image');
                 // $extension  = pathinfo($image, PATHINFO_EXTENSION);
            if(!empty($dimensions['width']))
                $width_orig = $dimensions['width'];
            if(!empty($dimensions['height']))
                $height_orig = $dimensions['height'];

                 if (!empty($width_orig) && !empty($height_orig) && ($width_orig / $height_orig) > 1)
                  {
                      $result['popup']              =  $this->model_tool_image->resize($image,$this->config->get('config_image_popup_height'),$this->config->get('config_image_popup_width'));
                      $result['pan_detail']         =  $this->model_tool_image->resize($image,332,221);
                      $result['thumb']              =  $this->model_tool_image->resize($image, $this->config->get('config_image_additional_height') , $this->config->get('config_image_additional_width') );
                      $result['image']              =  $this->model_tool_image->resize($image, $this->config->get('config_image_product_height') , $this->config->get('config_image_product_width') );
                      $result['original']           =  $this->model_tool_image->getOriginalImage($image);
                      $result['popup_width']      =  $this->config->get('config_image_popup_height');
                      $result['popup_height']      = $this->config->get('config_image_popup_width');
                      $result['pan_width']         =  332;
                      $result['pan_height']        = 221;
                      $result['additional_width']  = $this->config->get('config_image_additional_height');
                      $result['additional_height'] = $this->config->get('config_image_additional_width');
                      $result['width']             = $this->config->get('config_image_product_height');
                      $result['height']            = $this->config->get('config_image_product_width');
                      $result['img_related_width']  = $this->config->get('config_image_related_height');
                      $result['img_related_height'] = $this->config->get('config_image_related_width');
                      $result['img_vertical'] = false;

                } else {
                     $result['popup']              =  $this->model_tool_image->resize($image,$this->config->get('config_image_popup_width'),$this->config->get('config_image_popup_height'));
                     $result['pan_detail']         =  $this->model_tool_image->resize($image,332,497);
                     $result['thumb']              =  $this->model_tool_image->resize($image, $this->config->get('config_image_additional_width') , $this->config->get('config_image_additional_height') );
                     $result['image']              =  $this->model_tool_image->resize($image, $this->config->get('config_image_product_width') , $this->config->get('config_image_product_height') );
                     $result['original']           =  $this->model_tool_image->getOriginalImage($image);

                    $result['popup_width']        =  $this->config->get('config_image_popup_width');
                    $result['popup_height']       =  $this->config->get('config_image_popup_height');
                    $result['pan_width']          =  332;
                    $result['pan_height']         =  497;
                    $result['additional_width']   =  $this->config->get('config_image_additional_width');
                    $result['additional_height']  =  $this->config->get('config_image_additional_height');
                    $result['width']              = $this->config->get('config_image_product_width');
                    $result['height']             = $this->config->get('config_image_product_height');
                     $result['img_related_width']  = $this->config->get('config_image_related_width');
                    $result['img_related_height'] = $this->config->get('config_image_related_height');
                    $result['img_vertical'] = true;
                }

           return $result;
       }

  public function send_mail_or_sms($email, $message, $subject, $password)
    {
      //echo $message;
        $this->load->language('account/sms_templates');
        $this->load->language('account/forgotten');
        if (is_numeric($email) && SITE_ENVIRONMENT =='Production')
        {
            $send_sms = new SMS($message, $email);
            $send_sms->sendOTPMessage();
        } else {

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
      $mail->Subject =  html_entity_decode("OTP", ENT_QUOTES, 'UTF-8');
      $mail->msgHTML($message);
      $mail->send();

        }
    }
}
