<?php
/**
 * Delivered
 * @info All methods of delivery postponed by customer state can be write here.
 * @author Sudhanshu Jain
 * @date 27-Oct-2016
 */
require_once ('orderstatebase.php');
class Delivered extends OrderStateBase {
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
     * doAction - This method is to perform various actions on orders.
     * @param $data Array->{ 'action_type' => 'action'  , 'data' => $data}
     * @return mixed
     */
  public function doAction($inputs) {
      if(!empty($inputs) && !empty($inputs['action_type'])){
        extract($inputs);
        if(!empty($inputs['data'])){
           extract($data);
        }
        switch($action_type){
            case 'changeOrderState':
              throw new Exception("You can not change the given order state. The order is already  completed.");
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
     * getPostAction - This method returns an array of post actions
     * @return [array] - array of post actions
     */
    public function getPostAction() {
        return $this->post_action;
    }
}
