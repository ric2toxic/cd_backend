<?php
/**
 * OrderStateBase
 * @info This Order Status base class. All Methods related to order state can be write here.
 * @author Sudhanshu Jain
 * @date 27-Oct-2016
 */
class OrderStateBase{
    protected $order_id; // Order Id
    protected $suborder_id; // Suborder Id of an order
    protected $customer_id; // Customer Id of an order
    protected $registry; // For Registry Object
    protected $db; // For Database Object
    protected $customer_email; // For Customer email address;
    protected $customer_telephone; // For Customer Phone Number;
    protected $customer_addtional_emails = array(); // For customer Addtional Email Address

    /**
     * [__construct - Constrctor ( Sets order_id, registry, suborder_id, customer_id when object is creating)]
     * @param [type]  $registry    [description]
     * @param [type]  $order_id    [order id]
     * @param integer or array $suborder_id [suborder_ids]
     */
    public function __construct($registry, $order_id, $suborder_id = 0) {
      $this->registry = $registry;
      $this->db = $registry->db;
        if ( empty($order_id) ) {
          throw new Exception("Order Id is required.");
        }
        else {
          $this->order_id = $order_id; // Setting Order Id variable
          $this->suborder_id = $suborder_id; // Setting Suborder variable
            if(empty($suborder_id) || $suborder_id == 0){ // checking If suborder_id is empty
              $this->suborder_id = $this->getSubordersIds(); // Getting Suborders Id of an order and setting the variable
            }
            if(empty($customer_id)){
              $this->customer_id = $this->getCustomerId(); // Gets Customer Id and sets customer_id variable
            }
        }
    }

