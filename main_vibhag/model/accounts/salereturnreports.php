<?php

class ModelAccountsSalereturnreports extends Model {

    public function getSaleDetails($data = array(), $gst=0) {

        $result = array();

        $sql = "SELECT oo.order_id,
                       oo.order_no,
                       oo.customer_id,
                       oo.payment_company,
                       oo.shipping_city,
                       oo.shipping_zone,
                       oo.payment_city,
                       oo.payment_zone,
                       oo.payment_code,
                       oo.gst_number,
                       oo.payment_custom_field,
                       oo.date_added AS order_date,
                       oo.payment_zone_id,
                       oo.currency_value,
                       oo.live_currency_conversion_rate,
                       os.invoice_date,
                       os.suborder_id,
                       ocn.invoice_shipping AS cod_failed_shipping_charge,
                       CONCAT( os.invoice_prefix,os.invoice_no ) AS invoice_no,
                       CONCAT( oo.firstname, ' ', oo.lastname ) AS customer_name,
                       oo.code_version,
                       ocn.credit_note_id,
                       ocn.cod_failed_penalty,
                       CONCAT( ocn.credit_note_prefix, ocn.credit_note_no) as credit_note_no,
                       ocn.date_added as credit_note_date,
                       ocn.shipping_collected as shipping_charge, 
                       ocn.reversal_shipping, 
                       ocn.custom_credit_note_meta,
                       ocn.is_cod_failed
                FROM  " . DB_PREFIX . "credit_note AS ocn
                INNER JOIN  " . DB_PREFIX . "suborder os 
                        ON  os.order_id = ocn.order_id 
                            AND os.invoice_no > 0 
                            AND os.buyer_invoice_id > 0 
                            AND os.order_status_id > 0 
                            AND os.order_status_id <> 2 
                            AND os.gst = " . (int)$gst . " 
                INNER JOIN " . DB_PREFIX . "order oo 
                        ON  os.order_id = oo.order_id 
                            AND oo.franchise_id = 0 
                WHERE ocn.suborder_id = os.suborder_id
                      AND ocn.credit_note_status = 1 ";

        if (!empty($data['filter_credit_note_date_from'])) {
            $sql .= " AND ocn.date_added >=  '" . $this->db->escape(trim($data['filter_credit_note_date_from'])) . " 00:00:00' ";
        }

        if (!empty($data['filter_credit_note_date_to'])) {
            $sql .= " AND ocn.date_added <= '" . $this->db->escape(trim($data['filter_credit_note_date_to'])) . " 23:59:59' ";
        }

        if (!empty($data['filter_invoice_date_from'])) {
            $sql .= " AND os.invoice_date >= '" . $this->db->escape(trim($data['filter_invoice_date_from'])) . " 00:00:00' ";
        }

        if (!empty($data['filter_invoice_date_to'])) {
            $sql .= " AND os.invoice_date <= '" . $this->db->escape(trim($data['filter_invoice_date_to'])) . " 23:59:59' ";
        }
        
        if (!empty($data['filter_customer_id'])) {
            $sql .= " AND oo.customer_id = '".(int) $data['filter_customer_id']."'";
        }
        $sql .= " ORDER BY ocn.date_added ASC, ocn.credit_note_no ASC";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getCustomerLedger($customer_id) {
        $sql = "SELECT ledger_name
                FROM " . DB_PREFIX . "customer_ledgers
                WHERE customer_id = '" . (int) $customer_id . "'";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            return $result->row['ledger_name'];
        } else {
            return $this->generateCustomerLedgers($customer_id)['ledger_name'];
        }
    }

    public function generateCustomerLedgers($customer_id) {

        // Validation that no ledger exist by this customer_id
        $validation_sql = "SELECT customer_id, ledger_name 
                           FROM " . DB_PREFIX . "customer_ledgers 
                           WHERE customer_id = '" . (int)$customer_id . "' 
                           LIMIT 1";
        $validation_query = $this->db->query($validation_sql);

        if ($validation_query->num_rows) {
          return $validation_query->row;
        }

        //No Ledger exists with this customer_id
        $sql = "SELECT  o.customer_id,
                        TRIM(CONCAT(o.customer_id,
                                '-', 
                                TRIM(CONCAT(o.firstname,' ', o.lastname)),
                                '-',
                                TRIM(o.payment_company) 
                              )) AS ledger_name
                FROM " . DB_PREFIX . "order o 
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id 
                WHERE o.customer_id      = '" . (int) $customer_id . "' 
                AND o.franchise_id = 0 
                AND osub.order_id > 0 
                ORDER BY o.order_id ASC LIMIT 1";

        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            $sql = "INSERT INTO " . DB_PREFIX . "customer_ledgers
                    SET customer_id = '" . (int)$result->row['customer_id'] . "',
                        ledger_name = '" . $this->db->escape($result->row['ledger_name']) . "' ";
            if ($this->db->query($sql)) {
                return $result->row;
            }
        }
    }

    public function getReturnsFromCreditNoteId($credit_note_id) {
        $sql = "
                SELECT 
                  ocr.order_product_id, ocr.quantity
                FROM 
                  " . DB_PREFIX . "return ocr
                    INNER JOIN 
                " . DB_PREFIX . "order_product oop ON ocr.order_product_id = oop.order_product_id
                WHERE 
                    ocr.credit_note_id = '" . (int) $credit_note_id . "' 
                    AND oop.buyer_invoice_id > 0
              ";
        $return_products = $this->db->query($sql);
        if ($return_products->num_rows > 0) {
            return $return_products->rows;
        }
    }

}
