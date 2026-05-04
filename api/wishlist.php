<?php
     require_once(__DIR__.'/system.php');
     require_once(DIR_SYSTEM.'library/solr/lookup.php');
     require_once(DIR_SYSTEM.'library/producthotness.php');

class WishlistController extends SystemController
{
        private $error = "";

        public function __construct($params) 
        {
            parent::__construct($params);
            //$this->registry->set('customer',new Customer($this->registry));
               // Currency
            //$this->registry->set('currency', new Currency($this->registry));
            // Tax
            //$this->registry->set('tax', new Tax($this->registry));
        }
        /**
         * customer login
         */

  public function getList() {

    $this->load->model('tool/image');
    $this->load->model('catalog/product'); 
    $this->load->language('account/wishlist');
    
    $customer_wishlist = array_column($this->customer->getWishlistItems()->rows,'product_id');

    $data['products'] = array();
    foreach ($customer_wishlist as $key => $product_id) {
      $product_info = $this->model_catalog_product->getProduct($product_id);
      if ($product_info) {
        if ($product_info['image']) {
          $image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
          $img_width  = $this->config->get('config_image_product_width');
          $img_height = $this->config->get('config_image_product_height');
          /*--- commented on (04-01-2016) And image get same as well as product_list --*/
        } else {
          $image = false;
          $img_width = '';
          $img_height= '';
        }

        if ($product_info['quantity'] <= 0) {
          $stock = $product_info['stock_status'];
        } elseif ($this->config->get('config_stock_display')) {
          $stock = $product_info['quantity'];
        } else {
          $stock = $this->language->get('text_instock');
        }

        $seller_tax_factor = 1.0 + ( (float)$product_info['seller_tax'] / 100.0 );
        $commission_factor = 1.0 + ( (float)$product_info['commission'] / 100.0 );

        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $unit_price =  ceil($commission_factor * (float)($product_info['price']) / $seller_tax_factor);
          $price = $this->currency->format($this->tax->calculate($unit_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
        } else {
          $price = false;
        }

        if ((float)$product_info['special']) {
                    $unit_special_price = ceil($commission_factor * (float)($product_info['special']) / $seller_tax_factor);
          $special = $this->currency->format($this->tax->calculate($unit_special_price, $product_info['tax_class_id'], $this->config->get('config_tax'), $product_info['mrp']));
        } else {
          $special = false;
        }
        $url_related = '';

       $product_options =  $this->model_catalog_product->getProductOptions($product_info['product_id']);

        if($product_info['base_unit'] != '')
          {
                  if($special)
                    {
                        $special =  $special . ' / ' . $product_info['base_unit'];
                        $price_with_unit =  $price; 
                    }
                    else
                    {
                      $price_with_unit =  $price . ' / ' . $product_info['base_unit'];  
                    }

              $set_description = '1 ' . $product_info['super_unit'] . " = " . $product_info['piece_in_set'] . ' ' .$product_info['base_unit'] ;
             $set_description .=  ', ' . $product_info['set_description'] ;
           }else
           {
              $price_with_unit = sprintf($this->language->get('text_per_piece'), $price);
              $set_description = $product_info['set_description'];
          }
                                  
                
        $data['products'][] = array(
          'product_id' => $product_info['product_id'],
          'thumb'      => $image,
          'image'      => $image,
          'name'       => mb_strimwidth(html_entity_decode($product_info['name']), 0, 25, "..."),
          'model'      => $product_info['model'],
          'is_sor_enabled' => $product_info['is_sor_enabled'],
          'sor_enabled_text' => $product_info['sor_enabled_text'],
          'sor_enabled_detail_text' => $product_info['sor_enabled_detail_text'],
          'set_description' => $product_info['set_description'],
          'stock'      => $stock,
          'price'      => $price_with_unit,
          'set_description' => $set_description,
          'piece_in_set'      => $product_info['piece_in_set'],
          'special'    => $special,
          'href' => $this->url->rewrite_keyword('product_id', $product_info['product_id'], 'SSL'),
          'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id']),
          'rating'   => $product_info['rating'],
          'cod_available'=> $product_info['cod_available'],
          'img_width' => $img_width,
          'img_height' =>  $img_height,
          'row_data' => $product_info,
          'minimum' => $product_info['minimum'],
          'quantity' => $product_info['quantity'],
          'options' => $product_options,
          'margin_percentage' => !empty($product_info['margin_percentage']) ? $product_info['margin_percentage'] : ''
        );
      }
    }

          $this->data_packet->data       = $data;
          $this->data_packet->message    = 'wishlist product successfully fetch';
          $this->data_packet->statusCode = 200;
          return $this->data_packet;
  }

  public function remove()
  {
      $product_id = $this->request['remove'];
      $this->customer->deleteProductFromWishlist($product_id);
      $product_hotness = new ProductHotness($this->db);
      $product_hotness->updateHotness("remove-from-wishlist",$product_id);

      $data['count']    = $this->customer->getTotalWishlists(); 
      $this->data_packet->data       = $data;
      $this->data_packet->message    = 'wishlist product remove successfully';
      $this->data_packet->statusCode = 200;
      return $this->data_packet;
  }

  public function clear_all()
  {
      $this->customer->clearAllProductFromWishlist();
      $product_hotness = new ProductHotness($this->db);
      $data['count']    = $this->customer->getTotalWishlists(); 
      $this->data_packet->data       = $data;
      $this->data_packet->message    = 'wishlist product remove successfully';
      $this->data_packet->statusCode = 200;
      return $this->data_packet;
  }

 }