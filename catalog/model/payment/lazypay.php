<?php
class ModelPaymentLazypay extends Model {
    public function getMethod($address, $total) {

        $this->load->language('payment/lazypay');

        $method_data = array(
            'code'       => 'lazypay',
            'title'      => $this->language->get('text_title'),
            'terms'      => '',
            'sort_order' => $this->config->get('cod_sort_order')
        );

        return $method_data;
    }
}
