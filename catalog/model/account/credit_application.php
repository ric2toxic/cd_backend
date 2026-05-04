<?php
class ModelAccountCreditApplication extends Model {
    
    public function saveCredit($data) {
        
        $exits_credit_application = $this->isExitCustomerCreditApplicationId($data['customer_id']);
        $credit_application_changes = array();
        if($exits_credit_application['status'] == true && $exits_credit_application['draft'] > $data['draft']){
            $draft = $exits_credit_application['draft'];
        } else{
            $draft = $data['draft'];
        }

        if($exits_credit_application['status'] == true){
            unset($exits_credit_application['credit_application_data']['id']);
            unset($exits_credit_application['credit_application_data']['form_action']);
            $credit_application_changes = array_diff($exits_credit_application['credit_application_data'],$data);
        } else{
           $credit_application_changes = $data; 
        }
        if($data['draft'] == '1'){
            
            if($exits_credit_application['status'] == false){
                $sql = "INSERT INTO " . DB_PREFIX . "credit_application SET "
                        . "customer_id = " . $data['customer_id'] . ", " 
                        . "business_entity_type = '" . $this->db->escape($data['business_entity_type']) . "', "
                        . "first_name = '" . $this->db->escape(trim($data['first_name'])) . "', "
                        . "middle_name = '" . $this->db->escape(trim($data['middle_name'])) . "', "
                        . "last_name = '" . $this->db->escape(trim($data['last_name'])) . "', "
                        . "father_name = '" . $this->db->escape(trim($data['father_name'])) . "', "
                        . "mother_name = '" . $this->db->escape(trim($data['mother_name'])) . "', "
                        . "marital_status = '" . $this->db->escape($data['marital_status']) . "', "
                        . "pan_no = '" . $this->db->escape(trim($data['pan_no'])) . "', "
                        . "aadhaar_no = '" . (int)$data['aadhaar_no'] . "', "
                        . "dob = '" . date("Y-m-d", strtotime($data['dob'])) . "', " 
                        . "gender = '" . $this->db->escape($data['gender']) . "', "
                        . "education = '" . $this->db->escape($data['education']) . "', "
                        . "phone_no = '" . (int)$data['phone_no'] . "', "
                        . "email = '" . $this->db->escape($data['email']) . "', "
                        . "gst_number = '" . $this->db->escape($data['gst_number']) . "', "
                        . "current_address = '" . $this->db->escape(trim($data['current_address'])) . "', "
                        . "current_pincode = '" . $this->db->escape($data['current_pincode']) . "', "
                        . "current_city = '" . $this->db->escape(trim($data['current_city'])) . "', "
                        . "current_state = '" . $this->db->escape(trim($data['current_state'])) . "', "
                        . "current_landline_phone_no = '" . (int)$data['current_landline_phone_no'] . "', "
                        . "current_resident_premises = '" . $this->db->escape($data['current_resident_premises']) . "', "
                        . "residing_date = '" . $this->db->escape($data['residing_date']) . "', "
                        . "permanent_address = '" . $this->db->escape(trim($data['permanent_address'])) . "', "
                        . "permanent_pincode = '" . (int)$data['permanent_pincode'] . "', "
                        . "permanent_city = '" . $this->db->escape(trim($data['permanent_city'])) . "', "
                        . "permanent_state = '" . $this->db->escape(trim($data['permanent_state'])) . "', "
                        . "permanent_landline_phone_no = '" . (int)$data['permanent_landline_phone_no'] . "', "
                        . "permanent_resident_premises = '" . $this->db->escape($data['permanent_resident_premises']) . "', "
                        . "permanent_residing_date = '" . $this->db->escape($data['permanent_residing_date']) . "', "
                        . "draft = '" . (int) $draft . "', "
                        . "last_modified = '" . date("Y-m-d H:i:s") ."', "
                        . "created_date = '" . date("Y-m-d H:i:s") . "' ";
                
                $query = $this->db->query($sql);

                $credit_application_id = $this->db->getLastId();
                
                $data_log['action'] = 'ADD';
            
            
            } else{ 
                
                $sql = "UPDATE " . DB_PREFIX . "credit_application SET "
                        . "business_entity_type = '" . $this->db->escape($data['business_entity_type']) . "', "
                        . "first_name = '" . $this->db->escape(trim($data['first_name'])) . "', "
                        . "middle_name = '" . $this->db->escape(trim($data['middle_name'])) . "', "
                        . "last_name = '" . $this->db->escape(trim($data['last_name'])) . "', "
                        . "father_name = '" . $this->db->escape(trim($data['father_name'])) . "', "
                        . "mother_name = '" . $this->db->escape(trim($data['mother_name'])) . "', "
                        . "marital_status = '" . $this->db->escape($data['marital_status']) . "', "
                        . "pan_no = '" . $this->db->escape(trim($data['pan_no'])) . "', "
                        . "aadhaar_no = '" . (int)$data['aadhaar_no'] . "', "
                        . "dob = '" . date("Y-m-d", strtotime($data['dob'])) . "', " 
                        . "gender = '" . $this->db->escape($data['gender']) . "', "
                        . "education = '" . $this->db->escape(trim($data['education'])) . "', "
                        . "phone_no = '" . (int)$data['phone_no'] . "', "
                        . "email = '" . $this->db->escape($data['email']) . "', "
                        . "gst_number = '" . $this->db->escape($data['gst_number']) . "', "
                        . "current_address = '" . $this->db->escape(trim($data['current_address'])) . "', "
                        . "current_pincode = '" . $this->db->escape($data['current_pincode']) . "', "
                        . "current_city = '" . $this->db->escape(trim($data['current_city'])) . "', "
                        . "current_state = '" . $this->db->escape(trim($data['current_state'])) . "', "
                        . "current_landline_phone_no = '" . $data['current_landline_phone_no'] . "', "
                        . "current_resident_premises = '" . $this->db->escape($data['current_resident_premises']) . "', "
                        . "residing_date = '" . $this->db->escape($data['residing_date']) . "', "
                        . "permanent_address = '" . $this->db->escape(trim($data['permanent_address'])) . "', "
                        . "permanent_pincode = '" . (int)$data['permanent_pincode'] . "', "
                        . "permanent_city = '" . $this->db->escape(trim($data['permanent_city'])) . "', "
                        . "permanent_state = '" . $this->db->escape(trim($data['permanent_state'])) . "', "
                        . "permanent_landline_phone_no = '" . $data['permanent_landline_phone_no'] . "', "
                        . "permanent_resident_premises = '" . $this->db->escape($data['permanent_resident_premises']) . "', "
                        . "permanent_residing_date = '" . $this->db->escape($data['permanent_residing_date']) . "', "
                        . "draft = '" . (int) $draft . "', "
                        . "last_modified = '" . date("Y-m-d H:i:s") ."', "
                        . "version = '1' "
                        . "WHERE id = " . $exits_credit_application['credit_application_id'];

                $query = $this->db->query($sql);
                
                $credit_application_id = $exits_credit_application['credit_application_id'];
                $data_log['action'] = 'EDIT';
                
            }
            
                if(!empty($credit_application_changes)){
                $data_log['credit_application_id'] = $credit_application_id;
                $data_log['step'] = $data['draft'];
                if($data['crm_user_id'] != '' && $data['crm_user_id'] != '0'){
                    $data_log['user_id'] = $data['crm_user_id'];
                    $data_log['type'] = 'CRM_USER';
                }else if($data['khufiya_user_id'] != '' && $data['khufiya_user_id'] != '0'){
                    $data_log['user_id'] = $data['khufiya_user_id'];
                    $data_log['type'] = 'KHUFIYA_USER';
                } else{
                    $data_log['user_id'] = $data['customer_id'];
                    $data_log['type'] = 'CUSTOMER';
                }
                $this->addCreditApplicationActionLog($data_log);
                }

            if($query){
                return true;
            }else{
                return false;
            }
            
        }
        else if($data['draft'] == '2'){
            
            $sql = "UPDATE " . DB_PREFIX . "credit_application SET "
                    . "company_name = '" . $this->db->escape(trim($data['company_name'])) . "', "
                    . "entity_name = '" . $this->db->escape(trim($data['entity_name'])) . "', "
                    . "partners = '" . $this->db->escape(trim($data['partners'])) . "', "
                    . "shop_establishment_number = '" . $this->db->escape(trim($data['shop_establishment_number'])) . "', "
                    . "business_pan_no = '" . $this->db->escape(trim($data['business_pan_no'])) . "', "
                    . "gst = '" . $this->db->escape(trim($data['gst'])) . "', "
                    . "trading_name = '" . $this->db->escape(trim($data['trading_name'])) . "', "
                    . "nature_of_business = '" . $this->db->escape($data['nature_of_business']) . "', "
                    . "business_ownership = '" . $this->db->escape($data['business_ownership']) . "', "
                    . "business_segment = '" . $this->db->escape($data['business_segment']) . "', "
                    . "business_vintage = '" . $this->db->escape(trim($data['business_vintage'])) . "', "
                    . "months_in_current_business = '" . $this->db->escape(trim($data['months_in_current_business'])) . "', "
                    . "business_premises = '" . $this->db->escape($data['business_premises']) . "', "
                    . "occupied_since = '" . $this->db->escape($data['occupied_since']) . "', "
                    . "business_address = '" . $this->db->escape(trim($data['business_address'])) . "', "
                    . "business_pincode = '" . (int)$data['business_pincode'] . "', "
                    . "business_city = '" . $this->db->escape(trim($data['business_city'])) . "', "
                    . "business_state = '" . $this->db->escape(trim($data['business_state'])) . "', "
                    . "reg_office_address = '" . $this->db->escape(trim($data['reg_office_address'])) . "', "
                    . "reg_office_pincode = '" . $this->db->escape($data['reg_office_pincode']) . "', "
                    . "reg_office_city = '" . $this->db->escape(trim($data['reg_office_city'])) . "', "
                    . "reg_office_state = '" . $this->db->escape(trim($data['reg_office_state'])) . "', "
                    . "other_business_entity_detail = '" . $this->db->escape(trim($data['other_business_entity_detail'])) . "', "
                    . "business_since = '" . $this->db->escape(trim($data['business_since'])) . "', "
                    . "annual_turnover = '" . $this->db->escape($data['annual_turnover']) . "', "
                    . "litigation = '" . $this->db->escape(trim($data['litigation'])) . "', "
                    . "is_contact_person_same = '" . (int)$data['is_contact_person_same'] . "', "
                    . "contact_person_first_name = '" . $this->db->escape(trim($data['contact_person_first_name'])) . "', "
                    . "contact_person_middle_name = '" . $this->db->escape(trim($data['contact_person_middle_name'])) . "', "
                    . "contact_person_last_name = '" . $this->db->escape(trim($data['contact_person_last_name'])) . "', "
                    . "contact_person_designation = '" . $this->db->escape(trim($data['contact_person_designation'])) . "', "
                    . "contact_person_relation_with_borrower = '" . $this->db->escape(trim($data['contact_person_relation_with_borrower'])) . "', "
                    . "contact_person_email = '" . $this->db->escape($data['contact_person_email']) . "', "
                    . "contact_person_phone_no = '" . (int)$data['contact_person_phone_no'] . "', "
                    . "declaration = '" . (int) $data['declaration'] . "', "
                    . "draft = '" . (int) $draft . "' , "
                    . "last_modified = '" . date("Y-m-d H:i:s") ."', "
                    . "version = '1' "
                    . "WHERE customer_id = " . $data['customer_id'];
            
            $this->db->query($sql);
            
            $credit_application_id = $exits_credit_application['credit_application_id'];

            if(!empty($credit_application_changes)){
            $data_log['credit_application_id'] = $credit_application_id;
            $data_log['step'] = $data['draft'];
            if($draft == $data['draft'] && $exits_credit_application['draft'] != $data['draft']){
                $data_log['action'] = 'ADD';
            }else{
                $data_log['action'] = 'EDIT';
            }
            
            if($data['crm_user_id'] != '' && $data['crm_user_id'] != '0'){
                $data_log['user_id'] = $data['crm_user_id'];
                $data_log['type'] = 'CRM_USER';
            }else if($data['khufiya_user_id'] != '' && $data['khufiya_user_id'] != '0'){
                $data_log['user_id'] = $data['khufiya_user_id'];
                $data_log['type'] = 'KHUFIYA_USER';
            } else{
                $data_log['user_id'] = $data['customer_id'];
                $data_log['type'] = 'CUSTOMER';
            }
            $this->addCreditApplicationActionLog($data_log);
            }
        }
        
        
    }
    
    
    
