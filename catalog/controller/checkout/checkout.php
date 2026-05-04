<?php
class ControllerCheckoutCheckout extends Controller {
	public function index() {
		//echo "<pre>"; print_r($this->session->data); exit;
		//$_SESSION['URLSESSION'] = $_GET;
		// Validate cart has products and has stock. 
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) 
            || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))
            || (isset($this->session->data['error_cart_minimum']) && $this->session->data['error_cart_minimum']) ) {
			$this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}

		$this->load->model('catalog/product');
		$this->load->language('checkout/checkout');

		$data['default_address_delete_error'] = '';
		if(isset($this->request->get['error']) && !empty($this->request->get['error'])) {
			$data['default_address_delete_error'] = $this->request->get['error'];
		}

		//for mobile theme
		$data['steps_one'] = $this->language->get('steps_one');
		$data['steps_two'] = $this->language->get('steps_two');
		$data['steps_three'] = $this->language->get('steps_three');
		$data['button_back'] = $this->language->get('button_back');

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		$has_single = false;

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}

			}

			//echo '<pre>'; print_r($product); echo '</pre>';
			if($has_single == false and (int)$this->config->get('config_store_id') == 2) {
				$has_single = $this->model_catalog_product->checkIfProductIsSingle($product['product_id']);
			}
			if ($product['minimum'] > $product_total) {
				$this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
			}
		}
		//Validate minimum amount for order.
        //if(isset($this->config->get('config_limit')) && !empty($this->config->get('config_limit'))){
            $limitforSingleItemMinimumAmount = $this->config->get('config_limit');
        /*}else{
            $limitforSingleItemMinimumAmount = 5000;
        }*/
		if($has_single == true && $this->cart->getSubTotal() < $limitforSingleItemMinimumAmount){
			$this->session->data['error']= sprintf($this->language->get('error_minimum_order_amount_validation'), $this->currency->format($limitforSingleItemMinimumAmount));
			$this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}




		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		// Required by klarna
		if ($this->config->get('klarna_account') || $this->config->get('klarna_invoice')) {
			$this->document->addScript('http://cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js');
		}

		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/checkout', '', 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_checkout_option'] = $this->language->get('text_checkout_option');
		$data['text_checkout_account'] = $this->language->get('text_checkout_account');
		$data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
		$data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');
		$data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
		$data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');
		$data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');
		$data['order_summary'] = $this->language->get('order_summary');
		$data['price_details'] = $this->language->get('price_details');
		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		$data['logged'] = $this->customer->isLogged();

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = '';
		}

		$data['shipping_required'] = $this->cart->hasShipping();

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/new_checkout.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/new_checkout.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/checkout/checkout.tpl', $data));
		}
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	// delete address by vikas (27-02-2016)
	/*public function delete_new_address(){
		$this->load->model('account/address');
		$new_address_id = $this->request->post['address_value'];
		$this->model_account_address->deleteAddress($new_address_id);
		echo "success";
		die;
	}*/
}
