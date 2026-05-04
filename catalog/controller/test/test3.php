<?php
class ControllerTestTest3 extends Controller{

    private $seller_data = array();
    private $wsb_firm_data = array();
    
    public function makingDelhiTINLive() {
        
        // basic check to avoid any shipped+ order without buyer invoice
        $sql = "SELECT o.order_id,o.order_no
                FROM oc_order o
                INNER JOIN oc_suborder os ON ( o.order_id = os.order_id )
                WHERE os.order_status_id IN (4,5,13,14,15) AND os.invoice_no = 0";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            echo "<pre>"; print_r($query->rows); echo "</pre>";
            echo "Buyer invoice not generated despite shipped. fix these first. exiting..";
            exit();
        }
        
        // Now get suborders in processed state with Delhi invoicing
        $this->getProcessedOrderForDL();        
        
        
        $this->getAllSellerData();
        $this->getWSBFirmData();
        
    
        // First update all the existing seller_invoice(s)
        $this->updateSellerInvoices();
        
        
        
        //// DROP DEBIT NOTE COLUMN //////
        
        // Update pending orders
        
        // Update product tax rates
        
        
        /* 
update 
oc_product op
INNER JOIN oc_ms_product omp ON omp.product_id = op.product_id
INNER JOIN oc_ms_seller oms ON oms.seller_id = omp.seller_id 
SET 
op.seller_tax = 12.5, op.tax_class_id = 15  
WHERE oms.zone_id
IN ( 1483 ) 
AND op.seller_tax = 0 
AND op.tax_class_id = 11;


update 
oc_product op
INNER JOIN oc_ms_product omp ON omp.product_id = op.product_id
INNER JOIN oc_ms_seller oms ON oms.seller_id = omp.seller_id 
SET 
op.seller_tax = 5, op.tax_class_id = 14   
WHERE oms.zone_id = 1483  
AND op.seller_tax = 0 
AND op.tax_class_id = 9;


update 
oc_product op
INNER JOIN oc_ms_product omp ON omp.product_id = op.product_id
INNER JOIN oc_ms_seller oms ON oms.seller_id = omp.seller_id 
SET 
op.seller_tax = 0, op.tax_class_id = 14   
WHERE oms.zone_id IN (1486, 1505)   
AND op.seller_tax = 0 
AND op.tax_class_id = 9;


update oc_product set selling_price = ceil(price * (1 + (commission/100)) / (1 + (seller_tax/100))) where 1;

*/ 

        
        
    
    
    }
    
    private function getAllSellerData() {
        $sql = "SELECT oms.seller_id, oms.company, oms.address1, oms.address2, 
                       oms.pincode, oms.city, oms.zone_id, oms.country_id, oms.tin, 
                       oct.name as country, 
                       ozn.name as state,  
                FROM oc_ms_seller 
                LEFT JOIN oc_country oct ON oct.country_id = oms.country_id 
                LEFT JOIN oc_zone ozn ON ozn.zone_id = oms.zone_id 
                WHERE 1";
        $query = $this->db->query($sql);
        $this->seller_data = array_combine( array_column($query->rows, 'seller_id'), 
                                            $query->rows
                                          );
                                          
        foreach ($this->seller_data as $sid => $detail) {
            unset($this->seller_data[$sid]['seller_id']);
        }
    }
    
    private function getWSBFirmData() {
        $sql = "SELECT ovr.purchase_firm_name as company, 
                       ovr.purchase_firm_address1 as address1, 
                       ovr.purchase_firm_address2 as address2, 
                       ovr.purchase_firm_city as city, 
                       ovr.purchase_firm_pincode as pincode, 
                       ovr.purchase_firm_tin_no as tin, 
                       ovr.zone_id, 
                       ozn.country_id, 
                       oct.name as country, 
                       ozn.name as state,  
                FROM oc_vat_input_rules ovr  
                LEFT JOIN oc_zone ozn ON ozn.zone_id = ovr.zone_id 
                LEFT JOIN oc_country oct ON oct.country_id = ozn.country_id 
                WHERE ovr.default = 1";
        $query = $this->db->query($sql);
        $this->wsb_firm_data = $query->row;
    }
            
        
    
    private function updateSellerInvoices(){
        
        // First get all seller invoices
        $sql = "SELECT order_id, suborder_id, seller_id, seller_invoice_prefix, seller_invoice_no
                FROM oc_seller_invoice 
                WHERE 1";
        $query = $this->db->query($sql);
        
        foreach ($query->rows as $row) {
            
            $meta = array();
            $meta['buyer_data'] = $this->wsb_firm_data;
            $meta['seller_data'] = $this->seller_data[$row['seller_id']];
            $rule_id = 1;
            if ($this->seller_data[$row['seller_id']]['zone_id'] == 1501) {
                $rule_id = 2;
            }
            
            $sql = "UPDATE oc_seller_invoice 
                    SET seller_invoice_meta = '" . $this->db->escape(serialize($meta)) . "', 
                        vat_input_rule_id = '" . (int)$rule_id . "' 
                    WHERE order_id = '" . (int)$row['order_id'] . "' 
                      AND suborder_id = '" . $this->db->escape($row['suborder_id']) . "' 
                      AND seller_id = '" . (int)$row['seller_id'] . "' 
                      AND seller_invoice_prefix = '" . $this->db->prefix($row['seller_invoice_prefix']) . "' 
                      AND seller_invoice_no = '" . (int)$row['seller_invoice_no'] . "'";
                      
            $this->db->query($sql);
            
        }

    }
    

    public function getProcessedOrderForDL(){
        
        //  Get all processed orders from Delhi
        $sql = "SELECT distinct(os.suborder_id)
                FROM oc_suborder os
                INNER JOIN oc_order_product oop ON ( os.order_id = oop.order_id )
                INNER JOIN oc_ms_product omp ON ( omp.product_id = oop.product_id )
                INNER JOIN oc_ms_seller oms ON (omp.seller_id = oms.seller_id )
                WHERE os.suborder_id = oop.suborder_id 
                  AND os.order_status_id IN (9,16) 
                  AND (oms.zone_id = 1483 or oms.zone_id = 1486 or oms.zone_id = 1505)";
        $result = $this->db->query($sql);
        echo "<pre>"; print_r($result); echo "</pre>"; exit();
    }

    public function updateOrderProductTaxesForDLForPending(){
        $sql = "SELECT order_id
                FROM oc_suborder
                WHERE order_status_id = 1 OR ( order_status_id = 0 AND DATEDIFF(date_added,now()) <= 30 )";
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            //foreach( $result->rows as $row){
                $order_ids = array_column($result->rows,'order_id');
                $this->db->query("SET @suborder_id := ''");
                $sql = "UPDATE oc_order_product oop
                        INNER JOIN oc_ms_product omp ON ( oop.product_id = omp.product_id )
                        INNER JOIN oc_ms_seller oms ON ( omp.seller_id = oms.seller_id AND oms.zone_id = 1483 )
                        SET oop.seller_input_tax = 5.0000,
                            oop.seller_cst = 0,
                            oop.output_tax_rates = '5.0000P'
                        WHERE oop.order_id IN (". implode(",",$order_ids) .")
                        AND ( SELECT @suborder_id := IF(@suborder_id = '',CONCAT('\"',oop.order_id,'\":\"',oop.suborder_id,'\"'),CONCAT(@suborder_id,',\"',oop.order_id,'\":\"',oop.suborder_id,'\"')) );
                        ";
                $this->db->query($sql);
                $suborder_id = $this->db->query("SELECT CONCAT('{',@suborder_id,'}') as suborder_ids;");
                $suborder_id = json_decode($suborder_id->row['suborder_ids']);
                if(!empty($suborder_id)){
                    foreach ($suborder_id as $order_id => $suborder_id) {
                        $buyer_invoice = new BuyerInvoice($this);
                        $totals = $buyer_invoice->getTotals($order_id,$suborder_id);
                        $suborder_total = (float)$totals['total_amt']['value'] - (float)$totals['credit']['value'];
                        //$sql = "UPDATE oc_suborder SET total = "
                        //exit;
                    }
                }


            //}
        }
   }

 }
