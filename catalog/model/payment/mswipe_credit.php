<?php
class ModelPaymentMswipeCredit extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/mswipe_credit');

        // Oct 2019: Customer level mswipe status is removed, so disbaling 
        // mswipe credit payment permanently for now.
		return array();

		$method_data = array(
			'code'       => 'mswipe_credit',
			'title'      => $this->language->get('text_title'),
			'terms'      => '',
			'sort_order' => $this->config->get('mswipe_credit_sort_order')
		);

		return $method_data;
	}
}
