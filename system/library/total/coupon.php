<?php

require_once('totalbase.php');

class Coupon extends TotalBase {

    private $_sub_total = 0.0;
    private $_cart_products = array();
    private $_coupon = '';
    private $_shipping_method = array();
    private $_backend = array();
    private $_coupon_info = array();

    public function __construct($registry) {
        parent::__construct($registry);
    }

    /**
     * Internal method to do all the calculations for Coupon
     * It requires private members of this class populated in advance.
     * Method helps in avoiding code repetition in Cart and AppCart operations.
     * @Author: Madhur, 2016
     */
    private function _doCalculations(&$total_data, &$total, &$taxes, &$cst, $cst_class_id, $free_shipping_enabled = false ) {

        $this->_load->language('total/coupon');
        $this->_load->model('checkout/coupon', 'frontend');

        if (!empty($this->_coupon_info)) {
            // do nothing
        } else if ($this->_coupon && $this->_coupon != 'unit_testing_coupon') {
            
            $coupon_info = $this->_registry->frontend_model_checkout_coupon->getCoupon($this->_coupon);

            if ( $coupon_info['coupon_message']['status'] ) {
                $this->_coupon_info = $coupon_info;
            }
            
        } else if (!empty($this->_config->get('unit_testing_coupon'))) {
            $this->_coupon_info = $this->_config->get('unit_testing_coupon');
        } else if (!$this->suborder) {
            return false;
        }
        if (isset($this->_coupon_info['type']) && $this->_coupon_info['type'] == 'F') {
            return false;
        }

        if (!empty($this->_coupon_info)) {
            $discount_total = 0;
            if (!empty($this->_coupon_info['product'])) {
                $this->_sub_total = 0;
                foreach ($this->_cart_products as $product) {
                    if (!empty($this->_coupon_info['product'][$product['product_id']])) {
                        $this->_sub_total += $product['total'];
                    }
                }
            }

            $i = 1;
            $total_cart_product = (int) (count($this->_cart_products) - 1);
            foreach ($this->_cart_products as $key => $product) {
                $discount = 0;
                $discount_per_piece = 0;

                // If product specific coupon and the current product in loop is not applicable for the coupon
                if (!empty($this->_coupon_info['product']) 
                    && empty($this->_coupon_info['product'][$product['product_id']])) {
                    continue;
                }

                if ($this->_coupon_info['type'] == 'P') {
                    $discount_per_piece = round(
                            $product['price_per_piece'] / 100 * $this->_coupon_info['discount'], (int) $this->_currency->getDecimalPlace()
                    );

                    $discount = $discount_per_piece * $product['quantity'] * $product['piece_in_set'];
                    $discount_total += $discount;
                }

                
                /* saving discount of each product in cart */
                $cart_product_discount = array();
                if (!empty($this->_cart_data[$key]['discount_breakup'])) {
                    $cart_product_discount = unserialize($this->_cart_data[$key]['discount_breakup']);
                }
                $product_coupon_info = !empty($this->_coupon_info['product'][$product['product_id']]) ? $this->_coupon_info['product'][$product['product_id']] : array();
                $cart_product_discount['coupon']['code'] = $this->_coupon_info['code'];
                $cart_product_discount['coupon']['type'] = $this->_coupon_info['type'];
                $cart_product_discount['coupon']['discount'] = $this->_coupon_info['discount'];
                if (!empty($product_coupon_info)) {
                    $cart_product_discount['coupon'][$product_coupon_info['filter_name']] = $product_coupon_info['filter_value'];
                }
                $cart_product_discount['coupon']['value'] = "-$discount_per_piece";
                $this->_cart_data[$key]['discount_breakup'] = serialize($cart_product_discount);

                // End
                // If product has tax, adjust the tax rates
                if ($product['tax_class_id']) {
                    $tax_rates = $this->_tax->getRates($product['price_per_piece'], $product['tax_class_id'], '', $product['mrp']);

                    foreach ($tax_rates as $tax_rate) {
                        if ($tax_rate['type'] == 'P') {
                            $taxes[$tax_rate['tax_rate_id']] -= ($product['quantity'] *
                                    $product['piece_in_set'] *
                                    $discount_per_piece *
                                    $tax_rate['rate'] / 100);
                        }
                    }

                    if ($cst_class_id and $cst) {
                        $cst_to_deduct = $this->_tax->getCST($discount, $product['tax_class_id'], $cst_class_id, $product['mrp']);
                        $cst -= $cst_to_deduct;
                    }
                }

                /* save product complete discount */
                $cart_product_discount = 0;
                if (!empty($this->_cart_data[$key]['discount_per_piece'])) {
                    $cart_product_discount = (float) $this->_cart_data[$key]['discount_per_piece'];
                }
                $cart_product_discount += -(float) $discount_per_piece;
                $this->_cart_data[$key]['discount_per_piece'] = $cart_product_discount;
                $i++;
                /* End */
            }

            //Commented for Now
            // If coupon applies to shipping rates, and shipping has taxation as well
            // if ($coupon_info['shipping'] && !empty($this->_shipping_method['tax_class_id'])) {
            //
			// 	$tax_rates = $this->_tax->getRates(
            // 									$this->_shipping_method['cost'],
            // 									$this->_shipping_method['tax_class_id'], '', $product['mrp']
            // 								);
            //
			// 	foreach ($tax_rates as $tax_rate) {
            // 		if ($tax_rate['type'] == 'P') {
            // 			$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
            // 		}
            // 	}
            // 	$discount_total += $this->_shipping_method['cost'];
            // }
            // If discount greater than total
            if ($discount_total > $total) {
                $discount_total = $total;
            }

            if ( empty( $free_shipping_enabled )) {

                $total_data[] = array(
                    'code' => 'coupon',
                    'title' => sprintf($this->_language->get('text_coupon'), $this->_coupon),
                    'value' => -round($discount_total, (int) $this->_currency->getDecimalPlace()),
                    'sort_order' => ($this->_coupon_info['type'] == 'F') ? 9 : $this->_config->get('coupon_sort_order'),
                    'discount' => $this->_coupon_info['discount'],
                    'discount_type' => $this->_coupon_info['type'],
                );

                $total -= round($discount_total, (int) $this->_currency->getDecimalPlace());
            }
        }
    }