    public function saveCreditDocument($file_data,$post_data,$version=1,$type='') {
        $log_entry_flag = FALSE;
        if($post_data['credit_application_id']){
            $exits_credit_application = $this->isExitCustomerCreditApplicationIdKhufiya($post_data['credit_application_id'],$version);
        } else if(!empty($post_data['customer_id'])){
            $exits_credit_application = $this->isExitCustomerCreditApplicationId($post_data['customer_id'],$version);  
      }
        
        
        if($exits_credit_application['status'] == true && $exits_credit_application['draft'] > $post_data['draft']){
            $draft = $exits_credit_application['draft'];
        } else{
            $draft = $post_data['draft'];
        }
        
        if($exits_credit_application['status'] == true){
            
            if(SITE_ENVIRONMENT == 'Production'){
                $customer_folder_name = $post_data['customer_id'];
            }else{
                $customer_folder_name = 'staging_' .$post_data['customer_id'];  
            }

            if($post_data['customer_id'] == '0')
                 {
                   $customer_folder_name .= "_".$exits_credit_application['credit_application_id'];
                 }
            
            if(count($file_data) > 0){ 
                foreach($file_data as $key => $files){
                    if(count($files) > 0){
                        foreach($files as $k => $file){
                            if($file['name'] != ''){
                                
                                $document_number = '';
                                $expiry_date = '';
                                
                                if( !empty($post_data[$key]['number']) ){
                                    $document_number = "document_number = '" . $this->db->escape($post_data[$key]['number']) . "', ";
                                }
                                if( !empty($post_data[$key]['expiry_date']) ){
                                    $expiry_date =  "expiry_date = '" . date("Y-m-d", strtotime($post_data[$key]['expiry_date'])) . "', ";
                                }
                                    $user_type="self";
                                    $insert_user_id="user_id = NULL ";
                                if(!empty($post_data['khufiya_user_id'])){
                                    $user_type="khufiya_vibhag";
                                    $insert_user_id="user_id = '".$post_data['khufiya_user_id']."' ";
                                }else if(!empty($post_data['crm_user_id'])){
                                    $user_type="crm";
                                    $insert_user_id="user_id = '".$post_data['crm_user_id']."' ";
                                }

                                $insert_lat_lng='';
                                if(!empty($post_data['image_lat']) && !empty($post_data['image_lng'])){
                                    $insert_lat_lng="image_lat = '".$this->db->escape($post_data['image_lat'])."', ";
                                    $insert_lat_lng .="image_lng = '".$this->db->escape($post_data['image_lng'])."', ";
                                }

                                
                                $sql = "INSERT INTO " . DB_PREFIX . "credit_application_document SET "
                                            . "credit_application_id = '" . (int)$exits_credit_application['credit_application_id'] . "', "
                                            . "customer_id = '" . (int)$post_data['customer_id'] . "', "
                                            . "type = '" . $this->db->escape($post_data['document_type']) . "', "
                                            . "name = '" . $this->db->escape($key) . "', "
                                            . $document_number . $expiry_date 
                                            . "file_path = '" . 'credit_application/' . $this->db->escape($customer_folder_name) . '/' . $this->db->escape($post_data['document_type']) . '/' . $this->db->escape($file['name']) . "', "
                                            . "user_type = '" . $this->db->escape($user_type) . "', "
                                            . $insert_lat_lng
                                            . $insert_user_id;
                                $this->db->query($sql);

                                $last_image_id = $this->db->getLastId();
                                $document_name = ucwords(str_replace('_', ' ', $key));
                                $file_path = 'credit_application/' .$customer_folder_name . '/' . $post_data['document_type'] . '/' . $file['name'];


                                $log_entry_flag = TRUE;
                            }
                        }
                    }
                }
                
            }
            
            
            $form_action = '';
            if($post_data['draft'] == 4){
                $form_action = "form_action = 'edit', ";
            }
            
            //update draft status
            $sql = "UPDATE " . DB_PREFIX . "credit_application SET "
//                . $declaration 
                . $form_action 
                . "draft = '" . (int) $draft . "' "
                . "WHERE customer_id = " . $post_data['customer_id'];
            $this->db->query($sql);
            
            if($log_entry_flag || (isset($post_data['delete_document']) && !empty($post_data['delete_document']))){
            $data_log['credit_application_id'] = $exits_credit_application['credit_application_id'];
            $data_log['step'] = $post_data['draft'];
            if($draft == $post_data['draft'] && $exits_credit_application['draft'] != $post_data['draft']){
                $data_log['action'] = 'ADD';
            }else{
                $data_log['action'] = 'EDIT';
            }
            if(!empty($post_data['crm_user_id']) && $post_data['crm_user_id'] != '' && $post_data['crm_user_id'] != '0'){
                $data_log['user_id'] = $post_data['crm_user_id'];
                $data_log['type'] = 'CRM_USER';
            }else if(!empty($post_data['khufiya_user_id']) && $post_data['khufiya_user_id'] != '' && $post_data['khufiya_user_id'] != '0'){
                $data_log['user_id'] = $post_data['khufiya_user_id'];
                $data_log['type'] = 'KHUFIYA_USER';
            } else{
                $data_log['user_id'] = $post_data['customer_id'];
                $data_log['type'] = 'CUSTOMER';
            }
            $this->addCreditApplicationActionLog($data_log);
            }
        }

        if(!empty($type) && $type =='ajax'){
            $data['last_image_id'] = $last_image_id;
            $data['file_path'] = $file_path;
            $data['document_name'] = $document_name;
            return $data;
        }

        return TRUE;
        
    }

