<?php

class ControllerProductSortsList extends Controller
{
    public function index($data)
    {

        if (isset($data['sorts']) && count($data['sorts']) > 0 ) {

            //Stock status filter
            $data['stock_filters'] = array(
                'In Stock' => 0, //its 0, because in query we have variable show_out_of_stock = 0
                'All Stock' => 1
            );

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/sorts_list.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/product/sorts_list.tpl', $data);
            } else {
                return $this->load->view('default/template/product/sorts_list.tpl', $data);
            }
        }else{
            return '';
        }
    }
}