    public function getNetPayable(&$total_data, $total, &$net_payable_amount) {

        if ($this->suborder) {
            return false;
        }
        // get coupon
        $coupon_data = $this->_cart->getCoupon();
        if (!empty($coupon_data['coupon'])) {
            $this->_coupon = $coupon_data['coupon'];
        } else if (!empty($coupon_data['coupon_franchise'])) {
            $this->_coupon = $coupon_data['coupon_franchise'];

            $this->_coupon_info = array('discount' => $coupon_data['franchise_discount'],
										'type' => 'P',
										'code' => $coupon_data['coupon_franchise']
									);
        } else if (!empty($coupon_data['coupon_az_discount_five'])) {
            $this->_coupon = $coupon_data['coupon_az_discount_five'];

            $this->_coupon_info = array('discount' => 5,
                'type' => 'P',
                'code' => $coupon_data['coupon_az_discount_five']
            );
        } else if (!empty($coupon_data['coupon_az_discount_seven'])) {
            $this->_coupon = $coupon_data['coupon_az_discount_seven'];

            $this->_coupon_info = array('discount' => 7,
                'type' => 'P',
                'code' => $coupon_data['coupon_az_discount_seven']
            );
        }


        if (!empty($this->_coupon)) {
            $this->_sub_total = $this->_cart->getSubTotal();
            $this->_cart_products = $this->_cart->getProducts();

            $this->_shipping_method = !empty($this->_session->data['shipping_method']) ? $this->_session->data['shipping_method'] : '';
            $this->_cart_data = &$this->_cart->_in_stock_cart_data;
        }

        $this->_load->language('total/coupon');
        $this->_load->model('checkout/coupon', 'frontend');

        if (!empty($this->_coupon_info)) {
            // do nothing
        } else if ($this->_coupon && $this->_coupon != 'unit_testing_coupon') {

            $coupon_info = $this->_registry->frontend_model_checkout_coupon->getCoupon($this->_coupon);

            if ( $coupon_info['coupon_message']['status'] ) {
                $this->_coupon_info = $coupon_info;
            }
        } else if (!empty($this->_config->get('unit_testing_coupon'))) {
            $this->_coupon_info = $this->_config->get('unit_testing_coupon');
        } else if (!$this->suborder) {
            return false;
        }
        if (isset($this->_coupon_info['type']) && $this->_coupon_info['type'] == 'P') {
            return false;
        }
        if (!empty($this->_coupon_info)) {
            if (!empty($this->_coupon_info['product'])) {
                $this->_sub_total = 0;
                foreach ($this->_cart_products as $product) {
                    if (!empty($this->_coupon_info['product'][$product['product_id']])) {
                        $this->_sub_total += $product['total'];
                    }
                }
            }

            if ($this->_coupon_info['type'] == 'F') {
                $this->_coupon_info['discount'] = min($this->_coupon_info['discount'], $this->_sub_total);
            }

            // If discount greater than total
            if ($this->_coupon_info['discount'] > $total) {
                $this->_coupon_info['discount'] = $total;
            }


            $total_data[] = array(
                'code' => 'coupon',
                'title' => sprintf($this->_language->get('text_coupon'), $this->_coupon),
                'value' => -round($this->_coupon_info['discount'], (int) $this->_currency->getDecimalPlace()),
                'sort_order' => ($this->_coupon_info['type'] == 'F') ? 9 : $this->_config->get('coupon_sort_order'),
                'discount' => $this->_coupon_info['discount'],
                'discount_type' => $this->_coupon_info['type'],
            );



            $net_payable_amount -= round($this->_coupon_info['discount'], (int) $this->_currency->getDecimalPlace());
        }
    }