        public function saveShortCredit($data,$credit_application_id=0) {
        $exits_credit_application['status'] = false;
        if(empty($credit_application_id) && !empty($data['credit_application_id'])){
            $credit_application_id=$data['credit_application_id'];
        }
        if($credit_application_id>0){
            $exits_credit_application = $this->isExitCustomerCreditApplicationIdKhufiya($credit_application_id,2);
        }else if(!empty($data['customer_id'])){
            $exits_credit_application = $this->isExitCustomerCreditApplicationId($data['customer_id'],2);
        }
        $credit_application_changes = array();
        if($exits_credit_application['status'] == true){
            if(!empty($exits_credit_application['credit_application_data']['id'])){
                $credit_application_id=$exits_credit_application['credit_application_data']['id'];
            }
            unset($exits_credit_application['credit_application_data']['id']);
            unset($exits_credit_application['credit_application_data']['form_action']);
            @$credit_application_changes = array_diff($exits_credit_application['credit_application_data'],$data);
        } else{
           $credit_application_changes = $data; 
        }
        $data['old_customer_id']=$data['customer_id']??'0';
        if(empty($data['customer_id'])){
            $getCustomerId=$this->getCustomerIdUsingFormDetails($data);
            if(!empty($getCustomerId)){
                $data['customer_id']=$getCustomerId;
            }
        }
        if(!empty($data['dob'])){
                    $dob=date("Y-m-d", strtotime($data['dob']));
                    $dob_set="dob = '" . $dob . "'";
                 }
                   //// else{
                //    $dob_set="dob = NULL";
                // }
                $insert =  "customer_id = " . $data['customer_id'] . ", " ;
                if(isset($data['first_name'])){
                    $insert .="first_name = '" . $this->db->escape(trim($data['first_name'])) . "', ";
                }
                if(isset($data['middle_name'])){
                    $insert .="middle_name = '" . $this->db->escape(trim($data['middle_name'])) . "', ";
                }
                if(isset($data['last_name'])){
                    $insert .="last_name = '" . $this->db->escape(trim($data['last_name'])) . "', ";
                }
                if(isset($data['father_name'])){
                    $insert .="father_name = '" . $this->db->escape(trim($data['father_name'])) . "', ";
                }
                if(isset($data['phone_no'])){
                    $insert .="phone_no = '" . (int)$data['phone_no'] . "', ";
                }
                if(isset($data['gst_number'])){
                    $insert .="gst_number = '" . $this->db->escape(trim($data['gst_number'])) . "', ";
                }
                
                if(isset($data['current_pincode'])){
                    $insert .="current_pincode = '" . $this->db->escape(trim($data['current_pincode'])) . "', ";
                }

                if(isset($data['current_city'])){
                    $insert .="current_city = '" . $this->db->escape(trim($data['current_city'])) . "', ";
                }

                if(isset($data['current_state'])){
                    $insert .="current_state = '" . $this->db->escape(trim($data['current_state'])) . "', ";
                }

                if(isset($data['email'])){
                    $insert .="email = '" . $this->db->escape(trim($data['email'])) . "', ";
                }
                if(isset($data['draft'])){
                    $insert .="draft = '" . $this->db->escape(trim($data['draft'])) . "', ";
                }
                if(isset($data['company_name'])){
                    $insert .="company_name = '" . $this->db->escape(trim($data['company_name'])) . "', ";
                }
                if(isset($data['gender'])){
                    $insert .="gender = '" . $this->db->escape(trim($data['gender'])) . "', ";
                }
                if(isset($data['pan_no'])){
                    $insert .="pan_no = '" . $this->db->escape(trim($data['pan_no'])) . "', ";
                }
                if(!empty($data['aadhaar_no'])){
                    $insert .="aadhaar_no = '" . $this->db->escape(trim($data['aadhaar_no'])) . "', ";
                }
                if(!empty($data['business_start_year'])){
                    $insert .="business_start_year = '" . $this->db->escape(trim($data['business_start_year'])) . "', ";
                }
                if(isset($data['shop_distance'])){
                    $insert .="shop_distance = '" . $this->db->escape(trim($data['shop_distance'])) . "', ";
                }
                if(isset($data['home_distance'])){
                    $insert .="home_distance = '" . $this->db->escape(trim($data['home_distance'])) . "', ";
                }
                if(isset($data['months_in_current_location'])){
                    if(!empty($data['months_in_current_location'])){
                        $months_in_current_location_vals=$data['months_in_current_location'];
                    }else{
                        $months_in_current_location_vals=0; 
                    }
                    $insert .="months_in_current_location = '" . $this->db->escape(trim($months_in_current_location_vals)) . "', ";
                }

                if(!empty($data['current_address'])){
                    $insert .="current_address = '" . $this->db->escape(trim($data['current_address'])) . "', ";
                }
                if(!empty($data['permanent_address'])){
                    $insert .="permanent_address = '" . $this->db->escape(trim($data['permanent_address'])) . "', ";
                }

                if(isset($data['permanent_pincode'])){
                    $insert .="permanent_pincode = '" . $this->db->escape(trim($data['permanent_pincode'])) . "', ";
                }

                if(isset($data['permanent_city'])){
                    $insert .="permanent_city = '" . $this->db->escape(trim($data['permanent_city'])) . "', ";
                }

                if(isset($data['permanent_state'])){
                    $insert .="permanent_state = '" . $this->db->escape(trim($data['permanent_state'])) . "', ";
                }

                if(isset($data['current_city'])){
                    $insert .="current_city = '" . $this->db->escape(trim($data['current_city'])) . "', ";
                }

                if(isset($data['current_state'])){
                    $insert .="current_state = '" . $this->db->escape(trim($data['current_state'])) . "', ";
                }


                if(!empty($data['diffrent_address'])){
                    $insert .="diffrent_address = '1', ";
                    //$insert .="permanent_address = '" . $this->db->escape(trim($data['permanent_address'])) . "', ";
                    //$insert .="permanent_pincode = '" . $this->db->escape(trim($data['permanent_pincode'])) . "', ";
                    //$insert .="permanent_city = '" . $this->db->escape(trim($data['permanent_city'])) . "', ";
                    //$insert .="permanent_state = '" . $this->db->escape(trim($data['permanent_state'])) . "', ";

                }else if(isset($data['draft']) && $data['draft']=='2'){
                    $insert .="diffrent_address = '0', ";
                }else if(!empty($dob_set)){
                    $insert .=$dob_set . ", ";
                }
                    

                    if(isset($data['version']) && !empty($data['version'])){
                            $insert .="version = '".$this->db->escape(trim($data['version']))."', ";
                    }else{
                            $insert .="version = '2', ";
                    } 
                   
            
            if($exits_credit_application['status'] == false || $credit_application_id=='0'){
                $insert .="last_modified = '" . date("Y-m-d H:i:s") . "', ";
                

                 if(isset($data['source'])){
                  $insert .="created_date = '" . date("Y-m-d H:i:s") . "', ";
                  $insert .="source = '" . $this->db->escape(trim($data['source'])) . "' ";
                 }else{
                    $insert .="created_date = '" . date("Y-m-d H:i:s") . "' ";
                 }
                
                $sql = "INSERT INTO " . DB_PREFIX . "credit_application SET "
                       
                        . " $insert ";

                
                $query = $this->db->query($sql);

                $credit_application_id = $this->db->getLastId();

                $this->saveDeviceDetail($data,$credit_application_id);
                
                $data_log['action'] = 'ADD';
            
            
            }else{
            $insert .="last_modified = '" . date("Y-m-d H:i:s") . "' ";
                if (!empty(($credit_application_id))) {
               $sql = "UPDATE " . DB_PREFIX . "credit_application SET "
                        . "  $insert  "
                    . "WHERE id = " .(int)$credit_application_id;
                } else if(!empty($data['customer_id']) && !empty($data['old_customer_id'])){
                            $sql = "UPDATE " . DB_PREFIX . "credit_application SET "
                        . "  $insert  "
                            . "WHERE customer_id = " . (int)$data['customer_id']; 
                        }
                $query = $this->db->query($sql);
                if(isset($credit_application_id) && $credit_application_id!=''){
                 $credit_application_id = $credit_application_id;   
                }else{
                   $credit_application_id='0'; 
                }
                $data_log['action'] = 'Edit';

            }
            
                if(!empty($credit_application_changes)){
                $data_log['credit_application_id'] = $credit_application_id;
                if(isset($data['draft'])){
                    $data_log['step'] = $data['draft'];
                }else{
                    $data_log['step'] = '2';
                }
                if(isset($data['crm_user_id']) && $data['crm_user_id'] != '' && $data['crm_user_id'] != '0'){
                    $data_log['user_id'] = $data['crm_user_id'];
                    $data_log['type'] = 'CRM_USER';
                }else if(isset($data['khufiya_user_id']) && $data['khufiya_user_id'] != '' && $data['khufiya_user_id'] != '0'){
                    $data_log['user_id'] = $data['khufiya_user_id'];
                    $data_log['type'] = 'KHUFIYA_USER';
                } else{
                    $data_log['user_id'] = $data['customer_id'];
                    $data_log['type'] = 'CUSTOMER';
                }
                $this->addCreditApplicationActionLog($data_log);
                }

            if($query){
                return $credit_application_id;
            }else{
                return false;
            }

        
        
    }
    
