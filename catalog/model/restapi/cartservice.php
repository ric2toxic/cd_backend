<?php

class ModelRestapiCartservice extends Model
{
	
	public function UpdateOtp( int $user_id, string $otp ) { 
		
		$sql = "UPDATE 
					" . DB_PREFIX . "customer 
				SET 
					otp = '" . $this->db->escape( $otp ) . "' 
				WHERE 
					customer_id = '" . (int)$user_id . "'
				";
		
		if( $this->db->query( $sql ) ) {

			return 1;

		}else{

			return 0;
		}	
		
	}

	public function countProducts(int $user_id){

      return  $this->cart->countProducts( $user_id );

    }

	public function cart_data($request){

		$this->load->language('checkout/cart');
        $this->load->model('checkout/coupon');
		$this->load->model('restapi/service');
        $this->load->model('setting/setting');

		$user_id = $request['user_id'] ?? '';

        $headers = getallheaders();
        $request_by = 'ANDROID';
        if(isset($headers['REQUEST_BY']) && $headers['REQUEST_BY'] == 'IOS_APP'){
            $request_by = 'IOS_APP';
        }

		$pickup_city = array('JP' => "SHIPMENT FROM JAIPUR",'ST' => "SHIPMENT FROM SURAT",'DL' => 'SHIPMENT FROM DELHI',
            'BL' => 'SHIPMENT FROM BANGALORE','BLR' => 'SHIPMENT FROM BANGALORE','MB' => 'SHIPMENT FROM MUMBAI','MU' => 'SHIPMENT FROM MUMBAI',
            'KL' => 'SHIPMENT FROM KOLKATA');

		if( !empty($request['franchise_id']) ){
		    $this->cart->setFranchiseId($request['franchise_id'],$user_id);
        }
        if( !empty($request['franchise_margin']) ){
            $this->cart->setFranchiseMargin($request['franchise_margin'],$user_id);
        }

		$rt['coupon_message'] = "No coupon code applied";
		
		$country_code = 'IN';
		if(isset($request['country_code'])){
			$country_code = $request['country_code'];
		}
		$country_name = $this->fetchCountryName($country_code);
		$products = $this->cart->getProducts($user_id,true);
		if (empty($products)) {
			$rt['error_code'] = '1001';
			$rt['status'] = '0';
			$rt['status_text'] = 'Your WholesaleBox cart is empty, but it does not have to be.';
			$rt['message'] = 'Your WholesaleBox cart is empty, but it does not have to be.';
			return $rt;
		}
		$text_color_show = "0";

            if( !empty($request['coupon_code']) ){

            	$order_from = "ANDROID_APP";

            	if ( $request_by == "IOS_APP" ) {

            		$order_from = "IOS_APP";
            	}

                $coupon_data = array();

                $coupon_info = $this->model_checkout_coupon->getCoupon( $request['coupon_code'], $order_from );

                if( ($store_code = SalesStaff::checkStoreVoucher( $this->db , $request['coupon_code'] )) ){
                    $coupon_data['wsb_store_voucher'] = $request['coupon_code'];
                    $coupon_data['wsb_store_code'] = $store_code;
                }
                else if( ($store_code = SalesStaff::checkStoreDelivery( $this->db , $request['coupon_code'] )) ){
                    $coupon_data['wsb_store_delivery'] = $request['coupon_code'];
                    $coupon_data['wsb_store_code'] = $store_code;
                }
                else if($franchise_discount = $this->model_checkout_coupon->getFranchiseCoupon($request['coupon_code'],$user_id)){
                    $is_franchise = $this->model_restapi_service->isCustomerFranchise($user_id);
                    if((int)$is_franchise == 1){
                        $coupon_data['coupon_franchise'] = $request['coupon_code'];
                        $coupon_data['franchise_discount'] = $franchise_discount;
                    }
                    else{
                        $rt['coupon_message'] = "Either coupon code is invalid or expired!";
                    }
                }
                else if( $coupon_info['coupon_message']['status'] ){
                    $coupon_data['coupon'] = $request['coupon_code'];
                    $coupon_data['coupon_type'] = $coupon_info['type'];
                }
                else{
                  $coupon_key = $this->model_checkout_coupon->getCouponFromSetting($request['coupon_code']);
                    if (!empty($coupon_key)) {
                        if($coupon_key == 'coupon_franchise_cash' || $coupon_key == 'coupon_franchise_credit'){
                            // Franchise cash or credit coupon is valid only if cart contains franchise products.
                            if(!isset($request['franchise_id'])){
                                $apply_f_coupon = false;
                            }
                            else{
                                $apply_f_coupon = $this->cart->checkFranchiseProductsInCart($request['franchise_id'],$user_id);
                            }
                            if(!$apply_f_coupon){
                                $rt['coupon_message'] = "Either coupon code is invalid or expired!";
                                // In this case remove already applied coupon if any
                                $this->cart->setCoupon( '' );
                            }
                            else{
                                $coupon_data[$coupon_key] = $request['coupon_code'];
                            }

                        }
                        else{
                            $coupon_data[$coupon_key] = $request['coupon_code'];
                        }
                    }
                }

                if (!empty($coupon_data)) {
                    $this->cart->setCoupon( $coupon_data );
                    $rt['coupon_message'] = "Coupon code applied successfully!";
                } else {
						$rt['coupon_message'] = $coupon_info['coupon_message']['message'] ?? "Either coupon code is invalid or expired!";
						// In this case remove already applied coupon if any
						$this->cart->setCoupon( '' );
					}
            }

            $rt['coupon'] = '';
            /// If store voucher applied then setting cart limit to 1
            $coupon_data = $this->cart->getCoupon();
            if( !empty($coupon_data['wsb_store_voucher']) && !empty($coupon_data['wsb_store_code']) ){
                $this->config->set('config_customer_cart_limit',1);
                $this->config->set('config_cart_limit',1);
                $rt['coupon'] = $coupon_data['wsb_store_voucher'];
            }
            else if( !empty($coupon_data['coupon_store_trial']) ){ // store trial coupon
                $this->config->set('config_customer_cart_limit',1);
                $this->config->set('config_cart_limit',1);
                $rt['coupon'] = $coupon_data['coupon_store_trial'];
            }
            else if( !empty($coupon_data['coupon_franchise_cash']) || !empty($coupon_data['coupon_franchise_credit']) ){ // for order by franchise
                $this->config->set('config_customer_cart_limit',1);
                $this->config->set('config_cart_limit',1);
                $rt['coupon'] = array_values($coupon_data)[0];
            }
            else if (!empty($coupon_data['coupon_remove_cart_limit'])) {
                $this->config->set('config_customer_cart_limit',1);
                $this->config->set('config_cart_limit',1);
                $rt['coupon'] = $coupon_data['coupon_remove_cart_limit'];
            }
            else if(!empty($coupon_data)){
                $rt['coupon'] = array_values($coupon_data)[0];
            }

            if ( empty( $coupon_data ) && empty( $request['coupon_code'] )) {
            	
            	// auto apply oldest coupon
				$applied_code = $this->model_checkout_coupon->autoApplyReferralCoupon( (int) $user_id );
				
				if ( !empty( $applied_code )) {
					$rt['coupon'] = $applied_code;
				}
            }

            if(!empty($rt['coupon'])){
                $rt['coupon_message'] = "Coupon code applied successfully!";
            }
            
			if (!$this->cart->hasStock($user_id) && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$data['error_warning'] = $this->language->get('error_stock');
			} else {
				$data['error_warning'] = '';
			}
			if ($this->config->get('config_cart_weight')) {
				$data['weight'] = $this->weight->format($this->cart->getWeight($user_id), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
			} else {
				$data['weight'] = '';
			}
			$this->load->model('tool/image');
			$this->load->model('tool/upload');

            $update_price_by = $this->model_restapi_service->getCustomerSettingByKey('update_price',$user_id);

            if (empty($update_price_by)) {
                $update_price_by = DEFAULT_SHARE_MARGIN;
            } else {
                $update_price_by = $update_price_by['value'];
            }

			$data['products'] = array();
			$total_sets = 0;
			$total_pieces = 0;
			$moq_errors = array();
            $product_moq_error = array();
						$products = $this->cart->getProducts($user_id,true);
			foreach ($products as $product_key => $product) {
			    // calculate updated price
                $pr = $product['price_per_piece'];
                $updated_price = ($update_price_by * $pr)/100;
                $updated_price = ceil($updated_price + $pr);

                // will round to 5 only if currency is inr
                if ($this->currency->getCode() == 'INR') {
                    $updated_price = $this->model_restapi_service->roundUpToAny($updated_price, 5);
                }

				$product_total = 0;
				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}
				if ($product['minimum'] > $product_total) {
					$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}
				if(!empty($product['image'])){
					$image = $this->model_tool_image->resize($product['image'], '400', '600');
				}else{
					$image = $this->config->get('config_url').'image/no_image.png';
				}
				$pieces = $product['piece_in_set'] * $product['quantity'];

                $out_of_stock = 0;
                if($product['stock'] == false &&  $product['donot_have_enough_qty'] == false){
					$out_of_stock = "1";
                    $product['stock_quantity'] = 0;
                    if(sizeof($product['option']) > 0){
                        $product['option'][0]['quantity'] = 0;
                    }
				} elseif ($product['donot_have_enough_qty'] == true){
                    $out_of_stock = "1";
				}

                $moq_error = false;
                if($product['stock'] == true && $product['minimum'] > $product_total ){
                    $moq_error = true;
                    if( !isset($product_moq_error[$product['product_id']]) ) {
                        $moq_errors[] = array( 'key' => $product['key'], 'error' => sprintf($this->language->get('error_minimum_mobile'), $product['name'], $product['minimum']) );
                        $product_moq_error[$product['product_id']] = true;
                    }
                }

				$total_sets += (int)($product['quantity']);
				$total_pieces += (int)($product['piece_in_set'] * $product['quantity']);
				// In case of normal product, combo product id will be same as product id
				$product_info = $this->model_restapi_service->getProduct($product['combo_product_id'],$user_id);
				if (!empty($product_info)) {

				    if(!empty($request['franchise_margin'])){
				        $price_update_by = 1 + (float)$request['franchise_margin']/100.0;
                        $product_info['credit_price'] = $this->currency->format($price_update_by*$product_info['selling_price']);
                    }
                    else{
                        $product_info['credit_price'] = '';
                    }

					$cart_product_info = array(
					    'key'   => ($product['combo_product_id'] == $product['product_id']) ? $product['key'] : $product_key,
							// Here we are sending combo_product_id instead of product id, 
							// because we want all the cart operations performed on combo product instead of associate product
							// in case of normal product combo product id will be product's id 
						'product_id'=> $product['combo_product_id'],
						'image'     => $image,
						'name'      => $product['name'],
						'product_code'  => $product['model'],
						'set_description'     => $product['set_description'],
						'sets'  => $product['quantity'],
						'stock_quantity'=>(int)$product['stock_quantity'],
						'available_stock'     => (string) $product['stock'],// ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
						'price_per_piece' => $this->currency->format($product['price_per_piece']),
						'price_per_piece_with_text' => $this->currency->format($product['price_per_piece'])." /piece",
						'price_unit' => "/piece",
						'price_per_piece_without_text' => $product['price_per_piece'],
						'pieces'=> $product['total_pieces'],
						'piece_per_set'=> $product['piece_in_set'],
						'option' => $product['option'],
						'price'     => $this->currency->format($product['price']),
						'price_per_set'     => $this->currency->format($product['price']),
						'amount_without_tax'     => $this->currency->format($product['total']),
						'tax'       => $this->currency->format($product['tax']),
						'weight'	=> $this->weight->format($product['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')),
						'out_of_stock' => $out_of_stock,
						'product_details' => $product_info,
                        'product_comment' => $product['comment'],
                        'moq_error' => $moq_error,
                        'updated_price' => $this->currency->format($updated_price)

					);

					if(isset($request['app_version_code'])){
						if ( ($request_by == 'ANDROID' && $request['app_version_code'] > 62) ||
                            ($request_by == 'IOS_APP' && $request['app_version_code'] > 2)
                        ){
							$data['products'][$product['seller_pickup_city_code']][] = $cart_product_info;
						}else{
							$data['products'][] = $cart_product_info;
						}
					}else{
						$data['products'][] = $cart_product_info;
					}
				}
			}
			$data['total_sets'] = $total_sets;
			$data['total_pieces'] = $total_pieces;
			$user_data['user_id'] = $user_id;
			$user_data['country_code'] = $country_code;
			if(!empty($request['country_id']))
			    $user_data['country_id'] = $request['country_id'];
			if(!empty($request['zone_id']))
			    $user_data['zone_id'] = $request['zone_id'];
			// If Shipping method not selected then default shipping method will apply
			if(isset($request['shipping_method']) && !empty($request['shipping_method']) && isset($request['zone_id'])){
				$quote_data = $this->quote_cart($user_data);
				$quote_data['shipping_method_selected'] = $request['shipping_method'];
				$shipping_data = $this->shipping_cart($user_id,$quote_data);
			}
			// Totals
			$this->load->model('extension/extension');
			$total_data = array();
			$total = 0;
			$taxes = $this->cart->getTaxes($user_id);
			// CST Calculation
			$CST_CLASS_ID = 12;
			$cst = $this->cart->getCST($CST_CLASS_ID); // CST tax class id is 12
			// Display prices
			if ($this->config->get('config_customer_price') || !$this->config->get('config_customer_price')) {
				$sort_order = array();
				$results = $this->model_extension_extension->getExtensions('total');
				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}
				array_multisort($sort_order, SORT_ASC, $results);
				$extra['user_id'] = $user_id;
				if(isset($shipping_data['shipping_method']) && !empty($shipping_data['shipping_method'])){
					$extra['shipping_method'] = $shipping_data['shipping_method'];
					$rt['shipping_error'] = "0";
					$rt['shipping_error_message'] = "";
				}else{
					$extra['shipping_method'] = '';
					$rt['shipping_error'] = "1";
					$rt['shipping_error_message'] = "Sorry ,We dot not ship orders in ".$country_name;
				}
				$extra['cst'] = $cst;
				$extra['cst_class_id'] = $CST_CLASS_ID;
				$extra['payment_method'] = array();
				//If coupon code is available
				$extra['coupon'] = '';
                $total_factory = new TotalFactory($this, true);
                $total_data = $total_factory->getTotal();
			}
			$data['zone_name'] = '';
			$data['totals'] = array();
			$data['tax_in_totals'] = false;
			$cartTotal = 0.0;
                        $cartSubTotal = 0.0;
			$store_credit = 0.0;
			$total_amount = 0;
            
