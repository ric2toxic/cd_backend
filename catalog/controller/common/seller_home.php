<?php
class ControllerCommonSellerHome extends Controller {
    public function index()
    {
        $data = array();
        $this->load->language('common/seller_header');
        $data['text_name'] = $this->language->get('text_company');
        $data['text_sign_up'] = $this->language->get('text_sign_up');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_reg_now']= $this->language->get('text_reg_now');
        $data['benefit']= $this->language->get('benefits');
        $data['header'] = $this->load->controller('common/seller_header');
        $data['footer'] = $this->load->controller('common/seller_footer');
        $data['action'] = $this->url->link('account/register-seller', '', 'SSL');
        $data['text_areuamanu'] = $this->language->get('text_areuamanu');
        $data['benefit_mobile'] = $this->language->get('benefit_mobile');
        $data['text_important_1'] = $this->language->get('text_important_1');
        $data['text_important_2'] = $this->language->get('text_important_2');
        $data['text_important_3'] = $this->language->get('text_important_3');
        $data['text_important_4'] = $this->language->get('text_important_4');
        $data['text_important_5'] = $this->language->get('text_important_5');

        $data['benefits'] = array(
            'ben1' => $this->language->get('ben1'),
            'ben2' => $this->language->get('ben2'),
            'ben3' => $this->language->get('ben3'),
            'ben4' => $this->language->get('ben4'),
            'ben5' => $this->language->get('ben5'),
            'ben6' => $this->language->get('ben6')
        );
        $data['text_hts'] = $this->language->get('htosell');
        $data['how_to_sell'] = array(
            'how_to_sell1' => $this->language->get('htosell1'),
            'how_to_sell2' => $this->language->get('htosell2'),
            'how_to_sell3' => $this->language->get('htosell3'),
            'how_to_sell4' => $this->language->get('htosell4'),
        );


        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }
        $data['img_seller'] = $server . 'image/seller_manu.jpeg';

       if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/seller_home.tpl')) {
            //  echo $this->config->get('config_template') . '/template/common/seller_header.tpl'; die;
            echo $this->load->view($this->config->get('config_template') . '/template/common/seller_home.tpl', $data);
        } else {
            echo $this->load->view('default/template/common/seller_home.tpl', $data);
        }
    }
}
