<?php
class ControllerSellerPanelSellerFooter extends Controller {
    public function index() {

        $data['footer'] = 'Footer';
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/seller_panel/seller_footer.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/seller_panel/seller_footer.tpl', $data);
        } else {
            return $this->load->view('default/template/seller_panel/seller_footer.tpl', $data);
        }
    }

}
