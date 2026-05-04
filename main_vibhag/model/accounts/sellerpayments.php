<?php
class ModelAccountsSellerpayments extends Model
{
    public function getSellerInvoicePayment() {
        $sql = 'SELECT DISTINCT(sn.seller_id) 
                FROM `oc_seller_invoice` AS sn 
                WHERE date(sn.date_added) <= CURDATE() - INTERVAL 2 DAY 
                AND sn.payment_done = 0';
        $result = $this->db->query($sql)->rows;
        return $result;
    }

    public function getSellerDebitNotePayment() {
        $sql = 'SELECT sdn.return_ids, 
                sdn.order_id, 
                sdn.suborder_id, 
                sdn.seller_id, 
                sdn.debit_note_prefix, 
                sdn.debit_note_no, 
                sdn.debit_note_status, 
                sdn.date_added, 
                sdn.user, 
                sdn.payment_done, 
                sdn.payment_mode, 
                sdn.payment_date 
                FROM `oc_seller_debit_note` AS sdn
                INNER JOIN `oc_order` AS o ON (sdn.order_id = o.order_id)
                WHERE date(sdn.date_added) <= CURDATE() - INTERVAL 2 DAY 
                AND o.order_status_id = 15 
                AND sdn.payment_done = 0 
                AND o.franchise_id = 0 
                GROUP BY sdn.seller_id';
        $result = $this->db->query($sql);
        return $result;
    }
}
