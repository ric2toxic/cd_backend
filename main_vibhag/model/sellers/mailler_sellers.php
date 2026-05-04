<?php
class ModelSellersMaillerSellers extends Model{

    public function getSellersList($data = array()){

        $sql = "SELECT ms.seller_id,
                        ms.nickname,
                        ms.company,
                        CONCAT(c.firstname, ' ', c.lastname) AS name,
                        ms.seller_status,
                        z.name as zone_name,
                        z.code as zone_code
                FROM " . DB_PREFIX . "ms_seller ms
                LEFT JOIN " . DB_PREFIX . "customer c ON (ms.seller_id = c.customer_id)
                LEFT JOIN ".DB_PREFIX."zone z ON (ms.zone_id = z.zone_id)
                WHERE ms.zone_id != 0
                ORDER BY name ASC ";

        $query = $this->db->query($sql);
        return $query->rows;
    }


    // get email and company name by seller_id
    public function getSellersDetails($seller_ids){

        $sql = "SELECT  c.customer_id,
                        ms.company,
                        c.email
                FROM " . DB_PREFIX . "ms_seller ms
                INNER JOIN " . DB_PREFIX . "customer c
                  ON (ms.seller_id = c.customer_id)
                WHERE seller_id IN (".implode(',',$seller_ids).")";

        $query = $this->db->query($sql);

        if ($query->num_rows) {
            $seller_details = array();
            foreach ($query->rows as $seller_detail) {
                $seller_details[$seller_detail['customer_id']] = array(
                    'company' => $seller_detail['company'],
                    'email' => $seller_detail['email']
                );
            }
            return $seller_details;
        } else
            return false;
    }



    // get Additional emails by seller id
    public function getAdditionalEmails($seller_id){

       $query =  $this->db->query("SELECT email
                                    FROM " . DB_PREFIX . "customer_additional_email
                                    WHERE customer_id = '" . (int)$seller_id . "'");

        if($query->num_rows){
            return $query->rows;
        }else{
            return false;
        }
    }

    //Saving data into table oc_mailer_to_sellers_log
    public function insertIntoMailerToSellersLog($data){
        $this->db->query("INSERT INTO " . DB_PREFIX . "mailer_to_sellers_log
                          SET emails = '" . $this->db->escape(serialize($data['emails'])). "',
                              attachments = '" . $this->db->escape(serialize($data['attachments'])) . "', 
                              subject = '" . $this->db->escape($data['subject']) . "', 
                              message = '" . $this->db->escape($data['message']) . "', 
                              user = '" . $this->db->escape($data['user']) . "', 
                              date_sent =  NOW()");

    }

}