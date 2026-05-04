<?php

class ControllerAccountsPurchasereturnreports extends Controller {

    public function index() {

        $data = array();
        $this->load->autoLoadLanguage('accounts/purchasereturnreports', $data);

        $this->load->model('accounts/purchasereturnreports');

        $this->document->setTitle($data['heading_purchase_return_reports']);

        // Determining whether its Tally reports or not.
        $is_tally = false;
        if (!empty($this->request->get['tally'])) {
            $is_tally = true;
        }
        $data['is_tally'] = $is_tally;
        $url = '';
        $filter_data = array();

        $data['filter_order_date_from'] = '';
        if (!empty($this->request->get['filter_order_date_from'])) {
            $filter_order_date_from = $this->request->get['filter_order_date_from'];
            $filter_data['filter_order_date_from'] = $filter_order_date_from;
            $data['filter_order_date_from'] = $filter_order_date_from;
        }

        $data['filter_order_date_to'] = '';
        if (!empty($this->request->get['filter_order_date_to'])) {
            $filter_order_date_to = $this->request->get['filter_order_date_to'];
            $filter_data['filter_order_date_to'] = $filter_order_date_to;
            $data['filter_order_date_to'] = $filter_order_date_to;
        }


        $data['filter_debit_note_date_from'] = '';
        if (!empty($this->request->get['filter_debit_note_date_from'])) {
            $filter_debit_note_date_from = $this->request->get['filter_debit_note_date_from'];
            $filter_data['filter_debit_note_date_from'] = $filter_debit_note_date_from;
            $data['filter_debit_note_date_from'] = $filter_debit_note_date_from;
        }

        $data['filter_debit_note_date_to'] = '';
        if (!empty($this->request->get['filter_debit_note_date_to'])) {
            $filter_debit_note_date_to = $this->request->get['filter_debit_note_date_to'];
            $filter_data['filter_debit_note_date_to'] = $filter_debit_note_date_to;
            $data['filter_debit_note_date_to'] = $filter_debit_note_date_to;
        }

        $data['filter_supplier_id'] = '';
        if (!empty($this->request->get['filter_supplier_id'])) {
            $filter_supplier_id = $this->request->get['filter_supplier_id'];
            $filter_data['filter_supplier_id'] = $filter_supplier_id;
            $data['filter_supplier_id'] = $filter_supplier_id;
        }

        $data['filter_allow_wsb_order']        = 0;
        
        if (!empty($this->request->get['filter_allow_wsb_order'])) {
            $filter_allow_wsb_order = $this->request->get['filter_allow_wsb_order'];
            $filter_data['filter_allow_wsb_order'] = $filter_allow_wsb_order;
            $data['filter_allow_wsb_order'] = $filter_allow_wsb_order;
        }

        $data['error'] = false;
        if (empty($filter_data)) {
            $data['error'] = true;
        } else {
            $results = $this->model_accounts_purchasereturnreports->getPurchaseReturnDetails($filter_data);
            $results_for_gst = $this->model_accounts_purchasereturnreports->getPurchaseReturnDetails($filter_data, 1);
            $seller_details = $this->model_accounts_purchasereturnreports->getSellerDetails();

            if ($is_tally) {

                $file_name1 = '';
                $file_name2 = '';
                $file_name_array = array();
                $x = 0;
                if (!empty($results)) {
                    $file_name1 = $this->_saveCsvForTally($results, $seller_details);
                }
                if (!empty($results_for_gst)) {
                    $file_name2 = $this->_saveGSTForTally($results_for_gst, $seller_details);
                }
                $zip_file_name = 'accounts-tally-purchase-return-reports';
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
                $this->_saveCsv($results, $seller_details);
            }
        }


        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_purchase_return_reports'],
            'href' => $this->url->link('accounts/purchasereturnreports', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );


        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('accounts/purchasereturnreports.tpl', $data));
    }

    private function _saveCsv($results, $seller_details) {
        $file_name = DIR_DOWNLOAD . 'accounts-return-purchase-reports.csv';
        $fp = fopen($file_name, 'w');
        $details_grouping = $this->_getCsvData($results, $seller_details);
        $data = array('Order No',
            'Purchase Invoice Date',
            'Purchase Invoice No',
            'DN Number',
            'DN Date',
            'Seller Name',
            'Seller Code',
            'Seller Location',
            'WSB Tin No',
            'Total Product Value',
            'Total Tax',
            'Total Purchase');
        fputcsv($fp, $data);

        foreach ($details_grouping as $key => $value) {
            $data = array($value['order_no'],
                $value['seller_invoice_date'],
                $value['seller_invoice_no'],
                $value['debit_note_no'],
                $value['debit_note_date'],
                $value['seller_company'],
                $value['seller_nickname'],
                $value['seller_city'],
                $value['wsb_tin'],
                $value['total_product_return'],
                $value['total_tax_return'],
                $value['total_purchase_return'],
                '');
            $breakup = array('',
                $value['taxwise_breakup']['product_return'],
                $value['taxwise_breakup']['tax_rate'],
                $value['taxwise_breakup']['tax_return'],
                $value['taxwise_breakup']['purchase_return']
            );
            $data = array_merge($data, $breakup);


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

    private function _saveCsvForTally($results, $seller_details) {
        $file_name = DIR_DOWNLOAD . 'accounts-tally-return-purchase-reports-cst.csv';
        $fp = fopen($file_name, 'w');

        $details_grouping = $this->_getCsvData($results, $seller_details);
        $data = array('Order No',
            'Suborder No',
            'Purchase Invoice No',
            'DN Number',
            'DN Date',
            'WSB Purchase State',
            'WSB Tin No',
            'Supplier ID - Business Name',
            'Supplier Location',
            'Supplier State',
            '',
            'Total Product Value',
            'Total Tax',
            'Total Purchase',
            'Seller Tin'
        );
        fputcsv($fp, $data);

        foreach ($details_grouping as $key => $value) {

            foreach ($value['taxwise_breakup'] as $tax_key => $tax_value) {
                /* $data = array($value['order_no'],
                  $value['suborder_id'],
                  $value['seller_invoice_no'],
                  $value['debit_note_no'],
                  $value['debit_note_date'],

                  $value['purchase_firm_state'],
                  $value['wsb_tin'],
                  $value['seller_nickname'] . ' - '. $value['seller_company'],
                  $value['seller_city'],
                  $value['seller_state'],
                  '',
                  $value['total_product_return'],
                  $value['total_tax_return'],
                  $value['total_purchase_return']);
                  fputcsv($fp, $data); */
                $breakup = array($value['order_no'],
                    $value['suborder_id'],
                    $value['seller_invoice_no'],
                    $value['debit_note_no'],
                    $value['debit_note_date'],
                    $value['purchase_firm_state'],
                    $value['wsb_tin'],
                    $value['seller_nickname'] . ' - ' . $value['seller_company'],
                    $value['seller_city'],
                    $value['seller_state'],
                    $tax_value['tax_rate'],
                    $tax_value['product_return'],
                    $tax_value['tax_return'],
                    $tax_value['purchase_return'],
                    $value['seller_tin']
                );
                fputcsv($fp, $breakup);
            }
        }
        fclose($fp);
        return $file_name;
//        if (file_exists($file_name)) {
//            header('Content-Description: File Transfer');
//            header('Content-Type: application/octet-stream');
//            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($file_name));
//            readfile($file_name);
//            exit();
//        }
    }

    private function _saveGSTForTally($results, $seller_details) {
        $file_name = DIR_DOWNLOAD . 'accounts-tally-return-purchase-reports-gst.csv';
        $fp = fopen($file_name, 'w');

        $details_grouping = $this->_getGSTData($results, $seller_details);
        $data = array('Order No',
            'Suborder No',
            'Purchase Invoice No',
            'DN Invoice No',
            'DN Invoice Date',
            'WSB Purchase State',
            'WSB GSTIN',
            'Supplier ID - Business Name',
            'Supplier Location',
            'Supplier State',
            'Tax rate',
            'Total Product Value',
            'CGST',
            'SGST',
            'IGST',
            'Total Tax',
            'Total Purchase return',
            'Supplier GSTIN'
        );
        fputcsv($fp, $data);

        foreach ($details_grouping as $key => $value) {
            foreach ($value['taxwise_breakup'] as $tax_key => $tax_value) {
                $breakup = array($value['order_no'],
                    $value['suborder_id'],
                    $value['seller_invoice_no'],
                    $value['debit_note_no'],
                    $value['debit_note_date'],
                    $value['purchase_firm_state'],
                    $value['wsb_tin'],
                    $value['seller_nickname'] . ' - ' . $value['seller_company'],
                    $value['seller_city'],
                    $value['seller_state'],
                    $tax_value['tax_rate'],
                    $tax_value['product_return'],
                    $tax_value['cgst_tax'],
                    $tax_value['sgst_tax'],
                    $tax_value['igst_tax'],
                    $tax_value['tax_return'],
                    $tax_value['purchase_return'],
                    $value['seller_tin']
                );
                fputcsv($fp, $breakup);
            }
        }
        fclose($fp);
        return $file_name;
//        if (file_exists($file_name)) {
//            header('Content-Description: File Transfer');
//            header('Content-Type: application/octet-stream');
//            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($file_name));
//            readfile($file_name);
//            exit();
//        }
    }

    private function _getCsvData($results, $seller_details) {
        $details_grouping = array();
        $taxwise_breakup = array();
        $i = 0;

        foreach ($results as $result) {
            $key = $result['debit_note_id'];

            $details_grouping[$key] = array();
            $details_grouping[$key]['order_no'] = $result['order_no'];
            $details_grouping[$key]['suborder_id'] = $result['suborder_id'];
            $details_grouping[$key]['seller_invoice_date'] = 'not_available';
            $details_grouping[$key]['seller_invoice_no'] = 'not_available';
            if (!empty($result['seller_invoice_no'])) {
                $details_grouping[$key]['seller_invoice_date'] = date('d/m/Y', strtotime($result['seller_invoice_date']));
                $details_grouping[$key]['seller_invoice_no'] = $result['seller_invoice_prefix'] . $result['seller_invoice_no'];
            }

            $details_grouping[$key]['debit_note_date'] = date('d/m/Y', strtotime($result['debit_note_date']));
            $details_grouping[$key]['debit_note_no'] = $result['debit_note_prefix'] . $result['debit_note_no'];

            $wsb_tin = '';
            $purchase_firm_state = '';
            $seller_state = '';
            if (!empty($result['seller_invoice_meta'])) {
                $seller_invoice_meta = unserialize($result['seller_invoice_meta']);
                if (!empty($seller_invoice_meta['buyer_data']['tin'])) {
                    $wsb_tin = $seller_invoice_meta['buyer_data']['tin'];
                }
                if (!empty($seller_invoice_meta['buyer_data']['state'])) {
                    $purchase_firm_state = $seller_invoice_meta['buyer_data']['state'];
                }
                if (!empty($seller_invoice_meta['seller_data']['state'])) {
                    $seller_state = $seller_invoice_meta['seller_data']['state'];
                }
            }

            $details_grouping[$key]['seller_company'] = $seller_details[$result['seller_id']]['company'];
            $details_grouping[$key]['seller_nickname'] = $seller_details[$result['seller_id']]['nickname'];
            $details_grouping[$key]['seller_city'] = $seller_details[$result['seller_id']]['city'];
            $details_grouping[$key]['seller_state'] = $seller_state;
            $details_grouping[$key]['seller_tin'] = $seller_details[$result['seller_id']]['tin'];


            $details_grouping[$key]['wsb_tin'] = $wsb_tin;
            $details_grouping[$key]['purchase_firm_state'] = $purchase_firm_state;
            $details_grouping[$key]['total_product_return'] = 0;
            $details_grouping[$key]['total_tax_return'] = 0;
            $details_grouping[$key]['total_purchase_return'] = 0;
            $details_grouping[$key]['taxwise_breakup'] = array();



            $purchase_return = round($result['purchase_return_taxratewise'], 2);
            $product_return = round($result['purchase_return_taxratewise'] /
                    (1 + ((float) $result['seller_input_tax'] / 100)), 2);
            $tax_return = $purchase_return - $product_return;

            $details_grouping[$key]['total_purchase_return'] = $purchase_return;
            $details_grouping[$key]['total_product_return'] = $product_return;
            $details_grouping[$key]['total_tax_return'] = $tax_return;

            $tax_rate = '0';
            if ((float) $result['seller_input_tax']) {
                $tax_rate = $result['seller_cst'] ? 'CST (' : 'VAT (';
                $tax_rate .= (float) $result['seller_input_tax'] . '% )';
            }
            $taxwise_breakup[$key][$result['seller_input_tax']]['tax_rate'] = $tax_rate;
            $taxwise_breakup[$key][$result['seller_input_tax']]['product_return'] = $product_return;
            $taxwise_breakup[$key][$result['seller_input_tax']]['tax_return'] = $tax_return;
            $taxwise_breakup[$key][$result['seller_input_tax']]['purchase_return'] = $purchase_return;

            $details_grouping[$key]['taxwise_breakup'] = $taxwise_breakup[$key];
        }
        return $details_grouping;
    }

    private function _getGSTData($results, $seller_details) {
        $details_grouping = array();
        $taxwise_breakup = array();
        $is_igst = 1;
        foreach ($results as $result) {
            $key = $result['order_id'] . ';' . $result['suborder_id'] . ';' . $result['debit_note_id'];

            $details_grouping[$key] = array();
            $details_grouping[$key]['order_no'] = $result['order_no'];
            $details_grouping[$key]['suborder_id'] = $result['suborder_id'];
            $details_grouping[$key]['seller_invoice_date'] = 'not_available';
            $details_grouping[$key]['seller_invoice_no'] = 'not_available';
            if (!empty($result['seller_invoice_no'])) {
                $details_grouping[$key]['seller_invoice_date'] = date('d/m/Y', strtotime($result['seller_invoice_date']));
                $details_grouping[$key]['seller_invoice_no'] = $result['seller_invoice_prefix'] . $result['seller_invoice_no'];
            }

            $details_grouping[$key]['debit_note_date'] = date('d/m/Y', strtotime($result['debit_note_date']));
            $details_grouping[$key]['debit_note_no'] = $result['debit_note_prefix'] . $result['debit_note_no'];

            $wsb_tin = '';
            $purchase_firm_state = '';
            $seller_state = '';
            $wsb_state_code = '';
            $seller_state_code = '';
            $details_grouping[$key]['seller_company'] = '';
            $details_grouping[$key]['seller_nickname'] = '';
            $details_grouping[$key]['seller_city'] = '';
            $details_grouping[$key]['seller_state'] = '';
            $details_grouping[$key]['seller_tin'] = '';

            if (empty($result['seller_invoice_meta']) && empty($result['purchase_firm_meta'])) {
                // If meta not set getsellerinvoicemeta
                $seller_invoice = new SellerInvoice($this);
                $invoice_data = array(
                    'seller_id' => $seller_id,
                    'suborder_id' => $suborder_id,
                    'order_id' => $order_id,
                    'vat_input_rule_id' => $result['vat_input_rule_id']
                );
                $result['seller_invoice_meta'] = serialize($seller_invoice->getInvoiceMeta($invoice_data));
            }
            if (!empty($result['purchase_firm_meta'])) {
                $seller_invoice_meta = $result['seller_invoice_meta'];
                $result['seller_invoice_meta'] = array();
                $result['seller_invoice_meta']['seller_data'] = unserialize($seller_invoice_meta);
                $result['seller_invoice_meta']['buyer_data'] = unserialize($result['purchase_firm_meta']);
                $result['seller_invoice_meta'] = serialize($result['seller_invoice_meta']);
            }

            if (!empty($result['seller_invoice_meta'])) {
                $seller_invoice_meta = unserialize($result['seller_invoice_meta']);

                if ($result['stock_transfer'] == 1) {
                    $wsb_tin = $result['gst_number'];
                    $purchase_firm_state = $result['payment_zone'];
                    $wsb_state_code = $result['gst_state_code'];
                } else {
                    if (!empty($seller_invoice_meta['buyer_data']['tin'])) {
                        $wsb_tin = $seller_invoice_meta['buyer_data']['tin'];
                    }
                    if (!empty($seller_invoice_meta['buyer_data']['state'])) {
                        $purchase_firm_state = $seller_invoice_meta['buyer_data']['state'];
                    }
                    if (!empty($seller_invoice_meta['buyer_data']['state_code'][0]['gst_state_code'])) {
                        $wsb_state_code = $seller_invoice_meta['buyer_data']['state_code'][0]['gst_state_code'];
                    }
                }

                if (!empty($seller_invoice_meta['seller_data']['state_code'][0]['gst_state_code'])) {
                    $seller_state_code = $seller_invoice_meta['seller_data']['state_code'][0]['gst_state_code'];
                }
                if (!empty($seller_invoice_meta['seller_data']['state'])) {
                    $seller_state = $seller_invoice_meta['seller_data']['state'];
                }
                if (!empty($seller_invoice_meta['seller_data'])) {
                    $details_grouping[$key]['seller_company'] = $seller_invoice_meta['seller_data']['company'];
                    $details_grouping[$key]['seller_nickname'] = $seller_invoice_meta['seller_data']['nickname'];
                    $details_grouping[$key]['seller_city'] = $seller_invoice_meta['seller_data']['city'];
                    $details_grouping[$key]['seller_state'] = $seller_invoice_meta['seller_data']['state'];
                    $details_grouping[$key]['seller_tin'] = $seller_invoice_meta['seller_data']['tin'];
                }
            }
            if (!empty($wsb_state_code) && !empty($seller_state_code)) {
                $is_igst = ((int) $wsb_state_code === (int) $seller_state_code) ? 0 : 1;
            }

//            $details_grouping[$key]['seller_company'] = $seller_details[$result['seller_id']]['company'];
//            $details_grouping[$key]['seller_nickname'] = $seller_details[$result['seller_id']]['nickname'];
//            $details_grouping[$key]['seller_city'] = $seller_details[$result['seller_id']]['city'];
//            $details_grouping[$key]['seller_state'] = $seller_state;
//            $details_grouping[$key]['seller_tin'] = $seller_details[$result['seller_id']]['tin'];

            if (!empty($seller_state) && !empty($seller_state_code)) {
                $details_grouping[$key]['seller_state'] = $seller_state_code . '-' . $seller_state;
            }
            $details_grouping[$key]['seller_nickname'] = $seller_details[$result['seller_id']]['nickname'];
            $details_grouping[$key]['seller_company'] = $seller_details[$result['seller_id']]['company'];
            $details_grouping[$key]['wsb_tin'] = $wsb_tin;
            $details_grouping[$key]['purchase_firm_state'] = $purchase_firm_state;
            $details_grouping[$key]['total_product_return'] = 0;
            $details_grouping[$key]['total_tax_return'] = 0;
            $details_grouping[$key]['total_purchase_return'] = 0;
            $details_grouping[$key]['taxwise_breakup'] = array();


            $purchase_return = round($result['purchase_return_taxratewise'], 2);
            $product_return = round($result['purchase_return_taxratewise'] /
                    (1 + ((float) $result['seller_input_tax'] / 100)), 2);
            $tax_return = $purchase_return - $product_return;

            $details_grouping[$key]['total_purchase_return'] = $purchase_return;
            $details_grouping[$key]['total_product_return'] = $product_return;
            $details_grouping[$key]['total_tax_return'] = $tax_return;

            $tax_rate = '0';
            if ((float) $result['seller_input_tax']) {
                $tax_rate = (float) $result['seller_input_tax'];
            }
            $taxwise_breakup[$key][$result['seller_input_tax']]['tax_rate'] = $tax_rate;
            $taxwise_breakup[$key][$result['seller_input_tax']]['product_return'] = $product_return;
            $taxwise_breakup[$key][$result['seller_input_tax']]['tax_return'] = $tax_return;
            $taxwise_breakup[$key][$result['seller_input_tax']]['purchase_return'] = $purchase_return;
            $taxwise_breakup[$key][$result['seller_input_tax']]['igst_tax'] = $is_igst ? $tax_return : "";
            $taxwise_breakup[$key][$result['seller_input_tax']]['cgst_tax'] = $taxwise_breakup[$key][$result['seller_input_tax']]['sgst_tax'] = $is_igst ? "" : $tax_return / 2;

            $details_grouping[$key]['taxwise_breakup'] = $taxwise_breakup[$key];
        }
        return $details_grouping;
    }

}
