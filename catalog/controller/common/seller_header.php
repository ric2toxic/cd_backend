<?php
class ControllerCommonSellerHeader extends Controller {
    private $error = array();
    public function index(){
        $this->document->addScript('catalog/view/theme/default/javascript/jquery_sortable.js');
         $this->load->model('tool/image');
        $data = array();

        $data['color_template'] = 'orange_cyan';

        if(!empty($this->customer->getId())) {
            $data['strs'] = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());
            if(isset($data['strs']['store_id]'])){
                foreach ($data['strs'] as $st) {
                    $data['stores'][] = $this->MsLoader->MsProduct->getStore($st['store_id']);
                }
            }
        }

        if(isset($this->request->post['form_button']) && $this->request->post['form_button']== 'log-button' && $this->loginValidation())
        {
            $redirect_url =  $this->login_process();
            $this->response->redirect($redirect_url);
        }

        $stores = array();
        $this->load->language('common/seller_header');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_password'] = $this->language->get('text_password');
        $data['text_submit'] = $this->language->get('text_submit');

        $data['text_seller_login'] = $this->language->get('text_seller_login');
        $data['text_forgot_password'] = $this->language->get('text_forgot_password');
        $data['text_seller_hub'] = $this->language->get('text_seller_hub');
        $data['action'] = $this->url->link('common/seller_home/seller_login', '', 'SSL');
        $data['logged'] = $this->customer->isLogged();
        $data['logout'] = $this->url->link('account/logout', '', 'SSL');
        $sort_by_pid = 'product_id';
        $data['manage'] = $this->url->link('seller/manage-inventory', 'sort='.$sort_by_pid.'&order=DESC', 'SSL');
        $data['archive'] = $this->url->link('seller/manage-inventory&product_archived=1', '', 'SSL');
        $data['import_inventory'] = $this->url->link('seller/update_inventory', '', 'SSL');
        $data['update_bulk_price'] = $this->url->link('seller/update_bulk_price', '' , 'SSL');
        $data['coupons'] = $this->url->link('seller/coupon', '' , 'SSL');
        $data['reviews'] = $this->url->link('seller/review', '' , 'SSL');
		$data['import'] = $this->url->link('seller/seller_upload', '', 'SSL');
		//$this->url->link('seller/test', '', 'SSL');
		
        $seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
        $seller_id      = $seller['seller_id'];
        $data['seller_status'] = $seller['ms.seller_status'];
        $data['gst_provisional_id'] = $seller['ms.gst_provisional_id'];
        $data['gst_popup_link_click_here'] = $this->url->link('seller_panel/profile#Business','','SSL');
        if(isset($this->session->data['seller_store_id'])) { 
            $store_id = $this->session->data['seller_store_id'];
        }else{
            $store_id = $this->data['strs'][0]['store_id'];
        }
        $theme = $this->customer->getStoreConfigForSeller($store_id, 'theme_seller');
        $data['color_template'] = $theme;

        if(!file_exists(DIR_TEMPLATE."default/stylesheet/colors/".$data['color_template']."/".$data['color_template'].".css")){
            $data['color_template'] = 'default';
        }

        $seller_approval = $this->MsLoader->MsSeller->sellerApproval();

        if($seller_approval == 1){
            $data['seller_approval'] = 1;
        }else{
            $data['seller_approval'] = 0;
        }

        $data['text_logout'] = $this->language->get('text_logout');
        $data['dashboard'] = $this->language->get('dashboard');
        $data['scripts'] = $this->document->getScripts();
        $data['direction'] = $this->language->get('direction');
        $data['lang'] = $this->language->get('code');
        $data['text_search'] = $this->language->get('text_search');
        $data['name'] = $this->config->get('config_name');

