<?php
class ControllerModuleLatestAsCategory extends Controller {
	public function index($setting) {
		$this->load->language('module/latest');
        $this->load->model('catalog/category');

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
        $category_id = '';
        if(isset($this->request->request['category_id'])){
            $category_id = $this->request->request['category_id'];
        }
        $cat_info = $this->model_catalog_category->getCategory($category_id);

        $data['heading_title'] = $cat_info['name'];
        //echo $category_id;
        $data['category_id'] = $category_id;
        $data['href'] = $this->url->link('product/category', 'path=' . $category_id);
        $filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => 10, //$setting['limit'],
			'custom_store' 	=> $custom_store,
            'filter_category_id' => $category_id
		);

        $setting = array(
            'width' => 190,
            'height' => 283
        );


		$this->load->model('setting/setting');

		if(SOLR_ENABLED && SOLR_WSBOX_ENABLED){
			//$this->load->model('solr/product');
			$solr = new SolrProduct($this);
			$results_solr = $solr->getProductFromSolr($filter_data);
			$results = $results_solr['products'];

		}else{
			$results = $this->model_catalog_product->getInStockProducts($filter_data);
		}
        //echo "<pre>"; print_r($results);


		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);

				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);

				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					//$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))); // Default
					$price = $this->currency->format($this->tax->calculate($result['selling_price'], $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp'])); // Add Selling price (05-01-2015) Ravindra Singh
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
                    'amp_width'	=>  $setting['width'],
                    'amp_height' =>$setting['height']
				);
			}

          if(isset($this->request->get['amp']) && $this->request->get['amp'] == 1){ 
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/latest_as_category-amp.tpl')) {
					echo $this->load->view($this->config->get('config_template') . '/template/module/latest_as_category-amp.tpl', $data);
					} else {
					return $this->load->view('default/template/module/latest_as_category-amp.tpl', $data);
					}
			}else{
							if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/latest_as_category.tpl')) {
						echo $this->load->view($this->config->get('config_template') . '/template/module/latest_as_category.tpl', $data);
					} else {
			            return $this->load->view('default/template/module/latest_as_category.tpl', $data);
					}
		}
	}
}
}
