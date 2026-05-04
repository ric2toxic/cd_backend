<?php
class ControllerReportMarketing extends Controller {
	public function index() {
		//$this->load->language('report/marketing');
		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('report/marketing', $data);

		$this->document->setTitle($data['heading_title']);

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}
        //echo "<pre>";print_r($this->request->get);die;
		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		$data['reports'] = array('getCustommersWhoGivenFirstOrderInDateRange' => 'Get Customer Who Given First Order In Data Range',
								 'avgCustomerAcquisition' => 'Average Customer Acquisition',
								 'getTotalNumberOfRegistrations'=> 'Total Number of Registrations',
								 'getTotalCrmDataCount' => 'Total Crm Data Count',
								 'getExclusiveItmes' => 'Exclusive Reports' );


		if (isset($this->request->get['filter_report_name']) && !empty($this->request->get['filter_report_name'])) {
		     $method = $this->request->get['filter_report_name'];
		     $data['html'] = call_user_func(array($this, $method));	
		     $data['method'] = $method;
		     //echo "<pre>";print_r($data['html']);die;
		} else {	
			 $data['html'] = "";
		}

		// if (isset($this->request->get['page'])) {
		// 	$page = $this->request->get['page'];
		// } else {
		// 	$page = 1;
		// }
         
		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_report_name'])) {
			$url .= '&filter_report_name=' . $this->request->get['filter_report_name'];
		}

		// if (isset($this->request->get['filter_order_status_id'])) {
		// 	$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		// }

		// if (isset($this->request->get['page'])) {
		// 	$url .= '&page=' . $this->request->get['page'];
		// }

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('report/marketing', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/marketing');

		//$data['marketings'] = array();

		// $filter_data = array(
		// 	'filter_date_start'	     => $filter_date_start,
		// 	'filter_date_end'	     => $filter_date_end,
		// 	'filter_order_status_id' => $filter_order_status_id,
		// 	'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
		// 	'limit'                  => $this->config->get('config_limit_admin')
		// );

		//$marketing_total = $this->model_report_marketing->getTotalMarketing($filter_data);

		//$results = $this->model_report_marketing->getMarketing($filter_data);

		// foreach ($results as $result) {
		// 	$action = array();

		// 	$action[] = array(
		// 		'text' => $this->language->get('text_edit'),
		// 		'href' => $this->url->link('marketing/marketing/edit', 'token=' . $this->session->data['token'] . '&marketing_id=' . $result['marketing_id'] . $url, 'SSL')
		// 	);

		// 	$data['marketings'][] = array(
		// 		'campaign' => $result['campaign'],
		// 		'code'     => $result['code'],
		// 		'clicks'   => $result['clicks'],
		// 		'orders'   => $result['orders'],
		// 		'total'    => $this->currency->format($result['total'], $this->config->get('config_currency')),
		// 		'action'   => $action
		// 	);
		// }

		$data['column_action'] = $this->language->get('column_action');

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/order_status');

		//$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// $url = '';

		// if (isset($this->request->get['filter_date_start'])) {
		// 	$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		// }

		// if (isset($this->request->get['filter_date_end'])) {
		// 	$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		// }

		// if (isset($this->request->get['filter_order_status_id'])) {
		// 	$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		// }

		// $pagination = new Pagination();
		// $pagination->total = $marketing_total;
		// $pagination->page = $page;
		// $pagination->limit = $this->config->get('config_limit_admin');
		// $pagination->url = $this->url->link('report/marketing', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		//$data['pagination'] = $pagination->render();

		// $data['results'] = sprintf($data['text_pagination'], ($marketing_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($marketing_total - $this->config->get('config_limit_admin'))) ? $marketing_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $marketing_total, ceil($marketing_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		// $data['filter_order_status_id'] = $filter_order_status_id;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/marketing.tpl', $data));
	}

    /**
     * Get order received by affiliate tracking code
     * Using for google ad
     */
	public function conversionReportByAffiliateTrackingCode() {
        $this->load->model('report/ad_performance');
 
        $this->model_report_ad_performance->getConversionByAffiliateTrackingCode('577e51791bc08', '2016-08-01', '2016-08-31');
    }

    /**
     * Number of stores that ordered from this (First time order from Fresh registration)
     * Number of stores that ordered first time (First time order from old regn)
     */
    public function getCustommersWhoGivenFirstOrderInDateRange(){
        $this->load->model('report/customer');

        $start_date = $this->request->get['filter_date_start'];
        $end_date = $this->request->get['filter_date_end'];
        //Customers who signed up in given date range and placed first order in same date range
        $customers_signedup_and_order_in_date_range = $this->model_report_customer->getCustomersWhoGivenFirstOrderInDateRange($start_date, $end_date, 1);
        $total_customers_signedup_and_order_in_date_range = count($customers_signedup_and_order_in_date_range);

        //Customers who signed up before start date and placed first order in given date range
        $customers_order_in_date = $this->model_report_customer->getCustomersWhoGivenFirstOrderInDateRange($start_date, $end_date, 0);

        //POP customer whose first order date is outside date range
        $this->load->model('sale/order');
        $arr_customer_to_be_removed = array();
        foreach ($customers_order_in_date as $customer_id) {
            $first_order = $this->model_sale_order->getFirstOrderDataOfCustomer($customer_id);
            if(count($first_order) > 0) {
                if ($first_order['date_added'] < $start_date || $first_order['date_added'] > $end_date) {
                    $arr_customer_to_be_removed[] = $customer_id;
                }
            }
        }

        $final_array_of_customers = array_diff($customers_order_in_date, $arr_customer_to_be_removed);

        $total_customers_first_order_in_date = count($final_array_of_customers);

        $html = '';
        $html .= "<strong>Date Range : </strong>".$start_date ." to ". $end_date ;
        $html .= '<br />';
        $html .= "Number of stores that ordered from this (First time order from Fresh registration): ". $total_customers_signedup_and_order_in_date_range;
        $html .= '<br />';
        $html .= "Number of stores that ordered first time (First time order from old regn):".$total_customers_first_order_in_date;
        
        return $html ; 
    }

    public function getTotalCustomer() {
        $this->load->model('report/customer');

        $start_date = isset($this->request->get['start_date']) ? $this->request->get['start_date']: '';
        $end_date = isset($this->request->get['end_date']) ? $this->request->get['end_date']: '';

        $total = $this->model_report_customer->getTotalCustomer($start_date, $end_date);

        $html = '';

         $html .= "<strong>Date Range : </strong>".$start_date ." to ". $end_date ;
        $html .= '<br />';
        $html .= "Total customer:". (int)$total;

        return $html ;
    }

	public function avgCustomerAcquisition(){

		if( !isset($this->request->get['filter_date_start']) && !isset($this->request->get['filter_date_end']) ) {
			return "Select Start and End Date";
		}

		$this->load->model('report/customer');

        $start_date = $this->request->get['filter_date_start'];
        $end_date 	= $this->request->get['filter_date_end'];

		$result = $this->model_report_customer->avgCustomerAcquisition($start_date, $end_date);

		//  echo "<pre>"; print_r($result); die;
		$active_sales_staff = $this->model_report_customer->getSalesStaff($start_date, $end_date);

		$html = '';
        $html .= "<strong>Date Range : </strong>".$start_date ." to ". $end_date ;
        $html .= '<br />';
		$html .= "<strong>Total Customer Signup and Ordered : </strong>". (int)count(array_unique(array_column($result, 'customer_id')));
		$html .= '<br />';
        $html .= "<strong>These ".(int)count(array_unique(array_column($result, 'customer_id')))." Customers Placed order : </strong>". (int)count($result) ." (Include first or next order)";
		$html .= '<br />';
		$html .= "<strong>Total Sales Staff : </strong>". (int)count($active_sales_staff['total_sales_staff'])." (Include Active or Inactive)";
		$html .= '<br />';
		$html .= "<strong>Total Active Sales Staff : </strong>". count($active_sales_staff['active_sales_staff']);
		$html .= '<br />';

		// make an array of active sales staff according to there role .
		foreach($active_sales_staff['active_sales_staff'] as $active_staff) {
			$new_active_staff[$active_staff['role']][] = $active_staff['staff_id']; 
		}

		$new_arr = array();
		foreach($result as $val) {
			if($val['active_status'] == 1) {
				if(isset($val['role'])){
					$new_arr['role'][$val['role']]['order'][] = $val['order_id'];
					$new_arr['role'][$val['role']]['customer'][] = $val['customer_id'];
				}
			}
		}
		
		$html .= '<br /><strong> ------ Average Customer Acquisition per sales staff ------ </strong> <br />';
		if(isset($new_active_staff)){
			foreach($new_active_staff as $key => $val){
				$customer_count = isset($new_arr['role'][$key]['customer'])?$new_arr['role'][$key]['customer']:array();
				$html .= "<strong>Active ".$key." Sales Staff : </strong>". (int)count($new_active_staff[$key])." people --- Acquire " .(int)count(array_unique($customer_count)). " customer --- AVG. customer ". (int)count(array_unique($customer_count)) / (int)count($new_active_staff[$key]);
				$html .= '<br />';
			}
		}
		
		$html .= '<br /><strong> ------ Average Order per sales staff ------ </strong> <br />';
		if(isset($new_active_staff)){
			foreach($new_active_staff as $key => $val){
				$order_count = isset($new_arr['role'][$key]['order'])?$new_arr['role'][$key]['order']:array();
				$html .= "<strong>Active ".$key." Sales Staff : </strong>". (int)count($new_active_staff[$key])." people --- Placed " .(int)count($order_count). " order --- AVG. order ". (int)count($order_count) / (int)count($new_active_staff[$key]);
				$html .= '<br />';
			}
		}
		return $html;
	}
	

	public function getTotalNumberOfRegistrations() {

		if( !isset($this->request->get['filter_date_start']) && !isset($this->request->get['filter_date_end']) ) {
			return "Select Start and End Date";
		}

		$this->load->model('report/customer');

        $start_date = $this->request->get['filter_date_start'];
        $end_date = $this->request->get['filter_date_end'];
		
		$result = $this->model_report_customer->getNumberOfRegistrations($start_date, $end_date);

		$html = '';
        $html .= "<strong>Date Range : </strong>".$start_date ." to ". $end_date ;
        $html .= '<br />';
        $html .= "<strong>Total Number of registrations : </strong>". (int)$result['total_registration']['total_customers'];
		$html .= '<br />';
		$html .= "<strong>Total Number of registrations - APP : </strong>". (int)$result['total_app_registration']['total_customers'];

		return $html;
	}

	public function getTotalCrmDataCount(){

		if( !isset($this->request->get['filter_date_start']) && !isset($this->request->get['filter_date_end']) ) {
			return "Select Start and End Date";
		}

		$this->load->model('lead/lead', 'frontend');

        $start_date = $this->request->get['filter_date_start'];
        $end_date = $this->request->get['filter_date_end'];

		$result = $this->frontend_model_lead_lead->getTotalCrmDataCount($start_date, $end_date);

		$html = '';
        $html .= "<strong>Date Range : </strong>".$start_date ." to ". $end_date ;
        $html .= '<br />';
        $html .= "<strong>Total Number of Leads : </strong>". (int)$result['total_lead'];

		return $html;
	}

	public function getExclusiveItmes(){
		if( !isset($this->request->get['filter_date_start']) && !isset($this->request->get['filter_date_end']) ) {
			return "Select Start and End Date";
		}
		
		$this->load->model('report/marketing');
		
		$data['start_date'] = $this->request->get['filter_date_start'];
		$data['end_date']   = $this->request->get['filter_date_end'];

		$exclusive_products                 = $this->model_report_marketing->getExclusiveProducts();
		$exclusive_total_cart_products      = ''; //$this->model_report_marketing->getExclusiveTotalCartProducts();
		$exclusive_modified_cart_products   = ''; //$this->model_report_marketing->getExclusiveTotalCartProducts($data);
		$exclusive_sold_products            = $this->model_report_marketing->getExclusiveSoldTotalProducts($data,'all');
		$exclusive_item_sold_by_store       = $this->model_report_marketing->getExclusiveSoldTotalProducts($data,'store');
		$get_top_ten_exclusive_itmes        = $this->model_report_marketing->getTopTenExclusiveProducts($data);

		// echo "<pre>"; print_r($exclusive_total_cart_products); die;

		$html = '';
		$html .= "<strong>Date Range : </strong>".$data['start_date'] ." to ". $data['end_date'] ;
		$html .= '<br />';
		$html .= '<br />';
		$html .= "<strong>Total Number of Exclusive products : </strong>". (int)$exclusive_products['count'];
		$html .= '<br />';
		$html .= '<br />';
		$html .= "<strong>Total Carts which has exclusive products : </strong>". (int)$exclusive_total_cart_products;
		$html .= '<br />';
		$html .= '<br />';
		$html .= "<strong>Total Modified Carts which has exclusive products Between date ".$data['start_date']." to ".$data['end_date']." : </strong>". (int)$exclusive_modified_cart_products;
		$html .= '<br />';
		$html .= '<br />';
		$html .= "<strong>Total sold itmes has exclusive Between date ".$data['start_date']." to ".$data['end_date']." : </strong>". (int)$exclusive_sold_products['count'];
		$html .= '<br />';
		$html .= '<br />';
		$html .= "<strong>Total exclusive itmes sold from store Between date ".$data['start_date']." to ".$data['end_date']." : </strong>". (int)$exclusive_item_sold_by_store['count'];
		$html .= '<br />';
		$html .= '<br />';
		$html .= "<strong>Top Ten exclusive products : </strong>";
		$html .= '<br />';
		$html .= "<table class='table'>";
		$html .=    "<tr>";
		$html .=    	"<th>S.No</th>";
		$html .=    	"<th>Model</th>";
		$html .=    "</tr>";
		
		$i = 1;
		foreach($get_top_ten_exclusive_itmes as $values){
			$html .=    "<tr>";
			$html .= "<td>".$i."</td>"; 
			$html .= "<td>".$values['model']."</td>";
			$html .=    "</tr>";
			
			$i++;
		}
		$html .="</table>";

		return $html;
		
	}
}
