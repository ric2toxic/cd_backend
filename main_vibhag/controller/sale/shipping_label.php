<?php
class ControllerSaleShipmentLabelInformation extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/shipment_label_information');

		if (!$this->user->hasPermission('modify', 'sale/shipment_label_information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}else{
			$this->getForm();
		}
	}

	public function addWarehouseAddress(){
		$this->load->model('sale/shipment_label_information');

		//if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAddressForm()) {

			$this->model_sale_shipment_label_information->addAddress($this->request->post);

			$url = '';
			if (isset($this->request->get['order_id'])) {
				$url .= '&order_id=' . $this->request->get['order_id'];
			}
			if (isset($this->request->get['filter_warehouse'])) {
				$url .= '&filter_warehouse=' . $this->request->get['filter_warehouse'];
			}

			if (isset($this->request->get['filter_city'])) {
				$url .= '&filter_city=' . $this->request->get['filter_city'];
			}

			$this->response->redirect($this->url->link('sale/shipment_label_information', 'token=' . $this->session->data['token'] . $url , 'SSL'));
		//}
		//$this->getForm();
	}

	public function getForm(){
		$this->load->language('sale/shipment_label_information');
		$this->load->model('sale/order');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('sale/shipment_label_information');

		$data['heading_title'] = $this->language->get('heading_title');
		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_form'] = $this->language->get('text_form');
		$data['text_courier'] = $this->language->get('text_courier');
		$data['text_company_address'] = $this->language->get('text_company_address');
		$data['text_address'] = $this->language->get('text_address');

		$data['entry_courier'] = $this->language->get('entry_courier');
		$data['entry_docket'] = $this->language->get('entry_docket');
		$data['entry_weight'] = $this->language->get('entry_weight');
		$data['entry_search_warehouse'] = $this->language->get('entry_search_warehouse');
		$data['entry_search_city'] = $this->language->get('entry_search_city');
		$data['entry_company_name'] = $this->language->get('entry_company_name');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_state'] = $this->language->get('entry_state');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_phone_no'] = $this->language->get('entry_phone_no');
		$data['entry_select'] = $this->language->get('entry_select');

		$data['button_search'] = $this->language->get('button_search');
		$data['button_address_save'] = $this->language->get('button_address_save');

		$data['token'] = $this->session->data['token'];

		$data['error_warning'] = '';

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		}

		if (isset($this->error['select_addresses'])) {
			$data['error_warning'] = $this->error['select_addresses'];
		}

		if (isset($this->error['courier'])) {
			$data['error_warning'] = $this->error['courier'];
		}

		if (isset($this->error['docket_no'])) {
			$data['error_warning'] = $this->error['docket_no'];
		}


		if(isset($this->request->get['order_id'])){
			$order_id = $this->request->get['order_id'];
		}else{
			$order_id = 0;
		}

		if(isset($this->request->get['filter_warehouse'])){
			$search_warehouse = $this->request->get['filter_warehouse'];
		}else{
			$search_warehouse = '';
		}

		if(isset($this->request->get['filter_city'])){
			$search_city = $this->request->get['filter_city'];
		}else{
			$search_city = '';
		}

		$url = '';
		if(isset($this->request->get['order_id'])){
			$url .= '&order_id='.$this->request->get['order_id'];
		}
		if(isset($this->request->get['filter_warehouse'])){
			$url .= '&filter_warehouse='.$this->request->get['filter_warehouse'];
		}

		if(isset($this->request->get['filter_city'])){
			$url .= '&filter_city='.$this->request->get['filter_city'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Shipment Label Information',
			'href' => $this->url->link('sale/shipment_label_information', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);


		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['cancel'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['action_save_address'] = $this->url->link('sale/shipment_label_information/addWarehouseAddress', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['shipping_label'] = $this->url->link('sale/shipment_label_information/shipmentPdf', 'token=' . $this->session->data['token'] . $url , 'SSL');

		//courier partners list
		$listOfCouriers = $this->model_sale_shipment_label_information->listOfCouriers();
		$data['couriers'] = array();

		foreach($listOfCouriers as $courier){
			$data['couriers'][] = array(
				'id'=> $courier['id'],
				'name'=> $courier['courier_name'],
			);
		}

		//get countries
		$data['countries'] = $this->model_localisation_country->getCountries();

		//get order info by order id
		$order_info = $this->model_sale_order->getOrder($order_id);

		if (isset($this->request->post['courier'])) {
			$data['data_courier'] = $this->request->post['courier'];
		} elseif (!empty($order_info)) {
			$data['data_courier'] = $order_info['courier_partner'];
		} else {
			$data['data_courier'] = '';
		}

		if (isset($this->request->post['docket_no'])) {
			$data['data_docket_no'] = $this->request->post['docket_no'];
		} elseif (!empty($order_info)) {
			$data['data_docket_no'] = $order_info['tracking_no'];
		} else {
			$data['data_docket_no'] = '';
		}

		if (isset($this->request->post['weight'])) {
			$data['data_weight'] = $this->request->post['weight'];
		} elseif (!empty($order_info)) {
			$data['data_weight'] = sprintf($this->language->get('text_kg'),floor($order_info['weight']));
		} else {
			$data['data_weight'] = '';
		}

		$filter_data = array(
			'search_warehouse' => $search_warehouse,
			'search_city' => $search_city
		);


		//courier partners list
		$company_addresses = $this->model_sale_shipment_label_information->getAddresses($filter_data);

		$data['company_addresses'] = array();
		foreach($company_addresses as $address){
			$data['company_addresses'][] = array(
				'warehouse_id'=> $address['warehouse_id'],
				'warehouse_name'=> $address['warehouse_name'],
				'address_1' => $address['address_1'],
				'address_2' => $address['address_2'],
				'city' => $address['city'],
				'postcode' => $address['postcode'],
				'state' => $address['zone_name'],
				'country' => $address['country_name'],
				'telephone' => $address['telephone']
			);
		}

		$data['order_id'] 			= $order_id;
		$data['search_warehouse'] 	= $search_warehouse;
		$data['search_city'] 		= $search_city;


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/shipment_label_information.tpl',$data));
	}

	protected function validateForm(){
		if (!$this->user->hasPermission('modify', 'sale/shipment_label_information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((isset($this->request->post['courier'])) && empty($this->request->post['courier'])) {
			$this->error['courier'] = 'Please select courier !';
		}

		if ((isset($this->request->post['docket_no'])) && empty($this->request->post['docket_no'])) {
			$this->error['docket_no'] = 'Please enter docket No. !';
		}

		if ((!isset($this->request->post['select_address']))) {
			$this->error['select_addresses'] = 'Please select warehouse address !';
		}



		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

}
