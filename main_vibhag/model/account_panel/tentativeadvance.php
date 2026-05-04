<?php
class ModelAccountPanelTentativeadvance extends Model {

    public function getTentativeAdvances($data = array()) {
//echo "<pre>";print_r($data);die;
        $sql = "SELECT
                  ota.tentative_advance_id,
                  ota.order_id,
                  oo.order_no,
                  ota.payment_mode,
                  ota.cheque_no,
                  ota.collection_date,
                  
                  ota.txn_id,
                  
                  ota.dated,
                  ota.amount,
                  ota.notes,
                  ota.order_payment_id,
                  ota.msgadmin,
                  ota.bank_deposited,
                  ota.bank_deposited_image,
                  ota.branch_name,
                  ota.staff_id,
                  oss.name AS staff_name,
                  oss.crm_user_id,
                  ota.date_created,
                  ota.staff_detail,
                  ota.confirm,
                  ota.confirm_user,
                  ota.transaction_status,
                  oop.merchant_txn_id
                FROM
                  oc_tentative_advance ota
                INNER JOIN oc_sales_staff oss ON ota.staff_id = oss.staff_id
                INNER JOIN oc_order oo ON ota.order_id = oo.order_id
                LEFT JOIN oc_order_payment oop ON ota.order_payment_id = oop.payment_id";

        $sql .= " WHERE 1 = 1 ";

        /*
        * this condition returns without franchise id orders
        */
        $sql .= " AND oo.franchise_id = 0 ";

        if(!empty($data['filter_order_no'])){

            $sql .= " AND oo.order_no LIKE'%" .$this->db->escape($data['filter_order_no']). "%' ";
        }

        if(!empty($data['filter_status'])){
          if($data['filter_status'] == 'all'){
            $sql .= " ";            
          }
          else
          {
            $sql .= " AND ota.transaction_status = '".$this->db->escape($data['filter_status'])."' ";
          }
        }
        if(!empty($data['filter_confirm'])){
          if($data['filter_confirm'] == '101'){
            $sql .= " ";            
          }
          else
          {
            $sql .= " AND ota.confirm = '".$this->db->escape($data['filter_confirm'])."' ";
          }
        }
        else
        {
            $sql .= " AND ota.confirm = 0 ";
        }

        if(!empty($data['filter_staff'])){
            $sql .= " AND ota.staff_id = '".$this->db->escape($data['filter_staff'])."' ";
        }

        if(!empty($data['filter_amount_from'])){
            $sql .= " AND ota.amount >= ".(float)$data['filter_amount_from']." ";
        }
        if(!empty($data['filter_amount_to'])){
            $sql .= " AND ota.amount <= ".(float)$data['filter_amount_to']." ";
        }




        if(!empty($data['filter_ref'])){
            $sql .= " AND (ota.cheque_no LIKE'%" .$this->db->escape($data['filter_ref']). "%' OR ota.txn_id LIKE'%" .$this->db->escape($data['filter_ref']). "%')";
        }
        //WHERE a.dated>='2016-02-02' AND a.dated<='2016-04-14'
        if(!empty($data['filter_date_from'])){
            $sql .= " AND ota.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
            //$sql .= " AND DATE(ota.deposit_date >= '".$this->db->escape($data['filter_date_from'])."' OR ota.txn_date >= '".$this->db->escape($data['filter_date_from'])."') ";
        }
        if(!empty($data['filter_date_to'])){
            $sql .= " AND ota.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
            //$sql .= " AND DATE(ota.deposit_date <= '".$this->db->escape($data['filter_date_to'])."' OR ota.txn_date <= '".$this->db->escape($data['filter_date_to'])."') ";
        }

        $sql .= " ORDER BY ota.dated DESC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                //$data['limit'] = 20;
                $data['limit'] = 30;
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

    public function getTentativeAdvanceCount($data = array()) {

        $sql = "SELECT COUNT(*) as num
                FROM
                  oc_tentative_advance ota
                INNER JOIN oc_sales_staff oss ON ota.staff_id = oss.staff_id
                INNER JOIN oc_order oo ON ota.order_id = oo.order_id
                LEFT JOIN oc_order_payment oop ON ota.order_payment_id = oop.payment_id";

        $sql .= " WHERE 1 = 1 ";
        /*
        * this condition returns without franchise id orders
        */
        $sql .= " AND oo.franchise_id = 0 ";

        if(!empty($data['filter_order_no'])){
            $sql .= " AND oo.order_no LIKE '%".$this->db->escape($data['filter_order_no'])."%' ";
        }

        if(!empty($data['filter_status'])){
          if($data['filter_status'] == 'all'){
            $sql .= " ";            
          }
          else
          {
            $sql .= " AND ota.transaction_status = '".$this->db->escape($data['filter_status'])."' ";
          }
        }
        if(!empty($data['filter_confirm'])){
          if($data['filter_confirm'] == '101'){
            $sql .= " ";            
          }
          else
          {
            $sql .= " AND ota.confirm = '".$this->db->escape($data['filter_confirm'])."' ";
          }
        }
        else
        {
            $sql .= " AND ota.confirm = 0 ";
        }

        if(!empty($data['filter_staff'])){
            $sql .= " AND ota.staff_id = '".$this->db->escape($data['filter_staff'])."' ";
        }
        
        if(!empty($data['filter_amount_from'])){
            $sql .= " AND ota.amount >= ".(float)$data['filter_amount_from']." ";
        }
        if(!empty($data['filter_amount_to'])){
            $sql .= " AND ota.amount <= ".(float)$data['filter_amount_to']." ";
        }
        //WHERE a.dated>='2016-02-02' AND a.dated<='2016-04-14'
        if(!empty($data['filter_date_from'])){
            $sql .= " AND ota.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
            //$sql .= " AND DATE(ota.deposit_date >= '".$this->db->escape($data['filter_date_from'])."' OR ota.txn_date >= '".$this->db->escape($data['filter_date_from'])."') ";
        }
        if(!empty($data['filter_date_to'])){
            $sql .= " AND ota.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
            //$sql .= " AND DATE(ota.deposit_date <= '".$this->db->escape($data['filter_date_to'])."' OR ota.txn_date <= '".$this->db->escape($data['filter_date_to'])."') ";
        }
                


                //$sql .="ORDER BY a.dated DESC ";
                //die($sql);

                //WHERE a.confirm = 0

        $query = $this->db->query($sql);

        //return (int)($query->num_rows);
        return $query->row['num'];

    }
    public function getOrders() {
        
      $sql = "SELECT order_id, order_no FROM " . DB_PREFIX . "order ORDER BY `order_id` DESC limit 25";

      $query = $this->db->query($sql);

      if ( $query->num_rows ) {
          return $query->rows;
      }
    }

    public function getStaffs() {
        
      $sql = "SELECT staff_id, name FROM " . DB_PREFIX . "sales_staff ORDER BY name ASC";

      $query = $this->db->query($sql);

      if ( $query->num_rows ) {
          return $query->rows;
      }
    }

    public function getStaff($staff_id) {

        $sql = "SELECT * FROM oc_sales_staff WHERE staff_id = '" . $this->db->escape($staff_id) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function insertTentativeAdvance( $filter_date_from, $order_id, $mode, $cheque_no, $cheque_date, $cheque_to_be_deposited, $txn_id, $amount, $notes, $staff_id, $datedCM, $staff_array ) {

        $sql = "INSERT INTO " . DB_PREFIX . "tentative_advance
                SET dated = '" . $this->db->escape($filter_date_from) . "',
                    order_id = '" . $this->db->escape($order_id) . "',
                    mode = '" . $this->db->escape($mode) . "',
                    cheque_no = '" . $this->db->escape($cheque_no) . "',
                    cheque_date = '" . $this->db->escape($cheque_date) . "',
                    cheque_to_be_deposited = '" . $this->db->escape($cheque_to_be_deposited) . "',
                    txn_id = '" . $this->db->escape($txn_id) . "',
                    amount = '" . $this->db->escape($amount) . "',
                    notes = '" . $this->db->escape($notes) . "',
                    staff_id = '" . $this->db->escape($staff_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    staff_detail = '" . $this->db->escape(serialize($staff_array)) . "'
                    ";                    

        $this->db->query($sql);
    }

    public function updateTentativeAdvanceForConfirmMsgAdminById( $confirm, $msgadmin, $pymtID, $tentative_advance_id ) {

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $confirm_date = date("Y-m-d H:i:s");
        $confirm_user_array = array(
                                'user_id' => $user_id,
                                'user_name' => $user_name,
                                'confirm_date' => $confirm_date
                              );

        $sql = "UPDATE " . DB_PREFIX . "tentative_advance
                 SET confirm = '". (int)$confirm ."',
                 msgadmin = '" . $this->db->escape($msgadmin) . "',
                 order_payment_id = '" . (int)$pymtID . "',
                 confirm_user = '" . $this->db->escape(serialize($confirm_user_array)) . "'   
                WHERE tentative_advance_id = '". (int)$tentative_advance_id ."'";

        $this->db->query($sql);

        return true;
    }

    public function updateOcOrderPayment( $pymtID, $merchant_txn_id ) {

        $sql = "UPDATE " . DB_PREFIX . "order_payment
                 SET merchant_txn_id = '" . $this->db->escape($merchant_txn_id) . "',
                 txn_status = 'CHEQUE_SUCCESS',
                 bank_transfer_mode = 'cheque_success'
                WHERE payment_id = '". (int)$pymtID ."'";

        $this->db->query($sql);

        return true;
    }

    public function getTentativeAdvancesByID($tentative_advance_id) {

        $sql = "SELECT
                  oc_tentative_advance.tentative_advance_id,
                  oc_tentative_advance.order_id,
                  oc_order.order_no,
                  oc_tentative_advance.payment_mode,
                  oc_tentative_advance.cheque_no,
                  oc_tentative_advance.collection_date,
                  
                  oc_tentative_advance.txn_id,
                  
                  oc_tentative_advance.dated,
                  oc_tentative_advance.amount,
                  oc_tentative_advance.notes,
                  oc_tentative_advance.order_payment_id,
                  oc_tentative_advance.msgadmin,
                  oc_tentative_advance.bank_deposited,
                  oc_tentative_advance.bank_deposited_image,
                  oc_tentative_advance.branch_name,
                  oc_tentative_advance.staff_id,
                  oc_sales_staff.name AS staff_name,
                  oc_sales_staff.crm_user_id,
                  oc_tentative_advance.date_created,
                  oc_tentative_advance.staff_detail,
                  oc_tentative_advance.confirm,
                  oc_tentative_advance.confirm_user
                FROM
                  oc_tentative_advance 
                INNER JOIN oc_sales_staff ON oc_tentative_advance.staff_id = oc_sales_staff.staff_id
                INNER JOIN oc_order ON oc_tentative_advance.order_id = oc_order.order_id
                
                WHERE oc_tentative_advance.tentative_advance_id = '" . (int)$tentative_advance_id . "' 
                AND oc_order.franchise_id = 0 ";         

        $query = $this->db->query($sql);

        return $query->row;

    }

    public function getOcOrderPaymentDetail($order_id, $amount, $successfull) {
/*
        $sql = "SELECT
                oop.payment_id,
                oop.order_id,
                oop.merchant_txn_id,
                oop.order_no,
                oop.txn_date_time,
                oop.payment_gateway,
                oop.amount,
                oop.successfull,
                oop.bank_transfer_mode
              FROM
                oc_order_payment oop
              WHERE
                oop.order_id = '" . $this->db->escape($order_id) . "' 
                AND oop.amount = '" . $this->db->escape($amount) . "'
                AND oop.successfull = '" . $this->db->escape($successfull) . "'";
*/
        $sql = "SELECT
                oop.payment_id,
                oop.order_id,
                oop.merchant_txn_id,
                oop.order_no,
                oop.txn_date_time,
                oop.payment_gateway,
                oop.amount,
                oop.successfull,
                oop.bank_transfer_mode
              FROM
                oc_order_payment oop
              LEFT JOIN
                oc_tentative_advance ota ON oop.payment_id = ota.order_payment_id
              WHERE
                oop.order_id = '" . $this->db->escape($order_id) . "' 
                AND oop.amount = '" . $this->db->escape($amount) . "'
                AND oop.successfull = '" . $this->db->escape($successfull) . "'
                AND ota.order_payment_id IS NULL ";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTentativeAdvancesByOrderID($order_id) {

        $sql = "SELECT
                  oc_tentative_advance.tentative_advance_id,
                  oc_tentative_advance.order_id,
                  oc_order.order_no,
                  oc_tentative_advance.payment_mode,
                  oc_tentative_advance.cheque_no,
                  oc_tentative_advance.collection_date,
                  
                  oc_tentative_advance.txn_id,
                  
                  oc_tentative_advance.dated,
                  oc_tentative_advance.amount,
                  oc_tentative_advance.notes,
                  oc_tentative_advance.order_payment_id,
                  oc_tentative_advance.msgadmin,
                  oc_tentative_advance.bank_deposited,
                  oc_tentative_advance.bank_deposited_image,
                  oc_tentative_advance.branch_name,
                  oc_tentative_advance.staff_id,
                  oc_sales_staff.name AS staff_name,
                  oc_sales_staff.crm_user_id,
                  oc_tentative_advance.date_created,
                  oc_tentative_advance.staff_detail,
                  oc_tentative_advance.confirm,
                  oc_tentative_advance.confirm_user,
                  oc_tentative_advance.transaction_status,
                  oc_order_payment.merchant_txn_id
                FROM
                  oc_tentative_advance 
                INNER JOIN oc_sales_staff ON oc_tentative_advance.staff_id = oc_sales_staff.staff_id
                INNER JOIN oc_order ON oc_tentative_advance.order_id = oc_order.order_id
                LEFT JOIN oc_order_payment ON oc_tentative_advance.order_payment_id = oc_order_payment.payment_id
                
                WHERE oc_tentative_advance.order_id = '" . (int)$order_id . "'
                      AND oc_tentative_advance.confirm = 1 

                      AND oc_order.franchise_id = 0 ";

        $query = $this->db->query($sql);

        return $query->rows;

    }



}
