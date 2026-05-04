<?php

require_once('totalbase.php');

class MembershipDiscount extends TotalBase {

    private $_cart_products = array();

    public function __construct($registry) {
        parent::__construct($registry);
    }

    /**
     * Internal method to do all the calculations for membership discount
     * @Author: Devendra, September 2018
     */
    private function _doCalculations(&$total_data, &$total, &$taxes) {
        if ($this->membership_info == null) {
          $this->getMembershipInfo();
        }
        
        if (empty($this->membership_info)) {
          return;
        }
        
        $membership_name = $this->membership_info['membership_name'];
        if(strtotime($this->membership_info['membership_expiry_date']) >= strtotime(date('Y-m-d'))){
          $discount_percent = $this->membership_info[$this->_discount_type];
        }else{
          return;
        }

        $discount_total = 0;

        $i = 1;
        $total_cart_product = (int) (count($this->_cart_products) - 1);
        foreach ($this->_cart_products as $key => $product) {
          
          //If product found in exception rules, skip for discount process
          if( $this->isInExceptionRules( $product ) ) {
              continue;
          }

          $discount = 0;
          $discount_per_piece = 0;

          $discount_per_piece = round(
                  $product['price_per_piece'] / 100 * $discount_percent, (int) $this->_currency->getDecimalPlace()
          );

          $discount = $discount_per_piece * $product['quantity'] * $product['piece_in_set'];
          $discount_total += $discount;
      
          /* saving discount of each product in cart */
          $cart_product_discount = array();
          if (!empty($this->_cart_data[$key]['discount_breakup'])) {
              $cart_product_discount = unserialize($this->_cart_data[$key]['discount_breakup']);
          }
          $cart_product_discount['membership']['title'] = 'Membership Discount (-'. (int)$discount_percent .'%)';
          $cart_product_discount['membership']['valuep'] = $discount_percent;
          $cart_product_discount['membership']['value'] = "-$discount_per_piece";
          
          $this->_cart_data[$key]['discount_breakup'] = serialize($cart_product_discount);
      
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
          }

          /* save product complete discount */
          $cart_product_discount = 0;
          if (!empty($this->_cart_data[$key]['discount_per_piece'])) {
              $cart_product_discount = (float) $this->_cart_data[$key]['discount_per_piece'];
          }
          $cart_product_discount += -(float) $discount_per_piece;
          $this->_cart_data[$key]['discount_per_piece'] = $cart_product_discount;
          $i++;
        }
        
        if ($discount_total > $total) {
            $discount_total = $total;
        }


        $total_data[] = array(
            'code' => 'membership',
            'title' => 'Membership Discount (-'. (int)$discount_percent .'%)',
            'value' => -round($discount_total, (int) $this->_currency->getDecimalPlace()),
            'sort_order' => $this->_config->get('membership_sort_order')
        );

        $total -= round($discount_total, (int) $this->_currency->getDecimalPlace());
    }

    /**
     * [_doSuborderCalculation -- This method calculates the membership value for suborder]
     */
    private function _doSuborderCalculation(&$total_data, &$total, &$taxes) {
        $discount_total = 0;
        $title = '';
        
        foreach ($this->_cart_data as $product) {
            if (!empty($product['discount_breakup'])) {
                $discount_breakup = unserialize($product['discount_breakup']);
                if (!empty($discount_breakup['membership'])) {
                    $discount = (float) $discount_breakup['membership']['value'] *
                            (int) $product['piece_in_set'] *
                            (int) $product['quantity'];
                    $discount_total += (float) $discount;
                    $title = $discount_breakup['membership']['title'];
                }
            }
        }

        if (!empty($discount_total)) {
            $total_data[] = array(
                'code' => 'membership',
                'title' => $title,
                'value' => round($discount_total, (int) $this->_decimal_places),
                'sort_order' => $this->_sort_order['membership_sort_order'],
            );

            $total += round($discount_total, (int) $this->_decimal_places); // Adjusting total value for cashback
        }
    }

    public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0) {
        if ($this->suborder) {
            $this->_doSuborderCalculation($total_data, $total, $taxes);
            return;
        }
        
        // check for coupon, if percent discount applied, then skip the membership discount 
        $coupon_data = $this->_cart->getCoupon();
        if (isset($coupon_data['coupon_type']) && $coupon_data['coupon_type'] == 'P') return;
        if (isset($coupon_data['coupon_franchise'])) return;
        if (isset($coupon_data['coupon_az_discount_five'])) return;
        if (isset($coupon_data['coupon_az_discount_seven'])) return;
        if (isset($coupon_data['coupon_franchise_cash']) || isset($coupon_data['coupon_franchise_credit'])) return;
        
        $this->_payment_method_code = 'bank_transfer'; // default
        if (!empty($this->_session->data['payment_method']['code'])) {
          $this->_payment_method_code = $this->_session->data['payment_method']['code'];
        }

        $this->_discount_type = 'prepaid_discount';

        if($this->_payment_method_code == 'cod'){
          $this->_discount_type = 'cod_discount';
        }else if(in_array($this->_payment_method_code, CREDIT_PAYMENT_CODES) ){
          $this->_discount_type = 'credit_discount';
        }

        $this->_cart_products = $this->_cart->getProducts();

        $this->_cart_data = &$this->_cart->_in_stock_cart_data;
        $this->_doCalculations($total_data, $total, $taxes);  
    }

    /*
        Paycharge exception rules checking for Product, Seller & Category
        MSA April 2019  
    */
    protected function isInExceptionRules(array $product): bool
    {
        //get Paycharge exception rules
        $paycharge_exception_rules = $this->getPaychargeExceptionRules();
        $exception_status = false;

        if( !empty($paycharge_exception_rules['product'])
            && 
            !empty($product['product_id'])
            &&
            in_array($product['product_id'], $paycharge_exception_rules['product'])
        ) {
            $exception_status = true; 
        }else if( !empty($paycharge_exception_rules['seller'])
            && 
            !empty($product['seller_id'])
            &&
            in_array($product['seller_id'], $paycharge_exception_rules['seller'])
        ) {
           $exception_status = true; 
        } else if( !empty($paycharge_exception_rules['category'])
            &&
            !empty($product['category_id'])
        ){
            $product_categories = explode(',',$product['category_id']);
            if( !empty( array_intersect( $product_categories, $paycharge_exception_rules['category'] ) ) ){
                $exception_status = true;
            }
        }
        return $exception_status;
    }

    /*
        Protected method to get paycharge exception type(product/seller/category) wise rules
        MSA April 2019  
    */
    protected function getPaychargeExceptionRules()
    {
        $sql = "SELECT 
                  exception_type, 
                  type_id 
                FROM " . DB_PREFIX . "paycharge_exception 
                WHERE 
                  applicable_on_membership_discount = 1
                ";
        $result = $this->_db->query($sql);
        $data = array();
        if($result->num_rows) {
            foreach ($result->rows as $key => $value) {
                $data[$value['exception_type']][] = $value['type_id'];
            }
        }
        return $data;
    }


}

?>
