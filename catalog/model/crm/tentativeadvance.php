<?php
class ModelCrmTentativeadvance extends Model {

    public $db_crm;
    public $db;
    function __construct()
    {
        //CRM Database
        $this->db_crm = new Database\DB( DBCRM_SERVERS );
        //main site database
        $this->db = new Database\DB( DB_SERVERS );

    }

    public function getUserSettings($user_id) {
        $sql = "SELECT * FROM user_settings 
        WHERE user_id = '" . $user_id . "'";

        $query = $this->db_crm->query($sql);
        $user_settings['user_id'] =  $user_id;
        if (!empty($query->rows)) {
            foreach ($query->rows as $row) {
                $user_settings['settings'] = array(

                    'key_name'   => $row['key_name'],
                    'key_value' => $row['key_value']

                    );
            }
        }

        return $user_settings;
    }

    public function userValidation($crm_user_id, $access_token) {

        $sql = "SELECT * FROM users WHERE id = '" . $this->db_crm->escape($crm_user_id) . "' AND access_token = '" . $this->db_crm->escape($access_token) . "'";

        $query = $this->db_crm->query($sql);

        if($query->num_rows){

            return true;
        }

        return false;
    }

    public function getStaff($staff_id) {

        $sql = "SELECT * FROM oc_sales_staff WHERE staff_id = '" . $this->db->escape($staff_id) . "'";
        //$sql = "SELECT * FROM oc_sales_staff WHERE staff_id = '" . $staff_id . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getTentativeAdvances() {

        $sql = "SELECT
                  tentative_advance_id,
                  order_id,
                  payment_mode,
                  cheque_no,
                  collection_date,
                  
                  txn_id,
                  
                  dated,
                  amount,
                  notes,
                  order_payment_id,
                  msgadmin,
                  bank_deposited,
                  bank_deposited_image,
                  branch_name,
                  staff_id,
                  date_created,
                  staff_detail,
                  confirm,
                  confirm_user,
                  transaction_status
                FROM
                  oc_tentative_advance 
                ORDER BY date_created DESC";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } else {
          return array();
        }     
    }

    public function getTentativeAdvancesDateWise($staff_id, $order_id, $filter_date_from, $filter_date_to) {

        $sql = "SELECT
                  tentative_advance_id,
                  order_id,
                  payment_mode,
                  cheque_no,
                  collection_date,
                  
                  txn_id,
                  
                  dated,
                  amount,
                  notes,
                  order_payment_id,
                  msgadmin,
                  bank_deposited,
                  bank_deposited_image,
                  branch_name,
                  staff_id,
                  date_created,
                  staff_detail,
                  confirm,
                  confirm_user,
                  transaction_status
                FROM
                  oc_tentative_advance";

        $sql .= " WHERE 1 = 1 ";
        if(!empty($staff_id)){
            $sql .= " AND staff_id = " .$this->db->escape($staff_id) ;
        }
        if(!empty($order_id)){
            $sql .= " AND order_id = " .$this->db->escape($order_id) ;
        }
        //WHERE a.dated>='2016-02-02' AND a.dated<='2016-04-14'
        if(!empty($filter_date_from)){
            //$sql .= " AND date_created >= '".$this->db->escape($filter_date_from)."' ";
            $sql .= " AND dated >= '".$this->db->escape($filter_date_from)."' ";
        }
        if(!empty($filter_date_to)){
            //$sql .= " AND date_created <= '".$this->db->escape($filter_date_to)."' ";
            $sql .= " AND dated <= '".$this->db->escape($filter_date_to)."' ";
        }

        $sql .= " ORDER BY dated DESC";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->rows;
        } else {
          return array();
        }     
    }

    public function insertTentativeAdvance( $order_id, $payment_mode, $cheque_no, $collection_date, $txn_id, $dated, $amount, $notes, $bank_deposited, $bank_deposited_image, $branch_name, $staff_id, $datedCM, $staff_array,$transaction_status ) {

    $sql = "INSERT INTO " . DB_PREFIX . "tentative_advance
               SET order_id = '" . $this->db->escape($order_id) . "',
                   payment_mode = '" . $this->db->escape($payment_mode) . "',
                   cheque_no = '" . $this->db->escape($cheque_no) . "',
                   collection_date = '" . $this->db->escape($collection_date) . "',
                   
                   txn_id = '" . $this->db->escape($txn_id) . "',
                   
                   dated = '" . $this->db->escape($dated) . "',
                   amount = '" . $this->db->escape($amount) . "',
                   notes = '" . $this->db->escape($notes) . "',
                   bank_deposited = '" . $this->db->escape($bank_deposited) . "',
                   bank_deposited_image = '" . $this->db->escape($bank_deposited_image) . "',
                   branch_name = '" . $this->db->escape($branch_name) . "',
                   staff_id = '" . $this->db->escape($staff_id) . "',
                   date_created = '" . $this->db->escape($datedCM) . "',
                   staff_detail = '" . $this->db->escape(serialize($staff_array)) . "',
                   transaction_status = '" . $this->db->escape($transaction_status) . "'
                   ";  

       $this->db->query($sql);
       
       return $this->db->getLastId(); 

   }

    public function updateTentativeAdvanceForBankDepositedById( $bank_deposited, $bank_deposited_image, $tentative_advance_id ) {

        $sql = "UPDATE " . DB_PREFIX . "tentative_advance
                 SET bank_deposited = '". (int)$bank_deposited ."',
                 bank_deposited_image = '" . $this->db->escape($bank_deposited_image) . "'
                WHERE tentative_advance_id = '". (int)$tentative_advance_id ."'";

        $this->db->query($sql);
    }

    public function updateTentativeAdvanceByID($tentative_advance_id, $confirm, $datedCM, $staff_array) {

        $sql = "UPDATE " . DB_PREFIX . "tentative_advance
                 SET confirm = '". (int)$confirm ."',
                  date_created = '" . $this->db->escape($datedCM) . "',
                  staff_detail = '" . $this->db->escape(serialize($staff_array)) . "'
                WHERE tentative_advance_id = '". (int)$tentative_advance_id ."'";

        $this->db->query($sql);
    }



}
