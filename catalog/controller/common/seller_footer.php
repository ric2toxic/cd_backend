<?php
class ControllerCommonSellerFooter extends Controller {
    public function index() {

        $this->load->language('common/footer');
        $this->load->model('account/customer');
        $data['route'] = '';
        if(isset($this->request->get['route']) && $this->request->get['route'] != '') {
            $data['route'] = $this->request->get['route'];
        }

        $data['is_home'] = 0;
        if (!isset($this->request->get['route']) ) {
            $data['is_home'] = 1;
        }

        $data['popup'] = false;
        if (isset($this->request->get['popup'])) {
            $data['popup'] = $this->request->get['popup'];
        }
        /** MObile number popup ** */
        if (isset($_COOKIE['mobile']) && $_COOKIE['mobile'] != '' ) {
            $data['pop'] = '0';
        }else{
            $data['pop'] = '1';
        }
        if(isset($_COOKIE['skip'])){
            $data['skip'] = 1;
        }else{
            $data['skip'] = 0;
        }
        $data['referral'] = $this->url->link('common/header/referralUrl', '', 'SSL');

        $data['text_mob_pop'] = $this->language->get('text_pop_mob_for_whatsapp');
        $data['text_information'] = $this->language->get('text_information');
        $data['text_service'] = $this->language->get('text_service');
        $data['text_extra'] = $this->language->get('text_extra');
        $data['text_contact'] = $this->language->get('text_contact');
        $data['text_return'] = $this->language->get('text_return');
        $data['text_sitemap'] = $this->language->get('text_sitemap');
        $data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $data['text_voucher'] = $this->language->get('text_voucher');
        $data['text_affiliate'] = $this->language->get('text_affiliate');
        $data['text_special'] = $this->language->get('text_special');
        $data['text_account'] = $this->language->get('text_account');
        $data['text_order'] = $this->language->get('text_order');
        $data['text_wishlist'] = $this->language->get('text_wishlist');
        $data['text_logout'] = $this->language->get('text_logout');
        $data['text_register'] = $this->language->get('text_register');
        $data['text_login'] = $this->language->get('text_login');
        $data['text_dropshipper'] = $this->language->get('text_dropshipper');
        $data['register'] = $this->url->link('account/register', 'static=register', 'SSL');
        $data['login'] = $this->url->link('account/login', 'static=login', 'SSL');
        $data['dropshipper'] = $this->url->link('account/dropshipper', 'static=dropshipper', 'SSL');

        $data['text_ad_heading'] = $this->language->get('text_ad_heading');
        $data['text_ad_list'] = $this->language->get('text_ad_list');
        $data['text_shop_now'] = $this->language->get('text_shop_now');
        $data['text_Affiliate'] = $this->language->get('text_Affiliate');

        $this->load->model('catalog/information');

        $data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['bottom']) {
                $data['informations'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        /**
         * Checking seller agreement status
         * */
        if($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())){
            //echo "here"; die;
            if ($this->config->get('msconf_seller_terms_page')) {
                $this->load->model('catalog/information');
                $information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

                if ($information_info) {
                    $data['seller_terms'] = sprintf($this->language->get('ms_account_sellerinfo_terms_note'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('msconf_seller_terms_page'), 'SSL'), $information_info['title'], $information_info['title']);
                }
            }
            $seller_agreement_status = $this->MsLoader->MsSeller->getSellerAgreementStatus($this->customer->getId());
            $seller_approval = $this->MsLoader->MsSeller->sellerApproval();

            if($seller_agreement_status == 0 && $seller_approval == 1){
                //echo "here"; die;
                $data['logging_in'] = 1;
            }

        }

        $data['contact'] = $this->url->link('information/contact');
        $data['return'] = $this->url->link('account/return/add', '', 'SSL');
        $data['sitemap'] = $this->url->link('information/sitemap');
        $data['manufacturer'] = $this->url->link('product/manufacturer');
        $data['voucher'] = $this->url->link('account/voucher', '', 'SSL');
        $data['affiliate'] = $this->url->link('affiliate/account', '', 'SSL');
        $data['special'] = $this->url->link('product/special');
        $data['account'] = $this->url->link('account/account', '', 'SSL');
        $data['order'] = $this->url->link('account/order', '', 'SSL');
        $data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
        $data['affiliate'] = $this->url->link('affiliate/login', '', 'SSL');

        $data['seller_agreement_link'] = htmlspecialchars_decode($this->url->link('information/information/agree', 'information_id=' . $this->config->get('msconf_seller_terms_page'), 'SSL'));

        $data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

        $data['logged'] = $this->customer->isLogged();

        if ($data['logged']) {
            $customer_id = $this->customer->getId();
            $data['is_dropshipper'] = $this->model_account_customer->getisdropshipper($customer_id);
            //	print_r($data['is_dropshipper']); die;
            $dropshipper = $data['is_dropshipper'];
        }

        $data['logout'] = $this->url->link('account/logout', '', 'SSL');

        // Whos Online
        if ($this->config->get('config_customer_online')) {
            $this->load->model('tool/online');

            /*if (isset($this->request->server['REMOTE_ADDR'])) {
                $ip = $this->request->server['REMOTE_ADDR'];
            } else {
                $ip = '';
            }*/

            $ip = $this->request->getIpAddress;

            if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
                $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            } else {
                $url = '';
            }

            if (isset($this->request->server['HTTP_REFERER'])) {
                $referer = $this->request->server['HTTP_REFERER'];
            } else {
                $referer = '';
            }

            $this->model_tool_online->whosonline($ip, 
                                                 $this->customer->getId(), 
                                                 $this->config->get('config_store_id'), 
                                                 $url, 
                                                 $referer);
        }
        if($data['popup'] == true){
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/seller_footer_popup.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/common/seller_footer_popup.tpl', $data);
            } else {
                return $this->load->view('default/template/common/footer-popup.tpl', $data);
            }
        }else {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/seller_footer.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/common/seller_footer.tpl', $data);
            } else {
                return $this->load->view('default/template/common/footer.tpl', $data);
            }
        }
    }

}
