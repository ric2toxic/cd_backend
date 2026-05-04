<?php
class ControllerSellerPanelGstReports extends Controller {
  
  public function index() {

    if (!$this->customer->isLogged()) { 
        $this->response->redirect($this->url->link('account/login', '', 'SSL'));
    }

    $this->load->model('seller_panel/profile');

    $seller_id      = $this->customer->getId();
    $nickname       = $this->model_seller_panel_profile->getSellersInformation($seller_id,'nickname');
    $data           = array();
    $data['error']  = false;
    
    $this->load->autoLoadLanguage('accounts/gstreport', $data);
    //$this->load->model('accounts/gstreport');

    $this->document->setTitle($data['heading_payments_report']);
    
    $url = '';

    if (isset($this->request->get['page'])) {
      $page = $this->request->get['page'];
    } else {
      $page = 1;
    }
    $filter_data = array();
    $gst_report_filter_data = array();
    $gst_report_filter_data['filter_supplier_id'] = $nickname['nickname']; 

    $data['filter_order_no']            = '';
    $data['filter_seller_code']         = '';
    $data['filter_company_name']        = '';
    $data['filter_payment_done_date']   = '';
    $data['filter_reference_no']        = '';
    
    $data['filter_select_month']        = '';
    $data['filter_select_year']        = '';
    $data['filter_select_quarter']        = '';
    $data['filter_year_range']        = '';
    //pr($this->request->get);
    if ( !empty($this->request->get['filter_order_no']) ) {

      $data['filter_order_no']           = $this->request->get['filter_order_no'];
      $filter_data['filter_order_no']    = $this->request->get['filter_order_no'];
    }
    if ( !empty($this->request->get['filter_seller_code']) ) {

      $data['filter_seller_code']           = $this->request->get['filter_seller_code'];
      $filter_data['filter_seller_code']    = $this->request->get['filter_seller_code'];
    }
    if ( !empty($this->request->get['filter_company_name']) ) {

      $data['filter_company_name']           = $this->request->get['filter_company_name'];
      $filter_data['filter_company_name']    = $this->request->get['filter_company_name'];
    }
    if ( !empty($this->request->get['filter_payment_done_date']) ) {

      $data['filter_payment_done_date']           = $this->request->get['filter_payment_done_date'];
      $filter_data['filter_payment_done_date']    = $this->request->get['filter_payment_done_date'];
    }
    if ( !empty($this->request->get['filter_reference_no']) ) {

      $data['filter_reference_no']           = $this->request->get['filter_reference_no'];
      $filter_data['filter_reference_no']    = $this->request->get['filter_reference_no'];
    }



    if (!empty($this->request->get['filter_select_year'])) {
        $data['filter_select_year']           = $this->request->get['filter_select_year'];
        $filter_data['filter_select_year']    = $this->request->get['filter_select_year'];

        $gst_report_filter_data['filter_seller_inv_date_from'] =$filter_data['filter_select_year'].'-01'.'-01';
        $gst_report_filter_data['filter_seller_inv_date_to'] = $filter_data['filter_select_year'].'-12'.'-31';

        $gst_report_filter_data['filter_debit_note_date_from'] =$filter_data['filter_select_year'].'-01'.'-01';
        $gst_report_filter_data['filter_debit_note_date_to'] = $filter_data['filter_select_year'].'-12'.'-31';
    }

    if (!empty($this->request->get['filter_select_month'])) {
        $data['filter_select_month']           = $this->request->get['filter_select_month'];
        $filter_data['filter_select_month']    = $this->request->get['filter_select_month'];

        if(!empty($this->request->get['filter_select_year']))
        {
          $last_day = $this->days_in_month($data['filter_select_month'], $this->request->get['filter_select_year']);
          $gst_report_filter_data['filter_seller_inv_date_from'] = $this->request->get['filter_select_year'].'-'.$data['filter_select_month'].'-01';

         $gst_report_filter_data['filter_seller_inv_date_to']   = $this->request->get['filter_select_year'].'-'.$data['filter_select_month'].'-'.$last_day;
        }
        else
        {
          $last_day = $this->days_in_month($data['filter_select_month'], date("Y"));
          $gst_report_filter_data['filter_seller_inv_date_from'] = date("Y").'-'.$data['filter_select_month'].'-01';
          $gst_report_filter_data['filter_seller_inv_date_to']   = date("Y").'-'.$data['filter_select_month'].'-'.$last_day;
        }

        $gst_report_filter_data['filter_debit_note_date_from'] = $gst_report_filter_data['filter_seller_inv_date_from'];
        $gst_report_filter_data['filter_debit_note_date_to']   = $gst_report_filter_data['filter_seller_inv_date_to'];
    }


    if (!empty($this->request->get['filter_select_quarter']) && !empty($this->request->get['filter_select_year'])) {
        $data['filter_select_quarter']           = $this->request->get['filter_select_quarter'];
        $filter_data['filter_select_quarter']    = $this->request->get['filter_select_quarter'];

         $data['filter_year_range']           = $this->request->get['filter_select_year'];
        $filter_data['filter_year_range']    = $this->request->get['filter_select_year'];

        $filter_data['date_range'] = getDateRangeForQtrFY($data['filter_select_quarter'], $data['filter_year_range']);

        $gst_report_filter_data['filter_seller_inv_date_from'] = $filter_data['date_range']['start_date'];
        $gst_report_filter_data['filter_seller_inv_date_to'] = $filter_data['date_range']['end_date'];

        $gst_report_filter_data['filter_debit_note_date_from'] = $filter_data['date_range']['start_date'];
        $gst_report_filter_data['filter_debit_note_date_to'] = $filter_data['date_range']['end_date'];
        
    }

    $filter_data['start']    = ($page - 1) * $this->config->get('config_limit_admin');
    $filter_data['limit']    = $this->config->get('config_limit_admin');

    if ($seller_id!='') {
      $data['filter_seller_id']           = $seller_id;
      $filter_data['filter_seller_id']    = $seller_id;
    }

    $data['getMonths'] = getMonths();
    $data['getYears']  = getYears(2017, 0);
    $data['getQuarters'] = getQuarters();
    $data['getFinancialYears'] = getFinancialYears(2016, '');

    ///////////////////////////////////////// Sales Details ////////////////////////////////////////////////////////////
    //$results    = $this->model_accounts_gstreport->getInvoiceDetail($filter_data);
    $this->load->model('accounts/purchasereports','admin');
    $this->load->model('accounts/purchasereturnreports','admin');

    //$results = GstReports::getInvoiceDetail($this, $filter_data);
    $data['invoice_detail'] = array();


    $results_for_purchase   = $this->admin_model_accounts_purchasereports->getPurchaseDetails($gst_report_filter_data, 1);
   // echo "<pre>";print_r($results_for_purchase);die;
    $results_for_purchase = $this->_getGSTData($results_for_purchase);


    $data['invoice_details'] = array();
        foreach ($results_for_purchase as $key => $value) {
            foreach ($value['taxwise_breakup'] as $key => $taxwise_breakup) {
               if(!isset($data['invoice_details'][$taxwise_breakup['tax_rate']])){
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['seller_input_tax'] = $taxwise_breakup['tax_rate'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['total_product_value'] = (float)$taxwise_breakup['product'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['total_tax'] = (float)$taxwise_breakup['tax'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['SGST'] = (float)$taxwise_breakup['sgst_tax'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['CGST'] = (float)$taxwise_breakup['cgst_tax'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['IGST'] = (float)$taxwise_breakup['igst_tax'];
               }else{
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['seller_input_tax'] = $value['seller_input_tax'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['total_product_value'] += (float)$taxwise_breakup['product'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['total_tax'] += (float)$taxwise_breakup['tax'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['SGST'] += (float)$taxwise_breakup['sgst_tax'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['CGST'] += (float)$taxwise_breakup['cgst_tax'];
               $data['invoice_details'][$taxwise_breakup['tax_rate']]['IGST'] += (float)$taxwise_breakup['igst_tax'];
              }
            }
        }
   
    //echo "<pre>";print_r($data['invoice_details']);die;
  
    ///////////////////////////////////////// Return Details ////////////////////////////////////////////////////////////

    //$results2    = $this->model_accounts_gstreport->getReturnDetail($filter_data);
    //$results2 = GstReports::getReturnDetail($this, $filter_data);

    $results_for_return = $this->admin_model_accounts_purchasereturnreports->getPurchaseReturnDetails($gst_report_filter_data, 1);
    $results_for_return = $this->_getCsvReturnData($results_for_return);

    $data['return_detail'] = array();
    
        foreach ($results_for_return as $key => $value) {
            foreach ($value['taxwise_breakup'] as $key => $taxwise_breakup) {
               if(!isset($data['return_details'][$taxwise_breakup['tax_rate']])){
               $data['return_details'][$taxwise_breakup['tax_rate']]['seller_input_tax'] = $taxwise_breakup['tax_rate'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['total_product_value'] = (float)$taxwise_breakup['product_return'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['total_tax'] = (float)$taxwise_breakup['tax_return'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['SGST'] = (float)$taxwise_breakup['sgst_tax'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['CGST'] = (float)$taxwise_breakup['cgst_tax'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['IGST'] = (float)$taxwise_breakup['igst_tax'];
               }else{
               $data['return_details'][$taxwise_breakup['tax_rate']]['seller_input_tax'] = $taxwise_breakup['tax_rate'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['total_product_value'] += (float)$taxwise_breakup['product_return'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['total_tax'] += (float)$taxwise_breakup['tax_return'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['SGST'] += (float)$taxwise_breakup['sgst_tax'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['CGST'] += (float)$taxwise_breakup['cgst_tax'];
               $data['return_details'][$taxwise_breakup['tax_rate']]['IGST'] += (float)$taxwise_breakup['igst_tax'];
              }

            }
        }

    ///////////////////////////////////////// HSN Details ////////////////////////////////////////////////////////////
    //$results3    = $this->model_accounts_gstreport->getHSNDetail($filter_data);
   /* $results3 = GstReports::getHSNDetail($this, $filter_data);
    $data['hsn_detail'] = array();
    if(!empty($results3)){
        foreach ($results3 as $key => $result) {

            $data['hsn_detail'][$key]['seller_input_tax'] = $result['seller_input_tax'];
            $data['hsn_detail'][$key]['total_product_value'] = $result['total_product_value'];
            $data['hsn_detail'][$key]['total_tax'] = $result['total_tax'];
            $data['hsn_detail'][$key]['hsn_code'] = $result['hsn_code'];

            $unserialized = unserialize($result['seller_invoice_meta']);

            $data['hsn_detail'][$key]['buyer_data_state'] = $unserialized['buyer_data']['state'] ;
            $data['hsn_detail'][$key]['seller_data_state'] = $unserialized['seller_data']['state'] ;

            if (strtolower($unserialized['buyer_data']['state']) == strtolower($unserialized['seller_data']['state']))
            {
              $data['hsn_detail'][$key]['SGST'] = $data['hsn_detail'][$key]['total_tax']/2;
              $data['hsn_detail'][$key]['CGST'] = $data['hsn_detail'][$key]['total_tax']/2;
              $data['hsn_detail'][$key]['IGST'] = 0;
            }
            else
            {
              $data['hsn_detail'][$key]['SGST'] = 0;
              $data['hsn_detail'][$key]['CGST'] = 0;
              $data['hsn_detail'][$key]['IGST'] = $data['hsn_detail'][$key]['total_tax'];
            }
        }
    }
    $data['hsn_details'] = array();
    
    if(!empty($data['hsn_detail'])){
      foreach ($data['hsn_detail'] as $value) {
        
        if(!isset($data['hsn_details'][$value['hsn_code']])){
          $data['hsn_details'][$value['hsn_code']]['hsn_code'] = $value['hsn_code'];
          $data['hsn_details'][$value['hsn_code']]['seller_input_tax'] = $value['seller_input_tax'];
          $data['hsn_details'][$value['hsn_code']]['total_product_value'] = (float)$value['total_product_value'];
          $data['hsn_details'][$value['hsn_code']]['total_tax'] = (float)$value['total_tax'];
          $data['hsn_details'][$value['hsn_code']]['SGST'] = (float)$value['SGST'];
          $data['hsn_details'][$value['hsn_code']]['CGST'] = (float)$value['CGST'];
          $data['hsn_details'][$value['hsn_code']]['IGST'] = (float)$value['IGST'];
        }else{
           $data['hsn_details'][$value['hsn_code']]['hsn_code'] = $value['hsn_code'];
          $data['hsn_details'][$value['hsn_code']]['seller_input_tax'] = $value['seller_input_tax'];
          $data['hsn_details'][$value['hsn_code']]['total_product_value'] += (float)$value['total_product_value'];
          $data['hsn_details'][$value['hsn_code']]['total_tax'] += (float)$value['total_tax'];
          $data['hsn_details'][$value['hsn_code']]['SGST'] += (float)$value['SGST'];
          $data['hsn_details'][$value['hsn_code']]['CGST'] += (float)$value['CGST'];
          $data['hsn_details'][$value['hsn_code']]['IGST'] += (float)$value['IGST'];
        }
      }
    }*/
       $data['hsn_details'] = array();
        foreach ($results_for_purchase as $key => $value) {
            foreach ($value['taxwise_breakup'] as $key => $taxwise_breakup) {
               if(!isset($data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']])){
                $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['hsn_code']        = $taxwise_breakup['hsn_code']; 
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['seller_input_tax'] = $taxwise_breakup['tax_rate'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['total_product_value'] = (float)$taxwise_breakup['product'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['total_tax'] = (float)$taxwise_breakup['tax'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['SGST'] = (float)$taxwise_breakup['sgst_tax'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['CGST'] = (float)$taxwise_breakup['cgst_tax'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['IGST'] = (float)$taxwise_breakup['igst_tax'];
               }else{
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['hsn_code']        = $taxwise_breakup['hsn_code'];  
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['seller_input_tax'] = $taxwise_breakup['tax_rate'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['total_product_value'] += (float)$taxwise_breakup['product'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['total_tax'] += (float)$taxwise_breakup['tax'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['SGST'] += (float)$taxwise_breakup['sgst_tax'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['CGST'] += (float)$taxwise_breakup['cgst_tax'];
               $data['hsn_details'][$taxwise_breakup['hsn_code'].'-'.$taxwise_breakup['tax_rate']]['IGST'] += (float)$taxwise_breakup['igst_tax'];
              }

            }
        }
/////////////////////////////////////////////////////////////
/*
    //Generate pdf to download data
    if(isset($this->request->get['download']) && $this->request->get['download'] == 'csv'){
      $this->_generatePaymentCsv($data['invoice_details'], $data['return_details'], $data['hsn_details']);
    }
*/
    //Generate pdf to download data
    if(isset($this->request->get['download'])){
       
      $this->_generatePaymentCsv($results_for_purchase, $results_for_return, $data['hsn_details'], $this->request->get['download']);
    }

    $data['token'] = $this->session->data['token'];
    
    $data["seller_data"] = $data;
    $data["header"] =   $this->load->controller('seller_panel/seller_header',$data);
    $data["footer"] =   $this->load->controller('seller_panel/seller_footer');
    $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/accounts/gstreport.tpl', $data));

  }


    private function _getGSTData($results) {
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
            $details_grouping[$key]['taxwise_breakup'][$i]['hsn_code'] = $result['hsn_code'];
            $details_grouping[$key]['taxwise_breakup'][$i]['tax_rate'] = $tax_rate;
            $details_grouping[$key]['taxwise_breakup'][$i]['tax'] = $tax;
            $details_grouping[$key]['taxwise_breakup'][$i]['igst_tax'] = $is_igst ? $tax : "";
            $details_grouping[$key]['taxwise_breakup'][$i]['cgst_tax'] = $details_grouping[$key]['taxwise_breakup'][$i]['sgst_tax'] = $is_igst ? "" : $tax / 2;
            $details_grouping[$key]['taxwise_breakup'][$i]['is_igst'] = $is_igst;
            $details_grouping[$key]['taxwise_breakup'][$i]['purchase'] = $purchase;
            $details_grouping[$key]['seller_nickname'] = $result['nickname'];
            $details_grouping[$key]['seller_input_tax'] = $result['seller_input_tax'];
            $i++;
        }
        return $details_grouping;
    }


    private function _getCsvReturnData($results) {
        $details_grouping = array();
        $taxwise_breakup = array();
        $is_igst = 1;
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
            $details_grouping[$key]['seller_input_tax'] = $result['seller_input_tax'];
            $details_grouping[$key]['seller_nickname'] = $result['nickname'];
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

  private function _generatePaymentCsv($invoice_details, $return_details, $hsn_details, $csv) {
  
    $csvData = array();

    if ($csv == 'csvInvoice')
    {
      $csvData = $invoice_details;
      $this->_generateTallyPurchaseCsv($invoice_details);
      exit;  
    }

    elseif ($csv == 'csvReturn')
    {
      $csvData = $return_details; 
      $this->_generateTallyPurchaseRetuenCsv($return_details); 
      exit;
    }
    elseif ($csv == 'csvHSN')
    {
      $csvData = $hsn_details;  
    }
    

    $file_name = DIR_DOWNLOAD .'gst-report'.time().'.csv';
    $fp = fopen($file_name, 'w');
    ob_clean();
    if(empty($csvData)){
      $data = array("No Data Found.");
      fputcsv($fp, $data);
    }else{
      $csvFileData = array();
      /*
      * heading values
      */
      $heading = array("HSN Code","GST Rate","Product Value","CGST", "SGST", "IGST", "Total Tax");

      $csvFileData[0] = $heading;

      foreach ($csvData as $key => $value) {

        $row = array();
        $hsn_code = $value['hsn_code'];
        $seller_input_tax = $value['seller_input_tax'];
        $total_product_value = $value['total_product_value'];
        $SGST = $value['SGST'];
        $CGST = $value['CGST'];
        $IGST = $value['IGST'];
        $total_tax = $value['total_tax'];
        
        $row[] = stripslashes( $hsn_code );  
        $row[] = stripslashes( $seller_input_tax );
        $row[] = stripslashes( $total_product_value );                                  
        $row[] = stripslashes( $SGST );
        $row[] = stripslashes( $CGST );
        $row[] = stripslashes( $IGST );
        $row[] = stripslashes( $total_tax );

        $csvFileData[] = $row;

      }
      foreach ($csvFileData as $line) {
          fputcsv($fp, $line);
      }
    }
    fclose($fp);

    if (file_exists($file_name)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($file_name));
      readfile($file_name);
      exit();
    }
  }
  

    private function _generateTallyPurchaseCsv($results) {

        $file_name = DIR_DOWNLOAD .'gst-report'.time().'.csv';
        $fp = fopen($file_name, 'w');
        ob_clean();
        if(empty($results)){
          $data = array("No Data Found.");
          fputcsv($fp, $data);
         }else{
        
      /*
      * heading values
      */
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
        foreach ($results as $key => $value) {
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

      }

        fclose($fp);
          if (file_exists($file_name)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
          }


    }

    private function _generateTallyPurchaseRetuenCsv($results) {
        $file_name = DIR_DOWNLOAD .'gst-report'.time().'.csv';
        
        $fp = fopen($file_name, 'w');
        ob_clean();
        if(empty($results)){
          $data = array("No Data Found.");
          fputcsv($fp, $data);
         }else{

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

        foreach ($results as $key => $value) {
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
      }  
        fclose($fp);
     
         if (file_exists($file_name)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
          }
    }

   public function days_in_month($month, $year) 
   { 
     // calculate number of days in a month 
     return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
   }


}