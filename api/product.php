<?php
require_once __DIR__ . '/system.php';
require_once __DIR__ . '/data_packet.php';

require_once DIR_SYSTEM . 'library/document.php';
require_once DIR_SYSTEM . 'library/cart.php';
require_once DIR_SYSTEM . 'library/solr/product.php';
require_once DIR_SYSTEM . 'library/solr/model_solr_product.php';
require_once DIR_SYSTEM . 'library/wsb.php';

class ProductController extends SystemController {
	public function __construct($params) {

		parent::__construct($params);
		// Currency
		//$this->registry->set('currency', new Currency($this->registry));
		// Tax
		//$this->registry->set('tax', new Tax($this->registry));
		// Document
		$this->registry->set('document', new Document($this->registry));
		// Cart
		$this->registry->set('cart', new Cart($this->registry));
		// Customer
		//$this->registry->set('customer', new Customer($this->registry));
		// Wsb
		$this->registry->set('wsb', new Wsb($this->registry));
	}

	public function getProductDetails() {
		$product_id = $this->args['0'];
		$data['product_detail_id'] = $this->args['0'];
		$product_id = $this->url->rewrite_value($product_id);

		$this->load->model('catalog/product');
		$this->load->model('seller/product');
		$this->load->language('product/product');
		$has_single = false;

		$language = array();
		$data['product_info_schema'] = '';
		$data['category_info_schema'] = array();
		$data['subcategory_info_schema'] = array();
		$this->load->model('catalog/category');
		$data['downloadText'] = $this->language->get('downloadText');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		$data['seller_returnable'] = (int) ($product_info['non_returnable'] ?? 0);

		$parts = explode(',', $product_info['category_id']);
		$category_id = (int) array_pop($parts);
		$data['category_info'] = $this->model_catalog_category->getCategory($category_id);
		if (!empty($data['category_info']['parent_id'])) {
			$data['parent_category_info'] = $this->model_catalog_category->getCategory($data['category_info']['parent_id']);
		}

		$data['meta_title'] = $product_info['meta_title'];
		$data['meta_description'] = $product_info['meta_description'];
		$data['meta_keyword'] = $product_info['meta_keyword'];

		$alternate_product = $this->model_catalog_product->getAlternateProductInfo($product_info['is_single'], $product_info['model']);
		$data['custom_store_selling_price'] = $this->currency->format(isset($alternate_product['selling_price']) ? $alternate_product['selling_price'] : '');

		$data['custom_store_selling_price'] = sprintf($this->language->get('text_per_piece'), $data['custom_store_selling_price']);
		$data['custom_store_product_id'] = isset($alternate_product['product_id']) ? $alternate_product['product_id'] : '';

		$custom_url = '';
		if (isset($this->request->get['path'])) {
			$path = $this->request->get['path'];
		} else {
			$path = '';
		}

		$data['custom_store_product_href'] = "";
		if (!empty($data['custom_store_product_id'])) {
			$data['custom_store_product_href'] = $this->url->link('product/product', 'path=' . $path . '&product_id=' . $data['custom_store_product_id'] . $custom_url, 'SSL');
		}

		if ($has_single == false) {
			$is_single = $this->model_catalog_product->checkIfProductIsSingle($product_id);
		}

		$actualsold = 0;

		$data['last_category_href'] = '';
		// Getting category link (in case of multiple categories, it picks only the first one in the list)
		$data['last_category_href'] = $this->url->rewrite_keyword('category_id', (int) ($this->model_catalog_product->getCategory($product_id)), 'SSL');

		$data['heading_title'] = html_entity_decode($product_info['name']);
		$language['text_question'] = $this->language->get('text_question');
		$language['text_previously_ordered'] = $this->language->get('text_previously_ordered');
		$language['text_return'] = $this->language->get('text_select');
		$language['text_no_return'] = $this->language->get('text_no_return');
		$language['text_select'] = $this->language->get('text_select');
		$language['text_manufacturer'] = $this->language->get('text_manufacturer');
		$language['text_model'] = $this->language->get('text_model');
		$language['text_points'] = $this->language->get('text_points');
		$language['text_stock'] = $this->language->get('text_stock');
		$language['text_discount'] = $this->language->get('text_discount');
		$language['text_discount_post'] = $this->language->get('text_discount_post');
		$language['text_tax'] = $this->language->get('text_tax');
		$language['text_inc_tax'] = $this->language->get('text_inc_tax');
		$language['text_plus_cst'] = $this->language->get('text_plus_cst');
		$language['text_option'] = $this->language->get('text_option');
		$language['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
		$language['text_write'] = $this->language->get('text_write');
		$language['single_txt_write'] = $this->language->get('single_txt_write');
		$language['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
		$language['text_note'] = $this->language->get('text_note');
		$language['text_tags'] = $this->language->get('text_tags');
		$language['text_related'] = $this->language->get('text_related');
		$language['text_loading'] = $this->language->get('text_loading');
		$language['text_moq_default'] = $this->language->get('text_moq_default');
		$language['text_moq_pre'] = $this->language->get('text_moq_pre');
		$language['text_moq_post'] = $this->language->get('text_moq_post');
		$language['text_per_piece'] = $this->language->get('text_per_piece');
		$language['text_withoutslash_piece'] = $this->language->get('text_withoutslash_piece');
		$language['text_per_set'] = $this->language->get('text_per_set');
		$language['text_one_set'] = $this->language->get('text_one_set');
		$language['text_pieces'] = $this->language->get('text_pieces');
		$language['text_see_desc'] = $this->language->get('text_see_desc');
		$language['text_browse_more'] = $this->language->get('text_browse_more');
		$language['text_featured'] = $this->language->get('text_featured');
		$language['text_in_stock'] = sprintf($this->language->get('text_in_stock'), $product_info['quantity']);
		$language['text_sold_out'] = sprintf($this->language->get('text_sold_out'), $product_info['sold_out'] + $actualsold);
		$language['text_out_of_stock'] = $this->language->get('text_out_of_stock');
		$language['text_available_set'] = $this->language->get('text_available_set');
		$language['text_available_pieces'] = $this->language->get('text_available_pieces');
		$language['text_available_after'] = $this->language->get('text_available_after');
		$language['text_days'] = $this->language->get('text_days');

		$language['entry_qty'] = $this->language->get('entry_qty');
		$language['entry_price'] = $this->language->get('entry_price');
		$language['entry_name'] = $this->language->get('entry_name');
		$language['entry_review'] = $this->language->get('entry_review');
		$language['entry_rating'] = $this->language->get('entry_rating');
		$language['entry_good'] = $this->language->get('entry_good');
		$language['entry_bad'] = $this->language->get('entry_bad');
		$language['text_return'] = $this->language->get('text_return');
		$language['text_no_return'] = $this->language->get('text_no_return');
		$language['text_single'] = $this->language->get('text_single');
		$language['text_wholesaleBox'] = $this->language->get('text_wholesaleBox');
		$language['text_recently_viewed'] = $this->language->get('text_recently_viewed');
		$language['text_cod_available'] = $this->language->get('text_cod_available');

		$language['button_cart'] = $this->language->get('button_cart');
		$language['button_wishlist'] = $this->language->get('button_wishlist');
		$language['button_compare'] = $this->language->get('button_compare');
		$language['button_upload'] = $this->language->get('button_upload');
		$language['button_continue'] = $this->language->get('button_continue');
		$language['shipping_charges_txt'] = $this->language->get('shipping_charges_txt');
		$language['delivery_txt'] = $this->language->get('delivery_txt');
		$language['dispatch_txt'] = $this->language->get('dispatch_txt');
		$language['courier_txt'] = $this->language->get('courier_txt');
		$language['courier_txt_1'] = $this->language->get('courier_txt_1');
		$language['courier_txt_2'] = $this->language->get('courier_txt_2');
		$language['surface_txt'] = $this->language->get('surface_txt');
		$language['surface_txt_1'] = $this->language->get('surface_txt_1');
		$language['return_policy_txt'] = $this->language->get('return_policy_txt');
		$language['more_information_txt'] = $this->language->get('more_information_txt');

		$this->load->model('catalog/review');

		$language['tab_description'] = $this->language->get('tab_description');
		$language['tab_attribute'] = $this->language->get('tab_attribute');
		$language['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

		$data['review_count'] = $product_info['reviews'];
		$data['category_id'] = $product_info['category_id'];
		$data['seller_id'] = $product_info['seller_id'];
		$data['seller_nickname'] = $product_info['seller_nickname'];
		$data['href'] = $this->url->link('product/product', 'product_id=' . (int) $product_id, 'SSL');
		$data['mobile_href'] = $this->url->rewrite_keyword('product_id', $product_info['product_id'], 'SSL');
		$data['product_id'] = (int) $product_id;
		$data['manufacturer'] = $product_info['manufacturer'];
		$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id'], 'SSL');
		$data['model'] = $product_info['model'];
		$data['hsn_code'] = $product_info['hsn_code'];
		$data['points'] = $product_info['points'];
		$data['piece_in_set'] = $product_info['piece_in_set'];
		$data['seller_tax'] = $product_info['seller_tax'];
		$data['commission'] = $product_info['commission'];
		$data['sold_out'] = $product_info['sold_out'];
		$data['stock_status'] = $product_info['stock_status'];
		$data['status'] = $product_info['status'];
		$data['cod_available'] = $product_info['cod_available'];

		$data['format_mrp'] = $product_info['format_mrp'];
		$data['mrp'] = $product_info['mrp'];
		$data['margin_percentage'] = !empty($product_info['margin_percentage']) ? $product_info['margin_percentage'] : '';
		$data['saving_money'] = $product_info['saving_money'];
		$data['selling_price'] = $this->currency->format($product_info['selling_price']);
		$data['tax_per_piece'] = $this->currency->format($product_info['tax_per_piece']);

		$data['is_sor_enabled'] = $product_info['is_sor_enabled'];
		$data['sor_enabled_text'] = $product_info['sor_enabled_text'];
		$data['sor_enabled_detail_text'] = $product_info['sor_enabled_detail_text'];

		/* Added by Amarat (23-august-2017) */
		//set base Unit and super unit
		$data['base_unit'] = $product_info['base_unit'];
		$data['super_unit'] = $product_info['super_unit'];

		$pickup_city = preg_replace('/[0-9]+/', '', $product_info['pickup_city']);
		$pickup_city = explode(",", $pickup_city);
		$index_count = count($pickup_city) - 1;
		if (isset($pickup_city[$index_count])) {
			$product_info['pickup_city'] = $pickup_city[$index_count];
		} else {
			$product_info['pickup_city'] = $pickup_city[$index_count - 1];
		}

		$data['pickup_city'] = ucwords(strtolower(trim($product_info['pickup_city'])));

		//manage units
		if ($product_info['base_unit'] != '') {
			$data['text_per_piece'] = '/ ' . $product_info['base_unit'];
			$data['text_withoutslash_piece'] = 'Per ' . $product_info['base_unit'];
		} else {
			$data['text_per_piece'] = $this->language->get('text_per_piece');
			$data['text_withoutslash_piece'] = $this->language->get('text_withoutslash_piece');
		}

		//manage set description

		$data['set_description'] = $product_info['set_description'];
		/* End by Amarat (23-august-2017) */

		$language['column_mrp'] = $this->language->get('column_mrp');
		$language['column_our_price'] = $this->language->get('column_our_price');
		$language['column_save_money'] = $this->language->get('column_save_money');
		$data['exp_final_date'] = $product_info['exp_dispatch_days'];

		if ($product_info['quantity'] <= 0) {
			$data['stock'] = $product_info['stock_status'];
			$data['quantity'] = 0;
		} elseif ($this->config->get('config_stock_display')) {
			$data['stock'] = $product_info['quantity'];
			$data['quantity'] = $product_info['quantity'];
		} else {
			$language['stock'] = $this->language->get('text_instock');
			$data['quantity'] = $product_info['quantity'];
		}
		if ($product_info['vacation_mode'] == 1) {
			$data['stock'] = $product_info['stock_status'];
			$data['quantity'] = 0;
		}

		$this->load->model('tool/image');

		if ($_SERVER['HTTPS']) {
			$static_content_url = STATIC_CONTENT_URL_SSL;
		} else {
			$static_content_url = STATIC_CONTENT_URL;
		}

		if ($product_info['image']) {
			$data['original'] = $product_info['image'];
			$dimensions = unserialize($product_info['image_dimensions']);
			if (!empty($dimensions['width'])) {
				$width_orig = $dimensions['width'];
			}

			if (!empty($dimensions['height'])) {
				$height_orig = $dimensions['height'];
			}

			//if (file_exists(DIR_IMAGE . $product_info['image'])) {
			//   list($width_orig, $height_orig) = @getimagesize(DIR_IMAGE . $product_info['image']);

			if (!empty($width_orig) && !empty($height_orig) && ($width_orig / $height_orig) > 1) {
				$extension = pathinfo($product_info['image'], PATHINFO_EXTENSION);
				$data['wide_image'] = true;

				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_height'), $this->config->get('config_image_popup_width'));
				$data['pan_detail'] = $this->model_tool_image->resize($product_info['image'], 332, 221);
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_height'), $this->config->get('config_image_additional_width'));

				$data['original'] = $this->model_tool_image->getOriginalImage($product_info['image']);
				$data['popup_width'] = $this->config->get('config_image_popup_height');
				$data['popup_height'] = $this->config->get('config_image_popup_width');
				$data['pan_width'] = 332;
				$data['pan_height'] = 221;
				$data['additional_width'] = $this->config->get('config_image_additional_height');
				$data['additional_height'] = $this->config->get('config_image_additional_width');
				$data['img_vertical'] = false;

			} else {
				$extension = pathinfo($product_info['image'], PATHINFO_EXTENSION);
				$data['wide_image'] = false;

				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
				$data['pan_detail'] = $this->model_tool_image->resize($product_info['image'], 332, 497);
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'));

				$data['original'] = $this->model_tool_image->getOriginalImage($product_info['image']);
				$data['popup_width'] = $this->config->get('config_image_popup_width');
				$data['popup_height'] = $this->config->get('config_image_popup_height');
				$data['pan_width'] = 332;
				$data['pan_height'] = 497;
				$data['additional_width'] = $this->config->get('config_image_additional_width');
				$data['additional_height'] = $this->config->get('config_image_additional_height');
				$data['img_vertical'] = true;

			}
		}

		$data['images'] = array();

		//name added in data array
		$data['name'] = $product_info['name'];
		$data['is_combo'] = $product_info['is_combo'];
		$data['stock_bool'] = false;
		if ($product_info['stock_status'] == "In Stock") {
			$data['stock_bool'] = true;
		}

		$results = $this->model_catalog_product->getProductImages($product_id);
		$data['preload_img_string'] = array();
		$data['images'] = array();

		$image_dimensions = null;
		if (!empty($product_info['image_dimensions'])) {
			$image_dimensions = unserialize($product_info['image_dimensions']);
		}
		$data['images'][] = $this->get_images_data($product_info['image'], $image_dimensions);

		$data['preload_img_string'][] = $data['images'][0]['popup'];
		$data['preload_img_string'][] = $data['images'][0]['thumb'];
		$data['image'] = $data['images'][0]['image'];

		foreach ($results as $result) {
			$image_dimensions = null;
			if (!empty($result['image_dimensions'])) {
				$image_dimensions = unserialize($result['image_dimensions']);
			}
			$image_data = $this->get_images_data($result['image'], $image_dimensions);
			$data['images'][] = $image_data;
			$data['preload_img_string'][] = $image_data['popup'];
			$data['preload_img_string'][] = $image_data['thumb'];
		}

		if ($this->customer->isLogged()) {
			$data['previously_ordered'] = $this->model_catalog_product->checkPreviouslyOrdered($this->customer->getId(), $product_info['product_id']);
		} else {
			$data['previously_ordered'] = false;
		}

		$seller_tax_factor = 1.0 + ((float) $product_info['seller_tax'] / 100.0);
		$commission_factor = 1.0 + ((float) $product_info['commission'] / 100.0);

		$piece_in_set = (int) $product_info['piece_in_set'] > 1 ? (int) $product_info['piece_in_set'] : 1;
		$unit_price = ceil($commission_factor * ((float) ($product_info['price']) / $seller_tax_factor));

		/* Added by amarat by help devendra sir date on 01-july-2017*/
		//$tax_rate = $this->tax->getMajorTaxRate($product_info['tax_class_id']);
		$tax_rate = $product_info['tax_rate'];
		$data['text_tax_rate'] = $tax_rate ? '(' . (float) $tax_rate . "%)" : false;

		if ((float) $product_info['special']) {
			$unit_special_price = (float) $product_info['special'];
			$data['special'] = $this->currency->format($this->tax->calculate($unit_special_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
			$data['percent_discount'] = $this->model_catalog_product->calculateSpecialPriceValueInPercent($unit_special_price, $product_info['selling_price'], $product_info['tax_class_id']);
			$data['special_per_set'] = $this->currency->format($this->tax->calculate($unit_special_price * $piece_in_set, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
			$data['special_price'] = $unit_special_price;
		} else {
			$data['special_per_set'] = false;
			$data['percent_discount'] = false;
			$data['special'] = false;
			$data['special_price'] = false;
		}

		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$price = $this->currency->format($this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));

			if ($product_info['base_unit'] != '') {
				if ($data['special']) {
					$data['special'] = $data['special'] . ' / ' . $product_info['base_unit'];
					$price_with_unit = $price;
				} else {
					$price_with_unit = $price . ' / ' . $product_info['base_unit'];
				}
			} else {
				$price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
			}

			$data['price'] = $price_with_unit;
			$data['price_value'] = $this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']);
			$data['unformatted_price'] = $this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']);
		} else {
			$data['price'] = false;
			$data['price_value'] = false;
			$data['unformatted_price'] = false;
		}

		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$data['price_per_set'] = $this->currency->format($this->tax->calculate($unit_price * $piece_in_set, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
		} else {
			$data['price_per_set'] = false;
		}

		$data['tax_class_id'] = $product_info['tax_class_id'];

		if ($this->config->get('config_tax')) {
			$data['tax'] = $this->currency->format((float) $product_info['special'] ? ceil($commission_factor * (float) ($product_info['special']) / $seller_tax_factor) : ceil($commission_factor * (float) ($product_info['price']) / $seller_tax_factor));
		} else {
			$data['tax'] = false;
		}

		$data['discounts'] = array();

		if ($this->config->get('config_store_id') != SOR_STORE_ID) {
			$discounts = $this->model_catalog_product->getProductDiscounts($product_id, 0);
			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price' => $this->currency->format($this->tax->calculate(ceil($commission_factor * (float) $discount['price'] / $seller_tax_factor), $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp'])),
				);
			}
		}

		$data['options'] = array();
		$this->load->model('tool/image');
		foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
			$product_option_value_data = array();
			foreach ($option['product_option_value'] as $option_value) {
				if (!$option_value['subtract'] || ($option_value['quantity'] >= 0)) {
					if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float) $option_value['price']) {
						$option_price = 0;
						if ($option_value['price_prefix'] == "+") {
							$option_price += $option_value['price'];
						} else {
							$option_price -= $option_value['price'];
						}
						$product_info['option_price'] = $option_price;
						$price_details = $this->cart->getPrice($product_info, $this->registry);
						$unfor_option_price = $price_details['selling_price'];
						$price = $this->currency->format($unfor_option_price);
					} else {
						$price = false;
						$unfor_option_price = false;
					}
					$product_option_value_data[] = array(
						'product_option_value_id' => $option_value['product_option_value_id'],
						'option_value_id' => $option_value['option_value_id'],
						'name' => $option_value['name'],
						'image' => $this->model_tool_image->resize($option_value['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
						'image_thumb' => $this->model_tool_image->resize($option_value['image'], 50, 75),
						'price' => $price,
						'unfor_option_price' => $unfor_option_price,
						'price_prefix' => $option_value['price_prefix'],
						'quantity' => $option_value['quantity'],
						'extra_info' => $option_value['extra_info'],
					);
				}
			}

			$data['options'][] = array(
				'product_option_id' => $option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id' => $option['option_id'],
				'name' => $option['name'],
				'type' => $option['type'],
				'value' => $option['value'],
				'required' => $option['required'],
			);
		}

		if ($product_info['minimum']) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		// $data['review_status'] = $this->config->get('config_review_status');
		$data['review_status'] = 0;

		if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
			$data['review_guest'] = true;
		} else {
			$data['review_guest'] = false;
		}