			foreach ($total_data as $total) {
				$cst_apply = "0";
				if ($total['code'] == 'sub_total') {
					$total['title'] = $this->language->get('text_subtotal');
                    $cartSubTotal = (int)$this->currency->format($total['value'],'','',false);
				} elseif ($total['code'] == 'total') {
					$total['title'] = $this->language->get('text_total_amount');
					$cartTotal = $total['value'];
				} elseif ($total['code'] == 'credit') {
					$store_credit = $total['value'];
					$text_color_show = "0";
				} elseif ($total['code'] == 'tax') {  // Getting tax from Order total table and calculating 60% refund for Form C submission case
					$total['title'] = $this->language->get('text_tax');
					$data['tax_in_totals'] = true;
					$data['cst'] = $this->currency->format( ceil($cst) );
					$data['tax_refund'] = $this->currency->format( ceil($total['value']) - ceil($cst) );  // Refund will be total tax - cst value
					if(isset($request['cst_apply']) && $request['cst_apply'] == "1"){
						$data['totals'][] = array(
							'code'  => $total['code'],
							'title' => "Cst",
							'text'  => $this->currency->format( ceil($total['value']*2.0/5.5) )
						);
						$data['totals'][] = array(
							'code'  => $total['code'],
							'title' => "Tax Refund",
							'text'  => $this->currency->format( ceil($total['value']) - ceil($total['value']*2.0/5.5) )
						);
						$cst_apply = "1";
					}else{
						$cst_apply = "0";
					}
				}
				// free shipping code moved to freeShipping function because free shipping does not apply no more
				if($cst_apply == "0"){
					$data['totals'][] = array(
						'code'  => $total['code'],
						'title' => $total['title'],
						'text'  => $this->currency->format(($total['value']))
					);
				}
				if($total['code'] == "total" || $total['title'] == "text_total_amount"){
					if(isset($store_credit) && !empty($store_credit)){
						$total['value'] = $total['value'] - ($store_credit);
					}
					$total_amount = ceil($total['value']);
					$total_amount_with_text = $this->currency->format(ceil($total['value']));
				}
			}
			// free shipping code moved to freeShipping function because free shipping does not apply no more
			$sum_cart_credit = $cartTotal + abs($store_credit);
			// Checking for cart limit
			$data['error_cart_minimum'] = '';
			$error_cart_minimum = false;
			$store_id = $this->config->get('config_store_id') ;
			/*Remove minimum Purchase price Limit fro dropshipper (Ravindra Singh 02-02-2016) */
			if($this->cart->is_dropshipper($user_id) == 1){
				$this->config->set('config_cart_limit',$this->config->get('config_dropshipper_cart_limit'));
			}

