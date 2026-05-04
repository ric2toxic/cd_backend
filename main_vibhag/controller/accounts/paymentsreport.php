<?php
class ControllerAccountsPaymentsreport extends Controller {
	
	public function index() {
		
		$data           = array();
		$data['error']  = false;
		
		$this->load->autoLoadLanguage('accounts/paymentsreport', $data);
		$this->load->model('wsb_purchase/import');
		
		$this->document->setTitle($data['heading_payments_report']);
		
		$url = '';
		$page_limit = 15;
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$download = false;
		if(isset($this->request->get['download']) && $this->request->get['download'] == 'csv') {
			$download = true;
			$excelData = array();
			$csvRowCount = 0;
		}
		
		$filter_data = array();
		
		$data['filter_order_no']            = '';
		$data['filter_seller_code']         = '';
		$data['filter_company_name']        = '';
		$data['filter_date_range']          = '';
		$data['filter_invoice_date_range_from']  = '';
		$data['filter_invoice_date_range_to']  = '';
		$data['filter_payment_date_range_from']   = '';
		$data['filter_payment_date_range_to']   = '';
		$data['filter_invoice_no']   = '';
		$data['filter_utr']   = '';
		$pagination_filter = "";
		if ( !empty($this->request->get['filter_seller_code']) ) {
			$data['filter_seller_code']           = $this->request->get['filter_seller_code'];
			$filter_data['filter_seller_code']    = $this->request->get['filter_seller_code'];
			$pagination_filter .= "&filter_seller_code=".urlencode($this->request->get['filter_seller_code']);
		}
		if ( !empty($this->request->get['filter_company_name']) ) {
			$data['filter_company_name']           = $this->request->get['filter_company_name'];
			$filter_data['filter_company_name']    = $this->request->get['filter_company_name'];
			$pagination_filter .= "&filter_company_name=".urlencode($this->request->get['filter_company_name']);
		}
		if ( !empty($this->request->get['filter_invoice_no']) ) {
			$data['filter_invoice_no']           = $this->request->get['filter_invoice_no'];
			$filter_data['filter_invoice_no']    = $this->request->get['filter_invoice_no'];
			$pagination_filter .= "&filter_invoice_no=".urlencode($this->request->get['filter_invoice_no']);
		}
		if ( !empty($this->request->get['filter_order_no']) ) {
			$data['filter_order_no']           = $this->request->get['filter_order_no'];
			$filter_data['filter_order_no']    = $this->request->get['filter_order_no'];
			$pagination_filter .= "&filter_order_no=".urlencode($this->request->get['filter_order_no']);
		}
		if ( !empty($this->request->get['filter_seller_code']) ) {
			$data['filter_seller_code']           = $this->request->get['filter_seller_code'];
			$filter_data['filter_seller_code']    = $this->request->get['filter_seller_code'];
			$pagination_filter .= "&filter_seller_code=".urlencode($this->request->get['filter_seller_code']);
		}
		if ( !empty($this->request->get['filter_company_name']) ) {
			$data['filter_company_name']           = $this->request->get['filter_company_name'];
			$filter_data['filter_company_name']    = $this->request->get['filter_company_name'];
			$pagination_filter .= "&filter_company_name=".urlencode($this->request->get['filter_company_name']);
		}
		if ( !empty($this->request->get['filter_invoice_date_range_from']) && !empty($this->request->get['filter_invoice_date_range_to'])) {
			$data['filter_invoice_date_range_from']           = $this->request->get['filter_invoice_date_range_from'];
			$data['filter_invoice_date_range_to']           = $this->request->get['filter_invoice_date_range_to'];
			$filter_data['filter_invoice_date_range']    = $this->request->get['filter_invoice_date_range_from'].' - '.$this->request->get['filter_invoice_date_range_to'];
			$pagination_filter .= "&filter_invoice_date_range_from=".urlencode($this->request->get['filter_invoice_date_range_from']);
			$pagination_filter .= "&filter_invoice_date_range_to=".urlencode($this->request->get['filter_invoice_date_range_to']);
		}
		if ( !empty($this->request->get['filter_payment_date_range_from']) && !empty($this->request->get['filter_payment_date_range_to'])) {
			$data['filter_payment_date_range_from']           = $this->request->get['filter_payment_date_range_from'];
			$data['filter_payment_date_range_to']           = $this->request->get['filter_payment_date_range_to'];
			$filter_data['filter_payment_date_range']    = $this->request->get['filter_payment_date_range_from'].' - '.$this->request->get['filter_payment_date_range_to'];
			$pagination_filter .= "&filter_payment_date_range_from=".urlencode($this->request->get['filter_payment_date_range_from']);
			$pagination_filter .= "&filter_payment_date_range_to=".urlencode($this->request->get['filter_payment_date_range_to']);
		}
		if ( !empty($this->request->get['filter_utr']) ) {
			$data['filter_utr']           = $this->request->get['filter_utr'];
			$filter_data['filter_utr']    = $this->request->get['filter_utr'];
			$pagination_filter .= "&filter_utr=".urlencode($this->request->get['filter_utr']);
		}
		
		$filter_data['start']    = ($page - 1) * $page_limit;
		$filter_data['limit']    = $page_limit;
		$PaymentReports = new PaymentReports($this);//echo "<pre>";print_r($PaymentReports);die;
		$data['payment_details']  = array();
		if(isset($this->request->get['download']) && $this->request->get['download'] == 'csv') {
			$common_invoice_details = $PaymentReports->getInvoiceIds($this, $filter_data, 'download');
		} else {
			$common_invoice_details = $PaymentReports->getInvoiceIds($this, $filter_data);
			$invoice_details_total = $PaymentReports->getInvoiceIds($this, $filter_data, 'total');
		}
		
		if(!empty($common_invoice_details)) {
			$invoice_ids_order_arr = $common_invoice_details;
			
			$seller_invoice_ids = array_keys(array_column($common_invoice_details, 'tbl', 'invoice_ids'), 'oc_seller_invoice');
			$seller_invoice_ids = explode(',', implode(',', $seller_invoice_ids));
			
			$wsb_purchase_invoice_ids = array_keys(array_column($common_invoice_details, 'tbl', 'invoice_ids'), 'oc_wsb_purchase');
			$wsb_purchase_invoice_ids = explode(',', implode(',',$wsb_purchase_invoice_ids));
			
			$seller_invoices_detail = array();
			$wsb_purchase_invoices_detail = array();
			
			if(!empty($seller_invoice_ids)) {
				$seller_invoices_detail = $PaymentReports->getSellerInvoicesDetail($this, $seller_invoice_ids);
			}
			
			if(!empty($wsb_purchase_invoice_ids)) {
				$wsb_purchase_invoices_detail = $PaymentReports->getWsbPurchaseInvoicesDetail($this, $wsb_purchase_invoice_ids);
			}
			
			$final_invoice_details = array_merge($seller_invoices_detail, $wsb_purchase_invoices_detail);
			
			$formatted_payment_details = array();
			if(!empty($final_invoice_details)) {
				$secureFileDload = new SecureFileDownload($this->registry);
				$currencyObj = new Currency($this->registry);
				$formatted_payment_details = $PaymentReports->getInvoiceDetailsInReportFormat($this, $invoice_ids_order_arr, $final_invoice_details, $secureFileDload, $currencyObj, $this->model_wsb_purchase_import);
			}
			$data['payment_details'] = $formatted_payment_details;
		}
		
		// Generate Excel
		if($download) {
			$this->_generatePaymentCsv($data['payment_details']);
		}
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_payments_report'],
			'href' => $this->url->link('accounts/paymentsreport', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		// URL for pagination
		$pagination_url = $url;
		
		if (isset($this->request->get['sort'])) {
			$pagination_url .= '&sort=' . $this->request->get['sort'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $invoice_details_total;
		$pagination->page = $page;
		$pagination->limit = $page_limit;//$page_limit;
		$pagination->url = $this->url->link('accounts/paymentsreport', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}' . $pagination_filter, 'SSL');
		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($data['text_pagination'], ($invoice_details_total) ? (($page - 1) * $page_limit) + 1 : 0, ((($page - 1) * $page_limit) > ($invoice_details_total - $page_limit)) ? $invoice_details_total : ((($page - 1) * $page_limit) + $page_limit), $invoice_details_total, ceil($invoice_details_total / $page_limit));
		
		$data['token'] = $this->session->data['token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		// $data["seller_data"] = $data;
		// $data["header"] =   $this->load->controller('seller_panel/seller_header',$data);
		// $data["footer"] =   $this->load->controller('seller_panel/seller_footer');
		$this->response->setOutput($this->load->view('accounts/paymentsreport.tpl', $data));
		
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
    $file_name = 'Khufiya_Vibhag_Payment_Report_'.date("Y-m-d",time()).'_'.time().'.csv';
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
    if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filepath));
      readfile($filepath);
      exit();
    }
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
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=".basename($file_path));
		header("Cache-Control: max-age=0");
		
		$objWriter->save("php://output");
		flush();
		exit();
	}
}
