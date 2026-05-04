<?php
class ControllerSellerPanelSellerHeader extends Controller {
    public function index(){
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

        // get seller account status by field name is seller_status
        $this->load->model('seller_panel/profile');
        $data['get_seller_status'] = $this->model_seller_panel_profile->getSellersInformation($this->customer->getId(),'seller_status');
        $data['get_seller_nickname'] = $this->model_seller_panel_profile->getSellersInformation($this->customer->getId(),'nickname');
        $data['account_profile'] = $this->url->link('seller_panel/profile','','SSL');
        $data['account_dashboard'] = $this->url->link('seller_panel/account-order','','SSL');
        $data['account_order'] = $this->url->link('seller_panel/account-order/getPickpupOrderRequested','','SSL');
        $data['manage_inventory'] = $this->url->link('seller/manage-inventory', 'sort=product_id&order=DESC', 'SSL');
        $data['account_logout'] = $this->url->link('account/logout', '', 'SSL');
        $data['change_password'] = $this->url->link('account/password','', 'SSL');
        $data['order_return'] = $this->url->link('seller_panel/account-order/getOrderReturn','', 'SSL');
        $data['order_payment_report'] = $this->url->link('seller_panel/paymentreports','', 'SSL');
        $data['gst_report'] = $this->url->link('seller_panel/gstreports','', 'SSL');
        $data['sor_inventory_link'] = $this->url->link('seller_panel/account-order/sorInvoices','', 'SSL');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/seller_panel/seller_header.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/seller_panel/seller_header.tpl', $data);
        } else {
            return $this->load->view('default/template/seller_panel/seller_header.tpl', $data);
        }


        /*$this->document->addScript('catalog/view/theme/default/javascript/jquery_sortable.js');

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
        $stores = array();

        $this->load->language('common/seller_header');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_password'] = $this->language->get('text_password');
        $data['text_submit'] = $this->language->get('text_submit');

        $data['text_seller_login'] = $this->language->get('text_seller_login');
        $data['text_seller_hub'] = $this->language->get('text_seller_hub');
        $data['action'] = $this->url->link('account/login', '', 'SSL');
        $data['logged'] = $this->customer->isLogged();
        $data['logout'] = $this->url->link('account/logout', '', 'SSL');
        $sort_by_pid = 'product_id';
        $data['manage'] = $this->url->link('seller/manage-inventory', 'sort='.$sort_by_pid.'&order=DESC', 'SSL');
        $data['import_inventory'] = $this->url->link('seller/update_inventory', '', 'SSL');
        $data['update_bulk_price'] = $this->url->link('seller/update_bulk_price', '' , 'SSL');
        $data['coupons'] = $this->url->link('seller/coupon', '' , 'SSL');
        $data['reviews'] = $this->url->link('seller/review', '' , 'SSL');
        $data['add_products'] = $this->url->link('seller/product/add', '', 'SSL');

        $data['account_dashboard'] = $this->url->link('seller_panel/account-order','','SSL');
        $data['account_profile'] = $this->url->link('seller/account-profile', '', 'SSL');

        $seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
        $seller_id      = $seller['seller_id'];

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
        if (is_file(DIR_IMAGE . $this->customer->getStoreConfigForSeller($store_id, 'config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->customer->getStoreConfigForSeller($store_id, 'config_logo');
            $data['mobile_logo'] = $server . 'image/logo_seller.png';
        } else {
            $data['logo'] = '';
            $data['mobile_logo'] = $server . 'image/logo_seller.png';
        }
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
        $data['cart'] = $this->load->controller('common/cart');
        $data['seller_image'] = '';
        $data['add_upcoming_design'] = $this->url->link('seller/add-upcoming-designs/getList', '', 'SSL');

        if ($this->customer->isLogged() && $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())) {
            $data['seller_image'] = '../image/seller_panel_ad.jpg';
        }

        // seller store_id save in session
        $this->sessionStoreIDSellers();

        $data['popup'] = false;
        if (isset($this->request->get['popup'])) {
            $data['popup'] = $this->request->get['popup'];
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
        }*/

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

}