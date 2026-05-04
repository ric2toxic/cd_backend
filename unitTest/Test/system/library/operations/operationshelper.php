<?php
class OperationsHelper{
  private $driver;
  private $registry;
  private $db;
  public function __construct($registry){
    //$this->driver   = $obj;
    $this->registry = $registry;
    $this->db       = $registry->db;
    $this->load     = $registry->load;
  }
  /**
   * [addOrder - This method is for adding an order into ordertable]
   * @param [type] $inputs [description]
   */
   public function addOrder($inputs){
       $data = array(
               'invoice_prefix' => 'UNIT',
               'invoice_no' => 'UNIT',
               'store_id' => '0',
               'store_name' => 'Unit_Test',
               'store_url' => 'Unit Test Url',
               'customer_id' => '1111',
               'firstname' => 'Unit',
               'lastname' => 'Test',
               'email' => 'unit@test.com',
               'telephone' => '987654321',
               'custom_field' => '',
               'payment_firstname' => '',
               'payment_lastname' => '',
               'payment_company' => '',
               'payment_address_1' => '',
               'payment_address_2' => '',
               'payment_city' => '',
               'payment_postcode' => '',
               'payment_country' => '',
               'payment_country_id' => '',
               'payment_zone' => '',
               'payment_zone_id' => '',
               'payment_address_format' => '',
               'payment_custom_field' => '',
               'payment_method' => '',
               'payment_code' => '',
               'shipping_firstname' => '',
               'shipping_lastname' => '',
               'shipping_company' => '',
               'shipping_address_1' => '',
               'shipping_address_2' => '',
               'shipping_city' => '',
               'shipping_postcode' => '',
               'shipping_country' => '',
               'shipping_country_id' => '',
               'shipping_zone' => '',
               'shipping_zone_id' => '',
               'shipping_address_format' => '',
               'shipping_custom_field' => '',
               'shipping_method' => '',
               'shipping_code' => '',
               'comment' => '',
               'total' => '',
               'affiliate_id' => '',
               'commission' => '',
               'marketing_id' => '',
               'tracking' => '',
               'language_id' => '',
               'currency_id' => '',
               'currency_code' => '',
               'currency_value' => '',
               'currency_live_conversion_rate' => '',
               'ip' => '',
               'forwarded_ip' => '',
               'user_agent' => '',
               'accept_language' => '',
               'date_added' => 'NOW()',
               'date_modified' => 'NOW()',
               'cform_submit' => '',
               'cst_with_cform' => '',
               'refundable_cform' => '',
               'refund_status' => '',
               'live_conversion_rate' => '',
               'order_from' => '',
               'total_weight' => '',
               'weight_class_id'=>'',
       );
       if(is_array($inputs) || !empty($inputs)){
         foreach($inputs as $index => $input){
           $data[$index] = $input;
         }
       }
       $modal =  $this->registry->loadModel('checkout/order');
       $order_id =  $modal->model_checkout_order->addOrder($data);
       if(isset($data['order_status_id'])){
        $this->db->query('Update '.DB_PREFIX . 'order set order_status_id = '.(int)$data['order_status_id'].' where order_id = '.$order_id .' And store_id = 0');
       }

       if($order_id){
         return $order_id;
       }
   }

   /**
   * get 2 different seller_id By city (i.e. Jaipur , Surat)
   * then after get 2 different products by these seller_id
   * and then addOrderPrdoducts() using in unit test and then adding a order with order products
   * @output:  return product details in array
   * @author: vikas, 2016
   */

