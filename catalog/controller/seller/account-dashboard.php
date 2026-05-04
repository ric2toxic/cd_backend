<?php

class ControllerSellerAccountDashboard extends Controller {

    public  $data = array();

	public function index() {

		//## [New Insert]
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/login', '', 'SSL'));
        }

  	//   if ($this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId())) {
	//     if ($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_ACTIVE) {
	//            $this->response->redirect($this->url->link('seller_panel/account-order', '', 'SSL'));
	//        } else if($this->MsLoader->MsSeller->getStatus() == MsSeller::STATUS_INACTIVE) {
	//            //## If seller status is not active redirected to profile page to complete his profile (previously redirect to seller/account-profile)
	//            $this->response->redirect($this->url->link('seller_panel/profile', '', 'SSL'));
	//        } else {
	//        	//## If seller status is disabled redirected to login page 
	//        	$this->response->redirect($this->url->link('account/login', '', 'SSL'));	
	//        }
	// 	} else {
	// 		$this->response->redirect($this->url->link('account/order', '', 'SSL'));
	// 	}

        $this->document->addStyle('catalog/view/javascript/multimerch/datatables/css/jquery.dataTables.css');
        $this->document->addScript('catalog/view/javascript/multimerch/datatables/js/jquery.dataTables.min.js');
        $this->document->addScript('catalog/view/javascript/multimerch/common.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->MsLoader->MsHelper->addStyle('multiseller');

        if (!isset($this->session->data['multiseller']['files']))
            $this->session->data['multiseller']['files'] = array();
        //##

        $seller_id = $this->customer->getId();

        //## [New Insert] [Language]
        $this->load->autoLoadLanguage('multiseller/multiseller', $this->data);
        $this->load->autoLoadLanguage('seller/option',  $this->data);
		//##

		// Total Customer of that seller with multiple store or single store by vikas
		if ($this->MsLoader->MsProduct->getSellerStores($this->customer->getId())) {
			$total_customer = count($this->MsLoader->MsOrderData->getSellerStoreCustomers());
			$order_data_all_status = $this->MsLoader->MsOrderData->getOrdersStats(
					array(
							'seller_id' => $this->customer->getId(),
							'stores' => array_merge(array_column($this->MsLoader->MsProduct->getSellerStores($this->customer->getId()), 
							                                     'store_id')),
					     )
			);
		}

		if (isset($total_customer)) {
			if ($total_customer > 1000000000000) {
				$this->data['total_customer'] = round($total_customer / 1000000000000, 1) . 'T';
			} elseif ($total_customer > 1000000000) {
				$this->data['total_customer'] = round($total_customer / 1000000000, 1) . 'B';
			} elseif ($total_customer > 1000000) {
				$this->data['total_customer'] = round($total_customer / 1000000, 1) . 'M';
			} elseif ($total_customer > 1000) {
				$this->data['total_customer'] = round($total_customer / 1000, 1) . 'K';
			} else {
				$this->data['total_customer'] = $total_customer;
			}
		}

		$this->data['view_customer'] = $this->url->link('seller/account-customer', '', 'SSL');
		
		// Total order of that seller with WSB Stores
		$order_data = $this->MsLoader->MsOrderData->getOrdersStats(
						array('seller_id' => $this->customer->getId(),
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
			$this->data['total_order'] = round($total_order / 1000000000000, 1) . 'T';
		} elseif ($total_order > 1000000000) {
			$this->data['total_order'] = round($total_order / 1000000000, 1) . 'B';
		} elseif ($total_order > 1000000) {
			$this->data['total_order'] = round($total_order / 1000000, 1) . 'M';
		} elseif ($total_order > 1000) {
			$this->data['total_order'] = round($total_order / 1000, 1) . 'K';
		} else {
			$this->data['total_order'] = $total_order;
		}

		$this->data['view_customer'] = 'javascript:void(0);';
		$this->data['view_orders'] = $this->url->link('seller_panel/account-order', '', 'SSL');

		// Total sales of that seller with multiple store or single store by vikas
		$total_sales_data = 0;
		$total_sales_data += $order_data['total_amount'];

		if (isset($order_data_all_status)) {
			$total_sales_data += $order_data_all_status['total_amount'];
		}


		if ($total_sales_data > 1000000000000) {
			$this->data['total_sales'] = round($total_sales_data / 1000000000000, 1) . 'T';
		} elseif ($total_sales_data > 1000000000) {
			$this->data['total_sales'] = round($total_sales_data / 1000000000, 1) . 'B';
		} elseif ($total_sales_data > 1000000) {
			$this->data['total_sales'] = round($total_sales_data / 1000000, 1) . 'M';
		} elseif ($total_sales_data > 1000) {
			$this->data['total_sales'] = round($total_sales_data / 1000, 1) . 'K';
		} else {
			$this->data['total_sales'] = $total_sales_data;
		}

		$this->data['view_sales'] = $this->url->link('seller_panel/account-order', '', 'SSL');


		$this->data['header_seller'] = $this->load->controller('common/seller_header');
		$this->data['footer_seller'] = $this->load->controller('common/seller_footer');
		$this->data['logging_in'] = 0;

		$this->data['orders'] = $this->MsLoader->MsOrderData->getLatestOrders($seller_id);

		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_dashboard_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller_panel/account-order', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-dashboard');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
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
