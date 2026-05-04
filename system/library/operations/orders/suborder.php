<?php
declare(strict_types=1);

/**
 * Main Class for getting/processing SubOrder Related Info.
 * @author Nishu, Aug 2018
 */
class Suborder {

  /**
   * @info : Public method to get suborder details for SHIPPING PREFERENCES
   * @param: $order_id integer
   * @author: Nishu, Aug 2018
  */
  public static function getAllSuborderDetailsOrderWise(Database\DB $db, int $order_id) : array{
    $selector = array(
                  'suborder'  => array( 
                                  'select' => array(
                                                'suborder_id',
                                                'order_status_id',
                                                'no_wsb_tape',
                                                'no_invoice_with_shipment',
                                                'courier_partner_preference'
                                              )

                                 )
                 );

    //Get OrderInfo details
    $order_info = OrderInfo::getOrderInfo($db, $order_id,'',$selector);

    return $order_info['suborder'];
  }

  /**
   * @info : Public static method to get suborder details
   * @param: $db DB, $order_id integer, $selector Array(Optional)
   *           $selector = array(
   *                'suborder_id',
   *                'order_status_id',
   *                'no_wsb_tape',
   *                'no_invoice_with_shipment',
   *                'courier_partner_preference'
   *            );
   *            if selector is not passed all fie4lds of suborder table will be returned
   * @return: $data Array
   * @author: Nishu, Aug 2018
  */
  public static function getSuborderInfo(Database\DB $db, string $suborder_id, array $selector) : array{

    $data = array();

    if(!empty($suborder_id)){
      $field_list = '*'; //Default field list
      
      if(!empty($selector)){
        $field_list = implode(',', $selector);
      }
      
      $sql = "
              SELECT
                suborder_id, ". $field_list ."
              FROM
                ". DB_PREFIX ."suborder 
              WHERE
                suborder_id = '". $db->escape($suborder_id) ."'
             ";
      $result = $db->query($sql);
      $data   = $result->row;
    }
    
    return $data;
  }

  /**
   * @info : Public method to get all suborder(s) list not having buyer invoice yet
   * @param: $order_id integer
   * @author: Nishu, Aug 2018
  */
  public static function getAllSubordersNotHavingBuyerInvoice(Database\DB $db, int $order_id) : array{
    $data = array();

    //Order_id not empty check
    if(!empty($order_id)){

        $sql = "
                SELECT 
                    suborder_id,
                    no_wsb_tape,
                    no_invoice_with_shipment,
                    courier_partner_preference
                FROM
                    " . DB_PREFIX . "suborder
                WHERE
                    ( buyer_invoice_id <= 0 OR buyer_invoice_id IS NULL )
                    AND ( invoice_no <= 0 OR invoice_no IS NULL )
                    AND order_id = ". (int)$order_id ."
               ";
        $result = $db->query($sql);
        if( $result->num_rows > 0 ){
            $data = array_combine(
                      array_column($result->rows, 'suborder_id'),
                      $result->rows
                    );
        }
    }

    return $data;
  }


  /**
   * Public Method to Update all shipping Preferences details for single or multiple suborders
   *    If customer mark to update whole order's shippingPreferences, 
   *       All suborder which are not having generated buyer invoice yet, will get Updated
   * @param: $data Array
   * @return: $preference_data Array AND  list of suborder(s) which are updated
   * @author: Nishu, Aug 2018
  */
  public static function updateShippingPreferencesForOrderDetailPage(Database\DB $db, array $data) : bool{
    $is_updated = false;

    if( 
        isset($data['no_wsb_tape']) 
        && isset($data['no_invoice_with_shipment']) 
        && isset($data['courier_preference'])
        && isset($data['suborder_id'])
        && isset($data['suborder_ids'])
    ){
      
        $sql = "
                UPDATE 
                    " . DB_PREFIX . "suborder
                SET
                    no_wsb_tape                = ". (int)$data['no_wsb_tape'] .",
                    no_invoice_with_shipment   = ". (int)$data['no_invoice_with_shipment'] .",
                    courier_partner_preference = '". $db->escape($data['courier_preference']) ."'
               ";
        //Check if Update request is for whole order or specific suborder
        if(!empty($data['apply_whole_order'])){
            $sql .= " 
                      WHERE
                         suborder_id IN ('". implode("','", $data['suborder_ids']) ."')
                    ";
        }else{
            $sql .= " 
                      WHERE
                         suborder_id = '". $db->escape($data['suborder_id']) ."'
                    ";
        }

        //Execute query to update suborders
        $is_updated = $db->query($sql);
    }
    
    return $is_updated;
  }

