<?php

/**
 * Main Class for getting all Order Product Related Info.
 * specfic methods in this class fetching a particular data.
 * @author Nishu, Aug2018
 */
class OrderProduct {

  private $_registry = null;
  private $_db       = null;
  private $_default_selector = array(
                                    'order_product' => array(
                                                         'select' => array(
                                                                        'product_id', 
                                                                        'name'
                                                                     )
                                                       )
                               );

  public function __construct(Controller $controller) {
    $this->_registry = $controller;
    $this->_db       = $controller->db;
  }

  /**
   * General method to get Order product option given order_product_id
   * Public static method. No need to create Class object to call this method.
   * @param:
   *          $db - Database object (required)
   *          $order_product_id - Order ID (int, required)
   * @return: $data Array 
   * @author Nishu, Aug 2018
  */
  public static function getOrderProductOption(Database\DB $db, int $order_product_id) : array{
      $data = array();

      if(!empty($db) && !empty($order_product_id)){

          $sql = "
                  SELECT
                    oo.product_option_id,
                    oo.product_option_value_id,
                    oo.name,
                    oo.value,
                    pov.quantity,
                    p.image,
                    p.piece_in_set
                  FROM 
                    " . DB_PREFIX . "order_option oo
                  INNER JOIN 
                    " . DB_PREFIX . "product_option_value as pov
                      ON pov.product_option_value_id=oo.product_option_value_id
                  INNER JOIN 
                    " . DB_PREFIX . "order_product as op ON op.order_product_id=oo.order_product_id
                  INNER JOIN 
                    " . DB_PREFIX . "product p ON p.product_id=op.product_id
                  WHERE 
                    oo.order_product_id = ". (int)$order_product_id ." LIMIT 1";
          //Execute Query
          $result = $db->query($sql);
          //Check if query returned some result
          if($result->num_rows){
              $data = $result->row;
          }

      }
      return $data;
  }//End of getOrderProductOption()

