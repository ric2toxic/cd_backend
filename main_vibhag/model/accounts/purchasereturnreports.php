<?php

class ModelAccountsPurchasereturnreports extends Model {

    public function getPurchaseReturnDetails($data = array(), $gst=0) {

      $sql = "SELECT
         odn.debit_note_id,
         odn.order_id,
         odn.suborder_id,
         odn.seller_id,
         odn.debit_note_no,
         odn.debit_note_prefix,
         odn.date_added as debit_note_date,
         o.order_no,
         o.date_added as order_date,
         si.seller_invoice_prefix,
         si.seller_invoice_no,
         si.date_added as seller_invoice_date,
         si.seller_invoice_meta,
         '' as purchase_firm_meta,
         si.vat_input_rule_id,
         SUM(ort.quantity * oop.transfer_price_per_piece) AS purchase_return_taxratewise,
         oop.seller_input_tax,
         oop.seller_cst,
         0 as stock_transfer,
         '' as gst_state_code,
         o.payment_zone,
         o.gst_number,
         oms.nickname
        FROM " . DB_PREFIX . "seller_debit_note odn 
        INNER JOIN " . DB_PREFIX . "return ort ON ort.debit_note_id = odn.debit_note_id 
        INNER JOIN " . DB_PREFIX . "order_product oop ON oop.order_product_id = ort.order_product_id 
        INNER JOIN " . DB_PREFIX . "seller_invoice si ON si.seller_invoice_id = oop.seller_invoice_id 
        INNER JOIN " . DB_PREFIX . "order o ON ( odn.order_id = o.order_id )
        INNER JOIN " . DB_PREFIX . "ms_seller oms ON ( odn.seller_id = oms.seller_id )
        WHERE odn.debit_note_status = 1
          AND (odn.custom_id = 0 OR odn.custom_id IS NULL)
          AND si.gst = $gst 
          AND o.franchise_id = 0 ";

      if( empty($data['filter_allow_wsb_order']) ) {
        $sql .= "  AND odn.debit_note_no > 0  ";
      }

      // Order Date
      if (!empty($data['filter_order_date_from'])) {
        $sql .= " AND DATE(o.date_added) >=  DATE('" . $this->db->escape($data['filter_order_date_from']) . "') ";
      }

      if (!empty($data['filter_order_date_to'])) {
        $sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_order_date_to']) . "') ";
      }

      // Debit Note Date
      if (!empty($data['filter_debit_note_date_from'])) {
        $sql .= " AND DATE(odn.date_added) >= DATE('" . $this->db->escape($data['filter_debit_note_date_from']) . "') ";
      }

      if (!empty($data['filter_debit_note_date_to'])) {
        $sql .= " AND DATE(odn.date_added) <=  DATE('" . $this->db->escape($data['filter_debit_note_date_to']) . "') ";
      }
      
      if (!empty($data['filter_supplier_id'])) {
        $sql .= " AND oms.nickname LIKE  '%".$this->db->escape($data['filter_supplier_id'])."%' ";
      }

      $sql .= "  GROUP BY oop.seller_input_tax, oop.seller_cst, odn.debit_note_id";
      $sql .= " ORDER BY odn.debit_note_no ASC";

