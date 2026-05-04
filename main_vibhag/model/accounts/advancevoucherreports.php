<?php

class ModelAccountsAdvancevoucherreports extends Model {

    public function getAdvanceDetails($data = array()) {

        $sql = "SELECT oav.advance_voucher_id, 
                oav.order_id, 
                oav.suborder_id, 
                CONCAT( oav.advance_voucher_prefix,'_', oav.advance_voucher_no ) AS advance_voucher_no,
                oav.advance_voucher_date, 
                oav.locked, 
                oav.payment_id,
                oop.payment_gateway,
                oop.merchant_txn_id,
                oo.order_id,
                oo.order_no,
                oo.customer_id,
                oo.payment_company,
                oo.payment_city,
                oo.payment_zone,
                oo.gst_number,
                oo.payment_custom_field,
                oo.date_added AS order_date,
                oo.payment_zone_id,
                CONCAT( oo.firstname, ' ', oo.lastname ) AS customer_name
        FROM " . DB_PREFIX . "advance_voucher oav INNER JOIN 
             " . DB_PREFIX . "order_payment oop on oav.payment_id = oop.payment_id INNER JOIN 
             " . DB_PREFIX . "order oo on oav.order_id=oo.order_id INNER JOIN
             " . DB_PREFIX . "suborder os on oav.order_id=os.order_id
        WHERE oav.status=1 AND
              oav.suborder_id=os.suborder_id AND
              os.order_status_id > 0 AND
              os.order_status_id !=2 
              AND oo.franchise_id = 0 ";

        if (!empty($data['filter_advance_voucher_check'])) {
            $sql .= " AND (DATE(os.invoice_date) > DATE('" . $this->db->escape($data['filter_advance_voucher_date_to']) . "') OR
                            os.invoice_no = 0) ";
        }

        if (!empty($data['filter_advance_voucher_date_from'])) {
            $sql .= " AND DATE(oav.advance_voucher_date) >=  DATE('" . $this->db->escape($data['filter_advance_voucher_date_from']) . "') ";
        }

        if (!empty($data['filter_advance_voucher_date_to'])) {
            $sql .= " AND DATE(oav.advance_voucher_date) <=  DATE('" . $this->db->escape($data['filter_advance_voucher_date_to']) . "')";
        }

        $sql .= " ORDER BY oav.advance_voucher_date";
//        echo $sql;die;
        $query = $this->db->query($sql);
        return $query->rows;
    }

}