    public function deleteCreditDocument($ids) {
        if(isset($ids) && $ids != ''){
        $sql = "DELETE FROM " . DB_PREFIX . "credit_application_document WHERE id IN (" .$ids . ")";
        $this->db->query($sql);
        }
    }
    
    public function addCreditApplicationActionLog(array $data) {
        
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
    
    function isExitCustomerCreditApplicationId($customer_id,$version=1){
        
        $data = array();
        $status = false;
        $credit_application = 0;
        $draft = 0;
        $form_action = 'add';
        $credit_application_data = array();
        $credit_application_id = '0';

        $sql = "SELECT id,draft,form_action,business_entity_type,first_name,last_name,middle_name,father_name,pan_no,aadhaar_no,DATE_FORMAT(dob,'%d-%m-%Y') as dob,gender,education,phone_no,email,gst_number,current_address,current_pincode,current_city,current_state,current_landline_phone_no,current_resident_premises,residing_date,permanent_address,permanent_pincode,permanent_city,permanent_state,permanent_landline_phone_no,permanent_resident_premises,company_name,entity_name,partners,shop_establishment_number,business_pan_no,gst,trading_name,nature_of_business,business_premises,occupied_since,business_address,business_pincode,business_city,business_state ,reg_office_address,reg_office_pincode,reg_office_city,reg_office_state,other_business_entity_detail,business_since,annual_turnover,litigation,contact_person_first_name,contact_person_middle_name,contact_person_last_name,contact_person_designation ,contact_person_relation_with_borrower,contact_person_email,contact_person_phone_no,version,business_start_year,months_in_current_location FROM " . DB_PREFIX ."credit_application WHERE customer_id = " . $customer_id ;
        //echo $sql; die;
        $query = $this->db->query($sql);
        if($query->num_rows){
            $status = true;
            $credit_application_id = $query->row['id'];
            $draft = $query->row['draft'];
            $form_action = $query->row['form_action'];
            $credit_application_data = $query->row;
        }
        
        $data['status'] = $status;
        $data['credit_application_id'] = $credit_application_id;
        $data['draft'] = $draft;
        $data['form_action'] = $form_action;
        $data['credit_application_data'] = $credit_application_data;

        return $data;
        
        
    }


    function isExitCustomerCreditApplicationIdKhufiya($credit_application_id,$version=1){
        
        $data = array();
        $status = false;
        $credit_application = 0;
        $draft = 0;
        $form_action = 'add';
        $credit_application_data = array();

        $sql = "SELECT
                      id,
                      customer_id,
                      draft,
                      form_action,
                      business_entity_type,
                      first_name,
                      last_name,
                      middle_name,
                      father_name,
                      pan_no,
                      aadhaar_no,
                      DATE_FORMAT(dob, '%d-%m-%Y') AS dob,
                      gender,
                      education,
                      phone_no,
                      email,
                      gst_number,
                      current_address,
                      current_pincode,
                      current_city,
                      current_state,
                      current_landline_phone_no,
                      current_resident_premises,
                      residing_date,
                      permanent_address,
                      permanent_pincode,
                      permanent_city,
                      permanent_state,
                      permanent_landline_phone_no,
                      permanent_resident_premises,
                      company_name,
                      entity_name,
                      partners,
                      shop_establishment_number,
                      business_pan_no,
                      gst,
                      trading_name,
                      nature_of_business,
                      business_premises,
                      occupied_since,
                      business_address,
                      business_pincode,
                      business_city,
                      business_state,
                      reg_office_address,
                      reg_office_pincode,
                      reg_office_city,
                      reg_office_state,
                      other_business_entity_detail,
                      business_since,
                      annual_turnover,
                      litigation,
                      contact_person_first_name,
                      contact_person_middle_name,
                      contact_person_last_name,
                      contact_person_designation,
                      contact_person_relation_with_borrower,
                      contact_person_email,
                      contact_person_phone_no,
                      business_start_year,
                      months_in_current_location,
                      version FROM " . DB_PREFIX ."credit_application 
                      WHERE id = " . $credit_application_id ;
        $query = $this->db->query($sql);
        if($query->num_rows){
            $status = true;
            $credit_application_id = $query->row['id'];
            $draft = $query->row['draft'];
            $form_action = $query->row['form_action'];
            $credit_application_data = $query->row;
        }
        
        $data['status'] = $status;
        $data['credit_application_id'] = $credit_application_id;
        $data['draft'] = $draft;
        $data['form_action'] = $form_action;
        $data['credit_application_data'] = $credit_application_data;

        return $data;
        
        
    }

       function isExitCustomerCreditApplicationVersion($customer_id){
        
        $data = array();
        $status = false;
        $version = 1;
        $credit_application_data = array();
        $sql = "SELECT id,version FROM " . DB_PREFIX ."credit_application WHERE customer_id = " . $customer_id;
        //echo $sql; die;
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            $status = true;
            $version = $query->row['version'];
        }
        
        $data['status'] = $status;
        $data['version'] = $version;
        return $data;  
    }

