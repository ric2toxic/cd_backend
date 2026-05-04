<?php

// Strict mode
declare( strict_types = 1 );

/**
 * Class for Customer Entity
 * For now, this class is created for gst number concern handeling
 * In future, it will be updated according to final structure
 * @author: Anurag Jain; Aug 2018
 */
 
class CustomerEntity {
    
	private $customer_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;
	private $address_id;
	private $referral_code;
	public 	$is_dropshipper;
    private $access_token;
	private $mobile_verified;
	private $email_verified;
    private $gst_number;
    private $master_id;
    public $gstObject;

	public function __construct($registry, $customer_id = 0) {
        
		$this->gstObject = new GST($registry);
        
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
        
		if ($customer_id > 0) {
            
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
            
			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];
				$this->address_id = $customer_query->row['address_id'];
				$this->referral_code = $customer_query->row['referral_code'];
				$this->is_dropshipper = $customer_query->row['is_dropshipper'];
				$this->access_token = $customer_query->row['ws_access_token'];
				$this->mobile_verified = $customer_query->row['mobile_verified'];
				$this->email_verified = $customer_query->row['email_verified'];
				$this->gst_number = $customer_query->row['gst_number'] ?? "";
				$this->master_id = $customer_query->row['master_id'];
                
			}
		}
	}
    
	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}
	
	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}
	
	public function getMasterId() {
		return $this->master_id;
	}

    public function getGSTNumber() {
        return trim($this->gst_number);
    }
    
    /**
     * Public function to update gst number
     * @param : string $gst_number, bool $log_changes
     * @return : bool
     * @author : Anurag, Aug 2018
     */
    public function setGSTNumber(string $gst_number, bool $log_changes = false): bool {
        if((int) $this->customer_id > 0) {
    		$this->gst_number = $this->gst_number ?? "";
    		$this->gstObject->updateGstNumber((int) $this->customer_id, (int) $this->master_id, $this->gst_number, $gst_number, $log_changes);
    		$this->gst_number = $gst_number;
            return true;
        }
        return false;
    }
}
