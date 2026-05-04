<?php

class ControllerTestCreditData extends Controller
{
    /**
     *
     */
    public function import(){

       $file_path = DIR_UPLOAD.'Credit-Leads-DataX.csv';
       if(file_exists($file_path))
       {  
         $file = fopen($file_path, 'r');
         $i = 0;
         while (($line = fgetcsv($file)) !== FALSE) {
            //$line is an array of the csv elements
            if ($i <= 0) {
                $i++;
                continue;
            }
            
            $customer_id= 0; 
            $shop_name  = $line[0];
            $name       = $line[1];
            $city       = $line[2];
            $mobile     = $line[3];
            $email      = $line[4];
            $note       = $line[5];
            $status     = '';
            $type       = 'Note';

            if(strstr(strtolower($note), 'rejected' ))
            {
              $status   = 'rejected';
              $type     = 'Comment';
            }
            if(strstr(strtolower($note), 'approved' ))
            {
              $status   = 'approved';
              $type     = 'Comment';
            }
            if(strstr(strtolower($note), 'not interested' ))
            {
              $status   = 'customer_not_interested';
              $type     = 'Comment';
            }

            $name_arr = explode(" ", $name);
            $first_name = isset($name_arr[0]) ? $name_arr[0] : '';
            $last_name  = isset($name_arr[1]) ? $name_arr[1] : '';


            $sql = "SELECT id from ".DB_PREFIX."credit_application where phone_no = '". $this->db->escape($mobile)."'";
   
           $sql_query = $this->db->query($sql);
           if($sql_query->num_rows == 0)
           {
             $customer_sql = "SELECT customer_id from ".DB_PREFIX."customer where telephone = '". $this->db->escape($mobile)."'";
             $customer_query = $this->db->query($customer_sql);
             if($customer_query->num_rows > 0)
             {
               $customer_id = $customer_query->row['customer_id'];
             }

              $sql = "INSERT INTO ".DB_PREFIX."credit_application SET
                customer_id = '".(int)$customer_id."',
                first_name = '".$this->db->escape($first_name)."',
                last_name = '".$this->db->escape($last_name)."',
                phone_no= '".$this->db->escape($mobile)."',
                email= '".$this->db->escape($email)."',
                current_city = '" . $this->db->escape($city) . "',
                company_name = '" . $this->db->escape($shop_name) . "',
                document_status = '" . $this->db->escape($status) . "',
                version = '2',
                source = 'import',
                created_date = NOW(),
                last_modified = NOW()";  
                
               if($this->db->query($sql))
                {

                  $credit_application_id =  $this->db->getLastId();
                  $sql = "INSERT INTO ".DB_PREFIX."credit_application_status_remarks SET
                  type = '".$this->db->escape($type)."',
                  credit_application_id = '".(int)$credit_application_id."',
                  user_id= '0',
                  status= '".$this->db->escape($status)."',
                  remark = '" .$this->db->escape($note) . "',
                  date_added = NOW()"; 
                  $this->db->query($sql);
                }
           }
           else
           {
            echo $mobile." already exist. <br />";
           }

        }
        fclose($file);
      
         exit('success');
      }
      else
      {
        exit("file not found");
      }   
        // exit;
    }


    public function update_gst()
    {

        $sql = "SELECT ca.id, ca.customer_id, c.gst_number as customer_gst_number from ".DB_PREFIX."credit_application ca 
				INNER JOIN ".DB_PREFIX."customer c ON (ca.customer_id = c.customer_id) 
				where ca.customer_id > 0 AND (c.gst_number != '' OR c.gst_number IS NOT NULL) AND (ca.gst_number = '' OR ca.gst_number IS NULL) ";


        $sql_query = $this->db->query($sql);
        foreach($sql_query->rows as $row)
        {
            $check_sql = "SELECT id from ".DB_PREFIX."credit_application where gst_number = '".$this->db->escape($row['customer_gst_number'])."'";
            $check_query = $this->db->query($check_sql);

            if($check_query->num_rows == 0)
            {
                $sql = "UPDATE ".DB_PREFIX."credit_application SET
                gst_number = '".$this->db->escape($row['customer_gst_number'])."' 
                 where id = '". (int)$row['id'] ."'";

                $this->db->query($sql);
                echo $sql."<br/>Affected rows: " . $this->db->countAffected(). "<br/><br/>";

            }
        }
        echo "success";
        exit;

    }