    function isExitCustomerCreditApplicationVersionKhufiya($credit_application_id){
        
        $data = array();
        $status = false;
        $version = 1;
        $credit_application_data = array();
        $sql = "SELECT id,version FROM " . DB_PREFIX ."credit_application WHERE id = " . $credit_application_id;
        //echo $sql; die;
        $query = $this->db->query($sql);
        if($query->num_rows){
            $status = true;
            $version = $query->row['version'];
        }
        
        $data['status'] = $status;
        $data['version'] = $version;
        return $data;  
    }
    
    
    function getCustomerCreditApplication($customer_id, $credit_application_id=0){
        
        $result = array();
        if(!empty($customer_id)){
            $sql = "SELECT * FROM " . DB_PREFIX ."credit_application WHERE customer_id = " . $customer_id;
        $query = $this->db->query($sql);
        if($query->num_rows){
            $result = $query->row;
        }
        $app_doc = $this->getCustomerCreditApplicationDocument($customer_id);
        if(count($app_doc) > 0){
            $result['documents'] = $app_doc;
        }

        }else if(!empty($credit_application_id)){

            $sql = "SELECT * FROM " . DB_PREFIX ."credit_application WHERE id = " . $credit_application_id;
        $query = $this->db->query($sql);
        if($query->num_rows == 1){
            $result = $query->row;
        }
        
        $app_doc = $this->getCustomerCreditApplicationDocumentByFormId($credit_application_id);
        if(count($app_doc) > 0){
            $result['documents'] = $app_doc;
        }


        }
        
        return $result;
        
        
        
        
    }
    
    public function uniqueMultiDimensionalArray($array, $key) { 
        $temp_array = array(); 
        $i = 0; 
        $key_array = array(); 

        foreach($array as $val) { 
            if (!in_array($val[$key], $key_array)) { 
                $key_array[$i] = $val[$key]; 
                $temp_array[$i] = $val; 
            } 
            $i++; 
        } 
        return $temp_array; 
    }
    
    function getCustomerCreditApplicationDocument($customer_id){
        
        $result = array();
        
        $sql = "SELECT * FROM " . DB_PREFIX ."credit_application_document WHERE customer_id = " . $customer_id;
        $query = $this->db->query($sql); 
        if($query->num_rows > 0){
            
            $documents = $this->uniqueMultiDimensionalArray($query->rows, 'type');
            $documents = array_unique(array_column($documents, 'type'));
            
            foreach($documents as $key => $doc_type){
                foreach($query->rows as $k => $val){
                    if($query->rows[$k]['type'] == $doc_type){
                        $result[$doc_type][] = $query->rows[$k];
                    }
                }
                
            }
        }
        return $result;
        
    }

    function getDocumentCountByFormId($credit_application_id){
        
        $result = array();
        $sql = "SELECT id FROM " . DB_PREFIX ."credit_application_document WHERE credit_application_id = " . $credit_application_id;
        $query = $this->db->query($sql); 
        $doc_id=array();
        $doc_value='';
        if($query->num_rows > 0){
             foreach($query->rows as $key => $value){
                    $doc_id[$key]=$value['id'];
             }

             $doc_value=implode(',', $doc_id);

        }
        return $doc_value;
        
    }
    function getCustomerCreditApplicationDocumentByFormId($credit_application_id){
        
        $result = array();
        
        $sql = "SELECT * FROM " . DB_PREFIX ."credit_application_document WHERE credit_application_id = " . $credit_application_id;
        $query = $this->db->query($sql); 
        if($query->num_rows > 0){
            
            $documents = $this->uniqueMultiDimensionalArray($query->rows, 'type');
            $documents = array_unique(array_column($documents, 'type'));
            
            foreach($documents as $key => $doc_type){
                foreach($query->rows as $k => $val){
                    if($query->rows[$k]['type'] == $doc_type){
                        $result[$doc_type][] = $query->rows[$k];
                    }
                }
                
            }
        }
        return $result;
        
    }
    