   /**
   * Public Method to set all shipping Preferences details
   * @param:  $data Array
   * @return: $preference_data Array
   * @author: Nishu, Aug 2018
  */
  public function shippingPreferencesForOrderDetail(array $data) : array{
    $preference_data  = array();

    //Not empty checks for suborder wise shipping preferences
    if(!empty($data['order_id']) && !empty($data['suborder_id'])){
        $order_id = (int)$data['order_id'];

        //get suborder details for SHIPPING PREFERENCES order wise
        $suborders = Suborder::getAllSuborderDetailsOrderWise($order_id);

        //Empty checks
        if(!empty($suborders)){

          foreach ($suborders as $sid => $value) {

            if($sid == $data['suborder_id']){//Check for current suborder
              
              $preference_data['suborder_id']                = $value['suborder_id'];
              $preference_data['no_wsb_tape']                = $value['no_wsb_tape'];
              $preference_data['no_invoice_with_shipment']   = $value['no_invoice_with_shipment'];
              $preference_data['courier_partner_preference'] = $value['courier_partner_preference'];

              if(!in_array($value['order_status_id'], ORDER_STATUS_SHIPPED)){
                //All suborder ids for which shipping preferences editable
                $preference_data['editable_suborders'][]         = $sid;
              }
            }
          }
          if(!empty($preference_data['editable_suborders'])){
            //Shipping preferences are editable or not
            $preference_data['edit_button'] = true;

            //Get active and available courier partners
            $all_active_courier_partners = CourierBase::getAllActiveCourierPartners($this->_db);
            $preference_data['courier_preferences'] = $all_active_courier_partners;
          }
        }        
    }

    return $preference_data;
  }

  /**
   * Public method to check and set values shipping_preferences editable or not 
   * @param:  $res_data Array
   * @return: $res_data Array
   * @author: Nishu, Sept 2018
  */
  public static function isShippingPreferenceEditable(Database\DB $db, array $res_data ): array {
    $res_data['show_edit_button']       = false;
    $res_data['all_courier_preference'] = array();

    //Check for required data keys
    if(
      !empty($res_data) 
      && !empty($res_data['order_id'])
      && !empty($res_data['suborder_id']) 
      && !empty($res_data['suborder_ids']) 
    ){
      //Suborder data fetching
      $selector        = array( 'order_status_id', 'buyer_invoice_id' );
      $suborder_data   = Suborder::getSuborderInfo($db, $res_data['suborder_id'], $selector);
      $suborder_status = (int)$suborder_data['order_status_id'];
      $buyer_invoice_id= (int)$suborder_data['buyer_invoice_id'];

      //For cancelled suborders shipping preferences will not be editable
      if( empty($buyer_invoice_id) && $suborder_status != ORDER_STATUS['Canceled'] ){
        $res_data['show_edit_button']       = true;
        $res_data['all_courier_preference'] = CourierBase::getAllActiveCourierPartners($db);
      }

    }

    return $res_data;
  }

  /**
   * Public Methos to get suborder info for multiple order_ids
   * @param: $db, $order_ids
   * @return: Array
   * @author: Nishu, Jan 2019
  */
  public static function getSuborderInfoByOrderIds(Database\DB $db, array $order_ids, string $field_list):array{
    $result =  array();

    $sql = "
            SELECT
               order_id, suborder_id, ".$field_list."
            FROM
              ".DB_PREFIX."suborder
            WHERE
              order_id IN (". implode(',', $order_ids).")
           ";
    $qry = $db->query($sql);

    if($qry->num_rows > 0 ){
      foreach ($qry->rows as $key => $value) {
        $result[$value['order_id']][$value['suborder_id']] = $value;
      }
    }
    return $result;
  }
       
  /**
   *
   * checking order is Delivered first time
   * @author: Nishu, Feb 2019
   */
  public static function updateDeliveredDateIntoSuborder($db, string $suborder_id, string $delivered_date) {
    if(!empty($suborder_id) && !empty($delivered_date)){

      $sql = "UPDATE
                  " . DB_PREFIX . "suborder
              SET
                delivered_date = '".$db->escape($delivered_date)."'
              WHERE 
                  suborder_id = '" . $db->escape($suborder_id) . "'
              ";

      $db->query($sql);

      return true;
    }else{
      return false;
    }
  } 

