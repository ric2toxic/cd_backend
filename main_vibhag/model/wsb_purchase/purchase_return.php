<?php
class ModelWsbPurchasePurchaseReturn extends Model {
    /**
     * Public Method to get all product details against wsb_purchase_id
     * @param: $wsb_purchase_id Integer
     * @return: $data Array
     * @author: Nishu, Jan 2018
    */
    public function getWSBPurchaseProductDetails($wsb_purchase_id){
        $data = array();
        if(empty($wsb_purchase_id)){
            return $data;
        }
        $sql = "
               SELECT
                   ms.purchase_firm_id AS seller_purchase_firm,
                   op.model,
                   op.quantity as product_qty,
                   op.piece_in_set,
                   wp.purchase_id,
                   wp.purchase_firm_id,
                   wp.purchase_firm_meta,
                   wp.invoice_no,
                   wpb.breakup_id,
                   wpb.transfer_price_per_piece,
                   wpb.sku,
                   wpb.product_id,
                   wpb.pieces AS total_qty,
                   CONCAT(ms.company,'( ',ms.city,' )' ) as seller_firm,
                   SUM(IF(wsb_return.debit_note_status =1 , wsb_return.quantity, 0)) as dn_qty,
                   ms.nickname,
                   ms.company
               FROM
                   ".DB_PREFIX."wsb_purchase AS wp
                       INNER JOIN
                   ".DB_PREFIX."wsb_purchase_breakup AS wpb ON wp.purchase_id = wpb.purchase_id
                       INNER JOIN
                   ".DB_PREFIX."product AS op ON op.product_id = wpb.product_id
                       INNER JOIN
                   ".DB_PREFIX."ms_product AS msp ON msp.product_id = op.product_id
                       INNER JOIN
                   ".DB_PREFIX."ms_seller AS ms ON ms.seller_id = msp.seller_id
                       LEFT JOIN
                   (
                     SELECT
                         wprb.quantity,
                         wprb.product_id,
                         wprb.purchase_id,
                         wprb.transfer_price_per_piece,
                         wpr.debit_note_status
                     FROM
                         ".DB_PREFIX."wsb_purchase_return_breakup as wprb
                     INNER JOIN
                         ".DB_PREFIX."wsb_purchase_return as wpr ON wprb.debit_note_id = wpr.debit_note_id
                   ) AS wsb_return ON wsb_return.purchase_id = wp.purchase_id 
                   AND wsb_return.product_id = wpb.product_id 
                   AND wsb_return.transfer_price_per_piece = wpb.transfer_price_per_piece
               WHERE
                   wp.purchase_id = '". (int)$wsb_purchase_id ."'
               GROUP BY
                   wp.purchase_id, wpb.breakup_id
               ORDER BY
                  total_qty DESC, dn_qty DESC
               ";
        
        $result = $this->db->query($sql);
        if($result->num_rows > 0){
            $data = $result->rows;
        }
        return $data;
    } //End of getWSBPurchaseProductDetails()

}
