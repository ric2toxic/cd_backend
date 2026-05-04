<?php
class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {

	    $data['popup_view'] = false;
        if (isset($this->request->get['popup'])) {
            $data['popup_view'] = $this->request->get['popup'];
        }

        if($data['popup_view'] == false)
        {
            $this->document->addScript('catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.js');
            $this->document->addStyle('catalog/view/theme/default/stylesheet/touchspin/jquery.bootstrap-touchspin.css');
            $this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
            $this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
            $this->document->addScript('catalog/view/theme/default/javascript/jquery.elevatezoom.js');
            //$this->document->addScript('catalog/view/javascript/jquery/jquery.mousewheel.js');
            $this->document->addScript('catalog/view/javascript/jquery/jquery.preload.min.js');
        }

		$this->load->model('catalog/product');
		$data['product_info_schema'] = '';
		$data['category_info_schema'] = array();
		$data['subcategory_info_schema'] = array();

		$this->load->language('product/product');
        $has_single = false;
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', 'SSL')
		);

		$this->load->model('catalog/category');

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		$data['downloadText'] = $this->language->get('downloadText');
		$data['base'] = $server;

        if ($_SERVER['HTTPS']) {
            $static_content_url =  STATIC_CONTENT_URL_SSL;
        } else {
            $static_content_url =  STATIC_CONTENT_URL ;
        }


        if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			// For Canonical URLS
			// By Parth Gupta
			////////////////////////////////////////////
			// Commented for new cannonical on 8-2-16 //
			////////////////////////////////////////////
			// $can_url =$this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $this->request->get['product_id']);
			// $this->document->addLink($can_url,"canonical");

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path, 'SSL')
					);
					$data['category_info_schema'] = array(
						'name' => $category_info['name'],
						'url'  => $this->url->link('product/category', 'path=' . $path, 'SSL')
					);

				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url, 'SSL')
				);
				if(!empty($data['category_info_schema'])) {
					$data['subcategory_info_schema'] = array(
						'name' => $category_info['name'],
						'url'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url, 'SSL')
					);
				} else {
					$data['category_info_schema'] = array(
						'name' => $category_info['name'],
						'url'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url, 'SSL')
					);
				}
				//echo $category_info['name']; die;
			}
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer', 'SSL')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url, 'SSL')
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url, 'SSL')
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];

			//Added by Parth on 8-2-16
			//for changing canonical path
			$cats = $this->model_catalog_product->getCategories($product_id);

			$max_cat_no = 0;
			$max_cat_path = array();
			foreach ($cats as $category) {
				if($icount =count($cat_path_arr = $this->model_catalog_product->getCategoryHierarchy($category['category_id'])) > $max_cat_no) {
					$max_cat_path = $cat_path_arr;
					$max_cat_no = $icount;
				}
			}
			$can_path = implode("_", $max_cat_path);

			$can_new_url =$this->url->link('product/product', 'path=' . $can_path . '&product_id=' . $this->request->get['product_id'], 'SSL');
			$this->document->addLink($can_new_url,"canonical");

		} else {
			$product_id = 0;
		}

		if(count($this->request->post) > 0) {
			$product_info = $this->request->post;
		} else {
			$product_info = $this->model_catalog_product->getProduct($product_id);
		}

		if(!isset($product_info['non_returnable'])){
			/***--get seller returnable value by vikas (22-01-2016)--***/
			$seller_returnable = $this->model_catalog_product->checkReturnable($product_id);
			$data['seller_returnable'] = $seller_returnable['non_returnable'];
			/*******--------*******/
		}

		$data['seller_returnable'] = $product_info['non_returnable'];

        $tax_rate = $product_info['tax_rate'];
        $data['text_tax_rate'] = $tax_rate ? " + GST(".(float)$tax_rate . "%)" : false;
        $data['text_custom_tax_rate'] = $data['text_tax_rate'];

		/***--get single store or wholesale store selling price value by vikas For parth sir (22-01-2016)--***/
		$alternate_product = $this->model_catalog_product->getAlternateProductInfo($product_info['is_single'],$product_info['model']);
		$data['custom_store_selling_price'] = isset($alternate_product['selling_price']) ? $alternate_product['selling_price'] : '' ;
		$data['custom_store_product_id'] = isset($alternate_product['product_id']) ? $alternate_product['product_id'] : '' ;
        $data['text_custom_tax_rate'] = isset($alternate_product['tax_rate']) ? " + GST(".(float)$alternate_product['tax_rate'] . "%)": false ;
		if (isset($this->request->get['popup'])) {
			$custom_url = '&popup=true';
		} else {
			$custom_url = '';
		}

        if ( isset($this->request->get['path']) ) {
           $path = $this->request->get['path'];
        } else {
            $path = '';
        }

             $data['custom_store_product_href'] = $this->url->link('product/product', 'path=' . $path . '&product_id=' . $data['custom_store_product_id']. $custom_url, 'SSL');


		/*******--------*******/


        if($has_single == false) {
            $is_single = $this->model_catalog_product->checkIfProductIsSingle($product_id);
        }
        
        $actualsold = 0; 

        // $data['is_single'] = $is_single;
		if ($product_info) {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'][] = array(
                'text' => $product_info['name'],
                'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'], 'SSL')
            );
            $data['product_info_schema'] = $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'], 'SSL');

            $data['last_category_href'] = '';
            // Getting category link (in case of multiple categories, it picks only the first one in the list)
            if ($this->model_catalog_product->getCategory($product_id)) {
                $data['last_category_href'] = $this->url->link('product/category', 'path=' . (int)($this->model_catalog_product->getCategory($product_id)), 'SSL');
            }


            //Commented because links are added to products after finding category
            //  $this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
            $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

            $data['heading_title'] = $product_info['name'];
            $data['text_question'] = $this->language->get('text_question');
            $data['text_previously_ordered'] = $this->language->get('text_previously_ordered');
            $data['text_return'] = $this->language->get('text_select');
            $data['text_no_return'] = $this->language->get('text_no_return');
            $data['text_select'] = $this->language->get('text_select');
            $data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $data['text_model'] = $this->language->get('text_model');
            $data['text_points'] = $this->language->get('text_points');
            $data['text_stock'] = $this->language->get('text_stock');
            $data['text_discount'] = $this->language->get('text_discount');
            $data['text_discount_post'] = $this->language->get('text_discount_post');
            $data['text_tax'] = $this->language->get('text_tax');
            $data['text_inc_tax'] = $this->language->get('text_inc_tax');
            $data['text_plus_cst'] = $this->language->get('text_plus_cst');
            $data['text_option'] = $this->language->get('text_option');
            $data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
            $data['text_write'] = $this->language->get('text_write');
            $data['single_txt_write'] = $this->language->get('single_txt_write');
            $data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
            $data['text_note'] = $this->language->get('text_note');
            $data['text_tags'] = $this->language->get('text_tags');
            $data['text_related'] = $this->language->get('text_related');
            $data['text_loading'] = $this->language->get('text_loading');
            $data['text_moq_default'] = $this->language->get('text_moq_default');
            $data['text_moq_pre'] = $this->language->get('text_moq_pre');
            $data['text_moq_post'] = $this->language->get('text_moq_post');
            $data['text_per_piece'] = $this->language->get('text_per_piece');
            $data['text_withoutslash_piece'] = $this->language->get('text_withoutslash_piece');
            $data['text_per_set'] = $this->language->get('text_per_set');
            $data['text_one_set'] = $this->language->get('text_one_set');
            $data['text_pieces'] = $this->language->get('text_pieces');
            $data['text_see_desc'] = $this->language->get('text_see_desc');
            $data['text_browse_more'] = $this->language->get('text_browse_more');
            $data['text_featured'] = $this->language->get('text_featured');
            $data['text_in_stock'] = sprintf($this->language->get('text_in_stock'), $product_info['quantity']);
            $data['text_sold_out'] = sprintf($this->language->get('text_sold_out'), $product_info['sold_out'] + $actualsold);
            $data['text_out_of_stock'] = $this->language->get('text_out_of_stock');
            $data['text_available_set'] = $this->language->get('text_available_set');
            $data['text_available_pieces'] = $this->language->get('text_available_pieces');
            $data['text_available_after'] = $this->language->get('text_available_after');
            $data['text_days'] = $this->language->get('text_days');

            $data['entry_qty'] = $this->language->get('entry_qty');
            $data['entry_price'] = $this->language->get('entry_price');
            $data['entry_name'] = $this->language->get('entry_name');
            $data['entry_review'] = $this->language->get('entry_review');
            $data['entry_rating'] = $this->language->get('entry_rating');
            $data['entry_good'] = $this->language->get('entry_good');
            $data['entry_bad'] = $this->language->get('entry_bad');
            $data['text_return'] = $this->language->get('text_return');
            $data['text_no_return'] = $this->language->get('text_no_return');
            $data['text_single'] = $this->language->get('text_single');
            $data['text_wholesaleBox'] = $this->language->get('text_wholesaleBox');
            $data['text_recently_viewed'] = $this->language->get('text_recently_viewed');
            $data['text_cod_available'] = $this->language->get('text_cod_available');

            $data['button_cart'] = $this->language->get('button_cart');
            $data['button_wishlist'] = $this->language->get('button_wishlist');
            $data['button_compare'] = $this->language->get('button_compare');
            $data['button_upload'] = $this->language->get('button_upload');
            $data['button_continue'] = $this->language->get('button_continue');

            $this->load->model('catalog/review');

            $data['tab_description'] = $this->language->get('tab_description');
            $data['tab_attribute'] = $this->language->get('tab_attribute');
            $data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);
            $data['review_count'] = $product_info['reviews'];
            $data['product_id'] = (int)$this->request->get['product_id'];
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
            $data['saving_money'] = $product_info['saving_money'];
            $data['selling_price'] = $this->currency->format($product_info['selling_price']);
            $data['tax_per_piece'] = $this->currency->format($product_info['tax_per_piece']);

            $data['column_mrp'] = $this->language->get('column_mrp');
            $data['column_our_price'] = $this->language->get('column_our_price');
            $data['column_save_money'] = $this->language->get('column_save_money');
            $data['exp_final_date'] = $product_info['exp_dispatch_days'];

            if ($product_info['quantity'] <= 0) {
                $data['stock'] = $product_info['stock_status'];
                $data['quantity'] = 0;
            } elseif ($this->config->get('config_stock_display')) {
                $data['stock'] = $product_info['quantity'];
                $data['quantity'] = $product_info['quantity'];
            } else {
                $data['stock'] = $this->language->get('text_instock');
                $data['quantity'] = $product_info['quantity'];
            }
            if ($product_info['vacation_mode'] == 1) {
                $data['stock'] = $product_info['stock_status'];
                $data['quantity'] = 0;
            }

            $this->load->model('tool/image');

            if ($product_info['image']) {
                $data['original'] = $this->model_tool_image->getOriginalImage($product_info['image']);
                $dimensions = unserialize($product_info['image_dimensions']);
                if(!empty($dimensions['width']))
                    $width_orig = $dimensions['width'];
                if(!empty($dimensions['height']))
                    $height_orig = $dimensions['height'];
                //if (file_exists(DIR_IMAGE . $product_info['image'])) {
                 //   list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $product_info['image']);
                //}

                if (!empty($width_orig) && !empty($height_orig)
                    && ($width_orig / $height_orig) > 1
                ) {
                    $data['wide_image'] = true;
                    $data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_height'), $this->config->get('config_image_popup_width'));
                    $data['pan_detail'] = $this->model_tool_image->resize($product_info['image'], 500, 333);
                    $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_height'), $this->config->get('config_image_additional_width'));
                    $data['popup_img_width'] = 500;//$this->config->get('config_image_popup_height');
                    $data['popup_img_height'] = 333;//$this->config->get('config_image_popup_width');
                    $data['thumb_img_width'] = $this->config->get('config_image_additional_height');
                    $data['thumb_img_height'] = $this->config->get('config_image_additional_width');
                } else {
                    $data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
                    $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'));
                    $data['pan_detail'] = $this->model_tool_image->resize($product_info['image'], 267, 400);

                    $data['popup_img_width'] = 267;//$this->config->get('config_image_popup_width');
                    $data['popup_img_height'] = 400;//$this->config->get('config_image_popup_height');
                    $data['thumb_img_width'] = $this->config->get('config_image_additional_width');
                    $data['thumb_img_height'] = $this->config->get('config_image_additional_height');
                }


            } else {
                $data['popup'] = '';
                $data['thumb'] = '';
                $data['original'] = '';
            }
            $data['images'] = array();

            $results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

            if ($product_info['image']) {
                $data['images'][] = array(
                    'popup' => $data['popup'],
                    'thumb' => $data['thumb'],
                    'pan_detail' => $data['pan_detail'],
                    'original' => $data['original'],
                    'popup_img_width' => $data['popup_img_width'],
                    'popup_img_height' => $data['popup_img_height'],
                    'thumb_img_width' => $data['thumb_img_width'],
                    'thumb_img_height' => $data['thumb_img_height']
                );
            }

            foreach ($results as $result) {
                if (isset($data['wide_image'])) {
                    $data['images'][] = array(
                        'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_height'), $this->config->get('config_image_popup_width')),
                        'pan_detail' => $this->model_tool_image->resize($result['image'], 500, 333),

                        'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_height'), $this->config->get('config_image_additional_width')),
                        'original' => $this->model_tool_image->getOriginalImage($result['image']),
                        'popup_img_width' => $this->config->get('config_image_popup_height'),
                        'popup_img_height' => $this->config->get('config_image_popup_width'),
                        'thumb_img_width' => $this->config->get('config_image_additional_height'),
                        'thumb_img_height' => $this->config->get('config_image_additional_width')
                    );
                } else {
                    $data['images'][] = array(
                        'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
                        'pan_detail' => $this->model_tool_image->resize($result['image'], 267, 400),
                        'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height')),
                        'original' => $this->model_tool_image->getOriginalImage($result['image']),
                        'popup_img_width' => $this->config->get('config_image_popup_width'),
                        'popup_img_height' => $this->config->get('config_image_popup_height'),
                        'thumb_img_width' => $this->config->get('config_image_additional_width'),
                        'thumb_img_height' => $this->config->get('config_image_additional_height')
                    );
                }
            }
            //echo "<pre>"; print_r($data['images']);die;

            if ($this->customer->isLogged())
                $data['previously_ordered'] = $this->model_catalog_product->checkPreviouslyOrdered($this->customer->getId(), $product_info['product_id']);
            else
                $data['previously_ordered'] = false;


            $seller_tax_factor = 1.0 + ((float)$product_info['seller_tax'] / 100.0);
            $commission_factor = 1.0 + ((float)$product_info['commission'] / 100.0);

            $piece_in_set = (int)$product_info['piece_in_set'] > 1 ? (int)$product_info['piece_in_set'] : 1;
            $unit_price = ceil($commission_factor * (float)($product_info['price']) / $seller_tax_factor);
            //$tax_rate = $this->tax->getMajorTaxRate($product_info['tax_class_id']);
            $data['unformatted_price'] = $product_info['selling_price'];
            $data['price_value'] = $product_info['selling_price'];
            $data['price'] = $this->currency->format($product_info['selling_price']);
