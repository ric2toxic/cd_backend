<?php
  require_once( DIR_SYSTEM . 'library/total/tax.php' );
  require_once( 'total_helper.php' );

  class TaxTest extends OpenCartTest{
    private $_total_helper;
    private $_cart;
    private $_cart_data;
    private $_registry;
    private $_tax_object;
    public $taxes;
    public $total_data = array();
    public $total = 150;

    public function __construct(){
        $this->_cart = $this->cart;
        $this->_registry = $this;
        $this->_total_helper = new TotalHelper( $this );
        $this->taxes = $this->_total_helper->getTaxes();
        $this->_tax_object = new TotalTax( $this );
        $this->getTaxTotal();
    }

    public function testTaxTotalData(){
        $total = 150;
        $total_data = array();
        $this->_tax_object->getTotal($total_data,$total,$this->taxes);
        $this->assertEquals( $this->total_data , $total_data );
    }


    public function testTaxTotal(){
        $total = 150;
        $total_data = array();
        $this->_tax_object->getTotal($total_data,$total,$this->taxes);
        $this->assertEquals( $this->total , $total );
    }


    private function getTaxTotal(){
        $this->load->language('total/tax');
        $total_tax = 0;
        $total_data = array();
        foreach( $this->taxes as $key => $tax ){
            $total_tax += (float)$tax;
        }
        $total_data[0] = array(
            'code' => 'tax',
            'title' => $this->language->get('text_tax'),
            'value' => $total_tax,
            'sort_order' => $this->config->get('tax_sort_order')
        );
        $this->total_data = $total_data;
        $this->total += $total_tax;
    }
  }
?>
