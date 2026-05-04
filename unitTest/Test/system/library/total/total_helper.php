<?php
 Class TotalHelper{
     private $registry;
     private $db;
     private $_load;
     private $_cart;
     private $_config;
     public $total_data = array();
     public $total = 0;
     public $taxes;
     public $cst_class_id = CST_CLASS_ID;
     public $cst;
     private $_session;


     public function __construct($registry){
       if( method_exists( $registry , 'get' ) ){
           $this->db       = $registry->get('db');
           $this->_load     = $registry->get('load');
           $this->_cart     = $registry->get('cart');
           $this->_config     = $registry->get('config');
           $this->_session = $registry->get('session');
       }
       else{
           $this->db       = $registry->db;
           $this->_load     = $registry->load;
           $this->_cart     = $registry->cart;
           $this->_config = $registry->config;
           $this->_session = $registry->session;
       }
       $this->taxes = $this->_cart->getTaxes();
       $this->registry = $registry;
     }

     public function setCartData( $above_five_k = false ){
        $cart_data = array(
                 base64_encode(serialize(array('product_id' => 1600))) => array(
                                         'key' => base64_encode(serialize(array('product_id' => 1600))),
                                         'product_id' => 1600,
                                         'quantity' => 1,
                                         'piece_in_set' => 3,
                                         'total_pieces' => 3,
                                         'price' => 1230,
                                         'tax_class_id' => 9,
                                         'price_per_piece' => 410,
                                         'total' => 1230
                                    ),
                 base64_encode(serialize(array('product_id' => 1601))) => array(
                                         'key' => base64_encode(serialize(array('product_id' => 1601))),
                                         'product_id' => 1601,
                                         'quantity' => 1,
                                         'piece_in_set' => 2,
                                         'total_pieces' => 2,
                                         'price' => $above_five_k ? 9800 : 980,
                                         'tax_class_id' => 9,
                                         'price_per_piece' => $above_five_k ? 4900 : 490,
                                         'total' => $above_five_k ? 9800 : 980
                                     ),
                 base64_encode(serialize(array('product_id' => 1602))) => array(
                                         'key' => base64_encode(serialize(array('product_id' => 1602))),
                                         'product_id' => 1602,
                                         'quantity' => 2,
                                         'piece_in_set' => 2,
                                         'total_pieces' => 4,
                                         'price' => 500,
                                         'tax_class_id' => 0,
                                         'price_per_piece' => 250,
                                         'total' => 1000
                                     )
        );

        return $this->_cart->_cart_data = $cart_data;
     }

     public function setPaychargeData(){
         $paycharge[] = array (
                            'payment_method' => 'citrus',
                            'valuep' => -2,
                            'amount' => 5000,
                            'description' => array (1 => array( 'name' => 'Discount')),

                        );
         $paycharge[] = array (
                            'payment_method' => 'bank_transfer',
                            'valuep' => -2,
                            'amount' => 5000,
                            'description' => array (1 => array( 'name' => 'Discount')),

                       );
         $this->_config->set('paycharge',$paycharge);
     }

     public function setCashBackData(){
         $cashback = array(
                        'total_cashback' => '150',
                        'cashback_breakup' => array(
                                                '12' => 100,
                                                '13' => 50
                                            ),
                    );
         $this->_config->set( 'cashback_data_for_unit_testing' , $cashback );
     }

     public function setCouponData(){
         $start_date = strtotime("-1 day");
         $end_date = strtotime("+1 day");
         $coupon = array(
                'coupon_id' => 63,
                'code' => 'unit_testing',
                'name' => 'unit_testing',
                'type' => 'P',
                'discount' => 1.0000,
                'shipping' => 0,
                'total' => 100.0000,
                'product' => array(
                                '1600' => array(
                                        'product_id' => '1600',
                                        'filter_name' => 'product_id',
                                        'filter_value' => 70
                                    )

                                ),

                'date_start' => date('Y-m-d', $start_date),
                'date_end' => date('Y-m-d', $end_date),
                'uses_total' => 222,
                'uses_customer' => 222,
                'status' => 1,
                'date_added' => date('Y-m-d h:i:s'),
                    );
         $this->_config->set( 'unit_testing_coupon' , $coupon );
     }

     public function getTaxes( $below_five_k = false ){
         return array(
             '86' => $below_five_k ? 176.55 : 661.65 ,
         );
     }

     public function setShippingMethod(){
            $shipping =   array(
                                'code' => 'weight.weight_6',
                                'title' => 'Air Courier (2-3 days)',
                                'cost' => 6300,
                                'tax_class_id' => 0,
                                'text' => 'Rs. 6,300.00',
                            );
           $this->_session->data['shipping_method'] = $shipping;
     }

 }
 ?>