    /**
     * [_doSuborderCalculation -- This method calculates the coupon value for suborder]
     */
    private function _doSuborderCalculation(&$total_data, &$total, &$taxes, &$cst, $cst_class_id) {
        $language = $this->_registry->language->load('total/coupon');
        $coupon = array();
        $discount_total = 0;
        
        foreach ($this->_cart_data as $product) {
            if (!empty($product['discount_breakup'])) {
                $discount_breakup = unserialize($product['discount_breakup']);
                if (!empty($discount_breakup['coupon'])) {
                    $discount = (float) $discount_breakup['coupon']['value'] *
                            (int) $product['piece_in_set'] *
                            (int) $product['quantity'];
                    $discount_total += (float) $discount;
                    $coupon = $discount_breakup['coupon'];
                }
            }
        }

        if (!empty($discount_total)) {
            $total_data[] = array(
                'code' => 'coupon',
                'title' => sprintf($language['text_coupon'], $coupon['code']),
                'value' => round($discount_total, (int) $this->_decimal_places),
                'sort_order' => $this->_sort_order['coupon_sort_order'],
                'discount' => $coupon['discount'],
                'discount_type' => $coupon['type'],
            );

            $total += round($discount_total, (int) $this->_decimal_places); // Adjusting total value for cashback
        }
    }

