<?php
class ControllerAffiliateBanner extends Controller {
    public function index()
    {
        if (!$this->affiliate->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('affiliate/banner', '', 'SSL');

            $this->response->redirect($this->url->link('affiliate/login', '', 'SSL'));
        }

        $this->load->language('affiliate/account');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('affiliate/account', '', 'SSL')
        );

        $this->load->model('affiliate/affiliate');
        $affiliate_id =  $this->affiliate->getId();
        $code_fetch = $this->model_affiliate_affiliate->getAffiliate($affiliate_id);
        $data['affiliate_code'] = $code_fetch['code'];
        $this->load->language('affiliate/banner');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['text_size'] = $this->language->get('text_size');

     // echo "<pre>";  print_r($data['size']); die;

        $dir = DIR_IMAGE."/affiliate_banner/";
        if (is_dir($dir)){
            if ($dh = opendir($dir)){
                while (($file = readdir($dh)) !== false){
                    if(($file != '..') && ($file != '.')) {
                        $data['images'][] = $file;
                        $text = explode("_", $file);
                        $c = count($text);
                        $size_1 = explode(".", $text[$c-1]);
                        //echo "<pre>"; print_r($size);
                        $s = count($size_1);
                        $data['size'][] = $size_1[$s-2];
                    }
                }
                closedir($dh);
            }
        }

        $data['text_banner'] = $this->language->get('text_banner') ;
//echo "<pre>"; print_r($data['size']); die;

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/affiliate/banner.tpl')) {
        $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/affiliate/banner.tpl', $data));
    } else {
        $this->response->setOutput($this->load->view('default/template/affiliate/banner.tpl', $data));
    }

    }
}