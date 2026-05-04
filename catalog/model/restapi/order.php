<?php

class ModelRestapiOrder extends Model {

    public function getSuborderDetails( int $order_id, string $suborder_id ) {
        $selector = array('order' => array(),
            'suborder' => array(),
        );
        $order_info = OrderInfo::getOrderInfo($this->db, $order_id, $suborder_id, $selector);
        return $order_info;
    }

    public function getOrderHistories( int $order_id, string $suborder_id ) {

        $query = $this->db->query("SELECT oh.date_added,
                                          os.name AS status,
                                          oh.comment,
                                          oh.notify_email,
                                          oh.notify_sms,
                                          oh.notes,
                                          oh.user
                                   FROM " . DB_PREFIX . "order_history oh LEFT JOIN 
                                        " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id
                                   WHERE oh.order_id = '" . (int) $order_id . "' AND 
                                         oh.suborder_id = '" . $this->db->escape($suborder_id) . "' AND
                                         os.language_id = 1
                                   ORDER BY oh.date_added ASC");

        return $query->rows;
    }

}
