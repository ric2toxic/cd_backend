<?php
class ModelAccountPanelBankpayment extends Model {
  
    public function getBankPayments($data = array()) {

        $sql = "SELECT a.payment_id, a.dated, a.ledger_id, a.ledger_name, a.amount, a.mode, a.reference, a.narration, a.imgg, a.confirm1, a.confirm2, a.confirm3, a.ledger_id2, a.group_id
                FROM
                (
                SELECT
                  oc_payment.payment_id,
                  oc_payment.dated,
                  oc_payment.ledger_id,
                  oc_ledger.ledger_name,
                  oc_payment.amount,
                  oc_payment.mode,
                  oc_payment.reference,
                  oc_payment.narration,
                  oc_payment_sub.imgg,
                  oc_payment.confirm1,
                  oc_payment.confirm2,
                  oc_payment.confirm3,
                  oc_payment_sub.ledger_id AS ledger_id2,
                  oc_payment_sub.group_id AS group_id
                FROM
                  oc_payment
                INNER JOIN 
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                INNER JOIN 
                  oc_ledger ON oc_payment.ledger_id = oc_ledger.ledger_id
                WHERE oc_payment_sub.delete_status = 0

                UNION ALL

                SELECT
                  oc_payment.payment_id,
                  oc_payment.dated,
                  oc_payment.ledger_id,
                  oc_ledger.ledger_name,
                  oc_payment.amount,
                  oc_payment.mode,
                  oc_payment.reference,                  
                  oc_payment.narration,
                  '' AS imgg,
                  0 AS confirm1,
                  0 AS confirm2,
                  0 AS confirm3,
                  0 AS ledger_id2,
                  0 AS group_id                  
                FROM
                  oc_payment 
                INNER JOIN oc_ledger ON oc_payment.ledger_id = oc_ledger.ledger_id

                WHERE NOT EXISTS (SELECT * 
                                  FROM  oc_payment_sub
                                  WHERE oc_payment_sub.payment_id = oc_payment.payment_id)
                )a";

                $sql .= " WHERE 1 = 1 ";
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
                if(!empty($data['filter_outflow'])){
                    $sql .= " AND a.ledger_id2 = '".$this->db->escape($data['filter_outflow'])."' ";
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
    
    public function getPaymentCount($data = array()) {      

        $sql = "SELECT COUNT(*) as num
                FROM
                (
                SELECT 
                  a.payment_id, 
                  a.dated, 
                  a.ledger_id, 
                  a.ledger_name, 
                  a.amount, 
                  a.mode, 
                  a.reference, 
                  a.narration, 
                  a.imgg, 
                  a.confirm1, 
                  a.confirm2, 
                  a.confirm3, 
                  a.ledger_id2, 
                  a.group_id
                FROM
                (
                SELECT
                  oc_payment.payment_id,
                  oc_payment.dated,
                  oc_payment.ledger_id,
                  oc_ledger.ledger_name,
                  oc_payment.amount,
                  oc_payment.mode,
                  oc_payment.reference,
                  oc_payment.narration,
                  oc_payment_sub.imgg,
                  oc_payment.confirm1,
                  oc_payment.confirm2,
                  oc_payment.confirm3,
                  oc_payment_sub.ledger_id AS ledger_id2,
                  oc_payment_sub.group_id AS group_id
                FROM
                  oc_payment
                INNER JOIN 
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                INNER JOIN 
                  oc_ledger ON oc_payment.ledger_id = oc_ledger.ledger_id
                WHERE oc_payment_sub.delete_status = 0

                UNION ALL

                SELECT
                  oc_payment.payment_id,
                  oc_payment.dated,
                  oc_payment.ledger_id,
                  oc_ledger.ledger_name,
                  oc_payment.amount,
                  oc_payment.mode,
                  oc_payment.reference,                  
                  oc_payment.narration,
                  '' AS imgg,
                  0 AS confirm1,
                  0 AS confirm2,
                  0 AS confirm3,
                  0 AS ledger_id2,
                  0 AS group_id                  
                FROM
                  oc_payment 
                INNER JOIN oc_ledger ON oc_payment.ledger_id = oc_ledger.ledger_id

                WHERE NOT EXISTS (SELECT * 
                                  FROM  oc_payment_sub
                                  WHERE oc_payment_sub.payment_id = oc_payment.payment_id)
                )a";

                $sql .= " WHERE 1 = 1 ";
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
                if(!empty($data['filter_outflow'])){
                    $sql .= " AND a.ledger_id2 = '".$this->db->escape($data['filter_outflow'])."' ";
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

        $query = $this->db->query($sql);

        //return (int)($query->num_rows);
        return $query->row['num'];

    }

    public function InsertBankPayments($dated, $ledger_id, $amount, $mode, $reference, $user_id, $datedCM, $user_array){

        $sql = "INSERT INTO " . DB_PREFIX . "payment
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
        return $this->db->getLastId();
    }    

    public function getLedgerNameInLedgerTable($ledgerName) {

        $sql = "SELECT COUNT(DISTINCT ledger_id) AS total FROM oc_ledger WHERE ledger_name = '" . $this->db->escape($ledgerName) . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    //public function saveLedger( $ledger, $group_id, $user_id, $datedCM, $user_array) {
    public function saveLedger( $ledger, $group_id, $customer_id, $user_id, $datedCM, $user_array) {

        $sql = "INSERT INTO " . DB_PREFIX . "ledger
                SET ledger_name = '" . $this->db->escape($ledger) . "', 
                    group_id = '" . (int)($group_id) . "',
                    customer_id = '" . (int)($customer_id) . "',
                    user_id = '" . (int)($user_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "'
                    ";

        $this->db->query($sql);
        return $this->db->getLastId(); 
    }    
    public function saveLedgerForEmp( $ledger, $group_id, $emp_code, $user_id, $datedCM, $user_array) {

        $sql = "INSERT INTO " . DB_PREFIX . "ledger
                SET ledger_name = '" . $this->db->escape($ledger) . "', 
                    group_id = '" . (int)($group_id) . "',
                    emp_code = '" . $this->db->escape($emp_code) . "',
                    user_id = '" . (int)($user_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "'
                    ";

        $this->db->query($sql);
        return $this->db->getLastId(); 
    } 
    public function getLedgerIDInLedgerTable($ledgerName) {

        $sql = "SELECT ledger_id FROM oc_ledger WHERE ledger_name = '" . $this->db->escape($ledgerName) . "'";

        $query = $this->db->query($sql);

        return $query->row['ledger_id'];
    }

    public function getGroups() {

        //$sql = "SELECT * FROM " . DB_PREFIX . "group WHERE (group_id = 11 OR group_id = 12 OR group_id = 9 OR group_id = 8 OR group_id = 14 OR group_id = 2 OR group_id = 4 OR group_id = 5 OR group_id = 7) ORDER BY group_name";

        $sql = "SELECT * FROM " . DB_PREFIX . "group WHERE (group_id = 12 OR group_id = 13 OR group_id = 9 OR group_id = 8 OR group_id = 14 OR group_id = 2 OR group_id = 4 OR group_id = 5 OR group_id = 7 OR group_id = 16 OR group_id = 18 OR group_id = 19 OR group_id = 20 OR group_id = 15 OR group_id = 21 OR group_id = 25) ORDER BY group_name";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        }
    }


    public function getLedgers() {
/*
        //$neogrowthChargesLedgerID = 425; //local
        $neogrowthChargesLedgerID = 5580; //live
*/
        $sql = "SELECT
                  ledger_id,
                  ledger_name,
                  group_id
                FROM
                  oc_ledger
                WHERE
                  (group_id = 12 OR group_id = 13 OR group_id = 9 OR group_id = 8 OR group_id = 14 OR group_id = 2 OR group_id = 4 OR group_id = 5 OR group_id = 7 OR group_id = 16 OR group_id = 18 OR group_id = 19 OR group_id = 20 OR group_id = 15 OR group_id = 21 OR group_id = 25)
                AND ledger_id != 7 AND ledger_id != 8 AND ledger_id != 9 AND ledger_id != 5580 AND ledger_id != 13
                ORDER BY ledger_name";

                //  (group_id = 6 OR group_id = 7 OR group_id = 9 OR group_id = 10 OR group_id = 11 OR group_id = 2)
                //  AND ledger_id != 7 AND ledger_id != 8 AND ledger_id != 9

                //(group_id = 11 OR group_id = 12 OR group_id = 9 OR group_id = 8 OR group_id = 14 OR group_id = 2 OR group_id = 4 OR group_id = 5 OR group_id = 7)
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

    public function updatePaymentForNarration( $row_id, $input_value ) {
 
        $sql = "UPDATE " . DB_PREFIX . "payment SET narration= '".$this->db->escape($input_value)."' WHERE payment_id = '". (int)$row_id ."'";        
        $this->db->query($sql);
    }
    public function getAmountOfPayment($payment_id) {

        $sql = "SELECT amount FROM " . DB_PREFIX . "payment
         WHERE payment_id = '" . (int)($payment_id) . "'";

        $query = $this->db->query($sql);

        return $query->row['amount'];
    }

    public function updatePaymentForConfirm( $row_id, $confirm ) {

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName()['username'];
        $confirm_date = date("Y-m-d H:i:s");
        $confirm_user_array = array(
                                'user_id' => $user_id,
                                'user_name' => $user_name,
                                'confirm_date' => $confirm_date
                              );
/*
        $sql = "UPDATE " . DB_PREFIX . "payment 
                SET ".$this->db->escape($confirm)." = 1,
                confirm1_user = '" . $this->db->escape(serialize($confirm_user_array)) . "'   
                WHERE payment_id = '". (int)$row_id ."'";
*/
        $sql = "UPDATE " . DB_PREFIX . "payment 
                SET ".$this->db->escape($confirm)." = 1,
                ".$this->db->escape($confirm)."_user = '" . $this->db->escape(serialize($confirm_user_array)) . "'   
                WHERE payment_id = '". (int)$row_id ."'";

        $this->db->query($sql);
    }

    public function getPaymentSubForPaymentID($payment_id) {

        $sql = "SELECT * FROM " . DB_PREFIX . "payment_sub
         WHERE payment_id = '" . (int)$payment_id . "' 
         AND delete_status = 0";         

        $query = $this->db->query($sql);

        return $query->row;

    }
    public function getPaymentForConfirm($payment_id) {

        $sql = "SELECT * FROM " . DB_PREFIX . "payment
         WHERE payment_id = '" . (int)($payment_id) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }
    public function getLedgersOfGroupID($group_id) {
/*
        //$neogrowthChargesLedgerID = 425; //local
        $neogrowthChargesLedgerID = 5580; //live
*/
        $sql = "SELECT
                  ledger_id,
                  ledger_name,
                  group_id
                FROM " . DB_PREFIX . "ledger
                WHERE group_id = '" . (int)($group_id) . "' AND ledger_id != 7 AND ledger_id != 8 AND ledger_id != 9 AND ledger_id != 5580 AND ledger_id != 13";
        $query = $this->db->query($sql);

        return $query->rows;

    }
    public function insertPaymentSubForLedger( $row_id, $input_value, $group_id, $amount, $file_path, $user_id, $datedCM, $user_array ) {

        $sql = "INSERT INTO " . DB_PREFIX . "payment_sub
                SET payment_id = '" . (int)($row_id) . "',
                    ledger_id = '" . (int)($input_value) . "',
                    group_id = '" . (int)($group_id) . "',
                    amount = '" . (float)($amount) . "',
                    imgg = '" . $this->db->escape($file_path) . "',
                    user_id = '" . (int)($user_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "'
                    ";                    

        $this->db->query($sql);
        return $this->db->getLastId();
    }

    public function insertPaymentSubCSV($row_id, $payment_sub_id, $dated, $ledger_id, $amount, $order_no, $ref, $order_payment_id, $order_id, $ref_id, $ref_tablename) {

        $sql = "INSERT INTO " . DB_PREFIX . "payment_sub_csv
                SET payment_id = '" . (int)($row_id) . "',
                    payment_sub_id = '" . (int)($payment_sub_id) . "', 
                    dated = '" . $this->db->escape($dated) . "', 
                    ledger_id = '" . (int)($ledger_id) . "', 
                    amount = '" . (float)($amount) . "',
                    order_no = '" . $this->db->escape($order_no) . "', 
                    ref = '" . $this->db->escape($ref) . "', 
                    order_payment_id = '" . (int)($order_payment_id) . "', 
                    order_id = '" . (int)($order_id) . "', 
                    ref_id = '" . (int)($ref_id) . "', 
                    ref_tablename = '" . $this->db->escape($ref_tablename) . "' ";

        $this->db->query($sql);
    }

    //public function insertPaymentSubCSV($row_id, $payment_sub_id, $dated, $ledger_id, $amount, $order_no, $ref, $order_payment_id, $order_id) {xxxx
    public function insertPaymentSubIncomesCr($row_id, $payment_sub_id, $dated, $ledger_id, $trxn_amount,$order_no, $ref, $order_payment_id, $order_id, $ref_id, $ref_tablename) {

        $sql = "INSERT INTO " . DB_PREFIX . "payment_sub_incomescr
                SET payment_id = '" . (int)($row_id) . "',
                    payment_sub_id = '" . (int)($payment_sub_id) . "', 
                    dated = '" . $this->db->escape($dated) . "', 
                    ledger_id = '" . (int)($ledger_id) . "', 
                    incomes = '" . (float)($trxn_amount) . "',
                    order_no = '" . $this->db->escape($order_no) . "', 
                    ref = '" . $this->db->escape($ref) . "', 
                    order_payment_id = '" . (int)($order_payment_id) . "', 
                    order_id = '" . (int)($order_id) . "', 
                    ref_id = '" . (int)($ref_id) . "', 
                    ref_tablename = '" . $this->db->escape($ref_tablename) . "' ";                  

        $this->db->query($sql);
    }

    public function insertOcOrderPayment( $order_id, $ref, $order_no, $txn_status, $payment_mode, $amount, $dated, $date_added, $payment_gateway, $successful, $user_id, $tablename, $payment_id, $payment_sub_id) {

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
                    rec_pay_id = '" . (int)($payment_id) . "',
                    rec_pay_sub_id = '" . (int)($payment_sub_id) . "'";
                
        $this->db->query($sql);
        return $this->db->getLastId(); 
    }

    public function updatePaymentSubForLedger( $row_id, $input_value, $amount ) {

        $sql = "UPDATE " . DB_PREFIX . "payment_sub 
                SET ledger_id = '" . (int)($input_value) . "',
                    amount = '" . (float)($amount) . "'
                WHERE payment_id = '". (int)$row_id ."'";

        $this->db->query($sql);
    }

    public function updateReceiptForLedger( $row_id, $input_value, $amount ) {

        $sql = "INSERT INTO " . DB_PREFIX . "receipt_sub
                SET receipt_id = '" . (int)($row_id) . "',
                    ledger_id = '" . (int)($input_value) . "',
                    amount = '" . (float)($amount) . "'";

        $this->db->query($sql);
    }
    public function getPaymentSubForPaymentSubID($payment_id) {//asasdjasdjaskj

        $sql = "SELECT payment_sub_id FROM " . DB_PREFIX . "payment_sub
         WHERE payment_id = '". (int)$payment_id ."' AND delete_status = 0";

        $query = $this->db->query($sql);

        return $query->row['payment_sub_id'];
    }
    public function deletePaymentSub($row_id) {

        $sql = "DELETE FROM " . DB_PREFIX . "payment_sub 
                WHERE payment_id = '". (int)$row_id ."'";
        $this->db->query($sql);
    }  
    public function deletePaymentSubCSV($row_id) {

        $sql_sub_csv = "DELETE FROM " . DB_PREFIX . "payment_sub_csv 
                WHERE payment_id = '". (int)$row_id ."'";
        $this->db->query($sql_sub_csv);        
    }
    public function deleteOcOrderPayment($row_id, $tablename, $txn_status) {

        $sql = "DELETE FROM " . DB_PREFIX . "order_payment 
                WHERE rec_pay_id = '". (int)$row_id ."' 
                AND rec_pay_tablename = '". $this->db->escape($tablename) ."' 
                AND txn_status = '" . $this->db->escape($txn_status) . "'";        

        $this->db->query($sql);        
    }
    public function updatePaymentSub($row_id) {
        // only update whose delete_status = 0
        $sql = "UPDATE " . DB_PREFIX . "payment_sub SET delete_status= 1 WHERE payment_id = '". (int)$row_id ."' AND delete_status = 0";

        $this->db->query($sql);
    }
    public function updatePaymentSubCSV($row_id) {
        // only update whose delete_status = 0
        $sql = "UPDATE " . DB_PREFIX . "payment_sub_csv SET delete_status= 1 WHERE payment_id = '". (int)$row_id ."' AND delete_status = 0";

        $this->db->query($sql);        
    }
    public function updatePaymentSubIncomesCr($row_id) {
        // only update whose delete_status = 0
        $sql = "UPDATE " . DB_PREFIX . "payment_sub_incomescr SET delete_status= 1 WHERE payment_id = '". (int)$row_id ."' AND delete_status = 0";

        $this->db->query($sql);        
    }
    //not in use
    public function updateOcOrderPayment($row_id, $tablename, $txn_status) {
        // only update whose delete_status = 0
        $sql = "UPDATE " . DB_PREFIX . "order_payment SET delete_status= 1, successfull = 0
                WHERE rec_pay_id = '". (int)$row_id ."' 
                AND rec_pay_tablename = '". $this->db->escape($tablename) ."' 
                AND txn_status = '" . $this->db->escape($txn_status) . "'
                AND delete_status = 0";        

        $this->db->query($sql);        
    }
    public function getCustomerNameForPG($ref, $payment_gateway, $successful) {

        $sql = "SELECT
                  oc_order_payment.payment_id,
                  oc_order_payment.order_id,
                  oc_order_payment.merchant_txn_id,
                  oc_order_payment.payment_gateway,
                  oc_order_payment.successfull,
                  oc_order.firstname,
                  oc_order.lastname,
                  oc_order.payment_company,
                  oc_order.customer_id,
                  oc_order.gst_number
                FROM
                  oc_order,
                  oc_order_payment
                WHERE
                  oc_order.order_id = oc_order_payment.order_id 
                  AND oc_order_payment.merchant_txn_id = '" . $this->db->escape($ref) . "' 
                  AND oc_order_payment.payment_gateway = '" . $this->db->escape($payment_gateway) . "' 
                  AND oc_order_payment.successfull = '" . (int)($successful) . "' 
                  AND oc_order.franchise_id = 0 
                  AND oc_order_payment.payment_gateway NOT IN ('coupon','cashback') ";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getCustomerNameForBR($order_no) {

        $sql = "SELECT
                  oo.order_id,
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
        //echo $sql;die;
        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getOcOrderDetail($order_no, $amount) {

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
              Inner JOIN oc_order oo ON oop.order_id = oo.order_id
              WHERE
                oo.order_no LIKE '%" . $this->db->escape($order_no) . "%' 
                AND oo.franchise_id = 0 
                AND oop.payment_gateway NOT IN ('coupon','cashback') ";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getOcOrderDetailForBR($order_no, $amountOfPayment) {

        $sql = "SELECT
                oo.order_id,
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

    public function getPaymentSubCSVByPaymentID($payment_id) {

        $sql = "SELECT *
              FROM
                oc_payment_sub_csv
              WHERE
                payment_id = '" . (int)($payment_id) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }    

    public function deleteAllPayments() {
      return true;
        /*$sql = "Delete FROM " . DB_PREFIX . "payment
                WHERE NOT EXISTS
                  (SELECT * FROM oc_payment_sub WHERE oc_payment_sub.payment_id = oc_payment.payment_id)";

        $this->db->query($sql);*/
    }

    public function getPaymentForRef($reference) {

        //$sql = "SELECT * FROM " . DB_PREFIX . "payment WHERE reference = '" . $this->db->escape($reference) . "'";

        $sql = "SELECT reference FROM oc_receipt WHERE reference = '" . $this->db->escape($reference) . "'";
  
        $sql .= " UNION ALL ";

        $sql .= "SELECT reference FROM oc_payment WHERE reference = '" . $this->db->escape($reference) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }
    public function getPaymentForRef2($reference) {

        $sql = "SELECT reference FROM oc_payment WHERE reference = '" . $this->db->escape($reference) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }
    public function getPaymentSubCSVByOrderNo($ref, $amount, $order_no) {

        $sql = "SELECT * FROM oc_payment_sub_csv WHERE ref = '" . $this->db->escape($ref) . "' AND amount = '" . (float)($amount) . "' AND order_no LIKE '%" . $this->db->escape($order_no) . "%'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getPaymentSubForRef($reference) {

        $sql = "SELECT
                  oc_payment.reference
                FROM
                  oc_payment
                INNER JOIN
                oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                WHERE
                  oc_payment.reference = '" . $this->db->escape($reference) . "' AND oc_payment_sub.delete_status = 0";

        $query = $this->db->query($sql);

        return $query->row;
    }    
    public function getPaymentData($reference) {

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
                op.reference = '" . $this->db->escape($reference) . "'";        

        $query = $this->db->query($sql);

        return $query->row;
    }
    /*
    public function getEmp_CodeFromLedger($emp_code) {

        $sql = "SELECT * FROM oc_ledger WHERE emp_code = '" . $this->db->escape($emp_code) . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }
    */

    public function getEmp_CodeFromLedger($emp_code) {

        $sql = "SELECT COUNT(DISTINCT ledger_id) AS total FROM oc_ledger WHERE emp_code = '" . $this->db->escape($emp_code) . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getSellerPayment3Tables($ref, $trxn_doneSuccess, $trxn_doneRequested, $trxn_bank, $trxn_utr_date) {//utrdate also
      
        //not join with oc_order_payment (mdhrSir)

        $sql = "SELECT
                'oc_seller_invoice' AS tablename,
                osi.seller_invoice_id,
                osi.order_id,
                osi.suborder_id,
                osi.seller_id,
                osi.trxn_done,
                osi.trxn_amount,
                osi.trxn_utr,
                osi.trxn_utr_date,
                osi.trxn_bank,
                osi.seller_invoice_meta,

                oo.order_no
              FROM
                oc_seller_invoice osi
              Inner JOIN oc_order oo ON oo.order_id = osi.order_id
              WHERE
                osi.trxn_utr = '" . $this->db->escape($ref) . "' 
                AND (osi.trxn_done = '" . $this->db->escape($trxn_doneSuccess) . "' 
                  OR osi.trxn_done = '" . $this->db->escape($trxn_doneRequested) . "')
                AND osi.trxn_bank = '" . $this->db->escape($trxn_bank) . "'
                AND osi.trxn_utr_date = '" . $this->db->escape($trxn_utr_date) . "' 
                AND oo.franchise_id = 0 ";

                //AND osi.delete_status = 0";

        $sql .= " UNION ALL ";

        $sql .= "SELECT
                'oc_seller_debit_note' AS tablename,
                osdn.debit_note_id AS seller_invoice_id,
                osdn.order_id,
                osdn.suborder_id,
                osdn.seller_id,
                osdn.trxn_done,
                osdn.trxn_amount,
                osdn.trxn_utr,
                osdn.trxn_utr_date,
                osdn.trxn_bank,
                '' AS seller_invoice_meta,

                oo.order_no
              FROM
                oc_seller_debit_note osdn
              Inner JOIN oc_order oo ON oo.order_id = osdn.order_id
              WHERE
                osdn.trxn_utr = '" . $this->db->escape($ref) . "' 
                AND (osdn.trxn_done = '" . $this->db->escape($trxn_doneSuccess) . "' 
                  OR osdn.trxn_done = '" . $this->db->escape($trxn_doneRequested) . "')
                AND osdn.trxn_bank = '" . $this->db->escape($trxn_bank) . "'
                AND osdn.trxn_utr_date = '" . $this->db->escape($trxn_utr_date) . "' 
                AND oo.franchise_id = 0 ";
                
                //AND osdn.delete_status = 0";

        $sql .= " UNION ALL ";

        $sql .= "SELECT
                'oc_wsb_purchase' AS tablename,
                owp.purchase_id AS seller_invoice_id,
                0 AS order_id,
                0 AS suborder_id,
                owp.seller_id,
                otd.trxn_done,
                otd.trxn_amount,
                otd.trxn_utr,
                otd.trxn_utr_date,
                otd.trxn_bank,
                owp.seller_firm_meta AS seller_invoice_meta,

                'WSB_Purchase' AS order_no
              FROM
                oc_wsb_purchase owp 
              INNER JOIN oc_trxn_details otd ON owp.purchase_id = otd.trxn_for_id AND 
                                                otd.trxn_for = 'WSB_PURCHASE' 
              WHERE
                otd.trxn_utr = '" . $this->db->escape($ref) . "' 
                AND (otd.trxn_done = '" . $this->db->escape($trxn_doneSuccess) . "' 
                  OR otd.trxn_done = '" . $this->db->escape($trxn_doneRequested) . "')
                AND otd.trxn_bank = '" . $this->db->escape($trxn_bank) . "'
                AND otd.trxn_utr_date = '" . $this->db->escape($trxn_utr_date) . "'";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getSellerLedger($customer_id) {

        $sql = "SELECT *
                FROM " . DB_PREFIX . "customer_ledgers
                WHERE customer_id = '" . (int) $customer_id . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function saveSellerLedger( $customer_id, $sellerledgerName ) {

        $sql = "INSERT INTO " . DB_PREFIX . "customer_ledgers
                SET customer_id = '" . (int) $customer_id . "', 
                    ledger_name = '" . $this->db->escape($sellerledgerName) . "'
                    ";

        $this->db->query($sql);
    }

    public function updateCustomerLedger( $ledger_id, $customer_id){ 

        $sql = "UPDATE " . DB_PREFIX . "customer_ledgers 
                SET ledger_id = '". (int)($ledger_id) ."' 
                WHERE customer_id = '". (int)$customer_id ."'";

        $this->db->query($sql);
    }

    public function updateLedger( $ledger_id, $customer_id){ 

        $sql = "UPDATE " . DB_PREFIX . "ledger SET customer_id= '". (int)($customer_id) ."' 
                WHERE ledger_id = '". (int)$ledger_id ."' ";

        $this->db->query($sql);
    }

    public function getAmountByRef($reference) {

        $sql = "SELECT amount FROM " . DB_PREFIX . "payment
         WHERE reference = '" . $this->db->escape($reference) . "'";

        $query = $this->db->query($sql);

        return $query->row['amount'];
    }

    public function getLedgersByNameAndGroup($ledger_name, $group_id) {

        $sql = "SELECT
                  ledger_id,
                  ledger_name,
                  group_id
                FROM " . DB_PREFIX . "ledger
                WHERE
                ledger_name = '" . $this->db->escape($ledger_name) . "'
                AND group_id = '". (int)$group_id ."'";
        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getPaymentByID($payment_id) {

        $sql = "SELECT
                op.payment_id,
                op.dated,
                op.ledger_id,
                op.amount,
                op.reference 
              FROM
                oc_payment op
              WHERE
                op.payment_id = '" . (int)($payment_id) . "'";        

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getReceiptByAmountLedgerID($amount, $ledger_id) {

        $sql = "SELECT
                  orec.receipt_id,
                  orec.dated,
                  orec.ledger_id,
                  ol.ledger_name,
                  orec.amount,
                  orec.mode,
                  orec.reference,
                  orec.narration
                FROM
                  oc_receipt orec
                INNER JOIN
                  oc_ledger ol ON orec.ledger_id = ol.ledger_id
                WHERE orec.amount = '" . (float)($amount) . "'
                AND orec.ledger_id = '" . (int)($ledger_id) . "'
                AND NOT EXISTS
                  (SELECT * FROM oc_receipt_sub WHERE oc_receipt_sub.receipt_id = orec.receipt_id)";

        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getDoneCount() {

        $sql = "SELECT
                  COUNT(*) AS num
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                WHERE
                  oc_payment_sub.delete_status = 0";

        $query = $this->db->query($sql);

        return $query->row['num'];

    }

    public function getPendingCount() {

        $sql = "SELECT
                  COUNT(*) AS num
                FROM
                  oc_payment
                WHERE NOT EXISTS
                  (
                  SELECT * FROM oc_payment_sub
                  WHERE
                    oc_payment_sub.payment_id = oc_payment.payment_id
                )";

        $query = $this->db->query($sql);

        return $query->row['num'];

    }

    public function getRefundFromOcOrderPayment($order_id, 
                                                $merchant_txn_id, 
                                                $amount, 
                                                $payment_gateway) {
      $sql = "SELECT payment_id FROM " . DB_PREFIX . "order_payment 
              WHERE order_id = '" . (int)$order_id . "' 
                AND ( TRIM(merchant_txn_id) = '" . $this->db->escape(trim($merchant_txn_id)) . "'"; 

      // In case of Bank Transfer, auto refunds happen.
      // Sometimes, there is a delay in getting UTR (merchant_txn_id) from bank
      // Although, payment was done successfully. To avoid missing out finding such entries
      // we can also find entries where merchant_txn_id still does not exist
      if ($payment_gateway == 'bank_transfer') {
        $sql .= " OR TRIM(merchant_txn_id) = '' 
                  OR merchant_txn_id IS NULL 
                "; 
      }

      $sql .= " ) 
                AND amount          = " . -1 * abs($amount) . " 
                AND payment_gateway = '" . $this->db->escape($payment_gateway) . "' 
                AND successfull = 1 
                AND (rec_pay_id = 0 OR rec_pay_id IS NULL OR delete_status = 1) 
              LIMIT 1
              ";
      $query = $this->db->query($sql);

      if ($query->num_rows) {
        return (int)$query->row['payment_id'];
      } else {
        return 0;
      }
    }

    /**
    * To insert record in oc_payment_sub and get the last id created and use it further
    * 
    * @author Ashish 4-4-2018
    */
    public function insertOcPaymentSub( $row_id, $input_value, $group_id, $amount, $file_path, $user_id, $datedCM, $user_array ) {

        $sql = "INSERT INTO " . DB_PREFIX . "payment_sub
                SET payment_id = '" . (int)($row_id) . "',
                    ledger_id = '" . (int)($input_value) . "',
                    group_id = '" . (int)($group_id) . "',
                    amount = '" . (float)($amount) . "',
                    imgg = '" . $this->db->escape($file_path) . "',
                    user_id = '" . (int)($user_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "'
                    ";                    

        $this->db->query($sql);
        return $this->db->getLastId();
    }
}
