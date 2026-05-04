<?php
declare(strict_types=1);

/**
 * Main Class for getting Customer's all Order Related Info.
 * It is recommended to utilize the API function common between Web APIs and Mobile APIs
 * @Author Nishu, 2018
 */
class ValidateCustomerOrder {

  /**
   * Public Method to validate customer with Order Info passed by API request params
   * @author: Nishu, Aug 2018
  */
  public function validateCustomerWithOrder(Database\DB $db, array $data) : bool{

    $valid = false;

    if(!empty($data['customer_id'])){
      $join = '';
      $whr = "
             WHERE 
                o.customer_id = ". (int)$data['customer_id'] ."
             ";
      if(!empty($data['order_id'])){
        $whr .= " AND o.order_id =". (int)$data['order_id'];
      }

      if(!empty($data['order_no'])){
        $whr .= " AND o.order_no ='". $db->escape($data['order_no']). "'";
      }

      if(!empty($data['suborder_id'])){
        $join .= " INNER JOIN " . DB_PREFIX . "suborder AS so ON o.order_id = so.order_id ";
        $whr  .= " AND so.suborder_id ='". $db->escape($data['suborder_id']). "' ";
      }

      if(!empty($data['order_product_id'])){
        $join .= " INNER JOIN " . DB_PREFIX . "order_product AS op ON o.order_id = op.order_id ";
        $whr  .= " AND op.order_product_id =". (int)$data['order_product_id']. " ";
      }

      //Generating SQL Query for validating customer and his data
      $sql = "
              SELECT 
                o.order_id
              FROM
                " . DB_PREFIX . "order AS o
            ". $join ."
            ". $whr ."
             ";

      $result = $db->query($sql);
      if($result->num_rows > 0){
        $valid = true;
      }
    }
    return $valid;
  }

  

}
// close OrderInfo class
?>
