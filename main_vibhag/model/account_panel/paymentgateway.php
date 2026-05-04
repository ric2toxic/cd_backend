<?php
class ModelAccountPanelPaymentgateway extends Model {
  

    public function getSubOrders($data = array()) {

        $sql = "SELECT
                  DISTINCT(oo.order_id),
                  oo.order_no,
                  oo.date_added,
                  oop.payment_gateway,
                  oo.total
                FROM
                  oc_order oo
                INNER JOIN oc_order_payment oop ON oo.order_id = oop.order_id ";

        $sql .= " WHERE 1 = 1 ";
        $sql .= " AND oop.successfull = '1' ";

        /*
        * this condition returns without franchise id orders
        */
        $sql .= " AND oo.franchise_id = 0 ";
        $sql .= " AND oop.payment_gateway NOT IN ('coupon','cashback') ";

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
        if(!empty($data['filter_total_from'])){
            $sql .= " AND oo.total >= ".(float)$data['filter_total_from']." ";
        }
        if(!empty($data['filter_total_to'])){
            $sql .= " AND oo.total <= ".(float)$data['filter_total_to']." ";
        }
        
        if(!empty($data['filter_payment_gateway'])){
          if($data['filter_payment_gateway'] == '101'){
            $sql .= " AND (oop.payment_gateway = 'citrus' OR oop.payment_gateway = 'paytm' OR oop.payment_gateway = 'razorpay') ";
          }
          else
          {
            $sql .= " AND oop.payment_gateway = '".$this->db->escape($data['filter_payment_gateway'])."' ";
          }
        }

        $sql .= " ORDER BY oo.date_added DESC ";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } else {
          return array();
        }  
    }

    public function getSubOrdersCount($data = array()) {      

        $sql = "SELECT
                  COUNT(DISTINCT(oo.order_id)) AS num
                FROM
                  oc_order oo
                INNER JOIN oc_order_payment oop ON oo.order_id = oop.order_id";

        $sql .= " WHERE 1 = 1 ";
        $sql .= " AND oop.successfull = '1' ";

        /*
        * this condition returns without franchise id orders
        */
        $sql .= " AND oo.franchise_id = 0 ";
        $sql .= " AND oop.payment_gateway NOT IN ('coupon','cashback') ";

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
        if(!empty($data['filter_total_from'])){
            $sql .= " AND oo.total >= ".(float)$data['filter_total_from']." ";
        }
        if(!empty($data['filter_total_to'])){
            $sql .= " AND oo.total <= ".(float)$data['filter_total_to']." ";
        }
        /*
        if(!empty($data['filter_order_status'])){
            $sql .= " AND os.order_status_id = '".$this->db->escape($data['filter_order_status'])."' ";
        }
        */
        // if(!empty($data['filter_payment_gateway'])){
        //     $sql .= " AND oop.payment_gateway = '".$this->db->escape($data['filter_payment_gateway'])."' ";
        // }
        if(!empty($data['filter_payment_gateway'])){
          if($data['filter_payment_gateway'] == '101'){
            $sql .= " AND (oop.payment_gateway = 'citrus' OR oop.payment_gateway = 'paytm' OR oop.payment_gateway = 'razorpay') ";
          }
          else
          {
            $sql .= " AND oop.payment_gateway = '".$this->db->escape($data['filter_payment_gateway'])."' ";
          }
        }

        $query = $this->db->query($sql);

        //return (int)($query->num_rows);
        return $query->row['num'];

    }

    public function getSubOrdersByOrderID($order_id, $payment_gateway) {

        $sql = "SELECT
                  oop.payment_id,
                  oop.merchant_txn_id,
                  oop.payment_mode,
                  oop.txn_date_time,
                  oop.payment_gateway,
                  oop.successfull,
                  oop.amount
                FROM
                  oc_order_payment oop";

        $sql .= " WHERE 1 = 1 ";

        $sql .= " AND oop.order_id = '" . $this->db->escape($order_id) . "'";
        $sql .= " AND oop.amount > 0 ";
        $sql .= " AND oop.successfull = 1 ";
        //$sql .= " AND (oop.rec_pay_id = 0 OR oop.rec_pay_id IS NULL) ";
        $sql .= " AND oop.payment_gateway = '".$this->db->escape($payment_gateway)."' ";
        $sql .= " ORDER BY oop.merchant_txn_id ASC ";

                // WHERE
                //   oop.amount > 0 AND oop.payment_gateway = 'citrus' AND oop.successfull = 1 AND(
                //     oop.rec_pay_id = 0 OR oop.rec_pay_id IS NULL
                //   )";

//echo $sql;die;
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } 
    }

    public function getReceiptByOrderId2( $order_id, $payment_gateway ) {//y

        $sql = "SELECT
                  orsc.receipt_id,
                  orsc.receipt_sub_id,
                  orsc.oc_receipt_sub_csv_id,
                  orsc.order_payment_id,
                  orsc.ref,
                  orsc.dated,
                  ol.ledger_name AS payment_gateway,
                  orsc.amount,
                  orscd.charges
                FROM
                  oc_receipt_sub_csv orsc
                INNER JOIN
                  oc_receipt_sub ors ON (orsc.receipt_id = ors.receipt_id
                           AND orsc.receipt_sub_id = ors.receipt_sub_id
                           AND orsc.delete_status = 0 
                           )
                INNER JOIN 
                  oc_receipt_sub_chargesdr orscd on orsc.oc_receipt_sub_csv_id = orscd.oc_receipt_sub_csv_id
                INNER JOIN
                  oc_ledger ol ON ors.ledger_id = ol.ledger_id";
        $sql .= " WHERE 1 = 1 ";
        $sql .= " AND orsc.order_id = '" . $this->db->escape($order_id) . "'";
        $sql .= " AND ol.ledger_name = '".$this->db->escape($payment_gateway)."' ";
//echo $sql;die;
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        }
    }
// 23064
// 22952
    public function getSubOrdersByOrderIDCSV($order_id, $payment_gateway) {//y xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

        $sql = "SELECT
                  oo.order_id,
                  oo.order_no,
                  oo.date_added,
                  oo.total,
                  oo.customer_id,
                  CONCAT(oo.firstname,
                  '-',
                  oo.lastname) AS customer_name,

                  oop.payment_id,
                  oop.merchant_txn_id,
                  oop.payment_mode,
                  oop.txn_date_time,
                  oop.payment_gateway,
                  oop.successfull,
                  oop.amount,
                  orsc.amount AS amount2,
                  orscd.charges
                FROM
                  oc_order oo
                INNER JOIN
                  oc_order_payment oop ON oo.order_id = oop.order_id
                LEFT JOIN
                  oc_receipt_sub_csv orsc ON(
                    oop.payment_id = orsc.order_payment_id AND oop.order_id = orsc.order_id AND orsc.delete_status = 0 
                  )
                  
                LEFT JOIN
                  oc_receipt_sub_chargesdr orscd ON(
                    oop.payment_id = orscd.order_payment_id AND oop.order_id = orscd.order_id AND orscd.delete_status = 0 
                  )
                  
                WHERE
                  1 = 1 
                  AND oop.order_id = '" . $this->db->escape($order_id) . "' 
                  AND oop.amount > 0 AND oop.successfull = 1 
                  AND oop.payment_gateway = '".$this->db->escape($payment_gateway)."'
                ORDER BY
                  oop.merchant_txn_id ASC ";
//echo $sql;die;

        $query = $this->db->query($sql);
//echo "<pre>";print_r($query);die;
        

        if ( $query->num_rows ) {
          return $query->rows;
        } 

    }




}
