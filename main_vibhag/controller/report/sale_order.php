<?php
class ControllerReportSaleOrder extends Controller {
	public function index() {

		$data = array(); // Initializing the data array to be passed on to template files
	    // Autoloading the lanugage
	    $this->load->autoLoadLanguage('report/sale_order', $data);

		$this->document->setTitle($data['heading_title']);

		if (!empty($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (!empty($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (!empty($this->request->get['filter_group'])) {
			$filter_group = $this->request->get['filter_group'];
		} else {
			$filter_group = 'day';
		}

		// filter_order_status_id can have value 0. So avoiding using empty() check here.
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		}   else {
			$filter_order_status_id = -2;
		}

		$url = '';
		$url .= '&filter_date_start=' . $filter_date_start;
		$url .= '&filter_date_end=' . $filter_date_end;
		$url .= '&filter_group=' . $filter_group;
		$url .= '&filter_order_status_id=' . $filter_order_status_id;


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $data['text_home'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('report/sale_order', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/sale');

		$data['orders'] = array();

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_group'           => $filter_group,
			'filter_order_status_id' => $filter_order_status_id
		);

		$results = $this->model_report_sale->getOrders($filter_data);

		foreach ($results as $result) {
                        $data['orders'][] = array(
				'date_start'        => date($data['date_format_short'], strtotime($result['date_start'])),
				'date_end'          => date($data['date_format_short'], strtotime($result['date_end'])),
				'orders'            => $result['orders'],
                'suborders'         => $result['suborders'], 
                'pending_orders'    => $result['pending_orders'], 
                'pending_suborders' => $result['pending_suborders'], 
                'pending_total'     => $this->currency->format($result['pending_total'], 'INR', 1),  
				'total'             => $this->currency->format($result['total'], 'INR', 1) 
			);
		}
		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['groups'] = array();

		$data['groups'][] = array(
			'text'  => $data['text_year'],
			'value' => 'year',
		);

		$data['groups'][] = array(
			'text'  => $data['text_month'],
			'value' => 'month',
		);

		$data['groups'][] = array(
			'text'  => $data['text_week'],
			'value' => 'week',
		);

		$data['groups'][] = array(
			'text'  => $data['text_day'],
			'value' => 'day',
		);


		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_group'] = $filter_group;
		$data['filter_order_status_id'] = $filter_order_status_id;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/sale_order.tpl', $data));
	}
}
