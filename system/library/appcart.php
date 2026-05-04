<?php
class AppCart {
	private $config;
	private $db;
	private $data = array();

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');

		/*if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
			$this->session->data['cart'] = array();
		}*/
	}

	public function getProducts($user_id = '') { 
		$cartdata = array();
		if(!empty($user_id)){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->db->escape($user_id) . "'");
		}else{
			$user_id = $this->customer->getId();
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->db->escape($user_id) . "'");
		}
		if (!$this->data && !empty($cartdata)) {
			foreach ($cartdata as $key => $quantity) {
				$product = unserialize(base64_decode($key));

				$product_id = $product['product_id'];

				$stock = true;

				// Options
				if (!empty($product['option'])) {
					$options = $product['option'];
				} else {
					$options = array();
				}

				// Profile
				if (!empty($product['recurring_id'])) {
					$recurring_id = $product['recurring_id'];
				} else {
					$recurring_id = 0;
				}

				$product_query = $this->db->query("SELECT pd.*,p.*,mp.seller_id FROM " . DB_PREFIX . "product p 
                                                   LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
                                                   INNER JOIN " . DB_PREFIX . "ms_product mp ON (p.product_id = mp.product_id) 
                                                   INNER JOIN " . DB_PREFIX . "ms_seller ms ON (ms.seller_id = mp.seller_id) 
                                                   WHERE p.product_id = '" . (int)$product_id . "' 
                                                     AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                                                     AND p.date_available <= NOW() 
                                                     AND p.status = '1' 
                                                     AND ms.vacation_mode ='0'"); 

				if ($product_query->num_rows) {
					$option_price = 0;
					$option_points = 0;
					$option_weight = 0;

					$option_data = array();

					foreach ($options as $product_option_id => $value) {
						$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
						
						if ($option_query->num_rows) {
							if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $value,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
								foreach ($value as $product_option_value_id) {
									$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

									if ($option_value_query->num_rows) {
										if ($option_value_query->row['price_prefix'] == '+') {
											$option_price += $option_value_query->row['price'];
										} elseif ($option_value_query->row['price_prefix'] == '-') {
											$option_price -= $option_value_query->row['price'];
										}

										if ($option_value_query->row['points_prefix'] == '+') {
											$option_points += $option_value_query->row['points'];
										} elseif ($option_value_query->row['points_prefix'] == '-') {
											$option_points -= $option_value_query->row['points'];
										}

										if ($option_value_query->row['weight_prefix'] == '+') {
											$option_weight += $option_value_query->row['weight'];
										} elseif ($option_value_query->row['weight_prefix'] == '-') {
											$option_weight -= $option_value_query->row['weight'];
										}

										if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
											$stock = false;
										}

										$option_data[] = array(
											'product_option_id'       => $product_option_id,
											'product_option_value_id' => $product_option_value_id,
											'option_id'               => $option_query->row['option_id'],
											'option_value_id'         => $option_value_query->row['option_value_id'],
											'name'                    => $option_query->row['name'],
											'value'                   => $option_value_query->row['name'],
											'type'                    => $option_query->row['type'],
											'quantity'                => $option_value_query->row['quantity'],
											'subtract'                => $option_value_query->row['subtract'],
											'price'                   => $option_value_query->row['price'],
											'price_prefix'            => $option_value_query->row['price_prefix'],
											'points'                  => $option_value_query->row['points'],
											'points_prefix'           => $option_value_query->row['points_prefix'],
											'weight'                  => $option_value_query->row['weight'],
											'weight_prefix'           => $option_value_query->row['weight_prefix']
										);
									}
								}
							} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => '',
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => '',
									'name'                    => $option_query->row['name'],
									'value'                   => $value,
									'type'                    => $option_query->row['type'],
									'quantity'                => '',
									'subtract'                => '',
									'price'                   => '',
									'price_prefix'            => '',
									'points'                  => '',
									'points_prefix'           => '',
									'weight'                  => '',
									'weight_prefix'           => ''
								);
							}
						}
					}

					$price = $product_query->row['price'];
					$commission = $product_query->row['commission'];

					if($this->config->get('config_store_id') == SOR_STORE_ID){
						$custom_price = $this->wsb->getStorePrice($product_query->row['product_id'],$price,$this->config->get('config_store_id'), $product_query->row['seller_id']);
						//print_r($custom_price);
						if($custom_price['price'] > 0) {
							$price = $custom_price['price'];
						}
						if($custom_price['commission']> 0) {
							$commission = $custom_price['commission'];
						}

					}

                    $piece_in_set = (int)$product_query->row['piece_in_set'] > 1 ? (int)$product_query->row['piece_in_set'] : 1;
                    $seller_tax_factor = 1.0 + ( (float)$product_query->row['seller_tax'] / 100.0 );
                    $commission_factor = 1.0 + ( (float)$commission / 100.0 );


					if($this->config->get('config_store_id') != SOR_STORE_ID) {
						// Product Discounts
						$discount_quantity = 0;

						foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
							$product_2 = (array)unserialize(base64_decode($key_2));

							if ($product_2['product_id'] == $product_id) {
								$discount_quantity += $quantity_2;
							}
						}

						$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

						if ($product_discount_query->num_rows) {
							$price = (float)($product_discount_query->row['price']);
						}
					}

					// Product Specials
					$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

					if ($product_special_query->num_rows) {
						$price = (float)($product_special_query->row['price']);
					}

					// Downloads
					$download_data = array();

					$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$product_id . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					foreach ($download_query->rows as $download) {
						$download_data[] = array(
							'download_id' => $download['download_id'],
							'name'        => $download['name'],
							'filename'    => $download['filename'],
							'mask'        => $download['mask']
						);
					}

					// Stock
					if ( !$product_query->row['quantity'] 
                          or ($product_query->row['quantity'] < $quantity) 
                          or ($product_query->row['stock_status_id'] == 5) ) {
						$stock = false;
					}

					$recurring_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring` `p` JOIN `" . DB_PREFIX . "product_recurring` `pp` ON `pp`.`recurring_id` = `p`.`recurring_id` AND `pp`.`product_id` = " . (int)$product_query->row['product_id'] . " JOIN `" . DB_PREFIX . "recurring_description` `pd` ON `pd`.`recurring_id` = `p`.`recurring_id` AND `pd`.`language_id` = " . (int)$this->config->get('config_language_id') . " WHERE `pp`.`recurring_id` = " . (int)$recurring_id . " AND `status` = 1");

					if ($recurring_query->num_rows) {
						$recurring = array(
							'recurring_id'    => $recurring_id,
							'name'            => $recurring_query->row['name'],
							'frequency'       => $recurring_query->row['frequency'],
							'price'           => $recurring_query->row['price'],
							'cycle'           => $recurring_query->row['cycle'],
							'duration'        => $recurring_query->row['duration'],
							'trial'           => $recurring_query->row['trial_status'],
							'trial_frequency' => $recurring_query->row['trial_frequency'],
							'trial_price'     => $recurring_query->row['trial_price'],
							'trial_cycle'     => $recurring_query->row['trial_cycle'],
							'trial_duration'  => $recurring_query->row['trial_duration']
						);
					} else {
						$recurring = false;
					}
					//	print_r($this->session->data); die;
					if(isset($this->session->data['newcart'])){
						foreach($this->session->data['newcart'] as $newcart) {
							if ($product['product_id'] == $newcart['product_id']) {
								$quantity = $newcart['quantity'];
							}
							if ($product['product_id'] == $newcart['product_id']) {
								$piece_in_set = 1; // Update this with session value
							}
						}
					}


					$this->data[] = array(
						'key'             => $key,
						'seller_id'		  => $product_query->row['seller_id'],
						'product_id'      => $product_query->row['product_id'],
						'name'            => $product_query->row['name'],
						'model'           => $product_query->row['model'],
                        'sku'             => $product_query->row['sku'],
                        'set_description' => $product_query->row['set_description'],
						'shipping'        => $product_query->row['shipping'],
						'image'           => $product_query->row['image'],
						'option'          => $option_data,
						'download'        => $download_data,
						'hsn_code'        => $product_query->row['hsn_code'],
						'quantity'        => $quantity,
                        'piece_in_set'    => $piece_in_set,
                        'total_pieces'    => $piece_in_set*$quantity,
						'stock_quantity'  => $product_query->row['quantity'],
						'minimum'         => $product_query->row['minimum'],
						'subtract'        => $product_query->row['subtract'],
						'stock'           => $stock,
						'price'           => (ceil($price* $commission_factor / $seller_tax_factor) + ceil($option_price * $commission_factor / $seller_tax_factor))* $piece_in_set,
                        'price_per_piece' => ceil($price* $commission_factor / $seller_tax_factor) + ceil($option_price * $commission_factor / $seller_tax_factor),
						'total'           => (ceil($price* $commission_factor / $seller_tax_factor) + ceil($option_price * $commission_factor / $seller_tax_factor))* $piece_in_set*$quantity,
						'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
						'tax_class_id'    => $product_query->row['tax_class_id'],
						'weight'          => ($product_query->row['weight'] + $option_weight * $piece_in_set) * $quantity,
						'weight_class_id' => $product_query->row['weight_class_id'],
						'length'          => $product_query->row['length'],
						'width'           => $product_query->row['width'],
						'height'          => $product_query->row['height'],
						'length_class_id' => $product_query->row['length_class_id'],
						'recurring'       => $recurring,
						'seller_id'       => $product_query->row['seller_id'],
						'selling_price'   => $product_query->row['selling_price'],
						'image'           => $product_query->row['image'],
                        'seller_tax'      => $product_query->row['seller_tax'], 
                        'commission'      => $product_query->row['commission']
					);
					 //echo "<pre>"; print_r($this->data); exit;
					// echo "<br>"; print ($option_price) ;
				} else {
					$this->remove($key);
				}
			}
		}

		return $this->data;
	}

	public function getRecurringProducts($user_id) {
		$recurring_products = array();

		foreach ($this->getProducts($user_id) as $key => $value) {
			if ($value['recurring']) {
				$recurring_products[$key] = $value;
			}
		}

		return $recurring_products;
	}

	public function add($product_id, $qty = 1, $option = array(), $recurring_id = 0) {
		$this->data = array();

		$product['product_id'] = (int)$product_id;

		if ($option) {
			$product['option'] = $option;
		}

		if ($recurring_id) {
			$product['recurring_id'] = (int)$recurring_id;
		}

		$key = base64_encode(serialize($product)); 

		if ((int)$qty && ((int)$qty > 0)) {
			if (!isset($this->session->data['cart'][$key])) {
				$this->session->data['cart'][$key] = (int)$qty;
			} else {
				$this->session->data['cart'][$key] += (int)$qty;
			}
		}
	}

	public function update($key, $qty) {
		$this->data = array();

		if ((int)$qty && ((int)$qty > 0) && isset($this->session->data['cart'][$key])) {
			$this->session->data['cart'][$key] = (int)$qty;
		} else {
			$this->remove($key);
		}
	}

	public function remove($key) {
		$this->data = array();

		unset($this->session->data['cart'][$key]);
	}

	public function clear() {
		$this->data = array();

		$this->session->data['cart'] = array();
	}

	public function getWeight($user_id) {
		$weight = 0;

		foreach ($this->getProducts($user_id) as $product) {
			if ($product['shipping']) {
                $piece_in_set = (int)$product['piece_in_set'] > 1 ? (int)$product['piece_in_set'] : 1;
				$weight += $this->weight->convert($product['weight']*$piece_in_set, $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}

	public function getSubTotal($user_id = '') {
		$total = 0;
		foreach ($this->getProducts($user_id) as $product) {
			$total += $product['total'];
		}

		return $total;
	}
	
	/** Deal Of teh day code (Ravindra Singh 22-01-2016) Start**/
	public function getSubTotalAccordingSeller($seller_id = 0) {
		$total = 0;
		foreach ($this->getProducts() as $product) {
			//if(in_array($product['seller_id'],$sellers)){
			if($product['seller_id'] == $seller_id){
				$total += $product['total'];	
			}
		}

		return $total;
	}
	/** Deal Of teh day code (Ravindra Singh 22-01-2016) End**/

	public function getTaxes($user_id) {
		$tax_data = array();

		foreach ($this->getProducts($user_id) as $product) {
			if ($product['hsn_code']) {
				$tax_rates = $this->tax->getRates($product['price'], $product['hsn_code']);
				
				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getCST($cst_class_id) {
		$cst = 0.0;

		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
				$cst += ( $product['quantity'] * $this->tax->getCST($product['price'], $product['tax_class_id'], $cst_class_id) );
			}
		}

		return $cst;
	}
	
	public function getTotal($user_id) {
		$total = 0;

		foreach ($this->getProducts($user_id) as $product) {
			$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']) * $product['quantity'];
		}

		return $total;
	}

	public function countProducts($user_id) {
		$product_total = 0;

		$products = $this->getProducts($user_id);

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts($user_id) {
		//return count($this->session->data['cart']);
		$cartdata = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->db->escape($user_id) . "'");
		if(isset($query->row['cart'])){
			$cartdata = unserialize($query->row['cart']);
		}
		return count($cartdata);
	}

	public function hasRecurringProducts($user_id) {
		return count($this->getRecurringProducts($user_id));
	}

	public function hasStock($user_id) {
		$stock = true;

		foreach ($this->getProducts($user_id) as $product) {
			if (!$product['stock']) {
				$stock = false;
			}
		}

		return $stock;
	}

	public function hasShipping($user_id) {
		$shipping = false;

		foreach ($this->getProducts($user_id) as $product) {
			if ($product['shipping']) {
				$shipping = true;

				break;
			}
		}

		return $shipping;
	}

	public function hasDownload($user_id) {
		$download = false;

		foreach ($this->getProducts($user_id) as $product) {
			if ($product['download']) {
				$download = true;

				break;
			}
		}

		return $download;
	}



	/*
	 * get seller id for deal of the day by vikas (21-01-2016)
	 *
	 *
	public function getSellerID(){
		$sql = "SELECT DISTINCT dd.seller_id FROM " .DB_PREFIX."deal_of_day AS dd WHERE start_date LIKE '%".date('Y-m-d')."%'";
		$query = $this->db->query($sql);
		//echo "<pre>"; print_r($query->rows); echo "</pre>";die;
		return $query->rows;
	}

	/*
	 * get discount and order amount for deal of the day by vikas (21-01-2016)
	 *
	 *
	public function getDiscountAmount($sellerID){
		$sql = "SELECT dd.order_amount,dd.discount FROM " .DB_PREFIX."deal_of_day AS dd WHERE seller_id = '".$sellerID."'";
		$query = $this->db->query($sql);
		//echo "<pre>"; print_r($query->rows); echo "</pre>";die;
		return $query->rows;
	}
	*/
	
	public function is_dropshipper($user_id){
		$rt = 0;
		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$user_id . "' AND status = '1'");
		if(isset($customer_query->row['is_dropshipper'])){
			$rt = $customer_query->row['is_dropshipper'];
		}
		return $rt;
	}
	
	public function getBalance($user_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$user_id . "'");

		return $query->row['total'];
	}
    
    public function getCashbackAvailable($user_id) {
		$query = $this->db->query("SELECT SUM(amount - amount_utilized) AS total FROM " . DB_PREFIX . "customer_cashback 
                                   WHERE customer_id = '" . (int)$user_id . "' 
                                     AND expired = 0 
                                     AND amount > 0 
                                     AND (amount - amount_utilized) > 0");

		return $query->row['total'];
	}
}
