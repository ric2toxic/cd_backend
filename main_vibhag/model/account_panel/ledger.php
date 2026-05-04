<?php

class ModelAccountPanelLedger extends Model {


    public function getGroups() {
        /*        
        $sql = "SELECT * FROM " . DB_PREFIX . "group 
                WHERE
                  group_id != 1 AND group_id != 2 AND group_id != 3 AND group_id != 4 AND group_id != 5 AND group_id != 7
                  ORDER BY group_name";

        $sql = "SELECT * FROM " . DB_PREFIX . "group 
                WHERE
                  group_id NOT IN (1,2,3,4,5,7)";
        */
        $sql = "SELECT * FROM " . DB_PREFIX . "group 
                WHERE
                  group_id NOT IN (1,2,3,4,7) ORDER BY group_name";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } 
    }

    public function getLedgers($data = array()) {

        $sql = "SELECT ol.ledger_id, 
                        ol.ledger_name, 
                        ol.group_id, 
                        og.group_name, 
                        ol.opening_balance, 
                        ol.drcr, 
                        CONCAT(ol.opening_balance,' ', ol.drcr) AS op_bal,                        
                        ol.user_id, 
                        ol.date_created, 
                        ol.date_modified, 
                        ol.status 
                FROM oc_ledger ol 
                INNER JOIN oc_group og ON ol.group_id = og.group_id ";

        $sql .= " WHERE 1 = 1 ";
        $sql .= " AND ol.ledger_id NOT IN (13) ";
        
        if(!empty($data['filter_ledger_name'])){
            $sql .= " AND ol.ledger_name LIKE'%" .$this->db->escape($data['filter_ledger_name']). "%'";
        }

        $sql .= " ORDER BY ol.ledger_name ";

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
        } 
    }

    public function getLedgersCount($data = array()) {      

        $sql = "SELECT 
                  COUNT(*) AS num
                FROM oc_ledger ol 
                INNER JOIN oc_group og ON ol.group_id = og.group_id ";

        $sql .= " WHERE 1 = 1 ";
        $sql .= " AND ol.ledger_id NOT IN (13) ";
        
        if(!empty($data['filter_ledger_name'])){
            $sql .= " AND ol.ledger_name LIKE'%" .$this->db->escape($data['filter_ledger_name']). "%' ";
        }


        $query = $this->db->query($sql);

        return $query->row['num'];

    }

    public function saveLedger( $ledger, $group_id, $opening_balance, $drcr, $user_id, $datedCM, $user_array ) {
        if (!is_numeric($opening_balance))
        {
            $drcr = "";
        }       
        if($opening_balance != 0 && $drcr == "") {
            $drcr = "Dr";
        }

        $sql = "INSERT INTO " . DB_PREFIX . "ledger
                SET ledger_name = '" . $this->db->escape($ledger) . "', 
                    group_id = '" . $this->db->escape($group_id) . "',
                    opening_balance = '" . $this->db->escape($opening_balance) . "',
                    drcr = '" . $this->db->escape($drcr) . "',
                    user_id = '" . $this->db->escape($user_id) . "',
                    date_created = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "'
                    ";

        $this->db->query($sql);
    }
    
    public function updateLedgerThrGridById( $ledger, $group_id, $opening_balance, $drcr, $user_id, $datedCM, $user_array, $ledgerA ) {

        if (!is_numeric($opening_balance))
        {
            $drcr = "";
        }
        if($opening_balance != 0 && $drcr == "") {
            $drcr = "Dr";
        }

        $sql = "UPDATE " . DB_PREFIX . "ledger SET ledger_name= '".$this->db->escape($ledger)."',group_id= '".$this->db->escape($group_id)."',  opening_balance = '" . $this->db->escape($opening_balance) . "',
                    drcr = '" . $this->db->escape($drcr) . "',
                    user_id = '" . $this->db->escape($user_id) . "',
                    date_modified = '" . $this->db->escape($datedCM) . "',
                    user_detail = '" . $this->db->escape(serialize($user_array)) . "'
                    WHERE ledger_id = '". (int)$ledgerA ."'";

        $this->db->query($sql);
    }    
    public function deleteLedgerById( $id ){
    
        $sql = "DELETE FROM ". DB_PREFIX. "ledger WHERE ledger_id = '". (int)$id ."'";

        $query = $this->db->query($sql);
    }
    public function getLedgerByIdAjax($ledger_id) {

        $sql = "SELECT ol.ledger_id, 
                        ol.ledger_name, 
                        ol.group_id, 
                        og.group_name, 
                        ol.opening_balance, 
                        ol.drcr, 
                        CONCAT(ol.opening_balance,' ', ol.drcr) AS op_bal,                        
                        ol.user_id, 
                        ol.date_created, 
                        ol.date_modified, 
                        ol.status 
                FROM oc_ledger ol 
                INNER JOIN oc_group og ON ol.group_id = og.group_id 
                WHERE ol.ledger_id = '". (int)$ledger_id ."'
                ORDER BY ledger_name";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->row;
        } 
    }
    public function getLedgerByNameAjax($ledger_name) {

        $sql = "SELECT ol.ledger_id, 
                        ol.ledger_name, 
                        ol.group_id, 
                        og.group_name, 
                        ol.opening_balance, 
                        ol.drcr, 
                        CONCAT(ol.opening_balance,' ', ol.drcr) AS op_bal,                        
                        ol.user_id, 
                        ol.date_created, 
                        ol.date_modified, 
                        ol.status 
                FROM oc_ledger ol 
                INNER JOIN oc_group og ON ol.group_id = og.group_id 
                WHERE ol.ledger_name LIKE '%". $this->db->escape($ledger_name) ."%'
                ORDER BY ledger_name
                LIMIT 10";
//echo $sql;die;
                //WHERE ol.ledger_id = '". (int)$ledger_id ."'
                //WHERE ol.ledger_id LIKE '%". (int)$ledger_id ."%'

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } 
    }

    public function getClientLedgerById( $data = array()) {
//echo "<pre>";print_r($data);die;
        $sql = "SELECT a.dated, a.ledger_id, a.narration, a.reference, a.order_no, a.ledger_id2, a.ledger_name, a.Dr, a.Cr
                FROM
                (
                SELECT
                  oc_receipt.dated,
                  oc_receipt.ledger_id,
                  oc_receipt.narration,
                  oc_receipt.reference,
                  '' AS order_no,
                  oc_receipt_sub.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  oc_receipt_sub.amount AS Dr,
                  0 AS Cr
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id
                WHERE
                  oc_receipt.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_receipt_sub.delete_status = 0
                UNION ALL
                SELECT
                  oc_receipt.dated,
                  oc_receipt_sub.ledger_id,
                  oc_receipt.narration,
                  oc_receipt.reference,
                  '' AS order_no,
                  oc_receipt.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  0 AS Dr,
                  oc_receipt_sub.amount AS Cr
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt.ledger_id = oc_ledger.ledger_id
                WHERE
                  oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_receipt_sub.delete_status = 0
                UNION ALL
                SELECT
                  oc_receipt_sub_csv.dated,
                  oc_receipt_sub.ledger_id,
                  oc_receipt.narration,
                  oc_receipt.reference,
                  oc_receipt_sub_csv.order_no,
                  oc_receipt_sub_csv.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  oc_receipt_sub_csv.amount AS Dr,
                  0 AS Cr
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_receipt_sub_csv ON oc_receipt_sub.receipt_id = oc_receipt_sub_csv.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt_sub_csv.ledger_id = oc_ledger.ledger_id
                WHERE
                  oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."' 
                  AND oc_receipt_sub.delete_status = 0 AND oc_receipt_sub_csv.delete_status = 0
                UNION ALL
                SELECT
                  oc_receipt_sub_csv.dated,
                  oc_receipt_sub_csv.ledger_id,
                  oc_receipt.narration,
                  oc_receipt.reference,
                  oc_receipt_sub_csv.order_no,
                  oc_receipt_sub.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  0 AS Dr,
                  oc_receipt_sub_csv.amount AS Cr
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_receipt_sub_csv ON oc_receipt_sub_csv.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id
                WHERE
                  oc_receipt_sub_csv.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_receipt_sub.delete_status = 0 AND oc_receipt_sub_csv.delete_status = 0
                UNION ALL
                SELECT
                  oc_receipt_sub_chargesdr.dated,
                  oc_receipt_sub.ledger_id,
                  oc_receipt.narration,
                  oc_receipt.reference,
                  oc_receipt_sub_chargesdr.order_no,
                  oc_receipt_sub_chargesdr.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  0 AS Dr,
                  oc_receipt_sub_chargesdr.charges AS Cr
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
                INNER JOIN
                  oc_receipt_sub_chargesdr ON oc_receipt_sub_chargesdr.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_receipt_sub_chargesdr.ledger_id
                WHERE
                  oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_receipt_sub.delete_status = 0 AND oc_receipt_sub_chargesdr.delete_status = 0
                UNION ALL
                SELECT
                  oc_receipt_sub_chargesdr.dated,
                  oc_receipt_sub_chargesdr.ledger_id,
                  oc_receipt.narration,
                  oc_receipt.reference,
                  oc_receipt_sub_chargesdr.order_no,
                  oc_receipt_sub.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  oc_receipt_sub_chargesdr.charges AS Dr,
                  0 AS Cr
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
                INNER JOIN
                  oc_receipt_sub_chargesdr ON oc_receipt_sub_chargesdr.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_receipt_sub.ledger_id
                WHERE
                  oc_receipt_sub_chargesdr.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_receipt_sub.delete_status = 0 AND oc_receipt_sub_chargesdr.delete_status = 0
                UNION ALL
                SELECT
                  oc_payment.dated,
                  oc_payment.ledger_id,
                  oc_payment.narration,
                  oc_payment.reference,
                  '' AS order_no,
                  oc_payment_sub.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  0 AS Dr,
                  oc_payment_sub.amount AS Cr
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
                WHERE
                  oc_payment.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_payment_sub.delete_status = 0
                UNION ALL
                SELECT
                  oc_payment.dated,
                  oc_payment_sub.ledger_id,
                  oc_payment.narration,
                  oc_payment.reference,
                  '' AS order_no,
                  oc_payment.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  oc_payment_sub.amount AS Dr,
                  0 AS Cr
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment.ledger_id
                WHERE
                  oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_payment_sub.delete_status = 0
                UNION ALL
                SELECT
                  oc_payment_sub_csv.dated,
                  oc_payment_sub.ledger_id,
                  oc_payment.narration,
                  oc_payment.reference,
                  oc_payment_sub_csv.order_no,
                  oc_payment_sub_csv.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  0 AS Dr,
                  oc_payment_sub_csv.amount AS Cr
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                INNER JOIN
                  oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_csv.ledger_id
                WHERE
                  oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_payment_sub.delete_status = 0 AND oc_payment_sub_csv.delete_status = 0
                UNION ALL
                SELECT
                  oc_payment_sub_csv.dated,
                  oc_payment_sub_csv.ledger_id,
                  oc_payment.narration,
                  oc_payment.reference,
                  oc_payment_sub_csv.order_no,
                  oc_payment_sub.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  oc_payment_sub_csv.amount AS Dr,
                  0 AS Cr
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
                WHERE
                  oc_payment_sub_csv.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_payment_sub.delete_status = 0 AND oc_payment_sub_csv.delete_status = 0
                UNION ALL
                SELECT
                  oc_payment_sub_incomescr.dated,
                  oc_payment_sub.ledger_id,
                  oc_payment.narration,
                  oc_payment.reference,
                  oc_payment_sub_incomescr.order_no,
                  oc_payment_sub_incomescr.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  oc_payment_sub_incomescr.incomes AS Dr,
                  0 AS Cr
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                INNER JOIN
                  oc_payment_sub_incomescr ON oc_payment_sub_incomescr.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_incomescr.ledger_id
                WHERE
                  oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_payment_sub.delete_status = 0 AND oc_payment_sub_incomescr.delete_status = 0
                UNION ALL
                SELECT
                  oc_payment_sub_incomescr.dated,
                  oc_payment_sub_incomescr.ledger_id,
                  oc_payment.narration,
                  oc_payment.reference,
                  oc_payment_sub_incomescr.order_no,
                  oc_payment_sub.ledger_id AS ledger_id2,
                  oc_ledger.ledger_name,
                  0 AS Dr,
                  oc_payment_sub_incomescr.incomes AS Cr
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_payment_sub_incomescr ON oc_payment_sub_incomescr.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
                WHERE
                  oc_payment_sub_incomescr.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                  AND oc_payment_sub.delete_status = 0 AND oc_payment_sub_incomescr.delete_status = 0
                )a";

        $sql .= " WHERE 1 = 1 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND a.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND a.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND a.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND a.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
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

//echo $sql;die;
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } 
    }

    public function getClientLedgerByIdCount($data = array()) {      

        $sql = "SELECT SUM(a.num) AS num
                FROM
                (
                SELECT
                    COUNT(*) as num
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id ";
               
        $sql .= " WHERE
                oc_receipt.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_receipt.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }

        $sql .= " UNION ALL ";
        
        $sql .= " SELECT
                    COUNT(*) as num
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt.ledger_id = oc_ledger.ledger_id ";

        $sql .= " WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_receipt.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }  

        $sql .= " UNION ALL ";

        $sql .= " SELECT
                    COUNT(*) as num
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_receipt_sub_csv ON oc_receipt_sub.receipt_id = oc_receipt_sub_csv.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt_sub_csv.ledger_id = oc_ledger.ledger_id ";

        $sql .= " WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0
                AND oc_receipt_sub_csv.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt_sub_csv.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt_sub_csv.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_receipt.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND oc_receipt_sub_csv.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
                }

        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_receipt_sub_csv ON oc_receipt_sub_csv.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id ";

        $sql .= " WHERE
                oc_receipt_sub_csv.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0
                AND oc_receipt_sub_csv.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt_sub_csv.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt_sub_csv.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_receipt.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND oc_receipt_sub_csv.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
                }
                  
        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
                INNER JOIN
                  oc_receipt_sub_chargesdr ON oc_receipt_sub_chargesdr.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_receipt_sub_chargesdr.ledger_id ";

        $sql .= " WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 
                AND oc_receipt_sub_chargesdr.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_receipt.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
                }
                  
        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
                INNER JOIN
                  oc_receipt_sub_chargesdr ON oc_receipt_sub_chargesdr.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_receipt_sub.ledger_id ";

        $sql .= " WHERE
                oc_receipt_sub_chargesdr.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 
                AND oc_receipt_sub_chargesdr.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_receipt.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
                }
                  
        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id ";

        $sql .= " WHERE
                oc_payment.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                  
        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment.ledger_id ";

        $sql .= " WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                  
        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                INNER JOIN
                  oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_csv.ledger_id ";

        $sql .= " WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_csv.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment_sub_csv.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment_sub_csv.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_payment.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND oc_payment_sub_csv.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
                }
                  
        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id ";

        $sql .= " WHERE
                oc_payment_sub_csv.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_csv.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment_sub_csv.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment_sub_csv.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_payment.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND oc_payment_sub_csv.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
                }
                  
        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                INNER JOIN
                  oc_payment_sub_incomescr ON oc_payment_sub_incomescr.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_incomescr.ledger_id ";

        $sql .= " WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_incomescr.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment_sub_incomescr.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment_sub_incomescr.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_payment.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND oc_payment_sub_incomescr.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
                }
                  
        $sql .= " UNION ALL ";

        $sql .= "SELECT
                    COUNT(*) as num
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_payment_sub_incomescr ON oc_payment_sub_incomescr.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id";

        $sql .= " WHERE
                oc_payment_sub_incomescr.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_incomescr.delete_status = 0 ";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment_sub_incomescr.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment_sub_incomescr.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                if(!empty($data['filter_ref'])){
                    $sql .= " AND oc_payment.reference LIKE'%" .$this->db->escape($data['filter_ref']). "%'";
                }                
                if(!empty($data['filter_order_no'])){
                    $sql .= " AND oc_payment_sub_incomescr.order_no LIKE '%" .$this->db->escape($data['filter_order_no']). "%'";
                }

        $sql  .=")a";
