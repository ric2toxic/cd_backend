<?php
class ControllerModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('module/featured');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');
        $data['text_inc_tax'] = $this->language->get('text_inc_tax');
        $data['text_moq_default'] = $this->language->get('text_moq_default');
        $data['text_moq_pre'] = $this->language->get('text_moq_pre');
        $data['text_moq_post'] = $this->language->get('text_moq_post');
        $data['text_per_piece'] = $this->language->get('text_per_piece');
        $data['text_per_set'] = $this->language->get('text_per_set');
        $data['text_plus_cst'] = $this->language->get('text_plus_cst');        

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}
                    
                $seller_tax_factor = 1.0 + ( (float)$product_info['seller_tax'] / 100.0 );
                $commission_factor = 1.0 + ( (float)$product_info['commission'] / 100.0 );
                
                $piece_in_set = (int)$product_info['piece_in_set'] > 1 ? (int)$product_info['piece_in_set'] : 1;
                $unit_price =  ceil($commission_factor * (float)($product_info['price']) / $seller_tax_factor);

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
					} else {
						$price = false;
					}
                    
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					    $price_per_set = $this->currency->format($this->tax->calculate($unit_price*$piece_in_set, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
                    } else {
					    $price_per_set = false;
				    }

					if ((float)$product_info['special']) {
                        $unit_special_price = ceil($commission_factor * (float)($product_info['special']) / $seller_tax_factor);
						$special = $this->currency->format($this->tax->calculate($unit_special_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
					} else {
						$special = false;
					}
                    
                    if ((float)$product_info['special']) {
                        $unit_special_price = ceil($commission_factor * (float)($product_info['special']) / $seller_tax_factor);
					    $special_per_set = $this->currency->format($this->tax->calculate($unit_special_price*$piece_in_set, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
				    } else {
					    $special_per_set = false;
				    }

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? ceil($commission_factor * (float)($product_info['special']) / $seller_tax_factor) : ceil($commission_factor * (float)($product_info['price']) / $seller_tax_factor));
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
                        'set_description' => $product_info['set_description'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
						'price'       => $price,
                        'price_per_set' => $price_per_set,
                        'piece_in_set'=> $piece_in_set,
						'special'     => $special,
                        'special_per_set'=> $special_per_set,
						'tax'         => $tax,
                        'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
						'rating'      => $rating,
						'text_in_stock'=>sprintf($this->language->get('text_in_stock'), $product_info['quantity']),
						'text_sold_out'=>sprintf($this->language->get('text_sold_out'), $product_info['sold_out']),
						'text_out_of_stock'=>$this->language->get('text_out_of_stock'),
						'quantity'=>(int)$product_info['quantity'],
						'stock_status'=>$product_info['stock_status'],
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		if ($data['products']) {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/featured.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/module/featured.tpl', $data);
			} else {
				return $this->load->view('default/template/module/featured.tpl', $data);
			}
		}
	}
}