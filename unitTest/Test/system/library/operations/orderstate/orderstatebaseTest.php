<?php
/**
 * User: Sudhanshu
 * Date: 10/28/2016
 * Time: 5:17 PM
 */
 require_once(DIR_SYSTEM . '/library/operations/orders/orderstatebase.php');
 require_once('orderstatehelper.php');
 Class OrderStateBaseTest extends OpenCartTest{
   private $helper;
   private $order_state_object;
   public function __construct(){
     //$this->helper = new OrderStateHelper($this);
   }


  public function testcreateInvoiceNo(){

    $oprtnsHelper =  new operationsHelper($this);
    $model_chckout_splitodr = $this->loadModel('checkout/split_order');

    // first add a order with generated invoice no
    $data = array('invoice_prefix'=>'Test-2016-00','invoice_no'=>8888);
    $data['products'] = $oprtnsHelper->addOrderProducts();
    $first_order_id = $oprtnsHelper->addOrder($data);
    $model_chckout_splitodr->splitByCity($first_order_id);


    //Now add a new order with same invoice prefix and blank invoice no
    $second_data = array('invoice_prefix'=>'Test-2016-00');
    $second_data['products'] = $oprtnsHelper->addOrderProducts();
    $second_order_id = $oprtnsHelper->addOrder($second_data);
    $model_chckout_splitodr->splitByCity($second_order_id);
    $suborder_id = $oprtnsHelper->getSubordersIds($second_order_id);

    $new_invoice_no =  OrderStateBase::createInvoiceNo($this, $second_order_id, $suborder_id[0]);

    // Now I need to check that invoice no is created or not.
    // Will use select query to get data for this new order
    $suborder_data = $oprtnsHelper->getOcSuborder($second_order_id, $suborder_id[0]);

    //cancel invoice no test
    $this->CancelInvoiceNo($second_order_id, $suborder_id[0]);

    // delete order
     $oprtnsHelper->deleteOrder($first_order_id);
     $oprtnsHelper->deleteOrder($second_order_id);

    $this->assertEquals(8889, $suborder_data['invoice_no']);
  }



  private function CancelInvoiceNo($order_id, $suborder_id){

    $oprtnsHelper =  new operationsHelper($this);
    OrderStateBase::cancelInvoiceNo($this, $order_id, $suborder_id);

    // Now I need to check that invoice no is created or not.
    // Will use select query to get data for this new order
    $suborder_data = $oprtnsHelper->getOcSuborder($order_id, $suborder_id);

    //delete order
    $oprtnsHelper->deleteOrder($order_id);

    $this->assertEquals(0, $suborder_data['invoice_no']);
  }



   public function testOrderStateClassIfOrderHaveNoSuborders(){
     //$exception_message = null;
    // try{
    //   $this->order_state_object = $this->helper->createOrderStateObject('OrderStateBase',array());
     //}
     //catch (Exception $e) {
      // $exception_message = $e->getMessage();
     //}
     //$this->assertEquals($exception_message, "No Suborders found for this order id ".$this->helper->order_id);
   }
   /**
    * [testSubordersEmpty description]
    * @return [type] [description]
    */
   public function testSubordersEmpty(){
      $random_state_num = rand(1,10);
      //$result = $this->getOrderStatusId($random_state_num);
      //$this->assertNotEmpty($result['suborders']);
   }
   //public function testAddOrderHistory(){
      //$random_state_num = rand(1,10);
      //$data['order_status_id'] = $random_state_num;
      //$this->helper = new OrderStateHelper();
      //$order_id = $this->helper->addOrder($data);
      //$obj = new OrderStateBase(self::$registry,$order_id,0);
      //$obj = $this->addOrderHistory($random_state_num,1,1,'Unit Testing','Extra Notes');
   //}
   /**
    * [testSubordersStatusIdNotMatched description]
    * @return [Returns error if test case failed]
    */
   public function testSubordersStatusIdNotMatched(){
     $random_state_num = rand(1,10);
     //$result = $this->getOrderStatusId($random_state_num);
     //foreach ($result['suborders'] as $key => $value) {
         //$this->assertEquals($random_state_num , $result['suborders'][$key]['suborder_status_id']);
     //}
   }
   /**
    * [TestGetOrderStatusId description]
    * @param [int] $random_state_num [State id ]
    * @return [array] [It returns an array of suborder_id and state_id]
    */
   private function getOrderStatusId($random_state_num){
     $data['order_status_id'] = $random_state_num;
     $order_state_base = $this->helper->createOrderStateObject('OrderStateBase',$random_state_num);
     $result = $order_state_base->getOrderState();
     return $result;
   }
 }
