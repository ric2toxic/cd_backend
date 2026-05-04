<?php
class ModelReportAdminInfoLog extends Model {

    public function getAmdminLogInfoDetails($data = array()){

        $sql = "SELECT user_id, 
                       username, 
                       name, 
                       field_name, 
                       new_value,
                       user_type, 
                       count(log_id) as total
                FROM `oc_admin_change_log` 
                WHERE table_name='oc_admin_info_log' ";

        if (!empty($data['filter_field_type'])) {
            $sql .= " AND field_name = '" . $this->db->escape($data['filter_field_type']) . "'";
        }

        if (!empty($data['filter_field_value'])) {
            $sql .= " AND new_value = '" . $this->db->escape($data['filter_field_value']) . "'";
        }

        if (!empty($data['filter_date_from'])) {

            $sql .= " AND DATE_FORMAT(date_added,'%Y-%m-%d %H:%m:%s') >=  DATE_FORMAT('" . $this->db->escape($data['filter_date_from']) . "','%Y-%m-%d %H:%m:%s')";
        }

        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE_FORMAT(date_added,'%Y-%m-%d %H:%m:%s') <= DATE_FORMAT('" . $this->db->escape($data['filter_date_to']) . "','%Y-%m-%d %H:%m:%s')";
        }
        if (isset($data['filter_name'])) {
            $sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_user_group'])) {
            $sql .= " AND user_type = '" . $data['filter_user_group'] . "'";
        }

        $sql .= " GROUP BY user_id, field_name ORDER BY count(log_id) DESC " ;

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

        if( $query->num_rows ){
            return $query->rows; 
        }else{
            return false;
        }

    }

    public function getTotalAmdminLogInfo($data = array()){

        $sql = "SELECT user_id, 
                       username, 
                       name, 
                       field_name, 
                       count(log_id) 
                FROM `oc_admin_change_log` 
                WHERE table_name='oc_admin_info_log' ";

        if (!empty($data['filter_field_type'])) {
            $sql .= " AND field_name = '" . $this->db->escape($data['filter_field_type']) . "'";
        }

        if (!empty($data['filter_field_value'])) {
            $sql .= " AND new_value = '" . $this->db->escape($data['filter_field_value']) . "'";
        }

        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(date_added) >=  DATE('" . $this->db->escape($data['filter_date_from']) . "')";
        }

        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(date_added) <= DATE('" . $this->db->escape($data['filter_date_to']) . "')";
        }
        if (isset($data['filter_name'])) {
            $sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_user_group'])) {
            $sql .= " AND user_type = '" . $data['filter_user_group'] . "'";
        }

        $sql .= "GROUP BY user_id, field_name ORDER BY count(log_id) DESC " ;

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

        if( $query->num_rows ){
            return $query->num_rows; 
        }else{
            return false;
        }
    }

    public function getAllFieldType() {
        $sql = "SELECT DISTINCT field_name FROM " . DB_PREFIX . "admin_change_log WHERE table_name='oc_admin_info_log' ORDER BY field_name ASC";
        $query = $this->db->query($sql);
        if( $query->num_rows ){
            return array_column($query->rows, 'field_name');
        }
        return array();
    }

    public function getAllUserGroup() {
        $sql = "SELECT DISTINCT user_type FROM " . DB_PREFIX . "admin_change_log WHERE table_name='oc_admin_info_log' ORDER BY user_type ASC";
        $query = $this->db->query($sql);

        if( $query->num_rows ){
            return array_column($query->rows, 'user_type');
        }
        return array();
    }
}
