<?php
class ModelAccountPanelCod extends Model {
  

    public function getSubOrders($data = array()) {

        $sql = "SELECT
                  DISTINCT(oo.order_id),
                  oo.order_no,
                  oo.date_added,
                  oop.payment_gateway,
                  os.courier_partner,
                  oo.total,
                  oo.customer_id,
                  CONCAT(oo.firstname,'-',oo.lastname) AS customer_name
                FROM
                  oc_order oo
                INNER JOIN oc_suborder os ON oo.order_id = os.order_id
                INNER JOIN oc_order_payment oop ON oo.order_id = oop.order_id ";

        $sql .= " WHERE 1 = 1 ";

        $sql .= " AND oop.payment_gateway IN (".COD_PAYMENT_GATEWAYS.") ";
        $sql .= " AND os.order_status_id = 15";
        /*
        * this condition returns without franchise id orders
        */
        $sql .= " AND oo.franchise_id = 0 ";
        $sql .= " AND oop.payment_gateway NOT IN ('coupon','cashback') ";

        if(!empty($data['filter_order_no'])){
            $sql .= " AND oo.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
        }
        if(!empty($data['filter_suborder_id'])){
            $sql .= " AND os.suborder_id LIKE'%" .$this->db->escape($data['filter_suborder_id']). "%'";
        }
        if(!empty($data['filter_date_from'])){
            $newfromdate = date("Y-m-d H:i:s", strtotime($data['filter_date_from']));
            $sql .= " AND oo.date_added >= '". $newfromdate ."' ";
            //$sql .= " AND oo.date_added >= '". $this->db->escape($data['filter_date_from']) ."' ";
        }
        if(!empty($data['filter_date_to'])){
            $newtodate = date("Y-m-d 23:59:59", strtotime($data['filter_date_to']));
            $sql .= " AND oo.date_added <= '". $newtodate ."' ";
            //$sql .= " AND oo.date_added <= '". $this->db->escape($data['filter_date_to']) ."' ";
        }
        if(!empty($data['filter_tracking_no'])){
            $sql .= " AND os.tracking_no LIKE'%" .$this->db->escape($data['filter_tracking_no']). "%'";
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
        if(!empty($data['filter_cod'])){
            $sql .= " AND oop.payment_gateway = '".$this->db->escape($data['filter_cod'])."' ";
        }

        $sql .= " ORDER BY oo.order_id DESC ";
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
                INNER JOIN oc_suborder os ON oo.order_id = os.order_id
                INNER JOIN oc_order_payment oop ON oo.order_id = oop.order_id";

        $sql .= " WHERE 1 = 1 ";

        $sql .= " AND oop.payment_gateway IN (".COD_PAYMENT_GATEWAYS.") ";
        $sql .= " AND os.order_status_id = 15";
        
        /*
        * this condition returns without franchise id orders
        */
        $sql .= " AND oo.franchise_id = 0 ";
        $sql .= " AND oop.payment_gateway NOT IN ('coupon','cashback') ";

        if(!empty($data['filter_order_no'])){
            $sql .= " AND oo.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
        }
        if(!empty($data['filter_suborder_id'])){
            $sql .= " AND os.suborder_id LIKE'%" .$this->db->escape($data['filter_suborder_id']). "%'";
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
            $sql .= " AND os.tracking_no LIKE'%" .$this->db->escape($data['filter_tracking_no']). "%'";
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
        if(!empty($data['filter_cod'])){
            $sql .= " AND oop.payment_gateway = '".$this->db->escape($data['filter_cod'])."' ";
        }

        $query = $this->db->query($sql);

        return $query->row['num'];

    }

    public function getSubOrdersByOrderID($order_id, $payment_gateway) {//y

        $sql = "SELECT
                  os.suborder_id,
                  os.courier_partner,
                  os.tracking_no,
                  os.order_status_id,
                  oos.name,
                  oo.order_id,
                  oo.order_no,
                  oo.date_added,
                  oo.payment_company,
                  
                  oo.firstname,
                  oo.lastname,
                  oo.customer_id,
                  oo.gst_number
                FROM
                  oc_suborder AS os
                INNER JOIN oc_order oo ON os.order_id = oo.order_id
                INNER JOIN oc_order_status oos ON os.order_status_id = oos.order_status_id";

        $sql .= " WHERE 1 = 1 ";

        $sql .= " AND oo.order_id = '" . $this->db->escape($order_id) . "'";
        $sql .= " AND oo.payment_code = 'cod'";
        $sql .= " AND os.order_status_id = 15";
        $sql .= " AND oos.language_id = 1 
                  AND oo.franchise_id = 0 ";
                
                if($payment_gateway == 'gati_ltd'){
                    $sql .= " AND os.courier_partner = 'Gati'";
                }
                else if($payment_gateway == 'gati_kwe'){
                    $sql .= " AND os.courier_partner = 'Gati'";
                }
                else if($payment_gateway == 'fedex'){
                    $sql .= " AND os.courier_partner = 'FeDex'";
                }

                $sql .= " ORDER BY os.tracking_no ASC ";
//echo $sql;die;
        $query = $this->db->query($sql);

        $arrayName = array();
        

        if ( $query->num_rows ) {
          
          
          foreach ($query->rows as $key => $subOrderDetail) {
            $arrayName[$subOrderDetail['suborder_id']] = $subOrderDetail;
            //$arrayName[$subOrderDetail['suborder_id']]['invoiceAmt'] = AdvanceVoucherLib::getTotalSubOrderInvoiceAmount($this->db, $subOrderDetail['suborder_id']);
            $buyer_invoice = new BuyerInvoice($this);
            $arrayName[$subOrderDetail['suborder_id']]['net_receivable'] = (float)$buyer_invoice->getTotals($subOrderDetail['order_id'], $subOrderDetail['suborder_id'])['net_amount']['value'];
          }
        } 

        return $arrayName;

    }

    public function getReceiptByOrderId( $order_id, $payment_gateway ) {

        $sql = "SELECT 
                    oo.order_id,
                    oo.order_no,
                    oo.date_added,
                    oo.firstname,
                    oo.lastname,
                    oo.payment_company,
                    oo.customer_id,
                    oo.gst_number,
                    oo.total,
                    oop.payment_id,
                    oop.merchant_txn_id,
                    oop.payment_mode,
                    oop.txn_date_time,
                    oop.payment_gateway,
                    oop.successfull,
                    oop.amount

                FROM ". DB_PREFIX ."order as oo
                INNER JOIN oc_order_payment oop ON oo.order_id = oop.order_id
                WHERE oo.order_id = '" . $this->db->escape($order_id) . "'
                AND oop.successfull = '1' 
                AND oo.franchise_id = 0 
                AND oop.payment_gateway NOT IN ('coupon','cashback') 
                ";

          $sql .= " AND oop.payment_gateway IN (".COD_PAYMENT_GATEWAYS.") ";

                if($payment_gateway == 'gati_ltd'){
                    $sql .= " AND (oop.payment_gateway = 'gati_ltd' OR oop.payment_gateway = 'gati_kwe')";
                }
                else if($payment_gateway == 'gati_kwe'){
                    $sql .= " AND (oop.payment_gateway = 'gati_ltd' OR oop.payment_gateway = 'gati_kwe')";
                }
                else if($payment_gateway == 'fedex'){
                    $sql .= " AND oop.payment_gateway = 'FeDex'";
                }

          /*
          if(!empty($data['filter_cod'])){
              $sql .= " AND oop.payment_gateway = '".$this->db->escape($data['filter_cod'])."' ";
          }
          */
          $sql .= " ORDER BY oop.merchant_txn_id ASC ";

//echo $sql;die;
        $query = $this->db->query($sql);

        

        if ( $query->num_rows ) {
            return $query->rows;
            //return $query->row['amountRec'];//die;
        } 
    }

    public function getSubOrdersByOrderIDCSV($order_id, $payment_gateway) {//y

        $sql = "SELECT 
                  oo.order_id,
                  oo.order_no,
                  oo.date_added,
                  oo.total,
                  oo.customer_id,
                  CONCAT(oo.firstname,
                  '-',
                  oo.lastname) AS customer_name,
                  os.courier_partner,
                  os.suborder_id,
                  os.tracking_no,
                  os.order_status_id,
                  oos.name,
                  oop.txn_date_time,
                  oop.merchant_txn_id,
                  oop.payment_gateway,
                  oop.successfull,
                  oop.amount
                FROM
                  oc_order oo
                INNER JOIN
                  oc_suborder os ON oo.order_id = os.order_id
                LEFT JOIN
                oc_order_payment oop ON os.tracking_no = oop.merchant_txn_id
                INNER JOIN
                  oc_order_status oos ON os.order_status_id = oos.order_status_id";
        $sql .= " WHERE 1 = 1 ";

        $sql .= " AND oo.order_id = '" . $this->db->escape($order_id) . "'";
        $sql .= " AND oo.payment_code = 'cod'";
        $sql .= " AND os.order_status_id = 15";
        $sql .= " AND oos.language_id = 1 
                  AND oo.franchise_id = 0 ";

                if($payment_gateway == 'gati_ltd'){
                    $sql .= " AND os.courier_partner = 'Gati'";
                }
                else if($payment_gateway == 'gati_kwe'){
                    $sql .= " AND os.courier_partner = 'Gati'";
                }
                else if($payment_gateway == 'fedex'){
                    $sql .= " AND os.courier_partner = 'FeDex'";
                }

                $sql .= " ORDER BY os.tracking_no ASC ";


        $query = $this->db->query($sql);

        $arrayName = array();
        

        if ( $query->num_rows ) {
          
          
          foreach ($query->rows as $key => $subOrderDetail) {
            $arrayName[$subOrderDetail['suborder_id']] = $subOrderDetail;
            //$arrayName[$subOrderDetail['suborder_id']]['invoiceAmt'] = AdvanceVoucherLib::getTotalSubOrderInvoiceAmount($this->db, $subOrderDetail['suborder_id']);
            $buyer_invoice = new BuyerInvoice($this);
            $arrayName[$subOrderDetail['suborder_id']]['net_receivable'] = (float)$buyer_invoice->getTotals($subOrderDetail['order_id'], $subOrderDetail['suborder_id'])['net_amount']['value'];
          }
        } 

        return $arrayName;


    }

    public function getCods() {
      
        $sql = "SELECT
                  DISTINCT(oop.payment_gateway)
                FROM
                  oc_order_payment oop
                WHERE oop.payment_gateway IN (".COD_PAYMENT_GATEWAYS.") ";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        }
    }


}