		if ($this->customer->isLogged()) {
			$data['logged_in'] = true;
			$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			$data['telephone'] = $this->customer->getTelephone();
			$data['email'] = $this->customer->getEmail();
		} else {
			$data['customer_name'] = '';
			$data['telephone'] = '';
			$data['email'] = '';
		}

		$language['reviews'] = sprintf($this->language->get('text_reviews'), (int) $product_info['reviews']);
		$data['rating'] = (int) $product_info['rating'];
		$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
		$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_id);
		$data['is_single'] = $product_info['is_single'];
		$data['product_name'] = $product_info['name'];
		$data['product_image'] = $product_info['image'];
		$data['reviews'] = $product_info['reviews'];
		$data['schema_price'] = $product_info['selling_price'];
		$data['exp_dispatch_days'] = $product_info['exp_dispatch_days'];

		$ga_id = 'wsb-addtocart-from-detail-' . strtolower($product_info['model']);
		if (isset($this->request->get['popup'])) {

			$ga_id = 'wsb-addtocart-from-detailpopup-' . strtolower($product_info['model']);
		}
		$data['cart_tracking_id_for_ga'] = $ga_id;
		//echo "<pre>"; print_r($product_info); exit;
		/* Added by
	            * Kuldeep
*/
		$filters = $this->model_catalog_product->getProductFiltersData($product_id);
		$data['filters'] = $filters;

		$data['tags'] = array();

		if ($product_info['tag']) {
			$tags = explode(',', $product_info['tag']);

			foreach ($tags as $tag) {
				$data['tags'][] = array(
					'tag' => trim($tag),
					'href' => $this->url->link('product/search', 'tag=' . trim($tag), 'SSL'),
				);
			}
		}

		$language['text_payment_recurring'] = $this->language->get('text_payment_recurring');
		$data['recurrings'] = $this->model_catalog_product->getProfiles($product_id);

		if ($this->config->get('config_google_captcha_status')) {
			$this->document->addScript('https://www.google.com/recaptcha/api.js');
			$data['site_key'] = $this->config->get('config_google_captcha_public');
		} else {
			$data['site_key'] = '';
		}

		/**Language **/
		$data['language'] = $language;

		/**Wishlist **/
		$data['fill_heart'] = $this->customer->getWishlistIcon($product_id);

		/**related product**/
		$same_products_array = $this->model_seller_product->getProductRelated($product_id);
		$same_products[$product_id] = $this->model_catalog_product->getProductByIdMinimalData($product_id);
		if (!empty($same_products_array)) {
			foreach ($same_products_array as $key => $value) {
				$same_products[$value] = $this->model_catalog_product->getProductByIdMinimalData($value);
			}
		}
		$data['same_products'] = $same_products ?? array();

		// set custom meta data
		$this->load->model('catalog/custom_url');
		$custom_url_data = $this->model_catalog_custom_url->getMobileCustomUrlInfo($data['product_detail_id']);
		if (!empty($custom_url_data['meta_title'])) {
			$data['meta_title'] = $custom_url_data['meta_title'];
			$data['meta_description'] = $custom_url_data['meta_description'];
			$data['meta_keywords'] = $custom_url_data['keyword'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->rewrite_keyword('common/home', '', 'SSL'),
		);

		if (!empty($data['category_info'])) {
			$data['breadcrumbs'][] = array(
				'text' => $data['category_info']['name'],
				'href' => $this->url->rewrite_keyword('category_id', $data['category_info']['category_id'], 'SSL'),
			);
		}

		return $data;
	}

	public function askQuestion() {

		$this->load->model('catalog/product');
		$this->load->language('product/product');

		if ($this->customer->isLogged()) {
			$customer_id = $this->customer->isLogged();
			$customer_name = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
			$customer_telephone = $this->customer->getTelephone();
			$customer_email = $this->customer->getEmail();
			$product_id = $this->request['product_id'];
			$popup_question = $this->request['popup_question'];
		} else {
			$customer_id = 0;
			$customer_name = $this->request['customer_name'];
			$customer_telephone = $this->request['telephone'];
			$customer_email = $this->request['email'];
			$product_id = $this->request['product_id'];
			$popup_question = $this->request['popup_question'];
		}

		$json = array();

		$data_user_comment = array(
			'customer_id' => $customer_id,
			'customer_name' => $customer_name,
			'telephone' => $customer_telephone,
			'email' => $customer_email,
			'product_id' => $product_id,
			'popup_question' => $popup_question,

		);
		$this->load->model('tool/image');
		$getInformation = $this->model_catalog_product->askAQuestion($data_user_comment);

		$json = array(
			'product_id' => $product_id,
			'message' => $this->language->get('ask_question_message'),
		);
		return $json;

	}

	public function getRelatedProducts() {
		$pid = $this->args['0'];
		$seller_id = $this->args['1'];
		$pid = $this->url->rewrite_value($pid);
		if (isset($this->args['2'])) {$custom_store = $this->args['2'];} else { $custom_store = '';}

		if ($pid && $seller_id) {
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			$this->load->language('product/product');

			$data = array();
			$data['text_related'] = $this->language->get('text_related');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');

			if ($_SERVER['HTTPS']) {
				$static_content_url = STATIC_CONTENT_URL_SSL;
			} else {
				$static_content_url = STATIC_CONTENT_URL;
			}

			$data['products'] = array();

			if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
				$filter_data = array(
					'filter_seller_id' => $seller_id,
					'not_product_ids' => $pid,
					'filter_category_id' => $this->model_catalog_product->getRelatedCategoryProduct($pid),
					'custom_store' => $custom_store,
					'start' => 0,
					'limit' => 30,
					'shuffle' => true,
				);

				$solr = new SolrProduct($this);
				$results_solr = $solr->getProductFromSolr($filter_data);
				$results = $results_solr['products'];
				//shuffle($results);
			} else {
				$results = $this->model_catalog_product->getRelatedBySellerAndCategory($pid);
			}

			foreach ($results as $result) {

				if ($result['product_id'] > 0) {
					$result_images = array();

					$image_dimensions = null;
					if (!empty($result['image_dimensions'])) {
						$image_dimensions = unserialize($result['image_dimensions']);
					}
					$result_images[] = $this->get_images_data($result['image'], $image_dimensions);

					$seller_tax_factor = 1.0 + ((float) $result['seller_tax'] / 100.0);
					$commission_factor = 1.0 + ((float) $result['commission'] / 100.0);

					$piece_in_set = (int) $result['piece_in_set'] > 1 ? (int) $result['piece_in_set'] : 1;
					$unit_price = ceil($commission_factor * (float) ($result['price']) / $seller_tax_factor);

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
						$unformatted_price = $this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
					} else {
						$price = false;
						$unformatted_price = false;
					}

					if ((float) $result['special']) {
						$unit_special_price = (float) $result['special'];
						$special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']), '', '', true, 0, 'frontend');
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float) $result['special'] ? ceil($commission_factor * (float) ($result['special']) / $seller_tax_factor) : ceil($commission_factor * (float) ($result['price']) / $seller_tax_factor));
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = (int) $result['rating'];
					} else {
						$rating = false;
					}
					$url_related = '';
					if (isset($this->request->get['popup'])) {
						$url_related .= '&popup=true';
					}

					if ($result['quantity'] <= 0) {
						$productStock = $result['stock_status'];
						$productQuantity = 0;
					} elseif ($this->config->get('config_stock_display')) {
						$productStock = $result['quantity'];
						$productQuantity = $result['quantity'];
					} else {
						$productStock = $this->language->get('text_instock');
						$productQuantity = $result['quantity'];
					}
					if ($result['vacation_mode'] == 1) {
						$productStock = $result['stock_status'];
						$productQuantity = 0;
					}

					//for wishlist
					$fill_heart = $this->customer->getWishlistIcon($result['product_id']);
					$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

					//manage text units
					if ($result['base_unit'] != '') {
						if ($special) {
							$special = $special . ' / ' . $result['base_unit'];
							$price_with_unit = $price;
						} else {
							$price_with_unit = $price . ' / ' . $result['base_unit'];
						}
					} else {
						$price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
					}
					/* End by Amarat (23-august-2017) */

					$stock_array = Cart::getProductStockStatus($result);
					if (isset($stock_array['stock']) && $stock_array['stock'] === true) {
						$stock = true;
						$quantity = $result['quantity'];
					} else {
						$stock = false;
						$quantity = 0;
					}

					$pickup_city = preg_replace('/[0-9]+/', '', $result['pickup_city']);
					$pickup_city = explode(",", $pickup_city);
					$index_count = count($pickup_city) - 1;
					if (isset($pickup_city[$index_count])) {
						$result['pickup_city'] = trim($pickup_city[$index_count]);
					} else {
						$result['pickup_city'] = trim($pickup_city[$index_count - 1]);
					}

					if ($stock == true) {

						$data['products'][] = array(
							'product_id' => $result['product_id'],
							'name' => $result['name'],
							'set_description' => $result['set_description'],
							'is_sor_enabled' => $result['is_sor_enabled'],
							'sor_enabled_text' => $result['sor_enabled_text'],
							'sor_enabled_detail_text' => $result['sor_enabled_detail_text'],
							'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
							'price' => $price_with_unit,
							'unformatted_price' => $unformatted_price,
							'special' => $special,
							'pickup_city' => ucwords(strtolower($result['pickup_city'])),
							'piece_in_set' => $piece_in_set,
							'seller_id' => $result['seller_id'],
							'category_id' => $result['category_id'],
							'tax' => $tax,
							'tax_class_id' => $result['tax_class_id'],
							'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
							'rating' => $rating,
							'fill_heart' => $fill_heart,
							'href' => $this->url->rewrite_keyword('product_id', $result['product_id'], 'SSL'),
							'category_href' => $this->url->rewrite_keyword('category_id', $result['category_id'], 'SSL'),
							'image' => $result_images[0]['image'],
							'original' => $result_images[0]['original'],
							'img_vertical' => $result_images[0]['img_vertical'],
							'img_releted_width' => $result_images[0]['img_related_width'],
							'img_releted_height' => $result_images[0]['img_related_height'],
							'stock_status' => $result['stock_status'],
							'quantity' => $productQuantity,
							'options' => $product_options,
							'margin_percentage' => !empty($result['margin_percentage']) ? $result['margin_percentage'] : '',
						);
					}

				}
			}

			return $data;

		}

	}

	public function getRelatedProductsFromSeller() {
		$pid = $this->args['0'];
		$seller_id = $this->args['1'];
		$custom_store = $this->args['2'];

		if ($pid) {
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			$this->load->language('product/product');

			$data = array();
			$data['text_related'] = $this->language->get('text_related');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');

			if ($_SERVER['HTTPS']) {
				$static_content_url = STATIC_CONTENT_URL_SSL;
			} else {
				$static_content_url = STATIC_CONTENT_URL;
			}

			$data['products'] = array();

			if (SOLR_ENABLED && SOLR_WSBOX_ENABLED) {
				$filter_data = array(
					'filter_seller_id' => $seller_id,
					'not_product_ids' => $pid,
					'custom_store' => $custom_store,
					'start' => 0,
					'limit' => 30,
					'shuffle' => true,
				);

				$solr = new SolrProduct($this);
				$results_solr = $solr->getProductFromSolr($filter_data);
				$results = $results_solr['products'];
				shuffle($results);
			} else {
				$results = $this->model_catalog_product->getRelatedBySeller($pid);
			}

			foreach ($results as $result) {

				if ($result['product_id'] > 0) {

					$result_images = array();

					$image_dimensions = null;
					if (!empty($result['image_dimensions'])) {
						$image_dimensions = unserialize($result['image_dimensions']);
					}
					$result_images[] = $this->get_images_data($result['image'], $image_dimensions);

					$seller_tax_factor = 1.0 + ((float) $result['seller_tax'] / 100.0);
					$commission_factor = 1.0 + ((float) $result['commission'] / 100.0);

					$piece_in_set = (int) $result['piece_in_set'] > 1 ? (int) $result['piece_in_set'] : 1;
					$unit_price = ceil($commission_factor * (float) ($result['price']) / $seller_tax_factor);

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
						$unformatted_price = $this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
					} else {
						$price = false;
						$unformatted_price = false;
					}

					if ((float) $result['special']) {
						$unit_special_price = (float) $result['special'];
						$special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']), '', '', true, 0, 'frontend');
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float) $result['special'] ? ceil($commission_factor * (float) ($result['special']) / $seller_tax_factor) : ceil($commission_factor * (float) ($result['price']) / $seller_tax_factor));
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = (int) $result['rating'];
					} else {
						$rating = false;
					}
					$url_related = '';
					if (isset($this->request->get['popup'])) {
						$url_related .= '&popup=true';
					}

					if ($result['quantity'] <= 0) {
						$productStock = $result['stock_status'];
						$productQuantity = 0;
					} elseif ($this->config->get('config_stock_display')) {
						$productStock = $result['quantity'];
						$productQuantity = $result['quantity'];
					} else {
						$productStock = $this->language->get('text_instock');
						$productQuantity = $result['quantity'];
					}
					if ($result['vacation_mode'] == 1) {
						$productStock = $result['stock_status'];
						$productQuantity = 0;
					}

					//for wishlist
					$fill_heart = $this->customer->getWishlistIcon($result['product_id']);
					$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

					//manage text units
					if ($result['base_unit'] != '') {
						if ($special) {
							$special = $special . ' / ' . $result['base_unit'];
							$price_with_unit = $price;
						} else {
							$price_with_unit = $price . ' / ' . $result['base_unit'];
						}
					} else {
						$price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
					}
					/* End by Amarat (23-august-2017) */

					$stock_array = Cart::getProductStockStatus($result);
					if (isset($stock_array['stock']) && $stock_array['stock'] === true) {
						$stock = true;
						$quantity = $result['quantity'];
					} else {
						$stock = false;
						$quantity = 0;
					}

					if ($stock == true) {

						$data['products'][] = array(
							'product_id' => $result['product_id'],
							'name' => $result['name'],
							'is_sor_enabled' => $result['is_sor_enabled'],
							'sor_enabled_text' => $result['sor_enabled_text'],
							'sor_enabled_detail_text' => $result['sor_enabled_detail_text'],
							'set_description' => $result['set_description'],
							'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
							'price' => $price_with_unit,
							'unformatted_price' => $unformatted_price,
							'special' => $special,
							'piece_in_set' => $piece_in_set,
							'seller_id' => $result['seller_id'],
							'category_id' => $result['category_id'],
							'tax' => $tax,
							'tax_class_id' => $result['tax_class_id'],
							'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
							'rating' => $rating,
							'fill_heart' => $fill_heart,
							'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url_related, 'SSL'),
							'image' => $result_images[0]['image'],
							'original' => $result_images[0]['original'],
							'img_vertical' => $result_images[0]['img_vertical'],
							'img_releted_width' => $result_images[0]['img_related_width'],
							'img_releted_height' => $result_images[0]['img_related_height'],
							'stock_status' => $result['stock_status'],
							'quantity' => $productQuantity,
							'options' => $product_options,
							'margin_percentage' => !empty($result['margin_percentage']) ? $result['margin_percentage'] : '',
						);
					}

				}
			}
			return $data;

		}
	}

	public function getproductOption() {
		$pid = $this->args['0'];
	}

	public function productMetaTag($product_info, $data) {

	}

	public function user_comment() {
		$this->load->model('catalog/product');
		$this->load->language('product/search');

		$customer_id = $this->customer->isLogged();
		$customer_mobile = $this->request['customer_mobile'];
		$product_id = $this->request['product_id'];
		$product_status = $this->request['product_status'];
		$popup_comment = $this->request['popup_comment'];
		$option_name = isset($this->request['option_name']) ? $this->request['option_name'] : '';
		$option_value = isset($this->request['option_value']) ? $this->request['option_value'] : '';

		$request_source = 'WEB';
		if (CONFIG_IS_MOBILE) {
			$request_source = 'MOBILE';
		}

		$data_user_comment = array(
			'customer_id' => $customer_id,
			'product_id' => $product_id,
			'customer_mobile' => $customer_mobile,
			'product_status' => $product_status,
			'popup_comment' => $popup_comment,
			'option_name' => $option_name,
			'option_value' => $option_value,
			'request_source' => $request_source,
		);

		$getInformation = $this->model_catalog_product->user_comment($data_user_comment);

		$message = sprintf($this->language->get('mail_message'),
			$getInformation['customer_name'],
			$getInformation['mobile_no'],
			$getInformation['product_name'],
			$getInformation['product_model'],
			$getInformation['product_status'],
			$getInformation['user_comment']
		) . "\n\n";

		$subject = $this->language->get('mail_subject');
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $this->config->get('config_mail_smtp_hostname');
		$mail->SMTPSecure = 'ssl';
		$mail->Port = $this->config->get('config_mail_smtp_port');
		$mail->SMTPAuth = true;
		$mail->Username = $this->config->get('config_mail_smtp_username');
		$mail->Password = $this->config->get('config_mail_smtp_password');
		$mail->setFrom(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
		$mail->addReplyTo(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
		$mail->addAddress(EMAIL_IDS['sales']['email_id'], EMAIL_IDS['sales']['name']);
		$mail->Subject = $subject;
		$mail->msgHTML($message);
		$mail->send();

		$json = array(
			'product_id' => $product_id,
			'message' => $this->language->get('want_design_message'),
		);

		return $json;
	}

	public function addWishlist() {

		$this->load->language('account/wishlist');

		$json = array();

		if (isset($this->request['product_id'])) {
			$product_id = $this->request['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if (!$this->customer->checkExistingWishlistItem($product_id)) {
				$product_hotness = new ProductHotness($this->db);
				$product_hotness->updateHotness("add-to-wishList", $product_id);
				$this->customer->processWishlist($product_id);

				if ($this->customer->isLogged()) {
					$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int) $this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
				} else {
					$json['info'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'), $this->url->link('product/product', 'product_id=' . (int) $this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
				}
			} else {
				$json['info'] = sprintf($this->language->get('text_exists'), $this->url->link('product/product', 'product_id=' . (int) $this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
			}

			$total = $this->customer->getTotalWishlists();
			$json['total'] = sprintf($this->language->get('text_wishlist'), $total);
			$json['count'] = $total;
		}
		return $json;

	}

	public function addWishlistMobile() {

		$this->load->language('account/wishlist');

		$json = array();

		if (isset($this->request['product_id'])) {
			$product_id = $this->request['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if (!$this->customer->checkExistingWishlistItem($product_id)) {

				$product_hotness = new ProductHotness($this->db);
				$product_hotness->updateHotness("add-to-wishList", $product_id);

				$this->customer->processWishlist($product_id);

				if ($this->customer->isLogged()) {
					$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int) $this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
				} else {
					$json['info'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'), $this->url->link('product/product', 'product_id=' . (int) $this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
				}
			} else {
				$json['info'] = sprintf($this->language->get('text_exists'), $this->url->link('product/product', 'product_id=' . (int) $this->request['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
			}

			$total = $this->customer->getTotalWishlists();
			$json['total'] = sprintf($this->language->get('text_wishlist'), $total);
			$json['count'] = $total;
		}
		return $json;

	}

	public function getProductDetailsByModelNumber() {
		$produtDetail = array();
		$product_models = $this->request['modelNumbers'];
		$product_models_explode = explode(',', $product_models);
		foreach ($product_models_explode as $key => $product_model) {
			$this->load->model('catalog/product');
			$this->load->language('product/product');
			$produtDetail = $this->model_catalog_product->getProductByModel($product_model);
			if (isset($produtDetail['product_detail']['product_id'])) {
				$seller_tax_factor = 1.0 + ((float) $produtDetail['seller_tax'] / 100.0);
				$commission_factor = 1.0 + ((float) $produtDetail['product_detail']['commission'] / 100.0);

				$piece_in_set = (int) $produtDetail['product_detail']['piece_in_set'] > 1 ? (int) $produtDetail['product_detail']['piece_in_set'] : 1;
				$unit_price = ceil($commission_factor * (float) ($produtDetail['product_detail']['price']) / $seller_tax_factor);

				/* Added by amarat by help devendra sir date on 01-july-2017*/
				//$tax_rate = $this->tax->getMajorTaxRate($product_info['tax_class_id']);
				$tax_rate = $produtDetail['tax_rate'];
				$data['text_tax_rate'] = $tax_rate ? '(' . (float) $tax_rate . "%)" : false;

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($unit_price, $produtDetail['product_detail']['tax_class_id'], $this->config->get('config_tax'), $produtDetail['product_detail']['mrp']));
					if ($produtDetail['p_base_unit'] != '') {
						$price_with_unit = $price . ' / ' . $produtDetail['p_base_unit'];
					} else {
						$price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
					}

					$data['price'] = $price_with_unit;
					$data['price_value'] = $this->tax->calculate($unit_price, $produtDetail['product_detail']['tax_class_id'], $this->config->get('config_tax'), $produtDetail['product_detail']['mrp']);
					$data['unformatted_price'] = $this->tax->calculate($unit_price, $produtDetail['product_detail']['tax_class_id'], $this->config->get('config_tax'), $produtDetail['product_detail']['mrp']);
				} else {
					$data['price'] = false;
					$data['price_value'] = false;
					$data['unformatted_price'] = false;
				}
				$dataDetail[$product_model] = array(
					'product_id' => $produtDetail['product_detail']['product_id'],
					'seller_tax' => $produtDetail['product_detail']['seller_tax'],
					'commission' => $produtDetail['product_detail']['commission'],
					'sku' => $produtDetail['product_detail']['sku'],
					'model' => $produtDetail['product_detail']['model'],
					'image' => $produtDetail['product_detail']['image'],
					'selling_price' => $produtDetail['product_detail']['selling_price'],
					'price_per_set' => $produtDetail['product_detail']['price_per_set'],
					'piece_in_set' => $produtDetail['product_detail']['piece_in_set'],
					'minimum' => $produtDetail['product_detail']['minimum'],
					'name' => $produtDetail['product_detail']['name'],
					'set_description' => $produtDetail['product_detail']['set_description'],
					'tax_class_id' => $produtDetail['product_detail']['tax_class_id'],
					'mrp' => $produtDetail['product_detail']['mrp'],
					'prolink' => $this->url->link('product/product', 'product_id=' . (int) $produtDetail['product_detail']['product_id'], 'SSL'),
					'text_tax_rate' => $data['text_tax_rate'],
					'price' => $data['price'],
					'price_value' => $data['price_value'],
					'unformatted_price' => $data['unformatted_price'],
				);
			} else {
				$dataDetail[$product_model] = array();
			}
		}
		$result = $dataDetail;
		return $result;
	}

	/**
	 * method for get product detail
	 * @param  : product ids string
	 * @return : products array
	 * @author : Mahaveer Aug 2019
	 */
	public function getProductDetailsByProductId() {
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$product_id_array = $this->request['product_id_string'];
		$dataDetail = array();

		$produtDetail = $this->model_catalog_product->getWebengageProductDetail($product_id_array);

		foreach ($produtDetail as $product) {
			//special price calculate
			$seller_tax_factor = 1.0 + ((float) $product['seller_tax'] / 100.0);
			$commission_factor = 1.0 + ((float) $product['commission'] / 100.0);

			if ((float) $product['special']) {
				$unit_special_price = (float) $product['special'];
				$special = $this->currency->format($this->tax->calculate($unit_special_price, $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']));
			} else {
				$special = 0;
			}

			//price calculate
			$piece_in_set = (int) $product['piece_in_set'] > 1 ? (int) $product['piece_in_set'] : 1;
			$unit_price = ceil($commission_factor * ((float) ($product['price']) / $seller_tax_factor));
			$price_value = $this->tax->calculate($unit_price, $product['tax_class_id'], $this->config->get('config_tax'), $product['mrp']);

			//brand name
			$brand_name = $this->model_catalog_product->getProductBrandName($product['product_id']);

			//options
			$options = array();
			foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) {

				foreach ($option['product_option_value'] as $option_value) {

					if (!$option_value['subtract'] || ($option_value['quantity'] >= 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float) $option_value['price']) {
							$option_price = 0;
							if ($option_value['price_prefix'] == "+") {
								$option_price += $option_value['price'];
							} else {
								$option_price -= $option_value['price'];
							}
							$product_info['option_price'] = $option_price;
							$price_details = $this->cart->getPrice($product, $this->registry);
							$unfor_option_price = $price_details['selling_price'];
							$price = $this->currency->format($unfor_option_price);
						} else {
							$price = false;
							$unfor_option_price = false;
						}
						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id' => $option_value['option_value_id'],
							'name' => $option_value['name'],
							'image' => $this->model_tool_image->resize($option_value['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
							'price' => $price,
							'unfor_option_price' => $unfor_option_price,
							'price_prefix' => $option_value['price_prefix'],
							'quantity' => $option_value['quantity'],
							'extra_info' => $option_value['extra_info'],
						);
					}
				}
				$options[] = array(
					'product_option_id' => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id' => $option['option_id'],
					'name' => $option['name'],
					'type' => $option['type'],
					'value' => $option['value'],
					'required' => $option['required'],
				);
			}

			//final response data
			$dataDetail[$product['product_id']] = array(
				'product_id' => $product['product_id'],
				'model' => $product['model'],
				'brand_name' => $brand_name,
				'image' => $this->model_tool_image->resize($product['image'], 500, 250),
				'quantity' => $product['quantity'],
				'name' => $product['name'],
				'special_price' => $special,
				'price_value' => $price_value,
				'category_id' => $product['category_id'],
				'category_name' => $product['category_name'],
				'meta_title' => $product['meta_title'],
				'meta_description' => $product['meta_description'],
				'meta_keyword' => $product['meta_keyword'],
				'options' => $options,
				'seller_id' => $product['seller_id'],
				'seller_nickname' => $product['seller_nickname'],
			);

		}
		echo json_encode($dataDetail);
	}

}

?>