			if ($store_id == 2) { // Singles store
				$single_store_order_limit = (float)$this->config->get('config_limit');
				if ($cartTotal < $single_store_order_limit) {
					$data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'), $this->currency->format(ceil($single_store_order_limit)) );
					$error_cart_minimum = true;
				}
			} elseif ( $sum_cart_credit < (float)($this->config->get('config_cart_limit')) ) {
				$data['error_cart_minimum'] = sprintf($this->language->get('error_cart_minimum'),
					$this->currency->format( (float)($this->config->get('config_cart_limit')) ));
				$error_cart_minimum = true;
			}
			$data['text_out_of_stock_popup'] = $this->language->get('text_out_of_stock_popup');
			$data['continue'] = $this->url->link('common/home');
			$data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
			$this->load->model('extension/extension');
			$data['checkout_buttons'] = array();
			if($this->cart->is_dropshipper($user_id) == 1){
				$data['shipping'] = '';
			}else{
				$data['shipping'] = $this->load->controller('checkout/shipping');
			}
			$order_summary = $this->orderSummary($data['totals'], $text_color_show);
            $rt['show_cst'] = '0';
			$default_payment_method['title'] = "Estimated Shipping cost for Surface Courier( 6-9 days ) ";
			$default_payment_method['specifications'][0]['key'] = "2 to 15 KG";
			$default_payment_method['specifications'][0]['value'] = "Rs. 30 Per KG";
			$default_payment_method['specifications'][1]['key'] = "16 to 30 KG";
			$default_payment_method['specifications'][1]['value'] = "Rs. 25 Per KG";
			$default_payment_method['specifications'][2]['key'] = "31 to 45 KG";
			$default_payment_method['specifications'][2]['value'] = "Rs. 18 Per KG";
			$default_payment_method['specifications'][3]['key'] = "46+ KG";
			$default_payment_method['specifications'][3]['value'] = "Rs. 12 Per KG";
			$default_payment_method['summary'] = " *Rates applicable only if TIN number and road permit is provided, Else a flat rate of Rs 30 will be charged ";
			
