<?php
class ModelTotalShipping extends Model {
	
	private $_shipping_method = array();
	
	/**
	 * Internal method to do all the calculations for shipping
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total, &$taxes) {

        // checking for a customer having custom paycharge option
        // Look at TotalBase::customer_for_custom_paycharge
        $shipping_cost = 10*ceil($this->_shipping_method['cost']/10);

        // checking store voucher and store code
        // and setting shipping method to store pickup only if
        // store pick items are in cart
        // Done By Sudhanshu

        $coupon_data = $this->cart->getCoupon();
        if( !empty($coupon_data['wsb_store_voucher'] ) &&
            !empty($coupon_data['wsb_store_code']) ){
            $products = $this->cart->getProducts();
            $store_pickup = true;
            foreach ( $products as $product) {
                if(!$product['store_pickup']){
                    $store_pickup = $product['store_pickup'];
                    break;
                }
            }
            if($store_pickup){
                $this->_shipping_method['title'] = "Store Pickup";
                $shipping_cost = 0;
            }
        }
        // checking Topay coupon
        // and setting shipping method to 'To Pay Courier'
        // Done By Sudhanshu
        if( !empty($coupon_data['wsb_topay_coupon'] ) ){
            $this->_shipping_method['title'] = "To Pay Courier";
            $shipping_cost = 0;
        }

        //  As for Now we are not handle this case due to split order total calculation
        //
        // if ( !empty($this->_shipping_method['tax_class_id']) ) {
        // 	$tax_rates = $this->_tax->getRates($this->_shipping_method['cost'],
        // 	                                  $this->_shipping_method['tax_class_id']);
        //
        // 	foreach ($tax_rates as $tax_rate) {
        // 		if (!isset($taxes[$tax_rate['tax_rate_id']])) {
        // 			$taxes[$tax_rate['tax_rate_id']] = round($tax_rate['amount'],(int)$this->_currency->getDecimalPlace());
        // 		} else {
        // 			$taxes[$tax_rate['tax_rate_id']] += round($tax_rate['amount'],(int)$this->_currency->getDecimalPlace());
        // 		}
        // 	}
        // }


        $shipping_cost = round($shipping_cost,(int)$this->currency->getDecimalPlace());

        // Shipping is taxable under GST Now
        // Shipping cost is splitted pickup city wise
        $total_weight = 0;
        $weight_pickup_city_wise = array();
        $max_tax_rate_pickup_city_wise = array();

        foreach ($this->_cart_products as $product) {
            if ($product['tax_class_id']) {

                if (!isset($max_tax_rate_pickup_city_wise[$product['pickup_city']])) {
                    $max_tax_rate_pickup_city_wise[$product['pickup_city']] = 0;
                }

                if (!isset($weight_pickup_city_wise[$product['pickup_city']])) {
                    $weight_pickup_city_wise[$product['pickup_city']] = 0;
                }

                $weight_product = (int)$product['quantity']*(int)$product['piece_in_set']*(float)$product['weight_per_piece'];
                $weight_pickup_city_wise[$product['pickup_city']] += $weight_product;
                $total_weight += $weight_product;

                $tax_rates = $this->tax->getRates($product['price_per_piece'], $product['tax_class_id'], '', $product['mrp']);
                $current_tax_rate = 0;

                foreach ($tax_rates as $tax_rate) {
                    if ($tax_rate['type'] == 'P') {
                        $current_tax_rate += (float)$tax_rate['rate'];
                    }
                }

                $max_tax_rate_pickup_city_wise[$product['pickup_city']] = max($max_tax_rate_pickup_city_wise[$product['pickup_city']],
                    $current_tax_rate);
            }
        }

        // We dont care about storing tax rate in different tax rate id, especially for shipping
        // At the end, all will be combined in single tax row , in tax class file

        // To avoid division by zero error
        if ( $total_weight > 0 ) {
            reset($taxes);
            $trkey = key($taxes);

            foreach ($weight_pickup_city_wise as $pickup_city => $weight_suborder) {
                $taxes[$trkey] += ( ($weight_suborder/$total_weight) *
                    $shipping_cost *
                    $max_tax_rate_pickup_city_wise[$pickup_city] / 100 );
            }
        }


        $total_data[] = array(
            'code'       => 'shipping',
            'title'      => $this->_shipping_method['title'],
            'value'      => $shipping_cost,
            'sort_order' => $this->config->get('shipping_sort_order')
        );

        $total += $shipping_cost;
	}
	
	 
	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {
		
		if ( ($this->cart->hasShipping() && !empty($this->session->data['shipping_method'])) 
		     || !empty($this->session->data['shipping_method']) ) {
				 
			$this->_shipping_method = $this->session->data['shipping_method'];
			$this->_doCalculations($total_data, $total, $taxes);
		}
	}
	
	public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
		
		if ( !empty($extra['user_id']) && $this->cart->hasShipping($extra['user_id']) 
		     && !empty($extra['shipping_method']) ) {
			   
			$this->_shipping_method = $extra['shipping_method'];
            $this->_cart_products = $this->cart->getProducts($extra['user_id']);
			$this->_doCalculations($total_data, $total, $taxes);
		}
	}
}
