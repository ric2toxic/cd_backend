<?php

class ControllerCheckoutSuccess extends Controller {

    public function index() {

        $this->load->model('tool/image');
        $pickup_city_map = array(
            "JP" => "JAIPUR",
            "DL" => "DELHI",
            "ST" => "SURAT",
            "BLR" => "BANGALORE",
            "BL" => "BANGALORE"
        );

         //vernacular language for app
         if (!empty($this->request->get['language'])) 
         {
           $data['app_language'] = $this->request->get['language'];
           $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
         }
         else if (!empty($this->session->data['app_language'])) 
          {
             $data['app_language'] = $this->session->data['app_language'];
             $this->language->switchLanguage(VERNACULAR_LANGUAGE[$data['app_language']]);
          }
         else
         {
           $data['app_language'] = $this->config->get('config_language_id'); 
         }

         //$this->session->data['order_id'] = 44101;
         //----end code---
        // We check that Order ID must be in session to show the Success Page
        // Also, the success page must not either have been shown previously
        // If we show success page, at the end we ensure to unset order_id from session
        // On clicking refresh, customer will be redirected to home page
        if (isset($this->session->data['order_id']) &&
                !isset($this->session->data['success_page_already_shown'])) {
            $data = array();

            if ($this->request->server['HTTPS']) {
                $server = $this->config->get('config_ssl');
            } else {
                $server = $this->config->get('config_url');
            }


            $data['request_uri'] = $_SERVER['REQUEST_URI'];
            if ($this->request->server['HTTPS']) {
                $data['in_store'] = 'https://' . INDIA_STORE_HOST;
                $data['co_store'] = 'https://' . INTERNATIONAL_STORE_HOST;
            } else {
                $data['in_store'] = 'http://' . INDIA_STORE_HOST;
                $data['co_store'] = 'http://' . INTERNATIONAL_STORE_HOST;
            }

            $header_language = array();
            $footer_language = array();
            $login_language = array();

            $this->load->autoLoadLanguage('common/header', $header_language);
            $this->load->autoLoadLanguage('common/footer', $footer_language);
            $this->load->autoLoadLanguage('account/login', $login_language);
            $data['header_language'] = json_encode($header_language);
            $data['footer_language'] = json_encode($footer_language);
            $data['login_language'] = json_encode($login_language);
            $data['lang'] = $header_language['code'];
            $data['direction'] = $header_language['direction'];

            $store_id = (int) ($this->config->get('config_store_id'));
            $data['international_store'] = 0;
            if ($store_id == INTERNATIONAL_STORE_ID)
                $data['international_store'] = 1;

            $this->load->language('information/contact');
            $data['social_meta_tags'] = $this->document->getSocialMetaTags();
            $data['base'] = $server;
            $this->document->setTitle($this->language->get('heading_title'));

            $this->document->setDescription($this->config->get('config_meta_description'));
            $this->document->setKeywords($this->config->get('config_meta_keyword'));
            $data['title'] = $this->document->getTitle();

            $data['social_meta_tags'] = $this->document->getSocialMetaTags();
            $data['base'] = $server;
            $data['description'] = $this->document->getDescription();
            $data['keywords'] = $this->document->getKeywords();
            $data['links'] = $this->document->getLinks();
            $data['styles'] = $this->document->getStyles();


            $data['icon'] = $this->model_tool_image->getOriginalImage($this->config->get('config_icon'));

            $this->session->data['success_page_already_shown'] = $this->session->data['order_id'];

            $this->load->language('checkout/cart');
            $this->load->language('checkout/success');
            $this->load->language('account/sms_templates');
            
            $this->load->model('checkout/order');
            $this->load->model('setting/setting');

            $data['gaTracking'] = '';
            $order_id = (int) ($this->session->data['order_id']);

            // Getting Order Details								                    
            $selector = array('order' => array(), 'suborder' => array(), 'order_product' => array()); 
            //Check if payment made via fixed amount coupon or cashback  
            $order_info = OrderPayment::getOrderInfoIfNetPayableAmountApplicable($this->db, $order_id, $selector);

            $data['customer_data']    = OrderPayment::getOrderCustomerInfo($this->db,$order_info['order']['customer_id']);
            if(!empty($data['customer_data']['gst_number']))
            {
               $data['customer_data']['gst_number'] = 1;  
            }
            else
            {
               $data['customer_data']['gst_number'] = 0;  
            }
            $data['customer_data']['is_dropshipper'] = $data['customer_data']['is_dropshipper'];  
            $data['web_engage_data'] = array();
            $data['web_engage_data']['transaction_id'] = $order_info['order']['order_id'];
            $data['web_engage_data']['customer_id']    = $order_info['order']['customer_id'];
            $data['web_engage_data']['email']          = $order_info['order']['email'];
            $data['web_engage_data']['telephone']      = $order_info['order']['telephone'];
            $data['web_engage_data']['date_added']     = $order_info['order']['date_added'];
            $data['web_engage_data']['payment_mode']   = $order_info['order']['payment_code'];
            $data['web_engage_data']['payment_city']   = $order_info['order']['payment_city'];
            $data['web_engage_data']['payment_postcode'] = $order_info['order']['payment_postcode'];
            $data['web_engage_data']['shipping_city'] = $order_info['order']['shipping_city'];
            $data['web_engage_data']['shipping_postcode'] = $order_info['order']['shipping_postcode'];
            $data['web_engage_data']['product_ids']    = array();
            $data['web_engage_data']['product_names']  = array();
            $data['web_engage_data']['products_price'] = array();
            $data['web_engage_data']['cart_item']      = 0;
            $data['web_engage_data']['cart_value']     = 0;
            $data['web_engage_data']['number_of_unique_sku']  = 0;
            $data['web_engage_data']['order_total']    = $order_info['order']['total'];
            $data['web_engage_data']['order_from']     = $order_info['order']['order_from'];
            $data['web_engage_data']['seller_id']      = array();
            $data['web_engage_data']['seller_nickname']      = array();
            
            foreach($order_info['suborder'] as $suborder)
            {
                foreach($suborder['order_product'] as $order_product)
                {
                    $category_data = OrderPayment::getOrderCategory($this, $order_product['product_id']);

                    $data['web_engage_data']['product_ids'][] = $order_product['product_id'];
                    $data['web_engage_data']['product_names'][] = substr($order_product['name'],0,100);
                     $data['web_engage_data']['category_ids'][] = $category_data['category_id'];
                    $data['web_engage_data']['category_names'][] = $category_data['name'];
                    $data['web_engage_data']['products_price'][] = ($order_product['piece_in_set']*$order_product['quantity'])*$order_product['price_per_piece'];

                    $data['web_engage_data']['cart_value'] += ($order_product['piece_in_set']*$order_product['quantity'])*$order_product['price_per_piece'];

                    $data['web_engage_data']['cart_item'] += ($order_product['piece_in_set']*$order_product['quantity']);
                    $data['web_engage_data']['seller_id'][] = $order_product['seller_id'];
                    $data['web_engage_data']['seller_nickname'][] = OrderPayment::getOrderSellerNickname($this, $order_product['seller_id']);
                    $data['web_engage_data']['number_of_unique_sku']++;
                }
            }
            //$order_info = OrderInfo::getOrderInfo($this->db, $order_id,'',$selector);
            if (!empty($this->session->data['net_order_totals'])) {
                $order_info['total'] = $this->session->data['net_order_totals'];
            }
            $sub_order_info = $order_info['suborder'];
            $order_info = $order_info['order'];

            $data['text_success']  = $this->language->get('text_success');
            $data['button_submit'] = $this->language->get('button_submit');

            // Check if order actually exists
            if (!empty($order_info)) {

                $support_number = 8696491521;
                $request['customer_id'] = $this->customer->getId();
                $request['token'] = $this->customer->getAccessToken();

                $data_json = json_encode($request);
                $api_url = CRM_URL . "cron/getAgentofCustomer";
                $ch = curl_init($api_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                    'Content-Length: ' . strlen($data_json))
                );
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                $result = json_decode($result, true);

                if ($result['status'] == 1) {
                    if (!empty($result['agent'])) {
                        $support_number = $result['agent'] . ', +91-8696491521';
                    } else if (!empty($result['team_lead'])) {
                        $support_number = $result['team_lead'] . ', +91-8696491521';
                    } else {
                        $support_number = $result['support'];
                    }
                }
                
                // Creating URL to Order Detail page for the Order just placed
                $link = '';
                if (!empty($this->session->data['ctoken'])) {
                    $link = '&ctoken=' . $this->session->data['ctoken'];
                }
                
                if(CONFIG_IS_MOBILE == 1)
                {
                   $order_url = 'account/order_history';
                }
                else
                {
                  
                   $order_url = $this->url->link('account/order', $link, 'SSL');
                }
                

                // For now Razorpay is avoided as its mechanism is not fully integrated
                $payment_gateway = PaymentGatewayFactory::getPaymentGateway($this);

                if (isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code'] == 'cod') {

                    $advance = ceil($order_info['total'] * 10 / 100);
                    $advance = ($advance < 1000) ? 1000.00 : $advance;

                    $data['text_message'] = sprintf($this->language->get('text_customer_cod'), $order_url, $order_info['order_no'], $this->currency->format($advance, $order_info['currency_code'], $order_info['currency_value'], true), '', $support_number);
                    // Send COD Email
                    $this->model_checkout_order->successfullyEmailOnCOD($order_info['order_no'], $order_info['email'], $data['text_message']);

                    // Generating Payment Link
                    $order_data = array();
                    $order_data['order_id'] = $order_id;
                    $order_data['amount'] = sprintf("%.2f", $advance);
                    $order_data['order_total'] = sprintf("%.2f", $order_info['total']);
                    $order_data['order_no'] = $order_info['order_no'];

                    $response = $payment_gateway->generatePaymentLink($order_data);
                    if ($response['responseMsg'] == 'SUCCESS') {
                        $payment_url = sprintf($this->language->get('text_customer_cod_link_success'), $response['specialMsg'], $response['specialMsg']);
                    } else {
                        $payment_url = sprintf($this->language->get('text_customer_cod_link_failure'), '', '');
                    }
                    
                    $data['text_message'] = sprintf($this->language->get('text_customer_cod'), $order_url, $order_info['order_no'], $this->currency->format($advance, $order_info['currency_code'], $order_info['currency_value'], true), $payment_url, $support_number);

                    if(isset($this->session->data['payer_vpa']) && !empty($this->session->data['payer_vpa'])) {
                        $order_data['payer_vpa'] = $this->session->data['payer_vpa'];
                    }

                    if ( !empty( UPI_PAYMENT_METHOD_STATUS )) {
                        $upi_payment_gateway = new Upi($this);
                        $upi_gen_link_response = $upi_payment_gateway->generatePaymentLink($order_data);
                    }
													
				} elseif( isset($this->session->data['payment_method']['code']) 
						  && $this->session->data['payment_method']['code'] == 'bank_transfer') {
					$order_data = array();
					$order_data['order_id']       = $order_id;
					$order_data['amount']         = sprintf("%.2f", $order_info['total']);
					$order_data['order_total']    = sprintf("%.2f", $order_info['total']);
					$order_data['order_no']       = $order_info['order_no'];

					// For franchise customers we will not generate payment link
					if ($this->customer->isFranchise()) {
                        $response = array('responseMsg' => 'FAILURE');
                    } else {
                        $response = $payment_gateway->generatePaymentLink($order_data);
                    }

					if($response['responseMsg'] == 'SUCCESS') {
						$payment_url = sprintf($this->language->get('text_customer_bank_tranfer_success'), 
											   $response['specialMsg'], 
											   $response['specialMsg']);
						$data['text_message'] = sprintf($this->language->get('text_customer_bank_tranfer'),
														$order_url,
														$order_info['order_no'], 
														$this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], true),
														$payment_url, $support_number);
					} else {
						$data['text_message'] = sprintf($this->language->get('text_customer_bank_tranfer'),
														$order_url, 
														$order_info['order_no'], 
														$this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], true),
														'', $support_number);
					}

                    if(isset($this->session->data['payer_vpa']) && !empty($this->session->data['payer_vpa'])) {
                        $order_data['payer_vpa'] = $this->session->data['payer_vpa'];
                    }
                    
                    if ( !empty( UPI_PAYMENT_METHOD_STATUS )) {
                        $upi_payment_gateway = new Upi($this);
                        $upi_gen_link_response = $upi_payment_gateway->generatePaymentLink($order_data);
                    }

				} else if( isset($this->session->data['payment_method']['code']) 
						&& $this->session->data['payment_method']['code'] == 'upi') {

					$order_data = array();
					$order_data['order_id']       = $order_id;
					$order_data['amount']         = sprintf("%.2f", $order_info['total']);
					$order_data['order_total']    = sprintf("%.2f", $order_info['total']);
					$order_data['order_no']       = $order_info['order_no'];

					if(isset($this->session->data['upi_payment_status']) 
						&& $this->session->data['upi_payment_status'] == 'completed') {
						$data['text_message'] 	  = sprintf($this->language->get('text_customer_upi'),
							$order_url,
							$order_info['order_no']	
							);
					} else {
						$response = $payment_gateway->generatePaymentLink($order_data);
						$data['text_message'] = sprintf($this->language->get('text_customer_upi_fail'),
							$order_url,
							$order_info['order_no'],
							$this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], true),
							$response['specialMsg'],
							$response['specialMsg']);

						$upi_payment_gateway = new Upi($this);
						$data['order_id'] = $this->session->data['order_id'];
                        if(isset($this->session->data['payer_vpa']) && !empty($this->session->data['payer_vpa'])) {
                            $order_data['payer_vpa'] = $this->session->data['payer_vpa'];
                        }
                    
						$upi_gen_link_response = $upi_payment_gateway->generatePaymentLink($order_data);
					}

				} else if ( isset($this->session->data['payment_method']['code'])
                    && $this->session->data['payment_method']['code'] == 'franchise' ) {
				    // For orders by franchise we will not show any information
                    $data['text_message'] = '';

                } else {
					$account = $this->url->link('account/account','','SSL');
					$history = $this->url->link('account/order','','SSL');
                    $order_data['order_no'] = $order_info['order_no'];
                    $owner_url = "";

                    $sub_order_details = '';

                    $total_shipments = sizeof($sub_order_info);

                    if (!empty($sub_order_info)) {
                        $shipment_no = 1;
                        foreach ($sub_order_info as $suborder) {
                            $suborder_id = $suborder['suborder_id'];
                            $pickup_city_code = substr($suborder_id, -2);
                            if (!empty($pickup_city_map[$pickup_city_code]))
                                $pickup_city = $pickup_city_map[$pickup_city_code];
                            else
                                $pickup_city = $pickup_city_code;

                            /* $shipment_days = substr(preg_replace('/[^0-9]+/', '', $suborder['shipping_method']),-2);
                              if(!empty($shipment_days) && (int)$shipment_days > 20){
                              $shipment_days = substr($shipment_days,-1);
                              }
                              $text_expected_delivery_date = '';

                              if(!empty($shipment_days)) {
                              $expected_delivery_date = date('dS F Y', strtotime("+" . $shipment_days . " days"));
                              $text_expected_delivery_date = sprintf($this->language->get('text_expected_date'),$expected_delivery_date);
                              } */
                            $text_expected_delivery_date = $this->language->get('text_expected_date'); // For now we are not displaying

                            $sub_order_details .= sprintf($this->language->get('text_shipment'), $shipment_no, $suborder_id, $pickup_city, $text_expected_delivery_date);
                            $sub_order_details .= '<br/>';
                            $shipment_no = $shipment_no + 1;
                        }
                    }

                    $support_number = 8696491521;
                    $request['customer_id'] = $this->customer->getId();
                    $request['token'] = $this->customer->getAccessToken();

                    $data_json = json_encode($request);
                    $api_url = CRM_URL . "cron/getAgentofCustomer";
                    $ch = curl_init($api_url);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                        'Content-Length: ' . strlen($data_json))
                    );
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
                    $result = json_decode($result, true);

                    if ($result['status'] == 1) {
                        if (!empty($result['agent'])) {
                            $support_number = $result['agent'] . ', +91-8696491521';
                        } else if (!empty($result['team_lead'])) {
                            $support_number = $result['team_lead'] . ', +91-8696491521';
                        } else {
                            $support_number = $result['support'];
                        }
                    }


                    $data['text_message'] = sprintf($this->language->get('text_customer'), $order_url, $order_info['order_no'], $total_shipments, $sub_order_details, $history, $support_number);
                }

                // if international order, then show custom duty message on success
                if ($order_info['store_id'] == INTERNATIONAL_STORE_ID) {
                    $data['custom_duty_charge_message'] = $this->language->get('text_custom_duty_charge');
                }

                // Set data for e-commerce tagging
                $data['ecomm_tagging_id'] = $order_info['order_no'] . "_" . $order_info['customer_id'];
                $data['ecomm_tagging_revenue'] = sprintf("%.2f", $order_info['total']);

                $this->document->setTitle($this->language->get('heading_title'));

                $data['social_meta_tags'] = $this->document->getSocialMetaTags();
                $data['base'] = $server;
		        $data['title'] = $this->document->getTitle();
		        $data['description'] = $this->document->getDescription();
	        	$data['keywords'] = $this->document->getKeywords();
	            $data['links'] = $this->document->getLinks();
	        	$data['styles'] = $this->document->getStyles();

        $this->load->model('sale/courier_dockets', 'admin');
        $courier_partners = $this->admin_model_sale_courier_dockets->getCourierPartners();
        $data['courier_partners'] = $courier_partners;
        $data['text_packaging_preference'] = $this->language->get('text_packaging_preference');
				$data['text_no_wsb_tape'] = $this->language->get('text_no_wsb_tape');
        $data['text_no_offline_invoice'] = $this->language->get('text_no_offline_invoice');
				$data['text_courier_preferences'] = $this->language->get('text_courier_preferences');
        $data['text_shipping_preferences_note'] = $this->language->get('text_shipping_preferences_note');
        $data['order_id'] = $order_id;
        $data['ctoken'] = $this->session->data['ctoken'] ?? '';
        
				$data['breadcrumbs'] = array();

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_home'),
					'href' => $this->url->link('common/home')
				);

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_basket'),
					'href' => $this->url->link('checkout/cart')
				);

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_checkout'),
					'href' => $this->url->link('checkout/checkout', '', 'SSL')
				);

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_success'),
					'href' => $this->url->link('checkout/success')
				);

				$data['heading_title'] = $this->language->get('heading_title');

				$data['button_continue'] = $this->language->get('button_continue');

				$data['continue'] = $this->url->link('common/home');
				$data['steps_four'] = $this->language->get('steps_four');
				$data['thank_you'] = $this->language->get('thank_you');

				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				if(CONFIG_IS_MOBILE == 1)
                {
				  $data['footer'] = $this->load->controller('common/footer');
				  $data['header'] = $this->load->controller('common/header');
				}  
				$data['thanks_image'] = 1;

				unset($this->session->data['order_id']);
                unset($this->session->data['app_language']);
				unset($this->session->data['success_page_already_shown']);
                
                if ( isset( $this->session->data['upi_flow_already_processed'] )) {
                    unset( $this->session->data['upi_flow_already_processed'] );
                }

                if ( isset( $this->session->data['free_shipping_coupon_applied'] )) {
                    unset( $this->session->data['free_shipping_coupon_applied'] );
                }
                
                if(isset($this->session->data['payer_vpa'])) {
                    unset($this->session->data['payer_vpa']);
                }
				
				$data['method'] = $this->session->data['payment_method']['code'];

                $data['credit_application_link'] = $this->url->link('account/credit_application','','SSL');
                    

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data));
				} else {
					$this->response->setOutput($this->load->view('default/template/common/success.tpl', $data));
				}
			} else {
				unset($this->session->data['order_id']);
                unset($this->session->data['app_language']);
				unset($this->session->data['success_page_already_shown']);
                
                if(CONFIG_IS_MOBILE == 1)
                {
                    $this->response->redirect(HTTPS_SERVER);
                }
                else
                {
                   $this->response->redirect($this->url->link('account/account', ''  , 'SSL')); 
                }
					
			}
		} else {
			unset($this->session->data['order_id']);
            unset($this->session->data['app_language']);
			unset($this->session->data['success_page_already_shown']);
			if(CONFIG_IS_MOBILE == 1)
                {
                  $this->response->redirect(HTTPS_SERVER);
                }
                else
                {
                   $this->response->redirect($this->url->link('account/account', ''  , 'SSL')); 
                }	
		}
	}
}
