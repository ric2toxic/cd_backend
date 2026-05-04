<?php

require_once('totalbase.php');

class TotalTax extends TotalBase {

    public function __construct($registry) {
        parent::__construct($registry);
    }

    /**
     * Internal method to do all the calculations for tax
     * It requires private members of this class populated in advance.
     * Method helps in avoiding code repetition in Cart and AppCart operations.
     * @Author: Madhur, 2016
     */
    private function _doCalculations(&$total_data, &$total, $taxes) {
        $this->_load->language('total/tax');
        $total_tax = 0;
        foreach ($taxes as $key => $value) {
            /* As per Accounts, all the tax values need to be added up and shown
              as a single value 'Total Tax' */
            if ($value > 0) {
                $total_tax += $value;
            }
        }

        if ($total_tax > 0) {

            $total += $total_tax;

            $total_data[] = array(
                'code' => 'tax',
                'title' => $this->_language->get('text_tax'),
                'value' => round($total_tax, (int) $this->_currency->getDecimalPlace()),
                'sort_order' => $this->_config->get('tax_sort_order')
            );
        }
    }

    private function _doSuborderCalculation(&$total_data, &$total, $taxes) {
        $language = $this->_registry->language->load('total/tax');
        $total_tax = 0;

        foreach ($this->_cart_data as $product) {
            $total_pieces = (int) $product['quantity'] * (int) $product['piece_in_set'];
            $price_after_discount = ((float) $product['price_per_piece'] * $total_pieces) +
                    ((float) $product['discount_per_piece'] * $total_pieces);
            $tax = $price_after_discount * (float) $product['output_tax_rates'] / 100;
            $total_tax += $tax;
        }

        if ($this->_gst) {
            $max_tax_rate_for_shipping = max(array_column($this->_cart_data, 'output_tax_rates'));
            foreach ($total_data as $key => $value) {
                if ($value['code'] == 'shipping') {
                    $total_tax += (float) $value['value'] * (float) $max_tax_rate_for_shipping / 100;
                }
            }
        }
        $total += round($total_tax, (int) $this->_decimal_places);
        $total_data[] = array(
            'code' => 'tax',
            'title' => $language['text_tax'],
            'value' => round($total_tax, (int) $this->_decimal_places),
            'sort_order' => $this->_sort_order['tax_sort_order']
        );
    }

    public function getTotal(&$total_data, &$total, &$taxes, &$cst = NULL, $cst_class_id = 0, $backend = array()) {
        if ($this->suborder) {
            $this->_doSuborderCalculation($total_data, $total, $taxes);
        } else {
            $this->_doCalculations($total_data, $total, $taxes);
        }
    }

    public function getAppTotal(&$total_data, &$total, &$taxes, &$extra) {
        $this->_doCalculations($total_data, $total, $taxes);
    }

}

?>
