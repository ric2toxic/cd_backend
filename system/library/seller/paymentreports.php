<?php
  /**
 * Main Class for getting all GST, SGST, IGST Related Info.
 * Majority of functions here will be static.
 * @Author Murtaza, 2017
 */
class PaymentReports {
 
   /**
 * @Author Kalyan Sahai Sharma, 2017
 */
 
  public static function getPaymentDetail($controller, $data = array(), $pagination='no'){
  
    $sql   = '';
    $sql_1 = '';
    $sql_2 = '';
    $sql_3 = '';
  
    if (!empty($data['filter_debit_note_date']) || !empty($data['filter_reference_no'])) {
      /*
      * union for sor payment table
      */
      $sql_1 .= "SELECT 
              sp.order_id,
              '' as order_no,
              '' as order_date,
              seller.seller_id,
              seller.company,
              seller.nickname,
              sum(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS total_product_value,
              sum((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax)/100) as total_tax,
              '' as seller_invoice_ids,
              GROUP_CONCAT(sp.sor_payment_id) as sor_payment_ids
              FROM ". DB_PREFIX ."wsb_sor_payment as sp 
              INNER JOIN ". DB_PREFIX ."order_product AS oop 
                ON oop.suborder_id = sp.suborder_id 
              INNER JOIN ". DB_PREFIX ."order as oo 
                ON oo.order_id = sp.order_id 
              INNER JOIN ". DB_PREFIX ."ms_seller as seller 
                ON seller.seller_id = sp.seller_id
              WHERE oo.franchise_id = 0 
                AND sor_product=1 
                AND sp.sor_payment_id=oop.sor_payment_id 
                AND sp.wsb_purchase_id=oop.wsb_purchase_id 
                AND oop.wsb_purchase_id>0 
                AND sp.trxn_done IN('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE') ";
  
  
      /*
      * this condition returns without franchise id orders
      */
      $sql_1 .= " AND oo.franchise_id = 0 ";
  
      if(!empty($data['filter_seller_id'])){
        $sql_1 .=  " AND seller.seller_id='".(int)$data['filter_seller_id']."'";
      } 
      if(!empty($data['filter_order_no'])){
        $sql_1 .=  " AND oo.order_no LIKE '%".$data['filter_order_no']."%'";
      }
      if (!empty($data['filter_seller_code'])) {
        $sql_1 .= " AND seller.nickname LIKE '%".$data['filter_seller_code']."%'";
      }
      if (!empty($data['filter_company_name'])) {
        $sql_1 .= " AND seller.company LIKE '%".$data['filter_company_name']."%'";
      }
      if (!empty($data['filter_payment_done_date'])) {
        $sql_1 .= " AND sp.trxn_utr_date='".date('Y-m-d',strtotime($data['filter_payment_done_date']))."'";
      }
      if (!empty($data['filter_reference_no'])) {
        $sql_1 .= " AND sp.trxn_utr LIKE '%".$data['filter_reference_no']."%'";
      }  
      $sql_1 .= "  GROUP BY oo.order_id,seller.seller_id ";
      /*
      * union for seller_invoice table
      */
      $sql_2 .= "SELECT 
              oo.order_id,
              oo.order_no,
              oo.date_added as order_date,
              seller.seller_id,
              seller.company,
              seller.nickname,
              sum(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS total_product_value,
              sum((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax)/100) as total_tax,
              GROUP_CONCAT(oop.seller_invoice_id) as seller_invoice_ids,
              '' as sor_payment_ids
              FROM ". DB_PREFIX ."order as oo 
              INNER JOIN ". DB_PREFIX ."order_product as oop ON oop.order_id = oo.order_id 
              INNER JOIN ". DB_PREFIX ."seller_invoice as si 
              ON oop.order_id = si.order_id AND oop.seller_id=si.seller_id AND oop.suborder_id=si.suborder_id 
              INNER JOIN ". DB_PREFIX ."ms_seller as seller ON seller.seller_id = oop.seller_id ";
  
      $sql_2 .=" WHERE oop.seller_invoice_id > 0 ";
      /*
      * this condition returns without franchise id orders
      */
      $sql .= " AND oo.franchise_id = 0 ";
  
      if(!empty($data['filter_seller_id'])){
        $sql_2 .=  " AND seller.seller_id='".(int)$data['filter_seller_id']."'";
      } 
      if(!empty($data['filter_order_no'])){
        $sql_2 .=  " AND oo.order_no LIKE '%".$data['filter_order_no']."%'";
      }
      if (!empty($data['filter_seller_code'])) {
        $sql_2 .= " AND seller.nickname LIKE '%".$data['filter_seller_code']."%'";
      }
      if (!empty($data['filter_company_name'])) {
        $sql_2 .= " AND seller.company LIKE '%".$data['filter_company_name']."%'";
      }
      if (!empty($data['filter_payment_done_date'])) {
        $sql_2 .= " AND si.trxn_utr_date='".date('Y-m-d',strtotime($data['filter_payment_done_date']))."'";
      }
      if (!empty($data['filter_reference_no'])) {
        $sql_2 .= " AND si.trxn_utr LIKE '%".$data['filter_reference_no']."%'";
      }  
      $sql_2 .= "  GROUP BY oo.order_id,seller.seller_id ";
  
      /*
      * union for seller_debit_note table
      */
      $sql_3 .= "SELECT 
              oo.order_id,
              oo.order_no,
              oo.date_added as order_date,
              seller.seller_id,
              seller.company,
              seller.nickname,
              sum(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS total_product_value,
              sum((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax)/100) as total_tax,
              GROUP_CONCAT(oop.seller_invoice_id) as seller_invoice_ids,
              '' as sor_payment_ids
              FROM ". DB_PREFIX ."order as oo 
              INNER JOIN ". DB_PREFIX ."order_product as oop ON oop.order_id = oo.order_id 
              INNER JOIN ". DB_PREFIX ."seller_debit_note as sdn 
              ON oop.order_id = sdn.order_id AND oop.seller_id=sdn.seller_id AND oop.suborder_id=sdn.suborder_id  
              INNER JOIN ". DB_PREFIX ."ms_seller as seller ON seller.seller_id = oop.seller_id ";
  
      $sql_3 .=" WHERE oop.seller_invoice_id > 0";
      /*
      * this condition returns without franchise id orders
      */
      $sql .= " AND oo.franchise_id = 0 
                AND (sdn.custom_id = 0 OR sdn.custom_id = NULL)
              ";
  
      if(!empty($data['filter_seller_id'])){
        $sql_3 .=  " AND sdn.seller_id='".(int)$data['filter_seller_id']."'";
      }
      if(!empty($data['filter_order_no'])){
        $sql_3 .=  " AND oo.order_no LIKE '%".$data['filter_order_no']."%'";
      }
      if (!empty($data['filter_seller_code'])) {
        $sql_3 .= " AND seller.nickname LIKE '%".$data['filter_seller_code']."%'";
      }
      if (!empty($data['filter_company_name'])) {
        $sql_3 .= " AND seller.company LIKE '%".$data['filter_company_name']."%'";
      }
      if (!empty($data['filter_payment_done_date'])) {
        $sql_3 .= " AND sdn.trxn_utr_date='".date('Y-m-d',strtotime($data['filter_payment_done_date']))."'";
      }
      if (!empty($data['filter_reference_no'])) {
        $sql_3 .= " AND sdn.trxn_utr LIKE '%".$data['filter_reference_no']."%'";
      }  
      $sql_3 .= "  GROUP BY oo.order_id,seller.seller_id ";
  
      $sql = " ( ".$sql_1." ) UNION ( ".$sql_2." ) UNION ( ".$sql_3." ) ";
  
    }else{
  
      $sql_1 .= "SELECT 
              oo.order_id,
              '' as sor_payment,
              oo.order_no,
              oo.date_added as order_date,
              seller.seller_id,
              seller.company,
              seller.nickname,
              sum(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS total_product_value,
              sum((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax)/100) as total_tax,
              GROUP_CONCAT(oop.seller_invoice_id) as seller_invoice_ids,
              '' as sor_payment_ids
              FROM ". DB_PREFIX ."order as oo 
              INNER JOIN ". DB_PREFIX ."order_product as oop ON oop.order_id = oo.order_id 
              INNER JOIN ". DB_PREFIX ."ms_seller as seller ON seller.seller_id = oop.seller_id ";
  
      $sql_1 .=" WHERE oop.seller_invoice_id > 0";
      /*
      * this condition returns without franchise id orders
      */
      $sql_1 .= " AND oo.franchise_id = 0 ";
  
      if(!empty($data['filter_seller_id'])){
        $sql_1 .=  " AND seller.seller_id='".(int)$data['filter_seller_id']."'";
      }
      if(!empty($data['filter_order_no'])){
        $sql_1 .=  " AND oo.order_no LIKE '%".$data['filter_order_no']."%'";
      }
      if (!empty($data['filter_seller_code'])) {
        $sql_1 .= " AND seller.nickname LIKE '%".$data['filter_seller_code']."%'";
      }
      if (!empty($data['filter_company_name'])) {
        $sql_1 .= " AND seller.company LIKE '%".$data['filter_company_name']."%'";
      }      
      $sql_1 .= " GROUP BY oo.order_id,seller.seller_id "; 
  
      $sql_2 .= "SELECT 
              sp.order_id,
              sp.order_id as sor_payment,
              '' as order_no,
              '' as order_date,
              seller.seller_id,
              seller.company,
              seller.nickname,
              sum(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS total_product_value,
              sum((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax)/100) as total_tax,
              '' as seller_invoice_ids,
              GROUP_CONCAT(sp.sor_payment_id) as sor_payment_ids
              FROM ". DB_PREFIX ."wsb_sor_payment as sp 
              INNER JOIN ". DB_PREFIX ."order_product AS oop 
                ON oop.suborder_id = sp.suborder_id 
              INNER JOIN ". DB_PREFIX ."order as oo 
                ON oo.order_id = sp.order_id 
              INNER JOIN ". DB_PREFIX ."ms_seller as seller 
                ON seller.seller_id = sp.seller_id ";
  
     /*
      * this condition returns without franchise id orders
      */
      $sql_2 .= " 
      WHERE oo.franchise_id = 0 
              AND sor_product=1 
              AND sp.sor_payment_id=oop.sor_payment_id 
              AND sp.wsb_purchase_id=oop.wsb_purchase_id 
              AND oop.wsb_purchase_id>0 
              AND sp.trxn_done IN('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE') ";
  
      if(!empty($data['filter_seller_id'])){
        $sql_2 .=  " AND seller.seller_id='".(int)$data['filter_seller_id']."'";
      }
      if(!empty($data['filter_order_no'])){
        $sql_2 .=  " AND oo.order_no LIKE '%".$data['filter_order_no']."%'";
      }
      if (!empty($data['filter_seller_code'])) {
        $sql_2 .= " AND seller.nickname LIKE '%".$data['filter_seller_code']."%'";
      }
      if (!empty($data['filter_company_name'])) {
        $sql_2 .= " AND seller.company LIKE '%".$data['filter_company_name']."%'";
      }
      $sql_2 .= " GROUP BY oop.suborder_id, oop.wsb_purchase_id ";       
  
      $sql = $sql_1." UNION ".$sql_2;
  
    }
  
    $sql .= " ORDER BY order_id DESC ";
  
    if($pagination == 'no'){
      if (isset($data['start']) || isset($data['limit'])) {
        if ($data['start'] < 0) {
            $data['start'] = 0;
        }
        if ($data['limit'] < 1) {
            $data['limit'] = 20;
        }
        $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
      }
    }
  
  
    $result = $controller->db->query($sql);
  
    if($pagination == 'total'){
      return $result->num_rows;
    }else{
      return $result->rows;
    }
  }

  /**
 * @Author Kalyan Sahai Sharma, 2017
 */
  public static function getInvoicesDetail($controller, $invoice_ids){
    if(empty($invoice_ids)){
      return '';
    }
    $select = "
            si.seller_invoice_id,
            sum(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS invoice_wise_product_value,
            sum((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax)/100) as invoice_wise_tax,
            si.date_added as invoice_date,
            si.trxn_done as invoice_trxn_done,
            si.trxn_amount as invoice_trxn_amount,
            concat(si.seller_invoice_prefix,si.seller_invoice_no) AS invoice_no,
            si.trxn_utr AS invoice_bank_ref,
            si.trxn_utr_date AS invoice_payment_date
            ";
    $sql = "SELECT ". $select." 
              FROM ". DB_PREFIX ."seller_invoice as si 
            INNER JOIN ". DB_PREFIX ."order_product AS oop ON oop.seller_invoice_id = si.seller_invoice_id 
            WHERE si.seller_invoice_id IN (".$invoice_ids.")
            ";
    $sql .= " GROUP BY si.order_id,si.seller_invoice_id ";
    $sql .= " ORDER BY si.order_id ";
    
    $invoices_result = $controller->db->query($sql);
    return $invoices_result->rows;
  }

  /**
 * @Author Kalyan Sahai Sharma, 2017
 */
  public static function getReturnDetail($controller, $type, $invoice_no, $order_ids) {
    // Customer Returns
    if($type == 'customer') {
      $sql_customer_return = "SELECT 
          sdn.debit_note_no as dn_no,
          date(sdn.date_added) as dn_date,
          sum(ort.quantity * oop.transfer_price_per_piece) AS dn_value,
          sdn.debit_note_status
        FROM ". DB_PREFIX ."seller_debit_note as sdn
        INNER JOIN ". DB_PREFIX ."return as ort
          ON sdn.debit_note_id = ort.debit_note_id 
        INNER JOIN ". DB_PREFIX ."order_product as oop
          ON ort.order_product_id = oop.order_product_id 
        INNER JOIN ". DB_PREFIX ."seller_invoice as si
          ON si.seller_invoice_id = oop.seller_invoice_id 
        INNER JOIN ". DB_PREFIX ."order as oo
          ON oop.order_id = oo.order_id  
        INNER JOIN ". DB_PREFIX ."ms_seller as seller
          ON seller.seller_id = oop.seller_id 
        WHERE sdn.debit_note_no > 0
          AND oo.franchise_id = 0 
          AND sdn.order_id IN (".$order_ids.")
          AND si.seller_invoice_no = '".$controller->db->escape($invoice_no)."'";
      $sql_customer_return .= " GROUP BY oop.seller_invoice_id";
    }
    
    // SOR Returns
    if($type == 'sor') {
      $sql_sor_return = "SELECT 
          sdn.debit_note_no as dn_no,
          date(sdn.date_added) as dn_date,
          sum(ort.quantity * oop.transfer_price_per_piece) AS dn_value,
          sdn.debit_note_status
        FROM ". DB_PREFIX ."seller_debit_note as sdn
        INNER JOIN ". DB_PREFIX ."return as ort
          ON sdn.debit_note_id = ort.debit_note_id 
        INNER JOIN ". DB_PREFIX ."order_product as oop
          ON ort.order_product_id = oop.order_product_id 
        INNER JOIN ". DB_PREFIX ."wsb_purchase as wp
          ON wp.purchase_id = oop.wsb_purchase_id 
        INNER JOIN ". DB_PREFIX ."order as oo
          ON oop.order_id = oo.order_id  
        INNER JOIN ". DB_PREFIX ."ms_seller as seller
          ON seller.seller_id = oop.seller_id 
        WHERE sdn.debit_note_no > 0
          AND oop.sor_product = 1 
          AND oop.wsb_purchase_id > 0
          AND oo.franchise_id = 0 
          AND sdn.order_id IN (".$order_ids.")
          AND wp.invoice_no = '".$controller->db->escape($invoice_no)."'";
      $sql_sor_return .= " GROUP BY wp.invoice_no";
    }
    
    // Purchase Inventory Returns
    if($type == 'purchase') {
      $sql_purchase_return = "SELECT 
          sdn.debit_note_no as dn_no,
          date(sdn.date_added) as dn_date,
          sum(ort.quantity * oop.transfer_price_per_piece) AS dn_value,
          wpr.debit_note_status
        FROM ". DB_PREFIX ."wsb_purchase_return as wpr
        INNER JOIN ". DB_PREFIX ."wsb_purchase as wp
          ON wp.purchase_id = wpr.purchase_id
        INNER JOIN ". DB_PREFIX ."ms_seller as seller
          ON seller.seller_id = wp.seller_id 
        WHERE wpr.debit_note_no > 0
          AND wp.purchase_id > 0
          AND oo.franchise_id = 0 
          AND wp.invoice_no = '".$controller->db->escape($invoice_no)."'";
      $sql_purchase_return .= " GROUP BY wp.invoice_no";
    }
    
    $returns_result = $controller->db->query($sql_customer_return);
    if($returns_result->num_rows > 0){
      return $returns_result->rows;
    }else{
      return array();
    }
  }  
  /**
  * Method for get sor order payment detail to seller
  * @param : order_id
  * @param : seller_id
  * @return : array of payment detail
  * @author : kalyan 28th Nov 2017
  */
  public function getSorOrderPaymentDetail($sor_payment_ids){

    $sql = "SELECT 
              sp.sor_payment_id,
              sp.order_id,
              sp.seller_id,
              sp.suborder_id,
              sp.trxn_date_added,
              sp.trxn_amount,
              sp.trxn_done,
              sp.trxn_utr,
              sp.trxn_utr_date,
              wp.invoice_no,
              wp.invoice_date,
              sum(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS invoice_wise_product_value,
              sum((oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax)/100) as invoice_wise_tax
            FROM ". DB_PREFIX ."wsb_sor_payment sp             
            INNER JOIN ". DB_PREFIX ."order_product AS oop 
              ON oop.suborder_id = sp.suborder_id 
            INNER JOIN ". DB_PREFIX ."wsb_purchase AS wp 
              ON sp.wsb_purchase_id = wp.purchase_id 
            WHERE 
              sp.sor_payment_id IN (".$sor_payment_ids.") 
              AND sor_product=1 
              AND sp.sor_payment_id=oop.sor_payment_id 
              AND sp.wsb_purchase_id=oop.wsb_purchase_id 
              AND oop.wsb_purchase_id>0 
              AND sp.trxn_done IN('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE') 
            GROUP BY oop.suborder_id, oop.wsb_purchase_id 
            ORDER BY sp.order_id ";
    
    $query = $this->db->query($sql);
    
    return $query->rows;
  }
  
  /***
    @author: Anurag Jain (15 June 2018)
    @description:
      Function returns all invoice ids or total count (based on $pagination parameter)
      Invoice ids includes seller invoices and wsb purchases (sor excluded)
    @params: 
      $controller: instance
      $data: filter and limits data
      $pagination: 'no' -> page data; 'total' => total count
    @return: all invoice ids or total invoice count (based on $pagination parameter)
  ***/
  public static function getInvoiceIds($controller, $data = array(), $pagination='no') {
    
    // filters sql
    $sql_filter_seller_invoice = '';
    $sql_filter_seller_debit_note = '';
    $sql_filter_wsb_purchase = '';
    $sql_filter_wsb_purchase_return = '';
    
    if(!empty($data['filter_seller_id'])) {
      $filter_seller_id = $data['filter_seller_id'];
      
      $sql_filter_seller_invoice .= " AND osi.seller_id = $filter_seller_id";
      $sql_filter_seller_debit_note .= " AND osdn.seller_id = $filter_seller_id";
      $sql_filter_wsb_purchase .= " AND owp.seller_id = $filter_seller_id";
      $sql_filter_wsb_purchase_return .= " AND owp.seller_id = $filter_seller_id";
    }
    
    if(!empty($data['filter_seller_code'])) {
      $filter_seller_code = $data['filter_seller_code'];
      $filter_seller_code_sql = " AND oms.nickname LIKE '%$filter_seller_code%'";
      
      $sql_filter_seller_invoice .= $filter_seller_code_sql;
      $sql_filter_seller_debit_note .= $filter_seller_code_sql;
      $sql_filter_wsb_purchase .= $filter_seller_code_sql;
      $sql_filter_wsb_purchase_return .= $filter_seller_code_sql;
    }
    
    if(!empty($data['filter_company_name'])) {
      $filter_company_name = $data['filter_company_name'];
      $filter_company_name_sql = " AND oms.company LIKE '%$filter_company_name%'";
      
      $sql_filter_seller_invoice .= $filter_company_name_sql;
      $sql_filter_seller_debit_note .= $filter_company_name_sql;
      $sql_filter_wsb_purchase .= $filter_company_name_sql;
      $sql_filter_wsb_purchase_return .= $filter_company_name_sql;
    }
    
    if(!empty($data['filter_order_no'])) {
      $filter_order_no =  (int)$data['filter_order_no'];
      $sql_filter_seller_invoice .= " AND oo.order_no LIKE '%$filter_order_no%'";
      $sql_filter_seller_debit_note .= " AND oo.order_no LIKE '%$filter_order_no%'";
    }
    
    if(!empty($data['filter_invoice_no'])) {
      $filter_invoice_no =  $controller->db->escape($data['filter_invoice_no']);
      
      $sql_filter_seller_invoice .= " AND CONCAT(osi.seller_invoice_prefix, osi.seller_invoice_no) LIKE '%$filter_invoice_no%'";
      $sql_filter_seller_debit_note .= " AND CONCAT(osi.seller_invoice_prefix, osi.seller_invoice_no) LIKE '%$filter_invoice_no%'";
      $sql_filter_wsb_purchase .= " AND owp.invoice_no LIKE '%$filter_invoice_no%'";
      $sql_filter_wsb_purchase_return .= " AND owp.invoice_no LIKE '%$filter_invoice_no%'";
    }
    
    if(!empty($data['filter_utr'])) {
      $filter_trxn_utr =  $controller->db->escape($data['filter_utr']);
      
      $sql_filter_seller_invoice .= " AND osi.trxn_utr LIKE '%$filter_trxn_utr%'";
      $sql_filter_seller_debit_note .= " AND osdn.trxn_utr LIKE '%$filter_trxn_utr%'";
      $sql_filter_wsb_purchase .= " AND otd.trxn_utr LIKE '%$filter_trxn_utr%'";
      $sql_filter_wsb_purchase_return .= " AND otd.trxn_utr LIKE '%$filter_trxn_utr%'";
    }
    
    if(!empty($data['filter_invoice_date_range'])) {
      $invoice_date_range = explode('-',$data['filter_invoice_date_range']);
      $filter_invoice_date = "'".date('Y-m-d',strtotime(str_replace('/', '-', $invoice_date_range[0])))."' AND '".date('Y-m-d',strtotime(str_replace('/', '-', $invoice_date_range[1])))."'";
      
      $sql_filter_seller_invoice .= " AND osi.date_added BETWEEN $filter_invoice_date";
      $sql_filter_seller_debit_note .= " AND osi.date_added BETWEEN $filter_invoice_date";
      $sql_filter_wsb_purchase .= " AND owp.invoice_date BETWEEN $filter_invoice_date";
      $sql_filter_wsb_purchase_return .= " AND owp.invoice_date BETWEEN $filter_invoice_date";
    }

    
    if(!empty($data['filter_payment_date_range'])) {
      $payment_done_date = explode('-',$data['filter_payment_date_range']);
      $filter_trxn_utr_date = "'".date('Y-m-d',strtotime(str_replace('/', '-', $payment_done_date[0])))."' AND '".date('Y-m-d',strtotime(str_replace('/', '-', $payment_done_date[1])))."'";
      
      $sql_filter_seller_invoice .= " AND osi.trxn_utr_date BETWEEN $filter_trxn_utr_date";
      $sql_filter_seller_debit_note .= " AND osdn.trxn_utr_date BETWEEN $filter_trxn_utr_date";
      $sql_filter_wsb_purchase .= " AND otd.trxn_utr_date BETWEEN $filter_trxn_utr_date";
      $sql_filter_wsb_purchase_return .= " AND otd.trxn_utr_date BETWEEN $filter_trxn_utr_date";

    }
    
    // invoices sqls
    $sql_seller_invoice = "SELECT 
                               'oc_seller_invoice' as tbl, 
                               osi.order_id, 
                               GROUP_CONCAT(DISTINCT osi.seller_invoice_id) as invoice_ids, 
                               MAX(osi.date_added) as invoice_date 
                            FROM 
                              oc_seller_invoice as osi 
                            INNER JOIN oc_order oo ON oo.order_id = osi.order_id
                            INNER JOIN oc_ms_seller oms ON oms.seller_id = osi.seller_id
                            WHERE 
                              osi.trxn_done != 'INVALID_NO_GOODS' " . $sql_filter_seller_invoice . " GROUP BY osi.order_id ";
    $sql_seller_debit_note = "SELECT 
                                'oc_seller_invoice' as tbl, 
                                 osdn.order_id, 
                                 GROUP_CONCAT(DISTINCT oop.seller_invoice_id) as invoice_ids, 
                                 MAX(osi.date_added) as invoice_date
                              FROM 
                                oc_seller_debit_note as osdn 
                              INNER JOIN oc_return ort ON ort.debit_note_id = osdn.debit_note_id 
                              INNER JOIN oc_order_product oop ON oop.order_product_id = ort.order_product_id 
                              INNER JOIN oc_seller_invoice osi ON osi.seller_invoice_id = oop.seller_invoice_id 
                              INNER JOIN oc_order oo ON oo.order_id = osi.order_id
                              INNER JOIN oc_ms_seller oms ON oms.seller_id = osi.seller_id
                              WHERE 
                                osdn.debit_note_no > 0 
                                AND (osdn.custom_id = 0 OR osdn.custom_id IS NULL) 
                                AND osdn.debit_note_status = 1 " . $sql_filter_seller_debit_note . " GROUP BY osdn.order_id ";
                                
    if(empty($data['filter_order_no'])) { // filter with order no will not include wsb purchases
      
      $sql_wsb_purchase = "SELECT 
                              'oc_wsb_purchase' as tbl, 
                              '' as order_id, 
                               owp.purchase_id as invoice_ids, 
                               owp.invoice_date 
                            FROM 
                              oc_wsb_purchase as owp  
                            LEFT JOIN oc_trxn_details otd ON otd.trxn_for_id = owp.purchase_id AND otd.trxn_for = 'WSB_PURCHASE'
                            INNER JOIN oc_ms_seller oms ON oms.seller_id = owp.seller_id
                            WHERE 
                              owp.sor_purchase = 0".$sql_filter_wsb_purchase;
      $sql_wsb_purchase_return = "SELECT 
                                    'oc_wsb_purchase' as tbl, 
                                    '' as order_id, 
                                     owp.purchase_id as invoice_ids, 
                                     owp.invoice_date 
                                  FROM 
                                    oc_wsb_purchase_return as owpr 
                                  INNER JOIN oc_wsb_purchase owp ON owp.purchase_id = owpr.purchase_id 
                                  LEFT JOIN oc_trxn_details otd ON otd.trxn_for_id = owpr.debit_note_id AND otd.trxn_for = 'WSB_PURCHASE_RETURN' 
                                  INNER JOIN oc_ms_seller oms ON oms.seller_id = owp.seller_id
                                  WHERE 
                                    owp.sor_purchase = 0".$sql_filter_wsb_purchase_return;
    }
    
    // create sql with valid UNIONs
    $sql_final_invoice = "";
    if(!empty($sql_seller_invoice)) {
      $sql_final_invoice .= "(".$sql_seller_invoice.")";
    }
    if(!empty($sql_seller_debit_note)) {
      $sql_final_invoice .= " UNION (".$sql_seller_debit_note.")";
    }
    if(!empty($sql_wsb_purchase)) {
      $sql_final_invoice .= " UNION (".$sql_wsb_purchase.")";
    }
    if(!empty($sql_wsb_purchase_return)) {
      $sql_final_invoice .= " UNION (".$sql_wsb_purchase_return.")";
    }
    $sql_final_invoice .= " ORDER BY invoice_date DESC";
    
    // set limit
    if($pagination == 'no'){
       if (isset($data['start']) || isset($data['limit'])) {
         if ($data['start'] < 0) {
             $data['start'] = 0;
         }
         if (empty($data['limit'] || $data['limit'] < 1)) {
             $data['limit'] = 15;
         }
         $sql_final_invoice .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
       }
     }
    $result = $controller->db->query($sql_final_invoice);
    if($pagination == 'total'){
      return $result->num_rows;
    }else{
      return $result->rows;
    }
  }
  
  /***
    @author: Anurag Jain (15 June 2018)
    @description:
      Function returns all seller invoice ids data
    @params: 
      $controller: instance
      $seller_invoice_ids: array of seller invoice ids
    @return: all invoice ids details with debit notes and payments
  ***/
  public static function getSellerInvoicesDetail($controller, $seller_invoice_ids) {
    
    $sql_seller_invoice_details = "SELECT 
              oo.order_no,
              oo.order_id, 
              oos.name as order_status, 
              osi.seller_invoice_id as invoice_id,
              date(osi.date_added) as invoice_date,
              CONCAT(osi.seller_invoice_prefix, osi.seller_invoice_no) as invoice_no,
              sum(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS invoice_value,
              '' as debit_note_id,
              '' as dn_no,
              '' as dn_date,
              '' as dn_value,
              '' as debit_note_status,
              osi.trxn_amount,
              osi.trxn_utr,
              osi.trxn_utr_date
            FROM ".DB_PREFIX."seller_invoice as osi
              INNER JOIN ".DB_PREFIX."order_product as oop ON oop.seller_invoice_id = osi.seller_invoice_id
              INNER JOIN ".DB_PREFIX."order as oo ON oo.order_id = oop.order_id 
              INNER JOIN ".DB_PREFIX."suborder as osub ON osub.order_id = oo.order_id 
              INNER JOIN ".DB_PREFIX."order_status oos ON oos.order_status_id = osub.order_status_id AND oos.language_id = 1 
            WHERE
              osi.seller_invoice_id IN ('".implode("' , '", $seller_invoice_ids)."') 
            AND osub.suborder_id = osi.suborder_id 
            AND oop.suborder_id = osub.suborder_id 
            GROUP BY osi.seller_invoice_id ";
            
    $sql_seller_invoices_debit_note_details = "SELECT
              '' as order_no,
              '' as order_id, 
              '' as order_status, 
              oop.seller_invoice_id as invoice_id,
              '' as invoice_date,
              '' as invoice_no,
              '' AS invoice_value,
              osdn.debit_note_id as debit_note_id,
              CONCAT(osdn.debit_note_prefix, osdn.debit_note_no) as dn_no,
              date(osdn.date_added) as dn_date,
              sum(ort.quantity * oop.transfer_price_per_piece) as dn_value,
              osdn.debit_note_status as debit_note_status,
              osdn.trxn_amount,
              osdn.trxn_utr,
              osdn.trxn_utr_date
            FROM ". DB_PREFIX ."seller_debit_note as osdn
            INNER JOIN ". DB_PREFIX ."return as ort
              ON osdn.debit_note_id = ort.debit_note_id 
            INNER JOIN ". DB_PREFIX ."order_product as oop
              ON ort.order_product_id = oop.order_product_id 
            WHERE
              osdn.debit_note_no > 0 
              AND (osdn.custom_id = 0 OR osdn.custom_id IS NULL) 
              AND osdn.debit_note_status = 1 
              AND oop.seller_invoice_id IN ('".implode("' , '", $seller_invoice_ids)."') 
            GROUP BY osdn.debit_note_id ";
            
    $sql_final_seller_invoices_details = "(".$sql_seller_invoice_details.") UNION (".$sql_seller_invoices_debit_note_details.") ORDER BY invoice_id ASC";
    $result = $controller->db->query($sql_final_seller_invoices_details);
    return $result->rows;
  }
  
  /***
    @author: Anurag Jain (15 June 2018)
    @description:
      Function returns all wsb purchase invoice ids data
    @params: 
      $controller: instance
      $seller_invoice_ids: array of wsb purchase invoice ids
    @return: all invoice ids details with debit notes and payments
  ***/
  public static function getWsbPurchaseInvoicesDetail($controller, $wsb_purchase_invoice_ids) {
    
    $sql_wsb_purchase_invoices_details = "SELECT
              'Offline Purchase' as order_no,
              '' as order_id, 
              '' as order_status, 
              owp.purchase_id as invoice_id,
              date(owp.invoice_date) as invoice_date,
              owp.invoice_no,
              owp.total_purchase_value AS invoice_value,
              '' as debit_note_id,
              '' as dn_no,
              '' as dn_date,
              '' as dn_value,
              '' as debit_note_status,
              SUM(otd.trxn_amount) as trxn_amount,
              otd.trxn_utr,
              otd.trxn_utr_date
            FROM ".DB_PREFIX."wsb_purchase owp 
              LEFT JOIN ".DB_PREFIX."trxn_details otd 
               ON otd.trxn_for_id = owp.purchase_id AND otd.trxn_for = 'WSB_PURCHASE' 
            WHERE owp.sor_purchase = 0 
              AND owp.purchase_id IN ('".implode("' , '", $wsb_purchase_invoice_ids)."')
            GROUP BY owp.purchase_id, otd.trxn_utr";
            
    $sql_wsb_purchase_return_details = "SELECT
              'Offline Purchase' as order_no,
              '' as order_id, 
              '' as order_status, 
              owp.purchase_id as invoice_id,
              '' as invoice_date,
              '' as invoice_no,
              '' AS invoice_value,
              owpr.debit_note_id,
              CONCAT(owpr.debit_note_prefix, owpr.debit_note_no) as dn_no,
              date(owpr.date_added) as dn_date,
              owpr.debit_note_amount as dn_value,
              owpr.debit_note_status as debit_note_status,
              SUM(otd.trxn_amount) as trxn_amount,
              otd.trxn_utr,
              otd.trxn_utr_date
            FROM ".DB_PREFIX."wsb_purchase_return as owpr
              INNER JOIN ". DB_PREFIX ."wsb_purchase as owp
                ON owp.purchase_id = owpr.purchase_id
              LEFT JOIN ".DB_PREFIX."trxn_details otd 
                ON otd.trxn_for_id = owpr.debit_note_id AND otd.trxn_for = 'WSB_PURCHASE_RETURN'
            WHERE owpr.debit_note_no > 0 
              AND owp.sor_purchase = 0  
              AND owp.purchase_id IN ('".implode("' , '", $wsb_purchase_invoice_ids)."')
            GROUP BY owpr.debit_note_id, otd.trxn_utr";
            
    $sql_final_wsb_purchase_invoices_details = "(".$sql_wsb_purchase_invoices_details.") UNION (".$sql_wsb_purchase_return_details.") ORDER BY invoice_id ASC";
    $result = $controller->db->query($sql_final_wsb_purchase_invoices_details);
    return $result->rows;
  }
  

  /***
    @author: Anurag Jain (15 June 2018)
    @description:
      Function formats invoice details data in payment report format
      Also gives download links for invoices and debit notes
    @params: 
      $controller: instance
      $invoice_ids_order_arr: original sort order of invoice ids to be retained in terms of order id
      $final_invoice_details: invoice details to be formated
      $secureFileDload: SecureFileDownload object instance
      $currencyObj: Currency object (to format balances)
    @return: invoice details data in payment report format
  ***/
  public static function getInvoiceDetailsInReportFormat($controller, $invoice_ids_order_arr, $final_invoice_details, $secureFileDload, $currencyObj, $model_wsb_purchase_import) {
    
    $invoice_wise_details = array();
    foreach ($final_invoice_details as $key => $detail) {
      $invoice_wise_details[$detail['invoice_id']][] = $detail;
    }
    
    $parsed_order_ids = array();
    foreach ($invoice_ids_order_arr as $key => $order_detail) {
      if(!empty($order_detail['order_id']) && in_array($order_detail['order_id'], $parsed_order_ids)) {
        continue;
      }
      $parsed_order_ids[] = $order_detail['order_id'];
      $order_detail_invoice_ids = explode(',', $order_detail['invoice_ids']);
      $final_invoice_details_data = array();
      $current_invoice_data = array();
      foreach ($order_detail_invoice_ids as $key => $invoice_id) {
        $net_invoice = 0;
        $paid = 0;
        $net_return = 0;
        $current_invoice_data_with_returns = isset($invoice_wise_details[$invoice_id]) ? $invoice_wise_details[$invoice_id] : array();
        $invoice_payment_temp = array();
        foreach ($current_invoice_data_with_returns as $key => $current_invoice_data) {
          
          // set invoice data
          if(!empty($current_invoice_data['invoice_no'])) {
            $final_invoice_details_data[$invoice_id]['order_no'] = $current_invoice_data['order_no'];
            $final_invoice_details_data[$invoice_id]['invoice']['invoice_no'] = $current_invoice_data['invoice_no'];
            $final_invoice_details_data[$invoice_id]['invoice']['invoice_value'] = $current_invoice_data['invoice_value'];
            $final_invoice_details_data[$invoice_id]['invoice']['invoice_value_formatted'] = $currencyObj->format($current_invoice_data['invoice_value'],'INR',1);
            $final_invoice_details_data[$invoice_id]['invoice']['invoice_date'] = date('d M Y ',strtotime($current_invoice_data['invoice_date']));
            $final_invoice_details_data[$invoice_id]['invoice']['order_status'] = $current_invoice_data['order_status'];
            $net_invoice = $current_invoice_data['invoice_value'];
            
            $seller_invoice = array();
            $seller_invoice['seller_invoice_id'] = (int)$invoice_id;
            $seller_invoice = serialize($seller_invoice);
            if($current_invoice_data['order_no'] == "Offline Purchase"
                && !empty($current_invoice_data['invoice_id'])) {
                $image_data = array();
                $file_url = '';
                $purchase_id = $current_invoice_data['invoice_id'];
                $image_data = $model_wsb_purchase_import->checkDownloadWsbPurchaseInvoiceImage($purchase_id);
                if(!empty($image_data['purchase_bill_image'])) {
                    $encode_file = array();
                    $encode_file['purchase_id'] = $purchase_id;
                    $encode_file = base64_encode(serialize($encode_file));
                    $file_url = $secureFileDload->getDownloadLink('wsb_prchse_inv_img', $encode_file, false);
                }
                $final_invoice_details_data[$invoice_id]['invoice']['download_link'] = $file_url;
            } else {
                $final_invoice_details_data[$invoice_id]['invoice']['download_link'] = $secureFileDload->getDownloadLink('seller_invoice', base64_encode($seller_invoice), false);
            }
          }
          
          // set debit notes data
          if(!empty($current_invoice_data['dn_no']) && (int)$current_invoice_data['debit_note_status'] > 0) {
            $seller_debit_note = array();
            $seller_debit_note['debit_note_id'] = (int)$current_invoice_data['debit_note_id'];
            $seller_debit_note = serialize($seller_debit_note);
            if(!empty($current_invoice_data['order_no']) 
                && $current_invoice_data['order_no'] == "Offline Purchase") {
                $file_name = array();
                $file_name['dn_id'] = (int)$current_invoice_data['debit_note_id'];
                $file_name = serialize($file_name);
                $file_name = base64_encode($file_name);
                $file_url = $secureFileDload->getDownloadLink('wsb_purchase_return', $file_name, false);
            } else {
                $file_url = $secureFileDload->getDownloadLink('seller_debit_note', base64_encode($seller_debit_note), false);
            }
            
            $final_invoice_details_data[$invoice_id]['returns'][] = array(
                                                                      'dn_no' => $current_invoice_data['dn_no'],
                                                                      'dn_date' => date('d M Y ',strtotime($current_invoice_data['dn_date'])),
                                                                      'dn_value' => $current_invoice_data['dn_value'],
                                                                      'dn_value_formatted' => $currencyObj->format($current_invoice_data['dn_value'],'INR',1),
                                                                      'download_link' => $file_url
                                                                    );
            $net_return += $current_invoice_data['dn_value'];
          }
          if(!empty($current_invoice_data['trxn_utr'])) {
            $invoice_payment_temp[$current_invoice_data['trxn_utr']][] = array('trxn_amount' => $current_invoice_data['trxn_amount'], 'trxn_utr_date' => date('d M Y ',strtotime($current_invoice_data['trxn_utr_date'])));
          }
        }
        
        // set payments data
        $final_payments = array();
        if(!empty($invoice_payment_temp)) {
          foreach ($invoice_payment_temp as $trxn_utr => $utr_details) {
            foreach ($utr_details as $key => $payment_details) {
              $final_payments[] = array(
                                    'trxn_utr' => $trxn_utr,
                                    'trxn_amount' => $payment_details['trxn_amount'],
                                    'trxn_amount_formatted' => $currencyObj->format($payment_details['trxn_amount'],'INR',1),
                                    'trxn_utr_date' => $payment_details['trxn_utr_date']
                                  );
              $paid += $payment_details['trxn_amount'];
            }
          }
          $final_invoice_details_data[$invoice_id]['payments'] = $final_payments;
        }
        
        // set balances summary data
        if(!empty($final_invoice_details_data[$invoice_id]['invoice'])) {
          $final_invoice_details_data[$invoice_id]['net_payable'] = ($net_invoice - $net_return);
          $final_invoice_details_data[$invoice_id]['net_payable_formatted'] = $currencyObj->format(($net_invoice - $net_return),'INR',1);
          $final_invoice_details_data[$invoice_id]['paid'] = $paid;
          $final_invoice_details_data[$invoice_id]['paid_formatted'] = $currencyObj->format($paid,'INR',1);
          $final_invoice_details_data[$invoice_id]['balance'] = (($net_invoice - $net_return) - $paid);
          $final_invoice_details_data[$invoice_id]['balance_formatted'] = $currencyObj->format((($net_invoice - $net_return) - $paid),'INR',1);
          if(empty($final_invoice_details_data[$invoice_id]['payments']) && $final_invoice_details_data[$invoice_id]['net_payable'] == 0) {
            $final_invoice_details_data[$invoice_id]['full_return'] = "FULL RETURN";
          }
        }
      }
      // main array key data
      $data[] = $final_invoice_details_data;
    }
    return $data;
  }
}