        if (isset($this->request->get['search'])) {
            $data['searchText'] = $this->request->get['search'];
        } else {
            $data['searchText'] = '';
        }
        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }
        if(isset($_GET['error'])) {
            $data['error_login'] = base64_decode($_GET['error']);
        }
        $data['base'] = $server;

        $data['logo'] = $this->model_tool_image->getOriginalImage($this->customer->getStoreConfigForSeller($store_id, 'config_logo'));
        $data['mobile_logo'] = $this->model_tool_image->getOriginalImage('logo_seller.png');

        /*if (is_file(DIR_IMAGE . $this->customer->getStoreConfigForSeller($store_id, 'config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->customer->getStoreConfigForSeller($store_id, 'config_logo');
            $data['mobile_logo'] = $server . 'image/logo_seller.png';
        } else {
            $data['logo'] = '';
            $data['mobile_logo'] = $server . 'image/logo_seller.png';
        }*/

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);
        //echo "<pre>"; print_r($categories); exit;
        foreach ($categories as $category) {
            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }

                // Level 1
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'children' => $children_data,
                   'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
                
            }
        }
        if(!empty($_COOKIE['app_link_close']) && isset($_COOKIE['app_link_close'])){
            $data['app_link'] = 1;
        }else{
            $data['app_link'] = 0;
        }
        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search'] = $this->load->controller('common/search');
        $data['search_mobile'] = $this->load->controller('common/search_mobile');
        $data['seller_image'] = '';

        if ($this->customer->isLogged() && $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())) {
            $data['seller_image'] = '../image/seller_panel_ad.jpg';
        }

        // seller store_id save in session
        $this->sessionStoreIDSellers();
        
        //forgot password popup
        $this->load->form('register_form');
        $RegisterForm                = new RegisterForm();
        $data['send_email_otp_form'] = $RegisterForm->send_email_otp_form();
        $data['verify_otp_form']     = $RegisterForm->verify_otp_form();
        $data['send_otp_url']        = $this->url->link('common/seller_header/send_otp', '', 'SSL');
        $data['verify_otp_from_url'] = $this->url->link('common/seller_header/verify_otp', '', 'SSL');
        
        $data['popup'] = false;
        if (isset($this->request->get['popup'])) {
            $data['popup'] = $this->request->get['popup'];
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        } 


        if($data['popup'] == true){
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/seller_header_popup.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/common/seller_header_popup.tpl', $data);
            } else {
                return $this->load->view('default/template/common/seller_header_popup.tpl', $data);
            }
        }else { 
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/seller_header.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/common/seller_header.tpl', $data);
            } else {
                return $this->load->view('default/template/common/seller_header.tpl', $data);
            }
        }
        
    }

    public function app_link(){
        //echo "<pre>";print_r($this->request->post['app_data']); echo "</pre>";die;
        if(!empty($this->request->post['app_data']) && isset($this->request->post['app_data'])){
            $app_data = $this->request->post['app_data'];
            setcookie("$app_data", $app_data, time() + (86400 * 1), "/",".".HTTP_DOMAIN ); // 86400 = 1 day
            echo "success"; exit;
        }

    }


    // store_id of seller's are saved in session for seller panel by vikas (05-05-2016)
    public function sessionStoreIDSellers(){
        if (!empty($this->customer->getId())) {
            $data['storeid'] = $this->MsLoader->MsProduct->getSellerStores($this->customer->getId());

            if (isset($this->session->data['seller_store_id']) && !empty($this->session->data['seller_store_id'])) {
                $this->session->data['seller_store_id'] = $this->session->data['seller_store_id'];
            } else {
                if (count($data['storeid']) > 1 && isset($this->session->data['seller_store_id'])) {
                    $this->session->data['seller_store_id'] = $this->session->data['seller_store_id'];
                } else {
                    $this->session->data['seller_store_id'] = $data['storeid'][0]['store_id'];
                }

            }

        }
    }


    public function send_otp()
    {
      $ajax = new ajax;
      $ajax->HTTP_ORIGIN();
      $result = array('error'=>1);

   if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
     {
      $this->load->form('register_form');
      $this->load->language('account/sms_templates');
      $this->load->language('account/login');
      $this->load->model('account/customer');
      $RegisterForm = new RegisterForm();
      if(isset($this->request->post['reg_telephone']))
        {
    
          $emailValidation = $RegisterForm->emailValidation($this->request->post);
        if ($emailValidation['valid'])
          {
            $result['reg_telephone'] = $this->request->post['reg_telephone'];
            $user_data = $this->model_account_customer->getCustomerByEmailOrMobile($result['reg_telephone']);
            if(count($user_data) > 0)
            {
                $result['session_id']    = session_id();
                $result['ip']            = $this->request->getIpAddress;
                $result['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                $result['otp']           = (SITE_ENVIRONMENT =='Production')?rand(1000, 9999):1010;
                $result['otp_page']      = 'forgot_password';
                $result['customer_id']   = $user_data['customer_id'];
                $user_otp = $this->model_account_customer->addOTP($result);
                if($user_otp > 0) { $result['otp'] = $user_otp; }
              
                $message   = $this->language->get('on_app_signup_email');
                $verify_link = $this->url->link('common/login_popup/email_otp_verify', '', 'SSL').'&otp='.base64_encode($result['otp']);
                $message   = sprintf($message,$result['otp'], $verify_link);
                
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
                $mail->setFrom(EMAIL_IDS['sellers']['email_id'], EMAIL_IDS['sellers']['name']);
                $mail->addAddress($result['reg_telephone'], "WholesaleBox");
                $mail->Subject =  html_entity_decode("OTP", ENT_QUOTES, 'UTF-8');
                $mail->msgHTML($message);
                $mail->send();

                $result['otp']   = ''; 
                $result['error'] = 0;
                $result['msg']   = sprintf($this->language->get('otp_success_email'), $result['reg_telephone']);
            }
            else
            {
               $result['msg']   = 'Email address not found in database!';
               $result['error'] = 1;      
            }

          }
          else
          {
            $result['msg']   = $mobileValidation['errors']['message'];
            $result['error'] = 1;
          }
       
       }
      
      }
        header('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));
    }


    public function verify_otp()
    {
      $ajax = new ajax;
      $ajax->HTTP_ORIGIN();
      $result = array('error'=>1);

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    {
      $this->load->language('account/login');
      $this->load->model('account/customer');
      if(isset($this->request->post['otp']))
        {

            $result['otp_page']      = "forgot_password";
            $result['reg_telephone'] = $this->request->post['reg_telephone'];
            $result['otp']           = $this->request->post['otp'];
            $check_data     = $this->model_account_customer->checkOTP($result);
            if(count($check_data) > 0)
            {
                $this->model_account_customer->verifyOTP($check_data);
                $access_token  = $this->generate_access_token();
                if($this->customer->login($result['reg_telephone'], $result['otp'], true, false, $access_token))
                {
                    $redirect_url    =  $this->login_process();
                    $result['url']   =  $redirect_url;
                    $result['msg']   =  $this->language->get('text_login_success');
                    $result['error'] = 0;
               }
            }
            else
            {
                $result['msg']   = 'Please enter a valid OTP';
                $result['error'] = 1;
            }
        }
     }
        header('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));

    }


    private function loginValidation()
    {   
        $this->load->language('account/login');
         $this->load->model('account/customer');
        $this->event->trigger('pre.customer.login');
        $access_token  = $this->generate_access_token();
        if (!$this->customer->login($this->request->post['email'], $this->request->post['password'], false, false, $access_token))
            {
                $this->error['warning'] = $this->language->get('error_login');
                $this->model_account_customer->addLoginAttempt($this->request->post['email']);
               
            }
            else
            {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

                $this->event->trigger('post.customer.login');
                $this->load->model('lead/lead');
                $telephone = $this->customer->getTelephone();
                if (!empty($telephone)) {
                    $lead_data = [
                        'last_login_date'   => date('Y-m-d H:i:s')
                    ];
                    $this->model_lead_lead->updateLead($lead_data, $telephone, 'Last Login', $this->customer->getId() );
                }
            }
        return !$this->error;
    }

    private function login_process()  {
        unset($this->session->data['guest']);
        $this->load->model('account/address');
        $this->load->model('account/customer');

        if ($this->config->get('config_tax_customer') == 'payment')
            {
                $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }
        if ($this->config->get('config_tax_customer') == 'shipping')
            {
            $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }


       //Add access token
        $access_token = $this->model_account_customer->addCustomerToken($this->customer->getId());

        if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
                $redirect_url = str_replace('&amp;', '&', $this->request->post['redirect']);
            }
        else {
            if (!empty($this->request->post['referrers']) && $this->request->post['referrers']=='cart')
               {
                    $redirect_url = $this->url->link('checkout/one_page_checkout', '', 'SSL');
                }
            else {
                    if (!CONFIG_IS_MOBILE && $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId()))
                    {
                        if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_ACTIVE)
                           {
                              $redirect_url =  $this->url->link('seller_panel/account-order', '', 'SSL');
                            }
                        else if($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE)
                            {
                                $redirect_url =  $this->url->link('seller_panel/profile', '', 'SSL');
                            }
                        else{
                                $redirect_url =  $this->url->link('account/logout', '', 'SSL');
                            }
                    }
                    else
                       {
                            $redirect_url =  $this->url->link('account/order', '', 'SSL');
                        }
                }
            }
        return $redirect_url;
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


}
