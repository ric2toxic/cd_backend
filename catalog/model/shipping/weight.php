<?php
class ModelShippingWeight extends Model {
	public function getQuote($address, $weight_cart = 0, $free_shipping_enabled = true) {

		$this->load->language('shipping/weight');

		$quote_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");

        $coupon_data = $this->cart->getCoupon();

		foreach ($query->rows as $result) {
			if ($this->config->get('weight_' . $result['geo_zone_id'] . '_status')) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')"); // Default
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE (geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0') OR country_id = '0')"); // Add Default shipping
				//echo "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE (geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0') OR country_id = '0')"; exit;
				if ($query->num_rows) {
					$status = true;
				} else {
					$status = false;
				}
			} else {
				$status = false;
			}

			if ($status) {
				$cost = 0;

                if(isset($address['from_backend'])){
                    $weight = ceil($weight_cart);
                }else{
                    $weight = ceil($this->cart->getWeight());
                }

                $calculate_shipping = true;

                if((float)$weight == 0.0)
                {
                    $cost = 0;
                    $calculate_shipping = false;
                }
                else if( !empty($coupon_data['wsb_store_delivery'])
                    && in_array($result['geo_zone_id'],array('5','48','49') ) ) {
                    $pickup_city_map = $this->cart->getPickUpCityMap();
                    if (strpos(strtoupper($address['city']), strtoupper($pickup_city_map[$coupon_data['wsb_store_code']])) !== false) {
                        $cost = 100;
                        $calculate_shipping = false;
                    } else if (($coupon_data['wsb_store_code'] == 'BLR' || $coupon_data['wsb_store_code'] == 'BL') && strtoupper($address['city']) == 'BENGALURU') {
                        $cost = 100;
                        $calculate_shipping = false;
                    }
                }

                if ($calculate_shipping) {
                    if (null !== $this->config->get('weight_' . $result['geo_zone_id'] . '_check')
                        && $this->config->get('weight_' . $result['geo_zone_id'] . '_check') == 0 )
                    {
                        $rates = $this->config->get('weight_' . $result['geo_zone_id'] . '_meta');


                        $weight_for_cost = array();
                        foreach ($rates['cost_by_weight_range'] as $key => $row)
                        {
                            $weight_for_cost[$key] = $row['weight'];
                        }

                        array_multisort($weight_for_cost, SORT_ASC, $rates['cost_by_weight_range']);

                        $found_correct_slab = false;
                        $final_weight = 0;
                        foreach ( $rates['cost_by_weight_range'] as $key => $rate ) {
                            $final_weight = $rate['weight'];
                            if ($found_correct_slab)
                                break;

                            $low = isset($rates['cost_by_weight_range'][$key-1]['weight']) ? $rates['cost_by_weight_range'][$key-1]['weight'] : 0;
                            if ($weight <= $rate['weight']) {
                                // found correct slab
                                $found_correct_slab = true;
                                $high = $weight;
                            } else {
                                $high = $rate['weight'];
                            }

                            $cost += ($high - $low) * $rate['cost'];
                        }

                        if (!$found_correct_slab) {
                            $cost += ($weight - $final_weight) * $rates['cost_after_range_finish'];
                        }

                        if ($cost < $rates['minimum_cost']) {
                            $cost = $rates['minimum_cost'];
                        }

                    } else {
                        $rates = explode(',', $this->config->get('weight_' . $result['geo_zone_id'] . '_rate'));
                        $cost_for_last_weight = 0;
                        $last_weight = 0;
                        foreach ($rates as $rate) {
                            $data = explode(':', $rate);

                            if ($data[0] >= $weight) {
                                if (isset($data[1])) {
                                    $cost = $data[1];
                                }

                                break;
                            } else {
                                $cost_for_last_weight = $data[1];
                                $last_weight = $data[0];
                            }
                        }

                        if($cost == 0) {
                            $cost_after_sting_range = ($weight - $last_weight)*$this->config->get('weight_' . $result['geo_zone_id'] . '_cost_after_string_range');
                            $cost = $cost_after_sting_range + $cost_for_last_weight;
                        }
                    }
                }

                $cost = 10*ceil($cost/10);

                $free_shipping = $this->config->get('weight_' . $result['geo_zone_id'] . '_free_shipping') ?? 0;
                
                // if ( $free_shipping_enabled && $free_shipping ) {
                // 	$cost = 0;
                // }

                $text = $this->currency->format( $this->tax->calculate( $cost, $this->config->get('weight_tax_class_id'), $this->config->get('config_tax')));

				if ((string)$cost != '') {

					$quote_data_temp = array(
											'code' => 'weight.weight_' . $result['geo_zone_id'],
											'title' => $result['name'],
											'description' => $result['description'] /*. '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class_id')) . ')' */,
											'cost' => $cost,
											'tax_class_id' => $this->config->get('weight_tax_class_id'),
											'text' => $text,
											'free_shipping' => $free_shipping
										);

					// if( $free_shipping_enabled && $free_shipping ) {

					// 	$quote_data = array( 'weight_' . $result['geo_zone_id'] => $quote_data_temp ) + $quote_data;

					// } else {

						$quote_data['weight_' . $result['geo_zone_id']] = $quote_data_temp;
					//}

					
				}
			}
		}
		
		$method_data = array();

		if ($quote_data) {
			$method_data = array(
				'code'       => 'weight',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('weight_sort_order'),
				'error'      => false
			);
		}
		
		return $method_data;
	}

