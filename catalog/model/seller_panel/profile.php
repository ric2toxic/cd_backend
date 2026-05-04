<?php
class ModelSellerPanelProfile extends Model {
	public function getSellersInformation($seller_id,$fields){
		if(isset($seller_id) && !empty($seller_id)){
				$sql = "SELECT $fields FROM " . DB_PREFIX . "ms_seller
								WHERE seller_id = $seller_id ";
				$result = $this->db->query($sql);
				return $result->row;
		}
	}

	public function getfirstAndLastName($seller_id){
		$sql = "SELECT firstname,lastname FROM " . DB_PREFIX . "customer
						WHERE customer_id = $seller_id";
						$result = $this->db->query($sql);
						return $result->row;
	}

	public function getAdditionalEmails($customer_id){
		$sql = "SELECT GROUP_CONCAT(email) AS addtional_emails FROM " . DB_PREFIX . "customer_additional_email
						WHERE customer_id = $customer_id";
						$result = $this->db->query($sql);
                        return $result->row['addtional_emails'];

	}

	public function checkSellerProfileIsComplete($seller_id){
			if(isset($seller_id) && !empty($seller_id)){

			}
	}

	public function getSellersAgreementConsent($seller_id){
		
			$sql = "SELECT declaration FROM " . DB_PREFIX . "agreement_consent
								WHERE seller_id = ".(int)$seller_id;
			$result = $this->db->query($sql);
			if(!empty($result->row['declaration']))
			{
				return $result->row['declaration'];
			}
			else
			{
				return false;
			}
		
	}

	public function saveSellersAgreementConsent($seller_id, $declaration)
	{
        $sql = "INSERT INTO " . DB_PREFIX . "agreement_consent set
           seller_id = '".(int)$seller_id."',
           declaration = '".$declaration."',
           date_added = 'now()'";
	    $this->db->query($sql);
	    return $declaration;
	}

}
