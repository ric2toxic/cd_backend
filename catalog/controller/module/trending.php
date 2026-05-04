<?php
class ControllerModuleTrending extends Controller {
	public function index($setting) {
		$this->load->language('module/trending');

        $setting = array(
            'width' => 190,
            'height' => 283,
            'limit' => 10
        );

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['detail_view'] = $this->language->get('detail_view');


		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		//////////////////////////////////////////////////
		//Showing Single store items on wholesale store //
		//////////////////////////////////////////////////

		if (isset($this->session->data['custom_store']) && $this->session->data['custom_store'] != '') {
			$custom_store = $this->session->data['custom_store'];
			$data['custom_store'] = $this->session->data['custom_store'];
		} else {
			$data['custom_store'] = 'set';
			$custom_store ='set';
		}

		$data['products'] = array();

		$filter_data = array(
			'sort_data_by' 		=> 'hotness_value',
			'order_data_by' 	=> 'Desc',
			'custom_store' 		=> $custom_store,
			'days'				=> 7,
			'is_trending' 		=> 1,
			'filter_limit'  	=> 50
		);


		$this->load->model('setting/setting');


		if(SOLR_ENABLED && SOLR_WSBOX_ENABLED){
			//$this->load->model('solr/product');
			$solr = new SolrProduct($this);
			$results_solr = $solr->getProductFromSolr($filter_data);
			$results = $results_solr['products'];

			//echo "<pre>"; print_r($results); echo '</pre>';
		}else{
			$results = $this->model_catalog_product->getInStockProducts($filter_data);
		}

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}
                $seller_tax_factor = 1.0 + ( (float)$result['seller_tax'] / 100.0 );
                $commission_factor = 1.0 + ( (float)$result['commission'] / 100.0 );

                $unit_price =  ceil($commission_factor * (float)($result['price']) / $seller_tax_factor);
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					//$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))); // Default
					$price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp'])); // Add Selling price (05-01-2015) Ravindra Singh
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

                //## Remove wishlist functionality and icon from home page

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
					'amp_width'	=> $setting['width'],
					'amp_height' =>$setting['height']
				);
			}

			if(isset($this->request->get['amp']) && $this->request->get['amp'] == 1){
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/trending-amp.tpl')) {
					echo $this->load->view($this->config->get('config_template') . '/template/module/trending-amp.tpl', $data);
				} else {
					return $this->load->view('default/template/module/trending-amp.tpl', $data);
				}
			}else{
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/trending.tpl')) {
					return $this->load->view($this->config->get('config_template') . '/template/module/trending.tpl', $data);
				} else {
					return $this->load->view('default/template/module/trending.tpl', $data);
				}
			}
		}
	}
}