  /**
   * @info: Public method to reorder (Already orderd product added to cart)
   *         Derived from account/order.php
   * @author: Nishu, Aug 2018  
  */
  public function reorderOrderProduct(array $data) : bool{

    $is_reorder = false;
  
    if(!empty($data['customer_id']) && !empty($data['order_id']) && !empty($data['order_product_id']) ) {
      
      $order_id         = (int)$data['order_id'];
      $order_product_id = (int)$data['order_product_id'];

      $order_product_info = OrderInfo::getOrderProductDetailsByOpId($this->_db, $order_product_id);

      if(!empty($order_product_info)) {
        $this->_registry->load->model('catalog/product');
        $this->_registry->load->model('account/order');

        $option_data = array();

        $order_options = $this->getOrderProductOptions((int)$order_product_info['order_id'], (int)$order_product_id);

        foreach ($order_options as $order_option) {
          if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
            $option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
          } elseif ($order_option['type'] == 'checkbox') {
            $option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
          } elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
            $option_data[$order_option['product_option_id']] = $order_option['value'];
          } elseif ($order_option['type'] == 'file') {
            $option_data[$order_option['product_option_id']] = $this->_registry->encryption->encrypt($order_option['value']);
          }
        }

        if( !empty($option_data) ){
          $product_option = key($option_data);
          $product_option_value = $option_data[key($option_data)];
        }else{
          $product_option = '';
          $product_option_value = '';
        }
        
        //Add to Cart 
        $cart_data = array(
                        'customer_id'          => $data['customer_id'],
                        'cart_session_id'      => '',
                        'access_token'         => '',
                        'product_id'           => $order_product_info['combo_product_id'],
                        'product_quantity'     => 1,
                        'product_option'       => $product_option,
                        'product_option_value' => $product_option_value,
                        'cart_modified_from'   => 'DWEB'
                      );

        $cart = new Cart($this->_registry);
        if($cart->addToCart($cart_data)){
          $is_reorder = true;
        }
      }
    }
    //Default value set to false
    return $is_reorder;
  }
        
  /**
   * Public method to get Order Options
   * @param: $order_id, $order_product_id
   * @author: Nishu, Aug 2018
  */
  public function getOrderProductOptions(int $order_id, int $order_product_id) : array{
    $sql = "
            SELECT 
               *, pov.option_image 
            FROM 
              " . DB_PREFIX . "order_option oo
            INNER JOIN 
              ".DB_PREFIX."product_option_value pov ON(oo.product_option_value_id = pov.product_option_value_id) 
            WHERE 
              order_id = '" . (int)$order_id . "' 
              AND order_product_id = '" . (int)$order_product_id . "'
           ";
    $result = $this->_db->query($sql);

    return $result->rows;
  }

  /**
   * Public method to get order product details by suborder_id
   * @param:  $suborder_id String
   * @return: $data Array
   * @author: Nishu, Aug 2018
  */
  public static function getOrderProductBySuborderId(Database\DB $db, int $order_id, string $suborder_id, bool $get_all_product) : array{
    $data = array();

    if(!empty($suborder_id) ){
        $sql = "
                SELECT  
                  op.product_id,
                  op.name,
                  op.order_product_id,
                  op.order_id,
                  op.model,
                  op.suborder_id,
                  op.name,
                  op.quantity,
                  op.piece_in_set,
                  op.price_per_piece,
                  op.discount_per_piece,
                  op.discount_breakup,
                  op.weight_per_piece,
                  op.tax,
                  op.seller_sku,
                  op.comment,
                  op.output_tax_rates,
                  op.store_pickup,
                  op.edit_type,
                  op.edit_history, 
                  op.hsn_code, 
                  op.buyer_invoice_id, 
                  op.customer_comment,
                  op.seller_id, 
                  ms.nickname as seller_nickname,
                  op.seller_invoice_id, 
                  op.sor_product,
                  op.combo_product_id,
                  opr.product_review,
                  opr.product_review_date,
                  u.unit_id,
                  u.super_unit,
                  u.base_unit,
                  op.transfer_price_per_piece,
                  ms.non_returnable AS seller_non_returnable
                FROM 
                  " . DB_PREFIX . "order_product AS op 
                LEFT JOIN 
                  " . DB_PREFIX . "units AS u ON u.unit_id = op.unit_id
                LEFT JOIN 
                  " . DB_PREFIX . "order_product_review  opr ON opr.order_product_id = op.order_product_id
                INNER JOIN 
                  " . DB_PREFIX . "ms_seller ms ON ms.seller_id = op.seller_id  
                WHERE
                  op.order_id = '" . (int) $order_id . "' 
                  AND op.suborder_id = '" . $db->escape($suborder_id) . "'
              ";

        if (!$get_all_product) {
            $sql .= " AND (op.edit_type = 'YES' 
                            OR op.edit_type = 'SELLER_LATER_DISPATCH' 
                            OR op.edit_type = 'SELLER_APPROVED' 
                            OR op.edit_type = 'SELLER_PARTIAL' 
                            OR op.buyer_invoice_id > 0)  ";
        }
        $sql .= " ORDER BY op.order_product_id ASC";

        $result = $db->query($sql);
        if($result->num_rows > 0){
          $data = $result->rows;
        }
    }
    
    return $data;
  }

  /**
   * Public method to calculate product's amount by given row details for order_product
   * @param:  $data Array
   * @return: $result Array
   * @author: Nishu, Aug 2018
  */
  public static function calculateOrderProductAmountBreakup(Controller $controller, array $data) : array{
    $result = array();
    if(!empty($data)){
      //Set order_product_ids as key of main array
      $data = array_combine(array_column($data, 'order_product_id'), $data);

      //Get Order_id 
      $order_ids = array_unique(array_column($data, 'order_id'));
      $order_id  = $order_ids[0];
      
      $selector = array(
                    'order' => array('select' => array(
                                                   'currency_code',
                                                   'currency_value'
                                                   )
                               )
                  );
      $order_details  = OrderInfo::getOrderInfo($controller->db, $order_id, '',$selector);
      $currency_code  = $order_details['order']['currency_code'] ?? 'INR';
      $currency_value = $order_details['order']['currency_value'] ?? 1;

      //Loop over $data
      foreach ($data as $op_id => $value) {
        $result[$op_id] = $value;

        $total_pieces = (int)$value['quantity'] * (int)$value['piece_in_set'];
        $amount       = (float)$value['price_per_piece'];
        $discount     = (float)$value['discount_per_piece'];
        $tax_rate     = (float)$value['output_tax_rates'];
        $tax_val      = ($amount + $discount) * $tax_rate / 100;
        $total_amount = $total_pieces * ($amount + $discount + $tax_val);

        $result[$op_id]['amount_beakup']['pieces']             = array(
                                                                  'key'   => 'Total Pieces',
                                                                  'value' => $total_pieces,
                                                                  'row_value' => $total_pieces
                                                                );
        $result[$op_id]['amount_beakup']['price_per_piece']    = array(
                                                                  'key'   => 'Amount Per Piece',
                                                                  'value' => $controller->currency->format(round($amount, 2), $currency_code, $currency_value),
                                                                  'row_value' => round($amount, 2)
                                                                );
        $result[$op_id]['amount_beakup']['discount_per_piece'] = array(
                                                                  'key'   => 'Discount Per Piece',
                                                                  'value' => $controller->currency->format(round($discount, 2), $currency_code, $currency_value),
                                                                  'row_value' => round($discount, 2)
                                                                );
        $result[$op_id]['amount_beakup']['tax_rate']           = array(
                                                                  'key'   => 'Tax Rate',
                                                                  'value' => round($tax_rate, 2).'%',
                                                                  'row_value' => round($tax_rate, 2)
                                                                );
        $result[$op_id]['amount_beakup']['tax_val']            = array(
                                                                  'key'   => 'Tax Value',
                                                                  'value' => $controller->currency->format(round($tax_val, 2), $currency_code, $currency_value),
                                                                  'row_value' => round($tax_val, 2) 
                                                                );
        $result[$op_id]['amount_beakup']['total_amount']       = array(
                                                                  'key'   => 'Total Amount',
                                                                  'value' => $controller->currency->format(round($total_amount, 2), $currency_code, $currency_value),
                                                                  'row_value' => round($total_amount, 2)
                                                                );
      }

    }
    return $result;
  }

  /**
   * Public method to set extra links order product wise, such as like, dislike, download_images and reorder
   * @param:  $data Array
   * @return: $result Array
   * @author: Nishu, Aug 2018
  */
  public static function setExtraLinksForOrderProduct(URL $url, array $data, int $customer_id) : array{
    $result = array();
    if(!empty($data)){
      //Set order_product_ids as key of main array
      $data = array_combine(array_column($data, 'order_product_id'), $data);

      //Loop over $data
      foreach ($data as $op_id => $value) {
        $value['customer_id'] = $customer_id;
        $product_id           = (int)$value['product_id'];
        
        $result[$op_id]       = $value;

        //Set Like Dislike link OrderProduct Wise
        $result[$op_id] = self::setOrderProductLikeDislikeLink( $result[$op_id] );
        
        //Set reorder link for order_product
        $result[$op_id] = self::setOrderProductReorderLink( $result[$op_id] );

        //Set Download images link for order_product
        $result[$op_id]['download_images'] = self::setProductImageDownloadLinkByProduct_id( (int)$result[$op_id]['product_id'] );

        //Set Product Link
        $result[$op_id]['link'] = str_replace('&amp;', '&', $url->link('product/product', 'product_id=' . $product_id));
        
        //Set Product href
        $result[$op_id]['href'] = $url->rewrite_keyword('product_id', $product_id, 'SSL');

      }

    }
    return $result;
  }

  /**
   * Public method to set like dislike link
   * @param : $data Array
   * @return: $data Array
   * @author: Nishu, Aug 2018
  */
  private static function setOrderProductLikeDislikeLink(array $data) : array{
    
    if(!empty($data)){
      //Set Order Product Like Link
      $url_param = 'customer_id='.$data['customer_id'].'&order_id='.$data['order_id'].'&review=like&order_product_id='.$data['order_product_id'];

      //Generate Like API Link 
      $data['like_link'] = HTTPS_CATALOG.'api/customer_account/orders/orderProductLikeDislike';
      $data['like_link'] .= '&data=' . base64_encode($url_param);

      //Set Order Product Dislike Link
      $url_param = 'customer_id='.$data['customer_id'].'&order_id='.$data['order_id'].'&review=dislike&order_product_id='.$data['order_product_id'];

      //Generate Dislike API Link 
      $data['dislike_link'] = HTTPS_CATALOG.'api/customer_account/orders/orderProductLikeDislike';
      $data['dislike_link'] .= '&data=' . base64_encode($url_param);
    }

    return $data;
  }

  /**
   * Public method to set reorder link
   * @param : $data Array
   * @return: $data Array
   * @author: Nishu, Aug 2018
  */
  private static function setOrderProductReorderLink(array $data) : array{
    
    if(!empty($data)){
      //Set Order Product Reorder Link
      $url_param = 'customer_id='.$data['customer_id'].'&order_id='.$data['order_id'].'&order_product_id='.$data['order_product_id'];

     
      $data['reorder_link']  = HTTPS_CATALOG.'api/customer_account/orders/reorderOrderProduct';
      $data['reorder_link'] .= '&data=' . base64_encode($url_param);

    }

    return $data;
  }

  /**
   * Public method to set download images link
   * @param : $product_id integer
   * @return: $download_images String
   * @author: Nishu, Aug 2018
  */
  private static function setProductImageDownloadLinkByProduct_id(int $product_id) : string{
    $download_images = '';

    if(!empty($product_id)){
      //Set Order Product download images link
      $download_images  = HTTPS_CATALOG.'api/image/download_image&product_id='. $product_id;

    }

    return $download_images;
  }

    /**
   * Public method to get order product details by order_id
   * @param:  $db object,
   * @param:  $order_id, Integer
   * @param:  $filter_data, Optional
   *            keys :- 
   *              select---- for selector data (Which can be string or array) 
   *              where----- for where (Which can be string or array) 
   * @return: $data Array
   * @author: Nishu, sept 2019
  */
  public static function getOrderProductByOrderId(Database\DB $db, int $order_id, $filter_data) : array{
    $data = array();

    if(!empty($order_id) ){
      $selector = "";

      if(!empty($filter_data['select'])){
        $selector = $filter_data['select'];

        if(is_array($filter_data['select'])){
          $selector = implode(',', $selector);
        }

      }else{
        $selector = " op.order_product_id ";
      }

      $sql = "
              SELECT  
                ".$selector."
              FROM 
                " . DB_PREFIX . "order_product AS op
              INNER JOIN
                ".DB_PREFIX. "order AS o ON o.order_id = op.order_id
              INNER JOIN
                ".DB_PREFIX."suborder AS osub ON op.suborder_id = osub.suborder_id
                                            AND op.order_id = osub.order_id
              WHERE
                op.order_id = '" . (int) $order_id . "'
                AND op.buyer_invoice_id > 0
              ";
      $whr = "";
      if(!empty($filter_data['where'])){
        $whr = $filter_data['where'];

        if(is_array($filter_data['where'])){
          $whr = implode(' AND ', $whr);
        }

        $sql .= "AND ". $whr;
      }

      $sql .= " ORDER BY op.order_product_id ASC";

      $result = $db->query($sql);
      if($result->num_rows > 0){
        $data = $result->rows;
        $data = array_combine(
                  array_column($data, 'order_product_id'), 
                  $data
                ); 
      }
    }
    
    return $data;
  }

  /**
   * @info: Public static method to get product related into (Currently method written to add return from client side)
   * @param: $order_products_ids (can be array, string or integer)
   * @return: array 
   * @author: Nishu, Sept 2019
  */
  public static function getOrderProductInfoByOPIds($db, $order_product_ids){
    $data = array();
    if(!empty($order_product_ids)){
      if(is_array($order_product_ids)){
        $order_product_ids = implode(',', $order_product_ids);
      }

      $sql = "
              SELECT
                op.order_product_id,
                op.combo_product_id,
                op.order_id,
                op.suborder_id,
                op.seller_invoice_id,
                op.buyer_invoice_id,
                op.seller_id,
                (op.quantity * op.piece_in_set) AS total_quantity,
                o.order_no,
                o.customer_id,
                o.email,
                o.telephone,
                o.firstname,
                o.lastname,
                o.payment_company
              FROM
                ".DB_PREFIX."order_product AS op
              INNER JOIN ".DB_PREFIX."order AS o ON op.order_id = o.order_id
              WHERE
                order_product_id IN (". $order_product_ids .")
              ";
      $result = $db->query($sql);
      if($result->num_rows > 0){
        $data = $result->rows;
        $data = array_combine(
                  array_column($data , 'order_product_id'), 
                  $data 
                );
      }
    }
    return $data;
  }

}

// close OrderProduct class
?>