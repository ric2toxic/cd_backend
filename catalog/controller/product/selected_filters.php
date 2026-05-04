<?php

class ControllerProductSelectedFilters extends Controller
{
    public function index($data)
    {
        $this->load->model('catalog/category');

        if (isset($data['arr_selected_filter']) && count($data['arr_selected_filter']) > 0 ) {
            if (isset($data['arr_selected_filter']['filter']) && count($data['arr_selected_filter']['filter']) > 0 ) {
                $filters = explode(",", $data['arr_selected_filter']['filter']);
                $data['arr_selected_filter']['filters'] = $this->model_catalog_category->getSelectFilters($filters);
            }

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/selected_filters.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/product/selected_filters.tpl', $data);
            } else {
                return $this->load->view('default/template/product/selected_filters.tpl', $data);
            }
        }
    }
}