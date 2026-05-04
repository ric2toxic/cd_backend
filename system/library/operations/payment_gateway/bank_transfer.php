<?php
require_once(DIR_SYSTEM . 'library/operations/payment_gateway/payment_gateway_base.php');
/**
 * 	BankTransfer
 * 	@author @Nishu, Sept 2017
 */
class BankTransfer extends PaymentGatewayBase 
{    
    private $PYMT_SHEET_FOLDER          = '/var/payment-sheet/';
    private $STANC_PYMT_OUT_FOLDER      = '/opt/standard-chartered/host2host/documents/payments/out/wholesale-h2h/';
    private $STANC_ACK_REJ_BASE_FOLDER  = '/opt/standard-chartered/host2host/documents/payments/in/';
    
    private $_noinfo_customer = array();
    private $_mail = null;
    private $refund_initiated = array();
    
	public function __construct($registry) {
        parent::__construct($registry);
		$this->paymentgateway = 'bank_transfer';
  	}

    public function generatePaymentLink($data) {}
    
    public function paymentRefund($data) {
        
        // Generate CSV
        $file = $this->saveInCsv($data);
        
        // If csv is generated
        if ($file && !empty($file['filename']) && !empty($file['filepath']) ) {
        
            // Copy the csv to Host2Host folder
            chmod($file['filepath'], 0777);
            copy($file['filepath'], $this->STANC_PYMT_OUT_FOLDER.$file['filename']);
            chmod($this->STANC_PYMT_OUT_FOLDER.$file['filename'], 0777); // Changing permission of file in Stanc folder
            
            //Send mail to inform about refunds initiated by bank transfer.
            $this->sendMailRefundInitiatedCsv($file['filepath']);        
        }
                    
        // If there are customers with no bank details, and pending tentative refund
        if(!empty($this->_noinfo_customer)){
            $this->sendMailForPendingBankInfo();   
        }
        
    }

