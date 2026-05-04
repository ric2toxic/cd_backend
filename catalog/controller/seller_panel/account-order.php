<?php

class ControllerSellerPanelAccountOrder extends Controller {

	private $_obj_order_stores;
	private $record_limits = 15;

	public function __construct( $registry) {
		parent::__construct($registry);
		$this->_obj_order_stores = new OrderStores();
	}

	public function index() {
		
		if (!$this->customer->isLogged()) { 
	  		$this->response->redirect($this->url->link('account/login', '', 'SSL'));
    	}
		$this->getPickpupOrderRequested();
	}


	/* Method to get pickup order requested
	 * @return NULL
	 * @author Vikas, 2017
	 * last modified on 21th Nov. 2017 kalyan (removed sor orders from listing and everywhere)
	 */
	public function getPickpupOrderRequested(){		
		
		$data 				= array();
		
		$this->load->autoLoadLanguage('seller_panel/sellers',$data);

		$data['seller_id'] 	= $this->customer->getId();
		

		if( !empty($this->request->get['filter_order_no_requested']) ){

			$filter_order_no_requested = $this->request->get['filter_order_no_requested'];
		} else {

			$filter_order_no_requested = NULL;
		}
		if( !empty($this->request->get['filter_order_processing_date_from']) ){

			$filter_order_processing_date_from = $this->request->get['filter_order_processing_date_from'];
		} else {

			$filter_order_processing_date_from = NULL;
		}
		if( !empty($this->request->get['filter_order_processing_date_to']) ){

			$filter_order_processing_date_to = $this->request->get['filter_order_processing_date_to'];
		} else {

			$filter_order_processing_date_to = NULL;
		}
		if( !empty($this->request->get['filter_order_amount_from']) ){

			$filter_order_amount_from = $this->request->get['filter_order_amount_from'];
		} else {

			$filter_order_amount_from = NULL;
		}
		if( !empty($this->request->get['filter_order_amount_to']) ){

			$filter_order_amount_to = $this->request->get['filter_order_amount_to'];
		} else {

			$filter_order_amount_to = NULL;
		}
		if( !empty($this->request->get['filter_record_range'])  ){

			$filter_record_range = $this->request->get['filter_record_range'];
		} else {

			$filter_record_range = $this->record_limits;
		}

		if( !empty($this->request->get['edit_type_status'])  ){
			$edit_type_status = $this->request->get['edit_type_status'];
		} else {
			$edit_type_status = null;
		}

		if ( !empty($this->request->get['sort']) ) {

			$sort = $this->request->get['sort'];
		} else {

			$sort = 'order_processing_date';
		}
		if ( !empty($this->request->get['order']) ) {

			$order = $this->request->get['order'];
		} else {

			$order = 'DESC';
		}
		if ( !empty($this->request->get['page']) ) {

			$page = $this->request->get['page'];
		} else {

			$page = 1;
		}
		// General URL (without sort or page)
		$url = '';

		if( !empty($this->request->get['filter_order_no_requested']) ){

			$url .= '&filter_order_no_requested='. $this->request->get['filter_order_no_requested'];
		}
		if( !empty($this->request->get['filter_order_processing_date_from']) ){

			$url .= '&filter_order_processing_date_from='. $this->request->get['filter_order_processing_date_from'];
		}
		if( !empty($this->request->get['filter_order_processing_date_to']) ){

			$url .= '&filter_order_processing_date_to='. $this->request->get['filter_order_processing_date_to'];
		}
		if( !empty($this->request->get['filter_order_amount_from']) ){

			$url .= '&filter_order_amount_from='. $this->request->get['filter_order_amount_from'];
		}
		if( !empty($this->request->get['filter_order_amount_to']) ){

			$url .= '&filter_order_amount_to='. $this->request->get['filter_order_amount_to'];
		
		}
		if( !empty($this->request->get['filter_record_range']) ){

			$url .= '&filter_record_range='. $this->request->get['filter_record_range'];
		}
		if( !empty($this->request->get['edit_type_status']) ){

			$url .= '&edit_type_status='. $this->request->get['edit_type_status'];
		}
		// URL for General links to ensure we reach same settings again on the list page
        $general_url = $url;

        if (isset($this->request->get['sort'])) {

            $general_url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {

            $general_url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {

            $general_url .= '&page=' . $this->request->get['page'];
        }		

		$filter_data = array(
			
			'filter_order_no_requested' 	   => $filter_order_no_requested,
			'filter_order_processing_date_from'=> $filter_order_processing_date_from,
			'filter_order_processing_date_to'  => $filter_order_processing_date_to,
			'filter_order_amount_from' 	 	   => $filter_order_amount_from,
			'filter_order_amount_to' 	 	   => $filter_order_amount_to,
			'edit_type_status' 	 	   		   => $edit_type_status,
			'sort'                 	 		   => $sort,
			'order'                	 		   => $order,
			'start'                	 		   => ($page - 1) * $filter_record_range,
			'limit'                	 		   => $filter_record_range
		);

		$seller_id = $this->customer->getId();
		
		$getPickpupOrderRequested = $this->_obj_order_stores->getPickpupOrderRequested( $this->db, $seller_id, $filter_data);
		
		$total_results = $this->_obj_order_stores->getTotalOrderPickpupRequested( $this->db, $seller_id, $filter_data);

		$data['pickup_requested'] = array();

		if( !empty($getPickpupOrderRequested) ){
			foreach($getPickpupOrderRequested as $order_id_key => $orders_data ){

				foreach($orders_data as $pickuporder_value){

					//*****************************************//
					// Date Range (create Invoice date range) //
					//***************************************//
					$ord_prcesing_date = date('d-m-Y',strtotime($pickuporder_value['order_processing_date']));
					$current_date = date('d-m-Y');
					$before_two_days = date('d-m-Y', strtotime('-2 days', strtotime($current_date)));
					$date_array = array($ord_prcesing_date,$before_two_days);
					$max_date = max(array_map('strtotime', $date_array));
					$start_date = date('d-m-Y',$max_date);
					
					$begin = new DateTime($start_date);
					$end = new DateTime($current_date);
					$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
					$date_ranges = array();
					foreach($daterange as $date){
					    $date_ranges[] = $date->format("d F Y");
					}
					$date_ranges[] = date('d F Y', strtotime($current_date));
					//******************//
					// End Date Range  //
					//****************//


					// enable or disable seller edited after generating invoice
					$data['product_received'] = true;
					$pickup_status = $this->_obj_order_stores->getSellerInvoiceGenerated($this->registry, $this, $pickuporder_value['order_id'], $pickuporder_value['suborder_id'], $seller_id);

					$data['pickup_requested'][$order_id_key][$pickuporder_value['suborder_id']] = array(
						'order_id' 				=> $pickuporder_value['order_id'],
						'suborder_id'        	=> $pickuporder_value['suborder_id'],
						'order_no' 				=> $pickuporder_value['order_no'],
						'order_processing_date'	=> $pickuporder_value['order_processing_date'], //$order_processing_date,
						'date_ranges'			=> $date_ranges,
						'total' 				=> $this->currency->format( $pickuporder_value['total'],'INR',1),
						'edit_type_status'		=> $pickuporder_value['edit_type_status'] ,
						'pending_orders'		=>	$this->_obj_order_stores->getOrderProducts($this, $pickuporder_value['order_id'],  $pickuporder_value['suborder_id'], $seller_id, array('YES','SELLER_LATER_DISPATCH')),
						'seller_not_given'		=>	$this->_obj_order_stores->getOrderProducts($this, $pickuporder_value['order_id'], $pickuporder_value['suborder_id'],$seller_id, array('SELLER_NOT_SUPPLIED')),
						'sllr_invc_genrted'		=>	$this->_obj_order_stores->getSellerInvoiceGenerated($this->registry,$this, $pickuporder_value['order_id'],$pickuporder_value['suborder_id'],$seller_id),
					);
				}
			}
		}

		// URL For sorting
        $sort_url = $url;

        if ($order == 'ASC') {
            $sort_url .= '&order=DESC';
        } else {
            $sort_url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $sort_url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order_proccessing_date'] = $this->url->link('seller_panel/account-order','&sort=order_processing_date' . $sort_url, 'SSL');


        // URL for pagination
        $pagination_url = $url;

        if (isset($this->request->get['sort'])) {
            $pagination_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $pagination_url .= '&order=' . $this->request->get['order'];
        }

		$data['pagination'] = '';
		$data['results'] = '';
		if($filter_record_range !='all'){
			$pagination = new Pagination();
	        $pagination->total = $total_results;
	        $pagination->page = $page;
	        $pagination->limit = $filter_record_range;
	        $pagination->url = $this->url->link('seller_panel/account-order/getPickpupOrderRequested'. $url .'&page={page}', 'SSL');

	        $data['pagination'] = $pagination->render();

	        $data['results'] = sprintf($data['text_pagination'], ($total_results) ? (($page - 1) * $filter_record_range) + 1 : 0, ((($page - 1) * $filter_record_range) > ($total_results - $filter_record_range)) ? $total_results : ((($page - 1) * $filter_record_range) + $filter_record_range), $total_results, ceil($total_results / $filter_record_range));
		}

		$filters = array();		
		array_walk( $this->request->get , function( &$val , $key ) use (&$filters){
			if( !strpos($key,'_to') ){
				if( strpos($key,'_from') ){
					$code = str_replace('_from','',$key);
					$to_key = $code.'_to';
					$filters[$key]['value'] = "$val - ". $this->request->get[$to_key]."";
					$filters[$key]['title'] = str_replace("_",' ',$code);
					$filters[$key]['title'] = str_replace("filters_requested",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val&".$to_key."=".$this->request->get[$to_key] . "";
				}
				else{
					$filters[$key]['value'] = "$val";
					$filters[$key]['title'] = str_replace("_",' ',$key);
					$filters[$key]['title'] = str_replace("filters_requested ",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val";
				}
			}
			
		});
		
		$data['filters_requested'] = array();
		unset($filters['route'],$filters['token'],$filters['filters_requested'],$filters['page'],$filters['SSL'],$filters['sort'],$filters['order']);
		
		foreach ( $filters as $key => $value) {											
				$f = $filters;
				unset($f[$key]);
				$url = implode( '', array_column($f, 'url') );
				$data['filters_requested'][$key] = $value;
				$data['filters_requested'][$key]['url'] = $this->url->link('seller_panel/account-order/getPickpupOrderRequested',$url,'SSL');
		}


		$data['pickup_requested_link'] = $this->url->link('seller_panel/account-order/getPickpupOrderRequested','','SSL');
		$data['pickup_done_link'] = $this->url->link('seller_panel/account-order/getPickupOrderDone','','SSL');
		$data['pickup_tentative_link'] = $this->url->link('seller_panel/account-order/getTentativeOrders','','SSL');
		$data['clear_all_link'] = $this->url->link('seller_panel/account-order/getPickpupOrderRequested','','SSL');
		$data['sor_order_link'] = $this->url->link('seller_panel/account-order/getSorOrders','','SSL');

		$data['filter_order_no_requested'] 	   		= $filter_order_no_requested;
		$data['filter_order_processing_date_from']	= $filter_order_processing_date_from;
		$data['filter_order_processing_date_to']  	= $filter_order_processing_date_to;
		$data['filter_order_amount_from'] 	 		= $filter_order_amount_from;
		$data['filter_order_amount_to'] 	 	 	= $filter_order_amount_to;
		$data['filter_record_range'] 	 	 		= $filter_record_range;
		$data['edit_type_status'] 	 	 			= $edit_type_status;
		$data['sort']								= $sort;
		$data['order']								= $order;

		$data['header'] = $this->load->controller('seller_panel/seller_header');
		$data['footer'] = $this->load->controller('seller_panel/seller_footer');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/seller_panel/order_pickup_requested.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/seller_panel/order_pickup_requested.tpl', $data));

		} else {
			$this->response->setOutput($this->load->view('default/template/seller_panel/order_pickup_requested.tpl', $data));
		}
	}
	/** 
	 * Method to set Mark Out Of Stock (out of stock (quantity = 0 and edit type = seller_not_given) )
	 * @request: array of request with order id , suborder id , array of order product id 	
	 * @return NULL
	 * @author Vikas, 2017
	 */
	public function setMarkOutOfStock(){
		if( empty( $this->request->post ) ){
			return false;
		}
		
		$this->_obj_order_stores->setMarkOutOfStock( $this->db, $this->request->post, $this->customer->getId() );

        $order_id  = $this->request->post['order_id'];

		$this->_obj_order_stores->SendNotificationToSalesStaff($this, $order_id);
		
		echo json_encode(array('success'=>'successfully')); exit();
	}
	/* Method to get Pickup Order Done
	 * @return NULL
	 * @author Vikas, 2017
 	 */
	public function getPickupOrderDone(){
		$data = array();
		$this->load->autoLoadLanguage('seller_panel/sellers',$data);

		$data['getQuarters'] = getQuarters();
		$data['getFinancialYears'] = getFinancialYears(2016, '');

		if( !empty($this->request->get['filter_month_range']) ){
			$filter_month_range = $this->request->get['filter_month_range'];
		} else {
			$filter_month_range = NULL;
		}

		if( !empty($this->request->get['filter_year_range']) ){
			$filter_year_range = $this->request->get['filter_year_range'];
		} else {
			$filter_year_range = NULL;
		}

		if( !empty($this->request->get['filter_order_no']) ){
			$filter_order_no = $this->request->get['filter_order_no'];
		} else {
			$filter_order_no = NULL;
		}

		if( !empty($this->request->get['filter_invoice_date_from']) ){
			$filter_invoice_date_from = $this->request->get['filter_invoice_date_from'];
		} else {
			$filter_invoice_date_from = NULL;
		}

		if( !empty($this->request->get['filter_invoice_date_to']) ){
			$filter_invoice_date_to = $this->request->get['filter_invoice_date_to'];
		} else {
			$filter_invoice_date_to = NULL;
		}

		if( !empty($this->request->get['filter_invoice_no']) ){
			$filter_invoice_no = $this->request->get['filter_invoice_no'];
		} else {
			$filter_invoice_no = NULL;
		}

		if( !empty($this->request->get['filter_sale_from']) ){
			$filter_sale_from = $this->request->get['filter_sale_from'];
		} else {
			$filter_sale_from = NULL;
		}

		if( !empty($this->request->get['filter_sale_to']) ){
			$filter_sale_to = $this->request->get['filter_sale_to'];
		} else {
			$filter_sale_to = NULL;
		}

		if( !empty($this->request->get['filter_payment_status'])  ){
			$filter_payment_status = $this->request->get['filter_payment_status'];
		} else {
			$filter_payment_status = NULL;
		}

		if( !empty($this->request->get['filter_record_range'])  ){
			$filter_record_range = $this->request->get['filter_record_range'];
		} else {
			$filter_record_range = $this->record_limits;
		}

		if ( !empty($this->request->get['sort']) ) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if ( !empty($this->request->get['order']) ) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if ( !empty($this->request->get['page']) ) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if ( !empty($this->request->get['download_pickup_done_report']) ) {
			$download_pickup_done_report = $this->request->get['download_pickup_done_report'];
		} else {
			$download_pickup_done_report = false;
		}
		if ( !empty($this->request->get['filter_sor_record']) ) {
			$filter_sor_record = $this->request->get['filter_sor_record'];
		} else {
			$filter_sor_record = false;
		}

		// General URL (without sort or page)
		$url = '';

		if( !empty($this->request->get['filter_order_no']) ){
			$url .= '&filter_order_no='. $this->request->get['filter_order_no'];
		}

		if( !empty($this->request->get['filter_invoice_date_from']) ){
			$url .= '&filter_invoice_date_from='. $this->request->get['filter_invoice_date_from'];
		}

		if( !empty($this->request->get['filter_invoice_date_to']) ){
			$url .= '&filter_invoice_date_to='. $this->request->get['filter_invoice_date_to'];
		}

		if( !empty($this->request->get['filter_invoice_no']) ){
			$url .= '&filter_invoice_no='. $this->request->get['filter_invoice_no'];
		}

		if( !empty($this->request->get['filter_sale_from']) ){
			$url .= '&filter_sale_from='. $this->request->get['filter_sale_from'];
		}

		if( !empty($this->request->get['filter_sale_to']) ){
			$url .= '&filter_sale_to='. $this->request->get['filter_sale_to'];
		}

		if( !empty($this->request->get['filter_payment_status']) ){
			$url .= '&filter_payment_status='. $this->request->get['filter_payment_status'];
		}

		if( !empty($this->request->get['filter_record_range']) ){
			$url .= '&filter_record_range='. $this->request->get['filter_record_range'];
		}
		if( !empty($this->request->get['filter_sor_record']) ){
			$url .= '&filter_sor_record='. $this->request->get['filter_sor_record'];
		}
		
		$seller_id = $this->customer->getId();
		$filter_data = array();

		$filter_data = array(
			'filter_order_no' 		 	=> $filter_order_no,
			'filter_invoice_date_from'	=> $filter_invoice_date_from,
			'filter_invoice_date_to' 	=> $filter_invoice_date_to,
			'filter_invoice_no' 	 	=> $filter_invoice_no,
			'filter_sale_from' 	 	 	=> $filter_sale_from,
			'filter_sale_to' 	 	 	=> $filter_sale_to,
			'filter_payment_status'     => $filter_payment_status,
			'sort'                 	 	=> $sort,
			'order'                	 	=> $order,
			'start'                	 	=> ($page - 1) * $filter_record_range,
			'limit'                	 	=> $filter_record_range,
			'download_pickup_done_report'=> $download_pickup_done_report,
			'filter_sor_record'		    => $filter_sor_record
		);

		$results = $this->_obj_order_stores->getOrderPickupDone( $this->db, $seller_id, $filter_data);

		$total_results = $this->_obj_order_stores->getTotalRecordPickupDone( $this->db, $seller_id, $filter_data);
		
		$data['order_pickup_data'] = array();
		
		if( !empty($results) ){
			foreach ($results['records'] as $key => $result) {
				// calculate amount of debit note of perticular order id
				$total_amt_debit_note = 0; 
				if(!empty($results['debit_notes'][$result['order_id']])){
					foreach($results['debit_notes'][$result['order_id']] as $debit_note_values){
						$total_amt_debit_note += $debit_note_values['debit_note_amount'];
					}
				}

				//get seller invoice download link
  				$seller_inv_dload_link = array();
  				$check_trxn_done = false;
	            if(!empty($results['seller_invoices'][$result['suborder_id']])){
	            	// array of seller invoice corresponding to order_id
	            	$seller_invoice_data = $results['seller_invoices'][$result['suborder_id']];
                    
	            	foreach($seller_invoice_data as $seller_inv_id_key => $seller_inv_id_value){
	            		if($seller_inv_id_value['trxn_done'] == 'BANK_REQUESTED' || $seller_inv_id_value['trxn_done'] == 'BANK_SUCCESS' || $seller_inv_id_value['trxn_done'] == 'NOT_APPLICABLE'){
	            			$check_trxn_done = true;
	            		}
	            		$seller_invoice = array();
	            		$seller_invoice['seller_invoice_id'] = (int)$seller_inv_id_value['seller_invoice_id'];
		               	$seller_invoice = serialize($seller_invoice);
		              	$secureFileDload = new SecureFileDownload($this->registry);
		              	$seller_inv_dload_link[$seller_inv_id_key]['label'] = $seller_inv_id_value['seller_invoice_prefix'].''.$seller_inv_id_value['seller_invoice_no'];
		               	$seller_inv_dload_link[$seller_inv_id_key]['url'] = $secureFileDload->getDownloadLink('seller_invoice', base64_encode($seller_invoice), false);
	            	}
	            }

	            // get download link of debit note
				$debit_note_url = array();

				if( !empty($results['debit_notes'][$result['suborder_id']]) ){
					$debit_note_data = $results['debit_notes'][$result['suborder_id']];
					foreach ($debit_note_data as $key => $value) {
						$encode_file = array();
						$encode_file['order_no'] = $result["order_no"];
						$encode_file['debit_note_id'] = $value['debit_note_id'];
					    $encode_file = base64_encode(serialize($encode_file));
						$dload_url['label'] = $value['debit_note_prefix'].''.$value['debit_note_no'];
						$dload_url['url'] = $this->securefiledownload->getDownloadLink('seller_debit_note',$encode_file, false);
					  	$debit_note_url[$key] = $dload_url;
					}
				}

				$net_payable_amount 	= $result['sale_amt'] - $total_amt_debit_note ;
				$sor_invoice_no 		= '';
				$sor_invoice_date 		= '';
				$paymentToolTip			= '';
				$sor_payment 			= array();	

				if (isset($result['sor_invoice_no'])) {

					$sor_invoice_no 		= $result['sor_invoice_no'];
					$sor_payment  = $this->_obj_order_stores->getSorOrderPayments($this->db, $result['order_id'], $seller_id );
				}
				if (isset($result['sor_invoice_date'])) {

					$sor_invoice_date 		= $result['sor_invoice_date'];
				}
				/*
				* set the string for payment tool tip
				*/
				if(!empty($results['seller_invoices'][$result['suborder_id']])){
					
					$paymentToolTip .= '<div class="paid_detail">';
		            $paymentToolTip .= '<ul>';

					foreach ($results['seller_invoices'][$result['suborder_id']] as $keyse => $valuese) {
						
						if($valuese['trxn_done'] == 'BANK_REQUESTED' || $valuese['trxn_done'] == 'BANK_SUCCESS' || $valuese['trxn_done'] == 'NOT_APPLICABLE'){

			                $paymentToolTip .=     '<li>Inv. No. ' . $valuese["seller_invoice_prefix"].''.$valuese["seller_invoice_no"] . '</li>';
			                $paymentToolTip .=     '<li>Amount. ' . (($valuese["trxn_amount"]) ? $valuese["trxn_amount"] : ''). '</li>';
			                
			                if($valuese["trxn_utr"]!='' && $valuese["trxn_utr_date"]!=''){
			                   
			                    $paymentToolTip .=     '<li>Ref. ' . (($valuese["trxn_utr"]) ? $valuese["trxn_utr"] : '' ).'</li>';
			                    $paymentToolTip .=     '<li>Date. ' . (($valuese["trxn_utr_date"]) ? $valuese["trxn_utr_date"] : '').'</li>';
			                }
			            }
		            }  

				}
				if(!empty($sor_payment)){

					if ($paymentToolTip=='') {

						$paymentToolTip .= '<div class="paid_detail">';
		            	$paymentToolTip .= '<ul>';
					}

					foreach ($sor_payment as $keysor => $valuesor) {
						
						if ($valuesor['trxn_done'] == 'BANK_REQUESTED' || $valuesor['trxn_done'] == 'BANK_SUCCESS' || $valuesor['trxn_done'] == 'NOT_APPLICABLE') {
							
							$check_trxn_done = true;

							$paymentToolTip .=     '<li>Sub Order. No. ' . $valuesor["suborder_id"].'</li>';
			                $paymentToolTip .=     '<li>Amount. ' . (($valuesor["total_paid_amount"]) ? $valuesor["total_paid_amount"] : ''). '</li>';
			                
			                if($valuesor["trxn_utr"]!='' && $valuesor["trxn_utr_date"]!=''){
			                   
			                    $paymentToolTip .=     '<li>Ref. ' . (($valuesor["trxn_utr"]) ? $valuesor["trxn_utr"] : '' ).'</li>';
			                    $paymentToolTip .=     '<li>Date. ' . (($valuesor["trxn_utr_date"]) ? $valuesor["trxn_utr_date"] : '').'</li>';
			                }
			            }
					}

				}
				if ($paymentToolTip!='') {

	                $paymentToolTip .= '</ul>';
	                $paymentToolTip .= '</div>'; 
				}
				/*
				* setting all data in array
				*/
				$data['order_pickup_data'][] = array(
					'order_id' 		=> $result['order_id'],
					'order_no' 		=> $result['order_no'],
					'suborder_id' 		=> $result['suborder_id'],
					'order_date_added' => (!empty($results['order_process_lists'][$result['order_id']]) ? $results['order_process_lists'][$result['order_id']]['order_processing_date'] : $result['order_date']),
					'sale' 			=> number_format($result['sale_amt'],2),
					'seller_invoices'=> (!empty($results['seller_invoices'][$result['suborder_id']]) ? $results['seller_invoices'][$result['suborder_id']] : array()),
					'debit_notes'=> (!empty($results['debit_notes'][$result['suborder_id']]) ? $results['debit_notes'][$result['suborder_id']] : array()),
					'return_amount' => number_format($total_amt_debit_note,2),
					'penalty_amount'=> 0,
					'net_payable_amount' => number_format($net_payable_amount,2),
					'trxn_done'		=> ($check_trxn_done) ? 'Paid' : ((!$net_payable_amount) ? '<p style="background:red;">Full Returned</p>' : ''),
					'product_data' 	=> $this->_obj_order_stores->getOrderProducts($this, $result['order_id'], $result['suborder_id'], $seller_id, array() ),
					'seller_invoice_link' => $seller_inv_dload_link,
					'debit_note_link'=> $debit_note_url,
					'sllr_invc_genrted'	=>	$this->_obj_order_stores->getSellerInvoiceGenerated($this->registry, $this, $result['order_id'], $result['suborder_id'], $seller_id ),
					'seller_sor_product'	=>	$this->_obj_order_stores->getOrderProducts($this, $result['order_id'],$result['suborder_id'], $seller_id, array(),'','get_sor'),
					'sor_invoice_no'=>$sor_invoice_no,
					'sor_invoice_date'=>$sor_invoice_date,
					'payment_tool_tip'=>$paymentToolTip,
				);
			}
		}
		

		if( isset($this->request->get['download_pickup_done_report']) && $this->request->get['download_pickup_done_report'] ){

			$download_results = $data['order_pickup_data'];

			$file_name = DIR_DOWNLOAD .'order_pickup_done.csv';
	        $fp = fopen($file_name, 'w');
	        
	        $data = array('Order No', 
	                      'Invoice / Debit No.', 
	                      'Invoice / Debit Note Date', 
	                      'Invoice / Debit Amount', 
	                      'Payment Done', 
	                      'Payment Amount', 
	                      'Payment Date',
	                      'Payment Reference No.');
	        fputcsv($fp, $data);
	        if( !empty($results) ) {

	            foreach ($download_results as $key => $sub_value) {
	            	foreach( $sub_value['seller_invoices'] as $invoice_key => $invoice_values ){
	            		$order_no 		= ($sub_value['order_no'])?$sub_value['order_no']: '';
	            		$invoice_no 	= $invoice_values['seller_invoice_prefix'] .'' . $invoice_values['seller_invoice_no'];
	            		$date_added 	= ($invoice_values['date_added']) ? date('d-m-Y',strtotime($invoice_values['date_added'])) : '';
	            		$sale 			= ($invoice_values['seller_inv_amount']) ? $invoice_values['seller_inv_amount'] : '';
	            		$payment_done 	= ($invoice_values['trxn_done']) ? $invoice_values['trxn_done'] : '';
	            		$payment_amount = ($invoice_values['trxn_amount']) ? $invoice_values['trxn_amount'] : '';
	            		$payment_date 	= ($invoice_values['trxn_utr_date']) ? date('d-m-Y',strtotime($invoice_values['trxn_utr_date'])) : ''; 
	            		$payment_reference_no= ($invoice_values['trxn_utr']) ? $invoice_values['trxn_utr'] : ''; 
            			$data = array($order_no, 
		                              $invoice_no,
		                              $date_added, 
		                              $sale, 
		                              $payment_done, 
		                              $payment_amount, 
		                              $payment_date, 
		                              $payment_reference_no);
                		fputcsv($fp, $data);
	            	}
	            }

        		foreach($download_results as $sub_debit_key => $sub_debit_value){
        			if(!empty($sub_debit_value['debit_notes'])){
	        			foreach( $sub_debit_value['debit_notes'] as $debit_key => $debit_values ){
	        				$order_no 			= ($sub_debit_value['order_no']) ? $sub_debit_value['order_no']: '';
		            		$debit_no 			= $debit_values['debit_note_prefix'] .'' . $debit_values['debit_note_no'];
		            		$date_added 		= ($debit_values['date_added']) ? date('d-m-Y',strtotime($debit_values['date_added'])) : '';
		            		$debit_note_amount	= ($debit_values['debit_note_amount']) ? $debit_values['debit_note_amount'] : '';
		            		$debit_payment_done	= ($debit_values['trxn_done']) ? $debit_values['trxn_done'] : '';
		            		$debit_payment_amount= ($debit_values['trxn_amount']) ? $debit_values['trxn_amount'] : '';
		            		$debit_payment_date	= ($debit_values['trxn_utr_date']) ? date('d-m-Y',strtotime($debit_values['trxn_utr_date'])) : ''; 
		            		$debit_payment_reference_no= ($debit_values['trxn_utr']) ? date('d-m-Y',strtotime($debit_values['trxn_utr'])) : ''; 
	            			$data = array($order_no, 
			                              $debit_no,
			                              $date_added, 
			                              $debit_note_amount, 
			                              $debit_payment_done, 
			                              $debit_payment_amount, 
			                              $debit_payment_date, 
			                              $debit_payment_reference_no);
	                		fputcsv($fp, $data);
	        			}
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

		$data['pagination'] = '';
		$data['results'] = '';
		if($filter_record_range !='all'){
			$pagination = new Pagination();
	        $pagination->total = $total_results;
	        $pagination->page = $page;
	        $pagination->limit = $filter_record_range;
	        $pagination->url = $this->url->link('seller_panel/account-order/getPickupOrderDone'. $url .'&page={page}', 'SSL');

	        $data['pagination'] = $pagination->render();

	        $data['results'] = sprintf($data['text_pagination'], ($total_results) ? (($page - 1) * $filter_record_range) + 1 : 0, ((($page - 1) * $filter_record_range) > ($total_results - $filter_record_range)) ? $total_results : ((($page - 1) * $filter_record_range) + $filter_record_range), $total_results, ceil($total_results / $filter_record_range));
		}
		
		$filters = array();
		array_walk( $this->request->get , function( &$val , $key ) use (&$filters){

			if( !strpos($key,'_to') ){
				if( strpos($key,'_from') ){
					$code = str_replace('_from','',$key);
					$to_key = $code.'_to';
					$filters[$key]['value'] = "$val-". $this->request->get[$to_key]."";
					$filters[$key]['title'] = str_replace("_",' ',$code);
					$filters[$key]['title'] = str_replace("filter ",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val&".$to_key."=".$this->request->get[$to_key];
				}
				else{
					$filters[$key]['value'] = $val;
					$filters[$key]['title'] = str_replace("_",' ',$key);
					$filters[$key]['title'] = str_replace("filter ",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val";
				}
			}
			
		});

		$data['filters_pickup_done'] = array();
		unset($filters['route'],$filters['token'],$filters['filter_done'],$filters['filters_requested'],$filters['filter_month_range'],$filters['filter_year_range'],$filters['page'],$filters['SSL']);
		
		foreach ( $filters as $key => $value) {											
				$f = $filters;
				unset($f[$key]);
				if( ($key !='filter_month_range') && ($key !='filter_year_range')){ 
					$url = implode( '', array_column($f, 'url') );
					$data['filters_pickup_done'][$key] = $value;
					$data['filters_pickup_done'][$key]['url'] = $this->url->link('seller_panel/account-order/getPickupOrderDone',$url,'SSL');
				}
		}

		$data['filter_month_range']	 	 = $filter_month_range;
		$data['filter_year_range'] 		 = $filter_year_range;
		$data['filter_order_no'] 		 = $filter_order_no;
		$data['filter_invoice_date_from']= $filter_invoice_date_from;
		$data['filter_invoice_date_to']  = $filter_invoice_date_to;
		$data['filter_invoice_no'] 	     = $filter_invoice_no;
		$data['filter_sale_from'] 		 = $filter_sale_from;
		$data['filter_sale_to'] 		 = $filter_sale_to;
		$data['filter_payment_status'] 	 = $filter_payment_status;
		$data['filter_record_range'] 	 = $filter_record_range;

		$data['pickup_requested_link'] = $this->url->link('seller_panel/account-order/getPickpupOrderRequested','','SSL');
		$data['pickup_done_link'] = $this->url->link('seller_panel/account-order/getPickupOrderDone','','SSL');
		$data['pickup_tentative_link'] = $this->url->link('seller_panel/account-order/getTentativeOrders','','SSL');
		$data['sor_order_link'] = $this->url->link('seller_panel/account-order/getSorOrders','','SSL');
		$data['clear_all_link'] = $this->url->link('seller_panel/account-order/getPickupOrderDone','','SSL');

		$data['header'] = $this->load->controller('seller_panel/seller_header');
		$data['footer'] = $this->load->controller('seller_panel/seller_footer');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/seller_panel/order_pickup_done.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/seller_panel/order_pickup_done.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/seller_panel/order_pickup_done.tpl', $data));
		}
	}

	/* Method to get Tentative Orders
	 * @return NULL
	 * @author Vikas, 2017
	 */
	public function getTentativeOrders(){
		
		$this->load->model('localisation/order_status');
		$data = array();
		$this->load->autoLoadLanguage('seller_panel/sellers',$data);

		if ( !empty($this->request->get['filter_record_range']) ) {
			$filter_record_range = $this->request->get['filter_record_range'];
		} else {
			$filter_record_range = $this->record_limits;
		}

		if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.order_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

		if ( !empty($this->request->get['page']) ) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		// General URL (without sort or page)
		$url = '';

		if( !empty($this->request->get['filter_tentative_order_no']) && $tentative_order_filters ){
			$url .= '&filter_tentative_order_no='. $this->request->get['filter_tentative_order_no'];
		}

		if( !empty($this->request->get['filter_record_range']) ){
			$url .= '&filter_record_range='. $this->request->get['filter_record_range'];
		}

		$filter_data = array(
			'sort'						=> $sort,
			'order'						=> $order,
			'start'                	 	=> ($page - 1) * $filter_record_range,
			'limit'                	 	=>  $filter_record_range
		);

		$seller_id = $this->customer->getId();
		$tentative_order = $this->_obj_order_stores->getOrderTentative( $this->db, $seller_id, $filter_data);

		$total_tentative_order = $this->_obj_order_stores->getTotalOrderTentative( $this->db, $seller_id);

		$order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();
		$order_statuses = array();
		foreach($order_statuses_qry as $order_status_data){
			$order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
		}

		$data['tentative_order'] = array();
		if( !empty($tentative_order) ){
			foreach($tentative_order as $tentative_value ){
				$data['tentative_order'][] = array(
					'order_no' => $tentative_value['order_no'],
					'order_date_added' => date('d-m-Y',strtotime($tentative_value['order_date_added'])),
					'order_status' => $order_statuses[$tentative_value['order_status_id']],
					'total' => $tentative_value['total'],
					'product_data' => $this->_obj_order_stores->getOrderProducts($this, $tentative_value['order_id'],$tentative_value['suborder_id'], $seller_id ),					
				);
			}
		}
		
		// URL For sorting
        $sort_url = $url;

        if ($order == 'ASC') {
            $sort_url .= '&order=DESC';
        } else {
            $sort_url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $sort_url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order_no'] = $this->url->link('seller_panel/account-order/getTentativeOrders', 'sort=o.order_no' . $sort_url, 'SSL');
        $data['sort_date'] = $this->url->link('seller_panel/account-order/getTentativeOrders', 'sort=o.date_added' . $sort_url, 'SSL');
        $data['sort_total'] = $this->url->link('seller_panel/account-order/getTentativeOrders', 'sort=total' . $sort_url, 'SSL');
        

        // URL for pagination
        $pagination_url = $url;

        if (isset($this->request->get['sort'])) {
            $pagination_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $pagination_url .= '&order=' . $this->request->get['order'];
        }

		$pagination = new Pagination();
        $pagination->total = $total_tentative_order;
        $pagination->page = $page;
        $pagination->limit = $filter_record_range;
        $pagination->url = $this->url->link('seller_panel/account-order/getTentativeOrders'. $pagination_url .'&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($total_tentative_order) ? (($page - 1) * $filter_record_range) + 1 : 0, ((($page - 1) * $filter_record_range) > ($total_tentative_order - $filter_record_range)) ? $total_tentative_order : ((($page - 1) * $filter_record_range) + $filter_record_range), $total_tentative_order, ceil($total_tentative_order / $filter_record_range));

		$filters = array();
		array_walk( $this->request->get , function( &$val , $key ) use (&$filters){

			if( !strpos($key,'_to') ){
				if( strpos($key,'_from') ){
					$code = str_replace('_from','',$key);
					$to_key = $code.'_to';
					$filters[$key]['value'] = "$val-". $this->request->get[$to_key]."";
					$filters[$key]['title'] = str_replace("_",' ',$code);
					$filters[$key]['title'] = str_replace("filter ",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val&".$to_key."=".$this->request->get[$to_key];
				}
				else{
					$filters[$key]['value'] = "$val";
					$filters[$key]['title'] = str_replace("_",' ',$key);
					$filters[$key]['title'] = str_replace("filter ",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val";
				}
			}
			
		});

		$data['filters_tentative'] = array();
		unset($filters['route'],$filters['token'],$filters['filter_done'],$filters['filters_requested'],$filters['page'],$filters['SSL'],$filters['sort'],$filters['order']);
		
		foreach ( $filters as $key => $value) {											
				$f = $filters;
				unset($f[$key]);
				$url = implode( '', array_column($f, 'url') );
				
				$data['filters_tentative'][$key] = $value;
				$data['filters_tentative'][$key]['url'] = $this->url->link('seller_panel/account-order/getTentativeOrders',$url,'SSL');
		}

		$data['record_range_15'] = $this->url->link('seller_panel/account-order/getTentativeOrders&filter_record_range=15', '','SSL');
		$data['record_range_20'] = $this->url->link('seller_panel/account-order/getTentativeOrders&filter_record_range=20', '','SSL');
		$data['record_range_25'] = $this->url->link('seller_panel/account-order/getTentativeOrders&filter_record_range=25', '','SSL');
		$data['clear_all_link'] = $this->url->link('seller_panel/account-order/getTentativeOrders','','SSL');

		$data['pickup_requested_link'] = $this->url->link('seller_panel/account-order/getPickpupOrderRequested','','SSL');
		$data['pickup_done_link'] = $this->url->link('seller_panel/account-order/getPickupOrderDone','','SSL');
		$data['pickup_tentative_link'] = $this->url->link('seller_panel/account-order/getTentativeOrders','','SSL');
		$data['sor_order_link'] = $this->url->link('seller_panel/account-order/getSorOrders','','SSL');

		$data['filter_record_range'] = $filter_record_range;
		$data['sort']				 = $sort;
		$data['order']				 = $order;
		
		$data['header'] = $this->load->controller('seller_panel/seller_header');
		$data['footer'] = $this->load->controller('seller_panel/seller_footer');
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/seller_panel/order_tentative_order.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/seller_panel/order_tentative_order.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/seller_panel/order_tentative_order.tpl', $data));
		}
	}

	/**
	 * Method to generate Seller Invoice no
	 * @value $order_id : Integer for the order id
	 * @value $suborder_id : String for the suborder id
	 * @value $seller_id : Integer for the seller id
	 * @return generate Seller Invoice no
	 * @author Vikas Agrawal, 2016
	 */
    public function generateSellerInvoiceNo() {

    	$order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;
    	$suborder_id = isset($this->request->get['suborder_id']) ? $this->request->get['suborder_id'] : '';
    	$seller_id = $this->customer->isLogged();

    	$json = array();

    	if( $order_id && $suborder_id && $seller_id ) {

    		$obj = new SellerInvoice($this);
    		$file_name = $obj->generateInvoiceNo($order_id, $suborder_id, $seller_id);

            if ( !empty($file_name) ) {
                $file_name = base64_encode(serialize($file_name));
                $json = array('link' => $this->securefiledownload->getDownloadLink('seller_invoice',$file_name,false));
            }
    	}
    	$this->response->addHeader('Content-Type: application/json');
    	$this->response->setOutput(json_encode($json));
    }


    public function getOrderReturn(){

		$data = array();
		$this->load->autoLoadLanguage('seller_panel/sellers',$data);
	
		if( !empty($this->request->get['filter_order_no']) ){
			$filter_order_no = $this->request->get['filter_order_no'];
		} else {
			$filter_order_no = NULL;
		}

		if( !empty($this->request->get['filter_debit_ref_no']) ){
			$filter_debit_ref_no = $this->request->get['filter_debit_ref_no'];
		} else {
			$filter_debit_ref_no = NULL;
		}
	
		if( !empty($this->request->get['filter_sale_from']) ){
			$filter_sale_from = $this->request->get['filter_sale_from'];
		} else {
			$filter_sale_from = NULL;
		}
	
		if( !empty($this->request->get['filter_sale_to']) ){
			$filter_sale_to = $this->request->get['filter_sale_to'];
		} else {
			$filter_sale_to = NULL;
		}
	
		if( !empty($this->request->get['filter_debit_rate_from']) ){
			$filter_debit_rate_from = $this->request->get['filter_debit_rate_from'];
		} else {
			$filter_debit_rate_from = NULL;
		}
	
		if( !empty($this->request->get['filter_debit_rate_to']) ){
			$filter_debit_rate_to = $this->request->get['filter_debit_rate_to'];
		} else {
			$filter_debit_rate_to = NULL;
		}

		if( !empty($this->request->get['filter_record_range'])  ){
			$filter_record_range = $this->request->get['filter_record_range'];
		} else {
			$filter_record_range = $this->record_limits;
		}

		if ( !empty($this->request->get['sort']) ) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'debit_note_date';
		}

		if ( !empty($this->request->get['order']) ) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if ( !empty($this->request->get['page']) ) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		// General URL (without sort or page)
		$url = '';

		//order number requested
		if( !empty($this->request->get['filter_order_no']) ){
			$url .= '&filter_order_no='. $this->request->get['filter_order_no'];
		}
		
		//debit number requested
		if( !empty($this->request->get['filter_debit_ref_no']) ){
			$url .= '&filter_debit_ref_no='. $this->request->get['filter_debit_ref_no'];
		}

		if( !empty($this->request->get['filter_sale_from']) ){
			$url .= '&filter_sale_from='. $this->request->get['filter_sale_from'];
		}
		
		if( !empty($this->request->get['filter_sale_to']) ){
			$url .= '&filter_sale_to='. $this->request->get['filter_sale_to'];
		}
		
		if( !empty($this->request->get['filter_debit_rate_from'])  ){
			$url .= '&filter_debit_rate_from='. $this->request->get['filter_debit_rate_from'];
		}
		
		if( !empty($this->request->get['filter_debit_rate_to']) ){
			$url .= '&filter_debit_rate_to='. $this->request->get['filter_debit_rate_to'];
		}

		$data['record_range_15'] = $this->url->link('seller_panel/account-order/getOrderReturn&filter_record_range=15', $url,'SSL');
		$data['record_range_20'] = $this->url->link('seller_panel/account-order/getOrderReturn&filter_record_range=20', $url,'SSL');
		$data['record_range_25'] = $this->url->link('seller_panel/account-order/getOrderReturn&filter_record_range=25', $url,'SSL');
		

		$filter_data = array(
			
			'filter_order_no' 	               => $filter_order_no,
			'filter_debit_ref_no'              => $filter_debit_ref_no,
			'filter_sale_from'                 => $filter_sale_from,
			'filter_sale_to' 	 	           => $filter_sale_to,
			'filter_debit_rate_from' 	 	   => $filter_debit_rate_from,
			'filter_debit_rate_to'             => $filter_debit_rate_to,
			'sort'                 	 		   => $sort,
			'order'                	 		   => $order,
			'start'                	 		   => ($page - 1) * $filter_record_range,
			'limit'                	 		   => $filter_record_range
		);
		
		$seller_id = $this->customer->getId();
		// for pickup requested
		$getReturnOrderDetails = $this->_obj_order_stores->getReturnOrderDetails( $this->db, $seller_id, $filter_data);
		
		$data['return_order'] = array();
		if( !empty($getReturnOrderDetails) ){
			foreach($getReturnOrderDetails as $key=>$return_order ){
				$invoice_datas = explode(',',$return_order['seller_invoice_number']);
				
				$data['return_order'][$return_order['order_id']] = array(
					'debit_note_no'          => $return_order['debit_note_no'],
					'order_no'		         => $return_order['order_no'],
					'debit_note_amount'      => $return_order['debit_note_amount'],
					'debit_note_date'        => date('d/m/Y',strtotime($return_order['debit_note_date'])),
					'invoice_datas'			 => $invoice_datas,
					);
			}
		}

		$filters = array();		
		array_walk( $this->request->get , function( &$val , $key ) use (&$filters){
			if( !strpos($key,'_to') ){
				if( strpos($key,'_from') ){
					$code = str_replace('_from','',$key);
					$to_key = $code.'_to';
					$filters[$key]['value'] = "$val - ". $this->request->get[$to_key]."";
					$filters[$key]['title'] = str_replace("_",' ',$code);
					$filters[$key]['title'] = str_replace("filters_requested",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val&".$to_key."=".$this->request->get[$to_key] . "";
				}
				else{
					$filters[$key]['value'] = "$val";
					$filters[$key]['title'] = str_replace("_",' ',$key);
					$filters[$key]['title'] = str_replace("filter ",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val";
				}
			}
		});
		
	
		$data['filters_return'] = array();
		unset($filters['route'],$filters['token'],$filters['filters_return']);
		
		foreach ( $filters as $key => $value) {											
				$f = $filters;
				unset($f[$key]);
				$url = implode( '', array_column($f, 'url') );
				$data['filters_return'][$key] = $value;
				$data['filters_return'][$key]['url'] = $this->url->link('seller_panel/account-order/getOrderReturn',$url,'SSL');
		}
		
		if( isset($this->request->get['download_return_report']) && $this->request->get['download_return_report'] == 1 ){
		
            $file_name = DIR_DOWNLOAD .'return_order.csv';
            $fp = fopen($file_name, 'w');
           
            $results = $this->_obj_order_stores->getReturnOrderDetails( $this->db, $seller_id, $filter_data, $flag = 1);
           
            $headers = array('Order No', 
                          'Debit Ref. No.', 
                          'Debit Note Date', 
                          'Debit Amount', 
                          'Invoice No', 
                          'Invoice Date', 
                          'Invoice Amount',
						);
            fputcsv($fp, $headers);
           
            if( !empty($results) ) {
                foreach ($results as $key => $debit_values) {
				
						
                        $order_no           = ($debit_values['order_no'])?$debit_values['order_no']: '';
                        $debit_ref_no       = $debit_values['debit_note_no'] .'' . $debit_values['debit_note_no'];
                        $debit_note_date    = ($debit_values['debit_note_date']) ? date('d-m-Y',strtotime($debit_values['debit_note_date'])) : '';
                        $debit_note_amount  = ($debit_values['debit_note_amount']) ? $debit_values['debit_note_amount'] : '';
                     
                        $seller_invoice_array = explode(",",$debit_values['seller_invoice_number']);
						
						$inv_no_wth_amount = array();
						$inv_no_and_inv_id_pad = array();
						
						foreach ($seller_invoice_array as $invoice_ids_array_key => $invoice_ids_array_value) {
							$inv_no_and_inv_id_pad[] = explode(';', $invoice_ids_array_value);
						}
					
                        foreach($inv_no_and_inv_id_pad as $invoice_key=>$invoice_value) {
							$data_sheet = array(
											$order_no, 
											$debit_ref_no,
											$debit_note_date,
											$debit_note_amount,
											($invoice_value[0]) ? $invoice_value[0] : '',
											($invoice_value[1]) ? date('d-m-Y',strtotime($invoice_value[1])) : '',
											($invoice_value['2']) ?  : ''
											);
							fputcsv($fp, $data_sheet);
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
		
		$data['filter_order_no'] 	= $filter_order_no;
		$data['filter_debit_ref_no'] = $filter_debit_ref_no;
		$data['filter_sale_from']    = $filter_sale_from;
		$data['filter_sale_to']      = $filter_sale_to;
		$data['filter_debit_rate_from']  = $filter_debit_rate_from;
		$data['filter_debit_rate_to']    = $filter_debit_rate_to;
		$data['header'] = $this->load->controller('seller_panel/seller_header');
		$data['footer'] = $this->load->controller('seller_panel/seller_footer');
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/seller_panel/order_returns.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/seller_panel/order_returns.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/seller_panel/order_returns.tpl', $data));
		}
    }

    /**
	 * Method to generate Seller Invoice no when seller split orders
	 * @value $order_id : Integer for the order id
	 * @value $suborder_id : String for the suborder id
	 * @value $changes : array of split orders
	 * @return generate Seller Invoice no
	 * @author Vikas Agrawal, 2016
	 */
    public function splitOrderProducts(){

    	if( empty( $this->request->post )){
    		return false;
    	}

		$changes 	= $this->request->post['changes'];	
		
		// get only order product id and set in $order_product_ids
		$order_product_ids = array_keys($changes);
		// get a single record of order_id, suborder_id, and seller_id corresponding to order_product_id 
		$same_order_id_and_suborder_id = $this->_obj_order_stores->getSameOrderIdandSubOrderIdandSellerId($this->db, $order_product_ids);
		
		if(empty($same_order_id_and_suborder_id)){
			$json['error_msg'] =  "Something went wrong. Please try again!"; 
    		$json['error'] =  4; 
    		echo json_encode($json);
    		exit;
		}

		if( $same_order_id_and_suborder_id['seller_id'] != $this->customer->getId() ){
			$json['error_msg'] =  'You can not access another seller account at the same time ! Please try again after login.'; 
    		$json['redirect_link'] =  $this->url->link('account/logout', '', 'SSL'); 
    		$json['error'] =  2; 
    		echo json_encode($json);
    		exit;
		}
		
		$json = array();

    	$order_id 	= (int)$same_order_id_and_suborder_id['order_id'];
    	$suborder_id= $same_order_id_and_suborder_id['suborder_id'];
    	$seller_id 	= $this->customer->getId();

    	$invoice_no = trim($this->request->post['invoice_number']);
    	$invoice_date = $this->request->post['invoice_date'];

    	// regex for invoice no
    	if(!preg_match("/^[A-Za-z0-9\/-]{1,16}$/", $invoice_no, $output_array)){
    		$json['error_msg'] =  'Invalid Invoice number format !! Invoice number can only be 16 characters long; can contain either alphabets (a-z, A-Z), digits (0-9), hyphen (-), and/or forward slash (/).'; 
    		$json['error'] =  3; 
    		echo json_encode($json);
    		exit;
    	} 

    	// check invoice number when seller given new invoice number then check in database that new invoice number are already saved or Not. if new invoice number already saved in database then error message show.(Please enter new invoice number.)
    	$invoice_no_allowed = $this->_obj_order_stores->checkSellerInvoiceNoIsUnique( $this->db, $seller_id , $invoice_no );
    	if( !$invoice_no_allowed ){
    		$json['error_msg'] =  "This invoice number is already used! Please enter new invoice number."; 
    		$json['error'] =  1; 
    		echo json_encode($json);
    		exit;
    	}
        
        $nick_name = SellerInfo::getSellerFirmDetails($this->db, $seller_id)['nickname'];

		if( $order_id && $suborder_id && $seller_id ) {

			$order_product_ids_array = array();
			foreach( $changes as $order_product_id_key => $values ){
				$order_product_ids_array[] = $order_product_id_key;
			}

			$checkOrderProductIds = $this->_obj_order_stores->checkOrderProductIdsIsAvailableOrNot( $this->db, $order_product_ids_array );

			if(!$checkOrderProductIds){
				$json['error_msg'] =  "Something went wrong. Please try again.!"; 
	    		$json['error'] =  4; 
	    		echo json_encode($json);
	    		exit;
			}

    		$obj = new SellerInvoice($this->registry);
    		$file_name = $obj->generateInvoiceNo($order_id, $suborder_id, $seller_id, $invoice_no, $invoice_date);

    		$seller_invoice_id = 0;
            if ( !empty($file_name) 
            	&& isset($file_name['seller_invoice_id']) 
            	&& !empty((int)$file_name['seller_invoice_id']) ) {

            	$seller_invoice_id = (int)$file_name['seller_invoice_id'];
                $file_name = base64_encode(serialize($file_name));
                $json['link'] = $this->securefiledownload->getDownloadLink('seller_invoice',$file_name,false);
                $json['error'] = 0 ;

                // $order_product_id_array = array();
		    	foreach( $changes as $order_product_id_key => $values ){
		    		// $order_product_id_array[] = $order_product_id_key;
		    		
		    		$edit_history = array(
						'user_id' 	=> $seller_id,
                                                'user_name' 	=> $nick_name,
						'user_type' => 'seller',
						'user_ip'	=> $this->request->getIpAddress,
						'user_agent'=> $_SERVER['HTTP_USER_AGENT'],
						'date_added'=> date('d-m-Y H:i:s'),
						'comment' 	=> key($values)
					);

					$details = array(
						'edit_type' 	=> $values,
						'edit_history' 	=> $edit_history

					);

		    		if(in_array(key($values), array('SELLER_APPROVED'))){
		    			$this->_obj_order_stores->updateEditTypeBySeller( $this->db, $order_product_id_key, $details, $seller_id, $seller_invoice_id);

		    		} else if(in_array(key($values), array('SELLER_NOT_SUPPLIED','SELLER_LATER_DISPATCH'))){
		    			$this->_obj_order_stores->updateEditTypeBySeller( $this->db, $order_product_id_key, $details, $seller_id, '');

		    		} else if(in_array(key($values), array('SELLER_PARTIAL'))) {

		    			$edit_history['comment'] = 'SELLER_NOT_SUPPLIED';
		    			$details = array(
							'edit_type' 	=> 'SELLER_NOT_SUPPLIED',
							'edit_history' 	=> $edit_history
						);  			
		    			
		    			$details['quantity']      = $values['SELLER_PARTIAL']['value'];  
		    			$details['old_edit_type'] = 'SELLER_PARTIAL';
		    			OrderEdit::splitOrderProduct( $this->db, $order_product_id_key, $details, $seller_invoice_id);    			
		    		}
		    	}
                //Update order and suborder total
                OrderEdit::updateOrderTotalsDueVariousAction($this->db, $order_id, $suborder_id);

                $this->_obj_order_stores->SendNotificationToSalesStaff($this, $order_id);
                 
		    	echo json_encode($json);
		    	exit;

            } else {
            	$json['error'] = 1 ;
            	$json['error_msg'] = 'Something went wrong and invoice could not be generated. Please try again after some time.!';
               	echo json_encode($json);
    			exit;
            }
    	}    	
    }

    /*
    * Get Sor Orders
    */    
    public function getSorOrders(){
		$data = array();
		$this->load->autoLoadLanguage('seller_panel/sellers',$data);

		$data['getQuarters'] = getQuarters();
		$data['getFinancialYears'] = getFinancialYears(2016, '');

		if( !empty($this->request->get['filter_month_range']) ){
			$filter_month_range = $this->request->get['filter_month_range'];
		} else {
			$filter_month_range = NULL;
		}

		if( !empty($this->request->get['filter_year_range']) ){
			$filter_year_range = $this->request->get['filter_year_range'];
		} else {
			$filter_year_range = NULL;
		}

		if( !empty($this->request->get['filter_order_no']) ){
			$filter_order_no = $this->request->get['filter_order_no'];
		} else {
			$filter_order_no = NULL;
		}

		if( !empty($this->request->get['filter_invoice_date_from']) ){
			$filter_invoice_date_from = $this->request->get['filter_invoice_date_from'];
		} else {
			$filter_invoice_date_from = NULL;
		}

		if( !empty($this->request->get['filter_invoice_date_to']) ){
			$filter_invoice_date_to = $this->request->get['filter_invoice_date_to'];
		} else {
			$filter_invoice_date_to = NULL;
		}

		if( !empty($this->request->get['filter_invoice_no']) ){
			$filter_invoice_no = $this->request->get['filter_invoice_no'];
		} else {
			$filter_invoice_no = NULL;
		}

		if( !empty($this->request->get['filter_sale_from']) ){
			$filter_sale_from = $this->request->get['filter_sale_from'];
		} else {
			$filter_sale_from = NULL;
		}

		if( !empty($this->request->get['filter_sale_to']) ){
			$filter_sale_to = $this->request->get['filter_sale_to'];
		} else {
			$filter_sale_to = NULL;
		}

		if( !empty($this->request->get['filter_payment_status'])  ){
			$filter_payment_status = $this->request->get['filter_payment_status'];
		} else {
			$filter_payment_status = NULL;
		}

		if( !empty($this->request->get['filter_record_range'])  ){
			$filter_record_range = $this->request->get['filter_record_range'];
		} else {
			$filter_record_range = $this->record_limits;
		}

		if ( !empty($this->request->get['sort']) ) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if ( !empty($this->request->get['order']) ) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if ( !empty($this->request->get['page']) ) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if ( !empty($this->request->get['download_pickup_done_report']) ) {
			$download_pickup_done_report = $this->request->get['download_pickup_done_report'];
		} else {
			$download_pickup_done_report = false;
		}

		// General URL (without sort or page)
		$url = '';

		if( !empty($this->request->get['filter_order_no']) ){
			$url .= '&filter_order_no='. $this->request->get['filter_order_no'];
		}

		if( !empty($this->request->get['filter_invoice_date_from']) ){
			$url .= '&filter_invoice_date_from='. $this->request->get['filter_invoice_date_from'];
		}

		if( !empty($this->request->get['filter_invoice_date_to']) ){
			$url .= '&filter_invoice_date_to='. $this->request->get['filter_invoice_date_to'];
		}

		if( !empty($this->request->get['filter_invoice_no']) ){
			$url .= '&filter_invoice_no='. $this->request->get['filter_invoice_no'];
		}

		if( !empty($this->request->get['filter_sale_from']) ){
			$url .= '&filter_sale_from='. $this->request->get['filter_sale_from'];
		}

		if( !empty($this->request->get['filter_sale_to']) ){
			$url .= '&filter_sale_to='. $this->request->get['filter_sale_to'];
		}

		if( !empty($this->request->get['filter_payment_status']) ){
			$url .= '&filter_payment_status='. $this->request->get['filter_payment_status'];
		}

		if( !empty($this->request->get['filter_record_range']) ){
			$url .= '&filter_record_range='. $this->request->get['filter_record_range'];
		}

		
		$seller_id = $this->customer->getId();
		$filter_data = array();

		$filter_data = array(
			'filter_order_no' 		 	=> $filter_order_no,
			'filter_invoice_date_from'	=> $filter_invoice_date_from,
			'filter_invoice_date_to' 	=> $filter_invoice_date_to,
			'filter_invoice_no' 	 	=> $filter_invoice_no,
			'filter_sale_from' 	 	 	=> $filter_sale_from,
			'filter_sale_to' 	 	 	=> $filter_sale_to,
			'filter_payment_status'     => $filter_payment_status,
			'sort'                 	 	=> $sort,
			'order'                	 	=> $order,
			'start'                	 	=> ($page - 1) * $filter_record_range,
			'limit'                	 	=> $filter_record_range,
			'download_pickup_done_report'=> $download_pickup_done_report,
			'filter_sor_record'		    => 'onlysor'
		);

		$results = $this->_obj_order_stores->getOrderSorProduct( $this->db, $seller_id, $filter_data);
		
		$total_results = $this->_obj_order_stores->getTotalRecordSorProduct( $this->db, $seller_id, $filter_data);
		$data['order_pickup_data'] = array();
		
		if( !empty($results) ){

			foreach ($results['records'] as $key => $result) {
				// calculate amount of debit note of perticular order id
				$total_amt_debit_note = 0; 
				if(!empty($results['debit_notes'][$result['order_id']])){
					foreach($results['debit_notes'][$result['order_id']] as $debit_note_values){
						$total_amt_debit_note += $debit_note_values['debit_note_amount'];
					}
				}

				//get seller invoice download link
  				$seller_inv_dload_link = array();
  				$check_trxn_done = false;
	            if(!empty($results['seller_invoices'][$result['order_id']])){
	            	// array of seller invoice corresponding to order_id
	            	$seller_invoice_data = $results['seller_invoices'][$result['order_id']];

	            	foreach($seller_invoice_data as $seller_inv_id_key => $seller_inv_id_value){
	            		if($seller_inv_id_value['trxn_done'] == 'BANK_REQUESTED' || $seller_inv_id_value['trxn_done'] == 'BANK_SUCCESS' || $seller_inv_id_value['trxn_done'] == 'NOT_APPLICABLE'){
	            			$check_trxn_done = true;
	            		}
	            		$seller_invoice = array();
	            		$seller_invoice['seller_invoice_id'] = (int)$seller_inv_id_value['seller_invoice_id'];
		               	$seller_invoice = serialize($seller_invoice);
		              	$secureFileDload = new SecureFileDownload($this->registry);
		              	$seller_inv_dload_link[$seller_inv_id_key]['label'] = $seller_inv_id_value['seller_invoice_prefix'].''.$seller_inv_id_value['seller_invoice_no'];
		               	$seller_inv_dload_link[$seller_inv_id_key]['url'] = $secureFileDload->getDownloadLink('seller_invoice', base64_encode($seller_invoice), false);
	            	}
	            }

	            // get download link of debit note
				$debit_note_url = array();

				if( !empty($results['debit_notes'][$result['order_id']]) ){
					$debit_note_data = $results['debit_notes'][$result['order_id']];
					foreach ($debit_note_data as $key => $value) {
						$encode_file = array();
						$encode_file['order_no'] = $result["order_no"];
						$encode_file['debit_note_id'] = $value['debit_note_id'];
					    $encode_file = base64_encode(serialize($encode_file));
						$dload_url['label'] = $value['debit_note_prefix'].''.$value['debit_note_no'];
						$dload_url['url'] = $this->securefiledownload->getDownloadLink('seller_debit_note',$encode_file, false);
					  	$debit_note_url[$key] = $dload_url;
					}
				}

				$sor_invoice_no 		= '';
				$sor_invoice_date 		= '';
				$paymentToolTip 		= '';
				$sor_payment  			= array();

				if (isset($result['sor_invoice_no'])) {

					$sor_invoice_no 		= $result['sor_invoice_no'];
					$sor_payment  = $this->_obj_order_stores->getSorOrderPayments($this->db, $result['order_id'], $seller_id );
				}
				if (isset($result['sor_invoice_date'])) {

					$sor_invoice_date 		= $result['sor_invoice_date'];
				}
				/*
				* set the string for payment tool tip
				*/
				if(!empty($results['seller_invoices'][$result['order_id']])){
					
					$paymentToolTip .= '<div class="paid_detail">';
		            $paymentToolTip .= '<ul>';

					foreach ($results['seller_invoices'][$result['order_id']] as $keyse => $valuese) {
						
						if($valuese['trxn_done'] == 'BANK_REQUESTED' || $valuese['trxn_done'] == 'BANK_SUCCESS' || $valuese['trxn_done'] == 'NOT_APPLICABLE'){

			                $paymentToolTip .=     '<li>Inv. No. ' . $valuese["seller_invoice_prefix"].''.$valuese["seller_invoice_no"] . '</li>';
			                $paymentToolTip .=     '<li>Amount. ' . (($valuese["trxn_amount"]) ? $valuese["trxn_amount"] : ''). '</li>';
			                
			                if($valuese["trxn_utr"]!='' && $valuese["trxn_utr_date"]!=''){
			                   
			                    $paymentToolTip .=     '<li>Ref. ' . (($valuese["trxn_utr"]) ? $valuese["trxn_utr"] : '' ).'</li>';
			                    $paymentToolTip .=     '<li>Date. ' . (($valuese["trxn_utr_date"]) ? $valuese["trxn_utr_date"] : '').'</li>';
			                }
			            }
		            }  

				}

				if(!empty($sor_payment)){

					if ($paymentToolTip=='') {

						$paymentToolTip .= '<div class="paid_detail">';
		            	$paymentToolTip .= '<ul>';
					}
					

					foreach ($sor_payment as $keysor => $valuesor) {
						
						if (($valuesor['trxn_done'] == 'BANK_REQUESTED' || $valuesor['trxn_done'] == 'BANK_SUCCESS' || $valuesor['trxn_done'] == 'NOT_APPLICABLE') && $valuesor['suborder_id'] == $result['suborder_id']) {
							
							$check_trxn_done = true;

							$paymentToolTip .=     '<li>Sub Order. No. ' . $valuesor["suborder_id"].'</li>';
			                $paymentToolTip .=     '<li>Amount. ' . (($valuesor["total_paid_amount"]) ? $valuesor["total_paid_amount"] : ''). '</li>';
			                
			                if($valuesor["trxn_utr"]!='' && $valuesor["trxn_utr_date"]!=''){
			                   
			                    $paymentToolTip .=     '<li>Ref. ' . (($valuesor["trxn_utr"]) ? $valuesor["trxn_utr"] : '' ).'</li>';
			                    $paymentToolTip .=     '<li>Date. ' . (($valuesor["trxn_utr_date"]) ? $valuesor["trxn_utr_date"] : '').'</li>';
			                }
			            }
					}

				}
				if ($paymentToolTip!='') {

	                $paymentToolTip .= '</ul>';
	                $paymentToolTip .= '</div>'; 
				}
				/*
				* set sor data
				*/
				$data['order_pickup_data'][] = array(
					'order_id' 		=> $result['order_id'],
					'order_no' 		=> $result['order_no'],
					'suborder_id' 	=> $result['suborder_id'],
					'order_processing_date'=>$result['order_processing_date'],
					'order_date_added' => (!empty($results['order_process_lists'][$result['order_id']]) ? $results['order_process_lists'][$result['order_id']]['order_processing_date'] : $result['order_date']),
					'sale' 			=> number_format($result['sale_amt'],2),
					'debit_notes'=> (!empty($results['debit_notes'][$result['order_id']]) ? $results['debit_notes'][$result['order_id']] : array()),
					'return_amount' => number_format($total_amt_debit_note,2),
					'penalty_amount'=> 0,
					'net_payable_amount' => number_format($result['sale_amt'] - $total_amt_debit_note,2),
					'trxn_done'		=> ($check_trxn_done) ? 'Paid' : '',
					'seller_invoice_link' => $seller_inv_dload_link,
					'debit_note_link'=> $debit_note_url,
					'sllr_invc_genrted'	=>	$this->_obj_order_stores->getSellerInvoiceGenerated($this->registry, $this, $result['order_id'], $result['suborder_id'], $seller_id ),
					'seller_sor_product'	=>	$this->_obj_order_stores->getSorSubOrderProducts($this, $result['order_id'],$seller_id),
					'sor_invoice_no'=>$sor_invoice_no,
					'sor_invoice_date'=>$sor_invoice_date,
					'payment_tool_tip'=>$paymentToolTip,
					'order_status_id'=>	$result['order_status_id'],
					'order_status_name'=>	$result['order_status_name']
				);
			}
		}		
		
		if( isset($this->request->get['download_pickup_done_report']) && $this->request->get['download_pickup_done_report'] ){

			$download_results = $data['order_pickup_data'];

			$file_name = DIR_DOWNLOAD .'order_pickup_done.csv';
	        $fp = fopen($file_name, 'w');
	        
	        $data = array('Order No', 
	                      'Invoice / Debit No.', 
	                      'Invoice / Debit Note Date', 
	                      'Invoice / Debit Amount', 
	                      'Payment Done', 
	                      'Payment Amount', 
	                      'Payment Date',
	                      'Payment Reference No.');
	        fputcsv($fp, $data);
	        if( !empty($results) ) {

	            foreach ($download_results as $key => $sub_value) {
	            	foreach( $sub_value['seller_invoices'] as $invoice_key => $invoice_values ){
	            		$order_no 		= ($sub_value['order_no'])?$sub_value['order_no']: '';
	            		$invoice_no 	= $invoice_values['seller_invoice_prefix'] .'' . $invoice_values['seller_invoice_no'];
	            		$date_added 	= ($invoice_values['date_added']) ? date('d-m-Y',strtotime($invoice_values['date_added'])) : '';
	            		$sale 			= ($invoice_values['seller_inv_amount']) ? $invoice_values['seller_inv_amount'] : '';
	            		$payment_done 	= ($invoice_values['trxn_done']) ? $invoice_values['trxn_done'] : '';
	            		$payment_amount = ($invoice_values['trxn_amount']) ? $invoice_values['trxn_amount'] : '';
	            		$payment_date 	= ($invoice_values['trxn_utr_date']) ? date('d-m-Y',strtotime($invoice_values['trxn_utr_date'])) : ''; 
	            		$payment_reference_no= ($invoice_values['trxn_utr']) ? $invoice_values['trxn_utr'] : ''; 
            			$data = array($order_no, 
		                              $invoice_no,
		                              $date_added, 
		                              $sale, 
		                              $payment_done, 
		                              $payment_amount, 
		                              $payment_date, 
		                              $payment_reference_no);
                		fputcsv($fp, $data);
	            	}
	            }

        		foreach($download_results as $sub_debit_key => $sub_debit_value){
        			if(!empty($sub_debit_value['debit_notes'])){
	        			foreach( $sub_debit_value['debit_notes'] as $debit_key => $debit_values ){
	        				$order_no 			= ($sub_debit_value['order_no']) ? $sub_debit_value['order_no']: '';
		            		$debit_no 			= $debit_values['debit_note_prefix'] .'' . $debit_values['debit_note_no'];
		            		$date_added 		= ($debit_values['date_added']) ? date('d-m-Y',strtotime($debit_values['date_added'])) : '';
		            		$debit_note_amount	= ($debit_values['debit_note_amount']) ? $debit_values['debit_note_amount'] : '';
		            		$debit_payment_done	= ($debit_values['trxn_done']) ? $debit_values['trxn_done'] : '';
		            		$debit_payment_amount= ($debit_values['trxn_amount']) ? $debit_values['trxn_amount'] : '';
		            		$debit_payment_date	= ($debit_values['trxn_utr_date']) ? date('d-m-Y',strtotime($debit_values['trxn_utr_date'])) : ''; 
		            		$debit_payment_reference_no= ($debit_values['trxn_utr']) ? date('d-m-Y',strtotime($debit_values['trxn_utr'])) : ''; 
	            			$data = array($order_no, 
			                              $debit_no,
			                              $date_added, 
			                              $debit_note_amount, 
			                              $debit_payment_done, 
			                              $debit_payment_amount, 
			                              $debit_payment_date, 
			                              $debit_payment_reference_no);
	                		fputcsv($fp, $data);
	        			}
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

		$data['pagination'] = '';
		$data['results'] = '';
		if($filter_record_range !='all'){
			$pagination = new Pagination();
	        $pagination->total = $total_results;
	        $pagination->page = $page;
	        $pagination->limit = $filter_record_range;
	        $pagination->url = $this->url->link('seller_panel/account-order/getSorOrders'. $url .'&page={page}', 'SSL');

	        $data['pagination'] = $pagination->render();

	        $data['results'] = sprintf($data['text_pagination'], ($total_results) ? (($page - 1) * $filter_record_range) + 1 : 0, ((($page - 1) * $filter_record_range) > ($total_results - $filter_record_range)) ? $total_results : ((($page - 1) * $filter_record_range) + $filter_record_range), $total_results, ceil($total_results / $filter_record_range));
		}
		
		$filters = array();
		array_walk( $this->request->get , function( &$val , $key ) use (&$filters){

			if( !strpos($key,'_to') ){
				if( strpos($key,'_from') ){
					$code = str_replace('_from','',$key);
					$to_key = $code.'_to';
					$filters[$key]['value'] = "$val-". $this->request->get[$to_key]."";
					$filters[$key]['title'] = str_replace("_",' ',$code);
					$filters[$key]['title'] = str_replace("filter ",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val&".$to_key."=".$this->request->get[$to_key];
				}
				else{
					$filters[$key]['value'] = $val;
					$filters[$key]['title'] = str_replace("_",' ',$key);
					$filters[$key]['title'] = str_replace("filter ",' ',$filters[$key]['title']);
					$filters[$key]['url'] = "&$key=$val";
				}
			}
			
		});

		$data['filters_pickup_done'] = array();
		unset($filters['route'],$filters['token'],$filters['filter_done'],$filters['filters_requested'],$filters['filter_month_range'],$filters['filter_year_range'],$filters['page'],$filters['SSL']);
		
		foreach ( $filters as $key => $value) {											
				$f = $filters;
				unset($f[$key]);
				if( ($key !='filter_month_range') && ($key !='filter_year_range')){ 
					$url = implode( '', array_column($f, 'url') );
					
					$data['filters_pickup_done'][$key] = $value;
					$data['filters_pickup_done'][$key]['url'] = $this->url->link('seller_panel/account-order/getSorOrders',$url,'SSL');
				}
		}

		$data['filter_month_range']	 	 = $filter_month_range;
		$data['filter_year_range'] 		 = $filter_year_range;
		$data['filter_order_no'] 		 = $filter_order_no;
		$data['filter_invoice_date_from']= $filter_invoice_date_from;
		$data['filter_invoice_date_to']  = $filter_invoice_date_to;
		$data['filter_invoice_no'] 	     = $filter_invoice_no;
		$data['filter_sale_from'] 		 = $filter_sale_from;
		$data['filter_sale_to'] 		 = $filter_sale_to;
		$data['filter_payment_status'] 	 = $filter_payment_status;
		$data['filter_record_range'] 	 = $filter_record_range;

		$data['pickup_requested_link'] = $this->url->link('seller_panel/account-order/getPickpupOrderRequested','','SSL');
		$data['pickup_done_link'] = $this->url->link('seller_panel/account-order/getPickupOrderDone','','SSL');
		$data['pickup_tentative_link'] = $this->url->link('seller_panel/account-order/getTentativeOrders','','SSL');
		$data['sor_order_link'] = $this->url->link('seller_panel/account-order/getSorOrders','','SSL');
		$data['clear_all_link'] = $this->url->link('seller_panel/account-order/getSorOrders','','SSL');

		$data['header'] = $this->load->controller('seller_panel/seller_header');
		$data['footer'] = $this->load->controller('seller_panel/seller_footer');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/seller_panel/sor_orders.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/seller_panel/sor_orders.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/seller_panel/sor_orders.tpl', $data));
		}
	}

   /** 
   * Method to Revert Goods 
   * @return NULL
   * @author Vikas, 2017
   */
    public function revertGoodsBySeller(){
    	$order_product_ids = $this->request->post['revert_order_product_ids'];
    	$seller_id = $this->request->post['seller_id'];
    	$order_id = $this->request->post['order_id'];
    	$suborder_id = $this->request->post['suborder_id'];
    	$return_value = $this->_obj_order_stores->revertGoodsBySeller($this->db, $order_product_ids, $seller_id, $order_id, $suborder_id);    
    	$json = array();
    	if($return_value == 1){
    		$json['status'] = 1;
    		$json['message'] = 'Dear Seller, You cannot revert now. Order has been invoiced to customer already, hence marked closed.';
    	}

    	$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

  /** 
   * Method to get sor invoice inventory record for a seller
   * @return NULL
   * @author kalyan, 9th Nov, 2017
   */
    public function sorInvoices(){

		$data = array();
    	$this->load->autoLoadLanguage('seller_panel/sellers',$data);
    	$this->load->model('tool/image');

		$seller_id 			= $this->customer->getId();
		$data['seller_id'] 	= $seller_id;
		$countSorInvoices 	= '';
		$sorInvoicesData 	= array();
		$i 					= 0;
		$sorSkuWiseData 	= array();
		$total_results      = 0;

		if( !empty($this->request->get['filter_invoice_no']) ){

			$data['filter_invoice_no'] = $this->request->get['filter_invoice_no'];
		} else {

			$data['filter_invoice_no'] = NULL;
		}
		if ( !empty($this->request->get['sort']) ) {

			$sort = $this->request->get['sort'];
		} else {

			$sort = 'order_processing_date';
		}
		if ( !empty($this->request->get['order']) ) {

			$order = $this->request->get['order'];
		} else {

			$order = 'DESC';
		}
		if ( !empty($this->request->get['page']) ) {

			$page = $this->request->get['page'];
		} else {

			$page = 1;
		}
		
		$url = '';

		if( !empty($this->request->get['filter_invoice_no']) ){

			$url .= '&filter_invoice_no='. $this->request->get['filter_invoice_no'];
		}
		/*
		* general urls for pagination
		*/
        $general_url = $url;

        if (isset($this->request->get['sort'])) {

            $general_url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {

            $general_url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {

            $general_url .= '&page=' . $this->request->get['page'];
        }

        $limit = $this->config->get('config_limit_admin');

		$filter_data = array(
			
			'filter_invoice_no' 	   		   => $data['filter_invoice_no'],
			'sort'                 	 		   => $sort,
			'order'                	 		   => $order,
			'start'                	 		   => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                	 		   => $limit
		);			
		/*
		* get count for paging
		*/
		$total_results 		= $this->_obj_order_stores->getSorInvoicesList($this->db, $seller_id, $filter_data,'get_count');
		/*
		* get sor invoice records
		*/
		$sorInvoices 		= $this->_obj_order_stores->getSorInvoicesList($this->db, $seller_id, $filter_data);
		
		if (!empty($sorInvoices)) {		

			foreach ($sorInvoices as $key => $value) {				

				$sorInvoiceProduct 				= $this->_obj_order_stores->getSorInvoicesProductList( $this->db, $value['purchase_id']);

				$tatalPurchaseQty  				= 0;
				$total_purchase_return_pieces 	= 0;
				$total_purchase_return_amount   = 0; 
				$totalSoldQuantity 				= 0;
				$totalSoldAmount 				= 0;				
				$j 								= 0;
				$dataSkuWise 					= array();
				$invoiceProducts 				= array();
				$product_id_array 				= array();
				$products_sale_return   		= array();
				$products_purchase_return 		= array();
				$sor_sales_quantity 			= array();
				$purchase_id_array 				= array();
				$paid_amount_against_invoice 	= array();
				/*
				* get all product id in array
				*/
				$product_id_array 				= array_column($sorInvoiceProduct, 'product_id');
				$purchase_id_array 				= array_column($sorInvoiceProduct, 'purchase_id');
			
				if (!empty($product_id_array)) {
					/*
					* get all product sale
					*/
					$sor_sales_quantity 			= $this->_obj_order_stores->getSorProductSoldQuantity( $this->db, $product_id_array, $value['purchase_id']);

					/*
					* get all product sales return
					*/
					$products_sale_return  			= $this->_obj_order_stores->getSorProductsSalesReturns($this->db, $product_id_array, '', $value['purchase_id']);
					/*
					* get all product purchase return
					*/
					$products_purchase_return  		= $this->_obj_order_stores->getSorProductsPurchaseReturns($this->db, $product_id_array, '', $value['purchase_id']);
					/*
					* get paid amount against invoice
					*/
					$paid_amount_against_invoice	= $this->_obj_order_stores->getPaidAmountAgainstSorInvoice( $this->db, $seller_id, $purchase_id_array);
				}

				if (!empty($sorInvoiceProduct)) {

					foreach ($sorInvoiceProduct as $keysip => $valuesip) {

						//if (isset($products_purchase_return[$valuesip['product_id']])) {
							//$tatalPurchaseQty 	   += ($valuesip['pieces'] - $products_purchase_return[$valuesip['product_id']]['quantity']);
						//}else{
							$tatalPurchaseQty 	   += $valuesip['pieces'];	
						//}						

						$productSoldQuantity 	= '';
						$productSoldAmount 		= '';
						$productActualSoldQuantity = '';
						$productActualSoldAmount   = '';
						$return_quantity         = '';
                        $productReturnAmount     = '';
							
						/*
						* calculate total saold items and total amount
						*/
						if (isset($sor_sales_quantity[$valuesip['product_id']])) {
							
							$return_quantity = 0;
						
							if (isset($products_sale_return[$valuesip['product_id']])) {
								
								$return_quantity 		 = $products_sale_return[$valuesip['product_id']];

								$productReturnAmount 	 = ($return_quantity*$sor_sales_quantity[$valuesip['product_id']]['transfer_price_per_piece']);

								$totalSoldQuantity 		+= ($sor_sales_quantity[$valuesip['product_id']]['total_sold_qty'] - $return_quantity);
							
								$totalSoldAmount 		+= (($sor_sales_quantity[$valuesip['product_id']]['total_sold_qty'] - $return_quantity)*$sor_sales_quantity[$valuesip['product_id']]['transfer_price_per_piece']);
								
								$productSoldQuantity 	 = ($sor_sales_quantity[$valuesip['product_id']]['total_sold_qty'] - $return_quantity);

								$productSoldAmount 		 = (($sor_sales_quantity[$valuesip['product_id']]['total_sold_qty'] - $return_quantity)*$sor_sales_quantity[$valuesip['product_id']]['transfer_price_per_piece']);

								$productActualSoldQuantity 	 = $sor_sales_quantity[$valuesip['product_id']]['total_sold_qty'];

								$productActualSoldAmount 		 = (($sor_sales_quantity[$valuesip['product_id']]['total_sold_qty'])*$sor_sales_quantity[$valuesip['product_id']]['transfer_price_per_piece']);

							}else{

								$totalSoldQuantity 		+= $sor_sales_quantity[$valuesip['product_id']]['total_sold_qty'];
							
								$totalSoldAmount 		+= ($sor_sales_quantity[$valuesip['product_id']]['total_sale_value']);
								
								$productSoldQuantity 	 = $sor_sales_quantity[$valuesip['product_id']]['total_sold_qty'];

								$productSoldAmount 		 = ($sor_sales_quantity[$valuesip['product_id']]['total_sale_value']);

								$productActualSoldQuantity = $productSoldQuantity;
								$productActualSoldAmount   = $productSoldAmount;
							}	
							
						}
						/*
						* get image size and path
						*/
						$image 	= $this->model_tool_image->resize( $valuesip['image'], 100, 150);
	                    $width 	= $this->config->get('config_image_additional_width');
	                    $height = $this->config->get('config_image_additional_height');
						/*
						* set every purchase invoice product details
						*/
						
						$sku_temp 				= substr($valuesip['sku'],5);
						$purchase_return_pieces = 0;
						
						if (isset($products_purchase_return[$valuesip['product_id']])) {

							$purchase_return_pieces 		= $products_purchase_return[$valuesip['product_id']]['quantity'];
							$total_purchase_return_pieces  += $products_purchase_return[$valuesip['product_id']]['quantity'];
							$total_purchase_return_amount  += $products_purchase_return[$valuesip['product_id']]['quantity']*$valuesip['transfer_price_per_piece'];

						}
						
						$invoiceProducts[$sku_temp][$j]['sku'] 						= $valuesip['sku'];
						$invoiceProducts[$sku_temp][$j]['image'] 					= $image;
						$invoiceProducts[$sku_temp][$j]['width'] 					= $width;
						$invoiceProducts[$sku_temp][$j]['height'] 					= $height;

						$invoiceProducts[$sku_temp][$j]['pieces'] 					= $valuesip['pieces'];
						$invoiceProducts[$sku_temp][$j]['total_pur_amt'] 					= $this->currency->format($valuesip['pieces']*$valuesip['transfer_price_per_piece']);

						$invoiceProducts[$sku_temp][$j]['return_quantity'] 					= $return_quantity;

						$invoiceProducts[$sku_temp][$j]['productReturnAmount'] 					= $this->currency->format($productReturnAmount);

						$invoiceProducts[$sku_temp][$j]['purchase_return_pieces']	= $purchase_return_pieces;
						$invoiceProducts[$sku_temp][$j]['pieces_sold'] 				= $productSoldQuantity;
						$invoiceProducts[$sku_temp][$j]['amount_sold'] 				= $this->currency->format($productSoldAmount,'INR',1);

						$invoiceProducts[$sku_temp][$j]['actual_pieces_sold'] 				= $productActualSoldQuantity;
						$invoiceProducts[$sku_temp][$j]['actual_amount_sold'] 				= $this->currency->format($productActualSoldAmount,'INR',1);

						$invoiceProducts[$sku_temp][$j]['transfer_price_per_piece'] = $valuesip['transfer_price_per_piece'];

						$j++;
					}
				}
				/*
				* set paid amount to seller
				*/
				$totalPaidAmount 	= 0;
				
				if (!empty($paid_amount_against_invoice)) {

					if (isset($paid_amount_against_invoice[$value['purchase_id']])) {

						$totalPaidAmount = $paid_amount_against_invoice[$value['purchase_id']]['total_paid_amount'];						
					}
					
				}

				$sorInvoicesData[$i]['purchase_id'] 					= $value['purchase_id'];
				$sorInvoicesData[$i]['invoice_no'] 						= $value['invoice_no'];
				$sorInvoicesData[$i]['invoice_date'] 					= $value['invoice_date'];
				$sorInvoicesData[$i]['total_amount'] 					= $this->currency->format($value['total_purchase_value'],'INR',1);
				$sorInvoicesData[$i]['paid_amount'] 					= $this->currency->format($totalPaidAmount,'INR',1);
				$sorInvoicesData[$i]['total_pieces'] 					= $tatalPurchaseQty;
				$sorInvoicesData[$i]['total_purchase_return_pieces'] 	= $total_purchase_return_pieces;
				$sorInvoicesData[$i]['total_purchase_return_amount'] 	= $this->currency->format($total_purchase_return_amount);
				$sorInvoicesData[$i]['amount_sold'] 					= $this->currency->format($totalSoldAmount,'INR',1);
				$sorInvoicesData[$i]['pieces_sold'] 					= $totalSoldQuantity;
				$sorInvoicesData[$i]['unsold_pieces'] 					= (($tatalPurchaseQty-$totalSoldQuantity) - $total_purchase_return_pieces);
				$sorInvoicesData[$i]['unsold_amount'] 					= $this->currency->format(($value['total_purchase_value']-($totalSoldAmount+$total_purchase_return_amount)),'INR',1);
				$sorInvoicesData[$i]['invoice_products'] 				= $invoiceProducts;
				$sorInvoicesData[$i]['seller_balance'] 				= $this->currency->format($totalSoldAmount-$totalPaidAmount);

				$i++;
			}
		}
		
		$data['sorInvoicesData'] 	= $sorInvoicesData;
		$data['pagination'] 		= '';
		$data['results'] 			= '';

		$pagination 		= new Pagination();
        $pagination->total 	= $total_results;
        $pagination->page 	= $page;
        $pagination->limit 	= $limit;
        $pagination->url 	= $this->url->link('seller_panel/account-order/sorInventory'. $url .'&page={page}', 'SSL');
        
        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($total_results) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_results - $limit)) ? $total_results : ((($page - 1) * $limit) + $limit), $total_results, ceil($total_results / $limit));				
        
		$data['sor_inventory'] 			= $this->url->link('seller_panel/account-order/sorInventory','','SSL');
		$data['sor_inventory_link'] 	= $this->url->link('seller_panel/account-order/sorInvoices','', 'SSL');
        $data['sor_sku_link'] 			= $this->url->link('seller_panel/account-order/sorSkus','', 'SSL');
		
		$data['sort']	= $sort;
		$data['order']	= $order;

		$data['header'] = $this->load->controller('seller_panel/seller_header');
		$data['footer'] = $this->load->controller('seller_panel/seller_footer');

		$this->response->setOutput($this->load->view('default/template/seller_panel/sor_invoices.tpl', $data));
    }
    /** 
   * Method to get sor inventory sku wise for a seller
   * @return NULL
   * @author kalyan, 9th Nov, 2017
   */
    public function sorSkus(){

		$data = array();
    	$this->load->autoLoadLanguage('seller_panel/sellers',$data);
    	$this->load->model('tool/image');


		$seller_id 			= $this->customer->getId();
		$data['seller_id'] 	= $seller_id;
		$sorSkusData 		= array();
		$i 					= 0;
		$sorSkuWiseData 	= array();
		$total_results      = 0;

		if( !empty($this->request->get['filter_invoice_no']) ){

			$data['filter_invoice_no'] = $this->request->get['filter_invoice_no'];
		} else {

			$data['filter_invoice_no'] = NULL;
		}
		if ( !empty($this->request->get['sort']) ) {

			$sort = $this->request->get['sort'];
		} else {

			$sort = 'order_processing_date';
		}
		if ( !empty($this->request->get['order']) ) {

			$order = $this->request->get['order'];
		} else {

			$order = 'DESC';
		}
		if ( !empty($this->request->get['page']) ) {

			$page = $this->request->get['page'];
		} else {

			$page = 1;
		}
		
		$url = '';

		if( !empty($this->request->get['filter_invoice_no']) ){

			$url .= '&filter_invoice_no='. $this->request->get['filter_invoice_no'];
		}
		/*
		* general urls for pagination
		*/
        $general_url = $url;

        if (isset($this->request->get['sort'])) {

            $general_url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {

            $general_url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {

            $general_url .= '&page=' . $this->request->get['page'];
        }

        $limit = $this->config->get('config_limit_admin');

		$filter_data = array(
			
			'filter_invoice_no' 	   		   => $data['filter_invoice_no'],
			'sort'                 	 		   => $sort,
			'order'                	 		   => $order,
			'start'                	 		   => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                	 		   => $limit
		);			
		/*
		* get count for paging
		*/
		$total_results 		= $this->_obj_order_stores->getSorProductList($this->db, $seller_id, $filter_data,'get_count');
		/*
		* get sor invoice records
		*/	
		$sorSkuWiseData 	= $this->_obj_order_stores->getSorProductList($this->db, $seller_id, $filter_data);
		
		
		if (!empty($sorSkuWiseData)) {
				
			$product_id_array 			= array();
			$products_return_array  	= array();
			$product_id_array 			= array_column($sorSkuWiseData, 'product_id');
			/*
			* get all product sale
			*/
			$sor_sales_quantity 		= $this->_obj_order_stores->getSorProductSoldQuantity( $this->db, $product_id_array);
			/*
			* get all product sales return
			*/
			$products_sale_return  		= $this->_obj_order_stores->getSorProductsSalesReturns($this->db, $product_id_array);
			/*
			* get all product purchase return
			*/
			$products_purchase_return  	= $this->_obj_order_stores->getSorProductsPurchaseReturns($this->db, $product_id_array);
			/*
			* get paid amount against invoice
			*/
			$paid_amount_for_products	= $this->_obj_order_stores->getTotalPaidAmountAgainstSingleSorSku($this->db, $product_id_array, $seller_id);

			foreach ($sorSkuWiseData as $key => $valuein) {
				
				/*
				* calculate total saold items and total amount
				*/
				$productSoldQuantity 	= 0;
				$productSoldAmount 		= 0;
				$net_quantity 			= 0;
				$productActualSoldQuantity = 0;
				$productActualSoldAmount = 0;

				if (isset($products_purchase_return[$valuein['product_id']])) {

					$sorSkusData[$valuein['sku']]['purchase_return_pieces'] = $products_purchase_return[$valuein['product_id']]['quantity'];
					$sorSkusData[$valuein['sku']]['purchase_return_amt'] 	= ($products_purchase_return[$valuein['product_id']]['quantity'] * $valuein['transfer_price_per_piece']);					

				}else{

					$sorSkusData[$valuein['sku']]['purchase_return_pieces'] = 0;
					$sorSkusData[$valuein['sku']]['purchase_return_amt'] 	= 0;
				}

				$sorSkusData[$valuein['sku']]['total_pur_qty'] 				= $valuein['total_pur_qty'];
				$sorSkusData[$valuein['sku']]['total_pur_amt'] 				= ($valuein['total_pur_qty'] * $valuein['transfer_price_per_piece']);

				if (!empty($sor_sales_quantity)) {

					$return_quantity = 0;
						
					if (isset($products_sale_return[$valuein['product_id']])) {
						
						$return_quantity 		 = $products_sale_return[$valuein['product_id']];

						$productActualSoldQuantity 	 = $sor_sales_quantity[$valuein['product_id']]['total_sold_qty'];

						$productActualSoldAmount 		 = ($sor_sales_quantity[$valuein['product_id']]['total_sold_qty']*$sor_sales_quantity[$valuein['product_id']]['transfer_price_per_piece']);
						
						$productSoldQuantity 	 = ($sor_sales_quantity[$valuein['product_id']]['total_sold_qty'] - $return_quantity);

						$productSoldAmount 		 = (($sor_sales_quantity[$valuein['product_id']]['total_sold_qty'] - $return_quantity)*$sor_sales_quantity[$valuein['product_id']]['transfer_price_per_piece']);
                       
                        $productReturnQuantity   = $return_quantity;

						$productReturnAmount     = ($return_quantity*$sor_sales_quantity[$valuein['product_id']]['transfer_price_per_piece']);;


					}else{
						
						$productReturnQuantity   = 0;
						$productReturnAmount     = '';
						$productSoldQuantity 	 = $sor_sales_quantity[$valuein['product_id']]['total_sold_qty'];

						$productSoldAmount 		 = ($sor_sales_quantity[$valuein['product_id']]['total_sold_qty']*$sor_sales_quantity[$valuein['product_id']]['transfer_price_per_piece']);

						$productActualSoldQuantity = $productSoldQuantity;
						$productActualSoldAmount   = $productSoldAmount;
					}
				}
				/*
				* get image size and path
				*/				
				$image 	= $this->model_tool_image->resize( $valuein['image'], 100, 150);
                $width 	= $this->config->get('config_image_additional_width');
                $height = $this->config->get('config_image_additional_height');
                /*
				* get sold quantity and amount of sor product
				*/
				$totalPaidAmount 	= 0;
				$trxn_done          = '';
				$totalUnsoldAmount 	= 0;
				
				if (isset($paid_amount_for_products[$valuein['product_id']])) {
					
					if ($return_quantity>0) {

						$totalPaidAmount 	= ($paid_amount_for_products[$valuein['product_id']]['total_paid_amount']-($return_quantity*$paid_amount_for_products[$valuein['product_id']]['transfer_price_per_piece']));
					}else{

						$totalPaidAmount 	= $paid_amount_for_products[$valuein['product_id']]['total_paid_amount'];
					}
				}

				$sorSkusData[$valuein['sku']]['sku'] 				= $valuein['sku'];
				$sorSkusData[$valuein['sku']]['image'] 				= $image;
				$sorSkusData[$valuein['sku']]['width'] 				= $width;
				$sorSkusData[$valuein['sku']]['height'] 			= $height;
				
				if ($totalPaidAmount>0) {
					$sorSkusData[$valuein['sku']]['paid_amount'] 		= $this->currency->format($totalPaidAmount,'INR',1);
				}else{
					$sorSkusData[$valuein['sku']]['paid_amount'] 		= $totalPaidAmount;
				}
                
                $sorSkusData[$valuein['sku']]['total_return_qty'] 	= $productReturnQuantity;
               
               if ($productReturnAmount>0) {
                $sorSkusData[$valuein['sku']]['product_return_amt'] 	= $this->currency->format($productReturnAmount); 
                }
               else {
               	$sorSkusData[$valuein['sku']]['product_return_amt'] 	= $productReturnAmount; 
               } 

				$sorSkusData[$valuein['sku']]['total_sold_qty'] 	= $productSoldQuantity;

				$sorSkusData[$valuein['sku']]['total_actual_sold_qty'] 	= $productActualSoldQuantity;
				
				$totalUnsoldAmount 	= (($sorSkusData[$valuein['sku']]['total_pur_amt'] - $productSoldAmount) - $sorSkusData[$valuein['sku']]['purchase_return_amt']);

				$sorSkusData[$valuein['sku']]['total_pur_amt'] 		= $this->currency->format($sorSkusData[$valuein['sku']]['total_pur_amt'],'INR',1);				

				if ($productSoldAmount>0) {
					$sorSkusData[$valuein['sku']]['total_sold_amt'] 		= $this->currency->format($productSoldAmount,'INR',1);
				}else{
					$sorSkusData[$valuein['sku']]['total_sold_amt'] 		= $productSoldAmount;
				}

				if ($productActualSoldAmount>0) {
					$sorSkusData[$valuein['sku']]['total_actual_sold_amt'] 		= $this->currency->format($productActualSoldAmount,'INR',1);
				}else{
					$sorSkusData[$valuein['sku']]['total_actual_sold_amt'] 		= $productActualSoldAmount;
				}				

				if ($totalUnsoldAmount>0) {
					$sorSkusData[$valuein['sku']]['total_unsold_amt'] 		= $this->currency->format($totalUnsoldAmount,'INR',1);
				}else{
					$sorSkusData[$valuein['sku']]['total_unsold_amt'] 		= '';
				}

				$sorSkusData[$valuein['sku']]['unsold_pieces'] 		= (($sorSkusData[$valuein['sku']]['total_pur_qty']-$sorSkusData[$valuein['sku']]['total_sold_qty']) - $sorSkusData[$valuein['sku']]['purchase_return_pieces']);

				$sorSkusData[$valuein['sku']]['trxn_done'] 			= '<i class="fa fa-info-circle paid_tooltip" aria-hidden="true" style="color:blue; margin-left:10px;"></i>';
				$sorSkusData[$valuein['sku']]['purchase_id'] 		= $valuein['purchase_id'];
				$sorSkusData[$valuein['sku']]['product_id'] 		= $valuein['product_id'];
					
			}
		}
		
		$data['sorSkusData'] 	= $sorSkusData;

		$data['pagination'] 	= '';
		$data['results'] 		= '';

		$pagination 		= new Pagination();
        $pagination->total 	= $total_results;
        $pagination->page 	= $page;
        $pagination->limit 	= $limit;
        $pagination->url 	= $this->url->link('seller_panel/account-order/sorSkus'. $url .'&page={page}', 'SSL');
        
        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($total_results) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_results - $limit)) ? $total_results : ((($page - 1) * $limit) + $limit), $total_results, ceil($total_results / $limit));				
        
		$data['sor_inventory'] 					= $this->url->link('seller_panel/account-order/sorInventory','','SSL');
		$data['sor_inventory_link'] 			= $this->url->link('seller_panel/account-order/sorInvoices','', 'SSL');
        $data['sor_sku_link'] 					= $this->url->link('seller_panel/account-order/sorSkus','', 'SSL');
        
        $data['sor_sku_payment_detail_link'] 	= $this->url->link('seller_panel/account-order/sellerPaymentDetailOfSingleSku','', 'SSL');
		
		$data['sort']				= $sort;
		$data['order']				= $order;

		$data['header'] = $this->load->controller('seller_panel/seller_header');
		$data['footer'] = $this->load->controller('seller_panel/seller_footer');

		$this->response->setOutput($this->load->view('default/template/seller_panel/sor_skus.tpl', $data));
    }

  /** 
   * Method to get payment detail of single sku for a seller
   * @return NULL
   * @author kalyan, 17th Nov, 2017
   */
    public function sellerPaymentDetailOfSingleSku(){

    	$this->load->autoLoadLanguage('seller_panel/sellers',$data);

		$data = array();

		$seller_id 			= $this->customer->getId();
		$data['seller_id'] 	= $seller_id;
		$total_results      = 0;

		if( !empty($this->request->get['product_id']) ){

			$data['product_id'] = $this->request->get['product_id'];
		} else {

			$data['product_id'] = NULL;
		}
		if( !empty($this->request->get['purchase_id']) ){

			$data['purchase_id'] = $this->request->get['purchase_id'];
		} else {

			$data['purchase_id'] = NULL;
		}
		if ( !empty($this->request->get['sort']) ) {

			$sort = $this->request->get['sort'];
		} else {

			$sort = 'trxn_utr_date';
		}
		if ( !empty($this->request->get['order']) ) {

			$order = $this->request->get['order'];
		} else {

			$order = 'DESC';
		}
		if ( !empty($this->request->get['page']) ) {

			$page = $this->request->get['page'];
		} else {

			$page = 1;
		}
		
		$url = '';

		if( !empty($this->request->get['product_id']) ){

			$url .= '&product_id='. $this->request->get['product_id'];
		}
		if( !empty($this->request->get['purchase_id']) ){

			$url .= '&purchase_id='. $this->request->get['purchase_id'];
		}
		/*
		* general urls for pagination
		*/
        $general_url = $url;

        if (isset($this->request->get['sort'])) {

            $general_url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {

            $general_url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {

            $general_url .= '&page=' . $this->request->get['page'];
        }

        $limit = $this->config->get('config_limit_admin');

		$filter_data = array(
			
			'product_id' 	   		   		   => $data['product_id'],
			'purchase_id' 	   		   		   => $data['purchase_id'],
			'seller_id' 	   		   		   => $data['seller_id'],
			'sort'                 	 		   => $sort,
			'order'                	 		   => $order,
			'start'                	 		   => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                	 		   => $limit
		);
		/*
		* get count for paging
		*/

		$total_results 		= $this->_obj_order_stores->getPaidAmountDetailAgainstSingleSorSku($this->db, $filter_data, 'get_count');
		/*
		* get payment detail of single sku
		*/	
		$paid_data 		= $this->_obj_order_stores->getPaidAmountDetailAgainstSingleSorSku($this->db, $filter_data);
		
		$return_data 		= $this->_obj_order_stores->getSorProductsSalesReturns($this->db, array($data['product_id']),'single_sku');
		
		if(!empty($paid_data) && !empty($return_data )){

			foreach ($paid_data as $key => $value) {
				if (in_array($value['order_product_id'], $return_data)) {
					unset($paid_data[$key]);
				}
			}
		}
		
		/*
		* calculate total sold paid amount
		*/
		if (!empty($paid_data)) {
			foreach ($paid_data as $key => $value) {
				$paid_data[$key]['sold_amt'] = $this->currency->format(($value['quantity']*$value['piece_in_set']*$value['transfer_price_per_piece']),'INR',1);
			}
		}
		
		$data['paid_data'] 	= $paid_data;

		$data['pagination'] 	= '';
		$data['results'] 		= '';

		$pagination 		= new Pagination();
        $pagination->total 	= $total_results;
        $pagination->page 	= $page;
        $pagination->limit 	= $limit;
        $pagination->url 	= $this->url->link('seller_panel/account-order/sellerPaymentDetailOfSingleSku'. $url .'&page={page}', 'SSL');
        
        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($data['text_pagination'], ($total_results) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_results - $limit)) ? $total_results : ((($page - 1) * $limit) + $limit), $total_results, ceil($total_results / $limit));				
        
		$data['sor_inventory'] 		= $this->url->link('seller_panel/account-order/sorInventory','','SSL');
		$data['sor_inventory_link'] = $this->url->link('seller_panel/account-order/sorInvoices','', 'SSL');
        $data['sor_sku_link'] 		= $this->url->link('seller_panel/account-order/sorSkus','', 'SSL');
		
		$data['sort']				= $sort;
		$data['order']				= $order;

		$data['header'] = $this->load->controller('seller_panel/seller_header');
		$data['footer'] = $this->load->controller('seller_panel/seller_footer');

		$this->response->setOutput($this->load->view('default/template/seller_panel/seller_payment_detail_of_single_sku.tpl', $data));
    }

    /**
	 * Method for seller update invoiced products
	 * @value $seller_id : Integer for the seller_id
	 * @value $order_id : Integer for the order id
	 * @value $suborder_id : String for the suborder id
	 * @value $changes : array of split orders
	 * @return Update Invoiced products by seller
	 * @author Vikas, 2017
	 */
    public function sellerEditedAfterGeneratingInvoice(){

    	if( empty( $this->request->post )){
    		return false;
    	}
		
		$json = array();

    	$order_id 	= $this->request->post['order_id'];
    	$suborder_id= $this->request->post['suborder_id'];
    	$changes 	= $this->request->post['seller_invoiced_changes'];
    	$getSellerid= ($this->request->post['seller_id']) ? $this->request->post['seller_id'] : 0 ;
    	$seller_id 	= $this->customer->getId();
      	$invoice_no = $this->request->post['invoice_no'];
      	$invoice_date = $this->request->post['invoice_date'];
      	$sllr_inv_id = $this->request->post['seller_invoice_id'];

    	// check invoice number when seller given new invoice number then check in database that new invoice number are already saved or Not. if new invoice number already saved in database then error message show.(Please enter new invoice number.)
    	if($getSellerid != $seller_id){
    		$json['error_msg'] =  'You can not access another seller account at the same time ! Please try again after login.'; 
    		$json['redirect_link'] =  $this->url->link('account/logout', '', 'SSL'); 
    		$json['error'] =  2; 
    		echo json_encode($json);
    		exit;
    	}

    	$nick_name = SellerInfo::getSellerFirmDetails($this->db, $seller_id)['nickname'];

		if( $order_id && $suborder_id && $seller_id ) {

			if(!empty($changes)){

				foreach( $changes as $order_product_id_key => $values ){
	    		
		    		$edit_history = array(
						'user_id' 	=> $seller_id,
	                                            'user_name' 	=> $nick_name,
						'user_type' => 'seller',
						'user_ip'	=> $_SERVER['REMOTE_ADDR'],
						'user_agent'=> $_SERVER['HTTP_USER_AGENT'],
						'date_added'=> date('d-m-Y H:i:s'),
						'comment' 	=> key($values),
						'edited' 	=> 'seller_edited_after_generating_invoice'
					);

					$details = array(
						'edit_type' 	=> $values,
						'edit_history' 	=> $edit_history

					);

					$this->_obj_order_stores->sellerEditedAfterGeneratingInvoice( $this->db, 
																				  $order_id, 
																				  $suborder_id, 
																				  $order_product_id_key, 
																				  $details, $seller_id
																				);					
	    		}

	    		$updateInvoiceData = array(
	    									'invoice_no'  => $invoice_no,
	    									'invoice_date'=> $invoice_date,
	    									'sllr_inv_id' => $sllr_inv_id,
	    									'seller_id'	  => $seller_id	
	    								);
	    		
	    		$invoice_no_allowed = $this->_obj_order_stores->sellerUpdateInvoiceNoAndDateAfterGeneratingInvoice($this->db, $updateInvoiceData );
		    	if( !$invoice_no_allowed ){
		    		$json['error_msg'] =  "This invoice number is already used! Please enter new invoice number."; 
		    		$json['error'] =  1; 
		    		echo json_encode($json);
		    		exit;
		    	}

	    		$json['error'] = 5 ;
       			$json['error_msg'] = '';
       			echo json_encode($json);
	    		exit;

			} else {
	    		$json['error'] = 3 ;
	   			$json['error_msg'] = 'Please select any actions!';
	   			echo json_encode($json);
	    		exit;
	    	}

    	} else {
    		$json['error'] = 4 ;
   			$json['error_msg'] = 'Something went wrong. Please try again after some time.!';
   			echo json_encode($json);
    		exit;
    	} 
    }
}
?>