<?php
require_once('totalbase.php');
class SubTotal extends TotalBase {

	private $_sub_total = 0.0;
	private $_backend = array();

    public function __construct( $registry ){
        parent::__construct($registry);
	}

	/**
	 * Internal method to do all the calculations for subtotal
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total) {

		$this->_load->language('total/sub_total');

		$total_data[] = array(
			'code'       => 'sub_total',
			'title'      => $this->_language->get('text_sub_total'),
			'value'      => round($this->_sub_total,(int)$this->_currency->getDecimalPlace()),
			'sort_order' => $this->_config->get('sub_total_sort_order')
		);

		$total += round($this->_sub_total,(int)$this->_currency->getDecimalPlace());
	}

    private function _doSuborderCalculation(&$total_data, &$total){
        $language = $this->_registry->language->load('total/sub_total');
        if(!empty($this->_cart_data)){
            $subtotal = 0;
            foreach($this->_cart_data as $key => $product){
                $subtotal += (float)$product['quantity'] * (float)$product['piece_in_set'] * (float)$product['price_per_piece'];
            }
            $total_data[] = array(
    			'code'       => 'sub_total',
    			'title'      => $language['text_sub_total'],
    			'value'      => round($subtotal,(int)$this->_decimal_places),
    			'sort_order' => $this->_sort_order['sub_total_sort_order']
    		);
          $total += round($subtotal,(int)$this->_decimal_places);
        }

    }

	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0) {

		if ( $this->suborder ) {
            $this->_doSuborderCalculation($total_data, $total);
		}
        else{
            $this->_sub_total = $this->_cart->getSubTotal();
            $this->_doCalculations($total_data, $total);
        }

	}

	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {

		$this->_sub_total = $this->_cart->getSubTotal($extra['user_id']);
		$this->_doCalculations($total_data, $total);
	}
}
?>
