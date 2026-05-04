<?php
require_once('totalbase.php');
class Shipping extends TotalBase {
    private $_shipping_method = array();
    private $_cart_products = array();

    public function __construct( $registry ){
        parent::__construct($registry);
    }

	/**
	 * Internal method to do all the calculations for shipping
	 * It requires private members of this class populated in advance.
	 * Method helps in avoiding code repetition in Cart and AppCart operations.
	 * @Author: Madhur, 2016
	 */
	private function _doCalculations(&$total_data, &$total, &$taxes, $free_shipping_enabled = false ) {

        $shipping_cost = 10*ceil($this->_shipping_method['cost']/10);

        // checking store voucher and store code
        // and setting shipping method to store pickup only if
        // store pick items are in cart
        // Done By Sudhanshu

        $coupon_data = $this->_cart->getCoupon();
        if( !empty($coupon_data['wsb_store_voucher'] ) &&
            !empty($coupon_data['wsb_store_code']) ){
            $products = $this->_cart->getProducts();
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

        if( !empty($coupon_data['wsb_topay_with_shipping'] ) ){
            $this->_shipping_method['title'] = "To Pay Courier";
            $shipping_cost = 200;
        }

        if( !empty($coupon_data['self_pickup_coupon'] ) ){
            $this->_shipping_method['title'] = "Self Pickup from Warehouse";
            $shipping_cost = 0;
        }

        // check if jp a to z inventory discount coupon code applied
        if( !empty($coupon_data['coupon_az_discount_five']) || !empty($coupon_data['coupon_az_discount_seven'])){
            $this->_shipping_method['title'] = "Store Pickup";
            $shipping_cost = 0;
        }

        // For Franchise shipping will be zero.
        if( !empty($coupon_data['coupon_franchise'] ) ){
            $shipping_cost = 0;
        }

        // For normal coupon, if shipping set to zero.
        if( !empty($coupon_data['coupon']) ){
            $this->_load->model('checkout/coupon', 'frontend');
            $coupon_info = $this->_registry->frontend_model_checkout_coupon->getCoupon($coupon_data['coupon']);
            if ($coupon_info['coupon_message']['status'] && ($coupon_info['shipping'] == 1) && !$free_shipping_enabled) {
                $shipping_cost = 0;
            }
        }


        // For customers of franchise
        // Cart should contain at least one franchise product, then only we will set shipping cost to zero.
        if( ( !empty($coupon_data['coupon_franchise_cash']) || !empty($coupon_data['coupon_franchise_credit']) )
            && $this->_cart->checkFranchiseProductsInCart()){
            $shipping_cost = 0;
        }

        //  As for Now we are not handle this case due to split order total calculation
        //
        // if ( !empty($this->_shipping_method['tax_class_id']) ) {
        //  $tax_rates = $this->_tax->getRates($this->_shipping_method['cost'],
        //                                    $this->_shipping_method['tax_class_id']);
        //
        //  foreach ($tax_rates as $tax_rate) {
        //      if (!isset($taxes[$tax_rate['tax_rate_id']])) {
        //          $taxes[$tax_rate['tax_rate_id']] = round($tax_rate['amount'],(int)$this->_currency->getDecimalPlace());
        //      } else {
        //          $taxes[$tax_rate['tax_rate_id']] += round($tax_rate['amount'],(int)$this->_currency->getDecimalPlace());
        //      }
        //  }
        // }
        
        
        $shipping_cost = round($shipping_cost,(int)$this->_currency->getDecimalPlace());
        
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
                
                $tax_rates = $this->_tax->getRates($product['price_per_piece'], $product['tax_class_id'], '', $product['mrp']);
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
        
        // if international order and apply international price factor is true, then convert the amount to dummy inr amount 
        if (Cart:: $apply_international_price_factor) {
					$shipping_cost = $this->_currency->convert($shipping_cost, 'INR', DUMMY_INR_CURRENCY);
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
        
        // if ( $free_shipping_enabled && !empty( $this->_shipping_method['free_shipping'] )) {
        //     $shipping_cost = 0;
        // }
        
        $total_data[] = array(
            'code'       => 'shipping',
            'title'      => isset($this->_shipping_method['title']) ? $this->_shipping_method['title'] : '',
            'value'      => $shipping_cost,
            'sort_order' => $this->_config->get('shipping_sort_order')
        );

        $total += $shipping_cost;
    }


    private function _doSuborderCalculation(&$total_data, &$total, &$taxes){
        if(!empty($this->suborder_id)){

            $sql  = "SELECT shipping_charge, ";
            $sql .=        "shipping_method ";
            $sql .=      "FROM ".DB_PREFIX."suborder ";
            $sql .=      "WHERE suborder_id = '".$this->_db->escape($this->suborder_id)."'";

            $result = $this->_db->query($sql);
            $shipping = (float)$result->row['shipping_charge'];
            $total_data[] = array(
                'code'       => 'shipping',
                'title'      => $result->row['shipping_method'],
                'value'      => round( $shipping ,(int)$this->_decimal_places ),
                'sort_order' => $this->_sort_order['shipping_sort_order']
            );
            $total += round($shipping,(int)$this->_decimal_places);
        }
    }


	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array(), $free_shipping_enabled = false ) {
        if($this->suborder){
            $this->_doSuborderCalculation($total_data, $total, $taxes);
            return;
        }
        if ( ($this->_cart->hasShipping() && !empty($this->_session->data['shipping_method']))
             || !empty($this->_session->data['shipping_method']) ) {

            $this->_shipping_method = $this->_session->data['shipping_method'];
            $this->_cart_products = $this->_cart->getProducts();

            $this->_doCalculations($total_data, $total, $taxes, $free_shipping_enabled);
		}
	}

    public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {

        if ( !empty($extra['user_id']) && $this->_cart->hasShipping($extra['user_id'])
             && !empty($extra['shipping_method']) ) {

            $this->_shipping_method = $extra['shipping_method'];
            $this->_cart_products = $this->_cart->getProducts($extra['user_id']);
            $this->_doCalculations($total_data, $total, $taxes);
        }
    }
}
?>