  /**
   *
   * checking order is Delivered first time
   * @author: Nishu, Feb 2019
   */
  public static function getFirstDeliveredDateForSuborder($db, int $order_id, string $suborder_id) {
    $delivered_date = '';
    if(!empty($suborder_id) && !empty($order_id)){

      $sql = "
              SELECT 
                MIN(date_added) as delivered_date
              FROM 
                " . DB_PREFIX . "order_history
              WHERE 
                order_id            = '" . (int)$order_id . "'
                AND suborder_id     = '" . $db->escape($suborder_id) . "'
                AND order_status_id = '" . (int)ORDER_STATUS['Delivered'] . "'
              ";

      $result = $db->query($sql);
      if($result->num_rows > 0){
        $delivered_date = $result->row['delivered_date'];
      }
    }
    return $delivered_date;
  } 

  /**
   * Public method to get Suborder balance by given order_id and suborder_id
   * This method also accounts for the Net collectable COD amount; assuming that it is 
   * tentatively paid by customer (irrespective of COD remitted already or not).
   * 
   * @param $registry - Registry object
   * @param $order_id - Int
   * @param $suborder_id - String
   * 
   * @return float (balance amount)
   * @author: Nishu, March 2019
  */
  public static function getSubOrderBalanceAmount(Registry $registry, int $order_id, string $suborder_id) : float {
	  
	// Throw Exception if Database\DB instance is not available
	if ( empty($registry->get('db')) || !($registry->get('db') instanceof Database\DB) ) {
	  throw new \Exception('Valid Database\DB instance does not exist in the $registry!');
	}
	$db = $registry->get('db');

    $bal = 0;

    if(!empty($order_id) && !empty($suborder_id)){

      //Get Suborder invoice amount
      $selector = array('suborder_id', 'total' );
      $suborder_info = self::getSuborderInfo($db, $suborder_id,$selector); 
      $suborder_total = $suborder_info['total'] ?? 0;

	  //Get amounts from Credit Note (refund (credit_note_amount), receivable (cod_failed_penalty, less_cash_discount)
      $cn_data   = CreditNote::getCnDetailsToCalculateOrderBalance($db, $order_id, $suborder_id);
      $cn_amount = $cn_data[$order_id]['cn_amount'] ?? 0;
      $cod_failed_penalty = $cn_data[$order_id]['cod_failed_penalty'] ?? 0;
      $less_cash_discount = $cn_data[$order_id]['less_cash_discount'] ?? 0;
      $other_charges      = $cn_data[$order_id]['other_charges'] ?? 0;

	  // Get received amounts from Customer (paid_by_customer, cashback_coupon_amount)
      $payment_details = Customer::getPaymentDetailsBySubOrderId($db, $suborder_id);
      $paid_by_customer       = $payment_details['paid_by_customer'] ?? 0;
      $cashback_coupon_amount = $payment_details['cashback_coupon_amount'] ?? 0;
      
      // Get collectable_cod_amount (tentatively assumed to be paid by customer at the time of delivery)
      $collectable_cod_amount = 0;

      // Firstly, get the payment_code of the order
      $selector = array('order' => array('select' => 'payment_code')); 
      $order_info = OrderInfo::getOrderInfo($db, $order_id, '', $selector);
      $payment_code = strtolower(trim($order_info['order']['payment_code'] ?? ''));
      if( $payment_code === 'cod' || in_array($payment_code, CREDIT_PARTIAL_COD_CODES) ) {

          // Also get suborder status
          $status = (int)(self::getSuborderInfo($db, $suborder_id, array('order_status_id'))['order_status_id'] ?? 0);

          if ( $status > 0 && !in_array($status, ORDER_STATUS_CLUSTERS['failed']) ) {

              $buyer_invoice_obj = new BuyerInvoice($registry);
              $order_totals = $buyer_invoice_obj->getTotals($order_id, $suborder_id);
              $collectable_cod_amount = max($order_totals['net_amount']['value'] ?? 0,0);
          }

      }  
      
      // Get balance amount for this suborder
      $bal =   -(float)$suborder_total
               +(float)$cn_amount
               -(float)$cod_failed_penalty
               +(float)$cashback_coupon_amount
               +(float)$paid_by_customer
               -(float)$less_cash_discount
               +(float)$other_charges
               +(float)$collectable_cod_amount;

      $bal = round($bal, 2);
    }

    return $bal;
  }

  /**
   * @info : Public static function to update suborder fields in oc_suborder table
   * @param: $db, $field, $value, $suborder_id
   * @return: Nishu, May 2019
  */
  public static function updateField($db, $field, $value, $suborder_id ){
    if(!empty($field) && !empty($value) && !empty($suborder_id)){
      $sql = "
              UPDATE
                ".DB_PREFIX."suborder
              SET
                ".$field."= '". $db->escape($value) ."'
              WHERE
                suborder_id = '". $db->escape($suborder_id) ."'
             ";

      //Update Query Execution
      $db->query($sql);

    }
  }

 
}
// close Suborder class
?>
