<?php
  /**
 * Main Class for getting all seller invoices, GST, SGST, IGST Related Info.
 * All the functions here will be static.
 * @Author Murtaza, 2017
 */
class GstReports {
  

  /**
  * Method to get all Seller Invoices with input tax
  * @param $data : Array includes seller_id, and diff types of dates
  * @author Murtaza, 2017
  */
  public static function getInvoiceDetail($controller, $data = array()){

      $sql .= " SELECT
                  oop.seller_input_tax,
                  osi.seller_invoice_meta,
                  oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece AS total_product_value,
                  (oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax) / 100 AS total_tax
                FROM
                  oc_order_product oop
                INNER JOIN
                  oc_seller_invoice AS osi ON oop.seller_invoice_id = osi.seller_invoice_id
                INNER JOIN
                  oc_ms_seller AS seller ON seller.seller_id = oop.seller_id";

      $sql .=" WHERE oop.seller_invoice_id > 0 ";
      
      $sql .=" AND osi.trxn_done !='Invalid_no_goods' ";

      if(!empty($data['filter_seller_id'])){
        $sql .=  " AND seller.seller_id='".(int)$data['filter_seller_id']."'";
      } 

      if (!empty($data['filter_select_month'])) {
        $sql .=  " AND MONTH(osi.date_added)='".$data['filter_select_month']."'";
      }
      if (!empty($data['filter_select_year'])) {
        $sql .=  " AND YEAR(osi.date_added)='".$data['filter_select_year']."'";
      }

      if(!empty($data['filter_select_quarter'])){
          $sql .= " AND osi.date_added >= '".$controller->db->escape($data['date_range']['start_date'])."' ";
          $sql .= " AND osi.date_added <= '".$controller->db->escape($data['date_range']['end_date'])."' ";
      }

      $query = $controller->db->query($sql);

      if ( $query->num_rows ) {
          return $query->rows;
      } else {
        return array();
      }  

  }
  /**
  * Method to get all Debit Note of seller with input tax
  * @param $data : Array includes seller_id, and diff types of dates
  * @author Murtaza, 2017
  */
  public static function getReturnDetail($controller, $data = array()){

      $sql .= " SELECT
                  oop.seller_input_tax,
                  osi.seller_invoice_meta,
                  oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece AS total_product_value,
                  (oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax) / 100 AS total_tax
                FROM
                  oc_order_product oop
                INNER JOIN
                  oc_seller_invoice AS osi ON oop.seller_invoice_id = osi.seller_invoice_id
                INNER JOIN
                  oc_return AS ort ON oop.order_product_id = ort.order_product_id
                INNER JOIN 
                  oc_seller_debit_note AS sdn ON ort.debit_note_id = sdn.debit_note_id
                INNER JOIN
                  oc_ms_seller AS seller ON seller.seller_id = oop.seller_id";

      $sql .=" WHERE 
                  oop.seller_invoice_id > 0 
                  AND osi.trxn_done !='Invalid_no_goods' ";

      if(!empty($data['filter_seller_id'])){
        $sql .=  " AND seller.seller_id='".(int)$data['filter_seller_id']."'";
      } 

      if (!empty($data['filter_select_month'])) {
        $sql .=  " AND MONTH(osi.date_added)='".$data['filter_select_month']."'";
      }
      if (!empty($data['filter_select_year'])) {
        $sql .=  " AND YEAR(osi.date_added)='".$data['filter_select_year']."'";
      }

      if(!empty($data['filter_select_quarter'])){
          $sql .= " AND osi.date_added >= '".$controller->db->escape($data['date_range']['start_date'])."' ";
          $sql .= " AND osi.date_added <= '".$controller->db->escape($data['date_range']['end_date'])."' ";
      }

      $query = $controller->db->query($sql);

      if ( $query->num_rows ) {
          return $query->rows;
      } else {
        return array();
      }  
  }

  /**
  * Method to get all HSN Details of seller with input tax
  * @param $data : Array includes seller_id, and diff types of dates
  * @author Murtaza, 2017
  */
  public static function getHSNDetail($controller, $data = array()){

      $sql .= " SELECT
                  oop.seller_input_tax,
                  oop.hsn_code,
                  osi.seller_invoice_meta,
                  oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece AS total_product_value,
                  (oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece * oop.seller_input_tax) / 100 AS total_tax
                FROM
                  oc_order_product oop
                INNER JOIN
                  oc_seller_invoice AS osi ON oop.seller_invoice_id = osi.seller_invoice_id
                INNER JOIN
                  oc_ms_seller AS seller ON seller.seller_id = oop.seller_id";

      $sql .=" WHERE oop.seller_invoice_id > 0 ";

      $sql .=" AND osi.trxn_done !='Invalid_no_goods' ";
      
      $sql .=" AND oop.hsn_code <> '' ";

      if(!empty($data['filter_seller_id'])){
        $sql .=  " AND seller.seller_id='".(int)$data['filter_seller_id']."'";
      } 

      if (!empty($data['filter_select_month'])) {
        $sql .=  " AND MONTH(osi.date_added)='".$data['filter_select_month']."'";
      }
      if (!empty($data['filter_select_year'])) {
        $sql .=  " AND YEAR(osi.date_added)='".$data['filter_select_year']."'";
      }

      if(!empty($data['filter_select_quarter'])){
          $sql .= " AND osi.date_added >= '".$controller->db->escape($data['date_range']['start_date'])."' ";
          $sql .= " AND osi.date_added <= '".$controller->db->escape($data['date_range']['end_date'])."' ";
      }

//echo $sql;die;

        $query = $controller->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } else {
          return array();
        }  
  }

}