<?php

class ControllerAccountsAdvancevoucherreports extends Controller {

    public function index() {

        $this->load->model('accounts/advancevoucherreports');

        // Language AutoLoad
        $data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('accounts/advancevoucherreports', $data);

        $this->document->setTitle($data['heading_advancereports_title']);
        // Determining whether its Tally reports or not.
        $is_tally = false;
        if (!empty($this->request->get['tally'])) {
            $is_tally = $this->request->get['tally'];
        }

        $data['is_tally'] = $is_tally;

        $general_url = '';
        $filter_data = array();
        $data['filter_advance_voucher_date_from'] = '';
        $data['filter_advance_voucher_date_to'] = '';
        $data['filter_advance_voucher_check'] = '';

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
            $results = $this->model_accounts_advancevoucherreports->getAdvanceDetails($filter_data);

            if ($is_tally) {
                $this->_getCsvForTally($results);
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
            'text' => 'Advance Voucher',
            'href' => $this->url->link('accounts/advancevoucherreports', 'token=' . $this->session->data['token'] . $general_url, 'SSL')
        );

        $data['token'] = $this->session->data['token'];


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('accounts/advancevoucherreports.tpl', $data));
    }

    private function _getCsvForTally($results) {
        $this->load->model('accounts/salesreports');
        $details_grouping = $this->_getCsvData($results);
        $file_name = DIR_DOWNLOAD . 'accounts-advance-reports-tally.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
            'Suborder No',
            'Order Date',
            'Adv Date',
            'Adv Voucher No',
            'WSB Sales State',
            'WSB GSTIN',
            'Customer ID - Customer Name - Business Name',
            'Customer Location',
            'Customer State',
            'GST Rate',
            'Total Advance Value',
            'Shipping Charge',
            'Total Taxable Value',
            'CGST',
            'SGST',
            'IGST',
            'Total Tax',
            'Total Amount',
            'Customer GSTIN',
            'Payment Mode',
            'Merchant Txn Id'
        );
        fputcsv($fp, $data);
        foreach ($details_grouping as $order_id_suborder_id => $value) {
            $value['payment_gateway'] = ($value['payment_gateway'] == 'bank_transfer' || $value['payment_gateway']== 'cash') ? 'ICICI' : $value['payment_gateway'];
            $ledger_name = $this->model_accounts_salesreports->getCustomerLedger($value['customer_id']);
            if (isset($value['tax_wise_breakup'])) {
                $i = 0;
                $tin_no = $value['customer_tin_no'] == 'N/A' ? '' : $value['customer_tin_no'];
                foreach ($value['tax_wise_breakup'] as $key => $taxwise_breakup) {
                    $tax_rate = $key;
                    $shipping = 0;
                    $shipping_tax = 0;
                    $tot_val = 0;
                    if (!empty($taxwise_breakup['shipping'])) {
                        $shipping = $taxwise_breakup['shipping']['advance_exc_tax'];
                        $shipping_tax = $taxwise_breakup['shipping']['advance_tax_val'];
                        $tot_val = (float) $taxwise_breakup['advance_inc_tax'] + (float) $shipping + $shipping_tax;
                        $taxwise_breakup['advance_tax_val'] = $taxwise_breakup['advance_tax_val'] + $shipping_tax;
                    } else {
                        $tot_val = (float) $taxwise_breakup['advance_inc_tax'];
                        $taxwise_breakup['advance_tax_val'] = $taxwise_breakup['advance_tax_val'];
                    }

                    $taxwise_breakup['cgst_tax'] = (!$value['is_igst']) ? $taxwise_breakup['advance_tax_val'] / 2 : '';
                    $taxwise_breakup['sgst_tax'] = (!$value['is_igst']) ? $taxwise_breakup['advance_tax_val'] / 2 : '';
                    $taxwise_breakup['igst_tax'] = ($value['is_igst']) ? $taxwise_breakup['advance_tax_val'] : '';

                    $data = array(
                        $value['order_no'],
                        $value['suborder_id'],
                        $value['order_date'],
                        $value['advance_voucher_date'],
                        $value['advance_voucher_no'],
                        $value['wsb_state'],
                        $value['wsb_tin'],
                        $ledger_name,
                        $value['payment_city'],
                        $value['payment_zone'],
                        $tax_rate,
                        $taxwise_breakup['advance_exc_tax'],
                        $shipping,
                        ($taxwise_breakup['advance_exc_tax'] + $shipping),
                        $taxwise_breakup['cgst_tax'],
                        $taxwise_breakup['sgst_tax'],
                        $taxwise_breakup['igst_tax'],
                        $taxwise_breakup['advance_tax_val'],
                        $tot_val,
                        $tin_no,
                        $value['payment_gateway'],
                        $value['merchant_txn_id']
                    );
                    fputcsv($fp, $data);
                    $i++;
                }
            }
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

    private function _getCsv($results) {
        $details_grouping = $this->_getCsvData($results);
        $file_name = DIR_DOWNLOAD . 'accounts-sale-reports.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
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
            'Total sales');
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
        $this->load->model('accounts/advancevoucherreports');

        $shipping_tax_rate = 0;
        foreach ($results as $key => $result) {
            
            $order_id = $result['order_id'];
            $suborder_id = $result['suborder_id'];

            $advance_voucher_tax_wise = AdvanceVoucherLib::getTaxWiseAdvanceVoucherBreakup($this->db, $order_id, $suborder_id);
            
            $key = $order_id . ';' . $suborder_id;
            if (!isset($details_grouping[$key])) {
                $is_igst = 1;
                $i = 0;
                $details_grouping[$key] = array();
                $details_grouping[$key]['order_no'] = $result['order_no'];
                $details_grouping[$key]['suborder_id'] = $result['suborder_id'];
                $tin_no = $result['gst_number'] ?? 'N/A';
                $tin_no  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $tin_no);
                $tin_no  = trim($tin_no);
                $details_grouping[$key]['customer_tin_no'] = $tin_no;
                $details_grouping[$key]['order_date'] = date('d/m/Y', strtotime($result['order_date']));
                $details_grouping[$key]['advance_voucher_date'] = date('d/m/Y', strtotime($result['advance_voucher_date']));
                $details_grouping[$key]['advance_voucher_no'] = $result['advance_voucher_no'];
                $details_grouping[$key]['locked'] = $result['locked'];
                $details_grouping[$key]['payment_gateway'] = $result['payment_gateway'];

                $details_grouping[$key]['buiness_name'] = $result['payment_company'];
                $details_grouping[$key]['payment_city'] = $result['payment_city'];
                $details_grouping[$key]['payment_zone'] = $result['payment_zone'];
                $details_grouping[$key]['customer_name'] = $result['customer_name'];
                $details_grouping[$key]['customer_id'] = $result['customer_id'];
                $wsb_tin = '';
                $wsb_state = '';
                $wsb_state_code = '';
                $seller_state_code = '';
                $buyer_invoice = new BuyerInvoice($this);
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

                if(!empty($seller_state_code)) {
                    $details_grouping[$key]['payment_zone'] = $seller_state_code . '-' . $result['payment_zone'];
                }
                
                $details_grouping[$key]['wsb_tin'] = $wsb_tin;
                $details_grouping[$key]['wsb_state'] = $wsb_state;
                $details_grouping[$key]['is_igst'] = $is_igst;
                $details_grouping[$key]['tax_wise_breakup'] = $advance_voucher_tax_wise[$result['advance_voucher_id']];
                $details_grouping[$key]['merchant_txn_id'] = $result['merchant_txn_id'];
                
            }
            $i++;
        }
        return $details_grouping;
    }

}
