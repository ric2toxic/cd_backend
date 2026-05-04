<?php
class ModelAccountPanelDebtors extends Model {
  

    public function getDebtors($data = array()) {

        $sql = "SELECT * FROM oc_ledger ol";

        //$sql .= " WHERE oo.order_id >= 20740 AND oo.order_id <= 20790";
        //$sql .= " WHERE oo.order_id = 20747";
        $sql .= " WHERE 1 = 1 ";
        
        $sql .= " AND ol.group_id = 10  ";

        if(!empty($data['filter_order_no'])){
            $sql .= " AND oo.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
        }
        if(!empty($data['filter_suborder_id'])){
            $sql .= " AND os.suborder_id LIKE'%" .$this->db->escape($data['filter_suborder_id']). "%'";
        }
        if(!empty($data['filter_date_from'])){
            $sql .= " AND oo.date_added >= '".$this->db->escape($data['filter_date_from'])."' ";
        }
        if(!empty($data['filter_date_to'])){
            $sql .= " AND oo.date_added <= '".$this->db->escape($data['filter_date_to'])."' ";
        }
        if(!empty($data['filter_tracking_no'])){
            $sql .= " AND os.tracking_no LIKE'%" .$this->db->escape($data['filter_tracking_no']). "%'";
        }
        if(!empty($data['filter_total_from'])){
            $sql .= " AND os.total >= ".(float)$data['filter_total_from']." ";
        }
        if(!empty($data['filter_total_to'])){
            $sql .= " AND os.total <= ".(float)$data['filter_total_to']." ";
        }
        if(!empty($data['filter_order_status'])){
            $sql .= " AND os.order_status_id = '".$this->db->escape($data['filter_order_status'])."' ";
        }
        if(!empty($data['filter_payment_mode'])){
            $sql .= " AND oop.payment_mode = '".$this->db->escape($data['filter_payment_mode'])."' ";
        }

        $sql .= " ORDER BY ol.ledger_name ASC ";
//echo $sql;die;
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
//echo $sql;die;
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } else {
          return array();
        }  
    }    

    public function getReceiptByLedgerId( $ledger_id ) {
// $order_no = '20170902840';
// $order_id = '20741';
// $order_payment_id = '15615';
// $amount = '13791.00';
/*
        $sql = "SELECT
                orsc.receipt_id,
                orsc.dated,
                orsc.ledger_id,
                orsc.amount,
                orsc.order_payment_id,
                orsc.order_id,
                orsc.order_no
              FROM
                oc_receipt_sub_csv orsc
              INNER JOIN
                oc_ledger ol ON orsc.ledger_id = ol.ledger_id
              WHERE
                ol.group_id = 10 AND ol.ledger_id = 763 AND orsc.delete_status = 0";
*/
        $sql = "SELECT
                  SUM(orsc.amount) AS amountRec
                FROM
                  oc_receipt_sub_csv orsc
                INNER JOIN oc_ledger ol ON orsc.ledger_id = ol.ledger_id
                WHERE
                  ol.group_id = 10 AND ol.ledger_id = '" . $this->db->escape($ledger_id) . "'
                GROUP BY orsc.ledger_id";

                //WHERE ol.group_id = 10 AND ol.ledger_id = 763

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            //return $query->rows;
            return $query->row['amountRec'];//die;
        } 

    }

    public function getPaymentByLedgerId( $ledger_id ) {
// $order_no = '20170902840';
// $order_id = '20741';
// $order_payment_id = '15615';
// $amount = '13791.00';

/*
        $sql = "SELECT
                  opsc.payment_id,
                  opsc.dated,
                  opsc.ledger_id,
                  opsc.amount,
                  opsc.order_payment_id,
                  opsc.order_id,
                  opsc.order_no
                FROM
                  oc_payment_sub_csv opsc
                INNER JOIN
                  oc_ledger ol ON opsc.ledger_id = ol.ledger_id
                WHERE
                  ol.group_id = 11 AND ol.ledger_id = 4468 AND opsc.delete_status = 0";
*/
        $sql = "SELECT
                  SUM(opsc.amount) AS amountPay
                FROM
                  oc_payment_sub_csv opsc
                INNER JOIN
                  oc_ledger ol ON opsc.ledger_id = ol.ledger_id
                WHERE
                  ol.group_id = 10 AND ol.ledger_id = '" . $this->db->escape($ledger_id) . "'
                GROUP BY
                  opsc.ledger_id";

                  //ol.group_id = 10 AND ol.ledger_id = 164
        //$sql .=" GROUP BY oc_payment_sub_csv.order_id";
//echo $sql;die;
        $query = $this->db->query($sql);
//print_r($query);die;

        if ( $query->num_rows ) {
            //return $query->rows;
            return $query->row['amountPay'];//die;
        } 
    }

    public function getSubOrdersByLedgerID($ledger_id) {
//$ledger_id = 4231;
        $sql = "SELECT
                  os.suborder_id,
                  os.courier_partner,
                  os.tracking_no,
                  os.order_status_id,
                  oo.order_id,
                  oo.order_no,
                  oo.date_added,
                  oo.payment_company,
                  
                  oo.firstname,
                  oo.lastname,
                  oo.customer_id,
                  ol.ledger_id,
                  oo.gst_number
                FROM
                  oc_suborder AS os
                INNER JOIN oc_order oo ON os.order_id = oo.order_id
                INNER JOIN oc_ledger ol ON oo.customer_id = ol.customer_id
                
                WHERE ol.ledger_id = '" . $this->db->escape($ledger_id) . "'
                AND os.order_status_id > 0 AND os.order_status_id !=2
                AND oo.franchise_id = 0 ";
                
                $sql .= " ORDER BY oo.order_id DESC ";

        $query = $this->db->query($sql);


        $arrayName = array();

        if ( $query->num_rows ) {
          foreach ($query->rows as $key => $subOrderDetail) {

            $arrayName[$subOrderDetail['suborder_id']] = $subOrderDetail;
            $arrayName[$subOrderDetail['suborder_id']]['invoiceAmt'] = AdvanceVoucherLib::getTotalSubOrderInvoiceAmount($this->db, $subOrderDetail['suborder_id']);
          }
        } 

        //return $query->rows;
        //echo "<pre>"; print_r($query->rows); die;
        //echo "<pre>"; print_r($arrayName); die;
        return $arrayName;

    }


}
