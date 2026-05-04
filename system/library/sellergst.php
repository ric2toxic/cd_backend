<?php
// Strict mode
declare( strict_types = 1 );

class SellerGST extends GST {
    
    public function __construct($registry) {
        parent::__construct($registry);
    }
    
    /**
     * validates gst number including regex check, checksum check and duplicacy check
     * @param: $gst_number: (string)
     *         $seller_id: (int)
     *         $customer_duplicacy_check: (bool)
     *         $current_seller: (array) - current seller info; used when new seller tries to used duplicate GST (for mailing purpose)
     * @return: $result: success or error specifications
     * @author: Anurag Jain (Aug 2018)
     **/
    public function validateGSTNumber(string $gst_number, 
                                      int $seller_id = 0, 
                                      bool $customer_duplicacy_check = true,
                                      array $current_seller = array()): array {
        $result = array();
        
        $gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);;
        $gst_number = trim($gst_number);
        
        /*** GST format check ***/
        $result = self::validateGSTNumberFormat($gst_number);
        
        if(!empty($result['message']) && $result['message'] == 'valid') {
            
            /*** GST duplicacy check with seller ***/
            $current_seller = $this->getSellerBySellerId($seller_id);
            $duplicate_gst_number_seller = $this->getSellerByDuplicateGSTNumber($gst_number, $seller_id);
            $is_duplicate_gst = false;
            $mail_data = array('current_seller' => $current_seller);
            
            if($duplicate_gst_number_seller['total'] == 0) { // Case: no seller gst duplicacy
                
                /*** GST duplicacy check with customer ***/
                if($customer_duplicacy_check) {
                    $current_customer = $this->getCustomerByCustomerId($seller_id);
                    $master_id = $current_customer['master_id'] ?? 0;
                    $duplicate_gst_number_customer = $this->getCustomerByDuplicateGSTNumber($gst_number, (int) $master_id);
                    
                    if($duplicate_gst_number_customer['total'] > 0) { // Case: customer gst duplicacy
                        $is_duplicate_gst = true;
                        $duplicate_gst_number_details = array(
                                                            'total' => $duplicate_gst_number_customer['total'],
                                                            'entity_id' => $duplicate_gst_number_customer['customer_id'],
                                                            'master_id' => $duplicate_gst_number_customer['master_id'],
                                                            'firstname' => $duplicate_gst_number_customer['firstname'],
                                                            'telephone' => $duplicate_gst_number_customer['telephone'],
                                                            'email' => $duplicate_gst_number_customer['email'],
                                                            'gst_number' => $duplicate_gst_number_customer['gst_number']
                                                        );
                        $duplicate_gst_number_entity = $mail_data['duplicate_gst_number_entity'] = 'customer';
                        $mail_data['duplicate_entity'] = $duplicate_gst_number_details;
                    }
                }
            } else if($duplicate_gst_number_seller['total'] > 0) { // Case: seller gst duplicacy
                $is_duplicate_gst = true;
                $duplicate_gst_number_details = array(
                                                    'total' => $duplicate_gst_number_seller['total'],
                                                    'entity_id' => $duplicate_gst_number_seller['seller_id'],
                                                    'nickname' => $duplicate_gst_number_seller['nickname'],
                                                    'company' => $duplicate_gst_number_seller['company'],
                                                    'telephone' => $duplicate_gst_number_seller['mobile_no'],
                                                    'email' => $duplicate_gst_number_seller['email'],
                                                    'gst_number' => $duplicate_gst_number_seller['gst_provisional_id']
                                                );
                $duplicate_gst_number_entity = $mail_data['duplicate_gst_number_entity'] = 'seller';
                $mail_data['duplicate_entity'] = $duplicate_gst_number_details;
            }
            
            if($is_duplicate_gst) {
                $result = array('result' => false,
                                'message' => 'error_duplicate',
                                'duplicate_gst_number_details' => $duplicate_gst_number_details,
                                'duplicate_gst_number_entity' => $duplicate_gst_number_entity);
                          
                /*** Mail to authorities for GST duplicacy encounter ***/
                if( !empty( $mail_data['current_seller']) 
                    && !empty( $mail_data['duplicate_entity'])  
                    && $this->shouldSendGSTDuplicacyMail( array('entity_id' => $seller_id, 'gst_number' => $gst_number ))) {

                    $this->sendGSTNumberDuplicacyMail($mail_data);
                }
            }
        }
        return $result;
    }
    
    /**
     * check seller duplicacy for gst_number (excluding current seller id)
     * @param: $gst_number: (string)
     *         $seller_id: (int) current seller id
     * @return: array: duplicate seller details
     * @author: Anurag Jain (Aug 2018)
     **/
    public function getSellerByDuplicateGSTNumber(string $gst_number, int $seller_id): array {
        $sql = "SELECT count(seller_id) AS total, seller_id, nickname, company, mobile_no, email, gst_provisional_id, pan
                FROM ".DB_PREFIX."ms_seller 
		        WHERE gst_provisional_id = '" . $this->db->escape($gst_number) . "' 
                    AND seller_id <> '" . (int)$seller_id . "'";
        
		$result = $this->db->query($sql);
        if(empty($result->row['total'])) {
            return $this->getSellerByPendingVerificationDuplicateGSTNumber($gst_number, $seller_id);
        }
		return $result->row;
	}
    
    /**
     * check seller duplicacy for gst_number (excluding current seller id) in pending verification
     * @param: $gst_number: (string)
     *         $seller_id: (int) current seller id
     * @return: array: duplicate seller details
     * @author: Anurag Jain (Aug 2018)
     **/
    public function getSellerByPendingVerificationDuplicateGSTNumber(string $gst_number, int $seller_id): array {
        $sql = "SELECT 
                    count(oms.seller_id) AS total, 
                    oms.seller_id, 
                    oms.nickname, 
                    oms.company, 
                    oms.mobile_no, 
                    oms.email, 
                    osu.new_value AS gst_provisional_id
                FROM ".DB_PREFIX."seller_updates osu 
                INNER JOIN ".DB_PREFIX."ms_seller oms 
                    ON oms.seller_id = osu.seller_id
		        WHERE osu.update_type = 'gst_provisional_id' 
                    AND osu.new_value = '".$this->db->escape(trim($gst_number))."'
                    AND osu.seller_id <> '" . (int)$seller_id . "'
                    AND osu.verification_status = 'pending'";
        
		$result = $this->db->query($sql);
        $result->row['duplicacy_type'] = 'pending_verification_duplicacy';
		return $result->row;
	}

    /**
     * get required details of a given seller
     * @param: $seller_id: (int)
     * @return: array: seller details
     * @author: Anurag Jain (Aug 2018)
     **/ 
    public function getSellerBySellerId(int $seller_id): array {
        $sql = "SELECT 
                    seller_id, 
                    nickname, 
                    company, 
                    mobile_no, 
                    email, 
                    gst_provisional_id, 
                    pan 
                FROM " . DB_PREFIX . "ms_seller 
                WHERE seller_id = '" . $this->db->escape($seller_id) . "' 
                LIMIT 1";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            return $result->row;
        }
        return array();
    }
    
    /**
     * send mail if duplicacy of gst number is encountered (with seller/customer)
     * @param: $mail_data: (array) current seller and duplicate old customer/seller details
     * @return: bool
     * @author: Anurag Jain (Aug 2018)
     **/
    public function sendGSTNumberDuplicacyMail(array $mail_data): bool {
        $mail = new PHPMailer();
        
        $current_seller = $mail_data['current_seller'];
        $duplicate_entity = $mail_data['duplicate_entity'];
        $entity_label = ucfirst($mail_data['duplicate_gst_number_entity']);
        $subject = "Duplicate GST Number Encountered - ". date('d M Y h:i:s A', time()) ." !!!";
        
        $body    = ""; 
        $body .= "Dear Team,<br><br>";
        $body .= "A seller is trying to use GST Number: <b>". $duplicate_entity['gst_number'] ."</b> which is already being used against an old ". $mail_data['duplicate_gst_number_entity'] .".<br><br>";
        $body .= "Seller Details:<br>";
        $body .= "Seller Id: " . $current_seller['seller_id'] . "<br>";
        $body .= "Nickname: " . $current_seller['nickname'] . "<br>";
        $body .= "Company: " . $current_seller['company'] . "<br>";
        $body .= "Mobile Number: " . $current_seller['mobile_no'] . "<br>";
        $body .= "Email: " . $current_seller['email'] . "<br>";
        $body .= "<br><br>";
        $body .= "Old ". $entity_label ." Details:<br>";
        
        $body .= $entity_label." Id: " . $duplicate_entity['entity_id'] . "<br>";
        !empty($duplicate_entity['nickname']) ? $body .= "Nickname: " . $duplicate_entity['nickname'] . "<br>" : "" ;
        !empty($duplicate_entity['company']) ? $body .= "Company: " . $duplicate_entity['company'] . "<br>" : "" ;
        !empty($duplicate_entity['firstname']) ? $body .= "Name: " . $duplicate_entity['firstname'] . "<br>" : "" ;
        !empty($duplicate_entity['telephone']) ? $body .= "Mobile Number: " . $duplicate_entity['telephone'] . "<br>" : "" ;
        !empty($duplicate_entity['email']) ? $body .= "Email: " . $duplicate_entity['email'] . "<br>" : "" ;
        
        $body .= "<br><br>";
        $body .= "Thanks & Regards<br>WholesaleBox"; 
        
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl'; 
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port'); 
        $mail->Username = $this->config->get('config_mail_smtp_username');        
        $mail->Password = $this->config->get('config_mail_smtp_password');
        
        if( strtolower(SITE_ENVIRONMENT) == "production" ) {
            $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
            $mail->addReplyTo(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        } else {
            $mail->addAddress('anurag.jain@wholesalebox.co', 'Staging Anurag'); 
            $mail->addAddress('dheeraj.sharma@wholesalebox.co', 'Staging Dheeraj');
        }
        
        $mail->Subject = $subject;                  
        $mail->Body    = $body;  
        $mail->isHTML(true);
        $mail = $mail->send(true);
        
        return $mail;
    }
    
    /**
     * Public method to update seller gst number along with customer gst update flow (if seller is customer also)
     * @param: int $seller_id, string $gst_number, string $gst_arn, bool $log_changes
     * @return: bool
     * @author: Anurag Jain, Aug 2018
    */
    public function updateSellerGstNumber(int $seller_id, string $gst_number, string $gst_arn = "", bool $log_changes = false): bool {
        if($log_changes) {
            $this->log_changes = $log_changes;
        }
        
        $current_seller = $this->getSellerBySellerId($seller_id);
        $old_gst_number = $current_seller['gst_provisional_id'] ?? "";
        
        // we will not update seller if seller_id != nickname; i.e. seller's gst is approved once and will not be changed
        if(!empty($current_seller) && ($current_seller['seller_id'] == $current_seller['nickname'])) {
            $this->updateSellerGstInDB($seller_id, $gst_number, $gst_arn);
            $current_customer = $this->getCustomerByCustomerId($seller_id);
            
            /*** call updateGstNumber for gst update in customer if seller is customer ***/
            if(!empty($current_customer)) {
                $master_id = (int) $current_customer['master_id'] ?? 0;
                $this->updateGstNumber($seller_id, $master_id, $old_gst_number, $gst_number);
            }
        }
        return true;
    }
    
    /**
     * Public method to update gst number , pan , gst arn in db for seller
     * @param: int $seller_id, string $gst_number, string $gst_arn
     * @return: bool
     * @author: Anurag Jain, Aug 2018
    */
    public function updateSellerGstInDB(int $seller_id, string $gst_number, string $gst_arn = ""): bool {
        $pan        = self::getPANByGSTNumber($this->db->escape($gst_number));

        $gst_number = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $gst_number);
        $gst_number = trim($gst_number);
        
        $sql = "UPDATE ". DB_PREFIX ."ms_seller
                    SET gst_provisional_id = '". $this->db->escape($gst_number) ."',
                        pan = '". $this->db->escape($pan) ."'";

        if(!empty($gst_arn)) {
            $sql .= " , gst_arn = '". $this->db->escape($gst_arn) ."'";
        }
        
        $sql .= " WHERE seller_id = '". (int) $seller_id ."'";
        $result = $this->db->query($sql);
        return true;
    }
}