			$rt['status'] = '1';
			$rt['status_text'] = 'Success';
			$rt['data'] = $data['products'];
			$rt['pickup_city'] = $pickup_city;
			$rt['order_summary_desplay'] = $order_summary;
			
			$t_cart_limit = $this->config->get('config_cart_limit');
			if ($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {
				$t_cart_limit = floor($this->currency->convertLiveRates($t_cart_limit, 'USD', $this->currency->getCode()));
			}
			
			$rt['cart_limit'] = $t_cart_limit;
			$rt['cart_limit_with_text'] = $this->currency->format($t_cart_limit, $this->currency->getCode(),1);
			$rt['weight'] = $data['weight'];
			$rt['total_amount'] = $cartSubTotal; //sending subtotal to compare with cart limit//$total_amount;

			$rt['total_amount_inr']       = $total_amount;

            // iOS app still dont have currency converter[March 2019]. Quick Fix for currency issue on .co store
            if($request_by == 'IOS_APP'){
                $rt['total_amount_with_text'] = str_replace("Rs.","Rs ",$total_amount_with_text);
            } else{
                $rt['total_amount_with_text'] = $total_amount_with_text;
            }
			$rt['total_cart'] = $data['total_sets'];
			$rt['total_products'] = $data['total_pieces'];
			$rt['total_items'] = $data['total_sets'].' Sets = '.$data['total_pieces'].' Pieces';
			$rt['default_payment_method'] = $default_payment_method;
			$rt['moq_errors'] = $moq_errors;

		return $rt;
	}

