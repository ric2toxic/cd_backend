<?php
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

class ControllerCronRbl extends Controller {

    public $data = array();

    public function __construct($registry) {
        parent::__construct($registry);
        $this->registry = $registry;
        $this->MsLoader->MsHelper->addStyle('multiseller');
        $this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'), $this->language->load('product/product'));
    }
    
    /**
     * @info: Cron public method to read RBL csv file data
     * @param:  string csv file name
     * @return: array csv file data
     * @author: MSA, June 2019 
    */
    public function readRBLCreditCSVfile(string $filename): array
    {
       $obj = new MYSFTP();

       return $obj->get( $filename );
    }

    /**
     * @info: Cron public method to read RBL csv file data
     * @param: string table name
     * @param: int retailer id
     * @param: array selected fields list
     * @return: array retailer data or empty
     * @author: MSA, March 2019 
    */
    public function isRBLRetailerExists( string $table, int $retailer_id, array $fields ): array
    {
        $data = array();
        $selected_fields = implode(',', $fields);
        $sql = "
                SELECT 
                    ".$selected_fields."
                FROM " . DB_PREFIX . $table . "
                WHERE
                    retailer_id = '".(int) $retailer_id."'
               ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $data = $result->row;
        }
        return $data;
    }

    /**
     * @info: Cron public method to convert DB date format
     * @param: string $date
     * @return: string date
     * @author: MSA, March 2019 
    */
    public function convertDateFormat(string $date){
        if(!empty(trim($date))) {
            return date('Y-m-d', strtotime($date));
        }
    }

    /**
     * @info: Cron method to read RBL CSV file to get customer credit status 
     *           and add/update customer credit pre-approved status in db table
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLApprovedRetailers()
    {
        $creditData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['PRE_APPROVAL_RESPONSE']);  
        
        if(!empty($creditData)) {

            // save/update pre-approved customers credit data
            foreach($creditData as $retailer) {
               
                //csv file row data
                $date           = $retailer[0];
                $anchor_name    = $retailer[1];
                $anchor_id      = $retailer[2];
                $utr            = $retailer[3]; //URN Number

                $retailer_id    = $retailer[4];
                
                $retailer_name  = $retailer[5];
                $firm_name      = $retailer[6];
                $pan            = $retailer[7];
                $approval_date  = $this->convertDateFormat($retailer[8]);
                $approved_limit = $retailer[9];
                $roi            = $retailer[10]; 
                $limit_expiry_date  = $this->convertDateFormat($retailer[11]);
                $udpated_date   =  $this->convertDateFormat($retailer[12]);
                $remark_data = array(
                        'user_id'       => '0',
                        'type'          => 'Note',
                        'status'        => '',
                        'reason'        => 'Credit PreApproved',
                        'remark'        => 'Pre approved limit [ '.$approved_limit.' ] ',
                        'credit_limit'  => $approved_limit,
                        'followup_date' => $udpated_date                      
                    );

                //first check retailer_id must be exists in customer_preonboarding table
                $pre_onboarding_status = $this->isRetailerIdExistsInPreOnboardingTable($retailer_id);
                if(!$pre_onboarding_status) {
                    //if retailer id not found in customer pre-onboarding table 
                    //skip the records
                    continue;
                }

                // check if retailer already exist in table
                $existing_user = $this->isRBLRetailerExistsForCreditPreApproved($retailer_id);

                if( empty($existing_user) ) {

                    $insert_sql = "
                                    INSERT INTO 
                                        ".DB_PREFIX."customer_credit_preapproved
                                    SET
                                        retailer_id     = '".(int)$retailer_id."',
                                        utr             = '".$this->db->escape($utr)."',
                                        retailer_name   = '".$this->db->escape($retailer_name)."',
                                        firm_name       = '".$this->db->escape($firm_name)."',
                                        pan             = '".$this->db->escape($pan)."',
                                        approval_date   = '".$this->db->escape($approval_date)."',
                                        approved_limit  = '".$this->db->escape($approved_limit)."',
                                        roi             = '".$this->db->escape($roi)."',
                                        pre_approved_limit_expiry_date = '".$this->db->escape($limit_expiry_date)."',
                                        approved_by     = 'RBL',
                                        date_added      = NOW(),
                                        cif_creation_status = 'Pending',
                                        date_updated    = '".$this->db->escape($udpated_date)."'
                                    ";

                    $this->db->query($insert_sql);

                    $this->updatedActionLogForRBLStatus($retailer_id, 'Credit PreApproved Add');

                    $remark_data['reason'] = 'Added Credit PreApproved';

                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);

                    $sms_data = array(
                                        'action'        => 'pre_approved',
                                        'customer_id'   => (int)$retailer_id,
                                        'approved_limit'=> $this->currency->money_format($approved_limit,'INR',1),
                                        'approval_date' => date('d/m/Y',strtotime($approval_date)),
                                        'link'          => WSB_CREDIT_LINK,
                                    );
                    $this->sendSMSAlerts( $sms_data ); 

                } else { 

                    if(strtotime($udpated_date) > strtotime($existing_user['date_updated'])) {

                        $update_sql = "
                                    UPDATE 
                                        ".DB_PREFIX."customer_credit_preapproved
                                    SET
                                        retailer_name   = '".$this->db->escape($retailer_name)."',
                                        utr             = '".$this->db->escape($utr)."',
                                        firm_name       = '".$this->db->escape($firm_name)."',
                                        approval_date   = '".$this->db->escape($approval_date)."',
                                        approved_limit  = '".$this->db->escape($approved_limit)."',
                                        roi             = '".$this->db->escape($roi)."',
                                        pre_approved_limit_expiry_date = '".$this->db->escape($limit_expiry_date)."',
                                        date_updated    = '".$this->db->escape($udpated_date)."'
                                    WHERE
                                        retailer_id     = '".(int)$retailer_id."'
                            ";

                        $this->db->query($update_sql);

                        $this->updatedActionLogForRBLStatus($retailer_id, 'Credit PreApproved Edit');

                        $remark_data['reason'] = 'Updated Credit PreApproved';

                        $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                    }
                }
            }
           
           echo 'Retailer data updated for RBL pre-approved status.'; 

        }else{

            echo 'PreApproved Data not found on RBL server location ';
        }
    }

    /**
     * @info: Cron protected method to check customer exit in credit pre-approved table
     * 
     * @author: MSA, March 2019 
    */
    protected function isRBLRetailerExistsForCreditPreApproved(int $retailer_id):array {
        $data = array();
        $sql = "
                    SELECT 
                        ccp.id,
                        ccp.retailer_id,
                        ccp.utr,
                        ccp.retailer_name,
                        ccp.firm_name,
                        ccp.approval_date,
                        ccp.approved_limit,
                        ccp.pre_approved_limit_expiry_date,
                        ccp.date_updated,
                        cpo.mobile_number
                    FROM ".DB_PREFIX."customer_credit_preapproved AS ccp
                    LEFT JOIN  ".DB_PREFIX."customer_preonboarding AS cpo 
                                ON cpo.retailer_id=ccp.retailer_id
                    WHERE
                        ccp.retailer_id = '".(int) $retailer_id."'
                        AND
                        ccp.approved_by = 'RBL'
               ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $data = $result->row;
        }
        return $data;
    }


    /**
     * @info: Cron protected method to check customer preonboarding status
     * 
     * @author: MSA, March 2019 
    */
    protected function isRetailerIdExistsInPreOnboardingTable(int $retailer_id):int {
        $sql = "SELECT 
                   id
                FROM ".DB_PREFIX."customer_preonboarding
                WHERE
                   retailer_id = '".(int) $retailer_id."'
               ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
           return 1;
        }
        return 0;
    }


    /**
     * @info: Cron protected method to update credit application action log for 
     *           RBL customer credit status
     * 
     * @author: MSA, March 2019 
    */
    protected function updatedActionLogForRBLStatus(int $retailer_id, string $action)
    {
        $sql = "SELECT 
                    id 
                FROM ".DB_PREFIX."credit_application 
                WHERE
                     customer_id = '".(int)$retailer_id."'
                LIMIT 1   
                ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $credit_application_id = $result->row['id'];
            $log_sql = "INSERT INTO " . DB_PREFIX . "credit_application_action_log 
                        SET
                            credit_application_id = '".(int)$credit_application_id."',
                            type                  = 'CRON',
                            user_id               = '".(int)$retailer_id."',
                            step                  = '0',
                            action                = '".$this->db->escape($action)."',
                            date                  = NOW()
                        ";
            $this->db->query($log_sql);            
        }
    }

    /**
     * @info: Cron protected method to update credit status remark log for 
     *           RBL customer credit status
     * 
     * @author: MSA, March 2019 
    */
    protected function updatedCreditStatusRemarkForRBLStatus(int $retailer_id, array $data)
    {
        $sql = "SELECT 
                    id 
                FROM ".DB_PREFIX."credit_application 
                WHERE
                     customer_id = '".(int)$retailer_id."'
                LIMIT 1   
                ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $credit_application_id = $result->row['id'];
            $credit_limit = $data['credit_limit'] ?? '';
            $log_sql = "INSERT INTO " . DB_PREFIX . "credit_application_status_remarks 
                        SET
                            credit_application_id = '".(int)$credit_application_id."',
                            user_id               = '".$this->db->escape($data['user_id'])."',
                            type                  = '".$this->db->escape($data['type'])."',
                            status                = '".$this->db->escape($data['status'])."',
                            reason                = '".$this->db->escape($data['reason'])."',
                            remark                = '".$this->db->escape($data['remark'])."',
                            credit_limit          = '".$this->db->escape($credit_limit)."',
                            followup_date         = '".$this->db->escape($data['followup_date'])."',
                            date_added            = NOW()
                        ";
            $this->db->query($log_sql);
        }
    }
    
    


    /**
     * @info: Cron public method to read CIF Creation Discrepency CSV file for 
     *           RBL customer credit status
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLcifCreationDiscrepencyData()
    {
        $discrepencyData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['CIF_CREATION_DISCREPENCY']);

        if(!empty($discrepencyData)) {

            // save/update CIF Creation Discrepency data
            foreach($discrepencyData as $retailer) {
                //csv file row data
                $retailer_id                = $retailer[0] ?? 0;
                $retailer_name              = $retailer[1] ?? '';
                $pan_no                     = $retailer[2] ?? '';
                $document_received_flag     = $retailer[3] ?? '';
                $data_value_discrepency     = $retailer[4] ?? '';
                $document_name_discrepency  = $retailer[5] ?? '';
                $discrepency_reasons        = $retailer[6] ?? '';
                $required_resolution        = $retailer[7] ?? '';
                $discrepency_initiation_date= $retailer[8] ?? '';
                $udpated_date               = $retailer[9] ?? ''; // CSV column - Last Updated On
                $resolution_remarks         = $retailer[10] ?? '';
                $current_status             = $retailer[11] ?? '';

                $remark_data = array(
                            'user_id'       => '0',
                            'type'          => 'Note',
                            'status'        => '',
                            'reason'        => $resolution_remarks,
                            'remark'        => 'Discrepency Reason [ '.$discrepency_reasons.' ] initiated on[ '.$discrepency_initiation_date.']',
                            'followup_date' => $udpated_date                      
                    );

                $existing_user = $this->isRBLRetailerExistsForCifDiscrepency($retailer_id);

                if( empty($existing_user) ) {
                    
                    $insert_sql = "
                                    INSERT INTO 
                                        ".DB_PREFIX."customer_credit_cifcreation_discrepency
                                    SET
                                        retailer_id                 = '".(int)$retailer_id."',
                                        retailer_name               = '".$this->db->escape($retailer_name)."',
                                        pan_no                      = '".$this->db->escape($pan_no)."',
                                        document_received_flag      = '".$this->db->escape($document_received_flag)."',
                                        data_value_discrepency      = '".$this->db->escape($data_value_discrepency)."',
                                        document_name_discrepency   = '".$this->db->escape($document_name_discrepency)."',
                                        discrepency_reasons         = '".$this->db->escape($discrepency_reasons)."',
                                        required_resolution         = '".$this->db->escape($required_resolution)."',
                                        discrepency_initiation_date = '".$this->db->escape($discrepency_initiation_date)."',
                                        resolution_remarks          = '".$this->db->escape($resolution_remarks)."',
                                        current_status              = '".$this->db->escape($current_status)."',
                                        date_added                  = NOW(),
                                        date_updated                = '".$this->db->escape($udpated_date)."'
                            ";
                    
                    $this->db->query($insert_sql);

                    $this->updateCreditApplicationForDiscrepencyStatus($retailer_id);

                    $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Discrepency Status Added');

                    $remark_data['reason'] = 'CIF Discrepency Status Added';
                    
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);

                    $sms_data = array(
                                        'action'        => 'cif_creation_discrepency',
                                        'customer_id'   => (int)$retailer_id
                                    );
                    $this->sendSMSAlerts( $sms_data );

                }else{

                    if(strtotime($udpated_date) >= strtotime($existing_user['date_updated'])) {
                        $update_sql = "
                                    UPDATE 
                                        ".DB_PREFIX."customer_credit_cifcreation_discrepency
                                    SET
                                        retailer_id                 = '".(int)$retailer_id."',
                                        retailer_name               = '".$this->db->escape($retailer_name)."',
                                        pan_no                      = '".$this->db->escape($pan_no)."',
                                        document_received_flag      = '".$this->db->escape($document_received_flag)."',
                                        data_value_discrepency      = '".$this->db->escape($data_value_discrepency)."',
                                        document_name_discrepency   = '".$this->db->escape($document_name_discrepency)."',
                                        discrepency_reasons         = '".$this->db->escape($discrepency_reasons)."',
                                        required_resolution         = '".$this->db->escape($required_resolution)."',
                                        discrepency_initiation_date = '".$this->db->escape($discrepency_initiation_date)."',
                                        resolution_remarks          = '".$this->db->escape($resolution_remarks)."',
                                        current_status              = '".$this->db->escape($current_status)."',
                                        date_updated                = '".$this->db->escape($udpated_date)."'
                                    WHERE
                                        retailer_id     = '".(int)$retailer_id."'
                            ";
                        $this->db->query($update_sql);

                        $this->updateCreditApplicationForDiscrepencyStatus($retailer_id);

                        $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Discrepency Status Updated');

                        $remark_data['reason'] = 'CIF Discrepency Status Updated';
                        
                        $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                    }

                }

            }
            echo 'RBL customer data updated for CIF Creation Discrepency status.'; 
        }

    }

    /**
     * @info: Cron public method to update credit application for discrepency status
     * 
     * @author: MSA, March 2019 
    */
    public function updateCreditApplicationForDiscrepencyStatus(int $retailer_id)
    {
        /*Update CIF Creation Status*/
        $update_sql = "UPDATE ".DB_PREFIX."customer_credit_cifcreation
                        SET
                            cif_creation_status = 'DiscrepencyStatus'
                        WHERE
                            retailer_id     = '".(int)$retailer_id."'
                    ";
        $this->db->query($update_sql);
    }

    /**
     * @info: Cron protected method to check customer exit in CIF Creation Discrepency db table
     * 
     * @author: MSA, March 2019 
    */
    protected function isRBLRetailerExistsForCifDiscrepency(int $retailer_id):array {
        $data = array();
        $sql = "
                    SELECT 
                        id,retailer_id,date_updated
                    FROM ".DB_PREFIX."customer_credit_cifcreation_discrepency
                    WHERE
                        retailer_id = '".(int) $retailer_id."'
               ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $data = $result->row;
        }
        return $data;
    }


    /**
     * @info: Cron public method to read CIF Creation CSV file for 
     *           RBL customer credit status
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLcifCreationData()
    {
        $cifCreationData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['CIF_CREATION_REQUEST']);

        $data = array();

        if(!empty($cifCreationData)) {

            // save/update CIF Creation Discrepency data
            foreach($cifCreationData as $retailer) {
                //csv file row data
                $retailer_id = $retailer[0] ?? 0;
                //$data['retailer_id']    = $retailer[0] ?? 0;
                $data['gender']         = $retailer[1] ?? '';
                $data['title']          = $retailer[2] ?? '';
                $data['first_name']     = $retailer[3] ?? '';
                $data['middle_name']    = $retailer[4] ?? '';
                $data['last_name']      = $retailer[5] ?? '';
                $data['dob']            = $retailer[6] ?? '';
                $data['mother_maiden_name'] = $retailer[7] ?? '';
                $data['community']      = $retailer[8] ?? '';
                $data['marital_status'] = $retailer[9] ?? ''; 
                $data['gross_income']   = $retailer[10] ?? '';
                $data['refer_pan_no']   = $retailer[11] ?? '';
                $data['refer_address_type'] = $retailer[12] ?? '';
                $data['refer_address_line1'] = $retailer[13] ?? '';
                $data['refer_address_line2'] = $retailer[14] ?? '';
                $data['refer_address_line3'] = $retailer[15] ?? '';
                $data['refer_city']          = $retailer[16] ?? '';
                $data['refer_state']         = $retailer[17] ?? '';
                $data['refer_postcode']      = $retailer[18] ?? '';
                $data['refer_phone']         = $retailer[19] ?? '';
                $data['corporate_name']      = $retailer[20] ?? '';
                $data['incorporation_date']  = $retailer[21] ?? '';
                $data['entity_pan_no']       = $retailer[22] ?? '';
                $data['annual_turnover']     = $retailer[23] ?? '';
                $data['entity_gst_no']       = $retailer[24] ?? '';
                $data['entity_address_type'] = $retailer[25] ?? '';
                $data['entity_address_line1']= $retailer[26] ?? '';
                $data['entity_address_line2']= $retailer[27] ?? '';
                $data['entity_address_line3']= $retailer[28] ?? '';
                $data['entity_city']         = $retailer[29] ?? '';
                $data['entity_state']        = $retailer[30] ?? '';
                $data['entity_country']      = $retailer[31] ?? '';
                $data['entity_postcode']     = $retailer[32] ?? '';
                $data['entity_phone']        = $retailer[33] ?? '';
                $data['entity_email_id']     = $retailer[34] ?? '';
                $data['consent_details']     = $retailer[35] ?? '';
                $data['request_ref_number']  = $retailer[36] ?? '';
                $data['applied_on']          = $retailer[37] ?? '';  
                $data['tc_acceptance_flag']  = $retailer[38] ?? '';
                $udpated_date = $data['applied_on'];
                $remark_data = array(
                            'user_id'       => '0',
                            'type'          => 'Note',
                            'status'        => '',
                            'reason'        => 'CIF Creation',
                            'remark'        => 'CIF Creation [ '.$data['applied_on'].' ] ',
                            'followup_date' => $udpated_date                      
                    );
                $field_sql = "";
                foreach ($data as $key => $value) {
                    $field_sql .= $key ." = '" . $this->db->escape($value) . "', ";
                }
                
                $existing_user = $this->isRBLRetailerExistsForCifCreation($retailer_id);

                if( empty($existing_user) ) {
                    $field_sql .= " retailer_id     = '".(int)$retailer_id."' ";
                    $insert_sql = " INSERT INTO ".DB_PREFIX."customer_credit_cifcreation SET ";
                    $insert_sql .= $field_sql;
                    $this->db->query($insert_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Creation Status Added');
                    $remark_data['reason'] = 'CIF Creation Status Added';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }else if(strtotime($udpated_date) > strtotime($existing_user['date_updated'])) {
                        $update_sql = " UPDATE ".DB_PREFIX."customer_credit_cifcreation SET ";
                        $update_sql .= rtrim($field_sql,", ");
                        $update_sql .= " WHERE retailer_id     = '".(int)$retailer_id."' ";    
                        $this->db->query($update_sql);
                        $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Creation Status Updated');
                        $remark_data['reason'] = 'CIF Creation Status Updated';
                        $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }
            }
            echo 'RBL customer data updated for CIF Creation status.'; 
        }

    }

    /**
     * @info: Cron protected method to check customer exit in CIF Creation db table
     * 
     * @author: MSA, March 2019 
    */
    protected function isRBLRetailerExistsForCifCreation(int $retailer_id):array {
        $data = array();
        $sql = "
                    SELECT 
                        id,retailer_id,applied_on
                    FROM ".DB_PREFIX."customer_credit_cifcreation
                    WHERE
                        retailer_id = '".(int) $retailer_id."'
               ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $data = $result->row;
        }
        return $data;
    }

    /**
     * @info: Cron public method to read CIF Created data from RBL csv file
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLcifCreatedData()
    {   
        $csvData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['CIF_CREATION_RESPONSE']);
        $data = array();
        if(!empty($csvData)) {

            foreach($csvData as $retailer) {
                //csv file row data
                $date                           = $retailer[0] ?? '';
                $date                           = $this->convertDateFormat($date);
                $anchor_name                    = $retailer[1] ?? '';
                $anchor_id                      = $retailer[2] ?? '';
                $urn_number                     = $retailer[3] ?? '';
                $retailer_id                    = $retailer[4] ?? '';
                $data['retailer_name']          = $retailer[5] ?? '';
                $data['firm_name']              = $retailer[6] ?? '';
                $data['cif_id']                 = $retailer[7] ?? '';
                $data['credit_limit']           = $retailer[8] ?? '';
                $data['created_on']             = $this->convertDateFormat($retailer[9]);
                $data['credit_limit_expiry_date']= $this->convertDateFormat($retailer[10]);
                $data['updated_on']             = $this->convertDateFormat($retailer[11]);
                $udpated_date                   = $data['updated_on'];
                $approved_limit                 = $data['credit_limit'];
                $remark_data = array(
                            'user_id'       => '0',
                            'type'          => 'Comment',
                            'status'        => 'approved',
                            'reason'        => 'RBL Credit Approved',
                            'remark'        => 'Credit Approved on '.$udpated_date,
                            'credit_limit'  => $data['credit_limit'],
                            'followup_date' => $udpated_date                      
                    );
                $field_sql = "";
                foreach ($data as $key => $value) {
                    $field_sql .= $key ." = '" . $this->db->escape($value) . "', ";
                }

                $db_table = 'customer_credit_cif_created';
                //check for existing retailer data in table
                $existing_retailer = $this->isRBLRetailerExists($db_table, $retailer_id, array('id','updated_on'));

                if( empty($existing_retailer) ) {
                    $field_sql .= " retailer_id     = '".(int)$retailer_id."' ";
                    $insert_sql = " INSERT INTO ".DB_PREFIX.$db_table." SET ";
                    $insert_sql .= $field_sql;
                    $this->db->query($insert_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Status - Activated By RBL');
                    $remark_data['reason'] = 'CIF Created Status Added';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                
                    $sms_data = array(
                                    'action'        => 'cif_created',
                                    'customer_id'   => (int)$retailer_id,
                                    'retailer_name' => $data['retailer_name'],
                                    'firm_name'     => $data['firm_name'],
                                    'cif_id'        => substr($data['cif_id'], 0, -4) . '####',
                                    'credit_limit'  => $this->currency->money_format($data['credit_limit'],'INR',1),
                                    'created_on'    => $data['created_on'],
                                    'link'          => WSB_CREDIT_LINK,
                                    'urn_number'    => substr($urn_number, 0, -4) . '####' 
                                );
                    $this->sendSMSAlerts( $sms_data );

                }else if(strtotime($udpated_date) > strtotime($existing_user['updated_on'])) {
                    $update_sql = " UPDATE ".DB_PREFIX.$db_table." SET ";
                    $update_sql .= rtrim($field_sql,", ");
                    $update_sql .= " WHERE retailer_id     = '".(int)$retailer_id."' ";    
                    $this->db->query($update_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Created Status Updated');
                    $remark_data['reason'] = 'CIF Created Status Updated';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }

                
                //create new customer credit table row for newly approved customer
                $this->createCustomerCreditAccount($retailer_id, $approved_limit, $udpated_date);
                
                /*Update CIF Creation Status*/
                $update_sql = "UPDATE ".DB_PREFIX."customer_credit_cifcreation
                                SET
                                    cif_creation_status = 'CifCreated'
                                WHERE
                                    retailer_id     = '".(int)$retailer_id."'
                            ";
                $this->db->query($update_sql);

                /*Update Credit PreApproved Status*/
                $update_sql = "UPDATE ".DB_PREFIX."customer_credit_preapproved
                                SET
                                    cif_creation_status = 'CifCreated'
                                WHERE
                                    retailer_id     = '".(int)$retailer_id."'
                            ";
                $this->db->query($update_sql);

                /*Update Credit Applciation Status*/
                $update_sql = "UPDATE ".DB_PREFIX."credit_application
                                SET
                                    document_status = 'activated_by_rbl'
                                WHERE
                                    customer_id     = '".(int)$retailer_id."'
                            ";
                $this->db->query($update_sql);


            }
            echo 'RBL customer data updated for CIF Created status.';

        }else{

            echo 'CIF Data not found on RBL server location ';
        }
    }
    
    /**
     * @info: Cron public method to read customer Disbursal Request data from RBL csv file
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLDisbursalRequestData()
    {
        $csvData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['DISBURSAL_REQUEST']);
        $data = array(); 
         if(!empty($csvData)) {

            // save/update CIF Creation Discrepency data
            foreach($csvData as $retailer) {
                //csv file row data
                $retailer_id = $retailer[0] ?? 0;
                $data['cif_id']                 = $retailer[1] ?? '';
                $data['retailer_name']          = $retailer[2] ?? '';
                $data['firm_name']              = $retailer[3] ?? '';
                $data['invoice_number']         = $retailer[4] ?? '';
                $data['privious_credit_limit']  = $retailer[5] ?? '';
                $data['invoice_amount']         = $retailer[6] ?? '';
                $data['updated_credit_limit']   = $retailer[7] ?? '';
                $data['invoice_date']           = $retailer[8] ?? '';
                $data['updated_on']             = $retailer[9] ?? '';
                $udpated_date = $data['updated_on'];
                $remark_data = array(
                            'user_id'       => '0',
                            'type'          => 'Note',
                            'status'        => 'Activated',
                            'reason'        => 'Disbursal Request',
                            'remark'        => 'Disbursal Request [ '.$udpated_date.' ] ',
                            'followup_date' => $udpated_date                      
                    );
                $field_sql = "";
                foreach ($data as $key => $value) {
                    $field_sql .= $key ." = '" . $this->db->escape($value) . "', ";
                }

                $db_table = 'customer_credit_disbursal_request';
                //check for existing retailer data in table
                $existing_retailer = $this->isRBLRetailerExists($db_table, $retailer_id, array('id','updated_on'));

                if( empty($existing_retailer) ) {
                    $field_sql .= " retailer_id     = '".(int)$retailer_id."' ";
                    $insert_sql = " INSERT INTO ".DB_PREFIX.$db_table." SET ";
                    $insert_sql .= $field_sql;
                    $this->db->query($insert_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Created Status Added');
                    $remark_data['reason'] = 'Disbursal Request Status Added';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }else if(strtotime($udpated_date) > strtotime($existing_user['updated_on'])) {
                    $update_sql = " UPDATE ".DB_PREFIX.$db_table." SET ";
                    $update_sql .= rtrim($field_sql,", ");
                    $update_sql .= " WHERE retailer_id     = '".(int)$retailer_id."' ";    
                    $this->db->query($update_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Created Status Updated');
                    $remark_data['reason'] = 'Disbursal Request Status Updated';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }
            }
            echo 'RBL customer data updated for Disbursal Request status.'; 
        }
    }

    /**
     * @info: Cron public method to read customer Disbursal Lan data from RBL csv file
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLDisbursalLanData()
    {
        $csvData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['DISBURSAL_RESPONSE']);
        $customers_data = array(); 
        $rblPayments = new RblPayment($this);

        if(!empty($csvData)) 
        {
            foreach($csvData as $retailer) 
            {
                $retailer_id        = $retailer[4] ?? ''; 
                $order_id           = $retailer[11] ?? '';
                $disbursal_date     = $retailer[15] ?? '';
                $updated_date       = $retailer[17] ?? '';

              if( !empty($retailer_id) && !empty($order_id) )
                {
                    $customers_data = array(
                        'date'                  => $retailer[0],
                        'anchor_name'           => $retailer[1],
                        'anchor_id'             => $retailer[2],
                        'urn_number'            => $retailer[3],
                        'retailer_id'           => $retailer_id,
                        'cif_id'                => $retailer[5],
                        'retailer_name'         => $retailer[6],
                        'firm_name'             => $retailer[7],
                        'invoice_number'        => $retailer[8],
                        'privious_credit_limit' => $retailer[9],
                        'invoice_amount'        => $retailer[10],
                        'order_id'              => $order_id,
                        'order_amount'          => $retailer[12],
                        'disbursed_amount'      => $retailer[13],
                        'updated_credit_limit'  => $retailer[14],
                        'disbursal_date'        => $disbursal_date,
                        'due_date'              => $retailer[16],
                        'updated_date'          => $updated_date,
                    );

                    $check_sql = "
                                SELECT 
                                    id
                                FROM 
                                    ".DB_PREFIX."customer_credit_disbursal_lan
                                WHERE
                                    retailer_id     = '".(int) $retailer_id."'
                                    AND
                                    order_id        = '".$this->db->escape(trim($order_id))."'
                           ";

                    $check_result = $this->db->query($check_sql);
                   
                    if(empty($check_result->num_rows)) {
                         $sql = "INSERT INTO ".DB_PREFIX."customer_credit_disbursal_lan 
                                 SET
                                   retailer_id              = '".(int)$customers_data['retailer_id']."',
                                   order_id                 = '".(int)$customers_data['order_id']."',
                                   cif_id                   = '".$this->db->escape($customers_data['cif_id'])."',
                                   retailer_name            = '".$this->db->escape($customers_data['retailer_name'])."',
                                   firm_name                = '".$this->db->escape($customers_data['firm_name'])."',
                                   invoice_number           = '".$this->db->escape($customers_data['invoice_number'])."',
                                   privious_credit_limit    = '".$this->db->escape($customers_data['privious_credit_limit'])."',
                                   invoice_amount           = '".$this->db->escape($customers_data['invoice_amount'])."',
                                   disbursed_amount         = '".$this->db->escape($customers_data['disbursed_amount'])."',
                                   updated_credit_limit     = '".$this->db->escape($customers_data['updated_credit_limit'])."',
                                   disbursal_date           = '".$this->db->escape($this->convertDateFormat($customers_data['disbursal_date']))."',
                                   due_date                 = '".$this->db->escape($this->convertDateFormat($customers_data['due_date']))."',
                                   updated_date             = '".$this->db->escape($this->convertDateFormat($customers_data['updated_date']))."'
                            "; 
                       $this->db->query($sql);
                    }
                    
                }
            }

            echo 'RBL customer data updated for Disbursal Lan status.'; 
        }
    }

    /**
     * @info: Cron protected method to update transaction log for disbursal request status
     * @param: string $invoice_number
     * @author: MSA, March 2019 
    */
    protected function updateRBLTransactionLogForDisbursedStatus($order_id)
    {

        $update_sql = "UPDATE " . DB_PREFIX . "transaction_logs 
                        SET 
                            request_type = 'Disbursed' 
                        WHERE 
                            order_id = '" . (int)$order_id  ."'
                            AND
                            type = 'RBL'
                        ";
        $this->db->query($update_sql);


        /*
        $sql = "
                 SELECT
                    sub.order_id,
                    sub.suborder_id,
                    log.log_id
                FROM
                   ".DB_PREFIX."suborder AS sub
                INNER JOIN 
                    " . DB_PREFIX . "transaction_logs AS log 
                        ON ( log.order_no = sub.suborder_id AND log.order_id = sub.order_id )
                WHERE
                  CONCAT(sub.invoice_prefix,sub.invoice_no) = '".$this->db->escape($invoice_number)."'
                  AND
                  log.request_type = 'DisbursalRequest'
            ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $log_id = $result->row['log_id'] ?? 0;
            $update_sql = "UPDATE " . DB_PREFIX . "transaction_logs 
                            SET 
                                request_type = 'Disbursed' 
                            WHERE 
                                log_id = '" . (int)$log_id  ."'
                                AND
                                type = 'RBL'
                            ";
            $this->db->query($update_sql);
        }

        */
    }

    /**
     * @info: Cron protected method to create customer credit account
     * @param: int $customer_id
     * @param: float $credit_balance
     * @param: string $updated_on
     * @author: MSA, March 2019 
    */
    protected function createCustomerCreditAccount(int $customer_id, float $credit_balance, string $updated_on)
    {
       //check for existing customer credit data
        $select_sql = "
                        SELECT
                            credit_id,
                            credit_balance,
                            DATE(last_updated) as last_updated
                        FROM
                            ".DB_PREFIX."customer_credit
                        WHERE 
                            customer_id = ". (int)$customer_id ."
                            AND 
                            type    = 'RBL' 
                      ";
        $result = $this->db->query($select_sql);
        if($result->num_rows == 0){ 
            $sql = "
                    INSERT INTO
                        ".DB_PREFIX."customer_credit
                    SET
                        customer_id   = ".(int)$customer_id.",
                        credit_balance= '".(float)$credit_balance."',
                        last_updated  = '".$this->db->escape($updated_on)."',
                        type          = 'RBL',   
                        credit_status = '1'
                      ";
            $this->db->query($sql);
        } else {
           $last_updated = $result->row['last_updated'];
           if($updated_on  > $last_updated) {
                $this->updateCustomerCreditBalance($customer_id, $credit_balance);
           }
        }  
    }

    /**
     * @info: Cron protected method to update customer credit balance
     * @param: int $customer_id
     * @param: float $credit_balance
     * @author: MSA, March 2019 
    */
    protected function updateCustomerCreditBalance(int $customer_id, float $credit_balance) 
    {
        $sql = "UPDATE " . DB_PREFIX . "customer_credit 
                SET 
                    credit_balance = '" . $this->db->escape($credit_balance) ."',
                    last_updated = NOW()
                WHERE 
                    customer_id = '" . (int)$customer_id  ."'
                    AND
                    type = 'RBL'
                ";
        $this->db->query($sql);
    }

    /**
     * @info: Cron public method to read customer rejected cases data from RBL csv file
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLRejectedCasesData()
    {
        $csvData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['REJECTED_CASES']);
        
        $data = array();
         if(!empty($csvData)) {

            // save/update CIF Creation Discrepency data
            foreach($csvData as $retailer) {
                //csv file row data
                $retailer_id = $retailer[0] ?? 0;
                $data['retailer_name']          = $retailer[1] ?? '';
                $data['firm_name']              = $retailer[2] ?? '';
                $data['pre_approved_limit']     = $retailer[3] ?? '';
                $data['rejected_on']            = $retailer[4] ?? '';
                $data['rejected_reason']        = $retailer[5] ?? '';
                $data['updated_on']             = $retailer[6] ?? '';
                $udpated_date = $data['updated_on'];
                $remark_data = array(
                            'user_id'       => '0',
                            'type'          => 'Note',
                            'status'        => 'rejected',
                            'reason'        => '',
                            'remark'        => 'Rejected Case [ '.$udpated_date.' ] ',
                            'followup_date' => $udpated_date                      
                    );
                $field_sql = "";
                foreach ($data as $key => $value) {
                    $field_sql .= $key ." = '" . $this->db->escape($value) . "', ";
                }

                $db_table = 'customer_credit_rejected_cases';
                //check for existing retailer data in table
                $existing_retailer = $this->isRBLRetailerExists($db_table, $retailer_id, array('id','updated_on'));

                if( empty($existing_retailer) ) {
                    $field_sql .= " retailer_id     = '".(int)$retailer_id."' ";
                    $insert_sql = " INSERT INTO ".DB_PREFIX.$db_table." SET ";
                    $insert_sql .= $field_sql;
                    $this->db->query($insert_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'Rejected Case Added');
                    $remark_data['reason'] = 'Rejected Case Added';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }else if(strtotime($udpated_date) > strtotime($existing_user['updated_on'])) {
                    $update_sql = " UPDATE ".DB_PREFIX.$db_table." SET ";
                    $update_sql .= rtrim($field_sql,", ");
                    $update_sql .= " WHERE retailer_id     = '".(int)$retailer_id."' ";    
                    $this->db->query($update_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'Rejected Case Updated');
                    $remark_data['reason'] = 'Rejected Case Updated';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }
            }
            echo 'RBL customer data updated for Rejected Case status.'; 
        }
    }

    /**
     * @info: Cron public method to read customer payment received data from RBL csv file
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLPaymentReceivedData()
    {
        $csvData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['PAYMENT_RECEIVED']);
        
        $data = array();
         if(!empty($csvData)) {

            // save/update CIF Creation Discrepency data
            foreach($csvData as $retailer) {
                //csv file row data
                $retailer_id = $retailer[0] ?? 0;
                $data['cif_id']                 = $retailer[1] ?? '';
                $data['retailer_name']          = $retailer[2] ?? '';
                $data['firm_name']              = $retailer[3] ?? '';
                $data['lan']                    = $retailer[4] ?? '';
                $data['lan_amount']             = $retailer[5] ?? '';
                $data['payment_amount']         = $retailer[6] ?? '';
                $data['payment_ref_number']     = $retailer[7] ?? '';
                $data['updated_limit']          = $retailer[8] ?? '';
                $data['updated_on']             = $retailer[9] ?? '';
                $udpated_date = $data['updated_on'];
                $remark_data = array(
                            'user_id'       => '0',
                            'type'          => 'Note',
                            'status'        => '',
                            'reason'        => '',
                            'remark'        => 'Rejected Case [ '.$udpated_date.' ] ',
                            'followup_date' => $udpated_date                      
                    );
                $field_sql = "";
                foreach ($data as $key => $value) {
                    $field_sql .= $key ." = '" . $this->db->escape($value) . "', ";
                }

                $db_table = 'customer_credit_payment_received';
                //check for existing retailer data in table
                $existing_retailer = $this->isRBLRetailerExists($db_table, $retailer_id, array('id','updated_on'));

                if( empty($existing_retailer) ) {
                    $field_sql .= " retailer_id     = '".(int)$retailer_id."' ";
                    $insert_sql = " INSERT INTO ".DB_PREFIX.$db_table." SET ";
                    $insert_sql .= $field_sql;
                    $this->db->query($insert_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'Payment Received Added');
                    $remark_data['reason'] = 'Payment Received Added';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }else if(strtotime($udpated_date) > strtotime($existing_user['updated_on'])) {
                    $update_sql = " UPDATE ".DB_PREFIX.$db_table." SET ";
                    $update_sql .= rtrim($field_sql,", ");
                    $update_sql .= " WHERE retailer_id     = '".(int)$retailer_id."' ";    
                    $this->db->query($update_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'Payment Received Updated');
                    $remark_data['reason'] = 'Payment Received Updated';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }
            }
            echo 'RBL customer data updated for Payment Received status.'; 
        }
    }

    /**
     * @info: Cron public method to read customer recon data from RBL csv file
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLReconData()
    {
        $csvData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['RECON']);
        $data = array();
         if(!empty($csvData)) {

            // save/update CIF Creation Discrepency data
            foreach($csvData as $retailer) {
                //csv file row data
                $retailer_id = $retailer[0] ?? 0;
                $data['cif_id']                 = $retailer[1] ?? '';
                $data['retailer_name']          = $retailer[2] ?? '';
                $data['firm_name']              = $retailer[3] ?? '';
                $data['current_credit_limit']   = $retailer[4] ?? '';                
                $data['pos']                    = $retailer[5] ?? '';
                $data['next_due_date']          = $retailer[6] ?? '';
                $data['overdue_amount']         = $retailer[7] ?? '';
                $data['limit_status']           = $retailer[8] ?? '';
                $data['dpd']                    = $retailer[9] ?? '';
                $data['lan_in_dpd']             = $retailer[10] ?? '';
                $data['last_payment_date']      = $retailer[11] ?? '';
                $data['updated_on']             = $retailer[12] ?? '';
                $udpated_date = $data['updated_on'];
                $remark_data = array(
                            'user_id'       => '0',
                            'type'          => 'Note',
                            'status'        => '',
                            'reason'        => '',
                            'remark'        => 'Recon Status on [ '.$udpated_date.' ] ',
                            'followup_date' => $udpated_date                      
                    );
                $field_sql = "";
                foreach ($data as $key => $value) {
                    $field_sql .= $key ." = '" . $this->db->escape($value) . "', ";
                }

                $db_table = 'customer_credit_recon';
                //check for existing retailer data in table
                $existing_retailer = $this->isRBLRetailerExists($db_table, $retailer_id, array('id','updated_on'));

                if( empty($existing_retailer) ) {
                    $field_sql .= " retailer_id     = '".(int)$retailer_id."' ";
                    $insert_sql = " INSERT INTO ".DB_PREFIX.$db_table." SET ";
                    $insert_sql .= $field_sql;
                    $this->db->query($insert_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'Recon Status Added');
                    $remark_data['reason'] = 'Recon Status Added';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }else if(strtotime($udpated_date) > strtotime($existing_user['updated_on'])){
                    $update_sql = " UPDATE ".DB_PREFIX.$db_table." SET ";
                    $update_sql .= rtrim($field_sql,", ");
                    $update_sql .= " WHERE retailer_id     = '".(int)$retailer_id."' ";    
                    $this->db->query($update_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'Recon Status Updated');
                    $remark_data['reason'] = 'Recon Status Updated';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                }
            }
            echo 'RBL customer data updated for Recon status.'; 
        }
    }

    /**
     * @info: Cron public method to read Pre Approved retailers and check for required
     *        data to create CIF Creation request
     * 
     * @author: MSA, April 2019 
    */
    public function readPreApprovedRetailerForCIFCreation()
    {
        $sql = "
               SELECT 
                cp.id as credit_application_id,
                ccc.retailer_id,
                ccc.gender,
                ccc.title,
                ccc.first_name,
                ccc.middle_name,
                ccc.last_name,
                ccc.dob,
                ccc.mother_maiden_name,
                ccc.community,
                ccc.marital_status,
                ccc.gross_income,
                ccc.refer_pan_no,
                ccc.refer_address_type,
                ccc.refer_address_line1,
                ccc.refer_address_line2,
                ccc.refer_address_line3,
                ccc.refer_city,
                ccc.refer_state,
                ccc.refer_postcode,
                ccc.refer_phone,
                ccc.corporate_name,
                ccc.incorporation_date,
                ccc.entity_pan_no,
                ccc.annual_turnover,
                ccc.entity_gst_no,
                ccc.entity_address_type,
                ccc.entity_address_line1,
                ccc.entity_address_line2,
                ccc.entity_address_line3,
                ccc.entity_city,
                ccc.entity_state,
                ccc.entity_country,
                ccc.entity_postcode,
                ccc.entity_phone,
                ccc.entity_email_id,
                ccc.consent_details,
                ccc.request_ref_number,
                ccc.applied_on,
                ccc.tc_acceptance_flag,
                ccc.date_added
            FROM
                " . DB_PREFIX . "customer_credit_cifcreation AS ccc
            INNER JOIN
                " . DB_PREFIX . "credit_application AS cp ON cp.customer_id=ccc.retailer_id
            WHERE
                cif_creation_status IN ('Pending','DiscrepencyStatus')
            ";
        $result = $this->db->query($sql);

        if($result->num_rows) {

            $applied_on = date('Y-m-d');
            $tc_acceptance_flag = 'Yes';
            foreach ($result->rows as $key => $value) {
                $credit_application_id = $value['credit_application_id'] ?? '';
                $retailer_id = $value['retailer_id'] ?? '';
                $cif_creation_data = array(
                            $retailer_id,
                            $value['gender'] ?? '',
                            $value['title'] ?? '',
                            $value['first_name'] ?? '',
                            $value['middle_name'] ?? '',
                            $value['last_name'] ?? '',
                            $value['dob'] ?? '',
                            $value['mother_maiden_name'] ?? '',
                            $value['community'] ?? '',
                            $value['marital_status'] ?? '',
                            $value['gross_income'] ?? '',
                            $value['refer_pan_no'] ?? '',
                            $this->format_string($value['refer_address_type']) ?? '',
                            $this->format_string($value['refer_address_line1']) ?? '',
                            $this->format_string($value['refer_address_line2']) ?? '',
                            $this->format_string($value['refer_address_line3']) ?? '',
                            $this->format_string($value['refer_city']) ?? '',
                            $value['refer_state'] ?? '',
                            $value['refer_postcode'] ?? '',
                            $value['refer_phone'] ?? '',
                            $this->format_string($value['corporate_name']) ?? '',
                            $value['incorporation_date'] ?? '',
                            $value['entity_pan_no'] ?? '',
                            $value['annual_turnover'] ?? '',
                            $value['entity_gst_no'] ?? '',
                            $value['entity_address_type'] ?? '',
                            $this->format_string($value['entity_address_line1']) ?? '',
                            $this->format_string($value['entity_address_line2']) ?? '',
                            $this->format_string($value['entity_address_line3']) ?? '',
                            $this->format_string($value['entity_city']) ?? '',
                            $value['entity_state'] ?? '',
                            $value['entity_country'] ?? '',
                            $value['entity_postcode'] ?? '',
                            $value['entity_phone'] ?? '',
                            $value['entity_email_id'] ?? '',
                            $value['consent_details'] ?? '',
                            $value['request_ref_number'] ?? '',
                            $applied_on,
                            $tc_acceptance_flag
                        );

                try {

                    //write data in csv file
                    //$this->writeCifCreeationCSVData($retailer_id, $credit_application_id, $cif_creation_data);

                    $obj = new MYSFTP();

                    if( $obj->put( RBL_CSV_FILES['CIF_CREATION_REQUEST'], $cif_creation_data ) )
                    {
                        $remark_data = array(
                                'credit_application_id' => $credit_application_id,
                                'type'                  => 'Note',
                                'status'                => '',
                                'reason'                => 'Sent for CIF creation',
                                'remark'                => 'Sent for CIF creation',
                                'followup_date'         => date('Y-m-d')
                            );
                        $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                        
                        $this->updatedActionLogForRBLStatus($retailer_id, 'Send For CIF Creation');

                        /*Update CIF Creation Status*/
                        $update_sql = "UPDATE ".DB_PREFIX."customer_credit_cifcreation
                                        SET
                                            cif_creation_status = 'SentForCifCreation',
                                            applied_on = NOW()
                                        WHERE
                                            retailer_id     = '".(int)$retailer_id."'
                                    ";
                        $this->db->query($update_sql);

                        $sms_data = array(
                                            'action'        => 'cif_creation',
                                            'customer_id'   => (int)$retailer_id
                                        );
                        $this->sendSMSAlerts( $sms_data );

                    } else { 

                        //error handing, if file not uploaded over RBL srever
                        $this->updatedActionLogForRBLStatus($retailer_id, 'Failed To Send For CIF Creation (Cron Action)');

                    }
                    

                }catch(Exception $e) {
                    // exception handing
                    echo $e->getMessage();
                }
            } 
        }
        echo 'Customer data sent to RBL for CIF Creation process';            
    }

    /**
     * @info: Cron public method to read customer credit discrepency status 
     * 
     * @author: MSA, July 2019 
    */
    public function readCustomerCreditDiscrepency()
    {
        $csvData = $this->readRBLCreditCSVfile(RBL_CSV_FILES['CREDIT_DISCREPENCY']);
        $data = array();
         if(!empty($csvData)) {

            foreach($csvData as $retailer) {

                $retailer_id                    = $retailer[0] ?? 0;
                $data['retailer_name']          = $retailer[1] ?? '';
                $data['pan']                    = $retailer[2] ?? '';
                $data['discrepency_reasons']    = $retailer[3] ?? '';
                $data['required_resolution']    = $retailer[4] ?? '';
                $data['date_added']             = $retailer[5] ?? '';
                $data['updated_on']             = $retailer[6] ?? '';
                $data['status']                 = $retailer[7] ?? '';
                $udpated_date                   = $data['updated_on'];

                $remark_data = array(
                            'user_id'       => '0',
                            'type'          => 'Note',
                            'status'        => '',
                            'reason'        => $data['discrepency_reasons'],
                            'remark'        => 'Customer Credit Discrepency Status['.$data['required_resolution'].']',
                            'followup_date' => $udpated_date                      
                    );
                $field_sql = "";
                foreach ($data as $key => $value) {
                    $field_sql .= $key ." = '" . $this->db->escape($value) . "', ";
                }
                $db_table = 'customer_credit_discrepency';
                $existing_retailer = $this->isRBLRetailerExists($db_table, $retailer_id, array('id','updated_on','status'));
                if( empty($existing_retailer) ) {
                    $field_sql .= " retailer_id     = '".(int)$retailer_id."' ";
                    $insert_sql = " INSERT INTO ".DB_PREFIX.$db_table." SET ";
                    $insert_sql .= $field_sql;
                    $this->db->query($insert_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'Customer Credit Status Added');
                    $remark_data['reason'] = 'Collection Status Added';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                    $this->db->query("UPDATE oc_customer_preonboarding SET status = '0' WHERE retailer_id = ".(int)$retailer_id);
                }else if(strtolower($data['status']) != 'pending' && strtolower($data['status']) != strtolower($existing_retailer['status'])) {
                    $update_sql = " UPDATE ".DB_PREFIX.$db_table." SET ";
                    $update_sql .= rtrim($field_sql,", ");
                    $update_sql .= " WHERE retailer_id     = '".(int)$retailer_id."' ";    
                    $this->db->query($update_sql);
                    $this->updatedActionLogForRBLStatus($retailer_id, 'Customer Credit Status Updated');
                    $remark_data['reason'] = 'Customer Credit Status Updated';
                    $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);
                    if(strtolower($data['status'])!='pending'){
                      $this->db->query("UPDATE oc_customer_preonboarding SET status = '1' WHERE retailer_id = ".(int)$retailer_id);  
                    }
                } 
            }
            echo 'RBL customer credit discrepency data updated.'; 
        }
    }

    /**
     * @info: Cron public method to send SMS, Notification & Email to customer for RBL action
     * @param: array $sms_data
     * @author: MSA, July 2019 
    */
    public function sendSMSAlerts(array $sms_data)
    {
        if(!empty($sms_data) && 
           !empty($sms_data['action']) && 
           !empty($sms_data['customer_id']) 
        )
        {
           $this->sendRBLAlertsThroughCrmAPI($sms_data);
        }
    }


    protected function getCustomerInfo( int $customer_id )
    { 
        $sql = "SELECT 
                    first_name as firstname, 
                    email, 
                    phone_no as telephone
                FROM 
                    ".DB_PREFIX."credit_application
                WHERE 
                    customer_id = ".(int)$customer_id;
        $result = $this->db->query($sql);
        if(!empty($result->num_rows)){
            return $result->row;
        }
        return array();
    }


    /**
     * @info: Cron protected method to send SMS, Notification & Email through CRM API's
     * @param: array $data
     * @author: MSA, July 2019 
    */
    protected function sendRBLAlertsThroughCrmAPI(array $data)
    {
        $customer_id        = (int)$data['customer_id']; 
        $customer           = $this->getCustomerInfo($customer_id);
        $data               = array_merge($data,$customer);
        $this->setRBLEmailData($data);
        $this->setRBLSMSData($data);
        $this->queuePushNotification($data);        
    }

    /**
     * @info: Cron protected method to set customer email data for RBL action
     * @param: array $data
     * @author: MSA, July 2019 
    */
    protected function setRBLEmailData(array &$data)
    {
        $action     = $data['action'] ?? '';
        if(!empty($data['email'])) {
           $data['email_data'] = array(
                'to'        => $data['email'],
                'from'      => array(EMAIL_IDS['credit']['email_id']),
                'reply_to'  => array(EMAIL_IDS['credit']['email_id']),
                'cc'        => array(EMAIL_IDS['vikas']['email_id']),
                'subject'   => $this->getEmailSubject($action),
                'is_html'   => 1,
                'body'      => serialize($this->load->view('default/template/mail/rbl/'.$action.'.tpl', $data)),
            ); 
        }
    }

    /**
     * @info: Cron protected method to set email subject line
     * @param: string $action
     * @author: MSA, July 2019 
    */
    protected function getEmailSubject(string $action)
    {
        switch ($action) {
            
            case 'pre_approved':
                return 'RBL loan pre-approval status ';
                break;
            
            case 'cif_created':
                return 'Get more from RBL Bank Udhaar. Use your remaining credit limit';
                break;

             case 'collection':
                return 'Reminder! Your RBL Bank Udhaar payment due date is near!';
                break;

            case 'collection_defaulted':
                return 'Alert! Outstanding against your RBL Bank Udhaar account has defaulted.';
                break;
            
            default:
                return 'RBL Credit';
                break;
        }
    }

    /**
     * @info: Cron protected method to set customer SMS data for RBL action
     * @param: array $data
     * @author: MSA, July 2019 
    */
    protected function setRBLSMSData(array &$data)
    {
        if(!empty($data['telephone'])) {
            $action     = $data['action'] ?? '';
            $language   = $this->load->language('cron/cron');
            $link = '';
            if(!empty($data['link'])) {
              $short_urls = convertUrlToShortUrl($data['link']);
              $link       = $short_urls['message'] ?? '';  
            }

            if($action =='pre_approved'){
            
                $data['sms'] =  sprintf($language['sms_'.$action], $link);
                $data['notification'] = sprintf($language['notification_'.$action]);
                $data['is_email_to_send'] = 0; 

            }else if($action=='cif_created'){

                $data['sms'] =  sprintf($language['sms_'.$action], $link);
                $data['notification'] = sprintf($language['notification_'.$action]); 
                $data['is_email_to_send'] = 1;
            
            }else if($action=='collection'){
                if(!empty($data['days_diff']) && $data['days_diff'] == 2) {
                    $data['is_email_to_send'] = 1;
                }
                if(!empty($data['is_sms_to_send']) && !empty($data['is_pn_to_send'])) {
                    $due_date = $data['due_date1'];
                    $data['sms'] =  sprintf($language['sms_'.$action], $due_date);
                    if(isset($data['days_diff'])) {
                        $data['notification_title'] = sprintf($language['notification_'.$action.'_t'.$data['days_diff'].'_title']);
                        $data['notification'] = sprintf($language['notification_'.$action.'_t'.$data['days_diff'].'_text']);  
                    }else{
                        $data['notification_title'] = 'Reminder!';
                        $data['notification'] = sprintf($language['notification_'.$action.'_t1_text']);  
                    }
                }
            }else if($action=='collection_defaulted') {
                $data['is_email_to_send'] = 0;
                $data['is_pn_to_send'] = 0;
                $data['is_sms_to_send'] = 1;
                $data['sms'] =  sprintf($language['sms_'.$action], $due_date);
            }
        }
    }

    /**
     * @info: Cron protected method to call CRM API for SMS, Notification & Email
     * @param: array $data
     * @author: MSA, July 2019 
    */
    protected function queuePushNotification(array $data) {
        
        if(empty($data)) { return true; }

        $api_data = array();
        $api_data['notification_sending_time'] = date('Y-m-d h:i:s');
        $api_data['type'] = 'instant';
        
        $is_pn_to_send      = $data['is_pn_to_send'] ?? 1;
        $is_sms_to_send     = $data['is_sms_to_send'] ?? 1;
        $is_email_to_send   = $data['is_email_to_send'] ?? 1;

        $api_data['data'][] = array(
                                    'type'  => 'customer',
                                    'id'    => $data['customer_id'], 
                                    'is_pn_to_send' => $is_pn_to_send,
                                    'pn'    => array(
                                                    'msg_type'         => 1,
                                                    'message'          => $data['notification'],
                                                    'show_instantly'   => 1, 
                                                    'title'            => $data['notification_title'] ?? 'Credit From RBL!!',
                                                    'vibrate'          => 1,
                                                    'sound'            => 1,
                                                    'action'           => array('open_view' => 'open_webview','web_view_url' => $data['link'])
                                                ),
                                    'is_sms_to_send'    => $is_sms_to_send,
                                    'sms'               => array(
                                                                'mobile'    => $data['telephone'],
                                                                'message'   => $data['sms'],
                                                                ),
                                    'is_email_to_send'  => $is_email_to_send,
                                    'email'             => $data['email_data'],
                                    'email_to_head'     => 0,
                                    'pn_to_head'        => 0,
                                    'sms_to_head'       => 0
                                );
        Curl::post( SMS_SENDING_API_URL, $api_data );
    }
    
    /**
     * @info: Cron public method to remove newline and comma from string 
     * @param: string $subject
     * @author: MSA, July 2019 
    */
    public function format_string(string $subject)
    {
        return  preg_replace( "/<br>|\r|\n|,/", "", $subject ); 
    }

    /**
     * @info: Cron public method to read customer collection data from RBL csv file
     * 
     * @author: MSA, March 2019 
    */
    public function readRBLCollectionData()
    {
        $collection_data = $this->readRBLCreditCSVfile(RBL_CSV_FILES['COLLECTION']);
        if(!empty($collection_data))
        {
           $this->setDataHeadings($collection_data);
           $this->processCollectionDataToSave($collection_data);
           $this->processCollectionDataForAlerts($collection_data);
           echo 'Collection data processed!!';
        }
    }
    
    /**
     * @info: protected method to set title for collection data 
     * @param: array $data
     * @author: MSA, Sept 2019 
    */
    protected function setDataHeadings(array &$data)
    {
        $keys = $this->getHeadings('collection');
        array_walk($data,function(& $item) use ($keys) {
                $item = array_combine($keys, $item);
        });
    }

    /**
     * @info: protected method to return titles
     * @param : string $file
     * @author: MSA, Sept 2019 
    */
    protected function getHeadings(string $file)
    {
        switch ($file) {
            case 'collection':
                return ['date','retailer_id','cif_id','retailer_name','firm_name',
                        'current_credit_limit','pos','due_date','overdue_amount',
                        'limit_status','dpd','lan_in_dpd','last_payment_date',
                        'updated_on'
                        ];
                break;
            
            default:
                # code...
                break;
        }
    }

    protected function processCollectionDataToSave(array $data)
    {
        if(!empty($data))
        {
            foreach ($data as $key => $value) 
            {
                $collection_data = $value;
                
                $retailer_id = $value['retailer_id'];
                $db_table = 'customer_credit_collection';
                $existing_retailer = $this->isRBLRetailerExists($db_table, $retailer_id, array('id','updated_on'));

                //remove date and retailer_id field
                unset($collection_data['date']);
                unset($collection_data['retailer_id']);

                $collection_data['date_added']    = $this->convertDateFormat($value['date']);
                $collection_data['due_date']      = $this->convertDateFormat($value['due_date']);
                $collection_data['updated_on']    = $this->convertDateFormat($value['updated_on']);
                $field_sql = "";
                foreach ($collection_data as $field => $field_val) {
                    $field_sql .= $field ." = '" . $this->db->escape($field_val) . "', ";
                }

                if(empty($existing_retailer)) 
                {   
                    $query = " INSERT INTO ".DB_PREFIX.$db_table." SET ";
                    $field_sql .= " retailer_id     = '".(int)$retailer_id."' ";
                    $query .= $field_sql;
                    $this->db->query($query);

                } else if(strtotime($collection_data['updated_on']) > strtotime($existing_retailer['updated_on'])) {

                    $field_sql = rtrim($field_sql,", ");
                    $query = " UPDATE ".DB_PREFIX.$db_table." SET " . $field_sql;
                    $query .= " WHERE retailer_id     = '".(int)$retailer_id."' "; 
                    $this->db->query($query);
                }   
            }
        }
    }

    /**
     * @info: protected method to return titles
     * @param : array $data
     * @author: MSA, Sept 2019 
    */
    protected function processCollectionDataForAlerts(array $data)
    {
        $today  = date('Y-m-d');
        foreach ($data as $key => $value) {
           $due_date            = date('Y-m-d',strtotime($value['due_date']));
            if($due_date >= $today) {
            //Collection Remainers
                $current_date       = new DateTime($today);
                $due_date           = new DateTime($due_date);
                $days_diff          = $due_date->diff($current_date)->format("%a");
                if($days_diff > 0 && $days_diff <= 4) {
                    $value['days_diff']     = $days_diff;
                    $value['action']        = 'collection';
                    $value['customer_id']   = $value['retailer_id'];
                    $value['cif_id']        = substr_replace($value['cif_id'],"***",0,7);
                    $value['due_date1']     = date('d/m/Y',strtotime($value['due_date']));
                    $value['pos']           = $this->currency->money_format($value['pos'],'INR',1);
                    $value['is_sms_to_send']= ($days_diff > 0) ? 1 : 0 ;
                    $value['is_pn_to_send'] = ($days_diff > 0) ? 1 : 0 ;
                    $value['is_email_to_send'] = ($days_diff == 2) ? 1 : 0 ;
                    
                    if(!empty($days_diff)) {

                        $this->sendSMSAlerts($value);

                        $remark_data = array(
                                    'user_id'       => '0',
                                    'type'          => 'Comment',
                                    'status'        => '',
                                    'reason'        => 'Sent Payment Reminder Alerts',
                                    'remark'        => 'Payment Remainder on '.date("d-m-Y"),
                                    'followup_date' => date("Y-m-d")                      
                            );
                        $this->updatedCreditStatusRemarkForRBLStatus((int)$value['retailer_id'], $remark_data);
                    }
                } 
            
            } else {

                //Collection Defaulted
                $current_date       = new DateTime($today);
                $due_date           = new DateTime($due_date);
                $days_diff          = $due_date->diff($current_date)->format("%a");
                if($days_diff == 1) { 
                    $value['days_diff']     = $days_diff;
                    $value['action']        = 'collection_defaulted';
                    $value['customer_id']   = $value['retailer_id'];
                    $value['cif_id']        = substr_replace($value['cif_id'],"***",0,7);
                    $value['due_date1']     = date('d/m/Y',strtotime("+ ". ( 5 - (int)$days_diff ) ." days"));
                    $value['pos']           = $this->currency->money_format($value['pos'],'INR',1);
                    $value['is_sms_to_send']= 1;
                    $value['is_pn_to_send'] = 0;
                    $value['is_email_to_send'] = 0;
                    //$this->sendSMSAlerts($value);
                    $remark_data = array(
                                'user_id'       => '0',
                                'type'          => 'Note',
                                'status'        => '',
                                'reason'        => 'Sent Payment Defaulted Reminder Email',
                                'remark'        => 'Payment Defaulted Reminder on '.date("d-m-Y"),
                                'followup_date' => date("Y-m-d")                      
                        );
                    //$this->updatedCreditStatusRemarkForRBLStatus((int)$value['retailer_id'], $remark_data);
                }
            }
        }
    }


    /**
    * Method to check CIF Status for all pre-approved users who has completed their journey
    * @param: void 
    * @return: void
    * @author: MSA Sept 2019
    */
    public function checkPreApprovedUsersCIFStatus()
    {
        $listOfPreApprovedUsers = $this->getListOfPreApprovedUsers();

        if(!empty($listOfPreApprovedUsers)) {

            foreach ($listOfPreApprovedUsers as $key => $value) {
                
                $customerCIFStatus      = $this->getCustomerCIFStatus( $value );

                if( isset($customerCIFStatus['response']['RDFAnchorJourney']['status']) 
                    && 
                    ( 
                        $customerCIFStatus['response']['RDFAnchorJourney']['status'] == '0' 
                        ||
                        $customerCIFStatus['response']['RDFAnchorJourney']['status'] == '' 
                    )
                ) {

                    $customerCIFStatus['customer'] = $value;

                    $this->updateRBLUserForCIFStatus( $customerCIFStatus );
                
                } else { 

                    //update log for API call response data
                    $obj = new RblPayment($this);
                    $log_data = array(
                                        'user'          => 'CRON',
                                        'api_type'      => 'cifStatus',
                                        'customer_id'   => (int)$customerCIFStatus['retailer_id'],
                                        'api_endpoint'  => $customerCIFStatus['api_endpoint'],
                                        'api_post_data' => json_encode($customerCIFStatus['post_data']),
                                        'api_response_data' => json_encode($customerCIFStatus['response']),
                                        'api_status'    => 1,
                                        'status'        => 'SUCCESS'
                                    );
                    $obj->createRblApiLog($log_data);
                }
            }
        }
        echo "CIF Status updated for RBL pre-approved users.";
    }

    /**
    * Method to get list of all pre-approved users 
    * @param:  void 
    * @return: void
    * @author: MSA Sept 2019
    */
    public function getListOfPreApprovedUsers(): array
    {
        $sql = "SELECT 
                    ccp.retailer_id,
                    ccp.utr,
                    ccp.retailer_name,
                    ccp.firm_name
                FROM 
                    ".DB_PREFIX."customer_credit_preapproved AS ccp
                JOIN 
                    ".DB_PREFIX."customer AS c ON c.customer_id = ccp.retailer_id
                WHERE
                    ccp.utr != ''
                    AND
                    ccp.cif_creation_status = 'JourneyCompleted'
                ";

        $result = $this->db->query($sql);

        $data = array();

        if($result->num_rows) {

            $data = $result->rows;
        }

        return $data;
    }

    /**
    * Method to call RBL API to get customer CIF Status
    * @param:  array $customer_data 
    * @return: array $response
    * @author: MSA Sept 2019
    */
    public function getCustomerCIFStatus( array $customer_data )
    {
        try {
           
           $response = array();
           $URNumber    = $customer_data['utr'] ?? '';
           $retailerId  = $customer_data['retailer_id'] ?? '';

           $post_data['RDFAnchorJourney']['data'] = array(
                                                            'URNumber' => $URNumber,
                                                            'request'  => array(
                                                                    'cifStatus' => array(
                                                                        'retailerId' => $retailerId,
                                                                        'anchorId'   => RBL_API_ANCHORID
                                                                    )
                                                                )
                                                            );

           $api_endpoint   = RBL_API_ENDPOINTS.'?client_id='.RBL_API_CLIENT_ID.'&client_secret='.RBL_API_SECRET;
           
           $result         = Curl::callRblAPI( $api_endpoint, $post_data );
           
           $response       = json_decode($result,true);
           
           return array(
                        'retailer_id'   => $retailerId,
                        'api_endpoint'  => $api_endpoint,
                        'post_data'     => $post_data,
                        'response'      => $response
                    );

        } catch(Exception $e) {

            //handle exception
        }
    }

    /**
    * Method to update customer details for his CIF Status
    * @param:  int $customer_id 
    * @return: void
    * @author: MSA Sept 2019
    */
    public function updateRBLUserForCIFStatus(array $data)
    {
        $retailer_id    = $data['customer']['retailer_id'] ?? '';
        $retailer_name  = $data['customer']['retailer_name'] ?? '';
        $firm_name      = $data['customer']['firm_name'] ?? '';
        $cif_id         = $data['response']['RDFAnchorJourney']['data']['response']['cifStatus']['cifId'] ?? '';
        $credit_limit   = $data['response']['RDFAnchorJourney']['data']['response']['cifStatus']['limitAvailable'] ?? '';

        ## check for existing retailer data in table
        $db_table = 'customer_credit_cif_created';
        $existing_retailer = $this->isRBLRetailerExists($db_table, $retailer_id, array('id'));
        if(empty($existing_retailer)) {
        ## save data in cif created table
            $sql = "INSERT INTO ".DB_PREFIX.$db_table." 
                    SET 
                        retailer_id   = '".(int)$retailer_id."', 
                        retailer_name = '".$this->db->escape($retailer_name)."',
                        firm_name     = '".$this->db->escape($firm_name)."',
                        cif_id        = '".$this->db->escape($cif_id)."',
                        credit_limit  = '".$this->db->escape($credit_limit)."',
                        created_on    = NOW(),
                        credit_limit_expiry_date = DATE_ADD(NOW(), INTERVAL 6 MONTH),
                        updated_on    = NOW()
                   ";
            $this->db->query($sql); 

        ## save credit log data
            $this->updatedActionLogForRBLStatus($retailer_id, 'CIF Status - Activated By RBL');

        ## save credit remark data
            $udpated_date = date("Y-m-d");
            $remark_data = array(
                            'user_id'       => (int)$retailer_id,
                            'type'          => 'Comment',
                            'status'        => 'approved',
                            'reason'        => 'RBL Credit Approved',
                            'remark'        => 'Credit Approved on '.date('d-m-Y'),
                            'credit_limit'  => $credit_limit,
                            'followup_date' => $udpated_date                     
                    );
            $this->updatedCreditStatusRemarkForRBLStatus($retailer_id, $remark_data);

        ## save rbl log data
            $obj = new RblPayment($this);
            $log_data = array(
                                'user'          => 'CRON',
                                'api_type'      => 'cifStatus',
                                'customer_id'   => (int)$retailer_id,
                                'api_endpoint'  => $data['api_endpoint'],
                                'api_post_data' => json_encode($data['post_data']),
                                'api_response_data' => json_encode($data['response']),
                                'api_status'    => 0,
                                'status'        => 'SUCCESS'
                            );
            $obj->createRblApiLog($log_data);

        ## send Notification, SMS & Email for CIF created status
            $sms_data = array(
                        'action'        => 'cif_created',
                        'customer_id'   => (int)$retailer_id,
                        'retailer_name' => $retailer_name,
                        'firm_name'     => $firm_name,
                        'cif_id'        => $cif_id,
                        'credit_limit'  => $this->currency->money_format($credit_limit,'INR',1),
                        'created_on'    => date('d-m-Y'),
                        'link'          => WSB_CREDIT_LINK,
                    );
            $this->sendSMSAlerts( $sms_data );

        ## create new customer credit table row for newly approved customer
            $this->createCustomerCreditAccount((int)$retailer_id, $credit_limit, $udpated_date);
            
        ## Update Credit PreApproved Status*/
            $update_sql = "UPDATE ".DB_PREFIX."customer_credit_preapproved
                            SET
                                cif_creation_status = 'CifCreated'
                            WHERE
                                retailer_id     = '".(int)$retailer_id."'
                        ";
            $this->db->query($update_sql);

        ## Update Credit Applciation Status
            $update_sql = "UPDATE ".DB_PREFIX."credit_application
                            SET
                                document_status = 'activated_by_rbl'
                            WHERE
                                customer_id     = '".(int)$retailer_id."'
                        ";
            $this->db->query($update_sql);
        }

    }

}
