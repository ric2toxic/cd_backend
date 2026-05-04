<?php

final class Tax {

    private $_tax_rates = array();
    private $_hsn_to_tax_class = array();

    public function __construct($registry) {
        if (method_exists($registry, 'get')) {
            $this->db = $registry->get('db');
        }else{
            $this->db = $registry->db;
        }

        $this->setTaxRatesForTaxClassIds();
        $this->setTaxClassIdsForHSNCodes();
    }

    /**
     * Method to get and locally store all the tax rates for every tax class id
     */
    private function setTaxRatesForTaxClassIds() {

        $tax_query = $this->db->query("SELECT DISTINCT 
                                                    tr1.tax_class_id, 
                                                    tr2.tax_rate_id, 
                                                    tr2.name, 
                                                    tr2.type, 
                                                    tr2.start_range, 
                                                    tr2.end_range, 
                                                    tr2.rate, 
                                                    tr1.priority 
                                       FROM oc_tax_rule tr1 
                                       LEFT JOIN oc_tax_rate tr2 
                                       ON (tr1.tax_rate_id = tr2.tax_rate_id) 
                                       WHERE 1 = 1 
                                       ORDER BY tr1.tax_class_id ASC, tr1.priority ASC , tr2.start_range ASC");

        foreach ($tax_query->rows as $result) {
            $this->_tax_rates[$result['tax_class_id']]['tax_rate_id'] = $result['tax_rate_id'];
            $this->_tax_rates[$result['tax_class_id']]['name'] = $result['name'];
            $this->_tax_rates[$result['tax_class_id']]['type'] = $result['type'];
            $this->_tax_rates[$result['tax_class_id']]['priority'] = $result['priority'];
            $this->_tax_rates[$result['tax_class_id']]['tax_field'][] = $result;
        }

        return true;
    }

    /**
     * Method to get and locally store all the tax class ids for every HSN Code
     */
    private function setTaxClassIdsForHSNCodes() {

        $query = $this->db->query("SELECT hsn_code, tax_class_id FROM " . DB_PREFIX . "hsn WHERE 1");

        if ($query->num_rows) {
            $this->_hsn_to_tax_class = array_combine(array_column($query->rows, 'hsn_code'), array_column($query->rows, 'tax_class_id')
            );
        }
    }

    /*
     * method to return tax_class_id when provided with hsn_code
     * Returns false, if no Tax Class ID found
     */

    public function getTaxClassIdFromHSNCode($hsn_code) {

        if (isset($this->_hsn_to_tax_class[$hsn_code])) {
            return $this->_hsn_to_tax_class[$hsn_code];
        } else {
            return false;
        }
    }

    /**
     * Ensure that the $value is in 1 Base Unit of Measurement
     */
    public function calculate($value, $tax_class_id, $calculate = true, $mrp = 0) {
        //$calculate = true;
        if ($tax_class_id && $calculate) {
            $amount = 0;

            $tax_rates = $this->getRates($value, $tax_class_id, '', $mrp);
            foreach ($tax_rates as $tax_rate) {
                if ($calculate != 'P' && $calculate != 'F') {
                    $amount += $tax_rate['amount'];
                } elseif ($tax_rate['type'] == $calculate) {
                    $amount += $tax_rate['amount'];
                }
            }

            return $value + $amount;
        } else {
            return $value;
        }
    }

    /**
     * Method to calculate the tax value on an amount, 
     * given HSN Code, 
     * optional Tax class, 
     * and optional Tax Rates
     */
    public function getTax($value, $hsn_code, $tax_class_id = '', $modified_tax_rates = array(), $mrp = 0) {

        if (empty($tax_class_id)) {
            $tax_class_id = $this->getTaxClassIdFromHSNCode($hsn_code);
        }

        $amount = 0;

        if (!empty($tax_class_id)) {

            $tax_rates = $this->getRates($value, $tax_class_id, $modified_tax_rates, $mrp);

            foreach ($tax_rates as $tax_rate) {
                $amount += $tax_rate['amount'];
            }
        }
        return $amount;
    }

    public function getRateName($tax_rate_id) {
        $tax_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "tax_rate WHERE tax_rate_id = '" . (int) $tax_rate_id . "'");

        if ($tax_query->num_rows) {
            return $tax_query->row['name'];
        } else {
            return false;
        }
    }

