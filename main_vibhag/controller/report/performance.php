<?php
class ControllerReportPerformance extends Controller {
	public function index() {

		$data = array();
		//autoloading the language
		$this->load->autoLoadLanguage('report/performance',$data);

		$this->load->model('report/performance');
		$this->load->model('sale/customer');
		$this->load->model('report/customer');

		$this->document->setTitle($data['heading_performance_title']);

        if (isset($this->request->get['filter_order_no'])) {
			$filter_order_no = $this->request->get['filter_order_no'];
		} else {
			$filter_order_no = null;
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

        if (isset($this->request->get['filter_sales_staff'])) {
			$filter_sales_staff = $this->request->get['filter_sales_staff'];
		} else {
			$filter_sales_staff = null;
		}

		if (isset($this->request->get['filter_payment_code'])) {
			$filter_payment_code = $this->request->get['filter_payment_code'];
		} else {
			$filter_payment_code = null;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['filter_city'])) {
			$filter_city = $this->request->get['filter_city'];
		} else {
			$filter_city = null;
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = null;
		}
        
        //for franchise order
        if (isset($this->request->get['filter_franchise_order'])) {
			$filter_franchise_order = $this->request->get['filter_franchise_order'];
		} else {
			$filter_franchise_order = NULL;
		}
        

		$url = '';

        if (isset($this->request->get['filter_order_no'])) {
			$url .= '&filter_order_no=' . $this->request->get['filter_order_no'];
		}

		if (isset($this->request->get['filter_date_from'])) {
			$url .= '&filter_date_from=' . $this->request->get['filter_date_from'];
		}

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

        if (isset($this->request->get['filter_sales_staff'])) {
			$url .= '&filter_sales_staff=' . $this->request->get['filter_sales_staff'];
		}

		if (isset($this->request->get['filter_payment_code'])) {
			$url .= '&filter_payment_code=' . $this->request->get['filter_payment_code'];
		}

		if (isset($this->request->get['filter_city'])) {
			$url .= '&filter_city=' . $this->request->get['filter_city'];
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
        
        if (isset($this->request->get['filter_franchise_order'])) {
			$url .= '&filter_franchise_order=' . $this->request->get['filter_franchise_order'];
		}

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $data['heading_performance'],
			'href' => $this->url->link('report/performance', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['payment_codes'] = $this->model_report_performance->getDistinctPaymentCode();

		$data['orders'] = array();

		$filter_data = array(
            'filter_order_no'     => $filter_order_no,
			'filter_date_from'    => $filter_date_from,
			'filter_date_to' 	  => $filter_date_to,
            'filter_sales_staff'  => $filter_sales_staff,
            'filter_payment_code' => $filter_payment_code,
			'filter_city'		  => $filter_city,
			'filter_order_status' => $filter_order_status,
            'start'          	  => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           	  => $this->config->get('config_limit_admin'),
            'filter_franchise_order' => $filter_franchise_order
		);


		$total_orders = $this->model_report_performance->getTotalPerfomanceDetails($filter_data);

		$orders = $this->model_report_performance->getPerformanceDetails($filter_data, 'DESC');

		$customer_ids = array();

		if( $orders ) {
			$selector = array(
							'order' => array('select' => array(
												'order_no', 'date_added', 'customer_id', 'firstname', 'lastname',
												'shipping_company', 'shipping_city', 'shipping_postcode', 'telephone', 'total',
												'comment','currency_code','currency_value'
											)),
							'suborder' => array('select' => array('order_status_id', 'shipping_method', 'total')),
							'order_history'=> array('select' => array('order_id', 'suborder_id', 'order_status_id','date_added','comment'),'sort'=> array('date_added' => 'ASC'))
						);

			foreach( $orders as $order_row ) {
				$results[$order_row['order_id']] = OrderInfo::getOrderInfo($this->db, $order_row['order_id'],'',$selector);
				$customer_ids[] = $results[$order_row['order_id']]['order']['customer_id'];
			}
		}

		$customer_ids = array_unique($customer_ids);
		// Finding all the related id(s) and its duplicate/master marking status
        $duplicate_master_ids = $this->model_sale_customer->getAllMasterDuplicateCustomerIds($customer_ids);
        // all customer id of master id
        $all_customer_ids = array_column($duplicate_master_ids, 'all_related_ids');

        // Life Time Value show on order list page
        $ltvTotalOrders         = array();
        if(!empty($all_customer_ids)){
            $ltvTotalOrders         = $this->model_report_customer->getLifeTimeOrdersCountOfCustomers($all_customer_ids);
        }

        $data['sales_staff_list'] = $this->model_report_performance->getSalesStaffList();

        //get all order status
		$this->load->model('localisation/order_status');
		$order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

		$order_statuses = array();
		foreach($order_statuses_qry as $order_status_data){
			$order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
		}


		if(isset($results)){
			foreach ($results  as $key_order_id => $result) {
				foreach ($result['suborder'] as $key_suborder_id => $suborder_data) {
					$result['suborder'][$key_suborder_id]['status'] = $order_statuses[$suborder_data['order_status_id']];

					// Currency formatting on suborder total
					$result['suborder'][$key_suborder_id]['total'] = $this->currency->format(
																		$suborder_data['total'],
																		$result['order']['currency_code'],
																		$result['order']['currency_value'],
																		true);

					$last_update_history = '';
					if (!empty($suborder_data['order_history'])) {
						$last_update_history = end($suborder_data['order_history']);
						foreach($suborder_data['order_history'] as $order_history){
							$delivery_time = '';
							if( $order_history['order_status_id'] == 15 ){
								$delivery_time = $order_history['date_added'];
								break;
							}
						}
					}

					$result['suborder'][$key_suborder_id]['last_update_history'] = $last_update_history['comment'];
					$result['suborder'][$key_suborder_id]['delivery_time'] = $delivery_time;

					$data['suborder_view_button'] = 0;
					if ($this->user->hasPermission('access', 'sale/order')) {
						$result['suborder'][$key_suborder_id]['view'] = $this->url->link('sale/order/info',
																					'token=' . $this->session->data['token'] .
																					'&order_id=' . $result['order']['order_id'] .
																					'&suborder_id=' . $key_suborder_id ,
																					'SSL');
						$data['suborder_view_button'] = 1;

					}
				}
				// get Sales Staff
				$data['order_taged_sales_staff'] = $this->model_report_performance->getOrderTagedSalesStaff($result['order']['order_id']);
				$salesStaffList = array();
				if(!empty($data['order_taged_sales_staff'])){
					foreach($data['order_taged_sales_staff'] as $staff) {
						$salesStaffList[] = ( !empty($data['sales_staff_list'][$staff['sales_staff_id']]) ) ?
								$data['sales_staff_list'][$staff['sales_staff_id']] : '--None--' ;
					}
				}else{
					$salesStaffList[] = '--None--';
				}

				$all_related_ids = $duplicate_master_ids[$result['order']['customer_id']]['all_related_ids'];
            	$all_related_ids_array = explode(',',$all_related_ids);            

            	$getLtvTotalOrders         = 0;
	            if(!empty($all_related_ids_array)){
	                foreach ($all_related_ids_array as $cust_id) {
	                   $getLtvTotalOrders += !empty($ltvTotalOrders[$cust_id]['total_orders']) ? $ltvTotalOrders[$cust_id]['total_orders'] : 0 ;
	                }
	            }

				$data['orders'][] = array(
					'order_id'			=> $result['order']['order_id'],
					'order_no'      	=> $result['order']['order_no'],
					'date_added'		=> $result['order']['date_added'],
					'customer_id'		=> $result['order']['customer_id'],
					'client_name'		=> $result['order']['firstname'] . ' '. $result['order']['lastname'],
					'company'       	=> $result['order']['shipping_company'],
					'shipping_city'		=> $result['order']['shipping_city'],
					'telephone'			=> $result['order']['telephone'],
					'sales_staff_name'	=> $salesStaffList,
					'comment'			=> $result['order']['comment'],
					'shipping_postcode'	=> $result['order']['shipping_postcode'],
					'total'  			=> number_format($result['order']['total'],2),
					'suborders'			=> $result['suborder'],
					'count_order' 		=> $getLtvTotalOrders,
					'total_order_link'  => $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_customer_id=' . $result['order']['customer_id'], 'SSL')
				);
			}
		}

		$pagination = new Pagination();
		$pagination->total = $total_orders;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/performance/', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($data['text_pagination'], ($total_orders) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_orders - $this->config->get('config_limit_admin'))) ? $total_orders : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_orders, ceil($total_orders / $this->config->get('config_limit_admin')));

		$data['show_csv_button'] = 0;
		if($this->user->hasPermission('modify','report/performance')){
			$data['b2c_csv'] = $this->url->link('report/performance/csv', 'token=' . $this->session->data['token'] . $url, 'SSL');
			$data['show_csv_button'] = 1;
		}


        $data['column_order_no'] = $this->language->get('column_order_no');

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

        $data['filter_order_no'] = $filter_order_no;
		$data['filter_date_from'] = $filter_date_from;
		$data['filter_date_to'] = $filter_date_to;
        $data['filter_sales_staff'] = $filter_sales_staff;
		$data['filter_city'] = $filter_city;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_payment_code'] = $filter_payment_code;
        $data['filter_franchise_order'] = $filter_franchise_order;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/performance.tpl', $data));

	}

	public function autocomplete() {

		$json = array();

		if ( isset($this->request->get['filter_order_no'])) {

            if (isset($this->request->get['filter_order_no'])) {
				$filter_order_no = $this->request->get['filter_order_no'];
			} else {
				$filter_order_no = '';
			}

			$this->load->model('sale/order');

			$filter_data = array(
				'filter_order_no'  => $filter_order_no,
				'start'        => 0,
				'limit'        => 10
			);

			$results = $this->model_sale_order->get_order_no($filter_data);

			foreach ($results as $result) {

				$json[] = array(
					'order_no'       => $result['order_no']
				);
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function CSV() {		
            ini_set('memory_limit', -1);
		$data['orders'] = array();
        $this->load->model('report/performance');
        
        if (isset($this->request->get['filter_order_no'])) {
			$filter_order_no = $this->request->get['filter_order_no'];
		} else {
			$filter_order_no = null;
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

        if (isset($this->request->get['filter_sales_staff'])) {
			$filter_sales_staff = $this->request->get['filter_sales_staff'];
		} else {
			$filter_sales_staff = null;
		}

		if (isset($this->request->get['filter_city'])) {
			$filter_city = $this->request->get['filter_city'];
		} else {
			$filter_city = null;
		}
		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = null;
		}

		if (isset($this->request->get['filter_payment_code'])) {
			$filter_payment_code = $this->request->get['filter_payment_code'];
		} else {
			$filter_payment_code = null;
		}
        
        //for franchise order
        if (isset($this->request->get['filter_franchise_order'])) {
			$filter_franchise_order = $this->request->get['filter_franchise_order'];
		} else {
			$filter_franchise_order = NULL;
		}

        $filter_data = array(
            'filter_order_no'      	=> $filter_order_no,
			'filter_date_from'    	=> $filter_date_from,
			'filter_date_to' 	   	=> $filter_date_to,
            'filter_sales_staff' 	=> $filter_sales_staff,
			'filter_city'			=> $filter_city,
			'filter_order_status'	=> $filter_order_status,
			'filter_payment_code'	=> $filter_payment_code,
            'filter_franchise_order' => $filter_franchise_order
		);

		$orders = $this->model_report_performance->getPerformanceDetails($filter_data, 'DESC');
		$results = array();		
		$tracking_data = array();
                if( $orders ) {
			$selector = array(
							'order' => array('select' => array(
												'order_no', 'date_added', 'customer_id', 'firstname', 'lastname',
												'shipping_company', 'shipping_city', 'shipping_postcode', 'telephone', 'total',
												'comment','currency_code','currency_value', 'code_version',
												'payment_code'
											)),
							'suborder' => array(),
							'order_history'=> array('select' => array('order_id', 'suborder_id', 'order_status_id','date_added','comment'),'sort'=> array('date_added' => 'ASC'))
						);

			foreach( $orders as $order_row ) {
                            
                                $results[$order_row['order_id']] = OrderInfo::getOrderInfo($this->db, $order_row['order_id'],'',$selector);
                                $results[$order_row['order_id']]['order_status'] = $order_row['name'];
                                $status_ids = array();
                                if(!empty($results[$order_row['order_id']]['suborder']))
                                {
                                    foreach($results[$order_row['order_id']]['suborder'] as $row)
                                    {
                                        if(isset($row['courier_partner']) && isset($row['tracking_no']))
                                        {
                                          $courier_name = strtolower(str_replace(array(' ','_','-'),array('','',''),$row['courier_partner']));  
                                          $tracking_data[$courier_name][] = $row['tracking_no'];
                                        }
                                        if(isset($row['order_status_id'])){
                                            $status_ids[] = $row['order_status_id'];
                                        }
                                    }
                                }
                                
                                // if any one sub order with cancel status then order status will be Partial cancelled
                                if(count($status_ids) > 1 && in_array(2,$status_ids)) { 
                                    $results[$order_row['order_id']]['order_status'] = 'Partial cancelled';
                                } 
                            }
		}
	
        $docketDimensions = $this->getDimensionsForAllDockets($tracking_data);       
                
        $sales_staff_list = $this->model_report_performance->getSalesStaffList();
        ob_clean();
        $today = Date('d_M_y');

        $dir_name = DIR_SYSTEM . 'download/assets/SalesPerformanceReports/' . $filter_order_no . '/' . $today;
        $file = $dir_name . '/salesperformancereports-'.time().'.csv';
        if (!file_exists($dir_name)) {
            mkdir($dir_name, 0777, true);
        }
        $fp = fopen($file , 'w');

        $data = array(	'Order No',
        				'Order Date',
        				'Order Status',
        				'Delivery Time',
        				'Customer ID',
        				'Buyer Name',
        				'Company',
        				'Buyer City',
        				'Phone No.',
        				'Shipping Pin Code',
        				'Total',
        				'Sales Staff',
        				'Payment Code',
        				'Docket No',
        				'Courier Name',
                                        'Weight'
                                        );
        fputcsv($fp, $data);
	    if(!empty($results) && count($results)>0){    

	 		foreach ($results as $result) {

	            $order_id	 		= $result['order']['order_id'];
	            $order_no      		= $result['order']['order_no'];
	            $order_status 		= $result['order_status'];
	            $date_added			= $result['order']['date_added'];
	            $customer_id		= $result['order']['customer_id'];
				$client_name		= $result['order']['firstname'] . ' '. $result['order']['lastname'];
	            $company            = $result['order']['shipping_company'];
				$shipping_city		= $result['order']['shipping_city'];
				$telephone			= $result['order']['telephone'];
	            $shipping_postcode	= $result['order']['shipping_postcode'];
				$total  			= number_format($result['order']['total'],2);

				$data['order_taged_sales_staff'] = $this->model_report_performance->getOrderTagedSalesStaff($result['order']['order_id']);

				if(!empty($data['order_taged_sales_staff'])){
					$sales_staff_arr = array();
					foreach($data['order_taged_sales_staff'] as $staff) {
						$sales_staff_arr[] = ( !empty($sales_staff_list[$staff['sales_staff_id']]) ) ?
								$sales_staff_list[$staff['sales_staff_id']] : '--None--' ;
					}
					$sales_staff_name = implode(',', $sales_staff_arr);
				}else{
					$sales_staff_name = '--None--';
				}
				$payment_code = $result['order']['payment_code'];


				$docket_no = '';
				$courier_name = '';
				$delivery_time = '';
                                
				if(isset($result['suborder'])) {
					foreach ($result['suborder'] as $key => $suborder_data) {
						$docket_no    = isset($suborder_data['tracking_no']) ? $suborder_data['tracking_no'] : '';
						$courier_name = isset($suborder_data['courier_partner']) ? $suborder_data['courier_partner'] : '';

						foreach ($suborder_data['order_history'] as $index => $order_history) {
							if( $order_history['order_status_id'] == 15 ){
								$delivery_time = $order_history['date_added'];
								break;
							}
						}
					}
				}
                                
                                $weight = '';
                                $courierKey = strtolower(str_replace(array(' ','_','-'),array('','',''),$courier_name));
                                if(key_exists($courierKey, $docketDimensions))
                                {
                                    $weight = isset($docketDimensions[$courierKey][$docket_no]['weight'])
                                                    ?$docketDimensions[$courierKey][$docket_no]['weight']
                                                    :'';
                                }
                                
				$data = array($order_no,
							$date_added,
							$order_status,
							$delivery_time,
							$customer_id,
							$client_name,
							$company,
							$shipping_city,
							$telephone,
							$shipping_postcode,
							$total,
							$sales_staff_name,
							$payment_code,
							$docket_no,
							$courier_name,
                                                        $weight
							);

	            fputcsv($fp, $data);
				/*
				foreach($result['suborder'] as $suborder_key => $suborder_data){
					$buyer_invoice = new BuyerInvoice( $this );
	                $buyer_invoice->setOrderInfo($result);
					$totals = $buyer_invoice->getTotals(
	                    $result['order']['order_id'] ,
	                    $suborder_data['suborder_id']
	                );

					$discount_total = 0;
					if( !empty($totals['paycharge']) ){
						$discount_total += $totals['paycharge']['value'];
					}
					if( !empty($totals['coupon']) ){
						$discount_total += $totals['coupon']['value'];
					}
					if( !empty($totals['discount']) ){
						$discount_total += $totals['discount']['value'];
					}
					if( !empty($totals['deal_discount']) ){
						$discount_total += $totals['deal_discount']['value'];
					}
					if( !empty($totals['cashback']) ){
						$discount_total += $totals['cashback']['value'];
					}

					$suborder_id = $suborder_data['suborder_id'];
					//$suborder_total = $totals['sub_total']['value'];
					$suborder_total = $result['order']['total'];
					$suborder_discount = $discount_total;
					$suborder_tax = $totals['tax']['value'];
					$suborder_shipping = $totals['shipping']['value'];

	 				$data = array(	$order_no,
	 								$date_added,
	 								$customer_id,
	 								$client_name,
	 								$company,
	 								$shipping_city,
	 								$telephone,
	 								$shipping_postcode,
	 								$total,
	 								$sales_staff_name,
	 								$suborder_id,
	 								$suborder_total,
	 								$discount_total,
	 								$suborder_tax,
	 								$suborder_shipping);
	            	fputcsv($fp, $data);
	 			}*/
	        }
	    }
		if (file_exists($file)) {
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename="'.basename($file).'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    readfile($file);
		    exit;
		}
        fclose($fp);
	}
        
        protected function getDimensionsForAllDockets($list = array())
        {
            $docket_data = array();
            if(!empty($list))
            {
               foreach($list as $key => $value )
               {
                   $dimensionData = $this->model_report_performance->getDocketDimensionsString($key,$value);
                   if(!empty($dimensionData)) {
                       $docketDimensionValues = $this->processStringToGetDimensionData($key,$dimensionData);
                       $docket_data[$key] = $docketDimensionValues;
                       //$docket_data = $docketDimensionValues;
                   }
               }
            }
            return $docket_data;
        }
        protected function processStringToGetDimensionData($courier,$post_data_list)
        {
           $list = array();
           if(!empty($post_data_list))
           {
               foreach($post_data_list as $row){
                  $list[$row['docket_no']] = $this->getCourierDimensions($courier,$row['post_data']);
               }
           }
           return $list;
        }
        protected function getCourierDimensions($courier, $post_values)
        {
            $dimentions = array('weight'=>0,'breadth'=>0,'length'=>0,'height'=>0,'count'=>0);

            if(strtolower($courier) == 'gati' && !empty($post_values)){
                $postValues = new SimpleXMLElement($post_values);

                if(isset($postValues->details->req->ACTUAL_WT))
                {
                    $dimentions['weight'] = (float)$postValues->details->req->ACTUAL_WT;
                }
            }

            if(strtolower($courier) == 'dotzot' && !empty($post_values)){

                $postValues = new SimpleXMLElement($post_values);

                if(isset($postValues->DocketList->DocketList->Weight))
                {
                    $dimentions['weight'] = (float)$postValues->DocketList->DocketList->Weight;
                }
            }

            if(strtolower($courier) == 'bluedart' && !empty($post_values)){

                $postValues = json_decode($post_values,true);

                $dimentions['weight'] = isset($postValues['Request']['Services']['ActualWeight'])
                                        ? (float)$postValues['Request']['Services']['ActualWeight']
                                        : '';
                $dimentions['breadth']= isset($postValues['Request']['Services']['Dimensions']['Dimension']['Breadth'])
                                        ? (float)$postValues['Request']['Services']['Dimensions']['Dimension']['Breadth']
                                        : '';
                $dimentions['height'] = isset($postValues['Request']['Services']['Dimensions']['Dimension']['Height'])
                                        ? (float)$postValues['Request']['Services']['Dimensions']['Dimension']['Height']
                                        : '';
                $dimentions['length'] = isset($postValues['Request']['Services']['Dimensions']['Dimension']['Length'])
                                        ? (float)$postValues['Request']['Services']['Dimensions']['Dimension']['Length']
                                        : '';
                $dimentions['count'] = isset($postValues['Request']['Services']['Dimensions']['Dimension']['Count'])
                                    ? (float)$postValues['Request']['Services']['Dimensions']['Dimension']['Count']
                                    : '';
            }
            if((strtolower($courier) == 'connect-india' || strtolower($courier) == 'connectindia') && !empty($post_values)){
                $postValues = json_decode($post_values,true);
                $dimentions['weight'] = isset($postValues['consignments'][0]['weightInKilogram'])
                                        ? (float)$postValues['consignments'][0]['weightInKilogram']
                                        : '';
                $dimentions['breadth']= isset($postValues['consignments'][0]['widthInMeter'])
                                        ? (float) convertUnit($postValues['consignments'][0]['widthInMeter'],'METER_TO_CM')
                                        : '';
                $dimentions['height'] = isset($postValues['consignments'][0]['heightInMeter'])
                                        ? (float)convertUnit($postValues['consignments'][0]['heightInMeter'],'METER_TO_CM')
                                        : '';
                $dimentions['length'] = isset($postValues['consignments'][0]['lengthInMeter'])
                                        ? (float)convertUnit($postValues['consignments'][0]['lengthInMeter'],'METER_TO_CM')
                                        : '';
            }

           return $dimentions;
        }
        
        
        
}