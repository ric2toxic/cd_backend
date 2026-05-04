<?php
class ModelAccountsPaymentsreport extends Model
{

  public function getPaymentDetail($data = array(), $pagination='no'){
    
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
                AND sp.trxn_done IN ('BANK_REQUESTED','BANK_SUCCESS','NOT_APPLICABLE') ";

      
      /*
      * this condition returns without franchise id orders
      */
      $sql_1 .= " AND oo.franchise_id = 0 ";

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

      $sql_2 .=" WHERE oop.seller_invoice_id > 0";
      /*
      * this condition returns without franchise id orders
      */
      $sql .= " AND oo.franchise_id = 0 ";

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
      
      $sql = "(".$sql_1.") UNION (".$sql_2.")";
      
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
    
   
    $result = $this->db->query($sql);
    
    if($pagination == 'total'){
      return $result->num_rows;
    }else{
      return $result->rows;
    }
  }

  public function getInvoicesDetail($invoice_ids){
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
    
    $invoices_result = $this->db->query($sql);
    return $invoices_result->rows;
  }

  public function getReturnDetail($order_ids){
    $select = "
            sdn.order_id,
            sdn.seller_id,
            sdn.debit_note_id as return_id,
            sdn.debit_note_no as return_no,
            sdn.debit_note_amount as return_amt,
            sdn.date_added as return_date,
            sdn.trxn_utr as return_bank_ref,
            sdn.trxn_amount as return_payment_amt,
            sdn.trxn_utr_date as return_payment_date,
            sdn.trxn_done AS return_trxn_done
            ";
    $sql = "SELECT ". $select." 
              FROM ". DB_PREFIX ."seller_debit_note as sdn
              INNER JOIN ". DB_PREFIX ."ms_seller as seller ON seller.seller_id = sdn.seller_id  
            WHERE sdn.order_id IN (".$order_ids.")
            AND (sdn.custom_id = 0 OR sdn.custom_id = NULL)
            ";

    $sql .= " ORDER BY sdn.order_id ";
    
    $returns_result = $this->db->query($sql);
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
}
