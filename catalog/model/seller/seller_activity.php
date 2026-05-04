<?php
class ModelSellerSellerActivity extends Model
{
    public function getSellerActivities(){
        $sql = "SELECT * FROM `" . DB_PREFIX . "seller_change_log` WHERE mail = 0 ";
        $query = $this->db->query($sql);
        return $query->rows;

    }

    public function setEmailSent(){
        $sql = "UPDATE `" . DB_PREFIX . "seller_change_log` SET `mail`= 1";
        $query = $this->db->query($sql);

    }

    public function sellerAgreementSubmission($seller_agreement = 0, $seller_id){
        $this->db->query("UPDATE " . DB_PREFIX . "ms_seller SET seller_agreement = '" . (int)$seller_agreement . "', seller_agreement_acceptance_date = NOW() WHERE seller_id = '" . $seller_id . "'");

    }

}
