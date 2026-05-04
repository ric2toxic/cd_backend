<?php
class ModelTotalSubTotal extends Model {
	
	private $_sub_total = 0.0;
	private $_backend = array();
	
	/**
	 * Internal method to do all the calculations for subtotal
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total) {
		
		$this->load->language('total/sub_total');

		$total_data[] = array(
			'code'       => 'sub_total',
			'title'      => $this->language->get('text_sub_total'),
			'value'      => $this->_sub_total,
			'sort_order' => $this->config->get('sub_total_sort_order')
		);

		$total += $this->_sub_total;
	}
	
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
		
		if (empty($backend)) {
			$this->_sub_total = $this->cart->getSubTotal();
		} else {
		    if (isset($backend['subtotal']))
				$this->_sub_total = $backend['subtotal'];
		}
		$this->_doCalculations($total_data, $total);
	}
	
	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {

		$this->_sub_total = $this->cart->getSubTotal($extra['user_id']);
		$this->_doCalculations($total_data, $total);
	}
}
