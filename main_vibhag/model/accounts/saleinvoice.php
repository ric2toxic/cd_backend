<?php
class ModelAccountsSaleinvoice extends Model
{
  public function getInvoiceDetails($data = array()){
    $sql = "SELECT osub.order_id, osub.suborder_id
            FROM " . DB_PREFIX . "suborder osub
            WHERE osub.invoice_no > 0 
              AND osub.order_status_id  != 2 ";

    
    if(!empty($data['filter_invoice_no_from'])){
      $sql .= " AND osub.invoice_no >= " . (int)$data['filter_invoice_no_from'];
    }

    if(!empty($data['filter_invoice_no_to'])){
      $sql .= " AND osub.invoice_no <= " . (int)$data['filter_invoice_no_to'];
    }

    if(!empty($data['filter_invoice_date_from'])){
      $sql .= " AND DATE(osub.invoice_date) >= DATE('" . $this->db->escape($data['filter_invoice_date_from']) . "')";
    }

    if(!empty($data['filter_invoice_date_to'])){
      $sql .= " AND DATE(osub.invoice_date) <= DATE('" . $this->db->escape($data['filter_invoice_date_to']) . "')";
    }
    $query = $this->db->query($sql);

    if( $query->num_rows ){
      return $query->rows;
    } else {
      return false;
    }    
  }    
}
