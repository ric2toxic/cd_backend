<?php
class ControllerCheckoutCart extends Controller {
	public function index() {

		unset($this->session->data['order_id']);
		unset($this->session->data['success_page_already_shown']);
		unset($this->session->data['shipping_method']);
		unset($this->session->data['payment_method']);
		$this->load->model('tool/image');

		$data = array();
		$header_language = array();
		$footer_language = array();
		$login_language = array();


		$this->load->autoLoadLanguage('common/header', $header_language);
		$this->load->autoLoadLanguage('common/footer', $footer_language);
		$this->load->autoLoadLanguage('account/login', $login_language);
		$this->load->autoLoadLanguage('checkout/cart', $data);

		$language = $data;
		$data['header_language'] = json_encode($header_language);
        $data['footer_language'] = json_encode($footer_language);
        $data['login_language']  = json_encode($login_language);
        $data['surface_shipping'] = 0;

         $store_id = (int)($this->config->get('config_store_id'));
		 $data['international_store'] = 0;
         if($store_id == INTERNATIONAL_STORE_ID)
           $data['international_store'] = 1;


        $this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

        if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['request_uri'] = $_SERVER['REQUEST_URI'];
		if ($this->request->server['HTTPS']) {
			$data['in_store'] = 'https://'.INDIA_STORE_HOST;
			$data['co_store'] = 'https://'.INTERNATIONAL_STORE_HOST;
		} else {
			$data['in_store'] = 'http://'.INDIA_STORE_HOST;
			$data['co_store'] = 'http://'.INTERNATIONAL_STORE_HOST;
		}

		$data['title'] = $this->document->getTitle();
        $data['social_meta_tags'] = $this->document->getSocialMetaTags();
        $data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();

		$data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
				'href' => $this->url->link('common/home', '', 'SSL'),
				'text' => $this->language->get('text_home')
		);

		$data['breadcrumbs'][] = array(
				'href' => $this->url->link('checkout/cart', '', 'SSL'),
				'text' => $this->language->get('heading_title')
		);


