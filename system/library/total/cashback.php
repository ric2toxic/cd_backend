<?php

require_once('totalbase.php');

class Cashback extends TotalBase {

    private $_cashback_available = 0.0;
    private $_sub_total = 0.0;
    private $_cart_products = array();
    private $_cashback_breakup;

    public function __construct($registry) {
        parent::__construct($registry);
    }

    /**
     * Internal method to do all the calculations for cashback
     * It requires private members of this class populated in advance.
     * Method helps in avoiding code repetition in Cart and AppCart operations.
     * @Author: Madhur, 2016
     */
    private function _doCalculations(&$total_data, &$total, &$taxes, &$cst, $cst_class_id) {
        // Cashback is not to be adjusted against product price, but 
        // as a cash discount against overall bill value
        return false;
    }

    public function getNetPayable(&$total_data, $total, &$net_payable_amount) {

        if ($this->suborder) {
            return false;
        }
        
        $franchise_id = $this->_cart->getFranchiseId();
       // if this order is by franchise, then we will not use customer's credit in order
       if(!empty($franchise_id) && $this->_cart->checkFranchiseProductsInCart()){
           return false;
       }

        if ($this->_config->get('cashback_status')) {

            // Get available cashback with customer which is not expired yet
            if (!empty($this->_customer->getId())) {
                $cashback_data = $this->_customer->getCashbackAvailable();
            } else if (!empty($this->_config->get('cashback_data_for_unit_testing'))) {
                $cashback_data = $this->_config->get('cashback_data_for_unit_testing');
            }
            if (!empty($cashback_data)) {
                $this->_cashback_available = $cashback_data['total_cashback'];
                $this->_cashback_breakup = $cashback_data['cashback_breakup'];
            }
            $this->_sub_total = $this->_cart->getSubTotal();
            $this->_cart_products = $this->_cart->getProducts();
            $this->_cart_data = &$this->_cart->_in_stock_cart_data;
        }

        $this->_load->language('total/cashback');
        // Cashback applies only when order subtotal is greater than the cart limit set
        if ($this->_cashback_available && $this->_sub_total >= $this->_config->get('config_cart_limit')) {

            // Cashback usable cant be greater than order subtotal
            $cashback_usable = min($this->_cashback_available, $this->_sub_total);
            // If cashback usable greater than total, it must be set to total value
            // Total value cannot go less than 0
            if ($cashback_usable > $total) {
                $cashback_usable = $total;
            }
            
            if ($this->_currency->getCode() == DUMMY_INR_CURRENCY) {
              $cashback_usable = $cashback_usable/$this->_currency->getValue();
            }

            if ($cashback_usable > 0) {
                $total_data[] = array(
                    'code' => 'cashback',
                    'title' => $this->_language->get('text_cashback'),
                    'value' => -round($cashback_usable, (int) $this->_currency->getDecimalPlace()),
                    'sort_order' => $this->_config->get('cashback_sort_order')
                );

                $net_payable_amount -= round($cashback_usable, (int) $this->_currency->getDecimalPlace()); // Adjusting total value for cashback
            }
        }
    }

    /**
     * [_doSuborderCalculation -- This method calculates the cashback for suborder]
     */
    private function _doSuborderCalculation(&$total_data, &$total, &$taxes, &$cst, $cst_class_id) {
        $language = $this->_registry->language->load('total/cashback');
        $cashback_usable = 0;
        foreach ($this->_cart_data as $product) {
            if (!empty($product['discount_breakup'])) {
                $discount_breakup = unserialize($product['discount_breakup']);
                if (!empty($discount_breakup['cashback'])) {
                    $discount = (float) $discount_breakup['cashback']['value'] * (int) $product['piece_in_set'] * (int) $product['quantity'];
                    $cashback_usable += (float) $discount;
                }
            }
        }

        if (!empty($cashback_usable)) {
            $total_data[] = array(
                'code' => 'cashback',
                'title' => $language['text_cashback'],
                'value' => round($cashback_usable, (int) $this->_decimal_places),
                'sort_order' => $this->_sort_order['cashback_sort_order']
            );

            $total += round($cashback_usable, (int) $this->_decimal_places); // Adjusting total value for cashback
        }
    }

