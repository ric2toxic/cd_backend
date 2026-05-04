<?php
require_once('totalbase.php');
class Paycharge extends TotalBase {

	protected $_sub_total = 0.0;
	protected $_payment_method_code = '';
	protected $_cart_products = array();
    protected $_customer_id;

    public function __construct( $registry ){
        parent::__construct($registry);
	}

    public function getPaychargeDiscountPercent(){
        $discount_percent = 0;
		$discount_total = 0;
		$paychange_data = array();
        // Determine if the highest possible discount available for the
		// order subtotal range and the chosen payment method
        $paycharges = $this->getPaychargeData();
        $discount_description = "";
		foreach ($paycharges as $paycharge) {
			if ($paycharge['payment_method'] == $this->_payment_method_code) {
				if ( isset($paycharge['amount']) && isset($paycharge['valuep'])
				     && $this->_sub_total >= $paycharge['amount']
				     && abs($paycharge['valuep']) > abs($discount_percent) ) {
				    $paychange_data = $paycharge;
					$discount_percent = abs($paycharge['valuep']);
					if(!empty($paycharge['description'])) {
                        $discount_description = $paycharge['description'][$this->_config->get('config_language_id')]['name'] . ' (' . -$discount_percent . '%)';
                    }else {
                        $discount_description = "Discount";
                    }
                }
			}
		}
        return array(
            'discount_percent' => $discount_percent,
            'discount_total' => $discount_total,
            'paychange_data' => $paychange_data,
            'discount_description' => $discount_description
        );
    }

	private function _doCalculations(&$total_data, &$total, &$taxes, &$cst, $cst_class_id) {
		if ($this->membership_info == null) {
			$this->getMembershipInfo();
		}

		// if membership applied, paycharge discount will not be applicable
		if (!empty($this->membership_info)) {
			return;
		}
        
        $paycharge_discount_data = $this->getPaychargeDiscountPercent();
        $discount_percent = $paycharge_discount_data['discount_percent'];
        $discount_total = $paycharge_discount_data['discount_total'];
        $paychange_data = $paycharge_discount_data['paychange_data'];
        $discount_description = $paycharge_discount_data['discount_description'];

        if ($discount_percent > 0.0) {

            foreach ($this->_cart_products as $key => $product) {

                //If product found in exception rules, skip for discount process
                if( $this->isInExceptionRules( $product ) ) {
                    continue;
                }

                $discount = round(
                        $product['total'] / 100 * $discount_percent, (int) $this->_currency->getDecimalPlace()
                );
                $discount_per_piece = round(
                        $product['price_per_piece'] / 100 * $discount_percent, (int) $this->_currency->getDecimalPlace()
                );
                $discount_total += $discount;

                /* saving discount of each product in cart */

                $ind = base64_encode(serialize(array('product_id' => $product['product_id'])));
                $cart_product_discount = array();
                if (!empty($this->_cart_data[$key]['discount_breakup'])) {
                    $cart_product_discount = unserialize($this->_cart_data[$key]['discount_breakup']);
                }
                $cart_product_discount['paycharge']['payment_method'] = $paychange_data['payment_method'];
                $cart_product_discount['paycharge']['valuep'] = $paychange_data['valuep'];
                $cart_product_discount['paycharge']['amount'] = $paychange_data['amount'];
                $cart_product_discount['paycharge']['value'] = "-$discount_per_piece";
                $this->_cart_data[$key]['discount_breakup'] = serialize($cart_product_discount);

                // End

                if ($product['tax_class_id']) {
                    // will be negative since $discount is negative
                    $tax_rates = $this->_tax->getRates($product['price_per_piece'], $product['tax_class_id'], '', $product['mrp']);

                    foreach ($tax_rates as $tax_rate) {
                        if ($tax_rate['type'] == 'P') {
                            $taxes[$tax_rate['tax_rate_id']] -= ($product['quantity'] *
                                    $product['piece_in_set'] *
                                    $discount_per_piece *
                                    $tax_rate['rate'] / 100);
                        }
                    }

                    if ($cst_class_id && $cst) {
                        // calculated cst deduction will be negative since discount is negative
                        $cst_adjust_val = $this->_tax->getCST($discount, $product['tax_class_id'], $cst_class_id, $product['mrp']);
                        $cst -= $cst_adjust_val;
                    }
                }

                /* save product complete discount */

                $cart_product_discount = 0;
                if (!empty($this->_cart_data[$key]['discount_per_piece'])) {
                    $cart_product_discount = (float) $this->_cart_data[$key]['discount_per_piece'];
                }
                $cart_product_discount += -(float) $discount_per_piece;
                $this->_cart_data[$key]['discount_per_piece'] = $cart_product_discount;

                /* End */
            }


            $total_data[] = array(
                'code' => 'paycharge',
                'title' => $discount_description,
                'value' => -round($discount_total, (int) $this->_currency->getDecimalPlace()),
                'sort_order' => $this->_config->get('paycharge_sort_order')
            );


            $total -= round($discount_total, (int) $this->_currency->getDecimalPlace());
        }
    }

