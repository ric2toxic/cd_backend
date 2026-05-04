<?php
require_once('totalbase.php');
class Total extends TotalBase {

    public function __construct( $registry ){
        parent::__construct($registry);
	}

	/**
	 * Internal method to do all the calculations for total
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total) {
        $this->_load->language('total/total');

		$total_data[] = array(
			'code'       => 'total',
			'title'      => $this->_language->get('text_total'),
			'value'      => max(0, ROUND($total, 2)),
			'sort_order' => $this->_config->get('total_sort_order')
		);
	}

    private function _doSuborderCalculation(&$total_data, &$total){
        $language = $this->_registry->language->load('total/total');

		$total_data[] = array(
			'code'       => 'total',
			'title'      => $language['text_total'],
			'value'      => max(0, $total),
			'sort_order' => $this->_sort_order['total_sort_order']
		);
    }

	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
        if($this->suborder){
            $this->_doSuborderCalculation($total_data, $total);
            return;
        }
		$this->_doCalculations($total_data, $total);
	}

	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		$this->_doCalculations($total_data, $total);
	}
}
?>
