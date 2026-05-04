<?php
require_once(__DIR__ . '/../../../config.php');
require_once(DIR_SYSTEM . 'library/db.php');
require_once(DIR_SYSTEM . 'library/phpmailer.php');

$obj = new UpdateOldTrxn(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$obj->CheckPayment();

class UpdateOldTrxn{
    private $_db                        = null;
    private $_mail                      = null;
    private $_has_error                 = false;
    private $_seller_id                 = 0;
    public  $current_file               = '';
    public  $payment_breakup_detail     = array();
    //Source directory for .csv response files
    private $folder_to_read             = '/home/it/Documents/H2H/source'; 
    //Destination directory for .csv response files
    private $folder_to_move             = '/home/it/Documents/H2H/destination'; 
    public  $csv_key_array              = array(
                                            'Identifier'                        => 0,
                                            'Payment Type/Invoice Ref'          => 1,
                                            'Customer Ref/Inv Desc'             => 2,
                                            'Debit Account/Inv Amt'             => 3,
                                            'Payment Date/Inv Date'             => 4,
                                            'Payee Name'                        => 5,
                                            'Payee Account Number'              => 6,
                                            'IFSC Code'                         => 7,
                                            'Payment Amount'                    => 8,
                                            'Email ID'                          => 9,
                                            'Payment detail'                    => 10,
                                        );
    public $rejected_status_array       = array(
                                            'Batch Rejected by Approver',
                                            'Credit Rejected',
                                            'Credit Returned',
                                            'Debit Rejected',
                                            'Incomplete',
                                            'Partially Signed',
                                            'Rejected',
                                            'Rejected by Approver'
                                        );
    public $rejected_response           = '';
    public $reasonCount                 = 0;

    public function __construct($driver, $hostname, $username, $password, $database, $port){
        // Creating DB link object
        $this->_db = new DB($driver, $hostname, $username, $password, $database, $port);
    } //End of __construct

    /**
     * To insert data into oc_stanc_payment_csv 
     * @param string, Date
     * @return void
     * @author Nishu   
     */
    public function addPaymentCSV($filename, $date_added){
        $sql = "INSERT INTO " . DB_PREFIX . "stanc_payment_csv
                (csv_file_path, upload_success, date_added) VALUES('".$filename."',1,'".$date_added."') ";
        $this->_db->query($sql);
        return $this->_db->getLastId();
    } //End of addPaymentCSV method

    /**
     * validate amount into oc_seller_invoice
     * @param array, array
     * @return boolen
     * @author Nishu   
     */
    public function validateInvoiceData($invoice, $lead_row){
        $sql = "SELECT trxn_date_added 
                FROM ".DB_PREFIX."seller_invoice as si 
                WHERE si.seller_invoice_prefix = '".$invoice['prefix']."'
                AND si.seller_invoice_no = '".$invoice['number']."'";
        $result = $this->_db->query($sql)->row;
        $result['trxn_date_added'] = date('Y-m-d', strtotime($result['trxn_date_added']));
        if(strtotime($result['trxn_date_added']) != strtotime($lead_row[$this->csv_key_array['Payment Date/Inv Date']])){
            $this->_has_error = true;
            $this->reasonCount++;
            $this->rejected_response .= $this->reasonCount.". Transaction date(Invoice) is not matched with transaction date in .csv file : ".$this->current_file .", for Customer Ref. ='" . $lead_row[$this->csv_key_array['Customer Ref/Inv Desc']]."'\n"; 
            return false;
        }
        //Check given transaction amount is equals to amouont in DB or not
        $sql = "SELECT trxn_amount
                    FROM " . DB_PREFIX . "seller_invoice as si 
                    WHERE si.seller_invoice_prefix = '".$invoice['prefix']."'
                    AND si.seller_invoice_no = '".$invoice['number']."'";
        $result = $this->_db->query($sql)->row;
        if(abs((float)$result['trxn_amount'] - $invoice['amount']) > 0.01){
            $this->_has_error = true;
            $this->reasonCount++;
            $this->rejected_response .= $this->reasonCount.". Transaction amount(Invoice) is not matched with transaction date in .csv file : ".$this->current_file .", for Customer Ref. ='" . $lead_row[$this->csv_key_array['Customer Ref/Inv Desc']]."'\n"; 
            return false;
        }
        //Check for given transaction amount is equals to calculated amount or not
        $sql = "SELECT sum(oop.transfer_price_per_piece * oop.quantity * oop.piece_in_set) as total,si.*
                    FROM " . DB_PREFIX . "order_product as oop 
                    INNER JOIN " . DB_PREFIX . "ms_product omp ON oop.product_id = omp.product_id
                    INNER JOIN " . DB_PREFIX . "seller_invoice si ON oop.suborder_id = si.suborder_id AND omp.seller_id = si.seller_id
                    WHERE si.seller_invoice_prefix = '".$invoice['prefix']."'
                      AND si.seller_invoice_no = '".$invoice['number']."'";
        $result = $this->_db->query($sql)->row;
        if(abs((float)$result['total'] - $invoice['amount']) > 0.01){
            $this->_has_error = true;
            $this->reasonCount++;
            $this->rejected_response .= $this->reasonCount.". Transaction amount(Invoice) is not matched with transaction date in .csv file : ".$this->current_file .", Given amount is = ".$invoice['amount']." while calculate is = ".$result['total']." for Customer Ref. ='" . $lead_row[$this->csv_key_array['Customer Ref/Inv Desc']]."'\n"; 
            return false;
        }
        $this->payment_breakup_detail['breakup_details']['seller_invoice'][] = array(
                                                    'suborder_id' => $result['suborder_id'], 
                                                    'seller_invoice_no' => (int)$invoice['number'], 
                                                    'amount' => (float)$invoice['amount']
                                                    );
    } //End of validateInvoiceData method


    /**
     * validate amount into oc_seller_debit_note
     * @param array, array
     * @return boolen
     * @author Nishu   
     */
    public function validateDebitNote($invoice, $lead_row){

        $sql = "SELECT trxn_date_added 
                FROM ".DB_PREFIX."seller_debit_note as sdn 
                WHERE sdn.debit_note_prefix = '".$invoice['prefix']."'
                AND sdn.debit_note_no = '".$invoice['number']."'";
        $result = $this->_db->query($sql)->row;
        $result['trxn_date_added'] = date('Y-m-d', strtotime($result['trxn_date_added']));
        if(strtotime($result['trxn_date_added']) != strtotime($lead_row[$this->csv_key_array['Payment Date/Inv Date']])){
            $this->_has_error = true;
            $this->reasonCount++;
            $this->rejected_response .= $this->reasonCount.". Transaction date(Debit Note) is not matched with transaction date in .csv file : ".$this->current_file .", for Customer Ref. ='" . $lead_row[$this->csv_key_array['Customer Ref/Inv Desc']]."'\n"; 
            return false;
        }
        //Check given transaction amount is equals to amouont in DB or not
        $sql = "SELECT trxn_amount
                    FROM " . DB_PREFIX . "seller_debit_note as sdn 
                    WHERE sdn.debit_note_prefix = '".$invoice['prefix']."' 
                    AND sdn.debit_note_no = '".$invoice['number']."'";
        $result = $this->_db->query($sql)->row;
        if(abs((float)$result['trxn_amount'] - $invoice['amount']) > 0.01){
            $this->_has_error = true;
            $this->reasonCount++;
            $this->rejected_response .= $this->reasonCount.". Transaction amount(Debit Note) is not matched with transaction amount in .csv file : ".$this->current_file .", for Customer Ref. ='" . $lead_row[$this->csv_key_array['Customer Ref/Inv Desc']]."'\n"; 
            return false;
        }
        //Check for given transaction amount is equals to calculated amount or not
        $sql = "SELECT sum((-1) * oop.transfer_price_per_piece * ocr.quantity) as total, 
                    osdn.*
                FROM " . DB_PREFIX . "return ocr 
                INNER JOIN " . DB_PREFIX . "order_product oop ON ocr.order_product_id = oop.order_product_id
                INNER JOIN " . DB_PREFIX . "seller_debit_note osdn ON osdn.debit_note_id = ocr.debit_note_id
                WHERE osdn.debit_note_no = '". (int)$invoice['number']. "'
                 AND osdn.debit_note_prefix = '".$invoice['prefix']."'";
        $result = $this->_db->query($sql)->row;
        if(abs((float)$result['total'] - $invoice['amount']) > 0.01){
            $this->_has_error = true;
            $this->reasonCount++;
            $this->rejected_response .= $this->reasonCount.". Calculated transaction amount(Debit Note) is not matched with transaction amount in .csv file : ".$this->current_file .", calculate amount = ".(float)$result['total']." and amount in .csv = ".$invoice['amount']." for Customer Ref. ='" . $lead_row[$this->csv_key_array['Customer Ref/Inv Desc']]."'\n"; 
            return false;
        }
        $this->payment_breakup_detail['breakup_details']['seller_debit_note'][] = array(
                                                    'suborder_id' => $result['suborder_id'], 
                                                    'debit_note_no' => (int)$result['debit_note_no'], 
                                                    'amount' => (float)$invoice['amount']
                                                    );
    } //End of validateDebitNote method

    /**
     * Fetch Seller ID
     * @param array
     * @return $seller_id integer
     * @author Nishu   
     */
    public function getSellerId($invoice){
        $sql = "SELECT seller_id 
                FROM ".DB_PREFIX."seller_invoice as si 
                WHERE si.seller_invoice_prefix = '".$invoice['prefix']."'
                AND si.seller_invoice_no = '".$invoice['number']."'";
        $result = $this->_db->query($sql)->row;
        if(count($result) != 0){
            return $result['seller_id'];
        }else{
            return 0;
        }
    }

    /**
     * Insert into payment_breakup
     * @param array
     * @return void
     * @author Nishu   
     */
    public function insertPaymentBreakup($data){
        // First check for already existance
        $sql = "SELECT * FROM " . DB_PREFIX . "stanc_payment_breakup 
                WHERE payment_ref_no = '".$data['payment_ref_no']."'";
        $query = $this->_db->query($sql);
        if ($query->num_rows <= 0){
             $sql = "INSERT INTO " . DB_PREFIX . "stanc_payment_breakup 
                    (payment_csv_id, seller_id, payment_ref_no, payee_name, payee_account_no, payee_ifsc_code, payee_email, payee_amount, breakup_details) 
                    VALUES( ".
                    $data['payment_csv_id'].",". 
                    $data['seller_id'].",'". 
                    $data['payment_ref_no']."','".  
                    $data['payee_name']."','". 
                    $data['payee_account_no']."','".  
                    $data['payee_ifsc_code']."','". 
                    $data['payee_email']."','". 
                    $data['payee_amount']."','". 
                    $data['breakup_details']."'
                    )
                    ";
                    $this->_db->query($sql);
        }else{
            $this->_has_error = true;
            $this->reasonCount++;
            $this->rejected_response .= $this->reasonCount.". Data already existed in payment_breakup table for same customer ref= ".$data['payment_ref_no']." in .csv file : '".$this->current_file ."'\n"; 
        }
    }
  
    /**
     * Method to check payment status
     * @param void
     * @return void
     * @author Nishu   
     */
    public function CheckPayment(){
        //Check given source directory is existed or not
        if(!file_exists($this->folder_to_read)){
            exit;
        }
        $files = glob("$this->folder_to_read/*.csv"); //Get all .csv files from source folder
        if (empty($files)) { //Will exit, if any .csv file is not exist
            exit;
        }
        foreach ($files as $file) { //Looping for all .csv file in source folder
            $fileData               = fopen($file, "r"); //Get .csv file's object
            $this->current_file     = $file;
            $row                    = 0;
            $lead_row               = array();
            //Get formated date from filename
            $data                   = strrev(substr(strrev($file), 4));
            $data                   = substr($data, strrpos($data, '-')+1);
            $day                    = substr($data, 0, 2);
            $month                  = substr($data, 2,2);
            $year                   = '20'.substr($data, 4);
            $date                   = $day."-".$month."-".$year;
            $trxn_date              = date('Y-m-d', strtotime($date));
           
            //Add csv payments into oc_stanc_payment_csv db table
            $payment_csv_id         = $this->addPaymentCSV($file, $trxn_date);

            while($rowData = fgetcsv($fileData)) { //Itterating for file data row-wise
                if($row == 0){
                    $row++;
                    continue; //Skiping header row of .csv file
                }
                if($rowData[$this->csv_key_array['Identifier']] == 'P'){

                    if(!$this->_has_error && !empty($this->payment_breakup_detail)){
                        $this->payment_breakup_detail['payment_csv_id']     = $payment_csv_id;
                        $this->payment_breakup_detail['seller_id']          = $this->_seller_id;
                        $this->payment_breakup_detail['payment_ref_no']     = $lead_row[$this->csv_key_array['Customer Ref/Inv Desc']];
                        $this->payment_breakup_detail['payee_name']         = $lead_row[$this->csv_key_array['Payee Name']];
                        $this->payment_breakup_detail['payee_account_no']   = $lead_row[$this->csv_key_array['Payee Account Number']];
                        $this->payment_breakup_detail['payee_ifsc_code']    = $lead_row[$this->csv_key_array['IFSC Code']];
                        $this->payment_breakup_detail['payee_email']        = $lead_row[$this->csv_key_array['Email ID']];
                        $this->payment_breakup_detail['payee_amount']       = $lead_row[$this->csv_key_array['Payment Amount']];
                        $this->payment_breakup_detail['breakup_details']    = serialize($this->payment_breakup_detail['breakup_details']);
                        //Insert into payment breakup table
                        $this->insertPaymentBreakup($this->payment_breakup_detail);
                    }
                    unset($this->payment_breakup_detail);
                    $this->_has_error   = false;
                    $lead_row           = $rowData;
                    $lead_row[$this->csv_key_array['Payment Date/Inv Date']] = $trxn_date;
                    $this->_seller_id   = 0;
                    continue;
                }
                //Check, Is there any error for current transaction, If yes then continue.
                if($this->_has_error == true){ 
                    continue;
                }
                $invoive        = array();
                $invoiceData    = $rowData[$this->csv_key_array['Customer Ref/Inv Desc']];
                //Invoice details calculation
                $colon_index        = strpos($invoiceData, ':');
                $invoice['type']    = substr($invoiceData, 0, $colon_index);
                $invoiceData        = trim(substr($invoiceData, $colon_index+1));
                $invoice['number']  = substr($invoiceData, strrpos($invoiceData, '_')+1);
                $invoiceData        = strrev($invoiceData);
                $invoiceData        = substr($invoiceData, strpos($invoiceData, '_'));
                $invoice['prefix']  = strrev($invoiceData);
                $invoice['amount']  = (float)str_replace(',', '', $rowData[$this->csv_key_array['Debit Account/Inv Amt']]);
                //Get seller ID
                if($this->_seller_id == 0){
                    $this->_seller_id = $this->getSellerId($invoice);    
                }
                //Validate payments
                if(strtolower($invoice['type']) == 'invoice'){
                    $valid = $this->validateInvoiceData($invoice, $lead_row);
                    if($valid == false){
                        continue;
                    }
                }else if(strtolower($invoice['type']) == 'debit note'){
                    $valid = $this->validateDebitNote($invoice, $lead_row);
                    if($valid == false){
                        continue;
                    }
                }
                $row++;
            }// End of while
            if(file_exists($this->folder_to_move)){
                //Move file from source to destination directory 
                system('mv '.$file.' '.$this->folder_to_move);
            } //End of if
        }// End of foreach
        //Show list of rejections
        print_r($this->rejected_response);
    } //End of CheckPayment method
} //End of Class
