<?php
class ControllerCheckoutConfirm extends Controller {
	public function index() {
		$redirect = '';

		$data['button_back'] = $this->language->get('button_back');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['text_loading'] = $this->language->get('text_loading');
		if ($this->cart->hasShipping()) { 
			// Validate if shipping address has been set.
			if (!isset($this->session->data['shipping_address'])) {
				$redirect = $this->url->link('checkout/checkout', '', 'SSL');
			}

			// Validate if shipping method has been set.
			if (!isset($this->session->data['shipping_method'])) {
				$redirect = $this->url->link('checkout/checkout', '', 'SSL');
			}
		} else {
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
		}
		
		// Validate if payment address has been set.
		if (!isset($this->session->data['payment_address'])) {
			$redirect = $this->url->link('checkout/checkout', '', 'SSL');
		}
		
		// Validate if payment method has been set.
		if (!isset($this->session->data['payment_method'])) {
			$redirect = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$redirect = $this->url->link('checkout/cart', '', 'SSL');
		}

		if(isset($this->session->data['shipping_methods']['weight']['quote']['weight_5'])){
			if($this->session->data['shipping_method']['code'] == 'free.free'){
				$this->session->data['shipping_method'] = $this->session->data['shipping_methods']['weight']['quote']['weight_5'];		
			}
		}
		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$redirect = $this->url->link('checkout/cart', '', 'SSL');

				break;
			}
		}
		if (!$redirect) {
			$order_data = array();

			$order_data['totals'] = array();
			$total = 0;
			$taxes = $this->cart->getTaxes();
            
            // CST Calculation
            $CST_CLASS_ID = 12; 
            $cst = $this->cart->getCST($CST_CLASS_ID); // CST tax class id is 12

			$this->load->model('extension/extension');

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
					$order_data['totals'][] = array('code' => 'deal_discount','title' => 'Deal Discount','value' => ceil($seller_discount_total),'sort_order' => 5);
					$total = ceil($seller_discount_total);
				}

			}
			//echo "<pre>"; print_r($total_data); exit;
			/** Deal Of the day code (Ravindra Singh 22-01-2016) End**/


				//echo "<pre>"; print_r($total); 
			foreach ($results as $result) {

				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes, $cst, $CST_CLASS_ID);

				}
			}
			//echo "<pre>"; print_r($order_data['totals']); exit;
			$sort_order = array();
			foreach ($order_data['totals'] as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
				//$this->config->get('config_language_id')
				//echo "<pre>"; print_r($this->session->data['payment_method']); exit;
				if(($this->session->data['discount_type'] == "free_shipping") && ($this->session->data['payment_method']['code'] != 'cod') && ($this->session->data['payment_method']['code'] == 'bank_transfer' || $this->session->data['payment_method']['code'] == 'citrus')){
					if($this->config->get('free_total') && ($value['code'] == 'sub_total' && $value['value'] > $this->config->get('free_total'))){
						/**
						 * Add Shipping Free Above 10k orders(Only on prepaid) START
						 * Ravindra Singh(12-01-2015)
						 * */
						
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
						$this->session->data['shipping_method'] = $quote_data['free']['quote']['free'];
						/*if ($this->config->get('shipping_status')) {
							$this->load->model('total/shipping');

							$this->{'model_total_shipping'}->getTotal($order_data['totals'], $total, $taxes); 
						}*/
						$order_data['totals'] = array();
						$total = 0;
						$taxes = $this->cart->getTaxes();
                        
                        // CST Calculation
                        $CST_CLASS_ID = 12; 
                        $cst = $this->cart->getCST($CST_CLASS_ID); // CST tax class id is 12
            
						$sort_order = array();

						$results = $this->model_extension_extension->getExtensions('total');
						
						foreach ($results as $key => $value) {
							$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
						}

						array_multisort($sort_order, SORT_ASC, $results);
						/** Deal Of teh day code (Ravindra Singh 22-01-2016) Start**/
						if($this->config->get('deal_of_day') && !empty($this->config->get('deal_of_day'))){
							$sellers = $this->config->get('deal_of_day');//array("709");
							$seller_previous_amount_data = array();
							$discount = array();

							$dealflag = 0;
							foreach($sellers as $seller){
								//echo "<pre>"; print_r($seller); exit;
								if(!empty($seller['start_date'])){
									$start = explode("/",substr($seller['start_date'],0,10));
									$seller['start_date'] = $start[2].'-'.$start[0].'-'.$start[1];
								}
								if(!empty($seller['end_date'])){
									$end = explode("/",substr($seller['end_date'],0,10));
									$seller['end_date'] = $end[2].'-'.$end[0].'-'.$end[1];
								}
								//echo "<pre>"; print_r($seller); exit;
								$today = date('Y-m-d'); 
								$today=date('Y-m-d', strtotime($today));
								//echo $seller['start_date']; exit;
								$start_date = date('Y-m-d', strtotime($seller['start_date']));
								$end_date = date('Y-m-d', strtotime($seller['end_date']));

								if (($today >= $start_date) && ($today <= $end_date))
								{ 	//$seller['seller_id'] = 709;
									$seller_sub_total = $this->cart->getSubTotalAccordingSeller($seller['seller_id']);
									/*if($seller_sub_total > $seller['order_amount']){
										$dealflag = 1;
										$discount = ($seller['discount']/100)*$seller_sub_total;
										$seller_discount_total = $seller_discount_total + $discount;
									}*/
									//echo "<pre>"; print_r($seller_sub_total); die;
									if($seller_sub_total > $seller['order_amount']){
										if(isset($seller_previous_amount_data[$seller['seller_id']])){
											if($seller_previous_amount_data[$seller['seller_id']] < $seller['order_amount']){
												$seller_previous_amount_data[$seller['seller_id']] = $seller['order_amount'];
												$discount[$seller['seller_id']] = ($seller['discount']/100)*$seller_sub_total;
												$dealflag = 1;
											}
										}else{
											$seller_previous_amount_data[$seller['seller_id']] = $seller['order_amount'];
											$discount[$seller['seller_id']] = ($seller['discount']/100)*$seller_sub_total;
											$dealflag = 1;
										}
									}
								}
							}
							
							/*if($dealflag == 1){
								$order_data['totals'][] = array('code' => 'deal_discount','title' => 'Deal Discount','value' => $seller_discount_total,'sort_order' => 5);
								$total = $seller_discount_total;
							}*/
						}
						if($dealflag == 1){
							$total_dis = 0;
							$seller_discount_total = 0;
							foreach($discount as $dis){
								$total_dis+= $dis;
							}
							$seller_discount_total = $total_dis;
							$order_data['totals'][] = array('code' => 'deal_discount','title' => 'Deal Discount','value' => ceil($seller_discount_total),'sort_order' => 5);
						}
						/** Deal Of teh day code (Ravindra Singh 22-01-2016) End**/
						foreach ($results as $result) {
							if($result['code'] == 'paycharge' && $this->session->data['shipping_method']['code'] == 'free.free'){
									//echo "<pre>"; print_r($this->session->data['payment_method']); exit;
							}else{
								if ($this->config->get($result['code'] . '_status')) {
									$this->load->model('total/' . $result['code']);

									$this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes, $cst, $CST_CLASS_ID);

								}
							}
						}
						foreach ($order_data['totals'] as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}
						break;
						/**
						 * Add Shipping Free Above 10k orders(Only on prepaid) END
						 * Ravindra Singh(12-01-2015)
						 * */
					}
				}/*else{ 
					echo "<pre>"; print_r($this->session->data['shipping_method']);
					if(isset($this->session->data['shipping_methods']['weight']['quote']['weight_5'])){
						if($this->session->data['shipping_method']['code'] == 'free.free'){
							$this->session->data['shipping_method'] = $this->session->data['shipping_methods']['weight']['quote']['weight_5'];		
						}
					}
				}*/
			}

			array_multisort($sort_order, SORT_ASC, $order_data['totals']);

			$this->load->language('checkout/checkout');

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				$order_data['store_url'] = HTTP_SERVER;
			}
			if ($this->customer->isLogged()) {
				$this->load->model('account/customer');

				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

				$order_data['customer_id'] = $this->customer->getId();
				$order_data['firstname'] = $this->clean_string($customer_info['firstname']);
				$order_data['lastname'] = $this->clean_string($customer_info['lastname']);
				$order_data['email'] = $customer_info['email'];
				$order_data['telephone'] = $customer_info['telephone'];
			} elseif (isset($this->session->data['guest'])) {
				$order_data['customer_id'] = 0;
				$order_data['firstname'] = $this->clean_string($this->session->data['guest']['firstname']);
				$order_data['lastname'] = $this->clean_string($this->session->data['guest']['lastname']);
				$order_data['email'] = $this->session->data['guest']['email'];
				$order_data['telephone'] = $this->session->data['guest']['telephone'];
			}

			$order_data['payment_firstname'] = $this->clean_string($this->session->data['payment_address']['firstname']);
			$order_data['payment_lastname'] = $this->clean_string($this->session->data['payment_address']['lastname']);
			$order_data['payment_company'] = $this->session->data['payment_address']['company'];
			$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
			$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
			$order_data['payment_city'] = $this->session->data['payment_address']['city'];
			$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
			$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
			$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
			$order_data['payment_country'] = $this->session->data['payment_address']['country'];
			$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
			$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
			
			if (isset($this->session->data['payment_method']['title'])) {
				$order_data['payment_method'] = $this->session->data['payment_method']['title'];
			} else {
				$order_data['payment_method'] = '';
			}

			if (isset($this->session->data['payment_method']['code'])) {
				$order_data['payment_code'] = $this->session->data['payment_method']['code'];
			} else {
				$order_data['payment_code'] = '';
			}
			
			
			
			if ($this->cart->hasShipping()) {
				$order_data['shipping_firstname'] = $this->clean_string($this->session->data['shipping_address']['firstname']);
				$order_data['shipping_lastname'] = $this->clean_string($this->session->data['shipping_address']['lastname']);
				$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
				$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
				$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
				$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
				$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
				$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
				$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
				$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
				$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
				$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
				
				
				if (isset($this->session->data['shipping_method']['title'])) {
					$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
				} else {
					$order_data['shipping_method'] = '';
				}

				if (isset($this->session->data['shipping_method']['code'])) {
					$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
				} else {
					$order_data['shipping_code'] = '';
				}
			} else {
				$order_data['shipping_firstname'] = '';
				$order_data['shipping_lastname'] = '';
				$order_data['shipping_company'] = '';
				$order_data['shipping_address_1'] = '';
				$order_data['shipping_address_2'] = '';
				$order_data['shipping_city'] = '';
				$order_data['shipping_postcode'] = '';
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
				$order_data['shipping_address_format'] = '';
				$order_data['shipping_method'] = '';
				$order_data['shipping_code'] = '';
			}

            $total_pieces_order = 0;
            $total_sets = 0;

			$order_data['products'] = array();

			$order_data['total_weight'] = 0;
			$order_data['weight_class_id'] = $this->config->get('config_weight_class_id');

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					);
				}
                
                $total_pieces_order += (int)($product['total_pieces']);
                $total_sets += (int)($product['quantity']);

				if ($product['shipping']) {
					$order_data['total_weight'] += $this->weight->convert($product['weight'], 
					                                                      $product['weight_class_id'], 
					                                                      $this->config->get('config_weight_class_id'));
				}

				$order_data['products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
                    'sku'        => $product['sku'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
                    'piece_in_set'  => $product['piece_in_set'],
                    'total_pieces'  => $product['total_pieces'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
                    'price_per_piece' => $product['price_per_piece'],
					'total'      => $product['total'],
					'tax'        => $this->tax->getTax($product['total'], $product['hsn_code'], '', '', $product['mrp']),
                    'comment'    => $product['set_description'], 
                    'seller_tax' => $product['seller_tax'], 
                    'commission' => $product['commission'], 
                    'tax_rates'  => $this->model_catalog_product->getTaxRates($product['product_id'])
				);
			}


			// Gift Voucher
			$order_data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$order_data['vouchers'][] = array(
						'description'      => $voucher['description'],
						'code'             => substr(md5(mt_rand()), 0, 10),
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $voucher['amount']
					);
				}
			}

			$order_data['comment'] = $this->session->data['comment'];
			$order_data['total'] = $total;

			if (isset($this->request->cookie['tracking'])) {
				$order_data['tracking'] = $this->request->cookie['tracking'];

				$subtotal = $this->cart->getSubTotal();

				// Affiliate
				$this->load->model('affiliate/affiliate');

				$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);

				if ($affiliate_info) {
					$order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
					$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
				}

				// Marketing
				$this->load->model('checkout/marketing');

				$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

				if ($marketing_info) {
					$order_data['marketing_id'] = $marketing_info['marketing_id'];
				} else {
					$order_data['marketing_id'] = 0;
				}
			} else {
				$order_data['affiliate_id'] = 0;
				$order_data['commission'] = 0;
				$order_data['marketing_id'] = 0;
				$order_data['tracking'] = '';
			}

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId();
			$order_data['currency_code'] = $this->currency->getCode();
			$order_data['currency_conversion_rate'] = $this->currency->getCurrencyConversionRate();
			$order_data['currency_value'] = $this->currency->getValue($this->currency->getCode());
			$order_data['currency_live_conversion_rate'] = $this->currency->getLiveConversionRate($this->currency->getCode());

			$order_data['ip'] = $this->request->getIpAddress; //$this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}

			$data['totals'] = array();
            $data['cform_submit'] = 0;
			$data['text_cst'] = $this->language->get('text_cst');
            $data['text_tax_refund'] = $this->language->get('text_tax_refund');

			$order_data['cform_submit'] = 'no_submit';
			$order_data['cst_with_cform'] = 0.0;
			$order_data['refundable_cform'] = 0.0;
			$order_data['refund_status'] = 'not_applicable';

			foreach ($order_data['totals'] as $total) {
                
                 if ($total['code'] == 'subtotal') {
                    $total['title'] = $this->language->get('text_subtotal');
                } elseif ($total['code'] == 'total') {
                    $total['title'] = $this->language->get('text_total_amount');
                }

				$data['totals'][] = array(
					'code'  => $total['code'],
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value']),
				);


				if(isset($this->session->data['cform_submit'])&& ($this->session->data['cform_submit'] == 1)) {
					if((isset($this->session->data['payment_address']) && ($this->session->data['payment_address']['zone_id']!='1501'))){
                        if ($total['code'] == 'tax') {
                            $data['cst'] = $this->currency->format($cst);
                            $data['tax_refund'] = $this->currency->format($total['value'] - $cst);  // Refund will be total tax - cst value

							$order_data['cform_submit'] = 'will_submit';
							$order_data['cst_with_cform'] = $cst;
							$order_data['refundable_cform'] = $total['value'] - $cst;
							$order_data['refund_status'] = 'not_refunded';

							$data['cform_submit'] = 1;
						}
					}
				}
			}

			$this->load->model('checkout/order');
			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

			$data['text_recurring_item'] = $this->language->get('text_recurring_item');
			$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
            $data['text_product_code'] = $this->language->get('text_product_code');

			$data['column_image'] = $this->language->get('column_image');
			$data['column_name'] = $this->language->get('column_name');
			$data['column_model'] = $this->language->get('column_model');
			$data['column_quantity'] = $this->language->get('column_quantity');
			$data['piece_per_set'] = $this->language->get('piece_per_set');
			$data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');
			$data['column_price'] = $this->language->get('column_price');
			$data['column_total'] = $this->language->get('column_total');
            $data['column_tax'] = $this->language->get('column_tax');
			$data['column_inc_tax'] = $this->language->get('column_inc_tax');
			$data['column_per_piece'] = $this->language->get('column_per_piece');
			$data['column_per_set'] = $this->language->get('column_per_set');
			$data['pieces'] = $this->language->get('pieces');
            $data['text_total_qty'] = $this->language->get('text_total_qty');
            
            $data['total_pieces'] = $total_pieces_order;
            $data['total_sets'] = $total_sets;


			$data['text_number_of_pieces']	= $this->language->get('text_number_of_pieces');
			$data['text_price_per_piece']	= $this->language->get('text_price_per_piece');
			$data['text_price_per_set'] 	= $this->language->get('text_price_per_set');
			$data['text_total'] 			= $this->language->get('text_total');
			$data['text_tax'] 				= $this->language->get('text_tax');
			$data['text_set_description'] 	= $this->language->get('text_set_description');


			$this->load->model('tool/image');
			$this->load->model('tool/upload');
			
			$data['products'] = array();

			foreach ($this->cart->getProducts() as $product) {
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

				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				} else {
					$image = '';
				}

				$data['products'][] = array(
					'key'             => $product['key'],
					'product_id'      => $product['product_id'],
					'thumb'           => $image,
					'name'            => $product['name'],
					'model'           => $product['model'],
                    'set_description' => $product['set_description'],
					'option'    	  => $option_data,
					'recurring' 	  => $recurring,
					'quantity'  	  => $product['quantity'],
					'subtract'   	  => $product['subtract'],
					'price_per_piece' => $price_per_piece,
					'piece_in_set'	=> $product['piece_in_set'] * $product['quantity'],
					'price'      	=> $this->currency->format($product['price']),
					'total'      	=> $this->currency->format($product['price'] * $product['quantity']),
                    'tax'       	=> $this->currency->format($this->tax->getTax($product['price'] * $product['quantity'], $product['hsn_code'], '', '', $product['mrp'])),
					'href'       	=> $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL'),

				);
			}


			// Gift Voucher
			$data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'])
					);
				}
			}

			$data['payment'] = $this->load->controller('payment/' . $this->session->data['payment_method']['code']);

		} else {
			$data['redirect'] = $redirect;
		}



		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/confirm.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/confirm.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/checkout/confirm.tpl', $data));
		}
	}
	
	function clean_string($string) {
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

	   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}
}