    public function getCreditDocumentsfromIds($ids){
        $result = array();
        
        if(isset($ids) && $ids != ''){
        $sql = "SELECT * FROM " . DB_PREFIX ."credit_application_document WHERE id  IN (" .$ids . ")";
        $query = $this->db->query($sql); 
        if($query->num_rows > 0){
            $result=$query->rows;
        }
        }
        return $result;
    }

    public function CheckPhoneOrEmailIsRegister($type, $value)
    {
        if($type == 'email')
        {
          $sql = "SELECT id FROM " . DB_PREFIX ."credit_application WHERE email  = '" .$this->db->escape(trim($value)) . "' AND document_status != 'duplicate'";
        }
        else
        {
          $sql = "SELECT id FROM " . DB_PREFIX ."credit_application WHERE phone_no  = '" .$this->db->escape(trim($value)) . "' AND document_status != 'duplicate'";
        }  

        $query = $this->db->query($sql);
        return $query->num_rows; 
    }


    public function CheckPhoneOrEmailIsRegisterInEdit($type, $value, $id)
    {
        if($type == 'email')
        {
          $sql = "SELECT id FROM " . DB_PREFIX ."credit_application WHERE email  = '" .$this->db->escape(trim($value)) . "' and id != '".(int)$id."' AND document_status != 'duplicate'";
        }
        else
        {
          $sql = "SELECT id FROM " . DB_PREFIX ."credit_application WHERE phone_no  = '" .$this->db->escape(trim($value)) . "' and id != '".(int)$id."' AND document_status != 'duplicate'";
        }  

        $query = $this->db->query($sql);
        return $query->num_rows; 
    }
    
    function getCreditApplicationCustomerName($customer_id)
    {
        $result = '';
        $sql = "SELECT concat(first_name,' ',last_name ) as name FROM " . DB_PREFIX ."credit_application WHERE customer_id = " . (int)$customer_id;
        $query = $this->db->query($sql); 
        if($query->num_rows > 0){
           $result = $query->rows['0']['name']; 
        }
           return $result;
    }