    public function getTotal(&$total_data, &$total, &$taxes, &$cst = NULL, $cst_class_id = 0, $free_shipping_enabled = false ) {
        if ($this->suborder) {
            $this->_doSuborderCalculation($total_data, $total, $taxes, $cst, $cst_class_id);
            return;
        }

        // get coupon
        $coupon_data = $this->_cart->getCoupon();
        if (!empty($coupon_data['coupon'])) {
            $this->_coupon = $coupon_data['coupon'];
        } else if (!empty($coupon_data['coupon_franchise'])) {
            $this->_coupon = $coupon_data['coupon_franchise'];
            $this->_load->model('checkout/coupon', 'frontend');
            $coupon_discount = $this->_registry->frontend_model_checkout_coupon->getFranchiseCoupon($coupon_data['coupon_franchise'],$this->_customer->getId());
            if(!empty($coupon_discount)){
                $this->_coupon_info = array('discount' => $coupon_discount,
                    'type' => 'P',
                    'code' => $coupon_data['coupon_franchise']
                );
            }
        } else if (!empty($coupon_data['coupon_az_discount_five'])) {
            $this->_coupon = $coupon_data['coupon_az_discount_five'];

            $this->_coupon_info = array('discount' => 5,
                'type' => 'P',
                'code' => $coupon_data['coupon_az_discount_five']
            );
        } else if (!empty($coupon_data['coupon_az_discount_seven'])) {
            $this->_coupon = $coupon_data['coupon_az_discount_seven'];

            $this->_coupon_info = array('discount' => 7,
                'type' => 'P',
                'code' => $coupon_data['coupon_az_discount_seven']
            );
        }

        if (!empty($this->_coupon)) {
            $this->_sub_total = $this->_cart->getSubTotal();
            $this->_cart_products = $this->_cart->getProducts();

            $this->_shipping_method = !empty($this->_session->data['shipping_method']) ? $this->_session->data['shipping_method'] : '';
            $this->_cart_data = &$this->_cart->_in_stock_cart_data;
            $this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id, $free_shipping_enabled);
        }
    }

    public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {

        $user_id = $extra['user_id'];

        $this->_sub_total = $this->_cart->getSubTotal($user_id);
        $this->_cart_products = $this->_cart->getProducts($user_id);
        $this->_coupon = $extra['coupon'];
        $this->_shipping_method = $extra['shipping_method'];

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

        $this->_load->model('checkout/coupon', 'frontend');
        $coupon_info = $this->_registry->frontend_model_checkout_coupon->getCoupon($code);
        if ($coupon_info['coupon_message']['status']) {

            $this->_db->query("INSERT INTO `" . DB_PREFIX . "coupon_history`
			                  SET coupon_id = '" . (int) $coupon_info['coupon_id'] . "',
                                coupon_code = '". $code ."',
                                coupon_name = '". $coupon_info['name'] ."',
			                    order_id = '" . (int) $order_info['order_id'] . "',
			                    customer_id = '" . (int) $order_info['customer_id'] . "',
			                    amount = '" . (float) $order_total['value'] . "',
			                    date_added = NOW()");
            $payment_array = array(
                'order_id'           => $order_info['order_id'],
                'merchant_txn_id'    => $coupon_info['coupon_id'],
                'order_no'           => $order_info['order_no'],
                'txn_status'         => 'SUCCESS',
                'payment_mode'       => 'Coupon - ' . $coupon_info['code'],
                'amount'             => ((float) $coupon_info['discount']),
                'payment_gateway'    => 'coupon',
                'bank_transfer_mode' => 'not_applicable',
                'successfull'        => '1',
                'reference'          => $coupon_info['name'],
                'payment_link'       => $coupon_info['name'],
                'json_format'        => json_encode($coupon_info),
                'user_id'            => 0,
                'sales_staff_id'     => 0
            );

            if($coupon_info['type'] == 'F') {
                OrderPayment::insertOrderPayment($this->_db, $payment_array);
            }
           
        }
    }

    public function unconfirm($order_id) {
        $this->_db->query("DELETE FROM `" . DB_PREFIX . "coupon_history` WHERE order_id = '" . (int) $order_id . "'");
    }

}

?>