    /**
    * Private function to generate bank transfer CSV based on input $payments data array
    * @param: array
    * @return: void
    * @author: Nishu, Oct 2017
    */
    private function saveInCsv($payments){
        
        $filename = 'refund_payments_' . date('dmYHis') . '.csv';
        $filepath = $this->PYMT_SHEET_FOLDER . $filename ;
        
        $cn_obj = new CreditNote($this->registry);

        // creating file
        $fp = fopen($filepath, 'w');
        
        if(!empty($payments)){
            //Update payment_cleared flag in oc_tentative_refund
            $payment_refund_object = new PaymentRefund($this->registry);
            
            // Insert into DB
            $sql = "INSERT INTO " . DB_PREFIX . "stanc_payment_csv 
                    (`csv_file_path`, `upload_success`, `date_added`) 
                    VALUES 
                    ('" . $this->db->escape($filepath) . "', 0, NOW() )";
            $query = $this->db->query($sql);
            $payment_csv_id = $this->db->getLastId();
            
            // Creating CSV Header Row
            $head = array(
                        'Identifier',
                        'Payment Type/Invoice Ref',
                        'Customer Ref/Inv Desc',
                        'Debit Account/Inv Amt',
                        'Payment Date/Inv Date',
                        'Payee Name',
                        'Payee Account Number',
                        'IFSC Code',
                        'Payment Amount',
                        'Email ID',
                        'Payment detail',
                        'POP Code'
                    );
            fputcsv( $fp , $head );

            // Filling transactions row by row
            foreach( $payments as $tr_id => $details) {
                $order_id = $details['order_id'];
                $order_no = $details['order_no'];
                $customer = $details['customer'];
                
                // Atleast Rs 1 needs to be refunded
                if (!isset($details['refund']) || $details['refund'] <= 1) {
                    continue; // Invalid Tentative Refund - Skipping
                }
                
                // Check if bank account details of the customer exist
                if ( empty(trim(strtoupper($customer['bank_ac_holder_name']))) 
                  || empty(trim($customer['bank_ac_number'])) 
                  || empty(trim(strtoupper($customer['ifsc_code']))) 
                  || empty((float)$details['refund']) 
                ) {

                    //Mark tentative refund not done from flag under_process
                    $payment_refund_object->updateTentativeRefund( (int)$tr_id, 1, 0);

                    // Ignore this refund entry
                    $this->_noinfo_customer[$tr_id] = $details;
                    continue;
                }
                
                $ref_type = "CN";
                if($details['refund_type'] == "EXCESS_PAYMENT_BY_CUSTOMER"){
                    $ref_type = "EP";
                }
                
                // Initializing temp arrays
                $trxn_data = array();
                $trxn_data['tr_id']           = $tr_id;
                $trxn_data['trxn_for']        = $details['refund_type'];
                $trxn_data['trxn_for_id']     = $details['payment_breakup']['ref_id'];
                $trxn_data['trxn_done']       = 'BANK_REQUESTED';
                $trxn_data['trxn_amount']     = $details['refund'];
                $trxn_data['trxn_bank']       = 'stanc';
                $trxn_data['trxn_date_added'] = 'NOW()';
                $trxn_data['details']         = $details;
                
                // Determining a unique reference number
                $ref_exists = true; // initializing true assuming $ref_no already exists
                $ref_no     = '';
                while($ref_exists){ //Loop untill reference number is unique
                    $customer_id_ref_prefix = strval($customer['customer_id']) . '-' . $ref_type;
                    $ref_no = $customer_id_ref_prefix . substr(str_shuffle(uniqid()), 0, 16 - strlen($customer_id_ref_prefix));
                    $ref_exists = $this->checkIfRefNoExistsInStancPayments($ref_no);
                }
                
                // CSV Payment (P) Row
                $row = array(
                    'P',
                    'ACH',
                    $ref_no,
                    '75105102107',
                    date('d/m/Y'),
                    trim(strtoupper($customer['bank_ac_holder_name'])),
                    trim($customer['bank_ac_number']),
                    trim(strtoupper($customer['ifsc_code'])),
                    (float)$details['refund'],
                    trim($customer['email']),
                    'WholesaleBox Refund',
                    '',
                );
                
                // Record in CSV
                fputcsv( $fp , $row );
               
                //Mark tentative refund as done from under_process
                $payment_refund_object->updateTentativeRefund( (int)$tr_id, 1, 1);

                // Mark excess payment in order table 'YES', if it is an excess_payment case
                if ($details['refund_type'] == 'EXCESS_PAYMENT_BY_CUSTOMER') {
                    OrderEdit::updateExcessPayment($this->db, $order_id, 'YES');
                }else{
                    $cn_data = array();
                    $cn_data['payment_cleared'] = 'YES';
                    $cn_data['credit_note_id']  = $details['ref_id'];
                    
                    //Mark payement_cleared field as YES in oc_credit_note table
                    $cn_obj->updateCnPaymentClearedFlag($cn_data);
                }

                //Insert trxn details into DB
                $trxn_id = parent::addTrxnDetails($trxn_data);
                
                //Add order_payment details dynamically
                $order_data                      = array();
                $order_data['successfull']       = 1;
                $order_data['order_id']          = $order_id;
                $order_data['order_no']          = $order_no;
                $order_data['payment_mode']      = 'BANK TRANSFER';
                $order_data['merchant_txn_id']   = '';
                $order_data['trxn_id']           = $trxn_id;
                $order_data['refund_amount']     = -abs($details['refund']);
                $order_data['payment_link']      = $ref_no;
                $order_data['payment_gateway']   = 'bank_transfer';
                $order_data['bank_transfer_mode']= 'instant';
                $order_data['payment_mode']      = 'stanc';
                $order_data['reference']         = $details['ref'];
                $order_data['txn_status']        = 'BANK_REQUESTED';
                $order_data['txn_date_time']     = date('Y-m-d');
                $order_data['user_id']           = 0;
                $order_data['sales_staff_id']    = 0;
                $order_data['json_format']       = '';
                $order_payment_id = parent::insertRefundPaymentIntoDb($order_data);    

                $this->refund_initiated[$tr_id] = $details;
                
                // populating payment breakup details
                // CSV Payment (I) Row
                if(!empty($details['payment_breakup'])){
                    $payment_breakup = $details['payment_breakup'];
                    $row = array(
                                'I', 
                                $order_no, 
                                @$payment_breakup['ref'],
                                (float)@$payment_breakup['amount'],
                                date('d/m/Y', strtotime(@$payment_breakup['date_added'])),
                            );
                    fputcsv( $fp , $row );
                    
                    // Storing in DB
                    $breakup_detail_field = array();
                    $breakup_detail_field['refund']['order_id']         = $order_id;
                    $breakup_detail_field['refund']['trxn_id']          = $trxn_id;
                    $breakup_detail_field['refund']['tr_id']            = $tr_id;
                    $breakup_detail_field['refund']['order_payment_id'] = $order_payment_id;
                    $breakup_detail_field['refund']['ref_id']           = $trxn_data['trxn_for_id'];
                    $breakup_detail_field['refund']['ref']              = $payment_breakup['ref'];
                    $breakup_detail_field['refund']['amount']           = $payment_breakup['amount'];
                    $breakup_detail_field['refund']['type']             = $details['refund_type'];
                    
                    $sql = "INSERT INTO oc_stanc_payment_breakup 
                            (payment_csv_id, 
                             seller_id, 
                             payment_ref_no, 
                             payee_name, 
                             payee_account_no, 
                             payee_ifsc_code, 
                             payee_email, 
                             payee_amount, 
                             breakup_details,
                             payment_type) 
                            VALUES ('" . (int)$payment_csv_id . "', 
                                    '" . (int)$customer['customer_id'] . "', 
                                    '" . $this->db->escape($ref_no) . "', 
                                    '" . $this->db->escape(trim(strtoupper($customer['bank_ac_holder_name']))) . "', 
                                    '" . $this->db->escape(trim($customer['bank_ac_number'])) . "', 
                                    '" . $this->db->escape(trim(strtoupper($customer['ifsc_code']))) . "', 
                                    '" . $this->db->escape(trim($customer['email'])) . "', 
                                    '" . (float)$details['refund'] . "', 
                                    '" . $this->db->escape(serialize($breakup_detail_field)) . "',
                                    '" . $this->db->escape('CUSTOMER_REFUND') . "')";
                    $this->db->query($sql);
                }
                
                //Send SMS to Customer on successfull Refund Initiation
                $this->load->language('account/sms_templates');
                $mobile_no  = $customer['mobile'];
                $message    = '';
                if($details['refund_type'] == "EXCESS_PAYMENT_BY_CUSTOMER"){
                    $message = sprintf($this->load->language->get('cust_refund_excess_payment'), $details['refund'], $order_no);
                }else{
                    $message = sprintf($this->load->language->get('cust_refund_CN'), $details['refund'], $order_no);
                }
                $send_sms = new SMS($message,$mobile_no);
                $send_sms->sendMessage();
                
            } // end loop on tentative refund id
            fclose($fp);
            if ( !empty($this->refund_initiated) ) {
                return array('filename' => $filename, 
                             'filepath' => $filepath
                            );
            }
            
        } // end if (!empty(payments))
        
        // if we are here, something wrong
        //Remove file from path, if no refund found to initiate.
        fclose($fp);
        unlink($filepath);
        return false;
        
    } // end function

