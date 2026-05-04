<?php
class ModelAccountCustomerEmailHistory extends Model {
    
     public function saveCustomerEmailHistory($data) {

        $exits_customer_email_history = $this->isExitCustomerCustomerEmailHistory($data['message_id']);
        if(empty($exits_customer_email_history)){
                            $sql = "INSERT INTO " . DB_PREFIX . "customer_email_history SET "
                        . "`date` = '" . $data['date'] . "', " 
                        . "`user_id` = '" . (int)$data['user_id'] . "', "
                        . "`from` = '" . $this->db->escape($data['from']) . "', "
                        . "`message_id` = '" . $this->db->escape(trim($data['message_id'])) . "', "
                        . "`subject` = '" . $this->db->escape(trim($data['subject'])) . "', "
                        . "`to` ='" . $this->db->escape(trim($data['to'])) . "', "
                        . "`account_email_id` = '" . $this->db->escape(trim($data['account_email_id'])) . "', "
                        . "`file_path` = '" . $this->db->escape(trim($data['file_path'])) . "', "
                        . "`created` = '" . date("Y-m-d H:i:s") ."', "
                        . "`modified` = '" . date("Y-m-d H:i:s") . "' ";
                
                $query = $this->db->query($sql);

                $customer_email_history_id = $this->db->getLastId();
                return $customer_email_history_id;
        }else{
            $sql = "UPDATE " . DB_PREFIX . "customer_email_history SET "
                        . "`date` = '" . $data['date'] . "', " 
                        . "`user_id` = '" . (int)$data['user_id'] . "', " 
                        . "`from` = '" . $this->db->escape($data['from']) . "', "
                        . "`message_id` = '" . $this->db->escape(trim($data['message_id'])) . "', "
                        . "`subject` = '" . $this->db->escape(trim($data['subject'])) . "', "
                        . "`to` ='" . $this->db->escape(trim($data['to'])) . "', "
                        . "`account_email_id` = '" . $this->db->escape(trim($data['account_email_id'])) . "', "
                        . "`file_path` = '" . $this->db->escape(trim($data['file_path'])) . "', "
                        . "`modified` = '" . date("Y-m-d H:i:s") . "' "
                        . " where `id`='" .(int)$exits_customer_email_history . "' ";
                $query = $this->db->query($sql);

                //delete previous attach file 

                $delete_sql = "DELETE FROM `oc_customer_email_history_attachment` WHERE `customer_email_history_id`='" .(int)$exits_customer_email_history . "'";
                $delete_query = $this->db->query($delete_sql);
                return $exits_customer_email_history;
        }
        return false;
     }


    public function isExitCustomerCustomerEmailHistory($message_id){
        $sql = "SELECT id FROM " . DB_PREFIX ."customer_email_history WHERE message_id='".$this->db->escape(trim($message_id))."' GROUP BY message_id";
        $query = $this->db->query($sql);
        if(!empty($query->row['id'])){
            return $query->row['id'];
        }else{
            return 0;
        }
    }


    
    public function saveCustomerEmailHistoryAttachment($data) {
                        $sql = "INSERT INTO " . DB_PREFIX . "customer_email_history_attachment SET "
                        . "`customer_email_history_id` = '" . $this->db->escape($data['customer_email_history_id']) . "', "
                        . "`file_path` = '" . $this->db->escape(trim($data['file_path'])) . "'";
                $query = $this->db->query($sql);
                $customer_email_history_id = $this->db->getLastId();
                return true;
     }

     public function get_email_id_list($user_id) {
                        $sql = "SELECT
                                  `account_email_id` as email_id,
                                   max(`date`) as last_email_date
                                FROM
                                  " . DB_PREFIX . "customer_email_history
                                WHERE
                                  `user_id` = '". (int)$user_id."'
                                GROUP BY
                                  `account_email_id`";
                $query = $this->db->query($sql);
                return $query;
     }
    
}