<?php
/**
 * Order Full Shipped
 * @info All methods of order shipped full state can be write here.
 * @author Sudhanshu Jain
 * @date 27-Oct-2016
 */
require_once ('orderstatebase.php');
class OrderFullShipped extends OrderStateBase {
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
                  case 'OT_FR_DLRY':
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
                    break;
                  default:
                    throw new Exception("You can not change your current order state to ".$post_state);
                    break;
                }
                $this->changeOrderState($post_state_id);
                $notify = 0;
                $notify_sms = 0;
                $comment = "Dummy Comment...";
                $notes = "Dummy Notes...";
                if($this->notifyCustomerBySms()){
                  $notify = 1;
                  $notify_sms = 1;
                }
                if($this->notifyCustomerByEmail()){
                  $notify = 1;
                }
                $this->addOrderHistory( $post_state_id , $notify , $notify_sms , $comment , $notes );
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