    /**
     * This functions returns tax rates array for a particular tax class id
     * given value. 
     * Ensure that the value provided is Base Value (corresponding to 1 Unit of Measurement)
     */
    public function getRates($value, $tax_class_id, $modified_tax_rates = array(), $mrp = 0) {

        $tax_rate_data = array();
        $tax_rates = array();

        if (!empty($modified_tax_rates)) {
            $tax_rates = $modified_tax_rates;
        } else {
            $tax_rates = $this->_tax_rates;
        }

        $tax_rate_for_amount = 0;
        if (isset($tax_rates[$tax_class_id]) && !empty($tax_rates[$tax_class_id]['tax_field'])) {
            if (isset($tax_rate_data[$tax_rates[$tax_class_id]['tax_rate_id']])) {
                $amount = $tax_rate_data[$tax_rate['tax_rate_id']]['amount'];
            } else {
                $amount = 0;
            }

            if ($tax_rates[$tax_class_id]['type'] == 'F') {
                $amount += $tax_rate['rate'];
            } elseif ($tax_rates[$tax_class_id]['type'] == 'P') {
                $tax_rate_for_amount = $this->getTaxRate($value, $tax_class_id, $tax_rates, $mrp);
                $amount += ($value / 100 * $tax_rate_for_amount);
            }

            $tax_rate_data[$tax_rates[$tax_class_id]['tax_rate_id']] = array(
                'tax_rate_id' => $tax_rates[$tax_class_id]['tax_rate_id'],
                'name' => $tax_rates[$tax_class_id]['name'],
                'rate' => $tax_rate_for_amount,
                'type' => $tax_rates[$tax_class_id]['type'],
                'amount' => $amount
            );
        }
        return $tax_rate_data;
    }

    public function getCST($value, $tax_class_id, $cst_class_id, $mrp=0) {

        // Original Tax Class value
        $tax_class_amount = 0;

        if (isset($this->_tax_rates[$tax_class_id])) {

            if ($this->_tax_rates[$tax_class_id]['type'] == 'F') {
                $tax_class_amount += $this->_tax_rates[$tax_class_id]['type']['rate'];
            } elseif ($this->_tax_rates[$tax_class_id]['type'] == 'P') {
                $tax_rate_for_amount = $this->getTaxRate($value, $tax_class_id, '', $mrp);
                $tax_class_amount += ($value / 100 * $tax_rate_for_amount);
            }
        }

        // CST Value - harcoded at 2% currently
        $cst_amount = $value * (2 / 100);

        return min($tax_class_amount, $cst_amount);
    }

    public function has($tax_class_id) {

        return isset($this->_tax_rates[$tax_class_id]);
    }

    //method to find out the tax rate suitable for the given value and given tax class id and optional Tax Rates
    public function getTaxRate($value, $tax_class_id, $modified_tax_rates = array(), $mrp = 0) {

        $tax_rate_for_amount = 0;

        if (empty($modified_tax_rates)) {
            $modified_tax_rates = $this->_tax_rates;
        }

        if (isset($modified_tax_rates[$tax_class_id])) {

            foreach ($modified_tax_rates[$tax_class_id]['tax_field'] as $tax_rate) {
                if (!empty($tax_rate)) {

                    if ($tax_class_id == TAX_CLASS_ID_FOR_FOOTWEAR) {
                        
                        if(!empty($mrp) && (float) $mrp <= LOWEST_RANGE_FOR_FOOTWEAR && (float) $mrp>0) {
                            return LOWEST_GST_FOR_FOOTWEAR;
                        } else {
                            return HIGHEST_GST_FOR_FOOTWEAR;
                        }
                    } else {
                        if ($tax_rate['start_range'] <= $value && $tax_rate['end_range'] >= $value) {
                            $tax_rate_for_amount = (float) $tax_rate['rate'];
                            return $tax_rate_for_amount;
                        }
                    }
                }
            }
        }
        return $tax_rate_for_amount;
    }

    /*
     * method calculates tax rates for Tax Included Price
     * This method will return the seller tax which will be used for calucalting new 
     * seller price and eventually use getTaxRate() to get the 
     * output tax rate to be used in product listing
     * */

    public function getTaxRateForTaxIncludedPrice($price, $hsn_code, $mrp=0) {

        $tax_class_id = $this->getTaxClassIdFromHSNCode($hsn_code);
        if (!$tax_class_id) { // Handling when HSN code is not alloted Tax class id
            return false;
        }

        $modified_tax_rates = $this->_tax_rates;

        //reconstructing the tax_field array based on the tax rates for that range
        if (isset($modified_tax_rates[$tax_class_id])) {

            foreach ($modified_tax_rates[$tax_class_id]['tax_field'] as $key => $tax_rate) {

                if ($modified_tax_rates[$tax_class_id]['type'] == 'P' && $key < count($modified_tax_rates[$tax_class_id]['tax_field']) - 1) {

                    //setting the end range according to the set tax rate for that range
                    $modified_tax_rates[$tax_class_id]['tax_field'][$key]['end_range'] = round($tax_rate['end_range'] + $tax_rate['end_range'] * $tax_rate['rate'] / 100, 2);
                    //setting the start range for the next key according to the new end range for the previous one
                    $modified_tax_rates[$tax_class_id]['tax_field'][$key + 1]['start_range'] = ($modified_tax_rates[$tax_class_id]['tax_field'][$key]['end_range']) + 1e-2;
                }
            }
        }

        //getting the seller rate based on the new range 
        $seller_tax_rate = $this->getTaxRate($price, $tax_class_id, $modified_tax_rates, $mrp);

        return $seller_tax_rate;
    }

}
