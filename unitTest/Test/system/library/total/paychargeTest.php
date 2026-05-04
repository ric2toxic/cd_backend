<?php
  require_once( DIR_SYSTEM . 'library/total/paycharge.php' );
  require_once( 'total_helper.php' );

  class PaychargeTest extends OpenCartTest{
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
        $this->_total_helper->setPaychargeData();
        $this->config->set('paycharge_status',1);
        $this->config->set('config_language_id',1);
    }

    public function testPaychargeTotalDataIfCitrusAndAbove_Five_k(){
        $this->_cart_data = $this->_total_helper->setCartData(true);
        $this->session->data['payment_method']['code'] = 'citrus';
        $paycharge = new Paycharge( $this );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes();
        $paycharge->getTotal( $total_data, $total, $tax );
        $this->getPaychargeTotal('citrus');
        $this->assertEquals( $this->total_data , $total_data);
    }

    public function testPaychargeTotalDataIfCitrusAndBelow_Five_k(){
        $this->_cart_data = $this->_total_helper->setCartData();
        $this->session->data['payment_method']['code'] = 'citrus';
        $paycharge = new Paycharge( $this );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes();
        $paycharge->getTotal( $total_data, $total, $tax );
        $this->getPaychargeTotal('citrus');
        $expected = array();
        $this->assertEquals( $expected , $total_data);
    }

    public function testPaychargeTotalDataIfBankTransferAndAbove_Five_k(){
        $this->_cart_data = $this->_total_helper->setCartData(true);
        $this->session->data['payment_method']['code'] = 'bank_transfer';
        $paycharge = new Paycharge( $this );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes();
        $paycharge->getTotal( $total_data, $total, $tax );
        $this->getPaychargeTotal('bank_transfer');
        $this->assertEquals( $this->total_data , $total_data);
    }

    public function testTaxValue(){
        $this->_cart_data = $this->_total_helper->setCartData(true);
        $this->session->data['payment_method']['code'] = 'citrus';
        $paycharge = new Paycharge( $this );
        $total = $this->cart->getSubTotal();
        $total_data = array();
        $tax = $this->_total_helper->getTaxes();
        $paycharge->getTotal( $total_data, $total, $tax );
        $expected = array( '86' => 649.517 );
        $this->assertEquals( $expected , $tax );
    }

    private function getPaychargeTotal($case){
        if( $case == 'citrus'){
            $total_data[0] = array(
                'code' => 'paycharge',
                'title' => 'Discount (-2%)',
                'value' => -240.6,
                'sort_order' => $this->config->get('paycharge_sort_order')
            );
            $this->total_data = $total_data;
        }
        if( $case == 'bank_transfer' ){
            $total_data[0] = array(
                'code' => 'paycharge',
                'title' => 'Discount (-2%)',
                'value' => -240.6,
                'sort_order' => $this->config->get('paycharge_sort_order')
            );
            $this->total_data = $total_data;
        }

    }

  }
?>
