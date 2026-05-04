<?php
     require_once('system.php');
     require_once( DIR_SYSTEM . 'library/currency.php' );
     require_once( DIR_SYSTEM . 'library/seller/paymentreports.php' );
     require_once( DIR_SYSTEM . 'library/securefiledownload.php' );
     require_once( DIR_SYSTEM . 'engine/event.php' );

    class PaymentController extends SystemController
    {
        private $error = array();
        private $_obj_order_stores;
        private $record_limits = 10;

        public function __construct($params) {

            parent::__construct($params);

            $this->_obj_payment_reports = new PaymentReports();
            // Currency
            $this->registry->set('currency', new Currency($this->registry));
            
            // SecureFileDownload                                        
            $this->registry->set('securefiledownload', new SecureFileDownload($this->registry));
        }
        /**
         * customer login
         */

       public function paymentreports() 
        {
            
          $this->load->model('wsb_purchase/import', 'admin');
          $invoice_details_total = 0;
          $payment_details = array();

            $payment_details = array();
            $seller_id      = $this->customer->getId();
            $data           = array();
            $data['error']  = false;
            $download       = NULL;


            if(isset($this->request['download'])) 
             {
               $download = $this->request['download'];
               $excelData = array();
                $csvRowCount = 0;
             }
    
             $filter_data = array();
    
            if ( !empty($this->request['filter_invoice_no']) ) {
              $filter_data['filter_invoice_no']    = $this->request['filter_invoice_no'];
            }
           
            if ( !empty($this->request['filter_order_no']) ) {
              $filter_data['filter_order_no']    = $this->request['filter_order_no'];
            }

            if ( !empty($this->request['filter_seller_code']) ) {
              $filter_data['filter_seller_code']    = $this->request['filter_seller_code'];
            }

            if ( !empty($this->request['filter_company_name']) ) {
              $filter_data['filter_company_name']    = $this->request['filter_company_name'];
            }

            if (!empty($this->request['filter_payment_date_to']) && !empty($this->request['filter_payment_date_from'])) 
            {
              
              $filter_payment_date_to = date('d/m/Y',strtotime($this->request['filter_payment_date_to']));

              $filter_payment_date_from = date('d/m/Y',strtotime($this->request['filter_payment_date_from']));

              $filter_data['filter_payment_date_range'] = $filter_payment_date_from.'-'.$filter_payment_date_to;

            }
            else if(!empty($this->request['filter_payment_date_to']))
            {
              $filter_payment_date_to = date('d/m/Y',strtotime($this->request['filter_payment_date_to']));

              $filter_data['filter_payment_date_range'] = $filter_payment_date_to.'-'.$filter_payment_date_to;
            }
            else if(!empty($this->request['filter_payment_date_from']))
            {
             $filter_payment_date_from = date('d/m/Y',strtotime($this->request['filter_payment_date_from']));

              $filter_data['filter_payment_date_range'] = $filter_payment_date_from.'-'.$filter_payment_date_from;
            }



            if (!empty($this->request['filter_invoice_date_to']) && !empty($this->request['filter_invoice_date_from'])) 
            {
              
              $filter_invoice_date_to = date('d/m/Y',strtotime($this->request['filter_invoice_date_to']));

              $filter_invoice_date_from = date('d/m/Y',strtotime($this->request['filter_invoice_date_from']));

              $filter_data['filter_invoice_date_range'] = $filter_invoice_date_from.'-'.$filter_invoice_date_to;

            }
            else if(!empty($this->request['filter_invoice_date_to']))
            {
              $filter_invoice_date_to = date('d/m/Y',strtotime($this->request['filter_invoice_date_to']));

              $filter_data['filter_invoice_date_range'] = $filter_invoice_date_to.'-'.$filter_invoice_date_to;
            }
            else if(!empty($this->request['filter_invoice_date_from']))
            {
             $filter_invoice_date_from = date('d/m/Y',strtotime($this->request['filter_invoice_date_to']));

              $filter_data['filter_invoice_date_range'] = $filter_invoice_date_from.'-'.$filter_invoice_date_from;
            }

            if ( !empty($this->request['filter_utr']) ) {
              $filter_data['filter_utr']    = $this->request['filter_utr'];
             }


            if ( !empty($this->request['start']) ) {
            $filter_data['start'] = $this->request['start'];
            } else {
             $filter_data['start'] = 0;
            }

            if( !empty($this->request['limit'])  ){
              $filter_data['limit'] = $this->request['limit'];
            } else {
              $filter_data['limit'] = $this->config->get('config_limit_admin');
            } 
      

            if ($seller_id!='') {
              $filter_data['filter_seller_id']    = $seller_id;
            }

 
           $data['payment_details']  = array();

          if(isset($this->request['download'])) 
          {
            $common_invoice_details = PaymentReports::getInvoiceIds($this, $filter_data, 'download');
          } else {
            $common_invoice_details = PaymentReports::getInvoiceIds($this, $filter_data);
          }

          $invoice_details_total = PaymentReports::getInvoiceIds($this, $filter_data, 'total');


		    if(!empty($common_invoice_details)) {

		      $invoice_ids_order_arr = $common_invoice_details;
		      $seller_invoice_ids = array_keys(array_column($common_invoice_details, 'tbl', 'invoice_ids'), 'oc_seller_invoice');
		      $seller_invoice_ids = explode(',', implode(',', $seller_invoice_ids));
		      $wsb_purchase_invoice_ids = array_keys(array_column($common_invoice_details, 'tbl', 'invoice_ids'), 'oc_wsb_purchase');
		      $wsb_purchase_invoice_ids = explode(',', implode(',',$wsb_purchase_invoice_ids));
		      
		      $seller_invoices_detail = array();
		      $wsb_purchase_invoices_detail = array();
		      if(!empty($seller_invoice_ids)) {
		        $seller_invoices_detail = PaymentReports::getSellerInvoicesDetail($this, $seller_invoice_ids);
		      }
		      
		      if(!empty($wsb_purchase_invoice_ids)) {
		        $wsb_purchase_invoices_detail = PaymentReports::getWsbPurchaseInvoicesDetail($this, $wsb_purchase_invoice_ids);
		      }
		      $final_invoice_details = array_merge($seller_invoices_detail, $wsb_purchase_invoices_detail);
		      $formatted_payment_details = array();
		      if(!empty($final_invoice_details)) {
		        $secureFileDload = new SecureFileDownload($this->registry);
		        $currencyObj = new Currency($this->registry);
		        $formatted_payment_details = PaymentReports::getInvoiceDetailsInReportFormat   ($this, $invoice_ids_order_arr, $final_invoice_details, $secureFileDload, $currencyObj, $this->admin_model_wsb_purchase_import);

		      }
		
        if($download && $download  == 'csv')
         {
           $files = $this->_generatePaymentCsv($formatted_payment_details);
           $payment_details['file_name'] = $files['file_name'];
           $payment_details['file_link'] = $files['file_link'];
         } 
        else
         { 
           $payment_details[0]=array('id'=>0,'order_no'=>'','invoice'=>array(),'summary'=>array(),'returns'=>array(),'payments'=>array());
		       foreach($formatted_payment_details as $order_data)
		       {
		      	  foreach($order_data as $key => $invoice_details)
		      	  {
                $payments = array();
                $returns  = array();

                if(isset($invoice_details['payments']))
                {
                  $payments = $invoice_details['payments'];
                }

                if(isset($invoice_details['returns']))
                {
                  $returns = $invoice_details['returns'];
                }

                $order_no          = $invoice_details['order_no'];

		      	   	$payment_details[$order_no]['id']       = $invoice_details['order_no'];
                $payment_details[$order_no]['order_no'] = $invoice_details['order_no'];
		      	   	$payment_details[$order_no]['invoice'][$key] = $invoice_details['invoice'];

                $payment_details[$order_no]['returns'][$key] = $returns;
                $payment_details[$order_no]['payments'][$key]= $payments;

		      	   	$payment_details[$order_no]['summary'][$key] = array('net_payable_formatted' => $invoice_details['net_payable_formatted'], 'paid_formatted' => $invoice_details['paid_formatted'], 'balance_formatted' => $invoice_details['balance_formatted']);

		      	   }
		        }
            
            foreach($payment_details as $key => $payment_details_new)
            {
              $return_arr = array();
              $payment_arr = array();
              $payment_details[$key]['invoice'] = array_values($payment_details_new['invoice']);
              $payment_details[$key]['summary'] = array_values($payment_details_new['summary']);
              foreach($payment_details_new['returns'] as $returns_data)
              {
                if(count($returns_data) > 0) {  $return_arr = array_merge($return_arr,$returns_data); } 
                else {  array_push($return_arr, array()); }
              }
              foreach($payment_details_new['payments'] as $payments_data)
              {
                if(count($payments_data) > 0) {
                 $payment_arr = array_merge($payment_arr,$payments_data);
                } else { array_push($payment_arr, array());  }
              }
                $payment_details[$key]['payments'] =  $payment_arr;
                 $payment_details[$key]['returns']  =  $return_arr;
             }
            $payment_details = array_values($payment_details);
          }         
		    }
            // Generate Excel

      $this->data_packet->data           = $payment_details;
      $this->data_packet->totalRecord    = $invoice_details_total;
      $this->data_packet->statusCode     = 200;
      return $this->data_packet;
  }


private function _generatePaymentCsv($paymentData) {
    
    $csvData = array();
    $csvData[] = array('Order No', 'Invoice No', 'Invoice Date', 'Invoice Value', 'DN No', 'DN Date', 'DN Value', 'Payment UTR', 'Payment Date', 'Payment Amount');
    $csvData[] = array('');
    foreach ($paymentData as $key => $order_data) {
      $order_invoice_count = count($order_data);
      $current_order_csv_rows = array();
      $invoice_returns_count = 0;
      $current_order_payments = array();
      foreach ($order_data as $invoice_id => $invoice_all_data) {
        if(!empty($invoice_all_data['returns'])) {
          $invoice_returns_count += count($invoice_all_data['returns']);
          foreach ($invoice_all_data['returns'] as $return_key => $return_data) {
            $current_order_csv_rows[] = array($invoice_all_data['order_no'], 
                                              $invoice_all_data['invoice']['invoice_no'], 
                                              $invoice_all_data['invoice']['invoice_date'], 
                                              $invoice_all_data['invoice']['invoice_value'],
                                              $return_data['dn_no'],
                                              $return_data['dn_date'],
                                              $return_data['dn_value']
                                            );
          }
        } else {
          $current_order_csv_rows[] = array($invoice_all_data['order_no'], $invoice_all_data['invoice']['invoice_no'], $invoice_all_data['invoice']['invoice_date'], $invoice_all_data['invoice']['invoice_value'], '', '', '');
        }
        // Payments
        if(!empty($invoice_all_data['payments'])) {
          foreach ($invoice_all_data['payments'] as $payment_key => $payment_data) {
            $current_order_payments[] = array($payment_data['trxn_utr'], $payment_data['trxn_utr_date'], $payment_data['trxn_amount']);
          }
        } else {
          if(!empty($invoice_all_data['full_return'])) {
            $current_order_payments[] = array($invoice_all_data['full_return'], $invoice_all_data['full_return'], $invoice_all_data['full_return']);
          }
        }
      }
      
      if(!empty($current_order_payments)) {
        foreach ($current_order_csv_rows as $key => $current_row_data) {
          if(!empty($current_order_payments[0])) {
            $csvData[] = array_merge($current_row_data, $current_order_payments[0]);
            unset($current_order_payments[0]);
            $current_order_payments = array_values($current_order_payments);
          } else {
            $csvData[] = $current_row_data;
          }
        }
        if(!empty($current_order_payments[0])) {
          foreach ($current_order_payments as $key => $pay_data) {
            /*** $current_order_csv_rows[0][3] i.e. invoice value will be skipped as it increases total invoice values while calculating in csv***/
            $csvData[] = array($current_order_csv_rows[0][0], $current_order_csv_rows[0][1], $current_order_csv_rows[0][2], '', '', '', '', $pay_data[0], $pay_data[1], $pay_data[2]);
          }
        }
      } else {
        foreach ($current_order_csv_rows as $key => $current_row_data) {
          $csvData[] = $current_row_data;
        }
      }
    }
    
    // setup csv file
    $file_name = 'Payment_Report_'.date("Y-m-d").'_'.$this->customer->getId().'.csv';
    $file_link =  HTTPS_SERVER.'api/sellers/report/download_file?download_file='.$file_name;
    $folder_path = DIR_DOWNLOAD;
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    $filepath = $folder_path.$file_name;
    $file = fopen($filepath, 'w');

    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
    foreach ($csvData as $row) {
      fwrite($file, implode(',', $row) . "\r\n");
    }
    fclose ($file);
    return array('file_name'=>$file_name, 'file_link'=>$file_link);
    /*if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filepath));
      readfile($filepath);
      exit();
    }*/
  }


  private function _generatePaymentExcel($excelData) {
    
    require_once DIR_SYSTEM.'library/PHPExcel/IOFactory.php';
    
    $file_name = 'Payment_Report_'.date("Y-m-d",time()).'.xlsx';
    $file_path = DIR_DOWNLOAD.$file_name;
    
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial Black');
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
    $objPHPExcel->getProperties()->setCreator("Wholesalebox")->setTitle("Seller Payments Report")->setDescription("Excel for Seller Payment Reports");
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    $sheet = $objPHPExcel->getActiveSheet();
    
    // Add column headers
    $sheet->getCell('A1')->setValue('Order No');
    $sheet->getCell('B1')->setValue('Processing Date');
    $sheet->getCell('C1')->setValue('Invoice No.');
    $sheet->getCell('D1')->setValue('Invoice Date');
    $sheet->getCell('E1')->setValue('Invoice Value');
    $sheet->getCell('F1')->setValue('Return DN No.');
    $sheet->getCell('G1')->setValue('Return DN Date');
    $sheet->getCell('H1')->setValue('Return DN Value');
    $sheet->getCell('I1')->setValue('Payment Date');
    $sheet->getCell('J1')->setValue('Payment Amount');
    $sheet->getCell('K1')->setValue('Payment UTR No.');
    $sheet->getCell('L1')->setValue('Net Payable');
    $sheet->getCell('M1')->setValue('Balance');
    $sheet->getStyle("A1:M1")->getFont()->setBold( true );
    
    // Set worksheet title
    $sheet->setTitle($file_name);
    
    // Set data
    if(!empty($excelData)) {
      $rowNumber = 2;
      foreach ($excelData as $key => $reportData) {
        $merge_cell_start_net_payable = '';
        $merge_cell_end_net_payable = '';
        $merge_cell_start_balance = '';
        $merge_cell_end_balance = '';
        
        if(isset($reportData['invoice']['payments'])) {
          $merge_cell_start_net_payable = 'L'.$rowNumber;
          $merge_cell_start_balance = 'M'.$rowNumber;
          
          foreach ($reportData['invoice']['payments'] as $key => $invoice_payment_data) {
            $sheet->getCell('A'.$rowNumber)->setValue($reportData['order_no']);
            $sheet->getCell('B'.$rowNumber)->setValue($reportData['order_date']);
            
            $sheet->getCell('C'.$rowNumber)->setValue($reportData['invoice']['invoice_no']);
            $sheet->getCell('D'.$rowNumber)->setValue($reportData['invoice']['invoice_date']);
            $sheet->getCell('E'.$rowNumber)->setValue($reportData['invoice']['invoice_value']);
            
            $sheet->getCell('F'.$rowNumber)->setValue('');
            $sheet->getCell('G'.$rowNumber)->setValue('');
            $sheet->getCell('H'.$rowNumber)->setValue('');
            
            $sheet->getCell('I'.$rowNumber)->setValue($invoice_payment_data['trxn_utr_date']);
            $sheet->getCell('J'.$rowNumber)->setValue($invoice_payment_data['trxn_amount']);
            $sheet->getCell('K'.$rowNumber)->setValue($invoice_payment_data['trxn_utr']);
            
            $merge_cell_end_net_payable = 'L'.$rowNumber;
            $merge_cell_end_balance = 'M'.$rowNumber;
            $rowNumber++;
          }
          // merge netpayables cell
          $sheet->mergeCells($merge_cell_start_net_payable.":".$merge_cell_end_net_payable);
          $sheet->getCell($merge_cell_start_net_payable)->setValue($reportData['net_payable']);
          
          // merge balance cell
          $sheet->mergeCells($merge_cell_start_balance.":".$merge_cell_end_balance);
          $sheet->getCell($merge_cell_start_balance)->setValue($reportData['balance']);
          
        } else {  // no payments for this invoice
          
          $sheet->getCell('A'.$rowNumber)->setValue($reportData['order_no']);
          $sheet->getCell('B'.$rowNumber)->setValue($reportData['order_date']);
          
          $sheet->getCell('C'.$rowNumber)->setValue($reportData['invoice']['invoice_no']);
          $sheet->getCell('D'.$rowNumber)->setValue($reportData['invoice']['invoice_date']);
          $sheet->getCell('E'.$rowNumber)->setValue($reportData['invoice']['invoice_value']);
          
          $sheet->getCell('F'.$rowNumber)->setValue('');
          $sheet->getCell('G'.$rowNumber)->setValue('');
          $sheet->getCell('H'.$rowNumber)->setValue('');
          
          $sheet->getCell('I'.$rowNumber)->setValue('');
          $sheet->getCell('J'.$rowNumber)->setValue('');
          $sheet->getCell('K'.$rowNumber)->setValue('');
          
          $sheet->getCell('L'.$rowNumber)->setValue($reportData['net_payable']);
          $sheet->getCell('M'.$rowNumber)->setValue($reportData['net_payable']);
          $rowNumber++;
        }
        if(isset($reportData['returns']) && !empty($reportData['returns'])) {
          $merge_cell_start_return_net_payable = '';
          $merge_cell_end_return_net_payable = '';
          $merge_cell_start_return_balance = '';
          $merge_cell_end_return_balance = '';
          
          foreach ($reportData['returns'] as $return_key => $returnData) {
            $merge_cell_start_return_net_payable = 'L'.$rowNumber;
            $merge_cell_start_return_balance = 'M'.$rowNumber;
            
            if(isset($returnData[$return_key]['payments'])) {
              foreach ($returnData[$return_key]['payments'] as $key => $return_payment_data) {
                $sheet->getCell('A'.$rowNumber)->setValue($reportData['order_no']);
                $sheet->getCell('B'.$rowNumber)->setValue($reportData['order_date']);
                
                $sheet->getCell('C'.$rowNumber)->setValue($reportData['invoice']['invoice_no']);
                $sheet->getCell('D'.$rowNumber)->setValue($reportData['invoice']['invoice_date']);
                $sheet->getCell('E'.$rowNumber)->setValue($reportData['invoice']['invoice_value']);
                
                $sheet->getCell('F'.$rowNumber)->setValue($returnData['dn_no']);
                $sheet->getCell('G'.$rowNumber)->setValue($returnData['dn_date']);
                $sheet->getCell('H'.$rowNumber)->setValue($returnData['dn_value']);
                
                $sheet->getCell('I'.$rowNumber)->setValue($return_payment_data['trxn_utr_date']);
                $sheet->getCell('J'.$rowNumber)->setValue($return_payment_data['trxn_amount']);
                $sheet->getCell('K'.$rowNumber)->setValue($return_payment_data['trxn_utr']);
                
                $merge_cell_end_return_net_payable = 'L'.$rowNumber;
                $merge_cell_end_return_balance = 'M'.$rowNumber;
                $rowNumber++;
              }
              // merge netpayables cell
              $sheet->mergeCells($merge_cell_start_return_net_payable.":".$merge_cell_end_return_net_payable);
              $sheet->getCell($merge_cell_start_return_net_payable)->setValue($returnData['net_payable']);
              
              // merge balance cell
              $sheet->mergeCells($merge_cell_start_return_balance.":".$merge_cell_end_return_balance);
              $sheet->getCell($merge_cell_start_return_balance)->setValue($returnData['balance']);
            
            } else { // no payment for this return 
              $sheet->getCell('A'.$rowNumber)->setValue($reportData['order_no']);
              $sheet->getCell('B'.$rowNumber)->setValue($reportData['order_date']);
              
              $sheet->getCell('C'.$rowNumber)->setValue($reportData['invoice']['invoice_no']);
              $sheet->getCell('D'.$rowNumber)->setValue($reportData['invoice']['invoice_date']);
              $sheet->getCell('E'.$rowNumber)->setValue($reportData['invoice']['invoice_value']);
              
              $sheet->getCell('F'.$rowNumber)->setValue($returnData['dn_no']);
              $sheet->getCell('G'.$rowNumber)->setValue($returnData['dn_date']);
              $sheet->getCell('H'.$rowNumber)->setValue($returnData['dn_value']);
              
              $sheet->getCell('I'.$rowNumber)->setValue('');
              $sheet->getCell('J'.$rowNumber)->setValue('');
              $sheet->getCell('K'.$rowNumber)->setValue('');
              
              $sheet->getCell('L'.$rowNumber)->setValue($returnData['net_payable']);
              $sheet->getCell('M'.$rowNumber)->setValue($returnData['net_payable']);
              $rowNumber++;
            }
          }
        }
      }
      $sheet->getStyle("A1:M".$rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $sheet->getStyle("A1:M".$rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    } else {
      
    }
    ob_clean();
    //header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    //header("Content-Disposition: attachment; filename=".basename($file_path));
    //header("Cache-Control: max-age=0");
    
    $objWriter->save("php://output");
    //flush();
    return $file_name;
  }

}