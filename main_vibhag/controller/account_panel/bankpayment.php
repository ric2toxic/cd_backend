<?php
class ControllerAccountPanelBankpayment extends Controller{    
    private $error = array();

    /**
     * Method to show Account Panel -> Bank Payment Menu
     * Used to show Bank Payment Entries and its details
     * Author: Murtaza
     */

    public function index() {
        $user_id = $this->user->getId();

        $this->load->model('account_panel/bankpayment');
        
        $data = array();// Initializing the data array to be passed on to template f
        $this->load->autoLoadLanguage('wsb_purchase/analysis',$data);
        //$this->document->setTitle($this->language->get('heading_title'));     
        $this->document->setTitle('Bank Payment');


        //filtering
        if (isset($this->request->get['filter_ref'])) {
            $filter_ref = $this->request->get['filter_ref'];
        } else {
            $filter_ref = null;
        }
        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = null;
        }
        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = null;
        }
        if (isset($this->request->get['filter_order'])) {
            $filter_order = $this->request->get['filter_order'];
        } else {
            $filter_order = "DESC";
        }

        if (isset($this->request->get['filter_amount_from'])) {
            $filter_amount_from = $this->request->get['filter_amount_from'];
        } else {
            $filter_amount_from = null;
        }

        if (isset($this->request->get['filter_amount_to'])) {
            $filter_amount_to = $this->request->get['filter_amount_to'];
        } else {
            $filter_amount_to = null;
        }

        if (isset($this->request->get['filter_bank'])) {
            $filter_bank = $this->request->get['filter_bank'];
        } else {
            $filter_bank = null;
        }

        if (isset($this->request->get['filter_outflow'])) {
            $filter_outflow = $this->request->get['filter_outflow'];
        } else {
            $filter_outflow = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            //$filter_confirm = "101";
            $filter_status = "0";
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        // if (isset($this->request->get['limit'])) {
        //     $limit = $this->request->get['limit'];
        // } else {
        //     $limit = $this->config->get('config_limit_admin');
        // }

        if (isset($this->request->get['filter_page_limit'])) {
            $filter_page_limit = $this->request->get['filter_page_limit'];
        } else {
            $filter_page_limit = $this->config->get('config_limit_admin');
        }



        $url = '';

        if (isset($this->request->get['filter_ref'])) {
            $url .= '&filter_ref=' .$this->request->get['filter_ref'];
        }
        if(!empty($this->request->get['filter_date_from'])){
            //$sql .= " AND invoice_date >= '".$this->db->escape($filters['filter_date_from'])."' ";
            $url .= '&filter_date_from=' .$this->request->get['filter_date_from'];
        }
        if(!empty($this->request->get['filter_date_to'])){
            //$sql .= " AND invoice_date <= '".$this->db->escape($filters['filter_date_to'])."' ";
            $url .= '&filter_date_to=' .$this->request->get['filter_date_to'];
        }
        if (isset($this->request->get['filter_order'])) {
            //$url .= $this->request->get['filter_order'];
            $url .= '&filter_order=' .$this->request->get['filter_order'];
        }
        if (isset($this->request->get['filter_amount_from'])) {
            $url .= '&filter_amount_from=' .$this->request->get['filter_amount_from'];
        }
        if (isset($this->request->get['filter_amount_to'])) {
            $url .= '&filter_amount_to=' .$this->request->get['filter_amount_to'];
        }
        if (isset($this->request->get['filter_bank'])) {
            $url .= '&filter_bank=' .$this->request->get['filter_bank'];
        }
        if (isset($this->request->get['filter_outflow'])) {
            $url .= '&filter_outflow=' .$this->request->get['filter_outflow'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' .$this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_page_limit'])) {
            $url .= '&filter_page_limit=' . $this->request->get['filter_page_limit'];
        }

        $filter_data = array(
            'filter_ref'        => $filter_ref,
            'filter_order'      => $filter_order,
            'filter_date_from'  => $filter_date_from,
            'filter_date_to'    => $filter_date_to,
            'filter_amount_from'    => $filter_amount_from,
            'filter_amount_to'    => $filter_amount_to,
            'filter_bank'    => $filter_bank,
            'filter_outflow'    => $filter_outflow,
            'filter_status'    => $filter_status,
            // 'sort'           => $sort,
            // 'order'          => $order,
            'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
            //'limit'             => $this->config->get('config_limit_admin')
            'limit'             => $filter_page_limit
        );

        //$results = $this->model_account_panel_bankpayment->getBankPayments();
        $results = $this->model_account_panel_bankpayment->getBankPayments($filter_data);
        $paymentCount = $this->model_account_panel_bankpayment->getPaymentCount($filter_data);

        $data['payments'] = array();
        foreach($results as $keys => $values){
            $data['payments'][] = array(
                                    'payment_id' => $values['payment_id'],
                                    'dated' => $values['dated'],
                                    'ledger_id' => $values['ledger_id'],
                                    'ledger_name' => $values['ledger_name'],
                                    'amount' => $values['amount'],
                                    'mode' => $values['mode'],
                                    'reference' => $values['reference'],            
                                    'narration' => $values['narration'],
                                    'imgg' => $values['imgg'],
                                    'confirm1' => $values['confirm1'],
                                    'confirm2' => $values['confirm2'],
                                    'confirm3' => $values['confirm3'],
                                    'ledger_id2' => $values['ledger_id2'],
                                    'group_id' => $values['group_id'],
                                    'csv_import2' => $this->url->link('account_panel/bankpayment/addAjaxBankPaymentSub','token='.$this->session->data['token'].'&payment_id='.$values['payment_id'], 'SSL'),
                                    );         
        }

        
        $groups = $this->model_account_panel_bankpayment->getGroups();
        $data['groups'] = $groups;

        $ledgers = $this->model_account_panel_bankpayment->getLedgers();
        $data['ledgers'] = $ledgers;

        $banks = $this->model_account_panel_bankpayment->getBanks();
        $data['banks'] = $banks;

        $doneCount = $this->model_account_panel_bankpayment->getDoneCount();
        $pendingCount = $this->model_account_panel_bankpayment->getPendingCount();
        $data['doneCount'] = $doneCount;
        $data['pendingCount'] = $pendingCount;

        $data['user_id'] = $user_id;
   

        // Autoloading the lanugage
        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link('wsb_purchase/import', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        
        $data['add'] = $this->url->link('wsb_import/import/import', 'token=' . $this->session->data['token'] . $url, 'SSL');

         if (isset($this->session->data['error'])) {
             $data['error_warning'] = $this->session->data['error'];

             unset($this->session->data['error']);
         } elseif (isset($this->error['warning'])) {
             $data['error_warning'] = $this->error['warning'];
         } else {
             $data['error_warning'] = '';
         }

         if (isset($this->session->data['success'])) {
             $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
         } else {
             $data['success'] = '';
         }


        // URL for pagination
        $pagination_url = $url;

        $pagination = new Pagination();
        //$pagination->total = $product_total;
        $pagination->total = $paymentCount;
        $pagination->page = $page;
        //$pagination->limit = $this->config->get('config_limit_admin');
        $pagination->limit = $filter_page_limit;
        //$pagination->limit = 2;
        $pagination->url = $this->url->link('account_panel/bankpayment', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();
/*
        $data['results'] = sprintf($data['text_pagination'],
                                    ($paymentCount) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
                                    ((($page - 1) * $this->config->get('config_limit_admin')) > ($paymentCount - $this->config->get('config_limit_admin'))) ? $paymentCount : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
                                        $paymentCount, ceil($paymentCount / $this->config->get('config_limit_admin')));
*/
        $data['results'] = sprintf($data['text_pagination'],
                                    ($paymentCount) ? (($page - 1) * $filter_page_limit) + 1 : 0,
                                    ((($page - 1) * $filter_page_limit) > ($paymentCount - $filter_page_limit)) ? $paymentCount : ((($page - 1) * $filter_page_limit) + $filter_page_limit),
                                        $paymentCount, ceil($paymentCount / $filter_page_limit));

        $data['filter_ref'] = $filter_ref;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        $data['filter_order'] = $filter_order;
        $data['filter_amount_from'] = $filter_amount_from;
        $data['filter_amount_to'] = $filter_amount_to;
        $data['filter_bank'] = $filter_bank;
        $data['filter_outflow'] = $filter_outflow;
        $data['filter_status'] = $filter_status;
        $data['filter_page_limit'] = $filter_page_limit;
        $data['page_limit_array'] = array('30','60','100','200','500','1000');
        


        $data['csv_export'] = $this->url->link('account_panel/bankpayment/exportcsv', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['csv_import'] = $this->url->link('account_panel/bankpayment/importcsv', 'token=' . $this->session->data['token'] . $url, 'SSL');
        //$data['deleteAll'] = $this->url->link('account_panel/bankpayment/deleteAll', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['token'] = $this->session->data['token'];
        $data['route'] = $this->request->get['route'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account_panel/bankpayment.tpl', $data));
        
    }   
    
    public function importcsv() {

        $response = array();
        $this->load->model('account_panel/bankpayment');

            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName()['username'];
            $datedCM = date("Y-m-d H:i:s");
            $user_array = array(
                                    'user_id' => $user_id,
                                    'user_name' => $user_name,
                                    'date' => $datedCM
                                  );
            $row = 0;
            $bankGroupID = 8;

            //////// 1. Checking CSV Default Code - start /////////
            if(empty($this->request->files['fileToUpload']['name'])){
                //$this->response->redirect($this->url->link('account_panel/bankpayment', "", 'SSL'));
                $response['status'] = 22;
                echo json_encode( $response );
                exit();                 
            }
            // Sanitize the filename
            $filename = basename(html_entity_decode($this->request->files['fileToUpload']['name'], ENT_QUOTES, 'UTF-8'));

            // Validate the filename length
            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
                $this->error['warning']['filename']  = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array('csv',
                             'xls',
                             'xlsx');

            if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
                $this->error['warning']['filetype'] = $this->language->get('error_filetype');

            }
            // Allowed file mime types
            $allowed = array('application/vnd.ms-excel',
                             'text/plain',
                             'text/csv',
                             'text/tsv');

            if (!in_array($this->request->files['fileToUpload']['type'], $allowed)) {
                $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
                // $json['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['fileToUpload']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
            }

            // Return any upload error
            if ($this->request->files['fileToUpload']['error'] != UPLOAD_ERR_OK) {
                $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUpload']['error']);
            }
            if (!$this->error) {
                $file_handle = fopen($this->request->files['fileToUpload']['tmp_name'], 'r');
                while (!feof($file_handle) ) {
                    $line_of_text[] = fgetcsv($file_handle, 1024);
                }
                fclose($file_handle);
                
                ////////////condition of column for CSV and Bank Payment CSV Only Import//////
                $getColumnsBankPaymentCSV = array_values($line_of_text)[0];
                //$countColumnsBankPaymentCSV = count($getColumnsBankPaymentCSV);

                if ($getColumnsBankPaymentCSV[0]=='Date' && $getColumnsBankPaymentCSV[1]=='Bank' && $getColumnsBankPaymentCSV[2]=='Amount' && $getColumnsBankPaymentCSV[3]=='Mode' && $getColumnsBankPaymentCSV[4]=='Reference' && $this->request->files['fileToUpload']['name'] =='bankpayment.csv')
                {
                    //  // // // // // 5. Conditions Others- Start// // // // // //
                   // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                    $dated_arr = array_column($line_of_text,0);
                    unset($dated_arr[0]);
                    $checkDated_arr = 0;
                    foreach ( $dated_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkDated_arr = 1;
                            $response['status'] = 2;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                             
                        }
                    }
                    $row = 0;
                    // // // // // // 5(b). Check Bank Ledger Name not blank --- Start --- // // // // // // //
                    $ledgerName_arr = array_column($line_of_text,1);
                    unset($ledgerName_arr[0]);
                    $checkLedgerName_arr = 0;
                    foreach ( $ledgerName_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkLedgerName_arr = 1;
                            $response['status'] = 3;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();
                        }
                    }
                    $row = 0;
                    // // // // // // 5(c). Check Amount Is_Numeric and positive --- Start --- // // // // // // //
                    $amount_arr = array_column($line_of_text,2);
                    unset($amount_arr[0]);
                    $checkAmount_arr = 0;
                    foreach ( $amount_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkAmount_arr = 1;
                            $response['status'] = 4;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();
                        }
                        if($value <= 0) {
                            $checkAmount_arr = 1;
                            $response['status'] = 5;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();
                        }
                    }
                    $row = 0;
                    // // // // // // 5(d). Check Mode not blank --- Start --- // // // // // // //
                    $mod_arr = array_column($line_of_text,3);
                    unset($mod_arr[0]);
                    $checkMod_arr = 0;
                    foreach ( $mod_arr as $value ){
                        if( strlen($value) < 1 ) {
                            //$checkMod_arr = 1;
                            //$response['status'] = 6;
                            //echo json_encode( $response );
                            //exit();
                        }
                    }
                    // // // // // // 5(d). Check Ref No. not blank --- Start --- // // // // // // //
                    $ref_arr = array_column($line_of_text,4);
                    unset($ref_arr[0]);
                    $checkRef_arr = 0;
                    foreach ($ref_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkRef_arr = 1;
                            $response['status'] = 7;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                         
                        }
                        if( strlen($value) > 0 ) {
                            $checkPaymentForRef = $this->model_account_panel_bankpayment->getPaymentForRef($value);                            
                            if ( $checkPaymentForRef ) {
                                $checkRef_arr = 1;
                                $response['status'] =  77;
                                $response['row'] =  $row+1;
                                echo json_encode( $response );
                                exit();                                
                            }
                        }
                    }

                    // // // // // // 5(d1). Check Ref No. not blank on CSV (Frontend) --- Start --- // // // // // // //
                    if(count(array_unique($ref_arr))<count($ref_arr))
                    {
                        // Array has duplicates
                        $checkRef_arr = 1;
                        $response['status'] =  777;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                              
                    }
                    //  // // // // // 5. Conditions Others- End// // // // // //

                    if ($checkDated_arr !=1 && $checkLedgerName_arr !=1 && $checkAmount_arr !=1 && $checkMod_arr !=1 && $checkRef_arr !=1){
                        $i = 0;
                        foreach($line_of_text as $cav_data){
                            if (empty($cav_data['0']) || $i ==0) {
                                $i++;
                                continue;
                            }

                            $dated = $cav_data['0'];
                            $ledgerName        = $cav_data['1'];
                            $amount  = $cav_data['2'];
                            $mode  = $cav_data['3'];
                            $reference  = $cav_data['4'];

                            $datedx = "";
                            if( strlen( $dated ) > 1 ) {
                                $datedx = $dated;
                                $datedx = date("Y-m-d", strtotime($datedx));
                            }
                            
                            $ledgerNameInLedgerTable = $this->model_account_panel_bankpayment->getLedgerNameInLedgerTable($ledgerName);

                            if ( $ledgerNameInLedgerTable == 0 ){
                                $ledger_id_new = $this->model_account_panel_bankpayment->saveLedger( $ledgerName, $bankGroupID, 0, $user_id, $datedCM, $user_array);
                                $sql = $this->model_account_panel_bankpayment->InsertBankPayments($datedx, $ledger_id_new, $amount, $mode, $reference, $user_id, $datedCM, $user_array);
                            }
                            else
                            {
                                //for old Ledgers Case
                                $ledger_id_old = $this->model_account_panel_bankpayment->getLedgerIDInLedgerTable($ledgerName);
                                $sql = $this->model_account_panel_bankpayment->InsertBankPayments($datedx, $ledger_id_old, $amount, $mode, $reference, $user_id, $datedCM, $user_array);
                            }
                            $response['status'] =  53;
                        }  
                    }
                }
                else
                {
                    $response['status'] = 8;
                    echo json_encode( $response );
                    exit();
                } 
            }
            echo json_encode( $response );
    }

    public function importSalaryCSV() {

        $response = array();

        $this->load->model('account_panel/bankpayment');

        $row = 0;

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        $salaryLedgerID = 14;
        $expensesGroupID = 12;
        $employeesGroupID = 6;

        /////////// Entries of only Employees Salary Group ///////////////////////////////

		//////// 1. Checking of CSV Default Code - start /////////
		if(empty($this->request->files['fileToUploadSalary']['name'])){
                //$this->response->redirect($this->url->link('account_panel/bankpayment', "", 'SSL'));
			$response['status'] = 1;
			echo json_encode( $response );
			exit();                  
		}
		// Sanitize the filename
		$filename = basename(html_entity_decode($this->request->files['fileToUploadSalary']['name'], ENT_QUOTES, 'UTF-8'));

		// Validate the filename length
		if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
			$this->error['warning']['filename']  = $this->language->get('error_filename');
		}

		// Allowed file extension types
		$allowed = array('csv',
                         'xls',
                         'xlsx');

		if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
			$this->error['warning']['filetype'] = $this->language->get('error_filetype');
		}
		// Allowed file mime types
		$allowed = array('application/vnd.ms-excel',
                         'text/plain',
                         'text/csv',
                         'text/tsv');

		if (!in_array($this->request->files['fileToUploadSalary']['type'], $allowed)) {
			$this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
			// $json['error'] = $this->language->get('error_filetype');
		}

		// Check to see if any PHP files are trying to be uploaded
		$content = file_get_contents($this->request->files['fileToUploadSalary']['tmp_name']);

		if (preg_match('/\<\?php/i', $content)) {
			$this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
		}

		// Return any upload error
		if ($this->request->files['fileToUploadSalary']['error'] != UPLOAD_ERR_OK) {
			$this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUploadSalary']['error']);
		}

		////////1. Checking of CSV Default Code - End /////////

		//if no error in csv
		if (!$this->error) {
			///////2. Now CSV Read here./////////
			$file_handle = fopen($this->request->files['fileToUploadSalary']['tmp_name'], 'r');
			while (!feof($file_handle) ) {
				$line_of_text[] = fgetcsv($file_handle, 1024);
			}
			fclose($file_handle);

			////////////3. Condition of column of CSV and Salary CSV Import Only//////
			$getColumnsSalaryCSV = array_values($line_of_text)[0];
			//$countColumnsSalaryCSV = count($getColumnsSalaryCSV);

			if ($getColumnsSalaryCSV[0]=='Reference' && $getColumnsSalaryCSV[1]=='Emp Code' && $this->request->files['fileToUploadSalary']['name'] =='salary_CSV.csv')
			{
				//  // // // // // 4. Conditions Others- Start// // // // // //

				// // // 4(a). Check Ref No.  // // // // //
				$ref_arr = array_column($line_of_text,0);
				unset($ref_arr[0]);
				$checkRef_arr = 0;
				foreach ($ref_arr as $value ){
					$row++;
					if( strlen($value) < 1 ) {
						$checkRef_arr = 1;
						$response['status'] = 2;
						$response['row'] =  $row+1;
						echo json_encode( $response );
						exit();                         
					}
					//Ref No. should be in Payment Table.
					if( strlen($value) > 0 ) {
						$checkRefEntryInPayment = $this->model_account_panel_bankpayment->getPaymentForRef2($value);
						if ( !$checkRefEntryInPayment ) {
							$checkEmp_code_arr = 1;
							$response['status'] =  22;
							$response['row'] =  $row+1;
							echo json_encode( $response );
							exit();                                
						}
					}
					//Ref No. should not be in Payment Sub Table.
					if( strlen($value) > 0 ) {
						$checkRefEntryInPaymentSub = $this->model_account_panel_bankpayment->getPaymentSubForRef($value);
						if ( $checkRefEntryInPaymentSub ) {
							$checkEmp_code_arr = 1;
							$response['status'] =  23;
							$response['row'] =  $row+1;
							echo json_encode( $response );
							exit();                                
						}
					}
				}

                //Ref No. should not be duplicate on CSV
                if(count(array_unique($ref_arr))<count($ref_arr))
                {
                    // Array has duplicates
                    $checkRef_arr = 1;
                    $response['status'] =  222;
                    $response['row'] =  $row+1;
                    echo json_encode( $response );
                    exit();                              
                }

				$row = 0;
				// // // // // // 4(b). Check Emp Code --- Start --- // // // // // // //
				$emp_code_arr = array_column($line_of_text,1);
				unset($emp_code_arr[0]);
				$checkEmp_code_arr = 0;
				foreach ($emp_code_arr as $value ){
					$row++;
					if( strlen($value) < 1 ) {
						$checkEmp_code_arr = 1;
						$response['status'] = 3;
						$response['row'] =  $row+1;
						echo json_encode( $response );
						exit();                         
					}
					/*
					if( strlen($value) > 0 ) {
						$checkEmp_CodeInLedger = $this->model_account_panel_bankpayment->getEmp_CodeFromLedger($value);	
						if ( !$checkEmp_CodeInLedger ) {
							$checkEmp_code_arr = 1;
							$response['status'] =  333;
							$response['row'] =  $row+1;
							echo json_encode( $response );
							exit();                                
						}
					}
					*/
				}

				//  // // // // // 4. Conditions Others- End// // // // // //

				if ($checkRef_arr !=1 && $checkEmp_code_arr !=1){

				//////5. Insertion Updation in Payment Sub, Payment Sub CSV tables)-- Start --- /////  
					$i = 0;
					foreach($line_of_text as $cav_data){
						if (empty($cav_data['0']) || $i ==0) {
							$i++;
							continue;
						}

						$ref            = $cav_data['0'];
						$emp_code       = $cav_data['1'];
						$ledgerName     = $emp_code;

						////5(a). Insertion Updation in Payment -- Start --- /////
						$file_path = "";
						$getPaymentData = $this->model_account_panel_bankpayment->getPaymentData($ref);
						$payment_id = $getPaymentData['payment_id'];
						$ledger_id = $salaryLedgerID;
						$group_id = $expensesGroupID;
						$amountOfPayment = $getPaymentData['amount'];
						$dated = $getPaymentData['dated'];

						$this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledger_id, $group_id, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );

						$payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);

						$ledgerNameInLedgerTable = $this->model_account_panel_bankpayment->getEmp_CodeFromLedger($emp_code);

                        if ( $ledgerNameInLedgerTable == 0 ){
							$ledger_id_new = $this->model_account_panel_bankpayment->saveLedgerForEmp( $ledgerName, $employeesGroupID, $emp_code, $user_id, $datedCM, $user_array);
							$sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $dated, $ledger_id_new, $amountOfPayment, '', $ref, 0, 0, 0, 
                                'not_applicable');
						}
						else
						{
							//Old Ledgers Case
							$ledger_id_old = $this->model_account_panel_bankpayment->getLedgerIDInLedgerTable($ledgerName);
							$sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $dated, $ledger_id_old, $amountOfPayment, '', $ref, 0, 0, 0, 
                                'not_applicable');
						}

						$response['status'] =  51;
					}
				}
			}
			else
			{
				$response['status'] =  11;
			}
		}
        echo json_encode( $response );
    }

    public function exportcsv(){
        $file = DIR_DOWNLOAD.'bankpayment.csv';
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-disposition: attachment; filename=' . basename($file));
        header('Expires: 0');
        header('Cache-Control: no-cache');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit();
    }    

    public function editPaymentForConfirm(){
        $this->load->model('account_panel/bankpayment');

        $row_id = $_POST['row_id'];
        $confirm = $_POST['confirm'];

        $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $row_id);

        $status = 1;
        if (isset($checkPaymentSubForPaymentID) && !empty($checkPaymentSubForPaymentID))
        {
            $checkPaymentForConfirm = $this->model_account_panel_bankpayment->getPaymentForConfirm( $row_id);
            if ($confirm == "confirm2" )
            {
                if ($checkPaymentForConfirm['confirm1'] == 0)
                {
                    $status = 2;
                    echo json_encode( $status );
                }
                else
                {
                    //correct
                    $result = $this->model_account_panel_bankpayment->updatePaymentForConfirm($row_id, $confirm);
                    $status = 11;
                    echo json_encode( $status );
                }                
            }
            else if ($confirm == "confirm3" )
            {
                if ($checkPaymentForConfirm['confirm1'] == 0 || $checkPaymentForConfirm['confirm2'] == 0)
                {
                    $status = 2;
                    echo json_encode( $status );
                }
                else
                {
                    //correct
                    $result = $this->model_account_panel_bankpayment->updatePaymentForConfirm($row_id, $confirm);
                    $status = 11;
                    echo json_encode( $status );
                }                
            }
            else
            {
                //correct
                $result = $this->model_account_panel_bankpayment->updatePaymentForConfirm($row_id, $confirm);
                $status = 11;
                echo json_encode( $status );
            }            


        }
        else
        {
            $status = 1;
            echo json_encode( $status );            
        }
    }

    public function getLedgersOfGroup(){

        $this->load->model('account_panel/bankpayment');
        $response = array();

        $group_id = $_POST['group_id'];
        $payment_id = $_POST['payment_id'];
        
        $getLedgerOfGroupID = $this->model_account_panel_bankpayment->getLedgersOfGroupID($group_id);
        $response['getLedgerOfGroupID'] =  $getLedgerOfGroupID ;
        $response['payment_id'] =  $payment_id ;        

        if (!$getLedgerOfGroupID)
        {
            $response['status'] =  2 ;
        }
        else
        {
            $response['status'] =  1 ;
        }

        //echo "<pre>"; print_r($response['result']);die;
        // $this->response->addHeader('Content-Type: application/json');
        // $this->response->setOutput(json_encode($response));
        echo json_encode($response) ;   
    }       

    public function addAjaxBankPaymentSub() {

        $response = array();
        $checkedError = 0;

        $this->load->model('account_panel/bankpayment');
        ////1. Get All Values -- Start --- //////////////////
        $ledgerid = $this->request->post['ledgeridAA'];
        $groupid = $this->request->post['groupidAA'];
        $payment_id = $_REQUEST['payment_id'];
        //$amountOfPayment = $this->model_account_panel_bankpayment->getAmountOfPayment($payment_id);
        $amountOfPayment = $_REQUEST['amountAjax'];
        $amountOfPayment = (float)$amountOfPayment;
        $refAA = $this->request->post['refAA'];// it is used in CA

        $row = 0;

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        $paymentgatewayGroupID = 2;

        $citrusLedgerID = 4;
        $paytmLedgerID = 5;
        $razorpayLedgerID = 6;

        $creditAgencyGroupID = 21;
        //$neogrowthLedgerID = 424; //local
        $neogrowthLedgerID = 5579; //live

        $suspenseGroupID = 5;
        $bankGroupID = 8;
        $fixedAssetsGroupID = 9;
        $sundryDebtorsGroupID = 10;
        $sundryCreditorsGroupID = 11;        
        $expensesGroupID = 12;
        $incomesGroupID = 13;
        $investmentGroupID = 14;
        $advancesGroupID = 15;
        $dutiesTaxesGroupID = 16;
        $salaryLedgerID = 14;
        $cashGroupID = 18;
        $sundryDebtorsInvGroupID = 19;
        $securityDepositGroupID = 20;
        $bankingGroupID = 25;

        /////////// Buyer Refund means Payment Gateway ///////////////////////////////
        if ($groupid == $paymentgatewayGroupID || $groupid == $creditAgencyGroupID ){

            //////// 2. Checking of CSV Default Code - start /////////
            if(empty($this->request->files['fileToUpload3']['name'])){
                $checkedError = 1;
                $response['status'] = 22;
                echo json_encode( $response );
                exit();                  
            }
            // Sanitize the filename
            $filename = basename(html_entity_decode($this->request->files['fileToUpload3']['name'], ENT_QUOTES, 'UTF-8'));

            // Validate the filename length
            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
                $this->error['warning']['filename']  = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array('csv',
                             'xls',
                             'xlsx');

            if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
                $this->error['warning']['filetype'] = $this->language->get('error_filetype');

            }
            // Allowed file mime types
            $allowed = array('application/vnd.ms-excel',
                             'text/plain',
                             'text/csv',
                             'text/tsv');

            if (!in_array($this->request->files['fileToUpload3']['type'], $allowed)) {
                $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
                // $json['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['fileToUpload3']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
            }

            // Return any upload error
            if ($this->request->files['fileToUpload3']['error'] != UPLOAD_ERR_OK) {
                $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUpload3']['error']);
            }
            ////////2. Checking of CSV Default Code - End /////////

            //if no error in csv
            if (!$this->error) {
                ///////3. Now CSV Read here./////////
                $file_handle = fopen($this->request->files['fileToUpload3']['tmp_name'], 'r');
                while (!feof($file_handle) ) {
                    $line_of_text[] = fgetcsv($file_handle, 1024);
                }
                fclose($file_handle);

                ////////////4. Condition of column of CSV and Buyers Refund CSV Import Only//////
                $getColumnsBuyersRefundCSV = array_values($line_of_text)[0];
                //$countColumnsBuyersRefundCSV = count($getColumnsBuyersRefundCSV);

                if ($getColumnsBuyersRefundCSV[0]=='Date' && $getColumnsBuyersRefundCSV[1]=='Order No' && $getColumnsBuyersRefundCSV[2]=='Ref' && $getColumnsBuyersRefundCSV[3]=='Amount' && $this->request->files['fileToUpload3']['name'] =='Buyers_Refund_CSV.csv')
                {
                    //  // // // // // 5. Conditions Others- Start// // // // // //

                   // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                    $dated_arr = array_column($line_of_text,0);
                    unset($dated_arr[0]);
                    foreach ( $dated_arr as $value ){
                        $row++;
                        if( strlen($value) < 1 ) {
                            $checkedError = 1;
                            $response['status'] =  2;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                            
                        }
                    }
                    $row = 0;
                    $row2 = 0;
                    // // // // // // 5(b). Check Order No Is_Numeric --- Start --- // // // // // // //
                    $orderNo_arr = array_column($line_of_text,1);
                    unset($orderNo_arr[0]);
                    foreach ( $orderNo_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkedError = 1;
                            $response['status'] =  3;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                            
                        }
                        $row2++;
                        if( strlen($value) > 1 ) {
                            $CustomerName = $this->model_account_panel_bankpayment->getCustomerNameForBR($value);
                            
                            if ( !$CustomerName ) {
                                $checkedError = 1;
                                $response['status'] =  5;
                                $response['row'] =  $row2+1;
                                echo json_encode( $response );
                                exit();                                
                            }
                        } 
                    }

                    $row = 0;
                    // // // 5(c). Check Ref No. not blank and oc_suborder oc_order // // // // //
                    $payment_gateway = "";
                    $ledgerForCharges = "";
                    if ($ledgerid == $citrusLedgerID)
                    {
                        $payment_gateway = "citrus";
                    }
                    else if ($ledgerid == $paytmLedgerID)
                    {
                        $payment_gateway = "paytm";
                    }
                    else if ($ledgerid == $razorpayLedgerID)
                    {
                        $payment_gateway = "razorpay";
                    }
                    else if ($ledgerid == $neogrowthLedgerID)
                    {
                        $payment_gateway = "neogrowth";
                    }
                    
                    $ref_arr = array_column($line_of_text,2);
                    unset($ref_arr[0]);
                    if ($groupid == $paymentgatewayGroupID)
                    {
                        foreach ( $ref_arr as $value ){
                            $row++;
                            if( strlen($value) < 1 ) {
                                $checkedError = 1;
                                $response['status'] =  4;
                                $response['row'] =  $row+1;
                                echo json_encode( $response );
                                exit();                            
                            }
                            /*
                            if( strlen($value) > 1 ) {
                                $CustomerName = $this->model_account_panel_bankpayment->getCustomerNameForPG($value, $payment_gateway, 1);
                                if ( !$CustomerName ) {
                                    $checkRef_arr = 1;
                                    $response['status'] =  5;
                                    $response['row'] =  $row+1;
                                    echo json_encode( $response );
                                    exit();                                
                                }
                            }
                            */
                        }
                    }
                    else
                    {
                        foreach ( $ref_arr as $value ){
                            $row++;
                            if( strlen($value) > 1 ) {
                                $checkedError = 1;
                                $response['status'] =  44;
                                $response['row'] =  $row+1;
                                echo json_encode( $response );
                                exit();                            
                            }
                        }
                    }

                    $row = 0;
                    $row2 = 0;
                    // // // // // // 5(d). Check Amount Is_Numeric and positive --- Start --- // // // // // // //
                    $amount_arr = array_column($line_of_text,3);
                    unset($amount_arr[0]);
                    foreach ( $amount_arr as $value ){
                        $row++;
                        if( !is_numeric(trim($value)) ) {
                            $checkedError = 1;
                            $response['status'] =  6;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                            
                        }
                        $row2++;
                        if($value <= 0) {
                            $checkedError = 1;
                            $response['status'] =  7;
                            $response['row'] =  $row2+1;
                            echo json_encode( $response );
                            exit();                            
                        }
                    }
                    //  // // // // // 5(e). Total Amt. of CSV (check vd amountOfPayment Value) --- Start --- // // // // // //
                    $amountTotal_arr = array_column($line_of_text,3);
                    unset($amountTotal_arr[0]);
                    $amountTtl_arr = array_sum($amountTotal_arr);
                    $amountTtl_arr = (float)$amountTtl_arr;

                    if(round($amountOfPayment,2) !== round($amountTtl_arr,2)) {
                        $checkedError = 1;
                        $response['status'] =  8;
                        echo json_encode( $response );
                        exit();
                    }                    
                    //  // // // // // 5. Conditions Others- End// // // // // //

                    if ($checkedError !=1 && $ledgerid > 0 && strlen($refAA) > 0){

                        ////6(a). Insertion Updation in Payment Sub (Not payment_sub_csv table)-- Start --- /////
                        $file_path = "";
                        $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                        if ( !$checkPaymentSubForPaymentID ) {
                            $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                        } else {
                            $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                            $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                            $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                            //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                        }

                        $payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);

                        //////6(b). Insertion Updation in Payment Sub CSV table)-- Start --- /////  
                        $i = 0;
                        foreach($line_of_text as $cav_data){
                            if (empty($cav_data['0']) || $i ==0) {
                                $i++;
                                continue;
                            }

                            $dated          = $cav_data['0'];
                            $order_no       = $cav_data['1'];
                            $ref            = $cav_data['2'];//merchant_txn_id
                            $amount         = $cav_data['3'];

                            $datedx = "";
                            if( strlen( $dated ) > 1 ) {
                                $datedx = $dated;
                                $datedx = date("Y-m-d", strtotime($datedx));
                            }

                            if ($groupid == $creditAgencyGroupID ){
                                $ref = $refAA;
                            }
                            
                            $CustomerName = $this->model_account_panel_bankpayment->getCustomerNameForBR($order_no);

                            $this->load->model('accounts/salesreports');
                            $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                            $order_id = $CustomerName['order_id'];

                            $ledgerNameInLedgerTable = $this->model_account_panel_bankpayment->getLedgerNameInLedgerTable($ledgerName);

                            if ( $ledgerNameInLedgerTable == 0 ){

                                $ledger_id_new = $this->model_account_panel_bankpayment->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);

                                $gst_number = $CustomerName['gst_number'] ?? 'N/A';   
                                
                                $this->model_account_panel_bankpayment->updateCustomerLedger( $ledger_id_new, $CustomerName['customer_id']);

                                $pmntID = $this->model_account_panel_bankpayment->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', '', -abs($amount), $datedx, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'payment', $payment_id, $payment_sub_id);

                                $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedx, $ledger_id_new, $amount, $order_no, $ref, $pmntID, $order_id, 0, 'not_applicable');
                            }
                            else
                            {
                                //Old Ledgers Case
                                $ledger_id_old = $this->model_account_panel_bankpayment->getLedgerIDInLedgerTable($ledgerName);

                                $pmntID = $this->model_account_panel_bankpayment->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', '', -abs($amount), $datedx, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'payment', $payment_id, $payment_sub_id);                                    
                                $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedx, $ledger_id_old, $amount, $order_no, $ref, $pmntID, $order_id, 0, 'not_applicable');
                            }
                            $response['status'] =  51;
                        }
                    }
                }
                else
                {
                    $response['status'] =  1;
                }
            }
        }
        //Fixed Assets and Expenses (Image compulsory)
        else if ($groupid == $fixedAssetsGroupID || $groupid == $expensesGroupID)
        {
            //Image 
            $today = Date('d_M_Y');
                if (!file_exists(DIR_UPLOAD.'paymentx/'.$today)) {
                    mkdir(DIR_UPLOAD.'paymentx/'.$today, 0777, true);
                }
            $file_path = "";
            if($this->request->files['fileToUpload3']['error'] == 0){
                $file_name = $this->request->files['fileToUpload3']['name'];
                $file_temp_name = $this->request->files['fileToUpload3']['tmp_name'];
                $file_path = DIR_UPLOAD.'paymentx/'.$today.'/'.$file_name;
                move_uploaded_file($file_temp_name,$file_path);         
            }
            /////2. Conditions - Start ---/////////////////////
            $checkFile_path = 0;
            if ($ledgerid != $salaryLedgerID)
            {
                if ($file_path == "" && strlen($file_path) == 0 ){
                    $checkFile_path = 1;
                    $response['status'] =  21;
                }
            }
            /////2. Conditions - End ---/////////////////////
            if ($checkFile_path !=1){
                $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                $file_path = substr($file_path,36);
                if ( !$checkPaymentSubForPaymentID ) {
                    $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                } else {
                    $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                    $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                    $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                    //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                    $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                    $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                }
                $response['status'] =  52;
            }
        } 
        else if ($groupid == $cashGroupID || $groupid == $sundryCreditorsGroupID || $groupid == $investmentGroupID || $groupid == $bankGroupID || $groupid == $suspenseGroupID || $groupid == $dutiesTaxesGroupID || $groupid == $sundryDebtorsInvGroupID || $groupid == $incomesGroupID || $groupid == $securityDepositGroupID || $groupid == $advancesGroupID || $groupid == $bankingGroupID)
        {
            $file_path = "";
            $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
            if ( !$checkPaymentSubForPaymentID ) {
                $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
            } else {
                $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
            }
            $response['status'] =  53;
        }
        else
        {
            $response['status'] = 99;
            echo json_encode( $response );
            exit();
        }
        echo json_encode( $response );
    }//end function
    
    public function getOcOrderDetail() {
		
		//check order No. from oc_order table in buyer's refund Bank Payment
        $this->load->model('account_panel/bankpayment');
        $order_no = $this->request->post['order_no'];
        $amount = $this->request->post['amount'];
		
		//$CustomerName = $this->model_account_panel_bankpayment->getCustomerNameForPG($value, $payment_gateway, 1);
        $ocOrderDetails = $this->model_account_panel_bankpayment->getOcOrderDetail($order_no, $amount);
        echo "<pre>";print_r($ocOrderDetails);die;
        $response = array();
        if ( !$ocOrderDetails ){
            $response['norecord'] = 0;
            $response['ocOrderDetails'] =  '' ;
            echo json_encode($response) ; 
        }
        else
        {
            //echo "<pre>";print_r($ocOrderDetails);die;
            $response['norecord'] = 1;
            $response['ocOrderDetails'] =  $ocOrderDetails ;
            echo json_encode($response) ;            
        }
    } 
    public function addAjaxBuyersRefund() {      

        $response = array();

        $this->load->model('account_panel/bankpayment');    

        ////1. Get All Values -- Start --- //////////////////
        $order_no = $this->request->post['order_no'];
        $ledgerid = $this->request->post['ledgerid'];
        $groupid = $this->request->post['groupid'];
        $payment_id = $_REQUEST['payment_id'];
        $amountOfPayment = $_REQUEST['amountOfPayment'];
        $datedRO = $this->request->post['datedROAA'];
        $ref = $this->request->post['ref'];//merchant txn
        $payment_mode = $this->request->post['bank_name'];//bank name

        $mode = "BR";

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        $buyersRefundGroupID = 4;
        $sundryDebtorsGroupID = 10;

        //$ocOrderDetails = $this->model_account_panel_bankpayment->getOcOrderDetail($order_no, $amountOfPayment);
        $ocOrderDetails = $this->model_account_panel_bankpayment->getOcOrderDetailForBR($order_no, $amountOfPayment);
        //echo "<pre>";print_r($ocOrderDetails);die;
        $response = array();
        if ( !$ocOrderDetails ){
            $response['status'] =  1;
            echo json_encode($response) ;
            exit();
        }
        else
        {
            //echo "<pre>";print_r($ocOrderDetails);die;
        	if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
        	    $response['status'] =  1;
        	}
        	else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
        	    $response['status'] =  1;
        	}
        	else if ($payment_id == "" || strlen($payment_id) == 0 || !is_numeric($payment_id)) {
        	    $response['status'] =  1;
        	}
        	else if ($amountOfPayment == "" || strlen($amountOfPayment) == 0 || !is_numeric($amountOfPayment)) {
        	    $response['status'] =  1;
        	}
        	else if ($order_no == "" || strlen($order_no) == 0 || !is_numeric($order_no)) {
        	    $response['status'] =  1;
        	}
        	else
        	{
            	if ($groupid == $buyersRefundGroupID){
                	if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($payment_id) > 0 && is_numeric($payment_id) && strlen($amountOfPayment) > 0 && is_numeric($amountOfPayment) && strlen($order_no) > 0 && is_numeric($order_no))
                	{

                		//checking if buyers refund already done by this order_no
                		//not a gud condition bcos bank_ref always unique
                    	//$checkPaymentSubCSVByOrderNo = $this->model_account_panel_bankpayment->getPaymentSubCSVByOrderNo($ref,$amountOfPayment,$order_no); 
                    	//echo "<pre>";print_r($checkPaymentSubCSVByOrderNo);die;
                    	//if ( !$checkPaymentSubCSVByOrderNo ) {
        	            	$file_path = "";
            	            $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                	        if ( !$checkPaymentSubForPaymentID ) {
                    	        $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
	                        } else {

                                $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                                $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                                $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                                //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                                $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                                $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                    	    }
                            
                            $payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);
                            //$amountOfPayment = -abs($amountOfPayment);
                            $order_id = $ocOrderDetails['order_id'];

                            $this->load->model('accounts/salesreports');
                            $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($ocOrderDetails['customer_id']) ;
                            /*
                            $ledgerName = $ocOrderDetails['customer_id'];
                            $ledgerName .= (!empty(trim($ocOrderDetails['firstname'])) ? '_' . trim($ocOrderDetails['firstname']) : '');
                            $ledgerName .= (!empty(trim($ocOrderDetails['lastname'])) ? '_' . trim($ocOrderDetails['lastname']) : '');
                            $ledgerName .= (!empty(trim($ocOrderDetails['payment_company'])) ? '_' . trim($ocOrderDetails['payment_company']) : '');
                            */
                            $ledgerNameInLedgerTable = $this->model_account_panel_bankpayment->getLedgerNameInLedgerTable($ledgerName);
                            if ( $ledgerNameInLedgerTable == 0 ){
                                $ledger_id_new = $this->model_account_panel_bankpayment->saveLedger( $ledgerName, $sundryDebtorsGroupID, $ocOrderDetails['customer_id'], $user_id, $datedCM, $user_array);

                                //ask from mdhrSir
                                $gst_number = $ocOrderDetails['gst_number'] ?? 'N/A';   
                                
                                $this->model_account_panel_bankpayment->updateCustomerLedger( $ledger_id_new, $ocOrderDetails['customer_id']);

                                $pmntID = $this->model_account_panel_bankpayment->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', $payment_mode, -abs($amountOfPayment), $datedRO, date("Y-m-d H:i:s"), 'bank_transfer', 1, $user_id, 'payment', $payment_id, $payment_sub_id);
                                $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedRO, $ledger_id_new, $amountOfPayment, $order_no, $ref, $pmntID, $order_id, 0, 'not_applicable');
                            }
                            else
                            {
                                //Old Ledgers Case
                                $ledger_id_old = $this->model_account_panel_bankpayment->getLedgerIDInLedgerTable($ledgerName);
                                $pmntID = $this->model_account_panel_bankpayment->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', $payment_mode, -abs($amountOfPayment), $datedRO, date("Y-m-d H:i:s"), 'bank_transfer', 1, $user_id, 'payment', $payment_id, $payment_sub_id);
                                $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedRO, $ledger_id_old, $amountOfPayment, $order_no, $ref, $pmntID, $order_id, 0, 'not_applicable');
                            }
                            $response['status'] =  51;
                    	//}
                    	//else
                    	//{
                    	    //$response['status'] =  2;
                    	//}                    		
                	}
            	}
        	}
        }
        echo json_encode( $response );
    }//end function

    public function deleteAll() {
        /*
        $this->load->model('account_panel/bankpayment');
        $this->model_account_panel_bankpayment->deleteAllPayments();
        $this->response->redirect($this->url->link('account_panel/bankpayment', '&token=' . $this->request->get['token'] . $url, 'SSL'));        
        */ 
    }    

    public function addAjaxSellerPayment() {

        $response = array();

        $this->load->model('account_panel/bankpayment');    

        ////1. Get All Values -- Start --- //////////////////
        $ledgerid = $this->request->post['ledgerid'];
        $groupid = $this->request->post['groupid'];
        $payment_id = $_REQUEST['payment_id'];
        $amountOfPayment = $_REQUEST['amountOfPayment'];
        $datedRO = $this->request->post['datedROAA'];
        $ref = $this->request->post['ref'];//merchant txn
        //$payment_mode = $this->request->post['bank_name'];//bank name
        $trxn_bank = $this->request->post['bank_name'];//bank name

        $trxn_doneSuccess = "BANK_SUCCESS";
        $trxn_doneRequested = "BANK_REQUESTED";

        $mode = "SP";

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        // $buyersRefundGroupID = 4;
        // $sundryDebtorsGroupID = 10;
		
		$sellerPaymentGroupID = 7;
        $sellerPaymentLedgerID = 12;
        $sundryCreditorsGroupID = 11;

        $sellerPayment3TablesDetails = $this->model_account_panel_bankpayment->getSellerPayment3Tables($ref, $trxn_doneSuccess, $trxn_doneRequested, $trxn_bank, $datedRO);//utr date also
        //echo "<pre>";print_r($sellerPayment3TablesDetails);//die;

        $response = array();
        if ( !$sellerPayment3TablesDetails ){
            $response['status'] =  1;
            echo json_encode($response) ;
            exit();
        }
        else
        {
            //echo "<pre>";print_r($ocOrderDetails);die;
        	if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
        	    $response['status'] =  1;
        	}
        	else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
        	    $response['status'] =  1;
        	}
        	else if ($payment_id == "" || strlen($payment_id) == 0 || !is_numeric($payment_id)) {
        	    $response['status'] =  1;
        	}
        	else if ($amountOfPayment == "" || strlen($amountOfPayment) == 0 || !is_numeric($amountOfPayment)) {
        	    $response['status'] =  1;
        	}
        	else if ($ref == "" || strlen($ref) == 0) {
        	    $response['status'] =  1;
        	}
        	else
        	{
            	if ($groupid == $sellerPaymentGroupID){
                	if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($payment_id) > 0 && is_numeric($payment_id) && strlen($amountOfPayment) > 0 && is_numeric($amountOfPayment) && strlen($ref) > 0)
                	{
                        // // // // // // Compulsory another table one entry with seller_debit_note  --- Start --- // // // // // // //
                        /*
                        $tablename_arr = array_column($sellerPayment3TablesDetails,"tablename");
                        $checkTableName_arr = 0;
                        if (in_array("oc_seller_debit_note", $tablename_arr)) {
                            $a = in_array("oc_seller_invoice", $tablename_arr);
                            $b = in_array("oc_wsb_purchase", $tablename_arr);
                            if ( $a || $b) {
                            //if (!in_array("oc_seller_invoice", $tablename_arr)) {
                                echo "Got Irix";die;
                            }
                        }
                        echo "out"; die;
                        */
                        // // // // // // Check Seller ID is Unique (not 2 b diff)  --- Start --- // // // // // // //
                        $seller_id_arr = array_column($sellerPayment3TablesDetails,"seller_id");
                        $checkSellerID_arr = 0;
                        if(count(array_unique($seller_id_arr)) !=1 )
                        {
                            // Array has duplicates
                            $checkSellerID_arr = 1;
                            $response['status'] =  11;
                            echo json_encode( $response );
                            exit();                              
                        }

	                    // // // // // // Check Amount = (SI + (-SDN) + WSB)  --- Start --- // // // // // // //
	                    $trxn_amount_arr = array_column($sellerPayment3TablesDetails,"trxn_amount");
                        //echo "<pre>";print_r($trxn_amount_arr);die;
	                    $amountTtl_arr = array_sum($trxn_amount_arr);
	                    $amountTtl_arr = (float)$amountTtl_arr;
	                    $checkAmountTotal = 0;

	                    $diff = (round($amountOfPayment,2) - round($amountTtl_arr,2));
	                    $diff1 = round($diff,2);
	                    if ($diff1 != 0)
	                    {
	                        $checkAmountTotal = 1;
	                        $response['status'] =  12;
	                        $response['row'] =  $diff1;
	                        echo json_encode( $response );
	                        exit();
	                    }

                        // // // // // // (No Checking Here). Get tin no. from seller_invoice_meta  --- Start --- // // // // // // //
                        $checkSeller_invoice_meta = 0;
                        $co = count($sellerPayment3TablesDetails);
                        for ($k=0; $k < $co; $k++) {
                            if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_seller_invoice')
                            {
                                $tin_arr = array();
                                if (empty($sellerPayment3TablesDetails[$k]['seller_invoice_meta']))
                                {
                                    $checkSeller_invoice_meta = 1;
                                    $response['status'] =  65;
                                    //$response['row'] =  $diff1;
                                    echo json_encode( $response );
                                    exit();
                                }
                                else
                                {
                                    $tin_arr = unserialize($sellerPayment3TablesDetails[$k]['seller_invoice_meta']);

                                    $tinn = "";
                                    if (empty($tin_arr['seller_data']['tin']))
                                    {
                                        $tinn = 'N/A';
                                    }
                                    else
                                    {
                                        $tinn = $tin_arr['seller_data']['tin']; 
                                    }

                                    $sellerPayment3TablesDetails[$k]['tin'] =  $tinn;
                                    $sellerPayment3TablesDetails[$k]['nickname'] =  $tin_arr['seller_data']['nickname'];
                                    $sellerPayment3TablesDetails[$k]['company'] =  $tin_arr['seller_data']['company'];
                                }
                            }
                            else if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_seller_debit_note')
                            {
                                $tin_arr = array();
                                $seller_inv = new SellerInvoice( $this );

                                $tin_arr = $seller_inv->getInvoiceInfo( $sellerPayment3TablesDetails[$k]['order_id'] , $sellerPayment3TablesDetails[$k]['suborder_id'], $sellerPayment3TablesDetails[$k]['seller_id']);
                                if (!$tin_arr)
                                {
                                    $checkSeller_invoice_meta = 1;
                                    $response['status'] =  66;
                                    //$response['row'] =  $diff1;
                                    echo json_encode( $response );
                                    exit();
                                }
                                else
                                {
                                    $tin_arr = unserialize($tin_arr['seller_invoice_meta']);

                                    $tinn = "";
                                    if (empty($tin_arr['seller_data']['tin']))
                                    {
                                        $tinn = 'N/A';
                                    }
                                    else
                                    {
                                        $tinn = $tin_arr['seller_data']['tin'];
                                    }

                                    $sellerPayment3TablesDetails[$k]['tin'] =  $tinn;
                                    $sellerPayment3TablesDetails[$k]['nickname'] =  $tin_arr['seller_data']['nickname'];
                                    $sellerPayment3TablesDetails[$k]['company'] =  $tin_arr['seller_data']['company'];
                                }
                            }
                            else if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_wsb_purchase')
                            {
                                $tin_arr = array();
                                if (empty($sellerPayment3TablesDetails[$k]['seller_invoice_meta']))
                                {
                                    $checkSeller_invoice_meta = 1;
                                    $response['status'] =  67;
                                    //$response['row'] =  $diff1;
                                    echo json_encode( $response );
                                    exit();
                                }
                                else
                                {
                                    $tin_arr = unserialize($sellerPayment3TablesDetails[$k]['seller_invoice_meta']);

                                    $tinn = "";
                                    if (empty($tin_arr['tin']))
                                    {
                                        $tinn = 'N/A';
                                    }
                                    else
                                    {
                                        $tinn = $tin_arr['tin']; 
                                    }

                                    $sellerPayment3TablesDetails[$k]['tin'] =  $tinn;
                                    $sellerPayment3TablesDetails[$k]['nickname'] =  $tin_arr['nickname'];
                                    $sellerPayment3TablesDetails[$k]['company'] =  $tin_arr['company'];
                                }
                            }
                        }


                        //echo "<pre>";print_r($sellerPayment3TablesDetails);die;
                        //echo "out";die;

                    	if ($checkSellerID_arr !=1 && $checkAmountTotal !=1 && $checkSeller_invoice_meta !=1)
                    	{
        	            	$file_path = "";
            	            $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                	        if ( !$checkPaymentSubForPaymentID ) {
                    	        $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
	                        } else {
                                $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                                $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                                $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                                //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                                $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                                $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                    	    }
                            
                            $payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);

                            $co = count($sellerPayment3TablesDetails);
                    		for ($k=0; $k < $co; $k++) {

	                            $tablename = $sellerPayment3TablesDetails[$k]['tablename'];
	                            $order_id = $sellerPayment3TablesDetails[$k]['order_id'];
	                            $order_no = $sellerPayment3TablesDetails[$k]['order_no'];
	                            $trxn_amount = $sellerPayment3TablesDetails[$k]['trxn_amount'];
	                            //$pmntID = $sellerPayment3TablesDetails['payment_id'];
	                            
                                $seller_invoice_id = $sellerPayment3TablesDetails[$k]['seller_invoice_id'];

                                $customer_id = $sellerPayment3TablesDetails[$k]['seller_id'];
                                $gst_number = $sellerPayment3TablesDetails[$k]['tin'];
                                $nickname = $sellerPayment3TablesDetails[$k]['nickname'];
	                            $company = $sellerPayment3TablesDetails[$k]['company'];

                                //
                                //$SellerledgerName = CONCAT('customer_id','-',$nickname,'-',$company,'-',$gst_number);

                                $sellerledgerName = '';
                                if ($gst_number == "N/A")
                                {
                                    $sellerledgerName = $customer_id;
                                    $sellerledgerName .= (!empty(trim($nickname)) ? '_' . trim($nickname) : '');
                                    $sellerledgerName .= (!empty(trim($company)) ? '_' . trim($company) : '');
                                }
                                else
                                {
                                    $gst_number  = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
                                    $gst_number  = trim($gst_number);

                                    $sellerledgerName = $customer_id;
                                    $sellerledgerName .= (!empty(trim($nickname)) ? '_' . trim($nickname) : '');
                                    $sellerledgerName .= (!empty(trim($company)) ? '_' . trim($company) : '');
                                    $sellerledgerName .= (!empty($gst_number) ? '_' . trim($gst_number) : '');
                                }


                                $getSellerledger = $this->model_account_panel_bankpayment->getSellerLedger($customer_id);

                                if ( !$getSellerledger ) {
                                    $this->model_account_panel_bankpayment->saveSellerLedger( $customer_id, $sellerledgerName );
                                }
                                else
                                {
                                    $sellerledgerName = "";
                                    $sellerledgerName = $getSellerledger['ledger_name'];
                                }
                                //

	                            $ledgerNameInLedgerTable = $this->model_account_panel_bankpayment->getLedgerNameInLedgerTable($sellerledgerName);
                                $ledger_id = 0;
                                if ( $ledgerNameInLedgerTable == 0 ){
                                    $ledger_id = $this->model_account_panel_bankpayment->saveLedger( $sellerledgerName, $sundryCreditorsGroupID, $customer_id, $user_id, $datedCM, $user_array);

                                    $this->model_account_panel_bankpayment->updateCustomerLedger( $ledger_id, $customer_id);
                                }
                                else
                                {
                                    //Old Ledgers Case
                                    $ledger_id = $this->model_account_panel_bankpayment->getLedgerIDInLedgerTable($sellerledgerName);

                                    $this->model_account_panel_bankpayment->updateLedger( $ledger_id, $customer_id);
                                    $this->model_account_panel_bankpayment->updateCustomerLedger( $ledger_id, $customer_id);
                                }

                                if ($trxn_amount > 0)
                                {
                                    $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedRO, $ledger_id, $trxn_amount,$order_no, $ref, 0, $order_id, 0, 'not_applicable');
                                }
                                else
                                {
                                    $sql = $this->model_account_panel_bankpayment->insertPaymentSubIncomesCr($payment_id, $payment_sub_id, $datedRO, $ledger_id, abs($trxn_amount), $order_no, $ref, 0, $order_id, 0, 
                                        'not_applicable');
                                }
                    		}
                    		$response['status'] =  51;  
                    	}
                	}
            	}
        	}
        }
        echo json_encode( $response );
    }//end function

    public function importSellerPaymentCSV() {

        $sellerPaymentGroupID = 7;
        $sellerPaymentLedgerID = 12;
        $sundryCreditorsGroupID = 11;

        $response = array();

        $this->load->model('account_panel/bankpayment');

        $row = 0;

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        $salaryLedgerID = 14;
        $expensesGroupID = 12;
        $employeesGroupID = 6;

        /////////// Entries of only Employees Salary Group ///////////////////////////////

        //////// 1. Checking of CSV Default Code - start /////////
        if(empty($this->request->files['fileToUploadSellerPayment']['name'])){
                //$this->response->redirect($this->url->link('account_panel/bankpayment', "", 'SSL'));
            $response['status'] = 1;
            echo json_encode( $response );
            exit();                  
        }
        // Sanitize the filename
        $filename = basename(html_entity_decode($this->request->files['fileToUploadSellerPayment']['name'], ENT_QUOTES, 'UTF-8'));

        // Validate the filename length
        if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
            $this->error['warning']['filename']  = $this->language->get('error_filename');
        }

        // Allowed file extension types
        $allowed = array('csv',
                         'xls',
                         'xlsx');

        if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
            $this->error['warning']['filetype'] = $this->language->get('error_filetype');
        }
        // Allowed file mime types
        $allowed = array('application/vnd.ms-excel',
                         'text/plain',
                         'text/csv',
                         'text/tsv');

        if (!in_array($this->request->files['fileToUploadSellerPayment']['type'], $allowed)) {
            $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
            // $json['error'] = $this->language->get('error_filetype');
        }

        // Check to see if any PHP files are trying to be uploaded
        $content = file_get_contents($this->request->files['fileToUploadSellerPayment']['tmp_name']);

        if (preg_match('/\<\?php/i', $content)) {
            $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
        }

        // Return any upload error
        if ($this->request->files['fileToUploadSellerPayment']['error'] != UPLOAD_ERR_OK) {
            $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUploadSellerPayment']['error']);
        }

        ////////1. Checking of CSV Default Code - End /////////

        //if no error in csv
        if (!$this->error) {
            ///////2. Now CSV Read here./////////
            $file_handle = fopen($this->request->files['fileToUploadSellerPayment']['tmp_name'], 'r');
            while (!feof($file_handle) ) {
                $line_of_text[] = fgetcsv($file_handle, 1024);
            }
            fclose($file_handle);

            ////////////3. Condition of column of CSV and Salary CSV Import Only//////
            $getColumnsSellerPaymentCSV = array_values($line_of_text)[0];
            //$countColumnsSellerPaymentCSV = count($getColumnsSellerPaymentCSV);

            if ($getColumnsSellerPaymentCSV[0]=='Reference' && $this->request->files['fileToUploadSellerPayment']['name'] =='seller_payment_CSV.csv')
            {
                //  // // // // // 4. Conditions Others- Start// // // // // //

                // // // 4(a). Check Ref No.  // // // // //
                $ref_arr = array_column($line_of_text,0);
                unset($ref_arr[0]);
                $checkRef_arr = 0;
                foreach ($ref_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkRef_arr = 1;
                        $response['status'] = 2;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                         
                    }
                    //Ref No. should be in Payment Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInPayment = $this->model_account_panel_bankpayment->getPaymentForRef2($value);
                        if ( !$checkRefEntryInPayment ) {
                            $checkRef_arr = 1;
                            //$response['status'] =  22;
                            //$response['row'] =  $row+1;
                            // echo json_encode( $response );
                            // exit();
                            $response['status22'][] =  "Ref No. should be in Payment Table :- ";
                            $response['row22'][] =  $value;
                        }
                    }
                    //Ref No. should not be in Payment Sub Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInPaymentSub = $this->model_account_panel_bankpayment->getPaymentSubForRef($value);
                        if ( $checkRefEntryInPaymentSub ) {
                            $checkRef_arr = 1;
                            // $response['status'] =  23;
                            // $response['row'] =  $row+1;
                            // echo json_encode( $response );
                            // exit();
                            $response['status23'][] =  "Ref.No. should not be in Payment Sub Table :- ";
                            $response['row23'][] =  $value;
                        }
                    }
                }

                //Ref No. should not be duplicate on CSV
                if(count(array_unique($ref_arr))<count($ref_arr))
                {
                    // Array has duplicates
                    $checkRef_arr = 1;
                    $response['status'] =  222;
                    //$response['row'] =  $row+1;
                    echo json_encode( $response );
                    exit();                              
                }
                
                $row = 0;

                $checkNoEntry = 0;
                $checkSellerID_arr = 0;
                $checkAmountTotal = 0;
                $checkSeller_invoice_meta = 0;

                if ($checkRef_arr !=1){
                    $i = 0;
                    foreach ($ref_arr as $value ){
                        $row++;
                        $ref = $value;

                        $getPaymentData = $this->model_account_panel_bankpayment->getPaymentData($ref);
                        //echo "<pre>";print_r($getPaymentData);die;

                        ////1. Get All Values -- Start --- //////////////////
                        $ledgerid = $sellerPaymentLedgerID;
                        //$groupid = ""; //not necessary
                        $payment_id = $getPaymentData['payment_id'];
                        $amountOfPayment = $getPaymentData['amount'];
                        $datedRO = $getPaymentData['dated'];
                        $ref = $getPaymentData['reference'];
                        $trxn_bank = $getPaymentData['ledger_name'];//bank name

                        $trxn_doneSuccess = "BANK_SUCCESS";
                        $trxn_doneRequested = "BANK_REQUESTED";



                        $sellerPayment3TablesDetails = $this->model_account_panel_bankpayment->getSellerPayment3Tables($ref, $trxn_doneSuccess, $trxn_doneRequested, $trxn_bank, $datedRO);
                        //echo "<pre>";print_r($sellerPayment3TablesDetails);die;
                        
                        
                        if ( !$sellerPayment3TablesDetails ){
                            $checkNoEntry = 1;
                            // $response['status'] =  31;
                            // $response['row'] =  $row+1;
                            $response['status31'][] =  "No Entry found";
                            $response['row31'][] =  $value;
                            // echo json_encode($response) ;
                            // exit();
                        }
                        else
                        {
                            if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($payment_id) > 0 && is_numeric($payment_id) && strlen($amountOfPayment) > 0 && is_numeric($amountOfPayment) && strlen($ref) > 0)
                            {
                                // // // // // // Check Seller ID is Unique (not 2 b diff)  --- Start --- // // // // // // //
                                $seller_id_arr = array_column($sellerPayment3TablesDetails,"seller_id");

                                if(count(array_unique($seller_id_arr)) !=1 )
                                {
                                    // Array has duplicates
                                    $checkSellerID_arr = 1;
                                    $response['status'] =  32;
                                    $response['row'] =  $row+1;
                                    echo json_encode( $response );
                                    exit();                              
                                }

                                // // // // // // Check Amount = (SI + (-SDN) + WSB)  --- Start --- // // // // // // //
                                $trxn_amount_arr = array_column($sellerPayment3TablesDetails,"trxn_amount");
                                //echo "<pre>";print_r($trxn_amount_arr);die;
                                $amountTtl_arr = array_sum($trxn_amount_arr);
                                $amountTtl_arr = (float)$amountTtl_arr;

                                $diff = (round($amountOfPayment,2) - round($amountTtl_arr,2));
                                $diff1 = round($diff,2);
                                if ($diff1 != 0)
                                {
                                    $checkAmountTotal = 1;
                                    $response['status'] =  33;
                                    $response['row'] =  $row+1;
                                    $response['diff'] =  $diff1;
                                    echo json_encode( $response );
                                    exit();
                                }


                                // // // // // // get tin no. from seller_invoice_meta (only checking whole csv first)  --- Start --- // // // // // // //
                                $co = count($sellerPayment3TablesDetails);
                                for ($k=0; $k < $co; $k++) {
                                    if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_seller_invoice')
                                    {
                                        $tin_arr = array();
                                        if (empty($sellerPayment3TablesDetails[$k]['seller_invoice_meta']))
                                        {
                                            $checkSeller_invoice_meta = 1;
                                            $response['status'] =  65;
                                            $response['row'] =  $row+1;
                                            echo json_encode( $response );
                                            exit();
                                        }
                                    }
                                    else if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_seller_debit_note')
                                    {
                                        $tin_arr = array();
                                        $seller_inv = new SellerInvoice( $this );

                                        $tin_arr = $seller_inv->getInvoiceInfo( $sellerPayment3TablesDetails[$k]['order_id'] , $sellerPayment3TablesDetails[$k]['suborder_id'], $sellerPayment3TablesDetails[$k]['seller_id']);

                                        if (!$tin_arr)
                                        {
                                            $checkSeller_invoice_meta = 1;
                                            $response['status'] =  66;
                                            $response['row'] =  $row+1;
                                            echo json_encode( $response );
                                            exit();
                                        }
                                    }
                                    else if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_wsb_purchase')
                                    {
                                        $tin_arr = array();
                                        if (empty($sellerPayment3TablesDetails[$k]['seller_invoice_meta']))
                                        {
                                            $checkSeller_invoice_meta = 1;
                                            $response['status'] =  67;
                                            $response['row'] =  $row+1;
                                            echo json_encode( $response );
                                            exit();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }


                //  // // // // // 4. Conditions Others- End// // // // // //

                if ($checkRef_arr !=1 && $checkNoEntry !=1 && $checkSellerID_arr !=1 && $checkAmountTotal !=1 && $checkSeller_invoice_meta !=1){

                //////5. Insertion Updation in Payment Sub, Payment Sub CSV tables)-- Start --- /////  
                    $i = 0;
                    foreach($line_of_text as $cav_data){
                        if (empty($cav_data['0']) || $i ==0) {
                            $i++;
                            continue;
                        }

                        $ref            = $cav_data['0'];

                        ////5(a). Insertion Updation in Payment -- Start --- /////
                        $file_path = "";
                        $getPaymentData = $this->model_account_panel_bankpayment->getPaymentData($ref);
                        //echo "<pre>";print_r($getPaymentData);die;

                        ////1. Get All Values -- Start --- //////////////////
                        $ledgerid = $sellerPaymentLedgerID;
                        $groupid = $sellerPaymentGroupID;
                        $payment_id = $getPaymentData['payment_id'];
                        $amountOfPayment = $getPaymentData['amount'];
                        $datedRO = $getPaymentData['dated'];
                        $ref = $getPaymentData['reference'];
                        //$payment_mode = $this->request->post['bank_name'];//bank name
                        $trxn_bank = $getPaymentData['ledger_name'];//bank name

                        $trxn_doneSuccess = "BANK_SUCCESS";
                        $trxn_doneRequested = "BANK_REQUESTED";

                        $sellerPayment3TablesDetails = $this->model_account_panel_bankpayment->getSellerPayment3Tables($ref, $trxn_doneSuccess, $trxn_doneRequested, $trxn_bank, $datedRO);//utr date also
                        //echo "<pre>";print_r($sellerPayment3TablesDetails);die;

//
                        // // // // // // get tin no. from seller_invoice_meta  --- Start --- // // // // // // //
                        $checkSeller_invoice_meta = 0;
                        $co = count($sellerPayment3TablesDetails);
                        for ($k=0; $k < $co; $k++) {
                            if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_seller_invoice')
                            {
                                $tin_arr = array();
                                if (empty($sellerPayment3TablesDetails[$k]['seller_invoice_meta']))
                                {
                                    $checkSeller_invoice_meta = 1;
                                    $response['status'] =  65;
                                    //$response['row'] =  $diff1;
                                    echo json_encode( $response );
                                    exit();
                                }
                                else
                                {
                                    $tin_arr = unserialize($sellerPayment3TablesDetails[$k]['seller_invoice_meta']);

                                    $tinn = "";
                                    if (empty($tin_arr['seller_data']['tin']))
                                    {
                                        $tinn = 'N/A';
                                    }
                                    else
                                    {
                                        $tinn = $tin_arr['seller_data']['tin']; 
                                    }

                                    $sellerPayment3TablesDetails[$k]['tin'] =  $tinn;
                                    $sellerPayment3TablesDetails[$k]['nickname'] =  $tin_arr['seller_data']['nickname'];
                                    $sellerPayment3TablesDetails[$k]['company'] =  $tin_arr['seller_data']['company'];
                                }
                            }
                            else if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_seller_debit_note')
                            {
                                $tin_arr = array();
                                $seller_inv = new SellerInvoice( $this );

                                $tin_arr = $seller_inv->getInvoiceInfo( $sellerPayment3TablesDetails[$k]['order_id'] , $sellerPayment3TablesDetails[$k]['suborder_id'], $sellerPayment3TablesDetails[$k]['seller_id']);

                                if (!$tin_arr)
                                {
                                    $checkSeller_invoice_meta = 1;
                                    $response['status'] =  66;
                                    //$response['row'] =  $diff1;
                                    echo json_encode( $response );
                                    exit();
                                }
                                else
                                {
                                    $tin_arr = unserialize($tin_arr['seller_invoice_meta']);

                                    $tinn = "";
                                    if (empty($tin_arr['seller_data']['tin']))
                                    {
                                        $tinn = 'N/A';
                                    }
                                    else
                                    {
                                        $tinn = $tin_arr['seller_data']['tin'];
                                    }

                                    $sellerPayment3TablesDetails[$k]['tin'] =  $tinn;
                                    $sellerPayment3TablesDetails[$k]['nickname'] =  $tin_arr['seller_data']['nickname'];
                                    $sellerPayment3TablesDetails[$k]['company'] =  $tin_arr['seller_data']['company'];
                                }
                            }
                            else if ($sellerPayment3TablesDetails[$k]['tablename'] =='oc_wsb_purchase')
                            {
                                $tin_arr = array();
                                if (empty($sellerPayment3TablesDetails[$k]['seller_invoice_meta']))
                                {
                                    $checkSeller_invoice_meta = 1;
                                    $response['status'] =  67;
                                    //$response['row'] =  $diff1;
                                    echo json_encode( $response );
                                    exit();
                                }
                                else
                                {
                                    $tin_arr = unserialize($sellerPayment3TablesDetails[$k]['seller_invoice_meta']);

                                    $tinn = "";
                                    if (empty($tin_arr['tin']))
                                    {
                                        $tinn = 'N/A';
                                    }
                                    else
                                    {
                                        $tinn = $tin_arr['tin']; 
                                    }

                                    $sellerPayment3TablesDetails[$k]['tin'] =  $tinn;
                                    $sellerPayment3TablesDetails[$k]['nickname'] =  $tin_arr['nickname'];
                                    $sellerPayment3TablesDetails[$k]['company'] =  $tin_arr['company'];
                                }
                            }
                        }


                        //echo "<pre>";print_r($sellerPayment3TablesDetails);die;
                        //echo "out";die;
//

                        $file_path = "";
                        $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                        if ( !$checkPaymentSubForPaymentID ) {
                            $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                        } else {
                            $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                            $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                            $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                            //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                        }
                                        
                        $payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);

                        $co = count($sellerPayment3TablesDetails);
                        for ($k=0; $k < $co; $k++) {

                            $tablename = $sellerPayment3TablesDetails[$k]['tablename'];
                            $order_id = $sellerPayment3TablesDetails[$k]['order_id'];
                            $order_no = $sellerPayment3TablesDetails[$k]['order_no'];
                            $trxn_amount = $sellerPayment3TablesDetails[$k]['trxn_amount'];
                            //$pmntID = $sellerPayment3TablesDetails['payment_id'];
                                            
                            $seller_invoice_id = $sellerPayment3TablesDetails[$k]['seller_invoice_id'];

                            $customer_id = $sellerPayment3TablesDetails[$k]['seller_id'];
                            $gst_number = $sellerPayment3TablesDetails[$k]['tin'];
                            $nickname = $sellerPayment3TablesDetails[$k]['nickname'];
                            $company = $sellerPayment3TablesDetails[$k]['company'];


                            $sellerledgerName = '';
                            if ($gst_number == "N/A")
                            {
                                $sellerledgerName = $customer_id;
                                $sellerledgerName .= (!empty(trim($nickname)) ? '_' . trim($nickname) : '');
                                $sellerledgerName .= (!empty(trim($company)) ? '_' . trim($company) : '');
                            }
                            else
                            {
                                $sellerledgerName = $customer_id;
                                $sellerledgerName .= (!empty(trim($nickname)) ? '_' . trim($nickname) : '');
                                $sellerledgerName .= (!empty(trim($company)) ? '_' . trim($company) : '');
                                $sellerledgerName .= (!empty(trim($gst_number)) ? '_' . trim($gst_number) : '');
                            }

 
                            $getSellerledger = $this->model_account_panel_bankpayment->getSellerLedger($customer_id);

                            if ( !$getSellerledger ) {
                                $this->model_account_panel_bankpayment->saveSellerLedger( $customer_id, $sellerledgerName );
                            }
                            else
                            {
                                $sellerledgerName = "";
                                $sellerledgerName = $getSellerledger['ledger_name'];
                            }

                            $ledgerNameInLedgerTable = $this->model_account_panel_bankpayment->getLedgerNameInLedgerTable($sellerledgerName);
                            $ledger_id = 0;
                            if ( $ledgerNameInLedgerTable == 0 ){
                                $ledger_id = $this->model_account_panel_bankpayment->saveLedger( $sellerledgerName, $sundryCreditorsGroupID, $customer_id, $user_id, $datedCM, $user_array);

                                $this->model_account_panel_bankpayment->updateCustomerLedger( $ledger_id, $customer_id);
                            }
                            else
                            {
                                //Old Ledgers Case
                                $ledger_id = $this->model_account_panel_bankpayment->getLedgerIDInLedgerTable($sellerledgerName);

                                $this->model_account_panel_bankpayment->updateLedger( $ledger_id, $customer_id);
                                $this->model_account_panel_bankpayment->updateCustomerLedger( $ledger_id, $customer_id);
                            }

                            if ($trxn_amount > 0)
                            {
                                $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedRO, $ledger_id, $trxn_amount,$order_no, $ref, 0, $order_id, 0, 'not_applicable');
                            }
                            else
                            {
                                $sql = $this->model_account_panel_bankpayment->insertPaymentSubIncomesCr($payment_id, $payment_sub_id, $datedRO, $ledger_id, abs($trxn_amount), $order_no, $ref, 0, $order_id, 0, 
                                        'not_applicable');
                            }

                        }
                        $response['status'] =  51;
                    }
                }
            }
            else
            {
                $response['status'] =  11;
            }
        }
        // echo "<pre>";print_r($response);die;
        echo json_encode( $response );
    }   


    public function importBuyersRefundBulkCSVPG() {

        $this->load->model('account_panel/bankpayment');

        $sundryDebtorsGroupID = 10;

        $response = array();

        $row = 0;

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        //////// 1. Checking of CSV Default Code - start /////////
        if(empty($this->request->files['fileToUploadBuyersRefundBulkPG']['name'])){
                //$this->response->redirect($this->url->link('account_panel/bankpayment', "", 'SSL'));
            $response['status'] = 1;
            echo json_encode( $response );
            exit();                  
        }
        // Sanitize the filename
        $filename = basename(html_entity_decode($this->request->files['fileToUploadBuyersRefundBulkPG']['name'], ENT_QUOTES, 'UTF-8'));

        // Validate the filename length
        if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
            $this->error['warning']['filename']  = $this->language->get('error_filename');
        }

        // Allowed file extension types
        $allowed = array('csv',
                         'xls',
                         'xlsx');

        if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
            $this->error['warning']['filetype'] = $this->language->get('error_filetype');
        }
        // Allowed file mime types
        $allowed = array('application/vnd.ms-excel',
                         'text/plain',
                         'text/csv',
                         'text/tsv');

        if (!in_array($this->request->files['fileToUploadBuyersRefundBulkPG']['type'], $allowed)) {
            $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
            // $json['error'] = $this->language->get('error_filetype');
        }

        // Check to see if any PHP files are trying to be uploaded
        $content = file_get_contents($this->request->files['fileToUploadBuyersRefundBulkPG']['tmp_name']);

        if (preg_match('/\<\?php/i', $content)) {
            $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
        }

        // Return any upload error
        if ($this->request->files['fileToUploadBuyersRefundBulkPG']['error'] != UPLOAD_ERR_OK) {
            $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUploadBuyersRefundBulkPG']['error']);
        }

        ////////1. Checking of CSV Default Code - End /////////

        //if no error in csv
        if (!$this->error) {
            ///////2. Now CSV Read here./////////
            $file_handle = fopen($this->request->files['fileToUploadBuyersRefundBulkPG']['tmp_name'], 'r');
            while (!feof($file_handle) ) {
                $line_of_text[] = fgetcsv($file_handle, 1024);
            }
            fclose($file_handle);

            ////////////3. Condition of column of CSV and Salary CSV Import Only//////
            $getColumnsBuyersRefundBulkCSV = array_values($line_of_text)[0];
            //$countColumnsSellerPaymentCSV = count($getColumnsSellerPaymentCSV);

            if ($getColumnsBuyersRefundBulkCSV[0]=='Date' && $getColumnsBuyersRefundBulkCSV[1]=='Order No' && $getColumnsBuyersRefundBulkCSV[2]=='Ref' && $getColumnsBuyersRefundBulkCSV[3]=='Amount' && $getColumnsBuyersRefundBulkCSV[4]=='Bank Ref' && $getColumnsBuyersRefundBulkCSV[5]=='Payment Gateway' && $this->request->files['fileToUploadBuyersRefundBulkPG']['name'] =='Buyers_Refund_Bulk_PG_CSV.csv')
            {
                //  // // // // // 5. Conditions Others- Start// // // // // //

                // // // // // // 5(a). Check Date not blank --- Start --- // // // // // // //
                $dated_arr = array_column($line_of_text,0);
                unset($dated_arr[0]);
                $checkDated_arr = 0;
                foreach ( $dated_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkDated_arr = 1;
                        $response['status'] =  2;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                }
                // // // // // // 5(b). Check Order No Is_Numeric --- Start --- // // // // // // //
                $row = 0;
                $orderNo_arr = array_column($line_of_text,1);
                unset($orderNo_arr[0]);
                $checkOrderNo_arr = 0;
                foreach ( $orderNo_arr as $value ){
                    $row++;
                    if( !is_numeric(trim($value)) ) {
                        $checkOrderNo_arr = 1;
                        $response['status'] =  3;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                    if( strlen($value) > 1 ) {
                        $CustomerName = $this->model_account_panel_bankpayment->getCustomerNameForBR($value);
                        if ( !$CustomerName ) {
                            $checkOrderNo_arr = 1;
                            $response['status'] =  4;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                                
                        }
                    } 
                }

                // // // 5(c). Check Ref No. not blank // // // // //
                $row = 0;
                $ref_arr = array_column($line_of_text,2);
                unset($ref_arr[0]);
                $checkRef_arr = 0;
                foreach ( $ref_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkRef_arr = 1;
                        $response['status'] =  5;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                }
                
                // // // // // // 5(d). Check Amount Is_Numeric and positive --- Start --- // // // // // // //
                $row = 0;
                $amount_arr = array_column($line_of_text,3);
                unset($amount_arr[0]);
                $checkAmount_arr = 0;
                foreach ( $amount_arr as $value ){
                    $row++;
                    if( !is_numeric(trim($value)) ) {
                        $checkAmount_arr = 1;
                        $response['status'] =  6;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                    if($value <= 0) {
                        $checkAmount_arr = 1;
                        $response['status'] =  7;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                }

                // // // 5(e). Check Bank Ref No. not blank // // // // //
                $row = 0;
                $bank_ref_arr = array_column($line_of_text,4);
                unset($bank_ref_arr[0]);
                $checkBankRef_arr = 0;
                foreach ( $bank_ref_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkBankRef_arr = 1;
                        $response['status'] =  8;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();
                    }
                }

                // // // 5(f). Check Payment Gateway not blank and in ledger table // // // // //
                $row = 0;
                $payment_gateway_arr = array_column($line_of_text,5);
                unset($payment_gateway_arr[0]);
                $checkPaymentGateway_arr = 0;
                foreach ( $payment_gateway_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkPaymentGateway_arr = 1;
                        $response['status'] =  9;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                    if( strlen($value) > 1 ) {
                        $value = strtolower($value);
                        $LedgersByNameAndGroup = $this->model_account_panel_bankpayment->getLedgersByNameAndGroup($value, 2);
                        if ( !$LedgersByNameAndGroup ) {
                            $checkPaymentGateway_arr = 1;
                            $response['status'] =  10;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                               
                        }
                    } 
                }

                // // // // // // 5(f1). Check Payment Gateway not Duplicate on CSV (Frontend) --- Start --- // // // // // // //
                if(count(array_unique($payment_gateway_arr)) > 1)
                {
                    // Array has duplicates
                    $checkPaymentGateway_arr = 1;
                    $response['status'] =  11;
                    //$response['row'] =  $row+1;
                    echo json_encode( $response );
                    exit();                              
                }

                // // // 5(g). other most most most important conditions checking:- // // // // //

                $bank_ref_arr = array_column($line_of_text,4);
                unset($bank_ref_arr[0]);
                $unique_bank_ref_arr = array_unique($bank_ref_arr);
                $unique_bank_ref_arr = array_values($unique_bank_ref_arr);

                // // // // // // 5(g-a). Check Bank Ref No.  // // // // // // //
                foreach ( $unique_bank_ref_arr as $value ){
                    //Ref No. should be in Payment Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInPayment = $this->model_account_panel_bankpayment->getPaymentForRef2($value);
                        if ( !$checkRefEntryInPayment ) {
                            $checkBankRef_arr = 1;
                            $response['status'] =  12;
                            //$response['row'] =  $row+1;
                            $response['row'] =  $value;
                            echo json_encode( $response );
                            exit();                                
                        }
                    }
                    //Ref No. should not be in Payment Sub Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInPaymentSub = $this->model_account_panel_bankpayment->getPaymentSubForRef($value);
                        if ( $checkRefEntryInPaymentSub ) {
                            $checkBankRef_arr = 1;
                            $response['status'] =  13;
                            //$response['row'] =  $row+1;
                            $response['row'] =  $value;
                            echo json_encode( $response );
                            exit();                                
                        }
                    }
                }

                //$unique_bank_ref_count = array_count_values($bank_ref_arr);
                //echo "<pre>";print_r($unique_bank_ref_count);//die;


                // // // // // // 5(g-b). reference no. wise Amount calculation   // // // // // // //
                $calculated_amt_arr = array();
                
                $co = count($bank_ref_arr);

                foreach ( $unique_bank_ref_arr as $key => $value ){
                    $sum = 0;
                    for ($k=1; $k <= $co; $k++) {
                        if ($bank_ref_arr[$k] == $value)
                        {
                            if ($amount_arr[$k] > 0)
                            {
                                $sum  = $sum + $amount_arr[$k];
                            }
                        }
                    }
                    array_push($calculated_amt_arr, $sum);
                }

                $bank_ref_arr_count = count(array_unique($bank_ref_arr));
                $amt_arr_count = count(array_unique($calculated_amt_arr));

                $checkCountRefAndAmt = 0;
                if ($bank_ref_arr_count != $amt_arr_count)
                {
                    $checkCountRefAndAmt = 1;
                    $response['status'] =  99;
                    $response['row'] =  $row+1;
                    echo json_encode( $response );
                    exit(); 
                }

                //unique_bank_ref_arr
                // <pre>Array
                // (
                //     [0] => 226
                //     [1] => 227
                // )
                //calculated_amt_arr
                // Array
                // (
                //     [0] => 150
                //     [1] => 50
                // )
                // // // // // // 5(g-c). Reference wise calculated Amount matching with bank payment amount   // // // // // // //
                $checkAmtOfPayment = 0;
                $co = count($unique_bank_ref_arr);
                for ($k=0; $k < $co; $k++)
                {
                    $amountOfPayment = $this->model_account_panel_bankpayment->getAmountByRef($unique_bank_ref_arr[$k]);
                    if (round($amountOfPayment,2) != round($calculated_amt_arr[$k],2))
                    {
                        $checkAmtOfPayment = 1;
                        $response['status'] =  14;
                        //$response['row'] =  $row+1;
                        $response['row'] =  $unique_bank_ref_arr[$k];
                        echo json_encode( $response );
                        exit(); 
                    }
                }
                
                $row = 0;


                //  // // // // // 4. Conditions Others- End// // // // // //

                if ($checkDated_arr !=1 && $checkOrderNo_arr !=1 && $checkRef_arr !=1 && $checkAmount_arr !=1 && $checkBankRef_arr !=1 && $checkPaymentGateway_arr !=1 && $checkCountRefAndAmt !=1 && $checkAmtOfPayment !=1)
                {
                    //////5. Insertion Updation in Payment Sub, Payment Sub CSV tables)-- Start --- /////  
                    foreach ( $unique_bank_ref_arr as $value ){

                        ////1. Get All Values -- Start --- //////////////////

                        $getPaymentData = $this->model_account_panel_bankpayment->getPaymentData($value);        

                        $unique_payment_gateway_arr = array_unique($payment_gateway_arr);
                        $unique_payment_gateway_arr = array_values($unique_payment_gateway_arr);

                        $payment_gateway = strtolower($unique_payment_gateway_arr[0]);
                        $LedgersByNameAndGroup = $this->model_account_panel_bankpayment->getLedgersByNameAndGroup($payment_gateway, 2);

                        $ledgerid = 0;
                        $groupid = 0;
                        //if ($payment_gateway == "citrus")
                        if ($payment_gateway == strtolower($LedgersByNameAndGroup['ledger_name']))
                        {
                            //$ledgerid = 4;
                            $ledgerid = $LedgersByNameAndGroup['ledger_id'];
                            $groupid = $LedgersByNameAndGroup['group_id'];
                        }

                        $payment_id = $getPaymentData['payment_id'];
                        $amountOfPayment = $getPaymentData['amount'];
                        $datedRO = $getPaymentData['dated'];

                        $file_path = "";
                        $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                        if ( !$checkPaymentSubForPaymentID ) {
                            $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                        } else {
                            $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                            $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                            $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                            //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                        }

                        //$payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);
                    }


                    //////6(b). Insertion Updation in Payment Sub CSV table)-- Start --- /////  
                    $i = 0;
                    foreach($line_of_text as $cav_data){
                        if (empty($cav_data['0']) || $i ==0) {
                            $i++;
                            continue;
                        }

                        $dated             = $cav_data['0'];
                        $order_no          = $cav_data['1'];
                        $ref               = $cav_data['2'];//merchant_txn_id
                        $amount            = $cav_data['3'];
                        $bank_ref          = $cav_data['4'];
                        $payment_gateway   = $cav_data['5'];

                        $datedx = "";
                        if( strlen( $dated ) > 1 ) {
                            $datedx = $dated;
                            $datedx = date("Y-m-d", strtotime($datedx));
                        }
                        
                        $getPaymentData = $this->model_account_panel_bankpayment->getPaymentData($bank_ref);
                        //echo "<pre>"print_r($getPaymentData);die;
                        $payment_id = $getPaymentData['payment_id'];
                        $payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);


                        //$amount = -abs($amount);
                        $CustomerName = $this->model_account_panel_bankpayment->getCustomerNameForBR($order_no);

                        $this->load->model('accounts/salesreports');
                        $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                        /*
                        $ledgerName = $CustomerName['customer_id'];
                        $ledgerName .= (!empty(trim($CustomerName['firstname'])) ? '_' . trim($CustomerName['firstname']) : '');
                        $ledgerName .= (!empty(trim($CustomerName['lastname'])) ? '_' . trim($CustomerName['lastname']) : '');
                        $ledgerName .= (!empty(trim($CustomerName['payment_company'])) ? '_' . trim($CustomerName['payment_company']) : '');
                        */

                        $order_id = $CustomerName['order_id'];

                        $ledgerNameInLedgerTable = $this->model_account_panel_bankpayment->getLedgerNameInLedgerTable($ledgerName);

                        if ( $ledgerNameInLedgerTable == 0 ){
                            //$ledger_id_new = $this->model_account_panel_bankpayment->saveLedger( $ledgerName, $sundryDebtorsGroupID, $user_id, $datedCM, $user_array);

                            $ledger_id_new = $this->model_account_panel_bankpayment->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);

                            $gst_number = '';
                            if (empty($CustomerName['gst_number'])) {
                                $gst_number = 'N/A';
                            }
                            else
                            {
                                $gst_number = $CustomerName['gst_number'];   
                            }

                            $this->model_account_panel_bankpayment->updateCustomerLedger( $ledger_id_new, $CustomerName['customer_id']);

                            $pmntID = $this->model_account_panel_bankpayment->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', '', -abs($amount), $datedx, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'payment', $payment_id, $payment_sub_id);
                            $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedx, $ledger_id_new, $amount, $order_no, $ref, $pmntID, $order_id, 0, 'not_applicable');
                        }
                        else
                        {
                            //Old Ledgers Case
                            $ledger_id_old = $this->model_account_panel_bankpayment->getLedgerIDInLedgerTable($ledgerName);
                            $pmntID = $this->model_account_panel_bankpayment->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', '', -abs($amount), $datedx, date("Y-m-d H:i:s"), $payment_gateway, 1, $user_id, 'payment', $payment_id, $payment_sub_id);                                    
                            $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedx, $ledger_id_old, $amount, $order_no, $ref, $pmntID, $order_id, 0, 'not_applicable');
                        }
                        $response['status'] =  51;
                    }
                }
            }
            else
            {
                $response['status'] =  50;
            }
        }
        echo json_encode( $response );
    }

    public function importBuyersRefundBulkCSVBank() {

        $this->load->model('account_panel/bankpayment');

        $buyersRefundGroupID = 4;
        $buyersRefundLedgerID = 11;
        $sundryDebtorsGroupID = 10;

        $response = array();

        $row = 0;

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        //////// 1. Checking of CSV Default Code - start /////////
        if(empty($this->request->files['fileToUploadBuyersRefundBulkBank']['name'])){
                //$this->response->redirect($this->url->link('account_panel/bankpayment', "", 'SSL'));
            $response['status'] = 1;
            echo json_encode( $response );
            exit();                  
        }
        // Sanitize the filename
        $filename = basename(html_entity_decode($this->request->files['fileToUploadBuyersRefundBulkBank']['name'], ENT_QUOTES, 'UTF-8'));

        // Validate the filename length
        if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
            $this->error['warning']['filename']  = $this->language->get('error_filename');
        }

        // Allowed file extension types
        $allowed = array('csv',
                         'xls',
                         'xlsx');

        if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
            $this->error['warning']['filetype'] = $this->language->get('error_filetype');
        }
        // Allowed file mime types
        $allowed = array('application/vnd.ms-excel',
                         'text/plain',
                         'text/csv',
                         'text/tsv');

        if (!in_array($this->request->files['fileToUploadBuyersRefundBulkBank']['type'], $allowed)) {
            $this->error['warning']['file_mime_type'] = $this->language->get('error_filetype');
            // $json['error'] = $this->language->get('error_filetype');
        }

        // Check to see if any PHP files are trying to be uploaded
        $content = file_get_contents($this->request->files['fileToUploadBuyersRefundBulkBank']['tmp_name']);

        if (preg_match('/\<\?php/i', $content)) {
            $this->error['warning']['nophpfile'] = $this->language->get('error_filetype') ;
        }

        // Return any upload error
        if ($this->request->files['fileToUploadBuyersRefundBulkBank']['error'] != UPLOAD_ERR_OK) {
            $this->error['warning']['neterror'] = $this->language->get('error_upload_' . $this->request->files['fileToUploadBuyersRefundBulkBank']['error']);
        }

        ////////1. Checking of CSV Default Code - End /////////

        //if no error in csv
        if (!$this->error) {
            ///////2. Now CSV Read here./////////
            $file_handle = fopen($this->request->files['fileToUploadBuyersRefundBulkBank']['tmp_name'], 'r');
            while (!feof($file_handle) ) {
                $line_of_text[] = fgetcsv($file_handle, 1024);
            }
            fclose($file_handle);

            ////////////3. Condition of column of CSV and Salary CSV Import Only//////
            $getColumnsBuyersRefundBulkBankCSV = array_values($line_of_text)[0];
            //$countColumnsSellerPaymentCSV = count($getColumnsSellerPaymentCSV);

            if ($getColumnsBuyersRefundBulkBankCSV[0]=='Order No' && $getColumnsBuyersRefundBulkBankCSV[1]=='Bank Ref' && $this->request->files['fileToUploadBuyersRefundBulkBank']['name'] =='Buyers_Refund_Bulk_Bank_CSV.csv')
            {
                //  // // // // // 5. Conditions Others- Start// // // // // //

                // // // // // // 5(a). Check Order No Is_Numeric --- Start --- // // // // // // //
                $row = 0;
                $orderNo_arr = array_column($line_of_text,0);
                unset($orderNo_arr[0]);
                $checkOrderNo_arr = 0;
                foreach ( $orderNo_arr as $value ){
                    $row++;
                    if( !is_numeric(trim($value)) ) {
                        $checkOrderNo_arr = 1;
                        $response['status'] =  2;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();                            
                    }
                    if( strlen($value) > 1 ) {
                        $CustomerName = $this->model_account_panel_bankpayment->getCustomerNameForBR($value);
                        if ( !$CustomerName ) {
                            $checkOrderNo_arr = 1;
                            $response['status'] =  3;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                                
                        }
                    } 
                }

                // // // 5(b). Checking Duplicate Bank Ref  // // // // //
                $bank_ref_arr = array_column($line_of_text,1);
                unset($bank_ref_arr[0]);
                $checkBankRef_arr = 0;

                if(count(array_unique($bank_ref_arr))<count($bank_ref_arr))
                {
                    // Array has duplicates
                    $checkBankRef_arr = 1;
                    $response['status'] =  4;
                    $response['row'] =  $row+1;
                    echo json_encode( $response );
                    exit();                              
                } 

                // // // 5(c). Check Bank Ref No. not blank // // // // //
                $row = 0;
                // $bank_ref_arr = array_column($line_of_text,1);
                // unset($bank_ref_arr[0]);
                //$checkBankRef_arr = 0;

                foreach ( $bank_ref_arr as $value ){
                    $row++;
                    if( strlen($value) < 1 ) {
                        $checkBankRef_arr = 1;
                        $response['status'] =  5;
                        $response['row'] =  $row+1;
                        echo json_encode( $response );
                        exit();
                    }
                    //Ref No. should be in Payment Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInPayment = $this->model_account_panel_bankpayment->getPaymentForRef2($value);
                        if ( !$checkRefEntryInPayment ) {
                            $checkBankRef_arr = 1;
                            $response['status'] =  6;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                                
                        }
                    }
                    //Ref No. should not be in Payment Sub Table.
                    if( strlen($value) > 0 ) {
                        $checkRefEntryInPaymentSub = $this->model_account_panel_bankpayment->getPaymentSubForRef($value);
                        if ( $checkRefEntryInPaymentSub ) {
                            $checkBankRef_arr = 1;
                            $response['status'] =  7;
                            $response['row'] =  $row+1;
                            echo json_encode( $response );
                            exit();                                
                        }
                    }
                }

                if ($checkOrderNo_arr !=1 && $checkBankRef_arr !=1)
                {

                //////5. Insertion Updation in Payment Sub, Payment Sub CSV tables)-- Start --- /////  
                    $i = 0;
                    foreach($line_of_text as $cav_data){
                        if (empty($cav_data['0']) || $i ==0) {
                            $i++;
                            continue;
                        }

                        $order_no       = $cav_data['0'];
                        $bank_ref       = $cav_data['1'];

                        ////5(a). Insertion Updation in Payment -- Start --- /////
                        $getPaymentData = $this->model_account_panel_bankpayment->getPaymentData($bank_ref);

                        ////1. Get All Values -- Start --- //////////////////
                        $ledgerid = $buyersRefundLedgerID;
                        $groupid = $buyersRefundGroupID;
                        $payment_id = $getPaymentData['payment_id'];
                        $amountOfPayment = $getPaymentData['amount'];
                        $datedRO = $getPaymentData['dated'];
                        $ref = $getPaymentData['reference'];
                        $bank_name = $getPaymentData['ledger_name'];//bank name

                        $file_path = "";
                        $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);

                        // If a payment sub does not exist already
                        // it means we are creating a new entry
                        if ( !$checkPaymentSubForPaymentID ) {
                            $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                        } else {
                            // We are updating existing payment, so we first delete the existing
                            $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                            $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                            $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                            //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                            $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                            $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                        }

                        $payment_sub_id = $this->model_account_panel_bankpayment->getPaymentSubForPaymentSubID($payment_id);

                        $CustomerName = $this->model_account_panel_bankpayment->getCustomerNameForBR($order_no);

                        $this->load->model('accounts/salesreports');
                        $ledgerName = $this->model_accounts_salesreports->getCustomerLedger($CustomerName['customer_id']) ;

                        /*
                        $ledgerName = $CustomerName['customer_id'];
                        $ledgerName .= (!empty(trim($CustomerName['firstname'])) ? '_' . trim($CustomerName['firstname']) : '');
                        $ledgerName .= (!empty(trim($CustomerName['lastname'])) ? '_' . trim($CustomerName['lastname']) : '');
                        $ledgerName .= (!empty(trim($CustomerName['payment_company'])) ? '_' . trim($CustomerName['payment_company']) : '');
                        */

                        $order_id = $CustomerName['order_id'];

                        $ledgerNameInLedgerTable = $this->model_account_panel_bankpayment->getLedgerNameInLedgerTable($ledgerName);

                        // check if this refund entry already exists in database
                        $pmntID = $this->model_account_panel_bankpayment->getRefundFromOcOrderPayment($order_id, $ref, -abs($amountOfPayment), 'bank_transfer');

                        if (!$pmntID) {
                        $pmntID = $this->model_account_panel_bankpayment->insertOcOrderPayment( $order_id, $ref, $order_no, 'REFUND SUCCESS', $bank_name, -abs($amountOfPayment), $datedRO, date("Y-m-d H:i:s"), 'bank_transfer', 1, $user_id, 'payment', $payment_id, $payment_sub_id);
                        }

                        if ( $ledgerNameInLedgerTable == 0 ){
                            //$ledger_id_new = $this->model_account_panel_bankpayment->saveLedger( $ledgerName, $sundryDebtorsGroupID, $user_id, $datedCM, $user_array);

                            $ledger_id_new = $this->model_account_panel_bankpayment->saveLedger( $ledgerName, $sundryDebtorsGroupID, $CustomerName['customer_id'], $user_id, $datedCM, $user_array);

                            $gst_number = '';
                            if (empty($CustomerName['gst_number'])) {
                                $gst_number = 'N/A';
                            }
                            else
                            {
                                $gst_number = $CustomerName['gst_number'];   
                            }

                            $this->model_account_panel_bankpayment->updateCustomerLedger( $ledger_id_new, $CustomerName['customer_id']);

                            $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedRO, $ledger_id_new, $amountOfPayment, $order_no, $ref, $pmntID, $order_id, 0, 'not_applicable');
                        }
                        else
                        {
                            //Old Ledgers Case
                            $ledger_id_old = $this->model_account_panel_bankpayment->getLedgerIDInLedgerTable($ledgerName);
                            
                            $sql = $this->model_account_panel_bankpayment->insertPaymentSubCSV($payment_id, $payment_sub_id, $datedRO, $ledger_id_old, $amountOfPayment, $order_no, $ref, $pmntID, $order_id, 0, 'not_applicable');
                        }
                        $response['status'] =  51;
                    }
                }
            }
            else
            {
                $response['status'] =  50;
            }
        }
        echo json_encode( $response );
    }

    public function getReceiptByAmountLedgerID(){

        $this->load->model('account_panel/bankpayment');

        // $amount = $this->request->post['amount'];
        // $ledger_id = $this->request->post['ledgerid'];
        $payment_id = $this->request->post['payment_id'];
        
        $getPaymentData = $this->model_account_panel_bankpayment->getPaymentByID($payment_id);

        $amount = $getPaymentData['amount'];
        $ledger_id = $getPaymentData['ledger_id'];

        $getReceiptByAmountLedgerID = $this->model_account_panel_bankpayment->getReceiptByAmountLedgerID($amount, $ledger_id);

        $response = array();
        if ( !$getReceiptByAmountLedgerID ){
            $response['norecord'] = 0;
            $response['getReceiptByAmountLedgerID'] =  '' ;
        }
        else
        {
            $response['norecord'] = 1;
            $response['getReceiptByAmountLedgerID'] =  $getReceiptByAmountLedgerID ;
        }

        echo json_encode($response);
    }

    public function addAjaxBounce() { //xxxxxxxxxxxxxxxxxxxxxxxxxxxxx

        $response = array();

        $this->load->model('account_panel/bankreceipt');//both
        $this->load->model('account_panel/bankpayment');//both

        ////1. Get All Values -- Start --- //////////////////

        $payment_id = $this->request->post['payment_id'];        
        $receipt_id = $_REQUEST['receipt_id'];

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        //$bounceLedgerID = 372;//local ledger id
        $bounceLedgerID = 3936;//live ledger id
        $bounceGroupID = 5;

        $getPaymentData = $this->model_account_panel_bankreceipt->getPaymentByID($payment_id);
        $getReceiptData = $this->model_account_panel_bankreceipt->getReceiptByID($receipt_id);
        //echo "<pre>";print_r($getReceiptData);die;
        //echo $getReceiptData['ledger_id'];
        //echo "<pre>";print_r($getReceiptData);die;

        $response = array();
        if ( !$getReceiptData ){
            $response['status'] =  1;
            echo json_encode($response) ;
            exit();
        }
        else
        {
            if (strlen($payment_id) > 0 && is_numeric($payment_id) && strlen($receipt_id) > 0 && is_numeric($receipt_id))
            {
                ////6(a). Insertion Updation in Payment Sub  /////
                $file_path = "";
                $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                if ( !$checkPaymentSubForPaymentID ) {
                    $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $bounceLedgerID, $bounceGroupID, $getPaymentData['amount'], $file_path, $user_id, $datedCM, $user_array );
                } else {
                    $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                    $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                    $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                    //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                    $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                    $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $bounceLedgerID, $bounceGroupID, $getPaymentData['amount'], $file_path, $user_id, $datedCM, $user_array );
                }

                ////6(a). Insertion Updation in Receipt Sub  /////


                $sql = $this->model_account_panel_bankreceipt->updateNoOfOrders($receipt_id, 0);
                $checkReceiptSubForReceiptID = $this->model_account_panel_bankreceipt->getReceiptSubForReceiptID( $receipt_id);
                if ( !$checkReceiptSubForReceiptID ) {
                   $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $bounceLedgerID, $bounceGroupID, $getReceiptData['amount'], $user_id, $datedCM, $user_array );
                } else {
                    //it will not enter in this section any how.
                    $this->model_account_panel_bankreceipt->updateReceiptSub($receipt_id);
                    $this->model_account_panel_bankreceipt->updateReceiptSubCSV($receipt_id);
                    $this->model_account_panel_bankreceipt->updateReceiptSubChargesDr($receipt_id);
                    //$this->model_account_panel_bankreceipt->updateOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//20072017
                    $this->model_account_panel_bankreceipt->deleteOcOrderPayment($receipt_id, 'receipt', 'REFUND SUCCESS');//15112017
                    $this->model_account_panel_bankreceipt->deleteOcOrderPaymentCOD($receipt_id, 'receipt');//17112017
                    $this->model_account_panel_bankreceipt->updateOcOrderPaymentDS($receipt_id, 'receipt');//15112017 
                    $this->model_account_panel_bankreceipt->insertReceiptSubForLedger( $receipt_id, $bounceLedgerID, $bounceGroupID, $getReceiptData['amount'], $user_id, $datedCM, $user_array );
                }

                $response['status'] =  51;
            }
            
        }
        echo json_encode( $response );
    }//end function

    public function addAjaxBounce2() {

        $response = array();

        $this->load->model('account_panel/bankpayment');    

        ////1. Get All Values -- Start --- //////////////////
        
        $ledgerid = $this->request->post['ledgerid'];
        $groupid = $this->request->post['groupid'];
        $payment_id = $_REQUEST['payment_id'];
        $amountOfPayment = $_REQUEST['amountOfPayment'];
        $datedRO = $this->request->post['datedROAA'];

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $datedCM = date("Y-m-d H:i:s");
        $user_array = array(
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'date' => $datedCM
                            );

        //$bounceLedgerID = 372;//local ledger id
        $bounceLedgerID = 3936;//live ledger id
        $bounceGroupID = 5;

        //echo "<pre>";print_r($LedgerDetail);die;
        if ($ledgerid == "" || strlen($ledgerid) == 0 || !is_numeric($ledgerid)) {
            $response['status'] =  1;
        }
        else if ($groupid == "" || strlen($groupid) == 0 || !is_numeric($groupid)) {
            $response['status'] =  1;
        }
        else if ($payment_id == "" || strlen($payment_id) == 0 || !is_numeric($payment_id)) {
            $response['status'] =  1;
        }
        else if ($amountOfPayment == "" || strlen($amountOfPayment) == 0 || !is_numeric($amountOfPayment)) {
            $response['status'] =  1;
        }
        else if ($datedRO == "" || strlen($datedRO) == 0) {
            $response['status'] =  1;
        }
        else
        {
            if ($groupid == $bounceGroupID)
            {
                if (strlen($ledgerid) > 0 && is_numeric($ledgerid) && strlen($groupid) > 0 && is_numeric($groupid) && strlen($payment_id) > 0 && is_numeric($payment_id) && strlen($amountOfPayment) > 0 && is_numeric($amountOfPayment) && strlen($datedRO) > 0)
                {
                    ////6(a). Insertion Updation in Payment Sub  /////
                    $file_path = "";
                    $checkPaymentSubForPaymentID = $this->model_account_panel_bankpayment->getPaymentSubForPaymentID( $payment_id);
                    if ( !$checkPaymentSubForPaymentID ) {
                        $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                    } else {
                        $this->model_account_panel_bankpayment->updatePaymentSub($payment_id);
                        $this->model_account_panel_bankpayment->updatePaymentSubCSV($payment_id);
                        $this->model_account_panel_bankpayment->updatePaymentSubIncomesCr($payment_id);
                        //$this->model_account_panel_bankpayment->updateOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//20072017
                        $this->model_account_panel_bankpayment->deleteOcOrderPayment($payment_id, 'payment', 'REFUND SUCCESS');//15112017
                        $this->model_account_panel_bankpayment->insertPaymentSubForLedger( $payment_id, $ledgerid, $groupid, $amountOfPayment, $file_path, $user_id, $datedCM, $user_array );
                    }

                    $response['status'] =  51;                     
                }
            }
        }
      
        echo json_encode( $response );
    }//end function




}

?>