   public function addOrderProducts(){
      $sql_get_seller_id = $this->db->query("SELECT seller_id,nickname
                                             FROM oc_ms_seller
                                             WHERE city
                                              IN ('jaipur','surat') group BY city LIMIT 2 "
                                           )->rows;

      $seller_id = implode(',',array_column($sql_get_seller_id, 'seller_id'));
      $sid_to_nickname = array_combine(
                        array_column($sql_get_seller_id, 'seller_id'),
                        array_column($sql_get_seller_id, 'nickname')
                    );
      $sql_products_id = $this->db->query("SELECT product_id,seller_id
                                             FROM oc_ms_product
                                             WHERE seller_id
                                              IN ($seller_id)
                                            group BY seller_id LIMIT 2 ")->rows;
      $product_id = implode(',', array_column($sql_products_id, 'product_id'));
      $pid_to_sid = array_combine(
                        array_column($sql_products_id, 'product_id'),
                        array_column($sql_products_id, 'seller_id')
                    );
      $sql_products_data = $this->db->query("SELECT product_id,
                                                    model,
                                                    sku,
                                                    quantity,
                                                    piece_in_set,
                                                    price,
                                                    seller_tax,
                                                    commission
                                             FROM oc_product
                                             WHERE product_id
                                              IN ($product_id) ");

      //$result = $sql_products_data->rows;

      $result = array();
      foreach($sql_products_data->rows as $key => $values){
      $result[] = array_merge($values, array('name' =>  'test-product-01',
                                              'total_pieces' => 5,
                                              'price_per_piece' => '1200',
                                              'total' => '6000',
                                              'tax' => '0',
                                              'transfer_price_per_piece' => 110,
                                              'comment' => '',
                                              'tax_rates' => '',
                                              'option' => array(),
                                              'seller_id' => $pid_to_sid[$values['product_id']],
                                              'seller_nickname' => $sid_to_nickname[$pid_to_sid[$values['product_id']]]
                                            ));
      }

      return $result;

   }


  /**
  * get 2 different seller_id By city (i.e. Jaipur , Surat)
  * then after get 2 different products by these seller_id
  * and then addOrderPrdoducts() using in unit test and then adding a order with order products
  * @output:  return product details in array
  * @author: vikas, 2016
  */

  public function addOrderTotal(){
    $totals = Array
        (
            Array
                (
                    'code' => 'sub_total',
                    'title' => 'Sub-Total',
                    'value' => '12000',
                    'sort_order' => '1'
                ),

            Array
                (
                    'code' => 'paycharge',
                    'title' => 'Discount(2%)',
                    'value' => '-240',
                    'sort_order' => '6'
                ),

            Array
                (
                    'code' => 'credit',
                    'title' => 'Test Credit',
                    'value' => '-150',
                    'sort_order' => '6'
                ),

            Array
                (
                    'code' => 'advance',
                    'title' => 'Advance Collected',
                    'value' => '-500',
                    'sort_order' => '6'
                ),

            Array
                (
                    'code' => 'tax',
                    'title' => 'Total Tax',
                    'value' => '0',
                    'sort_order' => '5'
                ),



            Array
                (
                    'code' => 'shipping',
                    'title' => 'Surface Courier (6-9 days)',
                    'value' => '100',
                    'sort_order' => '6'
                ),


            Array
                (
                    'code' => 'total',
                    'title' => 'Total',
                    'value' => '11210',
                    'sort_order' => '8'
                )

        );

        return $totals;
  }


  /** Method to get suborder ids of perticular order id
   * for unit testing order related methods.
   * @author Vikas
   */
  public function getSubordersIds($order_id){
    $suborder_ids = array();
    $sql  = "Select suborder_id from " . DB_PREFIX . "suborder ";
    $sql .=   "where order_id = ".$order_id;
    $result = $this->db->query($sql);
    if(!empty($result->rows)){
      return array_column($result->rows,'suborder_id');
    }
  }


  /** Method to clean up data created in database
   * for unit testing order related methods.
   * @author Vikas
   */
  public function deleteOrder($order_id){
    // array storing all the order tables
    $order_tables = array('order',
                          'order_product',
                          'order_history',
                          'order_total',
                          'order_option',
                          'suborder');

    foreach ($order_tables as $table) {
      $this->db->query("DELETE FROM " . DB_PREFIX . $table .
                        " WHERE order_id = '" . (int)$order_id . "'");
    }
  }

  /**
   * Function to get all data from oc_order table
   * @param int order_id
   * @return array of array (order rows)
   * @author Vikas
   */
  public function getOcOrder($order_id) {
    return $this->db->query("SELECT * FROM " . DB_PREFIX . "order
                             WHERE order_id = '" . (int)$order_id . "'")->rows;
  }

  /**
   * Function to get all data from oc_suborder table
   * @param int order_id
   * @param string suborder_id
   * @return array of suborder row
   * @author Vikas
   */
  public function getOcSuborder($order_id, $suborder_id) {
    return $this->db->query("SELECT * FROM " . DB_PREFIX . "suborder
                             WHERE order_id = '" . (int)$order_id . "'
                               AND suborder_id = '" . $this->db->escape($suborder_id) . "'")->row;
  }



}
 ?>
