<?php

class ModelAccountsInventoryreports extends Model {

    public function getOnlineInventories($data = array(), $is_ajax = false) {
        $result = array();
        if (empty($data['filter_date'])) {
            return $result;
        }
        $sql1 = "(SELECT ";
        if ($is_ajax) {
            $sql1 .= "SUM(oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece) AS total_amount,
                    SUM((ROUND(oop.transfer_price_per_piece/(1+(oop.seller_input_tax/100)),2) * oop.quantity * oop.piece_in_set)) AS total_taxable_value";
        } else {
            $sql1 .= " oo.order_no,
                    os.suborder_id,
                    oms.nickname,
                    oop.product_id,
                    CONCAT(osi.seller_invoice_prefix,
                            osi.seller_invoice_no) AS seller_or_credit_inv_no,
                    osi.date_added AS seller_or_credit_inv_date,
                    oop.model AS wsb_product_code,
                    oop.seller_sku,
                    (oop.quantity * oop.piece_in_set) AS total_pieces,
                    oop.transfer_price_per_piece,
                    oop.seller_input_tax AS input_tax_rate,
                    oop.quantity * oop.piece_in_set * oop.transfer_price_per_piece AS amount,
                    (ROUND(oop.transfer_price_per_piece/(1+(oop.seller_input_tax/100)),2) * oop.quantity * oop.piece_in_set) AS taxable_value";
        }
        $sql1 .= "
                    
                FROM
                    oc_seller_invoice osi
                        INNER JOIN
                    oc_order_product oop ON osi.seller_invoice_id = oop.seller_invoice_id
                        INNER JOIN
                    oc_suborder os ON oop.order_id = os.order_id
                        INNER JOIN
                    oc_order oo ON oo.order_id = oop.order_id
                        INNER JOIN
                    oc_ms_seller oms ON oms.seller_id = oop.seller_id
                WHERE
                    oop.suborder_id = os.suborder_id
                        AND os.order_status_id > 0
                        AND os.order_status_id != 2
                        AND DATE(osi.date_added) <= DATE('" . $this->db->escape($data['filter_date']) . "')
                        AND oms.seller_invoice_generate = 1
                        AND
                         (
                            /* Buyer invoice is generated but at a later date */
                            (
                                oop.buyer_invoice_id = os.buyer_invoice_id AND 
                                os.buyer_invoice_id > 0 AND 
                                DATE(os.invoice_date) > DATE('" . $this->db->escape($data['filter_date']) . "')
                            )
                                OR
                            /* Buyer invoice is not at all generated AND 
                                a Debit note does not exist on or before the given date */
                            (
                                oop.buyer_invoice_id = 0 OR 
                                oop.buyer_invoice_id IS NULL
                            )
                         )
                        AND NOT EXISTS( SELECT 
                            1
                        FROM
                            oc_seller_debit_note osdn1
                                INNER JOIN
                            oc_return ort1 ON ort1.debit_note_id = osdn1.debit_note_id
                                INNER JOIN
                            oc_order_product oop1 ON oop1.order_product_id = ort1.order_product_id
                        WHERE
                            osdn1.debit_note_status = 1
                                AND osdn1.debit_note_no > 0
                                AND DATE(osdn1.date_added) <= DATE('" . $this->db->escape($data['filter_date']) . "')
                                AND (oop1.buyer_invoice_id = 0
                                OR oop1.buyer_invoice_id IS NULL)
                                AND oop1.order_product_id = oop.order_product_id))";

        $sql_union = " UNION ";

        $sql2 = "(SELECT ";
        if ($is_ajax) {
            $sql2 = "
                        SUM(ocr2.quantity * oop.transfer_price_per_piece) AS total_amount,
                        SUM((ROUND(oop.transfer_price_per_piece/(1+(oop.seller_input_tax/100)),2) * ocr2.quantity)) AS total_taxable_value";
        } else {
            $sql2 = "
                        oo.order_no,
                        oop.suborder_id,
                        oms.nickname,
                        oop.product_id,
                        CONCAT(ocn.credit_note_prefix,
                                ocn.credit_note_no) AS seller_or_credit_inv_no,
                        ocn.date_added AS seller_or_credit_inv_date,
                        oop.model AS wsb_product_code,
                        oop.seller_sku,
                        ocr2.quantity AS total_qty,
                        oop.transfer_price_per_piece,
                        oop.seller_input_tax AS input_tax_rate,
                        ocr2.quantity * oop.transfer_price_per_piece AS amount,
                        (ROUND(oop.transfer_price_per_piece/(1+(oop.seller_input_tax/100)),2) * ocr2.quantity) AS taxable_value";
        }

        $sql2 .= "  FROM
                        oc_return ocr2
                            INNER JOIN
                        (SELECT 
                            MAX(return_id) AS max_return_id,
                                MAX(ocr.debit_note_id) AS debit_note,
                                MAX(ocr.credit_note_id) AS credit_note
                        FROM
                            oc_return ocr
                        GROUP BY ocr.order_product_id , ocr.master_return_id) AS ocr1 ON ocr1.max_return_id = ocr2.return_id
                            AND ocr2.active_row = 1
                            INNER JOIN
                        oc_credit_note ocn ON ocn.credit_note_id = ocr1.credit_note
                            AND ocn.credit_note_status = 1
                            LEFT JOIN
                        oc_seller_debit_note osdn ON osdn.debit_note_id = ocr1.debit_note
                            AND osdn.debit_note_status = 1
                            INNER JOIN
                        oc_order_product oop ON oop.order_product_id = ocr2.order_product_id
                            INNER JOIN
                        oc_ms_seller oms ON oop.seller_id = oms.seller_id
                            INNER JOIN
                        oc_order oo ON oo.order_id = oop.order_id
                    WHERE
                        DATE(ocn.date_added) <= DATE('" . $this->db->escape($data['filter_date']) . "')
                            AND (osdn.debit_note_id IS NULL
                            OR DATE(osdn.date_added) > DATE('" . $this->db->escape($data['filter_date']) . "')
                            OR ocr2.return_action_id != 119))";
        /*
         * Commented for now as return is not including for online inventory report
         */
//        $sql = $sql1 . $sql_union . $sql2;
        $sql = $sql1;
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            if($is_ajax) {
                $result = $query->row;
            } else {
                $result = $query->rows;
            }
            
        }
        return $result;
    }

}
