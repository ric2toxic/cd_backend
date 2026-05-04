<?php
class ModelAccountPanelOrderpayment extends Model {
  
    /**
     * Public method to get Order wise listing for order receipt
     * @param: array $filter_data
     * @return: array
     * @author: Nishu, Jan 2019
    */
    public function getOrdersForOrderReceiptReport(array $data){
        $sql = "
                SELECT SQL_CALC_FOUND_ROWS
                    oo.order_id,
                    oo.order_no,
                    oo.date_added AS order_date,
                    oo.total,
                    oo.payment_code,
                    oo.customer_id,
                    CONCAT(oo.firstname,'-',oo.lastname) AS customer_name
                FROM
                    ".DB_PREFIX."order AS oo
                INNER JOIN
                    ".DB_PREFIX."suborder AS so ON oo.order_id = so.order_id
                LEFT JOIN
                    ".DB_PREFIX."order_payment AS op ON op.order_id = oo.order_id 
                    AND op.successfull = 1
                WHERE
                    oo.franchise_id = 0 
                    AND oo.store_id IN (" . WSB_STORES_ID . ")
               ";

        if(!empty($data['filter_order_no'])){
            $sql .= " AND oo.order_no LIKE'%" .$this->db->escape($data['filter_order_no']). "%'";
        }
        if(!empty($data['filter_date_from'])){
            $newfromdate = date("Y-m-d H:i:s", strtotime($data['filter_date_from']));
            $sql .= " AND oo.date_added >= '". $newfromdate ."' ";
        }
        if(!empty($data['filter_date_to'])){
            $newtodate = date("Y-m-d 23:59:59", strtotime($data['filter_date_to']));
            $sql .= " AND oo.date_added <= '". $newtodate ."' ";
        }
        if(!empty($data['filter_tracking_no'])){
            $sql .= " AND so.tracking_no LIKE'%" .$this->db->escape($data['filter_tracking_no']). "%'";
        }
        if(!empty($data['filter_total_from'])){
            $sql .= " AND oo.total >= ".(float)$data['filter_total_from']." ";
        }
        if(!empty($data['filter_total_to'])){
            $sql .= " AND oo.total <= ".(float)$data['filter_total_to']." ";
        }
        if(!empty($data['filter_order_status'])){
            $sql .= " AND so.order_status_id = '".$this->db->escape($data['filter_order_status'])."' ";
        }
        if(!empty($data['filter_customer_id'])){
            $sql .= " AND oo.customer_id = '".(int)$data['filter_customer_id']."' ";
        }
        if (!empty($data['filter_payment_code'])) {
            $sql .= " AND oo.payment_code LIKE '" . $this->db->escape($data['filter_payment_code']) . "'";
        }
        if (
            !empty($data['filter_payment_type']) && 
            $data['filter_payment_type'] == 'receipt'
        ) {
            $sql .= " AND op.amount > 0 ";
        }else if (
            !empty($data['filter_payment_type']) && 
            $data['filter_payment_type'] == 'payment'
        ) {
            $sql .= " AND op.amount < 0 ";
        }

        if (
            !empty($data['filter_verified']) && 
            $data['filter_verified'] == 'verified'
        ) {
            $sql .= " AND op.rec_pay_id > 0 ";
        }else if (
            !empty($data['filter_verified']) && 
            $data['filter_verified'] == 'unverified'
        ) {
            $sql .= " AND ( op.rec_pay_id <= 0 OR op.rec_pay_id IS NULL ) ";
        }

        $sql .= " GROUP BY oo.order_id ";

        $sql .= " ORDER BY oo.order_id DESC ";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            if ($data['limit'] >= 1) {
                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }
        }

        $query = $this->db->query($sql);

        //To get total number of rows for pagination
        $count_sql   = "SELECT FOUND_ROWS() AS overall_count";
        $count_query = $this->db->query($count_sql);

        $all_data = array();
        
        $all_data['data']        = array();
        $all_data['total_count'] = (int)$count_query->row['overall_count'];
        if($query->num_rows > 0){
            $rows = array_combine(
                      array_column($query->rows, 'order_id'),
                      $query->rows);
            $all_data['data'] = $rows;
        }
        
        return $all_data;
    }

}
