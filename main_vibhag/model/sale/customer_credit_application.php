<?php
class ModelSaleCustomerCreditApplication extends Model {

    /**
    * Kusum Joshi
    * get Customer Credit Applications List based on filters
    **/

    public function getCustomerCreditApplication($filter_data = array()) {

        $sql = "SELECT capp.id,concat(capp.first_name, ' ',capp.middle_name, ' ', capp.last_name ) as name,
                        capp.phone_no,
                        capp.email,
                        capp.gst_number,
                        capp.current_city,
                        capp.current_state,
                        capp.current_pincode,
                        capp.permanent_pincode,
                        capp.permanent_city,
                        capp.company_name,
                        capp.draft,
                        capp.crif_score,
                        capp.version,
                        capp.created_date as date_added,
                        cas.credit_response_application_id as application_id,
                        cas.credit_response_applicant_id as applicant_id,
                        cas.credit_response_redirect_url as redirect_url,
                        cas.epaylater_otp_flag as epaylater_otp_flag,
                        cas.epaylater_kyc_otp_flag as epaylater_kyc_otp_flag,
                        capp.customer_id,
                        cas.kyc_done,
                        cpd.name as cpd_name,
                        capp.document_status,
                        capp.last_modified,
                        group_concat(cp.name) as shared_with,
                        ccp.id as credit_preapproved_id,
                        ccp.approved_by,
                        ccp.cif_creation_status,
                        cccf.id as cif_creation_id,
                        cccs.id as discrepency_id,
                        capp.current_address,
                        capp.permanent_address
                FROM ". DB_PREFIX ."credit_application capp 
                LEFT JOIN ".DB_PREFIX."credit_application_share cas ON (capp.id = cas.credit_application_id) 
                LEFT JOIN ".DB_PREFIX."credit_partners cp ON (cas.credit_partner_id = cp.credit_partner_id) 
                LEFT JOIN ".DB_PREFIX."credit_application_document cpd ON(capp.id = cpd.credit_application_id) 
                LEFT JOIN ".DB_PREFIX."customer_credit_cifcreation cccf ON cccf.retailer_id = capp.customer_id
                LEFT JOIN ".DB_PREFIX."customer_credit_cifcreation_discrepency cccs ON cccs.retailer_id = capp.customer_id
                ";

            $add_left_join_on_ccp = true;
            if (isset($filter_data['filter_rbl_approved'])) {

                switch($filter_data['filter_rbl_approved']) {
                    case 'sent-for-approved': 
                        $sql .= " INNER JOIN ".DB_PREFIX."customer_preonboarding cpb ON cpb.retailer_id = capp.customer_id";
                        break;
                    
                    case 'non-approved':
                        $sql .= " LEFT JOIN ".DB_PREFIX."customer_preonboarding cpb ON (cpb.retailer_id = capp.customer_id AND cpb.retailer_id IS NULL)";
                        break;
                    case 'pre-approved-by-rbl':
                        $sql .= " INNER JOIN 
                                    ".DB_PREFIX."customer_credit_preapproved ccp 
                                  ON (ccp.retailer_id = capp.customer_id AND ccp.approved_by = 'RBL')
                                ";
                        $add_left_join_on_ccp = false;
                        break;
                    case 'cif-created-by-rbl':
                        $sql .= " INNER JOIN 
                                    ".DB_PREFIX."customer_credit_preapproved ccp 
                                  ON (ccp.retailer_id = capp.customer_id AND ccp.approved_by = 'RBL' AND 
                                        ccp.cif_creation_status = 'CifCreated' )
                                ";
                        $add_left_join_on_ccp = false;
                        break;
                    case 'rbl-journey-completed':
                        $sql .= " INNER JOIN 
                                    ".DB_PREFIX."customer_credit_preapproved ccp 
                                    ON (ccp.retailer_id = capp.customer_id AND ccp.approved_by = 'RBL' AND 
                                        ccp.cif_creation_status = 'JourneyCompleted' )
                                ";
                        $add_left_join_on_ccp = false;
                        break;
                }
                
            }
            if ($add_left_join_on_ccp) {
                $sql .= " LEFT JOIN 
                                    ".DB_PREFIX."customer_credit_preapproved ccp 
                                  ON (ccp.retailer_id = capp.customer_id AND ccp.approved_by = 'RBL')
                                ";
            }

            if (!empty($filter_data['filter_sms_log_type'])) {
                $sql .= " INNER JOIN ".DB_PREFIX."wsb_customer_to_sms_criteria_sync wps ON capp.customer_id = wps.customer_id ";
            }

            if (!empty($filter_data['filter_document_status'])) {

                if($filter_data['filter_document_status'] == "activated" || $filter_data['filter_document_status'] == "BLOCKED")
                    $sql .= " INNER JOIN ".DB_PREFIX."customer_wsb_credit cwc ON capp.customer_id = cwc.customer_id ";
                else
                    $sql .= " LEFT JOIN ".DB_PREFIX."customer_wsb_credit cwc ON capp.customer_id = cwc.customer_id AND cwc.status IN ('ENABLED')";
            }

            if (!empty($filter_data['filter_customer_id'])) {
                $sql .= " INNER JOIN ".DB_PREFIX."customer c ON capp.customer_id = c.customer_id ";
            }

            if (!empty($filter_data['filter_followup_date'])) {
                $sql = $sql." INNER JOIN ".DB_PREFIX."credit_application_status_remarks casr ON(capp.id = casr.credit_application_id) " ;
            }
            
          
           $sql = $sql."WHERE 1=1 ";
          
        $implode=array();


        if (!empty($filter_data['filter_customer_id']) ) {
            //======get master id=======//
            $all_related_ids = Customer::getRelatedIdsByCustomerId($this->db, (int)$filter_data['filter_customer_id']);
            if(!empty($all_related_ids)){
                $implode[] = " c.customer_id IN (". $all_related_ids .") ";
            }
            
        }

        if (!empty($filter_data['filter_followup_date']) ) {
            $implode[] = "casr.followup_date = '" . $this->db->escape($filter_data['filter_followup_date']) . "' ";
        }

        if (!empty($filter_data['filter_document_status']) && $filter_data['filter_document_status'] == "activated") {
            $implode[] = "cwc.status = 'ENABLED' ";
        }

        if (!empty($filter_data['filter_date_added'])) {
            $implode[] = "capp.created_date >= '" . $this->db->escape($filter_data['filter_date_added']) . " 00:00:00' ";
            $implode[] = "capp.created_date <= '" . $this->db->escape($filter_data['filter_date_added']) . " 23:59:59' ";
        }

        if (!empty($filter_data['filter_date_modified'])) {
            $implode[] = "capp.last_modified >= '" . $this->db->escape($filter_data['filter_date_modified']) . " 00:00:00' ";
            $implode[] = "capp.last_modified <= '" . $this->db->escape($filter_data['filter_date_modified']) . " 23:59:59' ";
        }

        if (!empty($filter_data['filter_name'])) {
            $fts_str = getFullTextSearchString($filter_data['filter_name']);
            $implode[] = " MATCH(capp.first_name, capp.middle_name, capp.last_name) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }

        if ($filter_data['filter_step'] != null) {
            $implode[] = "capp.draft = '" . $this->db->escape($filter_data['filter_step']) . "'";
        }

        if (!empty($filter_data['filter_email'])) {
            $implode[] = "capp.email LIKE '%" . $this->db->escape($filter_data['filter_email']) . "%'";
        }

        if (!empty($filter_data['filter_telephone'])) {
            $implode[] = "capp.phone_no LIKE '%" . $this->db->escape($filter_data['filter_telephone']) . "%'";
        }
        if (!empty($filter_data['filter_application_id'])) {
            $app_ids_arr = explode(",", $filter_data['filter_application_id']);
            $app_ids_arr = array_unique(array_filter(array_map('intval', $app_ids_arr)));
            $filter_application_id = implode(",", $app_ids_arr);
            if (!empty($filter_application_id)) {
                $implode[] = "capp.id IN (" . $this->db->escape($filter_application_id) . ") ";
            }
        }
        if (!empty($filter_data['filter_pancard'])) {
            $implode[] = "capp.pan_no = '" . $this->db->escape($filter_data['filter_pancard']) . "'";
        }
        if (!empty($filter_data['filter_document_type'])) {
            $implode[] = "cpd.name = '" . $this->db->escape($filter_data['filter_document_type']) . "'";
        }

        if (!empty($filter_data['filter_by_crif'])) {
            $implode[] = " capp.crif_score IS NOT NULL ";
        }

        if (!empty($filter_data['filter_city'])) {
            $fts_str = getFullTextSearchString($filter_data['filter_city']);
            $implode[] = " MATCH(capp.current_city, capp.permanent_city) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }

        if (!empty($filter_data['filter_state'])) {
            $implode[] = " (capp.current_state='" . $this->db->escape($filter_data['filter_state']) . "' OR capp.permanent_state= '" . $this->db->escape($filter_data['filter_state']) . "') ";
        }

        if (!empty($filter_data['filter_sms_log_type'])) {

            $filter_sms_log_type = $filter_data['filter_sms_log_type'];
            if($filter_data['filter_sms_log_type'] == "mswipe"){
                $filter_sms_log_type = "pos";
            }

            $implode[] = "wps.criteria = '" . $this->db->escape($filter_sms_log_type) . "' ";

            /*$filter_custom_array = array("filter_sms_log_type"=>$filter_data['filter_sms_log_type'], "start"=>$filter_data['start'], "limit"=>$filter_data['limit']);
            $all_process_sms_customers = $this->get_process_sms_customers($filter_custom_array, false);
            $implode[] = " capp.customer_id IN (" . $all_process_sms_customers . ") ";*/

        }

        // duplicate applications will not be visible until unless duplicate document status is applied
        if (!empty($filter_data['filter_document_status'])) {
            switch($filter_data['filter_document_status']) {
                case 'BLOCKED':    
                                $implode[] = "cwc.status = '" . $this->db->escape($filter_data['filter_document_status']) . "'";
                                $implode[] = " (capp.document_status IS NULL OR capp.document_status != 'duplicate') ";
                                break;
                case 'no_status':   
                                $implode[] = " (capp.document_status IS NULL OR capp.document_status = '') ";
                                $implode[] = " cwc.customer_id IS NULL ";
                                break;
                case 'activated':   
                                $implode[] = " (capp.document_status IS NULL OR capp.document_status != 'duplicate') ";
                                break; 
                case 'duplicate':   
                                $implode[] = " capp.document_status = 'duplicate' ";
                                break;
                        default:  
                                $implode[] = "capp.document_status = '" . $this->db->escape($filter_data['filter_document_status']) . "'";
                                $implode[] = " cwc.customer_id IS NULL ";
                                break;
            }
        } else {
            $implode[] = " (capp.document_status IS NULL OR capp.document_status != 'duplicate') ";
        }

        if(!empty($implode)){
            $sql .= " AND " . implode(" AND ", $implode);
        }

        $sql .= " GROUP BY capp.id ";

        $implode_having =array();

        if (!empty($filter_data['filter_discrepency_status'])) 
        {   
           if($filter_data['filter_discrepency_status'] == 'yes') {
                $implode_having[] = " discrepency_id IS NOT NULL ";
            }else if($filter_data['filter_discrepency_status'] == 'no'){
                $implode_having[] = " discrepency_id IS NULL ";
            } 
        }

        if(!empty($implode_having)){
            $sql .= " HAVING " . implode(" AND ", $implode_having);
        }


        if (isset($filter_data['sort'])) {
            $sql .= " ORDER BY " . $filter_data['sort'];
        } else {
            $sql .= " ORDER BY capp.id";
        }
        if (isset($filter_data['order']) && ($filter_data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($filter_data['start']) || isset($filter_data['limit'])) {
            if ($filter_data['start'] < 0) {
                $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
                $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
        }

        return $this->db->query($sql)->rows;
    }
    /** Kusum Joshi
    * Get All Credit Partners List
    **/
    public function getCreditPartnersList(){
         $sql = "SELECT *
                FROM ". DB_PREFIX ."credit_partners";
        return $this->db->query($sql)->rows;
    }


    /**
     * Public method to get  WSB CREDIT status for given customer ids
     * @param: $customer_id
     * @return: string status
     * @author: Rahul, 20 July 2019
    */
    public function wsbCreditStatus($customer_id){
        $status='';
        $sql = "SELECT 
                    status
                FROM
                    ".DB_PREFIX."customer_wsb_credit
                WHERE
                    customer_id = '" . (int)$customer_id . "'";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $status = strtolower($query->rows['0']['status']);
        }
        return $status;
    }

    

    /** Kusum Joshi
    * get Total Customer credit Applications based on filters
    **/
    public function getTotalCustomerCreditApplication($filter_data = array()) {

        $sql = "SELECT
                  capp.id,
                  ccp.id as credit_preapproved_id,
                  ccp.approved_by,
                  ccp.cif_creation_status,
                  cccs.id as discrepency_id
                FROM
                  ". DB_PREFIX ."credit_application capp
                LEFT JOIN
                  ".DB_PREFIX."credit_application_share cas ON
                    capp.id = cas.credit_application_id
                LEFT JOIN
                  ".DB_PREFIX."credit_partners cp ON
                    cas.credit_partner_id = cp.credit_partner_id
                LEFT JOIN
                  ".DB_PREFIX."credit_application_document cpd ON
                    capp.id = cpd.credit_application_id 
                LEFT JOIN ".DB_PREFIX."customer_credit_cifcreation_discrepency cccs ON 
                    cccs.retailer_id = capp.customer_id       
                ";

            $add_left_join_on_ccp = true;
            if (isset($filter_data['filter_rbl_approved'])) {

                switch($filter_data['filter_rbl_approved']) {
                    case 'sent-for-approved': 
                        $sql .= " INNER JOIN ".DB_PREFIX."customer_preonboarding cpb ON cpb.retailer_id = capp.customer_id";
                        break;
                    
                    case 'non-approved':
                        $sql .= " LEFT JOIN ".DB_PREFIX."customer_preonboarding cpb ON (cpb.retailer_id = capp.customer_id AND cpb.retailer_id IS NULL)";
                        break;
                    case 'pre-approved-by-rbl':
                        $sql .= " INNER JOIN 
                                    ".DB_PREFIX."customer_credit_preapproved ccp 
                                  ON (ccp.retailer_id = capp.customer_id AND ccp.approved_by = 'RBL')
                                ";
                        $add_left_join_on_ccp = false;
                        break;
                    case 'cif-created-by-rbl':
                        $sql .= " INNER JOIN 
                                    ".DB_PREFIX."customer_credit_preapproved ccp 
                                  ON (ccp.retailer_id = capp.customer_id AND ccp.approved_by = 'RBL' AND 
                                        ccp.cif_creation_status = 'CifCreated' )
                                ";
                        $add_left_join_on_ccp = false;
                        break;
                    case 'rbl-journey-completed':
                        $sql .= " INNER JOIN 
                                    ".DB_PREFIX."customer_credit_preapproved ccp 
                                    ON (ccp.retailer_id = capp.customer_id AND ccp.approved_by = 'RBL' AND 
                                        ccp.cif_creation_status = 'JourneyCompleted' )
                                ";
                        $add_left_join_on_ccp = false;
                        break;
                }
                
            }
            if ($add_left_join_on_ccp) {
                $sql .= " LEFT JOIN 
                                    ".DB_PREFIX."customer_credit_preapproved ccp 
                                  ON (ccp.retailer_id = capp.customer_id AND ccp.approved_by = 'RBL')
                                ";
            }
                    
            if (!empty($filter_data['filter_sms_log_type'])) {
                $sql .= " INNER JOIN ".DB_PREFIX."wsb_customer_to_sms_criteria_sync wps ON capp.customer_id = wps.customer_id ";
            }

            if (!empty($filter_data['filter_document_status'])) {

                if($filter_data['filter_document_status'] == "activated" || $filter_data['filter_document_status'] == "BLOCKED")
                    $sql .= " INNER JOIN ".DB_PREFIX."customer_wsb_credit cwc ON capp.customer_id = cwc.customer_id ";
                else
                    $sql .= " LEFT JOIN ".DB_PREFIX."customer_wsb_credit cwc ON capp.customer_id = cwc.customer_id AND cwc.status IN ('ENABLED')";
            }

            if (!empty($filter_data['filter_customer_id'])) {
                $sql .= " INNER JOIN ".DB_PREFIX."customer c ON capp.customer_id = c.customer_id ";
            }

            if (!empty($filter_data['filter_followup_date'])) {
                $sql = $sql." INNER JOIN ".DB_PREFIX."credit_application_status_remarks casr ON(capp.id = casr.credit_application_id) " ;
            }
            
           $sql = $sql."WHERE 1=1 ";

        $implode=array();

        if (!empty($filter_data['filter_customer_id']) ) {
            //======get all related customer ids for the given customer id=======//
            $all_related_ids = Customer::getRelatedIdsByCustomerId($this->db, (int)$filter_data['filter_customer_id']);
            if(!empty($all_related_ids)){
                $implode[] = " c.customer_id IN (". $all_related_ids .") ";
            }
        }

        if (!empty($filter_data['filter_followup_date']) ) {
            $implode[] = "casr.followup_date = '" . $this->db->escape($filter_data['filter_followup_date']) . "' ";
        }

        if (!empty($filter_data['filter_document_status']) && $filter_data['filter_document_status'] == "activated") {
            $implode[] = "cwc.status = 'ENABLED' ";
        }

        if (!empty($filter_data['filter_date_added'])) {
            $implode[] = "capp.created_date >= '" . $this->db->escape($filter_data['filter_date_added']) . " 00:00:00'";
            $implode[] = "capp.created_date <= '" . $this->db->escape($filter_data['filter_date_added']) . " 23:59:59'";
        }

        if (!empty($filter_data['filter_date_modified'])) {
            $implode[] = "capp.last_modified >= '" . $this->db->escape($filter_data['filter_date_modified']) . " 00:00:00'";
            $implode[] = "capp.last_modified <= '" . $this->db->escape($filter_data['filter_date_modified']) . " 23:59:59'";
        }

        if (!empty($filter_data['filter_name'])) {
            $fts_str = getFullTextSearchString($filter_data['filter_name']);
            $implode[] = " MATCH(capp.first_name, capp.middle_name, capp.last_name) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }

        if ($filter_data['filter_step'] != null) {
            $implode[] = "capp.draft = '" . $this->db->escape($filter_data['filter_step']) . "'";
        }

        if (!empty($filter_data['filter_email'])) {
            $implode[] = "capp.email LIKE '%" . $this->db->escape($filter_data['filter_email']) . "%'";
        }

        if (!empty($filter_data['filter_telephone'])) {
            $implode[] = "capp.phone_no LIKE '%" . $this->db->escape($filter_data['filter_telephone']) . "%'";
        }
        if (!empty($filter_data['filter_application_id'])) {
            $app_ids_arr = explode(",", $filter_data['filter_application_id']);
            $app_ids_arr = array_unique(array_filter(array_map('intval', $app_ids_arr)));
            $filter_application_id = implode(",", $app_ids_arr);
            if (!empty($filter_application_id)) {
                $implode[] = "capp.id IN (" . $this->db->escape($filter_application_id) . ") ";
            }
        }
        if (!empty($filter_data['filter_pancard'])) {
            $implode[] = "capp.pan_no = '" . $this->db->escape($filter_data['filter_pancard']) . "'";
        }
        if (!empty($filter_data['filter_document_type'])) {
            $implode[] = "cpd.name = '" . $this->db->escape($filter_data['filter_document_type']) . "'";
        }

        if (!empty($filter_data['filter_by_crif'])) {
            $implode[] = " capp.crif_score IS NOT NULL ";
        }

        if (!empty($filter_data['filter_city'])) {
            $fts_str = getFullTextSearchString($filter_data['filter_city']);
            $implode[] = " MATCH(capp.current_city, capp.permanent_city) AGAINST ('" . $this->db->escape($fts_str) . "' IN BOOLEAN MODE)";
        }

        if (!empty($filter_data['filter_state'])) {
            $implode[] = " (capp.current_state='" . $this->db->escape($filter_data['filter_state']) . "' OR capp.permanent_state= '" . $this->db->escape($filter_data['filter_state']) . "') ";
        }

        if (!empty($filter_data['filter_sms_log_type'])) {

            $filter_sms_log_type = $filter_data['filter_sms_log_type'];
            if($filter_data['filter_sms_log_type'] == "mswipe"){
                $filter_sms_log_type = "pos";
            }

            $implode[] = "wps.criteria = '" . $this->db->escape($filter_sms_log_type) . "' ";

            /*if($filter_data['filter_sms_log_type'] == "udaan" || $filter_data['filter_sms_log_type'] == "paytm" || $filter_data['filter_sms_log_type'] == "lazypay")
            {
                $implode[] = " wps.criteria_type = 'competitor_credit' AND wps.txn_type = '" . $this->db->escape($filter_data['filter_sms_log_type']) . "' ";
            }
            else if($filter_data['filter_sms_log_type'] == "mswipe"){

                $sql .= " AND wps.criteria_type = 'pos' AND wps.sender_name like '%mswipe%' ";
            }
            else
            {
                $implode[] = "wps.criteria_type = '" . $this->db->escape($filter_data['filter_sms_log_type']) . "' ";
            }*/

           /* $filter_custom_array = array("filter_sms_log_type"=>$filter_data['filter_sms_log_type'], "start"=>$filter_data['start'], "limit"=>$filter_data['limit']);
            $all_process_sms_customers = $this->get_process_sms_customers($filter_custom_array, false);
            $implode[] = " capp.customer_id IN (" . $all_process_sms_customers . ") ";*/
        }

         // duplicate applications will not be visible until unless duplicate document status is applied
        if (!empty($filter_data['filter_document_status'])) {
            switch($filter_data['filter_document_status']) {
                case 'BLOCKED':    
                                $implode[] = "cwc.status = '" . $this->db->escape($filter_data['filter_document_status']) . "'";
                                $implode[] = " (capp.document_status IS NULL OR capp.document_status != 'duplicate') ";
                                break;
                case 'no_status':   
                                $implode[] = " (capp.document_status IS NULL OR capp.document_status = '') ";
                                $implode[] = " cwc.customer_id IS NULL ";
                                break;
                case 'activated':   
                                $implode[] = " (capp.document_status IS NULL OR capp.document_status != 'duplicate') ";
                                break; 
                case 'duplicate':   
                                $implode[] = " capp.document_status = 'duplicate' ";
                                break;
                        default:  
                                $implode[] = "capp.document_status = '" . $this->db->escape($filter_data['filter_document_status']) . "'";
                                $implode[] = " cwc.customer_id IS NULL ";
                                break;
            }
        } else {
            $implode[] = " (capp.document_status IS NULL OR capp.document_status != 'duplicate') ";
        }


        if(!empty($implode)){
            $sql .= " AND " . implode(" AND ", $implode);
        }

        $sql .= " GROUP BY capp.id";

        $implode_having =array();
        
        if (!empty($filter_data['filter_discrepency_status'])) 
        {   
           if($filter_data['filter_discrepency_status'] == 'yes') {
                $implode_having[] = " discrepency_id IS NOT NULL ";
            }else if($filter_data['filter_discrepency_status'] == 'no'){
                $implode_having[] = " discrepency_id IS NULL ";
            } 
        }

        if(!empty($implode_having)){
            $sql .= " HAVING " . implode(" AND ", $implode_having);
        }

        $query = $this->db->query($sql);
        return (int)$query->num_rows;
    }

    /** kusum Joshi
    * get Credit Application data submitted by customer
    **/
    public function getCreditApplicationData($credit_application_id = ''){
        if(!empty($credit_application_id)){
            $sql = "SELECT 
                            capp.customer_id,
                            capp.first_name,
                            capp.middle_name,
                            capp.last_name,
                            capp.email,
                            capp.phone_no,
                            capp.draft,
                            capp.education,
                            capp.dob,
                            capp.gender,
                            capp.pan_no,
                            capp.version,
                            capp.father_name,
                            capp.mother_name,
                            capp.marital_status,
                            capp.aadhaar_no,
                            capp.current_address,
                            capp.current_city,
                            capp.current_state,
                            capp.current_pincode,
                            capp.current_landline_phone_no,
                            capp.current_resident_premises,
                            capp.residing_date,
                            capp.residing_date,
                            capp.permanent_address,
                            capp.permanent_city,
                            capp.permanent_state,
                            capp.permanent_pincode,
                            capp.permanent_landline_phone_no,
                            capp.permanent_resident_premises,
                            capp.permanent_residing_date,
                            capp.annual_turnover,
                            capp.business_vintage,
                            capp.company_name,
                            capp.business_pan_no,
                            capp.business_since,
                            capp.shop_establishment_number,
                            capp.business_address,
                            capp.business_city,
                            capp.business_state,
                            capp.business_pincode,
                            capp.contact_person_phone_no,
                            capp.business_premises,
                            capp.occupied_since,
                            capp.trading_name,
                            capp.business_entity_type,
                            capp.partners,
                            capp.nature_of_business,
                            capp.business_segment,
                            capp.business_premises,
                            capp.business_vintage,
                            capp.months_in_current_business,
                            capp.gst,                           
                            capp.source,
                            capp.device_id,
                            capp.os,
                            capp.browser,
                            capp.ip,
                            ca.date_added as csv_date_timing,
                            capp.created_date as csv_date_register_with_anchor
                    FROM ". DB_PREFIX ."credit_application capp 
                    LEFT JOIN ". DB_PREFIX ."customer as ca ON ca.customer_id = capp.customer_id
                    WHERE capp.id= '".(int)$credit_application_id."' ";

            $credit_application_data = $this->db->query($sql)->rows;

            if(!empty($credit_application_data)){

                foreach($credit_application_data as $data){

                $result['loanDetails']['marketplaceProductName'] = "Working Capital Loan for MyWholesaleBox";
                $result['loanDetails']['marketplaceProductId'] = 65;
                $result['loanDetails']['loanAmount'] = "200000";
                $result['loanDetails']['loanTenor'] = "30";

                   $result['date'] = date("Y-m-d");
                   $result['customerEmailVerified'] ="false";
                   $result['customerMobileVerified'] ="true";

                   $result['customer']['customer_id'] = $data['customer_id'];
                   $result['customer']['firstName'] = $data['first_name'];
                   $result['customer']['middleName'] = $data['middle_name'];
                   $result['customer']['lastName'] = $data['last_name'];
                   $result['customer']['emailAddress'] = $data['email'];
                   $result['customer']['telephoneNumber'] = $data['phone_no'];
                   $result['customer']['education'] = $data['education'];
                   $result['customer']['dob'] = $data['dob'];
                   $result['customer']['draft'] = $data['draft'];
                   $result['customer']['gender'] = $data['gender'];
                   $result['customer']['panNumber'] = $data['pan_no'];
                   $result['customer']['version'] = $data['version'];

                   $result['customer']['source'] = $data['source'];
                   $result['customer']['device_id'] = $data['device_id'];
                   $result['customer']['os'] = $data['os'];
                   $result['customer']['browser'] = $data['browser'];
                   $result['customer']['ip'] = $data['ip'];
                   $result['customer']['csv_date_timing'] = $data['csv_date_timing'] ?? '';
                   $result['customer']['csv_date_register_with_anchor'] = $data['csv_date_register_with_anchor'];

                   $result['customer']['fatherOrHusbandsFirstName'] =$data['father_name']; 
                   $result['customer']['fatherOrHusbandsMiddleName'] = "";
                   $result['customer']['fatherOrHusbandsLastName'] = "";

                   $result['customer']['firstNameOfMother'] = $data['mother_name']; ;
                   $result['customer']['middleNameOfMother'] = "";
                   $result['customer']['lastNameOfMother'] = "";

                     $result['customer']['maritalStatus'] = $data['marital_status']; //Single,Married,Divorced,Widowed
                  
                   $result['customer']['citizenship'] = "Indian";
                   $result['customer']['residentialStatus'] = "Both";
                   // $result['customer']['aadhaarCardNumber'] = $data['aadhaar_no'];
                   
                   $aadhar_govt_id['govtIdNumber'] =$data['aadhaar_no'];
                   $aadhar_govt_id['govtIdType'] ='AADHAAR';
                   $aadhar_govt_id['govtIdExpiryDate'] ='';
                   $result['customer']['govtId'][]=$aadhar_govt_id;

                   $result['customer']['residentialAddress']['line1']=$data['current_address'];
                    $result['customer']['residentialAddress']['line2']="";
                    $result['customer']['residentialAddress']['line3']="";
                   $result['customer']['residentialAddress']['city']=$data['current_city'];
                   $result['customer']['residentialAddress']['state']=$data['current_state'];
                   $result['customer']['residentialAddress']['district']="";
                   $result['customer']['residentialAddress']['postcode']=$data['current_pincode'];
                   $result['customer']['residentialAddress']['country']="India";
                   $result['customer']['residentialAddress']['landlinePhoneNumber']=$data['current_landline_phone_no'];
                   $result['customer']['residentialAddress']['typeOfPremise']=$data['current_resident_premises'];

                    $result['customer']['residentialAddress']['dateOccupied']=$data['residing_date'];

                   if(!empty($data['residing_date'])){
                    $explode_residing_date = explode('-',$data['residing_date']);
                    $result['customer']['residentialAddress']['dateOccupiedYear'] = $explode_residing_date[0];
                    $result['customer']['residentialAddress']['dateOccupiedMonth'] = isset($explode_residing_date[1]) ? $explode_residing_date[1] : '';
                    }

                   $result['customer']['permanentAddress']['line1']=$data['permanent_address'];
                    $result['customer']['permanentAddress']['line2']="";
                    $result['customer']['permanentAddress']['line3']="";
                   $result['customer']['permanentAddress']['city']=$data['permanent_city'];
                   $result['customer']['permanentAddress']['state']=$data['permanent_state'];
                   $result['customer']['permanentAddress']['district']="";
                   $result['customer']['permanentAddress']['postcode']=$data['permanent_pincode'];
                   $result['customer']['permanentAddress']['country']="India";
                   $result['customer']['permanentAddress']['landlinePhoneNumber']=$data['permanent_landline_phone_no'];
                   $result['customer']['permanentAddress']['typeOfPremise']=$data['permanent_resident_premises'];

                   $result['customer']['permanentAddress']['dateOccupied']=$data['permanent_residing_date'];

                   if(!empty($data['permanent_residing_date'])){
                    $explode_permanent_date = explode('-',$data['permanent_residing_date']);
                    $result['customer']['permanentAddress']['dateOccupiedYear'] = $explode_permanent_date[0];
                    $result['customer']['permanentAddress']['dateOccupiedMonth'] = isset($explode_permanent_date[1]) ? $explode_permanent_date[1] : '';
                    }

                   $result['businessDetails']['annual_turnover']=$data['annual_turnover'];   
                   $result['businessDetails']['business_vintage']=$data['business_vintage'];  
                   $result['businessDetails']['companyName']=$data['company_name'];
                   $result['businessDetails']['businessPan']=$data['business_pan_no'];
                   $result['businessDetails']['dateOfJoining'] = $data['business_since'];
                   $result['businessDetails']['dateOfIncorporation'] = '';
                   if(!empty($data['business_since'])) {
                        $result['businessDetails']['dateOfIncorporation']=date("Y-m-d", strtotime($data['business_since']));
                   }
                   $result['businessDetails']['shopEstablishmentNumber']=$data['shop_establishment_number'];
                   $result['businessDetails']['businessAddress']['line1']=$data['business_address'];
                   $result['businessDetails']['businessAddress']['line2']="";
                   $result['businessDetails']['businessAddress']['line3']="";
                   $result['businessDetails']['businessAddress']['city']=$data['business_city'];
                   $result['businessDetails']['businessAddress']['state']=$data['business_state'];
                   $result['businessDetails']['businessAddress']['district']="";
                   $result['businessDetails']['businessAddress']['country']="India";
                   $result['businessDetails']['businessAddress']['postcode']=$data['business_pincode'];
                   $result['businessDetails']['businessAddress']['landlinePhoneNumber'] = $data['contact_person_phone_no'];
                   $result['businessDetails']['businessAddress']['typeOfPremise'] = $data['business_premises']; 
                   $result['businessDetails']['businessAddress']['dateOccupied'] = $data['occupied_since'];
                   $result['businessDetails']['tradingName']=$data['trading_name'];
                   $result['businessDetails']['typeOfBusinessEntity']=ucfirst($data['business_entity_type']);
                   $result['businessDetails']['noOfDirectorsOrPartners']=$data['partners'];
                   $result['businessDetails']['natureOfBusiness']=$data['nature_of_business'];
                   $result['businessDetails']['businessSegment'] = $data['business_segment'];
                   $result['businessDetails']['businessPremises'] = $data['business_premises'];
                   $result['businessDetails']['businessVintage'] = $data['business_vintage'];
                   $result['businessDetails']['monthsInCurrentBusiness'] = $data['months_in_current_business'];
                   $result['businessDetails']['gstRegistered'] = !empty($data['gst'])?"true":"false";
                   $result['businessDetails']['gstNumber'] = $data['gst'];

                    $get_length = strlen($data['customer_id']);
                    $add_extra_zero = (10 - $get_length);
                    $prefix_zero = "";
                    
                    if($add_extra_zero > 0){
                        
                        for($i=0; $i < $add_extra_zero; $i++){
                            $prefix_zero .= "0";     
                        }
                    }
                    
                   $result['marketplaceSpecificSection']['marketplaceCustomerId'] = $prefix_zero.$data['customer_id'];
                   $result['marketplaceSpecificSection']['marketplaceCustomerIdFormalName'] = "Customer Id";
                   $result['customer']['customer_id'] = $data['customer_id'];
                }
                return $result;
            }else{
                return array();
            }
        }
        else{
            return array();
        }
    }


    /**
    * Kusum Joshi
    * Maintain logs for epaylater application submission
    **/
    public function saveEpaylaterApplicationShareLog($credit_application_id,$response = array(), $request = '',$share_type = ''){
        if(!empty($response) && !empty($request) && !empty($share_type)){
            $login_user_id = $this->user->getId();
            $json_response = json_encode($response);
        if(!empty($response['applicationId'])){
            $credit_share_sql = "INSERT INTO " . DB_PREFIX . "credit_application_share_log SET credit_application_id = '" . (int)$credit_application_id . "', 
            request = '". $this->db->escape($request) ."',
            response = '". $this->db->escape($json_response) ."',
            user_id = '". $this->db->escape($login_user_id) ."',
            share_type = '". $this->db->escape($share_type) ."',
            requested_through = 'API', success_flag = '1' ,created = NOW()";
            $this->db->query($credit_share_sql);

        } else{
            $credit_share_sql = "INSERT INTO " . DB_PREFIX . "credit_application_share_log SET credit_application_id = '" . (int)$credit_application_id . "', 
            request = '". $this->db->escape($request) ."',
            response = '". $this->db->escape($json_response) ."',
            user_id = '". $this->db->escape($login_user_id) ."',
            share_type = '". $this->db->escape($share_type) ."',
            requested_through = 'API', success_flag = '0' ,created = NOW()";
            // print_r($credit_share_sql);die;
            $this->db->query($credit_share_sql);
        }

        }

        return TRUE;
    }

    /**
    * Kusum Joshi
    * on Success of epaylater application submission, applicationId and applicantId saved in database
    **/
    public function saveEpaylaterApplicationSuccess($response = array(), $credit_application_id){

        $credit_partner_id = $this->getCreditpartnerId($partner_name = "ePayLater");

        if(!empty($response)){
            $epay_application_id = $response['applicationId'];
            $epay_applicant_id = $response['applicantId'];
            $epay_redirect_url = isset($response['redirectUrl']) ? $response['redirectUrl'] : '';
            $login_user_id = $this->user->getId();

        $credit_share_sql = "INSERT INTO " . DB_PREFIX . "credit_application_share SET credit_application_id = '" . (int)$credit_application_id . "', credit_response_application_id = '". $this->db->escape($epay_application_id) ."', 
            credit_response_applicant_id = '". $this->db->escape($epay_applicant_id) ."',
            credit_response_redirect_url = '". $this->db->escape($epay_redirect_url) ."',
            credit_partner_id = '". $this->db->escape($credit_partner_id) ."', 
            user_id = '". $this->db->escape($login_user_id) ."',
            requested_through = 'API', created = NOW()";
            $this->db->query($credit_share_sql);
        }

        return TRUE;
    }

    // kyc Epaylater

    /**
    * Kusum Joshi
    * get All kyc documents submitted by user
    **/
    public function getKycDocuments($credit_application_id){
        
        if(!empty($credit_application_id)){
            $sql = "SELECT *
                FROM ". DB_PREFIX ."credit_application_document doc 
                 WHERE credit_application_id=".$credit_application_id;
            $credit_application_doc_data = $this->db->query($sql)->rows;

            if(!empty($credit_application_doc_data)){
                $doc_list = array();
                foreach($credit_application_doc_data as $data){

                    switch($data['name']){
                        case 'pancard': $doc_list['personalPan'] =  $data['file_path'];
                        break;
                        case in_array($data['name'],array('aadhaar_card','voter_id','driving_license','passport')): $doc_list['residenceAddressProof'] =  $data['file_path'];
                        break;

                        case 'business_pan_no':$doc_list['businessPan'] =  $data['file_path'];
                        break;

                        case 'vat_return':$doc_list['vatReturn'] =  $data['file_path']; break;

                        case in_array($data['name'],array('electricity_bill','phone_landline_bill','rental_agreement')):$doc_list['businessAddressProof'] =  $data['file_path']; break;

                        case 'business_entity_address_proof':$doc_list['businessEntityAddressProof'] =  $data['file_path']; break;

                        case 'certificate_of_registration':$doc_list['certificateOfRegistration'] =  $data['file_path']; break;

                        default: break;
                    }
                    // $doc_list[$data['name']] =  $data['file_path'];
                    
                }
                return $doc_list;
            }
        }

        return array();
    }

    

    /**
    * Kusum Joshi
    * get Credit Partner Id
    **/
    public function getCreditpartnerId($partner_name = ''){

        if(!empty($partner_name)){
             $sql = "SELECT credit_partner_id
                FROM ". DB_PREFIX ."credit_partners WHERE name = '" .$partner_name. "'";
            return $this->db->query($sql)->row['credit_partner_id'];
        }
        return '';
    }

    /**
    * Kusum Joshi
    * get Credit partner Application Id
    **/
    public function getCreditResponseApplicationId($credit_application_id){

        $credit_partner_id = $this->getCreditpartnerId($partner_name = "ePayLater");
            $sql = "SELECT credit_response_application_id, credit_response_applicant_id
                FROM ". DB_PREFIX ."credit_application_share WHERE credit_application_id = '" . (int)$credit_application_id . "' AND credit_partner_id = '" . (int)$credit_partner_id. "'";
            return $this->db->query($sql)->row;
    }

    /**
    * updates kyc flag in oc_credit_application_share database
    **/
    public function updateKycFlag($credit_application_id){

        $credit_partner_id = $this->getCreditpartnerId($partner_name = "ePayLater");

         if(!empty($credit_application_id) && !empty($credit_partner_id)){
            $sql = "UPDATE " . DB_PREFIX . "credit_application_share SET kyc_done = 1 WHERE credit_application_id = '" . (int)$credit_application_id . "' AND credit_partner_id = '" . (int)$credit_partner_id. "'";
            $query = $this->db->query($sql);

        }

        return TRUE;
    }

    

    public function getCreditApplicationLog($credit_application_id){

            $sql = "SELECT id, type, user_id , step, action, date 
                FROM ". DB_PREFIX ."credit_application_action_log WHERE credit_application_id = '" . (int)$credit_application_id . "' ORDER BY date DESC";
            return $this->db->query($sql)->rows;
    }   

    public function getCreditApplicationGroupbyTypeLog($credit_application_id){

            $sql = "SELECT type, group_concat(DISTINCT user_id) as user_id
                FROM ". DB_PREFIX ."credit_application_action_log WHERE credit_application_id = '" . (int)$credit_application_id . "' GROUP BY type";
            return $this->db->query($sql)->rows;
    }

    public function getCrmUserNameFromCrmUserIds($crm_user_ids = ''){

        $sql = "SELECT crm_user_id, name
                FROM ". DB_PREFIX ."sales_staff WHERE crm_user_id In (" . $crm_user_ids . ")";
            return $this->db->query($sql)->rows;
    }

    public function updateePayLaterOTPFlags($credit_application_id,$type,$checked){

         $credit_partner_id = $this->getCreditpartnerId($partner_name = "ePayLater");
         if($type == "kyc"){
             $sql = "UPDATE " . DB_PREFIX . "credit_application_share SET epaylater_kyc_otp_flag = ".$checked." WHERE credit_application_id = '" . (int)$credit_application_id . "' AND credit_partner_id = '" . (int)$credit_partner_id. "'";
            $query = $this->db->query($sql);
        }else if($type == "onboard"){
             $sql = "UPDATE " . DB_PREFIX . "credit_application_share SET epaylater_otp_flag = ".$checked." WHERE credit_application_id = '" . (int)$credit_application_id . "' AND credit_partner_id = '" . (int)$credit_partner_id. "'";
            $query = $this->db->query($sql);
        }else{
            $query = true;
        }

        return $query;
    }

   public function updateDocumentStatus($credit_application_id,$document_status){
             $sql = "UPDATE " . DB_PREFIX . "credit_application SET document_status = '".$this->db->escape($document_status)."' WHERE id = '" . (int)$credit_application_id . "'";
             $query = $this->db->query($sql);
        return $query;
    }

    public function updatePaymentCycle($credit_application_id,$payment_cycle){
             $sql = "UPDATE " . DB_PREFIX . "credit_application SET payment_cycle = '".$this->db->escape($payment_cycle)."' WHERE id = '" . (int)$credit_application_id . "'";
             $query = $this->db->query($sql);
        return $query;
    }

    public function addStatusRemarks($data){
            if(!empty($data['reason']))
              {
               $data['reason'] = implode(',', $data['reason']);
              }
            else{ $data['reason'] = ''; }

            if(empty($data['credit_limit']))
              {
                $data['credit_limit'] = '';
              }
            
             if(empty($data['credit_expire']))
              {
                $data['credit_expire'] = '';
              }

              if(empty($data['show_comment']))
              {
                $data['show_comment'] = 0;
              }

              if(empty($data['bank_account_last_digit']))
              {
                $data['bank_account_last_digit'] = '';
              }

              if(empty($data['ifsc_code']))
              {
                $data['ifsc_code'] = '';
              }
              if(!empty($data['payment_cycle']) && $data['document_status']=='approved_document_received')
              {
                $payment_cycle="payment_cycle = '".$this->db->escape($data['payment_cycle'])."',";
              }else{
                $payment_cycle='';
              }

             $save_sql = "INSERT INTO " . DB_PREFIX . "credit_application_status_remarks"
                     . " SET "
                     . "credit_application_id = '" . (int)$data['credit_application_id'] . "',
                       user_id = '" . (int)$this->user->getId() . "',
                       type = 'Comment',
                       status = '" . $this->db->escape($data['document_status']) . "',
                       reason = '" . $this->db->escape($data['reason']) . "',
                       show_comment = '" . (int)$data['show_comment'] . "',
                       remark = '" . $this->db->escape($data['remark']) . "',
                       credit_limit = '" . $this->db->escape($data['credit_limit']) . "',
                       bank_account_last_digit = '" . $this->db->escape($data['bank_account_last_digit']) . "',
                       ifsc_code = '" . $this->db->escape($data['ifsc_code']) . "',
                       credit_expire_date = '" . $this->db->escape($data['credit_expire']) . "',
                       followup_date = '" . $this->db->escape($data['followup_date']) . "',
                       $payment_cycle
                       date_added = NOW()";
            $this->db->query($save_sql);
            return true;
    }

    public function getCrmUserIdFromKhufiyaUserId(int $khufiya_user_id):int{
        
           $sql = "SELECT crm_user_id from oc_user 
          
          WHERE user_id = " . (int)$khufiya_user_id . "  
          LIMIT 1 ";
         $result =  $this->db->query($sql)->row;
         
         $crm_user_id = 0;
         if(!empty($result)){
             $crm_user_id = $result['crm_user_id'];
         }
         return $crm_user_id;

    }
    public function addCreditPreapproved($data)
    {
        
         if(empty($data['credit_limit']))
              {
                $data['credit_limit'] = '';
              }
            
             if(empty($data['credit_expire']))
              {
                $data['credit_expire'] = '';
              }
              
         $sql = "SELECT customer_id, first_name, last_name, pan_no, company_name
                FROM ". DB_PREFIX ."credit_application WHERE phone_no = '" . $data['telephone'] . "'";
         $customer_data = $this->db->query($sql)->row;
         
         $chk_sql = "SELECT id from ". DB_PREFIX ."customer_credit_preapproved where retailer_id='".(int)$customer_data['customer_id']."'"; 
         $chk_row = $this->db->query($chk_sql)->row;
         $chk_num = $this->db->query($chk_sql)->num_rows;

         if($chk_num == 0)
         {
             $save_sql = "INSERT INTO " . DB_PREFIX . "customer_credit_preapproved"
                     . " SET "
                     . "retailer_id = '" . (int)$customer_data['customer_id'] . "',
                       retailer_name = '" . $this->db->escape($customer_data['first_name']." ".$customer_data['last_name']) . "',
                       firm_name = '" . $this->db->escape($customer_data['company_name']) . "',
                       pan = '" . $this->db->escape($customer_data['pan_no']) . "',
                       approval_date = NOW(),
                       approved_limit = '" . $this->db->escape($data['credit_limit']) . "',
                       pre_approved_limit_expiry_date = '" . $this->db->escape($data['credit_expire']) . "',
                       approved_by = 'WHOLESALEBOX',
                       date_added = NOW(),
                       date_updated = NOW()";
         }
         else
         {

            $save_sql = "UPDATE " . DB_PREFIX . "customer_credit_preapproved"
                     . " SET "
                     . "retailer_id = '" . (int)$customer_data['customer_id'] . "',
                       retailer_name = '" . $this->db->escape($customer_data['first_name']." ".$customer_data['last_name']) . "',
                       firm_name = '" . $this->db->escape($customer_data['company_name']) . "',
                       pan = '" . $this->db->escape($customer_data['pan_no']) . "',
                       approval_date = NOW(),
                       approved_limit = '" . $this->db->escape($data['credit_limit']) . "',
                       pre_approved_limit_expiry_date = '" . $this->db->escape($data['credit_expire']) . "',
                       approved_by = 'WHOLESALEBOX',
                       date_updated = NOW() where id = '".(int)$chk_row['id']."'";

         }

         $this->db->query($save_sql);

    }
    
    public function getDocumentStatus($credit_application_id)
    {

        $sql = "SELECT cpsr.status, cpsr.type, cpsr.credit_limit, cpsr.user_id, cpsr.reason, cpsr.bank_account_last_digit, cpsr.ifsc_code, cpsr.remark, cpsr.date_added, cpsr.followup_date,cpsr.payment_cycle, u.username FROM ". DB_PREFIX ."credit_application_status_remarks cpsr
          LEFT JOIN " . DB_PREFIX . "user u
          ON (cpsr.user_id = u.user_id) 
          WHERE cpsr.credit_application_id = '" . (int)$credit_application_id . "'
          order by cpsr.date_added DESC";
         return $this->db->query($sql)->rows;

    }

    public function getLastNote($credit_application_id)
    {
             $sql = "SELECT cpn.remark, cpn.user_id, cpn.followup_date, u.username, cpn.date_added
                FROM ". DB_PREFIX ."credit_application_status_remarks cpn
                LEFT JOIN " . DB_PREFIX . "user u
                 ON (cpn.user_id = u.user_id) 
                 WHERE cpn.credit_application_id = '" . (int)$credit_application_id . "'
                 order by cpn.date_added DESC limit 1";
            return $this->db->query($sql)->row;
    }

    public function getCreditLimit($credit_application_id)
    {
             $sql = "SELECT credit_limit
                FROM ". DB_PREFIX ."credit_application_status_remarks
                 WHERE credit_application_id = '" . (int)$credit_application_id . "' 
                 AND type = 'Comment' 
                 AND credit_limit IS NOT NULL 
                 AND credit_limit <> '' 
                 ORDER BY id DESC";

              $query = $this->db->query($sql);

              if ($query->num_rows > 0) 
              {
                return $query->row['credit_limit'];
              }
              else
              {
                return 0; 
              }   
            
    }

    public function getFICustomeDetail($credit_application_id)
    {
        $sql = "SELECT ca.customer_id, ca.first_name, ca.last_name, ca.phone_no, ca.current_address, ca.current_city, ca.current_state, ca.current_pincode, casr.credit_limit as `limit`, casr.bank_account_last_digit as `account`
                FROM ". DB_PREFIX ."credit_application ca
                LEFT JOIN  ". DB_PREFIX ."credit_application_status_remarks casr
                ON ca.id = casr.credit_application_id 
                 WHERE ca.id = '" . (int)$credit_application_id . "' 
                 And casr.type = 'Comment'
                 And casr.credit_limit != ''
                 And casr.credit_limit IS NOT NULL
                 group by casr.credit_application_id
                 order by casr.id DESC ";

        $query = $this->db->query($sql);
       
        if ($query->num_rows > 0) 
         {
           return $query->row;
         }
        else
        {
          return false;
        } 

    }

    public function getFiAvailability($city, $pincode, $current_city, $current_pincode)
    {
        $sql = "SELECT id
                FROM ". DB_PREFIX ."fi_meemo 
                 WHERE city = '" . $this->db->escape($city) . "' or pincode = '". $this->db->escape($pincode) ."' or city = '" . $this->db->escape($current_city) . "' or pincode = '". $this->db->escape($current_pincode) ."'";
        $query = $this->db->query($sql); 
        return $query->num_rows; 

    }

    public function addNote($data){
            
             $save_sql = "INSERT INTO " . DB_PREFIX . "credit_application_status_remarks"
                     . " SET "
                     . "credit_application_id = '" . (int)$data['credit_application_id'] . "',
                       user_id = '" . (int)$this->user->getId() . "',
                       type = 'Note',
                       status = '',
                       remark = '" . $this->db->escape($data['note']) . "',
                       followup_date = '" . $this->db->escape($data['followup_date']) . "',
                       date_added = NOW()";
                          
            $this->db->query($save_sql);
            return true;
    }    
    
    public function getCreditApplicationNotificationLog($credit_application_id)
    {
          $sql = "SELECT cpsr.status, cpsr.type, cpsr.reason, cpsr.remark, cpsr.date_added, cpsr.user_id, u.username FROM ". DB_PREFIX ."credit_application_status_remarks cpsr
          LEFT JOIN " . DB_PREFIX . "user u
          ON (cpsr.user_id = u.user_id) 
          WHERE cpsr.credit_application_id = '" . (int)$credit_application_id . "'
          And type = 'Notification'
          order by cpsr.date_added DESC";
         return $this->db->query($sql)->rows;
    }

    public function getAllBanksFromSmsLog($customer_id)
    {
        if(isset($customer_id) ){

            $all_banks_keys = 'hdfc|fromsc|stanch|icici|sbi|bzatmsbp|axis|kotak|canbnk|corp|pnb|idbibk|indus|
								citibk|airbnk|bob|centbk|boiind|hsbc|union|dcbank|bndn|kvbank|psbank|iobchn|obcbnk|idfcbk|dhanbk|
								vidybk|jpcbnk|andbnk|digibk|dbsbnk|synbnk|pkgbnk|vijbnk|kmcbnk|dena|yesbnk|albank|ktkbnk|ucobnk|kdccbk|
								dcubnk|cubank|kucb|pugbbk|tdcbbk|mzbank|VKEMPBNK|bhrdbk|kplbnk';


            $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);
            $sql = "SELECT DISTINCT (CASE 	WHEN sender_name LIKE '%hdfc%' THEN 'HDFC'
											WHEN sender_name LIKE '%fromsc%' THEN 'STANC' 
											WHEN sender_name LIKE '%stanch%' THEN 'STANC' 
											WHEN sender_name LIKE '%icici%' THEN 'ICICI' 
											WHEN sender_name LIKE '%sbi%' THEN 'SBI' 
											WHEN sender_name LIKE '%bzatmsbp%' THEN 'SBI' 
											WHEN sender_name LIKE '%axis%' THEN 'AXIS' 
											WHEN sender_name LIKE '%kotak%' THEN 'KOTAK' 
											WHEN sender_name LIKE '%canbnk%' THEN 'CANARA_BANK' 
											WHEN sender_name LIKE '%corp%' THEN 'Corporation Bank' 											
											WHEN sender_name LIKE '%pnb%' THEN 'PNB' 
											WHEN sender_name LIKE '%idbibk%' THEN 'IDBI' 
											WHEN sender_name LIKE '%indus%' THEN 'INDUSIND' 
											WHEN sender_name LIKE '%citibk%' THEN 'CITI BANK' 
											WHEN sender_name LIKE '%airbnk%' THEN 'AIRTEL PAYMENTS' 
											WHEN sender_name LIKE '%bob%' THEN 'BARODA BANK' 
											WHEN sender_name LIKE '%centbk%' THEN 'CENTRAL BANK' 
											WHEN sender_name LIKE '%boiind%' THEN 'BANK OF INDIA' 
											WHEN sender_name LIKE '%hsbc%' THEN 'HSBC' 
											WHEN sender_name LIKE '%union%' THEN 'UNION BANK' 
											WHEN sender_name LIKE '%dcbank%' THEN 'DC BANK' 
											WHEN sender_name LIKE '%bndn%' THEN 'BANDHAN' 
											WHEN sender_name LIKE '%kvbank%' THEN 'Karur Vysya Bank' 
											WHEN sender_name LIKE '%psbank%' THEN 'PS Bank' 
											WHEN sender_name LIKE '%iobchn%' THEN 'INDIAN OVERSEAS' 
											WHEN sender_name LIKE '%obcbnk%' THEN 'ORIENTAL BANK' 
											WHEN sender_name LIKE '%idfcbk%' THEN 'IDFC' 
											WHEN sender_name LIKE '%dhanbk%' THEN 'Dhanlaxmi Bank' 
											WHEN sender_name LIKE '%vidybk%' THEN 'Vidyanand Co-Op Bank' 
											WHEN sender_name LIKE '%jpcbnk%' THEN 'JPC Bank' 
											WHEN sender_name LIKE '%andbnk%' THEN 'Andhra Bank' 
											WHEN sender_name LIKE '%digibk%' THEN 'DIGI BANK' 
											WHEN sender_name LIKE '%dbsbnk%' THEN 'DIGI BANK' 
											WHEN sender_name LIKE '%synbnk%' THEN 'Syndicate Bank' 
											WHEN sender_name LIKE '%pkgbnk%' THEN 'PRAGATHI KRISHNA GRAMIN BANK' 
											WHEN sender_name LIKE '%vijbnk%' THEN 'VIJAYA BANK' 
											WHEN sender_name LIKE '%kmcbnk%' THEN 'Kokan Mercantile Co-op Bank Ltd' 
											WHEN sender_name LIKE '%dena%' THEN 'DENA' 
											WHEN sender_name LIKE '%yesbnk%' THEN 'YES BANK' 
											WHEN sender_name LIKE '%albank%' THEN 'ALLAHABAD BANK' 
											WHEN sender_name LIKE '%ktkbnk%' THEN 'Karnataka Bank' 
											WHEN sender_name LIKE '%ucobnk%' THEN 'UCO Bank' 
											WHEN sender_name LIKE '%kdccbk%' THEN 'District Cooperative Central Bank(KDCC)' 
											WHEN sender_name LIKE '%dcubnk%' THEN 'Digital Federal Credit Union (DCU)' 
											WHEN sender_name LIKE '%cubank%' THEN 'City Union Bank Ltd.' 
											WHEN sender_name LIKE '%kucb%' THEN 'Khamgaon Urban Cooperative Bank' 
											WHEN sender_name LIKE '%pugbbk%' THEN 'SBI Purvanchal Gramin Bank' 
											WHEN sender_name LIKE '%tdcbbk%' THEN 'Thrissur District Co Operative Bank' 
											WHEN sender_name LIKE '%mzbank%' THEN 'mzbank' 
											WHEN sender_name LIKE '%VKEMPBNK%' THEN 'VKEMPBNK' 
											WHEN sender_name LIKE '%bhrdbk%' THEN 'bhrdbk' 
											WHEN sender_name LIKE '%kplbnk%' THEN 'kplbnk' 											
									END) AS senderName
					FROM processed_sms 
					WHERE criteria_type = 'account'
					AND customer_id = '" . (int)$customer_id . "'  
					AND sender_name REGEXP '".$all_banks_keys."' ";

            $query = $aws_mysqli->query($sql);

            if ($query->num_rows < 1) {
                return array();
            } else {

                $all_bank_names = array();
                while($row = $query->fetch_assoc())
                {
                    $all_bank_names[] = $row;
                }

                return $all_bank_names;
            }

        } else {

            return array();
        }
    }

    public function allBanksNamesHavingStatement($customer_id)
    {
        if(isset($customer_id) ){

            $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);
            $sql = "SELECT DISTINCT (bank_name) AS senderName
					FROM account_statement 
					WHERE  customer_id = '" . (int)$customer_id . "'  ";
            $query = $aws_mysqli->query($sql);

            if ($query->num_rows < 1) {
                return array();
            } else {

                $all_bank_names = array();
                while($row = $query->fetch_assoc())
                {
                    $all_bank_names[] = $row;
                }

                return $all_bank_names;
            }

        } else {

            return array();
        }
    }

    public function getCustomerAccountFromBankStatement($customer_id, $bank_name)
    {

        if(isset($customer_id) && isset($bank_name)){

            $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);
            $sql = "SELECT distinct(account_no) as bank_account_no  FROM account_statement 
                WHERE customer_id = '" . (int)$customer_id . "' AND bank_name = '" . $this->db->escape($bank_name) . "' ";

            $sql .= " order by  DATE(txn_date) DESC ";

            $query = $aws_mysqli->query($sql);

            if ($query->num_rows > 0) {

                $short_sms_data = array();
                while($row = $query->fetch_assoc())
                {
                    if($row['bank_account_no'] == NULL)
                        $short_sms_data[] = "UnIdentified";
                    else
                        $short_sms_data[] = $row['bank_account_no'];
                }

                return $short_sms_data;

            } else {

                return array();
            }

        }  else {

            return array();
        }
    }

