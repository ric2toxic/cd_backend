<?php 
class ModelTotalPaycharge extends Model {
	
	private $_sub_total = 0.0;
	private $_payment_method_code = '';
	private $_cart_products = array();
	
	/**
	 * Internal method to do all the calculations for paycharge
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total, &$taxes, &$cst, $cst_class_id) {
		
		$discount_percent = 0;
		$discount_total = 0;
		
		// Determine if the highest possible discount available for the 
		// order subtotal range and the chosen payment method
		foreach ($this->config->get('paycharge') as $paycharge) {
			if ($paycharge['payment_method'] == $this->_payment_method_code) {
				if ( isset($paycharge['amount']) && isset($paycharge['valuep'])  
				     && $this->_sub_total > $paycharge['amount'] 
				     && abs($paycharge['valuep']) > abs($discount_percent) ) {
					
					$discount_percent = abs($paycharge['valuep']);
					$discount_description = $paycharge['description'][$this->config->get('config_language_id')]['name'] . 
					                        ' (' . -$discount_percent . '%)';
				}
			}
		}
					                        
		if ($discount_percent > 0.0) {
			
			foreach ($this->_cart_products as $product) {
				$discount = $product['total'] / 100 * $discount_percent;
				$discount_total += $discount;

				if ($product['hsn_code']) {
					// will be negative since $discount is negative
					$tax_rates = $this->tax->getRates($discount, $product['hsn_code']);

					foreach ($tax_rates as $tax_rate) {
						if ($tax_rate['type'] == 'P') {
							$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
						}
					}
				
					if ($cst_class_id && $cst) {
						// calculated cst deduction will be negative since discount is negative
						$cst_adjust_val = $this->tax->getCST($discount, $product['tax_class_id'], $cst_class_id);
						$cst -= $cst_adjust_val;
					}
				}
			}
			
			// If discount greater than total
			if ($discount_total > $total) {
				$discount_total = $total;
			}

			$total_data[] = array(
				'code' => 'paycharge',
				'title' => $discount_description,
				'value' => -$discount_total,
				'sort_order' => $this->config->get('paycharge_sort_order')
			);
			
			$total -= $discount_total;
		}
	}
	
	
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
        
        if ( !isset($this->request->get['route']) || $this->request->get['route'] != 'checkout/cart' ) {
            
            if( (isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1) 
                || $this->config->get('config_store_id') == INTERNATIONAL_STORE_ID){
                // ignore this if dropshipper account or International Store
                
            } else {
                if ($this->config->get('paycharge_status') 
                    && !empty($this->session->data['payment_method']['code']) 
                    && $this->cart->getSubTotal()) {
                    
                    $this->_sub_total = (float)$this->cart->getSubTotal();
                    $this->_cart_products = $this->cart->getProducts();
                    $this->_payment_method_code = $this->session->data['payment_method']['code'];
                    
                    $this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
                }
            }
        }
	}
	
	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		$user_id = $extra['user_id'];
		$payment_method = $extra['payment_method'];
		if ( isset($extra['cst']) && isset($extra['cst_class_id']) ) {
			$cst_class_id = $extra['cst_class_id'];
			$cst = $extra['cst'];
		} else {
			$cst_class_id = 0;
			$cst = NULL;
		}
		
        if ( !isset($this->request->get['route']) || $this->request->get['route'] != 'checkout/cart' ) {
			
            if( $this->cart->is_dropshipper($user_id) == 1 || $this->config->get('config_store_id') == INTERNATIONAL_STORE_ID ) {
				// Ignore if dropshipper or International Store
				
			} else {
				if ($this->config->get('paycharge_status') 
                    && !empty($payment_method['code']) 
                    && $this->cart->getSubTotal($user_id)) {
				
					$this->_sub_total = (float)$this->cart->getSubTotal($user_id);
					$this->_cart_products = $this->cart->getProducts($user_id);
					$this->_payment_method_code = $payment_method['code'];
						
					$this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
				}
			}
		}
	}
	
}
