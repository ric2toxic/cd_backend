<?php
require_once(__DIR__.'/../operationshelper.php');
class OrderStateHelper extends OperationsHelper{
  private $registry;
  private $db;
  public $order_id;
  public function __construct($registry){
    $this->registry = $registry;
    $this->db = $registry->db;
    parent::__construct($registry);
  }
  /**
   * [createDeliveredObject - This method is for creating delivery class object]
   * @return [object] [It returns the object of delivered class.]
   */
  public function createOrderStateObject($order_state,$data = array()){
    if(empty($data['order_status_ids'])){
        $data['order_status_id'] = rand(1,10);
    }
    if(empty($this->order_id)){
      $this->order_id = $this->addOrder($data);
    }
    if(!empty($order_state) && class_exists($order_state)){
      $obj = new $order_state($this->registry, $this->order_id , 0);
      return $obj;
    }

  }
  /**
   * [doActions This function is to perform action any action in Delivered::doAction function]
   * @param  [object] $obj - Object of Delivered Class
   * @param [string] $action - Action types
   * @param [array] $data - Appropriate data to given action type.
   * @return [type]      [description]
   */
  public function doActions($obj,$action,$data){
    $post_data['action_type'] = $action;
    $post_states_array = array(
                        'PRCD',
                        'PRTL_SHPD',
                        'FL_SHPD',
                        'OT_FR_DLRY',
                        'DLRY_FLD_BY_CSTMR',
                        'DLRY_PSPND_BY_CSTMR',
                        'DLVRD',
                        'PRCL_LST_CR',
                        'PLCING',
                        'PLCD',
                        'PMT_PNDG',
                      );
    $index = array_rand($post_states_array,1);
    $pre_data =  array('post_state' => $post_states_array[$index]);
    if(is_array($data) && !empty($data)){
      $data = array_merge($pre_data,$data);
    }
    $args = array(
      'action_type' => $action,
      'data' => $data,
    );
    //exit
    $status = $obj->doAction($args);
    //echo 'Class name is '.get_class($obj).'       <br>';

  }

}
 ?>
