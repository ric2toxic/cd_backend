<?php
require_once('totalbase.php');
class Handling extends TotalBase{

    public function __construct( $registry ){
        parent::__construct($registry);
	}
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
		if (($this->_cart->getSubTotal() > $this->_config->get('handling_total')) && ($this->_cart->getSubTotal() > 0)) {
			$this->_load->language('total/handling');

			$total_data[] = array(
				'code'       => 'handling',
				'title'      => $this->_language->get('text_handling'),
				'value'      => $this->_config->get('handling_fee'),
				'sort_order' => $this->_config->get('handling_sort_order')
			);

			if ($this->_config->get('handling_hsn_code')) {
				$tax_rates = $this->_tax->getRates($this->_config->get('handling_fee'), $this->_config->get('handling_hsn_code'));

				foreach ($tax_rates as $tax_rate) {
					if (!isset($taxes[$tax_rate['tax_rate_id']])) {
						$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			$total += $this->_config->get('handling_fee');
		}
	}
}
?>
