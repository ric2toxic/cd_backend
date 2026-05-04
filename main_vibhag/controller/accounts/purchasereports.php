<?php

class ControllerAccountsPurchasereports extends Controller {

    public function index() {

        $data = array();
        $this->load->autoLoadLanguage('accounts/purchasereports', $data);

        $this->load->model('accounts/purchasereports');

        $this->document->setTitle($data['heading_purchase_reports']);

        // Determining whether its Tally reports or not.
        $is_tally = false;
        if (!empty($this->request->get['tally'])) {
            $is_tally = true;
        }

        $url = '';
        $filter_data = array();
        $data['is_tally'] = $is_tally;
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


        $data['filter_wsb_inv_date_from'] = '';
        if (!empty($this->request->get['filter_wsb_inv_date_from'])) {
            $filter_wsb_inv_date_from = $this->request->get['filter_wsb_inv_date_from'];
            $filter_data['filter_wsb_inv_date_from'] = $filter_wsb_inv_date_from;
            $data['filter_wsb_inv_date_from'] = $filter_wsb_inv_date_from;
        }

        $data['filter_wsb_inv_date_to'] = '';
        if (!empty($this->request->get['filter_wsb_inv_date_to'])) {
            $filter_wsb_inv_date_to = $this->request->get['filter_wsb_inv_date_to'];
            $filter_data['filter_wsb_inv_date_to'] = $filter_wsb_inv_date_to;
            $data['filter_wsb_inv_date_to'] = $filter_wsb_inv_date_to;
        }


        $data['filter_seller_inv_date_from'] = '';
        if (!empty($this->request->get['filter_seller_inv_date_from'])) {
            $filter_seller_inv_date_from = $this->request->get['filter_seller_inv_date_from'];
            $filter_data['filter_seller_inv_date_from'] = $filter_seller_inv_date_from;
            $data['filter_seller_inv_date_from'] = $filter_seller_inv_date_from;
        }

        $data['filter_seller_inv_date_to'] = '';
        if (!empty($this->request->get['filter_seller_inv_date_to'])) {
            $filter_seller_inv_date_to = $this->request->get['filter_seller_inv_date_to'];
            $filter_data['filter_seller_inv_date_to'] = $filter_seller_inv_date_to;
            $data['filter_seller_inv_date_to'] = $filter_seller_inv_date_to;
        }
        $data['filter_supplier_id'] = '';
        if (!empty($this->request->get['filter_supplier_id'])) {
            $filter_supplier_id = $this->request->get['filter_supplier_id'];
            $filter_data['filter_supplier_id'] = $filter_supplier_id;
            $data['filter_supplier_id'] = $filter_supplier_id;
        }

        $data['error'] = false;
        if (empty($filter_data)) {
            $data['error'] = true;
        } else {

            $results = $this->model_accounts_purchasereports->getPurchaseDetails($filter_data);
            $results_for_gst = $this->model_accounts_purchasereports->getPurchaseDetails($filter_data, 1);

            $seller_details = $this->model_accounts_purchasereports->getSellerDetails();
            if ($is_tally) {
                $file_name1 = '';
                $file_name2 = '';
                $file_name_array = array();
                $x = 0;
                if (!empty($results)) {
                    $file_name1 = $this->_generateTallyPurchaseCsv($results, $seller_details);
                }
                if (!empty($results_for_gst)) {
                    $file_name2 = $this->_generateTallyPurchaseGST($results_for_gst, $seller_details);
                }

                $zip_file_name = 'accounts-tally-purchase-reports';
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
                $this->_generatePurchaseCsv($results, $seller_details);
            }
        }


        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_purchase_reports'],
            'href' => $this->url->link('accounts/purchasereports', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );


        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('accounts/purchasereports.tpl', $data));
    }

    private function _getCsvData($results, $seller_details) {
        $details_grouping = array();

        $i = 0;
        foreach ($results as $result) {
            $order_id = (int)$result['order_id'];
            $suborder_id = $result['suborder_id'];
            $seller_id = (int)$result['seller_id'];
            $seller_invoice_id = (int)$result['seller_invoice_id'];
            $purchase_id = (int)$result['purchase_id'];
            $key = $order_id . ';' . $suborder_id . ';' . $seller_id . ';' . $seller_invoice_id. ';' . $purchase_id;

            if (!isset($details_grouping[$key])) {
                $i = 0;
                $details_grouping[$key] = array();
                $details_grouping[$key]['order_no'] = $result['order_no'];
                $details_grouping[$key]['order_date'] = date('d/m/Y', strtotime($result['order_date']));

                $details_grouping[$key]['seller_invoice_date'] = 'n/a';
                $details_grouping[$key]['seller_invoice_no'] = 'n/a';
                if ($result['seller_invoice_no']) {
                    $details_grouping[$key]['seller_invoice_date'] = date('d/m/Y', strtotime($result['seller_invoice_date']));
                    $details_grouping[$key]['seller_invoice_no'] = $result['seller_invoice_prefix'] . $result['seller_invoice_no'];
                }

                $wsb_tin = '';
                $purchase_firm_state = '';
                $seller_state = '';
                if (!empty($result['seller_invoice_meta'])) {
                    $seller_invoice_meta = unserialize($result['seller_invoice_meta']);
                    if (!empty($seller_invoice_meta['buyer_data']['tin'])) {
                        $wsb_tin = $seller_invoice_meta['buyer_data']['tin'];
                    } elseif (!empty($seller_invoice_meta['tin'])) {
                        $wsb_tin = $seller_invoice_meta['tin'];
                    }
                    if (!empty($seller_invoice_meta['buyer_data']['state'])) {
                        $purchase_firm_state = $seller_invoice_meta['buyer_data']['state'];
                    } elseif (!empty($seller_invoice_meta['state'])) {
                        $purchase_firm_state = $seller_invoice_meta['state'];
                    }
                    if (!empty($seller_invoice_meta['seller_data']['state'])) {
                        $seller_state = $seller_invoice_meta['seller_data']['state'];
                    }
                }
                if (!empty($result['seller_invoice_meta_1'])) {
                    $seller_invoice_meta_1 = unserialize($result['seller_invoice_meta_1']);
                    if (!empty($seller_invoice_meta_1['state'])) {
                        $seller_state = $seller_invoice_meta_1['state'];
                    }
                }



                $details_grouping[$key]['seller_company'] = $seller_details[$result['seller_id']]['company'];
                $details_grouping[$key]['seller_nickname'] = $seller_details[$result['seller_id']]['nickname'];
                $details_grouping[$key]['seller_city'] = $seller_details[$result['seller_id']]['city'];
                $details_grouping[$key]['seller_state'] = $seller_state;
                $details_grouping[$key]['seller_tin'] = $seller_details[$result['seller_id']]['tin'];


                $details_grouping[$key]['wsb_tin'] = $wsb_tin;
                $details_grouping[$key]['purchase_firm_state'] = $purchase_firm_state;
                $details_grouping[$key]['total_product'] = 0;
                $details_grouping[$key]['total_tax'] = 0;
                $details_grouping[$key]['total_purchase'] = 0;
                $details_grouping[$key]['taxwise_breakup'] = array();
            }else {
                $i = (int) max(array_keys($details_grouping[$key]['taxwise_breakup'])) + 1;
            }

            $purchase = round($result['purchase_taxratewise'], 2);
            $product = round($result['purchase_taxratewise'] /
                    (1 + ((float) $result['seller_input_tax'] / 100)), 2);
            $tax = $purchase - $product;

            $details_grouping[$key]['total_purchase'] += $purchase;
            $details_grouping[$key]['total_product'] += $product;
            $details_grouping[$key]['total_tax'] += $tax;

            $details_grouping[$key]['taxwise_breakup'][$i]['product'] = $product;
            $tax_rate = '0';
            if ((float) $result['seller_input_tax']) {
                $tax_rate = $result['seller_cst'] ? 'CST (' : 'VAT (';
                $tax_rate .= (float) $result['seller_input_tax'] . '% )';
            }
            $details_grouping[$key]['taxwise_breakup'][$i]['tax_rate'] = $tax_rate;
            $details_grouping[$key]['taxwise_breakup'][$i]['tax'] = $tax;
            $details_grouping[$key]['taxwise_breakup'][$i]['purchase'] = $purchase;

            $i++;
        }
        return $details_grouping;
    }

    private function _getGSTData($results, $seller_details) {
        $details_grouping = array();

        $i = 0;

        foreach ($results as $result) {

            $order_id = $result['order_id'];
            $suborder_id = $result['suborder_id'];
            $seller_id = $result['seller_id'];
            $purchase_id = $result['purchase_id'];
            $seller_invoice_id = $result['seller_invoice_id'];
            $key = $order_id . ';' . $suborder_id . ';' . $purchase_id . ';' . $seller_id . ';' . $seller_invoice_id;

            if (!isset($details_grouping[$key])) {
                $is_igst = 1;
                $i = 0;
                $details_grouping[$key] = array();
                $details_grouping[$key]['order_no'] = $result['order_no'];
                $details_grouping[$key]['order_date'] = date('d/m/Y', strtotime($result['order_date']));
                $details_grouping[$key]['seller_company'] = '';
                $details_grouping[$key]['seller_nickname'] = '';
                $details_grouping[$key]['seller_city'] = '';
                $details_grouping[$key]['seller_state'] = '';
                $details_grouping[$key]['seller_tin'] = '';



                $wsb_tin = '';
                $purchase_firm_state = '';
                $seller_state = '';
                $wsb_state_code = '';
                $seller_state_code = '';

                if (empty($result['seller_invoice_meta']) && !empty($suborder_id) && !empty($order_id)) {
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


                if (!empty($result['seller_invoice_meta'])) {
                    $seller_invoice_meta = unserialize($result['seller_invoice_meta']);
                    if (!empty($seller_invoice_meta['buyer_data']['tin'])) {
                        $wsb_tin = $seller_invoice_meta['buyer_data']['tin'];
                    } elseif (!empty($seller_invoice_meta['tin'])) {
                        $wsb_tin = $seller_invoice_meta['tin'];
                    }
                    if (!empty($seller_invoice_meta['buyer_data']['state'])) {
                        $purchase_firm_state = $seller_invoice_meta['buyer_data']['state'];
                    } elseif (!empty($seller_invoice_meta['state'])) {
                        $purchase_firm_state = $seller_invoice_meta['state'];
                    }
                    if (!empty($seller_invoice_meta['seller_data']['state'])) {
                        $seller_state = $seller_invoice_meta['seller_data']['state'];
                    }
                    if (!empty($seller_invoice_meta['seller_data']['state_code'][0]['gst_state_code'])) {
                        $seller_state_code = $seller_invoice_meta['seller_data']['state_code'][0]['gst_state_code'];
                    }
                    if (!empty($seller_invoice_meta['buyer_data']['state_code'][0]['gst_state_code'])) {
                        $wsb_state_code = $seller_invoice_meta['buyer_data']['state_code'][0]['gst_state_code'];
                    } elseif (!empty($seller_invoice_meta['state_code'][0]['gst_state_code'])) {
                        $wsb_state_code = $seller_invoice_meta['state_code'][0]['gst_state_code'];
                    }

                    if (!empty($seller_invoice_meta['seller_data'])) {
                        $details_grouping[$key]['seller_company'] = $seller_invoice_meta['seller_data']['company'];
                        $details_grouping[$key]['seller_nickname'] = $seller_invoice_meta['seller_data']['nickname'];
                        $details_grouping[$key]['seller_city'] = $seller_invoice_meta['seller_data']['city'];
                        $details_grouping[$key]['seller_state'] = $seller_invoice_meta['seller_data']['state'];
                        $details_grouping[$key]['seller_tin'] = $seller_invoice_meta['seller_data']['tin'];
                    }
                }



                if (!empty($result['seller_invoice_meta_1'])) {
                    $seller_invoice_meta_1 = unserialize($result['seller_invoice_meta_1']);
                    if (!empty($seller_invoice_meta_1['state'])) {
                        $seller_state = $seller_invoice_meta_1['state'];
                    }
                    if (!empty($seller_invoice_meta_1['state_code'][0]['gst_state_code'])) {
                        $seller_state_code = $seller_invoice_meta_1['state_code'][0]['gst_state_code'];
                    }

                    if (!empty($seller_invoice_meta_1)) {
                        $details_grouping[$key]['seller_company'] = $seller_invoice_meta_1['company'];
                        $details_grouping[$key]['seller_nickname'] = $seller_invoice_meta_1['nickname'];
                        $details_grouping[$key]['seller_city'] = $seller_invoice_meta_1['city'];
                        $details_grouping[$key]['seller_state'] = $seller_invoice_meta_1['state'];
                        $details_grouping[$key]['seller_tin'] = $seller_invoice_meta_1['tin'];
                    }
                } else if (!empty($result['stock_trans_details'])) {
                    $stock_tran_details = explode('~=', $result['stock_trans_details']);

                    $purchase_firm_state = !empty($stock_tran_details[2]) ? $stock_tran_details[2] : '';
                    $wsb_tin = !empty($stock_tran_details[3]) ? $stock_tran_details[3] : '';
                    $wsb_state_code = !empty($stock_tran_details[4]) ? $stock_tran_details[4] : '';
                }
                if (!empty($wsb_state_code) && !empty($seller_state_code)) {
                    $is_igst = ((int) $wsb_state_code === (int) $seller_state_code) ? 0 : 1;
                }

                $details_grouping[$key]['wsb_tin'] = $wsb_tin;
                $details_grouping[$key]['purchase_firm_state'] = $purchase_firm_state;
                $details_grouping[$key]['total_product'] = 0;
                $details_grouping[$key]['total_tax'] = 0;
                $details_grouping[$key]['total_purchase'] = 0;
                $details_grouping[$key]['taxwise_breakup'] = array();
                $details_grouping[$key]['taxwise_breakup'][$i]['seller_invoice_date'] = 'n/a';
                $details_grouping[$key]['taxwise_breakup'][$i]['seller_invoice_no'] = 'n/a';
            } else {
                $i = (int) max(array_keys($details_grouping[$key]['taxwise_breakup'])) + 1;
            }


            if ($result['seller_invoice_no']) {
                $details_grouping[$key]['taxwise_breakup'][$i]['seller_invoice_date'] = date('d/m/Y', strtotime($result['seller_invoice_date']));
                $details_grouping[$key]['taxwise_breakup'][$i]['seller_invoice_no'] = $result['seller_invoice_prefix'] . $result['seller_invoice_no'];
            }
            
            if(!empty($seller_state) && !empty($seller_state_code)) {
                $details_grouping[$key]['seller_state'] = $seller_state_code . '-' . $seller_state;
            }
            
            
            $purchase = round($result['purchase_taxratewise'], 2);
            $product = round($result['purchase_taxratewise'] /
                    (1 + ((float) $result['seller_input_tax'] / 100)), 2);
            $tax = $purchase - $product;

            $details_grouping[$key]['total_purchase'] += $purchase;
            $details_grouping[$key]['total_product'] += $product;
            $details_grouping[$key]['total_tax'] += $tax;

            $details_grouping[$key]['taxwise_breakup'][$i]['product'] = $product;
            $tax_rate = '0';
            if ((float) $result['seller_input_tax']) {
                $tax_rate = (float) $result['seller_input_tax'];
            }
            $details_grouping[$key]['taxwise_breakup'][$i]['tax_rate'] = $tax_rate;
            $details_grouping[$key]['taxwise_breakup'][$i]['tax'] = $tax;
            $details_grouping[$key]['taxwise_breakup'][$i]['igst_tax'] = $is_igst ? $tax : "";
            $details_grouping[$key]['taxwise_breakup'][$i]['cgst_tax'] = $details_grouping[$key]['taxwise_breakup'][$i]['sgst_tax'] = $is_igst ? "" : $tax / 2;
            $details_grouping[$key]['taxwise_breakup'][$i]['is_igst'] = $is_igst;
            $details_grouping[$key]['taxwise_breakup'][$i]['purchase'] = $purchase;
            $details_grouping[$key]['seller_nickname'] = $seller_details[$result['seller_id']]['nickname'];
            $details_grouping[$key]['seller_company'] = $seller_details[$result['seller_id']]['company'];
            $i++;
        }
        return $details_grouping;
    }

    private function _generatePurchaseCsv($results, $seller_details) {

        $file_name = DIR_DOWNLOAD . 'accounts-purchase-reports.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
            'Order Date',
            'Purchase Invoice Date',
            'Purchase Invoice No',
            'Seller Name',
            'Seller Code',
            'Seller Location',
            'WSB Tin No',
            'Total Product Value',
            'Total Tax',
            'Total Purchase');
        fputcsv($fp, $data);

        $details_grouping = $this->_getCsvData($results, $seller_details);

        foreach ($details_grouping as $key => $value) {
            $data = array($value['order_no'],
                $value['order_date'],
                $value['seller_invoice_date'],
                $value['seller_invoice_no'],
                $value['seller_company'],
                $value['seller_nickname'],
                $value['seller_city'],
                $value['wsb_tin'],
                $value['total_product'],
                $value['total_tax'],
                $value['total_purchase'],
                '');
            foreach ($value['taxwise_breakup'] as $key => $taxwise_breakup) {
                $breakup = array('',
                    $taxwise_breakup['product'],
                    $taxwise_breakup['tax_rate'],
                    $taxwise_breakup['tax'],
                    $taxwise_breakup['purchase']
                );
                $data = array_merge($data, $breakup);
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

    private function _generateTallyPurchaseCsv($results, $seller_details) {

        $file_name = DIR_DOWNLOAD . 'tally-accounts-purchase-reports-cst.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
            'Order Date',
            'Purchase Invoice Date',
            'Purchase Invoice No',
            'WSB Purchase State',
            'WSB Tin No',
            'Supplier ID - Business Name',
            'Supplier Location',
            'Supplier State',
            '',
            'Total Product Value',
            'Total Tax',
            'Total Purchase',
            'Supplier Tin'
        );
        fputcsv($fp, $data);

        $details_grouping = $this->_getCsvData($results, $seller_details);
        foreach ($details_grouping as $key => $value) {
            /* $data = array($value['order_no'],
              $value['order_date'],
              $value['seller_invoice_date'],
              $value['seller_invoice_no'],
              $value['purchase_firm_state'],
              $value['wsb_tin'],
              $value['seller_nickname'] . ' - '. $value['seller_company'],
              $value['seller_city'],
              $value['seller_state'],
              '',
              $value['total_product'],
              $value['total_tax'],
              $value['total_purchase']);
              fputcsv($fp, $data); */
            foreach ($value['taxwise_breakup'] as $key => $taxwise_breakup) {
                $breakup = array($value['order_no'],
                    $value['order_date'],
                    $value['seller_invoice_date'],
                    $value['seller_invoice_no'],
                    $value['purchase_firm_state'],
                    $value['wsb_tin'],
                    $value['seller_nickname'] . ' - ' . $value['seller_company'],
                    $value['seller_city'],
                    $value['seller_state'],
                    $taxwise_breakup['tax_rate'],
                    $taxwise_breakup['product'],
                    $taxwise_breakup['tax'],
                    $taxwise_breakup['purchase'],
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

    private function _generateTallyPurchaseGST($results, $seller_details) {
        $file_name = DIR_DOWNLOAD . 'tally-accounts-purchase-reports-gst.csv';
        $fp = fopen($file_name, 'w');

        $data = array('Order No',
            'Order Date',
            'Purchase Invoice Date',
            'Purchase Invoice No',
            'WSB Purchase State',
            'WSB GSTIN',
            'Supplier ID - Business Name',
            'Supplier Location',
            'Supplier State',
            'Total GST rate',
            'Total Product Value',
            'CGST',
            'SGST',
            'IGST',
            'Total Tax',
            'Total Purchase',
            'Supplier GSTIN'
        );
        fputcsv($fp, $data);

        $details_grouping = $this->_getGSTData($results, $seller_details);
        foreach ($details_grouping as $key => $value) {
            foreach ($value['taxwise_breakup'] as $key => $taxwise_breakup) {
                $breakup = array($value['order_no'],
                    $value['order_date'],
                    $taxwise_breakup['seller_invoice_date'],
                    $taxwise_breakup['seller_invoice_no'],
                    $value['purchase_firm_state'],
                    $value['wsb_tin'],
                    $value['seller_nickname'] . ' - ' . $value['seller_company'],
                    $value['seller_city'],
                    $value['seller_state'],
                    $taxwise_breakup['tax_rate'],
                    $taxwise_breakup['product'],
                    $taxwise_breakup['cgst_tax'],
                    $taxwise_breakup['sgst_tax'],
                    $taxwise_breakup['igst_tax'],
                    $taxwise_breakup['tax'],
                    $taxwise_breakup['purchase'],
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

}
