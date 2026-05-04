<?php
require_once(__DIR__ . '/../../../config.php');
require_once(DIR_SYSTEM . 'library/db.php');
require_once(DIR_SYSTEM . 'library/phpmailer.php');

$bank_response = new ReadStancResponse(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

try {
    $bank_response->checkPayment();
} catch (Exception $e) {
    $bank_response->sendMailIfError($e);
    exit();
}

class ReadStancResponse{
    private $_db                = null;
    private $_mail              = null;
    
    //Source directory for .csv response files
    private $folder_to_read     = '/opt/standard-chartered/host2host/documents/reportscust/in/txn/'; 
    //Destination directory for .csv response files
    private $folder_to_move     = '/var/payment-sheet/payment_response/';

    private $failed_statuses = array(
                                     'Batch Rejected by Approver',
                                     'Credit Rejected',
                                     'Credit Returned',
                                     'Debit Rejected',
                                     'Incomplete',
                                     'Partially Signed',
                                     'Rejected',
                                     'Rejected by Approver'
                                    );
                                    
    private $response_errors = array();
    private $response_errors_temp = array();
    
    private $file_being_read = '';
    private $file_number = 0;
    private $invoices_updated = 0;
    private $sor_invoices_updated = 0;
    private $wsb_purchase_invoices_updated = 0;
    private $debit_notes_updated = 0;
    private $wsb_purchase_return_debit_notes_updated = 0;
    private $excess_pay_refund_updated     = 0;
    private $credit_note_refund_updated    = 0;

    public function __construct($driver, $hostname, $username, $password, $database, $port){
        // Creating DB link object
        $this->_db = new DB($driver, $hostname, $username, $password, $database, $port);
        $this->_db->useMasterDbOnly();
        
        // Creating PHPMailer object for usage across various stages in this code
        $this->prepareMailerObject();
    } //End of __construct

    /**
     * To get Payment details given customer refrence number
     * @param $cust_ref string
     * @return array
     * @author Nishu   
     */
    public function getPaymentDetails($cust_ref){
        
        $result = array();
        
        if (!empty($cust_ref)) {
            $sql = "SELECT * 
                    FROM " . DB_PREFIX . "stanc_payment_breakup
                    WHERE payment_ref_no =  '" . $this->_db->escape($cust_ref) . "' 
                    LIMIT 1 ";
            $query = $this->_db->query($sql);
            if ($query->num_rows) {
                $result = $query->row;
            }
        }
        return $result;
        
    } //End of getPaymentDetails method


    /**
     * Validate Payment Basics such as Debit Amount, Bank Account No, IFSC code etc, 
     * with the ones already stored in DB, against the CSV response
     * @param array, array
     * @return boolean
     * @author Nishu   
     */
    private function validatePaymentBasics($row_data, $payment_details){
		
		$validation_result = true; // Initializing
        
        // Debit Amount
        if( abs($row_data['payee_amount'] - (float)$payment_details['payee_amount']) > 0.01 ) {
            $this->response_errors_temp[] = 'P - Debit Amount Mismatch';
            $validation_result = false;
        }
        
        // Payee Account No  - There is a possibility of BOM character getting stored in DB. 
        // We use preg_replace to get rid of BOM characters before comparing.
        $payee_account_no = strtoupper(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $payment_details['payee_account_no'])));
        if( $row_data['payee_account_no'] !== $payee_account_no ){
			$this->response_errors_temp[] = 'P - Account No Mismatch';
            $validation_result = false;
        }
        
        // IFSC Code  - There is a possibility of BOM character getting stored in DB. 
        // We use preg_replace to get rid of BOM characters before comparing.
        $payee_ifsc_code = strtoupper(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $payment_details['payee_ifsc_code'])));
        if( $row_data['payee_ifsc_code'] !== $payee_ifsc_code ){
			$this->response_errors_temp[] = 'P - IFSC Code Mismatch';
            $validation_result = false;
        }
        
        return $validation_result;
    } //End of validatePaymentBasics method

    /**
     * To update trxn details into seller_invoice db table
     * Currently, if transaction is in FAILURE statuses, it does not do anything.
     * @param $row_data array, $seller_id, $invoice_number, $suborder_id
     * @return void
     * @author Nishu   
     */
    private function updateSellerInvoice($row_data, $seller_id, $invoice_id, $suborder_id){
		if ($row_data['trxn_failed'])
			return false;
		
        $sql = "UPDATE " . DB_PREFIX . "seller_invoice
                SET 
                trxn_utr_internal = '" . $this->_db->escape($row_data['trxn_utr_internal']) . "', 
                trxn_utr          = '" . $this->_db->escape($row_data['trxn_utr']) . "', 
                trxn_utr_date     = '" . $this->_db->escape($row_data['trxn_utr_date']) . "', 
                trxn_done         = '" . $this->_db->escape($row_data['trxn_done']) . "', 
                trxn_bank         = '" . $this->_db->escape($row_data['trxn_bank']) . "', 
                trxn_response     = '" . $this->_db->escape($row_data['trxn_response']) . "'  
                
                WHERE seller_id         = '" . (int)$seller_id . "' 
                  AND seller_invoice_id = '" . (int)$invoice_id . "' 
                  AND suborder_id       = '" . $this->_db->escape($suborder_id) . "'";
        $this->_db->query($sql); //Execute DB query
        $this->invoices_updated++;
        return true;
    } //End of updateSellerInvoice method
    
    /**
     * To update trxn details into wsb_sor_payment db table
     * Currently, if transaction is in FAILURE statuses, it does not do anything.
     * @param $row_data array, $seller_id, $sor_payment_id, $suborder_id
     * @return void
     * @author Madhur   
     */
    private function updateSorInvoice($row_data, $seller_id, $sor_payment_id, $suborder_id){
		if ($row_data['trxn_failed'])
			return false;
		
        $sql = "UPDATE " . DB_PREFIX . "wsb_sor_payment 
                SET 
                trxn_utr_internal = '" . $this->_db->escape($row_data['trxn_utr_internal']) . "', 
                trxn_utr          = '" . $this->_db->escape($row_data['trxn_utr']) . "', 
                trxn_utr_date     = '" . $this->_db->escape($row_data['trxn_utr_date']) . "', 
                trxn_done         = '" . $this->_db->escape($row_data['trxn_done']) . "', 
                trxn_bank         = '" . $this->_db->escape($row_data['trxn_bank']) . "', 
                trxn_response     = '" . $this->_db->escape($row_data['trxn_response']) . "'  
                
                WHERE seller_id         = '" . (int)$seller_id . "' 
                  AND sor_payment_id    = '" . (int)$sor_payment_id . "' 
                  AND suborder_id       = '" . $this->_db->escape($suborder_id) . "'";
        $this->_db->query($sql); //Execute DB query
        $this->sor_invoices_updated++;
        return true;
    } //End of updateSorInvoice method

    /**
     * To update trxn details into seller_debit_note db table
     * Currently, if transaction is in FAILURE statuses, it does not do anything.
     * @param $row_data array, $seller_id, $debit_note_no, $suborder_id, $debit_note_id
     * @return void
     * @author Nishu   
     */
    private function updateDebitNote($row_data, $seller_id, $debit_note_no, $suborder_id, $debit_note_id){
		if ($row_data['trxn_failed'])
			return false;
		
        $sql = "UPDATE " . DB_PREFIX . "seller_debit_note
                SET 
                trxn_utr_internal   = '" . $this->_db->escape($row_data['trxn_utr_internal']) . "', 
                trxn_utr            = '" . $this->_db->escape($row_data['trxn_utr']) . "', 
                trxn_utr_date       = '" . $this->_db->escape($row_data['trxn_utr_date']) . "', 
                trxn_done           = '" . $this->_db->escape($row_data['trxn_done']) . "', 
                trxn_bank           = '" . $this->_db->escape($row_data['trxn_bank']) . "', 
                trxn_response       = '" . $this->_db->escape($row_data['trxn_response']) . "' 
                
                WHERE seller_id     = '" . (int)$seller_id . "' 
                  AND debit_note_no = '" . (int)$debit_note_no . "' 
                  AND suborder_id   = '" . $this->_db->escape($suborder_id) . "' ";
        if($debit_note_id > 0){
            $sql .= " AND debit_note_id = '" . (int)$debit_note_id . "' ";
        }
        $this->_db->query($sql); //Execute DB query
        $this->debit_notes_updated++;
        return true;
    } //End of updateDebitNote method

    /**
     * To update trxn details to customer refund
     * @param $row_data Array, $refund_data Array
     * @return void
     * @author Nishu, Nov 2017
     */
    private function updateRefundTrxnDetails($row_data, $refund_data){
		
		// Updating trxn_details table 
        $sql = "UPDATE " . DB_PREFIX . "trxn_details
                SET 
                trxn_utr_internal   = '" . $this->_db->escape($row_data['trxn_utr_internal']) . "', 
                trxn_utr            = '" . $this->_db->escape($row_data['trxn_utr']) . "', 
                trxn_utr_date       = '" . $this->_db->escape($row_data['trxn_utr_date']) . "', 
                trxn_done           = '" . $this->_db->escape($row_data['trxn_done']) . "', 
                trxn_bank           = '" . $this->_db->escape($row_data['trxn_bank']) . "', 
                trxn_response       = '" . $this->_db->escape($row_data['trxn_response']) . "' 
                WHERE 
                  id = " . (int)$refund_data['trxn_id'];
        $this->_db->query($sql); //Execute DB query 
        
        // Data for order_payment table
        $successfull = ($row_data['trxn_failed'] ? 0 : 1);
        $txn_status = ($row_data['trxn_failed'] ? 'REFUND FAILED' : (empty($row_data['trxn_utr']) ? 'REFUND PROCESSED' : 'REFUND SUCCESS'));
        $reference = trim($refund_data['ref'] . ' - ' . $row_data['payment_status'] . '. ' . $row_data['payment_status_message']);
        
        // Updating order_payment table 
        $sql = "UPDATE " . DB_PREFIX . "order_payment 
                SET 
                merchant_txn_id     = '" . $this->_db->escape($row_data['trxn_utr']) . "', 
                txn_date_time       = '" . $this->_db->escape($row_data['trxn_utr_date']) . "', 
                txn_status          = '" . $this->_db->escape($txn_status) . "', 
                successfull         = '" . (int)$successfull . "',  
                reference           = '" . $this->_db->escape($reference) . "', 
                json_format         = '" . $this->_db->escape($row_data['trxn_response']) . "' 
                WHERE 
                  payment_id = " . (int)$refund_data['order_payment_id'];
        $this->_db->query($sql); //Execute DB query 

        if($refund_data['type'] == "EXCESS_PAYMENT_BY_CUSTOMER"){
			$this->excess_pay_refund_updated++;
			
			// If it is failed transaction, we also open the Order for recalculation of Excess Payment related refund
			if ($row_data['trxn_failed']) {
				$sql = "UPDATE " . DB_PREFIX . "order 
                        SET payment_cleared = 'NO'  
                        WHERE order_id = '" . (int)$refund_data['order_id'] . "'";
                $this->_db->query($sql); //Execute DB query 
			}
          
        }else{
          
            $this->credit_note_refund_updated++;

            // If it is failed transaction, we also open the Credit Note for recalculation of tentative refund
            if ($row_data['trxn_failed']) {
                $sql = "UPDATE " . DB_PREFIX . "credit_note  
                       SET payment_cleared = 'NO'  
                       WHERE credit_note_id = '" . (int)$refund_data['ref_id'] . "'";
               $this->_db->query($sql); //Execute DB query 
            }
        }

    
        return true;
    } //End of updateRefundTrxnDetails method
    
    
    /**
     * To update oc_trxn_details
     * @param $row_data Array, $breakup_data Array
     * @return void
     * @author Madhur, May 2018
     */
    private function updateTrxnDetails($row_data, $breakup_data){
		
		// Updating trxn_details table 
        $sql = "UPDATE " . DB_PREFIX . "trxn_details
                SET 
                trxn_utr_internal   = '" . $this->_db->escape($row_data['trxn_utr_internal']) . "', 
                trxn_utr            = '" . $this->_db->escape($row_data['trxn_utr']) . "', 
                trxn_utr_date       = '" . $this->_db->escape($row_data['trxn_utr_date']) . "', 
                trxn_done           = '" . $this->_db->escape($row_data['trxn_done']) . "', 
                trxn_bank           = '" . $this->_db->escape($row_data['trxn_bank']) . "', 
                trxn_response       = '" . $this->_db->escape($row_data['trxn_response']) . "' 
                WHERE 
                  id = " . (int)$breakup_data['trxn_id'];
        return $this->_db->query($sql); //Execute DB query 
    } //End of updateTrxnDetails method


    /**
     * Validate and Update Payment Breakup details. 
     * If transaction is in FAILURE statuses, it will set Customer Refunds to REFUND FAILED, 
     * and for Excess Payment case, it will reopen the Order for recalculations of excess payment related refunds.
     * For now, Seller payments are not updated, if transaction is in FAILURE statuses
     * @param $row_data array, $breakup_details array
     * @return void
     * @author Nishu   
     */
    public function validateAndUpdatePaymentBreakup($row_data, $breakup_details){

        // If it is a customer refund, then Validating Customer Refund amount
        if(!empty($breakup_details['refund'])){
			
			// key data for the corresponding refund entry
			$refund_data = $breakup_details['refund'];
			
			// We dont validate by calculations - instead we validate that breakup_details 
			// with various ids and amount etc, should return one and only one row from trxn_details 
			// table and order_payment table 
			$sql = "SELECT otd.id, oop.payment_id 
			        FROM " . DB_PREFIX . "trxn_details otd 
			        INNER JOIN " . DB_PREFIX . "order_payment oop ON oop.trxn_id = otd.id 
			        WHERE otd.id              = '" . (int)$refund_data['trxn_id'] . "' 
			          AND otd.trxn_for_id     = '" . (int)$refund_data['ref_id'] . "' 
			          AND otd.trxn_for        = '" . $this->_db->escape($refund_data['type']) . "' 
			          AND otd.trxn_bank       = 'stanc' 
			          AND oop.payment_id      = '" . (int)$refund_data['order_payment_id'] . "' 
			          AND oop.order_id        = '" . (int)$refund_data['order_id'] . "' 
			          AND oop.payment_gateway = 'bank_transfer' 
			          AND ABS(otd.trxn_amount - " . (float)$refund_data['amount'] . ") <= 0.01 
			          AND ABS(ABS(oop.amount) - " . (float)$refund_data['amount'] . ") <= 0.01 ";
            $result = $this->_db->query($sql);
            
            // If no row found - we have a refund without having trxn recorded in DB - serious error
            if ($result->num_rows == 0) {
				$this->response_errors_temp[] = "I - Customer Refund - Trxn Record NOT FOUND, trxn_id: " . $refund_data['trxn_id'] . ", order_payment_id: " . $refund_data['order_payment_id'] . ", amount: " . (float)$refund_data['amount']; 
				
            } elseif ($result->num_rows > 1) { // Multiple rows found - another serious error 
                $this->response_errors_temp[] = "I - Customer Refund - MULTIPLE Trxn Records found, trxn_id: " . $refund_data['trxn_id'] . ", order_payment_id: " . $refund_data['order_payment_id'] . ", amount: " . (float)$refund_data['amount'];

			} else { // One record found to update - Correct - we must update it now
				$this->updateRefundTrxnDetails($row_data, $refund_data);
			}
		}
		
		// Validating Transactions of WSB Purchase Return Debit Note
        if(!empty($breakup_details['wsb_purchase_return'])){
			foreach ($breakup_details['wsb_purchase_return'] as $wsb_purchase_return_debit_note) {
			
				// We dont validate by calculations - instead we validate that breakup_details 
				// with various ids and amount etc, should return one and only one row from trxn_details table
				$sql = "SELECT otd.id, owpr.debit_note_id 
						FROM " . DB_PREFIX . "trxn_details otd 
						INNER JOIN " . DB_PREFIX . "wsb_purchase_return owpr ON owpr.debit_note_id = otd.trxn_for_id  
						WHERE otd.id              = '" . (int)$wsb_purchase_return_debit_note['trxn_id'] . "' 
						  AND otd.trxn_for_id     = '" . (int)$wsb_purchase_return_debit_note['trxn_for_id'] . "' 
						  AND otd.trxn_for        = 'WSB_PURCHASE_RETURN' 
			              AND otd.trxn_bank       = 'stanc' 
			              AND ABS(otd.trxn_amount - " . (float)$wsb_purchase_return_debit_note['amount'] . ") < 0.01 ";
				$result = $this->_db->query($sql);
            
				// If no row found - we have a transaction without having trxn recorded in DB - serious error
				if ($result->num_rows == 0) {
					$this->response_errors_temp[] = "I - WSB_PURCHASE_RETURN - Trxn Record NOT FOUND" . 
					                                ", trxn_id: " . $wsb_purchase_return_debit_note['trxn_id'] . 
					                                ", trxn_for_id: " . $wsb_purchase_return_debit_note['trxn_for_id'] . 
					                                ", amount: " . (float)$wsb_purchase_return_debit_note['amount']; 
				
				} else { // One record found to update - Correct - we must update it now
					// we cant have more than one records, as 'id' field is primary key in oc_trxn_details
					if ( $this->updateTrxnDetails($row_data, $wsb_purchase_return_debit_note) ) {
						$this->wsb_purchase_return_debit_notes_updated++;
					}
				}
			}
		}
		
		
		// Validating Transactions of WSB Purchase Invoice
        if(!empty($breakup_details['wsb_purchase'])){
			foreach ($breakup_details['wsb_purchase'] as $wsb_purchase_invoice) {
			
				// We dont validate by calculations - instead we validate that breakup_details 
				// with various ids and amount etc, should return one and only one row from trxn_details table
				$sql = "SELECT otd.id, owp.purchase_id 
						FROM " . DB_PREFIX . "trxn_details otd 
						INNER JOIN " . DB_PREFIX . "wsb_purchase owp ON owp.purchase_id = otd.trxn_for_id  
						WHERE otd.id              = '" . (int)$wsb_purchase_invoice['trxn_id'] . "' 
						  AND otd.trxn_for_id     = '" . (int)$wsb_purchase_invoice['trxn_for_id'] . "' 
						  AND otd.trxn_for        = 'WSB_PURCHASE' 
			              AND otd.trxn_bank       = 'stanc' 
			              AND ABS(otd.trxn_amount - " . (float)$wsb_purchase_invoice['amount'] . ") < 0.01 ";
				$result = $this->_db->query($sql);
            
				// If no row found - we have a transaction without having trxn recorded in DB - serious error
				if ($result->num_rows == 0) {
					$this->response_errors_temp[] = "I - WSB_PURCHASE - Trxn Record NOT FOUND" . 
					                                ", trxn_id: " . $wsb_purchase_invoice['trxn_id'] . 
					                                ", trxn_for_id: " . $wsb_purchase_invoice['trxn_for_id'] . 
					                                ", amount: " . (float)$wsb_purchase_invoice['amount']; 
				
				} else { // One record found to update - Correct - we must update it now
					// we cant have more than one records, as 'id' field is primary key in oc_trxn_details
					if ( $this->updateTrxnDetails($row_data, $wsb_purchase_invoice) ) {
						$this->wsb_purchase_invoices_updated++;
					}
				}
			}
		}
		
        
        //Validating Seller Invoice amount
        if(!empty($breakup_details['seller_invoice'])){
             foreach ($breakup_details['seller_invoice'] as $seller_invoice) {
				 
                $sql = "SELECT sum(oop.transfer_price_per_piece * oop.quantity * oop.piece_in_set) as total
                        FROM " . DB_PREFIX . "order_product as oop 
                        WHERE oop.seller_id = '". (int)$breakup_details['seller_id']."'
                          AND oop.suborder_id = '". $this->_db->escape($seller_invoice['suborder_id']) ."' 
                          AND oop.seller_invoice_id = '" . (int)$seller_invoice['seller_invoice_id'] . "'";

                $result     = $this->_db->query($sql)->row;
                $total      = (float)$result['total'];
               
                if( abs($total - $seller_invoice['amount']) > 0.01 ){
                    $this->response_errors_temp[] = "I - Seller Invoice - Amount Mismatch, Earlier: " . $seller_invoice['amount'] . ", Now: " . $total . ", suborder_id: " . $seller_invoice['suborder_id'] . ", seller_invoice_id: " . $seller_invoice['seller_invoice_id'];
                }//End of if
                
                //Update txn details into seller_invoice db table 
                $this->updateSellerInvoice($row_data, 
                                           $breakup_details['seller_id'], 
                                           $seller_invoice['seller_invoice_id'], 
                                           $seller_invoice['suborder_id']);
            }//End of foreach
        }//End of if
        
        
        //Validating SOR Invoice amount
        if(!empty($breakup_details['sor_invoice'])){
             foreach ($breakup_details['sor_invoice'] as $sor_invoice) {
				 
                $sql = "SELECT sum(oop.transfer_price_per_piece * oop.quantity * oop.piece_in_set) as total
                        FROM " . DB_PREFIX . "order_product as oop 
                        INNER JOIN " . DB_PREFIX . "wsb_sor_payment as owsp ON owsp.sor_payment_id = oop.sor_payment_id 
                        WHERE owsp.seller_id = '". (int)$breakup_details['seller_id']."'
                          AND oop.suborder_id = '". $this->_db->escape($sor_invoice['suborder_id']) ."' 
                          AND oop.sor_payment_id = '" . (int)$sor_invoice['sor_payment_id'] . "' 
                          AND oop.wsb_purchase_id = owsp.wsb_purchase_id";

                $result     = $this->_db->query($sql)->row;
                $total      = (float)$result['total'];
               
                if( abs($total - $sor_invoice['amount']) > 0.01 ){
                    $this->response_errors_temp[] = "I - SOR Invoice - Amount Mismatch, Earlier: " . $sor_invoice['amount'] . ", Now: " . $total . ", suborder_id: " . $sor_invoice['suborder_id'] . ", sor_payment_id: " . $sor_invoice['sor_payment_id'];
                }//End of if
                
                //Update SOR Invoice txn details into wsb_sor_payment db table
                $this->updateSorInvoice($row_data, 
                                        $breakup_details['seller_id'], 
                                        $sor_invoice['sor_payment_id'], 
                                        $sor_invoice['suborder_id']);
            }//End of foreach

        }//End of if
        
        
        //Validating Debit note amount
        if(!empty($breakup_details['seller_debit_note'])){
            foreach ($breakup_details['seller_debit_note'] as $seller_debit_note) {
				
                $debit_note_id = 0;
                $sql = "SELECT sum(oop.transfer_price_per_piece * ocr.quantity) as total
                        FROM " . DB_PREFIX . "seller_debit_note osdn
                        INNER JOIN " . DB_PREFIX . "return ocr ON osdn.debit_note_id = ocr.debit_note_id 
                        INNER JOIN " . DB_PREFIX . "order_product oop ON ocr.order_product_id = oop.order_product_id
                        WHERE osdn.debit_note_no = '" . (int)$seller_debit_note['debit_note_no']. "'
                          AND osdn.suborder_id   = '" . $this->_db->escape($seller_debit_note['suborder_id']) . "' 
                          AND osdn.seller_id     = '" . (int)$breakup_details['seller_id'] . "' 
                          AND oop.seller_id      = '" . (int)$breakup_details['seller_id'] . "' "; 
                         
                if(!empty($seller_debit_note['debit_note_id'])){
                    $sql .= " AND osdn.debit_note_id = '". (int)$seller_debit_note['debit_note_id']. "' ";
                    $debit_note_id = (int)$seller_debit_note['debit_note_id'];
                }
                
                $result     = $this->_db->query($sql)->row;
                $total      = (-1)*(float)$result['total'];
                
                if( abs($total - $seller_debit_note['amount']) > 0.01 ){
					$this->response_errors_temp[] = "I - Seller Debit Note - Amount Mismatch, Earlier: " . $seller_debit_note['amount'] . ", Now: " . $total . ", suborder_id: " . $seller_debit_note['suborder_id'] . ", Debit Note No: " . $seller_debit_note['debit_note_no'];
                }//End of if
                
                //Update trxn details into seller_debit_note db table
                $this->updateDebitNote($row_data, 
                                       $breakup_details['seller_id'], 
                                       $seller_debit_note['debit_note_no'], 
                                       $seller_debit_note['suborder_id'], 
                                       $debit_note_id);
            }//End of foreach
        }
        
        
    } //End of validateAndUpdatePaymentBreakup method

    /**
     * Sending mail to report Payments with Errors Response / Validation Issues
     * @param void
     * @return void
     * @author Nishu   
     */
    private function sendMailForResponseErrors(){
      
      if ( !empty($this->response_errors) ) {
        // Mail Internally
        $this->prepareMailerObject();
        $this->_mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $this->_mail->Subject = 'Errors in Payments Response - ' . date("F j, Y, H:i:s");
        $body = "Howdy !! \n\n";
        $body .= "Madhur: Following transactions have Errors in Response, or Validation Issues. Please check. \n\n";
        
        foreach ($this->response_errors as $response) {
            $body .= implode(', ', $response['row_data']) . " \n\n";
            $i = 1;
            foreach($response['error_messages'] as $message) {
				$body .= $i . ': ' . $message . " \n";
				$i++;
			}
			$body .= " \n";
			$body .= "-------------------------------------------------------------------------------------- \n\n";
        }
        $body .= "Ciao !!\n";
        $body .= "Payments Bot";
        $this->_mail->Body = $body;
        $this->_mail->send(1,false);
      }
      
      // Empty out the array for next csv file being processed
      $this->response_errors = array();
      
    } //End of sendMailForResponseErrors
    
    /**
     * Sending mail after a response file is read, with updation numbers
     * @param void
     * @return void
     * @author Madhur   
     */
    private function sendMailAfterResponseFileIsRead($rows_read){
        $this->prepareMailerObject();
        $this->_mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $this->_mail->Subject = 'Stanc Response File# ' . $this->file_number . ' Read - ' . date("F j, Y, H:i:s");
        $body = "Howdy !! \n\n";
        $body .= "Response File read:  " . $this->file_being_read . " \n\n";;
        $body .= "Rows read:  " . $rows_read. " \n\n";;
        $body .= "Seller Invoice(s) Updated: " . $this->invoices_updated . " \n\n";
        $body .= "SOR Invoice(s) Updated: " . $this->sor_invoices_updated . " \n\n";
        $body .= "WSB Purchase Invoice(s) Updated: " . $this->wsb_purchase_invoices_updated . " \n\n";
        $body .= "Debit Note(s) Updated: " . $this->debit_notes_updated . " \n\n";
        $body .= "WSB Purchase Return Debit Note(s) Updated: " . $this->wsb_purchase_return_debit_notes_updated . " \n\n";
        $body .= "Customer Refund for Excess Payment(s) Updated: " . $this->excess_pay_refund_updated . " \n\n";
        $body .= "Customer Refund for Credit Note(s) Updated: " . $this->credit_note_refund_updated . " \n\n";
        $body .= "\n\n";
        $body .= "Ciao !!\n";
        $body .= "Payments Bot";
        $this->_mail->Body = $body;
        $this->_mail->send(1,false);
    } //End of sendMailAfterResponseFileIsRead
    
    
    /**
     * Sending mail if an error is caught
     * @param void
     * @return void
     * @author Madhur   
     */
    public function sendMailIfError($e){
        $this->prepareMailerObject();
        $this->_mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $this->_mail->Subject = 'ALERT: Error While Reading StanC Payment Response - ' . date("F j, Y, H:i:s");
        $body = "Howdy !! \n\n";
        $body .= "Error caught while reading:  " . $this->file_being_read . " \n\n";
        $body .= $e->getMessage();
        $body .= "\n\n";
        $body .= var_dump($e);
        $body .= "\n\n";
        $body .= "Ciao !!\n";
        $body .= "Payments Bot";
        $this->_mail->Body = $body;
        $this->_mail->send(1,false);
    } //End of sendMailIfError

    /**
     * Sending mail if there is no file found
     * @param void
     * @return void
     * @author Madhur   
     */
    private function sendMailForNoFilesToRead(){
        // Mail Internally
        $this->prepareMailerObject();
        $this->_mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
        $this->_mail->Subject = 'No Stanc Payment Response File to Read - ' . date("F j, Y, H:i:s");
        $body = "Howdy !! \n\n";
        $body .= "No payment response file was found on " . date("F j, Y, g:i a") . " in " . $this->folder_to_read;
        $body .= "\n\n";
        $body .= "Ciao !! \n";
        $body .= "Payments Bot";
        $this->_mail->Body = $body;
        $this->_mail->send(1,false);
        return;
    } //End of sendMailForNoFilesToRead
    
    
    /**
     * Method to check payment status
     * @param void
     * @return void
     * @author Nishu   
     */
    public function checkPayment(){
        //Check given source directory is existed or not
        if(!file_exists($this->folder_to_read)){
            exit;
        }
        $files = glob($this->folder_to_read . "*.csv"); //Get all .csv files from source folder
        
        if (empty($files)) { //Will exit, if any .csv file is not exist
            $this->sendMailForNoFilesToRead();
            exit;
        }
        foreach ($files as $file) { //Looping for all .csv file in source folder
            $this->file_being_read = $file;
            $this->file_number++;
            $this->invoices_updated = 0;
            $this->sor_invoices_updated = 0;
            $this->wsb_purchase_invoices_updated = 0;
            $this->debit_notes_updated = 0;
            $this->wsb_purchase_return_debit_notes_updated = 0;
            $this->excess_pay_refund_updated = 0;
            $this->credit_note_refund_updated = 0;
            
            $file_data = fopen($file, "r"); //Open .csv file
            
            $row = 0;
            while($raw_row_data = fgetcsv($file_data)) { //Iterating for file data row-wise
				$row++;
                if($row == 1){
                    continue; //Skiping header row of .csv file
                }
                
                
                // Getting processed Row Data
                $row_data = $this->prepareRowData($raw_row_data);
                
                //Get payment details based on Customer Reference (payment_ref_no)
                $payment_details = $this->getPaymentDetails($row_data['payment_ref_no']);
                
                // If we receive non-empty $payment_details, it means payment is done via system
                if(!empty($payment_details)){
					
					//If transaction status is in one of the FAILURE statuses - fill out an error message to mail
                    if($row_data['trxn_failed']){
						$this->response_errors_temp[] = 'P - Transaction in FAILED status';
                    }
                    
                    // Checksum for basic payment items such as bank account no, IFSC code, amount etc
                    // Currently even if validation fails, we continue to update transaction, as per the status received in CSV file
                    // Then, we report it internally - to investigate further
                    $basic_validation = $this->validatePaymentBasics($row_data, $payment_details);
                    
                    // Validating Payment Breakup (I rows in CSV), and updating them accordingly in the various tables
                    // unserializing breakup details
					$breakup_details = unserialize($payment_details['breakup_details']);
                    $breakup_details['seller_id'] = $payment_details['seller_id'];
                    $this->validateAndUpdatePaymentBreakup($row_data, $breakup_details);
					
					// Update Response Errors
					$this->updateResponseErrors($raw_row_data); 
					
                } //End of if 
            }// End of while
            
            $this->sendMailAfterResponseFileIsRead($row); // Inform internally about a file processed.
            
            if(file_exists($this->folder_to_move)){
               system('mv '. $file . ' ' . $this->folder_to_move);// Move file from source to destination directory 
            } //End of if
            
            //Send Mail for Response Errors
			$this->sendMailForResponseErrors();
        
        }// End of foreach
        
        exit();
    } //End of checkPayment method

    /**
     * Method which resets the $_mail object for next usage.
     * It basically clears all currently set addresses and attachments from it.
     */
    private function prepareMailerObject() {
        if ( isset($this->_mail) ) {
            $this->_mail->clearAllRecipients();
            $this->_mail->clearAttachments();
        } else {
            if ( !isset($this->_db) ) {
                throw new Exception('prepareMailerObjection function: Database link object is not created!');
            } else {

                // First get Mail login parameters etc
                $sql = "SELECT `key`, `value` FROM " . DB_PREFIX . "setting
                        WHERE `store_id` = 0
                          AND `code` LIKE 'config'
                          AND `key` IN ('config_mail_smtp_hostname',
                                        'config_mail_smtp_port',
                                        'config_mail_smtp_username',
                                        'config_mail_smtp_password'
                                       )";
                $query = $this->_db->query($sql);
                if ($query->num_rows) {
                    $mail_parameters = array_combine(array_column($query->rows, 'key'),
                                                     array_column($query->rows, 'value')
                                                    );
                    if ( !isset($mail_parameters['config_mail_smtp_hostname'],
                               $mail_parameters['config_mail_smtp_port'],
                               $mail_parameters['config_mail_smtp_username'],
                               $mail_parameters['config_mail_smtp_password']
                               ) ) {
                        throw new Exception('Insufficient Mail Parameters: ' . json_encode($mail_parameters));
                    } else {
                        $this->_mail = new PHPMailer();
                        $this->_mail->isSMTP();
                        $this->_mail->Host = $mail_parameters['config_mail_smtp_hostname'];
                        $this->_mail->Port = $mail_parameters['config_mail_smtp_port'];
                        $this->_mail->SMTPSecure = 'ssl';
                        $this->_mail->SMTPAuth = true;
                        //$this->_mail->SMTPDebug = 2;
                        $this->_mail->Username = $mail_parameters['config_mail_smtp_username'];
                        $this->_mail->Password = $mail_parameters['config_mail_smtp_password'];
                    }
                } else {
                    throw new Exception('No Mail Parameters obtained from DB!');
                }//End of else
            }//End of else
        }//End of else
    } //End of prepareMailerObject method
    
    
    /**
     * Private Method to populate rejected responses / responses with errors and issues, 
     * in the response_errors array.
     * @param $row_data array
     * @return void
     * @author Madhur
     */
    private function updateResponseErrors($row_data) {
		
		if (!empty($this->response_errors_temp)) {
			$this->response_errors[] = array(
			                                 'row_data'        => $row_data, 
			                                 'error_messages'  => $this->response_errors_temp 
			                                );
		}
		
		// Resetting the temp array
		$this->response_errors_temp = array();
		
		return true;
	} // End updateResponseErrors Method
	
	
	/**
	 * Method to prepare and return $row_data array for further usage.
	 * @param $raw_row_data array (read by fgetcsv)
	 * @return $row_data array. Mapping of the data will be as below:
	 * 
	 * <Column Title in Response CSV>      =>  <Column Index in CSV>  =>  <Key in return $row_data>
	 * 'Payment Ref'                       =>       0                 =>    'trxn_utr_internal'
	 * 'Debit Date'                        =>       1                 =>    'trxn_utr_date'
	 * 'Customer Reference'                =>       2                 =>    'payment_ref_no'
	 * 'Debit Amount'                      =>       3                 =>    'payee_amount'
	 * 'Bene. Account Number'              =>       4                 =>    'payee_account_no'
	 * 'IFSC Code'                         =>       5                 =>    'payee_ifsc_code'
	 * 'Payment Status'                    =>       6                 =>    'payment_status'
	 * 'Payment Return/Rejected Reason'    =>       7                 =>    'payment_status_message'
	 * 'Backoffice Reference'              =>       8                 =>    'trxn_utr'
	 * 
	 * It also populates 'trxn_failed' (boolean representing if transaction is in one of the FAILURE statuses, 
	 *  'trxn_done' (enum for db), 'trxn_bank' and 'trxn_response' (serialized $raw_row_data)
	 * @author Madhur
	 */
	public function prepareRowData($raw_row_data) {
		
		// Initializing return $row_data array
		$row_data = array();
		
		// 'Payment Ref' => 0 => 'trxn_utr_internal'
		$row_data['trxn_utr_internal'] = 'SIN21949' . strtoupper(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw_row_data[0])));
		
		// 'DebitDate' => 1 => 'trxn_utr_date'
		$row_data['trxn_utr_date'] = date('Y-m-d',strtotime(str_replace('/','-',trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/','',$raw_row_data[1])))));
	 
		// 'Customer Reference' => 2 => 'payment_ref_no'
		$row_data['payment_ref_no'] = trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw_row_data[2]));
		
		// 'Debit Amount' => 3 => 'payee_amount'
		$row_data['payee_amount'] = (float)str_replace(',','',trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/','',$raw_row_data[3]))); // comma in number sometimes
		
		// 'Bene. Account Number' => 4 => 'payee_account_no'
		$row_data['payee_account_no'] = strtoupper(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw_row_data[4])));
	 
		// 'IFSC Code' => 5 => 'payee_ifsc_code'
		$row_data['payee_ifsc_code'] = strtoupper(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw_row_data[5])));
		
		// 'Payment Status' => 6 => 'payment_status'
		$row_data['payment_status'] = trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw_row_data[6]));
		
		// 'Payment Return/Rejected Reason' => 7 => 'payment_status_message'
		$row_data['payment_status_message'] = trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw_row_data[7]));
		
		// 'Backoffice Reference' => 8 => 'trxn_utr'
		$row_data['trxn_utr'] = strtoupper(trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $raw_row_data[8])));
                            
        // 'trxn_failed' 
        $row_data['trxn_failed'] = $this->checkIfPaymentStatusIsFailure($row_data['payment_status']);
             
		// 'trxn_done' - In case of transaction not failed, if there is no UTR ($payment_ref), then it is still under processing stages
		$row_data['trxn_done'] = ($row_data['trxn_failed'] ? 'BANK_FAILURE' : (empty($row_data['trxn_utr']) ? 'BANK_PROCESSED' : 'BANK_SUCCESS'));
		
		// 'trxn_bank'
		$row_data['trxn_bank'] = 'stanc';

        // 'trxn_response'
		$row_data['trxn_response'] = serialize($raw_row_data);
        
        return $row_data;
	}
	
	/**
	 * Method to check if the payment_status received in the Response CSV 
	 * is in one of the Failure Statuses of Stanc H2H.
	 * @param $payment_status string
	 * @return boolean (true - Transaction is in Failed Status, else false)
	 * @author Madhur
	 */ 
	public function checkIfPaymentStatusIsFailure($payment_status) {
		return in_array($payment_status, $this->failed_statuses);
	}

} //End of Class