    /**
     * [_doSuborderCalculation -- This method calculates the paycharge value for suborder]
     */
    private function _doSuborderCalculation(&$total_data, &$total, &$taxes, &$cst, $cst_class_id){
        $paycharge = array();
        $discount_total = 0;
        $discount_rate_wise = array();
        foreach ($this->_cart_data as $product) {
            if(!empty($product['discount_breakup'])){
                
                $discount_breakup = unserialize($product['discount_breakup']);
                if(!empty($discount_breakup['paycharge'])){
                    $discount_rate = abs($discount_breakup['paycharge']['valuep']);
                    
                    $discount = (float)$discount_breakup['paycharge']['value'] * (int)$product['piece_in_set'] * (int)$product['quantity'];
                    if (!empty($discount_rate_wise[(string)$discount_rate])) {
                        $discount_rate_wise[(string)$discount_rate]['discount'] += (float) $discount;
                    } else {
                        $discount_rate_wise[(string)$discount_rate]['discount'] = (float) $discount;
                    }
                    $discount_total += (float)$discount;
                    $paycharge = $discount_breakup['paycharge'];
                    
                }
            }
        }
        

        if ($discount_total) {
            $breakup_arr = array();
            if(!empty($discount_rate_wise) && count($discount_rate_wise) > 1) {
                $discount_description = "Discount";
                $breakup_arr = $discount_rate_wise;
            } else {
                $discount_description = "Discount (".$paycharge['valuep']."%)";
            }
            $total_data[] = array(
                'code' => 'paycharge',
				'title' => $discount_description,
				'value' => round($discount_total,(int)$this->_decimal_places),
				'sort_order' => $this->_sort_order['paycharge_sort_order'],
                'breakup' => $breakup_arr
            );

            $total += round($discount_total,(int)$this->_decimal_places); // Adjusting total value for cashback
        }
    }

