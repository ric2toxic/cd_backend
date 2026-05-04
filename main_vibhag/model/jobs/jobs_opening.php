<?php
class Modeljobsjobsopening extends Model{

    // adding job opening
    public function addJobPost($data = array()){
        //echo "<pre>"; print_r($data); echo "</pre>";die;
        if(!empty($data)){
            $sql = "INSERT INTO " . DB_PREFIX . "wsb_job SET
                title = '" . $this->db->escape($data['job_title']) . "',
                job_type = '" . $this->db->escape($data['job_type']) . "',
                description = '" . $this->db->escape(html_entity_decode($data['job_description'])) . "',
                start_date = '" . $this->db->escape($data['job_start_date']) . "',
                end_date = '" . $this->db->escape($data['job_close_date']) . "',
                location = '" . $this->db->escape($data['job_location']) . "',
                email_to = '" . $this->db->escape($data['job_email_to']) . "',
                job_icon = '" . $this->db->escape($data['job_input_image']) . "',
                status = '" . (int)$data['job_status'] . "',
                created_by = '" . date('Y-m-d') . "'
                ";
             //echo "<pre>"; print_r($sql); echo "</pre>"; die;
            $this->db->query($sql);
        }
    }

    // updating job opening
    public function editJobPost($jobs_id,$data){
        //echo "<prE>"; print_r($data['job_description']); echo "</pre>"; die;
        $sql = "UPDATE " . DB_PREFIX . "wsb_job SET
         title = '" . $this->db->escape($data['job_title']) . "',
         job_type = '" . $this->db->escape($data['job_type']) . "',
         description = '" . $this->db->escape(html_entity_decode($data['job_description'])) ."',
         start_date = '" . $this->db->escape($data['job_start_date']) . "',
         end_date = '" . $this->db->escape($data['job_close_date']) . "',
         location = '" . $this->db->escape($data['job_location']) . "',
         email_to = '" . $this->db->escape($data['job_email_to']) . "',
         job_icon = '" . $this->db->escape($data['job_input_image']) . "',
         status = '" . (int)$data['job_status'] . "',
         modified_by = '" . date('Y-m-d') . "'
         WHERE job_id = '" . (int)$jobs_id . "'";
        $this->db->query($sql);
    }

    // deleting job opening
    public function deleteJob($job_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "wsb_job WHERE job_id = '" . (int)$job_id . "'");
    }

    // Total job opening
    public function getTotalJobs($data = array()){
        $sql = "SELECT COUNT(DISTINCT wj.job_id) AS total FROM " . DB_PREFIX . "wsb_job wj WHERE wj.title LIKE '" . $this->db->escape($data['filter_name']) . "%' AND wj.status LIKE '" . $this->db->escape($data['filter_status']) . "%'";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    // listing job opening
    public function getJobs($data = array()){
        //$sql = "SELECT * FROM " . DB_PREFIX . "wsb_job wj  WHERE pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        $sql = "SELECT * FROM " . DB_PREFIX . "wsb_job wj WHERE wj.title LIKE '%" . $this->db->escape($data['filter_name']) . "%' AND wj.status LIKE '%" . $this->db->escape($data['filter_status']) . "%'";
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 30;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        $query = $this->db->query($sql);
        return $query->rows;

    }


    public function getJob($job_id){
        //$sql = "SELECT * FROM " . DB_PREFIX . "wsb_job wj  WHERE pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "wsb_job wj WHERE job_id = '" . $this->db->escape($job_id) . "'";
        $query = $this->db->query($sql);
        return $query->row;

    }
}