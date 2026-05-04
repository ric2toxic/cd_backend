<?php
class ModelModuleCreditApplication extends Model {

    public function addCreditApplication($data = array()){
        //echo "<pre>"; print_r($this->request->post); echo "</pre>";die;
        if(!empty($data)){
            $shop_since = $data->post['shop_since'].'-'.$data->post['tenure_year'];
            $sql = "INSERT INTO " . DB_PREFIX . "wsb_credit_application SET
                owner_name = '" . $this->db->escape($data->post['name']) . "',
                firm_name = '" . $this->db->escape($data->post['firm']) . "',
                shop_since = '" . $this->db->escape($shop_since) . "',
                mobile = '" . ($data->post['telephone']) . "',
                email = '" . $this->db->escape($data->post['email']) . "',
                city = '" . $this->db->escape($data->post['city']) . "',
                state = '" . $this->db->escape($data->post['location']). "',
                created_by = '" . date('Y-m-d') . "'
                ";
            //echo "<pre>"; print_r($sql); echo "</pre>"; die;
            $this->db->query($sql);
        }
    }

    public function getState(){
        $sql = "SELECT name FROM ".DB_PREFIX."zone WHERE country_id = 99";
        $query = $this->db->query($sql);
        //echo "<pre>"; print_r($query->rows); echo "</pre>"; die;
        return $query->rows;
    }
}