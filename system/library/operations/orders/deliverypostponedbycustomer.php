<?php
/**
 * Delivery Postponed By Customer
 * @info All methods of delivery postponed by customer state can be write here.
 * @author Sudhanshu Jain
 * @date 27-Oct-2016
 */
require_once ('orderstatebase.php');
class DeliveryPostPonedByCustomer extends OrderStateBase {
  private $post_action = array();
  /**
   * [__construct description]
   * @param [object] $registry    [static registry object ]
   * @param [int] $orderid     [order_id]
   * @param [array or int] $suborder_id [suborder_ids]
   */
  public function __construct($registry,$orderid,$suborder_id){
    parent::__construct($registry,$orderid,$suborder_id);
  }
   /**
    * doAction
    * @param $data Array->{ 'action_type' => $action_type , 'data' => $data}
    * @return mixed
    *
    */
   public function doAction($inputs) {
     if(!empty($inputs) && !empty($inputs['action_type'])){
       extract($inputs);
       if(!empty($inputs['data'])){
          extract($data);
       }
       switch($action_type){
           case 'changeOrderState':
             if(!empty($post_state)){
               $notify = 0;
               $notify_sms = 0;
               $comment = "Dummy Comment...";
               $notes = "Dummy Notes...";
               switch ($post_state) {
                 case 'DLVRD':
                   $post_state_id = $this->getStateIdByname($post_state,1);
                   break;
                 case 'DLRY_FLD_BY_CSTMR':
                   $post_state_id = $this->getStateIdByname($post_state,1);
                   break;
                 case 'PRCL_LST_CR':
                   $post_state_id = $this->getStateIdByname($post_state,1);
                   break;
                 default:
                   throw new Exception("You can not change your current order state to ".$post_state);
                   break;
               }
              $result = $this->changeOrderState($post_state_id);
              if($result){
                 if($this->notifyCustomerBySms($message)){
                   $notify = 1;
                   $notify_sms = 1;
                 }
                 if($this->notifyCustomerByEmail($subject,$message)){
                   $notify = 1;
                 }
                 $this->addOrderHistory( $post_state_id , $notify , $notify_sms , $comment , $notes );
              }
             }
             else{
               throw new Exception("'post_state' can not empty");
             }
           break;
           default:
             throw new Exception("The action '$action_type' is not exist in this state.");
           break;
       }
     }
     else {
       throw new Exception("Data and index 'action_type' can not empty");
     }
  }

  /**
   * getPostAction
   * @return mixed
   *
   */
  public function getPostAction() {
      return $this->post_action;
  }
}
