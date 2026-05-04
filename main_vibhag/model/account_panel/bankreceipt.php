<?php
class ModelAccountPanelBankreceipt extends Model {

    public function getBankReceipts($data = array()) {

        $sql = "SELECT a.receipt_id, a.dated, a.ledger_id, a.ledger_name, a.amount, a.mode, a.reference, a.no_of_order, a.narration, a.confirm, a.ledger_id2, a.group_id
                FROM
                (
                SELECT
                  oc_receipt.receipt_id,
                  oc_receipt.dated,
                  oc_receipt.ledger_id,
                  oc_ledger.ledger_name,
                  oc_receipt.amount,
                  oc_receipt.mode,
                  oc_receipt.reference,
                  oc_receipt.no_of_order,
                  oc_receipt.narration,
                  oc_receipt.confirm,
                  oc_receipt_sub.ledger_id AS ledger_id2,
                  oc_receipt_sub.group_id AS group_id
                FROM
                  oc_receipt
                INNER JOIN 
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN 
                  oc_ledger ON oc_receipt.ledger_id = oc_ledger.ledger_id
                WHERE oc_receipt_sub.delete_status = 0

                UNION ALL

                SELECT
                  oc_receipt.receipt_id,
                  oc_receipt.dated,
                  oc_receipt.ledger_id,
                  oc_ledger.ledger_name,
                  oc_receipt.amount,
                  oc_receipt.mode,
                  oc_receipt.reference, 
                  oc_receipt.no_of_order,                 
                  oc_receipt.narration,
                  0 AS confirm,
                  0 AS ledger_id2,
                  0 AS group_id
                FROM
                  oc_receipt 
                INNER JOIN oc_ledger ON oc_receipt.ledger_id = oc_ledger.ledger_id

                WHERE NOT EXISTS (SELECT * 
                                  FROM  oc_receipt_sub
                                  WHERE oc_receipt_sub.receipt_id = oc_receipt.receipt_id)
                )a ";
                
                $sql .= "WHERE 1 = 1 ";
                if(!empty($data['filter_ref'])){
                    $sql .= " AND a.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }
                
                //WHERE a.dated>='2016-02-02' AND a.dated<='2016-04-14'
                if(!empty($data['filter_date_from'])){
                    $sql .= " AND a.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND a.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_amount_from'])){
                    $sql .= " AND a.amount >= ".(float)$data['filter_amount_from']." ";
                }
                if(!empty($data['filter_amount_to'])){
                    $sql .= " AND a.amount <= ".(float)$data['filter_amount_to']." ";
                }
                if(!empty($data['filter_bank'])){
                    $sql .= " AND a.ledger_id = '".$this->db->escape($data['filter_bank'])."' ";
                }
                if(!empty($data['filter_inflow'])){
                    $sql .= " AND a.ledger_id2 = '".$this->db->escape($data['filter_inflow'])."' ";
                    //$sql .= " ";
                }
                if(!empty($data['filter_status'])){
                  if($data['filter_status'] == '101'){
                    $sql .= " ";            
                  }
                  else
                  {
                    $sql .= " AND a.ledger_id2 != 0 ";
                  }
                }
                else
                {
                    $sql .= " AND a.ledger_id2 = 0 ";
                }


                if(!empty($data['filter_order'])){
                    $sql .= " ORDER BY a.dated ".$this->db->escape($data['filter_order'])."";
                }

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

    public function getReceiptCount($data = array()) {

        $sql = "SELECT COUNT(*) as num
                FROM
                (
                SELECT 
                    a.receipt_id,
                    a.dated,
                    a.ledger_id,
                    a.ledger_name,
                    a.amount,
                    a.mode,
                    a.reference,
                    a.no_of_order,
                    a.narration,
                    a.confirm,
                    a.ledger_id2,
                    a.group_id
                FROM
                (
                SELECT
                    oc_receipt.receipt_id,
                    oc_receipt.dated,
                    oc_receipt.ledger_id,
                    oc_ledger.ledger_name,
                    oc_receipt.amount,
                    oc_receipt.mode,
                    oc_receipt.reference,
                    oc_receipt.no_of_order,
                    oc_receipt.narration,
                    oc_receipt.confirm,
                    oc_receipt_sub.ledger_id AS ledger_id2,
                    oc_receipt_sub.group_id AS group_id
                FROM
                  oc_receipt
                INNER JOIN 
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN 
                  oc_ledger ON oc_receipt.ledger_id = oc_ledger.ledger_id
                WHERE oc_receipt_sub.delete_status = 0

                UNION ALL

                SELECT
                    oc_receipt.receipt_id,
                    oc_receipt.dated,
                    oc_receipt.ledger_id,
                    oc_ledger.ledger_name,
                    oc_receipt.amount,
                    oc_receipt.mode,
                    oc_receipt.reference,
                    oc_receipt.no_of_order,
                    oc_receipt.narration,
                    0 AS confirm,
                    0 AS ledger_id2,
                    0 AS group_id
                FROM
                  oc_receipt 
                INNER JOIN oc_ledger ON oc_receipt.ledger_id = oc_ledger.ledger_id

                WHERE NOT EXISTS (SELECT * 
                                  FROM  oc_receipt_sub
                                  WHERE oc_receipt_sub.receipt_id = oc_receipt.receipt_id)
                )a ";
                
                $sql .= "WHERE 1 = 1 ";
                if(!empty($data['filter_ref'])){
                    $sql .= " AND a.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }
                
                //WHERE a.dated>='2016-02-02' AND a.dated<='2016-04-14'
                if(!empty($data['filter_date_from'])){
                    $sql .= " AND a.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND a.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_amount_from'])){
                    $sql .= " AND a.amount >= ".(float)$data['filter_amount_from']." ";
                }
                if(!empty($data['filter_amount_to'])){
                    $sql .= " AND a.amount <= ".(float)$data['filter_amount_to']." ";
                }
                if(!empty($data['filter_bank'])){
                    $sql .= " AND a.ledger_id = '".$this->db->escape($data['filter_bank'])."' ";
                }
                if(!empty($data['filter_inflow'])){
                    $sql .= " AND a.ledger_id2 = '".$this->db->escape($data['filter_inflow'])."' ";
                }
                if(!empty($data['filter_status'])){
                  if($data['filter_status'] == '101'){
                    $sql .= " ";            
                  }
                  else
                  {
                    $sql .= " AND a.ledger_id2 != 0 ";
                  }
                }
                else
                {
                    $sql .= " AND a.ledger_id2 = 0 ";
                }
                
                $sql .= ")b ";

                //$sql .="ORDER BY a.dated DESC ";
                //die($sql);

                //WHERE a.confirm = 0

        $query = $this->db->query($sql);

        //return (int)($query->num_rows);
        return $query->row['num'];
    }
  
    public function InsertReceipt($dated, $ledger_id, $amount, $mode, $reference, $user_id, $datedCM, $user_array){

        $sql = "INSERT INTO " . DB_PREFIX . "receipt
                SET dated = '" . $this->db->escape($dated) . "',
                    ledger_id = '" . (int)($ledger_id) . "', 
                    amount = '" . (float)($amount) . "',
                    mode = '" . $this->db->escape($mode) . "',
                    reference = '" . $this->db->escape($reference) . "',
                    user_id = '" . (int)($user_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "'
                    ";  

        $this->db->query($sql);
    }
    //public function InsertReceiptSubCSV($receipt_id, $receipt_sub_id, $cod_or_pg, $dated, $ledger_id, $amount, $order_no, $ref, $order_id) {
    public function InsertReceiptSubCSV($receipt_id, $receipt_sub_id, $cod_or_pg, $dated, $ledger_id, $amount, $order_no, $ref, $order_payment_id, $order_id) {//17112017

        $sql = "INSERT INTO " . DB_PREFIX . "receipt_sub_csv
                SET receipt_id = '" . (int)($receipt_id) . "',
                    receipt_sub_id = '" . (int)($receipt_sub_id) . "',
                    cod_or_pg = '" . $this->db->escape($cod_or_pg) . "', 
                    dated = '" . $this->db->escape($dated) . "', 
                    ledger_id = '" . (int)($ledger_id) . "', 
                    amount = '" . (float)($amount) . "',
                    order_no = '" . $this->db->escape($order_no) . "', 
                    ref = '" . $this->db->escape($ref) . "', 
                    order_payment_id = '" . (int)($order_payment_id) . "',
                    order_id = '" . (int)($order_id) . "'";                  
        $this->db->query($sql);
        return $this->db->getLastId(); 
    }
    //it is bcos charges in separate table
    public function InsertReceiptSubCSVForCharges($receipt_id, $receipt_sub_id, $cod_or_pg, $dated, $ledger_id, $amount, $order_no, $ref, $order_payment_id, $order_id) {

        $sql = "INSERT INTO " . DB_PREFIX . "receipt_sub_csv
                SET receipt_id = '" . (int)($receipt_id) . "',
                    receipt_sub_id = '" . (int)($receipt_sub_id) . "',
                    cod_or_pg = '" . $this->db->escape($cod_or_pg) . "',
                    dated = '" . $this->db->escape($dated) . "',
                    ledger_id = '" . (int)($ledger_id) . "', 
                    amount = '" . (float)($amount) . "',
                    order_no = '" . $this->db->escape($order_no) . "',
                    ref = '" . $this->db->escape($ref) . "',
                    order_payment_id = '" . (int)($order_payment_id) . "',
                    order_id = '" . (int)($order_id) . "'";                  
        $this->db->query($sql);
        return $this->db->getLastId(); 
    }
    public function InsertReceiptSubChargesDr($receipt_id, $receipt_sub_id, $receipt_sub_csv_id, $dated, $ledger_id, $charges, $order_no, $ref, $order_payment_id, $order_id) {

        $sql = "INSERT INTO " . DB_PREFIX . "receipt_sub_chargesdr
                SET receipt_id = '" . (int)($receipt_id) . "',
                    receipt_sub_id = '" . (int)($receipt_sub_id) . "',
                    oc_receipt_sub_csv_id = '" . (int)($receipt_sub_csv_id) . "', 
                    dated = '" . $this->db->escape($dated) . "', 
                    ledger_id = '" . (int)($ledger_id) . "', 
                    charges = '" . (float)($charges) . "',
                    order_no = '" . $this->db->escape($order_no) . "', 
                    ref = '" . $this->db->escape($ref) . "', 
                    order_payment_id = '" . (int)($order_payment_id) . "', 
                    order_id = '" . (int)($order_id) . "'";

        $this->db->query($sql);
        return $this->db->getLastId(); 
    }
    public function insertOcOrderPayment( $order_id, $ref, $order_no, $txn_status, $payment_mode, $amount, $dated, $date_added, $payment_gateway, $successful, $user_id, $tablename, $receipt_id, $receipt_sub_id ) { 

        $sql = "INSERT INTO " . DB_PREFIX . "order_payment
                SET 
                    order_id = '" . (int)($order_id) . "',
                    merchant_txn_id = '" . $this->db->escape($ref) . "',
                    order_no = '" . $this->db->escape($order_no) . "', 
                    txn_status = '" . $this->db->escape($txn_status) . "',
                    payment_mode = '" . $this->db->escape($payment_mode) . "',
                    amount = '" . (float)($amount) . "',
                    txn_date_time = '" . $this->db->escape($dated) . "',
                    date_added = '" . $this->db->escape($date_added) . "',
                    payment_gateway = '" . $this->db->escape($payment_gateway) . "',
                    successfull = '" . (int)($successful) . "',
                    user_id = '" . (int)$user_id . "',
                    rec_pay_tablename = '" . $this->db->escape($tablename) . "',
                    rec_pay_id = '" . (int)($receipt_id) . "',
                    rec_pay_sub_id = '" . (int)($receipt_sub_id) . "'";

        $this->db->query($sql);
        return $this->db->getLastId(); 
    }   
    public function getLedgerNameInLedgerTable($ledgerName) {

        $sql = "SELECT COUNT(DISTINCT ledger_id) AS total FROM oc_ledger WHERE ledger_name = '" . $this->db->escape($ledgerName) . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    //public function saveLedger( $ledger, $group_id, $user_id, $datedCM, $user_array) {
    public function saveLedger( $ledger, $group_id, $customer_id, $user_id, $datedCM, $user_array, $is_cod_security_ledger = 0) {

        $sql = "INSERT INTO " . DB_PREFIX . "ledger
                SET ledger_name = '" . $this->db->escape($ledger) . "', 
                    group_id = '" . (int)($group_id) . "',
                    customer_id = '" . (int)($customer_id) . "',
                    user_id = '" . (int)($user_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "',
                    is_cod_security_ledger = '" . (int)($is_cod_security_ledger) . "'
                    ";

        $this->db->query($sql);
        return $this->db->getLastId(); 
    }  

    public function getLedgerIDInLedgerTable($ledgerName) {

        $sql = "SELECT ledger_id FROM oc_ledger WHERE ledger_name = '" . $this->db->escape($ledgerName) . "'";

        $query = $this->db->query($sql);

        return $query->row['ledger_id'];
    }

    public function getLedgers() {

        $include_group_ids = array(1,2,3,4,5,8,12,18,19,16,13,20,9,14,15,21,COD_SECURITY_GROUP_ID,25, MEMBERSHIP_GROUP_ID);
        $include_group_ids = implode( ',', $include_group_ids );

        $ignore_ledger_ids = array(7, 8, 9, 5580, 13);
        $ignore_ledger_ids = implode( ',', $ignore_ledger_ids);
        
        $include_ledger_ids = array(1926, 14723);
        $include_ledger_ids = implode( ',', $include_ledger_ids);
        
        $sql = "SELECT
                  ledger_id,
                  ledger_name,
                  group_id
                FROM
                  oc_ledger
                WHERE
                  (group_id in (" . $include_group_ids .") AND ledger_id NOT IN (" . $ignore_ledger_ids . ")) 
                  OR ledger_id IN (" . $include_ledger_ids . ") 
                ORDER BY ledger_name ASC";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        }
    }

    public function getBanks() {

        $sql = "SELECT
                  ledger_id,
                  ledger_name
                FROM
                  oc_ledger
                WHERE
                  group_id = 8
                ORDER BY ledger_name";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        }
    }

    public function updateReceiptForNarration( $row_id, $input_value ) {

        $sql = "UPDATE " . DB_PREFIX . "receipt SET narration= '".$this->db->escape($input_value)."' WHERE receipt_id = '". (int)$row_id ."'";        
        $this->db->query($sql);
    }
    public function getAmountOfReceipt($receipt_id) {

        $sql = "SELECT amount FROM " . DB_PREFIX . "receipt
         WHERE receipt_id = '" . (int)($receipt_id) . "'";

        $query = $this->db->query($sql);

        return $query->row['amount'];
    }

    public function getGroupIDOfLedger($ledger_id) {

        $sql = "SELECT group_id FROM " . DB_PREFIX . "ledger
         WHERE ledger_id = '" . (int)($ledger_id) . "'";

        $query = $this->db->query($sql);

        return $query->row['group_id'];
    }    

    public function updateReceiptForConfirm( $row_id ) {
        //$sql = "UPDATE " . DB_PREFIX . "receipt SET confirm= 1 WHERE receipt_id = '". (int)$row_id ."'";
        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $confirm_date = date("Y-m-d H:i:s");
        $confirm_user_array = array(
                                'user_id' => $user_id,
                                'user_name' => $user_name,
                                'confirm_date' => $confirm_date
                              );

        $sql = "UPDATE " . DB_PREFIX . "receipt
                 SET confirm= 1,
                 confirm_user = '" . $this->db->escape(serialize($confirm_user_array)) . "'   
                WHERE receipt_id = '". (int)$row_id ."'";

        $this->db->query($sql);
    }

    public function getReceiptSubForReceiptID($receipt_id) {

        $sql = "SELECT * FROM " . DB_PREFIX . "receipt_sub
         WHERE receipt_id = '" . (int)$receipt_id . "' 
         AND delete_status = 0";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function insertReceiptSubForLedger( $row_id, $input_value, $group_id, $amount, $user_id, $datedCM, $user_array ) {

        $sql = "INSERT INTO " . DB_PREFIX . "receipt_sub
                SET receipt_id = '" . (int)($row_id) . "',
                    ledger_id = '" . (int)($input_value) . "',
                    group_id = '" . (int)($group_id) . "',
                    amount = '" . (float)($amount) . "',
                    user_id = '" . (int)($user_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "'
                    ";

        $this->db->query($sql);
        return $this->db->getLastId();
    }
    public function updateReceiptSubForLedger( $row_id, $input_value, $group_id, $amount ) {

        $sql = "UPDATE " . DB_PREFIX . "receipt_sub 
                SET ledger_id = '" . (int)($input_value) . "',
                    group_id = '" . (int)($group_id) . "',
                    amount = '" . (float)($amount) . "'
                WHERE receipt_id = '". (int)$row_id ."'";

        $this->db->query($sql);
    }

    public function updateReceiptForLedger( $row_id, $input_value, $amount ) {

        $sql = "INSERT INTO " . DB_PREFIX . "receipt_sub
                SET receipt_id = '" . (int)($row_id) . "',
                    ledger_id = '" . (int)($input_value) . "',
                    amount = '" . (float)($amount) . "'";

        $this->db->query($sql);
    }

    public function updateNoOfOrders($receipt_id, $order_no) {
        $sql = "UPDATE " . DB_PREFIX . "receipt SET no_of_order= '".$this->db->escape($order_no)."' WHERE receipt_id = '". (int)$receipt_id ."'";
        $this->db->query($sql);
    }

    public function InsertReceiptSubFields($receipt_id, $ledgerid, $groupid, $amount) {

        $sql = "INSERT INTO " . DB_PREFIX . "receipt_sub
                SET receipt_id = '" . (int)($receipt_id) . "',
                    ledger_id = '" . (int)($ledgerid) . "', 
                    group_id = '" . (int)($groupid) . "', 
                    amount = '" . (float)($amount) . "'";

        $this->db->query($sql);
    }

    public function updateReceiptSubFields($receipt_id, $ledgerid, $groupid, $amountOfReceipt) {


    $sql = "UPDATE " . DB_PREFIX . "receipt_sub SET ledger_id= '". (int)($ledgerid) ."', group_id= '". (int)($groupid) ."', amount= '". (float)($amountOfReceipt) ."' WHERE receipt_id = '". (int)$receipt_id ."'";

        $this->db->query($sql);
    }

    public function getReceipt_idInSubTable($receipt_id) {

        $sql = "SELECT COUNT(DISTINCT receipt_id) AS total FROM " . DB_PREFIX . "receipt_sub WHERE receipt_id = '" . $this->db->escape($receipt_id) . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }    

    public function getReceiptSubForReceiptSubID($receipt_id) {

        $sql = "
                SELECT 
                    receipt_sub_id 
                FROM 
                    " . DB_PREFIX . "receipt_sub
                WHERE 
                    receipt_id = '". (int)$receipt_id ."' 
                    AND delete_status = 0
                ORDER BY
                    receipt_sub_id DESC
              ";

        $query = $this->db->query($sql);

        return $query->row['receipt_sub_id'];
    }

    public function deleteReceiptSub($row_id) {

        $sql = "DELETE FROM " . DB_PREFIX . "receipt_sub 
                WHERE receipt_id = '". (int)$row_id ."'";
        $this->db->query($sql);
    }
    public function deleteReceiptSubCSV($row_id) {

        $sql_sub_csv = "DELETE FROM " . DB_PREFIX . "receipt_sub_csv 
                WHERE receipt_id = '". (int)$row_id ."'";
        $this->db->query($sql_sub_csv);        
    }
    public function deleteReceiptSubChargesDr($row_id) {

        $sql_sub_csv = "DELETE FROM " . DB_PREFIX . "receipt_sub_chargesdr 
                WHERE receipt_id = '". (int)$row_id ."'";
        $this->db->query($sql_sub_csv);        
    }
  
    public function deleteOcOrderPayment($row_id, $tablename, $txn_status) {

        $sql = "DELETE FROM " . DB_PREFIX . "order_payment 
                WHERE rec_pay_id = '". (int)$row_id ."' 
                AND rec_pay_tablename = '". $this->db->escape($tablename) ."' 
                AND txn_status = '" . $this->db->escape($txn_status) . "'";        

        $this->db->query($sql);        
    }

    public function deleteOcOrderPaymentCOD($row_id, $tablename) {

        $sql = "DELETE FROM " . DB_PREFIX . "order_payment 
                WHERE rec_pay_id = '". (int)$row_id ."' 
                AND rec_pay_tablename = '". $this->db->escape($tablename) ."' 
                AND payment_gateway IN (" . COD_PAYMENT_GATEWAYS . ") ";        

        $this->db->query($sql);
    }

    public function updateReceiptSub($row_id) {
        // only update whose delete_status = 0
        $sql = "UPDATE " . DB_PREFIX . "receipt_sub SET delete_status= 1 WHERE receipt_id = '". (int)$row_id ."' AND delete_status = 0";

        $this->db->query($sql);
    }
    public function updateReceiptSubCSV($row_id) {
        // only update whose delete_status = 0
        $sql = "UPDATE " . DB_PREFIX . "receipt_sub_csv SET delete_status= 1 WHERE receipt_id = '". (int)$row_id ."' AND delete_status = 0";

        $this->db->query($sql);        
    }
    public function updateReceiptSubChargesDr($row_id) {
        // only update whose delete_status = 0
        $sql = "UPDATE " . DB_PREFIX . "receipt_sub_chargesdr SET delete_status= 1 WHERE receipt_id = '". (int)$row_id ."' AND delete_status = 0";

        $this->db->query($sql);        
    }
  
    //not in use 15112017
    public function updateOcOrderPaymentx($row_id, $tablename, $txn_status) {
        // only update whose delete_status = 0
        $sql = "UPDATE " . DB_PREFIX . "order_payment SET delete_status= 1, successfull = 0
                WHERE rec_pay_id = '". (int)$row_id ."' 
                AND rec_pay_tablename = '". $this->db->escape($tablename) ."' 
                AND txn_status = '" . $this->db->escape($txn_status) . "'
                AND delete_status = 0";        

        $this->db->query($sql);        
    }

    //public function updateOcOrderPaymentDS($row_id, $tablename, $txn_status) {
    public function updateOcOrderPaymentDS($row_id, $tablename) {
        $sql = "UPDATE " . DB_PREFIX . "order_payment 
                SET rec_pay_tablename = 'not_applicable', rec_pay_id = 0, rec_pay_sub_id = 0
                WHERE rec_pay_id = '". (int)$row_id ."' 
                AND rec_pay_tablename = '". $this->db->escape($tablename) ."'";

        $this->db->query($sql);        
    }

    public function getReceiptSubCSVForReceiptSubID( $receipt_sub_id) {

        $sql = "SELECT * FROM " . DB_PREFIX . "receipt_sub_csv
         WHERE receipt_sub_id = '" . (int)($receipt_sub_id) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }    

    public function getCustomerNameForCOD($ref, $COD, $order_no) { 

        $sql = "SELECT
                  osub.order_id,
                  osub.suborder_id,
                  osub.courier_partner,
                  osub.tracking_no,
                  o.firstname,
                  o.lastname,
                  o.payment_company,
                  o.customer_id,
                  o.gst_number
                FROM
                  oc_order AS o 
                JOIN oc_suborder AS osub ON osub.order_id = o.order_id 
                WHERE
                  osub.courier_partner = '" . $this->db->escape($COD) . "' 
                  AND osub.tracking_no = '" . $this->db->escape($ref) . "' 
                  AND o.order_no = '" . $this->db->escape($order_no) . "' 
                  AND o.franchise_id = 0 ";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getCustomerNameForBR($order_no) { 
        //temporary period (using Like)
        $sql = "SELECT
                  oo.order_id,
                  oo.order_no,
                  oo.firstname,
                  oo.lastname,
                  oo.payment_company,
                  oo.customer_id,
                  oo.gst_number
                FROM
                  oc_order oo
                WHERE
                  oo.order_no = '" . $this->db->escape($order_no) . "'
                  AND oo.franchise_id = 0 ";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getCustomerNameForPG($ref, $payment_gateway, $successfull = 1) {

        $sql = "SELECT
                  oop.payment_id,
                  oop.order_id,
                  oop.merchant_txn_id,
                  oop.payment_gateway,
                  oop.successfull,
                  oo.firstname,
                  oo.lastname,
                  oo.payment_company,
                  oo.customer_id,
                  oo.gst_number
                FROM
                  oc_order_payment oop
                INNER JOIN
                  oc_order oo ON oop.order_id = oo.order_id
                WHERE
                  oop.merchant_txn_id = '" . $this->db->escape($ref) . "' 
                  AND oop.payment_gateway = '" . $this->db->escape($payment_gateway) . "' 
                  AND oop.successfull = ". (int)$successfull." 
                  AND oop.rec_pay_id = 0 
                  AND oo.franchise_id = 0 
                  AND oop.payment_gateway NOT IN ('coupon','cashback') ";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getOpidByMerchantTrxnAndPaymentGateway($ref, $payment_gateway, $order_no) {
        $sql = "
                SELECT
                    payment_id
                FROM
                    ".DB_PREFIX."order_payment
                WHERE
                    merchant_txn_id     = '" . $this->db->escape($ref) . "' 
                    AND order_no        = '". $this->db->escape($order_no) ."'
                    AND payment_gateway = '" . $this->db->escape($payment_gateway) . "' 
                    AND successfull     = 1
                    AND payment_gateway NOT IN ('coupon','cashback')
               ";
        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getOrderDetailForQRCode($order_no, $amount, $successful) {  

        $sql = "SELECT
                oop.payment_id,
                oop.order_id,
                oop.merchant_txn_id,
                oop.order_no,
                oop.txn_date_time,
                oop.date_added,
                oop.payment_gateway,
                oop.amount,
                oop.successfull,
                oop.reference,
                oop.payment_link,
                oo.firstname,
                oo.lastname,
                oo.payment_company,
                oo.customer_id,
                oo.gst_number
              FROM
                oc_order_payment oop
              Inner JOIN oc_order oo ON oo.order_id = oop.order_id
              WHERE
                oo.order_no = '" . $this->db->escape($order_no) . "' 
                AND oop.amount = '" . (float)($amount) . "'
                AND oop.successfull = '" . (int)($successful) . "'
                AND oop.rec_pay_tablename !='receipt' 
                AND oop.rec_pay_id = 0 
                AND oo.franchise_id = 0 
                AND oop.payment_gateway NOT IN ('coupon','cashback')";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderDetailByPaymentIDQR($payment_id) {

        $sql = "SELECT
                oop.payment_id,
                oop.order_id,
                oop.merchant_txn_id,
                oop.order_no,
                oop.txn_date_time,
                oop.date_added,
                oop.payment_gateway,
                oop.amount,
                oop.successfull,
                oo.firstname,
                oo.lastname,
                oo.payment_company,
                oo.customer_id,
                oo.gst_number
              FROM
                oc_order_payment oop
              Inner JOIN oc_order oo ON oo.order_id = oop.order_id
              WHERE
                oop.payment_id = '" . (int)($payment_id) . "'
                AND oop.rec_pay_tablename !='receipt' 
                AND oop.rec_pay_id = 0 
                AND oo.franchise_id = 0 
                AND oop.payment_gateway NOT IN ('coupon','cashback') ";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function updateOcOrderPaymentForQR($payment_id, $order_no, $ref, $payment_gateway) {
    
        $sql = "UPDATE " . DB_PREFIX . "order_payment 
                SET reference = CONCAT(reference, IF((merchant_txn_id != '' AND merchant_txn_id IS NOT NULL), CONCAT('. Old Ref: ', merchant_txn_id), '')),
                    merchant_txn_id= '".$this->db->escape($ref)."', 
                    payment_gateway= '".$this->db->escape($payment_gateway)."'
                    
                WHERE 
                 payment_id = '". (int)$payment_id ."'  
                 AND order_no = '" . $this->db->escape($order_no) . "'
                 AND rec_pay_tablename !='receipt' 
                 AND rec_pay_id = 0";

        $this->db->query($sql);
    }

    public function updateOcOrderPaymentForQRTxnDateTime($txn_date_time, $order_no, $ref, $payment_gateway, $successful) {

        $sql = "UPDATE " . DB_PREFIX . "order_payment SET txn_date_time= '".$this->db->escape($txn_date_time)."'  
                WHERE 
                oc_order_payment.order_no = '" . $this->db->escape($order_no) . "' 
                AND oc_order_payment.merchant_txn_id = '" . $this->db->escape($ref) . "'
                AND oc_order_payment.payment_gateway = '" . $this->db->escape($payment_gateway) . "'
                AND oc_order_payment.successfull = '" . (int)($successful) . "'
                AND oc_order_payment.rec_pay_tablename !='receipt' 
                AND oc_order_payment.rec_pay_id = 0 
                AND oc_order_payment.payment_gateway NOT IN ('coupon','cashback')
                ";

        $this->db->query($sql);
    }

    public function getOrderDetail($order_no, $amount, $successful, $bank_transfer, $cash, $upi, $txn_status) { 

        $sql = "SELECT
                oop.payment_id,
                oop.order_id,
                oop.merchant_txn_id,
                oop.order_no,
                oop.txn_date_time,
                oop.date_added,
                oop.payment_gateway,
                oop.amount,
                oop.successfull,
                oo.firstname,
                oo.lastname,
                oo.payment_company,
                oo.customer_id,
                oo.gst_number
              FROM
                oc_order_payment oop
              Inner JOIN oc_order oo ON oo.order_id = oop.order_id
              WHERE
                oo.order_no LIKE '%" . $this->db->escape($order_no) . "%' 
                
                AND oop.successfull = '" . (int)($successful) . "' 
                AND (oop.payment_gateway = '" . $this->db->escape($bank_transfer) . "' 
                  OR oop.payment_gateway = '" . $this->db->escape($cash) . "'
                  OR oop.payment_gateway = '" . $this->db->escape($upi) . "')
                AND oop.txn_status != '" . $this->db->escape($txn_status) . "'
                AND oop.rec_pay_tablename !='receipt' 
                AND oop.rec_pay_id = 0 
                AND oo.franchise_id = 0 
                AND oop.payment_gateway NOT IN ('coupon','cashback')";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderDetailByPaymentID($payment_id, $amount, $successful, $bank_transfer, $cash, $upi) { 

        $sql = "SELECT
                oop.payment_id,
                oop.order_id,
                oop.merchant_txn_id,
                oop.order_no,
                oop.txn_date_time,
                oop.date_added,
                oop.payment_gateway,
                oop.amount,
                oop.successfull,
                oo.firstname,
                oo.lastname,
                oo.payment_company,
                oo.customer_id,
                oo.gst_number
              FROM
                oc_order_payment oop
              Inner JOIN oc_order oo ON oo.order_id = oop.order_id
              WHERE
                oop.payment_id = '" . (int)($payment_id) . "' 
                AND oop.successfull = '" . (int)($successful) . "' 
                AND (oop.payment_gateway = '" . $this->db->escape($bank_transfer) . "' OR oop.payment_gateway = '" . $this->db->escape($cash) . "' OR oop.payment_gateway = '" . $this->db->escape($upi) . "' OR oop.payment_gateway = 'wsb_credit_nach' )
                AND oop.rec_pay_tablename !='receipt' 
                AND oop.rec_pay_id = 0 
                AND oo.franchise_id = 0 
                AND oop.payment_gateway NOT IN ('coupon','cashback')";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function updateOcOrderPaymentForTxnDateAndTxnID($payment_id, $datedRO, $refAA) {
        
        //$sql = "UPDATE " . DB_PREFIX . "order_payment SET txn_date_time = date_added WHERE payment_id = '". (int)$payment_id ."'";
        $sql = "UPDATE " . DB_PREFIX . "order_payment 
                SET 
                   txn_date_time = '".$this->db->escape($datedRO)."',
                   reference = CONCAT(reference, IF((merchant_txn_id != '' AND merchant_txn_id IS NOT NULL), CONCAT('. Old Ref: ', merchant_txn_id), '')),
                   merchant_txn_id = '".$this->db->escape($refAA)."'
                WHERE payment_id = '". (int)$payment_id ."' 
                AND rec_pay_tablename !='receipt' 
                AND rec_pay_id = 0 
                "; 

        $this->db->query($sql);        
    }

    public function getReceiptSubCSVByPaymentID($order_id, $payment_id) {
/*
        $sql = "SELECT *
              FROM
                oc_receipt_sub_csv
              WHERE
                order_payment_id = '" . $this->db->escape($payment_id) . "'";
*/
        $sql = "SELECT *
              FROM
                oc_receipt_sub_csv
              WHERE
                order_payment_id = '" . (int)($payment_id) . "'
                AND delete_status = 0";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function deleteAllReceipts() {
        return true;
        /*$sql = "Delete FROM " . DB_PREFIX . "receipt
                WHERE NOT EXISTS
                  (SELECT * FROM oc_receipt_sub WHERE oc_receipt_sub.receipt_id = oc_receipt.receipt_id)";

        $this->db->query($sql);*/
    }

    public function getReceiptForRef($reference) {

        //$sql = "SELECT * FROM " . DB_PREFIX . "receipt WHERE reference = '" . $this->db->escape($reference) . "'";

        $sql = "SELECT reference FROM oc_receipt WHERE reference = '" . $this->db->escape($reference) . "'";
  
        $sql .= " UNION ALL ";

        $sql .= "SELECT reference FROM oc_payment WHERE reference = '" . $this->db->escape($reference) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }
    
    public function getReceiptForRef2($reference) {

        $sql = "SELECT reference FROM oc_receipt WHERE reference = '" . $this->db->escape($reference) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getReceiptSubForRef($reference) {

        $sql = "SELECT
                  oc_receipt.reference
                FROM
                  oc_receipt
                INNER JOIN
                oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
                WHERE
                  oc_receipt.reference = '" . $this->db->escape($reference) . "' AND oc_receipt_sub.delete_status = 0";

        $query = $this->db->query($sql);

        return $query->row;
    }  

    public function getReceiptData($reference) {

        $sql = "SELECT
                orec.receipt_id,
                orec.dated,
                orec.ledger_id,
                orec.amount,
                orec.reference,
                ol.ledger_name  
              FROM
                oc_receipt orec
              INNER JOIN oc_ledger ol ON orec.ledger_id = ol.ledger_id
              WHERE
                orec.reference = '" . $this->db->escape($reference) . "'";        

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getOcOrderDetailForBuyersRefund($order_no, $ref, $amount, $payment_type, $successful) { 

        $sql = "SELECT
                oop.payment_id,
                oop.order_id,
                oop.merchant_txn_id,
                oop.order_no,
                oop.txn_date_time,
                oop.payment_gateway,
                oop.amount,
                oop.successfull,
                oo.firstname,
                oo.lastname,
                oo.payment_company,
                oo.customer_id,
                oo.gst_number
              FROM
                oc_order_payment oop
              Inner JOIN oc_order oo ON oo.order_id = oop.order_id
              WHERE
                oop.order_no = '" . $this->db->escape($order_no) . "' 
                AND oop.merchant_txn_id = '" . $this->db->escape($ref) . "' 
                AND oop.amount = '" . (float)($amount) . "' 
                AND ( ";

        if($payment_type == 'refund'){
            $sql .= " oop.amount < 0 OR ";
        }
        
        $sql .= " oop.successfull = '" . (int)($successful) . "') 
                AND oo.franchise_id = 0 
                AND oop.payment_gateway NOT IN ('coupon','cashback')";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getLedgerByEmpCode($emp_code) {

        $sql = "SELECT *
              FROM
                oc_ledger
              WHERE
                oc_ledger.emp_code = '" . $this->db->escape($emp_code) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }


    public function getStaffs() {
        
      $sql = "SELECT staff_id, name FROM " . DB_PREFIX . "sales_staff ORDER BY name ASC";

      $query = $this->db->query($sql);

      if ( $query->num_rows ) {
          return $query->rows;
      }
    }

    public function getStaffByID($staff_id) {

        $sql = "SELECT staff_id, name
                FROM " . DB_PREFIX . "sales_staff
                WHERE staff_id = '". (int)$staff_id ."'";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->row;
        } 
    }

    public function getOrderIDByPaymentIDOnlyForSalesStaff($payment_id) {

        $sql = "SELECT
                oop.payment_id,
                oop.order_id
              FROM
                oc_order_payment oop
              WHERE
                oop.payment_id = '" . (int)($payment_id) . "'
                AND oop.payment_gateway NOT IN ('coupon','cashback')";

        $query = $this->db->query($sql);

        return $query->row;
    }

    //sale/order function
    public function getSalesStaffList() {

        $sales_staff = array('0' => array('name' => '--None--'));

        $staff_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sales_staff WHERE user_type = 'DirectSale' ORDER BY name ASC");

        if ($staff_query->rows) {
            foreach ($staff_query->rows as $staff) {
                $active_status = ($staff['active_status'] == 1) ? 'active' : 'inactive';
                $sales_staff[$staff['staff_id']]['name'] = $staff['role'] . '-' . $staff['name'] . '-' . $staff['telephone'] . '-' . $active_status;
                $sales_staff[$staff['staff_id']]['status'] = $staff['active_status'];
            }
        }

        return $sales_staff;
    }
    
    public function getStaffsForDS($payment_id) {
    
      $sales_staff = array('0' => array('name' => '--None--'));        

      $sql = "SELECT
              DISTINCT ooss.sales_staff_id AS staff_id,
              oss.name,
              oss.role,
              oss.telephone,
              oss.active_status
            FROM
              oc_order_sales_staff ooss
            INNER JOIN oc_sales_staff oss ON ooss.sales_staff_id = oss.staff_id
            WHERE ooss.order_id IN (".implode(',',$payment_id).") 
            ORDER BY name ASC";

      $staff_query = $this->db->query($sql);

        if ($staff_query->rows) {
            foreach ($staff_query->rows as $staff) {
                $active_status = ($staff['active_status'] == 1) ? 'active' : 'inactive';
                $sales_staff[$staff['staff_id']]['name'] = $staff['role'] . '-' . $staff['name'] . '-' . $staff['telephone'] . '-' . $active_status;
                $sales_staff[$staff['staff_id']]['status'] = $staff['active_status'];
            }
        } else {
            $sales_staff_id = 82;
            $sales_staff[$sales_staff_id]['name'] = 'tele-WholesaleBox Support-8696491521-active';
            $sales_staff[$sales_staff_id]['status'] = 1;
    

        }
        
        return $sales_staff;
    }

    public function updateCustomerLedger( $ledger_id, $customer_id){

        $sql = "UPDATE " . DB_PREFIX . "customer_ledgers 
                SET ledger_id = '". (int)($ledger_id) ."' 
                WHERE customer_id = '". (int)$customer_id ."'";
        $this->db->query($sql);
    }

    public function updateOcOrderPaymentForRec_pay_ID($tablename, $receipt_id, $receipt_sub_id, $payment_id, $order_id) {

        $sql = "UPDATE " . DB_PREFIX . "order_payment 
                SET                     
                    rec_pay_tablename = '" . $this->db->escape($tablename) . "',
                    rec_pay_id = '" . (int)($receipt_id) . "',
                    rec_pay_sub_id = '" . (int)($receipt_sub_id) . "'
                WHERE 
                    payment_id = '". (int)$payment_id ."' AND 
                    order_id='". (int)$order_id ."'";

        $this->db->query($sql);
    }

    public function getOrderDetailForDS($order_no, $amount, $successful) {  

        $sql = "SELECT
                oop.payment_id,
                oop.order_id,
                oop.merchant_txn_id,
                oop.order_no,
                oop.txn_date_time,
                oop.date_added,
                oop.payment_gateway,
                oop.amount,
                oop.successfull,
                oop.reference,
                oop.payment_link,
                oop.rec_pay_tablename,
                oop.rec_pay_id,
                oop.rec_pay_sub_id,
                oo.firstname,
                oo.lastname,
                oo.payment_company,
                oo.customer_id,
                oo.gst_number
              FROM
                oc_order_payment oop
              Inner JOIN oc_order oo ON oo.order_id = oop.order_id
              WHERE
                oo.order_no LIKE '%" . $this->db->escape($order_no) . "%' 
                AND oop.amount = '" . (float)($amount) . "'
                AND oop.successfull = '" . $this->db->escape($successful) . "'
                AND oo.franchise_id = 0 
                AND oop.payment_gateway NOT IN ('coupon','cashback')";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getReceiptByID($receipt_id) {

        $sql = "SELECT
                orec.receipt_id,
                orec.dated,
                orec.ledger_id,
                orec.amount,
                orec.reference 
              FROM
                oc_receipt orec
              WHERE
                orec.receipt_id = '" . (int)($receipt_id) . "'";        

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getPaymentByAmountLedgerID($amount, $ledger_id) {

        $sql = "SELECT
                  op.payment_id,
                  op.dated,
                  op.ledger_id,
                  ol.ledger_name,
                  op.amount,
                  op.mode,
                  op.reference,
                  op.narration
                FROM
                  oc_payment op
                INNER JOIN
                  oc_ledger ol ON op.ledger_id = ol.ledger_id
                WHERE op.amount = '" . (float)($amount) . "'
                AND op.ledger_id = '" . (int)($ledger_id) . "'
                AND NOT EXISTS
                  (SELECT * FROM oc_payment_sub WHERE oc_payment_sub.payment_id = op.payment_id)";        

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getPaymentByID($payment_id) {//xxxxxxxxxxxxxx

        $sql = "SELECT
                op.payment_id,
                op.dated,
                op.ledger_id,
                op.amount,
                op.reference,
                ol.ledger_name  
              FROM
                oc_payment op
              INNER JOIN oc_ledger ol ON op.ledger_id = ol.ledger_id
              WHERE
                op.payment_id = '" . (int)($payment_id) . "'";  

        $query = $this->db->query($sql);

        return $query->row;
    }
    public function getAmountByRef($reference) {

        $sql = "SELECT amount FROM " . DB_PREFIX . "receipt
         WHERE reference = '" . $this->db->escape($reference) . "'";

        $query = $this->db->query($sql);

        return $query->row['amount'];
    }

    public function getReceiptSubCSVByReceiptIDForDS_Bulk($receipt_id, $DS_Bulk, $credit_agency ) {

        $sql = "SELECT
                  *
                FROM
                  oc_receipt_sub_csv orsc
                WHERE
                  orsc.receipt_id = '" . (int)($receipt_id) . "' 
                  AND(orsc.cod_or_pg = '" . $this->db->escape($DS_Bulk) . "' OR orsc.cod_or_pg = '" . $this->db->escape($credit_agency) . "')
                  AND orsc.delete_status = 0";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        }
    }

    public function getDoneCount() {

        $sql = "SELECT
                  COUNT(*) AS num
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                WHERE
                  oc_receipt_sub.delete_status = 0";

        $query = $this->db->query($sql);

        return $query->row['num'];

    }

    public function getPendingCount() {

        $sql = "SELECT
                  COUNT(*) AS num
                FROM
                  oc_receipt
                WHERE NOT EXISTS
                  (
                  SELECT * FROM oc_receipt_sub
                  WHERE
                    oc_receipt_sub.receipt_id = oc_receipt.receipt_id
                )";

        $query = $this->db->query($sql);

        return $query->row['num'];

    }

    public function getOcOrderForCA($order_no, $amount, $successful) {

        $sql = "SELECT
                  oop.payment_id,
                  oop.order_id,
                  oop.merchant_txn_id,
                  oop.payment_gateway,
                  oop.successfull,
                  oo.firstname,
                  oo.lastname,
                  oo.payment_company,
                  oo.customer_id,
                  oo.gst_number
                FROM
                  oc_order_payment oop
                INNER JOIN
                  oc_order oo ON oop.order_id = oo.order_id
                WHERE
                  oo.order_no = '" . $this->db->escape($order_no) . "'
                  AND oop.payment_gateway IN (".CREDIT_AGENCY_PAYMENT_GATEWAYS.")
                  AND oop.amount = '" . (float)($amount) . "' 
                  AND oop.successfull = ". (int)$successful ." 
                  AND oop.rec_pay_tablename !='receipt' 
                  AND oop.rec_pay_id = 0 
                  AND oo.franchise_id = 0 
                  AND oop.payment_gateway NOT IN ('coupon','cashback') ";
        $query = $this->db->query($sql);

        return $query->row;
    }

    /**
    * public method to get customer details
    * @param: int customer id
    * @return: Array customer details
    * @author: MSA August 2018
    */
    public function getCustomerDetails(int $customer_id)
    {
        $sql = "SELECT 
                    c.customer_id,
                    c.firstname,
                    c.lastname,
                    IFNULL(a.city, ''),
                    IFNULL(a.company, ''),
                    c.telephone
                FROM " . DB_PREFIX . "customer c
                LEFT JOIN " . DB_PREFIX . "address a 
                    ON a.address_id = c.address_id
                WHERE 
                    c.customer_id = ".(int)$customer_id."
                LIMIT 1    
                ";
        $query = $this->db->query($sql);
        return $query->row;
    }

    /**
    * public method to get customer ladger lable for COD Security ledger
    * @param: int customer id
    * @return: string ladger
    * @author: MSA Oct 2018
    */
    public function getCustomerLedgerName( int $customer_id )
    {
        $sql = "SELECT  o.customer_id,
                        TRIM(CONCAT(o.customer_id,
                        '-', 
                        TRIM(CONCAT(o.firstname,' ', o.lastname)),
                        '-',
                        TRIM(o.payment_company) 
                      )) AS ledger_name
                FROM " . DB_PREFIX . "order o 
                INNER JOIN " . DB_PREFIX . "suborder osub ON osub.order_id = o.order_id 
                WHERE o.customer_id      = '" . (int) $customer_id . "' 
                AND o.franchise_id = 0 
                AND osub.order_id > 0 
                ORDER BY o.order_id ASC LIMIT 1
                ";
        $result = $this->db->query( $sql );        
        if( $result->num_rows ) {
            return $result->row['ledger_name'];
        }
        return false;
    }

    /**
    * public method to check if customer COD security deposit ladger exists
    * @param: int customer id
    * @return: Array customer details
    * @author: MSA August 2018
    */
    public function isCodSecurityCustomerLedgerExists(string $ladger_name, int $customer_id, int $group_id)
    {
        $sql = "
                SELECT 
                        `ledger_id`
                FROM 
                    " . DB_PREFIX . "ledger
                WHERE
                    ledger_name = '".$this->db->escape($ladger_name)."'
                    AND
                    customer_id =  ".(int) $customer_id ."
                    AND
                    group_id  =  ".(int) $group_id ." 
                    AND
                    is_cod_security_ledger = 1   
                LIMIT 1    

            ";
        $result = $this->db->query($sql);    
        if( $result->num_rows ){
            return $result->row['ledger_id'];
        }
        return 0;
    }

    /**
    * public method to save customer COD security data
    * @param: int master_id id
    * @param: float amount
    * @return: void
    * @author: MSA Sept 2018
    */
    public function setCustomerCODSecurityBalance( int $master_id, float $amount ): void
    {
        $check_sql = " SELECT 
                            master_id
                        FROM " . DB_PREFIX . "cod_security 
                        WHERE  
                            master_id = '".(int) $master_id."'   
                    ";
        $result = $this->db->query($check_sql);
        // IF customer COD Security Deposit already exist, then update 
        if( $result->num_rows ) {
            $sql = "
                    UPDATE 
                        " . DB_PREFIX . "cod_security
                    SET
                      cod_security_balance = cod_security_balance + ".(float)$this->db->escape($amount)."
                    WHERE
                        master_id = '".(int) $master_id."'
                    ";
        }else{
            // insert new entry for customer master id with COD security deposit amount
             $sql = "
                INSERT INTO 
                            " . DB_PREFIX . "cod_security
                        SET
                          master_id = '".(int) $master_id."',
                          cod_security_balance = '".(float)$this->db->escape($amount)."'
                    ";
        }
        $this->db->query($sql);
    }

    /*
     * @method: getOrderPayment- get order payment using order no, payment gateway, and amount 
     * @params: order no, payment gateway, amount
     * @return: payment data
     * @author: Devendra, August 2018
     */
    public function getOrderPayment(string $order_no, string $payment_gateway, float $amount): array {
      $sql = "SELECT payment_id, rec_pay_id FROM ". DB_PREFIX . "order_payment 
					WHERE 
					order_no ='". $this->db->escape($order_no) ."' AND
          amount ='". (float)$amount ."' AND
          payment_gateway = '".$this->db->escape($payment_gateway)."' AND 
					successfull = '1'";
          
			$query_result = $this->db->query($sql);
      
			if($query_result->num_rows) {
				return $query_result->row;
			}
			
			return array();
    }
    
    /*
     * @method: updateOrderPaymentUsingPaymentId- get order payment using order no, payment gateway, and amount 
     * @params: order no, payment gateway, amount
     * @return: payment data
     * @author: Devendra, August 2018
     */
    public function updateOrderPaymentUsingPaymentId(int $payment_id, array $data): bool {
      $fields = array('rec_pay_tablename', 'rec_pay_id', 'rec_pay_sub_id', 'merchant_txn_id', 'txn_date_time', 'amount', 'user_id');
      $sql = "UPDATE " . DB_PREFIX . "order_payment 
            SET ";
      $set_params = '';
      foreach ($fields as $key) {
        if (isset($data[$key])) {
          $set_params .= $key . "='" . $this->db->escape($data[$key]) . "', ";
        }
      }
      
      if (empty($set_params)) return false;
      
      $set_params = rtrim($set_params, ", ");
      $sql .= $set_params;
      
      $sql .= " WHERE payment_id='" . (int)$payment_id . "'";
      
      if ($this->db->query($sql)) {
        return true;
      }
    
      return false;
    }

    /**
    * public method to save customer COD security Receipt data
    * @param: int master_id id
    * @param: string table name
    * @param: int table id
    * @return: void
    * @author: MSA Sept 2018
    */
    public function setCustomerCODSecurityReceiptTableData( int $master_id, 
                                                   string $table_name,
                                                   int $table_id,
                                                   int $credit_note_id = 0
                                                ): void
    {
        
        $sql = "
                INSERT INTO 
                    " . DB_PREFIX . "cod_security_to_receipt
                SET
                  master_id     = '".(int)$master_id."',
                  table_name    = '".$this->db->escape($table_name)."',
                  table_id      = '".(int)$table_id."' ";
        
        // Default value of credit_note_id to be NULL. So, if empty value received, we wont set it.
        if ( $credit_note_id > 0 ) {
            $sql .= ", credit_note_id = '".(int)$credit_note_id."'";
        }
        $this->db->query($sql);        
    }

    /**
    * public method to get customer ledger details
    * @param: int customer_master_id
    * @return: array ledger detail
    * @author: MSA Oct 2018
    */
    public function getCustomerLedgerDetails(int $customer_master_id): array
    {
        $sql = "
                SELECT 
                    table_id
                FROM
                    " . DB_PREFIX . "cod_security_to_receipt
                WHERE
                    master_id = '" . (int)$customer_master_id . "' 
                    AND
                    table_name = 'oc_receipt_sub_csv' 
                LIMIT 1   
            ";
        $result = $this->db->query($sql);

        if($result->num_rows) {
            $table_id  =  $result->row['table_id'];
            $sql_sub = "
                        SELECT  
                            led.ledger_id,
                            led.group_id
                        FROM 
                           " . DB_PREFIX . "receipt_sub_csv as rsc
                        INNER JOIN 
                            " . DB_PREFIX . "ledger as led ON led.ledger_id = rsc.ledger_id
                        WHERE
                            oc_receipt_sub_csv_id = '" . (int) $table_id . "'     

                       ";
            $result_sub = $this->db->query($sql_sub);
            if($result_sub->num_rows){
                return $result_sub->row;
            }          
        } 
        return array();
    }
    
    /**
    * public method to get ledger details using customer id and group id
    * @param: customer id, group id
    * @return: Array ledger details
    * @author: Devendra, October 2018
    */
    public function getLedgerUsingCustomerAndGroup(int $customer_id, int $group_id): array
    {
        $sql = "
                SELECT 
                    ledger_id,
                    ledger_name
                FROM 
                    " . DB_PREFIX . "ledger
                WHERE
                    customer_id =  ".(int) $customer_id ."
                    AND
                    group_id  =  ".(int) $group_id ." 
                LIMIT 1
            ";
        $result = $this->db->query($sql);    
        if( $result->num_rows ){
            return $result->row;
        }
        
        return array();
    }

    
    /*
     * @method: getOrderPaymentUsingOrderAndPayment- get order payment using order no and payment gateway
     * @params: order no, payment gateway, amount
     * @return: payment data
     * @author: Devendra, September 2018
     */
    public function getOrderPaymentUsingOrderAndPayment(string $order_no, string $payment_gateway): array {
      $sql = "SELECT payment_id, amount, rec_pay_tablename, rec_pay_id FROM ". DB_PREFIX . "order_payment 
					WHERE 
					order_no ='". $this->db->escape($order_no) ."' AND
          payment_gateway = '".$this->db->escape($payment_gateway)."' AND 
          amount > 0 AND 
					successfull = '1'";
          
			$query_result = $this->db->query($sql);
      
			if($query_result->num_rows) {
				return $query_result->row;
			}
			
			return array();
    }


    public function getLinkedOrderPayment($ref, $payment_gateway) {
        $data = array();
        $sql = "SELECT
                  oop.payment_id
                FROM
                  oc_order_payment oop
                WHERE
                  oop.merchant_txn_id     = '" . $this->db->escape($ref) . "' 
                  AND oop.payment_gateway = '" . $this->db->escape($payment_gateway) . "' 
                  AND oop.successfull     = 1 
                  AND oop.rec_pay_id      > 0 
                ";

        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $data = $query->row;
        }
        return $data;
    }
}
