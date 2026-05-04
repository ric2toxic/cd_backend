<?php
declare(strict_types=1);

require_once( DIR_BASE .   'api/wholesalebox_bank_details.php' );
require_once( DIR_SYSTEM . 'library/operations/orders/order_info.php' );
require_once( DIR_SYSTEM . 'library/operations/orders/suborder.php' );
require_once( DIR_SYSTEM . 'library/operations/courier_factory/courier_base.php' );
require_once( DIR_SYSTEM . 'library/operations/payment_gateway/payment_gateway_base.php' );
require_once( DIR_SYSTEM . 'library/operations/payment_gateway/citrus.php' );
require_once( DIR_SYSTEM . 'library/operations/payment_gateway/razorpay.php' );
require_once( DIR_SYSTEM . 'library/operations/returns/return_action_base.php' );

/**
 * Main Class for getting Customer's all Order Related Info.
 * It is recommended to utilize the API function common between Web APIs and Mobile APIs
 * @Author Nishu, 2018
 */
class CustomerOrderInfo {

  private $registry = null;
  private $db       = null;
  private $load     = null;

  public $currency_code  = 'INR';
  public $currency_value = 1;

  public function __construct(Controller $controller) {
    $this->registry = $controller;
    $this->db       = $controller->db;
    $this->load     = $controller->load;
  }

  /**
   * Public method which returns wholesalebox's bank details
   * @author: Nishu, Aug 2018
  */
  public function setWsbBankAcDetails() : array{
    $data = array();

    $data['A/c Name']    = ACCOUNT_HOLDER_NAME;
    $data['A/c Number']  = ACCOUNT_NUMBER;
    $data['IFSC Code']   = IFSC_CODE;
    $data['Bank']        = BANK_BRANCH;
   
    return $data;
  }

  /**
   * Public method which returns wholesalebox's UPI ID
   * @author: Nishu, Aug 2018
  */
  public function setWsbUpiId() : array{
    $data = array();

    $data['UPI ID (VPA)'] = UPI_ID;
    
    return $data;
  }

  /**
   * public Method to get all Order(s) for given customer_id
   * @param: $result- Query result
   * @author: Nishu, Aug 2018
  */
  public function getAllOrdersByCustomerId(array $data, $condition = '') : array{
    
    $result = array();
    //Is empty check
    if(empty($data) || empty($data['customer_id'])){
        return $result;
    }
    
    //Apply Extra checks/filters on Order
    $whr  = $this->extraChecksForOrders($data);

    //Apply Extra joins on Order(s)
    $join = $this->extraJoinsForOrders($data);
   
    //Set value for 'offset' key AS - Number of items should be shown in list

    $data['offset'] = (int)($data['offset'] ?? 3);

    if(!empty($condition)){
      $condition = ' AND '.$condition;
    }

    $select_sql = "
                    SELECT 
                        o.order_id
                    FROM
                       " . DB_PREFIX . "order AS o
                    INNER JOIN
                       " . DB_PREFIX . "suborder AS osub ON osub.order_id = o.order_id AND osub.order_status_id > 0
                    ". $join ." 
                    WHERE
                       o.customer_id = ". (int)$data['customer_id']. $condition . $whr ."
                    GROUP BY 
                       o.order_id
                    ORDER BY 
                       o.order_id DESC
                    LIMIT
                       0, ".(int)$data['offset']."
                  ";
    

    $qry_result = $this->db->query($select_sql);


    if($qry_result->num_rows > 0){
        $result = $qry_result->rows;
        $result = array_combine(array_column($result, 'order_id'), $result);
    }
    return $result;
  }

