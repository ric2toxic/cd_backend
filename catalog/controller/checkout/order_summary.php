<?php
class ControllerCheckoutOrderSummary extends Controller {
	public function index() {
		$data = array();
		
		$this->load->autoLoadLanguage('checkout/cart', $data);

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
				'href' => $this->url->link('common/home'),
				'text' => $this->language->get('text_home')
		);

		$data['breadcrumbs'][] = array(
				'href' => $this->url->link('checkout/cart'),
				'text' => $this->language->get('heading_title')
		);

		if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {

			if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$data['error_warning'] = $this->language->get('error_stock');
			} elseif (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
			} else {
				$data['attention'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			$data['action'] = $this->url->link('checkout/cart/edit', '', true);

			if ($this->config->get('config_cart_weight')) {
				$data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
			} else {
				$data['weight'] = '';
			}

			$this->load->model('tool/image');
			$this->load->model('tool/upload');

			$data['products'] = array();

			$products = $this->cart->getProducts();


			$total_sets = 0;
			$total_pieces = 0;

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}

				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				} else {
					$image = '';
				}

				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
							'name'  => $option['name'],
							'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$piece_in_set = $product['piece_in_set'];

				// Display per piece prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price_per_piece = $this->currency->format($this->tax->calculate($product['price']/$piece_in_set, $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
				} else {
					$price_per_piece = false;
				}

				// Display per set prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
				} else {
					$price = false;
				}


				// Display total prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$total = $this->currency->format($product['total']);
				} else {
					$total = false;
				}

				$recurring = '';

				if ($product['recurring']) {
					$frequencies = array(
							'day'        => $this->language->get('text_day'),
							'week'       => $this->language->get('text_week'),
							'semi_month' => $this->language->get('text_semi_month'),
							'month'      => $this->language->get('text_month'),
							'year'       => $this->language->get('text_year'),
					);

					if ($product['recurring']['trial']) {
						$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
					}

					if ($product['recurring']['duration']) {
						$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					} else {
						$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax'))), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					}
				}

				$total_sets += (int)($product['quantity']);
				$total_pieces += (int)($product['piece_in_set'] * $product['quantity']);
				$data['products'][] = array(
						'key'       => $product['key'],
						'thumb'     => $image,
						'name'      => $product['name'],
						'model'     => $product['model'],
						'set_description'     => $product['set_description'],
						'option'    => $option_data,
						'recurring' => $recurring,
						'quantity'  => $product['quantity'],
						'stock_quantity'=>$product['stock_quantity'],
						'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
						'reward'    => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
						'price_per_piece' => $price_per_piece,
						'piece_in_set'=> $product['piece_in_set'] * $product['quantity'],
						'price'     => $price,
						'total'     => $total,
						'tax'       => $this->currency->format($this->tax->getTax($product['total'], $product['hsn_id'], '', '', $product['mrp'])),
						'weight'	=> $product['weight'],
						'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id']),
						'sellers'  => array($product['seller_id'] => array('price' => $product['selling_price'],'total'   => $total))
				);
				$data['sellers'][][$product['seller_id']] = array(
						'price' => $product['selling_price'],//$price,
						'total'   => $total
				);
			}
			echo "<pre>";
			print_r($data);
			die;
			$data['total_sets'] = $total_sets;
			$data['total_pieces'] = $total_pieces;

			//these 2 method call added by rakesh to display shipping in cart
			//echo "<pre>"; print_r($this->session->data['shipping_method']); exit;
			if (!isset($this->session->data['shipping_method'])) {
				$this->quote_cart();
				$this->shipping_cart();
			} // Commented by Madhur to allow shipping estimator back

			// Gift Voucher
			$data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$data['vouchers'][] = array(
							'key'         => $key,
							'description' => $voucher['description'],
							'amount'      => $this->currency->format($voucher['amount']),
							'remove'      => $this->url->link('checkout/cart', 'remove=' . $key)
					);
				}
			}

			// Totals
			$this->load->model('extension/extension');

			$total_data = array();
			$total = 0;
			$taxes = $this->cart->getTaxes();

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$sort_order = array();

				$results = $this->model_extension_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				/** Deal Of teh day code (Ravindra Singh 22-01-2016) Start**/
				if($this->config->get('deal_of_day') && !empty($this->config->get('deal_of_day'))){
					$sellers = $this->config->get('deal_of_day');//array("709");

					$seller_discount_total = '0';
					$dealsellers = array();
					$dealflag = 0;
					//echo "<pre>"; print_r($sellers); exit;
					foreach($sellers as $seller){
						if(!empty($seller['start_date'])){
							$start = explode("/",substr($seller['start_date'],0,10));
							$seller['start_date'] = $start[2].'-'.$start[0].'-'.$start[1];
						}
						if(!empty($seller['end_date'])){
							$end = explode("/",substr($seller['end_date'],0,10));
							$seller['end_date'] = $end[2].'-'.$end[0].'-'.$end[1];
						}
						$today = date('Y-m-d');
						$today=date('Y-m-d', strtotime($today));
						$start_date = date('Y-m-d', strtotime($seller['start_date']));
						$end_date = date('Y-m-d', strtotime($seller['end_date']));

						if (($today >= $start_date) && ($today <= $end_date))
						{
							$dealsellers[$seller['seller_id']][] = $seller;
						}
					}

					$seller_discount_total_all = 0;
					foreach($dealsellers as $seller_id => $sellerrs){
						$maxval = 0;
						foreach($sellerrs as $seller){
							$seller_sub_total = $this->cart->getSubTotalAccordingSeller($seller['seller_id']);
							if($seller_sub_total > $seller['order_amount']){
								if($maxval < $seller['order_amount']){
									$maxval = $seller['order_amount'];
									$dealflag = 1;
									$discount = ($seller['discount']/100)*$seller_sub_total;
									$seller_discount_total = $seller_discount_total_all + $discount;
								}
							}
						}
						$seller_discount_total_all = $seller_discount_total;
					}

					if(isset($seller_discount_total) && abs($seller_discount_total) <= 0){
						$dealflag = 0;
					}
					if($dealflag == 1){
						$total_data[] = array('code' => 'deal_discount','title' => 'Deal Discount','value' => $seller_discount_total,'sort_order' => 2);
						$total = $seller_discount_total;
					}

				}
				/** Deal Of the day code (Ravindra Singh 22-01-2016) End**/
				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {

						$this->load->model('total/' . $result['code']);
						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
					}
				}

				$sort_order = array();

				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data);
			}

			$data['zone_name'] = '';

			if ( $this->customer->isLogged() ) {
				$address_id = (int)$this->customer->getAddressId();

				$this->load->model('account/address');
				$address = $this->model_account_address->getAddress($address_id);

				if ($address and $address['zone_id']) {
					$this->load->model('localisation/zone');
					$data['zone_name'] = $this->model_localisation_zone->getZoneName( (int)($address['zone_id']) );
				}
			}

			$data['totals'] = array();
			$data['tax_in_totals'] = false;

			$cartTotal = 0.0;
			$store_credit = 0.0;

			$data['cform_submit'] = 0;
			if(isset($this->session->data['cform_submit'])&& ($this->session->data['cform_submit'] == 1)) {
				$data['cform_submit'] = 1;
				$data['cst'] = $this->currency->format( $this->session->data['cst'] );
				$data['tax_refund'] = $this->currency->format( $this->session->data['tax_refund'] );
			}

			/**Discount on Taxes with coupon code (Ravindra Singh 16-02-2016) End**/
			foreach ($total_data as $total) {

				if ($total['code'] == 'subtotal') {
					$total['title'] = $this->language->get('text_subtotal');
				} elseif ($total['code'] == 'total') {
					$total['title'] = $this->language->get('text_total_amount');
					$cartTotal = $total['value'];
				} elseif ($total['code'] == 'credit') {
					$store_credit = $total['value'];
				}

				/**Validate discount on free shipping (Ravindra Singh 05-02-2016) Start**/
				if(($total['code'] == "shipping" && $total['code'] == "0") || $total['title'] == "Free Shipping"){
					$freeshipflag = 1;
				}
				/**Validate discount on free shipping (Ravindra Singh 05-02-2016) End**/

				$data['totals'][] = array(
						'code'  => $total['code'],
						'title' => $total['title'],
						'text'  => $this->currency->format( $total['value'] )
				);
			}
			/**Validate discount on free shipping (Ravindra Singh 05-02-2016)**/
			if(isset($freeshipflag) && $freeshipflag == 1){
				foreach($data['totals'] as $k=>$ext){
					if($ext['code'] == "paycharge"){
						unset($data['totals'][$k]);
					}
				}
				$data['totals'] = array_values($data['totals']);
			}
			/**Validate discount on free shipping (Ravindra Singh 05-02-2016) End**/

			$sum_cart_credit = $cartTotal + abs($store_credit);
			// Checking for cart limit
			$data['error_cart_minimum'] = '';
			$this->session->data['error_cart_minimum'] = false;

			$store_id = $this->config->get('config_store_id') ;

			/*Remove minimum Purchase price Limit fro dropshipper (Ravindra Singh 02-02-2016) */
			if(isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1){
				$this->config->set('config_cart_limit',$this->config->get('config_dropshipper_cart_limit'));
			}


			//echo "<pre>"; print_r($this->session->data); exit;
			if ($store_id == 2) { // Singles store

				$single_store_order_limit = (float)$this->config->get('config_limit');
				if ($cartTotal < $single_store_order_limit) {
					$data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'), $this->currency->format(ceil($single_store_order_limit)) );
					$this->session->data['error_cart_minimum'] = true;
				}

			} elseif ( $sum_cart_credit < (float)($this->config->get('config_cart_limit')) ) {
				$data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'),
						$this->currency->format( (float)($this->config->get('config_cart_limit')) ));
				$this->session->data['error_cart_minimum'] = true;
			}

			$data['continue'] = $this->url->link('common/home');

			$data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
			$data['logged'] = $this->customer->isLogged();

			$this->load->model('extension/extension');

			$data['checkout_buttons'] = array();
			$data['coupon'] = $this->load->controller('checkout/coupon');
			$data['voucher'] = $this->load->controller('checkout/voucher');
			$data['reward'] = $this->load->controller('checkout/reward');

			if ($this->customer->isLogged()) {
				if($this->customer->is_dropshipper == 1){
					$data['shipping'] = '';
				}else{
					$data['shipping'] = $this->load->controller('checkout/shipping');
				}
			}else{
				$data['shipping'] = $this->load->controller('checkout/shipping');
			}

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			if(isset($this->request->get['ajax']) && $this->request->get['ajax'] == true){
				$ajax = true;
			}else{
				$ajax = false;
			}
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/order_summary.tpl')) {

				if($ajax == true) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/order_summary.tpl', $data));
				}else{
					return $this->load->view($this->config->get('config_template') . '/template/checkout/order_summary.tpl', $data);
				}

			} else {
				if($ajax == true) {
					$this->response->setOutput($this->load->view('default/template/checkout/order_summary.tpl', $data));
				}else {
					return $this->load->view('default/template/checkout/order_summary.tpl', $data);

				}


			}
		} else {

			$data['text_error'] = $this->language->get('text_empty');

			$data['continue'] = $this->url->link('common/home');

			unset($this->session->data['success']);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$data['item_in_cart'] = 0;
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/order_summary.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/order_summary.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/checkout/order_summary.tpl', $data));
			}
		}
	}

	/**
	 * Added by Rakesh to apply shipping rate (weight base method) manually in cart
	 */

	public function quote_cart() {
		$this->load->language('checkout/shipping');
		$this->load->model('localisation/country');
		$json = array();

		if(isset($_COOKIE['country']) && !empty($_COOKIE['country'])){
			$country_cod = $_COOKIE['country'];
		}else{
			$country_cod = "IN";
		}
		$country_data = $this->model_localisation_country->getCountryByCode($country_cod);

		if(isset($country_data['country_id']) && !empty($country_data['country_id'])){
			$country_id = $country_data['country_id'];
		}else{
			$country_id = '99';
		}

		$this->request->post['country_id'] = $country_id;
		$this->request->post['zone_id'] = 1501;
		$this->request->post['postcode']  = '';

		if (!$this->cart->hasProducts()) {
			$json['error']['warning'] = $this->language->get('error_product');
		}

		if (!$this->cart->hasShipping()) {
			$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		}

		if ($this->request->post['country_id'] == '') {
			$json['error']['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
			$json['error']['zone'] = $this->language->get('error_zone');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
			$json['error']['postcode'] = $this->language->get('error_postcode');
		}

		if (!$json) {

			if ($country_info) {
				$country = $country_info['name'];
				$iso_code_2 = $country_info['iso_code_2'];
				$iso_code_3 = $country_info['iso_code_3'];
				$address_format = $country_info['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

			if ($zone_info) {
				$zone = $zone_info['name'];
				$zone_code = $zone_info['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$this->session->data['shipping_address'] = array(
					'firstname'      => '',
					'lastname'       => '',
					'company'        => '',
					'address_1'      => '',
					'address_2'      => '',
					'postcode'       => $this->request->post['postcode'],
					'city'           => '',
					'zone_id'        => $this->request->post['zone_id'],
					'zone'           => $zone,
					'zone_code'      => $zone_code,
					'country_id'     => $this->request->post['country_id'],
					'country'        => $country,
					'iso_code_2'     => $iso_code_2,
					'iso_code_3'     => $iso_code_3,
					'address_format' => $address_format
			);

			$quote_data = array();

			$this->load->model('extension/extension');

			$results = $this->model_extension_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('shipping/' . $result['code']);

					$quote = $this->{'model_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {
						$quote_data[$result['code']] = array(
								'title'      => $quote['title'],
								'quote'      => $quote['quote'],
								'sort_order' => $quote['sort_order'],
								'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);

			$this->session->data['shipping_methods'] = $quote_data;

			if ($this->session->data['shipping_methods']) {
				$json['shipping_method'] = $this->session->data['shipping_methods'];
			} else {
				$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
			}
		}

		return true;
	}

	public function shipping_cart() {
		$this->load->language('checkout/shipping');

		$json = array();
		//echo "<pre>"; print_r($this->session->data['shipping_address']['country_id']); exit;
		if(isset($this->session->data['shipping_address']['country_id']) && $this->session->data['shipping_address']['country_id'] == "99"){
			//  Default shipping method for dropshipper
			if($this->customer->is_dropshipper == 1){
				$this->request->post['shipping_method'] = 'weight.weight_8';
			}else{
				$this->request->post['shipping_method'] = 'weight.weight_5';
			}
		}else{
			//  Default shipping method for dropshipper
			if($this->customer->is_dropshipper == 1){
				$this->request->post['shipping_method'] = 'weight.weight_9';
			}else{
				$this->request->post['shipping_method'] = 'weight.weight_9';
			}

		}


		if (!empty($this->request->post['shipping_method'])) {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$json['warning'] = $this->language->get('error_shipping');
			}
		} else {
			$json['warning'] = $this->language->get('error_shipping');
		}

		if (!$json) {
			$shipping = explode('.', $this->request->post['shipping_method']);

			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

			$this->session->data['success'] = $this->language->get('text_success');

			$json['redirect'] = $this->url->link('checkout/cart');
		}

		return true;
	}
}
