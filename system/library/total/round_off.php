<?php
require_once('totalbase.php');
class RoundOff extends TotalBase {

    public function __construct( $registry ){
        parent::__construct($registry);
	}
	/**
	 * Internal method to do all the calculations for roundoff
	 * It employs normal rounding (less than 0.5, rounded down else rounded up)
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total) {

		// As of now, we apply round-off only for orders in INR
		//@todo: Give flexibility of adding/removing currencies from backend
		if ( $this->_config->get('round_off_status')
		     && strtolower(trim($this->_currency->getCode())) == 'inr' ) {

			$round_off = round($total) - round($total, (int)$this->_currency->getDecimalPlace());
			// Round off should not be zero
			if ( !(abs($round_off) < pow(10.0, -((int)$this->_currency->getDecimalPlace()))) ) {

				$this->_load->language('total/round_off');

				$total_data[] = array(
					'code'       => 'round_off',
					'title'      => $this->_language->get('text_round_off'),
					'value'      => $round_off,
					'sort_order' => $this->_config->get('round_off_sort_order')
				);

				$total = round($total);
			}
		}
	}

    private function _doSuborderCalculation(&$total_data, &$total) {

			$round_off = round($total) - round($total, (int)$this->_decimal_places);
			// Round off should not be zero
			if ( !(abs($round_off) < pow(10.0, -((int)$this->_decimal_places))) ) {

				$language = $this->_registry->language->load('total/round_off');

				$total_data[] = array(
					'code'       => 'round_off',
					'title'      => $language['text_round_off'],
					'value'      => $round_off,
					'sort_order' => $this->_sort_order['round_off_sort_order']
				);

				$total = round($total);
			}
	}

	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
        if($this->suborder){
            // For now, we are not doing round-off at suborder level.
            // as round off numbers technically cant distribute as per the original round off value
            //$this->_doSuborderCalculation($total_data, $total);
            return;
        }
		$this->_doCalculations($total_data, $total);
	}

	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		$this->_doCalculations($total_data, $total);
	}
}
