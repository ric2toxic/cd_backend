<?php
class ModelPaymentRbl extends Model {
    public function getMethod($address, $total) {

        $this->load->language('payment/rbl');

        $method_data = array();

        if($this->customer->isCustomerCreditStatus('RBL'))
        {
            $method_data = array(
                'code'       => 'rbl_credit',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('rbl_sort_order')
            );
        }

        return $method_data;
    }




}
