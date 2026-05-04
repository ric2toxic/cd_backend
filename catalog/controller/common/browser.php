<?php
class ControllerCommonBrowser extends Controller {
    public function index()
    {
        $data = array();
        $data['base'] = $this->config->get('config_url');


       if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/browser.tpl')) {
            //  echo $this->config->get('config_template') . '/template/common/seller_header.tpl'; die;
            echo $this->load->view($this->config->get('config_template') . '/template/common/browser.tpl', $data);
        } else {
            echo $this->load->view('default/template/common/browser.tpl', $data);
        }
    }
}
