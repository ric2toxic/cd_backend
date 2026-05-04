<?php
  require_once( DIR_SYSTEM . 'library/total/cashback.php' );
  require_once( 'total_helper.php' );

  class CashbackTest extends OpenCartTest{
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

    public function testCashBackTotalData(){
        $this->_total_helper->setCashBackData();
        $cashback_object = new Cashback( $this->_registry );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes();
        $cashback_object->getTotal( $total_data, $total , $tax );
        $this->load->language('total/cashback');
        $expected = array();
        $expected[0] = array(
            'code' => 'cashback',
            'title' => $this->language->get('text_cashback'),
            'value' => -150,
            'sort_order' =>  $this->config->get('cashback_sort_order')
        );
        $this->assertEquals( $expected , $total_data);
    }

    public function testCashbackTax(){
        $this->_cart_data = $this->_total_helper->setCartData();
        $cashback = new Cashback( $this );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes();
        $cashback->getTotal( $total_data, $total, $tax );
        $expected = array( '86' => 655.9700934579439 );
        $this->assertEquals( $expected , $tax );
    }

    public function testCashBackTotal(){
        $this->_total_helper->setCashBackData();
        $cashback_object = new Cashback( $this->_registry );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes();
        $cashback_object->getTotal( $total_data, $total , $tax );
        $expected = 3060;
        $this->assertEquals( $expected , $total);
    }
  }
?>
