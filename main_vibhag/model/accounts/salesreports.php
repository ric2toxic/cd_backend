<?php

class ModelAccountsSalesreports extends Model {

    public function getSaleDetails($data = array(), $gst = 0) {

        $result = array();

        $sql = "SELECT oo.order_id,
                       oo.order_no,
                       oo.customer_id,
                       oo.payment_company,
                       oo.shipping_city,
                       oo.shipping_zone,
                       oo.payment_city,
                       oo.payment_zone,
                       oo.payment_custom_field,
                       oo.date_added AS order_date,
                       oo.gst_number,
                       oo.payment_zone_id,
                       oo.payment_code,
                       oo.currency_value,
                       oo.live_currency_conversion_rate,
                       osub.invoice_date,
                       osub.suborder_id,
                       osub.shipping_charge,
                       if( (osub.courier_partner='' OR osub.courier_partner IS NULL),osub.shipping_method, osub.courier_partner) as courier_partner,
                       osub.tracking_no,
                       CONCAT( osub.invoice_prefix, osub.invoice_no ) AS invoice_no,
                       CONCAT( oo.firstname, ' ', oo.lastname ) AS customer_name,
                       oo.code_version,
                       IFNULL(MIN(DATE(IF(ooh.order_status_id=15,ooh.date_added,NULL))), '') as delivery_date
                FROM  " . DB_PREFIX . "order AS oo
                INNER JOIN  " . DB_PREFIX . "suborder osub
                  ON (osub.order_id = oo.order_id)
                INNER JOIN " . DB_PREFIX . "order_history ooh
                  ON osub.order_id = ooh.order_id
                WHERE osub.invoice_no > 0 
                  AND osub.buyer_invoice_id > 0
                  AND osub.order_status_id != 2
                  AND osub.order_status_id > 0 
                  AND osub.gst =$gst 
                  AND oo.franchise_id = 0 
                  AND osub.suborder_id = ooh.suborder_id ";

        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(oo.date_added) >=  DATE('" . $this->db->escape($data['filter_date_from']) . "')";
        }

        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(oo.date_added) <= DATE('" . $this->db->escape($data['filter_date_to']) . "')";
        }

        if (!empty($data['filter_invoice_date_from'])) {
            $sql .= " AND DATE(osub.invoice_date) >= DATE('" . $this->db->escape($data['filter_invoice_date_from']) . "')";
        }

        if (!empty($data['filter_invoice_date_to'])) {
            $sql .= " AND DATE(osub.invoice_date) <= DATE('" . $this->db->escape($data['filter_invoice_date_to']) . "')";
        }
        if (!empty($data['filter_customer_id'])) {
            $sql .= " AND oo.customer_id = '".(int) $data['filter_customer_id']."'";
        }
        
        $sql .= " GROUP BY ooh.suborder_id ORDER BY osub.invoice_date, osub.invoice_no ASC";
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

        // No ledger exists with this customer_id
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
                        ledger_name = '" . $this->db->escape($result->row['ledger_name']) . "'";
            if ($this->db->query($sql)) {
                return $result->row;
            }
        }
    }

    public function getOrderCashDiscount($order_ids_arr) {
        $sql = "SELECT oopt.order_id,
                     oav.suborder_id,
                     SUM(oav.value) as cash_discount
              FROM " . DB_PREFIX . "order_payment oopt INNER JOIN
                   " . DB_PREFIX . "advance_voucher oav ON oopt.payment_id = oav.payment_id INNER JOIN
                   " . DB_PREFIX . "suborder osub ON osub.order_id = oav.order_id
              WHERE oopt.order_id IN (" . implode(',', $order_ids_arr) . ") AND
                    oopt.payment_gateway IN ('cashback', 'coupon') AND
                    osub.suborder_id = oav.suborder_id AND
                    osub.buyer_invoice_id > 0 AND
                    osub.invoice_no > 0 AND
                    osub.order_status_id > 0 AND
                    osub.order_status_id != 2 AND
                    oopt.successfull = 1 AND
                    oav.status = 1 AND
                    oav.locked = 1
              GROUP BY oav.suborder_id";
        $result = $this->db->query($sql);
        $return_arr = array();
        if ($result->num_rows > 0) {
            foreach ($result->rows as $value) {
                $suborder_id = $value['suborder_id'];
                $return_arr[$suborder_id] = (float)$value['cash_discount'];
            }
        }
        return $return_arr;
    }

    

}
