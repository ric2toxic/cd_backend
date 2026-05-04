<?php

class TotalFactory {

    public $total = 0;
    public $net_payable_amount = 0;
    public $taxes;
    public $cst;
    public $order_id;
    public $total_data = array();
    private $_cst_class_id = CST_CLASS_ID;
    private $_cst;
    private $_tax;
    private $_gst;
    private $_registry;
    private $_cart;
    private $_config;
    private $_load;
    private $_currency;
    private $_language;
    private $_extensions;
    private $_total_dir;
    private $_call_net_payable_amount;
    private $_extentions_details = array(
        'sub_total' => array('class' => 'SubTotal', 'file_name' => 'sub_total.php'),
        'coupon' => array('class' => 'Coupon', 'file_name' => 'coupon.php'),
        'credit' => array('class' => 'Credit', 'file_name' => 'credit.php'),
        'handling' => array('class' => 'Handling', 'file_name' => 'handling.php'),
        'klarna_fee' => array('class' => 'KlarnaFee', 'file_name' => 'klarna_fee.php'),
        'cashback' => array('class' => 'Cashback', 'file_name' => 'cashback.php'),
        'paycharge' => array('class' => 'Paycharge', 'file_name' => 'paycharge.php'),
        'shipping' => array('class' => 'Shipping', 'file_name' => 'shipping.php'),
        'tax' => array('class' => 'TotalTax', 'file_name' => 'tax.php'),
        'total' => array('class' => 'Total', 'file_name' => 'total.php'),
        'membership' => array('class' => 'MembershipDiscount', 'file_name' => 'membership_discount.php')
    );
    private $_sort_order = array(
        '0' => 4,
        '1' => 1,
        '2' => 5,
        '3' => 6,
        '4' => 8,
        '5' => 3,
        '6' => 2,
        '7' => 7,
        '8' => 3
    );
    private $_extension_status = array(
        'shipping' => 1, 
        'sub_total' => 1,
        'tax' => 1,
        'total' => 1,
        'credit' => 1,
        'coupon' => 1,
        'paycharge' => 1,
        'cashback' => 1,
        'membership' => 1
    );

    public function __construct($registry, $call_net_payable_amount= false) {
        $this->_total_dir = DIR_SYSTEM . 'library/total/';
        $this->_registry = $registry;
        $this->_call_net_payable_amount = $call_net_payable_amount;
        $this->_gst = $registry->gst;
        if (method_exists($registry, 'get')) {
            $this->_cart = $registry->get('cart');
            $this->_config = $registry->get('config');
            $this->_load = $registry->get('load');
            $this->_tax = $registry->get('tax');
            $this->_currency = $registry->get('currency');
            $this->_language = $registry->get('language');
        } else if (!empty($registry->suborder)) {
            $this->_registry = $registry;
            if(!empty($registry->order_id)) {
                $this->order_id = $registry->order_id;
            }
        } else {
            $this->_cart = $registry->cart;
            $this->_config = $registry->config;
            $this->_load = $registry->load;
            $this->_tax = $registry->tax;
            $this->_currency = $registry->currency;
            $this->_language = $registry->language;
        }

        $this->_extensions = $this->_getExtensions();
        $this->_loadFiles();
        
        if (!empty($this->_registry->suborder) && $this->_registry->suborder && !empty($this->order_id) && $this->order_id <= POST_ORDER_ID_FOR_CASH_DISCOUNT_COUPON) {
            $this->_sort_order = array(
                '0' => 5,
                '1' => 1,
                '2' => 6,
                '3' => 9,
                '4' => 7,
                '5' => 4,
                '6' => 3,
                '7' => 2,
                '8' => 3
            );
        }
    }

