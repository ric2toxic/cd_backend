<?php
class ModelTotalTotal extends Model {
	
	/**
	 * Internal method to do all the calculations for total
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total) {
		$this->load->language('total/total');

		$total_data[] = array(
			'code'       => 'total',
			'title'      => $this->language->get('text_total'),
			'value'      => max(0, $total),
			'sort_order' => $this->config->get('total_sort_order')
		);
	}
	
	
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
		$this->_doCalculations($total_data, $total);
	}
	
	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		$this->_doCalculations($total_data, $total);
	}
}
