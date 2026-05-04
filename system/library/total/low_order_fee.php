<?php
require_once('totalbase.php');
class LowOrderFee extends TotalBase {
    public function __construct( $registry ){
        parent::__construct($registry);
	}
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0) {
		if ($this->_cart->getSubTotal() && ($this->_cart->getSubTotal() < $this->_config->get('low_order_fee_total'))) {
			$this->_load->language('total/low_order_fee');

			$total_data[] = array(
				'code'       => 'low_order_fee',
				'title'      => $this->_language->get('text_low_order_fee'),
				'value'      => $this->_config->get('low_order_fee_fee'),
				'sort_order' => $this->_config->get('low_order_fee_sort_order')
			);

			if ($this->_config->get('low_order_fee_hsn_code')) {
				$tax_rates = $this->_tax->getRates($this->_config->get('low_order_fee_fee'), $this->_config->get('low_order_fee_hsn_code'));

				foreach ($tax_rates as $tax_rate) {
					if (!isset($taxes[$tax_rate['tax_rate_id']])) {
						$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			$total += $this->_config->get('low_order_fee_fee');
		}
	}
}
?>