    function addDefaultDataInCreditApplication($customer_id,$version='0',$data=array())
    {
        $this->load->model('account/address');
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
            $get_customer_address = $this->model_account_address->getAddress((int)$query->row['address_id'], (int)$customer_id);
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
                source = 'auto',
                last_modified = '".date("Y-m-d H:i:s")."',
                created_date = '".date("Y-m-d H:i:s")."' ";
            $this->db->query($credit_sql);
            $credit_application_id=$this->db->getLastId();
            $this->saveDeviceDetail($data,$credit_application_id);
            return $credit_application_id;
        }
        else
        {
            return $query->num_rows;
        }
    }

    function getCreditDocumentId($credit_application_id,$file_path){
        $sql = "SELECT
                  id
                FROM
                  " . DB_PREFIX ."credit_application_document
                WHERE
                  credit_application_id = '".$this->db->escape($credit_application_id)."' 
                  AND file_path = '".$this->db->escape($file_path)."'";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            return $query->rows['0']['id'];
        }else{
            return false;
        }
        
    }


    public function creditApplicationStatusForBanner($user_id) {
        
        $sql = "SELECT
                  cp.draft as credit_application_form_status,
                  cp.gst_number,
                  cwc.status
                FROM
                  " . DB_PREFIX ."credit_application AS cp
                LEFT JOIN
                  " . DB_PREFIX ."customer_wsb_credit cwc ON(
                    cp.customer_id = cwc.customer_id
                  )
                WHERE
                  cp.customer_id = '".$user_id."'";
        $query = $this->db->query($sql);

        $rt['activation_status']='0';
        $rt['credit_application_form_status']='0';
        $rt['gst_number']='0';

        if(!empty($query->num_rows)){
             if(!empty($query->row['status'])){
                $rt['activation_status']='1';
                if($query->row['status']!='ENABLED'){
                    $rt['message']= "Hi,  Your credit limit is ".ucfirst(str_replace('_',' ',strtolower($query->row['status']))).".\nKindly contact us on +91-8239778680";
                    }
            }


            if(!empty($query->row['credit_application_form_status'])){
              $rt['credit_application_form_status']=$query->row['credit_application_form_status'];  
            }
            if(!empty($query->row['gst_number'])){
            $rt['gst_number']=$query->row['gst_number'];
            }
        }

        return $rt;
    }
        public function getIpAndDeviceDetail(){
         $user_agent  =  $_SERVER['HTTP_USER_AGENT'];

        if (!empty($this->request->getIpAddress)) {
            $user_ip = $this->request->getIpAddress;;
        }else{
            $user_ip = '';
        }
        $ip = $this->request->server['REMOTE_ADDR'];
        $response=array();
        //for gettting the OS platform
        $os_platform    =   "Unknown OS Platform";
        $os_array       =   array(
                                '/windows nt 6.2/i'     =>  'Windows 8',
                                '/windows nt 6.1/i'     =>  'Windows 7',
                                '/windows nt 6.0/i'     =>  'Windows Vista',
                                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                '/windows nt 5.1/i'     =>  'Windows XP',
                                '/windows xp/i'         =>  'Windows XP',
                                '/windows nt 5.0/i'     =>  'Windows 2000',
                                '/windows me/i'         =>  'Windows ME',
                                '/win98/i'              =>  'Windows 98',
                                '/win95/i'              =>  'Windows 95',
                                '/win16/i'              =>  'Windows 3.11',
                                '/macintosh|mac os x/i' =>  'Mac OS X',
                                '/mac_powerpc/i'        =>  'Mac OS 9',
                                '/linux/i'              =>  'Linux',
                                '/ubuntu/i'             =>  'Ubuntu',
                                '/iphone/i'             =>  'iPhone',
                                '/ipod/i'               =>  'iPod',
                                '/ipad/i'               =>  'iPad',
                                '/android/i'            =>  'Android',
                                '/blackberry/i'         =>  'BlackBerry',
                                '/webos/i'              =>  'Mobile'
                             );

        foreach ($os_array as $regex => $value) {
         if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
         }
        }

        // for getting the browser of the user
        $browser        =   "Unknown Browser";

        $browser_array  =   array(
                                 '/msie/i'       =>  'Internet Explorer',
                                 '/firefox/i'    =>  'Firefox',
                                 '/safari/i'     =>  'Safari',
                                 '/chrome/i'     =>  'Chrome',
                                 '/opera/i'      =>  'Opera',
                                 '/netscape/i'   =>  'Netscape',
                                 '/maxthon/i'    =>  'Maxthon',
                                 '/konqueror/i'  =>  'Konqueror',
                                 '/mobile/i'     =>  'Handheld Browser'
                           );

        foreach ($browser_array as $regex => $value) {

          if (preg_match($regex, $user_agent)) {
             $browser    =   $value;
          }

        }

        $response['user_os']        =   $os_platform;
        $response['user_browser']   =   $browser;
        $response['user_ip']        =   $user_ip;
        return $response;

    }


    public function saveDeviceDetail($data,$credit_application_id=0){
        if(!empty($credit_application_id)){
                $respose=$this->getIpAndDeviceDetail();
                    $insert ="os = '" . $this->db->escape(trim($respose['user_os'])) . "', ";
                    $insert .="browser = '" . $this->db->escape(trim($respose['user_browser'])) . "', ";
                    if(!empty($data['device_id'])){
                        $insert .="device_id = '" . $this->db->escape(trim($data['device_id'])) . "', ";
                    }
                    $insert .="ip = '" . $this->db->escape(trim($respose['user_ip'])) . "'";
                    $sql = "UPDATE " . DB_PREFIX . "credit_application SET "
                        . "  $insert  "
                        . "WHERE id = " .$credit_application_id;
                        $query = $this->db->query($sql);
        }
        return TRUE;
    }

        public function getUserLocation($customer_id,$type){
            if(!empty($type) && $type=='shop'){
                $sqlTimeConstant=" DATE_FORMAT(FROM_UNIXTIME(cl.location_timestamp),'%H:%i:%s') BETWEEN '12:00:00' AND '18:59:59' ";
                $sqlTimeConstant1=" DATE_FORMAT(FROM_UNIXTIME(location_timestamp),'%H:%i:%s') BETWEEN '12:00:00' AND '18:59:59' ";
            }else{
                $sqlTimeConstant="DATE_FORMAT(FROM_UNIXTIME(cl.location_timestamp),'%H:%i:%s') BETWEEN '00:00:00' AND '05:59:59'";
                $sqlTimeConstant1="DATE_FORMAT(FROM_UNIXTIME(location_timestamp),'%H:%i:%s') BETWEEN '00:00:00' AND '05:59:59'";
            }
            $sql = "SELECT
                    cl.*,
                    CONCAT(
                      TRUNCATE(cl.location_lat ,3),
                      TRUNCATE(cl.location_lng ,3)
                    ) AS similar_group,
                    (
                    SELECT
                      COUNT(*)
                    FROM
                      " . DB_PREFIX ."customer_location
                    WHERE
                      TRUNCATE(`location_lat` ,3) = TRUNCATE(cl.location_lat ,3)
                      AND TRUNCATE(`location_lng` ,3) = TRUNCATE(cl.location_lng ,3)
                      AND `customer_id` = '".(int)$customer_id."'
                      AND $sqlTimeConstant1
                    ) AS lat_long_count
                    FROM
                    " . DB_PREFIX ."customer_location AS cl
                    WHERE
                    `customer_id` = '".(int)$customer_id."' 
                    AND  $sqlTimeConstant
                    group by similar_group
                    ORDER BY
                    lat_long_count DESC";
             $query = $this->db->query($sql);
        $data=array();
        if(!empty($query->num_rows) && $query->row['lat_long_count'] > 2){
            $data['lat'] = $query->row['location_lat'];
            $data['lng'] = $query->row['location_lng'];
        }else if(!empty($query->num_rows) && $query->num_rows > 1){
            $lat_long_count='0';
            foreach($query->rows as $key => $value){
               if($key!='0'){
                    $lat_diffrence = $this->claculateLocationDiffrence($query->row['location_lat'],$value['location_lat']);
                    $lng_diffrence = $this->claculateLocationDiffrence($query->row['location_lng'],$value['location_lng']);
                        if($lat_diffrence <= 0.003 && $lng_diffrence <= 0.003){
                          $lat_long_count = $lat_long_count + $value['lat_long_count'];
                        }
               }else{
                    $lat_long_count=$query->row['lat_long_count'];
               }     
            }
            if($lat_long_count > 2){
                $data['lat'] = $query->row['location_lat'];
                $data['lng'] = $query->row['location_lng'];
            }
        }
        return $data;

      }

    public function claculateLocationDiffrence($value1,$value2){
        $location1=round($value1,3);
        $location2=round($value2,3);
        if($location1 > $location2){
            $distance = $location1 - $location2;
        }else{
            $distance = $location2 - $location1;
        }
        return round($distance,3);

    }


    public function calculateDistance($location){
         $api_url='';
         $result = $origin_array = $destination_array = array();
         foreach ($location['origin'] as $key => $value) {
          if(!empty($value['value'])){
            $origin_array[$key] = urlencode($value['value']);
          }else{
            $origin_array[$key] = $value['lat'].','.$value['lng'];
          }
         }
         foreach ($location['destination'] as $key => $value) {
          if(!empty($value['value'])){
            $destination_array[$key] = urlencode($value['value']);
          }else{
            $destination_array[$key] = $value['lat'].','.$value['lng'];
          }
         }
         if(!empty($origin_array) && !empty($destination_array)){
            $origin=implode('|', $origin_array);
            $destination=implode('|', $destination_array);
            $api_url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin."&destinations=".$destination."&departure_time=now&key=AIzaSyCAwVsw0m7M9vHhGzZmzqPWZUiXblA_Uks";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($response, true);
            }
            return $result;

      }

      public function getCustomerLocationCount($customer_id){
                    $sql = "SELECT *
                    FROM
                      " . DB_PREFIX ."customer_location
                    WHERE
                    `customer_id` = '".(int)$customer_id."'";
             $query = $this->db->query($sql);

             $return['count']=$query->num_rows;
             if(!empty($query->num_rows)){
               $return['results'] = $query->rows;
             }else{
                $return['results'] = array();
             }
             return $return;

      }

      public function getCustomerLocationDetailInGroupByLocation($customer_id,$type){

            if(!empty($type) && $type=='shop'){
                $sqlTimeConstant=" DATE_FORMAT(FROM_UNIXTIME(cl.location_timestamp),'%H:%i:%s') BETWEEN '12:00:00' AND '18:59:59' ";
                $sqlTimeConstant1=" DATE_FORMAT(FROM_UNIXTIME(location_timestamp),'%H:%i:%s') BETWEEN '12:00:00' AND '18:59:59' ";
            }else{
                $sqlTimeConstant="DATE_FORMAT(FROM_UNIXTIME(cl.location_timestamp),'%H:%i:%s') BETWEEN '00:00:00' AND '05:59:59'";
                $sqlTimeConstant1="DATE_FORMAT(FROM_UNIXTIME(location_timestamp),'%H:%i:%s') BETWEEN '00:00:00' AND '05:59:59'";
            }
            $sql = "SELECT
                    cl.*,
                    CONCAT(
                      TRUNCATE(cl.location_lat ,3),
                      TRUNCATE(cl.location_lng ,3)
                    ) AS similar_group,
                    (
                    SELECT
                      COUNT(*)
                    FROM
                      " . DB_PREFIX ."customer_location
                    WHERE
                      TRUNCATE(`location_lat` ,3) = TRUNCATE(cl.location_lat ,3)
                      AND TRUNCATE(`location_lng` ,3) = TRUNCATE(cl.location_lng ,3)
                      AND `customer_id` = '".(int)$customer_id."'
                      AND $sqlTimeConstant1
                    ) AS lat_long_count
                    FROM
                    " . DB_PREFIX ."customer_location AS cl
                    WHERE
                    `customer_id` = '".(int)$customer_id."' 
                    AND  $sqlTimeConstant
                    group by similar_group
                    ORDER BY
                    lat_long_count DESC";
             $query = $this->db->query($sql);
             return $query;
      }

      public function getCustomerSavedEmailCount($customer_id){
        $customer_email_count = $this->db->query("SELECT COUNT(oceh.id) AS count_email
                        FROM
                      " . DB_PREFIX . "customer_email_history AS oceh
                    INNER JOIN
                      " . DB_PREFIX . "customer_email_history_attachment oceha ON oceh.id = oceha.customer_email_history_id
                    WHERE
                      oceh.user_id = '" . (int)$customer_id . "'");
        return $customer_email_count->row['count_email'];
        }

        public function getCustomerSavedEmail($customer_id){
            $sql = "SELECT
                      oceh.date,
                      oceh.subject,
                      oceh.file_path as email_body,
                      oceha.file_path AS attachmemt
                    FROM
                      " . DB_PREFIX . "customer_email_history AS oceh
                    INNER JOIN
                      " . DB_PREFIX . "customer_email_history_attachment oceha ON oceh.id = oceha.customer_email_history_id
                    WHERE
                      oceh.user_id = '" . (int)$customer_id . "'
                    ORDER BY date DESC";
            $query = $this->db->query($sql);
             if(!empty($query->num_rows)){
               $customer_email = $query->rows;
             }else{
                $customer_email = array();
             }
             return $customer_email;
        }

      public function sendPushNotificationAgent($customer_id,$lead_id,$lead_user_id,$action,$message,$title){
        $html_body                  = "";
        $send_wait                  = 'instant';
        $objDateTime                = new DateTime();
        $notification_sending_time  = $objDateTime->format('Y-m-d H:i:s');
        $user_data    = array();
        $mail_send    ='1';
        $order_no     =0;

        if(SITE_ENVIRONMENT == 'Test')
        {
          $path_name = 'staging/';
        }
        else
        {
          $path_name = '';
        }




        $i = 0;

        $user_data[$i]['type']              = 'crm_user';
        $user_data[$i]['id']                = $lead_user_id;
        $user_data[$i]['is_pn_to_send']     = true;             
        $user_data[$i]['is_sms_to_send']    = false;
        $user_data[$i]['is_email_to_send']  = false;
        $user_data[$i]['email_to_head']     = false;
        $user_data[$i]['pn_to_head']        = true;
        $user_data[$i]['is_web_pn_to_send'] = true;
        $user_data[$i]['web_pn_to_head']    = true;
        $user_data[$i]['sms_to_head']       = false;

        $user_data[$i]['email_to_sales_support']     = false;
        $user_data[$i]['pn_to_sales_support']        = false;
        $user_data[$i]['web_pn_to_sales_support']    = false;
        $user_data[$i]['sms_to_sales_support']       = false;
        $user_data[$i]['pn']                     = array("msg_type"=>"9",
                                                         "message"=>$message,
                                                         "title"=>$title,
                                                         "lead_ids"=>$lead_id);
         $user_data[$i]['web_pn']                = array('title'=>$title,
                                                             'message'=>$message,
                                                             'icon'=>'https://d36qiqd7gl7e25.cloudfront.net/img/catalog/rsz_wsb_tmp_logo_286.png',
                                                             'action'=>$action);

       
        $api_data['notification_sending_time']  = $notification_sending_time;
        $api_data['type']                       = $send_wait;
        $api_data['data']                       = $user_data;
        /*
         * api code
        */
        if($mail_send == 1)
        {      
          $json_data = json_encode($api_data);

          $url       = 'https://www.wholesalebox.biz/'.$path_name.'crmapi/Notifications/sendNotificationFromWeb';
          $curl      = curl_init();
         // Set SSL if required
          if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
          }
            
          curl_setopt($curl, CURLOPT_HEADER, false);
          curl_setopt($curl, CURLINFO_HEADER_OUT, true);
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
          curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS,$json_data);            
          $json = curl_exec($curl);
          curl_close($curl);
        } 
        return true;
      }

      public function getCustomerIdUsingFormDetails($data){
            $where_condition=array();
            $customer_id='0';
               if(!empty($data['phone_no'])){
                 $where_condition['0'] = "telephone = '".$this->db->escape(trim($data['phone_no']))."'";
               }
               else{
                $where_condition['1'] = "email = '".$this->db->escape(trim($data['email']))."'";
               }

               if(!empty($where_condition)){
                $implode_conditon=implode(' OR ', $where_condition);
                $query = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer WHERE ".$implode_conditon);
                if(!empty($query->num_rows)){
                   $customer_id = $query->row['customer_id'];
                 }
               }
               return $customer_id;
      }
    
    public function getRblActivationStatus($customer_id) {
        
        if(!empty($customer_id)){

            $sql = "SELECT 
                        c.firstname, c.lastname, cc.credit_balance
                    FROM 
                        ".DB_PREFIX."customer_credit cc
                    INNER JOIN 
                        ".DB_PREFIX."customer c ON cc.customer_id = c.customer_id
                    WHERE 
                        type = 'RBL' and credit_status = 1
                        AND cc.customer_id = ".(int)$customer_id;

            $query = $this->db->query($sql);

            if(!empty($query->num_rows) && !empty($query->row['credit_balance'])){
                return array(
                                "rbl_url"   => '',
                                "approved_limit"    => $query->row['credit_balance'],
                                "retailer_name"     => $query->row['firstname'],
                                "message"           => "Your business loan you've applied for is ready to disburse, The credit limit is ". $this->currency->money_format($query->row['credit_balance'],'INR',1).". ",
                                "action_image_url"  => "https://cdnimages.net/rbl/cif-created.jpg",
                                "action_title"      => "cif_created"
                            );

            }
        }

        return null;
    }

    public function getRblPreapprovedUrl($customer_id) {

        $activeStatus = $this->getRblActivationStatus($customer_id);
        
        if (!empty($activeStatus)) {

            return $activeStatus;

        } else {

            $sql = "SELECT
                        ccp.utr,
                        ccp.approved_limit,
                        ccp.retailer_name
                    FROM
                        ".DB_PREFIX."customer_credit_preapproved AS ccp
                    LEFT JOIN
                        oc_customer_credit AS cc ON ( ccp.retailer_id = cc.customer_id )
                    WHERE
                        ccp.retailer_id = '".(int)$customer_id."'
                        AND cc.customer_id IS NULL
                        AND ccp.approved_by = 'RBL'  
                    ";
            $query = $this->db->query($sql);

            if(!empty($query->num_rows) && !empty($query->row['utr']))
            {
                    return array(
                        "rbl_url"           => RBL_JOURNEY_URL .$query->row['utr'],
                        "approved_limit"    => $query->row['approved_limit'],
                        "retailer_name"     => $query->row['retailer_name'],
                        "message"           => "You're eligible for pre-approved Udhaar of ". $this->currency->money_format($query->row['approved_limit'],'INR',1) ." from RBL Bank. Grow your business with just a tap.",
                        "action_image_url"  => "https://cdnimages.net/rbl/pre-approved.jpg",
                        "action_title"      => "pre_approved"
                    );

            }
        }

        return null;
    }

    /**
	   * Public Method to get credit application document status using credit application id
	   * @param: $credit_application_id int
	   * @return $document_status String
	   * @author: Devendra, August 2019
	*/
    function getCreditApplicationDocumentStatus($credit_application_id)
    {
        $result = '';
        $sql = "SELECT document_status FROM " . DB_PREFIX ."credit_application WHERE id = " . (int)$credit_application_id;
        $query = $this->db->query($sql); 
        if($query->num_rows > 0){
           $result = $query->row['document_status']; 
        }
        return $result;
    }

}