<?php
/**
 * Payment Pending State
 * @info All methods of order pending state can be write here.
 * @author: Sudhanshu Jain
 * @date: 27-Oct-2016
 */
require_once ('orderstatebase.php');
class PaymentPending extends OrderStateBase{
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
                switch ($post_state) {
                  case 'PLCD':
                    if(empty($payment_transaction) || empty($payment_transaction['status'])){
                      $this->post_action[] = 'checkPaymentStaus';
                      return false;
                    }
                    if($payment_transaction['status']){
                      $post_state_id = $this->getStateIdByname($post_state,1);
                      $comment = "Dummy Comment...";
                      $notes = "Dummy Notes...";
                      $subject = "";
                      $message = "";
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
                      $this->post_action[] = 'genPaymentLink';
                      return false;
                    }
                    break;
                  default:
                    throw new Exception("You can not change your current order state to ".$post_state);
                    break;
                }

              }
            break;
            default:
              throw new Exception("The action '$action_type' is not exist in this state.");
            break;
        }
      }
      else {
        throw new Exception("Data and index ('action_type') can not empty");
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
