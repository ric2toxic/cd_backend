<?php
class ModelTotalTax extends Model {
	
	/**
	 * Internal method to do all the calculations for tax
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total, $taxes) {
		$this->load->language('total/tax');
        $total_tax = 0;
		foreach ($taxes as $key => $value) {
            /* As per Accounts, all the tax values need to be added up and shown
			   as a single value 'Total Tax' */
            if ($value > 0) {
                $total_tax += $value;
            }
		}
        
        if ($total_tax > 0) {
            
            $total += $total_tax;
            
            $total_data[] = array(
				'code'       => 'tax',
				'title'      => $this->language->get('text_tax'),
				'value'      => round($total_tax, (int) $this->currency->getDecimalPlace()),
				'sort_order' => $this->config->get('tax_sort_order')
			);
        }
	}
	
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
		$this->_doCalculations($total_data, $total, $taxes);
	}
	
	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		$this->_doCalculations($total_data, $total, $taxes);
	}
}
