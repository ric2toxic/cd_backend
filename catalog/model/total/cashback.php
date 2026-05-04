<?php
class ModelTotalCashback extends Model {
	
	private $_cashback_available = 0.0;
	private $_sub_total = 0.0;
	private $_cart_products = array();
	
	/**
	 * Internal method to do all the calculations for cashback
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total, &$taxes, &$cst, $cst_class_id) {
		
		$this->load->language('total/cashback');

		// Cashback applies only when order subtotal is greater than the cart limit set
		if ( $this->_cashback_available 
			&& $this->_sub_total >= $this->config->get('config_cart_limit') ) {

			// Cashback usable cant be greater than order subtotal
			$cashback_usable = min($this->_cashback_available, $this->_sub_total);

			$i = 1;
            $total_cart_product = (int)(count($this->_cart_products)-1);
            $discount_total = 0;
            foreach ($this->_cart_products as $product) {
				
				// Proprotionate discount due to cashback on a single product row in order
				//$discount = $cashback_usable * ($product['total'] / $this->_sub_total);

                $discount = 0;
                $discount_per_piece = 0;

                if( $i <= $total_cart_product ){
                    $discount_per_piece = round( $cashback_usable * ($product['price_per_piece'] / $this->_sub_total),
                                             (int)$this->currency->getDecimalPlace()
                                           );
                    $discount = $discount_per_piece * $product['quantity'] * $product['piece_in_set'];
                    $discount_total += $discount;    
                } else {
                    $discount = $cashback_usable - $discount_total;
                    $discount_per_piece = round(($discount/($product['quantity'] * $product['piece_in_set'])),
                                                (int)$this->currency->getDecimalPlace()
                                               );
                    // $discount = $discount_per_piece * $product['quantity'] * $product['piece_in_set'];
                    $discount_total += $discount;    
                }

				if ($product['tax_class_id']) {
					$tax_rates = $this->tax->getRates($product['price_per_piece'], $product['tax_class_id']);

                    foreach ($tax_rates as $tax_rate) {
                        if ($tax_rate['type'] == 'P') {
                            // Adjusting tax value due to cashback discount
                            $taxes[$tax_rate['tax_rate_id']] -= ($product['quantity']*
                                $product['piece_in_set']*
                                $discount_per_piece*
                                $tax_rate['rate']/100);
                        }
                    }
						
					if ($cst_class_id && $cst) {
						// Adjusting CST value due to cashback discount
						$cst_to_deduct = $this->tax->getCST($discount, $product['tax_class_id'], $cst_class_id);
						$cst -= $cst_to_deduct;
					}
				}
                $i++;
			}

			// If cashback usable greater than total, it must be set to total value
			// Total value cannot go less than 0
			if ($cashback_usable > $total) {
				$cashback_usable = $total;
			}

			if ($cashback_usable > 0) {
				$total_data[] = array(
					'code'       => 'cashback',
					'title'      => $this->language->get('text_cashback'),
					'value'      => -round($discount_total ,(int)$this->currency->getDecimalPlace()),
					'sort_order' => $this->config->get('cashback_sort_order')
				);

				$total -= $cashback_usable; // Adjusting total value for cashback
			}
		}
	}
	
	
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
		if ($this->config->get('cashback_status')) {

			// Get available cashback with customer which is not expired yet
			$this->_cashback_available = $this->customer->getCashbackAvailable();
            $this->_sub_total = $this->cart->getSubTotal();
            $this->_cart_products = $this->cart->getProducts();

			$this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
		}
	}
	
	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		
		if ($this->config->get('cashback_status')) {
			
			$user_id = $extra['user_id'];
			
			$cst = NULL;
			$cst_class_id = 0;
			if (isset($extra['cst']) && isset($extra['cst_class_id'])) {
				$cst = $extra['cst'];
				$cst_class_id = $extra['cst_class_id'];
			}

			// Get available cashback with customer which is not expired yet
			$this->_cashback_available = $this->cart->getCashbackAvailable($user_id);
            $this->_sub_total = $this->cart->getSubTotal($user_id);
            $this->_cart_products = $this->cart->getProducts($user_id);

			$this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
			
			// Filling the calculated CST
			$extra['cst'] = $cst;
		}
	}


	public function confirm($order_info, $order_total) {
		$this->load->language('total/cashback');

		if ($order_info['customer_id']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_cashback 
                              SET customer_id = '" . (int)$order_info['customer_id'] . "', 
                                  order_id = '" . (int)$order_info['order_id'] . "', 
                                  description = '" . $this->db->escape(sprintf($this->language->get('text_order_no'), (float)$order_info['order_no'])) . "', 
                                  amount = '" . (float)$order_total['value'] . "', 
                                  date_added = NOW(), 
                                  validity = NULL, amount_utilized = NULL, expired = NULL");
                                  
            // Setting cashback utilization
            // Get all available cashbacks
            $tot_utilize = abs((float)$order_total['value']);
            
            $cashbacks = $this->db->query("SELECT customer_cashback_id, 
                                                  (amount - amount_utilized) AS cashback_available 
                                           FROM " . DB_PREFIX . "customer_cashback 
                                           WHERE customer_id = '" . (int)$order_info['customer_id'] . "' 
                                             AND expired = 0 
                                             AND amount > 0 
                                             AND (amount - amount_utilized) > 0 
                                           ORDER BY date_added ASC");
                                           
            foreach ($cashbacks->rows as $cashback) {
                
                if ($tot_utilize > 0) {
                    
                    $utilize = (($tot_utilize > (float)$cashback['cashback_available']) ? (float)$cashback['cashback_available'] : $tot_utilize);
                    
                    $this->db->query("UPDATE " . DB_PREFIX . "customer_cashback 
                                      SET amount_utilized = amount_utilized + " . (float)$utilize . " 
                                      WHERE customer_cashback_id = '" . (int)$cashback['customer_cashback_id'] . "'");
                    
                    $tot_utilize -= $utilize;
                } else break;
            }                               
		}
	}
	
	
}
