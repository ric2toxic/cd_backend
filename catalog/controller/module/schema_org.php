<?php
class ControllerModuleSchemaOrg extends Controller {
    public function index($data) {
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/schema_org.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/module/schema_org.tpl', $data);
        } else {
            return $this->load->view('default/template/module/schema_org.tpl', $data);
        }
    }
}