    /**
    * Method to generate invoice no. by perticular order_id, suborder_id, invoice_prefix
    * @param $order_id      : Integer order id
    * @param $suborder_id   : string suborder id
    * @param $invoice_prefix: string invoice_prefix
    * @return get generated invoice no. otherwise false                 
    * @author Vikas , 2016
    */ 
    public static function createInvoiceNo($obj, $order_id ,$suborder_id){
       $result = $obj->db->query("SELECT invoice_no, invoice_prefix 
                                  FROM " . DB_PREFIX . "suborder 
                                  WHERE suborder_id = '".$obj->db->escape($suborder_id)."' 
                                    AND order_id = '".(int)$order_id."'
                                ");


      if ( $result->num_rows && empty($result->row['invoice_no']) ) {

        $query = $obj->db->query("SELECT MAX(invoice_no) AS last_invoice_no 
                                  FROM " . DB_PREFIX . "suborder 
                                  WHERE invoice_prefix = '".$obj->db->escape($result->row['invoice_prefix']).
                                  "'");


        if ( !empty($query->row['last_invoice_no']) ) {
          $invoice_no = (float)$query->row['last_invoice_no'] + 1;
        } else {
          $invoice_no = 1;
        }

        $sql = "UPDATE " . DB_PREFIX . "suborder 
                SET invoice_no = '" . (int)$invoice_no . "', 
                    invoice_date = NOW(), 
                    invoice_prefix = '" . $obj->db->escape($result->row['invoice_prefix']) . "' 
                WHERE order_id = '" . (int)$order_id . "' 
                  AND suborder_id = '". $obj->db->escape($suborder_id) . "'";

        $obj->db->query($sql);

        return $invoice_no;
      }
    }

    /**
    * Method to cancel generated invoice no. by perticular order_id, suborder_id
    * @param $order_id      : Integer order id
    * @param $suborder_id   : string suborder id
    * @return cancel generated invoice no. otherwise false                 
    * @author Vikas , 2016
    */
    public static function cancelInvoiceNo($obj, $order_id, $suborder_id) {

      $obj->db->query("UPDATE " . DB_PREFIX . "suborder 
                        SET invoice_no = '0', 
                        invoice_date = NULL 
                        WHERE order_id = '" . (int)$order_id . "'
                          AND suborder_id = '".$obj->db->escape($suborder_id)."'
                      ");

    }


    /**
     * Get Order State
     * @info This function gets the state of suborders.
     * @return [array]->['order_id','suborders'->array['suborder_id','suborder_status_id','suborder_status']]
     */
    public function getOrderState() {
        $order_id = $this->order_id;
        $suborder_id = $this->suborder_id;
        $result = array('order_id' => $order_id , 'suborders' => array());
        $status_ids = array();
        $suborder_id_query = "";
        $status = $this->db->query('SELECT `order_status_id`,`order_status` FROM `'.DB_PREFIX.'order_status`');
        if($status->num_rows > 0){
            $order_status = $status->rows;
        }
        if(!empty($suborder_id)){
            $suborder_id_query = "AND suborder_id In (".explode(',',$suborder_id).")";
        }
        if(!empty($order_id)){
            $sql  = "SELECT `suborder_id`,`order_status_id` FROM `".DB_PREFIX."suborder` ";
            $sql .=      "WHERE `order_id` = ".intval($order_id)." ".$suborder_id_query;

            $status_ids = $this->db->query($sql);
            if($status_ids->num_rows > 0){
                foreach ($status_ids->rows as $row){
                    foreach ($order_status as $status){
                        if( $status['order_status_id'] == $row['order_status_id'] ){
                            $result['suborders'][]['suborder_id'] = $row['suborder_id'];
                            $result['suborders'][]['suborder_status_id'] = $status['order_status_id'];
                            $result['suborders'][]['suborder_status'] = $status['order_status'];
                        }
                    }
                }
            }
        }
        return $result;

    }
    /**
     * [changeOrderState This method changes the state of suborders.
     * @param [int] $post_state_id []
     * @return [type] [description]
     */
    protected function changeOrderState($post_state_id){
      $order_id = $this->order_id;
      $suborder_id = $this->suborder_id;
      $suborder_id_query = "";
      if(!empty($suborder_id) || $suborder_id != 0){
          $suborder_id_query = "AND suborder_id In (".explode(',',$suborder_id).")";
      }
      $sql  = "UPDATE `".DB_PREFIX."suborder` ";
      $sql .=  "set order_status_id = ".(int)$post_state_id;
      $sql .=   "WHERE order_id =".$order_id." ".$suborder_id_query;
      $result = $this->db->query($sql);
      if(!$result){
        throw new Exception("Error in change order state.");
      }
    }
    /**
     * [addOrderHistory - This method add history of a suborders.]
     * @param [int] $status_id  [Status id of an suborder]
     * @param [int] $notify     [If notification send then 1 or if not sent then 0]
     * @param [int] $notify_sms [If sms sent then 1 or if not sent then 0]
     * @param string $comment    [Cotains Comment for history]
     * @param string $notes    [Contains Notes for history]
     */
    protected function addOrderHistory($status_id, $notify,$notify_sms,$comment = '',$notes = ''){
      $order_id = $this->order_id;
      $suborder_id = $this->suborder_id;
      if (!empty($suborder_id)) {
        $sql  = "INSERT INTO " . DB_PREFIX . "order_history ";
        $sql .= "(`order_id`,`suborder_id`,`order_status_id`,`notify`,`notify_sms`,`comment`,`date_added`,`notes`) ";
        $sql .= "Values ";
        foreach ($suborder_id as $sid) {
           $sql .= "(".$order_id.",";
           $sql .=     $sid.",";
           $sql .=     $status_id.",";
           $sql .=     $notify.",";
           $sql .=     $notify_sms.",'";
           $sql .=     $comment;
           $sql .=     "',NOW(),'";
           $sql .=     $notes;
           $sql .= "'),";
        }
        $sql = rtrim($sql,',').';';
        $status = $this->db->query($sql);
        if(!$status){
          throw new Exception("Failed to add history.");
        }
      }
    }

    /**
     * [notifyCustomerByemail description]
     * @param [string] $subject [Subject of the notification]
     * @param [string] $message [Message of notification]
     * @return [bool] [true if success]
    */
    protected function notifyCustomerByEmail( $subject, $message ){
      $customer_id = $this->customer_id; // Getting customer_id
      $customer_email = $this->customer_email; // Getting customer_eail
      $additional_emails = $this->customer_addtional_emails; // Getting customer additional emails
      if(empty($customer_email)){
        if(empty($customer_id)){
          $customer_id = $this->customer_id = $this->getCustomerId();
        }
        $customer_details = $this->getCustomerDetails();
        $customer_email = $this->customer_email = $customer_details['email'];
        $this->customer_telephone = $customer_details['telephone'];
      }
      if(empty($additional_emails)){
        $additional_emails = $this->customer_addtional_emails = $this->getCustomerAddtionalEmail();
      }
      $this->sendEmail($customer_email,$subject,$message,$additional_emails);
    }
    /**
     * [notifyCustomerBySms description]
     * @param [string] $message - Message of phone notification
     * @return [type] [description]
    */
    protected function notifyCustomerBySms($message){
      $customer_id = $this->customer_id; // Getting customer_id
      $customer_telephone = $this->customer_telephone; // Getting customer_telephone
      if(empty($customer_telephone)){
        if(empty($customer_id)){
          $customer_id = $this->customer_id = $this->getCustomerId();
        }
        $customer_details = $this->getCustomerDetails();
        $this->customer_email = $customer_details['email'];
        $customer_telephone = $this->customer_telephone = $customer_details['telephone'];
      }
      $this->sendSms($customer_telephone,$message);
    }

    protected function notifySellerByEmail(){

    }

    protected function notifySellerBySms(){

    }
    protected function notifyOperationsByEmail(){

    }

    protected function notifyOperationsBySms(){

    }


    /**
     * [getStateIdByname description]
     * @param  [string] $state [state name such as 'PRCD','PRTL_SHPD','FL_SHPD','OT_FR_DLRY']
     * @param  [int] $language_id [Language Id]
     * @return [int]   $id     [id of state]
     */
    protected function getStateIdByname($state,$language_id){
      $sql  = "Select order_status_id from ".DB_PREFIX."order_status ";
      $sql .=    "where order_status = '".$state."' AND ";
      $sql .=           "language_id = ".(int)$language_id;
      $result = $this->db->query($sql);
      if($result->num_rows > 0){
         $state_id = $result->row;
          return $state_id['order_status_id'];
      }

    }
    /**
     * [getSubordersIds This function is for getting suborder ids and customer ids of an order]
     * @return [array] [It returns an array of suborder_ids]
     */
    private function getSubordersIds(){
      $order_id = $this->order_id;
      $suborder_ids = array();
      $sql  = "Select suborder_id from " . DB_PREFIX . "suborder ";
      $sql .=   "where order_id = ".$order_id;
      $result = $this->db->query($sql);
      if(!empty($result->rows)){
        return array_column($result->rows,'suborder_id');
      }
      else{
        //throw new Exception("No Suborders found for this order id ".$order_id);
      }
    }
    /**
     * [getCustomerId This method is For Getting Customer Id of an order]
     * @return [Int] [It returns Customer Id of an order]
     */
    private function getCustomerId(){
      $order_id = $this->order_id; // Getting order id
      $sql  = "Select customer_id from ".DB_PREFIX."order ";
      $sql .=     "where order_id = ".$order_id;
      $result = $this->db->query($sql);
      if($result){
        $row = $result->row;
        return $row['customer_id'];
      }
      else{
        throw new Exception("No Customer Id found in order table with order_id ".$order_id);
      }
    }
    /**
     * [getCustomerDetails This method is for setting the customer email and telephone]
     * @return [type] [description]
     */
    private function getCustomerDetails(){
      $customer_id = $this->customer_id; // Getting customer id.
      $result = $this->db->query("Select email, telephone from ".DB_PREFIX."customer where customer_id = ".$customer_id);
      if ($result) {
        return $result->row;
      }
      else{
        throw new Exception("No Customer found in Customer table with customer_id".$customer_id);
      }
    }
    /**
     * [getCustomerAddtionalEmail This method is for getting customer addtional mail address]
     * @return [type] [description]
     */
    private function getCustomerAddtionalEmail(){
      $customer_id = $this->customer_id; // Getting customer id.
      $additional_emails = array() ;
      if(empty($customer_id)){
        $customer_id = $this->customer_id = $this->getCustomerId();
      }
      $sql  = "Select email from ".DB_PREFIX."customer_additional_email ";
      $sql .="where customer_id = ".$customer_id;
      $result = $this->db->query();
      if($result){
        $rows = $result->rows;
        foreach( $rows as $row){
          $additional_emails[] = $row['email'];
        }
      }
      return $additional_emails;
    }
  /**
   * sendEmail
   * @return mixed
   * @info This method is for notifying the customer by email
   * @param [string] $email [Email Address]
   * @param [string] $subject [Subject of the email]
   * @param [string] $message [Message of email]
   * @param [array] $bbcc [Array of addition email addresses]
   * @author Sudhanshu Jain
   * @date 27-Oct-2016
   */
   private function sendEmail($email,$subject,$message,$bbcc){

   }

   private function sendSms( $telephone , $message ){

   }

  private function getSuborderProductId(){
       $order_id = $this->order_id;
       $sql  = "Select product_id from ".DB_PREFIX."order_product ";
       $sql .=      "where order_id =".$order_id;
       $result = $this->db->query($sql);
       if($result){
         $rows = $result->rows;
         return array_column('product_id',$rows);
       }
    }

     //private function getSellerIds(){

     //}

    // private function getSellerDetails(){

     //}
}