	public function getTotal(&$total_data, &$total, &$taxes, &$cst=NULL, $cst_class_id=0, $backend=array()) {

    	if(isset($this->_cart)){
            $coupon_data = $this->_cart->getCoupon() ;
            if( !empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code']) ){
                return;
            }
            else if( !empty($coupon_data['coupon_franchise']) ){
                // will not give paycharge discount to franchise customer
                return;
            }
            else if( ( !empty($coupon_data['coupon_franchise_cash']) || !empty($coupon_data['coupon_franchise_credit']) )
                && $this->_cart->checkFranchiseProductsInCart()){
                // will not give paycharge discount to customers of franchise, if cart contains at least one franchise product.
                return;
            }
            else if( !empty($coupon_data['wsb_store_delivery']) && !empty($coupon_data['wsb_store_code']) ) {
                // if same city pickup coupon applied, then paycharge will not be available
                return;
            } else if (!empty($coupon_data['coupon'])) {
                // if percentage discount coupon applied, then paycharge discount will not be available
                if ($coupon_data['coupon_type'] == 'P') return;
            } else if (!empty($coupon_data['coupon_az_discount_five']) || !empty($coupon_data['coupon_az_discount_seven'])) {
                // check if jp a to z inventory discount coupon code applied
            	return;
			}
		}

        if($this->suborder){
            $this->_doSuborderCalculation($total_data, $total, $taxes, $cst, $cst_class_id);
            return;
        }

        if ( !isset($this->_request->get['route']) || $this->_request->get['route'] != 'checkout/cart' ) {

            if($this->_config->get('config_store_id') == INTERNATIONAL_STORE_ID){
                // ignore this if International Store

            } else {
                if($this->_custom_call){
                    $this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
                }
                else if($this->_config->get('paycharge_status')
                    && !empty($this->_session->data['payment_method']['code'])
                    && $this->_cart->getSubTotal()) {

                    $this->_sub_total = (float)$this->_cart->getSubTotal();
                    $this->_cart_products = $this->_cart->getProducts();
                    $this->_payment_method_code = $this->_session->data['payment_method']['code'];
					$this->_cart_data = &$this->_cart->_in_stock_cart_data;
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

        if ( !isset($this->_request->get['route']) || $this->_request->get['route'] != 'checkout/cart' ) {

            if( $this->_config->get('config_store_id') == INTERNATIONAL_STORE_ID ) {
				// Ignore if International Store

			} else {
				if ($this->_config->get('paycharge_status')
                    && !empty($payment_method['code'])
                    && $this->_cart->getSubTotal($user_id)) {

					$this->_sub_total = (float)$this->_cart->getSubTotal($user_id);
					$this->_cart_products = $this->_cart->getProducts($user_id);
					$this->_payment_method_code = $payment_method['code'];

					$this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
				}
			}
		}
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
        Public method to get paycharge data for payment methods
        MSA April 2019  
    */
    public function getPaychargeData(): array
    {
        $sql = "
                SELECT 
                    pr.id,
                    pr.payment_method,
                    pr.valuep,
                    pr.amount,
                    prd.language_id,
                    prd.name
                FROM 
                    " . DB_PREFIX . "paycharge_payment_method_rules AS pr
                LEFT JOIN 
                    " . DB_PREFIX . "paycharge_payment_method_rules_desc AS prd
                        ON prd.rules_id = pr.id
                ";
        $result = $this->_db->query($sql);
        $data = array();
        $language = array();
        if($result->num_rows) {
            foreach ($result->rows as $key => $value) {
                $language[$value['language_id']]['name'] = $value['name'];
                $data[$value['id']] = array(
                    'payment_method' => $value['payment_method'],
                    'valuep'         => $value['valuep'],
                    'amount'         => $value['amount'],
                    'description'    => $language
                );
            }
            $data = array_values($data);
        }
        return $data;
    }

    /*
        Public method to get paycharge exception type(product/seller/category) wise rules
        MSA April 2019  
    */
    public function getPaychargeExceptionRules()
    {
        $sql = "SELECT exception_type, type_id FROM " . DB_PREFIX . "paycharge_exception ";
        $result = $this->_db->query($sql);
        $data = array();
        if($result->num_rows) {
            foreach ($result->rows as $key => $value) {
                $data[$value['exception_type']][] = $value['type_id'];
            }
        }
        return $data;
    }

    /*
        public method to check for a product id exist in exception rules of not
        MSA April 2019
    */
    public function isProductIdExistInExceptionRules($product_id)
    {   
        //get product category
            $product_category = 0;
            $sql_category = "SELECT category_id 
                            FROM " . DB_PREFIX . "product_to_category
                            WHERE
                                product_id = '".(int)$product_id."'
                            ";
            $result_category = $this->_db->query($sql_category);  
            if($result_category->num_rows){
                $product_category = array_column($result_category->rows, 'category_id');
                $product_category = implode(',', $product_category);
            }      
        //get product seller
            $product_seller = 0;
            $sql_seller = "SELECT seller_id 
                            FROM " . DB_PREFIX . "ms_product
                            WHERE
                                product_id = '".(int)$product_id."'
                            ";
            $result_seller = $this->_db->query($sql_seller);  
            if($result_seller->num_rows){
                $product_seller = $result_seller->row['seller_id'];
            }

        // check product id, product category id and product seller id for exception rule
            $sql = "SELECT 
                        id 
                    FROM " . DB_PREFIX . "paycharge_exception 
                    WHERE 
                        ( 
                            exception_type = 'product'
                            AND
                            type_id = '".(int)$product_id."'
                        )
                        OR 
                        (
                            exception_type = 'category'
                            AND
                            type_id IN (".$product_category.") 
                        )
                        OR
                        (
                            exception_type = 'seller'
                            AND
                            type_id = '".(int)$product_seller."'
                        )
                    ";
            $result = $this->_db->query($sql);        
            if($result->num_rows){
                return 1;
            }
            return 0;
    }

}
?>