    /**
     * [getTotal - set the order total, total, tax values into the given array ]
     * @param  boolean $cst - if cst is true the cst is applied in total
     * @return [array] -- array of all total which are enabled and applied on current order.
     */
    public function getTotal( $cst = false, $free_shipping_coupon_applied = false ) {
        $cst = false;
        array_multisort($this->_sort_order, SORT_ASC, $this->_extensions);
        // Setting the discount_per_piece, discount_breakup and tax on their initial stages.
        // Because All discount classes add their discounts in the value of cart_product's disocunt_per_piece.
        // Same thing applies of tax in all discount classes.

        $this->total = 0;
        $this->net_payable_amount = 0;
        $this->total_data = array();
        $this->taxes = 0;

        if (empty($this->_registry->suborder)) {
            if (isset($this->_cart->_in_stock_cart_data) && is_array($this->_cart->_in_stock_cart_data)) {
                foreach ($this->_cart->_in_stock_cart_data as $key => $value) {
                    $this->_cart->_in_stock_cart_data[$key]['discount_breakup'] = 0;
                    $this->_cart->_in_stock_cart_data[$key]['discount_per_piece'] = 0;
                }
            }
            $this->taxes = $this->_cart->getTaxes();
        }
        foreach ($this->_extensions as $result) {
            
            // don't show negative coupon total if free shipping coupon
            if ( $result['code'] == 'coupon' && !empty( $free_shipping_coupon_applied )) {
                continue;
            }

            if ($this->_extension_status[$result['code']]) {
                $code_object = $result['code'];
                $code_object = new $this->_extentions_details[$code_object]['class']($this->_registry);

                if ($cst && empty($this->_registry->suborder)) {
                    $this->cst = $this->_cart->getCST($this->_cst_class_id);
                    $code_object->getTotal($this->total_data, $this->total, $this->taxes, $this->cst, $this->_cst_class_id, array(), $free_shipping_coupon_applied );
                } else {
                    if ($result['code'] == 'net_payable_amount') {
                        continue;
                    }
                    $cst = NULL;
                    $code_object->getTotal($this->total_data, $this->total, $this->taxes, $cst, 0, array(), $free_shipping_coupon_applied );
                }
            }
        }

        $this->net_payable_amount = $this->total;

        foreach ($this->_extensions as $result) {

            // don't show negative coupon total if free shipping coupon
            if ( $result['code'] == 'coupon' && !empty( $free_shipping_coupon_applied )) {
                continue;
            }

            if ($this->_extension_status[$result['code']]) {
                $code_object = $result['code'];
                $code_object = new $this->_extentions_details[$code_object]['class']($this->_registry);
                //ifmethodexists getNetPayable
                if (method_exists($code_object, 'getNetPayable')) {
                    $code_object->getNetPayable($this->total_data, $this->total, $this->net_payable_amount);
                }
                if ($result['code'] == 'net_payable_amount' && $this->_call_net_payable_amount) {
                    $code_object->getTotal($this->total_data, $this->net_payable_amount, $this->taxes);
                }
            }
        }
        $sort_order = array();

        foreach ($this->total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $this->total_data);
        
        if($this->_call_net_payable_amount && ((float)$this->net_payable_amount != (float)$this->total)) {
            $this->total_data[] = array(
            'code'       => 'net_payable_amount',
            'title'      => 'Net Payable',
            'value'      => max(0, ROUND($this->net_payable_amount, 2)),
            'sort_order' => 11
        );
        }
        return $this->total_data;
    }

    /**
     * [getTotal - set the order total, total, tax values into the given array ]
     * @param  boolean $cst - if cst is true the cst is applied in total
     * @return [array] -- array of all total which are enabled and applied on current order.
     */
    public function confirm($order_info, $total_datas = array()) {

        $this->total_data = $total_datas;
        if (empty($total_datas)) {
            $this->getTotal();
        }
        
        foreach ($this->total_data as $total) {
            if ($this->_extension_status[$total['code']]) {
                $code_object = $total['code'];
                $code_object = new $this->_extentions_details[$code_object]['class']($this->_registry);
                if (method_exists($code_object, 'confirm')) {
                    $code_object->confirm($order_info, $total);
                }
            }
        }
    }

    /**
     * [_getExtensions This method is for getting the activated extensions]
     * @return [array] [This function return array of extensions]
     */
    private function _getExtensions() {
        $sql = "SELECT e.* FROM " . DB_PREFIX . "extension e WHERE e.type = 'total'";
        $results = $this->_registry->db->query($sql);
        if ($results->num_rows > 0) {
            return $results->rows;
        }
    }

    /**
     * [_loadFiles this files load all file in total library]
     * @return [type] [description]
     */
    private function _loadFiles() {
        foreach ($this->_extentions_details as $extension) {
            if (file_exists($this->_total_dir . $extension['file_name'])) {
                require_once( $this->_total_dir . $extension['file_name'] );
            }
        }
    }

}

?>