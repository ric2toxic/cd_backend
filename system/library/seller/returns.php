 <?php
 class Returns {  

    /** 
   * Method to seller change log
   * @param: $db: Object of Database   
   * @param: $seller_id : Integer of seller Id 
   * @param: $filter_data: array of data    
   * @author Mahaveer, 2018
   */
  public function getReturnOrders($db, $seller_id, $filter_data) {
    
  $sql = "SELECT
              si.seller_id,
              oo.order_no,
              oo.order_id,
              oop.suborder_id,
              GROUP_CONCAT(DISTINCT oop.order_product_id) as order_product_ids,
              r.master_return_id,
              sum(r.quantity) as quantity,
              GROUP_CONCAT(DISTINCT rr.name) as reason_names,
              si.seller_invoice_id,
              CONCAT(si.seller_invoice_prefix,si.seller_invoice_no) as seller_invoice_number,
              DATE(si.date_added) as seller_invoice_date, 
              sdn.debit_note_id, 
              CONCAT(sdn.debit_note_prefix, sdn.debit_note_no) as  debit_note_no,
              sdn.debit_note_amount as debit_note_amount,
              SUM(r.quantity*oop.transfer_price_per_piece) as return_amount,
              DATE(sdn.date_added) as debit_note_date 
              
          FROM
           oc_return r
          INNER JOIN oc_return_reason rr
          on rr.return_reason_id = r.return_reason_id 
             AND rr.language_id = 1 
             AND rr.reason_type = 'RETURN'   
          LEFT JOIN oc_seller_debit_note sdn
          on sdn.debit_note_id = r.debit_note_id AND sdn.debit_note_status = 1 
          INNER JOIN oc_order_product oop
          on oop.order_product_id = r.order_product_id
          INNER JOIN oc_seller_invoice si
          on si.seller_invoice_id = oop.seller_invoice_id  
          INNER JOIN oc_order oo
         on oo.order_id = oop.order_id
         WHERE
          si.seller_id = ". (int)$seller_id."
          AND (sdn.custom_id = 0 OR sdn.custom_id IS NULL) 
          AND r.active_row = 1
          ";

          if ( !empty( $filter_data['master_return_id'] ) ) {
            $sql .= " AND r.master_return_id = '" . (int)$filter_data['master_return_id']. "'";
           }

           if ( !empty( $filter_data['seller_invoice_id'] ) ) {
            $sql .= " AND si.seller_invoice_id = '" . (int)$filter_data['seller_invoice_id']. "'";
           }           

          if ( !empty( $filter_data['return_action_id'] ) ) {
            $sql .= " AND r.return_action_id IN (" . $db->escape($filter_data['return_action_id']) . ")";
           }

          if ( !empty( $filter_data['filter_order_no'] ) ) {
            $sql .= " AND oo.order_no LIKE '%" .$db->escape($filter_data['filter_order_no']). "%'";
           }

           if ( !empty( $filter_data['debit_note_no'] ) ) {
              $sql .= " AND CONCAT(sdn.debit_note_prefix,sdn.debit_note_no) LIKE '%" . $db->escape($filter_data['debit_note_no']) . "%' ";
            }

          if ( !empty( $filter_data['debit_note_date_from'] )) 
          {
           $sql .= " AND DATE(sdn.date_added) >= DATE('".$filter_data['debit_note_date_from'] . "') ";
          }

          if ( !empty( $filter_data['debit_note_date_to'] )) 
          {
           $sql .= " AND DATE(sdn.date_added) <= DATE('".$filter_data['debit_note_date_to'] . "') ";
          }

     $sql .= " GROUP BY r.master_return_id, si.seller_invoice_id, r.debit_note_id ";
  
     if ( !empty($filter_data['sort']) ) {
          $sql .= " ORDER BY " . $filter_data['sort'];
     } else {
          $sql .= " ORDER BY oo.order_id ";
     }

     if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
          $sql .= " ASC";
     } else {
          $sql .= " DESC";
     }
  
     if (isset($filter_data['start']) || isset($filter_data['limit']) ) {
          if ($filter_data['start'] < 0) {
              $filter_data['start'] = 0;
          }

          if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
          }
      $limit_sql = " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
     }


     $query = $db->query($sql);
     $total_rows = $query->num_rows;  

     $query = $db->query($sql.$limit_sql);
     return array($query->rows, $total_rows);        
  }


  public function getReplacementOrders($db, $seller_id, $filter_data) {
    
  $sql = "SELECT
              si.seller_id,
              oo.order_no,
              oo.order_id,
              oop.suborder_id,
              GROUP_CONCAT(DISTINCT oop.order_product_id) as order_product_ids,
              r.master_return_id,
              sum(r.quantity) as quantity,
              GROUP_CONCAT(DISTINCT rr.name) as reason_names,
              si.seller_invoice_id,
              CONCAT(si.seller_invoice_prefix,si.seller_invoice_no) as seller_invoice_number,
              DATE(si.date_added) as seller_invoice_date 
          FROM
           oc_return r
          INNER JOIN oc_return_reason rr
          on rr.return_reason_id = r.return_reason_id 
             AND rr.language_id = 1 
             AND rr.reason_type = 'REPLACEMENT'   
          INNER JOIN oc_order_product oop
          on oop.order_product_id = r.order_product_id
          INNER JOIN oc_seller_invoice si
          on si.seller_invoice_id = oop.seller_invoice_id  
          INNER JOIN oc_order oo
         on oo.order_id = oop.order_id
         WHERE
          si.seller_id = ". (int)$seller_id."
          AND r.active_row = 1 ";


          if ( !empty( $filter_data['master_return_id'] ) ) {
            $sql .= " AND r.master_return_id = '" . (int)$filter_data['master_return_id']. "'";
           }

           if ( !empty( $filter_data['seller_invoice_id'] ) ) {
            $sql .= " AND si.seller_invoice_id = '" . (int)$filter_data['seller_invoice_id']. "'";
           } 

          if ( !empty( $filter_data['replacement_action_id'] ) ) {
            $sql .= " AND r.return_action_id IN (" . $db->escape($filter_data['replacement_action_id']) . ")";
           }

          if ( !empty( $filter_data['filter_order_no'] ) ) {
            $sql .= " AND oo.order_no LIKE '%" .$db->escape($filter_data['filter_order_no']). "%'";
           }

     $sql .= " GROUP BY r.master_return_id, si.seller_invoice_id ";
  
     if ( !empty($filter_data['sort']) ) {
          $sql .= " ORDER BY " . $filter_data['sort'];
     } else {
          $sql .= " ORDER BY oo.order_id ";
     }

     if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
          $sql .= " ASC";
     } else {
          $sql .= " DESC";
     }
  
     if (isset($filter_data['start']) || isset($filter_data['limit']) ) {
          if ($filter_data['start'] < 0) {
              $filter_data['start'] = 0;
          }

          if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
          }
      $limit_sql = " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
     }

     $query = $db->query($sql);
     $total_rows = $query->num_rows;  

     $query = $db->query($sql.$limit_sql);
     return array($query->rows, $total_rows);        
  }


  /** 
   * Method to get delivered returns to seller
   * @param: $db: Object of Database   
   * @param: $seller_id : Integer of seller Id 
   * @param: $filter_data: array of data    
   * @author Mahaveer, 2018
   */
  public function getDeliveredReturns($db, $seller_id, $filter_data) {
    
  $sql = "SELECT
              r1.master_return_id,
              si.seller_id,
              oo.order_no,
              oo.order_id,
              oop.suborder_id,
              GROUP_CONCAT(DISTINCT oop.order_product_id) as order_product_ids, 
              sum(r1.quantity) as quantity,
              GROUP_CONCAT(DISTINCT rr.name) as reason_names,
              si.seller_invoice_id,
              CONCAT(si.seller_invoice_prefix,si.seller_invoice_no) as seller_invoice_number,
              DATE(si.date_added) as seller_invoice_date, 
              sdn.debit_note_id, 
              CONCAT(sdn.debit_note_prefix, sdn.debit_note_no) as  debit_note_no,
              sdn.debit_note_amount as debit_note_amount,
              SUM(r1.quantity*oop.transfer_price_per_piece) as return_amount,
              DATE(sdn.date_added) as debit_note_date 
              
          FROM 
           oc_seller_debit_note sdn 
          INNER JOIN oc_return r1 ON sdn.debit_note_id = r1.debit_note_id
          INNER JOIN oc_return AS r2 ON r1.order_product_id = r2.order_product_id
          AND r1.master_return_id = r2.master_return_id
           AND r2.active_row = 1 
          INNER JOIN oc_return_reason rr ON rr.return_reason_id = r1.return_reason_id
           AND rr.language_id = 1
          INNER JOIN  oc_order_product oop ON oop.order_product_id = r1.order_product_id 
          INNER JOIN oc_seller_invoice si ON si.seller_invoice_id = oop.seller_invoice_id
          INNER JOIN oc_order oo ON oo.order_id = oop.order_id
          WHERE
          si.seller_id = ". (int)$seller_id."
          AND (sdn.custom_id = 0 OR sdn.custom_id IS NULL) 
          AND sdn.debit_note_status = 1 
          ";


          if ( !empty( $filter_data['master_return_id'] ) ) {
            $sql .= " AND r1.master_return_id = '" . (int)$filter_data['master_return_id']. "'";
           }

           if ( !empty( $filter_data['seller_invoice_id'] ) ) {
            $sql .= " AND si.seller_invoice_id = '" . (int)$filter_data['seller_invoice_id']. "'";
           }    

           if ( !empty( $filter_data['debit_note_id'] ) ) {
            $sql .= " AND sdn.debit_note_id = '" . (int)$filter_data['debit_note_id']. "'";
            }

           if ( !empty( $filter_data['return_action_id'] ) ) {
             $sql .= " AND r2.return_action_id NOT IN (" . $db->escape($filter_data['return_action_id']) . ")";
            }           

          if ( !empty( $filter_data['filter_order_no'] ) ) {
            $sql .= " AND oo.order_no LIKE '%" .$db->escape($filter_data['filter_order_no']). "%'";
           }

           if ( !empty( $filter_data['debit_note_no'] ) ) {
              $sql .= " AND CONCAT(sdn.debit_note_prefix,sdn.debit_note_no) LIKE '%" . $db->escape($filter_data['debit_note_no']) . "%' ";
            }

          if ( !empty( $filter_data['debit_note_date_from'] )) 
          {
           $sql .= " AND DATE(sdn.date_added) >= DATE('".$filter_data['debit_note_date_from'] . "') ";
          }

          if ( !empty( $filter_data['debit_note_date_to'] )) 
          {
           $sql .= " AND DATE(sdn.date_added) <= DATE('".$filter_data['debit_note_date_to'] . "') ";
          }
     
     $sql .= " GROUP BY sdn.debit_note_id DESC ";
  
     if ( !empty($filter_data['sort']) ) {
          $sql .= " ORDER BY " . $filter_data['sort'];
     } else {
          $sql .= " ORDER BY oo.order_id ";
     }

     if ( !empty($filter_data['order']) && ($filter_data['order'] == 'ASC') ) {
          $sql .= " ASC";
     } else {
          $sql .= " DESC";
     }
  
     if (isset($filter_data['start']) || isset($filter_data['limit']) ) {
          if ($filter_data['start'] < 0) {
              $filter_data['start'] = 0;
          }

          if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
          }
      $limit_sql = " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
     }


     $query = $db->query($sql);
     $total_rows = $query->num_rows;  

     $query = $db->query($sql.$limit_sql);
     return array($query->rows, $total_rows);        
  }


  public function getReturnProductDetails($obj, $seller_id, $filter_data) {
  
  $sql = "SELECT
              r.master_return_id,
              oop.seller_id, 
              oop.order_product_id,
              oop.order_product_id as id,
              oop.product_id,
              oop.name,
              oop.seller_sku,
              r.quantity,
              oop.transfer_price_per_piece,
              r.quantity*oop.transfer_price_per_piece as product_amount,
              oop.seller_input_tax,
              oop.seller_cst,
              oop.comment
          FROM
           oc_return r
          INNER JOIN oc_order_product oop
          on oop.order_product_id = r.order_product_id 
         WHERE
           r.active_row = 1";

          if ( !empty( $filter_data['master_return_id'] ) ) {
            $sql .= " AND r.master_return_id = '" .$obj->db->escape($filter_data['master_return_id']). "'";
           }

            if ( !empty( $filter_data['seller_invoice_id'] ) ) {
            $sql .= " AND si.seller_invoice_id = '" . (int)$filter_data['seller_invoice_id']. "'";
            } 

          if ( !empty( $filter_data['order_product_ids'] ) ) {
            $sql .= " AND r.order_product_id IN (" .$obj->db->escape($filter_data['order_product_ids']). ")";
           }

     //$sql .= " GROUP BY r.master_return_id, r.order_product_id";
      $sql .= " ORDER BY oop.order_product_id DESC";
  
     if (isset($filter_data['start']) || isset($filter_data['limit']) ) {
          if ($filter_data['start'] < 0) {
              $filter_data['start'] = 0;
          }

          if ($filter_data['limit'] < 1) {
              $filter_data['limit'] = 20;
          }
      $limit_sql = " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
     }
     $query = $obj->db->query($sql);
     $total_rows = $query->num_rows;  

     $query = $obj->db->query($sql.$limit_sql);

     $product_record_array = array();

     array_walk($query->rows, function(&$products , $key) use (&$product_record_array){
          
          $product_record_array[$products['order_product_id']] = $products;
        });

     return array($product_record_array, $total_rows);
            
  }

}
  ?>