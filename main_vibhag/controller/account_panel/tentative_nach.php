<?php
class ControllerAccountPanelTentativeNach extends Controller {
	public function index() {
		$this->document->setTitle('Tentative NACH Sheet');
		$this->getList();
	}

	/**
	 * @info: Public method to show display report for tentative NACH schedules which are already generated
	 * @author: Nishu, May 2019
	*/
	protected function getList() {
		
		$token       = $this->session->data['token'] ?? '';
        
        // Filter Data
        $filter_data = $this->validateFilters($this->request->post);
        
        $general_url = '';
        // Populating URL based on filter - although this page is getting filters via POST
        // Following code can serve as an idea for other pages in khufiya
        /*
        foreach ( $filter_data as $filter => $value ) {
            $general_url .= '&' . $filter . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
        }
        */

		$data = $filter_data; // Initializing the data array to be passed on to template files, which would also need filter data

		$this->load->autoLoadLanguage('accounts/tentative_nach', $data);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => 'Home',
			'href' => $this->url->link('common/dashboard', 'token=' . $token, 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => 'Tentative NACH Sheet',
			'href' => $this->url->link('account_panel/tentative_nach', 'token=' . $token . $general_url, 'SSL')
		);

		//Get result for all tentative NACH for given filters
		$nach_obj = new NachBehaviour($this->registry);
		$results  = $nach_obj->getNachSchedulesWithAdditionalInfo($filter_data);
        
		//Group data by customer wise
		$results  = $nach_obj->groupDataByCustomer($results);

        $dates_for_sort = array(); // initializing array to sort the data by nach_debit_date
        
		// Loop on grouped data for further processing
		foreach ($results as $comp_key => $customer_wise_data) {
            
            // Group by order within each customer wise data
			$results[$comp_key]['nach_schedules'] = $nach_obj->groupDataByOrder($customer_wise_data['nach_schedules']);
            
            // Get timestamp of the dates at first level to sort for display
            $dates_for_sort[] = strtotime($customer_wise_data['nach_debit_date'] ?? '');
            
            // Add URL for Edit Customer Credits page
            $customer_id = (int)($customer_wise_data['customer_id'] ?? 0);
            $results[$comp_key]['customer_credits_link'] = $this->url->link('sale/customer_credits', 
                                                                            'token=' . $token .
                                                                            '&customer_id=' . $customer_id, 'SSL');
                                                                            
            // Loop over Orders and add Order list page URL
            foreach ($results[$comp_key]['nach_schedules'] as $oid => $sch) {
                $order_no = $sch['order_no'] ?? '';
                $results[$comp_key]['nach_schedules'][$oid]['order_list_link'] = $this->url->link('sale/order',
                                                                                                  'token=' . $token . 
                                                                                                  '&filter_order_no=' . $order_no, 'SSL');
            }
		}

        // Sort on nach_debit_date in ASCENDING order
        array_multisort($dates_for_sort, SORT_ASC, SORT_NUMERIC, $results);
        
        $data['tentative_nach'] = $results;
        
        // Get possible options for Status to show in filter
        $data['status_options'] = $this->db->getEnumValues(DB_PREFIX . 'wsb_credit_nach_schedule', 'status');

		$data['token'] = $token;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('account_panel/tentative_nach_sheet.tpl', $data));
	}
    
    /*
     * Private method to sanitize and set default (if needed) for the 
     * filters input, on the list page / download csv button, etc
     * 
     * @param $source - Array - eg: POST / GET Request array
     * 
     * @return Array - $filter_data
     */
    private function validateFilters(array $source) : array {
        
        // FILTERS - Sanitization and Setting default (if not provided and needed)
        $filter_data = array();
		$filter_data['filter_order_no']              = $source['filter_order_no'] ?? NULL;
		$filter_data['filter_customer_name']         = $source['filter_customer_name'] ?? NULL;
		$filter_data['filter_customer_id']           = $source['filter_customer_id'] ?? NULL;
		$filter_data['filter_umrn_lan']              = $source['filter_umrn_lan'] ?? NULL;
        $filter_data['filter_today_active_checksum'] = (int)($source['filter_today_active_checksum'] ?? 1);
        $filter_data['filter_nach_debit_date_from']  = $source['filter_nach_debit_date_from'] ?? date('Y-m-d');
        $filter_data['filter_nach_debit_date_to']    = $source['filter_nach_debit_date_to'] ?? date('Y-m-d');
        $filter_data['filter_status']                = $source['filter_status'] ?? 'NOT_DONE';
        $filter_data['filter_deffered_by_customer']  = (int)($source['filter_deffered_by_customer'] ?? 0);
        
        return $filter_data;        
    }

	/**
	 * @info: Public method to download Today's Bank sheet, for uploading to Bank.
	 * @author: Nishu, March 2019
	*/
	public function downloadTodayBankSheet(){
		$filter_data = array();
        $filter_data['filter_today_active_checksum'] = 1;

		//Get result for all tentative NACH for given filters
		$nach_obj = new NachBehaviour($this->registry);
		$results  = $nach_obj->getNachSchedulesWithAdditionalInfo($filter_data);
        
        //To group by customer 
        $results = $nach_obj->groupSchedulesByCustomer($results);

        // Generate Csv files, Zip them up, Download and cleanup at the end
		$bank_sheets     = $nach_obj->generateBankSheet($results);
        $zip_file_name   = 'nach_sheet_'.date('dmY');
		createAndDownloadZip($bank_sheets, $zip_file_name, true, true, true);

		exit();
	}

	/**
	 * @info: Public method to download NACH CSV with details, 
     *        based on the applied filters (through GET request).
	 * @author: Nishu, March 2019
	*/
	public function downloadCsv(){

		// Filter Data
        $filter_data = $this->validateFilters($this->request->get);
        
		//Get result for all tentative NACH for given filters
		$nach_obj = new NachBehaviour($this->registry);
		$results  = $nach_obj->getNachSchedulesWithAdditionalInfo($filter_data);

		//Group data customer wise
		$results  = $nach_obj->groupDataByCustomer($results);

		//Then, group the data further order wise, within each customer
		foreach ($results as $comp_key => $customer_wise_data) {
			$results[$comp_key]['nach_schedules'] = $nach_obj->groupDataByOrder($results[$comp_key]['nach_schedules']);
		}

        //Download CSV
		$this->_getCsv($results);
	}

	/**
	 * @info: Private method to download .csv file of the NACH schedule breakup data.
     * Download file name is tentative_nach.csv
	 * @param: array
	 * @author: Nishu, May 2019
	*/
	private function _getCsv(array $results) : void {

        $file_name = DIR_DOWNLOAD . 'tentative_nach.csv';
        $fp = fopen($file_name, 'w');

        $data = array(
            'Debit Date',
            'Customer Id',
            'Customer Name',
            'NACH A/c Name',
            'NACH A/c No',
            'IFSC Code',
            'UMRN No', 
            'LAN No', 
            'Bank Type',
            'Total Debit Amount (For Customer)',
            'Order No (Order Id)',
            'Order Balance',
            'Suborder ID',
        	'Schedule Id',
            'Debit Amount', 
            'Status', 
            'Deffered by Customer' 
            );
        fputcsv($fp, $data);

        //Loop over data to download
        foreach ($results as $comp_key => $customer_wise_data) {
        	
        	//Loop over data order wise schedule(s)
        	foreach ($customer_wise_data['nach_schedules'] as $order_id => $order_wise_data) {
        		
	        	//Loop over schedules
	        	foreach ($order_wise_data['nach_schedules'] as $key => $schedule) {
       				
       				$data = array();

	        		$data[0] = $customer_wise_data['nach_debit_date'];
		        	$data[1] = $customer_wise_data['customer_id'];
		        	$data[2] = $customer_wise_data['customer_name'];

		        	$data[3] = $customer_wise_data['account_name'];
		        	$data[4] = $customer_wise_data['account_no'];
		        	$data[5] = $customer_wise_data['ifsc_code'];
		        	$data[6] = $customer_wise_data['umrn_no'];
                    $data[7] = $customer_wise_data['lan_no'];
		        	$data[8] = $customer_wise_data['bank_type'];
		        	$data[9] = $customer_wise_data['total_nach_debit_amount'];

		        	$data[10] = $order_wise_data['order_no'] . ' ('. $order_wise_data['order_id'] . ')' ;
	        	 	$data[11] = $order_wise_data['order_bal'] ?? '--';

	        		$data[12] = $schedule['suborder_id'];
		        	$data[13] = $schedule['nach_schedule_id'];
		        	$data[14] = $schedule['nach_debit_amount'];
                    $data[15] = $schedule['status'];
                    $data[16] = $schedule['deffered_by_customer'];
                    
		        	fputcsv($fp, $data);
	        	}
        	}
        }

        fclose($fp);

        if (file_exists($file_name)) {
            header('Content-Description: File Transfer');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_name));
            readfile($file_name);
            exit();
        }
    }

	/*
     * Public Route (method) to mark a given NACH Schedule as Deffered by Customer.
     *
     * Required keys in the POST request:
     *  - 'nach_schedule_id' : NACH Schedule ID ; Int ; Should be non empty
     * 
     * Response is a String. It will be empty, if there are no errors
     */
	public function markDefferedByCustomer(){
        
        $nach_schedule_id = (int)($this->request->post['nach_schedule_id'] ?? 0);
        
        $nach = new NachBehaviour($this->registry);
        $data = $nach->deferNachSchedule( $nach_schedule_id );
        
        if(!empty($data)){
            $message = implode(' | ', $data);
            echo $message; exit();
        }
	}

	/*
     * Public Route (method) to change amount in a given NACH Schedule, to a given new amount
     *
     * Required keys in the POST request:
     *  - 'nach_schedule_id' : NACH Schedule ID ; Int ; Should be non empty
     *  - 'updated_amount'   : New Amount for updation ; Float ; Should be positive ( > 0 )
     *  - 'comment'     : Comment on why amount is being changed ; String ; Should be non empty (after trimming)
     * 
     * Response is a String. It will be empty, if there are no errors
     */
	public function updateNachAmount(){
        
        $nach_schedule_id = (int)($this->request->post['nach_schedule_id'] ?? 0);
        $updated_amount   = (float)($this->request->post['updated_amount'] ?? 0.00);
        $comment          = $this->request->post['comment'] ?? '';

        $nach = new NachBehaviour($this->registry);
        $data = $nach->changeAmountInNachSchedule( $nach_schedule_id, $updated_amount, $comment);
        if(!empty($data)){
            $message = implode(' | ', $data);
            echo $message; exit();
        }
	}

	/*
     * Public Route (method) to shift a given NACH Schedule, to another given date.
     *
     * Required keys in the POST request:
     *  - 'nach_schedule_id' : NACH Schedule ID ; Int ; Should be non empty
     *  - 'new_date'    : Date to which schedule will be shifted to ; Can be Empty ; If not empty, String in YYYY-MM-DD format
     *  - 'comment'     : Comment on why shifting is being done ; String ; Should be non empty (after trimming)
     * 
     * Response is a String. It will be empty, if there are no errors
     */
	public function shiftNachSchedule(){
		
        $nach_schedule_id = (int)($this->request->post['nach_schedule_id'] ?? 0);
        $new_date         = $this->request->post['new_date'] ?? '';
        $comment          = $this->request->post['comment'] ?? '';

        $nach = new NachBehaviour($this->registry);
        $data = $nach->shiftNachSchedule( $nach_schedule_id, $new_date, $comment );
        
        if(!empty($data)){
            $message = implode(' | ', $data);
            echo $message; exit();
        }
	}

	/*
     * Public Route (method) to get NACH schedule history of a given nach_schedule_id
     * 
     * Required keys in the POST request:
     *  - 'nach_schedule_id' : NACH Schedule ID ; Int ; Should be non empty
     * 
     * Response is a Json encoded array of Log (history) records, if available.
	 */
	public function getScheduleHistory(){
        
        $nach_schedule_id = (int)($this->request->post['nach_schedule_id'] ?? 0);
        $nach = new NachBehaviour($this->registry);
        $data = $nach->getScheduleHistory( $nach_schedule_id );
		echo json_encode($data); exit();
	}

	/*
     * Public Route (method) to Defer NACH schedule(s) of a given customer on a given date
     *
     * Required keys in the POST request:
     *  - 'customer_id' : Customer Id ; Int ; Should be non empty
     *  - 'deffer_date' : Date, whose schedules are to be deffered ; String in YYYY-MM-DD format ; Should be non empty
     * 
     * Response is a String. It will be empty, if there are no errors
     */
	public function deferNachSchedulesOfCustomer(){

        $customer_id = (int)($this->request->post['customer_id'] ?? 0);
        $deffer_date  = $this->request->post['deffer_date'] ?? '';
        
        $nach = new NachBehaviour($this->registry);
        $data = $nach->deferNachSchedulesOfCustomer( $customer_id, $deffer_date );
        
        if(!empty($data)){
            $message = implode(' | ', $data);
            echo $message; exit();
        }
	}

	/*
     * Public Route (method) to shift NACH schedule(s) of a given customer, on a given date to another given date.
     *
     * Required keys in the POST request:
     *  - 'customer_id' : Customer Id ; Int ; Should be non empty
     *  - 'given_date'  : Date, whose schedules are to be shifted ; String in YYYY-MM-DD format ; Should be non empty
     *  - 'new_date'    : Date to which schedules will be shifted to ; Can be Empty ; If not empty, String in YYYY-MM-DD format
     *  - 'comment'     : Comment on why shifting is being done ; String ; Should be non empty (after trimming)
     * 
     * Response is a String. It will be empty, if there are no errors
     */
    public function shiftNachSchedulesOfCustomer(){

        $customer_id = (int)($this->request->post['customer_id'] ?? 0);
        $given_date  = $this->request->post['given_date'] ?? '';
        $new_date    = $this->request->post['new_date'] ?? '';
        $comment     = $this->request->post['comment'] ?? '';

        $nach = new NachBehaviour($this->registry);
        $data = $nach->shiftNachSchedulesOfCustomer( $customer_id, $given_date, $new_date, $comment );
        
        if(!empty($data)){
            $message = implode(' | ', $data);
            echo $message; exit();
        }
	}

	/**
     * Public function to regenrate Pending NACH shcedule(s), for specific customer_id
     * Only if some NACH schedule(s) are already generated 
     * @author: Nishu, May 2019 
    */
	public function regenerateScheduleOfCustomer(){
        
        exit(); // Need Rewrite
		/*
		//Checks if data is set into post params
		if( !empty($this->request->post['customer_id']) ){
			$customer_id = (int)$this->request->post['customer_id'];
			try{
				$nach = new NachBehaviour($this->registry);
				$data = $nach->regenerateScheduleOfCustomer( $customer_id );
				if(!empty($data)){
					echo json_encode($data); exit();
				}

			}catch (\Throwable $exception) {

				//Mail to track exception, to resolve issue
				mailException($exception, $this->config);
				
				echo "Contact Administrator!" . "</br>";
				echo "Exception Error:  ". $exception->getCode(). ': ' . $exception->getMessage(). ", " . $exception->getFile() .",  ". $exception->getLine();
				exit();
			}

		}else{
			echo "Customer Id Not found for regenrate Schedule(s)";
			exit();
		} */
	}

	/**
	 * @info: Public method to download NACH sheet For Unverified NACH schedules, With following filter(s)
	 *      	filter_nach_debit_date_from: no value
	 *			filter_nach_debit_date_to : current date - 3 days
	 *			order payment id is null
	 *			deffered_by_customer = 0
     *			status = NOT_DONE, BANK_PENDING
	 * @author: Nishu, June 2019
	*/
	public function downloadUnverifiedSchedule(){

		//Get result for all tentative NACH for given filters
		$nach_obj = new NachBehaviour($this->registry);
		$results  = $nach_obj->getUnverifiedSchedule();

        //Download CSV
		$file_names = $this->_getCsvUnverifiedSchedule($results);
		exit();
	}

	/**
	 * @info: Private method to prepare and download .csv of Unverified Receipt/Failed Schedules by Accounts
	 * @param: array
	 * @author: Nishu, May 2019
	*/
	private function _getCsvUnverifiedSchedule(array $results) {
        $file_name = DIR_DOWNLOAD . 'tentative_nach_'.date('ymdhis').'.csv';
        $fp = fopen($file_name, 'w');

		if(!empty($results)){
			$header = array(
			            'NACH Schedule Id',
			            'Suborder Id',
			            'NACH Debit Date',
			            'NACH Debit Amount',
			            'Status',
			            'Customer NACH Details Id',
			            'Deffered By Customer',
			        	'Date Added',
			            'Date Modified'
			           );
        	fputcsv($fp, $header);
        	//Loop over data to download
	        foreach ($results as $key => $data) {
	        	
        		$row  = array(
        					$data['nach_schedule_id'],
        					$data['suborder_id'],
        					$data['nach_debit_date'],
        					$data['nach_debit_amount'],
        					$data['status'],
        					$data['customer_nach_details_id'],
        					$data['deffered_by_customer'],
        					$data['date_added'],
        					$data['date_modified']
        			    );
	        	fputcsv($fp, $data);
		        	
	        }

		}else{
			$data = array("No data for CSV.");
			fputcsv($fp, $data);
		}

		//Close File object for writing data.
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

    public function addNachSchedule(){
        $resp = array();

        if(!empty($this->request->post)){
            $nach_debit_date   = $this->request->post['nach_debit_date'] ?? 0;
            $suborder_id       = $this->request->post['suborder_id'] ?? '';
            $nach_debit_amount = $this->request->post['nach_debit_amount'] ?? '';
            $comment           = $this->request->post['comment'] ?? '';



        }else{
            $resp['status']  = 'error';
            $resp['message'] = 'Required Data is not passed!!';
        }


        $resp['status']  = 'error';
        $resp['message'] = 'Required Data is not passed!!';
 
        echo json_encode($resp);exit;
    }
	
}
