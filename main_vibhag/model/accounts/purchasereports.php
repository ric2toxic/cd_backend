<?php

class ModelAccountsPurchasereports extends Model {

    public function getPurchaseDetails($data = array(), $gst = 0) {
        $sql = "(SELECT
                       0 as purchase_id,
                       '' as stock_trans_details,
                       o.order_id,
                       o.order_no,
                       os.suborder_id,
                       os.invoice_prefix,
                       os.invoice_no,
                       os.invoice_date,
                       os.order_status_id,
                       oop.seller_id,
                       oop.hsn_code,
                       osi.seller_invoice_id, 
                       osi.seller_invoice_prefix,
                       osi.seller_invoice_no,
                       osi.seller_invoice_meta,
                       '' as seller_invoice_meta_1,
                       osi.date_added as seller_invoice_date,
                       osi.vat_input_rule_id,
                       os.date_added as order_date,
                       sum(oop.transfer_price_per_piece * oop.piece_in_set * oop.quantity) as purchase_taxratewise,
                       oop.seller_input_tax,
                       oop.seller_cst,
                       oms.nickname
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder os ON (o.order_id = os.order_id)
                INNER JOIN " . DB_PREFIX . "order_product oop ON (os.order_id = oop.order_id )
                INNER JOIN " . DB_PREFIX . "ms_seller oms ON (oms.seller_id = oop.seller_id)
                INNER JOIN  " . DB_PREFIX . "seller_invoice osi ON (osi.seller_invoice_id = oop.seller_invoice_id)
                WHERE os.order_status_id > 0
                  AND os.gst = '" . (int) $gst . "'
                  AND os.suborder_id = oop.suborder_id
                  AND oms.seller_invoice_generate = 1 AND oop.sor_product=0
                  AND osi.trxn_done != 'INVALID_NO_GOODS' 
                  AND o.franchise_id = 0 ";

        if (!empty($data['seller_id']) && (int) $data['seller_id'] > 0) {
            $sql .= " AND oop.seller_id = '" . (int) $data['seller_id'] . "' ";
        }

        // Order Date
        if (!empty($data['filter_order_date_from'])) {
            $sql .= " AND DATE(os.date_added) >=  DATE('" . $this->db->escape($data['filter_order_date_from']) . "') ";
        }

        if (!empty($data['filter_order_date_to'])) {
            $sql .= " AND DATE(os.date_added) <= DATE('" . $this->db->escape($data['filter_order_date_to']) . "') ";
        }

        // WSB Invoice Date
        if (!empty($data['filter_wsb_inv_date_from'])) {
            $sql .= " AND DATE(os.invoice_date) >= DATE('" . $this->db->escape($data['filter_wsb_inv_date_from']) . "') ";
        }

        if (!empty($data['filter_wsb_inv_date_to'])) {
            $sql .= " AND DATE(os.invoice_date) <=  DATE('" . $this->db->escape($data['filter_wsb_inv_date_to']) . "') ";
        }

        // Seller Invoice Date
        if (!empty($data['filter_seller_inv_date_from'])) {
            $sql .= " AND DATE(osi.date_added) >=  DATE('" . $this->db->escape($data['filter_seller_inv_date_from']) . "') ";
        }

        if (!empty($data['filter_seller_inv_date_to'])) {
            $sql .= " AND DATE(osi.date_added) <= DATE('" . $this->db->escape($data['filter_seller_inv_date_to']) . "') ";
        }
        
        if (!empty($data['filter_supplier_id'])) {
            $sql .= " AND oms.nickname =  '".$this->db->escape($data['filter_supplier_id'])."' ";
        }

        $sql .= " GROUP BY osi.seller_invoice_id, 
                           oop.seller_input_tax,
                           oop.seller_cst
                  HAVING purchase_taxratewise > 0 ";
        $sql .= " ORDER BY o.order_id, os.suborder_id, oop.seller_id, oop.seller_input_tax ) ";

        $sql_union = ' UNION ';

        $sql_second = "
            (SELECT 
                owp.purchase_id,
                '' as stock_trans_details,
                '' as order_id,
                '' as order_no,
                '' as suborder_id,
                '' as invoice_prefix,
                '' as invoice_no,
                '' as invoice_date,
                '' as order_status_id,
                owp.seller_id,
                op.hsn_code as hsn_code,
                '' as seller_invoice_id, 
                '' as seller_invoice_prefix,
                owp.invoice_no as seller_invoice_no,
                owp.purchase_firm_meta as seller_invoice_meta,
                owp.seller_firm_meta as seller_invoice_meta_1,
                owp.invoice_date as seller_invoice_date,
                '' as vat_input_rule_id,
                owp.date_added as order_date,
                sum(owpb.transfer_price_per_piece * owpb.pieces) as purchase_taxratewise,
                owpb.seller_tax as seller_input_tax,
                0 as seller_cst,
                oms.nickname

            FROM oc_wsb_purchase owp INNER JOIN 
                 oc_wsb_purchase_breakup owpb on owp.purchase_id=owpb.purchase_id INNER JOIN
                 oc_product op on owpb.product_id=op.product_id INNER JOIN
                 oc_ms_seller oms on owp.seller_id=oms.seller_id
            WHERE 1 ";

        // IF Order Date filter exists than there is no mean for selecting wsb purchase entry
        if (!empty($data['filter_order_date_from'])) {
            $sql_second .= " AND FALSE ";
        }

        if (!empty($data['filter_order_date_to'])) {
            $sql_second .= " AND FALSE ";
        }
        
        // Seller Invoice Date
        if (!empty($data['filter_seller_inv_date_from'])) {
            $sql_second .= " AND DATE(owp.invoice_date) >= DATE('" . $this->db->escape($data['filter_seller_inv_date_from']) . "') ";
        }

        if (!empty($data['filter_seller_inv_date_to'])) {
            $sql_second .= " AND DATE(owp.invoice_date) <=  DATE('" . $this->db->escape($data['filter_seller_inv_date_to']) . "') ";
        }
        
        if (!empty($data['filter_supplier_id'])) {
            $sql_second .= " AND oms.nickname =  '".$this->db->escape($data['filter_supplier_id'])."' ";
        }

        if ($gst) {
            $sql_second .= " AND DATE(owp.invoice_date) >=  '" . date('2017-07-01 00:00:00') . "' ";
        } else {
            $sql_second .= " AND DATE(owp.invoice_date) <  '" . date('2017-07-01 00:00:00') . "' ";
        }

        $sql_second .= " GROUP BY owpb.purchase_id, 
                                  owpb.seller_tax 
                         HAVING purchase_taxratewise > 0 
                         ORDER BY owp.seller_id, owpb.seller_tax)";

        

        


        $sql_for_stock_trans = "(SELECT
                       0 as purchase_id,
                       CONCAT(o.payment_company, '~=',o.payment_city, '~=',o.payment_zone, '~=', o.gst_number, '~=', oz.gst_state_code) as stock_trans_details,
                       o.order_id,
                       o.order_no,
                       os.suborder_id,
                       os.invoice_prefix,
                       os.invoice_no,
                       os.invoice_date,
                       os.order_status_id,
                       oop.seller_id,
                       oop.hsn_code,
                       osi.seller_invoice_id, 
                       os.invoice_prefix as seller_invoice_prefix,
                       os.invoice_no as seller_invoice_no,
                       osi.seller_invoice_meta,
                       '' as seller_invoice_meta_1,
                       os.invoice_date as seller_invoice_date,
                       osi.vat_input_rule_id,
                       os.date_added as order_date,
                       sum(oop.transfer_price_per_piece * oop.piece_in_set * oop.quantity) as purchase_taxratewise,
                       oop.seller_input_tax,
                       oop.seller_cst,
                       oms.nickname
                FROM " . DB_PREFIX . "order o
                INNER JOIN " . DB_PREFIX . "suborder os ON (o.order_id = os.order_id)
                INNER JOIN " . DB_PREFIX . "order_product oop ON (os.order_id = oop.order_id )
                INNER JOIN " . DB_PREFIX . "ms_seller oms ON (oms.seller_id = oop.seller_id)
                INNER JOIN " . DB_PREFIX . "seller_invoice osi ON (osi.seller_invoice_id = oop.seller_invoice_id)
                INNER JOIN " . DB_PREFIX . "zone oz ON (o.payment_zone_id = oz.zone_id)
                WHERE os.order_status_id > 0
                    AND os.order_status_id != 2
                 AND os.invoice_no > 0
                 AND os.buyer_invoice_id > 0
                  AND os.gst = '" . (int) $gst . "'
                  AND os.suborder_id = oop.suborder_id
                  AND o.stock_transfer = 1
                  AND osi.trxn_done != 'INVALID_NO_GOODS' 
                  AND o.franchise_id = 0 ";

        // Order Date
        if (!empty($data['filter_order_date_from'])) {
            $sql_for_stock_trans .= " AND DATE(os.date_added) >=  DATE('" . $this->db->escape($data['filter_order_date_from']) . "') ";
        }

        if (!empty($data['filter_order_date_to'])) {
            $sql_for_stock_trans .= " AND DATE(os.date_added) <= DATE('" . $this->db->escape($data['filter_order_date_to']) . "') ";
        }

        // WSB Invoice Date
        if (!empty($data['filter_wsb_inv_date_from'])) {
            $sql_for_stock_trans .= " AND DATE(os.invoice_date) >= DATE('" . $this->db->escape($data['filter_wsb_inv_date_from']) . "') ";
        }

        if (!empty($data['filter_wsb_inv_date_to'])) {
            $sql_for_stock_trans .= " AND DATE(os.invoice_date) <=  DATE('" . $this->db->escape($data['filter_wsb_inv_date_to']) . "') ";
        }

        // Seller Invoice Date
        if (!empty($data['filter_seller_inv_date_from'])) {
            $sql_for_stock_trans .= " AND DATE(osi.date_added) >=  DATE('" . $this->db->escape($data['filter_seller_inv_date_from']) . "') ";
        }

        if (!empty($data['filter_seller_inv_date_to'])) {
            $sql_for_stock_trans .= " AND DATE(osi.date_added) <= DATE('" . $this->db->escape($data['filter_seller_inv_date_to']) . "') ";
        }
        
        if (!empty($data['filter_supplier_id'])) {
            $sql_for_stock_trans .= " AND oms.nickname =  '".$this->db->escape($data['filter_supplier_id'])."' ";
        }

        $sql_for_stock_trans .= " GROUP BY osi.seller_invoice_id, 
                           oop.seller_input_tax,
                           oop.seller_cst
                  HAVING purchase_taxratewise > 0 ";
        $sql_for_stock_trans .= " ORDER BY o.order_id, os.suborder_id, oop.seller_id, oop.seller_input_tax ) ";

        //$query = $this->db->query($sql_for_stock_trans);
        
        $sql = $sql . $sql_union . $sql_second . $sql_union . $sql_for_stock_trans;
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getSellerDetails() {
        $sql = "SELECT seller_id,
                       nickname,
                       company,
                       city,
                       tin
                FROM " . DB_PREFIX . "ms_seller WHERE 1";
        $query = $this->db->query($sql);
        $seller_details = array();
        if ($query->num_rows) {
            $seller_ids = array_column($query->rows, 'seller_id');
            $seller_details = array_combine($seller_ids, $query->rows);
        }
        return $seller_details;
    }

}
