<?php
class ControllerDashboardRecent extends Controller {

	public function index() {
		$data = array(); // Initializing the data array to be passed on to template files
        // Autoloading the lanugage
        $this->load->autoLoadLanguage('dashboard/recent', $data);
        

		$data['token'] = $this->session->data['token'];

		// Last 5 Orders
		$data['orders'] = array();

		$filter_data = array(
			'sort'  => 'o.order_id', // o.date_added DESC is same as o.order_id DESC
			'order' => 'DESC',
			'start' => 0,
			'limit' => 5
		);

    $results = array();

    $data_result   = $this->model_sale_order->getOrders($filter_data);
    $orders        = $data_result['data'] ?? array();

    if ($orders) {
        
      $selector = array('order' => array('select' => array('order_no','firstname','lastname','shipping_company', 
                                                           'shipping_city','date_added','total','currency_code',
                                                           'currency_value')), 
                        'suborder' => array('select' => array('order_status_id', 'total')) 
                       );
      
      foreach ($orders as $order_row) {
        $results[$order_row['order_id']] = OrderInfo::getOrderInfo($this->db, $order_row['order_id'], '', $selector);
      }
    }

    $this->load->model('localisation/order_status');
    $order_statuses_qry = $this->model_localisation_order_status->getOrderStatuses();

    $order_statuses = array();
    foreach($order_statuses_qry as $order_status_data){
        $order_statuses[$order_status_data['order_status_id']] = $order_status_data['name'];
    }

    foreach($results as $key => $result){

      $data['orders'][$key]['order'] = $result['order'];
      $data['orders'][$key]['suborder'] = $result['suborder'];


      // additional info in order
      $data['orders'][$key]['order']['customer']  = trim($result['order']['firstname'] .' ' .$result['order']['lastname']);
      // Currency formatting on order total
      $data['orders'][$key]['order']['total'] = $this->currency->format($data['orders'][$key]['order']['total'], 
                                                                        $result['order']['currency_code'],
                                                                        $result['order']['currency_value'],
                                                                        true);                          
      $data['orders'][$key]['status'] = array();
            
      foreach ($result['suborder'] as $key_suborder_id => $suborder_data) {
        $data['orders'][$key]['suborder'][$key_suborder_id]['status'] = $order_statuses[$suborder_data['order_status_id']] ;
        $data['orders'][$key]['status'][$key_suborder_id]['status'] = $key_suborder_id . " : " .  $order_statuses[$suborder_data['order_status_id']] ;
        $data['orders'][$key]['suborder'][$key_suborder_id]['view'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order']['order_id'] . '&suborder_id=' . $key_suborder_id , 'SSL');

        // Currency formatting on suborder total
        $suborder_total = $this->currency->format($data['orders'][$key]['suborder'][$key_suborder_id]['total'], 
                                                  $result['order']['currency_code'],
                                                  $result['order']['currency_value'],
                                                  true);
        $data['orders'][$key]['suborder'][$key_suborder_id]['total'] = $suborder_total;
      }
    }
		return $this->load->view('dashboard/recent.tpl', $data);
	}
}
