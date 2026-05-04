<?php
class ModelSaleCourierDockets extends Model {

    /**
     * Method to save courier data
     * @param Array $data
     * @return Boolean    true/false
     * Author: MSA
     */
    public function saveCourierDocketData(array $data)
    {
        if(!empty($data)) {
            
            $docket_sql = "INSERT INTO ".DB_PREFIX."courier_dockets
                    SET 
                        order_id                = '".(int)$data['order_id']."',
                        suborder_id             = '".$this->db->escape($data['suborder_id'])."',
                        courier_partners_id     = '".(int)$data['courier_partners_id']."',
                        docket_no               = '".$this->db->escape($data['docket_no'])."',
                        payment_mode            = '".$this->db->escape($data['payment_mode'])."',
                        cod_amount              = '".(float)$data['cod_amount']."',
                        parcel_amount           = '".(float)$data['parcel_amount']."',
                        post_mode               = '".$this->db->escape($data['post_mode'])."',
                        post_type               = '".$this->db->escape($data['post_type'])."',
                        post_url                = '".$this->db->escape($data['post_url'])."',
                        post_values             = '".$this->db->escape($data['post_values'])."',
                        response_type           = '".$this->db->escape($data['response_type'])."',
                        response_value          = '".$this->db->escape($data['response_value'])."',
                        date_added              = '".$this->db->escape($data['date_added'])."',
                        active                  = 1
			";
            if ($this->db->query($docket_sql)) {
                return true;
            } else {
                return false;
            }
        } else {
            
            return false;
        }
       
    }
    
    /**
     * Method to get courier partner id by courier title
     * @param String $title
     * @return integer $id
     * Author: MSA
     */
    public function getCourierPartnerIdByName(string $courier_name)
    {
        if(!empty($courier_name)) {
            
            $get_courier_name = $this->db->query("SELECT id
                                              FROM " . DB_PREFIX . "courier_partners
                                              WHERE courier_name = '" . $this->db->escape($courier_name) . "'");
            if($get_courier_name->num_rows) {
                return $get_courier_name->row['id'];
            } else {
                return false;
            }
        }
        return;
    }
    
    /**
     * getDocketDetails
     * Get dockets
     * @return ARRAY    result
     * @author Manoj
     */
    public function getDocketDetails($docket_no, $suborder_id) {
        
        if(!empty($docket_no) && !empty($suborder_id))
        {
            $docket_sql = "SELECT  *  FROM " .DB_PREFIX. "courier_dockets 
                           WHERE docket_no = '" . $this->db->escape($docket_no) . "' 
                             AND suborder_id = '" . $this->db->escape($suborder_id) . "' 
                             AND post_url <> 'No-Docket-Generation' " ;

            $docket_query = $this->db->query($docket_sql);

            if($docket_query->num_rows) {
                return $docket_query->row;
            } else {
                return false;
            }
        }
    }
    
    /*
      * Method: getCourierPartners
      * return: list of courier partners 
      * author: Devendra, May 2018 
     */
    public function getCourierPartners() {
        $sql = "SELECT * FROM " .DB_PREFIX. "courier_partners WHERE status=1";
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            return $result->rows;
        } 
        
        return array();
    }
}