    public function update_city_state()
    {

        $sql = "SELECT id, current_pincode from ".DB_PREFIX."credit_application where current_pincode != '' and (current_city = '' or current_state = '')";
   
           $sql_query = $this->db->query($sql);
           foreach($sql_query->rows as $row)
           {
              
              // set city and state 
                $request['pincode'] = $row['current_pincode'];
                $data_json = json_encode($request);
                $api_url = HTTPS_SERVER."index.php?route=restapi/lookup/pincode";
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
                $data = json_decode($result, true );

                $current_city = isset($data[0]['city']) ? $data[0]['city']:'';
                $current_state = isset($data[0]['zone']) ? $data[0]['zone']:'';

              $sql = "UPDATE ".DB_PREFIX."credit_application SET
                current_city = '".$this->db->escape($current_city)."',
                current_state = '".$this->db->escape($current_state)."' 
                 where id = '". (int)$row['id'] ."'";
              $this->db->query($sql);
           }
           echo "success";
            exit;

      } 

    public function update_notification()
    {
        $sql = "SELECT credit_application_id, message, created from ".DB_PREFIX."credit_application_notification_log order by id ASC";
   
           $sql_query = $this->db->query($sql);
           foreach($sql_query->rows as $row)
           {
               $sql = "INSERT INTO ".DB_PREFIX."credit_application_status_remarks SET
                credit_application_id = '".(int) $row['credit_application_id']."',
                type = 'Notification',
                user_id = '0',
                remark = '".$this->db->escape($row['message'])."',
                date_added = '".$this->db->escape($row['created'])."'"; 
               $this->db->query($sql);

           }
           echo "success";
            exit;

      }


###### Customer RBL OnBoarding Data [14 Sept 2019] #####