	public function shipping_cart( int $user_id, $quote_data ) {
		
		$this->load->language('checkout/shipping');

		$json = array();

		if(isset($quote_data['shipping_address']['country_id']) && $quote_data['shipping_address']['country_id'] == "99"){
			//  Default shipping method for dropshipper
			if($this->cart->is_dropshipper($user_id) == 1){
				$this->request->post['shipping_method'] = 'weight.weight_8';
			}else{
				if(isset($quote_data['shipping_method_selected']) && !empty($quote_data['shipping_method_selected'])){
					$this->request->post['shipping_method'] = $quote_data['shipping_method_selected'];
				}else{
					$this->request->post['shipping_method'] = 'weight.weight_5';
				}
			}
		}else{
			//  Default shipping method for dropshipper
			if($this->cart->is_dropshipper($user_id) == 1){
				$this->request->post['shipping_method'] = 'weight.weight_9';
			}else{
				if(isset($quote_data['shipping_method_selected']) && !empty($quote_data['shipping_method_selected'])){
					$this->request->post['shipping_method'] = $quote_data['shipping_method_selected'];
				}else{
					$this->request->post['shipping_method'] = 'weight.weight_9';
				}
			}

		}

		if (!empty($this->request->post['shipping_method'])) {
			
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($quote_data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$json['warning'] = $this->language->get('error_shipping');
			}

		} else {
			$json['warning'] = $this->language->get('error_shipping');
		}

		if (!$json) {

			$shipping = explode('.', $this->request->post['shipping_method']);

			$json['shipping_method'] = $quote_data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

		}

		return $json;
	}

