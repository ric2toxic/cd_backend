<?php
  require_once( DIR_SYSTEM . 'library/total/shipping.php' );
  require_once( 'total_helper.php' );

  class ShippingTest extends OpenCartTest{
    private $_total_helper;
    private $_cart;
    private $_cart_data;
    private $_registry;
    private $_sub_total_object;
    public $total_data = array();
    public $total = 0;

    public function __construct(){
        $this->_cart = $this->cart;
        $this->_registry = $this;
        $this->_total_helper = new TotalHelper( $this );
    }

    public function testShippingTotalData(){
        $this->load->language('total/sub_total');
        $this->_total_helper->setCartData();
        $this->_total_helper->setShippingMethod();
        $shipping = new Shipping( $this->_registry );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes(true);
        $shipping->getTotal( $total_data, $total , $tax );
        $expected = array();
        $expected[0] = array(
			'code'       => 'shipping',
			'title'      => $this->session->data['shipping_method']['title'],
			'value'      => $this->session->data['shipping_method']['cost'],
			'sort_order' => $this->config->get('shipping_sort_order')
		);
        $this->assertEquals( $expected , $total_data);
    }

    public function testShippingTotal(){
        $this->_total_helper->setCartData();
        $this->_total_helper->setShippingMethod();
        $shipping = new Shipping( $this->_registry );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes(true);
        $shipping->getTotal( $total_data, $total , $tax );
        $expected = 9510;
        $this->assertEquals( $expected , $total);
    }
  }
?>