//echo $sql;die;
        $query = $this->db->query($sql);

        return $query->row['num'];
    }

    public function getClientLedgerByIdOpBal( $data = array()) {

        $sql = "SELECT SUM(a.Dr) AS Dr, SUM(a.Cr) AS Cr, SUM(a.Dr)-SUM(a.Cr) AS Bal
                FROM
                (";

        $sql .= "  SELECT
                    IF(
                      ol.opening_balance > 0 AND ol.drcr = 'dr',
                      ol.opening_balance,
                      0
                    ) AS Dr,
                    IF(
                      ol.opening_balance > 0 AND ol.drcr = 'cr',
                      ol.opening_balance,
                      0
                    ) AS Cr
                  FROM
                    oc_ledger ol
                  WHERE
                    ol.ledger_id = '".$this->db->escape($data['ledger_id'])."' ";

        $sql .= " UNION ALL ";

        $sql .= "SELECT
                SUM(oc_receipt_sub.amount) AS Dr,
                0 AS Cr
              FROM
                 oc_receipt
              INNER JOIN
                 oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id
              WHERE
                oc_receipt.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 ";
                
        $sql .= " AND oc_receipt.dated < '".$this->db->escape($data['filter_date_from'])."' ";

        $sql .= " UNION ALL ";
        
        $sql .= "SELECT
                0 AS Dr,
                SUM(oc_receipt_sub.amount) AS Cr
              FROM
                  oc_receipt
              INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_receipt.ledger_id = oc_ledger.ledger_id
              WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 ";

        $sql .= " AND oc_receipt.dated < '".$this->db->escape($data['filter_date_from'])."' ";

        $sql .= " UNION ALL ";

        $sql .= "SELECT
                SUM(oc_receipt_sub_csv.amount) AS Dr,
                0 AS Cr
              FROM
                oc_receipt
              INNER JOIN
                oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_receipt_sub_csv ON oc_receipt_sub.receipt_id = oc_receipt_sub_csv.receipt_id
              INNER JOIN
                oc_ledger ON oc_receipt_sub_csv.ledger_id = oc_ledger.ledger_id
              WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."' 
                AND oc_receipt_sub.delete_status = 0
                AND oc_receipt_sub_csv.delete_status = 0 ";

        $sql .= " AND oc_receipt_sub_csv.dated < '".$this->db->escape($data['filter_date_from'])."' ";

          $sql .= " UNION ALL ";
          
          $sql .= "SELECT
                0 AS Dr,
                SUM(oc_receipt_sub_csv.amount) AS Cr
              FROM
                oc_receipt
              INNER JOIN
                oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id 
              INNER JOIN
                oc_receipt_sub_csv ON oc_receipt_sub_csv.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id
              WHERE
                oc_receipt_sub_csv.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0
                AND oc_receipt_sub_csv.delete_status = 0 ";

        $sql .= " AND oc_receipt_sub_csv.dated < '".$this->db->escape($data['filter_date_from'])."' ";
                              
            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                0 AS Dr,
                SUM(oc_receipt_sub_chargesdr.charges) AS Cr
              FROM
                oc_receipt
              INNER JOIN
                oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
              INNER JOIN
                oc_receipt_sub_chargesdr ON oc_receipt_sub_chargesdr.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_receipt_sub_chargesdr.ledger_id
              WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 
                AND oc_receipt_sub_chargesdr.delete_status = 0 ";

            $sql .= " AND oc_receipt_sub_chargesdr.dated < '".$this->db->escape($data['filter_date_from'])."' ";

            $sql .= " UNION ALL ";

            $sql .= "SELECT
                SUM(oc_receipt_sub_chargesdr.charges) AS Dr,
                0 AS Cr
              FROM
                oc_receipt
              INNER JOIN
                oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
              INNER JOIN
                oc_receipt_sub_chargesdr ON oc_receipt_sub_chargesdr.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_receipt_sub.ledger_id 
              WHERE
                oc_receipt_sub_chargesdr.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 
                AND oc_receipt_sub_chargesdr.delete_status = 0 ";

            $sql .= " AND oc_receipt_sub_chargesdr.dated < '".$this->db->escape($data['filter_date_from'])."' ";

            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                0 AS Dr,
                SUM(oc_payment_sub.amount) AS Cr
              FROM
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
              INNER JOIN  
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
              WHERE
                oc_payment.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0 ";

            $sql .= " AND oc_payment.dated < '".$this->db->escape($data['filter_date_from'])."' ";

            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                SUM(oc_payment_sub.amount) AS Dr,
                0 AS Cr
              FROM
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id  
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment.ledger_id
              WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0 ";

            $sql .= " AND oc_payment.dated < '".$this->db->escape($data['filter_date_from'])."' ";

            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                0 AS Dr,
                SUM(oc_payment_sub_csv.amount) AS Cr
              FROM
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
              INNER JOIN
                oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_csv.ledger_id
              WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_csv.delete_status = 0 ";

            $sql .= " AND oc_payment_sub_csv.dated < '".$this->db->escape($data['filter_date_from'])."' ";

            $sql .= " UNION ALL ";  
            
            $sql .= "SELECT
                SUM(oc_payment_sub_csv.amount) AS Dr,
                0 AS Cr
              FROM 
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
              WHERE
                oc_payment_sub_csv.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_csv.delete_status = 0 ";

            $sql .= " AND oc_payment_sub_csv.dated < '".$this->db->escape($data['filter_date_from'])."' ";

            $sql .= " UNION ALL ";

            $sql .= "SELECT
                SUM(oc_payment_sub_incomescr.incomes) AS Dr,
                0 AS Cr
              FROM
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
              INNER JOIN
                oc_payment_sub_incomescr ON oc_payment_sub_incomescr.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_incomescr.ledger_id
              WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_incomescr.delete_status = 0 ";

            $sql .= " AND oc_payment_sub_incomescr.dated < '".$this->db->escape($data['filter_date_from'])."' ";

            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                0 AS Dr,
                SUM(oc_payment_sub_incomescr.incomes) AS Cr
              FROM 
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_payment_sub_incomescr ON oc_payment_sub_incomescr.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
              WHERE
                oc_payment_sub_incomescr.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_incomescr.delete_status = 0 ";

            $sql .= " AND oc_payment_sub_incomescr.dated < '".$this->db->escape($data['filter_date_from'])."' ";

            $sql .= ")a";

        //$sql .= " ORDER BY dated ";
//echo $sql;die;
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->row;
        } 
    }
 
    public function getClientLedgerByIdClBal( $data = array()) {

        $sql = "SELECT SUM(a.Dr) AS Dr, SUM(a.Cr) AS Cr, SUM(a.Dr)-SUM(a.Cr) AS Bal
                FROM
                (";

        $sql .= "SELECT
                SUM(oc_receipt_sub.amount) AS Dr,
                0 AS Cr
              FROM
                 oc_receipt
              INNER JOIN
                 oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id
              WHERE
                oc_receipt.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

        $sql .= " UNION ALL ";
        
        $sql .= "SELECT
                0 AS Dr,
                SUM(oc_receipt_sub.amount) AS Cr
              FROM
                  oc_receipt
              INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_receipt.ledger_id = oc_ledger.ledger_id
              WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

        $sql .= " UNION ALL ";

        $sql .= "SELECT
                SUM(oc_receipt_sub_csv.amount) AS Dr,
                0 AS Cr
              FROM
                oc_receipt
              INNER JOIN
                oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_receipt_sub_csv ON oc_receipt_sub.receipt_id = oc_receipt_sub_csv.receipt_id
              INNER JOIN
                oc_ledger ON oc_receipt_sub_csv.ledger_id = oc_ledger.ledger_id
              WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."' 
                AND oc_receipt_sub.delete_status = 0
                AND oc_receipt_sub_csv.delete_status = 0";
                
                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt_sub_csv.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt_sub_csv.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

          $sql .= " UNION ALL ";
          
          $sql .= "SELECT
                0 AS Dr,
                SUM(oc_receipt_sub_csv.amount) AS Cr
              FROM
                oc_receipt
              INNER JOIN
                oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id 
              INNER JOIN
                oc_receipt_sub_csv ON oc_receipt_sub_csv.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id
              WHERE
                oc_receipt_sub_csv.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0
                AND oc_receipt_sub_csv.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt_sub_csv.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt_sub_csv.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }
                              
            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                0 AS Dr,
                SUM(oc_receipt_sub_chargesdr.charges) AS Cr
              FROM
                oc_receipt
              INNER JOIN
                oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
              INNER JOIN
                oc_receipt_sub_chargesdr ON oc_receipt_sub_chargesdr.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_receipt_sub_chargesdr.ledger_id
              WHERE
                oc_receipt_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 
                AND oc_receipt_sub_chargesdr.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

            $sql .= " UNION ALL ";

            $sql .= "SELECT
                SUM(oc_receipt_sub_chargesdr.charges) AS Dr,
                0 AS Cr
              FROM
                oc_receipt
              INNER JOIN
                oc_receipt_sub ON oc_receipt_sub.receipt_id = oc_receipt.receipt_id
              INNER JOIN
                oc_receipt_sub_chargesdr ON oc_receipt_sub_chargesdr.receipt_id = oc_receipt_sub.receipt_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_receipt_sub.ledger_id 
              WHERE
                oc_receipt_sub_chargesdr.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_receipt_sub.delete_status = 0 
                AND oc_receipt_sub_chargesdr.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_receipt_sub_chargesdr.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                0 AS Dr,
                SUM(oc_payment_sub.amount) AS Cr
              FROM
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
              INNER JOIN  
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
              WHERE
                oc_payment.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                SUM(oc_payment_sub.amount) AS Dr,
                0 AS Cr
              FROM
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id  
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment.ledger_id
              WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                0 AS Dr,
                SUM(oc_payment_sub_csv.amount) AS Cr
              FROM
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
              INNER JOIN
                oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_csv.ledger_id
              WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_csv.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment_sub_csv.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment_sub_csv.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

            $sql .= " UNION ALL ";  
            
            $sql .= "SELECT
                SUM(oc_payment_sub_csv.amount) AS Dr,
                0 AS Cr
              FROM 
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
              WHERE
                oc_payment_sub_csv.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_csv.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment_sub_csv.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment_sub_csv.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

            $sql .= " UNION ALL ";

            $sql .= "SELECT
                SUM(oc_payment_sub_incomescr.incomes) AS Dr,
                0 AS Cr
              FROM
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
              INNER JOIN
                oc_payment_sub_incomescr ON oc_payment_sub_incomescr.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_incomescr.ledger_id
              WHERE
                oc_payment_sub.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_incomescr.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment_sub_incomescr.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment_sub_incomescr.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

            $sql .= " UNION ALL ";
            
            $sql .= "SELECT
                0 AS Dr,
                SUM(oc_payment_sub_incomescr.incomes) AS Cr
              FROM 
                oc_payment
              INNER JOIN
                oc_payment_sub ON oc_payment.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_payment_sub_incomescr ON oc_payment_sub_incomescr.payment_id = oc_payment_sub.payment_id
              INNER JOIN
                oc_ledger ON oc_ledger.ledger_id = oc_payment_sub.ledger_id
              WHERE
                oc_payment_sub_incomescr.ledger_id = '".$this->db->escape($data['ledger_id'])."'
                AND oc_payment_sub.delete_status = 0
                AND oc_payment_sub_incomescr.delete_status = 0";

                if(!empty($data['filter_date_from'])){
                    $sql .= " AND oc_payment_sub_incomescr.dated >= '".$this->db->escape($data['filter_date_from'])."' ";
                }
                if(!empty($data['filter_date_to'])){
                    $sql .= " AND oc_payment_sub_incomescr.dated <= '".$this->db->escape($data['filter_date_to'])."' ";
                }

            $sql .= ")a";

        //$sql .= " ORDER BY dated ";
//echo $sql;die;
        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->row;
        } 
    }

    public function getLedgerIDInReceiptTablex($ledgerid) {

        //$sql = "SELECT COUNT(DISTINCT ledger_id) AS total FROM oc_ledger WHERE ledger_name = '" . $this->db->escape($ledgerName) . "'";
        $sql = "SELECT COUNT(ledger_id) AS total FROM oc_receipt WHERE ledger_id = '" . $this->db->escape($ledgerid) . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    } 
    public function getLedgerIDInAllTable($ledgerid, $tablename) {

        $sql = "SELECT COUNT(ledger_id) AS total FROM " . $this->db->escape($tablename) . " WHERE ledger_id = '" . $this->db->escape($ledgerid) . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
    
    public function getLedgerNameInLedgerTable($ledgerName) {

        $sql = "SELECT COUNT(DISTINCT ledger_id) AS total FROM oc_ledger WHERE ledger_name = '" . $this->db->escape($ledgerName) . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    } 



    public function getSubOrders() {
//$order_no = '20170919551';
$order_no = '20170902840';
$order_id = '20741';
$order_payment_id = '15615';
$amount = '13791.00';
/*
        $sql = "SELECT 
                    oo.order_id,
                    oo.order_no,
                    oo.date_added,
                    oo.firstname,
                    oo.lastname,
                    oo.payment_company,
                    oo.customer_id
                FROM ". DB_PREFIX ."order as oo
                WHERE 
                oo.order_no LIKE '%" . $this->db->escape($order_no) . "%'";
                
                $sql .= " ORDER BY oo.order_id DESC ";
*/

        $sql = "SELECT
                  os.suborder_id,
                  oo.order_id,
                  oo.order_no,
                  oo.date_added,
                  oo.firstname,
                  oo.lastname,
                  oo.payment_company,
                  oo.customer_id
                FROM
                  oc_suborder AS os
                INNER JOIN oc_order oo ON os.order_id = oo.order_id
                
                WHERE oo.order_id >= 20740 
                AND oo.order_id <= 20790 
                AND oo.franchise_id = 0 ";

                $sql .= " ORDER BY oo.order_id DESC ";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getReceiptByOrderId( $order_id ) {
// $order_no = '20170902840';
// $order_id = '20741';
// $order_payment_id = '15615';
// $amount = '13791.00';
/*
        $sql = "SELECT
                  oc_receipt_sub_csv.dated,
                  oc_receipt_sub_csv.ledger_id,
                  oc_receipt_sub_csv.order_payment_id,
                  oc_receipt_sub_csv.order_id,
                  oc_receipt_sub_csv.order_no,
                  oc_receipt_sub_csv.amount
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_receipt_sub_csv ON oc_receipt_sub_csv.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id
                WHERE
                  oc_receipt_sub_csv.order_id = '" . $this->db->escape($order_id) . "' AND oc_receipt_sub.delete_status = 0 AND oc_receipt_sub_csv.delete_status = 0";

                  //oc_receipt_sub_csv.ledger_id = '714' AND oc_receipt_sub.delete_status = 0 AND oc_receipt_sub_csv.delete_status = 0";

echo $sql;die;
*/

        $sql = "SELECT
                  SUM(oc_receipt_sub_csv.amount) AS amountRec
                FROM
                  oc_receipt
                INNER JOIN
                  oc_receipt_sub ON oc_receipt.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_receipt_sub_csv ON oc_receipt_sub_csv.receipt_id = oc_receipt_sub.receipt_id
                INNER JOIN
                  oc_ledger ON oc_receipt_sub.ledger_id = oc_ledger.ledger_id
                WHERE
                  oc_receipt_sub_csv.order_id = '" . $this->db->escape($order_id) . "' AND oc_receipt_sub.delete_status = 0 AND oc_receipt_sub_csv.delete_status = 0";

        $sql .=" GROUP BY oc_receipt_sub_csv.order_id";

        $query = $this->db->query($sql);

        

        if ( $query->num_rows ) {
            //return $query->rows;
            return $query->row['amountRec'];//die;
        } 

    }

    public function getPaymentByOrderId( $order_id ) {
// $order_no = '20170902840';
// $order_id = '20741';
// $order_payment_id = '15615';
// $amount = '13791.00';
/*
        $sql = "SELECT
                  oc_payment_sub_csv.dated,
                  oc_payment_sub.ledger_id,
                  oc_payment.narration,
                  oc_payment.reference,
                  oc_payment_sub_csv.order_no,
                  oc_payment_sub_csv.ledger_id,
                  oc_ledger.ledger_name,
                  0 AS Dr,
                  oc_payment_sub_csv.amount AS Cr
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                INNER JOIN
                  oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_csv.ledger_id
                WHERE
                  oc_payment_sub_csv.order_id = '" . $this->db->escape($order_id) . "' AND oc_payment_sub.delete_status = 0 AND oc_payment_sub_csv.delete_status = 0";

                  //oc_payment_sub.ledger_id = '4' AND oc_payment_sub.delete_status = 0 AND oc_payment_sub_csv.delete_status = 0";

echo $sql;die;
*/

        $sql = "SELECT
                  SUM(oc_payment_sub_csv.amount) AS amountPay
                FROM
                  oc_payment
                INNER JOIN
                  oc_payment_sub ON oc_payment_sub.payment_id = oc_payment.payment_id
                INNER JOIN
                  oc_payment_sub_csv ON oc_payment_sub_csv.payment_id = oc_payment_sub.payment_id
                INNER JOIN
                  oc_ledger ON oc_ledger.ledger_id = oc_payment_sub_csv.ledger_id
                WHERE
                  oc_payment_sub_csv.order_id = '" . $this->db->escape($order_id) . "' AND oc_payment_sub.delete_status = 0 AND oc_payment_sub_csv.delete_status = 0";

        $sql .=" GROUP BY oc_payment_sub_csv.order_id";
//echo $sql;die;
        $query = $this->db->query($sql);
//print_r($query);die;

        if ( $query->num_rows ) {
            //return $query->rows;
            return $query->row['amountPay'];//die;
        } 
    }

    public function getCreditNoteAmtBySubOrderId( $suborder_id ) {

        $sql = "SELECT
                  SUM(ocn.credit_note_amount) AS crNoteAmt
                FROM
                  oc_credit_note ocn
                WHERE
                  ocn.suborder_id = '" . $this->db->escape($suborder_id) . "'";

        $sql .=" GROUP BY ocn.suborder_id";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            //return $query->rows;
            return $query->row['crNoteAmt'];//die;
        } 
    }




}
