<?php
class ModelStaffStaff extends Model {

    public function getStaffInfoByImei($imei) {
        $sql = "SELECT * FROM " . DB_PREFIX . "sales_staff 
        WHERE imei_number = '" . $imei . "'
        AND active_status = 1";

        $query = $this->db->query($sql);

        return $query->row;

    }

    public function getStaffs($user_type, $status = 0) {

        $where = array();
        $where[] = ' 1 = 1';
        $where[] = " user_type = '".$user_type."'";

        if ($status != 2) {
           $where[] = ' active_status = '.$status;
        }
        $sql = "SELECT * FROM " . DB_PREFIX . "sales_staff 
        WHERE ".implode(' AND ', $where);

        $query = $this->db->query($sql);

        return $query->rows;

    }
}