      if($gst == 1){
        $sql_union = ' UNION ';

        $sql_second = "
                      SELECT
                       wpr.debit_note_id,
                       '' as order_id,
                       '' as suborder_id,
                       wp.seller_id,
                       wpr.debit_note_no,
                       wpr.debit_note_prefix,
                       wpr.date_added as debit_note_date,
                       '' as order_no,
                       '' as order_date,
                       '' as seller_invoice_prefix,
                       wp.invoice_no as seller_invoice_no,
                       wp.invoice_date as seller_invoice_date,
                       wp.seller_firm_meta as seller_invoice_meta,
                       wp.purchase_firm_meta as purchase_firm_meta,
                       0 as vat_input_rule_id,
                      SUM(wprb.quantity * wprb.transfer_price_per_piece) AS purchase_return_taxratewise,
                       wprb.tax_rate as seller_tax,
                       0 as seller_cst,
                       0 as stock_transfer,
                       '' as gst_state_code,
                       '' as payment_zone,
                       '' as gst_number,
                       oms.nickname
                      FROM " . DB_PREFIX . "wsb_purchase_return wpr
                          INNER JOIN 
                      " . DB_PREFIX . "wsb_purchase_return_breakup wprb ON wprb.debit_note_id = wpr.debit_note_id
                          INNER JOIN 
                      " . DB_PREFIX . "wsb_purchase wp ON wp.purchase_id = wpr.purchase_id 
                          INNER JOIN 
                      " . DB_PREFIX . "product op ON op.product_id = wprb.product_id 
                          INNER JOIN 
                      " . DB_PREFIX . "ms_seller oms ON ( wp.seller_id = oms.seller_id )
                      WHERE 
                        wpr.debit_note_status = 1
                        
                      ";

        if( empty($data['filter_allow_wsb_order']) ) {
          $sql_second .= "  AND wpr.debit_note_no > 0   ";
        }

        // Debit Note Date Filter
        if (!empty($data['filter_debit_note_date_from'])) {
          $sql_second .= " AND DATE(wpr.date_added) >= DATE('" . $this->db->escape($data['filter_debit_note_date_from']) . "') ";
        }

        if (!empty($data['filter_debit_note_date_to'])) {
          $sql_second .= " AND DATE(wpr.date_added) <=  DATE('" . $this->db->escape($data['filter_debit_note_date_to']) . "') ";
        }
        if (!empty($data['filter_supplier_id'])) {
         $sql_second .= " AND oms.nickname LIKE  '%".$this->db->escape($data['filter_supplier_id'])."%' ";
        }
        $sql_second .= "  GROUP BY wprb.tax_rate, wprb.debit_note_id";
        $sql_second .= " ORDER BY wpr.debit_note_no ASC";

        $sql = '('. $sql . ')' . $sql_union . '('. $sql_second . ')';
        
      
      $sql .= " UNION ";
       $sql_third = "(SELECT ocn.credit_note_id as debit_note_id,
                       oo.order_id,
                       os.suborder_id,
                       oop.seller_id,
                       ocn.credit_note_no as debit_note_no,
                       ocn.credit_note_prefix as debit_note_prefix,
                       ocn.date_added as debit_note_date,
                       oo.order_no,
                       oo.date_added as order_date,
                       os.invoice_prefix as seller_invoice_prefix,
                       os.invoice_no as seller_invoice_no,
                       os.invoice_date as seller_invoice_date,
                       si.seller_invoice_meta,
                       '' as purchase_firm_meta,
                       si.vat_input_rule_id,
                       SUM(ort.quantity * oop.price_per_piece * ROUND((1+oop.output_tax_rates/100),2)) AS purchase_return_taxratewise,
                       oop.output_tax_rates as seller_input_tax,
                       0 as seller_cst,
                       1 as stock_transfer,
                       oz.gst_state_code,
                       oo.payment_zone,
                       oo.gst_number,
                       oms.nickname 
                FROM  " . DB_PREFIX . "credit_note AS ocn
                INNER JOIN " . DB_PREFIX . "return ort ON ort.credit_note_id = ocn.credit_note_id
                INNER JOIN  " . DB_PREFIX . "order_product oop ON oop.order_product_id=ort.order_product_id 
                INNER JOIN  " . DB_PREFIX . "suborder os ON  ( oop.buyer_invoice_id = os.buyer_invoice_id )
                INNER JOIN " . DB_PREFIX . "order oo ON (os.order_id = oo.order_id)
                INNER JOIN " . DB_PREFIX . "seller_invoice si ON si.seller_invoice_id = oop.seller_invoice_id 
                INNER JOIN " . DB_PREFIX . "ms_seller oms ON ( oop.seller_id = oms.seller_id )
                INNER JOIN " . DB_PREFIX . "zone oz ON ( oo.payment_zone_id = oz.zone_id )
                WHERE 
                  ocn.suborder_id = os.suborder_id
                  AND ocn.credit_note_status=1
                  AND oo.stock_transfer = 1
                  AND os.gst = 1 
                  AND os.buyer_invoice_id > 0 
                  AND os.invoice_no > 0 
                  AND os.order_status_id > 0 
                  AND os.order_status_id != 2 
                  AND oo.franchise_id = 0 ";

        if (!empty($data['filter_debit_note_date_from'])) {
            $sql_third .= " AND DATE(ocn.date_added) >=  DATE('" . $this->db->escape($data['filter_debit_note_date_from']) . "')";
        }

        if (!empty($data['filter_debit_note_date_to'])) {
            $sql_third .= " AND DATE(ocn.date_added) <= DATE('" . $this->db->escape($data['filter_debit_note_date_to']) . "')";
        }

        if (!empty($data['filter_invoice_date_from'])) {
            $sql_third .= " AND DATE(os.invoice_date) >= DATE('" . $this->db->escape($data['filter_invoice_date_from']) . "')";
        }

        if (!empty($data['filter_invoice_date_to'])) {
            $sql_third .= " AND DATE(os.invoice_date) <= DATE('" . $this->db->escape($data['filter_invoice_date_to']) . "')";
        }

        if (!empty($data['filter_supplier_id'])) {
        $sql_third .= " AND oms.nickname LIKE  '%".$this->db->escape($data['filter_supplier_id'])."%' ";
        }

        
        if (!empty($data['filter_customer_id'])) {
            $sql_third .= " AND oo.customer_id = '".(int) $data['filter_customer_id']."'";
        }
        $sql_third .= "  GROUP BY oop.output_tax_rates, ocn.credit_note_id";
        $sql_third .= " ORDER BY ocn.date_added, ocn.credit_note_no ASC)";
        
        
        $sql = $sql.$sql_third;
      }
        
      $query = $this->db->query($sql);
      $result = $query->rows;
      
      return $result;
    }

    public function getSellerDetails() {
      $sql = "SELECT seller_id, nickname, company, city, tin FROM " . DB_PREFIX . "ms_seller WHERE 1";
      $query = $this->db->query($sql);
      $seller_details = array();
      if ($query->num_rows) {
        $seller_ids = array_column($query->rows, 'seller_id');
        $seller_details = array_combine($seller_ids, $query->rows);
      }
      return $seller_details;
    }

}