//            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
//                $data['price'] = $this->currency->format($this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax')));
//                $data['price_value'] = $this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax'));
//                $data['unformatted_price'] = $this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax'));
//            } else {
//                $data['price'] = false;
//                $data['price_value'] = false;
//                $data['unformatted_price'] = false;
//            }

            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                //$data['price_per_set'] = $this->currency->format($this->tax->calculate($unit_price * $piece_in_set, $product_info['tax_class_id'], $this->config->get('config_tax')));
                $data['price_per_set'] = $this->currency->format($product_info['selling_price']*$piece_in_set);
            } else {
                $data['price_per_set'] = false;
            }

            if ((float)$product_info['special']) {
                $unit_special_price = (float)$product_info['special']; //ceil($commission_factor * (float)($product_info['special']) / $seller_tax_factor);
                $data['special'] = $this->currency->format($this->tax->calculate($unit_special_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
                $data['percent_discount'] = $this->model_catalog_product->calculateSpecialPriceValueInPercent($unit_special_price, $product_info['selling_price'], $product_info['tax_class_id']);
                $data['special_per_set'] = $this->currency->format($this->tax->calculate($unit_special_price * $piece_in_set, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
            } else {
                $data['special_per_set'] = false;
                $data['percent_discount'] = false;
                $data['special'] = false;
            }

            $data['tax_class_id'] = $product_info['tax_class_id'];

            if ($this->config->get('config_tax')) {
                $data['tax'] = $this->currency->format((float)$product_info['special'] ? ceil($commission_factor * (float)($product_info['special']) / $seller_tax_factor) : ceil($commission_factor * (float)($product_info['price']) / $seller_tax_factor));
            } else {
                $data['tax'] = false;
            }

            $data['discounts'] = array();

            if ($this->config->get('config_store_id') != SOR_STORE_ID) {

                $discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id'], 0);


                foreach ($discounts as $discount) {
                    $data['discounts'][] = array(
                        'quantity' => $discount['quantity'],
                        'price' => $this->currency->format($this->tax->calculate(ceil($commission_factor * (float)$discount['price'] / $seller_tax_factor), $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']))
                    );
                }
            }

            $data['options'] = array();

            foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
                $product_option_value_data = array();
                foreach ($option['product_option_value'] as $option_value) {
                    if (!$option_value['subtract'] || ($option_value['quantity'] >= 0)) {
                        if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                            if ($option_value['price_prefix'] == "+") {
                                $price = $this->currency->format($this->tax->calculate(ceil($commission_factor * (float)($option_value['price']) / $seller_tax_factor), $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false, $product_info['mrp']));
                                $unfor_option_price = $this->tax->calculate(ceil($commission_factor * (float)($option_value['price']) / $seller_tax_factor), $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false, $product_info['mrp']);
                            } else {
                                $price = $this->currency->format($this->tax->calculate(floor($commission_factor * (float)($option_value['price']) / $seller_tax_factor), $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false, $product_info['mrp']));
                                $unfor_option_price = $this->tax->calculate(floor($commission_factor * (float)($option_value['price']) / $seller_tax_factor), $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false, $product_info['mrp']);

                            }
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
                    'required' => $option['required']
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

            $data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
            $data['rating'] = (int)$product_info['rating'];
            $data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
            $data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
            $data['set_description'] = $product_info['set_description'];
            $data['is_single'] = $product_info['is_single'];
            $data['product_name'] = $product_info['name'];
            $data['product_image'] = $product_info['image'];
            $data['rating'] = $product_info['rating'];
            $data['reviews'] = $product_info['reviews'];
            $data['schema_price'] = $product_info['selling_price'];

            $ga_id = 'wsb-addtocart-from-detail-'.strtolower($product_info['model']);
            if (isset($this->request->get['popup'])) {

                $ga_id = 'wsb-addtocart-from-detailpopup-'.strtolower($product_info['model']);
            }
            $data[ 'cart_tracking_id_for_ga'] = $ga_id;
            //echo "<pre>"; print_r($product_info); exit;
            /* Added by
            * Kuldeep
             */
            $filters = $this->model_catalog_product->getProductFiltersData($product_id);
            $data['filters'] = $filters;

            if($data['popup_view'] == false){
                $data['products'] = array();

                $results = $this->model_catalog_product->getRelatedBySellerAndCategory($this->request->get['product_id']);

                foreach ($results as $result) {
                    if ($result['image']) {
                        $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
                        $image_medium = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                        $img_releted_width = 125;
                        $img_releted_height = $this->config->get('config_image_product_height');
                    } else {
                        $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
                        $image_medium = '';
                        $img_releted_width = '';
                        $img_releted_height = '';
                    }

                    $seller_tax_factor = 1.0 + ((float)$result['seller_tax'] / 100.0);
                    $commission_factor = 1.0 + ((float)$result['commission'] / 100.0);

                    $piece_in_set = (int)$result['piece_in_set'] > 1 ? (int)$result['piece_in_set'] : 1;
                    $unit_price = ceil($commission_factor * (float)($result['price']) / $seller_tax_factor);

                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
                        $unformatted_price = $this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
                    } else {
                        $price = false;
                        $unformatted_price = false;
                    }

                    if ((float)$result['special']) {
                        $unit_special_price = ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
                        $special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
                    } else {
                        $special = false;
                    }

                    if ($this->config->get('config_tax')) {
                        $tax = $this->currency->format((float)$result['special'] ? ceil($commission_factor * (float)($result['special']) / $seller_tax_factor) : ceil($commission_factor * (float)($result['price']) / $seller_tax_factor));
                    } else {
                        $tax = false;
                    }

                    if ($this->config->get('config_review_status')) {
                        $rating = (int)$result['rating'];
                    } else {
                        $rating = false;
                    }
                    $url_related = '';

                    if (isset($this->request->get['popup'])) {
                        $url_related .= '&popup=true';

                    }


                    $data['products'][] = array(
                        'product_id' => $result['product_id'],
                        'thumb' => $image,
                        'image_medium' => $image_medium,
                        'name' => $result['name'],
                        'set_description' => $result['set_description'],
                        'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
                        'price' => $price,
                        'unformatted_price' => $unformatted_price,
                        'special' => $special,
                        'piece_in_set' => $piece_in_set,
                        'tax' => $tax,
                        'tax_class_id' => $result['tax_class_id'],
                        'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
                        'rating' => $rating,
                        'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url_related, 'SSL'),
                        'img_releted_width' => $img_releted_width,
                        'img_releted_height' => $img_releted_height,


                    );
                }
            }

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag), 'SSL')
					);
				}
			}

			$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

            if($data['popup_view'] == false)
            {
                if ($this->config->get('config_google_captcha_status')) {
                    $this->document->addScript('https://www.google.com/recaptcha/api.js');

                    $data['site_key'] = $this->config->get('config_google_captcha_public');
                } else {
                    $data['site_key'] = '';
                }

                $this->_setMetaTags($product_info, $data);

                $data['column_left'] = $this->load->controller('common/column_left');
                $data['column_right'] = $this->load->controller('common/column_right');

                $data['content_bottom'] = $this->load->controller('common/content_bottom');
                $data['header'] = $this->load->controller('common/header');
                //$data['viewed'] = $this->load->controller('module/viewed');
                //$data['schema_org'] = $this->load->controller('module/schema_org', $data);
                //echo $data['schema_org']; die;
                //$this->document->addAdditionalScript($data['schema_org']);
                $data['footer'] = $this->load->controller('common/footer');
            }
            $data['content_top'] = $this->load->controller('common/content_top');

			//echo $data['schema_org'];
			$data['downloadImages'] = $this->url->link('product/product/downloadProductImages','product_id=' . $product_id, 'SSL');

			// for comment popup
			$data['comment_popup_heading'] = $this->language->get('comment_popup_heading');
			$data['question_popup_heading'] = $this->language->get('question_popup_heading');

			$data['comment_popup_send'] = $this->language->get('comment_popup_send');
			$data['i_want_this_design'] = $this->language->get('i_want_this_design');
			// $data['landscape_photos'] = $this->load->controller('product/product_detail_top');
			// echo "<pre>"; print_r($data['options']); die;

			if ($data['options']) {
				$data['options_tpl'] = $this->load->view($this->config->get('config_template') . '/template/product/product_options_vertical.tpl', $data);
			} else {
				$data['options_tpl'] = $this->load->view($this->config->get('config_template') . '/template/product/product_no_options.tpl', $data);
			}
			// if (isset($data['wide_image'])) {
			// 	$data['landscape_photos'] = $this->load->view($this->config->get('config_template') . '/template/product/product_detail_top_landscape.tpl', $data);
			// } else {
			$data['landscape_photos'] = $this->load->view($this->config->get('config_template') . '/template/product/product_detail_top_portrait.tpl', $data);
			// }

			// echo $data['landscape_photos']; die;




			/**Wishlist **/
			$data['fill_heart'] = $this->customer->getWishlistIcon($product_id);

            $product_hotness = new ProductHotness($this->db);
            $product_hotness->updateHotness("see-product-detail",$product_id);
			
			if($data['popup_view'] == true){
				if (isset($data['wide_image'])) {
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product_popup.tpl')) {
						$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/product_popup.tpl', $data));
					} else {
						$this->response->setOutput($this->load->view('default/template/product/product_popup.tpl', $data));
					}
				} else {
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product_popup.tpl')) {
						$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/product_popup.tpl', $data));
					} else {
						$this->response->setOutput($this->load->view('default/template/product/product_popup.tpl', $data));
					}
				}
			}else {

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product_detail.tpl')) {
					$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/product_detail.tpl', $data));
				} else {
					$this->response->setOutput($this->load->view('default/template/product/product_detail.tpl', $data));
				}
			}
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id, 'SSL')
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home', '', 'SSL');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			//$data['viewed'] = $this->load->controller('module/viewed');
			$data['downloadImages'] = $this->url->link('product/product/downloadProductImages', 'product_id=' . $product_id, 'SSL');


			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/error/not_found.tpl', $data));
			}
		}
	}

	public function review() {
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		$data['text_no_reviews'] = $this->language->get('text_no_reviews');


		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/review.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/review.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/product/review.tpl', $data));
		}
	}

	public function getRelatedProducts()
    {
        $pid = $this->request->get['product_id'];
        if($pid)
        {
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $this->load->language('product/product');

            $data = array();
            $data['text_related'] = $this->language->get('text_related');
            $data['text_tax'] = $this->language->get('text_tax');
            $data['button_wishlist'] = $this->language->get('button_wishlist');
            $data['button_compare'] = $this->language->get('button_compare');

            $results = $this->model_catalog_product->getRelatedBySellerAndCategory($this->request->get['product_id']);

            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
                    $image_medium = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                    $img_releted_width = 125;
                    $img_releted_height = $this->config->get('config_image_product_height');
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
                    $image_medium = '';
                    $img_releted_width = '';
                    $img_releted_height = '';
                }

                $seller_tax_factor = 1.0 + ( (float)$result['seller_tax'] / 100.0 );
                $commission_factor = 1.0 + ( (float)$result['commission'] / 100.0 );

                $piece_in_set = (int)$result['piece_in_set'] > 1 ? (int)$result['piece_in_set'] : 1;
                $unit_price =  ceil($commission_factor * (float)($result['price']) / $seller_tax_factor);

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
                    $unformatted_price = $this->tax->calculate($unit_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']);
                } else {
                    $price = false;
                    $unformatted_price =false;
                }

                if ((float)$result['special']) {
                    $unit_special_price = ceil($commission_factor * (float)($result['special']) / $seller_tax_factor);
                    $special = $this->currency->format($this->tax->calculate($unit_special_price, $result['tax_class_id'], $this->config->get('config_tax'), $result['mrp']));
                } else {
                    $special = false;
                }

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float)$result['special'] ? ceil($commission_factor * (float)($result['special']) / $seller_tax_factor) : ceil($commission_factor * (float)($result['price']) / $seller_tax_factor));
                } else {
                    $tax = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = (int)$result['rating'];
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
                if($result['vacation_mode'] == 1){
                    $productStock = $result['stock_status'];
                    $productQuantity = 0;
                }


                $data['products'][] = array(
                    'product_id'  => $result['product_id'],
                    'thumb'       => $image,
                    'image_medium'=> $image_medium,
                    'name'        => $result['name'],
                    'set_description' => $result['set_description'],
                    'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
                    'price'       => $price,
                    'unformatted_price' => $unformatted_price,
                    'special'     => $special,
                    'piece_in_set'=> $piece_in_set,
                    'tax'         => $tax,
                    'tax_class_id'=> $result['tax_class_id'],
                    'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
                    'rating'      => $rating,
                    'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'].$url_related, 'SSL'),
                    'img_releted_width' => $img_releted_width,
                    'img_releted_height' => $img_releted_height,
                    'stock' => $productStock,
                    'quantity' => $productQuantity,
                    'row_data' => $result
                );
            }

            $this->response->setOutput($this->load->view('default/template/product/related_products.tpl', $data));
        }
    }

    public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			if ($this->config->get('config_google_captcha_status') && empty($json['error'])) {
				if (isset($this->request->post['g-recaptcha-response'])) {
					$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->config->get('config_google_captcha_secret')) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->getIpAddress);

					$recaptcha = json_decode($recaptcha, true);

					if (!$recaptcha['success']) {
						$json['error'] = $this->language->get('error_captcha');
					}
				} else {
					$json['error'] = $this->language->get('error_captcha');
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->language->load('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax', $product_info['mrp'])));
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function soldout(){

		$this->load->model('catalog/product');
		$this->model_catalog_product->soldout();

	}

	public function setqtydiscount(){

		$this->load->model('catalog/product');
		$this->model_catalog_product->setQtyDiscount();

	}
	/**
	* This function take each product and replaces with the seo url in url alias table
	* @return void
	*/

	public function setSeoUrl(){
		$arr_product_ids = array();
		$url_array = array();
		$this->load->model('catalog/product');
		$results = $this->model_catalog_product->getProductsWithSeoUrl();

		foreach ($results->rows as $result){
			 $arr_query = explode("=", $result['query']);
			 $arr_product_ids[] = $arr_query[1] ;

		}

		$results_new = $this->model_catalog_product->getProductsWithoutSeoUrl($arr_product_ids);

		foreach ($results_new->rows as $result){
			$name = str_replace(" ","-",strtolower($result['name']));
			$name_after_apostrophe = str_replace("'","",$name);
			$name_after_regex = preg_replace('/[^a-zA-Z0-9- \n\.]/', "", $name_after_apostrophe);
			$model = str_replace("_", "", strtolower($result['model']));
			$model_after_apostrophe = str_replace("'","",$model);
			$model_after_regex = preg_replace('/[^a-zA-Z0-9- \n\.]/', "",$model_after_apostrophe );
			$seo_line = $name_after_regex."-" . $model_after_regex ;
			$url_array[$result['product_id']] = $seo_line;
		}

		$this->model_catalog_product->insertIntoProductsUrl($url_array);
	}

	// New line by Parth

	/**
     * Download Product Images
     * @param  product_id
     * @return Images Download
     * @author Ravindra Singh
     */
    public function downloadProductImages(){

		if(isset($this->request->get['product_id']) && !empty($this->request->get['product_id'])){
			$product_id = (int)$this->request->get['product_id'];
		}else{
			$product_id = 0;
		}

		$this->load->model('catalog/product');
		//$product_id = '55';
		$pimages = $this->model_catalog_product->downloadProductImages($product_id);
		return true;
	}
	public function zoomImage(){
		$data['image'] = $this->request->get['image'];

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/zoom_image.tpl')) {

			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/zoom_image.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/product/zoom_image.tpl', $data));
		}

	}

	public function askAQuestion() {

		$this->load->model('catalog/product');
		$this->load->language('product/product');

		$customer_id = false;
		if($this->customer->isLogged()){
			$customer_id = $this->customer->getId();
		}

		$customer_name = $this->request->post['customer_name'];
		$customer_telephone = $this->request->post['telephone'];
		$customer_email = $this->request->post['email'];
		$product_id = $this->request->post['product_id'];
		$popup_question = $this->request->post['popup_question'];

		$json = array();

		$data_user_comment = array(
				'customer_id'	=> $customer_id,
				'customer_name' => $customer_name,
				'telephone' 	=> $customer_telephone,
				'email'      	=> $customer_email,
				'product_id' 	=> $product_id,
				'popup_question'=> $popup_question,

		);
		$this->load->model('tool/image');
		$getInformation = $this->model_catalog_product->askAQuestion($data_user_comment);

		$json = array(
			'product_id' => $product_id,
			'message'=> $this->language->get('ask_question_message')
		);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function testAddProductToSolr(){
		$this->load->model('catalog/product');

		$data_j = '{"product_id":"11926", "product_description":{"1":{"name":"Test for Solr","set_description":"1 set = 2 pcs each of 38, 40","description":"<p>First product to solr&nbsp;&nbsp;&nbsp;&nbsp;<br><\/p>","meta_title":"Meta tag","meta_description":"meta_description","meta_keyword":"meta_title","tag":""},"2":{"name":"","set_description":"","description":"<p><br><\/p>","meta_title":"","meta_description":"","meta_keyword":"","tag":""}},"image":"","model":"TEST_123","sku":"test123","upc":"","ean":"","jan":"","isbn":"","mpn":"","location":"","price":"100","selling_price":"110","piece_in_set":"2","seller_tax":"0","commission":"5","tax_class_id":"9","quantity":"100","single":"0","minimum":"1","subtract":"1","stock_status_id":"7","shipping":"1","keyword":"","date_available":"2016-08-04","length":"","width":"","height":"","length_class_id":"1","weight":"","weight_class_id":"1","status":"1","sort_order":"999","manufacturer":"","manufacturer_id":"0","category":"","product_category":["61","73","90"],"filter":"","product_filter":["175","256","23"],"product_store":["0"],"download":"","related":"","option":"","points":"","product_reward":{"1":{"points":""},"2":{"points":""},"3":{"points":""}},"product_layout":{"0":"","6":"","3":"","5":"","7":"","4":"","2":""},"store_id":" 3 ","Language":"","Meta_Tag_Title":"","Meta_Tag_Keywords":"","Meta_Tag_Description":""}';
		echo '<pre>';
		$data = json_decode($data_j);
		print_r($data);
		$this->model_catalog_product->addProductToSolr($data);
	}

	private function _setMetaTags($product_info, $data) {
        $product_name = $product_info['name'];
        $product_code = $product_info['model'];
        $product_image = $data['popup'];
        $base = $data['base'];
        $price = $data['price'];
        $product_url = $this->url->link('product/product', 'product_id=' . $product_info['product_id'], 'SSL');

        if($this->config->get('config_store_id') == INTERNATIONAL_STORE_ID) {

            $dynamic_title = $product_name . " - " . $product_code . " at lowest factory price  " . $price . ". This wholesale price is valid for retailers across the globe.";

            $dynamic_meta_description = "WholeSaleBox - Order" . $product_name . " - " . $product_code . "
                                         for your shop at wholesale price " . $price;
            $dynamic_keywords = $product_name . " - " . $product_code . " at wholesale price online";

            //$this->document->setTitle($product_info['meta_title']);
            //$this->document->setDescription($product_info['meta_description']);
            //$this->document->setKeywords($product_info['meta_keyword']);

            $this->document->setTitle($dynamic_title);
            $this->document->setDescription($dynamic_meta_description);
            $this->document->setKeywords($dynamic_keywords);

            //Set Social Meta Tags
            $social_media_tags = <<<EOT
            <meta property="og:title" content="$product_name - $product_code Fat Wholesale Price of $price">
            <meta property="og:image" content="$product_image">
            <meta property="og:url" content="$product_url">
            <meta property="og:site_name" content="$base">
            <meta property="og:description" content="$dynamic_title">
            <meta property="fb:app_id" content="1834233226846088">

            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:site" content="@WholesaleBox_in">
            <meta name="twitter:creator" content="@WholesaleBox_in">
            <meta name="twitter:title" content="$product_name - $product_code  at Wholesale Price of
            $price">
            <meta name="twitter:description" content="$dynamic_title">
            <meta name="twitter:image" content="$product_image">

EOT;
        } else {

            $dynamic_title = $product_name . " - " . $product_code . " For Shopkeepers at " . $price . " (WholeSale Price) as on " . Date('d-m-Y') . " | WholeSaleBox";

            $dynamic_meta_description = "WholeSaleBox - Order" . $product_name . " - " . $product_code . "
                                         for your shop at wholesale price " . $price . " This price
                                         is valid for shopkeepers/retailers across India including Mumbai, Delhi,
                                         Gurgaon, Bengaluru, Kolkata, Chennai.";
            $dynamic_keywords = $product_name . " - " . $product_code . " at wholesale price in India, "
                . $product_name . " - " . $product_code . " online,"
                . $product_name . " - " . $product_code . " at" . $price;

            //$this->document->setTitle($product_info['meta_title']);
            //$this->document->setDescription($product_info['meta_description']);
            //$this->document->setKeywords($product_info['meta_keyword']);

            $this->document->setTitle($dynamic_title);
            $this->document->setDescription($dynamic_meta_description);
            $this->document->setKeywords($dynamic_keywords);

            //Set Social Meta Tags
            $social_media_tags = <<<EOT
            <meta property="og:title" content="$product_name - $product_code For Shopkeepers at $price(WholeSale Price).">
            <meta property="og:image" content="$product_image">
            <meta property="og:url" content="$product_url">
            <meta property="og:site_name" content="$base">
            <meta property="og:description" content="WholeSaleBox - Order $product_name - $product_code for your shop at
            wholesale price $price. This price is valid for shopkeepers/retailers across India including Mumbai,
            Delhi, Gurgaon, Bengaluru, Kolkata, Chennai.">
            <meta property="fb:app_id" content="1834233226846088">

            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:site" content="@WholesaleBox_in">
            <meta name="twitter:creator" content="@WholesaleBox_in">
            <meta name="twitter:title" content="$product_name - $product_code For Shopkeepers at
            $price(WholeSale Price) in India">
            <meta name="twitter:description" content="WholeSaleBox - Order $product_name - $product_code for your shop at
            wholesale price $price. This price is valid for shopkeepers/retailers across India including Mumbai, Delhi,
            Gurgaon, Bengaluru, Kolkata, Chennai.">
            <meta name="twitter:image" content="$product_image">

EOT;
        }
        $this->document->setSocialMetaTags($social_media_tags);
    }

}