  /**
   * Private method to create where condition check for order filter in order list API
   * @author: Nishu, Aug 2018
  */
  private function extraChecksForOrders(array $data) : string{
    $whr = '';
    
    //If order_type_filter is not empty
    if(!empty($data['order_type_filter'])){
      $order_type_filter = $data['order_type_filter'];
      
      //Check for order where payments pending (not cleared complete payment against order)
      if($order_type_filter == ORDER_TYPE_FILTER['PAYMENT_PENDING_ORDERS']){
        $whr = " 
                AND o.operations_status IN ('none', 'dont_dispatch')
                AND osub.order_status_id IN (
                                           ".(int)ORDER_STATUS['Pending'].",
                                           ".(int)ORDER_STATUS['Processed'].",
                                           ".(int)ORDER_STATUS['Tentative Processed']."
                                          )
               ";
      }elseif($order_type_filter == ORDER_TYPE_FILTER['DELIVERED_ORDERS']){ 
        //Check for orders, having at least one suborder is delivered 
        $whr = " 
                AND osub.order_status_id IN (
                                           ".(int)ORDER_STATUS['Complete'].",
                                           ".(int)ORDER_STATUS['Delivered']."
                                          )
               ";
      }elseif($order_type_filter == ORDER_TYPE_FILTER['CANCELLED_ORDERS']){
        //Check for orders, having at least one suborder is Cancelled
        $whr = " AND osub.order_status_id = ".(int)ORDER_STATUS['Canceled'];

      }      
    }//End of if

    //order_ids in list must be smaller then beyond_order_id
    if(!empty($data['beyond_order_id'])){
        $whr .= " AND o.order_id < ". (int)$data['beyond_order_id']; //Default Value is latest order
    }

    //filter_order_or_invoice_no in list to filter for order_no or invoice_no
    if(!empty($data['filter_order_or_invoice_no'])){
        $whr .= " AND (
                    o.order_no LIKE '%".$this->db->escape($data['filter_order_or_invoice_no'])."%'
                      OR
                    CONCAT(osub.invoice_prefix, osub.invoice_no) LIKE '%".$this->db->escape($data['filter_order_or_invoice_no'])."%'
                  ) 
                ";
    }

