<?php
class ControllerModuleBestSeller extends Controller {
	public function index($setting) {
		$this->load->language('module/bestseller');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['detail_view'] = $this->language->get('detail_view');

		$this->load->model('catalog/product');
		$seller_id = array();
	//	echo "<pre>"; print_r($seller_id); echo "</pre>";
	
		//////////////////////////////////////////
		//Showing bestseller products of sellers //
		//////////////////////////////////////////
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $this->config->get('config_store_id'));

		if (isset($store_info['config_seller_id'])) {
			$seller_id = array('seller' =>  $store_info['config_seller_id']);
		}

		//////////////////////////////////////////////////
		// End Best seller products show in store front //
		//////////////////////////////////////////////////

		$this->load->model('tool/image');

		$data['products'] = array();

		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit'],$seller_id);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					//$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))); //Default
					$price = $this->currency->format($this->tax->calculate($result['selling_price'], $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));// Add Selling price (05-01-2015) Ravindra Singh
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'set_description' => $result['set_description'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => sprintf($this->language->get('text_per_piece'), $price),
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'quantity'	  => $result['quantity'],
					'minimum'	  => $result['minimum'],
					'stock_status'=>$result['stock_status'],
					'product_options' => $this->model_catalog_product->getProductOptions($result['product_id']),
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
				);
			}

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/bestseller.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/module/bestseller.tpl', $data);
			} else {
				return $this->load->view('default/template/module/bestseller.tpl', $data);
			}
		}
	}
}