	public function getAppQuote($address,$user_id) {
		$this->load->language('shipping/weight');
		//echo "<pre>"; print_r($address); exit;
		$quote_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");

		foreach ($query->rows as $result) {
			if ($this->config->get('weight_' . $result['geo_zone_id'] . '_status')) {
				//echo "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE (geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0') OR country_id = '0')"; exit;
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')"); // Default
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE (geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0') OR country_id = '0')"); // Add Default shipping
				//echo "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE (geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0') OR country_id = '0')"; exit;
				if ($query->num_rows) {
					$status = true;
				} else {
					$status = false;
				}
			} else {
				$status = false;
			}

			if ($status) {
				$cost = 0;
				$weight = ceil($this->cart->getWeight($user_id));

				if (null !== $this->config->get('weight_' . $result['geo_zone_id'] . '_check')
					&& $this->config->get('weight_' . $result['geo_zone_id'] . '_check') == 0) {

					$rates = $this->config->get('weight_' . $result['geo_zone_id'] . '_meta');


					$weight_for_cost = array();
					foreach ($rates['cost_by_weight_range'] as $key => $row)
					{
						$weight_for_cost[$key] = $row['weight'];
					}

					array_multisort($weight_for_cost, SORT_ASC, $rates['cost_by_weight_range']);

					$found_correct_slab = false;
					$final_weight = 0;
					foreach ( $rates['cost_by_weight_range'] as $key => $rate ) {
						$final_weight = $rate['weight'];
						if ($found_correct_slab)
							break;

						$low = isset($rates['cost_by_weight_range'][$key-1]['weight']) ? $rates['cost_by_weight_range'][$key-1]['weight'] : 0;
						if ($weight <= $rate['weight']) {
							// found correct slab
							$found_correct_slab = true;
							$high = $weight;
						} else {
							$high = $rate['weight'];
						}

						$cost += ($high - $low) * $rate['cost'];
					}

					if (!$found_correct_slab) {
						$cost += ($weight - $final_weight) * $rates['cost_after_range_finish'];
					}

					if ($cost < $rates['minimum_cost']) {
						$cost = $rates['minimum_cost'];
					}

				} else {
					$rates = explode(',', $this->config->get('weight_' . $result['geo_zone_id'] . '_rate'));
					$cost_for_last_weight = 0;
					$last_weight = 0;
					foreach ($rates as $rate) {
						$data = explode(':', $rate);

						if ($data[0] >= $weight) {
							if (isset($data[1])) {
								$cost = $data[1];
							}

							break;
						} else {
							$cost_for_last_weight = $data[1];
							$last_weight = $data[0];
						}
					}

					if($cost == 0) {
						$cost_after_sting_range = ($weight - $last_weight)*$this->config->get('weight_' . $result['geo_zone_id'] . '_cost_after_string_range');
						$cost = $cost_after_sting_range + $cost_for_last_weight;
					}
				}

                $this->load->model('account/customer');
                $is_dropshipper = $this->model_account_customer->getisdropshipper($user_id);

                if ( $is_dropshipper != 1 && $result['geo_zone_id'] == 8) {
                    //skip "Dropship by air" method for non dropshippers
                    continue;
                }

				if ((string)$cost != '') {
					$quote_data['weight_' . $result['geo_zone_id']] = array(
						'code'         => 'weight.weight_' . $result['geo_zone_id'],
						'title'        => $result['name'] /*. '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class_id')) . ')' */,
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('weight_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('weight_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}
		}

		$method_data = array();

		if ($quote_data) {
			$method_data = array(
				'code'       => 'weight',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('weight_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}

}
