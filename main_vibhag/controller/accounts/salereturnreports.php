<?php

class ControllerAccountsSalereturnreports extends Controller {

    public function index() {
		
		$data = array();
		// Autoloading the lanugage
		$this->load->autoLoadLanguage('accounts/salesreports', $data);
		
		$data['heading_salereturnreports'] = 'Sale Return Report';

        $this->load->model('accounts/salereturnreports');

        $this->document->setTitle($data['heading_salereturnreports']);

        // Determining whether its Tally reports or not.
        $is_tally = false;
        if (!empty($this->request->get['tally'])) {
            $is_tally = $this->request->get['tally'];
        }

        $data['is_tally'] = $is_tally;

        $general_url = '';
        $filter_data = array();
        $data['filter_credit_note_date_from'] = '';
        $data['filter_credit_note_date_to'] = '';
        $data['filter_invoice_date_from'] = '';
        $data['filter_invoice_date_to'] = '';
        $data['filter_customer_id'] = '';
        $filter_data = array();
        foreach ($this->request->get as $key => $value) {
            $data[$key] = $value;
            if (!in_array($key, array('route', 'token', 'tally'))) {
                $filter_data[$key] = $value;
            }
        }
        $data['error'] = false;
        if (empty($filter_data)) {
            $data['error'] = true;
        } else {
            $results = $this->model_accounts_salereturnreports->getSaleDetails($filter_data, 0);
            $results_for_gst = $this->model_accounts_salereturnreports->getSaleDetails($filter_data, 1);

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
                $zip_file_name = 'accounts-tally-sale-return-reports';
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
            'text' => $data['heading_salereturnreports'],
            'href' => $this->url->link('accounts/salereturnreports', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['token'] = $this->session->data['token'];


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('accounts/salereturnreports.tpl', $data));
    }

    private function _getCsvForTally($results) {
        $this->load->model('accounts/salesreports');
        $details_grouping = $this->_getCsvData($results);
        $file_name = DIR_DOWNLOAD . 'accounts-sale-reports-tally-cst.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
                    'Suborder No',
                    'Sale Invoice No',
                    'CN No',
                    'CN Date',
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
                    'Refunable Amount',
                    'Customer Tin No',
                    'Sale Invoice Date',
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
                    $tot_val = (float) $taxwise_breakup['product_total'] - (float) $shipping;
                    $products_total = (float) $taxwise_breakup['product_total'] - (float) $refundable_c_form;
                    $data = array(
                        $value['order_no'],
                        $value['suborder_id'],
                        $value['sale_invoice_no'],
                        $value['credit_note_no'],
                        $value['credit_note_date'],
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
                        $value['sale_invoice_date'],
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
        $this->load->model('accounts/salesreports');
        $details_grouping = $this->_getGSTData($results);

        $file_name = DIR_DOWNLOAD . 'accounts-sale-reports-tally-gst.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
            'Suborder No',
            'Order Date',
            'Sale Invoice No',
            'CN Invoice Date',
            'CN Invoice No',
            'WSB Sales State',
            'WSB GSTIN',
            'Customer ID - Customer Name - Business Name',
            'Customer Location',
            'Customer State',
            'GST Rate',
            'Total Product Value',
            'Discount',
            'Reverse Shipping Charge',
            'Shipping on COD Failed Sales',
            'Reversal of Shipping Charge', 
            'CGST',
            'SGST',
            'IGST',
            'Total Tax',
            'Total sales',
            'Total Penalty',
            'Customer GSTIN',
            'Sale Invoice Date',
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
                    $shipping_reverce = 0;
                    $shipping_cod_failed = 0;
                    $reversal_shipping = 0; // Reversal of shipping charge
                    $reversal_shipping_tax = 0; // Tax on Reversal of Shipping charges
                    
                    if ((float) $tax_rate === (float) $value['ship_tax_rate']) {
                        $shipping = $value['is_cod_failed'] ? $value['cod_failed_shipping_charge'] : $value['shipping'];
                        $shipping_tax = round(((float) $shipping * (float) $value['ship_tax_rate'] / 100), 2);
                        $shipping_reverce = $value['shipping'];
                        $shipping_cod_failed = $value['cod_failed_shipping_charge'];
                        
                        $reversal_shipping = (float)$value['reversal_shipping'];
                        $reversal_shipping_tax = round(($reversal_shipping * (float) $value['ship_tax_rate'] / 100), 2);
                    }
                    $tot_val = 0;
                    if ($value['is_cod_failed']) {
                        if ((float) $tax_rate === (float) $value['ship_tax_rate']) {
                            $tot_val = (float) $taxwise_breakup['product_total'] + 
                                       (float) $shipping + $shipping_tax + 
                                               $reversal_shipping + $reversal_shipping_tax;
                            $taxwise_breakup['tax'] = $taxwise_breakup['tax'] + $shipping_tax + $reversal_shipping_tax;
                        } else {
                            $tot_val = (float) $taxwise_breakup['product_total'] + (float) $shipping + $reversal_shipping;
                        }
                    } else {
                        if ((float) $tax_rate === (float) $value['ship_tax_rate']) {
                            $tot_val = (float) $taxwise_breakup['product_total'] - 
                                       (float) $shipping - $shipping_tax + 
                                               $reversal_shipping + $reversal_shipping_tax;
                            $taxwise_breakup['tax'] = $taxwise_breakup['tax'] - $shipping_tax + $reversal_shipping_tax;
                        } else {
                            $tot_val = (float) $taxwise_breakup['product_total'] - (float) $shipping + $reversal_shipping;
                        }
                    }

                    $products_total = (float) $taxwise_breakup['product_total'];

                    $taxwise_breakup['cgst_tax'] = (!$value['is_igst']) ? $taxwise_breakup['tax'] / 2 : '';
                    $taxwise_breakup['sgst_tax'] = (!$value['is_igst']) ? $taxwise_breakup['tax'] / 2 : '';
                    $taxwise_breakup['igst_tax'] = ($value['is_igst']) ? $taxwise_breakup['tax'] : '';
                    $value['cod_failed_penalty'] = ($i == 0) ? $value['cod_failed_penalty'] : 0;

                    $data = array(
                        $value['order_no'],
                        $value['suborder_id'],
                        $value['order_date'],
                        $value['sale_invoice_no'],
                        $value['credit_note_date'],
                        $value['credit_note_no'],
                        $value['wsb_state'],
                        $value['wsb_tin'],
                        $ledger_name,
                        $value['payment_city'],
                        $value['payment_zone'],
                        $tax_rate,
                        (ROUND((($taxwise_breakup['subtotal'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($taxwise_breakup['discount'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($shipping_reverce * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($shipping_cod_failed * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($reversal_shipping * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (!empty($taxwise_breakup['cgst_tax']) ? (ROUND((($taxwise_breakup['cgst_tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)) : ''),
                        (!empty($taxwise_breakup['sgst_tax']) ? (ROUND((($taxwise_breakup['sgst_tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)) : ''),
                        (!empty($taxwise_breakup['igst_tax']) ? (ROUND((($taxwise_breakup['igst_tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)) : ''),
                        (ROUND((($taxwise_breakup['tax'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($tot_val * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        (ROUND((($value['cod_failed_penalty'] * $value['currency_value']) / $value['live_currency_conversion_rate']), 2)),
                        $tin_no,
                        $value['sale_invoice_date'],
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

        $data = array(
                    'Order No',
                    'Sale Invoice No',
                    'CN No',
                    'CN Date',
                    'WSB Sales State',
                    'WSB Tin No',
                    'Customer Id',
                    'Customer Name',
                    'Business Name',
                    'Location',
                    '',
                    'Total Product Value',
                    'Dicount',
                    'Total Tax',
                    'Shipping',
                    'Total sales',
                    'Payment Method'
                );
        fputcsv($fp, $data);

        foreach ($details_grouping as $key => $value) {
            $data = array($value['order_no'],
                $value['sale_invoice_no'],
                $value['credit_note_no'],
                $value['credit_note_date'],
                $value['wsb_state'],
                $value['wsb_tin'],
                $value['customer_id'],
                $value['customer_name'],
                $value['buiness_name'],
                $value['payment_city'],
                '',
                $value['total_product'],
                $value['total_discount'],
                $value['total_tax'],
                $value['shipping'],
                $value['total_sales'],
                $value['payment_code']
                );
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
        $this->load->model('accounts/salereturnreports');
        foreach ($results as $key => $result) {
            $order_id = $result['order_id'];
            $suborder_id = $result['suborder_id'];
            $credit_note_id = $result['credit_note_id'];
            $buyer_invoice = new BuyerInvoice($this);
            $return_product_info = $this->model_accounts_salereturnreports->getReturnsFromCreditNoteId($result['credit_note_id']);

            if (empty($return_product_info)) {
                continue;
            }
            $total_breakups = array();
            $buyer_invoice->setOptions('product_info', $return_product_info);
            if (empty($return_product_info)) {
                if (!empty($result['custom_credit_note_meta'])) {
                    $total_breakups = unserialize($result['custom_credit_note_meta']);
                }
            } else {
                $opids = array_column($return_product_info, 'order_product_id');
                $return_product_info = array_combine(
                        $opids, array_column($return_product_info, 'quantity')
                );

                $fields = array('order_product_id', 'price_per_piece', 'discount_per_piece', 'output_tax_rates');
                $buyer_invoice->setOptions('for_cn', true);
                $products = $buyer_invoice->getProductArrayByOrderProductIds($opids, $fields);
                array_walk($products, function( &$product, $key ) use($return_product_info) {
                    $product['quantity'] = $return_product_info[$product['order_product_id']];
                    $product['piece_in_set'] = 1;
                });
                $buyer_invoice->setOptions('products', $products);

                if (!empty($this->request->get['tally'])) {
                    $buyer_invoice->setOptions('skip_cst', false);
                }
                $total_breakups = $buyer_invoice->getTotalsBreakups($order_id, $suborder_id);
            }
            $total_breakups['shipping'] = $result['shipping_charge'];
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
            $key = $order_id . ';' . $suborder_id . ';' . $credit_note_id;
            if (!isset($details_grouping[$key])) {
                $i = 0;
                $details_grouping[$key] = array();
                $details_grouping[$key]['order_no']         = $result['order_no'];
                $details_grouping[$key]['suborder_id']      = $result['suborder_id'];
                $details_grouping[$key]['credit_note_no']   = $result['credit_note_no'];
                $details_grouping[$key]['credit_note_date'] = $result['credit_note_date'];
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

                $details_grouping[$key]['buiness_name']   = $result['payment_company'];
                $details_grouping[$key]['payment_city']   = $result['payment_city'];
                $details_grouping[$key]['payment_zone']   = $result['payment_zone'];
                $details_grouping[$key]['payment_code']   = $result['payment_code'];
                $details_grouping[$key]['customer_name']  = $result['customer_name'];
                $details_grouping[$key]['customer_id']    = $result['customer_id'];
                $details_grouping[$key]['code_version']   = $result['code_version'];
                $details_grouping[$key]['currency_value'] = $result['currency_value'];
                $details_grouping[$key]['live_currency_conversion_rate'] = $result['live_currency_conversion_rate'];
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

    private function _getGSTData($results) {
        $details_grouping = array();
        $this->load->model('accounts/salereturnreports');

        $shipping_tax_rate = 0;
        foreach ($results as $key => $result) {

            $order_id = $result['order_id'];
            $suborder_id = $result['suborder_id'];
            $credit_note_id = $result['credit_note_id'];
            $buyer_invoice = new BuyerInvoice($this);
            $return_product_info = $this->model_accounts_salereturnreports->getReturnsFromCreditNoteId($result['credit_note_id']);

            if (empty($return_product_info)) {
                continue;
            }
            $total_breakups = array();
            $buyer_invoice->setOptions('product_info', $return_product_info);
            if (empty($return_product_info)) {
                if (!empty($result['custom_credit_note_meta'])) {
                    $total_breakups = unserialize($result['custom_credit_note_meta']);
                }
            } else {
                $opids = array_column($return_product_info, 'order_product_id');
                $return_product_info_new = array();
                foreach ($return_product_info as $pro_info) {
                    if(!empty($return_product_info_new[$pro_info['order_product_id']])) {
                        $return_product_info_new[$pro_info['order_product_id']] += $pro_info['quantity'];
                    } else {
                        $return_product_info_new[$pro_info['order_product_id']] = $pro_info['quantity'];
                    }
                }
                $return_product_info = $return_product_info_new;
                $fields = array('order_product_id', 'price_per_piece', 'discount_per_piece', 'output_tax_rates');
                $buyer_invoice->setOptions('for_cn', true);
                $products = $buyer_invoice->getProductArrayByOrderProductIds($opids, $fields);
                array_walk($products, function( &$product, $key ) use($return_product_info) {
                    $product['quantity'] = $return_product_info[$product['order_product_id']];
                    $product['piece_in_set'] = 1;
                });
                $buyer_invoice->setOptions('products', $products);

                if (!empty($this->request->get['tally'])) {
                    $buyer_invoice->setOptions('skip_cst', false);
                }
                $total_breakups = $buyer_invoice->getTotalsBreakups($order_id, $suborder_id);
                $shipping_tax_rate = max(array_keys($total_breakups['tax_wise_breakup']));
            }

            $shipping_cod_non_cod = 0;
            if ($result['is_cod_failed']) {
                $total_breakups['shipping'] = 0;
                $total_breakups['cod_failed_shipping_charge'] = $result['cod_failed_shipping_charge'];
                $shipping_cod_non_cod = $result['cod_failed_shipping_charge'];
            } else {
                $total_breakups['shipping'] = $result['shipping_charge'];
                $total_breakups['cod_failed_shipping_charge'] = 0;
                $shipping_cod_non_cod = $result['shipping_charge'];
            }

            $key = $order_id . ';' . $suborder_id . ';' . $credit_note_id;
            if (!isset($details_grouping[$key])) {
                $is_igst = 1;
                $i = 0;
                $details_grouping[$key] = array();
                $details_grouping[$key]['order_no'] = $result['order_no'];
                $details_grouping[$key]['suborder_id'] = $result['suborder_id'];
                $details_grouping[$key]['credit_note_no'] = $result['credit_note_no'];
                $details_grouping[$key]['credit_note_date'] = $result['credit_note_date'];
                $details_grouping[$key]['cod_failed_penalty'] = $result['cod_failed_penalty'];
                $tin_no = !empty($result['gst_number']) ? $result['gst_number'] : 'N/A';
                $details_grouping[$key]['customer_tin_no'] = $tin_no;
                $details_grouping[$key]['order_date'] = date('d/m/Y', strtotime($result['order_date']));

                $details_grouping[$key]['sale_invoice_date'] = 'n/a';
                $details_grouping[$key]['sale_invoice_no'] = 'n/a';
                if ($result['invoice_no']) {
                    $details_grouping[$key]['sale_invoice_date'] = date('d/m/Y', strtotime($result['invoice_date']));
                    $details_grouping[$key]['sale_invoice_no'] = $result['invoice_no'];
                }

                $details_grouping[$key]['buiness_name']   = $result['payment_company'];
                $details_grouping[$key]['payment_city']   = $result['payment_city'];
                $details_grouping[$key]['payment_zone']   = $result['payment_zone'];
                $details_grouping[$key]['payment_code']   = $result['payment_code'];
                $details_grouping[$key]['customer_name']  = $result['customer_name'];
                $details_grouping[$key]['customer_id']    = $result['customer_id'];
                $details_grouping[$key]['code_version']   = $result['code_version'];
                $details_grouping[$key]['currency_value'] = $result['currency_value'];
                $details_grouping[$key]['live_currency_conversion_rate'] = $result['live_currency_conversion_rate'];
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

                if (!empty($seller_state_code)) {
                    $details_grouping[$key]['payment_zone'] = $seller_state_code . '-' . $result['payment_zone'];
                }

                if (!empty($wsb_state_code) && !empty($seller_state_code)) {
                    $is_igst = ((int) $wsb_state_code === (int) $seller_state_code) ? 0 : 1;
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
                    $details_grouping[$key]['cod_failed_shipping_charge'] = $total_breakups['cod_failed_shipping_charge'];
                }
                if (!empty($total_breakups['cst'])) {
                    $details_grouping[$key]['cst'] = $total_breakups['cst'];
                }
                if (!empty($total_breakups['refund_c_form'])) {
                    $details_grouping[$key]['refund_c_form'] = $total_breakups['refund_c_form'];
                }
                $total += (float) $shipping_cod_non_cod;

                $details_grouping[$key]['total_sales'] = $total;

                if (!empty($total_breakups['tax_wise_breakup'])) {
                    $details_grouping[$key]['tax_wise_breakup'] = $total_breakups['tax_wise_breakup'];
                }
                $details_grouping[$key]['is_igst'] = $is_igst;
                $details_grouping[$key]['ship_tax_rate'] = $shipping_tax_rate;
                $details_grouping[$key]['is_cod_failed'] = $result['is_cod_failed'];
                
                // Reversal of Shipping Charge
				$details_grouping[$key]['reversal_shipping'] = $result['reversal_shipping'];
            }
            $i++;
        }
        return $details_grouping;
    }

}
