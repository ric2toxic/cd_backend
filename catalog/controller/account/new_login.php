<?php
class ControllerAccountLogin extends Controller
{
    private $error = array();

    public function index(){
        $this->LoginForm();
        $this->validateRegistration();
    }

    public function LoginForm(){

        $this->load->model('account/customer');

        // Login override for admin users
        if (!empty($this->request->get['token'])) {
            $this->event->trigger('pre.customer.login');

            $this->customer->logout();
            $this->cart->clear();

            unset($this->session->data['wishlist']);
            unset($this->session->data['payment_address']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['shipping_address']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);

            $customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);

            if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
                // Default Addresses
                $this->load->model('account/address');

                if ($this->config->get('config_tax_customer') == 'payment') {
                    $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                }

                if ($this->config->get('config_tax_customer') == 'shipping') {
                    $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                }

                $this->event->trigger('post.customer.login');

                $this->response->redirect($this->url->link('account/account', '', 'SSL'));
            }
        }

        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', 'SSL'));
        }

        $this->load->language('account/login');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateLogin()) {
            unset($this->session->data['guest']);

            // Default Shipping Address
            $this->load->model('account/address');

            if ($this->config->get('config_tax_customer') == 'payment') {
                $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            if ($this->config->get('config_tax_customer') == 'shipping') {
                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            // Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
            if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
                $this->response->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
            } else {
                $this->response->redirect($this->url->link('account/account', '', 'SSL'));
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_my_orders'),
            'href' => $this->url->link('account/order', '', 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_login'),
            'href' => $this->url->link('account/login', '', 'SSL')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_new_customer'] = $this->language->get('text_new_customer');
        $data['text_register'] = $this->language->get('text_register');
        $data['text_register_account'] = $this->language->get('text_register_account');
        $data['text_returning_customer'] = $this->language->get('text_returning_customer');
        $data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
        $data['text_forgotten'] = $this->language->get('text_forgotten');

        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_mobile_or'] = $this->language->get('entry_mobile_or');
        $data['entry_password'] = $this->language->get('entry_password');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_login'] = $this->language->get('button_login');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['action'] = $this->url->link('account/login', '', 'SSL');
        $data['register'] = $this->url->link('account/register', '', 'SSL');
        $data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
        // Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
        if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
            $data['redirect'] = $this->request->post['redirect'];
        } elseif (isset($this->session->data['redirect'])) {
            $data['redirect'] = $this->session->data['redirect'];
            unset($this->session->data['redirect']);
        } elseif(isset($_POST['seller_login'])) {
            $str = 'error=error_in_login';
            $error = base64_encode($str);
            $this->response->redirect($this->url->link('common/seller_home', 'error='.$error, 'SSL'));
        }else{
            $data['redirect'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/login.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/login.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/account/login.tpl', $data));
        }
    }

    public function RegistrationForm(){

        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', 'SSL'));
        } else {
            /**
             * Redirection to function step1
             */
            //$this->response->redirect($this->url->link('account/register/step1', '', 'SSL'));
        }

        $this->load->language('account/register');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

        $this->load->model('account/customer');

        $this->load->language('common/footer');
        $data['text_mob_pop'] = $this->language->get('text_pop_mob_for_whatsapp');
        $data['entry_whatsapp_telephone'] = $this->language->get('entry_whatsapp_telephone');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateRegistration()) {

            $arr_name = explode(" ", $this->request->post['name']);
            $this->request->post['firstname'] = $arr_name[0];
            if(isset($arr_name[1])) {
                $this->request->post['lastname'] = $arr_name[1];
            }else{
                $this->request->post['lastname'] = '';
            }

            if(empty($this->request->post['email'])){

                $this->request->post['email'] = 'noemailwsb@gmail.com';
            }
            //	echo"<pre>"; print_r($this->request->post);die;

            $customer_id = $this->model_account_customer->addCustomer($this->request->post);

            // Clear any previous login attempts for unregistered accounts.
            $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

            $this->customer->login($this->request->post['telephone'], $this->request->post['password']);

            unset($this->session->data['guest']);

            $this->response->redirect($this->url->link('account/success'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_register'),
            'href' => $this->url->link('account/register', '', 'SSL')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', 'SSL'));
        $data['text_your_details'] = $this->language->get('text_your_details');
        $data['text_your_address'] = $this->language->get('text_your_address');
        $data['text_your_password'] = $this->language->get('text_your_password');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_loading'] = $this->language->get('text_loading');

        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['entry_company'] = $this->language->get('entry_company');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_zone'] = $this->language->get('entry_zone');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_confirm'] = $this->language->get('entry_confirm');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_upload'] = $this->language->get('button_upload');
        $data['entry_referred'] = $this->language->get('entry_referred');

        if(isset($this->request->get['is_dropshipper'])){
            $data['is_dropshipper'] = $this->request->get['is_dropshipper'];
            $this->is_dropshipper = 2;
        }else {
            $data['is_dropshipper'] = 0;
        }

        //	echo $_GET['is_dropshipper']; die;

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }


        if (isset($this->error['firstname'])) {
            $data['error_firstname'] = $this->error['firstname'];
        } else {
            $data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $data['error_lastname'] = $this->error['lastname'];
        } else {
            $data['error_lastname'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['telephone'])) {
            $data['error_telephone'] = $this->error['telephone'];
        } else {
            $data['error_telephone'] = '';
        }

        if (isset($this->error['address_1'])) {
            $data['error_address_1'] = $this->error['address_1'];
        } else {
            $data['error_address_1'] = '';
        }

        if (isset($this->error['city'])) {
            $data['error_city'] = $this->error['city'];
        } else {
            $data['error_city'] = '';
        }

        if (isset($this->error['postcode'])) {
            $data['error_postcode'] = $this->error['postcode'];
        } else {
            $data['error_postcode'] = '';
        }

        if (isset($this->error['country'])) {
            $data['error_country'] = $this->error['country'];
        } else {
            $data['error_country'] = '';
        }

        if (isset($this->error['zone'])) {
            $data['error_zone'] = $this->error['zone'];
        } else {
            $data['error_zone'] = '';
        }

        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }

        if (isset($this->error['confirm'])) {
            $data['error_confirm'] = $this->error['confirm'];
        } else {
            $data['error_confirm'] = '';
        }

        $data['action'] = $this->url->link('account/register', '', 'SSL');

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name']; //$this->request->post['firstname'];
        } else {
            $data['name'] = '';
        }
        if (isset($this->request->post['firstname'])) {
            $data['firstname'] = $this->request->post['firstname']; //$this->request->post['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $data['lastname'] = $this->request->post['lastname'];
        } else {
            $data['lastname'] = '';
        }

        /*
        if (isset($this->request->post['firstname'])) {
            $data['firstname'] = $arrName[0]; //$this->request->post['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $data['lastname'] = $this->request->post['lastname'];
        } else {
            $data['lastname'] = '';
        }
        */
        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = '';
        }


        if (isset($this->request->post['telephone'])) {
            $data['telephone'] = $this->request->post['telephone'];
        } else {
            $data['telephone'] = '';
        }

        if (isset($this->request->post['company'])) {
            $data['company'] = $this->request->post['company'];
        } else {
            $data['company'] = '';
        }

        if (isset($this->request->post['address_1'])) {
            $data['address_1'] = $this->request->post['address_1'];
        } else {
            $data['address_1'] = '';
        }

        if (isset($this->request->post['address_2'])) {
            $data['address_2'] = $this->request->post['address_2'];
        } else {
            $data['address_2'] = '';
        }

        if (isset($this->request->post['postcode'])) {
            $data['postcode'] = $this->request->post['postcode'];
        } elseif (isset($this->session->data['shipping_address']['postcode'])) {
            $data['postcode'] = $this->session->data['shipping_address']['postcode'];
        } else {
            $data['postcode'] = '';
        }

        if (isset($this->request->post['city'])) {
            $data['city'] = $this->request->post['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->request->post['country_id'])) {
            $data['country_id'] = $this->request->post['country_id'];
        } elseif (isset($this->session->data['shipping_address']['country_id'])) {
            $data['country_id'] = $this->session->data['shipping_address']['country_id'];
        } else {
            $data['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($this->request->post['zone_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
        } elseif (isset($this->session->data['shipping_address']['zone_id'])) {
            $data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }

        if (isset($this->request->post['confirm'])) {
            $data['confirm'] = $this->request->post['confirm'];
        } else {
            $data['confirm'] = '';
        }

        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info) {
                $data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
            } else {
                $data['text_agree'] = '';
            }
        } else {
            $data['text_agree'] = '';
        }

        if (isset($this->request->post['agree'])) {
            $data['agree'] = $this->request->post['agree'];
        } else {
            $data['agree'] = false;
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/register.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/register.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/account/register.tpl', $data));
        }
    }

    public function validateLogin(){
        $this->event->trigger('pre.customer.login');
        $this->load->model('account/customer');
        $this->load->language('account/login');

        // Check how many login attempts have been made.

        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

        if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
            $this->error['warning'] = $this->language->get('error_attempts');
        }

        if (!$this->error) {
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {

                $this->error['warning'] = $this->language->get('error_login');

                $this->model_account_customer->addLoginAttempt($this->request->post['email']);
            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

                $this->event->trigger('post.customer.login');
            }
        }
        return !$this->error;

    }

    public function validateRegistration(){


        if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        /*
         if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }
       */

        if(!empty($this->request->post['email'] )) {
            if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
                $this->error['email'] = $this->language->get('error_email');
            }
            if ($this->model_account_customer->getTotalCustomersByEmailToValidate($this->request->post['email'])) {
                $this->error['warning'] = $this->language->get('error_exists');
            }
        }



        // if ((utf8_strlen($this->request->post['telephone']) < 10) || (utf8_strlen($this->request->post['telephone']) > 10)) {
        // 	$this->error['telephone'] = $this->language->get('error_telephone');
        // }

        if ((int)(is_numeric($this->request->post['telephone'])) == 0 || utf8_strlen($this->request->post['telephone']) !=  10) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        if (!empty($this->request->post['telephone']) && $this->model_account_customer->getTotalCustomersByTelephone($this->request->post['telephone'])) {
            $this->error['warning'] = $this->language->get('error_exists_phone');
        }

        if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
            $this->error['address_1'] = $this->language->get('error_address_1');
        }

        if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
            $this->error['city'] = $this->language->get('error_city');
        }

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

        if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
            $this->error['postcode'] = $this->language->get('error_postcode');
        }

        if ($this->request->post['country_id'] == '') {
            $this->error['country'] = $this->language->get('error_country');
        }

        if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
            $this->error['zone'] = $this->language->get('error_zone');
        }

        if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        }

        // Agree to terms
        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

            if ($information_info && !isset($this->request->post['agree'])) {
                $this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
            }
        }

        return !$this->error;
    }
}
