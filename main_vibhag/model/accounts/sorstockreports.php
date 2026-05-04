<?php
class ModelAccountsSorStockReports extends Model{
  
  /**
  * get all porducts
  * @author: kalyan 25th Sep, 2017
  */
  public function getSorStockReport($data = array()){

    $sql = "SELECT
                wpb.product_id, 
                wp.seller_id,
                ms.nickname,
                ms.company,
                pd.name,
                pd.set_description,
                p.store_sales,
                p.quantity,
                p.piece_in_set,
                p.model,
                p.wsb_purchase_id,
                p.hsn_code,
                p.price,
                p.price_per_set,
                wp.invoice_no,
                wp.invoice_date
            FROM " . DB_PREFIX . "wsb_purchase_breakup wpb
            INNER JOIN " . DB_PREFIX . "wsb_purchase wp
                on wpb.purchase_id=wp.purchase_id AND wp.sor_purchase=1";

   $sql .=" INNER JOIN " . DB_PREFIX . "product p
                on wpb.product_id=p.product_id AND p.sor_product=1";
   
   if(!isset($data['date_to']) && empty($data['date_to'])){             
    $sql .= " AND p.quantity>0";
   }             
    
    if(isset($data['store_name']) && !empty($data['store_name'])){
      $sql .=  " AND p.store_sales='".$this->db->escape($data['store_name'])."'";
    }else{
      $sql .=  " AND p.store_sales!='NO'";  
    }
                
   $sql .=" INNER JOIN " . DB_PREFIX . "product_description pd
                on wpb.product_id=pd.product_id AND pd.language_id=1
            INNER JOIN " . DB_PREFIX . "ms_seller ms
                on wp.seller_id=ms.seller_id";

   if (isset($data['seller_id']) && !empty($data['seller_id'])) {
      $sql .= " AND wp.seller_id='".(int)$data['seller_id']."'";
   }
       
             
    $sql .= " GROUP BY wpb.product_id";
  
    
    
    $result = $this->db->query($sql);

    return $result->rows;
  }
  /**
  * get stores list
  * @author: kalyan 25th Sep, 2017
  */
  public function storeSList(){
    $sql = "SELECT
                vir.pickup_city_code      
            FROM " . DB_PREFIX . "vat_input_rules vir
            GROUP BY vir.pickup_city_code
            ORDER BY vir.pickup_city_code ASC";
    $result = $this->db->query($sql);

    return $result->rows;
  }
  /**
  * get total purchase of one product
  * @author: kalyan 25th Sep, 2017
  */
  public function getSorTotalPurchaseProductQty($filters,$dateTo){
    $sql = "SELECT
                wpb.product_id,
                sum(wpb.pieces) total,
                wp.date_added    
            FROM " . DB_PREFIX . "wsb_purchase_breakup wpb
            INNER JOIN " . DB_PREFIX . "wsb_purchase wp
                on wpb.purchase_id=wp.purchase_id AND wp.sor_purchase='1' AND wp.date_added<='".date('Y-m-d',strtotime($dateTo))."'
            WHERE wpb.product_id='".(int)$filters['product_id']."'";
    $result   = $this->db->query($sql);
    $records  = $result->row;
    
    if (!empty($records) && count($records)>0) {
     return $records['total'];
    }else{
      return 0;
    }
  }
  /**
  * get total sale one product
  * @author: kalyan 25th Sep, 2017
  */
  public function getSorTotalSalesProductQty($filters,$dateTo){
   
    $sql = "SELECT
                op.product_id,
                sum(op.quantity) total,
                o.date_added      
            FROM " . DB_PREFIX . "order_product op
            INNER JOIN " . DB_PREFIX . "order o
                on op.order_id=o.order_id AND o.date_added<='".date('Y-m-d',strtotime($dateTo))." ' 
             WHERE op.product_id='".(int)$filters['product_id']."' 
             AND op.sor_product=1 and op.seller_invoice_id>0 
             AND o.franchise_id = 0 
             GROUP BY op.product_id";
      
    $result   = $this->db->query($sql);
    $records  = $result->row;
    
    if (!empty($records) && count($records)>0) {
     return $records['total'];
    }else{
      return 0;
    }
  }  
}