    public function getCompleteBankAccountStatement($customer_id, $bank_ac_no, $start=0, $limit=1500){


        if(isset($customer_id) && isset($bank_ac_no)){

            $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);

            if($bank_ac_no == "UnIdentified"){
                $sql = "SELECT * FROM account_statement 
                WHERE customer_id = '" . (int)$customer_id . "' AND account_no IS NULL ";

            } else {
                $sql = "SELECT * FROM account_statement 
                WHERE customer_id = '" . (int)$customer_id . "' AND account_no = '" . $this->db->escape($bank_ac_no) . "' ";
            }
            $sql .= " order by  DATE(txn_date) DESC ";

            $query = $aws_mysqli->query($sql);

            if ($query->num_rows > 0) {

                $smt_sms_data = array();
                $monthly_credit_data = array();

                while($row = $query->fetch_assoc())
                {
                    if($row['txn_type'] == "credit")
                    {
                        $get_month = date("F", strtotime($row['txn_date']));
                        $get_year = date("Y", strtotime($row['txn_date']));

                        $monthly_credit_data[$get_year.'-'.$get_month][] = $row['amount'];
                    }

                    $smt_sms_data['all_sms'][] = $row;
                }

                //add and count credit data
                if(!empty($monthly_credit_data)) {

                    foreach($monthly_credit_data as $credit_key => $credit_val){
                        $smt_sms_data['credit'][$credit_key] = array('tot_credit_sms'=>count($credit_val), 'tot_credit_amount'=>array_sum($credit_val));
                    }
                } else {
                    $smt_sms_data['credit'] = array();
                }

                return $smt_sms_data;

            } else {

                return array();
            }

        }  else {

            return array();
        }
    }

    public function getLastAndMonthlyAvailBalance($customer_id, $bank_ac_no, $bank_name){


        if(isset($customer_id) && isset($bank_ac_no) && (isset($bank_name) && $bank_ac_no != "UnIdentified") ){

            $smt_sms_data = array();

            $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);

            // Last Available Balance
            $sql = "SELECT * FROM last_available_balance 
                WHERE customer_id = '" . (int)$customer_id . "' 
                AND account_no = '" . $this->db->escape($bank_ac_no) . "' 
                AND bank_name = '" . $this->db->escape($bank_name) . "' LIMIT 1";
            $query = $aws_mysqli->query($sql);

            if ($query->num_rows > 0) {

                while($row = $query->fetch_assoc())
                {
                    $smt_sms_data['last_avail_bal'] = $row;
                }

            } else {

                $smt_sms_data['last_avail_bal'] = array();
            }

            // Monthly Available Balance
            $sql = "SELECT * FROM account_mab 
                WHERE customer_id = '" . (int)$customer_id . "' 
                AND account_no = '" . $this->db->escape($bank_ac_no) . "' 
                AND bank_name = '" . $this->db->escape($bank_name) . "' 
                ORDER BY YEAR(last_message_date) DESC, MONTH(last_message_date) DESC";
            $query = $aws_mysqli->query($sql);

            if ($query->num_rows > 0) {

                while($row = $query->fetch_assoc())
                {
                    $smt_sms_data['monthly_avail_bal'][] = $row;
                }

            } else {

                $smt_sms_data['monthly_avail_bal'] = array();
            }

            return $smt_sms_data;


        }  else {

            return array();
        }
    }

    public function getCustomerSmsLog($customer_id, $type, $start=0, $limit=1500, $bank_name = '')
    {

        //"SBI"=>"bzatmsbp",
        //"DIGI BANK"=>"dbsbnk",
        // "STANC"=>"stanch",
        $all_bank_names = array("HDFC"=>"hdfc",
            "STANC"=>"fromsc",
            "ICICI"=>"icici",
            "SBI"=>"sbi",
            "AXIS"=>"axis",
            "KOTAK"=>"kotak",
            "CANARA_BANK"=>"canbnk",
            "Corporation Bank"=>"corp",
            "PNB"=>"pnb",
            "IDBI"=>"idbibk",
            "INDUSIND"=>"indus",
            "CITI BANK"=>"citibk",
            "AIRTEL PAYMENTS"=>"airbnk",
            "BARODA BANK"=>"bob",
            "CENTRAL BANK"=>"centbk",
            "BANK OF INDIA"=>"boiind",
            "HSBC"=>"hsbc",
            "UNION BANK"=>"union",
            "DC BANK"=>"dcbank",
            "BANDHAN"=>"bndn",
            "Karur Vysya Bank"=>"kvbank",
            "PS Bank"=>"psbank",
            "INDIAN OVERSEAS"=>"iobchn",
            "ORIENTAL BANK"=>"obcbnk",
            "IDFC"=>"idfcbk",
            "Dhanlaxmi Bank"=>"dhanbk",
            "Vidyanand Co-Op Bank"=>"vidybk",
            "JPC Bank"=>"jpcbnk",
            "Andhra Bank"=>"andbnk",
            "DIGI BANK"=>"digibk",
            "Syndicate Bank"=>"synbnk",
            "PRAGATHI KRISHNA GRAMIN BANK"=>"pkgbnk",
            "VIJAYA BANK"=>"vijbnk",
            "Kokan Mercantile Co-op Bank Ltd"=>"kmcbnk",
            "DENA"=>"dena",
            "YES BANK"=>"yesbnk",
            "ALLAHABAD BANK"=>"albank",
            "Karnataka Bank"=>"ktkbnk",
            "UCO Bank"=>"ucobnk",
            "District Cooperative Central Bank(KDCC)"=>"kdccbk",
            "Digital Federal Credit Union (DCU)"=>"dcubnk",
            "City Union Bank Ltd."=>"cubank",
            "Khamgaon Urban Cooperative Bank"=>"kucb",
            "SBI Purvanchal Gramin Bank"=>"pugbbk",
            "Thrissur District Co Operative Bank"=>"tdcbbk",
            "mzbank"=>"mzbank",
            "VKEMPBNK"=>"VKEMPBNK",
            "bhrdbk"=>"bhrdbk",
            "kplbnk"=>"kplbnk"
        );

        if(isset($customer_id) && isset($type)){

            $aws_mysqli = mysqli_connect(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);
            $sql = "SELECT message, txn_type, sender_name, msg_original_date  FROM processed_sms 
                WHERE customer_id = '" . (int)$customer_id . "' ";

            if($type == "udaan" || $type == "paytm" || $type == "lazypay")
            {
                $sql .= " AND criteria_type = 'competitor_credit' AND txn_type = '" . $this->db->escape($type) . "' ";
            }
            else if($type == "mswipe"){

                $sql .= " AND criteria_type = 'pos' AND (sender_name like '%mswipe%' OR message like '%mswipe%') ";
            }
            else if($type == "account"){

                $sql .= " AND sender_name like '%".$all_bank_names[$bank_name]."%' ";
            }
            else
            {
                $sql .= " AND criteria_type = '" . $this->db->escape($type) . "' ";
            }

            $sql .= " order by  DATE(msg_original_date) DESC limit $start, $limit ";

            $query = $aws_mysqli->query($sql);

            if ($query->num_rows < 1) {
                return array();
            } else {

                $short_sms_data = array();
                while($row = $query->fetch_assoc())
                {
                    $short_sms_data[] = $row;
                }

                return $short_sms_data;
            }

        }  else {

            return array();
        }
    }

   public function checkCustomerTextMsgLog($customer_id, $type)
   {
        if(isset($customer_id) && isset($type)){

            if($type == "mswipe"){
                $type = "pos";
            }

            $sql = "SELECT *  FROM ". DB_PREFIX ."wsb_customer_to_sms_criteria_sync 
                    WHERE customer_id = '" . (int)$customer_id . "' 
                    AND criteria = '" . $this->db->escape($type) . "' LIMIT 1";

            return $this->db->query($sql)->num_rows;

        }  else {

            return 0;
        }
    }

    /**
	   * Public Method to get criteria wise text message logs for a given customer id
	   * @param: $customer_id Int
	   * @return $sms logs Array
	   * @author: Devendra, August 2019
	*/
    public function checkTextMsgLogCriteriaWiseByCustomerId($customer_id)
    {
        if (empty($customer_id)) return array();

        $sql = "SELECT id, criteria  FROM ". DB_PREFIX ."wsb_customer_to_sms_criteria_sync 
                WHERE customer_id = '" . (int)$customer_id . "' ";

        $qry = $this->db->query($sql);

        if ($qry->num_rows) {
            $result = array();
            foreach($qry->rows as $row) {
                $result[$row['criteria']] = $row['id'];
            }
            return $result;
        }

        return array();
     }

    public function saveCreditNotificationLogs(int $credit_application_id ,string  $message):bool{

             $credit_save_sql = "INSERT INTO " . DB_PREFIX . "credit_application_status_remarks"
                     . " SET "
                     . "credit_application_id = '" . (int)$credit_application_id . "',
                       user_id = '" . (int)$this->user->getId() . "',
                        remark = '". $this->db->escape($message) ."',
                        type = 'Notification',
                        status = '',
                        date_added = NOW()";
             
            $this->db->query($credit_save_sql);
            return true;
           
    }

    public function getCustomerBankDetail($customer_id)
    {
       if(isset($customer_id)){

            $sql = "SELECT bank_ac_holder_name, bank_ac_number, ifsc_code  FROM ". DB_PREFIX ."customer 
                WHERE customer_id = '" . (int)$customer_id . "' ";

            return $this->db->query($sql)->row;

        }  else {

            return array();
        }
    } 

    public function getGstVerification(int $credit_application_id)
    {
      $sql = "SELECT cpgv.gst_type, cpgv.gst_active, cpgv.user_id, cpgv.last_filling_date, cpgv.registration_date, cpgv.gst_verification_id, cpgv.date_added, u.username FROM ". DB_PREFIX ."credit_application_gst_verification cpgv
          LEFT JOIN " . DB_PREFIX . "user u
          ON (cpgv.user_id = u.user_id) 
          WHERE cpgv.credit_application_id = '" . (int)$credit_application_id . "'
          order by cpgv.gst_verification_id DESC";
         return $this->db->query($sql)->rows;
    }  

   public function getGstVerificationCheck(int $credit_application_id)
    {
        $sql = "SELECT gst_verification_id FROM ". DB_PREFIX ."credit_application_gst_verification
          WHERE credit_application_id = '" . (int)$credit_application_id . "'";
         return $this->db->query($sql)->num_rows;
    }

    public function saveGstVerification($data)
    {
      $save_sql = "INSERT INTO " . DB_PREFIX . "credit_application_gst_verification"
                     . " SET "
                     . "credit_application_id = '" . (int)$data['credit_application_id'] . "',
                       user_id = '" . (int)$this->user->getId() . "',
                       registration_date = '" . $this->db->escape($data['registration_date']) . "',
                       last_filling_date = '" . $this->db->escape($data['last_filling_date']) . "',
                       gst_type = '" . $this->db->escape($data['gst_type']) . "',
                       gst_active = '" . $this->db->escape($data['gst_active']) . "',
                       date_added = NOW()";
                          
            $this->db->query($save_sql);
            return true;
    }


    public function saveDocumentChecklist($data)
    {

       
        foreach ($data['credit_checklist_id'] as $credit_checklist_id) 
        {

             $sql = "SELECT id FROM " . DB_PREFIX . "credit_application_checklist WHERE credit_application_id = '" . (int)$data['credit_application_id'] . "' and credit_checklist_id = '".(int)$credit_checklist_id."'";

             $chk_sql = $this->db->query($sql);

             if($chk_sql->num_rows == 0)
             {

                 $save_sql = "INSERT INTO " . DB_PREFIX . "credit_application_checklist"
                . " SET "
                . "credit_application_id = '" . (int)$data['credit_application_id'] . "',
                       user_id = '" . (int)$this->user->getId() . "',
                       credit_checklist_id = '" . (int)$credit_checklist_id . "',
                       date_added = NOW(),
                        date_modified = NOW()";
                 $this->db->query($save_sql);

             }
             else
             {
                
                 $save_sql = "UPDATE " . DB_PREFIX . "credit_application_checklist"
                . " SET "
                . "credit_application_id = '" . (int)$data['credit_application_id'] . "',
                       user_id = '" . (int)$this->user->getId() . "',
                       credit_checklist_id = '" . (int)$credit_checklist_id . "',
                        date_modified = NOW() where id = '".(int)$chk_sql->row['id']."'";
                 $this->db->query($save_sql);

             }

        }
  
        return true;
    }
    public function getLeadDetailByCreditApplicationId($creditAppId){

        $sql = "SELECT oc.customer_id, oc.ws_access_token, capp.company_name, CONCAT(capp.first_name,' ',capp.middle_name,' ',capp.last_name) customer_name, capp.email FROM " . DB_PREFIX . "credit_application capp
				 INNER JOIN " . DB_PREFIX . "customer oc ON oc.customer_id = capp.customer_id 
				WHERE capp.id = '" . (int)$creditAppId . "'  LIMIT 1";
        $rs_data = $this->db->query($sql);

        if($rs_data->num_rows > 0)
        {
            $customerId = $rs_data->row['customer_id'];

            $alldata = array();
            $alldata['customer']['customer_id'] = $customerId;
            $alldata['customer']['ws_access_token'] = $rs_data->row['ws_access_token'];

            $creditAppCustomer = new Customer($this);
            $get_TL_data = $creditAppCustomer::getLeadTlInfoForCustomers($alldata);
            return array(
                "lead_data"=>$get_TL_data,
                "company_name"=>$rs_data->row['company_name'],
                "name"=>$rs_data->row['customer_name'],
                "email"=>$rs_data->row['email'],
                "customer_id" => $customerId
            );
        } else {
            return array();
        }
    }
    
    public function getCreditChecklist()
    {

      $sql = "SELECT credit_checklist_id, name, status FROM ". DB_PREFIX ."credit_checklist 
                WHERE status = 1";
      return $this->db->query($sql)->rows;
    }

    public function getDocumentChecklist($credit_application_id)
    {
       $sql = "SELECT credit_application_id, user_id, credit_checklist_id, date_added, date_modified FROM ". DB_PREFIX ."credit_application_checklist 
                WHERE credit_application_id = '". (int)$credit_application_id ."'";
       return $this->db->query($sql)->rows;
    }

   public function getCreditQuestions()
    {

      $sql = "SELECT credit_questionnaire_id, question, options, status FROM ". DB_PREFIX ."credit_questionnaire 
                WHERE status = 1";
      return $this->db->query($sql)->rows;
    }

    public function getQuestionAndAnswer($creditAppId)
    {

      $sql = "SELECT credit_application_id, user_id, credit_questionnaire_id, answer, date_added, date_modified FROM ". DB_PREFIX ."credit_questionnaire_answer 
                WHERE credit_application_id = '".(int)$creditAppId."'";
      return $this->db->query($sql)->rows;
    }

   public function saveQuestions($data)
   {
        
          foreach ($data['answer'] as $key => $answer) 
           {

             $sql = "SELECT id FROM " . DB_PREFIX . "credit_questionnaire_answer WHERE credit_application_id = '" . (int)$data['credit_application_id'] . "' and credit_questionnaire_id = '".(int)$key."'";
             $chk_sql = $this->db->query($sql);

             if($chk_sql->num_rows == 0)
            {
               if(is_array($answer)) { $answer = implode(",",$answer); }
               $save_sql = "INSERT INTO " . DB_PREFIX . "credit_questionnaire_answer"
                . " SET "
                . "credit_application_id = '" . (int)$data['credit_application_id'] . "',
                       user_id = '" . (int)$this->user->getId() . "',
                       credit_questionnaire_id = '" . (int)$key . "',
                       answer = '" . $this->db->escape($answer) . "',
                       date_added = NOW(),
                       date_modified = NOW()";
               $this->db->query($save_sql);
            }
            else
            {
                if(is_array($answer)) { $answer = implode(",",$answer); }
               $save_sql = "UPDATE " . DB_PREFIX . "credit_questionnaire_answer"
                . " SET "
                . "credit_application_id = '" . (int)$data['credit_application_id'] . "',
                       user_id = '" . (int)$this->user->getId() . "',
                       credit_questionnaire_id = '" . (int)$key . "',
                       answer = '" . $this->db->escape($answer) . "',
                       date_modified = NOW() where id = '".(int)$chk_sql->row['id']."'";
               $this->db->query($save_sql);
            }


       }
        return true;

   }

   public function get_process_sms_customers($filter_sms_data = array(), $getTotal = false){

       // Get all customers Ids from AWS DB to use in filter query
       $aws_sms_mysqli = new mysqli(RDS_SMSLOG_HOST, RDS_SMSLOG_USER, RDS_SMSLOG_PASSWORD, RDS_SMSLOG_DB);

       if($getTotal){
           $aws_sms_mysqli->query("SET SESSION group_concat_max_len = @@max_allowed_packet;");
       }

       $sql_sms = "SELECT GROUP_CONCAT(DISTINCT customer_id) as customer_ids FROM processed_sms wps WHERE 1=1 ";

       if($filter_sms_data['filter_sms_log_type'] == "udaan" || $filter_sms_data['filter_sms_log_type'] == "paytm" || $filter_sms_data['filter_sms_log_type'] == "lazypay")
       {
           $sql_sms .= " AND wps.criteria_type = 'competitor_credit' AND wps.txn_type = '" . $this->db->escape($filter_sms_data['filter_sms_log_type']) . "' ";
       }
       else if($filter_sms_data['filter_sms_log_type'] == "mswipe"){

           $sql_sms .= " AND wps.criteria_type = 'pos' AND wps.sender_name like '%mswipe%' ";
       }
       else
       {
           $sql_sms .= " AND wps.criteria_type = '" . $this->db->escape($filter_sms_data['filter_sms_log_type']) . "' ";
       }

       $sql_sms .= " order by DATE(msg_original_date) DESC ";

       if ($getTotal && (isset($filter_sms_data['start']) || isset($filter_sms_data['limit']))) {
           if ($filter_sms_data['start'] < 0) {
               $filter_sms_data['start'] = 0;
           }

           if ($filter_sms_data['limit'] < 1) {
               $filter_sms_data['limit'] = 20;
           }

           $sql_sms .= " LIMIT " . (int)$filter_sms_data['start'] . "," . (int)$filter_sms_data['limit'];
        }

        $query_process_customer = $aws_sms_mysqli->query($sql_sms);


        $get_all_process_customers = '';
        if ($query_process_customer->num_rows > 0) {
           $get_all_process_customers = $query_process_customer->fetch_assoc()['customer_ids'];
        }

        return $get_all_process_customers;


   }
   public function getCustomerIdUsingCreditApplicationId($id) {
            $sql = "SELECT customer_id FROM " . DB_PREFIX . "credit_application 
              WHERE id='" . (int)$id . "' LIMIT 1";
            $result = $this->db->query($sql);
              if ($result->num_rows) {
                return $result->row['customer_id'];
              }
                return 0;
    }

   public function checkCreditLead($customer_id)
    {
      $sql = "SELECT  id
                  FROM
                    ".DB_PREFIX."credit_application
                  WHERE
                    customer_id = '". (int)$customer_id."'";
        $qry = $this->db->query($sql);
        return $qry->num_rows;   
    }

      /**
	   * Public Method to get credit lead ids for given customer ids
	   * @param: $customer_ids string
	   * @return $credit lead ids Array
	   * @author: Devendra, August 2019
	*/
    public function checkCreditLeadByCustomerIds($customer_ids)
    {
        if (empty($customer_ids)) return array();

        $sql = "SELECT DISTINCT customer_id
                  FROM
                    ".DB_PREFIX."credit_application
                  WHERE
                    customer_id IN(". $customer_ids.")";
        $qry = $this->db->query($sql);
        $response = array();
        foreach($qry->rows as $row) {
            $response[(int)$row['customer_id']] = 1;
        }
        return $response;
    }

    public function addFiDocument($credit_application_id, $file_path)
    {
            $remarks_sql = "INSERT INTO " . DB_PREFIX ."credit_application_status_remarks set
                type = 'Note', 
                credit_application_id = '".(int)$credit_application_id."', 
                user_id = '".(int)$this->user->getId()."', 
                remark = 'FI Doucment uploaded by admin',
                fi_document = '".$file_path."',
                date_added = '".date("Y-m-d H:i:s")."' ";
            $this->db->query($remarks_sql);

            return $this->db->getLastId();
    }


    public function getCreditCrifJson($credit_application_id)
    {
       $sql = "SELECT file_path FROM " . DB_PREFIX . "credit_application_document 
              WHERE credit_application_id='" . (int)$credit_application_id . "' and name = 'credit_crif' and type='application_document' order by id desc LIMIT 1";
            $result = $this->db->query($sql);
              if ($result->num_rows) {
                return $result->row['file_path'];
              }
            return '';   
    }

    public function addCreditCrifJson($credit_application_id, $customer_id, $file_path)
    {
            $sql = "INSERT INTO " . DB_PREFIX ."credit_application_document set
                credit_application_id  = '".(int)$credit_application_id."', 
                customer_id  = '".(int)$customer_id."', 
                type  = 'application_document', 
                name = 'credit_crif', 
                user_id = '".(int)$this->user->getId()."',
                user_type = 'khufiya_vibhag',
                file_path = '".$file_path."'";

            $this->db->query($sql);

            return $this->db->getLastId();
    }

    public function getFiDocument($credit_application_id)
    {
           $sql = "SELECT fi_document FROM " . DB_PREFIX . "credit_application_status_remarks 
              WHERE credit_application_id='" . (int)$credit_application_id . "' and fi_document != '' and type='Note' order by id desc LIMIT 1";
            $result = $this->db->query($sql);
              if ($result->num_rows) {
                return $result->row['fi_document'];
              }
            return '';
    }
   
   public function getCustomerPancardDetail($credit_application_id)
   {
     $sql = "SELECT ca.first_name, ca.middle_name, ca.last_name, ca.pan_no, ca.customer_id, ca.dob, ca.phone_no, ca.email, cad.file_path FROM " . DB_PREFIX . "credit_application ca INNER JOIN " . DB_PREFIX . "credit_application_document cad ON ca.id=cad.credit_application_id
              WHERE ca.id='" . (int)$credit_application_id . "' AND cad.name = 'pancard' LIMIT 1";
     $result = $this->db->query($sql);
     return $result->row;
   }

   public function savePancardDetail($data)
   {
     $sql = "SELECT id FROM " . DB_PREFIX . "credit_application
              WHERE 
                id != '" . (int)$data['credit_application_id'] . "' and 
                (phone_no = '".$this->db->escape($data['mobile'])."' or email = '".$this->db->escape($data['email'])."') and 
                document_status != 'duplicate' LIMIT 1";
     $result = $this->db->query($sql);
     
      if ($result->num_rows == 0) 
      {
        $save_sql = "UPDATE " . DB_PREFIX . "credit_application
                SET first_name = '" . $this->db->escape($data['first_name']) . "',
                    middle_name = '" . $this->db->escape($data['middle_name']) . "',
                    last_name = '" . $this->db->escape($data['last_name']) . "',
                    pan_no = '" . $this->db->escape($data['pan_number']) . "',
                    dob = '" . $this->db->escape(date("Y-m-d", strtotime($data['dob']))) . "',
                    phone_no = '" . $this->db->escape($data['mobile']) . "',
                    email = '" . $this->db->escape($data['email']) . "',
                    last_modified = NOW() where id = '".(int)$data['credit_application_id']."'";
         $this->db->query($save_sql);
         return true;
      }
      else
      {
        return false;
      }
       
   }

   public function updateCrifScore($crif_score, $credit_application_id)
   {
     $save_sql = "UPDATE " . DB_PREFIX . "credit_application
                SET crif_score = '" . (int)$crif_score . "',
                    last_modified = NOW() where id = '".(int)$credit_application_id."'";
    $this->db->query($save_sql);

    return array('status' => 'Success',
                'crif_score' => $crif_score);
   }

   public function updateCrifScoreRemark($credit_application_id)
   {
     $remarks_sql = "INSERT INTO " . DB_PREFIX ."credit_application_status_remarks set
                type = 'Comment', 
                status = 'rejected',
                credit_application_id = '".(int)$credit_application_id."', 
                user_id = '".(int)$this->user->getId()."', 
                remark = 'CIBIL issue',
                date_added = '".date("Y-m-d H:i:s")."' ";
            $this->db->query($remarks_sql);
   }
   
   public function updateCrifScoreLog($credit_application_id, $request_data, $reponse_data)
   {
     $log_sql = "INSERT INTO " . DB_PREFIX ."crif_score_log set
                credit_application_id = '".(int)$credit_application_id."', 
                request_data = '".$this->db->escape($request_data)."', 
                reponse_data = '".$this->db->escape($reponse_data)."',
                 user_id = '".(int)$this->user->getId()."', 
                date = '".date("Y-m-d H:i:s")."' ";
            $this->db->query($log_sql);
   }

   public function updateDocumentType($id, $name)
   {
         $save_sql = "UPDATE " . DB_PREFIX . "credit_application_document
                SET name = '" . $this->db->escape($name) . "'
                   where id = ".(int)$id;
        $this->db->query($save_sql);
   }


   public function addDefaultDataInCreditApplication($customer_id)
    {
        $this->load->model('sale/customer');
        $customer_sql = "SELECT 
                firstname as first_name, 
                lastname as last_name, 
                email, 
                telephone as phone_no,
                gst_number,
                address_id 
                FROM ". DB_PREFIX ."customer 
                WHERE customer_id = '".(int)$customer_id."' LIMIT 1";

        $query = $this->db->query($customer_sql);
        $version_insert = '';
        if(!empty($version)){
           $version_insert = "version = '".$version."', ";
        }else{
           $version_insert = "version = '2', "; 
        }
        if($query->num_rows > 0){

            // get customer address
            $get_customer_address = $this->model_sale_customer->getAddress((int)$query->row['address_id']);
            $current_address_value='';
            if(!empty($get_customer_address['address_1'])){
                if(!empty($get_customer_address['address_2'])){
                    $current_address_value=$get_customer_address['address_1'].' '.$get_customer_address['address_1'];
                }else{
                    $current_address_value=$get_customer_address['address_1'];
                }

            }else if(!empty($get_customer_address['address_2'])){
                    $current_address_value=$get_customer_address['address_2'];
            }
             $credit_sql = "INSERT INTO " . DB_PREFIX ."credit_application set
                customer_id = '".(int)$customer_id."', 
                first_name = '".$this->db->escape($query->row['first_name'])."', 
                last_name = '".$this->db->escape($query->row['last_name'])."', 
                email = '".$this->db->escape($query->row['email'])."',
                phone_no = '".$this->db->escape($query->row['phone_no'])."',
                $version_insert
                gst_number = '".$this->db->escape($query->row['gst_number'])."',
                current_pincode = '".$this->db->escape($get_customer_address['postcode'])."',
                current_address = '".$this->db->escape($current_address_value)."',
                current_city = '".$this->db->escape($get_customer_address['city'])."',
                current_state = '".$this->db->escape($get_customer_address['zone'])."',
                draft = '0',
                source = 'Admin',
                last_modified = '".date("Y-m-d H:i:s")."',
                created_date = '".date("Y-m-d H:i:s")."' ";
            $this->db->query($credit_sql);
            $credit_application_id = $this->db->getLastId();
             
             $remarks_sql = "INSERT INTO " . DB_PREFIX ."credit_application_status_remarks set
                type = 'Note', 
                credit_application_id = '".(int)$credit_application_id."', 
                user_id = '".(int)$this->user->getId()."', 
                remark = 'New application added by admin',
                date_added = '".date("Y-m-d H:i:s")."' ";
            $this->db->query($remarks_sql);

            return $credit_application_id;

        }
        else
        {
            return $query->num_rows;
        }
    }
   



    public function addCreditApplicationDataInCustomer($credit_application_id, $credit_data)
    {   
        $search_sql = array();
        $data['password'] = 123456;
        $data['firstname'] = $credit_data['customer']['firstName'];
        $data['lastname'] = $credit_data['customer']['lastName'];
        $data['telephone'] = $credit_data['customer']['telephoneNumber'];
       $data['email'] = $credit_data['customer']['emailAddress'];
       $data['gst_number'] = $credit_data['businessDetails']['gstNumber'];

        if(!empty($data['telephone']))
        {
         $search_sql[] = "telephone = '" . $this->db->escape($data['telephone']) . "'";
        }

        if(!empty($data['email']))
        {
         $search_sql[] = "email = '" . $this->db->escape($data['email']) . "'";
        }

        if(!empty($data['gst_number']))
        {
         $search_sql[] = "gst_number = '" . $this->db->escape($data['gst_number']) . "'";
        }

        if(count($search_sql) > 0)
        {
          $search_sql = implode(" OR ", $search_sql);  
           $customer_sql = "SELECT  customer_id
                FROM ". DB_PREFIX ."customer 
                WHERE ".$search_sql."
                 LIMIT 1";

        $customer_query = $this->db->query($customer_sql);
        
        if($customer_query->num_rows > 0)
        {
          $customer_id = $customer_query->row['customer_id'];    
        }
        else
        {
           $this->load->model('crm/user', 'frontend');
           $customer_id = $this->frontend_model_crm_user->getCustomerId($data['telephone']);

          if($customer_id == 0)
          {

            $this->load->model('sale/customer');
            
            $request['pincode'] = $credit_data['customer']['permanentAddress']['postcode'];
            $data_json = json_encode($request);
            $api_url = "https://www.wholesalebox.in/index.php?route=restapi/lookup/pincode";
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array('Content-Type: application/json',
                    'Content-Length: ' . strlen($data_json))
            );
            curl_setopt($ch, CURLOPT_VERBOSE, 1);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            $result = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result);
            $result = json_decode($result, true );
          
          $data['address'][0]['firstname'] = $credit_data['customer']['firstName'];
          $data['address'][0]['lastname'] = $credit_data['customer']['lastName']; 
          $data['address'][0]['address_1'] = $credit_data['customer']['permanentAddress']['line1'];
          $data['address'][0]['address_2'] = $credit_data['customer']['permanentAddress']['line2'];
          $data['address'][0]['postcode'] = $credit_data['customer']['permanentAddress']['postcode'];
          $data['address'][0]['city'] = $credit_data['customer']['permanentAddress']['city'];
           $data['address'][0]['company'] = $credit_data['businessDetails']['companyName'];
           $data['address'][0]['country_id'] = isset($result[0]['country_id']) ? $result[0]['country_id']:99;;
           $data['address'][0]['zone_id'] = isset($result[0]['zone_id']) ? $result[0]['zone_id']:'';
           $data['address'][0]['default'] = 1;

           $customer_id = $this->model_sale_customer->addCustomer($data);
          } 
        }   


        $credit_sql = "UPDATE " . DB_PREFIX ."credit_application
              set customer_id = '".(int)$customer_id."' 
              where id = ".(int)$credit_application_id;
        $this->db->query($credit_sql);  

        return $customer_id;

     }
     else
     {
        return 0;
     }

    }

    public function countPreApprovedProcess($credit_application_id){
            $count_pre_approved_process = $this->db->query("SELECT
                                                              COUNT(credit_application_id) AS COUNT
                                                            FROM
                                                              `oc_credit_application_status_remarks`
                                                            WHERE
                                                              `status` IN(
                                                                'approved_but_agreement_pending',
                                                                'approved_document_received',
                                                                'approved_without_bank_statement'
                                                              ) AND credit_application_id = '".(int)$credit_application_id."'");
            return $count_pre_approved_process->row['COUNT'];
    }

        /**
     * Public method to make Credit Application Sticky To Agent against given customer ids
     * @param: array
     * @return array
     * @author: Manoj Singh, March 2019
     */
    public static function makeCreditApplicationStickyToAgent($customer_id){
        $results = array();

        $url = CRM_URL.'webapi/CreditApplication/makeCreditApplicationStickyToAgent';

        $input = array();

        $input['customer_id'] = $customer_id ?? '';

        $data_string = json_encode($input);

        // Set some options - we are passing in a useragent too here
        // Get cURL resources
        $ch = curl_init($url);
        // Set some options - we are passing in a useragent too here
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        // Send the request & save response to $resp
        $resp = curl_exec($ch);

        // Close request to clear up some resources
        curl_close($ch);
        $agent_data = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $resp);
        $res =json_decode($agent_data);
        return true;
    }
    
    public function saveCreditPreApprovalData(array $data )
    {
        array_shift($data); // remove first date value
        $dob = '';
        if(!empty($data[4])) {
            $dob = date('Y-m-d',strtotime($data[4]));   
        }
        $sql = "
                INSERT INTO ". DB_PREFIX ."customer_preonboarding
                    SET
                        anchor_name         = '".$this->db->escape($data[0])."',
                        anchor_id           = '".$this->db->escape($data[1])."',
                        retailer_id         = '".$this->db->escape($data[2])."',
                        customer_name       = '".$this->db->escape($data[3])."',
                        dob                 = '".$this->db->escape($dob)."',
                        pan_no              = '".$this->db->escape($data[5])."',
                        gender              = '".$this->db->escape($data[6])."',
                        mobile_number       = '".$this->db->escape($data[7])."',
                        residence_address   = '".$this->db->escape($data[8])."',
                        residence_pincode   = '".$this->db->escape($data[9])."',
                        email_id            = '".$this->db->escape($data[10])."',
                        firm_name           = '".$this->db->escape($data[11])."',
                        office_address_line1    = '".$this->db->escape($data[12])."',
                        office_address_line2    = '".$this->db->escape($data[13])."',
                        office_address_landmark = '".$this->db->escape($data[14])."',
                        office_city             = '".$this->db->escape($data[15])."',
                        office_state            = '".$this->db->escape($data[16])."',
                        office_pincode          = '".$this->db->escape($data[17])."',
                        annual_turnover     = '".$this->db->escape($data[18])."',
                        retailer_business_vintage = '".$this->db->escape($data[19])."',
                        date_of_first_order = '".$this->db->escape($data[20])."',
                        M1 = '".$this->db->escape($data[21])."',
                        M2 = '".$this->db->escape($data[22])."',
                        M3 = '".$this->db->escape($data[23])."',
                        M4 = '".$this->db->escape($data[24])."',
                        M5 = '".$this->db->escape($data[25])."',
                        M6 = '".$this->db->escape($data[26])."',
                        M7 = '".$this->db->escape($data[27])."',
                        M8 = '".$this->db->escape($data[28])."',
                        M9 = '".$this->db->escape($data[29])."',
                        M10 = '".$this->db->escape($data[30])."',
                        M11 = '".$this->db->escape($data[31])."',
                        M12 = '".$this->db->escape($data[32])."',
                        M13 = '".$this->db->escape($data[33])."',
                        date_added = NOW()
                   ";
        $this->db->query($sql);
    }

    public function isCustomerProcessedForRBL(int $customer_id): bool
    {
        $sql = "
            SELECT 
                id
            FROM
                ". DB_PREFIX ."customer_preonboarding
            WHERE
                retailer_id = '".(int) $customer_id."'
                AND
                status = 1
        ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            return true;
        }
        return false;
    }

    public function addCreditApplicationActionLog($data) {
        
        $sql = "INSERT INTO " . DB_PREFIX . "credit_application_action_log SET "
                . "credit_application_id = '" . $data['credit_application_id'] . "', "
                . "type = '" . $data['type']  . "', "
                . "user_id = '" . $data['user_id']  . "', "
                . "step = '" . $data['step'] . "', "
                . "action = '" . $data['action'] . "', "
                . "date = '" . date("Y-m-d H:i:s") . "'";
        $this->db->query($sql);

        return TRUE;
    }

    public function getCustomerCifCreationData(int $customer_id): array
    {
        $data = array();
        $sql = "
            SELECT 
                gender,title,first_name,middle_name,last_name,dob,mother_maiden_name,community,
                marital_status,gross_income,refer_pan_no,refer_address_type,refer_address_line1,
                refer_address_line2,refer_address_line3,refer_city,refer_state,refer_postcode,
                refer_phone,corporate_name,incorporation_date,entity_pan_no,annual_turnover,
                entity_gst_no,entity_address_type,entity_address_line1,entity_address_line2,
                entity_address_line3,entity_city,entity_state,entity_country,entity_postcode,
                entity_phone,entity_email_id,consent_details,request_ref_number,applied_on,
                tc_acceptance_flag,date_added
            FROM
                " . DB_PREFIX . "customer_credit_cifcreation 
            WHERE
                retailer_id = '".(int)$customer_id."'
            ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $data = $result->row;
        }
        return $data;
    }

    /**
     * Public method to get customer last successful delivered order data
     * @param: int $customer_id
     * @return array
     * @author: MSA, Sept 2019
     */
    public function isCustomerLastDeliveredOrderAddress(int $customer_id)
    {
        $data = array();
        $sql = "
               SELECT 
                    o.order_id,
                    o.shipping_company,
                    o.shipping_address_1,
                    o.shipping_address_2,
                    o.shipping_city,
                    o.shipping_zone,
                    o.shipping_postcode
               FROM ".DB_PREFIX."order o
               JOIN ".DB_PREFIX."suborder osub ON osub.order_id = o.order_id
               WHERE 
                    o.customer_id  = " . (int)$customer_id . "
                    AND
                    o.store_id IN (0,2,9)
                    AND
                    o.franchise_id = 0 
                    AND
                    osub.order_status_id IN(".implode(',',ORDER_STATUS_CLUSTERS['delivered']).")
                ORDER BY o.order_id DESC     
                LIMIT 1
            ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $data = $result->row;
        }
        return $data;
    }

    public function getCustomerApplicationRBLStatus(int $customer_id): string
    {
        $application_status = '';
        //first check for CIF Status
            $sql = "
                    SELECT 
                        1
                    FROM ".DB_PREFIX."credit_application
                    WHERE
                        customer_id     = '".(int)$customer_id."'
                        AND
                        document_status = 'activated_by_rbl'
                    ";
            $result = $this->db->query($sql);
            if($result->num_rows) {

                $application_status = 'activated_by_rbl';

            } else { 

                //check application for pre-approved, cif status or descrepency status
                $sql = "
                    SELECT 
                        cif_creation_status
                    FROM ".DB_PREFIX."customer_credit_preapproved
                    WHERE
                        retailer_id     = '".(int)$customer_id."'
                        AND
                        approved_by     = 'RBL' 
                    ";

                $result = $this->db->query($sql);    
                
                if($result->num_rows) {
                
                    $current_status = $result->row['cif_creation_status'];
                
                    if($current_status == 'Pending') {
                
                        $application_status = 'pre_approved_status';
                
                    }else if($current_status == 'DiscrepencyStatus') {
                        
                        $application_status = 'discrepency_status';

                    }else if($current_status == 'JourneyCompleted' || $current_status == 'CifCreated') {
                        
                        $application_status = 'cif_created';

                    }

                } else {

                    //if No pre-approval status then check for onBoarding data status
                    $sql = "
                         SELECT 
                            1
                        FROM ".DB_PREFIX."customer_preonboarding
                        WHERE
                            retailer_id     = '".(int)$customer_id."'   
                        ";
                    $result = $this->db->query($sql); 

                    if($result->num_rows) {

                        $application_status = 'on_boarding';
                    }
                }   
            }

        return $application_status;    
    }

    /**
     * Public method to get customer credit account status
     * @param: int $customer_id
     * @return int credit_status[1/0]
     * @author: MSA, Sept 2019
     */
    public function getCustomerApplicationStatus( int $customer_id )
    {
        $credit_status = 1;
        $sql = "
                SELECT 
                    credit_status
                FROM 
                    " . DB_PREFIX . "customer_credit 
                WHERE
                    customer_id = '" . (int)$customer_id  ."'
                    AND
                    type = 'RBL'
              ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $credit_status = $result->row['credit_status'];
        } 
        return $credit_status;
    }

    /**
     * Public method to get customer first successful delivered order date
     * @param: int $customer_id
     * @return array
     * @author: MSA, Oct 2019
     */
    public function getCustomerFirstDeliveredOrderDate(int $customer_id)
    {
        $date = "";
        $sql = "
               SELECT o.date_added
               FROM ".DB_PREFIX."order o
               JOIN ".DB_PREFIX."suborder osub ON osub.order_id = o.order_id
               WHERE 
                    o.customer_id  = " . (int)$customer_id . "
                    AND
                    o.store_id IN (0,2,9)
                    AND
                    o.franchise_id = 0 
                    AND
                    osub.order_status_id IN(".implode(',',ORDER_STATUS_CLUSTERS['delivered']).")
                ORDER BY o.order_id     
                LIMIT 1
            ";
        $result = $this->db->query($sql);
        if($result->num_rows) {
            $date = $result->row['date_added'];
        }
        return $date;
    }

} 