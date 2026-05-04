<?php

class ControllerProductFilterFacets extends Controller
{
    public function index($data)
    {

        if (isset($data['filter_facets']) && count($data['filter_facets']) > 0 ) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/filter_facets.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/product/filter_facets.tpl', $data);
            } else {
                return $this->load->view('default/template/common/filter_facets.tpl', $data);
            }
        }
    }
}