      public function customerRBLCreditData()
      {
        $customer_id = $this->request->get['customer_id']; 

        //Load model of khufiya Vibhag
        $this->load->model('sale/customer_credit_application','admin');
        $this->load->model('report/cohort','admin');

        $filter_data = array('filter_customer_id' => $customer_id); 
        $customer_credit_application = $this->admin_model_sale_customer_credit_application
                                            ->getCustomerCreditApplication( $filter_data );
        $credit_application_id = $customer_credit_application[0]['id'] ?? 0;

        if($credit_application_id) 
        {

          $customer_credit_data = $this->admin_model_sale_customer_credit_application
                                     ->getCreditApplicationData((int)$credit_application_id);

          $cohort_data = $this->admin_model_report_cohort
                              ->getCohortData(
                                      'order_total',
                                      array('num_intervals' => 12),
                                      array(),
                                      array(),
                                      array('consider_master_id' => $customer_id)
                                    );
            ##Customer Sales Data  
            $customer_sales_data = array();
            $customer_sales_data['date_of_first_order'] = $cohort_data[0]['Acq. Month'] ?? '';
            $customer_sales_data['M0'] = $cohort_data[0]['+0 M'] ?? '0.00';
            $customer_sales_data['M1'] = $cohort_data[0]['+1 M'] ?? '0.00';
            $customer_sales_data['M2'] = $cohort_data[0]['+2 M'] ?? '0.00';
            $customer_sales_data['M3'] = $cohort_data[0]['+3 M'] ?? '0.00';
            $customer_sales_data['M4'] = $cohort_data[0]['+4 M'] ?? '0.00';
            $customer_sales_data['M5'] = $cohort_data[0]['+5 M'] ?? '0.00';
            $customer_sales_data['M6'] = $cohort_data[0]['+6 M'] ?? '0.00';
            $customer_sales_data['M7'] = $cohort_data[0]['+7 M'] ?? '0.00';
            $customer_sales_data['M8'] = $cohort_data[0]['+8 M'] ?? '0.00';
            $customer_sales_data['M9'] = $cohort_data[0]['+9 M'] ?? '0.00';
            $customer_sales_data['M10'] = $cohort_data[0]['+10 M'] ?? '0.00';
            $customer_sales_data['M11'] = $cohort_data[0]['+11 M'] ?? '0.00';
            $customer_sales_data['M12'] = $cohort_data[0]['+12 M'] ?? '0.00';


            $customer_data      = $customer_credit_data['customer'];
            $customer_business_data = $customer_credit_data['businessDetails'];
            $customerNameAsOnPanNumber = $customer_data['firstName'] ?? ''; 
            if(!empty($customer_data['middleName'])) {
              $customerNameAsOnPanNumber .= ' ' . $customer_data['middleName'];
            }
            if(!empty($customer_data['lastName'])) {
              $customerNameAsOnPanNumber .= ' ' . $customer_data['lastName'];
            }
            $residence_address = $customer_data['permanentAddress']['line1'] ?? '';
            if(!empty($customer_data['permanentAddress']['line2'])){
              $residence_address .= ' ' . $customer_data['permanentAddress']['line2'];
            }
            if(!empty($customer_data['permanentAddress']['city'])){
              $residence_address .= ' ' . $customer_data['permanentAddress']['city'];
            }
            if(!empty($customer_data['permanentAddress']['district'])){
              $residence_address .= ' ' . $customer_data['permanentAddress']['district'];
            }
            if(!empty($customer_data['permanentAddress']['postcode'])){
              $residence_address .= ' ' . $customer_data['permanentAddress']['postcode'];
            }
            if(!empty($customer_data['permanentAddress']['country'])){
              $residence_address .= ' ' . $customer_data['permanentAddress']['country'];
            }
                
            $residence_address =  $this->format_string($residence_address ); 

            $customer_telephone_number = $customer_data['telephoneNumber'] ?? '';
            $customer_telephone_number = $this->validate_mobile_number($customer_telephone_number);
            $browser      = $customer_data['browser'];
            $ip           = $customer_data['ip'];
            $csv_date_timing= $customer_data['csv_date_timing'];
            $csv_date_register_with_anchor = $customer_data['csv_date_register_with_anchor'];
              
            $customer_delivery_address = $this->admin_model_sale_customer_credit_application->isCustomerLastDeliveredOrderAddress($customer_id);
            $delivery_address_landmark = '';
            if(!empty($customer_delivery_address)){
              $delivery_address_line_1  = $customer_delivery_address['shipping_address_1'];
              $delivery_address_line_2  = $customer_delivery_address['shipping_address_2'];
              $delivery_address_city    = $customer_delivery_address['shipping_city'];
              $delivery_address_state   = $customer_delivery_address['shipping_zone'];
              $delivery_address_postcode  = $customer_delivery_address['shipping_postcode'];
            }else{
              $delivery_address_line_1  = $customer_business_data['businessAddress']['line1'] ?? '';
              $delivery_address_line_2  = $customer_business_data['businessAddress']['line2'] ?? '';
              $delivery_address_city    = $customer_business_data['businessAddress']['city'] ?? '';
              $delivery_address_state   = $customer_business_data['businessAddress']['state'] ?? '';
              $delivery_address_postcode  = $customer_business_data['businessAddress']['postcode'] ?? '';
            }


            $csv_data = array(
                              date('m/d/Y'),
                              'WHOLESALEBOX',
                              'WSB111',
                              $customer_id ?? '',
                              $customerNameAsOnPanNumber ?? '',
                              date('m/d/Y',strtotime($customer_data['dob'])),
                              trim($customer_data['panNumber']) ?? '',
                              trim($customer_data['gender']) ?? '',
                              trim($customer_telephone_number),
                              $residence_address ?? '',
                              trim($customer_data['residentialAddress']['postcode']) ?? '',
                              trim($customer_data['emailAddress']) ?? '',
                              trim($this->format_string($customer_business_data['companyName'])) ?? '',
                            //Delivery Address
                              trim($this->format_string($delivery_address_line_1)),
                              trim($this->format_string($delivery_address_line_1)),
                              $delivery_address_landmark,
                              trim($this->format_string($delivery_address_city)),
                              trim($this->format_string($delivery_address_state)),
                              trim($delivery_address_postcode),
                            //Delivery Address
                              (int)(trim($customer_business_data['annual_turnover'])!='') ? $customer_business_data['annual_turnover'] :'0',
                              trim($customer_business_data['business_vintage']) ?? '',
                              $customer_sales_data['date_of_first_order'] ?? '',
                              '',
                              $customer_sales_data['M0'] ?? '0.00',
                              $customer_sales_data['M1'] ?? '0.00',
                              $customer_sales_data['M2'] ?? '0.00',
                              $customer_sales_data['M3'] ?? '0.00',
                              $customer_sales_data['M4'] ?? '0.00',
                              $customer_sales_data['M5'] ?? '0.00',
                              $customer_sales_data['M6'] ?? '0.00',
                              $customer_sales_data['M7'] ?? '0.00',
                              $customer_sales_data['M8'] ?? '0.00',
                              $customer_sales_data['M9'] ?? '0.00',
                              $customer_sales_data['M10'] ?? '0.00',
                              $customer_sales_data['M11'] ?? '0.00',
                              $customer_sales_data['M12'] ?? '0.00',
                              'CONSENT',
                              $browser,
                              $ip,
                              date('m/d/Y h:i',strtotime($csv_date_timing)),
                              date('m/d/Y',strtotime($csv_date_register_with_anchor))
                            );

            ## csv file headings
              $csv_heading = array(
                              'DATE', 
                              'Anchor_name',
                              'Anchor_ID',
                              'Retailer_ID', 
                              'Customer_Name_As_on_Pan_Card', 
                              'DOB_As_on_Pan_Card',
                              'PAN_No',
                              'Gender',
                              'Mobile_Number_10_Digit',
                              'Residence_Address',
                              'Residence_Pincode',
                              'Email_Id_O',
                              'Firm_Name',
                              'Delivery_Address_line_1',
                              'Delivery_Address_line_2',
                              'Delivery_Address_Landmark', 
                              'Delivery_Address_city',
                              'Delivery_Address_State', 
                              'Delivery_Address_Pincode',  
                              'Annual_Turnover_Self_Declared_O',
                              'Retailer_Business_Vintage_O_In_Months',
                              'Date_of_1st_Order_delivery_by_the_anchor',
                              'Sales_Data',
                              'M1',
                              'M2',  
                              'M3', 
                              'M4',  
                              'M5',  
                              'M6',  
                              'M7',  
                              'M8',  
                              'M9',  
                              'M10', 
                              'M11', 
                              'M12', 
                              'M13', 
                              'CONSENT', 
                              'Device_ID_Browser', 
                              'IP_Address',  
                              'Date_Timing', 
                              'Date_of_registration_with_anchor',
                          );


            ## create csv file 
              $file_name = DIR_DLOAD . 'rbl_data.csv';
              $fp = fopen($file_name,'w');
              //write csv file heading  
              fputcsv($fp, $csv_heading);    
              //write csv file data
              fputcsv($fp, $csv_data);
              fclose($fp);  

          ## Sent email with csv file as attachment
              $body = "PFA customer credit application data for RBL onboarding processing";
              $mail = new PHPMailer();
              $mail->isSMTP();
              $mail->Host = $this->config->get('config_mail_smtp_hostname');
              $mail->Port = $this->config->get('config_mail_smtp_port');
              $mail->SMTPSecure = 'ssl';
              $mail->SMTPAuth = true;
              $mail->Username = $this->config->get('config_mail_smtp_username');
              $mail->Password = $this->config->get('config_mail_smtp_password');

              $mail->setFrom($this->config->get('config_mail_smtp_username'), 'WholesaleBox');
              $mail->addAddress(EMAIL_IDS['rakesh']['email_id'], EMAIL_IDS['rakesh']['name']);

              $mail->Subject = "Customer credit application data for RBL onboarding  - " . $email_subject;
              $mail->AddAttachment($file_name);
              $mail->msgHTML($body);
              $mail->send(1,false);
            ## remove file from folder 
              unlink($file_name);
              exit;
          }
      }
      protected function format_string(string $subject)
      {
        return  preg_replace( "/<br>|\r|\n|,/", "", $subject ); 
      }
      protected function validate_mobile_number( string $mobile_number )
      {
        //remove extra chars - space, plus or hypen sign allow only numbers 0-9
        $mobile_number = preg_replace("/[^0-9]/", "", $mobile_number);

        // get last 10 digit from mobile string and validate for numbers only
        $mobile_number = substr( $mobile_number, -10 );

        return $mobile_number;
      }
      
}