    /**
     * Public function to check if the input @param $ref_no is unique or not.
     * It checks if the given ref_no already exists in the stanc_payment_breakup table or not.
     * @param: $ref_no String
     * @return: Boolean; True if $ref_no is unique (does not exist already), else False
     * @author: Nishu, Nov 2017
    */
    public function checkIfRefNoExistsInStancPayments($ref_no){
        $sql = "SELECT payment_id 
                FROM oc_stanc_payment_breakup
                WHERE 
                    payment_ref_no = '". $this->db->escape($ref_no) . "' 
                LIMIT 1";
        $result = $this->db->query($sql);
        
        if ($result->num_rows > 0) {
            return true;
        }
        
        return false;
    }

    public static function getCustomerCredentials($db){
        $sql = "
                SELECT customer_id, ws_access_token
                  FROM oc_customer
                  WHERE 
                    ws_access_token IS NOT null
                    AND ws_access_token != ''
                  LIMIT 0, 1
               ";
        $result = $db->query($sql);
        return $result->row;
    }
    

    /**
     * Sending mail if any customer's bank details are missing.
     * Refunds by bank transfer cannot be done untill bank details are there.
     * @param void
     * @return void
     * @author Nishu, Nov 2017   
     */
    private function sendMailForPendingBankInfo(){

        if(!empty($this->_noinfo_customer)){
            $alldata = array();

            $customer = self::getCustomerCredentials($this->db);
            
            $alldata['data']     = $this->_noinfo_customer;
            $alldata['customer'] = $customer;

            //Get TL Data from CRM APIs
            $agent_data = MailTemplate::getAgentName($alldata);

            //Send summary mail with all details
            $html    = MailTemplate::mailPendingCustBankDetails($agent_data['data']);

            $subject = 'Bank Details Pending for Customer Refund Payments - ' . date('d/M/Y H:i:s', time());

            //Send mail internally
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $this->config->get('config_mail_smtp_hostname');
            $mail->Port = $this->config->get('config_mail_smtp_port');
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('config_mail_smtp_username');
            $mail->Password = $this->config->get('config_mail_smtp_password');
            
            $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
            $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
            $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
            $mail->addCC(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
            $mail->addCC(EMAIL_IDS['vipul']['email_id'], EMAIL_IDS['vipul']['name']);
            $mail->addCC(EMAIL_IDS['tele.manager']['email_id'], EMAIL_IDS['tele.manager']['name']);

            $mail->Subject = $subject;
            $mail->msgHTML($html);
            $mail->send(1, false);

            if(!empty($agent_data['agents_data'])){
                foreach ($agent_data['agents_data'] as $key => $email) {
                    if(!empty($agent_data['data'][$key])){
                        //Send summary mail with all details
                        $html    = MailTemplate::mailForPendingBankDetailsIndividually($agent_data['data'][$key]);
                        //Send mail internally
                        $mail = new PHPMailer();
                        $mail->isSMTP();
                        $mail->Host = $this->config->get('config_mail_smtp_hostname');
                        $mail->Port = $this->config->get('config_mail_smtp_port');
                        $mail->SMTPSecure = 'ssl';
                        $mail->SMTPAuth = true;
                        $mail->Username = $this->config->get('config_mail_smtp_username');
                        $mail->Password = $this->config->get('config_mail_smtp_password');
                        $mail->addAddress($email);
                        $mail->Subject = $subject;
                        $mail->msgHTML($html);
                        $mail->send(1, false);
                    }
                }
            }
       }

    } //End of sendMailForPendingBankInfo

    /**
     * Sending mail for initiating refunds with attachment
     * @param void
     * @return void
     * @author Nishu, Nov 2017   
     */
    private function sendMailRefundInitiatedCsv($filepath){
        // Send mail with CSV as attachment
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->config->get('config_mail_smtp_hostname');
        $mail->Port = $this->config->get('config_mail_smtp_port');
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('config_mail_smtp_username');
        $mail->Password = $this->config->get('config_mail_smtp_password');
        $mail->addAddress(EMAIL_IDS['accounts']['email_id'], EMAIL_IDS['accounts']['name']);
        $mail->addAddress(EMAIL_IDS['returns']['email_id'], EMAIL_IDS['returns']['name']);
        $mail->addCC(EMAIL_IDS['chandan']['email_id'], EMAIL_IDS['chandan']['name']);
        $mail->addCC(EMAIL_IDS['rohit']['email_id'], EMAIL_IDS['rohit']['name']);
        $mail->addCC(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $mail->Subject = 'Customer Refund Payment Sheet Uploaded on StanC H2H - ' . date('d/M/Y H:i:s', time());
        $mail->addAttachment($filepath);
        $body = "Howdy !! \n\n";
        $body .= "A customer refund payment sheet has been uploaded today on H2H. Find attached the csv for your information. Please login to S2B and Approve. \n\n"; 
        $body .= "Ciao !!\n";
        $body .= "Customer Payments Bot";
        $mail->Body = $body;
        $mail->send(1, false);
    } //End of sendMailRefundInitiatedCsv

}
