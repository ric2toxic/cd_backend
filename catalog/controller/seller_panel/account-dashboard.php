<?php

class ControllerSellerPanelAccountDashboard extends Controller {

	public function index() {
		$this->dashboard();
	}

	public function dashboard(){
		$this->load->model('seller_panel/dashboard');
		$this->load->model('seller_panel/profile');
		$data = array();
		$this->load->autoLoadLanguage('seller-panel/account-dashboard', $data);

		$seller_id = $this->customer->getId();

		$result = $this->model_seller_panel_profile->checkSellerProfileIsComplete($seller_id);
		echo "<pre>";
		print_r($result);
		exit;

		$getSellerStores = array_column($this->model_seller_panel_dashboard->getSellerStores($seller_id), 'store_id');

		$total_customer = count($this->model_seller_panel_dashboard->getSellerStoreCustomers());

		// echo "<pre>"; print_r($getSellerStores); die;



		// Total Customer of that seller with multiple store or single store by vikas
		/*if ($this->MsLoader->MsProduct->getSellerStores($seller_id)) {
			$total_customer = count($this->MsLoader->MsOrderData->getSellerStoreCustomers(
			                        array_column($this->MsLoader->MsProduct->getSellerStores($seller_id), 'store_id')));
			$order_data_all_status = $this->MsLoader->MsOrderData->getOrdersStats(
					array(
							'seller_id' => $seller_id,
							'stores' => array_merge(array_column($this->MsLoader->MsProduct->getSellerStores($seller_id),
							                                     'store_id')),
					     )
			);
		}*/

		if (isset($total_customer)) {
			if ($total_customer > 1000000000000) {
				$data['total_customer'] = round($total_customer / 1000000000000, 1) . 'T';
			} elseif ($total_customer > 1000000000) {
				$data['total_customer'] = round($total_customer / 1000000000, 1) . 'B';
			} elseif ($total_customer > 1000000) {
				$data['total_customer'] = round($total_customer / 1000000, 1) . 'M';
			} elseif ($total_customer > 1000) {
				$data['total_customer'] = round($total_customer / 1000, 1) . 'K';
			} else {
				$data['total_customer'] = $total_customer;
			}
		}

		//$data['view_customer'] = $this->url->link('seller-panel/account-customer');

		// Total order of that seller with WSB Stores
		// $order_data = $this->MsLoader->MsOrderData->getOrdersStats(
		// 				array('seller_id' => $this->customer->getId(),
		// 					  'stores' => explode(",", WSB_STORES_ID),
		// 					  'order_status' => array(4, 5, 6, 7, 8, 9, 11, 12, 13, 14, 15, 16)
		// 					 )
		// 			  );

		$order_data_all_status = $this->model_seller_panel_dashboard->getOrdersStats(
										array(
												'seller_id' => $seller_id,
												'stores' => array_merge($getSellerStores),
										     ) );

		$order_data = $this->model_seller_panel_dashboard->getOrdersStats(
						array('seller_id' => $seller_id,
							  'stores' => explode(",", WSB_STORES_ID),
							  'order_status' => array(4, 5, 6, 7, 8, 9, 11, 12, 13, 14, 15, 16)
							 )
					  );

		//show all order_status
		$total_order = 0;
		$total_order += $order_data['total_order'];
		if (isset($order_data_all_status)) {
			$total_order += $order_data_all_status['total_order'];
		}


		if ($total_order > 1000000000000) {
			$data['total_order'] = round($total_order / 1000000000000, 1) . 'T';
		} elseif ($total_order > 1000000000) {
			$data['total_order'] = round($total_order / 1000000000, 1) . 'B';
		} elseif ($total_order > 1000000) {
			$data['total_order'] = round($total_order / 1000000, 1) . 'M';
		} elseif ($total_order > 1000) {
			$data['total_order'] = round($total_order / 1000, 1) . 'K';
		} else {
			$data['total_order'] = $total_order;
		}

		$data['view_customer'] = 'javascript:void(0);'; //$this->url->link('seller_panel/account-order/showCustomers');
		$data['view_orders'] = $this->url->link('seller_panel/account-order');

		// Total sales of that seller with multiple store or single store by vikas
		$total_sales_data = 0;
		$total_sales_data += $order_data['total_amount'];

		if (isset($order_data_all_status)) {
			$total_sales_data += $order_data_all_status['total_amount'];
		}


		if ($total_sales_data > 1000000000000) {
			$data['total_sales'] = round($total_sales_data / 1000000000000, 1) . 'T';
		} elseif ($total_sales_data > 1000000000) {
			$data['total_sales'] = round($total_sales_data / 1000000000, 1) . 'B';
		} elseif ($total_sales_data > 1000000) {
			$data['total_sales'] = round($total_sales_data / 1000000, 1) . 'M';
		} elseif ($total_sales_data > 1000) {
			$data['total_sales'] = round($total_sales_data / 1000, 1) . 'K';
		} else {
			$data['total_sales'] = $total_sales_data;
		}

		$data['view_sales'] = $this->url->link('seller-panel/account-order');

		$data['header_seller'] = $this->load->controller('seller-panel/seller_header');
		$data['footer_seller'] = $this->load->controller('seller-panel/seller_footer');
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/seller-panel/account-dashboard.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/seller-panel/account-dashboard.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/seller-panel/account-dashboard.tpl', $data));
		}
	}

	/**
	 * Seller Agreement Acceptance
	 * */

	public function sellerAgreementSubmission(){
		$this->load->model('seller/seller_activity');
		$this->model_seller_seller_activity->sellerAgreementSubmission($this->request->post['seller_agreement'], $this->customer->getId());
	}

}
?>
