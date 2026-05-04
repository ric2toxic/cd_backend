<?php
class ModelTotalCoupon extends Model {
	
	private $_sub_total = 0.0;
	private $_cart_products = array();
	private $_coupon = '';
	private $_shipping_method = array();
	private $_backend = array();
	private $_coupon_info = array();
	
	/**
	 * Internal method to do all the calculations for Coupon
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total, &$taxes, &$cst, $cst_class_id) {
		
		$this->load->language('total/coupon');
		$this->load->model('checkout/coupon');

		if(!empty($this->_coupon_info)){
		    // do nothing
        } else if ( !empty($this->_backend['coupon']) ) {
            $this->_coupon_info = array(
				'type'     => $this->_backend['discount_type'],
				'discount' => $this->_backend['discount_value'],
				'product'  => false,
				'shipping'  => false
			);
		} elseif ( $this->_coupon ) {
            $this->_coupon_info = $this->model_checkout_coupon->getCoupon($this->_coupon);
		} else {
			return false;
		}

		if ( ( $this->_coupon_info && !isset( $this->_coupon_info['coupon_message']['status'] )) 
			 || $this->_coupon_info['coupon_message']['status'] ) {
			
			$discount_total = 0;

			if ( !empty($this->_coupon_info['product']) ) {
				$this->_sub_total = 0;
				foreach ($this->_cart_products as $product) {
					if (in_array($product['product_id'], $this->_coupon_info['product'])) {
						$this->_sub_total += $product['total'];
					}
				}
			}

			if ($this->_coupon_info['type'] == 'F') {
                $this->_coupon_info['discount'] = min($this->_coupon_info['discount'], $this->_sub_total);
			}

			$i = 1;
			$total_cart_product = (int)(count($this->_cart_products)-1);
			foreach ($this->_cart_products as $product) {
				$discount = 0;
                $discount_per_piece = 0;

                // If product specific coupon and the current product in loop is not applicable for the coupon
                if ( !empty($this->_coupon_info['product']) && empty($this->_coupon_info['product'][$product['product_id']]) ){
                    continue;
                }
                // Determine discount amount corresponding to this product
                if ($this->_coupon_info['type'] == 'F') {
                    if( $i <= $total_cart_product ){
                        $discount_per_piece = round(
                            $this->_coupon_info['discount'] * ($product['price_per_piece'] / $this->_sub_total),
                                        (int)$this->currency->getDecimalPlace()
                                    );
                        $discount = $discount_per_piece * $product['quantity'] * $product['piece_in_set'];
                        $discount_total += $discount;
                    } else {
                        $discount = $this->_coupon_info['discount'] - $discount_total;
                        $discount_per_piece = round(($discount/($product['quantity'] * $product['piece_in_set'])),
                                                (int)$this->currency->getDecimalPlace()
                                               );
                        // $discount = $discount_per_piece * $product['quantity'] * $product['piece_in_set'];
                        $discount_total += $discount;
                    }
                } elseif ($this->_coupon_info['type'] == 'P') {
                    $discount_per_piece =  round(
                        $product['price_per_piece'] / 100 * $this->_coupon_info['discount'],
                        (int)$this->currency->getDecimalPlace()
                    );
                    $discount = $discount_per_piece * $product['quantity'] * $product['piece_in_set'];
                    $discount_total += $discount;
                }

				// If product has tax, adjust the tax rates
				if ($product['tax_class_id']) {
                    $tax_rates = $this->tax->getRates($product['price_per_piece'], $product['tax_class_id']);

                    foreach ($tax_rates as $tax_rate) {
                        if ($tax_rate['type'] == 'P') {
                            $taxes[$tax_rate['tax_rate_id']] -= ($product['quantity']*
                                $product['piece_in_set']*
                                $discount_per_piece*
                                $tax_rate['rate']/100);
                        }
                    }
					
					if ($cst_class_id and $cst) {
						$cst_to_deduct = $this->tax->getCST($discount, $product['tax_class_id'], $cst_class_id);
						$cst -= $cst_to_deduct;
					}
				}

				$i++;
			}

			// If coupon applies to shipping rates, and shipping has taxation as well
			if ($this->_coupon_info['shipping'] && !empty($this->_shipping_method['tax_class_id'])) {
				$tax_rates = $this->tax->getRates($this->_shipping_method['cost'], $this->_shipping_method['tax_class_id']);
			
				foreach ($tax_rates as $tax_rate) {
					if ($tax_rate['type'] == 'P') {
						$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
					}
				}
				$discount_total += $this->_shipping_method['cost'];
			}

			// If discount greater than total
			if ($discount_total > $total) {
				$discount_total = $total;
			}
			
			
			$total_data[] = array(
				'code'          => 'coupon',
				'title'         => ($this->_backend ? 'Discount' : sprintf($this->language->get('text_coupon'), $this->_coupon)),
				'value'         => -$discount_total,
				'sort_order'    => $this->config->get('coupon_sort_order'),
				'discount'      => $this->_coupon_info['discount'],
				'discount_type' => $this->_coupon_info['type'],
				
			);
			
			$total -= $discount_total;
		}
	}
	
	
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {

		if (!empty($this->session->data['coupon']) || $backend) {
			
			if ($backend) {
				$this->_sub_total = $backend['subtotal'];
				$this->_cart_products = array(); // From backend, product specific discounting is not done
				$this->_coupon = $backend['coupon'];
				$this->_backend = $backend;
			} else {
				$this->_sub_total = $this->cart->getSubTotal();
				$this->_cart_products = $this->cart->getProducts();
				$this->_coupon = $this->session->data['coupon'];
				$this->_shipping_method = $this->session->data['shipping_method'];
			}
			
			$this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
		}
	}

	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		
		$user_id = $extra['user_id'];

		$this->_sub_total = $this->cart->getSubTotal($user_id);
		$this->_cart_products = $this->cart->getProducts($user_id);
		$this->_coupon = $extra['coupon'];
		$this->_shipping_method = $extra['shipping_method'];
        if(empty($this->_coupon)){
            $coupon_data = $this->cart->getCoupon($user_id);
            if(!empty($coupon_data['coupon_franchise'])) {
                $this->_coupon = $coupon_data['coupon_franchise'];
                $this->_coupon_info = array(
                    'type'     => 'P',
                    'discount' => $coupon_data['franchise_discount'],
                    'product'  => false,
                    'shipping'  => false,
                    'code' => $this->_coupon
                );
            }
        }

		$cst = NULL;
		$cst_class_id = 0;
		
		if (isset($extra['cst']) && isset($extra['cst_class_id'])) {
			$cst_class_id = $extra['cst_class_id'];
			$cst = $extra['cst'];
		}
		
        $this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
        $extra['cst'] = $cst;
	}
	
	
	public function confirm($order_info, $order_total) {
		$code = '';

		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');

		if ($start && $end) {
			$code = substr($order_total['title'], $start, $end - $start);
		}

		$this->load->model('checkout/coupon');

		$coupon_info = $this->model_checkout_coupon->getCoupon($code);

		if ($coupon_info['coupon_message']['status']) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "coupon_history` 
			                  SET coupon_id = '" . (int)$coupon_info['coupon_id'] . "', 
			                    order_id = '" . (int)$order_info['order_id'] . "', 
			                    customer_id = '" . (int)$order_info['customer_id'] . "', 
			                    amount = '" . (float)$order_total['value'] . "', 
			                    date_added = NOW()");
		}
	}

	public function unconfirm($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "coupon_history` WHERE order_id = '" . (int)$order_id . "'");
	}
	
}