		if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {

		    $data['empty'] = "0";

			if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$data['error_warning'] = $this->language->get('error_stock');

				//$data['cart_error_show'] = 1;
			} elseif (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];
				$data['cart_error_show'] = 1;

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
				$data['cart_error_show'] = 1;
			} else {
				$data['attention'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			$data['action'] = $this->url->link('checkout/cart/edit', '', 'SSL');

			if ($this->config->get('config_cart_weight')) {
				$data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
			} else {
				$data['weight'] = '';
			}

			$this->load->model('tool/image');
			$this->load->model('tool/upload');

			$data['products'] = array();

			$products = $this->cart->getProducts('',true);

			/*Remove minimum Purchase price Limit for dropshipper (Ravindra Singh 02-02-2016) */
			if(isset($this->customer->is_dropshipper) && $this->customer->is_dropshipper == 1){
				$this->config->set('config_customer_cart_limit',$this->config->get('config_cart_limit'));
				$this->config->set('config_cart_limit',$this->config->get('config_dropshipper_cart_limit'));
			}
            $data['coupon_code'] = '';
            /// If store voucher applied then setting cart limit to 0
            $coupon_data = $this->cart->getCoupon();
            if( !empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code']) ){
                $this->config->set('config_customer_cart_limit',0);
                $this->config->set('config_cart_limit',0);
                $data['coupon_code'] = $coupon_data['wsb_store_voucher'];
            }
            else if( !empty($coupon_data['coupon_store_trial']) ){ // store trial coupon
                $this->config->set('config_customer_cart_limit',0);
                $this->config->set('config_cart_limit',0);
                $data['coupon_code'] = $coupon_data['coupon_store_trial'];
            }
            else if( !empty($coupon_data['coupon_franchise']) || !empty($coupon_data['coupon_franchise_cash']) || !empty($coupon_data['coupon_franchise_credit']) ){ // for order by franchise
                $this->config->set('config_customer_cart_limit',0);
                $this->config->set('config_cart_limit',0);
                $data['coupon_code'] = array_values($coupon_data)[0];
            }
            else if (!empty($coupon_data['coupon_remove_cart_limit'])) {
                $this->config->set('config_customer_cart_limit',0);
                $this->config->set('config_cart_limit',0);
                $data['coupon_code'] = $coupon_data['coupon_remove_cart_limit'];
            }
            else if(!empty($coupon_data)){
                $data['coupon_code'] = array_values($coupon_data)[0];
            }

            if ( empty( $coupon_data )) {
            	
				// auto apply referral coupon
				$this->load->model('checkout/coupon');
				$applied_coupon_code = $this->model_checkout_coupon->autoApplyReferralCoupon( (int) $this->customer->getId() );
				$data['coupon_code'] = $applied_coupon_code;
            }

			$this->load->model('catalog/product');

			$total_sets = 0;
			$total_pieces = 0;

			$total_out_of_stock_products = 0;
			$total_quantity_reduced_products = 0;
            $total_moq_error_products = 0;

            // set defaults
            $data['error_cart_minimum'] = '';
            $this->session->data['error_cart_minimum'] = false;
            $data['disable_place_order'] = 0;
            $data['text_cart_minimum'] = '';

			$data['clear_cart'] = array();

            $product_moq_error = array();

			$products_array = array();
			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id'] && $product_2['stock']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
					$data['cart_error_show'] = 1;
				}
				if($product['status'] == 0){
					$data['error_warning'] = $this->language->get('error_stock');
					//$data['cart_error_show'] = 1;
				}
                if($product['wrong_store']){
                    $data['error_warning'] = $this->language->get('error_wrong_store');
					//$data['cart_error_show'] = 1;
                }

				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
					$img_width = $this->config->get('config_image_cart_width');
					$img_height = $this->config->get('config_image_cart_height');
				} else {
					$image = '';
					$img_width = '';
					$img_height = '';
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

					if ($option['image']) {
						$image = $this->model_tool_image->resize($option['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
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

                $moq_error = false;

                if($product['stock'] == true && $product['minimum'] > $product_total ){
                    $moq_error = true;
                    $total_moq_error_products += 1;
                    if (!isset($product_moq_error[$product['product_id']])) {
                        $data['text_cart_minimum'] = $data['text_cart_minimum'] . sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']) . '<br/><br/>';
                        $data['cart_error_show'] = 1;
                        $data['disable_place_order'] = 1;
                        $this->session->data['error_cart_minimum'] = true;
                        $product_moq_error[$product['product_id']] = true;
                    }
                }

                $quantity_reduced = false;
                if(
									$product['donot_have_enough_qty'] == true &&
									$product['stock_quantity'] &&  // quantity should be greater than zero
									$product['product_id'] == $product['combo_product_id'] // should not be combo product
							   ){
                    $product['tax'] = $product['tax']*$product['stock_quantity']/$product['quantity'];
                    $product['quantity'] = $product['stock_quantity'];
                    $product['total'] = $product['price'] * $product['quantity'];
                    $this->cart->update($product['key'], $product['stock_quantity']);
                    $total_quantity_reduced_products += 1;
										$product['stock']= true;
                    $quantity_reduced = true;
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


                if ($this->customer->isLogged())
                    $previously_ordered = $this->model_catalog_product->checkPreviouslyOrdered($this->customer->getId(), $product['product_id']);
                else
                    $previously_ordered = false;

                if($product['stock'] == false)
                    $total_out_of_stock_products += 1;
                else{
                    $total_sets += (int)($product['quantity']);
                    $total_pieces += (int)($product['piece_in_set'] * $product['quantity']);
                }

                $pickup_city = preg_replace('/[0-9]+/', '', $product['pickup_city']);
                $pickup_city = explode(",", $pickup_city);
                $index_count = count($pickup_city)-1;
                if(isset($pickup_city[$index_count]))
                {
                  $product['pickup_city'] =  trim($pickup_city[$index_count]);
                }
                else
                {
                  $product['pickup_city'] =  trim($pickup_city[$index_count-1]);
                }

				$temp_data = array(
						'key'       => $product['key'],
						'product_id'=> $product['product_id'],
						'rating'    => $product['rating'],
						'thumb'     => $image,
						'name'      => $product['name'],
						'is_sor_enabled'          => $product['is_sor_enabled'],
						'sor_enabled_text'        => $product['sor_enabled_text'],
						'sor_enabled_detail_text' => $product['sor_enabled_detail_text'],
						'model'     => $product['model'],
						'status'     => $product['status'],
						'set_description'     => $product['set_description'],
						'option'    => $option_data,
						'recurring' => $recurring,
						'quantity'  => $product['quantity'],
                        'minimum'  => $product['minimum'],
						'stock_quantity'=>$product['stock_quantity'],
						'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
						'price_per_piece' => $price_per_piece,
						'piece_in_set'=> $product['piece_in_set'] * $product['quantity'],
						'price'     => $price,
						'total'     => $total,
						'tax'       => $this->currency->format($product['tax']),
						'weight'	=> $product['weight'],
						'href'      => $this->url->link('product/product', 'product_id=' . $product['combo_product_id']),
						'sellers'  => array($product['seller_id'] => array('price' => $product['selling_price'],'total' => $total)),
						'previously_ordered' => $previously_ordered,
						'is_single' => $product['is_single'],
                        'wrong_store' => $product['wrong_store'],
                        'store_pickup' => $product['store_pickup'],
						'cod_available' => $product['cod_available'],
						'img_width' => $img_width,
						'img_height' => $img_height,
                        'comment'  => $product['comment'],
                        'non_returnable' => $product['non_returnable'],
                        'exp_dispatch_date' => $product['exp_dispatch_date'],
                        'quantity_reduced' => $quantity_reduced,
                        'moq_error' => $moq_error,
                        'wishlist' => $this->customer->checkExistingWishlistItem($product['combo_product_id']),
                        'error_store_limit' => $product['error_store_limit'],
                        'error_store_limit_msg' => $product['error_store_limit_msg'],
                        'base_unit' => $product['base_unit'],
                        'super_unit' => $product['super_unit'],
                        'tax_rate' => 'GST('.$product['output_tax_rates'].'%)',
                        'pickup_city' => $product['seller_city'],
												'combo_product_id' => $product['combo_product_id']
				);

				$data['products'][] = $temp_data;
				$products_array[$product['pickup_city']][$product['key']][] = $temp_data;

				$data['sellers'][][$product['seller_id']] = array(
						'price' => $product['selling_price'],
						'total'   => $total
				);
                $data['clear_cart'][$product['pickup_city']][] = $product['key'];
			}
			$data['total_sets'] = $total_sets;
			$data['total_pieces'] = $total_pieces;
            $data['clear_cart'] = json_encode($data['clear_cart']);

            $data['total_out_of_stock_products'] = $total_out_of_stock_products;
            $data['total_quantity_reduced_products'] = $total_quantity_reduced_products;
            $data['total_moq_error_products'] = $total_moq_error_products;

            $data["product_json"] = json_encode($products_array);
			// Gift Voucher
			$data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$data['vouchers'][] = array(
							'key'         => $key,
							'description' => $voucher['description'],
							'amount'      => $this->currency->format($voucher['amount']),
							'remove'      => $this->url->link('checkout/cart', 'remove=' . $key, 'SSL')
					);
				}
			}

            // Totals

			$total_data = array();
			$total = 0;

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				// Shift total code in library By Sudhanshu
				$total_factory = new TotalFactory( $this, true );
				$total_data = $total_factory->getTotal(true);
				$total = $total_factory->total;
				$taxes = $total_factory->taxes;
				$cst = $total_factory->cst;

			}

			$data['totals'] = array();
			$tax_in_totals = false;

            $totals_data = array();

			$cartTotal = 0.0;
            $cartSubTotal = 0.0;
            $store_credit = 0.0;

            /**Discount on Taxes with coupon code (Ravindra Singh 16-02-2016) End**/
			foreach ($total_data as $total) {

				if ($total['code'] == 'sub_total') {
					$total['title'] = $this->language->get('text_subtotal');
                    $cartSubTotal = $total['value'];
				} elseif ($total['code'] == 'total') {
					$total['title'] = $this->language->get('text_total_amount');
					$cartTotal = $total['value'];
				} elseif ($total['code'] == 'credit') {
					$store_credit = $total['value'];
				} elseif ($total['code'] == 'tax') {
					$tax_in_totals = true;
                    $this->session->data['cst'] = $cst;
                    $this->session->data['tax_refund'] = $total['value'] - $cst; // Refund will be total tax - cst value
					$data['cst'] = $this->currency->format($cst);
					$data['tax_refund'] = $this->currency->format($total['value'] - $cst);  // Refund will be total tax - cst value
				}

				/**Validate discount on free shipping (Ravindra Singh 05-02-2016) Start**/
				if(($total['code'] == "shipping" && $total['code'] == "0") || $total['title'] == "Free Shipping"){
					$freeshipflag = 1;
				}
				/**Validate discount on free shipping (Ravindra Singh 05-02-2016) End**/
                $temp = array(
                    'code' => $total['code'],
                    'title' => $total['title'],
                    'value' => $total['value'],
                    'text'  => $this->currency->format($total['value'])
                );
				$data['totals'][] = $temp;

                $totals_data[$total['code']] = $temp;

			}

			$surface_shipping =  SURFACE_FREE_SHIPPING-$totals_data['sub_total']['value'];
            $data['surface_shipping'] = ($surface_shipping > 0) ? "'".$this->currency->format($surface_shipping)."'" : 0;

            $data['totalJson'] = json_encode($totals_data);

            $zone_name = '';
			if ( $this->customer->isLogged() ) {
				if(isset($this->session->data['shipping_address']) && !empty($this->session->data['shipping_address'])){
                    if (isset($this->session->data['shipping_address']['zone']))
					    $zone_name = $this->session->data['shipping_address']['zone'];
				}
				if(isset($this->session->data['payment_address']) && !empty($this->session->data['payment_address'])){
                    if (isset($this->session->data['payment_address']['zone']))
					    $zone_name = $this->session->data['payment_address']['zone'];
				}
			}

            // to determine whether to show Form C option or not
            $data['show_cform_option'] = false;
            if ($zone_name != 'Rajasthan' and $tax_in_totals and $store_id != INTERNATIONAL_STORE_ID)
                $data['show_cform_option'] = true;

			$sum_cart_credit = $cartSubTotal; //$cartTotal + abs($store_credit);

            $language['text_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'),
                $this->currency->format( (float)($this->config->get('config_cart_limit')),
                    $this->currency->getCode(), 1 ));

            $data['language'] = json_encode($language);
						
						// cart limit 
						$t_cart_limit = $this->config->get('config_cart_limit');
						if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
							$t_cart_limit = $this->currency->convertLiveRates($t_cart_limit, 'USD', $this->currency->getCode());
						}

            $exception_customer_ids = array(52);
            if ( $this->customer->isLogged()
                 and in_array($this->customer->getId(), $exception_customer_ids) ) {}
            else {
                if ( $sum_cart_credit*$this->currency->getValue() == 0 || $sum_cart_credit*$this->currency->getValue() < (float)($t_cart_limit) ) {
				    $data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'),
                                                          $this->currency->format( round($t_cart_limit),
                                                          $this->currency->getCode(), 1 ));
				    $this->session->data['error_cart_minimum'] = true;
					$data['cart_error_show'] = 1;
                    $data['disable_place_order'] = 1;
                    $data['text_cart_minimum'] = $data['text_cart_minimum'] . sprintf($this->language->get('error_cart_minimum'),
                        $this->currency->format( round($t_cart_limit),
                            $this->currency->getCode(), 1 ));
			    }
            }

            //customer data
            // get customer upi vpa 
            $upi = new Upi($this);
            $customer_vpa = "";

            $customer_vpa_result = $upi->getCustomerVPA($this->customer->getId());
            if(!empty($customer_vpa_result['upi_vpa'])) {
                    $customer_vpa = $customer_vpa_result['upi_vpa'];
            }
            $telephone = $this->customer->getTelephone();
            $customer_data = array(
                "customer_id" => $this->customer->getId(),
                "customer_vpa" => $customer_vpa,
                "is_dropshipper" => $this->customer->is_dropshipper,
                "gst_number" => $this->customer->getGSTNumber(),
                "address_id" => $this->customer->getAddressId(),
                "gst_option" => 0,
                "telephone" => !empty($telephone)?$telephone:""
            );
            $data['customer_data'] = json_encode($customer_data);

			$data['continue'] = $this->url->link('common/home', '', 'SSL');

			// it is used in one page checkout page by vikas(05-07-2016)
			$this->session->data['cart_total_value'] = $cartTotal;
			$this->session->data['config_customer_cart_limit_value'] = $this->config->get('config_customer_cart_limit');

            if(!$this->customer->isLogged())
			   $data['checkout'] = $this->url->link('account/login', 'referrers=cart', 'SSL');
            else
               $data['checkout'] = $this->url->link('checkout/one_page_checkout', '', 'SSL','delivery');

			//$data['clear_cart'] = $this->url->link('checkout/cart/clearCart','','SSL');
			$data['logged'] = $this->customer->isLogged();

			$this->load->model('extension/extension');

            $data['checkout_buttons'] = array();
			//$data['coupon'] = $this->load->controller('checkout/coupon');
			//$data['voucher'] = $this->load->controller('checkout/voucher');
			//$data['reward'] = $this->load->controller('checkout/reward');
			//echo $cartTotal; exit;
			// it is used in cart.tpl file by ravindra/vikas(05-07-2016)
			$data['cartlimitcross'] = 0;
			$data['show_more_shipping'] = 1;
			//if($store_id != INTERNATIONAL_STORE_ID) {

				if ($this->customer->isLogged()) {
					//echo $cartTotal; exit;

					if ($this->config->get('config_customer_cart_limit') > $cartTotal) {
						$data['cartlimitcross'] = 0;
						if ($this->customer->is_dropshipper == 1) {
							$this->config->set('default_shipping_method', 'weight.weight_8');
							$data['shipping'] = '';
						} else {
							$this->config->set('default_shipping_method', 'weight.weight_5');
							$data['shipping'] = $this->load->controller('checkout/shipping');
						}
					} else {
						$data['cartlimitcross'] = 1;
						if ($this->customer->is_dropshipper == 1) {
							$this->config->set('default_shipping_method', 'weight.weight_8');
							//$data['shipping'] = '';
							$data['shipping'] = $this->load->controller('checkout/shipping');

						} else {
							$this->config->set('default_shipping_method', 'weight.weight_5');
							$data['shipping'] = $this->load->controller('checkout/shipping');
						}
						//$this->config->set('default_shipping_method', 'weight.weight_5');

						//$data['shipping'] = $this->load->controller('checkout/shipping');

					}

				} else {

					$data['shipping'] = $this->load->controller('checkout/shipping');
				}
			//}else{
			//	$data['shipping'] = '';
			//}

			//these 2 method call added by rakesh to display shipping in cart
			//echo "<pre>"; print_r($this->session->data['shipping_method']); exit;
			if (!isset($this->session->data['shipping_method'])) {
				$this->quote_cart();
				$this->shipping_cart();
			}else{
				//echo "<pre>";	 print_r($this->session->data['shipping_method']); exit("ddd");
				if(isset($this->customer->is_dropshipper) && ($this->customer->is_dropshipper == 1) && isset($this->session->data['shipping_method']['code']) && $this->session->data['shipping_method']['code'] != $this->config->get('default_shipping_method')){
					$this->quote_cart();
					$this->shipping_cart();
					//echo "<pre>"; print_r($this->config->get('default_shipping_method')); exit;
				}else if(isset($this->customer->is_dropshipper) && ($this->customer->is_dropshipper == 0) && isset($this->session->data['shipping_method']['code']) && $this->session->data['shipping_method']['code'] != $this->config->get('default_shipping_method')){
					$this->quote_cart();
					$this->shipping_cart();
				}
			} // Commented by Madhur to allow shipping estimator back

            // To show a popup if "Your courier" shipping method is selected
			if(isset($this->session->data['shipping_method']) && $this->session->data['shipping_method']['code']=='weight.weight_7'){
				$data['custom_msg'] = $this->language->get('text_custom_msg');
				$data['cart_error_show'] = 1;
				$data['redirect'] = $this->url->link('checkout/one_page_checkout','','SSL');
			}

           if(CONFIG_IS_MOBILE == 1)
           {
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		   }
			//echo "<pre>"; print_r($data); exit;
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/cart.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/cart.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/checkout/cart.tpl', $data));
			}
		} else {
            $data['empty'] = "1";
            $data['language'] = json_encode($language);
			$data['text_error'] = $this->language->get('text_empty');

			$data['continue'] = $this->url->link('common/home', '', 'SSL');

			unset($this->session->data['success']);


           if(CONFIG_IS_MOBILE == 1)
           {
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		   }
			$data['item_in_cart'] = 0;

        $this->load->model('setting/store');
        $this->model_setting_store->getInternationalSwitch();
        $alertSwitchStore = $this->model_setting_store->alertOnRedirection;
        if($alertSwitchStore){

            $data_popup['logo'] = STATIC_CONTENT_URL_SSL . 'mobile_logo.png';
            $data_popup['name'] = $this->config->get('config_name');

            $data['redirect_popup'] = $this->load->view($this->config->get('config_template') . '/template/common/domain_redirect_popup.tpl', $data_popup);
            $data['show_redirect_popup'] = $alertSwitchStore;
        }


			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/cart.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/cart.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/checkout/cart.tpl', $data));
			}
		}
	}

	public function add() {
		$this->load->language('checkout/cart');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$data['popup'] = false;
		if (isset($this->request->get['popup'])) {
			$data['popup'] = $this->request->get['popup'];
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if (isset($this->request->post['quantity']) && ((int)$this->request->post['quantity'] >= $product_info['minimum'])) {
				$quantity = (int)$this->request->post['quantity'];
			} else {
				$quantity = $product_info['minimum'] ? $product_info['minimum'] : 1;
			}

			if (isset($this->request->post['option'])) {
				$option = array_filter($this->request->post['option']);
			} else {
				$option = array();
			}

			$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
			foreach ($product_options as $product_option) {
				if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
					$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
				}
			}

			if (isset($this->request->post['recurring_id'])) {
				$recurring_id = $this->request->post['recurring_id'];
			} else {
				$recurring_id = 0;
			}

			$recurrings = $this->model_catalog_product->getProfiles($product_info['product_id']);

			if ($recurrings) {
				$recurring_ids = array();

				foreach ($recurrings as $recurring) {
					$recurring_ids[] = $recurring['recurring_id'];
				}

				if (!in_array($recurring_id, $recurring_ids)) {
					$json['error']['recurring'] = $this->language->get('error_recurring_required');
				}
			}

			if (!$json) {
				$this->cart->add($this->request->post['product_id'], $quantity, $option, $recurring_id);
				if ($data['popup'] == true) {
					$json['success'] = sprintf($this->language->get('text_success_cart_added_popup'), $product_info['name']);
				}else{
					$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id'], 'SSL'), $product_info['name'], $this->url->link('checkout/cart', '', 'SSL'));
				}

				$this->_updateLead();

				unset($this->session->data['shipping_method']); /* Shipping method should not reset everytime */
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);

				// Totals
				//
				$total_data = array();
				$total = 0;
				$taxes = 0;
				// $this->load->model('extension/extension');
				// Display prices
				//
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {

                    $total_factory = new TotalFactory( $this, true );
					$total_data = $total_factory->getTotal();
					$total = $total_factory->total;
					$taxes = $total_factory->taxes;
					$cst = $total_factory->cst;
				}

				$json['total_in_cart'] = $this->cart->countProducts();
				$json['total'] = sprintf($this->language->get('text_items'),
				                         $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0),
				                         $this->currency->format($total));
				$json['cart_popup'] = 'index.php?route=checkout/cart&popup=true';

			} else {
				$json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id'], 'SSL'));
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function addWithOptions() {
		$this->load->language('checkout/cart');

		// echo "<pre>"; print_r($this->request->post); die;

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['option_quantities'])) {
			$option_quantities = $this->request->post['option_quantities'];
		} else {
			$option_quantities = 0;
		}
		if (isset($this->request->post['option_max_quantities'])) {
			$option_max_quantities = $this->request->post['option_max_quantities'];
		} else {
			$option_max_quantities = 0;
		}

		$data['popup'] = false;
		if (isset($this->request->get['popup'])) {
			$data['popup'] = $this->request->get['popup'];
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if ($option_quantities) {


				$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
				foreach ($product_options as $product_option) {

					$option_arr = $option_quantities[$product_option['product_option_id']];
					$option_max_arr =  $option_max_quantities[$product_option['product_option_id']];

					$res = array_map(array($this, 'compare_max_option_qty'), $option_arr, $option_max_arr);
					$keys = array_keys($option_arr);

					$net_arr = array_combine($keys, $res);
					if (array_filter($net_arr)) {
						$json['error']['option_value'][$product_option['product_option_id']] = array_filter($net_arr);
					}

					if ($product_option['required'] && !array_filter($option_quantities[$product_option['product_option_id']]) ) {
						$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_options_quantity_required'), $product_option['name']);
					}
				}


			}

			$recurring_id = 0;
			if (!$json) {
				foreach ($option_quantities as $option_value => $value_arr) {
					foreach ($value_arr as $option__value_id => $quantity) {
						if ($quantity) {
							$this->cart->add($this->request->post['product_id'], $quantity, array($option_value=>$option__value_id ), $recurring_id);
						}
					}
				}

				if ($data['popup'] == true) {
					$json['success'] = sprintf($this->language->get('text_success_cart_added_popup'), $product_info['name']);
				}else{
					$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id'], 'SSL'), $product_info['name'], $this->url->link('checkout/cart', '', 'SSL'));
				}

				unset($this->session->data['shipping_method']); /* Shipping method should not reset everytime */
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);

				// Totals
				$this->load->model('extension/extension');

				$total_data = array();
				$total = 0;
				$taxes = 0;

				// Display prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $total_factory = new TotalFactory( $this, true );
					$total_data = $total_factory->getTotal();
					$total = $total_factory->total;
					$taxes = $total_factory->taxes;
					$cst = $total_factory->cst;
				}
				$json['total_in_cart'] = $this->cart->countProducts();
				$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
				$json['cart_popup'] = 'index.php?route=checkout/cart&popup=true';

			} else {
				$json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id'], 'SSL'));
			}
		}
		// echo "<pre>"; print_r(json_encode($json)); die;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function addCForm() {

		$json = array();

		if (isset($this->request->post['cform_submit'])) {
			$cform_submit = (int)$this->request->post['cform_submit'];
		} else {
			$cform_submit = 0;
		}
        $this->session->data['cform_submit'] = $cform_submit;
		$json['success'] = sprintf('success');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function edit() {
		$this->load->language('checkout/cart');

		$json = array();

		// Update
		if (!empty($this->request->post['quantity'])) {
			//echo "<pre>"; print_r($this->request->post['quantity'][$this->request->post['quantity_change']]); exit;
			//foreach ($this->request->post['quantity'] as $key => $value) {
				if(isset($this->request->post['quantity'][$this->request->post['quantity_change']])){
					$key = $this->request->post['quantity_change'];
					$qnty = $this->request->post['quantity'][$this->request->post['quantity_change']];
					$this->cart->update($key, $qnty);
					//break;
				}
			//}
//a:5:{s:104:"YToyOntzOjEwOiJhZGRlZF90aW1lIjtzOjE5OiIyMDE2LTExLTAyIDE2OjI1OjA1IjtzOjEwOiJwcm9kdWN0X2lkIjtpOjM3NTQ1O30=";i:1;s:104:"YToyOntzOjEwOiJhZGRlZF90aW1lIjtzOjE5OiIyMDE2LTExLTAyIDE2OjI5OjU5IjtzOjEwOiJwcm9kdWN0X2lkIjtpOjM4NzQ7fQ==";i:10;s:104:"YToyOntzOjEwOiJhZGRlZF90aW1lIjtzOjE5OiIyMDE2LTExLTAyIDE2OjMwOjM1IjtzOjEwOiJwcm9kdWN0X2lkIjtpOjMzMDM3O30=";i:1;s:104:"YToyOntzOjEwOiJhZGRlZF90aW1lIjtzOjE5OiIyMDE2LTExLTAyIDE2OjMwOjQ4IjtzOjEwOiJwcm9kdWN0X2lkIjtpOjM2NDEwO30=";i:1;s:104:"YToyOntzOjEwOiJhZGRlZF90aW1lIjtzOjE5OiIyMDE2LTExLTAyIDE2OjMwOjQ5IjtzOjEwOiJwcm9kdWN0X2lkIjtpOjM4MzUxO30=";i:1;}
			/*unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);*/
//exit;
			$this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function remove() {
		$this->load->language('checkout/cart');
		$json = array();

		// Remove
		if (isset($this->request->post['key'])) {
			$this->cart->remove($this->request->post['key']);

			//unset($this->session->data['vouchers'][$this->request->post['key']]);

			$this->session->data['success'] = $this->language->get('text_remove');

			/*unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);*/

			// Totals
			// $this->load->model('extension/extension');

			$total_data = array();
			$total = 0;
			$taxes = 0;

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $total_factory = new TotalFactory( $this, true );
				$total_data = $total_factory->getTotal();
				$total = $total_factory->total;
				$taxes = $total_factory->taxes;
				$cst = $total_factory->cst;
			}
            $json['total_in_cart'] = $this->cart->countProducts();
			$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Added by Rakesh to apply shipping rate (weight base method) manually in cart
	 */

	public function quote_cart() {
		$this->load->language('checkout/shipping');
		$this->load->model('localisation/country');
		$json = array();

		if(isset($_COOKIE['user_country']) && !empty($_COOKIE['user_country'])){

			$country_cod = $_COOKIE['user_country'];
		}else{
			$country_cod = "IN";
		}
		$country_data = $this->model_localisation_country->getCountryByCode($country_cod);

		if(isset($country_data['country_id']) && !empty($country_data['country_id'])){
			$country_id = $country_data['country_id'];
			$this->request->post['country_id'] = $country_id;
			$this->request->post['zone_id'] = 0;
			$this->request->post['postcode']  = '';

		}else{
			$country_id = '99';
			$this->request->post['country_id'] = $country_id;
			$this->request->post['zone_id'] = 1501;
			$this->request->post['postcode']  = '';

		}


		if (!$this->cart->hasProducts()) {
			$json['error']['warning'] = $this->language->get('error_product');
		}

		if (!$this->cart->hasShipping()) {
			$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact', '', 'SSL'));
		}

		if ($this->request->post['country_id'] == '') {
			$json['error']['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id'])) {
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
				$country = 'India';
				$iso_code_2 = 'IN';
				$iso_code_3 = 'IND';
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
			//print_r($this->session->data['shipping_address']);

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
				$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact', '', 'SSL'));
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
			if(isset($this->customer->is_dropshipper) && ($this->customer->is_dropshipper == 1)){
				$this->request->post['shipping_method'] = $this->config->get('default_shipping_method');//'weight.weight_8';
			}else{
				$this->request->post['shipping_method'] = 'weight.weight_5';
			}
		}else{
			//  Default shipping method for dropshipper
			if(isset($this->customer->is_dropshipper) && ($this->customer->is_dropshipper == 1)){
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

			$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
		}

		return true;
	}


	// clear cart function made by vikas (05-05-2016)
	public function clearCart() {
		$this->load->language('checkout/cart');

		$this->cart->clear();

		$this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
	}
	public function compare_max_option_qty($option_quantity, $option_max_quantity)
	{
		if ($option_quantity > $option_max_quantity) {
			return 1;
		} else {
			return 0;;
		}
	}

    /*
         * Method to update CRM Lead on update of cart.
        */
    private function _updateLead($is_cart = 1){

        $customer_id = (int)($this->customer->getId() ?? 0);

        if ( $customer_id > 0 ) {

            $cart_data = '';
            $cart = $this->cart->get_customer_cart('customer_id = '. $customer_id);
            if(!empty($cart)){
                $cart_data = $cart[0]['cart_data'];
            }

            $this->load->model('lead/lead');
            $sql = "SELECT telephone FROM oc_customer WHERE customer_id = ". (int)$customer_id;
            $telephone = $this->db->query($sql)->row['telephone'] ?? '';

            if (!empty($telephone)) {
                $lead_data = [
                    'cart_modified_date'    => date('Y-m-d H:i:s'),
                    'cart'                  => $cart_data,
                    'is_cart'               => $is_cart
                ];
                $this->model_lead_lead->updateLead($lead_data, $telephone, 'cart updated', $customer_id);
            }
        }
    }
}