	public function getShippingMethods ( string $access_token, $user_data) {

		$check_access_token = $this->model_restapi_service->checkUserByAccessToken((string)$access_token, (int)$user_data['user_id']);
		
		$rt = array();
		
		if($check_access_token > 0){
		
			$shipping_methods = $this->quote_cart($user_data);
			$rt['status'] = '1';
			$rt['status_text'] = 'Success';
			$rt['data'] = $shipping_methods['shipping_methods'];

		}else{

			$rt['error_code'] = '1003';
			$rt['status'] = '0';
			$rt['status_text'] = 'Failed';
			$rt['message'] = 'Invalid Access Token.';
		}
		return $rt;
	}

	public function quote_cart($user_data) {

		$this->load->language('checkout/shipping');
		$this->load->model('localisation/country');

		$user_id = $user_data['user_id'];
		$country_cod = $user_data['country_code'];

		$json = array();
		$return_data = array();

		$country_data = $this->model_localisation_country->getCountryByCode($country_cod);

		if(isset($country_data['country_id']) && !empty($country_data['country_id'])){
			$country_id = $country_data['country_id'];
		}else{
			$country_id = '99';
		}

		if(!empty($user_data['country_id'])) {
            $this->request->post['country_id'] = $user_data['country_id'];
        }
		else {
            $this->request->post['country_id'] = $country_id;
        }

		if(!empty($user_data['zone_id'])) {
            $this->request->post['zone_id'] = $user_data['zone_id'];
        }
		else {
            $this->request->post['zone_id'] = 0;
        }

		$this->request->post['postcode']  = '';

		if (!$this->cart->hasProducts($user_id)) {
			$json['error']['warning'] = $this->language->get('error_product');
		}

		if (!$this->cart->hasShipping($user_id)) {
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

			$shipping_address = array(
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
			$json['shipping_address'] = $shipping_address;
			$quote_data = array();

			$this->load->model('extension/extension');

			$results = $this->model_extension_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					if($result['code'] != "free"){ //remove free shipping option
						$this->load->model('shipping/' . $result['code']);

						$quote = $this->{'model_shipping_' . $result['code']}->getAppQuote($shipping_address,$user_id);

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
			}
			$sub_total = $this->cart->getSubTotal($user_id);


			if($this->cart->is_dropshipper($user_id) == '1' && $sub_total < $this->config->get('config_cart_limit')){
				$dropshipper_shipping_method = $quote_data['weight']['quote']['weight_8'];
				$quote_data['weight']['quote'] = array();
				$quote_data['weight']['quote']['weight_8'] = $dropshipper_shipping_method;
			}

			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);
			$shipping_methods = $quote_data;

			if ($shipping_methods) {
				$json['shipping_methods'] = $shipping_methods;
			} else {
				$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
			}

		}

		return $json;
	}

