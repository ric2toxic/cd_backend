<?php
class ControllerApiCart extends Controller {
	public function add() {
		
		$this->load->language('api/cart');
		$json = array();
		if (!isset($this->session->data['api_id'])) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {

			if (isset($this->request->post['product'])) {
				$this->cart->clear();

				foreach ($this->request->post['product'] as $product) {
					if (isset($product['option'])) {
						$option = $product['option'];
					} else {
						$option = array();
					}
					//echo $product['comment'];
					$this->backendcart->add($product['product_id'], $product['quantity'], $option, $product['piece_in_set'], $product['comment'], $product['tax']);
				}
			}

			if (isset($this->request->post['product_id'])) {
				$this->load->model('catalog/product');

					$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);
					
				if ($product_info) {
					if (isset($this->request->post['quantity'])) {
						$quantity = $this->request->post['quantity'];
					} else {
						$quantity = 1;
					}
					If(isset($this->request->post['check_hide'])) {
							//isset and array_filter($this->request->post['set-size']);
							if ($this->request->post['set-size'][0] != '') {
								$this->setBreak($this->request->post['break-quantity'], $this->request->post['product_id'], $this->request->post['set-size'], $this->request->post['selling-price']);
							}else{
								unset($this->session->data['newcart'][$this->request->post['product_id']]);
							}

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

					if (!isset($json['error']['option'])) {
						$this->session->data['check_for_cart'] = 1;
						$this->cart->add($this->request->post['product_id'], $quantity, $option);

						$json['success'] = $this->language->get('text_success');

						unset($this->session->data['shipping_method']);
						unset($this->session->data['shipping_methods']);
						unset($this->session->data['payment_method']);
						unset($this->session->data['payment_methods']);
					}
				} else {
					$json['error']['store'] = $this->language->get('error_store');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		$this->load->language('api/cart');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->cart->update($this->request->post['key'], $this->request->post['quantity']);

			$json['success'] = $this->language->get('text_success');

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function remove() {
		$this->load->language('api/cart');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Remove
			if (isset($this->request->post['key'])) {
				$this->cart->remove($this->request->post['key']);

				unset($this->session->data['vouchers'][$this->request->post['key']]);

				$json['success'] = $this->language->get('text_success');

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['reward']);

			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function products() {
		$this->load->language('api/cart');
		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {
			// Stock
			if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$json['error']['stock'] = $this->language->get('error_stock');
			}
			// Products
			$json['products'] = array();

			$products = $this->backendcart->getProducts();
			
			$total_pieces = 0;
			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

                //print_r($this->session->data); die;
				$set_price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
				$comment = '';
				if($product['comment'] != ''){
					$comment = $product['comment'];
				}
				$product_pis = $product['piece_in_set'];
				$total_amount = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']) * $product['quantity']);
				if(isset($this->session->data['newcart'])) {
					foreach($this->session->data['newcart'] as $newcart){
					if ($product['product_id'] == $newcart['product_id']) {
						$product['price'] = $product['price_per_piece'];
						$product_pis = $newcart['quantity'];
						$comment = $newcart['comment'];
						$set_price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']) * $product_pis);
						$total_amount = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']) * $newcart['quantity'] * $product['quantity']);
					}
					}
				}

				if ($product['minimum'] > $product_total) {
					$json['error']['minimum'][] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}

				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				} else {
					$image = '';
				}

				$option_data = array();
				//print_r($product['option']); die;
				foreach ($product['option'] as $option) {
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option['value'],
						'type'                    => $option['type']
					);
				}

				$total_pieces += (int)($product['piece_in_set'] * $product['quantity']);
				$json['products'][] = array(
					'key'        => $product['key'],
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'], 
                    'sku'        => $product['sku'], 
                    'hsn_code'   => $product['hsn_code'],
					'option'     => $option_data,
					'quantity'   => $product['quantity'],
					'price_per_piece' => $this->currency->format($product['price_per_piece']), 
                    'transfer_price' => $product['transfer_price'], 
					'stock'      => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'shipping'   => $product['shipping'],
					'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp'])),
					'total'      => $total_amount,
					'image'      => $image,
					'comment'    => html_entity_decode($comment),
					'tax'       => $this->currency->format($this->tax->getTax($product['total'], $product['hsn_code'], '', '', $product['mrp'])),
					'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id']),
					'set_price'  => $set_price,
					'piece_in_set' => $product_pis
				);

			}
			$data['total_pieces'] = $total_pieces;
			// Voucher
			$json['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$json['vouchers'][] = array(
						'code'             => $voucher['code'],
						'description'      => $voucher['description'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $this->currency->format($voucher['amount'])
					);
				}
			}

			// CST Calculation
			$CST_CLASS_ID = 12;
			$cst = $this->cart->getCST($CST_CLASS_ID); // CST tax class id is 12
			$taxes = $this->backendcart->getTaxes();
			// Totals
			$this->load->model('extension/extension');

			$total_data = array();
			$total = 0;

			//echo "<pre>"; print_r($taxes);
			$sort_order = array();

			$results = $this->model_extension_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);
			//print_r($results);
			$backend_subtotal = 1;
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes, $cst, $CST_CLASS_ID, $backend_subtotal);

				}
			}

			$sort_order = array();

			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $total_data);

			$json['totals'] = array();
			$tax_in_totals = false;
			//print_r($total_data); die;

			$cst_text = "CST (2%)";
			$cst_refund = "Refundable amount on Form C submission";
			$cst_value  = '0.00';
			$tax_refund = '0.00';
			foreach ($total_data as $total) {

				if ($total['code'] == 'subtotal') {
					$total['title'] = $this->language->get('text_subtotal');
				} elseif ($total['code'] == 'total') {
					$total['title'] = $this->language->get('text_total_amount');
					$cartTotal = $total['value'];
				} elseif ($total['code'] == 'credit') {
					$store_credit = $total['value'];
				} elseif ($total['code'] == 'tax') {
					$this->session->data['cst'] = ceil($cst);
					$this->session->data['tax_refund'] = ceil($total['value']) - ceil($cst); // Refund will be total tax - cst value
					$cst_value = $this->currency->format( ceil($cst) );
					$tax_refund = $this->currency->format( ceil($total['value']) - ceil($cst) );  // Refund will be total tax - cst value
				}

				/**Validate discount on free shipping (Ravindra Singh 05-02-2016) Start**/
				if(($total['code'] == "shipping" && $total['code'] == "0") || $total['title'] == "Free Shipping"){
					$freeshipflag = 1;
				}
				/**Validate discount on free shipping (Ravindra Singh 05-02-2016) End**/

				$json['totals'][] = array(
					'code'  => $total['code'],
					'title' => $total['title'],
					'text'  => $this->currency->format(ROUND($total['value'], 2)),
					'cst'   => $cst_text,
					'cst_refund' => $cst_refund,
					'cst_value'  => $cst_value,
					'text_refund' => $tax_refund
				);

				//print_r($json['totals']);

			}
		}
		//echo "<pre>"; print_r($json); die;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function setBreak($quantity = array(), $product_id = '', $set_size = array(), $selling_price = ''){
		//echo $quantity.$product_id;
		//print_r($quantity); die;
		$new_quantity = array_sum($quantity);
		$filter_size = array_filter($set_size);
		$set_count = count($filter_size);
	//	print_r($filter_size);
		$total = $set_count*$new_quantity*$selling_price;
		//echo $total;
		$comment = 'Total Pieces => ' ;
		//echo $comment;
		for($i=0; $i<=$set_count-1; $i++){
			if(is_numeric($filter_size[$i])) {
				$comment .= $quantity[$i] . ' of ' . $filter_size[$i] . ' size' . '. ';
			}else{
				$comment .= $quantity[$i] . ' of ' . $filter_size[$i] . ' color' . '. ';
			}
		}
		//$comment .= '.';
	//	echo $comment;
		$both = array(
			'comment' => $comment,
			'piece_in_set' => $set_count,
			'product_id' => $product_id,
			'new_total'  => $total,
			'quantity' => $new_quantity,
			'price'    => $selling_price
		);
		//print_r($both); die;
		//unset($this->session->data['newcart']);
		$this->session->data['newcart'][$product_id] = $both;
		//print_r($this->session->data['newcart']);
	}

}