    public function getTotal(&$total_data, &$total, &$taxes, &$cst = NULL, $cst_class_id = 0, $backend = array()) {

        if ($this->suborder) {
            $this->_doSuborderCalculation($total_data, $total, $taxes, $cst, $cst_class_id);
            return;
        }

        $franchise_id = $this->_cart->getFranchiseId();
        // if this order is by franchise, then we will not use customer's credit in order
        if(!empty($franchise_id) && $this->_cart->checkFranchiseProductsInCart()){
            return;
        }

		if ($this->_config->get('cashback_status')) {
            $this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);
        }
    }

    public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {

        if ($this->_config->get('cashback_status')) {

            $user_id = $extra['user_id'];

            $cst = NULL;
            $cst_class_id = 0;
            if (isset($extra['cst']) && isset($extra['cst_class_id'])) {
                $cst = $extra['cst'];
                $cst_class_id = $extra['cst_class_id'];
            }

            $this->_doCalculations($total_data, $total, $taxes, $cst, $cst_class_id);

            // Filling the calculated CST
            $extra['cst'] = $cst;
        }
    }

    public function confirm($order_info, $order_total) {
        $this->_load->language('total/cashback');

        if ($order_info['customer_id']) {
            $this->_db->query("INSERT INTO " . DB_PREFIX . "customer_cashback
                              SET customer_id = '" . (int) $order_info['customer_id'] . "',
                                  order_id = '" . (int) $order_info['order_id'] . "',
                                  description = '" . $this->_db->escape(sprintf($this->_language->get('text_order_no'), (float) $order_info['order_no'])) . "',
                                  amount = '" . (float) $order_total['value'] . "',
                                  date_added = NOW(),
                                  validity = NULL, amount_utilized = NULL, expired = NULL");

            // Setting cashback utilization
            // Get all available cashbacks
            $tot_utilize = abs((float)$order_total['value']);

            $sql = "
                    SELECT 
                        customer_cashback_id,
                        suborder_id,
                        (amount - amount_utilized) AS cashback_available, 
                        description 
                    FROM 
                        " . DB_PREFIX . "customer_cashback
                    WHERE 
                        customer_id = '" . (int)$order_info['customer_id'] . "'
                        AND expired = 0
                        AND amount > 0
                        AND (amount - amount_utilized) > 0
                    ORDER BY 
                        date_added ASC
                    ";
            $cashbacks = $this->_db->query($sql );

            foreach ($cashbacks->rows as $cashback) {

                if ($tot_utilize > 0) {

                    $utilize = (($tot_utilize > (float)$cashback['cashback_available']) ? (float)$cashback['cashback_available'] : $tot_utilize);

                    $this->_db->query("UPDATE " . DB_PREFIX . "customer_cashback
                                      SET amount_utilized = amount_utilized + " . (float)$utilize . "
                                      WHERE customer_cashback_id = '" . (int)$cashback['customer_cashback_id'] . "'");

                    $tot_utilize -= $utilize;
                    
                    // merchant_txn_id for order_payment table
                    $mtxnid = (!empty($cashback['suborder_id']) ? 'Cashback From '. $cashback['suborder_id'] : $cashback['description']);
                    
                    $payment_array = array(
                        'order_id'           => $order_info['order_id'],
                        'merchant_txn_id'    => $mtxnid,
                        'order_no'           => $order_info['order_no'],
                        'txn_status'         => 'SUCCESS',
                        'payment_mode'       => 'CASHBACK CREDIT',
                        'amount'             => ((float)$utilize),
                        'payment_gateway'    => 'cashback',
                        'bank_transfer_mode' => 'not_applicable',
                        'successfull'        => '1',
                        'reference'          => 'Ref Cashback Id: '.(int)$cashback['customer_cashback_id'],
                        'payment_link'       => $cashback['description'],
                        'json_format'        => '',
                        'user_id'            => 0,
                        'sales_staff_id'     => 0
                    );
                    OrderPayment::insertOrderPayment($this->_db, $payment_array);
                } else
                    break;
            }
        }
    }

}

?>
