<?php
  require_once( DIR_SYSTEM . 'library/total/sub_total.php' );
  require_once( 'total_helper.php' );

  class SubTotalTest extends OpenCartTest{
    private $_total_helper;
    private $_cart;
    private $_cart_data;
    private $_registry;
    private $_sub_total_object;
    public $total_data = array();
    public $total = 0;

    public function __construct(){
        $this->_cart = $this->cart;
        $this->_total_helper = new TotalHelper( $this );
        $this->getSubTotal();
    }

    public function testCartTotal(){
        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();
        $sub_total_object = new SubTotal( $this );
        $this->_cart_data = $this->_total_helper->setCartData();
        $sub_total_object->getTotal( $total_data, $total, $taxes );
        $this->assertEquals( $this->total_data , $total_data);
    }

    public function testCartTotalData(){
        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();
        $sub_total_object = new SubTotal( $this );
        $this->_cart_data = $this->_total_helper->setCartData();
        $sub_total_object->getTotal( $total_data, $total, $taxes );
        $this->assertEquals( $this->total , $total);
    }



    private function getSubTotal(){
        $this->load->language('total/sub_total');
        $total = 0;
        $total_data = array();
        foreach( $this->cart->_cart_data as $key => $product ){
            $total += (float)$product['total'];
        }
        $total_data[0] = array(
            'code' => 'sub_total',
            'title' => $this->language->get('text_sub_total'),
            'value' => $total,
            'sort_order' => $this->config->get('sub_total_sort_order')
        );
        $this->total_data = $total_data;
        $this->total += $total;
    }
  }
?>
