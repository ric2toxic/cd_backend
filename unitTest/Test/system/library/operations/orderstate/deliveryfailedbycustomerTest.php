<?php
 /**
  * User: Sudhanshu
  * Date: 10/28/2016
  * Time: 5:17 PM
  */
  require_once(DIR_SYSTEM . '/library/operations/orders/deliveryfailedbycustomer.php');
  require_once( 'orderstatehelper.php');
  Class DeliveryFailedByCustomerTest extends OpenCartTest{
    private $helper;
    private $order_state_object;
    public function __construct(){
      $this->helper = new OrderStateHelper($this);
    }
    public function testOrderStateIsChanged(){
      //$data = array('post_state' => '');
      //$this->helper->doActions($this->order_state_object,'changeOrderState',$data);
    }
    /*
     * [testChangeOrderStateMailIsSentToCustomer - This method ensures that mail is sent]
     */
    public function testChangeOrderStateMailIsSent(){
      //$data = array();
      //$this->helper->doActions($this->order_state_object,'changeOrderState',$data); // Calling DeliveredTest::doActions
    }
    /**
     * [testChangeOrderStateSmsIsSentToCustomer]
     */
    public function testChangeOrderStateSmsIsSentToCustomer(){

    }
    /**
     * [testChangeOrderStateHistoryIsAdded]
     */
    public function testChangeOrderStateHistoryIsAdded(){

    }

    /**
     * [testDoActionIfPostStateNotExists - This method tested the exceptions comes or not if calling change order state action without post_state ]
     *
     */
    public function testDoActionIfPostStateNotExists(){
      $this->order_state_object = $this->helper->createOrderStateObject('DeliveryFailedByCustomer');
      $exception_message = null;
      try {
        $action = 'changeOrderState';
        $data = array('post_state'=> 'restricted_state');
        $this->helper->doActions($this->order_state_object,$action,$data);
      }
      catch (Exception $e) {
        $exception_message = $e->getMessage();
      }
      $this->helper->deleteOrder($this->helper->order_id);
      $this->assertEquals($exception_message, "You can not change your current order state to ".$data['post_state']);
    }

    public function testDoActionIfPostStateIsNotSet(){
      $this->order_state_object = $this->helper->createOrderStateObject('DeliveryFailedByCustomer');
      $exception_message = null;
      try {
        $data = array();
        $action = 'changeOrderState';
        $this->helper->doActions($this->order_state_object,$action,$data);
      }
      catch (Exception $e) {
        $exception_message = $e->getMessage();
      }
      $this->helper->deleteOrder($this->helper->order_id);
      $this->assertEquals($exception_message, "'post_state' can not empty");
    }
    /**
     * [testDoActionIfActionNotExists - This method tested the exceptions comes or not if calling non defined action ]
     *
     */
    public function testDoActionIfActionNotExists(){
      $this->order_state_object = $this->helper->createOrderStateObject('DeliveryFailedByCustomer');
      $exception_message = null;
      try {
        $action = 'restrictAction';
        $data = array();
        $this->helper->doActions($this->order_state_object,$action,$data);
      }
      catch (Exception $e) {
        $exception_message = $e->getMessage();
      }
      $this->helper->deleteOrder($this->helper->order_id);
      $this->assertEquals($exception_message, "The action '$action' is not exist in this state.");
    }

    public function testDoActionIfDataIsNotSet(){
      $this->order_state_object = $this->helper->createOrderStateObject('DeliveryFailedByCustomer');
      $exception_message = null;
      try {
        $data = array();
        $action = null;
        $this->helper->doActions($this->order_state_object,$action,$data);
      }
      catch (Exception $e) {
        $exception_message = $e->getMessage();
      }
      $this->helper->deleteOrder($this->helper->order_id);
      $this->assertEquals($exception_message, "Data and index 'action_type' can not empty");
    }
  }
 ?>
