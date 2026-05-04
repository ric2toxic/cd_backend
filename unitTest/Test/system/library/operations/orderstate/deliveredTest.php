<?php
/**
 * User: Sudhanshu
 * Date: 10/28/2016
 * Time: 5:17 PM
 */
 require_once(DIR_SYSTEM . 'library/operations/orders/delivered.php');
 require_once( 'orderstatehelper.php');
 class DeliveredTest extends OpenCartTest{
   private $helper;
   private $order_state_object;
   public function __construct(){
     $this->helper = new OrderStateHelper($this);
   }
   /**
    * [testDoAction This method checks the exception in change order state action]
    */
   public function testDoActionIfChangeOrderState(){
     $this->order_state_object = $this->helper->createOrderStateObject('Delivered');
     $exception_message = null;
     try {
         $data = array();
         $this->helper->doActions($this->order_state_object,'changeOrderState',$data); // Calling DeliveredTest::doActions
     }
     catch (Exception $e) {
       $exception_message = $e->getMessage();
     }
     $this->helper->deleteOrder($this->helper->order_id);
     $this->assertEquals($exception_message, 'You can not change the given order state. The order is already  completed.');
   }

   /**
    * [testDoActionIfActionNotExists - This method tested the exceptions comes or not if calling non defined action ]
    *
    */
   public function testDoActionIfActionNotExists(){
     $this->order_state_object = $this->helper->createOrderStateObject('Delivered');
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
   /**
    * [testDoActionIfActionNotExists
    * This method tested the exceptions comes or not if data is not set or data['action_type'] is not set.]
    *
    */
   public function testDoActionIfDataIsNotSet(){
     $this->order_state_object = $this->helper->createOrderStateObject('Delivered');
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
