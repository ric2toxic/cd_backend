<?php
  require_once( DIR_SYSTEM . 'library/total/coupon.php' );
  require_once( 'total_helper.php' );

  class CouponTest extends OpenCartTest{
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
        $this->_cart_data = $this->_total_helper->setCartData();

    }

    public function testCouponTotalData(){
        $this->_total_helper->setCouponData();
        $this->session->data['coupon'] = 'unit_testing_coupon';
        $coupon_object = new Coupon( $this->_registry );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes();
        $coupon_object->getTotal( $total_data, $total , $tax );
        $this->load->language('total/coupon','frontend');
        $expected = array();
        $coupon_info = $this->config->get('unit_testing_coupon');
        $expected[0] = array(
            'code' => 'coupon',
            'title' => sprintf($this->language->get('text_coupon'),$this->session->data['coupon']),
            'value' => -12.3,
            'sort_order'    => $this->config->get('coupon_sort_order'),
            'discount'      => $coupon_info['discount'],
            'discount_type' => $coupon_info['type'],
        );
        $this->assertEquals( $expected , $total_data);
    }

    public function testCouponTax(){
        $this->_total_helper->setCouponData();
        $this->session->data['coupon'] = 'unit_testing_coupon';
        $coupon = new Coupon( $this );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes(true);
        $coupon->getTotal( $total_data, $total, $tax );
        $expected = array( '86' => 175.8735 );
        $this->assertEquals( $expected , $tax );
    }

    public function testCouponTotal(){
        $this->_total_helper->setCouponData();
        $this->session->data['coupon'] = 'unit_testing_coupon';
        $coupon = new Coupon( $this->_registry );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes(true);
        $coupon->getTotal( $total_data, $total , $tax );
        $expected = 3197.7;
        $this->assertEquals( $expected , $total);
    }
  }
?>