	/**
	 * function fetchCountryName
	 * @param $country_code
	 * @author Kuldeep
	 * @return string $country_name
	 * */
	public function fetchCountryName($country_code) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 LIKE '%" . $this->db->escape($country_code) . "%'");
		
		if(isset($query->row['name']) && !empty($query->row['name'])){
			$country_name = $query->row['name'];
		}else{
			$country_name = '';
		}
		return $country_name;
	}

	public function orderSummary($data, $text_color_show) {
		$i = 0;
		$order_summary = array();
		foreach($data as $record){
			$order_summary[$i]['key'] = $record['title'];
			$order_summary[$i]['value'] = $record['text'];
			$order_summary[$i]['text_color_show'] = $text_color_show;
			$i++;
		}
		return $order_summary;
	}


    /**
     * Method to return an array of all the unique children category ids
     * given parent category ids. It goes upto infinite level deep.
     * Input(s):
     * @param array  parent_ids
     * @param boolean (optional defaulted to false) include_parent (Return array to include parent_id or not)
     * Output: Array of category_ids
     * Author: Mahaveer
     */
    public function getChildCategoryIds($parent_ids, $include_parent = false) {

    	if (empty($parent_ids)){
    		return array();
    	}

        $breakLoop = false;
        $return_ids = array();

        // Remove duplicate from parent_ids
        $parent_ids = array_unique($parent_ids, SORT_NUMERIC);

        if ($include_parent) {
            $return_ids = $parent_ids;
        }

        // This array is needed to be filled to prevent rechecking a parent id. To avoid circular loop.
        $already_checked_ids = array();

        while (!$breakLoop) {

            $sql = "SELECT category_id FROM " . DB_PREFIX . "category
                    WHERE parent_id IN (" . implode(', ', $parent_ids) . ")";
            $query = $this->db->query($sql);

            // Insert Present Level parent ids into already checked list
            $already_checked_ids = array_merge($already_checked_ids, $parent_ids);

            if ($query->num_rows) {

                foreach ($query->rows as $row) {

                    $child_cat_id = (int)$row['category_id'];

                    if ( !in_array( $child_cat_id, $return_ids ) ) { // getting the childern if they are not already stored previously
                        $return_ids[] = $child_cat_id;
                    }

                    // Initialize next level parent ids
                    $parent_ids = array();

                    // Every child is a potential next level parent id unless it has already been checked for
                    if ( !in_array( $child_cat_id, $already_checked_ids ) ) {
                        $parent_ids[] = $child_cat_id;
                    }
                }

                // If no more parent_ids at next level to check
                if ( empty($parent_ids) ) {
                    $breakLoop = true;
                }
            } else { // no more children found
                $breakLoop = true;
            }
        }

        return $return_ids;
    }


	public function get_cart_banner_data($category_id)
	{
       $result = array();

	   $store_id = $this->config->get('config_store_id');	

       $banner_id =  CART_BANNER_MOBILE_APP;

       $child_category_ids = $this->getChildCategoryIds($category_id);

       $category_id = array_merge($category_id,$child_category_ids);

       $sql = "SELECT * FROM " . DB_PREFIX . "banner b
                  INNER JOIN ". DB_PREFIX . "banner_image bi ON(b.banner_id  = bi.banner_id)
                  INNER JOIN " . DB_PREFIX . "wsb_banners_image_cart_category bicc ON (bicc.banner_image_id  = bi.banner_image_id)
                  LEFT JOIN " . DB_PREFIX . "banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id)
                  WHERE bi.banner_id = '" . (int)$banner_id . "' AND bid.language_id = '" . (int)$this->config->get('config_language_id') . "'AND b.store_id = '" . (int)$store_id . "' AND bi.status = 1 ORDER BY bi.sort_order ASC";
        $sql_result = $this->db->query($sql);
                  
        foreach($sql_result->rows as $rows)
        {
        	$positive_category = unserialize($rows['positive_category']);
        	$negative_category = unserialize($rows['negative_category']);

        	$negative_category_check = array_intersect($category_id,$negative_category);
        	$positive_category_check = array_intersect($category_id,$positive_category);
        	
        	if(count($negative_category_check) == 0 && count($positive_category_check) > 0)
        	{
        	   $result[] = $rows;	
        	}

        }

        return $result;

	}

	/**
	 * @return array [description]
	 * @author Anurag Jain, 19 June 2019
	 */
	public function getCartBottomBanner()
	{
		$bottom_banner = array();
		$free_shipping_min_amount = 15000;

		$subtotal = $this->getCartSubTotal();

		$background_text = "YAY! You get FREE Surface Shipping. (T&C apply: Free shipping offer may be revoked on Stocklots)";

		if ( (int)$subtotal < $free_shipping_min_amount ) {
			$remaining_amount = $free_shipping_min_amount - (int)$subtotal;
			$background_text = "Add products worth atleast ". str_replace( 'Rs. ', '₹', $this->currency->format( $remaining_amount )) ." more\nto get free shipping.";
		}

		$bottom_banner['aspect_ratio'] = "4.0";

		$bottom_banner['background_image_url'] = "http://d36qiqd7gl7e25.cloudfront.net/img/blue_gradient_1200_300.png";
		$bottom_banner['background_text'] = $background_text;
		$bottom_banner['background_text_size_dp'] = "16";
		$bottom_banner['background_text_title'] = "Free Shipping!!";
		$bottom_banner['background_text_title_size_dp'] = "18";

		$bottom_banner['text_color_hex_code'] = "#ffffff";
		$bottom_banner['title_text_color_hex_code'] = "#ffffff";

		$final_bottom_banners[][] = $bottom_banner;

		return $final_bottom_banners;
	}

	/**
	 * @return int [description]
	 * @author Anurag Jain, 19 June 2019
	 */
	public function getCartSubTotal()
	{
		$subtotal = 0;

		$total_factory = new TotalFactory($this, true);
		$total_data = $total_factory->getTotal();

		if ( !empty( $total_data )) {
			
			foreach ( $total_data as $key => $amounts_data ) {
				if (!empty( $amounts_data['code'] && $amounts_data['code'] == 'sub_total' )) {
					$subtotal = $amounts_data['value'];
					break;
				}
			}
		}
		
		return $subtotal;
	}
}