    return $whr;
  }

  /**
   * Private method to create Extra Join(s) for order filter in order list API
   * @author: Nishu, Aug 2018
  */
  private function extraJoinsForOrders(array $data) : string{
    $join = '';
    
    //If order_type_filter is not empty
    if(!empty($data['order_type_filter'])){
      $order_type_filter = $data['order_type_filter'];
      
      //Check for order(s) having at least one Return Added
      if($order_type_filter == ORDER_TYPE_FILTER['RETURNED_ORDERS']){
        //Check for orders, having at least one Return Added
        $join = " INNER JOIN " . DB_PREFIX . "master_return AS mr ON mr.order_id = o.order_id AND mr.cancel_return = 0 ";
      }      
    }//End of if

    return $join;
  }

  /**
   * Public method to get multiple order details for order listing page
   * @param: order_ids
   * @return: Array, Order(s) detail
   * @author: Nishu, Aug 2018
  */
  public function getMultipleOrderDetails(array $order_ids, string $suborder_id = '') : array{
    
    $order_info = array();
    if(!empty($order_ids)){
      $selector = array(
                  'order'         => array('select' => array(
                                                          'order_id',
                                                          'order_no',
                                                          'customer_id',
                                                          'total AS order_total',
                                                          'date_added AS order_date',
                                                          'payment_method AS payment_mode',
                                                          'payment_code',
                                                          'total',
                                                          'currency_code',
                                                          'currency_value',
                                                          'payment_firstname',
                                                          'payment_lastname',
                                                          'payment_company',
                                                          'payment_address_1',
                                                          'payment_address_2',
                                                          'payment_city',
                                                          'payment_postcode',
                                                          'payment_country',
                                                          'payment_zone',
                                                          'payment_method',
                                                          'shipping_firstname',
                                                          'shipping_lastname',
                                                          'alternate_contact_number as shipping_alternate_contact_number',
                                                          'shipping_company',
                                                          'shipping_address_1',
                                                          'shipping_address_2',
                                                          'shipping_city',
                                                          'shipping_postcode',
                                                          'shipping_country',
                                                          'shipping_zone',
                                                          'bank_slip_image'
                                                        ),
                                           'sort' => array('order_id' => 'DESC')
                                    ) ,
                  'suborder'      => array( 'select' => array(
                                                          'suborder_id',
                                                          'order_status_id',
                                                          'total AS total_amt',
                                                          'invoice_no',
                                                          'buyer_invoice_id',
                                                          'courier_partner',
                                                          'tracking_no'
                                                        )

                                     ),
                  'order_history' => array( 'select' => array(
                                                          'suborder_id', 
                                                          'order_status_id',
                                                          'comment',
                                                          'date_added AS status_date'
                                                        ),
                                            'sort'  => array(
                                                        'order_history_id' => 'ASC'
                                                       )
                                      ),
                 );
        //Get OrderInfo details
        $order_info = OrderInfo::getOrderInfo($this->db, $order_ids, $suborder_id,$selector);
    }

    return $order_info;
  }

  /**
   * Public method to get multiple order details for order listing page
   * @param: order_info, Array
   * @return: order_info, Array
   * @author: Nishu, Aug 2018
  */
  public function getManipulateOrderInfo(array $order_info) : array{
    $updated_order_info = array();

    //Loop at order Level
    foreach ($order_info as $order_id => $data) {
      $updated_order_info['orders'][$order_id] = $data['order'];
      
      //Set default key values
      $updated_order_info = $this->setOrderKeyWithDefaultValue($order_id, $updated_order_info);

      //Set and get suborder details
      $suborder_detail = $this->setSubOrderDetails($order_id, $data);
      $updated_order_info['orders'][$order_id]['suborders'] = $suborder_detail;
    }

    return $updated_order_info;
  }

  /**
   * public method to set suborder details 
   * @param:  Array
   * @return: Array
   * @author: Nishu, Aug 2018
  */
  public function setSubOrderDetails(int $order_id, array $data) : array{
    $this->currency_code  = $data['order']['currency_code'] ?? 'INR';
    $this->currency_value = $data['order']['currency_value'] ?? 1;

    $suborder_detail = array();
    
    //Loop for order Data
    foreach ($data['suborder'] as $sid => $value) {

      $suborder_detail[$sid] = $this->setIndividualSuborderDetails((string)$sid, $value);
    }
    return $suborder_detail;
  }

  /**
   * Public method to set information for suborder
   * @author: Nishu. Aug 2018
  */
  public function setIndividualSuborderDetails(string $sid, array $value) {
      $suborder_detail = array();

      $suborder_detail = $value;

      $total_amt = $suborder_detail['total_amt'];
      $suborder_detail['total_amt']     = $this->registry->currency->format($total_amt, $this->currency_code, $this->currency_value);
      $suborder_detail['total_amt_row'] = $total_amt;
      
      $suborder_detail['download_invoice_link']      = '';
      $suborder_detail['show_download_invoice_link'] = false;

      //Check Buyer invoice is generated or not
      if($value['invoice_no'] > 0 && $value['buyer_invoice_id']){
        //BuyerInvoice Download Link
        $file_path = $this->setBuyerInvoiceLink($value);
        $suborder_detail['download_invoice_link'] = $file_path;
        $suborder_detail['show_download_invoice_link'] = true;
      }
      
      //Set Suborder Shipment From Key
      $shipment_from = $this->setShipmentFrom($value['suborder_id']);
      $suborder_detail['shipment_from'] = $shipment_from;

      //Set Suborder Status History
      $order_history = $this->setSuborderStatusHistory($value['order_history']);
      $order_history = $this->orderHistoryUpdateForMultiplePendingStatus($order_history);
      $suborder_detail['order_history'] = $order_history;

      //Set Suborder status Date
      $order_status_date = $this->setSuborderStatusDate($order_history);
      $suborder_detail['status_date'] = $order_status_date;

      //Set Suborder status Comment
      $order_status_comment = $this->setSuborderStatusComment($order_history);
      $suborder_detail['status_comment'] = $order_status_comment;

      //Set Suborder status
      $order_status = $this->setSuborderStatus($value['order_status_id']);
      $suborder_detail['suborder_status'] = ORDER_STATUS_LABEL[$order_status];

      //Show or not Like/Dislike button at suborder level
      $suborder_detail['show_like_dislike_btn'] = $this->showLikeDislike($order_history, (int)$value['order_status_id']);

      //Set Suborder Status Color Code
      $order_status_color_code = ORDER_STATUS_FOR_PROGRESS_BAR[$order_status]['color_code'];
      $suborder_detail['status_color_code'] = $order_status_color_code;

      //Set Suborder Status Color
      $order_status_color = ORDER_STATUS_FOR_PROGRESS_BAR[$order_status]['color'];
      $suborder_detail['status_color'] = $order_status_color;

      //Set Suborder Status Progress
      $order_status_progress = ORDER_STATUS_FOR_PROGRESS_BAR[$order_status]['progress'];
      $suborder_detail['status_progress'] = $order_status_progress;

      //Set Suborder Current Status's Extra info
      $suborder = $suborder_detail;
      $current_status_info = $this->setSuborderCurrentStatusInfo($value['order_status_id'], $suborder);
      $suborder_detail['current_status_info'] = $current_status_info;

      return $suborder_detail;
  }

  /**
   * @info: Private function to show Like or Dislike button
   * @author: Nishu, Sept 2018
  */
  private function showLikeDislike(array $order_history, int $order_status_id): bool {
    $is_show = false;
    //Check if order order history is not created
    if(!empty($order_history)){
      //Loop over completed order history
      foreach ($order_history as $key => $order_status) {
        //Order Status date
        $order_date = new DateTime($order_status['status_date']);
        //Current date
        $curr_date  = new DateTime("now");

        //Get two date diffrence
        $date_diff  = $curr_date->diff($order_date);
        //Get total number of days diffrence between two dates
        $days_diff  = (int)$date_diff->days ?? 0;

        //Check Order status 
        if(
          in_array($order_status['order_status_id'], ORDER_STATUS_CLUSTERS['delivered']) 
          && in_array($order_status_id, ORDER_STATUS_CLUSTERS['delivered']) 
          && $days_diff >= 7
        ){
          $is_show = true;
          break;
        }
      }
    }

    return $is_show;
  }

  /**
   * public function to set Shipment From Key
   * @author: Nishu, Aug 2018
  */
  public function setShipmentFrom(string $sid) : string{
      $shipment_from = '';
      
      foreach (SUBORDER_CITY_CODE as $key => $value) {
        if(strpos($sid, $key) > 0){
          $shipment_from = 'Shipment From '.$value;
          break;
        }
      }
      
      return $shipment_from;
  }

  /**
   * public function to set Buyer invoice link to data set
   * @author: Nishu, Aug 2018
  */
  public function setBuyerInvoiceLink(array $data) : string{
      $order_id = $data['order_id'];
      $sid      = $data['suborder_id'];
      //BuyerInvoice Download Link
      $file_name = array();
      $file_name['order_id']    = $order_id;
      $file_name['suborder_id'] = $sid;
      $file_name = base64_encode(serialize($file_name));

      $file_path = $this->registry->securefiledownload->getDownloadLink('buyer_b2b_invoice',$file_name,false);
      
      return $file_path;
  }

  /**
   * public function to set some order level keys with default values
   * @author: Nishu, Aug 2018
  */
  public function setOrderKeyWithDefaultValue(int $order_id, array $updated_order_info) : array{

      $order_total = $updated_order_info['orders'][$order_id]['order_total'];

      $this->currency_code  = $updated_order_info['orders'][$order_id]['currency_code']  ?? 'INR';
      $this->currency_value = $updated_order_info['orders'][$order_id]['currency_value'] ?? 1;

      //Order Total and OrderTotal Row value
      $updated_order_info['orders'][$order_id]['order_total']      = $this->registry->currency->format($order_total, $this->currency_code, $this->currency_value);
      $updated_order_info['orders'][$order_id]['order_total_row']  = $this->registry->currency->format($order_total, $this->currency_code, $this->currency_value, false);
      $updated_order_info['orders'][$order_id]['total']            = $this->registry->currency->format($order_total, $this->currency_code, $this->currency_value, false);

      //Date Format for order_date as 'd M Y'
      $order_date = $updated_order_info['orders'][$order_id]['order_date'];
      $order_date = !empty($order_date)? date('d M Y', strtotime($order_date)) : '';
      $updated_order_info['orders'][$order_id]['order_date']                    = $order_date;

      //Payment Status order wise, default value NOT_PENDING
      $updated_order_info['orders'][$order_id]['payment_status']                = '';

      //Show already generated payment link
      $updated_order_info['orders'][$order_id]['show_payment_link']             = false;
      $updated_order_info['orders'][$order_id]['payment_link']                  = '';

      //Show link to generat new payment link
      $updated_order_info['orders'][$order_id]['show_request_new_payment_link'] = false;
      $updated_order_info['orders'][$order_id]['request_new_payment_link']      = '';

      //Total received amount for that order
      $updated_order_info['orders'][$order_id]['total_received']                = 0;    

      //Balance amount for that order to pay
      $updated_order_info['orders'][$order_id]['balance']                       = 0;

      return $updated_order_info;
  }

  /**
   * public method to set suborder histpry statuses for customer
   * @param: $order_status_history, Array
   * @return: $order_status_history, Array
   * @author: Nishu, Aug 2018
  */
  public function setSuborderStatusHistory(array $order_status_history) : array{
    $result_order_history = array();

    if(!empty($order_status_history)){
      $order_history = array();
      foreach ($order_status_history as $key => $value) {

        //Set Suborder status
        $order_status = $this->setSuborderStatus($value['order_status_id']);

        //For first itteration in loop
        if(empty($order_history) ){
          $last_order_status = $new_order_status = $order_status;
          $order_history[$key] = $new_order_status;
        }else{
          $new_order_status = $order_status;
        }
        if(!empty($order_history) && $last_order_status != $new_order_status){
          $order_history[$key] = $new_order_status;
        }
        //To make current value as last value
        $last_order_status = $new_order_status;

      }//End of Foreach loop

      //Set all other values in $order_history from $order_status_history
      foreach ($order_history as $key => $value) {
        $result_order_history[$key]['order_status']    = $value;
        $result_order_history[$key]['label']           = (isset(ORDER_STATUS_LABEL[$value])) ? ORDER_STATUS_LABEL[$value] : '';
        $result_order_history[$key]['order_status_id'] = $order_status_history[$key]['order_status_id'];
        $status_date                                   = $order_status_history[$key]['status_date'];
        $status_date                                   = !empty($status_date)? date('d M Y', strtotime($status_date)) : '';
        $result_order_history[$key]['status_date']     = $status_date;
        $result_order_history[$key]['comment']         = $order_status_history[$key]['comment'];
      }
    }

    return $result_order_history;
  }

  /**
   * public method to set suborder histpry statuses for customer
   * @param: $order_status_history, Array
   * @return: $order_status_history, Array
   * @author: Nishu, Aug 2018
  */
  public function orderHistoryUpdateForMultiplePendingStatus(array $order_status_history) : array{
    //Not empty check
    if(!empty($order_status_history)){

      $first_time_order_received = '';

      //Set all other values in $order_history from $order_status_history
      foreach ($order_status_history as $key => $value) {

        if($value['order_status'] == 'order_received'){
          
          //Check id order_received marked first time it will show as Order Received 
          if( empty($first_time_order_received) ){

            $first_time_order_received = 'order_received';

          }else{

            //If order_received / Pending marked multiple times 
            $order_status_history[$key]['label'] = 'Moved To Pending';

          }//End of else block
        }
      }//End of Foreach
    }

    return $order_status_history;
  }

  /**
   * public method to set suborder histpry statuses for customer
   * @param: $order_status_history, Array
   * @return: $order_status_history, Array
   * @author: Nishu, Aug 2018
  */
  public function setSuborderCurrentStatusInfo(string $order_status, array $suborder) : array{
    $current_status_info = array();
    $_courier_base       = Null;
    $_return_action_base = Null;
    $order_id            = $suborder['order_id'];

    if(!empty($order_status)){
      $current_status_info = CURRENT_ORDER_STATUS_INFO[$order_status];

       //Check if any extra info exist for current order status
       if(!empty($current_status_info)){
         foreach ($current_status_info as $key => $value) {
           if($key == 'track'){
              if(empty($_courier_base)){//Check if courier base object is not created
                $_courier_base = new CourierBase(); //Creat object and set to private global variable
              }
              //Get tracking URL for courier company
              $track_url = $_courier_base->getCourierTrackingURL($this->db, $suborder['courier_partner'], $suborder['tracking_no']);

              if(empty($track_url)){
                unset($current_status_info[$key]);
              }else{
                $current_status_info[$key]['button']['url'] = $track_url;
              }
           }else if($key == 'return'){

              $ctoken = $_SESSION['ctoken'] ?? '';
              //Now Return/Replacement button will always display after order status delivered
              $return_url = HTTPS_CATALOG.'index.php?route=account/return&ctoken='.$ctoken.'&order_id='. $order_id;
              $current_status_info[$key]['button']['url'] = $return_url;
           }

           //For Cancelled order add comment field at the end of message
           if($order_status == ORDER_STATUS['Canceled']){
             $current_status_info[$key]['message'] .= $suborder['status_comment'];
           }

         }//End of Foreach loop
       }//End of If
    }//End of If

    return $current_status_info;
  }

  /**
   * public method to get suborder status on behalf of customer
   * @param: $order_status
   * @return: String
   * @author: Nishu, Aug 2018
  */
  public function setSuborderStatus( string $order_status) : string{
    $new_order_status = '';
    foreach (ORDER_STATUS_CLUSTERS as $key => $value) {
      if(in_array($order_status, $value)){
        $new_order_status = $key;
      }
    }
    return $new_order_status;
  }

  /**
   * public method to get suborder status date on behalf of customer
   * @param: $order_status
   * @return: String
   * @author: Nishu, Aug 2018
  */
  public function setSuborderStatusDate(array $order_history) : string{

    $order_status_details = end($order_history);
    $order_status_date = !empty($order_status_details['status_date'])? $order_status_details['status_date'] : '';
    $order_status_date = !empty($order_status_date)? date('d M Y', strtotime($order_status_date)):'';

    return $order_status_date;
  }

  /**
   * public method to get suborder status date on behalf of customer
   * @param: $order_status
   * @return: String
   * @author: Nishu, Aug 2018
  */
  public function setSuborderStatusComment(array $order_history) : string{

    $order_status = end($order_history);
    $order_status_comment = !empty($order_status['comment'])? $order_status['comment'] : '';
    
    return $order_status_comment;
  }

  /**
   * @info: Public method to set payment status order wise 
   * @param: $data Array
   * @author: Nishu, Aug 2018
  */
  public function calculateTotalReceivedAndPendingBalanceAmount(array $data) : array{
      $order_data = array();
      
      //Loop over Orders
      foreach ($data['orders'] as $oid => $value) {

        $order_data[$oid]['order_id']     = $oid;
        $order_data[$oid]['total']        = $value['total'];
        $order_data[$oid]['payment_code'] = $value['payment_code'];
        
      }
      
      $payment_gateway = new PaymentGatewayBase($this->registry);
      $order_data = $payment_gateway->calculateTotalReceivedAndPendingBalanceAmount($order_data);
      
      return $order_data;
  }

  /**
   * @info: Public method to set payment status order wise and payment link will be shown or not
   * @param: $order_info Array
   * @author: Nishu, Aug 2018
  */
  public function setPaymentStatus(array $order_info){

    //Order status array to check for payment status
    $order_status = array(
                       ORDER_STATUS['Pending'],
                       ORDER_STATUS['Processed'],
                       ORDER_STATUS['Tentative Processed']
                      );
   
    //Loop over Orders
    foreach ($order_info['orders'] as $oid => $value) {

        //Loop over suborder(s)
        foreach ($value['suborders'] as $sid => $sdata) {
          
          $order_status_id = (int)$sdata['order_status_id'];
          //Check for payment status is Pending or not, Default value set to Done
          if(
              in_array($order_status_id, $order_status)
                  &&
              $value['balance'] > 1
          ){
              $order_info['orders'][$oid]['payment_status'] = 'Pending';
              break;
          }
        }
    }
    return $order_info;
  }

  /**
   * @info: Public method to set payment status suborder wise and payment link will be shown or not
   *         This function is reapeted because of different data key array
   * @param: $order_info Array
   * @author: Nishu, Aug 2018
  */
  public function setPaymentStatusForOrderDetails(array $order_info){

    $payment_status = '';

    //Order status array to check for payment status
    $order_status = array(
                       ORDER_STATUS['Pending'],
                       ORDER_STATUS['Processed'],
                       ORDER_STATUS['Tentative Processed']
                      );
   
    //Loop over sub-orders
    foreach ($order_info['suborder'] as $sdata) {
          
      $order_status_id = (int)$sdata['order_status_id'];
      //Check for payment status is Pending or not, Default value set to Done
      if(
          in_array($order_status_id, $order_status)
              &&
          $order_info['order']['balance'] > 1
      ){
          $payment_status = 'Pending';
          break;
      }
        
    }
    return $payment_status;
  }

  /**
   * Public method to set order's payment related data to $order_info
   * @param: $order_data, $order_info
   * @return $order_info
   * @author: Nishu, Aug 2018
  */
  public function setPaymentStatusToOrderInfo($order_data, $order_info){
      
    if(!empty($order_data)){
      foreach ($order_data as $oid => $data) {
        foreach ($data as $key => $value) {
          $order_info['orders'][$oid][$key] = $value;
        }
      }
    }
    //Set payment status and payment link will be shown or not
    $order_info = $this->setPaymentStatus($order_info);

    return $order_info;
  }

  /**
   * Public method to get order's Link to do payment and also to request new payment link
   * @param: $order_data, $order_info
   * @return $order_info
   * @author: Nishu, Aug 2018
  */
  public function getPaymentLinkAndRquestNewPaymentLink($order_info){
      
    if(!empty($order_info)){
      $order_ids = array();

      //Get OrderIds only for pending payment status
      foreach ($order_info['orders'] as $oid => $value) {

        //Payment links show only for payment status pending and currency code in indian currency
        if(
          strtolower($value['payment_status']) == 'pending' 
          && strtoupper($value['currency_code']) == 'INR' 
        ){
          
          //Request for Generating New Payment Link
          $payment_url  = $this->createRequestNewPaymentLink($value);
          
          $order_info['orders'][$oid]['show_request_new_payment_link'] = true;
          $order_info['orders'][$oid]['request_new_payment_link']      = $payment_url;

          //Order Ids to get payment link from oc_order_payment
          $order_ids[] = $oid;
        }
      }
      //If any order is pending for payment
      if(!empty($order_ids)){
        //get Orderid wise payment links
        $payment_gateway = new PaymentGatewayBase($this->registry);
        $order_data = $payment_gateway->getPaymentLinkOrderWise($order_ids);

        //Set payment links order wise from oc_order_payment
        foreach ($order_data as $oid => $value) {
          $order_info['orders'][$oid]['show_payment_link'] = true;
          $order_info['orders'][$oid]['payment_link']      = $value['payment_link'];
        }
      }
      
    }
    
    return $order_info;
  }

  /**
   * Public method to create link for requesting new payment link
   * @param: array
   * @return: string
   * @author: Nishu, Aug 2018
  */
  public function createRequestNewPaymentLink($value){

    $payment_url = ''; 

    if(!empty($value) && strtolower($value['payment_status']) == 'pending' ){

      $url_param = 'customer_id='.$value['customer_id'].'&order_id='.$value['order_id'].'&order_no='.$value['order_no'].'&balance='.$value['balance'].'&total='.$value['total'];

      //Request for Generating New Payment Link
      $payment_url  = HTTPS_CATALOG.'api/customer_account/orders/rquestNewPaymentLink';
      $payment_url .= '&data=' . base64_encode($url_param);

    }

    return $payment_url;
  }

  /**
   * Public method to Request for Generating New Payment Link
   * @param: $order_info
   * @return $payment_url
   * @author: Nishu, Aug 2018
  */
  public function rquestNewPaymentLink(array $order_info) : string{
    $payment_url = '';

    //Request for Generating New Payment Link
    $order_data                = array();
    $order_data['order_id']    = $order_info['order_id'];
    $order_data['amount']      = sprintf("%.2f", $order_info['balance']);
    $order_data['order_total'] = sprintf("%.2f", $order_info['total']);
    $order_data['order_no']    = $order_info['order_no'];

    //Loop over Payment Gateway class Like: Citrus Or Razorpay
    foreach (OFFLINE_INVOICING_PAYMENT_GATEWAYS_CLASSES as $pg_class) {
      $payment_gateway = new $pg_class($this->registry);
      $response = $payment_gateway->generatePaymentLink($order_data);
      if ($response['responseMsg'] == 'SUCCESS') { //Successfully generated Payment Link
        $payment_url = $response['specialMsg'];
        break;
      }
    }

    return $payment_url;
  }

  /**
   * Public method to set address blocks of payment and shipping address of order
   * @param: Integer, Array
   * @author: Nishu, Aug 2018
  */
  public function setAddressBlocksOfOrder(int $order_id, array $order_info) : array{
    //Set Payment address related keys as sub-array
    if( !empty($order_info[$order_id]['order']) ){
      $order_info[$order_id]['order'] = setSubArrayWithKeyPrefix('payment', $order_info[$order_id]['order']);
    }

    //Set Payment address related keys as sub-array
    if( !empty($order_info[$order_id]['order']) ){
      if(!empty($order_info[$order_id]['order']['shipping_alternate_contact_number']))
      {
        $shipping_alternate_contact_number = json_decode($order_info[$order_id]['order']['shipping_alternate_contact_number']);

        if(is_array($shipping_alternate_contact_number))
        {
          $order_info[$order_id]['order']['shipping_alternate_contact_number'] = implode(",", $shipping_alternate_contact_number);
        }
        
      }

      $order_info[$order_id]['order'] = setSubArrayWithKeyPrefix('shipping', $order_info[$order_id]['order']);
    }

    return $order_info;
  }

  /**
   * Public method to set payment related info as subarray
   * @param:  Integer, Array
   * @return: Array
   * @author: Nishu, Aug 2018
  */
  public function setPaymentInfoOrderWise(int $order_id, array $order_info) : array{
    $payment_info = array();

    if(!empty($order_info[$order_id])){
      $order = $order_info[$order_id]['order'];

      $payment_info['payment_mode'] = $order['payment_mode'] ?? '';
      $payment_info['payment_code'] = $order['payment_code'] ?? '';

      $payment_status = $this->setPaymentStatusForOrderDetails($order_info[$order_id]);
      $order_info[$order_id]['order']['payment_status'] = $payment_info['payment_status'] = $payment_status;
      $currency_code = $order['currency_code'];

      //Payment links show only for payment status pending and currency code in indian currency
      if(
        strtolower($payment_status) == 'pending'
        && strtoupper($currency_code) == 'INR' 
      ){
        $payment_url = $this->createRequestNewPaymentLink($order_info[$order_id]['order']);

        $payment_info['show_request_new_payment_link'] = true;
        $payment_info['request_new_payment_link']      = $payment_url;

        $payment_gateway = new PaymentGatewayBase($this->registry);
        $order_data      = $payment_gateway->getPaymentLinkOrderWise($order_id);

        //Set payment links order wise from oc_order_payment
        foreach ($order_data as $oid => $value) {
          $payment_info['show_payment_link'] = true;
          $payment_info['payment_link']      = $value['payment_link'];
        }

      }else{
        $payment_info['show_request_new_payment_link'] = false;
        $payment_info['request_new_payment_link']      = "";
        
        $payment_info['show_payment_link'] = false;
        $payment_info['payment_link']      = "";
      }

      //Set wholesalebox's Bank Details
      $payment_info['bank_details'] = $this->setWsbBankAcDetails();

      //Set wholesalebox's UPI Details
      $payment_info['upi_details'] = $this->setWsbUpiId();

    }

    $order_info[$order_id]['order']['payment_info'] = $payment_info;
    
    return $order_info;
  }

    /**
   * Public method to get multiple order details for order listing page
   * @param: order_ids
   * @return: Array, Order(s) detail
   * @author: Nishu, Sept 2019
  */
  public function getOrdersInfoForOrderListPage(array $order_ids) : array{
    
    $order_info = array();
    if(!empty($order_ids)){
      $selector = array(
                  'order' => array('select' => array(
                                                'order_id',
                                                'order_no',
                                                'total AS order_total',
                                                'date_added',
                                                'bank_slip_image'
                                              ),
                                 'sort' => array('order_id' => 'DESC')
                          ) ,
                  'suborder'      => array( 'select' => array(
                                                          'suborder_id',
                                                          'order_status_id',
                                                          'total AS total_amt',
                                                          'invoice_no',
                                                          'buyer_invoice_id',
                                                          'courier_partner',
                                                          'tracking_no'
                                                        )

                                     ),
                  'order_history' => array( 'select' => array(
                                                          'suborder_id', 
                                                          'order_status_id',
                                                          'comment',
                                                          'date_added AS status_date'
                                                        ),
                                            'sort'  => array(
                                                        'order_history_id' => 'ASC'
                                                       )
                                      ),
                 );
        //Get OrderInfo details
        $order_info = OrderInfo::getOrderInfo($this->db, $order_ids, $suborder_id,$selector);
    }

    return $order_info;
  }

}
// close OrderInfo class
?>
