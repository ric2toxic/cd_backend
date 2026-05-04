<?php
/**
 * Out For Delivery
 * @info All methods of out for delivery state can be write here.
 * @author Sudhanshu Jain
 * @date 27-Oct-2016
 */
require_once ('orderstatebase.php');
class OutForDelivery extends OrderStateBase {
  public function doAction($inputs) {
    if(!empty($inputs) && !empty($inputs['action_type'])){
      extract($inputs);
      if(!empty($inputs)){
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
                case 'DLRY_PSPND_BY_CSTMR':
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
