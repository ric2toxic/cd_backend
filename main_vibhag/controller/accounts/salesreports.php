<?php

class ControllerAccountsSalesreports extends Controller {

    public function index() {

        $this->load->model('accounts/salesreports');

        // Language AutoLoad
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('accounts/salesreports', $data);

        $this->document->setTitle($data['heading_salesreports_title']);

        // Determining whether its Tally reports or not.
        $is_tally = false;
        if (!empty($this->request->get['tally'])) {
            $is_tally = true;
        }
        $data['is_tally'] = $is_tally;
        $general_url = '';
        $filter_data = array();

        $data['filter_date_from'] = '';
        if (!empty($this->request->get['filter_date_from'])) {
            $filter_date_from = $this->request->get['filter_date_from'];
            $filter_data['filter_date_from'] = $filter_date_from;
            $data['filter_date_from'] = $filter_date_from;
        }
        $data['filter_date_to'] = '';
        if (!empty($this->request->get['filter_date_to'])) {
            $filter_date_to = $this->request->get['filter_date_to'];
            $filter_data['filter_date_to'] = $filter_date_to;
            $data['filter_date_to'] = $filter_date_to;
        }
        $data['filter_invoice_date_from'] = '';
        if (!empty($this->request->get['filter_invoice_date_from'])) {
            $filter_invoice_date_from = $this->request->get['filter_invoice_date_from'];
            $filter_data['filter_invoice_date_from'] = $filter_invoice_date_from;
            $data['filter_invoice_date_from'] = $filter_invoice_date_from;
        }
        $data['filter_invoice_date_to'] = '';
        if (!empty($this->request->get['filter_invoice_date_to'])) {
            $filter_invoice_date_to = $this->request->get['filter_invoice_date_to'];
            $filter_data['filter_invoice_date_to'] = $filter_invoice_date_to;
            $data['filter_invoice_date_to'] = $filter_invoice_date_to;
        }
        $data['filter_customer_id'] = '';
        if (!empty($this->request->get['filter_customer_id'])) {
            $filter_customer_id = $this->request->get['filter_customer_id'];
            $filter_data['filter_customer_id'] = $filter_customer_id;
            $data['filter_customer_id'] = $filter_customer_id;
        }
        
        $data['error'] = false;
        if (empty($filter_data)) {
            $data['error'] = true;
        } else {
            $results = $this->model_accounts_salesreports->getSaleDetails($filter_data);
            $results_for_gst = $this->model_accounts_salesreports->getSaleDetails($filter_data, 1);

            if ($is_tally) {
                $file_name1 = '';
                $file_name2 = '';
                $file_name_array = array();
                $x = 0;
                if (!empty($results)) {
                    $file_name1 = $this->_getCsvForTally($results);
                }
                if (!empty($results_for_gst)) {
                    $file_name2 = $this->_getGSTForTally($results_for_gst);
                }
                $zip_file_name = 'accounts-tally-sale-reports';
                if (!empty($file_name1)) {
                    $file_name_array[$x] = $file_name1;
                    $x++;
                }
                if (!empty($file_name2)) {
                    $file_name_array[$x] = $file_name2;
                    ;
                }
                createAndDownloadZip($file_name_array, $zip_file_name, true, true, true);
            } else {
                $this->_getCsv($results);
            }
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_salesreports'],
            'href' => $this->url->link('accounts/salesreports', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['token'] = $this->session->data['token'];


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('accounts/salesreports.tpl', $data));
    }

    private function _getCsvForTally($results) {
        $this->load->model('accounts/salesreports');
        $details_grouping = $this->_getCsvData($results);
        $file_name = DIR_DOWNLOAD . 'accounts-sale-reports-tally-cst.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
            'Suborder No',
            'Order Date',
            'Sale Invoice Date',
            'Sale Invoice No',
            'WSB Sales State',
            'WSB Tin No',
            'Customer ID - Customer Name - Business Name',
            'Customer Location',
            'Customer State',
            '',
            'Total Product Value',
            'Dicount',
            'Total Tax',
            'Total sales',
            'Shipping Charge',
            'Refundable C Form',
            'Total Amount',
            'Customer Tin No',
            'Delivery Date',
            'Payment Method'
        );
        fputcsv($fp, $data);
        foreach ($details_grouping as $key => $value) {

            $ledger_name = $this->model_accounts_salesreports->getCustomerLedger($value['customer_id']);
            if (isset($value['tax_wise_breakup'])) {
                $i = 0;
                $tin_no = $value['customer_tin_no'] == 'N/A' ? '' : $value['customer_tin_no'];
                foreach ($value['tax_wise_breakup'] as $key => $taxwise_breakup) {
                    $tax_rate = 0;
                    $refundable_c_form = 0;
                    if ($taxwise_breakup['tax_rate'] > 0) {
                        $tax_rate = "VAT({$taxwise_breakup['tax_rate']}%)";
                        if (strtolower($value['wsb_state']) != strtolower($value['payment_zone']) &&
                                $value['code_version'] != 1.0) {
                            $tax_rate = "CST({$taxwise_breakup['tax_rate']}%)";
                        }

                        if (!empty($value['cst']) && $taxwise_breakup['tax_rate'] == 2) {
                            $tax_rate = 'CST(2%)';
                            $refundable_c_form = $value['refund_c_form'];
                        } else {
                            $refundable_c_form = 0;
                        }
                    }
                    $shipping = 0;
                    if ($i == 0) {
                        $shipping = $value['shipping'];
                    }
                    $tot_val = (float) $taxwise_breakup['product_total'] + (float) $shipping;
                    $products_total = (float) $taxwise_breakup['product_total'] - (float) $refundable_c_form;
                    $data = array(
                        $value['order_no'],
                        $value['suborder_id'],
                        $value['order_date'],
                        $value['sale_invoice_date'],
                        $value['sale_invoice_no'],
                        $value['wsb_state'],
                        $value['wsb_tin'],
                        $ledger_name,
                        $value['payment_city'],
                        $value['payment_zone'],
                        $tax_rate,
                        (ROUND((($taxwise_breakup['subtotal'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($taxwise_breakup['discount'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($taxwise_breakup['tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($products_total * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($shipping * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        $refundable_c_form,
                        (ROUND((($tot_val * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        $tin_no,
                        $value['delivery_date'],
                        $value['payment_code']
                    );
                    fputcsv($fp, $data);
                    $i++;
                }
            }
        }

        fclose($fp);
        return $file_name;
    }

    private function _getGSTForTally($results) {

        $order_id_arr = array_unique(array_column($results, 'order_id'));
        asort($order_id_arr);
        $order_id_arr = array_values($order_id_arr);
        $min = POST_ORDER_ID_FOR_CASH_DISCOUNT_COUPON;
        $new_order_id_arr = array_filter(
                $order_id_arr, function ($value) use($min) {
            return ($value >= $min);
        }
        );
        $new_order_id_arr = array_values($new_order_id_arr);

        $this->load->model('accounts/salesreports');
        $cash_discount_suborder_wise = array();
        if(!empty($new_order_id_arr)) {
            $cash_discount_suborder_wise = $this->model_accounts_salesreports->getOrderCashDiscount($new_order_id_arr);
        }
        $details_grouping = $this->_getGSTData($results, $cash_discount_suborder_wise);
        $file_name = DIR_DOWNLOAD . 'accounts-sale-reports-tally-gst.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
            'Suborder No',
            'Order Date',
            'Sale Invoice Date',
            'Sale Invoice No',
            'WSB Sales State',
            'WSB GSTIN',
            'Customer ID - Customer Name - Business Name',
            'Customer Location',
            'Customer State',
            'GST Rate',
            'Total Product Value',
            'Dicount',
            'Shipping Charge',
            'CGST',
            'SGST',
            'IGST',
            'Total Tax',
            'Total sales',
            'Customer GSTIN',
            'Courier Partner',
            'Tracking No.',
            'Delivery Date'/* ,
              'COD amount', */,
            'Cash Discount',
            'Payment Method'
        );
        fputcsv($fp, $data);
        foreach ($details_grouping as $key => $value) {

            $ledger_name = $this->model_accounts_salesreports->getCustomerLedger($value['customer_id']);
            if (isset($value['tax_wise_breakup'])) {
                $i = 0;
                $tin_no = $value['customer_tin_no'] == 'N/A' ? '' : $value['customer_tin_no'];
                foreach ($value['tax_wise_breakup'] as $key => $taxwise_breakup) {
                    $tax_rate = 0;
                    if ($taxwise_breakup['tax_rate'] > 0) {
                        $tax_rate = "{$taxwise_breakup['tax_rate']}";
                        if (strtolower($value['wsb_state']) != strtolower($value['payment_zone']) &&
                                $value['code_version'] != 1.0) {
                            $tax_rate = "{$taxwise_breakup['tax_rate']}";
                        }
                    }
                    $shipping = 0;
                    $shipping_tax = 0;
                    $cash_discount = 0;
                    if ((float) $tax_rate === (float) $value['ship_tax_rate']) {
                        $shipping = $value['shipping'];
                        $shipping_tax = (float) $shipping * (float) $value['ship_tax_rate'] / 100;
                        $cash_discount = $value['cash_discount'];
                    }
                    $tot_val = (float) $taxwise_breakup['product_total'] + ((float) $shipping + $shipping_tax);
                    $products_total = (float) $taxwise_breakup['product_total'];

                    if ((float) $tax_rate === (float) $value['ship_tax_rate']) {
                        $taxwise_breakup['tax'] = $taxwise_breakup['tax'] + $shipping_tax;
                    }
                    $taxwise_breakup['cgst_tax'] = (!$value['is_igst']) ? $taxwise_breakup['tax'] / 2 : '';
                    $taxwise_breakup['sgst_tax'] = (!$value['is_igst']) ? $taxwise_breakup['tax'] / 2 : '';
                    $taxwise_breakup['igst_tax'] = ($value['is_igst']) ? $taxwise_breakup['tax'] : '';

                    $data = array(
                        $value['order_no'],
                        $value['suborder_id'],
                        $value['order_date'],
                        $value['sale_invoice_date'],
                        $value['sale_invoice_no'],
                        $value['wsb_state'],
                        $value['wsb_tin'],
                        $ledger_name,
                        $value['payment_city'],
                        $value['payment_zone'],
                        $tax_rate,
                        (ROUND((($taxwise_breakup['subtotal'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($taxwise_breakup['discount'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($shipping * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (!empty($taxwise_breakup['cgst_tax']) ? (ROUND((($taxwise_breakup['cgst_tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)) : ''),
                        (!empty($taxwise_breakup['sgst_tax']) ? (ROUND((($taxwise_breakup['sgst_tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)) : ''),
                        (!empty($taxwise_breakup['igst_tax']) ? (ROUND((($taxwise_breakup['igst_tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)) : ''),
                        (ROUND((($taxwise_breakup['tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($tot_val * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        $tin_no,
                        $value['courier_partner'],
                        $value['tracking_no'],
                        $value['delivery_date'],
                        /* ,
                          $value['net_amount'] */
                        (ROUND((($cash_discount * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        $value['payment_code']
                    );
                    fputcsv($fp, $data);
                    $i++;
                }
            }
        }

        fclose($fp);
        return $file_name;
    }

    private function _getCsv($results) {
        $details_grouping = $this->_getCsvData($results);
        $file_name = DIR_DOWNLOAD . 'accounts-sale-reports.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
            'Suborder No',
            'Order Date',
            'Sale Invoice Date',
            'Sale Invoice No',
            'WSB Tin No',
            'Customer Id',
            'Customer Name',
            'Business Name',
            'Location',
            'Total Product Value',
            'Dicount',
            'Total Tax',
            'Shipping',
            'Total sales',
            'Payment Method');
        fputcsv($fp, $data);

        foreach ($details_grouping as $key => $value) {
            $data = array($value['order_no'],
                $value['suborder_id'],
                $value['order_date'],
                $value['sale_invoice_date'],
                $value['sale_invoice_no'],
                $value['wsb_tin'],
                $value['customer_id'],
                $value['customer_name'],
                $value['buiness_name'],
                $value['payment_city'],
                $value['total_product'],
                $value['total_discount'],
                $value['total_tax'],
                $value['shipping'],
                $value['total_sales'],
                '');
            if (isset($value['tax_wise_breakup'])) {
                foreach ($value['tax_wise_breakup'] as $key => $taxwise_breakup) {
                    $tax_rate = $taxwise_breakup['tax_rate'];
                    $tax_rate = $tax_rate > 0 ? "VAT($tax_rate%)" : "$tax_rate%";
                    $breakup = array('',
                        $taxwise_breakup['subtotal'],
                        $taxwise_breakup['discount'],
                        $taxwise_breakup['tax'],
                        $tax_rate,
                        $taxwise_breakup['product_total'],
                        $value['payment_code']
                    );
                    $data = array_merge($data, $breakup);
                }
            }

            fputcsv($fp, $data);
        }

        fclose($fp);

        if (file_exists($file_name)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
        }
    }

    private function _getCsvData($results) {
        $details_grouping = array();

        foreach ($results as $key => $result) {
            $order_id = $result['order_id'];
            $suborder_id = $result['suborder_id'];
            $buyer_invoice = new BuyerInvoice($this);
            if (!empty($this->request->get['tally'])) {
                $buyer_invoice->setOptions('skip_cst', false);
            }
            $total_breakups = $buyer_invoice->getTotalsBreakups($order_id, $suborder_id);
            if (isset($total_breakups['cst'])) {
                $breakups = array();
                foreach ($total_breakups['tax_wise_breakup'] as $tax => $breakup) {
                    if ($tax == 0) {
                        $breakups[0] = $breakup;
                    } else if (empty($breakup[2])) {
                        $breakups[2] = $breakup;
                        $breakups[2]['tax'] = $total_breakups['cst'];
                        $breakups[2]['tax_rate'] = 2;
                    } else {
                        $breakups[2]['subtotal'] += (float) $breakup['subtotal'];
                        $breakups[2]['discount'] += (float) $breakup['discount'];
                        $breakups[2]['product_total'] += (float) $breakup['product_total'];
                    }
                }
                $total_breakups['tax_wise_breakup'] = $breakups;
            }
            $key = $order_id . ';' . $suborder_id;
            if (!isset($details_grouping[$key])) {
                $i = 0;
                $details_grouping[$key] = array();
                $details_grouping[$key]['order_no'] = $result['order_no'];
                $details_grouping[$key]['suborder_id'] = $result['suborder_id'];
                $tin_no = 'N/A';
                $payment_custom_field = $result['payment_custom_field'];
                if (!empty($payment_custom_field)) {
                    $payment_custom_field = unserialize($payment_custom_field);
                    if (!empty($payment_custom_field[1])) {
                        $tin_no = trim($payment_custom_field[1]) == '' ? 'N/A' : $payment_custom_field[1];
                    }
                }
                $details_grouping[$key]['customer_tin_no'] = $tin_no;
                $details_grouping[$key]['order_date'] = date('d/m/Y', strtotime($result['order_date']));

                $details_grouping[$key]['sale_invoice_date'] = 'n/a';
                $details_grouping[$key]['sale_invoice_no'] = 'n/a';
                if ($result['invoice_no']) {
                    $details_grouping[$key]['sale_invoice_date'] = date('d/m/Y', strtotime($result['invoice_date']));
                    $details_grouping[$key]['sale_invoice_no'] = $result['invoice_no'];
                }

                $details_grouping[$key]['buiness_name'] = $result['payment_company'];
                $details_grouping[$key]['payment_city'] = $result['payment_city'];
                $details_grouping[$key]['payment_zone'] = $result['payment_zone'];
                $details_grouping[$key]['payment_code'] = $result['payment_code'];
                $details_grouping[$key]['customer_name'] = $result['customer_name'];
                $details_grouping[$key]['customer_id'] = $result['customer_id'];
                $details_grouping[$key]['code_version'] = $result['code_version'];
                $details_grouping[$key]['currency_value'] = $result['currency_value'];
                $details_grouping[$key]['live_currency_conversion_rate'] = $result['live_currency_conversion_rate'];
                $details_grouping[$key]['delivery_date'] = $result['delivery_date'];

                $wsb_tin = '';
                $wsb_state = '';
                $purchased_firm = $buyer_invoice->getPurchaseFirmAddress($order_id, $suborder_id);
                if (!empty($purchased_firm)) {
                    reset($purchased_firm);
                    $wsb_tin = $purchased_firm[key($purchased_firm)]['tin'];
                    $wsb_state = $purchased_firm[key($purchased_firm)]['state'];
                } else {
                    $wsb_tin = '08195900085';
                }

                $details_grouping[$key]['wsb_tin'] = $wsb_tin;
                $details_grouping[$key]['wsb_state'] = $wsb_state;
                $details_grouping[$key]['cst'] = 0;
                if (!empty($total_breakups['cst'])) {
                    $details_grouping[$key]['cst'] = $total_breakups['cst'];
                }

                $total = 0;
                $details_grouping[$key]['total_product'] = 0;
                if (!empty($total_breakups['subtotal'])) {
                    $details_grouping[$key]['total_product'] = $total_breakups['subtotal'];
                }
                $total += (float) $total_breakups['subtotal'];

                $details_grouping[$key]['total_tax'] = 0;
                if (!empty($total_breakups['subtotal'])) {
                    $details_grouping[$key]['total_tax'] = !empty($total_breakups['tax']) ? $total_breakups['tax'] : 0;
                }
                if (!empty($total_breakups['tax'])) {
                    $total += (float) $total_breakups['tax'];
                }


                $details_grouping[$key]['total_discount'] = 0;
                if (!empty($total_breakups['subtotal'])) {
                    $details_grouping[$key]['total_discount'] = $total_breakups['discount'];
                }
                $total += (float) $total_breakups['discount'];

                $details_grouping[$key]['shipping'] = 0;
                if (!empty($total_breakups['subtotal'])) {
                    $details_grouping[$key]['shipping'] = $total_breakups['shipping'];
                }
                if (!empty($total_breakups['cst'])) {
                    $details_grouping[$key]['cst'] = $total_breakups['cst'];
                }
                if (!empty($total_breakups['refund_c_form'])) {
                    $details_grouping[$key]['refund_c_form'] = $total_breakups['refund_c_form'];
                }
                $total += (float) $total_breakups['shipping'];

                $details_grouping[$key]['total_sales'] = $total;

                if (!empty($total_breakups['tax_wise_breakup'])) {
                    $details_grouping[$key]['tax_wise_breakup'] = $total_breakups['tax_wise_breakup'];
                }
            }
            $i++;
        }
        return $details_grouping;
    }

    private function _getGSTData($results, $cash_discount_suborder_wise = array()) {
        $details_grouping = array();
        $shipping_tax_rate = 0;
        foreach ($results as $key => $result) {
            $order_id = $result['order_id'];
            $suborder_id = $result['suborder_id'];
            $buyer_invoice = new BuyerInvoice($this);
            if (!empty($this->request->get['tally'])) {
                $buyer_invoice->setOptions('skip_cst', false);
            }
            $total_breakups = $buyer_invoice->getTotalsBreakups($order_id, $suborder_id);
            //$total_data = $buyer_invoice->getTotals($order_id, $suborder_id);
            $shipping_tax_rate = max(array_keys($total_breakups['tax_wise_breakup']));
//            if (isset($total_breakups['cst'])) {
//                $breakups = array();
//                $shipping_tax_rate = max(array_keys($total_breakups['tax_wise_breakup']));
//                foreach ($total_breakups['tax_wise_breakup'] as $tax => $breakup) {
//                    if ($tax == 0) {
//                        $breakups[0] = $breakup;
//                    } else if (empty($breakup[2])) {
//                        $breakups[2] = $breakup;
//                        $breakups[2]['tax'] = $total_breakups['cst'];
//                        $breakups[2]['tax_rate'] = 2;
//                    } else {
//                        $breakups[2]['subtotal'] += (float) $breakup['subtotal'];
//                        $breakups[2]['discount'] += (float) $breakup['discount'];
//                        $breakups[2]['product_total'] += (float) $breakup['product_total'];
//                    }
//                }
//                $total_breakups['tax_wise_breakup'] = $breakups;
//            }
//echo $shipping_tax_rate;
            $key = $order_id . ';' . $suborder_id;
            if (!isset($details_grouping[$key])) {
                $is_igst = 1;
                $details_grouping[$key] = array();
                $details_grouping[$key]['order_no'] = $result['order_no'];
                $details_grouping[$key]['suborder_id'] = $result['suborder_id'];


                $tin_no = !empty($result['gst_number']) ? $result['gst_number'] : 'N/A';
                $details_grouping[$key]['customer_tin_no'] = $tin_no;
                $details_grouping[$key]['order_date'] = date('d/m/Y', strtotime($result['order_date']));

                $details_grouping[$key]['sale_invoice_date'] = 'n/a';
                $details_grouping[$key]['sale_invoice_no'] = 'n/a';
                if ($result['invoice_no']) {
                    $details_grouping[$key]['sale_invoice_date'] = date('d/m/Y', strtotime($result['invoice_date']));
                    $details_grouping[$key]['sale_invoice_no'] = $result['invoice_no'];
                }

                $details_grouping[$key]['buiness_name'] = $result['payment_company'];
                $details_grouping[$key]['payment_city'] = $result['payment_city'];
                $details_grouping[$key]['payment_zone'] = $result['payment_zone'];
                $details_grouping[$key]['payment_code'] = $result['payment_code'];
                $details_grouping[$key]['customer_name'] = $result['customer_name'];
                $details_grouping[$key]['customer_id'] = $result['customer_id'];
                $details_grouping[$key]['code_version'] = $result['code_version'];
                $details_grouping[$key]['currency_value'] = $result['currency_value'];
                $details_grouping[$key]['live_currency_conversion_rate'] = $result['live_currency_conversion_rate'];
                $details_grouping[$key]['courier_partner'] = $result['courier_partner'];
                $details_grouping[$key]['tracking_no'] = $result['tracking_no'];
                $details_grouping[$key]['delivery_date'] = $result['delivery_date'];
                $details_grouping[$key]['cash_discount'] = 0;
                if (!empty($cash_discount_suborder_wise[$suborder_id])) {
                    $details_grouping[$key]['cash_discount'] = $cash_discount_suborder_wise[$suborder_id];
                }
//                $details_grouping[$key]['net_amount'] = 0;
//                if (!empty($total_data['net_amount'])) {
//                    $details_grouping[$key]['net_amount'] = $total_data['net_amount']['value'];
//                }
                $wsb_tin = '';
                $wsb_state = '';
                $wsb_state_code = '';
                $seller_state_code = '';
                $purchased_firm = $buyer_invoice->getPurchaseFirmAddress($order_id, $suborder_id);
                if (!empty($purchased_firm)) {
                    reset($purchased_firm);
                    $wsb_tin = $purchased_firm[key($purchased_firm)]['tin'];
                    $wsb_state = $purchased_firm[key($purchased_firm)]['state'];
                    if (!empty($purchased_firm[key($purchased_firm)]['state_code'][0]['gst_state_code'])) {
                        $wsb_state_code = $purchased_firm[key($purchased_firm)]['state_code'][0]['gst_state_code'];
                    }
                } else {
                    $wsb_tin = '08195900085';
                }
                $this->load->model('localisation/zone', 'frontend');
                if (method_exists($this->registry, 'get')) {
                    $model_zone_gstcode = $this->registry->get('frontend_model_localisation_zone');
                } else {
                    $model_zone_gstcode = $this->registry->frontend_model_localisation_zone;
                }
                $get_zone_state_code = $model_zone_gstcode->getZoneGSTStateCode($result['payment_zone_id']);

                if (!empty($get_zone_state_code)) {
                    $seller_state_code = $get_zone_state_code[0]['gst_state_code'];
                }

                if (!empty($wsb_state_code) && !empty($seller_state_code)) {
                    $is_igst = ((int) $wsb_state_code === (int) $seller_state_code) ? 0 : 1;
                }

                if (!empty($seller_state_code)) {
                    $details_grouping[$key]['payment_zone'] = $seller_state_code . '-' . $result['payment_zone'];
                }

                $details_grouping[$key]['wsb_tin'] = $wsb_tin;
                $details_grouping[$key]['wsb_state'] = $wsb_state;
                $details_grouping[$key]['cst'] = 0;
                if (!empty($total_breakups['cst'])) {
                    $details_grouping[$key]['cst'] = $total_breakups['cst'];
                }

                $total = 0;
                $details_grouping[$key]['total_product'] = 0;
                if (!empty($total_breakups['subtotal'])) {
                    $details_grouping[$key]['total_product'] = $total_breakups['subtotal'];
                }
                $total += (float) $total_breakups['subtotal'];

                $details_grouping[$key]['total_tax'] = 0;
                if (!empty($total_breakups['subtotal'])) {
                    $details_grouping[$key]['total_tax'] = !empty($total_breakups['tax']) ? $total_breakups['tax'] : 0;
                }
                if (!empty($total_breakups['tax'])) {
                    $total += (float) $total_breakups['tax'];
                }


                $details_grouping[$key]['total_discount'] = 0;
                if (!empty($total_breakups['subtotal'])) {
                    $details_grouping[$key]['total_discount'] = $total_breakups['discount'];
                }
                $total += (float) $total_breakups['discount'];

                $details_grouping[$key]['shipping'] = 0;
                if (!empty($total_breakups['subtotal'])) {
                    $details_grouping[$key]['shipping'] = $total_breakups['shipping'];
                }
                if (!empty($total_breakups['cst'])) {
                    $details_grouping[$key]['cst'] = $total_breakups['cst'];
                }
                $total += (float) $total_breakups['shipping'];

                $details_grouping[$key]['total_sales'] = $total;

                if (!empty($total_breakups['tax_wise_breakup'])) {
                    $details_grouping[$key]['tax_wise_breakup'] = $total_breakups['tax_wise_breakup'];
                }
                $details_grouping[$key]['is_igst'] = $is_igst;
                $details_grouping[$key]['ship_tax_rate'] = $shipping_tax_rate;
            }
        }
        return $details_grouping;
    }

}
